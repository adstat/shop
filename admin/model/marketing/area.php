<?php
class ModelMarketingArea extends Model {

    public function getAreaCity(){
        $city = array('上海');

        return $city;
    }

    public function getAreaDistrict(){
        $district = array(
            '浦东','闵行','杨浦','松江','黄浦','徐汇','长宁','静安','普陀','虹口','杨浦','宝山','嘉定','金山','松江','青浦','奉贤'
            );

        return $district;
    }

    public function getArea($keyword=''){
        $sql = "select A.area_id, A.name area_name, A.city area_city, A.district area_district, A.bd_id, B.bd_name, A.status from oc_x_area A
        left join oc_x_bd B on A.bd_id = B.bd_id
        order by field(A.district, '浦东','闵行','杨浦','松江','黄浦','徐汇','长宁','静安','普陀','虹口','杨浦','宝山','嘉定','金山','松江','青浦','奉贤')
        ";

        if($keyword !== ''){
            $keyword  = $this->db->escape($keyword);
            $sql .= " where name like '%".$keyword."%' or district like '%".$keyword."%'";
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

    // 获取区域,仓库信息
    public function getAreaWarehouse()
    {
        $sql = "SELECT A.area_id,A.warehouse_id,B.title,C.name station_name
                  FROM oc_x_area_warehouse A
                  LEFT JOIN oc_x_warehouse B ON A.warehouse_id = B.warehouse_id
                  LEFT JOIN oc_x_station C ON B.station_id = C.station_id";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getAreaUnset($keyword='') {
        $sql = "select A.area_id, A.name area_name, A.city area_city, A.district area_district, A.bd_id, B.bd_name, A.status
        from oc_x_area A
        left join oc_x_bd B on A.bd_id = B.bd_id
        where A.draw_info is null
        order by field(A.district, '浦东','闵行','杨浦','松江','黄浦','徐汇','长宁','静安','普陀','虹口','杨浦','宝山','嘉定','金山','松江','青浦','奉贤')
        ";

        if($keyword !== ''){
            $keyword  = $this->db->escape($keyword);
            $sql .= " where name like '%".$keyword."%' or district like '%".$keyword."%'";
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getBdList(){
        $sql = "select bd_id, bd_name, phone, status from oc_x_bd order by status desc, bd_id ";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function add($targetTable, $rowData){
        $sql = "INSERT INTO ".$targetTable . " SET ";
        foreach($rowData as $k=>$v){
            $sql .= $k . "='" . $v . "',";
        }
        $sql .= 'date_added=NOW()';

        $this->db->query($sql);
        return $this->db->getLastId();
    }

    // 新增区域,仓库关联
    public function addAreaWarehouse($area_id = 0, $warehouse_data = array())
    {
        $area_id = (int)$area_id;
        if($area_id <= 0 || !is_array($warehouse_data) || empty($warehouse_data)){ return false; }

        $sql = "INSERT INTO oc_x_area_warehouse (area_id, warehouse_id, station_id) VALUES";
        foreach($warehouse_data as $value){
            $sql .= "(". $area_id .",". (int)$value['warehouse_id'] .",".(int)$value['station_id']."),";
        }
        $sql = rtrim($sql, ',');

        return $this->db->query($sql);
    }

    // 删除区域,仓库关联
    public function deleteAreaWarehouse($area_id = 0)
    {
        $area_id = (int)$area_id;
        if($area_id <= 0 ){ return false; }

        $sql = "DELETE FROM oc_x_area_warehouse WHERE area_id = ".$area_id;

        return $this->db->query($sql);
    }

    public function update($targetTable, $rowData, $indexFilter){
        //对比传入的BD_ID，如果发生变化，则进行更新，否则直接返回用户没有更改当前BD，则之后的订单调离亦不需要做处理
        $sql =  "select bd_id from " .$targetTable." where " .$indexFilter['field']." = '".$indexFilter['value']."'";

        $query = $this->db->query($sql);

        $query = true;
        $query_order = true;

        if(isset($query->row['bd_id']) ? $query->row['bd_id'] : 1 != $rowData['bd_id']){
            //更新区域BD
            $sql = "UPDATE ".$targetTable . " SET ";
            foreach($rowData as $k=>$v){
                if($k !== 'order_date'){
                    $sql .= $k . "='" . $v . "',";
                }
            }
            $sql .= " date_modified=NOW()";
            $sql .= " WHERE ".$indexFilter['field']." = '".$indexFilter['value']."'";

            $query = $query && $this->db->query($sql);

            if($query){
                //更新区域绑定客户BD
                $sql = "update  oc_customer A
                left join oc_x_bd B on A.bd_id = B.bd_id
                left join oc_x_area C on A.area_id = C.area_id
                left join oc_x_bd D on C.bd_id = D.bd_id
                set A.bd_id = C.bd_id
                where C.bd_id = '". (int)$rowData['bd_id'] ."'
                ";

                $query = $query && $this->db->query($sql);
            }


            //分离新BD做的订单
            if(isset($rowData['order_date'])){
                $sql = "update oc_order A left join oc_customer B on A.customer_id = B.customer_id
                set A.bd_id = B.bd_id
                where date(A.date_added) >= '".$rowData['order_date']."' and B.bd_id = '".(int)$rowData['bd_id']."'";

                $query_order = $query_order && $this->db->query($sql);

            }else{

                $query_order = true;
            }

        }else{
            //分离新BD做的订单
            if(isset($rowData['order_date'])){
                $sql = "update oc_order A left join oc_customer B on A.customer_id = B.customer_id
                set A.bd_id = B.bd_id
                where date(A.date_added) >= '".$rowData['order_date']."' and B.bd_id = '".(int)$rowData['bd_id']."'";

                $query_order = $query_order && $this->db->query($sql);

            }else{

                $query_order = true;

            }
        }

        $return = array(
            'bd_code' => $query,
            'order_code' => $query_order,
            'flag' => $query && $query_order ? 'ok':'no',
        );

        return $return;
    }

    public  function updateAreaUser($bd_id){
        $sql = "update  oc_customer A
              left join oc_x_bd B on A.bd_id = B.bd_id
            left join oc_x_area C on A.area_id = C.area_id
            left join oc_x_bd D on C.bd_id = D.bd_id
            set A.bd_id = C.bd_id
            where C.bd_id = ' $bd_id '
";
        $query = $this->db->query($sql);
        if ($query == 1){
            return 2;
        }
    }

    public function updateAreaOrders($order_date,$bd_id){
        $sql = "update oc_order A left join oc_customer B on A.customer_id = B.customer_id
        set A.bd_id = B.bd_id
        where date(A.date_added) >= ' $order_date ' and B.bd_id = ' $bd_id '";
        $query = $this->db->query($sql);
        if ($query == 1){
            return 3;
        }
    }



    public function addHistory($targetTable, $historyTable, $historyFields, $indexFilter, $operator){
        $sql = "INSERT INTO ".$historyTable." (".implode(',',$historyFields).",status,date_added,added_by)
        SELECT ".implode(',',$historyFields).",status,NOW(),'".$operator."'
        FROM ".$targetTable."
        WHERE ".$indexFilter['field']." = '".$indexFilter['value']."'";

        //return $sql;
        return $this->db->query($sql);
    }

    public function getCustomerByAreaByBd($area_id = false, $bd_id = false, $customer_id = false){
        $sql = "select A.customer_id, A.status, A.area_id, A.merchant_name, A.merchant_address, B.address_1 address, C.bd_id, C.bd_name, D.name area_name
        from oc_customer A
        left join oc_address B on A.customer_id = B.customer_id
        left join oc_x_bd C on A.bd_id = C.bd_id
        left join oc_x_area D on A.area_id = D.area_id
        where B.status = 1 and B.default = 1
        ";

        if($customer_id !== false){
            $sql .= " and A.customer_id = '".$customer_id."'";
        } else {
            if($area_id !== false){
                $sql .= " and A.area_id = '".$area_id."'";
            }

            if($bd_id !== false){
                $sql .= " and A.bd_id = '".$bd_id."'";
            }
        }

        //return $sql;
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function applyCustomerToArea($customers = array(),$area_id = 0){
        $sql = "update oc_customer set area_id = '".$area_id."' where customer_id in (".implode(',', $customers).")";

        //return  $sql;
        return $this->db->query($sql);
    }

    public function applyDrawToArea($position = array(),$area_id){
        //序列化存储百度地图画的地图信息
        $info = serialize($position);
        $sql = "update oc_x_area set draw_info = '". $info ."' where area_id =" . $area_id;

        return $this->db->query($sql);
    }

    public function getAreaDrawInfo($area_id,$bd_id){
        $sql = "select area_id, name, draw_info from oc_x_area where draw_info is not null and visible = 1";

        if($area_id>0){
            $sql .= " and area_id = '".(int) $area_id."'";
        }

        if($bd_id>0){
            $sql .= " and bd_id = '".(int) $bd_id."'";
        }

        $query = $this->db->query($sql);

        $result = array();

        if(sizeof($query->rows)){
            foreach($query->rows as $value){
                $points = unserialize($value['draw_info']);
                $count = count($points);
                $lng = 0;
                $lat = 0;
                foreach($points as $v){
                    $lng += $v[1];
                    $lat += $v[0];
                }
                $result[] = array(
                    'name' => $value['name'],
                    'area_id' => $value['area_id'],
                    'draw_info' => unserialize($value['draw_info']),
                    'center' => array(
                        round($lng/$count,6),
                        round($lat/$count,6),
                    ),
                );
            }
        }

        return $result;
    }

    public function deleteDrawToArea($area_id){
        if($area_id > 0){
            $sql = "update oc_x_area set draw_info = null where area_id = '".(int) $area_id."'";

            return $this->db->query($sql);
        }
        return false;
    }
}