<?php
require_once("../config.php");
require_once("../gloab.php");
require_once("../data_service.php");

$domain = "fenyong.lzz.work.newe.com";


/**
 * 输出表格中的数据
 */
function table_datas(){
    $DService = new MySqlDB();
    $detail = $DService->get_detail_list();
    for($i=0;$i<count($detail);$i++){
        date_default_timezone_set('PRC');
        $detail[$i]['exe_time'] = date('Y-m-d H:i:s',$detail[$i]['exe_time']);
        if($detail[$i]['type'] === 0) {
            $detail[$i]['type'] = "执行一次";
        }else{
            $detail[$i]['type'] = "长久执行";
        }
    }
    return $detail;
}

/**
 * json方式输出数据
 */
/*
function echo_json_data(){
    $DService = new MySqlDB();
    $detail = $DService->get_detail_list();
    for($i=0;$i<count($detail);$i++){
        date_default_timezone_set('PRC');
        $detail[$i]['exe_time'] = date('Y-m-d H:i:s',$detail[$i]['exe_time']);
    }
    $detail = json_encode($detail);
    echo '{"data":'.$detail.'}';
}

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
        echo_json_data();
    }else if( $_POST['action'] == "edit" ){
        $data = $_POST['data'][$id];
        $url = "'".$data['url']."'";
        date_default_timezone_set('PRC');
	    $exe_time = strtotime($data['exe_time']);
        $sql="update call_url set url=$url,exe_time=$exe_time,type={$data['type']},interval_time={$data['interval_time']} where id=".$id;
        query($sql);
        echo_json_data();
    }else{
        $data = $_POST['data'][$id];
        $url = "'".$data['url']."'";
	    date_default_timezone_set('PRC');
        $exe_time = strtotime($data['exe_time']);
        $sql="insert into call_url(url,exe_time,type,interval_time) values($url,$exe_time,{$data['type']},{$data['interval_time']})";
        $id = query($sql,"add");
        echo '{"data":[{"id":"'.$id.'","url":"'.$data['url'].'","exe_time":"'.$exe_time.'","type":"'.$data['type'].'","interval_time":"'.$data['interval_time'].'","retry":"0"}]}';
    }
}else{
    echo_json_data();
}
*/



