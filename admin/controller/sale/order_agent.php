<?php
class ControllerSaleOrderAgent extends Controller
{
    // 实效打开页面
    public function index()
    {
        $this->document->setTitle('订单代理');
        $data['heading_title'] = '代客下单';
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
            'text' => '代客下单',
            'href' => $this->url->link('catalog/inventory', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action_adjust'] = $this->url->link('catalog/inventory/order_agent', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('catalog/order_agent.tpl', $data));
    }

    public function order_agent(){
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['products']){
            $products = $this->request->post['products'];
            $comment = $this->request->post['comment'];
            $inventory_type = $this->request->post['inventory_type'];

            // 写入数据库
            $time = time()+8*3600;
            $date = date("Y-m-d", $time);
            $date_added = date("Y-m-d H:i:s", $time);
            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName();

            $this->db->query("START TRANSACTION");
            $this->db->query("INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`) VALUES('1', '{$date}', '{$time}', '1', '" . $inventory_type . "', '{$date_added}', '{$user_id}', '{$user_name}', '{$comment}')");
            $inventory_move_id = $this->db->getLastId();
            $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`) VALUES';

            $m=0;
            foreach($products as $product){
                $sql .= "('{$inventory_move_id}', '{$product['station_id']}', '{$product['product_id']}', '{$product['quantity']}')";
                if(++$m < sizeof($products)){
                    $sql .= ', ';
                }
                else{
                    $sql .= ';';
                }
            }

            //$this->db->query("INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`) VALUES('{$inventory_move_id}', '1', '{$product['product_id']}', '{$product['quantity']}')");
            $this->db->query($sql);
            $this->db->query('COMMIT');

            $this->session->data['success'] = '调整成功!';
            $this->response->redirect($this->url->link('catalog/inventory', 'token=' . $this->session->data['token'], 'SSL'));
        }else{
            exit('请求错误.');
        }
    }

    public function inventoryList(){
        $sql = "SELECT p.product_id, pd.name, p.status, if(i.ori_inv is null, 0, i.ori_inv) ori_inv, if(i.quantity is null, 0, i.quantity) quantity, D.name station, D.station_id
                FROM oc_product AS p
                left join oc_x_station D on p.station_id = D.station_id
                INNER JOIN oc_product_description AS pd ON p.product_id=pd.product_id
                INNER JOIN (SELECT product_id, sum(if(order_id = 0, quantity, 0)) ori_inv, SUM(quantity) AS quantity FROM oc_x_inventory_move_item WHERE status=1 GROUP BY product_id) AS i ON p.product_id=i.product_id
                WHERE pd.language_id=2 and p.instock = 1
                ORDER BY product_id DESC";
        $query = $this->db->query($sql);
        echo json_encode($query->rows, JSON_UNESCAPED_UNICODE);
    }

    public function getProductInv($product_id){
        $json = array();

        $product_id = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : 0;
        if( $product_id > 0 ){
            $sql = "select A.product_id, A.station_id, A.status, B.name, if(C.ori_inv is null, 0, C.ori_inv) ori_inv, if(C.curr_inv is null, 0, C.curr_inv) curr_inv, D.name station
                from oc_product A
                left join oc_product_description B on A.product_id = B.product_id
                left join (select product_id, sum(if(order_id = 0, quantity, 0)) ori_inv, sum(quantity) curr_inv from oc_x_inventory_move_item where product_id = '".$product_id."') C on A.product_id = C.product_id
                left join oc_x_station D on A.station_id = D.station_id
                where A.product_id = '".$product_id."'";

            $query = $this->db->query($sql);
            $json = $query->row;
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);

        //$this->response->addHeader('Content-Type: application/json');
        //$this->response->setOutput(json_encode($json));
    }
}