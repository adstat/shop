<?php
class ModelMarketingBd extends Model {
    public function getTotalBds($data = array()) {
        $sql = "select count(*) as total from oc_x_bd where 1";

        if (isset($data['filter_bd_name'])) {
            $sql .= " and bd_name like '"."%".$data['filter_bd_name'] ."%"."'";
        }

        if (isset($data['filter_status'])){
            $sql .= " and status = '". $data['filter_status'] ."'";
        }

        if (isset($data['filter_bd_id'])) {
            $sql .= " and bd_id = '". $data['filter_bd_id'] ."'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getBds($data = array()) {
        $sql = "select * from oc_x_bd where 1";

        $sort_data = array(
            'bd_id',
            'bd_name',
            'status'
        );

        if (isset($data['filter_bd_name'])) {
            $sql .= " and bd_name like '"."%".$data['filter_bd_name'] ."%"."'";
        }

        if (isset($data['filter_status'])){
            $sql .= " and status = '". $data['filter_status'] ."'";
        }

        if (isset($data['filter_bd_id'])) {
            $sql .= " and bd_id = '". $data['filter_bd_id'] ."'";
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY bd_id";
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

    public function getBd($bd_id){
        $sql = "select * from oc_x_bd where bd_id = '".(int)$bd_id."'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function editBd($bd_id,$data){
        $this->event->trigger('pre.admin.bd.edit', $data);

        //处理停用BD，须得特殊处理bd_name,crm_username,在后缀上面添加(停用)
        if(!$data['status']){
            if(strpos($data['bd_name'],'(停用)') === false){
                $data['bd_name'] = $data['bd_name'] . "(停用)";
            }
            if(strpos($data['crm_username'],'(停用)') === false){
                $data['crm_username'] = $data['crm_username'] . "(停用)";
            }
        }

        $sql = "update oc_x_bd set bd_code = '".$data['bd_code']."',bd_name = '".$data['bd_name']."', phone = '".$data['phone']."',status = '".$data['status']."',crm_username = '".$data['crm_username']."',sale_access_control = '".$data['sale_access_control']."', wx_id = '".$data['wx_id']."' where bd_id = '".(int)$bd_id."'";

        $this->db->query($sql);

        $this->event->trigger('post.admin.bd.edit', $bd_id);
    }

    public function addBd($data){
        $this->event->trigger('pre.admin.bd.add', $data);

        $sql = "insert into oc_x_bd (`bd_code`,`bd_name`,`phone`,`status`,`crm_username`,`sale_access_control`,`wx_id`)
          values('".$data['bd_code']."','".$data['bd_name']."','".$data['phone']."','".$data['status']."','".$data['crm_username']."','".$data['sale_access_control']."','".$data['wx_id']."')";

        $this->db->query($sql);

        $bd_id = $this->db->getLastId();

        $this->event->trigger('post.admin.bd.add', $bd_id);
    }
}