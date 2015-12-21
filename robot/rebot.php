<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 12/15/15
 * Time: 7:51 PM
 */

for( $i=0;$i<10;$i++ ){

}
$user = new User();
//$user->register();
$user->randStep(5);


class User{
    private $urls = array(
        0 => "url0",
        1 => "url1",
        2 => "url2",
        3 => "url3",
        4 => "url4",
        5 => "url5",
        6 => "url6"
    );

    /**
     * 注册步骤
     */
    public function register(){
        $step = array(1,3,4,5);
        foreach( $step as $i ){
            echo $this->urls[$i]."\n\r";
        }
    }

    /**
     * 随即步骤
     */
    public function randStep( $depth ){
        $size = count($this->urls);
        for($i=0;$i<$depth;$i++){
            $d = rand(0,$size-1);
            echo $this->urls[$d]."\n\r";

        }
    }
}

/*
require_once dirname(__FILE__) . "/CurlAsync.php";
$http = new CurlAsync;
$http->test1("http://localhost/");
$http->test2("http://www.baidu.com");
var_dump($http->test1());
var_dump($http->test2());
*/