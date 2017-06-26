<?php
class ModelPurchaseInventoryPlan extends Model {

    public function getInventoryPlanList(){
        $sql = "select A.inventory_plan_id, A.`date` inventory_plan_date, A.date_added adate, B.upload_times, A.add_user_name
            from oc_x_inventory_plan A
            left join (select count(inventory_plan_id) upload_times,  `date` inventory_plan_date from oc_x_inventory_plan group by inventory_plan_date) B on A.`date` = B.inventory_plan_date
            where A.status = 1 and date(A.date_added) between date_sub(current_date(), interval 30 day) and current_date()
            group by inventory_plan_date";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getInventoryPlanDetail($inventory_plan_id = 0){
        $sql = "select A.product_id, A.quantity, P.station_id, P.name product_name, P.status
                from oc_x_inventory_plan_product A
                left join oc_product P on A.product_id = P.product_id
                where A.inventory_plan_id =  '".$inventory_plan_id."'";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getInventoryProductInfo($list = array(0)){
        $sql = "SELECT product_id, name FROM oc_product WHERE product_id in(" . implode(',', $list) .")";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function addInventoryPlan($data){

        $date = $data['date'];
        $products = $data['products'];
        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName();


        $this->db->query('START TRANSACTION');
        $bool = 1;

        //替换已上传库存
        $bool = $bool && $this->db->query("update oc_x_inventory_plan A left join oc_x_inventory_plan_product B on A.inventory_plan_id = B.inventory_plan_id set A.status = 0, B.status = 0 where A.date = '".$date."'");

        //增加采购记录, 目前仅限生鲜使用
        $sql = "INSERT INTO `oc_x_inventory_plan` (`station_id`, `date`, `date_added`, `added_by`, `add_user_name`)
                VALUES('1', '".$date."', NOW(), '".$user_id."', '".$user_name."')";
        $bool = $bool && $this->db->query($sql);
        $inventory_plan_id = $this->db->getLastId();

        //增加采购
        $sql = "INSERT INTO `oc_x_inventory_plan_product` (`inventory_plan_id`, `station_id`, `product_id`, `quantity`) VALUES ";
        $m=0;
        foreach($products as $product){
            $sql .= "(".$inventory_plan_id.", 1, '".$product['product_id']."','".$product['quantity']."')";
            if(++$m < sizeof($products)){
                $sql .= ', ';
            }
            else{
                $sql .= ';';
            }
        }
        $bool = $bool && $this->db->query($sql);

        if($bool){
            $this->db->query('COMMIT');
        }
        else{
            $this->db->query("ROLLBACK");
        }

        return $bool;
    }
}