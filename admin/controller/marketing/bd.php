<?php
class ControllerMarketingBd extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('marketing/bd');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/bd');

        $this->getList();
    }

    public function add(){
        $this->load->language('marketing/bd');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/bd');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_marketing_bd->addBd($this->request->post);

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

            $this->response->redirect($this->url->link('marketing/bd', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    public function edit(){
        $this->load->language('marketing/bd');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/bd');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $this->model_marketing_bd->editBd($this->request->get['bd_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('marketing/bd', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }

    protected function getList() {
        if (isset($this->request->get['filter_bd_name'])) {
            $filter_bd_name = $this->request->get['filter_bd_name'];
        } else {
            $filter_bd_name = null;
        }

        if (isset($this->request->get['filter_bd_id'])) {
            $filter_bd_id = $this->request->get['filter_bd_id'];
        } else {
            $filter_bd_id = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }


        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'bd_id';
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
            'href' => $this->url->link('marketing/bd', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('marketing/bd/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] = $this->url->link('marketing/vd/bdDelete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['bds'] = array();

        $filter_data = array(
            'filter_bd_name' => $filter_bd_name,
            'filter_bd_id' => $filter_bd_id,
            'filter_status' => $filter_status,
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $bd_total = $this->model_marketing_bd->getTotalBds($filter_data);

        $results = $this->model_marketing_bd->getBds($filter_data);

        foreach($results as $result){
            $data['bds'][] = array(
                'bd_id' => $result['bd_id'],
                'bd_name' => $result['bd_name'],
                'phone' => $result['phone'],
                'crm_username' => $result['crm_username'],
                'status' => $result['status']?'启用':'停用',
                'edit' => $this->url->link('marketing/bd/edit', 'token=' . $this->session->data['token'] . '&bd_id=' . $result['bd_id'] . $url, 'SSL')
            );
        }


        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_add'] = $this->language->get('button_add');

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
        //排序
        $data['sort_bd_name'] = $this->url->link('marketing/bd', 'token=' . $this->session->data['token'] . '&sort=bd_name' . $url, 'SSL');
        $data['sort_bd_id'] = $this->url->link('marketing/bd', 'token=' . $this->session->data['token'] . '&sort=bd_id' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('marketing/bd', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $bd_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('marketing/bd', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($bd_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($bd_total - $this->config->get('config_limit_admin'))) ? $bd_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $bd_total, ceil($bd_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;

        $data['order'] = $order;
        $data['filter_bd_name'] = $filter_bd_name;
        $data['filter_bd_id'] = $filter_bd_id;
        $data['filter_status'] = $filter_status;

        $data['token'] = $this->session->data['token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('marketing/bd_list.tpl', $data));
    }

    public function getForm() {
        $data = array();
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['coupon_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['token'] = $this->session->data['token'];

        if (isset($this->request->get['bd_id'])) {
            $data['bd_id'] = $this->request->get['bd_id'];
        } else {
            $data['bd_id'] = 0;
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
            'href' => $this->url->link('marketing/bd', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        if (!isset($this->request->get['bd_id'])) {
            $data['action'] = $this->url->link('marketing/bd/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('marketing/bd/edit', 'token=' . $this->session->data['token'] . '&bd_id=' . $this->request->get['bd_id'] . $url, 'SSL');
        }

        $data['cancel'] = $this->url->link('marketing/bd', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['bd_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
            $bd_info = $this->model_marketing_bd->getBd($this->request->get['bd_id']);
        }

        if (isset($this->request->post['bd_code'])) {
            $data['bd_code'] = $this->request->post['bd_code'];
        } elseif (!empty($bd_info)) {
            $data['bd_code'] = $bd_info['bd_code'];
        } else {
            $data['bd_code'] = '';
        }

        if (isset($this->request->post['bd_name'])) {
            $data['bd_name'] = $this->request->post['bd_name'];
        } elseif (!empty($bd_info)) {
            $data['bd_name'] = $bd_info['bd_name'];
        } else {
            $data['bd_name'] = '';
        }

        if (isset($this->request->post['phone'])) {
            $data['phone'] = $this->request->post['phone'];
        } elseif (!empty($bd_info)) {
            $data['phone'] = $bd_info['phone'];
        } else {
            $data['phone'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($bd_info)) {
            $data['status'] = $bd_info['status'];
        } else {
            $data['status'] = '';
        }

        if (isset($this->request->post['crm_username'])) {
            $data['crm_username'] = $this->request->post['crm_username'];
        } elseif (!empty($bd_info)) {
            $data['crm_username'] = $bd_info['crm_username'];
        } else {
            $data['crm_username'] = '';
        }

        if (isset($this->request->post['sale_access_control'])) {
            $data['sale_access_control'] = $this->request->post['sale_access_control'];
        } elseif (!empty($bd_info)) {
            $data['sale_access_control'] = $bd_info['sale_access_control'];
        } else {
            $data['sale_access_control'] = '';
        }

        if (isset($this->request->post['wx_id'])) {
            $data['wx_id'] = $this->request->post['wx_id'];
        } elseif (!empty($bd_info)) {
            $data['wx_id'] = $bd_info['wx_id'];
        } else {
            $data['wx_id'] = '';
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/bd_form.tpl', $data));
    }

    public function bdDelete() {

    }

}