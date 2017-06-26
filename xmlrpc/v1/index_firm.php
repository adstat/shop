<?php
/* Init. Start */

require_once('../../config.php');

//Get DB Object, $dbm for Master(to write), $db for Slave(for select query)
require_once(DIR_SYSTEM.'/db.php');

//Get Log Object, $log for GMT+8, for China Time Zone only
require_once(DIR_SYSTEM.'/log.php');

//Get Modules Object, $order, $product $customer
//TODO, use autoloader
//require_once(DIR_MODULE.'/loader.php');
require_once (DIR_MODULE.'/firm.php');

//Get XMLRPC Module
require_once './xmlrpc.php';
require_once './xmlrpcs.php';
require_once './xmlrpc_wrappers.php';

//Test Auth
//$a = unserialize(AUTHKEY);
//var_dump($a['callcenter']);

//Test Query Select
// $sql = "INSERT INTO oc_ticket_order_memo(ticket_id,order_id) VALUES(552, 204909)";
// $sql = "SELECT * FROM oc_banner";
// $query = $db->query($sql);
// $results = $query->rows;
// var_dump($results);
// exit;

//Test Log
// var_dump(DIR_LOGS);
// $orderId = '2222';
//$log->write('API Start' . "\n\r");

//error_reporting(0);
/* Init. End */

//return strtotime(date("Y-m-d H:i:s"));
//return time();
//return strtotime($results[0]['start_date']);
//$log->write('START API'."\n\r");
//$log->write(serialize($HTTP_RAW_POST_DATA)."\n\r");

class soaXMLRPC{
    function onGetWebServices(){
        global $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        $services = array();

        /*
        * Service Registration Start
        */
        $registeredServices = array(
            // 'METHOD_NAME' => 'Method description'
            'getUserInfo' => 'Get user id from XSJ back-end, for admin view',
            'getBdInfo' => 'Get user id from XSJ back-end, for BD view',

            'getCustomerInfo' => '',

            'getSleepCustomerInfo' => '',

            'getWeekInfo' => '',
            'getLastWeekInfo' => '',
            'getLastMonthInfo' => '',

            'getOneOrder' => '',
            'getTwoOrder' => '',
            'getThreeOrder' => '',

            'addRealVisit' => '',
            'addVisitPlan' => '',
            'getVisitPlan' => '',
            'confirmVisit' => '',
            'getVisitDone' => '',
            'addNewMerchant' => '',
            'getNewVisitInfo' => '',
            'updatePreCustomerUnregister' => '',

            'cancelCustomer' => '',
            'cancelCustomerInfo' => '',
            'resetCustomer' => '',

            'getOrderDetail' => '',

            //经理页面接口
            'getBDVisitInfo'=> '',
            'getBDOrderInfo'=> '',

            'getCustomerNearby'=> '',
            'getxyLocation'=>'',

            'getBDname'=>'',
            'getAreaId'=>'',
            'getTotalCustomerNumber'=>'',

            //新增BD绩效报表
            'getNewPerforemance'=> '',
            'getOldPerforemance'=> '',
            'getPerforemanceInfo' => '',
            'getCoupon' => '',
            'bindCoupon' => '',
            'getCustomerCoupon' => ''
        );

        foreach($registeredServices as $k=>$v){
            $services['soa.'.$k] = array(
                'function' => 'soaFunctions::'.$k,
                'docstring'=> $v,
                'signature'=> array(
                    array(
                        $xmlrpcString, //Result
                        $xmlrpcString, //Request JSON data
                        $xmlrpcInt,    //Origin ID
                        $xmlrpcString  //Key
                    )
                )
            );
        }
        /*
        * Service Registration End
        */

        return $services;
    }
}

class soaFunctions{

    function getUserInfo($data, $origin_id, $key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getUserInfo($data);
    }

    function getBdInfo($data, $origin_id, $key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getBdInfo($data);
    }

    function getCustomerInfo($data, $origin_id, $key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getCustomerInfo($data);
    }

    function getSleepCustomerInfo($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getSleepCustomerInfo($data);
    }

    function getWeekInfo($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getWeekInfo($data);
    }

    function getLastWeekInfo($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data,true);
        $data['key'] = soaHelper::auth($origin_id,$key);

