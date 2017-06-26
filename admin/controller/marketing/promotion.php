<?php
class ControllerMarketingPromotion extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('marketing/promotion');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/promotion');


        $this->getList();
    }

    public function add()
    {
        $this->load->language('marketing/promotion');
        $this->document->setTitle($this->language->get('heading_title'));


        $this->load->model('marketing/promotion');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_promotion->addPromotion($this->request->post);
//
//            $this->session->data['success'] = $this->language->get('text_success');
//
//            $url = '';
//
//            if (isset($this->request->get['sort'])) {
//                $url .= '&sort=' . $this->request->get['sort'];
//            }
//
//            if (isset($this->request->get['order'])) {
//                $url .= '&order=' . $this->request->get['order'];
//            }
//
//            if (isset($this->request->get['page'])) {
//                $url .= '&page=' . $this->request->get['page'];
//            }
//
//            $this->response->redirect($this->url->link('marketing/promotion',
//                'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit()
    {
        $this->load->language('marketing/promotion');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/promotion');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_marketing_promotion->editPromotion($this->request->get['promotion_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('marketing/promotion',
                'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->load->language('marketing/promotion');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/promotion');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            if(!empty($this->request->post['selected'])){
                $promotion_ids = join(',',$this->request->post['selected']);
                $this->model_marketing_promotion->deletePromotion($promotion_ids);
                $this->session->data['success'] = $this->language->get('text_success');
            }
            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('marketing/promotion',
                'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
        $this->getList();
    }

    protected function getList()
    {
        $data['header'] = $this->load->controller('common/header');
        //暂时用session处理全局的warehouse_id_global
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();

        if($data['filter_warehouse_id_global']){
            $filter_warehouse_id_global = $data['filter_warehouse_id_global'];
        }else{
            $filter_warehouse_id_global = 0;
        }

        $this->load->language('marketing/promotion');

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'type';
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
            'href' => $this->url->link('marketing/promotion', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('marketing/promotion/add', 'token=' . $this->session->data['token'] . $url,
            'SSL');
        $data['delete'] = $this->url->link('marketing/promotion/delete',
            'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['promotion'] = array();

        $filter_data = array(
            'filter_warehouse_id_global' => $filter_warehouse_id_global,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $promotion_total = $this->model_marketing_promotion->getTotalPromotion($filter_data);

        $results = $this->model_marketing_promotion->getPromotions($filter_data);
        $this->load->model('station/station');
        foreach ($results as $result) {
            $data['promotions'][] = array(
                'promotion_id' => $result['promotion_id'],
                'type' => $result['type'] == 'gift' ? $this->language->get('text_gift') :
                    $this->language->get('text_discounts'),
                'title' => $result['title'],
                'station' => $this->model_station_station->getStationNameById($result['station_id']),
                'firstorder' => ($result['firstorder'] ? $this->language->get('text_yes') :
                    $this->language->get('text_no')),
                'overlap' => ($result['overlap'] ? $this->language->get('text_enabled') :
                    $this->language->get('text_disabled')),
                'min_cart_total' => $result['min_cart_total'],
                'max_cart_total' => $result['max_cart_total'],
                'disount_fixed' => $result['disount_fixed'] == 'fixed' ? $this->language->get('text_fixed') :
                    $this->language->get('text_rate'),
                'discount' => $result['discount'],
                'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
                'date_end' => date($this->language->get('date_format_short'), strtotime($result['date_end'])),
                'status' => ($result['status'] ? $this->language->get('text_enabled') :
                    $this->language->get('text_disabled')),
                'desc' => $result['desc'],
                'edit' => $this->url->link('marketing/promotion/edit',
                    'token=' . $this->session->data['token'] . '&promotion_id=' . $result['promotion_id'] . $url, 'SSL')

            );
        }
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');

        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_type'] = $this->language->get('column_type');
        $data['column_title'] = $this->language->get('column_title');
        $data['column_station'] = $this->language->get('column_station');
        $data['column_firstorder'] = $this->language->get('column_firstorder');
        $data['column_overlap'] = $this->language->get('column_overlap');
        $data['column_min_cart_total'] = $this->language->get('column_min_cart_total');
        $data['column_max_cart_total'] = $this->language->get('column_max_cart_total');
        $data['column_disount_fixed'] = $this->language->get('column_disount_fixed');
        $data['column_discount'] = $this->language->get('column_discount');
        $data['column_date_start'] = $this->language->get('column_date_start');
        $data['column_date_end'] = $this->language->get('column_date_end');
        $data['column_desc'] = $this->language->get('column_desc');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action '] = $this->language->get('column_action');


        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');

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

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $arr = [
            'sort_type' => 'marketing/promotion',
            'sort_title' => 'marketing/promotion',
            'sort_station' => 'marketing/promotion',
            'sort_firstorder' => 'marketing/promotion',
            'sort_overlap' => 'marketing/promotion',
            'sort_min_cart_total' => 'marketing/promotion',
            'sort_max_cart_total' => 'marketing/promotion',
            'sort_disount_fixed' => 'marketing/promotion',
            'sort_status' => 'marketing/promotion',
            'sort_discount' => 'marketing/promotion',
            'sort_date_start' => 'marketing/promotion',
            'sort_date_end' => 'marketing/promotion',
        ];
        foreach ($arr as $key => $value) {
            $arr = explode('_', $key);
            $arr0 = $arr[0];
            unset($arr[0]);
            $arr1 = join('_', $arr);
            $data[$key] = $this->url->link($value,
                'token=' . $this->session->data['token'] . '&' . $arr0 . '=' . $arr1 . $url, 'SSL');
        }


        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $promotion_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('marketing/promotion',
            'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'),
            ($promotion_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
            ((($page - 1) * $this->config->get('config_limit_admin')) >
                ($promotion_total - $this->config->get('config_limit_admin'))) ? $promotion_total :
                ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
            $promotion_total, ceil($promotion_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/promotion_list.tpl', $data));
    }

    protected function getForm()
    {
        $data['token'] = $this->session->data['token'];
        //language
        $language_common = [
            'text_enabled', 'text_disabled', 'text_yes', 'text_no', 'heading_title', 'button_save', 'button_cancel',
            'tab_general'
        ];
        $language = [
            'text_gift', 'text_discount', 'entry_type', 'entry_title', 'entry_station', 'entry_firstorder',
            'entry_overlap', 'entry_min_cart_total', 'entry_max_cart_total', 'entry_disount_fixed',
            'entry_discount', 'entry_date_start', 'entry_date_end', 'entry_desc', 'entry_status', 'text_fixed',
            'text_rate', 'tab_gift', 'table_product_id', 'table_product_name', 'column_action', 'column_name',
            'column_price', 'tab_history', 'text_no_results', 'table_order_id', 'table_user_id', 'table_date_added',
            'table_added_by', 'table_price', 'table_special_price', 'table_quantity', 'table_customer_id',
            'table_added_by'
        ];
        $language = array_merge($language_common, $language);

        $data['text_form'] = !isset($this->request->get['promotion_id']) ? $this->language->get('text_add') :
            $this->language->get('text_edit');

        foreach ($language as $value) {
            $data[$value] = $this->language->get($value);
        }


        $this->load->model('station/station');
        $data['stations'] = $this->model_station_station->getStationList();


//        $data['help_code'] = $this->language->get('help_code');
//        $data['help_type'] = $this->language->get('help_type');
//        $data['help_logged'] = $this->language->get('help_logged');
//        $data['help_total'] = $this->language->get('help_total');
//        $data['help_category'] = $this->language->get('help_category');
//        $data['help_product'] = $this->language->get('help_product');
//        $data['help_uses_total'] = $this->language->get('help_uses_total');
//        $data['help_uses_customer'] = $this->language->get('help_uses_customer');


        empty($this->request->get['promotion_id']) ? $data['promotion_id'] = 0 :
            $data['promotion_id'] = $this->request->get['promotion_id'];
        empty($this->error['warning']) ? $data['error_warning'] = '' : $data['error_warning'] = $this->error['warning'];

        empty($this->error['title']) ? $data['error_title'] = '' : $data['error_title'] = $this->error['title'];
        empty($this->error['date_start']) ? $data['error_date_start'] = '' :
            $data['error_date_start'] = $this->error['date_start'];
        empty($this->error['date_end']) ? $data['error_date_end'] = '' :
            $data['error_date_end'] = $this->error['date_end'];


        $url = '';
        isset($this->request->get['page']) && $url .= '&page=' . $this->request->get['page'];
        isset($this->request->get['sort']) && $url .= '&sort=' . $this->request->get['sort'];
        isset($this->request->get['order']) && $url .= '&order=' . $this->request->get['order'];


        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/promotion', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['promotion_id'])) {
            $data['action'] =
                $this->url->link('marketing/promotion/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('marketing/promotion/edit',
                'token=' . $this->session->data['token'] . '&promotion_id=' . $this->request->get['promotion_id'] .
                $url, 'SSL');
        }

        $data['cancel'] =
            $this->url->link('marketing/promotion', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['promotion_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
            $promotion_info = $this->model_marketing_promotion->getPromotion($this->request->get['promotion_id']);
            $promotion_info['date_start'] = substr($promotion_info['date_start'], 0, 10);
            $promotion_info['date_end'] = substr($promotion_info['date_end'], 0, 10);
            $gifts_info = $this->model_marketing_promotion->selectGifts($promotion_info['gifts']);

        } else {
            if (!empty($this->request->post['gifts'])) {
                $gifts_info = $this->model_marketing_promotion->selectGifts($this->request->post['gifts']);
            }
        }
        if (!empty($gifts_info)) {
            $data['gifts_info'] = $gifts_info;
        }
        $posts = ['station_id', 'type', 'title', 'firstorder', 'overlap', 'min_cart_total', 'max_cart_total',
            'disount_fixed', 'discount', 'date_start', 'date_end', 'status', 'gifts', 'desc'];
        foreach ($posts as $post) {
            if (isset($this->request->post[$post])) {
                $data[$post] = $this->request->post[$post];
            } elseif (!empty($promotion_info)) {
                $data[$post] = $promotion_info[$post];
            } else {
                $data[$post] = '';
            }
        }
//
//        if (isset($this->request->post['coupon_product'])) {
//            $products = $this->request->post['coupon_product'];
//        } elseif (isset($this->request->get['coupon_id'])) {
//            $products = $this->model_marketing_promotion->getCouponProducts($this->request->get['coupon_id']);
//        } else {
//            $products = array();
//        }
//
//        $this->load->model('catalog/product');
//
//        $data['coupon_product'] = array();
//
//        foreach ($products as $product_id) {
//            $product_info = $this->model_catalog_product->getProduct($product_id);
//
//            if ($product_info) {
//                $data['coupon_product'][] = array(
//                    'product_id' => $product_info['product_id'],
//                    'name' => $product_info['name']
//                );
//            }
//        }
//
//        if (isset($this->request->post['coupon_category'])) {
//            $categories = $this->request->post['coupon_category'];
//        } elseif (isset($this->request->get['coupon_id'])) {
//            $categories = $this->model_marketing_promotion->getCouponCategories($this->request->get['coupon_id']);
//        } else {
//            $categories = array();
//        }
//
//        $this->load->model('catalog/category');
//
//        $data['coupon_category'] = array();
//
//        foreach ($categories as $category_id) {
//            $category_info = $this->model_catalog_category->getCategory($category_id);
//
//            if ($category_info) {
//                $data['coupon_category'][] = array(
//                    'category_id' => $category_info['category_id'],
//                    'name' => ($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']
//                );
//            }
//        }
//
//        if (isset($this->request->post['date_start'])) {
//            $data['date_start'] = $this->request->post['date_start'];
//        } elseif (!empty($coupon_info)) {
//            $data['date_start'] = ($coupon_info['date_start'] != '0000-00-00' ? $coupon_info['date_start'] : '');
//        } else {
//            $data['date_start'] = date('Y-m-d', time());
//        }
//
//        if (isset($this->request->post['date_end'])) {
//            $data['date_end'] = $this->request->post['date_end'];
//        } elseif (!empty($coupon_info)) {
//            $data['date_end'] = ($coupon_info['date_end'] != '0000-00-00' ? $coupon_info['date_end'] : '');
//        } else {
//            $data['date_end'] = date('Y-m-d', strtotime('+1 month'));
//        }
//
//        if (isset($this->request->post['uses_total'])) {
//            $data['uses_total'] = $this->request->post['uses_total'];
//        } elseif (!empty($coupon_info)) {
//            $data['uses_total'] = $coupon_info['uses_total'];
//        } else {
//            $data['uses_total'] = 1;
//        }
//
//        if (isset($this->request->post['uses_customer'])) {
//            $data['uses_customer'] = $this->request->post['uses_customer'];
//        } elseif (!empty($coupon_info)) {
//            $data['uses_customer'] = $coupon_info['uses_customer'];
//        } else {
//            $data['uses_customer'] = 1;
//        }
//
//        if (isset($this->request->post['status'])) {
//            $data['status'] = $this->request->post['status'];
//        } elseif (!empty($coupon_info)) {
//            $data['status'] = $coupon_info['status'];
//        } else {
//            $data['status'] = true;
//        }
        //寻找所有的平台以及平台对应的仓库
        $this->load->model('station/station');
        $data['station_list']   = $this->model_station_station->getStationList();
        $data['warehouse_list'] = $this->model_station_station->getWarehouseAndStation();
        $data['warehouse_ids'] = array();
        if (isset($this->request->get['promotion_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $activity_warehouse_info = $this->model_marketing_promotion->getPromotionWarehouse($this->request->get['promotion_id']);
            foreach($activity_warehouse_info as $val){
                $data['warehouse_ids'][] = $val['warehouse_id'];
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/promotion_form.tpl', $data));
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'marketing/promotion')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['title']) < 3) || (utf8_strlen($this->request->post['title']) > 128)) {
            $this->error['title'] = $this->language->get('error_title');
        }
        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'marketing/promotion')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    public function history()
    {
        $data = [];
        $this->load->model('marketing/promotion');
        $this->load->model('user/user');
        isset($this->request->get['page']) ? $page = $this->request->get['page'] : $page = 1;
        $num = 10;
        $start = ($page - 1) * $num;

        $history_total =
            $this->model_marketing_promotion->getTotalPromotionHistories($this->request->get['promotion_id']);
        isset($this->request->get['page']) ? $page = $this->request->get['page'] : $page = 1;
        $data['results'] = sprintf($this->language->get('text_pagination'),
            ($history_total) ? (($page - 1) * 10) + 1 : 0,
            ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total,
            ceil($history_total / 10));

        $histories_infos =
            $this->model_marketing_promotion->getPromotionHistories($this->request->get['promotion_id'], $start, $num);
        foreach ($histories_infos as &$histories_info) {
            $histories_info['product_name'] =
                $this->model_marketing_promotion->selectGifts($histories_info['product_id'])[0]['name'];
            if ($histories_info['added_by'] != 0) {
                $histories_info['added_by'] = $this->model_user_user->getUser($histories_info['added_by']);
            }
        }
        $data['histories_infos'] = $histories_infos;

        $pagination = new Pagination();
        $pagination->total = $history_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = '{page}';
        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'),
            ($history_total) ? (($page - 1) * 10) + 1 : 0,
            ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total,
            ceil($history_total / 10));

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }


    public function selectGift()
    {
        $this->load->model('marketing/promotion');
        $json = $this->model_marketing_promotion->selectGift($this->request->get['station_id'],
            $this->request->get['name']);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
