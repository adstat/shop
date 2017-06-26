<?php
/**
 * Created by PhpStorm.
 * User: jshy
 * Date: 2016/10/27
 * Time: 17:16
 */
class  ModelPurchaseVmipurchase extends Model{

    public function vmipurchaseList(){
        $sql = "select A.purchase_order_id, A.`date` purchase_date, A.date_added adate, B.upload_times, A.add_user_name
            from (select * from oc_x_purchase_order where status = 1) A
            left join (select count(purchase_order_id) upload_times,  `date` purchase_date from  oc_x_purchase_order group by purchase_date) B on A.`date` = B.purchase_date
            where `date` between date_sub(current_date(), interval 30 day) and current_date()
            group by purchase_date";
        $query = $this->db->query($sql);
        echo json_encode($query->rows, JSON_UNESCAPED_UNICODE);
    }

    public function getVimPurchaseDetail($vmipurchase_order_id){
        $purchase_order_id = isset($this->request->get['purchase_order_id']) ? $this->request->get['purchase_order_id'] : 0;

        $sql = "select A.*, S.name sku_name from oc_x_purchase_order_product A
                left join oc_x_sku S on A.sku_id = S.sku_id
                where A.purchase_order_id =  '".$purchase_order_id."'";
        $query = $this->db->query($sql);
        echo json_encode($query->rows, JSON_UNESCAPED_UNICODE);
    }
}