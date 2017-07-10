<?php
require_once '../../api/config.php';
require_once(DIR_SYSTEM.'db.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if($_GET['change_id']){
    $change_id = $_GET['change_id'];
}
else{
    exit();
}

$sql = "SELECT
	p.product_id,
	p.weight,
	p.weight_inv_flag,
	ocp.change_type_id,
	ocp.product_id,
	ocp.product_name,
	ocp.price,
        ocp.quantity,
	ocp.total,
	ocp.weight_total,
	ocp.weight_change,
	ocp.change_product_credits,
  op.price as o_price,
  op.quantity as o_quantity
FROM
	oc_order_change_product AS ocp
LEFT JOIN oc_order_product AS op ON op.order_id = ocp.order_id
AND op.product_id = ocp.product_id
LEFT JOIN oc_product AS p ON ocp.product_id = p.product_id
WHERE
	ocp.change_id = " . $change_id;


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
                font-size: 1em;
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
        <?php if($result[0]['change_type_id'] == 2){ ?>
            重量差异数据
            <table>
                <tr>
                    <td>商品ID</td>
                    <td>商品名称</td>
                    <td>商品价格</td>
                    <td>订单数量</td>
                    <td>标准重量</td>
                    <td>出库数量</td>
                    <td>出库总重</td>
                    <td>出库重量差值</td>
                    <td>余额变动</td>
                </tr>


                <?php foreach($result as $key=>$value){?>
                    <tr>
                        <td><?php echo $value['product_id'];?></td>
                        <td><?php echo $value['product_name'];?></td>
                        <td><?php echo $value['price'];?></td>
                        <td><?php echo $value['o_quantity'];?></td>
                        <td><?php echo (int)$value['weight'];?></td>
                        <td><?php echo $value['quantity'];?></td>
                        <td><?php echo $value['weight_total'];?></td>
                        <td><?php echo $value['weight_change'];?></td>
                        <td><?php echo $value['change_product_credits'];?></td>
                    </tr> 
                <?php } ?>
            </table>
        <?php } else{ ?>
            价格调整数据
            <table>
                <tr>
                    <td>商品ID</td>
                    <td>商品名称</td>
                    <td>订单数量</td>
                    <td>订单价格</td>
                    <td>出库数量</td>
                    <td>出库价格</td>
                    <td>余额变动</td>
                </tr>


                <?php foreach($result as $key=>$value){?>
                    <tr>
                        <td><?php echo $value['product_id'];?></td>
                        <td><?php echo $value['product_name'];?></td>
                        <td><?php echo $value['o_quantity'];?></td>
                        <td><?php echo $value['o_price'];?></td>
                        <td><?php echo $value['quantity'];?></td>
                        <td><?php echo $value['price'];?></td>
                        <td><?php echo $value['change_product_credits'];?></td>
                    </tr> 
                <?php } ?>
            </table>
        <?php }?>
        
    </body>
</html>