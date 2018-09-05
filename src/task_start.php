<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5 0005
 * Time: 14:04
 */


include __DIR__.DIRECTORY_SEPARATOR."RedisMQ.php";

$redis=new \peteryang\src\RedisMQ();


$redis->task_run();