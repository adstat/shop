<?php
class ControllerMarketingCoupon extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('marketing/coupon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/coupon');

		$this->getList();
	}

	public function add() {

		$this->load->language('marketing/coupon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/coupon');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_marketing_coupon->addCoupon($this->request->post);

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

			$this->response->redirect($this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('marketing/coupon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/coupon');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_marketing_coupon->editCoupon($this->request->get['coupon_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function productDelete() {
		$this->load->language('marketing/coupon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/coupon');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_marketing_coupon->deleteProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getform();
	}

	public function delete() {
		$this->load->language('marketing/coupon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/coupon');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $coupon_id) {
				$this->model_marketing_coupon->deleteCoupon($coupon_id);
			}

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

			$this->response->redirect($this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . $url, 'SSL'));
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

		$this->load->language('marketing/coupon');

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if(isset($this->request->get['filter_valid_days'])) {
			$filter_valid_days = $this->request->get['filter_valid_days'];
		} else {
			$filter_valid_days = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'coupon_id';
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
			'href' => $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('marketing/coupon/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		//$data['delete'] = $this->url->link('marketing/coupon/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('marketing/coupon/productDelete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['coupons'] = array();

		$filter_data = array(
			'filter_name' => $filter_name,
			'filter_valid_days' => $filter_valid_days,
			'filter_status' => $filter_status,
			'filter_warehouse_id_global' => $filter_warehouse_id_global,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$coupon_total = $this->model_marketing_coupon->getTotalCoupons($filter_data);

		$results = $this->model_marketing_coupon->getCoupons($filter_data);

		foreach ($results as $result) {
			$data['coupons'][] = array(
				'coupon_id'  => $result['coupon_id'],
				'name'       => $result['name'],
				'code'       => $result['code'],
				'discount'   => $result['discount'],
				'date_start' => date($this->language->get('date_format_short'), strtotime($result['date_start'])),
				'date_end'   => date($this->language->get('date_format_short'), strtotime($result['date_end'])),
				'status'     => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'times'      =>$result['times'],
				'type'       =>$result['type']='F'?'固定金额':'百分比',
				'online_payment' =>$result['online_payment'] ? $this->language->get('text_yes') : '',
				'bd_only' =>$result['bd_only'] ? $this->language->get('text_yes') : '',
				'total'      =>$result['total'],
				'forbidden' =>($result['customer_limited'] ? $this->language->get('text_forbidden') : $this->language->get('text_allow')),
				'edit'       => $this->url->link('marketing/coupon/edit', 'token=' . $this->session->data['token'] . '&coupon_id=' . $result['coupon_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_code'] = $this->language->get('column_code');
		$data['column_discount'] = $this->language->get('column_discount');
		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_customer_forbidden'] = $this->language->get('column_customer_forbidden');
		$data['column_time'] = $this->language->get('column_time');

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

		$data['sort_name'] = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_code'] = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . '&sort=code' . $url, 'SSL');
		$data['sort_discount'] = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . '&sort=discount' . $url, 'SSL');
		$data['sort_date_start'] = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . '&sort=date_start' . $url, 'SSL');
		$data['sort_date_end'] = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . '&sort=date_end' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $coupon_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($coupon_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($coupon_total - $this->config->get('config_limit_admin'))) ? $coupon_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $coupon_total, ceil($coupon_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;

		$data['order'] = $order;
		$data['filter_name'] = $filter_name;
		$data['filter_valid_days'] = $filter_valid_days;
		$data['filter_status'] = $filter_status;

		$data['token'] = $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/coupon_list.tpl', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['coupon_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_amount'] = $this->language->get('text_amount');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_code'] = $this->language->get('entry_code');
		$data['entry_discount'] = $this->language->get('entry_discount');
		$data['entry_logged'] = $this->language->get('entry_logged');
		$data['entry_shipping'] = $this->language->get('entry_shipping');
		$data['entry_type'] = $this->language->get('entry_type');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_uses_total'] = $this->language->get('entry_uses_total');
		$data['entry_uses_customer'] = $this->language->get('entry_uses_customer');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_station'] = $this->language->get('entry_station');
		$data['entry_times'] = $this->language->get('entry_times');
		$data['entry_request'] = $this->language->get('entry_request');
		$data['entry_newcustomer'] = $this->language->get('entry_newcustomer');
		$data['entry_customerlimited'] = $this->language->get('entry_customerlimited');

		$data['help_code'] = $this->language->get('help_code');
		$data['help_type'] = $this->language->get('help_type');
		$data['help_logged'] = $this->language->get('help_logged');
		$data['help_total'] = $this->language->get('help_total');
		$data['help_category'] = $this->language->get('help_category');
		$data['help_product'] = $this->language->get('help_product');
		$data['help_uses_total'] = $this->language->get('help_uses_total');
		$data['help_uses_customer'] = $this->language->get('help_uses_customer');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_history'] = $this->language->get('tab_history');
		$data['tab_customebind'] = $this->language->get('tab_customebind');
		$data['tab_bindhistory'] = $this->language->get('tab_bindhistory');
		$data['tab_productbind'] = $this->language->get('tab_productbind');
		$data['tab_producthistory'] = $this->language->get('tab_producthistory');

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$data['coupon_id'] = 0;
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

//		if(isset($this->error['alert'])){
//			$data['error_alert'] = $this->error['alert'];
//		}else{
//			$data['error_alert'] = '';
//		}
		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		if (isset($this->error['date_start'])) {
			$data['error_date_start'] = $this->error['date_start'];
		} else {
			$data['error_date_start'] = '';
		}

		if (isset($this->error['date_end'])) {
			$data['error_date_end'] = $this->error['date_end'];
		} else {
			$data['error_date_end'] = '';
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['coupon_id'])) {
			$data['action'] = $this->url->link('marketing/coupon/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('marketing/coupon/edit', 'token=' . $this->session->data['token'] . '&coupon_id=' . $this->request->get['coupon_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['coupon_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
			$coupon_info = $this->model_marketing_coupon->getCoupon($this->request->get['coupon_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($coupon_info)) {
			$data['name'] = $coupon_info['name'];
		} else {
			$data['name'] = '';
		}

//		if (isset($this->request->post['code'])) {
//			$data['code'] = $this->request->post['code'];
//		} elseif (!empty($coupon_info)) {
//			$data['code'] = $coupon_info['code'];
//		} else {
//			$data['code'] = '';
//		}

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (!empty($coupon_info)) {
			$data['type'] = $coupon_info['type'];
		} else {
			$data['type'] = '';
		}

		if (isset($this->request->post['discount'])) {
			$data['discount'] = $this->request->post['discount'];
		} elseif (!empty($coupon_info)) {
			$data['discount'] = $coupon_info['discount'];
		} else {
			$data['discount'] = '';
		}

		if (isset($this->request->post['logged'])) {
			$data['logged'] = $this->request->post['logged'];
		} elseif (!empty($coupon_info)) {
			$data['logged'] = $coupon_info['logged'];
		} else {
			$data['logged'] = '';
		}

		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($coupon_info)) {
			$data['shipping'] = $coupon_info['shipping'];
		} else {
			$data['shipping'] = '';
		}

		if (isset($this->request->post['total'])) {
			$data['total'] = $this->request->post['total'];
		} elseif (!empty($coupon_info)) {
			$data['total'] = $coupon_info['total'];
		} else {
			$data['total'] = '';
		}

		if (isset($this->request->post['coupon_product'])) {
			$products = $this->request->post['coupon_product'];
		} elseif (isset($this->request->get['coupon_id'])) {
			$products = $this->model_marketing_coupon->getCouponProducts($this->request->get['coupon_id']);
		} else {
			$products = array();
		}

		$this->load->model('catalog/product');

		$data['coupon_product'] = array();

		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$data['coupon_product'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}

		if (isset($this->request->post['coupon_category'])) {
			$categories = $this->request->post['coupon_category'];
		} elseif (isset($this->request->get['coupon_id'])) {
			$categories = $this->model_marketing_coupon->getCouponCategories($this->request->get['coupon_id']);
		} else {
			$categories = array();
		}

		$this->load->model('catalog/category');

		$data['coupon_category'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['coupon_category'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']
				);
			}
		}

		if (isset($this->request->post['date_start'])) {
			$data['date_start'] = $this->request->post['date_start'];
		} elseif (!empty($coupon_info)) {
			$data['date_start'] = ($coupon_info['date_start'] != '0000-00-00' ? $coupon_info['date_start'] : '');
		} else {
			$data['date_start'] = date('Y-m-d', time());
		}

		if (isset($this->request->post['date_end'])) {
			$data['date_end'] = $this->request->post['date_end'];
		} elseif (!empty($coupon_info)) {
			$data['date_end'] = ($coupon_info['date_end'] != '0000-00-00' ? $coupon_info['date_end'] : '');
		} else {
			$data['date_end'] = date('Y-m-d', strtotime('+1 month'));
		}

		if (isset($this->request->post['uses_total'])) {
			$data['uses_total'] = $this->request->post['uses_total'];
		} elseif (!empty($coupon_info)) {
			$data['uses_total'] = $coupon_info['uses_total'];
		} else {
			$data['uses_total'] = 1;
		}

		if (isset($this->request->post['uses_customer'])) {
			$data['uses_customer'] = $this->request->post['uses_customer'];
		} elseif (!empty($coupon_info)) {
			$data['uses_customer'] = $coupon_info['uses_customer'];
		} else {
			$data['uses_customer'] = 1;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($coupon_info)) {
			$data['status'] = $coupon_info['status'];
		} else {
			$data['status'] = false;
		}

		if(isset($this->request->post['request'])) {
			$data['request'] = $this->request->post['request'];
		}elseif (!empty($coupon_info)) {
			$data['request'] = $coupon_info['customer_request'];
		}else{
			$data['request'] = false;
		}


		if(isset($this->request->post['online_payment'])) {
			$data['online_payment'] = $this->request->post['online_payment'];
		}elseif (!empty($coupon_info)) {
			$data['online_payment'] = $coupon_info['online_payment'];
		}else{
			$data['online_payment'] = 0;
		}

		if(isset($this->request->post['bd_only'])) {
			$data['bd_only'] = $this->request->post['bd_only'];
		}elseif (!empty($coupon_info)) {
			$data['bd_only'] = $coupon_info['bd_only'];
		}else{
			$data['bd_only'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['station_id'] = $this->request->post['status'];
		} elseif (!empty($coupon_info)) {
			$data['station_id'] = $coupon_info['station_id'];
		} else {
			$data['station_id'] = 2;
		}

		if(isset($this->request->post['newcustomer'])) {
			$data['newcustomer'] = $this->request->post['newcustomer'];
		}elseif (!empty($coupon_info)) {
			$data['newcustomer'] = $coupon_info['new_customer'];
		}else{
			$data['newcustomer'] = false;
		}

		if(isset($this->request->post['customerlimited'])) {
			$data['customerlimited'] = $this->request->post['customerlimited'];
		}elseif (!empty($coupon_info)) {
			$data['customerlimited'] = $coupon_info['customer_limited'];
		}else{
			$data['customerlimited'] = 1;
		}

		if(isset($this->request->post['times'])) {
			$data['times'] = $this->request->post['times'];
		}elseif (!empty($coupon_info)) {
			$data['times'] = $coupon_info['times'];
		}else{
			$data['times'] = 1;
		}

		if(isset($this->request->post['valid_days'])){
			$data['valid_days'] = $this->request->post['valid_days'];
		}elseif(!empty($coupon_info)){
			$data['valid_days'] = $coupon_info['valid_days'];
		}else{
			$data['valid_days'] = 30;
		}

		if(isset($this->request->post['reserve_days'])){
			$data['reserve_days'] = $this->request->post['reserve_days'];
		}elseif(!empty($coupon_info)){
			$data['reserve_days'] = $coupon_info['reserve_days'];
		}else{
			$data['reserve_days'] = 0;
		}

		//寻找所有的平台以及平台对应的仓库
		$this->load->model('station/station');
		$data['station_list']   = $this->model_station_station->getStationList();
		$data['warehouse_list'] = $this->model_station_station->getWarehouseAndStation();
		$data['warehouse_ids'] = array();
		if (isset($this->request->get['coupon_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$activity_warehouse_info = $this->model_marketing_coupon->getCouponWarehouse($this->request->get['coupon_id']);
			foreach($activity_warehouse_info as $val){
				$data['warehouse_ids'][] = $val['warehouse_id'];
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/coupon_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'marketing/coupon')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 128)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (isset($this->request->get['coupon_id'])) {
			$coupon_id = $this->request->get['coupon_id'];
		} else {
			$coupon_id = 0;
		}

//		if($coupon_id){
//			$sql = "select customer_limited from oc_coupon where coupon_id = $coupon_id";
//			$query = $this->db->query($sql)->row;
//			if($query['customer_limited'] == 0){
//				$this->error['alert'] = $this->language->get('error_alert');
//			}
//		}
//		if ((utf8_strlen($this->request->post['code']) < 3) || (utf8_strlen($this->request->post['code']) > 10)) {
//			$this->error['code'] = $this->language->get('error_code');
//		}

//		$coupon_info = $this->model_marketing_coupon->getCouponByCode($this->request->post['code']);
//
//		if ($coupon_info) {
//			if (!isset($this->request->get['coupon_id'])) {
//				$this->error['warning'] = $this->language->get('error_exists');
//			} elseif ($coupon_info['coupon_id'] != $this->request->get['coupon_id']) {
//				$this->error['warning'] = $this->language->get('error_exists');
//			}
//		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'marketing/coupon')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function history() {
		$this->load->language('marketing/coupon');

		$this->load->model('marketing/coupon');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_order_id'] = $this->language->get('column_order_id');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_amount'] = $this->language->get('column_amount');
		$data['column_date_added'] = $this->language->get('column_date_added');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->model_marketing_coupon->getCouponHistories($this->request->get['coupon_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'order_id'   => $result['order_id'],
                'order_url' => $this->url->link('sale/order', 'token=' . $this->session->data['token'] . '&filter_order_id=' . $result['order_id'], 'SSL'),
				'customer'   => $result['customer'],
				'amount'     => $result['amount'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'status'     => $result['status']
            );
		}

		$history_total = $this->model_marketing_coupon->getTotalCouponHistories($this->request->get['coupon_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('marketing/coupon/history', 'token=' . $this->session->data['token'] . '&coupon_id=' . $this->request->get['coupon_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('marketing/coupon_history.tpl', $data));
	}

	public function producthistory() {
		$this->load->language('marketing/coupon');

		$this->load->model('marketing/coupon');

		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['button_edit'] = $this->language->get('button_edit');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$data['delete'] = $this->url->link('marketing/coupon/productDelete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['producthistories'] = array();

		$results = $this->model_marketing_coupon->getProductHistories($this->request->get['coupon_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['producthistories'][] = array(
				'coupon_product_id' => $result['coupon_product_id'],
				'status' => $result['status'],
				'coupon_id' => $result['coupon_id'],
				'product_id'   => $result['product_id'],
				'product_url' => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&filter_model=' . $result['product_id'], 'SSL'),
				'name'     => $result['name'],
			);
		}

		$history_total = $this->model_marketing_coupon->getTotalProductHistories($this->request->get['coupon_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('marketing/coupon/producthistory', 'token=' . $this->session->data['token'] . '&coupon_id=' . $this->request->get['coupon_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('marketing/product_history.tpl', $data));
	}

	public function categoryBindHistory(){
		$this->load->language('marketing/coupon');

		$this->load->model('marketing/coupon');

		$data['text_no_results'] = $this->language->get('text_no_results');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$results = $this->model_marketing_coupon->getCategoryHistories($this->request->get['coupon_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'category_id'   => $result['category_id'],
				'name'   => $result['name'],
				'count'  => $result['count']
			);
		}

		$history_total = $this->model_marketing_coupon->getTotalCategoryHistories($this->request->get['coupon_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('marketing/coupon/categoryBindHistory', 'token=' . $this->session->data['token'] . '&coupon_id=' . $this->request->get['coupon_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$data['coupon_id'] = $_GET['coupon_id'];

		$data['banhistory'] = array();

		$banResults = $this->model_marketing_coupon->getproductBan($results,$this->request->get['coupon_id']);

        $data['banhistories'] = array();
        if($banResults){
			foreach ($banResults as $result) {
				$data['banhistories'][] = array(
					'category_id'   => $result['category_id'],
                    'coupon_id' => $result['coupon_id'],
					'category_name'   => $result['category_name'],
					'product_id'    => $result['product_id'],
					'product_name'  => $result['product_name']
				);
			}
		}

		$this->response->setOutput($this->load->view('marketing/category_history.tpl', $data));
	}

	public function customebind(){
		$this->load->language('marketing/coupon');

		$this->load->model('marketing/coupon');

		$this->error['customer'] = $this->language->get('error_customer');

		$data['customebind'] = array();
		$data['header'] = $this->load->controller('common/header');
		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
			$sql = "select times from oc_coupon where coupon_id = ".$data['coupon_id'];
			$query = $this->db->query($sql);
			$data['times'] = $query->row['times'];
		} else {
			$data['coupon_id'] = 0;
			$data['times'] = 1;
		}
		//取coupon表的customer_limited字段，若为1，则不需要对该优惠券进行客户绑定
		$coupon_check = $data['coupon_id'];
		if($coupon_check){
			$sql = "select customer_limited from oc_coupon where coupon_id = $coupon_check";
			$query = $this->db->query($sql)->row;
			if($query['customer_limited'] == 1){
				$data['customerforbidden'] = true;
			}else{
				$data['customerforbidden'] = false;
			}
		}else{
			$data['customerforbidden'] = null;
		}

		if (isset($this->error['date'])) {
			$data['error_date'] = $this->error['date'];
		} else {
			$data['error_date'] = '';
		}
		if (isset($this->error['customer'])) {
			$data['error_customer'] = $this->error['customer'];
		} else {
			$data['error_customer'] = '';
		}
		if (isset($this->request->post['date_start'])) {
			$data['date_start'] = $this->request->post['date_start'];
		} else {
			$data['date_start'] = date('Y-m-d', time());
		}

		if (isset($this->request->post['date_end'])) {
			$data['date_end'] = $this->request->post['date_end'];
		} else {
			$data['date_end'] = date('Y-m-d', strtotime('+1 month'));
		}
		$this->response->setOutput($this->load->view('marketing/coupon_bind.tpl', $data));
	}

	public function validateBind() {
		if (!$this->user->hasPermission('modify', 'marketing/coupon')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if(!$this->request->post['customer_id']){
			$this->error['customer'] = $this->language->get('error_customer');
		}
		if (!$this->request->post['date_start']||!$this->request->post['date_end']) {
			$this->error['date'] = $this->language->get('error_date');
		}
		return !$this->error;
	}
	public function addbind() {
		$this->load->model('marketing/coupon');
		if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateBind()) {
			$targetTable = 'oc_coupon_customer';
			$postData=$this->request->post;
			$rowData=array(
				'coupon_id' => $postData['coupon_id'],
				'customer_id' => $postData['customer_id'],
				'date_start' => $postData['date_start'],
				'date_end' => $postData['date_end'],
				'times'=> $postData['coupon_times'],
			);
			if($rowData['customer_id']&&$rowData['date_start']&&$rowData['date_end']&&$rowData['times']){
				$result = $this->model_marketing_coupon->addCustomer($targetTable, $rowData);
			}else{
				$result = false;
			}
		}else{
			$result = false;
		}
		echo json_encode($result);
	}
	public function addbinds() {
		$this->load->model('marketing/coupon');
		$targetTable = 'oc_coupon_customer';
		$rowData = $this->request->post;
		$coupon_id = $_GET['coupon_id'];
		if($this->request->server['REQUEST_METHOD'] == 'POST') {
			$result = $this->model_marketing_coupon->addCustomers($targetTable, $rowData);
		}
		echo json_encode($result);
	}
	public function bindhistory() {
		$this->load->language('marketing/coupon');

		$this->load->model('marketing/coupon');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['bindhistory'] = array();

		$results = $this->model_marketing_coupon->getBindHistories($this->request->get['coupon_id'], ($page - 1) *10, 10);

		foreach ($results as $result) {
			$data['bindhistory'][] = array(
				'name'   => $result['name'],
				'date_start'   => $result['date_start'],
				'date_end'     => $result['date_end'],
				'customer' => $result['customer'],
				'customer_url' => $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&filter_name=' . $result['customer'], 'SSL'),
				'times'     => $result['times'],
				'customer_id' => $result['customer_id'],
			);
		}

		$history_total = $this->model_marketing_coupon->getTotalBindHistory($this->request->get['coupon_id']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('marketing/coupon/bindhistory', 'token=' . $this->session->data['token'] . '&coupon_id=' . $this->request->get['coupon_id'] . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('marketing/coupon_bindhistory.tpl', $data));

	}
	public function getCustomer(){
        $customer_id = intval($_GET['customer_id']);
        $coupon_id = intval($_GET['coupon_id']);
        $this->load->model('marketing/coupon');
        $json = $this->model_marketing_coupon->customerInfo($customer_id,$coupon_id);
        echo json_encode($json);
	}

    public function getExcludedProducts(){
        $coupon_id = intval($_GET['coupon_id']);
        $this->load->model('marketing/coupon');
        $json = $this->model_marketing_coupon->getExcludedProducts($coupon_id);
        echo json_encode($json);
    }

	public function returnBanProduct(){
		$product_id = $_GET['product_id'];
		$coupon_id = $_GET['coupon_id'];
		$this->load->model('marketing/coupon');
		$json = $this->model_marketing_coupon->returnBanProduct($coupon_id,$product_id);
		echo json_encode($json);
	}
	public function checkCustomers() {
		$coupon_id = intval($_GET['coupon_id']);
		$customer_ids = explode(",",$_GET['customer_ids']);
		$this->load->model('marketing/coupon');
		$json = $this->model_marketing_coupon->checkCustomers($customer_ids,$coupon_id);
		echo json_encode($json);
	}
	public function checkProducts() {
		$coupon_id = intval($_GET['coupon_id']);
		$products_ids = explode(",",$_GET['product_ids']);
		$this->load->model('marketing/coupon');
		$json = $this->model_marketing_coupon->checkProducts($products_ids,$coupon_id);
		echo json_encode($json);
	}
	public function checkProductsBan() {
		$coupon_id = intval($_GET['coupon_id']);
		$products_ids = explode(",",$_GET['product_ids']);
		$this->load->model('marketing/coupon');
		$json = $this->model_marketing_coupon->checkProducts($products_ids,$coupon_id);
		echo json_encode($json);
	}
	public function productBind() {
		$this->load->language('marketing/coupon');
		$this->load->model('marketing/coupon');

		$data['productbind'] = array();
		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$data['coupon_id'] = 0;
		}
//		$sql = "select status from oc_coupon where coupon_id = ".$data['coupon_id'];
//		$query = $this->db->query($sql);
//		$data['status'] = $query->row['status'];
		$this->response->setOutput($this->load->view('marketing/product_bind.tpl', $data));
	}

	public function productBan(){
		$this->load->language('marketing/coupon');
		$this->load->model('marketing/coupon');

		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$date['coupon_id'] = 0;
		}
		$this->response->setOutput($this->load->view('marketing/product_ban.tpl', $data));

	}

	public function getCouponProductToCategory(){
		$this->load->model('marketing/coupon');

		$products = $_GET['products'];

		$json = $this->model_marketing_coupon->getCouponProductToCategory($products);

		echo json_encode($json);

	}
	public function categoryBan() {
		$this->load->model('marketing/coupon');
		$this->load->model('catalog/category');

		if(isset($this->request->get['coupon_id'])){
			$coupon_id = $this->request->get['coupon_id'];
		}else{
			$coupon_id = 0;
		}
		$categories = $this->model_marketing_coupon->getCategoryBindable($coupon_id);
//		$categories = $this->model_catalog_category->getCategories(array('sort'=>'name'));

		$category_list = array();
		foreach($categories as $value){
			$category_list[] = array(
				'category_id' => $value['category_id'],
				'name'        => strip_tags(html_entity_decode($value['name'], ENT_QUOTES, 'UTF-8'))
			);
		}
		$data = array();
		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$data['coupon_id'] = 0;
		}
		$data['token'] = $this->session->data['token'];
		$data['category_list'] = $category_list;

		$this->response->setOutput($this->load->view('marketing/category_ban.tpl', $data));
	}
	public function categoryBind() {
		$this->load->model('marketing/coupon');
		$this->load->model('catalog/category');

		$categories = $this->model_catalog_category->getCategories(array('sort'=>'name'));

		$category_list = array();
		foreach($categories as $value){
			$category_list[] = array(
				'category_id' => $value['category_id'],
				'name'        => strip_tags(html_entity_decode($value['name'], ENT_QUOTES, 'UTF-8'))
			);
		}
		$data = array();
		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$data['coupon_id'] = 0;
		}
		$data['token'] = $this->session->data['token'];
		$data['category_list'] = $category_list;

		$this->response->setOutput($this->load->view('marketing/category_bind.tpl', $data));
	}
	public function getCouponProducts(){
		$this->load->model('marketing/coupon');

		$category_id = $_GET['category_id'];

		$json = $this->model_marketing_coupon->getCouponProductList($category_id);

		echo json_encode($json);
	}
	public function getCouponCategories(){
		$this->load->model('marketing/coupon');

		$category_ids = $_GET['category_ids'];

		$json = $this->model_marketing_coupon->getCouponCategoryProducts($category_ids);

		echo json_decode($json);
	}
	public function getPreCategoriesBinded(){
		$this->load->model('marketing/coupon');

		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$data['coupon_id'] = 0;
		}

		if($this->request->server['REQUEST_METHOD'] == 'GET' && $data['coupon_id']){
			$result = $this->model_marketing_coupon->getPreCategoriesBinded($data['coupon_id']);
		}
		echo json_encode($result);
	}
	public function getPreCategoriesBanned(){
		$this->load->model('marketing/coupon');

		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$data['coupon_id'] = 0;
		}

		if($this->request->server['REQUEST_METHOD'] == 'GET' && $data['coupon_id']){
			$result = $this->model_marketing_coupon->getPreCategoriesBanned($data['coupon_id']);
		}
		echo json_encode($result);
	}
	public function banCategory(){
		$this->load->model('marketing/coupon');
//		var_dump($this->request->post);die;
		$categories_ids = $this->request->post['categories_ids'];

		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$data['coupon_id'] = 0;
		}

		if($this->request->server['REQUEST_METHOD'] == 'POST' && $data['coupon_id']){
			$result = $this->model_marketing_coupon->banCategory($categories_ids,$data['coupon_id']);
		}
		echo json_encode($result);
	}
	public function addCategory(){
		$this->load->model('marketing/coupon');
//		var_dump($this->request->post);die;
		$category = $this->request->post['category'];
		$products = $this->request->post['products'];

		if (isset($this->request->get['coupon_id'])) {
			$data['coupon_id'] = $this->request->get['coupon_id'];
		} else {
			$data['coupon_id'] = 0;
		}

        //TODO 暂定
        //清除之前的设置
        $this->model_marketing_coupon->resetCouponCategoryBind($data['coupon_id']);
		if($this->request->server['REQUEST_METHOD'] == 'POST' && $data['coupon_id']){
			$result = $this->model_marketing_coupon->addCategory($category,$products,$data['coupon_id']);
		}
		echo json_encode($result);
	}
	public function banproduct() {
		$this->load->model('marketing/coupon');
		$product_ids = explode(",",$_GET['product_ids']);
		$coupon_id = $this->request->post['coupon_id'];
		//$status = $this->request->post['status'];
		if($this->request->server['REQUEST_METHOD'] == 'POST' && $product_ids && $coupon_id) {
			$result = $this->model_marketing_coupon->addBanProduct($product_ids,$coupon_id);
		}
		echo json_encode($result);
	}
	public function addproduct() {
		$this->load->model('marketing/coupon');
		$product_ids = explode(",",$_GET['product_ids']);
		$coupon_id = $this->request->post['coupon_id'];
		//$status = $this->request->post['status'];
		if($this->request->server['REQUEST_METHOD'] == 'POST' && $product_ids && $coupon_id) {
			$result = $this->model_marketing_coupon->addproduct($product_ids,$coupon_id);
		}
		echo json_encode($result);
	}
	public function updateStatus() {
		$this->load->model('marketing/coupon');
		$product_id = $this->request->post['product_id'];
		$status = $this->request->post['status'];
		if($this->request->server['REQUEST_METHOD'] == 'POST'){
			$result =$this->model_marketing_coupon->updateStatus($product_id,$status);
		}
		echo json_encode($result);
	}
 }
