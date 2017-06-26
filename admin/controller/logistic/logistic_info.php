<?php
class ControllerLogisticLogisticInfo extends Controller
{
    // 实效打开页面
    public function index()
    {
        $this->document->setTitle('司机线路信息管理');
        $data['heading_title'] = '司机线路信息管理';
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
            'text' => '司机线路信息管理',
            'href' => $this->url->link('logistic/logistic_info', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action_adjust'] = $this->url->link('logistic/logistic_info/adjust_post', 'token=' . $this->session->data['token'], 'SSL');

        //$data['action_add'] = $this->url->link('logistic/logistic_info/add', 'token=' . $this->session->data['token'], 'SSL');
        //$data['action_edit'] = $this->url->link('logistic/logistic_info/edit', 'token=' . $this->session->data['token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('logistic/logistic_info.tpl', $data));
    }

    public function add(){
        $this->load->model('logistic/logistic');

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['type']){
            foreach($this->request->post['postData'] as $m){
                $postData[$m['name']] = $m['value'];
            }

            switch($this->request->post['type']){
                case 'line':
                    $targetTable = 'oc_x_logistic_line';
                    $rowData = array(
                        'logistic_line_title' => $postData['logistic_line_title'],
                        'default_logistic_driver_id' => $postData['default_logistic_driver_id'],
                        'added_by' => $this->user->getId()
                    );

                    $result = $this->model_logistic_logistic->add($targetTable, $rowData);
                    break;

                case 'driver':
                    $targetTable = 'oc_x_logistic_driver';
                    $rowData = array(
                        'logistic_driver_title' => $postData['logistic_driver_title'],
                        'logistic_driver_phone' => $postData['logistic_driver_phone'],
                        'default_logistic_van_id' => $postData['default_logistic_van_id'],
                        'added_by' => $this->user->getId()
                    );

                    $result = $this->model_logistic_logistic->add($targetTable, $rowData);
                    break;

                case 'van':
                    $targetTable = 'oc_x_logistic_van';
                    $rowData = array(
                        'logistic_van_title' => $postData['logistic_van_title'],
                        'model' => $postData['model'],
                        'ownership' => $postData['ownership'],
                        'owner' => $postData['owner'],
                        'contact' => $postData['contact'],
                        'added_by' => $this->user->getId()
                    );

                    $result = $this->model_logistic_logistic->add($targetTable, $rowData);

                    break;

                case 'deliveryman':
                    $targetTable = 'oc_x_logistic_deliveryman';
                    $rowData = array(
                        'logistic_deliveryman_title' => $postData['logistic_deliveryman_title'],
                        'logistic_deliveryman_phone' => $postData['logistic_deliveryman_phone'],
                        'added_by' => $this->user->getId()
                    );

                    $result = $this->model_logistic_logistic->add($targetTable, $rowData);

                    break;
            }
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);

        //$this->session->data['success'] = '添加完成';
        //$this->response->redirect($this->url->link('logistic/logistic_info&tab='.$this->request->post['type'], 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function edit(){
        $this->load->model('logistic/logistic');

        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['type']){
            foreach($this->request->post['postData'] as $m){
                $postData[$m['name']] = $m['value'];
            }

            switch($this->request->post['type']){
                case 'line':
                    $targetTable = 'oc_x_logistic_line';

                    $historyTable = 'oc_x_logistic_line_history';
                    $historyFields = array(
                        'logistic_line_id',
                        'logistic_line_title',
                        'default_logistic_driver_id'
                    );
                    $indexFilter = array(
                        'field' => 'logistic_line_id',
                        'value' => $this->request->post['id']
                    );

                    $rowData = array(
                        //'logistic_line_title' => $postData['logistic_line_title'],
                        'default_logistic_driver_id' => $postData['default_logistic_driver_id'],
                        'status' => $postData['status'],
                        'modified_by' => $this->user->getId()
                    );

                    $this->model_logistic_logistic->addHistory($targetTable, $historyTable, $historyFields, $indexFilter, $this->user->getId());
                    $result = $this->model_logistic_logistic->edit($targetTable, $rowData, $indexFilter);

                    break;

                case 'driver':
                    $targetTable = 'oc_x_logistic_driver';

                    $historyTable = 'oc_x_logistic_driver_history';
                    $historyFields = array(
                        'logistic_driver_id',
                        'logistic_driver_title',
                        'logistic_driver_phone',
                        'default_logistic_van_id'
                    );
                    $indexFilter = array(
                        'field' => 'logistic_driver_id',
                        'value' => $this->request->post['id']
                    );

                    $rowData = array(
                        //'logistic_driver_title' => $postData['logistic_driver_title'],
                        'logistic_driver_phone' => $postData['logistic_driver_phone'],
                        'default_logistic_van_id' => $postData['default_logistic_van_id'],
                        'status' => $postData['status'],
                        'modified_by' => $this->user->getId()
                    );

                    $this->model_logistic_logistic->addHistory($targetTable, $historyTable, $historyFields, $indexFilter, $this->user->getId());
                    $result = $this->model_logistic_logistic->edit($targetTable, $rowData, $indexFilter);

                    break;

                case 'van':
                    $targetTable = 'oc_x_logistic_van';

                    $historyTable = 'oc_x_logistic_van_history';
                    $historyFields = array(
                        'logistic_van_id',
                        'logistic_van_title',
                        'capacity',
                        'payload',
                        'model',
                        'ownership',
                        'owner',
                        'contact'
                    );
                    $indexFilter = array(
                        'field' => 'logistic_van_id',
                        'value' => $this->request->post['id']
                    );

                    $rowData = array(
                        //'logistic_van_title' => $postData['logistic_van_title'],
                        'model' => $postData['model'],
                        'ownership' => $postData['ownership'],
                        'owner' => $postData['owner'],
                        'contact' => $postData['contact'],
                        'status' => $postData['status'],
                        'modified_by' => $this->user->getId()
                    );

                    $this->model_logistic_logistic->addHistory($targetTable, $historyTable, $historyFields, $indexFilter, $this->user->getId());
                    $result = $this->model_logistic_logistic->edit($targetTable, $rowData, $indexFilter);

                    break;

                case 'deliveryman':
                    $targetTable = 'oc_x_logistic_deliveryman';

                    $historyTable = 'oc_x_logistic_deliveryman_history';
                    $historyFields = array(
                        'logistic_deliveryman_id',
                        'logistic_deliveryman_title',
                        'logistic_deliveryman_phone'
                    );
                    $indexFilter = array(
                        'field' => 'logistic_deliveryman_id',
                        'value' => $this->request->post['id']
                    );

                    $rowData = array(
                        //'logistic_deliveryman_title' => $postData['logistic_deliveryman_title'],
                        'logistic_deliveryman_phone' => $postData['logistic_deliveryman_phone'],
                        'status' => $postData['status'],
                        'modified_by' => $this->user->getId()
                    );

                    $this->model_logistic_logistic->addHistory($targetTable, $historyTable, $historyFields, $indexFilter, $this->user->getId());
                    $result = $this->model_logistic_logistic->edit($targetTable, $rowData, $indexFilter);

                    break;
            }
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function getLogisticLine($id=0){
        $this->load->model('logistic/logistic');
        $json = $this->model_logistic_logistic->getLogisticLine($id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getLogisticDriver($id=0){
        $this->load->model('logistic/logistic');
        $json = $this->model_logistic_logistic->getLogisticDriver($id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getLogisticVan($id=0){
        $this->load->model('logistic/logistic');
        $json = $this->model_logistic_logistic->getLogisticVan($id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getLogisticDeliveryman($id=0){
        $this->load->model('logistic/logistic');
        $json = $this->model_logistic_logistic->getLogisticDeliveryman($id);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
}