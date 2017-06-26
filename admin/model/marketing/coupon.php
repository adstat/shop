<?php
class ModelMarketingCoupon extends Model {
	public function addCoupon($data) {
		$this->event->trigger('pre.admin.coupon.add', $data);

        $sql = "INSERT INTO " . DB_PREFIX . "coupon SET
                    name = '" . $this->db->escape($data['name']) . "',
                    code = '" . $this->db->escape($data['code']) . "',
                    discount = '" . (float)$data['discount'] . "',
                    type = '" . $this->db->escape($data['type']) . "',
                    total = '" . (float)$data['total'] . "',
                    logged = '" . (int)$data['logged'] . "',
                    shipping = '" . (int)$data['shipping'] . "',
                    date_start = '" . $this->db->escape($data['date_start']) . "',
                    date_end = '" . $this->db->escape($data['date_end']) . "',
                    uses_total = '" . (int)$data['uses_total'] . "',
                    uses_customer = '" . (int)$data['uses_customer'] . "',
                    status = '" . (int)$data['status'] . "',
                    date_added = NOW(), times = '".(int)$data['times']."',
                    customer_request = '".(int)$data['request']."',
                    online_payment = '".(int)$data['online_payment']."',
                    bd_only = '". (int)$data['bd_only'] ."',
                    new_customer = '".(int)$data['newcustomer']."',
                    customer_limited = '".(int)$data['customerlimited']."',
                    station_id = '".(int)$data['station_id']."',
                    reserve_days = '".(int)$data['reserve_days']."',
                    valid_days = '".(int)$data['valid_days']."'";

		$this->db->query($sql);

		$coupon_id = $this->db->getLastId();

		if(isset($data['warehouse_ids'])){
			foreach ($data['warehouse_ids'] as $warehouse){
				$this->db->query("INSERT INTO oc_coupon_to_warehouse SET coupon_id = '" . (int)$coupon_id ."', warehouse_id = '" .(int)$warehouse."', station_id = '" .(int)$data['station_id']."'");

			}
		}

		if (isset($data['coupon_product'])) {
			foreach ($data['coupon_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_product SET coupon_id = '" . (int)$coupon_id . "', product_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['coupon_category'])) {
			foreach ($data['coupon_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_category SET coupon_id = '" . (int)$coupon_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->event->trigger('post.admin.coupon.add', $coupon_id);

		return $coupon_id;
	}
	public function addBanProduct($product_ids,$coupon_id) {
		$result = array();
		if(!$product_ids){
			return $result=array(
				'status'=>'null',
				'message'=>'没有商品'
			);
		}else{
			foreach($product_ids as $v){
//				$sql = "INSERT INTO oc_coupon_product SET coupon_id = ".$coupon_id .","."product_id = ".$v.","."status = ".$status;
				$sql = "INSERT INTO oc_coupon_category_deprecated SET coupon_id = ".$coupon_id .", category_id = 0,"."product_id = ".$v;
				if(!$this->db->query($sql)){
					return $result=array(
						'status'=>'false',
						'message'=>'保存出错'
					);
					break;
				}
			}
		}
		return $result=array(
			'status'=>'true',
			'message'=>'保存成功'
		);
	}
	public function addproduct($product_ids,$coupon_id) {
		$result = array();
		if(!$product_ids){
			return $result=array(
					'status'=>'null',
					'message'=>'没有商品'
			);
		}else{
			foreach($product_ids as $v){
//				$sql = "INSERT INTO oc_coupon_product SET coupon_id = ".$coupon_id .","."product_id = ".$v.","."status = ".$status;
				$sql = "INSERT INTO oc_coupon_product SET coupon_id = ".$coupon_id .","."product_id = ".$v;
				if(!$this->db->query($sql)){
					return $result=array(
							'status'=>'false',
							'message'=>'保存出错'
					);
					break;
				}
			}
		}
		return $result=array(
				'status'=>'true',
				'message'=>'保存成功'
		);
	}
	public function addCustomer($targetTable, $rowData) {
		$coupon_id = $rowData['coupon_id'];
		$customer_id = $rowData['customer_id'];
		$date_start = $rowData['date_start'];
		$date_end = $rowData['date_end'];
		$times = $rowData['times'];
		//$sql = "INSERT INTO $targetTable SET coupon_id = $coupon_id,customer_id = $customer_id,date_start = $date_start,date_end = $date_end,times = $times";
		$sql = "INSERT INTO ".$targetTable . " SET " .'coupon_id'."=".$coupon_id.",".'customer_id'."=".$customer_id.",".'date_start'."=". "'" .$date_start. "'" .",".'date_end'."=". "'" .$date_end. "'" .",".'times'."=".$times;

		$flag = $this->db->query($sql);

		$coupon_customer_id = $this->db->getLastId();

		//拓展历史记录表，为BD发放优惠券做记录
		$sql_h = "INSERT INTO oc_coupon_customer_history (`coupon_customer_history_id`,`coupon_id`,`customer_id`,`date_added`,`added_by`)
 				VALUES ('".(int)$coupon_customer_id."','".(int)$coupon_id."','".(int)$customer_id."', NOW(),'".$this->user->getId()."')";

		$this->db->query($sql_h);

		return $flag;
	}
	public function addCustomers($targetTable, $rowData) {
		$customers = explode(',',$rowData['coupon_customerids']);
		$coupon_id = $rowData['coupon_id'];
		$date_start = $rowData['date_start'];
		$date_end = $rowData['date_end'];
		$times = $rowData['coupon_times'];
		foreach($customers as $customer_id){
			$str = "SELECT customer_id,status from oc_customer where customer_id = $customer_id and status = 1";
			$ifExit=$this->db->query($str);
			if($customer_id && isset($ifExit) && $ifExit->row['status'] == 1){
				$sql = "INSERT INTO ".$targetTable . " SET ";
				$sql .= 'customer_id' . "=" .$customer_id . ",";
				$sql .= 'coupon_id' . "=" . $coupon_id . ",";
				$sql .= 'date_start' . "=" . "'" . $date_start. "'" . ",";
				$sql .= 'date_end' . "=" . "'" .$date_end . "'" . ",";
				$sql .= 'times' . "=" .$times;
				$ip = $this->db->query($sql);
				$coupon_customer_id = $this->db->getLastId();
				//插入历史记录
				$sql_h = "INSERT INTO oc_coupon_customer_history (`coupon_customer_id`,`coupon_id`,`customer_id`,`date_added`,`added_by`)
					VALUES ('".(int)$coupon_customer_id."','".(int)$coupon_id."','".$customer_id."',NOW(),'".$this->user->getId()."')";

				$this->db->query($sql_h);
			}
			if(!$ip){
				continue;
			}
		}
		return true;
	}
	public function getPreCategoriesBinded($coupon_id){
		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order, c1.status, c1.station_id
		FROM " . DB_PREFIX . "category_path cp
		LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id)
		LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id)
		LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id)
		LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id)
		INNER JOIN " . DB_PREFIX . "coupon_category cc ON (cc.category_id = cp.category_id)
		WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'
		and cc.coupon_id = '". (int)$coupon_id ."'
		GROUP BY cp.category_id";

		$categories = $this->db->query($sql)->rows;
		$category_ids = array();
		if(sizeof($categories)>0){
			foreach($categories as $value){
				$category_ids[] = array(
					'category_id' => $value['category_id'],
					'name'        => strip_tags(html_entity_decode($value['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		return $category_ids;

	}

	public function getCategoryBindable($coupon_id){
		$sql = "select category_id from ". DB_PREFIX ."coupon_category where coupon_id = '". (int)$coupon_id ."'";
		$query = $this->db->query($sql);
		$category_id = $query->rows;
		$category_ids = array();

		foreach($category_id as $value){
			$category_ids[] = $value['category_id'];
		}

		$category_ids = implode(",",$category_ids);

		if(strlen($category_ids)){
			$sql_c = " and cp.category_id not in (".$category_ids.")";
		}else{
			$sql_c = " ";
		}

		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order, c1.status, c1.station_id
		FROM " . DB_PREFIX . "category_path cp
		LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id)
		LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id)
		LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id)
		LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id)
		WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'
		". $sql_c ." GROUP BY cp.category_id ORDER BY name ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getPreCategoriesBanned($coupon_id){
		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order, c1.status, c1.station_id
		FROM " . DB_PREFIX . "category_path cp
		LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id)
		LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id)
		LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id)
		LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id)
		INNER JOIN " . DB_PREFIX . "coupon_banned_category cb ON (cb.category_id = cp.category_id)
		WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'
		and cb.coupon_id = '". (int)$coupon_id ."'
		GROUP BY cp.category_id";

		$categories = $this->db->query($sql)->rows;
		$category_ids = array();
		if(sizeof($categories)>0){
			foreach($categories as $value){
				$category_ids[] = array(
					'category_id' => $value['category_id'],
					'name'        => strip_tags(html_entity_decode($value['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		return $category_ids;

	}

    public function resetCouponCategoryBind($coupon_id){
        $this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category_deprecated WHERE category_id >0 and coupon_id = '" . (int)$coupon_id . "'");
    }

	public function banCategory($categories_ids,$coupon_id){
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_banned_category WHERE coupon_id = '". (int)$coupon_id ."'");
		$categories_ids = explode(',',$categories_ids);

		foreach($categories_ids as $value){
			$sql = "INSERT INTO ". DB_PREFIX . "coupon_banned_category SET coupon_id = '". (int)$coupon_id ."',category_id = '". (int)$value ."'";
			$this->db->query($sql);
		}

		return $this->db->getLastId();
	}

	public function addCategory($category,$products,$coupon_id){
		$categories = explode(',',$category);
		$products = explode(';',$products);

		$sql = "select category_id from ".DB_PREFIX."coupon_category where coupon_id = '".$coupon_id."'";
		$queryCategory = $this->db->query($sql)->rows;

		$queryCategories = array();

		foreach($queryCategory as $value){
			$queryCategories[] = $value['category_id'];
		}

        //Todo, find a better way to change coupon category binding settings
        //$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
        //$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category_deprecated WHERE category_id >0 and coupon_id = '" . (int)$coupon_id . "'");

        if($categories){
			foreach($categories as $value){
				$category_duplicate = array();
				if(!in_array($value,$queryCategories)){
					$sql = "INSERT INTO " . DB_PREFIX . "coupon_category SET coupon_id = '" .(int)$coupon_id. "', category_id = '" .(int)$value . "'";
				}else{
					$category_duplicate[] = $value;
				}
				$query = $this->db->query($sql);
			}
		}

		$sql = "select product_id from " . DB_PREFIX ."coupon_category_deprecated where coupon_id = '".$coupon_id."'";
		$queryProduct = $this->db->query($sql)->rows;

		$productWritten = array();

		foreach($queryProduct as $value){
			$productWritten[] = $value['product_id'];
		}

		$productInCategorySql = "select product_id from " . DB_PREFIX ."product_to_category where category_id in ($category)";
		$queryProductToCategoru = $this->db->query($productInCategorySql)->rows;

		$productQuery = array();

		foreach($queryProductToCategoru as $value){
			$productQuery[] = $value['product_id'];
		}

		foreach($products as $value){
			$c_to_p = explode(',',$value);
			if($c_to_p[0]&&$c_to_p[1]){
				if(!in_array($c_to_p[1],$productWritten) && in_array($c_to_p[1],$productQuery)){
					$sql = "INSERT INTO " . DB_PREFIX . "coupon_category_deprecated SET coupon_id = '" .(int)$coupon_id. "', category_id = '" .(int)$c_to_p[0] . "', product_id = '".(int)$c_to_p[1]."'";
				}
				$query = $this->db->query($sql);
			}
		}
		$result = array(
			'is_success' => 'Y',
			'category_d'=> $category_duplicate
		);
		return $result;
	}

	public function getproductBan($array,$coupon_id){
		$category = array();
		foreach($array as $value){
			$category[] = (int)$value['category_id'];
		}
		$categories = implode(",",$category);
		if($categories){
			$sql = "SELECT oc.category_id, oc.coupon_id, od.name category_name,oc.product_id,op.name product_name
				FROM oc_coupon_category_deprecated oc
				LEFT JOIN oc_product op on op.product_id = oc.product_id
				LEFT JOIN oc_category_description od on od.category_id = oc.category_id
				WHERE oc.category_id in ($categories) AND oc.coupon_id = '".$coupon_id."'";
			$query = $this->db->query($sql);
			return $query->rows;
		}
		return false;
	}

	public function getExcludedProducts($coupon_id){
        $sql = "SELECT oc.coupon_id,oc.product_id,op.name product_name, op.status
                FROM oc_coupon_category_deprecated oc
                LEFT JOIN oc_product op on op.product_id = oc.product_id
                WHERE oc.coupon_id = '".$coupon_id."' and category_id = 0";
        $query = $this->db->query($sql);
        return $query->rows;
	}

	public function returnBanProduct($coupon_id,$product_id){
		$sql = "DELETE FROM " . DB_PREFIX . "coupon_category_deprecated WHERE coupon_id = '$coupon_id' AND product_id = '$product_id'";
		$query = $this->db->query($sql);
		if($query){
			return true;
		}
	}
	public function checkProducts($products_ids,$coupon_id) {
		$invalidProducts = array();
		foreach($products_ids as $v){
			$str = "select product_id from oc_product  where product_id = ".$v;
			$invalid = $this->db->query($str)->row;
			if(!$invalid){
				$invalidProducts[]=$v;
			}

			$sql = "select product_id from oc_coupon_product where coupon_id = ".$coupon_id;
			$conflict = $this->db->query($sql)->rows;
			foreach($conflict as $value){
				if($v==$value['product_id'] ){
					$invalidProducts[]=$v;
				}
			}
			$sql = "select product_id from oc_coupon_category_deprecated where category_id = 0 and coupon_id = '". (int)$coupon_id ."' ";
			$conflict_ban = $this->db->query($sql)->rows;
			foreach($conflict_ban as $value){
				if($v==$value['product_id'] ){
					$invalidProducts[]=$v;
				}
			}
		}
		$work = array_merge(array_diff($products_ids, $invalidProducts));
		//根据有效的产品id来查询对应商品的名字
		$workname = array();
		foreach($work as $v){
			$sql = "select concat(name,'  -> ','价格:',price) as name from oc_product where product_id = ".$v;
			$productname = $this->db->query($sql);
			$workname[] = $productname->row['name'];
		}
		$result = array(
			'yes'=>$work,
			'no'=>$invalidProducts,
			'workname'=>$workname,
		);
		//print_r($result);die;
		return $result;
	}
	public function checkCustomers($customer_ids,$coupon_id) {
		$invalidCustomers = array();
		foreach($customer_ids as $v){
		//找无效的客户id
			$str = "SELECT  "."customer_id "."FROM "."oc_customer "."WHERE "."customer_id "."=".$v;
			$invalid = $this->db->query($str)->row;
			if(!$invalid){
				$invalidCustomers[]=$v;
			}
		//找已经绑定的客户id
			$sql = "SELECT customer_id FROM oc_coupon_customer WHERE coupon_id = ".$coupon_id;
			$conflict = $this->db->query($sql)->rows;
			foreach($conflict as $value){
				if($v==$value['customer_id'] ){
					$invalidCustomers[]=$v;
				}
			}
		}
		$work = array_merge(array_diff($customer_ids, $invalidCustomers));
		$result = array(
			'yes'=>$work,
			'no'=>$invalidCustomers
		);
		return $result;
	}
	public function editCoupon($coupon_id, $data) {
		$this->event->trigger('pre.admin.coupon.edit', $data);

        $this->db->query("UPDATE " . DB_PREFIX . "coupon SET
                            name = '" . $this->db->escape($data['name']) . "',
                            discount = '" . (float)$data['discount'] . "',
                            type = '" . $this->db->escape($data['type']) . "',
                            total = '" . (float)$data['total'] . "',
                            logged = '" . (int)$data['logged'] . "',
                            shipping = '" . (int)$data['shipping'] . "',
                            date_start = '" . $this->db->escape($data['date_start']) . "',
                            date_end = '" . $this->db->escape($data['date_end']) . "',
                            uses_total = '" . (int)$data['uses_total'] . "',
                            uses_customer = '" . (int)$data['uses_customer'] . "',
                            status = '" . (int)$data['status'] . "',
                            times = '".(int)$data['times'] ."',
                            customer_request = '".(int)$data['request']."',
                            online_payment = '".(int)$data['online_payment']."',
                            bd_only = '". (int)$data['bd_only'] ."',
                            new_customer = '".(int)$data['newcustomer']."',
                            customer_limited = '".(int)$data['customerlimited']."',
                            station_id = '".$data['station_id']."',
                            reserve_days = '".$data['reserve_days']."',
                            valid_days = '".$data['valid_days']."'
                        WHERE coupon_id = '" . (int)$coupon_id . "'");

		$this->db->query("DELETE FROM oc_coupon_to_warehouse WHERE coupon_id = '" . (int)$coupon_id. "'");
		if(isset($data['warehouse_ids'])){
			foreach ($data['warehouse_ids'] as $warehouse){
				$this->db->query("INSERT INTO oc_coupon_to_warehouse SET coupon_id = '" . (int)$coupon_id ."', warehouse_id = '" .(int)$warehouse."', station_id = '" .(int)$data['station_id']."'");

			}
		}

//		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");
//
//		//取coupon表里的customer_limited字段，如果被改为1，则把绑定的客户status状态置为0
//		//$sql = "select customer_limited from oc_coupon where coupon_id = $coupon_id";
//		//$query = $this->db->query($sql)->row;
//		//if($query['customer_limited']==1){
//		//  $sql = "update oc_coupon_customer set status = 1 where coupon_id = $coupon_id";
//		//  $this->db->query($sql);
//		//}
//
//		if (isset($data['coupon_product'])) {
//			foreach ($data['coupon_product'] as $product_id) {
//				$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_product SET coupon_id = '" . (int)$coupon_id . "', product_id = '" . (int)$product_id . "'");
//			}
//		}
//
//		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
//
//		if (isset($data['coupon_category'])) {
//			foreach ($data['coupon_category'] as $category_id) {
//				$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_category SET coupon_id = '" . (int)$coupon_id . "', category_id = '" . (int)$category_id . "'");
//			}
//		}

		$this->event->trigger('post.admin.coupon.edit', $coupon_id);
	}

	public function deleteCoupon($coupon_id) {
        return false; //DO NOT DELETE

		$this->event->trigger('pre.admin.coupon.delete', $coupon_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon WHERE coupon_id = '" . (int)$coupon_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_history WHERE coupon_id = '" . (int)$coupon_id . "'");

		$this->event->trigger('post.admin.coupon.delete', $coupon_id);
	}

	public function deleteProduct($product_id){
		$this->event->trigger('pre.admin.coupon.productDelete', $product_id);

		$sql = "delete from oc_coupon_product where coupon_product_id = ".$product_id;
		$this->db->query($sql);

		$this->event->trigger('post.admin.coupon.productDelete', $product_id);
	}

	public function getCoupon($coupon_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "coupon WHERE coupon_id = '" . (int)$coupon_id . "'");

		return $query->row;
	}

	public function getCouponByCode($code) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "coupon WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	public function getCoupons($data = array()) {
		$sql = "SELECT A.coupon_id, A.name, A.code, A.discount, A.date_start, A.date_end, A.status, A.online_payment, A.bd_only, A.customer_limited,A.times,A.type,A.total FROM " . DB_PREFIX . "coupon A
		 		left join oc_coupon_to_warehouse B on B.coupon_id = A.coupon_id where 1";

		$sort_data = array(
			'name',
			'code',
			'discount',
			'date_start',
			'date_end',
			'status'
		);

		if (isset($data['filter_name'])) {
			$sql .= " and A.name like '"."%".$data['filter_name'] ."%"."'";
		}

		if (isset($data['filter_valid_days'])) {
			$sql .= " and A.valid_days = '". $data['filter_valid_days'] ."'";
		}

		if (isset($data['filter_status'])){
			$sql .= " and A.status = '". $data['filter_status'] ."'";
		}

		if (isset($data['filter_warehouse_id_global']) && $data['filter_warehouse_id_global']){
			$sql .= " and B.warehouse_id = '". $data['filter_warehouse_id_global'] ."'";
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY coupon_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCouponProducts($coupon_id) {
		$coupon_product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");

		foreach ($query->rows as $result) {
			$coupon_product_data[] = $result['product_id'];
		}

		return $coupon_product_data;
	}

	public function getCouponCategories($coupon_id) {
		$coupon_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");

		foreach ($query->rows as $result) {
			$coupon_category_data[] = $result['category_id'];
		}

		return $coupon_category_data;
	}

	public function getTotalCoupons($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon A
		 		left join oc_coupon_to_warehouse B on B.coupon_id = A.coupon_id where 1";

		if (isset($data['filter_name'])) {
			$sql .= " and A.name like '"."%".$data['filter_name'] ."%"."'";
		}

		if (isset($data['filter_valid_days'])) {
			$sql .= " and A.valid_days = '". $data['filter_valid_days'] ."'";
		}

		if (isset($data['filter_status'])){
			$sql .= " and A.status = '". $data['filter_status'] ."'";
		}

		if (isset($data['filter_warehouse_id_global']) && $data['filter_warehouse_id_global']){
			$sql .= " and B.warehouse_id = '". $data['filter_warehouse_id_global'] ."'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCouponHistories($coupon_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT ch.order_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, ch.discount_total amount, ch.date_added, ch.status FROM " . DB_PREFIX . "coupon_history ch LEFT JOIN " . DB_PREFIX . "customer c ON (ch.customer_id = c.customer_id) WHERE ch.coupon_id = '" . (int)$coupon_id . "' ORDER BY ch.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;
	}

	public function getCategoryHistories($coupon_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}
		$sql = "SELECT od.category_id,od.name,count(cd.category_id) count
		FROM " . DB_PREFIX . "coupon_category oc
		LEFT JOIN " . DB_PREFIX . "category_description od ON od.category_id = oc.category_id
		LEFT JOIN " . DB_PREFIX . "coupon_category_deprecated cd ON cd.category_id = oc.category_id and cd.coupon_id = '".$coupon_id."'
		WHERE oc.coupon_id = '".$coupon_id."'
		GROUP BY oc.category_id
		ORDER BY oc.coupon_id ASC LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getBindHistories($coupon_id, $start = 0, $limit = 10) {
		if ($start<0) {
			$start = 0;
		}
		if ($limit<1) {
			$limit = 10;
		}
		$sql = "select A.customer_id,A.times,A.date_start,A.date_end,B.name,concat(C.firstname,' ',C.lastname) as customer
				from oc_coupon_customer as A
				left join oc_coupon as B on A.coupon_id = B.coupon_id
				left join oc_customer as C on A.customer_id = C.customer_id
				where B.coupon_id = $coupon_id
				order by C.date_added
				limit $start,$limit";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getProductHistories($coupon_id, $start = 0, $limit = 10) {
		if ($start<0) {
			$start = 0;
		}
		if ($limit<1) {
			$limit = 10;
		}
		$sql = "select B.coupon_product_id,B.product_id,B.coupon_id,B.status,A.name
				from oc_product as A
				left join oc_coupon_product as B on A.product_id = B.product_id
				where A.product_id = B.product_id and B.coupon_id = $coupon_id
				order by A.product_id asc
				limit $start,$limit";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalCouponHistories($coupon_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon_history WHERE coupon_id = '" . (int)$coupon_id . "'");

		return $query->row['total'];
	}

	public function getTotalBindHistory($coupon_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon_customer WHERE coupon_id = '" . (int)$coupon_id . "'");

		return $query->row['total'];
	}
	public function getTotalCategoryHistories($coupon_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");

		return $query->row['total'];
	}
	public function getTotalProductHistories($coupon_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");

		return $query->row['total'];
	}
	public function customerInfo($customer_id,$coupon_id) {
		$str = "select customer_id from oc_coupon_customer where coupon_id = $coupon_id";
		$query1 = $this->db->query($str)->rows;
		$result = array();
		if($customer_id){
			foreach($query1 as $v){
				if($customer_id==$v['customer_id']){
					return $result=array(
						'status'=>'false',
						'customerInfo'=>'conflict',
						'message'=>'该客户已经发放该优惠券',
					);
				}
			}

			$sql = "select customer_id,telephone,email,status,concat(firstname,' ',lastname) as name,merchant_name
				from oc_customer
				where customer_id = $customer_id";
			$query = $this->db->query($sql);

			if(!$query){
				return $result=array(
					'customerInfo'=>NULL,
					'status'=>'false',
					'message'=>'该客户不存在'
				);
			}else{
				return $result=array(
					'customerInfo'=>$query->rows,
					'status'=>'true',
					'message'=>'该客户满足要求',
				);
			}
		}
	}
	public function updateStatus($product_id,$status){
		$sql = "update oc_coupon_product set status = " . $status . " where coupon_product_id = " . $product_id ;
		return $this->db->query($sql);
	}
	public function getCouponProductList($category_id){
		$sql = "select C.category_id,C.product_id,P.name
			from oc_product_to_category C
			left join oc_product P on P.product_id = C.product_id
			where category_id = '".$category_id."'";
		$query = $this->db->query($sql);

		$category_to_products = array();

		foreach($query->rows as $value){
			$category_to_products[$category_id][]=$value;
		}
		return $category_to_products;
	}
	public function getCouponCategoryProducts($category_ids){
		$sql = "select product_id from oc_product_to_category where category_id in($category_ids)";

		$query = $this->db->query($sql)->rows;

		return $query;
	}
	public function getCouponProductToCategory($products){
		if($products) {
			$sql = "select CONCAT(category_id,',',product_id,';') as product from oc_product_to_category where product_id in($products)";
			$query = $this->db->query($sql)->rows;
			return $query;
		}else{
			return false;
		}
	}

	public function getCouponWarehouse($coupon_id)
	{
		$coupon_id    = (int)$coupon_id;
		if($coupon_id < 0){ return array(); }

		$query = $this->db->query("SELECT warehouse_id FROM oc_coupon_to_warehouse WHERE coupon_id = '" . $coupon_id . "'");
		return $query->rows;
	}
}