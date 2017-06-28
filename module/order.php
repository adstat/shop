<?php
require_once(DIR_SYSTEM.'/db.php');
require_once('customer.php');
require_once('inventory.php');

class ORDER{
    private $dbm,$db;
    private $redis;
    private $orderTime;

    public function __construct()
    {
        $this->redis     = new MyRedis();
        $this->orderTime = defined('REDIS_CACHE_TIME') ? REDIS_CACHE_TIME : 1800;
    }

    function getOrderDetailInfoKey($stationId=1, $orderId){
        $keyPrefix = defined('REDIS_ORDER_DETAIL_INFO_KEY_PREFIX') ? REDIS_ORDER_DETAIL_INFO_KEY_PREFIX : 'orderDetailInfo';
        $key       = $keyPrefix.':'.$stationId.':'.$orderId; //orderDetailInfo : stationId : orderId
        return $key;
    }

    function getOrderTotal($id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        if(!$id){
            return false;
        }

        //Prevent overpay
        $sql = "SELECT O.order_id, O.payment_code, O.order_status_id, O.order_payment_status_id, sum(OT.value) dueTotal, sum(if(OT.code='total',OT.value,0)) due FROM oc_order O ";
        $sql .= " LEFT JOIN oc_order_total OT ON O.order_id = OT.order_id AND OT.accounting = 1 ";
        $sql .= " WHERE O.order_id=".(int)$id;
        $query = $db->query($sql);
        $result = $query->row;

        if($result){
            //TODO log
            return $result;
        }

        return false;
    }

    function getOrderTotalDetail($id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        if(!$id){
            return false;
        }

        //Prevent overpay
        $sql = "SELECT  order_id, code, title, value, accounting, date_added FROM oc_order_total WHERE order_id=".(int)$id;
        $query = $db->query($sql);
        $result = $query->rows;

        if($result){
            //TODO log
            return $result;
        }

        return false;
    }

    // 订单汇总详情信息带缓存
    function getOrderTotalDetailWithCache(array $data){
        global $db;

        $orderId   = isset($data['data']['order_id']) ? (int)$data['data']['order_id'] : false ;
        $stationId = isset($data['stationId'])        ? (int)$data['stationId']        : 1;
        if(!$orderId){ return false; }

        $orderDetailInfoKey = $this->getOrderDetailInfoKey($stationId, $orderId);
        $field              = defined('REDIS_ORDER_DETAIL_INFO_OF_TOTAL_DETAIL') ? REDIS_ORDER_DETAIL_INFO_OF_TOTAL_DETAIL : 'totalDetail';
        if($this->redis->hexists($orderDetailInfoKey, $field)){
            $result         = $this->redis->hget($orderDetailInfoKey, $field);
            return json_decode($result);
        }

        $sql    = "SELECT  order_id, code, title, value, accounting, date_added FROM oc_order_total WHERE order_id=".$orderId;
        $query  = $db->query($sql);
        $result = $query->rows;

        if($result){
            $this->redis->hset($orderDetailInfoKey, $field, json_encode($result));
            $this->redis->expire($orderDetailInfoKey, $this->orderTime);
            return $result;
        }

        return false;
    }


    function getOrderProduct($id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        if(!$id){
            return false;
        }

        //Prevent overpay
        $sql = "SELECT OP.product_id, OP.name, OP.quantity, OP.price, OP.total, OP.price_ori, OP.is_gift, OP.shipping,
        P.status, if(P.unit_size is null or P.unit_size =0 , '', round(P.unit_size,0)) box_unit_size
        from oc_order_product OP
        LEFT JOIN oc_product P ON OP.product_id = P.product_id
        WHERE order_id=".(int)$id;
        $query = $db->query($sql);
        $result = $query->rows;

        if($result){
            //TODO log
            return $result;
        }

        return false;
    }

