<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/9/27
 * Time: 10:12
 */
class ControllerLogisticLogisticAllotOrder extends Controller
{
    public function index(){
        ini_set('date.timezone','Asia/Shanghai');

        $this->document->setTitle('订单分派线路');
        $data['heading_title'] = '订单分派线路';
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '订单分派线路',
            'href' => $this->url->link('logistic/logistic_allot_order', 'token=' . $this->session->data['token'], 'SSL')
        );

        $this->load->model('logistic/logistic');
        $data['line_data'] =  json_encode( $this->model_logistic_logistic->getLineData(), JSON_UNESCAPED_UNICODE );

        $this->load->model('station/station');
        $data['stations'] = $this->model_station_station->getStationList();
        $data['order_status'] = $this->model_logistic_logistic->getOrderStauts();
        $data['slots'] = $this->model_logistic_logistic->getSlotList();
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['filterDefault'] = array(
            'station_id' => 2,
            'deliver_date' => $date = date("Y-m-d", strtotime("0 day")), //Tomorrow
            'deliver_slot_id' => 4,
            'order_status_id' => 0, //默认全部
        );

        $this->response->setOutput($this->load->view('logistic/logistic_allot_order.tpl', $data));
    }

    public function getLine(){
        $date = $this->request->get['date'];
        $this->load->model('logistic/logistic');
        echo json_encode( $this->model_logistic_logistic->getLineData($date), JSON_UNESCAPED_UNICODE );
    }

    public function getAllotOrder(){
        $date = $this->request->get['date'];
        $station_id = $this->request->get['station_id'];
        $order_status_id = $this->request->get['order_status_id'];
        $classify = $this->request->get['classify'];
        $deliver_slot_id = $this->request->get['deliver_slot_id'];
        $logistic_index = $this->request->get['logistic_index'];
        $this->load->model('logistic/logistic');
        $this->load->model('station/station');

        $data = $this->model_logistic_logistic->getAllotOrder($date,$station_id,$order_status_id,$classify,$deliver_slot_id,$logistic_index);

        if(!empty($data)){
            foreach($data as &$value){
                $order = $this->model_logistic_logistic->getOrderInv($value['order_id']);
                if(empty($order)){
                    $num=0;
                    $orderQuantity = $this->model_logistic_logistic->getOrderQuantity($value['order_id']);
                    foreach($orderQuantity as $val){
                        $num +=$val['quantity'];
                    }
                    $num = '[未拣]共'.$num.'件';
                }else{
                    $num = '';
                    if(!empty($order['frame_count']) || !empty($order['frame_meat_count'])){
                        $num .= '框:'.((int)$order['frame_count'] + (int)$order['frame_meat_count']);
                    }
                    if(!empty($order['incubator_count']) || !empty($order['incubator_mi_count'])){
                        $num .= '保:'.($order['incubator_count'] + $order['incubator_mi_count']);
                    }
                    if(!empty($order['foam_count']) || !empty($order['foam_ice_count'])){
                        $num .= '泡:'.((int)$order['foam_count'] + (int)$order['foam_ice_count']);
                    }
                    if(!empty($order['frame_mi_count']) || !empty($order['frame_ice_count'])){
                        $num .= '奶框:'.((int)$order['frame_mi_count'] + (int)$order['frame_ice_count']);
                    }
                    if(!empty($order['box_count'])){
                        $num .= '箱:'.($order['box_count']);
                    }
                }
                $value['station_name'] = $this->model_station_station->getStationNameById($value['station_id']);
                $value['num'] = $num;
            }
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE );
    }

    public function updateLogisticIndex(){
        $orderLogisticIndex = isset($this->request->post['orderLogisticIndex']) ? $this->request->post['orderLogisticIndex'] : array();
        $orderList = isset($this->request->post['orderList']) ? $this->request->post['orderList'] : array();

        $date = isset($this->request->post['date']) ? $this->request->post['date'] : false;
        $station_id = isset($this->request->post['station_id']) ? $this->request->post['station_id'] : false;
        $deliver_slot_id = isset($this->request->post['deliver_slot_id']) ? $this->request->post['deliver_slot_id'] : false;

        if(!$date || !$station_id || !$deliver_slot_id){
            echo json_encode(array('error'=>'未指定仓库，配送日期或时间段'), JSON_UNESCAPED_UNICODE);
        }

        $this->load->model('logistic/logistic');
        $data = array();

        $data = $this->model_logistic_logistic->updateLogisticIndex($orderLogisticIndex, $date, $station_id, $deliver_slot_id );

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function addAllotOrder(){
        $station_id = isset($this->request->post['station_id']) ? $this->request->post['station_id'] : false;
        $deliver_date = isset($this->request->post['deliver_date']) ? $this->request->post['deliver_date'] : false;
        $deliver_slot_id = isset($this->request->post['deliver_slot_id']) ? $this->request->post['deliver_slot_id'] : false;
        $logistic_line_id =  isset($this->request->post['logistic_line_id']) ? $this->request->post['logistic_line_id'] : false;
        $logistic_driver_id =  isset($this->request->post['logistic_driver_id']) ? $this->request->post['logistic_driver_id'] : false;
        $logistic_van_id =  isset($this->request->post['logistic_van_id']) ? $this->request->post['logistic_van_id'] : false;

        $order_ids =  (isset($this->request->post['order_ids']) && is_array($this->request->post['order_ids'])) ? $this->request->post['order_ids'] : array();

        $logistic_deliveryman_id =  isset($this->request->post['logistic_deliveryman_id']) ? $this->request->post['logistic_deliveryman_id'] : false;

        if(!$station_id || !$deliver_date || !$deliver_slot_id || !$logistic_line_id || !$logistic_driver_id || !$logistic_van_id || !sizeof($order_ids)){
            exit('false');
        }

        $this->load->model('logistic/logistic');

        //检测是否线路已有分配记录
        $sql = "select min(logistic_allot_id) logistic_allot_id
            from oc_x_logistic_allot
            where station_id = '".$station_id."'
                and deliver_date = '".$deliver_date."'
                and deliver_slot_id = '".$deliver_slot_id."'

                and logistic_line_id = '".$logistic_line_id."'
                and logistic_driver_id = '".$logistic_driver_id."'
                ";
        $checkData = $this->db->query($sql)->row;
        $logistic_allot_id = $checkData['logistic_allot_id'];

        //添加分派记录
        if(!$logistic_allot_id){
            $line_data = $this->model_logistic_logistic->getLineInfo($logistic_line_id);
            //$line_data['logistic_line_id'] = $logistic_line_id;
            $van_data  = $this->model_logistic_logistic->getVanInfo($logistic_van_id);
            //$van_data['logistic_van_id'] = $logistic_van_id;
            $driver_data = $this->model_logistic_logistic->getDriverInfo($logistic_driver_id);
            //$driver_data['logistic_driver_id'] = $logistic_driver_id;
            //$data = array_merge($line_data,$driver_data,$van_data);
            if($logistic_deliveryman_id){
                $deliveryman_data = $this->model_logistic_logistic->getDeliverymanInfo($logistic_deliveryman_id);
                //$deliveryman_data['logistic_deliveryman_id'] = $logistic_deliveryman_id;
                //$data = array_merge($data,$deliveryman_data);
            }

            $targetTable = 'oc_x_logistic_allot';
            $rowData = array(
                'station_id' => $station_id,
                'deliver_date' => $deliver_date,
                'deliver_slot_id' => $deliver_slot_id,
                'logistic_line_id' => $logistic_line_id,
                'logistic_line_title' => $line_data['logistic_line_title'],
                'logistic_van_id' => $logistic_van_id,
                'logistic_van_title' => $van_data['logistic_van_title'],
                'logistic_driver_id' => $logistic_driver_id,
                'logistic_driver_title' => $driver_data['logistic_driver_title'],
                'logistic_driver_phone' => $driver_data['logistic_driver_phone'],
                'logistic_deliveryman_id' => $logistic_deliveryman_id ? $logistic_deliveryman_id : 0,
                'logistic_deliveryman_title' => $logistic_deliveryman_id ? $deliveryman_data['logistic_deliveryman_title'] : 0,
                'logistic_deliveryman_phone' => $logistic_deliveryman_id ? $deliveryman_data['logistic_deliveryman_phone'] : 0,
                'added_by' => $this->user->getId()
            );

            $logistic_allot_id = $this->model_logistic_logistic->add($targetTable, $rowData);
            //$logistic_allot_id = $this->model_logistic_logistic->add('oc_x_logistic_allot',$data);
        }

        if($logistic_allot_id){
            foreach($order_ids as $order_id){
                $add = ['order_id'=>$order_id , 'logistic_allot_id'=>$logistic_allot_id];
                $this->model_logistic_logistic->addAllotOrder($add);
            }
        }
        else{
            exit('false');
        }

        exit('true');
    }

    public function getsumnums(){
        $this->load->model('logistic/logistic');
        $logistic_driver_id =  isset($this->request->post['logistic_driver_id']) ? $this->request->post['logistic_driver_id'] : false;
        $deliver_date = isset($this->request->post['deliver_date']) ? $this->request->post['deliver_date'] : false;
        $line_id = isset($this->request->post['line_id']) ? $this->request->post['line_id'] : false;
       $datas =  $this->model_logistic_logistic->getsumnums($logistic_driver_id,$deliver_date,$line_id);

        foreach($datas as $data) {
            if (!empty($data['frame_count']) || !empty($data['frame_meat_count'])) {
                $data .= '框:' . ((int)$data['frame_count'] + (int)$data['frame_meat_count']);
            }
            if (!empty($data['incubator_count']) || !empty($data['incubator_mi_count'])) {
                $data .= '保:' . ($data['incubator_count'] + $data['incubator_mi_count']);
            }
            if (!empty($data['foam_count']) || !empty($data['foam_ice_count'])) {
                $data .= '泡:' . ((int)$data['foam_count'] + (int)$data['foam_ice_count']);
            }
            if (!empty($data['frame_mi_count']) || !empty($data['frame_ice_count'])) {
                $data .= '奶框:' . ((int)$data['frame_mi_count'] + (int)$data['frame_ice_count']);
            }
            if (!empty($data['box_count'])) {
                $data .= '箱:' . ($data['box_count']);
            }
            $data['num'] = $data;
        }
      // var_dump($data['num']);
      // return ($data['num']json_encode);
        echo json_encode($data['num'], JSON_UNESCAPED_UNICODE);

    }

}