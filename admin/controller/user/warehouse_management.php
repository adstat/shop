<?php
class ControllerUserWarehouseManagement extends Controller{


    public function index(){

        $userName = $this->user->getUserName();
        $date = date('Y-m-d');
        //实例化PHPExcel
        require_once (DIR_APPLICATION.'composer/vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        $objPHPExcel = new PHPExcel();

        $this->load->model('user/warehouse_management');
        $this->document->setTitle('仓库货物管理');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');


        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '仓库货位管理',
            'href' => $this->url->link('user/warehouse_management', 'token=' . $this->session->data['token'], 'SSL')
        );
        $this->load->model('station/station');
        $data['stations'] = $this->model_station_station->getStationList();
        $data['stations_section'] = $this->model_user_warehouse_management->getStationsSection();

        $result = $this->model_user_warehouse_management->getStationSectionTitle();

        $data['button_filter'] = $this->language->get('button_filter');
        $filter_station_id = isset($this->request->get['filter_station_id'])?$this->request->get['filter_station_id']:false;
        $filter_station_section_type_id =  isset($this->request->get['filter_station_section_type_id'])?$this->request->get['filter_station_section_type_id']:false;
        $filter_warehouse_id_global = isset($this->request->get['filter_warehouse_id_global'])?$this->request->get['filter_warehouse_id_global']:false;
        $filter_station_section_title =  isset($this->request->get['filter_station_section_title'])?$this->request->get['filter_station_section_title']:false;
        $export = isset($this->request->get['export']) ? $this->request->get['export']:0;

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = $this->getUrlParam();

        $data['nofilter'] = false;
        $filter_data = array(
            'filter_station_id' => $filter_station_id,
            'filter_station_section_type_id' => $filter_station_section_type_id,
            'filter_station_section_title' =>$filter_station_section_title,
            'filter_warehouse_id_global' =>$filter_warehouse_id_global,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );




        $ProductSections_total = $this->model_user_warehouse_management->getTotalProductSections($filter_data);
        $url = '';

        $data['add'] = $this->url->link('user/warehouse_management/add', 'token=' . $this->session->data['token'] . '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] . $url, 'SSL');


        $data['token'] = $this->session->data['token'];
        $results = $this->model_user_warehouse_management->getSectionProducts($filter_data);
        $data['station_section_info'] = $results;
        $data['productsections'] = array();

        foreach($results as $result){
            $data['productsections'][] = array(
                'station_section_id' =>$result['product_section_id'],
                'product_id' =>$result['product_id'],
                'product_name' =>$result['productname'],
                'station_id' =>$result['stationname'],
                'sectionname'=>$result['sectionname'],
                'station_section_title'=>$result['product_section_title'],
                'sort'  => $result['sort'],
                'edit'      => $this->url->link('user/warehouse_management/edit', 'token=' . $this->session->data['token'] . '&station_section_id=' . $result['product_section_id'] .'&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] . $url, 'SSL')
            );
        }

        //导出报表
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
                ->setCellValue('A1', '仓库区域明细表')
                ->setCellValue('A2', '序号')
                ->setCellValue('B2', '仓库分区名称')
                ->setCellValue('C2', '商品ID')
                ->setCellValue('D2', '商品名称')
                ->setCellValue('E2', '仓库平台')
                ->setCellValue('F2', '仓库区域')
                ->setCellValue('G2', '排序');
            $len = count($data['station_section_info']);
            for ($i = 0 ; $i < $len; $i++) {
                $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('A' . ($i + 3), $data['station_section_info'][$i]['station_section_id'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + 3), $data['station_section_info'][$i]['station_section_title']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + 3), $data['station_section_info'][$i]['product_id']);
                $objPHPExcel->getActiveSheet(0)->setCellValueExplicit('D' . ($i + 3), $data['station_section_info'][$i]['productname']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + 3), $data['station_section_info'][$i]['stationname']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + 3), $data['station_section_info'][$i]['sectionname']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + 3), $data['station_section_info'][$i]['sort']);
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
            header('Content-Disposition: attachment;filename="仓库分区明细表' . $date . '_' . $userName . '.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }




        $url = $this->getUrlParam();
        $pagination = new Pagination();
        $pagination->total = $ProductSections_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('user/warehouse_management', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($ProductSections_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($ProductSections_total - $this->config->get('config_limit_admin'))) ? $ProductSections_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $ProductSections_total, ceil($ProductSections_total / $this->config->get('config_limit_admin')));

        $data['filter_station_id'] = $filter_station_id;
        $data['filter_station_section_type_id'] = $filter_station_section_type_id;
        $data['filter_station_section_title'] = $filter_station_section_title;
        $this->response->setOutput($this->load->view('user/warehouse_management.tpl',$data));
    }


