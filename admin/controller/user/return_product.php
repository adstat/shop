<?php
class ControllerUserReturnProduct extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('user/return');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('user/return');

        $this->getList();
    }

    public function getList() {
        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = null;
        }

        if (isset($this->request->get['filter_product_id'])) {
            $filter_product_id = $this->request->get['filter_product_id'];
        } else {
            $filter_product_id = null;
        }

        if (isset($this->request->get['filter_return_confirmed'])) {
            $filter_return_confirmed = $this->request->get['filter_return_confirmed'];
        } else {
            $filter_return_confirmed = null;
        }

        if (isset($this->request->get['filter_logistic_user'])) {
            $filter_logistic_user = $this->request->get['filter_logistic_user'];
        } else {
            $filter_logistic_user = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }
        if (isset($this->request->get['filter_return_reason'])) {
            $filter_return_reason = $this->request->get['filter_return_reason'];
        } else {
            $filter_return_reason = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'op.order_id';
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

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_return_reason'])) {
            $url .= '&filter_return_reason=' . $this->request->get['filter_return_reason'];
        }

        if (isset($this->request->get['filter_return_confirmed'])) {
            $url .= '&filter_return_confirmed=' . $this->request->get['filter_return_confirmed'];
        }

        if (isset($this->request->get['filter_product_id'])) {
            $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
        }

        if (isset($this->request->get['filter_logistic_user'])) {
            $url .= '&filter_logistic_user=' . $this->request->get['filter_logistic_user'];
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
            'href' => $this->url->link('user/return_product', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['returns'] = array();
        $data['filter_order_id'] = $filter_order_id;
        $data['filter_product_id'] = $filter_product_id;
        $data['filter_return_confirmed'] = $filter_return_confirmed;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_logistic_user'] = $filter_logistic_user;
        $data['filter_return_reason'] = $filter_return_reason;
        //搜索项数据
        $filter_data = array(
            'filter_order_id' => $filter_order_id,
            'filter_date_added' => $filter_date_added,
            'filter_return_confirmed' => $filter_return_confirmed,
            'filter_product_id' => $filter_product_id,
            'filter_logistic_user' => $filter_logistic_user,
            'filter_return_reason' => $filter_return_reason,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        //当前查询到的归还数据的总数
        $return_total = $this->model_user_return->getTotalReturns($filter_data);
        //当前查询到的数据
        $results = $this->model_user_return->getReturns($filter_data);

        foreach($results as $result){
            $data['returns'][] = array(
                'orderid' => $result['order_id'],
                'productid' => $result['product_id'],
                'product' => $result['product'],
                'quantity' => $result['quantity'],
                'price' => $result['price'],
                'total' => $result['total'],
                'username' => $result['username'],
                'returnreason' => $result['name'],
                'status' => $result['status'],
                'statusTitle' => $result['status']?$this->language->get('text_enabled'):$this->language->get('text_invalid'),
                'dateadded' => $result['date_added'],
                'confirmed' => $result['confirmed'],
                'confirmedTitle' => $result['confirmed']?'已确认':'未确认',
                'returnaction' => $result['return_action_id'],
                'returntype' => $result['return_type'],
                'in_part' => $result['in_part'],
                'box_quantity' => $result['box_quantity'],
                'view' => $this->url->link('user/return_product/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . '&product_id=' . $result['product_id'] .$url, 'SSL'),
            );
        }
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }


        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_missing'] = $this->language->get('text_missing');
        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_product_id'] = $this->language->get('column_product_id');
        $data['column_product'] = $this->language->get('column_product');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_add_user'] = $this->language->get('column_add_user');
        $data['column_add_date'] = $this->language->get('column_add_date');
        $data['column_return_confirm'] = $this->language->get('column_return_confirm');
        $data['column_return_reason'] = $this->language->get('column_return_reason');
        $data['column_return_status'] = $this->language->get('column_return_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_view'] = $this->language->get('button_view');

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_return_reason'])) {
            $url .= '&filter_return_reason=' . $this->request->get['filter_return_reason'];
        }

        if (isset($this->request->get['filter_return_confirmed'])) {
            $url .= '&filter_return_confirmed=' . $this->request->get['filter_return_confirmed'];
        }

        if (isset($this->request->get['filter_product_id'])) {
            $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
        }

        if (isset($this->request->get['filter_logistic_user'])) {
            $url .= '&filter_logistic_user=' . $this->request->get['filter_logistic_user'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order'] = $this->url->link('user/return_product', 'token=' . $this->session->data['token'] . '&sort=op.order_id' . $url, 'SSL');

        $url = '';


        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_return_reason'])) {
            $url .= '&filter_return_reason=' . $this->request->get['filter_return_reason'];
        }

        if (isset($this->request->get['filter_return_confirmed'])) {
            $url .= '&filter_return_confirmed=' . $this->request->get['filter_return_confirmed'];
        }

        if (isset($this->request->get['filter_product_id'])) {
            $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
        }

        if (isset($this->request->get['filter_logistic_user'])) {
            $url .= '&filter_logistic_user=' . $this->request->get['filter_logistic_user'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $return_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('user/return_product', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($return_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($return_total - $this->config->get('config_limit_admin'))) ? $return_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $return_total, ceil($return_total / $this->config->get('config_limit_admin')));

        $data['logistic_user'] = $this->model_user_return->getLogisticUser();
        $data['return_confirmed'] = array(
            '0' => array(
                'confirm' => '0',
                'name' => '未确认'
            ),
            '1' => array(
                'confirm' => '1',
                'name' => '已确认'
            ),
        );

        $data['filter_order_id'] = $filter_order_id;
        $data['filter_product_id'] = $filter_product_id;
        $data['filter_return_confirmed'] = $filter_return_confirmed;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_logistic_user'] = $filter_logistic_user;
        $data['filter_return_reason'] = $filter_return_reason;

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['heading_title'] = $this->language->get('heading_title');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session->data['token'];

        $this->response->setOutput($this->load->view('user/return_list.tpl', $data));
    }
    public function deleteProduct() {
        $this->load->model('user/return');
        $opid = $this->request->post['opid'];
        $data = explode("-",$opid);
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $result = $this->model_user_return->deleteProduct($data);
        }
        echo json_encode($result);
    }
    public function disableDeliverReturnProduct() {
        $this->load->model('user/return');
        $opid = $this->request->post['opid'];
        $data = explode("-",$opid);
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $result = $this->model_user_return->disableDeliverReturnProduct($data);
        }
        echo json_encode($result);
    }
//    public function confirmReturn() {
//        //最终确认是否退货入库
//        $this->load->language('user/return');
//        $this->load->model('user/return');
//        $userid = $this->user->getId();
//        $datetime = date("Y-m-d H:i:s",time());
//        $data = explode("-",$_GET['orderid-productid']);
//        $return_action_id = $_GET['action_id'];
//        if($this->request->server['REQUEST_METHOD'] == 'POST') {
//            $result = $this->model_user_return->setConfirm($data[0],$data[1],$userid,$datetime,$return_action_id);
//        }
//        echo json_encode($result);
//        $this->session->data['success'] = $this->language->get('text_success');
//        $this->getList();
//    }
    public function confirmReturnInfo(){
        $this->load->model('user/return');
        $user_id = $this->user->getId();
        $order_id = $this->request->post['order_id'];
        $products = $this->request->post['order_product'];
        if($this->request->server['REQUEST_METHOD'] == 'POST') {
            $result = $this->model_user_return->confirmReturn($user_id,$order_id,$products);
        }
        echo json_encode($result);

    }
    public function info() {
        $this->load->language('user/return');
        $this->load->model('user/return');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        if (isset($this->request->get['product_id'])) {
            $product_id = $this->request->get['product_id'];
        } else {
            $product_id = 0;
        }
        if($product_id){
            $product_info = $this->model_user_return->getProductRturn($order_id,$product_id);
        }
        $data['productInfo'] = $product_info[0];
       // print_r($product_info[0]);die;
        //1.语言定义区域

        //url第一次定义区域
        $url = '';
        //heading信息设置区域
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('user/return_product', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );


        $url = '';

        $data[] = array();

        $data['order_id'] = $order_id;
        $data['product_id'] = $product_id;

        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['cancel'] = $this->url->link('user/return_product', 'token=' . $this->session->data['token'] . $url, 'SSL');

        $data['token'] = $this->session->data['token'];

        $data['heading_little'] = $this->language->get('heading_little');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('user/return_info.tpl', $data));
    }
    public function orderReturnList() {
        $this->load->language('user/return');
        $this->load->model('user/return');

        $data['text_no_results'] = $this->language->get('text_no_results');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['orderlist'] = array();

        $results = $this->model_user_return->getOrderList($this->request->get['order_id'], ($page - 1) * 10, 10);

        foreach ($results as $result) {
            $data['orderlist'][] = array(
                'product_id' => $result['product_id'],
                'product' => $result['product'],
                'customername' => $result['customername'],
                'telephone' => $result['telephone'],
                'quantity' => $result['quantity'],
                'action' => $result['action'],
                'status' => $result['status'],
            );
        }

        $list_total = $this->model_user_return->getTotalOrderList($this->request->get['order_id']);

        $pagination = new Pagination();
        $pagination->total = $list_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link('user/return_product/orderReturnList', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($list_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($list_total - 10)) ? $list_total : ((($page - 1) * 10) + 10), $list_total, ceil($list_total / 10));

        $data['token'] = $this->session->data['token'];
        $this->response->setOutput($this->load->view('user/return_order_list.tpl', $data));
    }
}