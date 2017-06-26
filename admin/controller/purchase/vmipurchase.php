<?php
/**
 * Created by PhpStorm.
 * User: jshy
 * Date: 2016/10/27
 * Time: 17:15
 */
class ControllerPurchaseVmipurchase extends Controller
{
    /*实效的打开页面*/
    public function index()
    {
        $this->document->setTitle('库存管理');
        $data['heading_title'] = '库存管理';
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        if (isset($this->session->data['success']) && $this->session->data['success']) {
            $data['success_msg'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['purchase_date'] = isset($this->request->post['purchase_date']) ? $this->request->post['purchase_date'] : date('Y-m-d', time() + 8 * 3600);


        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '库存管理',
            'href' => $this->url->link('purchase/vmipurchase', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '库存数据导入',
            'href' => $this->url->link('purchase/vmipurchase', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action_upload'] = $this->url->link('purchase/vmipurchase/upload_post', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_adjust'] = $this->url->link('purchase/vmipurchase/adjust_post', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('purchase/vmipurchase.tpl', $data));

    }

// 重置库存上传成功页面
    public function upload_post()
    {

        if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
            // Sanitize the filename
            $filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));
            $purchase_date = isset($this->request->post['purchase_date']) ? $this->request->post['purchase_date'] : date('Y-m-d', time() + 8 * 3600);

            // Allowed file extension types
            $allowed = array(
                'xls',
                'xlsx'
            );
            $file_extension = utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1));
            if (!in_array($file_extension, $allowed)) {
                exit('错误的文件类型.');
            }

