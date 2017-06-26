<?php
class ControllerPurchaseInventoryPlan extends Controller
{
    // 实效打开页面
    public function index()
    {
        $this->document->setTitle('预设可售库存');
        $data['heading_title'] = '预设可售库存';
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        if(isset($this->session->data['success']) && $this->session->data['success']){
            $data['success_msg'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['inventory_plan_date'] = isset($this->request->post['inventory_plan_date'])?$this->request->post['inventory_plan_date']:date('Y-m-d', time()+8*3600);


        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '预设可售库存',
            'href' => $this->url->link('purchase/inventory_plan', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '数据导入',
            'href' => $this->url->link('purchase/inventory_plan', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action_upload']  = $this->url->link('purchase/inventory_plan/upload_post', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('purchase/inventory_plan.tpl', $data));
    }

    // 重置库存上传成功页面
    public function upload_post(){
        //var_dump($_POST);
        //var_dump($_FILES);
        //var_dump($this->request->files);
        if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
            // Sanitize the filename
            $filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));
            $inventory_plan_date = isset($this->request->post['inventory_plan_date'])?$this->request->post['inventory_plan_date']:date('Y-m-d', time()+8*3600);

            // Allowed file extension types
            $allowed = array(
                'xls',
                'xlsx'
            );
            $file_extension = utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1));
            if (!in_array($file_extension, $allowed)) {
                exit('<meta charset="UTF-8" />错误的文件类型.');
            }

            // Check to see if any PHP files are trying to be uploaded
            $content = file_get_contents($this->request->files['file']['tmp_name']);
            if (preg_match('/\<\?php/i', $content)) {
                exit('<meta charset="UTF-8" />文件内空有PHP代码.');
            }

            // Return any upload error
            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                exit('<meta charset="UTF-8" />文件上传错误,请重试.');
            }
        } else {
            exit('<meta charset="UTF-8" />请上传文件.');
        }
        $target = DIR_UPLOAD .'inventory_plan/upload_' . date('YmdHis') . '_' . $this->user->getId() . '.' . $file_extension;
        $bool = move_uploaded_file($this->request->files['file']['tmp_name'], $target);
        if(!$bool){
            exit('<meta charset="UTF-8" />文件移动失败.');
        }
        //echo '上传成功.';

        $fileType = PHPExcel_IOFactory::identify($target);
        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $objPHPExcel = $objReader->load($target);
        $currentSheet = $objPHPExcel->getSheet(0);
        $allRow = $currentSheet->getHighestRow();
        if($allRow < 2){
            exit('<meta charset="UTF-8" />上传文件为空.');
        }

        $product_ids = array();
        for($i=1; $i<=$allRow; $i++){
            $m=$i+0;

            $product_ids[] = (int)$currentSheet->getCell('A' . $m)->getValue();
        }

        $this->load->model('purchase/inventory_plan');
        $resultList = $this->model_purchase_inventory_plan->getInventoryProductInfo($product_ids);

        $sys_names = array();
        foreach($resultList as $row){
            $sys_names[$row['product_id']] = $row['name'];
        }
        $products = array();
        for($i=1; $i<=$allRow; $i++){
            $m=$i+0;

            $products[$i]['product_id'] = (int)$currentSheet->getCell('A' . $m)->getValue();
            $products[$i]['name']       = $currentSheet->getCell('B' . $m)->getValue();
            $products[$i]['quantity']   = (float)$currentSheet->getCell('C' . $m)->getValue();
            $products[$i]['sys_name']   = isset($sys_names[$products[$i]['product_id']]) ? $sys_names[$products[$i]['product_id']] : 'NULL';
        }


        $data['products'] = $products;
        //print_r($products);

        $this->document->setTitle('预设可售库存－上传文件');
        $data['heading_title'] = '预设可售库存－上传文件';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '预设可售库存',
            'href' => $this->url->link('purchase/inventory_plan', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '数据导入',
            'href' => $this->url->link('purchase/inventory_plan', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['uploaded_file'] = $target;
        $data['inventory_plan_date'] = $inventory_plan_date;
        $data['action_confirm'] = $this->url->link('purchase/inventory_plan/upload_confirm', 'token=' . $this->session->data['token'], 'SSL');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('purchase/inventory_plan_upload_post.tpl', $data));
    }

    // 重置库存确认导入数据库
    public function upload_confirm(){
        date_default_timezone_set('Asia/Shanghai');
        $today = date("Y-m-d", time());
        $inventory_plan_date = isset($this->request->post['inventory_plan_date'])?$this->request->post['inventory_plan_date']:0;

        if(!$inventory_plan_date || $inventory_plan_date <= $today){
            exit('<meta charset="UTF-8" />请指定明天及以后的日期');
        }

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['uploaded_file']){
            $uploaded_file = $this->request->post['uploaded_file'];
            if(!file_exists($uploaded_file)){
                exit('<meta charset="UTF-8" />文件不存在,请重新上传.');
            }

            // 读取Excel
            $fileType = PHPExcel_IOFactory::identify($uploaded_file);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($uploaded_file);
            $currentSheet = $objPHPExcel->getSheet(0);
            $allRow = $currentSheet->getHighestRow();
            if($allRow < 1){
                exit('<meta charset="UTF-8" />上传文件为空.');
            }

            // 获取商品数据
            $products = array();
            for($i=1; $i<=$allRow; $i++){
                $m = $i + 0;
                $products[$i]['product_id'] = (int)$currentSheet->getCell('A' . $m)->getValue();
                $products[$i]['name']       = $currentSheet->getCell('B' . $m)->getValue();
                $products[$i]['quantity']   = (float)$currentSheet->getCell('C' . $m)->getValue();
            }

            $data = array(
                'date' => $inventory_plan_date,
                'products' => $products
            );

            $this->load->model('purchase/inventory_plan');
            $bool = $this->model_purchase_inventory_plan->addInventoryPlan($data);

            $title = "预设可售库存－导入成功";
            if(!$bool){
                $title = "预设可售库存－导入错误";
            }

            // 显示成功页面
            $this->document->setTitle('预设可售库存');
            $data['heading_title'] = $title;
            $data['breadcrumbs'] = array();
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );
            $data['breadcrumbs'][] = array(
                'text' => '数据导入',
                'href' => $this->url->link('purchase/inventory_plan', 'token=' . $this->session->data['token'], 'SSL')
            );

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput($this->load->view('purchase/inventory_plan.tpl', $data));
            $this->response->redirect($this->url->link('purchase/inventory_plan', 'token=' . $this->session->data['token'], 'SSL'));
        }else{
            exit('<meta charset="UTF-8" />文件或文件上传时间请求错误.');
        }
    }

    public function getInventoryPlanList(){
        $this->load->model('purchase/inventory_plan');
        $data = $this->model_purchase_inventory_plan->getInventoryPlanList();

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getInventoryPlanDetail(){
        $this->load->model('purchase/inventory_plan');
        $inventory_plan_id = isset($this->request->get['inventory_plan_id'])?$this->request->get['inventory_plan_id']:0;
        $data = $this->model_purchase_inventory_plan->getInventoryPlanDetail($inventory_plan_id);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}