#!/bin/bash

BASEDIR=$(dirname $0)

cd $BASEDIR

echo $ACTION $(date) >> ./log/tasker.log

php -f ./run.php $ACTION 2>> ./log/tasker.log >> ./log/tasker.log &