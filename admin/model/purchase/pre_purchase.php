<?php
class ModelPurchasePrePurchase extends Model {

    public function getPrePurchaseOrderProducts($id = 0){
        $sql = "select purchase_order_product_id, purchase_order_id, quantity, supplier_quantity, price, real_cost from oc_x_pre_purchase_order_product where purchase_order_id = '".$id."'";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function update($targetTable, $rowData, $indexFilter){
        $sql = "UPDATE ".$targetTable . " SET ";
        foreach($rowData as $k=>$v){
            $sql .= $k . "='" . $v . "',";
        }
        $sql .= " date_modified=NOW()";
        $sql .= " WHERE ".$indexFilter['field']." = '".$indexFilter['value']."'";

        //更新oc_product表中的real_cost，不需要保存到oc_product_history表中
        $sql_q = "select product_id from oc_x_pre_purchase_order_product where purchase_order_product_id = '".$indexFilter['value'] ."'";

        $product_id = $this->db->query($sql_q)->row;

        $sql_p = "update oc_product set real_cost = '".$rowData['real_cost']."' where product_id = '".$product_id['product_id']."' ";

        $this->db->query($sql_p);

        return $this->db->query($sql);
    }

    public function checkPrePurchaseOrderStatus($purchaseOrderId){
        $sql = "select PO.purchase_order_id, PO.checkout_status from oc_x_pre_purchase_order PO where PO.purchase_order_id = '".(int)$purchaseOrderId."'";
        $query = $this->db->query($sql);

        return $query->row;
    }

    public function reCalcPurchaseOrder($purchaseOrderId){
        $sql = "update oc_x_pre_purchase_order A left join (
                    select PO.purchase_order_id, PO.order_total, sum(POP.supplier_quantity*POP.price) new_total from oc_x_pre_purchase_order PO left join oc_x_pre_purchase_order_product POP on PO.purchase_order_id = POP.purchase_order_id
                    where POP.purchase_order_id = '".(int)$purchaseOrderId."'
                ) B on A.purchase_order_id = B.purchase_order_id
                set A.order_total = B.new_total
                where A.purchase_order_id = '".(int)$purchaseOrderId."' and A.checkout_status = '1'";

        return $this->db->query($sql);
    }

    public function addHistory($targetTable, $historyTable, $historyFields, $indexFilter, $operator){
        $sql = "INSERT INTO ".$historyTable." (".implode(',',$historyFields).",status,date_added,added_by)
        SELECT ".implode(',',$historyFields).",status,NOW(),'".$operator."'
        FROM ".$targetTable."
        WHERE ".$indexFilter['field']." = '".$indexFilter['value']."'";

        //return $sql;
        return $this->db->query($sql);
    }




    public function getOrder($order_id) {
        $order_query = $this->db->query("SELECT o.*,s.name from oc_x_pre_purchase_order as o left join oc_x_supplier as s on s.supplier_id = o.supplier_type WHERE purchase_order_id = '" . (int)$order_id . "'");

        if ($order_query->num_rows) {
            $sql = "select A.ds_real_cost,A.df_real_cost,A.ds_price,A.df_price,opc.purchase_cost, pd.name,pop.*
                from oc_x_pre_purchase_order_product as pop
                left join oc_product as p on p.product_id = pop.product_id
                left join oc_product_description pd on p.product_id = pd.product_id and pd.language_id = 2
                left join oc_x_purchase_cost opc on opc.product_id = pop.product_id
                left join (
                    select pop.product_id,
                    substring_index(substring_index(GROUP_CONCAT(pop.price order by pop.purchase_order_product_id desc),',',2),',',-1) ds_price,
                    substring_index(GROUP_CONCAT(pop.price order by pop.purchase_order_product_id desc),',',1) df_price,
                    substring_index(GROUP_CONCAT(pop.real_cost order by pop.purchase_order_product_id desc),',',1) df_real_cost,
                    substring_index(substring_index(GROUP_CONCAT(pop.real_cost order by pop.purchase_order_product_id desc),',',2),',',-1) ds_real_cost
                    from oc_x_pre_purchase_order_product as pop
                    left join oc_x_pre_purchase_order po on po.purchase_order_id = pop.purchase_order_id
                    inner join (
                        select product_id from oc_x_pre_purchase_order_product where purchase_order_id = '".$order_id."'
                    ) B on B.product_id = pop.product_id
                    where pop.price <> 0 and po.status <> 3 and po.order_type = 1
                    group by pop.product_id
                ) A on A.product_id = pop.product_id
                where pop.purchase_order_id = '".(int)$order_id."'
                ";

//			$order_product_query = $this->db->query("select opc.purchase_cost, pd.name,pop.* from oc_x_pre_purchase_order_product as pop left join oc_product as p on p.product_id = pop.product_id left join oc_product_description pd on p.product_id = pd.product_id and pd.language_id = 2 left join oc_x_purchase_cost opc on opc.product_id = pop.product_id where pop.purchase_order_id = '" . (int)$order_id . "'");
            $order_product_query = $this->db->query($sql);
            return array(
                'purchase_order_id'                => $order_query->row['purchase_order_id'],
                'station_id'              => $order_query->row['station_id'],
                'date_added'          => $order_query->row['date_added'],
                'date_deliver'                => $order_query->row['date_deliver'],
                'image'                => $order_query->row['image'],
                'added_by'              => $order_query->row['added_by'],
                'add_user_name'              => $order_query->row['add_user_name'],
                'supplier_type'               => $order_query->row['supplier_type'],
                'status'                    => $order_query->row['status'],
                'products'             => $order_product_query->rows,
                'checkout_type_id' => $order_query->row['checkout_type_id'],
                'checkout_cycle_id' => $order_query->row['checkout_cycle_id'],
                'checkout_cycle_num' => $order_query->row['checkout_cycle_num'],
                'checkout_username' => $order_query->row['checkout_username'],
                'checkout_userbank' => $order_query->row['checkout_userbank'],
                'checkout_usercard' => $order_query->row['checkout_usercard'],
                'checkout_status' => $order_query->row['checkout_status'],
                'invoice_flag' => $order_query->row['invoice_flag'],
                'order_total' => $order_query->row['order_total'],
                'use_credits_total' => $order_query->row['use_credits_total'],
                'quehuo_credits' => $order_query->row['quehuo_credits'],
                'supplier_name' => $order_query->row['name'],
                'order_comment' => $order_query->row['order_comment'],
                'order_type' => $order_query->row['order_type'],
                'related_order' => $order_query->row['related_order']

            );
        } else {
            return;
        }
    }

