<?php
class ControllerMarketingIndexPromotion extends Controller{
    private $error = array();

    public function index() {
        $this->load->language('marketing/index_promotion');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/index_promotion');

        $this->load->model('station/station');

        $this->getList();
    }

    public function edit() {
        $this->load->language('marketing/index_promotion');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/index_promotion');

        $this->load->model('station/station');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $filter_warehouse_id_global = $this->warehouse->getWarehouseIdGlobal();

            $this->model_marketing_index_promotion->editProducts($this->request->post,$filter_warehouse_id_global);

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $this->getList();
    }

//    protected function getList1() {
//
//        if (isset($this->request->get['sort'])) {
//            $sort = $this->request->get['sort'];
//        } else {
//            $sort = 'product_id';
//        }
//
//        if (isset($this->request->get['order'])) {
//            $order = $this->request->get['order'];
//        } else {
//            $order = 'DESC';
//        }
//
//        if (isset($this->request->get['page'])) {
//            $page = $this->request->get['page'];
//        } else {
//            $page = 1;
//        }
//
//        $url = '';
//
//        if (isset($this->request->get['page'])) {
//            $url .= '&page=' . $this->request->get['page'];
//        }
//
//        $data['breadcrumbs'] = array();
//
//        $data['breadcrumbs'][] = array(
//            'text' => $this->language->get('text_home'),
//            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
//        );
//
//        $data['breadcrumbs'][] = array(
//            'text' => $this->language->get('heading_title'),
//            'href' => $this->url->link('marketing/index_promotion', 'token=' . $this->session->data['token'] . $url, 'SSL')
//        );
//
//        $data['add'] = $this->url->link('marketing/coupon/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
//
//        $data['products'] = array();
//
//        $filter_data = array(
//            'sort'  => $sort,
//            'order' => $order,
//            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
//            'limit' => $this->config->get('config_limit_admin')
//        );
//
//        $products_total = $this->model_marketing_index_promotion->getTotalProducts($filter_data);
//
//        $results = $this->model_marketing_index_promotion->getProducts($filter_data);
//
//        foreach($results as $result){
//            $data['products'][] = array(
//                'product_id' => $result['product_id'],
//                'name' => $result['name'],
//                'priority' => $result['priority'],
//                'price' => $result['price'],
//                'maximum' => $result['maximum'],
//                'showup' => $result['showup'],
//                'promo_title' => $result['promo_title'],
//                'edit'       => $this->url->link('marketing/index_promotion/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
//
//            );
//        }
//
//        $data['heading_title'] = $this->language->get('heading_title');
//        $data['text_list'] = $this->language->get('text_list');
//        $data['text_no_results'] = $this->language->get('text_no_results');
//
//        $data['button_add'] = $this->language->get('button_add');
//        $data['button_edit'] = $this->language->get('button_edit');
//        $data['button_delete'] = $this->language->get('button_delete');
//
//        if (isset($this->session->data['success'])) {
//            $data['success'] = $this->session->data['success'];
//
//            unset($this->session->data['success']);
//        } else {
//            $data['success'] = '';
//        }
//
//        if (isset($this->request->post['selected'])) {
//            $data['selected'] = (array)$this->request->post['selected'];
//        } else {
//            $data['selected'] = array();
//        }
//
//        $url = '';
//
//        if ($order == 'ASC') {
//            $url .= '&order=DESC';
//        } else {
//            $url .= '&order=ASC';
//        }
//
//        if (isset($this->request->get['page'])) {
//            $url .= '&page=' . $this->request->get['page'];
//        }
//
//        $data['sort_product_id'] = $this->url->link('marketing/index_promotion', 'token=' . $this->session->data['token'] . '&sort=product_id' . $url, 'SSL');
//
//        $url = '';
//
//        if (isset($this->request->get['sort'])) {
//            $url .= '&sort=' . $this->request->get['sort'];
//        }
//
//        if (isset($this->request->get['order'])) {
//            $url .= '&order=' . $this->request->get['order'];
//        }
//
//        $pagination = new Pagination();
//        $pagination->total = $products_total;
//        $pagination->page = $page;
//        $pagination->limit = $this->config->get('config_limit_admin');
//        $pagination->url = $this->url->link('marketing/index_promotion', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
//
//        $data['pagination'] = $pagination->render();
//
//        $data['results'] = sprintf($this->language->get('text_pagination'), ($products_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($products_total - $this->config->get('config_limit_admin'))) ? $products_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $products_total, ceil($products_total / $this->config->get('config_limit_admin')));
//
//        $data['sort'] = $sort;
//
//        $data['order'] = $order;
//
//        $data['token'] = $this->session->data['token'];
//
//        $data['header'] = $this->load->controller('common/header');
//        $data['column_left'] = $this->load->controller('common/column_left');
//        $data['footer'] = $this->load->controller('common/footer');
//
//        $this->response->setOutput($this->load->view('marketing/index_promotion_list.tpl', $data));
//    }

