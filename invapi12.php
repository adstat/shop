<?php
date_default_timezone_set('Asia/Shanghai');
require_once 'config.php';

//exit('ERROR: ');
//exit(DIR_PATH.'/api/xmlrpc/v1/xmlrpc.php');
//Load PHP XMLRPC
require_once DIR_PATH.'/api/xmlrpc/v1/xmlrpc.php';
//exit(SITE_URI . '/api/xmlrpc/v1/index_warehouse.php');

require_once DIR_PATH.'/api/xmlrpc/v1/xmlrpcs.php';
require_once DIR_PATH.'/api/xmlrpc/v1/xmlrpc_wrappers.php';




define('APIURL', SITE_URI . '/api/xmlrpc/v1/index_warehouse.php');
//exit(SITE_URI . '/api/xmlrpc/v1/index_warehouse.php');

define('APIOROGIN', 1); //API
define('APIKEY', 'xsj_dev_mode_origin_01');



$client = new xmlrpc_client(APIURL);

function init($method, $id=0, $data='', $station_id){

    global $client;


   // setcookie("inventory_user",$_COOKIE['inventory_user'],time()+2*3600);

    
    //Get from XMLRPC API
    $method = 'soa.'.$method;
    $msg=new xmlrpcmsg($method);

    if($data !== ''){
        $msg->addParam(new xmlrpcval($data, "string"));
    }
    else{
        $msg->addParam(new xmlrpcval($id, "int"));
    }
    $msg->addParam(new xmlrpcval($station_id, "int")); //Station id
    $msg->addParam(new xmlrpcval(2, "int")); //Language id
    $msg->addParam(new xmlrpcval(APIOROGIN, "int")); //Origin id
    $msg->addParam(new xmlrpcval(APIKEY, "string")); //Key

    $response=$client->send($msg);

    if($response->faultcode()==0) {
        return (php_xmlrpc_decode($response->value()));
    }
    else{
        exit('ERROR: '.$response->faultcode().', '.$response->faultstring().'');
    }
}


function rpcRequest($post){
    if(empty($post['method'])){
        exit('Missing Request Method.');
    }
    global $client;
    $method = 'soa.' . $post['method'];
    $msg    = new xmlrpcmsg($method);

    $request = array(
        'uid'         => isset($post['uid']) ? $post['uid'] : '',
        'customer_id' => isset($post['customer_id']) ? $post['customer_id'] : '',
        'language_id' => isset($post['language_id']) ? $post['language_id'] : '',
        'station_id' => isset($post['station_id']) ? $post['station_id'] : 0,
        'data' => isset($post['data']) && is_array($post['data']) ? $post['data'] : array(),
    );

    $msg->addParam(new xmlrpcval(json_encode($request), "string"));
    $msg->addParam(new xmlrpcval(APIOROGIN, "int"));                   //Access Origin id
    $msg->addParam(new xmlrpcval(APIKEY, "string"));                      //Access Key
    $response = $client->send($msg);

    if($response->faultcode()==0) {
//        exit(($response->value()));

        $result = php_xmlrpc_decode($response->value());
        exit(json_encode($result));
    }
    else{
        exit('ERROR: '.$response->faultcode().', '.$response->faultstring().'');
    }
}

function rpcRequestReturn($post){
    if(empty($post['method'])){
        exit('Missing Request Method.');
    }
    global $client;
    $method = 'soa.' . $post['method'];
    $msg    = new xmlrpcmsg($method);

    $request = array(
        'data' => isset($post['data']) && is_array($post['data']) ? $post['data'] : array(),
    );

    $msg->addParam(new xmlrpcval(json_encode($request), "string"));
    $msg->addParam(new xmlrpcval(ORIGIN_ID, "int"));                   //Access Origin id
    $msg->addParam(new xmlrpcval(KEY, "string"));                      //Access Key
    $response = $client->send($msg);

    if($response->faultcode()==0) {
        $result = php_xmlrpc_decode($response->value());
        return $result;
    }

    return false;
}

function formatReturn($code, $msg, $data){
    $return = array(
        'return_code' => $code,
        'return_msg' => $msg,
        'return_data' => $data
    );

    return $return;
}
/* Functions For Init.php End */



/* Launch */
$method = isset($_POST['method']) ? $_POST['method'] : false;

//Method Filter
$basic_method = array(
    'find_order',
    'getAccomplishFrame',
    'delOneProductInv',
    'getFrameProductNumber',
    'short_regist',
    'getInvComment',
    'getProductID',
    'getSpareProductID',
    'getSkuProductInfoS',
    'getSortNum',
    'getSpareGoods',
    'getSpareSkuProductInfo',
    'submitReturn',
    'submitReturnSpare',
    'getLocationOrderStatus',
    'getOrderByStatus',
    'getSumCheckOrder',
    'getCheckOrdersInfo',
    'getContainer',
    'getLocationOrderInfo',
    'getContainerInfo',
    'submitCorrectionLocationOrder',
    'submitUnLocationOrder',
    'getCheckReason',
    'getperms',
    'getProductType',
    'getAreaList',
    'getcheck',
    'confirm_product',
    'location_details',
    'cancel_product',
    'submitCheckDetails',
    'getSpareDetails',
    'submitCheckSpareDetails',
    'getSearchCheck',
    'cancel_searchProduct',
    'getDrivers',
    'getOrderByDriver',
    'confirm_orderStatus',
    'getDeliverStatus',
    'submitDeliverStatus',
    'getWarehouseId',
    'getUseWarehouseId',
    'getWarehouseProductId',
    'submitcheck',
    'get_order_deliver_status_history',
    //上架管理

    //异常数据查询
    getAbnormalSort,



    //获取仓库分拣人员
    'getinventoryname',
    'getinventorynamerepack',

 //整单退货
    'getIssueOrderInfo',
    'getIssueReason',
    'redistr',
    'reDistrList',
    'getOrderInfo',
    'getLogisticId',
    'showOrderDetail',
    'handleRedistr',

    //司机确认收到退货
    'showDeliverConfirm',
    'warehouseConfirmReturnProduct',

    //仓库调拨
    'getWarehouseRequisition',
    'getNewWarehouseRequisition',
    'searchRequisition',
    'searchRequisitionNew',
    'viewItem',
    'startShipment',
    'startShipmentNew',
    'makeGetReadyLists',
    'getRelevantProductID',
    'submitProduct',
    'submitProductNew',
    'submitRelevantProduct',
    'submitProducts',
    'submitProductsNew',
    'submitProductsLists',
    'relevantViewItem',
    'addPurchaseOrderRelevantToInv',
    'getProductInformation',
    //分拣班组长核查任务
    'submitCheckProductInformation',
    'getProductsInformation',
    'submitCheckProductResult',
    'getCheckOrderInformation',
    'cancelCheckOrderProduct',
    'submitCheckOrderResult',
    'getCheckOrderInformation',
    'getOrderCheckInformation',
    'getDeliverOrderToCheck',
    'getCheckOrderInStockArea',
    //缺货提醒
    'shortReminder',
    'getReminderList',
    'confirmReminder',
    'confirmReplenishment',
    'getInfo',
    //缺货单个确认

    'confirmReturnSingleProduct',
    //快消当面退
    'confirmReturnBadSingleProduct',

    //出库基础信息录入
    'submitCorrectionOutOrder',
    //出库订单商品信息核对
    'showOrderProducts',

    //采购单信息
    'getPurchaseInfo',
    'getPurchaseTypeOrders',

    //更新分拣码
    'changeProductSku',
    //货架上货
    'confirmReturnShelves',

    //移库操作
    'getTransferInfo',
    'addTranserMission',
    'addTranserMission1',
    'getTransferMission',
    'changeTransferValuse',
    'getTransferProductInfo',
    'addChangeProductTransfer',
    'ChangeProductTransferStatus',
    'getTransfer',
    //盘点
    'confirmCheckSingleProduct',
    'changeCheckSingleProduct',
    // 回收篮框
    'getOrderByFrame',
    'submitFrameInStatus',
    'confirmFrameInStatus',
    'checkContainer',
    //分区分拣
    'getOrderSpareSortingUser',
    'insertOrderDistrSpare',
    'getOrderInfoByCount',
    'confirmOrderInfoByCount',
    'addOrderInfoByCount',
    'getOrderInfoBySpareComment',
    'addInvComment',
    //移仓
    'palletMove',
    'updateStocksChecks',
    'getStockChecks',
    'deleteStockChecks',
    'getWarehouseSection',
    'updateStockChecks',
    'addStockInventory',
    'getStockChecksMove',
    'addStockMove',
    'getStockChecksIn',
    'delectStockMOve',
    'addStockIn',
    'addStockMoveTransfer',
    'confirmTransfer',
    //移库分页
    'getTransferMissionNUM',
    //手动添加移库商品
    'manualAddTransfer',
    //分拣/存货信息录入更新
    'getWarehouseTransferInfo',
    //仓库分区
    'getProductSectionType',
    'confirmOut',
    'confirmIn',
    'getStockSectionProduct',
    'getSkuProductId',
    'getProductAllSingleInfo',
    'deleteStockSectionPorduct',
    'getInventoryOrderSoring',
    'addOrderInvComment',
    //外仓发过来的do单与本仓合单
    'getRelevantInfoByInput',
    'get_order_information_to_merge',
    'catOrderProductInfo',
    'submitReturnProduct',
    // 分配DO 到调拨单
    'getAllotDoOrder',
    'getDoOrderStatus',
    'addDoOrderRelevant',
    'getRelevantInfoByCount',
    'confirmDoRelevant',
    'confirmDoRelevantC',
    'addConsolidatedRelevant',
    'getRelevantInfoByProduct',
    'addConsolidatedRelevant',
    'addConsolidatedDoInfo',
    'updateDoRelevantC',
    'getAutoOrders',
    'print_relevants_merge',
    'merge_relevant_orders',
    'delete_merge_relevant_orders',
    'update_relevant_orders',
    'get_merge_order_status',
    'getContainerInformation',
    'get_frame_vg_list_status',
    'get_frame_vg_list_unique',
    'updateDoRelevant',
    //浦西没到货修改订单状态
    'updateDoStatus',
    'mergeDeliverOrder',
    //删除分拣数据
    'deleteOrderSorting',
    'checkContainerId',
    'addWarehouseContainer',
    'getProductDeliver',
    'getContainerDeliver',

    //新调拨单申请 Alex 20180310 －开始
    'getTransferBoxes',
    'addTransferOrder',
    'getTransferOrder',
    'cancelTransferOrder',
    //新调拨单申请 Alex 20180310 - 结束

    'getOrderContainerHistory',

    //仓库可售库存管理
    'getInventorySortingReturn',
    'addInventorySortingReturn',
    'updateInvComment',

    //删除框里的商品
    'deleteContainerProduct',

    //整件批次分拣
    'getBoxBatchInfo',
    'getBoxBatchOrders',
    'addBoxBatchOrder',
    'updateBoxBatchOrder',
    'cancelBoxBatchOrder',
    'submit_box_product_batch',
    'getBoxBatchOrderItem'
);

