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
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';

        $sql = "SELECT A.check_status,D.order_status_id, A.inv_comment, A.order_id , GROUP_CONCAT(DISTINCT(C.inventory_name)) inventory_name ,C.quantity,E.name,CL.reasons,A.frame_count,A.box_count
FROM  oc_order_inv A
LEFT JOIN oc_x_inventory_order_sorting B ON A.order_id = B.order_id
 LEFT JOIN  oc_order_distr C ON  A.order_id = C.order_id
 LEFT JOIN oc_order D ON  A.order_id = D.order_id
 LEFT JOIN  oc_order_status E ON D.order_status_id = E.order_status_id
 LEFT JOIN( SELECT F.order_id ,GROUP_CONCAT(DISTINCT  if(F.reasons>0,LR.reason_name,NULL)) AS reasons FROM oc_x_order_check_location F LEFT JOIN  oc_x_order_check_location_reason LR ON F.reasons = LR.check_location_reason_id where date(F.date_added) between date_sub(current_date(), interval 3 day) and current_date() GROUP BY F.order_id) AS CL ON A.order_id = CL.order_id
 WHERE  1=1 AND D.order_status_id IN (6,8) and D.order_deliver_status_id = 1 AND A.check_status = '".$order_check_status."'";


        if (!empty($order_id)) {
            $sql .= " AND A.order_id = '" . $order_id . "'";
        }
        else{
            if (!empty($date_start)) {
                $sql .= " AND A.uptime >= '" . $date_start . "'";
            }

            if (!empty($date_end)) {
                $sql .= " AND A.uptime <= '" .$date_end. "'";
            }
        }


        if(!empty($order_status_id)){
            $sql .= " AND D.order_status_id = '" . $order_status_id . "'";
        }

        $sql .= " and D.warehouse_id = '".$warehouse_id ."'" ;
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
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';


        $sql = "SELECT COUNT(DISTINCT A.order_id) AS  sum FROM oc_order_inv A LEFT JOIN oc_order B ON A.order_id =B.order_id LEFT JOIN oc_order_distr od ON   od.order_id = A.order_id WHERE B.order_status_id IN (6,8) and B.order_deliver_status_id = 1  AND A.check_status = 0  ";
        if (!empty($date_start)) {
            $sql .= " AND A.uptime >= '" . $date_start . "'";
        }
        if (!empty($date_end)) {
            $sql .= " AND A.uptime <= '" .$date_end. "'";
        }
        $sql .= " AND B.warehouse_id = '".$warehouse_id."' ";
        $query = $db->query($sql);
        $result = $query->row;

        $sql1 = "SELECT COUNT(DISTINCT A.order_id) AS sum1  FROM oc_order_inv A  LEFT JOIN oc_order B ON A.order_id =B.order_id LEFT JOIN oc_order_distr od ON   od.order_id = A.order_id WHERE B.order_status_id IN (6,8) and B.order_deliver_status_id = 1 AND  A.check_status = 1   ";
        if (!empty($date_start)) {
            $sql1 .= " AND A.uptime >= '" . $date_start . "'";
        }
        if (!empty($date_end)) {
            $sql1 .= " AND A.uptime <= '" .$date_end. "'";
        }
        $sql1 .= " AND B.warehouse_id = '".$warehouse_id."' ";
        $query1 = $db->query($sql1);
        $result1 = $query1->row;

        $return = array($result,$result1);
        return $return;

    }


    public function getCheckOrdersInfo(array $data){
        global $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : 0;

        $sql = "SELECT A.check_status,D.order_status_id, A.inv_comment, A.order_id ,C.inventory_name,C.inventory_name,SUM(C.quantity) AS quantity ,E.name FROM  oc_order_inv A LEFT JOIN oc_x_inventory_order_sorting B ON A.order_id = B.order_id LEFT JOIN  oc_order_distr C ON  A.order_id = C.order_id LEFT JOIN oc_order D ON  A.order_id = D.order_id LEFT JOIN  oc_order_status E ON D.order_status_id = E.order_status_id WHERE  A.order_id = '".$order_id."' GROUP BY A.order_id ";

        if($warehouse_id){
            $sql = "
            SELECT A.check_status, D.order_deliver_status_id, D.order_status_id, A.inv_comment, A.order_id ,C.inventory_name,C.inventory_name,SUM(C.quantity) AS quantity ,E.name
            FROM oc_order_inv A
            LEFT JOIN oc_x_inventory_order_sorting B ON A.order_id = B.order_id
            LEFT JOIN oc_order_distr C ON  A.order_id = C.order_id
            LEFT JOIN oc_order D ON  A.order_id = D.order_id
            LEFT JOIN oc_order_status E ON D.order_status_id = E.order_status_id
            WHERE A.order_id = '".$order_id."' AND D.warehouse_id = '".$warehouse_id."' AND D.order_deliver_status_id = 1
            GROUP BY A.order_id ";
        }

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
//            $sql = " update oc_order_inv   set  frame_count = '".$frame_count."' , frame_vg_list= '".$frames_ids ."' where  order_id = '".$order_id."' ";
//            $query = $db->query($sql);

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



    /*
     * zx
     * 为班组长分配任务
     * 开始
     * */
    function getDeliverOrderToCheck(array $data){
        global  $db ;
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : false;
        $added_by = $data['data']['added_by']? $data['data']['added_by'] : false;
        $order_type = $data['data']['order_type']? $data['data']['order_type'] : false;
        $get_order_id = $data['data']['get_order_id']? intval($data['data']['get_order_id']) : 0;
        $check_location_id = $data['data']['check_location_id']? $data['data']['check_location_id'] : false;
        $user_group_id = $data['data']['user_group_id']? $data['data']['user_group_id'] : '';
        $today = date("Y-m-d",time());
        $five_days_ago = date("Y-m-d",strtotime("-12 Days"));
        $today = $today;
        $five_days_ago = $five_days_ago;
        //预计总检查量
        $plan_num = 30;
        //预计so核查量
        $so_plan_num = 30;
        $do_plan_num = $plan_num - $so_plan_num;
        //预计do散件单核查量
        $do_is_repack = 15;
        $do_not_repack = $do_plan_num - $do_is_repack;
        $so_num = 0;
        $do_num = 0;
        $result = $this->check_in_right_group($user_group_id);
        if ($result['result']) {
            $return = $result['return'];
            return $return;
        }
        if (!$added_by) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '账户过期，请重新登录',
                'return_data' => ''
            );
            return $return;
        }
        if (!$warehouse_id) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '账户过期，请重新登录',
                'return_data' => ''
            );
            return $return;
        }
        //@order_type   1：正常核查生成判断；3：查看已核查数据；
        if ($order_type == 1) {
            if ($get_order_id == 0) {
                //查询核查表的全部信息
                $sql = "SELECT COUNT(ocl.check_location_id) AS num1,
COUNT(IF(ocl.status = 1,ocl.check_location_id,NULL)) AS num2,
COUNT(IF(ocl.deliver_order_id = 0,ocl.check_location_id,NULL)) AS so_num,
COUNT(IF(ocl.order_id = 0,ocl.check_location_id,NULL)) AS do_num,
COUNT(IF(do.is_repack = 1,ocl.check_location_id,NULL)) AS do_repack,
COUNT(IF(do.is_repack = 0,ocl.check_location_id,NULL)) AS not_repack,
GROUP_CONCAT(IF(ocl.status = 0,ocl.check_location_id,NULL) ORDER BY ocl.order_id,do.is_repack DESC,ocl.date_added) AS check_location_id,
GROUP_CONCAT(ocl.check_location_id,'@',ocl.deliver_order_id,'@',ocl.order_id ORDER BY ocl.order_id,do.is_repack DESC,ocl.date_added) AS orders
FROM oc_x_deliver_order_check_location ocl 
LEFT JOIN oc_x_deliver_order do ON do.deliver_order_id = ocl.deliver_order_id AND do.deliver_order_id != 0
WHERE ocl.add_user = '" . $added_by . "' 
AND DATE_FORMAT(ocl.date_added,'%Y-%m-%d') = '" . $today . "'
AND ocl.valid_status = 1 ";
//            return $sql;

                $query = $db->query($sql);
                $results = $query->row;
                $check_result = 0;//定义一个判断的变量，为1时就可直接进入查询单号生成核查任务操作

//        return $results;
                //如果无数据，就可直接进入查询单号生成核查任务操作，有数据需要进行相关判断
                if ($results) {
                    $so_num = intval($results['so_num']);
                    $do_num = intval($results['do_num']);
                    $do_repack = intval($results['do_repack']);
                    $not_repack = intval($results['not_repack']);
                    // 判断核查数量是否与计划值相等，相等判断是否有未核查完的，若没有且未传入预定的order_id就结束，若有未核查的优先进入未核查的order_id，否则进入预定的order_id；不相等判断是否有未核查完的，若没有直接进入查询单号生成核查任务操作
                    if (intval($results['num1']) == $plan_num) {
                        //相等判断是否有未核查完的，若没有且未传入预定的order_id就结束，若有未核查的优先进入未核查的order_id，否则进入预定的order_id
                        if (intval($results['num2']) == intval($results['num1']) && $get_order_id == 0) {
                            $return = array(
                                'return_code' => 'ERROR',
                                'return_msg' => '今日已全部核查',
                                'return_data' => ''
                            );
                            return $return;
                        // 若有未核查的优先进入未核查的order_id
                        } else {
                            if (intval($results['num2']) < intval($results['num1'])) {
                                $check_result = 2;
                                $check_location_id = explode(',', $results['check_location_id'])[0];
                                foreach (explode(',', $results['orders']) as $order) {
                                    $order = explode('@', $order);
                                    if ($order[0] == $check_location_id) {
                                        if (intval($order[1]) == 0) {
                                            $type = 0;
                                            $order_id = $order[2];
                                        } else {
                                            $type = 1;
                                            $order_id = $order[1];
                                        }
                                    }
                                }
                            //否则进入预定的order_id
                            } else {
                                $check_result = 1;
                            }
                        }
                    //不相等判断是否有未核查完的，若有未核查的优先进入未核查的order_id若没有直接进入查询单号生成核查任务操作
                    } else {
                        //不相等判断是否有未核查完的，若有未核查的优先进入未核查的order_id
                        if (intval($results['num2']) < intval($results['num1'])) {
                            $check_result = 2;
                            $check_location_id = explode(',', $results['check_location_id'])[0];
                            foreach (explode(',', $results['orders']) as $order) {
                                $order = explode('@', $order);
                                if ($order[0] == $check_location_id) {
                                    if (intval($order[1]) == 0) {
                                        $type = 0;
                                        $order_id = $order[2];
                                    } else {
                                        $type = 1;
                                        $order_id = $order[1];
                                    }
                                }
                            }
                        //若没有直接进入查询单号生成核查任务操作
                        } else {
                            $check_result = 1;
                        }
                    }
                } else {
                    $check_result = 1;
                }
            } else {
                $sql = "SELECT o.order_id,ocl.status,ocl.add_user FROM oc_order o LEFT JOIN oc_x_deliver_order_check_location ocl ON ocl.order_id = o.order_id AND ocl.valid_status = 1  WHERE o.order_id = '".$get_order_id."' AND o.warehouse_id = '".$warehouse_id."' AND o.order_status_id = 6 AND o.order_deliver_status_id = 1 ";
                $search_result = $db->query($sql)->row;
//                return $search_result;
                if (empty($search_result)) {
                    $return = array(
                        'return_code' => 'ERROR',
                        'return_msg'  => '所核查订单号非本仓合单且未出库的订单，请核实后再核查',
                        'return_data' => ''
                    );
                    return $return;
                } else if (!isset($search_result['status'])) {
                    $check_result = 1;
                } else if (intval($search_result['add_user']) != intval($added_by)) {
                    $return = array(
                        'return_code' => 'ERROR',
                        'return_msg'  => '该订单已被其他人员核查',
                        'return_data' => ''
                    );
                    return $return;
                } else {
                    $check_result = 2;
                    $order_id = $get_order_id;
                }

            }
            //return $check_result;
            //查询单号生成核查任务操作
            if ($check_result == 1) {
                $type = 2;//定义单号类型变量，为1查询do单号，为0时查询so单号
                $so_insert = empty($get_order_id) ? 0 : 1 ;//定义生成判断变量，1表示有预定义订单号就按预定义进行；0为按正常查询生产操作，首先判断do单数量是否符合，在进行是否so单查询
                if ($so_insert == 0) {

                    //看是do单未核查够，如果不够，查可用do单进行分配，如果够了，查so单进行分配
                    if (($do_plan_num - $do_num) > 0) {
                        //看散件是否够数，不够继续，够了就开始整件数
                        if ($do_is_repack > $do_repack) {
                            /*
                             * 查询五天内的未合单的散件do单
                             * */
                            $sql1 = "SELECT odo.deliver_order_id,doi.inv_comment 
FROM oc_x_deliver_order odo
LEFT JOIN oc_x_deliver_order_inv doi ON doi.deliver_order_id = odo.deliver_order_id
LEFT JOIN oc_order o ON o.order_id = odo.order_id
WHERE odo.order_status_id IN (6,8)
AND odo.is_repack = 1
AND o.order_status_id = 5
AND odo.do_warehouse_id = " . $warehouse_id . "
AND (SELECT COUNT(1) AS num FROM oc_x_deliver_order_check_location ocl WHERE ocl.deliver_order_id = odo.deliver_order_id AND ocl.valid_status = 1) = 0
AND DATE(o.deliver_date) BETWEEN '" . $five_days_ago . "' AND '" . $today . "'";
                            $query1 = $db->query($sql1);
                            $results1 = $query1->row;
                            if (empty($results1)) {
                                /*
                                  * 查询五天内的未合单的整件do单
                                  * */
                                $sql1 = "SELECT odo.deliver_order_id,doi.inv_comment 
FROM oc_x_deliver_order odo
LEFT JOIN oc_x_deliver_order_inv doi ON doi.deliver_order_id = odo.deliver_order_id
LEFT JOIN oc_order o ON o.order_id = odo.order_id
WHERE odo.order_status_id IN (6,8)
AND o.order_status_id = 5
AND odo.is_repack = 0
AND odo.do_warehouse_id = " . $warehouse_id . "
AND (SELECT COUNT(1) AS num FROM oc_x_deliver_order_check_location ocl WHERE ocl.deliver_order_id = odo.deliver_order_id AND ocl.valid_status = 1) = 0
AND DATE(o.deliver_date) BETWEEN '" . $five_days_ago . "' AND '" . $today . "'";
//                            return $sql1;

                                $query1 = $db->query($sql1);
                                $results1 = $query1->row;
                            }
//        return $sql1;
                        } else if ($do_not_repack > $not_repack) {
                            /*
                          * 查询五天内的未合单的整件do单
                          * */
                            $sql1 = "SELECT odo.deliver_order_id,doi.inv_comment 
FROM oc_x_deliver_order odo
LEFT JOIN oc_x_deliver_order_inv doi ON doi.deliver_order_id = odo.deliver_order_id
LEFT JOIN oc_order o ON o.order_id = odo.order_id
WHERE odo.order_status_id IN (6,8)
AND o.order_status_id = 5
AND odo.is_repack = 0
AND odo.do_warehouse_id = " . $warehouse_id . "
AND (SELECT COUNT(1) AS num FROM oc_x_deliver_order_check_location ocl WHERE ocl.deliver_order_id = odo.deliver_order_id AND ocl.valid_status = 1) = 0
AND DATE(o.deliver_date) BETWEEN '" . $five_days_ago . "' AND '" . $today . "'";
                            $query1 = $db->query($sql1);
                            $results1 = $query1->row;
                        }

//        return $sql1;

                        if ($results1) {
                            /*
                             *分配未合单do单
                             * */
                            $sql1 = "INSERT INTO oc_x_deliver_order_check_location (`deliver_order_id`,`date_added`,`add_user`,`order_id`,`old_inv_comment`) VALUES";
                            $sql1 .= "('" . $results1['deliver_order_id'] . "',NOW(),'" . $added_by . "',0,'" . $results1['inv_comment'] . "')";
                            $db->query($sql1);
                            $check_location_id = $db->getLastId();
                            $type = 1;
                            $order_id = $results1['deliver_order_id'];
                            //暂时查不到可分配do单，检查so单
                        } else {
                            $so_insert = 1;
                        }
                    } else {
                        $so_insert = 1;
                    }
                }
                if ($so_insert > 0) {
                    // 检查so单是否检查够，如果不够，查可用so单进行分配
                    if (($so_plan_num - $so_num)> 0 || $get_order_id > 0) {
                        /*
                          * 查询五天内的合单的SO单
                          * */
                        $sql2 = "SELECT o.order_id,oi.inv_comment
FROM oc_order o 
LEFT JOIN oc_order_inv oi ON oi.order_id = o.order_id
WHERE 1=1 ";
                        if ($get_order_id > 0) {
                            $sql2 .= " AND o.order_id = '" . $get_order_id . "'";
                        } else {
                            $sql2 .= "
    AND o.order_status_id = 6 
    AND o.order_deliver_status_id = 1  
    AND o.warehouse_id = " . $warehouse_id . " 
    AND (SELECT COUNT(1) AS NUM FROM oc_x_deliver_order_check_location ocl WHERE ocl.order_id = o.order_id AND ocl.valid_status = 1) = 0
    AND DATE(o.deliver_date) BETWEEN '" . $five_days_ago . "' AND '" . $today . "'";
                        }
//        return $sql2;
                        $query2 = $db->query($sql2);
                        $results2 = $query2->row;
                        if ($results2) {
                            /*
                             *zx
                             *分配合单so单
                             *
                             * */
                            $sql2 = "INSERT INTO oc_x_deliver_order_check_location (`order_id`,`date_added`,`add_user`,`deliver_order_id`,`old_inv_comment`) VALUES";
                            $sql2 .= "('" . $results2['order_id'] . "',NOW(),'" . $added_by . "',0,'".$results2['inv_comment']."')";
                            $db->query($sql2);
                            $check_location_id = $db->getLastId();
                            $type = 0;
                            $order_id = $results2['order_id'];
                            //暂时查不到可分配so单，暂时无单可查
                        } else {
                            $return = array(
                                'return_code' => 'ERROR',
                                'return_msg' => '暂时无合适订单可查，请稍后再试',
                                'return_data' => ''
                            );
                            return $return;
                        }
                    }
                }

                /*
                 *zx
                 *查询分配单信息插入明细表
                 * */
                $sql  =  "SELECT oop.product_id,oop.quantity AS plan_quantity,SUM(oios.quantity) AS sorting_quantity,oios.container_id,oios.added_by,0 AS product_num,".$check_location_id." AS check_location_id ";
                if ($type  == 1) {
                    $sql .= "
FROM oc_x_deliver_order_product oop
LEFT JOIN oc_x_inventory_order_sorting oios ON oios.product_id = oop.product_id AND oop.deliver_order_id = oios.deliver_order_id";
                } else if ($type == 0) {
                    $sql .= "
FROM oc_order_product oop
LEFT JOIN oc_x_inventory_order_sorting oios ON oios.product_id = oop.product_id AND oop.order_id = oios.order_id";
                }
                $sql .= "
WHERE ";
                if ($type  == 1) {
                    $sql .= " oios.deliver_order_id = '".$order_id."'";
                } else if ($type == 0) {
                    $sql .= " oios.order_id = '".$order_id."'";
                }
                $sql .= " AND oios.status = 1 
GROUP BY product_id,container_id";
//            return $sql;
                $query = $db->query($sql);
                $results = $query->rows;
//                return $results;
                if ($results) {
                    $this->insert_check_location_details($results);
                }
            }
            $sql  =  "SELECT ooi.inv_comment,ocl.status,ocl.check_location_id,oios.deliver_order_id,";
            if ($type == 0) {
                $sql .= "ooi.order_id,";
            } else if ($type == 1) {
                $sql .= "ooi.deliver_order_id AS order_id,";
            } else {
                $return = array(
                    'return_code' => 'ERROR',
                    'return_msg'  => '暂时无合适订单可查，请稍后再试',
                    'return_data' => ''
                );
                return $return;
            }
            $sql .= "
GROUP_CONCAT(DISTINCT IF (
	oios.container_id != 0,
	oios.container_id,
    NULL
)) AS container_id,
GROUP_CONCAT(DISTINCT oios.added_by) AS added_by,
SUM(IF (
    oios.container_id = 0,
    oios.quantity,
    NULL
))AS product_num,
COUNT(DISTINCT IF (
	oios.container_id != 0,
	oios.container_id,
    NULL
)) AS container_num ,".$type." AS type ";
            if ($type == 0) {
                $sql .= " FROM oc_order_inv ooi 
LEFT JOIN oc_x_inventory_order_sorting oios ON oios.order_id = ooi.order_id 
LEFT JOIN oc_x_deliver_order_check_location ocl ON ocl.order_id = ooi.order_id AND ocl.valid_status = 1
WHERE ooi.order_id = '".$order_id."' 
AND oios.status = 1 
AND ocl.valid_status = 1
GROUP BY ooi.order_id";
            } else if ($type == 1) {
                $sql .= " FROM oc_x_deliver_order_inv ooi 
LEFT JOIN oc_x_inventory_order_sorting oios ON oios.deliver_order_id = ooi.deliver_order_id 
LEFT JOIN oc_x_deliver_order_check_location ocl ON ocl.deliver_order_id = ooi.deliver_order_id AND ocl.valid_status = 1
WHERE ooi.deliver_order_id = '".$order_id."'  
AND oios.status = 1 
AND ocl.valid_status = 1
GROUP BY ooi.deliver_order_id";
            }
        }
        //查询已核查的数据
        if ($order_type == 3) {

            $sql  = "SELECT ocl.new_inv_comment,ocl.old_inv_comment AS inv_comment,ocl.check_location_id,ocl.deliver_order_id,ocl.order_id,ocl.status,";
            $sql .= "
GROUP_CONCAT(DISTINCT IF (
	ocd.container_id != 0,
	ocd.container_id,
    NULL
)) AS container_id,
GROUP_CONCAT(DISTINCT ocl.add_user) AS added_by,
SUM(IF (
    ocd.container_id = 0,
    ocd.plan_quantity,
    NULL
))AS product_num,
COUNT(DISTINCT IF (
	ocd.container_id != 0,
	ocd.container_id,
    NULL
)) AS container_num ,
IF(ocl.order_id = 0,1,0) AS type ";
            $sql .= " FROM oc_x_deliver_order_check_location ocl
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id
WHERE ocl.check_location_id = '".$check_location_id."'
AND ocd.status = 1
AND ocl.valid_status = 1
GROUP BY ocl.check_location_id";

        }

