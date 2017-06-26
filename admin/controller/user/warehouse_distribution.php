<?php

class ControllerUserWarehouseDistribution extends Controller {
    public function index(){

        $this->load->model('user/warehouse_distribution');

        $this->document->setTitle('订单分配');
        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');

       
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '订单分配',
            'href' => $this->url->link('user/warehouse_distribution', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['filter_date_start'] = $filter_date_start;
        $data['button_filter'] = $this->language->get('button_filter');

        $data['order_stations'] = $this->model_user_warehouse_distribution->distrStationList();
        $data['order_status'] = $this->model_user_warehouse_distribution->orderStatus();
        $data['area_list'] = $this->model_user_warehouse_distribution->orderArea();
        $data['customer_groups'] = $this->model_user_warehouse_distribution->getCustomerGroupList();
        $data['w_user_list'] = $this->model_user_warehouse_distribution->distrPersonList(1);
        $data['product_type_list'] = $this->model_user_warehouse_distribution->getProductTypeList();
        $data['area'] = $this->model_user_warehouse_distribution->getArea();
        $data['token'] = $this->session->data['token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $this->response->setOutput($this->load->view('user/warehouse_distribution.tpl',$data));
    }

    public function getDistrOrders(){
        $this->load->model('user/warehouse_distribution');
        $station_id = $this->request->post['filter_station'];
        $deliver_date = $this->request->post['filter_date_start'];
        $customer_group_id = $this->request->post['filter_customer_group_id'];
        $order_status_id = $this->request->post['filter_order_status'];
        $order_area = $this->request->post['filter_bd_area_list'];
        $product_type = $this->request_post['filter_product_type_list'];
        $area = $this->request_post['filter_area_list'];
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $result = $this->model_user_warehouse_distribution->getDistrOrders($station_id,$deliver_date,$customer_group_id,$order_status_id,$order_area,$product_type,$area);
        }

        echo json_encode($result);
    }

    public function getUnConfirmDistr(){
        $data['token'] = $this->session->data['token'];

        $this->load->model('user/warehouse_distribution');

        $data['text_no_results'] = $this->language->get('text_no_results');


        $data['histories'] = array();

        $results = $this->model_user_warehouse_distribution->getIfConfirmDistr($this->request->get['condition']);

        foreach ($results as $result) {
            $data['histories'][] = array(
                'order_id'   => $result['order_id'],
                'quantity'      =>$result['quantity'],
                'fj_quantity'   => $result['fj_quantity'],
                'product_name'  => $result['product_name'],
                'product_type_id' =>$result['product_type_id'],
                'date_added'  => $result['date_added'],
                'customer_level'  => $result['customer_level'],
                'shipping_address'  => $result['shipping_address'],
                'inventory_name' =>$result['inventory_name'],
                'flag_key' => $result['flag_key'],
            );
        }

//        $history_total = $this->model_user_warehouse_distribution->getIfConfirmDistrTotals($this->request->get['condition']);

//        $pagination = new Pagination();
//        $pagination->total = $history_total;
//        $pagination->page = $page;
//        $pagination->limit = 10;
//        $pagination->url = $this->url->link('user/warehouse_distribution/getUnConfirmDistr', 'token=' . $this->session->data['token'] . '&condition=' . $this->request->get['condition'] . '&page={page}', 'SSL');
//
//        $data['pagination'] = $pagination->render();
//
//        $data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

        $this->response->setOutput($this->load->view('user/order_all_distr.tpl', $data));
    }

    public function getOrdersToDistr(){
        $data['token'] = $this->session->data['token'];

        $this->load->model('user/warehouse_distribution');

        $data['text_no_results'] = $this->language->get('text_no_results');


        $data['histories'] = array();

        $results = $this->model_user_warehouse_distribution->getDistrOrders($this->request->get['condition']);

        foreach ($results as $result) {
            $data['histories'][] = array(
                'order_id'   => $result['order_id'],
                'quantity'      =>$result['quantity'],
                'fj_quantity'   => $result['fj_quantity'],
                'product_name'  => $result['product_name'],
                'product_type_id' =>$result['product_type_id'],
                'date_added'  => $result['date_added'],
                'customer_level'  => $result['customer_level'],
                'shipping_address'  => $result['shipping_address'],

                'flag_key' => $result['flag_key'],
            );
        }

//        $history_total = $this->model_user_warehouse_distribution->
//getDistrOrdersTotals($this->request->get['condition']);
//
//        $pagination = new Pagination();
//        $pagination->total = $history_total;
//        $pagination->page = $page;
//        $pagination->limit = 10;
//        $pagination->url = $this->url->link('user/warehouse_distribution/getOrdersToDistr', 'token=' . $this->session->data['token'] . '&condition=' . $this->request->get['condition'] . '&page={page}', 'SSL');
//
//        $data['pagination'] = $pagination->render();
//
//        $data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

        $this->response->setOutput($this->load->view('user/order_distr.tpl', $data));

    }

    public function distrOrderToWoker(){
        $this->load->model('user/warehouse_distribution');
        $order_id = $this->request->post['order_id'];
        $w_user_id = $this->request->post['w_user_id'];
        $inventory_name = $this->request->post['inventory_name'];
        $quantity = $this->request->post['quantity'];
        $product_type_id = $this->request->post['product_type_id'];
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $result = $this->model_user_warehouse_distribution->distrOrderToWoker($order_id,$w_user_id,$inventory_name,$quantity,$product_type_id);
        }
        echo json_encode($result);
    }

    public function redistrOrderToWoker(){
        $this->load->model('user/warehouse_distribution');
        $order_id = $this->request->post['order_id'];
        $sorting_area = $this->request->post['product_type_id'];
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $result = $this->model_user_warehouse_distribution->redistrOrderToWoker($order_id,$sorting_area);
        }

        echo json_encode($result);
    }
}