<?php
class ModelReportPurchase extends Model {
    public function getPurchaseOrder($filter_data){
        $sql = "SELECT
            o.purchase_order_id purchase_order_id,
            date(o.date_added) AS date_purchase,
            date(o.date_deliver) date_deliver_plan,
            date(xsm.date_added) AS date_deliver_receive,
            s.supplier_id supplier_id,
            s.`name` AS supplier_name,
            IF (o.checkout_status = 1, '未支付','已支付') checkout_status,
            os.name purchase_status,
            if(o.order_type  = 1, '采购单','退货单') purchase_type,
            o.order_total* if(o.order_type  = 1, 1, -1) order_total,
            round(sum( op.price * op.supplier_quantity / op.quantity * smi.quantity) ,2) * if(o.order_type  = 1, 1, -1) AS order_real_total,
            if(o.`invoice_flag` =1, '有', '无') invoice,
            o.use_credits_total credits_total,
            o.add_user_name add_user_name,
            o.checkout_username checkout_username,
            o.checkout_userbank checkout_userbank,
            o.checkout_usercard checkout_usercard,
            o.checkout_time checkout_time,
            u.username user_confirm,
            sct.name pay_type
            FROM oc_x_pre_purchase_order AS o
            LEFT JOIN oc_user u on o.checkout_user_id = u.user_id
            LEFT JOIN oc_x_supplier_checkout_cycle scc on o.checkout_cycle_id = scc.checkout_cycle_id
            LEFT JOIN oc_x_supplier_checkout_type sct on o.checkout_type_id = sct.checkout_type_id
            LEFT JOIN oc_x_pre_purchase_order_status os on o.status = os.order_status_id
            LEFT JOIN oc_x_pre_purchase_order_product AS op ON o.purchase_order_id = op.purchase_order_id
            LEFT JOIN oc_x_supplier AS s ON o.supplier_type = s.supplier_id
            LEFT JOIN oc_x_stock_move AS xsm ON xsm.purchase_order_id = o.purchase_order_id
            LEFT JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id AND smi.product_id = op.product_id";

        if(!empty($filter_data['filter_date_start'])&&!empty($filter_data['filter_date_end'])){
            $sql .= " where date(xsm.date_added) between '". $filter_data['filter_date_start'] ."' and '". $filter_data['filter_date_end'] ."'";
        }
        if(!empty($filter_data['filter_supplier_type'])){
            $sql .= " and o.supplier_type = '" . (int)$filter_data['filter_supplier_type'] . "'";
        }
        if(!empty($filter_data['filter_order_status_id'])){
            $sql .= " and o.status = '" . (int)$filter_data['filter_order_status_id'] . "'";
        }
        if(!empty($filter_data['filter_order_checkout_status_id'])){
            $sql .= " and o.checkout_status = '" . (int)$filter_data['filter_order_checkout_status_id'] . "'";
        }
        if(!empty($filter_data['filter_purchase_order_id'])){
            $sql .= " and (o.purchase_order_id = '" . (int)$filter_data['filter_purchase_order_id'] . "' or o.related_order = '" . (int)$filter_data['filter_purchase_order_id'] . "')";
        }
        if(!empty($filter_data['filter_order_type'])){
            $sql .= " and o.order_type = '" . (int)$filter_data['filter_order_type'] . "'";
        }

        $sql .= " GROUP BY o.purchase_order_id ORDER BY o.purchase_order_id ";

        $query = $this->db->query($sql);

        if($query){
            $return['purchases'] = $query->rows;
        }

        return $return;
    }

