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
    function faile_update_status($data);
    //判断是否是白名单中的ip
    function check_ip( $ip );
    //判断要执行的url存不存在
    function check_url( $url );
    //更新call_url中的字段信息
    function update_pass_url( $exe_time,$type,$url );
    //插入一条记录到call_url
    function insert_respon_url( $exe_time,$type,$url );
    //获取执行列表详情
    function get_detail_list($domain);
}


/**
 * Mysql处理类
 */
class MySqlDB implements DataService{
    //获取要执行的url
    function get_exe_url_list(){
        $sql = "select url,exe_time,type,interval_time,retry from call_url";
        $url_list = query($sql,"select");
        return $url_list;
    }
    /**
     * 执行成功后更新状态
     * 如果type就删除该记录
     * 如果type=1就更新exe_time 还有status
     */
    function success_update_status($time,$data){
        $sql = "select type from call_url where url='".$data."'";
        $res = query($sql,"select");
        $type = $res[0]['type'];

        if($type == 1){
            $sql="update call_url set exe_time=$time where url='".$data."'";
            query($sql);
        }else{
            $sql="delete from call_url where url='".$data."'";
            query($sql);
        }

    }
    /**
     * 执行失败后更新retry +1
     */
    function faile_update_status($data){
        $sql="update call_url set retry=retry+1 where url='".$data."'";
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
    function update_pass_url( $exe_time,$type,$url ){
        $sql="update call_url set exe_time=$exe_time,status=$type where url=$url";
        $res = query($sql);
        pf($res);
    }
    //插入一条记录到call_url
    function insert_respon_url( $exe_time,$type,$url ){
        $sql="insert into call_url (url,exe_time,type) values ($url,$exe_time,$type)";
        $res = query($sql);
        pf($res);
    }

    //获取详情,执行url所在的域名
    function get_detail_list($domain=null){
        if( empty($domain) ){
            $sql = "select * from call_url";
            return query($sql,"select");
        }else{
            $sql = "select * from call_url where url like '%".$domain."%'";
            return query($sql,"select");
        }
    }

}