    // 订单商品详情带缓存
    function getOrderProductWithCache(array $data){
        global $db;

        $orderId   = isset($data['data']['order_id']) ? (int)$data['data']['order_id'] : false;
        $stationId = isset($data['stationId'])        ? (int)$data['stationId']        : 1;
        if(!$orderId){ return false; }

        $orderDetailInfoKey = $this->getOrderDetailInfoKey($stationId, $orderId);
        $field              = defined('REDIS_ORDER_DETAIL_INFO_OF_PRODUCT') ? REDIS_ORDER_DETAIL_INFO_OF_PRODUCT : 'product';
        if($this->redis->hexists($orderDetailInfoKey, $field)){
            $result         = $this->redis->hget($orderDetailInfoKey, $field);
            return json_decode($result);
        }

        //Prevent overpay
        $sql    = "SELECT OP.product_id, OP.name, OP.quantity, OP.price, OP.total, OP.price_ori, OP.is_gift, OP.shipping, P.status
                    FROM oc_order_product OP
                    LEFT JOIN oc_product P ON OP.product_id = P.product_id
                    WHERE order_id=".$orderId;
        $query  = $db->query($sql);
        $result = $query->rows;

        if($result){
            $this->redis->hset($orderDetailInfoKey, $field, json_encode($result));
            $this->redis->expire($orderDetailInfoKey, $this->orderTime);
            return $result;
        }

        return false;
    }

    //TODO Change function to add payment success
    function addOrderTotal($data, $station_id=1, $language_id=2, $origin_id=1){
        global $db,$dbm,$log;

        $data = unserialize($data);
        if(!is_array($data)){
            //TODO log

            return false;
        }

        if( !isset($data['order_id']) ){
            //TODO log


            return false;
        }

        //Check if order was paid
        $balance_container_deposit = 0;
        $customer_id = 0;
        $sql = "SELECT order_id, customer_id, sub_total, order_status_id, if(order_status_id in (1,2), 1, 0) valid_status, order_payment_status_id, balance_container_deposit FROM oc_order WHERE order_id='".(int)$data['order_id']."'";
        $query = $db->query($sql);
        $result = $query->row;
        if($result['order_payment_status_id'] == 2 || $result['valid_status'] == 0){ //Paid order and invalid status, PASS
            return false;
        }
        $balance_container_deposit = $result['balance_container_deposit'];
        $customer_id = $result['customer_id'];
        $subTotal = $result['sub_total'];

        //Prevent overpay
        $sql = "SELECT sum(value) dueTotal, sum(if(code='total',value,0)) due FROM oc_order_total WHERE order_id=".(int)$data['order_id']." AND accounting = 1";
        $query = $db->query($sql);
        $result = $query->row;

        if($result['dueTotal']<=0){
            //TODO log
            return true;
        }

        //Update order for WeChat Payment
        $bool = true;
        $sql = "INSERT INTO `oc_order_total` (`order_id`, `code`, `title`, `value`, `accounting`, `sort_order`) VALUES";
        $sql .= "(".(int)$data['order_id'].", '".$db->escape($data['code'])."', '".$db->escape($data['title'])."', ".(float)$data['value'].", ".(int)$data['accounting'].", ".(int)$data['sort_order'].")";
        $bool = $bool && $dbm->query($sql);

        //Add Container Deposit Record
        if($bool && $balance_container_deposit>0){

            $balance_container_deposit = abs($balance_container_deposit)*(-1);
            $sql = "INSERT INTO `oc_order_total` (`order_id`, `code`, `title`, `value`, `accounting`, `sort_order`) VALUES";
            $sql .= "(".(int)$data['order_id'].", 'credit_container_deposit', '篮筐押金结转余额', ".(float)$balance_container_deposit.", 0, 98)";
            $bool = $bool && $dbm->query($sql);

            if($bool){
                $balance_container_deposit = abs($balance_container_deposit);
                $sql = "INSERT INTO `oc_customer_transaction` (`customer_id`, `order_id`,`customer_transaction_type_id`, `description`, `amount`, `date_added`) VALUES";
                $sql .= "(".$customer_id.", ".(int)$data['order_id'].", '12', '篮筐押金转余额', ".(float)$balance_container_deposit.", NOW())";
                $bool = $bool && $dbm->query($sql);
            }
        }

        if ($bool) {
            //TODO Update order, order_history
            $calc = $result['due'] + $data['value'];
            if($calc <= 0){
                $sql = "UPDATE oc_order SET order_status_id = 2, order_payment_status_id = 2 WHERE order_id=".(int)$data['order_id'];
                $dbm->query($sql);
            }

            return $data['order_id'];
        }
        return false;
    }

