<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');
require_once(DIR_SYSTEM.'/redis.php');

class CUSTOMER{
    private $redis;
    private $orderTime;

    public function __construct()
    {
        $this->redis     = new MyRedis();
        $this->orderTime = defined('REDIS_CACHE_TIME') ? REDIS_CACHE_TIME : 1800;
        $this->orderStatusTime = defined('REDIS_ORDER_STATUS_CACHE_TIME') ? REDIS_ORDER_STATUS_CACHE_TIME : 600;
    }

    function getOrderKey($stationId=1, $orderId){
        $keyPrefix = defined('REDIS_ORDER_KEY_PREFIX') ? REDIS_ORDER_KEY_PREFIX : 'order';
        $key       = $keyPrefix.':'.$stationId.':'.$orderId; //order : stationId : orderId
        return $key;
    }

    function getOrderListKey($stationId=1, $customerId){
        $keyPrefix = defined('REDIS_ORDER_LIST_KEY_PREFIX') ? REDIS_ORDER_LIST_KEY_PREFIX : 'orderList';
        $key       = $keyPrefix.':'.$stationId.':'.$customerId; //order : stationId : customerId
        return $key;
    }

    function getOrderStatusKey($stationId=1, $orderId){
        $keyPrefix = defined('REDIS_ORDER_STATUS_KEY_PREFIX') ? REDIS_ORDER_STATUS_KEY_PREFIX : 'orderStatus';
        $key       = $keyPrefix.':'.$stationId.':'.$orderId; //order : stationId : orderId
        return $key;
    }

    function getMyAccountKey($customerId){
        $keyPrefix = defined('REDIS_MY_ACCOUNT_KEY_PREFIX') ? REDIS_MY_ACCOUNT_KEY_PREFIX : 'myAccount';
        $key       = $keyPrefix.':'.$customerId; //myAccount : customerId
        return $key;
    }

    function getOrderDetailListKey($stationId=1, $customerId){
        $keyPrefix = defined('REDIS_ORDER_DETAIL_LIST_KEY_PREFIX') ? REDIS_ORDER_DETAIL_LIST_KEY_PREFIX : 'orderDetailList';
        $key       = $keyPrefix.':'.$stationId.':'.$customerId; //orderDetail : stationId : customerId
        return $key;
    }

    function getCustomerGroupInfo(array $data){
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? $data['customer_id'] : 0;
        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $language_id = (isset($data['language_id']) && (int)$data['language_id']) ? (int)$data['language_id'] : 2;

        $return = array(
            'return_code' => 'ERROR',
            'return_msg' => 'NO RECORD',
            'return_data' => array(
                'customerInfo' => array(),
                'customerOrderInfo' => array(),
                'customerGroupInfo' => array()
            )
        );

        if(!$customer_id || !$station_id){
            return $return;
        }

        // Customer Info
        $sql = "
            select
            A.customer_id,
            A.is_nopricetag,
            A.is_agent,
            A.is_demo,
            A.customer_group_id,
            B.top_level,
            C.name group_name,
            A.date_added 'reg_time'
            from oc_customer as A
            left join oc_customer_group B on A.customer_group_id = B.customer_group_id
            left join oc_customer_group_description as C on B.customer_group_id = C.customer_group_id and C.language_id = '".$language_id."'
            where A.status = 1 and A.customer_id = '".$customer_id."'";

        $query = $db->query($sql);
        $results = $query->row;
        if($results && sizeof($results)){
            $return['return_code'] = 'SUCCESS';
            $return['return_msg'] = 'OK';

            $return['return_data']['customerInfo'] = $results;
        }

        // Customer order info. this month only
        $sql = "
            select count(O.order_id) orders, if(sum(O.sub_total),sum(O.sub_total),0) totals
            from oc_order O
            where
                year(O.date_added) = year(current_date())
                and month(O.date_added) = month(current_date())
                and O.order_status_id != '".CANCELLED_ORDER_STATUS."'
                and O.station_id = '".$station_id."' and O.customer_id = '".$customer_id."'";

        $query = $db->query($sql);
        $results = $query->row;
        if($results && sizeof($results)){
            $return['return_data']['customerOrderInfo'] = $results;
        }

        // Customer group info. this month only
        $sql = "
            select
              A.customer_group_id, B.name group_name,
              A.aim_orders, A.aim_totals, A.discount_total, A.top_level,
              B.description group_desc
            from oc_customer_group A
            left join oc_customer_group_description as B on A.customer_group_id = B.customer_group_id
            where B.language_id = '".$language_id."' order by A.sort_order asc";

        $query = $db->query($sql);
        $results = $query->rows;
        if($results && sizeof($results)){
            $return['return_data']['customerGroupInfo'] = $results;
        }

        return $return;
    }

    function getCustomerInfo($customer_id=false, $station_id=1, $language_id=2, $origin_id=1, $uid=false){
        //TODO Station
        global $db,$log;

        if($uid){ //Get customer_id from uid
            $customer_id = $this->getCustomerIdByUid($uid);
        }

        if(!$customer_id && !$uid){
            $log->write(__FUNCTION__.' invalid customer_id or uid'. "\n\r");
            return false;
        }

        $customer_info = array();
        $customer_info['customer_id'] = $customer_id;

        //Get basic info
        $sql = "
            select
            A.customer_id 'customer_id',
            A.customer_group_id,
            A.is_nopricetag,
            A.milk_ordered,
            A.is_agent,
            A.is_demo,
            A.bd_id,
            A.area_id,
            A.uid,
            A.firstname,
            A.lastname,
            A.telephone,
            A.payment_cycle,
            A.email,
            C.name 'group',
            A.date_added 'reg_time'
            from oc_customer as A
            left join oc_customer_group_description as C on A.customer_group_id = C.customer_group_id and C.language_id = {$language_id}
            where A.status = 1 and A.customer_id = {$customer_id}";

        $query = $db->query($sql);
        $results = $query->row;
        if($results && sizeof($results)){
            // $results ['reg_time'] = strtotime($results ['cdate']);
            $customer_info['basic_info'] = $results;
            //return $results;
        }
        //return $customer_info;

            $customer_info['credit'] = $this->getCustomerCredit($customer_id,$station_id,$language_id,$origin_id);

        //Get points info
        $sql = "select round(sum(points),0) points_total from oc_customer_reward where customer_id = '".$customer_id."'";
        $query = $db->query($sql);
        $results = $query->row;
        if($results && sizeof($results)){
            // $results ['reg_time'] = strtotime($results ['cdate']);
            $customer_info['points'] = $results['points_total'];
            //return $results;
        }

        //Get order info
        $order_info = $this->getOrderByCustomer($customer_id,$station_id,$language_id,$origin_id);
        $customer_info['orders'] = $order_info;

        return $customer_info;
    }

    function getCustomerInfoByUid($uid=false, $station_id=1, $language_id=2, $origin_id=1){
        return $this->getCustomerInfo(false, $station_id, $language_id, $origin_id, $uid);
    }

    private function checkAgentCustomer($customer_id){
        global $db;

        //代理用户和账期用户不可使用余额
        $sql = "select customer_id from oc_customer where customer_id = '".$customer_id."' and is_agent = 0 and payment_cycle = 0";
        $query = $db->query($sql);

        if(!$query->num_rows){
            return true;
        }
        return false;
    }

    function getCustomerCredit($id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        //Check Agent
        if($this->checkAgentCustomer($id)){
            return 0;
        }

        //Get credit info
        $sql = "select round(sum(amount),2) credit_total from oc_customer_transaction where customer_id = '".$id."'";
        $query = $db->query($sql);
        $results = $query->row;
        if($results && sizeof($results)){
            // $results ['reg_time'] = strtotime($results ['cdate']);
            //$customer_info['credit'] = $results;
            return $results['credit_total'];
        }

        return false;
    }

    
    // 生成 customer_id 缓存
    function generateCustomerIdCache(array $data)
    {
        global $db;
        $uid        = !empty($data['uid'])        ? $db->escape($data['uid']) : false;
        $station_id = !empty($data['station_id']) ? (int)$data['station_id']  : false;

        if (!$uid || !$station_id) { return false; }

        $sql = "SELECT customer_id FROM oc_customer WHERE uid = '{$uid}'";
        $query = $db->query($sql);
        $result = $query->row;

        if (!empty($result['customer_id'])) {
            $generate_key = $this->getGenerateKey($uid, $station_id);

            $this->redis->setex($generate_key, $this->orderTime, base64_encode($result['customer_id']));
            return base64_encode($result['customer_id']);
        }

        return false;
    }

    // 获取 customer_id 缓存
    function getCustomerIdCacheByUid(array $data)
    {
        $uid        = !empty($data['uid'])        ? $data['uid']             : false;
        $station_id = !empty($data['station_id']) ? (int)$data['station_id'] : false;

        if (!$uid || !$station_id) { return false; }

        $generate_key = $this->getGenerateKey($uid, $station_id);

        if ($this->redis->exists($generate_key)) {
            $customer_id = $this->redis->get($generate_key);
        } else {
            $customer_id = $this->generateCustomerIdCache(array('uid' => $uid, 'station_id' => $station_id));
        }

        return base64_decode($customer_id);
    }


