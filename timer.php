<?php
require_once("client.php");
require_once("config.php");
require_once("gloab.php");


swoole_timer_tick(2000, function ($timer_id) {
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
    $sql = "select url,exe_time from call_url where status=0";
    $url_list = query($sql,"select");
    $arr = array();
    if($url_list){
        $i=0;
        foreach($url_list as $url){
            if(check_time($url[1])){
                $arr[$i] = $url[0];
                $i++;
            }
        }
    }
    return $arr;
}


/**
 * 检查时间是否达到执行的标准
 */
function check_time($exe_time){
    $low_time = time()-30;
    $hight_time = time()+30;
    if( $exe_time > $low_time && $exe_time < $hight_time ){
        return true;
    }else{
        return false;
    }
}