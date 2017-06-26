<?php
class ModelCatalogSupplier extends Model {
	public function addManufacturer($data) {
		$added_by = $this->user->getId();

		$date_added = date('Y-m-d H:i:s');

		//$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "x_supplier SET name = '" . $this->db->escape($data['name']) . "', contact_name = '" . $this->db->escape($data['contact_name']) . "', contact_phone = '" . $this->db->escape($data['contact_phone']) . "', memo = '" . $this->db->escape($data['memo']) . "',  status = '" . (int)$data['status'] . "',market_id = '" . (int)$data['market_id'] . "',checkout_type_id = '" . (int)$data['checkout_type_id'] . "',checkout_cycle_id = '" . (int)$data['checkout_cycle_id'] . "',checkout_cycle_num = '" . (int)$data['checkout_cycle_num'] . "',checkout_username = '" . $this->db->escape($data['checkout_username']) . "',checkout_userbank = '" . $this->db->escape($data['checkout_userbank']) . "',checkout_usercard = '" . $this->db->escape($data['checkout_usercard']) . "', manage_by = '".$data['filter_manage_userid'] ."',checkout_cycle_date = '". (int)$data['checkout_cycle_date'] ."',invoice_flag = '" . (int)$data['invoice_flag'] ."',added_by = '".(int)$added_by."',date_added = '".$date_added."' ");



		$manufacturer_id = $this->db->getLastId();

		//$this->db->query("INSERT INTO " . DB_PREFIX . "x_supplier_type SET name = '" . $this->db->escape($data['name']) . "', supplier_type_id = '" . (int)$manufacturer_id . "'");
		//$this->db->query("update " . DB_PREFIX . "x_supplier SET type = '" . $manufacturer_id . "' where  supplier_id = '" . (int)$manufacturer_id . "'");



		return $manufacturer_id;
	}

	public function editManufacturer($supplier_id, $data) {

		$this->db->query("UPDATE " . DB_PREFIX . "x_supplier SET name = '" . $this->db->escape($data['name']) . "', contact_name = '" . $this->db->escape($data['contact_name']) . "', contact_phone = '" . $this->db->escape($data['contact_phone']) . "', memo = '" . $this->db->escape($data['memo']) . "',  status = '" . (int)$data['status'] . "',market_id = '" . (int)$data['market_id'] . "',checkout_type_id = '" . (int)$data['checkout_type_id'] . "',checkout_cycle_id = '" . (int)$data['checkout_cycle_id'] . "',checkout_cycle_num = '" . (int)$data['checkout_cycle_num'] . "',checkout_username = '" . $this->db->escape($data['checkout_username']) . "',checkout_userbank = '" . $this->db->escape($data['checkout_userbank']) . "',checkout_usercard = '" . $this->db->escape($data['checkout_usercard']) . "',invoice_flag = '" . (int)$data['invoice_flag'] . " ' ,manage_by = '".$data['filter_manage_userid'] ."',checkout_cycle_date = '". (int)$data['checkout_cycle_date'] ."' WHERE supplier_id = '" . (int)$supplier_id . "'");

		//$this->db->query("UPDATE " . DB_PREFIX . "x_supplier_type SET name = '" . $this->db->escape($data['name']) . "' WHERE supplier_type_id = '" . (int)$supplier_id . "'");

	}

