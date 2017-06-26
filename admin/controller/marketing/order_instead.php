<?php
class ControllerMarketingOrderInstead extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('marketing/order_instead');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/order_instead');

        $this->getForm();
    }

    public function getForm(){
        $this->load->model('station/station');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form'] = $this->language->get('text_form');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['false'])) {
            $data['false'] = $this->session->data['false'];

            unset($this->session->data['false']);
        } else {
            $data['false'] = '';
        }
        if (isset($this->session->data['order_false'])) {
            $data['order_false'] = $this->session->data['order_false'];

            unset($this->session->data['order_false']);
        } else {
            $data['order_false'] = '';
        }
        if (isset($this->session->data['product_false'])) {
            $data['product_false'] = $this->session->data['product_false'];

            unset($this->session->data['product_false']);
        } else {
            $data['product_false'] = '';
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['order'])) {
            $data['error_order'] = $this->error['order'];
        } else {
            $data['error_order'] = '';
        }

        if (isset($this->error['product'])) {
            $data['error_product'] = $this->error['product'];
        } else {
            $data['error_product'] = '';
        }

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('marketing/order_instead', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['token'] = $this->session->data['token'];
        $data['action_order'] = $this->url->link('marketing/order_instead/order_insert', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_insert'] = $this->url->link('marketing/order_instead/one_insert', 'token=' . $this->session->data['token'], 'SSL');
        $data['stations'] = $this->model_station_station->getStationList();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('marketing/order_instead_form.tpl', $data));
    }

    public function order_insert(){
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
                exit('错误的文件类型.');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['file']['tmp_name']);
            if (preg_match('/\<\?php/i', $content)) {
                exit('文件内空有PHP代码.');
            }

            // Return any upload error
            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                exit('文件上传错误,请重试.');
            }
        } else {
            exit('请上传文件.');
        }
//        var_dump($this->request->files['file']['tmp_name']);die;
        $target = DIR_UPLOAD . 'order_instead_' . date('YmdHis') . '_' . $this->user->getId() . '.' . $file_extension;
        $bool = move_uploaded_file($this->request->files['file']['tmp_name'], $target);
        if(!$bool){
            exit('文件移动失败.');
        }

        $fileType = PHPExcel_IOFactory::identify($target);
        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $objPHPExcel = $objReader->load($target);
        $currentSheet = $objPHPExcel->getSheet(0);
        $allRow = $currentSheet->getHighestRow();
        if($allRow < 1){
            exit('上传文件为空.');
        }
        $merchants = array();
        $products = array();
        $order_merchant = array();
        $order_products = array();
        $orders_products = array();
        $product_ids = array();
        $product_skus = array();
        $merchantOrders = array();

        //获得excel中填写的product_id
//        for($i=1; $i<=$allRow; $i++){
//            if($i >= 2){
//                $product_ids[] = (int)$currentSheet->getCell('P' . $i)->getValue();
//            }
//        }

        //获得excel中的sku,确保外仓不提供我司的product_id，也能通过sku来匹配到我司的product_id
        for($i=1; $i<=$allRow; $i++){
            if($i >= 2){
                $product_skus[] = (int)$currentSheet->getCell('O' . $i)->getValue();
            }
        }

        //通过product_id 去匹配商品的系统名称
