<?php
class ModelCatalogActivity extends Model {
	private function _filter_data($data = array()){
		$sql = '';
		if (!empty($data['filter_name'])) {
			$sql .= " AND a.act_name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND a.act_status = '" . (int)$data['filter_status'] . "'";
		}

		if(isset($data['filter_date_start']) && !is_null($data['filter_date_start'])){
			$sql .= " AND DATE(a.date_start) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if(isset($data['filter_date_end']) && !is_null($data['filter_date_end'])){
			$sql .= " AND DATE(a.date_end) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		if (isset($data['filter_station']) && !is_null($data['filter_station'])) {
			$sql .= " AND a.station_id = '" . (int)$data['filter_station'] . "'";
		}
		return $sql;
	}


	public function getTotalActivities($data = array()) {
		$sql = "SELECT COUNT(1) AS total FROM oc_x_activity a";

		$sql .= " WHERE 1=1";

		$sql .= $this->_filter_data($data);

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function getActivities($data = array()) {
		$sql = "SELECT * FROM oc_x_activity a WHERE 1=1";

		$sql .= $this->_filter_data($data);
		// $sql .= " GROUP BY a.act_id";

		$sort_data = array(
			'a.act_name',
			'a.act_status',
			'a.sort_order',
			'a.date_start',
			'a.date_end'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.sort_order";
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
	
	public function addActivity($data) {
		$this->db->query("INSERT INTO oc_x_activity SET act_name= '". $this->db->escape($data['act_name']) ."', station_id = '". $this->db->escape($data['station_id']) ."', act_image = '". $this->db->escape($data['act_image']) ."', act_status = '". (int)$data['act_status'] ."', date_start = '". $this->db->escape($data['date_start']) ."', date_end = '". $this->db->escape($data['date_end']) ."', date_added = NOW(), sort_order = '".$this->db->escape($data['sort_order'])."'");
		
		$activity_id = $this->db->getLastId();
		
		if (isset($data['product_related'])) {
			$this->db->query("DELETE FROM oc_x_activity_product WHERE act_id = '" . (int)$activity_id. "'");
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("INSERT INTO oc_x_activity_product SET act_id = '" . (int)$activity_id . "', product_id = '" . (int)$related_id . "'");
			}
		}

		if(isset($data['warehouse_ids'])){
			foreach ($data['warehouse_ids'] as $warehouse){
				$this->db->query("INSERT INTO oc_x_activity_to_warehouse SET act_id = '" . (int)$activity_id ."', warehouse_id = '" .(int)$warehouse."', station_id = '" .(int)$data['station_id']."'");

			}
		}
		return $activity_id;
	}
	
	public function editActivity($activity_id, $data) {
		$this->db->query("UPDATE oc_x_activity SET act_name= '". $this->db->escape($data['act_name']) ."',  station_id = '". $this->db->escape($data['station_id']) ."', act_image = '". $this->db->escape($data['act_image']) ."', act_status = '". (int)$data['act_status'] ."', date_start = '". $this->db->escape($data['date_start']) ."', date_end = '". $this->db->escape($data['date_end']) ."', sort_order = '".$this->db->escape($data['sort_order'])."' WHERE act_id = '". (int)$activity_id. "'");
		
//		if (isset($data['product_related'])) {
//			$this->db->query("DELETE FROM oc_x_activity_product WHERE act_id = '" . (int)$activity_id. "'");
//
//            $sort = sizeof($data['product_related']);
//            foreach ($data['product_related'] as $related_id) {
//				$this->db->query("INSERT INTO oc_x_activity_product SET act_id = '" . (int)$activity_id . "', product_id = '" . (int)$related_id . "', sort_order = '".(int)$sort--."'");
//			}
//		}

		if (isset($data['product_link'])) {
			$this->db->query("DELETE FROM oc_x_activity_product WHERE act_id = '" . (int)$activity_id. "'");
			foreach ($data['product_link'] as $link){
				$this->db->query("INSERT INTO oc_x_activity_product SET act_id = '" . (int)$activity_id ."', product_id = '" .(int)$link['product_id']."', sort_order = '" .(int)$link['sort_order']."'");

			}
		}

		if(isset($data['warehouse_ids'])){
			$this->db->query("DELETE FROM oc_x_activity_to_warehouse WHERE act_id = '" . (int)$activity_id. "'");
			foreach ($data['warehouse_ids'] as $warehouse){
				$this->db->query("INSERT INTO oc_x_activity_to_warehouse SET act_id = '" . (int)$activity_id ."', warehouse_id = '" .(int)$warehouse."', station_id = '" .(int)$data['station_id']."'");

			}
		}
	}
	
	public function getActivity($act_id) {
		$sql = "SELECT * FROM oc_x_activity a WHERE a.act_id = ". (int)$act_id;
		
		$query = $this->db->query($sql);

		return $query->row;
	}

    public function getActivityRelatedProducts($actId){
        $sql = "select AP.product_id, AP.sort_order, PD.name, ST.name station_name, P.status, P.price, P.class from oc_x_activity_product AP
        inner join oc_product P on AP.product_id = P.product_id
        left join oc_x_station ST on P.station_id = ST.station_id
        left join oc_product_description PD on P.product_id = PD.product_id and PD.language_id = 2
        where AP.act_id = '".$actId."' order by AP.sort_order asc";

        $query = $this->db->query($sql);
        return $query->rows;
    }

	public function getActStation($banner_id){
		$stations = array();
		$sql = "select station_id from oc_x_activity_to_warehouse where act_id =".(int) $banner_id ." group by station_id";
		$query = $this->db->query($sql);
		if(sizeof($query->rows)){
			foreach($query->rows as $value){
				$stations[] = $value['station_id'];
			}
		}

		return $stations;

	}

	public function getActWarehouse($banner_id){
		$warehouses = array();
		$sql = "select warehouse_id from oc_x_activity_to_warehouse where act_id =".(int) $banner_id ." group by warehouse_id";
		$query = $this->db->query($sql);
		if(sizeof($query->rows)){
			foreach($query->rows as $value){
				$warehouses[] = $value['warehouse_id'];
			}
		}

		return $warehouses;
	}

	public function getActivityWarehouse($act_id)
	{
		$act_id    = (int)$act_id;
		if($act_id < 0){ return array(); }

		$query = $this->db->query("SELECT warehouse_id FROM oc_x_activity_to_warehouse WHERE act_id = '" . $act_id . "'");
		return $query->rows;
	}

}