if(in_array($method, $basic_method)){
    $post= $_POST;

    //Get WX User ID
    //TODO GET UID FROM CACHE
    //$userInfo = getWechatUserInfo(getWechatCropToken(),$post['code']);
    //$post['uid'] = isset($userInfo['UserId']) ? $userInfo['UserId'] : false;

    //Local Dev Setting
    //if($post['code'] == 'alex'){
    //    $post['uid'] = 'Alex';
    //}

    //exit(json_encode( formatReturn(API_RETURN_SUCCESS,'OK',$post) ));
    rpcRequest($post);
}
    

//var_dump($_POST);
function formatStrWithComma($str,$sym) {
    //Change line break to comma, remove any blank & space
    $str = trim($str);
    $str = ereg_replace("\t",$sym,$str);
    $str = ereg_replace("\r\n",$sym,$str);
    $str = ereg_replace("\r",$sym,$str);
    $str = ereg_replace("\n",$sym,$str);
    $str = ereg_replace(" ","",$str);
    return trim($str);
}

function auth(){
    $pwd = '2015888';

    if(isset($_POST['pwd']) && $_POST['pwd'] == $pwd){
        return true;
    }

    return false;
}

// Sample Request
//$method = 'inventoryIn';  //From 1 to station_id
//$method = 'inventoryOut'; //From station_id to 1
//$method = 'inventoryBreakage'; //From station_id to 1
//$data= '{"station":{"from":1,"to":2},"products":{"150628001085001050":5,"150628001053001250":6}}';

$regInvMethods = array('inventoryIn'=>'商品入库','inventoryOut'=>'商品出库','inventoryBreakage'=>'商品报损','inventoryInit'=>'商品盘点','inventoryNoonCheck'=>"商品午间盘点");

$warehouseRegInvMethods = array('inventoryIn'=>'商品入库','inventoryOut'=>'商品出库');
$warehouseRegInvMethods_p = array('inventoryIn'=>'门店商品投篮(商品入库)');
$warehouseRegInvMethods_s = array('addProductPlanToInv'=>'确认生产完成');
$stationRegInvMethods = array('inventoryBreakage'=>'商品报损','inventoryInit'=>'商品盘点');
$partTimeRegInvMethods = array('inventoryNoonCheck'=>"商品午间盘点");

$shelfLifeStrict = "1077,1133,1134,1137,1138,1139,1140,1141,1142,1143,1144,1145,1146,1181,1427,1431,1433,1435,1436,1481,1482,1483,1484,1485,1486,1487,1488,1556,1557,1558,1559,1560,1561,1562,1563,1564,1565,1566,1567,1571,1572,1573,1574,1575,1576,1577";

$id = isset($_POST['id']) ? $_POST['id'] : 0;
$station_id = isset($_POST['station']) ? $_POST['station'] : 0;
$products = isset($_POST['products']) ? $_POST['products'] : 0;
$method = isset($_POST['method']) ? $_POST['method'] : 0;
$purchase_plan_id = isset($_POST['purchase_plan_id']) ? $_POST['purchase_plan_id'] : 0;

$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;

$uid = 'of_WZtxNqXfuXadWCN6jAFinDU8o';





if($method != "inventory_login"){
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
}

if($method == "inventory_logout"){
    $return = array();
    
    setcookie("inventory_user","",time()-2*3600);

    exit(json_encode($return));
}


