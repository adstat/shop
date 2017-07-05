<?php
class ControllerCatalogActivity extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/activity');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/activity');

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
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

		if (isset($this->request->get['filter_station'])) {
			$filter_station = $this->request->get['filter_station'];
		} else {
			$filter_station = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'a.sort_order';
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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_station'])) {
			$url .= '&filter_station=' . $this->request->get['filter_station'];
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
			'href' => $this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/activity/add', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['activities'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_status'   => $filter_status,
			'filter_date_start' => $filter_date_start,
			'filter_date_end'	=> $filter_date_end,
			'filter_station' => $filter_station,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$activity_total = $this->model_catalog_activity->getTotalActivities($filter_data);

		$results = $this->model_catalog_activity->getActivities($filter_data);

        $this->load->model('common/common');
        $data['stations'] = $this->model_common_common->getStationList();

        $stationList = array();
        foreach($data['stations'] as $m){
            $stationList[$m['station_id']] = $m['station_name'];
        }

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['act_image'])) {
				$image = $this->model_tool_image->resize($result['act_image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['activities'][] = array(
				'act_id' => $result['act_id'],
				'image'      => $image,
				'station_id'      => $stationList[$result['station_id']],
				'name'       => $result['act_name'],
				'status'     => ($result['act_status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_start' => $result['date_start'],
				'date_end'	 =>	$result['date_end'],
				'sort_order'	 =>	$result['sort_order'],
				'edit'       => $this->url->link('catalog/activity/edit', 'token=' . $this->session->data['token'] . '&act_id=' . $result['act_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_success'] = $this->language->get('text_success');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_starttime'] = $this->language->get('column_starttime');
		$data['column_endtime'] = $this->language->get('column_endtime');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_starttime'] = $this->language->get('entry_starttime');
		$data['entry_endtime'] = $this->language->get('entry_endtime');
		$data['entry_station'] = $this->language->get('entry_station');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_station'])) {
			$url .= '&filter_station=' . $this->request->get['filter_station'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		// $data['sort_name'] = $this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . '&sort=a.act_status' . $url, 'SSL');
		$data['sort_order'] = $this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . '&sort=a.sort_order' . $url, 'SSL');
		$data['sort_starttime'] = $this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . '&sort=a.date_start' . $url, 'SSL');
		$data['sort_endtime'] = $this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . '&sort=a.date_end' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_station'])) {
			$url .= '&filter_station=' . $this->request->get['filter_station'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $activity_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();
		$data['activity_total'] = $activity_total;
		$data['results'] = sprintf($this->language->get('text_pagination'), ($activity_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($activity_total - $this->config->get('config_limit_admin'))) ? $activity_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $activity_total, ceil($activity_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_station'] = $filter_station;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/activity_list.tpl', $data));
	}
	
	public function add() {
		$this->load->language('catalog/activity');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/activity');

		//if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_catalog_activity->addActivity($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
	
	public function edit() {
		$this->load->language('catalog/activity');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/activity');

		//if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_catalog_activity->editActivity($this->request->get['act_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}
	
	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['act_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_minus'] = $this->language->get('text_minus');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_starttime'] = $this->language->get('entry_starttime');
		$data['entry_endtime'] = $this->language->get('entry_endtime');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_relatedproduct'] = $this->language->get('entry_relatedproduct');
		
		$data['help_related'] = $this->language->get('help_related');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_links'] = $this->language->get('tab_links');

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
			'href' => $this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['act_id'])) {
			$data['action'] = $this->url->link('catalog/activity/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/activity/edit', 'token=' . $this->session->data['token'] . '&act_id=' . $this->request->get['act_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/activity', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['act_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$activity_info = $this->model_catalog_activity->getActivity($this->request->get['act_id']);
		}

		$data['token'] = $this->session->data['token'];

		//$this->load->model('localisation/language');

		//$data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('common/common');
        $data['stations'] = $this->model_common_common->getStationList();

		if (isset($this->request->post['station_id'])) {
			$data['station_id'] = $this->request->post['station_id'];
		} elseif (!empty($activity_info)) {
			$data['station_id'] = $activity_info['station_id'];
		} else {
			$data['station_id'] = 0;
		}

		if (isset($this->request->post['act_image'])) {
			$data['act_image'] = $this->request->post['act_image'];
		} elseif (!empty($activity_info)) {
			$data['act_image'] = $activity_info['act_image'];
		} else {
			$data['act_image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['act_image']) && is_file(DIR_IMAGE . $this->request->post['act_image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['act_image'], 100, 100);
		} elseif (!empty($activity_info) && is_file(DIR_IMAGE . $activity_info['act_image'])) {
			$data['thumb'] = $this->model_tool_image->resize($activity_info['act_image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['act_name'])) {
			$data['act_name'] = $this->request->post['act_name'];
		} elseif (!empty($activity_info)) {
			$data['act_name'] = $activity_info['act_name'];
		} else {
			$data['act_name'] = '';
		}

		if (isset($this->request->post['date_start'])) {
			$data['date_start'] = $this->request->post['date_start'];
		} elseif (!empty($activity_info)) {
			$data['date_start'] = ($activity_info['date_start'] != '0000-00-00') ? $activity_info['date_start'] : '';
		} else {
			$data['date_start'] = date('Y-m-d');
		}
		
		if (isset($this->request->post['date_end'])) {
			$data['date_end'] = $this->request->post['date_end'];
		} elseif (!empty($activity_info)) {
			$data['date_end'] = ($activity_info['date_end'] != '0000-00-00') ? $activity_info['date_end'] : '';
		} else {
			$data['date_end'] = date('Y-m-d');
		}

		if (isset($this->request->post['act_status'])) {
			$data['act_status'] = $this->request->post['act_status'];
		} elseif (!empty($activity_info)) {
			$data['act_status'] = $activity_info['act_status'];
		} else {
			$data['act_status'] = true;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($activity_info)) {
			$data['sort_order'] = $activity_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

        $data['relatedProducts'] = array();
        if(isset($this->request->get['act_id'])){
            $data['relatedProducts'] = $this->model_catalog_activity->getActivityRelatedProducts($this->request->get['act_id']);
        }

		$data['product_links'] = array();
		if(isset($this->request->get['act_id'])){
			$data['product_links'] = $this->model_catalog_activity->getActivityRelatedProducts($this->request->get['act_id']);
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		//寻找所有的平台以及平台对应的仓库
		$this->load->model('station/station');
		$data['station_list']   = $this->model_station_station->getStationList();
		$data['warehouse_list'] = $this->model_station_station->getWarehouseAndStation();
		$data['station_warehouse'] = $this->model_station_station->getWarehouseIdBelongToStation($data['station_id']);

		$data['warehouse_ids'] = array();
		if (isset($this->request->get['act_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$activity_warehouse_info = $this->model_catalog_activity->getActivityWarehouse($this->request->get['act_id']);
			foreach($activity_warehouse_info as $val){
				$data['warehouse_ids'][] = $val['warehouse_id'];
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/activity_form.tpl', $data));
	}

	public function getProductInfo() {
		$json = array();

		$product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
		if( $product_id > 0 ){
			$sql = "select p.product_id, p.class, d.name ,s.name station_name, price, p.station_id, p.status
			from oc_product p
			left join oc_product_description d on d.product_id = p.product_id
			left join oc_x_station s on s.station_id = p.station_id
			where p.product_id = '". $product_id ."'";

			$query = $this->db->query($sql);
			$json = $query->row;
		}
		echo json_encode($json, JSON_UNESCAPED_UNICODE);
	}

}