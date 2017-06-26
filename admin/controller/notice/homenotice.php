<?php
class  ControllerNoticeHomenotice extends Controller{
    private $error = array();
    public function index() {
        $this->load->language('notice/homenotice');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('notice/homenotice');
        $this->getList();

    }

    protected  function getlist(){

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

        if (isset($this->request->get['stations_id'])) {
            $stations = $this->request->get['stations_id'];
        } else {
            $stations = null;
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

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

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
            'href' => $this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('notice/homenotice/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('notice/homenotice/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['homenotices'] = array();
        $filter_data = array(
            'name'  => $name,
            'stations' => $stations,
            'status' => $status,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );
        $notice_total = $this->model_notice_homenotice->getTotalHomenotice($filter_data);
        $results = $this->model_notice_homenotice->gethomenotices($filter_data);

        foreach ($results as $result) {
            $data['homenotices'][] = array(
                'notice_id' => $result['notice_id'],
                'title'      => $result['title'],
                'date_start' => date($this->language->get('datetime_format'), strtotime($result['date_start'])),
                'date_end'   => date($this->language->get('datetime_format'), strtotime($result['date_end'])),
                'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'station_id' =>$result['name'] ,
                'edit'      => $this->url->link('notice/homenotice/edit', 'token=' . $this->session->data['token'] . '&notice_id=' . $result['notice_id'] . $url, 'SSL')
            );

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

        $data['sort_notice_id'] = $this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . '&sort=notice_id' . $url, 'SSL');
        $data['sort_name'] = $this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
        $data['sort_station_id'] = $this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . '&sort=station_id' . $url, 'SSL');
        $data['sort_date_start'] = $this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . '&sort=date_start' . $url, 'SSL');
        $data['sort_date_end'] = $this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . '&sort=date_end' . $url, 'SSL');
        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        //$data['text_fast'] = $this->language->get('text_fast');
        //$data['text_fresh'] = $this->language->get('text_fresh');
        $this->load->model('station/station');
        $data['station_list'] = $this->model_station_station->getStationList();
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['column_id'] = $this->language->get('column_id');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_stations'] = $this->language->get('column_stations');
        $data['column_action'] = $this->language->get('column_action');
        $data['column_date_start'] = $this->language->get('column_date_start');
        $data['column_date_end'] = $this->language->get('column_date_end');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_stations'] = $this->language->get('entry_stations');
        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');

        $data['token'] = $this->session->data['token'];
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['name'] = $name;
        $data['date_start'] = $date_start;
        $data['status'] = $status;
        $data['stations'] = $stations;
        $data['date_end'] = $date_end;
        $data['sort'] = $sort;
        $data['order'] = $order;
        $pagination = new Pagination();
        $pagination->total = $notice_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('design/banner', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($notice_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($notice_total - $this->config->get('config_limit_admin'))) ? $notice_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $notice_total, ceil($notice_total / $this->config->get('config_limit_admin')));

        $this->response->setOutput($this->load->view('notice/homenotice_list.tpl',$data));
    }

    public function edit() {
        $this->load->language('notice/homenotice');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('notice/homenotice');
        $userId = $this->user->getId();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_notice_homenotice->editHomenotice($this->request->get['notice_id'], $this->request->post,$userId);

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

            $this->response->redirect($this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function add(){
        $this->load->language('notice/homenotice');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('notice/homenotice');
        $userId = $this->user->getId();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_notice_homenotice->addHomenotice($this->request->post,$userId);

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

            $this->response->redirect($this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }


        $this->getForm();
    }



    protected function getForm(){
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['notice_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        /*
        $data['text_fresh'] = $this->language->get('text_fresh');
        $data['text_fast'] = $this->language->get('text_fast');
        */
        $this->load->model('station/station');
        $data['station_list']   = $this->model_station_station->getStationList();
        $data['warehouse_list'] = $this->model_station_station->getWarehouseAndStation();
        $data['text_default'] = $this->language->get('text_default');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_link'] = $this->language->get('entry_link');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');
        $data['entry_stations'] =$this->language->get('entry_stations');
        $data['entry_warehouses'] = $this->language->get('entry_warehouses');
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

        if (isset($this->error['title'])) {
            $data['error_name'] = $this->error['title'];
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
            'href' => $this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['notice_id'])) {
            $data['action'] = $this->url->link('notice/homenotice/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('notice/homenotice/edit', 'token=' . $this->session->data['token'] . '&notice_id=' . $this->request->get['notice_id'] . $url, 'SSL');
        }
        $data['cancel'] = $this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['warehouse_ids'] = array();
        if (isset($this->request->get['notice_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $homenotice_info = $this->model_notice_homenotice->getHomenotice($this->request->get['notice_id']);
            $notice_warehouse_info = $this->model_notice_homenotice->getNoticeWarehouse($this->request->get['notice_id']);
            foreach($notice_warehouse_info as $val){
                $data['warehouse_ids'][] = $val['warehouse_id'];
            }
        }

        $data['token'] = $this->session->data['token'];

        if (isset($this->request->post['title'])) {
            $data['title'] = $this->request->post['title'];
        } elseif (!empty($homenotice_info)) {
            $data['title'] = $homenotice_info['title'];
        } else {
            $data['title'] = '';
        }
        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($homenotice_info)) {
            $data['status'] = $homenotice_info['status'];
        } else {
            $data['status'] = true;
        }
        if (isset($this->request->post['station_id'])){
            $data['station_id'] = $this->request->post['station_id'];
        }elseif(!empty($homenotice_info)){
            $data['station_id'] = $homenotice_info['station_id'];
        }else{
            $data['station_id']='';
        }
        if (isset($this->request->post['date_start'])) {
            $data['date_start'] = $this->request->post['date_start'];
        }elseif(!empty($homenotice_info)) {
            $data['date_start'] = ($homenotice_info['date_start'] != '0000-00-00 ' ? $homenotice_info['date_start'] : '');
        }else{
            $data['date_start'] = date('Y-m-d', time());
        }

        if (isset($this->request->post['date_end'])) {
            $data['date_end'] = $this->request->post['date_end'];
        }elseif(!empty($homenotice_info)) {
            $data['date_end'] = ($homenotice_info['date_end'] != '0000-00-00 ' ? $homenotice_info['date_end'] : '');
        }else {
            $data['date_end'] = date('Y-m-d', strtotime('+1 month'));
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('notice/homenotice_form.tpl', $data));

    }
    public function delete(){
        $this->load->language('notice/homenotice');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('notice/homenotice');
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $notice_id) {

                $this->model_notice_homenotice->deleteHomenotice($notice_id);

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

            $this->response->redirect($this->url->link('notice/homenotice', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }
        $this->getList();
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'notice/homenotice')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
    protected function validateForm()
    {

        if (!$this->user->hasPermission('modify', 'notice/homenotice')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['title']) < 3) || (utf8_strlen($this->request->post['title']) > 64)) {

            $this->error['title'] = $this->language->get('error_name');
        }

        if (!isset($this->request->post['warehouse_ids']) || sizeof($this->request->post['warehouse_ids']) <= 0) {
            $this->error['warning'] = $this->language->get('error_warehouse');
        }

        return !$this->error;
    }
}