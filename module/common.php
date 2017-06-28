<?php

require_once(DIR_SYSTEM.'db.php');

class COMMON{
    function getStation($id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT * FROM oc_x_station WHERE status=1";

        if($id>0){
            $sql = "SELECT * FROM oc_x_station WHERE station_id = {$id} AND status=1";
        }
        $query = $db->query($sql);
        $results = $query->rows;

        if(sizeof($results)){
            return $results;
        }
        else{
            return array();
        }

//        if($results && sizeof($results)){
//            if($id>0){
//                $sql = "SELECT * FROM oc_x_area WHERE station_id = {$id} AND status=1";
//                $query = $db->query($sql);
//                $areas = $query->rows;
//                if($areas && sizeof($areas)){
//                    $results[0]['areas'] = $areas;
//                }
//            }
//
//            return $results;
//        }
    }

    function getAreaList(array $data){
        global $db;

        $return_code = 'FAIL';
        $return_data = array(
            'area_list'=>array(),
            'district_list'=>array()
        );

        $query = $db->query("SELECT area_id, city, district, name FROM oc_x_area WHERE status=1 order by district");
        if($query->num_rows){
            $return_code = 'SUCCESS';
            $return_data['area_list'] = $query->rows;

            $query = $db->query("SELECT city, district, group_concat(area_id) area_id_list FROM oc_x_area WHERE status=1 group by district");
            $return_data['district_list'] = $query->rows;
        }

        return array(
            'return_code' => $return_code,
            'return_msg' => '',
            'return_data' => $return_data
        );
    }


    function checkFirstOrder(array $data){
        global $db;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;

        $sql = "select count(*) orders from oc_order
        where order_status_id not in (3) and customer_id = '".$customer_id."'
        and station_id = '".$station_id."' and type = 1
        ";
        $query = $db->query($sql);
        if($query->row){
            $orderInfo = $query->row;

            return $orderInfo['orders'];
        }

        return false;
    }
    
    function getOrderss($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;
        
        $data = json_decode($data, 2);

        $product_id = isset($data['product_id']) ? $data['product_id'] : false;
        if(!$product_id){
            return false;
        }
        $deliver_date = isset($data['deliver_date']) ? $data['deliver_date'] : false;
        if(!$deliver_date){
            return false;
        }
        $order_status_id = isset($data['order_status_id']) ? $data['order_status_id'] : false;
        if(!$order_status_id){
            return false;
        }

        $area_id_list = isset($data['area_id_list']) ? $data['area_id_list'] : false;

        $sql = "
            SELECT
              oc_order.order_id,
              oc_order.station_id,
              sum(oc_order_product.weight_inv_flag) as s_weight_inv_flag,
              sum(oc_order_product.quantity) as quantity,
              occ.customer_group_id as group_id,
              occ.shortname as group_shortname,
              oc_order.date_added,
              oc_order.shipping_address_1,
              oc_order.is_nopricetag,
              c.is_agent,
              a.name area_name,
              a.city,
              a.district
            from oc_order
            left join oc_order_product  on oc_order.order_id = oc_order_product.order_id
            left join oc_customer_group as occ on oc_order.customer_group_id = occ.customer_group_id
            left join oc_customer c on oc_order.customer_id = c.customer_id
            left join oc_x_area a on c.area_id = a.area_id
            left join oc_product as p on p.product_id = oc_order_product.product_id
            left join oc_product_to_category as ptc on ptc.product_id = p.product_id
        ";

        if($product_id ==5001){
        $sql .= " WHERE oc_order.station_id = 1 and p.product_type_id = 2";
    	}
        elseif($product_id ==5002){
            $sql .= " WHERE oc_order.station_id = 1 and p.product_type_id = 3";
    	}
        elseif($product_id ==5003){
            $sql .= " WHERE oc_order.station_id = 1 and p.product_type_id = 11";
    	}
        //elseif($product_id ==5003){
        //  $sql .= " WHERE ptc.category_id in (72,74,157) and oc_order.station_id = 1 ";
    	//}
        elseif($product_id ==5004){
            $sql .= " WHERE oc_order.station_id = 2";
    	}
        //elseif($product_id ==5005){
        //    $sql .= " WHERE oc_order.station_id = 2 and p.product_type = 4";
    	//}
        //elseif($product_id ==5006){
        //    $sql .= " WHERE oc_order.station_id = 2 and p.product_type = 5";
    	//}
        else{
    	    $sql .= " WHERE oc_order.station_id = 1 and p.product_type_id = 1";
        }

        if($area_id_list){
            $sql .= " AND c.area_id in (".$area_id_list.")";
        }
    	
    	$sql .= " AND oc_order.order_status_id = " . $data['order_status_id'];
    	$sql .= " AND oc_order.deliver_date  = '" . $data['deliver_date'] . "'";
    	$sql .=" group by oc_order.order_id";
    	$sql .=" order by a.city,a.district,a.name,oc_order.shipping_address_1";
    	/*
    	 * 数据插入到新表temp_order
    	*	$sql2=" INSERT INTO temp_order(order_id,quantity) SELECT * FROM (";
    	*	$sql2.=$sql;
    	*	$sql2.=") AS tb";
    	*	$query2=$db->query($sql2);
    	*/

    	$query = $db->query($sql);
    	
        $results = $query->rows;
        
        $all_orders = array();
        foreach($results as $key=>$value){
                $value['date_added'] = date("H:i:s",  strtotime($value['date_added']));
        	$all_orders[$value['order_id']] = $value;
        }
        
    /*	
    	$return = array();
        $return['data'] = array();
       // var_dump ($results);
       
    	if(sizeof($results)){
    		 
            foreach($results as $k=>$v){
            
             $return['data'][$v['order_id']] = $v;
             $return['data'][$v['order_id']]['quantity'] = $v['quantity'];
             $return['data'][$v['order_id']]['tb'] = $query2;
            }
    	}
    */	
                $distr = array();
    		if($product_id==5001){
    			
    		$sql = "select * from oc_order_distr where ordclass = 2";	
    		}
                elseif($product_id==5002){
    			
    		$sql = "select * from oc_order_distr where ordclass = 3";	
    		}
                elseif($product_id==5003){
    			
    		$sql = "select * from oc_order_distr where ordclass = 4";	
    		}
                elseif($product_id==5004){
    			
    		$sql = "select * from oc_order_distr where ordclass = 5";	
    		}
                elseif($product_id==5005){
    			
    		$sql = "select * from oc_order_distr where ordclass = 6";	
    		}
                elseif($product_id==5006){
    			
    		$sql = "select * from oc_order_distr where ordclass = 7";	
    		}
                else{
    		$sql = "select * from oc_order_distr where ordclass = 1";
    		 }
    	   
    	    $distr= $db->query($sql);
    		$distr = $distr->rows;
    		
    		foreach($distr as $d_key=>$d_value){
    			unset($all_orders[$d_value['order_id']]);
    		}
    		$all_orders=array_values($all_orders);
    		
                
        if(sizeof($all_orders)){
            return $all_orders;
        }
        else{
        	
            return array();
        }
    }

