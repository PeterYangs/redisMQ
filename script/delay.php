<?php
/**
 * 常驻运行此脚本
 */


//永久连接redis
ini_set('default_socket_timeout', -1);

date_default_timezone_set('PRC');

$redis = new \Redis();


$redis->connect('127.0.0.1', 6379, 10);


while (true) {


    $time = time();

    $arr = $redis->zRangeByScore('delay', '-inf', $time);



    if (!$arr) {
        sleep(1);

        continue;

    }


    $redis->zRemRangeByScore('delay', '-inf', $time);


    foreach ($arr as $key => $value) {
        $redis->lPush('task', explode('_', $value)[0]);
    }


}