if($method == 'getOrders'){
    //$userInfo = ('getUserInfoByUid', 0, '', 0) //Get
    //exit(json_encode($userInfo));

    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    $data['date'] = $date;   
    $data['order_status_id'] = isset($_POST['order_status_id']) ? (int)$_POST['order_status_id'] : 0;
    $data['station_id'] = isset($_POST['station_id']) ? (int)$_POST['station_id'] : 0; //New

    $data['inventory_user'] = isset($_POST['inventory_user']) && $_POST['inventory_user']  ? $_POST['inventory_user'] : 0;
    $data['warehouse_id'] = isset($_POST['warehouse_id']) && $_POST['warehouse_id']  ? $_POST['warehouse_id'] : 0;
    $data['warehouse_repack'] = isset($_POST['warehouse_repack']) && $_POST['warehouse_repack']  ? $_POST['warehouse_repack'] : 0;
    $data['user_repack'] = isset($_POST['user_repack']) && $_POST['user_repack']  ? $_POST['user_repack'] : 0;
    $data['psize'] = isset($_POST['psize']) && $_POST['psize']  ? $_POST['psize'] : 0;
    $data['start_row'] = isset($_POST['start_row']) && $_POST['start_row']  ? $_POST['start_row'] : 0;
    $data['order_type'] = isset($_POST['order_type']) && $_POST['order_type']  ? $_POST['order_type'] : 0;
    $data['old_deliver_order_id'] = isset($_POST['old_deliver_order_id']) && $_POST['old_deliver_order_id']  ? $_POST['old_deliver_order_id'] : 0;
    //$data['orderList'] = isset($_POST['orderList']) && sizeof($_POST['orderList']) ? $_POST['orderList'] : array(0); //New
    
    $orders = init('getOrders', $id, json_encode($data), 1); //Get Stations
    
    foreach($orders['data'] as $key=>$value){
        if($value['added_by'] && $value['added_by']!=$_COOKIE['inventory_user']){
            //$orders['data'][$key]['no_inv'] = 1;
        }
    }

    //echo "<pre>";print_r($orders);exit;
    exit(json_encode($orders));
}


if($method == 'getPurchaseOrders'){
    //$userInfo = ('getUserInfoByUid', 0, '', 0) //Get
    //exit(json_encode($userInfo));

    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    $data['date_end'] = !empty($_POST['date_end']) ? $_POST['date_end'] : '';
    $data['date'] = $date;
    $data['order_status_id'] = $_POST['order_status_id'];
    $data['purchase_order_id'] = !empty($_POST['purchase_order_id']) ? $_POST['purchase_order_id'] : '';
    $data['warehouse_id'] = $_POST['warehouse_id'];
    $data['handle_product'] = $_POST['handle_product'];
    $orders = init('getPurchaseOrders', $id, json_encode($data), 1); //Get Stations
    
    foreach($orders['data'] as $key=>$value){
        if($value['added_by'] && $value['added_by']!=$_COOKIE['inventory_user']){
            //$orders['data'][$key]['no_inv'] = 1;
        }
    }

    //echo "<pre>";print_r($orders);exit;
    exit(json_encode($orders));
}

if($method == 'getProductWeightInfo'){
    //$userInfo = ('getUserInfoByUid', 0, '', 0) //Get
    //exit(json_encode($userInfo));
    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    $data = array();
        
    $data['date'] = $date;  
    
    $product_weight_info = init($method, $id, json_encode($data), 1); //Get Stations
   
    exit(json_encode($product_weight_info));
}
if($method == 'getInventoryUserOrder'){
    //$userInfo = ('getUserInfoByUid', 0, '', 0) //Get
    //exit(json_encode($userInfo));

    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    $data['date'] = $date;        
    $data['order_status_id'] = isset($_POST['order_status_id']) ? (int)$_POST['order_status_id'] : 0;
    $data['warehouse_id'] = $_POST['warehouse_id'];
    $data['inventory_user'] = $_COOKIE['inventory_user'];
    $data['warehouse_repack'] = $_POST['warehouse_repack'];
    $data['user_repack'] = $_POST['user_repack'];
    $data['warehouse_id'] = $_POST['warehouse_id'];

    
    $orders = init($method, $id, json_encode($data), 1); //Get Stations
    
    $inventory_orders = array();
    if(!empty($orders['data'])){
        foreach($orders['data'] as $key=>$value){
            $inventory_orders[$value['order_id']][$value['ordclass']] = $value['quantity'];
        
        }
    }
    

    //echo "<pre>";print_r($orders);exit;
    exit(json_encode($inventory_orders));
}

if($method == 'ordered'){
    //$userInfo = ('getUserInfoByUid', 0, '', 0) //Get
    //exit(json_encode($userInfo));

    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    $data['order_status_id'] = isset($_POST['order_status_id']) ? $_POST['order_status_id'] : '';
    $data['product_id'] = isset($_POST['product_id']) ? $_POST['product_id'] : '';
    $data['deliver_date'] = !empty($_POST['deliver_date']) ? $_POST['deliver_date'] :$date;
    $data['warehouse_id'] = $_POST['warehouse_id'];
    $data['warehouse_repack'] = $_COOKIE['warehouse_repack'];
    $data['user_repack'] = $_COOKIE['user_repack'];
    $data['user_warehouse_id'] = $_POST['user_warehouse_id'];

    $orders = init('ordered', $id, json_encode($data), 1); //Get Stations

   
    //echo "<pre>";print_r($orders);exit;
    exit(json_encode($orders));
}


if($method == 'getOrderss'){
    //$userInfo = ('getUserInfoByUid', 0, '', 0) //Get
    //exit(json_encode($userInfo));

   // $date = !empty($_POST['date']) ? $_POST['date'] : '';
   // $data['date'] = $date;   
    $data['order_status_id'] = $_POST['order_status_id'];
    $data['product_id'] = $_POST['product_id'];
    $data['deliver_date'] = $_POST['deliver_date'];
    $data['area_id_list'] = $_POST['area_id_list'];
    $data['warehouse_id'] = $_POST['warehouse_id'];
    $data['new_warehouse_id'] = $_POST['new_warehouse_id'];
    $data['warehouse_repack'] = $_POST['warehouse_repack'];
    $data['user_repack'] = $_POST['user_repack'];
    $data['deliver_order_repack'] = $_POST['deliver_order_repack'];
    $data['user_warehouse_id'] = $_POST['user_warehouse_id'];
    $orders = init('getOrderss', $id, json_encode($data), 1); //Get Stations
    
    

    exit(json_encode($orders));
}

if($method == 'orderdistr'){
	$date = !empty($_POST['date']) ? $_POST['date'] : '';
    $data['date'] = $date;   
    $data['order_id'] = $_POST['order_id'];
	$data['inventory_name'] = $_POST['inventory_name'];	
	$data['add_user_name_id'] = $_COOKIE['inventory_user_id'];
	$data['product_id'] = $_POST['product_id'];
    $data['warehouse_id'] = $_POST['warehouse_id'];
    $data['warehouse_repack'] = $_POST['warehouse_repack'];
    $data['user_repack'] = $_POST['user_repack'];
	$orders = init('orderdistr', $id, json_encode($data), 1); //Get Stations
    
    foreach($orders['data'] as $key=>$value){
        if($value['added_by'] && $value['added_by']!=$_COOKIE['inventory_user']){
            //$orders['data'][$key]['no_inv'] = 1;
        }
    }

    //echo "<pre>";print_r($orders);exit;
    exit(json_encode($orders));
}
/*zx
自动领单*/
if($method == 'auto_order_distr'){
	$date = !empty($_POST['date']) ? $_POST['date'] : '';
    $data['date'] = $date;
    $data['order_id'] = $_POST['order_id'];
	$data['inventory_name'] = $_POST['inventory_name'];
	$data['product_id'] = $_POST['product_id'];
    $data['warehouse_id'] = $_POST['warehouse_id'];
    $data['warehouse_repack'] = $_POST['warehouse_repack'];
    $data['user_repack'] = $_POST['user_repack'];
	$orders = init('auto_order_distr', $id, json_encode($data), 1); //Get Stations

    foreach($orders['data'] as $key=>$value){
        if($value['added_by'] && $value['added_by'] != $_COOKIE['inventory_user']){
            //$orders['data'][$key]['no_inv'] = 1;
        }
    }

    //echo "<pre>";print_r($orders);exit;
    exit(json_encode($orders));
}

