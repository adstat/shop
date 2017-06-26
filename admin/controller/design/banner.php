<?php
class ControllerDesignBanner extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		$this->getList();
	}

	public function add() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_design_banner->addBanner($this->request->post);

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

			$this->response->redirect($this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$filter_warehouse_id_global = $this->warehouse->getWarehouseIdGlobal();

			$this->model_design_banner->editBanner($this->request->get['banner_id'], $this->request->post,$filter_warehouse_id_global);

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

			$this->response->redirect($this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('design/banner');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('design/banner');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $banner_id) {
				$this->model_design_banner->deleteBanner($banner_id);
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

			$this->response->redirect($this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {

		if (isset($this->request->get['name'])) {
			$name = $this->request->get['name'];
		} else {
			$name = null;
		}

		if (isset($this->request->get['status'])) {
			$status = $this->request->get['status'];
		} else {
			$status = null;
		}

		if (isset($this->request->get['filter_station_id'])) {
			$filter_station_id = $this->request->get['filter_station_id'];
		} else {
			$filter_station_id = null;
		}


		if (isset($this->request->get['date_start'])) {
			$date_start = $this->request->get['date_start'];
		} else {
			$date_start = null;
		}
		if (isset($this->request->get['date_end'])) {
			$date_end = $this->request->get['date_end'];
		} else {
			$date_end = null;
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
			'href' => $this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		$data['add'] = $this->url->link('design/banner/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('design/banner/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['banners'] = array();

		$filter_data = array(
			'name'  => $name,
			'stations' => $filter_station_id,
			'status' => $status,
			'date_start' => $date_start,
			'date_end' => $date_end,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$banner_total = $this->model_design_banner->getTotalBanners($filter_data);

		$results = $this->model_design_banner->getBanners($filter_data);

//		$banner_total = sizeof($results) ? sizeof($results) : 0;

		foreach ($results as $result) {
			$data['banners'][] = array(
				'banner_id' => $result['banner_id'],
				'name'      => $result['name'],
				'date_start' => date($this->language->get('datetime_format'), strtotime($result['date_start'])),
				'date_end'   => date($this->language->get('datetime_format'), strtotime($result['date_end'])),
				'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'station' =>$result['station'] ,
				'edit'      => $this->url->link('design/banner/edit', 'token=' . $this->session->data['token'] . '&banner_id=' . $result['banner_id'] . $url, 'SSL')
			);

		}


		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_fast'] = $this->language->get('text_fast');
		$data['text_fresh'] = $this->language->get('text_fresh');

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_stations'] = $this->language->get('entry_stations');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['column_id'] = $this->language->get('column_id');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_stations'] = $this->language->get('column_stations');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_date_start'] = $this->language->get('column_date_start');
		$data['column_date_end'] = $this->language->get('column_date_end');

		$data['token'] = $this->session->data['token'];
		$data['button_filter'] = $this->language->get('button_filter');
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

		$data['sort_banner_id'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . '&sort=banner_id' . $url, 'SSL');
		$data['sort_name'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_station_id'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . '&sort=station_id' . $url, 'SSL');
		$data['sort_date_start'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . '&sort=date_start' . $url, 'SSL');
		$data['sort_date_end'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . '&sort=date_end' . $url, 'SSL');
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $banner_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($banner_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($banner_total - $this->config->get('config_limit_admin'))) ? $banner_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $banner_total, ceil($banner_total / $this->config->get('config_limit_admin')));

		$this->load->model('station/station');

		$data['stationList'] = $this->model_station_station->getStationList();

		$data['name'] = $name;
		$data['date_start'] = $date_start;
		$data['status'] = $status;
		$data['filter_station_id'] = $filter_station_id;
		$data['date_end'] = $date_end;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/banner_list.tpl', $data));
	}

	protected function getForm() {
		$data['header'] = $this->load->controller('common/header');

		//增加全局仓库变量，发生改变的时候，页面刷新
		if (isset($this->request->get['filter_warehouse_id_global'])) {
			unset($this->session->data['filter_warehouse_id_global']);
			$data['filter_warehouse_id_global'] = $this->request->get['filter_warehouse_id_global'];
			$this->session->data['filter_warehouse_id_global'] = $this->request->get['filter_warehouse_id_global'];
		}elseif($this->warehouse->getWarehouseIdGlobal()){
			$data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();
		}else{
			unset($this->session->data['filter_warehouse_id_global']);
			$data['filter_warehouse_id_global'] = 0;
			$this->session->data['filter_warehouse_id_global'] = 0;
		}

		$this->load->language('design/banner');

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['banner_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_fresh'] = $this->language->get('text_fresh');
		$data['text_fast'] = $this->language->get('text_fast');
		$data['text_default'] = $this->language->get('text_default');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_link'] = $this->language->get('entry_link');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_stations'] =$this->language->get('entry_stations');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_banner_add'] = $this->language->get('button_banner_add');
		$data['button_remove'] = $this->language->get('button_remove');

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

		if (isset($this->error['banner_image'])) {
			$data['error_banner_image'] = $this->error['banner_image'];
		} else {
			$data['error_banner_image'] = array();
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
			'href' => $this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		if (!isset($this->request->get['banner_id'])) {
			$data['action'] = $this->url->link('design/banner/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('design/banner/edit', 'token=' . $this->session->data['token'] . '&banner_id=' . $this->request->get['banner_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['banner_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$banner_info = $this->model_design_banner->getBanner($this->request->get['banner_id']);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($banner_info)) {
			$data['name'] = $banner_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($banner_info)) {
			$data['status'] = $banner_info['status'];
		} else {
			$data['status'] = true;
		}
		if (isset($this->request->post['station_id'])){
			$data['station_id'] = $this->request->post['station_id'];
		}elseif(!empty($banner_info)){
			$data['station_id'] = $banner_info['station_id'];
		}else{
			$data['station_id']='';
		}
		if (isset($this->request->post['date_start'])) {
			$data['date_start'] = $this->request->post['date_start'];
		}elseif(!empty($banner_info)) {
			$data['date_start'] = ($banner_info['date_start'] != '0000-00-00 ' ? $banner_info['date_start'] : '');
		}else{
			$data['date_start'] = date('Y-m-d', time());
		}

		if (isset($this->request->post['date_end'])) {
			$data['date_end'] = $this->request->post['date_end'];
		}elseif(!empty($banner_info)) {
			$data['date_end'] = ($banner_info['date_end'] != '0000-00-00 ' ? $banner_info['date_end'] : '');
		}else {
			$data['date_end'] = date('Y-m-d', strtotime('+1 month'));
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('tool/image');

		if (isset($this->request->post['banner_image'])) {
			$banner_images = $this->request->post['banner_image'];
		} elseif (isset($this->request->get['banner_id'])) {
			$banner_images = $this->model_design_banner->getBannerImages($this->request->get['banner_id']);
		} else {
			$banner_images = array();
		}

		$data['banner_images'] = array();

		foreach ($banner_images as $banner_image) {
			if (is_file(DIR_IMAGE . $banner_image['image'])) {
				$image = $banner_image['image'];
				$thumb = $banner_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['banner_images'][] = array(
				'banner_image_description' => $banner_image['banner_image_description'],
				'link'                     => $banner_image['link'],
				'image'                    => $image,
				'thumb'                    => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order'               => $banner_image['sort_order']
			);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->load->model('station/station');

		$data['stationList'] = $this->model_station_station->getStationList();

		//寻找所有的平台以及平台对应的仓库
		$data['station_warehouse'] = $this->model_station_station->getStationWarehouse();

		//寻找该banner在哪些平台仓库中有显示
		$data['banner_warehouses'] = $this->model_design_banner->getBannerWarehouse(isset($this->request->get['banner_id'])?$this->request->get['banner_id']:0);
		$data['banner_stations'] = $this->model_design_banner->getBannerStation(isset($this->request->get['banner_id'])?$this->request->get['banner_id']:0);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('design/banner_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'design/banner')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (isset($this->request->post['banner_image'])) {
			foreach ($this->request->post['banner_image'] as $banner_image_id => $banner_image) {
				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					if ((utf8_strlen($banner_image_description['title']) < 2) || (utf8_strlen($banner_image_description['title']) > 64)) {
						$this->error['banner_image'][$banner_image_id][$language_id] = $this->language->get('error_title');
					}
				}
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'design/banner')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}