            $content = file_get_contents($this->request->files['file']['tmp_name']);
            if (preg_match('/\<\?php/i', $content)) {
                exit('文件内空有PHP代码.');
            }
            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                exit('文件上传错误,请重试.');
            }
        } else {
            exit('请上传文件.');
        }


        $target = DIR_UPLOAD . 'purchase/upload_' . date('YmdHis') . '_' . $this->user->getId() . '.' . $file_extension;
        $bool = move_uploaded_file($this->request->files['file']['tmp_name'], $target);
        if (!$bool) {
            exit('文件移动失败.');
        }
        //echo '上传成功.';

        $fileType = PHPExcel_IOFactory::identify($target);
        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $objPHPExcel = $objReader->load($target);
        $currentSheet = $objPHPExcel->getSheet(0);
        $allRow = $currentSheet->getHighestRow();
        if ($allRow < 2) {
            exit('上传文件为空.');
        }



        for ($i = 1; $i <= $allRow - 1; $i++) {
            $m = $i + 1;
            $products[$i]['sku_id'] = (int)$currentSheet->getCell('A' . $m)->getValue();
            $products[$i]['name'] = $currentSheet->getCell('B' . $m)->getValue();
            $products[$i]['purchase_price_500g_gross'] = (float)$currentSheet->getCell('C' . $m)->getValue();
            $products[$i]['purchase_price_500g'] = (float)$currentSheet->getCell('D' . $m)->getValue();
            $products[$i]['purchase_qty_500g'] = (float)$currentSheet->getCell('E' . $m)->getValue();
            $products[$i]['purchase_total'] = (float)$currentSheet->getCell('F' . $m)->getValue();
            $products[$i]['buyer'] = $currentSheet->getCell('G' . $m)->getValue();
            $products[$i]['sys_name'] = isset($sys_names[$products[$i]['sku_id']]) ? $sys_names[$products[$i]['sku_id']] : 'NULL';
        }
        $data['products'] = $products;
        //print_r($products);
        $this->document->setTitle('库存管理－上传文件');
        $data['heading_title'] = '库存管理－上传文件';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '库存管理',
            'href' => $this->url->link('purchase/vmipurchase', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '库存数据导入',
            'href' => $this->url->link('purchase/vmipurchase', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['uploaded_file'] = $target;
        $data['purchase_date'] = $purchase_date;
        $data['action_confirm'] = $this->url->link('purchase/vmipurchase/upload_confirm', 'token=' . $this->session->data['token'], 'SSL');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('purchase/vmipurchase_upload_post.tpl', $data));
    }
    public function upload_confirm(){
        $now = date("H:i", time()+8*3600);

        //var_dump($this->request->post); exit();

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['uploaded_file'] &&
            ($now>='04:00' || $now<'23:00') ){
            $uploaded_file = $this->request->post['uploaded_file'];
            if(!file_exists($uploaded_file)){
                exit('文件不存在,请重新上传.');
            }

            // 读取Excel
            $fileType = PHPExcel_IOFactory::identify($uploaded_file);
            $objReader = PHPExcel_IOFactory::createReader($fileType);
            $objPHPExcel = $objReader->load($uploaded_file);
            $currentSheet = $objPHPExcel->getSheet(0);
            $allRow = $currentSheet->getHighestRow();
            if($allRow < 2){
                exit('上传文件为空.');
            }
            $product_ids = array();
            for($i=1; $i<=$allRow-1; $i++){
                $m=$i+1;

                $product_ids[] = (int)$currentSheet->getCell('A' . $m)->getValue();
            }

            $query = $this->db->query("SELECT sku_id, name FROM oc_x_sku WHERE sku_id IN(" . implode(',', $product_ids) . ")");
            $sys_names = array();
            foreach($query->rows as $row){
                $sys_names[$row['sku_id']] = $row['name'];
            }
            // 获取商品数据
            $products = array();
            for($i=1; $i<=$allRow-1; $i++){
                $m = $i + 1;
                $products[$i]['sku_id'] = (int)$currentSheet->getCell('A' . $m)->getValue();
                $products[$i]['name']       = $currentSheet->getCell('B' . $m)->getValue();
                $products[$i]['purchase_price_500g_gross']   = (float)$currentSheet->getCell('C' . $m)->getValue();
                $products[$i]['purchase_price_500g']   = (float)$currentSheet->getCell('D' . $m)->getValue();
                $products[$i]['purchase_qty_500g']   = (float)$currentSheet->getCell('E' . $m)->getValue();
                $products[$i]['purchase_total']   = (float)$currentSheet->getCell('F' . $m)->getValue();
                $products[$i]['buyer'] = $currentSheet->getCell('G' . $m)->getValue();
            }

            // 写入数据库
            $time = time()+8*3600;
            $date = isset($this->request->post['purchase_date'])? $this->request->post['purchase_date'] : date("Y-m-d", $time);
            $date_added = date("Y-m-d H:i:s", $time);
            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName();

            $this->db->query('START TRANSACTION');
            $bool = 1;

            //替换已上传库存
            $bool = $bool && $this->db->query("update oc_x_vmipurchase_order A left join oc_x_vmipurchase_order_product B on A.purchase_order_id = B.purchase_order_id set A.status = 0, B.status = 0 where A.date = '".$date."'");

            //增加采购记录
            $sql = "INSERT INTO `oc_x_vmipurchase_order` (`station_id`, `date`, `date_added`, `added_by`, `add_user_name`) VALUES('1', '{$date}', '{$date_added}', '{$user_id}', '{$user_name}')";
            $bool = $bool && $this->db->query($sql);
            $purchase_order_id = $this->db->getLastId();

            //增加采购
            $sql = "INSERT INTO `oc_x_vmipurchase_order_product` (`purchase_order_id`,`sku_id`, `product_id`, `weight_class_id`, `date`, `purchase_price_500g_gross`, `purchase_price_500g`, `purchase_qty_500g`, `purchase_total`, `buyer`) VALUES ";
            $m=0;
            foreach($products as $product){
                $sql .= "(".$purchase_order_id.", '".$product['sku_id']."',0 , 0, '".$date."', '".$product['purchase_price_500g_gross']."', '".$product['purchase_price_500g']."', '".$product['purchase_qty_500g']."', '".$product['purchase_total']."', '". $product['buyer'] ."')";
                if(++$m < sizeof($products)){
                    $sql .= ', ';
                }
                else{
                    $sql .= ';';
                }
            }
            $bool = $bool && $this->db->query($sql);


            if($bool){
                $this->db->query('COMMIT');
            }
            else{
                $this->db->query("ROLLBACK");
            }

            $title = "库存管理－导入成功";
            if(!$bool){
                $title = "库存管理－导入错误";
            }

            // 显示成功页面
            $this->document->setTitle('采购管理');
            $data['heading_title'] = $title;
            $data['breadcrumbs'] = array();
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );
            $data['breadcrumbs'][] = array(
                'text' => '采购数据导入',
                'href' => $this->url->link('purchase/vmipurchase', 'token=' . $this->session->data['token'], 'SSL')
            );
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput($this->load->view('purchase/vmipurchase.tpl', $data));
            $this->response->redirect($this->url->link('purchase/vmipurchase', 'token=' . $this->session->data['token'], 'SSL'));
        }else{
            exit('文件或文件上传时间请求错误.');
        }
    }
    public function adjust_post(){
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['products']){
            $products = $this->request->post['products'];
            $comment = $this->request->post['comment'];

            // 写入数据库
            $time = time()+8*3600;
            $date = date("Y-m-d", $time);
            $date_added = date("Y-m-d H:i:s", $time);
            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName();

            $this->db->query("START TRANSACTION");
            $this->db->query("INSERT INTO oc_x_purchase_move (`station_id`, `date`, `timestamp`, `from_station_id`, `purchase_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`) VALUES('1', '{$date}', '{$time}', '1', '1', '{$date_added}', '{$user_id}', '{$user_name}', '{$comment}')");
            $purchase_move_id = $this->db->getLastId();
            $sql = 'INSERT INTO oc_x_purchase_move_item(`purchase_move_id`, `station_id`, `product_id`, `quantity`) VALUES';

            $m=0;
            foreach($products as $product){
                $sql .= "('{$purchase_move_id}', '1', '{$product['product_id']}', '{$product['quantity']}')";
                if(++$m < sizeof($products)){
                    $sql .= ', ';
                }
                else{
                    $sql .= ';';
                }
            }

            $this->db->query($sql);
            $this->db->query('COMMIT');

            $this->session->data['success'] = '调整成功!';
            $this->response->redirect($this->url->link('purchase/vmipurchase', 'token=' . $this->session->data['token'], 'SSL'));
        }else{
            exit('请求错误.');
        }
    }
    public function vmipurchaseList(){

        $sql = "select A.purchase_order_id, A.`date` purchase_date, A.date_added adate, B.upload_times, A.add_user_name from (select * from oc_x_vmipurchase_order where status = 1) A left join (select count(purchase_order_id) upload_times,  `date` purchase_date from  oc_x_vmipurchase_order group by purchase_date) B on A.`date` = B.purchase_date  where `date` between date_sub(current_date(), interval 30 day) and current_date() group by purchase_date";

        $query = $this->db->query($sql);
        echo json_encode($query->rows, JSON_UNESCAPED_UNICODE);
    }

    public function getVmiPurchaseDetail($purchase_order_id){
        $purchase_order_id = isset($this->request->get['purchase_order_id']) ? $this->request->get['purchase_order_id'] : 0;

        $sql = "select A.*, S.name sku_name from oc_x_vmipurchase_order_product A
                left join oc_x_sku S on A.sku_id = S.sku_id
                where A.purchase_order_id =  '".$purchase_order_id."'";
        $query = $this->db->query($sql);
        echo json_encode($query->rows, JSON_UNESCAPED_UNICODE);
    }

}