<?php
date_default_timezone_set('Asia/Shanghai');
// XSJ API for OC 2.0.1.1
// Alex@XSJ 2015-03-27
// GET OS
//define('IS_WIN',(strstr(PHP_OS, 'WIN')||strstr(PHP_OS, 'Darwin')) ? 1 : 0 );
$regOrgin = array(
    '1'=>'xsj_dev_mode_origin_01', //WeChat
    '2'=>'xsj_dev_mode_origin_02', //WeChat Fast Moving Consumer Goods
    '3'=>'xsj_dev_mode_origin_03', //WeChat Firm[CRM] - xsjfirm_32l3km
    '7'=>'xsj_dev_mode_origin_07', //POS
    '8'=>'xsj_dev_mode_origin_08' //Barcode Scaner
);

//AUTHKEY
define('AUTHKEY',serialize($regOrgin));

// DIR
define('DIR_ROOT', dirname(__FILE__).'/');
define('DIR_SYSTEM', DIR_ROOT.'system/');
define('DIR_MODULE', DIR_ROOT.'module/');
define('DIR_DATABASE', DIR_ROOT.'system/database/');
define('DIR_LOGS', DIR_ROOT.'logs/');
define('DOMAIN','@xianshiji.com');

define('DB_PREFIX', 'oc_');
//Master: Insert, Update
define('DB_DRIVER', 'mysql');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '123456');
define('DB_DATABASE', 'xsjb2b');

//Slave: Select
define('DB_DRIVER_SLAVE', 'mysql');
define('DB_HOSTNAME_SLAVE', '127.0.0.1');
define('DB_USERNAME_SLAVE', 'root');
define('DB_PASSWORD_SLAVE', '123456');
define('DB_DATABASE_SLAVE', 'xsjb2b');

////DB: Last Day
define('DB_LASTDAY_DRIVER', 'mysql');
//define('DB_LASTDAY_HOSTNAME', '10.26.174.99'); //内网地址
define('DB_LASTDAY_HOSTNAME', '139.129.213.223'); //外网地址
define('DB_LASTDAY_USERNAME', 'apilastday');
define('DB_LASTDAY_PASSWORD', 'dlks3m09#m2H3');
define('DB_LASTDAY_DATABASE', 'xsjb2b_lastday');

// REDIS Cache
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', '6380'); //Redis for B2B
define('REDIS_CACHE_TIME', '1800');
define('REDIS_CACHE_TIMEOUT', false);

define('REDIS_CART_EXPIRE',608400); //7*24*60*60, 7 days
define('REDIS_CART_KEY_PREFIX','b2b');
define('REDIS_CART_KEY_NAME','cart'); //customer:[customer_id[:[station_id]:cart
define('REDIS_CART_ITEM_QTY_LIMIT',999); //Max Cart Item Qty, default 99
define('REDIS_CART_ITEM_LIMIT',499); //Max Cart Item, default 30
define('REDIS_PRODUCT_KEY_PREFIX','product');
define('REDIS_LIST_KEY_PREFIX','list');
define('REDIS_STOCK_KEY_PREFIX','stock');
define('REDIS_STOCK_CACHE_TIME', 600);
define('REDIS_ORDER_KEY_PREFIX','order');
define('REDIS_ORDER_LIST_KEY_PREFIX','orderList');
define('REDIS_ORDER_STATUS_KEY_PREFIX','orderStatus');
define('REDIS_ORDER_DETAIL_LIST_KEY_PREFIX','orderDetailList');
define('REDIS_ORDER_DETAIL_INFO_KEY_PREFIX','orderDetailInfo');
define('REDIS_ORDER_DETAIL_INFO_OF_PRODUCT','product');
define('REDIS_ORDER_DETAIL_INFO_OF_TOTAL_DETAIL','totalDetail');
define('REDIS_MY_ACCOUNT_KEY_PREFIX','myAccount');
define('REDIS_MY_ACCOUNT_CACHE_TIME', 3600);
define('REDIS_CUSTOMER_COUPON_HISTORY_PREFIX', 'customerCouponHistory');
define('REDIS_CUSTOMER_COUPON_PREFIX', 'customerCoupon');
define('REDIS_COUPON_CACHE_TIME', 1800);


define('GENERATE_KEY', 'xsj_customer_id_key');

define('PADDING_ORDER_STATUS',1);
define('CONFIRMED_ORDER_STATUS',2);
define('CANCELLED_ORDER_STATUS',3);

