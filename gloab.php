<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 11/24/15
 * Time: 8:59 PM
 */

/**
 * 用0-1表示成功失败，这样客户端方面会好操作些
 */
function pf($data=null){
    if(empty($data)){
        echo 0;
    }else{
        echo 1;
    }
}

/**
 * 执行sql语句，如果是查询action = "select"
 */
function query($sql,$action=null){
    $db=new mysqli(DBHOST,USERNAME,PASSWORD,DATABASE);
    if(mysqli_connect_error()){
        echo 'Could not connect to database.';
        exit;
    }
    $res=$db->query($sql);
    if($action == "select"){
        $res=$res->fetch_all();
    }
    $db->close();
    return $res;
}
