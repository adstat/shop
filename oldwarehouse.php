<?php

require_once(DIR_SYSTEM . 'db.php');
//require_once(DIR_SYSTEM . '/redis.php');
require_once('customer.php');
require_once('order.php');
require_once('common.php');

class OLDWAREHOUSE {

    private $dbm, $db;

    function cleanstr($str, $sym = '') {
        //Change lind break to comma
        //Remove any blank & space
        $str = trim($str);
        $str = ereg_replace("\t", $sym, $str);
        $str = ereg_replace("\r\n", $sym, $str);
        $str = ereg_replace("\r", $sym, $str);
        $str = ereg_replace("\n", $sym, $str);
        $str = ereg_replace(" ", "", $str);
        return trim($str);
    }

    function deCodeProductBatch($products) {
        //Expect format: json_decode('{"products":{"150612001002003450":1,"150612001028001480":2}}', 2) => array()
        //Output: $product_info
        global  $log;
        $product_ids = array();
        $sub_total = 0;
        foreach ($products as $key => $val) {
            $key = $this->cleanstr($key);
            if (strlen($key) !== 18) {
                $log->write('ERR:[' . __FUNCTION__ . ']' . ': 条码位数错误，停止执行' . "\n\r");
                return false;
            }

            $product_id = (int) substr($key, 6, 6);
            $product[$product_id] = array(
                'product_batch' => $key,
                'due_date' => date("Y-m-d", strtotime('20' . substr($key, 0, 6))), //There is a bug till year 2099.
                'product_id' => $product_id,
                'special_price' => round((int) substr($key, 12, 6) / 100, 2),
                'qty' => $val
            );

            $product_ids[] = $product_id;
            $sub_total += $product[$product_id]['special_price'] * $product[$product_id]['qty'];
        }

        $product_info = array(
            'product' => $product,
            'sub_total' => $sub_total,
            'product_ids' => $product_ids
        );

        return $product_info;
    }

    function deCodeProductBatch2($products) {
        //Expect format: json_decode('{"products":{"150612001002003450":1,"150612001028001480":2}}', 2) => array()
        //Output: $product_info
        global  $log;

        $product_ids = array();
        $sub_total = 0;
        foreach ($products as $key => $val) {

            //Barcode rules for Code128(18) OR Ean13(13||12)
            //18: 6+6+6
            //12: 1+5+5+x
            //13: 2+5+5+x
            if (strlen($key) == 18) {
                $product_id = (int) substr($key, 6, 6);
                $product[] = array(
                    'product_batch' => $key,
                    'due_date' => date("Y-m-d", strtotime('20' . substr($key, 0, 6))), //There is a bug till year 2099.
                    'product_id' => $product_id,
                    'special_price' => round((int) substr($key, 12, 6) / 100, 2),
                    'qty' => $val
                );
            } elseif (strlen($key) == 12 || strlen($key) == 13) {
                $product_id = (int) substr($key, 1 - (12 - strlen($key)), 5);
                $due_date = '2015-07-30';

                $product[] = array(
                    'product_batch' => $key,
                    'due_date' => $due_date, //There is a bug till year 2099.
                    'product_id' => $product_id,
                    'special_price' => round((int) substr($key, 1 - (12 - strlen($key)) + 5, 5) / 100, 2),
                    'qty' => $val
                );
            } else {
                $log->write('ERR:[' . __FUNCTION__ . ']' . ': 条码位数错误，停止执行' . "\n\r");
                return false;
            }

            $product_ids[] = $product_id;
            $sub_total += $product[$product_id]['special_price'] * $product[$product_id]['qty'];
        }

        $product_info = array(
            'product' => $product,
            'sub_total' => $sub_total,
            'product_ids' => $product_ids
        );

        return $product_info;
    }

    function deCodeProductBatchInventory($products) {
        //Expect format: json_decode('{"products":{"150612001002003450":1,"150612001028001480":2}}', 2) => array()
        //Output: $product_info

        global $db;
        global $log;
        global $dbm;

        $product_ids = array();
        $sub_total = 0;





        $product_id_str = "";

        $product_id_arr = array_keys($products);
        $product_id_str = implode(",", $product_id_arr);


        $sql = "SELECT product_id, sku, price, sku_id FROM oc_product WHERE product_id IN (" . $product_id_str . ") ";

        $query = $dbm->query($sql);
        $result = $query->rows;


        $stationProductMove = array();
        if (sizeof($result)) {
            foreach ($result as $k => $v) {
                $stationProductMove[] = array(
                    'product_batch' => $v['sku'],
                    'due_date' => '0000-00-00', //There is a bug till year 2099.
                    'product_id' => $v['product_id'],
                    'special_price' => $v['price'],
                    'qty' => $products[$v['product_id']],
                    'sku_id' => $v['sku_id']
                    //'qty' => '-'.$value['quantity']
                );
            }
        }

        return $stationProductMove;




        /*
          foreach($products as $key=>$val){

          //Barcode rules for Code128(18) OR Ean13(13||12)
          //18: 6+6+6
          //12: 1+5+5+x
          //13: 2+5+5+x
          //不同的商品二维码长度不同，如果刚好是18位
          if(strlen($key) == 18){
          $product_id = $product_barcode_arr[$key]['product_id'];
          $product[] = array(
          'product_batch' => $key,
          'due_date' => date("Y-m-d",time()+8*3600), //There is a bug till year 2099.
          'product_id' => $product_barcode_arr[$key]['product_id'],
          'special_price' => $product_barcode_arr[$key]['price'],
          'qty' => $val
          );
          }
          elseif(strlen($key) == 12 || strlen($key) == 13){
          $product_id = (int)substr($key, 1-(12-strlen($key)), 5);
          $due_date = '2015-07-30';

          $product[] = array(
          'product_batch' => $key,
          'due_date' => $due_date, //There is a bug till year 2099.
          'product_id' => $product_id,
          'special_price' => round((int)substr($key, 1-(12-strlen($key))+5, 5)/100,2),
          'qty' => $val
          );
          }
          else{
          $log->write('ERR:['.__FUNCTION__.']'.': 条码位数错误，停止执行'."\n\r");
          return false;
          }

          $product_ids[] = $product_id;
          $sub_total += $product[$product_id]['special_price'] * $product[$product_id]['qty'];
          }

          $product_info = array(
          'product' => $product,
          'sub_total' => $sub_total,
          'product_ids' => $product_ids
          );

          return $product_info;

         */
    }

    function inventoryProcess($data, $station_id, $language_id, $origin_id) {
        global $log;

        if (!is_array($data) || !sizeof($data) || !$station_id || !$origin_id) {
            $log->write('ERR:[' . $data['api_method'] . ']: 参数错误' . serialize($data) . "\n\r");
            return false;
        }

        if (!$data['station']['from'] || !$data['station']['to']) {
            $log->write('ERR:[' . $data['api_method'] . ']' . ': [' . $station_id . ']未指名来往门店代号' . "\n\r");
            return false;
        }

        $product_info = $this->deCodeProductBatch2($data['products']);
        $data_inv['products'] = $product_info['product'];
        $data_inv['from_station_id'] = $data['station']['from'];
        $data_inv['to_station_id'] = $data['station']['to'];
        $data_inv['api_method'] = $data['api_method'];

        $data_inv['timestamp'] = $data['timestamp'];

        return $this->addInventoryMove($data_inv, $station_id);
    }

    function inventoryProcessProduct($data, $station_id, $language_id, $origin_id ,$warehouse_id) {
        global $log;

        if (!is_array($data) || !sizeof($data) || !$station_id || !$origin_id) {
            $log->write('ERR:[' . $data['api_method'] . ']: 参数错误' . serialize($data) . "\n\r");
            return false;
        }

        if (!$data['station']['from'] || !$data['station']['to']) {
            $log->write('ERR:[' . $data['api_method'] . ']' . ': [' . $station_id . ']未指名来往门店代号' . "\n\r");
            return false;
        }

        $product_info = $this->deCodeProductBatchInventory($data['products']);

        $data_inv['products'] = $product_info;
        $data_inv['from_station_id'] = $data['station']['from'];
        $data_inv['to_station_id'] = $data['station']['to'];
        $data_inv['api_method'] = $data['api_method'];

        $data_inv['timestamp'] = $data['timestamp'];
        $data_inv['add_user_name'] = $data['add_user_name'];

        return $this->addInventoryMoveOrder($data_inv, $station_id,$warehouse_id);
    }

    function addInventoryMove($data, $station_id) {
        global $db, $dbm, $log;

        //$log->write('INFO:['.__FUNCTION__.']'.': '.serialize($data)."\n\r");

        if (!is_array($data) || !sizeof($data) || !$station_id) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        if (!sizeof($data['products'])) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少商品信息' . "\n\r");
            return false;
        }

        if (!isset($data['timestamp']) || !$data['timestamp']) {
            return false;
        }

        if (!defined('INVENTORY_TYPE_OP')) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少库存计算关键配置数据[INVENTORY_TYPE_OP]' . "\n\r");
            return false;
        }

        //Check timestamp
        $sql = "select inventory_move_id from oc_x_inventory_move where station_id = '" . $station_id . "' and timestamp = '" . $data['timestamp'] . "';";

        $query = $db->query($sql);
        if (sizeof($query->rows)) {
            return -1;
        }

        //Get Inventory Type Opration From config
        $inventory_type_op = unserialize(INVENTORY_TYPE_OP); //array('api method'=>array(inventory_type_id, operation))
        $inventory_op = $inventory_type_op[$data['api_method']][1];
        $inventory_type = $inventory_type_op[$data['api_method']][0];
        if (!$inventory_type) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 未指定库存变动类型' . "\n\r");
            return false;
        }

        $data_insert = array();
        $data_insert['station_id'] = $station_id;
        $data_insert['timestamp'] = $data['timestamp'];
        $data_insert['from_station_id'] = isset($data['from_station_id']) ? (int) $data['from_station_id'] : 0;
        $data_insert['to_station_id'] = isset($data['to_station_id']) ? (int) $data['to_station_id'] : 0;
        $data_insert['order_id'] = isset($data['order_id']) ? (int) $data['order_id'] : 0;
        $data_insert['inventory_type_id'] = isset($inventory_type) ? (int) $inventory_type : 0;
        $data_insert['date_added'] = date('Y-m-d H:i:s', time());
        $data_insert['added_by'] = isset($data['added_by']) ? (int) $data['added_by'] : 0;
        $data_insert['memo'] = isset($data['memo']) ? $db->escape($data['memo']) : '';

        $dbm->begin();
        $log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Begin' . "\n\r");
        $bool = true;
        $sql = "INSERT INTO `oc_x_inventory_move` SET ";
        foreach ($data_insert as $key => $val) {
            $sql .= '`' . $key . '`' . '="' . $val . '"';
            if (current($data_insert) === false) {
                $sql .= ';';
            } else {
                $sql .= ', ';
            }
            next($data_insert);
        }

        $bool = $bool && $dbm->query($sql);
        $inventory_move_id = $dbm->getLastId();
        $log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表' . $sql . "\n\r");

        $sql = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `is_gift`, `checked`, `status`) VALUES ";
        $m = 0;
        foreach ($data['products'] as $product) {
            $sql .= "(" . $inventory_move_id . ", " . $station_id . ", '" . (isset($product['due_date']) ? $product['due_date'] : '0000-00-00') . "', '" . $product['product_id'] . "', '" . $product['special_price'] . "', '" . (isset($product['product_batch']) ? $product['product_batch'] : '') . "', " . $product['qty'] * $inventory_op . ", " . (isset($product['is_gift']) ? $product['is_gift'] : 0) . ", " . (isset($product['checked']) ? $product['checked'] : 0) . "," . (isset($product['status']) ? $product['status'] : 1) . ")";
            if (++$m < sizeof($data['products'])) {
                $sql .= ', ';
            } else {
                $sql .= ';';
            }
        }
        $log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存明细表' . $sql . "\n\r");
        $bool = $bool && $dbm->query($sql);

        //If the method is init, make every other records checked
        if ($data['api_method'] == 'inventoryInit') {
            $sql = "UPDATE oc_x_inventory_move_item SET checked=1 WHERE station_id = '" . $station_id . "' AND checked = 0 AND inventory_move_id < " . $inventory_move_id;
            $bool = $bool && $dbm->query($sql);
        }

        if (!$bool) {
            $dbm->rollback();
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Rollback' . "\n\r");
            return false;
        } else {
            $dbm->commit();
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Commit' . "\n\r");
            return true;
        }

        //TODO Update inventory / Redis
    }

    function addInventoryMoveOrder($data, $station_id,$warehouse_id,$order_type) {
        global $db, $dbm, $log;

        //$log->write('INFO:['.__FUNCTION__.']'.': '.serialize($data)."\n\r");

        if (!is_array($data) || !sizeof($data) || !$station_id) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        if (!sizeof($data['products']) && $data['api_method'] != 'inventoryOrderIn') {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少商品信息' . "\n\r");
            return false;
        }

        if (!isset($data['timestamp']) || !$data['timestamp']) {
            return false;
        }

        if (!defined('INVENTORY_TYPE_OP')) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少库存计算关键配置数据[INVENTORY_TYPE_OP]' . "\n\r");
            return false;
        }
        if($warehouse_id == 10){
            $sql  = "select order_id  from  oc_x_deliver_order where   deliver_order_id = '". $data['order_id'] ."' ";

            $query = $dbm->query($sql);
            $result2 = $query->row;
            $data['order_id'] =  $result2['order_id'];
        }


        //Check timestamp
        $sql = "select inventory_move_id from oc_x_stock_move where station_id = '" . $station_id . "' and timestamp = '" . $data['timestamp'] . "';";

        $query = $db->query($sql);
        if (sizeof($query->rows)) {
            return false;
        }


        if($data['api_method'] == 'inventoryOrderIn'){

            $sql = "select xsm.order_id,o.station_id from oc_x_stock_move as xsm left join oc_order as o on o.order_id = xsm.order_id where xsm.inventory_type_id = 12 and xsm.order_id = " . (isset($data['order_id']) ? (int) $data['order_id'] : 0);
            $query = $dbm->query($sql);
            $result_exists = $query->rows;

            if(!empty($result_exists)){
                if($result_exists[0]['station_id'] != 2){
                    return false;
                }
            }

        }






        //Get Inventory Type Opration From config
        $inventory_type_op = unserialize(INVENTORY_TYPE_OP); //array('api method'=>array(inventory_type_id, operation))
        $inventory_op = $inventory_type_op[$data['api_method']][1];
        if($order_type == 2){
            $inventory_op = $inventory_op*-1;
        }
        $inventory_type = $inventory_type_op[$data['api_method']][0];

        if (!$inventory_type) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 未指定库存变动类型' . "\n\r");
            return false;
        }

        $data_insert = array();

        $data_insert['station_id'] = $station_id;
        $data_insert['timestamp'] = $data['timestamp'];
        $data_insert['from_station_id'] = isset($data['from_station_id']) ? (int) $data['from_station_id'] : 0;
        $data_insert['to_station_id'] = isset($data['to_station_id']) ? (int) $data['to_station_id'] : 0;
        $data_insert['order_id'] = isset($data['order_id']) ? (int) $data['order_id'] : 0;

        $data_insert['purchase_order_id'] = isset($data['purchase_order_id']) ? (int) $data['purchase_order_id'] : 0;

        $data_insert['inventory_type_id'] = isset($inventory_type) ? (int) $inventory_type : 0;
        $data_insert['date_added'] = date('Y-m-d H:i:s', time());
        $data_insert['added_by'] = isset($data['added_by']) ? (int) $data['added_by'] : 0;
        $data_insert['memo'] = isset($data['memo']) ? $db->escape($data['memo']) : '';
        $data_insert['add_user_name'] = isset($data['add_user_name']) ? $data['add_user_name'] : '';
        $data_insert['warehouse_id'] = $warehouse_id;
        $log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动类型：'.$data['api_method']);

        $dbm->begin();
        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Begin' . "\n\r");
        $bool = true;
        $sql = "INSERT INTO `oc_x_stock_move` SET ";
        foreach ($data_insert as $key => $val) {
            $sql .= '`' . $key . '`' . '="' . $val . '"';
            if (current($data_insert) === false) {
                $sql .= ';';
            } else {
                $sql .= ', ';
            }
            next($data_insert);
        }

        $bool = $bool && $dbm->query($sql);
        $inventory_move_id = $dbm->getLastId();

        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表' . "\n\r");

        if(!empty($data['products'])){
            $sql = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`) VALUES ";
            $m = 0;
            foreach ($data['products'] as $product) {
                $sql .= "(" . $inventory_move_id . ", " . $station_id . ", '" . (isset($product['due_date']) ? $product['due_date'] : '0000-00-00') . "', '" . $product['product_id'] . "', '" . $product['special_price'] . "', '" . (isset($product['product_batch']) ? $product['product_batch'] : '') . "', " . $product['qty'] * $inventory_op . ", '".(isset($product['box_quantity']) ? $product['box_quantity'] : 1)."', " . (isset($product['product_weight']) ? $product['product_weight'] : 0) . "," . (isset($product['is_gift']) ? $product['is_gift'] : 0) . ", " . (isset($product['checked']) ? $product['checked'] : 0) . "," . (isset($product['status']) ? $product['status'] : 1) . "," . (isset($product['sku_id']) ? $product['sku_id'] : 0) . ")";

                if (++$m < sizeof($data['products'])) {
                    $sql .= ', ';
                } else {
                    $sql .= ';';
                }
            }

            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存明细表' . "\n\r");
            $bool = $bool && $dbm->query($sql);
        }
        //If the method is init, make every other records checked
        if ($data['api_method'] == 'inventoryInit') {
            $sql = "UPDATE oc_x_stock_move_item SET checked=1 WHERE station_id = '" . $station_id . "' AND checked = 0 AND inventory_move_id < " . $inventory_move_id;
            $bool = $bool && $dbm->query($sql);
        }

        //对于指定的库存变动类型（退货入库、商品报损、库存调整），同步调整前台可售库存。（采购入库已在其他地方处理）
        $inventory_type_auto_sync = unserialize(INVENTORY_TYPE_AUTO_SYNC);

        if(in_array($inventory_type, $inventory_type_auto_sync)){
            $sql = "INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`,`warehouse_id`)
                    VALUES('".$station_id."', current_date(), unix_timestamp(), '0', '".$inventory_type."', now(), '".$data_insert['added_by']."', '".$data_insert['add_user_name']."', '[API]".$data_insert['memo']."','".$warehouse_id."')";
            $bool = $bool && $dbm->query($sql);
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动操作SQL：'.$sql);
            $inventory_move_id = $dbm->getLastId();
            //$inventory_move_id = 999;
            $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`,`warehouse_id`) VALUES';

            $m = 0;
            foreach ($data['products'] as $product) {
                //处理散件退货商品 - 散件暂时不退货可售库存
                //TODO 散件售卖
                $returnInvqty = $product['qty'];
                if(isset($product['box_quantity']) && $product['box_quantity'] > 1){
                    $returnInvqty = 0;
                }
                $sql .= "('".$inventory_move_id."','".$station_id."','".$product['product_id']."','".$returnInvqty*$inventory_op ."','". $warehouse_id ."')";
                if (++$m < sizeof($data['products'])) {
                    $sql .= ', ';
                } else {
                    $sql .= ';';
                }

            }
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动操作SQL：'.$sql);
            $bool = $bool && $dbm->query($sql);
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 可售库存变动已添加');
        }

        if (!$bool) {
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Rollback' . "\n\r");
            $dbm->rollback();
            return false;
        } else {
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Commit' . "\n\r");
            $dbm->commit();
            return true;
        }

        //TODO Update inventory / Redis
    }

    public function stationRetail($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $dbm, $log;
        global $order;

        //$log->write('INFO:['.__FUNCTION__.'] '.$data."\n\r");

        $data_submit = $data;
        $data = json_decode($data, 2);
        $data['submit_data'] = $data_submit;

        if (!is_array($data) || !sizeof($data) || !$station_id || !$origin_id) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        if (!sizeof($data['products'])) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 商品错误，无条码' . "\n\r");
            return false;
        }

        //Get Customer ID
        $customer_id = isset($data['customer']['customer_id']) ? (int) $data['customer']['customer_id'] : 0;
        if (!$customer_id) {
            $sql = "select customer_id from oc_x_station where station_id = " . $station_id;
            $query = $db->query($sql);
            $result = $query->row;

            if (sizeof($result)) {
                $customer_id = (int) $result['customer_id'];
            }
        }

        if (!$customer_id) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 无用户信息' . "\n\r");
            return false;
        }


        //If not timestamp error.
        if (!isset($data['timestamp']) || !$data['timestamp']) {
            return false;
        }

        //Prepare Customer Info
        $sql = "select A.customer_id, A.email, B.firstname, A.telephone from oc_customer A left join oc_address B on A.customer_id = B.customer_id where A.customer_id =" . $customer_id;
        $query = $db->query($sql);
        $result = $query->row;

        $data['email'] = $result['email'];
        $data['firstname'] = $result['firstname'];
        $data['lastname'] = '-';
        $data['telephone'] = $result['telephone'];
        $data['shipping_phone'] = $result['telephone'];
        $data['shipping_firstname'] = $result['firstname'];
        $data['shipping_lastname'] = '-';
        $data['payment_firstname'] = $result['firstname'];



        //Prepare Order Products
        //TODO TAKECARE OF DIFFERENT BARCODE
        $product_info = $this->deCodeProductBatch($data['products']);

        $product = $product_info['product'];

        //Get Product Name and Original Price
        $product_ids = $product_info['product_ids'];
        $sql = "select A.product_id, A.price, B.name from oc_product A left join oc_product_description B on A.product_id = B.product_id where B.language_id = " . $language_id . " and A.product_id in(" . implode(',', $product_ids) . ")";
        $query = $db->query($sql);
        $result = $query->rows;
        foreach ($result as $m) {
            $product[$m['product_id']]['name'] = $m['name'];
            $product[$m['product_id']]['price'] = (float) $m['price'];
        }

        $data['products'] = $product;
        $data['sub_total'] = $product_info['sub_total'];



        //Prepare Order Total
        //TODO Offline Discount
        //$data['discount_total'] = 0;
        $data['discount_total'] = 0;
        $discount_title = '优惠合计';

        $today = date('Y-m-d', time());

        $data['shipping_fee'] = 0;

        $data['payment_method'] = '货到付款';
        $data['payment_code'] = "COD";

        //Get User Credit
        $credit = 0;
        $sql = "select sum(amount) credit from oc_customer_transaction where customer_id =" . $customer_id;
        $query = $db->query($sql);
        $result = $query->row;
        if (sizeof($result)) {
            $credit = $result['credit'];
        }

        $data['customer_credit'] = $credit;
        $data['credit_pay'] = isset($data['customer']['use_credit']) ? $data['customer']['use_credit'] : 0;
        $data['credit_paid'] = 0;
        if ($data['credit_pay'] && $credit > 0) { //Credit必须大于零
            $tmp_total = $data['sub_total'] + $data['discount_total'] + $data['shipping_fee'];
            if ($credit >= $tmp_total) {
                $data['credit_paid'] = 0 - abs($tmp_total);

                $data['payment_method'] = '余额支付'; //全部余额支付
                $data['payment_code'] = 'CREDIT'; //TODO 支付代码
                //$data['order_payment_status_id'] = 2; //已支付
            } else {
                $data['credit_paid'] = 0 - abs($credit);

                //$data['order_payment_status_id'] = 1; //未支付，暂不使用其他状态
            }
        }
        $data['order_payment_status_id'] = 2; //线下订单均已支付

        $data['total'] = round($data['sub_total'] + $data['discount_total'] + $data['shipping_fee'] + $data['credit_paid'], 2);
        $data['totals'][] = array(
            'code' => 'sub_total',
            'title' => '小计',
            'value' => $data['sub_total'],
            'accounting' => 0,
            'sort_order' => 1
        );
        if (abs($data['discount_total']) > 0) {
            $data['totals'][] = array(
                'code' => 'discount_total',
                'title' => $discount_title,
                'value' => $data['discount_total'],
                'accounting' => 0,
                'sort_order' => 2
            );
        }
        if ($data['credit_pay']) {
            $data['totals'][] = array(
                'code' => 'credit_paid',
                'title' => '余额支付',
                'value' => $data['credit_paid'],
                'accounting' => 0,
                'sort_order' => 4
            );
        }
        $data['totals'][] = array(
            'code' => 'total',
            'title' => '总计',
            'value' => $data['total'],
            'accounting' => 1,
            'sort_order' => 5
        );



        //Prepare Order Info
        $data['store_name'] = "鲜世纪";
        $data['customer_id'] = $customer_id;
        $data['customer_group_id'] = 1;
        $data['name'] = $result['firstname'];

        $data['shipping_address'] = "-";
        $data['shipping_city'] = "上海";
        $data['shipping_postcode'] = "200000";
        $data['shipping_country'] = "China";
        $data['shipping_country_id'] = "44";
        $data['shipping_zone'] = "Shanghai";
        $data['shipping_zone_id'] = "708";

        $data['shipping_method'] = "零售";
        $data['shipping_code'] = "RTS";

        $data['affiliate_id'] = 0;
        $data['commission'] = 0;
        $data['marketing_id'] = 0;
        //$data['tracking'] = "";
        $data['language_id'] = $language_id;
        $data['currency_id'] = 4;
        $data['currency_code'] = "CNY";
        $data['currency_value'] = 1;
        //$data['ip'] = "";
        //$data['forwarded_ip'] = "";
        //$data['user_agent'] = "";
        //$data['accept_language'] = "";

        $data['station_id'] = $station_id;
        $data['origin_id'] = $origin_id;

        $data['shipping_name'] = $result['firstname'];
        $data['deliver_date'] = date("Y-m-d", time());
        $data['deliver_slot'] = "00:00:00";
        $data['deliver_now'] = 0;
        $data['area_id'] = 0;
        $data['pickupspot_id'] = 0;

        //return $data;
        $order_id = $order->addOrder(serialize($data), $station_id, $language_id, $origin_id);

        if ($order_id) {
            return array(array('order_id' => $order_id, 'timestamp' => $data['timestamp']));
        } else {
            return array(array('order_id' => 0, 'timestamp' => $data['timestamp']));
        }
    }

    public function getProductWeightInfo($data, $station_id, $language_id = 2, $origin_id) {

        global $db;

        $dataInv = json_decode($data, true);
//        print_r($dataInv);

        if (!isset($dataInv['date'])) {
            return false;
        }
//        return 123;
        $sql = "SELECT
	op.product_id,p.weight,p.weight_range_least,p.weight_range_most
FROM
	oc_x_deliver_order_product AS op
LEFT JOIN oc_x_deliver_order AS o ON o.deliver_order_id = op.deliver_order_id
left join oc_product as p on p.product_id = op.product_id
WHERE
	op.weight_inv_flag = 1
AND o.deliver_date = '" . $dataInv['date'] . "'
group by op.product_id";
//        return $sql;
        $query = $db->query($sql);
        $result = $query->rows;

        if($result == ''){
            return 123;
        }
        $product_weight_arr = array();
        foreach ($result as $key => $value) {
            $product_weight_arr[$value['product_id']]['weight'] = (int) $value['weight'];
            $product_weight_arr[$value['product_id']]['weight_range_least'] = $value['weight_range_least'];
            $product_weight_arr[$value['product_id']]['weight_range_most'] = $value['weight_range_most'];
        }
        return $product_weight_arr;
    }

    public function inventoryIn($data, $station_id, $language_id = 2, $origin_id) {

        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryIn'; //Up

        $result = $this->inventoryProcess($data_inv, $station_id, $language_id, $origin_id);

        if ($result) {
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }

    public function inventoryInProduct($data, $station_id, $language_id = 2, $origin_id) {

        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryInProduct'; //Up


        $result = $this->inventoryProcessProduct($data_inv, $station_id, $language_id, $origin_id);

        if ($result) {
            $this->adjust_post($data_inv);
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }


    public function adjust_post($data_inv,$warehouse_id,$order_type,$purchase_order_id){

        global $db, $dbm, $log;



        $products = $data_inv['products'];


        $product_ids = array_keys($products);
        $product_id_str = implode(",", $product_ids);

        $comment = '采购入库';

        $fastpin_arr = array();
        $fastpin_arr_in = array();
        $sql = "select product_id,station_id from oc_product where product_id in (" . $product_id_str . ") and station_id = 2";

        $query = $dbm->query($sql);
        $fastpin_arr = $query->rows;

        if(empty($fastpin_arr)){
            return '';
        }
        else{
            foreach($fastpin_arr as $key=>$value){
                $fastpin_arr_in[] = $value['product_id'];
            }
        }
        $fastpin_id_str = implode(",", $fastpin_arr_in);


        // 写入数据库
        //补平后台调整的快销品可售库存，避免入库前添加的可售库存和入库库存重复
        //最后一次重置库存ID
        $sql = "select * from oc_x_inventory_move where inventory_type_id = 1 and status = 1 order by inventory_move_id desc limit 1";

        $query = $dbm->query($sql);
        $reset_inventory_id = $query->row;

        //重置库存之后后台预设的快消品库存
        $preset_fastpin_arr = array();
        $preset_fastpin_product_arr = array();
        $sql = "SELECT
	imi.product_id,sum(imi.quantity) as sum_preset_quantity
FROM
	oc_x_inventory_move_item AS imi
LEFT JOIN oc_x_inventory_move AS im ON im.inventory_move_id = imi.inventory_move_id

where im.inventory_move_id > " . $reset_inventory_id['inventory_move_id'] . "
and im.inventory_type_id = " . INVENTORY_TYPE_PRESET . "
and imi.product_id in (" . $fastpin_id_str . ")
group by imi.product_id";

        $query = $dbm->query($sql);
        $preset_fastpin_arr = $query->rows;

        foreach ($preset_fastpin_arr as $key=>$value){
            if($value['sum_preset_quantity'] != 0){
                $preset_fastpin_product_arr[$value['product_id']] = 0 - $value['sum_preset_quantity'];
            }
        }




        $time = time();
        $date = date("Y-m-d", $time);
        $date_added = date("Y-m-d H:i:s", $time);
        $user_id = 0;
        $user_name = $data_inv['add_user_name'];

        $dbm->query("START TRANSACTION");

        //添加预设库存记录，与之前的预设库存记录合计数量为0，然后以实际入库商品数量为可售库存
        if(!empty($preset_fastpin_product_arr)){

            $dbm->query("INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`,`warehouse_id`) VALUES('2', '{$date}', '{$time}', '1', '" . INVENTORY_TYPE_PRESET . "', '{$date_added}', '{$user_id}', '{$user_name}', '重置预设库存为0','{$warehouse_id}')");
            $inventory_move_id = $dbm->getLastId();

            $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`,`warehouse_id`,`purchase_order_id`) VALUES';

            foreach($preset_fastpin_product_arr as $key=>$product){
                if(in_array($key, $fastpin_arr_in)){
                    $sql .= "('{$inventory_move_id}', '2', '{$key}', '{$product}','{$warehouse_id}','{$purchase_order_id}'),";

                }
            }
            if(substr($sql, strlen($sql)-1,1) == ','){
                $sql = substr($sql, 0,-1);
            }


            $dbm->query($sql);
        }

        $dbm->query("INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`,`warehouse_id`) VALUES('2', '{$date}', '{$time}', '1', '" . INVENTORY_TYPE_STOCK_IN . "', '{$date_added}', '{$user_id}', '{$user_name}', '{$comment}','{$warehouse_id}')");
        $inventory_move_id = $dbm->getLastId();
        $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`,`warehouse_id`,`purchase_order_id`) VALUES';



        foreach($products as $key=>$product){
            if($order_type == 2){
                $product = $product*-1;
            }
            if(in_array($key, $fastpin_arr_in)){
                $sql .= "('{$inventory_move_id}', '2', '{$key}', '{$product}','{$warehouse_id}','{$purchase_order_id}'),";

            }
        }
        if(substr($sql, strlen($sql)-1,1) == ','){
            $sql = substr($sql, 0,-1);
        }



        $dbm->query($sql);

        $dbm->query("update oc_product set status = 1 where product_id in (" . $fastpin_id_str . ")");
        $dbm->query("update oc_product_to_warehouse set status = 1 where product_id in (" . $fastpin_id_str . ") and warehouse_id = '".$warehouse_id ."' ");
        $dbm->query('COMMIT');


    }






    public function inventoryOutProduct($data, $station_id, $language_id = 2, $origin_id) {

        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryOutProduct'; //Up

        $warehouse_id = $data_inv['warehouse_id'];




        $result = $this->inventoryProcessProduct($data_inv, $station_id, $language_id, $origin_id,$warehouse_id);

        if ($result) {
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }

    public function inventoryAdjustProduct($data, $station_id, $language_id = 2, $origin_id) {

        global $db;
        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryAdjust'; //Up

        $sql = "select date_added from oc_x_stock_move where inventory_type_id = 14 order by inventory_move_id desc limit 1";

        $query = $db->query($sql);
        $result = $query->row;

        $last_add_check_date = $result['date_added'];

        /*
        if (time()+8*3600 - strtotime($last_add_check_date) > 3600*3) {
            return array(array('status' => 3, 'timestamp' => $data_inv['timestamp']));

        }
        */


        $result = $this->inventoryProcessProduct($data_inv, $station_id, $language_id, $origin_id);

        if ($result) {
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }


    public function inventoryChangeProduct($data, $station_id, $language_id = 2, $origin_id) {

        global $db;
        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryChange'; //Up
        $warehouse_id = $data_inv['warehouse_id'];
        //获取所有促销商品
        $promotion_product_id_arr = array();
        $sql = "select product_id from oc_product_to_promotion_product";
        $query = $db->query($sql);
        $result = $query->rows;
        foreach ($result as $key => $value) {
            $promotion_product_id_arr[] = $value['product_id'];
        }
        foreach ($data_inv['products'] as $k => $v) {
            if (!in_array($k, $promotion_product_id_arr)) {
                return array(array('status' => 2, 'timestamp' => $data_inv['timestamp'], 'product_id' => $k));
                break;
            }
        }



        $result = $this->inventoryProcessProduct($data_inv, $station_id, $language_id, $origin_id,$warehouse_id);

        if ($result) {
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }

    public function inventoryCheckProduct($data, $station_id, $language_id = 2, $origin_id) {

        global $dbm;
        $data_inv = json_decode($data, 2);

        $sql = "insert into oc_x_inventory_check_sorting (product_id,quantity,uptime,added_by,warehouse_id) "
            . "values ";
        $i = 1;
        foreach ($data_inv['products'] as $product_id => $product_quantity) {
            if ($i == count($data_inv['products'])) {
                $sql .= "(" . $product_id . "," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "','" . $data_inv['warehouse_id'] . "')";
            } else {
                $sql .= "(" . $product_id . "," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "','" . $data_inv['warehouse_id'] . "'),";
            }
            $i++;
        }

        //$log->write($sql."\n\r");



        if ($query = $dbm->query($sql)) {
            $result = 1;
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }


    public function inventoryCheckSingleProduct($data, $station_id, $language_id = 2, $origin_id) {

        global $dbm;
        $data_inv = json_decode($data, 2);
        $date = $data_inv['date'];
        $warehouse_id = $data_inv['warehouse_id'];
        foreach ($data_inv['products'] as $product_id => $product_quantity) {
            $productid = $product_id;
        };


        //获取最早盘点值
        $sql_move  = " select   max(inventory_move_id) inventory_move_id  from oc_x_stock_move  WHERE  inventory_type_id = 14  and warehouse_id = '".$warehouse_id ."'  ";

        $query = $dbm->query($sql_move);
        $result_move = $query->row;

        //获取该商品最早的盘点值
        $sql_move_item = "select smi.quantity item_quantity  from  oc_x_stock_move_item  smi WHERE  smi.inventory_move_id   = '". $result_move['inventory_move_id'] ."'  and  smi.product_id = '".$productid ."'";


        $query = $dbm->query($sql_move_item);
        $result_move_item = $query->row;

        //获取该商品从最早的盘点值到提交时候的库存值

        $sql_real = "select  sum(smi.quantity) real_quantity from  oc_x_stock_move sm LEFT join oc_x_stock_move_item smi on sm.inventory_move_id = smi.inventory_move_id WHERE  smi.product_id = '".$productid."'and  sm.inventory_move_id >= '". $result_move['inventory_move_id'] . "' and  sm.warehouse_id = '".$warehouse_id ."' ";

        $query = $dbm->query($sql_real);
        $result_real = $query->row;


        //获取分拣占用的数量包括分拣中，跟待审核的订单
        $sql_occupy  = " select  sum(iso.quantity) occupy_quantity from oc_order o LEFT JOIN  oc_x_inventory_order_sorting iso ON  o.order_id = iso.order_id LEFT JOIN oc_x_stock_move sm on o.order_id = sm.order_id   WHERE  iso.product_id = '".$productid."' and iso.move_flag = 0 and o.order_status_id in (1,2,5,6,8) and o.warehouse_id ='".$warehouse_id ."' and iso.status =1  and sm.order_id is null  ";


        $query = $dbm->query($sql_occupy);
        $result_occupy = $query->row;

//        //获取下单未分拣的值
//        $sql_order = " select  sum(op.quantity) order_quantity  from   oc_order o LEFT JOIN  oc_order_product op on o.order_id = op.order_id where o.warehouse_id = '".$warehouse_id ."' and o.order_status_id in (1,2,5,8) and op.product_id = '".$product_id ."' group by op.product_id  ";
//        $query = $dbm->query($sql_order);
//        $result_order = $query->row;


        $sql = "insert into oc_x_inventory_check_single_sorting (product_id,inv_quantity,quantity,uptime,added_by,remark,remark_2,move_flag,warehouse_id,occupy_quantity) "
            . "values ";
        $i = 1;
        $error = 0;
        foreach ($data_inv['products'] as $product_id => $product_quantity) {

            $sql2 = "select * from oc_x_inventory_check_single_sorting where warehouse_id = '".$warehouse_id."' and  product_id = " . $product_id . " and move_flag = 0 and uptime > '" . $date . " 00:00:00' and uptime < '" . $date . " 24:00:00'";

            $query = $dbm->query($sql2);
            $result = $query->rows;
            if(!empty($result)){
                $error = 1;
                break;
            }

            $move_flag = 0;
            if($data_inv['products_inv'][$product_id] == ($product_quantity + $result_occupy['occupy_quantity'])){
                $move_flag = 0;
            }

            if ($i == count($data_inv['products'])) {
                $sql .= "(" . $product_id . ",'" . $result_real['real_quantity'] . "'," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "','" . $data_inv['remark'] . "','" . $data_inv['remark_2'] . "'," . $move_flag . " , '". $warehouse_id."','".$result_occupy['occupy_quantity'] ."')";
            } else {
                $sql .= "(" . $product_id . ",'" . $result_real['real_quantity'] . "'," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "','" . $data_inv['remark'] . "','" . $data_inv['remark_2'] . "'," . $move_flag . " , '". $warehouse_id."','".$result_occupy['occupy_quantity'] ."'),";
            }
            $i++;
        }

        //$log->write($sql."\n\r");

        if($error == 1){

            return array(array('status' => 5, 'msg' => "此商品还有未处理的盘盈盘亏操作，请先处理"));
        }


        if ($query = $dbm->query($sql)) {
            $result = 1;
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }

    public function inventoryVegCheckProduct($data, $station_id, $language_id = 2, $origin_id) {

        global $dbm;
        $data_inv = json_decode($data, 2);


        $sql = "insert into oc_x_inventory_veg_check_sorting (product_id,quantity,uptime,added_by,product_barcode) "
            . "values ";
        $i = 1;

        foreach ($data_inv['products'] as $product_id => $product_quantity) {

            if(empty($data_inv['product_barcode_arr'][$product_id])){
                $data_inv['product_barcode_arr'][$product_id] = array();
            }

            if ($i == count($data_inv['products'])) {
                $sql .= "(" . $product_id . "," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "','" . json_encode($data_inv['product_barcode_arr'][$product_id],true) . "')";
            } else {
                $sql .= "(" . $product_id . "," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "','" . json_encode($data_inv['product_barcode_arr'][$product_id],true) . "'),";
            }
            $i++;
        }

        //$log->write($sql."\n\r");



        if ($query = $dbm->query($sql)) {
            $result = 1;
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }
    public function inventoryOut($data, $station_id, $language_id = 2, $origin_id) {

        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryOut'; //Down

        $result = $this->inventoryProcess($data_inv, $station_id, $language_id, $origin_id);

        if ($result) {
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }

    public function inventoryReturn($data, $station_id, $language_id = 2, $origin_id) {


        $data_inv = json_decode($data, 2);



        //$data_inv['api_method'] = 'inventoryReturn'; //Down


        $this->addInventoryMoveOrder($data_inv, $station_id);
    }

    public function inventoryInit($data, $station_id, $language_id = 2, $origin_id) {

        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryInit'; //Init.

        $result = $this->inventoryProcess($data_inv, $station_id, $language_id, $origin_id);

        if ($result) {
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }

    public function inventoryBreakage($data, $station_id, $language_id = 2, $origin_id) {
        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryBreakage'; //Down

        $result = $this->inventoryProcess($data_inv, $station_id, $language_id, $origin_id);

        if ($result) {
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }

    public function inventoryNoonCheck($data, $station_id, $language_id = 2, $origin_id) {

        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryNoonCheck'; //Init.

        $result = $this->inventoryProcess($data_inv, $station_id, $language_id, $origin_id);

        if ($result) {
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }

    public function pushOrderByStation($data, $station_id, $language_id = 2, $origin_id) {
        //$data= '{"date":"2015-06-06","limit":"0,10"}';
        //$data= '{"date":"2015-06-06","order_id":"0","deliver_now":"1","pickup":"0","retail":"0","limit":"0,10"}';

        global $log;

        $data = json_decode($data, 2);
        if (!sizeof($data)) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        $data['push'] = 1;

        return $this->getOrderByStation(json_encode($data), $station_id, $language_id, $origin_id);
    }

    public function getOrderByStation($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;

        $data = json_decode($data, 2);
        if (!sizeof($data)) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        $limit = explode(',', $data['limit']);
        if (!is_numeric($limit[0]) || !is_numeric($limit[1])) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 查询条件Limit参数错误' . "\n\r");
            return false;
        }

        $sql = "select A.order_id, date(A.date_added) order_date, A.deliver_date, A.deliver_slot, A.deliver_now, A.shipping_method, D.name order_status, A.sub_total, A.discount_total, A.shipping_fee, A.credit_paid, A.total order_total, sum(E.value) order_due, A.payment_method, B.name payment_status, C.name deliver_status, A.shipping_name, A.shipping_phone, A.shipping_address_1 shipping_address
from oc_order A
left join oc_order_payment_status B on A.order_payment_status_id = B.order_payment_status_id
left join oc_order_deliver_status C on A.order_deliver_status_id = C.order_deliver_status_id
left join oc_order_status D on A.order_status_id = D.order_status_id
left join oc_order_total E on A.order_id = E.order_id and E.accounting = 1";
        if ($data['order_id']) {
            $sql .= " where A.station_id = " . $station_id . " and A.order_id = '" . $data['order_id'] . "'";
        } else {
            $sql .= " where A.station_id = " . $station_id . " and A.deliver_date = '" . $data['date'] . "'";

            // Conditions for different order types => OR
//            if($data['deliver_now'] || $data['pickup'] || $data['retail']){
//                $sql .= " and (";
//                    if($data['deliver_now']){
//                        $sqlwhere[] = " A.deliver_now = 1 ";
//                    }
//                    if($data['pickup']){
//                        $sqlwhere[] = " A.shipping_code = 'PSPOT' ";
//                    }
//
//                    if($data['retail']){
//                        $sqlwhere[] = " A.shipping_code = 'RTS' ";
//                    }
//
//                    foreach($sqlwhere as $m){
//                        $sql .= $m;
//                        if(current($sqlwhere) !== false && sizeof($sqlwhere) > 1){
//                            $sql .= ' or ';
//                        }
//                        next($sqlwhere);
//                    }
//
//                $sql .= " )";
//            }
            if ($data['deliver_now']) {
                $sql .= " and A.deliver_now = 1 ";
            } else {
                if ($data['pickup']) {
                    $sql .= " and A.deliver_now = 0 and A.shipping_code = 'PSPOT' ";
                }

                if ($data['retail']) {
                    $sql .= " and A.deliver_now = 0 and A.shipping_code = 'RTS' ";
                }
            }

            if (isset($data['push']) && $data['push']) {
                $sql .= " and A.order_status_id = 1 and A.deliver_now = 1 ";
            } else {
                $sql .= " and A.order_status_id not in (" . CANCELLED_ORDER_STATUS . ")";
            }
        }

        $sql .= " group by A.order_id order by A.order_id desc ";
        if ($data['limit']) {
            $sql .= " limit " . $data['limit'];
        }

        //return $sql;

        $query = $db->query($sql);
        $result = $query->rows;

        if (sizeof($result)) {
            return $result;
        } else {
            return array();
        }
    }

    public function getStationOrderProduct($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;

        $data = json_decode($data, 2);
        if (!isset($data['order_id']) || !$data['order_id']) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        //Prevent overpay
        $sql = "SELECT  B.product_id, B.name product_name, B.quantity product_qty, B.price product_price, B.total sub_total, B.status from oc_order A LEFT JOIN oc_order_product B ON A.order_id = B.order_id";
        $sql .= " WHERE A.order_id='" . $data['order_id'] . "' AND A.station_id=" . $station_id;
        $query = $db->query($sql);
        $result = $query->rows;

        if (sizeof($result)) {
            //TODO log
            return $result;
        } else {
            return array();
        }
    }

    public function stationOrderCancel($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;
        global $common;

        //TODO Cancel Order Refund
        $data = json_decode($data, 2);
        if (!isset($data['order_id']) || !$data['order_id']) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        $sql = "select order_id from oc_order where station_id =" . $station_id . " and order_id='" . $data['order_id'] . "'";
        $query = $db->query($sql);
        $result = $query->row;
        if (sizeof($result)) {
            $user_id = 10000 + $station_id;
            if ($common->orderStatus($data['order_id'], CANCELLED_ORDER_STATUS, (int) $user_id, $data['reason_id'], $data['reson'])) {  //It is reson
                return array(array('status' => 1, 'message' => 'Success'));
            } //Cancel Order
            else {
                return array(array('status' => 0, 'message' => 'Failed'));
            }
        } else {
            $log->write('INFO:[' . __FUNCTION__ . ']' . $data['order_id'] . ': 非此站点(' . $station_id . ')订单' . "\n\r");
            return array('status' => 0, 'message' => 'Failed, station and order not match');
        }
    }

    public function stationOrderConfirm($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;
        global $common;

        $data = json_decode($data, 2);
        if (!isset($data['order_id']) || !$data['order_id']) {
            $log->write('INFO:[' . __FUNCTION__ . ']: 参数错误' . "\n\r");
            return false;
        }

        $sql = "select order_id from oc_order where station_id =" . $station_id . " and order_id='" . $data['order_id'] . "'";
        $query = $db->query($sql);
        $result = $query->row;
        if (sizeof($result)) {
            $user_id = 10000 + $station_id;

            if ($common->orderStatus($data['order_id'], 2, (int) $user_id)) {
                return array(array('status' => 1, 'message' => 'Success'));
            } else {
                return array(array('status' => 0, 'message' => 'Failed'));
            }
        } else {
            $log->write('INFO:[' . __FUNCTION__ . ']' . $data['order_id'] . ': 非此站点(' . $station_id . ')订单' . "\n\r");
            return array('status' => 0, 'message' => 'Failed, station and order not match');
        }
    }

    public function stationOrderDelivered($data, $station_id, $language_id = 2, $origin_id) {

        global $db, $log;
        global $common;

        $data = json_decode($data, 2);
        if (!isset($data['order_id']) || !$data['order_id']) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        $sql = "select order_id from oc_order where station_id =" . $station_id . " and order_id='" . $data['order_id'] . "'";
        $query = $db->query($sql);
        $result = $query->row;
        if (sizeof($result)) {
            $user_id = 10000 + $station_id;

            if ($common->deliverStatus($data['order_id'], 3, (int) $user_id)) {
                return array(array('status' => 1, 'message' => 'Success'));
            } else {
                return array(array('status' => 0, 'message' => 'Failed'));
            }
        } else {
            $log->write('INFO:[' . __FUNCTION__ . ']' . $data['order_id'] . ': 非此站点(' . $station_id . ')订单' . "\n\r");
            return array('status' => 0, 'message' => 'Failed, station and order not match');
        }
    }

    public function stationOrderDeliverOut($data, $station_id, $language_id = 2, $origin_id) {

        global $db, $log;
        global $common;

        $data = json_decode($data, 2);
        if (!isset($data['order_id']) || !$data['order_id']) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        $sql = "select order_id from oc_order where station_id =" . $station_id . " and order_id='" . $data['order_id'] . "'";
        $query = $db->query($sql);
        $result = $query->row;
        if (sizeof($result)) {
            $user_id = 10000 + $station_id;

            if ($common->deliverStatus($data['order_id'], 2, (int) $user_id)) {
                return array(array('status' => 1, 'message' => 'Success'));
            } else {
                return array(array('status' => 0, 'message' => 'Failed'));
            }
        } else {
            $log->write('INFO:[' . __FUNCTION__ . ']' . $data['order_id'] . ': 非此站点(' . $station_id . ')订单' . "\n\r");
            return array('status' => 0, 'message' => 'Failed, station and order not match');
        }
    }

    public function stationOrderDeliverTimeChange($data, $station_id, $language_id = 2, $origin_id) {
        //TODO
    }

    public function getStationCustomerInfo($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;
        global $customer;

        $data = json_decode($data, 2);
        if (!isset($data['customer_id']) || !$data['customer_id']) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        $customer_id = $data['customer_id'];

        $sql = "select
            A.firstname,
            A.telephone
            from oc_customer as A
            where A.customer_id = {$customer_id}";

        $query = $db->query($sql);
        $results = $query->row;

        $customer_info['name'] = $results['firstname'];
        $customer_info['credit'] = $customer->getCustomerCredit($customer_id, $station_id, $language_id, $origin_id);
        $customer_info['phone'] = $results['telephone'];

        //Get order info
        $orders = array();
        $sql = "SELECT A.order_id, A.orderstamp, A.shipping_method, A.shipping_code,
            round(A.total,2) total, round(A.credit_paid,2) credit_paid, round(A.sub_total+A.shipping_fee+A.discount_total,2) order_total,
            if(sum(T.value)<0, 0, round(sum(T.value),2) ) due,
            A.deliver_date, A.shipping_name, left(A.date_added,10) order_date, A.sub_total, A.shipping_fee, A.shipping_address_1,
            A.order_status_id, A.order_payment_status_id, A.order_deliver_status_id, B.name order_status, C.name order_payment_status, D.name order_deliver_status,
            A.payment_method, A.payment_code, PS.name ps_name, PS.address ps_address
            FROM oc_order A
            LEFT JOIN oc_order_status B ON A.order_status_id = B.order_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_total T ON A.order_id = T.order_id AND T.accounting = 1
            LEFT JOIN oc_order_payment_status C ON A.order_payment_status_id = C.order_payment_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id  = D.order_deliver_status_id AND B.language_id = {$language_id}
            LEFT JOIN oc_x_pickupspot PS ON A.pickupspot_id  = PS.pickupspot_id
            WHERE A.customer_id = {$customer_id} and A.station_id = {$station_id}
            GROUP BY T.order_id
            ORDER BY order_id DESC LIMIT 5";

        $query = $db->query($sql);
        $orders = $query->rows;

        $customer_info['orders'] = $orders;

        if ($orders) {
            return array($customer_info);
        } else {
            return array();
        }
    }

    public function getStationProductInfo_bak($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;

        //Expect Data: $data= '{"products":"150628001085001050,150628001053001250"}';

        $data_inv = json_decode($data, 2);
        if (!isset($data_inv['products']) || !$data_inv['products']) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . $data . "\n\r");
            return false;
        }

        $products = explode(',', $data_inv['products']);
        $product_info = array();
        foreach ($products as $product) {
            //$product_id = (int)substr($product, 6, 6);
            //$barcode_info[$product_id] = array(
            //    'due_data' => date("Y-m-d",strtotime('20'.substr($product, 0, 6))),
            //    'retail_price' => round((int)substr($product, 12, 6)/100,2)
            //);
            //$product_ids[] = $product_id;
            //Barcode rules for Code128(18) OR Ean13(13||12)
            //18: 6+6+6
            //12: 1+5+5+x
            //13: 2+5+5+x
            if (strlen($product) == 18) {
                $product_id = (int) substr($product, 6, 6);
                $barcode_info[$product_id] = array(
                    'due_date' => date("Y-m-d", strtotime('20' . substr($product, 0, 6))), //There is a bug till year 2099.
                    'retail_price' => round((int) substr($product, 12, 6) / 100, 2)
                );
            } elseif (strlen($product) == 12 || strlen($product) == 13) {
                $product_id = (int) substr($product, 1 - (12 - strlen($product)), 5);
                $due_date = '2015-07-30';

                $barcode_info[$product_id] = array(
                    'due_date' => $due_date,
                    'retail_price' => round((int) substr($product, 1 - (12 - strlen($product)) + 5, 5) / 100, 2)
                );
            } else {
                $product_id = 0;
            }

            $product_ids[] = $product_id;
        }

        $sql = "select
            A.product_id,
            B.name,
            round(A.price,2) ori_price,
            round(if(isnull(D.price),A.price,D.price),2 ) sale_price,
            round(E.price,2) station_promo_price,
            if(A.weight_class_id=1, concat( round( if(isnull(D.price),A.price,D.price)/(A.weight/500), 2 ) , '/斤'), NULL) sale_price_500g,
            if(A.weight_class_id=1, concat(round(E.price/(A.weight/500),2) , '/斤'), NULL ) station_promo_price_500g,
            concat(round(A.weight,0), C.title) unit,
            A.shelf_life,
            now() checktime
            from xsj.oc_product A
            left join xsj.oc_product_description B on A.product_id = B.product_id and B.language_id = 2
            left join xsj.oc_weight_class_description C on A.weight_class_id = C.weight_class_id and C.language_id = 2
            left join  xsj.oc_product_special D on (A.product_id = D.product_id and now() between D.date_start and D.date_end)
            left join xsj.oc_product_promo E on A.product_id = E.product_id
            where A.product_id in (" . implode(',', $product_ids) . ")";

        $query = $db->query($sql);
        $results = $query->rows;

        if (sizeof($results)) {
            foreach ($results as $m) {
                $product_info[$m['product_id']] = $m;
                $product_info[$m['product_id']]['due_data'] = $barcode_info[$m['product_id']]['due_data'];
                $product_info[$m['product_id']]['retail_price'] = $barcode_info[$m['product_id']]['retail_price'];
            }

            $products = array();
            foreach ($product_info as $m) {
                $products[] = $m;
            }

            return $products;
        } else {
            return array();
        }
    }

    public function getUserInfoByUid($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;

        //Expect Data: $data= '{"uid":"orRNvt7MYe4zfbnl7u5p7hXykNcg"}';

        $data = json_decode($data, 2);

        $sql = "SELECT * FROM oc_user WHERE uid = '" . $db->escape($data['uid']) . "'";
        $query = $db->query($sql);
        $result = $query->row;

        if ($result) {
            return $result;
        }

        return array();
    }

    public function getStationMove($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;

        //Expect Data: $data= '{"date":"2015-06-26", "date_gap":"1"}';
        //$log->write('INFO:['.__FUNCTION__.']'.': 参数'.$data."\n\r");

        $data = json_decode($data, 2);

        $sql = "select ";
        $sql .= " A.inventory_move_id, A.inventory_type_id, A.station_id, A.date_added, C.name move_name, D.name station_title, SUM(B.quantity) total_qty, ";
        $sql .= " A.confirmed, A.memo, A.printed, A.print_time, A.last_print_time, ";
        $sql .= " SUM(B.quantity) total_qty ";
        $sql .= " from oc_x_inventory_move A ";
        $sql .= " left join oc_x_inventory_move_item as B on A.inventory_move_id = B.inventory_move_id and B.status = 1 ";
        $sql .= " left join oc_x_inventory_type as C on A.inventory_type_id = C.inventory_type_id ";
        $sql .= " left join oc_x_station D on A.station_id = D.station_id ";
        if ((int) $data['date_gap'] > 1) {
            $sql .= " where A.date_added between date_add(date('" . $data['date'] . "'), INTERVAL -(" . (int) $data['date_gap'] . ") DAY) and '" . $data['date'] . "' ";
        } else {
            $sql .= " where date(A.date_added) = '" . $data['date'] . "' ";
        }
        $sql .= " and A.station_id = '" . $station_id . "' ";
        $sql .= " group by A.inventory_move_id";
        $sql .= " order by A.date_added desc";

        //$log->write('INFO:['.__FUNCTION__.']'.': 查询'.$sql."\n\r");

        $query = $db->query($sql);
        $result = $query->rows;

        if (sizeof($result)) {
            return $result;
        }

        return array();
    }

    public function getStationMoveItem($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;

        //Expect Data: $data= '{"inventory_move_id":"1"}';
        $data = json_decode($data, 2);

        $sql = "select";
        $sql .= " B.product_id, D.name, round(B.price,2) price, B.quantity";
        $sql .= " from oc_x_inventory_move A";
        $sql .= " left join oc_x_inventory_move_item as B on A.inventory_move_id = B.inventory_move_id";
        $sql .= " left join oc_x_inventory_type as C on A.inventory_type_id = C.inventory_type_id";
        $sql .= " left join oc_product_description as D on B.product_id = D.product_id and D.language_id = 2";
        $sql .= " where A.inventory_move_id = '" . $data['inventory_move_id'] . "' and B.status = 1";
        $sql .= " order by B.product_id";

        $query = $db->query($sql);
        $result = $query->rows;

        if (sizeof($result)) {
            return $result;
        }

        return array();
    }

    public function confirmStationMove($data, $station_id, $language_id = 2, $origin_id) {
        global $dbm, $log;

        //Expect Data: $data= '{"inventory_move_id":"2", "confirmed_by":"3", "confirm_user_name":"Alex"}';
    }

    public function getSmsVerifyCode($data) {
        global $dbm, $log;
        global $common;

        //Expect Data: $data= '{"phone":"18616553486"}';
        $data = json_decode($data, 2);

        $patten = '123456789';
        $code = $common->randomkeys(6, $patten);
        $time = time();

        $dbm->begin();
        $bool = true;
        $sql = 'update oc_x_station_user set vcode = "' . $code . '", vcode_timestamp = "' . $time . '", vcode_life = 5 where phone = "' . $data['phone'] . '" and status = 1';
        $bool = $bool && $dbm->query($sql);

        $sql = "INSERT INTO `oc_msg` (`phone`, `msg_param_1`, `msg_param_2`, `isp_template_id`, `msg_type`, `sent`, `status`, `date_added`)
                VALUES('" . $data['phone'] . "', " . $code . ", '5', 18703, 'RTS', 0, 1, NOW())";
        $bool = $bool && $dbm->query($sql);

        if (!$bool) {
            $dbm->rollback();
            //$log->write('ERR:['.__FUNCTION__.']'.': 商家登陆获取验证码 Rollback'."\n\r");
            return array(array('status' => 0));
        } else {
            $dbm->commit();
            //$log->write('INFO:['.__FUNCTION__.']'.': 商家登陆获取验证码 Commit'."\n\r");
            return array(array('status' => 1));
        }
    }

    public function getStationOriginKey($data) {
        global $dbm, $log;

        //Expect Data: $data= '{"phone":"18616553486","vcode":"123456"}';
        $data = json_decode($data, 2);
        $sql = 'select A.station_user_id, A.station_id, A.name, B.name station_title, B.customer_id from oc_x_station_user A left join oc_x_station B on A.station_id = B.station_id where A.phone = "' . $data['phone'] . '"  and A.vcode="' . $data['vcode'] . '" and A.status = 1 limit 1';
        $query = $dbm->query($sql);

        $result = $query->row;

        if (sizeof($result)) {
            $auth = unserialize(AUTHKEY);

            $sql = 'update oc_x_station_user set vcode="" where station_user_id = "' . $result['station_user_id'] . '"';
            $dbm->query($sql);

            return array(array('name' => $result['name'], 'station_title' => $result['station_title'], 'customer_id' => $result['customer_id'], 'station_id' => $result['station_id'], 'message' => 'Test Station Message.', 'apiorigin' => '7', 'apikey' => $auth[7]));
        } else {
            return array(array('status' => 0));
        }
    }

    public function getStationInfo($data, $station_id, $language_id = 2, $origin_id) {
        global $dbm, $log;

        //Expect Data: $data='{"station_user_id":1}';
        $data = json_decode($data, 2);
        $sql = 'select A.station_user_id, A.station_id, A.name, B.name station_title, B.customer_id, A.status from oc_x_station_user A left join oc_x_station B on A.station_id = B.station_id
where A.station_user_id = "' . $data['station_user_id'] . '" and A.logined = 1';
        $query = $dbm->query($sql);

        $result = $query->row;
        if (sizeof($result)) {
            if ($result['status'] == 0) {
                return array(array('status' => -1));
            } else {
                return array(array('status' => 1, 'name' => $result['name'], 'station_title' => $result['station_title'], 'customer_id' => $result['customer_id'], 'station_id' => $result['station_id'], 'message' => 'Test Station Message, test 123.'));
            }
        } else {
            return array(array('status' => 0));
        }
    }

    public function checkUpdate($data) {
        //Expect Data: $data= '{"version":"1.0"}';
        $data = json_decode($data, 2);

        if ($data['version'] < 1.1) {
            return array(array('url' => 'http://demo.xianshiji.com/apk/xsj_update.apk'));
        }
        return array();
    }

    //Get Product Inentory Info, for cart page and add product to cart action
    public function getProductInventory($data = '', $station_id, $language_id = 2, $origin_id) {
        global $db, $log;

        //TODO Check Product
        $data = json_decode($data, 2);
        $customer_id = isset($data['customer_id']) ? (int) $data['customer_id'] : 0;

        $sql = "select B.product_id, B.minimum, B.maximum,
                sum(A.quantity) inventory_raw,
                B.safestock,
                if(B.instock=1, if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-B.safestock, 999) inventory
                from oc_product B
                left join oc_x_inventory_move_item A on B.product_id = A.product_id and A.status=1 and A.station_id = '" . $station_id . "'
                group by B.product_id";

        if (isset($data['cartItem']) && sizeof($data['cartItem'])) {
            $cart = array(1);
            foreach ($data['cartItem'] as $val) {
                $cart[] = (int) $val;
            }

//            $sql = "select B.product_id, B.minimum, B.maximum, sum(A.quantity) inventory_raw, B.safestock, if(sum(A.quantity) is null, 0,sum(A.quantity))-B.safestock inventory from oc_product B
//                left join oc_x_inventory_move_item A on B.product_id = A.product_id and A.status=1 and A.station_id = '".$station_id."'
//                and A.product_id in (" . implode(',',$cart) . ")
//                group by B.product_id";

            $sql = "select B.product_id, B.minimum, B.maximum, B.safestock,
                abs(sum(if(A.customer_id = '" . $customer_id . "' and A.status = 1, A.quantity, 0))) customer_ordered_today,
                abs(sum(if(A.customer_id = '" . $customer_id . "' and A.status = 0, A.quantity, 0))) customer_ordered_tmr,
                sum(if(A.status = 1, A.quantity, 0 )) inventory_raw,
                if(B.instock=1, if(sum(if(A.status = 1, A.quantity, 0 )) is null or sum(if(A.status = 1, A.quantity, 0 ))<0, 0, sum(if(A.status = 1, A.quantity, 0 )))-B.safestock, 999) inventory
                from oc_product B
                left join oc_x_inventory_move_item A on B.product_id = A.product_id
                where A.product_id in (" . implode(',', $cart) . ")
                group by B.product_id";
        }

        $query = $db->query($sql);
        $result = $query->rows;

        if (sizeof($result)) {
            return array('status' => 'SUCCESS', 'message' => 'OK', 'data' => $result);
        } else {
            return array('status' => 'ERROR', 'message' => 'No valid inventory data.', 'data' => array());
        }
    }

    public function getOrderSortingList($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $db;
        global $dbm;
        global $log;

        $data = json_decode($data, 2);

        //$date = isset($data['date']) ? $data['date'] : false;
        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        $user_group_id = isset($data['user_group_id']) ? $data['user_group_id'] : false;
        $repack = isset($data['repack']) ? $data['repack'] : false;
        $warehouse_repack = isset($data['warehouse_repack']) ? $data['warehouse_repack'] : 0;
        $user_repack = isset($data['user_repack']) ? $data['user_repack'] : 0;
        $frame_num = isset($data['frame_num']) ? $data['frame_num'] : 0;
//return $data;
        if (!$order_id) {
            return false;
        }

//        $sql = "select  order_status_id from oc_order where order_id = '". $order_id ."'" ;
//
//        $query = $db->query($sql);
//        $orderStatus = $query->row;

        $orderStatus = $this->getDeliverOrderInfo($order_id) ;


        if($orderStatus['order_status_id'] == 3){
            return false;
        }

        $sql = "select station_id , do_warehouse_id , warehouse_id  from oc_x_deliver_order where deliver_order_id = '".$order_id."'";

        $query = $db->query($sql);
        $stationInfo = $query->row;




        //获取订单的所有商品
        $sql = "SELECT
            op. deliver_order_product_id, op.order_id, op.product_id, op.weight_inv_flag, op.name,  op.quantity, op.price, op.total,  op.is_gift,  op.status,
            ";
//        $sql.="lpp.barcode,";
            $sql .= "p.repack,p.station_id,p.inv_class,ptw.sku_barcode sku,ptw.stock_area inv_class_sort,p.storage_mode_id,ptc.category_id,p.product_type,p.product_type_id , ''sku1
        FROM
            oc_x_deliver_order_product AS op
        LEFT JOIN oc_product AS p ON p.product_id = op.product_id
        left join oc_product_to_category as ptc on p.product_id = ptc.product_id
        ";
//            $sql .=" left join labelprinter.productlist as lpp on lpp.product_id = op.product_id ";
            $sql .= "
        LEFT JOIN oc_product_to_warehouse  ptw ON  op.product_id = ptw.product_id
        WHERE
            op.deliver_order_id = " . $order_id . "
             and ptw.warehouse_id = '" . $stationInfo['warehouse_id'] . "' and ptw.do_warehouse_id = '" . $stationInfo['do_warehouse_id'] . "'
        ORDER BY
            p.inv_class ASC,
                p.inv_class_sort ASC,
            op.product_id ASC";

        //TODO 快消品重新排序
        if($stationInfo['station_id'] == 2){
            $sql = "SELECT
                        op.deliver_order_product_id, op.order_id, op.product_id, op.weight_inv_flag, op.name,  sum(op.quantity) quantity , op.price, op.total, op.is_gift,  op.status,
                        '' barcode,p.repack,p.station_id,p.is_repack,p.inv_class,ptw.sku_barcode sku, ptw.stock_area inv_class_sort, p.storage_mode_id,ptc.category_id,p.product_type,p.product_type_id,group_concat(psb.sku_barcode) sku1,
                        left(ptw.stock_area, 3) shortsort
                    FROM
                        oc_x_deliver_order_product AS op
                    LEFT JOIN oc_product AS p ON p.product_id = op.product_id
                    left join oc_product_to_category as ptc on p.product_id = ptc.product_id
                    LEFT JOIN oc_product_to_warehouse ptw ON op.product_id = ptw.product_id
                    LEFT JOIN oc_product_sku_barcode psb ON  op.product_id = psb.product_id and psb.warehouse_id = '". $data['warehouse_id']."'
                    
                  
                     ";

            if($frame_num > 0 ){
                $sql .= " left join oc_x_inventory_order_sorting ios on ios.deliver_order_id = op.deliver_order_id and ios.product_id = op.product_id and ios.status =1  ";
            }
            $sql .= "   WHERE
                        op.deliver_order_id = " . $order_id . "
                        and ptw.warehouse_id = '". $data['warehouse_id']."' ";
            if($frame_num > 0 ){
                $sql .= "  and ios.container_id = '".$frame_num ."'";
            }

            if($data['warehouse_id'] == 12){
                $sql  .= " group by op.product_id
                    ORDER BY field(shortsort, 'A02','A03','A26','A27','A04','A05','A28','A29','A06','A07','A30','A31','A08','A09','A32','A33','A10','A11','A34','A35','A12','A13','A36','A37','A14','A15','A38','A39','A16','A17','A40','A41','A18','A19','A42','A43','A20','A21','A44','A45','A22','A23','A46','A47','A24','A25','A48','A49','A50','A51','A52','A53','A54','A55','A56','A57','A58'), ptw.stock_area asc
                    ";
            }
            else if($data['warehouse_id'] == 14){
                $sql  .= " group by op.product_id
                    ORDER BY ptw.stock_area_sort asc, ptw.stock_area asc
                    ";
            }
            else{
                $sql  .= "     group by op.product_id
                    ORDER BY
                        ptw.stock_area asc,
                        ptw.stock_area_sort asc";
            }
        }


        $query = $db->query($sql);

        $results = $query->rows;
        if (!sizeof($results)) {
            return false;
        }

        $return = array();
        $return['data'] = array();

        foreach ($results as $k => $v) {
            $return['data'][$v['product_id']] = $v;
            $return['data'][$v['product_id']]['plan_quantity'] = $v['quantity'];
            $return['data'][$v['product_id']]['category_id'] = $v['category_id'];
            $return['data'][$v['product_id']]['repack'] = $v['repack'];
            $return['data'][$v['product_id']]['station_id'] = $v['station_id'];
            $return['data'][$v['product_id']]['boxCount'] = 0;
            $return['data'][$v['product_id']]['product_barcode_arr'] = array();
        }


        //获取入库中间表中已入库的商品，并从计划入库的商品中减去已入库的商品

        $sql = "SELECT xis.* FROM oc_x_inventory_order_sorting AS xis  left join oc_product p on p.product_id = xis.product_id where  xis.status = 1 and xis.deliver_order_id = '" . $order_id . "'   ";

        if($frame_num > 0){
            $sql .= " and xis.container_id = '".$frame_num."'";
        }
//        return $sql;
        $query = $db->query($sql);
        $result = $query->rows;

        //HARD CODE!!!
        $bomProduct = array(
            '5661'=>6,
            '5662'=>3,
            '5663'=>3,
            '5664'=>6,
            '5665'=>2,
            '5797'=>2,
            '5798'=>3,
            '5799'=>2,
            '5800'=>1,

            '6751'=>3,
            '6753'=>3
        );

        if (sizeof($result)) {
            foreach ($result as $rk => $rv) {
                $return_move_p = array();
                $return['data'][$rv['product_id']]['quantity'] -= $rv['quantity'];

                if($return['data'][$rv['product_id']]['repack']){
                    $return['data'][$rv['product_id']]['boxCount'] = 0;
                }
                else{
                    //HARD CODE!!!
                    if(array_key_exists($rv['product_id'],$bomProduct)){
                        $return['data'][$rv['product_id']]['boxCount'] += $rv['quantity']*$bomProduct[$rv['product_id']];
                    }
                    else{
                        $return['data'][$rv['product_id']]['boxCount'] += $rv['quantity'];
                    }
                }

                if (empty($return['data'][$rv['product_id']]['product_barcode_arr'])) {
                    $return['data'][$rv['product_id']]['product_barcode_arr'] = json_decode($rv['product_barcode']);
                } else {
                    $return['data'][$rv['product_id']]['product_barcode_arr'] = array_merge($return['data'][$rv['product_id']]['product_barcode_arr'], json_decode($rv['product_barcode']));
                }

                if ($return['data'][$rv['product_id']]['quantity'] <= 0) {
                    $return_move_p = $return['data'][$rv['product_id']];
                    unset($return['data'][$rv['product_id']]);
                    $return['data'][$rv['product_id'] * 1000000] = $return_move_p;
                }
            }
        }


        //TODO 限制仅生鲜仓排序
        if($stationInfo['station_id'] == 1){
            foreach ($return['data'] as $rdk => $rdv) {
                $return_move_i = array();
                if ($rdk < 1000000) {
                    $return_move_i = $return['data'][$rdv['product_id']];
                    unset($return['data'][$rdv['product_id']]);

                    $inv_class_num = (int) $this->letter_to_num(substr($rdv['inv_class_sort'], 0, 1));
                    $product_sort_num = 0;
                    if ($inv_class_num > 0) {
                        $rdv['inv_class_sort'] = str_replace('-', '', $rdv['inv_class_sort']);
                        $rdv['inv_class_sort'] = str_pad($rdv['inv_class_sort'],7,"0",STR_PAD_RIGHT);
                        $product_sort_num = str_replace(substr($rdv['inv_class_sort'], 0, 1), $inv_class_num, $rdv['inv_class_sort']);

                    }

                    if ($rdv['product_id'] < 5000) {
                        $return['data'][$rdv['product_id'] + $product_sort_num * 10] = $return_move_i;
                    } else {
                        $return['data'][$rdv['product_id'] + $product_sort_num * 10] = $return_move_i;
                        //$return['data'][$rdv['product_id'] + $rdv['inv_class'] * 10000000] = $return_move_i;
                    }
                }
            }

            ksort($return['data']);
        }




        $return_arr = array();
        $return_arr['status'] = 1;
        $return_arr['data'] = array();
        //商品根据inv_class分组显示
        /*
          foreach($return['data'] as $k => $v){
          $return_arr['data'][$v['inv_class']]['inv_class_name'] = $v['inv_class_name'];
          $return_arr['data'][$v['inv_class']]['product'][$k]['name'] =  $v['name'];
          $return_arr['data'][$v['inv_class']]['product'][$k]['quantity'] =  $v['quantity'];
          $return_arr['data'][$v['inv_class']]['product'][$k]['plan_quantity'] =  $v['plan_quantity'];
          }
         */
        $return_arr['data'] = $return['data'];

        if($stationInfo['station_id'] == 2){
            $return_arr['data'] = array();
            foreach($return['data'] as $m){
                $return_arr['data'][] = $m;
            }
        }


        if ($data['is_view'] != 1) {

            $sql = "select * from oc_x_deliver_order where deliver_order_id = " . $order_id;
            $query = $db->query($sql);
            $order_status = $query->row;

            if ($order_status['order_status_id'] == 6) {

                return array("status" => 6);
            }

            if($user_group_id == 15){
                $sql = "update oc_x_deliver_order set order_status_id = 5 where deliver_order_id = " . $order_id;
                $dbm->query($sql);

                $sql = "update oc_order set order_status_id = 5 where order_id = '".$orderStatus['order_id']."'" ;
                $dbm->query($sql);
            }



        }


        return $return_arr;
    }
    public function getOrderAreaList($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $db;
        global $dbm;
        global $log;

        $data = json_decode($data, 2);

        //$date = isset($data['date']) ? $data['date'] : false;
        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        $repack = isset($data['repack']) ? $data['repack'] : false;
        $warehouse_transfer_area_id = $data['warehouse_transfer_area_id'];
        $warehouse_id = $data['warehouse_id'];
        $warehouse_repack = isset($data['warehouse_repack']) ? $data['warehouse_repack'] : 0;
        $user_repack = isset($data['user_repack']) ? $data['user_repack'] : 0;
        if (!$order_id) {
            return false;
        }

        $sql = "select  order_status_id from oc_order where order_id = '". $order_id ."'" ;

        $query = $db->query($sql);
        $orderStatus = $query->row;

        if($orderStatus['order_status_id'] == 3){
            return false;
        }

        $sql = "select station_id from oc_order where order_id = '".$order_id."'";
        $query = $db->query($sql);
        $stationInfo = $query->row;


        $return = array();
        $return['data'] = array();



        //获取入库中间表中已入库的商品，并从计划入库的商品中减去已入库的商品

        $sql = "SELECT xis.*,oss.name as areaname,oss.stock_section_id FROM oc_x_inventory_purchase_order_sorting AS xis  
left join oc_product p on p.product_id = xis.product_id 
left join oc_x_stock_section oss on oss.stock_section_id = xis.stock_section_id
where xis.area_status =1 and xis.order_id = '" . $order_id . "' ORDER BY oss.stock_section_id";


        $query = $db->query($sql);
        $result = $query->rows;

        $return['data'] = $result;
        $sql1 = 'SELECT count(stock_section_id) as count ,stock_section_id FROM `oc_x_inventory_purchase_order_sorting`  WHERE area_status = 1 GROUP BY stock_section_id';
        $query1 = $db->query($sql1);
        $count = $query1->rows;
        $warehouse_transfer_area_id = $data['warehouse_transfer_area_id'];
        $warehouse_id = $data['warehouse_id'];
        $sql2 = "SELECT oss.name as areaname,oss.stock_section_id FROM oc_x_stock_section oss  WHERE oss.stock_section_type_id ='".$warehouse_transfer_area_id."' AND oss.warehouse_id = '".$warehouse_id."' GROUP BY oss.stock_section_id";
        $query2 = $db->query($sql2);
        $result2 = $query2->rows;
        $sql3 = "SELECT oss.name as areaname,oss.stock_section_id FROM oc_x_inventory_purchase_order_sorting AS xis  
left join oc_x_stock_section oss on oss.stock_section_id = xis.stock_section_id
where xis.area_status =0 and xis.order_id = '" . $order_id . "'";
        $query3 = $db->query($sql3);
        $result3 = $query3->rows;
        $sql4 = "SELECT oss.name as areaname,oss.stock_section_id FROM oc_x_inventory_purchase_order_sorting AS xis  
left join oc_x_stock_section oss on oss.stock_section_id = xis.stock_section_id
where xis.order_id = '" . $order_id . "' GROUP BY xis.stock_section_id";
        $query4 = $db->query($sql4);
        $result4 = $query4->rows;

        $return_arr = array();
        $return_arr['status'] = 1;
        $return_arr['data'] = array();
        $return_arr['count'] = array();
        $return_arr['areas1'] = array();
        $return_arr['areas2'] = array();
        $return_arr['areas3'] = array();
        //商品根据inv_class分组显示
        /*
          foreach($return['data'] as $k => $v){
          $return_arr['data'][$v['inv_class']]['inv_class_name'] = $v['inv_class_name'];
          $return_arr['data'][$v['inv_class']]['product'][$k]['name'] =  $v['name'];
          $return_arr['data'][$v['inv_class']]['product'][$k]['quantity'] =  $v['quantity'];
          $return_arr['data'][$v['inv_class']]['product'][$k]['plan_quantity'] =  $v['plan_quantity'];
          }
         */
        $return_arr['count'] = $count;
        $return_arr['areas1'] = $result2;
        $return_arr['areas2'] = $result3;
        $return_arr['areas3'] = $result4;
        $return_arr['data'] = $return['data'];

//        if($stationInfo['station_id'] == 2){
//            $return_arr['data'] = array();
//            foreach($return['data'] as $m){
//                $return_arr['data'][] = $m;
//            }
//        }







        return $return_arr;
    }

//每隔五分钟检查订单状态是否取消
    public  function updateOrderStatus($data){
        global $db;

        //$order_id = isset($data['order_id']) ? $data['order_id'] : false;
        $order_id=str_replace('"', '', $data);

        $sql = "select order_status_id from oc_order WHERE  order_id = '". $order_id ."'";
        $query = $db->query($sql);
        $results = $query->row;
        return $results;
    }

    public  function updateStockSectionArea($data, $station_id, $language_id = 2, $origin_id){
        global $dbm;
        global $db;
        global $log;
        $data = json_decode($data, 2);
        $sql1 = "SELECT xis.* FROM oc_x_inventory_purchase_order_sorting AS xis  
where xis.warehouse_id = '".$data['warehouse_id']."' and xis.order_id = '" . $data['order_id'] . "' AND xis.stock_section_id ='".$data['stock_section_ids']."'";
        $query1 = $db->query($sql1);
        $result1 = $query1->rows;
        if ($data['status'] == 0) {
            foreach ($result1 as $value) {
                $sql = "UPDATE oc_x_inventory_purchase_order_sorting SET area_status = 1 WHERE warehouse_id = '".$data['warehouse_id']."' AND order_id = '" . $data['order_id']."' AND product_id = '".$value['product_id']."'";
//                return $sql;
                $dbm->query($sql);
            }
        } else if ($data['status'] == 1) {
            foreach ($result1 as $value) {
                $sql = "UPDATE oc_x_inventory_purchase_order_sorting SET area_status = 0 WHERE warehouse_id = '".$data['warehouse_id']."' AND order_id = '" . $data['order_id']."' AND product_id = '".$value['product_id']."'";
                $dbm->query($sql);
            }
        } else if ($data['status'] == 2) {
            $sql = "UPDATE oc_x_inventory_purchase_order_sorting SET stock_section_id = '".$data['stock_section_id']."' WHERE warehouse_id = '".$data['warehouse_id']."' AND order_id = '" . $data['order_id']."' AND product_id = '".$data['product_id']."'";
//            return $sql;
            $dbm->query($sql);
        }

        return array('status' => 1);
    }

    public function getWarehouseTransferArea($data, $station_id, $language_id = 2, $origin_id){
        global $db;
        global $dbm;
        global $log;
        $data = json_decode($data, 2);
        //区域详情
        $sql2s = "SELECT * FROM oc_x_stock_section where stock_section_type_id ='".$data['warehouse_transfer_area_id']."' and warehouse_id = '".$data['warehouse_id']."'";
        $query2s = $db->query($sql2s);
        $transfer_item = $query2s->rows;
        return $transfer_item;
    }

    public function getPurchaseOrderSortingList($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $db;
        global $dbm;
        global $log;

        $data = json_decode($data, 2);

        //$date = isset($data['date']) ? $data['date'] : false;
        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        $warehouse_id = isset($data['warehouse_id']) ? $data['warehouse_id'] : false;
        if (!$order_id) {
            return false;
        }





//获取订单的所有商品
        $sql = "SELECT
	op.*,lpp.barcode,p.inv_class,ptw.sku_barcode sku,ptw.stock_area inv_class_sort,p.storage_mode_id,ptc.category_id,p.product_type
FROM
	oc_x_pre_purchase_order_product AS op
LEFT JOIN oc_product AS p ON p.product_id = op.product_id
LEFT JOIN oc_product_to_warehouse ptw ON op.product_id = ptw.product_id  and ptw.warehouse_id = '".$warehouse_id."'

left join oc_product_to_category as ptc on p.product_id = ptc.product_id
left join labelprinter.productlist as lpp on lpp.product_id = op.product_id
WHERE
	op.purchase_order_id = " . $order_id . "
ORDER BY
	p.inv_class ASC,
	op.product_id ASC";
//return $sql;
        $query = $db->query($sql);

        $results = $query->rows;
        if (!sizeof($results)) {
            return false;
        }

        $return = array();
        $return['data'] = array();

        foreach ($results as $k => $v) {
            $return['data'][$v['product_id']] = $v;
            $return['data'][$v['product_id']]['plan_quantity'] = $v['quantity'];
            $return['data'][$v['product_id']]['category_id'] = $v['category_id'];
        }



        //获取入库中间表中已入库的商品，并从计划入库的商品中减去已入库的商品

        $sql = "SELECT xis.*,ss.name as areaname ,ss.stock_section_id FROM oc_x_inventory_purchase_order_sorting AS xis left join oc_x_stock_section ss on ss.stock_section_id = xis.stock_section_id where xis.order_id = '" . $order_id . "' order by xis.stock_section_id";
//return $sql;
        $query = $db->query($sql);
        $result = $query->rows;
        if (sizeof($result)) {
            foreach ($result as $rk => $rv) {
                $return_move_p = array();
                $return['data'][$rv['product_id']]['quantity'] -= $rv['quantity'];
                $return['data'][$rv['product_id']]['areaname'] = $rv['areaname'];
                $return['data'][$rv['product_id']]['areaid'] = $rv['stock_section_id'];
                if (empty($return['data'][$rv['product_id']]['product_barcode_arr'])) {
                    $return['data'][$rv['product_id']]['product_barcode_arr'] = json_decode($rv['product_barcode']);
                } else {
                    $return['data'][$rv['product_id']]['product_barcode_arr'] = array_merge($return['data'][$rv['product_id']]['product_barcode_arr'], json_decode($rv['product_barcode']));
                }
                if ($return['data'][$rv['product_id']]['quantity'] <= 0) {
                    $return_move_p = $return['data'][$rv['product_id']];
                    unset($return['data'][$rv['product_id']]);
                    $return['data'][$rv['product_id'] * 1000000] = $return_move_p;
                }
            }
        }




        $return_arr = array();
        $return_arr['status'] = 1;
        $return_arr['data'] = array();



        //区域分类
        $sql1s = "select * from oc_x_product_section_type order by station_section_type_id";
        $query1s = $db->query($sql1s);
        $transfer = $query1s->rows;

        //商品根据inv_class分组显示
        /*
          foreach($return['data'] as $k => $v){
          $return_arr['data'][$v['inv_class']]['inv_class_name'] = $v['inv_class_name'];
          $return_arr['data'][$v['inv_class']]['product'][$k]['name'] =  $v['name'];
          $return_arr['data'][$v['inv_class']]['product'][$k]['quantity'] =  $v['quantity'];
          $return_arr['data'][$v['inv_class']]['product'][$k]['plan_quantity'] =  $v['plan_quantity'];
          }
         */
        $return_arr['data'] = $return['data'];
        $return_arr['transfer'] = $transfer;



        return $return_arr;
    }


    function letter_to_num($letter) {

        $letter_to_num = array(
            "A" => 1,
            "B" => 2,
            "C" => 3,
            "D" => 4,
            "E" => 5,
            "F" => 6,
            "G" => 7,
            "H" => 8,
            "I" => 9,
            "J" => 10,
            "K" => 11,
            "L" => 12,
            "M" => 13,
            "N" => 14,
            "O" => 15,
            "P" => 16,
            "Q" => 17,
            "R" => 18,
            "S" => 19,
            "T" => 20,
            "U" => 21,
            "V" => 22,
            "W" => 23,
            "X" => 24,
            "Y" => 25,
            "Z" => 26
        );
        if (isset($letter_to_num[$letter])) {
            return $letter_to_num[$letter];
        } else {
            return 0;
        }
    }

    public function getStationProductInfo($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;

        //Expect Data: $data= '{"products":"150628001085001050,150628001053001250"}';

        $data_inv = json_decode($data, 2);
        if (!isset($data_inv['products']) || !$data_inv['products']) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . $data . "\n\r");
            return false;
        }

        $products = explode(',', $data_inv['products']);
        $product_info = array();
        foreach ($products as $product) {
            //$product_id = (int)substr($product, 6, 6);
            //$barcode_info[$product_id] = array(
            //    'due_data' => date("Y-m-d",strtotime('20'.substr($product, 0, 6))),
            //    'retail_price' => round((int)substr($product, 12, 6)/100,2)
            //);
            //$product_ids[] = $product_id;
            //Barcode rules for Code128(18) OR Ean13(13||12)
            //18: 6+6+6
            //12: 1+5+5+x
            //13: 2+5+5+x
            if (strlen($product) == 18) {
                $product_id = (int) substr($product, 6, 6);
                $barcode_info[$product_id] = array(
                    'due_date' => date("Y-m-d", strtotime('20' . substr($product, 0, 6))), //There is a bug till year 2099.
                    'retail_price' => round((int) substr($product, 12, 6) / 100, 2)
                );
            } elseif (strlen($product) == 12 || strlen($product) == 13) {
                $product_id = (int) substr($product, 1 - (12 - strlen($product)), 5);
                $due_date = '2015-07-30';

                $barcode_info[$product_id] = array(
                    'due_date' => $due_date,
                    'retail_price' => round((int) substr($product, 1 - (12 - strlen($product)) + 5, 5) / 100, 2)
                );
            } else {
                $product_id = 0;
            }

            $product_ids[] = $product_id;
        }

        $sql = "select
            A.product_id,
            B.name,
            PC.print_name inv_class,
            round(A.price,2) ori_price,
            round(if(isnull(D.price),A.price,D.price),2 ) sale_price,
            round(E.price,2) station_promo_price,
            if(A.weight_class_id=1, concat( round( if(isnull(D.price),A.price,D.price)/(A.weight/500), 2 ) , '/斤'), NULL) sale_price_500g,
            if(A.weight_class_id=1, concat(round(E.price/(A.weight/500),2) , '/斤'), NULL ) station_promo_price_500g,
            concat(round(A.weight,0), C.title) unit,
            A.shelf_life,
            now() checktime
            from xsj.oc_product A
            left join xsj.oc_product_description B on A.product_id = B.product_id and B.language_id = 2
            left join xsj.oc_weight_class_description C on A.weight_class_id = C.weight_class_id and C.language_id = 2
            left join  xsj.oc_product_special D on (A.product_id = D.product_id and now() between D.date_start and D.date_end)
            left join xsj.oc_product_promo E on A.product_id = E.product_id
            left join xsj.oc_product_inv_class PC on A.inv_class = PC.product_inv_class_id
            where A.product_id in (" . implode(',', $product_ids) . ")";

        $query = $db->query($sql);
        $results = $query->rows;

        if (sizeof($results)) {
            foreach ($results as $m) {
                $product_info[$m['product_id']] = $m;
                $product_info[$m['product_id']]['due_date'] = $barcode_info[$m['product_id']]['due_date'];
                $product_info[$m['product_id']]['retail_price'] = $barcode_info[$m['product_id']]['retail_price'];
            }

            $products = array();
            foreach ($product_info as $m) {
                $products[] = $m;
            }

            return $products;
        } else {
            return array();
        }
    }

    public function getStationProductInfob2b($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $log;
        return '12345';
        //Expect Data: $data= '{"products":"150628001085001050,150628001053001250"}';

        $data_inv = json_decode($data, 2);
        if (!isset($data_inv['products']) || !$data_inv['products']) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 参数错误' . $data . "\n\r");
            return false;
        }
        $products = explode(',', $data_inv['products']);
        $product_info = array();
        foreach ($products as $product) {
            //$product_id = (int)substr($product, 6, 6);
            //$barcode_info[$product_id] = array(
            //    'due_data' => date("Y-m-d",strtotime('20'.substr($product, 0, 6))),
            //    'retail_price' => round((int)substr($product, 12, 6)/100,2)
            //);
            //$product_ids[] = $product_id;
            //Barcode rules for Code128(18) OR Ean13(13||12)
            //18: 6+6+6
            //12: 1+5+5+x
            //13: 2+5+5+x
            if (strlen($product) == 18) {
                $product_id = (int) substr($product, 6, 6);
                $barcode_info[$product_id] = array(
                    'due_date' => date("Y-m-d", strtotime('20' . substr($product, 0, 6))), //There is a bug till year 2099.
                    'retail_price' => round((int) substr($product, 12, 6) / 100, 2)
                );
            } elseif (strlen($product) == 12 || strlen($product) == 13) {
                $product_id = (int) substr($product, 1 - (12 - strlen($product)), 5);
                $due_date = '2015-07-30';

                $barcode_info[$product_id] = array(
                    'due_date' => $due_date,
                    'retail_price' => round((int) substr($product, 1 - (12 - strlen($product)) + 5, 5) / 100, 2)
                );
            } elseif (strlen($product) <= 6) {

                $product_id = $product;
            } else {
                $product_id = 0;
            }

            $product_ids[] = $product_id;
        }

        $sql = "select
            A.product_id,
            IF ( A.inv_size IS NULL || A.is_repack = 1  , B.name, concat( B.name,'[',cast(A.inv_size AS signed),']')) AS name,
            B.abstract,
            PC.print_name inv_class,
            round(A.price,2) ori_price,
            round(if(isnull(D.price),A.price,D.price),2 ) sale_price,
            round(E.price,2) station_promo_price,
            if(A.weight_class_id=1, concat( round( if(isnull(D.price),A.price,D.price)/(A.weight/500), 2 ) , '/斤'), NULL) sale_price_500g,
            if(A.weight_class_id=1, concat(round(E.price/(A.weight/500),2) , '/斤'), NULL ) station_promo_price_500g,
            concat(round(A.weight,0), C.title) unit,
            A.shelf_life,
            now() checktime
            from oc_product A
            left join oc_product_description B on A.product_id = B.product_id and B.language_id = 2
            left join oc_weight_class_description C on A.weight_class_id = C.weight_class_id and C.language_id = 2
            left join oc_product_special D on (A.product_id = D.product_id and now() between D.date_start and D.date_end)
            left join oc_product_promo E on A.product_id = E.product_id
            left join oc_product_inv_class PC on A.inv_class = PC.product_inv_class_id
            where A.product_id in (" . implode(',', $product_ids) . ")";

        $query = $db->query($sql);
        $results = $query->rows;

        if (sizeof($results)) {
            foreach ($results as $m) {
                $product_info[$m['product_id']] = $m;
                $product_info[$m['product_id']]['due_date'] = $barcode_info[$m['product_id']]['due_date'];
                $product_info[$m['product_id']]['retail_price'] = $barcode_info[$m['product_id']]['retail_price'];
            }

            $products = array();
            foreach ($product_info as $m) {
                $products[] = $m;
            }

            return $products;
        } else {
            return array();
        }
    }

    public function addOrderProductStation($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;
        global $db;

        $data = json_decode($data, 2);

        $date = isset($data['date']) ? $data['date'] : false;
//        return $data;
        $product_id = isset($data['product_id']) ? $data['product_id'] : false;
        $container_id = isset($data['container_id']) ? $data['container_id'] : false;
        $warehouse_repack = isset($data['warehouse_repack']) ? $data['warehouse_repack'] : false;
        $user_repack = isset($data['user_repack']) ? $data['user_repack'] : false;
        $frame_vg_list  = isset($data['frame_vg_list']) ? $data['frame_vg_list'] : false;
        $frame_count  = isset($data['frame_count']) ? $data['frame_count'] : false;
        if (!$product_id) {
            return false;
        }
//        return $container_id;
        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
//        return $order_id;
        if (!$order_id) {
            return false;
        }



//        return 1234567;
        $orderStatus =  $this->getDeliverOrderInfo($order_id);
//        return $orderStatus;
        if($orderStatus['order_status_id'] == 3){
            return false;
        }
        //查分拣明细商品的数量
        $sql = " select quantity   from  oc_x_deliver_order_product    where deliver_order_id = '". $order_id ."' and  product_id = '".$product_id ."'  ";

        $query = $db->query($sql);
        $order_quantity  = $query->row;

        //查分拣中间表的数量
        $sql = " select quantity   from  oc_x_inventory_order_sorting    where deliver_order_id = '". $order_id ."' and  product_id = '".$product_id ."' and status = 1   ";
//        return $sql;
        $query = $db->query($sql);
        $sorting_quantity  = $query->row;
//        return $sorting_quantity;
        //数量对比
//        if($order_quantity['quantity'] <= isset($sorting_quantity['quantity'])?$sorting_quantity['quantity']:'0' ){
//            $return['memo']  =  '入库数量不能大于订单数量';
//            return $return;
//        }else {

            //默认返回状态0，成功返回状态1
            $return['status'] = 0;
            //查商品表的整/散
            $sql_product_repack = " select repack  from oc_product where product_id = '". $product_id ."'";
//            return $sql_product_repack;
            $query = $db->query($sql_product_repack);
            $result_product_repack  = $query->row;
            /*if($result_product_repack['repack'] == 0 ){
                $container_id = 0 ;
            }*/
            //查分拣中间表的数量
            $sql_inventory_product_id = "select quantity ,product_id,container_id   from oc_x_inventory_order_sorting where deliver_order_id = '".$order_id ."' and product_id = '".$product_id ."' and status = 1 group by  order_id , product_id   ";
//            return $sql_inventory_product_id;

            $query  = $dbm->query($sql_inventory_product_id);
            $result_inventory_product  = $query->row;

            isset($result_inventory_product)?$result_inventory_product:'0';
            /*
             * 分拣数量+已分拣数量<=商品数量
             * */
            if($result_inventory_product['quantity'] > 0){
                $sorting_quantity =  $result_inventory_product['quantity'] + $data['product_quantity'];
                $sql = " update   oc_x_inventory_order_sorting set  quantity = '".$sorting_quantity ."'  where deliver_order_id = '".$order_id ."' and product_id = '".$product_id ."' and status = 1 ";
                $sql = "insert into oc_x_inventory_order_sorting (order_id , product_id,deliver_order_id,quantity,uptime,added_by,container_id) "
                    . "values ( '".$orderStatus['order_id']."' ," . $product_id . "," . $order_id . "," . $data['product_quantity'] . ",now(),'" . $data['inventory_user'] . "', '".$container_id."')";
//                return $sql;
                $result = $dbm->query($sql);
//                return 'update';
            }else{
                $sql = "insert into oc_x_inventory_order_sorting (order_id , product_id,deliver_order_id,quantity,uptime,added_by,container_id) "
                    . "values ( '".$orderStatus['order_id']."' ," . $product_id . "," . $order_id . "," . $data['product_quantity'] . ",now(),'" . $data['inventory_user'] . "', '".$container_id."')";
//                return $sql;
                $result = $dbm->query($sql);
//                return 'insert';
            }
//            return '3456';

            //判断表中数据是否存在
            $sql_order_return_quantity = " select  inventory_sorting_id , sorting_quantity ,order_quantity  from  oc_x_inventory_order_return_quantity  where deliver_order_id = '".$order_id ."' and product_id = '".$product_id ."' and status = 1 ";

            $query  = $dbm->query($sql_order_return_quantity);
            $result_order_return_quantity = $query->row;



            if($result_product_repack['repack'] == 1){
                $sql = "select deliver_order_id  order_id  from oc_x_deliver_order_inv where deliver_order_id = '".$order_id."'";
                $query = $dbm->query($sql);


                if($query->num_rows) {
                    $sql = "update oc_x_deliver_order_inv set  date_modified = now(),  frame_vg_list= '".$frame_vg_list."' ,frame_count= '".$frame_count."' where deliver_order_id = '".$order_id."'";



                }
                else{
                    $sql="insert into oc_x_deliver_order_inv(deliver_order_id, frame_vg_list, inv_status, uptime, order_id ,frame_count) values ('".$order_id."','".$frame_vg_list."',1 , now() ,'".$orderStatus['order_id']."', '".$frame_count."' )";


                }

                $log->write('[分拣]记录或更新货位号[' . __FUNCTION__ . ']'.$sql."\n\r");
                $dbm->query($sql);


            }

            //更改 oc_x_stock_section_product 中的货位库存
            $sql_warehouse = " select quantity , product_id  , stock_section_id  from oc_x_stock_section_product  WHERE  warehouse_id = '".$data['warehouse_id']."' and product_id = '".$product_id ."' and stock_section_type_id = 1  ";


            $query = $dbm->query($sql_warehouse);
            $result_warehouse = $query->row;


            if($result_warehouse['product_id'] >= 0 ){


                $real_quantity =$result_warehouse['quantity'] - $data['product_quantity'];
                if($real_quantity <0 ){
                    $real_quantity =0;
                }

                $sql_change = "  update   oc_x_stock_section_product set  quantity = '" .$real_quantity ."' WHERE  warehouse_id = '".$data['warehouse_id']."' and product_id = '".$product_id ."'  and stock_section_type_id  = 1  and stock_section_id = '".$result_warehouse['stock_section_id']."'  ";


                $query = $dbm->query($sql_change);
                $result_change = $query->row;


                $sql = "insert into oc_x_stock_section_product_move ( `stock_section_id` , `section_move_type_id` , `product_id` , `quantity` , `date_added` , `added_name` , `order_id` , `warehouse_id` ) VALUES  ('".$result_warehouse['stock_section_id']."' , '2' ,'".$product_id ."' ,  '".$data['product_quantity']*(-1)."' ,NOW() ,  '".$data['inventory_user']."' , '".$order_id ."' , '". $data['warehouse_id'] ."' )";

                $query = $dbm->query($sql);

            }




            //$log->write($sql."\n\r");
            if($result){
                $return['status'] = 1;

                //HARD CODE!!!
                $bomProduct = array(
                    '5661'=>6,
                    '5662'=>3,
                    '5663'=>3,
                    '5664'=>6,
                    '5665'=>2,
                    '5797'=>2,
                    '5798'=>3,
                    '5799'=>2,
                    '5800'=>1,

                    '6751'=>3,
                    '6753'=>3
                );

                //添加分拣中间库记录后返回分拣的整箱数量
                //TOOD HardCode
                $sql = "select sum(if(p.product_id in (6751,6753), ios.quantity*3, ios.quantity)) qtyCount, sum(if(p.repack=0,if(p.product_id in (6751,6753), ios.quantity*3, ios.quantity),0)) boxCount, sum(if(p.repack=1,ios.quantity,0)) repackCount
            from oc_x_inventory_order_sorting ios
            left join oc_product p on ios.product_id = p.product_id
            where ios.deliver_order_id = '".$order_id."' and ios.status = 1 ";
                $query = $dbm->query($sql);
                $result = $query->row;

                $return['qtyCount'] = $result['qtyCount'];
                $return['boxCount'] = $result['boxCount'];
                $return['repackCount'] = $result['repackCount'];
                $sql_all="SELECT SUM(quantity ) as allNumber FROM oc_x_inventory_order_sorting WHERE  product_id = $product_id  AND deliver_order_id = $order_id";
                $query = $dbm->query($sql_all);
                $resultAll = $query->row;
                $return['allNumber'] = $resultAll['allNumber'];
            }

            return $return;
//        }
    }


    public function addPurchaseOrderProductStation($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;

        $data = json_decode($data, 2);

        //$date = isset($data['date']) ? $data['date'] : false;
        $product_id = isset($data['product_id']) ? $data['product_id'] : false;
        $transfer_area = isset($data['transfer_area']) ? $data['transfer_area'] : false;
        $transfer_area_item = isset($data['transfer_area_item']) ? $data['transfer_area_item'] : false;
        if (!$product_id) {
            return false;
        }

        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        if (!$order_id) {
            return false;
        }

        $sql_type = "select order_type  from oc_x_pre_purchase_order  where purchase_order_id = '".$order_id ."' ";

        $query = $dbm->query($sql_type);
        $order_type  = $query->row;


        /*
          //判断今天是否已经提交过了
          $sql = "select count(xim.inventory_move_id) as count_move_t from oc_x_inventory_move as xim left join oc_x_station_group as sg on sg.station_id = xim.station_id where date(xim.date_added) = date(now()) and sg.name = '" . $data['inventory_group'] . "'";
          $query = $dbm->query($sql);
          $result_move_flag = $query->row;

          //已经提交过了
          if($result_move_flag['count_move_t'] > 0){
          $return['status'] = 0;
          return $return;
          }
         */
        $sql = " select quantity   from  oc_x_pre_purchase_order_product    where purchase_order_id = '". $order_id ."' and  product_id = '".$product_id ."'  ";

        $query = $dbm->query($sql);
        $order_quantity  = $query->row;


        $sql = " select quantity   from  oc_x_inventory_purchase_order_sorting    where order_id = '". $order_id ."' and  product_id = '".$product_id ."'  ";
        $query = $dbm->query($sql);
        $sorting_quantity  = $query->row;


        if($order_type['order_type'] == 2 ){
            $sql = "SELECT
	ppo.purchase_order_id,
	ppo.related_order,
	sm.purchase_order_id,
	if(smi.product_id is null , 0 , smi.product_id) product_id , 
	if(smi.quantity is null , 0 , smi.quantity) quantity 
FROM
	oc_x_pre_purchase_order ppo
LEFT JOIN oc_x_stock_move sm ON ppo.related_order = sm.purchase_order_id
LEFT JOIN oc_x_stock_move_item smi ON sm.inventory_move_id = smi.inventory_move_id
WHERE
	ppo.purchase_order_id = '".$order_id ."' and smi.product_id = '".$product_id."'";

            $query = $dbm->query($sql);
            $result  = $query->row;

            if($result['product_id'] ==0 || $result['product_id'] == '' ){
                $return['memo'] = '此商品没有入库不能做退货操作';
                return $return;
            }
        }



//        if($order_quantity['quantity'] < $sorting_quantity['quantity']){
//            $return['memo'] = "入库数量不能大于订单数量";
//            return $return;
//
//        }else {
            $sql = 'SELECT * FROM oc_x_inventory_purchase_order_sorting WHERE order_id = "'.$order_id.'" and warehouse_id = "'.$data['warehouse_id'].'" AND stock_section_type_id = "'.$data['transfer_area'].'" AND product_id = "'.$product_id.'"';
//           return $sql;
            $query = $dbm->query($sql);
            $result = $query->rows;
//            return $result;
            if ($result) {
                if ($result[0]['area_status'] == 1) {
                    $return['status'] = 2;
                    return $return;
                } else {
                    $quantity = $data['product_quantity']+$result[0]['quantity'];
                    $sql_pur = "select purchase_order_id  from  oc_x_stock_move where purchase_order_id = '".$order_id ."'  ";
                    $query = $dbm->query($sql_pur);
                    $result_pur  = $query->row;
                    if($result_pur['purchase_order_id'] > 0 ){
                        $return['memo'] = "已提交入库不能再提交";
                        return $return;
                    }else {
                        $sql1 = 'UPDATE oc_x_inventory_purchase_order_sorting SET quantity = "' . $quantity . '" , stock_section_id = "' . $data['transfer_area_item'] . '" , uptime = now() WHERE inventory_sorting_id = "' . $result[0]['inventory_sorting_id'] . '"';
                        $dbm->query($sql1);
                    }
                }
            } else {

                $sql_pur = "select purchase_order_id  from  oc_x_stock_move where purchase_order_id = '".$order_id ."'  ";
                $query = $dbm->query($sql_pur);
                $result_pur  = $query->row;

                if($result_pur['purchase_order_id'] > 0 ){
                    $return['memo'] = "已提交入库不能再提交";
                    return $return;
                }else{
                    $sql1 = "insert into oc_x_inventory_purchase_order_sorting (product_id,order_id,quantity,uptime,added_by,product_barcode,warehouse_id,stock_section_type_id,stock_section_id) "
                        . "values ('" . $product_id . "','" . $order_id . "'," . $data['product_quantity'] . ",now(),'" . $data['inventory_user'] . "','" . json_encode($data['product_barcode_arr']) . "','" . $data['warehouse_id'] ."','".$data['transfer_area']."','".$data['transfer_area_item']."')";

                    $dbm->query($sql1);
                }


            }


            //$log->write($sql."\n\r");

            $return['status'] = 1;
            return $return;
        }
//    }

    public function addOrderProductToInv_pre($data, $station_id, $language_id = 2, $origin_id) {
        global $db;

        $data = json_decode($data, 2);
        $warehouse_repack = isset($data['warehouse_repack']) ? $data['warehouse_repack'] : 0;
        $user_repack = isset($data['user_repack']) ? $data['user_repack'] : 0;
        $warehouse_id = isset($data['warehouse_id']) ? trim($data['warehouse_id']) : 0;
        $go_warehouse_id = isset($data['go_warehouse_id']) ? trim($data['go_warehouse_id']) : 0;
        $sql =  "select order_status_id , order_id   from oc_x_deliver_order WHERE  deliver_order_id = '".$data['order_id'] ."'";
        $query = $db->query($sql);
        $result = $query->row;

        if($result['order_status_id'] == 3){
            $return['status'] = 2;
            return $return;
        }


        $sql_move = " select order_id  from  oc_x_stock_move where  order_id = '".$result['order_id']."'  ";

        $query = $db->query($sql_move);
        $result_move = $query->row;
        if($result_move['order_id'] == $result['order_id']){
            $return['status'] = 3;
            return $return ;
        }

        //判断中间库中的商品数量是否满足95% 如果不满足则不能提交,浦西仓只检查是否散件数量正确
        if (!empty($go_warehouse_id) && $warehouse_id == 12 && $go_warehouse_id == 14) {
            $sql = "SELECT sum(xis.quantity) as quantity FROM oc_x_inventory_order_sorting AS xis LEFT JOIN oc_product op ON op.product_id = xis.product_id where xis.move_flag = 0 and xis.deliver_order_id = '" . $data['order_id'] . "' and xis.status = 1 AND op.repack = 1 ";
        } else {
            $sql = "SELECT sum(quantity) as quantity FROM oc_x_inventory_order_sorting AS xis where xis.move_flag = 0 and xis.deliver_order_id = '" . $data['order_id'] . "' and xis.status = 1  ";
        }
        $query = $db->query($sql);
        $result = $query->row;

        if (!empty($go_warehouse_id) && $warehouse_id == 12 && $go_warehouse_id == 14) {
            $sql = "SELECT
                    o.deliver_order_id order_id,os.`name`,SUM(op.quantity) as quantity
                FROM
                    oc_x_deliver_order_product AS op
                LEFT JOIN oc_product AS opp ON opp.product_id = op.product_id
                LEFT JOIN oc_x_deliver_order AS o ON o.deliver_order_id = op.deliver_order_id
                LEFT JOIN oc_x_deliver_order_status AS os ON os.order_status_id = o.order_status_id
               
                WHERE op.deliver_order_id = '" . $data['order_id'] . "' AND opp.repack = 1
                group by op.deliver_order_id  ";
        } else {
            $sql = "SELECT
                    o.deliver_order_id order_id,os.`name`,SUM(op.quantity) as quantity
                FROM
                    oc_x_deliver_order_product AS op
                LEFT JOIN oc_x_deliver_order AS o ON o.deliver_order_id = op.deliver_order_id
                LEFT JOIN oc_x_deliver_order_status AS os ON os.order_status_id = o.order_status_id
               
                WHERE op.deliver_order_id = '" . $data['order_id'] . "'
                group by op.deliver_order_id  ";
        }


        $query = $db->query($sql);
        $result_plan = $query->row;


        $return = array();


        $return['status'] = 1;
        $return['plan_quantity'] = $result_plan['quantity'];
        $return['do_quantity'] = $result['quantity'] ? $result['quantity'] : 0;
        //}

        return $return;
    }


    public function addPurchaseOrderProductToInv_pre($data, $station_id, $language_id = 2, $origin_id) {
        global $db;

        $data = json_decode($data, 2);

        //判断中间库中的商品数量是否满足95% 如果不满足则不能提交
        $sql = "SELECT sum(quantity) as quantity FROM oc_x_inventory_purchase_order_sorting AS xis where xis.move_flag = 0 and xis.order_id = '" . $data['order_id'] . "' ";
        $query = $db->query($sql);
        $result = $query->row;


        $sql = "SELECT
                    o.purchase_order_id,os.`name`,SUM(op.quantity) as quantity
                FROM
                    oc_x_pre_purchase_order_product AS op
                LEFT JOIN oc_x_pre_purchase_order AS o ON o.purchase_order_id = op.purchase_order_id
                LEFT JOIN oc_x_pre_purchase_order_status AS os ON os.order_status_id = o.status

                WHERE
                    op.purchase_order_id = " . $data['order_id'];

        $query = $db->query($sql);
        $result_plan = $query->row;

        /*
          //判断今天是否已经提交过了
          $sql = "select count(xim.inventory_move_id) as count_move_t from oc_x_inventory_move as xim left join oc_x_station_group as sg on sg.station_id = xim.station_id where date(xim.date_added) = date(now()) and sg.name = '" . $data['inventory_group'] . "'";
          $query = $db->query($sql);
          $result_move_flag = $query->row;

          //已经提交过了
          if($result_move_flag['count_move_t'] > 0){
          $return['status'] = 0;
          return $return;
          }
         */
        $return = array();

        //中间表中没有要入库的商品
        /*
        if ($result['quantity'] <= 0) {
            $return['status'] = 2;
            return $return;
        }
         *
         */

        //if($result['sum_quantity'] / $result_plan['sum_quantity'] >= 0.95){
        $return['status'] = 1;
        $return['plan_quantity'] = $result_plan['quantity'];
        $return['do_quantity'] = $result['quantity'] ? $result['quantity'] : 0;
        //}

        return $return;
    }


    public function addOrderProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;

        $no_sorting = false;


        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryOrderIn'; //Up

        $order_id_do = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;

        $warehouse_id = isset($data_inv['warehouse_id']) ? $data_inv['warehouse_id'] : false;

        if (!$order_id_do) {
            return false;
        }

        //TODO 待强制验证货位号
        //在快消仓提交订单时先写入整箱和货位号
        $inv_comment = isset($data_inv['invComment']) ? (int)$data_inv['invComment'] : false;
        $box_count= isset($data_inv['boxCount']) ? (int)$data_inv['boxCount'] : false;

        $sql  = "select order_id  from  oc_x_deliver_order where   deliver_order_id = '". $order_id_do ."' ";
        $query = $dbm->query($sql);
        $result2 = $query->row;
        $order_id =  $result2['order_id'];

        //提交订单为待审核
        $userPendingCheck = isset($data_inv['userPendingCheck']) ? $data_inv['userPendingCheck'] : false;
        if($userPendingCheck){
            $sql = "update oc_order set order_status_id = 8 where order_id = '".$order_id."'";
            $dbm->query($sql);
            return array('status' => 8, 'timestamp' => $sql);
        }



        //验证订单分拣是否已提交
        $sql = "select xsm.order_id,o.station_id from oc_x_stock_move as xsm left join oc_order as o on o.order_id = xsm.order_id where xsm.inventory_type_id = 12 and xsm.order_id = " . $order_id;

        $query = $dbm->query($sql);
        $result_exists = $query->row;

        if($query->num_rows){
            //若订单已有出货库存扣减数据且状态为分拣中或待审核，将状态改为已拣完
            $sql = "update oc_order set order_status_id = 6 where order_id = '".$order_id."' and order_status_id in (5,8)";
            $dbm->query($sql);

            $sql = "update oc_x_deliver_order set order_status_id = 6 where deliver_order_id = '".$order_id_do."' and order_status_id in (5,8)";
            $dbm->query($sql);

            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);
        }


        $sql = "select xis.*,lpp.barcode,op.price,op.weight_inv_flag,p.sku_id from oc_x_inventory_order_sorting as xis left join labelprinter.productlist as lpp on xis.product_id = lpp.product_id left join oc_order_product as op on op.order_id = xis.order_id and op.product_id = xis.product_id left join oc_product as p on p.product_id = xis.product_id where xis.status = 1 and   xis.move_flag=0 and xis.order_id = " . $order_id;

        $query = $dbm->query($sql);
        $result = $query->rows;


        //判断分拣数据不能超过订单数据、分拣数据+已提交数据不能超过订单数据、不能提交有相同条码数据
        if(sizeof($result)){
            $order_inv_product_arr = array();
            $order_product_arr = array();
            $order_stock_product_arr = array();

            $product_barcode_arr = array();

            //提交的分拣数据
            foreach($result as $key=>$value){
                $order_inv_product_arr[$value['product_id']] += $value['quantity'];

                $product_barcode_arr = array_merge($product_barcode_arr,json_decode($value['product_barcode']));
            }

            //不能有重复的条码
            //$count_product_barcode = count($product_barcode_arr);

            //$unique_product_barcode_arr = array_unique($product_barcode_arr);

            //if(count($unique_product_barcode_arr) != $count_product_barcode){
            //    return array('status' => 5, 'timestamp' => '条码数量['.$count_product_barcode.']');
            //}

            //订单数据
            $sql = "select * from oc_order_product where order_id = " . $order_id;

            $query = $dbm->query($sql);
            $result2 = $query->rows;

            foreach($result2 as $k2=>$v2){
                $order_product_arr[$v2['product_id']] = abs($v2['quantity']);
            }

            //分拣数据不能超过订单数据
            foreach($order_inv_product_arr as $ik => $iv){
                if($iv > $order_product_arr[$ik]){
                    return array('status' => 5, 'timestamp' => '商品编号['.$ik.']');
                }
            }
            //已提交数据
            $sql = "SELECT smi.product_id, sum(smi.quantity) AS quantity FROM oc_x_stock_move AS xsm LEFT JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id WHERE xsm.inventory_type_id = 12 AND xsm.order_id = " . $order_id . " GROUP BY smi.product_id";

            $query = $dbm->query($sql);
            $result3 = $query->rows;

            foreach($result3 as $k3=>$v3){
                $order_stock_product_arr[$v3['product_id']] = abs($v3['quantity']);
            }

            //提交数据+已提交数据不能超过订单数据
            foreach($order_product_arr as $ok => $ov){
                if($ov < $order_stock_product_arr[$ok] + $order_inv_product_arr[$ok]){

                    return array('status' => 6, 'timestamp' => $data_inv['timestamp']);
                }
            }

        }



        $stationProductMove = array();
        $update_sorting_id_arr = array();
        if (sizeof($result)) {
            foreach ($result as $k => $v) {
                if (!empty($v['product_barcode'])) {
                    $product_barcode_arr = array();
                    $product_barcode_arr = json_decode($v['product_barcode']);
                    $product_weight = 0;
                    foreach ($product_barcode_arr as $pbk => $pbv) {
                        if ((strlen($pbv) == 18 || strlen($pbv) == 16 ) && $v['weight_inv_flag'] == 1) {
                            $product_weight += (int) substr($pbv, 4, 5);
                        }
                        else{
                            if (strlen($pbv) == 18) {
                                $v['barcode'] = $pbv;
                            }

                        }
                    }
                }

                $stationProductMove[] = array(
                    'product_batch' => $v['barcode'],
                    'due_date' => '0000-00-00', //There is a bug till year 2099.
                    'product_id' => $v['product_id'],
                    'special_price' => $v['price'],
                    'qty' => abs($v['quantity']),
                    'product_weight' => $product_weight,
                    'sku_id' => $v['sku_id']
                    //'qty' => '-'.$v['quantity']
                );


                $update_sorting_id_arr[] = $v['inventory_sorting_id'];
            }

            $data_inv['products'] = $stationProductMove;
        } else {
            $no_sorting = true;
            /*
            $inventory_user_admin = array('randy','alex','leibanban','yangyang');
            if(!in_array($data_inv['add_user_name'], $inventory_user_admin)){
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }
             *
             */
        }


//        $sql = "select xsm.order_id,o.station_id from oc_x_stock_move as xsm left join oc_order as o on o.order_id = xsm.order_id where xsm.inventory_type_id = 12 and xsm.order_id = " . $order_id;
//        $query = $dbm->query($sql);
//        $result_exists = $query->rows;
//
//        if(!empty($result_exists)){
//            if($result_exists[0]['station_id'] != 2){
//            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);
//        }
//        }


        if (sizeof($result) || $no_sorting) {

            $log->write($data_inv);

            $result = $this->addInventoryMoveOrder($data_inv, 1 , $warehouse_id);

            if ($result && !empty($update_sorting_id_arr)) {
                $update_sorting_id_str = implode(",", $update_sorting_id_arr);
//                $sql = "update oc_x_inventory_order_sorting set move_flag = 1 where status = 1 and  inventory_sorting_id in (" . $update_sorting_id_str . ")";
//                $log->write($sql);
//                $query = $dbm->query($sql);
            }
        }





        if ($result || $no_sorting) {

            $sql = "update oc_order set order_status_id = 6 where order_id = " . $order_id;
            $dbm->query($sql);

            $sql = "update oc_x_deliver_order set order_status_id = 6 where deliver_order_id = " . $order_id_do;
            $dbm->query($sql);


            return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }
    }


    public function addFastMoveSortingToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;


        $data_inv = json_decode($data, 2);
        $warehouse_id = $data_inv['warehouse_id'];

        $data_inv['api_method'] = 'inventoryOrderIn'; //Up

        //无订单信息，不可提交DO_单
        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;
        if (!$order_id) {
            return false;
        }

        // 获取订单的仓库信息

        $result_warehouse = $this->getDeliverOrderInfo($order_id);

        // SO_单
        $data_inv['order_id'] = $result_warehouse['order_id'] ;

        $sql_order_status  = "select order_status_id  from oc_order where order_id = '".$data_inv['order_id']."' ";

        $query = $dbm->query($sql_order_status);
        $result_order_status = $query->row;
//        return $result_order_status;
        if($result_order_status['order_status_id']  == 6){
            return false ;
        }
        

//        $sql_order_id = " select  order_id from oc_x_stock_move where order_id = '".$order_id."'";
//        $query = $dbm->query($sql_order_id);
//         $result = $query->row;
//         if($sql_order_id['order_id'] >0 ){
//             return false ;
//         }

        //TODO 待强制验证货位号
        //在快消仓提交订单时先写入整箱和货位号
        $warehouse_repack = isset($data_inv['warehouse_repack']) ? (int)$data_inv['warehouse_repack'] : 0;
        $user_repack = isset($data_inv['user_repack']) ? (int)$data_inv['user_repack'] : 0;


        $inv_comment = isset($data_inv['invComment']) ? (int)$data_inv['invComment'] : false;


        $sql_dan = "select count(order_id) num  from oc_x_deliver_order where order_id =  '". $data_inv['order_id']."' ";


        $query = $dbm->query($sql_dan);
        $result_dan = $query->row;
//        return  $result_dan;




        $box_count= isset($data_inv['boxCount']) ? (int)$data_inv['boxCount'] : 0;
        $frame_count= isset($data_inv['frame_count']) ? (int)$data_inv['frame_count'] : 0;
        $frame_vg_list= isset($data_inv['frame_vg_list']) ? $data_inv['frame_vg_list'] : 0;
        $frame_vg_list_arr = explode(',',$frame_vg_list);


        $warehouse_repack= isset($data_inv['warehouse_repack']) ? $data_inv['warehouse_repack'] : 0;
        $user_repack= isset($data_inv['user_repack']) ? $data_inv['user_repack'] : 0;

        if($frame_vg_list == 0){
            $sql_container_ids = " select GROUP_CONCAT(DISTINCT(container_id)) container_ids  from oc_x_inventory_order_sorting where deliver_order_id = '".$order_id ."' and status = 1  group by deliver_order_id ";


            $query = $dbm->query($sql_container_ids);

            $result_container_ids  = $query->row;

//            var_dump('--------------在这里----------');
//            return $result_container_ids;
        }

        if($result_container_ids['container_ids'] != '' && intval($result_container_ids['container_ids']) != 0){
            $frame_vg_list = $result_container_ids['container_ids'];
            $frame_vg_list_arr = explode(',',$frame_vg_list);
            foreach($frame_vg_list_arr as $k=>$v){
                if($v == '0'){
                    unset($frame_vg_list_arr[$k]);
                }
            }


           $frame_count = sizeof($frame_vg_list_arr);
         
            $frame_vg_list = implode(',',$frame_vg_list_arr);
           
        }

        //默认先记录货位号
        if($inv_comment){
            $sql = "select deliver_order_id  order_id  from oc_x_deliver_order_inv where deliver_order_id = '".$order_id."'";
            $query = $dbm->query($sql);


            if($query->num_rows) {
                $sql = "update oc_x_deliver_order_inv set  date_modified = now(), box_count='".$box_count."', inv_comment='".$inv_comment."', frame_count='".$frame_count."', frame_vg_list= '".$frame_vg_list."' where deliver_order_id = '".$order_id."'";



            }
            else{
                $sql="insert into oc_x_deliver_order_inv(deliver_order_id, box_count, inv_comment, frame_count, frame_vg_list, inv_status, uptime, order_id) values ('".$order_id."','".$box_count."','".$inv_comment."','".$frame_count."','".$frame_vg_list."',1 , now() ,'".$result_warehouse['order_id']."' )";



            }

            $log->write('[分拣]记录或更新货位号[' . __FUNCTION__ . ']'.$sql."\n\r");
            $dbm->query($sql);
        }



        if($frame_vg_list != 0 && !empty($frame_vg_list)){

            $sql_update_container = " update oc_x_container  set  occupy = 1  where container_id in (".$frame_vg_list.") ";

            $query = $dbm->query($sql_update_container);

        }

        $sql1="insert into oc_order_inv_log(order_id, box_count, inv_comment, frame_count, frame_vg_list, inv_status, uptime,added_by) values ('".$result_warehouse['order_id']."','".$box_count."','".$inv_comment."','".$frame_count."','".$frame_vg_list."',1 , now(),'".$user_repack."')";
//        return $sql1;

        $query = $dbm->query($sql1);


        //验证订单分拣是否已提交, inventory_type_id=12为分拣出库，
        //TODO 可能有退货问题
        $sql = "select xsm.order_id from oc_x_stock_move as xsm where xsm.inventory_type_id = 12 and xsm.order_id = '".$data_inv['order_id']."'";
        $query = $dbm->query($sql);
        $query->row;
        if($query->num_rows){
            //若订单已有出货库存扣减数据且状态为分拣中或待审核，将状态改为已拣完
            $sql = "update oc_x_deliver_order set order_status_id = 6 where deliver_order_id = '".$order_id."' and order_status_id in (5,8)";
            if($dbm->query($sql)){
                $log->write('[分拣]已扣减库存更新订单状态为已分拣[' . __FUNCTION__ . ']'.$sql."\n\r");
            }

            $sql = "update oc_x_inventory_order_sorting set move_flag = 1 where move_flag=0 and deliver_order_id = '".$order_id."'  and status = 1 ";
            if($dbm->query($sql)){
                $log->write('[分拣]已扣减库存提交分拣数据[' . __FUNCTION__ . ']'.$sql."\n\r");
            }

            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);
        }





        //提交订单为待审核
        $userPendingCheck = isset($data_inv['userPendingCheck']) ? $data_inv['userPendingCheck'] : false;
        if($userPendingCheck){
            $sql = "update oc_x_deliver_order set order_status_id = 8 where deliver_order_id = '".$order_id."'";
            if($dbm->query($sql)){
                $log->write('[分拣]提交订单为待审核[' . __FUNCTION__ . ']'.$sql."\n\r");

                //添加订单历史记录
                $this->addDeliverOrderSortingHistory($order_id);
                $this->addOrderSortingHistory($result_warehouse['order_id']);


            }
            return array('status' => 8, 'timestamp' => $sql);
        }


        if($result_dan['num']  == 1 && $result_warehouse['do_warehouse_id'] == $result_warehouse['warehouse_id']){
            $dbm->begin();
            $bool = true ;

            $sql_inv = " insert into oc_order_inv (`order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,`box_count` ,`inv_comment` , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` ) (select `order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,`box_count` ,`inv_comment` , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` from oc_x_deliver_order_inv   where order_id = '" . $result_warehouse['order_id'] . "' )";

            $query  =  $dbm->query($sql_inv);


            $sql_deliver_status  = "update oc_x_deliver_order set order_status_id = 6 where deliver_order_id = '".$order_id."'";
            $dbm->query($sql_deliver_status);

            $sql =  "update oc_order set order_status_id = 6 where order_id = '".$data_inv['order_id']."' ";
            if($dbm->query($sql)){


                $log->write('[分拣]提交订单为待审核[' . __FUNCTION__ . ']'.$sql."\n\r");

                //添加订单历史记录
                $this->addDeliverOrderSortingHistory($order_id);
                $this->addOrderSortingHistory($result_warehouse['order_id']);

                //写入oc_x_inventory_order_return_quantity
                $sql_return_quantity = " insert into oc_x_inventory_order_return_quantity ( `order_id` , `deliver_order_id` , `product_id` , `order_quantity` , `sorting_quantity` ,`return_quantity` , `uptime` , `added_by` )SELECT
                ios.order_id , 
                ios.deliver_order_id  , 
	ios.product_id , 
	sum(dop.quantity)  order_quantity , 
  if(sum(dop.quantity) > SUM(ios.quantity) , SUM(ios.quantity) ,sum(dop.quantity)  ) sorting_quantity ,
  (sum(dop.quantity) - sum(ios.quantity)) return_quantity  , 
  NOW(),
  '".$data_inv['add_user_name_id']."'
FROM
	oc_x_inventory_order_sorting ios
LEFT JOIN oc_x_deliver_order_product dop ON ios.deliver_order_id = dop.deliver_order_id 
and ios.product_id  =  dop.product_id
where  ios.status = 1  and ios.deliver_order_id  = '".$order_id."'
GROUP BY  ios.product_id ";
                return $sql_return_quantity;
                $bool =  $bool && $dbm->query($sql_return_quantity);
//                return $bool;
                if(!$bool) {
                    //$log->write('[分拣]分拣提交失败[' . __FUNCTION__ . ']' . "\n\r");
                    $dbm->rollback();
                    return array('status' => 0, 'timestamp' => $data['timestamp']);
                }
                else {
                    //$log->write('[分拣]分拣提交成功[' . __FUNCTION__ . ']' . "\n\r");
                    $dbm->commit();
                    // return array('status' => 1, 'timestamp' => $data['timestamp']);
                }
            }


        }else if($result_dan['num']  > 1 ){
            $dbm->begin();
            $bool = true ;

            $sql = "update oc_x_deliver_order set order_status_id = 6 where deliver_order_id = '".$order_id."'";
            if($dbm->query($sql)) {
                $log->write('[分拣]提交订单为待审核[' . __FUNCTION__ . ']' . $sql . "\n\r");

                //添加订单历史记录
                $this->addDeliverOrderSortingHistory($order_id);
//写入oc_x_inventory_order_return_quantity
                $sql_return_quantity = " insert into oc_x_inventory_order_return_quantity ( `order_id` , `deliver_order_id` , `product_id` , `order_quantity` , `sorting_quantity` ,`return_quantity` , `uptime` , `added_by` )SELECT
                ios.order_id , 
                ios.deliver_order_id  , 
	ios.product_id , 
	sum(dop.quantity)  order_quantity , 
  if(sum(dop.quantity) > SUM(ios.quantity) , SUM(ios.quantity) ,sum(dop.quantity)  ) sorting_quantity ,
  (sum(dop.quantity) - sum(ios.quantity)) return_quantity  , 
  NOW(),
  '".$data_inv['add_user_name_id']."'
FROM
	oc_x_inventory_order_sorting ios
LEFT JOIN oc_x_deliver_order_product dop ON ios.deliver_order_id = dop.deliver_order_id 
and ios.product_id  =  dop.product_id
where  ios.status = 1  and ios.deliver_order_id  = '".$order_id."'
GROUP BY  ios.product_id ";
                $bool =  $bool && $dbm->query($sql_return_quantity);

                if(!$bool) {
                    //$log->write('[分拣]分拣提交失败[' . __FUNCTION__ . ']' . "\n\r");
                    $dbm->rollback();
                    return array('status' => 0, 'timestamp' => $data['timestamp']);
                }
                else {
                    //$log->write('[分拣]分拣提交成功[' . __FUNCTION__ . ']' . "\n\r");
                    $dbm->commit();
                    // return array('status' => 1, 'timestamp' => $data['timestamp']);
                }

            }
        }else if($result_dan['num']  == 1 && $result_warehouse['do_warehouse_id'] != $result_warehouse['warehouse_id']){

            $dbm->begin();
            $bool = true ;

//            $sql_inv = " insert into oc_order_inv (`order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,`box_count` ,`inv_comment` , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` ) (select `order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,`box_count` ,`inv_comment` , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` from oc_x_deliver_order_inv   where order_id = '" . $result_warehouse['order_id'] . "' )";
//
//            $query  =  $dbm->query($sql_inv);


            $sql_deliver_status  = "update oc_x_deliver_order set order_status_id = 6 where deliver_order_id = '".$order_id."'";
            $dbm->query($sql_deliver_status);

            if($dbm->query($sql_deliver_status)){


                $log->write('[分拣]提交订单为待审核[' . __FUNCTION__ . ']'.$sql."\n\r");

                //添加订单历史记录
                $this->addDeliverOrderSortingHistory($order_id);
                $this->addOrderSortingHistory($result_warehouse['order_id']);
            if (intval($result_warehouse['warehouse_id']) != 17) {
                //写入oc_x_inventory_order_return_quantity
                $sql_return_quantity = " insert into oc_x_inventory_order_return_quantity ( `order_id` , `deliver_order_id` , `product_id` , `order_quantity` , `sorting_quantity` ,`return_quantity` , `uptime` , `added_by` )SELECT
                ios.order_id ,
                ios.deliver_order_id  ,
	ios.product_id ,
  	sum(dop.quantity)  order_quantity ,
  if(sum(dop.quantity) > SUM(ios.quantity) , SUM(ios.quantity) ,sum(dop.quantity)  ) sorting_quantity ,
  (sum(dop.quantity) - sum(ios.quantity)) return_quantity  ,
  NOW(),
  '".$data_inv['add_user_name_id']."'
FROM
	oc_x_inventory_order_sorting ios
LEFT JOIN oc_x_deliver_order_product dop ON ios.deliver_order_id = dop.deliver_order_id
and ios.product_id  =  dop.product_id
where  ios.status = 1  and ios.deliver_order_id  = '".$order_id."'
GROUP BY  ios.product_id ";
                $bool =  $bool &&  $dbm->query($sql_return_quantity);

            }

                if(!$bool) {
                    //$log->write('[分拣]分拣提交失败[' . __FUNCTION__ . ']' . "\n\r");
                    $dbm->rollback();
                    return array('status' => 0, 'timestamp' => $data['timestamp']);
                }
                else {
                    //$log->write('[分拣]分拣提交成功[' . __FUNCTION__ . ']' . "\n\r");
                    $dbm->commit();
                    // return array('status' => 1, 'timestamp' => $data['timestamp']);
                }
            }



        }
        return array('status' => 1, 'timestamp' => $sql);


    }


    private function addWarehouseRequ($order_id,$add_user_nam){
        global $dbm;
        $sql  = "select deliver_order_id  from oc_x_warehouse_requisition where deliver_order_id = '".$order_id ."'";
        $query = $dbm->query($sql);
        $result = $query->rows;
        if($result['deliver_order_id'] > 0 ){

        }else{
            $result =  $this->getDeliverOrderInfo($order_id);

            $sql = " insert into  oc_x_warehouse_requisition (`relevant_status_id` , `from_warehouse` , `to_warehouse`, `date_added` , `added_by` ,`status` , `deliver_order_id` , `out_type` ) (select 2, do_warehouse_id , warehouse_id , NOW(),'".$add_user_nam."' , 
            1 ,deliver_order_id , 'DO单调拨'  from  oc_x_deliver_order  where deliver_order_id = '".$order_id."' )";

            $query = $dbm->query($sql);
            $requ_id = $dbm->getLastId();

            if($requ_id){

                $sql = "select order_id , deliver_order_id , frame_vg_list  from  oc_x_deliver_order_inv  where deliver_order_id   =  '".$order_id ." ' ";
                $query = $dbm->query($sql);
                $result = $query->row;
                $frame_vg_list = explode(',',$result['frame_vg_list']);

                $sql_item  = "  insert into oc_x_warehouse_requisition_item (`relevant_id` ,`container_id` ,`comment` ) VALUES ";

                $n= 0 ;
                foreach ($frame_vg_list as $container_id) {

                    $sql_item .= "(
                    '".$requ_id."',
                    
                    '".$container_id."',

                   'DO单调拨'
                    )";
                    if (++$n < sizeof($frame_vg_list)) {
                        $sql_item .= ', ';
                    } else {
                        $sql_item .= ';';
                    }
                }

                $query = $dbm->query($sql_item);


            }

        }

    }

    private function addOrderSortingHistory($order_id, $added_by=0){
        global $dbm;
        $sql = "INSERT INTO oc_order_history (`order_id`, `notify`, `comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`)
                select order_id, 0, '[SYS]订单分拣', now(),  order_status_id, order_payment_status_id, order_deliver_status_id, '".$added_by."' from oc_order where order_id = '".$order_id."'
                ";

        return $dbm->query($sql);
    }

    private function  getDeliverOrderInfo($order_id){
        global $dbm;
        $sql = " select do.warehouse_id , do.do_warehouse_id , o.order_status_id , o.order_id ,o.station_id  , do.is_repack from  oc_x_deliver_order do  LEFT  JOIN  oc_order  o  on  do.order_id = o.order_id where do.deliver_order_id = '".$order_id ."'";
        $query = $dbm->query($sql);
        $result = $query->row;
        return $result;
    }

    private function addDeliverOrderSortingHistory($order_id, $added_by=0){

        global $dbm;
        $sql = "INSERT INTO oc_x_deliver_order_history (`deliver_order_id`, `notify`, `comment`, `date_added`, `order_status_id`, `order_deliver_status_id`, `modified_by`)
                select deliver_order_id, 0, '[SYS]订单分拣', now(),  order_status_id,  order_deliver_status_id, '".$added_by."' from oc_x_deliver_order where deliver_order_id = '".$order_id."'
                ";


        return   $dbm->query($sql);
    }

    private function addReturn($data , $warehouse_id) {
        global $db, $dbm;
        $date = date("Y-m-d");
        if(!sizeof($data) || !is_array($data)){
            return false; //为空直接返回
        }

        //$data = json_decode($data, 2);
        // 期望的数据，需要添加退货的订单号: $data = array(10001,10002,...);
        // 由分拣人员提交的数据将分拣人员信息写入备注

        // 计算订单应收金额，缺货金额
        // 计算缺货金额
        // 应收金额 >= 缺货金额,  仅退货
        // 应收金额 < 缺货金额,  退余额＝缺货金额－应收金额，实际应收为0，退余额
        $targetOrdersString = implode(',',$data);

        //查找订单应付
        $sql = "select order_id, sum(if(accounting = 1, value, 0)) due_total,  sum(if((accounting = 1 and value<0) or code = 'credit_paid' , value, 0)) paid_total from oc_order_total where order_id in (".$targetOrdersString.") group by order_id";
        $query = $db->query($sql);
        $dueInfo = $query->rows;
        $dueInfoList = array();
        foreach($dueInfo as $m){
            $dueInfoList[$m['order_id']] = $m;
        }

        //查找实际出库数据, 分拣数据为扣减库存，是负数，可能有多行，需合并计算
        $sql = "select o.order_id, oi.product_id, sum(oi.quantity) quantity
                from oc_x_stock_move o
                left join oc_x_stock_move_item oi on o.inventory_move_id = oi.inventory_move_id
                where o.order_id in (".$targetOrdersString.") and o.inventory_type_id = 12 group by o.order_id, oi.product_id";
        $query = $db->query($sql);
        $stockMoveInfo = $query->rows;
        $stockMoveInfoList = array();
        foreach($stockMoveInfo as $m){
            $stockMoveInfoList[$m['order_id']][$m['product_id']] = $m['quantity'];
        }

        //查找订单数据, 仅处理分拣中或待审核，且未配送出库的订单，此步骤执行成功后订单将变为已分拣，防止重复执行
        $sql = "select o.order_id, date(o.date_added) date_ordered, o.customer_id, op.product_id,
                op.name, sum(op.quantity) quantity  ,round(sum(op.quantity * op.price) /sum(op.quantity),2 ) price ,  sum(op.quantity * op.price) total
                from oc_order o
                left join oc_order_product op on o.order_id = op.order_id
                where o.order_id in (".$targetOrdersString.")   group by op.product_id ";
        $query = $db->query($sql);
        $orderInfo = array();
        foreach($query->rows as $m){
            $orderInfo[$m['order_id']][$m['product_id']] = $m;
            $orderInfoList[$m['order_id']]['customer_id'] = $m['customer_id'];
            $orderInfoList[$m['order_id']]['date_ordered'] = $m['date_ordered'];
        }
        $returnInfo = array();

        //整理退货信息，$data为传入的订单号
        foreach($data as $m){
            //无订单或分拣信息跳过
            if(!isset($stockMoveInfoList[$m]) || !isset($orderInfo[$m])){
                continue;
            }

            //整理退货表信息
            $returnInfo[$m]['order_id'] = $m;
            $returnInfo[$m]['customer_id'] = $orderInfoList[$m]['customer_id'];
            $returnInfo[$m]['date_ordered'] = $orderInfoList[$m]['date_ordered'];
            $returnInfo[$m]['added_by'] = 0;
            $returnInfo[$m]['comment'] = '[系统]分拣缺货';
            $returnInfo[$m]['return_reason_id'] = 1; //TODO, 目前为缺货未出库，待处理其他类型
            $returnInfo[$m]['return_action_id'] = 1; //TODO, 目前为无操作，若有退余额，更改类型
            $returnInfo[$m]['return_status_id'] = 2; //默认未确认，这里设置已确认，分拣缺货将直接写入系统，无需再确认
            $returnInfo[$m]['return_inventory_flag'] = 0; //分拣缺货不需要退库存
            $returnInfo[$m]['credits_returned'] = 1; //分拣缺货不论是否退余额，该状态设置为已退

            $returnInfo[$m]['due_total'] = $dueInfoList[$m]['due_total'];
            $returnInfo[$m]['paid_total'] = $dueInfoList[$m]['paid_total'];
            $returnInfo[$m]['return_credits'] = 0; //以下将重新计算退货金额，和订单应付比对作为余额退款依据
            $returnInfo[$m]['return_total'] = 0; //退货合计金额
            $returnInfo[$m]['return_qty_total'] = 0; //退货合计数量


            //匹配分拣数量，分拣数据为扣减库存，是负数，这里转换为正数
            foreach($orderInfo[$m] as $n){
                $productStockMoveQty = 0;
                if(isset($stockMoveInfoList[$m][$n['product_id']])){
                    $productStockMoveQty = abs($stockMoveInfoList[$m][$n['product_id']]);
                }

                if($n['quantity'] > $productStockMoveQty){
                    //$returnProductInfo[$n['product_id']] = $n;
                    $returnInfo[$m]['products'][$n['product_id']]['product_id'] = $n['product_id'];
                    $returnInfo[$m]['products'][$n['product_id']]['name'] = $n['name'];
                    $returnInfo[$m]['products'][$n['product_id']]['price'] = $n['price'];
                    $returnInfo[$m]['products'][$n['product_id']]['return_qty'] = $n['quantity'] - $productStockMoveQty;
                    $returnInfo[$m]['products'][$n['product_id']]['return_total'] = ($n['quantity'] - $productStockMoveQty) * $n['price'];

                    $returnInfo[$m]['return_total'] += $returnInfo[$m]['products'][$n['product_id']]['return_total'];
                    $returnInfo[$m]['return_qty_total'] += $returnInfo[$m]['products'][$n['product_id']]['return_qty'];
                }
            }
        }

        //TODO 查找已退货且退余额数据
//        $sql = "
//                select R.order_id, sum(R.return_credits) return_total, sum(if(CT.amount is null, 0, CT.amount)) return_credits_total from oc_return R
//                left join oc_customer_transaction CT on R.return_id = CT.return_id
//                where R.order_id in (".$targetOrdersString.") and R.return_status_id != 3 and R.return_reason_id = 1
//                and R.return_action_id = 3
//                group by R.order_id
//            ";
//        $query = $db->query($sql);
//        $returnedInfo = $query->rows;
//        $returnedInfoList = array();
//        foreach($returnedInfo as $m){
//            $returnedInfoList[$m['order_id']] = $m;
//        }

        //依次处理多个退货信息
        $bool = true;


//        file_put_contents('./log.txt', json_encode($stockMoveInfoList), FILE_APPEND);
//        file_put_contents('./log.txt', json_encode($orderInfo), FILE_APPEND);
        foreach($returnInfo as $m){

            //若退货数量为0，跳过
            if($m['return_qty_total'] == 0){
                continue;
            }

            // 应退余额 ＝ 缺货值 > 应付值 ? (缺货值-应付值) : 0
            // 应退余额 = 支付金额 > 应退余额 ? 应退余额 : 支付金额;
            // TODO 问题，出库金额小于优惠金额时，白送？
            $dueTotal = $returnInfo[$m['order_id']]['due_total'];
            $paidTotal = abs($returnInfo[$m['order_id']]['paid_total']);
            $returnTotal = $returnInfo[$m['order_id']]['return_total'];

            $returnCredits = ($returnTotal > $dueTotal) ? ($returnTotal - $dueTotal) : 0;
            $returnCredits = ($paidTotal > $returnCredits) ? $returnCredits : $paidTotal; //退货金额不大于支付金额（微信＋余额支付合计）
            $returnActionId = ($returnCredits > 0) ? $m['return_action_id'] : 3;

            //写入退货表
            $sql = "INSERT INTO `oc_return` (`order_id`, `customer_id`, `return_reason_id`, `return_action_id`, `return_status_id`, `comment`, `date_ordered`, `date_added`, `date_modified`, `add_user`, `return_credits`, `return_inventory_flag`, `credits_returned`)
                    VALUES(
                        '".$m['order_id']."',
                        '".$m['customer_id']."',
                        '".$m['return_reason_id']."',
                        '".$returnActionId."',
                        '".$m['return_status_id']."',
                        '".$m['comment']."',
                        '".$m['date_ordered']."',
                        NOW(),
                        NOW(),
                        '".$m['added_by']."',
                        '".$returnCredits."',
                        '".$m['return_inventory_flag']."',
                        '".$m['credits_returned']."')";

            $bool = $bool && $dbm->query($sql);
            $return_id = $dbm->getLastId();




            $sql = "INSERT INTO `oc_return_product` (`return_id`, `product_id`, `product`,  `quantity`, `in_part`, `box_quantity`, `price`, `total`, `return_product_credits`) VALUES";
            $n = 0;
            //TODO, 目前仅处理出库缺货，in_part＝0， box_quantity＝1
            foreach ($returnInfo[$m['order_id']]['products'] as $product) {
                $sql .= "(
                    '".$return_id."',
                    '".$product['product_id']."',
                    '".$product['name']."',
                    '".$product['return_qty']."',
                    '0',
                    '1',
                    '".$product['price']."',
                    '".$product['return_total']."',
                    '".$product['return_total']."'
                    )";
                if (++$n < sizeof($returnInfo[$m['order_id']]['products'])) {
                    $sql .= ', ';
                } else {
                    $sql .= ';';
                }
            }
            $bool = $bool && $dbm->query($sql);


            //退余额
            if($returnCredits > 0 ){
                $sql = "INSERT INTO oc_customer_transaction SET added_by = '".$m['added_by']."', customer_id = '" . $m['customer_id'] . "', order_id = '" . $m['order_id'] . "', description = '[系统]分拣缺货退款', amount = '" . $returnCredits . "', customer_transaction_type_id = '9', date_added = NOW(), return_id = '" . $return_id . "'";
                $bool = $bool && $dbm->query($sql);
            }
        }

        return $bool;
    }

    private  function   addDeliverOrderReturn($data , $warehouse_id ){
        global $db, $dbm;
        $date = date("Y-m-d");
        if(!sizeof($data) || !is_array($data)){
            return false; //为空直接返回
        }

        //$data = json_decode($data, 2);
        // 期望的数据，需要添加退货的订单号: $data = array(10001,10002,...);
        // 由分拣人员提交的数据将分拣人员信息写入备注

        // 计算订单应收金额，缺货金额
        // 计算缺货金额
        // 应收金额 >= 缺货金额,  仅退货
        // 应收金额 < 缺货金额,  退余额＝缺货金额－应收金额，实际应收为0，退余额
        $targetOrdersString = implode(',',$data);


//        //查找实际出库数据, 分拣数据为扣减库存，是负数，可能有多行，需合并计算
//        $sql = "select o.order_id, oi.product_id, sum(oi.quantity) quantity
//                from oc_x_stock_move o
//                left join oc_x_stock_move_item oi on o.inventory_move_id = oi.inventory_move_id
//                where o.order_id in (".$targetOrdersString.") and o.inventory_type_id = 12 group by o.order_id, oi.product_id";
//        $query = $db->query($sql);
//        $stockMoveInfo = $query->rows;
//        $stockMoveInfoList = array();
//        foreach($stockMoveInfo as $m){
//            $stockMoveInfoList[$m['order_id']][$m['product_id']] = $m['quantity'];
//        }

        $sql  = " select deliver_order_id order_id  , product_id , sum(quantity) quantity  from  oc_x_inventory_order_sorting where deliver_order_id in ('".$targetOrdersString."') and status =1  group by deliver_order_id , product_id  ";

        $query = $db->query($sql);
        $stockMoveInfo = $query->rows;
        $stockMoveInfoList = array();
        foreach($stockMoveInfo as $m){
            $stockMoveInfoList[$m['order_id']][$m['product_id']] = $m['quantity'];
        }

        //查找订单数据, 仅处理分拣中或待审核，且未配送出库的订单，此步骤执行成功后订单将变为已分拣，防止重复执行
        $sql = "select o.deliver_order_id order_id , date(o.date_added) date_ordered, o.customer_id, op.product_id,
                op.name, sum(op.quantity) quantity  ,round(sum(op.quantity * op.price) /sum(op.quantity),2 ) price ,  sum(op.quantity * op.price) total
                from oc_x_deliver_order o
                left join oc_x_deliver_order_product op on o.deliver_order_id = op.deliver_order_id
                where o.deliver_order_id in (".$targetOrdersString.") and o.order_status_id in (5,8) and o.order_deliver_status_id = 1  group by op.product_id ";

        $query = $db->query($sql);
        $orderInfo = array();
        foreach($query->rows as $m){
            $orderInfo[$m['order_id']][$m['product_id']] = $m;
            $orderInfoList[$m['order_id']]['customer_id'] = $m['customer_id'];
            $orderInfoList[$m['order_id']]['date_ordered'] = $m['date_ordered'];
        }

        $returnInfo = array();

        //整理退货信息，$data为传入的订单号

        foreach($data as $m){
            //无订单或分拣信息跳过

            if(!isset($stockMoveInfoList[$m]) || !isset($orderInfo[$m])){
                continue;
            }

            //整理退货表信息
            $returnInfo[$m]['order_id'] = $m;
            $returnInfo[$m]['customer_id'] = $orderInfoList[$m]['customer_id'];
            $returnInfo[$m]['date_ordered'] = $orderInfoList[$m]['date_ordered'];
            $returnInfo[$m]['added_by'] = 0;
            $returnInfo[$m]['comment'] = '[系统]分拣缺货';
            $returnInfo[$m]['return_reason_id'] = 1; //TODO, 目前为缺货未出库，待处理其他类型
            $returnInfo[$m]['return_action_id'] = 1; //TODO, 目前为无操作，若有退余额，更改类型
            $returnInfo[$m]['return_status_id'] = 2; //默认未确认，这里设置已确认，分拣缺货将直接写入系统，无需再确认
            $returnInfo[$m]['return_inventory_flag'] = 0; //分拣缺货不需要退库存
            $returnInfo[$m]['credits_returned'] = 1; //分拣缺货不论是否退余额，该状态设置为已退

//            $returnInfo[$m]['due_total'] = $dueInfoList[$m]['due_total'];
//            $returnInfo[$m]['paid_total'] = $dueInfoList[$m]['paid_total'];
            $returnInfo[$m]['return_credits'] = 0; //以下将重新计算退货金额，和订单应付比对作为余额退款依据
            $returnInfo[$m]['return_total'] = 0; //退货合计金额
            $returnInfo[$m]['return_qty_total'] = 0; //退货合计数量


            //匹配分拣数量，分拣数据为扣减库存，是负数，这里转换为正数
            foreach($orderInfo[$m] as $n){
                $productStockMoveQty = 0;
                if(isset($stockMoveInfoList[$m][$n['product_id']])){
                    $productStockMoveQty = abs($stockMoveInfoList[$m][$n['product_id']]);
                }

                if($n['quantity'] > $productStockMoveQty){
                    //$returnProductInfo[$n['product_id']] = $n;
                    $returnInfo[$m]['products'][$n['product_id']]['product_id'] = $n['product_id'];
                    $returnInfo[$m]['products'][$n['product_id']]['name'] = $n['name'];
                    $returnInfo[$m]['products'][$n['product_id']]['price'] = $n['price'];
                    $returnInfo[$m]['products'][$n['product_id']]['return_qty'] = $n['quantity'] - $productStockMoveQty;
                    $returnInfo[$m]['products'][$n['product_id']]['return_total'] = ($n['quantity'] - $productStockMoveQty) * $n['price'];

                    $returnInfo[$m]['return_total'] += $returnInfo[$m]['products'][$n['product_id']]['return_total'];
                    $returnInfo[$m]['return_qty_total'] += $returnInfo[$m]['products'][$n['product_id']]['return_qty'];
                }
            }
        }

        //TODO 查找已退货且退余额数据
//        $sql = "
//                select R.order_id, sum(R.return_credits) return_total, sum(if(CT.amount is null, 0, CT.amount)) return_credits_total from oc_return R
//                left join oc_customer_transaction CT on R.return_id = CT.return_id
//                where R.order_id in (".$targetOrdersString.") and R.return_status_id != 3 and R.return_reason_id = 1
//                and R.return_action_id = 3
//                group by R.order_id
//            ";
//        $query = $db->query($sql);
//        $returnedInfo = $query->rows;
//        $returnedInfoList = array();
//        foreach($returnedInfo as $m){
//            $returnedInfoList[$m['order_id']] = $m;
//        }

        //依次处理多个退货信息
        $bool = true;


        foreach($returnInfo as $m){

            //若退货数量为0，跳过
            if($m['return_qty_total'] == 0){
                continue;
            }

            // 应退余额 ＝ 缺货值 > 应付值 ? (缺货值-应付值) : 0
            // 应退余额 = 支付金额 > 应退余额 ? 应退余额 : 支付金额;
            // TODO 问题，出库金额小于优惠金额时，白送？
            $dueTotal = $returnInfo[$m['order_id']]['due_total'];
            $paidTotal = abs($returnInfo[$m['order_id']]['paid_total']);
            $returnTotal = $returnInfo[$m['order_id']]['return_total'];

            $returnCredits = ($returnTotal > $dueTotal) ? ($returnTotal - $dueTotal) : 0;
            $returnCredits = ($paidTotal > $returnCredits) ? $returnCredits : $paidTotal; //退货金额不大于支付金额（微信＋余额支付合计）
            $returnActionId = ($returnCredits > 0) ? $m['return_action_id'] : 3;

            //写入退货表
            $sql = "INSERT INTO `oc_x_deliver_return` (`deliver_order_id`, `customer_id`, `return_reason_id`, `return_action_id`, `return_status_id`, `comment`, `date_ordered`, `date_added`, `date_modified`, `add_user`, `return_credits`, `return_inventory_flag`, `credits_returned`)
                    VALUES(
                        '".$m['order_id']."',
                        '".$m['customer_id']."',
                        '".$m['return_reason_id']."',
                        '".$returnActionId."',
                        '".$m['return_status_id']."',
                        '".$m['comment']."',
                        '".$m['date_ordered']."',
                        NOW(),
                        NOW(),
                        '".$m['added_by']."',
                        '".$returnCredits."',
                        '".$m['return_inventory_flag']."',
                        '".$m['credits_returned']."')";
            $bool = $bool && $dbm->query($sql);
            $return_id = $dbm->getLastId();

            $sql = "INSERT INTO `oc_x_deliver_return_product` (`return_id`, `product_id`, `product`,  `quantity`, `in_part`, `box_quantity`, `price`, `total`, `return_product_credits`) VALUES";
            $n = 0;
            //TODO, 目前仅处理出库缺货，in_part＝0， box_quantity＝1
            foreach ($returnInfo[$m['order_id']]['products'] as $product) {
                $sql .= "(
                    '".$return_id."',
                    '".$product['product_id']."',
                    '".$product['name']."',
                    '".$product['return_qty']."',
                    '0',
                    '1',
                    '".$product['price']."',
                    '".$product['return_total']."',
                    '".$product['return_total']."'
                    )";
                if (++$n < sizeof($returnInfo[$m['order_id']]['products'])) {
                    $sql .= ', ';
                } else {
                    $sql .= ';';
                }
            }

            $bool = $bool && $dbm->query($sql);


        }

        return $bool;
    }






    public function addPurchaseOrderProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;

        $no_sorting = false;


        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryInProduct'; //Up
        $warehouse_id = $data_inv['warehouse_id'];
        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;
        if (!$order_id) {
            return false;
        }

        $sql =  "select purchase_order_id ,order_type from oc_x_pre_purchase_order WHERE  purchase_order_id = '".$order_id ."'";

        $query = $dbm->query($sql);
        $result_type = $query->row;
        $order_type = $result_type['order_type'];


        $sql = "select xsm.order_id from oc_x_stock_move as xsm where xsm.inventory_type_id = 11 and xsm.purchase_order_id = " . $order_id;

        $query = $dbm->query($sql);
        $result_exists = $query->rows;

        if(!empty($result_exists)){
            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);

        }


        $sql = "SELECT xis.*, '' barcode, op.price, p.weight_inv_flag, p.sku_id
                FROM oc_x_inventory_purchase_order_sorting AS xis
                -- LEFT JOIN labelprinter.productlist AS lpp ON xis.product_id = lpp.product_id
                LEFT JOIN oc_x_pre_purchase_order_product AS op ON op.purchase_order_id = xis.order_id AND op.product_id = xis.product_id
                LEFT JOIN oc_product AS p ON p.product_id = xis.product_id
                WHERE xis.move_flag = 0 AND xis.order_id = " . $order_id;

        $query = $dbm->query($sql);
        $result = $query->rows;


        $stationProductMove = array();
        $update_sorting_id_arr = array();
        if (sizeof($result)) {
            foreach ($result as $k => $v) {
                if (!empty($v['product_barcode'])) {
                    $product_barcode_arr = array();
                    $product_barcode_arr = json_decode($v['product_barcode']);
                    $product_weight = 0;
                    foreach ($product_barcode_arr as $pbk => $pbv) {
                        if ((strlen($pbv) == 18 || strlen($pbv) == 16 ) && $v['weight_inv_flag'] == 1) {
                            $product_weight += (int) substr($pbv, 4, 5);
                        }
                        else{
                            if (strlen($pbv) == 18) {
                                $v['barcode'] = $pbv;
                            }

                        }
                    }
                }

                $stationProductMove[] = array(
                    'product_batch' => $v['barcode'],
                    'due_date' => '0000-00-00', //There is a bug till year 2099.
                    'product_id' => $v['product_id'],
                    'special_price' => $v['price'],
                    'qty' => abs($v['quantity']),
                    'product_weight' => $product_weight,
                    'sku_id' => $v['sku_id']
                    //'qty' => '-'.$v['quantity']
                );


                $update_sorting_id_arr[] = $v['inventory_sorting_id'];
            }

            $data_inv['products'] = $stationProductMove;
        } else {
            $no_sorting = true;
            /*
            $inventory_user_admin = array('randy','alex','leibanban','yangyang');
            if(!in_array($data_inv['add_user_name'], $inventory_user_admin)){
                return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
            }
             *
             */
        }

        $sql = "select xsm.order_id from oc_x_stock_move as xsm where xsm.inventory_type_id = 11 and xsm.purchase_order_id = " . $order_id;

        $query = $dbm->query($sql);
        $result_exists = $query->rows;

        if(!empty($result_exists)){
            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);

        }


        if (sizeof($result) || $no_sorting) {

            $log->write($data_inv);

            $data_inv['purchase_order_id'] = $data_inv['order_id'];
            $data_inv['order_id'] = 0;


            $result = $this->addInventoryMoveOrder($data_inv, 1,$warehouse_id,$order_type);

            if ($result && !empty($update_sorting_id_arr)) {
                $update_sorting_id_str = implode(",", $update_sorting_id_arr);
                $sql = "update oc_x_inventory_purchase_order_sorting set move_flag = 1 where inventory_sorting_id in (" . $update_sorting_id_str . ")";
                $log->write($sql);
                $query = $dbm->query($sql);
            }
        }





        if ($result || $no_sorting) {

            $sql = "update oc_x_pre_purchase_order set status = 4 where purchase_order_id = " . $data_inv['purchase_order_id'];
            $dbm->query($sql);
            if (!empty($result)) {
                //添加可售库存和商品上架
                if(!empty($data_inv)){
                    foreach($data_inv['products'] as $key => $value){
                        $data_inv['products'][$value['product_id']] += $value['qty'];
                        unset($data_inv['products'][$key]);
                    }
                }

                $this->adjust_post($data_inv,$warehouse_id,$order_type,$data_inv['purchase_order_id']);
            }



            /*
              $sql = "update oc_order_inv set inv_status = 0 where order_id = " . $data_inv['order_id'];
              $dbm->query($sql);

              $data_inv['frame_count'] = $data_inv['frame_count'] ? $data_inv['frame_count'] : 0;
              $data_inv['incubator_count'] = $data_inv['incubator_count'] ? $data_inv['incubator_count'] : 0;
              $sql = "insert into oc_order_inv(order_id,frame_count,incubator_count,inv_comment) values(" . $data_inv['order_id'] . "," . $data_inv['frame_count'] . "," . $data_inv['incubator_count'] . ",'" . $data_inv['inv_comment'] . "');";
              $dbm->query($sql);
             */

            //已支付采购入库金额写入
            /*
            $sql = "select checkout_status , checkout_type_id , added_by ,supplier_type  from oc_x_pre_purchase_order where purchase_order_id = '".$data_inv['purchase_order_id']."'";

            $query = $dbm->query($sql);
            $result_pur = $query->row;

            if($result_pur['checkout_status'] == 2 && $result_pur['checkout_type_id'] == 3){

                $sql_amount = "SELECT
sum(smi.quantity*smi.price) amount
FROM
	oc_x_stock_move sm
LEFT JOIN oc_x_stock_move_item smi ON sm.inventory_move_id = smi.inventory_move_id
where sm.purchase_order_id = '".$data_inv['purchase_order_id']."'
GROUP BY sm.inventory_move_id ";
                $query = $dbm->query($sql_amount);
                $result_amount = $query->row;


                $sql = "insert into oc_x_supplier_transaction  (`supplier_id` ,`purchase_order_id` , `supplier_transaction_type_id` ,`description` , `amount`,`date_added`,`added_by`,`is_enabled`) VALUES ('".$result_pur['supplier_type']."','".$data_inv['purchase_order_id']."' , 2 , '预付款已支付采购单入库金额' ,'".$result_amount['amount']."' , NOW(),'".$data_inv['user_id']."'  , 1 )";
                $query = $dbm->query($sql);

            }
            */

            return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }
    }
    //根据采购入库修改库存记录表
    public function updateStockSectionProduct($data, $station_id, $language_id = 2, $origin_id){
        global $dbm;
        global $log;

        $data_inv = json_decode($data, 2);
        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;                          //订单编号
//        $product_ids = isset($data_inv['product_ids']) ? $data_inv['product_ids'] : false;                //商品编号
//        $areanames = isset($data_inv['areanames']) ? $data_inv['areanames'] : false;                       //区域编号
        $warehouse_id = isset($data_inv['warehouse_id']) ? $data_inv['warehouse_id'] : false;
//        $section_type = isset($data_inv['transfer_area']) ? $data_inv['transfer_area'] : false;           //区域类型
//        $update_qunity = isset($data_inv['update_qunity']) ? $data_inv['update_qunity'] : false;          //计划量
//        $update_section = isset($data_inv['update_section']) ? $data_inv['update_section'] : false;       //投篮量
        $date_added = date('Y-m-d H:i:s',time());                                                      //时间
//        $added_by = isset($data_inv['add_user_name']) ? $data_inv['add_user_name'] : false;               //修改人

        $sql = "select xis.*,xpo.order_type from oc_x_inventory_purchase_order_sorting xis left join oc_x_pre_purchase_order xpo on xpo.purchase_order_id = xis.order_id where xis.order_id = " . $order_id . " and xis.move_flag = 1 and xis.warehouse_id = ".$warehouse_id;
        $query = $dbm->query($sql);
        $results = $query->rows;

//        return $count;
        foreach ($results as $productData) {
            $product_id = $productData['product_id'];                 //商品编号
            $areaname = $productData['stock_section_id'];            //区域编号
            $section_type = $productData['stock_section_type_id'];           //区域类型
//            $update_qunity = $productData['update_qunity'];          //计划量
//            $update_section = $productData['update_section'];        //投篮量
            $added_by = $productData['added_by'];                     //修改人
            $quantity = $productData['quantity'] >=0 ? $productData['quantity'] : (0-$productData['quantity']);     //判断是否是退单表
            $sql = 'SELECT * FROM oc_x_stock_section_product WHERE warehouse_id = "' . $warehouse_id . '" AND stock_section_id = "' . $areaname . '" AND stock_section_type_id = "' . $section_type . '" AND product_id = "' . $product_id . '"';
//           return $sql;
            $query = $dbm->query($sql);
            $result = $query->rows;
//            return $result;
            if ($result) {
                $quantity1 = $quantity + $result[0]['qunity'];
                $sql1 = 'UPDATE oc_x_stock_section_product SET quantity = "' . $quantity1 . '" WHERE id = "' . $result[0]['id'] . '"';
//                return $sql1;

                $dbm->query($sql1);
            } else {
                $sql1 = 'INSERT INTO oc_x_stock_section_product (warehouse_id,stock_section_id,stock_section_type_id,product_id,quantity,date_added) VALUES ("' . $warehouse_id . '","' . $areaname . '","' . $section_type . '","' . $product_id . '","' . $quantity . '","' . $date_added . '")';
//                return $sql1;
                $dbm->query($sql1);
            }
            $sql2 = 'INSERT INTO oc_x_stock_section_product_move (warehouse_id,stock_section_id,section_move_type_id,product_id,quantity,date_added,added_by,purchase_order_id) VALUES ("' . $warehouse_id . '","' . $areaname . '","' . $section_type . '","' . $product_id . '","' . $quantity . '","' . $date_added . '","' . $added_by . '","' .$order_id.'")';
//            return $sql2;
            $dbm->query($sql2);
        }
//        } $count = count($product_ids);
////        return $count;
//        for ($i=0;$i<$count;$i++) {
//            $product_id = $product_ids[$i];
//            $areaname = $areanames[$i];
//            $quantity = $update_qunity[$i] - $update_section[$i];
//            $sql = 'SELECT * FROM oc_x_stock_section_product WHERE warehouse_id = "'.$warehouse_id.'" AND stock_section_id = "'.$areaname.'" AND stock_section_type_id = "'.$section_type.'" AND product_id = "'.$product_id.'"';
////           return $sql;
//            $query = $dbm->query($sql);
//            $result = $query->rows;
////            return $result;
//            if ($result) {
//                $quantity1 = $quantity + $result[0]['qunity'];
//                $sql1 = 'UPDATE oc_x_stock_section_product SET quantity = "'.$quantity1.'" WHERE id = "'.$result[0]['id'].'"';
////                return $sql1;
//
//                $dbm->query($sql1);
//            } else {
//                $sql1 = 'INSERT INTO oc_x_stock_section_product (warehouse_id,stock_section_id,stock_section_type_id,product_id,quantity,date_added) VALUES ("'.$warehouse_id.'","'.$areaname.'","'.$section_type.'","'.$product_id.'","'.$quantity.'","'.$date_added.'")';
////                return $sql1;
//                $dbm->query($sql1);
//            }
//            $sql2 = 'INSERT INTO oc_x_stock_section_product_move (warehouse_id,stock_section_id,section_move_type_id,product_id,quantity,date_added,added_by) VALUES ("'.$warehouse_id.'","'.$areaname.'","'.$section_type.'","'.$product_id.'","'.$quantity.'","'.$date_added.'","'.$added_by.'")';
////            return $sql2;
//            $dbm->query($sql2);
//            return array('status' => 1);
//        }
        return array('status' => 1);

    }



    public function delOrderProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);

        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;
        $add_user_name = isset($data_inv['add_user_name']) ? $data_inv['add_user_name'] : false;
        $add_user_name_id = isset($data_inv['inventory_user_id']) ? $data_inv['inventory_user_id'] : false;
        if (!$order_id) {
            return false;
        }

        $order_order_id = $this->getDeliverOrderInfo($order_id);


        $sql = "select * from oc_x_inventory_order_sorting where deliver_order_id = " . $order_id . " and move_flag = 1 and status =1 ";
        $query = $dbm->query($sql);
        $result_exists = $query->rows;
        if(!empty($result_exists)){
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }

        $sql_status =  "select order_status_id from oc_order where  order_id = '".$order_order_id['order_id'] ."'";

        $query = $dbm->query($sql_status);
        $result_status = $query->row;

        if($result_status['order_status_id'] == 6){
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }

        if($add_user_name_id != 527 && $add_user_name_id != 777 ) {


            $sql_do_status = "select order_status_id from oc_x_deliver_order  where  deliver_order_id = '" . $order_id . "'";
            $query = $dbm->query($sql_do_status);
            $result_do_status = $query->row;

            if ($result_do_status['order_status_id'] == 6) {

                return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
            }
        }
        //Update order status back before sorting start
        $sql = "update oc_order set order_status_id = 2 where  order_id = '".$order_order_id['order_id']."'";

        $dbm->query($sql);

        $sql = "update oc_x_deliver_order set order_status_id = 2 where  deliver_order_id = '".$order_id."'";
        $dbm->query($sql);

        //Remove Order Inv
        $sql_inv = "delete from oc_x_deliver_order_inv where deliver_order_id = " . $order_id;
        $dbm->query($sql_inv);

        $sql_distr = "delete from oc_order_distr where deliver_order_id = " . $order_id;
        $dbm->query($sql_distr);

        $sql_section =  "INSERT INTO oc_x_stock_section_product_move (
	`warehouse_id`,
	`stock_section_id`,
	`section_move_type_id`,
	`product_id`,
	`quantity`,
	`date_added`,
	`added_name`,
`order_id` 
)
(
		SELECT
			warehouse_id,
			stock_section_id,
			section_move_type_id,
			product_id,
			abs(quantity),
			NOW(),
			'".$add_user_name."' ,
			order_id 
		FROM
			oc_x_stock_section_product_move
		WHERE
			order_id = '".$order_order_id['order_id']."' and status = 1 
	)";


        $dbm->query($sql_section);

        $sql_section_quantity =  "UPDATE oc_x_stock_section_product ssp
LEFT JOIN oc_x_stock_section_product_move spm ON ssp.stock_section_id = spm.stock_section_id
AND ssp.product_id = spm.product_id
SET ssp.quantity = abs(spm.quantity) + ssp.quantity
WHERE
	spm.order_id = '". $order_order_id['order_id'] . "'  and spm.status = 1 ";

        $dbm->query($sql_section_quantity);

        $sql_update =  "update oc_x_stock_section_product_move  set status = 2  where order_id  = '".$order_order_id['order_id']."' ";

        $dbm->query($sql_update);

        $sql_container = " select GROUP_CONCAT(container_id) container_id from oc_x_inventory_order_sorting  where deliver_order_id  =  '".$order_id ."' and status =1  group by deliver_order_id ";

        $query = $dbm->query($sql_container);
        $result_container = $query->row;
        $container_id = $result_container['container_id'];
        if(!empty($container_id)){
            $sql_del_container = " update  oc_x_container  set occupy = 0  where container_id in ($container_id) ";

            $query = $dbm->query($sql_del_container);
        }


        //Remove Order Sorting Info

        $sql = "update   oc_x_inventory_order_return_quantity set status = 0   where deliver_order_id = " . $order_id;
        $dbm->query($sql);
            
        $sql = "update   oc_x_inventory_order_sorting set status = 0   where deliver_order_id = " . $order_id;

        if ($dbm->query($sql)) {
            return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }
    }



    public function delPurchaseOrderProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);

        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;
        if (!$order_id) {
            return false;
        }


        $sql = "select * from oc_x_inventory_purchase_order_sorting where order_id = " . $order_id . " and move_flag = 1";
        $query = $dbm->query($sql);
        $result_exists = $query->rows;
        if(!empty($result_exists)){
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }




        $sql = "delete from oc_x_inventory_purchase_order_sorting where order_id = " . $order_id;
        $dbm->query($sql);

        $sql = "select inventory_move_id from oc_x_stock_move where inventory_type_id = 11 and purchase_order_id = " . $order_id;
        $query = $dbm->query($sql);
        $result = $query->rows;
        if(!empty($result)){
            foreach($result as $key => $value){
                $sql = "delete from oc_x_stock_move where inventory_move_id = " . $value['inventory_move_id'];
                $dbm->query($sql);
                $sql = "delete from oc_x_stock_move_item where inventory_move_id = " . $value['inventory_move_id'];
                $dbm->query($sql);
            }
        }

        $sql = "update oc_x_pre_purchase_order set status = 2 where purchase_order_id = " . $order_id;


        if ($dbm->query($sql)) {

            return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }
    }
    //删除调拨单中间表数据
    public function delPurchaseOrderRelevantToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);

        $warehouse_id = isset($data_inv['warehouse_id']) ? trim($data_inv['warehouse_id']) : false;
        $date_added = isset($data_inv['date_added']) ? $data_inv['date_added'] : false;
        $relevant_id = isset($data_inv['relevant_id']) ? $data_inv['relevant_id'] : false;
        $product_id = isset($data_inv['product_id']) ? trim($data_inv['product_id']) : false;
        $container_id = isset($data_inv['container_id']) ? trim($data_inv['container_id']) : false;
//        return $data_inv;
        if (!$relevant_id) {
            return false;
        }
        if ($product_id) {
            $relevant_id = $relevant_id[0];
            if ($container_id != 0) {
                $sql = "DELETE FROM oc_x_warehouse_requisition_temporary 
WHERE relevant_id =". $relevant_id ." 
AND warehouse_id =".$warehouse_id." 
AND container_id =".$container_id;
            } else {
                $sql = "DELETE FROM oc_x_warehouse_requisition_temporary 
WHERE relevant_id =". $relevant_id ." 
AND warehouse_id =".$warehouse_id." 
AND product_id =".$product_id."
AND container_id =".$container_id;
            }
//        return $sql;
            $dbm->query($sql);
            return array('status'=>2);
        } else {
            $relevant_id = $relevant_id[0];
            if ($container_id) {
                $sql = "DELETE FROM oc_x_warehouse_requisition_temporary 
WHERE relevant_id =". $relevant_id ." 
AND warehouse_id =".$warehouse_id." 
AND container_id =".$container_id;
            } else {
//        foreach ($relevant_id as $relevant) {
                $sql = "DELETE FROM oc_x_warehouse_requisition_temporary WHERE relevant_id IN (" . $relevant_id . ") AND warehouse_id =" . $warehouse_id;
            }
//        return $sql;
            $dbm->query($sql);
            return array('status'=>1);
        }
//        }




//        $sql = "select inventory_move_id from oc_x_stock_move where inventory_type_id = 11 and purchase_order_id = " . $order_id;
//        $query = $dbm->query($sql);
//        $result = $query->rows;
//        if(!empty($result)){
//            foreach($result as $key => $value){
//                $sql = "delete from oc_x_stock_move where inventory_move_id = " . $value['inventory_move_id'];
//                $dbm->query($sql);
//                $sql = "delete from oc_x_stock_move_item where inventory_move_id = " . $value['inventory_move_id'];
//                $dbm->query($sql);
//            }
//        }
//
//        $sql = "update oc_x_pre_purchase_order set status = 2 where purchase_order_id = " . $order_id;
//
//
//        if ($dbm->query($sql)) {
//
//            return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
//        } else {
//            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
//        }
    }




    public function addCheckProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryCheck'; //Up
        $warehouse_id = $data_inv['warehouse_id'];

        $date = $data_inv['date'];





        $sql = "select xis.*,lpp.barcode,op.price,op.sku_id,op.station_id from oc_x_inventory_check_sorting as xis left join labelprinter.productlist as lpp on xis.product_id = lpp.product_id left join oc_product as op on op.product_id = xis.product_id where xis.move_flag=0 and xis.uptime > '" . date("Y-m-d",  strtotime($date . " 00:00:00") - 24*3600) . " 12:00:00' and xis.uptime < '" . $date . " 12:00:00'";

        $query = $dbm->query($sql);
        $result = $query->rows;


        $stationProductMove = array();
        $update_sorting_id_arr = array();
        if (sizeof($result)) {
            foreach ($result as $k => $v) {

                if (!empty($v['product_barcode'])) {
                    $product_barcode_arr = array();
                    $product_barcode_arr = json_decode($v['product_barcode']);
                    $product_weight = 0;
                    foreach ($product_barcode_arr as $pbk => $pbv) {
                        $product_weight += (int) substr($pbv, 4, 5);
                    }
                }

                $stationProductMove[] = array(
                    'product_batch' => $v['barcode'],
                    'due_date' => '0000-00-00', //There is a bug till year 2099.
                    'product_id' => $v['product_id'],
                    'special_price' => $v['price'],
                    'qty' => $v['quantity'],
                    'product_weight' => $product_weight,
                    'sku_id' => $v['sku_id']
                    //'qty' => '-'.$v['quantity']
                );


                $update_sorting_id_arr[] = $v['inventory_sorting_id'];
            }

            $data_inv['products'] = $stationProductMove;
        } else {
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }

        //如果盘点数据中缺少快销品数据，添加之前的快销品的数据
        //盘点的数据
        $stationProductMove_ids = array();
        foreach($stationProductMove as $key=>$value){
            $stationProductMove_ids[$value['product_id']] = $value['product_id'];
        }

        //商品促销数据
        $sql = "select * from oc_product_to_promotion_product";
        $product_to_promotion_arr = array();
        $query = $dbm->query($sql);
        $result = $query->rows;
        foreach($result as $key=>$value){
            $product_to_promotion_arr[$value['product_id']] = $value['promotion_product_id'];
        }





// 暂停计算快消库存
//        $sql = "select inventory_move_id,date_added from oc_x_stock_move where inventory_type_id = 14 order by inventory_move_id desc limit 1";
//
//        $query = $dbm->query($sql);
//        $inventory_check = $query->row;
//
//        $inventory_check_id = $inventory_check['inventory_move_id'];
//        $inventory_check_time = $inventory_check['date_added'];
//        if($inventory_check_id){
//            $sql = "SELECT
//                    xsm.inventory_move_id,
//                    xsm.inventory_type_id,
//                    smi.product_id,
//                    sum(smi.quantity) as product_move_type_quantity,
//                    pd.name,
//                    smi.product_batch,
//                    smi.price,
//                    smi.sku_id
//            FROM
//                    oc_x_stock_move AS xsm
//            left JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id
//            left join oc_product as p on p.product_id = smi.product_id
//            left join oc_product_to_category as ptc on ptc.product_id = smi.product_id
//            left join oc_product_description as pd on pd.product_id=smi.product_id and pd.language_id = 2
//            WHERE
//                    xsm.inventory_move_id >= " . $inventory_check_id . "
//                and xsm.date_added >= '" . $inventory_check_time . "'
//            and p.station_id = 2  and xsm.warehouse_id in (0,10,11)
//            group by xsm.inventory_type_id,smi.product_id";
//
//            $query = $dbm->query($sql);
//            $inventory_arr = $query->rows;
//            $inventory_product_move_arr = array();
//            foreach($inventory_arr as $key=>$value){
//                $inventory_product_move_arr[$value['product_id']]['quantity'][$value['inventory_type_id']] = $value['product_move_type_quantity'];
//                $inventory_product_move_arr[$value['product_id']]['name'] = $value['name'];
//                $inventory_product_move_arr[$value['product_id']]['sum_quantity'] = 0;
//                $inventory_product_move_arr[$value['product_id']]['date_added'] = $inventory_check['date_added'];
//
//                $inventory_product_move_arr[$value['product_id']]['product_batch'] = $value['product_batch'];
//                $inventory_product_move_arr[$value['product_id']]['price'] = $value['price'];
//                $inventory_product_move_arr[$value['product_id']]['sku_id'] = $value['sku_id'];
//            }
//
//        }
//
//        if(!empty($inventory_product_move_arr)){
//            foreach($inventory_product_move_arr as $key=>$value){
//                foreach($value['quantity'] as $k=>$v){
//
//                    if($k == 15 && $product_to_promotion_arr[$key]){
//                        if(isset($inventory_product_move_arr[$product_to_promotion_arr[$key]])){
//                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['quantity']['15'] = abs($v);
//                        }
//                        else{
//                            $inventory_product_move_arr[$product_to_promotion_arr[$key]] = $value;
//                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['quantity'] = array();
//
//                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['name'] =  '';
//                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['sum_quantity'] = 0;
//                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['date_added'] = $value['date_added'];
//                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['quantity']['15'] = abs($v);
//
//
//                        }
//
//
//                    }
//
//                }
//            }
//
//            //echo "<pre>";print_r($inventory_product_move_arr);exit;
//            foreach($inventory_product_move_arr as $key1=>$value1){
//
//                foreach($value1['quantity'] as $k1=>$v1){
//                    $inventory_product_move_arr[$key1]['sum_quantity'] += $v1;
//                }
//
//                if(!isset($stationProductMove_ids[$key1])){
//                    $stationProductMove[] = array(
//                        'product_batch' => $value1['product_batch'],
//                        'due_date' => '0000-00-00', //There is a bug till year 2099.
//                        'product_id' => $key1,
//                        'special_price' => $value1['price'],
//                        'qty' => $inventory_product_move_arr[$key1]['sum_quantity'] >= 0 ? $inventory_product_move_arr[$key1]['sum_quantity'] : 0,
//                        'product_weight' => 0,
//                        'sku_id' => $value1['sku_id']
//                        //'qty' => '-'.$v['quantity']
//                    );
//                }
//
//            }
//
//            $data_inv['products'] = $stationProductMove;
//        }





        if (sizeof($result)) {

            //$log->write($data_inv);


            //备份history
            /*
              $dbm->query("START TRANSACTION");
              $sql = "INSERT INTO oc_x_stock_move_item_history(inventory_move_item_id, inventory_move_id, station_id, due_date, product_id, price, product_batch, quantity, weight, weight_class_id, is_gift, checked, status) ";
              $sql .= "SELECT inventory_move_item_id, inventory_move_id, station_id, due_date, product_id, price, product_batch, quantity, weight, weight_class_id, is_gift, checked, status FROM oc_x_inventory_move_item";
              $this->db->query($sql);

              $this->db->query("TRUNCATE oc_x_inventory_move_item");
              $this->db->query("INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`) VALUES('1', '{$date}', '{$time}', '1', '1', '{$date_added}', '{$user_id}', '{$user_name}')");
              $inventory_move_id = $this->db->getLastId();
              foreach($products as $product){
              $this->db->query("INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`) VALUES('{$inventory_move_id}', '1', '{$product['product_id']}', '{$product['quantity']}')");
              }


              $this->db->query("COMMIT");
             */




            $result = $this->addInventoryMoveOrder($data_inv, 1,$warehouse_id);
            if ($result) {
                $update_sorting_id_str = implode(",", $update_sorting_id_arr);
                $sql = "update oc_x_inventory_check_sorting set move_flag = 1 where inventory_sorting_id in (" . $update_sorting_id_str . ")";
                $log->write($sql);
                $query = $dbm->query($sql);
            }
        }





        if ($result) {

            return array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }
    }




    public function addCheckSingleProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryAdjust'; //Up
        $warehouse_id = $data_inv['warehouse_id'];

        $date = $data_inv['date'];
        $sorting_id = $data_inv['sorting_id'];



        $sql = "select xis.*,lpp.barcode,op.price,op.sku_id,op.station_id from oc_x_inventory_check_single_sorting as xis left join labelprinter.productlist as lpp on xis.product_id = lpp.product_id left join oc_product as op on op.product_id = xis.product_id where xis.move_flag=0 and inventory_sorting_id = " . $sorting_id;

        $query = $dbm->query($sql);
        $result = $query->rows;



        $station_id = 1;
        $stationProductMove = array();
        $update_sorting_id_arr = array();
        if (sizeof($result)) {
            foreach ($result as $k => $v) {

                if (!empty($v['product_barcode'])) {
                    $product_barcode_arr = array();
                    $product_barcode_arr = json_decode($v['product_barcode']);
                    $product_weight = 0;
                    foreach ($product_barcode_arr as $pbk => $pbv) {
                        $product_weight += (int) substr($pbv, 4, 5);
                    }
                }

                $stationProductMove[] = array(
                    'product_batch' => $v['barcode'],
                    'due_date' => '0000-00-00', //There is a bug till year 2099.
                    'product_id' => $v['product_id'],
                    'special_price' => $v['price'],
                    'qty' => $v['occupy_quantity'] + $v['quantity'] - $v['inv_quantity'],
                    'product_weight' => $product_weight,
                    'sku_id' => $v['sku_id'],
                    'station_id' => $v['station_id']
                    //'qty' => '-'.$v['quantity']
                );

                //TODO, SET Station_ID for this move, Know there will be an error result if the product contain both station_id=1 and station_id=2
                $station_id = $v['station_id'];

                $update_sorting_id_arr[] = $v['inventory_sorting_id'];
            }

            $data_inv['products'] = $stationProductMove;
        } else {
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }






        if (sizeof($result)) {
            $log->write(serialize($data_inv));

            $result = $this->addInventoryMoveOrder($data_inv, $station_id,$warehouse_id);

            if ($result) {
                $update_sorting_id_str = implode(",", $update_sorting_id_arr);

                //获取最早盘点值
                $sql_move  = " select   max(inventory_move_id) inventory_move_id  from oc_x_stock_move  WHERE  inventory_type_id = 14  and warehouse_id = '".$warehouse_id ."'  ";

                $query = $dbm->query($sql_move);
                $result_move = $query->row;

                //获取该商品从最早的盘点值到提交时候的库存值

                $sql_real = "select  sum(smi.quantity) real_quantity from  oc_x_stock_move sm LEFT join oc_x_stock_move_item smi on sm.inventory_move_id = smi.inventory_move_id WHERE  smi.product_id = '".$stationProductMove[0]['product_id']."'and  sm.inventory_move_id >= '". $result_move['inventory_move_id'] . "' and  sm.warehouse_id = '".$warehouse_id ."' GROUP BY smi.product_id ";

                $query = $dbm->query($sql_real);
                $result_real = $query->row;

                //获取下单未分拣的值
                $sql_order = " select  sum(op.quantity) order_quantity  from   oc_order o LEFT JOIN  oc_order_product op on o.order_id = op.order_id where o.warehouse_id = '".$warehouse_id ."' and o.order_status_id in (1,2,5,8) and op.product_id = '".$stationProductMove[0]['product_id'] ."' and DATE (date_added) between  date_sub(current_date(), interval 6 day)  and  current_date()  group by op.product_id  ";


                $query = $dbm->query($sql_order);
                $result_order = $query->row;

                $qian_quantity = $result_real['real_quantity'] - abs($result_order['order_quantity']);

                //获取前台商品库存
                $sql_inventory = " select sum(quantity) inventory_quantity  from  oc_x_inventory_move_item  where  product_id = '".$stationProductMove[0]['product_id']."' and status = 1  and warehouse_id = '".$warehouse_id ."'";

                $query = $dbm->query($sql_inventory);
                $result_inventory = $query->row;

                if($result_inventory['inventory_quantity'] >= $qian_quantity ){
                    $inventory_type = 6 ;
                    $memo = '盘点亏损';
                }else {
                    $inventory_type = 7 ;
                    $memo = '盘点盈余';
                }
                $date = date("Y-m-d");
                if($result_inventory['inventory_quantity'] == 0 ){
                    $sql_di = " insert  into  oc_x_inventory_move (`station_id` , `date` , `timestamp` , `inventory_type_id` , `date_added` , `status` , `add_user_name` ,`memo` , `warehouse_id`) VALUES  ('2', '".$date."' ,UNIX_TIMESTAMP(NOW()) ,  '".$inventory_type."' , NOW() , '1' , '".$data_inv['add_user_name']."'  , '".$memo ."' , '".$warehouse_id."' )";
                    $query = $dbm->query($sql_di);
                    $inventory_di_move_id = $dbm->getLastId();

                    $sql = " insert into oc_x_inventory_move_item (`inventory_move_id`,`station_id` ,`product_id`,`quantity`,`status`,`warehouse_id`) VALUES ('".$inventory_di_move_id."' , '2', '".$stationProductMove[0]['product_id']."' , '".$qian_quantity *(1) ."' , '1' , '".$warehouse_id."')";

                    $query = $dbm->query($sql);


                }else{
                    //抵消前台库存
                    $sql_di = " insert  into  oc_x_inventory_move (`station_id` , `date` , `timestamp` , `inventory_type_id` , `date_added` , `status` , `add_user_name` ,`memo` , `warehouse_id`) VALUES  ('2', '".$date."' ,UNIX_TIMESTAMP(NOW()) ,  '".$inventory_type."' , NOW() , '1' , '".$data_inv['add_user_name']."'  , '".$memo ."' , '".$warehouse_id."' )";
                    $query = $dbm->query($sql_di);
                    $inventory_di_move_id = $dbm->getLastId();


                    $sql = " insert into oc_x_inventory_move_item (`inventory_move_id`,`station_id` ,`product_id`,`quantity`,`status`,`warehouse_id`) VALUES ('".$inventory_di_move_id."' , '2', '".$stationProductMove[0]['product_id']."' , '".$result_inventory['inventory_quantity'] * (-1) ."' , '1' , '".$warehouse_id."')";

                    $query = $dbm->query($sql);

                    //盘点数量减去下单未分拣
                    $sql_pan = " insert  into  oc_x_inventory_move (`station_id` , `date` , `timestamp` , `inventory_type_id` , `date_added` , `status` , `add_user_name` ,`memo` , `warehouse_id`) VALUES  ('2', '".$date."' ,UNIX_TIMESTAMP(NOW()) ,  '".$inventory_type."' , NOW() , '1' , '".$data_inv['add_user_name']."'  , '".$memo ."' , '".$warehouse_id."' )";

                    $query = $dbm->query($sql_pan);


                    $inventory_pan_move_id = $dbm->getLastId();

                    $sql = " insert into oc_x_inventory_move_item (`inventory_move_id`,`station_id` ,`product_id`,`quantity`,`status`,`warehouse_id`) VALUES ('".$inventory_pan_move_id."' , '2', '".$stationProductMove[0]['product_id']."' , '".$qian_quantity ."' , '1' , '".$warehouse_id."')";

                    $query = $dbm->query($sql);

                }


                $sql = "update oc_x_inventory_check_single_sorting set move_flag = 1, subtime = NOW() where inventory_sorting_id in (" . $update_sorting_id_str . ")";
                $log->write($sql);
                $query = $dbm->query($sql);
            }
        }


        if ($result) {

            return array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }
    }


    public function delCheckSingleProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];
        $sorting_id = $data_inv['sorting_id'];

        $sql = "delete from oc_x_inventory_check_single_sorting where  inventory_sorting_id = " . $sorting_id;




        if ($query = $dbm->query($sql)) {

            return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }
    }


    public function addVegCheckProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryVegCheck'; //Up
        $warehouse_id = $data_inv['warehouse_id'];

        $date = $data_inv['date'];


        $sql = "select xis.*,lpp.barcode,op.price,op.sku_id from oc_x_inventory_veg_check_sorting as xis left join labelprinter.productlist as lpp on xis.product_id = lpp.product_id left join oc_product as op on op.product_id = xis.product_id where xis.move_flag=0 and xis.uptime > '" . date("Y-m-d",  strtotime($date . " 00:00:00") - 24*3600) . " 12:00:00' and xis.uptime < '" . $date . " 12:00:00'";

        $query = $dbm->query($sql);
        $result = $query->rows;


        $stationProductMove = array();
        $update_sorting_id_arr = array();
        if (sizeof($result)) {
            foreach ($result as $k => $v) {

                if (!empty($v['product_barcode'])) {
                    $product_barcode_arr = array();
                    $product_barcode_arr = json_decode($v['product_barcode']);
                    $product_weight = 0;
                    foreach ($product_barcode_arr as $pbk => $pbv) {
                        $product_weight += (int) substr($pbv, 4, 5);
                    }
                }

                $stationProductMove[] = array(
                    'product_batch' => $v['barcode'],
                    'due_date' => '0000-00-00', //There is a bug till year 2099.
                    'product_id' => $v['product_id'],
                    'special_price' => $v['price'],
                    'qty' => $v['quantity'],
                    'product_weight' => $product_weight,
                    'sku_id' => $v['sku_id']
                    //'qty' => '-'.$v['quantity']
                );


                $update_sorting_id_arr[] = $v['inventory_sorting_id'];
            }

            $data_inv['products'] = $stationProductMove;
        } else {
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }

        if (sizeof($result)) {

            $log->write($data_inv);


            //备份history
            /*
              $dbm->query("START TRANSACTION");
              $sql = "INSERT INTO oc_x_stock_move_item_history(inventory_move_item_id, inventory_move_id, station_id, due_date, product_id, price, product_batch, quantity, weight, weight_class_id, is_gift, checked, status) ";
              $sql .= "SELECT inventory_move_item_id, inventory_move_id, station_id, due_date, product_id, price, product_batch, quantity, weight, weight_class_id, is_gift, checked, status FROM oc_x_inventory_move_item";
              $this->db->query($sql);

              $this->db->query("TRUNCATE oc_x_inventory_move_item");
              $this->db->query("INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`) VALUES('1', '{$date}', '{$time}', '1', '1', '{$date_added}', '{$user_id}', '{$user_name}')");
              $inventory_move_id = $this->db->getLastId();
              foreach($products as $product){
              $this->db->query("INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`) VALUES('{$inventory_move_id}', '1', '{$product['product_id']}', '{$product['quantity']}')");
              }


              $this->db->query("COMMIT");
             */










            $result = $this->addInventoryMoveOrder($data_inv, 1,$warehouse_id);
            if ($result) {
                $update_sorting_id_str = implode(",", $update_sorting_id_arr);
                $sql = "update oc_x_inventory_veg_check_sorting set move_flag = 1 where inventory_sorting_id in (" . $update_sorting_id_str . ")";
                $log->write($sql);
                $query = $dbm->query($sql);
            }
        }





        if ($result) {

            return array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }
    }


    public function addOrderNum($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $db,$dbm;
        global $log;




        $data_inv = json_decode($data, 2);

        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;

        $warehouse_id = isset($data_inv['warehouse_id']) ? $data_inv['warehouse_id'] : false;
        $frame_vg_product = isset($data_inv['frame_vg_product']) ? $data_inv['frame_vg_product'] : false;
        if (!$order_id) {
            return false;
        }

        $sql_so_order = "select oi.order_id   from oc_order o left join oc_order_inv  oi  on  o.order_id = oi.order_id left join oc_x_deliver_order doo on doo.order_id = o.order_id where doo.deliver_order_id  = '".$order_id."' ";

        $query = $db->query($sql_so_order);
        $result_so_order  = $query->row;
        if($result_so_order['order_id'] >0 ){
           $sql = " update oc_order_inv  set inv_comment = '".$data_inv['inv_comment']."' where order_id = '".$result_so_order['order_id']."'";

            $query = $db->query($sql);
        }
        
        if($warehouse_id == 10 ){

            $result = $this->getDeliverOrderInfo($order_id);

            $station_id = $result['station_id'];
            $order_id = $result['order_id'];
            //[add_type=3]: required from admin), [order_status_id=6]: order sorting data submitted, if require not from admin and sorting data submitted, ignore.
            $order_status_id = $result['order_status_id'];
            if ($data_inv['add_type'] != 3 and $order_status_id == 6) {
                return array('status' => 99, 'msg' => "订单已分拣完成，更改周转筐请联系主管。");
            }
            $order_status_id = $result['order_status_id'];
            if($data_inv['add_type'] !=3 and $order_status_id ==6){
                return array('status' => 99, 'msg' => "订单已分拣完成，更改周转筐请联系主管。");
            }


            $data_inv['frame_count'] = $data_inv['frame_count'] ? $data_inv['frame_count'] : 0;
            $data_inv['incubator_count'] = $data_inv['incubator_count'] ? $data_inv['incubator_count'] : 0;
            $data_inv['foam_count'] = $data_inv['foam_count'] ? $data_inv['foam_count'] : 0;
            $data_inv['frame_mi_count'] = $data_inv['frame_mi_count'] ? $data_inv['frame_mi_count'] : 0;
            $data_inv['incubator_mi_count'] = $data_inv['incubator_mi_count'] ? $data_inv['incubator_mi_count'] : 0;
            $data_inv['frame_ice_count'] = $data_inv['frame_ice_count'] ? $data_inv['frame_ice_count'] : 0;
            $data_inv['box_count'] = $data_inv['box_count'] ? $data_inv['box_count'] : 0;
            $data_inv['frame_meat_count'] = $data_inv['frame_meat_count'] ? $data_inv['frame_meat_count'] : 0;
            $data_inv['foam_ice_count'] = $data_inv['foam_ice_count'] ? $data_inv['foam_ice_count'] : 0;

            $date_h = date("H",time());
            if($date_h > 12){
                $order_deliver_date = date("Y-m-d", time()+24*3600);
            }
            else{
                $order_deliver_date = date("Y-m-d", time());
            }

            $data_inv['frame_vg_list'] = $data_inv['frame_vg_list'] ? $data_inv['frame_vg_list'] : '';
            $data_inv['frame_vg_arr'] = !empty($data_inv['frame_vg_list']) ? explode(",", $data_inv['frame_vg_list']) : array();

            $data_inv['frame_meat_list'] = $data_inv['frame_meat_list'] ? $data_inv['frame_meat_list'] : '';
            $data_inv['frame_meat_arr'] = !empty($data_inv['frame_meat_list']) ? explode(",", $data_inv['frame_meat_list']) : array();
            $data_inv['frame_mi_list'] = $data_inv['frame_mi_list'] ? $data_inv['frame_mi_list'] : '';
            $data_inv['frame_mi_arr'] = !empty($data_inv['frame_mi_list']) ? explode(",", $data_inv['frame_mi_list']) : array();
            $data_inv['frame_ice_list'] = $data_inv['frame_ice_list'] ? $data_inv['frame_ice_list'] : '';
            $data_inv['frame_ice_arr'] = !empty($data_inv['frame_ice_list']) ? explode(",", $data_inv['frame_ice_list']) : array();


            if(!empty($data_inv['frame_vg_arr'])){
                if($data_inv['frame_count'] < 1){
                    return array('status' => 17, 'timestamp' => "请输入框子数量");
                }
            }
            if(!empty($data_inv['frame_meat_arr'])){
                if($data_inv['frame_meat_count'] < 1){
                    return array('status' => 17, 'timestamp' => "请输入框子数量");
                }
            }
            if(!empty($data_inv['frame_mi_arr'])){
                if($data_inv['foam_count'] + $data_inv['frame_mi_count'] + $data_inv['incubator_mi_count'] < 1){
                    return array('status' => 17, 'timestamp' => "请输入框子数量");
                }
            }
            if(!empty($data_inv['frame_ice_arr'])){
                if($data_inv['incubator_count'] + $data_inv['frame_ice_count'] + $data_inv['foam_ice_count'] < 1){
                    return array('status' => 17, 'timestamp' => "请输入框子数量");
                }
            }


            //提交框号中有重复
            $all_list_arr = array();
            $all_list_str = '';
            $all_list_arr = array_merge($data_inv['frame_vg_arr'],$data_inv['frame_meat_arr'],$data_inv['frame_mi_arr'],$data_inv['frame_ice_arr']);
            $all_list_arr_unique = array_unique($all_list_arr);



            //判断框号是否存在
            if(!empty($all_list_arr)){
                $all_list_str = implode($all_list_arr, ",");
                $all_list_frame = array();

                $sql = "select * from oc_x_container where container_id in (" . $all_list_str . ")";

                $query = $dbm->query($sql);
                $all_list_frame = $query->rows;
                if(count($all_list_frame) != count($all_list_arr)){
                    return array('status' => 17, 'timestamp' => "不能输入不存在的框号");
                }
            }




            if(!empty($all_list_arr)){
                //return array('status' => 16, 'timestamp' => "未提交框号，请扫描框号");


                if(count($all_list_arr_unique) != count($all_list_arr)){
                    return array('status' => 15, 'timestamp' => "本次提交数据中有重复的框号，请检查");
                }
                $all_list_str = implode($all_list_arr, ",");

                //框子未做入库
                $sql = "select * from oc_x_container where container_id in (" . $all_list_str . ") and instore = 0";

                $query = $dbm->query($sql);
                $no_use_frame = $query->rows;
                $no_use_frame_arr = array();
                if(!empty($no_use_frame)){
                    foreach($no_use_frame as $key=>$value){
                        $no_use_frame_arr[] = $value['container_id'];
                    }
                    $no_use_frame_str = implode(",", $no_use_frame_arr);
                    return array('status' => 12, 'timestamp' => '框号 ' . $no_use_frame_str . " 不能使用，应在商家，框子未做入库操作");
                }

                //框号已作废
                $sql = "select * from oc_x_container where container_id in (" . $all_list_str . ") and status = 0";

                $query = $dbm->query($sql);
                $no_use_frame = $query->rows;
                $no_use_frame_arr = array();
                if(!empty($no_use_frame)){
                    foreach($no_use_frame as $key=>$value){
                        $no_use_frame_arr[] = $value['container_id'];
                    }
                    $no_use_frame_str = implode(",", $no_use_frame_arr);
                    return array('status' => 13, 'timestamp' => '框号 ' . $no_use_frame_str . " 已作废，请重新贴框号或使用其它框子");
                }

            }

            //蔬菜框, 仅对生鲜订单做判断

            if($data_inv['frame_count'] > 0){
                //数量不符

                $frame_vg_count = count($data_inv['frame_vg_arr']);
                if($frame_vg_count != $data_inv['frame_count']){
                    return array('status' => 11, 'timestamp' => $data_inv['timestamp']);
                }

                //今日其它订单已扫
                $frame_other_order = false;
                $frame_order_other_inv = false;
                foreach($data_inv['frame_vg_arr'] as $dik=>$div){

                    $sql = "select oi.order_id from oc_order_inv as oi
                left join oc_order as o on o.order_id = oi.order_id
                where o.deliver_date = '" . $order_deliver_date . "'
                and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%')
                and oi.order_id != " . $order_id;

                    if($station_id == 2){
                        $sql = "select oi.order_id from oc_order_inv as oi
                    left join oc_order as o on o.order_id = oi.order_id
                    where o.deliver_date = (select deliver_date from oc_order where order_id = '".$order_id."')
                    and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%')
                    and oi.order_id != " . $order_id;
                    }

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if(!empty($frame_order_result)){
                        $frame_other_order = $frame_order_result[0]['order_id'];
                        break;
                    }
                    //本订单其他人已扫
                    $sql = "select * from oc_order_inv as oi where ( frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.order_id = " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if(!empty($frame_order_result)){
                        $frame_order_other_inv = true;
                        break;
                    }
                }
                if($frame_other_order){
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被订单号 " . $frame_other_order . " 扫描使用，请检查");
                }
                if($frame_order_other_inv){
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被同订单其他分拣人扫描使用，请主管查看数据确认");
                }

            }


            //肉框

            if($data_inv['frame_meat_count'] > 0){
                //数量不符

                $frame_meat_count = count($data_inv['frame_meat_arr']);
                if($frame_meat_count != $data_inv['frame_meat_count']){
                    return array('status' => 11, 'timestamp' => $data_inv['timestamp']);
                }


                //今日其它订单/本订单其他人已扫
                $frame_other_order = false;
                $frame_order_other_inv = false;
                foreach($data_inv['frame_meat_arr'] as $dik=>$div){


                    $sql = "select oi.order_id from oc_order_inv as oi left join oc_order as o on o.order_id = oi.order_id where o.deliver_date = '" . $order_deliver_date . "' and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.order_id != " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if(!empty($frame_order_result)){
                        $frame_other_order = $frame_order_result[0]['order_id'];
                        break;
                    }
                    //本订单其他人已扫
                    $sql = "select * from oc_order_inv as oi where ( frame_vg_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.order_id = " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if(!empty($frame_order_result)){
                        $frame_order_other_inv = true;
                        break;
                    }
                }
                if($frame_other_order){
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被订单号 " . $frame_other_order . " 扫描使用，请检查");
                }
                if($frame_order_other_inv){
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被同订单其他分拣人扫描使用，请主管查看数据确认");
                }

            }

            //奶框

            if($data_inv['foam_count'] > 0 || $data_inv['frame_mi_count'] > 0 || $data_inv['incubator_mi_count'] > 0){
                //数量不符

                $frame_mi_count = count($data_inv['frame_mi_arr']);

                if($frame_mi_count != $data_inv['foam_count'] + $data_inv['frame_mi_count'] + $data_inv['incubator_mi_count']){
                    return array('status' => 11, 'timestamp' => $data_inv['timestamp']);
                }



                //今日其它订单已扫
                $frame_other_order = false;
                $frame_order_other_inv = false;
                foreach($data_inv['frame_mi_arr'] as $dik=>$div){


                    $sql = "select oi.order_id from oc_order_inv as oi left join oc_order as o on o.order_id = oi.order_id where o.deliver_date = '" . $order_deliver_date . "' and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.order_id != " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if(!empty($frame_order_result)){
                        $frame_other_order = $frame_order_result[0]['order_id'];
                        break;
                    }
                    //本订单其他人已扫
                    $sql = "select * from oc_order_inv as oi where ( frame_meat_list like '%" . $div . "%' or frame_vg_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.order_id = " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if(!empty($frame_order_result)){
                        $frame_order_other_inv = true;
                        break;
                    }
                }
                if($frame_other_order){
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被订单号 " . $frame_other_order . " 扫描使用，请检查");
                }
                if($frame_order_other_inv){
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被同订单其他分拣人扫描使用，请主管查看数据确认");
                }

            }


            //冷冻框

            if($data_inv['incubator_count'] > 0 || $data_inv['frame_ice_count'] > 0 || $data_inv['foam_ice_count'] > 0){
                //数量不符

                $frame_ice_count = count($data_inv['frame_ice_arr']);
                if($frame_ice_count != $data_inv['incubator_count'] + $data_inv['frame_ice_count'] + $data_inv['foam_ice_count']){
                    return array('status' => 11, 'timestamp' => $data_inv['timestamp']);
                }



                //今日其它订单已扫
                $frame_other_order = false;
                $frame_order_other_inv = false;
                foreach($data_inv['frame_ice_arr'] as $dik=>$div){


                    $sql = "select oi.order_id from oc_order_inv as oi left join oc_order as o on o.order_id = oi.order_id where o.deliver_date = '" . $order_deliver_date . "' and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.order_id != " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if(!empty($frame_order_result)){
                        $frame_other_order = $frame_order_result[0]['order_id'];
                        break;
                    }
                    //本订单其他人已扫
                    $sql = "select * from oc_order_inv as oi where ( frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_vg_list like '%" . $div . "%') and oi.order_id = " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if(!empty($frame_order_result)){
                        $frame_order_other_inv = true;
                        break;
                    }
                }
                if($frame_other_order){
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被订单号 " . $frame_other_order . " 扫描使用，请检查");
                }
                if($frame_order_other_inv){
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被同订单其他分拣人扫描使用，请主管查看数据确认");
                }

            }



            $sql = "select * from oc_order_inv where order_id = " . $order_id;

            $query = $dbm->query($sql);
            $order_inv = $query->row;
            if (empty($order_inv)) {
                $sql = "insert into oc_order_inv(order_id,frame_count,incubator_count,inv_comment,inv_status,foam_count,frame_mi_count,incubator_mi_count,frame_ice_count,box_count,foam_ice_count,frame_meat_count,frame_vg_list,frame_meat_list,frame_mi_list,frame_ice_list,uptime) values(" . $order_id . "," . $data_inv['frame_count'] . "," . $data_inv['incubator_count'] . ",'" . $data_inv['inv_comment'] . "',1," . $data_inv['foam_count'] . "," . $data_inv['frame_mi_count'] . ", " . $data_inv['incubator_mi_count'] . " ," . $data_inv['frame_ice_count'] . "," . $data_inv['box_count'] . "," . $data_inv['foam_ice_count'] . "," . $data_inv['frame_meat_count'] . ",'" . $data_inv['frame_vg_list'] . "','" . $data_inv['frame_meat_list'] . "','" . $data_inv['frame_mi_list'] . "','" . $data_inv['frame_ice_list'] . "',now());";
            } else {

                $sql = "update oc_order_inv set ";


//                if ($data_inv['add_type'] == 1) {
//                    $sql .= "frame_count = " . $data_inv['frame_count'] . ",";
//                    $sql .= "inv_comment = '" . $data_inv['inv_comment'] . "',";
//                    $sql .= "frame_vg_list = '" . $data_inv['frame_vg_list'] . "',";
//                }
//                if ($data_inv['add_type'] == 2) {
//                    $sql .= "foam_count = " . $data_inv['foam_count'] . ",";
//                    $sql .= "frame_mi_count = " . $data_inv['frame_mi_count'] . ",";
//                    $sql .= "incubator_mi_count = " . $data_inv['incubator_mi_count'] . ",";
//                    $sql .= "frame_mi_list = '" . $data_inv['frame_mi_list'] . "',";
//                }
                //if ($data_inv['add_type'] == 3) {
                if (1) {
                    $sql .= "frame_count = " . $data_inv['frame_count'] . ",";
                    $sql .= "inv_comment = '" . $data_inv['inv_comment'] . "',";

                    $sql .= "foam_count = " . $data_inv['foam_count'] . ",";
                    $sql .= "incubator_count = " . $data_inv['incubator_count'] . ",";
                    $sql .= "frame_mi_count = " . $data_inv['frame_mi_count'] . ",";
                    $sql .= "incubator_mi_count = " . $data_inv['incubator_mi_count'] . ",";
                    $sql .= "frame_ice_count = " . $data_inv['frame_ice_count'] . ",";
                    $sql .= "box_count = " . $data_inv['box_count'] . ",";
                    $sql .= "foam_ice_count = " . $data_inv['foam_ice_count'] . ",";
                    $sql .= "frame_meat_count = " . $data_inv['frame_meat_count'] . ",";
                    $sql .= "frame_vg_list = '" . $data_inv['frame_vg_list'] . "',";

                    $sql .= "frame_meat_list = '" . $data_inv['frame_meat_list'] . "',";
                    $sql .= "frame_mi_list = '" . $data_inv['frame_mi_list'] . "',";
                    $sql .= "frame_ice_list = '" . $data_inv['frame_ice_list'] . "',";
                }
//                if ($data_inv['add_type'] == 4) {
//                    $sql .= "incubator_count = " . $data_inv['incubator_count'] . ",";
//                    $sql .= "frame_ice_count = " . $data_inv['frame_ice_count'] . ",";
//                    $sql .= "box_count = " . $data_inv['box_count'] . ",";
//                    $sql .= "foam_ice_count = " . $data_inv['foam_ice_count'] . ",";
//
//                    $sql .= "frame_ice_list = '" . $data_inv['frame_ice_list'] . "',";
//                }
//                if ($data_inv['add_type'] == 5) {
//                    $sql .= "frame_meat_count = " . $data_inv['frame_meat_count'] . ",";
//                    $sql .= "frame_meat_list = '" . $data_inv['frame_meat_list'] . "',";
//                }

                //WTF IS THIS!!!, rewrite for fastmoving station
//                if($station_id == 2 ){
//                    $sql = "update oc_order_inv set ";
//                    $sql .= "frame_count = " . $data_inv['frame_count'] . ",";
//                    $sql .= "inv_comment = '" . $data_inv['inv_comment'] . "',";
//                    $sql .= "frame_vg_list = '" . $data_inv['frame_vg_list'] . "',";
//                    $sql .= "box_count = " . $data_inv['box_count'] . ",";
//                }

                $sql .= "uptime = now(),";

                $sql .= "inv_status = 1 ";
                $sql .= " where order_id = " . $order_id;
            }
            $dbm->query($sql);

            //临时方案，再写一遍deliver_order_inv

            $sql = "select * from oc_x_deliver_order_inv where deliver_order_id = " . $data_inv['order_id'];
            $query = $dbm->query($sql);
            $order_inv = $query->row;
            if (empty($order_inv)) {
                $sql = "insert into oc_x_deliver_order_inv(deliver_order_id,frame_count,incubator_count,inv_comment,inv_status,foam_count,frame_mi_count,incubator_mi_count,frame_ice_count,box_count,foam_ice_count,frame_meat_count,frame_vg_list,frame_meat_list,frame_mi_list,frame_ice_list,uptime , order_id) values(" . $data_inv['order_id'] . "," . $data_inv['frame_count'] . "," . $data_inv['incubator_count'] . ",'" . $data_inv['inv_comment'] . "',1," . $data_inv['foam_count'] . "," . $data_inv['frame_mi_count'] . ", " . $data_inv['incubator_mi_count'] . " ," . $data_inv['frame_ice_count'] . "," . $data_inv['box_count'] . "," . $data_inv['foam_ice_count'] . "," . $data_inv['frame_meat_count'] . ",'" . $data_inv['frame_vg_list'] . "','" . $data_inv['frame_meat_list'] . "','" . $data_inv['frame_mi_list'] . "','" . $data_inv['frame_ice_list'] . "',now() ,'" . $result['order_id'] . "');";
            } else {
                $sql = "update oc_x_deliver_order_inv set ";
                $sql .= "frame_count = " . $data_inv['frame_count'] . ",";
                $sql .= "inv_comment = '" . $data_inv['inv_comment'] . "',";
                $sql .= "foam_count = " . $data_inv['foam_count'] . ",";
                $sql .= "incubator_count = " . $data_inv['incubator_count'] . ",";
                $sql .= "frame_mi_count = " . $data_inv['frame_mi_count'] . ",";
                $sql .= "incubator_mi_count = " . $data_inv['incubator_mi_count'] . ",";
                $sql .= "frame_ice_count = " . $data_inv['frame_ice_count'] . ",";
                $sql .= "box_count = " . $data_inv['box_count'] . ",";
                $sql .= "foam_ice_count = " . $data_inv['foam_ice_count'] . ",";
                $sql .= "frame_meat_count = " . $data_inv['frame_meat_count'] . ",";
                $sql .= "frame_vg_list = '" . $data_inv['frame_vg_list'] . "',";
                $sql .= "frame_meat_list = '" . $data_inv['frame_meat_list'] . "',";
                $sql .= "frame_mi_list = '" . $data_inv['frame_mi_list'] . "',";
                $sql .= "frame_ice_list = '" . $data_inv['frame_ice_list'] . "',";
                $sql .= "uptime = now(),";
                $sql .= "inv_status = 1 ";
                $sql .= " where deliver_order_id = " . $data_inv['order_id'];
            }

            if ($dbm->query($sql)) {
                return array('status' => 1, 'timestamp' => $sql);
            } else {
                return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
            }

        }else {


            $result = $this->getDeliverOrderInfo($order_id);

            $station_id = $result['station_id'];

            //[add_type=3]: required from admin), [order_status_id=6]: order sorting data submitted, if require not from admin and sorting data submitted, ignore.
            $order_status_id = $result['order_status_id'];
            if ($data_inv['add_type'] != 3 and $order_status_id == 6) {
                return array('status' => 99, 'msg' => "订单已分拣完成，更改周转筐请联系主管。");
            }


            $data_inv['frame_count'] = $data_inv['frame_count'] ? $data_inv['frame_count'] : 0;
            $data_inv['incubator_count'] = $data_inv['incubator_count'] ? $data_inv['incubator_count'] : 0;
            $data_inv['foam_count'] = $data_inv['foam_count'] ? $data_inv['foam_count'] : 0;
            $data_inv['frame_mi_count'] = $data_inv['frame_mi_count'] ? $data_inv['frame_mi_count'] : 0;
            $data_inv['incubator_mi_count'] = $data_inv['incubator_mi_count'] ? $data_inv['incubator_mi_count'] : 0;
            $data_inv['frame_ice_count'] = $data_inv['frame_ice_count'] ? $data_inv['frame_ice_count'] : 0;
            $data_inv['box_count'] = $data_inv['box_count'] ? $data_inv['box_count'] : 0;
            $data_inv['frame_meat_count'] = $data_inv['frame_meat_count'] ? $data_inv['frame_meat_count'] : 0;
            $data_inv['foam_ice_count'] = $data_inv['foam_ice_count'] ? $data_inv['foam_ice_count'] : 0;

            $date_h = date("H", time());
            if ($date_h > 12) {
                $order_deliver_date = date("Y-m-d", time() + 24 * 3600);
            } else {
                $order_deliver_date = date("Y-m-d", time());
            }

            $data_inv['frame_vg_list'] = $data_inv['frame_vg_list'] ? $data_inv['frame_vg_list'] : '';
            $data_inv['frame_vg_arr'] = !empty($data_inv['frame_vg_list']) ? explode(",", $data_inv['frame_vg_list']) : array();

            $data_inv['frame_meat_list'] = $data_inv['frame_meat_list'] ? $data_inv['frame_meat_list'] : '';
            $data_inv['frame_meat_arr'] = !empty($data_inv['frame_meat_list']) ? explode(",", $data_inv['frame_meat_list']) : array();
            $data_inv['frame_mi_list'] = $data_inv['frame_mi_list'] ? $data_inv['frame_mi_list'] : '';
            $data_inv['frame_mi_arr'] = !empty($data_inv['frame_mi_list']) ? explode(",", $data_inv['frame_mi_list']) : array();
            $data_inv['frame_ice_list'] = $data_inv['frame_ice_list'] ? $data_inv['frame_ice_list'] : '';
            $data_inv['frame_ice_arr'] = !empty($data_inv['frame_ice_list']) ? explode(",", $data_inv['frame_ice_list']) : array();


            if (!empty($data_inv['frame_vg_arr'])) {
                if ($data_inv['frame_count'] < 1) {
                    return array('status' => 17, 'timestamp' => "请输入框子数量");
                }
            }
            if (!empty($data_inv['frame_meat_arr'])) {
                if ($data_inv['frame_meat_count'] < 1) {
                    return array('status' => 17, 'timestamp' => "请输入框子数量");
                }
            }
            if (!empty($data_inv['frame_mi_arr'])) {
                if ($data_inv['foam_count'] + $data_inv['frame_mi_count'] + $data_inv['incubator_mi_count'] < 1) {
                    return array('status' => 17, 'timestamp' => "请输入框子数量");
                }
            }
            if (!empty($data_inv['frame_ice_arr'])) {
                if ($data_inv['incubator_count'] + $data_inv['frame_ice_count'] + $data_inv['foam_ice_count'] < 1) {
                    return array('status' => 17, 'timestamp' => "请输入框子数量");
                }
            }


            //提交框号中有重复
            $all_list_arr = array();
            $all_list_str = '';
            $all_list_arr = array_merge($data_inv['frame_vg_arr'], $data_inv['frame_meat_arr'], $data_inv['frame_mi_arr'], $data_inv['frame_ice_arr']);
            $all_list_arr_unique = array_unique($all_list_arr);


            //判断框号是否存在
            if (!empty($all_list_arr)) {
                $all_list_str = implode($all_list_arr, ",");
                $all_list_frame = array();

                $sql = "select * from oc_x_container where container_id in (" . $all_list_str . ")";

                $query = $dbm->query($sql);
                $all_list_frame = $query->rows;
                if (count($all_list_frame) != count($all_list_arr)) {
                    return array('status' => 17, 'timestamp' => "不能输入不存在的框号");
                }
            }


            if (!empty($all_list_arr)) {
                //return array('status' => 16, 'timestamp' => "未提交框号，请扫描框号");


                if (count($all_list_arr_unique) != count($all_list_arr)) {
                    return array('status' => 15, 'timestamp' => "本次提交数据中有重复的框号，请检查");
                }
                $all_list_str = implode($all_list_arr, ",");

                //框子未做入库
                $sql = "select * from oc_x_container where container_id in (" . $all_list_str . ") and instore = 0";

                $query = $dbm->query($sql);
                $no_use_frame = $query->rows;
                $no_use_frame_arr = array();
                if (!empty($no_use_frame)) {
                    foreach ($no_use_frame as $key => $value) {
                        $no_use_frame_arr[] = $value['container_id'];
                    }
                    $no_use_frame_str = implode(",", $no_use_frame_arr);
                    return array('status' => 12, 'timestamp' => '框号 ' . $no_use_frame_str . " 不能使用，应在商家，框子未做入库操作");
                }

                //框号已作废
                $sql = "select * from oc_x_container where container_id in (" . $all_list_str . ") and status = 0";

                $query = $dbm->query($sql);
                $no_use_frame = $query->rows;
                $no_use_frame_arr = array();
                if (!empty($no_use_frame)) {
                    foreach ($no_use_frame as $key => $value) {
                        $no_use_frame_arr[] = $value['container_id'];
                    }
                    $no_use_frame_str = implode(",", $no_use_frame_arr);
                    return array('status' => 13, 'timestamp' => '框号 ' . $no_use_frame_str . " 已作废，请重新贴框号或使用其它框子");
                }

            }

            //蔬菜框, 仅对生鲜订单做判断

            if ($data_inv['frame_count'] > 0) {
                //数量不符

                $frame_vg_count = count($data_inv['frame_vg_arr']);
                if ($frame_vg_count != $data_inv['frame_count']) {
                    return array('status' => 11, 'timestamp' => $data_inv['timestamp']);
                }

                //今日其它订单已扫
                $frame_other_order = false;
                $frame_order_other_inv = false;
                foreach ($data_inv['frame_vg_arr'] as $dik => $div) {

                    $sql = "select oi.deliver_order_id order_id from oc_x_deliver_order_inv as oi
                left join oc_x_deliver_order as o on o.deliver_order_id = oi.deliver_order_id
                where o.deliver_date = '" . $order_deliver_date . "'
                and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%')
                and oi.deliver_order_id != " . $order_id;

                    if ($station_id == 2) {
                        $sql = "select oi.deliver_order_id from oc_x_deliver_order_inv as oi
                    left join oc_x_deliver_order as o on o.deliver_order_id = oi.deliver_order_id
                    where o.deliver_date = (select deliver_date from oc_x_deliver_order where deliver_order_id = '" . $order_id . "')
                    and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%')
                    and oi.deliver_order_id != " . $order_id;
                    }

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if (!empty($frame_order_result)) {
                        $frame_other_order = $frame_order_result[0]['order_id'];
                        break;
                    }
                    //本订单其他人已扫
                    $sql = "select * from oc_x_deliver_order_inv as oi where ( frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.deliver_order_id = " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if (!empty($frame_order_result)) {
                        $frame_order_other_inv = true;
                        break;
                    }
                }
                if ($frame_other_order) {
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被订单号 " . $frame_other_order . " 扫描使用，请检查");
                }
                if ($frame_order_other_inv) {
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被同订单其他分拣人扫描使用，请主管查看数据确认");
                }

            }


            //肉框

            if ($data_inv['frame_meat_count'] > 0) {
                //数量不符

                $frame_meat_count = count($data_inv['frame_meat_arr']);
                if ($frame_meat_count != $data_inv['frame_meat_count']) {
                    return array('status' => 11, 'timestamp' => $data_inv['timestamp']);
                }


                //今日其它订单/本订单其他人已扫
                $frame_other_order = false;
                $frame_order_other_inv = false;
                foreach ($data_inv['frame_meat_arr'] as $dik => $div) {


                    $sql = "select oi.deliver_order_id order_id from oc_x_deliver_order_inv as oi left join oc_x_deliver_order as o on o.deliver_order_id = oi.deliver_order_id where o.deliver_date = '" . $order_deliver_date . "' and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.deliver_order_id != " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if (!empty($frame_order_result)) {
                        $frame_other_order = $frame_order_result[0]['order_id'];
                        break;
                    }
                    //本订单其他人已扫
                    $sql = "select * from oc_x_deliver_order_inv as oi where ( frame_vg_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.deliver_order_id = " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if (!empty($frame_order_result)) {
                        $frame_order_other_inv = true;
                        break;
                    }
                }
                if ($frame_other_order) {
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被订单号 " . $frame_other_order . " 扫描使用，请检查");
                }
                if ($frame_order_other_inv) {
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被同订单其他分拣人扫描使用，请主管查看数据确认");
                }

            }

            //奶框

            if ($data_inv['foam_count'] > 0 || $data_inv['frame_mi_count'] > 0 || $data_inv['incubator_mi_count'] > 0) {
                //数量不符

                $frame_mi_count = count($data_inv['frame_mi_arr']);

                if ($frame_mi_count != $data_inv['foam_count'] + $data_inv['frame_mi_count'] + $data_inv['incubator_mi_count']) {
                    return array('status' => 11, 'timestamp' => $data_inv['timestamp']);
                }


                //今日其它订单已扫
                $frame_other_order = false;
                $frame_order_other_inv = false;
                foreach ($data_inv['frame_mi_arr'] as $dik => $div) {


                    $sql = "select oi.deliver_order_id order_id from oc_x_deliver_order_inv as oi left join oc_x_deliver_order as o on o.deliver_order_id = oi.deliver_order_id where o.deliver_date = '" . $order_deliver_date . "' and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.deliver_order_id != " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if (!empty($frame_order_result)) {
                        $frame_other_order = $frame_order_result[0]['order_id'];
                        break;
                    }
                    //本订单其他人已扫
                    $sql = "select * from oc_x_deliver_order_inv as oi where ( frame_meat_list like '%" . $div . "%' or frame_vg_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.deliver_order_id = " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if (!empty($frame_order_result)) {
                        $frame_order_other_inv = true;
                        break;
                    }
                }
                if ($frame_other_order) {
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被订单号 " . $frame_other_order . " 扫描使用，请检查");
                }
                if ($frame_order_other_inv) {
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被同订单其他分拣人扫描使用，请主管查看数据确认");
                }

            }


            //冷冻框

            if ($data_inv['incubator_count'] > 0 || $data_inv['frame_ice_count'] > 0 || $data_inv['foam_ice_count'] > 0) {
                //数量不符

                $frame_ice_count = count($data_inv['frame_ice_arr']);
                if ($frame_ice_count != $data_inv['incubator_count'] + $data_inv['frame_ice_count'] + $data_inv['foam_ice_count']) {
                    return array('status' => 11, 'timestamp' => $data_inv['timestamp']);
                }


                //今日其它订单已扫
                $frame_other_order = false;
                $frame_order_other_inv = false;
                foreach ($data_inv['frame_ice_arr'] as $dik => $div) {


                    $sql = "select oi.deliver_order_id from oc_x_deliver_order_inv as oi left join oc_x_deliver_order as o on o.deliver_order_id = oi.deliver_order_id where o.deliver_date = '" . $order_deliver_date . "' and (frame_vg_list like '%" . $div . "%' or frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_ice_list like '%" . $div . "%') and oi.deliver_order_id != " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if (!empty($frame_order_result)) {
                        $frame_other_order = $frame_order_result[0]['order_id'];
                        break;
                    }
                    //本订单其他人已扫
                    $sql = "select * from oc_x_deliver_order_inv as oi where ( frame_meat_list like '%" . $div . "%' or frame_mi_list like '%" . $div . "%' or frame_vg_list like '%" . $div . "%') and oi.deliver_order_id = " . $order_id;

                    $query = $dbm->query($sql);
                    $frame_order_result = $query->rows;
                    if (!empty($frame_order_result)) {
                        $frame_order_other_inv = true;
                        break;
                    }
                }
                if ($frame_other_order) {
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被订单号 " . $frame_other_order . " 扫描使用，请检查");
                }
                if ($frame_order_other_inv) {
                    return array('status' => 14, 'timestamp' => '框号 ' . $div . " 已被同订单其他分拣人扫描使用，请主管查看数据确认");
                }

            }


            $sql = "select * from oc_x_deliver_order_inv where deliver_order_id = " . $data_inv['order_id'];

            $query = $dbm->query($sql);
            $order_inv = $query->row;
            if (empty($order_inv)) {
                $sql = "insert into oc_x_deliver_order_inv(deliver_order_id,frame_count,incubator_count,inv_comment,inv_status,foam_count,frame_mi_count,incubator_mi_count,frame_ice_count,box_count,foam_ice_count,frame_meat_count,frame_vg_list,frame_meat_list,frame_mi_list,frame_ice_list,uptime , order_id) values(" . $data_inv['order_id'] . "," . $data_inv['frame_count'] . "," . $data_inv['incubator_count'] . ",'" . $data_inv['inv_comment'] . "',1," . $data_inv['foam_count'] . "," . $data_inv['frame_mi_count'] . ", " . $data_inv['incubator_mi_count'] . " ," . $data_inv['frame_ice_count'] . "," . $data_inv['box_count'] . "," . $data_inv['foam_ice_count'] . "," . $data_inv['frame_meat_count'] . ",'" . $data_inv['frame_vg_list'] . "','" . $data_inv['frame_meat_list'] . "','" . $data_inv['frame_mi_list'] . "','" . $data_inv['frame_ice_list'] . "',now() ,'" . $result['order_id'] . "');";

            } else {

                $sql = "update oc_x_deliver_order_inv set ";


                if ($data_inv['add_type'] == 1) {
                    $sql .= "frame_count = " . $data_inv['frame_count'] . ",";
                    $sql .= "inv_comment = '" . $data_inv['inv_comment'] . "',";
                    $sql .= "frame_vg_list = '" . $data_inv['frame_vg_list'] . "',";
                }
                if ($data_inv['add_type'] == 2) {
                    $sql .= "foam_count = " . $data_inv['foam_count'] . ",";
                    $sql .= "frame_mi_count = " . $data_inv['frame_mi_count'] . ",";
                    $sql .= "incubator_mi_count = " . $data_inv['incubator_mi_count'] . ",";
                    $sql .= "frame_mi_list = '" . $data_inv['frame_mi_list'] . "',";
                }
                if ($data_inv['add_type'] == 3) {
                    $sql .= "frame_count = " . $data_inv['frame_count'] . ",";
                    $sql .= "inv_comment = '" . $data_inv['inv_comment'] . "',";

                    $sql .= "foam_count = " . $data_inv['foam_count'] . ",";
                    $sql .= "incubator_count = " . $data_inv['incubator_count'] . ",";
                    $sql .= "frame_mi_count = " . $data_inv['frame_mi_count'] . ",";
                    $sql .= "incubator_mi_count = " . $data_inv['incubator_mi_count'] . ",";
                    $sql .= "frame_ice_count = " . $data_inv['frame_ice_count'] . ",";
                    $sql .= "box_count = " . $data_inv['box_count'] . ",";
                    $sql .= "foam_ice_count = " . $data_inv['foam_ice_count'] . ",";
                    $sql .= "frame_meat_count = " . $data_inv['frame_meat_count'] . ",";
                    $sql .= "frame_vg_list = '" . $data_inv['frame_vg_list'] . "',";

                    $sql .= "frame_meat_list = '" . $data_inv['frame_meat_list'] . "',";
                    $sql .= "frame_mi_list = '" . $data_inv['frame_mi_list'] . "',";
                    $sql .= "frame_ice_list = '" . $data_inv['frame_ice_list'] . "',";
                }
                if ($data_inv['add_type'] == 4) {
                    $sql .= "incubator_count = " . $data_inv['incubator_count'] . ",";
                    $sql .= "frame_ice_count = " . $data_inv['frame_ice_count'] . ",";
                    $sql .= "box_count = " . $data_inv['box_count'] . ",";
                    $sql .= "foam_ice_count = " . $data_inv['foam_ice_count'] . ",";

                    $sql .= "frame_ice_list = '" . $data_inv['frame_ice_list'] . "',";
                }
                if ($data_inv['add_type'] == 5) {
                    $sql .= "frame_meat_count = " . $data_inv['frame_meat_count'] . ",";
                    $sql .= "frame_meat_list = '" . $data_inv['frame_meat_list'] . "',";
                }

                //WTF IS THIS!!!, rewrite for fastmoving station
                if ($station_id == 2) {
                    $sql = "update oc_x_deliver_order_inv set ";
                    $sql .= "frame_count = " . $data_inv['frame_count'] . ",";
                    $sql .= "inv_comment = '" . $data_inv['inv_comment'] . "',";
                    $sql .= "frame_vg_list = '" . $data_inv['frame_vg_list'] . "',";
                    $sql .= "box_count = " . $data_inv['box_count'] . ",";
                }

                $sql .= "uptime = now(),";

                $sql .= "inv_status = 1 ";
                $sql .= " where deliver_order_id = " . $data_inv['order_id'];
            }


            if ($dbm->query($sql)) {

                if ($data_inv['warehouse_id'] != 10) {


                    $sql = "delete  from  oc_x_order_container  where deliver_order_id = '" . $data_inv['order_id'] . "'";
                    $dbm->query($sql);

                    $frame_vg_list = isset($data_inv['frame_vg_list']) ? $data_inv['frame_vg_list'] : '';
                    $frame_vg_list_arr = explode(',', $frame_vg_list);



                    $sql_order = " insert into oc_x_order_container (`deliver_order_id` , `container_id` , `date_added` , `added_by` , `warehouse_id` , `status`) VALUES ( '" . $data_inv['order_id'] . "','" . $frame_vg_product . "', NOW(),  '" . $data_inv['add_user_name'] . "',  '" . $data_inv['warehouse_id'] . "' , '1') ";

                    $query = $dbm->query($sql_order);

                    $sql_occupy = " update oc_x_container set occupy = 1 where container_id = '".$frame_vg_product."' ";

                    $query = $dbm->query($sql_occupy);

                    $sql_history = "  insert into oc_x_container_history  (`container_id` , `status`, `type` ,`instore` , `warehouse_id` , `occupy`,`date_added` , `added_by`) (select container_id , status , type , instore ,warehouse_id , occupy , NOW() ,  '".$data_inv['add_user_id']."' from  oc_x_container  where container_id = '".$frame_vg_product ."') ";
                    $query = $dbm->query($sql_history);
//                    $n = 0;
//                    foreach ($frame_vg_list_arr as $container_id) {
//
//                        $sql_order .= "(
//                    '" . $data_inv['order_id'] . "',
//                    '" . $container_id . "',
//                   NOW(),
//                   '" . $data_inv['add_user_name'] . "',
//                   '" . $data_inv['warehouse_id'] . "',
//                    1
//                    )";
//                        if (++$n < sizeof($frame_vg_list_arr)) {
//                            $sql_order .= ', ';
//                        } else {
//                            $sql_order .= ';';
//                        }
//                    }



                }


                return array('status' => 1, 'timestamp' => $sql);
            } else {
                return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
            }
        }
    }


    public function addCheckFrameOut($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);

        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : 0;
        if (!$order_id) {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }

        if(empty($data_inv['add_user_name'])){

            return array('status' => 999, 'timestamp' => $data_inv['timestamp']);
        }






        $frame_list = isset($data_inv['frame_list']) ? $data_inv['frame_list'] : '';


        //判断重复提交
        $sql = "select * from oc_x_container_move as cm left join oc_x_container as c on c.container_id = cm.container_id where c.type in (1,2,3) and cm.order_id = " . $order_id;
        $query = $dbm->query($sql);
        $result = $query->row;
        if(!empty($result)){
            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);
        }

        //确认到货
        $sql = "select station_id,order_status_id,order_deliver_status_id from oc_order where order_id = " . $order_id;
        $query = $dbm->query($sql);
        $result = $query->row;

        //判断订单状态和配送状态
        if($result['order_status_id'] != 6 || $result['order_deliver_status_id'] != 2){

            return array('status' => 5, 'timestamp' => "");
        }
        $stationId = (int)$result['station_id'];

        if($stationId == 2){
            return array('status' => 6, 'timestamp' => "");
//            if($result['order_status_id'] == 6 && $result['order_deliver_status_id'] == 2){
//                //修改配送状态 记录修改历史
//                $sql = "update oc_order SET order_status_id = '10', order_deliver_status_id = '3' where order_id = " . $order_id;
//                $dbm->query($sql);
//
//                //对于鲜奶T+4用户，配送完成且使用了鲜奶优惠券的用户
//                $sql = "update oc_customer set milk_ordered = 1 where customer_id in (select if(datediff(deliver_date, date(date_added))=4, customer_id, 0) customer_id from oc_order where order_status_id not in (3) and order_id = '".$order_id."')";
//                $dbm->query($sql);
//
//                $sql = "INSERT INTO oc_customer_history (customer_id, comment, date_added, added_by)
//                        select customer_id, 'milk_ordered=1', NOW(), '".$data_inv['add_user_name']."' from oc_order where order_id = '".$order_id."' and order_status_id not in (3) and datediff(deliver_date, date(date_added))=4";
//                $dbm->query($sql);
//
//                $sql = "INSERT INTO oc_order_history (`order_id`, `notify`, `comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`)
//                        SELECT '{$order_id}', '0', '物流确认订单到店', NOW(), order_status_id, order_payment_status_id, order_deliver_status_id, " . $data_inv['add_user_name'] . " FROM  oc_order WHERE order_id = {$order_id}";
//                $dbm->query($sql);
//
//                return array('status' => 1, 'timestamp' => "");
//            }
//            else{
//                return array('status' => 5, 'timestamp' => "");
//            }
        }




        //判断提交框号与分拣框号差异
        $sql = "select frame_vg_list,frame_meat_list,frame_mi_list,frame_ice_list from oc_order_inv where order_id = '" . $order_id . "'";

        $query = $dbm->query($sql);
        $result = $query->row;

        if(empty($result)){
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }
        $frame_vg_arr = !empty($result['frame_vg_list']) ? explode(",", $result['frame_vg_list']) : array();
        $frame_meat_arr = !empty($result['frame_meat_list']) ? explode(",", $result['frame_meat_list']) : array();
        $frame_mi_arr = !empty($result['frame_mi_list']) ? explode(",", $result['frame_mi_list']) : array();
        $frame_ice_arr = !empty($result['frame_ice_list']) ? explode(",", $result['frame_ice_list']) : array();

        $all_frame_arr = array_merge($frame_vg_arr,$frame_meat_arr,$frame_mi_arr,$frame_ice_arr);
        $frame_arr = explode(",", $frame_list);

        $all_frame_arr = array_unique($all_frame_arr);
        $frame_arr = array_unique($frame_arr);

        $array_dif = array_diff($all_frame_arr, $frame_arr);


        if(!empty($array_dif)){

            return array('status' => 3, 'timestamp' => $data_inv['timestamp']);
        }

        $array_dif = array_diff($frame_arr, $all_frame_arr);


        if(!empty($array_dif)){

            return array('status' => 3, 'timestamp' => $data_inv['timestamp']);
        }

        //插入frame_log数据
        $sql = "select customer_id from oc_order where order_id = " . $order_id;

        $query = $dbm->query($sql);
        $result_order = $query->row;


        $sql = "SELECT
                    f.container_id,f.type,fl.order_id
                FROM
                    oc_x_container_move as fl
                left join oc_x_container as f on f.container_id = fl.container_id

                WHERE
                    fl.customer_id = " . $result_order['customer_id'] . "
                GROUP BY
                    container_id
                HAVING
                    sum(move_type) = 1
                and f.type = 1 ";

        $query = $dbm->query($sql);
        $result = $query->rows;
        $user_frame_count = count($result);





        $sql = "insert into oc_x_container_move(container_id,order_id,move_type,date_added,customer_id,add_w_user_id,checked) values ";
        foreach ($frame_arr as $key=>$value){
            //修改框子状态
            $u_sql = "update oc_x_container set instore = 0 where container_id = " . $value;
            $query = $dbm->query($u_sql);
            $sql .= "('" . $value . "'," . $order_id . ",1,now()," . $result_order['customer_id'] . ",'" . $data_inv['add_user_name'] . "',0),";
        }
        $sql = substr($sql, 0, strlen($sql) - 1);

        $query = $dbm->query($sql);
        //余额变动
        $sql = "select * from oc_x_container as c left join oc_x_container_type as ct on c.type = ct.type_id where c.container_id in (" . $frame_list . ")";
        $query = $dbm->query($sql);
        $result = $query->rows;

        $frame_1_arr = array();
        $frame_other_arr = array();
        foreach($result as $k=>$v){
            if($v['type'] == 1){
                $frame_1_arr[] = $v;
            }
            else{
                $frame_other_arr[] = $v;
            }
        }



        $frame_1_credits = 0;
        $frame_other_credits = 0;
        $frame_all_credits = 0;
        $noFreeFrameCustomerIdSpot = 9350;
        if($user_frame_count >= 5){
            $frame_1_credits = count($frame_1_arr) * ($frame_1_arr[0]['price'] ? $frame_1_arr[0]['price'] : 0 );
        }
        elseif($user_frame_count + count($frame_1_arr) > 5){
            $frame_1_credits = ($user_frame_count + count($frame_1_arr) - 5) * ($frame_1_arr[0]['price'] ? $frame_1_arr[0]['price'] : 0 );
        }

        foreach($frame_other_arr as $ok=>$ov){
            $frame_other_credits += $ov['price'];
        }
        $frame_all_credits = $frame_1_credits + $frame_other_credits;


        if($frame_all_credits > 0 ){
            $sql = "INSERT INTO oc_customer_transaction SET added_by = '11', customer_id = '" . (int)$result_order['customer_id'] . "', order_id = '" . (int)$order_id . "', description = '收周转筐押金', amount = '-" . $frame_all_credits . "', customer_transaction_type_id = '12', date_added = NOW(),change_id = " . 0 . ", return_id = 0";
            $dbm->query($sql);
        }

        //修改配送状态 记录修改历史, 仅修改生鲜订单
        if($stationId == 1){
            $sql = "update oc_order SET order_status_id = '10', order_deliver_status_id = '3' where order_id = " . $order_id;
            $dbm->query($sql);

            //对于鲜奶T+4用户，配送完成且使用了鲜奶优惠券的用户
            $sql = "update oc_customer set milk_ordered = 1 where customer_id in (select if(datediff(deliver_date, date(date_added))=4, customer_id, 0) customer_id from oc_order where order_status_id not in (3) and order_id = '".$order_id."')";
            $dbm->query($sql);

            $sql = "INSERT INTO oc_customer_history (customer_id, comment, date_added, added_by)
                select customer_id, 'milk_ordered=1', NOW(), '".$data_inv['add_user_name']."' from oc_order where order_id = '".$order_id."' and order_status_id not in (3) and datediff(deliver_date, date(date_added))=4";
            $dbm->query($sql);

            $sql = "INSERT INTO oc_order_history (`order_id`, `notify`, `comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`)
SELECT '{$order_id}', '0', '物流确认到店', NOW(), order_status_id, order_payment_status_id, order_deliver_status_id, " . $data_inv['add_user_name'] . " FROM  oc_order WHERE order_id = {$order_id}";
            $dbm->query($sql);
        }

        return array('status' => 1, 'timestamp' => "");
    }



    public function addCheckFrameCage($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);

        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : 0;
        if (!$order_id) {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }

        if(empty($data_inv['add_user_name'])){

            return array('status' => 999, 'timestamp' => $data_inv['timestamp']);
        }




        $frame_list = isset($data_inv['frame_list']) ? $data_inv['frame_list'] : '';


        //判断重复提交
        $sql = "select * from oc_x_container_move as cm left join oc_x_container as c on c.container_id = cm.container_id where c.type= 4 and cm.order_id = " . $order_id;
        $query = $dbm->query($sql);
        $result = $query->row;
        if(!empty($result)){
            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);
        }


        //判断提交框号与分拣框号差异
        $sql = "select frame_vg_list,frame_meat_list,frame_mi_list,frame_ice_list from oc_order_inv where order_id = '" . $order_id . "'";

        $query = $dbm->query($sql);
        $result = $query->row;

        /*
        if(empty($result)){
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }
         *
         */
        $frame_vg_arr = !empty($result['frame_vg_list']) ? explode(",", $result['frame_vg_list']) : array();
        $frame_meat_arr = !empty($result['frame_meat_list']) ? explode(",", $result['frame_meat_list']) : array();
        $frame_mi_arr = !empty($result['frame_mi_list']) ? explode(",", $result['frame_mi_list']) : array();
        $frame_ice_arr = !empty($result['frame_ice_list']) ? explode(",", $result['frame_ice_list']) : array();

        $all_frame_arr = array_merge($frame_vg_arr,$frame_meat_arr,$frame_mi_arr,$frame_ice_arr);
        $frame_arr = explode(",", $frame_list);

        $all_frame_arr = array_unique($all_frame_arr);
        $frame_arr = array_unique($frame_arr);

        $array_dif = array_diff($all_frame_arr, $frame_arr);

        /*
        if(!empty($array_dif)){

            return array('status' => 3, 'timestamp' => $data_inv['timestamp']);
        }
        */
        $array_dif = array_diff($frame_arr, $all_frame_arr);

        /*
        if(!empty($array_dif)){

            return array('status' => 3, 'timestamp' => $data_inv['timestamp']);
        }

         */

        //插入frame_log数据
        $sql = "select customer_id from oc_order where order_id = " . $order_id;

        $query = $dbm->query($sql);
        $result_order = $query->row;


        $sql = "SELECT
	f.container_id,f.type,fl.order_id
FROM
	oc_x_container_move as fl
left join oc_x_container as f on f.container_id = fl.container_id

WHERE
	fl.customer_id = " . $result_order['customer_id'] . "
GROUP BY
	container_id
HAVING
	sum(move_type) = 1
and f.type = 1 ";

        $query = $dbm->query($sql);
        $result = $query->rows;
        $user_frame_count = count($result);



        //余额变动 判断是不是网笼
        $sql = "select * from oc_x_container as c where c.container_id in (" . $frame_list . ")";
        $query = $dbm->query($sql);
        $result = $query->rows;

        $err_container_id = 0;
        $err_container_id_status = 0;
        $err_container_id_instore = 0;
        foreach($result as $key=>$value){
            if($value['type'] != 4){
                $err_container_id = $value['container_id'];
                break;
            }
            if($value['status'] == 0){
                $err_container_id_status = $value['container_id'];
                break;
            }
            if($value['instore'] == 0){
                $err_container_id_instore = $value['container_id'];
                break;
            }
        }
        if($err_container_id){
            return array('status' => 5, 'timestamp' => "号码 " . $err_container_id . " 不是网笼，请检查");
        }
        if($err_container_id_status){
            return array('status' => 5, 'timestamp' => "号码 " . $err_container_id_status . " 不可用，请检查");
        }
        if($err_container_id_instore){
            return array('status' => 5, 'timestamp' => "号码 " . $err_container_id_instore . " 未做入库操作，请先入库后再使用");
        }


        $sql = "insert into oc_x_container_move(container_id,order_id,move_type,date_added,customer_id,add_w_user_id,checked) values ";
        foreach ($frame_arr as $key=>$value){
            //修改框子状态
            $u_sql = "update oc_x_container set instore = 0 where container_id = " . $value;
            $query = $dbm->query($u_sql);
            $sql .= "('" . $value . "'," . $order_id . ",1,now()," . $result_order['customer_id'] . ",'" . $data_inv['add_user_name'] . "',0),";
        }
        $sql = substr($sql, 0, strlen($sql) - 1);

        $query = $dbm->query($sql);
        //余额变动
        $sql = "select * from oc_x_container as c left join oc_x_container_type as ct on c.type = ct.type_id where c.container_id in (" . $frame_list . ")";
        $query = $dbm->query($sql);
        $result = $query->rows;


        $frame_1_arr = array();
        $frame_other_arr = array();
        foreach($result as $k=>$v){
            if($v['type'] == 1){
                $frame_1_arr[] = $v;
            }
            else{
                $frame_other_arr[] = $v;
            }
        }



        $frame_1_credits = 0;
        $frame_other_credits = 0;
        $frame_all_credits = 0;
        if($user_frame_count >= 5){
            $frame_1_credits = count($frame_1_arr) * ($frame_1_arr[0]['price'] ? $frame_1_arr[0]['price'] : 0 );
        }
        elseif($user_frame_count + count($frame_1_arr) > 5){
            $frame_1_credits = ($user_frame_count + count($frame_1_arr) - 5) * ($frame_1_arr[0]['price'] ? $frame_1_arr[0]['price'] : 0 );
        }

        foreach($frame_other_arr as $ok=>$ov){
            $frame_other_credits += $ov['price'];
        }
        $frame_all_credits = $frame_1_credits + $frame_other_credits;


        if($frame_all_credits > 0 ){
            $sql = "INSERT INTO oc_customer_transaction SET added_by = '11', customer_id = '" . (int)$result_order['customer_id'] . "', order_id = '" . (int)$order_id . "', description = '收周转筐押金', amount = '-" . $frame_all_credits . "', customer_transaction_type_id = '12', date_added = NOW(),change_id = " . 0 . ", return_id = 0";
            $dbm->query($sql);

        }

        return array('status' => 1, 'timestamp' => "");
    }



    public function addCheckFrameCheck($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);

        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : 0;
        if (!$order_id) {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }

        if(empty($data_inv['add_user_name'])){

            return array('status' => 999, 'timestamp' => $data_inv['timestamp']);
        }




        $frame_list = isset($data_inv['frame_list']) ? $data_inv['frame_list'] : '';


        //判断重复提交
        $sql = "select * from oc_x_container_move_check where order_id = " . $order_id;
        $query = $dbm->query($sql);
        $result = $query->row;
        if(!empty($result)){
            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);
        }


        //判断提交框号与分拣框号差异
        $sql = "select frame_vg_list,frame_meat_list,frame_mi_list,frame_ice_list from oc_order_inv where order_id = '" . $order_id . "'";

        $query = $dbm->query($sql);
        $result = $query->row;

        if(empty($result)){
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }
        $frame_vg_arr = !empty($result['frame_vg_list']) ? explode(",", $result['frame_vg_list']) : array();
        $frame_meat_arr = !empty($result['frame_meat_list']) ? explode(",", $result['frame_meat_list']) : array();
        $frame_mi_arr = !empty($result['frame_mi_list']) ? explode(",", $result['frame_mi_list']) : array();
        $frame_ice_arr = !empty($result['frame_ice_list']) ? explode(",", $result['frame_ice_list']) : array();

        $all_frame_arr = array_merge($frame_vg_arr,$frame_meat_arr,$frame_mi_arr,$frame_ice_arr);
        $frame_arr = explode(",", $frame_list);



        $array_dif = array_diff($all_frame_arr, $frame_arr);


        if(!empty($array_dif)){

            return array('status' => 3, 'timestamp' => $data_inv['timestamp']);
        }

        $array_dif = array_diff($frame_arr, $all_frame_arr);


        if(!empty($array_dif)){

            return array('status' => 3, 'timestamp' => $data_inv['timestamp']);
        }

        //插入frame_log数据
        $sql = "select customer_id from oc_order where order_id = " . $order_id;

        $query = $dbm->query($sql);
        $result_order = $query->row;








        $sql = "insert into oc_x_container_move_check(container_id,order_id,move_type,date_added,customer_id,add_w_user_id,checked) values ";
        foreach ($frame_arr as $key=>$value){

            $sql .= "('" . $value . "'," . $order_id . ",1,now()," . $result_order['customer_id'] . ",'" . $data_inv['add_user_name'] . "',0),";
        }
        $sql = substr($sql, 0, strlen($sql) - 1);

        $query = $dbm->query($sql);


        return array('status' => 1, 'timestamp' => $frame_1_credits . "-" . $frame_other_credits . "-" . $frame_all_credits . "-" . $sql);
    }


    public function addContainer(){

        global $dbm;

        $sql = "SELECT
	oi.*
        FROM
                oc_order AS o
        LEFT JOIN oc_x_container_move AS cm ON cm.order_id = o.order_id
        left join oc_order_inv as oi on oi.order_id = o.order_id
        WHERE
                o.order_status_id != 3
            AND o.deliver_date = '2016-05-11'
            and o.station_id = 1
        AND cm.container_move_id IS NULL

        GROUP BY
                o.order_id";

        $query = $dbm->query($sql);
        $result = $query->rows;

        foreach($result as $key=>$value){
            $all_frame_arr = array();
            $all_frame_list = '';
            $data = array();

            $frame_vg_arr = !empty($value['frame_vg_list']) ? explode(",", $value['frame_vg_list']) : array();
            $frame_meat_arr = !empty($value['frame_meat_list']) ? explode(",", $value['frame_meat_list']) : array();
            $frame_mi_arr = !empty($value['frame_mi_list']) ? explode(",", $value['frame_mi_list']) : array();
            $frame_ice_arr = !empty($value['frame_ice_list']) ? explode(",", $value['frame_ice_list']) : array();

            $all_frame_arr = array_merge($frame_vg_arr,$frame_meat_arr,$frame_mi_arr,$frame_ice_arr);

            $all_frame_list = implode(",", $all_frame_arr);

            $data['order_id'] = $value['order_id'];
            $data['frame_list'] = $all_frame_list;
            $data['add_user_name'] = 4;

            $this->addCheckFrameOut(json_encode($data), 1,2,1);
        }




    }


    public function getAddedFrameOut($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;

        $data_inv = json_decode($data, 2);
        $w_user_id = $data_inv['w_user_id'] ? $data_inv['w_user_id'] : 0;

        $date_t = date("Y-m-d", time()) . ' 00:00:00';

        //$date_t = '2016-03-01 00:00:00';

        $sql = "SELECT
	*
FROM
	oc_x_container_move as cm left join oc_x_container as c on cm.container_id = c.container_id
WHERE
	cm.add_w_user_id = " . $w_user_id . "
AND cm.move_type = 1
and c.type in (1,2,3)
and cm.date_added > '" . $date_t . "'
GROUP BY
	cm.order_id ";
        $query = $dbm->query($sql);
        $result = $query->rows;

        return $result;

    }



    public function getAddedFrameCage($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;

        $data_inv = json_decode($data, 2);
        $w_user_id = $data_inv['w_user_id'] ? $data_inv['w_user_id'] : 0;

        $date_t = date("Y-m-d", time()) . ' 00:00:00';

        //$date_t = '2016-03-01 00:00:00';

        $sql = "SELECT
	*
FROM
	oc_x_container_move as cm left join oc_x_container as c on cm.container_id = c.container_id
WHERE
	cm.add_w_user_id = " . $w_user_id . "
AND cm.move_type = 1
and c.type = 4
and cm.date_added > '" . $date_t . "'
GROUP BY
	cm.order_id ";
        $query = $dbm->query($sql);
        $result = $query->rows;

        return $result;

    }


    public function getAddedFrameCheck($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;

        $data_inv = json_decode($data, 2);
        $w_user_id = $data_inv['w_user_id'] ? $data_inv['w_user_id'] : 0;

        $date_t = date("Y-m-d", time()) . ' 00:00:00';

        //$date_t = '2016-03-01 00:00:00';

        $sql = "SELECT
	*
FROM
	oc_x_container_move_check
WHERE
	add_w_user_id = " . $w_user_id . "
AND move_type = 1
and date_added > '" . $date_t . "'
GROUP BY
	order_id ";
        $query = $dbm->query($sql);
        $result = $query->rows;

        return $result;

    }


    public function getAddedFrameIn($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;

        $data_inv = json_decode($data, 2);
        $w_user_id = $data_inv['w_user_id'] ? $data_inv['w_user_id'] : 0;

        $date_t = date("Y-m-d", time()) . ' 00:00:00';

        //$date_t = '2016-03-01 00:00:00';

        $sql = "SELECT
	*
FROM
	oc_x_container_move
WHERE
	add_w_user_id = " . $w_user_id . "
AND move_type = '-1'
and date_added > '" . $date_t . "'
";
        $query = $dbm->query($sql);
        $result = $query->rows;

        return $result;

    }


    public function addCheckFrameIn($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;



        $data_inv = json_decode($data, 2);


        if(empty($data_inv['add_user_name'])){

            return array('status' => 999, 'timestamp' => $data_inv['timestamp']);
        }

        $frame_list = isset($data_inv['frame_list']) ? $data_inv['frame_list'] : '';

        $warehouse_id = isset($data_inv['warehouse_id']) ? $data_inv['warehouse_id'] : '';

        if ($warehouse_id == 10) {

            //判断已回收篮框
            $sql = "select * from oc_x_container where container_id in ( " . $frame_list . ")";
            $query = $dbm->query($sql);
            $result = $query->rows;
            $in_house_frame = false;
            foreach($result as $key=>$value){
                if($value['instore'] == 1){
                    $in_house_frame = $value['container_id'];
                    break;
                }
            }
            if($in_house_frame){
                return array('status' => 0, 'timestamp' => "框号" . $in_house_frame . " 已回收，不能重复回收");
            }


            $frame_arr = array();
            $frame_arr = explode(",", $frame_list);

            $frame_arr = array_unique($frame_arr);

            //插入frame_log数据



            $err_container_id = 0;
            $return_user_credits = array();
            foreach ($frame_arr as $key=>$value){
                $f_sql = "select customer_id,order_id,move_type from oc_x_container_move where container_id = " . $value . " order by container_move_id desc limit 1";
                $f_query = $dbm->query($f_sql);
                $f_result = $f_query->row;

                if($f_result['move_type'] == '-1'){
                    $err_container_id = $value;
                    break;
                }


                //余额变动
                $sql = "SELECT
                    f.container_id,f.type,fl.order_id
                    FROM
                            oc_x_container_move as fl
                    left join oc_x_container as f on f.container_id = fl.container_id

                    WHERE
                            fl.customer_id = " . $f_result['customer_id'] . "
                    GROUP BY
                            container_id
                    HAVING
                            sum(move_type) = 1
                    and f.type = 1 ";

                $query = $dbm->query($sql);
                $result = $query->rows;
                $user_frame_count = count($result);



                $sql = "select * from oc_x_container as c left join oc_x_container_type as ct on c.type = ct.type_id where c.container_id = '" . $value . "'";
                $query = $dbm->query($sql);
                $result = $query->row;

                $frame_credits = 0;
                if($result['type'] == 1){
                    if($user_frame_count >= 6){
                        $frame_credits = $result['price'];
                    }
                } else {
                    $frame_credits = $result['price'];
                }

                if($frame_credits > 0){
                    $return_user_credits[$f_result['customer_id']][$f_result['order_id']] += $frame_credits;
                }





                //修改框子状态
                $u_sql = "update oc_x_container set instore = 1 where container_id = " . $value;
                $query = $dbm->query($u_sql);
                $sql = "insert into oc_x_container_move(container_id,order_id,move_type,date_added,customer_id,add_w_user_id,checked) values ";
                $sql .= "('" . $value . "'," . $f_result['order_id'] . ",'-1',now()," . $f_result['customer_id'] . ",'" . $data_inv['add_user_name'] . "',0)";

                $query = $dbm->query($sql);
            }

            if(!empty($return_user_credits) > 0 ){

                foreach($return_user_credits as $ruck=>$rucv){
                    foreach($rucv as $rucok=>$rucov){
                        $sql = "INSERT INTO oc_customer_transaction SET added_by = '11', customer_id = '" . (int)$ruck . "', order_id = '" . (int)$rucok . "', description = '退周转筐押金', amount = '" . $rucov . "', customer_transaction_type_id = '12', date_added = NOW(),change_id = 0, return_id = 0";

                        $dbm->query($sql);

                    }
                }


            }


            if($err_container_id){

                return array('status' => 5, 'timestamp' => "框号" . $err_container_id . " 找不到出库记录，请联系仓库检查");
            }


        }else {

            $sql = "  insert into oc_x_container_history (`container_id` , `status`, `type` , `instore` , `warehouse_id` , `occupy` , `date_added` , `added_by`) (

      SELECT  container_id , status, type , instore , warehouse_id , occupy ,  NOW() , '". $data_inv['add_user_name'] ."'  FROM  oc_x_container where  container_id = '".$frame_list."'
  )";
            $dbm->query($sql);





            $sql  = " update oc_x_container set  instore = 1 , occupy = 0  , warehouse_id = '".$warehouse_id."' where container_id = '".$frame_list ."'  " ;

            $dbm->query($sql);
        }






        return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
    }



    public function addReturnDeliverProduct($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $db, $dbm;
        global $log;


        $data_inv = json_decode($data, 2);

        $warehouse_id = $data_inv['warehouse_id'];

        if(empty($data_inv['add_user_name'])){
            return array('status' => 999, 'timestamp' => $data_inv['timestamp']);
        }

        $return_product_id_arr = array();
        $return_product_id_str = '';
        foreach($data_inv['products'] as $m){
            $return_product_id_arr[] = $m['product_id'];
        }
        $return_product_id_str = implode(",", $return_product_id_arr);

        //判断是否未回库后退货
        if(!isset($data_inv['isBack'])){
            return false;
        }

        $isBack = (int)$data_inv['isBack'];
        $isRepackMissing = (int)$data_inv['isRepackMissing'];

        //TODO: 判断用户是否已支付，未支付金额和退货金额对比
        $returnDataParam = array(
            'return_reason_id' => 0,
            'return_action_id' => 0,
            'is_back' => $isBack,
            'is_repack_missing' => $isRepackMissing
        );
        $returnProcessErrorMessage = "请检查订单状态和及退货商品信息，出库退货仅限60日内配送［已分拣］且［配送中?］的订单。";
        $returnOrderDeliverStatus = '1,2,3';

        if($isBack){
            $returnDataParam = array(
                'return_reason_id' => 0,
                'return_action_id' => 0,
                'is_back' => $isBack,
                'is_repack_missing' => $isRepackMissing
            );

            $returnProcessErrorMessage = "请检查订单状态和及退货商品信息，回库退货仅限60日内配送，已分拣完成且已配送出库的订单。";
            $returnOrderDeliverStatus = '2,3,7';
        }

        //TODO 判断用户是否全额支付
        //TODO 判断是否实物退货，选择不同操作

        //获取订单分拣出货数量, 出库回库退货时查询条件不同
        $sql = "select
                C.product_id,
                D.name,
                C.price,
                sum(C.quantity*-1) out_qty
                from oc_order A
                left join oc_x_stock_move B on A.order_id = B.order_id
                left join oc_x_stock_move_item C on B.inventory_move_id = C.inventory_move_id
                left join oc_product D on C.product_id = D.product_id
                where A.order_id = '".$data_inv['order_id']."'
                and A.order_status_id in (6,10)
                and A.order_deliver_status_id in (".$returnOrderDeliverStatus.")
                and A.station_id = 2
                and A.deliver_date between date_sub(current_date(), interval 300 day) and date_add(current_date(), interval 1 day)
                and B.inventory_type_id = 12 and C.status = 1
                and C.product_id in (".$return_product_id_str.")
                group by C.product_id";



        //$log->write('退货01[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $sortingResult = $query->rows;

        if(empty($sortingResult)){
            return array('status' => 3, 'timestamp' => "", 'message'=>$returnProcessErrorMessage, 'err_code'=>$sql);
        }

        //获取已退货数量包含了分拣出库差异
        $sql = "select rp.product_id, sum(rp.quantity/rp.box_quantity) qty, sum(if(r.return_reason_id>1,rp.quantity/rp.box_quantity,0)) out_returned_qty
            from oc_return r left join oc_return_product rp on r.return_id = rp.return_id
            where r.return_status_id in (1,2) and r.order_id='".$data_inv['order_id']."' and rp.product_id in (".$return_product_id_str.")
            group by rp.product_id";

        //$log->write('已退货01-1[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $returnedResult = array();
        if($query->num_rows){
            foreach($query->rows as $m){
                $returnedResult[$m['product_id']] = $m;
            }
        }


        //获取已登记退货数量：有效状态且尚未提交
        $sql = "select product_id, sum(quantity/box_quantity) qty
            from oc_return_deliver_product
            where status = 1 and confirmed = 0 and order_id='".$data_inv['order_id']."' and product_id in (".$return_product_id_str.")
            group by product_id";

        //$log->write('退货01-2[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $pendingReturnResult = array();
        if($query->num_rows){
            foreach($query->rows as $m){
                $pendingReturnResult[$m['product_id']] = $m;
            }
        }


        //$log->write('退货02[' . __FUNCTION__ . ']退货信息：'.serialize($data_inv['products'])."\n\r");
        $order_product_arr = array();
        $product_quantity_false = false;
        foreach($sortingResult as $key=>$value){
            //整理商品列表
            $order_product_arr[$value['product_id']] = $value;

            //获取已分拣数量、已退货及已登记未确认的数量
            $validQty = $value['out_qty'];
            $pendindReturnQty = array_key_exists($value['product_id'], $pendingReturnResult) ? $pendingReturnResult[$value['product_id']]['qty'] : 0;
            $ReturnedQty = array_key_exists($value['product_id'], $returnedResult) ? $returnedResult[$value['product_id']]['out_returned_qty'] : 0;
            $validQty -= ($pendindReturnQty + $ReturnedQty);

            //提交的商品数量, 换算散件退货数 数量/整箱数量
            $submitReturnQty = $data_inv['products'][$value['product_id']]['quantity'];
            if($data_inv['products'][$value['product_id']]['box_quantity'] >= 1){
                $submitReturnQty = $submitReturnQty/$data_inv['products'][$value['product_id']]['box_quantity'];
            }
            else{
                $product_quantity_false = $value['product_id'];
                $returnProcessErrorMessage = "退货商品" . $product_quantity_false . " 整箱规格数据异常";
                break;
            }

            if(round($submitReturnQty,4) > round($validQty,4)){
                $product_quantity_false = $value['product_id'];
                $returnProcessErrorMessage = "商品" . $product_quantity_false . " [分拣出库:".$value['out_qty']."，已退:".round($ReturnedQty,2)."，未确认：".round($pendindReturnQty,2)."，可退：".$validQty.", 本次退：".round($submitReturnQty,2)."]，退货数量不可大于分拣出库数量和已退货数量。";
                break;
            }
        }

        //$log->write('退货03[' . __FUNCTION__ . ']订单信息：'.serialize($order_product_arr)."\n\r");
        if(!$product_quantity_false){
            foreach($data_inv['products'] as $m){
                if(!array_key_exists($m['product_id'],$order_product_arr)){
                    $product_quantity_false = $m['product_id'];
                    $returnProcessErrorMessage = "商品" . $product_quantity_false . " 无订单分拣出库记录！";
                    break;
                }
            }
        }

        if($product_quantity_false){
            return array('status' => 3, 'timestamp' => '', 'message' => $returnProcessErrorMessage);
        }

        //插入oc_return_deliver_product数据
        $sql = "insert into oc_return_deliver_product(return_reason_id,return_action_id, is_back, is_repack_missing, order_id,product_id,product,model,quantity,in_part, box_quantity, price,total,add_user_id,date_added,warehouse_id ) values";

        foreach($data_inv['products'] as $m){
            //按提交数据是否整件计算商品金额
            $inPart = ($m['box_quantity']>1) ? 1 : 0;
            $returnProudctPrice = $order_product_arr[$m['product_id']]['price'];
            if($inPart){
                $returnProudctPrice = round($order_product_arr[$m['product_id']]['price']/$m['box_quantity'],2);
            }
            $sql .= "('".$data_inv['return_reason']."','".$returnDataParam['return_action_id']."','".$returnDataParam['is_back']."','". $returnDataParam['is_repack_missing'] ."','" . $data_inv['order_id'] . "','" . $m['product_id'] . "','" . $order_product_arr[$m['product_id']]['name'] . "','','" . $m['quantity'] . "','" . $inPart . "','" . $m['box_quantity'] . "','" . $returnProudctPrice . "','" . $returnProudctPrice*$m['quantity'] . "'," . $data_inv['add_user_name'] . ",now(),'".$warehouse_id."'),";
        }
        if(substr($sql, strlen($sql)-1,1) == ','){
            $sql = substr($sql, 0,-1);
        }

        //$log->write('退货04[' . __FUNCTION__ . ']写入退货中间表：'.$sql."\n\r");
        $dbm->query($sql);

        return array('status' => 1, 'timestamp' => '', 'message' => "商品退货完成");
    }


    // 移库操作
    public  function  submitStockChecksProduct($data, $station_id, $language_id = 2, $origin_id)
    {
        global $db, $dbm;
        global $log;

        $data_inv = json_decode($data, 2);
        $date = date("Y-m-d");
        $warehouse_id = $data_inv['warehouse_id'];
        $changeStockCheckProduct = $data_inv['changeStockCheckProduct'];
        $pallet_number = $data_inv['pallet_number'];
        $warehouse_section_id = $data_inv['warehouse_section_id'];
        $inventory_user_id = $data_inv['inventory_user_id'];
        $add = $data_inv['add'];

        if ($add == 0) {
            $sql_num = " select  stock_section_id  from  oc_x_stock_section  where  name = '" . $pallet_number . "' and warehouse_id = '" . $warehouse_id . "' and  stock_section_type_id = '" . $warehouse_section_id . "' ";

            $query = $dbm->query($sql_num);

            if ($query->num_rows) {
                return array('status' => 2, 'timestamp' => '', 'message' => "此分拣区已提交");
            }
        }




        if ($changeStockCheckProduct == '') {

            if($add == 0 ){
                $sql = " insert into oc_x_stock_checks (`date_added` , `added_by` ,`warehouse_id` , `pallet_number`) VALUES  (NOW() ,'" . $data_inv['add_user_name'] . "','" . $warehouse_id . "' , '" . $pallet_number . "')";

                $query = $db->query($sql);
                $stock_check_id = $db->getLastId();
            }

            if($add == 1 ){
                $sql_section  = " select stock_section_id  from  oc_x_stock_section where  name = '".$pallet_number ."'  ";

                $query = $db->query($sql_section);
//                    $stock_check_id = $db->getLastId();
                $stock_check = $query->row;
                $stock_check_id = $stock_check['stock_section_id'] ;

            }

            $sql = " insert  into  oc_x_stock_checks_item (`stock_check_id` , `product_id` , `quantity`, `date_added` ,`warehouse_id` ,`pallet_number`)  VALUES ";
            foreach ($data_inv['products'] as $m) {

                $sql .= "(  '" . $stock_check_id . "' ,'" . $m['product_id'] . "','" . $m['quantity'] . "', NOW()  , '" . $warehouse_id . "' , '" . $pallet_number . "'),";
            }

            if (substr($sql, strlen($sql) - 1, 1) == ',') {
                $sql = substr($sql, 0, -1);
            }


            $dbm->query($sql);

//                $sql_move = " insert  into  oc_x_stock_section_product_move (`stock_section_id` , `product_id` , `quantity`, `date_added` ,`section_move_type_id` ,`warehouse_id` , `added_by`)  VALUES ";
//                foreach ($data_inv['products'] as $m) {
//
//                    $sql_move .= "(  '".$stock_check_id ."' ,'" . $m['product_id'] . "','" . $m['quantity'] . "', NOW() , '6','".$warehouse_id."' , '".$inventory_user_id."'),";
//                }
//
//                if (substr($sql_move, strlen($sql_move) - 1, 1) == ',') {
//                    $sql_move = substr($sql_move, 0, -1);
//                }
//
//
//                $dbm->query($sql_move);

        } else {
            foreach ($data_inv['products'] as $m) {

                $sql = "update oc_x_stock_checks  set  quantity = '" . $m['quantity'] . "' where  stock_check_id = '" . $changeStockCheckProduct . "'  ";
                $dbm->query($sql);
            }

        }



        return array('status' => 1, 'timestamp' => '', 'message' => "商品盘点提交完成");

    }


    public function addReturnDeliverBadProduct($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $dbm;
        global $log;


        $data_inv = json_decode($data, 2);

        $warehouse_id = $data_inv['warehouse_id'];

        if(empty($data_inv['add_user_name'])){
            return array('status' => 999, 'timestamp' => $data_inv['timestamp']);
        }

        $return_product_id_arr = array();
        $return_product_id_str = '';
        foreach($data_inv['products'] as $m){
            $return_product_id_arr[] = $m['product_id'];
        }
        $return_product_id_str = implode(",", $return_product_id_arr);

        //判断是否未回库后退货
        if(!isset($data_inv['isBack'])){
            return false;
        }

        $isBack = (int)$data_inv['isBack'];
        $isRepackMissing = (int)$data_inv['isRepackMissing'];

        //TODO: 判断用户是否已支付，未支付金额和退货金额对比
        $returnDataParam = array(
            'return_reason_id' => 0,
            'return_action_id' => 0,
            'is_back' => $isBack,
            'is_repack_missing' => $isRepackMissing
        );
        $returnProcessErrorMessage = "请检查订单状态和及退货商品信息，出库退货仅限60日内配送［已分拣］且［配送中?］的订单。";
        $returnOrderDeliverStatus = '1,2,3';

        if($isBack){
            $returnDataParam = array(
                'return_reason_id' => 0,
                'return_action_id' => 0,
                'is_back' => $isBack,
                'is_repack_missing' => $isRepackMissing
            );

            $returnProcessErrorMessage = "请检查订单状态和及退货商品信息，回库退货仅限60日内配送，已分拣完成且已配送出库的订单。";
            $returnOrderDeliverStatus = '2,3,7';
        }

        //TODO 判断用户是否全额支付
        //TODO 判断是否实物退货，选择不同操作

        //获取订单分拣出货数量, 出库回库退货时查询条件不同
        $sql = "select
                C.product_id,
                D.name,
                C.price,
                sum(C.quantity*-1) out_qty
                from oc_order A
                left join oc_x_stock_move B on A.order_id = B.order_id
                left join oc_x_stock_move_item C on B.inventory_move_id = C.inventory_move_id
                left join oc_product D on C.product_id = D.product_id
                where A.order_id = '".$data_inv['order_id']."'
                and A.order_status_id in (6,10)
                and A.order_deliver_status_id in (".$returnOrderDeliverStatus.")
                and A.station_id = 2
                and A.deliver_date between date_sub(current_date(), interval 60 day) and date_add(current_date(), interval 1 day)
                and B.inventory_type_id = 12 and C.status = 1
                and C.product_id in (".$return_product_id_str.")
                group by C.product_id";



        //$log->write('退货01[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $sortingResult = $query->rows;

        if(empty($sortingResult)){
            return array('status' => 3, 'timestamp' => "", 'message'=>$returnProcessErrorMessage, 'err_code'=>$sql);
        }

        //获取已退货数量包含了分拣出库差异
        $sql = "select rp.product_id, sum(rp.quantity/rp.box_quantity) qty, sum(if(r.return_reason_id>1,rp.quantity/rp.box_quantity,0)) out_returned_qty
            from oc_return r left join oc_return_product rp on r.return_id = rp.return_id
            where r.return_status_id in (1,2) and r.order_id='".$data_inv['order_id']."' and rp.product_id in (".$return_product_id_str.")
            group by rp.product_id";

        //$log->write('已退货01-1[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $returnedResult = array();
        if($query->num_rows){
            foreach($query->rows as $m){
                $returnedResult[$m['product_id']] = $m;
            }
        }


        //获取已登记退货数量：有效状态且尚未提交
        $sql = "select product_id, sum(quantity/box_quantity) qty
            from oc_return_deliver_product
            where status = 1  and  confirmed = 1 and order_id='".$data_inv['order_id']."' and product_id in (".$return_product_id_str.")
            group by product_id";

        //$log->write('退货01-2[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $pendingReturnResult = array();
        if($query->num_rows){
            foreach($query->rows as $m){
                $pendingReturnResult[$m['product_id']] = $m;
            }
        }

        $sql = "select product_id, sum(quantity/box_quantity) qty
            from oc_return_deliver_bad_product
            where status = 1  and order_id='".$data_inv['order_id']."' and product_id in (".$return_product_id_str.")
            group by product_id";

        //$log->write('退货01-2[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $badReturnResult = array();
        if($query->num_rows){
            foreach($query->rows as $m){
                $badReturnResult[$m['product_id']] = $m;
            }
        }

        //$log->write('退货02[' . __FUNCTION__ . ']退货信息：'.serialize($data_inv['products'])."\n\r");
        $order_product_arr = array();
        $product_quantity_false = false;
        foreach($sortingResult as $key=>$value){
            //整理商品列表
            $order_product_arr[$value['product_id']] = $value;

            //获取已分拣数量、已退货及已登记未确认的数量
            $validQty = $value['out_qty'];
            $pendindReturnQty = array_key_exists($value['product_id'], $pendingReturnResult) ? $pendingReturnResult[$value['product_id']]['qty'] : 0;
            $badReturnQty = array_key_exists($value['product_id'], $badReturnResult) ? $badReturnResult[$value['product_id']]['qty'] : 0;
            $ReturnedQty = array_key_exists($value['product_id'], $returnedResult) ? $returnedResult[$value['product_id']]['out_returned_qty'] : 0;
//            $validQty -= ($pendindReturnQty + $ReturnedQty);


            //提交的商品数量, 换算散件退货数 数量/整箱数量
            $submitReturnQty = $data_inv['products'][$value['product_id']]['quantity'];
            $validQty =  $badReturnQty +$submitReturnQty ;
            if($data_inv['products'][$value['product_id']]['box_quantity'] >= 1){
                $submitReturnQty = $submitReturnQty/$data_inv['products'][$value['product_id']]['box_quantity'];
            }
            else{
                $product_quantity_false = $value['product_id'];
                $returnProcessErrorMessage = "退货商品" . $product_quantity_false . " 整箱规格数据异常";
                break;
            }


            if($validQty > round($ReturnedQty,2)){
                $product_quantity_false = $value['product_id'];
                $returnProcessErrorMessage = "商品" . $product_quantity_false . " [分拣出库:".$value['out_qty']."，已退:".round($ReturnedQty,2)."，已报损：".$badReturnQty.", 本次报损：".round($submitReturnQty,2)."]，报损数量不可大于退货数量。";
                break;
            }
        }

        //$log->write('退货03[' . __FUNCTION__ . ']订单信息：'.serialize($order_product_arr)."\n\r");
        if(!$product_quantity_false){
            foreach($data_inv['products'] as $m){
                if(!array_key_exists($m['product_id'],$order_product_arr)){
                    $product_quantity_false = $m['product_id'];
                    $returnProcessErrorMessage = "商品" . $product_quantity_false . " 无订单分拣出库记录！";
                    break;
                }
            }
        }

        if($product_quantity_false){
            return array('status' => 3, 'timestamp' => '', 'message' => $returnProcessErrorMessage);
        }

        //插入oc_return_deliver_product数据
        $sql = "insert into oc_return_deliver_bad_product(return_reason_id,return_action_id, is_back, is_repack_missing, order_id,product_id,product,model,quantity,in_part, box_quantity, price,total,add_user_id,date_added,warehouse_id ) values";

        foreach($data_inv['products'] as $m){
            //按提交数据是否整件计算商品金额
            $inPart = ($m['box_quantity']>1) ? 1 : 0;
            $returnProudctPrice = $order_product_arr[$m['product_id']]['price'];
            if($inPart){
                $returnProudctPrice = round($order_product_arr[$m['product_id']]['price']/$m['box_quantity'],2);
            }
            $sql .= "('".$data_inv['return_reason']."','".$returnDataParam['return_action_id']."','".$returnDataParam['is_back']."','". $returnDataParam['is_repack_missing'] ."','" . $data_inv['order_id'] . "','" . $m['product_id'] . "','" . $order_product_arr[$m['product_id']]['name'] . "','','" . $m['quantity'] . "','" . $inPart . "','" . $m['box_quantity'] . "','" . $returnProudctPrice . "','" . $returnProudctPrice*$m['quantity'] . "'," . $data_inv['add_user_name'] . ",now(),'".$warehouse_id."'),";
        }
        if(substr($sql, strlen($sql)-1,1) == ','){
            $sql = substr($sql, 0,-1);
        }

        //$log->write('退货04[' . __FUNCTION__ . ']写入退货中间表：'.$sql."\n\r");
        $dbm->query($sql);

        return array('status' => 1, 'timestamp' => '', 'message' => "商品退货完成");
    }





    // 前台用户退货申请
    public function addReturnDeliverProductData($data) {
        global $db, $dbm;
        global $log;

        $data_inv    = $data['data'];
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : '';

        if(empty($customer_id)){
            return array('status' => 999, 'timestamp' => $data_inv['timestamp'], "message" => "客户信息错误");
        }

        $return_product_id_arr = array();
        foreach($data_inv['products'] as $m){
            $return_product_id_arr[] = $m['product_id'];
        }
        $return_product_id_str = implode(",", $return_product_id_arr);

        //判断是否未回库后退货
        if(!isset($data_inv['isBack'])){
            return false;
        }

        $isBack          = (int)$data_inv['isBack'];
        $isRepackMissing = (int)$data_inv['isRepackMissing'];

        //TODO: 判断用户是否已支付，未支付金额和退货金额对比
        $returnDataParam = array(
            'return_reason_id'  => 0,
            'return_action_id'  => 0,
            'is_back'           => $isBack,
            'is_repack_missing' => $isRepackMissing
        );
        $returnProcessErrorMessage  = "请检查订单状态和及退货商品信息，出库退货仅限7日内配送［已分拣］且［配送中?］的订单。";
        $returnOrderDeliverStatus   = '1,2';

        if($isBack){
            $returnDataParam = array(
                'return_reason_id'  => 0,
                'return_action_id'  => 0,
                'is_back'           => $isBack,
                'is_repack_missing' => $isRepackMissing
            );

            $returnProcessErrorMessage  = "请检查订单状态和及退货商品信息，回库退货仅限7日内配送，已分拣完成且已配送出库的订单。";
            $returnOrderDeliverStatus   = '2,3,7';
        }

        //TODO 判断用户是否全额支付
        //TODO 判断是否实物退货，选择不同操作

        //获取订单分拣出货数量, 出库回库退货时查询条件不同
        $sql = "select
                C.product_id,
                D.name,
                C.price,
                sum(C.quantity*-1) out_qty
                from oc_order A
                left join oc_x_stock_move B on A.order_id = B.order_id
                left join oc_x_stock_move_item C on B.inventory_move_id = C.inventory_move_id
                left join oc_product D on C.product_id = D.product_id
                where A.order_id = '".$data_inv['order_id']."'
                and A.order_status_id in (6,10)
                and A.order_deliver_status_id in (".$returnOrderDeliverStatus.")
                and A.deliver_date between date_sub(current_date(), interval 7 day) and date_add(current_date(), interval 1 day)
                and B.inventory_type_id = 12 and C.status = 1
                and C.product_id in (".$return_product_id_str.")
                group by C.product_id";
        //and A.station_id = '".(int)$data['station_id']."'

        //$log->write('退货01[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $sortingResult = $query->rows;

        if(empty($sortingResult)){
            return array('status' => 3, 'timestamp' => "", 'message'=>$returnProcessErrorMessage, 'err_code'=>$sql);
        }

        //获取已退货数量包含了分拣出库差异
        $sql = "select rp.product_id, sum(rp.quantity/rp.box_quantity) qty
            from oc_return r left join oc_return_product rp on r.return_id = rp.return_id
            where r.return_status_id in (1,2) and r.order_id='".$data_inv['order_id']."' and rp.product_id in (".$return_product_id_str.")
            group by rp.product_id";

        //$log->write('已退货01-1[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $returnedResult = array();
        if($query->num_rows){
            foreach($query->rows as $m){
                $returnedResult[$m['product_id']] = $m;
            }
        }


        //获取已登记退货数量：有效状态且尚未提交
        $sql = "select product_id, sum(quantity/box_quantity) qty
            from oc_return_deliver_product
            where status = 1 and confirmed = 0 and order_id='".$data_inv['order_id']."' and product_id in (".$return_product_id_str.")
            group by product_id";

        //$log->write('退货01-2[' . __FUNCTION__ . ']检查：'.$sql."\n\r");
        $query = $db->query($sql);
        $pendingReturnResult = array();
        if($query->num_rows){
            foreach($query->rows as $m){
                $pendingReturnResult[$m['product_id']] = $m;
            }
        }

        //$log->write('退货02[' . __FUNCTION__ . ']退货信息：'.serialize($data_inv['products'])."\n\r");
        $order_product_arr = array();
        $product_quantity_false = false;
        foreach($sortingResult as $key=>$value){
            //整理商品列表
            $order_product_arr[$value['product_id']] = $value;

            //获取已分拣数量、已退货及已登记未确认的数量
            $validQty         = $value['out_qty'];
            $pendindReturnQty = array_key_exists($value['product_id'], $pendingReturnResult) ? $pendingReturnResult[$value['product_id']]['qty'] : 0;
            $ReturnedQty      = array_key_exists($value['product_id'], $returnedResult)      ? $returnedResult[$value['product_id']]['qty']      : 0;
            $validQty        -= ($pendindReturnQty + $ReturnedQty);

            //提交的商品数量, 换算散件退货数 数量/整箱数量
            $submitReturnQty = $data_inv['products'][$value['product_id']]['quantity'];
            if($data_inv['products'][$value['product_id']]['box_quantity'] >= 1){
                $submitReturnQty = $submitReturnQty/$data_inv['products'][$value['product_id']]['box_quantity'];
            }
            else{
                $product_quantity_false = $value['product_id'];
                $returnProcessErrorMessage = "退货商品<font color='red'>" . $value['name'] . "</font> 整箱规格数据异常";
                break;
            }

            if($submitReturnQty > $validQty){
                $product_quantity_false     = $value['product_id'];
                $returnProcessErrorMessage  = "商品<font color='red'>" . $value['name'] . "</font> [分拣出库:".$value['out_qty']."，已退:".round($ReturnedQty,2)."，未确认：".round($pendindReturnQty,2)."，可退：".$validQty.", 本次退：".round($submitReturnQty,2)."]，退货数量不可大于分拣出库数量和已退货数量。";
                break;
            }
        }

        //$log->write('退货03[' . __FUNCTION__ . ']订单信息：'.serialize($order_product_arr)."\n\r");
        if(!$product_quantity_false){
            foreach($data_inv['products'] as $m){
                if(!array_key_exists($m['product_id'],$order_product_arr)){
                    $product_quantity_false     = $m['product_id'];
                    $returnProcessErrorMessage  = "商品<font color='red'>" . $order_product_arr[$m['product_id']]['name'] . "</font> 无订单分拣出库记录！";
                    break;
                }
            }
        }

        if($product_quantity_false){
            return array('status' => 3, 'timestamp' => '', 'message' => $returnProcessErrorMessage);
        }

        //插入oc_return_deliver_product数据
        $sql = "insert into oc_return_deliver_product(return_reason_id,return_action_id, is_back, is_repack_missing, order_id,product_id,product,model,quantity,in_part, box_quantity, price,total,add_user_id,date_added) values";
        foreach($data_inv['products'] as $m){
            //按提交数据是否整件计算商品金额
            $inPart             = ($m['box_quantity']>1) ? 1 : 0;
            $returnProudctPrice = $order_product_arr[$m['product_id']]['price'];
            if($inPart){
                $returnProudctPrice = round($order_product_arr[$m['product_id']]['price']/$m['box_quantity'],2);
            }
            $sql .= "('".$returnDataParam['return_reason_id']."','".$returnDataParam['return_action_id']."','".$returnDataParam['is_back']."','". $returnDataParam['is_repack_missing'] ."','" . $data_inv['order_id'] . "','" . $m['product_id'] . "','" . $order_product_arr[$m['product_id']]['name'] . "','','" . $m['quantity'] . "','" . $inPart . "','" . $m['box_quantity'] . "','" . $returnProudctPrice . "','" . $returnProudctPrice*$m['quantity'] . "'," . $customer_id . ",now()),";
        }
        if(substr($sql, strlen($sql)-1, 1) == ','){
            $sql = substr($sql, 0, -1);
        }

        //$log->write('退货04[' . __FUNCTION__ . ']写入退货中间表：'.$sql."\n\r");
        $dbm->query($sql);

        return array('status' => 1, 'timestamp' => '', 'message' => "申请退货成功");
    }





    public function getAddedReturnDeliverProduct($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;

        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];
        if(!isset($data_inv['isBack'])){
            return false;
        }
        $isBack = (int)$data_inv['isBack'];
        $isRepackMissing = (int)$data_inv['isRepackMissing'];
        $warehouse_id = $data_inv['warehouse_id'];
        $add_user_id = $data_inv['add_user_name'];
        $product_id = $data_inv['product_id'];
        $shelves_id = $data_inv['isReturnShelves'];
        $user_group_id = $data_inv['user_group_id'];
        if($shelves_id ==0){
            if($user_group_id == 1 || $user_group_id == 22  ){
                $sql = "select
        p.`return_deliver_product_id`,  p.`order_id`,  p.`return_reason_id`,  p.`return_action_id`,  p.`is_back`,  p.`is_repack_missing`,  p.`product_id`,  p.`product`,  p.`model`,  p.`quantity`,  p.`in_part`,  p.`box_quantity`,  p.`price`,  p.`total`,  p.`add_user_id`,  p.`date_added`,  p.`status`,  p.`confirm_user_id`,  p.`date_comfirmed`,  p.`confirmed`,  p.`return_id`
        from oc_return_deliver_product p
        LEFT JOIN oc_order o ON  o.order_id = p.order_id
        where  p.status = 1 and date_format( p.date_added, '%Y-%m-%d') = '" . $date . "'
        and  p.is_back = '". $isBack ."' and  p.is_repack_missing = '".$isRepackMissing."'
        and p.warehouse_id = '". $warehouse_id ."'
          ";

                if($product_id !=''){
                    $sql .= " and p.product_id = '". $product_id ."' ";
                }
                $sql .= " order by  p.date_added desc";
            }else{
                $sql = "select
        p.`return_deliver_product_id`,  p.`order_id`,  p.`return_reason_id`,  p.`return_action_id`,  p.`is_back`,  p.`is_repack_missing`,  p.`product_id`,  p.`product`,  p.`model`,  p.`quantity`,  p.`in_part`,  p.`box_quantity`,  p.`price`,  p.`total`,  p.`add_user_id`,  p.`date_added`,  p.`status`,  p.`confirm_user_id`,  p.`date_comfirmed`,  p.`confirmed`,  p.`return_id`
        from oc_return_deliver_product p
        LEFT JOIN oc_order o ON  o.order_id = p.order_id
        where  p.status = 1 and date_format( p.date_added, '%Y-%m-%d') = '" . $date . "'
        and  p.is_back = '". $isBack ."' and  p.is_repack_missing = '".$isRepackMissing."'
        and p.warehouse_id = '". $warehouse_id ."'
        and p.add_user_id  = '".$add_user_id. "'  ";

                if($product_id !=''){
                    $sql .= " and p.product_id = '". $product_id ."' ";
                }
                $sql .= " order by  p.date_added desc";
            }




            $query = $db->query($sql);
            $result = $query->rows;
        }

        if($shelves_id == 1){
            $sql = " select  (group_concat(r.return_id)) return_id ,rp.product_id , rdp.box_quantity,
              sum(rp.quantity) quantity ,p.repack  ,p.box_size ,rp.price ,rdp.in_part,rdp.box_quantity ,p.name , rp.return_confirmed ,
              (sum(rp.quantity) * rp.price) total ,r.return_confirmed_by
              from  oc_return_deliver_product rdp
               LEFT JOIN oc_return r   ON r.return_id = rdp.return_id and r.return_status_id != 3
LEFT JOIN oc_return_product rp ON r.return_id = rp.return_id 
                LEFT  JOIN  oc_order o ON  o.order_id = r.order_id
                LEFT JOIN  oc_product p ON  rp.product_id  = p.product_id
                where  rdp.status = 1 and date_format( rdp.date_added, '%Y-%m-%d') = '" . $date . "' and  '" . $date . "' >= '2017-10-15'
                and rdp.warehouse_id = '". $warehouse_id ."' and  rdp.is_repack_missing = '".$isRepackMissing."'
                and  rdp.is_back = '". $isBack ."'  and  r.return_reason_id in (2,3,4,5,6)   ";

            if($product_id !=''){
                $sql .= " and rp.product_id = '". $product_id ."' ";
            }

            $sql  .= " group by date(r.return_confirmed_date) ,rp.product_id  , rdp.in_part ,rp.return_confirmed" ;

            $sql .= " order by  rp.product_id ";

            $query = $db->query($sql);
            $result = $query->rows;

        }

        return $result;
    }
    // 盘点
    public function getAddedStcokChecksProduct($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;

        $data_inv = json_decode($data, 2);
        $warehouse_id = $data_inv['warehouse_id'];
        $add_user_id = $data_inv['add_user_name'];
        $pallet_number = $data_inv['pallet_number'];

        $date = $data_inv['date'];
        $sql  = "SELECT
	
	ssp.product_id,
	ssp.quantity,
	p.name,
	ptw.sku_barcode,
	ss.stock_check_id stock_section_id
	
FROM
	oc_x_stock_checks ss
LEFT JOIN oc_x_stock_checks_item  ssp ON ss.stock_check_id = ssp.stock_check_id
LEFT JOIN oc_product p ON ssp.product_id = p.product_id
LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id and  ptw.warehouse_id = '".$warehouse_id."'  WHERE ss.warehouse_id = '".$warehouse_id."' 
         " ;

        if($pallet_number !=''){
            $sql .= " and ss.pallet_number = '".$pallet_number ."' ";
        }


        $query = $db->query($sql);
        $result = $query->rows;

        return $result;
    }

    //报损当面

    public  function  getAddedBadReturnDeliverProduct($data, $station_id, $language_id = 2, $origin_id){
        global $db;
        global $log;

        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];
        if(!isset($data_inv['isBack'])){
            return false;
        }
        $isBack = (int)$data_inv['isBack'];
        $isRepackMissing = (int)$data_inv['isRepackMissing'];
        $warehouse_id = $data_inv['warehouse_id'];
        $add_user_id = $data_inv['add_user_name'];
        $product_id = $data_inv['product_id'];


        $sql = "select
        p.`return_deliver_product_id`,  p.`order_id`,  p.`return_reason_id`,  p.`return_action_id`,  p.`is_back`,  p.`is_repack_missing`,  p.`product_id`,  p.`product`,  p.`model`,  p.`quantity`,  p.`in_part`,  p.`box_quantity`,  p.`price`,  p.`total`,  p.`add_user_id`,  p.`date_added`,  p.`status`,  p.`confirm_user_id`,  p.`date_comfirmed`,  p.`confirmed`,  p.`return_id`
        from oc_return_deliver_bad_product p
        LEFT JOIN oc_order o ON  o.order_id = p.order_id
        where  p.status = 1 and date_format( p.date_added, '%Y-%m-%d') = '" . $date . "'
        and  p.is_back = '". $isBack ."' and  p.is_repack_missing = '".$isRepackMissing."'
        and p.warehouse_id = '". $warehouse_id ."'
         ";

        if($product_id !=''){
            $sql .= " and p.product_id = '". $product_id ."' ";
        }
        $sql .= " order by  p.date_added desc";
        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }

    // 获取用户退货申请
    public function getReturnOrderApplyList($data) {
        global $db;

        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : '';
        if(empty($customer_id)){ return false; }
        $sql = "select
        `return_deliver_product_id`, `order_id`, `product_id`, `product`, `model`, `quantity`, `in_part`, `box_quantity`, `price`, `total`, `date_added`, `status`, `confirmed`
        from oc_return_deliver_product
        where add_user_id = " . $customer_id . "
        order by return_deliver_product_id desc,order_id desc";

        $query  = $db->query($sql);
        $result = $query->rows;

        return $result;
    }

    // 用户取消退货申请
    public function cancelReturnDeliverOrder($data) {
        global $db;

        $customer_id               = isset($data['customer_id'])                       ? (int)$data['customer_id']                       : '';
        $return_deliver_product_id = isset($data['data']['return_deliver_product_id']) ? (int)$data['data']['return_deliver_product_id'] : '';
        if(empty($customer_id) || empty($return_deliver_product_id)){
            return array('status' => 3, 'timestamp' => '', 'message' => "提交数据有误，取消退货失败");
        }
        $sql = "UPDATE oc_return_deliver_product SET status = 0
                WHERE status = 1
                AND confirmed = 0
                AND return_deliver_product_id = ". $return_deliver_product_id ."
                AND add_user_id = " . $customer_id;

        if($db->query($sql)){
            return array('status' => 1, 'timestamp' => '', 'message' => "取消退货完成");
        }
        else{
            return array('status' => 3, 'timestamp' => '', 'message' => "取消退货失败");
        }
    }

    public function disableReturnDeliverProduct($data, $station_id, $language_id = 2, $origin_id) {
        global $dbm;
        global $log;

        $data = json_decode($data, 2);
        if(!isset($data['return_deliver_product_id']) || !$data['return_deliver_product_id'] || !$data['add_user_id']){
            return array('status' => 0, 'timestamp' => '', 'message' => "提交数据有误，取消退货失败");
        }

        $sql = "update oc_return_deliver_product set status = 0 where confirmed = 0 and confirm_user_id = '".(int)$data['confirm_user_id']."' and return_deliver_product_id = '".(int)$data['return_deliver_product_id']."'";
        $log->write('取消退货[' . __FUNCTION__ . ']：'.$sql."\n\r");
        if($dbm->query($sql)){
            return array('status' => 1, 'timestamp' => '', 'message' => "取消退货完成");
        }
        else{
            return array('status' => 0, 'timestamp' => '', 'message' => "取消退货失败");
        }
    }

    public function disableReturnBadDeliverProduct($data, $station_id, $language_id = 2, $origin_id) {
        global $dbm;
        global $log;

        $data = json_decode($data, 2);
        if(!isset($data['return_deliver_product_id']) || !$data['return_deliver_product_id'] || !$data['add_user_id']){
            return array('status' => 0, 'timestamp' => '', 'message' => "提交数据有误，取消退货失败");
        }

        $sql = "update oc_return_deliver_bad_product set status = 0 where confirmed = 0 and confirm_user_id = '".(int)$data['confirm_user_id']."' and return_deliver_product_id = '".(int)$data['return_deliver_product_id']."'";


        if($dbm->query($sql)){
            return array('status' => 1, 'timestamp' => '', 'message' => "取消退货完成");
        }
        else{
            return array('status' => 0, 'timestamp' => '', 'message' => "取消退货失败");
        }
    }

    public function confirmReturnDeliverProduct($data, $station_id, $language_id = 2, $origin_id) {
        global $db, $dbm;
        global $log;

        //TODO 计算订单应收金额
        //TODO 计算缺货金额
        //TODO 应收金额 >= 缺货金额,  仅退货
        //TODO 应收金额 < 缺货金额,  退余额＝缺货金额－应收金额，实际应收为0，退余额待财务确认

        $data = json_decode($data, 2);
        if(!isset($data['isBack']) || !isset($data['add_user_id'])){
            return array('status' => 0, 'message' => "缺少关键参数，请刷新页面重新提交");
        }
        $date = $data['date'];
        $userId = $data['add_user_id'];
        $isBack = (int)$data['isBack'];
        $isRepackMissing = (int)$data['isRepackMissing'];
        $warehouse_id = $data['warehouse_id'];
        //查找指定日期，由本人提交的，有效的且未确认的退货记录
        $sql = "select order_id, sum(total) current_return_total from oc_return_deliver_product
        where status = 1 and confirmed = 0 and date_format(date_added, '%Y-%m-%d') = '" . $date . "' 
        and is_back = '". $isBack ."' and is_repack_missing = '". $isRepackMissing ."' group by order_id";
        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 出库缺货退货'. $sql . "\n\r");
        $query = $db->query($sql);
        $currentReturnInfo = $query->rows;
        $currentReturnInfoList = array();
        $targetOrders = array(0);
        foreach($currentReturnInfo as $m){
            $targetOrders[] = $m['order_id'];
            $currentReturnInfoList[$m['order_id']] = $m;
        }
        $targetOrdersString = implode(',',$targetOrders);

        //查找订单应付
        $sql = "select order_id, sum(value) due_total from oc_order_total where accounting = 1 and order_id in (".$targetOrdersString.") group by order_id";
        $query = $db->query($sql);
        $dueInfo = $query->rows;
        $dueInfoList = array();
        foreach($dueInfo as $m){
            $dueInfoList[$m['order_id']] = $m;
        }

        //查找实际出库数据
        $sql = "select
                O.order_id,
                O.customer_id,
                O.station_id,
                date(O.date_added) order_date,
                O.sub_total,
                sum(C.quantity*C.price*-1) out_total
                from oc_order O
                left join oc_x_stock_move B on O.order_id = B.order_id
                left join oc_x_stock_move_item C on B.inventory_move_id = C.inventory_move_id
                where O.order_id in (".$targetOrdersString.")
                and B.inventory_type_id = 12 and C.status = 1
                group by O.order_id";

        $query = $db->query($sql);
        $outInfo = $query->rows;
        $outInfoList = array();
        foreach($outInfo as $m){
            $outInfoList[$m['order_id']] = $m;
        }

        //查找已退货且退余额数据
        $sql = "
            select R.order_id, sum(R.return_credits) return_total, sum(if(CT.amount is null, 0, CT.amount)) return_credits_total from oc_return R
            left join oc_customer_transaction CT on R.return_id = CT.return_id
            where R.order_id in (".$targetOrdersString.") and R.return_status_id != 3 and R.return_reason_id = 1
            and R.return_action_id = 3
            group by R.order_id
        ";
        $query = $db->query($sql);
        $returnedInfo = $query->rows;
        $returnedInfoList = array();
        foreach($returnedInfo as $m){
            $returnedInfoList[$m['order_id']] = $m;
        }

        //依次处理多个订单
        $issuedOrderId = array();
        foreach($currentReturnInfoList as $m){
            $stationId =  array_key_exists($m['order_id'], $outInfoList) ? $outInfoList[$m['order_id']]['station_id'] : 0;
            $dueTotal = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['due_total'] : 0;
            $subTotal = array_key_exists($m['order_id'], $outInfoList) ? $outInfoList[$m['order_id']]['sub_total'] : 0;
            $outTotal = array_key_exists($m['order_id'], $outInfoList) ? $outInfoList[$m['order_id']]['out_total'] : 0;
            $returnTotal = array_key_exists($m['order_id'], $outInfoList) ? $outInfoList[$m['order_id']]['return_total'] : 0;
            $returnCreditsTotal = array_key_exists($m['order_id'], $outInfoList)? $outInfoList[$m['order_id']]['return_credits_total'] : 0;
            $currentReturnTotal = $currentReturnInfoList[$m['order_id']]['current_return_total'];

            //出库应收=｛实际出库-(小计-应付)｝
            //出库应退＝｛小计-应付-实际出库｝
            //出库缺货应收1=｛出库应收-出库缺货1｝
            //出库缺货应退1={出库缺货1-出库应收}
            $dueOut = $outTotal - ($subTotal-$dueTotal);

            $dueCurrent = $dueOut-$returnTotal-$currentReturnTotal;//本次退货应收 = 出库应收－已退货－本次退货
            $returnCurrent = ($dueCurrent < 0) ? abs($dueCurrent) : 0;//计算退货后后本次应收小于0，退余额

            //判断是否全部退货或退货金额占订单出货80%以上，不退余额
            if($currentReturnTotal >= $outTotal*0.8){
                $returnCurrent = 0;
            }

            //根据是出货退货和是否退余额确定退货操作
            $returnCredits = 0;
            $return_action_id = 1; //操作类型1（无），类型2（退还余额,退货入库），类型3（退还余额），类型4（退货入库）
            if($returnCurrent > 0){
                $return_action_id = $isBack ? 2 : 3;
                $returnCredits = $returnCurrent;
            }
            else{
                $return_action_id = $isBack ? 4 : 1;
                $returnCredits = 0;
            }

            $return_reason_id = $isBack ? 2 : 5; //出库缺货类型5（仓库出库，物流未找到），散件缺货时类型3（仓库出库，客户未收到）, 退货时类型2（客户退货）
            if($isRepackMissing){
                $return_reason_id = 3;
                $return_action_id = ($returnCurrent > 0) ? 3 : 1; //如果是回库散件缺货，不入库，判断是否应退余额
            }

            //For Debug
            $currentReturn = array(
                'dueTotal' => $dueTotal,
                'subTotal' => $subTotal,
                'outTotal' => $outTotal,
                'dueOut' => $dueOut,
                'currentReturnTotal' => $currentReturnTotal,
                'dueCurrnet' => $dueCurrent,
                'returnCredits' => $returnCredits,
                'return_reason_id' => $return_reason_id,
                'return_action_id' => $return_action_id
            );


            $returnAll = $returnTotal + $currentReturnTotal;
            if($returnAll > $outTotal){
                //退货合计超过出库合计，跳过
                $issuedOrderId[] = $m['order_id'];
                continue;
            }

            $dbm->begin();
            $bool = true;

            //写入退货表
            $sql = "INSERT INTO `oc_return` (`order_id`, `customer_id`, `return_reason_id`, `return_action_id`, `return_status_id`, `comment`, `date_ordered`, `date_added`, `date_modified`, `add_user`, `return_credits`, `return_inventory_flag`, `credits_returned`)
                    VALUES('".$m['order_id']."','".$outInfoList[$m['order_id']]['customer_id']."','".$return_reason_id."','".$return_action_id."','2','','".$outInfoList[$m['order_id']]['order_date']."',NOW(),NOW(),'".$userId."','".$returnCredits."','0','0');";

            $bool = $bool && $dbm->query($sql);
            $return_id = $dbm->getLastId();

            $sql = "INSERT INTO `oc_return_product` (`return_id`, `product_id`, `product`,  `quantity`, `in_part`, `box_quantity`, `price`, `total`, `return_product_credits`)
                    SELECT '".$return_id."', `product_id`, `product`,  `quantity`,  `in_part`, `box_quantity`, `price`, `total`,  `total`
                    FROM oc_return_deliver_product
                    WHERE status = 1  AND confirmed = 0 AND order_id = '".$m['order_id']."' AND is_back = '".$isBack."' and is_repack_missing = '". $isRepackMissing ."'";

            $bool = $bool && $dbm->query($sql);


            //完成后更新出库回库记录状态
            $sql = "UPDATE oc_return_deliver_product set confirm_user_id = '".$userId."', date_comfirmed = NOW(), confirmed = 1, return_id='".$return_id."'
                    WHERE status = 1 AND confirmed = 0 AND order_id = '".$m['order_id']."' AND is_back = '".$isBack."' and is_repack_missing = '". $isRepackMissing ."'";
            $bool = $bool && $dbm->query($sql);

            if (!$bool) {
                $log->write('ERR:[' . __FUNCTION__ . ']' . ':  出库缺货开始退货［回滚］' . "\n\r");
                $dbm->rollback();

                return array('status' => 0, 'message' => '确认退货失败');
            } else {
                $log->write('INFO:[' . __FUNCTION__ . ']' . ': 出库缺货开始退货［提交］' . "\n\r");
                $dbm->commit();

                //退货记录完成，开始写入入库数据
                //退货入库操作写库存表，仅操作回库且需要退货入库的订单
                if($return_action_id == 2 || $return_action_id == 4){
                    //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 开始退货入库' . "\n\r");
                    $stockMoveData = array();
                    $stockMoveData['api_method'] = 'inventoryReturn';
                    $stockMoveData['timestamp'] = time();
                    $stockMoveData['from_station_id'] = 0;
                    $stockMoveData['to_station_id'] = $stationId;
                    $stockMoveData['order_id'] = $m['order_id'];
                    $stockMoveData['purchase_order_id'] = 0;
                    $stockMoveData['added_by'] = isset($userId) ? (int)$userId : 0;
                    $stockMoveData['memo'] = '现场退货入库';
                    $stockMoveData['add_user_name'] = '';
                    $stockMoveData['products'] = array();

                    //获取退货的商品列表,需要station_id, product_id, price, quantity, box_quantity
                    $sql = "SELECT '".$stationId."', `product_id`, `price` special_price, `quantity` qty, `box_quantity`
                            FROM oc_return_product WHERE return_id = '".$return_id."'";
                    $query = $db->query($sql);
                    $stockMoveData['products'] = $query->rows;

                    $this->addInventoryMoveOrder($stockMoveData, $stationId,$warehouse_id);

                    //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 退货入库完成' . "\n\r");
                }

                if(sizeof($issuedOrderId)){
                    return array('status' => 2, 'message' => '确认退货成功, 部分订单退货金额有误，请核实['.implode(',',$issuedOrderId).']');
                }
                return array('status' => 1, 'message' => '确认退货成功');
            }
        }
    }



    public function checkFrameCanIn($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;



        $data_inv = json_decode($data, 2);


        $frame_list = isset($data_inv['frame_list']) ? $data_inv['frame_list'] : '';
        $warehouse_id = isset($data_inv['warehouse_id']) ? $data_inv['warehouse_id'] : '';

        if($warehouse_id == 10 ) {


            //判断已回收篮框
            $sql = "select * from oc_x_container where container_id in ( " . $frame_list . ")";
            $query = $dbm->query($sql);
            $result = $query->rows;
            $in_house_frame = false;
            foreach($result as $key=>$value){
                if($value['instore'] == 1){
                    $in_house_frame = $value['container_id'];
                    break;
                }
            }
            if($in_house_frame){
                return array('status' => 0, 'timestamp' => "框号" . $in_house_frame . " 已回收，不能重复回收");
            }


            $frame_arr = array();
            $frame_arr = explode(",", $frame_list);
            //插入frame_log数据



            $err_container_id = 0;
            $return_user_credits = array();
            foreach ($frame_arr as $key=>$value){
                $f_sql = "select customer_id,order_id,move_type from oc_x_container_move where container_id = " . $value . " order by container_move_id desc limit 1";
                $f_query = $dbm->query($f_sql);
                $f_result = $f_query->row;

                if($f_result['move_type'] != '1'){
                    $err_container_id = $value;
                    break;
                }


            }




            if($err_container_id){

                return array('status' => 5, 'timestamp' => "框号" . $err_container_id . " 找不到出库记录，请记录回收框子的商家并联系主管检查数据，");
            }


        }else {
            $sql = "select * from oc_x_container where container_id in ( " . $frame_list . ")";
            $query = $dbm->query($sql);
            $result = $query->rows;
        }



        return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
    }


    public function getSkuProductInfo($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $sku = isset($data_inv['sku']) ? $data_inv['sku'] : false;
        if (!$sku) {
            return false;
        }

        if (strlen($sku) == 18) {
            $sql = "SELECT cd.name ca_name , pd.name, p.status,ptw.sku_barcode, p.box_size, ptw.stock_area inv_class_sort, p.model, pd.special_price as price, p.product_id ,ptw.retail_barcode FROM oc_product AS p LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id LEFT JOIN labelprinter.productlist AS pd ON p.product_id = pd.product_id left join oc_product_to_category ptc on ptc.product_id = p.product_id
left join oc_category c on c.category_id = ptc.category_id
left join oc_category_description cd on cd.category_id = c.category_id WHERE ptw.sku_barcode = '" . $sku . "' and ptw.warehouse_id = '". $data_inv['warehouse_id']."' ";
        }
        elseif(is_numeric($sku) && strlen($sku) <= 6) {
            $sql = "SELECT cd.name ca_name , pd.name, p.status,ptw.sku_barcode, p.box_size,  ptw.stock_area inv_class_sort, p.model, p.price, p.product_id,ptw.retail_barcode FROM oc_product AS p  LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id LEFT JOIN  oc_product_description AS pd ON p.product_id = pd.product_id left join oc_product_to_category ptc on ptc.product_id = p.product_id
left join oc_category c on c.category_id = ptc.category_id
left join oc_category_description cd on cd.category_id = c.category_id  WHERE ptw.product_id = '".$sku."' and ptw.warehouse_id = '". $data_inv['warehouse_id']."' ";
        }
        elseif(is_numeric($sku) && strlen($sku) > 6) {
            $sql = "SELECT cd.name ca_name , pd.name, p.status, ptw.sku_barcode, p.box_size,  ptw.stock_area inv_class_sort, p.model, p.price, p.product_id ,ptw.retail_barcode FROM oc_product AS p  LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id left join oc_product_to_category ptc on ptc.product_id = p.product_id
left join oc_category c on c.category_id = ptc.category_id
left join oc_category_description cd on cd.category_id = c.category_id  WHERE ptw.sku_barcode like '".$sku."%' and ptw.warehouse_id = '". $data_inv['warehouse_id']."' ";
        }
        else{
            $sql = "SELECT cd.name ca_name , pd.name, p.status, ptw.sku_barcode, p.box_size,  ptw.stock_area inv_class_sort, p.model, p.price, p.product_id ,ptw.retail_barcode FROM oc_product AS p LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id left join oc_product_to_category ptc on ptc.product_id = p.product_id
left join oc_category c on c.category_id = ptc.category_id
left join oc_category_description cd on cd.category_id = c.category_id WHERE p.inv_class_sort like '".$sku."%' and  ptw.warehouse_id = '". $data_inv['warehouse_id']."'  ";
        }

//        if (strlen($sku) == 18) {
//            $sql = "SELECT pd.name, p.sku, p.inv_class_sort, p.model, pd.special_price as price, p.product_id FROM oc_product AS p LEFT JOIN labelprinter.productlist AS pd ON p.product_id = pd.product_id WHERE pd.barcode = '" . $sku . "'";
//        }
//        if (strlen($sku) == 13 || strlen($sku) == 14 || strlen($sku) == 8) {
//            $sql = "SELECT pd.name, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id WHERE p.sku like '%" . $sku . "%'";
//        }
//        if (strlen($sku) <=6 ) {
//            $sql = "SELECT pd.name, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id WHERE p.product_id = " . $sku;
//        }

        $query = $db->query($sql);
        $result = $query->row;

        if(!empty($result)){
            $result['price'] = round($result['price'],2);
        }

        return $result;
    }

    //仓库盘点
    public  function  getStockChecksProductInfo($data, $station_id, $language_id = 2, $origin_id){

        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $sku = isset($data_inv['sku']) ? $data_inv['sku'] : false;
        if (!$sku) {
            return false;
        }

        if(is_numeric($sku) && strlen($sku) <= 6) {
            $sql = "SELECT pd.name, p.status,ptw.sku_barcode, p.box_size,  ptw.stock_area inv_class_sort, p.model, p.price, p.product_id,ptw.retail_barcode ,cd.name ca_name  , ws.warehouse_section_id , ws.name section_name
FROM oc_product AS p  LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id LEFT JOIN  oc_product_description AS pd ON p.product_id = pd.product_id LEFT JOIN oc_x_sku s ON   p.sku_id =  s.sku_id LEFT JOIN oc_product_to_category ptc ON ptc.product_id = p.product_id

LEFT join oc_x_sku_category sc on sc.sku_category_id = s.sku_category_id
LEFT join  oc_x_warehouse_section ws on  sc.warehouse_section_id = ws.warehouse_section_id 
LEFT JOIN oc_category c ON c.category_id = ptc.category_id
LEFT JOIN oc_category_description cd ON cd.category_id = c.category_id
 WHERE ptw.product_id = '".$sku."' and ptw.warehouse_id = '". $data_inv['warehouse_id']."' ";

        }
        elseif(is_numeric($sku) && strlen($sku) > 6) {
            $sql = "SELECT pd.name, p.status, ptw.sku_barcode, p.box_size,  ptw.stock_area inv_class_sort, p.model, p.price, p.product_id ,ptw.retail_barcode ,cd.name ca_name ,ws.warehouse_section_id , ws.name  section_name FROM oc_product AS p  LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id LEFT JOIN oc_x_sku s ON   p.sku_id =  s.sku_id LEFT JOIN   oc_product_to_category ptc ON ptc.product_id = p.product_id

LEFT join oc_x_sku_category sc on sc.sku_category_id = s.sku_category_id
LEFT join  oc_x_warehouse_section ws on  sc.warehouse_section_id = ws.warehouse_section_id 
LEFT JOIN oc_category c ON c.category_id = ptc.category_id
LEFT JOIN oc_category_description cd ON cd.category_id = c.category_id WHERE ptw.sku_barcode like '".$sku."%' and ptw.warehouse_id = '". $data_inv['warehouse_id']."' ";
        }

        $query = $db->query($sql);
        $result = $query->row;

        if(!empty($result)){
            $result['price'] = round($result['price'],2);
        }

        return $result;
    }


    public function changeProductSection($data, $station_id, $language_id = 2, $origin_id){
        global $dbm;

        //TODO 移植到后台
        $data = json_decode($data, 2);

        if(isset($data['productSection']) && $data['productSection'] !== ''){
            $sql = "INSERT INTO oc_product_section_history(product_id,inv_class_sort,new_section,added_by,date_added,warehouse_id)
                SELECT product_id, stock_area, '".$data['productSection']."', '".$data['inventory_user']."', NOW() ,warehouse_id FROM oc_product_to_warehouse
                WHERE product_id = '".$data['productId']."' and warehouse_id = '".$data['warehouse_id']."'";
            $query = $dbm->query($sql);

            $sql = "UPDATE oc_product_to_warehouse SET stock_area = '".$data['productSection']."' WHERE product_id = '".$data['productId']."' and warehouse_id = '".$data['warehouse_id']."'";

            $query = $dbm->query($sql);

            return $query;
        }

        if(isset($data['productBarCode']) && $data['productBarCode'] !== ''){
            $sql = "UPDATE oc_product_to_warehouse SET retail_barcode = '".$data['productBarCode']."' WHERE product_id = '".$data['productId']."' and warehouse_id = '".$data['warehouse_id']."'";

            $query = $dbm->query($sql);

            return $query;
        }

        return false;
    }

    public function getProductSectionInfo($data, $station_id, $language_id = 2, $origin_id){
        global $db;

        $data = json_decode($data, 2);
        $productSection = isset($data['productSection']) ? $data['productSection'] : false;
        $productSectionInfo = array();

        if($productSection){
            $sql = "SELECT p.product_id, pd.name, p.repack, p.status, ptw.sku_barcode sku, ptw.stock_area inv_class_sort, p.model
                FROM oc_product AS p
                LEFT JOIN oc_product_to_warehouse  AS ptw ON ptw.product_id = p.product_id
                LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id
                WHERE ptw.warehouse_id = '".$data['warehouse_id']."' and  ptw.stock_area like '".$productSection."%'
                ORDER BY p.inv_class_sort DESC";
            $query = $db->query($sql);
            $productList = array();
            foreach($query->rows as $m){
                $productList[] = $m['product_id'];
            }
            $productStockInfo = $this->getProductStockInfo($productList);

            foreach($query->rows as $m){
                $productSectionInfo[$m['product_id']] = $m;
                $productSectionInfo[$m['product_id']]['qty'] = 0;
                if(array_key_exists($m['product_id'], $productStockInfo)){
                    $productSectionInfo[$m['product_id']]['qty'] = $productStockInfo[$m['product_id']];
                }
            }
        }
        if(!sizeof($productSectionInfo)){
            return false;
        }
        return $productSectionInfo;
    }

    public function getProductStockInfo($productList){
        global $db;

        $maxInvInitId = false;
        $returnData = array();
        if(sizeof($productList)){
            $sql = "select max(inventory_move_id) maxid from oc_x_stock_move where inventory_type_id = 14";
            $query = $db->query($sql);
            $result = $query->row;
            $maxInvInitId = $result['maxid'];

            if($maxInvInitId){
                $sql = "select
                    MI.product_id,
                    sum(MI.quantity) qty
                    from oc_x_stock_move M
                    left join oc_x_stock_move_item MI on M.inventory_move_id = MI.inventory_move_id
                    left join oc_product P on P.product_id = MI.product_id
                    where M.inventory_move_id >= '".$maxInvInitId."' and MI.product_id in (".implode(',',$productList).")
                    group by P.product_id";
                $query = $db->query($sql);

                foreach($query->rows as $m){
                    $returnData[$m['product_id']] = $m['qty'];
                }
            }
        }
        return $returnData;
    }

    public function getSkuProductInfoInv($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;

        set_time_limit(0);
        $data_inv = json_decode($data, 2);

        $sku = isset($data_inv['sku']) ? $data_inv['sku'] : false;
        $warehouse_id = isset($data_inv['warehouse_id']) ? (int)$data_inv['warehouse_id'] : false;

        if (!$sku || !$warehouse_id) {
            return false;
        }


        $product_inv = array();
        $product_inv['quantity'] = 0;
        $product_inv['status'] = 1;



        $uptime = date("Y-m-d 00:00:00", time());
        if (strlen($sku) == 18) {
            $sql2 = "
            select * from oc_x_inventory_check_single_sorting as ics
            left join oc_product as p on ics.product_id = p.product_id
            left join labelprinter.productlist AS pd ON p.product_id = pd.product_id
            where ics.warehouse_id = '".$warehouse_id."' and ics.uptime > '" . $uptime . "' and pd.barcode = '" . $sku . "'";

            $sql = "SELECT p.product_id FROM oc_product AS p
            LEFT JOIN labelprinter.productlist AS pd ON p.product_id = pd.product_id
            WHERE pd.barcode = '" . $sku . "'";
        }

        if (strlen($sku) > 6 && strlen($sku) <18) {
            $sql2 = "
            select * from oc_x_inventory_check_single_sorting as ics
            left join oc_product_to_warehouse as p on ics.product_id = p.product_id
            where ics.warehouse_id = '".$warehouse_id."' and ics.uptime > '" . $uptime . "' and p.sku_barcode like '%" . $sku . "%'";

            $sql = "
            SELECT p.product_id FROM oc_product AS p
            LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id
            LEFT JOIN oc_product_to_warehouse pw on p.product_id = pw.product_id and pw.warehouse_id = '".$warehouse_id."'
            WHERE pw.sku_barcode like '%" . $sku . "%'";
        }
        else{
            $sql2 = "
            select * from oc_x_inventory_check_single_sorting as ics
            left join oc_product as p on ics.product_id = p.product_id
            where ics.warehouse_id = '".$warehouse_id."' and ics.uptime > '" . $uptime . "' and p.product_id = " . $sku;

            $sql = "SELECT p.product_id FROM oc_product AS p
            LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id
            WHERE p.product_id = " . $sku;
        }


        $query2 = $db->query($sql2);
        if(!empty($query2->rows)){
            $product_inv['status'] = 0;
        }


        $query = $db->query($sql);
        $result = $query->row;

        $product_id = $result['product_id'];

        $select_inv_product_id_arr = array($product_id);

        //获取商品促销关系
        $sql = "select * from oc_product_to_promotion_product where promotion_product_id = " . $product_id;
        $product_to_promotion_arr = array();
        $query = $db->query($sql);
        $result = $query->rows;

        if(!empty($result)){
            foreach($result as $key => $value){
                $select_inv_product_id_arr[] = $value['product_id'];
                $product_to_promotion_arr[$value['product_id']] = $value['promotion_product_id'];
            }
        }
        $select_inv_product_id_str = implode(",", $select_inv_product_id_arr);


        //商品库存
        $inventory_product_move_arr = array();
        $sql = "select inventory_move_id,date_added from oc_x_stock_move where inventory_type_id = 14 and warehouse_id = '".$warehouse_id."' order by inventory_move_id desc limit 1";

        $query = $db->query($sql);
        $inventory_check = $query->row;

        //TODO处理生鲜warehouse_id为0问题，20170730
        $warehouseIds = $warehouse_id == 0 ? '0,10' : $warehouse_id;

        $inventory_check_id = $inventory_check['inventory_move_id'];
        $inventory_check_time = $inventory_check['date_added'];
        if($inventory_check_id){
            $sql = "SELECT
                    xsm.inventory_move_id,
                    xsm.inventory_type_id,
                    smi.product_id,
                    round(sum(smi.quantity/smi.box_quantity),2) as product_move_type_quantity,
                    pd.name
            FROM
                    oc_x_stock_move AS xsm
            left JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id
            left join oc_product as p on p.product_id = smi.product_id
            left join oc_product_to_category as ptc on ptc.product_id = smi.product_id
            left join oc_product_description as pd on pd.product_id=smi.product_id and pd.language_id = 2
            WHERE
                    xsm.inventory_move_id >= " . $inventory_check_id . "
            and xsm.date_added >= '" . $inventory_check_time . "'
            and smi.product_id in (" . $select_inv_product_id_str . ")
            and xsm.warehouse_id in (".$warehouseIds.")
            group by xsm.inventory_type_id,smi.product_id";

            $query = $db->query($sql);
            $inventory_arr = $query->rows;
            $inventory_product_move_arr = array();
            foreach($inventory_arr as $key=>$value){
                $inventory_product_move_arr[$value['product_id']]['quantity'][$value['inventory_type_id']] = $value['product_move_type_quantity'];
                $inventory_product_move_arr[$value['product_id']]['name'] = $value['name'];
                $inventory_product_move_arr[$value['product_id']]['sum_quantity'] = 0;
                $inventory_product_move_arr[$value['product_id']]['date_added'] = $inventory_check['date_added'];
            }

            $data = array();
            $data['inv_mi_cold_arr'] = $inventory_product_move_arr;

            if(!empty($data['inv_mi_cold_arr'])){


                foreach($data['inv_mi_cold_arr'] as $key=>$value){
                    foreach($value['quantity'] as $k=>$v){

                        if($k == 15 && $product_to_promotion_arr[$key]){
                            if(isset($data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]])){
                                $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['quantity']['15'] = abs($v);
                            }
                            else{
                                $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]] = $value;
                                $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['quantity'] = array();
                                //$data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['name'] .= "(促销品)";
                                $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['name'] =  $this->model_report_sale->getProductName($product_to_promotion_arr[$key]);
                                $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['sum_quantity'] = 0;
                                $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['date_added'] = $value['date_added'];
                                $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['quantity']['15'] = abs($v);
                            }


                        }

                    }
                }


                foreach($data['inv_mi_cold_arr'] as $key1=>$value1){

                    foreach($value1['quantity'] as $k1=>$v1){
                        $data['inv_mi_cold_arr'][$key1]['sum_quantity'] += $v1;
                    }
                    if($key1 == $product_id){
                        $product_inv['quantity'] = $data['inv_mi_cold_arr'][$key1]['sum_quantity'];
                    }
                }



            }
            else{
                $product_inv['quantity'] = 0;
            }



        }
        else{
            $product_inv['quantity'] = 0;
        }

        //获取商品库存近期变动
        $sql = "select concat(C.name) inv_type,
                if(A.inventory_type_id=14, concat(date(A.date_added),'_00_',A.inventory_move_id,'_', A.inventory_type_id),  concat(date(A.date_added), '_01_',A.inventory_type_id)) inventory_type_id,
                date(A.date_added) adate,
                round(sum(B.quantity/B.box_quantity),2) qty
                from oc_x_stock_move A
                left join oc_x_stock_move_item B on A.inventory_move_id = B.inventory_move_id
                left join oc_product P on B.product_id = P.product_id
                left join oc_x_inventory_type C on A.inventory_type_id = C.inventory_type_id
                where B.status = 1
                and A.date_added >= ( select min(date_added) from oc_x_stock_move where warehouse_id = '".$warehouse_id."' and inventory_type_id = 14 and date_added >= date_sub(current_date(), interval 3 day))  -- 最早盘点时间
                and P.station_id = 2 and B.product_id = '".$product_id."'
                and A.warehouse_id in (".$warehouseIds.")
                group by inventory_type_id
                order by inventory_type_id
                ";
        $query = $db->query($sql);
        $inventoryInfo = $query->rows;
        $product_inv['inventoryInfo'] = $inventoryInfo;

        //获取当分拣占用但未提交的商品
        $sql = "
            select sum(a.quantity) sort_qty from oc_x_inventory_order_sorting a
            left join oc_order o on a.order_id = o.order_id
            left join oc_x_stock_move sm on sm.order_id = o.order_id 
            where a.product_id = '".$product_id."' and a.move_flag = 0 and o.warehouse_id = '".$warehouse_id."' and o.order_status_id !=3 and o.order_deliver_status_id  = 1  and sm.inventory_move_id is null   and a.status = 1 ";
        $query = $db->query($sql);
        $sortInfo = $query->row;
        $product_inv['sortQty'] = isset($sortInfo['sort_qty']) ? $sortInfo['sort_qty'] : 0;

        $sql_cancel = " SELECT
 o.order_id  , 
sum(smi.quantity) cancel_quantity
FROM
	oc_order o
LEFT JOIN oc_order_product op ON o.order_id = op.order_id
LEFT JOIN oc_x_stock_move sm ON sm.order_id = o.order_id
LEFT JOIN oc_x_stock_move_item smi on sm.inventory_move_id = smi.inventory_move_id and smi.product_id = op.product_id 
WHERE
	o.order_status_id = 3
AND sm.inventory_move_id IS NOT NULL
AND op.product_id = '".$product_id."'
AND o.warehouse_id = '".$warehouse_id."'
and sm.inventory_type_id = 12 
AND DATE(o.date_added) BETWEEN date_sub(
	CURRENT_DATE (),
	INTERVAL 3 DAY
)
AND CURRENT_DATE () ";

        $query = $db->query($sql_cancel);
        $result_cancel = $query->row;
        $product_inv['cancelQuantity'] = isset($result_cancel['cancel_quantity']) ? abs($result_cancel['cancel_quantity']) : 0;


        return $product_inv;
    }


    public function getInventoryIn($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];


        $sql = "SELECT smi.*, SUM(smi.quantity) as sum_quantity, p. NAME FROM oc_x_stock_move_item AS smi LEFT JOIN oc_x_stock_move AS sm ON sm.inventory_move_id = smi.inventory_move_id LEFT JOIN oc_product_description AS p ON p.product_id = smi.product_id WHERE sm.inventory_type_id = 11 AND date_format(date_added, '%Y-%m-%d') = '" . $date . "' GROUP BY smi.product_id";




        $query = $db->query($sql);
        $result = $query->rows;
        if(!empty($result)){
            foreach($result as $key=>$value){
                $value['product_batch'] = trim($value['product_batch']);
                $result[$key]['product_batch'] = str_replace(" ","",$value['product_batch']);
                $result[$key]['price'] = round($value['price'],2);
            }
        }
        return $result;
    }

    public function inventory_login($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];
        $username = $data_inv['username'] ? $data_inv['username'] : '';
        $password = $data_inv['password'] ? $data_inv['password'] : '';
        $warehouse_id = $data_inv['warehouse_id'] ? $data_inv['warehouse_id'] : '';

        $user_query = $db->query("SELECT wu.username,wu.user_id,wu.status,wu.warehouse_id,w.is_dc, w.title,wu.user_group_id ,w.repack warehouse_repack , wu.repack user_repack ,wu.to_warehouse_id  FROM " . DB_PREFIX . "w_user wu left join oc_x_warehouse w on wu.warehouse_id = w.warehouse_id WHERE wu.username = '" . $db->escape($username) . "' AND (wu.password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $db->escape($password) . "'))))) OR wu.password = '" . $db->escape(md5($password)) . "') AND wu.status = '1' and wu.warehouse_id = '". $warehouse_id ."'");



        if ($user_query->num_rows) {
            $return['status'] = 2;
            $return['user'] = $user_query->row;
            return $return;
        }
        else{
            $return['status'] = 1;
            return $return;
        }
    }


    public function getInventoryAdjust($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];


        $sql = "SELECT smi.*, SUM(smi.quantity) as sum_quantity, p. NAME FROM oc_x_stock_move_item AS smi LEFT JOIN oc_x_stock_move AS sm ON sm.inventory_move_id = smi.inventory_move_id LEFT JOIN oc_product_description AS p ON p.product_id = smi.product_id WHERE sm.inventory_type_id = 16 AND date_format(date_added, '%Y-%m-%d') = '" . $date . "' GROUP BY smi.product_id";




        $query = $db->query($sql);
        $result = $query->rows;
        if(!empty($result)){
            foreach($result as $key=>$value){
                $value['product_batch'] = trim($value['product_batch']);
                $result[$key]['product_batch'] = str_replace(" ","",$value['product_batch']);
                $result[$key]['price'] = round($value['price'],2);
            }
        }
        return $result;
    }

    public function getInventoryOut($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];


        $sql = "SELECT smi.*, SUM(smi.quantity) as sum_quantity, p. NAME FROM oc_x_stock_move_item AS smi LEFT JOIN oc_x_stock_move AS sm ON sm.inventory_move_id = smi.inventory_move_id LEFT JOIN oc_product_description AS p ON p.product_id = smi.product_id WHERE sm.inventory_type_id = 13 AND date_format(date_added, '%Y-%m-%d') = '" . $date . "' GROUP BY smi.product_id";




        $query = $db->query($sql);
        $result = $query->rows;
        if(!empty($result)){
            foreach($result as $key=>$value){
                $value['product_batch'] = trim($value['product_batch']);
                $result[$key]['product_batch'] = str_replace(" ","",$value['product_batch']);
                $result[$key]['price'] = round($value['price'],2);
            }
        }
        return $result;
    }

    public function getInventoryChange($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];


        $sql = "SELECT smi.*, SUM(smi.quantity) as sum_quantity, p. NAME FROM oc_x_stock_move_item AS smi LEFT JOIN oc_x_stock_move AS sm ON sm.inventory_move_id = smi.inventory_move_id LEFT JOIN oc_product_description AS p ON p.product_id = smi.product_id WHERE sm.inventory_type_id = 15 AND date_format(date_added, '%Y-%m-%d') = '" . $date . "' GROUP BY smi.product_id";




        $query = $db->query($sql);
        $result = $query->rows;
        if(!empty($result)){
            foreach($result as $key=>$value){
                $value['product_batch'] = trim($value['product_batch']);
                $result[$key]['product_batch'] = str_replace(" ","",$value['product_batch']);
                $result[$key]['price'] = round($value['price'],2);
            }
        }
        return $result;
    }

    public function getInventoryCheck($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];


        $sql = "SELECT
	ics.*, SUM(ics.quantity) AS sum_quantity,
	pd. NAME,p.price,p.sku as product_batch
FROM
	oc_x_inventory_check_sorting AS ics
LEFT JOIN oc_product_description AS pd ON pd.product_id = ics.product_id
left join oc_product as p on p.product_id = ics.product_id
WHERE ics.uptime > '" . date("Y-m-d",  strtotime($date . " 00:00:00") - 24*3600) . " 12:00:00' and ics.uptime < '" . $date . " 12:00:00'
GROUP BY
	ics.product_id";



        $query = $db->query($sql);
        $result = $query->rows;
        if(!empty($result)){
            foreach($result as $key=>$value){
                $value['product_batch'] = trim($value['product_batch']);
                $result[$key]['product_batch'] = str_replace(" ","",$value['product_batch']);
                $result[$key]['price'] = round($value['price'],2);
            }
        }

        return $result;
    }

    public function getInventoryCheckSingle($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];
        $warehouse_id = (int)$data_inv['warehouse_id'];


        $sql = "SELECT
            ics.*,
            pd. NAME,p.price,p.sku as product_batch
        FROM
            oc_x_inventory_check_single_sorting AS ics
        LEFT JOIN oc_product_description AS pd ON pd.product_id = ics.product_id
        left join oc_product as p on p.product_id = ics.product_id";
        $sql .=" WHERE ics.uptime > '" . $date . " 00:00:00' and ics.uptime < '" . $date . " 24:00:00'";
        $sql .= " and ics.warehouse_id = '".$warehouse_id."'";



        $query = $db->query($sql);
        $result = $query->rows;
        if(!empty($result)){
            foreach($result as $key=>$value){
                $value['product_batch'] = trim($value['product_batch']);
                $result[$key]['product_batch'] = str_replace(" ","",$value['product_batch']);
                $result[$key]['price'] = round($value['price'],2);
            }
        }

        return $result;
    }



// 根据日期获取盘盈盘库的结果
    public function getinventoryCheckSingleDate($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];
        $warehouse_id = (int)$data_inv['warehouse_id'];


        $sql = "SELECT
            ics.*,
            pd. NAME,p.price,p.sku as product_batch
        FROM
            oc_x_inventory_check_single_sorting AS ics
        LEFT JOIN oc_product_description AS pd ON pd.product_id = ics.product_id
        left join oc_product as p on p.product_id = ics.product_id";
        $sql .=" WHERE ics.uptime > '" . $date . " 00:00:00' and ics.uptime < '" . $date . " 24:00:00'";
        $sql .= " and ics.warehouse_id = '".$warehouse_id."'";



        $query = $db->query($sql);
        $result = $query->rows;
        if(!empty($result)){
            foreach($result as $key=>$value){
                $value['product_batch'] = trim($value['product_batch']);
                $result[$key]['product_batch'] = str_replace(" ","",$value['product_batch']);
                $result[$key]['price'] = round($value['price'],2);
            }
        }

        return $result;
    }



    public function getInventoryVegCheck($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);

        $date = $data_inv['date'];

        /*
                $sql = "SELECT
            ics.*,
            pd. NAME,p.price,p.sku as product_batch
        FROM
            oc_x_inventory_veg_check_sorting AS ics
        LEFT JOIN oc_product_description AS pd ON pd.product_id = ics.product_id
        left join oc_product as p on p.product_id = ics.product_id
        WHERE date_format(ics.uptime, '%Y-%m-%d') = '" . $date . "'";
        */

        $sql = "SELECT
	ics.*,
	pd. NAME,p.price,p.sku as product_batch
FROM
	oc_x_inventory_veg_check_sorting AS ics
LEFT JOIN oc_product_description AS pd ON pd.product_id = ics.product_id
left join oc_product as p on p.product_id = ics.product_id
WHERE ics.uptime > '" . date("Y-m-d",  strtotime($date . " 00:00:00") - 24*3600) . " 12:00:00' and ics.uptime < '" . $date . " 12:00:00'";



        $query = $db->query($sql);
        $result = $query->rows;
        $result_arr = array();
        if(!empty($result)){
            foreach($result as $key=>$value){
                if(isset($result_arr[$value['product_id']])){
                    $result_arr[$value['product_id']]['sum_quantity'] += $value['quantity'];
                    $result_arr[$value['product_id']]['product_barcode'] = array_merge($result_arr[$value['product_id']]['product_barcode'],  json_decode($value['product_barcode']));
                }
                else{
                    $value['sum_quantity'] = $value['quantity'];
                    $value['product_barcode'] = json_decode($value['product_barcode']);
                    $result_arr[$value['product_id']] = $value;

                    $value['product_batch'] = trim($value['product_batch']);
                    $result_arr[$value['product_id']]['product_batch'] = str_replace(" ","",$value['product_batch']);
                    $result_arr[$value['product_id']]['price'] = round($value['price'],2);
                }

            }
        }

        return $result_arr;
    }

    public function getOrderStatus($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);



        $sql = "SELECT * FROM oc_x_deliver_order_status WHERE language_id = 2 ORDER BY order_status_id ASC";




        $query = $db->query($sql);
        $result = $query->rows;

        return $result;
    }

    public function getPurchaseOrderStatus($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;


        $data_inv = json_decode($data, 2);



        $sql = "select * from oc_x_pre_purchase_order_status where language_id = 2 order by order_status_id asc";




        $query = $db->query($sql);
        $result = $query->rows;

        return $result;
    }



    //COMMON

    function getStation($id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT * FROM oc_x_station WHERE status=1";

        if($id>0){
            $sql = "SELECT * FROM oc_x_station WHERE station_id = {$id} AND status=1";
        }
        $query = $db->query($sql);
        $results = $query->rows;

        if(sizeof($results)){
            return $results;
        }
        else{
            return array();
        }

//        if($results && sizeof($results)){
//            if($id>0){
//                $sql = "SELECT * FROM oc_x_area WHERE station_id = {$id} AND status=1";
//                $query = $db->query($sql);
//                $areas = $query->rows;
//                if($areas && sizeof($areas)){
//                    $results[0]['areas'] = $areas;
//                }
//            }
//
//            return $results;
//        }
    }

    function getAreaList(array $data){
        global $db;

        $return_code = 'FAIL';
        $return_data = array(
            'area_list'=>array(),
            'district_list'=>array()
        );

        $query = $db->query("SELECT area_id, city, district, name FROM oc_x_area WHERE status=1 order by district");
        if($query->num_rows){
            $return_code = 'SUCCESS';
            $return_data['area_list'] = $query->rows;

            $query = $db->query("SELECT city, district, group_concat(area_id) area_id_list FROM oc_x_area WHERE status=1 group by district");
            $return_data['district_list'] = $query->rows;
        }

        return array(
            'return_code' => $return_code,
            'return_msg' => '',
            'return_data' => $return_data
        );
    }


    function checkFirstOrder(array $data){
        global $db;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;

        $sql = "select count(*) orders from oc_order
        where order_status_id not in (3) and customer_id = '".$customer_id."'
        and station_id = '".$station_id."' and type = 1
        ";
        $query = $db->query($sql);
        if($query->row){
            $orderInfo = $query->row;

            return $orderInfo['orders'];
        }

        return false;
    }
//获取需要的do单信息
    function getOrderss($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;

        $data = json_decode($data, 2);
        $warehouse_repack = isset($data['warehouse_repack']) ? $data['warehouse_repack'] : false;
        $user_repack = isset($data['user_repack']) ? $data['user_repack'] : false;
        $deliver_order_repack = isset($data['deliver_order_repack']) ? $data['deliver_order_repack'] : false;
        $user_warehouse_id = isset($data['user_warehouse_id']) ? $data['user_warehouse_id'] : false;
        $product_id = isset($data['product_id']) ? $data['product_id'] : false;
        $check_is_dc = $this->checkWarehouseIsDc($data['warehouse_id']);

        if(!$product_id){
            return false;
        }
        $deliver_date = isset($data['deliver_date']) ? $data['deliver_date'] : false;
        if(!$deliver_date){
            return false;
        }
        $order_status_id = isset($data['order_status_id']) ? $data['order_status_id'] : false;
        if(!$order_status_id){
            return false;
        }

        $area_id_list = isset($data['area_id_list']) ? $data['area_id_list'] : false;

        $sql = "SELECT
              odo.deliver_order_id as order_id,
              odo.order_id as old_order_id,
              odo.station_id,
              odo.is_urgent,
              SUM(odop.weight_inv_flag) AS s_weight_inv_flag,
              SUM(odop.quantity) AS quantity,
              SUM(if(p.repack = 0 ,odop.quantity , 0)) AS quantity_zheng,
              SUM(if(p.repack = 1 ,odop.quantity , 0)) AS quantity_san,
              occ.customer_group_id AS group_id,
              occ.shortname AS group_shortname,
              odo.date_added,
              odo.warehouse_id,
              oxw.title,
              odo.shipping_address_1,
              odo.is_nopricetag,
              c.is_agent,
              a.name area_name,
              a.city,
              a.district
            FROM oc_x_deliver_order odo
            LEFT JOIN oc_x_deliver_order_product odop  ON odo.deliver_order_id = odop.deliver_order_id
            LEFT JOIN oc_customer_group AS occ ON odo.customer_group_id = occ.customer_group_id
            LEFT JOIN oc_customer c ON odo.customer_id = c.customer_id
            LEFT JOIN oc_x_area a ON c.area_id = a.area_id
            LEFT JOIN oc_product AS p ON p.product_id = odop.product_id
            LEFT JOIN oc_product_to_category AS ptc ON ptc.product_id = p.product_id
            LEFT JOIN oc_x_warehouse AS oxw ON oxw.warehouse_id = odo.warehouse_id
        ";
        //do单类型
        if($product_id ==5001){
            $sql .= " WHERE odo.do_warehouse_id = 10 AND p.product_type_id = 2";
        }
        elseif($product_id ==5002){
            $sql .= " WHERE odo.do_warehouse_id = 10 AND p.product_type_id = 3";
        }
        elseif($product_id ==5003){
            $sql .= " WHERE odo.do_warehouse_id = 10 AND p.product_type_id = 11";
        }
        //elseif($product_id ==5003){
        //  $sql .= " WHERE ptc.category_id in (72,74,157) and oc_order.station_id = 1 ";
        //}
        elseif($product_id ==5004){
            $sql .= " WHERE odo.station_id = 2";
        }
        //elseif($product_id ==5005){
        //    $sql .= " WHERE oc_order.station_id = 2 and p.product_type = 4";
        //}
        //elseif($product_id ==5006){
        //    $sql .= " WHERE oc_order.station_id = 2 and p.product_type = 5";
        //}
        else{
            $sql .= " WHERE odo.do_warehouse_id = 10 ";
        }
        $sql .= " AND odo.order_status_id = " . $data['order_status_id'];
        $sql .= " AND odo.do_warehouse_id = ". $data['warehouse_id'];
        $sql .= " AND odo.deliver_date  = '" . $data['deliver_date'] . "'";
        $sql .= " AND odo.order_type  != 3 ";

        if($warehouse_repack == 1){
            $sql .= " ANd odo.is_repack = '".$deliver_order_repack."'";
        }

        if($area_id_list){
            $sql .= " AND c.area_id IN (".$area_id_list.")";
        }

        if($data['warehouse_id'] == 12 || $check_is_dc){
            $sql .= "  and  odo.warehouse_id = '".$user_warehouse_id."' ";
        }


        $sql .=" GROUP BY odo.deliver_order_id";
        $sql .=" ORDER BY odo.warehouse_id DESC,odo.is_urgent DESC , a.city,a.district,a.name,odo.shipping_address_1";

        /*
         * 数据插入到新表temp_order
        *	$sql2=" INSERT INTO temp_order(order_id,quantity) SELECT * FROM (";
        *	$sql2.=$sql;
        *	$sql2.=") AS tb";
        *	$query2=$db->query($sql2);
        */


        $query = $db->query($sql);

        $results = $query->rows;

        $all_orders = array();

        //获取快消品平台关联订单信息
        $doInfo = array();
        if($data['warehouse_id'] && $data['deliver_date'] && sizeof($results)){
            $getDoInfo = array(
                'station_id' => 2,
                'warehouse_id' => $data['warehouse_id'],
                'date' => $data['deliver_date'],
                'inventory_user' => ''
            );
            $doInfo = $this->getDoOrderInfo($getDoInfo);
        }

        //将do单数据的日期按固定格式输出，添加订单信息
        foreach($results as $key=>$value){
            $value['date_added'] = date("H:i:s",  strtotime($value['date_added']));
            $all_orders[$value['order_id']] = $value;

            $all_orders[$value['order_id']]['doInfo'] = '';

            if(array_key_exists($value['old_order_id'], $doInfo)){
                foreach($doInfo[$value['old_order_id']] as $idx=>$val){
                    if($idx <> $value['order_id']){
                        $all_orders[$value['order_id']]['doInfo'] .= '['.$val.']';
                    }
                }
            }
        }


        /*
            $return = array();
            $return['data'] = array();
           // var_dump ($results);

            if(sizeof($results)){

                foreach($results as $k=>$v){

                 $return['data'][$v['order_id']] = $v;
                 $return['data'][$v['order_id']]['quantity'] = $v['quantity'];
                 $return['data'][$v['order_id']]['tb'] = $query2;
                }
            }
        */


        $all_orders=array_values($all_orders);


        if(sizeof($all_orders)){
            return $all_orders;
        }
        else{

            return array();
        }
    }
//DO单分配
    function orderdistr($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;
        global $orders_id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $data = json_decode($data, 2);
//return $sql;
        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        //return $order_id;
        if(!is_array($order_id)){
            return 0;
        }

        $inventory_name = isset($data['inventory_name']) ? $data['inventory_name'] : false;
        if(!$inventory_name){
            return 0;
        }

        $sql = "select  repack from oc_w_user  WHERE username = '".$inventory_name."' ";

        $query = $db->query($sql);
        $user_repack = $query->row;
        if ($data['warehouse_repack'] == 1) {
            if($user_repack['repack'] != $data['user_repack']){
                return 0 ;
            }

        }


        $product_id = isset($data['product_id']) ? $data['product_id'] : false;
//do单分类
        if(!$product_id){
            return 0;
        }

        else if ($product_id==5001){
            $ordclass = 2 ;

        }
        else if ($product_id==5002){
            $ordclass = 3 ;

        }
        else if ($product_id==5003){
            $ordclass = 4 ;

        }
        else if ($product_id==5004){
            $ordclass = 5 ;

        }
        else if ($product_id==5005){
            $ordclass = 6 ;

        }
        else if ($product_id==5006){
            $ordclass = 7 ;

        }
        else{
            $ordclass = 1 ;
        }



        if( is_array($order_id)){

            foreach($order_id as $value){
                $ayy = explode('@',$value);
                $orders_id = $ayy[0];
                $quantity = $ayy[1];
//                if ($data['old_order_id'] == null) {
                $old_order_id = $ayy[2];
//                } else {
//                    $old_order_id = $data['old_order_id'];
//                }

//查询do单状态以及整散
                $sql = "  SELECT order_status_id,is_repack FROM oc_x_deliver_order WHERE  deliver_order_id = '".$orders_id ."'  ";

                $query = $db->query($sql);
                $order_status = $query->row;

//如果不是已确认不能进行分配
                if($order_status['order_status_id'] != 2 ){
                    return 0;
                }else {

//判断是否是分区分拣，如果是，判断分配的订单是否对应相应的分拣人员
                    if ($data['warehouse_repack'] == 1) {
                        if ($order_status['is_repack'] != $user_repack['repack']) {
                            return 0;
                        }

                    }
                    $sql="INSERT INTO oc_order_distr 
(order_id, deliver_order_id,inventory_name, ordclass,quantity,warehouse_id,warehouse_repack, user_repack)
VALUES ('$old_order_id','$orders_id','$inventory_name','$ordclass','$quantity', '". $data['warehouse_id']."','". $data['warehouse_repack']."','". $data['user_repack']."')";
                    $sql1 = "UPDATE oc_x_deliver_order SET  order_status_id = 4  WHERE deliver_order_id = '" . $orders_id . "'";

                    $query = $db->query($sql);
                    $query1 = $db->query($sql1);

                }
            }
        }
        return $query;

    }

    function getStatus($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        return true;
        global $db;
        global $log;


        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;
        if(!$date){
            return false;
        }
        $order_id = isset($data['orders_id']) ? $data['orders_id'] : false;
        if(!$order_id){
            return false;
        }
        $sql =" select order_status_id from oc_order where ";
        $sql .= " order_id = " . $data['orders_id'];

        $query = $db->query($sql);
        $results = $query->row;

        //$results = String($results);

        if(sizeof($results)){
            return $results;
        }else{

            return array();
        }
    }
//获取分配分拣表中的数据
    function ordered($data, $station_id=1, $language_id=2, $origin_id=1, $key){

        global $db;
        global $log;


        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;
        $warehouse_repack = isset($data['warehouse_repack']) ? $data['warehouse_repack'] : 0;
        $user_warehouse_id = isset($data['user_warehouse_id']) ? $data['user_warehouse_id'] : 0;
        $user_repack = isset($data['user_repack']) ? $data['user_repack'] : 0;
        $order_status_id = isset($data['order_status_id']) ? $data['order_status_id'] : false;
        $check_is_dc = $this->checkWarehouseIsDc($data['warehouse_id']);
//        $deliver_order_repack = isset($data['deliver_order_repack']) ? $data['deliver_order_repack'] : false;
//        return $data;
        if(!$order_status_id){
            return false;
        }
        $product_id = isset($data['product_id']) ? $data['product_id'] : false;
        if(!$product_id){
            return false;
        }
        $deliver_date = isset($data['deliver_date']) ? $data['deliver_date'] : false;
        if(!$deliver_date){
            return false;
        }
        //判断分类
        if ($product_id==5001){

            $ordclass = 2 ;
        }elseif($product_id == 5002){
            $ordclass = 3;
        }
        elseif($product_id == 5003){
            $ordclass = 4;
        }
        elseif($product_id == 5004){
            $ordclass = 5;
        }
        elseif($product_id == 5005){
            $ordclass = 6;
        }
        elseif($product_id == 5006){
            $ordclass = 7;
        }
        else{
            $ordclass = 1 ;
        }
        //非分区分拣
        if($warehouse_repack == 0 ){
            $sql = "SELECT GROUP_CONCAT(o.is_nopricetag,td.quantity) AS groups1,
GROUP_CONCAT(o.order_status_id) AS groups2,
GROUP_CONCAT(td.deliver_order_id) AS groups3,
GROUP_CONCAT(os.name) AS groups4,
GROUP_CONCAT(o.warehouse_id) AS warehouse_id,
GROUP_CONCAT(oxw.title) AS title,
GROUP_CONCAT(IF (
		td.user_repack = 0,
		'整件',
		'散件'
	),td.inventory_name) AS inventory_name,
SUM(td.quantity) AS total, 
td.ordclass,o.deliver_date ,td.warehouse_repack,td.user_repack
FROM oc_order_distr AS td 
LEFT JOIN oc_x_deliver_order AS o ON o.deliver_order_id = td.deliver_order_id 
LEFT JOIN oc_x_deliver_order_status AS os ON os.order_status_id =o.order_status_id 
LEFT JOIN oc_x_warehouse AS oxw ON oxw.warehouse_id = o.warehouse_id WHERE";
            //$sql .= " o.order_status_id = " . $data['order_status_id'];
            $sql .="   o.do_warehouse_id = '" .$data['warehouse_id'] . "'";
            $sql .="  AND o.deliver_date = '" .$data['deliver_date'] . "'";

            $sql .= " AND td.ordclass =  ".$ordclass;
            $sql .=" GROUP BY td.inventory_name ORDER BY o.warehouse_id DESC";
//return $sql;
            $query = $db->query($sql);
            $results = $query->rows;
            //分区分拣
        } else{
            $sql = "SELECT
	GROUP_CONCAT(
		o.is_nopricetag,
		td.quantity
	) AS groups1,
GROUP_CONCAT(o.order_status_id) AS groups2,
GROUP_CONCAT(td.deliver_order_id) AS groups3,
GROUP_CONCAT(os.name) AS groups4,
GROUP_CONCAT(o.warehouse_id) AS warehouse_id,
GROUP_CONCAT(oxw.title) AS title,
GROUP_CONCAT(IF (
		td.user_repack = 0,
		'整件',
		'散件'
	),td.inventory_name) AS inventory_name,
	SUM(td.quantity) AS total,
	td.ordclass,
	o.deliver_date,
	o.order_id AS old_order_id,
	td.warehouse_repack,
	td.user_repack
FROM
	oc_order_distr AS td
LEFT JOIN oc_x_deliver_order AS o ON o.deliver_order_id = td.deliver_order_id
LEFT JOIN oc_x_deliver_order_status AS os ON os.order_status_id = o.order_status_id  
LEFT JOIN oc_x_warehouse AS oxw ON oxw.warehouse_id = o.warehouse_id
 left join  oc_w_user w on  w.username = td.inventory_name  WHERE ";


            //$sql .= " o.order_status_id = " . $data['order_status_id'];
            $sql .="   o.do_warehouse_id = '" .$data['warehouse_id'] . "'";
            $sql .="  AND o.deliver_date = '" .$data['deliver_date'] . "'";
            if($data['warehouse_id'] ==12 || $check_is_dc){
                $sql .=  " and  w.to_warehouse_id = '".$user_warehouse_id ."' ";
            }
            $sql .= " AND td.ordclass =  ".$ordclass;
//            $sql .= "  and o.is_repack = '".$deliver_order_repack."'";
            $sql .="  GROUP BY td.inventory_name ORDER BY o.warehouse_id DESC";

            $query = $db->query($sql);
            $results = $query->rows;
        }

        /*
         * 查找订单状态
         * @param order_ststus_id

         foreach($results as $k=>$v){
             $order_id = $v['order_id'];
             $sql = "select order_status_id from oc_order where order_id = $order_id";
             $query = $db->query($sql);
             $r = $query->row;
             $results[$k]['order_status_id'] = $r['order_status_id'];
         }
         */
        if(!empty($results)){
            return $results;
        }else{
            return array();
        }

    }
//重新分配
    function orderRedistr($data, $station_id=1, $language_id=2, $origin_id=1, $key){

        global $db;
        global $log;


        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;
        if(!$date){
            return false;
        }
        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        if(!$order_id){
            return false;
        }
        $ordclass = isset($data['ordclass']) ? $data['ordclass'] : false;
        if(!$ordclass){

            return false;
        }
        $warehouse_repack = isset($data['warehouse_repack']) ? $data['warehouse_repack'] : 0;
        $user_repack = isset($data['user_repack']) ? $data['user_repack'] : 0;

        $sql2 = "select order_status_id  from oc_x_deliver_order where deliver_order_id = '".$order_id ."'";

        $query = $db->query($sql2);
        $results = $query->row;
        if($results['order_status_id'] == 4) {

            $sql = "DELETE FROM oc_order_distr WHERE deliver_order_id = $order_id 
AND ordclass = $ordclass  
  ";

            $query = $db->query($sql);

            $sql1 = "UPDATE oc_x_deliver_order SET order_status_id = 2  
WHERE deliver_order_id = '" . $order_id . "'";
            $query1 = $db->query($sql1);

            return 1;
        }else{
            return 2 ;
        }
    }



    function getInventoryUserOrder($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;

        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;
        if(!$date){
            return false;
        }

        $sql = "SELECT
                    od.id, od.deliver_order_id order_id, od.w_user_id, od.inventory_name, od.ordclass, od.quantity
                FROM oc_order_distr AS od
                LEFT JOIN oc_x_deliver_order AS o ON od.deliver_order_id = o.deliver_order_id
                WHERE
                    
                 od.inventory_name = '" . $data['inventory_user'] . "' and o.order_status_id !=3 ";

//        if($data['warehouse_id'] != 12  && $data['warehouse_id'] !=14 ){
//            $sql  .= " and o.deliver_date = '" . $date . "'  ";
//        }
//        if($data['warehouse_id'] == 12 || $data['warehouse_id']  == 14 ){
//            $sql .= "  and o.order_status_id in (4,5,8)";
//        }

        if($data['order_status_id'] == 2 || $data['order_status_id'] == 4 || $data['order_status_id'] == 5){
            $sql .= " and o.deliver_date between date_sub(current_date(), interval 8 day) and date_add(current_date(), interval 1 day) ";
        }
        else{
            $sql .= " and o.deliver_date = '" . $date . "' ";
        }

        $query = $db->query($sql);
        $results = $query->rows;

        $return = array();
        $return['data'] = array();

        $return['data'] = $results;

        if(sizeof($return)){
            return $return;
        }
        else{
            return array();
        }

    }

    //获取指定日期DO单基本信息
    function getDoOrderInfo($data=array()){
        //Require $data['station_id'], $data['warehouse_id'], $data['date'], $data['inventory_user']
        global $db;
        global $log;

        $station_id = isset($data['station_id']) ? mysql_real_escape_string($data['station_id']) : false;
        $warehouse_id = isset($data['warehouse_id'])  ? mysql_real_escape_string($data['warehouse_id']) : false;
        $date = isset($data['date']) ? date('Y-m-d', strtotime($data['date'])) : false;
        $inventory_user = isset($data['inventory_user']) ? mysql_real_escape_string($data['inventory_user']) : false;
        $ongoing = isset($data['ongoing']) ? mysql_real_escape_string($data['ongoing']) : false;

        $doInfo = array();
        if($station_id && $warehouse_id && $date){
            $doSql = "select
                    o.deliver_order_id, o.order_id, o.order_status_id,
                    os.name order_status,
                    if(oi.inv_comment is null, 0, oi.inv_comment) inv_comment,
                    w.shortname
                from oc_x_deliver_order o
                left join oc_order_distr od on o.order_id = od.order_id
                left join oc_x_deliver_order_status os on os.order_status_id = o.order_status_id
                left join oc_x_deliver_order_inv oi on o.deliver_order_id = oi.deliver_order_id
                left join oc_x_warehouse w on o.warehouse_id = w.warehouse_id
                where o.station_id = '".$station_id."' and  o.warehouse_id = '".$warehouse_id."'
                and o.order_type !=3
                and o.deliver_date = '". $date."'
                ";
            if($ongoing){
                $doSql .=  " and o.order_status_id not in (1,2,3,4)";
            }
            else{
                $doSql .=  " and o.order_status_id not in (1,3)";
            }

            if($inventory_user){
                $doSql .=  " and od.inventory_name = '".$inventory_user."'";
            }
            $doSql .= " order by o.is_urgent desc";

            $doQuery = $db->query($doSql);
            $doInfoRaw = $doQuery->rows;
            foreach($doInfoRaw as $m){
                $doInfo[$m['order_id']][$m['deliver_order_id']] = $m['shortname'].'-'.$m['order_status'].'-'.$m['inv_comment'];
            }
        }

        return $doInfo;
    }

    function getOrders($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;
//        return 123;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;

        //New 20170416
        $station_id = isset($data['station_id']) ? $data['station_id'] : 0;
        $inventory_user = isset($data['inventory_user']) ? $data['inventory_user'] : false;
        $user_repack = isset($data['user_repack']) ? $data['user_repack'] : false;
        $warehouse_repack = isset($data['warehouse_repack']) ? $data['warehouse_repack'] : false;
        $psize = isset($data['psize']) ? $data['psize'] : false;
        $start_row = isset($data['start_row']) ? $data['start_row'] : false;
        $old_deliver_order_id = $data['old_deliver_order_id']>0 ? $data['old_deliver_order_id'] : false;
        $order_type = isset($data['order_type']) ? $data['order_type'] : false;
        //$orderList = isset($data['orderList']) && sizeof($data['orderList']) ? $data['orderList'] : array(0);
//        return $data;
        $return = array();
        $return['data'] = array();
        if(!$date){
            return false;
        }

        if($data['order_status_id']  == 999 ){
            $sql = "SELECT
          o.deliver_order_id order_id,
          o.order_id  so_order_id , 
          o.is_urgent,
          o.customer_id,
          o.station_id,
          o.deliver_date,
          o.date_added,
          GROUP_CONCAT(op.product_id) as product_id_str,
          os.`name`,
          SUM(op.quantity) as quantity,
          sum(if(p.repack = 0 ,op.quantity , 0)) as quantity_zheng,
          sum(if(p.repack = 1 ,op.quantity , 0)) as quantity_san,
          o.order_status_id,
          o.shipping_name,
          o.shipping_phone,
          o.shipping_address_1,
          o.customer_group_id,
          o.is_nopricetag,
          oi.frame_count,
          oi.inv_comment,
          oi.incubator_count,
          oi.foam_count,
          oi.frame_mi_count,
          incubator_mi_count,
          oi.frame_ice_count,
          oi.box_count,
          oi.foam_ice_count,
          oi.frame_meat_count,
          oi.frame_vg_list,
          oi.frame_meat_list,
          oi.frame_mi_list,
          oi.frame_ice_list,
          ptc.category_id,
          ocg.customer_group_id as group_id,
          ocg.shortname as group_shortname,
          c.is_agent,
          a.name area_name,
          a.city,
          a.district,
          '' order_container ,
         o.do_warehouse_id ,
         o.warehouse_id ,
         w.title, w.shortname
        FROM oc_order oo
        LEFT JOIN oc_x_deliver_order AS o ON oo.order_id = o.order_id
        LEFT JOIN oc_x_deliver_order_product AS op ON o.deliver_order_id = op.deliver_order_id
        left join oc_product p on p.product_id = op.product_id 
        left join oc_customer_group as ocg on o.customer_group_id = ocg.customer_group_id
        LEFT JOIN oc_x_deliver_order_status AS os ON os.order_status_id = o.order_status_id
        left join oc_customer c on o.customer_id = c.customer_id
        left join oc_x_area a on c.area_id = a.area_id
        LEFT join oc_x_warehouse w on o.warehouse_id = w.warehouse_id 
        ";


            if ($station_id == 2 && $inventory_user) {
                $sql .= " left join oc_order_distr od on o.deliver_order_id = od.deliver_order_id";
            }
            $sql .= "
        LEFT JOIN oc_product_to_category AS ptc ON ptc.product_id = op.product_id
        LEFT JOIN oc_x_deliver_order_inv  AS oi ON o.deliver_order_id = oi.deliver_order_id  AND oi.inv_status = 1
        WHERE ";
            if ($old_deliver_order_id) {
                if ($order_type == 1) {
                    $sql .= "o.deliver_order_id = '".$old_deliver_order_id."' AND oo.warehouse_id = '".$data['warehouse_id']."' AND o.order_type !=3  AND oo.order_status_id not in (1,2,3,6) 
        GROUP BY oo.order_id 
        HAVING warehouse_id != do_warehouse_id ";
                } else if ($order_type == 2) {
                    $sql .= "o.order_id = '".$old_deliver_order_id."' AND oo.warehouse_id = '".$data['warehouse_id']."' AND o.order_type !=3  AND oo.order_status_id not in (1,2,3,6) 
        GROUP BY oo.order_id 
        HAVING warehouse_id != do_warehouse_id ";
                }
            } else {
                $sql .= "oo.station_id = '".$station_id."' AND oo.warehouse_id = '".$data['warehouse_id']."' AND o.order_type !=3  AND oo.order_status_id not in (1,2,3,6) 
        GROUP BY oo.order_id 
        HAVING warehouse_id != do_warehouse_id ";
            }
//            return $sql;


            $sql1 = "SELECT
          o.deliver_order_id order_id,
          o.order_id  so_order_id , 
         o.do_warehouse_id ,
         o.warehouse_id ,
        COUNT(o.deliver_order_id) AS count
        FROM oc_order oo
        LEFT JOIN oc_x_deliver_order AS o ON oo.order_id = o.order_id
        WHERE ";
            if ($old_deliver_order_id) {
                if ($order_type == 1) {
                    $sql1 .= "o.deliver_order_id = '".$old_deliver_order_id."' AND oo.warehouse_id = '".$data['warehouse_id']."' AND o.order_type !=3  AND oo.order_status_id not in (1,2,3,6) 
        GROUP BY oo.order_id 
        HAVING warehouse_id != do_warehouse_id ";
                } else if ($order_type == 2) {
                    $sql1 .= "o.order_id = '".$old_deliver_order_id."' AND oo.warehouse_id = '".$data['warehouse_id']."' AND o.order_type !=3  AND oo.order_status_id not in (1,2,3,6) 
        GROUP BY oo.order_id 
        HAVING warehouse_id != do_warehouse_id ";
                }
            } else {
                $sql1 .= "oo.station_id = '" . $station_id . "' AND oo.warehouse_id = '" . $data['warehouse_id'] . "' AND oo.order_type !=3  AND oo.order_status_id not in (1,2,3,6)
        GROUP BY oo.order_id 
        HAVING count = 1 AND warehouse_id != do_warehouse_id ";
            }
            $query1 = $db->query($sql1);
            $results1 = $query1->rows;
            $return['data2'] = $results1;
        } else {
            $sql = "SELECT
          o.deliver_order_id order_id,
          o.order_id  so_order_id , 
          o.is_urgent,
          o.customer_id,
          o.station_id,
          o.deliver_date,
          o.date_added,
          GROUP_CONCAT(op.product_id) as product_id_str,
          os.`name`,
          SUM(op.quantity) as quantity,
          sum(if(p.repack = 0 ,op.quantity , 0)) as quantity_zheng,
          sum(if(p.repack = 1 ,op.quantity , 0)) as quantity_san,
          o.order_status_id,
          o.shipping_name,
          o.shipping_phone,
          o.shipping_address_1,
          o.customer_group_id,
          o.is_nopricetag,
          oi.frame_count,
          oi.inv_comment,
          oi.incubator_count,
          oi.foam_count,
          oi.frame_mi_count,
          incubator_mi_count,
          oi.frame_ice_count,
          oi.box_count,
          oi.foam_ice_count,
          oi.frame_meat_count,
          oi.frame_vg_list,
          oi.frame_meat_list,
          oi.frame_mi_list,
          oi.frame_ice_list,
          ptc.category_id,
          ocg.customer_group_id as group_id,
          ocg.shortname as group_shortname,
          c.is_agent,
          a.name area_name,
          a.city,
          a.district,
          '' order_container ,
         o.do_warehouse_id ,
         o.warehouse_id ,
         w.title, w.shortname
        FROM oc_x_deliver_order AS o
        LEFT JOIN oc_x_deliver_order_product AS op ON o.deliver_order_id = op.deliver_order_id
        left join oc_product p on p.product_id = op.product_id 
        left join oc_customer_group as ocg on o.customer_group_id = ocg.customer_group_id
        LEFT JOIN oc_x_deliver_order_status AS os ON os.order_status_id = o.order_status_id
        left join oc_customer c on o.customer_id = c.customer_id
        left join oc_x_area a on c.area_id = a.area_id
        LEFT join oc_x_warehouse w on o.warehouse_id = w.warehouse_id 
        ";


            if($station_id == 2 && $inventory_user){
                $sql .= " left join oc_order_distr od on o.deliver_order_id = od.deliver_order_id";
            }
            if($data['order_status_id'] == 555){
                $sql .= " left join oc_order oco on oco.order_id = o.order_id";
            }
            $sql .= "
        left join oc_product_to_category as ptc on ptc.product_id = op.product_id

        LEFT JOIN oc_x_deliver_order_inv  as oi on o.deliver_order_id = oi.deliver_order_id  and oi.inv_status = 1

        WHERE   o.do_warehouse_id = '". $data['warehouse_id']."'  and o.order_type !=3 ";

        if ($data['warehouse_id'] != 12 && $data['warehouse_id'] != 14) {
            $sql .= "  and o.deliver_date = '" . $date . "'";
        }


        if ($data['warehouse_id'] == 12 || $data['warehouse_id'] == 14) {

            $sql .= "  and o.order_status_id  in (4,5,8) ";
        }
            if ($old_deliver_order_id) {
                if ($order_type == 1) {
                    $sql .= "AND o.deliver_order_id = '".$old_deliver_order_id."'";
                } else if ($order_type == 2) {
                    $sql .= "AND o.order_id = '".$old_deliver_order_id."'";
                }
            } else {
                if ($data['order_status_id'] == 2 || $data['order_status_id'] == 4 || $data['order_status_id'] == 5 || $data['order_status_id'] == 8) {
//                    $sql .= " and o.deliver_date between date_sub(current_date(), interval 5 day) and date_add(current_date(), interval 1 day) ";
                } else {
//                    $sql .= " and o.deliver_date = '" . $date . "' ";
                }

            if ($data['order_status_id'] != 0 && $data['order_status_id'] < 100) {

                if ($data['order_status_id'] == 2) {
                    $sql .= " AND o.order_status_id in (2,4) ";
                } else {
                    $sql .= " AND o.order_status_id = " . $data['order_status_id'];
                }


            }
            if ($data['order_status_id'] == 777) {
                $sql .= " AND o.is_urgent = 1 ";
            }
            if ($data['order_status_id'] == 666) {
                $sql .= " AND o.warehouse_id != o.do_warehouse_id ";
            }

            if ($data['order_status_id'] == 555) {
                $sql .= " AND  oco.order_status_id = 5 ";
            }

                if ($inventory_user && $station_id == 2) {
                    $sql .= " AND od.inventory_name = '" . $inventory_user . "'";
                }
            }
//             $sql .= "  and o.order_id = 635330";
            $sql .= " GROUP BY op.deliver_order_id order by o.is_urgent desc, o.station_id asc,o.order_id asc";
            if($start_row && $psize){
                $sql .=  " limit  ".$start_row*$psize." ,  ".$psize ;
            }else {
                $sql .= " limit 0,  200 " ;
            }
        }
//        return  $sql;
//return $sql;
        $query = $db->query($sql);
        $results = $query->rows;


        //为分拣员工获取相关配送单信息, Alex 2018-01-20
        //未确认，已确认，取消订单, 已分配不列出
        if($inventory_user && $station_id == 2){
            $getDoInfo = array(
                'station_id' => $station_id,
                'warehouse_id' => $data['warehouse_id'],
                'date' => $date,
                'inventory_user' => $inventory_user,
                'ongoing' => 1
            );
            $doInfo = $this->getDoOrderInfo($getDoInfo);
        }


//return $doInfo
//    ;
        $queryOrderList = array(0);

        $array_order_ids = [];
        if (sizeof($results)) {
            foreach ($results as $k => $v) {

                $order_product_id_arr = array();
                $order_has_vg = false;
                $order_has_mi = false;
                $order_product_id_arr = explode(",", $v['product_id_str']);

                $v['inv_type_1'] = 0;
                $v['inv_type_2'] = 0;
                $v['inv_type_3'] = 0;
                $v['inv_type_4'] = 0;
                $v['inv_type_5'] = 0;
                $v['inv_type_6'] = 0;
                $v['inv_type_7'] = 0;

                $array_order_ids[$v['so_order_id']] = $v['so_order_id'];

                $return['data'][$v['station_id'] . $v['order_id']] = $v;
                $return['data'][$v['station_id'] . $v['order_id']]['plan_quantity'] = $v['quantity'];
                $return['data'][$v['station_id'] . $v['order_id']]['added_by'] = '';
                $return['data'][$v['station_id'] . $v['order_id']]['station_id'] = $v['station_id'];

                //20170421, 取消order_product_type 和 bao 的计算和设定
                $return['data'][$v['station_id'] . $v['order_id']]['order_product_type'] = 0;
                $return['data'][$v['station_id'] . $v['order_id']]['bao'] = 0;

                //为分拣员工获取相关配送单信息, Alex 2018-01-20
                $return['data'][$v['station_id'] . $v['order_id']]['doInfo'] = '';
                if(array_key_exists($v['so_order_id'], $doInfo)){
                    foreach($doInfo[$v['so_order_id']] as $idx=>$val){
                        if($idx < $v['order_id']){
                            $return['data'][$v['station_id'] . $v['order_id']]['doInfo'] .= '['.$val.']';
                        }
                    }
                }
            }
        }




        //$last_one_day = date("Y-m-d", time() + 8*3600 - 24*3600);
        //获取入库中间表中已入库的商品，并从计划入库的商品中减去已入库的商品

        $sql = "SELECT o.station_id, xis.deliver_order_id order_id , xis.product_id, sum(xis.quantity) quantity , xis.uptime, xis.move_flag, xis.added_by, xis.product_barcode, p.storage_mode_id, '0' category_id,o.station_id,p.product_type,p.product_type_id ,o.is_urgent
        FROM oc_x_inventory_order_sorting AS xis
        left join oc_x_deliver_order as o on o.deliver_order_id = xis.deliver_order_id
        left join oc_product as p on p.product_id = xis.product_id
        where  xis.status = 1 
        " ;
        if ($old_deliver_order_id) {
            if ($order_type == 1) {
                $sql .= "  and o.deliver_order_id = '".$old_deliver_order_id."'";
            } else if ($order_type == 2) {
                $sql .= " and o.order_id = '".$old_deliver_order_id."'";
            }
        } else {
            if (!in_array($data['warehouse_id'],[12,14])) {
//                $sql .= "  and  o.deliver_date = '" . $date . "' ";
            }

        if(in_array($data['warehouse_id'],[12,14])){
            $sql .= "  and   o.order_status_id in (4,5,6,8)";
//            $sql .= "  and o.deliver_date between date_sub(current_date(), interval 2 day) and date_add(current_date(), interval 1 day) ";
        }

            $sql .= "  and o.order_type = 1 group by o.deliver_order_id   ";
        }

        $sql .= " order by o.is_urgent desc, o.station_id asc,o.order_id asc";
        if($start_row && $psize){
            $sql .=  " limit  ".$start_row*$psize." ,".$psize ;
        }else {
            $sql .= " limit 0, ".$psize ;
        }
//        echo $sql;
        $query = $db->query($sql);
        $result = $query->rows;

//return $result;

        if(sizeof($result)){
            foreach($result as $rk => $rv){
                $return_move_p = array();
                if($return['data'][$rv['station_id'] . $rv['order_id']]['quantity'] > 0){
                    $return['data'][$rv['station_id'] . $rv['order_id']]['quantity'] -= $rv['quantity'];

                    if($return['data'][$rv['station_id'] . $rv['order_id']]['quantity'] <= 0){
                        $return_move_p = $return['data'][$rv['station_id'] . $rv['order_id']];
                        unset($return['data'][$rv['station_id'] . $rv['order_id']]);
                        $return['data'][$rv['station_id'] . $rv['order_id']] = $return_move_p;
                    }
                    $return['data'][$rv['station_id'] . $rv['order_id']]['added_by'] = $rv['added_by'];

                    if($rv['station_id'] == 1 && $rv['product_type_id'] == 1){
                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_1'] += $rv['quantity'];
                    }
                    if($rv['station_id'] == 1 && $rv['product_type_id'] == 2){
                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_2'] += $rv['quantity'];
                    }
                    if($rv['station_id'] == 1 && $rv['product_type_id'] == 3){
                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_3'] += $rv['quantity'];
                    }

                    if( $rv['station_id'] == 2 ){
                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_5'] += $rv['quantity'];
                    }
                }
            }
        }


        $array_order_ids = join($array_order_ids,',');
//        return $array_order_ids;
        $array_merge_info = [];
        if (!empty($array_order_ids)){
            $sql = "SELECT COUNT(deliver_order_id) as count,order_id FROM oc_x_deliver_order WHERE order_id IN(".$array_order_ids.") GROUP BY order_id ";
//            return $sql;

            $result_order = $db->query($sql)->rows;

            if (!empty($result_order)) {
                foreach ($result_order as $value) {
                    $array_merge_info[$value['order_id']]['count'] = $value['count'];
                }
                foreach ($return['data'] as $key => $value2) {
                    $return['data'][$key]['order_count'] = empty($array_merge_info[$value2['so_order_id']]) ? 0 : $array_merge_info[$value2['so_order_id']]['count'] ;
                }
            }

        }


        if(sizeof($return)){



            return  $return;
        }
        else{
            return array();
        }


    }


    function getPurchaseOrders($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;

        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $data = json_decode($data, 2);
        $date = '2018-6-20';
//            isset($data['date']) ? $data['date'] : false;
      $date_end = '2018-6-20';
// isset($data['date_end']) ? $data['date_end'] : false;
        $handle_product = isset($data['handle_product']) ? $data['handle_product'] : false;
        if(!$date){
            //return false;
        }
//        return $date;
        $sql = "SELECT
	GROUP_CONCAT(o.purchase_order_id ORDER BY o.purchase_order_id) purchase_order_ids 
FROM 
oc_x_pre_purchase_order AS o 
where o.is_gift = 0 AND o.order_type=1  and o.warehouse_id = '".$data['warehouse_id']."'
	 ";
        if($data['purchase_order_id']){
            $sql .= " AND o.purchase_order_id = " . $data['purchase_order_id'];
        } else {
            if($date){
                $sql .=" AND DATE(o.date_deliver) BETWEEN '" . $date . "' AND '".$date_end."' ";
            }
            if($data['order_status_id'] != 0 ){
                $sql .= " AND o.status = " . $data['order_status_id'];
            }
//            if($handle_product){
//                $sql .= " AND oppp.product_id = " .$handle_product;
//            }
        }
//return $sql;
        $query = $db->query($sql)->row;
        if (empty($query['purchase_order_ids'])) {
            return array();
        }
        //采购单id集
        $purchase_orders = $query['purchase_order_ids'];
        //赠品单数组集
        $order_gifts = [];
        //赠品单id集
        $gift_purchase_orders = '';

        $sql = "SELECT
	o.purchase_order_id as order_id,
	o.purchase_order_id,
        o.station_id,
	o.`status` as order_status_id,
	os.`name` AS name,
	st.`name` AS st_name,
        o.order_comment,
	SUM(op.quantity) as plan_quantity , 
	 u.lastname ,
	 u.firstname ,
	o.related_order,
      DATEDIFF(NOW(), o.date_deliver) date_diff ,
      SUM(IF(opp.repack = 0,op.quantity,0) ) AS quantity_one, 
      SUM(IF(opp.repack = 1,op.quantity,0) ) AS quantity_two 
   
	 
FROM
	oc_x_pre_purchase_order AS o
LEFT JOIN oc_x_pre_purchase_order_product AS op ON o.purchase_order_id = op.purchase_order_id
-- LEFT JOIN oc_x_supplier_type AS st ON st.supplier_type_id = o.supplier_type
LEFT JOIN oc_x_supplier AS st ON st.supplier_id = o.supplier_type
LEFT JOIN oc_x_pre_purchase_order_status AS os ON o.`status` = os.order_status_id
LEFT JOIN oc_user u on  o.added_by  = u.user_id
LEFT JOIN oc_product opp on  opp.product_id  = op.product_id
WHERE o.is_gift=1 AND o.with_gift =0 AND o.status != 3 AND o.related_order IN(".$purchase_orders.") GROUP BY o.purchase_order_id ORDER BY order_id ";
//        return $sql;
        $gift_orders = $db->query($sql)->rows;
        if (!empty($gift_orders)) {
            foreach ($gift_orders as $value) {
                $gift_purchase_orders .= ','.$value['order_id'];
            }
        }


        //返回数组集
        $return = array();
        //操作中间表集
        $sorting_array = array();

        //print_r($return['data']);

        //$last_one_day = date("Y-m-d", time() + 8*3600 - 24*3600);
        //获取入库中间表中已入库的商品，并从计划入库的商品中减去已入库的商品

        $sql = "SELECT
	xis.order_id,SUM(xis.quantity) quantity,xis.added_by
FROM
	oc_x_inventory_purchase_order_sorting AS xis
WHERE 1 = 1 
	 ";

            $sql  .= "  AND xis.order_id IN  (".$purchase_orders.$gift_purchase_orders.") GROUP BY xis.order_id ORDER BY xis.order_id";

//return $sql;
        $query = $db->query($sql);
        $result = $query->rows;
//return $result;



        if(sizeof($result)){
            foreach($result as $rk => $rv){
                $sorting_array[$rv['order_id']] = $rv;



//                $return_move_p = array();
//                if($return['data'][$rv['order_id']]['quantity'] > 0){
//                    $return['data'][$rv['order_id']]['quantity'] -= $rv['quantity'];
//                    if($return['data'][$rv['order_id']]['quantity'] <= 0){
//                        $return_move_p = $return['data'][$rv['order_id']];
//                        unset($return['data'][$rv['order_id']]);
//                        $return['data'][$rv['order_id']] = $return_move_p;
//                    }
//                    $return['data'][$rv['order_id']]['added_by'] = $rv['added_by'];
//
//
//
//                }
            }
        }
//        return $gift_orders;
        if (!empty($gift_orders)) {
            foreach ($gift_orders as $value) {
                $gift_orders[$value['order_id']] = $value;
                $sorting_quantity = intval($sorting_array[$value['order_id']]['quantity']);
                $plan_quantity = intval($value['plan_quantity']);
                if ($sorting_quantity > 0) {
                    $gift_orders[$value['order_id']]['added_by'] = $sorting_array[$value['order_id']]['added_by'];
                    $gift_orders[$value['order_id']]['quantity'] = $plan_quantity;
                    $gift_orders[$value['order_id']]['sort_num'] = $sorting_quantity;
                } else {
                    $gift_orders[$value['order_id']]['added_by'] = '';
                    $gift_orders[$value['order_id']]['quantity'] = $plan_quantity;
                    $gift_orders[$value['order_id']]['sort_num'] = 0;
                }
                $order_gifts[$value['related_order']][] = $gift_orders[$value['order_id']];
            }
        }
//return $order_gifts;

        //echo "<pre>";print_r($return);exit;
        //采购单数组集
        $sql = "SELECT
	o.purchase_order_id as order_id,
        o.station_id,
	o.`status` as order_status_id,
	os.`name` AS os_name,
	st.`name` AS st_name,
        o.order_comment,
	SUM(op.quantity) as plan_quantity , 
	 u.lastname ,
	 u.firstname ,
	o.need_delivery_service,
      DATEDIFF(NOW(), o.date_deliver) date_diff ,
      o.date_deliver,
         SUM(IF(opp.repack = 0,op.quantity,0) ) AS quantity_one, 
         SUM(IF(opp.repack = 1,op.quantity,0) ) AS quantity_two 

	 
FROM
	oc_x_pre_purchase_order AS o
LEFT JOIN oc_x_pre_purchase_order_product AS op ON o.purchase_order_id = op.purchase_order_id
-- LEFT JOIN oc_x_supplier_type AS st ON st.supplier_type_id = o.supplier_type
LEFT JOIN oc_x_supplier AS st ON st.supplier_id = o.supplier_type
LEFT JOIN oc_x_pre_purchase_order_status AS os ON o.`status` = os.order_status_id
LEFT JOIN oc_user u on  o.added_by  = u.user_id 
LEFT JOIN oc_product opp on  opp.product_id  = op.product_id

where 1=1 ";
//        if($data['purchase_order_id'] != '' ){
            $sql .= " AND o.purchase_order_id IN (" . $purchase_orders.") ";
//        } else {
//            if($date != ''){
//                $sql .=" and o.date_deliver = '" . $date . "'";
//            }
//            if($data['order_status_id'] != 0 ){
//                $sql .= " AND o.status = " . $data['order_status_id'];
//            }
//            if($handle_product){
//                $sql .= " AND oppp.product_id = " .$handle_product;
//            }
//        }

        $sql .= " GROUP BY o.purchase_order_id order by o.purchase_order_id asc";

        $query = $db->query($sql);
        $results = $query->rows;

//return $results;

        //echo "<pre>";print_r($results);

        if(sizeof($results)){
            foreach($results as $k=>$v){



                $return['data'][$v['order_id']] = $v;
//                $return['data'][$v['order_id']]['plan_quantity'] = $v['quantity'];
                $sorting_quantity = intval($sorting_array[$v['order_id']]['quantity']);
                $plan_quantity = intval($v['plan_quantity']);
                if ($sorting_quantity > 0) {
                    $return['data'][$v['order_id']]['quantity'] = $plan_quantity - $sorting_quantity;
                    $return['data'][$v['order_id']]['added_by'] = $sorting_array[$v['order_id']]['added_by'];
                } else {
                    $return['data'][$v['order_id']]['quantity'] = $plan_quantity;
                    $return['data'][$v['order_id']]['added_by'] = '';

                }
                $return['data'][$v['order_id']]['extend'] = $order_gifts[$v['order_id']];
//                $return['data'][$v['order_id']]['station_id'] = $v['station_id'];
            }
        }
        if(sizeof($return)){

            return $return;
        }
        else{
            return array();
        }


    }


    function getNotice($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Multi-language
        //TODO Station,Origin
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT notice_id, title FROM oc_x_notice WHERE status=1 AND station_id = '".$station_id."' AND now() BETWEEN date_start AND date_end ORDER BY notice_id DESC";

        if($id>0){
            $sql = "SELECT notice_id, title FROM oc_x_notice WHERE notice_id={$id} AND status=1 AND station_id = '".$station_id."' AND now() BETWEEN date_start AND date_end ORDER BY notice_id DESC";
        }
        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return false;
    }

    // 获取区域仓库notice
    function getNoticeWithWarehouse(array $data){
        global $db;

        $station_id     = !empty($data['station_id'])           ? (int)$data['station_id']           : 1;
        $language_id    = !empty($data['language_id'])          ? (int)$data['language_id']          : 2;
        $notice_id      = !empty($data['data']['notice_id'])    ? (int)$data['data']['notice_id']    : 0;
        $warehouse_id   = !empty($data['data']['warehouse_id']) ? (int)$data['data']['warehouse_id'] : 0;
        if($warehouse_id <= 0){ return array(); }

        $sql = "SELECT n.notice_id, n.title
                  FROM oc_x_notice n
                  LEFT JOIN oc_x_notice_to_warehouse nw ON n.notice_id = nw.notice_id
                  WHERE n.status = 1
                  AND now() BETWEEN n.date_start AND n.date_end";

        !empty($warehouse_id) && $sql .= " AND nw.warehouse_id = ".$warehouse_id;
        !empty($notice_id)    && $sql .= " AND n.notice_id = ".$notice_id;

        $sql    .= " ORDER BY n.notice_id DESC";
        $query   = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return array();
    }

    function getBanner($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Station
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT
        b.banner_id, b.name, b.banner_sort, bi.banner_image_id, bi.link, bi.image, bi.sort_order, bid.language_id, bid.title, bid.description
        FROM oc_banner b
        LEFT JOIN oc_banner_image bi ON (b.banner_id = bi.banner_id)
        LEFT JOIN oc_banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id)
        WHERE b.status=1 AND bid.language_id = {$language_id} AND b.station_id = {$station_id} AND now() BETWEEN b.date_start AND b.date_end";

        if($id>0){
            $sql = "SELECT
            b.banner_id, b.name, b.banner_sort, bi.banner_image_id, bi.link, bi.image, bi.sort_order, bid.language_id, bid.title, bid.description
            FROM oc_banner b
            LEFT JOIN oc_banner_image bi ON (b.banner_id = bi.banner_id)
            LEFT JOIN oc_banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id)
            WHERE b.banner_id = {$id} AND b.status=1 AND bid.language_id = {$language_id} AND b.station_id = {$station_id} AND now() BETWEEN b.date_start AND b.date_end";
        }
        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return array();
    }

    // 获取区域仓库banner
    function getBannerWithWarehouse(array $data){
        global $db;

        $station_id     = !empty($data['station_id'])           ? (int)$data['station_id']           : 1;
        $language_id    = !empty($data['language_id'])          ? (int)$data['language_id']          : 2;
        $banner_id      = !empty($data['data']['banner_id'])    ? (int)$data['data']['banner_id']    : 0;
        $warehouse_id   = !empty($data['data']['warehouse_id']) ? (int)$data['data']['warehouse_id'] : 0;
        if($warehouse_id <= 0){ return array(); }

        $sql = "SELECT
                  b.banner_id, b.name, b.banner_sort, bi.banner_image_id, bi.link, bi.image, bi.sort_order, bid.language_id, bid.title, bid.description
                  FROM oc_banner b
                  LEFT JOIN oc_banner_to_warehouse bw ON b.banner_id = bw.banner_id
                  LEFT JOIN oc_banner_image bi ON b.banner_id = bi.banner_id
                  LEFT JOIN oc_banner_image_description bid ON bi.banner_image_id = bid.banner_image_id
                  WHERE b.status = 1
                  AND bid.language_id = {$language_id}
                  AND bw.station_id = {$station_id}
                  AND now() BETWEEN b.date_start AND b.date_end";

        !empty($warehouse_id) && $sql .= " AND bw.warehouse_id = ".$warehouse_id;
        !empty($banner_id)    && $sql .= " AND b.banner_id = ".$banner_id;

        $query   = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return array();
    }

    function getCategory($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Station,Origin
        //TOOD 目前只处理一级目录
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT c.parent_id, c.status, cp.image, cpd.description, cpd.name parent_name, cp.sort_order parent_order, c.category_id, cd.name, c.sort_order
        FROM oc_category c
        LEFT JOIN oc_category cp ON c.parent_id = cp.category_id AND cp.status =1
        LEFT JOIN oc_category_description cd ON (c.category_id = cd.category_id and cd.language_id = {$language_id})
        LEFT JOIN oc_category_description cpd ON (cp.category_id = cpd.category_id and cpd.language_id = {$language_id})
        WHERE c.station_id = {$station_id}
        AND c.status = 1
        -- AND (c.category_id = 60 OR c.parent_id = 60)
        ORDER BY parent_order, c.sort_order";
        if($id>0){
            $sql = "SELECT c.parent_id, c.status, cp.image, cpd.name parent_name, cp.sort_order parent_order, c.category_id, cd.name, c.sort_order
        FROM oc_category c
        LEFT JOIN oc_category cp ON c.parent_id = cp.category_id AND cp.status =1
        LEFT JOIN oc_category_description cd ON (c.category_id = cd.category_id and cd.language_id = {$language_id})
        LEFT JOIN oc_category_description cpd ON (cp.category_id = cpd.category_id and cpd.language_id = {$language_id})
        WHERE c.station_id = {$station_id}
        AND c.status = 1
        AND (c.category_id = {$id} OR c.parent_id = {$id})
        ORDER BY parent_order, c.sort_order";
        }
        $query = $db->query($sql);
        $results = $query->rows;

        //整理目录树，目前只有两级，不可对数组排序
        if($results && sizeof($results)){
            $category = array();
            for($m=0;$m<sizeof($results);$m++){
                $pivot = $results[$m]['parent_id'] ? $results[$m]['parent_id'] : $results[$m]['category_id'];

                if($results[$m]['parent_id'] == 0){
                    $category[$pivot]['parent_id'] = $results[$m]['category_id'];
                    $category[$pivot]['image'] = $results[$m]['image'];
                    $category[$pivot]['desc'] = $results[$m]['description'];
                    $category[$pivot]['parent_name'] = $results[$m]['name'];
                }
                else{
                    $category[$pivot]['child'][] = $results[$m];
                }
            }

            return $category;
        }

        return false;
    }

    function orderStatus($order_id, $status_id, $user_id=0, $reason_id=0, $comment=''){
        global $dbm;

        //TODO 取消订单退余额

        $sql="update oc_order set order_status_id = {$status_id} where order_id = '{$order_id}'";

        $bool = true;
        $bool = $bool && $dbm->query($sql);

        if($bool){
            //SUCCESS
            //$this->addOrderHistory($order_id,'后台取消');
            $this->addOrderHistory($order_id,$user_id,$reason_id,$comment);

            //Add MSG Tasks
            //Get status setting, insert into msg
            $sql = "
                INSERT INTO `oc_msg` (`merchant_id`, `customer_id`, `phone`, `order_id`, `msg_param_1`, `msg_param_2`, `isp_template_id`, `msg_type`, `msg_status_id`,`msg_status_name`,`sent`, `status`, `date_added`)
                SELECT 0, o.customer_id, o.shipping_phone, ".$order_id.", '".$order_id."', st.contact_phone, mt.isp_template_id, mt.msg_type, o.order_status_id, os.name, 0, 1, NOW()
                FROM oc_order o
                LEFT JOIN oc_order_status os ON o.order_status_id = os.order_status_id AND os.language_id = 2
                LEFT JOIN oc_x_station st ON o.station_id = st.station_id
                LEFT JOIN oc_msg_template mt ON os.msg_template_id = mt.msg_template_id
                LEFT JOIN oc_customer c ON o.customer_id = c.customer_id
                WHERE
                o.order_id = '".$order_id."' AND o.order_status_id = '".$status_id."'
                AND os.msg = 1 AND c.accept_order_message = 1
                ";
            return $dbm->query($sql);
        }

        return false;
    }

    public function deliverStatus($order_id, $status_id, $user_id){
        global $dbm;

        $sql="update oc_order set order_deliver_status_id = {$status_id} where order_id = '{$order_id}'";
        $bool = true;
        $bool = $bool && $dbm->query($sql);

        if($bool){
            //SUCCESS
            $this->addOrderHistory($order_id,$user_id);

            //Add MSG Tasks
            //Get status setting, customer accept setting insert into msg
            $sql = "
                INSERT INTO `oc_msg` (`merchant_id`, `customer_id`, `phone`, `order_id`, `msg_param_1`, `msg_param_2`, `isp_template_id`, `msg_type`, `msg_status_id`,`msg_status_name`,`sent`, `status`, `date_added`)
                SELECT 0, o.customer_id, o.shipping_phone, ".$order_id.", '".$order_id."', if(o.shipping_code='D2D' or o.order_deliver_status_id=5, st.contact_phone, ps.close_time), mt.isp_template_id, mt.msg_type, o.order_deliver_status_id, os.name, 0, 1, NOW()
                FROM oc_order o
                LEFT JOIN oc_order_deliver_status os ON o.order_deliver_status_id = os.order_deliver_status_id AND os.language_id = 2
                LEFT JOIN oc_x_pickupspot ps ON o.pickupspot_id = ps.pickupspot_id
                LEFT JOIN oc_x_station st ON o.station_id = st.station_id
                LEFT JOIN oc_msg_template mt ON os.msg_template_id = mt.msg_template_id
                LEFT JOIN oc_customer c ON o.customer_id = c.customer_id
                WHERE
                o.order_id = '".$order_id."' AND o.order_deliver_status_id = '".$status_id."'
                AND os.msg = 1 AND c.accept_order_message = 1
                ";
            return $dbm->query($sql);
        }

        return false;

    }

    function addOrderHistory($order_id,$user_id=0, $reason_id=0, $comment=false){
        global $dbm;

        if(!$comment){
            $comment = '';
        }

        $sql = "INSERT INTO  oc_order_history (`order_id`, `notify`, `reason_id`, `comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`)
SELECT '{$order_id}', '0','{$reason_id}', '{$comment}', NOW(), order_status_id, order_payment_status_id, order_deliver_status_id, '{$user_id}' FROM  oc_order WHERE order_id = {$order_id}";

        $dbm->query($sql);
    }

    function randomkeys($length,$pattern)
    {
        //$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ<>?;#:@~[]{}-_=+)(*&^%$"!'; //Char
        //$pattern = $_REQUEST["pattern"];
        $key = '';

        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,(strlen($pattern)-1))};  //mt_rand(), retrun random int
        }
        return $key;
    }

    function sendMsg(array $data){
        global $dbm, $db;
        $post = $data['data'];
        $phone = isset($post['phone']) ? $post['phone'] : '';
        $type  = isset($post['type']) ? $post['type'] : '';
        if(empty($phone) || !preg_match('/1\d{10}/', $phone)){
            return array(
                'return_code'  => 'FAIL',
                'msg'          => '请输入正确的手机号码'
            );
        }

        if(empty($type)){
            return array(
                'return_code' => 'FAIL',
                'msg'         => '请输入正确的类型'
            );
        }

        if($type == 'reg'){
            $query = $db->query("SELECT telephone FROM oc_customer WHERE telephone='{$phone}'");
            if($query->num_rows){
                return array(
                    'return_code' => 'FAIL',
                    'msg'         => '此号码已注册'
                );
            }
        }
        if($type == 'pwd_reset'){
            $query = $db->query("SELECT telephone FROM oc_customer WHERE telephone='{$phone}'");
            if(!$query->num_rows){
                return array(
                    'return_code' => 'FAIL',
                    'msg'         => '此号码尚未注册'
                );
            }
        }

        $random = $this->randomkeys(6, '123456789');
        $time = time()+STATION_LOGIN_YTX_SMS_CODE_LIFE*60; //有效期5分钟(300秒)
        $returnCode = $random;

        $query_history = $db->query("SELECT phone, code, expiration FROM oc_x_msg_valid WHERE phone='{$phone}'");
        $msgInfo = $query_history->row;

        if($query_history->num_rows){
            if($msgInfo['expiration'] > time()){
                $returnCode = $msgInfo['code'];
            }
            else{
                $dbm->query("UPDATE oc_x_msg_valid SET code='{$random}', expiration='{$time}' WHERE phone='{$phone}'");
            }
        }else{
            $dbm->query("INSERT INTO oc_x_msg_valid SET phone='{$phone}', code='{$random}', expiration='{$time}'");
        }

        //异步发送短信
        //$dbm->query("INSERT INTO oc_msg SET phone='" . $phone . "', msg_param_1='" . $random . "', msg_param_2='5', isp_template_id='" . ISP_TEMPLATE_ID_REG . "', date_added=NOW()");

        return array(
            'return_code' => 'SUCCESS',
            'wait_second' => '60',
            'phone' => $phone,
            'random' =>$returnCode
        );

    }

    //DO单分配
    function auto_order_distr($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;
        global $orders_id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $data = json_decode($data, 2);
//return $data;
        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        $warehouse_repack = isset($data['warehouse_repack']) ? trim($data['warehouse_repack']) : false;
        $warehouse_id = isset($data['warehouse_id']) ? trim($data['warehouse_id']) : false;
        $user_repack1 = isset($data['user_repack']) ? trim($data['user_repack']) : false;
//        return $order_id;

        $inventory_name = isset($data['inventory_name']) ? trim($data['inventory_name']) : false;
        if (!$inventory_name) {
            return 0;
        }

        $sql = "SELECT repack FROM oc_w_user  WHERE username = '".$inventory_name."' ";
        $query = $db->query($sql);
        $user_repack = $query->row;
        if ($data['warehouse_repack'] == 1) {
            if ($user_repack['repack'] != $user_repack1) {
                return 0;
            }
        }
        $product_id = isset($data['product_id']) ? trim($data['product_id']) : false;
//do单分类
        if (!$product_id) {
            return 0;
        } else if ($product_id==5001) {
            $ordclass = 2 ;
        } else if ($product_id==5002) {
            $ordclass = 3 ;
        } else if ($product_id==5003) {
            $ordclass = 4 ;
        } else if ($product_id==5004) {
            $ordclass = 5 ;
        } else if ($product_id==5005) {
            $ordclass = 6 ;
        } else if ($product_id==5006) {
            $ordclass = 7 ;
        } else {
            $ordclass = 1 ;
        }


//查询do单状态以及整散
        $sql = "SELECT odo.order_status_id,odo.is_repack,odo.order_id AS old_order_id,SUM(odop.quantity) AS quantity
FROM oc_x_deliver_order odo
LEFT JOIN oc_x_deliver_order_product odop ON odop.deliver_order_id = odo.deliver_order_id
WHERE odo.deliver_order_id = '".$order_id ."'";
//        return $sql;
        $query = $db->query($sql);
        $order_status = $query->row;
        $old_order_id = $order_status['old_order_id'];
        $quantity = $order_status['quantity'];
//如果不是已确认不能进行分配
        if ($order_status['order_status_id'] != 2 ) {
            return 2;
        } else {

//判断是否是分区分拣，如果是，判断分配的订单是否对应相应的分拣人员
            if ($warehouse_repack == 1) {
                if ($order_status['is_repack'] != $user_repack['repack']) {
                    return 0;
                }
            }
            /*zx
            找出该分拣人最近分出的五百个单子，判断是否都已完成，如有为未完成的，不能领单*/
            $sql = "SELECT GROUP_CONCAT(odo.order_status_id) AS order_status
FROM oc_order_distr ood 
LEFT JOIN oc_x_deliver_order odo ON odo.deliver_order_id = ood.deliver_order_id
WHERE ood.inventory_name = '".$inventory_name."' 
ORDER BY ood.deliver_order_id DESC 
LIMIT 0,500";
            $query = $db->query($sql);
            $order_status1 = $query->rows;
            $dont_order_status = explode(',',$order_status1[0]['order_status']);
            if (in_array(4,$dont_order_status) || in_array(5,$dont_order_status)) {
                return 3;
            }
            /*zx
            非生鲜仓*/
            $sql = "SELECT odo.deliver_order_id 
FROM oc_order_distr odo 
WHERE odo.deliver_order_id = '".$order_id."'";
            $query = $db->query($sql);
            $order_status2 = $query->row;
            if ($order_status2) {
                return 4;
            }
            $sql = "INSERT INTO oc_order_distr 
(order_id, deliver_order_id,inventory_name, ordclass,quantity,warehouse_id,warehouse_repack, user_repack)
VALUES ('$old_order_id','$order_id','$inventory_name','$ordclass','$quantity','".$warehouse_id."','".$warehouse_repack."','".$user_repack1."')";
            $sql1 = "UPDATE oc_x_deliver_order SET order_status_id = 4 WHERE deliver_order_id = '".$order_id."'";
            $query1 = $db->query($sql1);
            $query = $db->query($sql);
            return 1;
        }

    }

    function checkWarehouseIsDc($warehouse_id){
        global $db;
        $sql = "SELECT  warehouse_id ,title,station_id,is_dc  FROM  oc_x_warehouse  where  warehouse_id = '".$warehouse_id."'";

        $query = $db->query($sql);
        $result = $query->row;
        $warehouse_is_dc = intval($result['is_dc']);
        if (in_array($warehouse_is_dc,[1])) {
            return true;
        } else {
            return false;
        }

    }


}
$oldwarehouse = new OLDWAREHOUSE();
?>