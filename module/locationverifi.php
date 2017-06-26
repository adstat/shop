<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');
class LOCATIONVERIFI{
    public function getLocationOrderStatus(){
        global $db;
        $sql = "SELECT  order_status_id ,name   FROM  oc_order_status  WHERE order_status_id IN (6,8)";
        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }


    public function getOrderByStatus(array $data){
        global $db;

        $date_start = $data['data']['date_start'] ? $data['data']['date_start'] : '';
        $date_end = $data['data']['date_end'] ? $data['data']['date_end'] : '';
        $order_status_id= $data['data']['order_status_id'] ? $data['data']['order_status_id'] : '';
        $order_check_status = $data['data']['order_check_status'] ? $data['data']['order_check_status'] : '0';
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';

        $sql = "SELECT A.check_status,D.order_status_id, A.inv_comment, A.order_id ,C.inventory_name,C.quantity,E.name,CL.reasons,A.frame_count,A.box_count
FROM  oc_order_inv A
LEFT JOIN oc_x_inventory_order_sorting B ON A.order_id = B.order_id
 LEFT JOIN  oc_order_distr C ON  A.order_id = C.order_id
 LEFT JOIN oc_order D ON  A.order_id = D.order_id
 LEFT JOIN  oc_order_status E ON D.order_status_id = E.order_status_id
 LEFT JOIN( SELECT F.order_id ,LR.reason_name AS reasons FROM oc_x_order_check_location F LEFT JOIN  oc_x_order_check_location_reason LR ON F.reasons = LR.check_location_reason_id  ) AS CL ON A.order_id = CL.order_id WHERE  1=1 AND D.order_status_id IN (6,8) and D.order_deliver_status_id = 1 AND A.check_status = '".$order_check_status."'";



        if (!empty($date_start)) {
            $sql .= " AND A.uptime >= '" . $date_start . "'";
        }

        if (!empty($date_end)) {
            $sql .= " AND A.uptime <= '" .$date_end. "'";
        }

        if (!empty($order_id)) {
            $sql .= " AND A.order_id = '" . $order_id . "'";
        }

        if(!empty($order_status_id)){
            $sql .= " AND D.order_status_id = '" . $order_status_id . "'";
        }

         $sql .=" GROUP BY A.order_id";
         $sql .=" order by (A.inv_comment +0) asc  ";

        $query = $db->query($sql);
        $result = $query->rows;

        return $result;

    }


    public function getSumCheckOrder(array  $data){
        global $db;
        $date_start = $data['data']['date_start'] ? $data['data']['date_start'] : '';
        $date_end = $data['data']['date_end'] ? $data['data']['date_end'] : '';


        $sql = "SELECT COUNT(1) AS  sum FROM oc_order_inv A LEFT JOIN oc_order B ON A.order_id =B.order_id LEFT JOIN oc_order_distr od ON   od.order_id = A.order_id WHERE B.order_status_id IN (6,8) and B.order_deliver_status_id = 1  AND A.check_status = 0  ";
        if (!empty($date_start)) {
            $sql .= " AND A.uptime >= '" . $date_start . "'";
        }
        if (!empty($date_end)) {
            $sql .= " AND A.uptime <= '" .$date_end. "'";
        }

        $query = $db->query($sql);
        $result = $query->row;

        $sql1 = "SELECT COUNT(1) AS sum1  FROM oc_order_inv A  LEFT JOIN oc_order B ON A.order_id =B.order_id LEFT JOIN oc_order_distr od ON   od.order_id = A.order_id WHERE B.order_status_id IN (6,8) and B.order_deliver_status_id = 1 AND  A.check_status = 1   ";
        if (!empty($date_start)) {
            $sql1 .= " AND A.uptime >= '" . $date_start . "'";
        }
        if (!empty($date_end)) {
            $sql1 .= " AND A.uptime <= '" .$date_end. "'";
        }

        $query1 = $db->query($sql1);
        $result1 = $query1->row;

        $return = array($result,$result1);
        return $return;

    }


    public function getCheckOrdersInfo(array $data){
        global $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';

        $sql = "SELECT A.check_status,D.order_status_id, A.inv_comment, A.order_id ,C.inventory_name,C.inventory_name,SUM(C.quantity) AS quantity ,E.name FROM  oc_order_inv A LEFT JOIN oc_x_inventory_order_sorting B ON A.order_id = B.order_id LEFT JOIN  oc_order_distr C ON  A.order_id = C.order_id LEFT JOIN oc_order D ON  A.order_id = D.order_id LEFT JOIN  oc_order_status E ON D.order_status_id = E.order_status_id WHERE  A.order_id = '".$order_id."' GROUP BY A.order_id ";
        $query = $db->query($sql);
        $result = $query->rows;

        return $result;

    }

