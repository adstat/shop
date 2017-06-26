<?php

class ModelCatalogInventory extends Model {

    public function getProductInventoryHistory($params = array(), $page, $page_size)
    {
        $_condition = $this->_getInventoryHistoryCondition($params);

        $sql = "SELECT IMI.product_id, PD.name, IM.station_id, PW.warehouse_id, IM.inventory_type_id, IM.date_added, IMI.quantity, IM.add_user_name
                  FROM oc_x_inventory_move_item IMI
                  LEFT JOIN oc_x_inventory_move IM ON IMI.inventory_move_id = IM.inventory_move_id
                  LEFT JOIN oc_product P ON IMI.product_id = P.product_id
                  LEFT JOIN oc_product_description PD ON P.product_id = PD.product_id
                  LEFT JOIN oc_product_to_warehouse PW ON P.product_id = PW.product_id
                  WHERE IM.status = 1 ";
        $sql .= implode('', $_condition);
        $sql .= " ORDER BY IMI.product_id DESC";

        if(!empty($page) && !empty($page_size)){
            $sql .= " LIMIT ". ((int)$page - 1) * (int)$page_size.','.(int)$page_size;
        }

        $query   = $this->db->query($sql);
        $results = $query->rows;

        return $results;
    }

    public function getProductInventoryHistoryCount($params = array())
    {
        $_condition = $this->_getInventoryHistoryCondition($params);

        $sql = "SELECT COUNT(*) num
                  FROM oc_x_inventory_move_item IMI
                  LEFT JOIN oc_x_inventory_move IM ON IMI.inventory_move_id = IM.inventory_move_id
                  LEFT JOIN oc_product P ON IMI.product_id = P.product_id
                  LEFT JOIN oc_product_description PD ON P.product_id = PD.product_id
                  LEFT JOIN oc_product_to_warehouse PW ON P.product_id = PW.product_id
                  WHERE IM.status = 1 ";
        $sql .= implode('', $_condition);

        $query  = $this->db->query($sql);
        $result = $query->row;

        return $result['num'];
    }

    public function _getInventoryHistoryCondition($params = array())
    {
        $_condition = array();
        if(!empty($params))
        {
            if(!empty($params['product_ids'])){
                $product_ids = array_filter(explode(',', trim($params['product_ids'], ',')));
                sizeof($product_ids) == 1        && $_condition[] = " AND IMI.product_id = ". (int)$product_ids[0];
                sizeof($product_ids) >  1        && $_condition[] = " AND IMI.product_id IN (". implode(',', $product_ids) .")";
            }
            !empty($params['warehouse_id'])      && $_condition[] = " AND PW.warehouse_id = ". (int)$params['warehouse_id'];
            !empty($params['start_date'])        && $_condition[] = " AND IM.date >= '{$params['start_date']}'";
            !empty($params['end_date'])          && $_condition[] = " AND IM.date <= '{$params['end_date']}'";
            !empty($params['inventory_type_id']) && $_condition[] = " AND IM.inventory_type_id = ". (int)$params['inventory_type_id'];
        }

        return $_condition;
    }

    // 获取商品库存
    public function getProductInventory($params = array(), $fields = '', $page = 0, $page_size = 0, $order_by = '', $group_by = '')
    {
        $_condition = $this->_getProductInventoryCondition($params);
        
        $sql = "SELECT {$fields}
                FROM oc_product p
                LEFT JOIN oc_product_inventory pi ON p.product_id = pi.product_id
                LEFT JOIN oc_product_description pd ON p.product_id = pd.product_id
                LEFT JOIN oc_product_to_warehouse pw ON p.product_id = pw.product_id
                WHERE 1 = 1 ";

        !empty($_condition) && $sql .= implode("", $_condition);
        !empty($order_by)   && $sql .= " ORDER BY ". $group_by;
        !empty($group_by)   && $sql .= " GROUP BY ". $group_by;

        if(!empty($page) && !empty($page_size)){
            $sql .= " LIMIT ". ((int)$page - 1) * (int)$page_size. ','. (int)$page_size;
        }

        $query   = $this->db->query($sql);
        $results = $query->rows;

        return $results;
    }