        return $firm->getLastWeekInfo($data);
    }

    function getLastMonthInfo($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getLastMonthInfo($data);
    }

    function getOneOrder($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getOneOrder($data);
    }

    function getTwoOrder($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getTwoOrder($data);
    }

    function getThreeOrder($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getThreeOrder($data);
    }

    function getVisitPlan($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getVisitPlan($data);
    }

    function addRealVisit($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->addRealVisit($data);
    }

    function addVisitPlan($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->addVisitPlan($data);
    }

    function confirmVisit($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->confirmVisit($data);
    }

    function getVisitDone($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getVisitDone($data);
    }

    function addNewMerchant($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->addNewMerchant($data);
    }

    function getNewVisitInfo($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getNewVisitInfo($data);
    }

    function updatePreCustomerUnregister($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->updatePreCustomerUnregister($data);
    }

    function cancelCustomer($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->cancelCustomer($data);
    }

    function cancelCustomerInfo($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->cancelCustomerInfo($data);
    }

    function resetCustomer($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->resetCustomer($data);
    }

    function getOrderDetail($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getOrderDetail($data);
    }

    //经理页面调用
    function getBDVisitInfo($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getBDVisitInfo($data);
    }

    function getBDOrderInfo($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getBDOrderInfo($data);
    }

    function getCustomerNearby($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getCustomerNearby($data);
    }

    function getxyLocation($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getxyLocation($data);
    }

    function getBDname($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getBDname($data);
    }

    function getAreaId($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getAreaId($data);
    }

    function getTotalCustomerNumber($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getTotalCustomerNumber($data);
    }

    function getNewPerforemance($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getNewPerforemance($data);
    }

    function getOldPerforemance($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getOldPerforemance($data);
    }

    function getPerforemanceInfo($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getPerforemanceInfo($data);
    }

    function getCoupon($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getCoupon($data);
    }

    function bindCoupon($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->bindCoupon($data);
    }

    function getCustomerCoupon($data,$origin_id,$key){
        global $firm;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $firm->getCustomerCoupon($data);
    }

}


class soaHelper {
    function auth($origin_id, $key) {
        //TODO AUTH
        $auth = unserialize(AUTHKEY);

        //return $auth[$origin_id].'-'.$key;

        if($auth[$origin_id] == $key){
            return $key;
        }

        return false;
    }

    function buildQuery($type = 'INSERT', $table, $values, $whereClause = '', $doNotEnclose = array()) {
        
        if(empty($table) || empty($values)){
            return;
        }
        $table = trim($table);
        $type = trim($type);
        $type = strtoupper($type);
        
        switch($type){
            
            case 'INSERT' :
            case 'REPLACE' :
                
                $q = "$type INTO `$table` (`";
                $q .= implode("`,\n`", array_keys($values));
                
                $q .= "`) VALUES (\n";
                $count = count($values);
                $i = 1;
                foreach($values as $key => $value){
                    if (in_array($key, $doNotEnclose)) {
                        // Important when using MySQL functions like
                        // "AES_ENCRYPT", "ENCODE", "REPLACE" or such
                        $q .= $value;
                    } else {
                        $q .= '\'' . addslashes($value). "'\n";
                    }
                    if ($i ++ < $count) {
                        $q .= ',';
                    }
                }
                $q .= ')';
                break;
            
            case 'UPDATE' :
                
                $q = "UPDATE `$table` SET ";
                $count = count($values);
                $i = 1;
                foreach($values as $key => $value){
                    $q .= "`$key` = '" . addslashes($value). "'";
                    if ($i ++ < $count) {
                        $q .= ",\n";
                    }
                }
                $q .= "\n$whereClause";
                
                break;
            
            default :
                return;
        }
        
        return $q;
    }
    
    function xmlSafeStr($s) {
        return preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", "", $s);
    }
}

// Launch XML Serices
$allCalls [0] = soaXMLRPC::onGetWebServices();
$methodsArray = array();
foreach($allCalls as $calls){
    $methodsArray = array_merge($methodsArray, $calls);
}

$xmlrpcServer = new xmlrpc_server($methodsArray, false);
// allow casting to be defined by that actual values passed
$xmlrpcServer->functions_parameters_type = 'phpvals';
// define UTF-8 as the internal encoding for the XML-RPC server
$xmlrpcServer->xml_header("UTF-8");
$xmlrpc_internalencoding = "UTF-8";
// debug level
$xmlrpcServer->setDebug(3);

// start the service
$xmlrpcServer->service();
?>