    function orderdistr($data, $station_id=1, $language_id=2, $origin_id=1, $key){
    	global $db;
        global $log;
        global $orders_id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;
        
        $data = json_decode($data, 2);
      
        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
   		//return $order_id;
        if(!is_array($order_id)){
            return false;
        }
       
    	$inventory_name = isset($data['inventory_name']) ? $data['inventory_name'] : false;
        if(!$inventory_name){
            return false;
        }
        
        $product_id = isset($data['product_id']) ? $data['product_id'] : false;
        if(!$product_id){
        	return false;
        }
        
        else if ($product_id==5001){
        	$ordclass = 2 ;
        	
        }
        else if ($product_id==5002){
        	$ordclass = 3 ;
        	
        }
        else if ($product_id==5003){
        	$ordclass = 4 ;
        	
        }
        else if ($product_id==5004){
        	$ordclass = 5 ;
        	
        }
        else if ($product_id==5005){
        	$ordclass = 6 ;
        	
        }
        else if ($product_id==5006){
        	$ordclass = 7 ;
        	
        }
        else{
        	$ordclass = 1 ;
         }
        	
        	
        if( is_array($order_id)){
        	
        	foreach($order_id as $value){
        		$ayy = explode('@',$value);
        		$orders_id = $ayy[0];
        		$quantity = $ayy[1];
        		
        		$sql="INSERT INTO oc_order_distr (order_id, inventory_name, ordclass,quantity)VALUES ('$orders_id','$inventory_name','$ordclass','$quantity')";
        
        		$query = $db->query($sql);
        	}
        }	
      return $query;
        
    }
    
     function getStatus($data, $station_id=1, $language_id=2, $origin_id=1, $key){
     		return true;
     	global $db;
        global $log;
       
        
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;
        
        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;
        if(!$date){
            return false;
        }
        $order_id = isset($data['orders_id']) ? $data['orders_id'] : false;
        if(!$order_id){
            return false;
        }
     	$sql =" select order_status_id from oc_order where ";
     	$sql .= " order_id = " . $data['orders_id'];
     	
     	$query = $db->query($sql);
     	$results = $query->row;
     	
     	//$results = String($results);
     
     	if(sizeof($results)){
        	return $results;
        }else{
        	
        	return array();
        }
     }
    
