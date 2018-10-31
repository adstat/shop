<?php
if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';
//exit('系统繁忙，请稍候...');
$file_name = empty($_GET['file_name']) ? 'w2' : $_GET['file_name'];
$inventory_user_admin = array('1','22');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
if(!in_array($_COOKIE['user_group_id'],[1,15,22])){
    //重定向浏览器
    header("Location: i.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪订单分拣－快消</title>
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
            /*height:2.5em;*/
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

        .submitting{
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
        }

        .innerSubmit{
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
            font-size:1rem;
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
            width:1.2em;
            height:1.2em;
            font-size: 2.8rem;
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

        #productsHold th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHoldDo td{
            background-color:#d0e9c6;
            color: #000;
            /*height: 2em;*/
            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHoldDo th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;
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


        #vg_list div{
            font-size: 1rem;
            border-radius: 5px;
            background-color: #d0e9c6;
            margin: 0.2rem;
            margin-right: 0.6rem;
            float: left;
            line-height: 1.4rem;
            padding:0.3rem 0 0.2rem 0.2rem;
        }

        .frame_num{
            background-color: yellow;
            font-size: 1.2rem;
            border-radius: 5px;
            padding:0.2rem;
        }

    </style>

    <style media="print">
        .noprint{display:none;}
    </style>

    <script>
        window.product_barcode_arr = {};
        window.product_barcode_arr_s = {};
        <?php if(!in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
        //$(document).keydown(function (event) {
        //$('#product').focus();
        //});
        <?php } ?>
    </script>
</head>
<body>
<div  style="display: none" id="warehouse_id"><?php echo $_COOKIE['warehouse_id'];?></div>
<script type="text/javascript">
    var is_admin = 0;

    var global = {};
    global.warehouse_id = parseInt($("#warehouse_id").text());
</script>
<div align="right"><?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <span onclick="javascript:logout_inventory_user();">退出</span>
    <?php if(in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
        <script type="text/javascript">
            is_admin = 1;

        </script>

        <a target="_blank" href="invSortingData.php">查看分拣数据</a>
        <a target="_blank" href="invSortingCheck.php">查看未分拣数据</a>
    <?php } ?>
</div>
<div align="center" style="display:block; margin:0.3rem auto" id="logo"><img src="view/image/logo.png" style="width:6em"/> 个人领单<button class="invopt" style="display: inline" onclick="javascript:location.reload();">刷新</button><button class="invopt" style="display: inline;float:left" onclick="window.location.href='<?php echo $file_name;?>.php?auth=xsj2015inv&ver=db'">返回</button></div>




<div id="login" align="center" style="margin:0.5em auto; display: none">
    <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
    <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
</div>

<div id="content" style="display: block">
    <div align="center" id="orderListTable" style="margin:0.5em auto; font-size: 0.8rem">
        <div style="margin:0.3rem; padding:0.3rem; color:#5e5e5e; font-style: italic; background-color: #d6d6d6; border: 1px dashed #5e5e5e;">
            <button style="color:red;" >领单之前请确保订单都已拣完</button>
        </div>
        <input type="hidden" id="current_order_id" value="">
        <table border="0" style="width:100%;" cellpadding="2" cellspacing="3" id="ordersHold">
            <thead>
            <tr>
                <th>订单ID</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="ordersList">

            </tbody>
        </table>



    </div>
    <div align="center" style="margin:0.5em auto;">

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

            $date_array[0]['date'] = date("m-d",time() + 8*3600);
            $date_array[1]['date'] = date("m-d",time() + 8*3600 + 24*3600);
            $date_array[2]['date'] = date("Y-m-d",time() + 8*3600 + 9*3600);
            $date_array[2]['shortdate'] = date("m-d",time() + 8*3600 + 9*3600);

        }


        ?>
        <input style="display: none; font-size: 0.9em; line-height: 0.9em" id="getplanned" class="submit_s style_lightgreen" type="button" value="获取计划入库值(<?php echo $date_array[2]['shortdate']; ?>)" onclick="javascript:getSortingList('<?php echo $date_array[2]['date']; ?>');">
        <input type="hidden" value=0 id="purchasePlanId" />
    </div>
    <div id="message" class="message style_ok" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
    <div id="alertinfo" class="message style_error" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
    <div id="inv_control" align="center">
        <div id="invMethods">

        </div>
        <div id="shelfLifeStrict" style="display: none"></div>
        <div id="productList" name="productList" method="POST" style="display: none">

            <div id="product_name" style="font-size:2em; display: none;" align="center"></div>
            <table id="productsHoldDo" border="0" style="width:100%;display:none;"  cellpadding=2 cellspacing=3>
                <tr>
                    <th style="width:4em">商品数</th>
                    <th style="width:4em">待分拣</th>
                    <th align="center" id="current_do_tj"></th>
                </tr>
                <tbody id="productsInfoDo">
                <tr>
                    <td id="current_product_plan"  align="center" style="font-size:2.5rem;"></td>
                    <td id="current_product_quantity" align="center" style="font-size:2.8rem;"></td>
                    <td align="center">
                        <div id="current_product_quantity_change_memo">请扫描商品条码</div>

                        <input type="hidden" id="current_product_quantity_change_start" value="0" />
                        <div id="current_product_quantity_change" style="display: none;"></div>
                        <div id="quehuotixing" style="display: none;"></div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div id="barcodescanner" style="display: none">
                <form method="get" onsubmit="handleProductList(); return false;">
                    <button class="addprod style_red" id="byKeyboard" type="button" style="font-size: 0.9rem; padding: 0.1em" onclick="switchKeyboardScan();">改输入</button>
                    <input id="byKeyboardFlag" type="hidden" value ="0" style="font-size: 1em; padding: 0.2em">
                    <input name="product" id="product" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描" style="height: 2em;" onfocus="scannerFocused();" />
                    <input class="addprod style_green" id="productSubmitButton"  type="submit" value ="OK" style="font-size: 1.1rem; padding: 0.2rem; margin-left: 0.3rem" />
                </form>
            </div>
            <script type="text/javascript">

                function switchKeyboardScan(){
                    var byKeyboardFlag = parseInt($("#byKeyboardFlag").val());
                    if(byKeyboardFlag == 1){
                        $("#byKeyboard").text("改输入");
                        $("#product").attr("placeholder","点击激活开始扫描");
                        $("#byKeyboardFlag").val(0);
                    }
                    else{
                        $("#byKeyboard").text("改扫描");
                        $("#product").attr("placeholder","点击开始手工输入");
                        $("#byKeyboardFlag").val(1);
                        $("#product").focus();
                    }

                }

                $("input[name='product']").keyup(function(){
                    var tmptxt=$(this).val();
                    //$(this).val(tmptxt.replace(/\D/g,''));
                    var byKeyboardFlag = parseInt($("#byKeyboardFlag").val());
                    if(!byKeyboardFlag){
                        if(tmptxt.length >= 4){
                            handleProductList();
                        }
                        $(this).val("");
                    }

                });
                //$("input[name='product']").css("ime-mode", "disabled");
            </script>




            <!-- 分拣完成后台提交 -->
            <div id="fastmove_order_comment" style="margin: 1rem 0 2.5rem 0; padding: 0.5rem; display: none; background-color: #c9e2b3;">
                <input type="hidden" id="box_count" class="fm_box_count" value="0" readonly="readonly" />
                <div style="margin: 0.5rem 0; font-weight: bold;">已分拣整件数: <span id="nonRepackBoxCount" style="font-size: 1.2rem">0</span></div>
                <div style="margin: 0.5rem 0;">
                    <span style="font-size: 1.2rem">货位号</span>
                    <input type="text" style="font-size: 1.5rem; width:5rem; height:2.8rem; padding: 0.2rem; border: 1px #333333 solid;" id="inv_comment" class="fm_inv_comment" />
                </div>
                <div style="font-size: 0.8rem">[货位号为3位以下数字]</div>
                <input class="innerSubmit" id="submitSorting" type="button" value="分拣完成" onclick="javascript:addOrderProductToInv_pre(1);">
            </div>


            <script type="text/javascript">
                $("input[id='inv_spare_comment']").keyup(function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));

                    if(tmptxt.length >= 4){

                    }else{
                        $(this).val("");
                    }

                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));
                });
                //$("input[name='product']").css("ime-mode", "disabled");
            </script>



            <div id="fastmove_order_frame" style="margin: 1rem 0 2.5rem 0; display: none; ">
                当前使用的周转框：<input type="" id="frame_vg_product" class="fm_frame_vg_list"  readonly/>
                <table border="1" style="width:100%;"  cellpadding=3 cellspacing=1>
                    <tr>
                        <th>周转筐数量</th>
                        <td><input type="text" id="frame_count" class="fm_frame_count" value="0" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <th>周转筐扫描</th>
                        <td>
                            <input type="hidden" id="frame_vg_list" class="fm_frame_vg_list" />

                            <input style="font-size: 1.2em; width:90%;" id="input_vg_frame" name="input_vg_frame" />

                            <script>
                                $("input[name='input_vg_frame']").keyup(function(){
                                    var tmptxt=$(this).val();
                                    var container_id_length = tmptxt.length;

                                    if(container_id_length < 6){
                                        $(this).val('');
                                    }




                                });
                            </script>

                            <div id="vg_list" class="fm_vg_list"></div>
                        </td>
                    </tr>
                </table>
                <input class="submit" type="button" value="周转框已满" onclick="javascript:addOrderNum();">
                <input class="submit" type="button" value="关闭" onclick="javascript:hideSubmitFrame();">
            </div>

            <hr />

            <input type="hidden" id="current_do_product" value="0">
            <div style="display:block; margin-top: 1em;">
                <span style=" font-size: 1.2em;">订单商品信息</span>
                <span style="float:right;font-size: 1em; line-height: 1.8em;">共<span id="count_plan_quantity"></span>件,待完成<span id="count_quantity"></span>件</span>
            </div>
            <table id="productsHold" border="0" style="width:100%;"  cellpadding=2 cellspacing=3>
                <tr>
                    <th align="left">商品信息</th>
                    <th style="width:2em">商品数</th>
                    <th style="width:2em">待分拣</th>
                    <th style="width:3em">操作</th>
                </tr>
                <tbody id="productsInfo">
                <!-- Scanned Product List -->
                </tbody>

                <tbody id="productsCompleted">
                <!-- Complted Product List -->
                </tbody>
            </table>



            <input type="hidden" name="method" id="method" value="" />
            <div style="float:left">当前时间: <span id="currentTime"><?php echo date('Y-m-d H:i:s', time());?></span></div>
            <br />

            <?php if(in_array($_COOKIE['user_group_id'],$inventory_user_admin)){ ?>
                <input class="submit classSubmitSortingPendingCheck" type="button" value="提交分拣完成" onclick="javascript:showSubmitSorting();">
                <input class="submit classShowSortingInvComment" type="button" value="显示周转筐" onclick="javascript:showSubmitFrame()">

                <input class="submit classRemoveSortingData" type="button" value="删除分拣数据" onclick="javascript:delOrderProductToInv();">
                <input class="submit classSubmitSorting"  id = 'classSubmitSorting' type="button" value="直接提交已拣完" onclick="javascript:addOrderProductToInv_pre();">
<!--                --><?php // if ($_COOKIE['warehouse_id']  ==  15 || $_COOKIE['warehouse_id']  ==  16  || $_COOKIE['warehouse_id']  ==  17 ) { ?>
<!--        <input class="submit classSubmitSortingRelevant"  id = 'classSubmitSortingRelevant' type="button" value="提交合单" onclick="javascript:addConsolidatedRelevant();">-->
<!--    --><?php //} ?>
<!--    --><?php // if ($_COOKIE['warehouse_id']  ==  15 ||$_COOKIE['warehouse_id']  ==  16 ||$_COOKIE['warehouse_id']  ==  17) { ?>
<!--        <input class="submit classUpdateDoStatus"  id = 'classUpdateDoStatus' type="button" value="浦西没到货修改订单状态并记录" onclick="javascript:updateDoStatus();">-->
<!--    --><?php //} ?>

            <?php } else{ ?>
                <input class="submit classSubmitSortingPendingCheck" type="button" value="提交分拣完成" onclick="javascript:showSubmitSorting();">
                <input class="submit classShowSortingInvComment" type="button" value="显示周转筐" onclick="javascript:showSubmitFrame()">
            <?php } ?>



            <div style="float:left;width: 100%;">
                <div id="inv_do_vg" style="border-bottom:1px dashed black;" >
                    框数：<input type="text" id="frame_count" ><br>
                    备注:<textarea id="inv_comment_NOUSE"></textarea><br>
                    <input type="hidden" id="frame_vg_list">
                    蔬菜框号：<input style="font-size: 1.4em;" id="input_vg_frame" name="input_vg_frame">
                    <div id="vg_list_nouse" ></div>
                </div>
                <div id="inv_do_meat" style="border-bottom:1px dashed black;">
                    肉框数：<input type="text" id="frame_meat_count" ><br>
                    <input type="hidden" id="frame_meat_list">
                    肉框号：<input style="font-size: 1.4em;" id="input_meat_frame" name="input_meat_frame">
                    <div id="meat_list"></div>
                </div>
                <div id="inv_do_mi" style="border-bottom:1px dashed black;">
                    泡沫箱数：<input type="text" id="foam_count" ><br>
                    奶框数：<input type="text" id="frame_mi_count" ><br>
                    保温箱数：<input type="text" id="incubator_mi_count" ><br>
                    <input type="hidden" id="frame_mi_list">
                    奶框号：<input style="font-size: 1.4em;" id="input_mi_frame" name="input_mi_frame">
                    <div id="mi_list"></div>
                </div>

                <div id="inv_do_ice" style="border-bottom:1px dashed black;">
                    保温箱数：<input type="text" id="incubator_count" ><br>
                    冷冻框：<input type="text" id="frame_ice_count" ><br>

                    冷冻泡沫箱：<input type="text" id="foam_ice_count" ><br>
                    纸箱(无框号)：<input type="text" id="box_count" ><br>
                    <input type="hidden" id="frame_ice_list">
                    冷冻框号：<input style="font-size: 1.4em;" id="input_ice_frame" name="input_ice_frame">
                    <div id="ice_list"></div>
                </div>
            </div>

            <script type="text/javascript">

                $("input[name='input_vg_frame']").keyup(function(){
                    var tmptxt=$(this).val();
                    //$(this).val(tmptxt.replace(/\D|^0/g,''));
                    if(tmptxt.length >= 6){
                        var frameContainerNumber =  tmptxt.substr(0,6);
                        if(!/^[1-9]\d{0,5}$/.test(frameContainerNumber.trim())){
                            alert("请输入数字格式周转筐号");
                            $(this).val("");
                            return false;
                        }

                        handleFrameList('vg');
                        $(this).val("");
                    }
                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D|^0/g,''));
                });

                $("input[name='input_meat_frame']").keyup(function(){
                    var tmptxt=$(this).val();
                    //$(this).val(tmptxt.replace(/\D|^0/g,''));
                    if(tmptxt.length == 8){

                        handleFrameList('meat');
                        $(this).val("");
                    }
                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D|^0/g,''));
                });

                $("input[name='input_mi_frame']").keyup(function(){
                    var tmptxt=$(this).val();
                    //$(this).val(tmptxt.replace(/\D|^0/g,''));
                    if(tmptxt.length == 8){
                        handleFrameList('mi');
                        $(this).val("");
                    }
                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D|^0/g,''));
                });

                $("input[name='input_ice_frame']").keyup(function(){
                    var tmptxt=$(this).val();
                    //$(this).val(tmptxt.replace(/\D|^0/g,''));
                    if(tmptxt.length == 8){
                        handleFrameList('ice');
                        $(this).val("");
                    }
                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D|^0/g,''));
                });


                $("input[name='input_merge_frame']").keyup(function(){
                    var tmptxt=$(this).val();
                    //$(this).val(tmptxt.replace(/\D|^0/g,''));
                    if(tmptxt.length == 6){
                        handleMergeFrameList($(this).val());
                        $(this).val("");
                    }
                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D|^0/g,''));
                });

            </script>














            <!--<input class="submit style_yellow" type="button" value="获取商品信息" onclick="javascript:getProductName();">-->
            <!-- <input class="submit style_gray" type="button" value="取消" onclick="javascript:cancel();"> -->
        </div>
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

