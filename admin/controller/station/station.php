<?php
/**
 * Created by PhpStorm.
 * User: liuyibao
 * Date: 15-8-27
 * Time: 下午7:47
 */
class ControllerStationStation extends Controller {
    public function index(){
        $this->document->setTitle('站点管理');
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '站点管理',
            'href' => $this->url->link('station/station', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['add'] = $this->url->link('station/station/add', 'token=' . $this->session->data['token'], 'SSL');
        $data['edit'] = $this->url->link('station/station/edit', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->load->model('station/station');
        $station_total = $this->model_station_station->getTotalStations();
        $condition = array(
            'start' => ($page - 1) * 30,
            'limit' => 30
        );
        $data['stations'] = $this->model_station_station->getStations($condition);

        $pagination = new Pagination();
        $pagination->total = $station_total;
        $pagination->page = $page;
        $pagination->limit = 30;
        $pagination->url = $this->url->link('catalog/product', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');
        $data['pagination'] = $pagination->render();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('station/station_list.tpl', $data));
    }

    public function edit(){
        $this->document->setTitle('站点编辑');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_product->editStation($this->request->get['station_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_model'])) {
                $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_price'])) {
                $url .= '&filter_price=' . $this->request->get['filter_price'];
            }

            if (isset($this->request->get['filter_quantity'])) {
                $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('catalog/product', 'token=' . $this->session->data['token'] . $url, 'SSL'));
        }

        $this->getForm();
    }
}