    protected function getList() {
        $data['header'] = $this->load->controller('common/header');
        //暂时用session处理全局的warehouse_id_global
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();

        if($data['filter_warehouse_id_global']){
            $filter_warehouse_id_global = $data['filter_warehouse_id_global'];
        }else{
            $filter_warehouse_id_global = 0;
        }

        $this->load->language('marketing/index_promotion');

        $data['valid_date'] = date('Y-m-d');

        $data['heading_title'] = $this->language->get('heading_title');

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/index_promotion', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['token'] = $this->session->data['token'];

        $data['button_save'] = $this->language->get('button_save');

        $data['action'] = $this->url->link('marketing/index_promotion/edit', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['customer_group'] = $this->model_station_station->getCustomerGroupList();
        $data['stations'] = $this->model_station_station->getStationList();
        $data['areas'] = $this->model_station_station->getAreaName($filter_warehouse_id_global);


//        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/index_promotion_form.tpl', $data));
    }

    public function deleteProduct(){
        $product_special_id = $_GET['product_special_id'];

        $this->load->model('marketing/index_promotion');

        $json = $this->model_marketing_index_promotion->deleteProduct($product_special_id);

        echo json_encode($json);
    }

    public function getProductsByCondition(){
        $this->load->model('marketing/index_promotion');
        $date_end = $this->request->post['end'];
        $product_id = $this->request->post['product_id'];
        $station_id = $this->request->post['station_id'];

        $json = $this->model_marketing_index_promotion->getProducts($date_end,$product_id,$station_id);

        echo json_encode($json);
    }

    public function getProductInfo(){
        $product_special_id = $this->request->get['product_id'];
        $warehouse_id = $this->request->get['warehouse_id'];

        $this->load->model('marketing/index_promotion');

        $json = $this->model_marketing_index_promotion->getProductInfo($product_special_id,$warehouse_id);

        echo json_encode($json);
    }

    public function history(){
        $this->load->model('marketing/index_promotion');

        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['histories'] = array();

        $results = $this->model_marketing_index_promotion->getHistories($this->request->post);

        foreach ($results['result'] as $result) {
            $data['histories'][] = array(
                'product_id' => $result['product_id'],
                'warehouse_id' => $result['warehouse_id'],
                'warehouse_name' => $result['warehouse_name'],
                'area_id' => $result['area_id'],
                'area_name' => $result['area_name'],
                'product_name' => $result['product_name'],
                'price' => $result['price'],
                'ori_price' => $result['ori_price'],
                'promo_title' => $result['promo_title'],
                'date_start' => $result['date_start'],
                'date_end' => $result['date_end'],
                'maximum' => $result['maximum'],
            );
        }

        $this->response->setOutput($this->load->view('marketing/index_promotion_list.tpl', $data));
    }

    public function getSort(){
        $this->load->model('marketing/index_promotion');

        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['histories'] = array();

        $results = $this->model_marketing_index_promotion->getSort($this->request->post);

        foreach ($results as $result) {
            $data['histories'][] = array(
                'product_id' => $result['product_id'],
                'warehouse_id' => $result['warehouse_id'],
                'warehouse_name' => $result['warehouse_name'],
                'area_id' => $result['area_id'],
                'area_name' => $result['area_name'],
                'product_name' => $result['product_name'],
//                'price' => $result['price'],
                'priority' => $result['priority'],
            );
        }

        $this->response->setOutput($this->load->view('marketing/index_promotion_sort.tpl', $data));
    }

    public function update(){
        $this->load->model('marketing/index_promotion');
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post){
            $json = $this->model_marketing_index_promotion->updatePromotion($this->request->post);
        }else{
            $json = false;
        }
        echo json_encode($json);
    }

    public function resetSort(){
        $this->load->model('marketing/index_promotion');
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post){
            $json = $this->model_marketing_index_promotion->resetSort($this->request->post);
        }else{
            $json = false;
        }
        echo json_encode($json);
    }
}