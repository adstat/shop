<?php
//exit('生鲜生产数据，2017-04-20停用');

require_once '../../api/config.php';
require_once(DIR_SYSTEM.'db.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if( !isset($_GET['auth']) || $_GET['auth'] !== 'xssx2017'){
    exit('Not authorized!');
}


if($_GET['date']){
    $date = $_GET['date'];
}
else{
     $date = date("Y-m-d",time() + 8*3600 - 24 * 3600);
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

//2.0
$sql = "SELECT
	p.product_id,
	pg.produce_group_id,
	pg.work_time_start,
	pg.work_time_end,
        pg.produce_type_id
        FROM
                oc_product AS p
        LEFT JOIN oc_x_produce_group AS pg ON p.produce_group_id = pg.produce_group_id
        WHERE
	p.product_id < 4000";

$query = $db->query($sql);
$result = $query->rows; 
$product_to_produce = array();
$produce_group_work_time_arr  = array();
foreach($result as $key=>$value){
    $product_to_produce[$value['product_id']] = $value['produce_group_id'];
    $produce_group_work_time_arr['work_time_start'] = $value['work_time_start'];
    $produce_group_work_time_arr['work_time_end'] = $value['work_time_end'];
    $produce_group_work_time_arr['produce_type_id'] = $value['produce_type_id'];
}









$sql = "SELECT
	ios.product_id,
	p.`name`,
	ios.quantity,
	pg.title as produce_group_name,
        pg.produce_type_id,
        p.produce_group_id,
        ios.product_barcode,
        op.weight_inv_flag
FROM
	oc_x_inventory_order_sorting as ios 
LEFT JOIN oc_order AS o ON o.order_id = ios.order_id
left join oc_order_product as op on op.order_id = ios.order_id and op.product_id = ios.product_id
LEFT JOIN oc_product AS p ON p.product_id = ios.product_id
left join oc_product_to_category as ptc on ptc.product_id = p.product_id
left join oc_x_produce_group as pg on p.produce_group_id = pg.produce_group_id
WHERE
	o.deliver_date = '" . date("Y-m-d",  strtotime($date) + 3600 * 24) . "'
and p.product_id < 4000
and ptc.category_id not in (72,74,157)
and ios.move_flag = 1

order by p.produce_group_id        
";

//echo $sql . "<br><br>";
$query = $db->query($sql);
$result = $query->rows;
$product_2 = array();


foreach($result as $key=>$value){
    if(isset($product_2[$value['product_id']])){
        $product_2[$value['product_id']]['quantity'] += $value['quantity'];
        $product_2[$value['product_id']]['product_barcode'] = array_merge($product_2[$value['product_id']]['product_barcode'],json_decode($value['product_barcode']));
        
    }
    else{
        $value['product_barcode'] = json_decode($value['product_barcode']);
        $product_2[$value['product_id']] = $value;
    }
}


//echo "<pre>";print_r($product_2);


//1.0
$sql = "SELECT
	ios.product_id,
	p.`name`,
	ios.quantity,
        ios.product_barcode
FROM
	xsj.oc_x_inventory_sorting as ios
LEFT JOIN xsj.oc_product AS p ON p.product_id = ios.product_id
left join oc_product_to_category as ptc on ptc.product_id = p.product_id

WHERE
	ios.uptime > '" . $date . " 12:00:00'
and ios.uptime < '" . date("Y-m-d",  strtotime($date) + 3600*24)  . " 12:00:00'
and ptc.category_id not in (72,74,157)           
and ios.move_flag = 1

";
//echo $sql . "<br><br>";
$query = $db->query($sql);
$result2 = $query->rows;


foreach($result2 as $key2=>$value2){
    if(isset($product_2[$value2['product_id']])){
        $product_2[$value2['product_id']]['quantity'] += $value2['quantity'];
        $product_2[$value2['product_id']]['product_barcode'] = array_merge($product_2[$value2['product_id']]['product_barcode'],  json_decode($value2['product_barcode']));
    }
    else{
        $value2['product_barcode'] = json_decode($value2['product_barcode']);
        $product_2[$value2['product_id']] = $value2;
    }
}



//盘点剩余
$sql = "SELECT
	ios.product_id,
	p.`name`,
	ios.quantity,
        ios.product_barcode
FROM
	oc_x_inventory_veg_check_sorting as ios
LEFT JOIN oc_product AS p ON p.product_id = ios.product_id
left join oc_product_to_category as ptc on ptc.product_id = p.product_id

WHERE
	ios.uptime > '" . $date . " 12:00:00'
and ios.uptime < '" . date("Y-m-d",  strtotime($date) + 3600*24)  . " 12:00:00'
and ptc.category_id not in (72,74,157)           
and ios.move_flag = 1

";
//echo $sql . "<br><br>";
$query = $db->query($sql);
$result3 = $query->rows;


foreach($result3 as $key3=>$value3){
    if(isset($product_2[$value3['product_id']])){
        $product_2[$value3['product_id']]['quantity'] += $value3['quantity'];
        $product_2[$value3['product_id']]['product_barcode'] = array_merge($product_2[$value3['product_id']]['product_barcode'],  json_decode($value3['product_barcode']));
    }
    else{
        $value3['product_barcode'] = json_decode($value3['product_barcode']);
        $product_2[$value3['product_id']] = $value3;
    }
}




//根据时间和称号判断生产分组



$produce_work_day_start = strtotime($date . " 08:00:00" );
$produce_work_day_end = strtotime($date . " 17:00:00" );

$produce_work_night_start = strtotime($date . " 17:00:00" );
$produce_work_night_end = strtotime(date("Y-m-d",strtotime($date) + 24 * 3600) . " 05:00:00" );


//print_r($result2);
//echo "<pre>";print_r($product_2);


$produce_group_work_arr = array();
$produce_group_work_arr[999]['produce_group_title'] = "未知生产分组";


foreach($product_2 as $pkey=>$pvalue){
    $produce_group_work_arr[$pvalue['produce_group_id']]['produce_group_title'] = $pvalue['produce_group_name'];
    $produce_group_work_arr[$pvalue['produce_group_id']]['product'][$pvalue['product_id']] = array(
        "product_id" => $pvalue['product_id'],
        "name" => $pvalue['name'],
        "quantity" => $pvalue['quantity'],
        'barcode_arr' => $pvalue['product_barcode'],
        'weight_inv_flag' => $pvalue['weight_inv_flag']
    );
    
    foreach($pvalue['product_barcode'] as $pbkey=>$pbvalue){
        
        $barcode_time = substr($pbvalue, 11,6);
        
        $barcode_time_h = substr($barcode_time, 0,2);
        $barcode_time_m = substr($barcode_time, 2,2);
        $barcode_time_s = substr($barcode_time, 4,2);
        
        
        
        $date_last_day = $date;
        
        if($pvalue['produce_type_id'] == 1){
            
            $product_produce_time = strtotime($date_last_day . " " . $barcode_time_h . ":" . $barcode_time_m . ":" . $barcode_time_s);
            
            if(!($product_produce_time >= $produce_work_day_start && $product_produce_time <= $produce_work_day_end)  ){
                $produce_group_work_arr[$pvalue['produce_group_id']]['product'][$pvalue['product_id']]['quantity']--;
                $chenghao = "2_" . (string)substr($pbvalue, 9,2);
                $chenghao2 = (string)substr($pbvalue, 9,2);
                if(isset($produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']])){
                    $produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']]['quantity']++;
                    $produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']]['product_time'][] = $product_produce_time;
                }
                else{
                    if($pvalue['weight_inv_flag'] == 0){
                    $produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']] = array(
                        "product_id" => $pvalue['product_id'],
                        "name" => $pvalue['name'],
                            "quantity" => $pvalue['qunatity'],
                            'product_time' => array($product_produce_time)
                        );
                        if($chenghao2 != '25'){
                            break;
                        }
                    }
                    else{
                        $produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']] = array(
                            "product_id" => $pvalue['product_id'],
                            "name" => $pvalue['name'],
                        "quantity" => 1,
                        'product_time' => array($product_produce_time)
                    );
                }
                
            }
                
            }
            else{
                
                $produce_group_work_arr[$pvalue['produce_group_id']]['product'][$pvalue['product_id']]['product_time'][] = $product_produce_time;
            }
        }
        
        
        if($pvalue['produce_type_id'] == 2){
            if((int)$barcode_time_h < 6){
                $product_produce_time = strtotime(date("Y-m-d",  strtotime($date)+24*3600) . " " . $barcode_time_h . ":" . $barcode_time_m . ":" . $barcode_time_s);
            }
            else{
                $product_produce_time = strtotime($date_last_day . " " . $barcode_time_h . ":" . $barcode_time_m . ":" . $barcode_time_s);
            }
            if(!($product_produce_time >= $produce_work_night_start && $product_produce_time <= $produce_work_night_end)  ){
                $produce_group_work_arr[$pvalue['produce_group_id']]['product'][$pvalue['product_id']]['quantity']--;
                $chenghao = "1_" . (string)substr($pbvalue, 9,2);
                $chenghao2 = (string)substr($pbvalue, 9,2);
                if(isset($produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']])){
                    $produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']]['quantity']++;
                    $produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']]['quantity']['product_time'][] = $product_produce_time;
                }
                else{
                    if($pvalue['weight_inv_flag'] == 0){
                    $produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']] = array(
                        "product_id" => $pvalue['product_id'],
                        "name" => $pvalue['name'],
                            "quantity" => $pvalue['quantity'],
                            "product_time" => array($product_produce_time)
                        );
                        if($chenghao2 != '25'){
                            break;
                        }
                    }
                    else{
                        $produce_group_work_arr[999][$chenghao]['product'][$pvalue['product_id']] = array(
                            "product_id" => $pvalue['product_id'],
                            "name" => $pvalue['name'],
                        "quantity" => 1,
                        "product_time" => array($product_produce_time)
                    );
                }
                
            }
                
            }
            else{
                $produce_group_work_arr[$pvalue['produce_group_id']]['product'][$pvalue['product_id']]['product_time'][] = $product_produce_time;
            }
        }
        
        if($pvalue['produce_type_id'] == 3){
            $produce_group_work_arr[$pvalue['produce_group_id']]['product'][$pvalue['product_id']]['product_time'][] = $product_produce_time;
            
        }
        
    }

    
}
ksort($produce_group_work_arr);
//echo "<pre>";print_r($produce_group_work_arr);



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
            font-size: 0.5em;
        }
            td{
                border:1px;
               
                
                font-size: 1em;
            }

            th{
                padding: 0.3em;
                background-color:#8fbb6c;
                color: #000;

            }
        </style>
    </head>
    <body>
         <!-- 生产日期：<?php echo $date;?> 
        <input style="font-size: 2em;" type="button" value="刷新" onclick="javascript:location.reload();">
         -->
        <table>
            <!--
            <tr>
                <td>生产班组</td>
                <td>班组类型_生产称号</td>
                
                <td>商品ID</td>
                <td>商品名称</td>
                <td>出库数量</td>
                <td>生产开始时间</td>
                <td>生产结束时间</td>
            </tr>
            -->
            
            <?php foreach($produce_group_work_arr as $key=>$value){?>
                <?php if($key!=999){ ?>
                    <?php foreach($value['product'] as $p_key=>$p_value){?>
                        <tr>
                            <td><?php echo $value['produce_group_title'];?></td>
                            <td></td>
                            
                            <td><?php echo $p_value['product_id'];?></td>
                            <td><?php echo $p_value['name'];?></td>
                            <td><?php echo $p_value['quantity'];?></td>
                            <td><?php echo date("H:i:s",min($p_value['product_time']));?></td>
                            <td><?php echo date("H:i:s",max($p_value['product_time']));?></td>

                        </tr> 
                    <?php } ?>
                <?php } else{ ?>
                    <?php foreach($value as $key1=>$value1){?>
                        
                            <?php foreach($value1['product'] as $p_key=>$p_value){?>
                        <tr>
                            <td><?php echo $value['produce_group_title'];?></td>
                            <td><?php echo $key1;?></td>
                            
                            <td><?php echo $p_value['product_id'];?></td>
                            <td><?php echo $p_value['name'];?></td>
                            <td><?php echo $p_value['quantity'];?></td>
                            <td><?php echo date("H:i:s",min($p_value['product_time']));?></td>
                            <td><?php echo date("H:i:s",max($p_value['product_time']));?></td>

                        </tr> 
                            <?php } ?> 
                        
                    <?php } ?>    
                <?php } ?>        
            <?php } ?>
        </table>
    </body>
</html>