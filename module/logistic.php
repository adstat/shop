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
              where   rdp.order_id = '". $keyword ."'
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



}

$logistic = new LOGISTIC();
?>