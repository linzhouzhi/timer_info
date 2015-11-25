<?php
require_once("config.php");
require_once("gloab.php");

$serv = new swoole_server("127.0.0.1", 9501);

//设置异步任务的工作进程数量
$serv->set(array('task_worker_num' => TASK_NUM));

$serv->on('receive', function($serv, $fd, $from_id, $data) {
    //$serv->send($fd, "Server: ".$data);
    //投递异步任务
    $task_id = $serv->task($data);
    echo "Dispath AsyncTask: id=$task_id\n";
});

//处理异步任务
$serv->on('task', function ($serv, $task_id, $from_id, $data) {
    $i = 0;	
    do{
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

        if($output != '0'){
		    //设置该url状态已经处理完成
            $sql="update call_url set status=1 where url='".$data."'";
            query($sql);
        	break;
        }

        $i++;
        if($i==5){
            $sql="update call_url set status=-1 where url='".$data."'";
            query($sql);
        }
        sleep(2);
    }while($i <= 5);

    //返回任务执行的结果
    $serv->finish("$data -> OK");
});

//处理异步任务的结果
$serv->on('finish', function ($serv, $task_id, $data) {
    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
});

$serv->start();
?>
~      
