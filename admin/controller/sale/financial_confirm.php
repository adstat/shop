<?php
class ControllerSaleFinancialConfirm extends Controller {
    public function index(){

        $this->load->language('sale/finanical_confirm');
        $this->document->setTitle("财务确认收款");
        $this->load->model('sale/financial_confirm');


        $data['heading_title'] = "财务确认收款";
        $data['breadcrumbs'] = array();

        $url = '';

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => "财务确认收款",
            'href' => $this->url->link('sale/financial_confirm', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $payment_status = $this->model_sale_financial_confirm->getPaymentStatus();
        $data['paymentstatus'] = array();
        foreach($payment_status as $m){
            $data['paymentstatus'][] = array(
              'status_id' => $m['order_payment_status_id'],
                'payment_name'=>$m['name'],
            );
        }

        $data['token'] = $this->session->data['token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('sale/financial_confirm.tpl',$data ));
    }

    public function checkOrders(){
        $orderids_ids = explode(",",$_GET['orderids_ids']);
        $this->load->model('sale/financial_confirm');
        $userId = $this->user->getId();
        $json = $this->model_sale_financial_confirm->checkOrders($orderids_ids,$userId);
        echo json_encode($json);
    }
    public function getOrdersInfo(){
        $orderids_ids = explode(",",$_GET['orderids_ids']);
        $this->load->model('sale/financial_confirm');
        $info = array();
        foreach ($orderids_ids as $orderids){
            $json = $this->model_sale_financial_confirm->getOrdersInfo($orderids);
            $info [] = $json;
        }
        echo json_encode($info);
    }

    public function getUnOrdersInfo(){
        $orderids_ids = explode(",",$_GET['orderids_ids']);
        $this->load->model('sale/financial_confirm');
        $info = array();
        foreach ($orderids_ids as $orderids){
            $json = $this->model_sale_financial_confirm->getUnOrdersInfo($orderids);
            $info [] = $json;
        }
        echo json_encode($info);
    }



}