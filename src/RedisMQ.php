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


        $redis = new \Redis();


        $redis->connect('127.0.0.1');


        $this->redis = $redis;


    }


    /**
     * 添加队列任务
     * Create by Peter
     * @param $data
     */
    function add_task($data)
    {

        $this->redis->lPush('task', $data);

    }




    function get_task(){

        $data = $this->redis->rPop('task');



        return $data;
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