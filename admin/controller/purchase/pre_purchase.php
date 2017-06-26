<?php

class ControllerPurchasePrePurchase extends Controller {

    private $error = array();

    public function index() {
        
        $this->load->language('sale/order');

        $this->document->setTitle("采购单");

        $this->load->model('purchase/pre_purchase');
        $this->load->model('purchase/pre_purchase_adjust');

        $this->load->model('station/station');

        $this->getList();
    }

    private function setUrl() {
        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_customer_group'])) {
            $url .= '&filter_customer_group=' . $this->request->get['filter_customer_group'];
        }
        if (isset($this->request->get['filter_station'])) {
            $url .= '&filter_station=' . $this->request->get['filter_station'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['filter_deliver_date'])) {
            $url .= '&filter_deliver_date=' . $this->request->get['filter_deliver_date'];
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

        return $url;
    }


    public function add() {
        $this->load->language('sale/order');

        $this->document->setTitle("添加采购单");

        $this->load->model('purchase/pre_purchase');

        $data['pre_purchase_product'] = array();
        $pre_purchase_product = array();
        
        $data['pre_purchase_order_product'] = array();
        $pre_purchase_order_product = array();
        $pre_purchase_order_product[1] = array();
        $data['supplier_credits'] = 0;
        
        
        $date_arr = array();
        if ($this->validate()) {
            if($this->request->server['REQUEST_METHOD'] == 'POST' && !isset($_POST['products'])){
               
               $datetime1 = new DateTime(date("Y-m-d", time() +8*3600));  
                $datetime2 = new DateTime($_POST['date_deliver']);  
               
                $interval = $datetime1->diff($datetime2);
                $this_date = date("Y-m-d", time()+8*3600);
                
                
                $interval_days = $interval->days - 1;
                $interval_days = $interval_days < 0 ? 0 : $interval_days; 
                
                $interval_days_arr = array();
                for($i = 1; $i <= $interval_days; $i ++){
                    $interval_days_arr[] = date("Y-m-d", time()+8*3600+$i*24*3600);
                }
                
                
                $datetime3 = new DateTime($_POST['date_before']);  
                $datetime4 = new DateTime($_POST['date_end']);
               
                
                $interval2 = $datetime3->diff($datetime4);
                $interval_days2 = $interval2->days + 1;

               //不需实时计算，已有报表支持，根据页面选择载入
               if(isset($_POST['preload']) && $_POST['preload']){
                   $pre_purchase_product = $this->model_purchase_pre_purchase->getPrePurchaseProduct($_POST);
                   $pre_purchase_order_product = $this->model_purchase_pre_purchase->getPrePurchaseOrderProduct($_POST);

                   if(!empty($pre_purchase_product)){
                       foreach($pre_purchase_product as $key=>$value){
                           $pre_purchase_product[$key]['pre_purchase_product']['count_date'] = count($value);
                           foreach($value as $k=>$v){
                               if(!in_array($k, $date_arr)){
                                   $date_arr[] = $k;
                               }

                               if(isset($pre_purchase_product[$key]['pre_purchase_product']['count_quantity'])){
                                   $pre_purchase_product[$key]['pre_purchase_product']['count_quantity'] += $v['op_quantity'];
                               }
                               else{
                                   $pre_purchase_product[$key]['pre_purchase_product']['count_quantity'] = $v['op_quantity'];
                               }
                           }

                           $pre_purchase_product[$key]['pre_purchase_product']['name'] = $v['name'];
                           $pre_purchase_product[$key]['pre_purchase_product']['sale_price'] = round($v['sale_price'],2);
                           $pre_purchase_product[$key]['pre_purchase_product']['price'] = $v['price'];
                           $pre_purchase_product[$key]['pre_purchase_product']['real_cost'] = $v['real_cost'];
                           $pre_purchase_product[$key]['pre_purchase_product']['supplier_unit_size'] = $v['supplier_unit_size'];
                           $pre_purchase_product[$key]['pre_purchase_product']['ori_inv'] = $v['ori_inv'];
                           $pre_purchase_product[$key]['pre_purchase_product']['inv_size'] = $v['inv_size'];
                           $pre_purchase_product[$key]['pre_purchase_product']['supplier_order_quantity_type'] = $v['supplier_order_quantity_type'];

                           $pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'] = intval($pre_purchase_product[$key]['pre_purchase_product']['count_quantity'] / count($value));
                           //$pre_purchase_product[$key]['pre_purchase_product']['s_quantity'] = $_POST['s_quantity_status_id'] == 1 ? $v['s_quantity'] : ($v['ori_inv'] + (isset($pre_purchase_order_product[1][$v['product_id']][$this_date]) ? $pre_purchase_order_product[1][$v['product_id']][$this_date]['quantity'] : 0 )  - $pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'] > 0 ? $v['ori_inv'] + (isset($pre_purchase_order_product[1][$v['product_id']][$this_date]) ? $pre_purchase_order_product[1][$v['product_id']][$this_date]['quantity'] : 0 )  - $pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'] : 0);
                           $pre_purchase_product[$key]['pre_purchase_product']['s_quantity'] = $_POST['s_quantity_status_id'] == 1 ? $v['s_quantity'] : ($v['ori_inv'] - $pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'] > 0 ? $v['ori_inv'] - $pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'] : 0);

                           $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] = $pre_purchase_product[$key]['pre_purchase_product']['s_quantity'];
                           //echo $v['product_id'] . " ~ " . $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] . "<br>";
                           foreach($interval_days_arr as $idk => $idv){
                               $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] = $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] + (isset($pre_purchase_order_product[1][$v['product_id']][$idv]) ? $pre_purchase_order_product[1][$v['product_id']][$idv]['quantity'] : 0) - $pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'];
                               $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] = $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] > 0 ? $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] : 0;
                           }
                           //echo $v['product_id'] . " ~ " . $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] . "<br>";
                           $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] = $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] + (isset($pre_purchase_order_product[1][$v['product_id']][$_POST['date_deliver']]) ? $pre_purchase_order_product[1][$v['product_id']][$_POST['date_deliver']]['quantity'] : 0) ;
                           //echo $v['product_id'] . " ~ " . $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] . "<br>";
                           /*
                           $pre_purchase_product[$key]['pre_purchase_product']['pre_purchase_quantity'] = ceil($pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'] * $v['turnover_factor']) -
                                   (
                                   $v['s_quantity'] + (isset($pre_purchase_order_product[2][$v['product_id']]) ? $pre_purchase_order_product[2][$v['product_id']] : 0) - $pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'] * $interval_days > 0
                                   ? ($v['s_quantity'] + (isset($pre_purchase_order_product[2][$v['product_id']]) ? $pre_purchase_order_product[2][$v['product_id']] : 0) - $pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'] * $interval_days)
                                   : 0
                                   ) ;
                           */
                           $pre_purchase_product[$key]['pre_purchase_product']['pre_purchase_quantity'] = ceil($pre_purchase_product[$key]['pre_purchase_product']['avg_quantity'] * $v['turnover_factor']) -
                                   (
                                   $pre_purchase_product[$key]['pre_purchase_product']['before_quantity'] > 0
                                   ? $pre_purchase_product[$key]['pre_purchase_product']['before_quantity']
                                   : 0
                                   ) ;
                           $pre_purchase_product[$key]['pre_purchase_product']['pre_purchase_quantity_old'] = $pre_purchase_product[$key]['pre_purchase_product']['pre_purchase_quantity'];
                           if($v['supplier_order_quantity_type'] == 1){
                                $pre_purchase_product[$key]['pre_purchase_product']['supplier_quantity'] = ceil($pre_purchase_product[$key]['pre_purchase_product']['pre_purchase_quantity'] * $v['inv_size']  / $pre_purchase_product[$key]['pre_purchase_product']['supplier_unit_size'])  ;

                                $pre_purchase_product[$key]['pre_purchase_product']['pre_purchase_quantity'] = intval($pre_purchase_product[$key]['pre_purchase_product']['supplier_quantity'] * $pre_purchase_product[$key]['pre_purchase_product']['supplier_unit_size'] / $v['inv_size']);
                           }
                           if($v['supplier_order_quantity_type'] == 2){
                           $pre_purchase_product[$key]['pre_purchase_product']['supplier_quantity'] = ceil($pre_purchase_product[$key]['pre_purchase_product']['pre_purchase_quantity'] * $v['inv_size']  / $pre_purchase_product[$key]['pre_purchase_product']['supplier_unit_size'])  ;
                                $pre_purchase_product[$key]['pre_purchase_product']['supplier_quantity'] = $pre_purchase_product[$key]['pre_purchase_product']['supplier_quantity'] * $pre_purchase_product[$key]['pre_purchase_product']['supplier_unit_size'] ;


                                $pre_purchase_product[$key]['pre_purchase_product']['pre_purchase_quantity'] = intval( $pre_purchase_product[$key]['pre_purchase_product']['supplier_quantity'] / $v['inv_size']);
                       }
                     }
                   }
               }

                //根据采购预警获取采购信息
                if(isset($_POST['pre_purchase_alert']) && $_POST['pre_purchase_alert']){
                    $pre_purchase_product = array();
                    $alertInfo = $this->model_purchase_pre_purchase->getPrePurchaseAlert($_POST['supplier_type']);
                    if(!empty($alertInfo)){
                        $pre_purchase_product = array();
                        foreach($alertInfo as $value){
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['count_date'] = '';
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['count_quantity'] = '';
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['name'] = $value['name'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['sale_price'] = round($value['sale_price'],2);
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['price'] = $value['price'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['real_cost'] = $value['real_cost'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['supplier_unit_size'] = $value['supplier_unit_size'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['ori_inv'] = $value['ori_inv'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['inv_size'] = $value['inv_size'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['avg_quantity'] = $value['avg_qty'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['s_quantity'] = $value['curr_inv'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['before_quantity'] = '';
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['pre_purchase_quantity'] = '';
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['pre_purchase_quantity_old'] = $value['suggest_inv_amount'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['supplier_quantity'] = ceil($value['suggest_supplier_amount']);
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['supplier_order_quantity_type'] = $value['supplier_order_quantity_type'];
                            $pre_purchase_product[$value['product_id']]['pre_purchase_product']['inv_in'] = '3天['.$value['inv_in_3days'].']<br />5天['.$value['inv_in_5days'].']';
                        }
                    }
                }
               
               sort($date_arr);
               
               //供应商付款信息
               $data['checkout_info'] = $this->model_purchase_pre_purchase->getPrePurchaseSuplierCheckoutInfo($_POST['supplier_type']);
               //供应商账户余额
               $this->load->model('catalog/supplier');
               
               $data['supplier_credits'] = $this->model_catalog_supplier->getTransactionTotal($_POST['supplier_type'],1,1);
            }

            //拷贝订单时读取原采购单信息
            if(isset($_GET['copy_order'])){
                $copy_order_info = $this->model_purchase_pre_purchase->getCopyOrder($_GET['copy_order']);
                if(!empty($copy_order_info)){
                   
                    $data['date_deliver'] = $copy_order_info['date_deliver'];
                    $data['station_id'] = $copy_order_info['station_id'];
                    $data['supplier_type'] = $copy_order_info['supplier_type'];
                    $pre_purchase_product = array();
                    foreach($copy_order_info['products'] as $key => $value){
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['count_date'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['count_quantity'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['name'] = $value['name'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['sale_price'] = round($value['sale_price'],2);
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['price'] = $value['price'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['real_cost'] = $value['real_cost'];;
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['supplier_unit_size'] = $value['supplier_unit_size'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['ori_inv'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['inv_size'] = $value['inv_size'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['avg_quantity'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['s_quantity'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['before_quantity'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['pre_purchase_quantity'] = $value['quantity'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['pre_purchase_quantity_old'] = $value['quantity_old'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['supplier_quantity'] = $value['supplier_quantity'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['supplier_order_quantity_type'] = $value['supplier_order_quantity_type'];
                    }

                    $pre_purchase_order_product = array();
                    $pre_purchase_order_product[1] = array();

                    sort($date_arr);

                    //供应商付款信息
                    $data['checkout_info'] = $this->model_purchase_pre_purchase->getPrePurchaseSuplierCheckoutInfo($data['supplier_type']);
                    //供应商账户余额
                    $this->load->model('catalog/supplier');

                    $data['supplier_credits'] = $this->model_catalog_supplier->getTransactionTotal($data['supplier_type'],1,1);
                }
            }



           if($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['products']) && $this->request->post['products'] && !empty($this->request->post['warehouse_id'])){
                $this->model_purchase_pre_purchase->addPrePurchaseOrder($_POST);
                $this->session->data['success'] = $this->language->get('text_success');

                $url = '';

                if (isset($this->request->get['filter_supplier_type'])) {
                    $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
                }
                if (isset($this->request->get['filter_order_type'])) {
                    $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
                }
                if (isset($this->request->get['filter_purchase_order_id'])) {
                    $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
                }

                if (isset($this->request->get['filter_date_deliver'])) {
                    $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
                }
                if (isset($this->request->get['filter_date_deliver_end'])) {
                    $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
                }
                if (isset($this->request->get['filter_order_status_id'])) {
                    $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
                }
                if (isset($this->request->get['filter_order_checkout_status_id'])) {
                    $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
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

                $this->response->redirect($this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url, 'SSL'));
           }
        }
        
        
        
        
        $data['pre_purchase_product'] = $pre_purchase_product;
        $data['date_array'] = $date_arr;
        $data['pre_purchase_order_product'] = $pre_purchase_order_product[1];
        $this->getForm($data);
    }

    public function add_return() {
        $this->load->language('sale/order');
    
        $this->document->setTitle("添加退货单");

        $this->load->model('purchase/pre_purchase');

        $data['pre_purchase_product'] = array();
        $pre_purchase_product = array();
        
        $data['pre_purchase_order_product'] = array();
        $pre_purchase_order_product = array();
        $pre_purchase_order_product[1] = array();
        $data['supplier_credits'] = 0;
        
        
        
        $date_arr = array();
        if ($this->validate()) {
           
            
           if(isset($_GET['purchase_order_id'])){
               
               $copy_order_info = $this->model_purchase_pre_purchase->getCopyOrder($_GET['purchase_order_id']);
               
               if(!empty($copy_order_info)){
                   
                   $data['purchase_order_id'] = $_GET['purchase_order_id'];
                   
                    $data['date_deliver'] = $copy_order_info['date_deliver'];  
                    $data['station_id'] = $copy_order_info['station_id'];
                    $data['supplier_type'] = $copy_order_info['supplier_type'];
                    $pre_purchase_product = array();
                    foreach($copy_order_info['products'] as $key => $value){
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['count_date'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['count_quantity'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['name'] = $value['name'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['sale_price'] = round($value['sale_price'],2);
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['price'] = $value['price'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['real_cost'] = $value['real_cost'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['supplier_unit_size'] = $value['supplier_unit_size'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['ori_inv'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['inv_size'] = $value['inv_size'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['avg_quantity'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['s_quantity'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['before_quantity'] = '';
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['pre_purchase_quantity'] = $value['quantity'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['pre_purchase_quantity_old'] = $value['quantity_old'];
                        $pre_purchase_product[$value['product_id']]['pre_purchase_product']['supplier_quantity'] = $value['supplier_quantity'];
                    }




                    $pre_purchase_order_product = array();
                    $pre_purchase_order_product[1] = array();

                    //echo "<pre>";print_r($pre_purchase_product);
                    //print_r($pre_purchase_order_product);


                    sort($date_arr);

                    //供应商付款信息
                    $data['checkout_info'] = $this->model_purchase_pre_purchase->getPrePurchaseSuplierCheckoutInfo($data['supplier_type']);
                    //供应商账户余额
                    $this->load->model('catalog/supplier');

                    $data['supplier_credits'] = $this->model_catalog_supplier->getTransactionTotal($data['supplier_type'],1,1);
               }
           }
           
           if($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['products']) && $this->request->post['products']){
               
               if(isset($_POST['order_type'])){
                   $this->model_purchase_pre_purchase->addPrePurchaseOrderReturn($_POST);
               }
               else{
                   $this->model_purchase_pre_purchase->addPrePurchaseOrder($_POST);
               }
               
               $this->session->data['success'] = $this->language->get('text_success');
               
               $url = '';

                if (isset($this->request->get['filter_supplier_type'])) {
                    $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
                }
                if (isset($this->request->get['filter_order_type'])) {
                    $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
                }
                if (isset($this->request->get['filter_purchase_order_id'])) {
                    $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
                }

                if (isset($this->request->get['filter_date_deliver'])) {
                    $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
                }
                if (isset($this->request->get['filter_date_deliver_end'])) {
                    $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
                }
                if (isset($this->request->get['filter_order_status_id'])) {
                    $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
                }
                if (isset($this->request->get['filter_order_checkout_status_id'])) {
                    $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
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

                $this->response->redirect($this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url, 'SSL'));
           }
        }
        
        
        
        
        $data['pre_purchase_product'] = $pre_purchase_product;
        $data['date_array'] = $date_arr;
        $data['pre_purchase_order_product'] = $pre_purchase_order_product[1];
        $this->getForm2($data);
    }

    
    public function update_checkout_status() {
		$json = array();

		$this->load->model('purchase/pre_purchase');
                
                $order_id = $_POST['order_id'];
                $checkout_ope = $_POST['checkout_ope'];

		$return = $this->model_purchase_pre_purchase->editOrderCheckoutStatus($order_id,$checkout_ope);
        if(!$return){
            $json['return_msg'] = "修改失败，请检查订单状态";
            $json['success']  = "修改失败";
        }
        else{
            $json['return_msg'] = "修改成功";
            $json['success'] = "修改成功";
        }
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

    
    public function edit() {
        $this->load->language('sale/order');

        $this->document->setTitle("采购单");

        $this->load->model('purchase/pre_purchase');

       
        if ($this->validate()) {
          
           if($this->request->server['REQUEST_METHOD'] == 'POST'){
               
               if(empty($_POST['product_image'])){
                   $this->session->data['error_warning'] = "请上传采购单图片";
               }
               else{
                    $bool = $this->model_purchase_pre_purchase->editPrePurchaseOrder($_POST);
               
                    if($bool){
                     $this->session->data['success'] = "修改成功";
                    }
                    else{

                        $this->session->data['error_warning'] = "上传采购单失败，确认收货后再提交采购单";
                    }
               }
               $url = '';

                if (isset($this->request->get['filter_supplier_type'])) {
                    $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
                }
                if (isset($this->request->get['filter_order_type'])) {
                    $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
                }
                if (isset($this->request->get['filter_purchase_order_id'])) {
                    $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
                }

                if (isset($this->request->get['filter_date_deliver'])) {
                    $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
                }
                if (isset($this->request->get['filter_date_deliver_end'])) {
                    $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
                }
                if (isset($this->request->get['filter_order_status_id'])) {
                    $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
                }
                if (isset($this->request->get['filter_order_checkout_status_id'])) {
                    $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
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

                $this->response->redirect($this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url, 'SSL'));
           }
        }
        
       
        $this->info();
    }


    public function delete() {
        return false; //DO NOT DELETE
        $this->load->language('sale/order');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/order');

        unset($this->session->data['cookie']);

        if (isset($this->request->get['order_id']) && $this->validate()) {
            // API
            $this->load->model('user/api');

            $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

            if ($api_info) {
                $curl = curl_init();

                // Set SSL if required
                if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
                    curl_setopt($curl, CURLOPT_PORT, 443);
                }

                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

                $json = curl_exec($curl);

                if (!$json) {
                    $this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
                } else {
                    $response = json_decode($json, true);

                    if (isset($response['cookie'])) {
                        $this->session->data['cookie'] = $response['cookie'];
                    }

                    curl_close($curl);
                }
            }
        }

        if (isset($this->session->data['cookie'])) {
            $curl = curl_init();

            // Set SSL if required
            if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
                curl_setopt($curl, CURLOPT_PORT, 443);
            }

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/order/delete&order_id=' . $this->request->get['order_id']);
            curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');

            $json = curl_exec($curl);

            if (!$json) {
                $this->error['warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
            } else {
                $response = json_decode($json, true);

                curl_close($curl);

                if (isset($response['error'])) {
                    $this->error['warning'] = $response['error'];
                }
            }
        }

        if (isset($response['error'])) {
            $this->error['warning'] = $response['error'];
        }

        if (isset($response['success'])) {
            $this->session->data['success'] = $response['success'];

            $url = '';

            if (isset($this->request->get['filter_order_id'])) {
                $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            if (isset($this->request->get['filter_customer'])) {
                $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_order_status'])) {
                $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
            }

            if (isset($this->request->get['filter_customer_group'])) {
                $url .= '&filter_customer_group=' . $this->request->get['filter_customer_group'];
            }
            if (isset($this->request->get['filter_station'])) {
                $url .= '&filter_station=' . $this->request->get['filter_station'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['filter_date_modified'])) {
                $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            }

            if (isset($this->request->get['filter_deliver_date'])) {
                $url .= '&filter_deliver_date=' . $this->request->get['filter_deliver_date'];
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

            $this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getList();
    }

    protected function getList() {

        if (isset($this->request->get['filter_supplier_type'])) {
            $filter_supplier_type = $this->request->get['filter_supplier_type'];
        } else {
            $filter_supplier_type = 0;
        }
        if (isset($this->request->get['filter_order_type'])) {
            $filter_order_type = $this->request->get['filter_order_type'];
        } else {
            $filter_order_type = 0;
        }

        if (isset($this->request->get['filter_purchase_order_id'])) {
            $filter_purchase_order_id = $this->request->get['filter_purchase_order_id'];
        } else {
            $filter_purchase_order_id = '';
        }

        if (isset($this->request->get['filter_purchase_person_id'])) {
            $filter_purchase_person_id = $this->request->get['filter_purchase_person_id'];
        } else {
            $filter_purchase_person_id = '';
        }



        if (isset($this->request->get['filter_date_deliver'])) {
            $filter_date_deliver = $this->request->get['filter_date_deliver'];
        } else {
            $filter_date_deliver = null;
        }
        if (isset($this->request->get['filter_date_deliver_end'])) {
            $filter_date_deliver_end = $this->request->get['filter_date_deliver_end'];
        } else {
            $filter_date_deliver_end = null;
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $filter_order_status_id = $this->request->get['filter_order_status_id'];
        } else {
            $filter_order_status_id = null;
        }
        if (isset($this->request->get['filter_order_checkout_status_id'])) {
            $filter_order_checkout_status_id = $this->request->get['filter_order_checkout_status_id'];
        } else {
            $filter_order_checkout_status_id = null;
        }
        if (isset($this->request->get['filter_warehouse_id_global'])) {
            $filter_warehouse_id = $this->request->get['filter_warehouse_id_global'];
        } else {
            $filter_warehouse_id = 0;
        }


        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.purchase_order_id';
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

        if (isset($this->request->get['filter_supplier_type'])) {
            $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
        }
        if (isset($this->request->get['filter_order_type'])) {
            $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
        }
        if (isset($this->request->get['filter_purchase_order_id'])) {
            $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
        }
        if (isset($this->request->get['filter_purchase_person_id'])) {
            $url .= '&filter_purchase_person_id=' . $this->request->get['filter_purchase_person_id'];
        }

        if (isset($this->request->get['filter_date_deliver'])) {
            $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
        }
        if (isset($this->request->get['filter_date_deliver_end'])) {
            $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_order_checkout_status_id'])) {
            $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
        }
        if (isset($this->request->get['filter_warehouse_id_global'])) {
            $url .= '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'];
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
            'text' => "采购单",
            'href' => $this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

       $data['add'] = $this->url->link('purchase/pre_purchase/add', 'token=' . $this->session->data['token'], 'SSL');

        $data['orders'] = array();

        $filter_data = array(
            'filter_supplier_type' => $filter_supplier_type,
            'filter_order_type' => $filter_order_type,
            'filter_purchase_order_id' => $filter_purchase_order_id,
            'filter_purchase_person_id' => $filter_purchase_person_id,
            'filter_date_deliver' => $filter_date_deliver,
            'filter_date_deliver_end' => $filter_date_deliver_end,
            'filter_order_status_id' => $filter_order_status_id,
            'filter_order_checkout_status_id' => $filter_order_checkout_status_id,
            'filter_warehouse_id' => $filter_warehouse_id,
            'remote_order_type' => 3,   // 去除调整单
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $order_total = $this->model_purchase_pre_purchase->getTotalOrders($filter_data);

        $results = $this->model_purchase_pre_purchase->getOrders($filter_data);

        $warehouse_data = $this->warehouse->getWarehouse();

        $purchase_order_ids = array();
        foreach ($results as $result) {
            
            $get_total = 0;
            $order_get_product = array();
            $order_info = $this->model_purchase_pre_purchase->getOrder($result['purchase_order_id']);
            foreach($order_info['products'] as $k1 => $v1){
                $order_info['products'][$v1['product_id']] = $v1;
                unset($order_info['products'][$k1]);
            }
            
            $order_get_product = $this->model_purchase_pre_purchase->getOrderGetProductInfo($result['purchase_order_id']);
            
            if(!empty($order_get_product)){
                foreach($order_get_product as $k2 => $v2){
                    if(!isset($order_info['products'][$k2])){
                        echo $result['purchase_order_id'] . "<br><br>";
                    }
                    $get_total += round($v2 * ($order_info['products'][$k2]['price'] * $order_info['products'][$k2]['supplier_quantity'] / $order_info['products'][$k2]['quantity'] ), 2);
                }
            }
            
            //获取订单退货信息
            $order_return_info = $this->model_purchase_pre_purchase->getOrderReturn($result['purchase_order_id']);
            
            $data['orders'][] = array(
                'purchase_order_id' => $result['purchase_order_id'],
                'station_id' => $result['station_id'],
                'date_added' => $result['date_added'],
                'date_deliver_plan' => $result['date_deliver'],
                'date_deliver' => $result['real_date_deliver'],
                'add_user_name' => $result['add_user_name'],
                'added_by' => $result['added_by'],
                'confirmed' => $result['confirmed'],
                'confirmed_by' => $result['confirmed_by'],
                'confirm_user_name' => $result['confirm_user_name'],
                'supplier_type' => $result['supplier_type'],
                'product_category' => $result['product_category'],
                'status' => $result['status'],
                'supplier_type_name' => $result['name'],
                'status_name' => $result['status_name'],
                'order_total' => $result['order_total'],
                'use_credits_total' => $result['use_credits_total'],
                'quehuo_credits' => $result['quehuo_credits'],
                'order_return' => $order_return_info['sum_return'],
                'get_total' => $get_total,
                'checkout_status' => $result['checkout_status'],
                'checkout_type_id' => $result['checkout_type_id'],
                'order_type' => $result['order_type'],
                'invoice_flag' => $result['invoice_flag'],
                'warehouse' => !empty($warehouse_data[$result['warehouse_id']]) ? $warehouse_data[$result['warehouse_id']]['title']  : '',
                
                'view' => $this->url->link('purchase/pre_purchase/info', 'token=' . $this->session->data['token'] . '&purchase_order_id=' . $result['purchase_order_id'] . $url, 'SSL'),
                'copy_order' => $this->url->link('purchase/pre_purchase/add', 'token=' . $this->session->data['token'] . '&copy_order=' . $result['purchase_order_id'] . $url, 'SSL'),
                'add_return' => $this->url->link('purchase/pre_purchase/add_return', 'token=' . $this->session->data['token'] . '&purchase_order_id=' . $result['purchase_order_id'] . $url, 'SSL'),
                
            );
            $purchase_order_ids[] = $result['purchase_order_id'];
        }

        // 查询具体调整订单状态
        if(!empty($purchase_order_ids)){
            $where          = array('related_order_ids' => $purchase_order_ids, 'order_type' => 3);
            $fields         = "purchase_order_id, related_order, status, order_total";
            $adjust_data    = $this->model_purchase_pre_purchase_adjust->getAdjustOrderInfo($where, $fields);
            $adjust_status  = array();
            $adjust_total   = array();
            if(!empty($adjust_data)){
                foreach($adjust_data as $val){
                    $adjust_status[$val['related_order']][] = $val['status'];
                    if($val['status'] == 2){
                        !isset($adjust_total[$val['related_order']]) && $adjust_total[$val['related_order']] = 0;
                        $adjust_total[$val['related_order']] += $val['order_total'];
                    }
                }
            }

            foreach($data['orders'] as &$order) {
                $order['adjust_type'] = 0;
                if (!empty($adjust_status[$order['purchase_order_id']])) {
                    $order['adjust_type'] = 1;
                    if(in_array(2, $adjust_status[$order['purchase_order_id']])){
                        // 金额矫正
                        !empty($adjust_total[$order['purchase_order_id']]) && $order['order_total'] += $adjust_total[$order['purchase_order_id']];
                    }
                }
            }
        }
        
        $data['heading_title'] = "采购单";

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_missing'] = $this->language->get('text_missing');

        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_date_modified'] = $this->language->get('column_date_modified');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_return_id'] = $this->language->get('entry_return_id');
        $data['entry_order_id'] = $this->language->get('entry_order_id');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['entry_date_modified'] = $this->language->get('entry_date_modified');

        $data['button_invoice_print'] = $this->language->get('button_invoice_print');
        $data['button_shipping_print'] = $this->language->get('button_shipping_print');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_view'] = $this->language->get('button_view');

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

         if (isset($this->session->data['error_warning'])) {
            $data['error_warning'] = $this->session->data['error_warning'];

            unset($this->session->data['error_warning']);
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_supplier_type'])) {
            $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
        }
        if (isset($this->request->get['filter_order_type'])) {
            $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
        }
        if (isset($this->request->get['filter_purchase_order_id'])) {
            $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
        }
        if (isset($this->request->get['filter_purchase_person_id'])) {
            $url .= '&filter_purchase_person_id=' . $this->request->get['filter_purchase_person_id'];
        }

        if (isset($this->request->get['filter_date_deliver'])) {
            $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
        }
        if (isset($this->request->get['filter_date_deliver_end'])) {
            $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
        }
         if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_order_checkout_status_id'])) {
            $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
        }
        if (isset($this->request->get['filter_warehouse_id_global'])) {
            $url .= '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order'] = $this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . '&sort=o.purchase_order_id' . $url, 'SSL');
        
        $data['sort_date_deliver'] = $this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . '&sort=o.date_deliver' . $url, 'SSL');

        $url = '';

        if (isset($this->request->get['filter_supplier_type'])) {
            $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
        }
        if (isset($this->request->get['filter_order_type'])) {
            $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
        }
        if (isset($this->request->get['filter_purchase_order_id'])) {
            $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
        }
        if (isset($this->request->get['filter_purchase_person_id'])) {
            $url .= '&filter_purchase_person_id=' . $this->request->get['filter_purchase_person_id'];
        }
        if (isset($this->request->get['filter_date_deliver'])) {
            $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
        }
        if (isset($this->request->get['filter_date_deliver_end'])) {
            $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_order_checkout_status_id'])) {
            $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
        }
        if (isset($this->request->get['filter_warehouse_id_global'])) {
            $url .= '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_supplier_type'] = $filter_supplier_type;
        $data['filter_order_type'] = $filter_order_type;
        $data['filter_purchase_order_id'] = $filter_purchase_order_id;
        $data['filter_purchase_person_id'] = $filter_purchase_person_id;
        $data['filter_date_deliver'] = $filter_date_deliver;
        $data['filter_date_deliver_end'] = $filter_date_deliver_end;
        $data['filter_order_status_id'] = $filter_order_status_id;
        $data['filter_order_checkout_status_id'] = $filter_order_checkout_status_id;
        

        
        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();


        $data['order_types'] = array(array("order_type_id"=>1,"name"=>"采购单"),array("order_type_id"=>2,"name"=>"退货单"), array("order_type_id"=>3,"name"=>"调整单"));
        $data['supplier_types'] = $this->model_purchase_pre_purchase->getSupplierTypes();
        $data['purchase_person'] = $this->model_station_station->getPurchasePerson();
        
        //load station
        $data['order_stations'] = array(1=>"生鲜",2=>"快销");
        $data['product_statuses'] = array(0=>"停用",1=>"启用");

        
        $data['order_statuses'] = $this->model_purchase_pre_purchase->getStatuses();
        $data['order_checkout_statuses'] = array(array("order_checkout_status_id"=>1,"name"=>"未支付"),array("order_checkout_status_id"=>2,"name"=>"已支付"));
        $data['checkout_table'] = $this->url->link('purchase/pre_purchase/checkout_table', 'token=' . $this->session->data['token'], 'SSL');
        $data['checkout_excel'] = $this->url->link('purchase/pre_purchase/checkout_excel', 'token=' . $this->session->data['token'], 'SSL');

         $data['user_group_id'] = $this->user->user_group_id;

        //TODO, try better way to verify user permission, global method
        $data['modifyPermission'] = false;
        if ($this->user->hasPermission('modify', 'purchase/pre_purchase')) {
            $data['modifyPermission'] = true;
        }

        $this->response->setOutput($this->load->view('purchase/pre_purchase_list.tpl', $data));
    }

    public function checkout_table() {
        $this->load->language('sale/order');
    
        $data['title'] = $this->language->get('text_shipping');
    
        if ($this->request->server['HTTPS']) {
            $data['base'] = HTTPS_SERVER;
        } else {
            $data['base'] = HTTP_SERVER;
        }

        $data['direction'] = $this->language->get('direction');
        $data['lang'] = $this->language->get('code');

        $data['text_shipping'] = $this->language->get('text_shipping');
        $data['text_picklist'] = $this->language->get('text_picklist');
        $data['text_order_detail'] = $this->language->get('text_order_detail');
        $data['text_order_id'] = $this->language->get('text_order_id');
        $data['text_invoice_no'] = $this->language->get('text_invoice_no');
        $data['text_invoice_date'] = $this->language->get('text_invoice_date');
        $data['text_date_added'] = $this->language->get('text_date_added');
        $data['text_telephone'] = $this->language->get('text_telephone');
        $data['text_fax'] = $this->language->get('text_fax');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_website'] = $this->language->get('text_website');
        $data['text_contact'] = $this->language->get('text_contact');
        $data['text_from'] = $this->language->get('text_from');
        $data['text_to'] = $this->language->get('text_to');
        $data['text_shipping_method'] = $this->language->get('text_shipping_method');
        $data['text_sku'] = $this->language->get('text_sku');
        $data['text_upc'] = $this->language->get('text_upc');
        $data['text_ean'] = $this->language->get('text_ean');
        $data['text_jan'] = $this->language->get('text_jan');
        $data['text_isbn'] = $this->language->get('text_isbn');
        $data['text_mpn'] = $this->language->get('text_mpn');

        $data['column_location'] = $this->language->get('column_location');
        $data['column_reference'] = $this->language->get('column_reference');
        $data['column_product'] = $this->language->get('column_product');
        $data['column_weight'] = $this->language->get('column_weight');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_comment'] = $this->language->get('column_comment');

        $this->load->model('purchase/pre_purchase');


        $data['orders'] = array();

        $orders = array();

        if (isset($this->request->post['selected'])) {
            $orders = $this->request->post['selected'];
        } elseif (isset($this->request->get['order_id'])) {
            $orders[] = $this->request->get['order_id'];
        }
        
        $all_order_product_weight_inv_arr = array();
        foreach ($orders as $order_id) {
            $order_info = $this->model_purchase_pre_purchase->getOrder($order_id);
            
            // Make sure there is a shipping method
            if ($order_info) {
                
                $order_info['order_return'] = $this->model_purchase_pre_purchase->getOrderReturn($order_id);
               
                $order_info['order_return'] = $order_info['order_return']['sum_return'] ? $order_info['order_return']['sum_return'] : 0;
                
                $order_info['get_product'] = $this->model_purchase_pre_purchase->getOrderGetProductInfo($order_id);
                
                $order_info['get_product_date'] = $this->model_purchase_pre_purchase->getOrderGetProductDate($order_id);

                $data['orders'][] = $order_info;
            }
        }

        //var_dump($order_info['orders']);
        $data['supplier_orders'] = array();
        if(!empty($data['orders'])){
            foreach($data['orders'] as $k => $v){
                $data['supplier_orders'][$v['supplier_type'] . "_" . $v['checkout_usercard']]['total'] = isset($data['supplier_orders'][$v['supplier_type'] . "_" . $v['checkout_usercard']]['total']) ? ($data['supplier_orders'][$v['supplier_type'] . "_" . $v['checkout_usercard']]['total']) : 0;
                $v['get_total'] = 0;
                
                foreach($v['products'] as $k1 => $v1){
                    $v['products'][$v1['product_id']] = $v1;
                    unset($v['products'][$k1]);
                }
                foreach($v['get_product'] as $k2 => $v2){
                    $v['get_total'] += $v2 * ($v['products'][$k2]['price'] * $v['products'][$k2]['supplier_quantity'] / $v['products'][$k2]['quantity'] );
                }

                $v['get_total'] = round($v['get_total'],4);

                $data['supplier_orders'][$v['supplier_type'] . "_" . $v['checkout_usercard']]['order'][] = $v; 
                
                if($v['checkout_type_id'] == 3 || $v['checkout_type_id'] == 1){
                    $data['supplier_orders'][$v['supplier_type'] . "_" . $v['checkout_usercard']]['total'] += ($v['order_total'] - $v['use_credits_total'] > 0 ? $v['order_total'] - $v['use_credits_total'] : 0);
                }
                else{
                    $data['supplier_orders'][$v['supplier_type'] . "_" . $v['checkout_usercard']]['total'] += ($v['get_total'] - $v['use_credits_total'] - $v['quehuo_credits'] - $v['order_return'] > 0 ? $v['get_total'] - $v['use_credits_total'] - $v['quehuo_credits'] - $v['order_return'] : 0);
                }

            }
        }

        if(!empty($data['supplier_orders'])){
            foreach($data['supplier_orders'] as $sk => $sv){
                $data['supplier_orders'][$sk]['total'] = sprintf("%.2f", $sv['total']);
                $data['supplier_orders'][$sk]['d_total'] = $this->num_to_rmb(sprintf("%.2f", $sv['total']));
            }
        }
        
        $this->response->setOutput($this->load->view('purchase/pre_purchase_checkout_frame.tpl', $data));
    }

    public function checkout_excel() {
        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();

        $this->load->model('purchase/pre_purchase');

        $data['orders'] = array();

        $orders = array();
        $order_info = array();
        $order_products_info = array();
        if (isset($this->request->post['selected'])) {
            $orders = $this->request->post['selected'];
        } else {
            echo "请选择一个订单编号！";
            die;
        }

        foreach($orders as $order_id){
            $order_products = $this->model_purchase_pre_purchase->getPurchaseOrderProducts($order_id);
            if($order_products){
                $order_products_info['products'] = $order_products;
            }

            $order_info = $this->model_purchase_pre_purchase->getPurchaseOrderInfo($order_id);
            if($order_info){
                $order_products_info['suplier'] = $order_info;
            }
        }

        $order_total = 0.00;
        $sum = 0;
        foreach($order_products_info['products'] as $value){
            $order_total += $value['p_price_total'];
            $sum += $value['quantity'];
        }

        //设置excel文件属性
        $objPHPExcel->getProperties()->setCreator("ctos")
            ->setLastModifiedBy("ctos")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        // 设置单元格宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);

        // 设置行高度
        $objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);


        // 设置水平居中
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // 字体和样式
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('A8:H8')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);

        $objPHPExcel->getActiveSheet()->getStyle('A8:H8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A8:H8')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //  合并
        $objPHPExcel->getActiveSheet()->mergeCells('A1:H2');
        $objPHPExcel->getActiveSheet()->mergeCells('C4:H4');
        $objPHPExcel->getActiveSheet()->mergeCells('C5:H5');

        // 表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1','鲜世纪订单')
            ->setCellValue('A3','订单编号')
            //->setCellValue('B3','xsj'.str_replace(array("-"),"",$order_products_info['suplier'][0]['order_data']))
            ->setCellValue('B3',$order_id)
            ->setCellValue('A4','订单日期')
            ->setCellValue('B4',$order_products_info['suplier'][0]['order_data'])
            ->setCellValue('A5','到货日期')
            ->setCellValue('B5',$order_products_info['suplier'][0]['real_receive'])
            ->setCellValue('A6','供应商编码')
            ->setCellValue('B6',$order_products_info['suplier'][0]['supplier_id'].' ')
            ->setCellValue('A7','供应商名称')
            ->setCellValue('B7',$order_products_info['suplier'][0]['name'])
            ->setCellValue('C4',$order_products_info['suplier'][0]['adderss'])
            ->setCellValue('C5',$order_products_info['suplier'][0]['station_admin'])
            ->setCellValue('A8', '商品编码')
            ->setCellValue('B8', '商品名称')
            ->setCellValue('C8', '商品条码')
            ->setCellValue('D8', '供应商规格')
            ->setCellValue('E8', '单价')
            ->setCellValue('F8', '单位')
            ->setCellValue('G8', '数量')
            ->setCellValue('G3', '货币')
            ->setCellValue('H3', '人名币')
            ->setCellValue('H8', '金额');
        for ($k = 1; $k < 8; $k++) {
//            $objPHPExcel->getActiveSheet()->getStyle('A' . ($k) . ':H' . ($k))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A' . ($k) . ':H' . ($k))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getRowDimension($k)->setRowHeight(16);
        }

        // 内容
        for ($i = 0, $len = count($order_products_info['products']); $i < $len; $i++) {
            $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 9), $order_products_info['products'][$i]['product_id'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 9), $order_products_info['products'][$i]['name']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 9), $order_products_info['products'][$i]['sku']);

            $objPHPExcel->getActiveSheet(0)->setCellValue('D' . ($i + 9), $order_products_info['products'][$i]['supplier_unit_size']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 9), $order_products_info['products'][$i]['price']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 9), $order_products_info['products'][$i]['title']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 9), $order_products_info['products'][$i]['quantity']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 9), $order_products_info['products'][$i]['p_price_total']);

            $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 9) . ':H' . ($i + 9))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 9) . ':H' . ($i + 9))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getRowDimension($i + 9)->setRowHeight(16);
        }

        $objPHPExcel->getActiveSheet()->mergeCells('A' . (count($order_products_info['products']) + 9) . ':E' . (count($order_products_info['products']) + 9));

        $objPHPExcel->getActiveSheet(0)->setCellValue('H' . (count($order_products_info['products']) + 9),round($order_total,2));
        $objPHPExcel->getActiveSheet(0)->setCellValue('G' . (count($order_products_info['products']) + 9),$sum);
        $objPHPExcel->getActiveSheet(0)->setCellValue('F' . (count($order_products_info['products']) + 9),'合计');

        //往下添加三行合并放置备注信息
        $objPHPExcel->getActiveSheet()->getStyle('A' . (count($order_products_info['products']) + 9) . ':H' . (count($order_products_info['products']) + 12))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A' . (count($order_products_info['products']) + 9) . ':H' . (count($order_products_info['products']) + 12))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getRowDimension($i + 9)->setRowHeight(16);

        $objPHPExcel->getActiveSheet()->mergeCells('A' . (count($order_products_info['products']) + 10) . ':A' . (count($order_products_info['products']) + 12));
        $objPHPExcel->getActiveSheet()->mergeCells('B' . (count($order_products_info['products']) + 10) . ':H' . (count($order_products_info['products']) + 10));
        $objPHPExcel->getActiveSheet()->mergeCells('B' . (count($order_products_info['products']) + 11) . ':H' . (count($order_products_info['products']) + 11));
        $objPHPExcel->getActiveSheet()->mergeCells('B' . (count($order_products_info['products']) + 12) . ':H' . (count($order_products_info['products']) + 12));

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . (count($order_products_info['products']) + 10),'备注')
            ->setCellValue('B' . (count($order_products_info['products']) + 10),'1.收货时间:周一-周五：8:00-20:00;周六/周日9:00-17:00')
            ->setCellValue('B' . (count($order_products_info['products']) + 11),'2.货物日期不超过保质期的1/4,否则务必提前说明')
            ->setCellValue('B' . (count($order_products_info['products']) + 12),'3.发票等重要票据请勿跟货至仓库')
        ;

        // 命名sheet
        $objPHPExcel->getActiveSheet()->setTitle('供应商');
        //工作sheet
        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="鲜世纪采购订单_' . $date . '_'.$order_id.'_' . $userName . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }

    
    public function update_order() {
            $json = array();

            $this->load->model('purchase/pre_purchase');

            $order_id = $_POST['order_id'];
            $order_ope = $_POST['order_ope'];

            $return = $this->model_purchase_pre_purchase->editOrderStatus($order_id,$order_ope);
            if(!$return){
                $json['return_msg'] = "修改失败，请检查订单支付状态";
                $json['success']  = "修改失败";
            }
            else{
                $json['return_msg'] = "修改成功";
                $json['success'] = "修改成功";
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
    }

    
    
    public function getForm($data = array()) {
        $this->load->model('purchase/pre_purchase');
        
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['order_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['text_product'] = $this->language->get('text_product');
        $data['text_voucher'] = $this->language->get('text_voucher');
        $data['text_order'] = $this->language->get('text_order');

        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['entry_fax'] = $this->language->get('entry_fax');
        $data['entry_comment'] = $this->language->get('entry_comment');
        $data['entry_affiliate'] = $this->language->get('entry_affiliate');
        $data['entry_address'] = $this->language->get('entry_address');
        $data['entry_company'] = $this->language->get('entry_company');
        $data['entry_address_1'] = $this->language->get('entry_address_1');
        $data['entry_address_2'] = $this->language->get('entry_address_2');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_postcode'] = $this->language->get('entry_postcode');
        $data['entry_zone'] = $this->language->get('entry_zone');
        $data['entry_zone_code'] = $this->language->get('entry_zone_code');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_product'] = $this->language->get('entry_product');
        $data['entry_option'] = $this->language->get('entry_option');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_to_name'] = $this->language->get('entry_to_name');
        $data['entry_to_email'] = $this->language->get('entry_to_email');
        $data['entry_from_name'] = $this->language->get('entry_from_name');
        $data['entry_from_email'] = $this->language->get('entry_from_email');
        $data['entry_theme'] = $this->language->get('entry_theme');
        $data['entry_message'] = $this->language->get('entry_message');
        $data['entry_amount'] = $this->language->get('entry_amount');
        $data['entry_shipping_method'] = $this->language->get('entry_shipping_method');
        $data['entry_payment_method'] = $this->language->get('entry_payment_method');
        $data['entry_coupon'] = $this->language->get('entry_coupon');
        $data['entry_voucher'] = $this->language->get('entry_voucher');
        $data['entry_reward'] = $this->language->get('entry_reward');
        $data['entry_order_status'] = $this->language->get('entry_order_status');

        $data['column_product'] = $this->language->get('column_product');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');
        $data['button_product_add'] = $this->language->get('button_product_add');
        $data['button_voucher_add'] = $this->language->get('button_voucher_add');

        $data['button_payment'] = $this->language->get('button_payment');
        $data['button_shipping'] = $this->language->get('button_shipping');
        $data['button_coupon'] = $this->language->get('button_coupon');
        $data['button_voucher'] = $this->language->get('button_voucher');
        $data['button_reward'] = $this->language->get('button_reward');
        $data['button_upload'] = $this->language->get('button_upload');
        $data['button_remove'] = $this->language->get('button_remove');

        $data['tab_order'] = $this->language->get('tab_order');
        $data['tab_customer'] = $this->language->get('tab_customer');
        $data['tab_payment'] = $this->language->get('tab_payment');
        $data['tab_shipping'] = $this->language->get('tab_shipping');
        $data['tab_product'] = $this->language->get('tab_product');
        $data['tab_voucher'] = $this->language->get('tab_voucher');
        $data['tab_total'] = $this->language->get('tab_total');

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $url = '';

        
        
        if (isset($this->request->get['filter_supplier_type'])) {
            $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
        }
        if (isset($this->request->get['filter_order_type'])) {
            $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
        }
        if (isset($this->request->get['filter_purchase_order_id'])) {
                    $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
                }

        if (isset($this->request->get['filter_date_deliver'])) {
            $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
        }
        if (isset($this->request->get['filter_date_deliver_end'])) {
                    $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
                }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_order_checkout_status_id'])) {
                    $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
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
            'text' => "采购单",
            'href' => $this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['cancel'] = $this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['order_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $order_info = $this->model_purchase_pre_purchase->getOrder($this->request->get['order_id']);
        }

        if (!empty($order_info)) {
            $data['order_id'] = $this->request->get['order_id'];
            $data['store_id'] = $order_info['store_id'];

            $data['customer'] = $order_info['customer'];
            $data['customer_id'] = $order_info['customer_id'];
            $data['customer_group_id'] = $order_info['customer_group_id'];
            $data['firstname'] = $order_info['firstname'];
            $data['lastname'] = $order_info['lastname'];
            $data['email'] = $order_info['email'];
            $data['telephone'] = $order_info['telephone'];
            $data['fax'] = $order_info['fax'];
            $data['account_custom_field'] = $order_info['custom_field'];

            $this->load->model('sale/customer');

            $data['addresses'] = $this->model_sale_customer->getAddresses($order_info['customer_id']);

            $data['payment_firstname'] = $order_info['payment_firstname'];
            $data['payment_lastname'] = $order_info['payment_lastname'];
            $data['payment_company'] = $order_info['payment_company'];
            $data['payment_address_1'] = $order_info['payment_address_1'];
            $data['payment_address_2'] = $order_info['payment_address_2'];
            $data['payment_city'] = $order_info['payment_city'];
            $data['payment_postcode'] = $order_info['payment_postcode'];
            $data['payment_country_id'] = $order_info['payment_country_id'];
            $data['payment_zone_id'] = $order_info['payment_zone_id'];
            $data['payment_custom_field'] = $order_info['payment_custom_field'];
            $data['payment_method'] = $order_info['payment_method'];
            $data['payment_code'] = $order_info['payment_code'];

            $data['shipping_firstname'] = $order_info['shipping_firstname'];
            $data['shipping_lastname'] = $order_info['shipping_lastname'];
            $data['shipping_company'] = $order_info['shipping_company'];
            $data['shipping_address_1'] = $order_info['shipping_address_1'];
            $data['shipping_address_2'] = $order_info['shipping_address_2'];
            $data['shipping_city'] = $order_info['shipping_city'];
            $data['shipping_postcode'] = $order_info['shipping_postcode'];
            $data['shipping_country_id'] = $order_info['shipping_country_id'];
            $data['shipping_zone_id'] = $order_info['shipping_zone_id'];
            $data['shipping_custom_field'] = $order_info['shipping_custom_field'];
            $data['shipping_method'] = $order_info['shipping_method'];
            $data['shipping_code'] = $order_info['shipping_code'];

            // Add products to the API
            $data['products'] = array();

            $products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);

            foreach ($products as $product) {
                $data['order_products'][] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']),
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total' => $product['total'],
                    'reward' => $product['reward']
                );
            }

            // Add vouchers to the API
            $data['order_vouchers'] = $this->model_sale_order->getOrderVouchers($this->request->get['order_id']);

            $data['coupon'] = '';
            $data['voucher'] = '';
            $data['reward'] = '';

            $data['order_totals'] = array();

            $order_totals = $this->model_sale_order->getOrderTotals($this->request->get['order_id']);

            foreach ($order_totals as $order_total) {
                // If coupon, voucher or reward points
                $start = strpos($order_total['title'], '(') + 1;
                $end = strrpos($order_total['title'], ')');

                if ($start && $end) {
                    if ($order_total['code'] == 'coupon') {
                        $data['coupon'] = substr($order_total['title'], $start, $end - $start);
                    }

                    if ($order_total['code'] == 'voucher') {
                        $data['voucher'] = substr($order_total['title'], $start, $end - $start);
                    }

                    if ($order_total['code'] == 'reward') {
                        $data['reward'] = substr($order_total['title'], $start, $end - $start);
                    }
                }
            }

            $data['order_status_id'] = $order_info['order_status_id'];
            $data['comment'] = $order_info['comment'];
            $data['affiliate_id'] = $order_info['affiliate_id'];
            $data['affiliate'] = $order_info['affiliate_firstname'] . ' ' . $order_info['affiliate_lastname'];
        } else {
            $data['order_id'] = 0;
            //$data['station_id'] = '';
            //$data['date_deliver'] = '';
            //$data['supplier_type'] = '';
            $data['products'] = '';
        }
        
        $today_date_time =  time() + 8 * 3600;
        $data['pre_three_day_before'] = isset($_POST['date_before']) ? $_POST['date_before'] : date("Y-m-d", $today_date_time - 3 * 24 * 3600);
        $data['pre_three_day_end'] = isset($_POST['date_end']) ? $_POST['date_end'] : date("Y-m-d", $today_date_time - 1 * 24 * 3600);
        $data['date_deliver'] = isset($_POST['date_deliver']) ? $_POST['date_deliver'] : (isset($data['date_deliver']) ? $data['date_deliver'] : '');
        
        $data['date_no_in'] = isset($_POST['date_no_in']) ? $_POST['date_no_in'] : '';
        $data['station_id'] = isset($_POST['station_id']) ? $_POST['station_id'] : (isset($data['station_id']) ? $data['station_id'] : '');
        $data['status_id'] = isset($_POST['status_id']) ? $_POST['status_id'] : '*';
        $data['s_quantity_status_id'] = isset($_POST['s_quantity_status_id']) ? $_POST['s_quantity_status_id'] : '1';
        
        $data['supplier_type'] = isset($_POST['supplier_type']) ? $_POST['supplier_type'] : (isset($data['supplier_type']) ? $data['supplier_type'] : '');
        

        
        $data['supplier_types'] = $this->model_purchase_pre_purchase->getSupplierTypes();
        
        

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();


        $data['action_select'] = $this->url->link('purchase/pre_purchase/add', 'token=' . $this->session->data['token'] , 'SSL');
        $data['action_adjust'] = $this->url->link('purchase/pre_purchase/add', 'token=' . $this->session->data['token'] , 'SSL');
        
       //load station
        $data['order_stations'] = array(1=>"生鲜",2=>"快销");
        $data['product_statuses'] = array(1=>"启用",0=>"停用");
        $this->response->setOutput($this->load->view('purchase/pre_purchase_form.tpl', $data));
    }

    public function getForm2($data = array()) {
        $this->load->model('purchase/pre_purchase');
        
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['order_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['text_product'] = $this->language->get('text_product');
        $data['text_voucher'] = $this->language->get('text_voucher');
        $data['text_order'] = $this->language->get('text_order');

        $data['entry_store'] = $this->language->get('entry_store');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $data['entry_firstname'] = $this->language->get('entry_firstname');
        $data['entry_lastname'] = $this->language->get('entry_lastname');
        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_telephone'] = $this->language->get('entry_telephone');
        $data['entry_fax'] = $this->language->get('entry_fax');
        $data['entry_comment'] = $this->language->get('entry_comment');
        $data['entry_affiliate'] = $this->language->get('entry_affiliate');
        $data['entry_address'] = $this->language->get('entry_address');
        $data['entry_company'] = $this->language->get('entry_company');
        $data['entry_address_1'] = $this->language->get('entry_address_1');
        $data['entry_address_2'] = $this->language->get('entry_address_2');
        $data['entry_city'] = $this->language->get('entry_city');
        $data['entry_postcode'] = $this->language->get('entry_postcode');
        $data['entry_zone'] = $this->language->get('entry_zone');
        $data['entry_zone_code'] = $this->language->get('entry_zone_code');
        $data['entry_country'] = $this->language->get('entry_country');
        $data['entry_product'] = $this->language->get('entry_product');
        $data['entry_option'] = $this->language->get('entry_option');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_to_name'] = $this->language->get('entry_to_name');
        $data['entry_to_email'] = $this->language->get('entry_to_email');
        $data['entry_from_name'] = $this->language->get('entry_from_name');
        $data['entry_from_email'] = $this->language->get('entry_from_email');
        $data['entry_theme'] = $this->language->get('entry_theme');
        $data['entry_message'] = $this->language->get('entry_message');
        $data['entry_amount'] = $this->language->get('entry_amount');
        $data['entry_shipping_method'] = $this->language->get('entry_shipping_method');
        $data['entry_payment_method'] = $this->language->get('entry_payment_method');
        $data['entry_coupon'] = $this->language->get('entry_coupon');
        $data['entry_voucher'] = $this->language->get('entry_voucher');
        $data['entry_reward'] = $this->language->get('entry_reward');
        $data['entry_order_status'] = $this->language->get('entry_order_status');

        $data['column_product'] = $this->language->get('column_product');
        $data['column_model'] = $this->language->get('column_model');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_total'] = $this->language->get('column_total');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');
        $data['button_product_add'] = $this->language->get('button_product_add');
        $data['button_voucher_add'] = $this->language->get('button_voucher_add');

        $data['button_payment'] = $this->language->get('button_payment');
        $data['button_shipping'] = $this->language->get('button_shipping');
        $data['button_coupon'] = $this->language->get('button_coupon');
        $data['button_voucher'] = $this->language->get('button_voucher');
        $data['button_reward'] = $this->language->get('button_reward');
        $data['button_upload'] = $this->language->get('button_upload');
        $data['button_remove'] = $this->language->get('button_remove');

        $data['tab_order'] = $this->language->get('tab_order');
        $data['tab_customer'] = $this->language->get('tab_customer');
        $data['tab_payment'] = $this->language->get('tab_payment');
        $data['tab_shipping'] = $this->language->get('tab_shipping');
        $data['tab_product'] = $this->language->get('tab_product');
        $data['tab_voucher'] = $this->language->get('tab_voucher');
        $data['tab_total'] = $this->language->get('tab_total');

        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $url = '';

        
        
        if (isset($this->request->get['filter_supplier_type'])) {
            $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
        }
        if (isset($this->request->get['filter_order_type'])) {
            $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
        }
        if (isset($this->request->get['filter_purchase_order_id'])) {
                    $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
                }

        if (isset($this->request->get['filter_date_deliver'])) {
            $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
        }
        if (isset($this->request->get['filter_date_deliver_end'])) {
                    $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
                }
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
        if (isset($this->request->get['filter_order_checkout_status_id'])) {
                    $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
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
            'text' => "采购单",
            'href' => $this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['cancel'] = $this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url, 'SSL');

        if (isset($this->request->get['order_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $order_info = $this->model_purchase_pre_purchase->getOrder($this->request->get['order_id']);
        }

        if (!empty($order_info)) {
            $data['order_id'] = $this->request->get['order_id'];
            $data['store_id'] = $order_info['store_id'];

            $data['customer'] = $order_info['customer'];
            $data['customer_id'] = $order_info['customer_id'];
            $data['customer_group_id'] = $order_info['customer_group_id'];
            $data['firstname'] = $order_info['firstname'];
            $data['lastname'] = $order_info['lastname'];
            $data['email'] = $order_info['email'];
            $data['telephone'] = $order_info['telephone'];
            $data['fax'] = $order_info['fax'];
            $data['account_custom_field'] = $order_info['custom_field'];

            $this->load->model('sale/customer');

            $data['addresses'] = $this->model_sale_customer->getAddresses($order_info['customer_id']);

            $data['payment_firstname'] = $order_info['payment_firstname'];
            $data['payment_lastname'] = $order_info['payment_lastname'];
            $data['payment_company'] = $order_info['payment_company'];
            $data['payment_address_1'] = $order_info['payment_address_1'];
            $data['payment_address_2'] = $order_info['payment_address_2'];
            $data['payment_city'] = $order_info['payment_city'];
            $data['payment_postcode'] = $order_info['payment_postcode'];
            $data['payment_country_id'] = $order_info['payment_country_id'];
            $data['payment_zone_id'] = $order_info['payment_zone_id'];
            $data['payment_custom_field'] = $order_info['payment_custom_field'];
            $data['payment_method'] = $order_info['payment_method'];
            $data['payment_code'] = $order_info['payment_code'];

            $data['shipping_firstname'] = $order_info['shipping_firstname'];
            $data['shipping_lastname'] = $order_info['shipping_lastname'];
            $data['shipping_company'] = $order_info['shipping_company'];
            $data['shipping_address_1'] = $order_info['shipping_address_1'];
            $data['shipping_address_2'] = $order_info['shipping_address_2'];
            $data['shipping_city'] = $order_info['shipping_city'];
            $data['shipping_postcode'] = $order_info['shipping_postcode'];
            $data['shipping_country_id'] = $order_info['shipping_country_id'];
            $data['shipping_zone_id'] = $order_info['shipping_zone_id'];
            $data['shipping_custom_field'] = $order_info['shipping_custom_field'];
            $data['shipping_method'] = $order_info['shipping_method'];
            $data['shipping_code'] = $order_info['shipping_code'];

            // Add products to the API
            $data['products'] = array();

            $products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);

            foreach ($products as $product) {
                $data['order_products'][] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']),
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total' => $product['total'],
                    'reward' => $product['reward']
                );
            }

            // Add vouchers to the API
            $data['order_vouchers'] = $this->model_sale_order->getOrderVouchers($this->request->get['order_id']);

            $data['coupon'] = '';
            $data['voucher'] = '';
            $data['reward'] = '';

            $data['order_totals'] = array();

            $order_totals = $this->model_sale_order->getOrderTotals($this->request->get['order_id']);

            foreach ($order_totals as $order_total) {
                // If coupon, voucher or reward points
                $start = strpos($order_total['title'], '(') + 1;
                $end = strrpos($order_total['title'], ')');

                if ($start && $end) {
                    if ($order_total['code'] == 'coupon') {
                        $data['coupon'] = substr($order_total['title'], $start, $end - $start);
                    }

                    if ($order_total['code'] == 'voucher') {
                        $data['voucher'] = substr($order_total['title'], $start, $end - $start);
                    }

                    if ($order_total['code'] == 'reward') {
                        $data['reward'] = substr($order_total['title'], $start, $end - $start);
                    }
                }
            }

            $data['order_status_id'] = $order_info['order_status_id'];
            $data['comment'] = $order_info['comment'];
            $data['affiliate_id'] = $order_info['affiliate_id'];
            $data['affiliate'] = $order_info['affiliate_firstname'] . ' ' . $order_info['affiliate_lastname'];
        } else {
            $data['order_id'] = 0;
            //$data['station_id'] = '';
            //$data['date_deliver'] = '';
            //$data['supplier_type'] = '';
            $data['products'] = '';
        }
        
        $today_date_time =  time() + 8 * 3600;
        $data['pre_three_day_before'] = isset($_POST['date_before']) ? $_POST['date_before'] : date("Y-m-d", $today_date_time - 3 * 24 * 3600);
        $data['pre_three_day_end'] = isset($_POST['date_end']) ? $_POST['date_end'] : date("Y-m-d", $today_date_time - 1 * 24 * 3600);
        $data['date_deliver'] = isset($_POST['date_deliver']) ? $_POST['date_deliver'] : (isset($data['date_deliver']) ? $data['date_deliver'] : '');
        
        $data['date_no_in'] = isset($_POST['date_no_in']) ? $_POST['date_no_in'] : '';
        $data['station_id'] = isset($_POST['station_id']) ? $_POST['station_id'] : (isset($data['station_id']) ? $data['station_id'] : '');
        $data['status_id'] = isset($_POST['status_id']) ? $_POST['status_id'] : '*';
        $data['s_quantity_status_id'] = isset($_POST['s_quantity_status_id']) ? $_POST['s_quantity_status_id'] : '1';
        
        $data['supplier_type'] = isset($_POST['supplier_type']) ? $_POST['supplier_type'] : (isset($data['supplier_type']) ? $data['supplier_type'] : '');
        

        
        $data['supplier_types'] = $this->model_purchase_pre_purchase->getSupplierTypes();
        
        

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['action_select'] = $this->url->link('purchase/pre_purchase/add', 'token=' . $this->session->data['token'] , 'SSL');
        $data['action_adjust'] = $this->url->link('purchase/pre_purchase/add', 'token=' . $this->session->data['token'] , 'SSL');
        
       //load station
        $data['order_stations'] = array(1=>"生鲜",2=>"快销");
        $data['product_statuses'] = array(1=>"启用",0=>"停用");
        
        $this->response->setOutput($this->load->view('purchase/pre_purchase_form_return.tpl', $data));
    }

    public function info() {
        $this->load->model('purchase/pre_purchase');
        $this->load->model('purchase/pre_purchase_adjust');

        if (isset($this->request->get['purchase_order_id'])) {
            $order_id = $this->request->get['purchase_order_id'];
        } else {
            $order_id = 0;
        }

        $order_info = $this->model_purchase_pre_purchase->getOrder($order_id);
        
        $order_get_product_info = $this->model_purchase_pre_purchase->getOrderGetProductInfo($order_id);
        
        if ($order_info) {
            $this->load->language('sale/order');

            $this->document->setTitle("采购单");

            $data['heading_title'] = "采购单";

            $data['text_order_id'] = $this->language->get('text_order_id');
            $data['text_invoice_no'] = $this->language->get('text_invoice_no');
            $data['text_invoice_date'] = $this->language->get('text_invoice_date');
            $data['text_store_name'] = $this->language->get('text_store_name');
            $data['text_store_url'] = $this->language->get('text_store_url');
            $data['text_customer'] = $this->language->get('text_customer');
            $data['text_customer_group'] = $this->language->get('text_customer_group');
            $data['text_email'] = $this->language->get('text_email');
            $data['text_telephone'] = $this->language->get('text_telephone');
            $data['text_fax'] = $this->language->get('text_fax');
            $data['text_total'] = $this->language->get('text_total');
            $data['text_reward'] = $this->language->get('text_reward');
            $data['text_order_status'] = $this->language->get('text_order_status');
            $data['text_comment'] = $this->language->get('text_comment');
            $data['text_affiliate'] = $this->language->get('text_affiliate');
            $data['text_commission'] = $this->language->get('text_commission');
            $data['text_ip'] = $this->language->get('text_ip');
            $data['text_forwarded_ip'] = $this->language->get('text_forwarded_ip');
            $data['text_user_agent'] = $this->language->get('text_user_agent');
            $data['text_accept_language'] = $this->language->get('text_accept_language');
            $data['text_date_added'] = $this->language->get('text_date_added');
            $data['text_date_modified'] = $this->language->get('text_date_modified');
            $data['text_firstname'] = $this->language->get('text_firstname');
            $data['text_lastname'] = $this->language->get('text_lastname');
            $data['text_company'] = $this->language->get('text_company');
            $data['text_address_1'] = $this->language->get('text_address_1');
            $data['text_address_2'] = $this->language->get('text_address_2');
            $data['text_city'] = $this->language->get('text_city');
            $data['text_postcode'] = $this->language->get('text_postcode');
            $data['text_zone'] = $this->language->get('text_zone');
            $data['text_zone_code'] = $this->language->get('text_zone_code');
            $data['text_country'] = $this->language->get('text_country');
            $data['text_shipping_method'] = $this->language->get('text_shipping_method');
            $data['text_payment_method'] = $this->language->get('text_payment_method');
            $data['text_history'] = $this->language->get('text_history');
            $data['text_country_match'] = $this->language->get('text_country_match');
            $data['text_country_code'] = $this->language->get('text_country_code');
            $data['text_high_risk_country'] = $this->language->get('text_high_risk_country');
            $data['text_distance'] = $this->language->get('text_distance');
            $data['text_ip_region'] = $this->language->get('text_ip_region');
            $data['text_ip_city'] = $this->language->get('text_ip_city');
            $data['text_ip_latitude'] = $this->language->get('text_ip_latitude');
            $data['text_ip_longitude'] = $this->language->get('text_ip_longitude');
            $data['text_ip_isp'] = $this->language->get('text_ip_isp');
            $data['text_ip_org'] = $this->language->get('text_ip_org');
            $data['text_ip_asnum'] = $this->language->get('text_ip_asnum');
            $data['text_ip_user_type'] = $this->language->get('text_ip_user_type');
            $data['text_ip_country_confidence'] = $this->language->get('text_ip_country_confidence');
            $data['text_ip_region_confidence'] = $this->language->get('text_ip_region_confidence');
            $data['text_ip_city_confidence'] = $this->language->get('text_ip_city_confidence');
            $data['text_ip_postal_confidence'] = $this->language->get('text_ip_postal_confidence');
            $data['text_ip_postal_code'] = $this->language->get('text_ip_postal_code');
            $data['text_ip_accuracy_radius'] = $this->language->get('text_ip_accuracy_radius');
            $data['text_ip_net_speed_cell'] = $this->language->get('text_ip_net_speed_cell');
            $data['text_ip_metro_code'] = $this->language->get('text_ip_metro_code');
            $data['text_ip_area_code'] = $this->language->get('text_ip_area_code');
            $data['text_ip_time_zone'] = $this->language->get('text_ip_time_zone');
            $data['text_ip_region_name'] = $this->language->get('text_ip_region_name');
            $data['text_ip_domain'] = $this->language->get('text_ip_domain');
            $data['text_ip_country_name'] = $this->language->get('text_ip_country_name');
            $data['text_ip_continent_code'] = $this->language->get('text_ip_continent_code');
            $data['text_ip_corporate_proxy'] = $this->language->get('text_ip_corporate_proxy');
            $data['text_anonymous_proxy'] = $this->language->get('text_anonymous_proxy');
            $data['text_proxy_score'] = $this->language->get('text_proxy_score');
            $data['text_is_trans_proxy'] = $this->language->get('text_is_trans_proxy');
            $data['text_free_mail'] = $this->language->get('text_free_mail');
            $data['text_carder_email'] = $this->language->get('text_carder_email');
            $data['text_high_risk_username'] = $this->language->get('text_high_risk_username');
            $data['text_high_risk_password'] = $this->language->get('text_high_risk_password');
            $data['text_bin_match'] = $this->language->get('text_bin_match');
            $data['text_bin_country'] = $this->language->get('text_bin_country');
            $data['text_bin_name_match'] = $this->language->get('text_bin_name_match');
            $data['text_bin_name'] = $this->language->get('text_bin_name');
            $data['text_bin_phone_match'] = $this->language->get('text_bin_phone_match');
            $data['text_bin_phone'] = $this->language->get('text_bin_phone');
            $data['text_customer_phone_in_billing_location'] = $this->language->get('text_customer_phone_in_billing_location');
            $data['text_ship_forward'] = $this->language->get('text_ship_forward');
            $data['text_city_postal_match'] = $this->language->get('text_city_postal_match');
            $data['text_ship_city_postal_match'] = $this->language->get('text_ship_city_postal_match');
            $data['text_score'] = $this->language->get('text_score');
            $data['text_explanation'] = $this->language->get('text_explanation');
            $data['text_risk_score'] = $this->language->get('text_risk_score');
            $data['text_queries_remaining'] = $this->language->get('text_queries_remaining');
            $data['text_maxmind_id'] = $this->language->get('text_maxmind_id');
            $data['text_error'] = $this->language->get('text_error');
            $data['text_loading'] = $this->language->get('text_loading');

            $data['help_country_match'] = $this->language->get('help_country_match');
            $data['help_country_code'] = $this->language->get('help_country_code');
            $data['help_high_risk_country'] = $this->language->get('help_high_risk_country');
            $data['help_distance'] = $this->language->get('help_distance');
            $data['help_ip_region'] = $this->language->get('help_ip_region');
            $data['help_ip_city'] = $this->language->get('help_ip_city');
            $data['help_ip_latitude'] = $this->language->get('help_ip_latitude');
            $data['help_ip_longitude'] = $this->language->get('help_ip_longitude');
            $data['help_ip_isp'] = $this->language->get('help_ip_isp');
            $data['help_ip_org'] = $this->language->get('help_ip_org');
            $data['help_ip_asnum'] = $this->language->get('help_ip_asnum');
            $data['help_ip_user_type'] = $this->language->get('help_ip_user_type');
            $data['help_ip_country_confidence'] = $this->language->get('help_ip_country_confidence');
            $data['help_ip_region_confidence'] = $this->language->get('help_ip_region_confidence');
            $data['help_ip_city_confidence'] = $this->language->get('help_ip_city_confidence');
            $data['help_ip_postal_confidence'] = $this->language->get('help_ip_postal_confidence');
            $data['help_ip_postal_code'] = $this->language->get('help_ip_postal_code');
            $data['help_ip_accuracy_radius'] = $this->language->get('help_ip_accuracy_radius');
            $data['help_ip_net_speed_cell'] = $this->language->get('help_ip_net_speed_cell');
            $data['help_ip_metro_code'] = $this->language->get('help_ip_metro_code');
            $data['help_ip_area_code'] = $this->language->get('help_ip_area_code');
            $data['help_ip_time_zone'] = $this->language->get('help_ip_time_zone');
            $data['help_ip_region_name'] = $this->language->get('help_ip_region_name');
            $data['help_ip_domain'] = $this->language->get('help_ip_domain');
            $data['help_ip_country_name'] = $this->language->get('help_ip_country_name');
            $data['help_ip_continent_code'] = $this->language->get('help_ip_continent_code');
            $data['help_ip_corporate_proxy'] = $this->language->get('help_ip_corporate_proxy');
            $data['help_anonymous_proxy'] = $this->language->get('help_anonymous_proxy');
            $data['help_proxy_score'] = $this->language->get('help_proxy_score');
            $data['help_is_trans_proxy'] = $this->language->get('help_is_trans_proxy');
            $data['help_free_mail'] = $this->language->get('help_free_mail');
            $data['help_carder_email'] = $this->language->get('help_carder_email');
            $data['help_high_risk_username'] = $this->language->get('help_high_risk_username');
            $data['help_high_risk_password'] = $this->language->get('help_high_risk_password');
            $data['help_bin_match'] = $this->language->get('help_bin_match');
            $data['help_bin_country'] = $this->language->get('help_bin_country');
            $data['help_bin_name_match'] = $this->language->get('help_bin_name_match');
            $data['help_bin_name'] = $this->language->get('help_bin_name');
            $data['help_bin_phone_match'] = $this->language->get('help_bin_phone_match');
            $data['help_bin_phone'] = $this->language->get('help_bin_phone');
            $data['help_customer_phone_in_billing_location'] = $this->language->get('help_customer_phone_in_billing_location');
            $data['help_ship_forward'] = $this->language->get('help_ship_forward');
            $data['help_city_postal_match'] = $this->language->get('help_city_postal_match');
            $data['help_ship_city_postal_match'] = $this->language->get('help_ship_city_postal_match');
            $data['help_score'] = $this->language->get('help_score');
            $data['help_explanation'] = $this->language->get('help_explanation');
            $data['help_risk_score'] = $this->language->get('help_risk_score');
            $data['help_queries_remaining'] = $this->language->get('help_queries_remaining');
            $data['help_maxmind_id'] = $this->language->get('help_maxmind_id');
            $data['help_error'] = $this->language->get('help_error');

            $data['column_product'] = $this->language->get('column_product');
            $data['column_model'] = $this->language->get('column_model');
            $data['column_quantity'] = $this->language->get('column_quantity');
            $data['column_price'] = $this->language->get('column_price');
            $data['column_total'] = $this->language->get('column_total');

            $data['entry_order_status'] = $this->language->get('entry_order_status');
            $data['entry_notify'] = $this->language->get('entry_notify');
            $data['entry_comment'] = $this->language->get('entry_comment');

            $data['button_invoice_print'] = $this->language->get('button_invoice_print');
            $data['button_shipping_print'] = $this->language->get('button_shipping_print');
            $data['button_edit'] = $this->language->get('button_edit');
            $data['button_cancel'] = $this->language->get('button_cancel');
            $data['button_generate'] = $this->language->get('button_generate');
            $data['button_reward_add'] = $this->language->get('button_reward_add');
            $data['button_reward_remove'] = $this->language->get('button_reward_remove');
            $data['button_commission_add'] = $this->language->get('button_commission_add');
            $data['button_commission_remove'] = $this->language->get('button_commission_remove');
            $data['button_history_add'] = $this->language->get('button_history_add');

            $data['tab_order'] = $this->language->get('tab_order');
            $data['tab_payment'] = $this->language->get('tab_payment');
            $data['tab_shipping'] = $this->language->get('tab_shipping');
            $data['tab_product'] = $this->language->get('tab_product');
            $data['tab_history'] = $this->language->get('tab_history');
            $data['tab_fraud'] = $this->language->get('tab_fraud');
            $data['tab_action'] = $this->language->get('tab_action');

            $data['token'] = $this->session->data['token'];

            $url = '';

            if (isset($this->request->get['filter_supplier_type'])) {
                $url .= '&filter_supplier_type=' . $this->request->get['filter_supplier_type'];
            }
        
            if (isset($this->request->get['filter_order_type'])) {
                $url .= '&filter_order_type=' . $this->request->get['filter_order_type'];
            }
            if (isset($this->request->get['filter_purchase_order_id'])) {
                $url .= '&filter_purchase_order_id=' . $this->request->get['filter_purchase_order_id'];
            }

            if (isset($this->request->get['filter_date_deliver'])) {
                $url .= '&filter_date_deliver=' . $this->request->get['filter_date_deliver'];
            }
            if (isset($this->request->get['filter_date_deliver_end'])) {
                $url .= '&filter_date_deliver_end=' . $this->request->get['filter_date_deliver_end'];
            }
            if (isset($this->request->get['filter_order_status_id'])) {
                $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            }
            if (isset($this->request->get['filter_order_checkout_status_id'])) {
                $url .= '&filter_order_checkout_status_id=' . $this->request->get['filter_order_checkout_status_id'];
            }
            if (isset($this->request->get['filter_warehouse_id_global'])) {
                $url .= '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'];
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
                'text' => "采购单",
                'href' => $this->url->link('purchase/pre_purchase', 'token=' . $this->session->data['token'] . $url, 'SSL')
            );

            $order_info['adjust_products']        = array();
            $order_info['finish_adjust_products'] = array();

            // 查询调整单
            $adjust_order = $this->model_purchase_pre_purchase_adjust->getAdjustOrderProductsByPurchaseOrder($order_id);
            if(!empty($adjust_order)){
                foreach($order_info['products'] as $value){
                    $product_name[$value['product_id']] = $value['name'];
                }

                foreach($adjust_order as &$val){
                    $val['name'] = !empty($product_name[$val['product_id']]) ? $product_name[$val['product_id']] : '';
                    if($val['status'] == 1){
                        $val['status_name']  = "已生效";
                        $order_info['finish_adjust_products'][] = $val['product_id'];
                    }else{
                        $val['status_name']  = "未生效";
                        $val['order_status'] == 1 && $val['status_name'] = "待审核";
                        $val['order_status'] == 3 && $val['status_name'] = "不通过";
                    }
                }
                $order_info['adjust_products'] = $adjust_order;
            }


            $data['order_id'] = $this->request->get['purchase_order_id'];

            $data = array_merge($data,$order_info);
            $data['order_get_product_info'] = $order_get_product_info;
            $this->load->model('tool/image');
            $data['thumb'] = $this->request->get['purchase_order_id'] && is_file(DIR_IMAGE . $order_info['image']) ? $this->model_tool_image->resize($order_info['image'], 100, 100) : $this->model_tool_image->resize('no_image.png', 100, 100) ;
           
           
           
           
           
           // 送货单多图
            $product_images = $this->request->get['purchase_order_id'] ? $this->model_purchase_pre_purchase->getOrderImages($this->request->get['purchase_order_id']) : array();
            
            $data['product_images'] = array();
            foreach ($product_images as $product_image) {
                    if (is_file(DIR_IMAGE . $product_image['image'])) {
                            $image = $product_image['image'];
                            $thumb = $product_image['image'];
                    } else {
                            $image = '';
                            $thumb = 'no_image.png';
                    }
                    
                    $data['product_images'][] = array(
                            'image'      => HTTP_CATALOG . "image/" . $image,
                            'thumb'      => HTTP_CATALOG . "image/" . $image,
                            'image_dir' => $image,
                            'image_title' => $product_image['image_title'],
                            'image_num' => $product_image['image_num']
                            //'thumb'      => $this->model_tool_image->resize($thumb, 100, 100)
                    );
            }
           
           
           $data['no_image_thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
           
           
           
           
           
           $data['action_image'] = $this->url->link('purchase/pre_purchase/edit', 'token=' . $this->session->data['token'] . '&purchase_order_id=' .$this->request->get['purchase_order_id'] , 'SSL');
           
            $data['order_stations'] = array(1=>"生鲜",2=>"快销");
            $data['product_statuses'] = array(0=>"停用",1=>"启用");
            $data['supplier_types'] = $this->model_purchase_pre_purchase->getSupplierTypes();
            
            $data['order_statuses'] = $this->model_purchase_pre_purchase->getStatuses();
            $data['order_info'] = $order_info;
        

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            //TODO, try better way to verify user permission, global method
            //Check if it was created by own user
            $data['modifyPermission'] = false;
            if ($this->user->hasPermission('modify', 'purchase/pre_purchase') && $this->user->getId() == $order_info['added_by']) {
                $data['modifyPermission'] = true;
            }

            $this->response->setOutput($this->load->view('purchase/pre_purchase_info.tpl', $data));
        } else {
            $this->load->language('error/not_found');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_not_found'] = $this->language->get('text_not_found');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('error/not_found', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('error/not_found.tpl', $data));
        }
    }


    protected function validate() {
        if (!$this->user->hasPermission('modify', 'purchase/pre_purchase')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    /**
*数字金额转换成中文大写金额的函数
*String Int  $num  要转换的小写数字或小写字符串
*return 大写字母
*小数位为两位
**/
function num_to_rmb($num){
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2); 
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
                return "金额太大，请检查";
        } 
        $i = 0;
        $c = "";
        while (1) {
                if ($i == 0) {
                        //获取最后一位数字
                        $n = substr($num, strlen($num)-1, 1);
                } else {
                        $n = $num % 10;
                }
                //每次将最后一位数字转化为中文
                $p1 = substr($c1, 3 * $n, 3);
                $p2 = substr($c2, 3 * $i, 3);
                if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                        $c = $p1 . $p2 . $c;
                } else {
                        $c = $p1 . $c;
                }
                $i = $i + 1;
                //去掉数字最后一位了
                $num = $num / 10;
                $num = (int)$num;
                //结束循环
                if ($num == 0) {
                        break;
                } 
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
                //utf8一个汉字相当3个字符
                $m = substr($c, $j, 6);
                //处理数字中很多0的情况,每次循环去掉一个汉字“零”
                if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                        $left = substr($c, 0, $j);
                        $right = substr($c, $j + 3);
                        $c = $left . $right;
                        $j = $j-3;
                        $slen = $slen-3;
                } 
                $j = $j + 3;
        } 
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c)-3, 3) == '零') {
                $c = substr($c, 0, strlen($c)-3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
                return "零元整";
        }else{
                return $c . "整";
        }
    }


    public function getPrePurchaseOrderProducts(){
        $this->load->model('purchase/pre_purchase');
        $json = $this->model_purchase_pre_purchase->getPrePurchaseOrderProducts($this->request->get['id']);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }


    public function update(){
        $this->load->model('purchase/pre_purchase');

        //检测采购单支付状态
        $returnData = array('return_code'=>'UNKNOWN', 'return_message'=>'未知错误，请联系管理员。', 'return_data'=>'');

        $paymentStatus = $this->model_purchase_pre_purchase->checkPrePurchaseOrderStatus($this->request->post['order_id']);
        if($paymentStatus['checkout_status'] === '1'){
            if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['type']){
                foreach($this->request->post['postData'] as $m){
                    $postData[$m['name']] = $m['value'];
                }

                switch($this->request->post['type']){
                    case 'plist':
                        $targetTable = 'oc_x_pre_purchase_order_product';

                        $historyTable = 'oc_x_pre_purchase_order_product_history';
                        $historyFields = array(
                            'purchase_order_product_id',
                            'purchase_order_id',
                            'station_id',
                            'sku_id',
                            'product_id',
                            'quantity',
                            'supplier_quantity',
                            'price',
                            'real_cost'
                        );
                        $indexFilter = array(
                            'field' => 'purchase_order_product_id',
                            'value' => $this->request->post['id']
                        );

                        $rowData = array(
                            'quantity' => $postData['quantity'],
                            'supplier_quantity' => $postData['supplier_quantity'],
                            'price' => $postData['price'],
                            'real_cost' => $postData['real_cost'],
                            'modified_by' => $this->user->getId()
                        );

                        $result = $this->model_purchase_pre_purchase->addHistory($targetTable, $historyTable, $historyFields, $indexFilter, $this->user->getId());
                        $result = $this->model_purchase_pre_purchase->update($targetTable, $rowData, $indexFilter);

                        break;
                }

                //Update order total
                $result = $this->model_purchase_pre_purchase->reCalcPurchaseOrder($this->request->post['order_id']);

                $returnData = array('return_code'=>'SUCCESS', 'return_message'=>'修改成功', 'return_data'=>$result);
            }
        }
        else{
            $returnData = array('return_code'=>'FAIL', 'return_message'=>'采购订单已支付，不可修改', 'return_data'=>$paymentStatus);
        }

        echo json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }

    public function readExcel(){
        if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
            // Sanitize the filename
            $filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

            // Allowed file extension types
            $allowed = array(
                'xls',
                'xlsx'
            );
            $file_extension = utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1));
            if (!in_array($file_extension, $allowed)) {
                $return_code ='错误的文件类型';
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['file']['tmp_name']);
            if (preg_match('/\<\?php/i', $content)) {
                $return_code ='文件内空有PHP代码';
            }

            // Return any upload error
            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                $return_code = '文件上传错误,请重试.';
            }
        } else {
            $return_code = '请上传文件.';
        }

        $target = DIR_UPLOAD . 'purchase_outstore_' . date('YmdHis') . '_' . $this->user->getId() . '.' . $file_extension;
        $bool = move_uploaded_file($this->request->files['file']['tmp_name'], $target);
        if(!$bool){
            $return_code = '文件移动失败.';
        }

        $fileType = PHPExcel_IOFactory::identify($target);
        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $objPHPExcel = $objReader->load($target);
        $currentSheet = $objPHPExcel->getSheet(0);
        $allRow = $currentSheet->getHighestRow();
        if($allRow < 1){
            $return_code = '上传文件为空.';
        }

        $products = array();
        for($i=1; $i<=$allRow-1; $i++){
            $m=$i+1;

            $products[$i]['product_id'] = (int)$currentSheet->getCell('A' . $m)->getValue();
            $products[$i]['product_name'] = $currentSheet->getCell('B' . $m)->getValue();
            $products[$i]['product_price'] = $currentSheet->getCell('C' . $m)->getValue();
            $products[$i]['product_quantity'] = (int)$currentSheet->getCell('D' . $m)->getValue();
            $products[$i]['supplier_quantity'] = $currentSheet->getCell('E' . $m)->getValue();
        }

        echo json_encode($products);
    }
}
