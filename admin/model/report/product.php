<?php
class ModelReportProduct extends Model {
	public function getProductsViewed($data = array()) {
		$sql = "SELECT pd.name, p.model, p.viewed FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.viewed > 0 ORDER BY p.viewed DESC";

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

	public function getTotalProductsViewed() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE viewed > 0");

		return $query->row['total'];
	}

	public function reset() {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = '0'");
	}

	public function getPurchased($data = array()) {
		$sql = "SELECT op.name, op.model, SUM(op.quantity) AS quantity, SUM((op.total + op.tax) * op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";

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

		$sql .= " GROUP BY op.model ORDER BY total DESC";

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

	public function getTotalPurchased($data) {
		$sql = "SELECT COUNT(DISTINCT op.model) AS total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";

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

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProductSale($filter_data){
		$return = array();

		//计算商品可售库存
		$sql_i = "SELECT p.product_id, pd.name, p.status, p.safestock, if(i.ori_inv is null, 0, i.ori_inv) ori_inv, if(i.quantity is null, 0, i.quantity) quantity, D.name station, D.station_id
			FROM oc_product AS p
			left join oc_x_station D on p.station_id = D.station_id
			INNER JOIN oc_product_description AS pd ON p.product_id=pd.product_id
			INNER JOIN (SELECT product_id, sum(if(order_id = 0, quantity, 0)) ori_inv, SUM(quantity) AS quantity FROM oc_x_inventory_move_item WHERE status=1 GROUP BY product_id) AS i ON p.product_id=i.product_id
			WHERE pd.language_id=2
			";

		if($filter_data['filter_station_id']){
			$sql_i .= " and p.station_id = '". $filter_data['filter_station_id'] ."'";
		}

		$sql_i .= " ORDER BY product_id DESC";

		$inventory = $this->db->query($sql_i)->rows;

		$ori_inv = array();
		foreach($inventory as $value){
			$ori_inv[$value['product_id']] = $value['quantity'];
		}

		$return['ori_inv'] = $ori_inv;

		//计算期初期末库存
		$sql_p = " ";
		if($filter_data['filter_station_id']){
			$sql_p = " and P.station_id = '". $filter_data['filter_station_id'] ."'";
		}
//		if($filter_data['filter_product_id_name']){
//			$sql_p = " and P.product_id =".$filter_data['filter_product_id_name']."";
//		}

		//期初库存算法为找到起始时间节点之前最近一次仓库盘点，然后只加上截止到结束时间点的采购入库库存以及库存调整
		$start = $filter_data['filter_date_start'] ;
		$end = $filter_data['filter_date_end'];

		$sql_s = "select max(inventory_move_id) id_s from oc_x_stock_move where date(date_added) = '". $start ."'";
		$inventory_move_id_s = $this->db->query($sql_s)->row['id_s'];
		$sql_e = "select max(inventory_move_id) id_e from oc_x_stock_move where date(date_added) = '". $end ."'";
		$inventory_move_id_e = $this->db->query($sql_e)->row['id_e'];

		//计算这个时间段内的所有采购入库以及库存调整
		$sql_s_i = "select
			MI.product_id,
			P.name,
			if(sum(MI.quantity),sum(MI.quantity),0) inv_end
			from oc_x_stock_move M
			left join oc_x_stock_move_item MI on M.inventory_move_id = MI.inventory_move_id
			inner join oc_product P on P.product_id = MI.product_id
			where MI.status = 1 ".$sql_p."  and  (M.inventory_type_id = 11 or M.inventory_type_id = 16)
			and M.inventory_move_id between '".$inventory_move_id_s."' and '".$inventory_move_id_e."'
			group by MI.product_id";

		//查询时间段内所有的采购入库量
		$s_o_inventory = $this->db->query($sql_s_i)->rows;

		$s_inventory_in = array();
		foreach($s_o_inventory as $k=>$v){
			$s_inventory_in[$v['product_id']] = $v;
		}

		$return['inventory_in'] = $s_inventory_in;

		//计算起始时间节点的商品库存
		//计算起始日期时间节点求得期初库存
		$sql_s = "select max(inventory_move_id) id_s from oc_x_stock_move where date(date_added) <= '". $start ."' and inventory_type_id = 14";
		$inventory_move_id_s = $this->db->query($sql_s)->row['id_s'];
		$sql_e = "select max(inventory_move_id) id_e from oc_x_stock_move where date(date_added) = '". $start ."'";
		$inventory_move_id_e = $this->db->query($sql_e)->row['id_e'];

		$sql_s_i = "select
			MI.product_id,
			P.name,
			sum(MI.quantity) inv_end
			from oc_x_stock_move M
			left join oc_x_stock_move_item MI on M.inventory_move_id = MI.inventory_move_id
			left join oc_product P on P.product_id = MI.product_id
			where MI.status = 1 ".$sql_p."
			and M.inventory_move_id between '".$inventory_move_id_s."' and '".$inventory_move_id_e."'
			group by MI.product_id";

		//查询时间起始节点库存作为期初库存
		$s_o_inventory = $this->db->query($sql_s_i)->rows;

		$s_inventory_key = array();
		foreach($s_o_inventory as $k=>$v){
			$s_inventory_key[$v['product_id']] = $v;
		}

		$return['s_inventory'] = $s_inventory_key;

		$sql = "select
			p.product_id,
			pd.name,
			p.price,
			if(p.status=1, '启用','停用') status,
			concat(round(p.weight,0), wd.title)  formate,
			pcdp.category_id f_category_id,
			pcdp.name  first_category,
			pcd.category_id s_category_id,
			pcd.name  second_category,
			sum(op.quantity) quangtity,
			date(min(o.date_added)) sale_date_begin,
			date(max(o.date_added)) sale_date_recent,
			sum(op.quantity)/(datediff(max(o.date_added), min(o.date_added))+1) turnover
			from  oc_order o
			left join oc_order_product op on o.order_id = op.order_id
			left join oc_product p on op.product_id = p.product_id
			left join oc_product_to_category pc on p.product_id = pc.product_id
			left join oc_category_description pcd on pc.category_id = pcd.category_id and pcd.language_id = 2
			left join oc_category c on pc.category_id = c.category_id
			left join oc_category_description pcdp on c.parent_id = pcdp.category_id and pcdp.language_id = 2
			left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id and wd.language_id = 2
			left join oc_product_description pd on p.product_id = pd.product_id and pd.language_id = 2
			";

		if($filter_data['filter_date_start']&&$filter_data['filter_date_end']){
			$sql .= " where date(o.date_added) between '".$filter_data['filter_date_start']."' and '".$filter_data['filter_date_end']."' ";
		}

		if($filter_data['filter_name']){
			$sql .= " and pd.name like '%" . $this->db->escape($filter_data['filter_name']) . "%'";
		}

		if($filter_data['filter_station_id']){
			$sql .= " and o.station_id = '". $filter_data['filter_station_id'] ."'";
		}

		if($filter_data['filter_customer_group_id']){
			$sql .= " and o.customer_group_id = '". $filter_data['filter_customer_group_id'] ."'";
		}

		if($filter_data['filter_customer_id']){
			$sql .= " and o.customer_id in (".$filter_data['filter_customer_id'].")";
		}

		if($filter_data['filter_product_id_name']){
			$sql .= " and p.product_id in (".$filter_data['filter_product_id_name'].")";
		}
		$sql .= " and o.order_status_id not in (3) group by op.product_id ";

		if($filter_data['filter_category_id']){
			$sql_c = "select c.parent_id,c.category_id from oc_category c left join oc_category_description d on d.category_id = c.category_id where d.name = '".$filter_data['filter_category_id']."'";
			$query_c = $this->db->query($sql_c)->rows;
			if(count($query_c) == 1){
				if($query_c[0]['parent_id'] == 0){
					$sql .= " having f_category_id = '".$query_c[0]['category_id']."'";
				}else{
					$sql .= " having s_category_id = '".$query_c[0]['category_id']."'";
				}
			}else if(!count($query_c)){
				$sql .= " having f_category_id = 0";
			}else{
				$f_category = array();
				$s_category = array();
				foreach($query_c as $value){
					if($value['parent_id'] == 0){
						$f_category[] = $value['category_id'];
					}else{
						$s_category[] = $value['category_id'];
					}
				}
				$f_categories = implode(',',$f_category);
				$s_categories = implode(',',$s_category);
				if($f_categories){
					$sql .= " having f_category_id in(".$f_categories.")";
				}
				if($s_categories){
					$sql .= " having s_category_id in(".$s_categories.")";
				}
			}
		}

		if($filter_data['filter_if_category']){
			$sql .= " order by pcdp.category_id desc, pcd.category_id desc, sum(op.quantity) desc";
		}else{
			$sql .= " order by sum(op.quantity) desc";
		}

		$query = $this->db->query($sql);

		if($query){
			$return['sales'] = $query->rows;
		}

		return $return;
	}

	public function getProducts($data = array()) {
		$sql = "select p.product_id, pd.name
			from oc_product p
			left join oc_product_description pd on pd.product_id = p.product_id where 1";

		if(!empty($data['filter_name'])){
			$sql .= " and p.name like '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " order by p.product_id DESC";
		} else {
			$sql .= " order by p.product_id ASC";
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

}