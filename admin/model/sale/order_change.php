<?php
class ModelSaleOrderChange extends Model {
	public function addChange($data) {
            //echo "INSERT INTO `" . DB_PREFIX . "order_change` SET order_id = '" . (int)$data['order_id'] . "',  customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', opened = '" . (int)$data['opened'] . "', change_type_id = '" . (int)$data['change_type_id'] . "', change_status_id = '" . (int)$data['change_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW(), add_user = " . $data['add_user'] . ", change_credits = '" . $data['change_credits'] . "'";exit;
                $this->db->query("INSERT INTO `" . DB_PREFIX . "order_change` SET order_id = '" . (int)$data['order_id'] . "',  customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', opened = '" . (int)$data['opened'] . "', change_type_id = '" . (int)$data['change_type_id'] . "', change_status_id = '" . (int)$data['change_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW(), add_user = " . $data['add_user'] . ", change_credits = '" . $data['change_credits'] . "'");
                $return_id = $this->db->getLastId();
                return $return_id;
                
	}

        
        public function addChangeProduct($return_id,$return_product) {
           
                $sql = "insert into `" . DB_PREFIX . "order_change_product`(change_id,product_id,order_id,change_type_id,model,quantity,price,total,weight_total,weight_change,change_product_credits,product_name) values";
                foreach($return_product as $key=>$value){
                    $sql .= "(" . $return_id . "," . $value['product_id'] . ",'" . $value['order_id'] . "'," . $value['change_type_id'] . ",'" . $value['model'] . "'," . $value['quantity'] . ",'" . $value['price'] . "','" . $value['quantity'] * $value['price'] . "'," . (isset($value['weight_total']) ? $value['weight_total'] : 0) . "," . (isset($value['weight_change']) ? $value['weight_change'] : 0) . ",'" . $value['change_product_credits'] . "','" . $value['name'] . "'),";
                }
                $sql = substr($sql, 0, -1);
                
                $this->db->query($sql);
                
                
        }
        
        
        

	public function editReturn($return_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
	}

        public function editReturnStatus($return_id, $return_status_id) {
                $data = array();
                $data['comment'] = '';
                $this->db->query("UPDATE `" . DB_PREFIX . "return` SET return_status_id = '" . (int)$return_status_id . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
               
                
		$this->db->query("INSERT INTO " . DB_PREFIX . "return_history SET return_id = '" . (int)$return_id . "', return_status_id = '" . (int)$return_status_id . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW(), add_user = " . $this->user->getId());
                

                if($return_status_id == 2){
                    //echo "SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'";exit;
                    $query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'");
                    $return_detail = $query->row;
                    
                    if($return_detail['return_credits'] > 0){
                        //退余额
                        $this->load->model('sale/customer');
                        $this->model_sale_customer->addTransaction($return_detail['customer_id'], "订单 " . $return_detail['order_id'] . " 退货", $return_detail['return_credits'],$return_detail['order_id'],1);
                        
                    }
                    if($return_detail['return_inventory_flag'] == 1){
                        //退库存
                        $return_products = array();
                        $sql = "select rp.* from oc_return as r left join oc_return_product as rp on r.return_id = rp.return_id where r.return_id = " . $return_id;
                        $query =  $this->db->query($sql);
                        $return_products = $query->rows;
                                
                        $return_products_move = array();
                        foreach($return_products as $key=>$value){
                            
                            
                            $return_products_move[] = array(
                                'product_batch' => ' ',
                                'due_date' => '0000-00-00', //There is a bug till year 2099.
                                'product_id' => $value['product_id'],
                                'special_price' => $value['price'],
                                'qty' => $value['quantity']
                                //'qty' => '-'.$value['quantity']
                            );
                            
                        }
                        
                        
                        $dataInv = array();

                        $dataInv['order_id'] = (int)$return_detail['order_id'];
                        $dataInv['api_method'] = 'inventoryReturn';
                        $dataInv['products'] = $return_products_move;
                        $dataInv['timestamp'] = time() + 8*3600 ;

                        $uri =  SITE_URI . "/www/admin/api.php";
                        $data = array(
                            "method" => "inventoryReturn",
                            'data' => json_encode($dataInv),
                            'station_id' => 1
                        );
                        
                        $ch = curl_init ();
                        curl_setopt ( $ch, CURLOPT_URL, $uri );
                        curl_setopt ( $ch, CURLOPT_POST, 1 );
                        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
                        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
                        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
                        $return = curl_exec ( $ch );
                        curl_close ( $ch );

                        
                    }
                }
		
	}

	public function deleteReturn($return_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "return` WHERE return_id = '" . (int)$return_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "return_history WHERE return_id = '" . (int)$return_id . "'");
	}

	public function getReturn($return_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'");

		return $query->row;
	}

	public function getReturns($data = array()) {
		$sql = "SELECT *,r.date_added as return_added,r.date_modified as return_modified, CONCAT(r.firstname, ' ', r.lastname) AS customer, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status FROM `" . DB_PREFIX . "return` r left join " . DB_PREFIX . "user as u on u.user_id = r.add_user ";

		$implode = array();

		if (!empty($data['filter_return_id'])) {
			$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		}

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}

		if (!empty($data['filter_return_status_id'])) {
			$implode[] = "r.return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'r.return_id',
			'r.order_id',
			'customer',
			'r.product',
			'r.model',
			'status',
			'r.date_added',
			'r.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.return_id";
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

        
        
        public function getChangeProducts($data = array()) {
		$sql = "SELECT r.*,rp.product_id as rp_product_id, rp.quantity as rp_quantity, rp.price as rp_price, rp.total as rp_total,rp.change_product_credits, CONCAT(r.firstname, ' ', r.lastname) AS customer FROM `" . DB_PREFIX . "order_change` r left join `" . DB_PREFIX . "order_change_product` as rp on r.change_id = rp.change_id ";

		$implode = array();

		
		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		
		if (!empty($data['filter_change_status_id'])) {
			$implode[] = "r.change_status_id = '" . (int)$data['filter_change_status_id'] . "'";
		}
                
                if (!empty($data['filter_change_type_id'])) {
			$implode[] = "r.change_type_id = '" . (int)$data['filter_change_type_id'] . "'";
		}
               

                
                
                
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.change_id";
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

	public function getTotalReturns($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return`r";

		$implode = array();

		if (!empty($data['filter_return_id'])) {
			$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . $this->db->escape($data['filter_order_id']) . "'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}

		if (!empty($data['filter_return_status_id'])) {
			$implode[] = "r.return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalReturnsByReturnStatusId($return_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_status_id = '" . (int)$return_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnsByReturnReasonId($return_reason_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_reason_id = '" . (int)$return_reason_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnsByReturnActionId($return_action_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_action_id = '" . (int)$return_action_id . "'");

		return $query->row['total'];
	}

	public function addReturnHistory($return_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET return_status_id = '" . (int)$data['return_status_id'] . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "return_history SET return_id = '" . (int)$return_id . "', return_status_id = '" . (int)$data['return_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");

		if ($data['notify']) {
			$return_query = $this->db->query("SELECT *, rs.name AS status FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE r.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "'");

			if ($return_query->num_rows) {
				$this->load->language('mail/return');

				$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'), $return_id);

				$message  = $this->language->get('text_return_id') . ' ' . $return_id . "\n";
				$message .= $this->language->get('text_date_added') . ' ' . date($this->language->get('date_format_short'), strtotime($return_query->row['date_added'])) . "\n\n";
				$message .= $this->language->get('text_return_status') . "\n";
				$message .= $return_query->row['status'] . "\n\n";

				if ($data['comment']) {
					$message .= $this->language->get('text_comment') . "\n\n";
					$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
				}

				$message .= $this->language->get('text_footer');

				$mail = new Mail($this->config->get('config_mail'));
				$mail->setTo($return_query->row['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject($subject);
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}
		}
	}

	public function getReturnHistories($return_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify FROM " . DB_PREFIX . "return_history rh LEFT JOIN " . DB_PREFIX . "return_status rs ON rh.return_status_id = rs.return_status_id WHERE rh.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReturnHistories($return_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_id = '" . (int)$return_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnHistoriesByReturnStatusId($return_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_status_id = '" . (int)$return_status_id . "' GROUP BY return_id");

		return $query->row['total'];
	}
}