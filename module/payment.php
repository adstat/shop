<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');

class Payment{
    public function paymentBillNotify($data){
        global $dbm;
        $bill_id = (int)$data['bill_id'];
        if(!$bill_id){
            return array(
                'return_code' => 'FAIL',
                'return_msg'  => '请求错误'
            );
        }

        $transaction_id = $dbm->escape($data['transaction_id']);
        $payment_code   = $dbm->escape($data['payment_code']);
        $amount         = (float)$data['amount'];
        $bool = $dbm->query("INSERT INTO oc_x_customer_bill_payment SET bill_id='$bill_id', transaction_id='$transaction_id', payment_code='$payment_code', amount='$amount', date_added=NOW()");
        if($bool){
            return array(
                'return_code' => 'SUCCESS'
            );
        }
    }
}

$customer = new Payment();
?>