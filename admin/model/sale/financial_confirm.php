<?php
class ModelSaleFinancialConfirm extends  Model {

    public function getPaymentStatus(){
        $sql = "SELECT order_payment_status_id,name FROM oc_order_payment_status";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function  checkOrders($orderids_ids,$userId){
        $invalidOrders = array();
        $invalidOrders2 = array();
        $invalidOrders3 = array();
        foreach($orderids_ids as $v){
            //找无效的订单id
            $str = "SELECT  "." order_id  "."FROM "."oc_order "."WHERE "."order_id "."=".$v;
            $invalid = $this->db->query($str)->row;
            if(!$invalid){
                $invalidOrders[]=$v;
            }

        }
        $work = array_merge(array_diff($orderids_ids, $invalidOrders));
        $result = array(
            'yes'=>$work,
            'no'=>$invalidOrders
        );




//        foreach($result['yes'] as $v1){
//           //查找oc_order_history 根据状态查找不存在的order_id ；
//            $sql = "SELECT order_id , order_status_id ,order_payment_status_id ,order_deliver_status_id FROM oc_order WHERE order_id = '". $v1 ."'";
//            $query = $this->db->query($sql);
//            $result =  $query->row;
//
//            $str1 = "SELECT  "." order_id  "."FROM "."oc_order_history "."WHERE "."order_id "."=".$result['order_id'] ." and "."order_status_id"."=". $result['order_status_id'] ." and "."order_payment_status_id"."=". $result['order_payment_status_id'] ." and "."order_deliver_status_id"."=". $result['order_deliver_status_id'] ;
//
//            $invalid1 = $this->db->query($str1)->row;
//            if(!$invalid1){
//                $invalidOrders1[]=$v1;
//            }
//
//        }
      //  $invalidOrders6 = array_merge(array_diff($work, $invalidOrders1));

        if($work) {
            foreach ($work as $val) {
                $sql = "select order_id, 0 AS notify, 0 AS reason_id , order_status_id, order_payment_status_id, order_deliver_status_id, 1 from oc_order
where order_status_id  IN (6,10) AND order_deliver_status_id IN (2,3)  AND order_id = ' ".$val." '";

                $invalid2 = $this->db->query($sql)->row;
                if ($invalid2) {
                    $invalidOrders2[] = $invalid2;
                    $invalidOrders3[] = $val;
                }
            }

            $comment = "批量确认支付";
            $s2 = implode(',', $invalidOrders3);
            $confirm = count($invalidOrders2);

            if ($invalidOrders2) {
                $sql = "INSERT INTO `oc_order_history` ( `order_id`, `notify`, `reason_id`, `comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`) VALUES ";
                foreach ($invalidOrders2 as $k => $item) {
                    $sql .= "('" . $item['order_id'] . "','" . $item['notify'] . " ','" . $item['reason_id'] . " ',' " . $comment . "' ,  now() ,'" . $item['order_status_id'] . "','" . $item['order_payment_status_id'] . " ',' " . $item['order_deliver_status_id'] . " ','" . $userId . "'),";
                }
                $sql = rtrim($sql, ',');

                $query = $this->db->query($sql);

                if ($query) {
                    $sql = "update oc_order set order_payment_status_id = 2 where order_status_id  IN (6,10) AND order_deliver_status_id IN (2,3) AND order_payment_status_id = 1 AND order_id IN (" . $s2 . ")  ";
                    $query = $this->db->query($sql);
                }
            }
            $invalidOrders5 = array_merge(array_diff($work, $invalidOrders3));
            $un_confirm = count($invalidOrders5);
            $result = array($un_confirm,$confirm,$invalidOrders,$work);
        }else{
            $result = array(0, 0,$invalidOrders,$work);
        }
       return $result;
    }


//    public  function getOrdersInfo($orderids){
//        $sql = "SELECT  A.order_id,C.name order_status_name ,A.sub_total,A.discount_total ,A.shipping_fee ,A.credit_paid,A.total , B.name payment_status_name ,D.name deliver_status_name FROM oc_order A LEFT JOIN oc_order_payment_status B ON A.order_payment_status_id = B.order_payment_status_id LEFT JOIN oc_order_status C ON A.order_status_id = C.order_status_id LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id = D.order_deliver_status_id WHERE  A.order_status_id  IN (6,10) AND A.order_deliver_status_id IN (2,3)AND A.order_payment_status_id = 1 AND A.order_id = ' $orderids ' ";
//        $query = $this->db->query($sql);
//        return $query->rows;
//    }

    public  function getUnOrdersInfo($orderids){

        $sql = "SELECT A.order_id,C.name order_status_name ,A.sub_total,A.discount_total ,A.shipping_fee ,A.credit_paid,A.total , B.name payment_status_name ,D.name deliver_status_name FROM oc_order A LEFT JOIN oc_order_payment_status B ON A.order_payment_status_id = B.order_payment_status_id LEFT JOIN oc_order_status C ON A.order_status_id = C.order_status_id LEFT JOIN oc_order_deliver_status D ON A.order_deliver_status_id = D.order_deliver_status_id WHERE  ( A.order_status_id NOT IN (6,10)  OR A.order_deliver_status_id NOT IN (2,3) ) AND  A.order_id = ' $orderids ' ";
        $query = $this->db->query($sql);
     
        return $query->rows;
    }



}