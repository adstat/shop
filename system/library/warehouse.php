<?php
class Warehouse {
    private $Warehous = array();
    public $warehouse_id_global;

    public function __construct($registry){
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');

        $sql = "select warehouse_id,title from " . DB_PREFIX . "x_warehouse where status = 1";

        $query = $this->db->query($sql);

        if(sizeof($query->rows)){
            foreach($query->rows as $value){
                $this->Warehous[$value['warehouse_id']] = array(
                    'warehouse_id' => $value['warehouse_id'],
                    'title'           => $value['title'],
                );
            }
        }

        $this->getWarehouseIdGlobal();
    }

    /**
     * @return array
     */
    public function getWarehouse(){
        return $this->Warehous;
    }

    public function getWarehouseIdGlobal(){
        $this->warehouse_id_global = isset($_SESSION['filter_warehouse_id_global']) ? $_SESSION['filter_warehouse_id_global'] : 0;
        return $this->warehouse_id_global;
    }


}