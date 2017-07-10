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

/*
$sql = "SELECT
	o_sorting.added_by,
	sum(o_sorting.quantity) inv_total,
	MIN(o_sorting.uptime) AS inv_start_time,
	max(o_sorting.uptime) AS inv_end_time
FROM
	(
		SELECT
			*
		FROM
	oc_x_inventory_order_sorting
		UNION
			SELECT
				*
			FROM
				oc_x_inventory_order_sorting_history
	) AS o_sorting
WHERE
	o_sorting.uptime > '" . $date . " 12:00:00'
GROUP BY
	o_sorting.added_by";
*/
$sql = "SELECT
	xis.added_by,sum(xis.quantity) inv_total,MIN(xis.uptime) as inv_start_time,max(xis.uptime) as inv_end_time,p.inv_size
FROM
	oc_x_inventory_order_sorting as xis left join oc_product as p on p.product_id = xis.product_id
WHERE
	xis.uptime > '" . $date . " 12:00:00'";

if(!empty($end)){
    $sql .= " and xis.uptime < '" . $end . " 12:00:00' ";
}

$sql .= "group by xis.added_by";

$query = $db->query($sql);
$result = $query->rows;


$sql = "SELECT
	xis.added_by,sum(xis.quantity) inv_total,MIN(xis.uptime) as inv_start_time,max(xis.uptime) as inv_end_time,p.inv_size
FROM
	oc_x_inventory_order_sorting as xis left join oc_product as p on p.product_id = xis.product_id
WHERE
	xis.uptime > '" . $date . " 12:00:00'";

if(!empty($end)){
    $sql .= " and xis.uptime < '" . $end . " 12:00:00' ";
}

$sql .= " and p.inv_size > 1 ";
$sql .= "group by xis.added_by";



$query = $db->query($sql);
$result2 = $query->rows;

$result_inv_size = array();
if(!empty($result2)){
    foreach ($result2 as $k => $v){
        $result_inv_size[$v['added_by']] = $v['inv_total'];
    }
}
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
        </style>
    </head>
    <body style="padding: 5px;">
        <div style="text-align: center; margin: 0 auto;">
            <form action="inv_data.php" method="get">
                <div style="padding: 3px 0;">开始日期: <input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="date" name="date" value="<?php echo $date; ?>"></div>
                <div style="padding: 3px 0;">结束日期: <input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="date" name="end" value="<?php echo $end; ?>"></div>

                <input style="font-size: 1.5rem;" type="button" value="返回" onclick="javascript:history.go(-1);">
                <input style="font-size: 1.5rem;" type="submit" value="查询">
            </form>
        </div>
        <table>
            <tr>
                <td>分拣人</td>
                <td>分拣数量</td>
                <td>开始时间</td>
                <td>最后时间</td>
                <td>单个商品数量</td>
                <td>需打包的商品数量</td>
            </tr>
            
            
            <?php foreach($result as $key=>$value){?>
                <tr>
                    <td><?php echo $value['added_by'];?></td>
                    <td align="center"><?php echo $value['inv_total'];?></td>
                    <td><?php echo $value['inv_start_time'];?></td>
                    <td><?php echo $value['inv_end_time'];?></td>
                    
                    <td align="center"><?php echo $value['inv_total'] - (isset($result_inv_size[$value['added_by']]) ? $result_inv_size[$value['added_by']] : 0);?></td>
                    <td align="center"><?php echo isset($result_inv_size[$value['added_by']]) ? $result_inv_size[$value['added_by']] : 0;?></td>
                </tr> 
            <?php } ?>
        </table>
    </body>
</html>