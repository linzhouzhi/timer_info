<?php
require_once("gloab.php");

/**
 * Interface DataService
 * 数据处理接口
 */
interface DataService{
	//获取要执行的url
    function get_exe_url_list();
    //执行成功后更新状态
    function success_update_status($time,$data);
    //执行失败后更新状态
    function faile_update_status($time,$data);
    //判断是否是白名单中的ip
    function check_ip( $ip );
    //判断要执行的url存不存在
    function check_url( $url );
    //更新call_url中的字段信息
    function update_pass_url( $exe_time,$url );
    //插入一条记录到call_url
    function insert_respon_url( $exe_time,$url );
}


/**
 * Mysql处理类
 */
class MySqlDB implements DataService{
    //获取要执行的url
    function get_exe_url_list(){
        $sql = "select url,exe_time,finish_time,type from call_url where status=0 or type=1";
        $url_list = query($sql,"select");
        return $url_list;
    }
    //执行成功后更新状态
    function success_update_status($time,$data){
        $sql="update call_url set status=1,finish_time=$time where url='".$data."'";
        query($sql);
    }
    //执行失败后更新状态
    function faile_update_status($time,$data){
        $sql="update call_url set status=-1,finish_time=$time where url='".$data."'";
        query($sql);
    }
    //判断是否是白名单中的ip
    function check_ip( $ip ){
        $sql = "SELECT id,IP FROM ip_list where ip='".$ip."'";
        $check_ip = query($sql,"select");
        return $check_ip;
    }
    //判断要执行的url存不存在
    function check_url($url){
        $sql = "select url from call_url where url='".$url."'";
        $check_url = query($sql,"select");
        return $check_url;
    }
    //更新call_url中的字段信息
    function update_pass_url( $exe_time,$url ){
        $sql="update call_url set exe_time=$exe_time,status=0 where url=$url";
        $res = query($sql);
        pf($res);
    }
    //插入一条记录到call_url
    function insert_respon_url( $exe_time,$url ){
        $sql="insert into call_url (url,exe_time) values ($url,$exe_time)";
        $res = query($sql);
        pf($res);
    }

}