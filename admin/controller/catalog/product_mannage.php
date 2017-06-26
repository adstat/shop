<?php
class ControllerCatalogProductMannage extends Controller{
    private $error = array();

    public function index(){

        $this->document->setTitle('修改销售价格申请');

        $this->load->model('catalog/product_mannage');

        $user_id = $this->user->getId();

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $flag = $this->model_catalog_product_mannage->submitApplication($user_id,$this->request->post['product']);
            if($flag){
                $this->session->data['success'] = '已提交申请，等待审核！';
            }
        }

        $this->getList();
    }

    public function getList(){
        $data = array();

        $data['heading_title'] = '提交修改价格申请';

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

        $data['action'] = $this->url->link('catalog/product_mannage/index', 'token=' . $this->session->data['token'] , 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/product_price_application.tpl', $data));
    }

    public function getProductInfo(){
        $this->load->model('catalog/product_mannage');

        $product_id = $this->request->post['product_id'] ?  $this->request->post['product_id'] : 0;

        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $json = $this->model_catalog_product_mannage->getProductInfo($product_id);
        }
        echo json_encode($json);
    }
}
?>