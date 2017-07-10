<?php
if($_GET['auth'] !== 'klsdlcklsdnon23lnwl'){
    exit('NO Auth!');
}

require_once('config.php');
require_once(DIR_SYSTEM . 'startup.php');


// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '42'");
exit(var_dump($query));