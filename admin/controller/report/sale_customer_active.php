<?php
class ControllerReportSaleCustomerActive extends Controller {
    public function index() {
        $data['header'] = $this->load->controller('common/header');
        //暂时用session处理全局的warehouse_id_global
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();

        if($data['filter_warehouse_id_global']){
            $filter_warehouse_id_global = $data['filter_warehouse_id_global'];
        }else{
            $filter_warehouse_id_global = 0;
        }

        $this->load->language('report/sale_customer_active');

        $this->document->setTitle($this->language->get('heading_title'));

        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
        //$filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_bd_list = isset($this->request->get['filter_bd_list'])?$this->request->get['filter_bd_list']:false;
        $filter_station_id = isset($this->request->get['filter_station_id'])?$this->request->get['filter_station_id']:false;
        $filter_activity = isset($this->request->get['filter_activity'])?$this->request->get['filter_activity']:0.5;
        $filter_bd_area_list = isset($this->request->get['filter_bd_area_list'])?$this->request->get['filter_bd_area_list']:false;

        $this->load->model('report/sale');
        $this->load->model('station/station');

        //BD user id by username, for Sale Access Limited
        if(in_array($this->user->getGroupId(), unserialize(SALE_ACCESS_CONTROL_GROUP))){
            $filter_bd_list = $this->model_report_sale->getBdIdByUsername($this->user->getUserName());
        }

        //var_export($this->model_report_sale->getCustomerPreform());

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
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('report/sale_customer_active', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_bd_list' => $filter_bd_list,
            'filter_station_id' => $filter_station_id,
            'filter_activity' => $filter_activity,
            'filter_bd_area_list' => $filter_bd_area_list,
            'filter_warehouse_id_global' => $filter_warehouse_id_global,
        );

        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);

        $data['nofilter'] = false;
        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32){
            $result = $this->model_report_sale->getCustomerActive($filter_data);
            $data['totals'] = $result['totals'];
            $data['orders'] = $result['orders'];
            $data['areas'] = $result['areas'];
            $data['bd_customers'] = $result['bd_customers'];
            $data['bd_ordered_customers'] = $result['bd_ordered_customers'];
            $data['area_customers'] = $result['area_customers'];
            $data['bd_area_customers'] = $result['bd_area_customers'];
            //分别对bd和区域合计一下订单产生的总金额
            $money_total = 0;
            $sum_orders = 0;
            $money_total_a = 0;
            $sum_orders_a = 0;
            foreach($data['totals'] as $value){
                $money_total += $value['sum_sub_total'];
                $sum_orders += $value['sum_orders'];
            }
            foreach($data['areas'] as $value){
                $money_total_a += $value['sum_sub_total'];
                $sum_orders_a += $value['sum_orders'];
            }
            $data['money_total'] = $money_total;
            $data['money_total_a'] = $money_total_a;
            $data['sum_orders'] = $sum_orders;
            $data['sum_orders_a'] = $sum_orders_a;
        }
        else{
            $data['nofilter'] = true;
        }
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_all_status'] = $this->language->get('text_all_status');

        $data['column_date_start'] = $this->language->get('column_date_start');
        $data['column_date_end'] = $this->language->get('column_date_end');
        $data['column_orders'] = $this->language->get('column_orders');
        $data['column_products'] = $this->language->get('column_products');
        $data['column_tax'] = $this->language->get('column_tax');
        $data['column_total'] = $this->language->get('column_total');

        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');
        $data['entry_group'] = $this->language->get('entry_group');
        $data['entry_status'] = $this->language->get('entry_status');

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
        $data['stations'] = $this->model_station_station->getStationList();
        $data['station_set'] = $this->model_station_station->setFilterStation($filter_warehouse_id_global);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('report/sale_customer_active.tpl', $data));
    }
}