    public function getContainer(array $data){
        global $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $sql = "SELECT  A.frame_vg_list FROM oc_order_inv A
 WHERE  A.order_id = '" . $order_id . "'  	";
        $query = $db->query($sql);
        $result = $query->row;


        $sql1 = "SELECT  A.frame_count FROM oc_order_inv A
 WHERE  A.order_id = '" . $order_id . "'  	";
         $query1 = $db->query($sql1);
         $result1 = $query1->row;

        $return['container'] = array($result,
                                      $result1);

        return $return;

    }

    public function getLocationOrderInfo(array $data){

         global $db;
         $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $sql = "  SELECT A.order_id , B.product_id ,sum(B.quantity) AS order_quantity ,C.sku ,C.name,sum(OM.quantity*-1) AS oty_quantity,C.station_section_title FROM oc_order A
                  LEFT JOIN oc_order_product B ON  A.order_id = B.order_id
                  LEFT JOIN oc_product C   ON  B.product_id = C.product_id
                  LEFT JOIN (SELECT O.order_id ,SMI.quantity , SMI.product_id FROM  oc_order O LEFT JOIN  oc_x_stock_move SM ON  O.order_id = SM.order_id
                              LEFT JOIN  oc_x_stock_move_item SMI ON  SM.inventory_move_id = SMI.inventory_move_id  WHERE O.order_id = '".$order_id ."' GROUP BY  SMI.product_id ) OM ON  OM.order_id = A.order_id and OM.product_id = B.product_id
           WHERE A.order_id = '".$order_id ."'    GROUP BY A.order_id, C.product_id ";
        $query = $db->query($sql);
        $result = $query->rows;


        $sql1 = "SELECT sum(B.quantity) AS order_quantity ,sum(OM.quantity*-1) AS oty_quantity,C.station_section_title FROM oc_order A
                  LEFT JOIN oc_order_product B ON  A.order_id = B.order_id
                  LEFT JOIN oc_product C   ON  B.product_id = C.product_id
                  LEFT JOIN (SELECT O.order_id ,SMI.quantity, SMI.product_id FROM  oc_order O LEFT JOIN  oc_x_stock_move SM ON  O.order_id = SM.order_id
                              LEFT JOIN  oc_x_stock_move_item SMI ON  SM.inventory_move_id = SMI.inventory_move_id  WHERE O.order_id = '".$order_id ."' ) OM ON  OM.order_id = A.order_id and OM.product_id = B.product_id
           WHERE A.order_id = '".$order_id ."' ";
        $query1 = $db->query($sql1);
        $result1 = $query1->rows;


        $result=array($result,$result1);

        return $result;

    }

    function getContainerInfo(array $data){
        global  $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $cantainer_id = $data['data']['container_id'] ? $data['data']['container_id'] : '';

        $sql = " SELECT  E.container_id FROM oc_order A LEFT JOIN  oc_x_container_move E ON  E.order_id = A.order_id
 WHERE  A.order_id = '" . $order_id . "' AND  E.container_id =  '" . $cantainer_id . "' ";
        $query = $db->query($sql);

        if ($query->num_rows) {
            $return['status'] = 2;
            $return['product'] = $query->rows;
            return $return;

        }
        else{
            $return['status'] = 1;
            return $return;
        }


    }



