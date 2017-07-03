<?php
class ControllerCatalogInventory extends Controller
{
    // 实效打开页面
    public function index()
    {
        $this->document->setTitle('库存管理');
        $data['heading_title'] = '库存管理';
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        if(isset($this->session->data['success']) && $this->session->data['success']){
            $data['success_msg'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '库存管理',
            'href' => $this->url->link('catalog/inventory', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action_reset']  = $this->url->link('catalog/inventory/reset_post', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_adjust'] = $this->url->link('catalog/inventory/adjust_post', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();
        $this->load->model('catalog/inventory');
        $data['inventory_type'] = $this->model_catalog_inventory->getInventoryType();
        $data['start_date']     = date('Y-m-d', time());
        $data['end_date']       = date('Y-m-d', time());
        $data['time_interval'] = is_null($this->config->get('config_inventory_query_time_interval')) ? 15 : $this->config->get('config_inventory_query_time_interval');
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();

        $this->response->setOutput($this->load->view('catalog/inventory.tpl', $data));
    }


    public function index_fresh()
    {
        $this->document->setTitle('生鲜可售库存');
        $data['heading_title'] = '生鲜可售库存';
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        if(isset($this->session->data['success']) && $this->session->data['success']){
            $data['success_msg'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '生鲜可售库存',
            'href' => $this->url->link('catalog/inventory', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action_reset']  = $this->url->link('catalog/inventory/reset_post', 'token=' . $this->session->data['token'], 'SSL');
        $data['action_adjust'] = $this->url->link('catalog/inventory/adjust_post', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();

        $this->response->setOutput($this->load->view('catalog/inventory_fresh.tpl', $data));
    }

    // 重置库存上传成功页面
    public function reset_post(){
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
        $target = DIR_UPLOAD . 'inventory_reset_' . date('YmdHis') . '_' . $this->user->getId() . '.' . $file_extension;
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
        if($allRow < 1){
            exit('上传文件为空.');
        }
        
        $product_ids = array();
        for($i=1; $i<=$allRow; $i++){
            $product_ids[] = (int)$currentSheet->getCell('A' . $i)->getValue();
        }
        $query = $this->db->query("SELECT product_id, name FROM oc_product_description WHERE product_id IN(" . implode(',', $product_ids) . ") AND language_id=2");
        $sys_names = array();
        foreach($query->rows as $row){
            $sys_names[$row['product_id']] = $row['name'];
        }
        
        $products = array();
        for($i=1; $i<=$allRow; $i++){
            $products[$i]['product_id'] = (int)$currentSheet->getCell('A' . $i)->getValue();
            $products[$i]['name']       = $currentSheet->getCell('B' . $i)->getValue();
            $products[$i]['sys_name']   = isset($sys_names[$products[$i]['product_id']]) ? $sys_names[$products[$i]['product_id']] : 'NULL';
            $products[$i]['quantity']   = (int)$currentSheet->getCell('C' . $i)->getValue();
        }

        
        $data['products'] = $products;
        //print_r($products);

        $this->document->setTitle('库存管理-重置库存');
        $data['heading_title'] = '重置库存';
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '库存管理',
            'href' => $this->url->link('catalog/inventory', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '重置库存',
            'href' => $this->url->link('catalog/inventory', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['uploaded_file'] = $target;
        $data['action_confirm'] = $this->url->link('catalog/inventory/reset_confirm', 'token=' . $this->session->data['token'], 'SSL');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('catalog/inventory_reset_post.tpl', $data));
    }

    // 重置库存确认导入数据库
    public function reset_confirm(){
        $now = (int)date("Hi", time()+8*3600);
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['uploaded_file'] &&
            ($now>=2105 && $now<2359) ){
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
            if($allRow < 1){
                exit('上传文件为空.');
            }

            // 获取商品数据
            $products = array();
            for($i=1; $i<=$allRow; $i++){
                $products[$i]['product_id'] = (int)$currentSheet->getCell('A' . $i)->getValue();
                $products[$i]['name']       = $currentSheet->getCell('B' . $i)->getValue();
                $products[$i]['quantity']   = (int)$currentSheet->getCell('C' . $i)->getValue();
            }

            // 开始写入数据
            $time = time()+8*3600;
            $date = date("Y-m-d", $time);
            $date_added = date("Y-m-d H:i:s", $time);
            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName();

            $this->db->query('START TRANSACTION');
            $bool = 1;

            // 记录生鲜当日可售库存变动
            $sql = "
            INSERT INTO oc_x_inventory_move_item_history(inventory_move_item_id, inventory_move_id, station_id, due_date, product_id, price, product_batch, quantity, weight, weight_class_id, is_gift, checked, status)
            SELECT inventory_move_item_id, inventory_move_id, station_id, due_date, product_id, price, product_batch, quantity, weight, weight_class_id, is_gift, checked, status
            FROM oc_x_inventory_move_item WHERE station_id = 1
            ";
            $bool = $bool && $this->db->query($sql);

            // 删除生鲜当日可售库存变动
            $bool = $bool && $this->db->query("DELETE FROM oc_x_inventory_move_item WHERE station_id = 1");

            // 写入生鲜商品可售库存记录
            $bool = $bool && $this->db->query("INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`) VALUES('1', '{$date}', '{$time}', '1', '1', '{$date_added}', '{$user_id}', '{$user_name}')");
            $inventory_move_id = $this->db->getLastId();

            $sql = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `product_id`, `quantity`) VALUES ";
            $m=0;
            foreach($products as $product){
                $sql .= "(".$inventory_move_id.", 1, '".$product['product_id']."', '".$product['quantity']."')";
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

            // 显示成功页面
            $this->document->setTitle('库存管理-重置库存');
            $data['heading_title'] = '重置库存';
            $data['breadcrumbs'] = array();
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
            );
            $data['breadcrumbs'][] = array(
                'text' => '库存管理',
                'href' => $this->url->link('catalog/inventory', 'token=' . $this->session->data['token'], 'SSL')
            );
            $data['breadcrumbs'][] = array(
                'text' => '重置库存',
                'href' => $this->url->link('catalog/inventory', 'token=' . $this->session->data['token'], 'SSL')
            );
            $query = $this->db->query("SELECT p.product_id, pd.name, i.quantity FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id=pd.product_id LEFT JOIN oc_x_inventory_move_item AS i ON p.product_id=i.product_id WHERE p.status=1 ORDER BY p.product_id DESC");
            $data['list'] = $query->rows;
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput($this->load->view('catalog/inventory_reset_success.tpl', $data));
        }else{
            exit('请求错误.');
        }
    }

    public function adjust_post(){
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['products']){
            $products = $this->request->post['products'];
            $comment = $this->request->post['comment'];
            $inventory_type = $this->request->post['inventory_type'];
            $warehouse_id = $this->request->post['global_warehouse_id'];

            // 写入数据库
            $time = time()+8*3600;
            $date = date("Y-m-d", $time);
            $date_added = date("Y-m-d H:i:s", $time);
            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName();

            $this->db->query("START TRANSACTION");
            $bool = true;
            //获取需要插入的station_id
            $sql = "select station_id from oc_x_warehouse where warehouse_id = '".$warehouse_id."'";
            $query = $this->db->query($sql);
            if($query->num_rows){
                $station_id = $query->row['station_id'];
            }else{
                $bool = false;
            }

            $sql = "INSERT INTO oc_x_inventory_move (`station_id`,`warehouse_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`) VALUES('1', '{$warehouse_id}','{$date}', '{$time}', '1', '" . $inventory_type . "', '{$date_added}', '{$user_id}', '{$user_name}', '{$comment}')";
//            var_dump($sql);die;
            $bool = $bool && $this->db->query("INSERT INTO oc_x_inventory_move (`station_id`,`warehouse_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`) VALUES('1', '{$warehouse_id}','{$date}', '{$time}', '1', '" . $inventory_type . "', '{$date_added}', '{$user_id}', '{$user_name}', '{$comment}')");
            $inventory_move_id = $this->db->getLastId();
            $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `warehouse_id`, `product_id`, `quantity`) VALUES';

            $m=0;
            foreach($products as $product){
                $sql .= "('{$inventory_move_id}', '{$product['station_id']}', '{$warehouse_id}','{$product['product_id']}', '{$product['quantity']}')";
                if(++$m < sizeof($products)){
                    $sql .= ', ';
                }
                else{
                    $sql .= ';';
                }
            }

            //$this->db->query("INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`) VALUES('{$inventory_move_id}', '1', '{$product['product_id']}', '{$product['quantity']}')");
            $bool = $bool && $this->db->query($sql);

            //调整库存的时候，需要更新oc_product_inventory表中的inventory
            $sql = "update oc_product_inventory oi
                right join oc_x_inventory_move_item A on A.station_id = oi.station_id and A.warehouse_id = oi.warehouse_id and A.product_id = oi.product_id
                left join oc_x_inventory_move B on A.inventory_move_id = B.inventory_move_id
                set oi.inventory = oi.inventory+A.quantity
                where B.inventory_move_id = '".(int)$inventory_move_id ."'";

            $bool = $bool && $this->db->query($sql);

            if($bool){
                $this->db->query('COMMIT');
            }else{
                $this->db->query('ROLLBACK');
            }

            $this->session->data['success'] = '调整成功!';
            $this->response->redirect($this->url->link('catalog/inventory', 'token=' . $this->session->data['token'], 'SSL'));
        }else{
            exit('请求错误.');
        }
    }

    public function inventoryList(){
        $warehouse_id = !empty($this->request->get['warehouse_id']) ? (int)$this->request->get['warehouse_id'] : 0;

        $sql = "SELECT p.product_id, pd.name, p.status, p.safestock, if(i.ori_inv is null, 0, i.ori_inv) ori_inv, if(i.quantity is null, 0, i.quantity) quantity, D.name station, D.station_id, W.title warehouse
                FROM oc_product AS p
                left join oc_x_station D on p.station_id = D.station_id
                INNER JOIN oc_product_description AS pd ON p.product_id=pd.product_id
                INNER JOIN (SELECT product_id, sum(if(order_id = 0, quantity, 0)) ori_inv, SUM(quantity) AS quantity FROM oc_x_inventory_move_item WHERE status=1 GROUP BY product_id) AS i ON p.product_id=i.product_id
                LEFT JOIN oc_product_to_warehouse PW ON p.product_id = PW.product_id
                LEFT JOIN oc_x_warehouse W ON PW.warehouse_id = W.warehouse_id
                WHERE pd.language_id=2 and p.instock = 1 and p.station_id = '".STATION_FAST_MOVE."'";

        !empty($warehouse_id) && $sql .= " AND PW.warehouse_id = ". $warehouse_id;
        $sql .= " ORDER BY p.product_id DESC";

        $query = $this->db->query($sql);

        echo json_encode($query->rows, JSON_UNESCAPED_UNICODE);
    }

    public function inventoryListFresh(){
        $warehouse_id = !empty($this->request->get['warehouse_id']) ? (int)$this->request->get['warehouse_id'] : 0;

        $sql = "SELECT p.product_id, pd.name, p.status, p.safestock, if(i.ori_inv is null, 0, i.ori_inv) ori_inv, if(i.quantity is null, 0, i.quantity) quantity, D.name station, D.station_id, W.title warehouse
                FROM oc_product AS p
                left join oc_x_station D on p.station_id = D.station_id
                INNER JOIN oc_product_description AS pd ON p.product_id=pd.product_id
                INNER JOIN (SELECT product_id, sum(if(order_id = 0, quantity, 0)) ori_inv, SUM(quantity) AS quantity FROM oc_x_inventory_move_item WHERE status=1 GROUP BY product_id) AS i ON p.product_id=i.product_id
                LEFT JOIN oc_product_to_warehouse PW ON p.product_id = PW.product_id
                LEFT JOIN oc_x_warehouse W ON PW.warehouse_id = W.warehouse_id
                WHERE pd.language_id=2 and p.instock = 1 and p.station_id = '".STATION_FRESH."'";

        !empty($warehouse_id) && $sql .= " AND PW.warehouse_id = ". $warehouse_id;
        $sql .= " ORDER BY p.product_id DESC";

        $query = $this->db->query($sql);
        echo json_encode($query->rows, JSON_UNESCAPED_UNICODE);
    }

    public function getProductInv($product_id){
        $json = array();

        $product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
        $warehouse_id = !empty($this->request->get['warehouse_id']) ? (int)$this->request->get['warehouse_id'] : 0;
        if( $product_id > 0 ){
            $sql = "select
                A.product_id, A.station_id, A.status, B.name, A.price sale_price, A.real_cost, if(C.ori_inv is null, 0, C.ori_inv) ori_inv, if(C.curr_inv is null, 0, C.curr_inv) curr_inv,
                D.name station, W.title warehouse, s.supplier_unit_size, s.price as purchase_price, A.inv_size,
                s.supplier_order_quantity_type,s.supplier_id
                from oc_product A
                left join oc_product_description B on A.product_id = B.product_id
                left join (select product_id, sum(if(order_id = 0, quantity, 0)) ori_inv, sum(quantity) curr_inv from oc_x_inventory_move_item where product_id = '".$product_id."') C on A.product_id = C.product_id
                left join oc_x_station D on A.station_id = D.station_id
                left join oc_x_sku as s on s.sku_id = A.sku_id
                LEFT JOIN oc_product_to_warehouse PW ON A.product_id = PW.product_id
                LEFT JOIN oc_x_warehouse W ON PW.warehouse_id = W.warehouse_id
                where A.product_id = '".$product_id."'";

            !empty($warehouse_id) && $sql .= " AND PW.warehouse_id = ".$warehouse_id;

            $query = $this->db->query($sql);
            $json = $query->row;
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);

        //$this->response->addHeader('Content-Type: application/json');
        //$this->response->setOutput(json_encode($json));
    }

    public function getProductInventoryHistory()
    {
        $post = $this->request->post;
        if(!isset($post['product_ids']) || empty($post['product_ids'])){ echo json_encode(array()); exit; }
        if(!isset($post['start_date'])  || empty($post['start_date'])) { echo json_encode(array()); exit; }
        if(!isset($post['end_date'])    || empty($post['end_date']))   { echo json_encode(array()); exit; }

        // 两个日期之间的限制
        $time_interval = is_null($this->config->get('config_inventory_query_time_interval')) ? 15 : $this->config->get('config_inventory_query_time_interval');
        if(((strtotime($post['end_date']) - strtotime($post['start_date']))/24/36000) > $time_interval ){
            echo json_encode(array()); exit;
        }

        if(!empty($this->request->post['page'])){
            $page = $this->request->post['page'];
        }else{
            $page = 1;
        }
        $page_size = $this->config->get('config_limit_admin');

        $this->load->model('catalog/inventory');
        $this->load->model('station/station');
        $inventory_num  = $this->model_catalog_inventory->getProductInventoryHistoryCount($post);
        $inventory_data = $this->model_catalog_inventory->getProductInventoryHistory($post, $page, $page_size);
        $inventory_type = $this->model_catalog_inventory->getInventoryType();
        $warehouse_data = $this->model_station_station->getWarehouseAndStation();

        $station = $warehouse = $type = array();
        foreach($warehouse_data as $val){
            $station[$val['station_id']]     = $val['name'];
            $warehouse[$val['warehouse_id']] = $val['title'];
        }
        foreach($inventory_type as $value){
            $type[$value['inventory_type_id']] = $value['name'];
        }

        foreach($inventory_data as &$v){
            $v['warehouse']     = !empty($warehouse[$v['warehouse_id']]) ? $warehouse[$v['warehouse_id']] : '';
            $v['station']       = !empty($station[$v['station_id']])     ? $station[$v['station_id']]     : '';
            $v['type']          = !empty($type[$v['inventory_type_id']]) ? $type[$v['inventory_type_id']] : '';
            $v['add_user_name'] = !is_null($v['add_user_name'])          ? $v['add_user_name']            : '';
        }
        $data['inventory_data'] = $inventory_data;

        $pagination         = new Pagination();
        $pagination->total  = $inventory_num;
        $pagination->page   = $page;
        $pagination->limit  = $page_size;
        $pagination->url    = "javascript:void(0)\" onclick='getProductInventoryHistory(this)' \"";

        $data['pagination'] = $pagination->render();
        $data['results']    = sprintf($this->language->get('text_pagination'), ($inventory_num) ? (($page - 1) * $page_size) + 1 : 0, ((($page - 1) * $page_size) > ($inventory_num - $page_size)) ? $inventory_num : ((($page - 1) * $page_size) + $page_size), $inventory_num, ceil($inventory_num / $page_size));
        $data['show_input'] = ceil($inventory_num / $page_size) > 9 ? 1 : 0;

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    public function getProductInventoryOnTheWay()
    {
        $post = $this->request->post;

        $this->load->model('catalog/inventory');
        $this->load->model('station/station');

        if(!empty($this->request->post['page'])){
            $page = $this->request->post['page'];
        }else{
            $page = 1;
        }
        $page_size = $this->config->get('config_limit_admin');

        $fields         = "p.product_id, pd.name, p.station_id, pw.warehouse_id, pi.inventory";
        $inventory_num  = $this->model_catalog_inventory->getProductInventoryCount($post);
        $inventory_data = $this->model_catalog_inventory->getProductInventory($post, $fields, $page, $page_size, '', 'p.product_id, pw.warehouse_id');

        if(empty($post['product_ids']) && !empty($inventory_data)){
            $product_ids = array();
            foreach($inventory_data as $value){
                $product_ids[] = $value['product_id'];
            }
            $post['product_ids'] = implode(',', $product_ids);
        }
        $fields         = "pop.product_id, SUM(IF(pop.quantity IS NULL, 0, pop.quantity)) quantity";
        $purchase_data  = $this->model_catalog_inventory->getProductInventoryOnTheWay($post, $fields, 0, 0, '', 'pop.product_id');
        $warehouse_data = $this->model_station_station->getWarehouseAndStation();

        $station = $warehouse = $on_the_way = array();
        foreach($warehouse_data as $val){
            $station[$val['station_id']]     = $val['name'];
            $warehouse[$val['warehouse_id']] = $val['title'];
        }

        if(!empty($purchase_data)){
            foreach($purchase_data as $value){
                $on_the_way[$value['product_id']] = $value['quantity'];
            }
        }

        if(!empty($inventory_data)){
            foreach($inventory_data as &$v){
                $v['warehouse']     = !empty($warehouse[$v['warehouse_id']])    ? $warehouse[$v['warehouse_id']]    : '';
                $v['station']       = !empty($station[$v['station_id']])        ? $station[$v['station_id']]        : '';
                $v['on_the_way']    = !empty($on_the_way[$v['product_id']])     ? $on_the_way[$v['product_id']]     : 0;
            }
        }

        $data['inventory_data'] = $inventory_data;


        $pagination         = new Pagination();
        $pagination->total  = $inventory_num;
        $pagination->page   = $page;
        $pagination->limit  = $page_size;
        $pagination->url    = "javascript:void(0)\" onclick='getProductInventoryOnTheWay(this)' \"";

        $data['pagination'] = $pagination->render();
        $data['results']    = sprintf($this->language->get('text_pagination'), ($inventory_num) ? (($page - 1) * $page_size) + 1 : 0, ((($page - 1) * $page_size) > ($inventory_num - $page_size)) ? $inventory_num : ((($page - 1) * $page_size) + $page_size), $inventory_num, ceil($inventory_num / $page_size));
        $data['show_input'] = ceil($inventory_num / $page_size) > 9 ? 1 : 0;

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    public function getTodayInventoryMove($product_id){
        $json = array();

        $product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
        if( $product_id > 0 ){
            $sql = "select A.inventory_move_id, A.inventory_type_id, T.name, A.station_id, A.date_added, B.product_id, B.quantity, B.order_id, B.customer_id, C.firstname, C.merchant_name,
                    concat( left(C.firstname,9),  if(CHAR_LENGTH(C.firstname) > 9, '...', '') ) short_firstname,
                    concat( left(C.merchant_name,9),  if(CHAR_LENGTH(C.merchant_name) > 9, '...', '') ) short_merchant_name,
                    B.status
                    from oc_x_inventory_move A
                    left join oc_x_inventory_move_item B on A.inventory_move_id = B.inventory_move_id
                    left join oc_customer C on B.customer_id = C.customer_id
                    left join oc_x_inventory_type T on A.inventory_type_id = T.inventory_type_id
                    where date(A.date_added) between date_sub(current_date(), interval 1 day) and current_date() and B.product_id ='".$product_id."'";

            $query = $this->db->query($sql);
            $json = $query->row;
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getStockMoveInfo($product_id){
        $json = array();

        $product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
        if( $product_id > 0 ){
            $sql = "select concat(C.name, '-',A.inventory_type_id) stock_move_type,
                max(A.date_added) last_op_time,
                 if(A.inventory_type_id = 11, group_concat( concat(date(A.date_added),C.name,B.quantity, WCD.title) ), '' ) qty_list,
                if(A.inventory_type_id = 14, substring_index(group_concat(B.quantity order by A.date_added desc),',',1), sum(B.quantity)) sub_total,
                sum(B.quantity) 数量
                from oc_x_stock_move A
            left join oc_x_stock_move_item B on A.inventory_move_id = B.inventory_move_id
            left join oc_product P on B.product_id = P.product_id
            left join oc_weight_class_description WCD on P.weight_class_id = WCD.weight_class_id
            left join oc_x_inventory_type C on A.inventory_type_id = C.inventory_type_id
            where B.status = 1
            and B.product_id = '".$product_id."'";

            $query = $this->db->query($sql);
            $json = $query->row;
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
}