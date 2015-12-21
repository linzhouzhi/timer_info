<?php
/**
 * 通用user_id查询
 */
function api_user_get( $id )
{
	$db = get_db();
	$re = $db->get_row( 'select * from total_user where user_id = ' . $id );
	if ( !$re )
	{
		show_excp( 'USER_INFO NOT EXIST ', $error_no );
	}
	return $re;
}

/**
 * 更新用户
 */
function api_user_update( $user_id, $update_data, $manul_arr = array( ) )
{
	$db = get_db();
	$re = $db->update( 'total_user', $update_data, 'user_id=' . $user_id, $manul_arr );
	return $re;
}

/**
 * 添加记录
 */
function api_user_add( $data )
{
	$db = get_db();
	$db->insert( 'total_user', $data );
	$user_id = $db->last_insert_id();
	//同时添加一行统计数据
	$db->insert( 'user_count', array( 'user_id' => $user_id ) );
	return $user_id;
}

/**
 * 获取用户统计数据
 * @param int $user_id 用户ID数据
 */
function api_user_count_get( $user_id )
{
	$cachekey = 'user_count_'. $user_id;
	if ( !cache_has( $cachekey, $res ) )
	{
		$db = get_db();
		$res = $db->get_row( 'select * from user_count where user_id='. $user_id );
		if ( !$res )
		{
			show_excp( '不存在用户统计数据', USER_COUNT_NOT_EXIST );
		}
		cache_set( $cachekey, $res );
	}
	return $res;
}

/**
 * 更新用户统计数据
 * @param int $user_id 用户ID
 * @param array $up_arr 数组
 */
function api_user_count_update( $user_id, $up_arr )
{
	$update_arr = array();
	foreach ( $up_arr as $key => $value )
	{
		$update_arr[ $key ] = $key .' + '. $value;
	}
	$db = get_db();
	$db->update( 'user_count', array(), 'user_id='. $user_id, $update_arr );
	cache_remove( 'user_count_'. $user_id );
}

/*
 * 扣除相关的款项。
 * $add_accumulate_sign 是否加上累计消费
 */

function api_user_money_minus( $user_id, $money_minus, $add_accumulate_sign = 0, $user_type = null )
{
	$ad_user_info = api_user_get( $user_id );
	$user_money_now = $ad_user_info[ 'money' ];
	if ( $money_minus > $user_money_now )
	{
		show_excp( '用户余额不足', USER_MONEY_NOT_ENOUGH );
	}
	$db = get_db();
	if ( $user_type == ACCOUNT_TYPE_ZMT )
	{
		$sql = 'update total_user set money_cash_zmt = money_cash_zmt - ' . $money_minus . ', money_cash_zmt_frozen = money_cash_zmt_frozen + ' . $money_minus . ' where user_id = ' . $user_id;
		if ( $add_accumulate_sign > 0 )
		{
			$sql = 'update total_user set money_cash_zmt = money_cash_zmt - ' . $money_minus . ', money_cash_zmt_frozen = money_cash_zmt_frozen + ' . $money_minus . ', accumulate_consume = accumulate_consume + ' . $money_minus . ' where user_id = ' . $user_id;
		}
	}
	else
	{
		$sql = 'update total_user set money = money - ' . $money_minus . ', money_frozen = money_frozen + ' . $money_minus . ' where user_id = ' . $user_id;
		if ( $add_accumulate_sign > 0 )
		{
			$sql = 'update total_user set money = money - ' . $money_minus . ', money_frozen = money_frozen + ' . $money_minus . ', accumulate_consume = accumulate_consume + ' . $money_minus . ' where user_id = ' . $user_id;
		}
	}
	$db->query( $sql, true );
}

/*
 * 扣除相关的提现款项。
 */

function api_user_money_cash_minus( $user_id, $money_cash_minus )
{
	if ( $money_cash_minus <= 0 )
	{
		show_excp( '提现金额必须大于零', USER_APPLY_MONEY_CASH_ERROR );
	}
	$ad_user_info = api_user_get( $user_id );
	$user_money_cash_now = $ad_user_info[ 'money_cash' ];
	if ( $money_cash_minus > $user_money_cash_now )
	{
		show_excp( '用户余额不足', USER_MONEY_NOT_ENOUGH );
	}
	$db = get_db();
	$sql = 'update total_user set money_cash = money_cash - ' . $money_cash_minus . ', money_cash_frozen = money_cash_frozen + ' . $money_cash_minus . ' where user_id = ' . $user_id;
	$db->query( $sql, true );
}

