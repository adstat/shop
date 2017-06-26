<?php
   // error_reporting(0);

class ControllerReportLogisticInfo extends Controller{
    public function index(){
        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $this->load->language('report/logistic_info');
        $this->document->setTitle($this->language->get('heading_title'));
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '物流信息',
            'href' => $this->url->link('report/logistic_info', 'token=' . $this->session->data['token'], 'SSL')
        );
        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_logistic_driver_list = isset($this->request->get['filter_logistic_driver_list'])?$this->request->get['filter_logistic_driver_list']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;
        $data['token'] = $this->session->data['token'];

        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_logistic_driver_list']=$filter_logistic_driver_list;
        $this->load->model('station/station');
        $this->load->model('report/logistic');
        $this->load->model('logistic/logistic');
        $data['date_gap']= $this->model_report_logistic->dateGap($filter_date_start,$filter_date_end);

        $filter_data=array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_logistic_driver_list'=>$filter_logistic_driver_list,
        );


        $data['nofilter'] = false;

        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32) {
            //获取投诉的次数
            $feadbackcounts = $this->model_report_logistic->getFeadbackCounts($filter_data);

            if(!empty($feadbackcounts)) {
                $tmp_str = '';
                foreach ($feadbackcounts as $k => $v) {
                    $tmp_str .= $v['feadback_id'] . ',';
                }
                $tmp_arr = explode(',', rtrim($tmp_str, ','));
                $counts = array_count_values($tmp_arr);
                $checkname_tmp = $this->model_report_logistic->getcheakname();

                $checkname_arr = array();
                foreach ($checkname_tmp as $k => $v) {
                    $checkname_arr[$v['feadback_id']] = $v['name'];
                }

                $tmp = '';
                foreach ($counts as $k => $v) {
                    if(array_key_exists($k, $checkname_arr)){
                        $tmp[$checkname_arr[$k]] = $v;
                    }
                }
                $data['tmp'] = $tmp;
                $data['counts'] = $counts;
            }

            //获取物流信息的基本数据
            $result = $this->model_report_logistic->getlogisticInfo($filter_data);


            if(!empty($result['logistics'])){
                foreach($result['logistics'] as &$value){
                    //获取件数
                    $order = $this->model_logistic_logistic->getOrderInv($value['order_id']);
                    if(empty($order)){
                        $num=0;
                        $orderQuantity = $this->model_logistic_logistic->getOrderQuantity($value['order_id']);
                        foreach($orderQuantity as $val){
                            $num +=$val['quantity'];
                        }
                        $num = '[未拣]共'.$num.'件';
                        $data['num'] = $num;
                        $data['num_id']  = $value['order_id'];
                    }else{
                        $num = '';
                        if(!empty($order['frame_count']) || !empty($order['frame_meat_count'])){
                            $num .= '框:'.((int)$order['frame_count'] + (int)$order['frame_meat_count']);
                        }
                        if(!empty($order['incubator_count']) || !empty($order['incubator_mi_count'])){
                            $num .= '保:'.($order['incubator_count'] + $order['incubator_mi_count']);
                        }
                        if(!empty($order['foam_count']) || !empty($order['foam_ice_count'])){
                            $num .= '泡:'.((int)$order['foam_count'] + (int)$order['foam_ice_count']);
                        }
                        if(!empty($order['frame_mi_count']) || !empty($order['frame_ice_count'])){
                            $num .= '奶框:'.((int)$order['frame_mi_count'] + (int)$order['frame_ice_count']);
                        }
                        if(!empty($order['box_count'])){
                            $num .= '箱:'.($order['box_count']);
                        }
                    }
                    $data['num'] = $num;
                    $data['num_id']  = $value['order_id'];
                    $value['num']=$num;

                }
            }
            $data['logistics'] = $result['logistics'];

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
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
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
                    ->setCellValue('A1', '物流信息报表')
                    ->setCellValue('A2', '订单编号')
                    ->setCellValue('B2', '仓库平台')
                    ->setCellValue('C2', '商家')
                    ->setCellValue('D2', '收货地址')
                    ->setCellValue('E2', '订单金额')
                    ->setCellValue('F2', 'BD人员')
                    ->setCellValue('G2', '司机')
                    ->setCellValue('H2', '物流评分')
                    ->setCellValue('I2', '到货核对')
                    ->setCellValue('J2', '单据签字')
                    ->setCellValue('K2', '周转箱使用')
                    ->setCellValue('L2',  '用户投诉')
                    ->setCellValue('M2', '事项记录')
                    ->setCellValue('N2', '用户建议')
                    ->setCellValue('O2', '投诉时间');


                // 内容
                $len = count($data['logistics']);
                for ($i = 0 ; $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['logistics'][$i]['order_id'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['logistics'][$i]['name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['logistics'][$i]['shipping_firstname']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . ($i + 3), $data['logistics'][$i]['shipping_address_1'],PHPExcel_Cell_DataType::TYPE_STRING);

                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['logistics'][$i]['total']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['logistics'][$i]['bd_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['logistics'][$i]['logistic_driver_title']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $data['logistics'][$i]['logistic_score']);

                    if(!$data['logistics'][$i]['cargo_check']){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), '无记录');
                    }
                    if($data['logistics'][$i]['cargo_check'] == 1){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), '整件点清，散件未点清');
                    }
                    if($data['logistics'][$i]['cargo_check'] == 2){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), '整散件均当场点清');
                    }
                    if($data['logistics'][$i]['cargo_check'] == 3){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), '没有点清货物');
                    }

                    if(!$data['logistics'][$i]['bill_of']){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), '无记录');
                    }
                    if($data['logistics'][$i]['bill_of'] == 1){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), '有');
                    }
                    if($data['logistics'][$i]['bill_of'] == 2){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), '无');
                    }

                    if(!$data['logistics'][$i]['box']){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), '无记录');
                    }
                    if($data['logistics'][$i]['box'] == 1){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), '有');
                    }
                    if($data['logistics'][$i]['box'] == 2){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), '无');
                    }
                    if($data['logistics'][$i]['box'] == 3){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), '没有散件商品');
                    }

                    $objPHPExcel->getActiveSheet(0)->setCellValue('L' . ($i + 3), $data['logistics'][$i]['checkname']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('M' . ($i + 3), $data['logistics'][$i]['comments']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('N' . ($i + 3), $data['logistics'][$i]['user_comments']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('O' . ($i + 3), $data['logistics'][$i]['date_added']);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':O' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':O' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }
                foreach($tmp as $k=>$v){
                    $string[] = $k.':'.$v.'次';
                }
                $a = implode(',',$string);

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.($len+3), '投诉次数统计');
                $objPHPExcel->getActiveSheet()->getStyle('A'.($len+3))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->mergeCells('A' . ($i + 3) . ':N' . ($i + 3));
                $objPHPExcel->getActiveSheet()->getStyle('A'.($len+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A'.($len+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet(0)->setCellValue('A'.($len+4),$a);
                $objPHPExcel->getActiveSheet()->mergeCells('A' . ($i + 4) . ':N' . ($i + 4));
                $objPHPExcel->getActiveSheet()->getStyle('A'.($len+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A'.($len+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('物流信息报表');
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
        $data['logistic_driver_list'] = $this->model_report_logistic->getLogisticDriver();
        $data['button_filter'] = $this->language->get('button_filter');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['heading_title'] = $this->language->get('heading_title');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('report/logistic_info.tpl',$data ));
    }
}