	public function deleteManufacturer($manufacturer_id) {
		$this->event->trigger('pre.admin.manufacturer.delete', $manufacturer_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		$this->cache->delete('manufacturer');

		$this->event->trigger('post.admin.manufacturer.delete', $manufacturer_id);
	}

	public function getSupplier($supplier_id) {
		$query = $this->db->query("SELECT DISTINCT A.* ,B.username ,C.name usergroup FROM " . DB_PREFIX . "x_supplier A
   LEFT JOIN oc_user B ON A.manage_by = B.user_id
   LEFT JOIN oc_user_group C ON C.user_group_id = B.user_group_id  WHERE supplier_id = '" . (int)$supplier_id . "'");

		return $query->row;
	}
        
        public function getMarket($supplier_id) {
		$query = $this->db->query("SELECT DISTINCT m.* FROM " . DB_PREFIX . "x_supplier as s left join oc_x_market as m on s.market_id = m.market_id WHERE s.supplier_id = '" . (int)$supplier_id . "'");

		return $query->row;
	}



	public function getManageUsergroups(){
		$sql = "SELECT concat(A.lastname,A.firstname) name, A.username,B.name usergroup ,A.user_id FROM oc_user A LEFT JOIN  oc_user_group B ON A.user_group_id = B.user_group_id WHERE A.status = 1 and A.user_group_id in(1,14,16,22,32) ORDER BY A.user_group_id";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getSuppliers($data = array()) {
		$sql = "SELECT A.supplier_id ,A.name,A.added_by,C.name usergroup,B.username,A.date_added,A.status, A.manage_by
		FROM " . DB_PREFIX . "x_supplier A
		LEFT JOIN oc_user B ON A.manage_by = B.user_id
		LEFT JOIN oc_user_group C ON C.user_group_id = B.user_group_id
		WHERE 1 = 1 ";



		$sort_data = array(
			'supplier_id',
			'name',
			'sort_order'
		);

		if (!empty($data['filter_name']) && is_numeric($data['filter_name']) ) {
			$sql .= " AND A.supplier_id  = " . $this->db->escape($data['filter_name']) . " ";
		}else{
			$sql .= " AND A.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_added'])) {
			$sql .= " AND A.added_by LIKE '%" . $this->db->escape($data['filter_added']) . "%'";
		}

		if (!empty($data['filter_manage'])) {
			$sql .= " AND B.username LIKE '%" . $this->db->escape($data['filter_manage']) . "%'";
		}

		if(isset($data['filter_date_start']) && !is_null($data['filter_date_start'])){
			$sql .= " AND DATE(A.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if(isset($data['filter_date_end']) && !is_null($data['filter_date_end'])){
			$sql .= " AND DATE(A.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND A.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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
        
        
         public function getTransactionType() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "x_supplier_transaction_type where added_type = 1 ");

		return $query->rows;
	}

        public function getTransactions($supplier_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "x_supplier_transaction WHERE supplier_id = '" . (int)$supplier_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

        public function getTransactionTotal($supplier_id,$is_enabled_flag = 0,$is_enabled = 1) {
            
                $sql = "SELECT SUM(amount) AS total FROM " . DB_PREFIX . "x_supplier_transaction WHERE supplier_id = '" . (int)$supplier_id . "'";
                
                if($is_enabled_flag == 1){
                    $sql .= (' and is_enabled = ' . $is_enabled);
                }
                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
        public function getTotalTransactions($supplier_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "x_supplier_transaction WHERE supplier_id = '" . (int)$supplier_id . "'");

		return $query->row['total'];
	}
        public function getOrderIds($supplier_id=0){
            $return = array();
            if($supplier_id){
                $sql = "select purchase_order_id from " . DB_PREFIX . "x_pre_purchase_order where supplier_type = " . $supplier_id . " order by purchase_order_id desc";
                $query = $this->db->query($sql);
                $result = $query->rows;
                $return = array();
                if(!empty($result)){
                    foreach($result as $key=>$value){
                        $return[] = $value['purchase_order_id'];
                    }
                }
            }

            return $return;
        }
        
	public function addTransaction($supplier_id, $description = '', $amount = '', $order_id = 0,$supplier_transaction_type_id = 1, $change_id=0, $return_id=0) {
            
            $customer_info = $this->getSupplier($supplier_id);
        $user = $this->user->getId();
        
        $sql = "select * from oc_x_supplier_transaction_type where customer_transaction_type_id = " . $supplier_transaction_type_id;
        $query = $this->db->query($sql);
        $supplier_transaction_type_info = $query->row;

		if ($customer_info) {
                    
			$this->db->query("INSERT INTO " . DB_PREFIX . "x_supplier_transaction SET added_by = '". $user ."', supplier_id = '" . (int)$supplier_id . "', purchase_order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', supplier_transaction_type_id = '" . $supplier_transaction_type_id . "', date_added = NOW(), is_enabled = " . $supplier_transaction_type_info['is_enabled']);

//			$this->load->language('mail/customer');
//
//			$this->load->model('setting/store');
//
//			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);
//
//			if ($store_info) {
//				$store_name = $store_info['name'];
//			} else {
//				$store_name = $this->config->get('config_name');
//			}
//
//			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
//			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($customer_id)));

//			$mail = new Mail($this->config->get('config_mail'));
//			$mail->setTo($customer_info['email']);
//			$mail->setFrom($this->config->get('config_email'));
//			$mail->setSender($store_name);
//			$mail->setSubject(sprintf($this->language->get('text_transaction_subject'), $this->config->get('config_name')));
//			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
//			$mail->send();
		}
	}

        
        public function getMarkets($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "x_market";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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

	public function getManufacturerStores($manufacturer_id) {
		$manufacturer_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}

		return $manufacturer_store_data;
	}

	public function getTotalManufacturers() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "x_supplier");

		return $query->row['total'];
	}
        
	public function getCheckoutType() {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "x_supplier_checkout_type");

	return $query->rows;
	}
	public function getCheckoutCycle() {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "x_supplier_checkout_cycle");

	return $query->rows;
	}

	public function userPermission($supplier_id,$user_id){
		$sql = "select * from " . DB_PREFIX . "x_supplier where supplier_id = '". $supplier_id ."' and manage_by = '". $user_id ."'";

		$query = $this->db->query($sql);

		if(sizeof($query->rows)){
			return true;
		}else{
			return false;
		}
	}
}