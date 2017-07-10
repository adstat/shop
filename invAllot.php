<?php
//exit('调试中,未分拣数据暂时停用');

require_once '../../api/config.php';
require_once(DIR_SYSTEM.'db.php');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$inventory_user_admin = array('alex','leibanban','wangshaokui');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=invAllot.php");
    //确保重定向后，后续代码不会被执行
    exit;
}

//当前日期
$h_now = date("H",time());
$today = date("Y-m-d 00:00:00", time());

if($h_now >= 12){
    $checkStart = date("Y-m-d 02:00:00",time());
}
else{
    $checkStart = date("Y-m-d 17:00:00",time() - 24*3600);
}
$checkEnd = date("Y-m-d H:00:00",time());

$checkStart = isset($_POST['checkStart']) ? $_POST['checkStart'] : $checkStart;
$checkEnd = isset($_POST['checkEnd']) ? $_POST['checkEnd'] : $checkEnd;
$displayType = isset($_POST['displayType']) ? $_POST['displayType'] : 1;
$orderStatus = isset($_POST['orderStatus']) ? $_POST['orderStatus'] : 6;

$queryOrderStatus = $orderStatus;
if($orderStatus == 99){
    $queryOrderStatus = '6,8'; //同时查找两种状态
}
$station_id = isset($_POST['station_id']) ? $_POST['station_id'] : 2;

//转换为标准日期格式
$checkStart = date('Y-m-d H:i:s', strtotime($checkStart));
$checkEnd = date('Y-m-d H:i:s', strtotime($checkEnd));

//如果查询时间非当天，则查找备份库
if(strtotime($checkEnd) < strtotime($today)){
    $db = new DB(DB_LASTDAY_DRIVER, DB_LASTDAY_HOSTNAME, DB_LASTDAY_USERNAME, DB_LASTDAY_PASSWORD, DB_LASTDAY_DATABASE);
}

//计算时间间隔, 查询日期范围不可超过7天
if(intval(abs(strtotime($checkStart)-strtotime($checkEnd))/86400) >= 3){
    echo '<input class="button" type="button" value="返回" onclick="javascript:history.go(-1);">';
    exit(' 查询日期范围不可超过3天');
}

//获取时间段内分拣的订单信息
$result = array();
if(sizeof($_POST)){

    //echo $sql.'<hr />';
    //echo $orderListString.'<hr />';

    //默认获取缺货的商品信息
    $sql = "
                select
                    op.product_id,
                    p.name,
                    p.sku,
                    p.inv_class_sort,
                    round(sum(op.quantity)/2,0) qty
                    from oc_order o
                    left join oc_order_product op on o.order_id = op.order_id
                    left join oc_product p on op.product_id = p.product_id
                    where o.date_added between date_add('".$checkStart."', interval 30 minute) and date_add('".$checkEnd."', interval 30 minute)
                    and o.order_status_id != 3 and o.station_id = 2
                    group by p.product_id having qty > 15
                    order by qty desc
                    limit 50
            ";


    $query = $db->query($sql);
    $result = $query->rows;

    //var_dump($sql);
}

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>分拣补货</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
            -webkit-appearance: none;
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
            font-size: 2em;
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

        .message{
            width: auto;
            margin: 0.5em;
            padding: 0.5em;
            text-align: center;

            border-radius: 0.3em;
            box-shadow: 0.2em rgba(0, 0, 0, 0.2);
        }

        .style_green{
            background-color: #117700;
            border: 0.1em solid #006600;
        }

        .style_lightgreen{
            background-color: #8FBB6C;
            border: 0.1em solid #8FBB6C;
        }

        .style_gray{
            background-color:#9d9d9d;
            border: 0.1em solid #888888;
        }

        .style_red{
            background-color:#DF0000;
            border: 0.1em solid #CC0101;
        }

        .style_yellow{
            background-color:#FF6600;
            border: 0.1em solid #df8505;
        }

        .style_light{
            background-color:#fbb450;
            border: 0.1em solid #fbb450;
        }

        .style_ok{
            background-color:#ccffcc;
            border: 0.1em solid #669966;
        }

        .style_error{
            background-color:#ffff00;
            border: 0.1em solid #ffcc33;
        }

        #infoList td{
            font-size: 1rem;
        }

        .tdWhite{
            background-color: #ffffff;
        }

        .font08rem{
            font-size: 0.8rem;
        }

        .font09rem{
            font-size: 0.9rem;
        }

        .font10rem{
            font-size: 1.0rem;
        }

        .font12rem{
            font-size: 1.2rem;
        }

        .font14rem{
            font-size: 1.4rem;
        }

        .order_block{
            border:1px #666 dashed;
            margin:2px 1px;
        }

        .invopt{
            background-color:#eeeeee;
            /*height:2.5em;*/
            line-height: 1em;
            padding: 0.5em 0.5em;
            margin:0.1em 0.1em;
            font-size: 0.8em;
            text-decoration: none;
            border: 0.1em solid #999999;
            border-radius: 0.2em;
            color: #999999;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

    </style>
</head>
<body>
<center><h3 style="margin: 5px">快消仓分拣补货建议</h3></center>
<div style="text-align: center; margin: 0 auto;">
    <?php if(sizeof($_POST)){ ?>
        <?php
        $total = 0;
        foreach($result as $m){
            $total += $m['qty'];
        }
        if($total > 0){
            $messageInfo =  "时间段内共".$total."件商品需要补货。";
            $messageStyle = 'style_light';
        }
        else{
            $messageInfo = "查询分拣时间段内无建议补货。";
            $messageStyle = 'style_ok';
        }
        ?>
        <div class='message <?php echo $messageStyle; ?>'>
            <?php echo $messageInfo; ?>
        </div>
    <?php } ?>

    <form action="#" method="post">
        <div style="margin: 3px;">
            <span>开始时间<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="datetime" name="checkStart" value="<?php echo $checkStart; ?>"></span>
        </div>
        <div style="margin: 3px;">
            <span>结束时间<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="datetime" name="checkEnd" value="<?php echo $checkEnd; ?>"></span>
        </div>
        <input class="button" type="button" value="返回" onclick="javascript:history.go(-1);">
        <input class="button" type="submit" value="查询">
    </form>
</div>

<?php if(sizeof($result)){ ?>
    <table id="infoList" border="0" style="width:100%;"  cellpadding=2 cellspacing=3>
        <tr>
            <td style="width: 2.1rem; display: none">编号</td>
            <td>商品货位</td>
            <td style="width: 3rem">补货量</td>
            <td style="width: 3.2rem">操作</td>
        </tr>
        <?php foreach($result as $m){?>
            <tr>
                <td style="display: none"><?php echo $m['product_id']; ?></td>
                <td><?php echo '<span class="font12rem">货位'.$m['inv_class_sort'].'</span><br /><span class="font08rem">'. $m['name'] . '</span><br /><span class="font08rem">[ID]'.$m['product_id'].'</span>' . '<br /><span class="font09rem">[条码]'.$m['sku'].'</span>';?></td>
                <td class="font14rem"><strong><?php echo $m['qty'];?></strong></td>
                <td><button class="invopt" style="display: inline" >开始</button></td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>
</body>
</html>