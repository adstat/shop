<?php
ini_set('display_errors',0);

define('APIOROGIN', 1); //API
define('APIKEY', 'wdf23447dkm316bf519d2juh5e47md56');

//define('APIURL', 'http://localhost/b2b/api/xmlrpc/v1/index.php');
define('APIURL', 'http://b2b.xianshiji.com/api/xmlrpc/v1/index.php');

define('DIR_ROOT', dirname(__FILE__).'/');
define('DIR_PATH',DIR_ROOT . '../../');

//??PHP-XMLRPC?
require_once DIR_PATH.'/api/xmlrpc/v1/xmlrpc.php';
require_once DIR_PATH.'/api/xmlrpc/v1/xmlrpcs.php';
require_once DIR_PATH.'/api/xmlrpc/v1/xmlrpc_wrappers.php';

$client = new xmlrpc_client(APIURL);

$today = date('Y-m-d', time()+8*60*60);



function api($method, $data='', $station_id = 1 ){
    
   
    global $client;
    $method = 'soa.'.$method;
    $msg=new xmlrpcmsg($method);
    $msg->addParam(new xmlrpcval($data, "string")); //Notice id
    $msg->addParam(new xmlrpcval($station_id, "int")); //Station id
    $msg->addParam(new xmlrpcval(2, "int")); //Language id
    $msg->addParam(new xmlrpcval(APIOROGIN, "int")); //Origin id
    $msg->addParam(new xmlrpcval(APIKEY, "string")); //Key

    $response=$client->send($msg);

    if($response->faultcode()==0) {
        return (php_xmlrpc_decode($response->value()));
    }
    else{
        exit('ERROR: '.$response->faultcode().', '.$response->faultstring().'');
    }
}


$method = isset($_POST['method']) ? $_POST['method'] : 0;
$data = isset($_POST['data']) ? $_POST['data'] : 0;
$station_id = isset($_POST['station_id']) ? $_POST['station_id'] : 0;
if($method == "inventoryReturn"){
    
    api($method,$data,$station_id);
}


?>