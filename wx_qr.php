<?php
ini_set('display_errors',0);
session_start();

phpinfo();

const APPID = 'wxbd11007804ba3749';
const APPSECRET = '50b49e8cb50297548bb7b49fd905c0cd';
const DIR = '/var/www/wx/xsj/www/admin/';

function getUrl($url,$post=0,$data=''){
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_USERAGENT, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, $post);
    if( isset($data) ){
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    $res = json_decode(curl_exec($curl),2);
    curl_close($curl);

    return $res;
}

function getToken(){
    $file = DIR . 'xsj_acc_token';

    if( file_exists($file) ){
        $tokenInfo = file_get_contents($file);
    }
    else{
        $tokenInfo = createToken();

        $handle = fopen($file, 'w+');
        fwrite($handle, $tokenInfo);
        fclose($handle);
    }

    return $tokenInfo;
}

function createToken(){
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
    $tokenInfo = getUrl($url); //Get JSON, return array

    //return serialized array, string
    return serialize($tokenInfo);
}


if( isset($_REQUEST['go']) && $_REQUEST['id'] ){
    if( !isset($_SESSION['reqTime']) || time()-$_SESSION['reqTime'] > $_SESSION['expires_in']-600){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
        $tokenInfo = getUrl($url);

        $_SESSION['reqTime'] = time();
        $_SESSION['access_token'] = $tokenInfo['access_token'];
        $_SESSION['expires_in'] = $tokenInfo['expires_in'];
    }
    $token = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : '';

    $qrTicketInfo = array();
    if($token <> ''){
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$token;
        $postData = array(
            //"expire_seconds"=> 604800,
            "action_name"=>"QR_LIMIT_SCENE",
            "action_info" => array(
                "scene" => array(
                    "scene_id" => $_REQUEST['id']
                )
            )
        );
        $postDataJson = json_encode($postData);
        $qrTicketInfo = getUrl($url,1,$postDataJson);
    }
    $ticket = isset($qrTicketInfo['ticket']) ? $qrTicketInfo['ticket'] : '';

    $_SESSION["QR"][$_REQUEST['id']] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
}
else{
    $_SESSION["QR"] = array();
}

echo "Request Time: ".date('Y-m-d H:i:s', $_SESSION['reqTime']+8*60*60)."<br />";
//echo "Token: ".$_SESSION['access_token']."<br />";
echo "Token Expired in: ".(7200-600-(time()-$_SESSION['reqTime']))." seconds<br />";
echo "<hr />";

//expire_seconds 该二维码有效时间，以秒为单位。 最大不超过604800（即7天）。
//action_name 二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
//action_info 二维码详细信息
//scene_id 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
//scene_str 场景值ID（字符串形式的ID），字符串类型，长度限制为1到64，仅永久二维码支持此字段
?>
<!DOCTYPE html>
<html dir="ltr" lang="cn">
<head>
<meta charset="UTF-8" />
<title>鲜世纪 - 微信商家二维码</title>
<base href="http://wx.xianshiji.com/www/admin/" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/javascript/bootstrap/less/bootstrap.less" rel="stylesheet/less" />
<script src="view/javascript/bootstrap/less-1.7.4.min.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<script src="view/javascript/jquery/datetimepicker/moment.js" type="text/javascript"></script>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="screen" />
<script src="view/javascript/common.js" type="text/javascript"></script>
<script>
    function getQRCode(){
        $("#getQR").submit();
        return;
    }
</script>

</head>
<body style="margin: 2em;">
    <form id="getQR" action="wx_qr.php" method="post">
        <input type="hidden" name="go" value="1" />
        商家ID(merchant_id):  <input type="text" name="id" value="<?=$_REQUEST['id'];?>" /><button onclick="javascript:getQRCode();">生成二维码</button>
    </form>
    <?php if($ticket<>''){ ?>
        <div align="center">
            <img src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=<?php echo $ticket; ?>" width="256" height="256" /><br />
            永久二维码：<?php echo $postDataJson?> <br />
            <?php
            foreach($_SESSION["QR"] as $key=>$val){
               echo $key."  ".$val.'<br />';
            }
            ?>
        </div>
    <?php } ?>
</body>
</html>