    public function getProductInventoryCount($params = array())
    {
        $_condition = $this->_getProductInventoryCondition($params);

        $sql = "SELECT COUNT(*) num
                FROM oc_product p
                LEFT JOIN oc_product_to_warehouse pw ON p.product_id = pw.product_id
                WHERE 1 = 1 ";

        !empty($_condition) && $sql .= implode("", $_condition);

        $query   = $this->db->query($sql);
        $results = $query->row;

        return $results['num'];
    }

    public function _getProductInventoryCondition($params = array())
    {
        $_condition = array();
        if(!empty($params))
        {
            if(!empty($params['product_ids'])){
                $product_ids = array_filter(explode(',', trim($params['product_ids'], ',')));
                sizeof($product_ids) == 1        && $_condition[] = " AND p.product_id = ". (int)$product_ids[0];
                sizeof($product_ids) >  1        && $_condition[] = " AND p.product_id IN (". implode(',', $product_ids) .")";
            }
            !empty($params['warehouse_id'])      && $_condition[] = " AND pw.warehouse_id = ". (int)$params['warehouse_id'];
        }

        return $_condition;
    }

    public function getProductInventoryOnTheWay($params = array(), $fields = '', $page = 0, $page_size = 0, $order_by = '', $group_by = '')
    {
        $_condition = $this->_getProductInventoryOnTheWayCondition($params);

        $sql = "SELECT {$fields}
                FROM oc_x_pre_purchase_order_product pop
                LEFT JOIN oc_x_pre_purchase_order ppo ON pop.purchase_order_id = ppo.purchase_order_id
                WHERE 1 = 1 ";

        !empty($_condition) && $sql .= implode("", $_condition);
        !empty($order_by)   && $sql .= " ORDER BY ". $group_by;
        !empty($group_by)   && $sql .= " GROUP BY ". $group_by;

        if(!empty($page) && !empty($page_size)){
            $sql .= " LIMIT ". ((int)$page - 1) * (int)$page_size.','.(int)$page_size;
        }

        $query   = $this->db->query($sql);
        $results = $query->rows;

        return $results;
    }

    public function getProductInventoryOnTheWayCount($params = array())
    {
        $_condition = $this->_getProductInventoryOnTheWayCondition($params);

        $sql = "SELECT COUNT(DISTINCT pop.product_id) num
                FROM oc_x_pre_purchase_order_product pop
                LEFT JOIN oc_x_pre_purchase_order ppo ON pop.purchase_order_id = ppo.purchase_order_id
                WHERE 1 = 1";

        !empty($_condition) && $sql .= implode("", $_condition);

        $query  = $this->db->query($sql);
        $result = $query->row;

        return $result['num'];
    }

    public function _getProductInventoryOnTheWayCondition($params = array())
    {
        $_condition = array();
        if(!empty($params))
        {
            if(!empty($params['product_ids'])){
                $product_ids = array_filter(explode(',', trim($params['product_ids'], ',')));
                sizeof($product_ids) == 1        && $_condition[] = " AND pop.product_id = ". (int)$product_ids[0];
                sizeof($product_ids) >  1        && $_condition[] = " AND pop.product_id IN (". implode(',', $product_ids) .")";
            }
            !empty($params['warehouse_id'])      && $_condition[] = " AND ppo.warehouse_id = ". (int)$params['warehouse_id'];
            !empty($params['status'])            && $_condition[] = " AND ppo.status = ". (int)$params['status'];
            !empty($params['order_type'])        && $_condition[] = " AND ppo.order_type = ". (int)$params['order_type'];
        }

        return $_condition;
    }

    public function getInventoryType()
    {
        $sql     = "SELECT inventory_type_id, name FROM oc_x_inventory_type";
        $query   = $this->db->query($sql);
        $results = $query->rows;
        return $results;
    }

}