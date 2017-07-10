<?php
/* Init. Start, mark sure all date*/

require_once('../../config.php');

//Get DB Object, $dbm for Master(to write), $db for Slave(for select query)
require_once(DIR_SYSTEM.'/db.php');

//Get Log Object, $log for GMT+8, for China Time Zone only
require_once(DIR_SYSTEM.'/log.php');

//Get Modules Object, $order, $product $customer
//TODO
//require_once(DIR_MODULE.'/loader.php');


/*
error_reporting(E_ALL);
ini_set('display_errors',1);
*/

var_dump(44555);
//Get Modules Object, TODO, repalce with loader

//require_once (DIR_MODULE.'/cart.php');
require_once (DIR_MODULE.'/oldwarehouse.php');
require_once (DIR_MODULE.'/warehouse.php');
require_once (DIR_MODULE.'/locationverifi.php');
//var_dump($order->test());


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
//return strtotime($results[0]['start_date']);
//$log->write('START API'."\n\r");
//$log->write(serialize($HTTP_RAW_POST_DATA)."\n\r");

class soaXMLRPC{
    static function onGetWebServices(){
        global $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
        $services = array();

        //FOR HOMEPAGE/COMMON [DEBUG_CODE:10XYYY, 10接口类型；X为消息类型0,1,2错误，正常，未知消息；YYY消息定义]
        //TODO 1、定位，匹配获取配送点 getStation
        //2、获取首页公告等
        //3、获取首页图片广告
        //4、获取菜单结构（目前只使用一级菜单，排序）
        $services['soa.getStation'] = array(
            'function' => 'soaFunctions::getStation',
            'docstring' => 'getStation($id, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getOrders'] = array(
            'function' => 'soaFunctions::getOrders',
            'docstring' => 'getOrders($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getPurchaseOrders'] = array(
            'function' => 'soaFunctions::getPurchaseOrders',
            'docstring' => 'getPurchaseOrders($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.ordered'] = array(
            'function' => 'soaFunctions::ordered',
            'docstring' => 'ordered($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getInventoryUserOrder'] = array(
            'function' => 'soaFunctions::getInventoryUserOrder',
            'docstring' => 'getInventoryUserOrder($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getProductWeightInfo'] = array(
            'function' => 'soaFunctions::getProductWeightInfo',
            'docstring' => 'getProductWeightInfo($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        $services['soa.getOrderss'] = array(
            'function' => 'soaFunctions::getOrderss',
            'docstring' => 'getOrderss($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.orderdistr'] = array(
            'function' => 'soaFunctions::orderdistr',
            'docstring' => 'orderdistr($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.orderRedistr'] = array(
            'function' => 'soaFunctions::orderRedistr',
            'docstring' => 'orderRedistr($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getNotice'] = array(
            'function' => 'soaFunctions::getNotice',
            'docstring' => 'Get Notice, getNotice($id, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getBanner'] = array(
            'function' => 'soaFunctions::getBanner',
            'docstring' => 'Get Banner, getBanner($id, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getCategory'] = array(
            'function' => 'soaFunctions::getCategory',
            'docstring' => 'Get Category, getCategory($id, $station_id=1, $language_id=2, $origin_id, $key), here id is partent id',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Specific ID
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );




        $services['soa.getOrderSortingList'] = array(
            'function' => 'soaFunctions::getOrderSortingList',
            'docstring' => 'getOrderSortingList($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getPurchaseOrderSortingList'] = array(
            'function' => 'soaFunctions::getPurchaseOrderSortingList',
            'docstring' => 'getPurchaseOrderSortingList($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );



        //FOR Station Offline Retail
        $services['soa.stationRetail'] = array(
            'function' => 'soaFunctions::stationRetail',
            'docstring' => 'stationRetail($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.pushOrderByStation'] = array(
            'function' => 'soaFunctions::pushOrderByStation',
            'docstring' => 'pushOrderByStation($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getStationProductInfo'] = array(
            'function' => 'soaFunctions::getStationProductInfo',
            'docstring' => 'getStationProductInfo($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getOrderByStation'] = array(
            'function' => 'soaFunctions::getOrderByStation',
            'docstring' => 'getOrderByStation($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getStationOrderProduct'] = array(
            'function' => 'soaFunctions::getStationOrderProduct',
            'docstring' => 'getStationOrderProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.addOrderProductStation'] = array(
            'function' => 'soaFunctions::addOrderProductStation',
            'docstring' => 'addOrderProductStation($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addPurchaseOrderProductStation'] = array(
            'function' => 'soaFunctions::addPurchaseOrderProductStation',
            'docstring' => 'addPurchaseOrderProductStation($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.stationOrderCancel'] = array(
            'function' => 'soaFunctions::stationOrderCancel',
            'docstring' => 'stationOrderCancel($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addOrderProductToInv_pre'] = array(
            'function' => 'soaFunctions::addOrderProductToInv_pre',
            'docstring' => 'addOrderProductToInv_pre($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addPurchaseOrderProductToInv_pre'] = array(
            'function' => 'soaFunctions::addPurchaseOrderProductToInv_pre',
            'docstring' => 'addPurchaseOrderProductToInv_pre($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        $services['soa.addOrderProductToInv'] = array(
            'function' => 'soaFunctions::addOrderProductToInv',
            'docstring' => 'addOrderProductToInv($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addFastMoveSortingToInv'] = array(
            'function' => 'soaFunctions::addFastMoveSortingToInv',
            'docstring' => 'addFastMoveSortingToInv($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        $services['soa.addPurchaseOrderProductToInv'] = array(
            'function' => 'soaFunctions::addPurchaseOrderProductToInv',
            'docstring' => 'addPurchaseOrderProductToInv($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.delOrderProductToInv'] = array(
            'function' => 'soaFunctions::delOrderProductToInv',
            'docstring' => 'delOrderProductToInv($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.delPurchaseOrderProductToInv'] = array(
            'function' => 'soaFunctions::delPurchaseOrderProductToInv',
            'docstring' => 'delPurchaseOrderProductToInv($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addCheckProductToInv'] = array(
            'function' => 'soaFunctions::addCheckProductToInv',
            'docstring' => 'addCheckProductToInv($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addCheckSingleProductToInv'] = array(
            'function' => 'soaFunctions::addCheckSingleProductToInv',
            'docstring' => 'addCheckSingleProductToInv($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.delCheckSingleProductToInv'] = array(
            'function' => 'soaFunctions::delCheckSingleProductToInv',
            'docstring' => 'delCheckSingleProductToInv($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addVegCheckProductToInv'] = array(
            'function' => 'soaFunctions::addVegCheckProductToInv',
            'docstring' => 'addVegCheckProductToInv($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.addOrderNum'] = array(
            'function' => 'soaFunctions::addOrderNum',
            'docstring' => 'addOrderNum($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addCheckFrameOut'] = array(
            'function' => 'soaFunctions::addCheckFrameOut',
            'docstring' => 'addCheckFrameOut($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addCheckFrameCage'] = array(
            'function' => 'soaFunctions::addCheckFrameCage',
            'docstring' => 'addCheckFrameCage($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        $services['soa.addCheckFrameCheck'] = array(
            'function' => 'soaFunctions::addCheckFrameCheck',
            'docstring' => 'addCheckFrameCheck($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getAddedFrameOut'] = array(
            'function' => 'soaFunctions::getAddedFrameOut',
            'docstring' => 'getAddedFrameOut($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getAddedFrameCage'] = array(
            'function' => 'soaFunctions::getAddedFrameCage',
            'docstring' => 'getAddedFrameCage($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getAddedFrameCheck'] = array(
            'function' => 'soaFunctions::getAddedFrameCheck',
            'docstring' => 'getAddedFrameCheck($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getAddedFrameIn'] = array(
            'function' => 'soaFunctions::getAddedFrameIn',
            'docstring' => 'getAddedFrameIn($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.addCheckFrameIn'] = array(
            'function' => 'soaFunctions::addCheckFrameIn',
            'docstring' => 'addCheckFrameIn($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        $services['soa.checkFrameCanIn'] = array(
            'function' => 'soaFunctions::checkFrameCanIn',
            'docstring' => 'checkFrameCanIn($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.stationOrderConfirm'] = array(
            'function' => 'soaFunctions::stationOrderConfirm',
            'docstring' => 'stationOrderConfirm($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.stationOrderDelivered'] = array(
            'function' => 'soaFunctions::stationOrderDelivered',
            'docstring' => 'stationOrderDelivered($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.stationOrderDeliverOut'] = array(
            'function' => 'soaFunctions::stationOrderDeliverOut',
            'docstring' => 'stationOrderDeliverOut($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getStationCustomerInfo'] = array(
            'function' => 'soaFunctions::getStationCustomerInfo',
            'docstring' => 'getStationCustomerInfo($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.inventoryIn'] = array(
            'function' => 'soaFunctions::inventoryIn',
            'docstring' => 'inventoryIn($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.inventoryInProduct'] = array(
            'function' => 'soaFunctions::inventoryInProduct',
            'docstring' => 'inventoryInProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        //出库退货，添加方法
        $services['soa.addReturnDeliverProduct'] = array(
            'function' => 'soaFunctions::addReturnDeliverProduct',
            'docstring' => 'addReturnDeliverProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        //出库退货，取消记录
        $services['soa.disableReturnDeliverProduct'] = array(
            'function' => 'soaFunctions::disableReturnDeliverProduct',
            'docstring' => 'disableReturnDeliverProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        //出库退货，全部确认
        $services['soa.confirmReturnDeliverProduct'] = array(
            'function' => 'soaFunctions::confirmReturnDeliverProduct',
            'docstring' => 'confirmReturnDeliverProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );




        $services['soa.inventoryOutProduct'] = array(
            'function' => 'soaFunctions::inventoryOutProduct',
            'docstring' => 'inventoryOutProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.inventoryAdjustProduct'] = array(
            'function' => 'soaFunctions::inventoryAdjustProduct',
            'docstring' => 'inventoryAdjustProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.inventoryChangeProduct'] = array(
            'function' => 'soaFunctions::inventoryChangeProduct',
            'docstring' => 'inventoryChangeProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        $services['soa.inventoryCheckProduct'] = array(
            'function' => 'soaFunctions::inventoryCheckProduct',
            'docstring' => 'inventoryCheckProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.inventoryCheckSingleProduct'] = array(
            'function' => 'soaFunctions::inventoryCheckSingleProduct',
            'docstring' => 'inventoryCheckSingleProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.inventoryVegCheckProduct'] = array(
            'function' => 'soaFunctions::inventoryVegCheckProduct',
            'docstring' => 'inventoryVegCheckProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.inventoryOut'] = array(
            'function' => 'soaFunctions::inventoryOut',
            'docstring' => 'inventoryOut($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        $services['soa.inventoryReturn'] = array(
            'function' => 'soaFunctions::inventoryReturn',
            'docstring' => 'inventoryReturn($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );



        $services['soa.inventoryInit'] = array(
            'function' => 'soaFunctions::inventoryInit',
            'docstring' => 'inventoryInit($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.inventoryBreakage'] = array(
            'function' => 'soaFunctions::inventoryBreakage',
            'docstring' => 'inventoryBreakage($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.inventoryNoonCheck'] = array(
            'function' => 'soaFunctions::inventoryNoonCheck',
            'docstring' => 'inventoryNoonCheck($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getStationProductInfob2b'] = array(
            'function' => 'soaFunctions::getStationProductInfob2b',
            'docstring' => 'getStationProductInfob2b($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getUserInfoByUid'] = array(
            'function' => 'soaFunctions::getUserInfoByUid',
            'docstring' => 'getUserInfoByUid($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getStationMove'] = array(
            'function' => 'soaFunctions::getStationMove',
            'docstring' => 'getStationMove($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.confirmStationMove'] = array(
            'function' => 'soaFunctions::confirmStationMove',
            'docstring' => 'confirmStationMove($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        $services['soa.getStationMoveItem'] = array(
            'function' => 'soaFunctions::getStationMoveItem',
            'docstring' => 'getStationMoveItem($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );


        $services['soa.getInventoryIn'] = array(
            'function' => 'soaFunctions::getInventoryIn',
            'docstring' => 'getInventoryIn($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getAddedReturnDeliverProduct'] = array(
            'function' => 'soaFunctions::getAddedReturnDeliverProduct',
            'docstring' => 'getAddedReturnDeliverProduct($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.inventory_login'] = array(
            'function' => 'soaFunctions::inventory_login',
            'docstring' => 'inventory_login($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );



        $services['soa.getInventoryAdjust'] = array(
            'function' => 'soaFunctions::getInventoryAdjust',
            'docstring' => 'getInventoryAdjust($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getInventoryOut'] = array(
            'function' => 'soaFunctions::getInventoryOut',
            'docstring' => 'getInventoryOut($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getInventoryChange'] = array(
            'function' => 'soaFunctions::getInventoryChange',
            'docstring' => 'getInventoryChange($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getInventoryCheck'] = array(
            'function' => 'soaFunctions::getInventoryCheck',
            'docstring' => 'getInventoryCheck($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getInventoryCheckSingle'] = array(
            'function' => 'soaFunctions::getInventoryCheckSingle',
            'docstring' => 'getInventoryCheckSingle($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getInventoryVegCheck'] = array(
            'function' => 'soaFunctions::getInventoryVegCheck',
            'docstring' => 'getInventoryVegCheck($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getOrderStatus'] = array(
            'function' => 'soaFunctions::getOrderStatus',
            'docstring' => 'getOrderStatus($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getPurchaseOrderStatus'] = array(
            'function' => 'soaFunctions::getPurchaseOrderStatus',
            'docstring' => 'getPurchaseOrderStatus($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        //Staiton Retial Login
        $services['soa.getSmsVerifyCode'] = array(
            'function' => 'soaFunctions::getSmsVerifyCode',
            'docstring' => 'getSmsVerifyCode($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString //JSON Retail Order Info
                )
            )
        );

        $services['soa.getStationOriginKey'] = array(
            'function' => 'soaFunctions::getStationOriginKey',
            'docstring' => 'getStationOriginKey($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString //JSON Retail Order Info
                )
            )
        );

        $services['soa.getStationInfo'] = array(
            'function' => 'soaFunctions::getStationInfo',
            'docstring' => 'getStationInfo($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.checkUpdate'] = array(
            'function' => 'soaFunctions::checkUpdate',
            'docstring' => 'checkUpdate($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString //JSON Retail Order Info
                )
            )
        );
        //每隔五分钟对检查订单是否取消
        $services['soa.updateOrderStatus'] = array(
            'function' => 'soaFunctions::updateOrderStatus',
            'docstring' => 'updateOrderStatus($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );




        $services['soa.sendMsg'] = array(
            'function' => 'soaFunctions::sendMsg',
            'docstring'=> 'sendMsg($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );








        //Product Inventory
        $services['soa.getProductInventory'] = array(
            'function' => 'soaFunctions::getProductInventory',
            'docstring' => 'getProductInventory($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getSkuProductInfo'] = array(
            'function' => 'soaFunctions::getSkuProductInfo',
            'docstring' => 'getSkuProductInfo($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.changeProductSection'] = array(
            'function' => 'soaFunctions::changeProductSection',
            'docstring' => 'changeProductSection($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getProductSectionInfo'] = array(
            'function' => 'soaFunctions::getProductSectionInfo',
            'docstring' => 'getProductSectionInfo($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getSkuProductInfoInv'] = array(
            'function' => 'soaFunctions::getSkuProductInfoInv',
            'docstring' => 'getSkuProductInfoInv($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //JSON Retail Order Info
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );






        $services['soa.checkFirstOrder'] = array(
            'function' => 'soaFunctions::checkFirstOrder',
            'docstring'=> 'checkFirstOrder($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );



        //Get Customer Group Info
        $services['soa.getAreaList'] = array(
            'function' => 'soaFunctions::getAreaList',
            'docstring'=> 'getAreaList($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );


        //司机确认出库
        $services['soa.getOrderByDriver'] = array(
            'function' => 'soaFunctions::getOrderByDriver',
            'docstring'=> 'getOrderByDriver($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.confirm_orderStatus'] = array(
            'function' => 'soaFunctions::confirm_orderStatus',
            'docstring'=> 'confirm_orderStatus($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getDeliverStatus'] = array(
            'function' => 'soaFunctions::getDeliverStatus',
            'docstring'=> 'getDeliverStatus($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.submitDeliverStatus'] = array(
            'function' => 'soaFunctions::submitDeliverStatus',
            'docstring'=> 'submitDeliverStatus($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        //货位核查跟出库前司机货物核对
        $services['soa.find_order'] = array(
            'function' => 'soaFunctions::find_order',
            'docstring'=> 'find_order($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getcheck'] = array(
            'function' => 'soaFunctions::getcheck',
            'docstring'=> 'getcheck($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.short_regist'] = array(
            'function' => 'soaFunctions::short_regist',
            'docstring'=> 'short_regist($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getInvComment'] = array(
            'function' => 'soaFunctions::getInvComment',
            'docstring'=> 'getInvComment($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getProductID'] = array(
            'function' => 'soaFunctions::getProductID',
            'docstring'=> 'getProductID($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getSpareProductID'] = array(
            'function' => 'soaFunctions::getSpareProductID',
            'docstring'=> 'getSpareProductID($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getSkuProductInfoS'] = array(
            'function' => 'soaFunctions::getSkuProductInfoS',
            'docstring'=> 'getSkuProductInfoS($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getSortNum'] = array(
            'function' => 'soaFunctions::getSortNum',
            'docstring'=> 'getSortNum($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getSpareGoods'] = array(
            'function' => 'soaFunctions::getSpareGoods',
            'docstring'=> 'getSpareGoods($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getSpareSkuProductInfo'] = array(
            'function' => 'soaFunctions::getSpareSkuProductInfo',
            'docstring'=> 'getSpareSkuProductInfo($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.submitReturn'] = array(
            'function' => 'soaFunctions::submitReturn',
            'docstring'=> 'submitReturn($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.submitReturnSpare'] = array(
            'function' => 'soaFunctions::submitReturnSpare',
            'docstring'=> 'submitReturnSpare($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getLocationOrderStatus'] = array(
            'function' => 'soaFunctions::getLocationOrderStatus',
            'docstring'=> 'getLocationOrderStatus($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getOrderByStatus'] = array(
            'function' => 'soaFunctions::getOrderByStatus',
            'docstring'=> 'getOrderByStatus($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getSumCheckOrder'] = array(
            'function' => 'soaFunctions::getSumCheckOrder',
            'docstring'=> 'getSumCheckOrder($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getCheckOrdersInfo'] = array(
            'function' => 'soaFunctions::getCheckOrdersInfo',
            'docstring'=> 'getCheckOrdersInfo($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getContainer'] = array(
            'function' => 'soaFunctions::getContainer',
            'docstring'=> 'getContainer($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getLocationOrderInfo'] = array(
            'function' => 'soaFunctions::getLocationOrderInfo',
            'docstring'=> 'getLocationOrderInfo($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getContainerInfo'] = array(
            'function' => 'soaFunctions::getContainerInfo',
            'docstring'=> 'getContainerInfo($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getCheckReason'] = array(
            'function' => 'soaFunctions::getCheckReason',
            'docstring'=> 'getCheckReason($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.submitCorrectionLocationOrder'] = array(
            'function' => 'soaFunctions::submitCorrectionLocationOrder',
            'docstring'=> 'submitCorrectionLocationOrder($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.submitUnLocationOrder'] = array(
            'function' => 'soaFunctions::submitUnLocationOrder',
            'docstring'=> 'submitUnLocationOrder($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getProductType'] = array(
            'function' => 'soaFunctions::getProductType',
            'docstring'=> 'getProductType($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.confirm_product'] = array(
            'function' => 'soaFunctions::confirm_product',
            'docstring'=> 'confirm_product($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.location_details'] = array(
            'function' => 'soaFunctions::location_details',
            'docstring'=> 'location_details($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.cancel_product'] = array(
            'function' => 'soaFunctions::cancel_product',
            'docstring'=> 'cancel_product($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.submitCheckDetails'] = array(
            'function' => 'soaFunctions::submitCheckDetails',
            'docstring'=> 'submitCheckDetails($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getSpareDetails'] = array(
            'function' => 'soaFunctions::getSpareDetails',
            'docstring'=> 'getSpareDetails($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.submitCheckSpareDetails'] = array(
            'function' => 'soaFunctions::submitCheckSpareDetails',
            'docstring'=> 'submitCheckSpareDetails($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getSearchCheck'] = array(
            'function' => 'soaFunctions::getSearchCheck',
            'docstring'=> 'getSearchCheck($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.cancel_searchProduct'] = array(
            'function' => 'soaFunctions::cancel_searchProduct',
            'docstring'=> 'cancel_searchProduct($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getDrivers'] = array(
            'function' => 'soaFunctions::getDrivers',
            'docstring'=> 'getDrivers($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.submitcheck'] = array(
            'function' => 'soaFunctions::submitcheck',
            'docstring'=> 'submitcheck($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        //获取整单退货订单信息
        $services['soa.getIssueOrderInfo'] = array(
            'function' => 'soaFunctions::getIssueOrderInfo',
            'docstring'=> 'getIssueOrderInfo($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getIssueReason'] = array(
            'function' => 'soaFunctions::getIssueReason',
            'docstring'=> 'getIssueReason($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.redistr'] = array(
            'function' => 'soaFunctions::redistr',
            'docstring'=> 'redistr($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.reDistrList'] = array(
            'function' => 'soaFunctions::reDistrList',
            'docstring'=> 'reDistrList($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getOrderInfo'] = array(
            'function' => 'soaFunctions::getOrderInfo',
            'docstring'=> 'getOrderInfo($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getLogisticId'] = array(
            'function' => 'soaFunctions::getLogisticId',
            'docstring'=> 'getLogisticId($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );


        $services['soa.getWarehouseId'] = array(
            'function' => 'soaFunctions::getWarehouseId',
            'docstring'=> 'getWarehouseId($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );


        //出库单
        $services['soa.getWarehouseRequisition'] = array(
            'function' => 'soaFunctions::getWarehouseRequisition',
            'docstring'=> 'getWarehouseRequisition($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.searchRequisition'] = array(
            'function' => 'soaFunctions::searchRequisition',
            'docstring'=> 'searchRequisition($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.viewItem'] = array(
            'function' => 'soaFunctions::viewItem',
            'docstring'=> 'viewItem($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        $services['soa.getRelevantProductID'] = array(
            'function' => 'soaFunctions::getRelevantProductID',
            'docstring'=> 'getRelevantProductID($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.startShipment'] = array(
            'function' => 'soaFunctions::startShipment',
            'docstring'=> 'startShipment($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.submitProduct'] = array(
            'function' => 'soaFunctions::submitProduct',
            'docstring'=> 'submitProduct($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.submitProducts'] = array(
            'function' => 'soaFunctions::submitProducts',
            'docstring'=> 'submitProducts($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        //操作整单退货
        $services['soa.handleRedistr'] = array(
            'function' => 'soaFunctions::handleRedistr',
            'docstring'=> 'handleRedistr($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.showOrderDetail'] = array(
            'function' => 'soaFunctions::showOrderDetail',
            'docstring'=> 'showOrderDetail($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );


        return $services;
    }
}

class soaFunctions{

    //TODO All keyCheck
    function getStation($id, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getStation($id, $station_id, $language_id, $origin_id);
    }



    function getOrders($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getOrders($data, $station_id, $language_id, $origin_id,$key);
    }

    function getPurchaseOrders($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getPurchaseOrders($data, $station_id, $language_id, $origin_id,$key);
    }

    function ordered($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->ordered($data, $station_id, $language_id, $origin_id,$key);
    }

    function getInventoryUserOrder($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getInventoryUserOrder($data, $station_id, $language_id, $origin_id,$key);
    }

    function getStatus($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getStatus($data, $station_id, $language_id, $origin_id,$key);
    }

    function getOrderss($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;


        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getOrderss($data, $station_id, $language_id, $origin_id,$key);
    }

    function orderdistr($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->orderdistr($data, $station_id, $language_id, $origin_id,$key);
    }

    function orderRedistr($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->orderRedistr($data, $station_id, $language_id, $origin_id,$key);
    }

    function getNotice($id, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getNotice($id, $station_id, $language_id, $origin_id);
    }

    function getBanner($id, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getBanner($id, $station_id, $language_id, $origin_id);
    }

    function getCategory($id, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getCategory($id, $station_id, $language_id, $origin_id);
    }


    //For Offline
    function stationRetail($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->stationRetail($data, $station_id, $language_id, $origin_id);
    }

    function pushOrderByStation($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->pushOrderByStation($data, $station_id, $language_id, $origin_id);
    }

    function getOrderByStation($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getOrderByStation($data, $station_id, $language_id, $origin_id);
    }
    function getProductWeightInfo($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getProductWeightInfo($data, $station_id, $language_id, $origin_id);
    }

    function getStationOrderProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getStationOrderProduct($data, $station_id, $language_id, $origin_id);
    }

    function addOrderProductToInv_pre($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addOrderProductToInv_pre($data, $station_id, $language_id, $origin_id);
    }

    function addPurchaseOrderProductToInv_pre($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addPurchaseOrderProductToInv_pre($data, $station_id, $language_id, $origin_id);
    }

    function addOrderProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addOrderProductToInv($data, $station_id, $language_id, $origin_id);
    }

    function addFastMoveSortingToInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addFastMoveSortingToInv($data, $station_id, $language_id, $origin_id);
    }

    function addPurchaseOrderProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addPurchaseOrderProductToInv($data, $station_id, $language_id, $origin_id);
    }

    function delOrderProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->delOrderProductToInv($data, $station_id, $language_id, $origin_id);
    }

    function delPurchaseOrderProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->delPurchaseOrderProductToInv($data, $station_id, $language_id, $origin_id);
    }



    function addCheckProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addCheckProductToInv($data, $station_id, $language_id, $origin_id);
    }

    function addCheckSingleProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addCheckSingleProductToInv($data, $station_id, $language_id, $origin_id);
    }

    function delCheckSingleProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->delCheckSingleProductToInv($data, $station_id, $language_id, $origin_id);
    }

    function addVegCheckProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addVegCheckProductToInv($data, $station_id, $language_id, $origin_id);
    }


    function addOrderNum($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addOrderNum($data, $station_id, $language_id, $origin_id);
    }
    function addCheckFrameOut($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addCheckFrameOut($data, $station_id, $language_id, $origin_id);
    }

    function addCheckFrameCage($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addCheckFrameCage($data, $station_id, $language_id, $origin_id);
    }

    function addCheckFrameCheck($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addCheckFrameCheck($data, $station_id, $language_id, $origin_id);
    }

    function addContainer(){
        global $oldwarehouse;

        return $oldwarehouse->addContainer();
    }


    function getAddedFrameOut($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getAddedFrameOut($data, $station_id, $language_id, $origin_id);
    }

    function getAddedFrameCage($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getAddedFrameCage($data, $station_id, $language_id, $origin_id);
    }


    function getAddedFrameCheck($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getAddedFrameCheck($data, $station_id, $language_id, $origin_id);
    }
    function getAddedFrameIn($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getAddedFrameIn($data, $station_id, $language_id, $origin_id);
    }

    function checkFrameCanIn($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->checkFrameCanIn($data, $station_id, $language_id, $origin_id);
    }

    function addCheckFrameIn($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addCheckFrameIn($data, $station_id, $language_id, $origin_id);
    }

    function stationOrderCancel($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->stationOrderCancel($data, $station_id, $language_id, $origin_id);
    }

    function stationOrderConfirm($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->stationOrderConfirm($data, $station_id, $language_id, $origin_id);
    }

    function stationOrderDelivered($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->stationOrderDelivered($data, $station_id, $language_id, $origin_id);
    }

    function stationOrderDeliverOut($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->stationOrderDeliverOut($data, $station_id, $language_id, $origin_id);
    }

    function getStationCustomerInfo($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getStationCustomerInfo($data, $station_id, $language_id, $origin_id);
    }

    function inventoryIn($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        //Limit
        if($origin_id == 7 && in_array($station_id, unserialize(STATION_INVENTORY_FUNC_LIMIT))){
            return false; //Some station inventory function via POS limited
        }

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        //For App version 0803 bug
        if($origin_id == 7 && $station_id == 10){
            return $oldwarehouse->inventoryInit($data, $station_id, $language_id, $origin_id);
        }
        if($origin_id == 7 && $station_id == 11){
            return $oldwarehouse->inventoryInit($data, $station_id, $language_id, $origin_id);
        }
        if($origin_id == 7 && $station_id == 12){
            return $oldwarehouse->inventoryInit($data, $station_id, $language_id, $origin_id);
        }

        return $oldwarehouse->inventoryIn($data, $station_id, $language_id, $origin_id);
    }


    function inventoryInProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;



        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }


        return $oldwarehouse->inventoryInProduct($data, $station_id, $language_id, $origin_id);
    }

    function addReturnDeliverProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;



        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }


