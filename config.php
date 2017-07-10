<?php
ini_set('display_errors',0);

define('SITE_URI','http://b2b.xianshiji.com');
define('DIR_PATH','/xsj');

//define('SITE_URI','http://localhost/xsjb2b');
//define('DIR_PATH','/Users/alexsun/htdocs/xsjb2b');


define('ADMIN_VIEW', 100);
define('CS_VIEW', 20);

// HTTP
define('HTTP_SERVER', SITE_URI.'/www/admin/');
define('HTTP_CATALOG', SITE_URI.'/www/');

// HTTPS
define('HTTPS_SERVER', SITE_URI.'/www/admin/');
define('HTTPS_CATALOG', SITE_URI.'/www/');

define('DIR_APPLICATION', DIR_PATH.'/www/admin/');
define('DIR_SYSTEM', DIR_PATH.'/www/system/');
define('DIR_LANGUAGE', DIR_PATH.'/www/admin/language/');
define('DIR_TEMPLATE', DIR_PATH.'/www/admin/view/template/');
define('DIR_CONFIG', DIR_PATH.'/www/system/config/');
define('DIR_IMAGE', DIR_PATH.'/www/image/');
define('DIR_CACHE', DIR_PATH.'/www/system/cache/');
define('DIR_DOWNLOAD', DIR_PATH.'/www/system/download/');
define('DIR_UPLOAD', DIR_PATH.'/www/system/upload/');
define('DIR_LOGS', DIR_PATH.'/www/system/logs/');
define('DIR_MODIFICATION', DIR_PATH.'/www/system/modification/');
define('DIR_CATALOG', DIR_PATH.'/www/catalog/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'b2badmin');
define('DB_PASSWORD', 'dk2l3kms2me');
define('DB_DATABASE', 'xsjb2b');
define('DB_PREFIX', 'oc_');

//线上昨日
//define('DB_HOST', '139.129.213.223');
//define('DB_USER', 'liangwenquanview');
//define('DB_PW', 's923sdf33f3');
//define('DB_DATABASE', 'xsjb2b_lastday');

//Slave: Select
define('DB_DRIVER_SLAVE', 'mysql');
define('DB_HOSTNAME_SLAVE', 'localhost');
define('DB_USERNAME_SLAVE', 'b2badmin');
define('DB_PASSWORD_SLAVE', 'dk2l3kms2me');
define('DB_DATABASE_SLAVE', 'xsjb2b');

define('ALLOW_CANCEL',serialize(array(1,2)));
define('ALLOW_M_DELIVER',serialize(array(1,2,4)));
define('ALLOW_M_DELIVER_DATE',serialize(array(5,7)));
define('ALLOW_M_PAYMENT',serialize(array(1)));
define('LOCK_ORDER_STATUS',serialize(array(5,10)));

define('INVENTORY_TYPE_ORDERED',5);
define('INVENTORY_TYPE_ORDER_CANCEL',10);

define('PADDING_ORDER_STATUS',1);
define('CONFIRMED_ORDER_STATUS',2);
define('CANCELLED_ORDER_STATUS',3);

define('UNPAID_ORDER_STATUS',1);
define('PAID_ORDER_STATUS',2);
define('CREDIT_PAID_ORDER_STATUS',3);
define('REFUND_ORDER_STATUS',4);
define('CREDIT_REFUND_ORDER_STATUS',5);
define('ORDER_SORTED_STATUS',6);


//常用用户组，页面权限管理
define('ADMIN_PURCHASE_DIRECTOR_GROUP_ID', 14);
define('ADMIN_FINANCE_DIRECTOR_GROUP_ID', 26);
define('ADMIN_MARKETING_DIRECTOR_GROUP_ID', 24);
define('ADMIN_WAREHOUSE_DIRECTOR_GROUP_ID', 17);
define('ADMIN_LOGISTIC_DIRECTOR_GROUP_ID', 18);
define('ADMIN_ADMIN_GROUP_ID', 1);

define('ALLOW_PRODUCT_HIGHT_MANAGE',serialize(array(1,14,16,20,32)));


//订单补送赠品
define('ORDER_REPLENISH_VALID_HINT','历史订单状态必须为已分拣或已完成，已支付且已配送完成');
define('ORDER_REPLENISH_VALID_ORDER_STATUS', serialize(array(6,10)));//历史订单状态已分拣或已完成
define('ORDER_REPLENISH_VALID_ORDER_PAYMENT_STATUS', serialize(array(2,3)));//历史订单支付状态已支付
define('ORDER_REPLENISH_VALID_ORDER_DELIVER_STATUS', serialize(array(3)));//历史订单配送状态已配送

define('BAIDU_MAP_AK', 'TkbDdiAKKOmHBuHDMeHQk0eO');
define('BAIDU_MAP_URL', 'http://api.map.baidu.com/api?v=2.0&ak='.BAIDU_MAP_AK);

//站点设置
define('STATION_FRESH',1);
define('STATION_FAST_MOVE',2);

//BD人员管理组－销售报表访问控制
define('SALE_ACCESS_CONTROL_GROUP',serialize(array(12))); //市场推广用户分组编号12

// 获取订单的分拣序号, 若已指定order_id, 获取该订单在当天的分拣序号，
// !!!注意，按次方法订单的配送时间不可更改 !!!
//快消分拣订单分拣位编号起始值
define('FAST_MOVE_ORDER_SORTING_INDEX_START', 501);


$inventory_user_arr = array(
    'river' => 'r987sh12',
    //'randy' => 'r123123',
    'alex' => 'a123123',
    //'huguangwei' => 'hgw123',
    //'liqiang' => 'lq123',
    //'yangyang' => 'yy234',
    'leibanban' => 'Lbb8936',
    'wuguobiao' => 'wgb9988',
    //'luohuizheng' => 'lhz123',
    //'panyiping' => 'pyp123',

    'liuhe' => 'lh123',
    //'zengfujun' => 'zfj123',
    //'zhaotianxiang' => 'ztx123',
    //'bijungang' => 'bjg123',
    //'zhaoxingyu' => 'zxy123',
    //'zhaochao' => 'zc123',
    'kelly' => 'ky6789',

    //'ckczy' => 'xsj2016inv',
    //'sunyu' => 'sy123',
    //'zengfuxian' => 'zfx123',
    //'chenlei' => 'cl123',
    //'sunpudong' => 'spd123',
    //'lijiang' => 'lj123',
    //'liaoxiaoxiao' => 'lxx123',

    //'yanglingling' => 'y892361', // 杨灵灵
    //'xiangshuying' => 'sy739165', // 向术英

    "xsfj001" => "xsfj01",
    "xsfj002" => "xsfj02",
    "xsfj003" => "xsfj03",
    "xsfj004" => "xsfj04",
    "xsfj005" => "xsfj05",
    "xsfj006" => "xsfj06",
    "xsfj007" => "xsfj07",
    "xsfj008" => "xsfj08",
    "xsfj009" => "xsfj09",
    "xsfj010" => "xsfj10",
    "xsfj011" => "xsfj11",
    "xsfj012" => "xsfj12",
    "xsfj013" => "xsfj13",
    "xsfj014" => "xsfj14",
    "xsfj015" => "xsfj15",
    "xsfj016" => "xsfj16",
    "xsfj017" => "xsfj17",
    "xsfj018" => "xsfj18",
    "xsfj019" => "xsfj19",
    "xsfj020" => "xsfj20",
    
    
    "xsfj021" => "xsfj21",
    "xsfj022" => "xsfj22",
    "xsfj023" => "xsfj23",
    "xsfj024" => "xsfj24",
    "xsfj025" => "xsfj25",
    "xsfj026" => "xsfj26",
    "xsfj027" => "xsfj27",
    "xsfj028" => "xsfj28",
    "xsfj029" => "xsfj29",
    "xsfj030" => "xsfj30",
    
    
    'scfj001' => 'scfj01',
    'scfj002' => 'scfj02',
    'scfj003' => 'scfj03',
    'scfj004' => 'scfj04',
    'scfj005' => 'scfj05',
    'scfj006' => 'scfj06',
    'scfj007' => 'scfj07',
    'scfj008' => 'scfj08',
    'scfj009' => 'scfj09',
    'scfj010' => 'scfj10',
    
    
    'rhfj001' => 'rhfj01',
    'rhfj002' => 'rhfj02',
    'rhfj003' => 'rhfj03',
    'rhfj004' => 'rhfj04',
    'rhfj005' => 'rhfj05',
    'rhfj006' => 'rhfj06',
    'rhfj007' => 'rhfj07',
    'rhfj008' => 'rhfj08',
    'rhfj009' => 'rhfj09',
    'rhfj010' => 'rhfj10',
    
    
    'lwfj001' => 'lwfj01',
    'lwfj002' => 'lwfj02',
    'lwfj003' => 'lwfj03',
    'lwfj004' => 'lwfj04',
    'lwfj005' => 'lwfj05',
    'lwfj006' => 'lwfj06',
    'lwfj007' => 'lwfj07',
    'lwfj008' => 'lwfj08',
    'lwfj009' => 'lwfj09',
    'lwfj010' => 'lwfj10',
    
    //'wangyunying' => 'wangyunying' ,
    'wangshaokui' => 'skw98526', // 王少奎 仓库管理

    // 姚红超	补货人员	盘盈盘亏 。货位条码。商品报损
    'yaohongchao' => 'yhc77631',

    // 孙大玉	补货人员	盘盈盘亏 。货位条码。商品报损
    'sundayu' => 'sdy98461',

    // 和顺梅	补货人员	盘盈盘亏 。货位条码。商品报损
    'heshunmei' => 'hsm89234',

    // 汪小丽	补货人员	盘盈盘亏 。货位条码。商品报损
    'wangxiaoli' => 'wxl49591',

    // 李双	补货人员	盘盈盘亏 。货位条码。商品报损
    'lishuang' => 'ls89823',

    // 闫勉丰	补货人员	盘盈盘亏 。货位条码。商品报损
    'yanmianfeng' => 'ymf99832',

    // 周欢	收货人员	后台账号。采购入库提交权限
    'zhouhuan' => 'zH31389',

    // 韩婷婷	收货人员	后台账号。采购入库提交权限
    'hantingting' => 'Htt98231',

    'niudoudou' => 'ndd8931', //早班出库组长
    'doorman' => 'dm8899',

    'yangpeng' => 'yp8912', //白班班组长杨鹏
    'penglie' => 'pl9762' //夜班班组长彭磊

);