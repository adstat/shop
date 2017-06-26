<meta charset="utf-8">
<?php
ini_set('display_errors',1);

define('APIOROGIN', 7); //API使用途径，7为线下门店设备使用
//define('APIKEY', '4c5701159f352c915f2e824d9d274f25'); //API使用密钥
define('APIKEY', 'dgdf56e450bddf711c98e08e3363c4de'); //API使用密钥 DEMO EKY

//define('APIURL', 'http://demo.xianshiji.com/api/xmlrpc/v1/index.php'); //API路径
//define('APIURL', 'http://api.xianshiji.com/api/xmlrpc/v1/index.php'); //API路径
define('APIURL', 'http://localhost/xsjb2b/api/xmlrpc/v1/index.php'); //API路径

//define('APIURL', 'http://b2b.xianshiji.com/api/xmlrpc/v1/index.php'); //API路径


define('DIR_ROOT', dirname(__FILE__).'/');

//载入PHP-XMLRPC库
require_once DIR_ROOT.'xmlrpc/v1/xmlrpc.php';
require_once DIR_ROOT.'xmlrpc/v1/xmlrpcs.php';
require_once DIR_ROOT.'xmlrpc/v1/xmlrpc_wrappers.php';

//新建客户端
$client = new xmlrpc_client(APIURL);

$method = 'soa.getStation'; //测试方法，已获取门店信息为例
//
////开始构造消息
//$msg=new xmlrpcmsg($method);
//$msg->addParam(new xmlrpcval(1, "int")); //必须参数，查询ID, 部分方法中需要的数据结构负责，需要改为String类型，值为序列化的数组
//$msg->addParam(new xmlrpcval(3, "int")); //必须参数，部分公用方法对返回结果没有影响，门店的ID
//$msg->addParam(new xmlrpcval(2, "int")); //必须参数，客户端环境语言ID，目前默认为2，只支持简体中文
//$msg->addParam(new xmlrpcval(APIOROGIN, "int")); //API使用途径
//$msg->addParam(new xmlrpcval(APIKEY, "string")); //API使用密钥
//
////发送消息并获取结果
//$response=$client->send($msg);
//
////结果的展示和处理
//if($response->faultcode()==0) {
//    //return php_xmlrpc_decode($response->value());
//    exit(var_dump(php_xmlrpc_decode($response->value()))); //默认返回XML格式，使用php_xmlrpc_decode转为数组
//}
//else{
//    exit('ERROR: '.$response->faultcode().', '.$response->faultstring().'');
//}


function init($method, $id, $data='', $station_id, $language_id, $origin_id){
    global $client;
    //$output_options = array("output_type" => "php");

    //Get from XMLRPC API
    $method = 'soa.'.$method;
    $msg=new xmlrpcmsg($method);

    if($data !== ''){
        $msg->addParam(new xmlrpcval($data, "string")); //Notice id
    }
    else{
        $msg->addParam(new xmlrpcval($id, "int")); //Notice id
    }
    //$msg->addParam(new xmlrpcval($data, "string")); //Notice id
    $msg->addParam(new xmlrpcval($station_id, "int")); //Station id
    $msg->addParam(new xmlrpcval($language_id, "int")); //Language id
    $msg->addParam(new xmlrpcval($origin_id, "int")); //Origin id
    $msg->addParam(new xmlrpcval(APIKEY, "string")); //Key

    $response=$client->send($msg);

    if($response->faultcode()==0) {
        exit(json_encode( php_xmlrpc_decode($response->value())) );
    }
    else{
        exit('ERROR: '.$response->faultcode().', '.$response->faultstring().'');
    }
}

function rpcRequest($post){
    if(empty($post['method'])){
        exit('Missing Request Method.');
    }
    global $client;
    $method = 'soa.' . $post['method'];
    $msg    = new xmlrpcmsg($method);

    $request = array(
        'uid'         => isset($post['uid']) ? $post['uid'] : '',
        'customer_id' => isset($post['customer_id']) ? $post['customer_id'] : '',
        'language_id' => isset($post['language_id']) ? $post['language_id'] : '',
        'data' => isset($post['data']) && is_array($post['data']) ? $post['data'] : array(),
    );

    $msg->addParam(new xmlrpcval(json_encode($request), "string"));
    $msg->addParam(new xmlrpcval(APIOROGIN, "int"));                   //Access Origin id
    $msg->addParam(new xmlrpcval(APIKEY, "string"));                      //Access Key
    $response = $client->send($msg);

    if($response->faultcode()==0) {
        $result = php_xmlrpc_decode($response->value());

        //直接发送短信
        if($post['method'] == 'sendMsg'){
            if( $result['return_code'] == 'SUCCESS' && isset($result['phone']) && isset($result['random']) ){
                require_once DIR_SMS.'sms.php';
                $sms = new SMS(SMS_ACC_ID,SMS_ACC_TOKEN,SMS_APP_ID,SMS_SERVER,SMS_SERVER_PORT,SMS_VERSION);
                $sms->sendSms($result['phone'],array($result['random'],STATION_REG_YTX_SMS_CODE_LIFE),STATION_REG_YTX_SMS_TEMPLATE);
            }
        }
        exit(json_encode($result));
    }
    else{
        exit('ERROR: '.$response->faultcode().', '.$response->faultstring().'');
    }
}

//GO
$id = 0;
$station_id = 1;
$language_id = 2;
$origin_id = 7;

init($method, $id, '', $station_id, $language_id, $origin_id);
?>