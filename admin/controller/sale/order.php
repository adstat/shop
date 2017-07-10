<?php

class ControllerSaleOrder extends Controller {

    private $error = array();

    public function index() {
        
        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        $this->getList();
    }

    public function download() {

        $type = 'PO'; //PO for Purchase Order, PSR for Pickup-spot Receipt
        if (isset($_REQUEST['type'])) {
            $type = $_REQUEST['type'];
        }

        $deliver_date = date('H-i-s');
        if (isset($_REQUEST['deliver_date'])) {
            $deliver_date = $_REQUEST['deliver_date'];
        }

        $filter_customer_group = isset($_REQUEST['filter_customer_group']) ? $_REQUEST['filter_customer_group'] : false;
        $filter_station = isset($_REQUEST['filter_station']) ? $_REQUEST['filter_station'] : false;

        $data["deliver_date"] = $deliver_date;
        $data["filter_customer_group"] = $filter_customer_group;
        $data["filter_station"] = $filter_station;
        $data['base'] = HTTP_SERVER;
        $data['token'] = $this->session->data['token'];
        $data['filter_type'] = $type;

        //TODO 前天11:59（或根据系统截单时间）自动生成不同单据（采购订单和自提点签收单），等待下载
        //TODO 可以直接生成？

        if ($type == 'PO') {
            $sql = "select
                p.sku_id,
                s.name sku_name,
                p.sku,
                b.product_id,
                b.name,
                p.image,
                pcd.name category,
                pcdp.name parent_category,
                round(avg(b.price),2) price,
                concat( round(min(b.price),2),'~',round(max(b.price),2) ) price_range,
                round(p.weight,0) product_weight,
                if(p.weight_class_id=1, 1, 0) by_gram,
                concat(round(p.weight,0), wd.title) product_unit,
                sum(b.quantity) qty_total,
                sum( if((a.payment_code = 'WXPAY' and a.order_payment_status_id = 1),b.quantity,0) ) unpaid_qty_total,
                concat(round(p.weight,0), wd.title, ' x', sum(b.quantity)) purchase
                from oc_order a
                left join oc_order_product b on a.order_id = b.order_id
                left join oc_product p on b.product_id = p.product_id
                left join oc_x_sku s on p.sku_id = s.sku_id
                left join oc_product_to_category pc on p.product_id = pc.product_id
                left join oc_category_description pcd on pc.category_id = pcd.category_id and pcd.language_id = 2
                left join oc_category c on pc.category_id = c.category_id
                left join oc_category_description pcdp on c.parent_id = pcdp.category_id and pcdp.language_id = 2
                left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id and wd.language_id = 2
                left join oc_product_description pd on p.product_id = pd.product_id and pd.language_id = 2
                left join oc_customer cust on a.customer_id = cust.customer_id
                where a.deliver_date = '" . $deliver_date . "' ";

            if ($filter_customer_group) {
                $sql .= " and cust.customer_group_id = '" . $filter_customer_group . "'";
            }
            if ($filter_station) {
                $sql .= " and a.station_id = '" . $filter_station . "'";
            }

            $sql .= "
                and b.status = 1
                and a.shipping_code in ('D2D','PSPOT')
                and a.order_status_id not in (" . CANCELLED_ORDER_STATUS . ")
                -- and (a.payment_code = 'COD' OR a.payment_code = 'FREE' OR (a.payment_code = 'WXPAY' and a.order_payment_status_id = 2) )
                group by b.product_id
                order by sku_id, parent_category, category, qty_total desc";

            $query = $this->db->query($sql);
            $data["podata"] = $query->rows;

            $header = array(
                'product_id' => '商品编号',
                'name' => '商品名称',
                'category' => '二级分类',
                'parent_category' => '一级分类',
                'price' => '平均售价',
                'price_range' => '售价范围',
                'product_unit' => '规格单位',
                'qty_total' => '下单合计',
                'purchase' => '采购数量'
            );
            $data["header"] = $header;

            //WXPAY Unpiad orders items
            $sql = "SELECT
                p.sku,
                b.product_id,
                b.name,
                p.image,
                pcd.name category,
                pcdp.name parent_category,
                round(avg(b.price),2) price,
                concat( round(min(b.price),2),'~',round(max(b.price),2) ) price_range,
                concat(round(p.weight,0), wd.title) product_unit,
                sum(b.quantity) qty_total,
                concat(round(p.weight,0), wd.title, ' x', sum(b.quantity)) purchase,
                group_concat(a.order_id) unpaid_orders
                from oc_order a
                left join oc_order_product b on a.order_id = b.order_id
                left join oc_product p on b.product_id = p.product_id
                left join oc_product_to_category pc on p.product_id = pc.product_id
                left join oc_category_description pcd on pc.category_id = pcd.category_id and pcd.language_id = 2
                left join oc_category c on pc.category_id = c.category_id
                left join oc_category_description pcdp on c.parent_id = pcdp.category_id and pcdp.language_id = 2
                left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id and wd.language_id = 2
                left join oc_product_description pd on p.product_id = pd.product_id and pd.language_id = 2
                left join oc_customer cust on a.customer_id = cust.customer_id
                where a.deliver_date = '" . $deliver_date . "' ";

            if ($filter_customer_group) {
                $sql .= " and cust.customer_group_id = '" . $filter_customer_group . "'";
            }
            if ($filter_station) {
                $sql .= " and a.station_id = '" . $filter_station . "'";
            }

            $sql .= "
                and b.status = 1
                and a.shipping_code in ('D2D','PSPOT')
                and a.order_status_id not in (" . CANCELLED_ORDER_STATUS . ")
                and a.payment_code = 'WXPAY' and a.order_payment_status_id = 1
                group by b.product_id
                order by parent_category, category, qty_total desc";
            $query = $this->db->query($sql);
            $data["podata_unpaid"] = $query->rows;

            //Load customer group
            $sql = "SELECT customer_group_id, name group_name from oc_customer_group";
            $query = $this->db->query($sql);
            $customerGroupsRaws = $query->rows;

            $data['customer_groups'] = array();
            foreach ($customerGroupsRaws as $val) {
                $data['customer_groups'][$val['customer_group_id']] = $val['group_name'];
            }


            $this->response->setOutput($this->load->view('sale/po_purchaseorder.tpl', $data));
        }




        if ($type == 'PR') {

            if (isset($this->request->get['filter_order_status'])) {
                $filter_order_status = $this->request->get['filter_order_status'];
            } else {
                $filter_order_status = null;
            }
            if (isset($this->request->get['filter_payment_status'])) {
                $filter_payment_status = $this->request->get['filter_payment_status'];
            } else {
                $filter_payment_status = null;
            }
            if (isset($this->request->get['filter_date_add'])) {
                $filter_date_add = $this->request->get['filter_date_add'];
            } else {
                $filter_date_add = null;
            }
            if (isset($this->request->get['filter_product_inv_class'])) {
                $filter_product_inv_class = $this->request->get['filter_product_inv_class'];
            } else {
                $filter_product_inv_class = null;
            }
            if (isset($this->request->get['filter_price_tag'])) {
                $filter_price_tag = $this->request->get['filter_price_tag'];
            } else {
                $filter_price_tag = null;
            }
            if (isset($this->request->get['filter_produce_group'])) {
                $filter_produce_group = $this->request->get['filter_produce_group'];
            } else {
                $filter_produce_group = null;
            }

            $filter_station_id = isset($this->request->get['filter_station_id']) ? $this->request->get['filter_station_id'] : null;



            $data['filter_order_status'] = $filter_order_status;
            $data['filter_payment_status'] = $filter_payment_status;
            $data['filter_date_add'] = $filter_date_add;
            $data['filter_product_inv_class'] = $filter_product_inv_class;
            $data['filter_price_tag'] = $filter_price_tag;
            $data['filter_produce_group'] = $filter_produce_group;
            $data['filter_station_id'] = $filter_station_id;





            $sql = "select
                p.sku,
                b.product_id,
                b.name,
                p.image,
                pcd.name category,
                pcdp.name parent_category,
                round(avg(b.price),2) price,
                concat( round(min(b.price),2),'~',round(max(b.price),2) ) price_range,
                concat(round(p.weight,0), wd.title) product_unit,
                sum(b.quantity) qty_total,
                sum( if((a.payment_code = 'WXPAY' and a.order_payment_status_id = 1),b.quantity,0) ) unpaid_qty_total,
                concat(round(p.weight,0), wd.title, ' x', sum(b.quantity)) purchase
                from oc_order a
                left join oc_order_product b on a.order_id = b.order_id";
            if ($filter_order_status && $filter_date_add) {
                $sql .= " LEFT JOIN (select * from oc_order_history where order_status_id = " . $filter_order_status . " group by order_id) oh ON a.order_id = oh.order_id";
            }
            $sql .= " left join oc_product p on b.product_id = p.product_id
                left join oc_product_to_category pc on p.product_id = pc.product_id
                left join oc_category_description pcd on pc.category_id = pcd.category_id and pcd.language_id = 2
                left join oc_category c on pc.category_id = c.category_id
                left join oc_category_description pcdp on c.parent_id = pcdp.category_id and pcdp.language_id = 2
                left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id and wd.language_id = 2
                left join oc_product_description pd on p.product_id = pd.product_id and pd.language_id = 2
                where a.deliver_date = '{$deliver_date}'";


            if ($filter_order_status) {
                $sql .= " and a.order_status_id = " . $filter_order_status;
            }
            if ($filter_payment_status) {
                if ($filter_payment_status == 2) {
                    $sql .= " and a.order_payment_status_id in (2,3)";
                } else {
                    $sql .= " and a.order_payment_status_id = " . $filter_payment_status;
                }
            }
            if ($filter_date_add) {

                if ($filter_order_status) {
                    if ($filter_date_add == 1) {
                        $sql .= " and oh.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 16:00:00'";
                    }
                    if ($filter_date_add == 2) {
                        $sql .= " and oh.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 16:00:00'";
                        $sql .= " and oh.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 18:00:00'";
                    }
                    if ($filter_date_add == 3) {
                        $sql .= " and oh.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 18:00:00'";
                        $sql .= " and oh.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 19:00:00'";
                    }
                    if ($filter_date_add == 4) {
                        $sql .= " and oh.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 19:00:00'";
                        $sql .= " and oh.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 19:30:00'";
                    }
                    if ($filter_date_add == 5) {
                        $sql .= " and oh.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 19:30:00'";
                        $sql .= " and oh.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 20:00:00'";
                    }
                    if ($filter_date_add == 6) {
                        $sql .= " and oh.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 20:00:00'";
                    }
                } else {
                    if ($filter_date_add == 1) {
                        $sql .= " and a.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 16:00:00'";
                    }
                    if ($filter_date_add == 2) {
                        $sql .= " and a.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 16:00:00'";
                        $sql .= " and a.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 18:00:00'";
                    }
                    if ($filter_date_add == 3) {
                        $sql .= " and a.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 18:00:00'";
                        $sql .= " and a.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 19:00:00'";
                    }
                    if ($filter_date_add == 4) {
                        $sql .= " and a.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 19:00:00'";
                        $sql .= " and a.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 19:30:00'";
                }
                    if ($filter_date_add == 5) {
                        $sql .= " and a.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 19:30:00'";
                        $sql .= " and a.date_added < '" . date("Y-m-d", time() + 8 * 3600) . " 20:00:00'";
            }
                    if ($filter_date_add == 6) {
                        $sql .= " and a.date_added >= '" . date("Y-m-d", time() + 8 * 3600) . " 20:00:00'";
            }
            }
            }
            if ($filter_product_inv_class) {
                if($filter_product_inv_class == 5){
                    $sql .= " and p.inv_class in (2,3,4) ";
                }
                else{
                $sql .= " and p.inv_class = " . $filter_product_inv_class;
            }
            }
            if ($filter_price_tag !== null) {
                $sql .= " and a.is_nopricetag = " . $filter_price_tag;
            }
            if ($filter_produce_group) {
                $sql .= " and p.produce_group_id = " . $filter_produce_group;
            }

            if($filter_station_id){
                $sql .= " and a.station_id = '".$filter_station_id. "'";
            }



            $sql .="    and b.status = 1
                and a.shipping_code in ('D2D','PSPOT')
                and a.order_status_id not in (" . CANCELLED_ORDER_STATUS . ")
                group by b.product_id
                order by parent_category, category, qty_total desc";

            // -- and (a.payment_code = 'COD' OR a.payment_code = 'FREE' OR (a.payment_code = 'WXPAY' and a.order_payment_status_id = 2) )

            $query = $this->db->query($sql);
            $data["podata"] = $query->rows;

            $header = array(
                'product_id' => '商品编号',
                'name' => '商品名称',
                'category' => '二级分类',
                'parent_category' => '一级分类',
                'price' => '平均售价',
                'price_range' => '售价范围',
                'product_unit' => '规格单位',
                'qty_total' => '下单合计',
                'purchase' => '采购数量'
            );
            $data["header"] = $header;

            //WXPAY Unpiad orders items
            $sql = "SELECT
                p.sku,
                b.product_id,
                b.name,
                p.image,
                pcd.name category,
                pcdp.name parent_category,
                round(avg(b.price),2) price,
                concat( round(min(b.price),2),'~',round(max(b.price),2) ) price_range,
                concat(round(p.weight,0), wd.title) product_unit,
                sum(b.quantity) qty_total,
                concat(round(p.weight,0), wd.title, ' x', sum(b.quantity)) purchase,
                group_concat(a.order_id) unpaid_orders
                from oc_order a
                left join oc_order_product b on a.order_id = b.order_id
                left join oc_product p on b.product_id = p.product_id
                left join oc_product_to_category pc on p.product_id = pc.product_id
                left join oc_category_description pcd on pc.category_id = pcd.category_id and pcd.language_id = 2
                left join oc_category c on pc.category_id = c.category_id
                left join oc_category_description pcdp on c.parent_id = pcdp.category_id and pcdp.language_id = 2
                left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id and wd.language_id = 2
                left join oc_product_description pd on p.product_id = pd.product_id and pd.language_id = 2
                where a.deliver_date = '{$deliver_date}'
                and b.status = 1
                and a.shipping_code in ('D2D','PSPOT')
                and a.order_status_id not in (" . CANCELLED_ORDER_STATUS . ")
                and a.payment_code = 'WXPAY' and a.order_payment_status_id = 1
                group by b.product_id
                order by parent_category, category, qty_total desc";
                
                
            $query = $this->db->query($sql);
            $data["podata_unpaid"] = $query->rows;
            $data['podata_unpaid'] = array();


            $this->load->model('localisation/order_status');
            $this->load->model('sale/order');
            $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

            $produceGroupRaw = $this->model_sale_order->getProduceGroup();
            $data['this_product_group'] = '';
            foreach ($produceGroupRaw as $produceGroup) {
                $data['product_group'][$produceGroup['produce_group_id']] = $produceGroup;
                if ($produceGroup['produce_group_id'] == $filter_produce_group) {
                    $data['this_product_group'] = $produceGroup['title'];
                }
            }
            //$data['product_group'] = $this->model_sale_order->getProduceGroup();

            $this->load->model('station/station');
            $data['stations'] = $this->model_station_station->getStationList();

            $data['order_date_add'] = array(
                1 => "16:00前", 
                2 => "16:00 ~ 18:00",
                3 => "18:00 ~ 19:00",
                4 => "19:00 ~ 19:30",
                5 => "19:30 ~ 20:00",
                6 => "20:00后");
            $data['product_inv_classes'] = $this->model_sale_order->getProductInvclasses();
            $data['order_price_tages'] = array(1 => "有", 0 => "无");
            //echo "<pre>";print_r($data);exit;

            $this->response->setOutput($this->load->view('sale/po_productionorder.tpl', $data));
        }











        if ($type == 'PSR') {

            $sql = "SELECT
                O.order_id, O.payment_code,
                concat(LEFT(O.firstname,1),'*') member_name,
                O.shipping_name shipping_name,
                O.telephone,
                O.shipping_phone,
                O.shipping_method, O.payment_method,
                sum(OT.value) dueTotal, sum(if(OT.code='total',OT.value,0)) due,
                O.date_added order_date, O.deliver_date,
                OS.name order_status,
                OPS.name order_payment_status,
                O.pickupspot_id,
                XP.name pickspot_name
                FROM oc_order O
                LEFT JOIN oc_order_total OT ON O.order_id = OT.order_id AND OT.accounting = 1
                LEFT JOIN oc_order_status OS ON O.order_status_id = OS.order_status_id
                LEFT JOIN oc_order_payment_status OPS ON O.order_payment_status_id = OPS.order_payment_status_id
                LEFT JOIN oc_x_pickupspot XP ON O.pickupspot_id = XP.pickupspot_id
                WHERE OT.accounting = 1 AND O.order_status_id NOT IN (" . CANCELLED_ORDER_STATUS . ")
                AND O.order_payment_status_id = 2
                AND O.shipping_code = 'PSPOT'
                AND O.deliver_date = '{$deliver_date}'
                GROUP BY O.order_id";

            $query = $this->db->query($sql);
            $result = $query->rows;
            //$data["psrdata"] = $psrdata;

            $psrdata = array();
            $pivot = $result[0]['pickupspot_id'];
            $psrdata[$pivot]['name'] = $result[0]['pickspot_name'];
            $psrdata[$pivot]['orders'][] = $result[0];

            for ($i = 1; $i < sizeof($result); $i++) {
                if ($pivot !== $result[$i]['pickupspot_id']) {
                    $pivot = $result[$i]['pickupspot_id'];
                    $psrdata[$pivot]['name'] = $result[$i]['pickspot_name'];
                }

                $psrdata[$pivot]['orders'][] = $result[$i];
            }

            $data["psrdata"] = $psrdata;

            //Unpaid orders
            $sql = "SELECT
                O.order_id, O.payment_code,
                concat(LEFT(O.firstname,1),'*') member_name,
                O.shipping_name shipping_name,
                O.telephone,
                O.shipping_phone,
                O.shipping_method, O.payment_method,
                sum(OT.value) dueTotal, sum(if(OT.code='total',OT.value,0)) due,
                O.date_added order_date, O.deliver_date,
                OS.name order_status,
                OPS.name order_payment_status,
                O.pickupspot_id,
                XP.name pickspot_name
                FROM oc_order O
                LEFT JOIN oc_order_total OT ON O.order_id = OT.order_id AND OT.accounting = 1
                LEFT JOIN oc_order_status OS ON O.order_status_id = OS.order_status_id
                LEFT JOIN oc_order_payment_status OPS ON O.order_payment_status_id = OPS.order_payment_status_id
                LEFT JOIN oc_x_pickupspot XP ON O.pickupspot_id = XP.pickupspot_id
                WHERE OT.accounting = 1 AND O.order_status_id NOT IN (" . CANCELLED_ORDER_STATUS . ")
                AND O.order_payment_status_id = 1
                AND O.shipping_code = 'PSPOT'
                AND O.deliver_date = '{$deliver_date}'
                GROUP BY O.order_id";

            $query = $this->db->query($sql);
            $result = $query->rows;
            $data["psrdata_unpaid"] = $result;

            $this->response->setOutput($this->load->view('sale/psr_pspotreceipt.tpl', $data));
        }

        if ($type == 'DO') {

            $sql = "SELECT
                O.order_id, O.payment_code,
                concat(LEFT(O.firstname,1),'*') member_name,
                O.firstname customer_name,
                O.shipping_name shipping_name,
                O.telephone,
                O.shipping_phone,
                O.shipping_method, O.payment_method,
                sum(OT.value) dueTotal, sum(if(OT.code='total',OT.value,0)) due,
                O.date_added order_date, O.deliver_date,
                OS.name order_status,
                O.payment_method,
                O.payment_code,
                O.order_payment_status_id,
                OPS.name order_payment_status,
                O.total,
                O.sub_total,
                O.pickupspot_id,
                O.shipping_address_1 shipping_address,
                O.comment,
                O.order_status_id,
                OEX.firstorder,
                XP.name pickspot_name,
                oi.frame_count,
                oi.incubator_count,
                oi.foam_count,
                oi.frame_mi_count,
                oi.incubator_mi_count,
                oi.frame_ice_count,
                oi.box_count,
                oi.foam_ice_count,
                oi.frame_meat_count,
                oi.inv_comment,
                xb.bd_name,
                xb.phone,
                ad.deliver_slot_id,
                ad.area_id,
                O.station_id,
                ad.has_locker
                FROM oc_order O
                LEFT JOIN oc_order_total OT ON O.order_id = OT.order_id AND OT.accounting = 1
                LEFT JOIN oc_order_extend OEX ON O.order_id = OEX.order_id
                LEFT JOIN oc_order_status OS ON O.order_status_id = OS.order_status_id
                LEFT JOIN oc_order_payment_status OPS ON O.order_payment_status_id = OPS.order_payment_status_id
                LEFT JOIN oc_x_pickupspot XP ON O.pickupspot_id = XP.pickupspot_id
                LEFT JOIN oc_order_inv as oi on oi.order_id = O.order_id and oi.inv_status = 1 
                left join oc_x_bd as xb on xb.bd_id = O.bd_id
                left join oc_address ad on O.customer_id = ad.customer_id
                WHERE  O.deliver_date = '{$deliver_date}'";
                /*
                if ($filter_station) {
                    $sql .= " and O.station_id = '" . $filter_station . "'";
                }
                */
                $sql .= "GROUP BY O.order_id
                ORDER BY O.station_id asc,O.order_id ASC";

            $query = $this->db->query($sql);
            $data['do_data'] = $query->rows;


            //Get Deliver Slot Info
            $sql = "select deliver_slot_id,title from oc_deliver_slot";
            $query = $this->db->query($sql);
            $deliver_slots = $query->rows;
            $data['deliver_slots'] = array("0" => "-");
            foreach ($deliver_slots as $deliver_slot) {
                $data['deliver_slots'][$deliver_slot['deliver_slot_id']] = $deliver_slot['title'];
            }

            $return = array();
            $return['do_data'] = array();
            $order_id_arr = array();
            $order_id_str = '';
            $order_products_arr = array();
            foreach ($data['do_data'] as $key => $value) {
                $value['category_67'] = 0;
                $value['category_65_66'] = 0;
                $value['category_other'] = 0;
                $return['do_data'][$value['order_id']] = $value;
                $order_id_arr[] = $value['order_id'];
            }
            $order_id_str = implode(",", $order_id_arr);

            $sql = "SELECT
                op.order_id,
                op.product_id,
                ptc.category_id,
                sum(op.quantity) as category_product_quantity,
              cd.name
            FROM
                oc_order_product AS op
            LEFT JOIN oc_product_to_category AS ptc ON op.product_id = ptc.product_id
            left join oc_category_description as cd on cd.category_id = ptc.category_id and cd.language_id = 2
            WHERE
                op.order_id IN (" . $order_id_str . ")
            GROUP BY
                op.order_id,
                ptc.category_id";


            $query = $this->db->query($sql);
            $order_products_arr = $query->rows;

            foreach ($order_products_arr as $pkey => $pvalue) {
                if ($pvalue['category_id'] == 67) {
                    $return['do_data'][$pvalue['order_id']]['category_67'] += $pvalue['category_product_quantity'];
                } else if ($pvalue['category_id'] == 65 || $pvalue['category_id'] == 66) {
                    $return['do_data'][$pvalue['order_id']]['category_65_66'] += $pvalue['category_product_quantity'];
                } else {
                    $return['do_data'][$pvalue['order_id']]['category_other'] += $pvalue['category_product_quantity'];
                }
            }




            
            
            
            
            
            //应收金额需减去退货金额
            if(!empty($return['do_data'])){
                $unpaid_order_ids = array();
                $unpaid_order_id_str = '';
                foreach($return['do_data'] as $key=>$value){
                    $return['do_data'][$key]['need_pay'] = $value['total'];
                    if($value['payment_code'] == 'COD'){
                        $unpaid_order_ids[$value['order_id']] = $key;

                    }
                }

                if(!empty($unpaid_order_ids)){
                    $unpaid_order_id_str = implode(",", array_keys($unpaid_order_ids));

                    $sql = "SELECT r.order_id, sum(rp.return_product_credits) AS return_credits FROM oc_return AS r LEFT JOIN oc_return_product AS rp ON r.return_id = rp.return_id WHERE r.order_id IN (" . $unpaid_order_id_str . ") and r.return_status_id = 2 and r.return_credits = 0 GROUP BY r.order_id";
                    $query = $this->db->query($sql);
                    $unpaid_return_orders = $query->rows;
                    if(!empty($unpaid_return_orders)){
                        foreach($unpaid_return_orders as $k => $v){
                            $return['do_data'][$unpaid_order_ids[$v['order_id']]]['need_pay'] = $return['do_data'][$unpaid_order_ids[$v['order_id']]]['need_pay'] - $v['return_credits'];
                            $return['do_data'][$unpaid_order_ids[$v['order_id']]]['need_pay'] = $return['do_data'][$unpaid_order_ids[$v['order_id']]]['need_pay'] < 0 ? 0 : $return['do_data'][$unpaid_order_ids[$v['order_id']]]['need_pay'];
                        }
                    }
                }

            }
            
            
            
            
            
            
            



            $data['filter_station'] = $filter_station;
            $data['do_data'] = $return['do_data'];



            $this->response->setOutput($this->load->view('sale/do_deliverorder.tpl', $data));
        }
    }