if($method == 'orderRedistr'){
	$date = !empty($_POST['date']) ? $_POST['date'] : '';
    $data['date'] = $date;   
    $data['order_id'] = $_POST['order_id'];
    $data['ordclass'] = $_POST['ordclass'];
    $data['warehouse_repack'] = $_POST['warehouse_repack'];
    $data['user_repack'] = $_POST['user_repack'];

	$orders = init('orderRedistr', $id, json_encode($data), 1); //Get Stations
    

    //echo "<pre>";print_r($orders);exit;
    exit(json_encode($orders));
}



if($method == 'getStations'){
    //$userInfo = ('getUserInfoByUid', 0, '', 0) //Get
    //exit(json_encode($userInfo));

    $stations = init('getStation', $id, '', 1); //Get Stations
    $stationList = array();
    foreach($stations as $m){
        if($m['parent_station_id'] < 1000){
            $stationList[] = $m;
        }
    }

    exit(json_encode($stationList));
}

if($method == 'getOrderSortingList'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : exit();

    $data = array(
        'order_id' => $order_id,
        'is_view' => $_POST['is_view'],
        'warehouse_id'=> $_POST['warehouse_id'],
        'warehouse_repack' => $_COOKIE['warehouse_repack'],
        'user_repack' => $_COOKIE['user_repack'],
        'user_group_id'=>$_COOKIE['user_group_id'],
        'repack'=>$_POST['repack'],
        'frame_num'=>$_POST['frame_num'],
    );

    $sorting = init($method, $id, json_encode($data), 1); //TODO use new method 'getSortingList' instead of 'getPlannedList'

    exit(json_encode($sorting));
}
if($method == 'getOrderAreaList'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }



    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : exit();

    $data = array(
        'order_id' => $order_id,
        'is_view' => $_POST['is_view'],
        'warehouse_id'=> $_POST['warehouse_id'],
        'warehouse_transfer_area_id'=> $_POST['warehouse_transfer_area_id'],
        'warehouse_repack' => $_COOKIE['warehouse_repack'],
        'user_repack' => $_COOKIE['user_repack'],
        'repack'=>$_POST['repack'],
    );

    $sorting = init($method, $id, json_encode($data), 1); //TODO use new method 'getSortingList' instead of 'getPlannedList'

    exit(json_encode($sorting));
}
if($method == 'updateStockSectionArea'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }



    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : exit();

    $data = array(
        'order_id' => $order_id,
        'status' => $_POST['status'],
        'warehouse_id'=> $_POST['warehouse_id'],
        'product_id' => $_POST['product_id'],
        'stock_section_id' => $_POST['stock_section_id'],
        'stock_section_ids' => $_POST['stock_section_ids'],
    );

    $sorting = init($method, $id, json_encode($data), 1); //TODO use new method 'getSortingList' instead of 'getPlannedList'

    exit(json_encode($sorting));
}
if($method == 'getWarehouseTransferArea'){
    //$userInfo = ('getUserInfoByUid', 0, '', 0) //Get
    //exit(json_encode($userInfo));
    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    $data = array();
    $data['warehouse_transfer_area_id'] = isset($_POST['warehouse_transfer_area_id']) ? $_POST['warehouse_transfer_area_id'] : 0;
    $data['warehouse_id'] = isset($_POST['warehouse_id']) ? $_POST['warehouse_id'] : 0;
    $data['date'] = $date;
    $product_weight_info = init($method, $id, json_encode($data), 1); //Get Stations
    exit(json_encode($product_weight_info));
}

if($method == 'getPurchaseOrderSortingList'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }


    $warehouse_id = $_POST['warehouse_id'];
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : exit();

    $data = array(
        'order_id' => $order_id,
        'warehouse_id' => $warehouse_id,
    );

    $sorting = init($method, $id, json_encode($data), 1); //TODO use new method 'getSortingList' instead of 'getPlannedList'

    exit(json_encode($sorting));
}


if($method == 'getSortingProductInfo'){ //TODO Same as getProductInfo, but add sorting class name
    //Barcode rules for Code128(18) OR Ean13(13||12)
    //18: 6+6+6
    //12: 1+5+5+x
    //13: 2+5+5+x

    $productBarcodeRaw = explode(',',$products);

    $productBarcode = array();
    foreach($productBarcodeRaw as $m){
        if((strlen($m) == 18 || strlen($m) == 12 || strlen($m) == 13 || strlen($m)) && is_numeric($m)){ //Check and filter
            $productBarcode[] = $m;
        }
    }
    $data = array('products'=>implode(',',$productBarcode));
    $dataProdInfoRaw = init('getStationProductInfob2b', 0, json_encode($data), $station_id);

    $dataProdInfo = array();

    foreach($dataProdInfoRaw as $m){

        $dataProdInfo[$m['product_id']] = $m;
    }

    $productsInfo = array();
    foreach($productBarcode as $m){
        if(strlen($m) == 18){
            $product_id = (int)substr($m, 6, 6);
        }
        elseif(strlen($m) == 12 || strlen($m) == 13){
            $product_id = (int)substr($m, 1-(12-strlen($m)), 5);
        }
        elseif(strlen($m) <= 6){
            $product_id = $m;
        }
        else{
            $product_id = 0;
        }
        //$productsInfo[$m] = $dataProdInfo[$product_id]['inv_class'].'-'.$dataProdInfo[$product_id]['name'];
        $productsInfo[$m] = $dataProdInfo[$product_id]['name'];
        if($dataProdInfo[$product_id]['abstract']){
            $productsInfo[$m] = $dataProdInfo[$product_id]['name'].'<br /><span style="font-size:1rem">'.$dataProdInfo[$product_id]['abstract']."</span>";
        }
        //$productsInfo[$m] = $dataProdInfo[$product_id]['abstract'];
    }

    exit(json_encode($productsInfo));
}


if($method == 'addOrderProductStation'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    $product_barcode_arr = array();
    if(!empty($_POST['product_barcode_arr'])){
        foreach ($_POST['product_barcode_arr'][$product_id] as $k=>$v){
                $product_barcode_arr[] = $v;
        }
    }
    
    $data = array(
        'product_id' => $product_id,
        'order_id' => $_POST['order_id'],
        'product_quantity' => isset($_POST['product_quantity']) ? $_POST['product_quantity'] : 0 ,
        'inventory_user' => $_COOKIE['inventory_user'],
        'product_barcode_arr' => $product_barcode_arr,
        'warehouse_id' =>$_POST['warehouse_id'],
        'warehouse_repack'=>$_POST['warehouse_repack'],
        'user_repack' =>$_POST['user_repack'],
        'container_id' => $_POST['frame_vg_product'],
        'inventory_user_id' => $_COOKIE['inventory_user_id'],
        'frame_vg_list' => $_POST['frame_vg_list'],
        'frame_count' => $_POST['frame_count'],
    );

    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}






