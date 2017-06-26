<?php
class ControllerReportSaleBdPerforemance extends Controller{
    public function index(){
//        var_dump(1213);
        $this->load->model('report/sale');

        $this->document->setTitle('BD绩效');

        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
        //$filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_bd_list = isset($this->request->get['filter_bd_list'])?$this->request->get['filter_bd_list']:false;
        $filter_station_id = isset($this->request->get['filter_station_id'])?$this->request->get['filter_station_id']:false;
        $filter_activity = isset($this->request->get['filter_activity'])?$this->request->get['filter_activity']:0.5;
        $filter_bd_area_list = isset($this->request->get['filter_bd_area_list'])?$this->request->get['filter_bd_area_list']:false;

        //BD user id by username, for Sale Access Limited
        if(in_array($this->user->getGroupId(), unserialize(SALE_ACCESS_CONTROL_GROUP))){
            $filter_bd_list = $this->model_report_sale->getBdIdByUsername($this->user->getUserName());
        }

        $url = '';
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        if ($filter_bd_list) {
            $url .= '&filter_bd_list=' . $filter_bd_list;
        }

        if ($filter_bd_area_list) {
            $url .= '&filter_bd_area_list=' . $filter_bd_area_list;
        }

        if ($filter_station_id) {
            $url .= '&filter_station_id=' . $filter_station_id;
        }

        if ($filter_activity) {
            $url .= '&filter_activity=' . $filter_activity;
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => 'BD绩效',
            'href' => $this->url->link('report/sale_bd_perforemance', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

//        $data = array();

        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_bd_list' => $filter_bd_list,
            'filter_station_id' => $filter_station_id,
            'filter_activity' => $filter_activity,
            'filter_bd_area_list' => $filter_bd_area_list,
        );

        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);

        $data['nofilter'] = false;

        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32) {

            $result = $this->model_report_sale->getBdPerforemance($filter_data);
            $data['orders'] = $result['orders'];
            $data['returns'] = $result['returns'];
            $data['bd_customers'] = $result['bd_customers'];
            $data['bd_ordered_customers'] = $result['bd_ordered_customers'];
//            echo '<pre>';
//            print_r($data['orders']);
        }else{
            $data['nofilter'] = true;
        }

        $data['heading_title'] = 'BD绩效';

        $data['text_list'] = $this->language->get('BD绩效列表');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_bd_list'] = $filter_bd_list;
        $data['filter_station_id'] = $filter_station_id;
        $data['filter_activity'] = $filter_activity;
        $data['filter_bd_area_list'] = $filter_bd_area_list;

        $data['bd_list'] = $this->model_report_sale->getBdList();
        $data['bd_area_list'] = $this->model_report_sale->getBDAreaList();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/sale_bd_active.tpl', $data));
    }
}
?>