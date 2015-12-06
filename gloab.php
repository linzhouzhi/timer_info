<?php
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
    if(mysqli_connect_error()){ //断线重连接
        $db=new mysqli(DBHOST,USERNAME,PASSWORD,DATABASE);
        if(mysqli_connect_error()){
            echo 'Could not connect to database.';
            exit;
        }
    }
    $res=$db->query($sql);

    $data = null;
    if($action == "select"){
        #关联数组方式获取结果集
        while($row = $res->fetch_assoc())
        {
            $data[] = $row;
        }
    }
    if($action == "add"){
        $data = $db->insert_id;
    }
    $db->close();
    return $data;
}
