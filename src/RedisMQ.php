<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5 0005
 * Time: 09:29
 */

namespace peteryang\src;

class RedisMQ
{

    protected $redis = null;

    protected $func = null;


    function __construct()
    {

        //永久连接redis
        ini_set('default_socket_timeout', -1);

        $redis = new \Redis();


        $redis->connect('127.0.0.1',6379,10);


        $this->redis = $redis;


    }


    /**
     * 添加队列任务
     * Create by Peter
     * @param $data   string 数据
     * @param int $delay int 执行任务时间
     */
    function add_task($data,$delay=0)
    {



        if($delay){


            $this->zAdd($delay,$data);

        }else{

            $this->redis->lPush('task', $data);

        }


    }


    private function zAdd($delay,$data){

        $re=$this->redis->zAdd('delay',$delay,$data."_".mt_rand(100000,999999));


        if(!$re) $this->zAdd($delay,$data);

    }


    /**
     * 获取任务
     * Create by Peter
     * @return array
     */
    function get_task(){




     return   $this->redis->brPop('task',0)[1];




    }



//    /**
//     * 开启任务进程
//     * Create by Peter
//     * @param $task_num int 进程数
//     * @param $work_code string 业务代码
//     */
//    function task_start($task_num, $work_code)
//    {
//
//        $this->run_code($work_code);
//
//
////        exec('nohup php '.__DIR__.DIRECTORY_SEPARATOR."task_start.php &");
//        exec("sh ".__DIR__.DIRECTORY_SEPARATOR."mq.sh taskstart 3");
//
//
//
//
//    }
//
//
//    function run_code($func)
//    {
//
//
//        $this->func = $func;
//
//    }
//
//
//    /**
//     * 执行业务逻辑
//     * Create by Peter
//     */
//    private function execute($data)
//    {
//
//
//        call_user_func_array($this->func, [$data]);
//
//
//    }
//
//
//    /**
//     * 获取任务
//     * Create by Peter
//     */
//    function task_run()
//    {
//
//        if(!$this->is_cli()) throw new \Exception('请在cli下运行');
//
//
//        while (true) {
//
//
//            $data = $this->redis->rPop('task');
//
//
//            $this->execute($data);
//
//        }
//
//
//    }
//
//
//    function is_cli()
//    {
//        return preg_match("/cli/i", php_sapi_name()) ? true : false;
//    }


}