     function ordered($data, $station_id=1, $language_id=2, $origin_id=1, $key){
    	
        global $db;
        global $log;
        
        
        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;
       
        $order_status_id = isset($data['order_status_id']) ? $data['order_status_id'] : false;
        if(!$order_status_id){
            return false;
        }
         $product_id = isset($data['product_id']) ? $data['product_id'] : false;
        if(!$product_id){
            return false;
        }
        $deliver_date = isset($data['deliver_date']) ? $data['deliver_date'] : false;
        if(!$deliver_date){
            return false;
        }
        if ($product_id==5001){
        
        	$ordclass = 2 ;
        }elseif($product_id == 5002){
            $ordclass = 3;
        }
        elseif($product_id == 5003){
            $ordclass = 4;
        }
        elseif($product_id == 5004){
            $ordclass = 5;
        }
        elseif($product_id == 5005){
            $ordclass = 6;
        }
        elseif($product_id == 5006){
            $ordclass = 7;
        }
        else{
        	$ordclass = 1 ;
         }
        	
        $sql = "select GROUP_CONCAT(o.order_status_id,o.is_nopricetag,td.order_id,td.quantity,os.name) as groups,td.inventory_name,sum(td.quantity) as total, td.ordclass,o.deliver_date from oc_order_distr as td left join oc_order as o on o.order_id = td.order_id left join oc_order_status as os on os.order_status_id =o.order_status_id where";
        //$sql .= " o.order_status_id = " . $data['order_status_id'];
        $sql .="  o.deliver_date = '" .$data['deliver_date'] . "'";
        $sql .= " AND td.ordclass =  ".$ordclass;
        $sql .=" group by td.inventory_name";
       
        $query = $db->query($sql);
        $results = $query->rows;
        
        
       /*
        * 查找订单状态
        * @param order_ststus_id
       
        foreach($results as $k=>$v){
        	$order_id = $v['order_id'];
        	$sql = "select order_status_id from oc_order where order_id = $order_id";
        	$query = $db->query($sql);
        	$r = $query->row;
        	$results[$k]['order_status_id'] = $r['order_status_id'];
        }
        */
        if(sizeof($results)){
        	return $results;
        }else{
        	
        	return array();
        }
       
 
    }
     
      function orderRedistr($data, $station_id=1, $language_id=2, $origin_id=1, $key){
    	
        global $db;
        global $log;
        
         
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;
        
        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;
        if(!$date){
            return false;
        }
        $order_id = isset($data['order_id']) ? $data['order_id'] : false;
        if(!$order_id){
            return false;
        }
        $ordclass = isset($data['ordclass']) ? $data['ordclass'] : false;
        if(!$ordclass){
        	
            return false;
        }
        
        $sql = "delete from oc_order_distr where order_id = $order_id and ordclass = $ordclass;";
        $query = $db->query($sql);
        
        return $query;
    }
     
     
    
