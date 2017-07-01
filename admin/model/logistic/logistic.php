<?php

class ModelLogisticLogistic extends Model
{

    public function add($targetTable, $rowData)
    {
        $sql = "INSERT INTO " . $targetTable . " SET ";
        foreach ($rowData as $k => $v) {
            $sql .= $k . "='" . $v . "',";
        }
        $sql .= 'date_added=NOW()';
        $this->db->query($sql);
        return $this->db->getLastId();

    }

    public function edit($targetTable, $rowData, $indexFilter)
    {
        $sql = "UPDATE " . $targetTable . " SET ";
        foreach ($rowData as $k => $v) {
            $sql .= $k . "='" . $v . "',";
        }
        $sql .= " date_modified=NOW()";
        $sql .= " WHERE " . $indexFilter['field'] . " = '" . $indexFilter['value'] . "'";

        //return $sql;
        return $this->db->query($sql);
    }

    public function addHistory($targetTable, $historyTable, $historyFields, $indexFilter, $operator)
    {
        $sql = "INSERT INTO " . $historyTable . " (" . implode(',', $historyFields) . ",status,date_added,added_by)
        SELECT " . implode(',', $historyFields) . ",status,NOW(),'" . $operator . "'
        FROM " . $targetTable . "
        WHERE " . $indexFilter['field'] . " = '" . $indexFilter['value'] . "'";


        return $this->db->query($sql);
    }

