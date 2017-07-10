<?php
require_once '../../api/config.php';
require_once(DIR_SYSTEM.'db.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$inventory_user_admin = array('randy','alex','leibanban','yangyang');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器 
    
    header("Location: inventory_login.php?return=w.php"); 
    
    
    //确保重定向后，后续代码不会被执行 
    exit;
}

//当前日期
$h_now = date("H",time());
if($h_now >= 12){
    $today = date("Y-m-d",time() + 24*3600);
}
else{
    $today = date("Y-m-d",time());
}
$date = isset($_GET['date']) ? $_GET['date'] : $today;

$station_id = isset($_GET['station_id']) ? $_GET['station_id'] : 0;


$sql = "SELECT
	od.inventory_name,
	op.order_id,
	op.product_id,
	op. NAME,
	op.quantity,

IF (
	inv_pro.inv_sum IS NULL,
	op.quantity,
	op.quantity - inv_pro.inv_sum
) AS inv_dif
FROM oc_order AS oo
LEFT JOIN oc_order_product AS op ON oo.order_id = op.order_id
LEFT JOIN (
    SELECT odis.order_id, odis.inventory_name
    FROM oc_order o
    LEFT JOIN  oc_order_distr odis
    ON o.order_id = odis.order_id
    WHERE o.deliver_date = '".$date."' AND odis.ordclass in (1,5) GROUP BY odis.order_id, odis.ordclass
) AS od ON od.order_id = oo.order_id
LEFT JOIN (
	SELECT
		ioo.order_id,
		ioo.product_id,
		sum(ioo.quantity) AS inv_sum
	FROM
		oc_x_inventory_order_sorting AS ioo
	LEFT JOIN oc_order AS o ON o.order_id = ioo.order_id
	WHERE
		o.deliver_date = '" . $date . "'
	GROUP BY
		ioo.order_id,
		ioo.product_id
) AS inv_pro ON inv_pro.order_id = op.order_id
AND inv_pro.product_id = op.product_id
WHERE oo.deliver_date = '" . $date . "'
AND oo.order_status_id != 3
AND oo.station_id = '".$station_id."'
";

if(!in_array($_COOKIE['inventory_user'], $inventory_user_admin)){
    $sql .= " and od.inventory_name = '" . $_COOKIE['inventory_user'] . "'";
}

$sql .= " AND (
	op.quantity - inv_pro.inv_sum > 0
	OR inv_pro.inv_sum IS NULL
)";
if($station_id == 1){
    $sql .= " ORDER BY od.inventory_name,op.product_id";
}else{
    $sql .= " ORDER BY op.product_id, od.inventory_name";
}


$result = array();
if($station_id && $date){
    //echo "$sql";
    $query = $db->query($sql);
    $result = $query->rows;
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
    <body>
        <div style="text-align: center; margin: 0 auto;">
           <form action="inv_dif_data.php" method="get">
               <div style="padding: 3px 0;">配送日期: <input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="date" name="date" value="<?php echo $date; ?>"></div>
               <div style="padding: 3px 0;">仓库:
                   <select name='station_id'>
                       <option value="0">请选择</option>
                       <option value="1" <?php if($station_id==1) { echo "selected='selected'"; } ?>>生鲜</option>
                       <option value="2" <?php if($station_id==2) { echo "selected='selected'"; } ?>>快消</option>
                   </select>
               </div>

               <input style="font-size: 1.5rem; margin: 0.5rem" type="button" value="返回" onclick="javascript:history.go(-1);">
               <input style="font-size: 1.5rem; margin: 0.5rem" type="submit" value="查询">
               <input style="font-size: 1.5rem; margin: 0.5rem" type="button" value="刷新" onclick="javascript:location.reload();">
           </form>
        </div>
        <table>
            <tr>
                <td>分拣人</td>
                <td>订单ID</td>
                <td>商品ID</td>
                <td>商品名字</td>
                <td>商品数量</td>
                <td>未出库数量</td>
            </tr>
            
            
            <?php foreach($result as $key=>$value){?>
                <tr>
                    <td><?php echo $value['inventory_name'];?></td>
                    <td><?php echo $value['order_id'];?></td>
                    <td align="center"><?php echo $value['product_id'];?></td>
                    <td><?php echo $value['NAME'];?></td>
                    <td><?php echo $value['quantity'];?></td>
                    <td><?php echo $value['inv_dif'];?></td>
                </tr> 
            <?php } ?>
        </table>
    </body>
</html>