    public function getCopyOrder($order_id) {
        $order_query = $this->db->query("SELECT o.*,s.name from oc_x_pre_purchase_order as o left join oc_x_supplier as s on s.supplier_id = o.supplier_type WHERE purchase_order_id = '" . (int)$order_id . "'");

        if ($order_query->num_rows) {

            $order_product_query = $this->db->query("select p.name, p.price sale_price, p.inv_size,s.supplier_unit_size, s.supplier_order_quantity_type, pop.* from oc_x_pre_purchase_order_product as pop left join oc_product as p on p.product_id = pop.product_id left join oc_x_sku as s on s.sku_id = p.sku_id where pop.purchase_order_id = '" . (int)$order_id . "'");

            return array(
                'purchase_order_id' => $order_query->row['purchase_order_id'],
                'station_id' => $order_query->row['station_id'],
                'date_added' => $order_query->row['date_added'],
                'date_deliver' => $order_query->row['date_deliver'],
                'image' => $order_query->row['image'],
                'add_user_name' => $order_query->row['add_user_name'],
                'supplier_type' => $order_query->row['supplier_type'],
                'status' => $order_query->row['status'],
                'products' => $order_product_query->rows,
                'checkout_type_id' => $order_query->row['checkout_type_id'],
                'checkout_cycle_id' => $order_query->row['checkout_cycle_id'],
                'checkout_cycle_num' => $order_query->row['checkout_cycle_num'],
                'checkout_username' => $order_query->row['checkout_username'],
                'checkout_userbank' => $order_query->row['checkout_userbank'],
                'checkout_usercard' => $order_query->row['checkout_usercard'],
                'checkout_status' => $order_query->row['checkout_status'],
                'invoice_flag' => $order_query->row['invoice_flag'],
                'order_total' => $order_query->row['order_total'],
                'use_credits_total' => $order_query->row['use_credits_total'],
                'quehuo_credits' => $order_query->row['quehuo_credits'],
                'supplier_name' => $order_query->row['name'],
                'order_comment' => $order_query->row['order_comment'],
                'order_type' => $order_query->row['order_type'],
                'related_order' => $order_query->row['related_order']
            );
        } else {
            return;
        }
    }

    public function getOrderImages($purchase_order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "x_pre_purchase_order_image WHERE purchase_order_id = '" . (int)$purchase_order_id . "' ");

