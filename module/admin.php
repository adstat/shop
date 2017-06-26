<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');

class ADMIN{
    function getCustomerOrderInfo(array $data){
        global $db;

        $customer_id = isset($data['customer_id']) && $data['customer_id'] ? (int)$data['customer_id'] : 0;
        if(!$customer_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        $credit_total = 0;
        $sql = "SELECT if(sum(amount) is null, 0, round(sum(amount),2)) total_amount
                FROM oc_customer_transaction
                WHERE customer_id='" . $customer_id . "'";
        $query = $db->query($sql);
        if($query->row){
            $creditTotalRaw = $query->row;
            $credit_total = $creditTotalRaw['total_amount'];
        }

        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
            'return_data' => array(
                'credit_total' => $credit_total
            )
        );

        return $return;
    }
}

$admin = new ADMIN();
?>