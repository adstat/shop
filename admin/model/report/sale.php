<?php
class ModelReportSale extends Model {
	// Sales
	public function getTotalSales($data = array()) {
		$sql = "SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0'";

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
		
	// Map
	public function getTotalOrdersByCountry() {
		$query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, c.iso_code_2 FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "country` c ON (o.payment_country_id = c.country_id) WHERE o.order_status_id > '0' GROUP BY o.payment_country_id");

		return $query->rows;
	}
		
	// Orders
	public function getTotalOrdersByDay() {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		
		$order_data = array();

		for ($i = 0; $i < 24; $i++) {
			$order_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}
				
		$query = $this->db->query("SELECT COUNT(*) AS total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");

		foreach ($query->rows as $result) {
			$order_data[$result['hour']] = array(
				'hour'  => $result['hour'],
				'total' => $result['total']
			);
		}

		return $order_data;
	}

	public function getTotalOrdersByWeek() {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}		
		
		$order_data = array();

		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$order_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') GROUP BY DAYNAME(date_added)");

		foreach ($query->rows as $result) {
			$order_data[date('w', strtotime($result['date_added']))] = array(
				'day'   => date('D', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $order_data;
	}

	public function getTotalOrdersByMonth() {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
				
		$order_data = array();

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$order_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");

		foreach ($query->rows as $result) {
			$order_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $order_data;
	}

	public function getTotalOrdersByYear() {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
				
		$order_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$order_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");

		foreach ($query->rows as $result) {
			$order_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $order_data;
	}
	
	public function getOrders($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, COUNT(*) AS `orders`, (SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id) AS products, (SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'tax' GROUP BY ot.order_id) AS tax, SUM(o.total) AS `total` FROM `" . DB_PREFIX . "order` o";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added)";
				break;
		}

		$sql .= " ORDER BY o.date_added DESC";

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

	public function getTotalOrders($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added), DAY(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), WEEK(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTaxes($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (ot.order_id = o.order_id) WHERE ot.code = 'tax'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;
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

	public function getTotalTaxes($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), WEEK(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
		}

		$sql .= " LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'tax'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getShipping($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'shipping'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;
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

	public function getTotalShipping($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), WEEK(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
		}

		$sql .= " LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'shipping'";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

    public function getOrderStatus(){
        $sql = "select order_status_id id,name from oc_order_status";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderPaymentStatus(){
        $sql = "select order_payment_status_id id,name from oc_order_payment_status";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getOrderDeliverStatus(){
        $sql = "select order_deliver_status_id id,name from oc_order_deliver_status";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getBdList(){
        $sql = "select bd_id id, bd_name name, phone, status from oc_x_bd";
        $query = $this->db->query($sql);

        return $query->rows;
    }

	public function getBDAreaList(){
		$sql = "select area_id id,concat(district, '->',name) name from oc_x_area";
		$query = $this->db->query($sql);

		return $query->rows;
	}
	public function getLogisticList(){
		$sql = "SELECT logistic_driver_id, logistic_driver_title FROM oc_x_logistic_driver";
		$query = $this->db->query($sql);
		return $query->rows;
	}
    public function getMkOrders($filter_data){
        //    'filter_date_start' => $filter_date_start,
        //    'filter_date_end' => $filter_date_end,
        //    'filter_order_status' => $filter_order_status,
        //    'filter_order_payment_status' => $filter_order_payment_status,
        //    'filter_bd_list' => $filter_bd_list,
        //    'filter_customer_id' => $filter_customer_id
        //);
        //$return = array('orders'=>array(), 'totals'=>array());

        $sql = "select A.order_id,A.customer_id customer_count_id, concat(A.shipping_city,A.shipping_address_1) order_address, A.shipping_phone, C.name order_status, D.name payment_status, E.name deliver_status,R.logistic_driver_title,
                A.deliver_date, date(A.date_added) order_date, time(A.date_added) order_time, A.customer_id, A.shipping_name, A.firstname, CU.merchant_name merchant_name,
                BD.bd_name, BD.phone bd_phone, EXT.firstorder,AREA.name area_name,
                sum(if(B.code='sub_total', B.value, 0))  sub_total,
                EXT.fresh_total,
                EXT.nonfresh_total,
                EXT.fresh_skus,
                EXT.fresh_items,
                sum(if(B.code='discount_total', B.value, 0)) discount,
                sum(if(B.code='total', B.value, 0))  order_due,
                sum(if(B.code='wxpay', B.value, 0))  wechat_paid,
                sum(if(B.code='user_point', B.value, 0))  user_point_paid,
                sum(if(B.code='credit_paid', B.value, 0))  credit_paid

                from oc_order A
                left join oc_customer CU on A.customer_id = CU.customer_id
                left join oc_order_total B on A.order_id=B.order_id
                left join oc_order_status C on A.order_status_id = C.order_status_id
                left join oc_order_payment_status D on A.order_payment_status_id = D.order_payment_status_id
                left join oc_order_deliver_status E on A.order_deliver_status_id = E.order_deliver_status_id
                left join oc_customer CUST on A.customer_id = CUST.customer_id
                left join oc_order_extend EXT on A.order_id = EXT.order_id
                left join oc_x_bd BD on CUST.bd_id = BD.bd_id
			    left join oc_x_area AREA on AREA.area_id = CUST.area_id
			    left join oc_x_logistic_allot_order Q on A.order_id = Q.order_id
				left join oc_x_logistic_allot R on Q.logistic_allot_id = R.logistic_allot_id
                ";

        if($filter_data['filter_datatype'] == 2){
			$sql .= " where date(A.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }
        else{
			$sql .= " where A.deliver_date between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
        }

        if($filter_data['filter_order_status']){
			$sql .=" and A.order_status_id = '".$filter_data['filter_order_status']."' ";
        }else{
			$sql .=" and A.order_status_id not in (".CANCELLED_ORDER_STATUS.") ";
        }

        if($filter_data['filter_order_deliver_status']){
			$sql .=" and A.order_deliver_status_id = '".$filter_data['filter_order_deliver_status']."' ";
        }else{
			$sql .=" and A.order_deliver_status_id not in (7) ";
        }

        if($filter_data['filter_order_payment_status']){
			$sql .=" and A.order_payment_status_id = '".$filter_data['filter_order_payment_status']."' ";
        }
        if($filter_data['filter_bd_list']){
			$sql .=" and A.bd_id = '".$filter_data['filter_bd_list']."' ";
        }
		if($filter_data['filter_bd_area_list']){
			$sql .=" and AREA.area_id = '".$filter_data['filter_bd_area_list']."'";
		}

		if($filter_data['filter_logistic_list']){
			$sql .=" and R.logistic_driver_title = '".$filter_data['filter_logistic_list']."'";
		}
//        if($filter_data['filter_firstorder']){
//			$sql .=" and EXT.firstorder = '".$filter_data['filter_firstorder']."' ";
//        }
        
        if($filter_data['filter_station']){
			$sql .=" and A.station_id = '".$filter_data['filter_station']."' ";
        }
        
        if($filter_data['filter_customer_id']){
			$sql .=" and A.customer_id in (".$filter_data['filter_customer_id'].")";
        }

		if($filter_data['filter_warehouse_id_global']){
			$sql .=" and A.warehouse_id = '".(int)$filter_data['filter_warehouse_id_global']."'";
		}

		$sql .=" group by B.order_id order by A.order_id ASC";

		$query = $this->db->query($sql);
		$orders = array();
		if($query->num_rows){
			$orders = $query->rows;
			foreach($orders as &$value){
				$sql_o = "select OO.order_id,sum(OO.outstock_return_total) outstock_return_total,sum(OO.return_total) return_total
					from(
						select
						r.order_id,rp.product_id,
						if(r.return_reason_id in (1,5),sum(rp.return_product_credits),0) outstock_return_total,
						if(r.return_reason_id in (2,3,4),sum(rp.return_product_credits),0) return_total
						from oc_return r
						left join oc_return_product rp on rp.return_id = r.return_id
						where r.order_id = '".(int)$value['order_id']."' and r.return_status_id != '3'
						group by r.order_id,rp.product_id,r.return_reason_id
					) OO
					group by OO.order_id";

				$query = $this->db->query($sql_o);
				if($query->num_rows){
					$value['quehuo_credits'] = $query->row['outstock_return_total'];
					$value['tuihuo_credits'] = $query->row['return_total'];
				}else{
					$value['quehuo_credits'] = 0.00;
					$value['tuihuo_credits'] = 0.00;
				}

			}
		}
		$return['orders'] = $orders;

		//区域汇总
//        $sql_sum_area = "select ";
//        if($filter_data['filter_datatype'] == 2){
//			$sql_sum_area .= " AA.order_date sum_date, AA.area_name, ";
//        }
//        else{
//			$sql_sum_area .= " AA.deliver_date sum_date, AA.area_name,";
//        }
//		$sql_sum_area .=" count(AA.order_id) order_count,
//		 count(distinct AA.customer_count_id) sum_customer_count,
//		 if(sum(AA.firstorder),sum(AA.firstorder),0) sum_first_total,
//        round(sum(AA.sub_total),2) sum_sub_total,
//        round(sum(AA.sub_total)/count(AA.order_id) ,2) avg_order_total,
//
//        round(sum(AA.fresh_total),2) sum_fresh_total,
//        sum(if(AA.fresh_skus>15,1,0)) fresh_orders,
//        round( sum(AA.fresh_total)/sum(if(AA.fresh_skus>15,1,0)) ,2) fresh_order_total,
//
//        round(sum(AA.nonfresh_total),2) sum_nonfresh_total,
//        round(sum(AA.discount),2) sum_discount,
//        round(sum(order_due),2) sum_order_due,
//        round(sum(wechat_paid),2) sum_wechat_paid,
//        round(sum(user_point_paid),2) sum_user_point_paid,
//        round(sum(credit_paid),2) sum_credit_paid from(".$sql.") AA";
//        if($filter_data['filter_datatype'] == 2){
//			$sql_sum_area .= " group by AA.area_name";
//        }
//        else{
//			$sql_sum_area .= " group by AA.area_name";
//        }
//
//        $query = $this->db->query($sql_sum_area);
//        if($query){
//            $return['areas'] = $query->rows;
//        }
		//

        $sql_sum = "select ";
        if($filter_data['filter_datatype'] == 2){
            $sql_sum .= " AA.order_date sum_date, ";
        }
        else{
            $sql_sum .= " AA.deliver_date sum_date, ";
        }
        $sql_sum .=" count(AA.order_id) order_count,
        round(sum(AA.sub_total),2) sum_sub_total,
        round(sum(AA.sub_total)/count(AA.order_id) ,2) avg_order_total,

        round(sum(AA.fresh_total),2) sum_fresh_total,
        sum(if(AA.fresh_skus>15,1,0)) fresh_orders,
        round( sum(AA.fresh_total)/sum(if(AA.fresh_skus>15,1,0)) ,2) fresh_order_total,

        round(sum(AA.nonfresh_total),2) sum_nonfresh_total,
        round(sum(AA.discount),2) sum_discount,
        round(sum(order_due),2) sum_order_due,
        round(sum(wechat_paid),2) sum_wechat_paid,
        round(sum(user_point_paid),2) sum_user_point_paid,
        round(sum(credit_paid),2) sum_credit_paid from(".$sql.") AA";
        if($filter_data['filter_datatype'] == 2){
            $sql_sum .= " group by AA.order_date ";
        }
        else{
            $sql_sum .= " group by AA.deliver_date ";
        }

        $query = $this->db->query($sql_sum);
        if($query){
            $return['totals'] = $query->rows;
        }

        return $return;
    }

    public function getCustomerActive($filter_data){

        $return = array('orders'=>array(), 'totals'=>array(),'areas'=>array(),);

        $sql ="select AREA.area_id, AREA.name area_name, o.bd_id, b.bd_name, o.customer_id, o.firstname customer_name, o.shipping_firstname merchant_name, o.shipping_address_1 merchant_address, o.shipping_phone,
                sum(e.firstorder) with_firstorder, min(o.order_id) firstorder_id,
                if(sum(e.firstorder) > 0, min(date(o.date_added)), '".$filter_data['filter_date_start']."') checkdate,
                min(date(o.date_added)) earlist_order_adate,
                count(distinct date(o.date_added)) order_dates,
                count(o.order_id) orders,
                sum(e.firstorder) fistorder,
                sum(e.firstorder_fm) fistorder_fm,
                datediff('".$filter_data['filter_date_end']."', if(sum(e.firstorder) > 0, min(date(o.date_added)), '".$filter_data['filter_date_start']."'))+1 checkdate_gap,
                sum(o.sub_total) sub_total,
                sum(e.fresh_total) fresh_total
             from oc_order o
             left join oc_order_extend e on o.order_id = e.order_id
             left join oc_x_bd b on o.bd_id = b.bd_id
             left join oc_customer CUST on o.customer_id = CUST.customer_id
             left join oc_x_area AREA on AREA.area_id = CUST.area_id
             where
             date(o.date_added) between '".$filter_data['filter_date_start']."'
             and '".$filter_data['filter_date_end']."'
             and o.order_status_id not in (3)";


        if($filter_data['filter_bd_list']>0){
            $sql = $sql . " and o.bd_id = '".$filter_data['filter_bd_list']."'";
        }
        if($filter_data['filter_station_id']>0){
            $sql = $sql . " and o.station_id = '".$filter_data['filter_station_id']."'";
        }
		if($filter_data['filter_warehouse_id_global']){
			$sql = $sql . " and o.warehouse_id = '".$filter_data['filter_warehouse_id_global']."'";
		}
		if($filter_data['filter_bd_area_list']>0){
			$sql = $sql . " and AREA.area_id = '".$filter_data['filter_bd_area_list']."'";
		}

        $sql = $sql . " group by bd_id, customer_id";

        if($filter_data['filter_bd_list']>0){
            $query = $this->db->query($sql);
            if($query){
                $return['orders'] = $query->rows;
            }
        }

        //统计结果中不含日期范围内最后一天首单的用户
        $sql_sum = "select A.bd_id, A.bd_name, A.area_name,
            sum(A.orders) sum_orders, sum(A.fistorder) fistorders, sum(A.fistorder_fm) fm_fistorders,
            sum(A.sub_total) sum_sub_total, sum(A.fresh_total) sum_fresh_total,
            count(A.customer_id) sum_customers, sum(if( if( A.with_firstorder = 1 and A.earlist_order_adate = '".$filter_data['filter_date_end']."',  A.order_dates -1,A.order_dates ) /A.checkdate_gap > ".$filter_data['filter_activity'].",1,0)) active_customers
            from (".$sql.") A group by A.bd_id";

        $query = $this->db->query($sql_sum);
        if($query){
            $return['totals'] = $query->rows;
        }

//		if($filter_data['filter_bd_area_list']>0){
			$sql_sum = "select A.bd_id, A.bd_name, A.area_name,if(A.area_id is null,0,A.area_id) area_id,
            sum(A.orders) sum_orders, sum(A.fistorder) fistorders, sum(A.fistorder_fm) fm_fistorders,
            sum(A.sub_total) sum_sub_total, sum(A.fresh_total) sum_fresh_total,
            count(A.customer_id) sum_customers, sum(if( if( A.with_firstorder = 1 and A.earlist_order_adate = '".$filter_data['filter_date_end']."',  A.order_dates -1,A.order_dates ) /A.checkdate_gap > ".$filter_data['filter_activity'].",1,0)) active_customers
            from (".$sql.") A group by A.area_id";
//		}
		$query = $this->db->query($sql_sum);
		if($query){
			$return['areas'] = $query->rows;
		}

        //BD所有用户统计
        $return['bd_customers'] = array();
        $sql_bd = "select bd_id, count(*) customers, sum(if(status=0, 1, 0)) disable_customer from oc_customer group by bd_id";
        $query = $this->db->query($sql_bd);
        if($query){
            $bd_customers = $query->rows;
            foreach($bd_customers as $m){
                $return['bd_customers'][$m['bd_id']] = $m;
            }
        }

        //BD所有下单用户统计
        $return['bd_ordered_customers'] = array();
        $sql_bd = "select if(C.bd_id is null, 0 , C.bd_id) bd_id,
                    count(distinct O.customer_id) ordered_customers
                    from oc_order O
                    left join oc_customer C on O.customer_id = C.customer_id
                    where O.order_status_id not in (3)  and O.type = 1
                    ";
        if($filter_data['filter_station_id']>0){
            $sql_bd = $sql_bd . " and O.station_id = '".$filter_data['filter_station_id']."'";
        }
		if($filter_data['filter_warehouse_id_global']>0){
			$sql_bd = $sql_bd . " and O.warehouse_id = '".$filter_data['filter_warehouse_id_global']."'";
		}
        $sql_bd .= ' group by bd_id';
        $query = $this->db->query($sql_bd);
        if($query){
            $bd_ordered_customers = $query->rows;
            foreach($bd_ordered_customers as $m){
                $return['bd_ordered_customers'][$m['bd_id']] = $m;
            }
        }

		//区域所有客户统计
		$return['area_customers'] = array();
		$sql_a = "select if(AREA.area_id is null,0,AREA.area_id) area_id, count(DISTINCT CUST.customer_id) customers, sum(if(CUST.status=0, 1, 0)) disable_customer
			from oc_customer CUST
			left join oc_x_area AREA on AREA.area_id = CUST.area_id
			group by AREA.area_id";
		$query = $this->db->query($sql_a);
		if($query){
			$area_customers = $query->rows;
			foreach($area_customers as $m){
				$return['area_customers'][$m['area_id']] = $m;
			}
		}

		//区域所有下单客户统计
		$return['bd_area_customers'] = array();
		$sql_a = "select if(AREA.area_id is null,0,AREA.area_id) area_id, count(DISTINCT CUST.customer_id) ordered_customers
			from oc_order o
			left join oc_x_bd b on o.bd_id = b.bd_id
			left join oc_customer CUST on o.customer_id = CUST.customer_id
			left join oc_x_area AREA on AREA.area_id = CUST.area_id
			where o.order_status_id not in (3) and o.type = 1";

		if($filter_data['filter_warehouse_id_global']>0){
			$sql_a = $sql_a . " and o.station_id = '".$filter_data['filter_station_id']."'";
		}

		if($filter_data['filter_station_id']>0){
			$sql_a = $sql_a . " and o.warehouse_id = '".$filter_data['filter_warehouse_id_global']."'";
		}

		$sql_a = $sql_a . " group by AREA.area_id";

		$query = $this->db->query($sql_a);
		if($query){
			$bd_area_customers = $query->rows;
			foreach($bd_area_customers as $m){
				$return['bd_area_customers'][$m['area_id']] = $m;
			}
		}

        return $return;
    }

    public function addDate($orgDate,$day){
        $cd = strtotime($orgDate);
        $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$day,date('Y',$cd)));
        return $retDAY;
    }

    public function dateList($start='',$end=''){
        $gap = dateGap($start,$end);

        $dayList = array();
        $cd = strtotime($start);
        for($m=0; $m<$gap+1; $m++){
            $dayList[] = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$m,date('Y',$cd)));
        }

        return $dayList;
    }

    public function dateGap($start='',$end=''){
        $a = date_create($start);
        $b = date_create($end);

        $m = date_diff($a,$b);
        $gap = $m->format('%a');

        return $gap;
    }

    
    public function getInvMiCold($station_id = 1){
        $sql = "select inventory_move_id,date_added from oc_x_stock_move where inventory_type_id = 14 order by inventory_move_id desc limit 1";
        
        $query = $this->db->query($sql);
        $inventory_check = $query->row;
        
        $inventory_check_id = $inventory_check['inventory_move_id'];
        $inventory_check_time = $inventory_check['date_added'];
        if($inventory_check_id){
            $sql = "SELECT
                    xsm.inventory_move_id,
                    xsm.inventory_type_id,
                    smi.product_id,
                    sum(smi.quantity) as product_move_type_quantity,
                    p.name,
                    p.inv_class_sort 
            FROM
                    oc_x_stock_move AS xsm
            left JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id
            left join oc_product as p on p.product_id = smi.product_id 
            -- left join (select * from oc_product_to_category group by product_id) as ptc on ptc.product_id = smi.product_id
            -- left join (select * from oc_product_description where language_id = 2 group by product_id) as pd on pd.product_id=smi.product_id
            WHERE
                    xsm.inventory_move_id >= " . $inventory_check_id . "
            and xsm.date_added >= '" . $inventory_check_time . "'
            and p.station_id = '".$station_id."'
            -- and p.product_id > 5000
            -- and (smi.product_id > 5000 or ptc.category_id in (72,74,157))
            group by xsm.inventory_type_id,smi.product_id";

            $query = $this->db->query($sql);
            $inventory_arr = $query->rows;
            $inventory_product_move_arr = array();
            foreach($inventory_arr as $key=>$value){
                $inventory_product_move_arr[$value['product_id']]['quantity'][$value['inventory_type_id']] = $value['product_move_type_quantity'];
                $inventory_product_move_arr[$value['product_id']]['name'] = $value['name'];
                $inventory_product_move_arr[$value['product_id']]['inv_class_sort'] = $value['inv_class_sort'];
                $inventory_product_move_arr[$value['product_id']]['sum_quantity'] = 0;
                $inventory_product_move_arr[$value['product_id']]['date_added'] = $inventory_check['date_added'];
            }
            return $inventory_product_move_arr;
        }else{
            return array();
        }
        
    }

    public function getProductToPromotion(){
        $sql = "select * from oc_product_to_promotion_product";
        $product_to_promotion_arr = array();
        $query = $this->db->query($sql);
        $result = $query->rows;
        foreach($result as $key=>$value){
            $product_to_promotion_arr[$value['product_id']] = $value['promotion_product_id'];
        }
        return $product_to_promotion_arr;
    }


    public function getProductName($product_id){
        $sql = "select name from oc_product where product_id = " . $product_id;

        $query = $this->db->query($sql);
        $result = $query->row;
        return $result['name'];
    }

    public function getBdIdByUsername($username){
        $sql = "select bd_id, bd_name, crm_username from oc_x_bd where crm_username = '".$username."' and sale_access_control = 1";

        $query = $this->db->query($sql);
        $result = $query->row;
        if($result){
            return $result['bd_id'];
        }

        return 0;
    }

    public function getCustomerPreform(){
        $sql = "select A.customer_id 用户ID,  A.firstname 用户名, CU.merchant_name 商家名, concat(A.shipping_city,A.shipping_address_1) 地址,
                BD.bd_name BD名称,
                A.order_id 首单号,
                A.deliver_date 配送日期, date(A.date_added) 下单日期,
                if(W.second_order>A.order_id, W.second_order,'') 二次下单,   if(W.second_order>A.order_id, W.second_order_date,'')  二次下单日期,
                if(W.third_order>W.second_order, W.third_order,'') 三次下单,  if(W.third_order>W.second_order, W.third_order_date,'')  三次下单日期
                from oc_order A
                left join oc_customer CU on A.customer_id = CU.customer_id
                left join oc_order_status C on A.order_status_id = C.order_status_id
                left join oc_order_payment_status D on A.order_payment_status_id = D.order_payment_status_id
                left join oc_order_deliver_status E on A.order_deliver_status_id = E.order_deliver_status_id
                left join oc_order_extend EXT on A.order_id = EXT.order_id
                left join oc_x_bd BD on CU.bd_id = BD.bd_id
                left join(
                        select
                        B.customer_id,
                        SUBSTRING_INDEX(B.orderlist,',',1) first_order,
                        SUBSTRING_INDEX(B.datelist,',',1) first_order_date,
                        SUBSTRING_INDEX(SUBSTRING_INDEX(B.orderlist,',',2),',',-1) second_order,
                        SUBSTRING_INDEX(SUBSTRING_INDEX(B.datelist,',',2),',',-1) second_order_date,
                        SUBSTRING_INDEX(SUBSTRING_INDEX(B.orderlist,',',3),',',-1) third_order,
                        SUBSTRING_INDEX(SUBSTRING_INDEX(B.datelist,',',3),',',-1) third_order_date
                        from (
                            select customer_id, group_concat(adate order by order_id asc) datelist, group_concat(order_id order by order_id asc) orderlist from
                            (
                                select
                                customer_id,
                                date(date_added) adate,
                                min(order_id) order_id
                                from oc_order where date(date_added) between '2016-06-31' and '2016-07-13'  and order_status_id not in (3)  and type = 1
                                group by adate, customer_id
                            ) A group by A.customer_id
                        ) B group by B.customer_id
                ) W on CU.customer_id = W.customer_id
                where date(A.date_added) between '2016-06-31' and '2016-07-13' and A.order_status_id not in (3) and EXT.firstorder = 1 and A.station_id = 2
                group by A.order_id order by A.order_id ASC LIMIT 1";

        $query = $this->db->query($sql);
        return $query->rows;
    }

	public function getAreaInfo($filter_data){
		$area = array();
		$sql = "select A.order_id,A.customer_id customer_count_id, concat(A.shipping_city,A.shipping_address_1) order_address, A.shipping_phone, C.name order_status, D.name payment_status, E.name deliver_status,R.logistic_driver_title,
                A.deliver_date, date(A.date_added) order_date, time(A.date_added) order_time, A.customer_id, A.shipping_name, A.firstname, CU.merchant_name merchant_name,
                BD.bd_name, BD.phone bd_phone, EXT.firstorder,AREA.name area_name,
                sum(if(B.code='sub_total', B.value, 0))  sub_total,
                EXT.fresh_total,
                EXT.nonfresh_total,
                EXT.fresh_skus,
                EXT.fresh_items,
                sum(if(B.code='discount_total', B.value, 0)) discount,
                sum(if(B.code='total', B.value, 0))  order_due,
                sum(if(B.code='wxpay', B.value, 0))  wechat_paid,
                sum(if(B.code='user_point', B.value, 0))  user_point_paid,
                sum(if(B.code='credit_paid', B.value, 0))  credit_paid

                from oc_order A
                left join oc_customer CU on A.customer_id = CU.customer_id
                left join oc_order_total B on A.order_id=B.order_id
                left join oc_order_status C on A.order_status_id = C.order_status_id
                left join oc_order_payment_status D on A.order_payment_status_id = D.order_payment_status_id
                left join oc_order_deliver_status E on A.order_deliver_status_id = E.order_deliver_status_id
                left join oc_customer CUST on A.customer_id = CUST.customer_id
                left join oc_order_extend EXT on A.order_id = EXT.order_id
                left join oc_x_bd BD on CUST.bd_id = BD.bd_id
			    left join oc_x_area AREA on AREA.area_id = CUST.area_id
			    left join oc_x_logistic_allot_order Q on A.order_id = Q.order_id
				left join oc_x_logistic_allot R on Q.logistic_allot_id = R.logistic_allot_id
                ";
		if($filter_data['filter_datatype'] == 2){
			$sql .= " where date(A.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
		}
		else{
			$sql .= " where A.deliver_date between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
		}

		if($filter_data['filter_order_status']){
			$sql .=" and A.order_status_id = '".$filter_data['filter_order_status']."' ";
		}else{
			$sql .=" and A.order_status_id not in (".CANCELLED_ORDER_STATUS.") ";
		}

		if($filter_data['filter_order_deliver_status']){
			$sql .=" and A.order_deliver_status_id = '".$filter_data['filter_order_deliver_status']."' ";
		}else{
			$sql .=" and A.order_deliver_status_id not in (7) ";
		}

		if($filter_data['filter_order_payment_status']){
			$sql .=" and A.order_payment_status_id = '".$filter_data['filter_order_payment_status']."' ";
		}
		if($filter_data['filter_bd_list']){
			$sql .=" and A.bd_id = '".$filter_data['filter_bd_list']."' ";
		}
		if($filter_data['filter_bd_area_list']){
			$sql .=" and AREA.area_id = '".$filter_data['filter_bd_area_list']."'";
		}

		if($filter_data['filter_logistic_list']){
			$sql .=" and R.logistic_driver_title = '".$filter_data['filter_logistic_list']."'";
		}

		if($filter_data['filter_station']){
			$sql .=" and A.station_id = '".$filter_data['filter_station']."' ";
		}

		if($filter_data['filter_customer_id']){
			$sql .=" and A.customer_id in (".$filter_data['filter_customer_id'].")";
		}
		$sql .=" group by B.order_id order by A.order_id ASC";

		//区域汇总
		$sql_sum_area = "select ";
		if($filter_data['filter_datatype'] == 2){
			$sql_sum_area .= " AA.order_date sum_date, if(AA.area_name is null, '未分类',AA.area_name) area_name, ";
		}
		else{
			$sql_sum_area .= " AA.deliver_date sum_date, if(AA.area_name is null, '未分类',AA.area_name) area_name,";
		}
		$sql_sum_area .=" count(AA.order_id) order_count,
		 count(distinct AA.customer_count_id) sum_customer_count,
		 if(sum(AA.firstorder),sum(AA.firstorder),0) sum_first_total,
        round(sum(AA.sub_total),2) sum_sub_total,
        round(sum(AA.sub_total)/count(AA.order_id) ,2) avg_order_total,

        round(sum(AA.fresh_total),2) sum_fresh_total,
        sum(if(AA.fresh_skus>15,1,0)) fresh_orders,
        round( sum(AA.fresh_total)/sum(if(AA.fresh_skus>15,1,0)) ,2) fresh_order_total,

        round(sum(AA.nonfresh_total),2) sum_nonfresh_total,
        round(sum(AA.discount),2) sum_discount,
        round(sum(order_due),2) sum_order_due,
        round(sum(wechat_paid),2) sum_wechat_paid,
        round(sum(user_point_paid),2) sum_user_point_paid,
        round(sum(credit_paid),2) sum_credit_paid from(".$sql.") AA";
		if($filter_data['filter_datatype'] == 2){
			$sql_sum_area .= " group by AA.area_name";
		}
		else{
			$sql_sum_area .= " group by AA.area_name";
		}

		$query = $this->db->query($sql_sum_area);
		if($query){
			$area = $query->rows;
		}
		return $area;
	}

	public function getDriverTotal($filter_data){
		$sql = "select A.order_id,A.customer_id customer_count_id, concat(A.shipping_city,A.shipping_address_1) order_address, A.shipping_phone, C.name order_status, D.name payment_status, E.name deliver_status,R.logistic_driver_title,
                A.deliver_date, date(A.date_added) order_date, time(A.date_added) order_time, A.customer_id, A.shipping_name, A.firstname, CU.merchant_name merchant_name,
                BD.bd_name, BD.phone bd_phone,
                sum(if(B.code='sub_total', B.value, 0))  sub_total,
                sum(if(B.code='discount_total', B.value, 0)) discount,
                sum(if(B.code='total', B.value, 0))  order_due,
                sum(if(B.code='wxpay', B.value, 0))  wechat_paid,
                sum(if(B.code='user_point', B.value, 0))  user_point_paid,
                sum(if(B.code='credit_paid', B.value, 0))  credit_paid

                from oc_order A
                left join oc_customer CU on A.customer_id = CU.customer_id
                left join oc_order_total B on A.order_id=B.order_id
                left join oc_order_status C on A.order_status_id = C.order_status_id
                left join oc_order_payment_status D on A.order_payment_status_id = D.order_payment_status_id
                left join oc_order_deliver_status E on A.order_deliver_status_id = E.order_deliver_status_id
                left join oc_x_bd BD on CU.bd_id = BD.bd_id
			    left join oc_x_logistic_allot_order Q on A.order_id = Q.order_id
				left join oc_x_logistic_allot R on Q.logistic_allot_id = R.logistic_allot_id
				where date(R.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."'";

		if($filter_data['filter_order_status']){
			$sql .=" and A.order_status_id = '".$filter_data['filter_order_status']."' ";
		}else{
			$sql .=" and A.order_status_id not in (".CANCELLED_ORDER_STATUS.") ";
		}

		if($filter_data['filter_order_deliver_status']){
			$sql .=" and A.order_deliver_status_id = '".$filter_data['filter_order_deliver_status']."' ";
		}else{
			$sql .=" and A.order_deliver_status_id not in (7) ";
		}

		if($filter_data['filter_order_payment_status']){
			$sql .=" and A.order_payment_status_id = '".$filter_data['filter_order_payment_status']."' ";
		}

		if($filter_data['filter_station']){
			$sql .=" and A.station_id = '".$filter_data['filter_station']."' ";
		}

		if($filter_data['filter_warehouse_id_global']){
			$sql .=" and A.warehouse_id = '".$filter_data['filter_warehouse_id_global']."' ";
		}

		if($filter_data['filter_logistic_list']){
			$sql .=" and R.logistic_driver_id = '".$filter_data['filter_logistic_list']."'";
		}

		if($filter_data['filter_logistic_line']){
			$sql .=" and R.logistic_line_id = '".$filter_data['filter_logistic_line']."'";
		}

		$sql .=" group by B.order_id order by A.order_id ASC";
		$query = $this->db->query($sql);

		$order_info = array();
		$order_ids = array();
		if($query->num_rows){
			$order_info = $query->rows;
			foreach($order_info as $value){
				$order_ids[] = $value['order_id'];
			}

			$orders = implode(",",$order_ids);

			$sql = "select OO.order_id,sum(OO.outstock_return_total) outstock_return_total,sum(OO.return_total) return_total
					from(
						select
						r.order_id,rp.product_id,
						if(r.return_reason_id in (1,5),sum(rp.return_product_credits),0) outstock_return_total,
						if(r.return_reason_id in (2,3,4),sum(rp.return_product_credits),0) return_total
						from oc_return r
						left join oc_return_product rp on rp.return_id = r.return_id
						where r.order_id in (".$orders.")
						group by r.order_id,rp.product_id,r.return_reason_id
					) OO
					group by OO.order_id";

			$query = $this->db->query($sql);

			$order_short = array();

			if($query->num_rows){
				foreach($query->rows as $value){
					$order_short[$value['order_id']] = $value;
				}
			}
		}
		foreach($order_info as &$value){
			if(array_key_exists($value['order_id'],$order_short)){
				$value['quehuo_credits'] = $order_short[$value['order_id']]['outstock_return_total'];
				$value['tuihuo_credits'] = $order_short[$value['order_id']]['return_total'];
			}else{
				$value['quehuo_credits'] = 0;
				$value['tuihuo_credits'] = 0;
			}

			$value['sum_due'] = $value['order_due'] + $value['wechat_paid'] + $value['user_point_paid'] - $value['quehuo_credits'] - $value['tuihuo_credits'];
		}

		return $order_info;

	}

	public function getBdPerforemance($filter_data){

		$return = array('orders'=>array(), 'returns'=>array(),'bd_customers'=>array(),'bd_ordered_customers'=>array());

		$sql = "SELECT o.bd_id,count(DISTINCT o.order_id) as order_count , sum(if(e.firstorder = 1, 1 ,0)) firstorder_sum, sum(if(e.secondorder_fm = 1, 1 ,0)) secondorder_sum,sum(if(e.thirdorder_fm = 1, 1, 0)) thirdorder_sum,
			sum(o.sub_total) sub_total, sum(o.discount_total) discount_total, sum(o.total) total , count(DISTINCT o.customer_id) as sum_customer,b.bd_name,sum(if(e.awakenorder_fm = 1,1,0)) awokenorder_sum
			from oc_order o
			left join oc_order_extend e on o.order_id = e.order_id
			left join oc_x_bd b on b.bd_id = o.bd_id

			where o.order_status_id <> 3 and date(o.date_added) BETWEEN '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."'
			";

		if($filter_data['filter_bd_list']>0){
			$sql = $sql . " and o.bd_id = '".$filter_data['filter_bd_list']."'";
		}
		if($filter_data['filter_station_id']>0){
			$sql = $sql . " and o.station_id = '".$filter_data['filter_station_id']."'";
		}

//		if($filter_data['filter_bd_area_list']>0){
//			$sql = $sql . " and AREA.area_id = '".$filter_data['filter_bd_area_list']."'";
//		}

		$sql  = $sql. " group by o.bd_id";
		$query = $this->db->query($sql);

		$orders = array();

		if(sizeof($query->rows)){
			foreach($query->rows as $value){
				$orders[$value['bd_id']] = $value;
			}
		}

		if($query){
			$return['orders'] = $orders;
		}

		//查询退货
		$sql = "select o.bd_id ,sum(if(r.return_reason_id in (1,2,3,4,5),r.return_credits,0)) r_sum, sum(if(r.return_reason_id = 2,r.return_credits,0)) c_r_sum
			from oc_order o
			inner join oc_return r on r.order_id = o.order_id
			left join oc_return_product rp on rp.return_id = r.return_id
			where date(o.date_added) BETWEEN '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."'
			and r.return_reason_id in (1,2,3,4,5) and r.return_status_id = 2
			and o.order_status_id <> 3 and r.credits_returned > 0
			";

		if($filter_data['filter_bd_list']>0){
			$sql = $sql . " and o.bd_id = '".$filter_data['filter_bd_list']."'";
		}
		if($filter_data['filter_station_id']>0){
			$sql = $sql . " and o.station_id = '".$filter_data['filter_station_id']."'";
		}

		$sql = $sql . "group by o.bd_id";

		$query = $this->db->query($sql);

		$returns = array();
		if(sizeof($query->rows)){
			foreach($query->rows as $value){
				$returns[$value['bd_id']] = $value;
			}
		}

		if($query){
			$return['returns'] = $returns;
		}

		//BD所有用户统计
		$sql_bd = "select bd_id, count(*) customers, sum(if(status=0, 1, 0)) disable_customer from oc_customer group by bd_id";
		$query = $this->db->query($sql_bd);

		$bd_customers = array();
		if(sizeof($query->rows)){
			foreach($query->rows as $value){
				$bd_customers[$value['bd_id']] = $value;
			}
		}

		if($query){
			$return['bd_customers'] = $bd_customers;
		}

		//BD所有下单用户统计
		$sql_bd = "select if(C.bd_id is null, 0 , C.bd_id) bd_id,
                    count(distinct O.customer_id) ordered_customers
                    from oc_order O
                    left join oc_customer C on O.customer_id = C.customer_id
                    where O.order_status_id not in (3)  and O.type = 1
                    ";
		if($filter_data['filter_station_id']>0){
			$sql_bd = $sql_bd . " and O.station_id = '".$filter_data['filter_station_id']."'";
		}
		if($filter_data['filter_bd_list']>0){
			$sql_bd = $sql_bd . " and O.bd_id = '".$filter_data['filter_bd_list']."'";
		}

		$sql_bd .= ' group by O.bd_id';

		$query = $this->db->query($sql_bd);

		$bd_ordered_customers = array();

		if(sizeof($query->rows)){
			foreach($query->rows as $value){
				$bd_ordered_customers[$value['bd_id']] = $value;
			}
		}

		if($query){
			$return['bd_ordered_customers'] = $bd_ordered_customers;
		}

		return $return;
	}
}