    public function getPurchaseDetail($filter_data){
        $sql = "SELECT
            o.purchase_order_id purchase_order_id,
            date(o.date_added) AS date_purchase,
            date(o.date_deliver) date_deliver_plan,
            date(xsm.date_added) AS date_deliver_receive,
            s.supplier_id supplier_id,
            s.`name` AS supplier_name,
            IF (o.checkout_status = 1, '未支付','已支付') checkout_status,
            os.name purchase_status,
            if(o.order_type  = 1, '采购单','退货单') purchase_type,
            op.product_id product_id,
            p.`name` AS product_name,
            cd2.`name` AS first_category,
            cd.`name` AS second_category,
            sku.sku_id sku_id,
            sku.name sku_name,
            sc.parent_name sku_first_category,
            sc.name sku_second_category,
            op.supplier_quantity supplier_quantity,
            op.quantity quantity,
             op.price price,
            op.supplier_quantity * op.price *  if(o.order_type  = 1, 1, -1) AS purchase_total,
            sum(smi.quantity) quantity_move,
            round(sum( op.price * op.supplier_quantity / op.quantity * smi.quantity) ,2) *  if(o.order_type  = 1, 1, -1) AS purchase_real_total
            FROM oc_x_pre_purchase_order AS o
            LEFT JOIN oc_user u on o.checkout_user_id = u.user_id
            LEFT JOIN oc_x_supplier_checkout_cycle scc on o.checkout_cycle_id = scc.checkout_cycle_id
            LEFT JOIN oc_x_supplier_checkout_type sct on o.checkout_type_id = sct.checkout_type_id
            LEFT JOIN oc_x_pre_purchase_order_status os on o.status = os.order_status_id
            LEFT JOIN oc_x_pre_purchase_order_product AS op ON o.purchase_order_id = op.purchase_order_id
            LEFT JOIN oc_x_supplier AS s ON o.supplier_type = s.supplier_id
            LEFT JOIN oc_product AS p ON p.product_id = op.product_id
            LEFT JOIN oc_product_to_category ptc on ptc.product_id = p.product_id
            LEFT JOIN oc_category AS c ON c.category_id = ptc.category_id
            LEFT JOIN oc_category_description AS cd ON cd.category_id = c.category_id
            LEFT JOIN oc_category AS c2 ON c.parent_id = c2.category_id
            LEFT JOIN oc_category_description AS cd2 ON cd2.category_id = c2.category_id
            LEFT JOIN oc_x_sku sku ON p.sku_id = sku.sku_id
            LEFT JOIN oc_x_sku_category sc ON sku.sku_category_id = sc.sku_category_id
            LEFT JOIN oc_x_stock_move AS xsm ON xsm.purchase_order_id = o.purchase_order_id
            LEFT JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id AND smi.product_id = op.product_id
           ";

        if(!empty($filter_data['filter_date_start'])&&!empty($filter_data['filter_date_end'])){
            $sql .= " where date(xsm.date_added) between '". $filter_data['filter_date_start'] ."' and '". $filter_data['filter_date_end'] ."'";
        }
        if(!empty($filter_data['filter_supplier_type'])){
            $sql .= " and o.supplier_type = '" . (int)$filter_data['filter_supplier_type'] . "'";
        }
        if(!empty($filter_data['filter_order_status_id'])){
            $sql .= " and o.status = '" . (int)$filter_data['filter_order_status_id'] . "'";
        }
        if(!empty($filter_data['filter_order_checkout_status_id'])){
            $sql .= " and o.checkout_status = '" . (int)$filter_data['filter_order_checkout_status_id'] . "'";
        }
        if(!empty($filter_data['filter_purchase_order_id'])){
            $sql .= " and (o.purchase_order_id = '" . (int)$filter_data['filter_purchase_order_id'] . "' or o.related_order = '" . (int)$filter_data['filter_purchase_order_id'] . "')";
        }
        if(!empty($filter_data['filter_order_type'])){
            $sql .= " and o.order_type = '" . (int)$filter_data['filter_order_type'] . "'";
        }


        $sql .= " GROUP BY o.purchase_order_id,op.product_id ORDER BY o.purchase_order_id,op.product_id ";

        $query = $this->db->query($sql);

        if($query){
            $return['details'] = $query->rows;
        }

        return $return;
    }
}