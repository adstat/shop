<?php
class ModelMarketingCustomerActivity extends Model {
    public function addEvent($data = array()) {
        if($data){
            $sql = "insert into oc_x_marketing_event (`title`,`content`,`date_start`,`date_end`,`date_added`)
                values('".$data['title']."','".$data['content']."','".$data['date_start']."','".$data['date_end']."',now())";
            $query = $this->db->query($sql);
        }

    }

    public function editEvent($marketing_event_id, $data = array()) {
        if($data){
            $sql = "update oc_x_marketing_event
                set
                title = '".$data['title']."',
                content = '".$data['content']."',
                date_start = '".$data['date_start']."',
                date_end = '".$data['date_end']."',
                date_added = now()
                where marketing_event_id = $marketing_event_id
                 ";
            $query = $this->db->query($sql);
        }
    }

    public function getTotalActivities($data = array()){
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "x_marketing_event where 1";

        if (isset($data['filter_name'])) {
            $sql .= " and title like '"."%".$data['filter_name'] ."%"."'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getActivities($data = array()){
        $sql = "select * from oc_x_marketing_event where 1";

        $sort_data = array(
            'marketing_event_id',
        );

        if (isset($data['filter_name'])) {
            $sql .= " and title like '"."%".$data['filter_name'] ."%"."'";
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY marketing_event_id";
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

    public function getActivity($marketing_event_id){
        $sql = "select * from oc_x_marketing_event where marketing_event_id = '".$marketing_event_id."'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function getSignUpList($marketing_event_id, $start = 0, $limit = 10){
        if ($start<0) {
            $start = 0;
        }
        if ($limit<1) {
            $limit = 10;
        }
        $sql = "select e.customer_id customer_id, e.contact_name name, e.contact_phone telephone, b.bd_name bd_name, e.date_added date_added
                from oc_x_marketing_event_signup e
                left join oc_customer c on c.customer_id = e.customer_id
                left join oc_x_bd b on b.bd_id = c.bd_id
                where e.marketing_event_id = '".$marketing_event_id."'
				limit $start,$limit";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getTotalSignUpList($marketing_event_id) {
        $sql = "select count(*) total from oc_x_marketing_event_signup where marketing_event_id = '".$marketing_event_id."'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }
}