<?php


class ControllerPurchaseWarehouseAllocationNote extends Controller {


    public function index()
    {
        $this->document->setTitle('仓库出库单');
        $data['heading_title'] = '仓库出库单';
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $this->load->model('purchase/warehouse_allocation_note');
        if(isset($this->session->data['success']) && $this->session->data['success']){
            $data['success_msg'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if (isset($this->request->get['filter_warehouse_order_id'])) {
            $filter_warehouse_order_id = $this->request->get['filter_warehouse_order_id'];
        } else {
            $filter_warehouse_order_id = '';
        }
        if (isset($this->request->get['filter_date_deliver'])) {
            $filter_date_deliver = $this->request->get['filter_date_deliver'];
        } else {
            $filter_date_deliver = '';
        }
        if (isset($this->request->get['warehouse_id'])) {
            $warehouse_id = $this->request->get['warehouse_id'];
        } else {
            $warehouse_id = '';
        }
        if (isset($this->request->get['filter_order_status_id'])) {
            $filter_order_status_id = $this->request->get['filter_order_status_id'];
        } else {
            $filter_order_status_id = '';
        }


        $url = '';
        if (isset($this->request->get['filter_warehouse_order_id'])) {
            $url .= '&filter_warehouse_order_id=' . $this->request->get['filter_warehouse_order_id'];
        }
        $data['purchase_date'] = isset($this->request->post['purchase_date'])?$this->request->post['purchase_date']:date('Y-m-d', time()+8*3600);

        $data['filterDefault'] = array(
            'deliver_date' => $date = date("Y-m-d", strtotime("0 day")), //Tomorrow
        );
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '仓库出库单',
            'href' => $this->url->link('purchase/warehouse_allocation_note', 'token=' . $this->session->data['token']. '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] , 'SSL')
        );



        $data['warehouse'] = $this->model_purchase_warehouse_allocation_note->getWarehouse();
        $data['warehouse_status_id'] = $this->model_purchase_warehouse_allocation_note->getWarehouse_StatusId();
        $filter_warehouse_id_global = isset($this->request->get['filter_warehouse_id_global'])?$this->request->get['filter_warehouse_id_global']:false;
        $filter_warehouse_order_id = isset($this->request->get['filter_warehouse_order_id'])?$this->request->get['filter_warehouse_order_id']:false;
        $filter_date_deliver = isset($this->request->get['filter_date_deliver'])?$this->request->get['filter_date_deliver']:false;
        $warehouse_id = isset($this->request->get['warehouse_id'])?$this->request->get['warehouse_id']:false;
        $filter_order_status_id = isset($this->request->get['filter_order_status_id'])?$this->request->get['filter_order_status_id']:false;
        $filter_out_type = isset($this->request->get['filter_out_type'])?$this->request->get['filter_out_type']:false;
        $filter_out_type_id = isset($this->request->get['filter_out_type_id'])?$this->request->get['filter_out_type_id']:false;
        $filter_data = array(
            'filter_warehouse_id_global' => $filter_warehouse_id_global,
            'filter_warehouse_order_id' => $filter_warehouse_order_id,
            'filter_date_deliver' =>$filter_date_deliver,
            'warehouse_id' =>$warehouse_id,
            'filter_order_status_id' =>$filter_order_status_id,
            'filter_out_type'=>$filter_out_type,
            'filter_out_type_id'=>$filter_out_type_id,
        );

        $results = $this->model_purchase_warehouse_allocation_note->getWarehouseRequisition($filter_data);
        $data['warehouse_requisition'] = array();
        foreach($results as $result){
            $data['warehouse_requisition'][] = array(
                'relevant_id' =>$result['relevant_id'],
                'to_warehouse'=>$result['title'],
                'out_type' =>$result['out_type'],
                'from_warehouse'=>$result['title'],
                'date_added'=>$result['date_added'],
                'add_user'=>$result['username'],
                'deliver_date' =>$result['deliver_date'],
                'warehouse_status' =>$result['name'],
                'comment'=>$result['comment'],
                'relevant_status_id'=>$result['relevant_status_id'],
                'view' =>$this->url->link('purchase/warehouse_allocation_note/view', 'token=' . $this->session->data['token'] . '&relevant_id=' . $result['relevant_id'] .'&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] . $url, 'SSL')
            );

        }


        $data['add'] = $this->url->link('purchase/warehouse_allocation_note/add', 'token=' . $this->session->data['token'] . '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] . $url, 'SSL');
        $data['text_list'] = $this->language->get('仓库出库单');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_view'] = $this->language->get('查看详情');
        $data['token'] = $this->session->data['token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['filter_warehouse_order_id'] = $filter_warehouse_order_id;
        $data['filter_order_status_id'] = $filter_order_status_id;
        $data['filter_date_deliver'] =$filter_date_deliver;
        $data['filter_out_type'] = $filter_out_type;
        $data['warehouse_id'] = $warehouse_id;
        $this->response->setOutput($this->load->view('purchase/warehouse_allocation_note.tpl',$data));
    }

    public function view(){
        $this->document->setTitle('仓库出库单');
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $this->load->model('purchase/warehouse_allocation_note');
        $data['breadcrumbs'][] = array(
            'text' => '仓库出库单',
            'href' => $this->url->link('purchase/warehouse_allocation_note', 'token=' . $this->session->data['token']. '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] , 'SSL')
        );
        $filter_warehouse_id_global = isset($this->request->get['filter_warehouse_id_global'])?$this->request->get['filter_warehouse_id_global']:false;
        $relevant_id = isset($this->request->get['relevant_id'])?$this->request->get['relevant_id']:false;
        $results = $this->model_purchase_warehouse_allocation_note->getWarehouseRequisitionItem($relevant_id,$filter_warehouse_id_global);
        $data['item']=array();
        foreach($results as $result){
            $data['item'][]=array(
                'relevant_id'=>$result['relevant_id'],
                'product_id'=> $result['product_id'],
                'out_type'=>$result['out_type'],
                'product_name'=>$result['name'],
                'title' =>$result['title'],
                'product_section_title'=>$result['product_section_title'],
                'inventory'=>$result['inventory'],
                'num'=>$result['num'],
            );
        }
        $data['relevant_id']=$relevant_id;
        $data['text_list'] = $this->language->get('仓库出库单明细');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('purchase/warehouse_allocation_note_item.tpl',$data));
    }


    function confirm(){

        $this->load->model('purchase/warehouse_allocation_note');
        $userId = $this->user->getId();

        $relevant_id = isset($this->request->get['relevant_id'])?$this->request->get['relevant_id']:false;
        $results = $this->model_purchase_warehouse_allocation_note->confirm($relevant_id,$userId);

        echo json_encode( $results, JSON_UNESCAPED_UNICODE );
    }

    function add(){
        $this->document->setTitle('仓库出库单');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->load->model('purchase/warehouse_allocation_note');
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '仓库出库单',
            'href' => $this->url->link('purchase/warehouse_allocation_note', 'token=' . $this->session->data['token']. '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] , 'SSL')
        );
//        $data['filterDefault'] = array(
//            'deliver_date' => $date = date("Y-m-d", strtotime("0 day")), //Tomorrow
//        );
        $data['warehouse'] = $this->model_purchase_warehouse_allocation_note->getWarehouse();
        $data['token'] = $this->session->data['token'];
        $data['button_filter'] = $this->language->get('button_filter');
        $this->response->setOutput($this->load->view('purchase/warehouse_allocation_note_form.tpl',$data));
    }

    public function autocomplete(){
        $json =array();
        if (isset($this->request->get['products'])) {
            if (isset($this->request->get['products'])) {
                $products_name = $this->request->get['products'];
            } else {
                $products_name = '';
            }

            $this->load->model('purchase/warehouse_allocation_note');

            $filter_data = array(
                'filter_name' =>$products_name,
            );
            $filter_warehouse_id_global = isset($this->request->post['warehouse_id'])?$this->request->post['warehouse_id']:false;
            $results = $this->model_purchase_warehouse_allocation_note->getProducts($filter_data,$filter_warehouse_id_global);

            foreach ($results as $result) {
                if($result['status'] ==0){
                    $json[] = array(
                        'product_id' => $result['product_id'],
                        'status' =>$result['status'],
                        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                        'fix' => $arr=array($result['product_id'],'停用',$result['name'],$result['title'])
                    );
                }else{
                    $json[] = array(
                        'product_id' => $result['product_id'],
                        'status' =>$result['status'],
                        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                        'fix' => $arr=array($result['product_id'],'启用',$result['name'],$result['title'])
                    );
                }

            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }
    public function getProductInfo(){
        $this->load->model('purchase/warehouse_allocation_note');
        $filter_warehouse_id_global = isset($this->request->post['warehouse_id'])?$this->request->post['warehouse_id']:false;
        $product_id = isset($this->request->post['product_id'])?$this->request->post['product_id']:false;
        $results = $this->model_purchase_warehouse_allocation_note->getProductInfo($product_id,$filter_warehouse_id_global);
        echo json_encode( $results, JSON_UNESCAPED_UNICODE );

    }
    public function submitAdjust(){
        $this->load->model('purchase/warehouse_allocation_note');
        $userId = $this->user->getId();
        $filter_warehouse_id_global = isset($this->request->post['from_warehouse_id'])?$this->request->post['from_warehouse_id']:false;
        $to_warehouse_id= isset($this->request->post['to_warehouse_id'])?$this->request->post['to_warehouse_id']:false;
        $deliver_date= isset($this->request->post['deliver_date'])?$this->request->post['deliver_date']:false;
        $reasons= isset($this->request->post['reasons'])?$this->request->post['reasons']:false;
        $outtype= isset($this->request->post['outtype'])?$this->request->post['outtype']:false;
        $products = isset($this->request->post['products'])?$this->request->post['products']:false;
        $results = $this->model_purchase_warehouse_allocation_note->submitAdjust($products,$filter_warehouse_id_global,$to_warehouse_id,$deliver_date,$userId,$reasons,$outtype);
        echo json_encode( $results, JSON_UNESCAPED_UNICODE );

    }

}