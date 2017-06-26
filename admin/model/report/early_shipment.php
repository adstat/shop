<?php
class ModelReportEarlyShipment extends Model {
    public function getEarlyShipment($filter_data){

        $sql ="select AA.order_id ,date(AA.date_added) AS  date_added,A.name ,AA.product_id,AA.product_name ,
if(P.instock=0,'外仓','') AS instock,if(P.repack=1,'散发','')  AS  repack,AA.order_qty ,round(AA.order_total,2)  AS  order_total,
ST.quantity ,round(if(CC.deliver_missing_qty is null, 0, CC.deliver_missing_qty),2) AS  deliver_missing_qty,
round(if(CC.deliver_missing_total is null, 0, CC.deliver_missing_total),2) AS  deliver_missing_total,
ST.added_by,LA.logistic_driver_title,LA.logistic_driver_phone,CC.adduser

from(
     -- 销售数量
    select
    O.order_id,
    O.customer_id,
    OP.product_id,
    O.bd_id,
    O.date_added,

  O.deliver_date,
    OP.name product_name,
    sum(OP.quantity) order_qty,
    sum(OP.total) order_total

  from oc_order O
    left join xsjb2b.oc_order_product OP on O.order_id = OP.order_id

 where O.deliver_date between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."'
    and O.station_id = 2
    and O.order_status_id not in (3)

  group by O.order_id, OP.product_id
) AA
inner join (
    select
    O.order_id,
    B.product_id,
    A.date_added,
    A.return_reason_id,
  concat(U.lastname, U.firstname) adduser,
    sum(if(A.return_reason_id=3,B.quantity/B.box_quantity,0))
deliver_missing_qty,
    sum(if(A.return_reason_id=3,B.total,0)) deliver_missing_total

 from oc_order O
    left join oc_return A on O.order_id = A.order_id

 left join oc_return_product B on A.return_id = B.return_id

 left join oc_user U on A.add_user = U.user_id

 where   A.return_status_id ='".$filter_data['filter_return_id']."' and O.deliver_date between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."'

group by O.order_id, B.product_id
) CC on  AA.order_id = CC.order_id and AA.product_id = CC.product_id

left join oc_product P on AA.product_id = P.product_id

left join oc_customer CU on AA.customer_id = CU.customer_id

left join oc_x_area A on CU.area_id = A.area_id

left join oc_x_inventory_order_sorting ST on AA.order_id = ST.order_id and AA.product_id = ST.product_id

left join oc_x_logistic_allot_order LAO on AA.order_id = LAO.order_id

left join oc_x_logistic_allot LA on LAO.logistic_allot_id = LA.logistic_allot_id  WHERE 1=1 ";


        if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
            $sql .= " and date(AA.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }
        if($filter_data['filter_instock_id']!=2 ){
            $sql .=" and P.instock = '".$filter_data['filter_instock_id']."' ";
        }
        if($filter_data['filter_repack_id'] !=2){
            $sql .=" and P.repack = '".$filter_data['filter_repack_id']."' ";
        }
        if($filter_data['filter_logistic_list']){
            $sql .=" and LA.logistic_driver_id = '".$filter_data['filter_logistic_list']."' ";
        }

        if($filter_data['filter_return_id']){
            $sql .=" and CC.return_reason_id = '".$filter_data['filter_return_id']."' ";
        }

        $query = $this->db->query($sql);
        return $query->rows;

    }


    public  function getReturnList(){
        $sql = "SELECT return_reason_id ,name FROM  oc_return_reason ";

        $query = $this->db->query($sql);
        return $query->rows;
    }
}