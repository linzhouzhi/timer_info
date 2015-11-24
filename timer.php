<?php
require_once("client.php");
swoole_timer_tick(2000, function ($timer_id) {
    $base_url = "http://192.168.1.130/swoole/testurl/";
    $url_list = array('url000.php','url001.php','url002.php');
    foreach($url_list as $url){
    	client($base_url.$url);
    }	
   //file_put_contents("/phpstudy/www/swoole/mylog.log",time()."\r\n",FILE_APPEND);
});

?>