    public function getLogisticLine($id)
    {
        $sql = "SELECT A.logistic_line_id, A.logistic_line_title, if(B.logistic_driver_id is null, '', B.logistic_driver_id) logistic_driver_id, if(B.logistic_driver_title is null, '', B.logistic_driver_title) logistic_driver_title, A.status, A.date_added FROM oc_x_logistic_line A
         LEFT JOIN oc_x_logistic_driver B on A.default_logistic_driver_id = B.logistic_driver_id";
        if ($id) {
            $sql .= " WHERE A.logistic_line_id = '" . (int)$id . "'";
        }
        $sql .= " ORDER BY A.status DESC, A.logistic_line_id DESC";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getLogisticDriver($id)
    {
        $sql = "SELECT A.logistic_driver_id, A.logistic_driver_title, A.logistic_driver_phone,
        if(B.logistic_van_id is null, '', B.logistic_van_id) logistic_van_id,
        if(B.logistic_van_title is null, '', B.logistic_van_title) logistic_van_title,
        if(B.model is null, '', B.model) model,
        A.status, A.date_added FROM oc_x_logistic_driver A
         LEFT JOIN oc_x_logistic_van B on A.default_logistic_van_id = B.logistic_van_id";
        if ($id) {
            $sql .= " WHERE A.logistic_driver_id = '" . (int)$id . "'";
        }
        $sql .= " ORDER BY A.status DESC, A.logistic_driver_id DESC";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getLogisticVan($id)
    {
        $sql =
            "SELECT logistic_van_id, logistic_van_title, model, capacity, payload, ownership, owner, contact, status, date_added FROM oc_x_logistic_van ";
        if ($id) {
            $sql .= " WHERE logistic_van_id = '" . (int)$id . "'";
        }
        $sql .= " ORDER BY status DESC, logistic_van_id DESC";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getLogisticDeliveryman($id)
    {
        $sql =
            "SELECT logistic_deliveryman_id, logistic_deliveryman_title, logistic_deliveryman_phone, status, date_added FROM oc_x_logistic_deliveryman ";
        if ($id) {
            $sql .= " WHERE logistic_deliveryman_id = '" . (int)$id . "'";
        }
        $sql .= " ORDER BY status DESC, logistic_deliveryman_id DESC";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getLineData($date='')
    {
        $sql_data ='';
        if(!empty($date)){
            $sql_data = ' and o.deliver_date =\''.$date.'\'';
        }
        $data = [];
        $sql =
            'select logistic_line_id , logistic_line_title , default_logistic_driver_id  from oc_x_logistic_line where status=1';
        $data['line'] = $this->db->query($sql)->rows;
        foreach($data['line'] as &$line){
            $sql = 'select count(*) order_total
                    from oc_x_logistic_allot la
                    inner join oc_x_logistic_allot_order lao on la.logistic_allot_id=lao.logistic_allot_id
                    inner join oc_order o on o.order_id = lao.order_id
                    where la.logistic_line_id='.$line['logistic_line_id'];
            $sql .= $sql_data;
            $count = $this->db->query($sql)->row;
            if(!empty($count)){
                $line['count']  = $count['order_total'];
            }
        }

        $sql =
            'select logistic_driver_id , logistic_driver_title ,default_logistic_van_id, logistic_driver_phone from oc_x_logistic_driver where status = 1 ';
        $data['driver'] = $this->db->query($sql)->rows;

        foreach($data['driver'] as &$driver){
            $sql = 'select count(*) order_total
                from oc_x_logistic_allot la
                inner join oc_x_logistic_allot_order lao on la.logistic_allot_id=lao.logistic_allot_id
                inner join oc_order o on o.order_id = lao.order_id
                where la.logistic_driver_id='.$driver['logistic_driver_id'];
            $sql .= $sql_data;
            $count = $this->db->query($sql)->row;
            if(!empty($count)){
                $driver['count'] = $count['order_total'];
            }
        }


        $sql = 'select logistic_van_id , logistic_van_title , model , capacity , payload  from oc_x_logistic_van where status = 1 ';
        $data['van'] = $this->db->query($sql)->rows;

        $sql =
            'select logistic_deliveryman_id , logistic_deliveryman_title, logistic_deliveryman_phone  from oc_x_logistic_deliveryman where status = 1 ';
        $data['deliveryman'] = $this->db->query($sql)->rows;

        return $data;

    }


    public function getLineDatas(){
        $sql = 'select * from oc_x_logistic_line where status =1';
        return $this->db->query($sql)->rows;
    }
    public function getLogisticList(){
        $sql = "SELECT
  ld.logistic_driver_id,
  ld.logistic_driver_title ,
  b.date
FROM
  oc_x_logistic_driver ld
  LEFT JOIN
    (SELECT
      la.logistic_driver_id,
      MAX(la.deliver_date)  AS DATE
    FROM
      oc_x_logistic_allot la
    GROUP BY la.logistic_driver_id
    ) b  ON ld.`logistic_driver_id` = b.logistic_driver_id
    ORDER BY b.date  DESC
    ";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getOrderStauts(){
        $sql = "SELECT  order_status_id ,name   FROM  oc_order_status WHERE order_status_id !=3";
        return $this->db->query($sql)->rows;
    }

    public function getSlotList(){
        $sql = 'select * from oc_deliver_slot where status =1';
        return $this->db->query($sql)->rows;
    }

    public function getAllotOrder($date, $station_id,$order_status_id, $classify,$deliver_slot_id, $logistic_index=0)
    {

        if ($classify == 1) {

            $sql =
                'select DISTINCT(o.order_id), o.station_id, lo.logistic_index, oe.firstorder, oe.firstorder_fm, o.shipping_address_1 , o.shipping_city , o.shipping_firstname , o.shipping_phone , o.comment ,os.name,xa.name AS area_name,o.order_deliver_status_id,lao.order_id allotted_order_id
                from oc_order o
                LEFT JOIN  oc_x_logistic_order_index lo ON  o.order_id = lo.order_id
                LEFT JOIN  oc_customer oc ON  o.customer_id = oc.customer_id
                LEFT JOIN  oc_x_area  xa  ON  oc.area_id = xa.area_id
                left join oc_order_extend oe on o.order_id = oe.order_id
                LEFT JOIN  oc_order_status os ON  o.order_status_id = os.order_status_id
                left join oc_x_logistic_allot_order lao on lao.order_id = o.order_id where o.order_status_id !=3 and  lao.order_id is null  and o.order_deliver_status_id = 1 and o.station_id=' .
                $station_id . '  and o.deliver_date > "2016-01-01" and o.deliver_date =\'' . $date . '\'';

            if(!empty($deliver_slot_id)){
                $sql .=' and o.deliver_slot_id='.$deliver_slot_id;
            }
            if(!empty($order_status_id)){
                $sql .=' and o.order_status_id ='.$order_status_id;
            }

            if($logistic_index){
                $sql .=' and lo.logistic_index='.$logistic_index;
            }

            $sql .= " order by o.order_status_id ,o.shipping_address_1 asc";

            $classify2 = $this->db->query($sql)->rows;
            foreach ($classify2 as &$value) {
                if(!$value['allotted_order_id'] and ($value['order_deliver_status_id'] ==1)){
                    $value['classify_type'] = 3;
                }
            }

            return $classify2;

        } elseif ($classify == 2) {
            //已分配
            $sql =
                'select DISTINCT(o.order_id), o.station_id, lo.logistic_index, oe.firstorder, oe.firstorder_fm, o.shipping_address_1 , o.shipping_city , o.shipping_firstname , o.shipping_phone , o.comment ,os.name,xa.name AS area_name,o.order_deliver_status_id,lao.order_id allotted_order_id
                from oc_order o
                  LEFT JOIN  oc_x_logistic_order_index lo ON  o.order_id = lo.order_id
                  LEFT JOIN  oc_customer oc ON  o.customer_id = oc.customer_id
                LEFT JOIN  oc_x_area  xa  ON  oc.area_id = xa.area_id
                left join oc_order_extend oe on o.order_id = oe.order_id
                LEFT JOIN  oc_order_status os ON  o.order_status_id = os.order_status_id
                inner join oc_x_logistic_allot_order lao on lao.order_id = o.order_id where o.order_status_id !=3 and  o.order_deliver_status_id in (1,2) and o.station_id=' .
                $station_id . ' and  o.deliver_date > "2016-01-01" and o.deliver_date =\'' . $date . '\'';

            if(!empty($deliver_slot_id)){
                $sql .=' and o.deliver_slot_id='.$deliver_slot_id;
            }
            if(!empty($order_status_id)){
                $sql .=' and o.order_status_id ='.$order_status_id;
            }
            if($logistic_index){
                $sql .=' and lo.logistic_index='.$logistic_index;
            }

            $sql .= " order by o.order_status_id ,o.shipping_address_1 asc";

            $classify2 = $this->db->query($sql)->rows;
            foreach ($classify2 as &$value) {
                if($value['allotted_order_id'] and ($value['order_deliver_status_id'] ==1 or$value['order_deliver_status_id'] ==2 )){
                    $value['classify_type'] = 1;
                }
                if($value['allotted_order_id'] and $value['order_deliver_status_id'] ==11){
                    $value['classify_type'] = 2;
                }
                if(!$value['allotted_order_id'] and $value['order_deliver_status_id'] ==1){
                    $value['classify_type'] = 3;
                }
            }

            return $classify2;
        } elseif ($classify == 3) {

            //重新分配
            $sql =
                'select DISTINCT(o.order_id), o.station_id, lo.logistic_index, oe.firstorder, oe.firstorder_fm, o.shipping_address_1 , o.shipping_city , o.shipping_firstname , o.shipping_phone , o.comment ,os.name,xa.name AS area_name,o.order_deliver_status_id,lao.order_id allotted_order_id
                from oc_order o
                  LEFT JOIN  oc_x_logistic_order_index lo ON  o.order_id = lo.order_id
                  LEFT JOIN  oc_customer oc ON  o.customer_id = oc.customer_id
                LEFT JOIN  oc_x_area  xa  ON  oc.area_id = xa.area_id
                left join oc_order_extend oe on o.order_id = oe.order_id
                LEFT JOIN  oc_order_status os ON  o.order_status_id = os.order_status_id
                inner join oc_x_logistic_allot_order lao on lao.order_id = o.order_id where o.order_status_id !=3 and  o.order_deliver_status_id =11 and o.station_id=' .
                $station_id . ' and  o.deliver_date > "2016-01-01" and o.deliver_date =\'' . $date . '\'';

            if(!empty($deliver_slot_id)){
                $sql .=' and o.deliver_slot_id='.$deliver_slot_id;
            }
            if(!empty($order_status_id)){
                $sql .=' and o.order_status_id ='.$order_status_id;
            }
            if($logistic_index){
                $sql .=' and lo.logistic_index='.$logistic_index;
            }

            $sql .= " order by o.order_status_id ,o.shipping_address_1 asc";

            $classify2 = $this->db->query($sql)->rows;
            foreach ($classify2 as &$value) {
                if($value['allotted_order_id'] and $value['order_deliver_status_id'] ==11){
                    $value['classify_type'] = 2;
                }
            }

            return $classify2;

        } else {
            $sql =
                'select DISTINCT(o.order_id), lao.order_id allotted_order_id, lo.logistic_index, oe.firstorder, oe.firstorder_fm, o.station_id, o.shipping_address_1, o.shipping_city, o.shipping_firstname , o.shipping_phone , o.comment ,os.name,xa.name AS area_name,o.order_deliver_status_id
                from oc_order o
                  LEFT JOIN  oc_x_logistic_order_index lo ON  o.order_id = lo.order_id
                  LEFT JOIN  oc_customer oc ON  o.customer_id = oc.customer_id
                LEFT JOIN  oc_x_area  xa  ON  oc.area_id = xa.area_id
                left join oc_order_extend oe on o.order_id = oe.order_id
                LEFT JOIN  oc_order_status os ON  o.order_status_id = os.order_status_id
                left join oc_x_logistic_allot_order lao on lao.order_id = o.order_id
                where o.order_status_id !=3 and o.order_deliver_status_id in (1,2,11) and o.station_id=' .
                $station_id . '  and o.deliver_date > "2016-01-01" and o.deliver_date =\'' . $date . '\'';

            if(!empty($deliver_slot_id)){
                $sql .=' and o.deliver_slot_id='.$deliver_slot_id;
            }
            if(!empty($order_status_id)){
                $sql .=' and o.order_status_id ='.$order_status_id;
            }

            if($logistic_index){
                $sql .=' and lo.logistic_index='.$logistic_index;
            }

            $sql .= " order by o.order_status_id , o.shipping_address_1 asc";



            $classify2 = $this->db->query($sql)->rows;

            foreach ($classify2 as &$value) {
                if( ($value['order_deliver_status_id'] == 1 or $value['order_deliver_status_id'] ==2 )  and $value['allotted_order_id']  ){
                    $value['classify_type'] = 1;
                }
                if($value['allotted_order_id'] and $value['order_deliver_status_id'] ==11){
                    $value['classify_type'] = 2;
                }
                if(!$value['allotted_order_id'] and $value['order_deliver_status_id'] ==1){
                    $value['classify_type'] = 3;
                }
            }
            return $classify2;
            //return array_merge($classify1, $classify2);
        }
    }

    public function getOrderInfo($id)
    {
        $sql = 'select  station_id ,deliver_date from oc_order where order_id=' . $id;
        return $this->db->query($sql)->row;
    }

    public function getLineInfo($id)
    {
        $sql = 'select logistic_line_title  from oc_x_logistic_line where logistic_line_id=' . $id;
        return $this->db->query($sql)->row;
    }

    public function getDriverInfo($id)
    {
        $sql =
            'select logistic_driver_title, logistic_driver_phone  from oc_x_logistic_driver where logistic_driver_id=' .
            $id;
        return $this->db->query($sql)->row;
    }

    public function getVanInfo($id)
    {
        $sql = 'select logistic_van_title  from oc_x_logistic_van where logistic_van_id=' . $id;
        return $this->db->query($sql)->row;
    }

    public function getDeliverymanInfo($id)
    {
        $sql =
            'select logistic_deliveryman_title, logistic_deliveryman_phone from oc_x_logistic_deliveryman where logistic_deliveryman_id=' .
            $id;
        return $this->db->query($sql)->row;
    }

    public function addAllotOrder($arr)
    {
        $data = '';
        foreach ($arr as $key => $value) {
            $data .= " $key=$value,";
        }

        $data = substr($data, 0, -1);

        $order_id =  explode(',',$data);
        $order = explode('=',$order_id[0]);

        $sql1 = " update oc_order  set order_deliver_status_id = 1 WHERE order_id = '".$order[1] ."' ";

        $this->db->query($sql1);

        $sql = 'insert into oc_x_logistic_allot_order set ' . $data;

        $this->db->query($sql);
        return $this->db->getLastId();
    }

    public function updateLogisticIndex($orderLogisticIndex, $date, $station_id, $deliver_slot_id ){

        if(sizeof($orderLogisticIndex)){
            foreach($orderLogisticIndex as $key=>$val){

                if($val >=0){
                    $sql = "select COUNT(*) as num  from  oc_x_logistic_order_index WHERE order_id = '".$key."'";
                    $query=$this->db->query($sql);
                    $result=$query->row;

                    if($result['num'] > 0){
                        $this->db->query("update oc_x_logistic_order_index set logistic_index = '".$val."' where order_id = '".$key."' ");
                    }else{
                        $this->db->query("insert into oc_x_logistic_order_index (`order_id`,`station_id`,`deliver_date`,`logistic_index`) VALUES ('".$key."','".$station_id."',NOW(),'".$val."')");
                    }

                }
            }
        }


        if($date && $station_id && $deliver_slot_id){
            $sql = "select o.logistic_index, count(A.order_id) orders
            from oc_x_logistic_order_index o
            LEFT JOIN oc_order A ON o.order_id = A.order_id
            where A.order_status_id in (2,5,6,8) and A.order_deliver_status_id in (1,2)
            and A.deliver_date = '".$date."' and A.station_id = '".$station_id."' and A.deliver_slot_id = '".$deliver_slot_id."'
            group by o.logistic_index";

            return $this->db->query($sql)->rows;
        }

        return array();
    }

    public function getOrderInv($id)
    {

        $sql = 'select * from oc_order_inv where order_id=' . $id;

        return $this->db->query($sql)->row;
    }

    public function getOrderQuantity($id)
    {
        $sql = 'select quantity from oc_order_product where order_id=' . $id;
        return $this->db->query($sql)->rows;
    }

    public function getAllotInfo($where = [])
    {

        if (empty($where)) {
            $sql =
                'select lao.order_id, la.deliver_date , la.logistic_line_title , la.logistic_driver_title, la.logistic_driver_phone , la.logistic_van_title, la.logistic_deliveryman_title, la.logistic_deliveryman_phone ,lao.logistic_allot_order_id, lao.logistic_allot_id  , o.shipping_address_1 , o.shipping_city , o.station_id  , o.shipping_firstname , o.shipping_phone , o.comment  , bd.bd_name  , bd.phone , o.total ,la.logistic_allot_idfrom oc_x_logistic_allot_order lao inner join oc_x_logistic_allot la on lao.logistic_allot_id= la.logistic_allot_id  inner join oc_order o on o.order_id=lao.order_id inner join oc_x_bd bd on o.bd_id=bd.bd_id  order by la.date_added ';
        } else {

            $whereCond = "where 1=1 and  o.order_status_id != 3 ";
            $whereCond .= isset($where['order_id']) ? " and lao.order_id='".$where['order_id']."'" : "";
            $whereCond .= isset($where['logistic_line_id']) ? " and la.logistic_line_id='".$where['logistic_line_id']."'" : "";
            $whereCond .= isset($where['logistic_driver_id']) ? " and la.logistic_driver_id='".$where['logistic_driver_id']."'" : "";

            $whereCond .= isset($where['station_id']) ? " and la.station_id='".$where['station_id']."'" : "";
            $whereCond .= isset($where['deliver_slot_id']) ? " and la.deliver_slot_id='".$where['deliver_slot_id']."'" : "";
            $whereCond .= isset($where['deliver_date']) ? " and la.deliver_date='".$where['deliver_date']."'" : "";

            $sql =
                'select lao.order_id order_id, la.deliver_date , la.logistic_line_title , la.logistic_driver_title, la.logistic_driver_phone , la.logistic_van_title, la.logistic_deliveryman_title, la.logistic_deliveryman_phone  ,lao.logistic_allot_order_id, lao.logistic_allot_id , o.shipping_address_1 , o.shipping_city , o.station_id , o.shipping_firstname , o.shipping_phone , o.comment , bd.bd_name  , bd.phone  , o.total,os.name AS status_name,o.order_status_id,la.logistic_allot_id
                from oc_x_logistic_allot la
                left join oc_x_logistic_allot_order lao on la.logistic_allot_id= lao.logistic_allot_id
                left join oc_order o on lao.order_id=o.order_id
                left JOIN oc_order_status os ON o.order_status_id = os.order_status_id
                left join oc_x_bd bd on o.bd_id=bd.bd_id '.
                $whereCond . ' order by lao.logistic_allot_id , la.date_added ';
        }

        return $this->db->query($sql)->rows;
    }

    public function del($logistic_allot_order_id, $logistic_allot_id)
    {
        $sql = 'delete from oc_x_logistic_allot_order where logistic_allot_order_id=' . $logistic_allot_order_id;
        $this->db->query($sql);

        $sql = "select count(order_id) cont from oc_x_logistic_allot_order where logistic_allot_id = '".$logistic_allot_id."'";
        $checkData = $this->db->query($sql)->row;

        //无订单则删除分派记录
        if($checkData['cont'] == 0){
            $sql = 'delete from oc_x_logistic_allot where logistic_allot_id=' . $logistic_allot_id;
            $this->db->query($sql);
        }
    }


    public function getSumNum($where = [])
    {

        if (empty($where)) {
            $sql = "select select sum(A.frame_count),sum(A.frame_meat_count),sum(A.incubator_count),sum(A.incubator_mi_count),sum(A.foam_count),sum(A.foam_ice_count),sum(A.frame_mi_count),sum(A.frame_ice_count),sum(A.box_count) from oc_order_inv A INNER JOIN  oc_x_logistic_allot_order lao ON A.order_id = lao.order_id inner join oc_x_logistic_allot la on lao.logistic_allot_id= la.logistic_allot_id  inner join oc_order o on o.order_id=lao.order_id inner join oc_x_bd bd on o.bd_id=bd.bd_id where o.order_status_id !=3 order by la.date_added";
        } else {
            $whereCond = "where o.order_status_id !=3 ";
            $whereCond .= isset($where['order_id']) ? " and lao.order_id='" . $where['order_id'] . "'" : "";
            $whereCond .= isset($where['logistic_line_id']) ? " and la.logistic_line_id='" . $where['logistic_line_id'] . "'" : "";
            $whereCond .= isset($where['logistic_driver_id']) ? " and la.logistic_driver_id='" . $where['logistic_driver_id'] . "'" : "";
            $whereCond .= isset($where['logistic_van_id']) ? " and la.logistic_van_id='" . $where['logistic_van_id'] . "'" : "";
            $whereCond .= isset($where['station_id']) ? " and la.station_id='" . $where['station_id'] . "'" : "";
            $whereCond .= isset($where['deliver_slot_id']) ? " and la.deliver_slot_id='" . $where['deliver_slot_id'] . "'" : "";
            $whereCond .= isset($where['deliver_date']) ? " and la.deliver_date='" . $where['deliver_date'] . "'" : "";

            $sql =
                'select sum(A.frame_count),sum(A.frame_meat_count),sum(A.incubator_count),sum(A.incubator_mi_count),sum(A.foam_count),sum(A.foam_ice_count),sum(A.frame_mi_count),sum(A.frame_ice_count),sum(A.box_count)
                from oc_order_inv A LEFT JOIN   oc_x_logistic_allot_order lao ON A.order_id = lao.order_id
                left join  oc_x_logistic_allot la on la.logistic_allot_id= lao.logistic_allot_id
                left join oc_order o on lao.order_id=o.order_id
                left join oc_x_bd bd on o.bd_id=bd.bd_id ' .
                $whereCond . ' order by la.date_added ';
        }
        return $this->db->query($sql)->rows;
    }
    public function  getSumNotpicking($where=[]){
        if(empty($where)){
            $sql = "SELECT sum(A.quantity) FROM oc_order_product A INNER JOIN  oc_x_logistic_allot_order lao ON A.order_id = lao.order_id inner join oc_x_logistic_allot la on lao.logistic_allot_id= la.logistic_allot_id  inner join oc_order o on o.order_id=lao.order_id inner join oc_x_bd bd on o.bd_id=bd.bd_id  order by la.date_added";
        }else{

            $whereCond = "where 1=1 ";
            $whereCond .= isset($where['order_id']) ? " and lao.order_id='" . $where['order_id'] . "'" : "";
            $whereCond .= isset($where['logistic_line_id']) ? " and la.logistic_line_id='" . $where['logistic_line_id'] . "'" : "";
            $whereCond .= isset($where['logistic_driver_id']) ? " and la.logistic_driver_id='" . $where['logistic_driver_id'] . "'" : "";
            $whereCond .= isset($where['logistic_van_id']) ? " and la.logistic_van_id='" . $where['logistic_van_id'] . "'" : "";
            $whereCond .= isset($where['station_id']) ? " and la.station_id='" . $where['station_id'] . "'" : "";
            $whereCond .= isset($where['deliver_slot_id']) ? " and la.deliver_slot_id='" . $where['deliver_slot_id'] . "'" : "";
            $whereCond .= isset($where['deliver_date']) ? " and la.deliver_date='" . $where['deliver_date'] . "'" : "";

            $sql = 'SELECT sum(A.quantity) FROM oc_order_product A INNER JOIN  oc_x_logistic_allot_order lao ON A.order_id = lao.order_id inner join oc_x_logistic_allot la on lao.logistic_allot_id= la.logistic_allot_id  inner join oc_order o on o.order_id=lao.order_id inner join oc_x_bd bd on o.bd_id=bd.bd_id ' . $whereCond . ' order by la.date_added ';

        }
        return $this->db->query($sql)->rows;
    }


    public function getUnTable($where = []){

        if(empty($where)){

            $sql = "SELECT  O.order_id ,O.shipping_address_1 , O.shipping_city , O.station_id , O.shipping_firstname , O.shipping_phone , O.comment , BD.bd_name  , BD.phone  , O.total ,O.deliver_date FROM oc_order O LEFT JOIN  oc_x_bd BD ON O.bd_id = BD.bd_id  WHERE (SELECT COUNT(1)AS  num FROM oc_x_logistic_allot_order B  WHERE O.order_status_id !=3 AND O.order_id = B.order_id) = 0 ";

        }else{

            $whereCond = "where 1=1  ";
            $whereCond = isset($where['order_id']) ? " and O.order_id='".$where['order_id']."'" : "";

            $whereCond .= isset($where['station_id']) ? " and O.station_id='".$where['station_id']."'" : "";
         ;
       $date= isset($where['deliver_date']) ? "  DATE(O.deliver_date)='".$where['deliver_date']."'" : "";


            $sql = "SELECT  O.order_id ,O.shipping_address_1 , O.shipping_city , O.station_id , O.order_status_id,O.shipping_firstname , O.shipping_phone , O.comment , BD.bd_name  , BD.phone  , O.total ,O.deliver_date ,os.name AS status_name ,O.order_status_id FROM oc_order O left JOIN oc_order_status os ON O.order_status_id = os.order_status_id
LEFT JOIN  oc_x_bd BD ON O.bd_id = BD.bd_id  WHERE $date AND (SELECT COUNT(1)AS  num FROM oc_x_logistic_allot_order B  WHERE  O.order_id = B.order_id  ) = 0   $whereCond  and O.order_status_id !=3";

        }

        return $this->db->query($sql)->rows;

    }
    public function getsumnums($logistic_driver_id,$deliver_date,$line_id){
        $sql = " select sum(A.frame_count),sum(A.frame_meat_count),sum(A.incubator_count),sum(A.incubator_mi_count),sum(A.foam_count),sum(A.foam_ice_count),sum(A.frame_mi_count),sum(A.frame_ice_count),sum(A.box_count) from oc_order_inv A INNER JOIN  oc_x_logistic_allot_order lao ON A.order_id = lao.order_id inner join oc_x_logistic_allot la on lao.logistic_allot_id= la.logistic_allot_id   where  la.deliver_date = '". $deliver_date  ."'";
        if($logistic_driver_id){
            $sql .= " and la.logistic_driver_id = '". $logistic_driver_id ."'";
        }
        if($line_id){
            $sql .= " and la.logistic_line_id = '". $line_id ."'";
        }

        return $this->db->query($sql)->rows;
    }

    public function getLogisticAllotId($date,$line_id,$driver_id,$station_id){
        $sql = "select DISTINCT(A.logistic_allot_id) from oc_x_logistic_allot_order A left join oc_x_logistic_allot B ON A.logistic_allot_id = B.logistic_allot_id WHERE B.deliver_date = '" .$date ."' and B.logistic_line_id = '".$line_id."' and logistic_driver_id = $driver_id and B.station_id = '".$station_id ."' ";

        return $this->db->query($sql)->rows;

    }

    public function getlogistics($logistic_allot_id){
        $sql =
            "select lao.order_id order_id, la.deliver_date , la.logistic_line_title , la.logistic_driver_title, la.logistic_driver_phone , la.logistic_van_title, la.logistic_deliveryman_title, la.logistic_deliveryman_phone  ,lao.logistic_allot_order_id, lao.logistic_allot_id , o.shipping_address_1 , o.shipping_city , o.station_id , o.shipping_firstname , o.shipping_phone , o.comment , bd.bd_name  , bd.phone  , o.total,os.name AS status_name,o.order_status_id,la.logistic_allot_id
                from oc_x_logistic_allot la
                left join oc_x_logistic_allot_order lao on la.logistic_allot_id= lao.logistic_allot_id
                left join oc_order o on lao.order_id=o.order_id
                left JOIN oc_order_status os ON o.order_status_id = os.order_status_id
                left join oc_x_bd bd on o.bd_id=bd.bd_id  WHERE o.order_status_id !=3 AND lao.logistic_allot_id = '".$logistic_allot_id ."'
               ";
        $sql .="order by lao.logistic_allot_id , la.date_added";
        return $this->db->query($sql)->rows;
    }

    public function getLogisticSumNum($logistic_allot_id){
        $sql =
            "select sum(A.frame_count),sum(A.frame_meat_count),sum(A.incubator_count),sum(A.incubator_mi_count),sum(A.foam_count),sum(A.foam_ice_count),sum(A.frame_mi_count),sum(A.frame_ice_count),sum(A.box_count)
                from oc_order_inv A LEFT JOIN   oc_x_logistic_allot_order lao ON A.order_id = lao.order_id
                left join  oc_x_logistic_allot la on la.logistic_allot_id= lao.logistic_allot_id
                left join oc_order o on lao.order_id=o.order_id
                left join oc_x_bd bd on o.bd_id=bd.bd_id WHERE la.logistic_allot_id = '" .
            $logistic_allot_id . "' order by la.date_added ";

        return $this->db->query($sql)->rows;
    }


    public function getLogisticSumNotpicking($logistic_allot_id){
        $sql = "SELECT sum(A.quantity) FROM oc_order_product A INNER JOIN  oc_x_logistic_allot_order lao ON A.order_id = lao.order_id inner join oc_x_logistic_allot la on lao.logistic_allot_id= la.logistic_allot_id  inner join oc_order o on o.order_id=lao.order_id inner join oc_x_bd bd on o.bd_id=bd.bd_id WHERE la.logistic_allot_id ='" . $logistic_allot_id ." ' order by la.date_added ";

        return $this->db->query($sql)->rows;
    }

}