        return $oldwarehouse->addReturnDeliverProduct($data, $station_id, $language_id, $origin_id);
    }

    function disableReturnDeliverProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->disableReturnDeliverProduct($data, $station_id, $language_id, $origin_id);
    }


    function confirmReturnDeliverProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->confirmReturnDeliverProduct($data, $station_id, $language_id, $origin_id);
    }

    function inventoryOutProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;



        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }


        return $oldwarehouse->inventoryOutProduct($data, $station_id, $language_id, $origin_id);
    }
    function inventoryAdjustProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;



        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }


        return $oldwarehouse->inventoryAdjustProduct($data, $station_id, $language_id, $origin_id);
    }
    function inventoryChangeProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;



        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }


        return $oldwarehouse->inventoryChangeProduct($data, $station_id, $language_id, $origin_id);
    }
    function inventoryCheckProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;



        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }


        return $oldwarehouse->inventoryCheckProduct($data, $station_id, $language_id, $origin_id);
    }

    function inventoryCheckSingleProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;



        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }


        return $oldwarehouse->inventoryCheckSingleProduct($data, $station_id, $language_id, $origin_id);
    }


    function inventoryVegCheckProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;



        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }


        return $oldwarehouse->inventoryVegCheckProduct($data, $station_id, $language_id, $origin_id);
    }


    function inventoryOut($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->inventoryOut($data, $station_id, $language_id, $origin_id);
    }

    function getStationProductInfob2b($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getStationProductInfob2b($data, $station_id, $language_id, $origin_id);
    }


    function inventoryReturn($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;
        global $log;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        return $oldwarehouse->inventoryReturn($data, $station_id, $language_id, $origin_id);
    }

    function inventoryInit($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        //Limit
        if($origin_id == 7 && in_array($station_id, unserialize(STATION_INVENTORY_FUNC_LIMIT))){
            return false; //Some station inventory function via POS limited
        }

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->inventoryInit($data, $station_id, $language_id, $origin_id);
    }

    function inventoryBreakage($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        //Limit
        if($origin_id == 7 && in_array($station_id, unserialize(STATION_INVENTORY_FUNC_LIMIT))){
            return false; //Some station inventory function via POS limited
        }

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->inventoryBreakage($data, $station_id, $language_id, $origin_id);
    }

    function inventoryNoonCheck($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        //Limit
        //if($origin_id == 7 && in_array($station_id, unserialize(STATION_INVENTORY_FUNC_LIMIT))){
        //    return false; //Some station inventory function via POS limited
        //}

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->inventoryNoonCheck($data, $station_id, $language_id, $origin_id);
    }

    function getStationProductInfo_bak($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getStationProductInfo($data, $station_id, $language_id, $origin_id);
    }
    //Inv Get Plan - TEMP
    function getOrderSortingList($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getOrderSortingList($data, $station_id, $language_id, $origin_id);
    }

    function getPurchaseOrderSortingList($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getPurchaseOrderSortingList($data, $station_id, $language_id, $origin_id);
    }
    function getUserInfoByUid($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getUserInfoByUid($data, $station_id, $language_id, $origin_id);
    }

    function getStationMove($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getStationMove($data, $station_id, $language_id, $origin_id);
    }

    function confirmStationMove($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->confirmStationMove($data, $station_id, $language_id, $origin_id);
    }

    function getStationMoveItem($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getStationMoveItem($data, $station_id, $language_id, $origin_id);
    }


    //Station Retail Login
    function getSmsVerifyCode($data){
        global $oldwarehouse;

        return $oldwarehouse->getSmsVerifyCode($data);
    }

    function getStationOriginKey($data){
        global $oldwarehouse;

        return $oldwarehouse->getStationOriginKey($data);
    }

    function getStationInfo($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getStationInfo($data, $station_id, $language_id, $origin_id);
    }

    function checkUpdate($data){
        global $oldwarehouse;

        return $oldwarehouse->checkUpdate($data);
    }




    function sendMsg($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $oldwarehouse;
        return $oldwarehouse->sendMsg(json_decode($data, true));
    }




    function getProductInventory($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getProductInventory($data, $station_id, $language_id, $origin_id);
    }

    function addOrderProductStation($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addOrderProductStation($data, $station_id, $language_id, $origin_id);
    }

    function addPurchaseOrderProductStation($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->addPurchaseOrderProductStation($data, $station_id, $language_id, $origin_id);
    }

    function getSkuProductInfo($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getSkuProductInfo($data, $station_id, $language_id, $origin_id);
    }

    function changeProductSection($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->changeProductSection($data, $station_id, $language_id, $origin_id);
    }

    function getProductSectionInfo($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getProductSectionInfo($data, $station_id, $language_id, $origin_id);
    }

    function getSkuProductInfoInv($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getSkuProductInfoInv($data, $station_id, $language_id, $origin_id);
    }


    function getInventoryIn($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getInventoryIn($data, $station_id, $language_id, $origin_id);
    }


    function getAddedReturnDeliverProduct($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getAddedReturnDeliverProduct($data, $station_id, $language_id, $origin_id);
    }

    function inventory_login($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->inventory_login($data, $station_id, $language_id, $origin_id);
    }

    function getInventoryAdjust($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getInventoryAdjust($data, $station_id, $language_id, $origin_id);
    }

    function getInventoryOut($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getInventoryOut($data, $station_id, $language_id, $origin_id);
    }
    function getInventoryChange($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getInventoryChange($data, $station_id, $language_id, $origin_id);
    }
    function getInventoryCheck($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getInventoryCheck($data, $station_id, $language_id, $origin_id);
    }
    function getInventoryCheckSingle($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getInventoryCheckSingle($data, $station_id, $language_id, $origin_id);
    }
    function getInventoryVegCheck($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getInventoryVegCheck($data, $station_id, $language_id, $origin_id);
    }
    function getOrderStatus($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getOrderStatus($data, $station_id, $language_id, $origin_id);
    }

    function getPurchaseOrderStatus($data, $station_id, $language_id, $origin_id, $key){
        global $oldwarehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getPurchaseOrderStatus($data, $station_id, $language_id, $origin_id);
    }


    function checkFirstOrder($data, $origin_id, $key){
        global $oldwarehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->checkFirstOrder(json_decode($data, true));
    }



    function getAreaList($data, $origin_id, $key){
        global $oldwarehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getAreaList(json_decode($data, true));
    }

    //司机确认订单出库
    function getOrderByDriver($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getOrderByDriver(json_decode($data, true));
    }
    function confirm_orderStatus($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->confirm_orderStatus(json_decode($data, true));
    }

    function getDeliverStatus($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getDeliverStatus(json_decode($data, true));
    }


    function  submitDeliverStatus($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->submitDeliverStatus(json_decode($data, true));
    }

    ///货物核查跟司机出库前货物核对
    function find_order($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->find_order(json_decode($data, true));
    }

    function getcheck($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getcheck(json_decode($data, true));
    }

    function short_regist($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->short_regist(json_decode($data, true));
    }

    function getInvComment($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getInvComment(json_decode($data, true));
    }
    function getProductID($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getProductID(json_decode($data, true));
    }

    function getSpareProductID($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getSpareProductID(json_decode($data, true));
    }

    function getSkuProductInfoS($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getSkuProductInfoS(json_decode($data, true));
    }
    function getSortNum($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getSortNum(json_decode($data, true));
    }
    function  getSpareGoods($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getSpareGoods(json_decode($data, true));
    }
    function  getSpareSkuProductInfo($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getSpareSkuProductInfo(json_decode($data, true));
    }
    function  submitReturn($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->submitReturn(json_decode($data, true));
    }
    function  submitReturnSpare($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->submitReturnSpare(json_decode($data, true));
    }

    function  getLocationOrderStatus($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->getLocationOrderStatus(json_decode($data, true));
    }
    function  getOrderByStatus($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->getOrderByStatus(json_decode($data, true));
    }
    function  getSumCheckOrder($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->getSumCheckOrder(json_decode($data, true));
    }
    function  getCheckOrdersInfo($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->getCheckOrdersInfo(json_decode($data, true));
    }

    function  getContainer($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->getContainer(json_decode($data, true));
    }
    function  getLocationOrderInfo($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->getLocationOrderInfo(json_decode($data, true));
    }
    function  getContainerInfo($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->getContainerInfo(json_decode($data, true));
    }
    function  getCheckReason($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->getCheckReason(json_decode($data, true));
    }
    function  submitCorrectionLocationOrder($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->submitCorrectionLocationOrder(json_decode($data, true));
    }
    function  submitUnLocationOrder($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $locationverifi->submitUnLocationOrder(json_decode($data, true));
    }

    function getProductType($data, $origin_id, $key){
        global $oldwarehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $oldwarehouse->getProductType(json_decode($data, true));
    }

    function  confirm_product($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->confirm_product(json_decode($data, true));
    }


    function location_details($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->location_details(json_decode($data, true));
    }

    function cancel_product($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->cancel_product(json_decode($data, true));
    }
    function submitCheckDetails($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->submitCheckDetails(json_decode($data, true));
    }
    function getSpareDetails($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getSpareDetails(json_decode($data, true));
    }
    function submitCheckSpareDetails($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->submitCheckSpareDetails(json_decode($data, true));
    }
    function getSearchCheck($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getSearchCheck(json_decode($data, true));
    }

    function cancel_searchProduct($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->cancel_searchProduct(json_decode($data, true));
    }

    function getDrivers($data, $origin_id, $key){
        global $warehouse;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getDrivers(json_decode($data, true));
    }


    function submitcheck($data, $origin_id, $key){
        global $locationverifi;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        return $locationverifi->submitcheck(json_decode($data, true));
    }
