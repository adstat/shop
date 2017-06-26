<?php
class ControllerReportInvMiCold extends Controller {
    public function index() {
        
	//set_time_limit(0);

        $this->load->language('report/sale_order');

        $this->document->setTitle($this->language->get('heading_title'));

        $filter_station = isset($this->request->get['filter_station'])?$this->request->get['filter_station']:1;


        $url = '';
        if (isset($this->request->get['filter_station'])) {
            $url .= '&filter_station=' . $this->request->get['filter_station'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '库存',
            'href' => $this->url->link('report/inv_mi_cold', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $this->load->model('report/sale');

        $filter_data = array(
            'filter_station' => $filter_station
        );


        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_all_status'] = $this->language->get('text_all_status');

        $data['column_date_start'] = $this->language->get('column_date_start');
        $data['column_date_end'] = $this->language->get('column_date_end');
        $data['column_orders'] = $this->language->get('column_orders');
        $data['column_products'] = $this->language->get('column_products');
        $data['column_tax'] = $this->language->get('column_tax');
        $data['column_total'] = $this->language->get('column_total');

        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');
        $data['entry_group'] = $this->language->get('entry_group');
        $data['entry_status'] = $this->language->get('entry_status');

        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

        $data['filter_station'] = $filter_station;

        /*
        $data['order_status'] = $this->model_report_sale->getOrderStatus();
        $data['order_payment_status'] = $this->model_report_sale->getOrderPaymentStatus();
        $data['bd_list'] = $this->model_report_sale->getBdList();
        */
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['inv_mi_cold_arr'] = array();
        
        $data['inv_mi_cold_arr'] = $this->model_report_sale->getInvMiCold($filter_station);
        $product_to_promotion_arr = $this->model_report_sale->getProductToPromotion();
        
        
        if(!empty($data['inv_mi_cold_arr'])){
            
            
            foreach($data['inv_mi_cold_arr'] as $key=>$value){
                foreach($value['quantity'] as $k=>$v){
                    
                    if($k == 15 && $product_to_promotion_arr[$key]){
                        if(isset($data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]])){
                            $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['quantity']['15'] = abs($v);
                        }
                        else{
                            $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]] = $value;
                            $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['quantity'] = array();
                            //$data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['name'] .= "(促销品)";
                            $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['name'] =  $this->model_report_sale->getProductName($product_to_promotion_arr[$key]);
                            $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['sum_quantity'] = 0;
                            $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['date_added'] = $value['date_added'];
                            $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['inv_class_sort'] = $value['inv_class_sort'];
                            $data['inv_mi_cold_arr'][$product_to_promotion_arr[$key]]['quantity']['15'] = abs($v);
                            
                            
                        }
                        
                        
                    }
                    
                }
            }
            
            
            foreach($data['inv_mi_cold_arr'] as $key1=>$value1){
                
                foreach($value1['quantity'] as $k1=>$v1){
                    $data['inv_mi_cold_arr'][$key1]['sum_quantity'] += $v1;
                }
                $data['inv_check_date'] = $value1['date_added'];
            }
            
        }
        
        
        
        
        $this->response->setOutput($this->load->view('report/inv_mi_cold.tpl', $data));
        

    }
}