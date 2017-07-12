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
require_once (DIR_MODULE.'/logistic.php');
require_once (DIR_MODULE.'/inventory.php');

//Get XMLRPC Module
require_once './xmlrpc.php';
require_once './xmlrpcs.php';
require_once './xmlrpc_wrappers.php';


class soaXMLRPC{
    static function onGetWebServices(){
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

            'getVisitDone' => '',
            'updateStatus'=>'',
            'updateStatus2'=>'',
            'getOrderNoInfo'=>'',
            'getOrderAllInfo'=>'',
            'getOrderAlreadyInfo'=>'',
            'getReturnOrders'=>'',
            'deliverConfirmReturnProduct' => '',
            'warehouseConfirmReturnProduct' => ''
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
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->getUserInfo($data);
    }

    function getOrderNoInfo($data, $origin_id, $key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->getOrderNoInfo($data);
    }
    function cancelOrderInfo($data, $origin_id, $key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->cancelOrderInfo($data);
    }

    function updateStatus($data, $origin_id, $key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->updateStatus($data);
    }

    function updateStatus2($data, $origin_id, $key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->updateStatus2($data);
    }

    function updateStatus1($data, $origin_id, $key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->updateStatus1($data);
    }



//    function getBdInfo($data, $origin_id, $key){
//        global $logistic;
//        if ( !soaHelper::auth($origin_id, $key) ){
//            return 'ERROR, NO AUTHORIZED.';
//        }
//
//        $data = json_decode($data, true);
//        $data['key'] = soaHelper::auth($origin_id, $key);
//
//        return $logistic->getBdInfo($data);
//    }

    function getDriverInfo($data, $origin_id, $key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->getDriverInfo($data);
    }

    function getCustomerInfo($data, $origin_id, $key){
        global $logistic;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->getCustomerInfo($data);
    }


    function getOrderAllInfo($data,$origin_id,$key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->getOrderAllInfo($data);
    }

    function getOrderAlreadyInfo($data,$origin_id,$key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data,true);
        $data['key'] = soaHelper::auth($origin_id,$key);

        return $logistic->getOrderAlreadyInfo($data);
    }

    function  getReturnOrders($data,$origin_id,$key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data,true);
        $data['key'] = soaHelper::auth($origin_id,$key);

        return $logistic->getReturnOrders($data);
    }

    function getVisitDone($data,$origin_id,$key){
        global $logistic;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        $data = json_decode($data, true);
        $data['key'] = soaHelper::auth($origin_id, $key);

        return $logistic->getVisitDone($data);
    }

    function deliverConfirmReturnProduct($data,$origin_id,$key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $inventory;
        return $inventory->deliverConfirmReturnProduct(json_decode($data, true));
    }

    function warehouseConfirmReturnProduct($data,$origin_id,$key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $inventory;
        return $inventory->deliverConfirmReturnProduct(json_decode($data, true));
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