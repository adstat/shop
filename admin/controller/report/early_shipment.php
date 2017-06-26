<?php
class ControllerReportEarlyShipment extends Controller{
    public function index(){

        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();

        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_instock_id = isset($this->request->get['filter_instock_id'])?$this->request->get['filter_instock_id']:'0';
        $filter_repack_id = isset($this->request->get['filter_repack_id'])?$this->request->get['filter_repack_id']:'0';
        $filter_logistic_list =isset($this->request->get['filter_logistic_list'])?$this->request->get['filter_logistic_list']:false;
        $filter_return_id =isset($this->request->get['filter_return_id'])?$this->request->get['filter_return_id']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;

        $this->load->model('report/early_shipment');
        $url = $this->setUrl();
        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_instock_id' => $filter_instock_id,
            'filter_repack_id' => $filter_repack_id,
            'filter_logistic_list'=>$filter_logistic_list,
            'filter_return_id' => $filter_return_id
        );
        $this->document->setTitle($this->language->get('早班出库报表'));
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '早班出库报表',
            'href' => $this->url->link('report/early_shipment', 'token=' . $this->session->data['token'], 'SSL')
        );

        $this->load->model('report/sale');
        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);
        $data['nofilter'] = false;

        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32){
            $result = $this->model_report_early_shipment->getEarlyShipment($filter_data);
            $data['earlyshipments'] = $result;
            if($export) {
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
                // 设置行高度
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
                // 设置水平居中
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                // 字体和样式
                $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
                $objPHPExcel->getActiveSheet()->getStyle('A2:Q2')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A2:Q2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A2:Q2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                //  合并
                $objPHPExcel->getActiveSheet()->mergeCells('A1:Q1');
                // 表头
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '早班出库报表')
                    ->setCellValue('A2', '订单号')
                    ->setCellValue('B2', '下单日期')
                    ->setCellValue('C2', '区域')
                    ->setCellValue('D2', '商品编号')
                    ->setCellValue('E2', '商品名称')
                    ->setCellValue('F2', '是否外仓')
                    ->setCellValue('G2', '是否散发')
                    ->setCellValue('H2', '订单商品数量')
                    ->setCellValue('I2', '订单商品金额')
                    ->setCellValue('J2', '分拣数量')
                    ->setCellValue('K2', '散件遗失数量')
                    ->setCellValue('L2', '散件遗失金额')
                    ->setCellValue('M2', '分拣人')
                    ->setCellValue('N2', '配送司机')
                    ->setCellValue('O2', '司机电话')
                    ->setCellValue('P2', '记录人');
                $len = count($data['earlyshipments']);
                for ($i = 0 ; $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['earlyshipments'][$i]['order_id'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['earlyshipments'][$i]['date_added']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['earlyshipments'][$i]['name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . ($i + 3), $data['earlyshipments'][$i]['product_id']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['earlyshipments'][$i]['product_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['earlyshipments'][$i]['instock']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['earlyshipments'][$i]['repack']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $data['earlyshipments'][$i]['order_qty']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $data['earlyshipments'][$i]['order_total']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), $data['earlyshipments'][$i]['quantity']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), $data['earlyshipments'][$i]['deliver_missing_qty']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('L' . ($i + 3), $data['earlyshipments'][$i]['deliver_missing_total']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('M' . ($i + 3), $data['earlyshipments'][$i]['added_by']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('N' . ($i + 3), $data['earlyshipments'][$i]['logistic_driver_title']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('O' . ($i + 3), $data['earlyshipments'][$i]['logistic_driver_phone']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('P' . ($i + 3), $data['earlyshipments'][$i]['adduser']);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':Q' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':Q' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }

                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':Q' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':Q' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('早班出库报表');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="早班出库报表' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');

            }

        } else{
            $data['nofilter'] = true;
        }

        $data['logistic_list'] = $this->model_report_sale->getLogisticList();


        $data['return_list'] = $this->model_report_early_shipment->getReturnList();
        $data['token'] = $this->session->data['token'];
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_instock_id'] = $filter_instock_id;
        $data['filter_repack_id'] = $filter_repack_id;
        $data['filter_logistic_list'] = $filter_logistic_list;
        $data['filter_return_id'] = $filter_return_id;
        $data['button_filter'] = $this->language->get('button_filter');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_list'] = $this->language->get('早班出货报表');
        $data['header'] = $this->load->controller('common/header');
        $data['heading_title'] = $this->language->get('heading_tite');
        $data['column_left'] = $this->load->controller('common/column_left');





        $this->response->setOutput($this->load->view('report/early_shipment.tpl',$data));
    }


    private function setUrl() {
        $url = '';

        if (isset($this->request->get['filter_instock_id'])) {
            $url .= '&filter_instock_id=' . $this->request->get['filter_instock_id'];
        }

        if (isset($this->request->get['filter_repack_id'])) {
            $url .= '&filter_repack_id=' . $this->request->get['filter_repack_id'];
        }

        if (isset($this->request->get['filter_return_id'])) {
            $url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
        }
        if (isset($this->request->get['filter_logistic_list'])) {
            $url .= '&filter_logistic_list=' . $this->request->get['filter_logistic_list'];
        }


        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }


        return $url;
    }

}
?>