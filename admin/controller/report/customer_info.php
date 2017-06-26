<?php
class ControllerReportCustomerInfo extends Controller {

    public function index() {
        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();

        $this->load->language('report/customer_info');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/sale');

        $this->load->model('report/customer');

        $this->load->model('station/station');

        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_customer_group_id = isset($this->request->get['filter_customer_group_id'])?$this->request->get['filter_customer_group_id']:false;
        $filter_customer_id = isset($this->request->get['filter_customer_id'])?$this->request->get['filter_customer_id']:false;
        $filter_bd_list = isset($this->request->get['filter_bd_list'])?$this->request->get['filter_bd_list']:false;
        $filter_bd_area_list = isset($this->request->get['filter_bd_area_list'])?$this->request->get['filter_bd_area_list']:false;
        $filter_customer_name = isset($this->request->get['filter_customer_name'])?$this->request->get['filter_customer_name']:false;
        $filter_report_type = isset($this->request->get['filter_report_type'])?$this->request->get['filter_report_type']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;

        $url = '';
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        if ($filter_customer_group_id) {
            $url .= '&filter_customer_group_id=' . $filter_customer_group_id;
        }

        if ($filter_customer_id) {
            $url .= '&filter_customer_id=' . $filter_customer_id;
        }

        if ($filter_bd_list) {
            $url .= '&filter_bd_list=' . $filter_bd_list;
        }

        if ($filter_bd_area_list) {
            $url .= '&filter_bd_area_list=' . $filter_bd_area_list;
        }

        if ($filter_customer_name) {
            $url .= '&filter_customer_name=' . $filter_customer_name;
        }

        if ($filter_report_type) {
            $url .= '&filter_report_type=' . $filter_report_type;
        }

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('report/customer_info', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_customer_group_id' => $filter_customer_group_id,
            'filter_customer_id' => $filter_customer_id,
            'filter_bd_list' => $filter_bd_list,
            'filter_bd_area_list' => $filter_bd_area_list,
            'filter_customer_name' => $filter_customer_name,
            'filter_report_type' => $filter_report_type,
        );

        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);

        $data['nofilter'] = false;

        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32){
            //调用查询用户信息数据模型
            $result = $this->model_report_customer->getCustomerInfo($filter_data);
            $data['customers'] = $result['customers'];
            //导出报表
            if(!$export){
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
                    ->setCellValue('A1', '用户信息报表')
                    ->setCellValue('A2', '用户编号')
                    ->setCellValue('B2', '会员等级')
                    ->setCellValue('C2', '用户名')
                    ->setCellValue('D2', '电话')
                    ->setCellValue('E2', '店名')
                    ->setCellValue('F2', '地址')
                    ->setCellValue('G2', '当前用户BD')
                    ->setCellValue('H2', '注册时间')
                    ->setCellValue('I2', '区域名称')
                    ->setCellValue('J2', '当前区域负责BD')
                    ->setCellValue('K2', '最早下单时间')
                    ->setCellValue('L2', '最后下单时间')
                    ->setCellValue('M2', '未下单天数')
                    ->setCellValue('N2', '最近一单');

                // 内容
                for ($i = 0, $len = count($data['customers']); $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['customers'][$i]['customer_id'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['customers'][$i]['customer_grade']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['customers'][$i]['customer_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . ($i + 3), $data['customers'][$i]['telephone'],PHPExcel_Cell_DataType::TYPE_STRING);

                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['customers'][$i]['merchant_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['customers'][$i]['merchant_address']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['customers'][$i]['bd_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $data['customers'][$i]['registe_date']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $data['customers'][$i]['area_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), $data['customers'][$i]['area_bd_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), $data['customers'][$i]['order_first_date']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('L' . ($i + 3), $data['customers'][$i]['order_recent_date']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('M' . ($i + 3), $data['customers'][$i]['order_no_dates'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('N' . ($i + 3), $data['customers'][$i]['recen_order'],PHPExcel_Cell_DataType::TYPE_STRING);


                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':N' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':N' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }

                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('用户信息报表');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="用户报表_' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');

            }

        }
        else{
            $data['nofilter'] = true;
        }



        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');

        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_customer_group_id'] = $filter_customer_group_id;
        $data['filter_customer_id'] = $filter_customer_id;
        $data['filter_bd_list'] = $filter_bd_list;
        $data['filter_bd_area_list'] = $filter_bd_area_list;
        $data['filter_customer_name'] = $filter_customer_name;
        $data['filter_report_type'] = $filter_report_type;

        $data['bd_list'] = $this->model_report_sale->getBdList();
        $data['bd_area_list'] = $this->model_report_sale->getBDAreaList();
        $data['customerGroup'] = $this->model_station_station->getCustomerGroupList();
        $data['report_types'] = array(array("report_id"=>1,"name"=>"新注册"),array("report_id"=>2,"name"=>"下单客户"));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/customer_info.tpl', $data));
    }
}