    function addOrder($data, $station_id=1, $language_id=2, $origin_id=1){
        //TODO: Try()Catch()
        //TODO: Roll-back
        //TODO: Station

        //return $data;

        global $db,$dbm,$log;
        global $customer;
        global $inventory;

        $log->write('INFO:['.__FUNCTION__.']'.$data. "\n\r");

        $data = unserialize($data);
        if(!is_array($data)){
            return false;
        }

        if(!isset($data['timestamp']) || !$data['timestamp']){
            $data['timestamp'] = time();
        }

        if(!isset($data['products']) && !isset($data['totals'])){
            //TODO log
            //Order with empty products

            return false;
        }

        if( $data['sub_total']<0 || (!$data['credit_pay'] && $data['total'] <= 0) ){
            //TODO log
            //Error in price

            return false;
        }

        //Insert new user, customer, address(customer_id), customer(address_id)
        //Update customer info
        //TODO Customer default address, approved=0(sms check)
        if(!$data['customer_id']){
            $bool = true;
            //$dbm->beginTransaction();

            $sql = "INSERT INTO `oc_customer` (`customer_group_id`, `store_id`, `firstname`, `lastname`, `email`, `telephone`,`address_id`, `status`, `approved`,`date_added`, `uid`, `origin_id`) VALUES";
            $sql .= "(1, 0, '".$db->escape($data['shipping_name'])."', '-', '".$db->escape($data['shipping_phone']).DOMAIN."', '".$db->escape($data['shipping_phone'])."',0, 1, 1, NOW(), '".$data['uid']."', ".$origin_id.")";
            //return $sql;
            $bool = $bool && $dbm->query($sql);
            $data['customer_id'] = $dbm->getLastId();

            //Add customer address
            $data['email'] = $db->escape($data['shipping_phone']).DOMAIN;
            $data['firstname'] = $db->escape($data['shipping_name']);
            $data['lastname'] = '-';
            $data['telephone'] = $db->escape($data['shipping_phone']);
            $data['shipping_firstname'] = $data['shipping_name'];
            $data['shipping_lastname'] = '-';
            $data['payment_firstname'] = $data['shipping_name'];

            $sql = "INSERT INTO `oc_address` (`customer_id`, `firstname`, `lastname`, `company`, `address_1`, `address_2`, `city`, `postcode`, `country_id`, `zone_id`, `custom_field`, `default`, `status`, `name`, `shipping_phone`,`station_id`,`pickupspot_id`,`area_id`) VALUES";
            $sql .= "(".$data['customer_id'].", '".$db->escape($data['shipping_name'])."', '-', '', '".$db->escape($data['shipping_address'])."', '', '上海', '200000', 44, 708, '', 0, 1, '".$db->escape($data['shipping_name'])."', '".$db->escape($data['shipping_phone'])."'".$db->escape($data['station_id']).",".$db->escape($data['pickupspot_id']).",".$db->escape($data['area_id']).")";
            //return $sql;
            $bool = $bool && $dbm->query($sql);

            //if ($bool) {
            //    $bool = $dbm->commit ();
            //} else {
            //    $dbm->rollBack ();
            //}
        }
        else{
            //TODO, multi-address manage
            $bool = true;
            //$dbm->beginTransaction();
            if(!isset($data['payment_id'])){ $data['payment_id'] = 0; }

            $sql = "UPDATE `oc_address` SET ";
            $sql .= " `address_1` = '".$db->escape($data['shipping_address'])."', `name`='".$db->escape($data['shipping_name'])."', `shipping_phone`='".$db->escape($data['shipping_phone'])."', `pickupspot_id`=0, `area_id`=".$db->escape($data['area_id']).", `payment_id`=".$db->escape($data['payment_id']);
            $sql .= " WHERE `customer_id` = ".$data['customer_id'];
            //return $sql;
            $bool = $bool && $dbm->query($sql);

            //if ($bool) {
            //    $bool = $dbm->commit ();
            //} else {
            //    $dbm->rollBack ();
            //}
        }

        //TODO: Add Customer Failed
        if(!$data['customer_id']){
            //TODO log
            return false;
        }

        //Check timestamp
        //$sql = "select order_id from oc_order where customer_id = '".$data['customer_id']."' and timestamp = '".$data['timestamp']."';";
        //$query = $db->query($sql);
        //if(sizeof($query->rows)){
            //return -1;
        //}

        //Order total and Credit Paid Orders Status
        $order_total = isset($data['total'])?(float)$data['total']:0;
        $total_adjust = isset($data['total_adjust'])?(float)$data['total_adjust']:0;
        $line_total = isset($data['line_total'])?(float)$data['line_total']:0;
        $sub_total = isset($data['sub_total'])?(float)$data['sub_total']:0;
        $discount_total = isset($data['discount_total'])?(float)$data['discount_total']:0;
        $coupon_discount = isset($data['couponDiscountTotal'])?(float)$data['couponDiscountTotal']:0;
        $promotion_discount = isset($data['promotionDiscountTotal'])?(float)$data['promotionDiscountTotal']:0;
        $shipping_fee= isset($data['shipping_fee'])?(int)$data['shipping_fee']:0;
        $balance_container_deposit= isset($data['balance_container_deposit'])?(int)$data['balance_container_deposit']:0;
        $credit_pay = isset($data['credit_pay'])?(int)$data['credit_pay']:0;
        $credit_paid = isset($data['credit_paid'])?(float)$data['credit_paid']:0;
        $point_pay = isset($data['point_pay'])?(int)$data['point_pay']:0;
        $point_paid = isset($data['point_paid'])?(float)$data['point_paid']:0;

        $cashback_status = isset($data['order_cashback_status_id'])?(int)$data['order_cashback_status_id']:0;
        $warehouse_id = !empty($data['warehouse_id']) ? (int)$data['warehouse_id'] : 0;

        //异常订单
        if($sub_total <= 0 || $discount_total > 0 || $shipping_fee < 0 || $balance_container_deposit < 0 || $order_total < 0){
            return false;
        }

        $order_status_id = 1;
        if($order_total==0){
            $order_status_id = 2;
        }

        //Start to add order
        //$orderstamp = date ( "ymdHis" ) . mt_rand ( 1000, 9999 );
        $sql = "INSERT INTO oc_order SET
        orderid = CONCAT(DATE_FORMAT(NOW(), '%y%m%d%H%i%s'), LPAD(ROUND(RAND()*1000,0),3,'0')),
        invoice_prefix = 'INV-2015-00',
        store_id = '0',
            store_name = '".$db->escape($data['store_name'])."',
        store_url = '',
        customer_id = '" . (int)$data['customer_id'] . "',

        timestamp = '" . $data['timestamp'] . "',

        customer_group_id = '" . (int)$data['customer_group_id'] . "',
        is_nopricetag = '" . (int)$data['is_nopricetag'] . "',
        bd_id = '" . (int)$data['bd_id'] . "',
        firstname = '".$db->escape($data['firstname'])."',
        lastname = '".$db->escape($data['lastname'])."',
        email = '" . $db->escape($data['email']) . "',
        telephone = '" . $db->escape($data['telephone']) . "',
        fax = '',
        custom_field = '',
        payment_firstname = '',
        payment_lastname = '',
        payment_company = '',
        payment_address_1 = '',
        payment_address_2 = '',
        payment_city = '',
        payment_postcode = '',
        payment_country = '',
        payment_country_id = '',
        payment_zone = '',
        payment_zone_id = '',
        payment_address_format = '',
        payment_custom_field = '',
            payment_method = '" . $db->escape($data['payment_method']) . "',
            payment_code = '" . $db->escape($data['payment_code']) . "',
        shipping_firstname = '" . $db->escape($data['shipping_firstname']) . "',
        shipping_lastname = '" . $db->escape($data['shipping_lastname']) . "',
        shipping_phone = '" . $db->escape($data['shipping_phone']) . "',
            shipping_company = '',
        shipping_address_1 = '" . $db->escape($data['shipping_address']) . "',
        shipping_address_2 = '',
            shipping_city = '" . $db->escape($data['shipping_city']) . "',
            shipping_postcode = '" . $db->escape($data['shipping_postcode']) . "',
            shipping_country = '" . $db->escape($data['shipping_country']) . "',
            shipping_country_id = '" . (int)$data['shipping_country_id'] . "',
            shipping_zone = '" . $db->escape($data['shipping_zone']) . "',
            shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "',
        shipping_address_format = '',
        shipping_custom_field = '',
            shipping_method = '" . $db->escape($data['shipping_method']) . "',
            shipping_code = '" . $db->escape($data['shipping_code']) . "',
        comment = '" . $db->escape($data['comment']) . "',
            affiliate_id = '" . (int)$data['affiliate_id'] . "',
            commission = '" . (float)$data['commission'] . "',
            marketing_id = '" . (int)$data['marketing_id'] . "',
            tracking = '" . $db->escape($data['tracking']) . "',
        language_id = '" . (int)$data['language_id'] . "',
            currency_id = '" . (int)$data['currency_id'] . "',
            currency_code = '" . $db->escape($data['currency_code']) . "',
            currency_value = '" . (float)$data['currency_value'] . "',
            ip = '" . $db->escape($data['ip']) . "',
            forwarded_ip = '" .  $db->escape($data['forwarded_ip']) . "',
            user_agent = '" . $db->escape($data['user_agent']) . "',
            accept_language = '" . $db->escape($data['accept_language']) . "',
        date_added = NOW(),
        date_modified = NOW(),

        station_id = '" . (int)$data['station_id'] . "',
        warehouse_id = '" . $warehouse_id . "',
        origin_id = '" . (int)$data['origin_id'] . "',
        uid = '" . $db->escape($data['uid']) . "',

        shipping_name = '" . $db->escape($data['shipping_name']) . "',

        deliver_date = '" . $db->escape($data['deliver_date']) . "',
        deliver_slot = '" . $db->escape($data['deliver_slot']) . "',
        deliver_now = '" . $db->escape($data['deliver_now']) . "',

        pickupspot_id = '" . $db->escape($data['pickupspot_id']) . "',
        area_id = '" . $db->escape($data['area_id']) . "',


        order_status_id = '" . $order_status_id . "',
        order_payment_status_id = '" . (int)$data['order_payment_status_id'] . "',

        sub_total = '" . $sub_total . "',
        discount_total = '" . $discount_total . "',
        coupon_discount = '" . $coupon_discount . "',
        promotion_discount = '" . $promotion_discount . "',
        shipping_fee = '" . $shipping_fee . "',
        balance_container_deposit = '" . $balance_container_deposit . "',
        credit_pay = '" . $credit_pay . "',
        credit_paid = '" . $credit_paid . "',
        point_pay = '" . $point_pay . "',
        point_paid = '" . $point_paid . "',
        line_total = '" . $line_total . "',
        total_adjust = '" . $total_adjust . "',
        total = '" . $order_total . "',

        order_cashback_status_id = '" . $cashback_status . "'
        ";

        $dbm->begin();
        $bool = true;

        //Use Master,Start to write order
        $bool = $bool && $dbm->query($sql);
        $order_id = $dbm->getLastId();

        //return $sql;
        //$order_id = 10;

        //Use Master, Start to write products
        $sql = "INSERT INTO `oc_order_product` (`order_id`, `product_id`, `weight_inv_flag`, `name`, `model`, `quantity`, `price`, `total`, `tax`, `reward`, `price_ori`, `retail_price`, `is_gift`, `shipping`) VALUES";
        foreach($data['products'] as $product){
            $is_gift = isset($product['is_gift'])?(int)$product['is_gift']:0; //兼容生鲜平台结构
            $reward = isset($product['reward'])?(int)$product['reward']:0; //商品返积分，兼容
            //$sql .= "(".(int)$order_id.",".(int)$product['itemid'].", '".$db->escape($product['name'])."', '', ".(int)$product['num'].", ".(float)$product['price'].", ".(float)$product['price']*(int)$product['num'].", 0.0000, 0, ".(float)$product['oldprice'].", 0, 1),";
            $sql .= "(".(int)$order_id.",".(int)$product['product_id'].",".(int)$product['weight_inv_flag'].", '".$db->escape($product['name'])."', '', ".(int)$product['qty'].", ".(float)$product['special_price'].", ".(float)$product['special_price']*(int)$product['qty'].", 0.0000, '" . $reward . "', ".(float)$product['price'].",".(float)$product['retail_price'].", '".$is_gift."', 1),";
        }
        $sql = rtrim($sql, ","); //Get ride of last comma
        $bool = $bool && $dbm->query($sql);


        //添加促销记录
        //$data['orderDiscountData'] = array('coupon'=> array(), 'promotion' => array());
        $orderDiscountData = array();
        if( isset($data['orderDiscountData']) ){
            foreach($data['orderDiscountData'] as $type => $info){
                foreach($info as $k => $v){
                    $discountPool = $v['discountTotal'];
                    for($m=0;$m<sizeof($v['list']);$m++){
                        if($m < sizeof($v['list'])-1){
                            $discountDivider = round($v['discountTotal'] * (($data['products'][$v['list'][$m]]['qty'] * $data['products'][$v['list'][$m]]['price'])/$v['total']),2);
                            $discountPool -= $discountDivider;
                        }else{
                            $discountDivider = $discountPool;
                        }

                        $orderDiscountData[] = array(
                            'product_id'=> $v['list'][$m],
                            'type' => $type,
                            'relevant_id' => $k,
                            'discount_total' => $discountDivider
                        );
                    }
                }
            }
        }
        if(sizeof($orderDiscountData)){
            $sql = "INSERT INTO `oc_order_product_discount` (`order_id`, `product_id`, `type`, `relevant_id`, `discount_total`, `status`) VALUES";
            foreach($orderDiscountData as $val){
                $sql .= "(".(int)$order_id.", '".$val['product_id']."', '".$val['type']."', ".$val['relevant_id'].", ".$val['discount_total'].",1),";
            }
            $sql = rtrim($sql, ","); //Get ride of last comma
            $bool = $bool && $dbm->query($sql);
        }

        //Use Master, Add Cash Back Info 20160609
//        if(sizeof($cashBackProducts)){
//            $sql = "INSERT INTO `oc_order_product_cashback` (`order_id`,`customer_id`, `product_id`, `quantity`, `cashback_due`, `cashback`, `status`, `excuted`) VALUES";
//            foreach($cashBackProducts as $val){
//                $sql .= "(".(int)$order_id.", '".(int)$data['customer_id']."', '".$val['product_id']."', ".$val['qty'].", ".$val['qty']*$val['cashback'].",0,1,0),";
//            }
//            $sql = rtrim($sql, ","); //Get ride of last comma
//            $bool = $bool && $dbm->query($sql);
//        }

        //Use Master, Start to write order totoals
        $sql = "INSERT INTO `oc_order_total` (`order_id`, `code`, `title`, `value`, `accounting`, `sort_order`) VALUES";
        foreach($data['totals'] as $total){
            $sql .= "(".(int)$order_id.", '".$db->escape($total['code'])."', '".$db->escape($total['title'])."', ".(float)$total['value'].", ".(int)$total['accounting'].", ".(int)$total['sort_order']."),";
        }
        $sql = rtrim($sql, ","); //Get ride of last comma
        $bool = $bool && $dbm->query($sql);

        //For Credit Payment, Also Update Customer Transaction
        if($data['credit_pay']){
            $sql = "INSERT INTO `oc_customer_transaction` (`customer_id`, `order_id`, `customer_transaction_type_id`, `description`, `amount`, `date_added`) VALUES";
            $sql .= "(".(int)$data['customer_id'].", ".(int)$order_id.", 3, '订单支付', ".(float)$data['credit_paid'].", NOW())";
            $bool = $bool && $dbm->query($sql);

            //Todo clear up this mess, gonna re-write addorder() and addOrderTotal()
            if($bool && $order_status_id == 2 && $balance_container_deposit > 0){
                $balance_container_deposit = abs($balance_container_deposit)*(-1);
                $sql = "INSERT INTO `oc_order_total` (`order_id`, `code`, `title`, `value`, `accounting`, `sort_order`) VALUES";
                $sql .= "(".(int)$order_id.", 'credit_container_deposit', '篮筐押金结转余额', ".(float)$balance_container_deposit.", 0, 98)";
                $bool = $bool && $dbm->query($sql);

                if($bool){
                    $balance_container_deposit = abs($balance_container_deposit);
                    $sql = "INSERT INTO `oc_customer_transaction` (`customer_id`, `order_id`,`customer_transaction_type_id`, `description`, `amount`, `date_added`) VALUES";
                    $sql .= "(".$data['customer_id'].", ".(int)$order_id.", '12', '篮筐押金转余额', ".(float)$balance_container_deposit.", NOW())";
                    $bool = $bool && $dbm->query($sql);
                }
            }
        }

        //添加积分支付
        if($point_pay){
            $sql = "INSERT INTO `oc_customer_reward` (`customer_id`,`order_id`, `points`,  `date_added`, `reward_id`, `add_by`) VALUES";
            $sql .= "('".$data['customer_id']."', '".(int)$order_id."', '".$point_paid*POINTS_TO_PAYMENT_RATE."', NOW(), '".POINTS_TO_PAYMENT_RULE_ID."', '0')";
            $bool = $bool && $dbm->query($sql);
        }

        //添加库存扣减记录, 仅明天配送订单参与实时库存计算
        $inv_status = 0;
        if($data['deliver_date'] == date('Y-m-d', strtotime('+1 day'))){
            $inv_status = 1;
        }

        //快消品全部扣减库存
        if((int)$data['station_id'] == 2){
            $inv_status = 1;
        }
        //$inv_status = 1;

        $sql = "INSERT INTO `oc_x_inventory_move` (`station_id`, `date`, `timestamp`, `from_station_id`, `to_station_id`, `order_id`, `inventory_type_id`, `date_added`, `status`, `warehouse_id`)
                VALUES
                (".(int)$data['station_id'].", current_date(), unix_timestamp(now()), 0, 0, '".$order_id."', ".INVENTORY_TYPE_ORDERED.", now(), 1, {$warehouse_id});";
        $bool = $bool && $dbm->query($sql);
        $inventory_move_id = $dbm->getLastId();

        //添加明细
        $sql = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `order_id`, `customer_id`,`product_id`, `quantity`, `status`, `warehouse_id`)
                select '".$inventory_move_id."', ".(int)$data['station_id'].", B.order_id, B.customer_id, C.product_id, abs(C.quantity)*-1 quantity, '".$inv_status."', {$warehouse_id}
                from oc_order B
                left join oc_order_product C on B.order_id = C.order_id
                where B.order_id = '".$order_id."'";
        $bool = $bool && $dbm->query($sql);

