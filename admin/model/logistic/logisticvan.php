<?php
/**
 * Created by PhpStorm.
 * User: jshy
 * Date: 2016/11/7
 * Time: 15:03
 */

class ModelLogisticLogisticvan extends Model
{
    public function getOrdersByAreaByDate($classify,$area_id = false,$search_date ,$station_id=false,$warehouse_id_global){
        if($classify == 1) {
            $sql = "select A.order_id,D.station_id,D.name, A.area_id,A.shipping_address_1,A.deliver_date,A.deliver_slot,A.order_status_id,B.bd_id,B.bd_name,C.name area_name  from oc_order A  left join oc_x_bd B on A.bd_id = B.bd_id  left join oc_x_logistic_allot_order lao on lao.order_id = A.order_id
        left join oc_x_area C on A.area_id = C.area_id left join oc_x_station D on A.station_id = D.station_id
        where lao.order_id is null and A.order_status_id in (2,5,6,8) and A.order_deliver_status_id in (1,2,11) and A.area_id = '$area_id' and A.deliver_date = '$search_date' and D.station_id = '$station_id' and A.warehouse_id = '".$warehouse_id_global."' ";

            $sql .= " order by A.shipping_address_1 asc";

            return $this->db->query($sql)->rows;

        }elseif ($classify ==2){
            $sql = "select A.order_id,D.station_id,D.name, A.area_id,A.shipping_address_1,A.deliver_date,A.deliver_slot,A.order_status_id,B.bd_id,B.bd_name,C.name area_name  from oc_order A  left join oc_x_bd B on A.bd_id = B.bd_id
        left join oc_x_area C on A.area_id = C.area_id left join oc_x_station D on A.station_id = D.station_id inner join oc_x_logistic_allot_order lao on lao.order_id = A.order_id
        where  A.order_status_id in (2,5,6,8) and A.order_deliver_status_id in (1,2,11) and A.area_id = '$area_id'and A.deliver_date = '$search_date'and D.station_id = '$station_id' and A.warehouse_id = '".$warehouse_id_global."' ";

            $sql .= " order by A.shipping_address_1 asc";

        }else{
            $sql = "select A.order_id,D.station_id,D.name, A.area_id,A.shipping_address_1,A.deliver_date,A.deliver_slot,A.order_status_id,B.bd_id,B.bd_name,C.name area_name  from oc_order A  left join oc_x_bd B on A.bd_id = B.bd_id
        left join oc_x_area C on A.area_id = C.area_id left join oc_x_station D on A.station_id = D.station_id
        where  A.order_status_id in (2,5,6,8) and A.order_deliver_status_id in (1,2,11) and A.area_id = '$area_id'and A.deliver_date = '$search_date'and D.station_id = '$station_id' and A.warehouse_id = '".$warehouse_id_global."' ";
            $sql .= " order by A.shipping_address_1 asc";
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function getLine(){
        $sql = 'select A.logistic_line_id ,A.logistic_line_title from  oc_x_logistic_line A';
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getDriver(){
        $sql = 'select A.logistic_driver_id, A.logistic_driver_title from oc_x_logistic_driver A';
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function  getCar(){
        $sql = 'select A.logistic_van_id, A.logistic_van_title from oc_x_logistic_van A';
        $query = $this->db->query($sql);
        return $query->rows;
}
    public function getDeliveryman(){
        $sql = 'select A.logistic_deliveryman_id,A.logistic_deliveryman_title from oc_x_logistic_deliveryman A';
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function getSlotList(){
        $sql = 'select * from oc_deliver_slot where status =1';
        return $this->db->query($sql)->rows;
    }
    public  function  applyOrderToDriver($rowData){

        $targetTable= 'oc_x_logistic_allot';
        $sql = "INSERT INTO " . $targetTable . " SET ";
        foreach ($rowData as $k => $v) {
            $sql .= $k . "='" . $v . "',";
        }
        $sql .= 'date_added=NOW()';
        $this->db->query($sql);
        return $this->db->getLastId();

    }
}