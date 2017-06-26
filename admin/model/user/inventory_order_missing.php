<?php
class ModelUserInventoryOrderMissing extends Model {
    public function getInventoryOrderMissing($filter_data){
        $sql = "SELECT A.order_id, A.added_by, A.date_added,A.confirmed_by,
A.supervisor_checked,A.monitor_checked,A.confirmed,A.recovered_by,A.date_confirmed,
A.date_recovered,A.recovered,A.status,A.memo,B.username AS confirmed_name ,C.username AS recovered_name FROM oc_x_inventory_order_missing A LEFT JOIN oc_user B ON A.confirmed_by = B.user_id  LEFT  JOIN oc_w_user C ON  A.recovered_by = C.user_id WHERE 1=1";

        if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
            $sql .= " and date(A.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }
        if($filter_data['filter_confirm_id']!=2 ){
            $sql .=" and A.confirmed = '".$filter_data['filter_confirm_id']."' ";
        }
        if($filter_data['filter_status_id'] !=2){
            $sql .=" and A.status = '".$filter_data['filter_status_id']."' ";
        }
        if($filter_data['filter_recover_id'] !=2){
            $sql .=" and A.recovered = '".$filter_data['filter_recover_id']."' ";
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function recovered_order($order_id,$userId,$reasons){


        $sql = "SELECT  memo  FROM oc_x_inventory_order_missing WHERE  order_id = '" .$order_id ."'";
        $query = $this->db->query($sql);
        $result1 = $query->row;
         if(strlen($result1['memo']) > 0){
                return 2;
         }else{
             $sql = "UPDATE oc_x_inventory_order_missing SET recovered = 1 ,date_recovered = now() ,recovered_by = '" . $userId . "' ,memo='". $reasons ."'   WHERE order_id = '" . $order_id . "' ";
             $query = $this->db->query($sql);
             if($query){
                 $sql = "UPDATE oc_x_stock_move  SET  inventory_type_id = 20 WHERE order_id = '" . $order_id . "' ";
                 $query1 = $this->db->query($sql);
             }
             return $query1;
         }

    }

    public function confirmed_order($order_id,$userId )
    {
        // 查询订单之前是否被确认过
        $sql = "SELECT  confirmed  FROM oc_x_inventory_order_missing WHERE  order_id ='" . $order_id . "' ";
        $query = $this->db->query($sql);
        $result = $query->row;
        if ($result['confirmed'] == 1) {
            return 3;
        } else {
            //查询出需要确认丢失的订单 是否已经分拣完的状态
            $sql = "SELECT  COUNT(1) AS num FROM oc_order WHERE order_id ='" . $order_id . "' AND order_status_id = 6 ";

            $query = $this->db->query($sql);
            $result = $query->row;

            if ($result['num'] == 0) {
                return 2;
            } else {

                $sql = "UPDATE oc_x_inventory_order_missing SET confirmed = 1 ,date_confirmed = now() ,confirmed_by = '" . $userId . "' WHERE order_id = '" . $order_id . "' ";

                $query = $this->db->query($sql);
                if ($query) {
                    $sql = "UPDATE oc_x_stock_move  SET  inventory_type_id = 19 WHERE order_id = '" . $order_id . "' ";
                    $query = $this->db->query($sql);
                    if ($query) {
                        $sql = "SELECT * FROM oc_order_inv WHERE order_id = '" . $order_id . "' ";
                        $query = $this->db->query($sql);
                        $result = $query->rows;

                        $sql1 = "SELECT * FROM oc_x_inventory_order_sorting WHERE order_id = '" . $order_id . "' ";
                        $query1 = $this->db->query($sql1);
                        $result1 = $query1->rows;

                        $order_inv = json_encode($result);
                        $inventory_order_sorting = json_encode($result1);

                        $sql2 = "UPDATE oc_x_inventory_order_missing SET order_inv_history = '" . $order_inv . "' , order_sorting_history = '" . $inventory_order_sorting . "'  WHERE order_id = '" . $order_id . "' ";
                        $query2 = $this->db->query($sql2);
                        if ($query2) {
                            $sql1 = "DELETE FROM oc_order_inv WHERE order_id = '" . $order_id . "'";
                            $sql2 = "DELETE FROM oc_x_inventory_order_sorting  WHERE order_id = '" . $order_id . "'";
                            $sql3 = "DELETE FROM oc_order_distr WHERE order_id = '" . $order_id . "'";
                            $query1 = $this->db->query($sql1);
                            $query2 = $this->db->query($sql2);
                            $query3 = $this->db->query($sql3);

                        }
                    }

                    $sql1 = "SELECT logistic_allot_id FROM oc_x_logistic_allot_order WHERE order_id = '" . $order_id . "' ";
                    $query1 = $this->db->query($sql1);
                    $result1 = $query1->row;
                    if (count($result1) > 0) {
                        $sql = "DELETE FROM oc_x_logistic_allot  WHERE logistic_allot_id = '" . $result1['logistic_allot_id'] . "'";
                        $query = $this->db->query($sql);
                        if ($query) {
                            $sql = "DELETE FROM oc_x_logistic_allot_order  WHERE order_id = '" . $order_id . "' ";
                            $query = $this->db->query($sql);
                        }
                    }

                    $sql2 = "SELECT order_id FROM oc_return WHERE order_id = '" . $order_id . "'";
                    $query2 = $this->db->query($sql2);
                    $result2 = $query2->row;
                    if (count($result2) > 0) {
                        $sql = "DELETE FROM oc_return  WHERE order_id = '" . $order_id . "' ";
                        $query = $this->db->query($sql);
                    }

                    $sql3 = "UPDATE oc_order  SET order_status_id = 2  WHERE order_id = '" . $order_id . "' ";

                }
                $query3 = $this->db->query($sql3);
                return $query3;
            }
        }
    }
}