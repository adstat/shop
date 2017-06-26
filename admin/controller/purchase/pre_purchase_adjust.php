<?php

class ControllerPurchasePrePurchaseAdjust extends Controller {

    public function index()
    {
        $this->load->language('sale/order');

        $this->document->setTitle("采购单调整单");

        $this->load->model('purchase/pre_purchase');
        $this->load->model('purchase/pre_purchase_adjust');

        $this->load->model('station/station');

        $this->getList();
    }

    protected function getList() {

        if (isset($this->request->get['filter_supplier_type'])) {
            $filter_supplier_type = $this->request->get['filter_supplier_type'];
        } else {
            $filter_supplier_type = 0;
        }

        if (isset($this->request->get['filter_purchase_order_id'])) {
            $filter_purchase_order_id = $this->request->get['filter_purchase_order_id'];
        } else {
            $filter_purchase_order_id = '';
        }

        if (isset($this->request->get['filter_purchase_person_id'])) {
            $filter_purchase_person_id = $this->request->get['filter_purchase_person_id'];
        } else {
            $filter_purchase_person_id = '';
        }

        if (isset($this->request->get['filter_date_deliver'])) {
            $filter_date_deliver = $this->request->get['filter_date_deliver'];
        } else {
            $filter_date_deliver = null;
        }

        if (isset($this->request->get['filter_date_deliver_end'])) {
            $filter_date_deliver_end = $this->request->get['filter_date_deliver_end'];
        } else {
            $filter_date_deliver_end = null;
        }

        if (isset($this->request->get['filter_order_status_id'])) {
            $filter_order_status_id = $this->request->get['filter_order_status_id'];
        } else {
            $filter_order_status_id = null;
        }

        if (isset($this->request->get['filter_order_checkout_status_id'])) {
            $filter_order_checkout_status_id = $this->request->get['filter_order_checkout_status_id'];
        } else {
            $filter_order_checkout_status_id = null;
        }

        if (isset($this->request->get['filter_warehouse_id_global'])) {
            $filter_warehouse_id = $this->request->get['filter_warehouse_id_global'];
        } else {
            $filter_warehouse_id = 0;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.purchase_order_id';
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

        $url = '';
        if (isset($this->request->get['filter_supplier_type'])) {
            $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
        }
        if (isset($this->request->get['filter_purchase_order_id'])) {
            $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
        }
        if (isset($this->request->get['filter_purchase_person_id'])) {
            $url .= '&filter_purchase_person_id=' . $this->request->get['filter_purchase_person_id'];
        }
        if (isset($this->request->get['filter_date_deliver'])) {
            $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
        }
        if (isset($this->request->get['filter_date_deliver_end'])) {
            $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_order_checkout_status_id'])) {
            $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
        }
        if (isset($this->request->get['filter_warehouse_id_global'])) {
            $url .= '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'];
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
            'text' => "采购调整单",
            'href' => $this->url->link('purchase/pre_purchase_adjust', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['orders'] = array();
        $filter_data = array(
            'filter_supplier_type' => $filter_supplier_type,
            'filter_purchase_order_id' => $filter_purchase_order_id,
            'filter_purchase_person_id' => $filter_purchase_person_id,
            'filter_date_deliver' => $filter_date_deliver,
            'filter_date_deliver_end' => $filter_date_deliver_end,
            'filter_order_status_id' => $filter_order_status_id,
            'filter_order_checkout_status_id' => $filter_order_checkout_status_id,
            'filter_warehouse_id' => $filter_warehouse_id,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $order_total = $this->model_purchase_pre_purchase_adjust->getTotalAdjustOrders($filter_data);
        $results = $this->model_purchase_pre_purchase_adjust->getAdjustOrders($filter_data);

        $purchase_order_ids = array();
        foreach ($results as $result) {
            $get_total = 0;
            $order_info = $this->model_purchase_pre_purchase->getOrder($result['purchase_order_id']);
            foreach($order_info['products'] as $k1 => $v1){
                $order_info['products'][$v1['product_id']] = $v1;
                unset($order_info['products'][$k1]);
            }

            $order_get_product = $this->model_purchase_pre_purchase->getOrderGetProductInfo($result['purchase_order_id']);

            if(!empty($order_get_product)){
                foreach($order_get_product as $k2 => $v2){
                    if(!isset($order_info['products'][$k2])){
                        echo $result['purchase_order_id'] . "<br><br>";
                    }
                    $get_total += round($v2 * ($order_info['products'][$k2]['price'] * $order_info['products'][$k2]['supplier_quantity'] / $order_info['products'][$k2]['quantity'] ), 2);
                }
            }

            //获取订单退货信息
            $order_return_info = $this->model_purchase_pre_purchase->getOrderReturn($result['purchase_order_id']);

            $data['orders'][] = array(
                'purchase_order_id' => $result['purchase_order_id'],
                'station_id' => $result['station_id'],
                'date_added' => $result['date_added'],
                'date_deliver_plan' => $result['date_deliver'],
                'date_deliver' => $result['real_date_deliver'],
                'add_user_name' => $result['add_user_name'],
                'added_by' => $result['added_by'],
                'confirmed' => $result['confirmed'],
                'confirmed_by' => $result['confirmed_by'],
                'confirm_user_name' => $result['confirm_user_name'],
                'supplier_type' => $result['supplier_type'],
                'product_category' => $result['product_category'],
                'status' => $result['status'],
                'supplier_type_name' => $result['name'],
                'status_name' => $result['status_name'],
                'order_total' => $result['order_total'],
                'use_credits_total' => $result['use_credits_total'],
                'quehuo_credits' => $result['quehuo_credits'],
                'order_return' => $order_return_info['sum_return'],
                'get_total' => $get_total,
                'checkout_status' => $result['checkout_status'],
                'checkout_type_id' => $result['checkout_type_id'],
                'order_type' => $result['order_type'],
                'invoice_flag' => $result['invoice_flag'],

                'view' => $this->url->link('purchase/pre_purchase_adjust/info', 'token=' . $this->session->data['token'] . '&purchase_order_id=' . $result['purchase_order_id'] . $url, 'SSL'),
            );

            $purchase_order_ids[] = $result['purchase_order_id'];
        }

        // 查询具体调整订单状态
        if(!empty($purchase_order_ids)){
            $where          = array('related_order_ids' => $purchase_order_ids, 'order_type' => 3);
            $fields         = "purchase_order_id, related_order, status, order_total";
            $adjust_data    = $this->model_purchase_pre_purchase_adjust->getAdjustOrderInfo($where, $fields);
            $adjust_status  = array();
            $adjust_total   = array();
            if(!empty($adjust_data)){
                foreach($adjust_data as $val){
                    $adjust_status[$val['related_order']][] = $val['status'];
                    if($val['status'] == 2){
                        !isset($adjust_total[$val['related_order']]) && $adjust_total[$val['related_order']] = 0;
                        $adjust_total[$val['related_order']] += $val['order_total'];
                    }
                }
            }

            foreach($data['orders'] as &$order) {
                $order['adjust_status'] = '';
                if (!empty($adjust_status[$order['purchase_order_id']])) {
                    if(in_array(3, $adjust_status[$order['purchase_order_id']])){
                        $order['adjust_type']   = "已审核";
                        $order['adjust_status'] = 2;
                    }
                    if(in_array(2, $adjust_status[$order['purchase_order_id']])){
                        $order['adjust_type']   = "已审核";
                        $order['adjust_status'] = 2;
                        // 金额矫正
                        !empty($adjust_total[$order['purchase_order_id']]) && $order['order_total'] += $adjust_total[$order['purchase_order_id']];
                    }
                    if(in_array(1, $adjust_status[$order['purchase_order_id']])){
                        $order['adjust_type']   = "待审核";
                        $order['adjust_status'] = 1;
                    }
                }
            }
        }

        $data['heading_title'] = "采购调整单";

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

        if (isset($this->session->data['error_warning'])) {
            $data['error_warning'] = $this->session->data['error_warning'];

            unset($this->session->data['error_warning']);
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';
        if (isset($this->request->get['filter_supplier_type'])) {
            $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
        }
        if (isset($this->request->get['filter_purchase_order_id'])) {
            $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
        }
        if (isset($this->request->get['filter_purchase_person_id'])) {
            $url .= '&filter_purchase_person_id=' . $this->request->get['filter_purchase_person_id'];
        }
        if (isset($this->request->get['filter_date_deliver'])) {
            $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
        }
        if (isset($this->request->get['filter_date_deliver_end'])) {
            $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_order_checkout_status_id'])) {
            $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
        }
        if (isset($this->request->get['filter_warehouse_id_global'])) {
            $url .= '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'];
        }
        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order'] = $this->url->link('purchase/pre_purchase_adjust', 'token=' . $this->session->data['token'] . '&sort=o.purchase_order_id' . $url, 'SSL');

        $data['sort_date_deliver'] = $this->url->link('purchase/pre_purchase_adjust', 'token=' . $this->session->data['token'] . '&sort=o.date_deliver' . $url, 'SSL');

        $url = '';
        if (isset($this->request->get['filter_supplier_type'])) {
            $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
        }
        if (isset($this->request->get['filter_purchase_order_id'])) {
            $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
        }
        if (isset($this->request->get['filter_purchase_person_id'])) {
            $url .= '&filter_purchase_person_id=' . $this->request->get['filter_purchase_person_id'];
        }
        if (isset($this->request->get['filter_date_deliver'])) {
            $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
        }
        if (isset($this->request->get['filter_date_deliver_end'])) {
            $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_order_checkout_status_id'])) {
            $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
        }
        if (isset($this->request->get['filter_warehouse_id_global'])) {
            $url .= '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('purchase/pre_purchase_adjust', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_supplier_type'] = $filter_supplier_type;
        $data['filter_purchase_order_id'] = $filter_purchase_order_id;
        $data['filter_purchase_person_id'] = $filter_purchase_person_id;
        $data['filter_date_deliver'] = $filter_date_deliver;
        $data['filter_date_deliver_end'] = $filter_date_deliver_end;
        $data['filter_order_status_id'] = $filter_order_status_id;
        $data['filter_order_checkout_status_id'] = $filter_order_checkout_status_id;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();

        $data['supplier_types'] = $this->model_purchase_pre_purchase->getSupplierTypes();
        $data['purchase_person'] = $this->model_station_station->getPurchasePerson();

        //load station
        $data['order_stations']   = array(1=>"生鲜", 2=>"快销");
        $data['product_statuses'] = array(0=>"停用", 1=>"启用");

        $data['order_statuses'] = $this->model_purchase_pre_purchase->getStatuses();
        $data['order_checkout_statuses'] = array(array("order_checkout_status_id"=>1, "name"=>"未支付"), array("order_checkout_status_id"=>2, "name"=>"已支付"));

        $data['user_group_id'] = $this->user->user_group_id;

        $data['modifyPermission'] = false;
        if ($this->user->hasPermission('modify', 'purchase/pre_purchase_adjust')) {
            $data['modifyPermission'] = true;
        }

        $this->response->setOutput($this->load->view('purchase/pre_purchase_adjust.tpl', $data));
    }

    // 采购调整单 详情
    public function info() {
        $this->load->model('purchase/pre_purchase');
        $this->load->model('purchase/pre_purchase_adjust');

        if (isset($this->request->get['purchase_order_id'])) {
            $order_id = $this->request->get['purchase_order_id'];
        } else {
            $order_id = 0;
        }

        $order_info             = $this->model_purchase_pre_purchase->getOrder($order_id);
        $order_get_product_info = $this->model_purchase_pre_purchase->getOrderGetProductInfo($order_id);

        if ($order_info) {
            $this->load->language('sale/order');
            $this->document->setTitle("采购单调整单");
            $data['heading_title'] = "采购单调整单";
            $data['token'] = $this->session->data['token'];

            $url = '';
            if (isset($this->request->get['filter_supplier_type'])) {
                $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
            }

            if (isset($this->request->get['filter_order_type'])) {
                $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
            }
            if (isset($this->request->get['filter_purchase_order_id'])) {
                $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
            }

            if (isset($this->request->get['filter_date_deliver'])) {
                $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
            }
            if (isset($this->request->get['filter_date_deliver_end'])) {
                $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
            }
            if (isset($this->request->get['filter_order_status_id'])) {
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            }
            if (isset($this->request->get['filter_order_checkout_status_id'])) {
                $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
            }
            if (isset($this->request->get['filter_warehouse_id_global'])) {
                $url .= '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'];
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
                'text' => "采购单调整单",
                'href' => $this->url->link('purchase/pre_purchase_adjust', 'token=' . $this->session->data['token'] . $url, 'SSL')
            );

            // 查询调整单
            $order_info['adjust_products']       = array();
            $order_info['adjust_product_status'] = array();
            $adjust_order = $this->model_purchase_pre_purchase_adjust->getAdjustOrderProductsByPurchaseOrder($order_id);
            if(!empty($adjust_order)){
                foreach($order_info['products'] as $value){
                    $product_name[$value['product_id']] = $value['name'];
                }

                foreach($adjust_order as &$val){
                    $val['name'] = !empty($product_name[$val['product_id']]) ? $product_name[$val['product_id']] : '';
                    if($val['status'] == 1){
                        $val['status_name'] = "已生效";
                    }else{
                        $val['status_name'] = "未生效";
                        if($val['order_status'] == 3){
                            $val['status_name'] = "审核未通过";
                        }
                    }
                    $order_info['adjust_product_status'][$val['product_id']] = $val['status'];
                }
                $order_info['adjust_products'] = $adjust_order;
            }


            $data['order_id'] = $this->request->get['purchase_order_id'];

            $data = array_merge($data, $order_info);
            $data['order_get_product_info'] = $order_get_product_info;
            $data['order_info'] = $order_info;

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $data['modifyPermission'] = false;
            if ($this->user->hasPermission('modify', 'purchase/pre_purchase') && $this->user->getId() == $order_info['added_by']) {
                $data['modifyPermission'] = true;
            }

            $this->response->setOutput($this->load->view('purchase/pre_purchase_adjust_info.tpl', $data));

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

    // 生成采购调整单
    public function createAdjustOrder()
    {
        $this->load->model('purchase/pre_purchase_adjust');
        $this->load->model('purchase/pre_purchase');

        $returnData = array('return_code'=>'FAIL', 'return_message'=>'生成采购调整单失败, 请查看订单状态与金额', 'return_data'=>'');

        //检测采购单支付状态
        $paymentStatus = $this->model_purchase_pre_purchase->checkPrePurchaseOrderStatus($this->request->post['order_id']);
        if($paymentStatus['checkout_status'] === '2'){
            $purchase_order_product_id  = !empty($this->request->post['id'])        ? $this->request->post['id']        : 0;
            $purchase_order_id          = !empty($this->request->post['order_id'])  ? $this->request->post['order_id']  : 0;
            $price                      = !empty($this->request->post['price'])     ? $this->request->post['price']     : 0;

            if($this->request->server['REQUEST_METHOD'] == 'POST' && $purchase_order_product_id && $purchase_order_id && $price){
                $data = array(
                    'purchase_order_id'         => $purchase_order_id,
                    'purchase_order_product_id' => $purchase_order_product_id,
                    'price'                     => $price
                );

                $result = $this->model_purchase_pre_purchase_adjust->createAdjustOrder($data);
                if($result){
                    $returnData = array('return_code'=>'SUCCESS', 'return_message'=>'生成采购调整单成功', 'return_data'=>$result);
                }
            }
        }

        echo json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }

    // 采购调整单 审核通过
    public function passAdjustOrder()
    {
        $this->load->model('purchase/pre_purchase_adjust');

        $returnData = array('return_code'=>'FAIL', 'return_message'=>'未知错误，请联系管理员。', 'return_data'=>'');

        $purchase_order_product_id  = !empty($this->request->post['id'])        ? $this->request->post['id']        : 0;
        $purchase_order_id          = !empty($this->request->post['order_id'])  ? $this->request->post['order_id']  : 0;

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $purchase_order_product_id && $purchase_order_id){
            $data = array(
                'purchase_order_id'         => $purchase_order_id,
                'purchase_order_product_id' => $purchase_order_product_id,
            );

            $result = $this->model_purchase_pre_purchase_adjust->passAdjustOrder($data);
            if($result){
                $returnData = array('return_code'=>'SUCCESS', 'return_message'=>'采购调整单已通过审核', 'return_data'=>$result);
            }
        }

        echo json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }

    // 采购调整单 审核不通过
    public function cancelAdjustOrder()
    {
        $this->load->model('purchase/pre_purchase_adjust');

        $returnData = array('return_code'=>'FAIL', 'return_message'=>'未知错误，请联系管理员。', 'return_data'=>'');

        $purchase_order_id          = !empty($this->request->post['order_id'])  ? $this->request->post['order_id']  : 0;
        $purchase_order_product_id  = !empty($this->request->post['id'])        ? $this->request->post['id']        : 0;
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $purchase_order_id && $purchase_order_product_id){

            $result = $this->model_purchase_pre_purchase_adjust->cancelAdjustOrder($purchase_order_id, $purchase_order_product_id);
            if($result){
                $returnData = array('return_code' => 'SUCCESS', 'return_message' => '采购调整单已取消', 'return_data' => $result);
            }
        }

        echo json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }

}