/*
 * 后台通过用户提现：减少冻结现金，增加累计返现 $operate_res = 2 提现通过 $operate_res = 3 提现不通过拒绝
 */

function api_user_withdraw_money( $user_id, $money_withdraw, $operate_res )
{
	if ( $money_withdraw <= 0 )
	{
		show_excp( '提现金额必须大于零', USER_APPLY_MONEY_CASH_ERROR );
	}
	$ad_user_info = api_user_get( $user_id );
	$user_money_cash_frozen_now = $ad_user_info[ 'money_cash_frozen' ];
	if ( $money_withdraw > $user_money_cash_frozen_now )
	{
		show_excp( '用户冻结余额不足', USER_MONEY_NOT_ENOUGH );
	}
	$db = get_db();
	if ( $operate_res == PAY_WITHDRAW_APPLY_2 )
	{
		$sql = 'update total_user set money_cash_frozen = money_cash_frozen - ' . $money_withdraw . ', accumulate_rebate = accumulate_rebate + ' . $money_withdraw . ' where user_id = ' . $user_id;
	}
	elseif ( $operate_res == PAY_WITHDRAW_APPLY_3 )
	{
		$sql = 'update total_user set money_cash_frozen = money_cash_frozen - ' . $money_withdraw . ', money_cash = money_cash + ' . $money_withdraw . ' where user_id = ' . $user_id;
	}
	return $db->query( $sql, true );
}

/*
 * 后台通过转帐申请：通过，不通过 $operate_res = 2 转帐通过 $operate_res = 3 转帐不通过拒绝
 * $user_id 上级， $user_id_sub 下级， $money_transfer 转帐数字
 */

function api_user_transfer_money( $user_id, $user_id_sub, $money_transfer, $operate_res )
{
	$db = get_db();
	if ( $operate_res == PAY_TRANSFER_APPLY_2 )
	{
		$sql = 'update total_user set money_frozen = money_frozen - ' . $money_transfer . ', accumulate_transfer_out = accumulate_transfer_out + ' . $money_transfer . ' where user_id = ' . $user_id;
		$db->query( $sql, true );
		$sql = 'update total_user set money = money + ' . $money_transfer . ', accumulate_transfer_in = accumulate_transfer_in + ' . $money_transfer . ' where user_id = ' . $user_id_sub;
		$db->query( $sql, true );
	}
	elseif ( $operate_res == PAY_TRANSFER_APPLY_3 )
	{
		$sql = 'update total_user set money_frozen = money_frozen - ' . $money_transfer . ', money = money + ' . $money_transfer . ' where user_id = ' . $user_id;
		$db->query( $sql, true );
	}
}

/*
 * 用户充值,添加余额，同时增加累计充值
 */

function api_user_recharge_addmoney( $user_id, $money_add )
{
	$ad_user_info = api_user_get( $user_id );
	if ( empty( $ad_user_info ) )
	{
		show_excp( '无此用户', USER_NOT_EXIST );
	}
	$db = get_db();
	$sql = 'update total_user set money = money + ' . $money_add . ', accumulate_recharge = accumulate_recharge + ' . $money_add . ' where user_id = ' . $user_id;
	$db->query( $sql, true );
}

/*
 * 用户的派单审核不通过：返款给用户余额
 */

function api_user_money_verify_back( $user_id, $money_add )
{
	$ad_user_info = api_user_get( $user_id );
	if ( empty( $ad_user_info ) )
	{
		show_excp( '无此用户', USER_NOT_EXIST );
	}
	$db = get_db();
	$sql = 'update total_user set money = money + ' . $money_add . ', money_frozen = money_frozen - ' . $money_add . ' where user_id = ' . $user_id;
	$db->query( $sql, true );
}

/*
 * 下级账号充值，返佣金给广告主：增加广告主的余额？
 */

