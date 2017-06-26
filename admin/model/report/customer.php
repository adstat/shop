<?php
class ModelReportCustomer extends Model {
	public function getTotalCustomersByDay() {
		$customer_data = array();

		for ($i = 0; $i < 24; $i++) {
			$customer_data[$i] = array(
				'hour'  => $i,
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");

		foreach ($query->rows as $result) {
			$customer_data[$result['hour']] = array(
				'hour'  => $result['hour'],
				'total' => $result['total']
			);
		}

		return $customer_data;
	}

	public function getTotalCustomersByWeek() {
		$customer_data = array();

		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));

			$order_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') GROUP BY DAYNAME(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('w', strtotime($result['date_added']))] = array(
				'day'   => date('D', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $customer_data;
	}

	public function getTotalCustomersByMonth() {
		$customer_data = array();

		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;

			$customer_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $customer_data;
	}

	public function getTotalCustomersByYear() {
		$customer_data = array();

		for ($i = 1; $i <= 12; $i++) {
			$customer_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				'total' => 0
			);
		}

		$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "customer` WHERE YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}

		return $customer_data;
	}
	
	public function getOrders($data = array()) {
		$sql = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, COUNT(o.order_id) AS orders, SUM(op.quantity) AS products, SUM(o.total) AS `total` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_product` op ON (o.order_id = op.order_id)LEFT JOIN `" . DB_PREFIX . "customer` c ON (o.customer_id = c.customer_id) LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE o.customer_id > 0 AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

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

		$sql .= " GROUP BY o.customer_id ORDER BY total DESC";

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
		$sql = "SELECT COUNT(DISTINCT o.customer_id) AS total FROM `" . DB_PREFIX . "order` o WHERE o.customer_id > '0'";

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
		
	public function getRewardPoints($data = array()) {
		$sql = "SELECT cr.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, SUM(cr.points) AS points, COUNT(o.order_id) AS orders, SUM(o.total) AS total FROM " . DB_PREFIX . "customer_reward cr LEFT JOIN `" . DB_PREFIX . "customer` c ON (cr.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (cr.order_id = o.order_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(cr.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(cr.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$sql .= " GROUP BY cr.customer_id ORDER BY points DESC";

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

	public function getTotalRewardPoints($data = array()) {
		$sql = "SELECT COUNT(DISTINCT customer_id) AS total FROM `" . DB_PREFIX . "customer_reward`";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCredit($data = array()) {
		$sql = "SELECT ct.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, SUM(ct.amount) AS total FROM `" . DB_PREFIX . "customer_transaction` ct LEFT JOIN `" . DB_PREFIX . "customer` c ON (ct.customer_id = c.customer_id) LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_date_start'])) {
			$sql .= "DATE(ct.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= "DATE(ct.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$sql .= " GROUP BY ct.customer_id ORDER BY total DESC";

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

	public function getTotalCredit($data = array()) {
		$sql = "SELECT COUNT(DISTINCT customer_id) AS total FROM `" . DB_PREFIX . "customer_transaction`";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCustomersOnline($data = array()) {
		$sql = "SELECT co.ip, co.customer_id, co.url, co.referer, co.date_added FROM " . DB_PREFIX . "customer_online co LEFT JOIN " . DB_PREFIX . "customer c ON (co.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_ip'])) {
			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "co.customer_id > 0 AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " ORDER BY co.date_added DESC";

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

	public function getTotalCustomersOnline($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_online` co LEFT JOIN " . DB_PREFIX . "customer c ON (co.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_ip'])) {
			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "co.customer_id > 0 AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getCustomerActivities($data = array()) {
		$sql = "SELECT ca.activity_id, ca.customer_id, ca.key, ca.data, ca.ip, ca.date_added FROM " . DB_PREFIX . "customer_activity ca LEFT JOIN " . DB_PREFIX . "customer c ON (ca.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "ca.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(ca.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(ca.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " ORDER BY ca.date_added DESC";

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

	public function getTotalCustomerActivities($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_activity` ca LEFT JOIN " . DB_PREFIX . "customer c ON (ca.customer_id = c.customer_id)";

		$implode = array();

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		if (!empty($data['filter_ip'])) {
			$implode[] = "ca.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}

		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(ca.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(ca.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function  getCustomerInfo($filter_data){
		if($filter_data['filter_report_type']){
			if($filter_data['filter_report_type'] == 2){
				$sql_q = " and date(A.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
			}else{
				$sql_q = "";
			}

		}

		$sql = "select
		A.customer_id,
		G.name customer_grade,
		A.firstname customer_name,  A.telephone,
		A.merchant_name, A.merchant_address,
		C.bd_name,  A.date_added registe_date,
		D.name area_name, E.bd_name area_bd_name,
		if(O.first_orderdate is null, '',O.first_orderdate) order_first_date,
		if(O.last_orderdate is null, '', O.last_orderdate) order_recent_date,
		if(O.last_orderdate is null, '', datediff(current_date(), date(O.last_orderdate)) ) order_no_dates,
		O.lastorder recen_order
		from oc_customer A
		left join (
			 select A.date_added, A.customer_id, sum(A.sub_total) subtotal, count(A.order_id) orders, min(A.order_id) firstorder,  max(A.order_id) lastorder, min(A.date_added) first_orderdate, max(A.date_added) last_orderdate from
			 oc_order A
			 where  A.order_status_id not in (3)
			 and A.station_id = 2 ".$sql_q."
			 group by A.customer_id order by subtotal desc
		) O on A.customer_id = O.customer_id
		left join oc_x_bd C on C.bd_id = A.bd_id
		left join oc_x_area D on A.area_id = D.area_id
		left join oc_x_bd E on D.bd_id = E.bd_id
		left join oc_customer_group G on A.customer_group_id = G.customer_group_id";

		if($filter_data['filter_report_type']){
			if($filter_data['filter_report_type'] == 1){
				if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
					$sql .= " where date(A.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
				}
			}else{
				if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
					$sql .= " where date(O.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
				}
			}

		}else{
			if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
				$sql .= " where date(A.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
			}
		}

		if($filter_data['filter_bd_list']){
			$sql .= " and A.bd_id = '".$filter_data['filter_bd_list']."' ";
		}

		if($filter_data['filter_customer_id']){
			$sql .= " and A.customer_id in (".$filter_data['filter_customer_id'].")";
		}

		if($filter_data['filter_customer_name']){
			$sql_c = "select customer_id from oc_customer where firstname like '"."%".$filter_data['filter_customer_name']."%"."'";
			$query_c = $this->db->query($sql_c)->rows;
			if($query_c){
				$sql .= " and A.customer_id = '". $query_c[0]['customer_id'] ."'";
			}else{
				$sql .= " and A.customer_id = 0";
			}
		}

		if($filter_data['filter_customer_group_id']){
			$sql .= " and G.customer_group_id = '".$filter_data['filter_customer_group_id'] ."'";
		}

		if($filter_data['filter_bd_area_list']){
			$sql .= " and A.area_id = '". $filter_data['filter_bd_area_list'] ."'";
		}

		$sql .= " group by A.customer_id order by A.bd_id";

		$query = $this->db->query($sql);

		if($query){
			$return['customers'] = $query->rows;
		}

		return $return;
	}
}