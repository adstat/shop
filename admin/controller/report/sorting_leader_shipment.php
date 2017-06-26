<?php
class ControllerReportSortingLeaderShipment extends Controller{
    public function index(){
        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_check_id =isset($this->request->get['filter_check_id'])?$this->request->get['filter_check_id']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;

        $this->load->model('report/sorting_leader_shipment');
        $url = $this->setUrl();
        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_check_id' => $filter_check_id,
        );
        $this->document->setTitle($this->language->get('分拣班组长报表'));
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '分拣班组长报表',
            'href' => $this->url->link('report/early_shipment', 'token=' . $this->session->data['token'], 'SSL')
        );

        $this->load->model('report/sale');
        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);
        $data['nofilter'] = false;

        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32){
            $result = $this->model_report_sorting_leader_shipment->getSortingLeaderShipment($filter_data);
            $data['sortingleadershipments'] = $result;
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

                // 设置行高度
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
                // 设置水平居中
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                // 字体和样式
                $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
                $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                //  合并
                $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
                // 表头
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '分拣班组长报表')
                    ->setCellValue('A2', '订单号')
                    ->setCellValue('B2', '旧货位号')
                    ->setCellValue('C2', '新货位号')
                    ->setCellValue('D2', '核对筐号')
                    ->setCellValue('E2', '出错原因')
                    ->setCellValue('F2', '添加人')
                    ->setCellValue('G2', '添加日期');
                $len = count($data['sortingleadershipments']);

                for ($i = 0 ; $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['sortingleadershipments'][$i]['order_id'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['sortingleadershipments'][$i]['old_inv_comment']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['sortingleadershipments'][$i]['new_inv_comment']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . ($i + 3), $data['sortingleadershipments'][$i]['container_id']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['sortingleadershipments'][$i]['reason_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['sortingleadershipments'][$i]['username']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['sortingleadershipments'][$i]['date_added']);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('分拣班组长报表');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="分拣班组长报表' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');



            }


        } else{
            $data['nofilter'] = true;
        }

        $data['token'] = $this->session->data['token'];
        $data['check_list'] = $this->model_report_sorting_leader_shipment->getCheckReturnList();
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_check_id'] =$filter_check_id;
        $data['button_filter'] = $this->language->get('button_filter');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_list'] = $this->language->get('分拣班组长报表');
        $data['header'] = $this->load->controller('common/header');
        $data['heading_title'] = $this->language->get('heading_tite');
        $data['column_left'] = $this->load->controller('common/column_left');


        $this->response->setOutput($this->load->view('report/sorting_leader_shipment.tpl',$data));


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