function api_user_agent_commission( $user_id, $commission_money_add )
{
	$ad_user_info = api_user_get( $user_id );
	if ( empty( $ad_user_info ) )
	{
		show_excp( '无此用户', USER_NOT_EXIST );
	}
	$db = get_db();
	$sql = 'update total_user set money_cash = money_cash + ' . $commission_money_add . ' where user_id = ' . $user_id;
	$db->query( $sql, true );
}

/**
 * 按用户名获取信息
 * @param string $uname 自媒体用户名
 */
function api_total_user_get_by_name( $uname )
{
	$db = get_db();
	$res = $db->get_row( 'select * from total_user where user_name="' . $uname . '"' );
	if ( !$res )
	{
		show_excp( '帐号不存在', ERROR_NO_UNAME );
	}
	return $res;
}

/**
 * 按用户名获取信息
 * @param string $uname 自媒体用户名
 */
function api_total_user_get_by_name_like( $uname )
{
	$db = get_db();
	$res = $db->get_all( 'select * from total_user where user_name like "%' . $uname . '%"' );
	if ( !$res )
	{
		show_excp( '帐号不存在', ERROR_NO_UNAME );
	}
	return $res;
}

/**
 * 根据条件查询用户信息
 */
function api_total_user_count( $where )
{
	$db = get_db();
	$where_str = _total_user_where( $where );
	$sql = 'select count(*) from total_user' . $where_str;
	return $db->get_one( $sql );
}

function api_total_user_page_count( $where )
{
	$count = api_total_user_count( $where );
	return ceil( $count / USER_PAGE );
}

/**
 * 获取total_user表的一条数据
 */
function api_total_user_one( $where )
{
	$db = get_db();
	$where = _total_user_where( $where );
	$sql = 'select * from total_user' . $where;
	$res = $db->get_row( $sql );
	return $res;
}

/**
 * 根据手机号获取用户信息
 */
function api_user_get_by_mobile( $mobile )
{
	$db = get_db();
	$res = $db->get_row( 'select * from total_user where user_mobile="'. $mobile .'"' );
	if ( !$res )
	{
		show_error( '没有手机号为'. $mobile .'的用户', USER_INFO_NOT_EXIST );
	}
	return $res;
}

/**
 * 根据邮箱获取用户信息
 */
function api_user_get_by_email( $email )
{
	$db = get_db();
	$res = $db->get_row( 'select * from total_user where user_email="'. $email .'" and email_ischeck=1' );
	if ( !$res )
	{
		show_error( '邮箱不存在或者没有通过验证', USER_INFO_NOT_EXIST );
	}
	return $res;
}

/**
 * 用户列表获取
 */
function api_total_user_list( $where = array( ), $page = 1, $perpage = USER_PAGE, $order_by = ' user_id ', $px = ' asc ' )
{
	$db = get_db();
	//开始条数
	$beg = ( $page - 1 ) * $perpage;
	$sql = 'select * from total_user' . _total_user_where( $where ) . ' order by ' . $order_by . $px . ' limit ' . $beg . ',' . $perpage;
	$zmt_user_info = $db->get_all( $sql );
	return $zmt_user_info;
}

/*
 * 获取符合条件的总共多少条
 */

function api_total_user_total_num( $where )
{
	$db = get_db();
	$sql = 'select count(*) from total_user' . _total_user_where( $where );
	return $db->get_one( $sql );
}

/*
 * 对广告主某字段进行 +1 -1
 */

function api_user_update_field( $where, $field, $num = 1 )
{
	$db = get_db();
	$where_str = _total_user_where( $where );
	if ( $num > 0 )
	{
		$sql = 'update total_user set ' . $field . ' = ' . $field . ' + ' . $num . $where_str;
	}
	elseif ( $num < 0 )
	{
		$sql = 'update total_user set ' . $field . ' = ' . $field . $num . $where_str;
	}
	return $db->query( $sql, true );
}

/**
 * 返回条件
 */