//        $query = $this->db->query("SELECT product_id, name FROM oc_product_description WHERE product_id IN(" . implode(',', $product_ids) . ") AND language_id=2");
//        $sys_names = array();
//
//        foreach($query->rows as $row){
//            $sys_names[$row['product_id']] = $row['name'];
//        }

        //通过product表中的sku去匹配外仓在我司的商品系统名称
        $query = $this->db->query("SELECT product_id,sku, name FROM oc_product WHERE sku IN(" . implode(',', $product_skus) . ")");
        $sys_names = array();

        foreach($query->rows as $row){
            $sys_names[$row['sku']] = array(
                'name' => $row['name'],
                'product_id' => $row['product_id'],
            );
        }

        for($i=1; $i<=$allRow; $i++){
            if($i>=2){
                $merchants[$i] = $currentSheet->getCell('A' . $i)->getValue();
                $merchantOrders[$i] = $currentSheet->getCell('B' . $i)->getValue();
                $products[$i] = array(
                    'product_id' => $currentSheet->getCell('P' . $i)->getValue(),
                    'product_name' => $currentSheet->getCell('M' . $i)->getValue(),
                    'sys_name' => isset($sys_names[$currentSheet->getCell('P' . $i)->getValue()]) ? $sys_names[$currentSheet->getCell('P' . $i)->getValue()] : '无此商品',
                    'price' => $currentSheet->getCell('Q' . $i)->getValue(),
                    'quantity' => $currentSheet->getCell('S' .$i)->getValue(),
                    'line_sum' => sprintf("%.2f",$currentSheet->getCell('Q' . $i)->getValue()*$currentSheet->getCell('S' . $i)->getValue()),
                );
            }
        }

        $rows = count($merchants);
        for($i = 0; $i < $rows; $i++){
            for($j = 2; $j < $rows+1; $j++){
                if($merchants[$j] && !$merchants[$j+1]){
                    $merchants[$j+1] = $merchants[$j];
                }
            }
        }

        $rows = count($merchantOrders);
        for($i = 0; $i < $rows; $i++){
            for($j = 2; $j < $rows+1; $j++){
                if($merchantOrders[$j] && !$merchantOrders[$j+1]){
                    $merchantOrders[$j+1] = $merchantOrders[$j];
                }
            }
        }

        //通过excel划定的顺序来绑定商品属于某个商家的订单
        foreach($products as $k=>&$value){
            $value['excel_order'] = $merchants[$k];
            $value['out_order_id'] = $merchantOrders[$k];
        }


        $merchants = array_unique($merchants);

        $merchantOrders = array_unique($merchantOrders);
        //设置是否可以上传flag,如果flag为true才可以提交代下单

        $flag = true;

        //整合订单主表信息到订单产品
        foreach($products as $k=>$v){
            $product_id_now = $currentSheet->getCell('P' . $k)->getValue();
            $orders_products[] = array(
                'customer_id' => $currentSheet->getCell('E' . $k)->getValue(),
                'merchant_name' => $currentSheet->getCell('F' . $k)->getValue(),
                'customer_name' => $currentSheet->getCell('K' . $k)->getValue(),
                'merchant_address' => $currentSheet->getCell('J' . $k)->getValue(),
                'telephone' => $currentSheet->getCell('L' . $k)->getValue(),
                //判断外仓填写我司的product_id是否正确,如正确才会允许保存该订单,如果外仓没有填写product_id则用sku查出来的product_id
                //无论外仓是否填写商品id，系统任然会匹配填写id
                'product_id_written' => $currentSheet->getCell('P' . $k)->getValue(),
                'product_id' => array_key_exists($currentSheet->getCell('O' . $k)->getValue(),$sys_names) ? $sys_names[$currentSheet->getCell('O' . $k)->getValue()]['product_id'] : 0,
//                    $product_id_now :  isset($sys_names[$currentSheet->getCell('O' . $k)->getValue()]['product_id']) ? $sys_names[$currentSheet->getCell('O' . $k)->getValue()]['product_id'] : 0,
                'product_name' => $currentSheet->getCell('M' . $k)->getValue(),
                'sys_name' => isset($sys_names[$currentSheet->getCell('O' . $k)->getValue()]['name']) ? $sys_names[$currentSheet->getCell('O' . $k)->getValue()]['name'] : '无此商品',
                'price' => $currentSheet->getCell('Q' . $k)->getValue(),
                'quantity' => $currentSheet->getCell('S' .$k)->getValue(),
                'line_sum' => sprintf("%.2f",$currentSheet->getCell('Q' . $k)->getValue()*$currentSheet->getCell('S' . $k)->getValue()),
            );
            //如对应sku找不到商品id则不予保存
            if(!array_key_exists($currentSheet->getCell('O' . $k)->getValue(),$sys_names)){
                $flag = $flag && false;
            }
        }