if($method == 'addPurchaseOrderProductStation'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    if(date("H:i",time()) > "21:30" && date("H:i",time()) < "21:40" ){
        $invInfo = array('msg'=>"晚上21：30 -- 21：40 不能入库",'status'=>0);
         exit(json_encode($invInfo));
    }
    
    
    $product_barcode_arr = array();
    if(!empty($_POST['product_barcode_arr'])){
        foreach ($_POST['product_barcode_arr'][$product_id] as $k=>$v){
                $product_barcode_arr[] = $v;
        }
    }
    
    $data = array(
        'product_id' => $product_id,
        'order_id' => $_POST['order_id'],
        'product_quantity' => isset($_POST['product_quantity']) ? $_POST['product_quantity'] : 0 ,
        'transfer_area' => isset($_POST['transfer_area']) ? $_POST['transfer_area'] : 0 ,
        'transfer_area_item' => isset($_POST['transfer_area_item']) ? $_POST['transfer_area_item'] : 0 ,
        'inventory_user' => $_COOKIE['inventory_user'],
        'warehouse_id'=>$_POST['warehouse_id'],
        'product_barcode_arr' => $product_barcode_arr
    );

    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'addOrderProductToInv_pre'){
    
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    $data = array(
        'station_id' => $station_id,
        'station' => array('from_station_id'=>1,'to_station_id'=>1),
        'order_id' => $_POST['order_id'],
        'warehouse_id' => $_POST['warehouse_id'],
        'go_warehouse_id' => $_POST['go_warehouse_id'],
        'timestamp' => time(),
        'warehouse_repack'=>$_COOKIE['warehouse_repack'],
        'user_repack'=>$_COOKIE['user_repack'],
        'frame_vg_list'=>$_COOKIE['frame_vg_list'],
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

if($method == 'addPurchaseOrderProductToInv_pre'){
    
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    if(date("H:i",time()) > "21:30" && date("H:i",time()) < "21:40" ){
        $invInfo = array('msg'=>"晚上21：30 -- 21：40 不能入库",'status'=>3);
         exit(json_encode($invInfo));
    }
    
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    $data = array(
        'station_id' => $station_id,
        'station' => array('from_station_id'=>1,'to_station_id'=>1),
        'order_id' => $_POST['order_id'],
        'timestamp' => time(),
        'warehouse_id' => $_POST['warehouse_id'],
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}
if($method == 'addOrderProductToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => $_POST['order_id'],
        'userPendingCheck' => isset($_POST['userPendingCheck']) ? $_POST['userPendingCheck'] : 0,
        'invComment' => isset($_POST['invComment']) ? $_POST['invComment'] : 0,
        'boxCount' => isset($_POST['boxCount']) ? $_POST['boxCount'] : 0,
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'date' => date("Y-m-d", time()),
        'warehouse_id'=>$_COOKIE['warehouse_id'],
        
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

if($method == 'addFastMoveSortingToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => $_POST['order_id'],
        'userPendingCheck' => isset($_POST['userPendingCheck']) ? $_POST['userPendingCheck'] : 0,
        'invComment' => isset($_POST['invComment']) ? $_POST['invComment'] : 0,
        'boxCount' => isset($_POST['boxCount']) ? $_POST['boxCount'] : 0,
        'frame_count' => isset($_POST['frame_count']) ? $_POST['frame_count'] : 0,
        'frame_vg_list' => isset($_POST['frame_vg_list']) ? $_POST['frame_vg_list'] : 0,
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'add_user_name_id' => $_COOKIE['inventory_user_id'],
        'date' => date("Y-m-d", time()),
        'warehouse_id' =>$_POST['warehouse_id'],
        'warehouse_repack'=>$_POST['warehouse_repack'],
        'user_repack'=>$_POST['user_repack'],
        'user_group'=>$_COOKIE['user_group_id'],


    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

if($method == 'addPurchaseOrderProductToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    if(date("H:i",time()) > "21:30" && date("H:i",time()) < "21:40" ){
        $invInfo = array('msg'=>"晚上21：30 -- 21：40 不能入库",'status'=>3);
         exit(json_encode($invInfo));
    }

    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => $_POST['order_id'],
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'date' => date("Y-m-d", time()),
        'warehouse_id'=>$_POST['warehouse_id'],
        'user_id'=>$_COOKIE['inventory_user_id'],
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}
if($method == 'updateStockSectionProduct'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    if(date("H:i",time()) > "21:30" && date("H:i",time()) < "21:40" ){
        $invInfo = array('msg'=>"晚上21：30 -- 21：40 不能入库",'status'=>3);
        exit(json_encode($invInfo));
    }

    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => $_POST['order_id'],
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user_id'],
        'date' => date("Y-m-d", time()),
        'warehouse_id'=>$_POST['warehouse_id'],
        'areanames'=>$_POST['areanames'],
        'product_ids'=>$_POST['product_ids'],
        'transfer_area'=>$_POST['transfer_area'],
        'update_qunity'=>$_POST['update_qunity'],
        'update_section'=>$_POST['update_section'],
    );
    $planned = init($method, 0, json_encode($data), $station_id);
    exit(json_encode($planned));
}

if($method == 'delOrderProductToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => $_POST['order_id'],
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'inventory_user_id' => $_COOKIE['inventory_user_id'],
        'date' => date("Y-m-d", time())
        
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}



if($method == 'delPurchaseOrderProductToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => $_POST['order_id'],
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'date' => date("Y-m-d", time())
        
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}
//删除调拨单中间表数据
if($method == 'delPurchaseOrderRelevantToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }


    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'relevant_id' => $_POST['relevant_id'],
        'warehouse_id' => $_POST['warehouse_id'],
        'date_added' => $_POST['date_added'],
        'product_id' => $_POST['product_id'],
        'container_id' => $_POST['container_id'],
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'date' => date("Y-m-d", time())


    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'addCheckProductToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => '',
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'date' => date("Y-m-d", time()),
        'warehouse_id' => $_POST['warehouse_id'],
        
    );
        
    
    
    if(date("H", time()) > 20){
        $data['date'] = date("Y-m-d", time()+24*3600);
    }
    else{
        $data['date'] = date("Y-m-d", time());
    }
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}



if($method == 'addCheckSingleProductToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => '',
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'date' => date("Y-m-d", time()),
        'sorting_id' => $_POST['sorting_id'],
        'warehouse_id' =>$_POST['warehouse_id'],
        
    );
    
    
    
    
    $data['date'] = date("Y-m-d", time());
    
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'delCheckSingleProductToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => '',
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'date' => date("Y-m-d", time()),
        'sorting_id' => $_POST['sorting_id']
        
    );
    
    
    
    if(date("H", time()) > 20){
        $data['date'] = date("Y-m-d", time()+24*3600);
    }
    else{
        $data['date'] = date("Y-m-d", time());
    }
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}



if($method == 'addVegCheckProductToInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => '',
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'date' => date("Y-m-d", time()),
        'warehouse_id' => $_POST['warehouse_id'],
    );
    
    if(date("H", time()) > 20){
        $data['date'] = date("Y-m-d", time()+24*3600);
    }
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'addOrderNum'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'station_id' => $station_id,
        'station' => array('from'=>1,'to'=>1),
        'order_id' => $_POST['order_id'],
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user'],
        'frame_count' => $_POST['frame_count'],
        'incubator_count' => $_POST['incubator_count'],
        'inv_comment' => $_POST['inv_comment'],
        'inv_spare_comment' => $_POST['inv_spare_comment'],
        'foam_count' => $_POST['foam_count'],
        'frame_mi_count' => $_POST['frame_mi_count'],
        'incubator_mi_count' => $_POST['incubator_mi_count'],
        'frame_ice_count' => $_POST['frame_ice_count'],
        'box_count' => $_POST['box_count'],
        'foam_ice_count' => $_POST['foam_ice_count'],
        'frame_meat_count' => $_POST['frame_meat_count'],
        'add_type' => $_POST['add_type'],
        'frame_vg_list' => $_POST['frame_vg_list'],
        'frame_meat_list' => $_POST['frame_meat_list'],
        'frame_mi_list' => $_POST['frame_mi_list'],
        'frame_ice_list' => $_POST['frame_ice_list'],
        'warehouse_id' => $_COOKIE['warehouse_id'] ,
        'frame_vg_product'=>$_POST['frame_vg_product'],
        'add_user_id' => $_COOKIE['inventory_user_id'],
        
    );
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}



if($method == 'addCheckFrameOut'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        
        'order_id' => $_POST['order_id'],
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user_id'],
        'frame_list' => $_POST['frame_list']
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'addCheckFrameCage'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        
        'order_id' => $_POST['order_id'],
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user_id'],
        'frame_list' => $_POST['frame_list']
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'addCheckFrameCheck'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        
        'order_id' => $_POST['order_id'],
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user_id'],
        'frame_list' => $_POST['frame_list']
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}




