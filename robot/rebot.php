<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 12/15/15
 * Time: 7:51 PM
 */

for( $i=0;$i<10;$i++ ){
    $user = new User();
    $user->register();
    $user->login();
}

class User{
    private $urls = array(
        1 => "url1",
        2 => "url2",
        3 => "url3"
    );

    public function register(){
        $step = array(1,3,4,5);
        foreach( $step as $i ){
            curl($this->urls[ $step[$i] ]);
        }
    }

    public function randStep( $depth ){
        $size = count($this->urls);
        for($i=0;$i<$depth;$i++){
            $d = rand(0,$size);
            curl( $this->urls[$d] );

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