        //添加赠品记录
//        if( isset($data['validPromotionGifts']) && sizeof($data['validPromotionGifts']) ){
//            $sql = "INSERT INTO `oc_x_promotion_activity` (`promotion_id`, `title`, `product_id`, `price`, `special_price`, `quantity`, `order_id`, `customer_id`, `date_added`, `status`) VALUES";
//            foreach($data['validPromotionGifts'] as $m){
//                $sql .= "(".$m['promotion_id'].", '".$m['promotion_title']."','".$m['product_id']."','".$m['price']."','".$m['special_price']."','".$m['promotion_quantity']."', '".(int)$order_id."', '".$data['customer_id']."', NOW(), 1),";
//            }
//            $sql = rtrim($sql, ",").';'; //Get ride of last comma
//
//            $bool = $bool && $dbm->query($sql);
//        }

        //TODO添加促销规则记录
        //添加优惠券记录
        if(isset($data['couponData']) && sizeof($data['couponData'])){
            $sql = 'INSERT INTO `oc_coupon_history` (`coupon_id`, `order_id`, `customer_id`, `discount_total`, `date_added`) VALUES';
            $sql .= " ('".(int)$data['couponData']['coupon_id']."','".$order_id."','".$data['customer_id']."','".(float)$data['couponData']['coupon_discount']."',NOW());";
            $bool = $bool && $dbm->query($sql);
        }

