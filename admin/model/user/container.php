<?php
class ModelUserContainer extends Model {
	public function addCustomer($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "', newsletter = '" . (int)$data['newsletter'] . "', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', status = '" . (int)$data['status'] . "', approved = '" . (int)$data['approved'] . "', safe = '" . (int)$data['safe'] . "', date_added = NOW()");

		$customer_id = $this->db->getLastId();

		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($address['firstname']) . "', lastname = '" . $this->db->escape($address['lastname']) . "', company = '" . $this->db->escape($address['company']) . "', address_1 = '" . $this->db->escape($address['address_1']) . "', address_2 = '" . $this->db->escape($address['address_2']) . "', city = '" . $this->db->escape($address['city']) . "', postcode = '" . $this->db->escape($address['postcode']) . "', country_id = '" . (int)$address['country_id'] . "', zone_id = '" . (int)$address['zone_id'] . "', custom_field = '" . $this->db->escape(isset($address['custom_field']) ? serialize($address['custom_field']) : '') . "'");

				if (isset($address['default'])) {
					$address_id = $this->db->getLastId();

					$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
				}
			}
		}
	}

	public function editContainer($container_id, $data) {
		
		$this->db->query("UPDATE " . DB_PREFIX . "x_container SET status = '" . (int)$data['container_status'] . "' WHERE container_id = '" . (int)$container_id . "'");

		
	}

        
        public function getContainerMoves($container_id){
            $query = $this->db->query("select *,cm.date_added as cm_date_added from " . DB_PREFIX . "x_container_move as cm left join oc_customer as c on cm.customer_id = c.customer_id left join oc_w_user  as u on u.user_id = cm.add_w_user_id where cm.container_id = '" . (int)$container_id . "' order by cm.container_move_id desc limit 50");
            return $query->rows;
		
        }
        
        
	public function editToken($customer_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function deleteCustomer($customer_id) {
        return;
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function getContainer($container_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "x_container WHERE container_id = '" . (int)$container_id . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getContainers($data = array()) {
            
                if (!is_null($data['filter_container_outdate']) || !is_null($data['filter_container_indate'])) {
                    $sql = "SELECT c.*, ct.type_name  FROM " . DB_PREFIX . "x_container c LEFT JOIN " . DB_PREFIX . "x_container_type ct ON (c.type = ct.type_id) LEFT JOIN " . DB_PREFIX . "x_container_move AS cm ON cm.container_id = c.container_id where 1=1 ";
                }
                else{
		$sql = "SELECT c.*, ct.type_name  FROM " . DB_PREFIX . "x_container c LEFT JOIN " . DB_PREFIX . "x_container_type ct ON (c.type = ct.type_id) where 1=1 ";
                }
		$implode = array();

		

		if (!empty($data['filter_container_id'])) {
			$implode[] = "c.container_id = " . $this->db->escape($data['filter_container_id']) . " ";
		}

		if (!is_null($data['filter_container_instore'])) {
			$implode[] = "c.instore = " . $this->db->escape($data['filter_container_instore']) . " ";
		}

		if (isset($data['filter_container_type']) && !is_null($data['filter_container_type'])) {
			$implode[] = "c.type = '" . (int)$data['filter_container_type'] . "'";
		}

		if (!is_null($data['filter_container_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_container_status'] . "'";
		}

                if (!is_null($data['filter_container_outdate'])) {
                        $implode[] = " cm.move_type = 1";
			$implode[] = " date_format(cm.date_added,'%Y-%m-%d') = '" . $data['filter_container_outdate'] . "'";
		}

                if (!is_null($data['filter_container_indate'])) {
                        $implode[] = " cm.move_type = '-1'";
			$implode[] = " date_format(cm.date_added,'%Y-%m-%d') = '" . $data['filter_container_indate'] . "'";
		}
                

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.approved',
			'c.ip',
			'c.date_added',
                        'container_id'
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

        public function getContainersW($data = array()) {
        
                $sql = "SELECT
                        cm.container_id,cm.date_added,ct.type_name,cu.firstname,cu.lastname,cm.order_id,cu.merchant_name,cu.merchant_address,cm.customer_id
                    FROM
                            oc_x_container_move AS cm
                    LEFT JOIN oc_x_container AS c ON c.container_id = cm.container_id
                    LEFT JOIN oc_x_container_type AS ct ON ct.type_id = c.type
                    left join oc_customer as cu on cu.customer_id = cm.customer_id 
                    where 1=1 
                ";
		$implode = array();
        
		

		if (isset($data['filter_container_type']) && !is_null($data['filter_container_type'])) {
			$implode[] = "c.type = '" . (int)$data['filter_container_type'] . "'";
		}

                
                
		if (!is_null($data['filter_container_outdate'])) {
			$implode[] = " date_format(cm.date_added,'%Y-%m-%d') = '" . $data['filter_container_outdate'] . "'";
		}
                
                
                if (!empty($data['filter_container_customer'])) {
			$implode[] = " CONCAT(cu.firstname, ' ', cu.lastname) LIKE '%" . $this->db->escape($data['filter_container_customer']) . "%'";
		}
                

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

                $sql .= "GROUP BY
	cm.order_id,
	cm.container_id
HAVING
	sum(cm.move_type) = 1";
                
                if (!empty($data['filter_container_days'])) {
			$sql .= " and date_format(cm.date_added,'%Y-%m-%d') <= '" . date("Y-m-d", time()+8*3600 - $this->db->escape($data['filter_container_days'])*24*3600) . "' ";
		}
                
		$sort_data = array(
			
                        'cm.container_id',
                        'cu.customer_id',
                        'cm.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cm.date_added";
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
				$data['limit'] = 100;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
                
		$query = $this->db->query($sql);

		return $query->rows;
	}

        
        public function getContainerNoMoveOrders(){
            $sql = "SELECT
            oi.*
            FROM
                    oc_order AS o
            LEFT JOIN oc_x_container_move AS cm ON cm.order_id = o.order_id
            left join oc_order_inv as oi on oi.order_id = o.order_id
            WHERE
                    o.order_status_id != 3
            AND o.deliver_date = '" . date("Y-m-d",  time()+8*3600) . "'
            AND cm.container_move_id IS NULL
            and oi.order_id is not null
            GROUP BY
                o.order_id";
                
            
            $query = $this->db->query($sql);
            $result = array();
            $result = $query->rows;
            
            $result_id_arr = array();
            $result_id_str = '';
            if(!empty($result)){
                foreach($result as $key=>$value){
                    $result_id_arr[] = $value['order_id'];
                }
            }
            $result_id_str = implode(",", $result_id_arr);
            return $result_id_str;
            
        }
        
        public function getContainerTypes($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "x_container_type";

		$sql .= " ORDER BY type_id";

		
                $sql .= " ASC";
		
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
        
        
        
	public function approve($customer_id) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET approved = '1' WHERE customer_id = '" . (int)$customer_id . "'");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
				$store_url = $store_info['url'] . 'index.php?route=account/login';
			} else {
				$store_name = $this->config->get('config_name');
				$store_url = HTTP_CATALOG . 'index.php?route=account/login';
			}

			$message  = sprintf($this->language->get('text_approve_welcome'), $store_name) . "\n\n";
			$message .= $this->language->get('text_approve_login') . "\n";
			$message .= $store_url . "\n\n";
			$message .= $this->language->get('text_approve_services') . "\n\n";
			$message .= $this->language->get('text_approve_thanks') . "\n";
			$message .= $store_name;

			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject(sprintf($this->language->get('text_approve_subject'), $store_name));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}


	public function getTotalContainers($data = array()) {

                if (!is_null($data['filter_container_outdate']) || !is_null($data['filter_container_indate'])) {
                    $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "x_container as c left join " . DB_PREFIX . "x_container_move as cm on cm.container_id = c.container_id";
                }
                else{
                    $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "x_container as c";
                }
		

		$implode = array();

		if (!empty($data['filter_container_id'])) {
			$implode[] = "c.container_id = " . $this->db->escape($data['filter_container_id']) . " ";
		}

		if (!is_null($data['filter_container_instore'])) {
			$implode[] = "c.instore = " . $this->db->escape($data['filter_container_instore']) . " ";
		}

		if (isset($data['filter_container_type']) && !is_null($data['filter_container_type'])) {
			$implode[] = "c.type = '" . (int)$data['filter_container_type'] . "'";
		}

		if (!is_null($data['filter_container_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_container_status'] . "'";
		}

		

		if (!is_null($data['filter_container_outdate'])) {
                        $implode[] = " cm.move_type = 1";
			$implode[] = " date_format(cm.date_added,'%Y-%m-%d') = '" . $data['filter_container_outdate'] . "'";
		}
                
                if (!is_null($data['filter_container_indate'])) {
                        $implode[] = " cm.move_type = '-1'";
			$implode[] = " date_format(cm.date_added,'%Y-%m-%d') = '" . $data['filter_container_indate'] . "'";
		}

		

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

        
        
        
	public function getTotalContainersW($data = array()) {
                
                $sql = " select count(container_id) as total from (";
                $sql .= "SELECT
	cm.order_id,cm.container_id,cm.date_added
FROM
	oc_x_container_move AS cm
LEFT JOIN oc_x_container AS c ON c.container_id = cm.container_id
LEFT JOIN oc_x_container_type AS ct ON ct.type_id = c.type
left join oc_customer as cu on cu.customer_id = cm.customer_id 
";
                

		$implode = array();

		
		
		if (isset($data['filter_container_type']) && !is_null($data['filter_container_type'])) {
			$implode[] = "c.type = '" . (int)$data['filter_container_type'] . "'";
		}

                
                
		if (!is_null($data['filter_container_outdate'])) {
			$implode[] = " date_format(cm.date_added,'%Y-%m-%d') = '" . $data['filter_container_outdate'] . "'";
		}
                
                
                if (!empty($data['filter_container_customer'])) {
			$implode[] = " CONCAT(cu.firstname, ' ', cu.lastname) LIKE '%" . $this->db->escape($data['filter_container_customer']) . "%'";
		}
		

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
                
                $sql .= "GROUP BY
	cm.order_id,
	cm.container_id
HAVING
	sum(cm.move_type) = 1";
                
                if (!empty($data['filter_container_days'])) {
			$sql .= " and date_format(cm.date_added,'%Y-%m-%d') <= '" . date("Y-m-d", time()+8*3600 - $this->db->escape($data['filter_container_days'])*24*3600) . "' ";
		}
                
                $sql .= ") as t";

                
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

        
        
        
        
        
	public function getTotalCustomersAwaitingApproval() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE status = '0' OR approved = '0'");

		return $query->row['total'];
	}

	public function getTotalAddressesByCustomerId($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByCountryId($country_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE country_id = '" . (int)$country_id . "'");

		return $query->row['total'];
	}

	public function getTotalAddressesByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE zone_id = '" . (int)$zone_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByCustomerGroupId($customer_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE customer_group_id = '" . (int)$customer_group_id . "'");

		return $query->row['total'];
	}

	public function addHistory($customer_id, $comment) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_history SET customer_id = '" . (int)$customer_id . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', date_added = NOW()");
	}

	public function getHistories($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT comment, date_added FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalHistories($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

        public function getTransactionType() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_transaction_type ");

		return $query->rows;
	}

        
        
	public function addTransaction($customer_id, $description = '', $amount = '', $order_id = 0,$customer_transaction_type_id = 1, $change_id=0, $return_id=0) {
            
            $customer_info = $this->getCustomer($customer_id);
        $user = $this->user->getId();

		if ($customer_info) {
                    
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET added_by = '". $user ."', customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', customer_transaction_type_id = '" . $customer_transaction_type_id . "', date_added = NOW(),change_id = " . $change_id . ", return_id = " . $return_id);

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

	public function deleteTransaction($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactions($customer_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalTransactions($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total  FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTransactionTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalTransactionsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function addReward($customer_id, $description = '', $points = '', $order_id = 0) {
		$customer_info = $this->getCustomer($customer_id);

		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', points = '" . (int)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");

			$this->load->language('mail/customer');

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
			$message .= sprintf($this->language->get('text_reward_total'), $this->getRewardTotal($customer_id));

			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($store_name);
			$mail->setSubject(sprintf($this->language->get('text_reward_subject'), $store_name));
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}

	public function deleteReward($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "' AND points > 0");
	}

	public function getRewards($customer_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "' ORDER BY date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalRewards($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($customer_id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomerRewardsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getIps($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->rows;
	}

	public function getTotalIps($customer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByIp($ip) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($ip) . "'");

		return $query->row['total'];
	}

	public function addBanIp($ip) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_ban_ip` SET `ip` = '" . $this->db->escape($ip) . "'");
	}

	public function removeBanIp($ip) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_ban_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");
	}

	public function getTotalBanIpsByIp($ip) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_ban_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");

		return $query->row['total'];
	}
	
	public function getTotalLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");

		return $query->row;
	}	

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");
	}		
        
        public function getOrderIds($customer_id){
            $sql = "select order_id from " . DB_PREFIX . "order where customer_id = " . $customer_id . " order by order_id desc";
            $query = $this->db->query($sql);
            $result = $query->rows;
            $return = array();
            if(!empty($result)){
                foreach($result as $key=>$value){
                    $return[] = $value['order_id'];
                }
            }
            return $return;
        }
}