<?php
if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';

$inventory_user_admin = array('1','22','20','24');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
//if(!in_array($_COOKIE['user_group_id'],$inventory_user_admin)){
//    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
//    exit("入库功能仅限指定人员操作, 请返回");
//}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪仓间调拨</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
    <!-- <script type="text/javascript" src="js/alert.js"></script> -->
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
            /*隐藏checkbox*/
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

        label, input, button {
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

        .invopt{
            background-color:#DF0000;
            height:3em;
            line-height: 1em;
            padding: 0.5em 0.5em;
            margin:0.1em 0.1em;
            font-size: 1em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .invback{
            background-color:#DF0000;
            width:5em;
            height:3em;
            line-height: 1em;
            padding: 0.5em 0.5em;
            margin:0.1em 0.1em;
            font-size: 1em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .qtyopt{
            background-color:#DF0000;
            width:2em;
            height:1.8em;
            line-height: 1.2em;
            padding: 0.1em 0.1em;
            margin:0.1em 0.1em;
            font-size: 1.2em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            float: left;
            font-weight: bold;
        }
        .qtyopt_weight{
            background-color:#DF0000;
            width:2em;
            height:1.8em;
            line-height: 1.2em;
            padding: 0.1em 0.1em;
            margin:0.1em 0.1em;
            font-size: 1.2em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            font-weight: bold;
        }

        .submit{
            background-color: #DF0000;
            padding: 0.3em 0.8em;
            margin:0.1em 0.1em;
            font-size: 1em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            float: right;
        }

        #inventory{
            width: 100%;
        }

        #product{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }


        #frame_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #incubator_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #foam_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #frame_mi_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #incubator_mi_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #frame_ice_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #box_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #frame_merge_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #frame_meat_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #foam_ice_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }


        #input_vg_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_meat_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_mi_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_ice_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_merge_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }




        .addprod{
            cursor: pointer;
            color: #fff;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .qty{
            width:2.3em;
            height:1.2em;
            font-size: 1.5em;
            text-align: center;
            background: none;
        }

        #inv_control{
            padding:0.5em
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

        #productsInfo{
            border: 0.1em solid #888888;
        }
        #productsInfoRelevant{
            border: 0.1em solid #888888;
        }
        #productsInfo1{
            border: 0.1em solid #888888;
        }
        #productsInfo2{
            border: 0.1em solid #888888;
        }

        #station{
            font-size: 1em;
        }

        .message{
            width: auto;
            margin: 0.5em;
            padding: 0.5em;
            text-align: center;

            border-radius: 0.3em;
            box-shadow: 0.2em rgba(0, 0, 0, 0.2);
        }

        .current_do_class td{
            color:red;
            height: 5em;
        }

        #productsHold td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHoldRelevant td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHold1 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHold2 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHoldRelevant th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHold1 th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHold2 th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHoldDo td{
            background-color:#d0e9c6;
            color: #000;
            height: 2em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHoldDo1 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHoldDo2 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHoldDo th{
            padding: 0.3em;
            height: 3em;
            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHoldDo1 th{
            padding: 0.3em;
            height: 3em;
            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHoldDo2 th{
            padding: 0.3em;
            height: 3em;
            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #ordersHold td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        #ordersHold th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #warehouse_change th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }



        .submit_s{
            padding: 0.2em 0.2em;
            margin:0.1em 0.1em;
            font-size: 0.9em;
            text-decoration: none;
            border-radius: 0.2em;
            color:#333;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }


        #invMovesHold th{
            padding: 0.3em;
            background-color:#f0ad4e;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #invMovesHold td{
            background-color:#ffffaa;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #invMovesPrintCaption{
            width: 100%;
            text-align: left;
        }


        #invMovesPrintHold{
            border-right: solid #000 1px;
            border-bottom: solid #000 1px;
        }

        #invMovesPrintHold th{
            padding: 3px;
            /*background-color:#f0ad4e;*/
            color: #000;
            font-size: 12px;
            font-weight: bold;

            border-left: solid #000 1px;
            border-top: solid #000 1px;
            /*border-radius: 0.1em;*/
            /*box-shadow: 0.1em rgba(0, 0, 0, 0.2);*/
        }

        #invMovesPrintHold td{
            /*background-color:#ffffaa;*/
            color: #000;
            height: 15px;
            font-size: 12px;
            padding: 2px;

            border-left: solid #000 1px;
            border-top: solid #000 1px;
            /*border-radius: 0.1em;*/
            /*box-shadow: 0.1em rgba(0, 0, 0, 0.2);*/
        }

        #prtInvMoveType,#prtInvMoveTitle,#prtInvMoveTime{
            color: #000;
            font-weight: bold;
        }

        .simple-player-container {
            display: inline-block;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        .simple-player-container > div > ul {
            margin: 0;
            padding-left: 0;
        }
        .simpleplayer-play-control {
            background-image: url('images/play.png');
            display: block;
            width: 16px;
            height: 16px;
            bottom: -5px;
            position: relative;
        }
        .simpleplayer-play-control:hover {
            background-image: url('images/playing.png');
        }
        .simpleplayer-stop-control {
            background-image: url('images/stop.png');
            display: block;
            width: 16px;
            height: 16px;
            bottom: -5px;
            position: relative;
        }
        .simpleplayer-stop-control:hover {
            background-image: url('images/stoped.png');
        }

        input::-webkit-input-placeholder {
            color: #CC0000;
        }
        input:-moz-placeholder {
            color: #CC0000;
        }

        input#product{
            height: 1.2em;
            font-size: 1.1em;
        }

        .productBarcode{font-size:14px;}

        .prodlist{font-size: 1.4em;}

        #overlay {
            background: #000;
            filter: alpha(opacity=50); /* IE的透明度 */
            opacity: 0.5;  /* 透明度 */
            display: none;
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            z-index: 100; /* 此处的图层要大于页面 */
            display:none;
        }

        .frame_num{
            background-color: yellow;
            font-size: 1.8em;
        }

        button{
            font-size: 0.8rem;
            padding: 0.2rem;
        }

    </style>

    <style media="print">
        .noprint{display:none;}
    </style>

    <script>
        var global = {};
        global.warehouse_array = {};
        global.relevant_id = 0;
        window.product_barcode_arr = {};
        window.product_barcode_arr_s = {};
        <?php if(!in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
        $(document).keydown(function (event) {
            $('#product').focus();
        });
        <?php } ?>
    </script>
</head>

<body>
<script type="text/javascript">
    var is_admin = 0;
</script>
<div align="right" style="margin: 0.2rem">
    <div id ="inventory_user"><?php echo $_COOKIE['inventory_user'];?></div><input type="hidden" id="inventory_user_id" value="<?php echo $_COOKIE['inventory_user_id'];?>"/> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <button onclick="javascript:logout_inventory_user();">退出</button>
</div>
<div align="center" id="purchase_info"></div>
<div style="display: none" id="warehouse_id"> <?php echo $_COOKIE['warehouse_id'];?></div>
<div style="display: none" id="local_warehouse_name"><?php echo $_COOKIE['warehouse_title'];?></div>
<div align="center" style="display:block; margin:0.5em auto" id="logo"><img src="view/image/logo.png" style="width:6em"/> 调拨操作<button class="invopt" style="display: inline;float:left" onclick="window.location.href='i.php?auth=xsj2015inv&ver=db'">返回</button><button class="invopt" style="display: inline" onclick="javascript:location.reload();">刷新</button></div><br />
<div align="center" style="display:block; margin:0.5em auto" id="show_order_comment"></div>
<?php

if(isset($_GET['date'])){
    $date_array = array();

    $date_array[0]['date'] = date("m-d",  strtotime($_GET['date']));
    $date_array[1]['date'] = date("m-d",strtotime($_GET['date']));
    $date_array[2]['date'] = date("Y-m-d",strtotime($_GET['date']));
    $date_array[2]['shortdate'] = date("m-d",strtotime($_GET['date']));
}
else{
    $date_array = array();

    $date_array[0]['date'] = date("m-d",time());
    $date_array[1]['date'] = date("m-d",time() + 24*3600);
    $date_array[2]['date'] = date("Y-m-d",time());
    $date_array[2]['shortdate'] = date("m-d",time()  + 9*3600);

}


$selete_date = array();
$today_date = $date_array[2]['date'];

$selete_date[] = '';

for($i = 14; $i >= 0 ;$i--){
    $cur_date = date("Y-m-d", strtotime($today_date . " 00:00:00") - $i * 24 * 3600);
    $selete_date[] = $cur_date;

}

for($i = 1; $i <=14 ;$i++){
    $cur_date = date("Y-m-d", strtotime($today_date . " 00:00:00") + $i * 24 * 3600);
    $selete_date[] = $cur_date;

}

?>


<hr />
<div id="productListsRelevant">
<div id="searchPurchaseOrder">
    <span style="width: 3em">调拨日期：</span>
    <input id="date_start" name="date_start"  autocomplete="off" class="date" type="text" style="font-size: 15px; width:8em;height:1.5em;border:1px solid" data-date-format="YYYY-MM-DD-HH" value="<?php echo date("Y-m-d",time());?>"/>
    <input type="hidden" id="last1_day_time" value="<?php echo date("Y-m-d",strtotime("-1 Days"));?>"/>
    <input type="hidden" id="last2_day_time" value="<?php echo date("Y-m-d",strtotime("-2 Days"));?>"/>
    <input type="hidden" id="last3_day_time" value="<?php echo date("Y-m-d",strtotime("-3 Days"));?>"/>
    <input type="hidden" id="next1_day_time" value="<?php echo date("Y-m-d",strtotime("+1 Days"));?>"/>
    <input type="hidden" id="next2_day_time" value="<?php echo date("Y-m-d",strtotime("+2 Days"));?>"/>
    <input type="hidden" id="next3_day_time" value="<?php echo date("Y-m-d",strtotime("+3 Days"));?>"/>
    <input type="hidden" id="next0_day_time" value="<?php echo date("Y-m-d",time());?>"/>
    &nbsp;<select id="date_change" style="font-size: 15px;width:5em;height:1.5em;border:1px solid" onchange="javascript:change_date_time();">
        <option value="last3_day_time">大前天</option>
        <option value="last2_day_time">前天</option>
        <option value="last1_day_time">昨天</option>
        <option value="next0_day_time" selected>今天</option>
        <option value="next1_day_time">明天</option>
        <option value="next2_day_time">后天</option>
        <option value="next3_day_time">大后天</option>
    </select>

    <br />
    <br />
    <script>
        function change_date_time() {
            var date_change = $("#date_change").val();
            $("#date_start").val($("#"+date_change).val());
        }
    </script>
    <span style="width: 3em">结束日期：</span>
    <input id="date_end" name="date_start"  autocomplete="off" class="date" type="text" style="font-size: 15px; width:8em;height:1.5em;border:1px solid" data-date-format="YYYY-MM-DD-HH" value="<?php echo date("Y-m-d",strtotime("+1 Days"));?>"/>
    &nbsp;<select id="date_end_change" style="font-size: 15px;width:5em;height:1.5em;border:1px solid" onchange="javascript:change_date_end_time();">
        <option value="last3_day_time">大前天</option>
        <option value="last2_day_time">前天</option>
        <option value="last1_day_time">昨天</option>
        <option value="next0_day_time">今天</option>
        <option value="next1_day_time" selected>明天</option>
        <option value="next2_day_time">后天</option>
        <option value="next3_day_time">大后天</option>
    </select>

    <br />
    <br />
    <script>
        function change_date_end_time() {
            var date_change = $("#date_end_change").val();
            $("#date_end").val($("#"+date_change).val());
        }
    </script>
<!--    目的仓库：-->
<!--    <select id="to_warehouse" style="font-size: 15px;width:8em;height:1.5em;border:1px solid">-->
<!--    </select>-->
<!--    <br />-->
<!--    <br />-->
    调拨类型：
    <select id="warehouse_out_type" style="font-size: 15px;width:8em;height:1.5em;border:1px solid">
<!--        <option value="2">仓间调拨单</option>-->
<!--        <option value="1">DO单调拨</option>-->
<!--        <option value="4">退货调拨单</option>-->
        <option value="3">仓内调拨单</option>
<!--	<option value="5">调拨到货错误异常收货</option>-->
    </select>
    <br />
    <br />
    调拨单号：
    <input id="inventory_order_sorting" name="input_purchase_relevant_id" style="font-size: 15px;width:8em;height:1.5em;border:1px solid" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" class="style_red" style="font-size:1em;width:3em;height:1.5em; background: red" onclick="javascript:getNewWarehouseRequisition()" value="查询">
    <br />
    <br />
</div>

<div id="login" align="center" style="margin:0.5em auto; display: none">
    <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
    <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
</div>
<hr />
<div id="content" style="display: block">

    <!--    <input id="much_look" type="button" value="批量扫描包裹" style="display:none;width: 90px; height: 30px; background: red" />-->
    <div align="center" id="orderListTable" style="margin:0.5em auto;">
        <input type="hidden" id="current_relevant_id" value="">
        <b>调拨类型：<span id="get_warehouse_out_type"></span></b><input id="createRelevantWarehouse" style="display: none" class="invopt" type="button" value ="生成退货调拨单" onclick="javascript:createRelevantWarehouse();">
        <input id="createIssueRelevantProduct" style="display: none" class="invopt" type="button" value ="调拨商品错误异常收货" onclick="javascript:createRelevantWarehouse();">
        <table border="0" style="width:100%;" cellpadding="2" cellspacing="3" id="ordersHold">
            <thead>
            <tr>

                <th id="relevant_change" style="display: none">选择</th>
                <th >单号</th>
                <th>目的仓</th>
                <th>扫描数</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="ordersList">

            </tbody>
        </table>
        <form id="form_return" style="display: none;">
            <table  border="0" style="width:100%;" cellpadding="2" cellspacing="3" id="warehouse_change">
                <thead>
                <tr style="background:#8fbb6c;">
                    <th>出库单号</th>
                    <!--                            <th>出库类型</th>-->
                    <!--                            <th>调往仓库</th>-->
                    <!--                <th>添加时间</th>-->
                    <th>添加人</th>
                    <th>出库单状态</th>
                    <th>备注</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="warehouse_product_relevant"  style="background:#8fbb6c;">

                </tbody>

            </table>

        </form>


    </div>
    <div align="center" style="margin:0.5em auto;">


        <input style="display: none; font-size: 0.9em; line-height: 0.9em" id="getplanned" class="submit_s style_lightgreen" type="button" value="获取计划入库值(<?php echo $date_array[2]['shortdate']; ?>)" onclick="javascript:getSortingList('<?php echo $date_array[2]['date']; ?>');">
        <input type="hidden" value=0 id="purchasePlanId" />
    </div>
