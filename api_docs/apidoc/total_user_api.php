<pre>
/**
 * 通用user_id查询
 */
function api_user_get( $id )
/**
 * 更新用户
 */
function api_user_update( $user_id, $update_data, $manul_arr = array( ) )
/**
 * 添加记录
 */
function api_user_add( $data )
/**
 * 获取用户统计数据
 * @param int $user_id 用户ID数据
 */
function api_user_count_get( $user_id )
/**
 * 更新用户统计数据
 * @param int $user_id 用户ID
 * @param array $up_arr 数组
 */
function api_user_count_update( $user_id, $up_arr )
/*
 * 扣除相关的款项。
 * $add_accumulate_sign 是否加上累计消费
 */
function api_user_money_minus( $user_id, $money_minus, $add_accumulate_sign = 0, $user_type = null )
/*
 * 扣除相关的提现款项。
 */
function api_user_money_cash_minus( $user_id, $money_cash_minus )
/*
 * 后台通过用户提现：减少冻结现金，增加累计返现 $operate_res = 2 提现通过 $operate_res = 3 提现不通过拒绝
 */
function api_user_withdraw_money( $user_id, $money_withdraw, $operate_res )
/*
 * 后台通过转帐申请：通过，不通过 $operate_res = 2 转帐通过 $operate_res = 3 转帐不通过拒绝
 * $user_id 上级， $user_id_sub 下级， $money_transfer 转帐数字
 */
function api_user_transfer_money( $user_id, $user_id_sub, $money_transfer, $operate_res )
/*
 * 用户充值,添加余额，同时增加累计充值
 */
function api_user_recharge_addmoney( $user_id, $money_add )
/*
 * 用户的派单审核不通过：返款给用户余额
 */
function api_user_money_verify_back( $user_id, $money_add )
/*
 * 下级账号充值，返佣金给广告主：增加广告主的余额？
 */
function api_user_agent_commission( $user_id, $commission_money_add )
/**
 * 按用户名获取信息
 * @param string $uname 自媒体用户名
 */
function api_total_user_get_by_name( $uname )
/**
 * 按用户名获取信息
 * @param string $uname 自媒体用户名
 */
function api_total_user_get_by_name_like( $uname )
/**
 * 根据条件查询用户信息
 */
function api_total_user_count( $where )
function api_total_user_page_count( $where )
/**
 * 获取total_user表的一条数据
 */
function api_total_user_one( $where )
/**
 * 根据手机号获取用户信息
 */
function api_user_get_by_mobile( $mobile )
/**
 * 根据邮箱获取用户信息
 */
function api_user_get_by_email( $email )
/**
 * 用户列表获取
 */
function api_total_user_list( $where = array( ), $page = 1, $perpage = USER_PAGE, $order_by = ' user_id ', $px = ' asc ' )
/*
 * 获取符合条件的总共多少条
 */
function api_total_user_total_num( $where )
/*
 * 对广告主某字段进行 +1 -1
 */
function api_user_update_field( $where, $field, $num = 1 )
/**
 * 返回条件
 */
function _total_user_where( $where, $return_where = true )
/*
 * 扣除相关的款项。
 */
</pre>
function api_zmt_user_money_minus( $uid, $money_cash_minus )
