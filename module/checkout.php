<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');

class CHECKOUT{
    private $db;

    public function getShippingDate($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Multi-language
        //TODO Station

        $sdate = array();
        $now = time();
        $sdate [] = array("今天",date("m/d",$now),date("Y-m-d",$now) );
        $sdate [] = array("明天",date('m/d' ,strtotime('+1 day',$now)),date('Y-m-d' ,strtotime('+1 day',$now)) );
        $sdate [] = array("后天",date('m/d' ,strtotime('+2 day',$now)),date('Y-m-d' ,strtotime('+2 day',$now)) );
        $sdate [] = array("大后天",date('m/d' ,strtotime('+3 day',$now)),date('Y-m-d' ,strtotime('+3 day',$now)) );

        return $sdate;
    }

    function getPickupSpot($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Multi-language
        //TODO Station

        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT * FROM oc_x_pickupspot WHERE status=1";

        if($id>0){
            $sql = "SELECT * FROM oc_x_pickupspot WHERE pickupspot_id = {$id} AND status=1";
        }
        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return false;
    }

    public function getPaymentMethod($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Multi-language
        //TODO Station
        //TODO Payment method management

        $arr_payment = array();
        $arr_payment [] = array("余额支付",1);
        $arr_payment [] = array("微信支付",2);

        return $arr_payment;
    }

}

$checkout = new CHECKOUT();
?>