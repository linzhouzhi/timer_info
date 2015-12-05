<?php
require_once("config.php");
require_once("gloab.php");
require_once("data_service.php");

//判断参数是否合法
if( empty($_SERVER["REMOTE_ADDR"]) || !isset($_SERVER["REMOTE_ADDR"]) || empty($_GET['url']) || !isset($_GET['exe_time']) || !isset($_GET['type']) ){
	echo "您的请求参数不符合格式！";
	exit();
}else{
	$DService = new MySqlDB();
	$check_ip = $DService->check_ip( $_SERVER["REMOTE_ADDR"] );
	if( !empty($check_ip) ){  //检查ip是否在白名单中
		$url = "'".$_GET['url']."'";
		$exe_time = "'".$_GET['exe_time']."'";
		$type = $_GET['type'];
 		//数据库中要是没有相应的记录就直接插入，否则更新
		$check_url = $DService->check_url( $_GET['url'] );
		if( !empty($check_url) ){
			//数据库有相应的记录则更新
			$DService->update_pass_url( $exe_time,$type,$url );
		}else{
			//否则直接插入
			$DService->insert_respon_url( $exe_time,$type,$url );
		}
	}else{
		echo "您没有权限访问";
		exit();
	}
}

