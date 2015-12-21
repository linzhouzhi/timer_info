<?php

/**
 * 疑问建议列表
 */
function api_question_list( $where, $page, $prepage = QUESTION_PAGESIZE, $orderby = 'id', $px = 'desc' )
{
	$db = get_db();
	$sql = 'select * from user_question' . _question_where( $where );
	if ( $orderby )
	{
		$sql.=' order by ' . $orderby . ' ' . $px;
	}
	if ( $page >= 1 )
	{
		$sql.=' limit ' . ($page - 1) * $prepage . ' , ' . $prepage;
	}
	return $db->get_all( $sql );
}

/**
 * 条件查询
 * @param type $data
 * @return type
 */
function api_question_data( $data, $orderby = 'id ASC' )
{
	$db = get_db();
	return $db->get_all( 'select * from user_question' . _question_where( $data ) . ' order by ' . $orderby );
}

/**
 * 查询数量
 * @param type $where
 */
function api_question_num( $where )
{
	$db = get_db();
	return $db->get_one( 'select count(*) from user_question' . _question_where( $where ) );
}

/**
 * 根据ID查询
 * @param type $id
 * @return type
 */
function api_question_byid( $id )
{
	$db = get_db();
	return $db->get_row( 'select * from user_question where id=' . $id );
}

/**
 * 更新
 * @param type $id
 * @param type $data
 */
function api_question_update( $id, $data )
{
	$db = get_db();
	return $db->update( 'user_question', $data, ' id=' . $id );
}

/**
 * 删除s
 * @param type $id
 */
function api_question_del( $id )
{
	$db = get_db();
	return $db->delete( 'user_question', '  id =' . $id );
}

/**
 * @param type $data
 * @return type
 */
function api_question_add( $data )
{
	$db = get_db();
	$db->insert( 'user_question', $data );
	return $db->LAST_INSERT_ID();
}

/**
 * 条件筛选
 * @param type $data
 * @return string
 */
function _question_where( $data )
{
	if ( empty( $data ) )
	{
		return '';
	}
	$where_arr = array( );
	if ( isset( $data[ 'id' ] ) )
	{
		$where_arr[ ] = ' id =' . $data[ 'id' ];
	}
	if ( isset( $data[ 'topic_id' ] ) )
	{
		$where_arr[ ] = ' topic_id =' . $data[ 'topic_id' ];
	}
	if ( isset( $data[ 'user_id' ] ) )
	{
		$where_arr[ ] = ' user_id =' . $data[ 'user_id' ];
	}
	if ( isset( $data[ 'question_type_in' ] ) )
	{
		$where_arr[ ] = ' question_type in (' . $data[ 'question_type_in' ] . ')';
	}

	if ( isset( $data[ 'is_topic' ] ) )
	{
		$where_arr[ ] = ' is_topic =' . $data[ 'is_topic' ];
	}
	if ( empty( $where_arr ) )
	{
		return '';
	}
	else
	{
		return ' where ' . join( ' and ', $where_arr );
	}
}
