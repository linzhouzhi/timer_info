<?php
require_once("config.php");
require_once("gloab.php");

//判断参数是否合法
if( empty($_SERVER["REMOTE_ADDR"]) || !isset($_SERVER["REMOTE_ADDR"]) || empty($_GET['url']) || empty($_GET['exe_time']) ){
	echo "您的请求参数不符合格式！";
	exit();
}else{
	$sql = "SELECT id,IP FROM ip_list where ip='".$_SERVER["REMOTE_ADDR"]."'";
	$check_ip = query($sql,"select");
	if( !empty($check_ip) ){  //检查ip是否在白名单中
		$url = "'".$_GET['url']."'";
		$exe_time = "'".$_GET['exe_time']."'";
 		//数据库中要是没有相应的记录就直接插入，否则更新
		$sql = "select url from call_url where url='".$_GET['url']."'"; 
		$check_url = query($sql,"select");
		if( !empty($check_url) ){
			//数据库有相应的记录则更新
			$sql="update call_url set exe_time=$exe_time where url=$url";
			$res = query($sql);
			pf($res);
		}else{
			//否则直接插入
			$sql="insert into call_url (url,exe_time) values ($url,$exe_time)";
			$res = query($sql);
			pf($res);
		}
	}else{
		echo "您没有权限访问";
		exit();
	}
}