    private function setUrl() {
        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_order_payment_status'])) {
            $url .= '&filter_order_payment_status=' . $this->request->get['filter_order_payment_status'];
        }

        if (isset($this->request->get['filter_order_deliver_status'])) {
            $url .= '&filter_order_deliver_status=' . $this->request->get['filter_order_deliver_status'];
        }

        if (isset($this->request->get['filter_customer_group'])) {
            $url .= '&filter_customer_group=' . $this->request->get['filter_customer_group'];
        }
        if (isset($this->request->get['filter_station'])) {
            $url .= '&filter_station=' . $this->request->get['filter_station'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['filter_deliver_date'])) {
            $url .= '&filter_deliver_date=' . $this->request->get['filter_deliver_date'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        return $url;
    }

    private function addOrderHistory($order_id, $comment = false) {
        $user = $this->user->getId();
        //var_dump(get_class_methods(get_class($user))); //Get obj->class->methods

        if (!$comment) {
            $comment = '';
        }

        $sql = "INSERT INTO  " . DB_PREFIX . "order_history (`order_id`, `notify`, `comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`)
SELECT '{$order_id}', '0', '{$comment}', NOW(), order_status_id, order_payment_status_id, order_deliver_status_id, '{$user}' FROM  oc_order WHERE order_id = {$order_id}";

        $this->db->query($sql);
    }

    public function orderStatus() {
        //TODO 阻止重复操作，取消订单退余额

        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        $this->error['warning'] = "未知的订单编号或状态编号";

        if (isset($_REQUEST['order_id']) || isset($_REQUEST['status_id'])) {
            $order_id = (int) $_REQUEST['order_id'];
            $status_id = (int) $_REQUEST['status_id'];
//            $return_confirm_id = (int) $_REQUEST['return_confirm_id'];
            //处理订单状态, 仅ALLOW_CANCEL（未处理，已确认）订单可以在后台更改订单状态
//            $sql = "SELECT order_status_id, `name` status_name FROM oc_order_status WHERE language_id = 2";
//            $query = $this->db->query($sql);
//            $orderStatusRaw = $query->rows;
//
//            $orderStatus = array();
//            foreach ($orderStatusRaw as $val) {
//                $orderStatus[$val['order_status_id']] = $val['status_name'];
//            }

            $orderStatus = $this->model_sale_order->getOrderStatus();

//            $sql = "SELECT o.order_id, o.order_status_id FROM oc_order o WHERE o.order_id = '" . $order_id . "'";
//            $query = $this->db->query($sql);
//            $currentStatus = $query->row;

            $currentStatus = $this->model_sale_order->getCurrentOrderStatus($order_id);

//            $bool = false;

            $order_deliver_status_id = $this->model_sale_order->getOrderToCanceDeliverStatus($order_id);

            $order_allot_flag = $this->model_sale_order->getOrderIsAlloted($order_id);

            //待配送的订单,并且订单没有被分车才可以做出库前取消操作
            if($order_deliver_status_id == 1 && !$order_allot_flag){
                if (in_array($currentStatus['order_status_id'], unserialize(ALLOW_CANCEL))) {
                    $bool = true;
//                $sql = "update oc_order set order_status_id = '{$status_id}' where order_id = '{$order_id}'";
//                $bool = true;
//                $bool = $bool && $this->db->query($sql);
//
//
//                // 取消订单，执行库存操作
//                if ($status_id == CANCELLED_ORDER_STATUS && $currentStatus['order_status_id'] !== CANCELLED_ORDER_STATUS) {
//                    //TODO退余额退款操作
//
//                    //取消使用优惠券记录
//                    $sql = "update oc_coupon_history set status = '0' where order_id = '".$order_id."'";
//                    $bool = true;
//                    $bool = $bool && $this->db->query($sql);
//
//
//                    //查找是否已有库存扣减记录，如有添加库存增加记录
//                    $sql = "INSERT INTO `oc_x_inventory_move` (`station_id`, `date`, `timestamp`, `from_station_id`, `to_station_id`, `order_id`, `inventory_type_id`, `date_added`, `status`)
//                    select A.station_id, current_date() date, unix_timestamp(now()) timestamp, 0 from_station_id, 0 to_station_id, A.order_id, " . INVENTORY_TYPE_ORDER_CANCEL . " inventory_type_id, now() date_added, 1 status
//                    from oc_order A
//                    inner join oc_x_inventory_move B on A.order_id = B.order_id and inventory_type_id = '" . INVENTORY_TYPE_ORDERED . "'
//                    where A.order_id = '" . $order_id . "'";
//                    $bool = $bool && $this->db->query($sql);
//                    //$inventory_move_id = $dbm->getLastId();
//                    //按照添加的，后天订单设置状态为0, 不参与实时库存计算
//                    $sql = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `order_id`, `customer_id`, `product_id`, `quantity`, `status`)
//                    select A.inventory_move_id, A.station_id, B.order_id, B.customer_id, C.product_id, C.quantity quantity, if(B.deliver_date = date_add(date(B.date_added), interval 1 day), 1, 0) status
//                    from oc_x_inventory_move A
//                    left join oc_order B on A.order_id = B.order_id
//                    left join oc_order_product C on B.order_id = C.order_id
//                    where A.order_id = '" . $order_id . "' and A.inventory_type_id = '" . INVENTORY_TYPE_ORDER_CANCEL . "'
//                    ";
////                    $sql = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `order_id`, `customer_id`, `product_id`, `quantity`, `status`)
////                    select A.inventory_move_id, A.station_id, B.order_id, B.customer_id, C.product_id, C.quantity quantity, 1 status
////                    from oc_x_inventory_move A
////                    left join oc_order B on A.order_id = B.order_id
////                    left join oc_order_product C on B.order_id = C.order_id
////                    where A.order_id = '" . $order_id . "' and A.inventory_type_id = '" . INVENTORY_TYPE_ORDER_CANCEL . "'
////                    ";
//                    $bool = $bool && $this->db->query($sql);
//                }
                    //如果$status_id = 2 或者 3的话则执行下面方法,否则需要进行判断仓库或者物流是否进行相关操作之后再取消订单
                    if($status_id == 2 || $status_id == 3){
                        $bool = $this->model_sale_order->orderStatusAllowes($status_id,$order_id,$currentStatus);
                    }else{
                        //如果订单配送状态为待分配，才可以操作出库前取消订单的动作
                        //如果$status_id为5，则是处理分拣中的订单，如果$status_id为6则处理的是已拣完的订单
                        if($status_id == 5){
                            //模型层处理退余额，申请折现客服联系财务即可
                            $this->model_sale_order->cancelOrderDistr($status_id,$order_id,$currentStatus);

                        }elseif($status_id == 6){
                            //取消已拣完订单，不删除分拣数据
                            //退款处理
                            $this->model_sale_order->cancelOrderDistr($status_id,$order_id,$currentStatus);
                        }
                    }
                }
            }else{
                $bool = false;
            }


            if ($bool) {
                //SUCCESS
                //$this->addOrderHistory($order_id,'后台取消');
                $this->addOrderHistory($order_id);

                //Add MSG Tasks
                //Get status setting, insert into msg
//                $sql = "
//                    INSERT INTO `oc_msg` (`merchant_id`, `customer_id`, `phone`, `order_id`, `msg_param_1`, `msg_param_2`, `isp_template_id`, `msg_type`, `msg_status_id`,`msg_status_name`,`sent`, `status`, `date_added`)
//                    SELECT 0, o.customer_id, o.shipping_phone, " . $order_id . ", '" . $order_id . "', st.contact_phone, mt.isp_template_id, mt.msg_type, o.order_status_id, os.name, 0, 1, NOW()
//                    FROM oc_order o
//                    LEFT JOIN oc_order_status os ON o.order_status_id = os.order_status_id AND os.language_id = 2
//                    LEFT JOIN oc_x_station st ON o.station_id = st.station_id
//                    LEFT JOIN oc_msg_template mt ON os.msg_template_id = mt.msg_template_id
//                    LEFT JOIN oc_customer c ON o.customer_id = c.customer_id
//                    WHERE
//                    o.order_id = '" . $order_id . "' AND o.order_status_id = '" . $status_id . "'
//                    AND os.msg = 1 AND c.accept_order_message = 1
//                    ";
//                $this->db->query($sql);

                //业务迁移到model层


                $this->session->data['success'] = "订单[{$_REQUEST['order_id']}]状态更新成功，当前状态为[{$orderStatus[$status_id]}]";

                $url = $this->setUrl();
                $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            } else {
                //ERROR
                $this->error['warning'] = "订单[{$_REQUEST['order_id']}]状态更新[{$orderStatus[$status_id]}]失败, 当前状态为[{$orderStatus[$currentStatus['order_status_id']]}]";
            }
        }

        $this->getList();
    }

    public function deliver() {

        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        $user_id = $this->user->getId();

        $this->error['warning'] = "未知的订单编号或状态编号";
        if (isset($_REQUEST['order_id']) || isset($_REQUEST['status_id'])) {
            $order_id = (int) $_REQUEST['order_id'];
            $status_id = (int) $_REQUEST['status_id'];

            $sql = "select customer_id,customer_group_id,is_nopricetag,order_status_id,firstname,lastname,deliver_date,email,telephone,date_added,order_deliver_status_id,station_id,order_payment_status_id from oc_order where order_id = " . $order_id;
            $query = $this->db->query($sql);
            $order_customer = $query->row;

            $result = false;

            //var_dump($order_customer);

            if($order_customer['order_status_id'] == 6){
                $sql = "update " . DB_PREFIX . "order set order_deliver_status_id = '{$status_id}' where order_id = '{$order_id}'";
                //订单改为重新配送，修改订单配送日期
                if($status_id == 11){
                    $sql = "update " . DB_PREFIX . "order set order_deliver_status_id = '".$status_id."', deliver_date = date_add(current_date(), interval 1 day) where order_id = '".$order_id."'";
                }
                $result = $this->db->query($sql);

                //原状态为整单退回，更新退回订单状态时修改
                if($order_customer['order_deliver_status_id'] == 10){
                    $sql = "update oc_order_deliver_issue set status = 2, date_updated = now(), updated_by = '".$user_id."' where status = 1 and order_id = '".$order_id."'";
                    $result = $this->db->query($sql);
                }

                //对于鲜奶T+4用户，配送完成且使用了鲜奶优惠券的用户
                if($status_id == 3){ //配送完成状态为3
                    //20170217 判定更改为订购了鲜奶的用户，配送日期为T+4
                    //$sql = "update oc_customer set milk_ordered = 1 where customer_id in (select customer_id from oc_coupon_history where order_id = '".$order_id."' and status = 1 and coupon_id = 6)";
                    $sql = "update oc_customer set milk_ordered = 1 where customer_id in (select if(datediff(deliver_date, date(date_added))=4, customer_id, 0) customer_id from oc_order where order_status_id not in (3) and order_id = '".$order_id."')";
                    $this->db->query($sql);

                    $sql = "INSERT INTO oc_customer_history (customer_id, comment, date_added, added_by)
                        select customer_id, 'milk_ordered=1', NOW(), '".$user_id."' from oc_order where order_id = '".$order_id."' and order_status_id not in (3) and datediff(deliver_date, date(date_added))=4";
                    $this->db->query($sql);
                }

                //SUCCESS
                $this->addOrderHistory($order_id);
                $this->session->data['success'] = "订单[{$_REQUEST['order_id']}]配送状态已更新";
            }
            else{
                $this->session->data['success'] = "";
                $this->error['warning'] = "订单[{$_REQUEST['order_id']}]未提交分拣出库，不能修改配送状态";
            }

            //快消品缺货分拣完成时自动匹配，这里仅处理生鲜平台
            if ($result && $order_customer['order_status_id'] == 6 && $order_customer['station_id'] == 1 ) {

                //计算按重出库商品的金额变动
                $inv_weight_product_arr = array();
                if ($status_id == 2 && $order_customer['order_status_id'] == 6 && $order_customer['order_deliver_status_id'] == 1) {

                    //获取送货前一天价格版本的商品价格，即标签价格
                    $sql = "select * from oc_x_product_price_history where date = '" . date("Y-m-d" ,strtotime($order_customer['deliver_date']) - 3600 * 24) . "'";
                    $query = $this->db->query($sql);
                    $price_history_arr = $query->rows;

                    $price_history_arr_bak = array();
                    foreach($price_history_arr as $phk => $phv){
                        $price_history_arr_bak[$phv['product_id']] = $phv;
                    }
                    $price_history_arr = $price_history_arr_bak;



                    $sql = "SELECT
                            p.product_id,
                            p.weight as p_weight,
                            op.price,
                            p.price as now_price,
                            p.retail_price as now_retail_price,
                            op.quantity as o_quantity,
                            op.total as o_total,
                            op.retail_price as op_retail_price,
                            sum(smi.quantity) as i_quantity,
                            sum(smi.weight) as i_weight,
                            smi.product_batch,
                            op.weight_inv_flag,
                            op.name,
                            op.model


                            FROM
                                    oc_x_stock_move_item AS smi
                            LEFT JOIN oc_x_stock_move AS xsm ON smi.inventory_move_id = xsm.inventory_move_id
                            LEFT JOIN oc_order_product AS op ON xsm.order_id = op.order_id and smi.product_id = op.product_id
                            LEFT JOIN oc_product AS p ON p.product_id = op.product_id 
                            WHERE
                                     xsm.order_id = " . $order_id . "
                            GROUP BY
                                    p.product_id";

                    $query = $this->db->query($sql);
                    $inv_weight_product_arr = $query->rows;
                    
                    
                    
                    
                    $inv_product_arr = array();
                    if(!empty($inv_weight_product_arr)){
                        foreach($inv_weight_product_arr as $key=>$value){
                            $inv_product_arr[$value['product_id']] = $value;
                        }
                    }
                    $inv_weight_product_arr = $inv_product_arr;
                    
                    
                    
                    $this->load->model('sale/customer');
                    $quantity_dif_flag = false;
                    $return_credits = 0;
                    $return_data = array();
                    $return_product_data = array();


                    $return_credits_weight_dif = 0;
                    $return_credits_price_dif = 0;

                    $return_credits_weight_dif_product = array();
                    $return_credits_price_dif_product = array();


                    foreach ($inv_weight_product_arr as $k => $v) {


                            
                        if ($v['weight_inv_flag'] == 1) {
                            
                            
                            
                            //根据标签售价比较下单时售价  计算价格变动差异
                            
                            $product_tag_retail_price = $price_history_arr[$v['product_id']]['retail_price'];
                            $price_dif = 0;
                            if ($product_tag_retail_price != $v['op_retail_price'] ) {
                                $price_dif = $v['price'] - $price_history_arr[$v['product_id']]['price'];
                            }

                            if ($price_dif != 0) {
                                $price_dif_count = $price_dif * abs($v['i_quantity']);

                                
                                $return_credits_price_dif_product[$v['product_id']] = array(
                                    "product_id" => $v['product_id'],
                                    "model" => $v['model'],
                                    "name" => $v['name'],
                                    "order_id" => $order_id,
                                    "change_type_id" => 3,
                                    "quantity" => abs($v['i_quantity']),
                                    "price" => $price_history_arr[$v['product_id']]['price'],
                                    "total" => $price_history_arr[$v['product_id']]['price']*abs($v['i_quantity']),
                                    "change_product_credits" => $price_dif_count
                                );
                                
                                
                                
                                
                                //只退不收
                                if($price_dif_count > 0){
                                    
                                    $return_credits_price_dif += $price_dif_count;
                                    /*
                                //补差价
                                $price_dif_comment = "订单 " . $order_id . " 商品 " . $v['product_id'] . " " . $v['name'] . " 价格调整为 " . $price_history_arr[$v['product_id']]['price'] . ",补差价";
                                $this->model_sale_customer->addTransaction($order_customer['customer_id'], $price_dif_comment, $price_dif_count, $order_id);
                                     * 
                                     */
                            }
                            }
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                        //根据重量计算金额
                        $weight_dif = 0;
                        $weight_dif_count = 0;
                        $weight_dif_credit = 0;
                        $weight_dif_comment = '';
                        $weight_dif = (int) $v['p_weight'] - $v['i_weight'] / abs($v['i_quantity']);

                        if ($weight_dif) {
                            $weight_dif_count = sprintf("%.2f", abs($v['i_quantity']) * $weight_dif);

                                $weight_dif_credit = sprintf("%.2f", $price_history_arr[$v['product_id']]['price'] / $v['p_weight'] * $weight_dif_count);

                            //退余额
                            if ($weight_dif > 0) {
                                    $weight_dif_comment = "订单 " . $order_id . " 商品 " . $v['product_id'] . " " . $v['name'] . " 出库重量少 " . abs($weight_dif_count);
                            } else {
                                    $weight_dif_comment = "订单 " . $order_id . " 商品 " . $v['product_id'] . " " . $v['name'] . " 出库重量多 " . abs($weight_dif_count);
                            }
                                
                                $return_credits_weight_dif_product[$v['product_id']] = array(
                                    "product_id" => $v['product_id'],
                                    "model" => $v['model'],
                                    "name" => $v['name'],
                                    "order_id" => $order_id,
                                    "change_type_id" => 2,
                                    "quantity" => abs($v['i_quantity']),
                                    "price" => $price_history_arr[$v['product_id']]['price'],
                                    "total" => $price_history_arr[$v['product_id']]['price']*abs($v['i_quantity']),
                                    "weight_total" => $v['i_weight'],
                                    "weight_change" => $v['i_weight'] - $v['p_weight']*  abs($v['i_quantity']),
                                    "change_product_credits" => $weight_dif_credit
                                );
                                
                                
                                
                            //记录订单实际出库商品重量、金额

                            $sql = "insert into oc_order_product_weight_inv(order_id,product_id,quantity,weight_total,total) values(" . $order_id . "," . $v['product_id'] . "," . abs($v['i_quantity']) . ",'" . $v['i_weight'] . "','" . ($v['o_total'] - $weight_dif_credit) . "')";
                            $this->db->query($sql);

                                $return_credits_weight_dif += $weight_dif_credit;
                                
                                /*
                            $this->model_sale_customer->addTransaction($order_customer['customer_id'], $weight_dif_comment, $weight_dif_credit, $order_id);
                                 * 
                                 */
                        }
                            else{
                                $sql = "insert into oc_order_product_weight_inv(order_id,product_id,quantity,weight_total,total) values(" . $order_id . "," . $v['product_id'] . "," . abs($v['i_quantity']) . ",'" . $v['i_weight'] . "','" . $v['o_total'] . "')";
                                $this->db->query($sql);
                            }
                        } else {
                            
                            //if($v['product_batch'] &&  strlen($v['product_batch']) == 18 && $v['product_id'] < 5000){
                                
                            //根据标签售价比较下单时售价  计算价格变动差异
                                /*
                                $product_tag_retail_price = substr($v['product_batch'], 4, 5);
                            $product_tag_retail_price = (int) $product_tag_retail_price / 100;
                                */
                            
                            
                                $product_tag_retail_price = $price_history_arr[$v['product_id']]['price'];
                                /*
                            $price_dif = 0;
                                if ($product_tag_retail_price != $v['op_retail_price'] ) {
                                    
                                    $price_dif = $v['price'] - $product_tag_retail_price;
                    }
                                */
                                $price_dif = $v['price'] - $product_tag_retail_price;
                            if ($price_dif != 0) {
                                $price_dif_count = $price_dif * abs($v['i_quantity']);
                                    
                                    
                                    $return_credits_price_dif_product[$v['product_id']] = array(
                                        "product_id" => $v['product_id'],
                                        "model" => $v['model'],
                                        "name" => $v['name'],
                                        "order_id" => $order_id,
                                        "change_type_id" => 3,
                                        "quantity" => abs($v['i_quantity']),
                                        "price" => $product_tag_retail_price,
                                        "total" => $product_tag_retail_price*abs($v['i_quantity']),
                                        "change_product_credits" => $price_dif_count
                                    );
                                    
                                    
                                    
                                    
                                    
                                    //只退不收
                                    if($price_dif_count > 0 ){
                                        
                                        $return_credits_price_dif += $price_dif_count;
                                        /*
                                //补差价
                                $price_dif_comment = "订单 " . $order_id . " 商品 " . $v['product_id'] . " " . $v['name'] . " 价格调整为 " . $v['now_price'] . ",补差价";
                                $this->model_sale_customer->addTransaction($order_customer['customer_id'], $price_dif_comment, $price_dif_count, $order_id);
                                         * 
                                         */
                }
                        }
                           //}
                    }
                        
                        
                        //添加缺货商品的退货记录
                        $quantity_dif = 0;
                        $quantity_dif_credit = 0;
                        
                        $quantity_dif = $v['o_quantity'] - abs($v['i_quantity']);
                        
                        if($quantity_dif > 0 ){
                            $quantity_dif_flag = true;
                            $quantity_dif_credit = sprintf("%.2f", $quantity_dif * $v['price']);
                            $return_credits += $quantity_dif_credit;
                            //退货
                            if($quantity_dif > 0){
                                $return_product_data[$v['product_id']] = array(
                                    "product_id" => $v['product_id'],
                                    "model" => $v['model'],
                                    "name" => $v['name'],
                                    "order_id" => $order_id,
                                    "change_type_id" => 1,
                                    "quantity" => $quantity_dif,
                                    "price" => $v['price'],
                                    "total" => $v['price']*$quantity_dif,
                                    "return_product_credits" => $quantity_dif_credit
                                );
                            }
                        }
                    }
                    
                    //判断未分拣的商品
                    $order_products = $this->model_sale_order->getOrderProducts($order_id);
                    
                    if(!empty($order_products)){
                        foreach($order_products as $o_key => $o_valule){
                            if(!array_key_exists($o_valule['product_id'], $inv_weight_product_arr)){
                                $quantity_dif_flag = true;
                                $return_product_data[$o_valule['product_id']] = array(
                                    "product_id" => $o_valule['product_id'],
                                    "model" => $o_valule['model'],
                                    "name" => $o_valule['name'],
                                    "order_id" => $order_id,
                                    "change_type_id" => 1,
                                    "quantity" => $o_valule['quantity'],
                                    "price" => $o_valule['price'],
                                    "total" => $o_valule['total'],
                                    "return_product_credits" => $o_valule['total']
                                );
                                $return_credits += $o_valule['total'];
                            }
                        }
                    }
                    
                    $this->load->model('sale/order_change');
                        $return_data['order_id'] = $order_id;
                        $return_data['date_ordered'] = date("Y-m-d",  strtotime($order_customer['date_added']));
                        $return_data['customer'] = $order_customer['firstname'];
                        $return_data['customer_id'] = $order_customer['customer_id'];
                        $return_data['firstname'] = $order_customer['firstname'];
                        $return_data['lastname'] = $order_customer['lastname'];
                        $return_data['email'] = $order_customer['email'];
                        $return_data['telephone'] = $order_customer['telephone'];
                        $return_data['return_reason_id'] = 1;
                        $return_data['opened'] = 0;
                        $return_data['comment'] = '';
                        
                        if( $order_customer['order_payment_status_id'] == 1){
                            $return_data['return_action_id'] = 1;
                            $return_data['return_credits'] = 0;
                        }
                        else{
                        $return_data['return_action_id'] = 3;
                            $return_data['return_credits'] = $return_credits;
                        }
                        
                        
                        $return_data['return_status_id'] = 1;
                        $return_data['return_inventory_flag'] = 0;
                        
                            $return_data['change_status_id'] = 2;
                        
                        $return_data['add_user'] =  $this->user->getId();
                        
                    //提交退货
                    if($quantity_dif_flag){
                        
                       
                        $this->load->model('sale/return');
                       
                       
                        $return_id = $this->model_sale_return->addReturn($return_data);
                        $this->model_sale_return->addReturnProduct($return_id, $return_product_data);
                        $this->model_sale_return->editReturnStatus($return_id, 2);
                        
                        /*
                        $return_id = $this->model_sale_order_change->addChange($return_data);
                        $this->model_sale_order_change->addChangeProduct($return_id, $return_product_data);
                        
                        
                        if($return_credits != 0){
                            
                            $price_dif_comment = "订单 " . $order_id . " 出库商品缺货 ,退余额";
                            $this->model_sale_customer->addTransaction($order_customer['customer_id'], $price_dif_comment, $return_credits, $order_id,9,$return_id);
                             
                    }
                        */
                        
                }

                    $this->load->model('sale/customer');
                    //无价签订单、快销品订单 不补价格调整差价
                    if($return_credits_price_dif != 0 && $order_customer['is_nopricetag'] != 0 && $order_customer['station_id'] != 2){

                        $return_data['change_type_id'] = 3;
                        $return_data['change_credits'] = $return_credits_price_dif;
                        $return_id = $this->model_sale_order_change->addChange($return_data);
                        
                        $this->model_sale_order_change->addChangeProduct($return_id, $return_credits_price_dif_product);
                        

                        $price_dif_comment = "订单 " . $order_id . " 出库商品价格调整,补差价";
                        $this->model_sale_customer->addTransaction($order_customer['customer_id'], $price_dif_comment, $return_credits_price_dif, $order_id,8,$return_id);
                    }

                    if($return_credits_weight_dif != 0){
                        $return_data['change_type_id'] = 2;
                        $return_data['change_credits'] = $return_credits_weight_dif;
                        $return_id = $this->model_sale_order_change->addChange($return_data);
                        $this->model_sale_order_change->addChangeProduct($return_id, $return_credits_weight_dif_product);
                        $price_dif_comment = "订单 " . $order_id . " 出库商品重量差异 ,补差价";
                        $this->model_sale_customer->addTransaction($order_customer['customer_id'], $price_dif_comment, $return_credits_weight_dif, $order_id,6,$return_id);
                    }
                }

                //Add MSG Tasks
                //Get status setting, customer accept setting insert into msg
                $sql = "
                    INSERT INTO `oc_msg` (`merchant_id`, `customer_id`, `phone`, `order_id`, `msg_param_1`, `msg_param_2`, `isp_template_id`, `msg_type`, `msg_status_id`,`msg_status_name`,`sent`, `status`, `date_added`)
                    SELECT 0, o.customer_id, o.shipping_phone, " . $order_id . ", '" . $order_id . "', if(o.shipping_code='D2D' or o.order_deliver_status_id=5, st.contact_phone, ps.close_time), mt.isp_template_id, mt.msg_type, o.order_deliver_status_id, os.name, 0, 1, NOW()
                    FROM oc_order o
                    LEFT JOIN oc_order_deliver_status os ON o.order_deliver_status_id = os.order_deliver_status_id AND os.language_id = 2
                    LEFT JOIN oc_x_pickupspot ps ON o.pickupspot_id = ps.pickupspot_id
                    LEFT JOIN oc_x_station st ON o.station_id = st.station_id
                    LEFT JOIN oc_msg_template mt ON os.msg_template_id = mt.msg_template_id
                    LEFT JOIN oc_customer c ON o.customer_id = c.customer_id
                    WHERE
                    o.order_id = '" . $order_id . "' AND o.order_deliver_status_id = '" . $status_id . "'
                    AND os.msg = 1 AND c.accept_order_message = 1
                    ";
                $this->db->query($sql);

                $this->session->data['success'] = "订单[{$_REQUEST['order_id']}]配送状态已更新";

                $url = $this->setUrl();
                $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            }
        }else{
            $this->error['warning'] = "未知的订单编号或状态编号";
        }

        $this->getList();
    }

    public function payment() {

        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        $this->error['warning'] = "未知的订单编号或状态编号";
        if (isset($_REQUEST['order_id']) || isset($_REQUEST['status_id'])) {
            $order_id = (int) $_REQUEST['order_id'];
            $status_id = (int) $_REQUEST['status_id'];

            $sql = "update  " . DB_PREFIX . "order set payment_method='货到付款', payment_code='COD', order_payment_status_id = '{$status_id}' where order_id = '{$order_id}'";
            $result = false;
            $result = $this->db->query($sql);

            if ($result) {
                //SUCCESS
                $this->addOrderHistory($order_id);

                //Add MSG Tasks
                //Get status setting, insert into msg
                $sql = "
                    INSERT INTO `oc_msg` (`merchant_id`, `customer_id`, `phone`, `order_id`, `msg_param_1`, `msg_param_2`, `isp_template_id`, `msg_type`, `msg_status_id`,`msg_status_name`,`sent`, `status`, `date_added`)
                    SELECT 0, o.customer_id, o.shipping_phone, " . $order_id . ", '" . $order_id . "', st.contact_phone, mt.isp_template_id, mt.msg_type, o.order_payment_status_id, os.name, 0, 1, NOW()
                    FROM oc_order o
                    LEFT JOIN oc_order_payment_status os ON o.order_payment_status_id = os.order_payment_status_id AND os.language_id = 2
                    LEFT JOIN oc_x_station st ON o.station_id = st.station_id
                    LEFT JOIN oc_msg_template mt ON os.msg_template_id = mt.msg_template_id
                    LEFT JOIN oc_customer c ON o.customer_id = c.customer_id
                    WHERE
                    o.order_id = '" . $order_id . "' AND o.order_payment_status_id = '" . $status_id . "'
                    AND os.msg = 1 AND c.accept_order_message = 1
                    ";
                $this->db->query($sql);



                $this->session->data['success'] = "订单[{$_REQUEST['order_id']}]支付状态已更新";

                $url = $this->setUrl();
                $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            } else {
                //ERROR
                $this->error['warning'] = "订单[{$_REQUEST['order_id']}]支付状态更新失败";
            }
        }

        $this->getList();
    }

    public function order_print_status() {

        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        $this->error['warning'] = "未知的订单编号或状态编号";
        if (isset($_REQUEST['order_id'])) {
            $order_id = (int) $_REQUEST['order_id'];

            $sql = "update " . DB_PREFIX . "order set order_print_status = 1 where order_id = '{$order_id}'";
            $result = false;
            $result = $this->db->query($sql);

            if ($result) {





                $this->session->data['success'] = "订单[{$_REQUEST['order_id']}]打印状态已更新";

                $url = $this->setUrl();
                $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            } else {
                //ERROR
                $this->error['warning'] = "订单[{$_REQUEST['order_id']}]打印状态更新失败";
            }
        }

        $this->getList();
    }

    public function order_bd() {

        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        $this->error['warning'] = "未知的订单编号或状态编号";
        if (isset($_REQUEST['order_id'])) {
            $order_id = (int) $_REQUEST['order_id'];

            $sql = "update " . DB_PREFIX . "order set bd_id = " . (int) $_REQUEST['status_id'] . " where order_id = '{$order_id}'";
            $result = false;
            $result = $this->db->query($sql);

            $order_info = $this->model_sale_order->getOrder($order_id);

            $sql = "update " . DB_PREFIX . "customer set bd_id = " . (int) $_REQUEST['status_id'] . " where customer_id = '{$order_info['customer_id']}'";
            $result = false;
            $result = $this->db->query($sql);

            if ($result) {





                $this->session->data['success'] = "订单[{$_REQUEST['order_id']}]及用户所属BD已更新";

                $url = $this->setUrl();
                $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
            } else {
                //ERROR
                $this->error['warning'] = "订单[{$_REQUEST['order_id']}]及用户所属BD更新失败";
            }
        }

        $this->getList();
    }

    public function customer_group_id() {

        return false;
        //TBD
        //不再更改无价签,代码待移除

        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        $this->error['warning'] = "未知的订单编号或状态编号";
        if (isset($_REQUEST['order_id'])) {
            $order_id = (int) $_REQUEST['order_id'];


            $order_info = $this->model_sale_order->getOrder($order_id);
            $this_time_h = (int) date("H", time() + 8 * 3600);
            $next_day = date("Y-m-d", time() + 8 * 3600 + 24 * 3600);

            if ($this_time_h >= 19 && $order_info['deliver_date'] == $next_day) {
                $this->error['warning'] = '晚7点以后不能修改第二天送货订单的价签属性';
            } else {
                $sql = "update " . DB_PREFIX . "order set customer_group_id = " . (int) $_REQUEST['status_id'] . " where order_id = '{$order_id}'";
                $result = false;
                $result = $this->db->query($sql);



                $sql = "update " . DB_PREFIX . "customer set customer_group_id = " . (int) $_REQUEST['status_id'] . " where customer_id = '{$order_info['customer_id']}'";
                $result = false;
                $result = $this->db->query($sql);

                if ($result) {
                    $this->session->data['success'] = "订单[{$_REQUEST['order_id']}]及用户的商品价签属性已更新";

                    $url = $this->setUrl();
                    $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
                } else {
                    //ERROR
                    $this->error['warning'] = "订单[{$_REQUEST['order_id']}]及用户的商品价签属性更新失败";
                }
            }
        }

        $this->getList();
    }

    public function updatePaymentStatus() {
        //TODO
        return false;
    }

    public function updateOrderStatus() {
        //TODO
        return false;
    }

    public function updateDeliverStatus() {
        //TODO
        return false;
    }

    public function updateDeliverDate() {
        //TODO
        return false;
    }

    public function add() {
        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        unset($this->session->data['cookie']);

        if ($this->validate()) {
            // API
            $this->load->model('user/api');

            $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

            if ($api_info) {
                $curl = curl_init();

                // Set SSL if required
                if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
                    curl_setopt($curl, CURLOPT_PORT, 443);
                }

                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

                $json = curl_exec($curl);

                if (!$json) {
                    $this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
                } else {
                    $response = json_decode($json, true);

                    if (isset($response['cookie'])) {
                        $this->session->data['cookie'] = $response['cookie'];
                    }

                    curl_close($curl);
                }
            }
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        unset($this->session->data['cookie']);

        if ($this->validate()) {
            // API
            $this->load->model('user/api');

            $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

            if ($api_info) {
                $curl = curl_init();

                // Set SSL if required
                if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
                    curl_setopt($curl, CURLOPT_PORT, 443);
                }

                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

                $json = curl_exec($curl);

                if (!$json) {
                    $this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
                } else {
                    $response = json_decode($json, true);

                    if (isset($response['cookie'])) {
                        $this->session->data['cookie'] = $response['cookie'];
                    }

                    curl_close($curl);
                }
            }
        }

        $this->getForm();
    }

