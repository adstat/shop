<?php
class ControllerReportSaleOrder extends Controller {

    public function index() {

        $data['header'] = $this->load->controller('common/header');
        //暂时用session处理全局的warehouse_id_global
        $data['filter_warehouse_id_global'] = $this->warehouse->getWarehouseIdGlobal();

        if($data['filter_warehouse_id_global']){
            $filter_warehouse_id_global = $data['filter_warehouse_id_global'];
        }else{
            $filter_warehouse_id_global = 0;
        }

        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $this->load->language('report/sale_order');

        $this->document->setTitle($this->language->get('heading_title'));

        $filter_datatype = isset($this->request->get['filter_datatype'])?$this->request->get['filter_datatype']:2;
        //$filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_order_status = isset($this->request->get['filter_order_status'])?$this->request->get['filter_order_status']:false;
        $filter_order_payment_status = isset($this->request->get['filter_order_payment_status'])?$this->request->get['filter_order_payment_status']:false;
        $filter_order_deliver_status = isset($this->request->get['filter_order_deliver_status'])?$this->request->get['filter_order_deliver_status']:false;
        $filter_bd_list = isset($this->request->get['filter_bd_list'])?$this->request->get['filter_bd_list']:false;
        $filter_bd_area_list = isset($this->request->get['filter_bd_area_list'])?$this->request->get['filter_bd_area_list']:false;
        $filter_logistic_list =isset($this->request->get['filter_logistic_list'])?$this->request->get['filter_logistic_list']:false;
        $filter_firstorder = isset($this->request->get['filter_firstorder'])?$this->request->get['filter_firstorder']:false;
        $filter_station = isset($this->request->get['filter_station'])?$this->request->get['filter_station']:false;
        $filter_customer_id = isset($this->request->get['filter_customer_id'])?$this->request->get['filter_customer_id']:false;
        $showmap = isset($this->request->get['showmap'])?$this->request->get['showmap']:0;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;

        $this->load->model('report/sale');
        $this->load->model('station/station');

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

        if ($filter_order_status) {
            $url .= '&filter_order_status=' . $filter_order_status;
        }

        if ($filter_order_payment_status) {
            $url .= '&filter_order_status_id=' . $filter_order_payment_status;
        }
        if ($filter_order_deliver_status) {
            $url .= '&filter_order_deliver_id=' . $filter_order_deliver_status;
        }

        if ($filter_bd_list) {
            $url .= '&filter_bd_list=' . $filter_bd_list;
        }

        if ($filter_bd_area_list) {
            $url .= '&filter_bd_area_list=' . $filter_bd_area_list;
        }
        if ($filter_logistic_list){
            $url .= '&filter_logistic_list=' . $filter_logistic_list;
        }
        if ($filter_firstorder) {
            $url .= '&filter_firstorder=' . $filter_firstorder;
        }
        if ($filter_station) {
            $url .= '&filter_station=' . $filter_station;
        }

        if ($filter_customer_id) {
            $url .= '&filter_customer_id=' . $filter_customer_id;
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('report/sale_order', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $filter_data = array(
            'filter_datatype' => $filter_datatype,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_order_status' => $filter_order_status,
            'filter_order_payment_status' => $filter_order_payment_status,
            'filter_order_deliver_status' => $filter_order_deliver_status,
            'filter_bd_list' => $filter_bd_list,
            'filter_bd_area_list' => $filter_bd_area_list,
            'filter_logistic_list'=>$filter_logistic_list,
            'filter_firstorder' => $filter_firstorder,
            'filter_station' => $filter_station,
            'filter_customer_id' => $filter_customer_id,
            'filter_warehouse_id_global' => $filter_warehouse_id_global,
        );

        $data['token'] = $this->session->data['token'];

        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);

        $data['nofilter'] = false;
        if(isset($this->request->get['filter_datatype']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32){
            $result = $this->model_report_sale->getMkOrders($filter_data);
            $data['orders'] = $result['orders'];
            $data['totals'] = $result['totals'];
//            $data['areas'] = $result['areas'];
            if(sizeof($result['orders'])){
                foreach($result['orders'] as &$value){
                    $sum_due = $value['order_due'] + $value['wechat_paid'] + $value['user_point_paid'] - $value['quehuo_credits'] - $value['tuihuo_credits'];
                    if($sum_due > 0){
                        $data['sum_due'] = $sum_due;
                        $value['sum_due']= $sum_due;
                    }else{
                        $data['sum_due'] = $sum_due = 0;
                        $value['sum_due']= $sum_due;
                    }

                }
            }

            $data['orders'] = $result['orders'];
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

        $data['filter_datatype'] = $filter_datatype;
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_order_status'] = $filter_order_status;
        $data['filter_order_payment_status'] = $filter_order_payment_status;
        $data['filter_order_deliver_status'] = $filter_order_deliver_status;
        $data['filter_bd_list'] = $filter_bd_list;
        $data['filter_bd_area_list'] = $filter_bd_area_list;
        $data['filter_logistic_list'] = $filter_logistic_list;
        $data['filter_firstorder'] = $filter_firstorder;
        $data['filter_station'] = $filter_station;
        $data['filter_customer_id'] = $filter_customer_id;

        $data['order_status'] = $this->model_report_sale->getOrderStatus();
        $data['order_payment_status'] = $this->model_report_sale->getOrderPaymentStatus();
        $data['order_deliver_status'] = $this->model_report_sale->getOrderDeliverStatus();
        $data['bd_list'] = $this->model_report_sale->getBdList();
        $data['bd_area_list'] = $this->model_report_sale->getBDAreaList();
        $data['logistic_list'] = $this->model_report_sale->getLogisticList();
//        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['stations'] = $this->model_station_station->getStationList();
        $data['station_set'] = $this->model_station_station->setFilterStation($filter_warehouse_id_global);


        if($showmap){
            $this->response->setOutput($this->load->view('report/sale_order_map.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('report/sale_order.tpl', $data));
        }

        if($export){
            //设置excel文件属性
            $objPHPExcel->getProperties()->setCreator("ctos")
                ->setLastModifiedBy("ctos")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
            // 设置单元格宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            // 设置行高度
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
            // 设置水平居中
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            // 字体和样式
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
            $objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            //  合并
            $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
            // 表头
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '销售报告')
                ->setCellValue('A2', '订单号')
                ->setCellValue('B2', '订单状态')
                ->setCellValue('C2', '支付状态')
                ->setCellValue('D2', '配送日期')
                ->setCellValue('E2', '下单日期')
                ->setCellValue('F2', '下单时间')
                ->setCellValue('G2', '用户ID')
                ->setCellValue('H2', '商家名')
                ->setCellValue('I2', 'BD')
                ->setCellValue('J2', '小计')
                ->setCellValue('K2', '优惠')
                ->setCellValue('L2', '余额支付')
                ->setCellValue('M2', '应收')
                ->setCellValue('N2', '微信支付')
                ->setCellValue('O2', '积分支付')
                ->setCellValue('P2', '缺货')
                ->setCellValue('Q2', '退货')
                ->setCellValue('R2', '司机')
                ->setCellValue('S2', '财务应收');
            // 内容
            $len = count($data['orders']);
            for ($i = 0 ; $i < $len; $i++) {
                $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['orders'][$i]['order_id'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['orders'][$i]['order_status']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['orders'][$i]['payment_status']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('D' . ($i + 3), $data['orders'][$i]['deliver_date']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['orders'][$i]['order_date']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['orders'][$i]['order_time']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['orders'][$i]['customer_id']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $data['orders'][$i]['merchant_name']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $data['orders'][$i]['bd_name']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), $data['orders'][$i]['sub_total']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), $data['orders'][$i]['discount']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('L' . ($i + 3), $data['orders'][$i]['credit_paid']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('M' . ($i + 3), $data['orders'][$i]['order_due']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('N' . ($i + 3), $data['orders'][$i]['wechat_paid']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('O' . ($i + 3), $data['orders'][$i]['user_point_paid']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('P' . ($i + 3), $data['orders'][$i]['quehuo_credits']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('Q' . ($i + 3), $data['orders'][$i]['tuihuo_credits']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('R' . ($i + 3), $data['orders'][$i]['logistic_driver_title']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('S' . ($i + 3), $data['orders'][$i]['sum_due']);
            }
                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('销售报表');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="销售报表' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
        }
    }

    public function getAreaInfo(){
        $this->load->model('report/sale');
        if($this->request->server['REQUEST_METHOD'] = 'POST' && $this->request->post){
            $json = $this->model_report_sale->getAreaInfo($this->request->post['filter_data']);
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
}