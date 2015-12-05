<?php
require_once("../config.php");
require_once("../gloab.php");
require_once("../data_service.php");


if( !empty($_POST) ){
    $key = $_POST['data'];
    $id=0;
    foreach($key as $k => $v){
        $id = $k;
    }

    if($_POST['action'] == "remove"){
        foreach($key as $k => $v){
            $id = $k;
            $sql="delete from call_url where id=".$id;
            query($sql);
        }
    }else if( $_POST['action'] == "edit" ){
        $data = $_POST['data'][$id];
        $url = "'".$data['url']."'";
        $exe_time = strtotime($data['exe_time']);
        $sql="update call_url set url=$url,exe_time=$exe_time,type={$data['type']},interval_time={$data['interval_time']} where id=".$id;
        query($sql);
    }else{
        $data = $_POST['data'][$id];
        $url = "'".$data['url']."'";
        $exe_time = strtotime($data['exe_time']);
        $sql="insert into call_url(url,exe_time,type,interval_time) values($url,$exe_time,{$data['type']},{$data['interval_time']})";
        query($sql);
    }
}


$DService = new MySqlDB();
$domain = "fenyong.lzz.work.newe.com";
$detail = $DService->get_detail_list();
for($i=0;$i<count($detail);$i++){
    $detail[$i]['exe_time'] = date('Y-m-d H:i:s',$detail[$i]['exe_time']);
}
$detail = json_encode($detail);
echo '{"data":'.$detail.'}';

