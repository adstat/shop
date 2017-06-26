<?php
class ControllerReportWarehouseDistribution extends Controller{
    public function index(){

        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $this->load->language('report/warehouse_distribution');

        $filter_station_id = isset($this->request->get['filter_station_id'])?$this->request->get['filter_station_id']:false;
        $filter_date_start = isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:date('Y-m-d');
        $filter_date_end = isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:date('Y-m-d');
        $filter_product_name = isset($this->request->get['filter_product_name'])?$this->request->get['filter_product_name']:false;
        $filter_product_type = isset($this->request->get['filter_product_type'])?$this->request->get['filter_product_type']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;

//        if (isset($this->request->get['sort'])) {
//            $sort = $this->request->get['sort'];
//        } else {
//            $sort = 'A.order_id';
//        }

//        if (isset($this->request->get['order'])) {
//            $order = $this->request->get['order'];
//
//        } else {
//            $order = 'ASC';
//        }

//        if (isset($this->request->get['page'])) {
//            $page = $this->request->get['page'];
//
//        } else {
//            $page = 1;
//        }
       $url = $this->setUrl();

//        $data['sort_order'] = $this->url->link('report/warehouse_distribution', 'token=' . $this->session->data['token'] . '&sort=A.order_id' . $url, 'SSL');


        $filter_data = array(
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'filter_station_id' => $filter_station_id,
            'filter_product_id_name' => $filter_product_name,
            'filter_product_type'=>$filter_product_type,
//            'sort' => $sort,
//            'order' => $order,
//            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
//            'limit' => $this->config->get('config_limit_admin')
        );
        $data['nofilter'] = false;


        $this->load->model('station/station');
        $this->load->model('report/sale');
        $this->load->model('report/warehouse_distribution');
        $this->load->language('report/warehouse_distribution');
        $this->document->setTitle($this->language->get('heading_title'));
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '仓库分拣报表',
            'href' => $this->url->link('report/warehouse_distribution', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['date_gap']= $this->model_report_sale->dateGap($filter_date_start,$filter_date_end);

        $data['nofilter'] = false;
        if(isset($this->request->get['filter_date_start']) && isset($this->request->get['filter_date_end']) && $data['date_gap'] < 32){

          //  $order_distr_total = $this->model_report_warehouse_distribution->getOrderDistrTotal($filter_data);

            $result = $this->model_report_warehouse_distribution->getOrderDistr($filter_data);
            $data['orderdistrs'] = $result;
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
                    ->setCellValue('A1', '仓库分拣报表')
                    ->setCellValue('A2', '订单号')
                    ->setCellValue('B2', '仓库平台')
                    ->setCellValue('C2', '商品分类属性')
                    ->setCellValue('D2', '下单时间')
                    ->setCellValue('E2', '分拣人')
                    ->setCellValue('F2', '分拣数量');
                $len = count($data['orderdistrs']);
                for ($i = 0 ; $i < $len; $i++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['orderdistrs'][$i]['order_id'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['orderdistrs'][$i]['station_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['orderdistrs'][$i]['product_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . ($i + 3), $data['orderdistrs'][$i]['date_added']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['orderdistrs'][$i]['inventory_name']);
                    $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['orderdistrs'][$i]['quantity']);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                }

                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':H' . ($i + 3))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(16);
                // 命名sheet
                $objPHPExcel->getActiveSheet()->setTitle('仓库分拣报表');
                //工作sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="仓库分拣报表' . $date . '_' . $userName . '.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');

            }

//            $pagination = new Pagination();
//            $pagination->total = $order_distr_total;
//            $pagination->page = $page;
//            $pagination->limit = $this->config->get('config_limit_admin');
//            $pagination->url = $this->url->link('report/warehouse_distribution', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
//            $data['pagination'] = $pagination->render();
//            $data['results'] = sprintf($this->language->get('text_pagination'), ($order_distr_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_distr_total - $this->config->get('config_limit_admin'))) ? $order_distr_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_distr_total, ceil($order_distr_total / $this->config->get('config_limit_admin')));

        } else{
            $data['nofilter'] = true;
        }
        $data['token'] = $this->session->data['token'];
        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['filter_product_name'] = $filter_product_name;
        $data['filter_station_id'] = $filter_station_id;
        $data['filter_product_type'] = $filter_product_type;
//        $data['sort'] = $sort;
//        $data['order'] = $order;

        $data['stations'] = $this->model_station_station->getStationList();
        $data['product_types'] = $this->model_report_warehouse_distribution->getProductList();
        $data['button_filter'] = $this->language->get('button_filter');
        $data['header'] = $this->load->controller('common/header');
        $data['entry_date_start'] = $this->language->get('entry_date_start');
        $data['entry_date_end'] = $this->language->get('entry_date_end');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['heading_title'] = $this->language->get('heading_tite');
        $data['column_left'] = $this->load->controller('common/column_left');
        $this->response->setOutput($this->load->view('report/warehouse_distribution.tpl',$data));
    }

    private function setUrl() {
        $url = '';

        if (isset($this->request->get['filter_station_id'])) {
            $url .= '&filter_station_id=' . $this->request->get['filter_station_id'];
        }

        if (isset($this->request->get['filter_product_id_name'])) {
            $url .= '&filter_product_id_name=' . $this->request->get['filter_product_id_name'];
        }


        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }


//        if (isset($this->request->get['page'])) {
//            $url .= '&page=' . $this->request->get['page'];
//        }

//        if (isset($this->request->get['sort'])) {
//            $url .= '&sort=' . $this->request->get['sort'];
//
//        }


//        if (isset($this->request->get['order'])) {
//            $url .= '&order=' . $this->request->get['order'];
//
//        }
        return $url;
    }
}