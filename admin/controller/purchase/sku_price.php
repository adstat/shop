<?php
/**
 * Created by PhpStorm.
 * User: liuyibao
 * Date: 15-10-18
 * Time: 下午6:16
 */
class ControllerPurchaseSkuPrice extends Controller
{
    // 实效打开页面
    public function index()
    {
        $this->document->setTitle('原料价格维护');
        $data['heading_title'] = '原料价格维护';
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        if(isset($this->session->data['success']) && $this->session->data['success']){
            $data['success_msg'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $time = time()+8*3600;
        $data['upload_date'] = date("Y-m-d", $time);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '原料价格维护',
            'href' => $this->url->link('purchase/sku_price', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '原料价格数据上传',
            'href' => $this->url->link('purchase/sku_price', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action_upload']  = $this->url->link('purchase/sku_price/upload_post', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_adjust'] = $this->url->link('purchase/sku_price/adjust_post', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('purchase/sku_price.tpl', $data));
    }

    // 重置库存上传成功页面
    public function upload_post(){
        //var_dump($_POST);
        //var_dump($_FILES);
        //var_dump($this->request->files);
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
        $target = DIR_UPLOAD .'sku_price/upload_' . date('YmdHis') . '_' . $this->user->getId() . '.' . $file_extension;
        $bool = move_uploaded_file($this->request->files['file']['tmp_name'], $target);
        if(!$bool){
            exit('文件移动失败.');
        }
        //echo '上传成功.';

        $fileType = PHPExcel_IOFactory::identify($target);
        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $objPHPExcel = $objReader->load($target);
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

        $sql = "SELECT A.product_id, B.name, A.price, A.retail_price FROM oc_product A
                LEFT JOIN oc_product_description B on A.product_id = B.product_id WHERE A.product_id IN (" . implode(',', $product_ids) . ")";
        $query = $this->db->query($sql);
        $sysInfo = array();
        foreach($query->rows as $row){
            $sysInfo[$row['product_id']] = array(
                'name' => $row['name'],
                'price' => $row['price'],
                'retail_price' => $row['retail_price']
            );
        }

        $products = array();
        for($i=1; $i<=$allRow-1; $i++){
            $m=$i+1;
            $products[$i]['product_id'] = (int)$currentSheet->getCell('A' . $m)->getValue();
            $products[$i]['name']    = $currentSheet->getCell('B' . $m)->getValue();
            $products[$i]['ori_retail_price']   = $currentSheet->getCell('C' . $m)->getValue();
            $products[$i]['retail_price']   = $currentSheet->getCell('D' . $m)->getValue();
            $products[$i]['memo']   = $currentSheet->getCell('E' . $m)->getValue();

            $products[$i]['last_price'] = isset($sysInfo[$products[$i]['product_id']]['price']) ? $sysInfo[$products[$i]['product_id']]['price'] : 'NULL';
            $products[$i]['last_retail_price']   = isset($sysInfo[$products[$i]['product_id']]['retail_price']) ? $sysInfo[$products[$i]['product_id']]['retail_price'] : 'NULL';
            $products[$i]['sys_name']   = isset($sysInfo[$products[$i]['product_id']]['name']) ? $sysInfo[$products[$i]['product_id']]['name'] : 'NULL';
        }


        $data['products'] = $products;

        $this->document->setTitle('原料价格维护－上传文件');
        $data['heading_title'] = '原料价格维护－上传文件';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => '原料价格维护',
            'href' => $this->url->link('purchase/sku_price', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '原料价格数据导入',
            'href' => $this->url->link('purchase/sku_price', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['uploaded_file'] = $target;
        $data['action_confirm'] = $this->url->link('purchase/sku_price/upload_confirm', 'token=' . $this->session->data['token'], 'SSL');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('purchase/sku_price_upload_post.tpl', $data));
    }

    public function upload_confirm(){
        $time = time()+8*3600;
        $now = date("H:i", $time);

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['uploaded_file'] &&
            ($now>'02:00' || $now<'23:00') ){
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

            $product_ids = array(0);
            for($i=1; $i<=$allRow-1; $i++){
                $m=$i+1;

                $product_ids[] = (int)$currentSheet->getCell('A' . $m)->getValue();
            }

            //获取系统当前数据
            $sql = "SELECT A.sku_id, A.name FROM oc_x_sku A WHERE A.sku_id IN (" . implode(',', $product_ids) . ")";
            $query = $this->db->query($sql);

            $sysInfo = array();
            foreach($query->rows as $row){
                $sysInfo[$row['product_id']] = array(
                    'name' => $row['name']
                );
            }

            $products = array();
            for($i=1; $i<=$allRow-1; $i++){
                $m=$i+1;
                $products[$i]['sku_id'] = (int)$currentSheet->getCell('A' . $m)->getValue();
                $products[$i]['name']    = $currentSheet->getCell('B' . $m)->getValue();
                $products[$i]['total_cost']   = (float)$currentSheet->getCell('C' . $m)->getValue();
                $products[$i]['gross_weight']   = (float)$currentSheet->getCell('D' . $m)->getValue();
                $products[$i]['net_weight']   = (float)$currentSheet->getCell('E' . $m)->getValue();
                $products[$i]['gross_price']   = (float)$currentSheet->getCell('F' . $m)->getValue();
                $products[$i]['net_price']   = (float)$currentSheet->getCell('G' . $m)->getValue();
                $products[$i]['memo']   = $currentSheet->getCell('H' . $m)->getValue();

                $products[$i]['sys_name']   = isset($sysInfo[$products[$i]['product_id']]['name']) ? $sysInfo[$products[$i]['product_id']]['name'] : 'NULL';
            }

            // 写入数据库
            $user_id = $this->user->getId();

            $this->db->query('START TRANSACTION');
            $bool = 1;

            //替换已今日上传记录
            $sql = "UPDATE oc_x_product_price_upload set status = 0 WHERE date = DATE(NOW()) AND product_id IN(" . implode(',', $product_ids) . ")";
            $bool = $bool && $this->db->query($sql);

            //增加原料价格记录
            $sql = "INSERT INTO `oc_x_product_price_upload` (`date`, `time`, `product_id`, `last_retail_price`, `last_price`, `upload_last_retail_price`, `upload_retail_price`, `added_by`, `memo`, `checked`,  `status`) VALUES ";
            $m=0;
            foreach($products as $product){
                $sql .= "( DATE(NOW()), TIME(NOW()), ".$product['product_id'].", '".$product['last_retail_price']."', '". $product['last_price'] ."', '". $product['ori_retail_price'] ."', '".$product['retail_price']."', '".$user_id."', LEFT('".$product['memo']."',30), '0', '1')";
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

            $title = "原料价格维护－导入成功";
            if(!$bool){
                $title = "原料价格维护－导入错误";
            }

            // 显示成功页面
            $this->document->setTitle('原料价格维护');
            $data['heading_title'] = $title;
            $data['breadcrumbs'] = array();
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );
            $data['breadcrumbs'][] = array(
                'text' => '原料价格数据导入',
                'href' => $this->url->link('purchase/sku_price', 'token=' . $this->session->data['token'], 'SSL')
            );

            //$query = $this->db->query("SELECT p.product_id, pd.name, i.quantity FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id=pd.product_id LEFT JOIN oc_x_purchase_move_item AS i ON p.product_id=i.product_id WHERE p.status=1 ORDER BY p.product_id DESC");
            //$data['list'] = $products;

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput($this->load->view('purchase/sku_price.tpl', $data));
            $this->response->redirect($this->url->link('purchase/sku_price', 'token=' . $this->session->data['token'], 'SSL'));
        }else{
            exit('文件或文件上传时间请求错误.');
        }
    }

    public function getUploadPriceByDate(){
        $date = isset($_REQUEST['date']) ? $_REQUEST['date'] : date("Y-m-d", time()+8*3600);

        $sql = "select  A.product_id, B.name,  A.time, A.last_retail_price,  A.upload_last_retail_price,  A.upload_retail_price,  concat(C.lastname, C.firstname) addedby, C.username,  A.memo,  A.checked, D.upload_times, D.upload_history
            from oc_x_product_price_upload A
            left join oc_product_description B on A.product_id = B.product_id
            left join oc_user C on A.added_by  = C.user_id
            left join (select product_id, count(*) upload_times, group_concat(upload_retail_price) upload_history from oc_x_product_price_upload where date = '".$date."' group by product_id) D on A.product_id = D.product_id
            where A.date = '".$date."' and A.status = 1
            order by A.product_id";
        $query = $this->db->query($sql);

        echo json_encode($query->rows, JSON_UNESCAPED_UNICODE);
    }
}