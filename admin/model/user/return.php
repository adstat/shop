<?php
class ModelUserReturn extends Model {

    public function getTotalReturns($data = array()) {
        $sql = "select count(*) as total from oc_return_deliver_product ";

        if (isset($data['filter_return_confirmed'])) {
            $implode = array();

            $confirmed_status = explode(',', $data['filter_return_confirmed']);
            foreach ($confirmed_status as $confirmed) {
                $implode[] = "confirmed = '" . (int)$confirmed . "'";
            }

            if ($implode) {
                $sql .= " WHERE (" . implode(" OR ", $implode) . ")";
            } else {

            }
        } else {
            $sql .= " WHERE confirmed >= '0'";
        }

        if (isset($data['filter_logistic_user'])) {
            $implode = array();

            $logistic_user = explode(',', $data['filter_logistic_user']);
            foreach ($logistic_user as $user) {
                $implode[] = "add_user_id = '" . (int)$user . "'";
            }

            if ($implode) {
                $sql .= " and (" . implode(" OR ", $implode) . ")";
            } else {

            }
        } else {
            $sql .= " and add_user_id >= '0'";
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " and order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_product_id'])) {
            $sql .= " and product_id = '" . (int)$data['filter_product_id'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " and date_added like '" . $data['filter_date_added'] ."%". "'";
        }
        if($data['filter_return_reason'] == 1){
            $sql .= "and is_back = 0 and in_part = 0 ";
        }
        if($data['filter_return_reason'] == 2){
            $sql .= "and is_back = 1 and in_part = 0";
        }
        if($data['filter_return_reason'] ==3){
            $sql .="and is_back = 1 and in_part = 1";
        }
        if($data['filter_return_reason'] ==4){
            $sql .=" and is_back = 1 ";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
    public function getReturns($data = array()) {
        $sql = "select op.order_id,op.product_id,op.product,op.quantity,op.price,op.total,op.date_added,op.status,op.confirmed,op.return_action_id,
                orr.name,ou.username,ora.name as return_type,op.in_part,op.box_quantity
                from oc_return_deliver_product op
                left join oc_return_reason orr on orr.return_reason_id = op.return_reason_id
                left join oc_w_user ou on ou.user_id = op.add_user_id
                left join oc_return_action ora on ora.return_action_id = op.return_action_id
                ";

        if (isset($data['filter_return_confirmed'])) {
            $implode = array();

            $confirmed_status = explode(',', $data['filter_return_confirmed']);
            foreach ($confirmed_status as $confirmed) {
                $implode[] = "op.confirmed = '" . (int)$confirmed . "'";
            }

            if ($implode) {
                $sql .= " WHERE (" . implode(" OR ", $implode) . ")";
            } else {

            }
        } else {
            $sql .= " WHERE op.confirmed >= '0'";
        }

        if (isset($data['filter_logistic_user'])) {
            $implode = array();

            $logistic_user = explode(',', $data['filter_logistic_user']);
            foreach ($logistic_user as $user) {
                $implode[] = "op.add_user_id = '" . (int)$user . "'";
            }

            if ($implode) {
                $sql .= " and (" . implode(" OR ", $implode) . ")";
            } else {

            }
        } else {
            $sql .= " and op.add_user_id >= '0'";
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " and op.order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_product_id'])) {
            $sql .= " and op.product_id = '" . (int)$data['filter_product_id'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " and op.date_added like '" . $data['filter_date_added'] ."%". "'";
        }

        if($data['filter_return_reason'] == 1){
            $sql .= "and op.is_back = 0 and op.in_part = 0 ";
        }
        if($data['filter_return_reason'] == 2){
            $sql .= "and op.is_back = 1 and op.in_part = 0";
        }
        if($data['filter_return_reason'] ==3){
            $sql .="and op.is_back = 1 and op.in_part = 1";
        }
        if($data['filter_return_reason'] ==4){
            $sql .=" and op.is_back = 1 ";
        }

        $sort_data = array(
            'op.order_id',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY op.order_id";
        }
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
//        var_dump($sql);die;
        $query = $this->db->query($sql);
        $returns = $query->rows;
        return $returns;
    }
    public function setConfirm($orderid,$productid,$userid,$time,$return_action_id) {
        //更新oc_return_deliver_product表进行确认
        $sql = "update oc_return_deliver_product set confirmed = 1,confirm_user_id = $userid,date_confirmed = '$time',return_action_id = $return_action_id where order_id = $orderid and product_id = $productid";
        //对退货主表oc_return进行写操作,然后就是继续对oc_return_product以及oc_return_history进行写数据
        if($this->db->query($sql)){
            //取得订单信息和物流退货信息，写入退货主表
        $str = "select oo.customer_id,oo.firstname,oo.lastname,oo.email,oo.telephone,oo.date_added as date_ordered,oo.credit_pay,
                ord.return_action_id,ord.return_reason_id,ord.confirmed,ord.order_id,ord.product_id,ord.product,ord.quantity,ord.total,ord.confirm_user_id,ord.date_confirmed
                from oc_return_deliver_product ord
                left join oc_order oo  on ord.order_id = oo.order_id
                where ord.order_id = $orderid and ord.product_id = $productid";
            $return_info = $this->db->query($str)->rows;
            $insertReturn = "insert into `" . DB_PREFIX .  "return`(order_id,product_id,customer_id,firstname,lastname,email,telephone,date_ordered,return_action_id,return_reason_id,return_status_id,return_inventory_flag,product,quantity,return_credits,add_user,date_added) values";
            foreach($return_info as $value){
            //根据退货类型进行是否退货入库的操作
            if($value['return_action_id'] == 2||$value['return_action_id'] ==4){
                $return_inventory_flag = 1;//可以退货入库
            }else{
                $return_inventory_flag = 0;
            }
            //根据订单是否线上全额支付来判断是否需要退款
            if($value['credit_pay']){
                $return_credits = $value['total'];
            }else{
                $return_credits = 0;
            }
            //根据确认退货状态判断return_station_id
            if($value['confirmed']){
                $return_station_id = 2;
            }else{
                $return_station_id = 1;
            }
                $insertReturn .= "(". $value['order_id'] . "," . $value['product_id'] . "," .$value['customer_id'] ."," ."'".$value['firstname'] ."'" . ","."'" .$value['lastname'] ."'". ","."'" .$value['email'] ."'". "," .$value['telephone'] . ","."'" .$value['date_ordered'] ."'"."," .$value['return_action_id'] ."," .$value['return_reason_id'] ."," .$return_station_id .",". $return_inventory_flag.","."'" .$value['product'] ."'"."," .$value['quantity'] .",".$return_credits."," .$value['confirm_user_id'] ."," ."'" .$value['date_confirmed'] ."'".")";
                $returnInsert = $this->db->query($insertReturn);
                /* oc_return主表写完之后，根据此表写oc_return_product以及oc_return_history的数据*/
                //读取oc_return表的数据，分别把对应的字段写到相应的表里去
                if($returnInsert){
                    $returnSql = "select ocr.return_id,ocr.return_status_id,ocr.add_user,if(ocr.date_added,ocr.date_added,ocr.date_modified) as date_added,ocr.product_id,ocr.product,ocr.quantity,ocr.return_credits,
                    orp.price,orp.total
                    from oc_return ocr
                    left join oc_return_deliver_product orp on orp.order_id = ocr.order_id
                    where ocr.order_id = $orderid and ocr.product_id = $productid
                    ";
                    $returnSonInfo = $this->db->query($returnSql)->rows;
                    foreach($returnSonInfo as $vvalue){
                        $poductSql = "insert into `" . DB_PREFIX .  "return_product`(return_id,product_id,product,quantity,price,total,return_product_credits) values"."(".$vvalue['return_id'].",".$vvalue['product_id'].","."'".$vvalue['product']."'".",".$vvalue['quantity'].",".$vvalue['price'].",".$vvalue['total'].",".$vvalue['return_credits'].")";
                        $productInsert = $this->db->query($poductSql);
                        if(!$productInsert){
                            //写product失败
                            return false;
                        }
                        $historySql = "insert into `" . DB_PREFIX .  "return_history`(return_id,return_status_id,add_user,date_added) values"."(".$vvalue['return_id'].",".$vvalue['return_status_id'].",".$vvalue['add_user'].","."'".$vvalue['date_added']."'".")";
                        $historyInsert = $this->db->query($historySql);
                        if(!$historyInsert){
                            //写history失败
                            return false;
                        }
                    }
                }else{
                    //oc_return保存出错返回的值
                    return false;
                }
            }
        }else{
            //oc_return_delivery出错的信息
            return false;
        }
        return true;

    }
    public function deleteProduct($data){
        return false;

        $order_id = $data[0];
        $product_id = $data[1];
        $sql = "delete from oc_return_deliver_product where order_id = '".(int)$order_id."' and product_id = '".$product_id."'";
        $query = $this->db->query($sql);
        if($query){
            return true;
        }else{
            return false;
        }
    }

    public function disableDeliverReturnProduct($data){
        $order_id = $data[0];
        $product_id = $data[1];

        //作废而不要删除
        $sql = "update oc_return_deliver_product set status = 0 where confirmed = 0 and order_id = '".(int)$order_id."' and product_id = '".$product_id."'";

        $query = $this->db->query($sql);
        if($query){
            return true;
        }else{
            return false;
        }
    }

    public function getLogisticUser() {
        $sql = "select user_id,username from oc_w_user";
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function getProductRturn($order_id,$product_id) {
        $sql = "select concat(ocr.firstname,' ',ocr.lastname) as customername,orr.name as reason,orp.quantity,orp.price,ocr.return_inventory_flag,owu.username as logisticname,ocr.date_added
            from oc_return ocr
            left join oc_return_product orp on orp.return_id = ocr.return_id
            left join oc_return_reason orr on orr.return_reason_id = ocr.return_reason_id
            left join oc_w_user owu on owu.user_id = ocr.add_user
            where ocr.order_id = $order_id and ocr.product_id = $product_id";
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function getOrderList($order_id, $start = 0, $limit = 10) {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $sql = "select ocr.product_id,ocr.product,concat(ocr.firstname,' ',ocr.lastname) as customername,ocr.telephone,ocr.quantity,ora.name as action,ors.name as status
            from oc_return ocr
            left join oc_return_action ora on ora.return_action_id = ocr.return_action_id
            left join oc_return_status ors on ors.return_status_id = ocr.return_status_id
            where order_id = $order_id
            limit $start,$limit
            ";

        $query = $this->db->query($sql);

        return $query->rows;
    }
    public function getTotalOrderList($order_id) {
        $sql = "select count(*) as total from oc_return where order_id = $order_id";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }
    public function confirmReturn($user_id,$order_id,$products){

        //根据order_id取得order信息，保存到oc_return表中
        $sql = "select o.order_id,o.order_payment_status_id,o.sub_total,date(o.date_added) date_ordered,c.customer_id,c.firstname,c.telephone
            from oc_order o
            left join oc_customer c on c.customer_id = o.customer_id
            where o.order_id = '". $order_id ."'";

        $order_info = $this->db->query($sql)->row;

        if($order_info['order_id']){
            if($order_info['order_payment_status_id'] == 1){

                //未支付的订单处理办法，不退还余额
                $this->db->query('START TRANSACTION');
                $sql = "INSERT INTO oc_return (`order_id`,`customer_id`,`firstname`,`telephone`,`return_reason_id`,`return_action_id`,`date_ordered`,`return_status_id`,`add_user`,`date_added`)
                values('".$order_id."','".$order_info['customer_id']."','".$order_info['firstname']."','".$order_info['telephone']."','1','1','".$order_info['date_ordered']."','1','".$user_id."',current_timestamp())";

                //执行oc_return的插入
                $query = $this->db->query($sql);

                $return_id = $this->db->getLastId();

                $bool = 1;

                $sql = "INSERT INTO oc_return_product (`return_id`,`product_id`,`product`,`quantity`,`price`,`total`) VALUES";
                $m=0;
                foreach($products as $value){
                    $sql_u = "update oc_return_deliver_product set confirmed = 1, return_id = '".$return_id."' where order_id = '".$order_id."' and product_id = '$value[0]'";
                    $query = $this->db->query($sql_u);

                    $sql .= "(".$return_id.", '".$value[0]."','".$value[1]."','".$value[2]."','".$value[3]."','".$value[2]*$value[3]."')";
                    if(++$m < sizeof($products)){
                        $sql .= ', ';
                    }
                    else{
                        $sql .= ';';
                    }
                }

                $bool = $bool && $this->db->query($sql);
                if($bool){
                    $this->db->query('COMMIT');

                    return true;
                }
                else{
                    $this->db->query("ROLLBACK");
                }
            }
            if($order_info['order_payment_status_id'] == 2){

                //已支付的订单处理办法
                $this->db->query('START TRANSACTION');

                $sql = "INSERT INTO oc_return (`order_id`,`customer_id`,`firstname`,`telephone`,`return_reason_id`,`return_action_id`,`date_ordered`,`return_status_id`,`add_user`,`date_added`)
                values('".$order_id."','".$order_info['customer_id']."','".$order_info['firstname']."','".$order_info['telephone']."','1','3','".$order_info['date_ordered']."','1','".$user_id."',current_timestamp())";

                //执行oc_return的插入，并且累加得到oc_return的退货金额
                $query = $this->db->query($sql);

                $return_id = $this->db->getLastId();

                $bool = 1;

                $return_credits_total = 0;

                foreach($products as $value){
                    $sql_u = "update oc_return_deliver_product set confirmed = 1, return_id = '".$return_id."' where order_id = '".$order_id."' and product_id = '$value[0]'";

                    $update = $this->db->query($sql_u);

                    $sql = "INSERT INTO oc_return_product (`return_id`,`product_id`,`product`,`quantity`,`price`,`total`,`return_product_credits`)
                        values('".$return_id."','".$value[0]."','".$value[1]."','".$value[2]."','".$value[3]."','".$value[2]*$value[3]."','".$value[2]*$value[3]."')";

                    $bool = $bool && $this->db->query($sql);

                    $return_credits_total += $value[2]*$value[3];

                }
                //更新oc_return表里面的退款金额
                $sql = "update oc_return set return_credits = '".$return_credits_total."' where return_id = '".$return_id."'";

                $bool = $bool && $this->db->query($sql);
                if($bool){
                    $this->db->query('COMMIT');

                    return true;
                }
                else{
                    $this->db->query("ROLLBACK");
                }
            }
            if($order_info['order_payment_status_id'] == 3){

                //部分余额支付的退款处理办法
                $this->db->query('START TRANSACTION');

                $sql = "INSERT INTO oc_return (`order_id`,`customer_id`,`firstname`,`telephone`,`return_reason_id`,`return_action_id`,`date_ordered`,`return_status_id`,`add_user`,`date_added`)
                values('".$order_id."','".$order_info['customer_id']."','".$order_info['firstname']."','".$order_info['telephone']."','1','3','".$order_info['date_ordered']."','1','".$user_id."',current_timestamp())";

                //执行oc_return的插入，并且累加得到oc_return的退货金额
                $query = $this->db->query($sql);

                $return_id = $this->db->getLastId();


                $bool = 1;

                $return_credits_total = 0;

                foreach($products as $value){
                    $sql_u = "update oc_return_deliver_product set confirmed = 1, return_id = '".$return_id."' where order_id = '".$order_id."' and product_id = '$value[0]'";

                    $update = $this->db->query($sql_u);

                    $sql = "INSERT INTO oc_return_product (`return_id`,`product_id`,`product`,`quantity`,`price`,`total`,`return_product_credits`)
                        values('".$return_id."','".$value[0]."','".$value[1]."','".$value[2]."','".$value[3]."','".$value[2]*$value[3]."','".$value[2]*$value[3]."')";

                    $bool = $bool && $this->db->query($sql);

                    $return_credits_total += $value[2]*$value[3];
                }

                //更新oc_return表里面的退款金额
                $sql = "update oc_return set return_credits = '".$return_credits_total."' where return_id = '".$return_id."'";

                $bool = $bool && $this->db->query($sql);
                if($bool){
                    $this->db->query('COMMIT');

                    return true;
                }
                else{
                    $this->db->query("ROLLBACK");
                }
            }

        }else{
            return false;
        }
    }
}