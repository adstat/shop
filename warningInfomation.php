<?php
/**
 * Created by PhpStorm.
 * User: jshy
 * Date: 2017/3/13
 * Time: 14:45
 */
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;

}
if (isset($_GET['start_time'])){
    print_r($_REQUEST);
    $sql="SELECT GROUP_CONCAT(DISTINCT order_id) order_id FROM oc_x_deliver_order 
    WHERE deliver_date BETWEEN '".$_GET['start_time']." ' and '".$_GET['end_time']." '";
    echo $sql;
    $order_id=$db->query($sql)->rows;
    print_r($order_id);
}



?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪仓库分拣缺货确认表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>


    <link rel="stylesheet"  type="text/css"  href="view/javascript/jquery/datetimepicker/bootstrap.min.css">
    <script type="text/javascript" src="view/javascript/jquery/datetimepicker/bootstrap.min.js"></script>

    <script type="text/javascript" src="view/javascript/jquery/datetimepicker/moment.js"></script>
    <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>

    <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
    <link rel="stylesheet" type="text/css" href="view/css/i.css"/>

</head>

<header class="bar bar-nav">
    <div class="title"> <input type="button" class="invopt" style="background: red;width: 70px; font-size: 15px; " id="return_index"  value="返回" onclick="javascript:history.back(-1);"><span id="small_title" style="width: 70px; font-size: 20px;">异常数据查询</span><input class="invopt"  style="background: red;width: 70px; font-size: 15px;" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();"></div>

</header>
<hr>
<body>
<form action="" method="get">
    <h1>请输入分拣时间</h1>
    开始时间:<input  type="text" name="start_time">
    开始时间:<input  type="text" name="end_time">
    <button type="submit">好的</button>
</form>



<script>


</script>
</body>



