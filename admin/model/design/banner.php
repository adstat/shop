<?php
class ModelDesignBanner extends Model {
	public function addBanner($data) {
		$this->event->trigger('pre.admin.banner.add', $data);

		$this->db->query("INSERT INTO " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "',date_start = '". $data['date_start'] . "',date_end = '". $data['date_end'] . "'");

		$banner_id = $this->db->getLastId();

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");

				$banner_image_id = $this->db->getLastId();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image_description SET banner_image_id = '" . (int)$banner_image_id . "', language_id = '" . (int)$language_id . "', banner_id = '" . (int)$banner_id . "', title = '" .  $this->db->escape($banner_image_description['title']) . "'");
				}
			}
		}

		//对oc_x_banner_to_warehouse表进行新增
		if(isset($data['station_warehouse']['warehouse'])){
			foreach($data['station_warehouse']['warehouse'] as $key => $value){
//				$this->db->query("DELETE FROM " . DB_PREFIX . "banner_to_warehouse where banner_id = '".(int)$banner_id."' and warehouse_id = '".(int)$key."'");
				$sql = "INSERT INTO " . DB_PREFIX . "banner_to_warehouse set banner_id = '".(int)$banner_id ."', station_id = '".(int)$value."',warehouse_id = '".(int)$key."'";
				$this->db->query($sql);
			}
		}

		$this->event->trigger('post.admin.banner.add', $banner_id);

		return $banner_id;
	}

	public function editBanner($banner_id, $data) {
		$this->event->trigger('pre.admin.banner.edit', $data);

		$this->db->query("UPDATE " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', date_start = '". $data['date_start'] . "', date_end = '". $data['date_end'] . "' WHERE banner_id = '" . (int)$banner_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int)$banner_id . "'");

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $banner_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");

				$banner_image_id = $this->db->getLastId();

				foreach ($banner_image['banner_image_description'] as $language_id => $banner_image_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image_description SET banner_image_id = '" . (int)$banner_image_id . "', language_id = '" . (int)$language_id . "', banner_id = '" . (int)$banner_id . "', title = '" .  $this->db->escape($banner_image_description['title']) . "'");
				}
			}
		}

		//对oc_x_banner_to_warehouse表进行修改
		if(isset($data['station_warehouse']['warehouse'])){
			$this->db->query("DELETE FROM " . DB_PREFIX . "banner_to_warehouse where banner_id = '".(int)$banner_id."'");
			foreach($data['station_warehouse']['warehouse'] as $key => $value){
				$sql = "INSERT INTO " . DB_PREFIX . "banner_to_warehouse set banner_id = '".(int)$banner_id ."', station_id = '".(int)$value."',warehouse_id = '".(int)$key."'";
				$this->db->query($sql);
			}
		}
		$this->event->trigger('post.admin.banner.edit', $banner_id);
	}

	public function deleteBanner($banner_id) {
		$this->event->trigger('pre.admin.banner.delete', $banner_id);

		$this->db->query("DELETE FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "banner_image_description WHERE banner_id = '" . (int)$banner_id . "'");

		$this->event->trigger('post.admin.banner.delete', $banner_id);
	}

	public function getBanner($banner_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");

		return $query->row;
	}

	public function getBanners($data = array()) {

//		$sql = "SELECT banner_id,A.name,A.station_id,A.status,A.date_start, A.date_end ,B.name as stationname FROM oc_banner A LEFT JOIN oc_x_station B ON A.station_id = B.station_id WHERE 1=1 ";

		$sql = "SELECT A.banner_id,A.name,A.station_id,A.status,A.date_start, A.date_end FROM oc_banner A
				WHERE 1=1";

		if (!empty($data['name'])) {
			$sql .= " AND A.name LIKE '%" . $this->db->escape($data['name']) . "%'";
		}

		if (isset($data['status']) && !is_null($data['status'])) {
			$sql .= " AND A.status = '" . (int)$data['status'] . "'";
		}

		if(isset($data['date_start']) && !is_null($data['date_start'])){
			$sql .= " AND DATE(A.date_start) >= DATE('" . $this->db->escape($data['date_start']) . "')";
		}

		if(isset($data['date_end']) && !is_null($data['date_end'])){
			$sql .= " AND DATE(A.date_end) <= DATE('" . $this->db->escape($data['date_end']) . "')";
		}

//		if (isset($data['stations']) && !is_null($data['stations'])) {
//			$sql .= " AND A.station_id = '" . (int)$data['stations'] . "'";
//		}

		if (isset($data['stations']) && !is_null($data['stations'])) {
			$sql_q= "SELECT banner_id from oc_banner_to_warehouse where station_id = ".(int) $data['stations'];
			$query = $this->db->query($sql_q);
			$station_banner = array();
			if(sizeof($query->rows)){
				foreach($query->rows as $value){
					$station_banner[] = $value['banner_id'];
				}
				$banner_ids = implode(',',$station_banner);

				$sql .= " AND A.banner_id in (".$banner_ids.")";
			}else{
				$sql .= " AND A.banner_id = 0";
			}
		}

		$sort_data = array(
			'banner_id',
			'name',
			'station_id',
			'status',
			'date_start',
			'date_end',
		);

		$sql .= " group by A.banner_id";

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

		$banner_list = $query = $this->db->query($sql)->rows;

		$sql = "
				select w.banner_id,w.station_id,s.name
				from oc_banner_to_warehouse w
				left join oc_banner b on w.banner_id = b.banner_id
				left join oc_x_station s on s.station_id = w.station_id
				group by w.banner_id,w.station_id";

		$station_list = $query = $this->db->query($sql)->rows;

		$station_belongTo_banner = array();

		foreach($station_list as $value){
			$station_belongTo_banner[$value['banner_id']][] = $value;
		}
//		echo '<pre>';
//print_r($station_belongTo_banner);
		foreach($banner_list as &$value){
			if(array_key_exists($value['banner_id'],$station_belongTo_banner)){
				$tpl_station = array();
				foreach($station_belongTo_banner[$value['banner_id']] as $vv){
					$tpl_station[] = $vv['name'];
				}
				$value['station'] = $tpl_station;
			}else{
				$value['station'] = array();
			}
		}
		return $banner_list;
	}

	public function getBannerImages($banner_id) {
		$banner_image_data = array();

		$banner_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "' ORDER BY sort_order ASC");

		foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_description_data = array();

			$banner_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image_description WHERE banner_image_id = '" . (int)$banner_image['banner_image_id'] . "' AND banner_id = '" . (int)$banner_id . "'");

			foreach ($banner_image_description_query->rows as $banner_image_description) {
				$banner_image_description_data[$banner_image_description['language_id']] = array('title' => $banner_image_description['title']);
			}

			$banner_image_data[] = array(
				'banner_image_description' => $banner_image_description_data,
				'link'                     => $banner_image['link'],
				'image'                    => $banner_image['image'],
				'sort_order'               => $banner_image['sort_order']
			);
		}

		return $banner_image_data;
	}

	public function getTotalBanners($data = array()) {
//		$sql = "SELECT count(*) as total FROM oc_banner A LEFT JOIN oc_x_station B ON A.station_id = B.station_id WHERE 1=1 ";
		$sql = "SELECT count(*) as total FROM oc_banner A
				WHERE 1=1";
		if (!empty($data['name'])) {
			$sql .= " AND A.name LIKE '%" . $this->db->escape($data['name']) . "%'";
		}

		if (isset($data['status']) && !is_null($data['status'])) {
			$sql .= " AND A.status = '" . (int)$data['status'] . "'";
		}

		if(isset($data['date_start']) && !is_null($data['date_start'])){
			$sql .= " AND DATE(A.date_start) >= DATE('" . $this->db->escape($data['date_start']) . "')";
		}

		if(isset($data['date_end']) && !is_null($data['date_end'])){
			$sql .= " AND DATE(A.date_end) <= DATE('" . $this->db->escape($data['date_end']) . "')";
		}

//		if (isset($data['stations']) && !is_null($data['stations'])) {
//			$sql .= " AND A.station_id = '" . (int)$data['stations'] . "'";
//		}

		if (isset($data['stations']) && !is_null($data['stations'])) {
			$sql_q= "SELECT banner_id from oc_banner_to_warehouse where station_id = ".(int) $data['stations'];
			$query = $this->db->query($sql_q);
			$station_banner = array();
			if(sizeof($query->rows)){
				foreach($query->rows as $value){
					$station_banner[] = $value['banner_id'];
				}
				$banner_ids = implode(',',$station_banner);

				$sql .= " AND A.banner_id in (".$banner_ids.")";
			}else{
				$sql .= " AND A.banner_id = 0";
			}
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getBannerStation($banner_id){
		$stations = array();
		$sql = "select station_id from oc_banner_to_warehouse where banner_id =".(int) $banner_id ." group by station_id";
		$query = $this->db->query($sql);
		if(sizeof($query->rows)){
			foreach($query->rows as $value){
				$stations[] = $value['station_id'];
			}
		}

		return $stations;

	}

	public function getBannerWarehouse($banner_id){
		$warehouses = array();
		$sql = "select warehouse_id from oc_banner_to_warehouse where banner_id =".(int) $banner_id ." group by warehouse_id";
		$query = $this->db->query($sql);
		if(sizeof($query->rows)){
			foreach($query->rows as $value){
				$warehouses[] = $value['warehouse_id'];
			}
		}

		return $warehouses;
	}


//	private function _filter_data($data = array()){
//
//		$sql = '';
//		if (!empty($data['name'])) {
//			$sql .= " AND A.name LIKE '%" . $this->db->escape($data['name']) . "%'";
//		}
//
//		if (isset($data['status']) && !is_null($data['status'])) {
//			$sql .= " AND A.status = '" . (int)$data['status'] . "'";
//		}
//		if (isset($data['stations']) && !is_null($data['stations'])) {
//			$sql .= " AND A.station_id = '" . (int)$data['stations'] . "'";
//		}
//
//
//		if(isset($data['date_start']) && !is_null($data['date_start'])){
//			$sql .= " AND DATE(date_start) >= DATE('" . $this->db->escape($data['date_start']) . "')";
//		}
//
//		if(isset($data['date_end']) && !is_null($data['date_end'])){
//			$sql .= " AND DATE(date_end) <= DATE('" . $this->db->escape($data['date_end']) . "')";
//		}
//		return $sql;
//	}
}