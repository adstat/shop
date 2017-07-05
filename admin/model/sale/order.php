<?php
class ModelSaleOrder extends Model {
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT * ,(SELECT name FROM ".DB_PREFIX."order_payment_status ops WHERE ops.order_payment_status_id = o.order_payment_status_id AND ops.language_id = 2) payment_status, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$reward = 0;

			$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

			foreach ($order_product_query->rows as $product) {
				$reward += $product['reward'];
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			if ($order_query->row['affiliate_id']) {
				$affiliate_id = $order_query->row['affiliate_id'];
			} else {
				$affiliate_id = 0;
			}

			$this->load->model('marketing/affiliate');

			$affiliate_info = $this->model_marketing_affiliate->getAffiliate($affiliate_id);

			if ($affiliate_info) {
				$affiliate_firstname = $affiliate_info['firstname'];
				$affiliate_lastname = $affiliate_info['lastname'];
			} else {
				$affiliate_firstname = '';
				$affiliate_lastname = '';
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
				$language_directory = $language_info['directory'];
			} else {
				$language_code = '';
				$language_directory = '';
			}

            $order_account_query = $this->db->query("SELECT round(sum(value),2) order_due, round(sum(if(code='user_point',value,0)),2) user_point_paid, round(sum(if(code='wxpay',value,0)),2) wxpay_paid  FROM ".DB_PREFIX."order_total WHERE accounting = 1 AND order_id = '".(int)$order_id."'");
            
            if($order_account_query){
                $order_due_total =  $order_account_query->row['order_due'];
                $user_point_paid = $order_account_query->row['user_point_paid'];
                $wxpay_paid = $order_account_query->row['wxpay_paid'];
            }
            else{
                $order_due_total = 0;
                $user_point_paid = 0;
                $wxpay_paid = 0;
            }

            
            
            $sql = "SELECT r.order_id, sum(rp.return_product_credits) AS return_credits FROM oc_return AS r LEFT JOIN oc_return_product AS rp ON r.return_id = rp.return_id WHERE r.order_id = " . (int)$order_id . " and r.return_status_id = 2 and r.return_credits = 0 GROUP BY r.order_id";
            
            $query = $this->db->query($sql);
            $unpaid_return_order = $query->row;
            if(!empty($unpaid_return_order)){
                //判断有没有四舍五入调整过，如果有则需先调整为四舍五入前金额
                $order_account_query2 = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE code = 'total_adjust' AND order_id = '".(int)$order_id."'");
                $order_due_total =  $order_due_total - (isset($order_account_query2->row['value']) ? $order_account_query2->row['value'] : 0 );
                
                $order_due_total = $order_due_total - $unpaid_return_order['return_credits'] > 0 ?  $order_due_total - $unpaid_return_order['return_credits'] : 0;
            }
            //查询出库退货
			$sql = "SELECT r.order_id, sum(rp.return_product_credits) AS return_credits FROM oc_return AS r LEFT JOIN oc_return_product AS rp ON r.return_id = rp.return_id WHERE r.order_id = " . (int)$order_id . " and r.return_status_id = 2 and r.return_credits > 0 GROUP BY r.order_id";

			$query = $this->db->query($sql);

			$paid_return_order = $query->row;

			return array(
				'orderid'                => $order_query->row['orderid'],
				'type'                => $order_query->row['type'],
				'order_id'                => $order_query->row['order_id'],
				'station_id'                => $order_query->row['station_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'customer'                => $order_query->row['customer'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'email'                   => $order_query->row['email'],
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'custom_field'            => unserialize($order_query->row['custom_field']),
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_custom_field'    => unserialize($order_query->row['payment_custom_field']),
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_phone'          => $order_query->row['shipping_phone'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field'   => unserialize($order_query->row['shipping_custom_field']),
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'reward'                  => $reward,
				'order_status_id'         => $order_query->row['order_status_id'],
				'affiliate_id'            => $order_query->row['affiliate_id'],
				'affiliate_firstname'     => $affiliate_firstname,
				'affiliate_lastname'      => $affiliate_lastname,
				'commission'              => $order_query->row['commission'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'language_directory'      => $language_directory,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'],
				'user_agent'              => $order_query->row['user_agent'],
				'accept_language'         => $order_query->row['accept_language'],
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified'],

				'shipping_method'           => $order_query->row['shipping_method'],
				'shipping_name'           => $order_query->row['shipping_name'],
				'deliver_date'           => $order_query->row['deliver_date'],
				'pickupspot_id'           => $order_query->row['pickupspot_id'],
				'order_status_id'           => $order_query->row['order_status_id'],
				'order_deliver_status_id'           => $order_query->row['order_deliver_status_id'],
				'order_payment_status_id'           => $order_query->row['order_payment_status_id'],
				'payment_method'           => $order_query->row['payment_method'],
				'payment_status'           => $order_query->row['payment_status'],
				'order_due_total'          => $order_due_total,
                    'user_point_paid'      => $user_point_paid,
                    'wxpay_paid'           => $wxpay_paid,
                'order_return_outofstock' => isset($unpaid_return_order['return_credits']) ? $unpaid_return_order['return_credits'] : 0,
				'order_return_fromcustomer' => isset($paid_return_order['return_credits']) ? $paid_return_order['return_credits'] : 0,

				'sub_total'           => $order_query->row['sub_total'],
				'discount_total'           => $order_query->row['discount_total'],
				'shipping_fee'           => $order_query->row['shipping_fee'],
				'balance_container_deposit'           => $order_query->row['balance_container_deposit'],
				'credit_pay'           => $order_query->row['credit_pay'],
				'credit_paid'           => $order_query->row['credit_paid']




			);
		} else {
			return;
		}
	}

	public function getOrders($data = array()) {
		$sql = "SELECT o.orderid, o.type, o.order_id, o.station_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, o.comment, oxd.firstorder,o.order_payment_status_id,
		(SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status,
		(SELECT od.name FROM " . DB_PREFIX . "order_deliver_status od WHERE od.order_deliver_status_id = o.order_deliver_status_id AND od.language_id = '" . (int)$this->config->get('config_language_id') . "') AS deliver_status,
		(SELECT op.name FROM " . DB_PREFIX . "order_payment_status op WHERE op.order_payment_status_id = o.order_payment_status_id AND op.language_id = '" . (int)$this->config->get('config_language_id') . "') AS payment_status,
		(SELECT concat(ps.name,'-',ps.address) FROM " . DB_PREFIX . "x_pickupspot ps WHERE ps.pickupspot_id = o.pickupspot_id) AS pickupspot,
		o.shipping_code,o.bd_id, o.total, o.sub_total, o.discount_total, o.shipping_fee, o.balance_container_deposit, o.credit_paid, o.currency_code, o.currency_value, o.date_added, o.date_modified,
		o.pickupspot_id, o.deliver_date, o.shipping_method, o.shipping_phone, o.payment_method, o.payment_code, o.shipping_firstname, o.shipping_address_1,
		o.order_status_id, o.order_deliver_status_id, o.order_payment_status_id,o.station_id,
		A.logistic_allot_id, B.logistic_driver_id, B.logistic_driver_title, B.logistic_driver_phone,
		o.order_print_status,
        concat(left(ps.name,4),'*') pspot_short_name,  ps.name pspot_name, o.customer_group_id, o.is_nopricetag, bd.bd_name, bd.phone bd_phone, ocg.shortname group_shortname
		FROM `" . DB_PREFIX . "order` o
		LEFT JOIN oc_x_pickupspot ps ON o.pickupspot_id = ps.pickupspot_id
		LEFT JOIN oc_x_bd bd ON o.bd_id = bd.bd_id
		LEFT JOIN oc_order_extend oxd ON o.order_id = oxd.order_id
		LEFT JOIN oc_customer_group ocg on o.customer_group_id = ocg.customer_group_id
		LEFT JOIN oc_x_logistic_allot_order A ON o.order_id = A.order_id
		LEFT JOIN oc_x_logistic_allot B ON A.logistic_allot_id = B.logistic_allot_id
		";

		if (isset($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			} else {

			}
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

        if (!empty($data['filter_order_payment_status'])) {
            $sql .= " AND o.order_payment_status_id = '" . (int)$data['filter_order_payment_status'] . "'";
        }

        if (!empty($data['filter_order_deliver_status'])) {
            $sql .= " AND o.order_deliver_status_id = '" . (int)$data['filter_order_deliver_status'] . "'";
        }

        if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

        if (!empty($data['filter_station'])) {
			$sql .= " AND o.station_id = '" . (int)$data['filter_station'] . "'";
		}

        if (!empty($data['filter_driver'])) {
			$sql .= " AND B.logistic_driver_id = '" . (int)$data['filter_driver'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			//$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
                        $sql .= " AND (CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%' or o.customer_id = '" . $this->db->escape($data['filter_customer']) . "' or o.telephone like '%" . $this->db->escape($data['filter_customer']) . "%' or o.shipping_phone like '%" . $this->db->escape($data['filter_customer']) . "%')";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_deliver_date'])) {
			$sql .= " AND DATE(o.deliver_date) = DATE('" . $this->db->escape($data['filter_deliver_date']) . "')";
		}

		if (!empty($data['filter_customer_group'])) {
			$sql .= " AND o.customer_group_id = '" . (int)$data['filter_customer_group'] . "'";
		}

		if (!empty($data['filter_warehouse_id_global'])) {
			$sql .= " AND o.warehouse_id = '" . (int)$data['filter_warehouse_id_global'] . "'";
		}

        $sql .= " AND o.shipping_code IN ('D2D','PSPOT') GROUP BY o.order_id ";

		$sort_data = array(
			'o.order_id',
			'customer',
			'status',
			'o.date_added',
			'o.date_modified',
			'o.deliver_date',
			'o.total'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
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

            //指定司机时列出指定日期改司机配送的所有订单(
            if(!empty($data['filter_deliver_date']) && !empty($data['filter_driver'])){
                $data['limit'] = 100;
            }

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

        $orders = $query->rows;
                
        //应收金额需减去退货金额
        if(!empty($orders)){
            $unpaid_order_ids = array();
            $unpaid_order_id_str = '';
            foreach($orders as $key=>$value){

                //查找支付信息
                $order_account_query = $this->db->query("SELECT round(sum(value),2) order_due, round(sum(if(code='user_point',value,0)),2) user_point_paid, round(sum(if(code='wxpay',value,0)),2) wxpay_paid  FROM ".DB_PREFIX."order_total WHERE accounting = 1 AND order_id = '".(int)$value['order_id']."'");

                if($order_account_query){
                    $user_point_paid = $order_account_query->row['user_point_paid'];
                    $wxpay_paid = $order_account_query->row['wxpay_paid'];
                }
                else{
                    $user_point_paid = 0;
                    $wxpay_paid = 0;
                }

                $orders[$key]['user_point_paid'] = $user_point_paid;
                $orders[$key]['wxpay_paid'] = $wxpay_paid;

                $orders[$key]['need_pay'] = $value['total'] - abs($user_point_paid) - abs($wxpay_paid);
                if($value['payment_code'] == 'COD' || $value['order_payment_status_id'] == 1){
                    $unpaid_order_ids[$value['order_id']] = $key;
                }
            }

            if(!empty($unpaid_order_ids)){
                $unpaid_order_id_str = implode(",", array_keys($unpaid_order_ids));

                $sql = "SELECT r.order_id, sum(rp.return_product_credits) AS return_credits FROM oc_return AS r LEFT JOIN oc_return_product AS rp ON r.return_id = rp.return_id WHERE r.order_id IN (" . $unpaid_order_id_str . ") and r.return_status_id = 2 and r.return_credits = 0 GROUP BY r.order_id";
                $query = $this->db->query($sql);
                $unpaid_return_orders = $query->rows;
                if(!empty($unpaid_return_orders)){
                    foreach($unpaid_return_orders as $k => $v){

                        //判断有没有四舍五入调整过，如果有则需先调整为四舍五入前金额
                        $order_account_query2 = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE code = 'total_adjust' AND order_id = '".(int)$v['order_id']."'");
                        $orders[$unpaid_order_ids[$v['order_id']]]['need_pay'] =  $orders[$unpaid_order_ids[$v['order_id']]]['need_pay'] - (isset($order_account_query2->row['value']) ? $order_account_query2->row['value'] : 0 );



                        $orders[$unpaid_order_ids[$v['order_id']]]['need_pay'] = $orders[$unpaid_order_ids[$v['order_id']]]['need_pay'] - $v['return_credits'];
                        $orders[$unpaid_order_ids[$v['order_id']]]['need_pay'] = $orders[$unpaid_order_ids[$v['order_id']]]['need_pay'] < 0 ? 0 : $orders[$unpaid_order_ids[$v['order_id']]]['need_pay'];
                    }
                }
            }

        }

		return $orders;
	}

        
        public function getBds(){
            $sql = "select bd_id,bd_name from " . DB_PREFIX . "x_bd where status = 1 order by bd_id asc";
            $query = $this->db->query($sql);

            return $query->rows;
        }
        
	public function getOrderProducts($order_id) {
        $sql = "SELECT A.order_product_id, A.order_id, A.product_id, A.name, A.model, A.quantity, A.price, A.total, A.tax, A.reward, A.price_ori, A.is_gift, A.shipping, A.status,
                    C.name cate_name, B.category_id, P.class, PC.print_name inv_class_name,P.weight,A.weight_inv_flag,P.shelf_life,P.weight_range_least,P.weight_range_most,P.inv_size   
                     FROM oc_order_product A
                     LEFT JOIN oc_product P ON A.product_id = P.product_id
                     LEFT JOIN oc_product_inv_class PC on P.inv_class = PC.product_inv_class_id
                     LEFT JOIN (select * from oc_product_to_category group by product_id) B ON A.product_id = B.product_id
                     LEFT JOIN oc_category_description C ON B.category_id = C.category_id and C.language_id = 2
                     WHERE A.order_id = '".(int)$order_id."'
                     ORDER BY P.inv_class ASC, B.category_id, A.product_id";
		$query = $this->db->query($sql);

		return $query->rows;
	}

        
        public function getOrderProductsWeightInv($order_id) {
        $sql = "SELECT  A.product_id,  A.quantity, A.weight_total, A.total
                     FROM oc_order_product_weight_inv as A
                     WHERE A.order_id = '".(int)$order_id."'";
		$query = $this->db->query($sql);

		return $query->rows;
	}

    public function getOrderInvProducts($order_id){
        $sql = "SELECT
	xsm.order_id,xsmi.product_id,xsmi.quantity
FROM
	oc_x_stock_move AS xsm
LEFT JOIN oc_x_stock_move_item AS xsmi ON xsm.inventory_move_id = xsmi.inventory_move_id
WHERE
	xsm.order_id = " . $order_id . "
AND xsm.inventory_type_id = 12";
        $query = $this->db->query($sql);

        return $query->rows;
    }
    
	public function getOrderOption($order_id, $order_option_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_option_id = '" . (int)$order_option_id . "'");

		return $query->row;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	public function getOrderVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrderVoucherByVoucherId($voucher_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE voucher_id = '" . (int)$voucher_id . "'");

		return $query->row;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}

        if (!empty($data['filter_order_payment_status'])) {
            $sql .= " AND order_payment_status_id = '" . (int)$data['filter_order_payment_status'] . "'";
        }

        if (!empty($data['filter_order_deliver_status'])) {
            $sql .= " AND order_deliver_status_id = '" . (int)$data['filter_order_deliver_status'] . "'";
        }


        if (!empty($data['filter_order_id'])) {
			$sql .= " AND order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_warehouse_id_global'])) {
			$sql .= " AND warehouse_id = '" . (int)$data['filter_warehouse_id_global'] . "'";
		}

                if (!empty($data['filter_station'])) {
			$sql .= " AND station_id = '" . (int)$data['filter_station'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND (CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%' or customer_id = '" . $this->db->escape($data['filter_customer']) . "' or telephone like '%" . $this->db->escape($data['filter_customer']) . "%' or shipping_phone like '%" . $this->db->escape($data['filter_customer']) . "%')";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

        if (!empty($data['filter_deliver_date'])) {
            $sql .= " AND DATE(deliver_date) = DATE('" . $this->db->escape($data['filter_deliver_date']) . "')";
        }

		if (!empty($data['filter_total'])) {
			$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalOrdersByStoreId($store_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE store_id = '" . (int)$store_id . "'");

		return $query->row['total'];
	}

    public function getProduceGroup() {
        $query = $this->db->query("SELECT produce_group_id, title, memo FROM oc_x_produce_group WHERE status = 1");

        return $query->rows;
    }

	public function getTotalOrdersByOrderStatusId($order_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$order_status_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByProcessingStatus() {
		$implode = array();

		$order_statuses = $this->config->get('config_processing_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode));

			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalOrdersByCompleteStatus() {
		$implode = array();

		$order_statuses = $this->config->get('config_complete_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode) . "");

			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalOrdersByLanguageId($language_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE language_id = '" . (int)$language_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByCurrencyId($currency_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE currency_id = '" . (int)$currency_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

    public function getOrderInvData($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_inv` WHERE order_id = '" . (int)$order_id . "'");

		return $query->row;
	}

	public function createInvoiceNo($order_id) {
		$order_info = $this->getOrder($order_id);

		if ($order_info && !$order_info['invoice_no']) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");

			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");

			return $order_info['invoice_prefix'] . $invoice_no;
		}
	}

    public function getFeadbacks($order_id){

		$sql = "SELECT
		    A.order_id,
		    B.name, E.date_added, E.shipping_name, E.total, F.bd_name,D.logistic_driver_title,
		    FB.name feadback_options, A.comments,A.user_comments,A.logistic_score,A.cargo_check,A.bill_of,A.box, A.date_added record_date
		FROM oc_x_order_feadback A
		LEFT JOIN oc_x_feadback_type FB on A.feadback_id = FB.feadback_id
		LEFT JOIN  oc_x_station B ON A.station_id = B.station_id
		LEFT JOIN oc_x_logistic_allot_order C ON C.order_id = A.order_id
		LEFT JOIN oc_x_logistic_allot D ON D.logistic_allot_id = C.logistic_allot_id
		LEFT JOIN  oc_order E ON E.order_id = A.order_id
		LEFT JOIN  oc_x_bd F ON  E.bd_id = F.bd_id WHERE A.order_id = ' $order_id'";
        $query = $this->db->query($sql);
        return $query->rows;
    }

	public function getOrderHistories($order_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT oh.date_added, os.name AS status, ds.name AS deliver_status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id LEFT JOIN " . DB_PREFIX . "order_deliver_status ds ON ds.order_deliver_status_id = oh.order_deliver_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalOrderHistories($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrderHistoriesByOrderStatusId($order_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int)$order_status_id . "'");

		return $query->row['total'];
	}

	public function getEmailsByProductsOrdered($products, $start, $end) {
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}

		$query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0' LIMIT " . (int)$start . "," . (int)$end);

		return $query->rows;
	}

	public function getTotalEmailsByProductsOrdered($products) {
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}

		$query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");

		return $query->row['total'];
	}

    public function getOrderDeliverStatus() {
        $query = $this->db->query("SELECT order_deliver_status_id id,name FROM " . DB_PREFIX . "order_deliver_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->rows;
    }

    public function getOrderPaymentStatus() {
        $query = $this->db->query("SELECT order_payment_status_id id,name FROM " . DB_PREFIX . "order_payment_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->rows;
    }

	public function getOrderPaymentStatusId($order_id){
		$sql = "SELECT order_payment_status_id id from oc_order WHERE order_id = '". (int)$order_id ."'";

		$query = $this->db->query($sql);

		return $query->row['id'];
	}

    public function NOUSE_getOrderPaymentStatues() {
        $query = $this->db->query("SELECT order_payment_status_id,name FROM " . DB_PREFIX . "order_payment_status WHERE language_id = '" . (int) $this->config->get('config_language_id') . "' order by order_payment_status_id asc");

        return $query->rows;
    }
    public function getProductInvclasses() {
        $query = $this->db->query("SELECT product_inv_class_id,name FROM " . DB_PREFIX . "product_inv_class order by product_inv_class_id asc");

        return $query->rows;
    }
    
    public function getOrderInventoryProduct($order_id){
        /*
        $sql = "SELECT
	op.order_id,SUM(op.quantity) as quantity   
FROM
	oc_order_product AS op
WHERE op.order_id in (" . $order_id_str . ") group by op.order_id";
        $query = $this->db->query($sql);
        $results = $query->rows;
        $orders = array();
        foreach($results as $key=>$value){
            $orders[$value['order_id']]['order_quantity'] = $value['quantity'];
            $orders[$value['order_id']]['has_ooc'] = 0;
}
        */
        
        
        $orders = array();
        $stock_result = array();
        $sql = "SELECT
	sm.order_id,
        smi.product_id,
	sum(smi.quantity) as stock_quantity,
        sum(smi.weight) as stock_weight 
FROM
	oc_x_stock_move AS sm
LEFT JOIN oc_x_stock_move_item AS smi ON sm.inventory_move_id = smi.inventory_move_id
WHERE
	sm.order_id = " . $order_id . "
AND sm.inventory_type_id = 12
GROUP BY
	smi.product_id";
        
        $query = $this->db->query($sql);
        $stock_result = $query->rows;
        
        if(!empty($stock_result)){
            foreach($stock_result as $srk=>$srv){
                $orders[$srv['product_id']]['stock_quantity'] = $srv['stock_quantity'];
                $orders[$srv['product_id']]['stock_weight'] = $srv['stock_weight'];
                
            }
        }
        
        return $orders;
    }
    
    public function getContainerMove($order_id){
            
            $sql = "SELECT
                        f.container_id,f.type,fl.order_id,ct.type_name,fl.date_added,o.shipping_firstname,o.shipping_address_1
                    FROM
                        oc_x_container_move as fl
                    left join oc_x_container as f on f.container_id = fl.container_id
                    left join oc_x_container_type as ct on ct.type_id = f.type
                    left join oc_order as o on o.order_id = fl.order_id
                    WHERE
                        fl.order_id = " . $order_id . "
                    GROUP BY
                        container_id
                    HAVING
                        sum(move_type) = 1
                    order by fl.container_move_id desc
                    ";
            $query = $this->db->query($sql);
            $result = array();
            $result = $query->rows;
            
            return $result;
    }

    public function getFastMoveOrderSortingIndex($order_id){
        $indexStart = FAST_MOVE_ORDER_SORTING_INDEX_START;

        $query = $this->db->query("select deliver_date,station_id from oc_order where order_id = '".$order_id."'");
        $deliverDate = $query->row['deliver_date'];
        if($query->row['station_id'] == STATION_FRESH){
            return 0;
        }

        $query = $this->db->query("select min(order_id) order_id from oc_order where station_id = '".STATION_FAST_MOVE."' and deliver_date  = '".$deliverDate."'");
        $minOrderId = $query->row['order_id'];


        $query = $this->db->query("select count(order_id) freshOrders from oc_order where station_id = '".STATION_FRESH."' and deliver_date = '".$deliverDate."' and order_id between ".$minOrderId." and ". $order_id);
        $withinFreshOrders = $query->row['freshOrders'];

        return $indexStart + (int)$order_id - (int)$minOrderId - (int)$withinFreshOrders;
    }

    public function getOrderSortingIndexList($deliver_date,$station_id,$indexStart, $order_id = false){
        //获取订单的分拣序号, 若已指定order_id, 获取该订单在当天的分拣序号，
        // 注意，按次方法订单的配送时间不可更改 !!!
        $indexStart = $indexStart ? $indexStart : 501;
        $deliver_date = $deliver_date ? $deliver_date : 0;
        $station_id = $station_id ? $station_id : 0;

        if($order_id && is_numeric($order_id)){
            $query = $this->db->query("select deliver_date,station_id from oc_order where order_id = '".$order_id."'");
            $deliver_date = $query->row['deliver_date'];
            $station_id = $query->row['station_id'];
        }

        $query = $this->db->query("select order_id from oc_order where station_id = '".$station_id."' and deliver_date  = '".$deliver_date."' order by order_id asc");
        $orderList = $query->rows;
        $indexList = array();
        foreach($orderList as $order){
            $indexList[$order['order_id']] = $indexStart++;
        }

        if($order_id && is_numeric($order_id)){
            return $indexList[$order_id];
        }

        return $indexList;
    }

    public function getOrderFeadback($order_ids){
        $sql = "SELECT  A.order_id, A.station_id, E.logistic_driver_id, E.logistic_driver_title
        FROM oc_order A
        LEFT JOIN  oc_x_logistic_allot_order D ON A.order_id = D.order_id
        LEFT JOIN  oc_x_logistic_allot E ON E.logistic_allot_id = D.logistic_allot_id WHERE A.order_id = ' $order_ids '";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function addOrderFeadback($targetTable,$rowData){
        $sql = "INSERT INTO " . $targetTable . " SET ";
        foreach ($rowData as $k => $v) {
            $sql .= $k . "='" . $v . "',";

        }
        $sql .= 'date_added=NOW()';
        $this->db->query($sql);
        return $this->db->getLastId();
    }


	public function feadbacktype(){
		$sql = "SELECT feadback_id ,name FROM oc_x_feadback_type WHERE feadback_id not in(6,9)";
		$query = $this->db->query($sql);
		return $query->rows;
		//var_dump($query);
	}

	public function getPreDeliverShortInfo($order_id){
        //出库单仅显示类型1（缺货未出库）和类型5（出库未找到）
		$sql = "select r.order_id,rp.product_id,sum(rp.return_product_credits) as sum_credits
			from oc_return r
			left join oc_return_product rp on rp.return_id = r.return_id
			where
			r.order_id = '".$order_id."' and r.return_reason_id in (1,5) and r.return_status_id = 2
			group by r.order_id";
		$query = $this->db->query($sql);
		if($query->num_rows){
			return $query->rows;
		}else{
			return 0;
		}
	}

    //TODO 重做getReturnInfo
    public function getReturnInfo($order_id){
        //出库单仅显示类型1（缺货未出库）和类型5（出库未找到）
        $sql = "select rp.product_id,
                sum(rp.quantity) return_quantity, rp.price, rp.total,
                sum(if(r.return_reason_id in(1,5),rp.quantity,0)) return_quantity,
                sum(if(r.return_reason_id=5,rp.quantity,0)) deliver_return_quantity,
                sum(if(r.return_reason_id=3,rp.quantity,0)) back_missing_quantity,
                sum(if(r.return_reason_id in (2,4) and rp.in_part=0, rp.quantity,0)) customer_return_box_quantity,
                sum(if(r.return_reason_id in (2,4) and rp.in_part=1, rp.quantity,0))/max(rp.box_quantity) customer_return_part_quantity_flag,
                concat(sum(if(r.return_reason_id in (2,4) and rp.in_part=1, rp.quantity,0)), '/',max(rp.box_quantity)) customer_return_part_quantity
                from oc_return r
                left join oc_return_product rp on rp.return_id = r.return_id
                where r.order_id = '".$order_id."' and r.return_status_id = 2
                group by rp.product_id
                ";
        $query = $this->db->query($sql);

        if($query->num_rows){
            $return = array();
            foreach($query->rows as $m){
                $return[$m['product_id']] = $m;
            }
        }else{
            $return = false;
        }

        return $return;
    }

	/*分离order控制层中的业务代码到模型*/
	//获取订单状态
	public function getOrderStatus(){
		$sql = "SELECT order_status_id, `name` status_name FROM oc_order_status WHERE language_id = 2";
		$query = $this->db->query($sql);
		$orderStatusRaw = $query->rows;

		$orderStatus = array();
		foreach ($orderStatusRaw as $val) {
			$orderStatus[$val['order_status_id']] = $val['status_name'];
		}

		return $orderStatus;
	}

	public function getCurrentOrderStatus($order_id){
		$sql = "SELECT o.order_id, o.order_status_id FROM oc_order o WHERE o.order_id = '" . $order_id . "'";
		$query = $this->db->query($sql);
		$currentStatus = $query->row;

		return $currentStatus;
	}

	public function updateOrderStatus($status_id,$order_id){
		$sql = "update oc_order set order_status_id = '{$status_id}' where order_id = '{$order_id}'";

		return $this->db->query($sql);
	}

	public function cancelCouponUsed($order_id){
		$sql = "update oc_coupon_history set status = '0' where order_id = '".$order_id."'";

		return $this->db->query($sql);
	}

	public function orderStatusAllowes($status_id,$order_id,$currentStatus){
		$sql = "update oc_order set order_status_id = '{$status_id}' where order_id = '{$order_id}'";
		$bool = true;
		$bool = $bool && $this->db->query($sql);


		// 取消订单，执行库存操作
		if ($status_id == CANCELLED_ORDER_STATUS && $currentStatus['order_status_id'] !== CANCELLED_ORDER_STATUS) {
			//TODO退余额退款操作

			//取消使用优惠券记录
			$sql = "update oc_coupon_history set status = '0' where order_id = '".$order_id."'";
			$bool = true;
			$bool = $bool && $this->db->query($sql);

			//查找是否已有库存扣减记录，如有添加库存增加记录
			$sql = "INSERT INTO `oc_x_inventory_move` (`station_id`, `date`, `timestamp`, `from_station_id`, `to_station_id`, `order_id`, `inventory_type_id`, `date_added`, `status`)
                    select A.station_id, current_date() date, unix_timestamp(now()) timestamp, 0 from_station_id, 0 to_station_id, A.order_id, " . INVENTORY_TYPE_ORDER_CANCEL . " inventory_type_id, now() date_added, 1 status
                    from oc_order A
                    inner join oc_x_inventory_move B on A.order_id = B.order_id and inventory_type_id = '" . INVENTORY_TYPE_ORDERED . "'
                    where A.order_id = '" . $order_id . "'";
			$bool = $bool && $this->db->query($sql);
			//$inventory_move_id = $dbm->getLastId();
			//按照添加的，后天订单设置状态为0, 不参与实时库存计算
			$sql = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `order_id`, `customer_id`, `product_id`, `quantity`, `status`)
                    select A.inventory_move_id, A.station_id, B.order_id, B.customer_id, C.product_id, C.quantity quantity, if(B.deliver_date = date_add(date(B.date_added), interval 1 day), 1, 0) status
                    from oc_x_inventory_move A
                    left join oc_order B on A.order_id = B.order_id
                    left join oc_order_product C on B.order_id = C.order_id
                    where A.order_id = '" . $order_id . "' and A.inventory_type_id = '" . INVENTORY_TYPE_ORDER_CANCEL . "'
                    ";
			$bool = $bool && $this->db->query($sql);

			//现在需处理已支付的订单，如果订单为已支付，则需要退余额操作
			$sql = "select order_payment_status_id from oc_order where order_id = ".(int)$order_id;

			$query = $this->db->query($sql);

			if($query->row['order_payment_status_id'] == 2){
				$this->addTransaction($order_id,$status_id);
			}
		}

		return $bool;
	}

	public function addMsg($status_id,$order_id){
		$sql = "
                    INSERT INTO `oc_msg` (`merchant_id`, `customer_id`, `phone`, `order_id`, `msg_param_1`, `msg_param_2`, `isp_template_id`, `msg_type`, `msg_status_id`,`msg_status_name`,`sent`, `status`, `date_added`)
                    SELECT 0, o.customer_id, o.shipping_phone, " . $order_id . ", '" . $order_id . "', st.contact_phone, mt.isp_template_id, mt.msg_type, o.order_status_id, os.name, 0, 1, NOW()
                    FROM oc_order o
                    LEFT JOIN oc_order_status os ON o.order_status_id = os.order_status_id AND os.language_id = 2
                    LEFT JOIN oc_x_station st ON o.station_id = st.station_id
                    LEFT JOIN oc_msg_template mt ON os.msg_template_id = mt.msg_template_id
                    LEFT JOIN oc_customer c ON o.customer_id = c.customer_id
                    WHERE
                    o.order_id = '" . $order_id . "' AND o.order_status_id = '" . $status_id . "'
                    AND os.msg = 1 AND c.accept_order_message = 1
                    ";
		$this->db->query($sql);
	}

	//处理余额,如若用户没有要求退现，则全部退到余额里，否则分开处理本单余额支付和微信支付的钱
	private function addTransaction($order_id,$status_id){

		$sql = "select abs(sum(if(ot.code = 'credit_paid', ot.value, 0))) credit_paid, abs(sum(if(ot.code = 'wxpay' ,ot.value, 0))) wxpay_paid
			from oc_order_total ot
			where ot.order_id = '".(int) $order_id ."'
			group by ot.order_id";

		$results = $this->db->query($sql)->rows;

		$wxpay_paid = isset($results[0]['wxpay_paid']) ? $results[0]['wxpay_paid'] : 0;

		$credit_paid = isset($results[0]['credit_paid']) ? $results[0]['credit_paid'] : 0;

		$sql = "select customer_id from oc_order where order_id = '".(int) $order_id ."'";

		$results = $this->db->query($sql)->row;

		$customer_id = $results['customer_id'];

		switch($status_id){
			case 5:
				$description = '取消分拣中的订单['.$order_id.']';
				break;
			case 6:
				$description = '取消已拣完的订单['.$order_id.']';
				break;
			default :
				$description = '取消订单';
				break;
		}

		$this->db->query('START TRANSACTION');

		$bool = 1;


		$amount = $credit_paid + $wxpay_paid;

		//全部退还到余额，此时还需检查该订单是否发生过分拣缺货，并且已退还该客户余额的操作
//		$sql = "select return_id,return_credits from oc_return where order_id = '".(int)$order_id."' and  return_reasono_id = 1 and return_status_id = 2 and credits_returned = 1";
		$sql = "select amount from oc_customer_transaction where order_id = '".(int)$order_id."' and customer_id = '".(int)$customer_id."' and  customer_transaction_type_id = 9";
		$query = $this->db->query($sql);
		if($query->num_rows){

			$amount_b = $query->row['amount'];
			$sql ="INSERT INTO oc_customer_transaction (`customer_id`,`order_id`,`customer_transaction_type_id`,`description`,`amount`,`date_added`,`added_by`)
			VALUES ('".(int) $customer_id ."','".(int) $order_id."','14','扣除该订单分拣缺货返还给用户的积分','".$amount_b."',now(),'".(int) $this->user->getId() ."')";

			$bool = $bool && $this->db->query($sql);
		}

		$sql = "INSERT INTO oc_customer_transaction (`customer_id`,`order_id`,`customer_transaction_type_id`,`description`,`amount`,`date_added`,`added_by`)
			VALUES ('".(int) $customer_id ."','".(int) $order_id."','4','". $description ."','".$amount."',now(),'".(int) $this->user->getId() ."')";

		$bool = $bool && $this->db->query($sql);

		//检查客户是否使用了积分支付，如果使用了，则需要返还积分
		$sql = "select customer_id, order_id, abs(points) points from oc_customer_reward where order_id = '".(int)$order_id."' and reward_id = 6";

		$query = $this->db->query($sql);

		if($query->num_rows){
			$points = $query->row['points'];
			$sql = "INSERT INTO oc_customer_reward (`customer_id`,`order_id`,`description`,`points`,`date_added`,`reward_id`,`add_by`)
				VALUES ('".(int) $customer_id."','".(int) $order_id."','取消订单返还积分','".$points."',now(),7,'".(int) $this->user->getId() ."')";
			$this->db->query($sql);
		}

		if($bool){
			$this->db->query('COMMIT');
		}
		else{
			$this->db->query("ROLLBACK");
		}

		return $bool;

	}

	//处理仓库库存,返还分拣出库的商品库存
	private function dealStockinventory($order_id){
		//已拣完的订单被取消被视作整单退，不做退货表，
		//如果分拣过程中出现出库缺货，则作废该条退货数据，
		//在该订单为已支付的情况下，扣除之前返还给客户的余额

		$bool =true;
		//查找是否已有库存扣减记录，如有添加库存增加退库记录
		$sql = "INSERT INTO `oc_x_stock_move` (`station_id`, `timestamp`, `from_station_id`, `to_station_id`, `order_id`, `inventory_type_id`, `date_added`)
                    select A.station_id, unix_timestamp(now()) timestamp, 0 from_station_id, 0 to_station_id, A.order_id, " . INVENTORY_TYPE_ORDER_STOCK_RETURN . " inventory_type_id, now() date_added
                    from oc_order A
                    inner join oc_x_stock_move B on A.order_id = B.order_id and inventory_type_id = '" . INVENTORY_TYPE_ORDER_STOCK_OUT . "'
                    where A.order_id = '" . $order_id . "'";

		$bool = $bool && $this->db->query($sql);

		$sql = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `product_id`, `quantity`,`price`,`weight`,`box_quantity`)
                    select A.inventory_move_id, A.station_id,C.product_id, C.quantity quantity, C.price ,0 weight, if(D.repack = 0,1,D.inv_size)
                    from oc_x_stock_move A
                    left join oc_order B on A.order_id = B.order_id
                    left join oc_order_product C on B.order_id = C.order_id
                    left join oc_product D on D.product_id = C.product_id
                    where A.order_id = '" . $order_id . "' and A.inventory_type_id = '".INVENTORY_TYPE_ORDER_STOCK_RETURN."'";

		$bool = $bool && $this->db->query($sql);

		return $bool;
	}

	//处理可售库存
	private function dealSaleinvFllows($order_id){
		//取消使用优惠券记录
		$sql = "update oc_coupon_history set status = '0' where order_id = '".$order_id."'";
		$bool = true;
		$bool = $bool && $this->db->query($sql);

		//查找是否已有库存扣减记录，如有添加库存增加记录
		$sql = "INSERT INTO `oc_x_inventory_move` (`station_id`, `date`, `timestamp`, `from_station_id`, `to_station_id`, `order_id`, `inventory_type_id`, `date_added`, `status`,`warehouse_id`)
                    select A.station_id, current_date() date, unix_timestamp(now()) timestamp, 0 from_station_id, 0 to_station_id, A.order_id, " . INVENTORY_TYPE_ORDER_CANCEL . " inventory_type_id, now() date_added, 1 status,A.warehouse_id
                    from oc_order A
                    inner join oc_x_inventory_move B on A.order_id = B.order_id and inventory_type_id = '" . INVENTORY_TYPE_ORDERED . "'
                    where A.order_id = '" . $order_id . "'";
		$bool = $bool && $this->db->query($sql);
		//$inventory_move_id = $dbm->getLastId();
		//按照添加的，后天订单设置状态为0, 不参与实时库存计算
		$sql = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `order_id`, `customer_id`, `product_id`, `quantity`, `status`,`warehouse_id`)
                    select A.inventory_move_id, A.station_id, B.order_id, B.customer_id, C.product_id, C.quantity quantity, if(B.deliver_date = date_add(date(B.date_added), interval 1 day), 1, 0) status,A.warehouse_id
                    from oc_x_inventory_move A
                    left join oc_order B on A.order_id = B.order_id
                    left join oc_order_product C on B.order_id = C.order_id
                    where A.order_id = '" . $order_id . "' and A.inventory_type_id = '" . INVENTORY_TYPE_ORDER_CANCEL . "'
                    ";
		$bool = $bool && $this->db->query($sql);

		//当可售库存被返还的时候,多仓系统需要对oc_product_inventory表进行更新
		$sql = "update oc_product_inventory pi
			right join (
				select B.station_id,B.warehouse_id,B.product_id,B.quantity,A.inventory_type_id
				from oc_x_inventory_move_item B
				left join oc_x_inventory_move A on A.inventory_move_id = B.inventory_move_id
				where A.order_id = '".$order_id."' and A.inventory_type_id = '".INVENTORY_TYPE_ORDER_CANCEL."'
				group by A.inventory_move_id,B.product_id
			) B on B.product_id = pi.product_id
			and B.station_id = pi.station_id and B.warehouse_id = pi.warehouse_id
			set pi.inventory = pi.inventory+B.quantity";

		$this->db->query($sql);

		return $bool;
	}

	public function cancelOrderDistr($status_id,$order_id,$currentStatus){
		//判断该订单是否已支付，没有支付的话，直接取消
		$payment_status = $this->model_sale_order->getOrderPaymentStatusId($order_id);

		if($currentStatus['order_status_id'] !== CANCELLED_ORDER_STATUS){
			if($payment_status == 1){
				$sql = "update oc_order set order_status_id = ". CANCELLED_ORDER_STATUS ." where order_id = '{$order_id}'";
				$bool = true;
				$bool = $bool && $this->db->query($sql);
				//更改订单状态为取消状态，则执行退库操作
				if($bool){

					//返还可售库存
					$bool = $bool && $this->dealSaleinvFllows($order_id);

					if($status_id == 6){
						$bool = $bool && $this->dealStockinventory($order_id);
					}

					return $bool;
				}else{

					return false;//取消未支付的订单失败

				}
			}elseif($payment_status == 2){
				/*没有微信退现金，则全部退余额，分拣中的订单不允许出库，
				 *先返回可售库存，再退余额给用户，执行成功之后才可以把订单状态改为已取消
				 * 取消的订单不做退货处理，返还可售库存即可
				*/
				$bool = true;
				$sql = "update oc_order set order_status_id =  ". CANCELLED_ORDER_STATUS ." where order_id = '".(int) $order_id."'";

				$bool = $bool &&  $this->db->query($sql);

				$bool = $bool && $this->dealSaleinvFllows($order_id);

				if($status_id ==6){
					$bool = $bool && $this->dealStockinventory($order_id);
				}

				/*
					退余额到客户账户
					需要扣减之前出库缺货的金额
				*/

				$bool = $bool && $this->addTransaction($order_id,$status_id);

				//已拣完的订单需要生成退货数据，暂时交由仓库处理

				return $bool;

			}

		}

	}

	public function getOrderToCanceDeliverStatus($order_id){
		$sql = "select order_deliver_status_id from oc_order where order_id =".(int) $order_id;
		$query = $this->db->query($sql);
		return $query->row['order_deliver_status_id'];
	}

	public function getOrderIsAlloted($order_id){
		$sql = "select order_id from oc_x_logistic_allot_order where order_id =".(int) $order_id;

		$query = $this->db->query($sql);

		if($query->num_rows){
			return true;
		}

		return false;
	}

}