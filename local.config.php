<?php
// XSJ API for OC 2.0.1.1
// Alex@XSJ 2015-03-27
// GET OS
//define('IS_WIN',(strstr(PHP_OS, 'WIN')||strstr(PHP_OS, 'Darwin')) ? 1 : 0 );

//AUTHKEY
//md5('xsj@20150416pudong');
define('AUTHKEY',serialize(array('1'=>'fcf23447dkm316bf519d2juh5e47md23')));
//Origin = 1 WeChat

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
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'apimaster');
define('DB_PASSWORD', 'demodemo');
define('DB_DATABASE', 'xsj');

//Slave: Select
define('DB_DRIVER_SLAVE', 'mysql');
define('DB_HOSTNAME_SLAVE', 'localhost');
define('DB_USERNAME_SLAVE', 'apislave');
define('DB_PASSWORD_SLAVE', 'demodemo');
define('DB_DATABASE_SLAVE', 'xsj');

// REDIS Cache
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', '6379');
define('REDIS_CACHE_TIME', '1800');
define('REDIS_CACHE_TIMEOUT', false);

define('REDIS_CART_EXPIRE',608400); //7*24*60*60, 7 days
define('REDIS_CART_KEY_PREFIX','customer'); //customer:[customer_id[:[station_id]:cart
define('REDIS_CART_KEY_NAME','cart'); //customer:[customer_id[:[station_id]:cart
define('REDIS_CART_ITEM_QTY_LIMIT',99); //Max Cart Item Qty, default 99
define('REDIS_CART_ITEM_LIMIT',30); //Max Cart Item, default 30

define('CANCELLED_ORDER_STATUS',3);
?>