<!--<div>-->
<!--    -->
<!--</div>-->
<!--</div>-->

<!--<div id="contents" style="display: block">-->
    <div id="message" class="message style_ok" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
    <div id="inv_control" align="center">
        <div id="invMethods">

        </div>
        <div id="shelfLifeStrict" style="display: none"></div>
        <div id="productList" name="productList" method="POST" style="display: none">

            <table id="productsHoldDo" border="1px" style="width:100%;display:none;border:1px solid;solid-color: black;" >

                <tbody id="productsInfoDo">
                <tr >
                    <th>单号</th><th id="product_name" ></th>
                    <th>调拨日期</th><th id="current_product_quantity_change"  ></th>
                </tr>
                <tr><th>发货仓</th><th id="current_product_plan" ></th>
                    <th>目的仓</th><th id="current_product_quantity"></th>
                </tr>
                </tbody>
            </table>
            <table id="productsHoldDo2" border="1px" style="width:100%;display:none;border:1px solid;solid-color: black;" >

                <tbody id="productsInfoDo2">
                <tr >
                    <th colspan="2">退货调拨单</th>
                    <th>生成日期</th><th id="current_product_quantity_change2"  ></th>
                </tr>
                <tr><th>退货仓</th><th id="current_product_plan2" ></th>
                    <th>目的仓</th><th id="current_product_quantity2"></th>
                </tr>
                </tbody>
            </table>
            <table id="productsHoldDo3" border="1px" style="width:100%;display:none;border:1px solid;solid-color: black;" >

                <tbody id="productsInfoDo3">
                <tr >
                    <th>调拨单号</th><th id="product_name3" ></th>
                    <th>调拨仓</th><th id="current_product_quantity_change3"  ></th>
                </tr>
                <tr>
                    <th>调拨商品</th><th id="current_product_plan3" ></th>
                    <th>仓库数量</th><th id="current_product_quantity3"></th>
                </tr>
                </tbody>
            </table>
            <table id="productsHoldDo1" border="1px" style="width:100%;display:none;border:1px solid;solid-color: black;" cellpadding=2 cellspacing=3>

                <tbody id="productsInfoDo1">

                </tbody>
            </table>
            <div id="barcodescanner" style="display: none">
                <form method="get" onsubmit="handleProductList(null); return false;">
                    <input id="product_make_hand" rows="1" maxlength="19" placeholder="请手动输入" style="font-size: 1.1em;margin-top: 0.5em;margin-bottom: 0.5em;ime-mode:disabled;background-color:#d0e9c6;height: 2em;display:none"/>
                    <input class="addprod style_green" type="submit" id="make_hand" value ="确认" style="font-size: 1em; padding: 0.2em;display:none"/>
                    <input name="product" id="product" rows="1" maxlength="19"  autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; height: 2em;"/></form>
                <!--                <input class="addprod style_green"  id="make_hand_hide" type="submit" value ="手动输入" onclick="javascript:clickProductInput();" style="font-size: 1em; padding: 0.2em;"><input class="addprod style_green"  id="make_hand_hides" type="submit" value ="隐藏手动" onclick="javascript:clickProductInputs();" style="font-size: 1em; padding: 0.2em;display:none;"><br />-->
                <!--<input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">-->
            </div>

            <script type="text/javascript">
                $("input[name='product']").keyup(function(){
                    var tmptxt=$(this).val();
                    // $(this).val(tmptxt.replace(/\D/g,''));

                    if(tmptxt.length >= 3){
                         handleProductList(null);
                    }
                    $(this).val("");
                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));
                });
                $("#product_make_hand").keyup(function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));

                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));
                });
                $("#handle_product").keyup(function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));

                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));
                });
                //$("input[name='product']").css("ime-mode", "disabled");
            </script>
        <div id="Order_View_Page" style="display: none">
            <table  border='1'cellspacing="0" cellpadding="0" id="order_view_lists">
                <thead>
                <tr style="background:#8fbb6c;">
                    <td>商品名称</td>
                    <td>货位号</td>
                    <td>仓库数量</td>
                    <td>操作</td>
                </tr>
                </thead>
                <tbody id="order_out_lists">

                </tbody>

            </table>

        </div>
        <div id="View_Page" style="display: none">
            <form id="form_return2" >
                <table  border='1'cellspacing="0" cellpadding="0" id="warehouse_change2">
                    <thead>
                    <tr style="background:#8fbb6c;">
                        <td>商品ID</td>
                        <td>商品名称</td>
                        <td>货位号</td>
                        <td>仓库数量</td>
                        <td>待投篮数量</td>
                    </tr>
                    </thead>
                    <tbody id="warehouse_product_relevant2">

                    </tbody>

                </table>

            </form>
        </div>
        <div id="Shipment_Page"  style="display:none;">
            <div>
                <div>出库单号:<span id="return_relevant_id"></span></div>
                <form  id="form_return_index">
                    <table  border='1'cellspacing="0" cellpadding="0" id="table_return_index">
                        <thead>
                        <!--                <tr style="background:#8fbb6c;">-->
                        <!--                    <td>商品名称</td>-->
                        <!--                    <td>货位号</td>-->
                        <!--                    <td>调拨数量</td>-->
                        <!--                    <td>待投篮数量</td>-->
                        <!--                    <td>操作</td>-->
                        <!--                </tr>-->
                        <!--                </thead>-->
                        <!--                <tbody id="tbody_return_index">-->
                        <!---->
                        <!--                </tbody>-->

                    </table>
                </form>
            </div>
            <div><hr></div>
            <div>
                <form id="form_return3" >
                    <div id = "change_info" style="display: none ;background: yellow">保证需要转化的商品有足够的库存,库存不足的情况下请不要转化商品，否则会造成库存不准
                    <br />扫描商品-》扫描货位号</div>
                    <table  border='1'cellspacing="0" cellpadding="0" id="warehouse_change3">
                        <thead>

                        </thead>
                        <tbody id="warehouse_product_relevant3">

                        </tbody>
                        <tbody id="warehouse_product_relevant4" style="display: none">

                        </tbody>

                    </table>

                </form>
            </div>
        </div>
        <input class="qtyopt"  type="button"   id="button_re" style=" float: right ;display: none"  value="提交" onclick="javascript:submitProducts();" >

        <div id="productLists" name="productLists" method="POST" style="display:;">
            <input type="hidden" id="current_do_product" value="0">
            <div id="product_back_information" style="display:block; margin-top: 0em;">
                <span style=" font-size: 1.2em;">包裹收货情况</span>
                <span style="float:right;font-size: 1em; line-height: 1.8em;">共<span id="count_container_plan"></span>个周转筐,待收<span id="count_container"></span>个周转筐</span>
                <span style="float:right;font-size: 1em; line-height: 1.8em;">共<span id="count_plan_quantity"></span>件整件,待收<span id="count_quantity"></span>件整件</span>
            </div>
            <table id="productsHold" border="0" style="width:100%;"  >
                <tr>
                    <th style="width:3em">包裹号</th>
                    <!--                    <th style="width:3em">单号</th>-->
                    <th style="width:3em">商品号</th>
                    <th style="width:3em">数量</th>
                    <th style="width:3em">商品名称</th>
                    <th style="width:3em">状态</th>
                    <th style="width:3em">操作</th>
                </tr>
                <tbody id="productsInfo">
                <!-- Scanned Product List -->
                </tbody>
            </table>
            <table id="productsHold2" border="0" style="width:100%;display:none;"  >
                <tr>
                    <th style="width:3em">商品号</th>
                    <th style="width:3em">数量</th>
                    <th style="width:3em">商品名称</th>
                    <th style="width:3em">操作</th>
                </tr>
                <tbody id="productsInfo2">
                <!-- Scanned Product List -->
                </tbody>
            </table>


            <input type="hidden" name="method" id="method" value="" />
            <div style="float:left">当前时间: <span id="currentTime"><?php echo date('Y-m-d H:i:s', time());?></span></div>
            <br />

            <div id="creat_shipping_cost" style="float:right;display: none;" class="col-sm-4"  >
                仓库成本：<input style="solid :1px" type="text" id= 'warehouse_cost' placeholder="单击填写仓库成本" class="btn " value="" >
                <br />
                调拨运费：<input style="solid :1px" type="text" id= 'shipping_cost' placeholder="单击填写调拨运费" class="btn " value="" >
            <br />
            </div>
            <input class="submit" id="submit_del" type="button" value="删除收货数据" onclick="javascript:delPurchaseOrderRelevantToInv(null,null);">
<!--            --><?php //if(in_array($_COOKIE['user_group_id'],$inventory_user_admin)){?>
                <input class="submit" id="submit" type="submit" value="提交入库" onclick="javascript:addOrderProductToInv_pre();">
<!--            --><?php //} ?>
            <br />
            <br />
            <br />

            <!--<input class="submit style_yellow" type="button" value="获取商品信息" onclick="javascript:getProductName();">-->
            <!-- <input class="submit style_gray" type="button" value="取消" onclick="javascript:cancel();"> -->
        </div>
        </div>
        <div id="merge_relevant_orders" style="display: none">
<!--            <input class="submit"   type="button" value="删除已合并调拨单" onclick="javascript:delete_merge_relevant_orders();"/>-->
            <input class="submit" type="button" value="合并调拨单" onclick="javascript:merge_relevant_orders();"/></div>
        <input class="submit" id="update_relevant_orders" style="display: none" type="button" value="取消调拨单" onclick="javascript:update_relevant_orders();"/><input id="print_relevants_merge" style="display: none" class="submit" type="button" value="打印面单" onclick="javascript:print_relevants_merge();"/>

    </div>
    <div style="display: none">
        <div comment="Used for alert but hide">
            <audio id="player" src="view/sound/redalert.wav">
                Your browser does not support the <code>audio</code> element.
            </audio>
        </div>
    </div>
    <div style="float: none; clear: both"><hr style="border: 0.1em #999 dashed"></div>
    <div id='move_list' align="center" style="display:none; margin:0.5em auto;">
        <!-- Insert Move List -->
        <table id="invMovesHold" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
            <tr>
                <th style="width:4.2em">类型</th>
                <th style="">站点/添加时间</th>
                <th style="width:2.4em">总数</th>
                <th style="width:2.4em">操作</th>
            </tr>
            <tbody id="invMovesInfo">
            <!-- Scanned Product List -->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            </tbody>
        </table>
    </div>
</div>
</div>

<div id="productsHoldRelevant" style="display: none">
    <div id='invMovesPrint' align="center" style="margin:0.5em auto;">
        <!-- Insert Move List -->


<!--        <div class="noprint"><input class="submit_s style_gray" type="button" value="返回主页" onclick="javascript:backhome();"></div>-->

        <div id="invMovesPrintCaption" style="padding: 10px 5px;">类型:<span id="prtInvMoveType">调拨单打印</span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;打印人:<span id="prtInvMoveTitle"><?php echo $_COOKIE['inventory_user'];?></span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;时间:<span id="prtInvMoveTime"><?php echo date('Y-m-d H:i:s', time());?></span></div>
        <div style=" padding: 10px 5px;">
            <table  border="0" style="width:75%;"  cellpadding=0 cellspacing=0>
                <tr>
                    <th align="center" style="width:4em">调拨单号</th>
                    <th align="center" style="width:4em">操作时间，操作人，操作</th>
                </tr>
                <tbody id="productsInfoRelevant2">
                <!-- Scanned Product List -->
                </tbody>
            </table>
        </div>
        <div style=" padding: 10px 5px;">
            <table id="invMovesPrintHold" border="0" style="width:75%;"  cellpadding=0 cellspacing=0>
                <tr>
                    <th align="center" style="width:4em">调拨单号</th>
                    <th align="center" style="width:4em">包裹号</th>
                    <th align="center" style="width:4em">商品号</th>
                    <th align="center" style="width:4em">数量</th>
                    <th align="center" style="">商品名称</th>
                </tr>
                <tbody id="productsInfoRelevant">
                <!-- Scanned Product List -->
                </tbody>
            </table>
        </div>
<!--        <div style="padding: 0 5px; display: block; float: none; clear: both">-->
<!--            <div style="float: right">-->
<!--                合计数量: <span id="invMovesPrintQtyTotal">0</span>(件)&nbsp;&nbsp;&nbsp;-->
<!--                合计金额: <span id="invMovesPrintAmountTotal">0</span>(元)-->
<!--            </div>-->
<!--        </div>-->
    </div>
</div>

<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="player3" src="view/sound/ding.mp3">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>
<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="player2" src="view/sound/redalert.wav">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>

<div id="overlay">

</div>