function _total_user_where( $where, $return_where = true )
{
	if ( empty( $where ) )
	{
		return null;
	}
	$str_arr = array( );
	if ( isset( $where[ 'user_iszmt' ] ) )
	{
		$str_arr[ ] = 'user_iszmt = ' . $where[ 'user_iszmt' ];
	}

	//自媒体ID
	if ( isset( $where[ 'user_id' ] ) )
	{
		$str_arr[ ] = 'user_id =' . "'" . $where[ 'user_id' ] . "'";
	}
	//uid多个查询
	if ( isset( $where[ 'user_id_in' ] ) )
	{
		$str_arr[ ] = 'user_id in (' . $where[ 'user_id_in' ] . ')';
	}
	//账号名称
	if ( isset( $where[ 'user_name' ] ) )
	{
		$str_arr[ ] = 'user_name = ' . "'" . $where[ 'user_name' ] . "'";
	}
	if ( isset( $where[ 'username_like' ] ) )
	{
		$str_arr[ ] = ' user_name like "%' . $where[ 'username_like' ] . '%"';
	}
	if ( isset( $where[ 'nick_name_like' ] ) )
	{
		$str_arr[ ] = ' nick_name like "%' . $where[ 'nick_name_like' ] . '%"';
	}
	if ( isset( $where[ 'agent_id_ad' ] ) )
	{
		$str_arr[ ] = 'agent_id_ad = ' . "'" . $where[ 'agent_id_ad' ] . "'";
	}
	if ( isset( $where[ 'agent_id_zmt' ] ) )
	{
		$str_arr[ ] = 'agent_id_zmt = ' . "'" . $where[ 'agent_id_zmt' ] . "'";
	}

	//用户密码
	if ( isset( $where[ 'user_pwd' ] ) )
	{
		$str_arr[ ] = 'user_pwd = ' . "'" . $where[ 'user_pwd' ] . "'";
	}
	if ( isset( $where[ 'user_mobile' ] ) )
	{
		$str_arr[ ] = 'user_mobile =' . "'" . $where[ 'user_mobile' ] . "'";
	}

	//用户邮箱
	if ( isset( $where[ 'user_email' ] ) )
	{
		$str_arr[ ] = 'user_email=' . "'" . $where[ 'user_email' ] . "'";
	}


	if ( isset( $where[ 'user_ad_type' ] ) )
	{
		$str_arr[ ] = 'user_ad_type =' . $where[ 'user_ad_type' ];
	}
	if ( isset( $where[ 'user_isad' ] ) )
	{
		$str_arr[ ] = 'user_isad =' . $where[ 'user_isad' ];
	}
	if ( isset( $where[ 'user_ad_ban' ] ) )
	{
		$str_arr[ ] = 'user_ad_ban =' . $where[ 'user_ad_ban' ];
	}

	if ( isset( $where[ 'user_ad_name_like' ] ) )
	{
		$str_arr[ ] = ' user_ad_name like "%' . $where[ 'user_ad_name_like' ] . '%"';
	}

	if ( isset( $where[ 'user_reg_time_start' ] ) )
	{
		$str_arr[ ] = 'user_reg_time >= ' . $where[ 'user_reg_time_start' ];
	}
	if ( isset( $where[ 'user_reg_time_end' ] ) )
	{
		$str_arr[ ] = 'user_reg_time <= ' . $where[ 'user_reg_time_end' ];
	}

	if ( isset( $where[ 'is_agent' ] ) )
	{
		$str_arr[ ] = 'is_agent=' . $where[ 'is_agent' ];
	}

	$str = null;
	if ( $return_where )
	{
		$str = " where ";
	}
	return $str . join( ' and ', $str_arr );
}

/*
 * 扣除相关的款项。
 */

function api_zmt_user_money_minus( $uid, $money_cash_minus )
{
	if ( $money_cash_minus <= 0 )
	{
		show_excp( '提现金额必须大于零', USER_APPLY_MONEY_CASH_ERROR );
	}
	$zmt_user_info = api_user_get( $uid );
	$user_money_cash_now = $zmt_user_info[ 'money_cash' ];
	if ( $money_cash_minus > $user_money_cash_now )
	{
		show_excp( '用户余额不足', USER_MONEY_NOT_ENOUGH );
	}
	$db = get_db();
	$sql = 'update zmt_user set money_cash = money_cash - ' . $money_cash_minus . ', money_cash_frozen = money_cash_frozen + ' . $money_cash_minus . ' where zmt_uid = ' . $uid;
	$db->query( $sql, true );
}

