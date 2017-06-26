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



error_reporting(E_ALL);
ini_set('display_errors',1);



//Get Modules Object, TODO, repalce with loader
require_once (DIR_MODULE.'/product.php');
require_once (DIR_MODULE.'/customer.php');
require_once (DIR_MODULE.'/order.php');
require_once (DIR_MODULE.'/common.php');
require_once (DIR_MODULE.'/checkout.php');
require_once (DIR_MODULE.'/cart.php');
require_once (DIR_MODULE.'/inventory.php');
require_once (DIR_MODULE.'/customer.php');
require_once (DIR_MODULE.'/promotion.php');
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



        //FOR PRODUCT LIST 商品列表[DEBUG_CODE:11XYYY, 10接口类型；X为消息类型0,1,2错误，正常，未知消息；YYY消息定义]
        //1、获取菜单对应商品列表及商品信息（图片路径，名称，简介，原价，销售价，价格有效期，排序，是否促销商品［只可购买一件］）
        //2、获取商品详情（同上，商品详细描述）
        $services['soa.getCategoryProduct'] = array(
            'function' => 'soaFunctions::getCategoryProduct',
            'docstring' => 'Get Category, getCategoryProduct($id, $station_id=1, $language_id=2, $origin_id, $key), here id is category id, if it is also a parent id, then dig them up',
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

        $services['soa.getOnSaleProduct'] = array(
            'function' => 'soaFunctions::getOnSaleProduct',
            'docstring' => 'getOnSaleProduct($id, $station_id=1, $language_id=2, $origin_id, $key), here id is category id, if it is also a parent id, then dig them up',
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

        $services['soa.getProduct'] = array(
            'function' => 'soaFunctions::getProduct',
            'docstring' => 'Get Category, getProduct($id, $station_id=1, $language_id=2, $origin_id, $key)',
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

        $services['soa.getProducts'] = array(
            'function' => 'soaFunctions::getProducts',
            'docstring' => 'Get Category, getProducts($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Product IDs, 3,4,5,6
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getParentCategoryProduct'] = array(
            'function' => 'soaFunctions::getParentCategoryProduct',
            'docstring' => 'getParentCategoryProduct($id, $station_id=1, $language_id=2, $origin_id, $key)',
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

        $services['soa.getStationProduct'] = array(
            'function' => 'soaFunctions::getStationProduct',
            'docstring' => 'getStationProduct($id, $station_id=1, $language_id=2, $origin_id, $key)',
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

        $services['soa.getPromProduct'] = array(
            'function' => 'soaFunctions::getPromProduct',
            'docstring' => 'Get Category, getPromProduct($id, $station_id=1, $language_id=2, $origin_id, $key), here id is category id, if it is also a parent id, then dig them up',
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


        //FOR SHOPPING CART 购物车 [DEBUG_CODE:12XYYY, 10接口类型；X为消息类型0,1,2错误，正常，未知消息；YYY消息定义]
        //1、用户(Session)购物车信息，购物车商品加减，REDIS缓存
        //TODO 2、促销信息［买增，满减］getPromotion
        //TODO 3、优惠券信息［待定可选］getCoupon
        //TODO 4、购物车合计 getCartTotal
        //TODO 5、检查购物车数据，列出缺货无法配送的商品，无法更改数量的赠品，自动添加无法删除的赠品 checkCart
        $services['soa.getCart'] = array(
            'function' => 'soaFunctions::getCart',
            'docstring' => 'getCart($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //array($customer_id, $uid), serialized
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.delCart'] = array(
            'function' => 'soaFunctions::delCart',
            'docstring' => 'delCart($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //array($customer_id, $uid), serialized
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getContainersInfo'] = array(
            'function' => 'soaFunctions::getContainersInfo',
            'docstring' => 'getContainersInfo($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //array($customer_id, $uid), serialized
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        

        $services['soa.addCart'] = array(
            'function' => 'soaFunctions::addCart',
            'docstring' => 'addCart($data=serialize(array($customer_id, $product_id)), $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //array($customer_id, $product_id), serialized
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.updateCart'] = array(
            'function' => 'soaFunctions::updateCart',
            'docstring' => 'updateCart($data=serialize(array($customer_id, $product_id, $option=1, $qty=1, $limit=99)), $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //array($customer_id, $product_id, $option=1, $qty=1, $limit=99), serialized
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


        //FOR CHECKOUT 结算 [DEBUG_CODE:13XYYY, 10接口类型；X为消息类型0,1,2错误，正常，未知消息；YYY消息定义]
        //1、列出配送日期 getShippingDate
        //2、列出配送方式［目前只提供自提点］getPickupSpot
        //3、获取配送地址及联系人信息［老用户获取配送地址列表，可以选择；新用户需要验证手机，手机号作为用户名，自动绑定微信，无需登陆］
        //4、获取支付方式［微信支付，余额支付］
        //TODO 5、列出订单明细［只读］，订单合计金额，计算运费
        //TODO 6、提交订单检测，提交，计算余额

        $services['soa.getShippingDate'] = array(
            'function' => 'soaFunctions::getShippingDate',
            'docstring' => 'getShippingDate($customer_id, $station_id=1, $language_id=2, $origin_id, $key)',
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

        $services['soa.getPickupSpot'] = array(
            'function' => 'soaFunctions::getPickupSpot',
            'docstring' => 'getPickupSpot($customer_id, $station_id=1, $language_id=2, $origin_id, $key)',
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

        //$services['soa.getCustomerShippingAddress'] GOTO My Account part

        $services['soa.getPaymentMethod'] = array(
            'function' => 'soaFunctions::getPaymentMethod',
            'docstring' => 'getPaymentMethod($customer_id, $station_id=1, $language_id=2, $origin_id, $key)',
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

        $services['soa.addOrder'] = array(
            'function' => 'soaFunctions::addOrder',
            'docstring' => 'addOrder($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Order Items
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.addOrderTotal'] = array(
            'function' => 'soaFunctions::addOrderTotal',
            'docstring' => 'addOrderTotal($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Order Items
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getOrderTotal'] = array(
            'function' => 'soaFunctions::getOrderTotal',
            'docstring' => 'getOrderTotal($id, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Order id, must
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getOrderProduct'] = array(
            'function' => 'soaFunctions::getOrderProduct',
            'docstring' => 'getOrderProduct($id, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Order id, must
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getOrderTotalDetail'] = array(
            'function' => 'soaFunctions::getOrderTotalDetail',
            'docstring' => 'getOrderTotalDetail($id, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Order id, must
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getOrderByCustomer'] = array(
            'function' => 'soaFunctions::getOrderByCustomer',
            'docstring' => 'getOrderByCustomer($id, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Customer id, must
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.customerCancelOrder'] = array(
            'function' => 'soaFunctions::customerCancelOrder',
            'docstring' => 'customerCancelOrder($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Order Items
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getCustomerCredit'] = array(
            'function' => 'soaFunctions::getCustomerCredit',
            'docstring' => 'getCustomerCredit($id, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Customer id, must
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getCustomerLimitedProductId'] = array(
            'function' => 'soaFunctions::getCustomerLimitedProductId',
            'docstring' => 'getCustomerLimitedProductId($id, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcInt, //Customer id, must
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );




        //FOR MY ACCOUNT 我的账户 [DEBUG_CODE:14XYYY, 10接口类型；X为消息类型0,1,2错误，正常，未知消息；YYY消息定义]
        //TODO 1、账户安全，更改密码、更换手机［验证］
        //TODO 2、余额明细
        //3、配送地址列表［最多10个常用地址］
        //TODO 4、订单列表
        //TODO 5、订单详情

        $services['soa.getCustomerInfo'] = array(
            'function' => 'soaFunctions::getCustomerInfo',
            'docstring' => 'getCustomerInfo($id, $station_id=1, $language_id=2, $origin_id, $key), here $id=customer_id, it is required.',
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

        $services['soa.getCustomerInfoByUid'] = array(
            'function' => 'soaFunctions::getCustomerInfoByUid',
            'docstring' => 'getCustomerInfoByUid($uid, $station_id=1, $language_id=2, $origin_id, $key), $uid is required.',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Vars of Data, UID - WeChat
                    $xmlrpcInt, //Language ID
                    $xmlrpcInt, //Station ID
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.getCustomerShippingAddress'] = array(
            'function' => 'soaFunctions::getCustomerShippingAddress',
            'docstring' => 'getCustomerShippingAddress($id, $station_id=1, $language_id=2, $origin_id, $key), here $id=customer_id, it is required.',
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

        //Update Customer Scan Event
        $services['soa.addWXScan'] = array(
            'function' => 'soaFunctions::addWXScan',
            'docstring' => 'addWXScan($data, $station_id=1, $language_id=2, $origin_id, $key)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Order Items
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

        $services['soa.register'] = array(
            'function' => 'soaFunctions::register',
            'docstring'=> 'register($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.login'] = array(
            'function' => 'soaFunctions::login',
            'docstring'=> 'login($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.logout'] = array(
            'function' => 'soaFunctions::logout',
            'docstring'=> 'logout($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.paymentCycle'] = array(
            'function' => 'soaFunctions::paymentCycle',
            'docstring'=> 'paymentCycle($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.orderList'] = array(
            'function' => 'soaFunctions::orderList',
            'docstring'=> 'orderList($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.creditDetail'] = array(
            'function' => 'soaFunctions::creditDetail',
            'docstring'=> 'creditDetail($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.billList'] = array(
            'function' => 'soaFunctions::billList',
            'docstring'=> 'billList($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.billDetailCurrent'] = array(
            'function' => 'soaFunctions::billDetailCurrent',
            'docstring'=> 'billDetailCurrent($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.billDetail'] = array(
            'function' => 'soaFunctions::billDetail',
            'docstring'=> 'billDetail($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.myAccount'] = array(
            'function' => 'soaFunctions::myAccount',
            'docstring'=> 'myAccount($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getBillTotal'] = array(
            'function' => 'soaFunctions::getBillTotal',
            'docstring'=> 'getBillTotal($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.paymentBillNotify'] = array(
            'function' => 'soaFunctions::paymentBillNotify',
            'docstring'=> 'paymentBillNotify($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
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

        $services['soa.productDetail'] = array(
            'function' => 'soaFunctions::productDetail',
            'docstring'=> 'productDetail($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.freshProducts'] = array(
            'function' => 'soaFunctions::freshProducts',
            'docstring'=> 'freshProducts($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );


        //TODO
        //Promotion
        $services['soa.verifyCartPromotion'] = array(
            'function' => 'soaFunctions::verifyCartPromotion',
            'docstring' => 'verifyCartPromotion($customer_id, $station_id=1, $language_id=2, $origin_id, $key)',
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

        $services['soa.getMerchantInfo'] = array(
            'function' => 'soaFunctions::getMerchantInfo',
            'docstring'=> 'getMerchantInfo($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.addMarketingEvent'] = array(
            'function' => 'soaFunctions::addMarketingEvent',
            'docstring'=> 'addMarketingEvent($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.checkMarketingEvent'] = array(
            'function' => 'soaFunctions::checkMarketingEvent',
            'docstring'=> 'checkMarketingEvent($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getCustomerRanking'] = array(
            'function' => 'soaFunctions::getCustomerRanking',
            'docstring'=> 'getCustomerRanking($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getCreditTotal'] = array(
            'function' => 'soaFunctions::getCreditTotal',
            'docstring'=> 'getCreditTotal($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getCreditDetail'] = array(
            'function' => 'soaFunctions::getCreditDetail',
            'docstring'=> 'getCreditDetail($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getCreditTotalByType'] = array(
            'function' => 'soaFunctions::getCreditTotalByType',
            'docstring'=> 'getCreditTotalByType($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );



        //Promotions
        $services['soa.getPromotions'] = array(
            'function' => 'soaFunctions::getPromotions',
            'docstring'=> 'getPromotions($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        //Search
        $services['soa.searchProduct'] = array(
            'function' => 'soaFunctions::searchProduct',
            'docstring'=> 'searchProduct($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        //Reset Password
        $services['soa.resetPassword'] = array(
            'function' => 'soaFunctions::resetPassword',
            'docstring'=> 'resetPassword($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        //Check First Order
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


        //Get Product Discount
        $services['soa.getProductDiscount'] = array(
            'function' => 'soaFunctions::getProductDiscount',
            'docstring'=> 'getProductDiscount($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );


        //Coupons
        $services['soa.getCoupons'] = array(
            'function' => 'soaFunctions::getCoupons',
            'docstring'=> 'getCoupons($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        //Activity Catalog
        $services['soa.getActivityCategory'] = array(
            'function' => 'soaFunctions::getActivityCategory',
            'docstring'=> 'getActivityCategory($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        //Apply Coupon to Customer
        $services['soa.applyCouponToCustomer'] = array(
            'function' => 'soaFunctions::applyCouponToCustomer',
            'docstring'=> 'applyCouponToCustomer($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        //Apply Coupon to Customer
        $services['soa.applyTransactionRuleToCustomer'] = array(
            'function' => 'soaFunctions::applyTransactionRuleToCustomer',
            'docstring'=> 'applyTransactionRuleToCustomer($data)',
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
        $services['soa.getCustomerGroupInfo'] = array(
            'function' => 'soaFunctions::getCustomerGroupInfo',
            'docstring'=> 'getCustomerGroupInfo($data)',
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
	
	
	

        $services['soa.getNoticeWithWarehouse'] = array(
            'function' => 'soaFunctions::getNoticeWithWarehouse',
            'docstring'=> 'getNoticeWithWarehouse($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getBannerWithWarehouse'] = array(
            'function' => 'soaFunctions::getBannerWithWarehouse',
            'docstring'=> 'getBannerWithWarehouse($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getCanReturnOrderByCustomer'] = array(
            'function' => 'soaFunctions::getCanReturnOrderByCustomer',
            'docstring' => 'getCanReturnOrderByCustomer($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        $services['soa.orderListWithCache'] = array(
            'function' => 'soaFunctions::orderListWithCache',
            'docstring'=> 'orderListWithCache($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getCanReturnOrderList'] = array(
            'function' => 'soaFunctions::getCanReturnOrderList',
            'docstring'=> 'getCanReturnOrderList($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.getReturnOrderApplyList'] = array(
            'function' => 'soaFunctions::getReturnOrderApplyList',
            'docstring'=> 'getReturnOrderApplyList($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        // New Search
        $services['soa.newSearchProduct'] = array(
            'function' => 'soaFunctions::newSearchProduct',
            'docstring'=> 'newSearchProduct($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        // 商品信息带缓存
        $services['soa.getProductWithCache'] = array(
            'function' => 'soaFunctions::getProductWithCache',
            'docstring'=> 'getProductWithCache($data, $origin_id, $key)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        // 减少商品缓存库存
        $services['soa.minusStock'] = array(
            'function' => 'soaFunctions::minusStock',
            'docstring'=> 'minusStock($data, $origin_id, $key)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        // 增加商品缓存库存
        $services['soa.addCacheStock'] = array(
            'function' => 'soaFunctions::addCacheStock',
            'docstring'=> 'addCacheStock($data, $origin_id, $key)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        // 更改订单缓存状态
        $services['soa.changeCacheOrderStatus'] = array(
            'function' => 'soaFunctions::changeCacheOrderStatus',
            'docstring'=> 'changeCacheOrderStatus($data, $origin_id, $key)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        // 清除个人订单缓存
        $services['soa.clearOrderListCache'] = array(
            'function' => 'soaFunctions::clearOrderListCache',
            'docstring'=> 'clearOrderListCache($data, $origin_id, $key)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        // 账户信息带缓存
        $services['soa.myAccountWithCache'] = array(
            'function' => 'soaFunctions::myAccountWithCache',
            'docstring'=> 'myAccountWithCache($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );
        // 用户订单信息带缓存
        $services['soa.getOrderByCustomerWithCache'] = array(
            'function' => 'soaFunctions::getOrderByCustomerWithCache',
            'docstring' => 'getOrderByCustomerWithCache($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getOrderTotalDetailWithCache'] = array(
            'function' => 'soaFunctions::getOrderTotalDetailWithCache',
            'docstring' => 'getOrderTotalDetailWithCache($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        $services['soa.getOrderProductWithCache'] = array(
            'function' => 'soaFunctions::getOrderProductWithCache',
            'docstring' => 'getOrderProductWithCache($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );

        // 清除单一订单状态缓存
        $services['soa.clearOrderStatusCache'] = array(
            'function' => 'soaFunctions::clearOrderStatusCache',
            'docstring' => 'clearOrderStatusCache($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        // 清除单一订单状态缓存
        $services['soa.addReturnDeliverProductData'] = array(
            'function' => 'soaFunctions::addReturnDeliverProductData',
            'docstring' => 'addReturnDeliverProductData($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        // 用户取消退货申请
        $services['soa.cancelReturnDeliverOrder'] = array(
            'function' => 'soaFunctions::cancelReturnDeliverOrder',
            'docstring' => 'cancelReturnDeliverOrder($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        // 获取BD区域
        $services['soa.getBdAreaByBdCode'] = array(
            'function' => 'soaFunctions::getBdAreaByBdCode',
            'docstring' => 'getBdAreaByBdCode($data)',
            'signature' => array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt, //Origin ID
                    $xmlrpcString //Key
                )
            )
        );
        // 获取用户所属区域仓库Id
        $services['soa.getWarehouseIdByAreaId'] = array(
            'function' => 'soaFunctions::getWarehouseIdByAreaId',
            'docstring'=> 'getWarehouseIdByAreaId($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        //
        $services['soa.newGetProductInventory'] = array(
            'function' => 'soaFunctions::newGetProductInventory',
            'docstring'=> 'newGetProductInventory($data)',
            'signature'=> array(
                array(
                    $xmlrpcString, //Result
                    $xmlrpcString, //Request JSON data
                    $xmlrpcInt,    //Origin ID
                    $xmlrpcString  //Key
                )
            )
        );

        $services['soa.newGetProducts'] = array(
            'function' => 'soaFunctions::newGetProducts',
            'docstring'=> 'newGetProducts($data)',
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
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $common->getStation($id, $station_id, $language_id, $origin_id);
    }

    
    
     function getOrders($data, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        
        return $common->getOrders($data, $station_id, $language_id, $origin_id,$key);
    }

    function getPurchaseOrders($data, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        
        return $common->getPurchaseOrders($data, $station_id, $language_id, $origin_id,$key);
    }

    function ordered($data, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        
        return $common->ordered($data, $station_id, $language_id, $origin_id,$key);
    }
    
    function getInventoryUserOrder($data, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        
        return $common->getInventoryUserOrder($data, $station_id, $language_id, $origin_id,$key);
    }

    function getStatus($data, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        
        return $common->getStatus($data, $station_id, $language_id, $origin_id,$key);
    }
    
    function getOrderss($data, $station_id, $language_id, $origin_id, $key){
        global $common;
	
		
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        
        return $common->getOrderss($data, $station_id, $language_id, $origin_id,$key);
    }
	
	  function orderdistr($data, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        
        return $common->orderdistr($data, $station_id, $language_id, $origin_id,$key);
    }
	
	 function orderRedistr($data, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        
        return $common->orderRedistr($data, $station_id, $language_id, $origin_id,$key);
    }
	
    function getNotice($id, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $common->getNotice($id, $station_id, $language_id, $origin_id);
    }

    function getBanner($id, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $common->getBanner($id, $station_id, $language_id, $origin_id);
    }
    
    function getCategory($id, $station_id, $language_id, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $common->getCategory($id, $station_id, $language_id, $origin_id);
    }
    
    function getCategoryProduct($id, $station_id, $language_id, $origin_id, $key){
        global $product;

        return $product->getCategoryProduct($id, $station_id, $language_id, $origin_id);
    }

    function getOnSaleProduct($id, $station_id, $language_id, $origin_id, $key){
        global $product;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->getOnSaleProduct($id, $station_id, $language_id, $origin_id);
    }

    function getProduct($id, $station_id, $language_id, $origin_id, $key){
        global $product;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->getProduct($id, $station_id, $language_id, $origin_id);
    }

    function getProducts($data, $station_id, $language_id, $origin_id, $key){
        global $product;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->getProducts($data, $station_id, $language_id, $origin_id);
    }

    function getParentCategoryProduct($id, $station_id, $language_id, $origin_id, $key){
        global $product;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->getParentCategoryProduct($id, $station_id, $language_id, $origin_id);
    }

    function getStationProduct($id, $station_id, $language_id, $origin_id, $key){
        global $product;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->getStationProduct($id, $station_id, $language_id, $origin_id);
    }

    function getPromProduct($id, $station_id, $language_id, $origin_id, $key){
        global $product;

        return $product->getPromProduct($id, $station_id, $language_id, $origin_id);
    }

    function getPickupSpot($id, $station_id, $language_id, $origin_id, $key){
        global $checkout;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $checkout->getPickupSpot($id, $station_id, $language_id, $origin_id);
    }

    function getShippingDate($id, $station_id, $language_id, $origin_id, $key){
        global $checkout;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $checkout->getShippingDate($id, $station_id, $language_id, $origin_id);
    }

    function getPaymentMethod($id, $station_id, $language_id, $origin_id, $key){
        global $checkout;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $checkout->getPaymentMethod($id, $station_id, $language_id, $origin_id);
    }

    //Shopping Cart
    function getCart($data, $station_id, $language_id, $origin_id, $key){
        global $cart;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $cart->getCart($data, $station_id, $language_id, $origin_id);
    }

    function delCart($data, $station_id, $language_id, $origin_id, $key){
        global $cart;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $cart->delCart($data, $station_id, $language_id, $origin_id);
    }
    function getContainersInfo($data, $station_id, $language_id, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getContainersInfo($data, $station_id, $language_id, $origin_id);
    }

    function addCart($data, $station_id, $language_id, $origin_id, $key){
        global $cart;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $cart->addCart($data, $station_id, $language_id, $origin_id);
    }

    function updateCart($data, $station_id, $language_id, $origin_id, $key){
        global $cart;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $cart->updateCart($data, $station_id, $language_id, $origin_id);
    }



    //Customer
    function getCustomerInfo($id, $station_id, $language_id, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getCustomerInfo($id, $station_id, $language_id, $origin_id);
    }

    function getCustomerInfoByUid($uid, $station_id, $language_id, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getCustomerInfoByUid($uid, $station_id, $language_id, $origin_id);
    }

    function getCustomerShippingAddress($id, $station_id, $language_id, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getCustomerShippingAddress($id, $station_id, $language_id, $origin_id);
    }

    function getOrderByCustomer($id, $station_id, $language_id, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getOrderByCustomer($id, $station_id, $language_id, $origin_id);
    }

    function customerCancelOrder($data, $station_id, $language_id, $origin_id, $key){
        global $order;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $order->customerCancelOrder($data, $station_id, $language_id, $origin_id);
    }

    function getCustomerCredit($id, $station_id, $language_id, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getCustomerCredit($id, $station_id, $language_id, $origin_id);
    }

    function getCustomerLimitedProductId($id, $station_id, $language_id, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getCustomerLimitedProductId($id, $station_id, $language_id, $origin_id);
    }

    function addWXScan($data, $station_id, $language_id, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->addWXScan($data, $station_id, $language_id, $origin_id);
    }



    //Order
    function addOrder($data, $station_id, $language_id, $origin_id, $key){
        global $order;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $order->addOrder($data, $station_id, $language_id, $origin_id);
    }

    function addOrderTotal($data, $station_id, $language_id, $origin_id, $key){
        global $order;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $order->addOrderTotal($data, $station_id, $language_id, $origin_id);
    }

    function getOrderTotal($id, $station_id, $language_id, $origin_id, $key){
        global $order;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $order->getOrderTotal($id, $station_id, $language_id, $origin_id);
    }

    function getOrderProduct($id, $station_id, $language_id, $origin_id, $key){
        global $order;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $order->getOrderProduct($id, $station_id, $language_id, $origin_id);
    }

    function getOrderTotalDetail($id, $station_id, $language_id, $origin_id, $key){
        global $order;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $order->getOrderTotalDetail($id, $station_id, $language_id, $origin_id);
    }



    //For Offline
    function stationRetail($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->stationRetail($data, $station_id, $language_id, $origin_id);
    }

    function pushOrderByStation($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->pushOrderByStation($data, $station_id, $language_id, $origin_id);
    }

    function getOrderByStation($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getOrderByStation($data, $station_id, $language_id, $origin_id);
    }
    function getProductWeightInfo($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getProductWeightInfo($data, $station_id, $language_id, $origin_id);
    }

    function getStationOrderProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getStationOrderProduct($data, $station_id, $language_id, $origin_id);
    }

    function addOrderProductToInv_pre($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addOrderProductToInv_pre($data, $station_id, $language_id, $origin_id);
    }

    function addPurchaseOrderProductToInv_pre($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addPurchaseOrderProductToInv_pre($data, $station_id, $language_id, $origin_id);
    }

    function addOrderProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addOrderProductToInv($data, $station_id, $language_id, $origin_id);
    }

    function addFastMoveSortingToInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addFastMoveSortingToInv($data, $station_id, $language_id, $origin_id);
    }
    
    function addPurchaseOrderProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addPurchaseOrderProductToInv($data, $station_id, $language_id, $origin_id);
    }
    
    function delOrderProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->delOrderProductToInv($data, $station_id, $language_id, $origin_id);
    }
    
    function delPurchaseOrderProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;
    
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
    
        return $inventory->delPurchaseOrderProductToInv($data, $station_id, $language_id, $origin_id);
    }
    
    
    
    function addCheckProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addCheckProductToInv($data, $station_id, $language_id, $origin_id);
    }
    
    function addCheckSingleProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addCheckSingleProductToInv($data, $station_id, $language_id, $origin_id);
    }
    
    function delCheckSingleProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->delCheckSingleProductToInv($data, $station_id, $language_id, $origin_id);
    }
    
    function addVegCheckProductToInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;
    
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addVegCheckProductToInv($data, $station_id, $language_id, $origin_id);
    }
    
    
    function addOrderNum($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addOrderNum($data, $station_id, $language_id, $origin_id);
    }
    function addCheckFrameOut($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addCheckFrameOut($data, $station_id, $language_id, $origin_id);
    }
    
    function addCheckFrameCage($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addCheckFrameCage($data, $station_id, $language_id, $origin_id);
    }
    
    function addCheckFrameCheck($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;
    
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addCheckFrameCheck($data, $station_id, $language_id, $origin_id);
    }
    
    function addContainer(){
        global $inventory;

        return $inventory->addContainer();
    }
    
    
    function getAddedFrameOut($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getAddedFrameOut($data, $station_id, $language_id, $origin_id);
    }
    
    function getAddedFrameCage($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;
    
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getAddedFrameCage($data, $station_id, $language_id, $origin_id);
    }
    
    
    function getAddedFrameCheck($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getAddedFrameCheck($data, $station_id, $language_id, $origin_id);
    }
    function getAddedFrameIn($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getAddedFrameIn($data, $station_id, $language_id, $origin_id);
    }
    
    function checkFrameCanIn($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->checkFrameCanIn($data, $station_id, $language_id, $origin_id);
    }
    
    function addCheckFrameIn($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addCheckFrameIn($data, $station_id, $language_id, $origin_id);
    }
    
    function stationOrderCancel($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->stationOrderCancel($data, $station_id, $language_id, $origin_id);
    }

    function stationOrderConfirm($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->stationOrderConfirm($data, $station_id, $language_id, $origin_id);
    }

    function stationOrderDelivered($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->stationOrderDelivered($data, $station_id, $language_id, $origin_id);
    }

    function stationOrderDeliverOut($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->stationOrderDeliverOut($data, $station_id, $language_id, $origin_id);
    }

    function getStationCustomerInfo($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getStationCustomerInfo($data, $station_id, $language_id, $origin_id);
    }

    function inventoryIn($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        //Limit
        if($origin_id == 7 && in_array($station_id, unserialize(STATION_INVENTORY_FUNC_LIMIT))){
            return false; //Some station inventory function via POS limited
        }

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

		//For App version 0803 bug
        if($origin_id == 7 && $station_id == 10){
            return $inventory->inventoryInit($data, $station_id, $language_id, $origin_id);
        }
		if($origin_id == 7 && $station_id == 11){
            return $inventory->inventoryInit($data, $station_id, $language_id, $origin_id);
        }
		if($origin_id == 7 && $station_id == 12){
            return $inventory->inventoryInit($data, $station_id, $language_id, $origin_id);
        }

        return $inventory->inventoryIn($data, $station_id, $language_id, $origin_id);
    }

    
     function inventoryInProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

       

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

	
        return $inventory->inventoryInProduct($data, $station_id, $language_id, $origin_id);
    }
    
    function addReturnDeliverProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

       

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

	
        return $inventory->addReturnDeliverProduct($data, $station_id, $language_id, $origin_id);
    }

    function disableReturnDeliverProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->disableReturnDeliverProduct($data, $station_id, $language_id, $origin_id);
    }


    function confirmReturnDeliverProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->confirmReturnDeliverProduct($data, $station_id, $language_id, $origin_id);
    }

    function inventoryOutProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;
    
       

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

	
        return $inventory->inventoryOutProduct($data, $station_id, $language_id, $origin_id);
    }
    function inventoryAdjustProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

       

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

	
        return $inventory->inventoryAdjustProduct($data, $station_id, $language_id, $origin_id);
    }
    function inventoryChangeProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;
    
    

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

	
        return $inventory->inventoryChangeProduct($data, $station_id, $language_id, $origin_id);
    }
    function inventoryCheckProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

       

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

	
        return $inventory->inventoryCheckProduct($data, $station_id, $language_id, $origin_id);
    }
    
    function inventoryCheckSingleProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

       

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

	
        return $inventory->inventoryCheckSingleProduct($data, $station_id, $language_id, $origin_id);
    }
    
    
    function inventoryVegCheckProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;
    
       

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

	
        return $inventory->inventoryVegCheckProduct($data, $station_id, $language_id, $origin_id);
    }
    
    
    function inventoryOut($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->inventoryOut($data, $station_id, $language_id, $origin_id);
    }

     function getStationProductInfob2b($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getStationProductInfob2b($data, $station_id, $language_id, $origin_id);
    }
    
    
    function inventoryReturn($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;
        global $log;
        
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        return $inventory->inventoryReturn($data, $station_id, $language_id, $origin_id);
    }

    function inventoryInit($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

		//Limit
        if($origin_id == 7 && in_array($station_id, unserialize(STATION_INVENTORY_FUNC_LIMIT))){
            return false; //Some station inventory function via POS limited
        }

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->inventoryInit($data, $station_id, $language_id, $origin_id);
    }

    function inventoryBreakage($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        //Limit
        if($origin_id == 7 && in_array($station_id, unserialize(STATION_INVENTORY_FUNC_LIMIT))){
            return false; //Some station inventory function via POS limited
        }

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->inventoryBreakage($data, $station_id, $language_id, $origin_id);
    }

    function inventoryNoonCheck($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        //Limit
        //if($origin_id == 7 && in_array($station_id, unserialize(STATION_INVENTORY_FUNC_LIMIT))){
        //    return false; //Some station inventory function via POS limited
        //}

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->inventoryNoonCheck($data, $station_id, $language_id, $origin_id);
    }

    function getStationProductInfo_bak($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getStationProductInfo($data, $station_id, $language_id, $origin_id);
    }
 //Inv Get Plan - TEMP
    function getOrderSortingList($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getOrderSortingList($data, $station_id, $language_id, $origin_id);
    }
    
    function getPurchaseOrderSortingList($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getPurchaseOrderSortingList($data, $station_id, $language_id, $origin_id);
    }
    function getUserInfoByUid($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getUserInfoByUid($data, $station_id, $language_id, $origin_id);
    }

    function getStationMove($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getStationMove($data, $station_id, $language_id, $origin_id);
    }

    function confirmStationMove($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->confirmStationMove($data, $station_id, $language_id, $origin_id);
    }

    function getStationMoveItem($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getStationMoveItem($data, $station_id, $language_id, $origin_id);
    }


    //Station Retail Login
    function getSmsVerifyCode($data){
        global $inventory;

        return $inventory->getSmsVerifyCode($data);
    }

    function getStationOriginKey($data){
        global $inventory;

        return $inventory->getStationOriginKey($data);
    }

    function getStationInfo($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getStationInfo($data, $station_id, $language_id, $origin_id);
    }

    function checkUpdate($data){
        global $inventory;

        return $inventory->checkUpdate($data);
    }

    function register($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->register(json_decode($data, true));
    }

    function login($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->login(json_decode($data, true));
    }

    function logout($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->logout(json_decode($data, true));
    }

    function paymentCycle($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->paymentCycle(json_decode($data, true));
    }

    function orderList($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->orderList(json_decode($data, true));
    }

    function creditDetail($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->creditDetail(json_decode($data, true));
    }

    function billList($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->billList(json_decode($data, true));
    }

    function billDetailCurrent($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->billDetailCurrent(json_decode($data, true));
    }

    function billDetail($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->billDetail(json_decode($data, true));
    }

    function myAccount($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->myAccount(json_decode($data, true));
    }

    function getBillTotal($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->getBillTotal(json_decode($data, true));
    }

    function paymentBillNotify($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $payment;
        return $payment->paymentBillNotify(json_decode($data, true));
    }

    function sendMsg($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $common;
        return $common->sendMsg(json_decode($data, true));
    }

    function productDetail($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $product;
        return $product->productDetail(json_decode($data, true));
    }

    function freshProducts($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $product;
        return $product->freshProducts(json_decode($data, true));
    }

    function verifyCartPromotion($id, $station_id, $language_id, $origin_id, $key){
        global $promotion;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $promotion->verifyCartPromotion($id, $station_id, $language_id, $origin_id);
    }

    function getProductInventory($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getProductInventory($data, $station_id, $language_id, $origin_id);
    }
    
    function addOrderProductStation($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addOrderProductStation($data, $station_id, $language_id, $origin_id);
    }
    
    function addPurchaseOrderProductStation($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->addPurchaseOrderProductStation($data, $station_id, $language_id, $origin_id);
    }
    
    function getSkuProductInfo($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getSkuProductInfo($data, $station_id, $language_id, $origin_id);
    }

    function changeProductSection($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->changeProductSection($data, $station_id, $language_id, $origin_id);
    }

    function getProductSectionInfo($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getProductSectionInfo($data, $station_id, $language_id, $origin_id);
    }
    
    function getSkuProductInfoInv($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;
    
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getSkuProductInfoInv($data, $station_id, $language_id, $origin_id);
    }
    
    
    function getInventoryIn($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getInventoryIn($data, $station_id, $language_id, $origin_id);
    }
    
    
    function getAddedReturnDeliverProduct($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
}

        return $inventory->getAddedReturnDeliverProduct($data, $station_id, $language_id, $origin_id);
    }
    
    function inventory_login($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
}

        return $inventory->inventory_login($data, $station_id, $language_id, $origin_id);
    }
    
    function getInventoryAdjust($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getInventoryAdjust($data, $station_id, $language_id, $origin_id);
    }

    function getInventoryOut($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getInventoryOut($data, $station_id, $language_id, $origin_id);
    }
    function getInventoryChange($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getInventoryChange($data, $station_id, $language_id, $origin_id);
    }
    function getInventoryCheck($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getInventoryCheck($data, $station_id, $language_id, $origin_id);
    }
    function getInventoryCheckSingle($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getInventoryCheckSingle($data, $station_id, $language_id, $origin_id);
    }
    function getInventoryVegCheck($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getInventoryVegCheck($data, $station_id, $language_id, $origin_id);
    }
    function getOrderStatus($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getOrderStatus($data, $station_id, $language_id, $origin_id);
    }

    function getPurchaseOrderStatus($data, $station_id, $language_id, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->getPurchaseOrderStatus($data, $station_id, $language_id, $origin_id);
    }

    function getMerchantInfo($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->getMerchantInfo(json_decode($data, true));
    }

    function addMarketingEvent($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->addMarketingEvent(json_decode($data, true));
    }

    function checkMarketingEvent($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->checkMarketingEvent(json_decode($data, true));
    }

    function getCustomerRanking($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->getCustomerRanking(json_decode($data, true));
    }

    function getCreditTotal($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->getCreditTotal(json_decode($data, true));
    }

    function getCreditDetail($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->getCreditDetail(json_decode($data, true));
    }

    function getCreditTotalByType($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->getCreditTotalByType(json_decode($data, true));
    }

    function getPromotions($data, $origin_id, $key){
        global $promotion;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $promotion->getPromotions(json_decode($data, true));
    }

    function searchProduct($data, $origin_id, $key){
        global $product;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->searchProduct(json_decode($data, true));
    }

    function resetPassword($data, $origin_id, $key){
        global $customer;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->resetPassword(json_decode($data, true));
    }

    function checkFirstOrder($data, $origin_id, $key){
        global $common;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $common->checkFirstOrder(json_decode($data, true));
    }

    function getProductDiscount($data, $origin_id, $key){
        global $product;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->getProductDiscount(json_decode($data, true));
    }

    function getCoupons($data, $origin_id, $key){
        global $promotion;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $promotion->getCoupons(json_decode($data, true));
    }

    function getActivityCategory($data, $origin_id, $key){
        global $promotion;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $promotion->getActivityCategory(json_decode($data, true));
    }

    function applyCouponToCustomer($data, $origin_id, $key){
        global $promotion;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $promotion->applyCouponToCustomer(json_decode($data, true));
    }


    function applyTransactionRuleToCustomer($data, $origin_id, $key){
        global $promotion;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $promotion->applyTransactionRuleToCustomer(json_decode($data, true));
    }

    function getCustomerGroupInfo($data, $origin_id, $key){
        global $customer;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getCustomerGroupInfo(json_decode($data, true));
    }

    function getAreaList($data, $origin_id, $key){
        global $common;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $common->getAreaList(json_decode($data, true));
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
        global $common;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $common->getProductType(json_decode($data, true));
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

    function getNoticeWithWarehouse($data, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $common->getNoticeWithWarehouse(json_decode($data, true));
    }

    function getBannerWithWarehouse($data, $origin_id, $key){
        global $common;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $common->getBannerWithWarehouse(json_decode($data, true));
    }

    function getCanReturnOrderByCustomer($data, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getCanReturnOrderByCustomer(json_decode($data, true));
    }

    function orderListWithCache($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->orderListWithCache(json_decode($data, true));
    }

    // 用户可退订单列表
    function getCanReturnOrderList($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->getCanReturnOrderList(json_decode($data, true));
    }

    function addReturnDeliverProductData($data, $origin_id, $key){
        global $inventory;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }
        return $inventory->addReturnDeliverProductData(json_decode($data, true));
    }

    // 用户退货申请列表
    function getReturnOrderApplyList($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $inventory;
        return $inventory->getReturnOrderApplyList(json_decode($data, true));
    }

    function cancelReturnDeliverOrder($data, $origin_id, $key){
        global $inventory;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $inventory->cancelReturnDeliverOrder(json_decode($data, true));
    }

    function myAccountWithCache($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->myAccountWithCache(json_decode($data, true));
    }

    function newSearchProduct($data, $origin_id, $key){
        global $product;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->newSearchProduct(json_decode($data, true));
    }

    // 获取商品信息[ 带缓存 ]
    function getProductWithCache($data, $origin_id, $key){
        global $product;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->getProductWithCache(json_decode($data, true));
    }

    // 减少Redis缓存里的库存
    function minusStock($data, $origin_id, $key){
        global $product;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->minusStock(json_decode($data, true));
    }

    // 增加Redis缓存里的库存
    function addCacheStock($data, $origin_id, $key){
        global $product;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $product->addCacheStock(json_decode($data, true));
    }

    // 订单取消处理Redis订单状态
    function changeCacheOrderStatus($data, $origin_id, $key){
        global $customer;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->changeCacheOrderStatus(json_decode($data, true));
    }

    // 清除用户Redis订单缓存 [订单新增]
    function clearOrderListCache($data, $origin_id, $key){
        global $customer;
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->clearOrderListCache(json_decode($data, true));
    }

    // 用户订单详情缓存
    function getOrderByCustomerWithCache($data, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->getOrderByCustomerWithCache(json_decode($data, true));
    }
    function getOrderTotalDetailWithCache($data, $origin_id, $key){
        global $order;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $order->getOrderTotalDetailWithCache(json_decode($data, true));
    }
    function getOrderProductWithCache($data, $origin_id, $key){
        global $order;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $order->getOrderProductWithCache(json_decode($data, true));
    }

    function clearOrderStatusCache($data, $origin_id, $key){
        global $customer;

        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        return $customer->clearOrderStatusCache(json_decode($data, true));
    }

    function getWarehouseIdByAreaId($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->getWarehouseIdByAreaId(json_decode($data, true));
    }

    function getBdAreaByBdCode($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $customer;
        return $customer->getBdAreaByBdCode(json_decode($data, true));
    }

    function newGetProductInventory($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $inventory;
        return $inventory->newGetProductInventory(json_decode($data, true));
    }

    function newGetProducts($data, $origin_id, $key){
        if ( !soaHelper::auth($origin_id, $key) ){
            return 'ERROR, NO AUTHORIZED.';
        }

        global $product;
        return $product->newGetProducts(json_decode($data, true));
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