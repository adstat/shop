<?php
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//var_dump($_REQUEST);
$date = isset($_GET['date']) ? $_GET['date'] : false;
$end = isset($_GET['end']) ? $_GET['end'] : false;

if(!$date){
    $h_now = date("H",time());
    if($h_now >= 12){
        $date = date("Y-m-d",time());
    }
    else{

        $date = date("Y-m-d",time() - 24*3600);
    }
}

$gap=round((strtotime($end)-strtotime($date))/3600/24) ;
if($gap > 31){
    exit('查询区间不可超过31天, 超过时间可多分次获取。');
}

$inventory_user_admin = array('alex','leibanban','wangshaokui','wuguobiao','wuguobiaosx');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器

    header("Location: inventory_login.php?return=w2.php");

    //确保重定向后，后续代码不会被执行
    exit;
}

$warehouse_id = isset($_COOKIE['warehouse_id']) ? $_COOKIE['warehouse_id'] : false;
if(!$warehouse_id){
    exit("未设置仓库登录属性，请在分拣页面重新登录");
}


//当前日期
$h_now = date("H",time());
$today = date("Y-m-d 00:00:00", time());

if($h_now >= 10){
    $checkStart = date("Y-m-d 07:00",time());
}
else{
    $checkStart = date("Y-m-d 19:00",time() - 24*3600);
}
$checkEnd = date("Y-m-d H:00",time());
$checkStart = isset($_POST['checkStart']) ? $_POST['checkStart'] : $checkStart;
$checkEnd = isset($_POST['checkEnd']) ? $_POST['checkEnd'] : $checkEnd;
$orderId = isset($_POST['orderId']) ? $_POST['orderId'] : 0;
$checkOrder = isset($_POST['checkOrder']) && $_POST['checkOrder']=='on' ? true : false;


$station_id = isset($_POST['station_id']) ? $_POST['station_id'] : 2;

//转换为标准日期格式
$checkStart = date('Y-m-d H:i:s', strtotime($checkStart));
$checkEnd = date('Y-m-d H:i:s', strtotime($checkEnd));


//如果查询时间非当天，则查找备份库
if(strtotime($checkEnd) < strtotime($today)){
    $db = new DB(DB_LASTDAY_DRIVER, DB_LASTDAY_HOSTNAME, DB_LASTDAY_USERNAME, DB_LASTDAY_PASSWORD, DB_LASTDAY_DATABASE);
}

//计算时间间隔, 查询日期范围不可超过7天
if(intval(abs(strtotime($checkStart)-strtotime($checkEnd))/86400) > 31){
    echo '<input class="button" type="button" value="返回" onclick="javascript:history.go(-1);">';
    exit(' 查询日期范围不可超过31天， 超过时间可多分次获取。');
}


//$sql = "
//SELECT xis.added_by,sum(xis.quantity) inv_total, MIN(xis.uptime) as inv_start_time, max(xis.uptime) as inv_end_time,
//sum(if(p.repack=0,xis.quantity,0)) box_count, sum(if(p.repack=1,xis.quantity,0)) non_box_count
//FROM oc_x_inventory_order_sorting as xis
//LEFT JOIN oc_product as p on p.product_id = xis.product_id
//WHERE
//	xis.uptime between '" . $checkStart . "' and '".$checkEnd."' GROUP BY xis.added_by";
//
//$query = $db->query($sql);
//$result = $query->rows;