<div id="print" style="display: none">
    <div id='invMovesPrint' align="center" style="margin:0.5em auto;">
        <!-- Insert Move List -->


        <div class="noprint"><input class="submit_s style_gray" type="button" value="返回主页" onclick="javascript:backhome();"></div>

        <div id="invMovesPrintCaption" style="padding: 0 5px;">类型:<span id="prtInvMoveType"></span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;门店:<span id="prtInvMoveTitle"></span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;添加时间:<span id="prtInvMoveTime"></span></div>
        <div style=" padding: 10px 5px;">
            <table id="invMovesPrintHold" border="0" style="width:100%;"  cellpadding=0 cellspacing=0>
                <tr>
                    <th align="left" style="width:4em">商品ID</th>
                    <th align="left" style="">商品名称</th>
                    <th style="width:4em">价格</th>
                    <th style="width:3em">数量</th>
                    <th style="width:3em">备注</th>
                </tr>
                <tbody id="invMovesPrintInfo">
                <!-- Scanned Product List -->
                </tbody>
            </table>
        </div>
        <div style="padding: 0 5px; display: block; float: none; clear: both">
            <div style="float: right">
                合计数量: <span id="invMovesPrintQtyTotal">0</span>(件)&nbsp;&nbsp;&nbsp;
                合计金额: <span id="invMovesPrintAmountTotal">0</span>(元)
            </div>
        </div>
    </div>
