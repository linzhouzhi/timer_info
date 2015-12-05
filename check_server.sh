#!/bin/bash
#配置信息
PHP_DIR='/phpstudy/server/php/bin/'
WORK_DIR='/phpstudy/www/swoole/'
LOG_DIR='/phpstudy/www/swoole/log/'

#计算server.php 和 timer.php 的进程数
server_count=`ps -fe | grep "server.php" | grep -v "grep" | grep -v "master" | wc -l`
timer_count=`ps -fe | grep "timer.php" | grep -v "grep" | grep -v "master" | wc -l`
#如果小于1那么就要重启
if [ $server_count -lt 1 ]||[ $timer_count -lt 1 ]; then
    ps -eaf | grep "server.php" | grep -v "grep" | awk '{print $2}' | xargs kill -9
    ps -eaf | grep "timer.php" | grep -v "grep" | awk '{print $2}' | xargs kill -9
    #休眠 2秒，杀死进程是需要时间的
    sleep 2
    #进程资源不加以限制
    ulimit -c unlimited
    #顺序启动
    $PHP_DIR'php'   $WORK_DIR'server.php' &
    $PHP_DIR'php'   $WORK_DIR'timer.php' &
    echo "重启啦："$(/bin/date +%Y-%m-%d_%H:%M:%S) >$LOG_DIR'restart.log'
fi

