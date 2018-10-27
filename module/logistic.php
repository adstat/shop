<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');

class LOGISTIC{

    function getUserInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $db->escape($data['uid']) : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;
        $key = isset($data['key']) && $data['key'] ? $data['key'] : false;

        if(!$uid){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Not receive uid';
            $returnData = array();
        }
        else{
            $sql = "SELECT * from oc_user where md5(concat(username,'".$key."','".$code."')) ='" . $uid . "'";
            $query = $db->query($sql);
            if($query->row){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'ok';
                $returnData = $query->row;
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;
    }

    function updateStatus(array $data){
        global  $db;
        $uid = isset($data['uid']) && $data['uid'] ? $db->escape($data['uid']) : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;
        $key = isset($data['key']) && $data['key'] ? $data['key'] : false;

        if(!$uid){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Not receive uid';
            $returnData = array();
        }else{
            $sql =  "update  oc_order set order_deliver_status_id = 3  WHERE order_id = '".$data['data']['order_id']."' ";
            $query = $db->query($sql);
            if($query){
              $sql = "select A.order_status_id,A.order_payment_status_id ,la.logistic_driver_id  from  oc_order A
                      LEFT JOIN oc_x_logistic_allot_order lao ON  A.order_id = lao.order_id
                      LEFT JOIN  oc_x_logistic_allot la ON la.logistic_allot_id = lao.logistic_allot_id
                      LEFT JOIN  oc_x_logistic_driver ld ON  ld.logistic_driver_id = la.logistic_driver_id
                       WHERE A.order_id = '".$data['data']['order_id']."'";
                $query = $db->query($sql);
                $result = $query->row;

                if($result){
                    $comment = "司机更改配送完成";
                    $sql = "insert into oc_order_history  (`order_id`,`comment`,`order_status_id`,`order_payment_status_id`,`order_deliver_status_id`,`modified_by`,`date_added`) VALUES ('".$data['data']['order_id'] ."','".$comment."','".$result['order_status_id'] ."', '".$result['order_payment_status_id']."','3','".$result['logistic_driver_id']."',NOW() )";

                    $query = $db->query($sql);
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'SUCCESS';
                    $returnData = $query->row;
                }

            }
        }
        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;
}

    function updateStatus2(array $data){
        global  $db;
        $uid = isset($data['uid']) && $data['uid'] ? $db->escape($data['uid']) : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;
        $key = isset($data['key']) && $data['key'] ? $data['key'] : false;
        $order_id = isset($data['data']['order_id']) && $data['data']['order_id'] ? $data['data']['order_id'] : false;
        $product_id = isset($data['data']['product_id']) && $data['data']['product_id'] ? $data['data']['product_id'] : false;
        $in_part = isset($data['data']['in_part']) && $data['data']['in_part'] ? $data['data']['in_part'] : false;

        if(!$uid){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Not receive uid';
            $returnData = array();
        }else{
            $sql = "update oc_return_deliver_product set status = 2 WHERE order_id = '". $order_id ."' and product_id = '". $product_id ."'  and in_part = '".$in_part ."' and status = 1";
            $query = $db->query($sql);
            if($query){
                $comment = "司机确认收到退货";
                $sql = "insert into oc_return_deliver_product_history (`order_id`,`product_id`,`in_part`,`add_user`,`date_added`,`status` ,`comment` ) VALUES ('". $order_id ."','". $product_id ."','".$in_part ."','". $code ."' ,NOW(),'2','". $comment ."')";
                $query = $db->query($sql);

                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'SUCCESS';
                $returnData = $query->row;
            }
        }
        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;

    }

    function getDriverInfo(array $data){

        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $db->escape($data['uid']) : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;
        $key = isset($data['key']) && $data['key'] ? $data['key'] : false;

        if(!$uid){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Not receive uid';
            $returnData = array();

        }
        else{
            $sql = "SELECT logistic_driver_id, logistic_driver_title, logistic_driver_phone from oc_x_logistic_driver where md5(concat(crm_username,'".$key."','".$code."')) ='" . $uid . "'";


            $query = $db->query($sql);
            if($query->row){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'ok';
                $returnData = $query->row;
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;
    }

    private function stripSearchKeyword($keyword){
        if($keyword){
            $str = trim(chop(strip_tags($keyword)));
            $find = array(";","*","\n","\r","%","$","&","-","_","+","<",">","=","/","\\","(",")","{","}",".",",","!","\"","'");
            $str = str_replace($find,'',$str);
            $strArr = explode(' ',$str);
            $keyword = '';
            foreach($strArr as $k){
                if(strlen($k)){
                    $keyword .= '%'.$k;
                }
            }
        }

        return $keyword;
    }


    /**
     * @param array $data
     * @return array
     */
    function getCustomerInfo(array $data){

        global $db;
//return 131;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;

        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;
        $date = isset($data['data']['date']) && $data['data']['date'] ? $data['data']['date'] : false;
        $logistic_allot_id = isset($data['data']['logistic_allot_id']) && $data['data']['logistic_allot_id'] ? $data['data']['logistic_allot_id'] : '';

        $driverInfo = $this->getDriverInfo($data);
        $driver_id = $driverInfo['return_data']['logistic_driver_id'];
        if(!$uid || !$driver_id ){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $driverInfo;
        }else{
           if(!$logistic_allot_id){
               $customerSql = "select C.order_deliver_status_id as status,A.deliver_date, A.logistic_allot_id ,C.shipping_phone, B.order_id ,C.shipping_address_1 ,C.shipping_firstname ,C.date_added,C.payment_method,F.bd_name,F.phone, A.logistic_line_title, D.frame_count,D.frame_meat_count,D.incubator_count,D.incubator_mi_count,D.foam_count,D.foam_ice_count,D.frame_ice_count,D.frame_mi_count,D.box_count,E.quantity
from oc_x_logistic_allot A
LEFT JOIN  oc_x_logistic_allot_order B on A.logistic_allot_id = B.logistic_allot_id
LEFT  JOIN  oc_x_logistic_driver ld ON  ld.logistic_driver_id = A.logistic_driver_id
LEFT JOIN oc_order C on B.order_id = C.order_id
 LEFT JOIN  oc_order_inv D ON C.order_id = D.order_id
 LEFT JOIN oc_order_product E ON C.order_id = E.order_id
 LEFT JOIN oc_x_bd F ON C.bd_id = F.bd_id
 WHERE A.logistic_driver_id  = '".$driver_id ."' AND
 DATE(A.deliver_date) = '".$date."'  AND C.order_status_id !=3     ";
               if($keyword){
                   $customerSql.=" and B.order_id = '".$keyword."'";
               }
           }else{
               $customerSql = "select C.order_deliver_status_id as status,A.deliver_date, A.logistic_allot_id ,C.shipping_phone, B.order_id ,C.shipping_address_1 ,C.shipping_firstname ,C.date_added,C.payment_method,F.bd_name,F.phone, A.logistic_line_title, D.frame_count,D.frame_meat_count,D.incubator_count,D.incubator_mi_count,D.foam_count,D.foam_ice_count,D.frame_ice_count,D.frame_mi_count,D.box_count,E.quantity
from oc_x_logistic_allot A
LEFT JOIN  oc_x_logistic_allot_order B on A.logistic_allot_id = B.logistic_allot_id
LEFT  JOIN  oc_x_logistic_driver ld ON  ld.logistic_driver_id = A.logistic_driver_id
LEFT JOIN oc_order C on B.order_id = C.order_id
 LEFT JOIN  oc_order_inv D ON C.order_id = D.order_id
 LEFT JOIN oc_order_product E ON C.order_id = E.order_id
 LEFT JOIN oc_x_bd F ON C.bd_id = F.bd_id
 WHERE A.logistic_driver_id  = '".$driver_id ."' AND
 A.logistic_allot_id = '".$logistic_allot_id."'  AND C.order_status_id !=3     ";
               if($keyword){
                   $customerSql.=" and B.order_id = '".$keyword."'";
               }
           }
return $customerSql;
            $queryCustomers = $db->query($customerSql);

            if(sizeof($queryCustomers->rows)){
                foreach($queryCustomers->rows as $m){
                    $ordersInfo[$m['order_id']] = $m;
                }
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                $returnData = $ordersInfo;
            }
            else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NO RECORD';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;
    }

    function getOrderNoInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;

        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;
        $date = isset($data['data']['date']) && $data['data']['date'] ? $data['data']['date'] : false;
        $logistic_allot_id = isset($data['data']['logistic_allot_id']) && $data['data']['logistic_allot_id'] ? $data['data']['logistic_allot_id'] : '';
        $driverInfo = $this->getDriverInfo($data);

        $driver_id = $driverInfo['return_data']['logistic_driver_id'];

        if(!$uid || !$driver_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $driverInfo;
        }else{
            if(!$logistic_allot_id){
                $customerSql = "select C.order_deliver_status_id as status,ds.name,A.deliver_date, A.logistic_allot_id ,C.shipping_phone, B.order_id ,C.shipping_address_1 ,C.shipping_firstname ,C.date_added,C.payment_method,F.bd_name,F.phone, A.logistic_line_title, D.frame_count,D.frame_meat_count,D.incubator_count,D.incubator_mi_count,D.foam_count,D.foam_ice_count,D.frame_ice_count,D.frame_mi_count,D.box_count,E.quantity
from oc_x_logistic_allot A
LEFT JOIN  oc_x_logistic_allot_order B on A.logistic_allot_id = B.logistic_allot_id
LEFT  JOIN  oc_x_logistic_driver ld ON  ld.logistic_driver_id = A.logistic_driver_id
LEFT JOIN oc_order C on B.order_id = C.order_id
LEFT JOIN  oc_order_deliver_status ds ON  C.order_deliver_status_id = ds.order_deliver_status_id
 LEFT JOIN  oc_order_inv D ON C.order_id = D.order_id
 LEFT JOIN oc_order_product E ON C.order_id = E.order_id
 LEFT JOIN oc_x_bd F ON C.bd_id = F.bd_id
 WHERE A.logistic_driver_id = '".$driver_id ."' AND
  DATE(A.deliver_date) = '".$date."' AND C.order_status_id !=3 AND C.order_deliver_status_id IN (1,2)   ";
                if($keyword){
                    $customerSql.=" and B.order_id = '".$keyword."'";
                }
            }else{
                $customerSql = "select C.order_deliver_status_id as status,ds.name,A.deliver_date, A.logistic_allot_id ,C.shipping_phone, B.order_id ,C.shipping_address_1 ,C.shipping_firstname ,C.date_added,C.payment_method,F.bd_name,F.phone, A.logistic_line_title, D.frame_count,D.frame_meat_count,D.incubator_count,D.incubator_mi_count,D.foam_count,D.foam_ice_count,D.frame_ice_count,D.frame_mi_count,D.box_count,E.quantity
from oc_x_logistic_allot A
LEFT JOIN  oc_x_logistic_allot_order B on A.logistic_allot_id = B.logistic_allot_id
LEFT  JOIN  oc_x_logistic_driver ld ON  ld.logistic_driver_id = A.logistic_driver_id
LEFT JOIN oc_order C on B.order_id = C.order_id
LEFT JOIN  oc_order_deliver_status ds ON  C.order_deliver_status_id = ds.order_deliver_status_id
 LEFT JOIN  oc_order_inv D ON C.order_id = D.order_id
 LEFT JOIN oc_order_product E ON C.order_id = E.order_id
 LEFT JOIN oc_x_bd F ON C.bd_id = F.bd_id
 WHERE A.logistic_driver_id = '".$driver_id ."' AND
  A.logistic_allot_id = '".$logistic_allot_id."' AND C.order_status_id !=3 AND C.order_deliver_status_id IN (1,2)   ";
                if($keyword){
                    $customerSql.=" and B.order_id = '".$keyword."'";
                }
            }


            $queryCustomers = $db->query($customerSql);

            if(sizeof($queryCustomers->rows)){
                foreach($queryCustomers->rows as $m){
                    $ordersInfo[$m['order_id']] = $m;
                }
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                $returnData = $ordersInfo;
            }
            else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NO RECORD';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;

    }



    function getOrderAllInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;

        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;
        $date = isset($data['data']['date']) && $data['data']['date'] ? $data['data']['date'] : false;
        $logistic_allot_id = isset($data['data']['logistic_allot_id']) && $data['data']['logistic_allot_id'] ? $data['data']['logistic_allot_id'] : '';
        $driverInfo = $this->getDriverInfo($data);

        $driver_id = $driverInfo['return_data']['logistic_driver_id'];

        if(!$uid || !$driver_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $driverInfo;
        }else{
           if(!$logistic_allot_id){
               $customerSql = "select C.order_deliver_status_id as status,ds.name,A.deliver_date, A.logistic_allot_id ,C.shipping_phone, B.order_id ,C.shipping_address_1 ,C.shipping_firstname ,C.date_added,C.payment_method,F.bd_name,F.phone, A.logistic_line_title, D.frame_count,D.frame_meat_count,D.incubator_count,D.incubator_mi_count,D.foam_count,D.foam_ice_count,D.frame_ice_count,D.frame_mi_count,D.box_count,E.quantity
from oc_x_logistic_allot A
LEFT JOIN  oc_x_logistic_allot_order B on A.logistic_allot_id = B.logistic_allot_id
LEFT  JOIN  oc_x_logistic_driver ld ON  ld.logistic_driver_id = A.logistic_driver_id
LEFT JOIN oc_order C on B.order_id = C.order_id
LEFT JOIN  oc_order_deliver_status ds ON  C.order_deliver_status_id = ds.order_deliver_status_id
 LEFT JOIN  oc_order_inv D ON C.order_id = D.order_id
 LEFT JOIN oc_order_product E ON C.order_id = E.order_id
 LEFT JOIN oc_x_bd F ON C.bd_id = F.bd_id
 WHERE A.logistic_driver_id = '".$driver_id  ."' AND
  DATE(A.deliver_date) = '".$date."' AND C.order_status_id !=3  ";
               if($keyword){
                   $customerSql.=" and B.order_id = '".$keyword."'";
               }
           }else{
               $customerSql = "select C.order_deliver_status_id as status,ds.name,A.deliver_date, A.logistic_allot_id ,C.shipping_phone, B.order_id ,C.shipping_address_1 ,C.shipping_firstname ,C.date_added,C.payment_method,F.bd_name,F.phone, A.logistic_line_title, D.frame_count,D.frame_meat_count,D.incubator_count,D.incubator_mi_count,D.foam_count,D.foam_ice_count,D.frame_ice_count,D.frame_mi_count,D.box_count,E.quantity
from oc_x_logistic_allot A
LEFT JOIN  oc_x_logistic_allot_order B on A.logistic_allot_id = B.logistic_allot_id
LEFT  JOIN  oc_x_logistic_driver ld ON  ld.logistic_driver_id = A.logistic_driver_id
LEFT JOIN oc_order C on B.order_id = C.order_id
LEFT JOIN  oc_order_deliver_status ds ON  C.order_deliver_status_id = ds.order_deliver_status_id
 LEFT JOIN  oc_order_inv D ON C.order_id = D.order_id
 LEFT JOIN oc_order_product E ON C.order_id = E.order_id
 LEFT JOIN oc_x_bd F ON C.bd_id = F.bd_id
 WHERE A.logistic_driver_id = '".$driver_id  ."' AND
  A.logistic_allot_id = '".$logistic_allot_id."' AND C.order_status_id !=3  ";
               if($keyword){
                   $customerSql.=" and B.order_id = '".$keyword."'";
               }
           }


            $queryCustomers = $db->query($customerSql);

            if(sizeof($queryCustomers->rows)){
                foreach($queryCustomers->rows as $m){
                    $ordersInfo[$m['order_id']] = $m;
                }
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                $returnData = $ordersInfo;
            }
            else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NO RECORD';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;

    }

    function getOrderAlreadyInfo(array $data){
        global $db;


        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;

        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;
        $date = isset($data['data']['date']) && $data['data']['date'] ? $data['data']['date'] : false;
        $logistic_allot_id = isset($data['data']['logistic_allot_id']) && $data['data']['logistic_allot_id'] ? $data['data']['logistic_allot_id'] : '';
        $driverInfo = $this->getDriverInfo($data);

        $driver_id = $driverInfo['return_data']['logistic_driver_id'];

        if(!$uid || !$driver_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $driverInfo;
        }else{
            if(!$logistic_allot_id){
                $customerSql = "select C.order_deliver_status_id as status,ds.name,A.deliver_date, A.logistic_allot_id ,C.shipping_phone, B.order_id ,C.shipping_address_1 ,C.shipping_firstname ,C.date_added,C.payment_method,F.bd_name,F.phone, A.logistic_line_title, D.frame_count,D.frame_meat_count,D.incubator_count,D.incubator_mi_count,D.foam_count,D.foam_ice_count,D.frame_ice_count,D.frame_mi_count,D.box_count,E.quantity
from oc_x_logistic_allot A
LEFT JOIN  oc_x_logistic_allot_order B on A.logistic_allot_id = B.logistic_allot_id
LEFT  JOIN  oc_x_logistic_driver ld ON  ld.logistic_driver_id = A.logistic_driver_id
LEFT JOIN oc_order C on B.order_id = C.order_id
LEFT JOIN  oc_order_deliver_status ds ON  C.order_deliver_status_id = ds.order_deliver_status_id
 LEFT JOIN  oc_order_inv D ON C.order_id = D.order_id
 LEFT JOIN oc_order_product E ON C.order_id = E.order_id
 LEFT JOIN oc_x_bd F ON C.bd_id = F.bd_id
 WHERE A.logistic_driver_id = '". $driver_id ."' AND
 DATE(A.deliver_date) = '".$date."' AND C.order_status_id !=3 AND C.order_deliver_status_id = 3   ";
                if($keyword){
                    $customerSql.=" and B.order_id = '".$keyword."'";
                }
            }else{
                $customerSql = "select C.order_deliver_status_id as status,ds.name,A.deliver_date, A.logistic_allot_id ,C.shipping_phone, B.order_id ,C.shipping_address_1 ,C.shipping_firstname ,C.date_added,C.payment_method,F.bd_name,F.phone, A.logistic_line_title, D.frame_count,D.frame_meat_count,D.incubator_count,D.incubator_mi_count,D.foam_count,D.foam_ice_count,D.frame_ice_count,D.frame_mi_count,D.box_count,E.quantity
from oc_x_logistic_allot A
LEFT JOIN  oc_x_logistic_allot_order B on A.logistic_allot_id = B.logistic_allot_id
LEFT  JOIN  oc_x_logistic_driver ld ON  ld.logistic_driver_id = A.logistic_driver_id
LEFT JOIN oc_order C on B.order_id = C.order_id
LEFT JOIN  oc_order_deliver_status ds ON  C.order_deliver_status_id = ds.order_deliver_status_id
 LEFT JOIN  oc_order_inv D ON C.order_id = D.order_id
 LEFT JOIN oc_order_product E ON C.order_id = E.order_id
 LEFT JOIN oc_x_bd F ON C.bd_id = F.bd_id
 WHERE A.logistic_driver_id = '". $driver_id ."' AND
 A.logistic_allot_id = '".$logistic_allot_id."' AND C.order_status_id !=3 AND C.order_deliver_status_id = 3   ";
                if($keyword){
                    $customerSql.=" and B.order_id = '".$keyword."'";
                }
            }

            $queryCustomers = $db->query($customerSql);

            if(sizeof($queryCustomers->rows)){
                foreach($queryCustomers->rows as $m){
                    $ordersInfo[$m['order_id']] = $m;
                }
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                $returnData = $ordersInfo;
            }
            else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NO RECORD';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;

    }

    function  getReturnOrders(array  $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;
        if($keyword){
            $sql = " select rdp.order_id,
                rdp.product_id,
                rdp.product,
                rdp.date_added,
                SUM(rdp.quantity) as quantity,
                rdp.price,
                SUM(rdp.total) as total,
                if(rdp.in_part =0,'整件','散件') as part,
                rdp.in_part,
                oo.customer_id,
                oc.firstname,
                oc.merchant_name,
                oc.merchant_address,
                rdp.status,
                rdp.date_added
              from oc_return_deliver_product rdp
              LEFT JOIN  oc_order oo ON  rdp.order_id = oo.order_id
              LEFT JOIN  oc_customer oc ON  oc.customer_id = oo.customer_id
              where   rdp.order_id = '". $keyword ."' and rdp.status = 1
              GROUP BY
              rdp.order_id,
              rdp.product_id,
              rdp.in_part,
              rdp.status
              order by rdp.status
               ";
            $queryCustomers = $db->query($sql);
        }

        if(sizeof($queryCustomers->rows)){
            foreach($queryCustomers->rows as $m){
                $ordersInfo[] = $m;
            }
            $returnCode = API_RETURN_SUCCESS;
            $returnMessage = 'OK';
            $returnData = $ordersInfo;
        }
        else{
            $returnCode = API_RETURN_SUCCESS;
            $returnMessage = 'NO RECORD';
            $returnData = array();
        }


        $return = array(
        'return_code' => $returnCode,
        'return_msg'  => $returnMessage,
        'return_data' => $returnData
        );

        return $return;

     }

    public function deliverConfirmReturnProduct(array $data)
    {

        global $db, $dbm;
        global $log;

        //TODO 计算订单应收金额
        //TODO 计算缺货金额
        //TODO 应收金额 >= 缺货金额,  仅退货
        //TODO 应收金额 < 缺货金额,  退余额＝缺货金额－应收金额，实际应收为0，退余额待财务确认


        $isBack          = !empty($data['data']['isBack'])          ? (int)$data['data']['isBack']          : false;
        $inPart          = !empty($data['data']['inPart'])          ? (int)$data['data']['inPart']          : 0;
        $isRepackMissing = !empty($data['data']['isRepackMissing']) ? (int)$data['data']['isRepackMissing'] : 0;
        $order_id        = !empty($data['data']['order_id'])        ? (int)$data['data']['order_id']        : false;
        $driverInfo = $this->getDriverInfo($data);
        $driver_id = $driverInfo['return_data']['logistic_driver_id'];

//        if(!$isBack || !$order_id || !$driver_id){
//            return array('status' => 0, 'message' => "缺少关键参数，请刷新页面重新提交");
//        }


        //查找指定日期，有效的且未确认的退货记录
        $sql = "select order_id, sum(total) current_return_total
                    from oc_return_deliver_product
                    where status = 1
                    and order_id = {$order_id}
                    and confirmed = 0
                    and confirm_driver_id = 0
                    and is_back = {$isBack}
                    and in_part = {$inPart}
                    and is_repack_missing = {$isRepackMissing}
                    group by order_id";
        $query             = $db->query($sql);
        $currentReturnInfo = $query->row;

        if(empty($currentReturnInfo)){
            return array('status' => 0, 'message' => "没有该订单对货申请记录");
        }

        //查找订单应付
        $sql = "select order_id, sum(value) due_total
                    from oc_order_total
                    where accounting = 1
                    and order_id = {$order_id}
                    group by order_id";
        $query   = $db->query($sql);
        $dueInfo = $query->row;

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
                    where O.order_id = {$order_id}
                    and B.inventory_type_id = 12
                    and C.status = 1
                    group by O.order_id";
        $query   = $db->query($sql);
        $outInfo = $query->row;

        //查找已退货且退余额数据
        $sql = "select R.order_id, sum(R.return_credits) return_total, sum(if(CT.amount is null, 0, CT.amount)) return_credits_total
                    from oc_return R
                    left join oc_customer_transaction CT on R.return_id = CT.return_id
                    where R.order_id = {$order_id}
                    and R.return_status_id != 3
                    and R.return_reason_id = 1
                    and R.return_action_id = 3
                    group by R.order_id";
        $query        = $db->query($sql);
        $returnedInfo = $query->row;


        //依次单个订单
        $currentReturnTotal = $currentReturnInfo['current_return_total'];

        $dueTotal    = !empty($dueInfo)      ? $dueInfo['due_total']         : 0;
        $subTotal    = !empty($outInfo)      ? $outInfo['sub_total']         : 0;
        $outTotal    = !empty($outInfo)      ? $outInfo['out_total']         : 0;
        $returnTotal = !empty($returnedInfo) ? $returnedInfo['return_total'] : 0;


        //出库应收=｛实际出库-(小计-应付)｝
        //出库应退＝｛小计-应付-实际出库｝
        //出库缺货应收1=｛出库应收-出库缺货1｝
        //出库缺货应退1={出库缺货1-出库应收}
        $dueOut         = $outTotal - ($subTotal-$dueTotal);

        $dueCurrent     = $dueOut-$returnTotal-$currentReturnTotal;//本次退货应收 = 出库应收－已退货－本次退货
        $returnCurrent  = ($dueCurrent < 0) ? abs($dueCurrent) : 0;//计算退货后后本次应收小于0，退余额
        //判断是否全部退货或退货金额占订单80%以上，不退余额
        if($currentReturnTotal >= $dueOut*0.8){
            $returnCurrent = 0;
        }

        //根据是出货退货和是否退余额确定退货操作
        $returnCredits    = 0;
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

        //For Debug
        $currentReturn = array(
            'dueTotal'           => $dueTotal,
            'subTotal'           => $subTotal,
            'outTotal'           => $outTotal,
            'dueOut'             => $dueOut,
            'currentReturnTotal' => $currentReturnTotal,
            'dueCurrnet'         => $dueCurrent,
            'returnCredits'      => $returnCredits,
            'return_reason_id'   => $return_reason_id,
            'return_action_id'   => $return_action_id
        );

        $returnAll = $returnTotal + $currentReturnTotal;
        if($returnAll > $outTotal){
            //退货合计超过出库合计，跳过
            return array('status' => 0, 'message' => "确认退货失败, 部分订单退货金额有误，请核实[{$order_id}]");
        }

        $dbm->begin();
        $bool = true;

        //写入退货表
        $sql = "INSERT INTO `oc_return` (`order_id`, `customer_id`, `return_reason_id`, `return_action_id`, `return_status_id`, `comment`, `date_ordered`, `date_added`, `date_modified`, `add_user`, `return_credits`, `return_inventory_flag`, `credits_returned`,`inventory_returned`, `confirm_driver_id`, `date_driver_confirm`)
                VALUES('{$order_id}', '{$outInfo['customer_id']}', '{$return_reason_id}', '{$return_action_id}', '2', '司机确认退货', '{$outInfo['order_date']}', '0', '0', '0', '{$returnCredits}', '1' ,'0' ,'{$inventoryReturned}', '{$driver_id}', NOW());";

        $bool = $bool && $dbm->query($sql);
        $return_id = $dbm->getLastId();

        $sql = "INSERT INTO `oc_return_product` (`return_id`, `product_id`, `product`,  `quantity`, `in_part`, `box_quantity`, `price`, `total`, `return_product_credits`)
                SELECT '{$return_id}', `product_id`, `product`,  `quantity`,  `in_part`, `box_quantity`, `price`, `total`,  `total`
                FROM oc_return_deliver_product
                WHERE status = 1
                AND confirmed = 0
                AND order_id = '{$order_id}'
                AND is_back = '{$isBack}'
                AND is_repack_missing = '{$isRepackMissing}'";
        $bool = $bool && $dbm->query($sql);

        //完成后更新出库回库记录状态
        $sql = "UPDATE oc_return_deliver_product set confirm_driver_id = '{$driver_id}', date_driver_confirm = NOW(), return_id = '{$return_id}',status = 2
                WHERE status = 1
                AND order_id = '{$order_id}'
                AND is_back = '{$isBack}'
                AND is_repack_missing = '{$isRepackMissing}'";
        $bool = $bool && $dbm->query($sql);

        if (!$bool) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ':  出库缺货开始退货［回滚］' . "\n\r");
            $dbm->rollback();

            return array('status' => 0, 'message' => '确认退货失败');
        } else {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 出库缺货开始退货［提交］' . "\n\r");
            $dbm->commit();

            return array('status' => 1, 'message' => '确认退货成功');
        }
    }



}

$logistic = new LOGISTIC();
?>