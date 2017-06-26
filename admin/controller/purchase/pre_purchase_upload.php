<?php
class ControllerPurchasePrePurchaseUpload extends Controller{
    public function index(){
        $this->load->model('purchase/pre_purchase');

        $this->info(0);
    }

    public function edit(){
        $this->load->model('purchase/pre_purchase');
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            if(empty($_POST['product_image'])){
                $this->session->data['error_warning'] = "请上传采购单图片";
            }else{
                $bool = $this->model_purchase_pre_purchase->editPrePurchaseOrder($_POST);

                if($bool){
                    $this->session->data['success_msg'] = "修改成功";
                }
                else{

                    $this->session->data['error_warning'] = "上传采购单失败，确认收货后再提交采购单";
                }
            }
        }

        $this->info($_POST['purchase_order_id']);

    }

    public function info($purchase_order_id){
        if (isset($this->request->get['filter_purchase_order_id'])) {
            $order_id = $this->request->get['filter_purchase_order_id'];
        } elseif($purchase_order_id) {
            $order_id = $purchase_order_id;
        } else {
            $order_id = 0;
        }

        if (isset($this->session->data['error_warning'])) {
            $data['error_warning'] = $this->session->data['error_warning'];
            unset($this->session->data['error_warning']);
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success_msg'])) {
            $data['success_msg'] = $this->session->data['success_msg'];
            unset($this->session->data['success_msg']);
        } else {
            $data['success_msg'] = '';
        }









        $order_info = $this->model_purchase_pre_purchase->getOrder($order_id);

        $order_get_product_info = $this->model_purchase_pre_purchase->getOrderGetProductInfo($order_id);



        $this->document->setTitle("采购单");

        $data['heading_title'] = "采购单";
        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];




        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => "采购单",
            'href' => $this->url->link('purchase/pre_purchase_upload', 'token=' . $this->session->data['token'], 'SSL')
        );


        $data['order_id'] = $order_id;

        if($order_id){
            $data = array_merge($data,$order_info);
        }
        $data['order_get_product_info'] = $order_get_product_info;
        $this->load->model('tool/image');
        $data['thumb'] = $order_id && is_file(DIR_IMAGE . $order_info['image']) ? $this->model_tool_image->resize($order_info['image'], 100, 100) : $this->model_tool_image->resize('no_image.png', 100, 100) ;





        // 送货单多图
        $product_images = $order_id ? $this->model_purchase_pre_purchase->getOrderImages($order_id) : array();

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

        $data['action_image'] = $this->url->link('purchase/pre_purchase_upload/edit', 'token=' . $this->session->data['token'] . '&purchase_order_id=' .$order_id , 'SSL');

        $data['order_stations'] = array(1=>"生鲜",2=>"快销");

        $data['supplier_types'] = $this->model_purchase_pre_purchase->getSupplierTypes();

        $data['order_statuses'] = $this->model_purchase_pre_purchase->getStatuses();

        $data['filter_purchase_order_id'] = $order_id;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('purchase/pre_purchase_pictrue.tpl', $data));
    }

    public function purchaseList(){
        $this->load->model('purchase/pre_purchase');

        $url = '';

        if(isset($this->request->get['supplier_type'])){
            $data['supplier_type'] = $this->request->get['supplier_type'];
            $url .= '&supplier_type=' . $this->request->get['supplier_type'];
        }else{
            $data['supplier_type'] = '';
        }

        if(isset($this->request->get['filter_order_status_id'])){
            $data['filter_order_status_id'] = $this->request->get['filter_order_status_id'];
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }else{
            $data['filter_order_status_id'] = '';
        }

        if(isset($this->request->get['station_id'])){
            $data['station_id'] = $this->request->get['station_id'];
            $url .= '&station_id=' . $this->request->get['station_id'];
        }else{
            $data['station_id'] = '';
        }

        if(isset($this->request->get['date_deliver'])){
            $data['date_deliver'] = $this->request->get['date_deliver'];
            $url .= '&date_deliver=' . $this->request->get['date_deliver'];

        }else{
            $data['date_deliver'] = '';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $filter_data = array(
            'supplier_type' => $data['supplier_type'],
            'filter_order_status_id' => $data['filter_order_status_id'],
            'station_id' => $data['station_id'],
            'date_deliver' => $data['date_deliver'],
        );
//var_dump($this->request->get);die;
        $data['orderLists'] = array();

        $results = $this->model_purchase_pre_purchase->getPurchaseOrderList($filter_data, ($page - 1) * 10, 10);

        foreach ($results as $result) {
            $data['orderLists'][] = array(
                'purchase_order_id'   => $result['purchase_order_id'],
                'name'   => $result['name'],
                'username'     => $result['username'],
            );
        }

        $history_total = $this->model_purchase_pre_purchase->getTotalPurchaseOrderList($filter_data);

        $pagination = new Pagination();
        $pagination->total = $history_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = $this->url->link('purchase/pre_purchase_upload/purchaseList', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

        $this->response->setOutput($this->load->view('purchase/purchase_order_list.tpl', $data));
    }
}
?>