if($checkOrder && $orderId){
    $sql = "select a.order_id,  a.product_id,  b.name, a.added_by,  a.uptime, a.quantity, a.move_flag from oc_x_inventory_order_sorting a
               left join oc_product b on a.product_id = b.product_id
               where a.status = 1 and  a.order_id = '".$orderId."'";
    $query = $db->query($sql);
    $result = $query->rows;
}
else{
    $sql = "
    SELECT xis.added_by,sum(xis.quantity) inv_total, MIN(xis.uptime) as inv_start_time, max(xis.uptime) as inv_end_time,
    sum(if(p.repack=0,xis.quantity,0)) box_count,  sum(if(p.repack=1 && p.is_repack = 0 ,xis.quantity /p.sale_start_quantity,0)) non_box_count ,sum(if(p.is_repack=1 ,xis.quantity /p.sale_start_quantity,0)) non_box_count2 , wu.warehouse_id
    FROM oc_x_inventory_order_sorting as xis
    LEFT JOIN oc_w_user wu on xis.added_by = wu.username
    LEFT JOIN oc_product as p on p.product_id = xis.product_id
    WHERE xis.status = 1 and 
	xis.uptime between '" . $checkStart . "' and '".$checkEnd."' and wu.warehouse_id = '".$warehouse_id."'
	GROUP BY xis.added_by";

    $query = $db->query($sql);
    $result = $query->rows;
}
$productId=$_REQUEST['productId'];
$warehouse_id=$_COOKIE['warehouse_id'];
if ($productId){
    $sql="SELECT
                o.order_id,
                ox1.title,
                o.deliver_date,
                os.`name` orderStatu,
                oo.deliver_order_id,
                oos.`name` deliverStatu,
                ox2.title deliverHouse,
                op.product_id ,
                oop. NAME productName,
                op.quantity,
                ios.quantity deliverNum,
                oo.date_added deliverDate
            FROM
                oc_order o
            LEFT JOIN oc_order_status os ON os.order_status_id = o.order_status_id
            LEFT JOIN oc_x_deliver_order oo ON oo.order_id = o.order_id
            LEFT JOIN oc_x_warehouse ox1 ON ox1.warehouse_id = o.warehouse_id
            LEFT JOIN oc_x_warehouse ox2 ON ox2.warehouse_id = oo.do_warehouse_id
            LEFT JOIN oc_x_deliver_order_status oos ON oos.order_status_id = oo.order_status_id
            LEFT JOIN oc_x_deliver_order_product op ON op.deliver_order_id = oo.deliver_order_id
            LEFT JOIN (
                SELECT
                    deliver_order_id,
                    product_id,
                    sum(quantity) AS quantity
                FROM
                    oc_x_inventory_order_sorting i
                LEFT JOIN oc_order d ON d.order_id = i.order_id
                WHERE
                    d.date_added BETWEEN '$checkStart'
                AND '$checkEnd'
                AND i.product_id IN ($productId)
                AND STATUS = 1
                GROUP BY
                    deliver_order_id,
                    product_id
            ) ios ON ios.deliver_order_id = oo.deliver_order_id
            AND op.product_id = ios.product_id
            LEFT JOIN oc_product oop ON oop.product_id = op.product_id
            WHERE
                o.date_added BETWEEN '$checkStart'
            AND '$checkEnd'
            AND op.product_id IN ($productId)
            AND oo.do_warehouse_id = '$warehouse_id'
            ORDER BY
                deliverNum DESC";
//    echo $sql;
    $query = $db->query($sql);
    $productList= $query->rows;
}
?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <style>
        html, body, div, object, pre, code, h1, h2, h3, h4, h5, h6, p, span, em,
        cite, del, a, img, ul, li, ol, dl, dt, dd, fieldset, legend, form,
        input, button, textarea, header, section, footer, article, nav, aside,
        menu, figure, figcaption {
            margin: 0;
            padding: 0;
            outline: none
        }

        h1, h2, h3, h4, h5, h6, sup {
            font-size: 100%;
            font-weight: normal
        }

        fieldset, img {
            border: 0;
        }

        input, textarea, select {
            /*-webkit-appearance: none;*/
            outline: none;
        }

        mark {
            background: transparent;
        }

        header, section, footer, article, nav, aside, menu {
            display: block
        }

        .clr {
            display: block;
            clear: both;
            height: 0;
            overflow: hidden;
        }
            /*table {
                border-collapse:collapse;
                border-spacing:0;
            }*/
        ol, ul, li {
            list-style: none;
        }

        em {
            font-style: normal;
        }

        label, input, button, textarea {
            border: none;
            vertical-align: middle;
        }

        html, body {
            width: 100%;
            overflow-x: hidden;
        }

        html {
            -webkit-text-size-adjust: none;
        }

        body {
            text-align: left;
            font-family: Helvetica, Tahoma, Arial, Microsoft YaHei, sans-serif;
            color: #666;
            background-color: #fff;
            font-size: 1em;
        }

        td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            /*font-size: 2em;*/
        }

        th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .button{
            font-size: 1.1rem;
            margin: 0.2rem;
            padding: 0.2rem;
            background-color:#fa6800;
            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.5);
        }

        #infoList td{
            font-size: 1rem;
        }
    </style>
