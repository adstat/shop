<?php
class ControllerCatalogBalance extends Controller {

	public function index() {
		$this->load->language('catalog/manufacturer');

		$this->document->setTitle("原料供应商余额明细管理");

		$this->load->model('catalog/supplier');

		$this->getList();
	}

	public function edit() {
		$this->load->language('catalog/manufacturer');

		$this->document->setTitle("原料供应商管理");

		$this->load->model('catalog/supplier');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {

			$url = '';

			$this->response->redirect($this->url->link('catalog/supplier', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {

			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
		if (isset($this->request->get['filter_added'])) {
			$filter_added = $this->request->get['filter_added'];
		} else {
			$filter_added = null;
		}
		if (isset($this->request->get['filter_manage'])) {
			$filter_manage = $this->request->get['filter_manage'];
		} else {
			$filter_manage = null;
		}

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = null;
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}




		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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

		if (isset($this->request->get['filter_added'])) {
			$url .= '&filter_added=' . urlencode(html_entity_decode($this->request->get['filter_added'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_manage'])) {
			$url .= '&filter_manage=' . urlencode(html_entity_decode($this->request->get['filter_manage'], ENT_QUOTES, 'UTF-8'));
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
			'text' => "原料供应商管理",
			'href' => $this->url->link('catalog/supplier', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/supplier/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/supplier/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['manufacturers'] = array();

		$filter_data = array(
			'filter_name'=>$filter_name,
			'filter_status'=>$filter_status,
			'filter_added'=>$filter_added,
			'filter_manage'=>$filter_manage,
			'filter_date_start' =>$filter_date_start,
			'filter_date_end'	=>$filter_date_end,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$manufacturer_total = $this->model_catalog_supplier->getTotalManufacturers();

		$results = $this->model_catalog_supplier->getSuppliers($filter_data);

        //仅管理员财务，采购主管和采购及指定采购员可管理余额
        $userId =  $this->user->getId();
        $userGroup = $this->user->getGroupId();
        $passedGroupArr = array(ADMIN_ADMIN_GROUP_ID,ADMIN_FINANCE_DIRECTOR_GROUP_ID,ADMIN_PURCHASE_DIRECTOR_GROUP_ID);

		foreach ($results as $result) {
			$data['manufacturers'][] = array(
				'supplier_id' => $result['supplier_id'],
				'name'            => $result['name'],
				'status'          => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'status_id'  => $result['status'],
				'usergroup'     =>  $result['usergroup'],
				'added_by'       => $result['added_by'],
				'date_added'     => $result['date_added'],
				'username'      => $result['username'],
				'edit'            => ($userId == $result['manage_by'] || in_array($userGroup,$passedGroupArr)) ? $this->url->link('catalog/balance/edit', 'token=' . $this->session->data['token'] . '&supplier_id=' . $result['supplier_id'] . $url, 'SSL'): ''
			);
		}

		$data['heading_title'] = "原料供应商管理";

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_added']  = $this->language->get('entry_added');
		$data['entry_manage'] = $this->language->get('entry_manage');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_starttime'] = $this->language->get('entry_starttime');
		$data['entry_endtime'] = $this->language->get('entry_endtime');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_added'] = $this->language->get('column_added');
		$data['column_manage'] = $this->language->get('column_manage');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_usergroup'] = $this->language->get('column_usergroup');

		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/supplier', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_supplier_id'] = $this->url->link('catalog/supplier', 'token=' . $this->session->data['token'] . '&sort=supplier_id' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $manufacturer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/supplier', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($manufacturer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($manufacturer_total - $this->config->get('config_limit_admin'))) ? $manufacturer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $manufacturer_total, ceil($manufacturer_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;


		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end']   = $filter_date_end;
		$data['filter_name'] = $filter_name;
		$data['filter_added'] = $filter_added;
		$data['filter_manage'] = $filter_manage;
		$data['filter_status'] = $filter_status;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/supplier_balance_list.tpl', $data));
	}

	protected function getForm() {
		$data['heading_title'] = "原料供应商管理";

		$data['text_form'] = !isset($this->request->get['manufacturer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_amount'] = $this->language->get('text_amount');

		$data['entry_addedtime'] = $this->language->get('entry_addedtime');
		$data['entry_manage'] = $this->language->get('entry_manage');
		$data['entry_added'] = $this->language->get('entry_added');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');

		$data['help_keyword'] = $this->language->get('help_keyword');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		if (isset($this->error['date'])) {
			$data['error_date'] = $this->error['date'];
		} else {
			$data['error_date'] = '';
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
			'text' => "原料供应商管理",
			'href' => $this->url->link('catalog/supplier', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['supplier_id'])) {
			$data['action'] = $this->url->link('catalog/supplier/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/supplier/edit', 'token=' . $this->session->data['token'] . '&supplier_id=' . $this->request->get['supplier_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/balance', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['supplier_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$manufacturer_info = $this->model_catalog_supplier->getSupplier($this->request->get['supplier_id']);
		}


		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($manufacturer_info)) {
			$data['name'] = $manufacturer_info['name'];
		} else {
			$data['name'] = '';
		}



		if (isset($this->request->post['filter_manage_userid'])) {
			$data['filter_manage_userid'] = $this->request->post['filter_manage_userid'];
		} elseif (!empty($manufacturer_info)) {
			$data['filter_manage_userid'] = $manufacturer_info['manage_by'];
		} else {
			$data['filter_manage_userid'] = '';
		}

		if (isset($this->request->post['usergroup'])) {
			$data['usergroup'] = $this->request->post['usergroup'];
		} elseif (!empty($manufacturer_info)) {
			$data['usergroup'] = $manufacturer_info['usergroup'];
		} else {
			$data['usergroup'] = '';
		}

		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($manufacturer_info)) {
			$data['date_added'] = $manufacturer_info['date_added'];
		} else {
			$data['date_added'] = '';
		}



		if (isset($this->request->post['market_id'])) {
			$data['market_id'] = $this->request->post['market_id'];
		} elseif (!empty($manufacturer_info)) {
			$data['market_id'] = $manufacturer_info['market_id'];
		} else {
			$data['market_id'] = '';
		}


		if (isset($this->request->post['contact_name'])) {
			$data['contact_name'] = $this->request->post['contact_name'];
		} elseif (!empty($manufacturer_info)) {
			$data['contact_name'] = $manufacturer_info['contact_name'];
		} else {
			$data['contact_name'] = '';
		}
		if (isset($this->request->post['contact_phone'])) {
			$data['contact_phone'] = $this->request->post['contact_phone'];
		} elseif (!empty($manufacturer_info)) {
			$data['contact_phone'] = $manufacturer_info['contact_phone'];
		} else {
			$data['contact_phone'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($manufacturer_info)) {
			$data['status'] = $manufacturer_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['invoice_flag'])) {
			$data['invoice_flag'] = $this->request->post['invoice_flag'];
		} elseif (!empty($manufacturer_info)) {
			$data['invoice_flag'] = $manufacturer_info['invoice_flag'];
		} else {
			$data['invoice_flag'] = 0;
		}

		if (isset($this->request->post['memo'])) {
			$data['memo'] = $this->request->post['memo'];
		} elseif (!empty($manufacturer_info)) {
			$data['memo'] = $manufacturer_info['memo'];
		} else {
			$data['memo'] = '';
		}

		if (isset($this->request->post['checkout_type_id'])) {
			$data['checkout_type_id'] = $this->request->post['checkout_type_id'];
		} elseif (!empty($manufacturer_info)) {
			$data['checkout_type_id'] = $manufacturer_info['checkout_type_id'];
		} else {
			$data['checkout_type_id'] = '';
		}

		if (isset($this->request->post['checkout_cycle_id'])) {
			$data['checkout_cycle_id'] = $this->request->post['checkout_cycle_id'];
		} elseif (!empty($manufacturer_info)) {
			$data['checkout_cycle_id'] = $manufacturer_info['checkout_cycle_id'];
		} else {
			$data['checkout_cycle_id'] = '';
		}

		if (isset($this->request->post['checkout_cycle_num'])) {
			$data['checkout_cycle_num'] = $this->request->post['checkout_cycle_num'];
		} elseif (!empty($manufacturer_info)) {
			$data['checkout_cycle_num'] = $manufacturer_info['checkout_cycle_num'];
		} else {
			$data['checkout_cycle_num'] = '';
		}

		if (isset($this->request->post['checkout_cycle_date'])) {
			$data['checkout_cycle_date'] = $this->request->post['checkout_cycle_date'];
		} elseif (!empty($manufacturer_info)) {
			$data['checkout_cycle_date'] = $manufacturer_info['checkout_cycle_date'];
		} else {
			$data['checkout_cycle_date'] = '';
		}


		if (isset($this->request->post['checkout_username'])) {
			$data['checkout_username'] = $this->request->post['checkout_username'];
		} elseif (!empty($manufacturer_info)) {
			$data['checkout_username'] = $manufacturer_info['checkout_username'];
		} else {
			$data['checkout_username'] = '';
		}
		if (isset($this->request->post['checkout_userbank'])) {
			$data['checkout_userbank'] = $this->request->post['checkout_userbank'];
		} elseif (!empty($manufacturer_info)) {
			$data['checkout_userbank'] = $manufacturer_info['checkout_userbank'];
		} else {
			$data['checkout_userbank'] = '';
		}
		if (isset($this->request->post['checkout_usercard'])) {
			$data['checkout_usercard'] = $this->request->post['checkout_usercard'];
		} elseif (!empty($manufacturer_info)) {
			$data['checkout_usercard'] = $manufacturer_info['checkout_usercard'];
		} else {
			$data['checkout_usercard'] = '';
		}

		$manage_usergroups = $this->model_catalog_supplier->getManageUsergroups();
		$data['manage_usergroups'] = array();
		foreach ($manage_usergroups as $manage_usergroup){
			$data['manage_usergroups'][] = array(
				'user_id' =>$manage_usergroup['user_id'],
				'username' =>$manage_usergroup['username'],
				'usergroup' =>$manage_usergroup['usergroup'],

			);
		}








		// 采购市场
		$supplier_id = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : 0;
		if($supplier_id){
			$market_info = $this->model_catalog_supplier->getMarket($supplier_id);

			$data['market'] = $market_info ? $market_info['name'] : '';
		}else{
			$data['market'] = '';
		}

		$data['supplier_id'] = $supplier_id;
		$data['transaction_types'] = $this->model_catalog_supplier->getTransactionType();

		//$data['order_ids'] = $this->model_catalog_supplier->getOrderIds($this->request->get['supplier_id']);

		//结账类型
		$data['supplier_checkout_type'] = $this->model_catalog_supplier->getCheckoutType();

		//结账周期类型
		$data['supplier_checkout_cycle'] = $this->model_catalog_supplier->getCheckoutCycle();


        //仅管理员财务，采购主管和采购及指定采购员可管理余额
		$add_transaction_flag = false;
		$add_transaction_user_group_arr = array(ADMIN_ADMIN_GROUP_ID,ADMIN_FINANCE_DIRECTOR_GROUP_ID, ADMIN_PURCHASE_DIRECTOR_GROUP_ID);
		$userGroup = $this->user->getGroupId();
        //var_dump($this->user->getGroupId());
		if(in_array($userGroup, $add_transaction_user_group_arr)){
			$add_transaction_flag = true;
		}
		$data['add_transaction_flag'] = $add_transaction_flag;

        $userId = $this->user->getId();
        if($userId == $manufacturer_info['manage_by']){
            $data['add_transaction_flag'] = true;
        }


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/supplier_balance_form.tpl', $data));
	}


}