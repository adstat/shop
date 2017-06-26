<?php
class ModelReportBdCoupon extends Model {
  public function getBdCoupon($filter_data){
      $sql = "SELECT F.coupon_id ,F.name AS  coupon_name ,A.order_id ,A.date_added ,A.customer_id,B.merchant_address ,C.bd_name, D.name AS area_name  ,G.name AS level_name ,os.name AS  status_name FROM oc_order A  LEFT JOIN oc_customer B ON A.customer_id = B.customer_id  LEFT  JOIN oc_x_bd  C ON  B.bd_id = C.bd_id LEFT JOIN  oc_x_area D ON B.area_id = D.area_id LEFT JOIN oc_coupon_history E ON  E.order_id = A.order_id LEFT JOIN  oc_coupon F ON E.coupon_id = F.coupon_id LEFT JOIN  oc_customer_group G ON  G.customer_group_id  = B.customer_group_id LEFT JOIN oc_order_status os ON os.order_status_id = A.order_status_id  WHERE 1=1 ";

      if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
          $sql .= " and date(A.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
      }
      if($filter_data['filter_station_id']){
          $sql .=" and A.station_id = '".$filter_data['filter_station_id']."' ";
      }
      if($filter_data['filter_bd_list']){
          $sql .=" and C.bd_id in (".$filter_data['filter_bd_list'].") ";
      }

      if ($filter_data['filter_bd_area_list']) {
          $sql .=" and D.area_id in (".$filter_data['filter_bd_area_list'].") ";
      }

      if($filter_data['filter_bd_num']){
          $sql .= " and F.coupon_id in (".$filter_data['filter_bd_num'].") ";
      }
      $sql .="order by C.bd_id";
      $query = $this->db->query($sql);
      return $query->rows;
  }

}