    private function getGenerateKey($uid = '', $station_id = 0)
    {
        $uid        = !empty($uid)        ? $uid             : false;
        $station_id = !empty($station_id) ? (int)$station_id : false;
        if(!$uid  || !$station_id){ return false; }

        $generate_key = defined('GENERATE_KEY') ? GENERATE_KEY : 'xsj_customer_id_key';
        $uid          = md5($uid . $generate_key);
        $generate_key = "customer:" . $station_id . ':' . $uid;

        return $generate_key;
    }


    function getCustomerIdByUid($uid=false){
        global $db;

        $sql = "SELECT customer_id FROM oc_customer WHERE uid = '{$uid}'";
        $query = $db->query($sql);
        $results = $query->row;
        $customer_id = 0;
        if($results && sizeof($results)){
            $customer_id = $results['customer_id'];
        }

        return $customer_id;
    }

    function getCustomerShippingAddress($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Station
        global $db,$log;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        if($id>0){
            $sql = "SELECT * FROM oc_address WHERE customer_id = {$id} AND status=1";
        }
        else{
            $log->write(__FUNCTION__.' invalid customer_id'. "\n\r");
            return false;
        }

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }

        return false;
    }

    function getOrderByCustomer($customer_id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        if(!$customer_id){
            //TODO Log
            return false;
        }

        $sql = "SELECT A.order_id, A.orderstamp, A.shipping_method, A.shipping_code,
            round(A.line_total,2) line_total, round(A.total_adjust,2) total_adjust, round(A.discount_total,2) discount_total, round(A.coupon_discount,2) coupon_discount, round(A.promotion_discount,2) promotion_discount,
            round(A.total,2) total, round(A.credit_paid,2) credit_paid,round(A.point_paid,2) point_paid, round(A.sub_total+A.shipping_fee+A.discount_total+A.balance_container_deposit,2) order_total,
            round(A.balance_container_deposit,2) balance_container_deposit,if(sum(T.value)<0, 0, round(sum(T.value),2) ) due,
            A.deliver_date, A.shipping_name, left(A.date_added,10) order_date, A.sub_total, A.shipping_fee, A.shipping_address_1,
            A.order_status_id, A.order_payment_status_id, A.order_deliver_status_id, B.name order_status, C.name order_payment_status, D.name order_deliver_status,
            A.payment_method, A.payment_code, PS.name ps_name, PS.address ps_address
            FROM oc_order A
            LEFT JOIN oc_order_status B ON A.order_status_id = B.order_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_total T ON A.order_id = T.order_id AND T.accounting = 1
            LEFT JOIN oc_order_payment_status C ON A.order_payment_status_id = C.order_payment_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id  = D.order_deliver_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_x_pickupspot PS ON A.pickupspot_id  = PS.pickupspot_id
            WHERE A.type = 1 AND A.customer_id = {$customer_id} AND A.station_id = {$station_id}
            GROUP BY T.order_id
            ORDER BY order_id DESC";

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }

        return false;
    }

    function getCanReturnOrderByCustomer(array $data){
        global $db;
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : false;
        $language_id = isset($data['language_id']) ? (int)$data['language_id'] : 2;
        if(!$customer_id){ return false; }

        $sql = "SELECT A.order_id, A.orderstamp, A.shipping_method, A.shipping_code,
            round(A.line_total,2) line_total, round(A.total_adjust,2) total_adjust, round(A.discount_total,2) discount_total,
            round(A.total,2) total, round(A.credit_paid,2) credit_paid, round(A.sub_total+A.shipping_fee+A.discount_total+A.balance_container_deposit,2) order_total,
            round(A.balance_container_deposit,2) balance_container_deposit,if(sum(T.value)<0, 0, round(sum(T.value),2) ) due,
            A.deliver_date, A.shipping_name, left(A.date_added,10) order_date, A.sub_total, A.shipping_fee, A.shipping_address_1,
            A.order_status_id, A.order_payment_status_id, A.order_deliver_status_id, B.name order_status, C.name order_payment_status, D.name order_deliver_status,
            A.payment_method, A.payment_code, PS.name ps_name, PS.address ps_address
            FROM oc_order A
            LEFT JOIN oc_order_status B ON A.order_status_id = B.order_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_total T ON A.order_id = T.order_id AND T.accounting = 1
            LEFT JOIN oc_order_payment_status C ON A.order_payment_status_id = C.order_payment_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id  = D.order_deliver_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_x_pickupspot PS ON A.pickupspot_id  = PS.pickupspot_id
            WHERE A.type = 1 AND A.customer_id = {$customer_id}
            GROUP BY T.order_id
            ORDER BY order_id DESC";

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }

        return false;
    }

    function getOrderByCustomerWithCache(array $data){
        global $db;
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : false;
        $language_id = isset($data['language_id']) ? (int)$data['language_id'] : 2;
        $station_id  = isset($data['station_id'])  ? (int)$data['station_id']  : 0;
        if(!$customer_id){ return false; }

        // 获取订单 [info] 缓存
        $orderDetailListKey = $this->getOrderDetailListKey($station_id, $customer_id);
        if($this->redis->llen($orderDetailListKey)){
            $results  = array();
            $orderIds = $this->redis->lrange($orderDetailListKey, 0, -1);
            foreach($orderIds as $orderId){
                $orderKey = $this->getOrderKey($station_id, $orderId);
                $orderInfo = $this->redis->hgetall($orderKey);
                // 获取订单各状态
                $orderStatusInfo = $this->getOrderStatusWithCache(array('stationId' => $station_id, 'orderId' => $orderId));
                if(sizeof($orderStatusInfo)) {
                    foreach($orderStatusInfo as $key => $value){ $orderInfo[$key] = $value; }
                }

                $results[] = $orderInfo;
            }
            return $results;
        }

        $sql = "SELECT A.order_id, A.orderstamp, A.shipping_method, A.shipping_code,
            round(A.line_total,2) line_total, round(A.total_adjust,2) total_adjust, round(A.discount_total,2) discount_total,
            round(A.total,2) total, round(A.credit_paid,2) credit_paid, round(A.sub_total+A.shipping_fee+A.discount_total+A.balance_container_deposit,2) order_total,
            round(A.balance_container_deposit,2) balance_container_deposit,if(sum(T.value)<0, 0, round(sum(T.value),2) ) due,
            A.deliver_date, A.shipping_name, left(A.date_added,10) order_date, A.sub_total, A.shipping_fee, A.shipping_address_1,
            A.order_status_id, A.order_payment_status_id, A.order_deliver_status_id, B.name order_status, C.name order_payment_status, D.name order_deliver_status,
            A.payment_method, A.payment_code, PS.name ps_name, PS.address ps_address
            FROM oc_order A
            LEFT JOIN oc_order_status B ON A.order_status_id = B.order_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_total T ON A.order_id = T.order_id AND T.accounting = 1
            LEFT JOIN oc_order_payment_status C ON A.order_payment_status_id = C.order_payment_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id  = D.order_deliver_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_x_pickupspot PS ON A.pickupspot_id  = PS.pickupspot_id
            WHERE A.type = 1 AND A.customer_id = {$customer_id} AND A.station_id = {$station_id}
            GROUP BY T.order_id
            ORDER BY order_id DESC";

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            // 缓存订单 [info] 详情
            foreach($results as $value){
                $this->setOrderAndStatusCache($station_id, $value);
                $this->redis->rpush($orderDetailListKey, $value['order_id']);
            }
            $this->redis->expire($orderDetailListKey, $this->orderTime);

            return $results;
        }

        return false;
    }


    function getCustomerLimitedProductId($customer_id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;
        return array();

        //$customer = 0, ok

        $sql = "SELECT product_id, name, limit_alert FROM
                (SELECT O.customer_id, P.product_id, PD.name, PD.limit_alert, SUM(OP.quantity) total_ordered, P.customer_total_limit FROM oc_order O
                LEFT JOIN oc_order_product OP ON O.order_id = OP.order_id
                LEFT JOIN oc_product P ON OP.product_id = P.product_id
                LEFT JOIN oc_product_description PD ON P.product_id = PD.product_id AND PD.language_id = {$language_id}
                WHERE O.customer_id = {$customer_id} AND O.order_status_id NOT IN (".CANCELLED_ORDER_STATUS.")
                AND P.customer_total_limit > 0
                GROUP BY P.product_id) A
                WHERE A.total_ordered >= A.customer_total_limit";
        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }

        return false;
    }

    function getCategoryProductByCustomer($data, $station_id=1, $language_id=2, $origin_id=1){
        //TODO
    }

    function getOnSaleProductByCustomer($data, $station_id=1, $language_id=2, $origin_id=1){
        //TODO
    }

    function addCustomer($data=array(), $station_id=1, $language_id=2, $origin_id=1){
        global $db,$dbm;

        //Required
        //Customer Data: email(from uid/phone), shipping_name, shipping_phone, uid
        //Address Data: customer_id, shipping_name, shipping_address(''), shipping_phone, pickupspot_id, area_id

        //UID is required
        if(!isset($data['uid']) || !$data['uid'] ){
            return false;
        }

        $bool = true;
        //$dbm->beginTransaction();

        $shipping_name = isset($data['shipping_name']) ? $data['shipping_name'] : '-';
        $shipping_phone = isset($data['shipping_phone']) ? $data['shipping_phone'] : '';
        $wx_subscribe_time = isset($data['event_createtime']) ? $data['event_createtime'] : '0000-00-00 00:00:00';
        $wx_subscribe_time_now = $wx_subscribe_time;
        $wx_merchant_qrscan_now = $wx_subscribe_time;
        $merchant_id = isset($data['event_key']) ? $data['event_key'] : 0;
        $merchant_id_now = $merchant_id;

        $sql = "INSERT INTO `oc_customer` (
            `customer_group_id`,
            `store_id`,
            `firstname`,
            `lastname`,
            `email`,
            `telephone`,
            `address_id`,
            `status`,
            `approved`,
            `date_added`,
            `uid`,
            `origin_id`,
            `wx_subscribe_time`,
            `wx_subscribe_time_now`,
            `wx_merchant_qrscan_now`,
            `merchant_id`,
            `merchant_id_now`
            ) VALUES";
        $sql .= "(1, 0, '".$db->escape($shipping_name)."', '-', '".$db->escape($data['uid']).DOMAIN."', '".$db->escape($data['shipping_phone'])."',0, 1, 1, NOW(), '".$data['uid']."', ".$origin_id.",'".$wx_subscribe_time."','".$wx_subscribe_time_now."','".$wx_merchant_qrscan_now."','".$merchant_id."','".$merchant_id_now."')";
        //return $sql;
        $bool = $bool && $dbm->query($sql);
        $data['customer_id'] = $dbm->getLastId();

        //Add customer address
        $shipping_address = isset($data['shipping_address']) ? $data['shipping_address'] : '';
        $pickupspot_id = isset($data['pickupspot_id']) ? $data['pickupspot_id'] : 0;
        $area_id = isset($data['area_id']) ? $data['area_id'] : 0;

        $sql = "INSERT INTO `oc_address` (`customer_id`, `firstname`, `lastname`, `company`, `address_1`, `address_2`, `city`, `postcode`, `country_id`, `zone_id`, `custom_field`, `default`, `status`, `name`, `shipping_phone`,`pickupspot_id`,`area_id`) VALUES";
        $sql .= "(".$data['customer_id'].", '".$db->escape($shipping_name)."', '-', '', '".$db->escape($shipping_address)."', '', '上海', '200000', 44, 708, '', 0, 1, '".$db->escape($shipping_name)."', '".$db->escape($shipping_phone)."',".$db->escape($pickupspot_id).",".$db->escape($area_id).")";
        //return $sql;
        $bool = $bool && $dbm->query($sql);
        //if ($bool) {
        //    $bool = $dbm->commit ();
        //} else {
        //    $dbm->rollBack ();
        //}

        if($bool){
            return $data['customer_id'];
        }

        return false;
    }

    function updateCustomerWXScanInfo($data=array(), $station_id=1, $language_id=2, $origin_id=1){
        global $db,$dbm;

        //Required
        //Customer Data: email(from uid/phone), shipping_name, shipping_phone, uid
        //Address Data: customer_id, shipping_name, shipping_address(''), shipping_phone,

        if(!isset($data['customer_id']) || !$data['customer_id']){
            return false;
        }

        $event_createtime = isset($data['event_createtime']) ? $data['event_createtime'] : '0000-00-00 00:00:00';
        $merchant_id_now = isset($data['event_key']) ? $data['event_key'] : 0;




        $bool = true;

        $sql = "UPDATE `oc_customer` SET ";
        if($data['event_type'] == 'scan'){
            $sql .= " `wx_merchant_qrscan_now` = '".$db->escape($event_createtime)."', ";
        }
        else{
            $sql .= " `wx_subscribe_time_now` = '".$db->escape($event_createtime)."', ";
        }
        $sql .= " `merchant_id_now`='".$db->escape($merchant_id_now)."' ";
        $sql .= " WHERE `customer_id` = ".$data['customer_id'];
        $bool = $bool && $dbm->query($sql);

        if($bool){
            return $data['customer_id'];
        }

        return false;
    }

    function addCustomerEvent($data, $origin_id=1){
        global $db,$dbm;

        $bool = true;
        $sql = "INSERT INTO `oc_customer_event` (`customer_id`, `uid`, `origin_id`, `even_type`, `even_key`, `event_createtime`, `date_added`)
                VALUES
                    (".$db->escape($data['customer_id']).", '".$db->escape($data['uid'])."', ".$origin_id.", '".$db->escape($data['event_type'])."', '".$db->escape($data['event_key'])."', '".$db->escape($data['event_createtime'])."', NOW());
                ";
        $bool = $bool && $dbm->query($sql);
        if($bool){
            return $data['customer_id'];
        }

        return false;
    }

    function addWXScan($data='', $station_id=1, $language_id=2, $origin_id=1){
        //oc_customer, oc_customer_address
        //oc_merchant
        //oc_customer_event

        //API get data: uid, event_createtime, event_type, event_key, event_data

        //Required
        //Customer Data: email(from uid/phone), shipping_name, shipping_phone, uid
        //Address Data: customer_id, shipping_name, shipping_address(''), shipping_phone, pickupspot_id, area_id
        $data = unserialize($data); //Needed data in array

        if( isset($data['uid']) ){
            $customer_id = $this->getCustomerIdByUid($data['uid']);
            $data['customer_id'] = $customer_id;

            //Log Event
            $this->addCustomerEvent($data, $origin_id);

            if(!$data['customer_id']){
                return $this->addCustomer($data, $station_id, $language_id, $origin_id);
            }
            else{
                return $this->updateCustomerWXScanInfo($data, $station_id, $language_id, $origin_id);
            }
        }

        return false;
    }

    function register(array $data){
        global $db, $dbm;
        $reg = $data['data'];
        $uid = isset($data['uid']) ? $data['uid'] : '';
        if($uid){
            $query = $db->query("SELECT customer_id, uid FROM oc_customer WHERE uid='" . $db->escape($uid) . "'");
            if($query->row && $query->row['customer_id']){
                return array(
                    'return_code' => 'FAIL',
                    'return_msg'  => '此微信号已经注册，请直接登录！'
                );
            }
        }

        if(!preg_match('/1\d{10}/', $reg['telephone'])){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '手机号码输入错误！'
            );
        }
        if(!preg_match('/\d{6}/', $reg['msg_code'])){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '验证码格式错误！'
            );
        }
        if(!preg_match('/\w{6,20}/', $reg['password'])){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请输入6-20位数字或字母！'
            );
        }
        if(!preg_match('/\w{6}/', $reg['bd_code'])){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请输入6位授权码！'
            );
        }

        $query_customer = $db->query("SELECT customer_id, uid FROM oc_customer WHERE telephone='" . $db->escape($reg['telephone']) . "'");
        if($query_customer->row && $query_customer->row['customer_id']){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '此手机号已经注册，请直接登录！'
            );
        }

        $query_msg_code = $db->query("SELECT code FROM oc_x_msg_valid WHERE phone='" . $db->escape($reg['telephone']) . "' AND expiration>unix_timestamp()");
        if(!$query_msg_code->num_rows || $query_msg_code->row['code'] != $reg['msg_code']){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '验证码错误，请重试！'
            );
        }

        $query_bd_code = $db->query("SELECT bd_id,bd_code FROM oc_x_bd WHERE bd_code='" . $db->escape($reg['bd_code']) . "' AND status=1");
        if(!$query_bd_code->num_rows){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '授权码错误'
            );
        }

        if(!$reg['firstname'] || !$reg['merchant_name'] || !$reg['merchant_address']){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请输入用户名、商户名、商户地址！'
            );
        }

        if(!isset($reg['area_id'])){ $reg['area_id'] = 0; }

        $salt = mt_rand(100000000,999999999);

        $sql  = "INSERT INTO oc_customer SET ";
        $sql .= " customer_group_id=1,";
        $sql .= " firstname='" . $dbm->escape($reg['firstname']) . "',";
        $sql .= " telephone='" . $dbm->escape($reg['telephone']) . "',";
        $sql .= " salt='" . $salt . "',";
        $sql .= " password='" . md5($salt . $reg['password']) . "',";
        $sql .= " uid='" . $dbm->escape($uid) . "',";
        $sql .= " merchant_name='" . $dbm->escape($reg['merchant_name']) . "',";
        $sql .= " merchant_address='" . $dbm->escape($reg['merchant_address']) . "',";
        $sql .= " bd_id='" . $dbm->escape($query_bd_code->row['bd_id']) . "',";
        $sql .= " ori_bd_id='" . $dbm->escape($query_bd_code->row['bd_id']) . "',";
        $sql .= " bd_code='" . $dbm->escape($query_bd_code->row['bd_code']) . "',";
        $sql .= " status=1,";
        $sql .= " approved=1,";
        $sql .= " area_id=" . $dbm->escape($reg['area_id']) . ",";
        $sql .= " date_added=NOW()";
        $dbm->query($sql);
        $customer_id = $dbm->getLastId();

        //添加地址数据
        $sql = "INSERT INTO `oc_address` (`customer_id`, `firstname`, `lastname`, `company`, `address_1`, `address_2`, `city`, `postcode`, `country_id`, `zone_id`, `custom_field`, `default`, `status`, `name`, `shipping_phone`, `area_id`) VALUES";
        $sql .= "(".$customer_id.", '".$dbm->escape($reg['merchant_name'])."', '-', '', '".$dbm->escape($reg['merchant_address'])."', '', '上海', '200000', 44, 708, '', 1, 1, '".$dbm->escape($reg['merchant_name'])."', '".$dbm->escape($reg['telephone'])."',". $dbm->escape($reg['area_id']) .")";
        $dbm->query($sql);

        //根据新用户规则添加优惠券，仅限“新客户是否可以使用”，属性new_customer为1
        $sql = "INSERT INTO `oc_coupon_customer` (`coupon_id`, `customer_id`, `times`, `date_start`, `date_end`, `status`)
                SELECT coupon_id, '".$customer_id."', times, date_add(current_date(), interval reserve_days day) date_start,  date_add(current_date(), interval valid_days+reserve_days-1 day) date_end, 1 'status' from oc_coupon
                WHERE new_customer = 1 and current_date() between date_start and date_end and status = 1";
        $dbm->query($sql);


        return array(
            'return_code' => 'SUCCESS',
            'return_msg'  => '注册成功',
            'customer_id' => $customer_id
        );
    }

    function login(array $data){
        global $dbm;
        $uid = isset($data['uid']) ? $data['uid'] : '';
        $login = $data['data'];
        $query = $dbm->query("SELECT customer_id,salt,password FROM oc_customer WHERE status =1 and telephone='" . $dbm->escape($login['telephone']) . "'");
        if($query->row && $query->row['customer_id']){
            $password = md5($query->row['salt'] . $login['password']);
            if($password == $query->row['password']){
                $dbm->query("UPDATE oc_customer SET uid='" . $dbm->escape($data['uid']) . "' WHERE customer_id='" . $query->row['customer_id'] . "'");
                return array(
                    'return_code' => 'SUCCESS',
                    'return_msg'  => '登录成功',
                    'customer_id' => $query->row['customer_id']
                 );
            }
        }

        return array(
            'return_code' => 'FAIL',
            'return_msg'  => '登录失败'
        );
    }

    function logout(array $data){
        global $dbm;
        $uid = isset($data['uid']) ? $data['uid'] : '';
        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? $data['customer_id'] : '';

        if($uid && $customer_id){
            $sql = "UPDATE oc_customer SET uid=concat(uid,'@',customer_id) WHERE customer_id='" . $customer_id . "' and uid='". $uid ."'";
            $dbm->query($sql);
            return array(
                'return_code' => 'SUCCESS',
                'return_msg'  => '已登出'
            );
        }

        return array(
            'return_code' => 'FAIL',
            'return_msg'  => '账户错误，请联系我们'
        );
    }

    function paymentCycle(array $data){
        global $dbm, $db;
        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? $data['customer_id'] : '';
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求账期错误'
            );
        }

        $query = $db->query("SELECT payment_cycle FROM oc_customer WHERE customer_id='" . (int)$customer_id . "'");
        if($query->row['payment_cycle']){
            $payment_cycle_status = '1';
            $query_cycle = $db->query("SELECT * FROM oc_x_customer_payment_cycle WHERE customer_id='" . (int)$customer_id . "'");
            $query_bill = $db->query("SELECT COUNT(bill_id) AS not_pay_bill_num, SUM(amount) AS not_pay_bill_amount FROM oc_x_customer_bill WHERE customer_id='" . (int)$customer_id . "' AND payment_status=0");
            $query_bill_detail = $db->query("SELECT SUM(amount) current_amount  FROM oc_x_customer_bill_detail WHERE customer_id='" . (int)$customer_id . "' AND status=1 AND bill_create_status=0");
            if($query_bill->row['not_pay_bill_num']>$query_cycle->row['bill_limit']){
                $payment_cycle_status = '0';
                $payment_cycle_notice = '未支付账单数已超过上限';
            }
            if(($query_bill->row['not_pay_bill_amount'] + $query_bill_detail->row['current_amount']) > $query_cycle->row['amount_limit']){
                $payment_cycle_status = '0';
                $payment_cycle_notice = '未支付订单金额已超过上限';
            }
            return array(
                'return_code'         => 'SUCCESS',
                'return_msg'          => '成功获取账期信息',
                'payment_cycle'       => $query_cycle->row,                         // 当前用户付款周期规则
                'not_pay_bill_num'    => $query_bill->row['not_pay_bill_num'],      // 未支付账单数量
                'not_pay_bill_amount' => $query_bill->row['not_pay_bill_amount'],   // 未支付账单总金额
                'current_amount'      => (float)$query_bill_detail->row['current_amount'], // 未生成账单交易总金额
                'payment_cycle_open'  => '1',                                       // 账单支付是否启用
                'payment_cycle_status'=> $payment_cycle_status,                     // 账单支付是否可用
                'payment_cycle_notice'=> $payment_cycle_notice                      // 当payment_cycle_status=0时提醒信息
            );
        }

        return array(
            'return_code'         => 'SUCCESS',
            'return_msg'          => '成功获取账期信息',
            'payment_cycle_open'  => '0',                                           // 账单支付未开启
        );
    }

    function getBdAreaByBdCode(array $data){
        global $db;
        $bdCode = isset($data['data']['bdCode']) ? $data['data']['bdCode'] : '';
        if( empty($bdCode) ){ return array(); }

        $sql    = "SELECT bd_id, bd_code, area_control FROM oc_x_bd WHERE bd_code='" . $db->escape($bdCode) . "' AND status=1 LIMIT 1";
        $query  = $db->query($sql);
        $result = $query->rows;
        if(!$result){ return array(); }

        $sql        = "SELECT area_id, name, city, district FROM oc_x_area WHERE status = 1";
        if($result[0]['area_control'] == 1){
            $sql .= " AND bd_id =".$result[0]['bd_id'];
        }

        $query      = $db->query($sql);
        if($results = $query->rows){
            return $results;
        }
        return array();
    }

    //$customer_id, $station_id=1, $language_id=2, $origin_id=1
    function orderList(array $data){
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? $data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求账期错误'
            );
        }
        $post = $data['data'];
        $page = isset($post['page']) ? (int)$post['page'] : 1;
        $page_size = isset($post['page_size']) ? (int)$post['page_size'] : 5;
        $start = ($page-1)*$page_size;
        $limit = $page_size;
        $language_id = 2;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 1;

        $sql = "SELECT A.orderid, A.order_id, A.orderstamp, A.shipping_method, A.shipping_code,
            round(A.total,2) total, round(A.credit_paid,2) credit_paid, round(A.sub_total+A.shipping_fee+A.discount_total+A.balance_container_deposit,2) order_total,
            round(A.balance_container_deposit,2) balance_container_deposit, if(sum(T.value)<0, 0, round(sum(T.value),2) ) due,
            A.deliver_date, A.shipping_name, left(A.date_added,10) order_date, A.sub_total, A.shipping_fee, A.shipping_address_1,
            A.order_status_id, A.order_payment_status_id, A.order_deliver_status_id, B.name order_status, C.name order_payment_status, D.name order_deliver_status,
            A.payment_method, A.payment_code, PS.name ps_name, PS.address ps_address, LA.logistic_driver_title, LA.logistic_driver_phone
            FROM oc_order A
            LEFT JOIN oc_order_status B ON A.order_status_id = B.order_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_total T ON A.order_id = T.order_id AND T.accounting = 1
            LEFT JOIN oc_order_payment_status C ON A.order_payment_status_id = C.order_payment_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id  = D.order_deliver_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_x_pickupspot PS ON A.pickupspot_id  = PS.pickupspot_id
            LEFT JOIN oc_x_logistic_allot_order LAO ON A.order_id = LAO.order_id
            LEFT JOIN oc_x_logistic_allot LA ON LAO.logistic_allot_id =LA.logistic_allot_id
            WHERE A.type = 1 AND A.customer_id = {$customer_id} AND A.station_id = {$station_id}
            GROUP BY T.order_id
            ORDER BY order_id DESC LIMIT $start, $limit";

        $query = $db->query($sql);
        return array(
            'return_code' => 'SUCCESS',
            'order_list'  => $query->rows,
            'order_num'   => count($query->rows)
        );
    }

    // 用户订单[ 缓存 ]
    function orderListWithCache(array $data)
    {
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? $data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求账期错误'
            );
        }
        $post        = $data['data'];
        $page        = isset($post['page']) ? (int)$post['page'] : 1;
        $pageSize    = isset($post['page_size']) ? (int)$post['page_size'] : 5;
        $language_id = 2;
        $station_id  = isset($data['station_id']) ? (int)$data['station_id'] : 1;

        // 获取Redis缓存
        $results = array();
        $start   = ( $page - 1 ) * $pageSize;
        $end     = $page * $pageSize - 1;

        $orderListKey = $this->getOrderListKey($station_id, $customer_id);
        $orderLen     = $this->redis->llen($orderListKey);
        if($orderLen){
            $orderIds = $this->redis->lrange($orderListKey, $start, $end);
            foreach($orderIds as $orderId){
                $orderKey  = $this->getOrderKey($station_id, $orderId);
                $orderInfo = $this->redis->hgetall($orderKey);

                // 获取订单各状态
                $orderStatusInfo = $this->getOrderStatusWithCache(array('stationId' => $station_id, 'orderId' => $orderId));
                if(sizeof($orderStatusInfo)) {
                    foreach($orderStatusInfo as $key => $value){ $orderInfo[$key] = $value; }
                }

                $results[] = $orderInfo;
            }

            return array('return_code' => 'SUCCESS', 'order_list' => $results, 'order_num' => count($results));
        }

        $sql = "SELECT A.orderid, A.order_id, A.orderstamp, A.shipping_method, A.shipping_code,
            round(A.total,2) total, round(A.credit_paid,2) credit_paid, round(A.sub_total+A.shipping_fee+A.discount_total+A.balance_container_deposit,2) order_total,
            round(A.balance_container_deposit,2) balance_container_deposit, if(sum(T.value)<0, 0, round(sum(T.value),2) ) due,
            A.deliver_date, A.shipping_name, left(A.date_added,10) order_date, A.sub_total, A.shipping_fee, A.shipping_address_1,
            A.order_status_id, A.order_payment_status_id, A.order_deliver_status_id, B.name order_status, C.name order_payment_status, D.name order_deliver_status,
            A.payment_method, A.payment_code, PS.name ps_name, PS.address ps_address, LA.logistic_driver_title, LA.logistic_driver_phone
            FROM oc_order A
            LEFT JOIN oc_order_status B ON A.order_status_id = B.order_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_total T ON A.order_id = T.order_id AND T.accounting = 1
            LEFT JOIN oc_order_payment_status C ON A.order_payment_status_id = C.order_payment_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id  = D.order_deliver_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_x_pickupspot PS ON A.pickupspot_id  = PS.pickupspot_id
            LEFT JOIN oc_x_logistic_allot_order LAO ON A.order_id = LAO.order_id
            LEFT JOIN oc_x_logistic_allot LA ON LAO.logistic_allot_id =LA.logistic_allot_id
            WHERE A.type = 1 AND A.customer_id = {$customer_id} AND A.station_id = {$station_id}
            GROUP BY T.order_id
            ORDER BY order_id DESC";

        $query  = $db->query($sql);
        $result = $query->rows;

        // 生成缓存
        $return = array();
        if($result && sizeof($result)){
            foreach($result as $key => $value){
                $this->setOrderAndStatusCache($station_id, $value);
                $this->redis->rpush($orderListKey, $value['order_id']);
                if(($start<=$key) && ($key<=$end)){ $return[] = $value; }
            }
            $this->redis->expire($orderListKey, $this->orderTime);
        }

        return array('return_code' => 'SUCCESS', 'order_list' => $return, 'order_num' => count($return));
    }

    // 用户可退货订单列表
    function getCanReturnOrderList(array $data){
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? $data['customer_id'] : 0;
        if(!$customer_id){
            return array('return_code' => 'FAIL', 'return_msg'  => '请求账期错误');
        }
        $post           = $data['data'];
        $page           = isset($post['page']) ? (int)$post['page'] : 1;
        $page_size      = isset($post['page_size']) ? (int)$post['page_size'] : 5;
        $start          = ($page-1) * $page_size;
        $limit          = $page_size;
        $language_id    = 2;
        //$station_id     = isset($data['station_id']) ? (int)$data['station_id'] : 1;

        $sql = "SELECT A.orderid, A.order_id, A.orderstamp, A.shipping_method, A.shipping_code,
            round(A.total,2) total, round(A.credit_paid,2) credit_paid, round(A.sub_total+A.shipping_fee+A.discount_total+A.balance_container_deposit,2) order_total,
            round(A.balance_container_deposit,2) balance_container_deposit, if(sum(T.value)<0, 0, round(sum(T.value),2) ) due,
            A.deliver_date, A.shipping_name, left(A.date_added,10) order_date, A.sub_total, A.shipping_fee, A.shipping_address_1,
            A.order_status_id, A.order_payment_status_id, A.order_deliver_status_id, B.name order_status, C.name order_payment_status, D.name order_deliver_status,
            A.payment_method, A.payment_code, PS.name ps_name, PS.address ps_address, LA.logistic_driver_title, LA.logistic_driver_phone
            FROM oc_order A
            LEFT JOIN oc_order_status B ON A.order_status_id = B.order_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_total T ON A.order_id = T.order_id AND T.accounting = 1
            LEFT JOIN oc_order_payment_status C ON A.order_payment_status_id = C.order_payment_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id  = D.order_deliver_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_x_pickupspot PS ON A.pickupspot_id  = PS.pickupspot_id
            LEFT JOIN oc_x_logistic_allot_order LAO ON A.order_id = LAO.order_id
            LEFT JOIN oc_x_logistic_allot LA ON LAO.logistic_allot_id =LA.logistic_allot_id
            WHERE A.type = 1 AND A.customer_id = {$customer_id}
            AND A.order_status_id in (6,10)
            AND A.order_deliver_status_id in (2,3,7)
            AND A.deliver_date between date_sub(current_date(), interval 7 day) and date_add(current_date(), interval 1 day)
            GROUP BY T.order_id
            ORDER BY order_id DESC LIMIT $start, $limit";
        // AND A.station_id = {$station_id}

        $query = $db->query($sql);

        return array('return_code' => 'SUCCESS', 'order_list' => $query->rows, 'order_num' => count($query->rows));
    }

    // 更改Redis订单状态
    function changeCacheOrderStatus(array $data)
    {
        $stationId     = isset($data['station_id'])              ? (int)$data['station_id']         : 0;
        $orderId       = isset($data['data']['order_id'])        ? (int)$data['data']['order_id']   : 0;
        $orderStatus   = isset($data['data']['order_status'])    ? $data['data']['order_status']    : '';
        $orderStatusId = isset($data['data']['order_status_id']) ? $data['data']['order_status_id'] : 0;
        if($orderId < 0 || $stationId < 0 ){ return false; }

        $orderStatusKey      = $this->getOrderStatusKey($stationId, $orderId);
        if($this->redis->hlen($orderStatusKey)){
            if(!empty($orderStatus))  { $this->redis->hset($orderStatusKey, 'order_status', $orderStatus); }
            if(!empty($orderStatusId)){ $this->redis->hset($orderStatusKey, 'order_status_id', $orderStatusId); }

            $this->redis->expire($orderStatusKey, $this->orderStatusTime);
        }

        return true;
    }

    // 清除订单缓存 & 订单详情缓存列表
    function clearOrderListCache(array $data)
    {
        $stationId     = (int)$data['station_id'];
        $customerId    = (int)$data['customer_id'];
        if($stationId < 0 || $customerId < 0 ){ return false; }

        $orderListKey       = $this->getOrderListKey($stationId, $customerId);
        $orderDetailListKey = $this->getOrderDetailListKey($stationId, $customerId);
        if( $this->redis->llen($orderListKey) ){
            $this->redis->del($orderListKey);
        }
        if( $this->redis->llen($orderDetailListKey) ){
            $this->redis->del($orderDetailListKey);
        }

        return true;
    }

    // 获取订单Status缓存
    function getOrderStatusWithCache(array $data)
    {
        global $db;
        $stationId  = !empty($data['stationId'])  ? (int)$data['stationId']  : 0;
        $orderId    = !empty($data['orderId'])    ? (int)$data['orderId']    : 0;
        if($orderId < 0 || $stationId < 0){ return array(); }

        $orderStatusKey = $this->getOrderStatusKey($stationId, $orderId);
        if($this->redis->hlen($orderStatusKey)) {
            $orderStatusInfo = $this->redis->hgetall($orderStatusKey);
            return $orderStatusInfo;
        }

        $sql = "SELECT A.order_status_id,A.order_payment_status_id,A.order_deliver_status_id,B.name order_status,C.name order_payment_status,D.name order_deliver_status
            FROM oc_order A
            LEFT JOIN oc_order_status B ON A.order_status_id = B.order_status_id
            LEFT JOIN oc_order_payment_status C ON A.order_payment_status_id = C.order_payment_status_id
            LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id  = D.order_deliver_status_id
            WHERE A.type = 1 AND A.order_id = {$orderId} AND A.station_id = {$stationId}";
        $query  = $db->query($sql);
        $result = $query->rows;

        if($result && sizeof($result)){
            $orderStatusKey = $this->getOrderStatusKey($stationId, $orderId);
            foreach($result[0] as $key => $value){
                $this->redis->hset($orderStatusKey, $key, $value);
            }
            $this->redis->expire($orderStatusKey, $this->orderStatusTime);

            return $result[0];
        }
        return array();
    }

    // 生成订单 & 订单状态缓存
    function setOrderAndStatusCache($stationId, array $data)
    {
        $orderId   = !empty($data['order_id']) ? (int)$data['order_id'] : 0;
        $stationId = (int)$stationId;
        if($stationId < 0 || !sizeof($data) || $orderId < 0 ){ return false; }

        $statusArray    = array('order_status_id', 'order_payment_status_id', 'order_deliver_status_id', 'order_status', 'order_payment_status', 'order_deliver_status');
        $orderKey       = $this->getOrderKey($stationId, $orderId);
        $orderStatusKey = $this->getOrderStatusKey($stationId, $orderId);
        foreach($data as $k => $v){
            $this->redis->hsetnx($orderKey, $k, $v);
            if(in_array($k, $statusArray)){ $this->redis->hset($orderStatusKey, $k, $v); }
        }
        $this->redis->expire($orderKey, $this->orderTime);
        $this->redis->expire($orderStatusKey, $this->orderStatusTime);
        return true;
    }

    // 清除单个订单的状态
    function clearOrderStatusCache(array $data)
    {
        $orderId   = !empty($data['data']['order_id']) ? (int)$data['data']['order_id'] : 0;
        $stationId = !empty($data['station_id'])       ? (int)$data['station_id']       : 0;
        if($stationId < 0 || $orderId < 0 ){ return false; }

        $orderStatusKey = $this->getOrderStatusKey($stationId, $orderId);
        if($this->redis->hlen($orderStatusKey)){
            $this->redis->del($orderStatusKey);
        }

        return true;
    }

    // 获取用户区域id获取仓库id
    function getWarehouseIdByAreaId(array $data)
    {
        global $db;

        $area_id    = !empty($data['data']['area_id']) ? (int)$data['data']['area_id'] : 0;
        $station_id = !empty($data['station_id']) ? (int)$data['station_id'] : 0;
        if(!$area_id){ return false; }

        $sql    = "SELECT warehouse_id FROM oc_x_area_warehouse WHERE status = 1 AND area_id = ".$area_id. " AND station_id = ".$station_id." LIMIT 1";
        $query  = $db->query($sql);
        $result = $query->row;

        if($result && sizeof($result)){
            return $result;
        }

        return false;
    }


    function creditDetail(array $data){
        //Expected Data: customer_id, query_type[date_gap:[7 day], date_range:[from,to], all]
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        //Check Agent
        if($this->checkAgentCustomer($customer_id)){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '账户不可使用余额'
            );
        }

        $post = $data['data'];
        $page = isset($post['page']) ? (int)$post['page'] : 1;
        $page_size = isset($post['page_size']) ? (int)$post['page_size'] : 20;
        $start = ($page-1)*$page_size;
        $limit = $page_size;
        // $language_id = 2;
        $sql = "SELECT customer_transaction_id,customer_id,order_id,description,round(amount,2) amount,date(date_added) adate, date_added
                    FROM oc_customer_transaction
                    WHERE customer_id='" . $customer_id . "' ORDER BY customer_transaction_id DESC LIMIT $start, $limit";
        $query = $db->query($sql);
        $credit_list = $query->rows;

        $credit_total = 0;
        $sql = "SELECT if(sum(amount) is null, 0, round(sum(amount),2)) total_amount
                FROM oc_customer_transaction
                WHERE customer_id='" . $customer_id . "'";
        $query = $db->query($sql);
        if($query->row){
            $credit_total_raw_data = $query->row;
            $credit_total = $credit_total_raw_data['total_amount'];
        }

        $return = array(
            'return_code' => 'SUCCESS',
            'credit_list'   => $credit_list,
            'credit_total' => $credit_total,
            'credit_rows' => count($credit_list)
        );

        return $return;
    }

    function getCreditTotal(array $data){
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        //Check Agent
        if($this->checkAgentCustomer($customer_id)){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '账户不可使用余额'
            );
        }

        $credit_total = 0;
        $sql = "SELECT if(sum(amount) is null, 0, round(sum(amount),2)) total_amount
                FROM oc_customer_transaction
                WHERE customer_id='" . $customer_id . "'";
        $query = $db->query($sql);
        if($query->row){
            $creditTotalRaw = $query->row;
            $credit_total = $creditTotalRaw['total_amount'];
        }

        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
            'return_data' => array(
                'credit_total' => $credit_total
            )
        );

        return $return;
    }


    function getCreditDetail(array $data){
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        //Check Agent
        if($this->checkAgentCustomer($customer_id)){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '账户不可使用余额'
            );
        }

        $post = $data['data'];
        $page = isset($post['page']) ? (int)$post['page'] : 1;
        $page_size = isset($post['page_size']) ? (int)$post['page_size'] : 20;
        $start = ($page-1)*$page_size;
        $limit = $page_size;

        $data_start = isset($post['date_start']) ? $post['date_start'] : false;
        $data_end = isset($post['date_end']) ? $post['date_end'] : false;

        //Get Credit List
        $sql = "
        select
            A.customer_transaction_id id,
            B.customer_transaction_type_id type_id,
            B.name type,
            round(A.amount,2) amount,
            A.order_id,
            date(A.date_added) adate,
            A.date_added,
            A.return_id,
            A.change_id,
            concat('#',A.customer_transaction_id,': ',A.description) description
        from oc_customer_transaction A
        left join oc_customer_transaction_type B on (A.customer_transaction_type_id = B.customer_transaction_type_id)
        where A.customer_id = '".$customer_id."' and date(A.date_added) between '".$data_start."' and '".$data_end."' limit ".$start.", ".$limit." ";

        $query = $db->query($sql);
        $creditListRaw = $query->rows;

        //Calc Credi List Total && Get Reurn ID | Change ID
        $credit_list_total = 0;
        $listReturnIds = array();
        $listChangeIds = array();
        foreach($creditListRaw as $m){
            //$credit_list_total += $m['amount'];

            if($m['return_id']){
                $listReturnIds[] = $m['return_id'];
            }
            if($m['change_id']){
                $listChangeIds[] = $m['change_id'];
            }
        }

        //Get Order Return info if exists
        if(sizeof($listReturnIds)){
            $sql = "select return_id, product_id, product product_name, quantity, price, total, round(return_product_credits,2) credits
            from oc_return_product where return_id in (".implode(',',$listReturnIds).")";

            $query = $db->query($sql);
            $creditReturnListRaw = $query->rows;

            $credit_return_list = array();
            foreach($creditReturnListRaw as $m){
                $credit_return_list[$m['return_id']][] = $m;
            }
        }

        //Get Order Change info if exists
        if(sizeof($listChangeIds)){
            $sql = "select CP.change_id, CP.product_id, P.name product_name, CP.quantity,  CP.price,  CP.total,  CP.weight_total,  CP.weight_change, round(CP.change_product_credits,2) credits
                    from oc_order_change_product CP
                    left join oc_product_description P on CP.product_id = P.product_id
                    where CP.change_id in (".implode(',',$listChangeIds).")";

            $query = $db->query($sql);
            $creditChangeListRaw = $query->rows;

            $credit_change_list = array();
            foreach($creditChangeListRaw as $m){
                $credit_change_list[$m['change_id']][] = $m;
            }
        }

        //Finally, Combine Credit List Data
        $credit_list = array();
        foreach($creditListRaw as $m){
            if($m['return_id']){
                $m['return_list'] = $credit_return_list[$m['return_id']];
            }
            if($m['change_id']){
                $m['change_list'] = $credit_change_list[$m['change_id']];
            }
            $credit_list[] = $m;
        }

        //Get period credit total
        $credit_total = 0;
        $sql = "SELECT if(sum(A.amount) is null, 0, round(sum(A.amount),2)) total_amount
                FROM oc_customer_transaction A
                where A.customer_id = '".$customer_id."' and date(A.date_added) between '".$data_start."' and '".$data_end."'";
        $query = $db->query($sql);
        if($query->row){
            $creditPeriodTotalRaw = $query->row;
            $credit_list_total = $creditPeriodTotalRaw['total_amount'];
        }

        $return_code = 'SUCCESS';
        $return_msg = 'OK';
        if(!count($credit_list)){
            $return_msg = 'NO RECORD';
        }

        $return_data = array(
            'credit_list'   => $credit_list,
            'credit_list_total' => $credit_list_total,
            'credit_rows' => count($credit_list)
        );

        return array(
            'return_code' => $return_code,
            'return_msg' => $return_msg,
            'return_data' => $return_data
        );
    }

    function getCreditTotalByType(array $data){
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        //Check Agent
        if($this->checkAgentCustomer($customer_id)){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '账户不可使用余额'
            );
        }

        $post = $data['data'];
        $type_id = isset($post['type_id']) ? (int)$post['type_id'] : 0;

        $credit_total = 0;
        $type_name = '';
        $sql = "SELECT A.customer_id, B.name type_name, sum(A.amount) total_amount FROM oc_customer_transaction A
                LEFT JOIN oc_customer_transaction_type B on A.customer_transaction_type_id = B.customer_transaction_type_id
                WHERE A.customer_transaction_type_id = '".$type_id."' and A.customer_id ='" . $customer_id . "'";
        $query = $db->query($sql);
        if($query->row){
            $creditTotalRaw = $query->row;
            $type_name = $creditTotalRaw['type_name'];
            $credit_total = $creditTotalRaw['total_amount'];
        }

        $return_code = 'SUCCESS';
        $return_msg = 'OK';
        $return_data = array(
            //'type_name' => $type_name,
            'credit_total' => $credit_total
        );

        return array(
            'return_code' => $return_code,
            'return_msg' => $return_msg,
            'return_data' => $return_data
        );
    }

    function getRewardTotal(array $data){

//        return array(
//            'return_code' => 'SUCCESS',
//            'return_msg' => 'OK',
//            'return_data' => array('total'=>0)
//        );

        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        //Check Agent
        if($this->checkAgentCustomer($customer_id)){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '账户不可使用余额'
            );
        }

        $credit_total = 0;
        $sql = "SELECT if(sum(points) is null, 0, round(sum(points),2)) points
                FROM oc_customer_reward
                WHERE customer_id='" . $customer_id . "'";
        $query = $db->query($sql);
        if($query->row){
            $rewardTotalRaw = $query->row;
            $rewardTotal = $rewardTotalRaw['points'];
        }

        return array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
            'return_data' => array(
                'reward_total' => $rewardTotal,
                'points_payment_account' => $rewardTotal/POINTS_TO_PAYMENT_RATE,
            )
        );

    }

    function getRewardDetail(array $data){
        //查询间隔不超过7天

        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        //Check Agent
        if($this->checkAgentCustomer($customer_id)){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '账户不可使用积分'
            );
        }

        $post = $data['data'];
        $page = isset($post['page']) ? (int)$post['page'] : 1;
        $page_size = isset($post['page_size']) ? (int)$post['page_size'] : 5;
        $start = ($page-1)*$page_size;
        $limit = $page_size;

        $data_start = isset($post['date_start']) ? $post['date_start'] : false;
        $data_end = isset($post['date_end']) ? $post['date_end'] : false;

        //Get Credit List
        $sql = "
            select
                A.customer_reward_id id,
                B.name 'type',
                A.points,
                A.order_id,
                A.date_added,
                A.description
                from oc_customer_reward A
                left join oc_reward B on A.reward_id = B.reward_id
            where A.customer_id = '".$customer_id."' and date(A.date_added) between '".$data_start."' and '".$data_end."'
            order by A.date_added desc
            limit ".$start.", ".$limit." ";

        $query = $db->query($sql);
        $reward_list = $query->rows;

        //Get period credit total
        $reward_list_total = 0;
        $sql = "SELECT if(sum(A.points) is null, 0, sum(A.points)) total_amount
                FROM oc_customer_reward A
                where A.customer_id = '".$customer_id."' and date(A.date_added) between '".$data_start."' and '".$data_end."'";
        $query = $db->query($sql);
        if($query->row){
            $rewardPeriodTotalRaw = $query->row;
            $reward_list_total = $rewardPeriodTotalRaw['total_amount'];
        }

        $return_code = 'SUCCESS';
        $return_msg = 'OK';
        if(!count($reward_list)){
            $return_msg = 'NO RECORD';
        }

        $return_data = array(
            'reward_list'   => $reward_list,
            'reward_list_total' => $reward_list_total,
            'reward_rows' => count($reward_list)
        );

        return array(
            'return_code' => $return_code,
            'return_msg' => $return_msg,
            'return_data' => $return_data
        );
    }

    function billList(array $data){
        global $db;
        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }
        $query = $db->query("SELECT bill_id,date_added FROM oc_x_customer_bill WHERE customer_id=" . $customer_id . " LIMIT 24");
        $result = array();
        foreach($query->rows as $row){
            $result[] = array(
                'bill_id' => $row['bill_id'],
                'date'    => substr($row['date_added'], 0, 10)
            );
        }
        return array(
            'return_code' => 'SUCCESS',
            'bill_list'   => $result,
            'bill_num'    => count($result)
        );
    }

    function billDetailCurrent(array $data){
        global $db;
        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }
        $query = $db->query("SELECT `type`,`related_id`,`amount`,`date_added` FROM oc_x_customer_bill_detail WHERE customer_id=" . $customer_id . " AND bill_create_status=0");
        $result = array();
        foreach($query->rows as $row){
            switch($row['type']){
                case 'order' : $type_name='订单';break;
                case 'adjust': $type_name='调整';break;
                case 'refund': $type_name='退款';break;
            }
            $result[] = array(
                'type'       => $row['type'],
                'type_name'  => $type_name,
                'related_id' => $row['related_id'],
                'amount'     => $row['amount'],
                'date_added' => $row['date_added']
            );
        }

        $query_total_amount = $db->query("SELECT SUM(amount) AS total_amount FROM oc_x_customer_bill_detail WHERE customer_id=" . $customer_id . " AND status=1 AND bill_create_status=0");
        $query_next_date = $db->query("SELECT next_date FROM oc_x_customer_payment_cycle WHERE customer_id=" . $customer_id);
        return array(
            'return_code'   => 'SUCCESS',
            'detail_list'   => $result,
            'detail_num'    => count($result),
            'total_amount'  => $query_total_amount->row['total_amount'],
            'next_date'     => $query_next_date->row['next_date']
        );
    }

    function billDetail(array $data){
        global $db;
        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        $bill_id = (int)$data['data']['bill_id'];
        if(!$customer_id || !$bill_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        $query = $db->query("SELECT `type`,`related_id`,`amount`,`date_added` FROM oc_x_customer_bill_detail WHERE customer_id=" . $customer_id . " AND status=1 AND bill_create_status=1 AND bill_id=" . $bill_id);
        $result = array();
        foreach($query->rows as $row){
            switch($row['type']){
                case 'order' : $type_name='订单';break;
                case 'adjust': $type_name='调整';break;
                case 'refund': $type_name='退款';break;
            }
            $result[] = array(
                'type'       => $row['type'],
                'type_name'  => $type_name,
                'related_id' => $row['related_id'],
                'amount'     => $row['amount'],
                'date_added' => $row['date_added']
            );
        }

        $query_bill = $db->query("SELECT * FROM oc_x_customer_bill WHERE customer_id=" . $customer_id . " AND bill_id=" . $bill_id);
        return array(
            'return_code'   => 'SUCCESS',
            'detail_list'   => $result,
            'detail_num'    => count($result),
            'bill_id' => $query_bill->row['bill_id'],
            'amount'  => $query_bill->row['amount'],
            'date_added'     => $query_bill->row['date_added'],
            'payment_status' => $query_bill->row['payment_status'],
            'payment_status_name' => $query_bill->row['payment_status'] ? '已支付' : '未支付',
            'payment_method' => $query_bill->row['payment_method'],
            'payment_code'   => $query_bill->row['payment_code']
        );
    }

    function myAccount(array $data){
        global $db;
        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        // 获取商户基本信息
        $query_customer = $db->query("SELECT firstname,telephone,merchant_name,merchant_address,payment_cycle FROM oc_customer WHERE customer_id=" . $customer_id);
        $result = array(
            'return_code'  => 'SUCCESS',
            'firstname'    => $query_customer->row['firstname'],
            'telephone'    => $query_customer->row['telephone'],
            'merchant_name'=> $query_customer->row['merchant_name'],
            'merchant_address' => $query_customer->row['merchant_address'],
            'payment_cycle' => $query_customer->row['payment_cycle']
        );

        // 判断商户是否开启账期
        if($query_customer->row['payment_cycle']){
            $query_payment_cycle = $db->query("SELECT * FROM oc_x_customer_payment_cycle WHERE customer_id=" . $customer_id);
            $result['payment_cycle_detail'] = array(
                'week'         => $query_payment_cycle->row['week'],
                'amount_limit' => $query_payment_cycle->row['amount_limit'],
                'bill_limit'   => $query_payment_cycle->row['bill_limit'],
                'before_date'  => $query_payment_cycle->row['before_date'],
                'next_date'    => $query_payment_cycle->row['next_date']
            );
        }

        return $result;

    }

    function myAccountWithCache(array $data){
        global $db;
        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array('return_code' => 'FAIL', 'return_msg' => '请求错误');
        }

        // 获取缓存
        $myAccountKey = $this->getMyAccountKey($customer_id);
        if( $this->redis->exists($myAccountKey) ){
            $result = $this->redis->get($myAccountKey);
            return json_decode($result);
        }

        // 获取商户基本信息
        $query_customer = $db->query("SELECT firstname,telephone,merchant_name,merchant_address,payment_cycle FROM oc_customer WHERE customer_id=" . $customer_id);
        $result = array(
            'return_code'  => 'SUCCESS',
            'firstname'    => $query_customer->row['firstname'],
            'telephone'    => $query_customer->row['telephone'],
            'merchant_name'=> $query_customer->row['merchant_name'],
            'merchant_address' => $query_customer->row['merchant_address'],
            'payment_cycle' => $query_customer->row['payment_cycle']
        );

        // 判断商户是否开启账期
        if($query_customer->row['payment_cycle']){
            $query_payment_cycle = $db->query("SELECT * FROM oc_x_customer_payment_cycle WHERE customer_id=" . $customer_id);
            $result['payment_cycle_detail'] = array(
                'week'         => $query_payment_cycle->row['week'],
                'amount_limit' => $query_payment_cycle->row['amount_limit'],
                'bill_limit'   => $query_payment_cycle->row['bill_limit'],
                'before_date'  => $query_payment_cycle->row['before_date'],
                'next_date'    => $query_payment_cycle->row['next_date']
            );
        }

        // 缓存
        $cacheTime    = defined('REDIS_MY_ACCOUNT_CACHE_TIME') ? REDIS_MY_ACCOUNT_CACHE_TIME : 3600;
        $this->redis->setex($myAccountKey, $cacheTime, json_encode($result));

        return $result;
    }

    function getBillTotal(array $data){
        global $db;
        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        $bill_id = (int)$data['data']['bill_id'];
        if(!$customer_id || !$bill_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        $query_bill = $db->query("SELECT * FROM oc_x_customer_bill WHERE customer_id=" . $customer_id . " AND bill_id=" . $bill_id);
        $bill_info = $query_bill->row;
        if(empty($bill_info)){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '此账单不存在'
            );
        }

        return array(
            'return_code'  => 'SUCCESS',
            'bill_id'      => $bill_info['bill_id'],
            'customer_id'  => $bill_info['customer_id'],
            'amount'       => round($bill_info['amount'], 2),
            'date_added'   => $bill_info['date_added'],
            'payment_method' => $bill_info['payment_method'],
            'payment_code'   => $bill_info['payment_code'],
            'payment_status' => $bill_info['payment_status']
        );

    }

    function getMerchantInfo(array $data){
        global $db;

        $uid = isset($data['uid']) ? $data['uid'] : false;

        $sql = "SELECT customer_id, merchant_name, merchant_address, firstname, telephone FROM oc_customer WHERE uid like '".$uid."%'";
        $query = $db->query($sql);
        $results = $query->row;
        if($results && sizeof($results)){
            return $results;
        }

        return array(
            'return_code' => 'FAIL',
            'return_msg'  => '仅限注册用户'
        );
    }

    function addMarketingEvent(array $data){
        global $db,$dbm;

        $uid = isset($data['uid']) ? $data['uid'] : false;
        $contact_name = isset($data['data']['contact_name']) ? $data['data']['contact_name'] : '0';
        $contact_phone = isset($data['data']['contact_phone']) ? $data['data']['contact_phone'] : '0';
        $marketing_event_id = isset($data['data']['marketing_event_id']) ? $data['data']['marketing_event_id'] : '0';

        $bool = true;
        $sql = "
          INSERT INTO `oc_x_marketing_event_signup` (`marketing_event_id`,`customer_id`, `uid`, `bd_id`, `contact_name`, `contact_phone`, `date_added`)
          select '".$marketing_event_id."', A.customer_id, '".$uid."', A.bd_id, '".$contact_name."','".$contact_phone."',now() from oc_customer A where A.uid = '".$uid."'
        ";
        $bool = $bool && $dbm->query($sql);
        if($bool){
            return true;
        }

        return false;
    }

    function checkMarketingEvent(array $data){
        global $db;

        $uid = isset($data['uid']) ? $data['uid'] : false;
        $marketing_event_id = isset($data['data']['marketing_event_id']) ? $data['data']['marketing_event_id'] : '0';

        $sql = "SELECT customer_id,contact_name,contact_phone FROM oc_x_marketing_event_signup WHERE uid = '{$uid}' and marketing_event_id = '{$marketing_event_id}' limit 1";
        $query = $db->query($sql);
        $result = $query->row;
        if($result && sizeof($result)){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '已申请',
                'info' => $result
            );
        }

        return array(
            'return_code' => 'SUCCESS',
            'return_msg'  => '可以申请'
        );
    }

    function getCustomerRanking(array $data){
        global $db;

        $uid = isset($data['uid']) ? $data['uid'] : false;
        $date_gap = isset($data['data']['date_gap']) ? (int)$data['data']['date_gap'] : 1;
        $rank = isset($data['data']['rank']) ? (int)$data['data']['rank'] : 10;
        $rank = $rank>20 ? 20 : $rank;

        $sql = "
        SELECT A.customer_id, if(B.uid = '".$uid."', 1, 0) you_are_here, B.merchant_name, SUM(A.sub_total) total
        FROM oc_order A LEFT JOIN oc_customer B ON A.customer_id = B.customer_id
        WHERE A.deliver_date = date_add(current_date(), interval ".$date_gap." day) AND A.order_status_id NOT IN (1,3)
        GROUP BY A.customer_id ORDER BY total DESC limit ".$rank."
        ";
        $query = $db->query($sql);
        $result = $query->rows;
        if($result && sizeof($result)){
            $gap=0;
            for($i=0; $i<sizeof($result); $i++){
                $result[$i]["rank"] = $i+1;
                if($i>0){
                    $result[$i]["gap"] = $result[$i-1]["total"] - $result[$i]["total"];
                }
                else{
                    $result[$i]["gap"] = 0;
                }
            }
            return array(
                'return_code' => 'SUCCESS',
                'return_msg'  => '',
                'info' => $result
            );
        }

        return array(
            'return_code' => 'FAIL',
            'return_msg'  => 'No Ranking',
            'info' => ''
        );
    }
    
    function getContainersInfo(array $data){
         global $db;
        $data = unserialize($data);
        $customer_id = isset($data['customer_id']) ? $data['customer_id'] : false;
        $date = isset($data['date']) ? $data['date'] : '';
        
        if(!$customer_id){
            return false;
}

        //截至今日店内有的框子
        $sql = "
                SELECT
                    *
                FROM
                    oc_x_container_move as fl
                left join oc_x_container as f on f.container_id = fl.container_id
                left join oc_x_container_type as ct on ct.type_id = f.type
                WHERE
                        fl.customer_id = " . $customer_id . "
                        and fl.date_added < '" . $date . " 00:00:00'
                GROUP BY
                        fl.container_id
                HAVING
                        sum(fl.move_type) = 1
               
               
        ";
       
        $query = $db->query($sql);
        $result = $query->rows;
        $return['inBusiness'] = $result;
        
        //今日送至店内的框子
        $sql = "
                SELECT
                    *
                FROM
                    oc_x_container_move as fl
                left join oc_x_container as f on f.container_id = fl.container_id
                left join oc_x_container_type as ct on ct.type_id = f.type
                WHERE
                        fl.customer_id = " . $customer_id . "
                and fl.move_type = 1
                and fl.date_added >= '" . $date . " 00:00:00'
                and fl.date_added <= '" . $date . " 23:59:59'
               
        ";
       
        $query = $db->query($sql);
        $result = $query->rows;
        $return['dayInBusiness'] = $result;
        //今日回收的框子
        $sql = "
                SELECT
                    *
                FROM
                    oc_x_container_move as fl
                left join oc_x_container as f on f.container_id = fl.container_id
                left join oc_x_container_type as ct on ct.type_id = f.type
                WHERE
                        fl.customer_id = " . $customer_id . "
                and fl.move_type = -1
                and fl.date_added >= '" . $date . " 00:00:00'
                and fl.date_added <= '" . $date . " 23:59:59'
               
        ";
       
        $query = $db->query($sql);
        $result = $query->rows;
        $return['dayOutBusiness'] = $result;
        
        return $return;
    }

    function resetPassword(array $data){
        global $dbm, $db;

        $phone = isset($data['data']['phone']) ? $data['data']['phone'] : false;
        $smsCode = isset($data['data']['smsCode']) ? $data['data']['smsCode'] : false;
        $password = isset($data['data']['password']) ? $data['data']['password'] : false;

        if($phone && $smsCode && $password){
            $query = $db->query("SELECT phone, code, expiration FROM oc_x_msg_valid WHERE phone='".$phone."' and code = '".$smsCode."'");
            $msgInfo = $query->row;

            if($query->num_rows){
                if($msgInfo['expiration'] > time()){
                    $query = $db->query("update oc_customer set `password` =  md5(concat(salt, '".$password."')), uid=concat(uid,'@',customer_id) where telephone = '".$phone."'");

                    $return_code = 'SUCCESS';
                    $return_msg = '密码已重置，请重新登录';
                    $return_data = '';
                }
                else{
                    $dbm->query("DELETE FROM oc_x_msg_valid WHERE phone='".$phone."'");

                    $return_code = 'FAIL';
                    $return_msg = '验证码已过期，请重新获取';
                    $return_data = '';
                }
            }else{
                $return_code = 'FAIL';
                $return_msg = '验证码不存在，请重新获取';
                $return_data = '';
            }
        }
        else{
            $return_code = 'FAIL';
            $return_msg = '信息提交错误，请重试';
            $return_data = '';
        }

        return array(
            'return_code' => $return_code,
            'return_msg' => $return_msg,
            'return_data' => $return_data
        );
    }
}

$customer = new CUSTOMER();
?>