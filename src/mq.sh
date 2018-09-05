#!/bin/bash





##开启beanstalkd服务端
#if [ ${1} == "start" ]
#then
#
#       nohup beanstalkd -l 127.0.0.1 -p 11301 &
##关闭beanstalkd服务端
#elif [ ${1} == "stop" ]
#then
#
#     kill -9 $(pidof beanstalkd)

#开启任务进程，可以开启多个
if [ ${1} == "taskstart" ]
then
        if [ $2 -gt 1 ]
        then

            for((i=0;i<${2};i++))
            do

#            nohup /opt/lampp/bin/php /www/admin/think beanstalkd_task >/www/admin/public/log.log  &

#                nohup php ../../think beanstalkd_task >/mnt/hgfs/USA/USA/public/log.log  &

                nohup php ./task_start.php &



            done

        else


#            nohup /opt/lampp/bin/php /www/admin/think beanstalkd_task >/www/admin/public/log.log  &

            nohup php ../../think beanstalkd_task >/mnt/hgfs/USA/USA/public/log.log  &
        fi

#关闭所有任务进程
elif [ ${1} == "taskstop" ]
then

    ps aux | grep "beanstalkd_task" |grep -v grep| cut -c 9-15 | xargs kill -9

#查看队列状态
elif [ ${1} == "status" ]
then

    php test.php

#帮助
elif [ ${1} == "help" ]
then

    echo "start:启动队列服务端"
    echo "stop:关闭队列服务端"
    echo "taskstart:开启消费者进程，后面可以跟一个数字参数，表示开启几个任务进程。例：./beanstalkd.sh taskstart 2,表示开启两个任务进程"
    echo "taskstop:关闭所有任务进程"
#    echo "status:查看当前队列状态"


fi

exit 0