    function getInventoryUserOrder($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;
        
        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;
        if(!$date){
            return false;
        }
        
        $sql = "SELECT
                    od.id, od.order_id, od.w_user_id, od.inventory_name, od.ordclass, od.quantity
                FROM oc_order_distr AS od
                LEFT JOIN oc_order AS o ON od.order_id = o.order_id
                WHERE
                    o.deliver_date = '" . $date . "'
                and od.inventory_name = '" . $data['inventory_user'] . "' ";
        $query = $db->query($sql);
        $results = $query->rows;
        
        $return = array();
        $return['data'] = array();
        
        $return['data'] = $results;
        
        if(sizeof($return)){
            return $return;
        }
        else{
            return array();
        }
        
    }
    
    
    
    
    function getOrders($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;
        
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;
        
        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;

        //New 20170416
        $station_id = isset($data['station_id']) ? $data['station_id'] : 0;
        $inventory_user = isset($data['inventory_user']) ? $data['inventory_user'] : false;
        //$orderList = isset($data['orderList']) && sizeof($data['orderList']) ? $data['orderList'] : array(0);

        if(!$date){
            return false;
        }
            

        $sql = "SELECT
          o.order_id,
          o.customer_id,
          o.station_id,
          o.deliver_date,
          o.date_added,
          GROUP_CONCAT(op.product_id) as product_id_str,
          os.`name`,
          SUM(op.quantity) as quantity,
          o.order_status_id,
          o.shipping_name,
          o.shipping_phone,
          o.shipping_address_1,
          o.customer_group_id,
          o.is_nopricetag,
          oi.frame_count,
          oi.inv_comment,
          oi.incubator_count,
          oi.foam_count,
          oi.frame_mi_count,
          incubator_mi_count,
          oi.frame_ice_count,
          oi.box_count,
          oi.foam_ice_count,
          oi.frame_meat_count,
          oi.frame_vg_list,
          oi.frame_meat_list,
          oi.frame_mi_list,
          oi.frame_ice_list,
          ptc.category_id,
          ocg.customer_group_id as group_id,
          ocg.shortname as group_shortname,
          c.is_agent,
          a.name area_name,
          a.city,
          a.district
        FROM oc_order AS o
        LEFT JOIN oc_order_product AS op ON o.order_id = op.order_id
        left join oc_customer_group as ocg on o.customer_group_id = ocg.customer_group_id
        LEFT JOIN oc_order_status AS os ON os.order_status_id = o.order_status_id
        left join oc_customer c on o.customer_id = c.customer_id
        left join oc_x_area a on c.area_id = a.area_id
        ";


        if($station_id == 2){
            $sql .= " left join oc_order_distr od on o.order_id = od.order_id";
        }

        $sql .= "
        left join oc_product_to_category as ptc on ptc.product_id = op.product_id
        LEFT JOIN oc_order_inv  as oi on o.order_id = oi.order_id and oi.inv_status = 1
        WHERE o.station_id = '".$station_id."'";
	    $sql .=" AND o.deliver_date = '" . $date . "'";
        if($data['order_status_id'] != 0 ){
            $sql .= " AND o.order_status_id = " . $data['order_status_id'];
        }

        if($inventory_user && $station_id == 2){
            $sql .= " AND od.inventory_name = '".$inventory_user."'";
        }

        $sql .= " GROUP BY op.order_id order by o.station_id asc,o.order_id asc";
        
        
        $query = $db->query($sql);
        $results = $query->rows;
        
        $return = array();
        $return['data'] = array();
        
        $queryOrderList = array(0);
        if(sizeof($results)){
            foreach($results as $k=>$v){
                
                $order_product_id_arr = array();
                $order_has_vg = false;
                $order_has_mi = false;
                $order_product_id_arr = explode(",", $v['product_id_str']);
                
                $v['inv_type_1'] = 0;
                $v['inv_type_2'] = 0;
                $v['inv_type_3'] = 0;
                $v['inv_type_4'] = 0;
                $v['inv_type_5'] = 0;
                $v['inv_type_6'] = 0;
                $v['inv_type_7'] = 0;
                
//                foreach($order_product_id_arr as $key=>$value){
//                    if($value > 1000 && $value < 5000){
//                        $order_has_vg = true;
//
//                    }
//
//                    if($value > 5000){
//                        $order_has_mi = true;
//                    }
//                }
//
//                if($order_has_vg && !$order_has_mi){
//                    $v['order_product_type'] = 1;
//                }
//                if($order_has_vg && $order_has_mi){
//                    $v['order_product_type'] = 2;
//                }
//                if(!$order_has_vg && $order_has_mi){
//                    $v['order_product_type'] = 3;
//                }
                
                $return['data'][$v['station_id'] . $v['order_id']] = $v;
                $return['data'][$v['station_id'] . $v['order_id']]['plan_quantity'] = $v['quantity'];
                $return['data'][$v['station_id'] . $v['order_id']]['added_by'] = '';
                $return['data'][$v['station_id'] . $v['order_id']]['station_id'] = $v['station_id'];

                //20170421, 取消order_product_type 和 bao 的计算和设定
                $return['data'][$v['station_id'] . $v['order_id']]['order_product_type'] = 0;
                $return['data'][$v['station_id'] . $v['order_id']]['bao'] = 0;
            }
        }
         
        
        //$last_one_day = date("Y-m-d", time() + 8*3600 - 24*3600);
        //获取入库中间表中已入库的商品，并从计划入库的商品中减去已入库的商品     
        
        $sql = "SELECT o.station_id, xis.order_id, xis.product_id, xis.quantity, xis.uptime, xis.move_flag, xis.added_by, xis.product_barcode, p.storage_mode_id,ptc.category_id,o.station_id,p.product_type,p.product_type_id
        FROM oc_x_inventory_order_sorting AS xis
        left join oc_order as o on o.order_id = xis.order_id
        left join oc_product as p on p.product_id = xis.product_id
        left join oc_product_to_category as ptc on ptc.product_id = p.product_id
        where o.deliver_date = '" . $date . "' and o.station_id = '".$station_id."'";
    
        $query = $db->query($sql);
        $result = $query->rows;
        
        
        
        
        if(sizeof($result)){
            foreach($result as $rk => $rv){
                
                $return_move_p = array();
                if($return['data'][$rv['station_id'] . $rv['order_id']]['quantity'] > 0){
                    $return['data'][$rv['station_id'] . $rv['order_id']]['quantity'] -= $rv['quantity'];
                    if($return['data'][$rv['station_id'] . $rv['order_id']]['quantity'] <= 0){
                        $return_move_p = $return['data'][$rv['station_id'] . $rv['order_id']];
                        unset($return['data'][$rv['station_id'] . $rv['order_id']]);
                        $return['data'][$rv['station_id'] . $rv['order_id']] = $return_move_p;
                    }
                    $return['data'][$rv['station_id'] . $rv['order_id']]['added_by'] = $rv['added_by'];
                
                    if($rv['station_id'] == 1 && $rv['product_type_id'] == 1){
                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_1'] += $rv['quantity'];
                    }
                    if($rv['station_id'] == 1 && $rv['product_type_id'] == 2){
                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_2'] += $rv['quantity'];
                    }
                    if($rv['station_id'] == 1 && $rv['product_type_id'] == 3){
                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_3'] += $rv['quantity'];
                    }
//                    if(in_array($rv['category_id'], array(72,74,157))){
//                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_4'] += $rv['quantity'];
//                    }
//                    if(  $rv['product_type'] == 4){
//                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_6'] += $rv['quantity'];
//                    }
                    if( $rv['station_id'] == 2 ){
                        $return['data'][$rv['station_id'] . $rv['order_id']]['inv_type_5'] += $rv['quantity'];
                    }
                }
            }
        }
        //echo "<pre>";print_r($return);exit;
        
        
        
        if(sizeof($return)){
            
//            $bao_product_arr = array(1047,1720);
//            $bao_user_arr = array(834,810,769,850,752,973,808,754,1026,815,1121,1505);
//            $bao_week_arr = array(5,6,0);
//
//
//            foreach($return['data'] as $key=>$value){
//
//                $week_order_deliver = date("w",  strtotime($value['date_added']));
//
//                $array_jiaoji = array();
//                $return['data'][$key]['is_bao'] = 0;
//                $product_id_arr = explode(",", $value['product_id_str']);
//                $array_jiaoji = array_intersect($product_id_arr, $bao_product_arr);
//                if(in_array($value['customer_id'], $bao_user_arr)&&!empty($array_jiaoji)&&  in_array($week_order_deliver, $bao_week_arr)){
//                    //$return['data'][$key]['is_bao'] = 1;
//                }
//
//            }
            
            return $return;
        }
        else{
            return array();
        }

//        if($results && sizeof($results)){
//            if($id>0){
//                $sql = "SELECT * FROM oc_x_area WHERE station_id = {$id} AND status=1";
//                $query = $db->query($sql);
//                $areas = $query->rows;
//                if($areas && sizeof($areas)){
//                    $results[0]['areas'] = $areas;
//                }
//            }
//
//            return $results;
//        }
    }
    
    
    function getPurchaseOrders($data, $station_id=1, $language_id=2, $origin_id=1, $key){
        global $db;
        global $log;
        
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;
        
        $data = json_decode($data, 2);
        $date = isset($data['date']) ? $data['date'] : false;
        if(!$date){
            //return false;
        }
            

        $sql = "SELECT
	o.purchase_order_id as order_id,
        o.station_id,
	o.`status` as order_status_id,
	os.`name` AS os_name,
	st.`name` AS st_name,
        o.order_comment,
	SUM(op.quantity) as quantity
FROM
	oc_x_pre_purchase_order AS o
LEFT JOIN oc_x_pre_purchase_order_product AS op ON o.purchase_order_id = op.purchase_order_id
-- LEFT JOIN oc_x_supplier_type AS st ON st.supplier_type_id = o.supplier_type
LEFT JOIN oc_x_supplier AS st ON st.supplier_id = o.supplier_type
LEFT JOIN oc_x_pre_purchase_order_status AS os ON o.`status` = os.order_status_id
where o.order_type=1 ";
        
        
        if($date != ''){
            $sql .=" and o.date_deliver = '" . $date . "'";
        }
        if($data['order_status_id'] != 0 ){
            $sql .= " AND o.status = " . $data['order_status_id'];
        }
        
        if($data['purchase_order_id'] != 0 ){
            $sql .= " AND o.purchase_order_id = " . $data['purchase_order_id'];
        }
        
        $sql .= " GROUP BY o.purchase_order_id order by o.purchase_order_id asc";
       
        $query = $db->query($sql);
        $results = $query->rows;
        
        $return = array();
        $return['data'] = array();
        
        //echo "<pre>";print_r($results);
        
        if(sizeof($results)){
            foreach($results as $k=>$v){
                
               
                
                $return['data'][$v['order_id']] = $v;
                $return['data'][$v['order_id']]['plan_quantity'] = $v['quantity'];
                $return['data'][$v['order_id']]['added_by'] = '';
                $return['data'][$v['order_id']]['station_id'] = $v['station_id'];
            }
        }
         //print_r($return['data']);
        
        //$last_one_day = date("Y-m-d", time() + 8*3600 - 24*3600);
        //获取入库中间表中已入库的商品，并从计划入库的商品中减去已入库的商品     
        
        $sql = "SELECT
	xis.*
FROM
	oc_x_inventory_purchase_order_sorting AS xis
LEFT JOIN oc_x_pre_purchase_order AS o ON o.purchase_order_id = xis.order_id
WHERE
	o.date_deliver =  '" . $date . "' ";
    
        $query = $db->query($sql);
        $result = $query->rows;
        
        
        
       
        if(sizeof($result)){
            foreach($result as $rk => $rv){
                
                
                
                $return_move_p = array();
                if($return['data'][$rv['order_id']]['quantity'] > 0){
                    $return['data'][$rv['order_id']]['quantity'] -= $rv['quantity'];
                    if($return['data'][$rv['order_id']]['quantity'] <= 0){
                        $return_move_p = $return['data'][$rv['order_id']];
                        unset($return['data'][$rv['order_id']]);
                        $return['data'][$rv['order_id']] = $return_move_p;
                    }
                    $return['data'][$rv['order_id']]['added_by'] = $rv['added_by'];
                
                
                    
                }
            }
        }
        //echo "<pre>";print_r($return);exit;
       
        
        
        if(sizeof($return)){
            
            return $return;
        }
        else{
            return array();
        }


    }
    
    
    function getNotice($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Multi-language
        //TODO Station,Origin
        global $db;
        
        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;
        
        $sql = "SELECT notice_id, title FROM oc_x_notice WHERE status=1 AND station_id = '".$station_id."' AND now() BETWEEN date_start AND date_end ORDER BY notice_id DESC";
        
        if($id>0){
            $sql = "SELECT notice_id, title FROM oc_x_notice WHERE notice_id={$id} AND status=1 AND station_id = '".$station_id."' AND now() BETWEEN date_start AND date_end ORDER BY notice_id DESC";
        }
        $query = $db->query($sql);
        $results = $query->rows;
        
        if($results && sizeof($results)){
            return $results;
        }
        return false;
    }