</div>

<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="player3" src="view/sound/ding.wav">
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


<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="playerSubmit" src="view/sound/ding.wav">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>
<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="playerAlert" src="view/sound/redalert.wav">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>
<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="playerMessage" src="view/sound/message.wav">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>

<div id="overlay">

</div>
<script>
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



    function reloadPage(){
        javascript:location.reload();
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

    var inventory_user = 0;
    if(is_admin == 0){
        inventory_user = '<?php echo $_COOKIE['inventory_user'];?>';
    }

    $(document).ready(function () {
        startTime();
        var warehouse_id = $("#warehouse_id").text();
        var html = '<td colspan="6">正在载入...</td>';
        $('#ordersList').html(html);
        var warehouse_id = $("#warehouse_id").text();
        getAutoOrders();
    });



    function getAutoOrders(){

        var warehouse_id = $("#warehouse_id").text();
        var user_repack = '<?php echo $_COOKIE['user_repack'];?>';
        var to_warehouse_id = '<?php echo $_COOKIE['to_warehouse_id'];?>';

// alert(warehouse_id);
        //Get RegMethod
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=getAutoOrders',
            data: {
                method: 'getAutoOrders',
                data:{
                    warehouse_id: warehouse_id,
                    user_repack:user_repack,
                    to_warehouse_id:to_warehouse_id,
                    warehouse_repack : '<?php echo $_COOKIE['warehouse_repack'];?>',
                }
            },
            success : function (response , status , xhr) {
                //console.log(response);

                if (response) {
                    var html = '';
                    var order_been_over = '';
                    var jsonData = $.parseJSON(response);
                    $.each(jsonData, function(index, value){
                        if (parseInt(value.order_id)>0) {


                        html += '<tr class="barcodeHolder"  id="bd'+ value.order_id +'">';
                        html += '<td '+order_been_over+' class="prodlist" id="td'+value.order_id+'" >';
                        // html += '<span style="font-size: 1.2rem"><span>' + value.inv_class_sort + '</span> ';
                        html += '<span name="productBarcode" class="sortingProductBarcode" style="display:;" >' + value.order_id + '</span>';
                        // html += '<span class="sortingProductSku" style="display:none;" >' + value.sku + '</span>';
                        // html += '<span class="sortingProductQty" style="display:none;" >' + value.quantity + '</span>';
                        // html += '<span class="sortingProductRepack" style="display:none;" id="sortingProductRepack'+value.order_id +'"  >' + value.repack + '</span>';
                        // html += '[<span name="productId"" id="pid'+value.order_id+'">' + value.order_id + '</span>]';
                        html += '</td><td>';
                        html += '<button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderdistr(' + value.order_id +');">领单</button>';
                        html += '</td></tr>';
                        } else {
                            alert('无合适分拣单');
                            return true;
                        }
                    });
                    $("#ordersList").html(html);
                }
            }
        });
    }
    /*zx
    * 领单操作*/
    function orderdistr(order_id){
        var inventory_name = '<?php echo $_COOKIE['inventory_user'];?>';
        var deliver_order_status = $('#orderStatus').val();
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var warehouse_repack = '<?php echo $_COOKIE['warehouse_repack'];?>';
        var user_repack = '<?php echo $_COOKIE['user_repack'];?>';
        showOverlay();
        $.ajax({
            type : 'POST',
            url:'invapi.php',
            data : {
                method : 'auto_order_distr',
                date : '<?php echo $date_array[2]['date']; ?>',
                order_id : order_id,
                product_id:5004,
                inventory_name : inventory_name,
                warehouse_id : warehouse_id,
                user_repack : user_repack,
                warehouse_repack : warehouse_repack,

            },

            success : function (response , status , xhr){

                var jsonData = $.parseJSON(response);

                if(jsonData == 1){
                    alert("领单成功");
                    window.location = '<?php echo $file_name;?>.php?auth=xsj2015inv&ver=db';
                } else if(jsonData == 0){
                    alert('散整件订单分配人员错误,请刷新页面,注意分配人员以及订单');
                    location.reload();
                }  else if(jsonData == 3){
                    alert('你的单子未分拣完，请分拣完后再领单');
                    window.location = '<?php echo $file_name;?>.php?auth=xsj2015inv&ver=db';
                }  else if(jsonData == 4){
                    alert('此订单已分配，请刷新页面后重试');
                    location.reload();
                } else if(jsonData == 2){
                    alert('此订单不能分配，请刷新页面后重试');
                    location.reload();
                }
            },complete : function(){
                hideOverlay();
            }

        });
    }
    function playOverdueAlert(){
        //$('#player').attr('src',sound);
        $('.simpleplayer-play-control').click();
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
        $(barCodeId+' td').css('backgroundColor',color);
    }


    function backhome(){
        $('#content').show();
        $('#print').hide();
    }

    function cancel(){
        if(confirm('确认取消此次操作，所有页面数据将不被保存！')){
            location=window.location.href;
        }

        return;
    }

    function checkStation(){
        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }
        return true;
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
                    window.location = 'inventory_login.php?return=<?php echo $file_name;?>.php';
                }
            });
        }
    }

    function check_in_array(stringToSearch, arrayToSearch) {
        for (s = 0; s < arrayToSearch.length; s++) {
            thisEntry = arrayToSearch[s].toString();
            if (thisEntry == stringToSearch) {
                return true;
            }
        }
        return false;
    }
</script>
</body>
</html>