    public function delete() {
        return false; //DO NOT DELETE
        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        unset($this->session->data['cookie']);

        if (isset($this->request->get['order_id']) && $this->validate()) {
            // API
            $this->load->model('user/api');

            $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

            if ($api_info) {
                $curl = curl_init();

                // Set SSL if required
                if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
                    curl_setopt($curl, CURLOPT_PORT, 443);
                }

                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

                $json = curl_exec($curl);

                if (!$json) {
                    $this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
                } else {
                    $response = json_decode($json, true);

                    if (isset($response['cookie'])) {
                        $this->session->data['cookie'] = $response['cookie'];
                    }

                    curl_close($curl);
                }
            }
        }

        if (isset($this->session->data['cookie'])) {
            $curl = curl_init();

            // Set SSL if required
            if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
                curl_setopt($curl, CURLOPT_PORT, 443);
            }

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/order/delete&order_id=' . $this->request->get['order_id']);
            curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');

            $json = curl_exec($curl);

            if (!$json) {
                $this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
            } else {
                $response = json_decode($json, true);

                curl_close($curl);

                if (isset($response['error'])) {
                    $this->error['warning'] = $response['error'];
                }
            }
        }

        if (isset($response['error'])) {
            $this->error['warning'] = $response['error'];
        }

        if (isset($response['success'])) {
            $this->session->data['success'] = $response['success'];

            $url = $this->setUrl();

            $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {
        $data['header'] = $this->load->controller('common/header');
        //暂时用session处理全局的warehouse_id_global
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();

        if($data['filter_warehouse_id_global']){
            $filter_warehouse_id_global = $data['filter_warehouse_id_global'];
        }else{
            $filter_warehouse_id_global = 0;
        }

        $this->load->language('sale/order');

        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = null;
        }

        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = null;
        }

        if (isset($this->request->get['filter_order_status'])) {
            $filter_order_status = $this->request->get['filter_order_status'];
        } else {
            $filter_order_status = null;
        }

        if (isset($this->request->get['filter_order_payment_status'])) {
            $filter_order_payment_status = $this->request->get['filter_order_payment_status'];
        } else {
            $filter_order_payment_status = null;
        }

        if (isset($this->request->get['filter_order_deliver_status'])) {
            $filter_order_deliver_status = $this->request->get['filter_order_deliver_status'];
        } else {
            $filter_order_deliver_status = null;
        }

        if (isset($this->request->get['filter_customer_group'])) {
            $filter_customer_group = $this->request->get['filter_customer_group'];
        } else {
            $filter_customer_group = null;
        }

        if (isset($this->request->get['filter_station'])) {
            $filter_station = $this->request->get['filter_station'];
        } else {
            $filter_station = null;
        }

        if (isset($this->request->get['filter_driver'])) {
            $filter_driver = $this->request->get['filter_driver'];
        } else {
            $filter_driver = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $filter_date_modified = $this->request->get['filter_date_modified'];
        } else {
            $filter_date_modified = null;
        }

        if (isset($this->request->get['filter_deliver_date'])) {
            $filter_deliver_date = $this->request->get['filter_deliver_date'];
        } else {
            $filter_deliver_date = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.order_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = $this->setUrl();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'], 'SSL');
        $data['shipping'] = $this->url->link('sale/order/shipping', 'token=' . $this->session->data['token'], 'SSL');
        $data['add'] = $this->url->link('sale/order/add', 'token=' . $this->session->data['token'], 'SSL');

        $data['orders'] = array();

        $filter_data = array(
            'filter_order_id' => $filter_order_id,
            'filter_customer' => $filter_customer,
            'filter_order_status' => $filter_order_status,
            'filter_order_payment_status' => $filter_order_payment_status,
            'filter_order_deliver_status' => $filter_order_deliver_status,
            'filter_customer_group' => $filter_customer_group,
            'filter_station' => $filter_station,
            'filter_driver' => $filter_driver,
            'filter_date_added' => $filter_date_added,
            'filter_date_modified' => $filter_date_modified,
            'filter_deliver_date' => $filter_deliver_date,
            'filter_warehouse_id_global' => $filter_warehouse_id_global,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $order_total = $this->model_sale_order->getTotalOrders($filter_data);

        $results = $this->model_sale_order->getOrders($filter_data);
        $data['currentListSize'] = sizeof($results);


        $bds = $this->model_sale_order->getBds();
        $data['bds'] = $bds;

        foreach ($results as $result) {
            $data['orders'][] = array(
                'orderid' => $result['orderid'],
                'type' => $result['type'],
                'order_id' => $result['order_id'],
                'customer' => $result['customer'],
                'status' => $result['status'],
                'deliver_status' => $result['deliver_status'],
                'payment_status_id' => $result['order_payment_status_id'],
                'payment_status' => $result['payment_status'],
                'payment_method' => $result['payment_method'],
                'payment_code' => $result['payment_code'],
                'order_status_id' => $result['order_status_id'],
                'order_deliver_status_id' => $result['order_deliver_status_id'],
                'order_payment_status_id' => $result['order_payment_status_id'],
                'pickupspot_id' => $result['pickupspot_id'],
                'pspot_short_name' => $result['pspot_short_name'],
                'pspot_name' => $result['pspot_name'],
                'deliver_date' => $result['deliver_date'],
                'shipping_address' => $result['shipping_address_1'],
                'shipping_method' => $result['shipping_method'],
                'shipping_code' => $result['shipping_code'],
                'shipping_firstname' => $result['shipping_firstname'],
                'shipping_phone' => $result['shipping_phone'],
                'logistic_driver_title'=>$result['logistic_driver_title'],
                'logistic_driver_phone'=>$result['logistic_driver_phone'],
                'comment' => $result['comment'],
                'firstorder' => $result['firstorder'],
                'bd_name' => $result['bd_name'],
                'bd_phone' => $result['bd_phone'],
                'customer_group_id' => $result['customer_group_id'],
                'is_nopricetag' => $result['is_nopricetag'],
                'group_shortname' => $result['group_shortname'],
                'sub_total' => $result['sub_total'],
                'shipping_fee' => $result['shipping_fee'],
                'discount_total' => $result['discount_total'],
                'credit_paid' => $result['credit_paid'],
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'order_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'date_added' => $result['date_added'],
                //'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
                'date_modified' => $result['date_modified'],
                'shipping_code' => $result['shipping_code'],
                'view' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL'),
                'edit' => $this->url->link('sale/order/edit', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL'),
                'order_print_status' => $result['order_print_status'],
                'bd_id' => $result['bd_id'],
                'station_id' => $result['station_id'],
                'allot_flag' => $result['allot_flag'],

                'user_point_paid' => round($result['user_point_paid']).'.00',
                'wxpay_paid' => round($result['wxpay_paid']).'.00',

                'need_pay' => $this->currency->format(round($result['need_pay']).'.00', $result['currency_code'], $result['currency_value']),
                    //DO NOT DELETE/ 'delete'        => $this->url->link('sale/order/delete', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_missing'] = $this->language->get('text_missing');

        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_date_modified'] = $this->language->get('column_date_modified');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_return_id'] = $this->language->get('entry_return_id');
        $data['entry_order_id'] = $this->language->get('entry_order_id');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['entry_date_modified'] = $this->language->get('entry_date_modified');

        $data['button_invoice_print'] = $this->language->get('button_invoice_print');
        $data['button_shipping_print'] = $this->language->get('button_shipping_print');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_view'] = $this->language->get('button_view');

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = $this->setUrl();

        //if ($order == 'ASC') {
        //    $url .= '&order=ASC';
        //} else {
        //    $url .= '&order=DESC';
        //}


        $data['sort_order'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
        $data['sort_customer'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
        $data['sort_total'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
        $data['sort_date_added'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
        $data['sort_date_modified'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

        $data['sort_deliver_date'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&sort=o.deliver_date' . $url, 'SSL');

        //$url = $this->setUrl();
        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_order_id'] = $filter_order_id;
        $data['filter_customer'] = $filter_customer;
        $data['filter_order_status'] = $filter_order_status;
        $data['filter_order_payment_status'] = $filter_order_payment_status;
        $data['filter_order_deliver_status'] = $filter_order_deliver_status;
        $data['filter_customer_group'] = $filter_customer_group;
        $data['filter_station'] = $filter_station;
        $data['filter_driver'] = $filter_driver;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_date_modified'] = $filter_date_modified;

        $data['filter_deliver_date'] = $filter_deliver_date;

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['order_payment_statuses'] = $this->model_sale_order->getOrderPaymentStatus();
        $data['order_deliver_statuses'] = $this->model_sale_order->getOrderDeliverStatus();

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['user_group_id'] = $this->user->user_group_id;

        //Set User Permition for User Groups
        //商务信息: 管理员｜用户订单管理 | 商务助理 1,20,38
        //订单状态(取消订单): 管理员｜用户订单管理 1,20
        //配送状态: 管理员｜仓库主管｜仓库值班主管｜物流配送 1,17,21,18 => 20160718 改为客服（用户订单管理）
        //打印状态: 管理员｜仓库主管｜仓库值班主管 1,17,21
        //支付信息: 管理员｜财务管理 1,26
        $data['updateCustInfo'] = in_array($data['user_group_id'], array(1, 20,38)) ? true : false;
        $data['updateCustTagInfo'] = in_array($data['user_group_id'], array(1, 20, 28)) ? true : false;
        $data['updateOrderStatus'] = in_array($data['user_group_id'], array(1, 17, 20, 28)) ? true : false;
        $data['updateDeliverOn'] = in_array($data['user_group_id'], array(1,17,15,21)) ? true : false; //管理员，仓库主管，分拣组长可改配送出库
        $data['updateDeliverStatus'] = in_array($data['user_group_id'], array(1,17,18,20,28)) ? true : false; //管理员，物流主管，客服可更改配送状态
        $data['updateReDeliverStatus'] = in_array($data['user_group_id'], array(1,20,28)) ? true : false; //管理员，客服可更改重新配送状态
        $data['updatePrintFlag'] = in_array($data['user_group_id'], array(1, 17, 21)) ? true : false;
        $data['updatePayment'] = in_array($data['user_group_id'], array(1, 26, 33)) ? true : false;

        //Load Driver List
        $this->load->model('common/common');
        $this->load->model('station/station');
        $data['driverList'] = $this->model_common_common->getLogisticDriverList();

        //Load customer group
        $data['customer_groups'] = $this->model_common_common->getCustomerGroupList();

        //load station
        //$data['order_stations'] = array(1=>"生鲜",2=>"非生鲜");
        $data['stations'] = $this->model_station_station->getStationList();
        $data['station_set'] = $this->model_station_station->setFilterStation($filter_warehouse_id_global);

        $this->response->setOutput($this->load->view('sale/order_list.tpl', $data));
    }

    public function getForm() {
        $this->load->model('sale/customer');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['order_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['text_product'] = $this->language->get('text_product');
        $data['text_voucher'] = $this->language->get('text_voucher');
        $data['text_order'] = $this->language->get('text_order');

        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['entry_fax'] = $this->language->get('entry_fax');
        $data['entry_comment'] = $this->language->get('entry_comment');
        $data['entry_feadback'] = $this->language->get('entry_feadback');
        $data['entry_affiliate'] = $this->language->get('entry_affiliate');
        $data['entry_address'] = $this->language->get('entry_address');
        $data['entry_company'] = $this->language->get('entry_company');
        $data['entry_address_1'] = $this->language->get('entry_address_1');
        $data['entry_address_2'] = $this->language->get('entry_address_2');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_postcode'] = $this->language->get('entry_postcode');
        $data['entry_zone'] = $this->language->get('entry_zone');
        $data['entry_zone_code'] = $this->language->get('entry_zone_code');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_product'] = $this->language->get('entry_product');
        $data['entry_option'] = $this->language->get('entry_option');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_to_name'] = $this->language->get('entry_to_name');
        $data['entry_to_email'] = $this->language->get('entry_to_email');
        $data['entry_from_name'] = $this->language->get('entry_from_name');
        $data['entry_from_email'] = $this->language->get('entry_from_email');
        $data['entry_theme'] = $this->language->get('entry_theme');
        $data['entry_message'] = $this->language->get('entry_message');
        $data['entry_amount'] = $this->language->get('entry_amount');
        $data['entry_shipping_method'] = $this->language->get('entry_shipping_method');
        $data['entry_payment_method'] = $this->language->get('entry_payment_method');
        $data['entry_coupon'] = $this->language->get('entry_coupon');
        $data['entry_voucher'] = $this->language->get('entry_voucher');
        $data['entry_reward'] = $this->language->get('entry_reward');
        $data['entry_order_status'] = $this->language->get('entry_order_status');

        $data['column_product'] = $this->language->get('column_product');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');
        $data['button_product_add'] = $this->language->get('button_product_add');
        $data['button_voucher_add'] = $this->language->get('button_voucher_add');

        $data['button_payment'] = $this->language->get('button_payment');
        $data['button_shipping'] = $this->language->get('button_shipping');
        $data['button_coupon'] = $this->language->get('button_coupon');
        $data['button_voucher'] = $this->language->get('button_voucher');
        $data['button_reward'] = $this->language->get('button_reward');
        $data['button_upload'] = $this->language->get('button_upload');
        $data['button_remove'] = $this->language->get('button_remove');

        $data['tab_order'] = $this->language->get('tab_order');
        $data['tab_customer'] = $this->language->get('tab_customer');
        $data['tab_payment'] = $this->language->get('tab_payment');
        $data['tab_shipping'] = $this->language->get('tab_shipping');
        $data['tab_product'] = $this->language->get('tab_product');
        $data['tab_voucher'] = $this->language->get('tab_voucher');
        $data['tab_total'] = $this->language->get('tab_total');

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_customer_group'])) {
            $url .= '&filter_customer_group=' . $this->request->get['filter_customer_group'];
        }
        if (isset($this->request->get['filter_station'])) {
            $url .= '&filter_station=' . $this->request->get['filter_station'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['filter_deliver_date'])) {
            $url .= '&filter_deliver_date=' . $this->request->get['filter_deliver_date'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['cancel'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['order_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);
        }

        $data['user_group_id'] = $this->user->user_group_id;
        if (!empty($order_info)) {
            $data['orderid'] = $this->request->get['orderid'];
            $data['type'] = $this->request->get['type'];
            $data['order_id'] = $this->request->get['order_id'];
            $data['store_id'] = $order_info['store_id'];

            $data['customer'] = $order_info['customer'];
            $data['customer_id'] = $order_info['customer_id'];
            $data['customer_group_id'] = $order_info['customer_group_id'];
            $data['firstname'] = $order_info['firstname'];
            $data['lastname'] = $order_info['lastname'];
            $data['email'] = $order_info['email'];
            $data['telephone'] = $order_info['telephone'];
            $data['fax'] = $order_info['fax'];
            $data['account_custom_field'] = $order_info['custom_field'];

            $this->load->model('sale/customer');

            $data['addresses'] = $this->model_sale_customer->getAddresses($order_info['customer_id']);

            $data['payment_firstname'] = $order_info['payment_firstname'];
            $data['payment_lastname'] = $order_info['payment_lastname'];
            $data['payment_company'] = $order_info['payment_company'];
            $data['payment_address_1'] = $order_info['payment_address_1'];
            $data['payment_address_2'] = $order_info['payment_address_2'];
            $data['payment_city'] = $order_info['payment_city'];
            $data['payment_postcode'] = $order_info['payment_postcode'];
            $data['payment_country_id'] = $order_info['payment_country_id'];
            $data['payment_zone_id'] = $order_info['payment_zone_id'];
            $data['payment_custom_field'] = $order_info['payment_custom_field'];
            $data['payment_method'] = $order_info['payment_method'];
            $data['payment_code'] = $order_info['payment_code'];

            $data['shipping_firstname'] = $order_info['shipping_firstname'];
            $data['shipping_lastname'] = $order_info['shipping_lastname'];
            $data['shipping_company'] = $order_info['shipping_company'];
            $data['shipping_address_1'] = $order_info['shipping_address_1'];
            $data['shipping_address_2'] = $order_info['shipping_address_2'];
            $data['shipping_city'] = $order_info['shipping_city'];
            $data['shipping_postcode'] = $order_info['shipping_postcode'];
            $data['shipping_country_id'] = $order_info['shipping_country_id'];
            $data['shipping_zone_id'] = $order_info['shipping_zone_id'];
            $data['shipping_custom_field'] = $order_info['shipping_custom_field'];
            $data['shipping_method'] = $order_info['shipping_method'];
            $data['shipping_code'] = $order_info['shipping_code'];

            // Add products to the API
            $data['products'] = array();

            $products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);

            foreach ($products as $product) {
                $data['order_products'][] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']),
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total' => $product['total'],
                    'reward' => $product['reward']
                );
            }

            // Add vouchers to the API
            $data['order_vouchers'] = $this->model_sale_order->getOrderVouchers($this->request->get['order_id']);

            $data['coupon'] = '';
            $data['voucher'] = '';
            $data['reward'] = '';

            $data['order_totals'] = array();

            $order_totals = $this->model_sale_order->getOrderTotals($this->request->get['order_id']);

            foreach ($order_totals as $order_total) {
                // If coupon, voucher or reward points
                $start = strpos($order_total['title'], '(') + 1;
                $end = strrpos($order_total['title'], ')');

                if ($start && $end) {
                    if ($order_total['code'] == 'coupon') {
                        $data['coupon'] = substr($order_total['title'], $start, $end - $start);
                    }

                    if ($order_total['code'] == 'voucher') {
                        $data['voucher'] = substr($order_total['title'], $start, $end - $start);
                    }

                    if ($order_total['code'] == 'reward') {
                        $data['reward'] = substr($order_total['title'], $start, $end - $start);
                    }
                }
            }

            $data['order_status_id'] = $order_info['order_status_id'];
            $data['comment'] = $order_info['comment'];
            $data['affiliate_id'] = $order_info['affiliate_id'];
            $data['affiliate'] = $order_info['affiliate_firstname'] . ' ' . $order_info['affiliate_lastname'];
        } else {
            $data['order_id'] = 0;
            $data['store_id'] = '';
            $data['customer'] = '';
            $data['customer_id'] = '';
            $data['customer_group_id'] = $this->config->get('config_customer_group_id');
            $data['firstname'] = '';
            $data['lastname'] = '';
            $data['email'] = '';
            $data['telephone'] = '';
            $data['fax'] = '';
            $data['customer_custom_field'] = array();

            $data['addresses'] = array();

            $data['payment_firstname'] = '';
            $data['payment_lastname'] = '';
            $data['payment_company'] = '';
            $data['payment_address_1'] = '';
            $data['payment_address_2'] = '';
            $data['payment_city'] = '';
            $data['payment_postcode'] = '';
            $data['payment_country_id'] = '';
            $data['payment_zone_id'] = '';
            $data['payment_custom_field'] = array();
            $data['payment_method'] = '';
            $data['payment_code'] = '';

            $data['shipping_firstname'] = '';
            $data['shipping_lastname'] = '';
            $data['shipping_company'] = '';
            $data['shipping_address_1'] = '';
            $data['shipping_address_2'] = '';
            $data['shipping_city'] = '';
            $data['shipping_postcode'] = '';
            $data['shipping_country_id'] = '';
            $data['shipping_zone_id'] = '';
            $data['shipping_custom_field'] = array();
            $data['shipping_method'] = '';
            $data['shipping_code'] = '';

            $data['order_products'] = array();
            $data['order_vouchers'] = array();
            $data['order_totals'] = array();

            $data['order_status_id'] = $this->config->get('config_order_status_id');

            $data['comment'] = '';
            $data['affiliate_id'] = '';
            $data['affiliate'] = '';

            $data['coupon'] = '';
            $data['voucher'] = '';
            $data['reward'] = '';
        }

        // Stores
        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        // Customer Groups
        $this->load->model('sale/customer_group');

        $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

        // Custom Fields
        $this->load->model('sale/custom_field');

        $data['custom_fields'] = array();

        $custom_fields = $this->model_sale_custom_field->getCustomFields();

        foreach ($custom_fields as $custom_field) {
            $data['custom_fields'][] = array(
                'custom_field_id' => $custom_field['custom_field_id'],
                'custom_field_value' => $this->model_sale_custom_field->getCustomFieldValues($custom_field['custom_field_id']),
                'name' => $custom_field['name'],
                'value' => $custom_field['value'],
                'type' => $custom_field['type'],
                'location' => $custom_field['location']
            );
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/country');

        $data['countries'] = $this->model_localisation_country->getCountries();

        $data['voucher_min'] = $this->config->get('config_voucher_min');

        $this->load->model('sale/voucher_theme');

        $data['voucher_themes'] = $this->model_sale_voucher_theme->getVoucherThemes();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        //Order detail page control
        $data['lock'] = false;
        $data['cancelled'] = '';
        if ($data['order_status_id'] == CANCELLED_ORDER_STATUS) {
            $data['lock'] = true;
            $data['cancelled'] = 'cancelled_order'; //CSS Style to show cross line and gery text
        }

        
       
        $this->response->setOutput($this->load->view('sale/order_form.tpl', $data));
    }

    public function info() {
        $this->load->model('sale/order');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        $data['user_group_id'] = $this->user->user_group_id;
        $order_info = $this->model_sale_order->getOrder($order_id);

        if ($order_info) {
            $this->load->language('sale/order');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_order_id'] = $this->language->get('text_order_id');
            $data['text_invoice_no'] = $this->language->get('text_invoice_no');
            $data['text_invoice_date'] = $this->language->get('text_invoice_date');
            $data['text_store_name'] = $this->language->get('text_store_name');
            $data['text_store_url'] = $this->language->get('text_store_url');
            $data['text_customer'] = $this->language->get('text_customer');
            $data['text_customer_group'] = $this->language->get('text_customer_group');
            $data['text_email'] = $this->language->get('text_email');
            $data['text_telephone'] = $this->language->get('text_telephone');
            $data['text_fax'] = $this->language->get('text_fax');
            $data['text_total'] = $this->language->get('text_total');
            $data['text_reward'] = $this->language->get('text_reward');
            $data['text_order_status'] = $this->language->get('text_order_status');
            $data['text_comment'] = $this->language->get('text_comment');
            $data['text_affiliate'] = $this->language->get('text_affiliate');
            $data['text_commission'] = $this->language->get('text_commission');
            $data['text_ip'] = $this->language->get('text_ip');
            $data['text_forwarded_ip'] = $this->language->get('text_forwarded_ip');
            $data['text_user_agent'] = $this->language->get('text_user_agent');
            $data['text_accept_language'] = $this->language->get('text_accept_language');
            $data['text_date_added'] = $this->language->get('text_date_added');
            $data['text_date_modified'] = $this->language->get('text_date_modified');
            $data['text_firstname'] = $this->language->get('text_firstname');
            $data['text_lastname'] = $this->language->get('text_lastname');
            $data['text_company'] = $this->language->get('text_company');
            $data['text_address_1'] = $this->language->get('text_address_1');
            $data['text_address_2'] = $this->language->get('text_address_2');
            $data['text_city'] = $this->language->get('text_city');
            $data['text_postcode'] = $this->language->get('text_postcode');
            $data['text_zone'] = $this->language->get('text_zone');
            $data['text_zone_code'] = $this->language->get('text_zone_code');
            $data['text_country'] = $this->language->get('text_country');
            $data['text_shipping_method'] = $this->language->get('text_shipping_method');
            $data['text_payment_method'] = $this->language->get('text_payment_method');
            $data['text_history'] = $this->language->get('text_history');
            $data['text_feadback'] = $this->language->get('text_feadback');
            $data['text_country_match'] = $this->language->get('text_country_match');
            $data['text_country_code'] = $this->language->get('text_country_code');
            $data['text_high_risk_country'] = $this->language->get('text_high_risk_country');
            $data['text_distance'] = $this->language->get('text_distance');
            $data['text_ip_region'] = $this->language->get('text_ip_region');
            $data['text_ip_city'] = $this->language->get('text_ip_city');
            $data['text_ip_latitude'] = $this->language->get('text_ip_latitude');
            $data['text_ip_longitude'] = $this->language->get('text_ip_longitude');
            $data['text_ip_isp'] = $this->language->get('text_ip_isp');
            $data['text_ip_org'] = $this->language->get('text_ip_org');
            $data['text_ip_asnum'] = $this->language->get('text_ip_asnum');
            $data['text_ip_user_type'] = $this->language->get('text_ip_user_type');
            $data['text_ip_country_confidence'] = $this->language->get('text_ip_country_confidence');
            $data['text_ip_region_confidence'] = $this->language->get('text_ip_region_confidence');
            $data['text_ip_city_confidence'] = $this->language->get('text_ip_city_confidence');
            $data['text_ip_postal_confidence'] = $this->language->get('text_ip_postal_confidence');
            $data['text_ip_postal_code'] = $this->language->get('text_ip_postal_code');
            $data['text_ip_accuracy_radius'] = $this->language->get('text_ip_accuracy_radius');
            $data['text_ip_net_speed_cell'] = $this->language->get('text_ip_net_speed_cell');
            $data['text_ip_metro_code'] = $this->language->get('text_ip_metro_code');
            $data['text_ip_area_code'] = $this->language->get('text_ip_area_code');
            $data['text_ip_time_zone'] = $this->language->get('text_ip_time_zone');
            $data['text_ip_region_name'] = $this->language->get('text_ip_region_name');
            $data['text_ip_domain'] = $this->language->get('text_ip_domain');
            $data['text_ip_country_name'] = $this->language->get('text_ip_country_name');
            $data['text_ip_continent_code'] = $this->language->get('text_ip_continent_code');
            $data['text_ip_corporate_proxy'] = $this->language->get('text_ip_corporate_proxy');
            $data['text_anonymous_proxy'] = $this->language->get('text_anonymous_proxy');
            $data['text_proxy_score'] = $this->language->get('text_proxy_score');
            $data['text_is_trans_proxy'] = $this->language->get('text_is_trans_proxy');
            $data['text_free_mail'] = $this->language->get('text_free_mail');
            $data['text_carder_email'] = $this->language->get('text_carder_email');
            $data['text_high_risk_username'] = $this->language->get('text_high_risk_username');
            $data['text_high_risk_password'] = $this->language->get('text_high_risk_password');
            $data['text_bin_match'] = $this->language->get('text_bin_match');
            $data['text_bin_country'] = $this->language->get('text_bin_country');
            $data['text_bin_name_match'] = $this->language->get('text_bin_name_match');
            $data['text_bin_name'] = $this->language->get('text_bin_name');
            $data['text_bin_phone_match'] = $this->language->get('text_bin_phone_match');
            $data['text_bin_phone'] = $this->language->get('text_bin_phone');
            $data['text_customer_phone_in_billing_location'] = $this->language->get('text_customer_phone_in_billing_location');
            $data['text_ship_forward'] = $this->language->get('text_ship_forward');
            $data['text_city_postal_match'] = $this->language->get('text_city_postal_match');
            $data['text_ship_city_postal_match'] = $this->language->get('text_ship_city_postal_match');
            $data['text_score'] = $this->language->get('text_score');
            $data['text_explanation'] = $this->language->get('text_explanation');
            $data['text_risk_score'] = $this->language->get('text_risk_score');
            $data['text_queries_remaining'] = $this->language->get('text_queries_remaining');
            $data['text_maxmind_id'] = $this->language->get('text_maxmind_id');
            $data['text_error'] = $this->language->get('text_error');
            $data['text_loading'] = $this->language->get('text_loading');

            $data['help_country_match'] = $this->language->get('help_country_match');
            $data['help_country_code'] = $this->language->get('help_country_code');
            $data['help_high_risk_country'] = $this->language->get('help_high_risk_country');
            $data['help_distance'] = $this->language->get('help_distance');
            $data['help_ip_region'] = $this->language->get('help_ip_region');
            $data['help_ip_city'] = $this->language->get('help_ip_city');
            $data['help_ip_latitude'] = $this->language->get('help_ip_latitude');
            $data['help_ip_longitude'] = $this->language->get('help_ip_longitude');
            $data['help_ip_isp'] = $this->language->get('help_ip_isp');
            $data['help_ip_org'] = $this->language->get('help_ip_org');
            $data['help_ip_asnum'] = $this->language->get('help_ip_asnum');
            $data['help_ip_user_type'] = $this->language->get('help_ip_user_type');
            $data['help_ip_country_confidence'] = $this->language->get('help_ip_country_confidence');
            $data['help_ip_region_confidence'] = $this->language->get('help_ip_region_confidence');
            $data['help_ip_city_confidence'] = $this->language->get('help_ip_city_confidence');
            $data['help_ip_postal_confidence'] = $this->language->get('help_ip_postal_confidence');
            $data['help_ip_postal_code'] = $this->language->get('help_ip_postal_code');
            $data['help_ip_accuracy_radius'] = $this->language->get('help_ip_accuracy_radius');
            $data['help_ip_net_speed_cell'] = $this->language->get('help_ip_net_speed_cell');
            $data['help_ip_metro_code'] = $this->language->get('help_ip_metro_code');
            $data['help_ip_area_code'] = $this->language->get('help_ip_area_code');
            $data['help_ip_time_zone'] = $this->language->get('help_ip_time_zone');
            $data['help_ip_region_name'] = $this->language->get('help_ip_region_name');
            $data['help_ip_domain'] = $this->language->get('help_ip_domain');
            $data['help_ip_country_name'] = $this->language->get('help_ip_country_name');
            $data['help_ip_continent_code'] = $this->language->get('help_ip_continent_code');
            $data['help_ip_corporate_proxy'] = $this->language->get('help_ip_corporate_proxy');
            $data['help_anonymous_proxy'] = $this->language->get('help_anonymous_proxy');
            $data['help_proxy_score'] = $this->language->get('help_proxy_score');
            $data['help_is_trans_proxy'] = $this->language->get('help_is_trans_proxy');
            $data['help_free_mail'] = $this->language->get('help_free_mail');
            $data['help_carder_email'] = $this->language->get('help_carder_email');
            $data['help_high_risk_username'] = $this->language->get('help_high_risk_username');
            $data['help_high_risk_password'] = $this->language->get('help_high_risk_password');
            $data['help_bin_match'] = $this->language->get('help_bin_match');
            $data['help_bin_country'] = $this->language->get('help_bin_country');
            $data['help_bin_name_match'] = $this->language->get('help_bin_name_match');
            $data['help_bin_name'] = $this->language->get('help_bin_name');
            $data['help_bin_phone_match'] = $this->language->get('help_bin_phone_match');
            $data['help_bin_phone'] = $this->language->get('help_bin_phone');
            $data['help_customer_phone_in_billing_location'] = $this->language->get('help_customer_phone_in_billing_location');
            $data['help_ship_forward'] = $this->language->get('help_ship_forward');
            $data['help_city_postal_match'] = $this->language->get('help_city_postal_match');
            $data['help_ship_city_postal_match'] = $this->language->get('help_ship_city_postal_match');
            $data['help_score'] = $this->language->get('help_score');
            $data['help_explanation'] = $this->language->get('help_explanation');
            $data['help_risk_score'] = $this->language->get('help_risk_score');
            $data['help_queries_remaining'] = $this->language->get('help_queries_remaining');
            $data['help_maxmind_id'] = $this->language->get('help_maxmind_id');
            $data['help_error'] = $this->language->get('help_error');

            $data['column_product'] = $this->language->get('column_product');
            $data['column_model'] = $this->language->get('column_model');
            $data['column_quantity'] = $this->language->get('column_quantity');
            $data['column_price'] = $this->language->get('column_price');
            $data['column_total'] = $this->language->get('column_total');

            $data['entry_order_status'] = $this->language->get('entry_order_status');
            $data['entry_notify'] = $this->language->get('entry_notify');
            $data['entry_comment'] = $this->language->get('entry_comment');
            $data['entry_feadback'] = $this->language->get('entry_feadback');
            $data['button_invoice_print'] = $this->language->get('button_invoice_print');
            $data['button_shipping_print'] = $this->language->get('button_shipping_print');
            $data['button_edit'] = $this->language->get('button_edit');
            $data['button_cancel'] = $this->language->get('button_cancel');
            $data['button_generate'] = $this->language->get('button_generate');
            $data['button_reward_add'] = $this->language->get('button_reward_add');
            $data['button_reward_remove'] = $this->language->get('button_reward_remove');
            $data['button_commission_add'] = $this->language->get('button_commission_add');
            $data['button_commission_remove'] = $this->language->get('button_commission_remove');
            $data['button_history_add'] = $this->language->get('button_history_add');
            $data['button_feadback_add']=$this->language->get('button_feadback_add');

            $data['tab_order'] = $this->language->get('tab_order');
            $data['tab_payment'] = $this->language->get('tab_payment');
            $data['tab_shipping'] = $this->language->get('tab_shipping');
            $data['tab_product'] = $this->language->get('tab_product');
            $data['tab_history'] = $this->language->get('tab_history');
            $data['tab_fraud'] = $this->language->get('tab_fraud');
            $data['tab_action'] = $this->language->get('tab_action');

            $data['token'] = $this->session->data['token'];

            $url = '';

            if (isset($this->request->get['filter_order_id'])) {
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            if (isset($this->request->get['filter_customer'])) {
                $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_order_status'])) {
                $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
            }

            if (isset($this->request->get['filter_customer_group'])) {
                $url .= '&filter_customer_group=' . $this->request->get['filter_customer_group'];
            }
            if (isset($this->request->get['filter_station'])) {
                $url .= '&filter_station=' . $this->request->get['filter_station'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['filter_date_modified'])) {
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            }

            if (isset($this->request->get['filter_deliver_date'])) {
                $url .= '&filter_deliver_date=' . $this->request->get['filter_deliver_date'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL')
            );

            $data['shipping'] = $this->url->link('sale/order/shipping', 'token=' . $this->session->data['token'] . '&order_id=' . (int) $this->request->get['order_id'], 'SSL');
            $data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'] . '&order_id=' . (int) $this->request->get['order_id'], 'SSL');
            $data['edit'] = $this->url->link('sale/order/edit', 'token=' . $this->session->data['token'] . '&order_id=' . (int) $this->request->get['order_id'], 'SSL');
            $data['cancel'] = $this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL');

            $data['order_id'] = $this->request->get['order_id'];
            $data['orderid'] = $order_info['orderid'];
            $data['type'] = $order_info['type'];

            if ($order_info['invoice_no']) {
                $data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
            } else {
                $data['invoice_no'] = '';
            }

            $data['store_name'] = $order_info['store_name'];
            $data['store_url'] = $order_info['store_url'];
            $data['firstname'] = $order_info['firstname'];
            $data['lastname'] = $order_info['lastname'];

            if ($order_info['customer_id']) {
                $data['customer'] = $this->url->link('sale/customer/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $order_info['customer_id'], 'SSL');
            } else {
                $data['customer'] = '';
            }

            $this->load->model('sale/customer_group');

            $customer_group_info = $this->model_sale_customer_group->getCustomerGroup($order_info['customer_group_id']);

            if ($customer_group_info) {
                $data['customer_group'] = $customer_group_info['name'];
            } else {
                $data['customer_group'] = '';
            }

            $data['email'] = $order_info['email'];
            $data['telephone'] = $order_info['telephone'];
            $data['fax'] = $order_info['fax'];

            $data['account_custom_field'] = $order_info['custom_field'];


            // Custom Fields
            $this->load->model('sale/custom_field');

            $data['account_custom_fields'] = array();

            $custom_fields = $this->model_sale_custom_field->getCustomFields();

            foreach ($custom_fields as $custom_field) {
                if ($custom_field['location'] == 'account' && isset($order_info['custom_field'][$custom_field['custom_field_id']])) {
                    if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
                        $custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($order_info['custom_field'][$custom_field['custom_field_id']]);

                        if ($custom_field_value_info) {
                            $data['account_custom_fields'][] = array(
                                'name' => $custom_field['name'],
                                'value' => $custom_field_value_info['name']
                            );
                        }
                    }

                    if ($custom_field['type'] == 'checkbox' && is_array($order_info['custom_field'][$custom_field['custom_field_id']])) {
                        foreach ($order_info['custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
                            $custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($custom_field_value_id);

                            if ($custom_field_value_info) {
                                $data['account_custom_fields'][] = array(
                                    'name' => $custom_field['name'],
                                    'value' => $custom_field_value_info['name']
                                );
                            }
                        }
                    }

                    if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
                        $data['account_custom_fields'][] = array(
                            'name' => $custom_field['name'],
                            'value' => $order_info['custom_field'][$custom_field['custom_field_id']]
                        );
                    }

                    if ($custom_field['type'] == 'file') {
                        $upload_info = $this->model_tool_upload->getUploadByCode($order_info['custom_field'][$custom_field['custom_field_id']]);

                        if ($upload_info) {
                            $data['account_custom_fields'][] = array(
                                'name' => $custom_field['name'],
                                'value' => $upload_info['name']
                            );
                        }
                    }
                }
            }

            $data['comment'] = nl2br($order_info['comment']);
            $data['shipping_method'] = $order_info['shipping_method'];
            $data['payment_method'] = $order_info['payment_method'];
            $data['total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);

            $this->load->model('sale/customer');

            $data['reward'] = $order_info['reward'];

            $data['reward_total'] = $this->model_sale_customer->getTotalCustomerRewardsByOrderId($this->request->get['order_id']);

            $data['affiliate_firstname'] = $order_info['affiliate_firstname'];
            $data['affiliate_lastname'] = $order_info['affiliate_lastname'];

            if ($order_info['affiliate_id']) {
                $data['affiliate'] = $this->url->link('marketing/affiliate/edit', 'token=' . $this->session->data['token'] . '&affiliate_id=' . $order_info['affiliate_id'], 'SSL');
            } else {
                $data['affiliate'] = '';
            }

            $data['commission'] = $this->currency->format($order_info['commission'], $order_info['currency_code'], $order_info['currency_value']);

            $this->load->model('marketing/affiliate');

            $data['commission_total'] = $this->model_marketing_affiliate->getTotalTransactionsByOrderId($this->request->get['order_id']);

            $this->load->model('localisation/order_status');

            $order_status_info = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);

            if ($order_status_info) {
                $data['order_status'] = $order_status_info['name'];
            } else {
                $data['order_status'] = '';
            }

            $data['ip'] = $order_info['ip'];
            $data['forwarded_ip'] = $order_info['forwarded_ip'];
            $data['user_agent'] = $order_info['user_agent'];
            $data['accept_language'] = $order_info['accept_language'];
            $data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
            $data['date_modified'] = date($this->language->get('date_format_short'), strtotime($order_info['date_modified']));

            // Payment
            $data['payment_firstname'] = $order_info['payment_firstname'];
            $data['payment_lastname'] = $order_info['payment_lastname'];
            $data['payment_company'] = $order_info['payment_company'];
            $data['payment_address_1'] = $order_info['payment_address_1'];
            $data['payment_address_2'] = $order_info['payment_address_2'];
            $data['payment_city'] = $order_info['payment_city'];
            $data['payment_postcode'] = $order_info['payment_postcode'];
            $data['payment_zone'] = $order_info['payment_zone'];
            $data['payment_zone_code'] = $order_info['payment_zone_code'];
            $data['payment_country'] = $order_info['payment_country'];

            // Uploaded files			
            $this->load->model('tool/upload');

            // Custom fields
            $data['payment_custom_fields'] = array();

            foreach ($custom_fields as $custom_field) {
                if ($custom_field['location'] == 'address' && isset($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                    if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
                        $custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($order_info['payment_custom_field'][$custom_field['custom_field_id']]);

                        if ($custom_field_value_info) {
                            $data['payment_custom_fields'][] = array(
                                'name' => $custom_field['name'],
                                'value' => $custom_field_value_info['name']
                            );
                        }
                    }

                    if ($custom_field['type'] == 'checkbox' && is_array($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
                        foreach ($order_info['payment_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
                            $custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($custom_field_value_id);

                            if ($custom_field_value_info) {
                                $data['payment_custom_fields'][] = array(
                                    'name' => $custom_field['name'],
                                    'value' => $custom_field_value_info['name']
                                );
                            }
                        }
                    }

                    if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
                        $data['payment_custom_fields'][] = array(
                            'name' => $custom_field['name'],
                            'value' => $order_info['payment_custom_field'][$custom_field['custom_field_id']]
                        );
                    }

                    if ($custom_field['type'] == 'file') {
                        $upload_info = $this->model_tool_upload->getUploadByCode($order_info['payment_custom_field'][$custom_field['custom_field_id']]);

                        if ($upload_info) {
                            $data['payment_custom_fields'][] = array(
                                'name' => $custom_field['name'],
                                'value' => $upload_info['name']
                            );
                        }
                    }
                }
            }

            // Shipping
            $data['shipping_firstname'] = $order_info['shipping_firstname'];
            $data['shipping_lastname'] = $order_info['shipping_lastname'];
            $data['shipping_company'] = $order_info['shipping_company'];
            $data['shipping_address_1'] = $order_info['shipping_address_1'];
            $data['shipping_address_2'] = $order_info['shipping_address_2'];
            $data['shipping_city'] = $order_info['shipping_city'];
            $data['shipping_postcode'] = $order_info['shipping_postcode'];
            $data['shipping_zone'] = $order_info['shipping_zone'];
            $data['shipping_zone_code'] = $order_info['shipping_zone_code'];
            $data['shipping_country'] = $order_info['shipping_country'];

            $data['shipping_custom_fields'] = array();

            foreach ($custom_fields as $custom_field) {
                if ($custom_field['location'] == 'address' && isset($order_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
                    if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
                        $custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($order_info['shipping_custom_field'][$custom_field['custom_field_id']]);

                        if ($custom_field_value_info) {
                            $data['shipping_custom_fields'][] = array(
                                'name' => $custom_field['name'],
                                'value' => $custom_field_value_info['name']
                            );
                        }
                    }

                    if ($custom_field['type'] == 'checkbox' && is_array($order_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
                        foreach ($order_info['shipping_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
                            $custom_field_value_info = $this->model_sale_custom_field->getCustomFieldValue($custom_field_value_id);

                            if ($custom_field_value_info) {
                                $data['shipping_custom_fields'][] = array(
                                    'name' => $custom_field['name'],
                                    'value' => $custom_field_value_info['name']
                                );
                            }
                        }
                    }

                    if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
                        $data['shipping_custom_fields'][] = array(
                            'name' => $custom_field['name'],
                            'value' => $order_info['shipping_custom_field'][$custom_field['custom_field_id']]
                        );
                    }

                    if ($custom_field['type'] == 'file') {
                        $upload_info = $this->model_tool_upload->getUploadByCode($order_info['shipping_custom_field'][$custom_field['custom_field_id']]);

                        if ($upload_info) {
                            $data['shipping_custom_fields'][] = array(
                                'name' => $custom_field['name'],
                                'value' => $upload_info['name']
                            );
                        }
                    }
                }
            }

            $data['products'] = array();

            
$products_weight_inv = array();     
$products_weight_inv_arr = array();
$products_weight_inv = $this->model_sale_order->getOrderProductsWeightInv($order_id);
foreach ($products_weight_inv as $k => $v) {
    //$v['total'] = round($v['total']/0.8,2);
    //$v['total'] = $v['total']/0.8;
    $products_weight_inv_arr[$v['product_id']] = $v;
}

//echo "<pre>";print_r($products_weight_inv_arr);




            $products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);




            foreach ($products as $product) {
                $option_data = array();

                $options = $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

                foreach ($options as $option) {
                    if ($option['type'] != 'file') {
                        $option_data[] = array(
                            'name' => $option['name'],
                            'value' => $option['value'],
                            'type' => $option['type']
                        );
                    } else {
                        $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                        if ($upload_info) {
                            $option_data[] = array(
                                'name' => $option['name'],
                                'value' => $upload_info['name'],
                                'type' => $option['type'],
                                'href' => $this->url->link('tool/upload/download', 'token=' . $this->session->data['token'] . '&code=' . $upload_info['code'], 'SSL')
                            );
                        }
                    }
                }

                $data['products'][] = array(
                    'order_product_id' => $product['order_product_id'],
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'quantity' => $product['quantity'],
                    'weight' => $product['weight'],
                    'weight_inv_flag' => $product['weight_inv_flag'],
                    'product_expiry_date' => date("Y-m-d",strtotime($order_info['deliver_date']) + ($product['shelf_life'] - 1)*3600*24),
                    'product_price' => $product['price'],
                    'price' => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'total' => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'href' => $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL')
                );
            }

            $data['vouchers'] = array();

            $vouchers = $this->model_sale_order->getOrderVouchers($this->request->get['order_id']);

            foreach ($vouchers as $voucher) {
                $data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'amount' => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
                    'href' => $this->url->link('sale/voucher/edit', 'token=' . $this->session->data['token'] . '&voucher_id=' . $voucher['voucher_id'], 'SSL')
                );
            }

            $totals = $this->model_sale_order->getOrderTotals($this->request->get['order_id']);

            foreach ($totals as $total) {
                $data['totals'][] = array(
                    'title' => $total['title'],
                    'text' => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
                );
            }

            $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

            $data['order_status_id'] = $order_info['order_status_id'];

            // Unset any past sessions this page date_added for the api to work.
            unset($this->session->data['cookie']);

            // Set up the API session
            if ($this->user->hasPermission('modify', 'sale/order')) {
                $this->load->model('user/api');

                $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

                if ($api_info) {
                    $curl = curl_init();

                    // Set SSL if required
                    if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
                        curl_setopt($curl, CURLOPT_PORT, 443);
                    }

                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                    curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

                    $json = curl_exec($curl);

                    if (!$json) {
                        $data['error_warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
                    } else {
                        $response = json_decode($json, true);
                    }

                    if (isset($response['cookie'])) {
                        $this->session->data['cookie'] = $response['cookie'];
                    }
                }
            }

            if (isset($response['cookie'])) {
                $this->session->data['cookie'] = $response['cookie'];
            } else {
                $data['error_warning'] = $this->language->get('error_permission');
            }

            // Fraud
            $this->load->model('sale/fraud');

            $fraud_info = $this->model_sale_fraud->getFraud($order_info['order_id']);

            if ($fraud_info) {
                $data['country_match'] = $fraud_info['country_match'];

                if ($fraud_info['country_code']) {
                    $data['country_code'] = $fraud_info['country_code'];
                } else {
                    $data['country_code'] = '';
                }

                $data['high_risk_country'] = $fraud_info['high_risk_country'];
                $data['distance'] = $fraud_info['distance'];

                if ($fraud_info['ip_region']) {
                    $data['ip_region'] = $fraud_info['ip_region'];
                } else {
                    $data['ip_region'] = '';
                }

                if ($fraud_info['ip_city']) {
                    $data['ip_city'] = $fraud_info['ip_city'];
                } else {
                    $data['ip_city'] = '';
                }

                $data['ip_latitude'] = $fraud_info['ip_latitude'];
                $data['ip_longitude'] = $fraud_info['ip_longitude'];

                if ($fraud_info['ip_isp']) {
                    $data['ip_isp'] = $fraud_info['ip_isp'];
                } else {
                    $data['ip_isp'] = '';
                }

                if ($fraud_info['ip_org']) {
                    $data['ip_org'] = $fraud_info['ip_org'];
                } else {
                    $data['ip_org'] = '';
                }

                $data['ip_asnum'] = $fraud_info['ip_asnum'];

                if ($fraud_info['ip_user_type']) {
                    $data['ip_user_type'] = $fraud_info['ip_user_type'];
                } else {
                    $data['ip_user_type'] = '';
                }

                if ($fraud_info['ip_country_confidence']) {
                    $data['ip_country_confidence'] = $fraud_info['ip_country_confidence'];
                } else {
                    $data['ip_country_confidence'] = '';
                }

                if ($fraud_info['ip_region_confidence']) {
                    $data['ip_region_confidence'] = $fraud_info['ip_region_confidence'];
                } else {
                    $data['ip_region_confidence'] = '';
                }

                if ($fraud_info['ip_city_confidence']) {
                    $data['ip_city_confidence'] = $fraud_info['ip_city_confidence'];
                } else {
                    $data['ip_city_confidence'] = '';
                }

                if ($fraud_info['ip_postal_confidence']) {
                    $data['ip_postal_confidence'] = $fraud_info['ip_postal_confidence'];
                } else {
                    $data['ip_postal_confidence'] = '';
                }

                if ($fraud_info['ip_postal_code']) {
                    $data['ip_postal_code'] = $fraud_info['ip_postal_code'];
                } else {
                    $data['ip_postal_code'] = '';
                }

                $data['ip_accuracy_radius'] = $fraud_info['ip_accuracy_radius'];

                if ($fraud_info['ip_net_speed_cell']) {
                    $data['ip_net_speed_cell'] = $fraud_info['ip_net_speed_cell'];
                } else {
                    $data['ip_net_speed_cell'] = '';
                }

                $data['ip_metro_code'] = $fraud_info['ip_metro_code'];
                $data['ip_area_code'] = $fraud_info['ip_area_code'];

                if ($fraud_info['ip_time_zone']) {
                    $data['ip_time_zone'] = $fraud_info['ip_time_zone'];
                } else {
                    $data['ip_time_zone'] = '';
                }

                if ($fraud_info['ip_region_name']) {
                    $data['ip_region_name'] = $fraud_info['ip_region_name'];
                } else {
                    $data['ip_region_name'] = '';
                }

                if ($fraud_info['ip_domain']) {
                    $data['ip_domain'] = $fraud_info['ip_domain'];
                } else {
                    $data['ip_domain'] = '';
                }

                if ($fraud_info['ip_country_name']) {
                    $data['ip_country_name'] = $fraud_info['ip_country_name'];
                } else {
                    $data['ip_country_name'] = '';
                }

                if ($fraud_info['ip_continent_code']) {
                    $data['ip_continent_code'] = $fraud_info['ip_continent_code'];
                } else {
                    $data['ip_continent_code'] = '';
                }

                if ($fraud_info['ip_corporate_proxy']) {
                    $data['ip_corporate_proxy'] = $fraud_info['ip_corporate_proxy'];
                } else {
                    $data['ip_corporate_proxy'] = '';
                }

                $data['anonymous_proxy'] = $fraud_info['anonymous_proxy'];
                $data['proxy_score'] = $fraud_info['proxy_score'];

                if ($fraud_info['is_trans_proxy']) {
                    $data['is_trans_proxy'] = $fraud_info['is_trans_proxy'];
                } else {
                    $data['is_trans_proxy'] = '';
                }

                $data['free_mail'] = $fraud_info['free_mail'];
                $data['carder_email'] = $fraud_info['carder_email'];

                if ($fraud_info['high_risk_username']) {
                    $data['high_risk_username'] = $fraud_info['high_risk_username'];
                } else {
                    $data['high_risk_username'] = '';
                }

                if ($fraud_info['high_risk_password']) {
                    $data['high_risk_password'] = $fraud_info['high_risk_password'];
                } else {
                    $data['high_risk_password'] = '';
                }

                $data['bin_match'] = $fraud_info['bin_match'];

                if ($fraud_info['bin_country']) {
                    $data['bin_country'] = $fraud_info['bin_country'];
                } else {
                    $data['bin_country'] = '';
                }

                $data['bin_name_match'] = $fraud_info['bin_name_match'];

                if ($fraud_info['bin_name']) {
                    $data['bin_name'] = $fraud_info['bin_name'];
                } else {
                    $data['bin_name'] = '';
                }

                $data['bin_phone_match'] = $fraud_info['bin_phone_match'];

                if ($fraud_info['bin_phone']) {
                    $data['bin_phone'] = $fraud_info['bin_phone'];
                } else {
                    $data['bin_phone'] = '';
                }

                if ($fraud_info['customer_phone_in_billing_location']) {
                    $data['customer_phone_in_billing_location'] = $fraud_info['customer_phone_in_billing_location'];
                } else {
                    $data['customer_phone_in_billing_location'] = '';
                }

                $data['ship_forward'] = $fraud_info['ship_forward'];

                if ($fraud_info['city_postal_match']) {
                    $data['city_postal_match'] = $fraud_info['city_postal_match'];
                } else {
                    $data['city_postal_match'] = '';
                }

                if ($fraud_info['ship_city_postal_match']) {
                    $data['ship_city_postal_match'] = $fraud_info['ship_city_postal_match'];
                } else {
                    $data['ship_city_postal_match'] = '';
                }

                $data['score'] = $fraud_info['score'];
                $data['explanation'] = $fraud_info['explanation'];
                $data['risk_score'] = $fraud_info['risk_score'];
                $data['queries_remaining'] = $fraud_info['queries_remaining'];
                $data['maxmind_id'] = $fraud_info['maxmind_id'];
                $data['error'] = $fraud_info['error'];
            } else {
                $data['maxmind_id'] = '';
            }

            $data['payment_action'] = $this->load->controller('payment/' . $order_info['payment_code'] . '/orderAction', '');

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');






            //出库
            $stock_products = $this->model_sale_order->getOrderInventoryProduct($order_id);

            //退货
            $this->load->model('sale/return');
            $return_products = $this->model_sale_return->getReturnProducts(array("filter_order_id" => $order_id, "filter_no_return_status_id" => 3));

            if (!empty($return_products)) {
                foreach ($return_products as $rpkey => $rpvalue) {
                    if (isset($return_products[$rpvalue['rp_product_id']])) {
                        $return_products[$rpvalue['rp_product_id']] += $rpvalue['rp_quantity'];
                    } else {
                        $return_products[$rpvalue['rp_product_id']] = $rpvalue['rp_quantity'];
                    }
                    unset($return_products[$rpkey]);
                }
            }

            //缺货
            $change_products = array();
            $this->load->model('sale/order_change');
            $change_products = $this->model_sale_order_change->getChangeProducts(array("filter_order_id" => $order_id, "filter_change_status_id" => 2, "filter_change_type_id" => 1));

            if(!empty($change_products)){
                foreach($change_products as $cp_key=>$cp_value){
                    if (isset($return_products[$cp_value['rp_product_id']])) {
                        $return_products[$cp_value['rp_product_id']] += $cp_value['rp_quantity'];
                    }
                    else{
                        $return_products[$cp_value['rp_product_id']] = $cp_value['rp_quantity'];
                    }
                }
            }

            
            
            $data['stock_products'] = $stock_products;
            $data['return_products'] = $return_products;


            $data['products_weight_inv_arr'] = $products_weight_inv_arr;

            $this->load->model('localisation/return_reason');
            $data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();

            $this->load->model('localisation/return_action');
            $data['return_actions'] = $this->model_localisation_return_action->getReturnActions();



            $data['customer_container_move'] = $this->model_sale_order->getContainerMove($this->request->get['order_id']);

            //Add order feed back
            $feadback_type = $this->model_sale_order->feadbacktype();
            $data['feadback_type'] = $feadback_type;

            $this->response->setOutput($this->load->view('sale/order_info.tpl', $data));
        } else {
            $this->load->language('error/not_found');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_not_found'] = $this->language->get('text_not_found');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('error/not_found.tpl', $data));
        }
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function createInvoiceNo() {
        $this->load->language('sale/order');

        $json = array();

        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $json['error'] = $this->language->get('error_permission');
        } elseif (isset($this->request->get['order_id'])) {
            if (isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }

            $this->load->model('sale/order');

            $invoice_no = $this->model_sale_order->createInvoiceNo($order_id);

            if ($invoice_no) {
                $json['invoice_no'] = $invoice_no;
            } else {
                $json['error'] = $this->language->get('error_action');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function addReward() {
        $this->load->language('sale/order');

        $json = array();

        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            if (isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }

            $this->load->model('sale/order');

            $order_info = $this->model_sale_order->getOrder($order_id);

            if ($order_info && $order_info['customer_id'] && ($order_info['reward'] > 0)) {
                $this->load->model('sale/customer');

                $reward_total = $this->model_sale_customer->getTotalCustomerRewardsByOrderId($order_id);

                if (!$reward_total) {
                    $this->model_sale_customer->addReward($order_info['customer_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['reward'], $order_id);
                }
            }

            $json['success'] = $this->language->get('text_reward_added');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function removeReward() {
        $this->load->language('sale/order');

        $json = array();

        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            if (isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }

            $this->load->model('sale/order');

            $order_info = $this->model_sale_order->getOrder($order_id);

            if ($order_info) {
                $this->load->model('sale/customer');

                $this->model_sale_customer->deleteReward($order_id);
            }

            $json['success'] = $this->language->get('text_reward_removed');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function addCommission() {
        $this->load->language('sale/order');

        $json = array();

        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            if (isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }

            $this->load->model('sale/order');

            $order_info = $this->model_sale_order->getOrder($order_id);

            if ($order_info) {
                $this->load->model('marketing/affiliate');

                $affiliate_total = $this->model_marketing_affiliate->getTotalTransactionsByOrderId($order_id);

                if (!$affiliate_total) {
                    $this->model_marketing_affiliate->addTransaction($order_info['affiliate_id'], $this->language->get('text_order_id') . ' #' . $order_id, $order_info['commission'], $order_id);
                }
            }

            $json['success'] = $this->language->get('text_commission_added');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function removeCommission() {
        $this->load->language('sale/order');

        $json = array();

        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            if (isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }

            $this->load->model('sale/order');

            $order_info = $this->model_sale_order->getOrder($order_id);

            if ($order_info) {
                $this->load->model('marketing/affiliate');

                $this->model_marketing_affiliate->deleteTransaction($order_id);
            }

            $json['success'] = $this->language->get('text_commission_removed');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function addreturnproduct() {
        $this->load->language('sale/order');
        $this->load->model('sale/return');
        $this->load->model('sale/order');
        $json = array();
        $order_returned_wait = $this->model_sale_return->getReturnProducts(array("filter_order_id" => $this->request->get['order_id'], "filter_return_status_id" => 1));
        $order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);

        if (!$this->user->hasPermission('modify', 'sale/order')) {
            $json['error'] = $this->language->get('error_permission');
        } else if (($order_info['order_payment_status_id'] != 2 && $_POST['return_action'] != 4 && $_POST['return_action'] != 1) || $order_info['order_status_id'] == 3) {
            $json['error'] = "订单已取消或未支付，不能退货";
        } else if (!empty($order_returned_wait)) {
            $json['error'] = "本订单还有未处理的退货，请先处理后再提交";
        } else {
            if (isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }


            $this->load->model('localisation/return_action');

            $return_actions = $this->model_localisation_return_action->getReturnActions();


            $return_reason_id = $_POST['return_reason'];
            $return_action_id = $_POST['return_action'];
            $return_comment = $_POST['return_comment'];
            
            
            
            foreach ($return_actions as $rak => $rav) {
                if ($return_action_id == $rav['return_action_id']) {
                    $return_status_id = $rav['return_status'];
                    $return_inventory_flag = $rav['return_inventory'];
                    $return_action_detail = $rav;
                }
            }

            $return_product = json_decode($_POST['return_product_str']);
            $return_product_credit = json_decode($_POST['return_product_credit_str']);
            $return_product_desc = json_decode($_POST['return_product_desc_str']);
            $return_product_action = json_decode($_POST['return_product_action_str']);

            $order_product_info = $this->model_sale_order->getOrderProducts($order_id);
            $order_product_id_info = array();
            foreach($order_product_info as $key=>$value){
                $order_product_id_info[$value['product_id']] = $value;
            }

            foreach($return_product as $rk=>$rv){

                if(!isset($return_product_credit->$rk)){
                    $json['error'] = "商品 " . $rk . " 无退货金额，请检查";
                    echo json_encode($json);
                    exit;
                }
                //退货金额不能为负数
                if($return_product_credit->$rk < 0 ){
                    $json['error'] = "商品 " . $rk . " 退货金额不能为负数，请检查";
                    echo json_encode($json);
                    exit;
            }
                //退货金额为0时，商品金额也必须为0
                if($return_product_credit->$rk == 0 && $order_product_id_info[$rk]['price'] != 0 ){
                    $json['error'] = "商品 " . $rk . " 退货金额不能为0，请检查";
                    echo json_encode($json);
                    exit;
                }
            
            }
            


            
            $order_returned = $this->model_sale_return->getReturnProducts(array("filter_order_id" => $order_id, "filter_no_return_status_id" => 3));


            $order_returned_product = array();
            $returned_fresh_credits = 0;
            if (!empty($order_returned)) {
                foreach ($order_returned as $ork => $orv) {
                    if (empty($order_returned_product[$orv['rp_product_id']])) {
                        $order_returned_product[$orv['rp_product_id']] = $orv['rp_quantity'];
                    } else {
                        $order_returned_product[$orv['rp_product_id']] += $orv['rp_quantity'];
                    }

                    if ($orv['rp_product_id'] < 5000) {
                        $returned_fresh_credits += $orv['return_product_credits'];
                    }
                }
            }


            //缺货
            $change_products = array();
            $this->load->model('sale/order_change');
            $change_products = $this->model_sale_order_change->getChangeProducts(array("filter_order_id" => $order_id, "filter_change_status_id" => 2, "filter_change_type_id" => 1));
            
            if(!empty($change_products)){
                foreach($change_products as $cp_key=>$cp_value){
                    if (isset($order_returned_product[$cp_value['rp_product_id']])) {
                        $order_returned_product[$cp_value['rp_product_id']] += $cp_value['rp_quantity'];
                    }
                    else{
                        $order_returned_product[$cp_value['rp_product_id']] = $cp_value['rp_quantity'];
                    }
                }
            }

            
            

            if ($order_info) {
                $this->load->model('sale/return');
                $data = array();

                $data['order_id'] = $order_id;
                $data['date_ordered'] = date("Y-m-d", strtotime($order_info['date_added']));
                $data['customer'] = $order_info['customer'];
                $data['customer_id'] = $order_info['customer_id'];
                $data['firstname'] = $order_info['firstname'];
                $data['lastname'] = $order_info['lastname'];
                $data['email'] = $order_info['email'];
                $data['telephone'] = $order_info['telephone'];
                //$data['product'] = $order_info[''];
                //$data['product_id'] = $order_info[''];
                //$data['model'] = $order_info[''];
                //$data['quantity'] = $order_info[''];
                $data['return_reason_id'] = $return_reason_id;
                $data['opened'] = 0;
                $data['comment'] = $return_comment;
                $data['return_action_id'] = $return_action_id;
                $data['return_status_id'] = $return_status_id;
                $data['return_inventory_flag'] = $return_inventory_flag;
                $data['add_user'] = $this->user->getId();


                $ret_product_arr = array();
                $return_credits = 0;
                $return_fresh_product = 0;
                $fressh_product_total = 0;

              
                foreach ($order_product_info as $opik => $opiv) {
                    if ($opiv['product_id'] < 5000) {
                        $fressh_product_total += $opiv['total'];
                    }
                }
                foreach ($return_product as $rpk => $rpv) {
                    foreach ($order_product_info as $key => $value) {
                        if ($rpk == $value['product_id']) {

                            if (empty($order_returned_product[$rpk])) {
                                if ($rpv > $value['quantity']) {
                                    $json['error'] = '退货数量不能超过订货数量！';
                                    echo json_encode($json);
                                    exit;
                                }
                            } else {
                                if ($rpv + $order_returned_product[$rpk] > $value['quantity']) {
                                    $json['error'] = '退货数量不能超过订货数量！';
                                    echo json_encode($json);
                                    exit;
                                }
                            }

                            if($value['weight_inv_flag'] == 1){
                                if(round(floatval($return_product_credit->$value['product_id']),2) > round($rpv * $value['price'] * $value['weight_range_most'],2) ){
                                    $json['error'] = '商品 ' . $value['product_id'] . ' 退货金额不能超过出库时的价格！';
                                    echo json_encode($json);
                                    exit;
                                }
                            }
                            else{
                                if(round(floatval($return_product_credit->$value['product_id']),2) > round($rpv * $value['price'],2) ){
                                    $json['error'] = '商品 ' . $value['product_id'] . ' 退货金额不能超过订单的价格！';
                                    echo json_encode($json);
                                    exit;
                                }
                            }


                            $value['quantity'] = $rpv;
                            if (isset($return_product_credit->$value['product_id'])) {
                                $value['return_product_credits'] = round($return_product_credit->$value['product_id'],2);
                            } else {
                                $value['return_product_credits'] = round($value['price'] * $rpv, 2);
                            }
                            
                            $value['return_product_desc'] = isset($return_product_desc->$value['product_id']) ? $return_product_desc->$value['product_id'] : '';
                            $value['return_product_action'] = isset($return_product_action->$value['product_id']) ? $return_product_action->$value['product_id'] : '';
                            
                            $ret_product_arr[$rpk] = $value;
                            if ($return_action_detail['return_credits'] == 1) {
                                $return_credits += $value['return_product_credits'];
                                if ($rpk < 5000) {
                                    $return_fresh_product += $value['return_product_credits'];
                                }
                            }
                        }
                    }
                }

                if ($return_credits > 0) {
                    $order_pay = 0;
                    $order_discount = 0;
                    $order_total = $this->model_sale_order->getOrderTotals($order_id);

                    foreach ($order_total as $otk => $otv) {
                        if (in_array($otv['code'], array("sub_total", "shipping_fee", "discount_total"))) {

                            $order_pay += $otv['value'];
                        }
                        if ($otv['code'] == "discount_total") {
                            $order_discount = abs($otv['value']);
                        }
                    }

                    if ($order_discount > 0 && $fressh_product_total - $returned_fresh_credits - $return_fresh_product < 200) {
                        $order_discount_factor = round($order_discount / ($order_discount + $order_pay), 2);
                        //$return_credits = round((1 - $order_discount_factor) * $return_credits,2);

                        $new_return_credits = 0;
                        foreach ($ret_product_arr as $rpak => $rpav) {
                            //$product_total = $rpav['quantity'] * $rpav['price'];
                            $product_total = $rpav['return_product_credits'];
                            if ($rpak < 5000) {
                                $ret_product_arr[$rpak]['return_product_credits'] = round((1 - $order_discount_factor) * $product_total, 2);
                            } else {
                                $ret_product_arr[$rpak]['return_product_credits'] = $product_total;
                            }
                            $new_return_credits += $ret_product_arr[$rpak]['return_product_credits'];
                        }
                        $return_credits = $new_return_credits;
                    }
                    $return_credits = $return_credits > $order_pay ? $order_pay : $return_credits;
                }

                $data['return_credits'] = $return_credits;


                $return_id = $this->model_sale_return->addReturn($data);



                $this->model_sale_return->addReturnProduct($return_id, $ret_product_arr);
            }

            $json['success'] = "提交退货成功";
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function country() {
        $json = array();

        $this->load->model('localisation/country');

        $country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

        if ($country_info) {
            $this->load->model('localisation/zone');

            $json = array(
                'country_id' => $country_info['country_id'],
                'name' => $country_info['name'],
                'iso_code_2' => $country_info['iso_code_2'],
                'iso_code_3' => $country_info['iso_code_3'],
                'address_format' => $country_info['address_format'],
                'postcode_required' => $country_info['postcode_required'],
                'zone' => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
                'status' => $country_info['status']
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function feadback(){
        $this->load->language('sale/order');
        $data['column_feadback_date'] = $this->language->get('column_feadback_date');
        $data['column_feadback_id'] = $this->language->get('column_feadback_id');
        $data['column_feadback_station'] = $this->language->get('column_feadback_station');
        $data['column_feadback_driver'] = $this->language->get('column_feadback_driver');

        $data['feadbacks']=array();

        $this->load->model('sale/order');

        $results = $this->model_sale_order->getFeadbacks($this->request->get['order_id']);

        foreach($results as $result){
            $data['feadbacks'][]=array(
                'date_added'=>date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'order_id'=>$result['order_id'],
                'total'=>$result['total'],
                'bd_name'=>$result['bd_name'],
                'shipping_name' =>$result['shipping_name'],
                'name'=>$result['name'],
                'feadback_options'=>$result['feadback_options'],
                'logistic_score'=>$result['logistic_score'],
                'cargo_check'=>$result['cargo_check'],
                'bill_of'=>$result['bill_of'],
                'box'=>$result['box'],
                'comments' =>$result['comments'],
                'user_comments'=>$result['user_comments'],
                'record_date'=>date($this->language->get('date_format_short'), strtotime($result['record_date'])),
                'logistic_driver_title'=>$result['logistic_driver_title'],
            );
        }

        $this->response->setOutput($this->load->view('sale/order_feadback.tpl',$data));
    }

    public function history() {
        $this->load->language('sale/order');

        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_notify'] = $this->language->get('column_notify');
        $data['column_comment'] = $this->language->get('column_comment');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['histories'] = array();

        $this->load->model('sale/order');

        $results = $this->model_sale_order->getOrderHistories($this->request->get['order_id'], ($page - 1) * 10, 10);

        foreach ($results as $result) {
            $data['histories'][] = array(
                'notify' => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
                'status' => $result['status'],
                'deliver_status' => $result['deliver_status'],
                'comment' => nl2br($result['comment']),
                'date_added' => $result['date_added']
            );
        }

        $history_total = $this->model_sale_order->getTotalOrderHistories($this->request->get['order_id']);

        $pagination = new Pagination();
        $pagination->total = $history_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link('sale/order/history', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

        $this->response->setOutput($this->load->view('sale/order_history.tpl', $data));
    }





    public function invoice() {
        $this->load->language('sale/order');

        $data['title'] = $this->language->get('text_invoice');

        if ($this->request->server['HTTPS']) {
            $data['base'] = HTTPS_SERVER;
        } else {
            $data['base'] = HTTP_SERVER;
        }

        $data['direction'] = $this->language->get('direction');
        $data['lang'] = $this->language->get('code');

        $data['text_invoice'] = $this->language->get('text_invoice');
        $data['text_order_detail'] = $this->language->get('text_order_detail');
        $data['text_order_id'] = $this->language->get('text_order_id');
        $data['text_invoice_no'] = $this->language->get('text_invoice_no');
        $data['text_invoice_date'] = $this->language->get('text_invoice_date');
        $data['text_date_added'] = $this->language->get('text_date_added');
        $data['text_telephone'] = $this->language->get('text_telephone');
        $data['text_fax'] = $this->language->get('text_fax');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_website'] = $this->language->get('text_website');
        $data['text_to'] = $this->language->get('text_to');
        $data['text_ship_to'] = $this->language->get('text_ship_to');
        $data['text_payment_method'] = $this->language->get('text_payment_method');
        $data['text_shipping_method'] = $this->language->get('text_shipping_method');

        $data['column_product'] = $this->language->get('column_product');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_comment'] = $this->language->get('column_comment');

        $this->load->model('sale/order');

        $this->load->model('setting/setting');

        $data['orders'] = array();

        $orders = array();

        if (isset($this->request->post['selected'])) {
            $orders = $this->request->post['selected'];
        } elseif (isset($this->request->get['order_id'])) {
            $orders[] = $this->request->get['order_id'];
        }

        foreach ($orders as $order_id) {
            $order_info = $this->model_sale_order->getOrder($order_id);

            if ($order_info) {
                $store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

                if ($store_info) {
                    $store_address = $store_info['config_address'];
                    $store_email = $store_info['config_email'];
                    $store_telephone = $store_info['config_telephone'];
                    $store_fax = $store_info['config_fax'];
                } else {
                    $store_address = $this->config->get('config_address');
                    $store_email = $this->config->get('config_email');
                    $store_telephone = $this->config->get('config_telephone');
                    $store_fax = $this->config->get('config_fax');
                }

                if ($order_info['invoice_no']) {
                    $invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
                } else {
                    $invoice_no = '';
                }

                if ($order_info['payment_address_format']) {
                    $format = $order_info['payment_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => $order_info['payment_firstname'],
                    'lastname' => $order_info['payment_lastname'],
                    'company' => $order_info['payment_company'],
                    'address_1' => $order_info['payment_address_1'],
                    'address_2' => $order_info['payment_address_2'],
                    'city' => $order_info['payment_city'],
                    'postcode' => $order_info['payment_postcode'],
                    'zone' => $order_info['payment_zone'],
                    'zone_code' => $order_info['payment_zone_code'],
                    'country' => $order_info['payment_country']
                );

                $payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                if ($order_info['shipping_address_format']) {
                    $format = $order_info['shipping_address_format'];
                } else {
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => $order_info['shipping_firstname'],
                    'lastname' => $order_info['shipping_lastname'],
                    'company' => $order_info['shipping_company'],
                    'address_1' => $order_info['shipping_address_1'],
                    'address_2' => $order_info['shipping_address_2'],
                    'city' => $order_info['shipping_city'],
                    'postcode' => $order_info['shipping_postcode'],
                    'zone' => $order_info['shipping_zone'],
                    'zone_code' => $order_info['shipping_zone_code'],
                    'country' => $order_info['shipping_country']
                );

                $shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

                $this->load->model('tool/upload');

                $product_data = array();

                $products = $this->model_sale_order->getOrderProducts($order_id);

                foreach ($products as $product) {
                    $option_data = array();

                    $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

                    foreach ($options as $option) {
                        if ($option['type'] != 'file') {
                            $value = $option['value'];
                        } else {
                            $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                            if ($upload_info) {
                                $value = $upload_info['name'];
                            } else {
                                $value = '';
                            }
                        }

                        $option_data[] = array(
                            'name' => $option['name'],
                            'value' => $value
                        );
                    }

                    $product_data[] = array(
                        'name' => $product['name'],
                        'model' => $product['model'],
                        'option' => $option_data,
                        'quantity' => $product['quantity'],
                        'price' => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                        'total' => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
                    );
                }

                $voucher_data = array();

                $vouchers = $this->model_sale_order->getOrderVouchers($order_id);

                foreach ($vouchers as $voucher) {
                    $voucher_data[] = array(
                        'description' => $voucher['description'],
                        'amount' => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
                    );
                }

                $total_data = array();

                $totals = $this->model_sale_order->getOrderTotals($order_id);

                foreach ($totals as $total) {
                    $total_data[] = array(
                        'title' => $total['title'],
                        'text' => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
                    );
                }

                $data['user_group_id'] = $this->user->user_group_id;
                $data['orders'][] = array(
                    'orderid' => $order_info['orderid'],
                    'type' => $order_info['type'],
                    'order_id' => $order_id,
                    'invoice_no' => $invoice_no,
                    'date_added' => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
                    'store_name' => $order_info['store_name'],
                    'store_url' => rtrim($order_info['store_url'], '/'),
                    'store_address' => nl2br($store_address),
                    'store_email' => $store_email,
                    'store_telephone' => $store_telephone,
                    'store_fax' => $store_fax,
                    'email' => $order_info['email'],
                    'telephone' => $order_info['telephone'],
                    'shipping_address' => $shipping_address,
                    'shipping_method' => $order_info['shipping_method'],
                    'payment_address' => $payment_address,
                    'payment_method' => $order_info['payment_method'],
                    'product' => $product_data,
                    'voucher' => $voucher_data,
                    'total' => $total_data,
                    'comment' => nl2br($order_info['comment'])
                );
            }
        }

        $this->response->setOutput($this->load->view('sale/order_invoice.tpl', $data));
    }

    public function shipping() {
        $this->load->language('sale/order');

        $data['title'] = $this->language->get('text_shipping');

        if ($this->request->server['HTTPS']) {
            $data['base'] = HTTPS_SERVER;
        } else {
            $data['base'] = HTTP_SERVER;
        }

        $data['direction'] = $this->language->get('direction');
        $data['lang'] = $this->language->get('code');

        $data['text_shipping'] = $this->language->get('text_shipping');
        $data['text_picklist'] = $this->language->get('text_picklist');
        $data['text_order_detail'] = $this->language->get('text_order_detail');
        $data['text_order_id'] = $this->language->get('text_order_id');
        $data['text_invoice_no'] = $this->language->get('text_invoice_no');
        $data['text_invoice_date'] = $this->language->get('text_invoice_date');
        $data['text_date_added'] = $this->language->get('text_date_added');
        $data['text_telephone'] = $this->language->get('text_telephone');
        $data['text_fax'] = $this->language->get('text_fax');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_website'] = $this->language->get('text_website');
        $data['text_contact'] = $this->language->get('text_contact');
        $data['text_from'] = $this->language->get('text_from');
        $data['text_to'] = $this->language->get('text_to');
        $data['text_shipping_method'] = $this->language->get('text_shipping_method');
        $data['text_sku'] = $this->language->get('text_sku');
        $data['text_upc'] = $this->language->get('text_upc');
        $data['text_ean'] = $this->language->get('text_ean');
        $data['text_jan'] = $this->language->get('text_jan');
        $data['text_isbn'] = $this->language->get('text_isbn');
        $data['text_mpn'] = $this->language->get('text_mpn');

        $data['column_location'] = $this->language->get('column_location');
        $data['column_reference'] = $this->language->get('column_reference');
        $data['column_product'] = $this->language->get('column_product');
        $data['column_weight'] = $this->language->get('column_weight');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_comment'] = $this->language->get('column_comment');

        $this->load->model('sale/order');

        $this->load->model('catalog/product');

        $this->load->model('setting/setting');

        $data['orders'] = array();

        $orders = array();

        if (isset($this->request->post['selected'])) {
            $orders = $this->request->post['selected'];
        } elseif (isset($this->request->get['order_id'])) {
            $orders[] = $this->request->get['order_id'];
        }

        $all_order_product_weight_inv_arr = array();
        $return_order_arr = array();
        foreach ($orders as $order_id) {
            $order_info = $this->model_sale_order->getOrder($order_id);

            if ($order_info['order_deliver_status_id'] == 1 || $order_info['order_deliver_status_id'] == 11) {
                //if ($product['weight_inv_flag'] == 1 && $order_info['order_deliver_status_id'] == 1 ) {
                header("Content-type: text/html; charset=utf-8");
                echo "订单 " . $order_info['order_id'] . "需修改配送状态为后才能打印面单";
                exit;
            }

            //获取订单的分拣序号, 若已指定order_id, 获取该订单在当天的分拣序号。
            // 注意，按次方法订单的配送时间不可更改 !!!
            //$sortingIndex = $this->model_sale_order->getOrderSortingIndexList(0,0,0,$order_id);

            // Make sure there is a shipping method
            if ($order_info && $order_info['shipping_code']) {
                $store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

                if ($store_info) {
                    $store_address = $store_info['config_address'];
                    $store_email = $store_info['config_email'];
                    $store_telephone = $store_info['config_telephone'];
                    $store_fax = $store_info['config_fax'];
                } else {
                    $store_address = $this->config->get('config_address');
                    $store_email = $this->config->get('config_email');
                    $store_telephone = $this->config->get('config_telephone');
                    $store_fax = $this->config->get('config_fax');
                }

                if ($order_info['invoice_no']) {
                    $invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
                } else {
                    $invoice_no = '';
                }

                if ($order_info['shipping_address_format']) {
                    $format = $order_info['shipping_address_format'];
                } else {
                    //$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
                    $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{city}' . ' ' . '{address_1}' . "\n" . '{address_2}';
                }

                $find = array(
                    '{firstname}',
                    '{lastname}',
                    '{company}',
                    '{address_1}',
                    '{address_2}',
                    '{city}',
                    '{postcode}',
                    '{zone}',
                    '{zone_code}',
                    '{country}'
                );

                $replace = array(
                    'firstname' => $order_info['shipping_firstname'],
                    'lastname' => $order_info['shipping_lastname'],
                    'company' => $order_info['shipping_company'],
                    'address_1' => $order_info['shipping_address_1'],
                    'address_2' => $order_info['shipping_address_2'],
                    'city' => $order_info['shipping_city'],
                    'postcode' => $order_info['shipping_postcode'],
                    'zone' => $order_info['shipping_zone'],
                    'zone_code' => $order_info['shipping_zone_code'],
                    'country' => $order_info['shipping_country']
                );

                //$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
                $shipping_address = $order_info['shipping_city'] . ' ' . $order_info['shipping_address_1'];
                if ($order_info['shipping_address_2']) {
                    $shipping_address .= '<br />' . $order_info['shipping_address_2'];
                }

                $this->load->model('tool/upload');

                $product_data = array();

                $products = $this->model_sale_order->getOrderProducts($order_id);
                
                //按重出库商品数据
                $products_weight_inv = array();
                $products_weight_inv_arr = array();
                $products_weight_inv = $this->model_sale_order->getOrderProductsWeightInv($order_id);
                foreach ($products_weight_inv as $k => $v) {
                    $products_weight_inv_arr[$v['product_id']] = $v;
                }

                $all_order_product_weight_inv_arr[$order_id] = $products_weight_inv_arr;
                
                $inv_products = array();
                $inv_products = $this->model_sale_order->getOrderInvProducts($order_id);
                foreach ($inv_products as $inv_key => $inv_value) {
                    if (isset($inv_products[$order_id . "_" . $inv_value['product_id']])) {
                        $inv_products[$order_id . "_" . $inv_value['product_id']] += abs($inv_value['quantity']);
                    } else {
                        $inv_products[$order_id . "_" . $inv_value['product_id']] = abs($inv_value['quantity']);
                    }
                    unset($inv_products[$inv_key]);
                }


                foreach ($products as $product) {


                    if ($order_info['order_deliver_status_id'] == 1 && date("Y-m-d", time() + 8 * 3600) >= $order_info['deliver_date']) {
                    //if ($product['weight_inv_flag'] == 1 && $order_info['order_deliver_status_id'] == 1 ) {
                        header("Content-type: text/html; charset=utf-8");
                        echo "订单 " . $order_info['order_id'] . "需修改配送状态后才能打印面单";
                        exit;
                    }

                    $product_info = $this->model_catalog_product->getProduct($product['product_id']);

                    $option_data = array();

                    $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

                    foreach ($options as $option) {
                        if ($option['type'] != 'file') {
                            $value = $option['value'];
                        } else {
                            $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                            if ($upload_info) {
                                $value = $upload_info['name'];
                            } else {
                                $value = '';
                            }
                        }

                        $option_data[] = array(
                            'name' => $option['name'],
                            'value' => $value
                        );
                    }

                    $product_data[] = array(
                        'product_id' => $product['product_id'],
                        'name' => $product['name'],
                        'abstract'=> $product_info['abstract'],
                        'cate_name' => $product['cate_name'],
                        'inv_class_name' => $product['inv_class_name'],
                        'model' => $product_info['model'],
                        'option' => $option_data,
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                        'total' => $product['total'],
                        'location' => $product_info['location'],
                        'sku' => $product_info['sku'],
                        'upc' => $product_info['upc'],
                        'ean' => $product_info['ean'],
                        'jan' => $product_info['jan'],
                        'isbn' => $product_info['isbn'],
                        'mpn' => $product_info['mpn'],
                        'weight' => $this->weight->format($product_info['weight'], $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')),
                        'weight_inv_flag' => $product['weight_inv_flag'],
                        'inv_product_quantity' => isset($inv_products[$order_id . "_" . $product['product_id']]) ? $inv_products[$order_id . "_" . $product['product_id']] : 0,
                        
                        'inv_size' => $product['inv_size']
                            //'inv_no_product_quantity' => $product['quantity'] - $inv_products[$order_id . "_" . $product['product_id']]
                    );
                }

                //订单金额添加显示 称重调整、调价调整、缺货
                $sql  = "select * from oc_customer_transaction where order_id = '" . $order_id . "' and customer_transaction_type_id in (6,8,9)";
                $t_query = $this->db->query($sql);
                $t_arr = $t_query->rows;
                $transaction_change_arr = array();
                $order_real_total = $order_info['total'];

                
                if(!empty($t_arr)){
                    foreach($t_arr as $t_k=>$t_v){
                        if($t_v['amount'] > 0){
                            $t_v['amount'] = "-" . $t_v['amount'];
                        }
                        else{
                            $t_v['amount'] = abs($t_v['amount']);
                        }
                        $transaction_change_arr[$t_v['customer_transaction_type_id']] = $t_v;
                        $order_real_total += $t_v['amount'];
                        
                    }
                }

                //计算司机出发前的库存不足缺货的总额
                $short_info = $this->model_sale_order->getPreDeliverShortInfo($order_id);

                $returnInfo = $this->model_sale_order->getReturnInfo($order_id);
                $return_order_arr[$order_id] = $returnInfo;
                $data['user_group_id'] = $this->user->user_group_id;
//                $data['return'] = $returnInfo;
                $data['orders'][] = array(
                    'orderid' => $order_info['orderid'],
                    'type' => $order_info['type'],
                    'order_id' => $order_id,
                    'station_id' => $order_info['station_id'],
                    'invoice_no' => $invoice_no,
                    'date_added' => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
                    'store_name' => $order_info['store_name'],
                    'store_url' => rtrim($order_info['store_url'], '/'),
                    'store_address' => nl2br($store_address),
                    'store_email' => $store_email,
                    'store_telephone' => $store_telephone,
                    'store_fax' => $store_fax,
                    'email' => $order_info['email'],
                    'telephone' => $order_info['telephone'],
                    'shipping_phone' => $order_info['shipping_phone'],
                    'shipping_address' => $shipping_address,
                    'shipping_method' => $order_info['shipping_method'],
                    'product' => $product_data,
                    'comment' => nl2br($order_info['comment']),
                    'payment_method' => $order_info['payment_method'],
                    'deliver_date' => $order_info['deliver_date'],
                    'order_payment_status_id' => $order_info['order_payment_status_id'],
                    'payment_status' => $order_info['payment_status'],
                    'sub_total' => $order_info['sub_total'],
                    'discount_total' => $order_info['discount_total'],
                    'shipping_fee' => $order_info['shipping_fee'],
                    'balance_container_deposit' => $order_info['balance_container_deposit'],
                    'credit_paid' => $order_info['credit_paid'],
                    
                    'weight_change' => isset($transaction_change_arr[6]) ? round($transaction_change_arr[6]['amount'],2) : '0.00',
                    'price_change' => isset($transaction_change_arr[8]) ? round($transaction_change_arr[8]['amount'],2) : '0.00',
                    'quantity_change' => isset($transaction_change_arr[9]) ? round($transaction_change_arr[9]['amount'],2) : '0.00',
                    
                    'order_real_total' => $order_real_total,
                    
                    'total' => $order_info['total'],
                    'order_due_total' => round($order_info['order_due_total']) . ".00",
                    'user_point_paid' => round($order_info['user_point_paid']) . ".00",
                    'wxpay_paid' => round($order_info['wxpay_paid']) . ".00",

                    'order_return_outofstock' => $order_info['order_return_outofstock'],
                    'order_return_fromcustomer' => round($order_info['order_return_fromcustomer'],2),
                    'shipping_name' => $order_info['shipping_firstname'] . $order_info['shipping_lastname'],
                    'has_inv_product' => !empty($inv_products) ? 1 : 0,
                    'order_inv_data' => $this->model_sale_order->getOrderInvData($order_id),

                    //使用分拣货位，非固定货位
                    //'orderSoringIndex' => ($sortingIndex >= FAST_MOVE_ORDER_SORTING_INDEX_START) ? $sortingIndex : false
                    'stock_out_short' => round($short_info[0]['sum_credits'],2),
                );
            }
        }

        $data['return'] = $return_order_arr;

        $data['has_inv_product'] = 0;
        $data['all_order_product_weight_inv_arr'] = $all_order_product_weight_inv_arr;
        foreach ($data['orders'] as $d_o_key => $d_o_value) {
            if ($d_o_value['has_inv_product'] == 1) {
                $data['has_inv_product'] = 1;
                break;
            }
        }

        $this->response->setOutput($this->load->view('sale/order_shipping.tpl', $data));
    }

    public function addfeadbacks(){
        $order_id =$this->request->post['order_id'];
        $feadback_options= isset($this->request->post['check_value'])?$this->request->post['check_value']:false;

        if(empty($order_id)  ){
            return false;
        }else {
            $flag = true;
            //$feadback_options = implode(',', $feadback_options);
            $comment = isset($this->request->post['comments'])?$this->request->post['comments']:false;
            $score = isset($this->request->post['score'])?$this->request->post['score']:false;
            $is_check = isset($this->request->post['is_check'])?$this->request->post['is_check']:false;
            $billof = isset($this->request->post['billof'])?$this->request->post['billof']:0;
            $box = isset($this->request->post['box'])?$this->request->post['box']:false;
            $user_comment = isset($this->request->post['user_comments'])?$this->request->post['user_comments']:false;
            $this->load->model('sale/order');
            $data = $this->model_sale_order->getOrderFeadback($order_id);

            if($feadback_options){
                foreach($feadback_options as $feadbackId){
                    $rowdata = array(
                        'order_id' => $data['order_id'],
                        'station_id' => $data['station_id'],
                        'feadback_id' => $feadbackId,
                        'logistic_driver_id' => $data['logistic_driver_id'],
                        'logistic_driver' => $data['logistic_driver_title'],
                        //'feadback_options' => $feadback_options,
                        'comments' => $comment,
                        'user_comments'=>$user_comment,
                        'logistic_score'=>$score,
                        'cargo_check'=>$is_check,
                        'bill_of'=>$billof,
                        'box'=>$box,
                    );
                    $orderFeadbacks = $this->model_sale_order->addOrderFeadback('oc_x_order_feadback', $rowdata);

                }
            }else{
                    $rowdata = array(
                        'order_id' => $data['order_id'],
                        'station_id' => $data['station_id'],

                        'logistic_driver_id' => $data['logistic_driver_id'],
                        'logistic_driver' => $data['logistic_driver_title'],
                        //'feadback_options' => $feadback_options,
                        'comments' => $comment,
                        'user_comments'=>$user_comment,
                        'logistic_score'=>$score,
                        'cargo_check'=>$is_check,
                        'bill_of'=>$billof,
                        'box'=>$box,
                    );
                    $orderFeadbacks = $this->model_sale_order->addOrderFeadback('oc_x_order_feadback', $rowdata);


            }


        }
        if($flag){
            echo true;
        }
    }

    public function api() {
        $json = array();

        // Store
        if (isset($this->request->get['store_id'])) {
            $store_id = $this->request->get['store_id'];
        } else {
            $store_id = 0;
        }

        $this->load->model('setting/store');

        $store_info = $this->model_setting_store->getStore($store_id);

        if ($store_info) {
            $url = $store_info['ssl'];
        } else {
            $url = HTTPS_CATALOG;
        }

        if (isset($this->session->data['cookie']) && isset($this->request->get['api'])) {
            // Include any URL perameters
            $url_data = array();

            foreach ($this->request->get as $key => $value) {
                if ($key != 'route' && $key != 'token' && $key != 'store_id') {
                    $url_data[$key] = $value;
                }
            }

            $curl = curl_init();

            // Set SSL if required
            if (substr($url, 0, 5) == 'https') {
                curl_setopt($curl, CURLOPT_PORT, 443);
            }

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url . 'index.php?route=' . $this->request->get['api'] . ($url_data ? '&' . http_build_query($url_data) : ''));

            if ($this->request->post) {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->request->post));
            }

            curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');

            $json = curl_exec($curl);

            curl_close($curl);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($json);
    }

    function userFeadback(){

    }

    public function feadbackAll(){
        $this->response->setOutput($this->load->view('sale/order_feadbackAll.tpl'));
        $this->load->model('sale/order');
        $results = $this->model_sale_order->feadbackAll();
    }
}