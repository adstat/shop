<?php
class ModelReportSortingStaff extends Model {
  public function getSortingStaff($filter_data){
      $sql = "SELECT xis.added_by ,
MIN(xis.uptime) AS start_time ,
max(xis.uptime) AS end_time,
sum(xis.quantity) AS  quantity,
sum(if(p.repack=0,xis.quantity,0))  whole_quantity ,
sum(if(p.repack=1,xis.quantity,0)) spare_quantity
FROM oc_x_inventory_order_sorting as xis
LEFT JOIN oc_product as p on p.product_id = xis.product_id
WHERE xis.uptime between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."'
";
      if($filter_data['filter_product_id_name'] ){
          $sql .=" and xis.added_by = '".$filter_data['filter_product_id_name']."' ";
      }
      $sql .="GROUP BY xis.added_by";

      $query = $this->db->query($sql);
      return $query->rows;

  }
}