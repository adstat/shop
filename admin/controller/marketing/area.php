<?php
class ControllerMarketingArea extends Controller
{
    // 实效打开页面
    public function index()
    {
        $this->document->setTitle('用户区域管理');
        $data['heading_title'] = '用户区域管理';
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
            'text' => '用户区域管理',
            'href' => $this->url->link('marketing/area', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('marketing/area.tpl', $data));
    }

    public function add(){
        $this->load->model('marketing/area');

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['type']){
            $warehouse_ids = $postData = array();
            foreach($this->request->post['postData'] as $m){
                $postData[$m['name']] = $m['value'];
                if($m['name'] == 'warehouse_ids'){
                    $warehouse_ids[] = $m['value'];
                }
            }

            if(empty($warehouse_ids)){
                exit(json_encode('请选择区域仓库!', JSON_UNESCAPED_UNICODE));
            }

            $warehouse_data     = $this->checkRepeatStation($warehouse_ids);
            if($warehouse_data == false){
                exit(json_encode('选择区域仓库有误, 同一平台下只能选择一个区域仓库!', JSON_UNESCAPED_UNICODE));
            }


            switch($this->request->post['type']){
                case 'area':
                    $targetTable = 'oc_x_area';
                    $rowData = array(
                        'name' => $postData['area_name'],
                        'bd_id' => $postData['bd_id'],
                        'city' => $postData['area_city'],
                        'district' => $postData['area_district'],
                        'added_by' => $this->user->getId()
                    );

                    $area_id = $this->model_marketing_area->add($targetTable, $rowData);
                    $result  = $this->model_marketing_area->addAreaWarehouse($area_id, $warehouse_data);
                    break;
            }
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);

        //$this->session->data['success'] = '添加完成';
        //$this->response->redirect($this->url->link('logistic/logistic_info&tab='.$this->request->post['type'], 'token=' . $this->session->data['token'], 'SSL'));
    }

    // 检查warehouse_ids所属station_id有无重复;
    public function checkRepeatStation($warehouse_ids = array())
    {
        if(empty($warehouse_ids)){ return false; }

        $this->load->model('station/station');
        $data        = $this->model_station_station->getWarehouseAndStation(array('warehouse_ids'=> $warehouse_ids));
        $station_ids = array();
        $result      = array();
        if(!empty($data)){
            foreach($data as $value){
                if(in_array($value['station_id'], $station_ids)){
                    return false;
                } else {
                    $station_ids[] = $value['station_id'];
                    $result[]      = $value;
                }
            }
        }

        return $result;
    }

    public function update(){
        $this->load->model('marketing/area');

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['type']){
            $postData = array();
            $warehouse_ids = array();
            foreach($this->request->post['postData'] as $m){
                $postData[$m['name']] = $m['value'];
                if($m['name'] == 'warehouse_ids' && $m['value'] != 0){
                    $warehouse_ids[] = $m['value'];
                }
            }

            if(empty($warehouse_ids)){
                exit(json_encode('请选择区域仓库!', JSON_UNESCAPED_UNICODE));
            }

            $warehouse_data     = $this->checkRepeatStation($warehouse_ids);
            if($warehouse_data == false){
                exit(json_encode('选择区域仓库有误, 同一平台下只能选择一个区域仓库!', JSON_UNESCAPED_UNICODE));
            }

            switch($this->request->post['type']){
                case 'area':
                    $targetTable = 'oc_x_area';

                    $historyTable = 'oc_x_logistic_line_history';
                    $historyFields = array(
                        'logistic_line_id',
                        'logistic_line_title',
                        'default_logistic_driver_id'
                    );

                    $indexFilter = array(
                        'field' => 'area_id',
                        'value' => $this->request->post['id']
                    );

                    $rowData = array(
                        'name' => $postData['area_name'],
                        'district' => $postData['area_district'],
                        'bd_id' => $postData['bd_id'],
                        'status' => $postData['status'],
                        'modified_by' => $this->user->getId(),
                        'order_date' => $postData['order_date'],
                    );

                    $result = $this->model_marketing_area->update($targetTable, $rowData, $indexFilter);
                    $this->model_marketing_area->deleteAreaWarehouse($this->request->post['id']);
                    $this->model_marketing_area->addAreaWarehouse($this->request->post['id'], $warehouse_data);
                    break;
                default:
                    break;
            }
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function getAreaCity(){
        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->getAreaCity();

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getAreaDistrict($id=0){
        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->getAreaDistrict($id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getArea(){
        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->getArea();
        $areaWarehouseData = $this->model_marketing_area->getAreaWarehouse();
        $areaWarehouseIds  = array();
        foreach($areaWarehouseData as $value){
            $areaWarehouseIds[$value['area_id']][] = $value;
        }

        foreach($json as &$value){
            $value['warehouse_data'] = !empty($areaWarehouseIds[$value['area_id']]) ? $areaWarehouseIds[$value['area_id']] : array();
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getWarehouseAndStation()
    {
        $this->load->model('station/station');
        $json = $this->model_station_station->getWarehouseAndStation();

        $data = array();
        if(!empty($json)){
            foreach($json as $value){
                $data[$value['station_id']]['station_id']       = $value['station_id'];
                $data[$value['station_id']]['station_name']     = $value['name'];
                $data[$value['station_id']]['warehouse_data'][] = $value;
            }
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getAreaUnset(){
        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->getAreaUnset();

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getBdList(){
        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->getBdList();

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getCustomerByAreaByBd(){
        $area_id = isset($this->request->post['area_id']) ? $this->request->post['area_id'] : false;
        $bd_id = isset($this->request->post['bd_id']) ? $this->request->post['bd_id'] : false;
        $customer_id = isset($this->request->post['customer_id']) ? $this->request->post['customer_id'] : false;

        if($area_id == 'false'){ $area_id = false; }
        if($bd_id == 'false'){ $bd_id = false; }
        if($customer_id == 'false'){ $customer_id = false; }

        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->getCustomerByAreaByBd($area_id,$bd_id,$customer_id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function applyCustomerToArea(){
        $customers = isset($this->request->post['customers']) ? $this->request->post['customers'] : array(0);
        $area_id = isset($this->request->post['area_id']) ? $this->request->post['area_id'] : 0;

        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->applyCustomerToArea($customers,$area_id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function applyDrawToArea(){
        $position = isset($this->request->post['positions']) ? $this->request->post['positions'] : array(0);
        $area_id = isset($this->request->post['area_id']) ? $this->request->post['area_id'] : 0;
        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->applyDrawToArea($position,$area_id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getAreaDrawInfo(){
        $area_id = isset($this->request->post['area_id']) ? $this->request->post['area_id'] : 0;
        $bd_id = isset($this->request->post['bd_id']) ? $this->request->post['bd_id'] : 0;
        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->getAreaDrawInfo($area_id,$bd_id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function deleteDrawToArea(){
        $area_id = isset($this->request->post['area_id']) ? $this->request->post['area_id'] : 0;
        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->deleteDrawToArea($area_id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
}