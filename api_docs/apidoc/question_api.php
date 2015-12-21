<pre>
/**
 * 疑问建议列表
 */
function api_question_list( $where, $page, $prepage = QUESTION_PAGESIZE, $orderby = 'id', $px = 'desc' )
/**
 * 条件查询
 * @param type $data
 * @return type
 */
function api_question_data( $data, $orderby = 'id ASC' )
/**
 * 查询数量
 * @param type $where
 */
function api_question_num( $where )
/**
 * 根据ID查询
 * @param type $id
 * @return type
 */
function api_question_byid( $id )
/**
 * 更新
 * @param type $id
 * @param type $data
 */
function api_question_update( $id, $data )
/**
 * 删除s
 * @param type $id
 */
function api_question_del( $id )
/**
 * @param type $data
 * @return type
 */
function api_question_add( $data )
/**
 * 条件筛选
 * @param type $data
 * @return string
 */
</pre>
function _question_where( $data )
