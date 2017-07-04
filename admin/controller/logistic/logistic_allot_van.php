<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/10/10
 * Time: 14:10
 */
class ControllerLogisticLogisticAllotVan extends Controller
{
    public function index(){
        $this->document->setTitle('物流区域管理');
        $data['heading_title'] = '物流区域管理';
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
            'text' => '订单分派汇总',
            'href' => $this->url->link('logistic/logistic_allot_van', 'token=' . $this->session->data['token'], 'SSL')
        );


        $this->load->model('logistic/logisticvan');
        $this->load->model('station/station');
        $data['stations'] = $this->model_station_station->getStationList();
        $data['slots'] = $this->model_logistic_logisticvan->getSlotList();
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['filterDefault'] = array(
            'station_id' => 2,
            'deliver_date' => $date = date("Y-m-d", strtotime("1 day")), //Tomorrow
            'deliver_slot_id' => 4
        );
        $this->response->setOutput($this->load->view('logistic/logistic_allot_van.tpl', $data));
    }
    public function getOrdersByAreaByDate(){
        $classify = $this->request->post['classify'];
        $search_date = $this->request->post['search_date'];
        $area_id = isset($this->request->post['area_id']) ? $this->request->post['area_id'] : false;
        $station_id = isset($this->request->post['station_id'])? $this->request->post['station_id']:false;
        $warehouse_id_global = isset($this->request->post['warehouse_id_global'])? $this->request->post['warehouse_id_global']:false;
        if($area_id == 'false'){ $area_id = false; }

        $this->load->model('logistic/logisticvan');
        $this->load->model('logistic/logistic');
        $json = $this->model_logistic_logisticvan->getOrdersByAreaByDate($classify,$area_id,$search_date,$station_id,$warehouse_id_global);

       foreach($json as &$value){
           $order = $this->model_logistic_logistic->getOrderInv($value['order_id']);
           if (empty($order)) {
               $num = 0;
               $orderQuantity = $this->model_logistic_logistic->getOrderQuantity($value['order_id']);
               foreach ($orderQuantity as $val) {
                   $num += $val['quantity'];
               }
               $num = '未拣:共' . $num . '件';
           } else {
               $num = '';
               if (!empty($order['frame_count']) || !empty($order['frame_meat_count'])) {
                   $num .= '框:' . ((int)$order['frame_count'] + (int)$order['frame_meat_count']);
               }
               if (!empty($order['incubator_count']) || !empty($order['incubator_mi_count'])) {
                   $num .= '保:' . ($order['incubator_count'] + $order['incubator_mi_count']);
               }
               if (!empty($order['foam_count']) || !empty($order['foam_ice_count'])) {
                   $num .= '泡:' . ((int)$order['foam_count'] + (int)$order['foam_ice_count']);
               }
               if (!empty($order['frame_mi_count']) || !empty($order['frame_ice_count'])) {
                   $num .= '奶框:' . ((int)$order['frame_mi_count'] + (int)$order['frame_ice_count']);
               }
               if (!empty($order['box_count'])) {
                   $num .= '箱:' . ($order['box_count']);
               }
           }
           $value['num'] = $num;
       }


        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getSelectedQuantity(){
        $this->load->model('logistic/logisticvan');
        $this->load->model('logistic/logistic');

        $json = isset($this->request->post['order_id']) ? $this->request->post['order_id'] : false;
        $num  = '';
        $num1 = '';
        $num2 = '';
        $num3 = '';
        $num4 = '';
        $num5 = '';

        foreach($json as &$value){
            $order = $this->model_logistic_logistic->getOrderInv($value);
            if (empty($order)) {
                $orderQuantity = $this->model_logistic_logistic->getOrderQuantity($value);
                foreach ($orderQuantity as $val) {
                    $num += $val['quantity'];
                }
                $num = '未拣:共' . $num . '件';
            }else {
                if (!empty($order['frame_count']) || !empty($order['frame_meat_count'])) {
                    $num1 += ((int)$order['frame_count'] + (int)$order['frame_meat_count']);
                }
                if (!empty($order['incubator_count']) || !empty($order['incubator_mi_count'])) {
                    $num2 += ($order['incubator_count'] + $order['incubator_mi_count']);
                }
                if (!empty($order['foam_count']) || !empty($order['foam_ice_count'])) {
                    $num3 += ((int)$order['foam_count'] + (int)$order['foam_ice_count']);
                }
                if (!empty($order['frame_mi_count']) || !empty($order['frame_ice_count'])) {
                    $num4 += ((int)$order['frame_mi_count'] + (int)$order['frame_ice_count']);
                }
                if (!empty($order['box_count'])) {
                    $num5 += ($order['box_count']);
                }
            }

        }

        $num1 = '框:共' . $num1 . '件';
        $num2 = '保:共' . $num2 . '件';
        $num3 = '泡:共' . $num3 . '件';
        $num4 = '奶框:共' . $num4 . '件';
        $num5 = '箱:共' . $num5 . '件';
       $sum = array($num,$num1,$num2,$num3,$num4,$num5 );

        echo json_encode($sum, JSON_UNESCAPED_UNICODE);
    }


    public function  getLine(){
        $this->load->model('logistic/logisticvan');
        $json = $this->model_logistic_logisticvan->getLine();
        echo json_encode($json,JSON_UNESCAPED_UNICODE);
    }

    public function  getDriver(){
        $this->load->model('logistic/logisticvan');
        $json = $this->model_logistic_logisticvan->getDriver();
        echo json_encode($json,JSON_UNESCAPED_UNICODE);
    }
    public function  getCar(){
        $this->load->model('logistic/logisticvan');
        $json = $this->model_logistic_logisticvan->getCar();
        echo json_encode($json,JSON_UNESCAPED_UNICODE);
    }
    public function getDeliveryman(){
        $this->load->model('logistic/logisticvan');
        $json = $this->model_logistic_logisticvan->getDeliveryman();
        echo json_encode($json,JSON_UNESCAPED_UNICODE);
    }
    public function addAllotOrder(){
        $station_id = isset($this->request->post['station_id']) ? $this->request->post['station_id'] : false;
        $deliver_date = isset($this->request->post['deliver_date']) ? $this->request->post['deliver_date'] : false;
        $deliver_slot_id = isset($this->request->post['deliver_slot_id']) ? $this->request->post['deliver_slot_id'] : false;
        $logistic_line_id =  isset($this->request->post['logistic_line_id']) ? $this->request->post['logistic_line_id'] : false;
        $logistic_driver_id =  isset($this->request->post['logistic_driver_id']) ? $this->request->post['logistic_driver_id'] : false;
        $logistic_van_id =  isset($this->request->post['logistic_van_id']) ? $this->request->post['logistic_van_id'] : false;
        $order_ids =  (isset($this->request->post['order_ids']) && is_array($this->request->post['order_ids'])) ? $this->request->post['order_ids'] : array();
        $logistic_deliveryman_id =  isset($this->request->post['logistic_deliveryman_id']) ? $this->request->post['logistic_deliveryman_id'] : false;;

        $warehouse_id_global =  isset($this->request->post['warehouse_id_global']) ? $this->request->post['warehouse_id_global'] : false;;
        if(!$station_id || !$deliver_date || !$deliver_slot_id || !$logistic_line_id || !$logistic_driver_id || !$logistic_van_id || !count($order_ids)){
            exit('false');
        }
        $this->load->model('logistic/logistic');
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
      //  if(!$logistic_allot_id){
            $line_data = $this->model_logistic_logistic->getLineInfo($logistic_line_id);
            $van_data  = $this->model_logistic_logistic->getVanInfo($logistic_van_id);
            $driver_data = $this->model_logistic_logistic->getDriverInfo($logistic_driver_id);
            if($logistic_deliveryman_id){
                $deliveryman_data = $this->model_logistic_logistic->getDeliverymanInfo($logistic_deliveryman_id);
            }
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
                'added_by' => $this->user->getId(),
                'warehouse_id' => $warehouse_id_global,
            );
    //    }

   //     $data = array_merge($line_data,$driver_data,$van_data);
        $flag = true;
        foreach($order_ids as $order_id){
            $order_data = $this->model_logistic_logistic->getOrderInfo($order_id);
            $data = array_merge($rowData,$order_data);
            $data['added_by'] = $_SESSION['user_id'];
            $logistic_allot_id = $this->model_logistic_logistic->add('oc_x_logistic_allot',$data);

            if(!$logistic_allot_id){
                $flag = false;
            }
            $add = ['order_id'=>$order_id , 'logistic_allot_id'=>$logistic_allot_id];
            $allot_order_id = $this->model_logistic_logistic->addAllotOrder($add);
            if(!$allot_order_id){
                $flag = false;
            }
        }
        if($flag){
            echo true;
        }

    }

}