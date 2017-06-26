<?php

class ModelPurchasePrePurchaseAdjust extends Model {

    // 创建采购调整单
    public function createAdjustOrder($data = array())
    {
        if(!isset($data['purchase_order_id']) || $data['purchase_order_id'] <= 0){
            return false;
        }
        if(!isset($data['purchase_order_product_id']) || $data['purchase_order_product_id'] <= 0){
            return false;
        }
        if(!isset($data['price']) || $data['price'] <= 0){
            return false;
        }


        $user_id    = $this->user->getId();
        $user_name  = $this->user->getUserName();
        $time       = date('Y-m-d H:i:s', time());

        // 查询 supplier_quantity
        $sql    = "SELECT price, supplier_quantity FROM oc_x_pre_purchase_order_product WHERE purchase_order_product_id = ". $data['purchase_order_product_id'];
        $query  = $this->db->query($sql);
        $result = $query->row;
        if(empty($result)){ return false; }

        $supplier_quantity = $result['supplier_quantity'];
        $price             = $result['price'];
        if($price == $data['price']){ return false; }


        $total  = $data['price'] * $supplier_quantity;

        $this->db->query('START TRANSACTION');
        $bool = 1;

        // 新增 pre_purchase_order 记录
        $filed = "station_id, date_deliver, invoice_flag, image, use_credits_total, quehuo_credits, supplier_type, product_category, checkout_type_id, checkout_cycle_id, checkout_cycle_num, checkout_username, checkout_userbank, checkout_usercard, checkout_status, checkout_plan_date, checkout_time, checkout_user_id, confirmed, confirmed_by, confirm_user_name, warehouse_id";
        $sql = "INSERT INTO oc_x_pre_purchase_order (". $filed .",
                    date_added,
                    added_by,
                    add_user_name,
                    order_total,
                    status,
                    order_comment,
                    order_type,
                    related_order
                    )
                SELECT ". $filed .",
                    '{$time}' AS date_added,
                    {$user_id} AS added_by,
                    '{$user_name}' AS add_user_name,
                    {$total} AS order_total,
                    1 AS status,
                    '新增调整单' AS order_comment,
                    3 AS order_type,
                    {$data['purchase_order_id']} AS related_order
                 FROM oc_x_pre_purchase_order WHERE purchase_order_id = ". $data['purchase_order_id'];
        $bool = $bool && $this->db->query($sql);
        $purchase_order_id = $this->db->getLastId();