if($method == 'getAddedFrameOut'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        
        
        'w_user_id' => $_COOKIE['inventory_user_id']
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}
if($method == 'getAddedFrameCage'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }


    $data = array(

        
        'w_user_id' => $_COOKIE['inventory_user_id']
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'getAddedFrameCheck'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        
        
        'w_user_id' => $_COOKIE['inventory_user_id']
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'getAddedFrameIn'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        
        
        'w_user_id' => $_COOKIE['inventory_user_id']
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'addCheckFrameIn'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user_id'],
        'frame_list' => $_POST['frame_list'],
        'warehouse_id' => $_POST['warehouse_id'],

        
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

if($method == 'addReturnDeliverProduct'){

    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $dataInv = array();
    
    //删除尾部逗号

    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }

    //原始数据格式[ID]:[qty]:[boxqty],...
    //Exp.: "8921:3:18,7145:1:1,8763:1:1"
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] = array(
            'product_id' => $n[0],
            'quantity' => $n[1],
            'box_quantity' => $n[2]
        );
    }
    
    $dataInv['products'] = $productsBarcodeWithQty;
    $dataInv['timestamp'] = time();
    $dataInv['order_id'] = $_POST['order_id'];
    $dataInv['return_reason'] = $_POST['return_reason'];
    $dataInv['add_user_name'] = $_COOKIE['inventory_user_id'];
    $dataInv['warehouse_id'] = $_POST['warehouse_id'];

    //判断isBack是否设置，重要参数
    if(isset($_POST['isBack'])){
        $dataInv['isBack'] = (int)$_POST['isBack'];
    }
    else{
        $invInfo = array('msg'=>"关键参数未设置，请刷新页面",'status'=>998);
        exit(json_encode($invInfo));
    }
    $dataInv['isRepackMissing'] = isset($_POST['isRepackMissing']) ? $_POST['isRepackMissing']: 0;

    $planned = init($method, 0, json_encode($dataInv), $station_id);
    
    exit(json_encode($planned));
}
//盘点
if($method == 'submitStockChecksProduct'){

    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $dataInv = array();

    //删除尾部逗号

    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }

    //原始数据格式[ID]:[qty]:[boxqty],...
    //Exp.: "8921:3:18,7145:1:1,8763:1:1"
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] = array(
            'product_id' => $n[0],
            'quantity' => $n[1],
            'box_quantity' => $n[2]
        );
    }

    $dataInv['products'] = $productsBarcodeWithQty;
    $dataInv['timestamp'] = time();
    $dataInv['add_user_name'] = $_COOKIE['inventory_user_id'];
    $dataInv['warehouse_id'] = $_POST['warehouse_id'];
    $dataInv['changeStockCheckProduct'] = $_POST['changeStockCheckProduct'];
    $dataInv['pallet_number'] = $_POST['pallet_number'];
    $dataInv['warehouse_section_id'] = $_POST['warehouse_section_id'];
    $dataInv['inventory_user_id'] = $_POST['inventory_user_id'];
    $dataInv['add'] = $_POST['add'];

    $planned = init($method, 0, json_encode($dataInv), $station_id);

    exit(json_encode($planned));
}


if($method == 'addReturnDeliverBadProduct'){

    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $dataInv = array();

    //删除尾部逗号

    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }

    //原始数据格式[ID]:[qty]:[boxqty],...
    //Exp.: "8921:3:18,7145:1:1,8763:1:1"
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] = array(
            'product_id' => $n[0],
            'quantity' => $n[1],
            'box_quantity' => $n[2]
        );
    }

    $dataInv['products'] = $productsBarcodeWithQty;
    $dataInv['timestamp'] = time();
    $dataInv['order_id'] = $_POST['order_id'];
    $dataInv['return_reason'] = $_POST['return_reason'];
    $dataInv['add_user_name'] = $_COOKIE['inventory_user_id'];
    $dataInv['warehouse_id'] = $_POST['warehouse_id'];

    //判断isBack是否设置，重要参数
    if(isset($_POST['isBack'])){
        $dataInv['isBack'] = (int)$_POST['isBack'];
    }
    else{
        $invInfo = array('msg'=>"关键参数未设置，请刷新页面",'status'=>998);
        exit(json_encode($invInfo));
    }
    $dataInv['isRepackMissing'] = isset($_POST['isRepackMissing']) ? $_POST['isRepackMissing']: 0;

    $planned = init($method, 0, json_encode($dataInv), $station_id);

    exit(json_encode($planned));
}






if($method == 'checkFrameCanIn'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
     if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    
    $data = array(
        
        'timestamp' => time(),
        'add_user_name' => $_COOKIE['inventory_user_id'],
        'frame_list' => $_POST['frame_list'],
        'warehouse_id' => $_POST['warehouse_id']
        
    );
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}




if($method == 'getSkuProductInfo'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    $data = array(
        'sku' => $_POST['sku'],
        'warehouse_id'=> $_POST['warehouse_id']
    );
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

//仓库盘点
if($method == 'getStockChecksProductInfo'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';

    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'sku' => $_POST['sku'],
        'warehouse_id'=> $_POST['warehouse_id']
    );

    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}



if($method == 'changeProductSection'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';

    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'productId' => $_POST['productId'],
        'productSection' => isset($_POST['productSection'])?$_POST['productSection']:'',
        'productBarCode' => isset($_POST['productBarCode'])?$_POST['productBarCode']:'',
        'inventory_user' => $_COOKIE['inventory_user'],
        'warehouse_id' =>  $_POST['warehouse_id'],

    );

    $return = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($return));
}

if($method == 'getProductSectionInfo'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';

    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'productSection' => isset($_POST['productSection'])?$_POST['productSection']:'',
        'warehouse_id' => $_POST['warehouse_id'],
    );

    $return = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($return));
}

if($method == 'getSkuProductInfoInv'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $warehouse_id = $_POST['warehouse_id'] ? $_POST['warehouse_id'] : false;
    if(!$warehouse_id){
        exit(json_decode(array('status'=>0, 'msg'=>'Warehouse ID is missing.')));
    }

    $data = array(
        'sku' => $_POST['sku'],
        'product_id' => isset($_POST['product_id']) ? $_POST['product_id'] : 0,
        'warehouse_id' => $warehouse_id
    );
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'inventoryInProduct'){
    
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    if(date("H:i",time()) > "21:30" && date("H:i",time()) < "21:40" ){
        $invInfo = array('msg'=>"晚上21：30 -- 21：40 不能入库",'status'=>0);
         exit(json_encode($invInfo));
    }
    
    
    $dataInv = array();
    if($method == 'inventoryInProduct'){
        $dataInv['station']=array('from'=>1,'to'=>1);
        $dataInv['purchase_plan_id'] = $purchase_plan_id; // To Handle Purchase Plan
        $dataInv['add_user_name'] = $_COOKIE['inventory_user'];
    }
    
    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }
    
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] += $n[1];
        
        if($n[0] < 7100 || ($n[0] >= 7403 && $n[0] <= 8567) || ($n[0] >= 9003 && $n[0] <= 9009)){
            $invInfo = array('msg'=>"奶制品/日化品需在新入库页面操作，请知悉",'status'=>0);
            exit(json_encode($invInfo));
    }
    }
    
    $dataInv['products'] = $productsBarcodeWithQty;

    $dataInv['timestamp'] = time();
    
    $status = init($method, 0, json_encode($dataInv), 1);

    if($status[0]['status']){
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]完成",'status'=>1);
    }
    else{
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]出错",'status'=>0);
    }

    exit(json_encode($invInfo));
}

