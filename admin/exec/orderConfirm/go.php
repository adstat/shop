<?php
ini_set('display_errors',0);

include_once("./SendTemplateSMS.php");
include_once("../../config.php");
include_once("./log.php");
require_once(DIR_SYSTEM . 'startup.php');

date_default_timezone_set('PRC'); //SET TIME ZONE

$sms_accesskey = isset($_REQUEST['smskey']) ? $_REQUEST['smskey'] : false;
if( $sms_accesskey !== 'sdjksbeoi2b3lkjdeiuh23jlwk'){
    $log->write('UNAUTHORIZED ACCESS: '.@$_SERVER['REMOTE_ADDR'].' - '.@$_SERVER['HTTP_USER_AGENT']);
    exit("UNAUTHORIZED ACCESS!");
}

$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$db_slave = new DB(DB_DRIVER_SLAVE, DB_HOSTNAME_SLAVE, DB_USERNAME_SLAVE, DB_PASSWORD_SLAVE, DB_DATABASE_SLAVE);

//Batch send SMS, 100 rows once
$query = $db_slave->query("SELECT msg_id, phone,msg_param_1,msg_param_2,isp_template_id FROM oc_msg WHERE sent = 0 AND status = 1 AND retry < 3 LIMIT 100");
$pools = $query->rows;

if(is_array($pools) && sizeof($pools)){
    $success_msg_ids = array();
    $error_msg_ids = array();

    foreach($pools as $pool){
        $phone = $pool['phone'];
        $content = array($pool['msg_param_1'],$pool['msg_param_2']);
        $template_id = $pool['isp_template_id'];

        //$result = sendTemplateSMS("",array('123987','5'),"");//手机号码，替换内容数组，模板ID
        $result = sendTemplateSMS($phone,$content,$template_id);//手机号码，替换内容数组，模板ID
        //$result = false;

        if($result === true){
            $success_msg_ids[] = $pool['msg_id'];
        }
        else{
            //TODO error log
            $error_msg_ids[] = $pool['msg_id'];
        }
    }

    //Update sent msg status
    if( sizeof($success_msg_ids) ){
        $log->write('SMS SUCCESS IDs: '.implode(',',$success_msg_ids) );
        $db->query("UPDATE oc_msg SET sent = 1, date_sent = NOW() WHERE msg_id IN (".implode(',',$success_msg_ids).")");
    }

    //Update failed msg status - retry times
    if( sizeof($error_msg_ids) ){
        $log->write('SMS ERROR IDs: '.implode(',',$error_msg_ids) );
        $db->query("UPDATE oc_msg SET retry = retry+1, date_sent = NOW() WHERE msg_id IN (".implode(',',$error_msg_ids).")");
    }
}
else{
    //$log->write('SMS: Nothing in the queue.');
}

//var_dump($success_msg_ids);
//echo "<hr />";
//var_dump( $error_msg_ids );
