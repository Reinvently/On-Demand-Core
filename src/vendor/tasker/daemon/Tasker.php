<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 19.10.2017
 * Time: 13:45
 */

namespace reinvently\ondemand\core\vendor\tasker\daemon;

/**
 * Class Tasker
 * @package reinvently\ondemand\core\vendor\tasker
 */
class Tasker
{
    use SingletonTrait;

    const STATUS_DISABLE = 0;
    const STATUS_READY_TO_RUN = 1;
    const STATUS_WORKING_ON_TASK = 2;
    const STATUS_STUCK = 3;

    const TASK_STATUS_WAITING = 0;
    const TASK_STATUS_READY_TO_RUN = 1;
    const TASK_STATUS_LAUNCHED = 2;
    const TASK_STATUS_FINISHED_SUCCESSFUL = 3;
    const TASK_STATUS_FAILED = 4;
    const TASK_STATUS_CANCELED = 5;


    /** @var DbHelper */
    public $db;

    /** @var int */
    protected $id;
    /** @var int */
    protected $status;
    /** @var int */
    protected $timeStart;
    /** @var int */
    protected $timeLastActivity;
    /** @var int */
    protected $processId;
    /** @var int */
    protected $currentTaskId;
    /** @var int */
    protected $currentCyclicTaskId;

    /** @var string */
    protected $log;

    /**
     *
     */
    public function init()
    {
        session_start();

        $this->db = new DbHelper();
        $this->db->connect();

    }

    /**
     *
     */
    public function start()
    {
        $this->init();
        Params::init();
    }

    /**
     *
     */
    public function stop()
    {
        $this->db->close();
    }

    /**
     * @throws \Error
     */
    public function setValuesStart()
    {

        $time = time();
        $status = self::STATUS_READY_TO_RUN;
        $processId = getmypid();
        $this->processId = $processId;

        $this->db->query(<<<SQL
INSERT INTO `tasker` (`status`, `timeStart`, `timeLastActivity`, `processId`) VALUES ($status, $time, $time, $processId)
SQL
        );

        $this->id = $this->db->getLastInsertId();

        if (empty($this->id)) {
            throw new \Error('empty tasker id');
        }

    }

    /**
     *
     */
    public function setValuesStop()
    {

        $status = static::STATUS_STUCK;
        $this->db->query("UPDATE `tasker` SET status = $status WHERE id < $this->id");
        $this->db->query("DELETE FROM `tasker` WHERE id = $this->id");
    }

    /**
     * @return bool
     * @throws \Error
     */
    public function checkExpiredTasker()
    {
        $this->updateTimeLastActivity();

        $arr = $this->db->query('SELECT MAX(id) from `tasker`');

        if (empty($arr)) {
            throw new \Error('Tasker not found');
        }

        $row = array_pop($arr);
        $maxId = array_pop($row);

        $arr = $this->db->query("SELECT `status` from `tasker` WHERE id = $this->id");

        if (empty($arr)) {
            throw new \Error('Tasker not found');
        }

        $row = array_pop($arr);
        $status = array_pop($row);

        $numbersOfTasker = Params::get(Params::PARAMS_NUMBERS_OF_TASKER);

        return $this->id <= ($maxId - $numbersOfTasker) || $status == static::STATUS_DISABLE;
    }

    /**
     * @param $taskId
     * @throws \Error
     */
    public function runCyclicTask($taskId)
    {
        $this->db->query('LOCK TABLES tasker_cyclic_task WRITE');

        $time = time();

        $array = $this->db->query("SELECT * FROM tasker_cyclic_task WHERE id = $taskId");

        if (empty($array)) {
            throw new \Error('Cyclic runTask not found');
        }

        $task = array_pop($array);

        if (
            (int)$task['status'] !== self::TASK_STATUS_READY_TO_RUN
            || (int)$task['timeNextRun'] > $time
        ) {
            $this->db->query('UNLOCK TABLES');
            return;
        }


        $status = self::TASK_STATUS_LAUNCHED;
        $timeNextRun = $time + $task['timeInterval'];

        $this->db->query(<<<SQL
UPDATE tasker_cyclic_task SET
status = $status,
timeLastRun = $time,
timeLastStatus = $time,
timeNextRun = $timeNextRun
WHERE id = $taskId
SQL
);

        $this->db->query('UNLOCK TABLES');

        $status = static::STATUS_WORKING_ON_TASK;

        $this->db->query(<<<SQL
UPDATE tasker SET
status = $status,
timeLastActivity = $time,
currentTaskId = null,
currentCyclicTaskId = $taskId
WHERE id = $this->id
SQL
        );

        $this->setLog('');
        $this->runProcess($task['cmd'], $task['data']);


        $time = time();

        $status = self::TASK_STATUS_READY_TO_RUN;
        $this->db->query(<<<SQL
UPDATE tasker_cyclic_task SET
status = $status,
timeLastStatus = $time,
`log` = '{$this->getLog()}'
WHERE id = $taskId
SQL
);

        $status = static::STATUS_READY_TO_RUN;
        $this->db->query(<<<SQL
UPDATE tasker SET
status = $status,
timeLastActivity = $time,
currentTaskId = null,
currentCyclicTaskId = null
WHERE id = $this->id
SQL
        );

    }