    public function submitCorrectionLocationOrder(array $data){
        global  $db;

        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $new_inv_comment = $data['data']['inv_comment'] ? $data['data']['inv_comment'] : '';
        $frames = $data['data']['frames'] ? $data['data']['frames'] : '';
        $user_id = $data['data']['user_id'] ? $data['data']['user_id'] : '';
        $check_value = $data['data']['check_value'] ? $data['data']['check_value'] : '';
        $frame_count = $data['data']['frame_count'] ? $data['data']['frame_count'] : '';
        $frames_ids = $data['data']['frames_ids'] ? $data['data']['frames_ids'] : '';

        $string=implode(',',$check_value);
            $sql1 = " SELECT  A.inv_comment FROM oc_order_inv A  WHERE  A.order_id = '" . $order_id . "' ";
            $query1 = $db->query($sql1);
            $result1 = $query1->row;
            $old_inv_comment = $result1['inv_comment'];


        //判断是否提交过周转筐
        if($frame_count > 0){
            //未提交
            $sql = " update oc_order_inv   set  frame_count = '".$frame_count."' , frame_vg_list= '".$frames_ids ."' where  order_id = '".$order_id."' ";
            $query = $db->query($sql);

            //货位号错误
            if ($new_inv_comment != $old_inv_comment ) {

                $sql = "INSERT INTO oc_x_order_check_location ( `order_id`,`old_inv_comment`,`new_inv_comment`,`reasons`,`container_id`,`add_user`,`date_added`)
                VALUES ('" . $order_id . "','" . $old_inv_comment . "','" . $new_inv_comment . "','" . $string . "','" . $frames . "','" . $user_id . "',NOW())";

                $query = $db->query($sql);
                if ($query) {
                    $sql = "UPDATE oc_order_inv SET check_status = 1 ,inv_comment = $new_inv_comment WHERE order_id = '" . $order_id . "' ";

                    $query = $db->query($sql);
                    $return['status'] = 2;
                    $return['check_location'] = $query->rows;
                }

            }

            //货位号相同
            if ($new_inv_comment == $old_inv_comment) {
                $sql = "INSERT INTO oc_x_order_check_location ( `order_id`,`old_inv_comment`,`new_inv_comment`,`reasons`,`container_id`,`add_user`,`date_added`)
                VALUES ('" . $order_id . "','" . $old_inv_comment . "','" . $new_inv_comment . "','" . $string . "','" . $frames . "','" . $user_id . "',NOW())";
                $query = $db->query($sql);
                if ($query) {
                    $sql = "UPDATE oc_order_inv SET check_status = 1 WHERE order_id = '" . $order_id . "' ";
                    $query = $db->query($sql);
                    $return['status'] = 4;
                    $return['check_location'] = $query->rows;

                }
            }


        }else{
            //已经提交
            //货位号错误
            if ($new_inv_comment != $old_inv_comment ) {

                $sql = "INSERT INTO oc_x_order_check_location ( `order_id`,`old_inv_comment`,`new_inv_comment`,`reasons`,`container_id`,`add_user`,`date_added`)
                VALUES ('" . $order_id . "','" . $old_inv_comment . "','" . $new_inv_comment . "','" . $string . "','" . $frames . "','" . $user_id . "',NOW())";

                $query = $db->query($sql);
                if ($query) {
                    $sql = "UPDATE oc_order_inv SET check_status = 1 ,inv_comment = $new_inv_comment WHERE order_id = '" . $order_id . "' ";

                    $query = $db->query($sql);
                    $return['status'] = 2;
                    $return['check_location'] = $query->rows;
                }

            }

            //货位号相同
            if ($new_inv_comment == $old_inv_comment) {
                $sql = "INSERT INTO oc_x_order_check_location ( `order_id`,`old_inv_comment`,`new_inv_comment`,`reasons`,`container_id`,`add_user`,`date_added`)
                VALUES ('" . $order_id . "','" . $old_inv_comment . "','" . $new_inv_comment . "','" . $string . "','" . $frames . "','" . $user_id . "',NOW())";
                $query = $db->query($sql);
                if ($query) {
                    $sql = "UPDATE oc_order_inv SET check_status = 1 WHERE order_id = '" . $order_id . "' ";
                    $query = $db->query($sql);
                    $return['status'] = 4;
                    $return['check_location'] = $query->rows;

                }
            }


        }







        return $return;

    }

    public  function submitcheck(array $data){
        global  $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $inv_comment = $data['data']['inv_comment'] ? $data['data']['inv_comment'] : '';
        $user_id = $data['data']['user_id'] ? $data['data']['user_id'] : '';
        $sql = "SELECT  cm.container_id FROM   oc_x_container_move cm
 WHERE  cm.order_id = '" . $order_id . "' ";
        $query = $db->query($sql);
        $result = $query->rows;
        foreach ($result as $container_id){
            $check_container_id[] = $container_id['container_id'];
        }
        $containerids = implode(',',$check_container_id);
        $reasons = 4;
        $sql1 = "INSERT INTO oc_x_order_check_location ( `order_id`,`old_inv_comment`,`new_inv_comment`,`reasons`,`container_id`,`add_user`,`date_added`)
                VALUES ('" . $order_id . "','" . $inv_comment . "','" . $inv_comment . "','" . $reasons . "','".$containerids."','" . $user_id . "',NOW())";


        $query = $db->query($sql1);

        if ($query) {
            $sql = "UPDATE oc_order_inv SET check_status = 1 WHERE order_id = '" . $order_id . "' ";

            $query = $db->query($sql);
            $return['status'] = 2;
            $return['check_location'] = $query->rows;

        }
        return $return['status'];
    }

    public function getCheckReason(){
        global  $db;
        $sql = "select * from oc_x_order_check_location_reason WHERE status = 1";

        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }


}
$locationverifi = new LOCATIONVERIFI();

?>