if($method == 'inventoryOutProduct'){
    
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    $dataInv = array();
    if($method == 'inventoryOutProduct'){
        $dataInv['station']=array('from'=>1,'to'=>1);
        $dataInv['purchase_plan_id'] = $purchase_plan_id; // To Handle Purchase Plan
        $dataInv['add_user_name'] = $_COOKIE['inventory_user'];
        $dataInv['warehouse_id'] = $_POST['warehouse_id'];
    }
    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    $productsBarcodeWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] += $n[1];
    }
    
    $dataInv['products'] = $productsBarcodeWithQty;

    $dataInv['timestamp'] = time();
    
    $status = init($method, 0, json_encode($dataInv), 1);

    if($status[0]['status']){
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]完成",'status'=>1);
    }
    else{
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]出错",'status'=>0);
    }

    exit(json_encode($invInfo));
}


if($method == 'inventoryAdjustProduct'){
    
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    $dataInv = array();
    if($method == 'inventoryAdjustProduct'){
        $dataInv['station']=array('from'=>1,'to'=>1);
        $dataInv['purchase_plan_id'] = $purchase_plan_id; // To Handle Purchase Plan
        $dataInv['add_user_name'] = $_COOKIE['inventory_user'];
    }
    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] += $n[1];
    }
    
    $dataInv['products'] = $productsBarcodeWithQty;

    $dataInv['timestamp'] = time();
    
    $status = init($method, 0, json_encode($dataInv), 1);

    if($status[0]['status']){
        if($status[0]['status'] == 3){
            $invInfo = array('msg'=>"[".$regInvMethods[$method]."]完成",'status'=>3);
        }
        else{
            $invInfo = array('msg'=>"[".$regInvMethods[$method]."]完成",'status'=>1);
        
        }
    }
    else{
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]出错",'status'=>0);
    }

    exit(json_encode($invInfo));
}


if($method == 'inventoryChangeProduct'){
    
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    $dataInv = array();
    if($method == 'inventoryChangeProduct'){
        $dataInv['station']=array('from'=>1,'to'=>1);
        $dataInv['purchase_plan_id'] = $purchase_plan_id; // To Handle Purchase Plan
        $dataInv['add_user_name'] = $_COOKIE['inventory_user'];
        $dataInv['warehouse_id'] = $_POST['warehouse_id'];
    }
    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] += $n[1];
    }
    
    $dataInv['products'] = $productsBarcodeWithQty;

    $dataInv['timestamp'] = time();
    
    $status = init($method, 0, json_encode($dataInv), 1);

    if($status[0]['status']){
        if($status[0]['status'] == 2){
            $invInfo = array('msg'=>"[".$regInvMethods[$method]."]完成",'status'=>2,'product_id'=>$status[0]['product_id']);
        }
        else{
            $invInfo = array('msg'=>"[".$regInvMethods[$method]."]完成",'status'=>1);
        
        }
    }
    else{
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]出错",'status'=>0);
    }

    exit(json_encode($invInfo));
}



if($method == 'inventoryCheckProduct'){
    
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    $dataInv = array();
    if($method == 'inventoryCheckProduct'){
        $dataInv['station']=array('from'=>1,'to'=>1);
        $dataInv['purchase_plan_id'] = $purchase_plan_id; // To Handle Purchase Plan
        $dataInv['add_user_name'] = $_COOKIE['inventory_user'];
        $dataInv['warehouse_id'] = $_POST['warehouse_id'];
    }

    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] += $n[1];
    }
    
    $dataInv['products'] = $productsBarcodeWithQty;

    $dataInv['timestamp'] = time();
    
    $status = init($method, 0, json_encode($dataInv), 1);

    if($status[0]['status']){
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]完成",'status'=>1);
    }
    else{
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]出错",'status'=>0);
    }

    exit(json_encode($invInfo));
}


if($method == 'inventoryCheckSingleProduct'){
    
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    $dataInv = array();
    if($method == 'inventoryCheckSingleProduct'){
        $dataInv['station']=array('from'=>1,'to'=>1);
        $dataInv['purchase_plan_id'] = $purchase_plan_id; // To Handle Purchase Plan
        $dataInv['add_user_name'] = $_COOKIE['inventory_user'];
        $dataInv['warehouse_id'] = $_POST['warehouse_id'];
    }
    
    
    
    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] += $n[1];
    }
    
    $dataInv['products'] = $productsBarcodeWithQty;

    
    $prodListWithQty_inv = $_POST['prodListWithQty_inv'];
    if(substr($prodListWithQty_inv, strlen($prodListWithQty_inv)-1,1) == ','){
        $prodListWithQty_inv = substr($prodListWithQty_inv, 0,-1);
    }
    $prodListWithQty_inv_raw = explode(',',$prodListWithQty_inv);

    $productsWithQty_inv_arr = array();
    foreach($prodListWithQty_inv_raw as $m){
        $n = explode(':',$m);
        $productsWithQty_inv_arr[$n[0]] += $n[1];
    }
    
    $dataInv['products_inv'] = $productsWithQty_inv_arr;
    
    
    $dataInv['date'] = date("Y-m-d", time());
    
    $dataInv['timestamp'] = time();
    $dataInv['remark'] = $_POST['remark'];
    $dataInv['remark_2'] = $_POST['remark_2'];
    
    
    
    $status = init($method, 0, json_encode($dataInv), 1);

    if($status[0]['status']){
        
        if($status[0]['status'] == 5){
            $invInfo = array('msg'=>"此商品还有未处理的盘盈盘亏操作，请先处理",'status'=>5);
        }
        else{
            $invInfo = array('msg'=>"[".$regInvMethods[$method]."]完成",'status'=>1);
        }
    }
    else{
        
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]出错",'status'=>0);
    }

    exit(json_encode($invInfo));
}


