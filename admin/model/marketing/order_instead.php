<?php
date_default_timezone_set('Asia/Shanghai');
//echo date('Y-m-d H:i:s',time());

class ModelMarketingOrderInstead extends Model {
    public function order_insert($data = array()){
        $return_type = array(
            'order' => true,
            'product' => true,
        );

        $timestamp = time();
        foreach($data as $value){
//            $sql = "insert into oc_order (`customer_id`,`firstname`,`shipping_address_1`,`shipping_phone`,`sub_total`,`date_added`, )
//              values('".$value['customer_id']."','".$value['merchant_name']."','".$value['merchant_address']."','".$value['telephone']."','".$value['sub_total']."',now())";
//            $bool = $this->db->query($sql);
//            if(!$bool){
//                $return_type['order'] = false;
//            }

            //写入订单
            $timestamp++;
            $orderTimestamp = date('ymdHis',$timestamp) . rand(100,999);

            //TODO 指定用户和仓库，获取用户基本信息，验证商品编号是否有效。
            $customer_id = 8765;
            $station_id = 1;
            $origin_id = 1;
            $uid = 'oodlTw-YvdL0hUkiiH-YOWMB_MVY';
            $firstname = '[渠道]闪电购';
            $lastname = '代理';
            $email = '1860000004@xianshiji.com';
            $telephone = '18600000004';
            $payment_method = '渠道代理(月结)';
            $payment_code = 'CYCLE';
            $shipping_firstname = $value['merchant_name']."(".$value['customer_name'].")";
            $shipping_address = $value['merchant_address'];
            $shipping_phone = $value['telephone'];
            $credit_pay = 0;

            $sub_total = (float)$value['sub_total'];
            $discount_total = 0;
            $shipping_fee = 0;
            $balance_container_deposit = 0;
            $credit_paid = 0;
            $line_total = $sub_total + $discount_total + $shipping_fee + $balance_container_deposit + $credit_paid;
            $total_adjust = 0;
            $total = $line_total + $total_adjust;

            $date_added = date('Y-m-d H:i:s',$timestamp);
            $deliver_date = date('Y-m-d',$timestamp + 24*60*60);
            $comment = '渠道代理订单，此单无需现场支付。';
            $order_status_id = 2;
            $order_payment_status_id = 1;
            $internal_note = $value['internal_note'];

            $sql = "INSERT INTO oc_order SET
                    orderid = '" . $orderTimestamp . "',
                    invoice_prefix = 'INV-2017-00',
                    store_id = '0',
                    store_name = '鲜世纪',
                    store_url = '',
                    customer_id = '" . $customer_id . "',
                    timestamp = '" . $timestamp . "',
                    customer_group_id = '1',
                    is_nopricetag = '0',
                    bd_id = '1',
                    firstname = '".$firstname."',
                    lastname = '".$lastname."',
                    email = '" . $email . "',
                    telephone = '" . $telephone . "',
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
                    payment_method = '" . $payment_method . "',
                    payment_code = '" . $payment_code . "',
                    shipping_firstname = '" . $shipping_firstname . "',
                    shipping_lastname = '',
                    shipping_phone = '$shipping_phone',
                        shipping_company = '',
                    shipping_address_1 = '$shipping_address',
                    shipping_address_2 = '',
                        shipping_city = '上海市',
                        shipping_postcode = '200000',
                        shipping_country = 'China',
                        shipping_country_id = '44',
                        shipping_zone = 'Shanghai',
                        shipping_zone_id = '708',
                    shipping_address_format = '',
                    shipping_custom_field = '',
                        shipping_method = '送货',
                        shipping_code = 'D2D',
                    comment = '" . $comment . "',
                        affiliate_id = '0',
                        commission = '0',
                        marketing_id = '0',
                        tracking = '',
                    language_id = '2',
                        currency_id = '4',
                        currency_code = 'CNY',
                        currency_value = '1',
                        ip = '',
                        forwarded_ip = '',
                        user_agent = '',
                        accept_language = '',
                    date_added = '" . $date_added . "',
                    date_modified = '" . $date_added . "',

                    station_id = '" . $station_id . "',
                    origin_id = '" . $origin_id . "',
                    uid = '" . $uid . "',

                    shipping_name = '" . $shipping_firstname . "',
                    deliver_date = '" . $deliver_date . "',
                    deliver_slot = '00:00:00',
                    deliver_now = '0',
                    pickupspot_id = '0',
                    area_id = '0',
                    order_status_id = '" . $order_status_id . "',
                    order_payment_status_id = '" . $order_payment_status_id . "',

                    sub_total = '" . $sub_total . "',
                    discount_total = '" . $discount_total . "',
                    shipping_fee = '" . $shipping_fee . "',
                    balance_container_deposit = '" . $balance_container_deposit . "',
                    credit_pay = '" . $credit_pay . "',
                    credit_paid = '" . $credit_paid . "',
                        line_total = '" . $line_total . "',
                        total_adjust = '" . $total_adjust . "',
                    total = '" . $total . "',
                    order_cashback_status_id = '0',
                    internal_note = '". $internal_note ."'
                    ";

            $bool = $this->db->query($sql);
            if(!$bool){
                $return_type['order'] = false;
            }

            //取得插入后的最后一条order_id，与订单商品表关联起来
            $order_id = $this->db->getLastId();
            foreach($value['products'] as $vv){
                $sql = "insert into oc_order_product (`order_id`,`product_id`,`name`,`quantity`,`price`,`total`)
                  values('".$order_id."','".$vv['product_id']."','".$vv['product_name']."','".$vv['quantity']."','".$vv['price']."','".$vv['line_sum']."')";
                $bool = $this->db->query($sql);
                if(!$bool){
                    $return_type['product'] = false;
                }
            }

            //写入订单支付
            $sql = "
                INSERT INTO `oc_order_total` (`order_id`, `code`, `title`, `value`, `accounting`, `sort_order`, `date_added`)
                VALUES
                    (".$order_id.", 'sub_total', '小计', '".$sub_total."', 0, 1, '0000-00-00 00:00:00'),
                    (".$order_id.", 'shipping_fee', '运费', '".$shipping_fee."', 0, 3, '0000-00-00 00:00:00'),
                    (".$order_id.", 'total_adjust', '应收取整调整', '".$total_adjust."', 0, 3, '0000-00-00 00:00:00'),
                    (".$order_id.", 'total', '总计', ".$total.", 1, 5, '0000-00-00 00:00:00');
                ";
            $this->db->query($sql);
        }
        return $return_type;
    }

