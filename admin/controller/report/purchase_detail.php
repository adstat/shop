<?php
class ControllerReportPurchaseDetail extends Controller {
    public function index() {
        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $data = array();

        $this->load->language('report/purchase_detail');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('report/sale');

        $this->load->model('report/purchase');

        $this->load->model('purchase/pre_purchase');

        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_supplier_type = isset($this->request->get['filter_supplier_type'])?$this->request->get['filter_supplier_type']:false;
        $filter_order_status_id = isset($this->request->get['filter_order_status_id'])?$this->request->get['filter_order_status_id']:false;
        $filter_order_checkout_status_id = isset($this->request->get['filter_order_checkout_status_id'])?$this->request->get['filter_order_checkout_status_id']:false;
        $filter_purchase_order_id = isset($this->request->get['filter_purchase_order_id'])?$this->request->get['filter_purchase_order_id']:false;
        $filter_order_type = isset($this->request->get['filter_order_type'])?$this->request->get['filter_order_type']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;

        $url = '';
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        if (isset($this->request->get['filter_supplier_type'])) {
            $url .= '&filter_supplier_type=' . $filter_supplier_type;
        }

        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $filter_order_status_id;
        }

        if (isset($this->request->get['filter_order_checkout_status_id'])) {
            $url .= '&filter_order_checkout_status_id=' . $filter_order_checkout_status_id;
        }

        if (isset($this->request->get['filter_purchase_order_id'])) {
            $url .= '&filter_purchase_order_id=' . $filter_purchase_order_id;
        }

        if (isset($this->request->get['filter_order_type'])) {
            $url .= '&filter_order_type=' . $filter_order_type;
        }


        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('report/purchase_detail', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_supplier_type' => $filter_supplier_type,
            'filter_order_status_id' => $filter_order_status_id,
            'filter_order_checkout_status_id' => $filter_order_checkout_status_id,
            'filter_purchase_order_id' => $filter_purchase_order_id,
            'filter_order_type' => $filter_order_type,
        );

        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);

        $data['nofilter'] = false;

        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32) {
            $result = $this->model_report_purchase->getPurchaseDetail($filter_data);
            $data['details'] = $result['details'];

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
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
                $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
                $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);

                // 设置行高度
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

                // 设置水平居中
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                // 字体和样式
                $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
                $objPHPExcel->getActiveSheet()->getStyle('A2:W2')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

                $objPHPExcel->getActiveSheet()->getStyle('A2:W2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A2:W2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                //  合并
                $objPHPExcel->getActiveSheet()->mergeCells('A1:W1');

                // 表头
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '采购明细报告')
                    ->setCellValue('A2', '采购单号')
                    ->setCellValue('B2', '下单日期')
                    ->setCellValue('C2', '计划到货日期')
                    ->setCellValue('D2', '实际收货日期')
                    ->setCellValue('E2', '供应商编号')
                    ->setCellValue('F2', '供应商名称')
                    ->setCellValue('G2', '支付状态')
                    ->setCellValue('H2', '采购单状态')
                    ->setCellValue('I2', '采购单类型')
                    ->setCellValue('J2', '商品编号')
                    ->setCellValue('K2', '商品名称')
                    ->setCellValue('L2', '商品主分类')
                    ->setCellValue('M2', '商品分类')
                    ->setCellValue('N2', '原料编号')
                    ->setCellValue('O2', '原料名称')
                    ->setCellValue('P2', '原料主分类')
                    ->setCellValue('Q2', '商品分类')
                    ->setCellValue('R2', '供应商采购数量')
                    ->setCellValue('S2', '采购数量')
                    ->setCellValue('T2', '采购单价')
                    ->setCellValue('U2', '小计')
                    ->setCellValue('V2', '数量（到货）')
                    ->setCellValue('W2', '金额（收）');

                // 内容
                for ($i = 0, $len = count($data['details']); $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['details'][$i]['purchase_order_id'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['details'][$i]['date_purchase']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['details'][$i]['date_deliver_plan']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('D' . ($i + 3), $data['details'][$i]['date_deliver_receive']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('E' . ($i + 3), $data['details'][$i]['supplier_id'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['details'][$i]['supplier_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['details'][$i]['checkout_status']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $data['details'][$i]['purchase_status']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $data['details'][$i]['purchase_type']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('J' . ($i + 3), $data['details'][$i]['product_id'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('K' . ($i + 3), $data['details'][$i]['product_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('L' . ($i + 3), $data['details'][$i]['first_category']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('M' . ($i + 3), $data['details'][$i]['second_category']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('N' . ($i + 3), $data['details'][$i]['sku_id'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('O' . ($i + 3), $data['details'][$i]['sku_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('P' . ($i + 3), $data['details'][$i]['sku_first_category']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('Q' . ($i + 3), $data['details'][$i]['sku_second_category']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('R' . ($i + 3), $data['details'][$i]['supplier_quantity'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('S' . ($i + 3), $data['details'][$i]['quantity'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('T' . ($i + 3), $data['details'][$i]['price'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('U' . ($i + 3), $data['details'][$i]['purchase_total'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('V' . ($i + 3), $data['details'][$i]['quantity_move'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('W' . ($i + 3), $data['details'][$i]['purchase_real_total'],PHPExcel_Cell_DataType::TYPE_STRING);


                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':W' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':W' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }

                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('采购明细报表');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="采购明细报表_' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');

            }
        }else{
            $data['nofilter'] = true;
        }


        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');

        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_supplier_type'] = $filter_supplier_type;
        $data['filter_order_status_id'] = $filter_order_status_id;
        $data['filter_order_checkout_status_id'] = $filter_order_checkout_status_id;
        $data['filter_purchase_order_id'] = $filter_purchase_order_id;
        $data['filter_order_type'] = $filter_order_type;

        $data['supplier_types'] = $this->model_purchase_pre_purchase->getSupplierTypes();
        $data['order_statuses'] = $this->model_purchase_pre_purchase->getStatuses();
        $data['order_checkout_statuses'] = array(array("order_checkout_status_id"=>1,"name"=>"未支付"),array("order_checkout_status_id"=>2,"name"=>"已支付"));
        $data['order_types'] = array(array("order_type_id"=>1,"name"=>"采购单"),array("order_type_id"=>2,"name"=>"退货单"));


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view('report/purchase_detail.tpl', $data));
    }
}
