<?php
class ModelReportSortingLeaderShipment extends Model {
    public function getSortingLeaderShipment($filter_data){


        $sql = "SELECT A.order_id ,A.old_inv_comment,A.new_inv_comment,A.container_id,A.reasons,B.reason_name ,A.add_user,A.date_added ,C.username FROM oc_x_order_check_location A LEFT JOIN  oc_x_order_check_location_reason B ON A.reasons = B.check_location_reason_id LEFT JOIN oc_w_user C ON  C.user_id = A.add_user WHERE 1=1";

        if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
            $sql .= " and date(A.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }
        if($filter_data['filter_check_id']){
            $sql .=" and A.reasons = '".$filter_data['filter_check_id']."' ";
        }
        $sql .="GROUP BY A.reasons";

        $query = $this->db->query($sql);
        return $query->rows;

    }
    public function getCheckReturnList(){
        $sql = "SELECT  check_location_reason_id ,reason_name  FROM oc_x_order_check_location_reason";
        $query = $this->db->query($sql);
        return $query->rows;
    }
}