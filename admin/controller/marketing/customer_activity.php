<?php
class ControllerMarketingCustomerActivity extends Controller {
    private $error = array();

    public function index(){
        $this->load->language('marketing/customer_activity');

        $this->document->setTitle($this->language->get('heading_title'));

//        $this->load->model('sale/customer');

        $this->load->model('marketing/customer_activity');

        $this->getList();
    }

    public function add() {
        $this->load->language('marketing/customer_activity');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/customer_activity');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_marketing_customer_activity->addEvent($this->request->post);

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

            $this->response->redirect($this->url->link('marketing/customer_activity', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('marketing/customer_activity');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/customer_activity');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->model_marketing_customer_activity->editEvent($this->request->get['marketing_event_id'],$this->request->post);

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

            $this->response->redirect($this->url->link('marketing/customer_activity', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function validateForm(){
        if (!$this->user->hasPermission('modify', 'marketing/customer_activity')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['title']) < 3) || (utf8_strlen($this->request->post['name']) > 128)) {
            $this->error['title'] = $this->language->get('error_title');
        }


        return !$this->error;
    }

    protected function getList() {
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'marketing_event_id';
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
            'href' => $this->url->link('marketing/customer_activity', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('marketing/customer_activity/add', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['activities'] = array();

        $filter_data = array(
            'filter_name' => $filter_name,
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $activities_total = $this->model_marketing_customer_activity->getTotalActivities($filter_data);

        $results = $this->model_marketing_customer_activity->getActivities($filter_data);

        foreach ($results as $result) {
            $data['activities'][] = array(
                'marketing_event_id'  => $result['marketing_event_id'],
                'title'       => $result['title'],
                'date_start'  => $result['date_start'],
                'date_end'   => $result['date_end'],
                'edit'       => $this->url->link('marketing/customer_activity/edit', 'token=' . $this->session->data['token'] . '&marketing_event_id=' . $result['marketing_event_id'] . $url, 'SSL')
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');

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

        $data['sort_name'] = $this->url->link('marketing/customer_activity', 'token=' . $this->session->data['token'] . '&sort=marketing_event_id' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $activities_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($activities_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($activities_total - $this->config->get('config_limit_admin'))) ? $activities_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $activities_total, ceil($activities_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;

        $data['order'] = $order;
        $data['filter_name'] = $filter_name;

        $data['token'] = $this->session->data['token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/customer_activity_list.tpl', $data));
    }

    protected  function getForm() {

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['marketing_event_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['token'] = $this->session->data['token'];

        if (isset($this->request->get['marketing_event_id'])) {
            $data['marketing_event_id'] = $this->request->get['marketing_event_id'];
        } else {
            $data['marketing_event_id'] = 0;
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = '';
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
            'href' => $this->url->link('marketing/customer_activity', 'token=' . $this->session->data['token'], 'SSL')
        );

        if (!isset($this->request->get['marketing_event_id'])) {
            $data['action'] = $this->url->link('marketing/customer_activity/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('marketing/customer_activity/edit', 'token=' . $this->session->data['token'] . '&marketing_event_id=' . $this->request->get['marketing_event_id'] . $url, 'SSL');
        }
        
        $data['cancel'] = $this->url->link('marketing/customer_activity', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['marketing_event_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
            $activity_info = $this->model_marketing_customer_activity->getActivity($this->request->get['marketing_event_id']);
        }

        if (isset($this->request->post['title'])) {
            $data['title'] = $this->request->post['title'];
        } elseif (!empty($activity_info)) {
            $data['title'] = $activity_info['title'];
        } else {
            $data['title'] = '';
        }

        if (isset($this->request->post['content'])) {
            $data['content'] = $this->request->post['content'];
        } elseif (!empty($activity_info)) {
            $data['content'] = $activity_info['content'];
        } else {
            $data['content'] = '';
        }

        if (isset($this->request->post['date_start'])) {
            $data['date_start'] = $this->request->post['date_start'];
        } elseif (!empty($activity_info)) {
            $data['date_start'] = $activity_info['date_start'];
        } else {
            $data['date_start'] = '';
        }

        if (isset($this->request->post['date_end'])) {
            $data['date_end'] = $this->request->post['date_end'];
        } elseif (!empty($activity_info)) {
            $data['date_end'] = $activity_info['date_end'];
        } else {
            $data['date_end'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('marketing/customer_activity_form.tpl', $data));
    }

    public function signUpList() {
        $this->load->model('marketing/customer_activity');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['customers'] = array();

        $results = $this->model_marketing_customer_activity->getSignUpList($this->request->get['marketing_event_id'], ($page - 1) *10, 10);

        foreach ($results as $result) {
            $data['customers'][] = array(
                'customer_id'   => $result['customer_id'],
                'name'   => $result['name'],
                'telephone'     => $result['telephone'],
                'bd_name' => $result['bd_name'],
                'date_added' => $result['date_added'],
            );
        }

        $history_total = $this->model_marketing_customer_activity->getTotalSignUpList($this->request->get['marketing_event_id']);

        $pagination = new Pagination();
        $pagination->total = $history_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link('marketing/customer_activity/signUpList', 'token=' . $this->session->data['token'] . '&marketing_event_id=' . $this->request->get['marketing_event_id'] . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

        $this->response->setOutput($this->load->view('marketing/customer_signUpList.tpl', $data));
    }
}