//        return $sql;
        $query = $db->query($sql);
        $results = $query->row;
//return $type;
        if ($results) {
            $return = array(
                'return_code' => 'SUCCESS',
                'return_msg'  => '成功',
                'return_data' => $results
            );
        } else {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '该单子分拣数据丢失',
                'return_data' => $order_id
            );
        }
        return $return;

    }
    /*zx
   根据商品条码获取商品信息*/
    public function getProductsInformation( array $data){
        global  $db;
        $product_id = isset($data['data']['product_id']) ? $data['data']['product_id'] : false;
        $order_type = isset($data['data']['order_type']) ? $data['data']['order_type'] : false;
        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id'] : false;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;

        $sql = "SELECT ptw.product_id,p.name,ptw.stock_area,ptw.sku_barcode,oop.quantity AS plan_quantity
FROM  oc_product_to_warehouse ptw
LEFT JOIN  oc_product p ON ptw.product_id  = p.product_id";
        if ($order_type == 0) {
            $sql .= " LEFT JOIN  oc_order_product oop ON ptw.product_id  = oop.product_id AND oop.order_id = ".$order_id;
        } else {
            $sql .= " LEFT JOIN  oc_x_deliver_order_product oop ON ptw.product_id  = oop.product_id AND oop.deliver_order_id = ".$order_id;
        }
        $sql .= "
WHERE ptw.warehouse_id = ".$warehouse_id." AND ";
        if (strlen($product_id) > 6) {
            $sql .= " ptw.sku_barcode = '".$product_id."'";
        } else {
            $sql .= " ptw.product_id = '".$product_id."'";
        }
        $sql .= " GROUP BY ptw.product_id";
//return $sql;
        $query = $db->query($sql);
        $results = $query->row;
        return $results;
    }
    /*zx
    扫描商品插入中间表*/
    public function submitCheckProductInformation( array $data){
        global  $db;
        $product_id = isset($data['data']['product_id']) ?trim($data['data']['product_id']) : false;
        $warehouse_id = isset($data['data']['warehouse_id']) ? trim($data['data']['warehouse_id']) : false;
        $check_location_id = isset($data['data']['check_location_id']) ? trim($data['data']['check_location_id']) : false;
        $post_data = isset($data['data']['post_data']) ? trim($data['data']['post_data']) : false;
        $order_type = isset($data['data']['order_type']) ? trim($data['data']['order_type']) : false;
        $container_id = isset($data['data']['container_id']) ? trim($data['data']['container_id']) : false;
        $plan_quantity = isset($data['data']['plan_quantity']) ? trim($data['data']['plan_quantity']) : false;
        $sorting_quantity = isset($data['data']['sorting_quantity']) ? trim($data['data']['sorting_quantity']) : false;
        $product_num = isset($data['data']['product_num']) ? trim($data['data']['product_num']) : false;
        $added_by = isset($data['data']['added_by']) ? trim($data['data']['added_by']) : false;
        $user_group_id = isset($data['data']['user_group_id']) ? trim($data['data']['user_group_id']) : false;
        $result = $this->check_in_right_group($user_group_id);
        if ($result['result']) {
            $return = $result['return'];
            return $return;
        }
        $sql = "SELECT ocl.status,ocl.order_id,ocd.final_quantity AS quantity
FROM oc_x_deliver_order_check_location ocl 
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id AND ocd.status = 1
WHERE ocl.add_user = '".$added_by."'
AND ocl.valid_status = 1 
AND ocl.deliver_order_id = 0 AND ";
        $sql .= " ocl.check_location_id = '".$check_location_id."'";
        $sql .=  " AND ocd.container_id = '".$container_id."' AND ocd.product_id = '".$product_id."'";
//        return $sql;
        $query = $db->query($sql);
        $results = $query->row;
        if ($results) {
            if ($results['status'] == 0 && intval($results['quantity']) != 0) {
                $this->update_check_location_details($check_location_id,$container_id,$product_id,$product_num,0);
            } else if ($results['status'] == 0 && intval($results['quantity']) == 0) {
                $this->update_check_location_details($check_location_id,$container_id,$product_id,$product_num,$product_num);
            } else {
                $return = array(
                    'return_code' => 'ERROR',
                    'return_msg'  => '该订单已被核查，请刷新后重试',
                    'return_data' => ''
                );
            }
        } else {
            $array[] = ['check_location_id'=>$check_location_id,'container_id'=>$container_id,'plan_quantity'=>$plan_quantity,'product_num'=>$product_num,'product_id'=>$product_id];
            $this->insert_check_location_details($array);
//            $sql = "INSERT INTO oc_x_deliver_order_check_details
//(`check_location_id`,`container_id`,`plan_quantity`,`sorting_quantity`,`quantity`,`final_quantity`,`product_id`,`date_added`)
//VALUES
//('".$check_location_id."','".$container_id."','".$plan_quantity."','".$sorting_quantity."','".$product_num."','".$product_num."','".$product_id."',NOW())";
////            return $sql;
//            $db->query($sql);

        }
        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg'  => '成功',
            'return_data' => ''
        );
        return $return;

    }
    //明细表插入
    public function insert_check_location_details($array) {
        global  $db;
        if (is_array($array)) {
            $sql = "INSERT INTO oc_x_deliver_order_check_details 
(`check_location_id`,`container_id`,`plan_quantity`,`sorting_quantity`,`quantity`,`final_quantity`,`product_id`,`date_added`) 
VALUES";
            foreach ($array as $value) {
                $sql .= "('".$value['check_location_id']."','".$value['container_id']."','".$value['plan_quantity']."','".$value['sorting_quantity']."','".$value['product_num']."','".$value['product_num']."','".$value['product_id']."',NOW()),";
//            return $sql;
            }
            $sql = rtrim($sql,',');
            $db->query($sql);
        }
    }
    //明细表修改
    public function update_check_location_details($check_location_id,$container_id,$product_id,$product_num,$quantity) {
        global  $db;
        $sql = "UPDATE oc_x_deliver_order_check_details SET `final_quantity` = '".$product_num."'";
        if (intval($quantity) > 0) {
            $sql .= ",`quantity` = '".$quantity."'";
        }
        $sql .= "
WHERE check_location_id = '".$check_location_id."' 
AND container_id = '".$container_id."' 
AND product_id = '".$product_id."'";
//                return $sql;
        $db->query($sql);
    }
    /*zx
    核查任务，整件或周转筐数据更新*/
    public function submitCheckProductResult( array $data){
        global  $db;
        $check_location_id = isset($data['data']['check_location_id']) ? trim($data['data']['check_location_id']) : false;
        $order_type = isset($data['data']['order_type']) ? trim($data['data']['order_type']) : false;
        $product_type = isset($data['data']['product_type']) ? trim($data['data']['product_type']) : false;
        $added_by = isset($data['data']['added_by']) ? trim($data['data']['added_by']) : false;
        $user_group_id = isset($data['data']['user_group_id']) ? trim($data['data']['user_group_id']) : false;
        $so_type = 0;
        $do_type = 1;
        $products_type = 1;
        $containers_type = 2;
        if ($order_type != $so_type && $order_type != $do_type) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '订单类型错误，请刷新后重试',
                'return_data' => ''
            );
            return $return;
        }
        if ($product_type != $products_type && $product_type != $containers_type) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '商品类型错误，请刷新后重试',
                'return_data' => ''
            );
            return $return;
        }
        $result = $this->check_in_right_group($user_group_id);
        if ($result['result']) {
            $return = $result['return'];
            return $return;
        }
        $array_update = [];
        $array_insert = [];
        $sql = "SELECT ocd.product_id,
