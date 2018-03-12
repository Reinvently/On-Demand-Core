#!/usr/bin/php -q
<?php

use reinvently\ondemand\core\vendor\tasker\daemon\Tasker;

if (isset($argv[1]) && file_exists($argv[1])) {
    include $argv[1];
} else {
    include 'config.php';
}

include './daemon/SingletonTrait.php';
include './daemon/DbHelper.php';
include './daemon/Params.php';
include './daemon/Tasker.php';

$tasker = Tasker::getInstance();
$tasker->start();
$tasker->setValuesStart();

while (!$tasker->checkExpiredTasker()) {
    $workedCyclicTask = $tasker->runAllCyclicTasks();
    $workedTask = $tasker->runAllTasks();
    if (!$workedCyclicTask && !$workedTask) {
        $tasker->sleep();
    }
}

$tasker->setValuesStop();
$tasker->stop();
