<?php
class ControllerReportProductSale extends Controller{
    private $error = array();

    public function index(){
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

        $this->load->language('report/product_sale');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('station/station');

        $this->load->model('report/sale');

        $this->load->model('report/product');

        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_station_id = isset($this->request->get['filter_station_id'])?$this->request->get['filter_station_id']:false;
        $filter_customer_group_id = isset($this->request->get['filter_customer_group_id'])?$this->request->get['filter_customer_group_id']:false;
        $filter_customer_id = isset($this->request->get['filter_customer_id'])?$this->request->get['filter_customer_id']:false;
        $filter_product_id_name = isset($this->request->get['filter_product_id_name'])?$this->request->get['filter_product_id_name']:false;
        $filter_category_id = isset($this->request->get['filter_category_id'])?$this->request->get['filter_category_id']:false;
        $filter_name = isset($this->request->get['filter_name'])?$this->request->get['filter_name']:false;
        $filter_if_category = isset($this->request->get['filter_if_category'])?$this->request->get['filter_if_category']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;

        $url = '';
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        if ($filter_station_id) {
            $url .= '&filter_station_id=' . $filter_station_id;
        }

        if ($filter_customer_group_id) {
            $url .= '&filter_customer_group_id=' . $filter_customer_group_id;
        }

        if ($filter_customer_id) {
            $url .= '&filter_customer_id=' . $filter_customer_id;
        }

        if ($filter_product_id_name) {
            if(is_numeric(trim($filter_product_id_name))){
                $url .= '&filter_product_id_name=' . $filter_product_id_name;
            }else{
                $this->session->data['warning'] = '请正确输入商品ID';
            }
        }

        if ($filter_category_id) {
            $url .= '&filter_category_id=' . $filter_category_id;
        }

        if ($filter_name) {
            $url .= '$filter_name=' . $filter_name;
        }

        if ($filter_if_category) {
            $url .= '$filter_if_category=' . $filter_if_category;
        }

        if (isset($this->session->data['warning'])) {
            $data['warning'] = $this->session->data['warning'];

            unset($this->session->data['warning']);
        } else {
            $data['warning'] = '';
        }

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('report/product_sale', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_station_id' => $filter_station_id,
            'filter_customer_group_id' => $filter_customer_group_id,
            'filter_customer_id' => $filter_customer_id,
            'filter_product_id_name' => $filter_product_id_name,
            'filter_category_id' => $filter_category_id,
            'filter_name' => $filter_name,
            'filter_if_category' => $filter_if_category,
            'filter_warehouse_id_global' => $filter_warehouse_id_global,
        );

        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);