GROUP_CONCAT(ocd.container_id) AS container_id,ocd.plan_quantity,
GROUP_CONCAT(ocd.sorting_quantity) AS sorting_quantitys,
GROUP_CONCAT(ocd.final_quantity) AS quantitys,
SUM(ocd.sorting_quantity) AS sorting_quantity,
SUM(ocd.final_quantity) AS quantity
FROM oc_x_deliver_order_check_location ocl 
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id AND ocd.status = 1 ";
        if ($product_type == $products_type) {
            $sql .= " AND ocd.container_id = 0 ";
        } else if ($product_type == $containers_type) {
            $sql .= " AND ocd.container_id > 0 ";
        }
        $sql .= "
WHERE ocl.add_user = '".$added_by."'
AND ocl.check_location_id = '".$check_location_id."'
AND ocl.valid_status = 1";
        $sql .= " GROUP BY ocd.product_id HAVING sorting_quantitys != quantitys";
//        return $sql;
        $query = $db->query($sql);
        $results = $query->rows;
//        return $results;
        $sql = "SELECT oios.order_id,oios.deliver_order_id,oios.added_by,oios.warehouse_repack,oios.user_repack,oios.move_flag,
COUNT(oios.inventory_sorting_id) AS count1,GROUP_CONCAT(IF(oios.product_barcode != '',oios.inventory_sorting_id,NULL)) AS count2s
FROM oc_x_deliver_order_check_location ocl ";
        if ($order_type == $so_type) {
            $sql .= "
LEFT JOIN oc_x_inventory_order_sorting oios ON oios.order_id = ocl.order_id ";
        } else {
            $sql .= "
LEFT JOIN oc_x_inventory_order_sorting oios ON oios.deliver_order_id = ocl.deliver_order_id ";
        }
        if ($product_type == $products_type) {
            $sql .= " AND oios.container_id = 0 ";
        } else if ($product_type == $containers_type) {
            $sql .= " AND oios.container_id > 0 ";
        }
        $sql .= "
