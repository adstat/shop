<?php
class  ControllerReportLogisticDriver extends Controller{
    public function index (){
        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();

        $filter_station = isset($this->request->get['filter_station'])?$this->request->get['filter_station']:false;
        $filter_logistic_driver_list = isset($this->request->get['filter_logistic_driver_list'])?$this->request->get['filter_logistic_driver_list']:false;
        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_order_payment_status = isset($this->request->get['filter_order_payment_status'])?$this->request->get['filter_order_payment_status']:false;
        $filter_order_deliver_status = isset($this->request->get['filter_order_deliver_status'])?$this->request->get['filter_order_deliver_status']:false;
        $filter_order_status = isset($this->request->get['filter_order_status'])?$this->request->get['filter_order_status']:false;
        $filter_bd_area_list = isset($this->request->get['filter_bd_area_list'])?$this->request->get['filter_bd_area_list']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;

        $this->load->model('report/logistic_driver');
        $data['date_gap']= $this->model_report_logistic_driver->dateGap($filter_date_start,$filter_date_end);




        $url = '';
        if ($filter_station) {
            $url .= '&filter_station=' . $filter_station;
        }
        if ($filter_logistic_driver_list) {
            $url .= '&filter_station=' . $filter_logistic_driver_list;
        }
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        if ($filter_order_payment_status) {
            $url .= '&filter_order_status_id=' . $filter_order_payment_status;
        }
        if ($filter_order_deliver_status) {
            $url .= '&filter_order_deliver_id=' . $filter_order_deliver_status;
        }

        if ($filter_order_status) {
            $url .= '&filter_order_status=' . $filter_order_status;
        }
        if ($filter_bd_area_list) {
            $url .= '&filter_bd_area_list=' . $filter_bd_area_list;
        }



        $this->load->language('report/logistic_driver');
        $this->document->setTitle($this->language->get('heading_title'));
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '物流报表',
            'href' => $this->url->link('report/logistic_driver', 'token=' . $this->session->data['token'], 'SSL')
        );

        $filter_data = array(
            'filter_station' => $filter_station,
            'filter_logistic_driver_list' =>$filter_logistic_driver_list,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_order_payment_status' => $filter_order_payment_status,
            'filter_order_deliver_status' => $filter_order_deliver_status,
            'filter_order_status' => $filter_order_status,
            'filter_bd_area_list' => $filter_bd_area_list,

        );

        $data['nofilter'] = false;

        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32) {
            $result = $this->model_report_logistic_driver->getLogisticInfo($filter_data);

            $data['logistics'] = $result;
            //导出报表
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
                    ->setCellValue('A1', '物流数据报表')
                    ->setCellValue('A2', '订单号')
                    ->setCellValue('B2', '仓库平台')
                    ->setCellValue('C2', '司机编号')
                    ->setCellValue('D2', '司机名称')
                    ->setCellValue('E2', '配送地址')
                    ->setCellValue('F2', '订单状态')
                    ->setCellValue('G2', '支付状态')
                    ->setCellValue('H2', '配送状态')
                    ->setCellValue('I2', '行政区域')
                    ->setCellValue('J2', '用户区域')
                    ->setCellValue('K2', '篮框数')
                    ->setCellValue('L2', '纸箱数');

                //内容
                $len = count($data['logistics']);
                for ($i = 0 ; $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['logistics'][$i]['order_id'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['logistics'][$i]['station_id']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['logistics'][$i]['logistic_driver_id']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . ($i + 3), $data['logistics'][$i]['logistic_driver_title'],PHPExcel_Cell_DataType::TYPE_STRING);

                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['logistics'][$i]['shipping_address_1']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['logistics'][$i]['order_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['logistics'][$i]['payment_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $data['logistics'][$i]['deliver_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $data['logistics'][$i]['district']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), $data['logistics'][$i]['name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), $data['logistics'][$i]['frame_count']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('L' . ($i + 3), $data['logistics'][$i]['box_count']);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':N' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':N' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }

                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':N' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':N' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);

                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('物流数据报表');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="物流报表_' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
            }

        }else{
            $data['nofilter'] = true;
        }


       // $data['date_gap']= $this->model_report_logistic_driver->dateGap($filter_date_start,$filter_date_end);

        $data['token'] = $this->session->data['token'];

        $data['filter_station'] = $filter_station;
        $data['filter_logistic_driver_list'] = $filter_logistic_driver_list;
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_order_payment_status'] = $filter_order_payment_status;
        $data['filter_order_deliver_status'] = $filter_order_deliver_status;
        $data['filter_order_status'] = $filter_order_status;

        $data['filter_bd_area_list'] = $filter_bd_area_list;

        $data['order_stations'] = array(1=>"生鲜",2=>"快消");
        $data['logistic_driver_list'] =  $this->model_report_logistic_driver->getLogisticDriver();
        $data['order_payment_statuses'] = $this->model_report_logistic_driver->getOrderPaymentStatus();
        $data['order_deliver_statuses'] = $this->model_report_logistic_driver->getOrderDeliverStatus();
        $data['order_status'] = $this->model_report_logistic_driver->getOrderStatus();
        $data['bd_area_list'] = $this->model_report_logistic_driver->getBDAreaList();

        $data['text_list'] = $this->language->get('text_list');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['heading_title'] = $this->language->get('heading_title');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/logistic_driver.tpl',$data ));
    }

}
