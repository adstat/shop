<?php
ini_set('display_errors',1);

define('SITE_URI','http://localhost/xsj/');
define('DIR_PATH','/Users/alexsun/htdocs/xsj');

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
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'xsj');
define('DB_PREFIX', 'oc_');

//Slave: Select
define('DB_DRIVER_SLAVE', 'mysql');
define('DB_HOSTNAME_SLAVE', 'localhost');
define('DB_USERNAME_SLAVE', 'slave01');
define('DB_PASSWORD_SLAVE', 'dsj023lm23mkl23m2');
define('DB_DATABASE_SLAVE', 'xsj');

define('ALLOW_CANCEL',serialize(array(1,2)));
define('ALLOW_M_DELIVER',serialize(array(1,2,4)));
define('ALLOW_M_DELIVER_DATE',serialize(array(5,7)));
define('ALLOW_M_PAYMENT',serialize(array(1)));
define('CANCELLED_ORDER_STATUS',3);