WHERE ocl.add_user = '".$added_by."'
AND ocl.check_location_id = '".$check_location_id."'
AND ocl.valid_status = 1 ";
        $query = $db->query($sql);
        $results2 = $query->row;
//        return $results2;
        $count2 = count(explode(',',$results2['count2s']));
        $count1 = intval($results2['count1']);
//        return $count1;
        if ($count2 != $count1) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '已经更新过数据，请不要重复更新',
                'return_data' => '1'
            );
            return $return;
        }
//        return $results2;
        if (empty($results)) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '商品核查数据与分拣数据相同，无需更新',
                'return_data' => '1'
            );
            return $return;
        }
        /*
         * 商品对比
         * */
        if ($product_type == 1) {
            foreach ($results as $value) {
                //分拣大于核查,直接修改数量
                if ($value['sorting_quantity'] > $value['quantity']) {
                    $array_update[$value['product_id'].'@'.$value['container_id']] =
                        "UPDATE oc_x_inventory_order_sorting SET `quantity` = '".$value['quantity']."'
WHERE container_id = 0 
AND status = 1 
AND product_id = '".$value['product_id']."' 
AND order_id = '".$results2['order_id']."'
AND deliver_order_id = '".$results2['deliver_order_id']."'";
                    //分拣小于核查，核查大于等于订单按订单量插入差值，核查小于订单按核查量插入差值
                } else if ($value['sorting_quantity'] < $value['quantity']) {
                    if ($value['plan_quantity'] > $value['quantity']) {
                        $quantitys = intval($value['quantity']) - intval($value['sorting_quantity']);
                    } else {
                        $quantitys = intval($value['plan_quantity']) - intval($value['sorting_quantity']);
                    }
                    $array_insert[$value['product_id'].'@'.$value['container_id']] =
                        "('".$results2['order_id']."','".$results2['deliver_order_id']."','0','".$added_by."','".$results2['user_repack']."','".$results2['warehouse_repack']."','".$quantitys."','".$value['product_id']."','".$results2['move_flag']."',NOW()),";
                }
            }
            /*
             * 周转筐商品对比
             * */
        } else {
            foreach ($results as $value) {
                $product_quantity = intval($value['plan_quantity']);
                $product_quantitys = 0;
                $sorting_quantitys = explode(',',$value['sorting_quantitys']);
                $container_id = explode(',',$value['container_id']);
                $quantitys = explode(',',$value['quantitys']);
                foreach ($sorting_quantitys as $key2 => $value2) {
                    $product_quantitys += intval($quantitys[$key2]);
                    if (intval($value2) != intval($quantitys[$key2])) {
                        if (intval($value2) != 0) {
                            $array_update[$value['product_id'].'@'.$container_id[$key2]] =
                                "UPDATE oc_x_inventory_order_sorting SET `status` = 0
WHERE container_id = ".$container_id[$key2]." 
AND status = 1 
AND product_id = '".$value['product_id']."' 
AND order_id = '".$results2['order_id']."'
AND deliver_order_id = '".$results2['deliver_order_id']."'";
                        }
                        $array_insert[$value['product_id'].'@'.$container_id[$key2]] =
                            "('".$results2['order_id']."','".$results2['deliver_order_id']."',".$container_id[$key2].",'".$added_by."','".$results2['user_repack']."','".$results2['warehouse_repack']."','".intval($quantitys[$key2])."','".$value['product_id']."','".$results2['move_flag']."',NOW()),";
                    }
                }
                if ($product_quantitys > $product_quantity) {
                    $return = array(
                        'return_code' => 'ERROR',
                        'return_msg'  => '核查数量不能大于订单数量，请更正后在提交',
                        'return_data' => intval($value['product_id'])
                    );
                    return $return;
                }
            }
        }
        if (count($array_update) > 0) {
            foreach ($array_update as $value) {
                $sql_update = $value;
                $db->query($sql_update);
            }
        }
