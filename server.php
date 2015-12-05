<?php
require_once("config.php");
require_once("gloab.php");
require_once("data_service.php");

$serv = new swoole_server("127.0.0.1", 9501);

//设置异步任务的工作进程数量
$serv->set(array('task_worker_num' => TASK_NUM));

$serv->on('receive', function($serv, $fd, $from_id, $data) {
    //$serv->send($fd, "Server: ".$data);
    //投递异步任务
    $task_id = $serv->task($data);
    $loginfo = "Dispath AsyncTask: id=$task_id";
    file_put_contents(LOG_DIR."exeurl.log",$loginfo."\r\n",FILE_APPEND);
});

//处理异步任务
$serv->on('task', function ($serv, $task_id, $from_id, $data) {
    //初始化
    $ch = curl_init();
    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL,$data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch ,CURLOPT_HEADER, 0);
    //执行并获取HTML文档内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);

    $time = time();
    $DService = new MySqlDB();
    if($output != '0'){
        //设置该url状态已经处理完成
        $DService->success_update_status($time,$data);
    }else{
        $DService->faile_update_status($data);
    }

    //返回任务执行的结果
    $serv->finish("$data -> OK");
});

//处理异步任务的结果
$serv->on('finish', function ($serv, $task_id, $data) {
    $loginfo = "AsyncTask[$task_id] Finish: $data".PHP_EOL;
    file_put_contents(LOG_DIR."exeurl.log",$loginfo."\r\n",FILE_APPEND);
});

$serv->start();
?>
~      
