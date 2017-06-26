<?php
class ModelReportLogisticDriver extends  Model{

    public function dateGap($start='',$end=''){
        $a = date_create($start);
        $b = date_create($end);
        $m = date_diff($a,$b);
        $gap = $m->format('%a');

        return $gap;
    }


    public function getLogisticDriver(){
        $sql = "SELECT logistic_driver_id,logistic_driver_title,logistic_driver_phone FROM oc_x_logistic_driver";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getOrderPaymentStatus(){
        $sql = "select order_payment_status_id id,name from oc_order_payment_status";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderDeliverStatus(){
        $sql = "select order_deliver_status_id id,name from oc_order_deliver_status";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderStatus(){
        $sql = "select order_status_id id,name from oc_order_status";
        $query = $this->db->query($sql);

        return $query->rows;
    }
    public function getBDAreaList(){
        $sql = "select area_id id,concat(district, '->',name) name from oc_x_area";
        $query = $this->db->query($sql);

        return $query->rows;
    }
    public function getAreaList(){
        $sql = "select  district name from oc_x_area GROUP BY  district ";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getLogisticInfo ($filter_data){
        $sql = "select A.order_id ,A.deliver_date,A.station_id ,LA.logistic_driver_id, LA.logistic_driver_title, A.shipping_address_1,C.name order_name, D.name payment_name ,E.name deliver_name, AR.name , AR.district,B.frame_count, B.box_count  from oc_order A
left join oc_order_inv B on A.order_id = B.order_id
left join oc_order_status C on A.order_status_id = C.order_status_id
left join oc_order_payment_status D on A.order_payment_status_id = D.order_payment_status_id
left join oc_order_deliver_status E on A.order_deliver_status_id = E.order_deliver_status_id
left join oc_customer CUST on A.customer_id = CUST.customer_id
left join oc_x_bd BD on A.bd_id = BD.bd_id
left join oc_x_area AR on CUST.area_id = AR.area_id
left join oc_x_logistic_allot_order LAO on A.order_id = LAO.order_id
left join oc_x_logistic_allot LA on LAO.logistic_allot_id = LA.logistic_allot_id WHERE 1 = 1 ";

        if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
            $sql .= " and date(LA.deliver_date) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }

        if($filter_data['filter_logistic_driver_list']){
            $sql .= " and LA.logistic_driver_id  = '".$filter_data['filter_logistic_driver_list']."' ";
        }

        if($filter_data['filter_station']){
            $sql .=" and A.station_id = '".$filter_data['filter_station']."' ";
        }

        if($filter_data['filter_order_status']){
            $sql .=" and A.order_status_id = '".$filter_data['filter_order_status']."' ";
        }

        if($filter_data['filter_order_payment_status']){
            $sql .=" and A.order_payment_status_id  = '".$filter_data['filter_order_payment_status']."' ";
        }

        if($filter_data['filter_order_deliver_status']){
            $sql .=" and A.order_deliver_status_id = '".$filter_data['filter_order_deliver_status']."' ";

        }
        if($filter_data['filter_bd_area_list']){
            $sql .=" and CUST.area_id = '".$filter_data['filter_bd_area_list']."' ";

        }



        $query = $this->db->query($sql);

        return $query->rows;
    }

}