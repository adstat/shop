<?php
class ControllerCatalogProductSkubarcode extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('catalog/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/product_inv');

        $this->getList();
    }

    public function edit(){
        $this->load->language('catalog/product');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/product_inv');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_catalog_product_inv->editProductBarcode($this->request->get['product_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_model'])) {
                $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
            }

//            if (isset($this->request->get['filter_price'])) {
//                $url .= '&filter_price=' . $this->request->get['filter_price'];
//            }

            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
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

            $this->response->redirect($this->url->link('catalog/product_sku_list', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
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

        $this->load->language('catalog/product');

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_model'])) {
            $filter_model = $this->request->get['filter_model'];
        } else {
            $filter_model = null;
        }

        if (isset($this->request->get['filter_price'])) {
            $filter_price = $this->request->get['filter_price'];
        } else {
            $filter_price = null;
        }

        if (isset($this->request->get['filter_quantity'])) {
            $filter_quantity = $this->request->get['filter_quantity'];
        } else {
            $filter_quantity = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'pd.name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
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
            'href' => $this->url->link('catalog/product_inv', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('catalog/product_inv/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['copy'] = $this->url->link('catalog/product_inv/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('catalog/product_inv/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['products'] = array();

        $filter_data = array(
            'filter_name'	  => $filter_name,
            'filter_model'	  => $filter_model,
            'filter_price'	  => $filter_price,
            'filter_quantity' => $filter_quantity,
            'filter_status'   => $filter_status,
            'filter_warehouse_id_global' => $filter_warehouse_id_global,
            'sort'            => $sort,
            'order'           => $order,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        $this->load->model('tool/image');

        $product_total = $this->model_catalog_product_inv->getTotalProducts($filter_data);
        $results = $this->model_catalog_product_inv->getProducts($filter_data);

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.png', 40, 40);
            }

            $special = false;

            $product_specials = $this->model_catalog_product_inv->getProductSpecials($result['product_id']);

            foreach ($product_specials  as $product_special) {
                if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
                    $special = $product_special['price'];

                    break;
                }
            }

            $data['products'][] = array(
                'product_id' => $result['product_id'],
                'image'      => $image,
                'name'       => $result['name'],
                'abstract'       => $result['abstract'],
                'model'      => $result['model'],
                'price'      => $result['price'],
                'retail_price'      => $result['retail_price'],
                'special'    => $special,
                'quantity'   => $result['quantity'],
                'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'       => $this->url->link('catalog/product_skubarcode/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, 'SSL')
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_image'] = $this->language->get('column_image');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_model'] = $this->language->get('entry_model');
        $data['entry_price'] = $this->language->get('entry_price');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_status'] = $this->language->get('entry_status');

        $data['button_copy'] = $this->language->get('button_copy');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');

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
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('catalog/product_inv', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, 'SSL');
        $data['sort_model'] = $this->url->link('catalog/product_inv', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, 'SSL');
        $data['sort_price'] = $this->url->link('catalog/product_inv', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, 'SSL');
        $data['sort_quantity'] = $this->url->link('catalog/product_inv', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('catalog/product_inv', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, 'SSL');
        $data['sort_order'] = $this->url->link('catalog/product_inv', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('catalog/product_inv', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;
        $data['filter_model'] = $filter_model;
        $data['filter_price'] = $filter_price;
        $data['filter_quantity'] = $filter_quantity;
        $data['filter_status'] = $filter_status;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/product_sku_list.tpl', $data));
    }

    public function getForm(){
        $data['header'] = $this->load->controller('common/header');
        //暂时用session处理全局的warehouse_id_global
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();

        if($data['filter_warehouse_id_global']){
            $filter_warehouse_id_global = $data['filter_warehouse_id_global'];
        }else{
            $filter_warehouse_id_global = 0;
        }

        $this->load->language('catalog/product');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['length'])) {
            $data['error_length'] = $this->error['length'];
        } else {
            $data['error_length'] = '';
        }

        if (isset($this->error['string'])) {
            $data['error_string'] = $this->error['string'];
        } else {
            $data['error_string'] = '';
        }

        $url = '';
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_price'])) {
            $url .= '&filter_price=' . $this->request->get['filter_price'];
        }
        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }
        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
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
            'href' => $this->url->link('catalog/product_skubarcode', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        if (!isset($this->request->get['product_id'])) {
            $data['action'] = $this->url->link('catalog/product_skubarcode/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('catalog/product_skubarcode/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, 'SSL');
        }
        $data['cancel'] = $this->url->link('catalog/product_inv', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        // 商品信息
        $data['product_id'] = $product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : '0';
        $product_info = $product_id ? $this->model_catalog_product_inv->getProduct($product_id) : array();

        // 商品描述 多语言
        $data['product_description'] = $product_id ? $this->model_catalog_product_inv->getProductDescriptions($product_id) : array();

        //寻找该商品的平台，并且找出该平台的所有仓库
        $this->load->model('station/station');

        $station_id = $this->model_station_station->getProductStation($product_id);

        $data['warehouses'] = $this->model_station_station->getWarehouseBelongToStation($station_id,$filter_warehouse_id_global);



        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('catalog/product_sku_form_basic.tpl', $data));

    }

    protected function validateForm(){
        if (!$this->user->hasPermission('modify', 'catalog/product_inv')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!utf8_strlen($this->request->post['sku_barcode'])) {
            $this->error['length'] = '商品编码不能为空';
        }else{
            if(!is_numeric($this->request->post['sku_barcode'])){
                $this->error['string'] = '编码必须为纯数字';
            }
        }

        return !$this->error;

    }

    public function history(){
        $this->load->language('catalog/product');
        $this->load->model('catalog/product_inv');
        $product_id = $this->request->get['product_id']?$this->request->get['product_id']:0;
        $warehouse= $this->request->post['warehouses']?$this->request->post['warehouses']:'';

        $data['warehouses'] = json_encode($warehouse);
        $data['token'] = $this->session->data['token'];
        $data['product_id'] = $product_id;
        $data['histories'] = array();
        if($product_id){
            $results = $this->model_catalog_product_inv->getBarcodeHistory($product_id);
            foreach($results as $result){
                $data['histories'][] = array(
                    'sku_barcode_id' => $result['sku_barcode_id'],
                    'product_id' => $result['product_id'],
                    'name' => $result['name'],
                    'sku_barcode' => $result['sku_barcode'],
                    'box' => $result['box'],
                    'warehouse' => $result['warehouse_limit'],
                );
            }
        }
        $data['text_no_results'] = $this->language->get('text_no_results');
        $this->response->setOutput($this->load->view('catalog/product_sku_history.tpl', $data));
    }

    public function updateBarcode(){
        $this->load->model('catalog/product_inv');
        $product_id = isset($this->request->get['product_id'])?$this->request->get['product_id']:0;
        $sku_barcode_id = isset($this->request->get['sku_barcode_id'])?$this->request->get['sku_barcode_id']:0;
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $json = $this->model_catalog_product_inv->updateBarcode($product_id,$sku_barcode_id,$this->request->post);
        }

        echo json_encode($json);
    }
}
?>