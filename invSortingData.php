<?php
require_once '../../api/config.php';
require_once(DIR_SYSTEM.'db.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


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
    exit('查询区间不可超过31天');
}

$inventory_user_admin = array('alex','leibanban','wangshaokui');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器

    header("Location: inventory_login.php?return=w2.php");

    //确保重定向后，后续代码不会被执行
    exit;
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

$station_id = isset($_POST['station_id']) ? $_POST['station_id'] : 2;

//转换为标准日期格式
$checkStart = date('Y-m-d H:i:s', strtotime($checkStart));
$checkEnd = date('Y-m-d H:i:s', strtotime($checkEnd));


//如果查询时间非当天，则查找备份库
if(strtotime($checkEnd) < strtotime($today)){
    $db = new DB(DB_LASTDAY_DRIVER, DB_LASTDAY_HOSTNAME, DB_LASTDAY_USERNAME, DB_LASTDAY_PASSWORD, DB_LASTDAY_DATABASE);
}

//计算时间间隔, 查询日期范围不可超过7天
if(intval(abs(strtotime($checkStart)-strtotime($checkEnd))/86400) >= 7){
    echo '<input class="button" type="button" value="返回" onclick="javascript:history.go(-1);">';
    exit(' 查询日期范围不可超过7天');
}


$sql = "
SELECT xis.added_by,sum(xis.quantity) inv_total, MIN(xis.uptime) as inv_start_time, max(xis.uptime) as inv_end_time,
sum(if(p.repack=0,xis.quantity,0)) box_count, sum(if(p.repack=1,xis.quantity,0)) non_box_count
FROM oc_x_inventory_order_sorting as xis
LEFT JOIN oc_product as p on p.product_id = xis.product_id
WHERE
	xis.uptime between '" . $checkStart . "' and '".$checkEnd."' GROUP BY xis.added_by";

$query = $db->query($sql);
$result = $query->rows;
?>
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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

        #infoList td{
            font-size: 1rem;
        }
        </style>
    </head>
    <body style="padding: 5px;">
        <div style="text-align: center; margin: 0 auto;">
            <form action="#" method="post">
                <div style="margin: 3px;">
                    <span>分拣开始时间<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="datetime" name="checkStart" value="<?php echo $checkStart; ?>"></span>
                </div>
                <div style="margin: 3px;">
                    <span>分拣结束时间<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="datetime" name="checkEnd" value="<?php echo $checkEnd; ?>"></span>
                </div>
                <input class="button" type="button" value="返回" onclick="javascript:history.go(-1);">
                <input class="button" type="submit" value="查询">
            </form>
        </div>
        <table id="infoList">
            <tr>
                <td>分拣人</td>
                <td>分拣数量</td>
                <td>开始分拣时间</td>
                <td>最后分拣时间</td>
                <td>整件数量</td>
                <td>散件数量</td>
            </tr>
            
            
            <?php foreach($result as $key=>$value){?>
                <tr>
                    <td><?php echo $value['added_by'];?></td>
                    <td align="center"><?php echo $value['inv_total'];?></td>
                    <td><?php echo $value['inv_start_time'];?></td>
                    <td><?php echo $value['inv_end_time'];?></td>
                    
                    <td align="center"><?php echo $value['box_count']?></td>
                    <td align="center"><?php echo $value['non_box_count']?></td>
                </tr>
            <?php } ?>
        </table>
    </body>
</html>