// 获取整单退货订单信息
    function getIssueOrderInfo($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getIssueOrderInfo(json_decode($data, true));
    }

    function getIssueReason($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getIssueReason(json_decode($data, true));
    }
    function redistr($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->redistr(json_decode($data, true));
    }

    function reDistrList($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->reDistrList(json_decode($data, true));
    }

    function getOrderInfo($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getOrderInfo(json_decode($data, true));
    }
    function getLogisticId($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getLogisticId(json_decode($data, true));
    }


    function getWarehouseId($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;
        var_dump(556677);
        return('ceshi');
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getWarehouseId(json_decode($data, true));
    }


    //出库单
    function getWarehouseRequisition($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getWarehouseRequisition(json_decode($data, true));
    }
    function searchRequisition($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->searchRequisition(json_decode($data, true));
    }
    function viewItem($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->viewItem(json_decode($data, true));
    }

    function getRelevantProductID($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->getRelevantProductID(json_decode($data, true));
    }

    function startShipment($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->startShipment(json_decode($data, true));
    }

    function submitProduct($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->submitProduct(json_decode($data, true));
    }

    function submitProducts($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->submitProducts(json_decode($data, true));
    }


    //操作整单退货
    function handleRedistr($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->handleRedistr(json_decode($data, true));
    }
    function showOrderDetail($data, $station_id, $language_id, $origin_id, $key){
        global $warehouse;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $warehouse->showOrderDetail(json_decode($data, true));
    }



}


class soaHelper {
    function auth($origin_id, $key) {
        //TODO AUTH
        $auth = unserialize(AUTHKEY);

        //return $auth[$origin_id].'-'.$key;

        if($auth[$origin_id] == $key){
            return true;
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



/*
$return_products_move[] = array(
                                'product_batch' => '',
                                'due_date' => '0000-00-00', //There is a bug till year 2099.
                                'product_id' => 1480,
                                'special_price' => '1.5',
                                'qty' => "-1"
                            );
    $dataInv['order_id'] = 100931;
    $dataInv['api_method'] = 'inventoryReturn';
    $dataInv['products'] = $return_products_move;
    $dataInv['timestamp'] = time() + 8*3600 ;
    $dataInv['date'] = '2015-10-24';

   soaFunctions::getOrders(json_encode($dataInv),1,2,1,'wdf23447dkm316bf519d2juh5e47md56');
*/


/*
 if($_GET['addContainer'] == '1'){


   soaFunctions::addContainer();
 }
*/




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