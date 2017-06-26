<?php
class ControllerUserInventoryOrderMissing extends Controller{
    public function index(){

        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();

        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_order_id = isset($this->request->get['filter_order_id'])?$this->request->get['filter_order_id']:false;
        $filter_confirm_id = isset($this->request->get['filter_confirm_id'])?$this->request->get['filter_confirm_id']:'0';
        $filter_status_id = isset($this->request->get['filter_status_id'])?$this->request->get['filter_status_id']:'0';
        $filter_recover_id = isset($this->request->get['filter_recover_id'])?$this->request->get['filter_recover_id']:'0';
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;
        $this->load->model('user/inventory_order_missing');
        $url = $this->setUrl();
        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_order_id' => $filter_order_id,
            'filter_confirm_id' => $filter_confirm_id,
            'filter_status_id'=>$filter_status_id,
            'filter_recover_id'=>$filter_recover_id
        );


        $this->document->setTitle($this->language->get('库内丢失确认'));
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '库内丢失确认',
            'href' => $this->url->link('report/sorting_staff', 'token=' . $this->session->data['token'], 'SSL')
        );
        $this->load->model('report/sale');
        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);
        $data['nofilter'] = false;

        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32){
            $result = $this->model_user_inventory_order_missing->getInventoryOrderMissing($filter_data);
            $data['inventoryordermissings'] = $result;

            //导出报表
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
                // 设置行高度
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
                // 设置水平居中
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                // 字体和样式
                $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
                $objPHPExcel->getActiveSheet()->getStyle('A2:O2')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A2:O2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A2:O2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                //  合并
                $objPHPExcel->getActiveSheet()->mergeCells('A1:O1');
                // 表头
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '库内丢失订单统计')
                    ->setCellValue('A2', '订单号')
                    ->setCellValue('B2', '添加人')
                    ->setCellValue('C2', '添加时间')
                    ->setCellValue('D2', '确认人')
                    ->setCellValue('E2', '主管确认时间')
                    ->setCellValue('F2', '视频确认时间')
                    ->setCellValue('G2', '是否确认')
                    ->setCellValue('H2', '确认提交时间')
                    ->setCellValue('I2', '找回人')
                    ->setCellValue('J2', '找回时间')
                    ->setCellValue('K2', '是否找回')
                    ->setCellValue('L2', '是否有效')
                    ->setCellValue('M2', '备注');
                $len = count($data['inventoryordermissings']);
                for ($i = 0 ; $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['inventoryordermissings'][$i]['order_id'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['inventoryordermissings'][$i]['added_by']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['inventoryordermissings'][$i]['date_added']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . ($i + 3), $data['inventoryordermissings'][$i]['confirmed_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['inventoryordermissings'][$i]['supervisor_checked'] );
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['inventoryordermissings'][$i]['monitor_checked'] );
                    if($data['inventoryordermissings'][$i]['confirmed'] == 0){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), '否');
                    }else{
                        $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), '是');
                    }

                    $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $data['inventoryordermissings'][$i]['date_confirmed']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $data['inventoryordermissings'][$i]['recovered_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), $data['inventoryordermissings'][$i]['date_recovered']);

                    if($data['inventoryordermissings'][$i]['recovered'] == 0){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), '否');
                    }else{
                        $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), '是');
                    }
                    if($data['inventoryordermissings'][$i]['status'] == 0){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('L' . ($i + 3), '否');
                    }else{
                        $objPHPExcel->getActiveSheet(0)->setCellValue('L' . ($i + 3), '是');
                    }


                    $objPHPExcel->getActiveSheet(0)->setCellValue('M' . ($i + 3), $data['inventoryordermissings'][$i]['memo']);

                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':O' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':O' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('库内丢失订单统计');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="库内丢失订单统计' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');

            }


        } else{
            $data['nofilter'] = true;
        }
        $data['token'] = $this->session->data['token'];
        $data['status_id'] = [['status_id'=>1,'name'=>'是'],['status_id'=>0,'name'=>'否']];
        $data['confirm_id'] = [['confirm_id'=>0,'name'=>'否'],['confirm_id'=>1,'name'=>'是']];
        $data['recover_id'] = [['recover_id'=>0,'name'=>'否'] ,['recover_id'=>1,'name'=>'是']];

        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_order_id'] = $filter_order_id;
        $data['filter_status_id'] = $filter_status_id;
        $data['filter_confirm_id'] = $filter_confirm_id;
        $data['filter_recover_id'] = $filter_recover_id;
        $data['button_filter'] = $this->language->get('button_filter');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_list'] = $this->language->get('库内丢失确认');
        $data['header'] = $this->load->controller('common/header');
        $data['heading_title'] = $this->language->get('heading_tite');
        $data['column_left'] = $this->load->controller('common/column_left');

        $this->response->setOutput($this->load->view('user/inventory_order_missing.tpl',$data));
    }



    private function setUrl() {
        $url = '';
        if (isset($this->request->get['filter_recover_id'])) {
            $url .= '&filter_recover_id=' . $this->request->get['filter_recover_id'];
        }

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }



        return $url;
    }
    public function recovered_order(){
        $order_id = $this->request->post['order_id'];
        $reasons = $this->request->post['reasons'];
        $this->load->model('user/inventory_order_missing');
        $userId = $this->user->getId();
        $result = $this->model_user_inventory_order_missing->recovered_order($order_id,$userId,$reasons);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function confirmed_order(){
        $order_id = $this->request->post['order_id'];
        $this->load->model('user/inventory_order_missing');
        $userId = $this->user->getId();
        $result = $this->model_user_inventory_order_missing->confirmed_order($order_id,$userId);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }


}