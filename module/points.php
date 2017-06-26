<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');

class POINTS{
    function getPointsRule(array $data){
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        $sql = "select `points_rule_id`, `station_id`, `name`, `basic`, `rate`, `type`, `points`, `date_start`, `date_end`, `valid_weekday`, `status`, `sign_in`, `refer_products`, `refer_categories`, `exclude_prouducts`, `description` from oc_x_points_rule";
        $query = $db->query($sql);
        if($query->row){
            $rules = $query->row;
        }

        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
            'return_data' => array(
                'rules' => $rules
            )
        );

        return $return;
    }
}

$points = new POINTS();
?>