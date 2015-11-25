<?php
/**
 * 处理思路
 * 定时器执行分两种类型
 * 1 根据指定的时间进行执行如
 *   1_1:   [30 21 * * *]        每晚21点30分执行
 *   1_2:   [0,30 18-23 * * *]   18到23点每30分钟执行一次
 * 这种类型我们根据时间比对就可以判断是否需要执行
 * 2 每隔一段时间执行一次
 *   2_1：  [* '*'/2 * * *]      每隔两个小时执行一次
 *   2_2：  [* 23-7/3 * * *]     23点到凌晨7点每隔三个小时执行一次
 * 这种类型我们根据上一次执行完的时间跟当前时间戳进行比对即可
 */

/**
 * 根据执行时间，和最后执行时间，判断是否需要执行
 */
function crontab_check($exe_time,$finish_time=0){
    //获取执行任务的类型
    $type = crontab_exe_type($exe_time);
    //根据空格分割时间格式后 返回分时日月周的数组
    $exe_arr = explode(' ',$exe_time);
    $curr_arr = crontab_get_time_arr();

    $res = false;
    for($i=0;$i<5;$i++){
        if($exe_arr[$i] != '*'){
            if($type === 1){  //如果是第一种类型，就是上面描述的那种
                if(com_exe_curr_type1($exe_arr[$i],$curr_arr[$i])){
                    $res = true;
                }else{
                    $res = false;
                    break;
                }
            }else{  //否则就是第二种类型了
                if( com_exe_curr_type2($exe_arr[$i],$curr_arr[$i],$finish_time,$i) ){

                }else{
                    $res = false;
                    break;
                }
            }

        }
    }
    return $res;
}

/**
 * 根据上一次执行完的时间跟当前时间戳进行比对即可
 * $finish 是上次执行完成的时间
 * $time_type 是时间类型，0 1 2 3 4 分别表示分 时 日 月 周
 */
function com_exe_curr_type2($exe,$curr,$finish,$time_type){
    
}

/**
 * 根据时间比对就可以判断是否需要执行
 * 比较当前的时间，可能是分 时 日 月 周 的一种
 */
function com_exe_curr_type1($exe,$curr){
    echo $exe."--".$curr."*";
    //有三种情况 1 只有一个数字的，有 '-' 这个符号的，有 ',' 这个符号的
    $pos = strpos($exe,'-');
    if($pos !== false){
        $arr = explode('-',$exe);
        if($arr[0] < $arr[1]){  //如果下限小于上线那么如果在中间就满足执行条件了
            if($arr[0] <= $curr && $arr[1] >= $curr){
                return true;
            }
            return false;
        }else{  //如果下限比上限还大比如： 22-7 那么要大于上线 或着落在0-上限 这样才可以满足
            if($arr[0] <= $curr || (0 <= $curr && $curr <= $arr[1])){
                return true;
            }
            return false;
        }
    }
    //一个数字的，或',' 这个符号
    $pos = strpos($exe,$curr);
    if($pos !== false){
        return true;
    }
    return false;
}

/**
 * 根据执行时间格式获取执行时间类型
 * 返回参数
 * 1 根据指定的时间进行执行如30 21 * * *，这样表示每晚21点30分执行
 * 2 每隔一段时间执行一次如* * /2 * * *，这样表示两个小时执行
 */
function crontab_exe_type($exe_time){
    $pos = strpos($exe_time,'/');
    if($pos){
        return 2;
    }else{
        return 1;
    }
}

/**
 * 根据当前时间返回 分时日月周的数组
 */
function crontab_get_time_arr(){
    //这个是因为当前获取的是格林威治时间，与北京时间相差8小时
    date_default_timezone_set(PRC);
    $time = date("i-H-d-m-w");
    $arr = explode('-',$time);
    return $arr;
}


$res = crontab_check("* 11-15 25 * *","");
var_dump($res);
exit();