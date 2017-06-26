<?php
class ModelReportLogistic extends  Model{
    public function getLogisticDriver(){
        $sql = "SELECT logistic_driver_id,logistic_driver_title,logistic_driver_phone FROM oc_x_logistic_driver";
        $query = $this->db->query($sql);
        return $query->rows;
    }
    public function dateGap($start='',$end=''){
        $a = date_create($start);
        $b = date_create($end);
        $m = date_diff($a,$b);
        $gap = $m->format('%a');

        return $gap;
    }
    public function getlogisticInfo($filter_data){

        $sql = "SELECT A.order_id,A.date_added,B.shipping_firstname,B.shipping_address_1,B.total,C.name ,D.bd_name,F.logistic_line_title,F.logistic_driver_title,F.logistic_van_title, A.comments,A.user_comments,A.logistic_score,A.cargo_check,A.bill_of,A.box, G.name checkname
FROM oc_x_order_feadback A LEFT JOIN oc_order B ON A.order_id = B.order_id
LEFT JOIN oc_x_station C ON A.station_id = C.station_id
LEFT JOIN oc_x_bd D ON B.bd_id = D.bd_id
LEFT JOIN  oc_x_logistic_allot_order E ON A.order_id = E.order_id
LEFT JOIN oc_x_logistic_allot F ON F.logistic_allot_id = E.logistic_allot_id
LEFT JOIN oc_x_feadback_type G ON A.feadback_id = G.feadback_id ";

        if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
            $sql .= " where date(B.deliver_date) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }

        if($filter_data['filter_logistic_driver_list']){
            $sql .= " and F.logistic_driver_id  = '".$filter_data['filter_logistic_driver_list']."' ";
        }
        $sql .=" order by A.order_id ASC ,A.date_added ASC ";
        $query = $this->db->query($sql);
        if($query){
            $return['logistics'] = $query->rows;
        }

        return $return;
    }

    public  function getFeadbackCounts($filter_data){
        $sql = "SELECT A.feadback_id FROM  oc_x_order_feadback A LEFT JOIN oc_order B ON A.order_id = B.order_id LEFT JOIN oc_x_logistic_allot_order C ON A.order_id = C.order_id LEFT JOIN oc_x_logistic_allot E ON  C.logistic_allot_id = E.logistic_allot_id";
        if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
            $sql .= " where date(B.deliver_date) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }

        if($filter_data['filter_logistic_driver_list']){
            $sql .= " and E.logistic_driver_id  = '".$filter_data['filter_logistic_driver_list']."' ";
        }
        $query = $this->db->query($sql);
        return  $query->rows;
    }
    public function getcheakname(){
        $sql = "SELECT feadback_id ,name FROM oc_x_feadback_type";
        $query = $this->db->query($sql);
        return $query->rows;
    }


}