        $data['nofilter'] = false;
        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32 && !$data['warning']){
            $result = $this->model_report_product->getProductSale($filter_data);
            $data['sales'] = $result['sales'];
            $data['s_inventory'] = $result['s_inventory'];
            $data['ori_inv'] = $result['ori_inv'];
            $data['inventory_in'] = $result['inventory_in'];
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


                // 设置行高度
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
                $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

                // 设置水平居中
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                // 字体和样式
                $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
                $objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

                $objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                //  合并
                $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');

                // 表头
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '商品销量报表')
                    ->setCellValue('A2', '商品编号')
                    ->setCellValue('B2', '商品名称')
                    ->setCellValue('C2', '状态')
                    ->setCellValue('D2', '规格')
                    ->setCellValue('E2', '一级分类')
                    ->setCellValue('F2', '二级分类')
                    ->setCellValue('G2', '售价')
                    ->setCellValue('H2', '销量')
                    ->setCellValue('I2', '周转率')
                    ->setCellValue('J2', '可售库存');

                // 内容
                for ($i = 0, $len = count($data['sales']); $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['sales'][$i]['product_id'],PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['sales'][$i]['name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['sales'][$i]['status']);

                    $objPHPExcel->getActiveSheet(0)->setCellValue('D' . ($i + 3), $data['sales'][$i]['formate']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['sales'][$i]['first_category']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['sales'][$i]['second_category']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['sales'][$i]['price']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + 3), $data['sales'][$i]['quangtity']);
                    //计算周转率
                    if(!array_key_exists($data['sales'][$i]['product_id'], $data['s_inventory']) && !array_key_exists($data['sales'][$i]['product_id'], $data['inventory_in'])){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), '无库存计算');
                    }else{
                        if(array_key_exists($data['sales'][$i]['product_id'], $data['s_inventory']) && array_key_exists($data['sales'][$i]['product_id'], $data['inventory_in'])){
                            if((0.5*((2*($data['s_inventory'][$data['sales'][$i]['product_id']]['inv_end']+$data['inventory_in'][$data['sales'][$i]['product_id']]['inv_end']))-$data['sales'][$i]['quangtity'])) != 0){
                                $rate = round($data['sales'][$i]['quangtity']/(0.5*((2*($data['s_inventory'][$data['sales'][$i]['product_id']]['inv_end']+$data['inventory_in'][$data['sales'][$i]['product_id']]['inv_end']))-$data['sales'][$i]['quangtity'])),4);
                                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $rate);
                            }else{
                                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), '除数为0');
                            }
                        }elseif(array_key_exists($data['sales'][$i]['product_id'], $data['s_inventory']) && !array_key_exists($data['sales'][$i]['product_id'], $data['inventory_in'])){
                            if((0.5*((2*$data['s_inventory'][$data['sales'][$i]['product_id']]['inv_end'])-$data['sales'][$i]['quangtity'])) != 0){
                                $rate = round($data['sales'][$i]['quangtity']/(0.5*((2*$data['s_inventory'][$data['sales'][$i]['product_id']]['inv_end'])-$data['sales'][$i]['quangtity'])),4);
                                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $rate);
                            }else{
                                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), '除数为0');
                            }
                        }elseif(!array_key_exists($data['sales'][$i]['product_id'], $data['s_inventory']) && array_key_exists($data['sales'][$i]['product_id'], $data['inventory_in'])){
                            if((0.5*((2*$data['inventory_in'][$data['sales'][$i]['product_id']]['inv_end'])-$data['sales'][$i]['quangtity'])) != 0){
                                $rate = round($data['sales'][$i]['quangtity']/(0.5*((2*$data['inventory_in'][$data['sales'][$i]['product_id']]['inv_end'])-$data['sales'][$i]['quangtity'])),4);
                                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), $rate);
                            }else{
                                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . ($i + 3), '除数为0');
                            }
                        }
                    }
                    if(array_key_exists($data['sales'][$i]['product_id'],$data['ori_inv'])){
                        $ori_inv = $data['ori_inv'][$data['sales'][$i]['product_id']];
                        $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), $ori_inv);
                    }else{
                        $objPHPExcel->getActiveSheet(0)->setCellValue('J' . ($i + 3), '该商品无库存变化记录');
                    }

                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':J' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':J' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }

                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('商品销量报表');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="商品销量报表_' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
            }

        }
        else{
            $data['nofilter'] = true;
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_no_results'] = $this->language->get('text_no_results');

        $data['text_list'] = $this->language->get('text_list');

        $data['button_filter'] = $this->language->get('button_filter');

        $data['token'] = $this->session->data['token'];

        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_station_id'] = $filter_station_id;
        $data['filter_customer_group_id'] = $filter_customer_group_id;
        $data['filter_customer_id'] = $filter_customer_id;
        $data['filter_product_id_name'] = $filter_product_id_name;
        $data['filter_category_id'] = $filter_category_id;
        $data['filter_name'] = $filter_name;
        $data['filter_if_category'] = $filter_if_category;


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['stations'] = $this->model_station_station->getStationList();
        $data['station_set'] = $this->model_station_station->setFilterStation($filter_warehouse_id_global);
        $data['customerGroup'] = $this->model_station_station->getCustomerGroupList();

        $this->response->setOutput($this->load->view('report/product_sale.tpl', $data));
    }

    public function autocomplete(){
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        $this->load->model('report/product');

        $filter_data = array(
            'filter_name' => $filter_name,
            'sort'        => 'product_id',
            'order'       => 'DESC',
            'start' => 0,
            'limit' => 5
        );

        $results = $this->model_report_product->getProducts($filter_data);

        foreach ($results as $result) {
            $json[] = array(
                'product_id' => $result['product_id'],
                'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
            );
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