        // 新增 pre_purchase_order_product 记录
        $filed = "station_id, sku_id, product_id, quantity_old, quantity, supplier_quantity, date, weight_class_id, weight, real_cost, purchase_in_pcs, purchase_price_500g, purchase_qty_500g, purchase_total, purchase_weight_total, buyer, modified_by, date_modified";
        $sql = "INSERT INTO oc_x_pre_purchase_order_product(". $filed .",
                    purchase_order_id,
                    status,
                    price
                    )
                SELECT ". $filed .",
                    {$purchase_order_id} AS purchase_order_id,
                    0 AS status,
                    {$data['price']} AS price
                    FROM oc_x_pre_purchase_order_product WHERE purchase_order_product_id = {$data['purchase_order_product_id']}";
        $bool = $bool && $this->db->query($sql);

        if($bool){
            $this->db->query('COMMIT');
        }
        else{
            $this->db->query("ROLLBACK");
        }

        return $bool;
    }

    // 采购调整单审核通过
    public function passAdjustOrder($data = array())
    {
        if(!isset($data['purchase_order_id']) || $data['purchase_order_id'] <= 0){
            return false;
        }
        if(!isset($data['purchase_order_product_id']) || $data['purchase_order_product_id'] <= 0){
            return false;
        }

        $user_id    = $this->user->getId();
        $user_name  = $this->user->getUserName();
        $time       = date('Y-m-d H:i:s', time());

        // 查找 更改过价格的商品ID & 采购调整单ID
        $sql = "SELECT product_id, purchase_order_id
                    FROM oc_x_pre_purchase_order_product
                    WHERE purchase_order_product_id = {$data['purchase_order_product_id']}";
        $query  = $this->db->query($sql);
        $result = $query->row;
        if(empty($result)){
            return false;
        }
        $product_id              = $result['product_id'];
        $first_purchase_order_id = $result['purchase_order_id'];

        // 通过原始采购单ID & 商品ID 查询 [ 原始订单商品ID ] supplier_quantity * price
        $sql    = "SELECT OP.supplier_quantity, OP.price
                      FROM oc_x_pre_purchase_order_product OP
                      LEFT JOIN oc_x_pre_purchase_order PPO ON PPO.purchase_order_id = OP.purchase_order_id
                      WHERE PPO.purchase_order_id = {$data['purchase_order_id']}
                      AND product_id = {$product_id}";
        $query  = $this->db->query($sql);
        $result = $query->row;
        if(empty($result)){
            return false;
        }
        $supplier_quantity = $result['supplier_quantity'];
        $price  = -$result['price'];
        $total  = $price * $supplier_quantity;


        $this->db->query('START TRANSACTION');
        $bool = 1;

        // 新增 pre_purchase_order 负金额记录
        $filed = "station_id, date_deliver, invoice_flag, image, use_credits_total, quehuo_credits, supplier_type, product_category, checkout_type_id, checkout_cycle_id, checkout_cycle_num, checkout_username, checkout_userbank, checkout_usercard, checkout_status, checkout_plan_date, checkout_time, checkout_user_id, confirmed, confirmed_by, confirm_user_name, warehouse_id";
        $sql = "INSERT INTO oc_x_pre_purchase_order (". $filed .",
                    date_added,
                    added_by,
                    add_user_name,
                    order_total,
                    status,
                    order_comment,
                    order_type,
                    related_order
                    )
                SELECT ". $filed .",
                    '{$time}' AS date_added,
                    {$user_id} AS added_by,
                    '{$user_name}' AS add_user_name,
                    {$total} AS order_total,
                    2 AS status,
                    '新增调整单' AS order_comment,
                    3 AS order_type,
                    {$data['purchase_order_id']} AS related_order
                 FROM oc_x_pre_purchase_order WHERE purchase_order_id = ". $data['purchase_order_id'];
        $bool = $bool && $this->db->query($sql);
        $purchase_order_id = $this->db->getLastId();

        // 新增 pre_purchase_order_product 负金额记录
        $filed = "station_id, sku_id, product_id, quantity_old, quantity, supplier_quantity, date, weight_class_id, weight, real_cost, purchase_in_pcs, purchase_price_500g, purchase_qty_500g, purchase_total, purchase_weight_total, buyer, modified_by, date_modified";
        $sql = "INSERT INTO oc_x_pre_purchase_order_product(". $filed .",
                    purchase_order_id,
                    status,
                    price
                    )
                SELECT ". $filed .",
                    {$purchase_order_id} AS purchase_order_id,
                    1 AS status,
                    {$price} AS price
                    FROM oc_x_pre_purchase_order_product WHERE purchase_order_product_id = {$data['purchase_order_product_id']}";
        $bool = $bool && $this->db->query($sql);

        // 修改采购调整单ID状态
        $sql  = "UPDATE oc_x_pre_purchase_order SET status = 2 WHERE purchase_order_id = {$first_purchase_order_id}";
        $bool = $bool && $this->db->query($sql);

        // 修改采购调整单商品状态
        $sql  = "UPDATE oc_x_pre_purchase_order_product SET status = 1 WHERE purchase_order_product_id = {$data['purchase_order_product_id']}";
        $bool = $bool && $this->db->query($sql);

        if($bool){
            $this->db->query('COMMIT');
        }
        else{
            $this->db->query("ROLLBACK");
        }

        return $bool;
    }

    // 采购调整单审核不通过
    public function cancelAdjustOrder($related_order_id = 0, $purchase_order_product_id = 0)
    {
        $related_order_id          = (int)$related_order_id;
        $purchase_order_product_id = (int)$purchase_order_product_id;
        if($related_order_id <= 0 || $purchase_order_product_id <= 0){
            return false;
        }

        $user_id    = $this->user->getId();
        $time       = date('Y-m-d H:i:s', time());

        // 采购调整单商品ID => 查询 采购调整单
        $sql    = "SELECT purchase_order_id
                      FROM oc_x_pre_purchase_order_product
                      WHERE purchase_order_product_id = {$purchase_order_product_id}";
        $query  = $this->db->query($sql);
        $result = $query->row;
        if(empty($result)){
            return false;
        }
        $purchase_order_id = $result['purchase_order_id'];


        $this->db->query('START TRANSACTION');
        $bool = 1;

        // 更改采购调整单状态 [ 取消 ]
        $sql  = "UPDATE oc_x_pre_purchase_order SET status = 3
                    WHERE purchase_order_id = {$purchase_order_id}
                    AND related_order = {$related_order_id}";
        $bool = $bool && $this->db->query($sql);

        // 写入一条操作历史记录
        $sql  = "INSERT INTO oc_x_pre_purchase_order_history
                    SET
                    purchase_order_id = {$purchase_order_id},
                    status = 3,
                    checkout_status = 2,
                    date_added = '{$time}',
                    added_by = {$user_id}";
        $bool = $bool && $this->db->query($sql);

        if($bool){
            $this->db->query('COMMIT');
        }
        else{
            $this->db->query("ROLLBACK");
        }

        return $bool;
    }

    // 采购调整单列表
    public function getAdjustOrders($data = array())
    {
        $_condition = array();
        if(!empty($data))
        {
            !empty($data['filter_supplier_type'])               && $_condition[] = " AND o.supplier_type = '" . (int)$data['filter_supplier_type'] . "'";
            !empty($data['filter_order_type'])                  && $_condition[] = " AND o.order_type = '" . (int)$data['filter_order_type'] . "'";
            !empty($data['filter_purchase_order_id'])           && $_condition[] = " AND (o.purchase_order_id = '" . (int)$data['filter_purchase_order_id'] . "' or o.related_order = '" . (int)$data['filter_purchase_order_id'] . "')";
            !empty($data['filter_purchase_person_id'])          && $_condition[] = " AND o.added_by = '". (int)$data['filter_purchase_person_id'] ."'";
            !empty($data['filter_date_deliver'])                && $_condition[] = " AND DATE(o.date_deliver) >= DATE('" . $this->db->escape($data['filter_date_deliver']) . "')";
            !empty($data['filter_date_deliver_end'])            && $_condition[] = " AND DATE(xsm.date_added) <= DATE('" . $this->db->escape($data['filter_date_deliver_end']) . "')";
            !empty($data['filter_order_status_id'])             && $_condition[] = " AND o.status = '" . (int)$data['filter_order_status_id'] . "'";
            !empty($data['filter_order_checkout_status_id'])    && $_condition[] = " AND o.checkout_status = '" . (int)$data['filter_order_checkout_status_id'] . "'";
            !empty($data['filter_warehouse_id'])                && $_condition[] = " AND o.warehouse_id = ". (int)$data['filter_warehouse_id'];
        }

        $sql = "SELECT o.*, st.name, pos.`name` as status_name, date(xsm.date_added) as real_date_deliver
                    FROM oc_x_pre_purchase_order o
                    LEFT JOIN oc_x_supplier st ON o.supplier_type = st.supplier_id
                    LEFT JOIN oc_x_pre_purchase_order_status pos ON pos.order_status_id = o.status
                    LEFT JOIN oc_x_stock_move xsm ON xsm.purchase_order_id = o.purchase_order_id
                    WHERE o.purchase_order_id IN (
                        SELECT related_order AS purchase_order_id FROM oc_x_pre_purchase_order WHERE order_type = 3
                    )";

        $sql .= implode("", $_condition);
        $sort_data = array('o.purchase_order_id', 'o.date_deliver');

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY o.purchase_order_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) { $data['start'] = 0;  }
            if ($data['limit'] < 1) { $data['limit'] = 20; }
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    // 获取采购单信息
    public function getAdjustOrderInfo($data = array(), $fields = '')
    {
        $_condition = array();
        if(!empty($data))
        {
            !empty($data['related_order_ids']) && sizeof($data['related_order_ids']) && $_condition[] = " AND related_order IN (". implode(',', $data['related_order_ids']) .")";
            !empty($data['order_type'])                                              && $_condition[] = " AND order_type = ". (int)$data['order_type'];
        }

        $sql   = "SELECT {$fields} FROM oc_x_pre_purchase_order WHERE 1 = 1 ";
        $sql  .= implode("", $_condition);

        $query = $this->db->query($sql);
        return $query->rows;
    }

    // 根据原始采购单ID 获取 采购调整单商品信息
    public function getAdjustOrderProductsByPurchaseOrder($purchase_order_id = 0)
    {
        $purchase_order_id = (int)$purchase_order_id;
        if($purchase_order_id < 0){
            return array();
        }

        $sql    = "SELECT GROUP_CONCAT(purchase_order_id) purchase_order_ids
                      FROM oc_x_pre_purchase_order
                      WHERE order_type = 3 AND related_order = ". $purchase_order_id;
        $query  = $this->db->query($sql);
        $result = $query->row;
        if(empty($result['purchase_order_ids'])){
            return array();
        }

        $sql   = "SELECT OP.purchase_order_product_id, OP.product_id, OP.quantity, OP.supplier_quantity, OP.price, OP.real_cost, OP.status status, PPO.status order_status
                      FROM oc_x_pre_purchase_order_product OP
                      LEFT JOIN oc_x_pre_purchase_order PPO ON PPO.purchase_order_id = OP.purchase_order_id
                      WHERE OP.purchase_order_id IN({$result['purchase_order_ids']})";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    // 查询调整单总数
    public function getTotalAdjustOrders($data = array()) {

        $_condition = $this->_getPurchaseOrderCondition($data);

        $sql  = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "x_pre_purchase_order` WHERE 1 = 1 ";
        $sql .= implode("", $_condition);
        $sql .= " AND purchase_order_id IN (
                        SELECT related_order AS purchase_order_id FROM oc_x_pre_purchase_order WHERE order_type = 3
                  )";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    // 筛选条件
    public function _getPurchaseOrderCondition($data = array())
    {
        $_condition = array();
        if(!empty($data))
        {
            !empty($data['filter_supplier_type'])               && $_condition[] = " AND supplier_type = '" . (int)$data['filter_supplier_type'] . "'";
            !empty($data['filter_order_type'])                  && $_condition[] = " AND order_type = '" . (int)$data['filter_order_type'] . "'";
            !empty($data['filter_purchase_order_id'])           && $_condition[] = " AND (purchase_order_id = '" . (int)$data['filter_purchase_order_id'] . "' or related_order = '" . (int)$data['filter_purchase_order_id'] . "')";
            !empty($data['filter_purchase_person_id'])          && $_condition[] = " AND added_by = '". (int)$data['filter_purchase_person_id'] ."'";
            !empty($data['filter_date_deliver'])                && $_condition[] = " AND DATE(date_deliver) >= DATE('" . $this->db->escape($data['filter_date_deliver']) . "')";
            !empty($data['filter_date_deliver_end'])            && $_condition[] = " AND DATE(date_deliver) <= DATE('" . $this->db->escape($data['filter_date_deliver_end']) . "')";
            !empty($data['filter_order_status_id'])             && $_condition[] = " AND status = '" . (int)$data['filter_order_status_id'] . "'";
            !empty($data['filter_order_checkout_status_id'])    && $_condition[] = " AND checkout_status = '" . (int)$data['filter_order_checkout_status_id'] . "'";
            !empty($data['filter_warehouse_id'])                && $_condition[] = " AND warehouse_id = ". (int)$data['filter_warehouse_id'];
        }
        return $_condition;
    }

    // 获取采购调整单 金额
    public function getPurchaseOrders($data = array(), $fields = ' * ', $order_by = 'purchase_order_id DESC', $group_by = '', $page = '', $page_size = '')
    {
        $_condition = $this->_getPurchaseOrderCondition($data);

        $sql  = "SELECT {$fields} FROM oc_x_pre_purchase_order WHERE 1 =1 ";
        $sql .= implode("", $_condition);
        $sql .= " ORDER BY {$order_by}";

        if(!empty($group_by)) {
            $sql .= " GROUP BY {$group_by}";
        }
        if(!empty($page) && !empty($page_size)){
            $page = $page < 1 ? $page = 1 : $page;
            $sql .= " LIMIT ". ($page - 1 ) * $page_size .','. $page_size;
        }

        $query   = $this->db->query($sql);
        $results = $query->rows;

        return $results;
    }
}