    public function getUrlParam()
    {
        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['filter_station_id'])) {
            $url .= '&filter_station_id=' . $this->request->get['filter_station_id'];
        }

        if (isset($this->request->get['filter_station_section_id'])) {
            $url .= '&filter_station_section_id=' . $this->request->get['filter_station_section_id'];
        }

        if (isset($this->request->get['filter_station_section_title'])) {
            $url .= '&filter_station_section_title=' . $this->request->get['filter_station_section_title'];
        }

        return $url;
    }

    public function add(){

        $this->load->model('user/warehouse_management');
        $this->document->setTitle('仓库货位管理');
        $userId = $this->user->getId();
        $filter_warehouse_id_global = isset($this->request->get['filter_warehouse_id_global'])?$this->request->get['filter_warehouse_id_global']:false;
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $station_section = $this->request->post['station_section_title'];
            $station_section_title = trim($station_section);

            if($station_section_title){
                $parent_station_section= substr($station_section_title,0,strrpos($station_section_title,"-"));
                $this->model_user_warehouse_management->addProductSection($this->request->post,$userId,$parent_station_section,$filter_warehouse_id_global);
            }

            $url = '';

//            $this->response->redirect($this->url->link('user/warehouse_management', 'token=' . $this->session->data['token'] . $url, 'SSL'));

        }


        $this->getForm();
    }

    public function edit(){

        $this->load->model('user/warehouse_management');

        $this->document->setTitle('仓库货位管理');
        $userId = $this->user->getId();
        $filter_warehouse_id_global = isset($this->request->get['filter_warehouse_id_global'])?$this->request->get['filter_warehouse_id_global']:false;
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $station_section = $this->request->post['station_section_title'];
            $station_section_title = trim($station_section);
            if($station_section_title){
                $parent_station_section= substr($station_section_title,0,strrpos($station_section_title,"-"));

                $updateProductsSectionInfo = $this->model_user_warehouse_management->updateProductsSectionInfo ($this->request->get['station_section_id'],$this->request->post,$userId,$parent_station_section,$filter_warehouse_id_global);

                $url = '';

//                $this->response->redirect($this->url->link('user/warehouse_management', 'token=' . $this->session->data['token'] . '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] . $url, 'SSL'));

            }


        }
        $this->getForm();
    }


    public function getForm(){
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');



        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '仓库货位管理',
            'href' => $this->url->link('user/warehouse_management', 'token=' . $this->session->data['token'].'&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] , 'SSL')
        );
        $this->load->model('station/station');
        $data['stations'] = $this->model_station_station->getStationList();
        $data['stations_section'] = $this->model_user_warehouse_management->getStationsSection();

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $url = '';
        if (!isset($this->request->get['station_section_id'])) {
            $data['action'] = $this->url->link('user/warehouse_management/add', 'token=' . $this->session->data['token'] . '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] . $url, 'SSL');
        } else {
            $data['action'] = $this->url->link('user/warehouse_management/edit', 'token=' . $this->session->data['token'] . '&station_section_id=' . $this->request->get['station_section_id'] . '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] . $url, 'SSL');
        }
        $data['cancel'] = $this->url->link('user/warehouse_management', 'token=' . $this->session->data['token'] . '&filter_warehouse_id_global=' . $this->request->get['filter_warehouse_id_global'] . $url, 'SSL');

        if (isset($this->request->get['station_section_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $productsSectionInfo = $this->model_user_warehouse_management->getProductsSectionInfo($this->request->get['station_section_id']);
        }

        $data['token'] = $this->session->data['token'];

        if (isset($this->request->post['filter_station_id'])) {
            $data['filter_station_id'] = $this->request->post['filter_station_id'];
        } elseif (!empty($productsSectionInfo)) {
            $data['filter_station_id'] = $productsSectionInfo['station_id'];
        } else {
            $data['filter_station_id'] = '';
        }

        if (isset($this->request->post['filter_station_section_id'])) {
            $data['filter_station_section_id'] = $this->request->post['filter_station_section_id'];
        } elseif (!empty($productsSectionInfo)) {
            $data['filter_station_section_id'] = $productsSectionInfo['product_section_type_id'];
        } else {
            $data['filter_station_section_id'] = '';
        }

        if (isset($this->request->post['station_section_title'])) {
            $data['station_section_title'] = $this->request->post['station_section_title'];

        } elseif (!empty($productsSectionInfo)) {
            $data['station_section_title'] = $productsSectionInfo['product_section_title'];

        } else {
            $data['station_section_title'] = '';

        }

        if (isset($this->request->post['title'])) {
            $data['title'] = $this->request->post['title'];
        } elseif (!empty($productsSectionInfo)) {
            $data['title'] = $productsSectionInfo['sectionname'];
        } else {
            $data['title'] = '';
        }
        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($productsSectionInfo)) {
            $data['status'] = $productsSectionInfo['station_id'];
        } else {
            $data['status'] = '';
        }
        if (isset($this->request->post['products'])) {
            $data['products'] = $this->request->post['products'];
        } elseif (!empty($productsSectionInfo)) {
            $data['products'] = $productsSectionInfo['product_id'];
        } else {
            $data['products'] = '';
        }
        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($productsSectionInfo)) {
            $data['sort_order'] = $productsSectionInfo['sort'];
        } else {
            $data['sort_order'] = '';
        }

        if (isset($this->request->post['tray'])) {
            $data['tray'] = $this->request->post['tray'];
        } elseif (!empty($productsSectionInfo)) {
            $data['tray'] = $productsSectionInfo['is_tray'];
        } else {
            $data['tray'] = '';
        }
        if (isset($this->request->post['shelf'])) {
            $data['shelf'] = $this->request->post['shelf'];
        } elseif (!empty($productsSectionInfo)) {
            $data['shelf'] = $productsSectionInfo['is_shelf'];
        } else {
            $data['shelf'] = '';
        }
        if (isset($this->request->post['mobile'])) {
            $data['mobile'] = $this->request->post['mobile'];
        } elseif (!empty($productsSectionInfo)) {
            $data['mobile'] = $productsSectionInfo['is_mobile'];
        } else {
            $data['mobile'] = '';
        }


        $this->response->setOutput($this->load->view('user/warehouse_management_edit.tpl',$data));
    }



    public function updateSort(){
        $this->load->model('user/warehouse_management');
        if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->request->post['type']){

            foreach($this->request->post['postData'] as $m){
                $postData[$m['name']] = $m['value'];

            }
            switch($this->request->post['type']){
                case 'plist':
                    $station_section_id = $this->request->post['id'];
                    $sort = trim($postData['sort']);
                    $modify_by = $this->user->getId();
                    $result = $this->model_user_warehouse_management->updateSort($station_section_id,$sort,$modify_by);

            }

        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);

    }

public function autocomplete(){
        $json =array();
    if (isset($this->request->get['products'])) {
        if (isset($this->request->get['products'])) {
            $products_name = $this->request->get['products'];
        } else {
            $products_name = '';
        }

        $this->load->model('user/warehouse_management');

        $filter_data = array(
            'filter_name' =>$products_name,
        );
        $filter_warehouse_id_global = isset($this->request->post['warehouse_id'])?$this->request->post['warehouse_id']:false;
        $results = $this->model_user_warehouse_management->getProducts($filter_data,$filter_warehouse_id_global);

        foreach ($results as $result) {
            if($result['status'] ==0){
                $json[] = array(
                    'product_id' => $result['product_id'],
                    'status' =>$result['status'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'fix' => $arr=array($result['product_id'],'停用',$result['name'],$result['title'])
                );
            }else{
                $json[] = array(
                    'product_id' => $result['product_id'],
                    'status' =>$result['status'],
                    'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'fix' => $arr=array($result['product_id'],'启用',$result['name'],$result['title'])
                );
            }

        }
    }

    $sort_order = array();

    foreach ($json as $key => $value) {
        $sort_order[$key] = $value['name'];
    }

    array_multisort($sort_order, SORT_ASC, $json);

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));

}

    public function getProductSection(){
        $json =array();
        if (isset($this->request->get['section'])) {
            if (isset($this->request->get['section'])) {
                $section_name = $this->request->get['section'];
            } else {
                $section_name = '';
            }

            $this->load->model('user/warehouse_management');

            $filter_data = array(
                'section' =>$section_name,
            );
            $filter_warehouse_id_global = isset($this->request->post['warehouse_id'])?$this->request->post['warehouse_id']:false;
            $results = $this->model_user_warehouse_management->getProductSection($filter_data,$filter_warehouse_id_global);

            foreach ($results as $result) {
                if($result['status'] ==0){
                    $json[] = array(
                        'product_id' => $result['product_id'],
                        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                        'fix' => $arr=array($result['product_id'],$result['product_section_title'],'停用',$result['name'],$result['title'])
                    );
                }else{
                    $json[] = array(
                        'product_id' => $result['product_id'],
                        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                        'fix' => $arr=array($result['product_id'],$result['product_section_title'],'启用',$result['name'],$result['title'])
                    );
                }

            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }


    public function GetCaseType(){

        $this->load->model('user/warehouse_management');
        $result = $this->model_user_warehouse_management->getStationSectionTitle();

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
