<?php
class ControllerMarketingCustomerPrice extends Controller {
    public function index(){
        $this->load->language('sale/customer');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/customer');

        $this->load->model('marketing/customer_price');


        $this->getList();
    }

    public function edit()
    {
        $this->load->language('marketing/customer_price');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/customer_price');

        $user_id = $this->user->getId();

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_marketing_customer_price->editCustomerProduct($this->request->get['customer_id'],$user_id, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' .
                    urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_email'])) {
                $url .= '&filter_email=' .
                    urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_customer_group_id'])) {
                $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_approved'])) {
                $url .= '&filter_approved=' . $this->request->get['filter_approved'];
            }

            if (isset($this->request->get['filter_ip'])) {
                $url .= '&filter_ip=' . $this->request->get['filter_ip'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

        }

        $this->getForm();
    }

    protected function getList() {
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

//		if (isset($this->request->get['filter_email'])) {
//			$filter_email = $this->request->get['filter_email'];
//		} else {
//			$filter_email = null;
//		}
        if (isset($this->request->get['filter_bd'])) {
            $filter_bd = $this->request->get['filter_bd'];
        } else {
            $filter_bd = null;
        }

//        if (isset($this->request->get['filter_agent'])) {
//            $filter_agent = $this->request->get['filter_agent'];
//        } else {
//            $filter_agent = null;
//        }


        if (isset($this->request->get['filter_customer_group_id'])) {
            $filter_customer_group_id = $this->request->get['filter_customer_group_id'];
        } else {
            $filter_customer_group_id = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }

        if (isset($this->request->get['filter_approved'])) {
            $filter_approved = $this->request->get['filter_approved'];
        } else {
            $filter_approved = null;
        }

//		if (isset($this->request->get['filter_ip'])) {
//			$filter_ip = $this->request->get['filter_ip'];
//		} else {
//			$filter_ip = null;
//		}

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
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
            $url .= '&filter_name=' .
                urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

//		if (isset($this->request->get['filter_email'])) {
//			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
//		}

        if (isset($this->request->get['filter_customer_group_id'])) {
            $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

        if (isset($this->request->get['filter_ip'])) {
            $url .= '&filter_ip=' . $this->request->get['filter_ip'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
            'href' => $this->url->link('marketing/customer_price', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['add'] = $this->url->link('marketing/customer_price/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
        $data['delete'] =
            $this->url->link('marketing/customer_price/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['customers'] = array();

        $filter_data = array(
            'filter_name' => $filter_name,
//			'filter_email'             => $filter_email,
            'filter_bd' => $filter_bd,
//            'filter_agent' => $filter_agent,
            'filter_customer_group_id' => $filter_customer_group_id,
            'filter_status' => $filter_status,
            'filter_approved' => $filter_approved,
            'filter_date_added' => $filter_date_added,
//			'filter_ip'                => $filter_ip,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $customer_total = $this->model_marketing_customer_price->getTotalCustomers($filter_data);

        $results = $this->model_marketing_customer_price->getCustomers($filter_data);

        foreach ($results as $result) {
            if (!$result['approved']) {
                $approve = $this->url->link('sale/customer/approve',
                    'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'] . $url, 'SSL');
            } else {
                $approve = '';
            }

            $login_info = $this->model_sale_customer->getTotalLoginAttempts($result['email']);

            if ($login_info && $login_info['total'] > $this->config->get('config_login_attempts')) {
                $unlock = $this->url->link('sale/customer/unlock',
                    'token=' . $this->session->data['token'] . '&email=' . $result['email'] . $url, 'SSL');
            } else {
                $unlock = '';
            }

            $data['customers'][] = array(
                'customer_id' => $result['customer_id'],
                'name' => $result['name'],
//                'email'          => $result['email'],
                'merchant_name' => $result['merchant_name'],
                'merchant_address' => $result['merchant_address'],
                'customer_group' => $result['customer_group'],
                'orderid' => empty($result['orderid']) ? '' : $result['orderid'],
                'order_date_added' => empty($result['order_date_added']) ? '' : $result['order_date_added'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') :
                    $this->language->get('text_disabled')),
//				'ip'               => $result['ip'],
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'approve' => $approve,
                'unlock' => $unlock,
                'edit' => $this->url->link('marketing/customer_price/edit',
                    'token=' . $this->session->data['token'] . '&customer_id=' . $result['customer_id'] . $url, 'SSL')
            );
        }
//        $language_common = [
//            'text_enabled', 'text_disabled', 'text_yes', 'text_no', 'heading_title', 'button_save', 'button_cancel',
//            'tab_general'
//        ];
        $language = [
            'column_select_name', 'column_bd', 'column_agent', 'column_merchant_name', 'column_merchant_address',
            'column_last_order'
        ];
//        $language = array_merge($language_common, $language);
        foreach ($language as $value) {
            $data[$value] = $this->language->get($value);
        }
        $this->load->model('common/common');
        $data['bd_lists'] = $this->model_common_common->getBdList();

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_name'] = $this->language->get('column_name');
        $data['column_email'] = $this->language->get('column_email');
        $data['column_customer_group'] = $this->language->get('column_customer_group');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_approved'] = $this->language->get('column_approved');
        $data['column_ip'] = $this->language->get('column_ip');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_approved'] = $this->language->get('entry_approved');
        $data['entry_ip'] = $this->language->get('entry_ip');
        $data['entry_date_added'] = $this->language->get('entry_date_added');

        $data['button_approve'] = $this->language->get('button_approve');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_login'] = $this->language->get('button_login');
        $data['button_unlock'] = $this->language->get('button_unlock');

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
            $url .= '&filter_name=' .
                urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

//		if (isset($this->request->get['filter_email'])) {
//			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
//		}

//        if (isset($this->request->get['filter_agent'])) {
//            $url .= '&filter_agent=' .
//                urlencode(html_entity_decode($this->request->get['filter_agent'], ENT_QUOTES, 'UTF-8'));
//        }

        if (isset($this->request->get['filter_customer_group_id'])) {
            $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

//		if (isset($this->request->get['filter_ip'])) {
//			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
//		}

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] =
            $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
        $data['sort_email'] =
            $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.email' . $url, 'SSL');
        $data['sort_customer_group'] =
            $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=customer_group' . $url,
                'SSL');
        $data['sort_status'] =
            $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.status' . $url,
                'SSL');
        $data['sort_ip'] =
            $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.ip' . $url, 'SSL');
        $data['sort_date_added'] =
            $this->url->link('sale/customer', 'token=' . $this->session->data['token'] . '&sort=c.date_added' . $url,
                'SSL');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' .
                urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

//		if (isset($this->request->get['filter_email'])) {
//			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
//		}

//        if (isset($this->request->get['filter_agent'])) {
//            $url .= '&filter_agent=' .
//                urlencode(html_entity_decode($this->request->get['filter_agent'], ENT_QUOTES, 'UTF-8'));
//        }


        if (isset($this->request->get['filter_bd'])) {
            $url .= '&filter_bd=' .
                urlencode(html_entity_decode($this->request->get['filter_bd'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_customer_group_id'])) {
            $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

//		if (isset($this->request->get['filter_ip'])) {
//			$url .= '&filter_ip=' . $this->request->get['filter_ip'];
//		}

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $customer_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url =
            $this->url->link('marketing/customer_price', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'),
            ($customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0,
            ((($page - 1) * $this->config->get('config_limit_admin')) >
                ($customer_total - $this->config->get('config_limit_admin'))) ? $customer_total :
                ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')),
            $customer_total, ceil($customer_total / $this->config->get('config_limit_admin')));

        $data['filter_name'] = $filter_name;
//		$data['filter_email'] = $filter_email;
        $data['filter_bd'] = $filter_bd;
//        $data['filter_agent'] = $filter_agent;
        $data['filter_customer_group_id'] = $filter_customer_group_id;
        $data['filter_status'] = $filter_status;
        $data['filter_approved'] = $filter_approved;
//		$data['filter_ip'] = $filter_ip;
        $data['filter_date_added'] = $filter_date_added;

        $this->load->model('sale/customer_group');

        $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('marketing/customer_price_list.tpl', $data));
    }

    protected function getForm(){
        $data = array();

        $data['heading_title'] = $this->language->get('heading_title');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/customer_price', 'token=' . $this->session->data['token'], 'SSL')
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->get['customer_id'])){
            $data['customer_id'] = $this->request->get['customer_id'];
        }else{
            $data['customer_id'] = 0;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' .
                urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

//		if (isset($this->request->get['filter_email'])) {
//			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
//		}

        if (isset($this->request->get['filter_customer_group_id'])) {
            $url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_approved'])) {
            $url .= '&filter_approved=' . $this->request->get['filter_approved'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

        $data['product_links'] = array();

        //取指定用户商品信息
        $customer_product = $this->model_marketing_customer_price->getCustomerProductPrice($this->request->get['customer_id']);

        $data['product_links'] = $customer_product;

        $data['token'] = $this->session->data['token'];

        $data['button_save'] = $this->language->get('button_save');

        $data['action_edit'] = $this->url->link('marketing/customer_price/edit', 'token=' . $this->session->data['token'] . '&customer_id=' . $this->request->get['customer_id'] . $url , 'SSL') ;

        $data['cancel'] = $this->url->link('marketing/customer_price', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/customer_price_form.tpl', $data));
    }
}