define('UNPAID_ORDER_STATUS',1);
define('PAID_ORDER_STATUS',2);
define('CREDIT_PAID_ORDER_STATUS',3);
define('REFUND_ORDER_STATUS',4);
define('CREDIT_REFUND_ORDER_STATUS',5);

//array('api method'=>array(inventory_type_id, operation))
//define('INVENTORY_TYPE_OP',serialize(array('inventoryInit'=>array(1,1), 'inventoryIn'=>array(2,1), 'inventoryBreakage'=>array(3,-1), 'inventoryOut'=>array(4,-1), 'stationRetail'=>array(5,-1), 'inventoryLoss'=>array(6,-1), 'inventoryProfit'=>array(7,1), 'inventoryReturn'=>array(8,1), 'inventoryOverdue'=>array(9,-1))));
define('INVENTORY_TYPE_OP',serialize(array('inventoryInit'=>array(1,1), 'inventoryIn'=>array(2,1), 'inventoryBreakage'=>array(3,-1), 'inventoryOut'=>array(4,-1), 'stationRetail'=>array(5,-1), 'inventoryLoss'=>array(6,-1), 'inventoryProfit'=>array(7,1), 'inventoryReturn'=>array(8,1), 'inventoryOverdue'=>array(9,-1), 'inventoryCancelOrder'=>array(10,1),'inventoryInProduct'=>array(11,1),'inventoryOrderIn'=>array(12,-1),'inventoryOutProduct'=>array(13,-1),'inventoryCheck'=>array(14,1),'inventoryChange'=>array(15,-1),'inventoryAdjust'=>array(16,1),'inventoryVegCheck'=>array(17,1),'inventoryInProductPreset'=>array(18,1),'inventoryMissing'=>array(19,-1),'inventoryMissingRecover'=>array(20,1),'inventoryReturnCancel'=>array(21,-1))));
define('INVENTORY_TYPE_AUTO_SYNC', serialize(array(8,16)));
//退货入库,8,inventoryReturn
//暂不处理  //采购入库,11,inventoryInProduct 其他方法中已处理
//暂不处理  //商品报损,13,inventoryOutProduct
//暂不处理  //转促销品,15,inventoryChange
//库存调整,16,inventoryAdjust

define('INVENTORY_TYPE_ORDERED',5); //订单扣减库存
define('INVENTORY_TYPE_ORDER_CANCEL',10); //取消订单增加库存

define('INVENTORY_TYPE_INIT',1); //后台管理界面重置可售库存
define('INVENTORY_TYPE_PRESET',18); //预设库存类型, 后台管理界面调整预设可售库存
define('INVENTORY_TYPE_STOCK_IN',11); //仓库入库类型, 商品入库后，最近一次设置可售库存后新增的“预售库存类型”的数值将被重置，新增一条冲销记录，再已实际入库值作为可售库存

define('STATION_LOGIN_YTX_SMS_TEMPLATE',27657); //Template
define('STATION_LOGIN_YTX_SMS_CODE_LIFE',15); //5 minutes

define('STATION_INVENTORY_FUNC_LIMIT', serialize(array(4,5,6,8,9))); //Station inventory function limitation

define('ISP_TEMPLATE_ID_REG', '27657');
define('IMGPATH', 'http://b2b.xianshiji.com/www/image/');
//define('OSSIMGPATH', 'http://xianshiji.oss-cn-hangzhou.aliyuncs.com/image/');
define('OSSIMGPATH', 'http://b2b.xianshiji.com/www/image/');

define('ICEBOX_PRODUCT', '5000');

define('SEARCH_PROMO_PROD', 'PROMO');
define('SEARCH_PRODUCT_BRIEF_INFO','PRODUCT_BRIEF_INFO');
define('SEARCH_ACTIVITY_PRODUCT','ACTIVITY_PRODUCT');
define('SEARCH_PRODUCT_PRICE','PRODUCT_PRICE');

define('API_RETURN_SUCCESS','SUCCESS');
define('API_RETURN_ERROR','ERROR');

define('POINTS_TO_PAYMENT_RATE',100);
define('POINTS_TO_PAYMENT_RULE_ID',6);
define('POINTS_TO_PAYMENT_CANCEL_RULE_ID',7);

define('FREE_FRAME',5);
define('NON_FREE_FRAME_CUSTOMER_ID_START',9350);
?>
