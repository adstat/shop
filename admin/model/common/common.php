<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/9/21
 * Time: 15:28
 */
class Modelcommoncommon extends Model
{
    public function getBdList(){
        $sql = 'select bd_id, bd_name from '.DB_PREFIX.'x_bd';
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function getBdNameByBdId($bd_id){
        if($bd_id){
            $sql = 'select bd_name from '.DB_PREFIX.'x_bd where bd_id='.$bd_id;
            $query = $this->db->query($sql);
            return $query->row['bd_name'];
        }else{
            $bd_name = "¶©µ¥Îª¿ÕBD";
            return $bd_name;
        }

    }

    public function getOrderStatusName($id){
        $sql = 'select name from '.DB_PREFIX.'order_status where order_status_id='.$id;
        $query = $this->db->query($sql);
        return $query->row['name'];
    }

    public function getPaymentName($id){
        $sql = 'select name from '.DB_PREFIX.'order_payment_status where order_payment_status_id='.$id;
        $query = $this->db->query($sql);
        return $query->row['name'];
    }

    public function getOrderDeliverName($id){
        $sql = 'select name from '.DB_PREFIX.'order_deliver_status where order_deliver_status_id='.$id;
        $query = $this->db->query($sql);
        return $query->row['name'];
    }

    public function getLogisticDriverList()
    {
        $sql = "SELECT A.logistic_driver_id, A.logistic_driver_title, A.status
                FROM oc_x_logistic_driver A
                ORDER BY A.status DESC, A.logistic_driver_id DESC";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getCustomerGroupList(){
        $sql = "SELECT customer_group_id, name group_name from oc_customer_group order by sort_order";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getStationList(){
        $sql = "SELECT station_id, name station_name from oc_x_station where status=1";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getProductType($station_id=0){
        if($station_id){
            $sql = "SELECT product_type_id, name type_name from oc_product_type where status=1 and station_id = '".$station_id."'";
        }
        else{
            $sql = "SELECT product_type_id, name type_name from oc_product_type where status=1";
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }
}