</head>
<body style="padding: 5px;">
<div style="text-align: center; margin: 0 auto;">
    <div><?php echo $_COOKIE['warehouse_title'];?></div>
    <form action="#" method="post">
        <div style="margin: 3px;" class="checkWorker">
            <span>分拣开始时间<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="text" name="checkStart" value="<?php echo $checkStart; ?>"></span>
        </div>
        <div style="margin: 3px;" class="checkWorker">
            <span>分拣结束时间<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="text" name="checkEnd" value="<?php echo $checkEnd; ?>"></span>
        </div>

        <div style="margin: 3px; display: none" class="checkOrder">
            <span>订单号<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="text" name="orderId" value="<?php echo ($orderId ? $orderId : ''); ?>"></span>
        </div>
        <div style="margin: 3px; display: none" class="checkProduct">
            <span>商品号<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="text" name="productId" value=""></span>
        </div>
        <input style="display: none" class="button" type="button" value="返回" onclick="javascript:history.go(-1);">
        查订单<input type="checkbox" name="checkOrder" <?php echo ($checkOrder?'checked="checked"':'')  ?> id="checkOrder" style="padding-left: 10px" onchange="switchCheckOrder()" />
        查商品<input type="checkbox" name="checkProduct" <?php echo ($productId?'checked="checked"':'')  ?>  id="checkProduct" style="padding-left: 10px" onchange="switchCheckProduct()" />
        <input class="button" type="submit" value="查询">
    </form>
    <script>
        $(document).ready(function () {
             
            gaga=$('input[name="checkOrder"]').attr('checked');
            wawa=$('input[name="checkProduct"]').attr('checked');
            if (gaga == 'checked') {
                $("#infoList").show();
            }
            if (wawa == 'checked') {
                $("#ProductInfo").show();
            }
        });
        function switchCheckOrder(){
            var tmp = $("input[name='checkOrder']:checked").val();
            $("#ProductInfo").hide();
            $("#infoList").hide();
            if(tmp == 'on'){
                $("input[name='productId']").val("");
                $("input[name='productId']").hide();
                $(".checkWorker").hide();
                $(".checkOrder").show();
            }
            else{

                $(".checkWorker").show();
                $(".checkOrder").hide();
            }
        }
        function switchCheckProduct(){

            $("#ProductInfo").show();
            var tmp = $("input[name='checkProduct']:checked").val();
            $("#infoList").hide();
            if(tmp == 'on'){
                $("input[name='productId']").show();
                $("input[name='orderId']").val("");
                $(".checkWorker").hide();
                $(".checkOrder").hide();
                $(".checkProduct").show();
            }
            else{
                $("input[name='productId']").hide();
                $("#ProductInfo").hide();
                $(".checkWorker").show();
                $(".checkOrder").hide();

            }
        }

        switchCheckOrder();
        switchCheckProduct();
    </script>
</div>
<?php if(!$productId){ ?>
    <?php if ($orderId){ ?>
        <table id="infoList" style="margin-top: 0.5rem">
            <caption>[订单号#<?php echo $orderId; ?>分拣情况]</caption>
            <tr>
                <td>分拣</td>
                <td>编号</td>
                <td>商品名称</td>
                <td>分拣时间</td>
                <td>数量</td>
                <td>提交</td>
            </tr>

            <?php foreach($result as $key=>$value){?>
                <tr>
                    <td><?php echo $value['added_by'];?></td>
                    <td><?php echo $value['product_id'];?></td>
                    <td><?php echo $value['name'];?></td>
                    <td><?php echo $value['uptime'];?></td>
                    <td><?php echo $value['quantity'];?></td>
                    <td><?php echo $value['move_flag'];?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <table id="infoList">
            <tr>
                <td>分拣人</td>
                <td>分拣数量</td>
                <td>开始分拣时间</td>
                <td>最后分拣时间</td>
                <td>整件数量</td>
                <td>旧散件数量</td>
                <td>新散件数量</td>
            </tr>


            <?php foreach($result as $key=>$value){?>

                <tr>
                    <td><?php echo $value['added_by'];?></td>
                    <td align="center"><?php echo $value['inv_total'];?></td>
                    <td><?php echo $value['inv_start_time'];?></td>
                    <td><?php echo $value['inv_end_time'];?></td>
                    <td align="center"><?php echo $value['box_count']?></td>
                    <td align="center"><?php echo $value['non_box_count']?></td>
                    <td align="center"><?php echo $value['non_box_count2']?></td>
                </tr>
            <?php } ?>

        </table>
            <?php } ?>

<?php }?>


<table hidden id="ProductInfo" style="margin-top: 0.5rem">
    <thad>
        <tr>
            <td>订单号</td>
            <td>订单状态</td>
            <td>出库仓</td>
            <td>分拣日期</td>
            <td>配送日期</td>
            <td>分拣单号</td>
            <td>分拣单状态</td>
            <td>分检仓</td>
            <td>分拣数量</td>
            <td>商品id</td>
            <td>商品名称</td>
            <td>商品数量</td>
        </tr>
    </thad>

    <tbody>
    <?php if ($productList): ?>
    <?php foreach ($productList as $v): ?>
        <tr>
            <td align="center"><?=$v['order_id'];?></td>
            <td align="center"><?=$v['orderStatu'];?></td>
            <td align="center"><?=$v['title'];?></td>
            <td align="center"><?=$v['deliverDate'];?></td>
            <td align="center"><?=$v['deliver_date'];?></td>
            <td align="center"><?=$v['deliver_order_id'];?></td>
            <td align="center"><?=$v['deliverStatu'];?></td>
            <td align="center"><?=$v['deliverHouse'];?></td>
            <td align="center"><?=$v['deliverNum'];?></td>
            <td align="center"><?=$v['product_id'];?></td>
            <td align="center"><?=$v['productName'];?></td>
            <td align="center"><?=$v['quantity'];?></td>
        </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>



</table>

</body>
</html>