        return $query->rows;
    }

    public function getOrderGetProductInfo($order_id) {
        $sql = "SELECT
	xsm.inventory_move_id,
	xsm.purchase_order_id,
	smi.product_id,
	sum(smi.quantity) AS quantity
FROM
	oc_x_stock_move AS xsm
LEFT JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id
WHERE
	xsm.inventory_type_id = 11
AND xsm.purchase_order_id = " . $order_id . "
GROUP BY
	smi.product_id";
        $query = $this->db->query($sql);
        $result = $query->rows;

        $return = array();

        if(!empty($result)){
            foreach($result as $key => $value){
                $return[$value['product_id']] = $value['quantity'];
            }
        }

        return $return;

    }

    public function getOrderGetProductDate($order_id) {
        $sql = "SELECT
	xsm.inventory_move_id,
	xsm.purchase_order_id,
	date_format(xsm.date_added, '%Y-%m-%d') as deliver_date
FROM
	oc_x_stock_move AS xsm
WHERE
	xsm.inventory_type_id = 11
AND xsm.purchase_order_id = " . $order_id . "
";
        $query = $this->db->query($sql);
        $result = $query->rows;

        $return = array();

        if(!empty($result)){
            foreach($result as $key => $value){
                $return[] = $value['deliver_date'];
            }
        }
        $return = implode(",", $return);
        return $return;

    }


    public function getOrders($data = array()) {

        $sql = "SELECT o.*,st.name,pos.`name` as status_name,date(xsm.date_added) as real_date_deliver
		FROM `" . DB_PREFIX . "x_pre_purchase_order` o
		LEFT JOIN oc_x_supplier st on o.supplier_type = st.supplier_id
        LEFT JOIN oc_x_pre_purchase_order_status as pos on pos.order_status_id = o.status
        LEFT JOIN oc_x_stock_move AS xsm ON xsm.purchase_order_id = o.purchase_order_id
        where 1=1
		";



        if (!empty($data['filter_supplier_type'])) {
            $sql .= " AND o.supplier_type = '" . (int)$data['filter_supplier_type'] . "'";
        }
        if (!empty($data['filter_order_type'])) {
            // 调整单
            if($data['filter_order_type'] == 3){
                $sql .=" AND o.purchase_order_id IN (
                        SELECT related_order AS purchase_order_id FROM oc_x_pre_purchase_order WHERE order_type = ". (int)$data['filter_order_type'] ."
                    )";
            }else{
                $sql .= " AND o.order_type = '" . (int)$data['filter_order_type'] . "'";
            }
        }

        if (!empty($data['filter_purchase_order_id'])) {
            $sql .= " AND (o.purchase_order_id = '" . (int)$data['filter_purchase_order_id'] . "' or o.related_order = '" . (int)$data['filter_purchase_order_id'] . "')";
        }


        if (!empty($data['filter_purchase_person_id'])) {
            $sql .= " AND o.added_by = '". (int)$data['filter_purchase_person_id'] ."'";
        }

        if (!empty($data['filter_date_deliver'])) {
            $sql .= " AND DATE(o.date_deliver) >= DATE('" . $this->db->escape($data['filter_date_deliver']) . "')";
        }
        if (!empty($data['filter_date_deliver_end'])) {
            $sql .= " AND DATE(xsm.date_added) <= DATE('" . $this->db->escape($data['filter_date_deliver_end']) . "')";
        }
        if (!empty($data['filter_order_status_id'])) {
            $sql .= " AND o.status = '" . (int)$data['filter_order_status_id'] . "'";
        }
        if (!empty($data['filter_order_checkout_status_id'])) {
            $sql .= " AND o.checkout_status = '" . (int)$data['filter_order_checkout_status_id'] . "'";
        }
        if (!empty($data['filter_warehouse_id'])){
            $sql .= " AND o.warehouse_id = ". (int)$data['filter_warehouse_id'];
        }
        if (!empty($data['remote_order_type'])){
            $sql .= " AND o.order_type != ". (int)$data['remote_order_type'];
        }


        $sort_data = array(
            'o.purchase_order_id',
            'o.date_deliver'
        );

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
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function editOrderStatus($order_id, $order_status_id) {
        $data = array();
        $data['comment'] = '';

        $sql = "select purchase_order_id, status, checkout_status from oc_x_pre_purchase_order where purchase_order_id = " . $order_id;
        $query = $this->db->query($sql);
        $order_info = $query->row;

//        //已支付不可取消 - 暂时可以取消
//        if($order_info['checkout_status'] == 2 && (int)$order_status_id == 3){
//            return false;
//        }

        $this->db->query("UPDATE `" . DB_PREFIX . "x_pre_purchase_order` SET status = '" . (int)$order_status_id . "' WHERE purchase_order_id = '" . (int)$order_id . "'");

        //增加操作历史记录
        $user_id = $this->user->getId();
        $historySql = "
            INSERT INTO `oc_x_pre_purchase_order_history` (`purchase_order_id`, `status`, `checkout_status`, `date_added`, `added_by`)
            select purchase_order_id, '".(int)$order_status_id."', checkout_status, now(), '".$user_id."' from oc_x_pre_purchase_order where purchase_order_id = '".$order_id."'
        ";
        $this->db->query($historySql);

        if($order_status_id == 3){
            $sql = "select * from oc_x_supplier_transaction where purchase_order_id = " . $order_id . " and supplier_transaction_type_id = 3 ";
            $query = $this->db->query($sql);
            $result = $query->row;
            if(!empty($result)){
                $sql = "insert into oc_x_supplier_transaction set supplier_id = " . $result['supplier_id'] . ", purchase_order_id = " . $result['purchase_order_id'] . ", supplier_transaction_type_id = 9, description = '取消采购单', amount = '" . abs($result['amount']) . "', date_added = now(), added_by = '" . $user_id . "' , is_enabled = 1"; ;

                $query = $this->db->query($sql);
            }
        }

        return true;
    }


    public function getSupplierTypes(){
        $sql = "select * from " . DB_PREFIX . "x_supplier order by supplier_id asc";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getStatuses(){
        $sql = "select * from " . DB_PREFIX . "x_pre_purchase_order_status order by order_status_id asc";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getBds(){
        $sql = "select bd_id,bd_name from " . DB_PREFIX . "x_bd where status = 1 order by bd_id asc";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderProducts($order_id) {
        $sql = "SELECT A.order_product_id, A.order_id, A.product_id, A.name, A.model, A.quantity, A.price, A.total, A.tax, A.reward, A.price_ori, A.is_gift, A.shipping, A.status,
                    C.name cate_name, B.category_id, P.class, PC.print_name inv_class_name,P.weight,A.weight_inv_flag,P.shelf_life,P.weight_range_least,P.weight_range_most
                     FROM oc_order_product A
                     LEFT JOIN oc_product P ON A.product_id = P.product_id
                     LEFT JOIN oc_product_inv_class PC on P.inv_class = PC.product_inv_class_id
                     LEFT JOIN (select * from oc_product_to_category group by product_id) B ON A.product_id = B.product_id
                     LEFT JOIN oc_category_description C ON B.category_id = C.category_id and C.language_id = 2
                     WHERE A.order_id = '".(int)$order_id."'
                     ORDER BY P.inv_class ASC, B.category_id, A.product_id";
        $query = $this->db->query($sql);

        return $query->rows;
    }


    public function getPrePurchaseProduct($data){

        $sql = "SELECT
                    op2.product_id,
                    date_format(o2.date_added, '%Y-%m-%d') as o_date,
                    sum(op2.quantity) as op_quantity,
                    i.ori_inv,
                    i.s_quantity,
                    s.price,
                    s.turnover_factor,
                    s.supplier_unit_size,
                    p.name,
                    p.inv_size,
                    p.price sale_price,
                    p.real_cost,
                    s.supplier_order_quantity_type
                FROM ";


        $sql .= " (SELECT
                op.order_id,

            IF (
                ISNULL(ptp.product_id),
                op.product_id,
                ptp.product_id
            ) AS product_id,

            IF (
                ISNULL(ptp.product_id),
                CAST(op.quantity AS SIGNED),

            IF (
                ptp.transformation_product_purchase_type = 1,
                CAST(
                    op.quantity / ptp.transformation_factor AS SIGNED
                ),
                0
            )
            ) AS quantity

            FROM
                oc_order_product AS op
            LEFT JOIN oc_x_product_transformation_product AS ptp ON op.product_id = ptp.transformation_product_id
            LEFT JOIN oc_order AS o ON o.order_id = op.order_id
            WHERE
             date_format(o.date_added, '%Y-%m-%d') >= '" . $data['date_before'] . "'
            AND date_format(o.date_added, '%Y-%m-%d') <= '" . $data['date_end'] . "'
            and o.order_status_id != 3
            AND
            IF (
                ISNULL(ptp.product_id),
                CAST(op.quantity AS SIGNED),

            IF (
                ptp.transformation_product_purchase_type = 1,
                CAST(
                    op.quantity * ptp.transformation_factor AS SIGNED
                ),
                0
            )
            ) > 0) ";


        $sql .= " AS op2
            LEFT JOIN oc_order AS o2 ON o2.order_id = op2.order_id
            left join oc_product as p on p.product_id = op2.product_id
            left join oc_x_sku as s on s.sku_id = p.sku_id
            left join oc_x_supplier as sp on sp.supplier_id = s.supplier_id
            INNER JOIN (SELECT product_id, sum(if(order_id = 0, quantity, 0)) ori_inv, SUM(quantity) AS s_quantity FROM oc_x_inventory_move_item WHERE status=1 GROUP BY product_id) AS i ON p.product_id=i.product_id
            WHERE
                o2.order_status_id != 3
            AND date_format(o2.date_added, '%Y-%m-%d') >= '" . $data['date_before'] . "'
            AND date_format(o2.date_added, '%Y-%m-%d') <= '" . $data['date_end'] . "'
            and p.station_id = " . $data['station_id'] . "
            and sp.supplier_id = " . $data['supplier_type'];

        if($data['status_id'] != '*'){
            $sql .= " and p.status = " . $data['status_id'];
        }

        if(!empty($data['date_no_in'])){
            $date_no_in_arr = explode(",", $data['date_no_in']);

            $sql .= " and date_format(o2.date_added, '%Y-%m-%d') not in (";
            foreach($date_no_in_arr as $key=>$value){
                if($key < count($date_no_in_arr) - 1){
                    $sql .= "'" . $value . "',";
                }
                else{
                    $sql .= "'" . $value . "')";
                }

            }
        }
        $sql .= " group by op2.product_id,date_format(o2.date_added, '%Y-%m-%d')
            order by p.product_id,date_format(o2.date_added, '%Y-%m-%d')

            ";

        $query = $this->db->query($sql);
        $result = $query->rows;
        $return = array();
        foreach($result as $key=>$value){
            $return[$value['product_id']][$value['o_date']] = $value;
        }


        return $return;
    }


    public function getPrePurchaseOrderProduct($data){

        $this_date = date("Y-m-d", time()+8*3600);
        $sql = "SELECT
                    o.date_deliver,op.product_id,sum(op.quantity) as quantity
                FROM
                    oc_x_pre_purchase_order AS o
                LEFT JOIN oc_x_pre_purchase_order_product AS op ON o.purchase_order_id = op.purchase_order_id
                left join oc_product as p on p.product_id = op.product_id
                WHERE";
        if($data['s_quantity_status_id'] == 1){
            $sql .=	" o.date_deliver > '" . $this_date . "'";
        }
        else{
            $sql .=	" o.date_deliver >= '" . $this_date . "'";
        }
        $sql .= " AND o.date_deliver <= '" . $data['date_deliver'] . "'
                and p.station_id = " . $data['station_id'] . "
                and o.supplier_type = " . $data['supplier_type'] ;

        if($data['status_id'] != "*"){
            $sql .= " and p.status = " . $data['status_id'];
        }

        $sql .= " and o.`status` not in (1,3)
                group by o.date_deliver,op.product_id";

        $query = $this->db->query($sql);
        $result = $query->rows;
        $return = array();
        $return[1] = array();
        $return[2] = array();
        foreach($result as $key=>$value){
            $return[1][$value['product_id']][$value['date_deliver']] = $value;
            if(isset($return[2][$value['product_id']])){
                $return[2][$value['product_id']] += $value['quantity'];
            }
            else{
                $return[2][$value['product_id']] = $value['quantity'];
            }

        }

        return $return;

    }


    public function getPrePurchaseAlert($supplier_id){
        $sql = "select
                    x.product_id,
                    x.sku_id,
                    x.name,
                    x.sale_price,
                    x.real_cost,
                    x.price,
                    x.class,
                    x.inv_size,
                    x.supplier_unit_size,
                    x.supplier_order_quantity_type,
                    if(x.ori_inv is null, 0, x.ori_inv) ori_inv,
                    if(x.curr_inv is null, 0, x.curr_inv) curr_inv,
                    if(x.avg_qty is null, 0, x.avg_qty) avg_qty,
                    if(x.max_qty is null, 0, x.max_qty) max_qty,
                    if(x.min_qty is null, 0, x.min_qty) min_qty,
                    if(x.notice_3days is null, 0, x.notice_3days) notice_3days,
                    if(x.notice_5days is null, 0, x.notice_5days) notice_5days,
                    if(x.inv_in_3days is null, 0, x.inv_in_3days) inv_in_3days,
                    if(x.inv_in_5days is null, 0, x.inv_in_5days) inv_in_5days,
                    x.turnover_factor,
                    x.supplier_id,
                    if(x.notice_3days>=0 or x.notice_3days is null, 0, x.notice_3days) suggest_inv_amount,
                    if(x.supplier_order_quantity_type =1,
                        if(x.notice_3days>=0 or x.notice_3days is null, 0, abs(x.notice_3days))*x.inv_size / x.supplier_unit_size,
                        if(x.notice_3days>=0 or x.notice_3days is null, 0, abs(x.notice_3days))*x.inv_size)  suggest_supplier_amount
                from (
                    select
                    P.product_id, PD.name, P.price sale_price, P.real_cost, S.price, P.class, P.inv_size, P.sku_id, S.supplier_unit_size, S.supplier_order_quantity_type,
                    SUBSTRING_INDEX(GROUP_CONCAT(M.quantity ORDER BY inventory_move_id),',',1) ori_inv,  sum(M.quantity) curr_inv,
                    OT.avg_qty, OT.max_qty, OT.min_qty,
                    sum(M.quantity)-OT.avg_qty*3+if(PO.purchase_order_qty_3day is null, 0,  PO.purchase_order_qty_3day) notice_3days,
                    sum(M.quantity)-OT.avg_qty*5+if(PO.purchase_order_qty_5day is null, 0,  PO.purchase_order_qty_5day) notice_5days,
                    if(PO.purchase_order_qty_3day is null, 0,  PO.purchase_order_qty_3day) inv_in_3days,
                    if(PO.purchase_order_qty_5day is null, 0,  PO.purchase_order_qty_5day) inv_in_5days,
                    S.turnover_factor,
                    SP.supplier_id,SP.name supplier_name
                    from xsjb2b.oc_product P
                    left join xsjb2b.oc_product_description PD on P.product_id = PD.product_id
                    left join xsjb2b.oc_x_inventory_move_item M on P.product_id = M.product_id and M.status = 1
                    left join xsjb2b.oc_x_sku S on P.sku_id = S.sku_id
                    left join xsjb2b.oc_x_supplier SP on S.supplier_id  = SP.supplier_id
                    left join xsjb2b.oc_x_analysis_product_sale OT on P.product_id = OT.product_id and OT.date= date_sub(current_date(), interval 1 day)
                    left join (
                        SELECT
                            op.product_id,
                            sum(op.quantity*if(o.order_type=1,1,-1)) purchase_order_qty, sum(smi.quantity) inv_in,
                            sum(if(date(o.date_deliver) between  date_add(current_date(), interval 0 day) and date_add(current_date(), interval 0 day), op.quantity, 0)*if(o.order_type  = 1, 1, -1))  purchase_order_qty_1day,
                            sum(if(date(o.date_deliver) between  date_add(current_date(), interval 0 day) and date_add(current_date(), interval 1 day), op.quantity, 0)*if(o.order_type  = 1, 1, -1))  purchase_order_qty_2day,
                            sum(if(date(o.date_deliver) between  date_add(current_date(), interval 0 day) and date_add(current_date(), interval 2 day), op.quantity, 0)*if(o.order_type  = 1, 1, -1))  purchase_order_qty_3day,
                            sum(if(date(o.date_deliver) between  date_add(current_date(), interval 0 day) and date_add(current_date(), interval 3 day), op.quantity, 0)*if(o.order_type  = 1, 1, -1))  purchase_order_qty_4day,
                            sum(if(date(o.date_deliver) between  date_add(current_date(), interval 0 day) and date_add(current_date(), interval 4 day), op.quantity, 0)*if(o.order_type  = 1, 1, -1))  purchase_order_qty_5day
                        FROM xsjb2b.oc_x_pre_purchase_order AS o
                        LEFT JOIN xsjb2b.oc_x_pre_purchase_order_product AS op ON o.purchase_order_id = op.purchase_order_id
                        LEFT JOIN xsjb2b.oc_product AS p ON p.product_id = op.product_id
                        LEFT JOIN xsjb2b.oc_x_stock_move AS xsm ON xsm.purchase_order_id = o.purchase_order_id
                        LEFT JOIN xsjb2b.oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id AND smi.product_id = op.product_id
                        WHERE
                            o.status != 3
                            AND date(o.date_deliver) between  date_add(current_date(), interval 0 day) and date_add(current_date(), interval 4 day)
                            AND p.station_id = 2 and p.instock = 1
                        GROUP BY op.product_id
                    ) PO on P.product_id = PO.product_id
                    where P.station_id = 2 and S.supplier_id = '".$supplier_id."'
                    group by P.product_id
                ) x
                order by suggest_inv_amount asc";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function editOrderCheckoutStatus($order_id, $checkout_ope) {
        $sql = "select purchase_order_id, status, checkout_status from oc_x_pre_purchase_order where purchase_order_id = " . $order_id;
        $query = $this->db->query($sql);
        $order_info = $query->row;

        //取消订单不可支付
        if($order_info['status'] == 3 && (int)$checkout_ope == 2){
            return false;
        }

        //增加操作历史记录
        $user_id = $this->user->getId();
        $historySql = "
            INSERT INTO `oc_x_pre_purchase_order_history` (`purchase_order_id`, `status`, `checkout_status`, `date_added`, `added_by`)
            select purchase_order_id, status, '".(int)$checkout_ope."', now(), '".$user_id."' from oc_x_pre_purchase_order where purchase_order_id = '".(int)$order_id."'
        ";
        $this->db->query($historySql);

        $this->db->query("UPDATE `" . DB_PREFIX . "x_pre_purchase_order` SET checkout_status = '" . (int)$checkout_ope . "',checkout_time = NOW(),checkout_user_id = " . $user_id . " WHERE purchase_order_id = '" . (int)$order_id . "'");

        return true;
    }

    public function getPrePurchaseSuplierCheckoutInfo($supplier_id){
        $sql = "select checkout_username,checkout_userbank,checkout_usercard,invoice_flag,checkout_type_id from oc_x_supplier where supplier_id = " . $supplier_id;
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function addPrePurchaseOrder($data){



        $date_added = date("Y-m-d H:i:s", time() + 8 * 3600);
        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName();
        $products = $data['products'];
        $warehouse_id = !empty($data['warehouse_id']) ? (int)$data['warehouse_id'] : 0;

        $product_ids = array();
        if(!empty($data['products'])){
            foreach ($data['products'] as $key=>$value){
                $product_ids[] = $value['product_id'];
            }
        }
        $product_id_str = implode(",", $product_ids);
        $sku_weight_arr = array();
        $sql = "SELECT
                        s.sku_id,
                        p.product_id,
                    s.weight,
                    s.weight_class_id,
                        s.price
                FROM
                    oc_x_sku AS s
                LEFT JOIN oc_product AS p ON s.sku_id = p.sku_id
                WHERE
                    p.product_id IN(" . $product_id_str . ")";
        $query = $this->db->query($sql);
        $result = $query->rows;
        foreach($result as $key=>$value){
            $sku_weight_arr[$value['product_id']] = $value;
        }

        $sql = "SELECT
                        *
                FROM
                    oc_x_supplier
                WHERE
                    supplier_id  = " . $data['supplier_type'] . "";
        $query = $this->db->query($sql);
        $supplier_info = $query->row;

        //计算账期时间
        $sql = "select checkout_cycle_id,checkout_cycle_num,checkout_cycle_date from " . DB_PREFIX . "x_supplier  where supplier_id = '{$data['supplier_type']}'";
        $query = $this->db->query($sql)->rows;

        $checkout_cycle_num = $query[0]['checkout_cycle_num'];
        $checkout_cycle_date = $query[0]['checkout_cycle_date'];

        if(!$checkout_cycle_date || $query[0]['checkout_cycle_id'] == 1){
            switch($query[0]['checkout_cycle_id']){
                case 3:
                    $date_num = "'+ ". $checkout_cycle_num ." month'";
                    $check_plan_date = date('Y-m-d',strtotime(trim($date_num,"''")));
                    break;
                case 2:
                    $date_num = "'+ ". $checkout_cycle_num ." week'";
                    $check_plan_date = date('Y-m-d',strtotime(trim($date_num,"''")));
                    break;
                default:
                    $date_num = "'+ ". $checkout_cycle_num ." day'";
                    $check_plan_date = date('Y-m-d',strtotime(trim($date_num,"''")));
                    break;
            }
        }else{
            switch($query[0]['checkout_cycle_id']){
                case 3:
                    $date_num = "'Y-m-".$checkout_cycle_date."'";
                    $check_plan_date = date(trim($date_num,"'"),strtotime('+1 month'));
                    break;
                case 2:
                    switch($checkout_cycle_date){
                        case 0:
                            $check_plan_date = date('Y-m-d',strtotime("next week sunday"));
                            break;
                        case 1:
                            $check_plan_date = date('Y-m-d',strtotime("next week monday"));
                            break;
                        case 2:
                            $check_plan_date = date('Y-m-d',strtotime("next week tuesday "));
                            break;
                        case 3:
                            $check_plan_date = date('Y-m-d',strtotime("next week wednesday "));
                            break;
                        case 4:
                            $check_plan_date = date('Y-m-d',strtotime("next week thursday "));
                            break;
                        case 5:
                            $check_plan_date = date('Y-m-d',strtotime("next week friday "));
                            break;
                        case 6:
                            $check_plan_date = date('Y-m-d',strtotime("next week Saturday "));
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    break;
            }
        }

        $this->db->query('START TRANSACTION');
        $bool = 1;

        //增加采购记录
        $sql = "INSERT INTO `oc_x_pre_purchase_order` (`station_id`, `date_added`, `date_deliver`,  `added_by`, `add_user_name`,supplier_type,checkout_type_id,checkout_cycle_id,checkout_cycle_num,checkout_plan_date,checkout_username,checkout_userbank,checkout_usercard,order_comment,invoice_flag, warehouse_id) VALUES('{$data['station_id']}', '{$date_added}', '{$data['date_deliver']}', '{$user_id}', '{$user_name}','{$data['supplier_type']}','{$supplier_info['checkout_type_id']}','{$supplier_info['checkout_cycle_id']}','{$supplier_info['checkout_cycle_num']}','{$check_plan_date}','{$supplier_info['checkout_username']}','{$supplier_info['checkout_userbank']}','{$supplier_info['checkout_usercard']}','{$data['order_comment']}','{$data['invoice_flag']}',{$warehouse_id})";

        $bool = $bool && $this->db->query($sql);

        $purchase_order_id = $this->db->getLastId();

        //增加采购
        $sql = "INSERT INTO `oc_x_pre_purchase_order_product` (`purchase_order_id`, station_id, `sku_id`, `product_id`, `weight`, `weight_class_id`, `date`,`price`,`real_cost`,quantity,supplier_quantity,quantity_old,warehouse_id) VALUES ";
        $m=0;


        foreach($products as $key=>$value){
            if($value['quantity'] == 0){
                unset($products[$key]);
            }
        }

        $order_total = 0;
        foreach($products as $product){
            $sql .= "(".$purchase_order_id."," . $data['station_id'] . ", '".$sku_weight_arr[$product['product_id']]['sku_id'] ."'," . $product['product_id'] . " , '" . $sku_weight_arr[$product['product_id']]['weight'] . "', '" . $sku_weight_arr[$product['product_id']]['weight_class_id'] . "', '".$data['date_deliver']."', '" . (!empty($product['price']) ? $product['price'] : 0) . "','".(!empty($product['real_cost']) ? $product['real_cost'] : 0). "'," . (isset($product['quantity']) ? $product['quantity'] : 0) . ", " . (!empty($product['supplier_quantity']) ? $product['supplier_quantity'] : 0) . "," . (!empty($product['purchase_quantity_old']) ? $product['purchase_quantity_old'] : 0) . ", {$warehouse_id})";
            if(++$m < sizeof($products)){
                $sql .= ', ';
            }
            else{
                $sql .= ';';
            }
            $order_total += (!empty($product['price']) ? $product['price'] : 0) * (!empty($product['supplier_quantity']) ? $product['supplier_quantity'] : 0);
        }

        $bool = $bool && $this->db->query($sql);

        //添加余额支付记录
        $use_credits = 0;
        $order_checkout_status = 1;
        //供应商账户余额
        $this->load->model('catalog/supplier');
        $supplier_credits = $this->model_catalog_supplier->getTransactionTotal($data['supplier_type'],1,1);

        if(isset($data['sub_use_credits']) && $data['sub_use_credits'] == 1 && $supplier_credits > 0){

            $order_checkout_status = $supplier_credits < $order_total ? $order_checkout_status : 2;

            $use_credits = $supplier_credits < $order_total ? $supplier_credits : $order_total;
            $sql = "insert into oc_x_supplier_transaction set supplier_id = " . $data['supplier_type'] . ", purchase_order_id = " . $purchase_order_id . ", supplier_transaction_type_id = 3, description = '采购单支付', amount = '-" . $use_credits . "', date_added = now(), added_by = '" . $user_id . "' , is_enabled = 1"; ;

            $query = $this->db->query($sql);

        }



        $sql = "update oc_x_pre_purchase_order set order_total = '" . $order_total . "',use_credits_total = '" . $use_credits . "', checkout_status = " . $order_checkout_status ;
        if($order_checkout_status == 2){
            $sql .= ", checkout_time = now() ";
        }
        $sql .= " where purchase_order_id = " . $purchase_order_id;

        $bool = $bool && $this->db->query($sql);
        //$sql = "update oc_x_purchase_order_product A left join oc_product B on A.product_id = B.product_id set A.sku_id = B.sku_id, A.weight_class_id = B.weight_class_id, A.weight = B.weight where A.date = '".$date."' and A.status = 1";
        //$bool = $bool && $this->db->query($sql);

        //更新商品（估算的）实际采购成本
        $sql = "update oc_product p inner join oc_x_pre_purchase_order_product pop on p.product_id = pop.product_id
            set p.real_cost = pop.real_cost
            where pop.purchase_order_id = '". $purchase_order_id ."'";
        $bool = $bool && $this->db->query($sql);

        if($bool){
            $this->db->query('COMMIT');
        }
        else{
            $this->db->query("ROLLBACK");
        }

        return $bool;
    }


    public function addPrePurchaseOrderReturn($data){



        $date_added = date("Y-m-d H:i:s", time() + 8 * 3600);
        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName();
        $products = $data['products'];

        $product_ids = array();
        if(!empty($data['products'])){
            foreach ($data['products'] as $key=>$value){

                $product_ids[] = $value['product_id'];
            }
        }
        $product_id_str = implode(",", $product_ids);
        $sku_weight_arr = array();
        $sql = "SELECT
    s.sku_id,
    p.product_id,
s.weight,
s.weight_class_id,
    s.price
FROM
oc_x_sku AS s
LEFT JOIN oc_product AS p ON s.sku_id = p.sku_id
WHERE
p.product_id IN(" . $product_id_str . ")";
        $query = $this->db->query($sql);
        $result = $query->rows;
        foreach($result as $key=>$value){
            $sku_weight_arr[$value['product_id']] = $value;
        }

        $sql = "SELECT
    *
FROM
oc_x_supplier
WHERE
supplier_id  = " . $data['supplier_type'] . "";
        $query = $this->db->query($sql);
        $supplier_info = $query->row;



        $this->db->query('START TRANSACTION');
        $bool = 1;

        //增加采购记录
        $sql = "INSERT INTO `oc_x_pre_purchase_order` (`station_id`, `date_added`, `date_deliver`,  `added_by`, `add_user_name`,supplier_type,checkout_type_id,checkout_cycle_id,checkout_cycle_num,checkout_username,checkout_userbank,checkout_usercard,order_comment,invoice_flag,order_type,related_order) VALUES('{$data['station_id']}', '{$date_added}', '{$data['date_deliver']}', '{$user_id}', '{$user_name}','{$data['supplier_type']}','{$supplier_info['checkout_type_id']}','{$supplier_info['checkout_cycle_id']}','{$supplier_info['checkout_cycle_num']}','{$supplier_info['checkout_username']}','{$supplier_info['checkout_userbank']}','{$supplier_info['checkout_usercard']}','{$data['order_comment']}','0','{$data['order_type']}','{$data['purchase_order_id']}')";

        $bool = $bool && $this->db->query($sql);

        $purchase_order_id = $this->db->getLastId();

        //增加采购
        $sql = "INSERT INTO `oc_x_pre_purchase_order_product` (`purchase_order_id`, station_id, `sku_id`, `product_id`, `weight`, `weight_class_id`, `date`,`price`,quantity,supplier_quantity,quantity_old) VALUES ";
        $m=0;


        foreach($products as $key=>$value){
            if($value['quantity'] == 0){
                unset($products[$key]);
            }
        }

        $order_total = 0;
        foreach($products as $product){
            $sql .= "(".$purchase_order_id."," . $data['station_id'] . ", '".$sku_weight_arr[$product['product_id']]['sku_id'] ."'," . $product['product_id'] . " , '" . $sku_weight_arr[$product['product_id']]['weight'] . "', '" . $sku_weight_arr[$product['product_id']]['weight_class_id'] . "', '".$data['date_deliver']."', '" . (!empty($product['price']) ? $product['price'] : 0) . "'," . (isset($product['quantity']) ? $product['quantity'] : 0) . ", " . (!empty($product['supplier_quantity']) ? $product['supplier_quantity'] : 0) . "," . (!empty($product['purchase_quantity_old']) ? $product['purchase_quantity_old'] : 0) . ")";
            if(++$m < sizeof($products)){
                $sql .= ', ';
            }
            else{
                $sql .= ';';
            }
            $order_total += (!empty($product['price']) ? $product['price'] : 0) * (!empty($product['supplier_quantity']) ? $product['supplier_quantity'] : 0);
        }

        $bool = $bool && $this->db->query($sql);

        //添加退余额记录
        $use_credits = 0;
        $order_checkout_status = 2;
        //供应商账户余额
        $this->load->model('catalog/supplier');

        if(isset($data['sub_use_credits']) && $data['sub_use_credits'] == 1 && $order_total > 0){


            $use_credits =  $order_total;
            $sql = "insert into oc_x_supplier_transaction set supplier_id = " . $data['supplier_type'] . ", purchase_order_id = " . $purchase_order_id . ", supplier_transaction_type_id = 6, description = '采购单退货', amount = '" . $use_credits . "', date_added = now(), added_by = '" . $user_id . "' , is_enabled = 1"; ;

            $query = $this->db->query($sql);

        }



        $sql = "update oc_x_pre_purchase_order set order_total = '" . $order_total . "',use_credits_total = '" . $use_credits . "', checkout_status = " . $order_checkout_status ;
        if($order_checkout_status == 2){
            $sql .= ", checkout_time = now() ";
        }
        $sql .= " where purchase_order_id = " . $purchase_order_id;

        $bool = $bool && $this->db->query($sql);
        //$sql = "update oc_x_purchase_order_product A left join oc_product B on A.product_id = B.product_id set A.sku_id = B.sku_id, A.weight_class_id = B.weight_class_id, A.weight = B.weight where A.date = '".$date."' and A.status = 1";
        //$bool = $bool && $this->db->query($sql);

        if($bool){
            $this->db->query('COMMIT');
        }
        else{
            $this->db->query("ROLLBACK");
        }

        return $bool;
    }


    public function editPrePurchaseOrder($data){

        $sql = "select status,checkout_type_id from oc_x_pre_purchase_order where purchase_order_id = " . $data['purchase_order_id'];
        $query = $this->db->query($sql);
        $result = $query->row;

        if(!in_array($result['status'], array(4,5))){
            return false;
        }
        if($result['checkout_type_id'] == 1){
            $sql = "update oc_x_pre_purchase_order set checkout_status = 2 where purchase_order_id = " . $data['purchase_order_id'];

            $bool = $this->db->query($sql);
        }



        //判断如果缺货则退余额
        if($result['checkout_type_id'] == 3){
            //判断是否已支付,有余额记录
        }


        $this->db->query("DELETE FROM " . DB_PREFIX . "x_pre_purchase_order_image WHERE purchase_order_id = '" . (int)$data['purchase_order_id'] . "'");

        if (isset($data['product_image'])) {
            foreach ($data['product_image'] as $product_image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "x_pre_purchase_order_image SET purchase_order_id = '" . (int)$data['purchase_order_id'] . "', image = '" . $this->db->escape($product_image['image']) .  "', image_title = '" . $this->db->escape($product_image['image_title']) .  "', image_num = '" . $this->db->escape($product_image['image_num']) .  "'");
            }
        }


        //修改采购单状态
        $sql = "update oc_x_pre_purchase_order set status = 5 where purchase_order_id = " . $data['purchase_order_id'];

        $bool = $this->db->query($sql);



        return $bool;
    }

    public function getOrderProductsWeightInv($order_id) {
        $sql = "SELECT  A.product_id,  A.quantity, A.weight_total, A.total
                     FROM oc_order_product_weight_inv as A
                     WHERE A.order_id = '".(int)$order_id."'";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderInvProducts($order_id){
        $sql = "SELECT
                    xsm.order_id,xsmi.product_id,xsmi.quantity
                FROM
                    oc_x_stock_move AS xsm
                LEFT JOIN oc_x_stock_move_item AS xsmi ON xsm.inventory_move_id = xsmi.inventory_move_id
                WHERE
                    xsm.order_id = " . $order_id . "
                AND xsm.inventory_type_id = 12";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderOption($order_id, $order_option_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_option_id = '" . (int)$order_option_id . "'");

        return $query->row;
    }

    public function getOrderOptions($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

        return $query->rows;
    }

    public function getOrderVouchers($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

        return $query->rows;
    }

    public function getOrderVoucherByVoucherId($voucher_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE voucher_id = '" . (int)$voucher_id . "'");

        return $query->row;
    }

    public function getOrderTotals($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

        return $query->rows;
    }

    public function getOrderReturn($order_id) {

        $query = $this->db->query("SELECT sum(order_total) as sum_return FROM " . DB_PREFIX . "x_pre_purchase_order WHERE related_order = '" . (int)$order_id . "' and status = 2 ");

        return $query->row;
    }

    public function getTotalOrders($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "x_pre_purchase_order` where 1=1 ";


        if (!empty($data['filter_supplier_type'])) {
            $sql .= " AND supplier_type = '" . (int)$data['filter_supplier_type'] . "'";
        }
        if (!empty($data['filter_order_type'])) {
            if($data['filter_order_type'] == 3){
               $sql .=" AND purchase_order_id IN (
                        SELECT related_order AS purchase_order_id FROM oc_x_pre_purchase_order WHERE order_type = ". (int)$data['filter_order_type'] ."
                    )";
            }else{
                $sql .= " AND order_type = '" . (int)$data['filter_order_type'] . "'";
            }
        }
        if (!empty($data['filter_purchase_order_id'])) {
            $sql .= " AND (purchase_order_id = '" . (int)$data['filter_purchase_order_id'] . "' or related_order = '" . (int)$data['filter_purchase_order_id'] . "')";
        }

        if (!empty($data['filter_purchase_person_id'])) {
            $sql .= " AND added_by = '". (int)$data['filter_purchase_person_id'] ."'";
        }

        if (!empty($data['filter_date_deliver'])) {
            $sql .= " AND DATE(date_deliver) >= DATE('" . $this->db->escape($data['filter_date_deliver']) . "')";
        }
        if (!empty($data['filter_date_deliver_end'])) {
            $sql .= " AND DATE(date_deliver) <= DATE('" . $this->db->escape($data['filter_date_deliver_end']) . "')";
        }
        if (!empty($data['filter_order_status_id'])) {
            $sql .= " AND status = '" . (int)$data['filter_order_status_id'] . "'";
        }
        if (!empty($data['filter_order_checkout_status_id'])) {
            $sql .= " AND checkout_status = '" . (int)$data['filter_order_checkout_status_id'] . "'";
        }
        if (!empty($data['filter_warehouse_id'])) {
            $sql .= " AND warehouse_id = ". (int)$data['filter_warehouse_id'];
        }
        if (!empty($data['remote_order_type'])){
            $sql .= " AND order_type != ". (int)$data['remote_order_type'];
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTotalOrdersByStoreId($store_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE store_id = '" . (int)$store_id . "'");

        return $query->row['total'];
    }

    public function getProduceGroup() {
        $query = $this->db->query("SELECT produce_group_id, title, memo FROM oc_x_produce_group WHERE status = 1");

        return $query->rows;
    }

    public function getTotalOrdersByOrderStatusId($order_status_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$order_status_id . "' AND order_status_id > '0'");

        return $query->row['total'];
    }

    public function getTotalOrdersByProcessingStatus() {
        $implode = array();

        $order_statuses = $this->config->get('config_processing_status');

        foreach ($order_statuses as $order_status_id) {
            $implode[] = "order_status_id = '" . (int)$order_status_id . "'";
        }

        if ($implode) {
            $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode));

            return $query->row['total'];
        } else {
            return 0;
        }
    }

    public function getTotalOrdersByCompleteStatus() {
        $implode = array();

        $order_statuses = $this->config->get('config_complete_status');

        foreach ($order_statuses as $order_status_id) {
            $implode[] = "order_status_id = '" . (int)$order_status_id . "'";
        }

        if ($implode) {
            $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode) . "");

            return $query->row['total'];
        } else {
            return 0;
        }
    }

    public function getTotalOrdersByLanguageId($language_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE language_id = '" . (int)$language_id . "' AND order_status_id > '0'");

        return $query->row['total'];
    }

    public function getTotalOrdersByCurrencyId($currency_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE currency_id = '" . (int)$currency_id . "' AND order_status_id > '0'");

        return $query->row['total'];
    }

    public function createInvoiceNo($order_id) {
        $order_info = $this->getOrder($order_id);

        if ($order_info && !$order_info['invoice_no']) {
            $query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");

            if ($query->row['invoice_no']) {
                $invoice_no = $query->row['invoice_no'] + 1;
            } else {
                $invoice_no = 1;
            }

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");

            return $order_info['invoice_prefix'] . $invoice_no;
        }
    }

    public function getOrderHistories($order_id, $start = 0, $limit = 10) {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);

        return $query->rows;
    }

    public function getTotalOrderHistories($order_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");

        return $query->row['total'];
    }

    public function getTotalOrderHistoriesByOrderStatusId($order_status_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int)$order_status_id . "'");

        return $query->row['total'];
    }

    public function getEmailsByProductsOrdered($products, $start, $end) {
        $implode = array();

        foreach ($products as $product_id) {
            $implode[] = "op.product_id = '" . (int)$product_id . "'";
        }

        $query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0' LIMIT " . (int)$start . "," . (int)$end);

        return $query->rows;
    }

    public function getTotalEmailsByProductsOrdered($products) {
        $implode = array();

        foreach ($products as $product_id) {
            $implode[] = "op.product_id = '" . (int)$product_id . "'";
        }

        $query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");

        return $query->row['total'];
    }

    public function getOrderDeliverStatus() {
        $query = $this->db->query("SELECT order_deliver_status_id,name FROM " . DB_PREFIX . "order_deliver_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getOrderPaymentStatus() {
        $query = $this->db->query("SELECT order_payment_status_id,name FROM " . DB_PREFIX . "order_payment_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }
    public function getOrderPaymentStatues() {
        $query = $this->db->query("SELECT order_payment_status_id,name FROM " . DB_PREFIX . "order_payment_status WHERE language_id = '" . (int) $this->config->get('config_language_id') . "' order by order_payment_status_id asc");

        return $query->rows;
    }
    public function getProductInvclasses() {
        $query = $this->db->query("SELECT product_inv_class_id,name FROM " . DB_PREFIX . "product_inv_class order by product_inv_class_id asc");

        return $query->rows;
    }

    public function getOrderInventoryProduct($order_id){
        /*
        $sql = "SELECT
	op.order_id,SUM(op.quantity) as quantity
FROM
	oc_order_product AS op
WHERE op.order_id in (" . $order_id_str . ") group by op.order_id";
        $query = $this->db->query($sql);
        $results = $query->rows;
        $orders = array();
        foreach($results as $key=>$value){
            $orders[$value['order_id']]['order_quantity'] = $value['quantity'];
            $orders[$value['order_id']]['has_ooc'] = 0;
}
        */


        $orders = array();
        $stock_result = array();
        $sql = "SELECT
	sm.order_id,
        smi.product_id,
	sum(smi.quantity) as stock_quantity,
        sum(smi.weight) as stock_weight
FROM
	oc_x_stock_move AS sm
LEFT JOIN oc_x_stock_move_item AS smi ON sm.inventory_move_id = smi.inventory_move_id
WHERE
	sm.order_id = " . $order_id . "
AND sm.inventory_type_id = 12
GROUP BY
	smi.product_id";

        $query = $this->db->query($sql);
        $stock_result = $query->rows;

        if(!empty($stock_result)){
            foreach($stock_result as $srk=>$srv){
                $orders[$srv['product_id']]['stock_quantity'] = $srv['stock_quantity'];
                $orders[$srv['product_id']]['stock_weight'] = $srv['stock_weight'];

            }
        }

        return $orders;
    }

    public function getContainerMove($order_id){

        $sql = "SELECT
	f.container_id,f.type,fl.order_id,ct.type_name,fl.date_added,o.shipping_firstname,o.shipping_address_1
FROM
	oc_x_container_move as fl
left join oc_x_container as f on f.container_id = fl.container_id
left join oc_x_container_type as ct on ct.type_id = f.type
left join oc_order as o on o.order_id = fl.order_id
WHERE
	fl.order_id = " . $order_id . "
GROUP BY
	container_id
HAVING
	sum(move_type) = 1
order by fl.container_move_id desc
";
        $query = $this->db->query($sql);
        $result = array();
        $result = $query->rows;

        return $result;
    }

    public function getPurchaseOrderProducts($order_id) {
        $sql = "select  sp.name suplier_name,date(o.date_added) date_order,o.purchase_order_id, o.order_total, p.product_id, p.quantity, s.supplier_id, s.name, op.sku, s.supplier_unit_size, p.price, wcd.title, p.quantity, p.quantity*p.price p_price_total
            from oc_x_pre_purchase_order o
            left join oc_x_pre_purchase_order_product p on p.purchase_order_id = o.purchase_order_id
            left join oc_product op on op.product_id = p.product_id
            left join oc_weight_class_description wcd on wcd.weight_class_id = op.weight_class_id
            left join oc_x_sku s on s.sku_id = p.sku_id
            left join oc_x_supplier sp on s.supplier_id = sp.supplier_id
            where o.purchase_order_id = '".(int)$order_id."'";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getPurchaseOrderInfo($order_id) {
        $sql = "SELECT
            o.purchase_order_id,
            date(o.date_added) AS order_data,
            date(o.date_deliver) plan_receive,
            date(xsm.date_added) AS real_receive,
            s.supplier_id,
            s.`name`,
            concat('仓库：',ocs.adderss) adderss,
            concat('仓库联系人：',ocs.contact_name,' ',ocs.contact_phone) station_admin
            FROM oc_x_pre_purchase_order AS o
            LEFT JOIN oc_user u on o.checkout_user_id = u.user_id
            LEFT JOIN oc_x_supplier_checkout_cycle scc on o.checkout_cycle_id = scc.checkout_cycle_id
            LEFT JOIN oc_x_supplier_checkout_type sct on o.checkout_type_id = sct.checkout_type_id
            LEFT JOIN oc_x_pre_purchase_order_status os on o.status = os.order_status_id
            LEFT JOIN oc_x_pre_purchase_order_product AS op ON o.purchase_order_id = op.purchase_order_id
            LEFT JOIN oc_x_supplier AS s ON o.supplier_type = s.supplier_id
            LEFT JOIN oc_x_stock_move AS xsm ON xsm.purchase_order_id = o.purchase_order_id
            LEFT JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id AND smi.product_id = op.product_id
            LEFT JOIN oc_x_station AS ocs ON ocs.station_id = o.station_id
            WHERE
                    o.purchase_order_id = '".(int)$order_id."'
            GROUP BY
                    o.purchase_order_id
            ORDER BY
                    o.purchase_order_id ";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    function getPurchaseOrderList($data = array(), $start = 0, $limit = 10){
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }
        $sql = "select o.purchase_order_id,s.name,concat(u.lastname,firstname) username
            from oc_x_pre_purchase_order o
            left join oc_x_supplier s on s.supplier_id = o.supplier_type
            left join oc_user u on u.user_id = o.added_by where 1";

        if($data['supplier_type']){
            $sql .= " and o.supplier_type = '".(int)$data['supplier_type']."'";
        }

        if($data['filter_order_status_id']){
            $sql .= " and o.status = '".(int)$data['filter_order_status_id']."'";
        }

        if($data['station_id']){
            $sql .= " and o.station_id = '".(int)$data['station_id']."'";
        }

        if($data['date_deliver']){
            $sql .= " and o.date_deliver = '".$data['date_deliver']."'";
        }

        $sql .= " order by o.purchase_order_id desc";

        $sql .= " limit ".$start.",".$limit;

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalPurchaseOrderList($data = array()){
        $sql = "select count(*) as total
            from oc_x_pre_purchase_order o
            left join oc_x_supplier s on s.supplier_id = o.supplier_type
            left join oc_user u on u.user_id = o.added_by where 1";

        if($data['supplier_type']){
            $sql .= " and o.supplier_type = '".(int)$data['supplier_type']."'";
        }

        if($data['filter_order_status_id']){
            $sql .= " and o.status = '".(int)$data['filter_order_status_id']."'";
        }

        if($data['station_id']){
            $sql .= " and o.station_id = '".(int)$data['station_id']."'";
        }

        if($data['date_deliver']){
            $sql .= " and o.date_deliver = '".(int)$data['date_deliver']."'";
        }

        $sql .= " order by o.purchase_order_id desc";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

}