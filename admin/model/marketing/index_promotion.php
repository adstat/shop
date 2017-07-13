<?php
class ModelMarketingIndexPromotion extends Model {
    public function getTotalProducts($data = array()){
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_special where 1 ";

        $query = $this->db->query($sql);

	return $query->row['total'];
    }

    public function getProducts($date_end,$product_id,$station_id){
	//比如查询2017-05-24号是否有效 则活动促销结束时间必须大于2017-05-24 59:59:59,开始时间必须小于2017-05-24 00:00:00
	$query_date_start = $date_end . '00:00:00';
	$query_date_end = $date_end . '23:59:59';
	$sql = "SELECT
		    ps.product_special_id,
		    p.product_id,
		    round(p.price,2) ori_price,
		    pd.name,
		    ps.priority priority,
		    ps.price,
		    ps.maximum,
		    ps.showup,
		    ps.promo_title,
		    if(ps.date_start = '0000-00-00','',ps.date_start) date_start,
		    if(ps.date_end = '0000-00-00','',ps.date_end) date_end,
		    if(p.instock=1, if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-p.safestock, 999) stock,
		    if( (if(sum(A.quantity) is null, 0,sum(A.quantity))-p.safestock) = 0, 0 , 1) stock_order
		    FROM oc_product_to_category pc
		    RIGHT JOIN oc_category c ON (pc.category_id = c.category_id)
		    RIGHT JOIN oc_category_description cd ON (c.category_id = cd.category_id)
		    LEFT JOIN oc_product p ON (pc.product_id = p.product_id AND p.status = 1)
		    LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)
		    LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)
		    LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND date('".$date_end."') BETWEEN ps.date_start AND ps.date_end)
		    LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)
		    LEFT JOIN oc_x_inventory_move_item A on p.product_id = A.product_id and A.station_id = '".$station_id."' and A.status=1
		    WHERE c.status =1 and p.station_id = '".$station_id."'
		    AND ps.product_id IS NOT NULL
		    ";

	if(isset($date_end)&&$date_end){
	    $sql .= " and ps.date_start <= '".$query_date_start."' and ps.date_end > '".$query_date_end."'";
	}

	if(isset($product_id)&&$product_id){
	    $sql .= " and p.product_id = '". $product_id ."'";
	}
	$sql .= "  GROUP BY p.product_id
	ORDER BY stock_order DESC, priority ASC, p.product_id DESC";

	$query = $this->db->query($sql);

	return $query->rows;
    }

    public function getSort($data){
	$sql = "select ps.product_id,ps.warehouse_id,w.title warehouse_name,ps.area_id,
		if(ps.area_id = 0 , '全部', a.name) area_name,p.name product_name,ps.priority

		from oc_product_special ps
		left join oc_product p on ps.product_id = p.product_id
		left join oc_x_warehouse w on w.warehouse_id = ps.warehouse_id
		left join oc_x_area a on a.area_id = ps.area_id
		where 1";

	if(!empty($data['data']['sort_date'])){
	    $query_date_start = $data['data']['sort_date'] . ' 00:00:00';
	    $query_date_end = $data['data']['sort_date'] . ' 23:59:59';

	    $sql .= " and ps.date_start <= '".$query_date_start."' and ps.date_end > '".$query_date_end."'";

	}

	if($data['data']['filter_warehouse_id_global']){
	    $sql .= " and ps.warehouse_id = '".(int) $data['data']['filter_warehouse_id_global']."'";
	}

	$sql .= " group by ps.product_id order by ps.priority asc";

	$query = $this->db->query($sql);

	return $query->rows;
    }

    public function getHistories($data){
	$date_end = $data['data']['search_date'];
	$product_id = $data['data']['product_id'];
	$station_id = $data['data']['station_id'];

	$sql = "select ps.product_id,ps.warehouse_id,w.title warehouse_name,ps.area_id,
		if(ps.area_id = 0 , '全部', a.name) area_name,p.name product_name,ps.price,
		ps.promo_title,p.price ori_price,ps.date_start,ps.date_end,ps.maximum
		from oc_product_special ps
		left join oc_product p on ps.product_id = p.product_id
		left join oc_x_warehouse w on w.warehouse_id = ps.warehouse_id
		left join oc_x_area a on a.area_id = ps.area_id
		where 1";

//        $sql_t = "select count(*) total
//                from oc_product_special ps
//                left join oc_product p on ps.product_id = p.product_id
//                left join oc_x_warehouse w on w.warehouse_id = ps.warehouse_id
//                where 1";

	if(!empty($data['data']['search_date'])){
	    $query_date_start = $date_end . ' 00:00:00';
	    $query_date_end = $date_end . ' 23:59:59';

	    $sql .= " and ps.date_start <= '".$query_date_start."' and ps.date_end > '".$query_date_end."'";
//            $sql_t .= " and ps.date_start <= '".$query_date_start."' and ps.date_end > '".$query_date_end."'";

	}

	if(isset($data['data']['product_id'])&&$data['data']['product_id']){
	    $sql .= " and ps.product_id = '". $product_id ."'";
//            $sql_t .= " and ps.product_id = '". $product_id ."'";
	}

	if($data['data']['filter_warehouse_id_global']){
	    $sql .= " and ps.warehouse_id = '".(int) $data['data']['filter_warehouse_id_global']."'";
//            $sql_t .= " and ps.warehouse_id = '".(int) $data['data']['filter_warehouse_id_global']."'";
	}else{
	    $sql .= " and p.station_id = '".(int)$station_id."'";
//            $sql_t .= " and p.station_id = '".(int)$station_id."'";

	}

	$sql .= " group by ps.product_id , ps.warehouse_id ,ps.area_id";

	$query = $this->db->query($sql)->rows;
//        $query_t = $this->db->query($sql_t)->row['total'];

	$results = array(
	    'result' => $query,
//            'result_total' => $query_t
	);

	return $results;
    }

    public function editProducts($data = array(),$warehouse_id){


	if(!$warehouse_id){
	    //没有选择指定仓库，将会保存到指定平台下的所有仓库
	    $sql = "select warehouse_id from oc_x_warehouse where status = 1 and station_id = '".(int)$data['set-station']."'";

	    $warehouse = $this->db->query($sql)->rows;

	    if(sizeof($warehouse)){
			foreach($warehouse as $value){
				//每种设置的商品促销，在仓库里一一保存
				foreach($data['product'] as $product){
					if($product['price']){
						$this->db->query("delete from oc_product_special where product_id = '".(int)$product['product_id']."' and warehouse_id = '".(int)$value['warehouse_id']."'");
						$insert = "INSERT INTO oc_product_special (`product_id`,`warehouse_id`,`price`,`maximum`,`promo_title`,`date_start`,`date_end`,`priority`)
						values('".$product['product_id']."','".$value['warehouse_id']."','".$product['price']."','".$product['maximum']."','".$product['promo_title']."','".$product['date_start']."','".$product['date_end']."','".$product['priority']."')";
						$this->db->query($insert);

						$product_special_id = $this->db->getLastId();

						//插入特价历史记录

						$sql_s = "INSERT INTO oc_product_special_history (`product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end`,`maximum`,`showup`,`is_promo`,`promo_title`,`promo_limit`,`warehouse_id`,`area_id`,`user_id_modify`,`date_modify`)
						select `product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end`,`maximum`,`showup`,`is_promo`,`promo_title`,`promo_limit`,`warehouse_id`,`area_id`,'". $this->user->getId() ."',now() from oc_product_special where product_special_id = '".$product_special_id ."'";

						$this->db->query($sql_s);
					}
				}
			}
	    }

	}else{
	    //判断是否指定到区域，如果没有指定，area_id为0
	    if(!isset($data['set-area'])){
			$area_id = 0;
			foreach($data['product'] as $product){
				if($product['price']){
					$this->db->query("delete from oc_product_special where product_id = '".$product['product_id']."' and warehouse_id = '".(int)$warehouse_id."'");
					$insert = "INSERT INTO oc_product_special (`product_id`,`warehouse_id`,`area_id`,`price`,`maximum`,`promo_title`,`date_start`,`date_end`,`priority`)
					values('".$product['product_id']."','".$warehouse_id."','".$area_id."','".$product['price']."','".$product['maximum']."','".$product['promo_title']."','".$product['date_start']."','".$product['date_end']."','".$product['priority']."')";
					$this->db->query($insert);
					//插入特价历史记录
					$product_special_id = $this->db->getLastId();

					$sql_s = "INSERT INTO oc_product_special_history (`product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end`,`maximum`,`showup`,`is_promo`,`promo_title`,`promo_limit`,`warehouse_id`,`area_id`,`user_id_modify`,`date_modify`)
						select `product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end`,`maximum`,`showup`,`is_promo`,`promo_title`,`promo_limit`,`warehouse_id`,`area_id`,'". $this->user->getId() ."',now() from oc_product_special where product_special_id = '".$product_special_id ."'";

					$this->db->query($sql_s);
				}
			}
	    }else{
			foreach($data['set-area'] as $area){
				foreach($data['product'] as $product){
					if($product['price']){
						$this->db->query("delete from oc_product_special where product_id = '".$product['product_id']."' and warehouse_id = '".(int)$warehouse_id."' and area_id = '".$area."'");
						$insert = "INSERT INTO oc_product_special (`product_id`,`warehouse_id`,`area_id`,`price`,`maximum`,`promo_title`,`date_start`,`date_end`,`priority`)
						values('".$product['product_id']."','".$warehouse_id."','".$area."','".$product['price']."','".$product['maximum']."','".$product['promo_title']."','".$product['date_start']."','".$product['date_end']."','".$product['priority']."')";
						$this->db->query($insert);

						//插入特价历史记录
						$product_special_id = $this->db->getLastId();

						$sql_s = "INSERT INTO oc_product_special_history (`product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end`,`maximum`,`showup`,`is_promo`,`promo_title`,`promo_limit`,`warehouse_id`,`area_id`,`user_id_modify`,`date_modify`)
						select `product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end`,`maximum`,`showup`,`is_promo`,`promo_title`,`promo_limit`,`warehouse_id`,`area_id`,'". $this->user->getId() ."',now() from oc_product_special where product_special_id = '".$product_special_id ."'";

						$this->db->query($sql_s);
					}
				}
			}
	    }
	}
	//加入指定到区域的修改
//        if(!$warehouse_id){
//            //没有选择指定仓库，将会保存到指定平台下的所有仓库
//            $sql = "select warehouse_id from oc_x_warehouse where station_id =" . (int)$data['set-station'];
//
//            $warehouse = $this->db->query($sql)->rows;
//
//            if(sizeof($warehouse)){
//                foreach($warehouse as $value){
//                    //每种设置的商品促销，在仓库里一一保存
//                    foreach($data['product'] as $product){
//                        $this->db->query("delete from oc_product_special where product_id = '".(int)$product['product_id']."' and warehouse_id = '".(int)$value['warehouse_id']."'");
//                        $insert = "INSERT INTO oc_product_special (`product_id`,`warehouse_id`,`price`,`maximum`,`promo_title`,`date_start`,`date_end`)
//                        values('".$product['product_id']."','".$value['warehouse_id']."','".$product['price']."','".$product['maximum']."','".$product['promo_title']."','".$product['date_start']."','".$product['date_end']."')";
//                        $this->db->query($insert);
//                    }
//                }
//            }
//
//        }else{
//            foreach($data['product'] as $product){
//                $this->db->query("delete from oc_product_special where product_id = '".$product['product_id']."' and warehouse_id = '".(int)$warehouse_id."'");
//                $insert = "INSERT INTO oc_product_special (`product_id`,`warehouse_id`,`price`,`maximum`,`promo_title`,`date_start`,`date_end`)
//                        values('".$product['product_id']."','".$warehouse_id."','".$product['price']."','".$product['maximum']."','".$product['promo_title']."','".$product['date_start']."','".$product['date_end']."')";
//                $this->db->query($insert);
//            }
//        }

    }

    public function deleteProduct($product_special_id){
	$sql = "DELETE FROM oc_product_special where product_special_id = '".(int)$product_special_id."'";

	$result = $this->db->query($sql);

	return $result;
    }

    public function getProductInfo($product_id,$warehouse_id){
		//如果做促销的时候选择了对应仓库，则给出仓库的商品价格,否则已仓库最低价做判断，并且库存为仓库平均库存
		if($warehouse_id){
			//由于每晚结算的商品可售库存并没有添加仓库属性字段，且当前系统每个平台只有一个仓库，暂且拿掉warehouse_id的条件
			$sql = "SELECT
			pd.name,if(p.instock=1,
			if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-p.safestock, 999) stock,
			round(pw.price,2) ori_price,
			round(c.purchase_cost*p.price_protect,2) limit_price,
			round(pw.price*0.95,2) compare_price
			FROM oc_product p
			LEFT JOIN oc_product_description pd on pd.product_id = p.product_id
			LEFT JOIN oc_product_to_warehouse pw on pw.product_id = p.product_id
			LEFT JOIN oc_x_inventory_move_item A on p.product_id = A.product_id and A.station_id = 2 and A.status=1
			LEFT JOIN oc_x_purchase_cost c on c.product_id = p.product_id
			WHERE p.product_id = '".$product_id."' and pw.warehouse_id = '".$warehouse_id."'
			group by pw.product_id";
		}else{
			$sql = "SELECT
			pd.name,
			min(pi.stock) min_stock,
			max(pi.stock) max_stock,
			concat('均存',avg(pi.stock)) stock,
			round(min(pw.price),2) ori_price,
			round(c.purchase_cost*p.price_protect,2) limit_price,
			round(min(pw.price)*0.95,2) compare_price
			FROM oc_product p
			LEFT JOIN oc_product_description pd on pd.product_id = p.product_id
			LEFT JOIN oc_product_to_warehouse pw on pw.product_id = p.product_id
			LEFT JOIN (
				select p.product_id, if(p.instock=1,if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-p.safestock, 999) stock
				from oc_product p
				left join oc_x_inventory_move_item A on p.product_id = A.product_id and A.station_id = 2 and A.status=1
				where p.product_id = '".$product_id."'
				group by A.warehouse_id
			) pi on pi.product_id = p.product_id
			LEFT JOIN oc_x_purchase_cost c on c.product_id = p.product_id
			WHERE p.product_id = '".$product_id."'
			group by pw.product_id";
		}


	$result = $this->db->query($sql);

	return $result->rows;
    }

    public function updatePromotion($data=array()){

	$sql = "update oc_product_special set
		price = '".$data['price']."',
		date_start = '".$data['start']."',
		date_end = '".$data['end']."',
		promo_title = '".$data['title']."',
		maximum = '".$data['maximum']."'
		where product_id = '".(int)$data['product_id']."'
		and warehouse_id = '".(int)$data['warehouse_id']."'
		and area_id = '".(int)$data['area_id']."'";

	$query = $this->db->query($sql);

	if($query){
	    return true;
	}else{
	    return false;
	}
    }

    public function resetSort($data=array()){
	foreach($data as $key => $value){
	    $product_id = substr($key,0,strpos($key,'_'));
	    $warehouse_id = substr($key,strpos($key,'_')+1);
	    $sql = "update oc_product_special set priority = '".(int)$value."'
		where product_id = '".(int)$product_id."' and warehouse_id = '".(int)$warehouse_id."'";
	    $this->db->query($sql);
	}

	return true;
    }

}