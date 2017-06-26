<?php
class ControllerMarketingSmspinQuery extends Controller
{
    // 实效打开页面
    public function index()
    {
        $this->document->setTitle('注册验证码查询');
        $data['heading_title'] = '注册验证码查询';
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        if(isset($this->session->data['success']) && $this->session->data['success']){
            $data['success_msg'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        if (isset($this->session->data['warning'])) {
            $data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => '注册验证码查询',
            'href' => $this->url->link('marketing/smspin_query', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('marketing/smspin_query.tpl', $data));
    }

    public function getSmsPin(){
        $bd_id = isset($this->request->post['bd_id']) ? $this->request->post['bd_id'] : 0;
        $telephone = isset($this->request->post['telephone']) ? $this->request->post['telephone'] : 0;

        if($bd_id && $telephone){
            $this->load->model('marketing/smspin_query');
            $json = $this->model_marketing_smspin_query->getSmsPin($bd_id, $telephone);
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getBdList(){
        //TODO SHOULD USE BD COMMON-MODULE
        $this->load->model('marketing/area');
        $json = $this->model_marketing_area->getBdList();

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    public function getQueryHistory(){
        $this->load->model('marketing/smspin_query');
        $json = $this->model_marketing_smspin_query->getQueryHistory();

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
}