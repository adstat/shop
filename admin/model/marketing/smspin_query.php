<?php
class ModelMarketingSmspinQuery extends Model {

    public function getSmsPin($bd_id, $telephone){
        $sql = "select phone,code,FROM_UNIXTIME(expiration) expiration from oc_x_msg_valid where phone = '".$telephone."'";
        $query = $this->db->query($sql);
        $resultRaw = $query->row;

        $result = array();
        if($resultRaw){
            $result = array(
                'phone' => $resultRaw['phone'],
                'code' => $resultRaw['code'],
                'expiration' => $resultRaw['expiration']
            );

            //Add Query History
            $this->addHistory($bd_id, $telephone);
        }

        return $result;
    }

    private function addHistory($bd_id, $telephone){
        $sql = "INSERT INTO oc_customer_smspin_query_history (bd_id, telephone, code, expiration, added_by, date_added)
            select '".$bd_id."', phone, code, FROM_UNIXTIME(expiration) expiration, '".$this->user->getId()."', now() from oc_x_msg_valid
            where phone = '".$telephone."'";

        @$this->db->query($sql);
    }

    public function getQueryHistory(){
        $sql = "select A.bd_id, B.bd_name, A.telephone, A.code, A.expiration, A.added_by, C.username, A.date_added
        from oc_customer_smspin_query_history A
        left join oc_x_bd B on A.bd_id = B.bd_id
        left join oc_user C on A.added_by = C.user_id
        order by history_id desc limit 100";

        $query = $this->db->query($sql);
        return $query->rows;
    }
}