//        return $array_insert;
        if (count($array_insert) > 0) {
            $sql_insert = "INSERT INTO oc_x_inventory_order_sorting
            (`order_id`,`deliver_order_id`,`container_id`,`added_by`,`user_repack`,`warehouse_repack`,`quantity`,`product_id`,`move_flag`,`uptime`) VALUES";
            foreach ($array_insert as $value) {
                $sql_insert .= $value;
            }
            $sql_insert = rtrim($sql_insert,',');
//            return $sql_insert;
            $db->query($sql_insert);
        }
//        return $array_update;


        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg'  => '该订单商品正确',
            'return_data' => ''
        );


        return $return;

    }
    /*zx
    订单核查提交*/
    public function submitCheckOrderResult( array $data){
        global  $db;
        $check_location_id = isset($data['data']['check_location_id']) ? trim($data['data']['check_location_id']) : false;
        $added_by = isset($data['data']['added_by']) ? trim($data['data']['added_by']) : false;
        $comment = isset($data['data']['comment']) ? trim($data['data']['comment']) : '';
        $submit_type = isset($data['data']['submit_type']) ? intval($data['data']['submit_type']) : 0;
        $new_inv_comment = isset($data['data']['new_inv_comment']) ? trim($data['data']['new_inv_comment']) : '';
        $user_group_id = isset($data['data']['user_group_id']) ? trim($data['data']['user_group_id']) : false;
        $order_type = isset($data['data']['order_type']) ? trim($data['data']['order_type']) : false;
        $order_id = isset($data['data']['order_id']) ? trim($data['data']['order_id']) : false;
        $sql_update = [];
        $sql_delete = '';
        $sql_insert = '';

        $result = $this->check_in_right_group($user_group_id);
        if ($result['result']) {
            $return = $result['return'];
            return $return;
        }
        $sql = "SELECT ocl.order_id,ocl.deliver_order_id,
ocd.product_id,ocl.status,
SUM(ocd.final_quantity) AS quantity,
SUM(ocd.sorting_quantity) AS sorting_quantity,
ocd.plan_quantity AS plan_quantity
FROM oc_x_deliver_order_check_location ocl
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id
WHERE ocd.status = 1 ";
        if ($order_type == 0) {
            $sql .= "
AND ocl.check_location_id = ".$check_location_id;
        } else {
            $sql .= "
AND ocl.check_location_id = ".$check_location_id;
        }
        $sql .= " AND ocl.valid_status = 1 GROUP BY ocd.product_id HAVING quantity != sorting_quantity ";
//        return $sql;
        $query = $db->query($sql);
        $results1 = $query->rows;
        if (intval($results1[0]['status']) == 1) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '该订单已经核查过',
                'return_data' => ''
            );
            return $return;
        }
        if ($submit_type != 1) {
            $sql = "SELECT iorq.product_id,iorq.order_id,iorq.deliver_order_id,iorq.move_flag
FROM oc_x_inventory_order_sorting iorq
WHERE iorq.status = 1 ";
        if ($order_type == 0) {
            $sql .= "
AND iorq.order_id = ".$order_id;
        } else {
            $sql .= "
AND iorq.deliver_order_id = " . $order_id;
        }
        $sql .= " GROUP BY iorq.product_id";
//        return $sql;

        $query = $db->query($sql);
        $value2 = $query->row;
        if ($value2) {
            $deliver_order_id = $value2['deliver_order_id'];
            $order_id = $value2['order_id'];
            $move_flag = $value2['move_flag'];
        }

        if ($results1) {
            foreach ($results1 as $key1 => $value1) {
                $quantity = intval($value1['quantity']);
                $sorting_quantity = intval($value1['sorting_quantity']);
                $plan_quantity = intval($value1['plan_quantity']);
                //核查商品数为零
                if ($quantity > $plan_quantity) {
                    $return = array(
                        'return_code' => 'ERROR',
                        'return_msg'  => '核查数量不可大于订单数量',
                        'return_data' => ''
                    );
                    return $return;
                }
                if ($quantity == 0) {
                    if ($sorting_quantity != 0) {
                        $sql_delete .= $value1['product_id'].',';
                    }
                    //核查数不为零
                } else {
                    if ($sorting_quantity != 0) {
                        if ($sorting_quantity != $quantity) {
                            $sql_update[$value1['product_id']] =
                                "UPDATE oc_x_inventory_order_return_quantity SET `sorting_quantity` = '".$quantity."',
`return_quantity` = '".($plan_quantity-$quantity)."'
WHERE product_id = '".$value1['product_id']."'
AND order_id = '".$order_id."'
AND deliver_order_id = '".$deliver_order_id."'
AND status = 1";
                        }
                    } else {
                        $sql_insert .= "('".$order_id."','".$deliver_order_id."','".$value1['product_id']."','".intval($value1['plan_quantity'])."','".intval($value1['quantity'])."','".(intval($value1['plan_quantity'])-intval($value1['quantity']))."',NOW(), '".$move_flag."','".$added_by."','1'),";
                    }
                }
            }
            $sql = "SELECT ocd.product_id,ocl.order_id,ocl.deliver_order_id,ocl.new_inv_comment,
GROUP_CONCAT(DISTINCT IF(ocd.container_id > 0,ocd.container_id,0)) AS container_id,ocd.plan_quantity,
COUNT(DISTINCT IF(ocd.container_id > 0,ocd.container_id,NULL)) AS container_num,
SUM(IF(ocd.container_id = 0,ocd.final_quantity,NULL)) AS quantity
FROM oc_x_deliver_order_check_location ocl
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id
WHERE ocd.status = 1 AND ocl.valid_status = 1 ";
            if ($order_type == 0) {
                $sql .= "
AND ocl.check_location_id = ".$check_location_id."
GROUP BY ocd.check_location_id";
            } else {
                $sql .= "
AND ocl.check_location_id = ".$check_location_id."
GROUP BY ocd.check_location_id";
        }
//        return $sql;
        $query = $db->query($sql);
        $results3 = $query->row;

        if ($order_type == 0) {
            $sql_update[2] = " UPDATE oc_order_inv SET 
`frame_count`='".$results3['container_num']."',`frame_vg_list`='".$results3['container_id']."',`box_count`='".$results3['quantity']."',`inv_comment`='".$new_inv_comment."',`uptime`=NOW() WHERE order_id=".$order_id;
        } else {
            $sql_update[3] = " UPDATE oc_x_deliver_order_inv SET 
`box_count`='".$results3['quantity']."',`inv_comment`='".$new_inv_comment."',`frame_count`='".$results3['container_num']."',`frame_vg_list`='".$results3['container_id']."',`uptime`=NOW() WHERE deliver_order_id=".$deliver_order_id;
            }
        }

        }

        $sql_update[1] = " UPDATE oc_x_deliver_order_check_location SET 
 `status` =  1,`comment` = '".$comment."',`new_inv_comment` = '".$new_inv_comment."',`up_time` = NOW()
