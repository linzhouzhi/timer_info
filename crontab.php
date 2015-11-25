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
    for($i=0;$i<4;$i++){
        if($exe_arr[$i] != '*'){
            if($type === 1){  //如果是第一种类型，就是上面描述的那种
                if( com_exe_curr_type1($exe_arr[$i],$curr_arr[$i]) ){
                    $res = true;
                }else{
                    $res = false;
                    break;
                }
            }else{  //否则就是第二种类型了
                if( com_exe_curr_type2($exe_arr[$i],$curr_arr[$i],$finish_time,$i) ){
                    $res = true;
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
    $pos = strpos($exe,'/');
    $arr = explode('/',$exe);
    if($pos !== false){
        //判断是* 这种格式的还是 '-' 这种格式的语法
        if( $arr[0] === '*' ){
            return check_finish_time($arr[1],$finish,$time_type);
        }else{
            $range = explode('-',$arr[0]);
            $res = check_time_range($range,$curr);
            if($res){
                return check_finish_time($arr[1],$finish,$time_type);
            }
            return false;
        }

    }else{  //否则就是类型一的比对了
        if( com_exe_curr_type1($exe,$curr) ){
            return check_finish_time($arr[1],$finish,$time_type);
        }
        return false;
    }
}

/**
 * 根据最后执行的时间，还有时间类型，和时间间隔
 * 判断是否满足执行条件
 */
function check_finish_time($dist,$finish,$time_type){
    if(empty($finish)){ //如果最后执行的时间是0那么表示没执行过可以直接执行
        return true;
    }

    $exe_time = 0;
    switch($time_type){
        case 0:  //按分钟执行
            $exe_time = $finish+$dist*60;
            break;
        case 1:  //按小时执行
            $exe_time = $finish+$dist*60*60;
            break;
        case 2:  //按日执行
            $exe_time = $finish+$dist*60*60*24;
            break;
        case 3:  //按月执行
            $exe_time = $finish+$dist*60*60*24*30;
            break;
        case 4:  //按周执行
            $exe_time = $finish+$dist*60*60*24*7;
            break;
    }

    if($exe_time <= time()){  //如果当前时间比要执行的时间还大就执行
        return true;
    }else{
        return false;
    }
}


/**
 * 根据时间比对就可以判断是否需要执行
 * 比较当前的时间，可能是分 时 日 月 周 的一种
 */
function com_exe_curr_type1($exe,$curr){

    //有三种情况 1 只有一个数字的，有 '-' 这个符号的，有 ',' 这个符号的
    $pos = strpos($exe,'-');
    if($pos !== false){
        $arr = explode('-',$exe);
        return check_time_range($arr,$curr);
    }
    //一个数字的，或',' 这个符号
    $pos = strpos($exe,$curr);
    if($pos !== false){
        return true;
    }
    return false;
}

/**
 * 判断时间是否在范围之内 1-7 但是有时候是 22-9 这种表示22点到早上9点
 */
function check_time_range($range,$curr){
    if($range[0] < $range[1]){  //如果下限小于上线那么如果在中间就满足执行条件了
        if($range[0] <= $curr && $range[1] >= $curr){
            return true;
        }
        return false;
    }else{  //如果下限比上限还大比如： 22-7 那么要大于上线 或着落在0-上限 这样才可以满足
        if($range[0] <= $curr || (0 <= $curr && $curr <= $range[1])){
            return true;
        }
        return false;
    }
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
    date_default_timezone_set('PRC');
    $time = date("i-H-d-m-w");
    $arr = explode('-',$time);
    return $arr;
}


//$res = crontab_check("12-24/2 11-16 25,23 * *",1448439669);