<script>
    global.warehouseId = '<?php echo $_COOKIE['warehouse_id'];?>';
    global.userId = '<?php echo $_COOKIE['inventory_user_id'];?>';
    global.userName = '<?php echo $_COOKIE['inventory_user'];?>';
    global.warehouse = '<?php echo $_COOKIE['warehouse_title'];?>';
    global.products_array = {};
    window.product_id1 = '';
    window.product_id2 = '';
    <?php if(strstr($_COOKIE['inventory_user'],'scfj')){ ?>
    $(document).keydown(function (event) {
        $('#product').focus();
    });
    <?php } ?>
    window.product_weight_info = new Array();
    //JS Date Format Extend
    Date.prototype.Format = function(fmt)
    { //author: meizz
        var o = {
            "M+" : this.getMonth()+1,                 //月份
            "d+" : this.getDate(),                    //日
            "h+" : this.getHours(),                   //小时
            "m+" : this.getMinutes(),                 //分
            "s+" : this.getSeconds(),                 //秒
            "q+" : Math.floor((this.getMonth()+3)/3), //季度
            "S"  : this.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt))
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        for(var k in o)
            if(new RegExp("("+ k +")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        return fmt;
    }


    /* 显示遮罩层 */
    function showOverlay() {
        $("#overlay").height(pageHeight());
        $("#overlay").width(pageWidth());

        // fadeTo第一个参数为速度，第二个为透明度
        // 多重方式控制透明度，保证兼容性，但也带来修改麻烦的问题
        $("#overlay").fadeTo(200, 0.5);
    }

    /* 隐藏覆盖层 */
    function hideOverlay() {
        $("#overlay").fadeOut(200);
    }


    /* 当前页面高度 */
    function pageHeight() {
        return document.body.scrollHeight;
    }

    /* 当前页面宽度 */
    function pageWidth() {
        return document.body.scrollWidth;
    }

    $(document).ready(function () {
        startTime();

        var warehouse_ids = $("#warehouse_id").text();
        var select_date = $("#date_start").val();
        /*zx
        * 获取正在使用中的仓库*/
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getUseWarehouseId',
            },
            success : function (response , status , xhr){
                if(response) {
                    var jsonData = $.parseJSON(response);
                    var html = '';
                    $.each(jsonData, function (index, value) {
                        global.warehouse_array[parseInt(value.warehouse_id)] = value;
                        html += '<option value="' +value.warehouse_id+ '">' + value.title + '</option>';
                    });
                    $('#to_warehouse').html(html);
                    $('#to_warehouse').val(parseInt(warehouse_ids));
                }
            }

        });


        //Alert Sound Settings
        var settings = {
            progressbarWidth: '0',
            progressbarHeight: '5px',
            progressbarColor: '#22ccff',
            progressbarBGColor: '#eeeeee',
            defaultVolume: 0.8
        };
        $("#player").player(settings);
    });

    function playOverdueAlert(){
        //$('#player').attr('src',sound);
        $('.simpleplayer-play-control').click();
    }
    //批量扫描，暂已弃用
    function getNewWarehouseStatus() {
        var warehouse_id2 = $("#warehouse_id").text();
        var warehouse_id1 = $("#to_warehouse").val();
        // console.log(parseInt(warehouse_id1));
        // console.log(parseInt(warehouse_id2));
        if (parseInt(warehouse_id1) == parseInt(warehouse_id2)) {
            $('#much_look').click(function () {
                orderInventory(0, 4);
            });
        } else {
            $('#much_look').click(function () {
                orderInventory(0, 2);
            })
            // $('.simpleplayer-play-control').click();
        }
    }
    function stopOverdueAlert(){
        $('.simpleplayer-stop-control').click();
    }

    function startTime()
    {
        var today=new Date();
        var year=today.getFullYear();
        var month=today.getMonth()+1;
        var day=today.getDate();

        var h=today.getHours();
        var m=today.getMinutes();
        var s=today.getSeconds();
        // add a zero in front of numbers<10
        m=checkTime(m);
        s=checkTime(s);
        $('#currentTime').html(year+"/"+month+"/"+day+" "+h+":"+m+":"+s);
        t=setTimeout('startTime()',500)
    }

    function checkTime(i)
    {
        if (i<10)
        {i="0" + i}
        return i
    }

    /*zx
    * 调拨单详情按钮事件*/
    function orderInventory(relevant_id,is_view){
        // console.log(is_view);
        $('#invMethods').hide();
        $("#orderListTable").hide();
        $('#productList').show();
        $('#productsHoldDo').show();
        $('#productsHoldDo2').hide();
        $("#searchPurchaseOrder").hide();
        $("#much_look").hide();
        var warehouse_id2 = parseInt($("#warehouse_id").text());
        var warehouse_id1 = parseInt($("#to_warehouse").val());
        // console.log(parseInt(warehouse_id1));
        // console.log(parseInt(warehouse_id2));
        /*zx
        * 批量扫描，暂已舍弃*/
        if (relevant_id == 0) {
            if ($('#inventory_order_sorting').val()>0) {
                relevant_id = $('#inventory_order_sorting').val();
            }
            $("#product_name").html('批量');
            $("#submit_del").hide();
            $("#submit").hide();
            $("#barcodescanner").show();
        /*zx
        * 单个调拨单展示*/
        } else {
            $("#product_name").html(relevant_id);
            /*zx
            * 出库*/
            if (is_view ==2 ) {
                if (parseInt(warehouse_id1) != parseInt(warehouse_id2)) {
                    $("#submit").show();
                    $("#barcodescanner").show();
                    $("#submit_del").show();
                }
            /*zx
            * 入库*/
            } else if (is_view ==4 ) {
                if (parseInt(warehouse_id1) == parseInt(warehouse_id2)) {
                    $("#submit").show();
                    $("#barcodescanner").show();
                    $("#submit_del").show();
                }
            /*zx
            * 查看*/
            } else {
                $("#submit").hide();
                $("#submit_del").hide();
            }
        }


        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:reloadNewWarehouseRequisition();">返回</button>'+'');
        /*zx
        * 获得调拨单详细数据*/
        getOrderSortingList(relevant_id,is_view);
    }

    /*zx
    * 获得调拨单详细数据*/
    function getOrderSortingList(relevant_id,is_view){
        $('#update_relevant_orders').hide();
        $('#merge_relevant_orders').hide();
        $("#print_relevants_merge").hide();
        $('#relevant_change').hide();

        var warehouse_id = parseInt($("#warehouse_id").text());
        var date_added = $('#date_start').val();
        var to_warehouse_id =parseInt($('#to_warehouse').val());
        var from_warehouse_id =parseInt($('#from_warehouse').val());
        var warehouse_out_type = parseInt($('#warehouse_out_type').val());

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'relevantViewItem',
                data :{
                    relevant_id : relevant_id ,
                    warehouse_id : warehouse_id ,
                    relevant_status_id :is_view ,
                    date_added :date_added,
                    to_warehouse_id :to_warehouse_id,
                    from_warehouse_id :from_warehouse_id,
                    warehouse_out_type :warehouse_out_type
                }
            },
            success : function (response , status , xhr){
                var html = '<td colspan="4">正在载入...</td>';
                $('#productsInfo').html(html);
                var jsonData = $.parseJSON(response);
                window.product_id_arr = {};
                /*未收货*/
                window.product_plan_number = 0;
                /*已收货*/
                window.plan_numbers = 0;
                window.plan_container_numbers = jsonData.container_numbers ? parseInt(jsonData.container_numbers) : 0 ;
                window.container_numbers = jsonData.container_numbers2 ? parseInt(jsonData.container_numbers2) : 0 ;
                $('#count_container_plan').html(window.plan_container_numbers);
                $('#count_container').html(window.plan_container_numbers-window.container_numbers);
                html = '';
                /*zx
                * 未放入中间表的展示在上方*/
                if(jsonData.product2){
                    $.each(jsonData.product2,function(i,value) {
                        order_been_over = "style = ''";
                        order_been_over_size = "style = ''";
                        /*整件*/
                        if (value.container_id == "" || value.container_id == 0) {
                            product_id_arr[value.product_id] = value.relevant_id;
                            product_id_arr[value.sku_barcode] = value.product_id;
                            html += '<tr class="container'+value.container_id+'" id="bd'+ value.product_id +'">';
                            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+'"><span id="containerm'+value.product_id+'" >';
                            html += '商品</th>';
                            html += '<th '+'style="display:none;"'+' class="relevantlist" id="relevant'+value.product_id+'" >';
                            html += value.relevant_id+'</th>';
                            html += '<th '+order_been_over+' class="product_lists'+value.container_id+'" id="container'+value.product_id+'" >';
                            html += value.product_id+'</th>';
                            html += '<th '+order_been_over+' class="containerlist" id="containerl'+value.product_id+'" ><span class="num1_lists" id="num_'+value.product_id+'">';
                            html += 0;
                            html += '</span>';
                            html += '<br />[<span id="num2_'+value.product_id+'">'+value.num2+'</span>]</th>';
                            html += '<th '+order_been_over+'  id="containero'+value.product_id+'">';
                            html += value.name+'</th>';
                            html += '<th '+order_been_over+' id="opera'+value.product_id+'">';
                            html += '未收'+ '</th>';
                            if (warehouse_id == from_warehouse_id) {
                                if (is_view == 2) {
                                    html += '<th><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv('+value.product_id+','+value.container_id+');" value="删除"/></th>';
                                }
                            } else {
                                if (is_view == 4) {
                                    html += '<th><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv('+value.product_id+','+value.container_id+');" value="删除"/></th>';
                                }
                            }
                            html += '</tr>';
                            window.product_plan_number+=parseInt(value.num2);
                            /*周转筐*/
                        } else {
                            product_id_arr[value.container_id] = value.relevant_id;
                            html += '<tr class="container'+value.container_id+'" id="bd'+ value.product_id+value.container_id +'">';
                            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+value.container_id+'"><span id="containerm'+value.product_id+value.container_id+'" >';
                            html += value.container_id+'</th>';
                            html += '<th '+'style="display:none;"'+' class="relevantlist" id="relevant'+value.product_id+value.container_id+'" >';
                            html += value.relevant_id+'</th>';
                            html += '<th '+order_been_over+' class="product_lists'+value.container_id+'" id="container'+value.product_id+value.container_id+'" >';
                            html += value.product_id+'</th>';
                            html += '<th '+order_been_over+' class="containerlist" id="containerl'+value.product_id+value.container_id+'" ><span class="num1_lists" id="num_'+value.product_id+value.container_id+'">';
                            html += value.num2;
                            html += '</span>';
                            html += '</th>';
                            html += '<th '+order_been_over+'  id="containero'+value.product_id+value.container_id+'">';
                            html += value.name+'</th>';
                            html += '<th '+order_been_over+' id="opera'+value.product_id+value.container_id+'">';
                            html += '未收'+ '</th>';
                            if (warehouse_id == from_warehouse_id) {
                                if (is_view == 2) {
                                    html += '<th><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv('+value.product_id+','+value.container_id+');" value="删除"/></th>';
                                }
                            } else {
                                if (is_view == 4) {
                                    html += '<th><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv('+value.product_id+','+value.container_id+');" value="删除"/></th>';
                                }
                            }
                            html += '</tr>';
                        }
                    });
                }
                /*zx
                * 存入中间表的数据*/
                if (jsonData.product1){
                    $.each(jsonData.product1,function(i,value) {
                        /*整件*/
                        if (value.container_id == "" || value.container_id == 0) {
                            product_id_arr[value.product_id] = value.relevant_id;
                            product_id_arr[value.sku_barcode] = value.product_id;
                            if (value.num2 == value.num1) {
                                order_been_over = "style = 'background-color:#666666;'";
                                order_been_over_size = "style = 'background-color:#666666;font-size:2em;'";
                            } else {
                                order_been_over = "style = ''";
                                order_been_over_size = "style = ''";
                            }
                            if (warehouse_id == from_warehouse_id && warehouse_out_type == 4) {
                                var num1 = value.num2;
                                order_been_over = "style = 'background-color:#666666;'";
                                order_been_over_size = "style = 'background-color:#666666;font-size:2em;'";
                            } else {
                                var num1 = value.num1;
                            }
                            product_plan_number+=parseInt(value.num2);
                            plan_numbers+=parseInt(num1);
                            html += '<tr class="container'+value.container_id+'" id="bd'+ value.product_id +'">';
                            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+'"><span id="containerm'+value.product_id+'" >';
                            html += '商品</th>';
                            html += '<th '+'style="display:none;"'+' class="relevantlist" id="relevant'+value.product_id+'" >';
                            html += value.relevant_id+'</th>';
                            html += '<th '+order_been_over+' class="product_lists'+value.container_id+'" id="container'+value.product_id+'" >';
                            html += value.product_id+'</th>';
                            html += '<th '+order_been_over+' class="containerlist" id="containerl'+value.product_id+'" ><span class="num1_lists" id="num_'+value.product_id+'">';
                            html += num1;
                            html += '</span>';
                            html += '<br />[<span id="num2_'+value.product_id+'">'+value.num2+'</span>]</th>';
                            html += '<th '+order_been_over+'  id="containero'+value.product_id+'">';
                            html += value.name+'</th>';
                            html += '<th '+order_been_over+' id="opera'+value.product_id+'">';
                            html += '已收'+ '</th>';
                            if (warehouse_id == from_warehouse_id) {
                                if (is_view == 2) {
                                    html += '<th><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv('+value.product_id+','+value.container_id+');" value="删除"/></th>';
                                }
                            } else {
                                if (is_view == 4) {
                                    html += '<th><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv('+value.product_id+','+value.container_id+');" value="删除"/></th>';
                                }
                            }
                            html += '</tr>';
                            /*周转筐*/
                        } else {
                            product_id_arr[value.container_id] = 1;
                            order_been_over = "style = 'background-color:#666666;'";
                            order_been_over_size = "style = 'background-color:#666666;font-size:2em;'";
                            html += '<tr class="container'+value.container_id+'" id="bd'+ value.product_id+value.container_id+'">';
                            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+value.container_id+'"><span id="containerm'+value.product_id+value.container_id+'" >';
                            html += value.container_id+'</th>';
                            html += '<th '+'style="display:none;"'+' class="relevantlist" id="relevant'+value.product_id+value.container_id+'" >';
                            html += value.relevant_id+'</th>';
                            html += '<th '+order_been_over+' class="product_lists'+value.container_id+'" id="container'+value.product_id+value.container_id+'" >';
                            html += value.product_id+'</th>';
                            html += '<th '+order_been_over+' class="containerlist" id="containerl'+value.product_id+value.container_id+'" ><span class="num1_lists" id="num_'+value.product_id+value.container_id+'">';
                            html += value.num1;
                            html += '</span>';
                            html += '</th>';
                            html += '<th '+order_been_over+'  id="containero'+value.product_id+value.container_id+'">';
                            html += value.name+'</th>';
                            html += '<th '+order_been_over+' id="opera'+value.product_id+value.container_id+'">';
                            html += '已收'+ '</th>';
                            if (warehouse_id == from_warehouse_id) {
                                if (is_view == 2) {
                                    html += '<th><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv('+value.product_id+','+value.container_id+');" value="删除"/></th>';
                                }
                            } else {
                                if (is_view == 4) {
                                    html += '<th><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv('+value.product_id+','+value.container_id+');" value="删除"/></th>';
                                }
                            }
                            html += '</tr>';
                        }
                    });
                }
                $('#productsInfo').html(html);
                $("#count_plan_quantity").html(window.product_plan_number);
                $("#count_quantity").html(window.product_plan_number-window.plan_numbers);
            }
        });
    }

    function chooseStation(){
        if($('#station').val() > 0){
            $('#barcodescanner').show();
            $('#product').focus();

            if($('#method').val()){
                $('#station').attr('disabled',"disabled");
            }
        }
        else{
            $('#product').blur();
            $('#barcodescanner').hide();
        }
    }

    function checkStation(){
        if($('#station').val() <= 0){
            alert('请选择站点');

            $('#product').blur();
            $('#station').focus();

            return false;
        }

        return true;
    }

    function getSetDate(dateGap,dateFormart) {
        var dd = new Date();
        dd.setDate(dd.getDate()+dateGap);//获取AddDayCount天后的日期

        return dd.Format(dateFormart);

        //console.log(getSetDate(1,'yyMMdd')); //Tomorrow
        //console.log(getSetDate(-1,'yyMMdd')); //Yesterday
    }

    function markBarCodeLine(barCodeId,color){
        //console.log('Marked Barcode line'+barCodeId);
        $(barCodeId+'td').css('backgroundColor',color);
    }

    function locateInput(){
        $('#product').focus();
    }

    /*zx
    * 手写输入，暂时弃用*/
    function clickProductInput(){
        // $('#move_list').hide();
        $('#make_hand').show();
        $('#product_make_hand').show();
        $('#make_hand_hide').hide();
        $('#make_hand_hides').show();
        $('#product').hide();
        $('#product').val('');
    }
    function clickProductInputs(){
        // $('#move_list').hide();
        $('#make_hand').hide();
        $('#product_make_hand').hide();
        $('#make_hand_hide').show();
        $('#make_hand_hides').hide();
        $('#product').show();
        $('#product_make_hand').val('');
    }



    /*zx
    * 商品扫描触发*/
    function handleProductList(container_ids){

        var warehouse_id2 = parseInt($("#warehouse_id").text());
        var warehouse_id1 =parseInt($('#to_warehouse').val());
        if (warehouse_id1 == warehouse_id2) {
            var relevant_status_id = 4;
        } else {
            var relevant_status_id = 2;
        }
        // $('.simpleplayer-play-control').click();
        /*zx
        * 获得扫描或手输的条码*/
        if (container_ids == null || container_ids == '') {
            var rawId = $('#product').val().trim() == '' ? $('#product_make_hand').val().trim() : $('#product').val().trim();
            container_id = rawId.substr(0, 18);//Get 18 code

        } else {
            container_id = container_ids;
        }
        $('#product').val('');
        /*判断是否是退货调拨，如果是，进入调拨单生成页面*/
        var warehouse_out_type = parseInt($('#warehouse_out_type').val());
        if ((warehouse_out_type == 4 && warehouse_id1 != warehouse_id2) || warehouse_out_type == 5 ) {
            /*zx
            * 扫描相同商品减数量*/
            if (container_id == parseInt($("#get_product_id").val())) {
                qtyminus(container_id,2);
                play_sound(3);

            /*zx
            * 扫描新商品生成商品扫描信息栏*/
            } else {
                var num2 = parseInt($("#num_" + container_id).text());
                // console.log(num2);
                /*zx
                * 扫描已经扫描过的商品，获得之前扫描的数量*/
                if (!isNaN(num2)) {
                    play_sound(3);

                    //console.log(container_id);
                    var id = container_id;
                    var product_name = $("#containero" + id).text();
                    var products_id = parseInt($("#sku_barcode" + container_id).text());
                    $("#current_do_product").val(id);
                    $("#productsInfoDo1").html('<tr id="clear' + id + '"><input type="hidden" id="manysubmit' + id + '" value="' +
                        id + '"/><input type="hidden" id="get_product_id" value="'+
                        id + '"/><td colspan="3" id="product_name1' + id + '" align="center" style="font-size:1.4em;"></td></tr><tr id="clearss' +
                        id + '"><th style="width:4em"> 退货量 </th><th align="center" id ="manysubmits"><button style="float:left" class="invopt manysubmits" onclick="javascript:tjStationPlanProduct(' +
                        id + ',' + products_id + ',\'' + product_name + '\');">提交</button></th></tr><tr id="clears' +
                        id + '"><td id="current_product_quantity1' +
                        id + '" align="center" style="font-size:1.4em;"></td><td id="current_product_quantity_change1' +
                        id + '"></td></tr>');
                    $("#product_name1" + id).html(product_name);
                    // $("#current_product_plan1"+id).html($("#num2_"+id).text()+'<span style="display:none;" name="productId" id="pid'+id+'">' + $("#num2_"+id).text() + '</span>');
                    $("#current_product_quantity1" + id).html('<input class="qty"  id="' +
                        id + '" value="' + (num2 + 1) + '"><input type="hidden" id="plan' +
                        id + '" value="' + $("#num2_" + id).text() + '"><input type="hidden" id="old_plan' +
                        id + '" value="' + $("#num2_" + id).text() + '"><input type="hidden" id="do' + id + '" value="0">');
                    $("#current_product_quantity_change1" + id).html('<input style="height:3em;width:3em;" class="qtyopt" type="button" value="-" onclick="javascript:qtyadd(\'' +
                        id + '\',2)"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+" onclick="javascript:qtyminus(\'' +
                        id + '\',2)"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+10" onclick="javascript:qtyminus10(\'' +
                        id + '\',2)"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+50" onclick="javascript:qtyminus50(\'' + id + '\',2)">');
                    $("#productsHoldDo1").show();
                /*zx
                * 扫描未扫描过的商品,判断数据库是否存在，生成商品详情操作栏*/
                } else {
                    $.ajax({
                        type: 'POST',
                        url: 'invapi.php',
                        data: {
                            method: 'getProductInformation',
                            data: {
                                product_id: container_id
                            }
                        },
                        success: function (response) {
                            if (response) {
                                var jsonData = $.parseJSON(response);
                                hideOverlay();
                                if (jsonData != '') {
                                    var id = container_id;
                                    var product_name = jsonData.name;
                                    var products_id = jsonData.product_id;
                                    play_sound(3);

                                    $("#current_do_product").val(id);
                                    $("#productsInfoDo1").html('<tr id="clear' + id + '"><input type="hidden" id="manysubmit' + id + '" value="' +
                                        products_id + '"/><input type="hidden" id="get_product_id" value="' +
                                        id + '"/><td colspan="3" id="product_name1' + id + '" align="center" style="font-size:1.4em;"></td></tr><tr id="clearss' +
                                        id + '"><th style="width:4em">退货量</th><th align="center" id ="manysubmits"><button style="float:left" class="invopt manysubmits" onclick="javascript:tjStationPlanProduct(' +
                                        id + ',' + products_id + ',\'' + product_name + '\');">提交</button></th></tr><tr id="clears' +
                                        id + '"><td id="current_product_quantity1' +
                                        id + '" align="center" style="font-size:1.4em;"></td><td id="current_product_quantity_change1' +
                                        id + '"></td></tr>');
                                    $("#product_name1" + id).html(product_name);
                                    // $("#current_product_plan1"+id).html($("#num2_"+id).text()+'<span style="display:none;" name="productId" id="pid'+id+'">' + $("#num2_"+id).text() + '</span>');
                                    $("#current_product_quantity1" + id).html('<input class="qty"  id="' +
                                        id + '" value="1"><input type="hidden" id="plan' +
                                        id + '" value="' + $("#num2_" + id).text() + '"><input type="hidden" id="old_plan' +
                                        id + '" value="' + $("#num2_" + id).text() + '"><input type="hidden" id="do' + id + '" value="0">');
                                    $("#current_product_quantity_change1" + id).html('<input style="height:3em;width:3em;" class="qtyopt" type="button" value="-" onclick="javascript:qtyadd(\'' +
                                        id + '\',2)"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+" onclick="javascript:qtyminus(\'' +
                                        id + '\',2)"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+10" onclick="javascript:qtyminus10(\'' +
                                        id + '\',2)"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+50" onclick="javascript:qtyminus50(\'' + id + '\',2)">');
                                    $("#productsHoldDo1").show();
                                } else {
                                    play_sound(2);

                                    alert('扫描的商品号错误');
                                    return false;
                                }
                            }
                        }
                    });
                }
            }
        /*zx
        * 仓内调拨单扫描*/
        } else if (warehouse_out_type == 3) {
            if (window.product_id_arr[container_id] > 0) {
                play_sound(3);
                // if (typeof(window.product_id_arr[container_id]) != 'undefined') {
                //
                // } else {
                    if (container_id.length > 5) {
                        container_id = window.product_id_arr[container_id];
                    }
                    $("#bar_code1_"+container_id).show();
                // }
            } else {
                play_sound(2);
                alert('商品扫描错误');
            }

        /*zx
        * 非退货调拨单出库*/
        } else {
            /*
            * 扫描商品*/
            if (container_id.length > 9 || container_id.length < 6) {

                if (window.product_id_arr[container_id] > 0) {
                    if (container_id.length > 5) {
                        container_id = window.product_id_arr[container_id];
                    }
                    var product_ids = container_id;
                    if (parseInt($("#num_" + container_id).text()) < parseInt($("#num2_" + container_id).text())) {
                        // showOverlay();

                        /*zx
                        * 扫描相同商品减数量*/
                        if (product_ids == parseInt($("#get_product_id").val())) {
                            play_sound(3);

                            qtyminus(product_ids,1);
                            // console.log(parseInt($("#"+product_ids).val()));
                            if (parseInt($("#"+product_ids).val()) == 0) {
                                addProduct(product_ids, 1, 0, relevant_status_id);
                            }
                        /*zx
                        * 扫描新商品生成商品扫描信息栏*/
                        } else {
                            var id = product_ids;
                            play_sound(3);

                            $("#current_do_product").val(id);
                            $("#productsInfoDo1").html('<tr id="clear' + id + '"><input type="hidden" id="manysubmit'+id+'" value="' +
                                id + '"/><input type="hidden" id="get_product_id" value="' +
                                id + '"/><td colspan="3" id="product_name1' + id + '" align="center" style="font-size:1.4em;"></td></tr><tr id="clearss' +
                                id + '"><th style="width:4em">计划量</th><th style="width:4em">待收货</th><th align="center" id ="manysubmits"><button style="float:left" class="invopt manysubmits" onclick="javascript:addProduct(' +
                                product_ids + ',1,0,' + relevant_status_id + ');">提交</button></th></tr><tr id="clears' +
                                id + '"><td id="current_product_plan1' +
                                id + '"  align="center" style="font-size:1.5em;"></td> <td id="current_product_quantity1' +
                                id + '" align="center" style="font-size:1.4em;"></td> <td id="current_product_quantity_change1' +
                                id + '"></td></tr>');
                            $("#product_name1" + id).html($("#containero" + id).html());
                            $("#current_product_plan1" + id).html($("#num2_" + id).text() + '<span style="display:none;" name="productId" id="pid' + id + '">' + $("#num2_" + id).text() + '</span>');
                            $("#current_product_quantity1" + id).html('<input class="qty"  id="' +
                                id + '" value="' + ($("#num2_" + id).text() - $("#num_" + id).text()-1) + '"><input type="hidden" id="plan' +
                                id + '" value="' + $("#num2_" + id).text() + '"><input type="hidden" id="old_plan' +
                                id + '" value="' + $("#num2_" + id).text() + '"><input type="hidden" id="do' + id + '" value="0">');
                            $("#current_product_quantity_change1" + id).html('<input style="height:3em;width:3em;" class="qtyopt" type="button" value="+" onclick="javascript:qtyadd(\'' +
                                id + '\',1)"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus(\'' +
                                id + '\',1)"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-10" onclick="javascript:qtyminus10(\'' +
                                id + '\',1)"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-50" onclick="javascript:qtyminus50(\'' + id + '\',1)">');
                            $("#productsHoldDo1").show();
                        }
                    } else {
                        play_sound(2);

                        alert('该商品已收完');
                        return false;
                    }
                } else {
                    play_sound(2);

                    alert('扫描的商品号错误');
                    return false;
                }
                /*
                * 扫描周转筐*/
            } else {
                var container_id = parseInt(container_id.substr(0, 6));
                if (window.product_id_arr[container_id] > 0) {

                    var product_ids = [];
                    var quantitys = [];
                    var containers = [];
                    var relevant_id = parseInt($("#product_name").text());
                    $(".product_lists" + container_id).each(function () {
                        var products = $(this).text();
                        var quantity = $("#num_" + products + container_id).text();
                        var container = $("#containerm" + products + container_id).text();
                        product_ids.push(products);
                        quantitys.push(quantity);
                        containers.push(container);
                    });
                    /*zx
                    * 判断周转筐是否已收完*/
                    if (window.product_id_arr[container_id] == 1) {
                        play_sound(2);

                        alert('该包裹已收');
                        return false;
                    } else {
                        showOverlay();
                        var player3 = $("#player3")[0];
                        player3.play();
                        window.container_numbers += 1;
                        $('#count_container').html(window.plan_container_numbers - window.container_numbers);
                        $(".product_lists" + container_id).each(function () {
                            var products1 = $(this).text();
                            $("#opera" + products1 + container_id).html('已收');
                            $("#container" + products1 + container_id).css("background-color", "#666666");
                            $("#containerl" + products1 + container_id).css("background-color", "#666666");
                            $("#relevant" + products1 + container_id).css("background-color", "#666666");
                            $("#containern" + products1 + container_id).css("background-color", "#666666");
                            $("#containero" + products1 + container_id).css("background-color", "#666666");
                            $("#opera" + products1 + container_id).css("background-color", "#666666");
                        });
                        window.product_id_arr[container_id] = 1;
                        addProduct(product_ids, quantitys, containers, relevant_status_id);
                    }
                } else {
                    play_sound(2);

                    alert('扫描的包裹号错误');
                    return false;
                }
            }
        }
    }
    /*生成退货调拨单列表*/
    function tjStationPlanProduct(product_id,product_id2,name){
        $("#productsHold").hide();
        $("#productsHold2").show();
        var num1 = parseInt($("#"+product_id).val());
        var num2 = parseInt($("#num_"+product_id).text());
        play_sound(3);

        /*zx
        * 判断是否已在下方生成过，如果是，修改数量，否则生成新的*/
        if (!isNaN(num2)) {
            $("#num_"+product_id).text(num1);
        } else {
            // product_plan_number += parseInt(value.num2);
            // plan_numbers += parseInt(value.num1);
            var html = '';
            order_been_over = '';
            html += '<tr class="container' + product_id + '" id="bd' + product_id + '">';
            html += '<th ' + order_been_over + ' class="product_lists" id="container' + product_id + '" >';
            html += product_id2 + '</th>';
            html += '<th ' + order_been_over + ' class="containerlist" id="containerl' + product_id + '" ><span class="num1_lists" id="num_' + product_id + '">';
            html += num1;
            html += '</span><div id="sku_barcode'+product_id+'" style="display:none;">'+product_id2+'</div>';
            html += '</th>';
            html += '<th ' + order_been_over + '  id="containero' + product_id + '">';
            html += name + '</th>';
            // html += '<th ' + order_been_over + ' id="opera' + product_id + '">';
            // html += '已收' + '</th>';
            html += '<th><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv(' + product_id + ',1);" value="删除"/></th></tr>';
            $('#productsInfo2').append(html);
        }
        $("#productsInfoDo1").html('');
    }
    //中间表提交
    function addProduct(product_ids,quantitys,containers,relevant_status_id) {
        var relevant_id = parseInt($("#product_name").text());
        var warehouse_id = parseInt($("#warehouse_id").text());
        /*zx
        * 判断是否是商品，如果是先进行数量的判断生成对应的样式*/
        if (containers == 0) {
            var container_id = product_ids;
            var player3 = $("#player3")[0];
            player3.play();
            quantitys = parseInt($("#num2_"+container_id).text())-parseInt($("#"+container_id).val());
            window.plan_numbers+=(quantitys-parseInt($("#num_"+container_id).text()));
            $("#count_quantity").html(window.product_plan_number - window.plan_numbers);
            if (parseInt($("#"+container_id).val()) == 0) {
                $("#num_"+container_id).html(quantitys);
                $("#container"+container_id).css("background-color", "#666666");
                $("#containerl"+container_id).css("background-color", "#666666");
                $("#relevant"+container_id).css("background-color", "#666666");
                $("#containern"+container_id).css("background-color", "#666666");
                $("#containero"+container_id).css("background-color", "#666666");
                $("#opera"+container_id).css("background-color", "#666666");
                $("#opera"+container_id).html('已收');
            } else {
                $("#num_"+container_id).html(quantitys);
                $("#opera"+container_id).html('已收');
            }
            $("#productsInfoDo1").html('');
        }
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'submitRelevantProduct',
                data: {
                    relevant_id: relevant_id,
                    relevant_status_id: relevant_status_id,
                    warehouse_id: warehouse_id,
                    product_ids: product_ids,
                    quantitys: quantitys,
                    containers: containers,
                }

            },
            success: function (response) {
                if (response) {
                    var jsonData = $.parseJSON(response);
                    hideOverlay();
                    if (jsonData != 2) {
                        play_sound(2);

                        alert('该包裹已收');
                        return false;
                    }
                }
            }
        });
    }
    function delPurchaseOrderRelevantToInv(product_id,container_id){
        showOverlay();
        if(confirm("是否确认删除已扫描数据？")){
            var warehouse_id = parseInt($("#warehouse_id").text());
            var date_added = $('#date_start').val();
            var from_warehouse_id =parseInt($('#from_warehouse').val());
            var to_warehouse_id =parseInt($('#to_warehouse').val());
            var relevant_id = [];
            relevant_id.push(parseInt($("#product_name").text()));
            var warehouse_out_type = parseInt($('#warehouse_out_type').val());
            /*zx
            * 退货调拨单出库删除*/
            if (warehouse_out_type == 4 && warehouse_id != to_warehouse_id) {
                if (container_id == 1) {
                    $("#bd"+product_id).remove();
                    play_sound(3);

                } else {
                    $('#productsInfo2').html('');
                    play_sound(3);

                }
            } else {
                $.ajax({
                    type: 'POST',
                    url: 'invapi.php',
                    data: {
                        method: 'delPurchaseOrderRelevantToInv',
                        warehouse_id: warehouse_id,
                        date_added: date_added,
                        product_id: product_id,
                        container_id: container_id,
                        warehouse_out_type : warehouse_out_type,
                        relevant_id: relevant_id,
                    },
                    success: function (response, status, xhr) {
                        if (response) {
                            //var jsonData = eval(response);
                            var jsonData = $.parseJSON(response);


                            if (jsonData.status == 999) {
                                alert("未登录，请登录后操作");
                                window.location = 'inventory_login.php?return=w_i.php';
                            }

                            if (jsonData.status == 1) {
                                alert("数据已删除,请重新扫描");
                                if (warehouse_id == from_warehouse_id) {
                                    getOrderSortingList(relevant_id[0], 2);
                                } else {
                                    getOrderSortingList(relevant_id[0], 4);
                                }
                            }

                            if (jsonData.status == 2) {
                                alert("数据已删除,可以重新扫描了");
                                if (warehouse_id == from_warehouse_id) {
                                    getOrderSortingList(relevant_id[0], 2);
                                } else {
                                    getOrderSortingList(relevant_id[0], 4);
                                }
                            }

                        }
                    }
                });
            }
        }
        hideOverlay();
    }


    function addOrderProductToInv_pre(){
        var warehouse_out_type = parseInt($('#warehouse_out_type').val());
        if (warehouse_out_type == 4 || warehouse_out_type == 5) {
            var shipping_cost = parseInt($("#shipping_cost").val());
            var warehouse_cost = parseInt($("#warehouse_cost").val());
            if(shipping_cost > 0){
            } else {
                alert('请填写调拨运费');
                return false;
            }
            if(warehouse_cost > 0){
            } else {
                alert('请填写仓库成本');
                return false;
            }

        }
        showOverlay();
        $("input[type=submit]").attr('disabled',true);
        var warehouse_id2 = parseInt($("#warehouse_id").text());
        var date_added = $('#date_start').val();
        var warehouse_id1 =parseInt($('#to_warehouse').val());
        var inventory_user =$('#inventory_user').text();
        var inventory_user_id =parseInt($('#inventory_user_id').val());
        var warehouse_out_type = parseInt($('#warehouse_out_type').val());
        if (warehouse_out_type == 3) {
            submitProducts1();
            return false;
        }
        if (warehouse_id1 == warehouse_id2) {
            var warehouse_id = warehouse_id2;
            var relevant_status_id = 6;
        } else {
            var warehouse_id = warehouse_id2;
            var relevant_status_id = 4;
        }
        var relevant_id = [];
        relevant_id.push(parseInt($("#product_name").text()));
        var container_id = [];
        $('.containerlist').each(function(){
            container_id.push($(this).text());
        });
        var product_ids = [];
        var quantitys = [];
        var containers = [];
        $(".product_lists").each(function () {
            var products = $(this).text();
            product_ids.push(products);
        });
        $(".num1_lists").each(function () {
            var quantity = $(this).text();
            quantitys.push(quantity);
        });
        if (warehouse_out_type != 4) {
            var confirm_text = "待扫描周转筐"+(window.plan_container_numbers-window.container_numbers)+'个，待扫描整件'+(window.product_plan_number-window.plan_numbers)+'件，是否确认提交完成？';
        } else {
            if (warehouse_id2 != warehouse_id1) {
                var confirm_text = '是否确认提交完成？';
            } else {
                var confirm_text = '待扫描整件'+(window.product_plan_number-window.plan_numbers)+'件，是否确认提交完成？';
            }
        }
        if(confirm(confirm_text)){
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'addPurchaseOrderRelevantToInv',
                    data : {
                        warehouse_id : warehouse_id2,
                        to_warehouse_id : warehouse_id1,
                        date_added : date_added,
                        added_by : inventory_user,
                        added_by_id : inventory_user_id,
                        warehouse_out_type : warehouse_out_type,
                        relevant_id : relevant_id,
                        shipping_cost:shipping_cost,
                        warehouse_cost:warehouse_cost,
                        relevant_suppler_id : global.warehouse_array,
                        container_id : container_id,
                        product_ids : product_ids,
                        quantitys : quantitys,
                    }
                },
                success : function (response, status, xhr){
                    var jsonData = $.parseJSON(response);


                    if(jsonData == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w_i.php';
                    }

                    if(jsonData == 1){
                        alert('提交成功');
                        location.reload();
                    }
                    if(jsonData == 0){
                        alert('请扫描商品后在提交');
                        return false;
                    }
                    hideOverlay();
                    $("input[type=submit]").attr('disabled',false);

                }
            });
        }else{
            hideOverlay();
            return false;
        }
        $("#shipping_cost").val('');
        $("#warehouse_cost").val('');

    }


    function goWindowUrl(id){
        window.location = 'consolidated_relevant.php?auth=xsj2015inv&ver=db&relevant_id='+id;
    }
    /*zx
    生成退货调拨单操作*/
    function createRelevantWarehouse(){
        $("#createRelevantWarehouse").hide();
        $("#productsHold").hide();
        $("#orderListTable").hide();
        $("#searchPurchaseOrder").hide();
        $("#product_back_information").hide();
        $('#productList').show();
        $('#creat_shipping_cost').show();
      var   select_option=$("#warehouse_out_type option:selected").val();
      if(select_option == 5){

      }else{
          $('#productsHoldDo2').show();
      }

        $("#barcodescanner").show();
        $("#current_product_plan2").html($('#local_warehouse_name').text());
        $("#current_product_quantity2").html($('#to_warehouse').find("option:selected").text());
        $("#current_product_quantity_change2").html('<?php echo date("Y-m-d",time());?>');
        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:reloadNewWarehouseRequisition();">返回</button>'+'');

    }
    /*
    * zx
    * 刷新页面
    * */
    function reloadNewWarehouseRequisition(type){
        switch (type) {
            case 2:
                $('#invMethods').show();
                $("#orderListTable").show();
                $("#contents").show();
                // $('#productsHoldDo').show();
                $('#productsHoldDo2').show();
                $("#searchPurchaseOrder").show();
                $("#much_look").show();
                $("#Order_View_Page").hide();
                $("#productList").hide();
                $('#productsInfoDo1').html('');
                $('#logo').html('<img src="view/image/logo.png" style="width:6em"/> 调拨操作<button class="invopt" style="display: inline;float:left" onclick="window.location.href=\''+'i.php?auth=xsj2015inv&ver=db'+'\'">返回</button><button class="invopt" style="display: inline" onclick="javascript:location.reload();">刷新</button>');
                searchRequisition();

                break;
            case 3:
                $('#productLists').hide();
                $('#productsInfoDo1').html('');
                orderViewItem(global.relevant_id);
                $("#Initial_Page").show();
                $("#productsHoldDo3").hide();
                $("#barcodescanner").hide();
                $("#Shipment_Page").hide();
                $("#Order_View_Page").show();
                $("#order_out_lists").show();
                $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:reloadNewWarehouseRequisition(2);">返回</button>'+'');

                break;
            default:
                $('#invMethods').show();
                $("#orderListTable").show();
                $('#productList').hide();
                $('#productsHoldDo').hide();
                $('#productsHoldDo2').show();
                $("#searchPurchaseOrder").show();
                $("#much_look").show();
                $('#productListsRelevant').show();
                $('#productsHoldRelevant').hide();


                $('#logo').html('<img src="view/image/logo.png" style="width:6em"/> 调拨操作<button class="invopt" style="display: inline;float:left" onclick="window.location.href=\''+'i.php?auth=xsj2015inv&ver=db'+'\'">返回</button><button class="invopt" style="display: inline" onclick="javascript:location.reload();">刷新</button>');
                getNewWarehouseRequisition();
                break;
        }

    }
    /*zx
    * 调拨单首页查询操作*/
    function getNewWarehouseRequisition(){
        $("#productLists").show();
        $("#contents").show();
        // getNewWarehouseStatus();
        var cookie_warehouse_id =parseInt($('#warehouse_id').text());
        var relevant_id = $('#inventory_order_sorting').val();
        var date_added = $('#date_start').val();
        var date_end = $('#date_end').val();
        var warehouse_out_type = parseInt($('#warehouse_out_type').val());
        var warehouse_id2 = parseInt($("#warehouse_id").text());
        var warehouse_id1 = parseInt($('#to_warehouse').val());
        if ((Date.parse(date_end)-Date.parse(date_added))>(3*24000*3600)) {
            alert("日期间隔不能大于三天");
            return false;
        }
        var to_warehouse_id =parseInt($('#to_warehouse').val());
        var from_warehouse_id =parseInt($('#from_warehouse').val());
	/*zx
        * DO类型调拨单可取消*/
        if (warehouse_out_type == 1) {
            if (warehouse_id1 == warehouse_id2) {
                $('#merge_relevant_orders').show();
                $('#update_relevant_orders').hide();
            } else {
                $('#merge_relevant_orders').hide();
                $('#update_relevant_orders').show();
            }
            $("#print_relevants_merge").show();
            $('#relevant_change').show();

        } else {
            $("#print_relevants_merge").hide();
            $('#relevant_change').hide();
            $('#update_relevant_orders').hide();
        }
        $("#get_warehouse_out_type").html($("#warehouse_out_type option:selected").text());
        if (warehouse_out_type == 3) {
            $("#form_return").show();
            $("#ordersHold").hide();
            searchRequisition();
            return false;
	    }
        $("#ordersHold").show();
        $("#form_return").hide();
        /*zx
        * 出入库不同且选择退货调拨单，显示生成退货调拨单按钮*/
        if (warehouse_id1 != warehouse_id2 && warehouse_out_type == 4) {
            $("#createRelevantWarehouse").show();
            $("#createIssueRelevantProduct").hide();
        }
        if ( warehouse_out_type == 5) {
            $("#createIssueRelevantProduct").show();
            $("#createRelevantWarehouse").hide();
        }
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getNewWarehouseRequisition',
                data: {
                    warehouse_id: cookie_warehouse_id,
                    relevant_id: relevant_id,
                    date_added: date_added,
                    date_end: date_end,
                    to_warehouse_id: to_warehouse_id,
                    warehouse_out_type: warehouse_out_type
                }
            },
            success: function (response, status, xhr) {

                if (response) {

                    var jsonData = $.parseJSON(response);

                    var html = '';

                    $.each(jsonData.data.container1, function (index, value) {
                        if (1 == 1) {
                            var t_status_class = '';

                            html += '<tr station_id="' + value.relevant_id + '">';
                            if (warehouse_out_type == 1) {
                                if (warehouse_id1 == warehouse_id2 && value.relevant_status_id == 3) {
                                    html += '<td></td>';
                                } else {
                                    html += '<td><input style="width:1.5em;height:1.5em;color: #040404" type="checkbox" name="relevant_ids[]" value="'+value.relevant_id+'"/></td>';
                                }
                            }
                            html += '<td>' + value.relevant_id +'<br />'+value.date_added.substr(0,11)+'</td>';
                            html += '<td ' + t_status_class + '>' + $('#to_warehouse').find("option:selected").text();
                            html += '<input type="hidden" id="order_comment_' + value.relevant_id + '" value="' + value.order_comment + '">';
                            html += '</td>';
                            var numbers2 = 0;
                            if (warehouse_id1 == warehouse_id2 || warehouse_out_type != 4) {
                                $.each(jsonData.data.container2, function (index2, value2) {
                                    if (parseInt(value2.relevant_id) == parseInt(value.relevant_id)) {
                                        numbers2 = value2.numbers2 == '' ? 0 : value2.numbers2;
                                    }
                                });
                            } else {
                                numbers2 = value.numbers;
                            }
                            html += '<td ' + t_status_class + '>' + numbers2 + '<br />[' + value.numbers + ']</td>';

                            $("#current_product_plan").html(value.title + '<input type="hidden" id="from_warehouse" value="'+ value.from_warehouse+'">');
                            // console.log(value.relevant_status_id);
                            if (warehouse_id1 == warehouse_id2) {
                                if (value.relevant_status_id == 4) {
                                    // if (warehouse_out_type == 1) {
                                    //     html += '<button  class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.relevant_id + ',' + 1 + ');">查看</button>';
                                    // } else {
                                    html += '<td ' + t_status_class + '>待入库</td>';
                                    html += '<td>';
                                    if ((Date.parse("<?php echo date("Y-m-d",time());?>")-Date.parse(value.deliver_date))>(3*24000*3600) && warehouse_out_type == 2) {
                                        html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.relevant_id + ',' + 4 + ');">调拨超期,请及时入库</button>';
                                    } else {
                                        html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.relevant_id + ',' + 4 + ');">入库</button>';
                                    }
                                } else {
                                    if (value.relevant_status_id == 6) {
                                        html += '<td ' + t_status_class + '>' + value.name + '</td>';
                                        if (value.out_type == 1) {
                                            html += '<td>';
                                            html += '<button  class="invopt" style="display: inline" onclick="javascript:goWindowUrl(' + value.relevant_id + ');">开始合单</button></td>';
                                        }
                                    } else {
                                        html += '<td ' + t_status_class + '>发货仓未出库，请联系发货仓</td>';
                                    }
                                    html += '<td><button  class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.relevant_id + ',' + 6 + ');">查看</button>';
                                }
                            } else {
                                if (value.relevant_status_id == 2) {
                                    html += '<td ' + t_status_class + '>待出库</td>';
                                    html += '<td>';
                                    if ((Date.parse("<?php echo date("Y-m-d",time());?>")-Date.parse(value.deliver_date))>(3*24000*3600) && warehouse_out_type == 2) {
                                        html += '<button id="inventoryIn" class="invopt" style="display: inline" >调拨超期,不可调拨</button>';
                                    } else {
                                        html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.relevant_id + ',' + 2 + ');">出库</button>';
                                    }
                                } else {
                                    html += '<td ' + t_status_class + '>' + value.name + '</td>';
                                    html += '<td>';
                                    html += '<button  class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.relevant_id + ',' + 1 + ');">查看</button>';
                                }
                            }
                            html += '</td>';
                            html += '</tr>';
                        }
                    });

                    $("#current_product_quantity").html($('#to_warehouse').find("option:selected").text());
                    $("#current_product_quantity_change").html($('#date_start').val());
                    $("#get_warehouse_out_type").html($("#warehouse_out_type option:selected").text());

                    $('#ordersList').html(html);
                    // console.log(html);
                    // if (jsonData.data.container1 != '') {
                    //     $('#much_look').show();
                    // }
                }
            }
        });
    }

    function logout_inventory_user(){
        if(confirm("确认退出？")){
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'inventory_logout'
                },
                success : function (response , status , xhr){
                    //console.log(response);
                    window.location = 'inventory_login.php?return=allocation_new_warehouse.php';
                }
            });
        }
    }


    <!--    --><?php //foreach($no_scan_product_id_arr_w_i as $key=>$value){ ?>
    //    window.no_scan_product_id_arr[<?php //echo $key;?>//] = <?php //echo $value;?>//;
    //    <?php //} ?>


    function qtyadd(id,status){
        var prodId = "#"+id;
        if (status == 1) {
            if ($(prodId).val() < (parseInt($("#plan" + id).val()) - parseInt($("#num_" + id).text()))) {

                var qty = parseInt($(prodId).val()) + 1;

                var do_qty = parseInt($("#do" + id).val()) - 1;


                if (qty >= parseInt($("#plan" + id).val())) {
                    qty = parseInt($("#plan" + id).val());
                    do_qty = 0;

                }

                $(prodId).val(qty);
                $("#do" + id).val(do_qty);
            }
        } else if (status == 2) {
            if(parseInt($(prodId).val()) - 1 >= 0) {
                var qty = parseInt($(prodId).val()) - 1;

                var do_qty = parseInt($("#do" + id).val()) + 1;


                if (qty >= parseInt($("#plan" + id).val())) {
                    qty = parseInt($("#plan" + id).val());
                    do_qty = 0;

                }

                $(prodId).val(qty);
                $("#do" + id).val(do_qty);
            }
        }
    }


    function qtyminus(id,status){
        var prodId = "#"+id;
        if (status == 1) {

            if($(prodId).val() >= 1){
                var qty = parseInt($(prodId).val()) - 1;
                $(prodId).val(qty);

                var do_qty = parseInt($("#do"+id).val()) + 1;
                $("#do"+id).val(do_qty);

            }
        } else if (status == 2) {
            var qty = parseInt($(prodId).val()) + 1;
            $(prodId).val(qty);

            var do_qty = parseInt($("#do"+id).val()) + 1;
            $("#do"+id).val(do_qty);

        }
        // console.log(id+':'+qty);
    }

    function qtyminus10(id,status){
        var prodId = "#"+id;
        if (status == 1) {

            if(parseInt($(prodId).val()) - 10 >= 0){
                var qty = parseInt($(prodId).val()) - 10;
                $(prodId).val(qty);

                var do_qty = parseInt($("#do"+id).val()) + 10;
                $("#do"+id).val(do_qty);

            }
            else{
                alert("入库数量不能超过采购数量，拒收超出商品或联系采购补充采购单");
            }
        } else if (status == 2) {
            var qty = parseInt($(prodId).val()) + 10;
            $(prodId).val(qty);

            var do_qty = parseInt($("#do"+id).val()) + 10;
            $("#do"+id).val(do_qty);
        }
        // console.log(id+':'+qty);
    }

    function qtyminus50(id,status){
        var prodId = "#"+id;
        if (status == 1) {

            if(parseInt($(prodId).val()) - 50 >= 0){
                var qty = parseInt($(prodId).val()) - 50;
                $(prodId).val(qty);

                var do_qty = parseInt($("#do"+id).val()) + 50;
                $("#do"+id).val(do_qty);

            }
            else{
                alert("入库数量不能超过采购数量，拒收超出商品或联系采购补充采购单");
            }
        } else if (status == 2) {
            var qty = parseInt($(prodId).val()) + 50;
            $(prodId).val(qty);

            var do_qty = parseInt($("#do"+id).val()) + 50;
            $("#do"+id).val(do_qty);
        }
        // console.log(id+':'+qty);
    }
    function print_relevants_merge() {
        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:reloadNewWarehouseRequisition();">返回</button>'+'');
        var cookie_warehouse_id =parseInt($('#warehouse_id').text());
        var relevant_ids = [];
        $.each($('input:checkbox:checked'),function(){
            relevant_ids.push($(this).val());
        });
        var from_warehouse_id =parseInt($('#from_warehouse').val());
        var warehouse_out_type = parseInt($('#warehouse_out_type').val());
        var to_warehouse_id = parseInt($('#to_warehouse').val());
        if (relevant_ids.length == 0) {
            play_sound(2);

            alert("请勾选前面的选择框");
            return false;
        }
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'print_relevants_merge',
                data: {
                    warehouse_id: cookie_warehouse_id,
                    to_warehouse_id: to_warehouse_id,
                    from_warehouse_id: from_warehouse_id,
                    warehouse_out_type: warehouse_out_type,
                    relevant_ids: relevant_ids,
                }
            },
            success: function (response, status, xhr) {

                if (response) {

                    var jsonData = $.parseJSON(response);
                    if (jsonData == 1) {
                        play_sound(2);

                        alert("请选择一样状态的调拨单");
                        return false;
                    } else if (jsonData == 3) {
                        play_sound(2);

                        alert("只能打印对应状态的调拨单");
                        return false;
                    }
                    var html = '';
                    var html2 = '';
                    $.each(jsonData.item,function(i,value) {
                        order_been_over = "style = ''";
                        order_been_over_size = "style = ''";
                        /*整件*/
                        if (value.container_id == "" || value.container_id == 0) {
                            html += '<tr class="container'+value.container_id+'" id="bd'+ value.product_id +'">';
                            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+'"><span id="containerm'+value.product_id+'" >';
                            html += value.relevant_id+'</th>';
                            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+'"><span id="containerm'+value.product_id+'" >';
                            html += '商品</th>';
                            html += '<th '+'style="display:none;"'+' class="relevantlist" id="relevant'+value.product_id+'" >';
                            html += value.relevant_id+'</th>';
                            html += '<th '+order_been_over+' class="product_lists'+value.container_id+'" id="container'+value.product_id+'" >';
                            html += value.product_id+'</th>';
                            html += '<th '+order_been_over+' class="containerlist" id="containerl'+value.product_id+'" ><span class="num1_lists" id="num_'+value.product_id+'"><span id="num2_'+value.product_id+'">'+value.num2+'</span></th>';
                            html += '<th '+order_been_over+'  id="containero'+value.product_id+'">';
                            html += value.name+'</th>';
                            html += '</tr>';
                            /*周转筐*/
                        } else {
                            html += '<tr class="container'+value.container_id+'" id="bd'+ value.product_id+value.container_id +'">';
                            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+value.container_id+'"><span id="containerm'+value.product_id+value.container_id+'" >';
                            html += value.relevant_id+'</th>';
                            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+value.container_id+'"><span id="containerm'+value.product_id+value.container_id+'" >';
                            html += value.container_id+'</th>';
                            html += '<th '+'style="display:none;"'+' class="relevantlist" id="relevant'+value.product_id+value.container_id+'" >';
                            html += value.relevant_id+'</th>';
                            html += '<th '+order_been_over+' class="product_lists'+value.container_id+'" id="container'+value.product_id+value.container_id+'" >';
                            html += value.product_id+'</th>';
                            html += '<th '+order_been_over+' class="containerlist" id="containerl'+value.product_id+value.container_id+'" ><span class="num1_lists" id="num_'+value.product_id+value.container_id+'">';
                            html += value.num2;
                            html += '</span>';
                            html += '</th>';
                            html += '<th '+order_been_over+'  id="containero'+value.product_id+value.container_id+'">';
                            html += value.name+'</th>';
                            html += '</tr>';
                        }

                    });
                    $.each(jsonData.main,function(i,value) {
                        html2 += '<tr >';
                        html2 += '<th >';
                        html2 += value.relevant_id+'</th><th>';
                        var relevant_id_status = value.relevant_status.split(',');
                        $.each(relevant_id_status,function(i2,value2) {
                            var relevant_status = value2.split('@');
                            html2 += relevant_status[0]+','+relevant_status[1]+','+relevant_status[2]+'<br />';
                        });
                        html2 += '</th></tr>';

                    });
                    $('#productsInfoRelevant').html(html);
                    $('#productsInfoRelevant2').html(html2);
                    $('#productListsRelevant').hide();
                    $('#productsHoldRelevant').show();

                }
            }
        });
    }
    function merge_relevant_orders() {
        var cookie_warehouse_id =parseInt($('#warehouse_id').text());
        var relevant_ids = [];
        $.each($('input:checkbox:checked'),function(){
            relevant_ids.push($(this).val());
        });
        var from_warehouse_id =parseInt($('#from_warehouse').val());
        var warehouse_out_type = parseInt($('#warehouse_out_type').val());
        var to_warehouse_id = parseInt($('#to_warehouse').val());
        var inventory_user_id =parseInt($('#inventory_user_id').val());
        if (relevant_ids.length <= 1) {
            play_sound(2);

            alert("请至少选择两个调拨单");
            return false;
        }
        if (confirm("只能合并已出库的调拨单,请确认货已收完后再合并")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'merge_relevant_orders',
                    data: {
                        warehouse_id: cookie_warehouse_id,
                        relevant_ids: relevant_ids,
                        to_warehouse_id: to_warehouse_id,
                        from_warehouse_id: from_warehouse_id,
                        warehouse_out_type: warehouse_out_type,
                        added_by_id: inventory_user_id,
                    }
                },
                success: function (response, status, xhr) {

                    if (response) {

                        var jsonData = $.parseJSON(response);
                        if (jsonData == 1) {
                            play_sound(3);

                            alert("调拨单合并成功，请刷新页面后进行合单");
                        } else if (jsonData == 2) {
                            play_sound(2);

                            alert("调拨单类型错误，请刷新页面");
                        } else if (jsonData == 3) {
                            play_sound(2);

                            alert("不是入库，请刷新页面");
                        } else if (jsonData == 4) {
                            play_sound(2);

                            alert("调拨单已合并，无法再次合并");
                        } else if (jsonData == 5) {
                            play_sound(2);

                            alert("调拨单不是已出库");
                        } else {
                            play_sound(2);

                            alert("调拨单合并失败");
                        }
                    }
                },
                complete : function(){
                    getNewWarehouseRequisition();
                }
             });
        }

    }
    /*zx
    * 删除合并的调拨单*/
    function delete_merge_relevant_orders() {
        var cookie_warehouse_id =parseInt($('#warehouse_id').text());
        var relevant_ids = [];
        $.each($('input:checkbox:checked'),function(){
            relevant_ids.push($(this).val());
        });
        var from_warehouse_id =parseInt($('#from_warehouse').val());
        var warehouse_out_type = parseInt($('#warehouse_out_type').val());
        var to_warehouse_id = parseInt($('#to_warehouse').val());
        var inventory_user_id =parseInt($('#inventory_user_id').val());
        if (relevant_ids.length != 1) {
            play_sound(2);

            alert("一次只能删除一个调拨单");
            return false;
        }
        if (confirm("只能删除被合并的调拨单,请确认后再删除")) {
            if (confirm("请确认该调拨单未进行合单操作")) {
                $.ajax({
                    type: 'POST',
                    url: 'invapi.php',
                    data: {
                        method: 'delete_merge_relevant_orders',
                        data: {
                            warehouse_id: cookie_warehouse_id,
                            relevant_ids: relevant_ids,
                            to_warehouse_id: to_warehouse_id,
                            from_warehouse_id: from_warehouse_id,
                            warehouse_out_type: warehouse_out_type,
                            added_by_id: inventory_user_id,
                        }
                    },
                    success: function (response, status, xhr) {

                        if (response) {

                            var jsonData = $.parseJSON(response);
                            if (jsonData == 1) {
                                play_sound(3);
                                alert("调拨单删除成功");
                            } else if (jsonData == 2) {
                                play_sound(2);
                                alert("调拨单类型错误，请刷新页面");
                            } else if (jsonData == 3) {
                                play_sound(2);
                                alert("不是入库，请刷新页面");
                            } else if (jsonData == 4) {
                                play_sound(2);
                                alert("该调拨单不是合并后的，无法删除");
                            } else if (jsonData == 5) {
                                play_sound(2);
                                alert("调拨单不是已入库");
                            } else {
                                play_sound(2);
                                alert("调拨单删除失败");
                            }
                        }
                    },
                    complete : function(){
                        // location.reload();
                    }
                });
            }
        }

    }
    /*zx
    * 取消调拨单*/
    function update_relevant_orders() {
        var cookie_warehouse_id =parseInt($('#warehouse_id').text());
        var relevant_ids = [];
        $.each($('input:checkbox:checked'),function(){
            relevant_ids.push($(this).val());
        });
        var from_warehouse_id =parseInt($('#from_warehouse').val());
        var warehouse_out_type = parseInt($('#warehouse_out_type').val());
        var to_warehouse_id = parseInt($('#to_warehouse').val());
        var inventory_user_id =parseInt($('#inventory_user_id').val());
        if (relevant_ids.length != 1) {
            play_sound(2);

            alert("一次只能取消一个调拨单");
            return false;
        }
        if (confirm("只能取消未出库的调拨单,请确认后")) {
            if (confirm("请确认该调拨单未进行出库操作")) {
                $.ajax({
                    type: 'POST',
                    url: 'invapi.php',
                    data: {
                        method: 'update_relevant_orders',
                        data: {
                            warehouse_id: cookie_warehouse_id,
                            relevant_ids: relevant_ids,
                            to_warehouse_id: to_warehouse_id,
                            from_warehouse_id: from_warehouse_id,
                            warehouse_out_type: warehouse_out_type,
                            added_by_id: inventory_user_id,
                        }
                    },
                    success: function (response, status, xhr) {

                        if (response) {

                            var jsonData = $.parseJSON(response);
                            if (jsonData == 1) {
                                play_sound(3);
                                alert("调拨单取消成功");
                            } else if (jsonData == 2) {
                                play_sound(2);
                                alert("调拨单类型错误，请刷新页面");
                            } else if (jsonData == 3) {
                                play_sound(2);
                                alert("不是出库前，无法取消");
                            } else if (jsonData == 4) {
                                play_sound(2);
                                alert("该调拨单不是合并后的，无法删除");
                            } else if (jsonData == 5) {
                                play_sound(2);
                                alert("调拨单出库后，无法取消");
                            } else {
                                play_sound(2);
                                alert("调拨单取消失败");
                            }
                        }
                    },
                    complete : function(){
                        // location.reload();
                        getNewWarehouseRequisition();
                    }
                });
            }
        }

    }

    /*zx
    * 仓内调拨单*/
    function searchRequisition(){
        $('#productLists').hide();
        $('#merge_relevant_orders').hide();
        var filter_out_type = $('select[name=\'out_type\']').find("option:selected").text();
        var filter_out_type_id = $('select[name=\'out_type\']').val();
        var cookie_warehouse_id =$('#warehouse_id').text();

        var date_start = $("#date_start").val();
        var date_end = $("#date_end").val();
        $.ajax({
            type: 'POST',
            url : 'invapi.php',
            data:{
                method : 'searchRequisitionNew',
                data: {
                    date_start: date_start,
                    date_end: date_end,
                    filter_out_type:filter_out_type,
                    filter_out_type_id:filter_out_type_id,
                }
            },
            success: function (response) {

                var html = "";
                var jsonData = $.parseJSON(response);
                if(response){
                    $.each(jsonData,function(i,v){

                        html += "<tr id='"+ v.relevant_id+"'>";
                        html += "<th>"+ v.relevant_id+"<br />"+v.date_added.substr(0,10)+"</th>";
                        // html += "<td>"+ v.out_type+ "</td>";
                        // html += "<td>"+ v.title+ "</td>";
                        // html += "<th>"+ v.date_added+ "</th>";
                        html += "<th>"+ v.username+ "</th>";
                        html += "<th>"+ v.name+ "</th>";
                        html += "<th>"+ v.comment+ "</th>";

                        if (parseInt(v.relevant_status_id) != 2) {
                            html += "<th>";
                            html += '<input type="button" class="invopt" value="查看" onclick="orderViewItem('+v.relevant_id +')">';
                            html += "</th>";
                        } else {
                            html += "<th>";
                            html += '<input type="button" class="invopt" value="调库" onclick="orderViewItem('+v.relevant_id +')">';
                            html += "</th>";
                        }
                        // if (parseInt(v.relevant_status_id) != 4) {
                        //     html += "<th>";
                        //     html += '<input type="button" class="invopt" value="查看" onclick="viewItem('+v.relevant_id +')">';
                        //     html += "</th>";
                        // } else {
                        //     html += "<th>";
                        //     html += '<input type="button" class="invopt" value="入库" onclick="orderViewItem('+v.relevant_id +')">';
                        //     html += "</th>";
                        // }
                        html += "</tr>";
                    });
                    $("#warehouse_product_relevant").html(html);
                }
            }
        });
    }

    function  orderViewItem(relevant_id){
        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:reloadNewWarehouseRequisition(2);">返回</button>'+'');
        global.relevant_id = relevant_id;
        $('#invMethods').hide();
        $("#orderListTable").hide();
        $("#contents").hide();
        $('#productList').show();
        // $('#productsHoldDo').show();
        $('#productsHoldDo2').hide();
        $("#searchPurchaseOrder").hide();
        $("#much_look").hide();
        $("#Order_View_Page").show();
        $("#productList").show();
        $("#product_name3").html(relevant_id);
        $("#current_product_quantity_change3").html($('#local_warehouse_name').text());
        var warehouse_id = $("#warehouse_id").text();

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'startShipmentNew',
                data: {
                    relevant_id: relevant_id,
                    warehouse_id:warehouse_id,
                }
            },
            success:function(response){
                var html = "";
                var jsonData = $.parseJSON(response);
                if(jsonData.product2){
                    $.each(jsonData.product2,function(i,v) {
                        if(v.out_type == 3){
                            $("#change_info").show();

                            global.products_array[v.product_id] = v;
                            html += "<tr  style='background-color: #d0e9c6 ' id='change_color_"+v.product_id+"'><td ><input type='hidden' id='in_warehouse_product' value='"+v.product_id+"'/>" + v.product_id +"/"+v.name+"</td>";
                            html += "<td >" + v.stock_area + "</td>";
                            html += "<td >" + v.inventory + "</td>";
                            html += "<td>";
                            html +='<input class="submit"  type="button"   value="操作" onclick="javascript:startShipment(\''+ relevant_id+'\',\''+v.product_id +'\',3,\''+v.num+'\',\''+v.name+'\',\''+v.inventory+'\',\''+(v.comment.split(':')[0])+'\');" />';
                            html += "</td>";
                            html += "</tr>";
                        }
                    });
                    // $("#submit").hide();
                } else {
                    // $("#submit").show();
                }

                if(jsonData.product1){
                    $.each(jsonData.product1,function(i,v) {
                        global.products_array[v.product_id] = v;
                        if(v.out_type == 3){
                            $("#change_info").show();
                            var color_style = "style='background-color: #d0e9c6'";
                            if (v.submit_status == 1) {
                                color_style = "style='background-color: gray'";
                            }
                            html += "<tr "+color_style+" id='change_color_"+v.product_id+"'><td >" + v.product_id +"/"+v.name+"</td>";
                            html += "<td >" + v.stock_area + "</td>";
                            html += "<td >" + v.inventory + "</td>";
                            html += "<td>";
                            if (v.submit_status == 1) {
                                html += '<input class="submit"  type="button"   value="查看" onclick="javascript:startShipment(\''+ relevant_id+'\',\''+v.product_id +'\',1,\''+v.num+'\',\''+v.name+'\',\''+v.inventory+'\',\''+v.product_id2+'\');" />';
                            } else {
                                html +='<input class="submit"  type="button"   value="操作" onclick="javascript:startShipment(\''+ relevant_id+'\',\''+v.product_id +'\',2,\''+v.num+'\',\''+v.name+'\',\''+v.inventory+'\',\''+v.product_id2+'\');" />';
                            }
                            html += "</td>";
                            html += "</tr>";
                        }
                    });
                }

                $("#order_out_lists").html(html);
            }

        });
    }

    function startShipment(relevant_id,product_id,status,num,name,inventory,product_id2){
        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:reloadNewWarehouseRequisition(3);">返回</button>'+'');
        window.product_id1 = product_id;
        window.product_id2 = product_id2;

        $("#Initial_Page").hide();
        $("#productsHoldDo3").show();
        $("#barcodescanner").show();
        $("#Shipment_Page").show();
        $("#current_product_quantity3").html(inventory);
        $("#current_product_plan3").html(name);
        $("#Order_View_Page").hide();
        $("#order_out_lists").hide();
        //$("#current_product_quantity3").html($('#to_warehouse').find("option:selected").text());
        //$("#current_product_quantity_change3").html('<?php //echo date("Y-m-d",time());?>//');
        $("#return_relevant_id").text(relevant_id);
        var warehouse_id = $("#warehouse_id").text();
        window.product_id_arr = {};

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'makeGetReadyLists',
                data: {
                    relevant_id: relevant_id,
                    warehouse_id:warehouse_id,
                    product_id:product_id,
                    product_id2:product_id2
                }
            },
            success:function(response){
                var html1 = "";
                var html2 = "";
                var jsonData = $.parseJSON(response);
                if(jsonData.products){
                    $.each(jsonData.products,function(i,v) {

                        if(v.out_type == 3){
                            if (product_id == v.product_id) {
                                product_id_arr[v.product_id] = v.product_id;
                                product_id_arr[v.sku_barcode] = v.product_id;
                                html1 += "<div id='shipment1_" + v.product_id + "' product = '"+v.product_id+"' class= 'submit_status' style='background-color: #d0e9c6 '>";
                                html1 += "<tr><th>商品名称</th><th id='name7_"+v.product_id+"'>" + v.product_id +"/"+v.name+"</th></tr>";
                                html1 += "<tr><th>货位号</th><th id='name4_"+v.product_id +"'>" + v.istock_area + "</th></tr>";
                                console.log(v.num);
                                if (typeof (v.stock_area) == 'undefined') {
                                    html1 += '<tr id="stock_in"  style="display:;"><th>扫描出库货位号</th><th><input id="bar_code1_'+v.product_id+'"  type="text" class="submit_success" style="height: 25px;margin-top:10px;border:1px solid;display: none;" placeholder="扫描商品货位号"  onkeyup="javascript:checkKey1(\''+ v.product_id+'\',\''+v.name+'\',\''+v.num+'\');"/></th></tr>';
                                }
                                var quantity = typeof(v.quantity)=="undefined"?v.num:v.quantity;
                                html1 += "<tr><th>调拨数量</th><th class='key_plan' id='num3_"+v.product_id +"'>" + v.num + "</th></tr>";
                                html1 += "<tr id='out_product_make1' style='display:none'><th>";
                                html1 += '<input type="hidden" id="box1_'+v.product_id+'" value="'+v.box_size+'">';
                                html1 += "待扫描数量</th><th name='key_words' id='num4_"+v.product_id +"'>"+quantity+"</th></tr>";
                                if (typeof (v.quantity) != 'undefined') {
                                    html1 += '<tr id="out_product_make2" style="display:;"><th>操作</th><th id="make_product_result1">已移出';
                                    $("#warehouse_product_relevant4").show();
                                } else {
                                    html1 += '<tr id="out_product_make2" style="display:none;"><th>操作</th><th id="make_product_result1"><input class="submit" style="display: none;" type="button" id="button_id1' + v.product_id + '"  value="确认移出" onclick="javascript:submitProduct(\'' + v.product_id + '\',2);" />';
                                }
                                html1 += "</th>";
                                html1 += "</tr>";
                                html1 += "</div>";
                                html1 += "<br />";
                            } else {
                                product_id_arr[v.product_id] = v.product_id;
                                product_id_arr[v.sku_barcode] = v.product_id;
                                html2 += "<div id='shipment2_" + v.product_id + "' product = '" + v.product_id + "' class= 'submit_status' style='background-color: #d0e9c6 '>";
                                html2 += "<tr><th id = 'scale_name" + v.product_id + "' >转化商品ID</th><th id='name5_" + v.product_id + "'>" + v.name + "</th></tr>";
                                html2 += "<tr><th>转化商品货位号</th><th id='name6_" + v.product_id + "'>" + v.istock_area + "</th></tr>";
                                if (typeof (v.stock_area) == 'undefined') {
                                    html2 += '<tr id="stock_out" style="display:;"><th id = "scale_stock' + v.product_id + '" >扫描转化商品货位号</th><th><input id="bar_code1_' + v.product_id + '" type="text" class="submit_success"  style="height: 25px;margin-top:10px;border:1px solid;display: none;" placeholder="扫描商品货位号"  onkeyup="javascript:checkKey2(\'' + v.product_id + '\',' + v.product_id + ');"/></th></tr>';
                                }
                                var quantity2 = typeof(v.quantity)=="undefined"?v.scale_num:v.quantity;
                                html2 += "<tr><th id = 'scale_num" + v.product_id + "' >转化数量</th><th id='num5_" + product_id + "'>" + quantity2 + "</th></tr>";
                                html2 += "<tr id='out_product_make3' style='display:;'><th>";
                                html2 += '<input type="hidden" id="box2_'+product_id+'" value="'+v.box_size+'">';
                                html2 += "操作</th><th id='make_product_result2'>";
                                if (typeof (v.stock_area) == 'undefined') {
                                    html2 += '<input class="submit"  style="display: none;" type="button" id="button_id2' + v.product_id + '"  value="确认移入" onclick="javascript:submitProduct(\'' + v.product_id + '\',4);" />';
                                } else {
                                    html2 += '已移入';
                                }
                                console.log(v.stock_area);

                                html2 += "</th>";
                                html2 += "</tr>";
                                html2 += "</div>";
                                html2 += "<br />";
                                // if (status == 2) {
                                //     if (typeof (v.stock_area) == 'undefined') {
                                //         html2 += '<tr><input class="qtyopt"  type="button" id="button_ch" style=" float: right;display: none;" value="提交" onclick="javascript:submitProducts1(\''+product_id+'\',\''+v.product_id+'\');" ></tr>';
                                //     } else {
                                //         html2 += '<tr><input class="qtyopt"  type="button" id="button_ch" style=" float: right;display:;" value="提交" onclick="javascript:submitProducts1(\''+product_id+'\',\''+v.product_id+'\');" ></tr>';
                                //     }
                                // } else {
                                //     html2 += '<tr><th>已提交</th></tr>';
                                // }
                            }
                            $("#change_info").show();
                        }
                    });
                }
                // if (status == 2) {
                    $("#warehouse_product_relevant3").html(html1);
                // } else if (status == 4) {
                    $("#warehouse_product_relevant4").html(html2);
                // }
                // if (status == 2) {
                //     // $("#warehouse_product_relevant3").hide();
                //     // $("#warehouse_product_relevant4").show();
                // } else if (status == 4) {
                //     // $("#warehouse_product_relevant3").show();
                //     // $("#warehouse_product_relevant4").show();
                //     $("#button_ch").show();
                //     $("#stock_in").hide();
                //     $("#stock_out").hide();
                // }
            }

        });
    }

    $("input[name='bar_code']").keyup(function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
        if(tmptxt.length >= 4){
            handleProductList2();
        }

    }).bind("paste",function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
    });
    $("input[name='key_words']").keyup(function(){
        var tmptxt=$(this).val();
        var tmptxt1=$('.key_plan').text();
        console.log(tmptxt1);
        $(this).val(tmptxt.replace(/\D/g,''));
        if(tmptxt <= 0){
            $(this).val(0);
        } else if (tmptxt >= tmptxt1) {
            $(this).val(tmptxt1);
        }

    }).bind("paste",function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
    });
    function checkKey1(product_id,comment,quantity) {

            var $stock_area1 = $("#bar_code1_"+product_id);
            var stock_area1 = $stock_area1.val().trim();
            var stock_area1s = $("#name4_"+product_id).text();
            if (stock_area1s !== stock_area1) {
                $stock_area1.val("");
                play_sound(2);
                alert("货位号不存在或错误,请重新扫描");
                $("#button_ch").hide();
            } else {
                play_sound(3);
                $("#button_id1"+product_id).show();
                $("#out_product_make1").show();
                var plan_num = parseInt($("#num3_"+product_id).text());
                var product_num1 = parseInt($('#num4_'+product_id).text());
                var product_num = plan_num-product_num1;
                make_product_information(product_id,comment,quantity,product_num,"productsHoldDo1","productsInfoDo1");
            }

    }
    function checkKey2(product_id,comment) {

            var $stock_area2 = $("#bar_code1_"+product_id);
            var stock_area2 = $stock_area2.val().trim();
            var stock_area2s = $("#name6_"+product_id).text();
            if (stock_area2s !== stock_area2) {
                $stock_area2.val("");
                play_sound(2);
                alert("货位号不存在或错误,请重新扫描");
                $("#button_ch").hide();
                return false;
            } else {
                play_sound(3);
                var box1 = $("#box1_"+window.product_id1).val();
                var box2 = $("#box2_"+window.product_id1).val();
                var plan_num = parseInt($("#num3_"+window.product_id1).text());
                $("#num5_"+window.product_id1).html((plan_num*box1)/box2);
                $("#button_id2"+product_id).show();
            }

    }



    /*zx
    *临时表提交 */
    function submitProduct(product_id,status){

        if (status == 2) {
            $("#button_id1"+product_id).hide();
            var product_stock_area = $.trim($('#bar_code1_'+product_id).val());
            var plan_num = parseInt($("#num3_"+product_id).text());
            var product_num1 = parseInt($('#num4_'+product_id).text());
            var product_num = plan_num-product_num1;


        } else if (status == 4) {
            $("#button_id2"+product_id).hide();
            var product_stock_area = $.trim($('#bar_code1_'+product_id).val());
            var product_num = parseInt($('#num5_'+window.product_id1).text());

        }
        var relevant_id = $("#return_relevant_id").text();
        var warehouse_id = $("#warehouse_id").text();
        var text = "";
        // if (product_stock_area.length > 0) {
        // } else {
        //     alert("货位号不能为空");
        //     return false;
        // }
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'submitProductNew',
                data:{
                    relevant_id: relevant_id,
                    warehouse_id: warehouse_id,
                    product_id: product_id,
                    product_id1: window.product_id1,
                    product_num: product_num,
                    product_stock_area: product_stock_area,
                    status: status,
                }

            },
            success:function(response){
                if(response){
                    play_sound(3);
                    // startShipment(relevant_id,product_id,4);
                    // console.log(121);
                    $("#warehouse_product_relevant4").show();
                    if (status == 4) {
                        $("make_product_result2").html("已移入");
                        submitProducts1(window.product_id1,window.product_id2);
                    } else {
                        $("make_product_result1").html("已移出");
                    }
                    // $("#shipment_"+product_id).css("background-color", "#ADADAD");

                }
            }
        });

    }
    /*
    * zx
    * 仓内调拨单提交
    *
    * */
    function submitProducts1(product_id1,product_id2){
        var warehouse_user = $("#warehouse_user").text();
        var relevant_id = $("#return_relevant_id").text();
        var warehouse_id = $("#warehouse_id").text();
        var plan_num = parseInt($("#num3_"+product_id1).text());
        var product_num1 = parseInt($('#num4_'+product_id1).text());
        var product_num2 = parseInt($('#num5_'+product_id1).text());
        // console.log($("#num3_"+product_id1).text());
        // console.log($('#num4_'+product_id1).text());
        // console.log($('#num5_'+product_id1).text());
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'submitProductsNew',
                data: {
                    warehouse_id:warehouse_id,
                    relevant_id:relevant_id,
                    warehouse_user:'<?php echo $_COOKIE['inventory_user_id'];?>',
                },

            },
            success: function (response) {
                if (response) {
                    alert('提交成功');
                    $("#change_color_"+window.product_id1).css("background-color", "gray");
                    $("#warehouse_product_relevant4").hide();
                    reloadNewWarehouseRequisition(3);
                }
            }
        });
    }
    /*
    * zx
    * 仓内调拨单数量提交
    *
    * */
    function addProducts(product_id1){
        play_sound(3);
        var box1 = $("#box1_"+product_id1).val();
        var box2 = $("#box2_"+product_id1).val();
        var plan_num = $("#num3_"+product_id1).text();
        var num = $("#"+product_id1).val();
        $("#num4_"+product_id1).html(plan_num-num);
        $("#num5_"+product_id1).html((num*box1)/box2);
        // console.log((num*box1)/box2);

        $("#productsInfoDo1").html('');
        $("#out_product_make2").show();
    }
    /*
    * 生成商品详情栏，可进行加减操作
    *
    *table_id         表名id
    *tbody_id         tbody栏id
    *product_name     商品名栏id
    *plan_quantity    计划数量栏id
    *quantity         实际数量栏id
    *operation        操作栏id
    *prodId           实际操作数量的id
    *planId           计划数量的id
    *
    * */
    function make_product_information(id,name,quantity,real_quantity,table_id,tbody_id){

        var table_id = "#"+table_id;
        var tbody_id = "#"+tbody_id;
        var product_name_id = "product_name1"+id;
        var plan_quantity_id = "current_product_plan1"+id;
        var quantity_id = "current_product_quantity1"+id;
        var operation_id = "current_product_quantity_change1"+id;
        var prodId = id;
        var planId = "pid"+id;
        $(tbody_id).html('<tr id="clear' + id + '"><input type="hidden" id="get_product_id" value="' +
            id + '"/><td colspan="3" id="'+product_name_id+'" align="center" style="font-size:1.4em;"></td></tr><tr id="clearss' +
            id + '"><th style="width:4em">总量</th><th style="width:4em">已扫描</th><th align="center" id ="manysubmits"><button style="float:left" class="invopt manysubmits" onclick="javascript:addProducts(' +
            id + ');">提交</button></th></tr><tr id="clears'+
            id + '"><td id="'+
            plan_quantity_id+'" align="center" style="font-size:1.5em;"></td> <td id="'+
            quantity_id+'" align="center" style="font-size:1.4em;"></td> <td id="'+
            operation_id+'"></td></tr>');
        $("#"+product_name_id).html(name);
        $("#"+plan_quantity_id).html(quantity + '<span style="display:none;" name="productId" id="'+planId+'">' + quantity + '</span>');
        $("#"+quantity_id).html('<input class="qty"  id="'+prodId+'" value="' + real_quantity + '">');
        var html = '<input style="height:3em;width:3em;" class="qtyopt" type="button" value="-" onclick="javascript:qtyminusadd(\'' +
            id + '\',1,1,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+" onclick="javascript:qtyminusadd(\'' +
            id + '\',2,1,\''+prodId+'\',\''+planId+'\')">';
        if (quantity < 10 && quantity > 0) {
        } else {
            html += '<input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+10" onclick="javascript:qtyminusadd(\'' +
                id + '\',2,10,\''+prodId+'\',\''+planId+'\')">';
        }
        if (quantity < 50 && quantity >0) {
        } else {
            html += '<input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+50" onclick="javascript:qtyminusadd(\'' +
                id + '\',2,50,\''+prodId+'\',\''+planId+'\')">'
        }
        $("#"+operation_id).html(html);
        $(table_id).show();

    }
    /*
    * 商品数量操作
    *id          商品id
    *status      1为减，2为加
    *num         需要操作的数量
    *prodId      操作数量的id
    *planId      计划数量id
    * */
    function qtyminusadd(id,status,num,prodId,planId){
        var prodId = "#"+prodId;
        var planId = "#"+planId;
        if (status == 1) {
            if($(prodId).val() >= num){
                var qty = parseInt($(prodId).val()) - num;
                $(prodId).val(qty);
            }
        } else if (status == 2) {
            if($(prodId).val() <= $(planId).text() -num) {
                var qty = parseInt($(prodId).val()) + num;
                $(prodId).val(qty);
            }
        }
    }
    function play_sound(type){
        var player = $("#player"+type)[0];
        player.play();
    }

    // function check_in_array(stringToSearch, arrayToSearch) {
    //     for (s = 0; s < arrayToSearch.length; s++) {
    //         thisEntry = arrayToSearch[s].toString();
    //         if (thisEntry == stringToSearch) {
    //             return true;
    //         }
    //     }
    //     return false;
    // }
</script>
</body>
</html>