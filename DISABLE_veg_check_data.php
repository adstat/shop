<?php
require_once '../../api/config.php';
require_once(DIR_SYSTEM.'db.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if($_GET['date']){
    $date = $_GET['date'];
}
else{
     $date = date("Y-m-d",time() + 8*3600);
    /*
    $h_now = date("H",time() + 8*3600);
    if($h_now >= 12){
        $date = date("Y-m-d",time() + 8*3600);
    }
    else{

        $date = date("Y-m-d",time() + 8*3600 - 24*3600);
    }
     * 
     */
}

$sql = "SELECT
	smi.product_id,
	p.`name`,
	abs(sum(smi.quantity)) as sum_quantity,
	sum(smi.weight) as sum_weight
FROM
	oc_x_stock_move_item AS smi
LEFT JOIN oc_x_stock_move AS xsm ON xsm.inventory_move_id = smi.inventory_move_id

LEFT JOIN oc_product AS p ON p.product_id = smi.product_id
WHERE
	xsm.inventory_type_id = 17 
        and xsm.date_added > '" . $date . " 00:00:00'
        and xsm.date_added < '" . $date . " 15:00:00'

GROUP BY
	smi.product_id";


$query = $db->query($sql);
$result = $query->rows;
//echo "<pre>";print_r($result);exit;

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
         盘点日期：<?php echo $date;?>
        <input style="font-size: 2em;" type="button" value="刷新" onclick="javascript:location.reload();">
        <table>
            <tr>
                <td>商品ID</td>
                <td>商品名称</td>
                <td>库存数量</td>
                <td>库存重量</td>
                
            </tr>
            
            
            <?php foreach($result as $key=>$value){?>
                <tr>
                    <td><?php echo $value['product_id'];?></td>
                    <td><?php echo $value['name'];?></td>
                    <td><?php echo $value['sum_quantity'];?></td>
                    <td><?php echo round($value['sum_weight']/500,2);?> &nbsp; 斤</td>
                </tr> 
            <?php } ?>
        </table>
    </body>
</html>