WHERE check_location_id = '".$check_location_id."' 
AND add_user = '".$added_by."'";

        if ($sql_delete) {
            $sql_delete = rtrim($sql_delete,',');
            $sql = "UPDATE `oc_x_inventory_order_return_quantity` SET status = 0 WHERE status = 1 AND deliver_order_id = ".$deliver_order_id." AND order_id = ".$order_id." AND product_id IN (".$sql_delete.")";

            $db->query($sql);
        }
        if (count($sql_update) > 0) {
            foreach ($sql_update as $value) {
                $sql = $value;
                $db->query($sql);
            }
        }
        if ($sql_insert) {
            $sql_insert = rtrim($sql_insert,',');
            $sql = "INSERT INTO `oc_x_inventory_order_return_quantity` ( `order_id`, `deliver_order_id`, `product_id`, `order_quantity`, `sorting_quantity`, `return_quantity`, `uptime`, `move_flag`, `added_by`, `status`) VALUES".$sql_insert;

            $db->query($sql);
        }
        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg'  => '提交成功',
            'return_data' => ''
        );
        return $return;
    }
    /*zx
    取消或清空周转筐及商品*/
    public function cancelCheckOrderProduct( array $data){
        global  $db;
        $check_location_id = isset($data['data']['check_location_id']) ? trim($data['data']['check_location_id']) : false;
        $added_by = isset($data['data']['added_by']) ? trim($data['data']['added_by']) : false;
        $product_type = isset($data['data']['product_type']) ? trim($data['data']['product_type']) : false;
        $product_id = isset($data['data']['product_id']) ? trim($data['data']['product_id']) : false;
        $container_id = isset($data['data']['container_id']) ? trim($data['data']['container_id']) : false;
        $comment = isset($data['data']['comment']) ? trim($data['data']['comment']) : '';
        $user_group_id = isset($data['data']['user_group_id']) ? trim($data['data']['user_group_id']) : false;


        $result = $this->check_in_right_group($user_group_id);
        if ($result['result']) {
            $return = $result['return'];
            return $return;
        }
        $sql = "UPDATE oc_x_deliver_order_check_details SET `status` =  0
WHERE check_location_id = '".$check_location_id."'";
        if ($product_type == 1) {
            $sql .= " AND product_id = '".$product_id."'AND container_id = 0 ";
        } else {
            $sql .= " AND container_id = '".$container_id."'";
        }
        $db->query($sql);
        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg'  => '领取成功',
            'return_data' => ''
        );
        return $return;
    }
    /*
     * zx
     * 查询班组长的每日任务情况
     * */
    function getOrderCheckInformation(array $data){
        global  $db ;
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : false;
        $order_type = 2;
//            $data['data']['order_type']? $data['data']['order_type'] : 3;
        $added_by = $data['data']['added_by']? $data['data']['added_by'] : false;
        $date_added = $data['data']['date_added']? $data['data']['date_added'] : false;
        $deliver_order_id = $data['data']['deliver_order_id']? $data['data']['deliver_order_id'] : false;
//        $order_id = $data['data']['order_id']? $data['data']['order_id'] : false;
        /*
         * @param $total 展示所有，大于其展示so，小于其展示do
         * */

        $total = 2;
//        if ($user_group_id != 22) {
//            $return = array(
//                'return_code' => 'ERROR',
//                'return_msg'  => '权限不对，请刷新页面',
//                'return_data' => ''
//            );
//            return $return;
//        }
        if (!$added_by) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '账户过期，请重新登录',
                'return_data' => ''
            );
            return $return;
        }
        if (!$warehouse_id) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '账户过期，请重新登录',
                'return_data' => ''
            );
            return $return;
        }

        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg'  => '领取成功',
            'return_data' => ''
        );
        if ($order_type <= $total) {
            /*
             *zx
             *查询分配do单信息
             * */
            $sql  =  "SELECT
	ocl.deliver_order_id,
	ocl.check_location_id,
	oclr.reason_name AS name,
	ocl.comment,
	ocl.reasons AS status_id,
	ocl.new_inv_comment AS stock_area,
IF (
    ocd.container_id = 0,
    SUM(ocd.final_quantity),
     NULL
)AS product_num,
COUNT(IF (
	ocd.container_id != 0,
	ocd.container_id,
    NULL
)) AS container_num
FROM oc_x_deliver_order_check_location ocl 
LEFT JOIN oc_x_order_check_location_reason oclr ON ocl.reasons = oclr.check_location_reason_id
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id AND ocd.status = 1
WHERE ocl.add_user = '".$added_by."'
AND DATE_FORMAT(ocl.date_added,'%Y-%m-%d') = '".$date_added."'
AND ocl.order_id = 0
AND ocl.status = 1
AND ocl.valid_status = 1
GROUP BY ocl.order_id,ocl.deliver_order_id ORDER BY ocl.order_id DESC";
//            if ($order_id) {
//                $sql .= " AND ocl.deliver_order_id = '".$order_id."'";
//            }
//            return $sql;
            $query = $db->query($sql);
            $results2 = $query->rows;

            $return['return_data']['do'] = $results2;
        }
        if ($order_type >= $total) {
            /*
             *zx
             *查询分配so单信息
             * */
            $sql  =  "SELECT
	ocl.order_id,
	ocl.check_location_id,
	oclr.reason_name AS name,
	ocl.comment,
	ocl.reasons AS status_id,
	ocl.new_inv_comment AS stock_area,
SUM(IF (
    ocd.container_id = 0,
    ocd.final_quantity,
     NULL
))AS product_num,
COUNT(IF (
	ocd.container_id > 0,
	ocd.container_id,
    NULL
)) AS container_num
FROM oc_x_deliver_order_check_location ocl 
LEFT JOIN oc_x_order_check_location_reason oclr ON ocl.reasons = oclr.check_location_reason_id
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id AND ocd.status = 1
WHERE ocl.add_user = '".$added_by."'
AND DATE_FORMAT(ocl.date_added,'%Y-%m-%d') = '".$date_added."'
AND ocl.deliver_order_id = 0
AND ocl.status = 1
AND ocl.valid_status = 1
GROUP BY ocl.order_id,ocl.deliver_order_id ORDER BY ocl.order_id DESC";
//            if ($order_id) {
//                $sql .= " AND ocl.order_id = '".$order_id."'";
//            }
//            return $sql;
            $query = $db->query($sql);
            $results = $query->rows;
            $return['return_data']['so'] = $results;

        }
        $sql  =  "SELECT 