    /**
     * @return bool
     */
    public function runAllCyclicTasks()
    {

        $time = time();
        $status = self::TASK_STATUS_READY_TO_RUN;

        $arr = $this->db->query(<<<SQL
SELECT id FROM tasker_cyclic_task
WHERE timeNextRun < $time
AND status = $status
SQL
);

        if (empty($arr)) {
            return false;
        }

        foreach($arr as $row) {
            $this->runCyclicTask($row['id']);
            if ($this->checkExpiredTasker()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $taskId
     * @throws \Error
     */
    public function runTask($taskId)
    {
        $this->db->query('LOCK TABLES tasker_task WRITE');

        $time = time();

        $array = $this->db->query("SELECT * FROM tasker_task WHERE id = $taskId");

        if (empty($array)) {
            throw new \Error('Task not found');
        }

        $task = array_pop($array);

        if (
            (int)$task['status'] !== self::TASK_STATUS_READY_TO_RUN
            || (int)$task['timeNextRun'] > $time
        ) {
            $this->db->query('UNLOCK TABLES');
            return;
        }


        $status = self::TASK_STATUS_LAUNCHED;

        $this->db->query(<<<SQL
UPDATE tasker_task SET
status = $status,
timeLastStatus = $time
WHERE id = $taskId
SQL
        );

        $this->db->query('UNLOCK TABLES');

        $status = static::STATUS_WORKING_ON_TASK;
        $this->db->query(<<<SQL
UPDATE tasker SET
status = $status,
timeLastActivity = $time,
currentTaskId = $taskId,
currentCyclicTaskId = null
WHERE id = $this->id
SQL
        );

        $this->setLog('');
        $response = $this->runProcess($task['cmd'], $task['data']);

        $time = time();

        $status = $response ? self::TASK_STATUS_FINISHED_SUCCESSFUL : self::TASK_STATUS_FAILED;
        $this->db->query(<<<SQL
UPDATE tasker_task SET
status = $status,
timeLastStatus = $time,
`log` = '{$this->getLog()}'
WHERE id = $taskId
SQL
        );

        $status = static::STATUS_READY_TO_RUN;
        $this->db->query(<<<SQL
UPDATE tasker SET
status = $status,
timeLastActivity = $time,
currentTaskId = null,
currentCyclicTaskId = null
WHERE id = $this->id
SQL
        );

    }

    /**
     * @return bool
     */
    public function runAllTasks()
    {
        $time = time();
        $status = self::TASK_STATUS_READY_TO_RUN;

        $arr = $this->db->query(<<<SQL
SELECT id FROM tasker_task
WHERE timeNextRun < $time
AND status = $status
SQL
        );

        if (empty($arr)) {
            return false;
        }

        foreach($arr as $row) {
            $this->runTask($row['id']);
            if ($this->checkExpiredTasker()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @param string $log
     */
    public function addLog($log)
    {
        $this->log .= "\n" . $log;
    }

    /**
     * @return string
     */
    public function getLog()
    {
        return mysqli_real_escape_string($this->db->getDb(), mb_substr($this->log, 0, 0xfffe, 'ASCII'));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * @param $data
     */
    public function echoLog($data)
    {
        echo "\n" . date('c') . "\n";
        echo 'id: ' . $this->getId() . "\n";
        echo 'processId: ' . $this->getProcessId() . "\n";
        $backtraceArray = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        if (key_exists(1, $backtraceArray)) {
            echo $backtraceArray[1]['file'] . ':' . $backtraceArray[1]['line'] . "\n";
        }
        var_dump($data);

    }

    /**
     *
     */
    public function sleep()
    {
        sleep(Params::get(Params::PARAMS_TASKER_SLEEP_INTERVAL));
    }

    /**
     *
     */
    protected function updateTimeLastActivity()
    {
        $time = time();
        $this->db->query("UPDATE `tasker` SET timeLastActivity = $time WHERE id = $this->id");
    }

    /**
     * @param $cmd
     * @param $params
     * @return bool
     */
    protected function runProcess($cmd, $params)
    {
        $pipes = [];
        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $this->addLog('cmd: ' . $cmd);
        $process = proc_open(
            $cmd,
            $descriptorSpec,
            $pipes,
            PROCESS_CWD
        );

        $return_value = -1;
        $stderr = '';

        if (is_resource($process)) {
            fwrite($pipes[0], $params);
            $this->addLog('stdin: ' . $params);
            fclose($pipes[0]);

            $this->addLog('stdout: ' . stream_get_contents($pipes[1]));
            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);
            $this->addLog('stderr: ' . $stderr);
            fclose($pipes[2]);

            $return_value = proc_close($process);
        }
        $this->addLog('Return value: ' . $return_value);

        return $return_value != -1 && !$stderr;
    }
}

