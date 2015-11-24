<?php
function client($url){
        
	$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

	//注册连接成功回调
	$client->on("connect", function($cli) use ($url) {
    		$cli->send($url);
	});

	//注册数据接收回调
	$client->on("receive", function($cli, $data){
		file_put_contents("/phpstudy/www/swoole/mylog.log",$data."--null"."\r\n",FILE_APPEND);
   		echo "Received: ".$data."\n";
	});

	//注册连接失败回调
	$client->on("error", function($cli){
    		echo "Connect failed\n";
	});

	//注册连接关闭回调
	$client->on("close", function($cli){
    		echo "Connection close\n";
	});

	//发起连接
	$client->connect('127.0.0.1', 9501, 0.5);
}

?>
