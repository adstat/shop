<?php

require_once(DIR_SYSTEM . 'db.php');
require_once(DIR_SYSTEM . '/redis.php');
require_once('customer.php');
require_once('order.php');
require_once('common.php');
require_once('product.php');

class INVENTORY {

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

    private function newRedis()
    {
        $redis = new MyRedis();
        $redis->selectdb(1);
        return $redis;
    }

    function getStockKey($warehouseId, $productId){
        $keyPrefix = REDIS_STOCK_KEY_PREFIX ? REDIS_STOCK_KEY_PREFIX : 'stock';
        $key       = $keyPrefix.':'.$warehouseId.':'.$productId; //stock : warehouseId : productId
        return $key;
    }

    function deCodeProductBatch($products) {
        //Expect format: json_decode('{"products":{"150612001002003450":1,"150612001028001480":2}}', 2) => array()
        //Output: $product_info
        global $log;

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

    function inventoryProcessProduct($data, $station_id, $language_id, $origin_id) {
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

        return $this->addInventoryMoveOrder($data_inv, $station_id);
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

    function addInventoryMoveOrder($data, $station_id) {
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
            $sql = "INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`)
                    VALUES('".$station_id."', current_date(), unix_timestamp(), '0', '".$inventory_type."', now(), '".$data_insert['added_by']."', '".$data_insert['add_user_name']."', '[API]".$data_insert['memo']."')";
            $bool = $bool && $dbm->query($sql);
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动操作SQL：'.$sql);
            $inventory_move_id = $dbm->getLastId();
            //$inventory_move_id = 999;
            $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`) VALUES';

            $m = 0;
            foreach ($data['products'] as $product) {
                //处理散件退货商品 - 散件暂时不退货可售库存
                //TODO 散件售卖
                $returnInvqty = $product['qty'];
                if(isset($product['box_quantity']) && $product['box_quantity'] > 1){
                    $returnInvqty = 0;
                }
                $sql .= "('".$inventory_move_id."','".$station_id."','".$product['product_id']."','".$returnInvqty*$inventory_op ."')";
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

        if (!isset($dataInv['date'])) {
            return false;
        }

        $sql = "SELECT
	op.product_id,p.weight,p.weight_range_least,p.weight_range_most
FROM
	oc_order_product AS op
LEFT JOIN oc_order AS o ON o.order_id = op.order_id
left join oc_product as p on p.product_id = op.product_id
WHERE
	op.weight_inv_flag = 1
AND o.deliver_date = '" . $dataInv['date'] . "'
group by op.product_id";

        $query = $db->query($sql);
        $result = $query->rows;
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

    
    public function adjust_post($data_inv){

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
            
            $dbm->query("INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`) VALUES('2', '{$date}', '{$time}', '1', '" . INVENTORY_TYPE_PRESET . "', '{$date_added}', '{$user_id}', '{$user_name}', '重置预设库存为0')");
        $inventory_move_id = $dbm->getLastId();
            
        $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`) VALUES';

            foreach($preset_fastpin_product_arr as $key=>$product){
                if(in_array($key, $fastpin_arr_in)){
                    $sql .= "('{$inventory_move_id}', '2', '{$key}', '{$product}'),";

                }
            }
            if(substr($sql, strlen($sql)-1,1) == ','){
                $sql = substr($sql, 0,-1);
            }


            $dbm->query($sql);
        }
        
        $dbm->query("INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`) VALUES('2', '{$date}', '{$time}', '1', '" . INVENTORY_TYPE_STOCK_IN . "', '{$date_added}', '{$user_id}', '{$user_name}', '{$comment}')");
        $inventory_move_id = $dbm->getLastId();
        $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`) VALUES';

        foreach($products as $key=>$product){
            if(in_array($key, $fastpin_arr_in)){
                $sql .= "('{$inventory_move_id}', '2', '{$key}', '{$product}'),";
                
            }
        }
        if(substr($sql, strlen($sql)-1,1) == ','){
            $sql = substr($sql, 0,-1);
        }
        
        
        
        $dbm->query($sql);
        
        $dbm->query("update oc_product set status = 1 where product_id in (" . $fastpin_id_str . ")");
        
        $dbm->query('COMMIT');

        
    }
    
    
    
    
    

    public function inventoryOutProduct($data, $station_id, $language_id = 2, $origin_id) {

        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryOutProduct'; //Up






        $result = $this->inventoryProcessProduct($data_inv, $station_id, $language_id, $origin_id);

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



        $result = $this->inventoryProcessProduct($data_inv, $station_id, $language_id, $origin_id);

        if ($result) {
            return array(array('status' => (int) $result, 'timestamp' => $data_inv['timestamp']));
        } else {
            return array(array('status' => 0, 'timestamp' => $data_inv['timestamp']));
        }
    }

    public function inventoryCheckProduct($data, $station_id, $language_id = 2, $origin_id) {

        global $dbm;
        $data_inv = json_decode($data, 2);

        $sql = "insert into oc_x_inventory_check_sorting (product_id,quantity,uptime,added_by) "
                . "values ";
        $i = 1;
        foreach ($data_inv['products'] as $product_id => $product_quantity) {
            if ($i == count($data_inv['products'])) {
                $sql .= "(" . $product_id . "," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "')";
            } else {
                $sql .= "(" . $product_id . "," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "'),";
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
        
        $sql = "insert into oc_x_inventory_check_single_sorting (product_id,inv_quantity,quantity,uptime,added_by,remark,remark_2,move_flag) "
                . "values ";
        $i = 1;
        $error = 0;
        foreach ($data_inv['products'] as $product_id => $product_quantity) {
            
          
            
            $sql2 = "select * from oc_x_inventory_check_single_sorting where product_id = " . $product_id . " and move_flag = 0 and uptime > '" . $date . " 00:00:00' and uptime < '" . $date . " 24:00:00'";
            
            $query = $dbm->query($sql2);
            $result = $query->rows;
            if(!empty($result)){
                $error = 1;
                break;
            }
            
            $move_flag = 0;
            if($data_inv['products_inv'][$product_id] == $product_quantity){
                $move_flag = 1;
            }
            
            if ($i == count($data_inv['products'])) {
                $sql .= "(" . $product_id . "," . $data_inv['products_inv'][$product_id] . "," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "','" . $data_inv['remark'] . "','" . $data_inv['remark_2'] . "'," . $move_flag . ")";
            } else {
                $sql .= "(" . $product_id . "," . $data_inv['products_inv'][$product_id] . "," . $product_quantity . ",now(),'" . $data_inv['add_user_name'] . "','" . $data_inv['remark'] . "','" . $data_inv['remark_2'] . "'," . $move_flag . "),";
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


    public function newGetProductInventory($data = array()){
        global $db, $product;

        $customer_id    = !empty($data['customer_id'])          ? (int)$data['customer_id']             : 0;
        $station_id     = !empty($data['station_id'])           ? (int)$data['station_id']              : 0;
        $warehouse_id   = !empty($data['data']['warehouse_id']) ? (int)$data['data']['warehouse_id']    : 0;
        $product_ids    = !empty($data['data']['product_ids'])  ? $data['data']['product_ids']             : array();

        if($warehouse_id <= 0){
            return array('status' => 'ERROR', 'message' => 'No warehouse id', 'data' => array());
        }

        $result = array();
        if (sizeof($product_ids)) {
            $sql = "SELECT
                    A.product_id,
                    ABS(SUM(IF(A.status = 1, A.quantity, 0))) customer_ordered_today,
                    ABS(SUM(IF(A.status = 0, A.quantity, 0))) customer_ordered_tmr
                    FROM oc_x_inventory_move_item A
                    WHERE A.status = 1
                    AND A.customer_id = {$customer_id}
                    AND A.warehouse_id = {$warehouse_id}
                    AND A.station_id = {$station_id}
                    AND A.product_id IN (". implode(',', $product_ids) .")
                    GROUP BY A.product_id";
            $query          = $db->query($sql);
            $move_result    = $query->rows;
            $customer_order = array();
            if(!empty($move_result)){
                foreach($move_result as $val){
                    $customer_order[$val['product_id']]['today'] = $val['customer_ordered_today'];
                    $customer_order[$val['product_id']]['tmr']   = $val['customer_ordered_tmr'];
                }
            }

            $redis  = $this->newRedis();
            foreach($product_ids as $key => $product_id){
                $result[$key]['product_id']             = $product_id;
                $result[$key]['customer_ordered_today'] = 0;
                $result[$key]['customer_ordered_tmr']   = 0;
                if(!empty($customer_order[$product_id])) {
                    $result[$key]['customer_ordered_today'] = $customer_order[$product_id]['today'];
                    $result[$key]['customer_ordered_tmr']   = $customer_order[$product_id]['tmr'];
                }

                $stockKey = $this->getStockKey($warehouse_id, $product_id);
                if( $redis->exists($stockKey) ){
                    $result[$key]['inventory'] = $redis->get($stockKey);
                } else {
                    $result[$key]['inventory'] = $product->getProductStock($warehouse_id, $product_id);
                }
            }
        }

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
        if (!$order_id) {
            return false;
        }

        $sql = "select station_id from oc_order where order_id = '".$order_id."'";
        $query = $db->query($sql);
        $stationInfo = $query->row;




        //获取订单的所有商品
        $sql = "SELECT
            op.order_product_id, op.order_id, op.product_id, op.weight_inv_flag, op.name, op.model, op.quantity, op.price, op.total, op.tax, op.reward, op.price_ori, op.retail_price, op.is_gift, op.shipping, op.status,
            lpp.barcode,p.repack,p.station_id,p.inv_class,p.sku,p.inv_class_sort,p.storage_mode_id,ptc.category_id,p.product_type,p.product_type_id
        FROM
            oc_order_product AS op
        LEFT JOIN oc_product AS p ON p.product_id = op.product_id
        left join oc_product_to_category as ptc on p.product_id = ptc.product_id
        left join labelprinter.productlist as lpp on lpp.product_id = op.product_id
        WHERE
            op.order_id = " . $order_id . "

        ORDER BY
            p.inv_class ASC,
                p.inv_class_sort ASC,
            op.product_id ASC";

        //TODO 快消品重新排序
        if($stationInfo['station_id'] == 2){
            $sql = "SELECT
                        op.order_product_id, op.order_id, op.product_id, op.weight_inv_flag, op.name, op.model, op.quantity, op.price, op.total, op.tax, op.reward, op.price_ori, op.retail_price, op.is_gift, op.shipping, op.status,
                        '' barcode,p.repack,p.station_id,p.inv_class,p.sku,p.inv_class_sort,p.storage_mode_id,ptc.category_id,p.product_type,p.product_type_id
                    FROM
                        oc_order_product AS op
                    LEFT JOIN oc_product AS p ON p.product_id = op.product_id
                    left join oc_product_to_category as ptc on p.product_id = ptc.product_id
                    WHERE
                        op.order_id = " . $order_id . "

                    ORDER BY
                        p.inv_class_sort_order asc,
                        p.inv_class_sort asc";
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

        $sql = "SELECT `inventory_sorting_id`, `order_id`, `product_id`, `quantity`, `uptime`, `move_flag`, `added_by`, `product_barcode` FROM oc_x_inventory_order_sorting  where order_id = '" . $order_id . "'";
        $query = $db->query($sql);
        $result = $query->rows;

        if (sizeof($result)) {
            foreach ($result as $rk => $rv) {
                $return_move_p = array();
                $return['data'][$rv['product_id']]['quantity'] -= $rv['quantity'];

                if($return['data'][$rv['product_id']]['repack'] == 0){
                    $return['data'][$rv['product_id']]['boxCount'] += $rv['quantity'];
                }

                //TODO 依赖product_barcode字段格式
                $return['data'][$rv['product_id']]['product_barcode_arr'] = array_merge($return['data'][$rv['product_id']]['product_barcode_arr'], json_decode($rv['product_barcode']));

                if ($return['data'][$rv['product_id']]['quantity'] <= 0) {
                    $return_move_p = $return['data'][$rv['product_id']];
                    unset($return['data'][$rv['product_id']]);
                    $return['data'][$rv['product_id'] * 1000000] = $return_move_p; //TODO "*1000000" ? WTF
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

            $sql = "select order_status_id from oc_order where order_id = " . $order_id;
            $query = $db->query($sql);
            $order_status = $query->row;

            if ($order_status['order_status_id'] == 6) {

                return array("status" => 6);
            }

            $sql = "update oc_order set order_status_id = 5 where order_id = " . $order_id;
            $dbm->query($sql);

            //添加订单历史记录
            $this->addOrderSortingHistory($order_id);
        }

        return $return_arr;
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
        if (!$order_id) {
            return false;
        }





//获取订单的所有商品
        $sql = "SELECT
	op.*,lpp.barcode,p.inv_class,p.sku,p.inv_class_sort,p.storage_mode_id,ptc.category_id,p.product_type   
FROM
	oc_x_pre_purchase_order_product AS op
LEFT JOIN oc_product AS p ON p.product_id = op.product_id
-- left join (select * from oc_product_to_category group by product_id) as ptc on p.product_id = ptc.product_id
left join oc_product_to_category as ptc on p.product_id = ptc.product_id
left join labelprinter.productlist as lpp on lpp.product_id = op.product_id 
WHERE
	op.purchase_order_id = " . $order_id . "
ORDER BY
	p.inv_class ASC,
	op.product_id ASC";

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

        $sql = "SELECT xis.* FROM oc_x_inventory_purchase_order_sorting AS xis where xis.order_id = '" . $order_id . "' ";

        $query = $db->query($sql);
        $result = $query->rows;
        if (sizeof($result)) {
            foreach ($result as $rk => $rv) {
                $return_move_p = array();
                $return['data'][$rv['product_id']]['quantity'] -= $rv['quantity'];
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
            } elseif (strlen($product) == 4) {

                $product_id = $product;
            } else {
                $product_id = 0;
            }

            $product_ids[] = $product_id;
        }

        //  IF ( A.unit_size IS NULL, B.name, concat( B.name,'[',cast(A.unit_size AS signed),']')) AS name,
        
        $sql = "select
            A.product_id,
            IF (A.inv_size>0 and A.repack=1,concat(B.name,'[',A.inv_size,']'), B.name) AS name,
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
            from xsjb2b.oc_product A
            left join xsjb2b.oc_product_description B on A.product_id = B.product_id and B.language_id = 2
            left join xsjb2b.oc_weight_class_description C on A.weight_class_id = C.weight_class_id and C.language_id = 2
            left join  xsjb2b.oc_product_special D on (A.product_id = D.product_id and now() between D.date_start and D.date_end)
            left join xsj.oc_product_promo E on A.product_id = E.product_id
            left join xsjb2b.oc_product_inv_class PC on A.inv_class = PC.product_inv_class_id
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

        $data = json_decode($data, 2);

        //$date = isset($data['date']) ? $data['date'] : false;
        $product_id = isset($data['product_id']) ? $data['product_id'] : false;
        if (!$product_id) {
            return false;
        }

        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        if (!$order_id) {
            return false;
        }


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

        //默认返回状态0，成功返回状态1
        $return['status'] = 0;

        $sql = "insert into oc_x_inventory_order_sorting (product_id,order_id,quantity,uptime,added_by,product_barcode) "
                . "values (" . $product_id . "," . $order_id . "," . $data['product_quantity'] . ",now(),'" . $data['inventory_user'] . "','" . json_encode($data['product_barcode_arr']) . "')";
        $result = $dbm->query($sql);

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
            where ios.order_id = '".$order_id."'";
            $query = $dbm->query($sql);
            $result = $query->row;

            $return['qtyCount'] = $result['qtyCount'];
            $return['boxCount'] = $result['boxCount'];
            $return['repackCount'] = $result['repackCount'];
        }

        return $return;
    }

    
    public function addPurchaseOrderProductStation($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;

        $data = json_decode($data, 2);

        //$date = isset($data['date']) ? $data['date'] : false;
        $product_id = isset($data['product_id']) ? $data['product_id'] : false;
        if (!$product_id) {
            return false;
        }

        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        if (!$order_id) {
            return false;
        }


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


        $sql = "insert into oc_x_inventory_purchase_order_sorting (product_id,order_id,quantity,uptime,added_by,product_barcode) "
                . "values (" . $product_id . "," . $order_id . "," . $data['product_quantity'] . ",now(),'" . $data['inventory_user'] . "','" . json_encode($data['product_barcode_arr']) . "')";


        //$log->write($sql."\n\r");

        $query = $dbm->query($sql);
        $return['status'] = 1;
        return $return;
    }

    public function addOrderProductToInv_pre($data, $station_id, $language_id = 2, $origin_id) {
        global $db;

        $data = json_decode($data, 2);

        //判断中间库中的商品数量是否满足95% 如果不满足则不能提交
        $sql = "SELECT sum(quantity) as quantity FROM oc_x_inventory_order_sorting AS xis where xis.move_flag = 0 and xis.order_id = '" . $data['order_id'] . "' ";
        $query = $db->query($sql);
        $result = $query->row;


        $sql = "SELECT
                    o.order_id,os.`name`,SUM(op.quantity) as quantity
                FROM
                    oc_order_product AS op
                LEFT JOIN oc_order AS o ON o.order_id = op.order_id
                LEFT JOIN oc_order_status AS os ON os.order_status_id = o.order_status_id
                AND o.language_id = os.language_id
                WHERE op.order_id = " . $data['order_id'];
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

        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;
        if (!$order_id) {
            return false;
        }

        //TODO 待强制验证货位号
        //在快消仓提交订单时先写入整箱和货位号
        $inv_comment = isset($data_inv['invComment']) ? (int)$data_inv['invComment'] : false;
        $box_count= isset($data_inv['boxCount']) ? (int)$data_inv['boxCount'] : false;

//        if($inv_comment){
//            $sql = "select order_id from oc_order_inv where order_id = '".$order_id."'";
//            $query = $dbm->query($sql);
//            if($query->num_rows) {
//                $sql="insert into oc_order_inv(order_id, inv_comment, inv_status, uptime)
//                    values ('".$order_id."','".$inv_comment."',9 , now())";
//                if($box_count){
//                    $sql="insert into oc_order_inv(order_id, box_count, inv_comment, inv_status, uptime)
//                    values ('".$order_id."','".$box_count."','".$inv_comment."',9 , now())";
//                }
//            }
//            else{
//                $sql = "update oc_order_inv set inv_comment='".$inv_comment."' where order_id = '".$order_id."'";
//                if($box_count){
//                    $sql = "update oc_order_inv set box_count='".$box_count."', inv_comment='".$inv_comment."' where order_id = '".$order_id."'";
//                }
//            }
//            //$log->write('[分拣]记录或更新货位号[' . __FUNCTION__ . ']'.$sql."\n\r");
//            $dbm->query($sql);
//        }

        //提交订单为待审核
        $userPendingCheck = isset($data_inv['userPendingCheck']) ? $data_inv['userPendingCheck'] : false;
        if($userPendingCheck){
            $sql = "update oc_order set order_status_id = 8 where order_id = '".$order_id."'";
            $dbm->query($sql);
            return array('status' => 8, 'timestamp' => $sql);
        }

        /*
          $sql = "update oc_x_inventory_order_sorting set move_flag = 1 where order_id = " . $data_inv['order_id'];
          $query = $dbm->query($sql);


          $sql = "update oc_order set order_status_id = 6 where order_id = " . $data_inv['order_id'];
          $dbm->query($sql);

          return array('status'=>1,'timestamp'=>$data_inv['timestamp']);
          exit;
         */

        //验证订单分拣是否已提交
        $sql = "select xsm.order_id,o.station_id from oc_x_stock_move as xsm left join oc_order as o on o.order_id = xsm.order_id where xsm.inventory_type_id = 12 and xsm.order_id = " . $order_id;
        $query = $dbm->query($sql);
        $result_exists = $query->row;
        
        if($query->num_rows){
            //若订单已有出货库存扣减数据且状态为分拣中或待审核，将状态改为已拣完
            $sql = "update oc_order set order_status_id = 6 where order_id = '".$order_id."' and order_status_id in (5,8)";
            $dbm->query($sql);

            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);
        }


        $sql = "select xis.*,lpp.barcode,op.price,op.weight_inv_flag,p.sku_id from oc_x_inventory_order_sorting as xis left join labelprinter.productlist as lpp on xis.product_id = lpp.product_id left join oc_order_product as op on op.order_id = xis.order_id and op.product_id = xis.product_id left join oc_product as p on p.product_id = xis.product_id where xis.move_flag=0 and xis.order_id = " . $order_id;
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

            $result = $this->addInventoryMoveOrder($data_inv, 1);
            if ($result && !empty($update_sorting_id_arr)) {
                $update_sorting_id_str = implode(",", $update_sorting_id_arr);
                $sql = "update oc_x_inventory_order_sorting set move_flag = 1 where inventory_sorting_id in (" . $update_sorting_id_str . ")";
                $log->write($sql);
                $query = $dbm->query($sql);
            }
        }





        if ($result || $no_sorting) {

            $sql = "update oc_order set order_status_id = 6 where order_id = " . $data_inv['order_id'];
            $dbm->query($sql);

            /*
              $sql = "update oc_order_inv set inv_status = 0 where order_id = " . $data_inv['order_id'];
              $dbm->query($sql);

              $data_inv['frame_count'] = $data_inv['frame_count'] ? $data_inv['frame_count'] : 0;
              $data_inv['incubator_count'] = $data_inv['incubator_count'] ? $data_inv['incubator_count'] : 0;
              $sql = "insert into oc_order_inv(order_id,frame_count,incubator_count,inv_comment) values(" . $data_inv['order_id'] . "," . $data_inv['frame_count'] . "," . $data_inv['incubator_count'] . ",'" . $data_inv['inv_comment'] . "');";
              $dbm->query($sql);
             */


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
        $data_inv['api_method'] = 'inventoryOrderIn'; //Up

        //无订单信息，不可提交
        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;
        if (!$order_id) {
            return false;
        }

        //TODO 待强制验证货位号
        //在快消仓提交订单时先写入整箱和货位号
        $inv_comment = isset($data_inv['invComment']) ? (int)$data_inv['invComment'] : false;
        $box_count= isset($data_inv['boxCount']) ? (int)$data_inv['boxCount'] : 0;
        $frame_count= isset($data_inv['frame_count']) ? (int)$data_inv['frame_count'] : 0;
        $frame_vg_list= isset($data_inv['frame_vg_list']) ? $data_inv['frame_vg_list'] : '';

        //默认先记录货位号
        if($inv_comment){
            $sql = "select order_id from oc_order_inv where order_id = '".$order_id."'";
            $query = $dbm->query($sql);
            if($query->num_rows) {
                $sql = "update oc_order_inv set box_count='".$box_count."', inv_comment='".$inv_comment."', frame_count='".$frame_count."', frame_vg_list= '".$frame_vg_list."' where order_id = '".$order_id."'";
            }
            else{
                $sql="insert into oc_order_inv(order_id, box_count, inv_comment, frame_count, frame_vg_list, inv_status, uptime) values ('".$order_id."','".$box_count."','".$inv_comment."','".$frame_count."','".$frame_vg_list."',1 , now())";
            }
            $log->write('[分拣]记录或更新货位号[' . __FUNCTION__ . ']'.$sql."\n\r");
            $dbm->query($sql);
        }

        //验证订单分拣是否已提交, inventory_type_id=12为分拣出库，
        //TODO 可能有退货问题
        $sql = "select xsm.order_id from oc_x_stock_move as xsm where xsm.inventory_type_id = 12 and xsm.order_id = '".$order_id."'";
        $query = $dbm->query($sql);
        $query->row;
        if($query->num_rows){
            //若订单已有出货库存扣减数据且状态为分拣中或待审核，将状态改为已拣完
            $sql = "update oc_order set order_status_id = 6 where order_id = '".$order_id."' and order_status_id in (5,8)";
            if($dbm->query($sql)){
                $log->write('[分拣]已扣减库存更新订单状态为已分拣[' . __FUNCTION__ . ']'.$sql."\n\r");
            }

            $sql = "update oc_x_inventory_order_sorting set move_flag = 1 where move_flag=0 and order_id = '".$order_id."'";
            if($dbm->query($sql)){
                $log->write('[分拣]已扣减库存提交分拣数据[' . __FUNCTION__ . ']'.$sql."\n\r");
            }

            return array('status' => 4, 'timestamp' => $data_inv['timestamp']);
        }

        //提交订单为待审核
        $userPendingCheck = isset($data_inv['userPendingCheck']) ? $data_inv['userPendingCheck'] : false;
        if($userPendingCheck){
            $sql = "update oc_order set order_status_id = 8 where order_id = '".$order_id."'";
            if($dbm->query($sql)){
                $log->write('[分拣]提交订单为待审核[' . __FUNCTION__ . ']'.$sql."\n\r");

                //添加订单历史记录
                $this->addOrderSortingHistory($order_id);

            }
            return array('status' => 8, 'timestamp' => $sql);
        }

        //获取库存数据，准备扣减库存
        $sql = "select xis.product_id, xis.quantity, op.price, op.quantity order_quantity, op.weight_inv_flag, p.sku_id
            from oc_x_inventory_order_sorting as xis
            left join oc_order_product as op on op.order_id = xis.order_id and op.product_id = xis.product_id
            left join oc_product as p on p.product_id = xis.product_id
            where xis.move_flag=0 and xis.order_id = '".$order_id."'";
        $query = $dbm->query($sql);
        $result = $query->rows;

        $stationProductMove = array();
        if(sizeof($result)) {
            foreach ($result as $k => $v) {
                $stationProductMove[] = array(
                    'product_batch' => '',
                    'due_date' => '0000-00-00', //There is a bug till year 2099.
                    'product_id' => $v['product_id'],
                    'special_price' => $v['price'],
                    'qty' => abs(min($v['quantity'],$v['order_quantity'])),
                    'product_weight' => 0,
                    'sku_id' => $v['sku_id']
                );
            }

            $data_inv['products'] = $stationProductMove;

            //$log->write('[分拣]整理库存数据[' . __FUNCTION__ . ']'.serialize($data_inv)."\n\r");
            $result = $this->addInventoryMoveOrder($data_inv, 1);
            if($result){
                $dbm->begin();
                $bool = true;

                //添加分拣缺货至退货表
                $this->addReturn(array($order_id));

                $sql = "update oc_x_inventory_order_sorting set move_flag = 1 where order_id = '". $order_id ."'";
                //$log->write('[分拣]更新分拣数据提交状态[' . __FUNCTION__ . ']'.$sql."\n\r");
                $bool = $bool && $dbm->query($sql);

                $sql = "update oc_order set order_status_id = 6 where order_id = '". $order_id ."'";
                //$log->write('[分拣]更新订单状态为已拣完[' . __FUNCTION__ . ']'.$sql."\n\r");
                $bool = $bool && $dbm->query($sql);

                //添加订单历史记录
                $this->addOrderSortingHistory($order_id);


                if(!$bool) {
                    //$log->write('[分拣]分拣提交失败[' . __FUNCTION__ . ']' . "\n\r");
                    $dbm->rollback();
                    return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
                }
                else {
                    //$log->write('[分拣]分拣提交成功[' . __FUNCTION__ . ']' . "\n\r");
                    $dbm->commit();
                    return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
                }
            }else {
                return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
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

    private function addReturn($data) {
        global $db, $dbm;

        if(!sizeof($data)){
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
        $sql = "select o.order_id, date(o.date_added) date_ordered, o.customer_id, op.product_id, op.name, op.quantity, op.price, op.total
                from oc_order o
                left join oc_order_product op on o.order_id = op.order_id
                where o.order_id in (".$targetOrdersString.") and o.order_status_id in (5,8) and o.order_deliver_status_id = 1";
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



    public function addPurchaseOrderProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;
    
        $no_sorting = false;


        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryInProduct'; //Up

        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;
        if (!$order_id) {
            return false;
        }


        
        /*
          $sql = "update oc_x_inventory_order_sorting set move_flag = 1 where order_id = " . $data_inv['order_id'];
          $query = $dbm->query($sql);


          $sql = "update oc_order set order_status_id = 6 where order_id = " . $data_inv['order_id'];
          $dbm->query($sql);

          return array('status'=>1,'timestamp'=>$data_inv['timestamp']);
          exit;
         */

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

            
            $result = $this->addInventoryMoveOrder($data_inv, 1);
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

            //添加可售库存和商品上架
            if(!empty($data_inv)){
                foreach($data_inv['products'] as $key => $value){
                    $data_inv['products'][$value['product_id']] += $value['qty'];
                    unset($data_inv['products'][$key]);
                }
            }
            $this->adjust_post($data_inv);

            /*
              $sql = "update oc_order_inv set inv_status = 0 where order_id = " . $data_inv['order_id'];
              $dbm->query($sql);

              $data_inv['frame_count'] = $data_inv['frame_count'] ? $data_inv['frame_count'] : 0;
              $data_inv['incubator_count'] = $data_inv['incubator_count'] ? $data_inv['incubator_count'] : 0;
              $sql = "insert into oc_order_inv(order_id,frame_count,incubator_count,inv_comment) values(" . $data_inv['order_id'] . "," . $data_inv['frame_count'] . "," . $data_inv['incubator_count'] . ",'" . $data_inv['inv_comment'] . "');";
              $dbm->query($sql);
             */


            return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
        }
    }

    
    
    public function delOrderProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);
       
        $order_id = isset($data_inv['order_id']) ? $data_inv['order_id'] : false;
        if (!$order_id) {
            return false;
        }


        $sql = "select * from oc_x_inventory_order_sorting where order_id = " . $order_id . " and move_flag = 1";
        $query = $dbm->query($sql);
        $result_exists = $query->rows;
        if(!empty($result_exists)){
            return array('status' => 2, 'timestamp' => $data_inv['timestamp']);
        }

        //Update order status back before sorting start
        $sql = "update oc_order set order_status_id = 2 where order_status_id in (5,6,8) and order_id = '".$order_id."'";
        $dbm->query($sql);

        //Remove Order Inv
        $sql = "delete from oc_order_inv where order_id = " . $order_id;
        $dbm->query($sql);

        //Remove Order Sorting Info
        $sql = "delete from oc_x_inventory_order_sorting where order_id = " . $order_id;
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

    
    
    
    public function addCheckProductToInv($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $dbm;
        global $log;




        $data_inv = json_decode($data, 2);
        $data_inv['api_method'] = 'inventoryCheck'; //Up


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
        
        
        
        
        
        
        $sql = "select inventory_move_id,date_added from oc_x_stock_move where inventory_type_id = 14 order by inventory_move_id desc limit 1";
        
        $query = $dbm->query($sql);
        $inventory_check = $query->row;
        
        $inventory_check_id = $inventory_check['inventory_move_id'];
        $inventory_check_time = $inventory_check['date_added'];
        if($inventory_check_id){
            $sql = "SELECT
                    xsm.inventory_move_id,
                    xsm.inventory_type_id,
                    smi.product_id,
                    sum(smi.quantity) as product_move_type_quantity,
                    pd.name,
                    smi.product_batch,
                    smi.price,
                    smi.sku_id
            FROM
                    oc_x_stock_move AS xsm
            left JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id
            left join oc_product as p on p.product_id = smi.product_id 
            -- left join (select * from oc_product_to_category group by product_id) as ptc on ptc.product_id = smi.product_id
            -- left join (select * from oc_product_description where language_id = 2 group by product_id) as pd on pd.product_id=smi.product_id
            left join oc_product_to_category as ptc on ptc.product_id = smi.product_id
            left join oc_product_description as pd on pd.product_id=smi.product_id and pd.language_id = 2
            WHERE
                    xsm.inventory_move_id >= " . $inventory_check_id . "
                and xsm.date_added >= '" . $inventory_check_time . "'
            and p.station_id = 2             
            -- and (smi.product_id > 5000 or ptc.category_id in (72,74,157))
            group by xsm.inventory_type_id,smi.product_id";
            
            $query = $dbm->query($sql);
            $inventory_arr = $query->rows;
            $inventory_product_move_arr = array();
            foreach($inventory_arr as $key=>$value){
                $inventory_product_move_arr[$value['product_id']]['quantity'][$value['inventory_type_id']] = $value['product_move_type_quantity'];
                $inventory_product_move_arr[$value['product_id']]['name'] = $value['name'];
                $inventory_product_move_arr[$value['product_id']]['sum_quantity'] = 0;
                $inventory_product_move_arr[$value['product_id']]['date_added'] = $inventory_check['date_added'];
                
                $inventory_product_move_arr[$value['product_id']]['product_batch'] = $value['product_batch'];
                $inventory_product_move_arr[$value['product_id']]['price'] = $value['price'];
                $inventory_product_move_arr[$value['product_id']]['sku_id'] = $value['sku_id'];
            }
            
        }
        
        if(!empty($inventory_product_move_arr)){
            
            
            foreach($inventory_product_move_arr as $key=>$value){
                foreach($value['quantity'] as $k=>$v){
                    
                    if($k == 15 && $product_to_promotion_arr[$key]){
                        if(isset($inventory_product_move_arr[$product_to_promotion_arr[$key]])){
                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['quantity']['15'] = abs($v);
                        }
                        else{
                            $inventory_product_move_arr[$product_to_promotion_arr[$key]] = $value;
                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['quantity'] = array();
                            
                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['name'] =  '';
                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['sum_quantity'] = 0;
                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['date_added'] = $value['date_added'];
                            $inventory_product_move_arr[$product_to_promotion_arr[$key]]['quantity']['15'] = abs($v);
                            
                            
                        }
                        
                        
                    }
                    
                }
            }
           
            //echo "<pre>";print_r($inventory_product_move_arr);exit;
            foreach($inventory_product_move_arr as $key1=>$value1){
                
                foreach($value1['quantity'] as $k1=>$v1){
                    $inventory_product_move_arr[$key1]['sum_quantity'] += $v1;
                }
                
                if(!isset($stationProductMove_ids[$key1])){
                    $stationProductMove[] = array(
                        'product_batch' => $value1['product_batch'],
                        'due_date' => '0000-00-00', //There is a bug till year 2099.
                        'product_id' => $key1,
                        'special_price' => $value1['price'],
                        'qty' => $inventory_product_move_arr[$key1]['sum_quantity'] >= 0 ? $inventory_product_move_arr[$key1]['sum_quantity'] : 0,
                        'product_weight' => 0,
                        'sku_id' => $value1['sku_id']
                            //'qty' => '-'.$v['quantity']
                    );
                }
                
            }
            
            $data_inv['products'] = $stationProductMove;
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










            $result = $this->addInventoryMoveOrder($data_inv, 1);
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
                    'qty' => $v['quantity'] - $v['inv_quantity'],
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

            $result = $this->addInventoryMoveOrder($data_inv, $station_id);
            if ($result) {
                $update_sorting_id_str = implode(",", $update_sorting_id_arr);
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










            $result = $this->addInventoryMoveOrder($data_inv, 1);
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
        if (!$order_id) {
            return false;
        }

        $sql = "select station_id,order_status_id from oc_order where order_id = '".$order_id."'";
        $query = $db->query($sql);
        $result = $query->row;
        $station_id = $result['station_id'];

        //[add_type=3]: required from admin), [order_status_id=6]: order sorting data submitted, if require not from admin and sorting data submitted, ignore.
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
                    and (frame_vg_list like '%" . $div . "%')
                    and o.order_deliver_status_id not in (3,7) and oi.order_id != " . $order_id;
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
        
        

        $sql = "select * from oc_order_inv where order_id = " . $data_inv['order_id'];
        $query = $dbm->query($sql);
        $order_inv = $query->row;
        if (empty($order_inv)) {
            $sql = "insert into oc_order_inv(order_id,frame_count,incubator_count,inv_comment,inv_status,foam_count,frame_mi_count,incubator_mi_count,frame_ice_count,box_count,foam_ice_count,frame_meat_count,frame_vg_list,frame_meat_list,frame_mi_list,frame_ice_list,uptime) values(" . $data_inv['order_id'] . "," . $data_inv['frame_count'] . "," . $data_inv['incubator_count'] . ",'" . $data_inv['inv_comment'] . "',1," . $data_inv['foam_count'] . "," . $data_inv['frame_mi_count'] . ", " . $data_inv['incubator_mi_count'] . " ," . $data_inv['frame_ice_count'] . "," . $data_inv['box_count'] . "," . $data_inv['foam_ice_count'] . "," . $data_inv['frame_meat_count'] . ",'" . $data_inv['frame_vg_list'] . "','" . $data_inv['frame_meat_list'] . "','" . $data_inv['frame_mi_list'] . "','" . $data_inv['frame_ice_list'] . "',now());";
        } else {

            $sql = "update oc_order_inv set ";


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
            if($station_id == 2 ){
                $sql = "update oc_order_inv set ";
                $sql .= "frame_count = " . $data_inv['frame_count'] . ",";
                $sql .= "inv_comment = '" . $data_inv['inv_comment'] . "',";
                $sql .= "frame_vg_list = '" . $data_inv['frame_vg_list'] . "',";
                $sql .= "box_count = " . $data_inv['box_count'] . ",";
            }

            $sql .= "uptime = now(),";
            
            $sql .= "inv_status = 1 ";
            $sql .= " where order_id = " . $data_inv['order_id'];
        }

        if ($dbm->query($sql)) {
            return array('status' => 1, 'timestamp' => $sql);
        } else {
            return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
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
            }
            else{
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
        
        
        
        
        
        
        
        
        
        
        return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
    }
    
    
    
    public function addReturnDeliverProduct($data, $station_id, $language_id = 2, $origin_id) {
        //Expect Data: $data= '{"data":"2015-09-02","station_id":"2"}';
        //Expect Data: $data= '{"station_id":"2"}';
        global $db, $dbm;
        global $log;

        $data_inv = json_decode($data, 2);

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
        $returnProcessErrorMessage = "请检查订单状态和及退货商品信息，出库退货仅限7日内配送［已分拣］且［配送中?］的订单。";
        $returnOrderDeliverStatus = '1,2';

        if($isBack){
            $returnDataParam = array(
                'return_reason_id' => 0,
                'return_action_id' => 0,
                'is_back' => $isBack,
                'is_repack_missing' => $isRepackMissing
            );

            $returnProcessErrorMessage = "请检查订单状态和及退货商品信息，回库退货仅限7日内配送，已分拣完成且已配送出库的订单。";
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
                from xsjb2b.oc_order A
                left join xsjb2b.oc_x_stock_move B on A.order_id = B.order_id
                left join xsjb2b.oc_x_stock_move_item C on B.inventory_move_id = C.inventory_move_id
                left join xsjb2b.oc_product D on C.product_id = D.product_id
                where A.order_id = '".$data_inv['order_id']."'
                and A.order_status_id in (6,10)
                and A.order_deliver_status_id in (".$returnOrderDeliverStatus.")
                and A.station_id = 2
                and A.deliver_date between date_sub(current_date(), interval 7 day) and date_add(current_date(), interval 1 day)
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
            $validQty = $value['out_qty'];
            $pendindReturnQty = array_key_exists($value['product_id'], $pendingReturnResult) ? $pendingReturnResult[$value['product_id']]['qty'] : 0;
            $ReturnedQty = array_key_exists($value['product_id'], $returnedResult) ? $returnedResult[$value['product_id']]['qty'] : 0;
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

            if($submitReturnQty > $validQty){
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
        $sql = "insert into oc_return_deliver_product(return_reason_id,return_action_id, is_back, is_repack_missing, order_id,product_id,product,model,quantity,in_part, box_quantity, price,total,add_user_id,date_added) values";
        foreach($data_inv['products'] as $m){
            //按提交数据是否整件计算商品金额
            $inPart = ($m['box_quantity']>1) ? 1 : 0;
            $returnProudctPrice = $order_product_arr[$m['product_id']]['price'];
            if($inPart){
                $returnProudctPrice = round($order_product_arr[$m['product_id']]['price']/$m['box_quantity'],2);
            }
            $sql .= "('".$data_inv['return_reason']."','".$returnDataParam['return_action_id']."','".$returnDataParam['is_back']."','". $returnDataParam['is_repack_missing'] ."','" . $data_inv['order_id'] . "','" . $m['product_id'] . "','" . $order_product_arr[$m['product_id']]['name'] . "','','" . $m['quantity'] . "','" . $inPart . "','" . $m['box_quantity'] . "','" . $returnProudctPrice . "','" . $returnProudctPrice*$m['quantity'] . "'," . $data_inv['add_user_name'] . ",now()),";
        }
        if(substr($sql, strlen($sql)-1,1) == ','){
            $sql = substr($sql, 0,-1);
        }

        //$log->write('退货04[' . __FUNCTION__ . ']写入退货中间表：'.$sql."\n\r");
        $dbm->query($sql);

        return array('status' => 1, 'timestamp' => '', 'message' => "商品退货完成");
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

        $sql = "select
        `return_deliver_product_id`, `order_id`, `return_reason_id`, `return_action_id`, `is_back`, `is_repack_missing`, `product_id`, `product`, `model`, `quantity`, `in_part`, `box_quantity`, `price`, `total`, `add_user_id`, `date_added`, `status`, `confirm_user_id`, `date_comfirmed`, `confirmed`, `return_id`
        from oc_return_deliver_product
        where status = 1 and date_format(date_added, '%Y-%m-%d') = '" . $date . "' and add_user_id = " . $data_inv['add_user_name'] . "
        and is_back = '". $isBack ."' and is_repack_missing = '".$isRepackMissing."'
        order by date_added desc";

        $query = $db->query($sql);
        $result = $query->rows;

        return $result;
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

        //查找指定日期，由本人提交的，有效的且未确认的退货记录
        $sql = "select order_id, sum(total) current_return_total from oc_return_deliver_product
        where status = 1 and confirmed = 0 and date_format(date_added, '%Y-%m-%d') = '" . $date . "' and add_user_id = " . $userId . "
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
            $returnTotal = array_key_exists($m['order_id'], $returnedInfoList) ? $returnedInfoList[$m['order_id']]['return_total'] : 0;
            $returnCreditsTotal = array_key_exists($m['order_id'], $returnedInfoList)? $returnedInfoList[$m['order_id']]['return_credits_total'] : 0;
            $currentReturnTotal = $currentReturnInfoList[$m['order_id']]['current_return_total'];

            //出库应收=｛实际出库-(小计-应付)｝
            //出库应退＝｛小计-应付-实际出库｝
            //出库缺货应收1=｛出库应收-出库缺货1｝
            //出库缺货应退1={出库缺货1-出库应收}
            $dueOut = $outTotal - ($subTotal-$dueTotal);

            $dueCurrent = $dueOut-$returnTotal-$currentReturnTotal;//本次退货应收 = 出库应收－已退货－本次退货
            $returnCurrent = ($dueCurrent < 0) ? abs($dueCurrent) : 0;//计算退货后后本次应收小于0，退余额

            //判断是否全部退货或退货金额占订单80%以上，不退余额
            if($currentReturnTotal >= $dueOut*0.8){
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

            //增加是否入库标志位，仓库操作时根据$return_action_id状态判断，是否入库标志可置为1，前台用户退货，司机确认时，默认为0，待仓库确认。
            $inventoryReturned = 0;
            if($return_action_id == 2 || $return_action_id == 4){
                $inventoryReturned = 1;
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
            $sql = "INSERT INTO `oc_return` (`order_id`, `customer_id`, `return_reason_id`, `return_action_id`, `return_status_id`, `comment`, `date_ordered`, `date_added`, `date_modified`, `add_user`, `return_credits`, `return_inventory_flag`, `credits_returned`,`inventory_returned`)
                    VALUES('".$m['order_id']."','".$outInfoList[$m['order_id']]['customer_id']."','".$return_reason_id."','".$return_action_id."','2','','".$outInfoList[$m['order_id']]['order_date']."',NOW(),NOW(),'".$userId."','".$returnCredits."','0','0','".$inventoryReturned."');";

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

                    $this->addInventoryMoveOrder($stockMoveData, $stationId);
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
            $sql = "SELECT pd.name, p.status, p.sku, p.box_size, p.inv_class_sort, p.model, pd.special_price as price, p.product_id FROM oc_product AS p LEFT JOIN labelprinter.productlist AS pd ON p.product_id = pd.product_id WHERE pd.barcode = '" . $sku . "'";
        }
        elseif(is_numeric($sku) && strlen($sku) <= 6) {
            $sql = "SELECT pd.name, p.status, p.sku, p.box_size, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id WHERE p.product_id = '".$sku."'";
        }
        elseif(is_numeric($sku) && strlen($sku) > 6) {
            $sql = "SELECT pd.name, p.status, p.sku, p.box_size, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id WHERE p.sku like '".$sku."%'";
        }
        else{
            $sql = "SELECT pd.name, p.status, p.sku, p.box_size, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id WHERE p.inv_class_sort like '".$sku."%'";
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

    public function changeProductSection($data, $station_id, $language_id = 2, $origin_id){
        global $dbm;

        //TODO 移植到后台
        $data = json_decode($data, 2);

        if(isset($data['productSection']) && $data['productSection'] !== ''){
            $sql = "INSERT INTO oc_product_section_history(product_id,inv_class_sort,new_section,added_by,date_added)
                SELECT product_id, inv_class_sort, '".$data['productSection']."', '".$data['inventory_user']."', NOW() FROM oc_product
                WHERE product_id = '".$data['productId']."'";
            $query = $dbm->query($sql);

            $sql = "UPDATE oc_product SET inv_class_sort = '".$data['productSection']."' WHERE product_id = '".$data['productId']."'";
            $query = $dbm->query($sql);

            return $query;
        }

        if(isset($data['productBarCode']) && $data['productBarCode'] !== ''){
            $sql = "UPDATE oc_product SET model = '".$data['productBarCode']."' WHERE product_id = '".$data['productId']."'";
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
            $sql = "SELECT p.product_id, pd.name, p.repack, p.status, p.sku, p.inv_class_sort, p.model
                FROM oc_product AS p
                LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id
                WHERE p.inv_class_sort like '".$productSection."%'
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
        if (!$sku) {
            return false;
        }

        
        $product_inv = array();
        $product_inv['quantity'] = 0;
        $product_inv['status'] = 1;
        
        
        
        $uptime = date("Y-m-d 00:00:00", time());
        if (strlen($sku) == 18) {
             $sql2 = "select * from oc_x_inventory_check_single_sorting as ics left join oc_product as p on ics.product_id = p.product_id left join  labelprinter.productlist AS pd ON p.product_id = pd.product_id 
where ics.uptime > '" . $uptime . "' and pd.barcode = '" . $sku . "'";
            $sql = "SELECT  p.product_id FROM oc_product AS p LEFT JOIN labelprinter.productlist AS pd ON p.product_id = pd.product_id WHERE pd.barcode = '" . $sku . "'";
        }
        if (strlen($sku) == 13 || strlen($sku) == 14) {
             $sql2 = "select * from oc_x_inventory_check_single_sorting as ics left join oc_product as p on ics.product_id = p.product_id
where ics.uptime > '" . $uptime . "' and p.sku like '%" . $sku . "%'";
            $sql = "SELECT p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id WHERE p.sku like '%" . $sku . "%'";
        }
        if (strlen($sku) <= 6) {
            
            $sql2 = "select * from oc_x_inventory_check_single_sorting as ics left join oc_product as p on ics.product_id = p.product_id
where ics.uptime > '" . $uptime . "' and p.product_id = " . $sku;
            $sql = "SELECT  p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id WHERE p.product_id = " . $sku;
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
        $sql = "select inventory_move_id,date_added from oc_x_stock_move where inventory_type_id = 14 order by inventory_move_id desc limit 1";
        
        $query = $db->query($sql);
        $inventory_check = $query->row;
        
        $inventory_check_id = $inventory_check['inventory_move_id'];
        $inventory_check_time = $inventory_check['date_added'];
        if($inventory_check_id){
            $sql = "SELECT
                    xsm.inventory_move_id,
                    xsm.inventory_type_id,
                    smi.product_id,
                    sum(smi.quantity) as product_move_type_quantity,
                    pd.name
            FROM
                    oc_x_stock_move AS xsm
            left JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id
            left join oc_product as p on p.product_id = smi.product_id 
            -- left join (select * from oc_product_to_category group by product_id) as ptc on ptc.product_id = smi.product_id
            -- left join (select * from oc_product_description where language_id = 2 group by product_id) as pd on pd.product_id=smi.product_id
            left join oc_product_to_category as ptc on ptc.product_id = smi.product_id
            left join oc_product_description as pd on pd.product_id=smi.product_id and pd.language_id = 2
            WHERE
                    xsm.inventory_move_id >= " . $inventory_check_id . "
            and xsm.date_added >= '" . $inventory_check_time . "'
            and smi.product_id in (" . $select_inv_product_id_str . ")
            
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
                sum(B.quantity) qty
                from oc_x_stock_move A
                left join oc_x_stock_move_item B on A.inventory_move_id = B.inventory_move_id
                left join oc_product P on B.product_id = P.product_id
                left join oc_x_inventory_type C on A.inventory_type_id = C.inventory_type_id
                where B.status = 1
                and A.date_added >= ( select min(date_added) from oc_x_stock_move where inventory_type_id = 14 and date_added >= date_sub(current_date(), interval 3 day))  -- 最早盘点时间
                and P.station_id = 2 and B.product_id = '".$product_id."'
                group by inventory_type_id
                order by inventory_type_id
                ";
        $query = $db->query($sql);
        $inventoryInfo = $query->rows;
        $product_inv['inventoryInfo'] = $inventoryInfo;

        //获取当分拣占用但未提交的商品
        $sql = "select sum(quantity) sort_qty from oc_x_inventory_order_sorting where product_id = '".$product_id."' and move_flag = 0";
        $query = $db->query($sql);
        $sortInfo = $query->row;
        $product_inv['sortQty'] = isset($sortInfo['sort_qty']) ? $sortInfo['sort_qty'] : 0;
        
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

        //$date = $data_inv['date'];

        $username = $data_inv['username'] ? $data_inv['username'] : '';
        $password = $data_inv['password'] ? $data_inv['password'] : '';
        
        $user_query = $db->query("SELECT * FROM " . DB_PREFIX . "w_user WHERE username = '" . $db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $db->escape($password) . "'))))) OR password = '" . $db->escape(md5($password)) . "') AND status = '1'");

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


        $sql = "SELECT
	ics.*,
	pd. NAME,p.price,p.sku as product_batch
FROM
	oc_x_inventory_check_single_sorting AS ics
LEFT JOIN oc_product_description AS pd ON pd.product_id = ics.product_id
left join oc_product as p on p.product_id = ics.product_id";
$sql .=" WHERE ics.uptime > '" . $date . " 00:00:00' and ics.uptime < '" . $date . " 24:00:00'";

        

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



        $sql = "select * from oc_order_status where language_id = 2 order by order_status_id asc";




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
               and A.deliver_date between date_sub(current_date(), interval 15 day) and date_add(current_date(), interval 1 day)
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
            $sql .= "('".$returnDataParam['return_reason_id']."','".$returnDataParam['return_action_id']."','".$returnDataParam['is_back']."','". $returnDataParam['is_repack_missing'] ."','" .
                $data_inv['order_id'] . "','" . $m['product_id'] . "','" . $order_product_arr[$m['product_id']]['name'] . "','','" . $m['quantity'] . "','" . $inPart . "','" . $m['box_quantity'] . "','" . $returnProudctPrice . "','" . $returnProudctPrice*$m['quantity'] . "'," . $customer_id . ",now()),";
       }
        if(substr($sql, strlen($sql)-1, 1) == ','){
            $sql = substr($sql, 0, -1);
        }

        //$log->write('退货04[' . __FUNCTION__ . ']写入退货中间表：'.$sql."\n\r");
        $dbm->query($sql);

        return array('status' => 1, 'timestamp' => '', 'message' => "申请退货成功");
    }




}

$inventory = new INVENTORY();
?>