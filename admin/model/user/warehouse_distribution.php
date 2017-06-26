<?php
class ModelUserWarehouseDistribution extends Model {
    public function distrStationList() {
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

    public function orderArea() {
        $sql = "select area_id id,concat(district, '->',name) name from oc_x_area";
        $query = $this->db->query($sql);

        return $query->rows;
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

    public function getProductTypeList(){
        $query = $this->db->query('select product_type_id , name from oc_product_type');
        $productRow = $query->rows;
        $productList = array();
        if(sizeof($productRow)){
            foreach($productRow as $value){
                $productList[$value["product_type_id"]] = $value;
            }
        }
        return $productList;
    }

    public function getArea(){
        $query = $this->db->query('select district from oc_x_area GROUP BY district');
        $areaRow = $query->rows;
        $areaList = array();
        if(sizeof($areaRow)){
            foreach($areaRow as $value){
                $areaList[$value["district"]] = $value;
            }
        }
        return $areaList;
    }

    public function orderStatus(){
        $query = $this->db->query("select order_status_id, name from oc_order_status");
        $statusRow = $query->rows;
        $statusList = array();
        if(sizeof($statusRow)){
            foreach($statusRow as $status){
                $statusList[$status['order_status_id']] = $status;
            }
        }

        return $statusList;
    }

    public function distrPersonList($station_id){
        $station_id = 0;
        $sql = "select user_id,username from oc_w_user where station_id = '".$station_id."' and status = 1";
        $query = $this->db->query($sql);
        $wUserRow = $query->rows;
        $wUserList = array();
        if(sizeof($wUserRow)){
            foreach($wUserRow as $user){
                $wUserList[$user['user_id']] = $user;
            }
        }

        return $wUserList;
    }


    public function getIfConfirmDistr($condition){


        $conditions = explode(",",$condition);

        $station_id = $conditions[0];
        $deliver_date = $conditions[1];
        $customer_group_id = $conditions[2];
        $order_status_id = $conditions[3];
        $order_area = $conditions[4];
        $product_type = $conditions[5];
        $area = $conditions[6];
        $fj_worker = $conditions[7];

        $sql = "SELECT DD.*,SSS.quantity
from(

SELECT O.`order_id` , PT.product_type_id , sum(OP.quantity) fj_quantity,concat(O.order_id,P.repack) flag_key, O.date_added,PT.name product_name,
CD.name customer_level,shipping_address_1 shipping_address,A.district,
 OD.inventory_name ,OD.w_user_id,O.customer_id ,C.customer_group_id,C.area_id
FROM oc_order O
LEFT JOIN oc_customer C ON O.`customer_id` = C.`customer_id`
LEFT JOIN oc_customer_group CG ON C.`customer_group_id` = CG.`customer_group_id`
LEFT JOIN oc_customer_group_description CD ON CD.customer_group_id = CG.customer_group_id
LEFT JOIN oc_order_product OP ON OP.`order_id` = O.`order_id`
LEFT JOIN oc_product P ON P.`product_id` = OP.`product_id`
LEFT JOIN oc_product_type PT ON PT.product_type_id = P.product_type
LEFT JOIN oc_x_area A ON A.area_id = C.area_id
LEFT JOIN oc_order_distr OD ON OD.order_id = O.order_id AND PT.product_type_id = OD.product_type_id
WHERE O.station_id = '". $station_id ."' AND O.deliver_date = '". $deliver_date ."'AND OD.`inventory_name` IS NOT NULL  GROUP BY O.`order_id` ,PT.product_type_id
)DD
left join (
select SS.order_id,sum(SS.quantity) quantity
from
(
	SELECT O.`order_id` , PT.product_type_id , sum(OP.quantity) quantity
	FROM oc_order O
	LEFT JOIN oc_customer C ON O.`customer_id` = C.`customer_id`
	LEFT JOIN oc_customer_group CG ON C.`customer_group_id` = CG.`customer_group_id`
	LEFT JOIN oc_customer_group_description CD ON CD.customer_group_id = CG.customer_group_id
	LEFT JOIN oc_order_product OP ON OP.`order_id` = O.`order_id`
	LEFT JOIN oc_product P ON P.`product_id` = OP.`product_id`
	LEFT JOIN oc_product_type PT ON PT.product_type_id = P.product_type
	LEFT JOIN oc_x_area A ON A.area_id = C.area_id
	LEFT JOIN oc_order_distr OD ON OD.order_id = O.order_id AND PT.product_type_id = OD.product_type_id
	WHERE O.station_id =  '". $station_id ."' AND O.deliver_date =  '". $deliver_date ."'AND OD.`inventory_name` IS NOT NULL  GROUP BY O.`order_id`
)SS group by SS.order_id

)SSS on SSS.order_id = DD.order_id
where 1 =1 ";

        if($customer_group_id){
            $sql .= " and DD.customer_group_id = '".$customer_group_id."'";
        }

        if($order_status_id){
            $sql .= " and DD.order_status_id = '". $order_status_id ."'";
        }
        if($order_area){
            $sql .= " and DD.area_id = '". $order_area ."'";
        }
        if($product_type >= 0){

            $sql .= " and DD.product_type_id = '". $product_type ."'";
        }
        if($area){
            $sql .= " and DD.district = '". $area ."'";
        }
        if($fj_worker){
            $sql .= "and DD.w_user_id = '". $fj_worker ."'";
        }
        $sql .= " GROUP BY DD.`order_id` ";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getDistrOrders($condition){

        $conditions = explode(",",$condition);

        $station_id = $conditions[0];
        $deliver_date = $conditions[1];
        $customer_group_id = $conditions[2];
        $order_status_id = $conditions[3];
        $order_area = $conditions[4];
        $product_type = $conditions[5];
        $area = $conditions[6];



        $sql = "SELECT DD.*,SSS.quantity
from(

SELECT O.`order_id` , PT.product_type_id , sum(OP.quantity) fj_quantity, concat(O.order_id,P.repack) flag_key , O.date_added,PT.name product_name,
CD.name customer_level,shipping_address_1 shipping_address,A.district,
 OD.inventory_name ,O.customer_id ,C.customer_group_id,C.area_id,O.order_status_id
FROM oc_order O
LEFT JOIN oc_customer C ON O.`customer_id` = C.`customer_id`
LEFT JOIN oc_customer_group CG ON C.`customer_group_id` = CG.`customer_group_id`
LEFT JOIN oc_customer_group_description CD ON CD.customer_group_id = CG.customer_group_id
LEFT JOIN oc_order_product OP ON OP.`order_id` = O.`order_id`
LEFT JOIN oc_product P ON P.`product_id` = OP.`product_id`
LEFT JOIN oc_product_type PT ON PT.product_type_id = P.product_type
LEFT JOIN oc_x_area A ON A.area_id = C.area_id
LEFT JOIN oc_order_distr OD ON OD.order_id = O.order_id AND PT.product_type_id = OD.product_type_id
WHERE O.station_id = '". $station_id ."' AND O.deliver_date = '". $deliver_date ."'AND OD.`inventory_name` IS NULL  GROUP BY O.`order_id` ,PT.product_type_id
)DD
left join (
select SS.order_id,sum(SS.quantity) quantity
from
(
	SELECT O.`order_id` , PT.product_type_id , sum(OP.quantity) quantity
	FROM oc_order O
	LEFT JOIN oc_customer C ON O.`customer_id` = C.`customer_id`
	LEFT JOIN oc_customer_group CG ON C.`customer_group_id` = CG.`customer_group_id`
	LEFT JOIN oc_customer_group_description CD ON CD.customer_group_id = CG.customer_group_id
	LEFT JOIN oc_order_product OP ON OP.`order_id` = O.`order_id`
	LEFT JOIN oc_product P ON P.`product_id` = OP.`product_id`
	LEFT JOIN oc_product_type PT ON PT.product_type_id = P.product_type
	LEFT JOIN oc_x_area A ON A.area_id = C.area_id
	LEFT JOIN oc_order_distr OD ON OD.order_id = O.order_id AND PT.product_type_id = OD.product_type_id
	WHERE O.station_id =  '". $station_id ."' AND O.deliver_date =  '". $deliver_date ."'AND OD.`inventory_name` IS NULL  GROUP BY O.`order_id`
)SS group by SS.order_id

)SSS on SSS.order_id = DD.order_id
where 1 =1 ";

        if($customer_group_id){
            $sql .= " and DD.customer_group_id = '".$customer_group_id."'";
        }

        if($order_status_id){
            $sql .= " and DD.order_status_id = '". $order_status_id ."'";
        }
        if($order_area){
            $sql .= " and DD.area_id = '". $order_area ."'";
        }
        if($product_type){

            $sql .= " and DD.product_type_id = '". $product_type ."'";
        }
        if($area){
            $sql .= " and DD.district = '". $area ."'";
        }
        $sql .= " GROUP BY DD.`order_id` ";
        $query = $this->db->query($sql);

        return $query->rows;

    }



    public function distrOrderToWoker($order_id,$w_user_id,$inventory_name,$quantity,$product_type_id){
        $return = array(
            'distred' => false,
        );
        //判断该订单某种分拣类型是否已经分配
        $sql = "select inventory_name worker from oc_order_distr where order_id = '".$order_id."' and product_type_id='".$product_type_id."'";

        $query = $this->db->query($sql);

        if(sizeof($query->rows)>0){
            $return['distred'] = true;
        }

        if(!$return['distred'] && $w_user_id > 0){
            $sql = "insert into oc_order_distr (`order_id`,`w_user_id`,`inventory_name`,`quantity`,`product_type_id`)
                values('".$order_id."','".$w_user_id."','".$inventory_name."','".$quantity."','".$product_type_id."')";

            $return = $this->db->query($sql);

        }
        return $return;
    }

    public function redistrOrderToWoker($order_id,$product_type_id){
        $sql = "delete from oc_order_distr where order_id = '".$order_id."' and product_type_id='".$product_type_id."'";

        $insert_flag = $this->db->query($sql);

        return $insert_flag;
    }
}