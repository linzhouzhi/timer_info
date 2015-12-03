<?php
require_once("client.php");
require_once("config.php");
require_once("gloab.php");
require_once("crontab.php");
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
        foreach($url_list as $url){
            if(check_time($url[1],$url[2],$url[3])){
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
function check_time($exe_time,$finish_time,$type){
    if( empty($exe_time) ){
		return  true;
    }	
    if( empty($type) ){
        $low_time = time()-RANGE_TIME;
        $hight_time = time()+RANGE_TIME;
        if( $exe_time > $low_time && $exe_time < $hight_time ){
            return true;
        }else{
            return false;
        }
    }else{
        return crontab_check($exe_time,$finish_time);
    }

}
