<?php 

class ControllerSaleOrderMap extends Controller {

	public function index(){
		$this->document->setTitle("订单分布图");
		$this->getList();
	}
	
	public function getList(){
		
		$this->data['deliver_date'] = isset($this->request->get['deliver_date'])?$this->request->get['deliver_date']:date('Y-m-d',(time()+8*60*60));
		$this->data['time_slot'] = isset($this->request->get['time_slot'])?$this->request->get['time_slot']:0;
		$this->data['region'] = isset($this->request->get['region'])?$this->request->get['region']:0;
		
		$this->data['van_id'] = isset($this->request->get['van_id'])?$this->request->get['van_id']:0;
		$this->data['type'] = isset($this->request->get['type'])?$this->request->get['type']:0;
		$this->data['order_id'] = isset($this->request->get['order_id'])?$this->request->get['order_id']:0;
		$this->data['task'] = isset($this->request->get['task'])?$this->request->get['task']:0;
		
		$this->data['print'] = isset($this->request->get['print'])?$this->request->get['print']:0;
		
		$this->data['time_slots'] = $this->model_logistic_logistic->get_timeslot2();
		$this->data['regions'] = $this->model_logistic_logistic->get_region();
		
         $this->data['token'] = $this->session->data['token'];



        if($this->data['time_slot'] == '10:00 - 17:00'){
			$this->data['region'] = 1;
		} 

		if($this->data['region'] == 1){
		   $this->data['time_slot'] = '10:00 - 17:00';
		}


		$results = $this->model_logistic_logistic->get_van_info();
		if($results){

			foreach ($results as $result) {
				$this->data['vans'][] = array(
					'logistic_van_id' => $result["logistic_van_id"],
					'code'  => $result["code"],
				);
			}
		}
		
		$data = array(
				'deliver_date'  => $this->data['deliver_date'],
				'region' =>  $this->data['region'],
				'time_slot' => $this->data['time_slot']
		);
		
		//Get all allocated orders and allocated for assigned van, to exclude and get not allocated order in next step
		$order_list = false;
		$allocated_order_list = $this->model_logistic_logistic->get_van_allocated_orders($data['deliver_date'],$data['time_slot'],'',true); //Get orders only
		$order_list = $this->model_logistic_logistic->get_van_allocated_orders($data['deliver_date'],$data['time_slot'],"",false); //Get orders only
		//$results = $this->model_logistic_logistic->get_order_user_info(implode(',',$allocated_order_list)); //Use the sort by elements
		$vanOrdersArr = "";
		if($order_list){
			$i = 1;
			foreach ($order_list as $result) {
				$vanOrdersArr[strtolower($result['code'])]["order_list"] = unserialize($result['order_list']);
				$vanOrdersArr[strtolower($result['code'])]["logistic_van_id"] =  $result['logistic_van_id'];
				$i++;
			}
		}
		else{
			//$this->data['allocated_orders'] = false;
		}
        
        $allocated_order_list = implode(",",$allocated_order_list);
        $this->data['allocated_order_list'] = $allocated_order_list;
		unset($results);
		$order_list = false;

		$this_city = implode(',',$this->data['regions'][$data['region']]['city_ids']);
        $this_area = implode(',',$this->data['regions'][$data['region']]['area_ids']);
		$van_orders = $this->model_logistic_logistic->get_van_orders($data['deliver_date'], $this_city, $this_area);
		if($this->data['time_slot']){
			$order_list = $van_orders[$this->data['time_slot']]['order_list'];
		}
		else{
			foreach($van_orders AS $van_order){
				 if($order_list){
					$order_list .= ','. $van_order['order_list'];
				} else {
					$order_list = $van_order['order_list'];
				} 
			}
		}

		$payment_info = $this->model_logistic_logistic->get_order_payment_info($order_list);
		$order_list = explode(',',$order_list);
		//$order_list = array_diff($order_list,$allocated_order_list);//exclude
		$this->data['not_allocated_list'] = implode(",",array_diff($order_list,$allocated_order_list));;
		$results = $this->model_logistic_logistic->get_order_user_info(implode(',',$order_list),$sortby);
		  $this->data['shelf_name'] = array(
            'normal' => '常',
            'iced' => '冻',
            'cold' => '冷',
            'warm' => '温'
        );
		
		if($results){
			$i = 1;
			foreach ($results as $result) {
			    if(trim($result['SHIPPING_ADDRESS_CN']) == '' ){
					$address =  $result['CITY_CN']."市".$result['AREA_CN'].trim($result['SHIPPING_ADDRESS']);
				} else {
					 if($result['CITY_CN'] != "上海"){
						$address = $result['CITY_CN']."市".$result['SHIPPING_ADDRESS_CN'];
					 } else {
					    $address = $result['CITY_CN']."市".$result['AREA_CN'].$result['SHIPPING_ADDRESS_CN'];
					 }
					$add_tmp = explode("号",$address);
					if(count($add_tmp)>1){
						$address = $add_tmp[0]."号";
					} else {
					    if($result['CITY_CN'] != "上海"){
							$address_tmp = explode("道",$address);
							$address = $address_tmp[0]."道";
						}else {
						   $address = $result['CITY_CN']."市" .$result['AREA_CN'] .$result['SHIPPING_ADDRESS_CN'];
						}
					}
				}

				$van_id="";
                if($vanOrdersArr){

					foreach($vanOrdersArr as $k => $v){
						if(in_array($result['ORDER_ID'],$vanOrdersArr[$k]["order_list"])){
							$van_id=$k;
                            $van_val= $vanOrdersArr[$k]["logistic_van_id"];
						}
					}
				}
                $this_shelf_info =  unserialize($result['SHELF_INFO']);
				 $this_shelf_info = !empty($this_shelf_info['shelf_num']) ? $this_shelf_info['shelf_num'] : $this_shelf_info;
				  foreach($shelf_name as $key=>$val){
					   $this_shelf_info = str_replace($key,$val,$this_shelf_info);
				  }

				  if($result['SPEC_DELIVER_TIME']){
				     $showTime=$result['DELIVER_TIME']."(".$result['SPEC_DELIVER_TIME'].")";
				  } else {
					 $showTime=$result['DELIVER_TIME'];
				  }


               $region_id  = '';
               if($result["ZONE_ID"] != '' ){
                  if($result["ZONE_ID"] == '1'){
				     foreach ($this->data['regions'] as $region){
						if(in_array($result["AREA_ID"],$region["area_ids"])){
						     $region_id  = $region["logistic_region_id"];
						}
					 }
				  } else {
				     $region_id  =1;
				  }
			   } else {
			       $region_id  = 5;
			   }

            
				
                
				$this->data['not_allocated_orders'][] = array(
						'ORDER_ID'  => $result['ORDER_ID'],
						'DELIVER_TIME'       => $showTime,
						'SPEC_DELIVER_TIME'       => $result['SPEC_DELIVER_TIME'],
						'SHIPPING_NAME'       => $result['SHIPPING_NAME'],
						'SHIPPING_ADDRESS'       => trim($result['SHIPPING_ADDRESS']),
						'SHIPPING_ADDRESS_CN'       => trim($result['SHIPPING_ADDRESS_CN']),
						'CITY'       => $result['CITY_CN'],
						'AREA'       => $result['AREA_CN'],
						'SHELF_INFO' => $this_shelf_info,
					    'ADDRESS' => $address,
					    'van_id' => $van_id,
					    'region_id' =>$region_id,
					    'van_val' => $van_val,
					    'order_status' => $payment_info[$result['ORDER_ID']]['ORDER_STATUS'],
					    'point' => '',
				);	
				
				$this->data['not_allocated_orders2'][$result['ORDER_ID']] = array(
						'ORDER_ID'  => $result['ORDER_ID'],
						'DELIVER_TIME'       => $showTime,
						'SPEC_DELIVER_TIME'       => $result['SPEC_DELIVER_TIME'],
						'SHIPPING_NAME'       => $result['SHIPPING_NAME'],
						'SHIPPING_ADDRESS'       => trim($result['SHIPPING_ADDRESS']),
						'SHIPPING_ADDRESS_CN'       => trim($result['SHIPPING_ADDRESS_CN']),
						'CITY'       => $result['CITY_CN'],
						'AREA'       => $result['AREA_CN'],
						'SHELF_INFO' => $this_shelf_info,
					    'ADDRESS' => $address,
					    'van_id' => $van_id,
						'van_val' => $van_val,
					     'region_id' =>$region_id,
					    'order_status' => $payment_info[$result['ORDER_ID']]['ORDER_STATUS'],
					    'point' => '',
				);		
				$i++;
			}
		}
		else{
			$this->data['not_allocated_orders'] = false;
		}

		//var_dump($this->data['not_allocated_orders'] );
		unset($results);
       
		//////eric 9/10/2013 update time slot//////

	 
        //////eric 9/10/2013 update time slot//////
        $this->data['button_query'] = $this->language->get('button_query');
        $this->data['button_van_order_in'] = $this->language->get('button_van_order_in');
        $this->data['button_van_order_out'] = $this->language->get('button_van_order_out');
        $this->data['button_van_view'] = $this->language->get('button_van_view');

        $this->data['button_select_all'] = $this->language->get('button_select_all');
        $this->data['button_select_none'] = $this->language->get('button_select_none');
        $this->data['button_select_anti'] = $this->language->get('button_select_anti');
        $this->data['button_print'] = $this->language->get('button_print');
        
		 //form
        $this->data['form_title'] = $this->language->get('form_title');
        $this->data['form_wave'] = $this->language->get('form_wave');
        $this->data['form_region'] = $this->language->get('form_region');
        $this->data['form_status'] = $this->language->get('form_status');
        $this->data['form_button_query'] = $this->language->get('form_button_query');
        $this->data['form_button_print_selected'] = $this->language->get('form_button_print_selected');


		$this->data['heading_title'] = $this->language->get('heading_title_van_allot');
		$this->template = 'logistic/map.tpl';
		$this->children = array(
				'common/header',
				'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}

	public function add2cars()
	{
		
		
		$order_id = isset($this->request->get['order_id'])?$this->request->get['order_id']:0;
		$van_id = isset($this->request->get['van_id'])?$this->request->get['van_id']:0;
		$old_van = isset($this->request->get['old_van'])?$this->request->get['old_van']:0;
		 $this->data['deliver_date'] = isset($this->request->get['deliver_date'])?$this->request->get['deliver_date']:date('Y-m-d',(time()+8*60*60));
        	$this->data['time_slot'] = isset($this->request->get['time_slot'])?$this->request->get['time_slot']:0;
        	$this->data['region'] = isset($this->request->get['region'])?$this->request->get['region']:0;
               
		$this->load->model('logistic/logistic');
		if($order_id == ""){
		    echo "error";
			return false;
		} 
      
	    	if($van_id){
			$task="push";
		} else {
			$task="pop";
		}

	   if($old_van){
			 $allocate_data = array(
               			 'task' => "pop",
               			 'deliver_date' => $this->data['deliver_date'],
               			 'order_id' => $order_id,
                		'time_slot' => $this->data['time_slot'],
				 'region' => $this->data['region'],
                		'van_id' =>$old_van
           		 );
	   
			$this->model_logistic_logistic->allocate_order($allocate_data);
	   }
       
       if($van_id){
		   $allocate_data = array(
					'task' => $task,
					'deliver_date' => $this->data['deliver_date'],
					'order_id' => $order_id,
					'time_slot' => $this->data['time_slot'],
			        	 'region' => $this->data['region'],
					'van_id' =>$van_id
				);		
		  $this->model_logistic_logistic->clear_allocate_order($allocate_data);
	   }
	  

	   
      		echo   $this->model_logistic_logistic->allocate_order($allocate_data);
	}


	public function addCars(){
	     $order_id = isset($this->request->get['order_id'])?$this->request->get['order_id']:0;
		$van_id = isset($this->request->get['van_id'])?$this->request->get['van_id']:0;
		$old_van = isset($this->request->get['old_van'])?$this->request->get['old_van']:0;
		 $this->data['deliver_date'] = isset($this->request->get['deliver_date'])?$this->request->get['deliver_date']:date('Y-m-d',(time()+8*60*60));
        	$this->data['time_slot'] = isset($this->request->get['time_slot'])?$this->request->get['time_slot']:0;
        	$this->data['region'] = isset($this->request->get['region'])?$this->request->get['region']:0;
		 $this->data['token'] = $this->session->data['token'];
		$this->load->model('logistic/logistic');
		  $allocate_data = array(
                	 'task' => "pop",
                	'deliver_date' => $this->data['deliver_date'],
               		 'order_id' => $order_id,
                	'time_slot' => $this->data['time_slot'],
			'region' => $this->data['region'],
                	'van_id' =>$van_id
             );


            $this->model_logistic_logistic->allocate_order($allocate_data);

			  $allocate_data = array(
                 		  'task' => "push",
               			 'deliver_date' => $this->data['deliver_date'],
               			 'order_id' => $order_id,
                		'time_slot' => $this->data['time_slot'],
				'region' => $this->data['region'],
                		'van_id' =>$van_id
           		 );
           		 
           		  $this->model_logistic_logistic->clear_allocate_order($allocate_data);

           		$this->model_logistic_logistic->allocate_order($allocate_data);

			$this->index();
	}

}
?>