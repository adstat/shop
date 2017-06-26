<?php
class ModelSaleReturn extends Model {
	public function addReturn($data) {
           
                //echo "INSERT INTO `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', return_status_id = '" . (int)$data['return_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW()";exit;
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', return_status_id = '" . (int)$data['return_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW()");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "',  customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', return_status_id = '" . (int)$data['return_status_id'] . "', return_inventory_flag = '" . (int)$data['return_inventory_flag'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW(), add_user = " . $data['add_user'] . ", return_credits = '" . $data['return_credits'] . "'");
                $return_id = $this->db->getLastId();
                return $return_id;
                
	}

        
        public function addReturnProduct($return_id,$return_product) {
           
                $sql = "insert into `" . DB_PREFIX . "return_product`(return_id,product_id,product,model,quantity,price,total,return_product_credits,return_product_desc,return_product_action) values";
                foreach($return_product as $key=>$value){
                    $sql .= "(" . $return_id . "," . $value['product_id'] . ",'" . (isset($value['name']) ? $value['name'] : "") . "','" . $value['model'] . "'," . $value['quantity'] . ",'" . $value['price'] . "','" . $value['quantity'] * $value['price'] . "','" . $value['return_product_credits'] . "','" . (isset($value['return_product_desc']) ? $this->db->escape($value['return_product_desc']) : '') . "','" . (isset($value['return_product_action']) ? $this->db->escape($value['return_product_action']) : '') . "'),";
                }
                $sql = substr($sql, 0, -1);
                $this->db->query($sql);
                
                
        }
        
        
        

	public function editReturn($return_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
	}

	public function editReturnCredits($return_id, $return_status_id){
		$data = array();
		$data['comment'] = '';
		if($return_status_id == 2){

			$this->db->query("UPDATE `" . DB_PREFIX . "return` SET credits_returned = 1, date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");

			$this->db->query("INSERT INTO " . DB_PREFIX . "return_history SET return_id = '" . (int)$return_id . "', return_status_id = '" . (int)$return_status_id . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW(), add_user = " . $this->user->getId());

			$query = $this->db->query("SELECT DISTINCT r.*, ( SELECT CONCAT(c.firstname, ' ', c.lastname) FROM oc_customer c WHERE c.customer_id = r.customer_id ) AS customer, o.station_id, o.order_payment_status_id FROM oc_return r LEFT JOIN oc_order AS o ON o.order_id = r.order_id WHERE r.return_id = '" . (int)$return_id . "'");
			$return_detail = $query->row;

			if($return_detail['return_credits'] > 0){
				//退余额
				$this->load->model('sale/customer');
				if($return_detail['return_reason_id'] == 1 || $return_detail['return_reason_id'] == 5  && $return_detail['order_payment_status_id'] != 1){
					$this->model_sale_customer->addTransaction($return_detail['customer_id'], "订单 " . $return_detail['order_id'] . " 缺货", $return_detail['return_credits'],$return_detail['order_id'],9,0,$return_id);
				}
				else{
					$this->model_sale_customer->addTransaction($return_detail['customer_id'], "订单 " . $return_detail['order_id'] . " 退货", $return_detail['return_credits'],$return_detail['order_id'],1,0,$return_id);
				}

			}
		}else{
			$return = array("error"=>1);
			return $return;
		}

	}

        public function editReturnStatus($return_id, $return_status_id) {
                $data = array();
                $data['comment'] = '';
                
                if($return_status_id == 2){
                    //echo "SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'";exit;
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "return WHERE return_id = '" . (int)$return_id . "'");
                    $return_detail = $query->row;
                    if($return_detail['return_status_id'] == 2 || $return_detail['return_status_id'] == 3 ){
                        $return = array("error"=>1);
                        return $return;
                        
                    }
                }
                
                
                
                $this->db->query("UPDATE `" . DB_PREFIX . "return` SET return_status_id = '" . (int)$return_status_id . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
               
                
		$this->db->query("INSERT INTO " . DB_PREFIX . "return_history SET return_id = '" . (int)$return_id . "', return_status_id = '" . (int)$return_status_id . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW(), add_user = " . $this->user->getId());
                

                if($return_status_id == 2){
                    //echo "SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'";exit;
                    $query = $this->db->query("SELECT DISTINCT r.*, ( SELECT CONCAT(c.firstname, ' ', c.lastname) FROM oc_customer c WHERE c.customer_id = r.customer_id ) AS customer, o.station_id, o.order_payment_status_id FROM oc_return r LEFT JOIN oc_order AS o ON o.order_id = r.order_id WHERE r.return_id = '" . (int)$return_id . "'");
                    $return_detail = $query->row;
                    
                    if($return_detail['return_credits'] > 0){
                        //退余额
                        $this->load->model('sale/customer');
                        if($return_detail['return_reason_id'] == 1  && $return_detail['order_payment_status_id'] != 1){
                            $this->model_sale_customer->addTransaction($return_detail['customer_id'], "订单 " . $return_detail['order_id'] . " 缺货", $return_detail['return_credits'],$return_detail['order_id'],9,0,$return_id);
                        }
                        else{
                            $this->model_sale_customer->addTransaction($return_detail['customer_id'], "订单 " . $return_detail['order_id'] . " 退货", $return_detail['return_credits'],$return_detail['order_id'],1,0,$return_id);
                        }
                        
                        
                    }
                    if($return_detail['return_inventory_flag'] == 1){
                        //退库存
                        $return_products = array();
                        $sql = "select rp.*,p.sku_id from oc_return as r left join oc_return_product as rp on r.return_id = rp.return_id left join oc_product as p on p.product_id = rp.product_id where r.return_id = " . $return_id;
                        $query =  $this->db->query($sql);
                        $return_products = $query->rows;
                                
                        $return_products_move = array();
                        foreach($return_products as $key=>$value){
                            
                            
                            $return_products_move[] = array(
                                'product_batch' => ' ',
                                'due_date' => '0000-00-00', //There is a bug till year 2099.
                                'product_id' => $value['product_id'],
                                'special_price' => $value['price'],
                                'qty' => $value['quantity'],
                                'sku_id' => $value['sku_id']
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
		$sql = "SELECT *,r.date_added as return_added,r.date_modified as return_modified, CONCAT(r.firstname, ' ', r.lastname) AS customer, sum(rp.total) AS return_total, sum(rp.quantity/rp.box_quantity) AS return_real_quantity, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status FROM `" . DB_PREFIX . "return` r left join " . DB_PREFIX . "return_product as rp on rp.return_id = r.return_id  left join " . DB_PREFIX . "user as u on u.user_id = r.add_user ";

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

		$sql .= " GROUP BY r.return_id ";

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

        
        
        public function getReturnProducts($data = array()) {
		$sql = "SELECT r.*,rp.product_id as rp_product_id, rp.quantity as rp_quantity, rp.in_part, rp.box_quantity as box_quantity, rp.product as rp_product, rp.price as rp_price, rp.total as rp_total,rp.return_product_credits, CONCAT(r.firstname, ' ', r.lastname) AS customer, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status,rp.return_product_desc,rp.return_product_action FROM `" . DB_PREFIX . "return` r left join `" . DB_PREFIX . "return_product` as rp on r.return_id = rp.return_id ";

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
                if (!empty($data['filter_no_return_status_id'])) {
			$implode[] = "r.return_status_id != '" . (int)$data['filter_no_return_status_id'] . "'";
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