if($method == 'inventoryVegCheckProduct'){
    
    if(empty($_COOKIE['inventory_user'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    $dataInv = array();
    if($method == 'inventoryVegCheckProduct'){
        $dataInv['station']=array('from'=>1,'to'=>1);
        $dataInv['purchase_plan_id'] = $purchase_plan_id; // To Handle Purchase Plan
        $dataInv['add_user_name'] = $_COOKIE['inventory_user'];
    }
    if(substr($products, strlen($products)-1,1) == ','){
        $products = substr($products, 0,-1);
    }
    $productsBarcodeWithQtyRaw = explode(',',$products);

    $productsWithQty = array();
    foreach($productsBarcodeWithQtyRaw as $m){
        $n = explode(':',$m);
        $productsBarcodeWithQty[$n[0]] += $n[1];
    }
    
    $dataInv['products'] = $productsBarcodeWithQty;

    $dataInv['timestamp'] = time();
    $dataInv['product_barcode_arr'] = $_POST['product_barcode_arr'];
    
    
    foreach($dataInv['product_barcode_arr'] as $key=>$value){
        $dataInv['product_barcode_arr'][$key] = array_keys($value);
    } 
    
    foreach($dataInv['product_barcode_arr'] as $key=>$value){
        foreach($value as $b_key => $b_value){
            $dataInv['product_barcode_arr'][$key][$b_key] = (string)$b_value;
        }
    } 
    
    
    
    $status = init($method, 0, json_encode($dataInv), 1);

    if($status[0]['status']){
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]完成",'status'=>1);
    }
    else{
        $invInfo = array('msg'=>"[".$regInvMethods[$method]."]出错",'status'=>0);
    }

    exit(json_encode($invInfo));
}


if($method == 'inventory_login'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    $username = $_POST['username'];
    $password = $_POST['password'];
    $warehouse_id = $_POST['warehouse_id'];
    $data = array(
        "username" => $username,
        "password" => $password,
        "warehouse_id"=>$warehouse_id,
    );

    $return = init($method, 0, json_encode($data), $station_id);
    //  $return = init($method, 0, json_encode($data), $station_id);
    if($return['status'] == 1){
        exit(json_encode($return));
    }else{
        $cookie_time = time()+2*3600+8*3600;
        setcookie("inventory_user",$return['user']['username'],$cookie_time);
        setcookie("inventory_user_id",$return['user']['user_id'],$cookie_time);
        setcookie("warehouse_id",$return['user']['warehouse_id'],$cookie_time);
        setcookie("warehouse_title",$return['user']['title'],$cookie_time);
        setcookie("user_group_id" , $return['user']['user_group_id'],$cookie_time);
        setcookie("warehouse_repack" , $return['user']['warehouse_repack'],$cookie_time);
        setcookie("user_repack" , $return['user']['user_repack'],$cookie_time);
        setcookie("to_warehouse_id" , $return['user']['to_warehouse_id'],$cookie_time);
        setcookie("warehouse_is_dc" , $return['user']['is_dc'],$cookie_time);
        exit(json_encode($return));
    }

}



if($method == 'getInventoryIn'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
   
    
    $data = array(
        'date' => $_POST['getdate'] ? $_POST['getdate'] : date("Y-m-d", time())
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

if($method == 'getAddedReturnDeliverProduct'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
   if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }
    
    $data = array(
        'date' => isset($_POST['searchDate'])? date('Y-m-d', strtotime($_POST['searchDate'])) :date("Y-m-d", time()),
        'add_user_name' => $_COOKIE['inventory_user_id'],
        'isBack' => isset($_POST['isBack'])?(int)$_POST['isBack']:0,
        'isRepackMissing' => isset($_POST['isRepackMissing']) ? (int)$_POST['isRepackMissing']: 0,
        'warehouse_id' => $_POST['warehouse_id'],
        'product_id' => $_POST['product_id'],
        'isReturnShelves'=>$_POST['isReturnShelves'],
        'user_group_id'=>$_POST['user_group_id'],
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

//盘点
if($method == 'getAddedStcokChecksProduct'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'date' => isset($_POST['searchDate'])? date('Y-m-d', strtotime($_POST['searchDate'])) :date("Y-m-d", time()),
        'add_user_name' => $_COOKIE['inventory_user_id'],
        'warehouse_id' => $_POST['warehouse_id'],
//        'product_id' => $_POST['product_id'],
        'pallet_number' => $_POST['pallet_number'],
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

if($method == 'getAddedBadReturnDeliverProduct'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'date' => isset($_POST['searchDate'])? date('Y-m-d', strtotime($_POST['searchDate'])) :date("Y-m-d", time()),
        'add_user_name' => $_COOKIE['inventory_user_id'],
        'isBack' => isset($_POST['isBack'])?(int)$_POST['isBack']:0,
        'isRepackMissing' => isset($_POST['isRepackMissing']) ? (int)$_POST['isRepackMissing']: 0,
        'warehouse_id' => $_POST['warehouse_id'],
        'product_id' => $_POST['product_id'],

    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

if($method == 'disableReturnDeliverProduct'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
   if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'date' => date("Y-m-d", time()),
        'add_user_id' => $_COOKIE['inventory_user_id'],
        'return_deliver_product_id' => isset($_POST['return_deliver_product_id'])?(int)$_POST['return_deliver_product_id']:0
    );
    $data = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($data));
}

//报损当面
if($method == 'disableReturnBadDeliverProduct'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'date' => date("Y-m-d", time()),
        'add_user_id' => $_COOKIE['inventory_user_id'],
        'return_deliver_product_id' => isset($_POST['return_deliver_product_id'])?(int)$_POST['return_deliver_product_id']:0
    );
    $data = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($data));
}

if($method == 'confirmReturnDeliverProduct'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    if(empty($_COOKIE['inventory_user_id'])){
        $invInfo = array('msg'=>"未登录，请登录后操作",'status'=>999);
        exit(json_encode($invInfo));
    }

    $data = array(
        'date' => isset($_POST['searchDate'])? date('Y-m-d', strtotime($_POST['searchDate'])) :date("Y-m-d", time()),
        'add_user_id' => $_COOKIE['inventory_user_id'],
        'isBack' => isset($_POST['isBack'])?(int)$_POST['isBack']:0,
        'isRepackMissing' => isset($_POST['isRepackMissing']) ? (int)$_POST['isRepackMissing']: 0,
        'warehouse_id' => $_POST['warehouse_id'],
    );
    $return = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($return));
}

if($method == 'getInventoryOut'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
   
    
    $data = array(
        'date' => date("Y-m-d", time()),
        'warehouse_id' => $_POST['warehouse_id'] ? $_POST['warehouse_id'] : 0
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}
if($method == 'getInventoryAdjust'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
    $data = array(
        'date' => date("Y-m-d", time())
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}
if($method == 'getInventoryChange'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
   
    
    $data = array(
        'date' => date("Y-m-d", time())
    );
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}
if($method == 'getInventoryCheck'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
   
    
    if(date("H", time()) > 20){
    $data = array(
            'date' => date("Y-m-d", time()+24*3600)
        );
    }
    else{
        $data = array(
        'date' => date("Y-m-d", time())
    );
    }
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}

if($method == 'getInventoryCheckSingle'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';

    $warehouse_id = $_POST['warehouse_id'] ? $_POST['warehouse_id'] : false;

    if(!$warehouse_id){
        exit(json_decode(array('status'=>0, 'msg'=>'Warehouse ID is missing.')));
    }

    $data = array(
        'date' => $_POST['getdate'] ? $_POST['getdate'] : date("Y-m-d", time()),
        'warehouse_id' => $warehouse_id
    );
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}
//根据日期查询盘盈盘亏结果
if($method == 'getinventoryCheckSingleDate'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';

    $warehouse_id = $_POST['warehouse_id'] ? $_POST['warehouse_id'] : false;

    if(!$warehouse_id){
        exit(json_decode(array('status'=>0, 'msg'=>'Warehouse ID is missing.')));
    }

    $data = array(
        'date' => $_POST['getdate'] ? $_POST['getdate'] : date("Y-m-d", time()),
        'warehouse_id' => $warehouse_id
    );

    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}





if($method == 'getInventoryVegCheck'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
   
    if(date("H", time()) > 20){
    $data = array(
            'date' => date("Y-m-d", time()+24*3600)
        );
    }
    else{
        $data = array(
        'date' => date("Y-m-d", time())
    );
    }
    
    
    $planned = init($method, 0, json_encode($data), $station_id);

    exit(json_encode($planned));
}


if($method == 'getOrderStatus'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';
   
    
    $data = array(
        
    );
    $planned = init($method, 0, json_encode($data), $station_id);
    //$planned = array();
    exit(json_encode($planned));
}



if($method == 'getPurchaseOrderStatus'){
    //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"0"}';

    $warehouse_id = $_POST['data']['warehouse_id'] ? $_POST['data']['warehouse_id'] : false;
    $stock_section_type_id = $_POST['data']['stock_section_type_id'] ? $_POST['data']['stock_section_type_id'] : false;
    if(!$warehouse_id){
        exit(json_decode(array('status'=>0, 'msg'=>'Warehouse ID is missing.')));
    }
    $data = array(
        'warehouse_id' => $warehouse_id,
        'stock_section_type_id' => $stock_section_type_id,
    );
    $planned = init($method, 0, json_encode($data), $station_id);
    //$planned = array();
    exit(json_encode($planned));
}

if($method == 'getAreaList'){
    $post= $_POST;
    rpcRequest($post);
}



exit();
?>