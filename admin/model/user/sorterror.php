<?php
class ModelUserSorterror extends Model{

    public function getSorterrors($filter_order_id){
        $sql = "SELECT A.order_id, A.sorterror_type,B.name ,A.comment,A.date_added  FROM  oc_x_order_distr_sorterror A  LEFT JOIN  oc_x_sorterror_type B ON A.sorterror_type = B.sorterror_id WHERE  1=1  ";

        if (!empty($filter_order_id)){
            $sql .= " AND A.order_id  =  ' " . $this->db->escape($filter_order_id) . " '";
        }

        $query = $this->db->query($sql);

        return $query->rows;


    }
    public function getSorttype($filter_order_id){
        $sql = "SELECT COUNT(*) FROM oc_x_order_distr_sorterror WHERE sorterror_type = ' $filter_order_id '";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getSorterror($order_id) {
        $query = $this->db->query("SELECT DISTINCT A.order_id ,A.ordclass,A.inventory_name ,A.quantity FROM  oc_order_distr A   WHERE A.order_id = ' $order_id '");
        return $query->rows;
    }

    public function getSorterrorType(){
        $query = $this->db->query("SELECT sorterror_id,name FROM oc_x_sorterror_type");
        return $query->rows;
    }

    public function addSorterrors($targetTable,$rowData){
        $sql = "INSERT INTO " . $targetTable . " SET ";
        foreach ($rowData as $k => $v) {
            $sql .= $k . "='" . $v . "',";

        }
        $sql .= 'date_added=NOW()';

        $this->db->query($sql);
        return $this->db->getLastId();
    }



}