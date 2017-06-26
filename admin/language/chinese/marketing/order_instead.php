<?php
/**
 * $Author: http://www.opencartchina.com 
**/
// Heading
$_['heading_title']       = '代下单';

// Text
$_['text_success']        = '成功: 已修导入!';
$_['text_order_false']     = '失败: 导入订单失败!';
$_['text_product_false']   = '失败: 导入订单产品失败!';
$_['text_false']          = '失败: 导入失败!';
$_['text_list']           = '代下单列表';
$_['text_add']            = '添加订单';
$_['text_edit']           = '编辑订单';
$_['text_percent']        = '百分比';
$_['text_amount']         = '固定金额';
$_['text_forbidden']       ='指定用户';
$_['text_allow']          ='未指定用户';
$_['text_form']           = '编辑代下单';

// Column
$_['column_name']         = '折扣券名称';
$_['column_code']         = '折扣券代码';
$_['column_discount']     = '折扣';
$_['column_date_start']   = '开始日期';
$_['column_date_end']     = '结束日期';
$_['column_status']       = '状态';
$_['column_order_id']     = '订单号';
$_['column_customer']     = '会员';
$_['column_amount']       = '合计';
$_['column_date_added']   = '添加日期';
$_['column_action']       = '操作';
$_['column_time']         ='使用次数';
$_['column_request']     ='客户可否索取';
$_['column_newcustomer'] ='新客户是否可用';
$_['column_customer_id'] ='客户ID';
$_['column_customer_forbidden'] ='指定用户';
$_['column_customer_new'] ='允许新客户使用';
$_['column_customer_request'] ='允许客户索取';
$_['column_station_id']   = '平台';

// Entry
$_['entry_name']          = '折扣券名称';
$_['entry_code']          = '折扣券代码';
$_['entry_type']          = '类型';
$_['entry_discount']      = '折扣';
$_['entry_logged']        = '会员登录';
$_['entry_shipping']      = '免费配送';
$_['entry_total']         = '最低订单金额';
$_['entry_category']      = '分类';
$_['entry_product']       = '商品';
$_['entry_date_start']    = '开始日期';
$_['entry_date_end']      = '结束日期';
$_['entry_uses_total']    = '每张折扣券可以使用次数';
$_['entry_uses_customer'] = '每个会员可以使用次数';
$_['entry_status']        = '状态';
$_['entry_times']         ='优惠券使用次数';
$_['entry_request']       ='是否可以索取';
$_['entry_newcustomer']  ='新客户是否可以使用';
$_['entry_customerlimited']  ='是否指定用户';
$_['entry_station'] = '平台';


// Help
$_['help_code']           = '会员输入该代码以得到折扣。';
$_['help_type']           = '百分比或固定金额。';
$_['help_logged']         = '会员必须登录后使用该折扣券。';
$_['help_total']          = '该折扣券有效所需最低订单金额！';
$_['help_category']       = '选择所选分类下的所有商品。';
$_['help_product']        = '选择可以应用该折扣券的特定商品。如果没有选择任何商品，则适用于购物车内的所有商品。';
$_['help_uses_total']     = '所有会员可使用折扣券的最大次数，留空即无限制。';
$_['help_uses_customer']  = '每个会员可使用折扣券的最大次数，空白既无限制。';

// Error
$_['error_permission']    = '警告: 无权限带下单！';
$_['error_exists']        = '警告: 折扣券代码已经存在！';
$_['error_order']          = '必须输入有效的客户ID';
$_['error_product']          = '必须选择商品！';
$_['error_date']          = '优惠券必须有开始时间或者结束时间必填';
$_['error_customer']      = '用户必填';
$_['error_alert']         ='全场禁用已经选择有效项，不可以修改！';