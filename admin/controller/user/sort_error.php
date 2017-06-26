<?php
class  ControllerUserSortError extends Controller{
    private $error = array();
    public function index() {
        $this->load->language('user/sorterror');

        $this->document->setTitle("分拣错误信息管理");

        $this->load->model('user/sorterror');

        $this->getForm();
    }





    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'user/sort_error')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }



    protected function getForm(){

        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = '';
        }



        $data['column_action'] = $this->language->get('column_action');
        $data['token'] = $this->session->data['token'];
        $data['button_edit'] = $this->language->get('button_edit');
        $data['text_no_result'] = $this->language->get('text_no_result');
        $data['text_no_results'] = $this->language->get('text_no_results');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }



        $data['filter_order_id'] = $filter_order_id;
        $data['button_filter'] = $this->language->get('button_filter');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['heading_title'] = "分拣错误信息管理";


        $data['text_form'] = $this->language->get('text_form');
        $data['button_save'] = $this->language->get('button_save');
        $data['entry_comment'] = $this->language->get('entry_comment');
        $data['entry_sorterrortype'] = $this->language->get('entry_sorterrortype');
        $data['button_sorterror_add'] = $this->language->get('button_sorterror_add');
        $data['text_sorterrortype'] = $this->language->get('text_sorterrortype');
        $data['token'] = $this->session->data['token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $url = '';

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => "分拣错误信息管理",
            'href' => $this->url->link('user/sort_error', 'token=' . $this->session->data['token'] . $url, 'SSL')
        );

        $data['action'] = $this->url->link('user/sort_error/add', 'token=' . $this->session->data['token'] . $url, 'SSL');



        if(!empty($filter_order_id)){

            $sorterror      = $this->model_user_sorterror->getSorterror($filter_order_id);
            $sorterror_info = $this->model_user_sorterror->getSorterrors($filter_order_id );

            $data['sorterror'] = array();
            foreach($sorterror as $Serror){
                $data['sorterror'][] = array(
                    'order_id' => $Serror['order_id'],
                    'inventory_name' =>$Serror['inventory_name'],
                );
            }

            $data['sorterrors'] = array();
            foreach($sorterror_info as $sorterror_in) {
                $data['sorterrors'][] = array(
                    'order_id' => $sorterror_in['order_id'],
                    'comment' =>$sorterror_in['comment'],
                    'sorterror_type' => $sorterror_in['sorterror_type'],
                    'date_added' =>$sorterror_in['date_added'],
                    'name' =>$sorterror_in['name'],
                );
            }
        }else{
            $data['sorterrors'] = array();
            $data['sorterror'] = array();
        }


        $sorterror_type = $this->model_user_sorterror->getSorterrorType();
        $data['sorterror_type'] = $sorterror_type;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('user/sorterror_form.tpl',$data));
    }


   public  function addsorterrors(){

       $this->load->model('user/sorterror');
       $filter_order_id =$this->request->post['filter_order_id'];
       $sorterroroptions= $this->request->post['check_value'];

       if(empty($filter_order_id) ||empty($sorterroroptions)){
           return false;
       }else{
           $flag = true;
           $sorterror_options = implode(',', $sorterroroptions);
           $comment = $this->request->post['comments'];
           $rowdata = array(
             'order_id' => $filter_order_id,
               'sorterror_type' =>$sorterror_options,
               'comment' =>$comment
           );

           $sorterrordistr = $this->model_user_sorterror->addSorterrors('oc_x_order_distr_sorterror', $rowdata);
           if(empty($sorterrordistr)) {
               $flag = false;
           }
       }
       if($flag){
           echo $flag = true;
       }

   }
}