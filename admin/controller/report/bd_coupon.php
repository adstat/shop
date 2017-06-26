<?php
class ControllerReportBdCoupon extends Controller {
    public function index(){


        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $filter_station_id = isset($this->request->get['filter_station_id'])?$this->request->get['filter_station_id']:false;
        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_bd_list = isset($this->request->get['filter_bd_list'])?$this->request->get['filter_bd_list']:false;
        $filter_bd_area_list = isset($this->request->get['filter_bd_area_list'])?$this->request->get['filter_bd_area_list']:false;
        $filter_bd_num = isset($this->request->get['filter_bd_num'])?$this->request->get['filter_bd_num']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;
        $url = $this->setUrl();
        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_station_id' => $filter_station_id,
            'filter_bd_list' => $filter_bd_list,
            'filter_bd_area_list'=>$filter_bd_area_list,
            'filter_bd_num'=>$filter_bd_num
        );
        $this->document->setTitle($this->language->get('优惠券使用报表'));
        $this->load->model('station/station');
        $this->load->model('report/sale');
        $this->load->model('report/bd_coupon');
        $data['stations'] = $this->model_station_station->getStationList();
        $data['bd_list'] = $this->model_report_sale->getBdList();
        $data['bd_area_list'] = $this->model_report_sale->getBDAreaList();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '优惠券使用报表',
            'href' => $this->url->link('report/bd_coupon', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);
        $data['nofilter'] = false;
        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32){
            $result = $this->model_report_bd_coupon->getBdCoupon($filter_data);
            $data['bdcoupons'] = $result;

            if($export) {
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
                // 设置行高度
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
                // 设置水平居中
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                // 字体和样式
                $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
                $objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                //  合并
                $objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
                // 表头
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '优惠券使用报表')
                    ->setCellValue('A2', '编号')
                    ->setCellValue('B2', '名称')
                    ->setCellValue('C2', '订单号')
                    ->setCellValue('D2', '订单状态')
                    ->setCellValue('E2', '下单日期')
                    ->setCellValue('F2', '商家ID')
                    ->setCellValue('G2', '商家地址')
                    ->setCellValue('H2', 'BD归属')
                    ->setCellValue('I2', '商家会员等级')
                    ->setCellValue('J2', '商家区域');
                $len = count($data['bdcoupons']);
                for ($i = 0 ; $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['bdcoupons'][$i]['coupon_id'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['bdcoupons'][$i]['coupon_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['bdcoupons'][$i]['order_id']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['bdcoupons'][$i]['status_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . ($i + 3), $data['bdcoupons'][$i]['date_added']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['bdcoupons'][$i]['customer_id']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['bdcoupons'][$i]['merchant_address']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['bdcoupons'][$i]['bd_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $data['bdcoupons'][$i]['area_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $data['bdcoupons'][$i]['level_name']);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':M' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':M' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }

                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':M' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':M' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('优惠券使用报表');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="优惠券使用报表' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');

            }

        }else{
            $data['nofilter'] = true;
        }
        $data['token'] = $this->session->data['token'];
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_station_id'] = $filter_station_id;
        $data['filter_bd_list'] = $filter_bd_list;
        $data['filter_bd_area_list'] = $filter_bd_area_list;
        $data['filter_bd_num'] = $filter_bd_num;
        $data['button_filter'] = $this->language->get('button_filter');
        $data['header'] = $this->load->controller('common/header');
        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');
        $data['text_list'] = $this->language->get('优惠券使用报表');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['heading_title'] = $this->language->get('heading_tite');
        $data['column_left'] = $this->load->controller('common/column_left');
        $this->response->setOutput($this->load->view('report/bd_coupon.tpl',$data));
    }

    private function setUrl() {
        $url = '';

        if (isset($this->request->get['filter_station_id'])) {
            $url .= '&filter_station_id=' . $this->request->get['filter_station_id'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if (isset($this->request->get['filter_bd_list'])) {
            $url .= '&filter_bd_list=' . $this->request->get['filter_bd_list'];
        }
        if (isset($this->request->get['filter_bd_area_list'])) {
            $url .= '&filter_bd_area_list=' . $this->request->get['filter_bd_area_list'];
        }
        if (isset($this->request->get['filter_bd_num'])) {
            $url .= '&filter_bd_num=' . $this->request->get['filter_bd_num'];
        }


        return $url;
    }
}