    public function order_write($customer_id,$shipping_address_1,$shipping_phone,$station_id,$sub_total,$order_products){
        $return_type = array(
            'order' => true,
            'product' => true,
        );
        $sql = "insert into oc_order (`customer_id`,`station_id`,`shipping_address_1`,`shipping_phone`,`sub_total`,`date_added`)
            values('".$customer_id."','".$station_id."','".$shipping_address_1."','".$shipping_phone."','".$sub_total."',now())";
        $bool = $this->db->query($sql);
        if(!$bool){
            $return_type['order'] = false;
        }

        $order_id = $this->db->getLastId();
        foreach($order_products as $value){
            $sql = "insert into oc_order_product (`order_id`,`product_id`,`name`,`quantity`,`price`,`total`)
               values('".$order_id."','".$value['product_id']."','".$value['name']."','".$value['quantity']."','".$value['price']."','".$value['total']."')";
            $bool = $this->db->query($sql);
            if(!$bool){
                $return_type['product'] = false;
            }
        }

        //对订单产生的库存做出调整,插入主表oc_inventory_move数据
        $time = time();
        $date = date("Y-m-d", $time);
        $date_added = date("Y-m-d H:i:s", $time);
        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName();

        $this->db->query('START TRANSACTION');
        $bool = 1;

        $sql = "insert into oc_x_inventory_move (`station_id`,`order_id`, `date`, `timestamp`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`)
            values('{$station_id}','{$order_id}', '{$date}', '{$time}', '5', '{$date_added}', '{$user_id}', '{$user_name}')";

        $bool = $bool && $this->db->query($sql);
        $inventory_move_id = $this->db->getLastId();

        $sql = "INSERT INTO oc_x_inventory_move_item (`inventory_move_id`, `station_id`, `order_id`,`customer_id`,`product_id`, `quantity`) VALUES ";
        $m=0;
        foreach($order_products as $product){
            $sql .= "(".$inventory_move_id.", '".$station_id."','".$order_id."','".$customer_id."', '".$product['product_id']."', '".$product['quantity']."')";
            if(++$m < sizeof($order_products)){
                $sql .= ', ';
            }
            else{
                $sql .= ';';
            }
        }
        $bool = $bool && $this->db->query($sql);

        if($bool){
            $this->db->query('COMMIT');
        }
        else{
            $this->db->query("ROLLBACK");
        }

        return $return_type;
    }
}