    // 获取区域仓库notice
    function getNoticeWithWarehouse(array $data){
        global $db;

        $station_id     = !empty($data['station_id'])           ? (int)$data['station_id']           : 1;
        $language_id    = !empty($data['language_id'])          ? (int)$data['language_id']          : 2;
        $notice_id      = !empty($data['data']['notice_id'])    ? (int)$data['data']['notice_id']    : 0;
        $warehouse_id   = !empty($data['data']['warehouse_id']) ? (int)$data['data']['warehouse_id'] : 0;
        if($warehouse_id <= 0){ return array(); }

        $sql = "SELECT n.notice_id, n.title
                  FROM oc_x_notice n
                  LEFT JOIN oc_x_notice_to_warehouse nw ON n.notice_id = nw.notice_id
                  WHERE n.status = 1
                  AND now() BETWEEN n.date_start AND n.date_end";

        !empty($warehouse_id) && $sql .= " AND nw.warehouse_id = ".$warehouse_id;
        !empty($notice_id)    && $sql .= " AND n.notice_id = ".$notice_id;

        $sql    .= " ORDER BY n.notice_id DESC";
        $query   = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return array();
    }

    function getBanner($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Station
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;
        
        $sql = "SELECT 
        b.banner_id, b.name, b.banner_sort, bi.banner_image_id, bi.link, bi.image, bi.sort_order, bid.language_id, bid.title, bid.description
        FROM oc_banner b 
        LEFT JOIN oc_banner_image bi ON (b.banner_id = bi.banner_id) 
        LEFT JOIN oc_banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id) 
        WHERE b.status=1 AND bid.language_id = {$language_id} AND b.station_id = {$station_id} AND now() BETWEEN b.date_start AND b.date_end";
        
