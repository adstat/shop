<?php
class ModelReportWarehouseDistribution extends Model {
    public function getOrderDistr($filter_data){
        $sql = "SELECT  A.order_id ,C.name AS station_name ,D.name AS product_name ,B.date_added,A.inventory_name,A.quantity FROM oc_order_distr  A
LEFT JOIN oc_order B ON A.order_id = B.order_id
LEFT JOIN oc_x_station C ON B.station_id = C.station_id
LEFT JOIN oc_product_type D ON D.product_type_id = A.ordclass WHERE 1=1";

        if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
            $sql .= " and date(B.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }
        if($filter_data['filter_station_id']){
            $sql .=" and B.station_id = '".$filter_data['filter_station_id']."' ";
        }
        if($filter_data['filter_product_type']){
            $sql .=" and A.ordclass  = '".$filter_data['filter_product_type']."' ";
        }
        if (!empty($filter_data['filter_product_id_name'])) {
            $sql .= "and A.inventory_name = '" .$filter_data['filter_product_id_name'] . " '";
        }

//        $sort_data = array(
//            'A.order_id',
//        );
//
//
//        if (isset($filter_data['sort']) && in_array($filter_data['sort'], $sort_data)) {
//            $sql .= " ORDER BY " . $filter_data['sort'];
//
//        } else {
//            $sql .= " ORDER BY A.order_id";
//        }
//
//        if (isset($filter_data['order']) && ($filter_data['order'] == 'DESC')) {
//            $sql .= " DESC";
//
//        } else {
//            $sql .= " ASC";
//
//        }



        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getOrderDistrTotal($filter_data){
        $sql = "SELECT COUNT(*) AS total FROM oc_order_distr  A
 LEFT JOIN oc_order B ON A.order_id = B.order_id
 LEFT JOIN oc_x_station C ON B.station_id = C.station_id
 LEFT JOIN oc_product_type D ON D.product_type_id = A.ordclass WHERE 1=1 ";
        if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
            $sql .= " and date(B.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }
        if($filter_data['filter_station_id']){
            $sql .=" and B.station_id = '".$filter_data['filter_station_id']."' ";
        }

        if($filter_data['filter_product_type']){
            $sql .=" and A.ordclass  = '".$filter_data['filter_product_type']."' ";
        }

        if (!empty($filter_data['filter_product_id_name'])) {
            $sql .= "and A.inventory_name = '" .$filter_data['filter_product_id_name'] . " '";
        }



        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getProductList(){
        $sql = "SELECT  product_type_id , name FROM oc_product_type";
        $query = $this->db->query($sql);
        return $query->rows;
    }
}