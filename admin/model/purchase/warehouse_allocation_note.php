<?php
class  ModelPurchaseWarehouseAllocationNote  extends Model {
    public function getWarehouse(){
        $sql = "select * from oc_x_warehouse";
        $query = $this->db->query($sql);
        $rows = $query->rows;
        return $rows;
    }
    public function  getWarehouse_StatusId(){
    $sql = "select * from oc_x_warehouse_requisition_status";
        $query = $this->db->query($sql);
        $rows = $query->rows;
        return $rows;
}

    public function getProducts($data = array(),$filter_warehouse_id_global ){
        $sql = "SELECT   ptw.product_id ,p.name ,ptw.status ,w.title FROM oc_product_to_warehouse ptw LEFT JOIN oc_product p ON ptw.product_id = p.product_id  LEFT JOIN  oc_x_warehouse w ON  ptw.warehouse_id = w.warehouse_id WHERE 1=1 ";
        if($filter_warehouse_id_global){
            $sql .= "  and ptw.warehouse_id = '".$filter_warehouse_id_global ."' ";
        }

        $implode = array();
        if(!empty($data['filter_name'])){
            //商品名字
            if(preg_match('/\D/', $data['filter_name']) == 1){
                $implode[] = '( p.name LIKE \'%' . $this->db->escape($data['filter_name']) . '%\')';
            }else{
                $implode[] = '( p.name LIKE \'%' . $this->db->escape($data['filter_name']) . '%\' OR             ptw.product_id=' . $this->db->escape($data['filter_name']) . ')';

            }

            if ($implode) {
                $sql .= " AND " . implode(" AND ", $implode);

            }

        }

        $query = $this->db->query($sql);
        $rows = $query->rows;
        return $rows;
    }

    public function getProductInfo($product_id,$filter_warehouse_id_global){
        $sql = "  select pi.product_id ,p.name,ps.product_section_title,pi.inventory,p.price
   from oc_product_inventory pi
  LEFT JOIN oc_product_section ps ON pi.product_id = ps.product_id and pi.warehouse_id = ps.warehouse_id
   LEFT JOIN  oc_product p ON  pi.product_id = p.product_id  WHERE pi.product_id = '".$product_id."' and pi.warehouse_id = '".$filter_warehouse_id_global."'";

        $query = $this->db->query($sql);
        $rows = $query->rows;
        return $rows;
    }
    public function submitAdjust($products,$filter_warehouse_id_global,$to_warehouse_id,$deliver_date,$userId,$reasons,$outtype){

        if($products){

                $sql = "insert into oc_x_warehouse_requisition (`from_warehouse`,`to_warehouse`,`added_by`,`date_added` , `deliver_date`,`out_type`,`comment` )  VALUES  ('". $filter_warehouse_id_global ."','". $to_warehouse_id ."','".$userId ."' , NOW(),'".$deliver_date."','".$outtype."','".$reasons."')";
                $query = $this->db->query($sql);
                $lastId =  $this->db->getLastId();

                if($lastId){
                    foreach($products as $product){
                        $sql = " insert into oc_x_warehouse_requisition_item ( `relevant_id`,`product_id`,`num`,`price`,`product_section_title`,`date_added`,`added_by`  ) VALUES ('". $lastId ."' , '".$product[0]."' ,'".$product[6]."' ,'".$product[5]."','".$product[3]."',NOW(), '".$userId ."')";

                        $query = $this->db->query($sql);


                }

            }
            return 1;
        }else{
            return 2;
        }

    }

    public function getWarehouseRequisition(array $data){
        $sql = " select wr.relevant_id ,wr.from_warehouse ,wr.to_warehouse,wr.date_added,u.username,wr.deliver_date,wr.relevant_status_id ,wrs.name ,w.title,wr.relevant_status_id,wr.out_type,wr.comment
              from  oc_x_warehouse_requisition wr
              LEFT JOIN oc_x_warehouse w  ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1
               ";

        if($data['filter_warehouse_id_global'] !=0){
                $sql .= " and wr.from_warehouse = '". $data['filter_warehouse_id_global']."'";
        }
        if($data['filter_warehouse_order_id']){
            $sql .= " and wr.relevant_id = '".$data['filter_warehouse_order_id'] ."'";
        }
        if($data['filter_date_deliver']){
            $sql .= " and wr.deliver_date = '".$data['filter_date_deliver'] ."'";
        }
        if($data['warehouse_id']){
            $sql .= " and wr.to_warehouse = '".$data['warehouse_id'] ."'";
        }
        if($data['filter_order_status_id']){
            $sql .= " and wr.relevant_status_id = '".$data['filter_order_status_id'] ."'";
        }
        if($data['filter_out_type_id']){
            $sql .= " and wr.out_type = '".$data['filter_out_type'] ."'";
        }
        $query = $this->db->query($sql);
        $rows = $query->rows;

        return $rows;
    }

    public function getWarehouseRequisitionItem($relevant_id,$filter_warehouse_id_global){

        $sql = " select wri.relevant_id ,wri.product_id ,p.name ,pi.inventory ,wri.num ,ps.product_section_title ,w.title,wr.out_type
              from oc_x_warehouse_requisition_item wri
              LEFT JOIN  oc_product_section  ps ON wri.product_id = ps.product_id
              LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wri.relevant_id
              LEFT JOIN  oc_x_warehouse w ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN  oc_product p ON wri.product_id = p.product_id
              LEFT JOIN  oc_product_inventory pi ON wri.product_id = pi.product_id and wr.from_warehouse = pi.warehouse_id
              WHERE  wri.relevant_id = '".$relevant_id."' and wr.from_warehouse = '".$filter_warehouse_id_global."'";

        $query = $this->db->query($sql);
        $rows = $query->rows;
        return $rows;
    }
    public function confirm($relevant_id ,$user_id){
        $status= '';
        $sql = "  update oc_x_warehouse_requisition set  relevant_status_id = 2 ,confirm_by = '".$user_id ."' where relevant_id = '".$relevant_id ."'";
        $query = $this->db->query($sql);
        if($query){
            return $status = 1 ;
        }else{
            return $status = 2 ;
        }
    }

}