        if($id>0){
            $sql = "SELECT 
            b.banner_id, b.name, b.banner_sort, bi.banner_image_id, bi.link, bi.image, bi.sort_order, bid.language_id, bid.title, bid.description
            FROM oc_banner b 
            LEFT JOIN oc_banner_image bi ON (b.banner_id = bi.banner_id) 
            LEFT JOIN oc_banner_image_description bid ON (bi.banner_image_id  = bid.banner_image_id) 
            WHERE b.banner_id = {$id} AND b.status=1 AND bid.language_id = {$language_id} AND b.station_id = {$station_id} AND now() BETWEEN b.date_start AND b.date_end";
        }
        $query = $db->query($sql);
        $results = $query->rows;
        
        if($results && sizeof($results)){
            return $results;
        }
        return array();
    }

    // 获取区域仓库banner
    function getBannerWithWarehouse(array $data){
        global $db;

        $station_id     = !empty($data['station_id'])           ? (int)$data['station_id']           : 1;
        $language_id    = !empty($data['language_id'])          ? (int)$data['language_id']          : 2;
        $banner_id      = !empty($data['data']['banner_id'])    ? (int)$data['data']['banner_id']    : 0;
        $warehouse_id   = !empty($data['data']['warehouse_id']) ? (int)$data['data']['warehouse_id'] : 0;
        if($warehouse_id <= 0){ return array(); }

        $sql = "SELECT
                  b.banner_id, b.name, b.banner_sort, bi.banner_image_id, bi.link, bi.image, bi.sort_order, bid.language_id, bid.title, bid.description
                  FROM oc_banner b
                  LEFT JOIN oc_banner_to_warehouse bw ON b.banner_id = bw.banner_id
                  LEFT JOIN oc_banner_image bi ON b.banner_id = bi.banner_id
                  LEFT JOIN oc_banner_image_description bid ON bi.banner_image_id = bid.banner_image_id
                  WHERE b.status = 1
                  AND bid.language_id = {$language_id}
                  AND bw.station_id = {$station_id}
                  AND now() BETWEEN b.date_start AND b.date_end";

        !empty($warehouse_id) && $sql .= " AND bw.warehouse_id = ".$warehouse_id;
        !empty($banner_id)    && $sql .= " AND b.banner_id = ".$banner_id;

        $query   = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return array();
    }

    function getCategory($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Station,Origin
        //TOOD 目前只处理一级目录
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;
        
        $sql = "SELECT c.parent_id, c.status, cp.image, cpd.description, cpd.name parent_name, cp.sort_order parent_order, c.category_id, cd.name, c.sort_order
        FROM oc_category c
        LEFT JOIN oc_category cp ON c.parent_id = cp.category_id AND cp.status =1
        LEFT JOIN oc_category_description cd ON (c.category_id = cd.category_id and cd.language_id = {$language_id})
        LEFT JOIN oc_category_description cpd ON (cp.category_id = cpd.category_id and cpd.language_id = {$language_id})
        WHERE c.station_id = {$station_id}
        AND c.status = 1
        -- AND (c.category_id = 60 OR c.parent_id = 60)
        ORDER BY parent_order, c.sort_order";
        if($id>0){
            $sql = "SELECT c.parent_id, c.status, cp.image, cpd.name parent_name, cp.sort_order parent_order, c.category_id, cd.name, c.sort_order
        FROM oc_category c
        LEFT JOIN oc_category cp ON c.parent_id = cp.category_id AND cp.status =1
        LEFT JOIN oc_category_description cd ON (c.category_id = cd.category_id and cd.language_id = {$language_id})
        LEFT JOIN oc_category_description cpd ON (cp.category_id = cpd.category_id and cpd.language_id = {$language_id})
        WHERE c.station_id = {$station_id}
        AND c.status = 1
        AND (c.category_id = {$id} OR c.parent_id = {$id})
        ORDER BY parent_order, c.sort_order";
        }
        $query = $db->query($sql);
        $results = $query->rows;

        //整理目录树，目前只有两级，不可对数组排序
        if($results && sizeof($results)){
            $category = array();
            for($m=0;$m<sizeof($results);$m++){
                $pivot = $results[$m]['parent_id'] ? $results[$m]['parent_id'] : $results[$m]['category_id'];

                if($results[$m]['parent_id'] == 0){
                    $category[$pivot]['parent_id'] = $results[$m]['category_id'];
                    $category[$pivot]['image'] = $results[$m]['image'];
                    $category[$pivot]['desc'] = $results[$m]['description'];
                    $category[$pivot]['parent_name'] = $results[$m]['name'];
                }
                else{
                    $category[$pivot]['child'][] = $results[$m];
                }
            }

            return $category;
        }

        return false;
    }

    function orderStatus($order_id, $status_id, $user_id=0, $reason_id=0, $comment=''){
        global $dbm;

        //TODO 取消订单退余额

        $sql="update oc_order set order_status_id = {$status_id} where order_id = '{$order_id}'";

        $bool = true;
        $bool = $bool && $dbm->query($sql);

        if($bool){
            //SUCCESS
            //$this->addOrderHistory($order_id,'后台取消');
            $this->addOrderHistory($order_id,$user_id,$reason_id,$comment);

            //Add MSG Tasks
            //Get status setting, insert into msg
            $sql = "
                INSERT INTO `oc_msg` (`merchant_id`, `customer_id`, `phone`, `order_id`, `msg_param_1`, `msg_param_2`, `isp_template_id`, `msg_type`, `msg_status_id`,`msg_status_name`,`sent`, `status`, `date_added`)
                SELECT 0, o.customer_id, o.shipping_phone, ".$order_id.", '".$order_id."', st.contact_phone, mt.isp_template_id, mt.msg_type, o.order_status_id, os.name, 0, 1, NOW()
                FROM oc_order o
                LEFT JOIN oc_order_status os ON o.order_status_id = os.order_status_id AND os.language_id = 2
                LEFT JOIN oc_x_station st ON o.station_id = st.station_id
                LEFT JOIN oc_msg_template mt ON os.msg_template_id = mt.msg_template_id
                LEFT JOIN oc_customer c ON o.customer_id = c.customer_id
                WHERE
                o.order_id = '".$order_id."' AND o.order_status_id = '".$status_id."'
                AND os.msg = 1 AND c.accept_order_message = 1
                ";
            return $dbm->query($sql);
        }

        return false;
    }

    public function deliverStatus($order_id, $status_id, $user_id){
        global $dbm;

        $sql="update oc_order set order_deliver_status_id = {$status_id} where order_id = '{$order_id}'";
        $bool = true;
        $bool = $bool && $dbm->query($sql);

        if($bool){
            //SUCCESS
            $this->addOrderHistory($order_id,$user_id);

            //Add MSG Tasks
            //Get status setting, customer accept setting insert into msg
            $sql = "
                INSERT INTO `oc_msg` (`merchant_id`, `customer_id`, `phone`, `order_id`, `msg_param_1`, `msg_param_2`, `isp_template_id`, `msg_type`, `msg_status_id`,`msg_status_name`,`sent`, `status`, `date_added`)
                SELECT 0, o.customer_id, o.shipping_phone, ".$order_id.", '".$order_id."', if(o.shipping_code='D2D' or o.order_deliver_status_id=5, st.contact_phone, ps.close_time), mt.isp_template_id, mt.msg_type, o.order_deliver_status_id, os.name, 0, 1, NOW()
                FROM oc_order o
                LEFT JOIN oc_order_deliver_status os ON o.order_deliver_status_id = os.order_deliver_status_id AND os.language_id = 2
                LEFT JOIN oc_x_pickupspot ps ON o.pickupspot_id = ps.pickupspot_id
                LEFT JOIN oc_x_station st ON o.station_id = st.station_id
                LEFT JOIN oc_msg_template mt ON os.msg_template_id = mt.msg_template_id
                LEFT JOIN oc_customer c ON o.customer_id = c.customer_id
                WHERE
                o.order_id = '".$order_id."' AND o.order_deliver_status_id = '".$status_id."'
                AND os.msg = 1 AND c.accept_order_message = 1
                ";
            return $dbm->query($sql);
        }

        return false;

    }

    function addOrderHistory($order_id,$user_id=0, $reason_id=0, $comment=false){
        global $dbm;

        if(!$comment){
            $comment = '';
        }

        $sql = "INSERT INTO  oc_order_history (`order_id`, `notify`, `reason_id`, `comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`)
SELECT '{$order_id}', '0','{$reason_id}', '{$comment}', NOW(), order_status_id, order_payment_status_id, order_deliver_status_id, '{$user_id}' FROM  oc_order WHERE order_id = {$order_id}";

        $dbm->query($sql);
    }

    function randomkeys($length,$pattern)
    {
        //$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ<>?;#:@~[]{}-_=+)(*&^%$"!'; //Char
        //$pattern = $_REQUEST["pattern"];
        $key = '';

        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,(strlen($pattern)-1))};  //mt_rand(), retrun random int
        }
        return $key;
    }

    function sendMsg(array $data){
        global $dbm, $db;
        $post = $data['data'];
        $phone = isset($post['phone']) ? $post['phone'] : '';
        $type  = isset($post['type']) ? $post['type'] : '';
        if(empty($phone) || !preg_match('/1\d{10}/', $phone)){
            return array(
                'return_code'  => 'FAIL',
                'msg'          => '请输入正确的手机号码'
            );
        }

        if(empty($type)){
            return array(
                'return_code' => 'FAIL',
                'msg'         => '请输入正确的类型'
            );
        }

        if($type == 'reg'){
            $query = $db->query("SELECT telephone FROM oc_customer WHERE telephone='{$phone}'");
            if($query->num_rows){
                return array(
                    'return_code' => 'FAIL',
                    'msg'         => '此号码已注册'
                );
            }
        }
        if($type == 'pwd_reset'){
            $query = $db->query("SELECT telephone FROM oc_customer WHERE telephone='{$phone}'");
            if(!$query->num_rows){
                return array(
                    'return_code' => 'FAIL',
                    'msg'         => '此号码尚未注册'
                );
            }
        }

        $random = $this->randomkeys(6, '123456789');
        $time = time()+STATION_LOGIN_YTX_SMS_CODE_LIFE*60; //有效期5分钟(300秒)
        $returnCode = $random;

        $query_history = $db->query("SELECT phone, code, expiration FROM oc_x_msg_valid WHERE phone='{$phone}'");
        $msgInfo = $query_history->row;

        if($query_history->num_rows){
            if($msgInfo['expiration'] > time()){
                $returnCode = $msgInfo['code'];
            }
            else{
                $dbm->query("UPDATE oc_x_msg_valid SET code='{$random}', expiration='{$time}' WHERE phone='{$phone}'");
            }
        }else{
            $dbm->query("INSERT INTO oc_x_msg_valid SET phone='{$phone}', code='{$random}', expiration='{$time}'");
        }

        //异步发送短信
        //$dbm->query("INSERT INTO oc_msg SET phone='" . $phone . "', msg_param_1='" . $random . "', msg_param_2='5', isp_template_id='" . ISP_TEMPLATE_ID_REG . "', date_added=NOW()");

        return array(
            'return_code' => 'SUCCESS',
            'wait_second' => '60',
            'phone' => $phone,
            'random' =>$returnCode
        );

    }
}

$common = new COMMON();
?>