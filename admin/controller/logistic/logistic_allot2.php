<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/10/10
 * Time: 14:10
 */
class ControllerLogisticLogisticAllot2 extends Controller
{
    public function index(){
        $this->document->setTitle('订单分派汇总');
        $data['heading_title'] = '订单分派汇总';
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
            'href' => $this->url->link('logistic/logistic_allot2', 'token=' . $this->session->data['token'], 'SSL')
        );


        $this->load->model('logistic/logistic');

        $this->load->model('station/station');
        $data['stations'] = $this->model_station_station->getStationList();

        $data['slots'] = $this->model_logistic_logistic->getSlotList();
        $data['logistic_list'] = $this->model_logistic_logistic->getLogisticList();
        $data['lines'] = $this->model_logistic_logistic->getLineDatas();
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['filterDefault'] = array(
            'station_id' => 2,
            'deliver_date' => $date = date("Y-m-d", strtotime("0 day")), //Tomorrow
            'deliver_slot_id' => 4
        );
        $data['logistic_print'] = $this->url->link('logistic/logistic_allot2/logistic_print', 'token=' . $this->session->data['token'].'SSL');
        $this->response->setOutput($this->load->view('logistic/logistic_allot2.tpl', $data));
    }

    public function getAllotInfo($type)
    {
        $this->load->model('logistic/logistic');
        $this->load->model('station/station');
        $where = [];
        !empty($this->request->get['order_id']) && $where['order_id'] = $this->request->get['order_id'];
        !empty($this->request->get['line_id']) && $where['logistic_line_id'] = $this->request->get['line_id'];
        !empty($this->request->get['driver_id']) && $where['logistic_driver_id'] = $this->request->get['driver_id'];
        !empty($this->request->get['van_id']) && $where['logistic_van_id'] = $this->request->get['van_id'];
        !empty($this->request->get['date']) && $where['deliver_date'] = $this->request->get['date'];
        !empty($this->request->get['station_id']) && $where['station_id'] = $this->request->get['station_id'];
        !empty($this->request->get['deliver_slot_id']) && $where['deliver_slot_id'] = $this->request->get['deliver_slot_id'];
        $assign_status_id = isset($this->request->get['assign_status_id'])?$this->request->get['assign_status_id']:1;
        $warehouse_id_global = isset($this->request->get['warehouse_id_global'])?$this->request->get['warehouse_id_global']: false;

        $deliver_date = isset($this->request->get['date']) ? $this->request->get['date'] : false;
        $station_id = isset($this->request->get['station_id']) ? $this->request->get['station_id'] : STATION_FAST_MOVE;
        $indexStart = defined('FAST_MOVE_ORDER_SORTING_INDEX_START') ? FAST_MOVE_ORDER_SORTING_INDEX_START : 501;

//        if($deliver_date){
//            $this->load->model('sale/order');
//            $orderSortingIndexList = $this->model_sale_order->getOrderSortingIndexList($deliver_date,$station_id,$indexStart);
//        }

        if($assign_status_id == 2){
            $data = $this->model_logistic_logistic->getUnTable($where,$warehouse_id_global);
            foreach ($data as &$value) {
              //  $value['sortIndex'] = $orderSortingIndexList[$value['order_id']];
                $order = $this->model_logistic_logistic->getOrderInv($value['order_id']);

                if (empty($order)) {
                    $num = 0;
                    $orderQuantity = $this->model_logistic_logistic->getOrderQuantity($value['order_id']);
                    foreach ($orderQuantity as $val) {
                        $num += $val['quantity'];
                    }
                    $num = '未拣:共' . $num . '件';
                } else {
                    $invComment = $order['inv_comment'];

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
                $value['station_name'] = $this->model_station_station->getStationNameById($value['station_id']);
                $value['num'] = $num;
                $value['invComment'] = $invComment;
            }

        }


        if($assign_status_id ==1){
            $data = $this->model_logistic_logistic->getAllotInfo($where,$warehouse_id_global);
            foreach ($data as &$value) {
              //  $value['sortIndex'] = $orderSortingIndexList[$value['order_id']];
                $order = $this->model_logistic_logistic->getOrderInv($value['order_id']);
                $invComment = '';
                if (empty($order)) {
                    $num = 0;
                    $orderQuantity = $this->model_logistic_logistic->getOrderQuantity($value['order_id']);
                    foreach ($orderQuantity as $val) {
                        $num += $val['quantity'];
                    }
                    $num = '未拣:共' . $num . '件';
                } else {
                    $invComment = $order['inv_comment'];
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

                $sumData = $this->model_logistic_logistic->getSumNum($where);
                foreach ($sumData as &$sumvalue) {
                    $data[0]['sum'] = array(
                        'kuang' =>($sumvalue['sum(A.frame_count)'] + $sumvalue['sum(A.frame_meat_count)']),
                        'kuang2' => ($sumvalue['sum(A.frame_count)'] + $sumvalue['sum(A.frame_meat_count)'])*2,
                        'bao' => $sumvalue['sum(A.incubator_count)'] + $sumvalue['sum(A.incubator_mi_count)'],
                        'pao' => $sumvalue['sum(A.foam_count)'] + $sumvalue['sum(A.foam_ice_count)'],
                        'naikuang' => $sumvalue['sum(A.frame_mi_count)'] + $sumvalue['sum(A.frame_ice_count)'],
                        'xiang' => $sumvalue['sum(A.box_count)'],
                    );
                }

                $sumNotpicking = $this->model_logistic_logistic->getSumNotpicking($where);
                foreach ($sumNotpicking as &$notpicking) {
                    $data[0]['notpicking'] = array(
                        'quantity' =>$notpicking['sum(A.quantity)'],
                    );
                }

                $value['station_name'] = $this->model_station_station->getStationNameById($value['station_id']);
                $value['num'] = $num;
                $value['invComment'] = $invComment;
            }



        }

        if($type==1){

            return $data;
        }else{

            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }

    }

    public function del(){
    $logistic_allot_order_id = $this->request->get['logistic_allot_order_id'];
    $logistic_allot_id = $this->request->get['logistic_allot_id'];
    $this->load->model('logistic/logistic');
    $this->model_logistic_logistic->del($logistic_allot_order_id,$logistic_allot_id);
}

    public function getLogisticAllotId(){
        $date = $this->request->post['date'];
        $line_id = $this->request->post['line_id'];
        $driver_id = $this->request->post['driver_id'];
        $station_id = $this->request->post['station_id'];
        $this->load->model('logistic/logistic');
        $result = $this->model_logistic_logistic->getLogisticAllotId($date,$line_id,$driver_id,$station_id);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }


    public function logistic_print(){
        $this->load->model('logistic/logistic');
        $this->load->model('station/station');
        $logistic_allot_id = $this->request->get['logistic_allot_id'];
        $data = $this->model_logistic_logistic->getlogistics($logistic_allot_id);

        foreach ($data as &$value) {
            $order = $this->model_logistic_logistic->getOrderInv($value['order_id']);
            $invComment = '';
            if (empty($order)) {
                $num = 0;
                $orderQuantity = $this->model_logistic_logistic->getOrderQuantity($value['order_id']);
                foreach ($orderQuantity as $val) {
                    $num += $val['quantity'];
                }
                $num = '未拣:共' . $num . '件';
            } else {
                $invComment = $order['inv_comment'];
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

            $sumData = $this->model_logistic_logistic->getLogisticSumNum($logistic_allot_id);
            foreach ($sumData as &$sumvalue) {
                $data[0]['sum'] = array(
                    'kuang' =>($sumvalue['sum(A.frame_count)'] + $sumvalue['sum(A.frame_meat_count)']),
                    'kuang2' => ($sumvalue['sum(A.frame_count)'] + $sumvalue['sum(A.frame_meat_count)'])*2,
                    'bao' => $sumvalue['sum(A.incubator_count)'] + $sumvalue['sum(A.incubator_mi_count)'],
                    'pao' => $sumvalue['sum(A.foam_count)'] + $sumvalue['sum(A.foam_ice_count)'],
                    'naikuang' => $sumvalue['sum(A.frame_mi_count)'] + $sumvalue['sum(A.frame_ice_count)'],
                    'xiang' => $sumvalue['sum(A.box_count)'],
                );
            }

            $sumNotpicking = $this->model_logistic_logistic->getLogisticSumNotpicking($logistic_allot_id);
            foreach ($sumNotpicking as &$notpicking) {
                $data[0]['notpicking'] = array(
                    'quantity' =>$notpicking['sum(A.quantity)'],
                );
            }

            $value['station_name'] = $this->model_station_station->getStationNameById($value['station_id']);
            $value['num'] = $num;
            $value['invComment'] = $invComment;
        }



        $this->response->setOutput($this->load->view('logistic/logistic_print.tpl', $data));

    }

}