//        foreach($merchants as $k=>$v){
//            //取订单主表的商家信息
//            $order_merchant[$v] = array(
//                'customer_id' => $currentSheet->getCell('E' . $k)->getValue(),
//                'merchant_name' => $currentSheet->getCell('F' . $k)->getValue(),
//                'customer_name' => $currentSheet->getCell('K' . $k)->getValue(),
//                'merchant_address' => $currentSheet->getCell('J' . $k)->getValue(),
//                'telephone' => $currentSheet->getCell('L' . $k)->getValue(),
//                'internal_note' => $currentSheet->getCell('B' . $k)->getValue(),
//            );
//            foreach($products as $vv){
//                if($vv['excel_order'] == $v){
//                    //商家订单里包含的产品明细
//                    $order_products[$v][] = $vv;
//                }
//            }
//            $order_merchant[$v]['products'] = $order_products[$v];
//        }

        foreach($merchantOrders as $k=>$v){
            //取订单主表的商家信息
            $order_merchant[$v] = array(
                'customer_id' => $currentSheet->getCell('E' . $k)->getValue(),
                'merchant_name' => $currentSheet->getCell('F' . $k)->getValue(),
                'customer_name' => $currentSheet->getCell('K' . $k)->getValue(),
                'merchant_address' => $currentSheet->getCell('J' . $k)->getValue(),
                'telephone' => $currentSheet->getCell('L' . $k)->getValue(),
                'internal_note' => $currentSheet->getCell('B' . $k)->getValue(),
            );
            foreach($products as $vv){
                if($vv['out_order_id'] == $v){
                    //商家订单里包含的产品明细
                    $order_products[$v][] = $vv;
                }
            }
            $order_merchant[$v]['products'] = $order_products[$v];
        }

        $data['orders'] = $order_merchant;
        $data['orders_products'] = $orders_products;
        $data['flag'] = $flag;

        $this->document->setTitle('代下单-批量导入');
        $data['heading_title'] = '批量导入订单';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '批量导入订单',
            'href' => $this->url->link('marketing/order_instead', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['uploaded_file'] = $target;
        $data['action_confirm'] = $this->url->link('marketing/order_instead/order_confirm', 'token=' . $this->session->data['token'], 'SSL');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('marketing/order_instead_confirm.tpl', $data));
    }

    public function order_confirm(){
        $this->load->language('marketing/order_instead');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/order_instead');

        $this->load->model('marketing/order_instead');
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['uploaded_file']){
            $uploaded_file = $this->request->post['uploaded_file'];
            if(!file_exists($uploaded_file)){
                exit('文件不存在,请重新上传.');
            }

            //读取excel
            $fileType = PHPExcel_IOFactory::identify($uploaded_file);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($uploaded_file);
            $currentSheet = $objPHPExcel->getSheet(0);
            $allRow = $currentSheet->getHighestRow();
            if($allRow < 1){
                exit('上传文件为空.');
            }
            $merchants = array();
            $products = array();
            $order_merchant = array();
            $order_products = array();
            $orders_products = array();
            $product_ids = array();
            $product_skus = array();
            $merchantOrders = array();

//            for($i=1; $i<=$allRow; $i++){
//                if($i >= 2){
//                    $product_ids[] = (int)$currentSheet->getCell('P' . $i)->getValue();
//                }
//            }

            //获得excel中的sku,确保外仓不提供我司的product_id，也能通过sku来匹配到我司的product_id
            for($i=1; $i<=$allRow; $i++){
                if($i >= 2){
                    $product_skus[] = (int)$currentSheet->getCell('O' . $i)->getValue();
                }
            }

//            $query = $this->db->query("SELECT product_id, name FROM oc_product_description WHERE product_id IN(" . implode(',', $product_ids) . ") AND language_id=2");
//            $sys_names = array();
//
//            foreach($query->rows as $row){
//                $sys_names[$row['product_id']] = $row['name'];
//            }

            //通过product表中的sku去匹配外仓在我司的商品系统名称
            $query = $this->db->query("SELECT product_id,sku, name FROM oc_product WHERE sku IN(" . implode(',', $product_skus) . ")");
            $sys_names = array();

            foreach($query->rows as $row){
                $sys_names[$row['sku']] = array(
                    'name' => $row['name'],
                    'product_id' => $row['product_id'],
                );
            }

            for($i=1; $i<=$allRow; $i++){
                if($i>=2){
                    $merchants[$i] = $currentSheet->getCell('A' . $i)->getValue();
                    $merchantOrders[$i] = $currentSheet->getCell('B' . $i)->getValue();
                    $products[$i] = array(
                        'product_id' => $currentSheet->getCell('P' . $i)->getValue(),
                        'product_name' => $currentSheet->getCell('M' . $i)->getValue(),
                        'sys_name' => isset($sys_names[$currentSheet->getCell('P' . $i)->getValue()]) ? $sys_names[$currentSheet->getCell('P' . $i)->getValue()] : '无此商品',
                        'price' => $currentSheet->getCell('Q' . $i)->getValue(),
                        'quantity' => $currentSheet->getCell('S' .$i)->getValue(),
                        'line_sum' => sprintf("%.2f",$currentSheet->getCell('Q' . $i)->getValue()*$currentSheet->getCell('S' . $i)->getValue()),
                    );
                }
            }
//        var_export($products);die;
            $rows = count($merchants);
            for($i = 0; $i < $rows; $i++){
                for($j = 2; $j < $rows+1; $j++){
                    if($merchants[$j] && !$merchants[$j+1]){
                        $merchants[$j+1] = $merchants[$j];
                    }
                }
            }

            $rows = count($merchantOrders);
            for($i = 0; $i < $rows; $i++){
                for($j = 2; $j < $rows+1; $j++){
                    if($merchantOrders[$j] && !$merchantOrders[$j+1]){
                        $merchantOrders[$j+1] = $merchantOrders[$j];
                    }
                }
            }

            //通过excel划定的顺序来绑定商品属于某个商家的订单
            foreach($products as $k=>&$value){
                $value['excel_order'] = $merchants[$k];
                $value['out_order_id'] = $merchantOrders[$k];
            }

            $merchants = array_unique($merchants);
            $merchantOrders = array_unique($merchantOrders);

            //整合订单主表信息到订单产品
            foreach($products as $k=>$v){
                $orders_products[] = array(
                    'customer_id' => $currentSheet->getCell('E' . $k)->getValue(),
                    'merchant_name' => $currentSheet->getCell('F' . $k)->getValue(),
                    'customer_name' => $currentSheet->getCell('K' . $k)->getValue(),
                    'merchant_address' => $currentSheet->getCell('J' . $k)->getValue(),
                    'telephone' => $currentSheet->getCell('L' . $k)->getValue(),
//                    'product_id' => $currentSheet->getCell('P' . $k)->getValue(),
                    'product_id_written' => $currentSheet->getCell('P' . $k)->getValue(),
                    'product_id' => array_key_exists($currentSheet->getCell('O' . $k)->getValue(),$sys_names) ? $sys_names[$currentSheet->getCell('O' . $k)->getValue()]['product_id'] : 0,
                    'sku' => $currentSheet->getCell('O' . $k)->getValue(),
                    'product_name' => $currentSheet->getCell('M' . $k)->getValue(),
                    'sys_name' => isset($sys_names[$currentSheet->getCell('P' . $k)->getValue()]) ? $sys_names[$currentSheet->getCell('P' . $k)->getValue()] : '无此商品',
                    'price' => $currentSheet->getCell('Q' . $k)->getValue(),
                    'quantity' => $currentSheet->getCell('S' .$k)->getValue(),
                    'line_sum' => sprintf("%.2f",$currentSheet->getCell('Q' . $k)->getValue()*$currentSheet->getCell('S' . $k)->getValue()),
                    'excel_order' => $v['excel_order'],
                );
            }

//        var_export($orders_products);die;
//            foreach($merchants as $k=>$v){
//                //取订单主表的商家信息
//                $order_merchant[$v] = array(
//                    'customer_id' => $currentSheet->getCell('E' . $k)->getValue(),
//                    'merchant_name' => $currentSheet->getCell('F' . $k)->getValue(),
//                    'customer_name' => $currentSheet->getCell('K' . $k)->getValue(),
//                    'merchant_address' => $currentSheet->getCell('J' . $k)->getValue(),
//                    'telephone' => $currentSheet->getCell('L' . $k)->getValue(),
//                    'internal_note' => $currentSheet->getCell('B' . $k)->getValue(),
//                );
//                foreach($orders_products as $vv){
//                    if($vv['excel_order'] == $v){
//                        //商家订单里包含的产品明细
//                        $order_products[$v][] = $vv;
//                    }
//                }
//                $order_merchant[$v]['products'] = $order_products[$v];
//            }

            foreach($merchantOrders as $k=>$v){
                //取订单主表的商家信息
                $order_merchant[$v] = array(
                    'customer_id' => $currentSheet->getCell('E' . $k)->getValue(),
                    'merchant_name' => $currentSheet->getCell('F' . $k)->getValue(),
                    'customer_name' => $currentSheet->getCell('K' . $k)->getValue(),
                    'merchant_address' => $currentSheet->getCell('J' . $k)->getValue(),
                    'telephone' => $currentSheet->getCell('L' . $k)->getValue(),
                    'internal_note' => $currentSheet->getCell('B' . $k)->getValue(),
                );
                foreach($products as $vv){
                    if($vv['out_order_id'] == $v){
                        //商家订单里包含的产品明细
                        $order_products[$v][] = $vv;
                    }
                }
                $order_merchant[$v]['products'] = $order_products[$v];
            }

            //写入excel表格中分离出来的order_merchant的数组(只需写入到order表和order_product表)
            //算出每单的总金额
            foreach($order_merchant as &$value){
                $sub_total = 0;
                foreach($value['products'] as $vv){
                    $sub_total += $vv['line_sum'];
                }
                $value['sub_total'] = sprintf("%.2f",$sub_total);
            }

            $excel_insert = $this->model_marketing_order_instead->order_insert($order_merchant);
            $data['insert'] = $excel_insert;

            if($data['insert']['order'] && $data['insert']['product']){
                $this->session->data['success'] = $this->language->get('text_success');
            }elseif(!$data['insert']['order']){
                $this->session->data['order_false'] = $this->language->get('text_order_false');
            }elseif(!$data['insert']['product']){
                $this->session->data['product_false'] = $this->language->get('text_product_false');
            }else{
                $this->session->data['false'] = $this->language->get('text_false');
            }
        }

        $this->getForm();
    }

    public function one_insert() {
        $this->load->language('marketing/order_instead');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('marketing/order_instead');

        $this->load->model('marketing/order_instead');
        if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()){
            $customer_id = $this->request->post['customer_id'];
            $shipping_address_1 = $this->request->post['shipping_address_1'];
            $shipping_phone = $this->request->post['shipping_phone'];
            $station_id = $this->request->post['station_id'];
            $sub_total = $this->request->post['priceTotal'];
            $order_products = $this->request->post['products'];

            $order_insert = $this->model_marketing_order_instead->order_write($customer_id,$shipping_address_1,$shipping_phone,$station_id,$sub_total,$order_products);

            $data['insert'] = $order_insert;

            if($data['insert']['order'] && $data['insert']['product']){
                $this->session->data['success'] = $this->language->get('text_success');
            }elseif(!$data['insert']['order']){
                $this->session->data['order_false'] = $this->language->get('text_order_false');
            }elseif(!$data['insert']['product']){
                $this->session->data['product_false'] = $this->language->get('text_product_false');
            }else{
                $this->session->data['false'] = $this->language->get('text_false');
            }
        }

        $this->getForm();
    }

    public function validateForm(){
        if (!$this->user->hasPermission('modify', 'marketing/order_instead')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        //客户ID为空，不予保存
        if(!$this->request->post['customer_id']){
            $this->error['order'] = $this->language->get('error_order');
        }
        //没有商品信息，不予保存
        if(sizeof($this->request->post['products'])<1){
            $this->error['product'] = $this->lanuage->get('error_product');
        }

        return !$this->error;
    }

    public function order_customer(){
        $customer_id = $this->request->get['customer_id'];

        $sql = "select merchant_address,telephone from oc_customer where customer_id = '". $customer_id ."'";

        $json = $this->db->query($sql)->rows;

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function order_product(){
        $product_id = $this->request->post['product_id'];
        $customer_id = $this->request->post['customer_id'];

        $sql = "select * from oc_product_customer_price where product_id = '".$product_id."' and customer_id = '".$customer_id."'";

        $flag = $this->db->query($sql)->rows;

        if(sizeof($flag) > 0){
            $sql = "select p.name,cp.customer_price price
               from oc_product_customer_price cp
               left join oc_product p on p.product_id = cp.product_id
               where cp.product_id = '".$product_id."' and cp.customer_id = '".$customer_id."' and p.status = 1";
        }else{
            $sql = "select price,name from oc_product where product_id = '".$product_id."' and status = 1";
        }

        $json = $this->db->query($sql)->rows;

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
}