COUNT(IF(ocl.order_id > 0,ocl.order_id,NULL))AS so_num,
COUNT(IF(ocl.deliver_order_id > 0,ocl.deliver_order_id,NULL))AS do_num,
COUNT(IF(ocl.order_id > 0 AND ocl.status = 1,ocl.order_id,NULL))AS so_do_num,
COUNT(IF(ocl.deliver_order_id > 0 AND ocl.status = 1,ocl.deliver_order_id,NULL))AS do_do_num
FROM oc_x_deliver_order_check_location ocl 
WHERE ocl.add_user = '".$added_by."' 
AND ocl.valid_status = 1
AND DATE_FORMAT(ocl.date_added,'%Y-%m-%d') = '".$date_added."' 
GROUP BY ocl.add_user";
//        return $sql;
        $query = $db-> query($sql);
        $results3 = $query->row;
        $return['return_data']['num'] = $results3;

        return $return;

    }
    /*
     * zx
     * 获取周转筐或整件核查完毕后数据
     * */
    function getCheckOrderInformation(array $data){
        global  $db ;
        $check_location_id = isset($data['data']['check_location_id']) ? trim($data['data']['check_location_id']) : false;
        $order_type = isset($data['data']['order_type']) ? trim($data['data']['order_type']) : false;
        $product_type = isset($data['data']['product_type']) ? trim($data['data']['product_type']) : false;
        $added_by = isset($data['data']['added_by']) ? trim($data['data']['added_by']) : false;
        $warehouse_id = isset($data['data']['warehouse_id']) ? trim($data['data']['warehouse_id']) : false;
        $user_group_id = isset($data['data']['user_group_id']) ? trim($data['data']['user_group_id']) : false;
        $so_type = 0;
        $do_type = 1;
        $products_type = 1;
        $containers_type = 2;
        if ($order_type != $so_type && $order_type != $do_type) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '订单类型错误，请刷新后重试',
                'return_data' => ''
            );
            return $return;
        }
        if ($product_type != $products_type && $product_type != $containers_type) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '商品类型错误，请刷新后重试',
                'return_data' => ''
            );
            return $return;
        }
        $result = $this->check_in_right_group($user_group_id);
        if ($result['result']) {
            $return = $result['return'];
            return $return;
        }
        $sql = "SELECT ocd.product_id,ocl.order_id,ocl.deliver_order_id,op.name,ptw.stock_area,
GROUP_CONCAT(ocd.container_id) AS container_id,ocd.plan_quantity,
GROUP_CONCAT(ocd.sorting_quantity) AS sorting_quantitys,
GROUP_CONCAT(ocd.final_quantity) AS quantitys,
SUM(ocd.sorting_quantity) AS sorting_quantity,
SUM(ocd.final_quantity) AS quantity
FROM oc_x_deliver_order_check_location ocl 
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id AND ocd.status = 1 
LEFT JOIN oc_product op ON op.product_id = ocd.product_id 
LEFT JOIN oc_product_to_warehouse ptw ON op.product_id = ptw.product_id AND ptw.warehouse_id = '".$warehouse_id."' ";
//        if ($order_type == $so_type) {
//            $sql .= "
//LEFT JOIN oc_x_inventory_order_sorting oios ON oios.order_id = ocl.order_id ";
//        } else {
//            $sql .= "
//LEFT JOIN oc_x_inventory_order_sorting oios ON oios.deliver_order_id = ocl.deliver_order_id ";
//        }
        $sql .= "
