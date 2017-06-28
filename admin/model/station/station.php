<?php
/**
 * Created by PhpStorm.
 * User: liuyibao
 * Date: 15-8-27
 * Time: 下午7:53
 */
class ModelStationStation extends Model{
    public function getStations(array $condition = array()){
        $sql = "SELECT * FROM oc_x_station ORDER BY station_id DESC";
        if(isset($condition['start']) && isset($condition['limit'])){
            $start = (int)$condition['start'];
            $limit = (int)$condition['limit'];
            $sql .= " LIMIT $start, $limit";
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getTotalStations(){
        $query = $this->db->query("SELECT COUNT(station_id) AS total FROM oc_x_station");
        return $query->row['total'];
    }

    public function getStationList(){
        $query = $this->db->query('select station_id, name from oc_x_station where status = 1');
        $stationsRaw = $query->rows;
        $stationList = array();
        if(sizeof($stationsRaw)){
            foreach($stationsRaw as $station){
                $stationList[$station["station_id"]] = $station;
            }
        }

        return $stationList;
    }

    //查找全局变量，设定平台搜索框中被选中的平台
    public function setFilterStation($warehouse_id){
        $sql = "select station_id from oc_x_warehouse where warehouse_id = ".(int)$warehouse_id;

        $query = $this->db->query($sql);

        if($query->num_rows){
            return $query->row['station_id'];
        }else{
            return 0;
        }
    }

    public function getProductTypeList(){
        $query = $this->db->query("select product_type_id,name from oc_product_type where status = 1");
        $typeRaw = $query->rows;
        $typeList = array();
        if(sizeof($typeRaw)){
            foreach($typeRaw as $type){
                $typeList[$type["product_type_id"]] = $type;
            }
        }

        return $typeList;
    }

    // User: hhz
    public function getStationNameById($id){
        $query = $this->db->query('select name from oc_x_station where station_id = '.$id);
        return $query->row['name'];
    }

    public function getCustomerGroupList(){
        $query = $this->db->query('select customer_group_id, name from oc_customer_group');
        $customerGroup = $query->rows;
        $customerGroupList = array();
        if(sizeof($customerGroup)){
            foreach($customerGroup as $value){
                $customerGroupList[$value["customer_group_id"]] = $value;
            }
        }

        return $customerGroupList;
    }

    public function getPurchasePerson(){
        $sql = "select A.user_id,if(status = 1,concat(u.lastname,u.firstname),concat(u.lastname,u.firstname,'(停用)')) name,u.status
                from
                (
                    select o.added_by user_id
                    from oc_x_pre_purchase_order o
                    group by o.added_by
                ) A
                left join oc_user u on u.user_id = A.user_id
                order by status desc";

        $query = $this->db->query($sql);
        $purchaseGroup = $query->rows;
        $purchaseGroupList = array();
        if(sizeof($purchaseGroup)){
            foreach($purchaseGroup as $value){
                $purchaseGroupList[$value["user_id"]] = $value;
            }
        }

        return $purchaseGroupList;
    }

    public function getLogisticLine(){
        $sql = "select logistic_line_id,logistic_line_title from oc_x_logistic_line";

        $query = $this->db->query($sql);
        $line = $query->rows;
        $lineList = array();
        if(sizeof($line)){
            foreach($line as $value){
                $lineList[$value["logistic_line_id"]] = $value;
            }
        }

        return $lineList;
    }

    public function getStationWarehouse(){
        $sql = "select os.station_id,os.name,ow.warehouse_id,ow.title
                from oc_x_station os
                left join oc_x_warehouse ow on ow.station_id = os.station_id
                where os.status = 1
                group by os.station_id,ow.warehouse_id
                ";
        $query = $this->db->query($sql);
        $station_warehouse = array();
        if(sizeof($query->rows)){
            foreach($query->rows as $value){
                $station_warehouse[$value['station_id']][] = $value;
            }
        }

        return $station_warehouse;
    }

    //查找平台下所有的仓库
    public function getWarehouseBelongToStation($station_id,$warehosue_id = 0){
        $sql = "select warehouse_id,title from oc_x_warehouse where status = 1 and station_id =" . $station_id;

        if($warehosue_id > 0){
            $sql .= " and warehouse_id = ". $warehosue_id;
        }

        $query = $this->db->query($sql);
        $warehouse = $query->rows;
        $warehouseList = array();
        if(sizeof($warehouse)){
            foreach($warehouse as $value){
                $warehouseList[$value["warehouse_id"]] = $value;
            }
        }

        return $warehouseList;
    }

    //查找平台下所有的仓库
    public function getWarehouseIdBelongToStation($station_id){
        $sql = "select warehouse_id,title from oc_x_warehouse where station_id =" . $station_id;

        $query = $this->db->query($sql);
        $warehouse = $query->rows;
        $warehouseList = array();
        if(sizeof($warehouse)){
            foreach($warehouse as $value){
                $warehouseList[] = $value['warehouse_id'];
            }
        }

        return $warehouseList;
    }

    //查找所有的仓库
    public function getWarehouseAndStation($params = array()){
        $sql = "SELECT A.warehouse_id,A.title,A.station_id,B.name
                  FROM oc_x_warehouse A
                  LEFT JOIN oc_x_station B ON B.station_id = A.station_id
                  WHERE A.status = 1";

        if(!empty($params))
        {
            !empty($params['warehouse_ids']) && sizeof($params['warehouse_ids']) && $sql .= " AND A.warehouse_id IN (".implode(',', $params['warehouse_ids']).")";
        }

        $query      = $this->db->query($sql);
        $warehouse  = $query->rows;
        if(sizeof($warehouse)){ return $warehouse; }

        return array();
    }

    //查找商品对应的平台
    public function getProductStation($product_id){
        $sql = "select station_id from oc_product where product_id =" . $product_id;
        $query = $this->db->query($sql);

        return $query->row['station_id'];
    }

    //查找区域公用方法
    public function getAreaName($filter_warehouse_id){
        $sql = "select A.area_id,concat(A.district,'/',A.name) title
            from oc_x_area A
            where 1
            ";
        $area_q = array();
        if($filter_warehouse_id){
            $sql_w = "select area_id from oc_x_area_warehouse where warehouse_id = '".(int)$filter_warehouse_id."'";
            $query = $this->db->query($sql_w);
            if($query->num_rows){
                foreach($query->rows as $value){
                    $area_q[] = $value['area_id'];
                }
                $area_ids = implode(',',$area_q);
            }
        }

        if(sizeof($area_q)){
            $sql .= " and A.area_id in (".$area_ids.")";
        }

        $sql .= " group by A.area_id";

        $sql .= " order by field(A.district, '浦东','闵行','杨浦','松江','黄浦','徐汇','长宁','静安','普陀','虹口','杨浦','宝山','嘉定','金山','松江','青浦','奉贤')";
//var_dump($sql);die;
        $query = $this->db->query($sql);
        $area = $query->rows;
        $areaList = array();
        if(sizeof($area)){
            foreach($area as $value){
                $areaList[$value["area_id"]] = $value;
            }
        }

        return $areaList;
    }
}