        //是否是账期支付
//        if($data['payment_code'] == 'CYCLE'){
//            $dbm->query("INSERT INTO oc_x_customer_bill_detail SET customer_id='" . $data['customer_id'] . "', type='order', related_id='" . $order_id . "', amount='" . (float)$data['total'] . "',date_added=NOW()");
//        }


        if(!$bool) {
            $dbm->rollback();
            $log->write('ERR:['.__FUNCTION__.']'.$data['customer_id']."\n\r");

            return false;

        }else {
            $dbm->commit();
            $log->write('INFO:['.__FUNCTION__.']'.$data['customer_id'].':'.$order_id. "\n\r");

            return $order_id;
            //return array('status'=>'SUCCESS','message'=>'订单'.$order_id.'已取消，支付部分(如有)已退回余额');
        }
        //TODO Update Cusomer Info

        //TODO Creidt Payment
    }

    function customerCancelOrder($data, $station_id=1, $language_id=2, $origin_id=1){
        global $dbm, $log;

        //Expect Data: $data= '{"customer_id":"664","order_id":"12223"}';
        $data = unserialize($data);

        $customer_id = isset($data['customer_id'])?(int)$data['customer_id']:false;
        $order_id = isset($data['order_id'])?(int)$data['order_id']:false;

        if(!$customer_id || !$order_id){
            return array('status'=>'ERR','message'=>'[错误101]用户订单信息错误');
        }

        $sql = "select order_id from oc_order where order_id = '".$order_id."' and order_status_id = '".PADDING_ORDER_STATUS."' and order_payment_status_id = '".UNPAID_ORDER_STATUS."' and customer_id = '".$customer_id."'";
        $query = $dbm->query($sql);
        $result = $query->rows;

        if(!sizeof($result)){
            return array('status'=>'ERR','message'=>'[错误102]订单状态当前状态不可取消');
        }

        //开始取消订单操作
        $dbm->begin();
        $bool = true;
        $sql = "INSERT INTO `oc_order_history` (`order_id`,`comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`)
                select order_id, 'Customer', now(), ".CANCELLED_ORDER_STATUS.", order_payment_status_id, order_deliver_status_id, customer_id  from oc_order where order_status_id = '".PADDING_ORDER_STATUS."' and order_payment_status_id = '".UNPAID_ORDER_STATUS."' and order_id = '". $order_id ."'";
        $bool = $bool && $dbm->query($sql);

        $sql = "update oc_order set order_status_id = '".CANCELLED_ORDER_STATUS."' where order_id = '".$order_id."'";
        $bool = $bool && $dbm->query($sql);

        //恢复扣除的优惠券试用次数
        $sql = "update oc_coupon_history set status = '0' where order_id = '".$order_id."'";
        $bool = $bool && $dbm->query($sql);

        //订单已经取消，以订单状态为3（已取消）作为依据
        $sql = "INSERT INTO `oc_customer_transaction` (`customer_id`, `order_id`, `customer_transaction_type_id`, `description`, `amount`, `date_added`)
                select A.customer_id, A.order_id, 4, '取消订单退余额', abs(B.value), now()
                from oc_order A left join oc_order_total B on A.order_id = B.order_id
                where A.order_status_id = '".CANCELLED_ORDER_STATUS."' and A.order_payment_status_id = '".UNPAID_ORDER_STATUS."' and A.order_id = '".$order_id."' and B.code = 'credit_paid';";
        $bool = $bool && $dbm->query($sql);

        //恢复扣除的积分支付记录
        $sql = "INSERT INTO `oc_customer_reward` (`customer_id`,`order_id`, `points`,  `date_added`, `reward_id`, `add_by`)
                select A.`customer_id`, A.`order_id`, abs(B.`points`),  NOW(), '".POINTS_TO_PAYMENT_CANCEL_RULE_ID."', 0 from oc_order A
                inner join oc_customer_reward B on A.order_id = B.order_id and B.reward_id = '".POINTS_TO_PAYMENT_RULE_ID."'
                where A.order_id = '".$order_id."' and A.order_status_id = '".CANCELLED_ORDER_STATUS."'";
        $bool = $bool && $dbm->query($sql);

        //查找是否已有库存扣减记录，如有添加库存增加记录
        $sql = "INSERT INTO `oc_x_inventory_move` (`station_id`, `date`, `timestamp`, `from_station_id`, `to_station_id`, `order_id`, `inventory_type_id`, `date_added`, `status`, `warehouse_id`)
                select ".$station_id." station_id, current_date() date, unix_timestamp(now()) timestamp, 0 from_station_id, 0 to_station_id, A.order_id, ".INVENTORY_TYPE_ORDER_CANCEL." inventory_type_id, now() date_added, 1 status, A.warehouse_id warehouse_id
                from oc_order A
                inner join oc_x_inventory_move B on A.order_id = B.order_id and inventory_type_id = '".INVENTORY_TYPE_ORDERED."'
                where A.order_id = '".$order_id."'";
        $bool = $bool && $dbm->query($sql);
        //$inventory_move_id = $dbm->getLastId();

        //按照添加的，除明天配送订单，其他设置状态为0, 不参与实时库存计算
        $sql = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `order_id`, `customer_id`, `product_id`, `quantity`, `status`, `warehouse_id`)
                select A.inventory_move_id, B.station_id, B.order_id, B.customer_id, C.product_id, C.quantity quantity, if(B.deliver_date = date_add(date(B.date_added), interval 1 day), 1, 0) status, B.warehouse_id warehouse_id
                from oc_x_inventory_move A
                left join oc_order B on A.order_id = B.order_id
                left join oc_order_product C on B.order_id = C.order_id
                where A.order_id = '".$order_id."' and A.inventory_type_id = '".INVENTORY_TYPE_ORDER_CANCEL."'
                ";

//        $sql = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `order_id`, `customer_id`, `product_id`, `quantity`, `status`)
//                select A.inventory_move_id, ".$station_id." station_id, B.order_id, B.customer_id, C.product_id, C.quantity quantity, 1 status
//                from oc_x_inventory_move A
//                left join oc_order B on A.order_id = B.order_id
//                left join oc_order_product C on B.order_id = C.order_id
//                where A.order_id = '".$order_id."' and A.inventory_type_id = '".INVENTORY_TYPE_ORDER_CANCEL."'
//                ";
        $bool = $bool && $dbm->query($sql);


        if(!$bool) {
            $dbm->rollback();
            return array('status'=>'ERR','message'=>'[错误103]订单'.$order_id.'取消失败');
        }else {
            $dbm->commit();
            return array('status'=>'SUCCESS','message'=>'订单'.$order_id.'已取消，支付部分(如有)已退回余额');
        }
    }
}

$order = new ORDER();
?>