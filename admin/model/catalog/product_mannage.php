<?php
class ModelCatalogProductMannage extends Model{
   public function addPriceApplication(){

   }

   public function getProductInfo($product_id)
   {
       $sql = "select p.product_id,p.name,p.price,s.price s_price,if(p.instock = 0,s.price,if(s.supplier_order_quantity_type = 1,round(s.price*p.inv_size/s.supplier_unit_size,4),round(s.price*p.inv_size,4))) p_price
            from oc_product p
            left join oc_x_sku s on s.sku_id = p.sku_id
            where p.product_id = '" . $product_id . "'";

       $query = $this->db->query($sql);

       if (sizeof($query->rows) > 0) {
           $return['status'] = 1;
           $return['info'] = $query->row;
       }else{
           $return['status'] = 0;
       }

       return $return;
   }

    public function submitApplication($user_id,$data){
        $date = date('Y-m-d H:i:s');
        $sql = "INSERT INTO oc_product_price_application (`product_id`,`name`,`price`,`sku_price`,`purchase_price`,`edit_price`,`date_added`,`add_user`) VALUES ";
        $m=0;
        foreach($data as $value){
            $sql .= "(".$value['product_id'].",'".$value['name']."',".$value['price'].",".$value['s_price'].",".$value['p_price'].",'".$value['e_price']."','".$date."',".$user_id.")";
            if(++$m < sizeof($data)){
                $sql .= ', ';
            }
            else{
                $sql .= ';';
            }
        }
        $query = $this->db->query($sql);

        if($query){
            return true;
        }else{
            return false;
        }
    }

    public function getApplicationList($filter_data){

        $sql = "select pp.price_edit_id,pp.product_id,pp.name,pp.price,pp.sku_price,pp.purchase_price,pp.edit_price,concat(u.lastname,u.firstname) app_user,date(pp.date_added) app_time
            from oc_product_price_application pp
            left join oc_user u on u.user_id = pp.add_user
            where pp.status = 1 and pp.confirmed = 0
            ";

        if($filter_data['filter_user'] != NULL && $filter_data['filter_user'] != '*' ){
            $sql .= " and pp.add_user = '".$filter_data['filter_user']."'";
        }
        if($filter_data['filter_date'] != NULL){
            $sql .= " and date(pp.date_added) = '".$filter_data['filter_date']."'";
        }

        $sql .= " order by pp.price_edit_id";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getConfirmedHistory($start,$limit){
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $sql = "select pp.price_edit_id,pp.product_id,pp.name,pp.price,pp.sku_price,pp.purchase_price,pp.edit_price,concat(u.lastname,u.firstname) add_user,date(pp.date_added) date_added
            from oc_product_price_application pp
            left join oc_user u on u.user_id = pp.add_user
            where pp.status = 1 and pp.confirmed = 1
            order by pp.price_edit_id limit ".$start.",".$limit."
            ";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getTotalConfirmedList(){
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_price_application where status = 1 and confirmed = 1";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function confirmApplication($user_id,$price_edit_id){
        $date = date('Y-m-d H:i:s');
        $price_edit_ids = implode(",",$price_edit_id);
        $sql = "update oc_product_price_application
                set status = 1,
                confirmed = 1,
                confirm_user = '".(int)$user_id."',
                confirm_date = '".$date."'
                where price_edit_id in (".$price_edit_ids.")";

        $query = $this->db->query($sql);

        if($query){
            return true;
        }else{
            return false;
        }
    }

    public function rollbackApplication($price_edit_id){
        $sql = "update oc_product_price_application
           set status = 0,
           confirmed = 0
           where price_edit_id = '".(int)$price_edit_id."'";

        $query = $this->db->query($sql);

        if($query){
            return true;
        }else{
            return false;
        }

    }
}
?>