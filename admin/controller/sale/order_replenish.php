<?php
class ControllerSaleOrderReplenish extends Controller
{
    // 实效打开页面
    public function index()
    {
        $this->document->setTitle('订单补货');
        $data['heading_title'] = '订单补货';
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        if(isset($this->session->data['success']) && $this->session->data['success']){
            $data['success_msg'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        if (isset($this->session->data['warning'])) {
            $data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '订单补货管理',
            'href' => $this->url->link('sale/order_replenish', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action_adjust'] = $this->url->link('sale/order_replenish/adjust_post', 'token=' . $this->session->data['token'], 'SSL');

        $data['gift_list'] = $this->getGiftList();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('sale/order_replenish.tpl', $data));
    }

    public function adjust_post(){
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['lists']){
            $lists = $this->request->post['lists'];
            $comment = $this->request->post['comment'];

            // 写入数据库
            $user_id = $this->user->getId();
            $user_name = $this->user->getUserName();

            //TODO 检查多行记录，不可重复添加
            //TODO 库存处理
            //TODO 取消补货计划，删除已补货商品
            //TODO 不存在补货记录，仍可补货?
            foreach($lists as $list){
                //{ [0]=> array(4) { ["order_id"]=> string(6) "130591" ["target_order"]=> string(1) "1" ["product_id"]=> string(4) "7257" ["qty"]=> string(1) "1" } }
                //target_order = 1, 系统监控自动执行
                //target_order > 1, 手动选择订单立即执行，订单号与原缺货订单用户需匹配

                //检查是否已存在添加记录
                $sql = "SELECT * FROM oc_x_order_replenish WHERE order_id = '".(int)$list['order_id']."' AND product_id = '".(int)$list['product_id']."' AND status = 1";
                $checkExitReplenishPlan = $this->db->query($sql);
                if(sizeof($checkExitReplenishPlan->rows)){
                    $this->session->data['warning'] = '添加订单＃'.(int)$list['order_id'].'补货失败，对应赠品补货计划已存在。';
                    $this->response->redirect($this->url->link('sale/order_replenish', 'token=' . $this->session->data['token'], 'SSL'));
                }
                else{
                    if((int)$list['target_order']>1){
                        $sql = "SELECT * FROM oc_order_product WHERE order_id = '".(int)$list['target_order']."' AND product_id = '".(int)$list['product_id']."'";
                        $checkExitGift = $this->db->query($sql);
                        $tmpsql = $sql;
                        if(sizeof($checkExitGift->rows)){
                            $sql = "UPDATE oc_order_product SET quantity = quantity + ".(int)$list['qty']." WHERE order_id = '".(int)$list['target_order']."' AND product_id = '".(int)$list['product_id']."'";
                        }
                        else{
                            $sql = "INSERT INTO `oc_order_product` (`order_id`, `product_id`, `weight_inv_flag`, `name`, `model`, `quantity`, `price`, `total`, `tax`, `reward`, `price_ori`, `retail_price`, `is_gift`, `shipping`, `status`)
                            SELECT '".(int)$list['target_order']."' as order_id, '".(int)$list['product_id']."' as product_id, '0' weight_inv_flag, name, model, '".(int)$list['qty']."' as quantity, '0' as price, '0' as total, '0' as tax, '0' as reward, '0' as price_ori, '0' as retail_price, is_gift, shipping, '1' as status
                            FROM oc_product where product_id = '".(int)$list['product_id']."' and is_gift = 1";
                        }
                        $this->db->query($sql);

                        if(1) //TODO 检测执行影响商品行数 mysql_affected_rows()
                        {
                            $sql = "INSERT INTO `oc_x_order_replenish` (`order_id`, `deliver_date`, `customer_id`, `product_id`, `quantity`, `is_gift`, `date_added`, `added_by`, `status`, `target_order_id`, `auto_execute`, `executed`, `date_executed`, `executed_by`, `comment`)
                            SELECT order_id, deliver_date, customer_id, '".(int)$list['product_id']."' as product_id, '".(int)$list['qty']."' as quantity, '1' as is_gift, NOW(), '".$user_id."' as added_by, '1' as status, '".(int)$list['target_order']."' as target_order_id, '0' auto_execute, '1' executed, NOW(), '".$user_id."' as executed_by, '".$comment."' as comment
                            FROM oc_order WHERE order_id = '".(int)$list['order_id']."' and order_status_id not in ('".CANCELLED_ORDER_STATUS."')";
                            $this->db->query($sql);
                        }
                    }
                    else{
                        $sql = "INSERT INTO `oc_x_order_replenish` (`order_id`, `deliver_date`, `customer_id`, `product_id`, `quantity`, `is_gift`, `date_added`, `added_by`, `status`, `target_order_id`, `auto_execute`, `executed`, `date_executed`, `executed_by`, `comment`)
                            SELECT order_id, deliver_date, customer_id, '".(int)$list['product_id']."' as product_id, '".(int)$list['qty']."' as quantity, '1' as is_gift, NOW(), '".$user_id."' as added_by, '1' as status, '0' as target_order_id, '1' auto_execute, '0' executed, '0000-00-00 00:00:00' as date_executed, '0' as executed_by, '".$comment."' as comment
                            FROM oc_order WHERE order_id = '".(int)$list['order_id']."' and order_status_id not in ('".CANCELLED_ORDER_STATUS."')";
                        $this->db->query($sql);
                    }

                    $this->session->data['success'] = '补送计划添加完成';
                    $this->response->redirect($this->url->link('sale/order_replenish&tab=list', 'token=' . $this->session->data['token'], 'SSL'));
                }
            }
        }else{
            exit('请求错误.');
        }
    }

    public function getGiftList(){
        $sql = "select P.product_id, PD.name from oc_product P
                left join oc_product_description PD on P.product_id = PD.product_id
                where P.is_gift = 1 and P.is_replenish_gift = 1 and P.price = 0;
        ";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getOrderReplenishList(){
        //TODO 按时间查询，按订单查询
        $json = array();
        $sql = "select  A.`order_replenish_id`, A.`order_id` replenish_order_id, A.`product_id`, P.name product_name, A.`quantity`, A.`date_added`, A.`added_by`, B.username added_username,
            A.`status`,  A.`target_order_id`, A.`auto_execute`, A.`executed`, A.`date_executed`, A.`executed_by`,  C.username executed_username,
            A. `comment`,
            O.order_id, O.deliver_date, O.customer_id, O.sub_total, O.total, BD.bd_name, O.station_id, S.name station, OS.name order_status, O.payment_method, OPS.name payment_status, ODS.name deliver_status,
            concat('[',O.customer_id,'][',O.firstname,'][',O.shipping_firstname,'][',O.shipping_address_1,']') customer_info,
            concat(left(concat('[ID:',O.customer_id,'][',O.firstname,'][',O.shipping_address_1,']'),15), '...') customer_brief_info
            from oc_x_order_replenish A
            left join oc_user B on A.added_by = B.user_id
            left join oc_user C on A.executed_by = C.user_id
            left join oc_order O on A.target_order_id = O.order_id
            left join oc_x_station S on O.station_id = S.station_id
            left join oc_order_status OS on O.order_status_id = OS.order_status_id
            left join oc_order_payment_status OPS on O.order_payment_status_id = OPS.order_payment_status_id
            left join oc_order_deliver_status ODS on O.order_deliver_status_id = ODS.order_deliver_status_id
            left join oc_x_bd BD on O.bd_id = BD.bd_id
            left join oc_product P on A.product_id = P.product_id
            order by A.order_replenish_id desc";
        $query = $this->db->query($sql);
        $json = $query->rows;

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getOrderInfo($order_id){
        $json = array();

        $order_id = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : 0;
        if($order_id > 0){
            $sql = "select O.order_id, O.customer_id, O.sub_total, O.total, BD.bd_name, O.station_id, S.name station, OS.name order_status, O.payment_method, OPS.name payment_status, ODS.name deliver_status,
                concat('[',O.customer_id,'][',O.firstname,'][',O.shipping_firstname,'][',O.shipping_address_1,']') customer_info,
                concat(left(concat('[',O.customer_id,'][',O.firstname,'][',O.shipping_firstname,'][',O.shipping_address_1,']'),25), '...') customer_brief_info,
                O.order_status_id, O.order_payment_status_id, O.order_deliver_status_id
                from oc_order O
                left join oc_x_station S on O.station_id = S.station_id
                left join oc_order_status OS on O.order_status_id = OS.order_status_id
                left join oc_order_payment_status OPS on O.order_payment_status_id = OPS.order_payment_status_id
                left join oc_order_deliver_status ODS on O.order_deliver_status_id = ODS.order_deliver_status_id
                left join oc_x_bd BD on O.bd_id = BD.bd_id
                where O.order_id = '".$order_id." and O.type = 1'
                ";

            $query = $this->db->query($sql);
            $json = $query->row;

            //由限明天配送日期，改为查找未配送状态，Alex- 2017-07-09
            if(isset($json['customer_id'])){
                $sql = "select order_id, deliver_date from oc_order
                        where type = 1 and customer_id = '".$json['customer_id']."'
                        and station_id = '".$json['station_id']."'
                        and order_status_id not in (3) and order_deliver_status_id = 1
                        ";
                $query = $this->db->query($sql);
                $json['target_orders'] = $query->rows;
            }
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }


    public function getOrderProductOutStockHistory($order_id){
        $json = array();

        $order_id = isset($this->request->get['order_id']) ? $this->request->get['order_id'] : 0;
        if( $order_id > 0 ){
            $sql = "select O.order_id, O.customer_id, BD.bd_name, O.date_added, date(O.date_added) adate, O.deliver_date,
                    O.customer_id, O.firstname, O.shipping_firstname, O.shipping_address_1,
                    concat('[',O.customer_id,'][',O.firstname,'][',O.shipping_firstname,'][',O.shipping_address_1,']') customer_info,
                    concat(left(concat('[',O.customer_id,'][',O.firstname,'][',O.shipping_firstname,'][',O.shipping_address_1,']'),25), '...') customer_brief_info,
                    OP.product_id,  OP.name product_name,
                    concat('#',OP.product_id,', ',OP.name) product_info,
                    ORT.return_quantity order_replenish_qty
                    from oc_order O
                    left join oc_x_bd BD on O.bd_id = BD.bd_id
                    left join oc_order_product OP on O.order_id = OP.order_id
                    right join oc_product P on OP.product_id = P.product_id
                    left join (
                        select ort.order_id, ortp.product_id, sum(ortp.quantity) as return_quantity from
                            oc_return as ort
                            left join oc_order as o on ort.order_id = o.order_id
                            left join oc_return_product as ortp on ort.return_id  = ortp.return_id
                        where o.deliver_date between date_sub(current_date(), interval 90 day) and current_date()
                            and o.order_status_id not in (3)
                            and o.order_deliver_status_id in (2,3)
                            and o.station_id = 2
                            and ort.return_status_id = 2
                            group by ort.order_id, ortp.product_id
                    ) ORT on O.order_id = ORT.order_id and OP.product_id = ORT.product_id
                    where
                    O.customer_id = (select customer_id from oc_order where order_id = '".$order_id."')
                        and P.is_gift = 1
                        and O.order_status_id not in (3)
                        and O.order_deliver_status_id in (2,3)
                        and O.station_id = 2
                        and ORT.return_quantity > 0
                    ";

            $query = $this->db->query($sql);
            $json = $query->rows;

            for($m=0; $m<sizeof($json); $m++){
                $json[$m]['order_replenish_id'] = 0;
                $sql = "select order_replenish_id from oc_x_order_replenish where order_id = '".$json[$m]['order_id']."' and product_id = '".$json[$m]['product_id']."' and status = 1";
                $query = $this->db->query($sql);
                if(sizeof($query->row)){
                    $orderReplenishId = $query->row;
                    $json[$m]['order_replenish_id'] = $orderReplenishId['order_replenish_id'];
                }
            }
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
}