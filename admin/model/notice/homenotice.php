<?php
class ModelNoticeHomenotice extends Model{
    public function gethomenotices($data = array())
    {
        $sql = "SELECT A.notice_id,A.title,A.station_id,A.date_start,A.date_end,A.status,
                    GROUP_CONCAT(DISTINCT D.name SEPARATOR ' / ') name,
                    GROUP_CONCAT(DISTINCT C.title SEPARATOR ' / ') warehouse_name
	                FROM oc_x_notice A
                    LEFT JOIN oc_x_notice_to_warehouse B ON A.notice_id = B.notice_id
                    LEFT JOIN oc_x_warehouse C ON B.warehouse_id = C.warehouse_id
                    LEFT JOIN oc_x_station D ON C.station_id = D.station_id WHERE 1 = 1";

        $_condition = $this->_getHomeNoticeCondition($data);
        $sql .= implode('', $_condition);
        $sql .= " GROUP BY A.notice_id";

        $sort_data = array(
            'notice_id',
            'title',
            'station_id',
            'date_start',
            'date_end',
        );
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if( $data['sort'] == 'station_id' ){
                $sql .= " ORDER BY D." . $data['sort'];
            } else {
                $sql .= " ORDER BY A." . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY A.title";
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

    public function getTotalHomenotice($params = array())
    {
        $sql = "SELECT COUNT(DISTINCT A.notice_id) AS total
	                FROM oc_x_notice A
                    LEFT JOIN oc_x_notice_to_warehouse B ON A.notice_id = B.notice_id
                    LEFT JOIN oc_x_warehouse C ON B.warehouse_id = C.warehouse_id
                    LEFT JOIN oc_x_station D ON C.station_id = D.station_id WHERE 1 = 1";

        $_condition = $this->_getHomeNoticeCondition($params);
        $sql .= implode('', $_condition);

        $query  = $this->db->query($sql);
        $result = $query->row;

        return $result['total'];
    }

    public function _getHomeNoticeCondition($data = array())
    {
        $_condition = array();
        if(!empty($data))
        {
            if (!empty($data['name'])) {
                $_condition[] = " AND A.title LIKE '%" . $this->db->escape($data['name']) . "%'";
            }

            if (isset($data['status']) && !is_null($data['status'])) {
                $_condition[] = " AND A.status = '" . (int)$data['status'] . "'";
            }

            if(isset($data['date_start']) && !is_null($data['date_start'])){
                $_condition[] = " AND DATE(A.date_start) >= DATE('" . $this->db->escape($data['date_start']) . "')";
            }

            if(isset($data['date_end']) && !is_null($data['date_end'])){
                $_condition[] = " AND DATE(A.date_end) <= DATE('" . $this->db->escape($data['date_end']) . "')";
            }

            if (isset($data['stations']) && !is_null($data['stations'])) {
                $_condition[] = " AND A.station_id IN(0 , ". (int)$data['stations'] .")";
            }
        }

        return $_condition;
    }

    public function getHomenotice($notice_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM  oc_x_notice WHERE notice_id = '" . (int)$notice_id . "'");
        return $query->row;
    }

    public function getNoticeWarehouse($notice_id)
    {
        $notice_id    = (int)$notice_id;
        if($notice_id < 0){ return array(); }

        $query = $this->db->query("SELECT warehouse_id FROM oc_x_notice_to_warehouse WHERE notice_id = '" . $notice_id . "'");
        return $query->rows;
    }

    public function editHomenotice($notice_id, $data,$userId){
        $this->event->trigger('pre.admin.homenotice.edit', $data);
        $this->db->query("UPDATE oc_x_notice  SET title = '" . $this->db->escape($data['title']) . "', status = '" . (int)$data['status'] . "', station_id = '" .(int)$data['station_id'] ."' , date_start = '". $data['date_start'] . "', date_end = '". $data['date_end'] . "' ,date_updated = NOW(), update_user_id = ' $userId '  WHERE notice_id = '" . (int)$notice_id . "'");

        $this->db->query("DELETE FROM oc_x_notice_to_warehouse WHERE notice_id = ".(int)$notice_id);
        if(sizeof($data['warehouse_ids'])){
            $query       = $this->db->query("SELECT station_id, warehouse_id FROM oc_x_warehouse WHERE warehouse_id IN (". implode(',', $data['warehouse_ids']) .")");
            $station_ids = array();
            foreach($query->rows as $value){
                $station_ids[$value['warehouse_id']] = $value['station_id'];
            }
            foreach($data['warehouse_ids'] as $warehouse_id){
                $station_id = !empty($station_ids[$warehouse_id]) ? (int)$station_ids[$warehouse_id] : 0;
                $this->db->query("INSERT INTO oc_x_notice_to_warehouse SET notice_id = ".(int)$notice_id.",warehouse_id = ".(int)$warehouse_id.",station_id = ".$station_id);
            }
        }

        $this->event->trigger('post.admin.homenotice.edit', $notice_id);
    }

    public function addHomenotice($data,$userId){
        $this->event->trigger('pre.admin.homenotice.add', $data);

        $result = $this->db->query("INSERT INTO  oc_x_notice   SET title = '" . $this->db->escape($data['title']) . "', status = '" . (int)$data['status'] . "'  , station_id = '" .(int)$data['station_id'] ."' ,  date_start = '". $data['date_start'] . "', date_end = '". $data['date_end'] . "' ,date_added = NOW(), add_user_id = ' $userId ' "  );

        if($result && sizeof($data['warehouse_ids'])){
            $insert_id   = $this->db->getLastId();
            $query       = $this->db->query("SELECT station_id, warehouse_id FROM oc_x_warehouse WHERE warehouse_id IN (". implode(',', $data['warehouse_ids']) .")");
            $station_ids = array();
            foreach($query->rows as $value){
                $station_ids[$value['warehouse_id']] = $value['station_id'];
            }
            foreach($data['warehouse_ids'] as $warehouse_id){
                $station_id = !empty($station_ids[$warehouse_id]) ? (int)$station_ids[$warehouse_id] : 0;
                $this->db->query("INSERT INTO oc_x_notice_to_warehouse SET notice_id = ".(int)$insert_id.",warehouse_id = ".(int)$warehouse_id.",station_id = ".$station_id);
            }
        }

        $this->event->trigger('post.admin.homenotice.add');
    }

    public function deleteHomenotice($notice_id){

        $this->event->trigger('pre.admin.homenotice.delete', $notice_id);

        $this->db->query("DELETE FROM oc_x_notice WHERE notice_id = '" . (int)$notice_id . "'");
        $this->db->query("DELETE FROM oc_x_notice_to_warehouse WHERE notice_id = ".(int)$notice_id);

        $this->event->trigger('post.admin.homenotice.delete',$notice_id);
    }
}