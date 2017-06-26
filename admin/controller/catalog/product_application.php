<?php
class ControllerCatalogProductApplication extends Controller
{
    private $error = array();

    public function index(){
        $this->document->setTitle('审核销售价格申请');

        $this->load->model('catalog/product_mannage');

        $user_id = $this->user->getId();

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validteForm()){
            $flag = $this->model_catalog_product_mannage->confirmApplication($user_id,$this->request->post['selected']);
            if($flag){
                $this->session->data['success'] = '审核成功！';
            }
        }

        $this->getList();
    }

    protected function validteForm(){
        if (!$this->user->hasPermission('modify', 'catalog/product_application')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    private function getList(){

        $data = array();

        if (isset($this->request->get['filter_user'])) {
            $filter_user = $this->request->get['filter_user'];
        } else {
            $filter_user = null;
        }

        if(isset($this->request->get['filter_date'])) {
            $filter_date = $this->request->get['filter_date'];
        } else {
            $filter_date = null;
        }


        $data['heading_title'] = '审核售价申请';

        $data['button_save'] = $this->language->get('button_save');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['action'] = $this->url->link('catalog/product_application/index', 'token=' . $this->session->data['token'] , 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session->data['token'];

        $this->load->model('catalog/product_mannage');

        $data['text_no_results'] = $this->language->get('text_no_results');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['lists'] = array();

        $filter_data = array(
            'filter_user' => $filter_user,
            'filter_date' => $filter_date,
        );

        $data['filter_user'] = $filter_user;
        $data['filter_date'] = $filter_date;

        $results = $this->model_catalog_product_mannage->getApplicationList($filter_data);
        foreach ($results as $result) {
            $data['lists'][] = array(
                'price_edit_id'   => $result['price_edit_id'],
                'product_url'  => $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&filter_product_id=' . $result['product_id'], 'SSL'),
                'product_id' => $result['product_id'],
                'name'   => $result['name'],
                'price'     => $result['price'],
                'sku_price'  => $result['sku_price'],
                'purchase_price' => $result['purchase_price'],
                'edit_price' => $result['edit_price'],
                'app_user' => $result['app_user'],
                'app_time' => $result['app_time'],
            );
        }

        $this->load->model('station/station');
        $data['purchase_person'] = $this->model_station_station->getPurchasePerson();

        $this->response->setOutput($this->load->view('catalog/price_exzamine.tpl', $data));

    }

    public function getConfirmed(){
        $this->load->model('catalog/product_mannage');

        $data['text_no_results'] = $this->language->get('text_no_results');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $data['histories'] = array();

        $results = $this->model_catalog_product_mannage->getConfirmedHistory(($page - 1) * 10, 10);

        foreach ($results as $result) {
            $data['histories'][] = array(
                'price_edit_id'   => $result['price_edit_id'],
                'product_id' => $result['product_id'],
                'name'   => $result['name'],
                'price'     => $result['price'],
                'edit_price' => $result['edit_price'],
                'add_user'     => $result['add_user'],
                'date_added'   => $result['date_added']
            );
        }

        $history_total = $this->model_catalog_product_mannage->getTotalConfirmedList();

        $pagination = new Pagination();
        $pagination->total = $history_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link('catalog/product_application/getConfirmed', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

        $this->response->setOutput($this->load->view('catalog/price_exzamine_history.tpl', $data));
    }

    public function rollbackApplication(){
        $this->load->model('catalog/product_mannage');

        $price_edit_id = $this->request->post['price_edit_id'] ?  $this->request->post['price_edit_id'] : 0;

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $json = $this->model_catalog_product_mannage->rollbackApplication($price_edit_id);
        }
        echo json_encode($json);
    }

}
?>