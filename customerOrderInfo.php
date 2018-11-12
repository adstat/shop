<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/6
 * Time: 16:28
 */
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');

if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
$date = date('Y-m-d');
$warehouse_id = isset($_REQUEST['warehouse_id']) ? (int)$_REQUEST['warehouse_id'] : 21;
$gap = isset($_REQUEST['gap']) ? (int)$_REQUEST['gap'] : 2;
$gap = $gap<=15 ? $gap : 15; //查询不超过15天
$sql = "select warehouse_id, shortname warehouse from oc_x_warehouse where status = 1 and station_id = 2";
$list = $db->query($sql);
$dataWarehouseRaw = $list->rows;
foreach($dataWarehouseRaw as $m){
    $dataWarehouse[$m['warehouse_id']] = $m;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <title>用户退货订单商品</title>
</head>
<body>

</body>
</html>
