<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 23.10.2017
 * Time: 11:00
 */

namespace reinvently\ondemand\core\vendor\tasker\daemon;


class DbHelper
{

    /** @var \mysqli */
    protected $db;

    /**
     * @return \mysqli
     */
    public function getDb()
    {
        return $this->db;
    }

    public function connect()
    {
        $this->db = mysqli_connect(
            CONFIG_MYSQL_HOST,
            CONFIG_MYSQL_LOGIN,
            CONFIG_MYSQL_PASS,
            CONFIG_MYSQL_DB
        );

        if (mysqli_connect_errno()) {
            throw new \Error("Connect failed: %s\n", mysqli_connect_error());
        }

        if (!mysqli_set_charset($this->db, "utf8")) {
            throw new \Error("Error loading character set utf8: %s\n", mysqli_error($this->db));
        }
    }

    public function close()
    {
        mysqli_close($this->db);
    }

    /**
     * @param $query
     * @return boolean|array
     */
    public function query($query)
    {
//        Tasker::getInstance()->echoLog($query);

        $arr = array();
        $result = mysqli_query($this->db, $query);
        if ($result === false) {
            Tasker::getInstance()->echoLog('Error: ' . mysqli_error($this->db) . "\n");
        } elseif ($result instanceof \mysqli_result) {

            while ($row = mysqli_fetch_assoc($result)) {
                $arr[] = $row;
            }

//            Tasker::getInstance()->echoLog($arr);

            mysqli_free_result($result);
            return $arr;
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getLastInsertId()
    {
        return mysqli_insert_id($this->getDb());
    }


}