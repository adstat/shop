<?php
/**
 * Created by PhpStorm.
 * User: liuyibao
 * Date: 15-9-1
 * Time: 下午3:55
 */
class ControllerStationAccountingCycle extends Controller{
    public function edit(){
        $this->document->setTitle('账期管理');
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '账期管理',
            'href' => $this->url->link('station/station', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['add'] = $this->url->link('station/station/add', 'token=' . $this->session->data['token'], 'SSL');
        $data['edit'] = $this->url->link('station/station/edit', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->load->model('station/station');
        $station_total = $this->model_station_station->getTotalStations();
        $condition = array(
            'start' => ($page - 1) * 30,
            'limit' => 30
        );
        $data['stations'] = $this->model_station_station->getStations($condition);

        $pagination = new Pagination();
        $pagination->total = $station_total;
        $pagination->page = $page;
        $pagination->limit = 30;
        $pagination->url = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
        $data['pagination'] = $pagination->render();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('station/station_list.tpl', $data));
    }

    protected function getForm() {
        $this->document->addStyle('view/javascript/validform/validform_v5.3.2.css');
        $this->document->addScript('view/javascript/validform/validform_v5.3.2_min.js');

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

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = array();
        }

        if (isset($this->error['model'])) {
            $data['error_model'] = $this->error['model'];
        } else {
            $data['error_model'] = '';
        }

        if (isset($this->error['date_available'])) {
            $data['error_date_available'] = $this->error['date_available'];
        } else {
            $data['error_date_available'] = '';
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = '';
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
            'href' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );
        if (!isset($this->request->get['product_id'])) {
            $data['action'] = $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, 'SSL');
        }
        $data['cancel'] = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['token'] = $this->session->data['token'];

        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();

        // 商品信息
        $data['product_id'] = $product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : '0';
        $product_info = $product_id ? $this->model_catalog_product->getProduct($product_id) : array();
        // 基本信息
        $data['image'] = $product_id ? $product_info['image'] : '';
        $this->load->model('tool/image');
        $data['thumb'] = $product_id && is_file(DIR_IMAGE . $product_info['image']) ? $this->model_tool_image->resize($product_info['image'], 100, 100) : $this->model_tool_image->resize('no_image.png', 100, 100) ;
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['model'] = $product_id ? $product_info['model'] : '';
        $data['sku'] = $product_id ? $product_info['sku'] : '';
        $data['location'] = $product_id ? $product_info['location'] : '';

        $data['keyword']  = $product_id ? $product_info['keyword'] : '';
        $data['shipping'] = $product_id ? $product_info['shipping'] : '';
        $data['price']    = $product_id ? $product_info['price'] : '';
        $data['storage_mode'] = $product_id ? $product_info['storage_mode'] : '';
        $data['date_available'] = $product_id && ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
        $data['quantity'] = $product_id ? $product_info['quantity'] : '1';
        $data['minimum'] = $product_id ? $product_info['minimum'] : '1';
        $data['maximum'] = $product_id ? $product_info['maximum'] : '20';
        $data['customer_total_limit'] = $product_id ? $product_info['customer_total_limit'] : '999999';
        $data['wxpay_only'] = $product_id ? $product_info['wxpay_only'] : '0';
        $data['shelf_life'] = $product_id ? $product_info['shelf_life'] : '3';
        $data['issupportstore'] = $product_id ? $product_info['issupportstore'] : '1';
        $data['sort_order'] = $product_id ? $product_info['sort_order'] : '1';

        // 缺货时状态 2-3Days In-Stock
        $this->load->model('localisation/stock_status');
        $data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
        $data['stock_status_id'] = $product_id ? $product_info['stock_status_id'] : 0; // 缺货时状态
        $data['status'] = $product_id ? $product_info['status'] : true;

        // 重量
        $data['weight'] = $product_id ? $product_info['weight'] : '';
        $this->load->model('localisation/weight_class');
        $data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
        $data['weight_class_id'] = $product_id ? $product_info['weight_class_id'] : $this->config->get('config_weight_class_id');

        // 尺寸
        $data['length'] = $product_id ? $product_info['length'] : '';
        $data['width'] = $product_id ? $product_info['width'] : '';
        $data['height'] = $product_id ? $product_info['height'] : '';
        $this->load->model('localisation/length_class');
        $data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
        $data['length_class_id'] = $product_id ? $product_info['length_class_id'] : $this->config->get('config_length_class_id');


        // 商品品牌
        $this->load->model('catalog/manufacturer');
        $data['manufacturer_id'] = $product_id ? $product_info['manufacturer_id'] : 0;
        if($product_id){
            $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
            $data['manufacturer'] = $manufacturer_info ? $manufacturer_info['name'] : '';
        }else{
            $data['manufacturer'] = '';
        }

        // 商品分类
        $this->load->model('catalog/category');
        $categories = $product_id ? $this->model_catalog_product->getProductCategories($product_id) : array();
        $data['product_categories'] = array();
        foreach ($categories as $category_id) {
            $category_info = $this->model_catalog_category->getCategory($category_id);
            if ($category_info) {
                $data['product_categories'][] = array(
                    'category_id' => $category_info['category_id'],
                    'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
                );
            }
        }

        // 用户组
        $this->load->model('sale/customer_group');
        $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();


        // 商品特殊销售价格
        $product_specials = $product_id ? $this->model_catalog_product->getProductSpecials($this->request->get['product_id']) : array();
        $data['product_specials'] = array();
        foreach ($product_specials as $product_special) {
            $data['product_specials'][] = array(
                'customer_group_id' => $product_special['customer_group_id'],
                'priority'          => $product_special['priority'],
                'price'             => $product_special['price'],
                'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
                'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
            );
        }

        // 商品多图
        $product_images = $product_id ? $this->model_catalog_product->getProductImages($product_id) : array();
        $data['product_images'] = array();
        foreach ($product_images as $product_image) {
            if (is_file(DIR_IMAGE . $product_image['image'])) {
                $image = $product_image['image'];
                $thumb = $product_image['image'];
            } else {
                $image = '';
                $thumb = 'no_image.png';
            }

            $data['product_images'][] = array(
                'image'      => $image,
                'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
                'sort_order' => $product_image['sort_order']
            );
        }

        // 关联商品
        $products = $product_id ? $this->model_catalog_product->getProductRelated($product_id) : array();
        $data['product_relateds'] = array();
        foreach ($products as $related_product_id) {
            $related_info = $this->model_catalog_product->getProduct($related_product_id);
            if ($related_info) {
                $data['product_relateds'][] = array(
                    'product_id' => $related_info['product_id'],
                    'name'       => $related_info['name']
                );
            }
        }

        // 商品描述 多语言
        $data['product_description'] = $product_id ? $this->model_catalog_product->getProductDescriptions($product_id) : array();

        // 其它
        $data['error_warning'] = '';
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('catalog/product_form_basic.tpl', $data));
    }
}