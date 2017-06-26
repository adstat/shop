<?php
// Heading
$_['heading_title']					= 'Authorize.Net (SIM)';

// Text 
$_['text_payment']          = '支付管理';
$_['text_success']          = '成功：您已修改Authorize.Net（AIM）帳戶細節！';
$_['text_edit']                     = '編輯 Authorize.Net (SIM)';
$_['text_authorizenet_sim']			= '<a onclick="window.open(\'http://reseller.authorize.net/application/?id=5561103\');"><img src="view/image/payment/authorizenet.png" alt="Authorize.Net" title="Authorize.Net" style="border: 1px solid #EEEEEE;" /></a>';

// Entry
$_['entry_merchant']        = '商家 ID：';
$_['entry_key']             = '交易 Key：';
$_['entry_callback']        = 'Relay Response URL：';
$_['entry_md5']						= 'MD5 Hash Value';
$_['entry_test']            = '測試模式：';
$_['entry_total']           = '訂單合計：';
$_['entry_order_status']    = '訂單狀態：';
$_['entry_geo_zone']        = '區域群組：';
$_['entry_status']          = '狀態：';
$_['entry_sort_order']      = '排序：';

// Help
$_['help_callback']					= '請登入並設置了這個 <a href="https://secure.authorize.net" target="_blank" class="txtLink">https://secure.authorize.net</a>。';
$_['help_md5']						= 'The MD5 Hash feature enables you to authenticate that a transaction response is securely received from Authorize.Net.Please login and set this at <a href="https://secure.authorize.net" target="_blank" class="txtLink">https://secure.authorize.net</a>.(Optional)';
$_['help_total']					= '當結帳時訂單合計必須大於此數置才可使用本支付方式。';

// Error
$_['error_permission']				= 'Warning: You do not have permission to modify payment Authorize.Net (SIM)!';
$_['error_merchant']				= 'Merchant ID Required!';
$_['error_key']						= 'Transaction Key Required!';