WHERE ocl.add_user = '".$added_by."'
AND ocl.check_location_id = '".$check_location_id."'
AND ocl.valid_status = 1 ";
        if ($product_type == $products_type) {
            $sql .= " AND ocd.container_id = 0 ";
        } else if ($product_type == $containers_type) {
            $sql .= " AND ocd.container_id > 0 ";
        }
        $sql .= " GROUP BY product_id";
//        return $sql;
        $query = $db->query($sql);
        $results = $query->rows;
        if (empty($results)) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '商品未核查，请核查后在提交',
                'return_data' => ''
            );
        } else {
            $return = array(
                'return_code' => 'SUCCESS',
                'return_msg'  => '',
                'return_data' => $results
            );
        }

        return $return;

    }
    /*
     * zx
     * 查询具体订单商品信息
     * */
    function getCheckOrderInStockArea(array $data){
        global  $db ;
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : false;
        $order_type = $data['data']['order_type'] != 0 ? $data['data']['order_type'] : 0;
        $product_type = $data['data']['product_type']? $data['data']['product_type'] : 1;
        $added_by = $data['data']['added_by']? $data['data']['added_by'] : false;
        $user_group_id = $data['data']['user_group_id']? $data['data']['user_group_id'] : false;
        $date_added = $data['data']['date_added']? $data['data']['date_added'] : false;
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : false;
        $check_location_id = $data['data']['check_location_id']? $data['data']['check_location_id'] : false;
        if (!$added_by) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '账户过期，请重新登录',
                'return_data' => ''
            );
            return $return;
        }
        $result = $this->check_in_right_group($user_group_id);
        if ($result['result']) {
            $return = $result['return'];
            return $return;
        }
        if (!$warehouse_id) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '账户过期，请重新登录',
                'return_data' => ''
            );
            return $return;
        }

        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg'  => '领取成功',
            'return_data' => ''
        );
        $products = [];
        $container = [];
        $containers = [];

//        /*
//         *zx
//         *查询分配so单信息
//         * */
//        $sql  =  "SELECT oop.product_id,SUM(oop.quantity) AS plan_quantity,SUM(oios.quantity) AS sorting_quantity,op.name,ops.sku_barcode,ops.stock_area,oios.container_id,oios.added_by";
//        if ($order_type  == 1) {
//            $sql .= "
//FROM oc_x_deliver_order_product oop
//LEFT JOIN oc_x_inventory_order_sorting oios ON oios.product_id = oop.product_id AND oop.deliver_order_id = oios.deliver_order_id";
//        } else if ($order_type == 0) {
//            $sql .= "
//FROM oc_order_product oop
//LEFT JOIN oc_x_inventory_order_sorting oios ON oios.product_id = oop.product_id AND oop.order_id = oios.order_id";
//        }
//        $sql .= "
//LEFT JOIN oc_product op ON oop.product_id = op.product_id
//LEFT JOIN oc_product_to_warehouse ops ON oop.product_id = ops.product_id AND ops.warehouse_id = ".$warehouse_id."
//WHERE ";
//        if ($order_type  == 1) {
//            $sql .= " oios.deliver_order_id = '".$order_id."'";
//        } else if ($order_type == 0) {
//            $sql .= " oios.order_id = '".$order_id."'";
//        }
//        $sql .= " AND oios.status = 1
//GROUP BY product_id,container_id";
////            return $sql;
//        $query = $db->query($sql);
//        $results = $query->rows;
        if ($product_type == 1) {
            $sql2  =  "SELECT ocd.final_quantity AS quantity,ocd.plan_quantity,ocd.sorting_quantity,ocd.container_id,ocd.product_id,op.name,ops.sku_barcode,ops.stock_area
FROM oc_x_deliver_order_check_location ocl 
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id AND ocd.status = 1
LEFT JOIN oc_product op ON ocd.product_id = op.product_id
LEFT JOIN oc_product_to_warehouse ops ON ocd.product_id = ops.product_id AND ops.warehouse_id = ".$warehouse_id."
WHERE ocl.check_location_id = '".$check_location_id."'
AND ocl.add_user = '".$added_by."'
AND ocd.container_id = 0 
AND ocl.valid_status = 1
GROUP BY ocd.product_id,ocd.container_id";
//            return $sql;
            $query2 = $db->query($sql2);
            $results2 = $query2->rows;

//            foreach ($results as $value) {
//                if ($value['container_id'] > 0) {
//
//                } else {
//                    $products[$value['product_id']] = $value;
//                    $products[$value['product_id']]['quantity'] = 0;
                    foreach ($results2 as $value2) {
                        $products[$value2['product_id']] = $value2;
//                        if ($value['product_id'] == $value2['product_id']) {
//                            $products[$value['product_id']]['quantity'] = $value2['quantity'];
//                        }
                    }
//                }
//
//            }


            $return['return_data'] = $products;
        } else if ($product_type == 2) {
            $sql2  =  "SELECT ocd.final_quantity AS quantity,ocd.plan_quantity,ocd.sorting_quantity,ocd.container_id,ocd.product_id,op.name,ops.sku_barcode,ops.stock_area
FROM oc_x_deliver_order_check_location ocl 
LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id AND ocd.status = 1
LEFT JOIN oc_product op ON ocd.product_id = op.product_id
LEFT JOIN oc_product_to_warehouse ops ON ocd.product_id = ops.product_id AND ops.warehouse_id = ".$warehouse_id."
WHERE ocl.check_location_id = '".$check_location_id."'
AND ocl.add_user = '".$added_by."'
AND ocd.container_id > 0 
AND ocl.valid_status = 1
GROUP BY ocd.product_id,ocd.container_id";
//            return $sql2;
            $query2 = $db->query($sql2);
            $results2 = $query2->rows;

//return $results;
//            foreach ($results as $value) {
//                if ($value['container_id'] > 0) {
//                    $container[$value['product_id'].'@'.$value['container_id']] = $value;
//                    $container[$value['product_id'].'@'.$value['container_id']]['quantity'] = 0;
                    foreach ($results2 as $value2) {
                        $containers[$value2['container_id']] = [];
                        $container[$value2['product_id'].'@'.$value2['container_id']] = $value2;
//                        if ($value['product_id'] == $value2['product_id'] && $value['container_id'] == $value2['container_id']) {
//                            $container[$value['product_id'].'@'.$value['container_id']]['quantity'] = $value2['quantity'];
//                        }
                    }
//                }
//            }
//return $container;
            foreach ($containers as $key2 => $value2) {
                foreach ($container as $key => $value) {
                    $container_id = explode('@',$key)[1];
//                    if ($container_id == $key2) {
                        $containers[$container_id][$value['product_id']] = $value;
//                    }
                }
            }

            $return['return_data']['product'] = $container;
            $return['return_data']['container'] = $containers;
        }

//            return $containers;



        return $return;

    }
    //权限判断
    public function check_in_right_group($user_group_id) {
        $result['result'] = false;
        if (!in_array($user_group_id,[1,22])) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => '账户权限错误，请重新登录',
                'return_data' => ''
            );
            $result['result'] = true;
            $result['return'] = $return;
        }
        return $result;
    }
    /* zx
     * 班组长核查任务
     * 结束
     * */

}
$locationverifi = new LOCATIONVERIFI();

?>