<?php
require_once("client.php");
require_once("config.php");
require_once("gloab.php");
require_once("data_service.php");

/**
 * 每两秒轮询一次
 */

swoole_timer_tick(LOOP_TIME, function ($timer_id) {
    //获取需要执行的url
    $url_list = get_exe_url();
    if($url_list){
        foreach($url_list as $url){
            client($url);
        }
    }
   //file_put_contents("/phpstudy/www/swoole/mylog.log",time()."\r\n",FILE_APPEND);

});

/**
 * 获取需要定时执行的url
 */
function get_exe_url(){
    //status为0表示没有执行过的，-1表示重试了也失败的，1表示成功的
    $DService = new MySqlDB();
    $url_list = $DService->get_exe_url_list();

    $arr = array();
    if($url_list){
        $i=0;
        foreach($url_list as $row){
            if( $row['retry'] < RETRY_NUM ){  //如果重试次数大于系统设置的次数就不符合条件
                if( check_time($row['exe_time'],$row['interval_time'],$row['retry'],$row['type']) ){
                    $arr[$i] = $row['url'];
                    $i++;
                }
            }
        }
    }

    return $arr;
}


/**
 * 检查时间是否达到执行的标准
 * 检查url是否满足执行条件，执行时间exe_time加上间隔时间 interval_time要，小于等于当前时间表示要执行的
 * retry是重试的次数
 */
function check_time($exe_time,$interval_time,$retry,$type){

    if( $type==0 && $retry!=0 ){  //如果是一次性执行的那么重试时间就是重试的次数*重试的时间间隔
        $sum_time = $exe_time+($retry*SLEEP_TIME);
    }else{
        $sum_time = $exe_time+$interval_time;
    }

    $time = time();
    if( $time >= $sum_time ){
        return true;
    }
    return false;

}
