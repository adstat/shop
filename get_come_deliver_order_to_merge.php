<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26
 * Time: 15:31
 */

if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';
$inventory_user_admin = array('1','22','29');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php&ver=db");
    //确保重定向后，后续代码不会被执行
    exit;
}

//if(!in_array($_COOKIE['inventory_user'],$inventory_user_admin)){
//    exit("此功能仅限指定库管操作, 请返回");
//}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪出库回库管理</title>
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

        .invopt{
            background-color:#DF0000;
            width:4em;
            height:1.5em;
            line-height: 1em;
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

        #input_order_id{
            height: 1.5em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_order_id2{
            height: 1.5em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
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
        #product2{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }
        #invcomment{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }

        #product3{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }
        .addprod{
            cursor: pointer;
            color: #fff;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .qty{
            width:2em;
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
        #productsInfo3{
            border: 0.1em solid #888888;
        }
        #productsInfo4{
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


        #productsHold th{
            padding: 0.3em;
            background-color:#8FBBAE;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold2 th{
            background-color:#8FBBAE;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHold3 th{
            background-color:#8FBBAE;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHold4 th{
            background-color:#8FBBAE;
            color: #000;
            height: 2.5em;

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

        #invMovesHold th{
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

        #invMovesPrintHold th{
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

        .input_default{
            height: 1.8rem;
            font-size:1rem;
            margin: 0.1rem 0;

            background-color: #e3e3e3;
            border-radius: 0.2rem;
            box-shadow: 0.1rem rgba(0, 0, 0, 0.2);
            padding-left: 0.2rem;
        }

        #orderMissing div{
            margin: 3px;
        }
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
        .page_size{
            background-color:#229922;
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


        .w6rem{ width: 6rem; }
        .w4rem{ width: 4rem; }
        .w2rem{ width: 2rem; }

        .f0_7rem{ font-size: 0.7rem; }
        .f0_8rem{ font-size: 0.8rem; }
        .f0_9rem{ font-size: 0.9rem; }
        .f1_0rem{ font-size: 1.0rem; }
        .f1_1rem{ font-size: 1.1rem; }
        .f1_2rem{ font-size: 1.2rem; }
    </style>

    <style media="print">
        .noprint{display:none;}
    </style>
    <script>
        var global = {};
        global.order_information = {};
        global.deliver_order_id = 0;
        global.total_container = 0;
        global.local_show_container_id = 0;
        global.condition_type = 0;
        global.old_order_id = {};
        global.product_quantity = {};
    </script>

</head>
<?php

//if(isset($_GET['date'])){
//    $date_array = array();
//
//    $date_array[0]['date'] = date("m-d",  strtotime($_GET['date']));
//    $date_array[1]['date'] = date("m-d",strtotime($_GET['date']));
//    $date_array[2]['date'] = date("Y-m-d",strtotime($_GET['date']));
//    $date_array[2]['shorthate'] = date("m-d",strtotime($_GET['date']));
//}
//else{
//    $date_array = array();
//
//    $date_array[0]['date'] = date("m-d",time());
//    $date_array[1]['date'] = date("m-d",time() + 24*3600);
//    $date_array[2]['date'] = date("Y-m-d",time() + 12*3600);
//    $date_array[2]['shorthate'] = date("m-d",time() + 12*3600);
//
//}


?>
<div id="make_item_show"><button class="invopt" id="return_index" style="width:4em;" onclick="window.location.href='i.php?auth=xsj2015inv&ver=db'">返回</button></div>
<div align="right"><?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <span onclick="javascript:logout_inventory_user();">退出</span></div>
<div id="barcodescanner2" >
    <form method="post" onsubmit="handleProductList2(); return false;">

        <input name="product2" id="product2" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描周转筐" style="ime-mode:disabled; font-size: 1.2rem"/>
        <input class="addprod style_green" type="submit" value ="添加周转框编号" style="font-size: 1em; padding: 0.2em">

    </form>
    <form method="post" onsubmit="handleProductList3(); return false;">

        <input name="product2" id="product3" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描商品ID" style="ime-mode:disabled; font-size: 1.2rem"/>
        <input class="addprod style_green" type="submit" value ="添加商品ID" style="font-size: 1em; padding: 0.2em">

    </form>
</div>
<hr />
<table id="productsHold2" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
    <tr>
        <th style="background-color:#d0e9c6;">DO单号</th>
        <th id="show_container" style="display:none;background-color:#d0e9c6;">周转筐</th>
        <th style="background-color:#d0e9c6;" >货位号</th>
        <th id="show_product" style="display:none;background-color:#d0e9c6;" >商品ID</th>
        <th style="background-color:#d0e9c6;" >待投篮</th>
        <!--        <th align="left">实际操作数量</th>-->
        <th id="show_make_change" style="display:none;background-color:#d0e9c6;" >操作</th>


    </tr>
    <tbody id="productsInfo2">
    <!-- Scanned Product List -->
    </tbody>

</table>
<hr />
<?php if(in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
<button id="get_merge_info" class="invopt" style="display:;width:8em;height:3em;" onclick="javascript:get_order_information_to_merge(0,40,0);">待合单信息</button>
<button id="get_merge_info1" class="invopt" style="display:;width:8em;height:3em;" onclick="javascript:get_order_information_to_merge(0,40,1);">待配送信息</button>
<div id="get_information" style="display: none">
    <br />
    <span style="width: 3em">配送日期:</span>
    &nbsp;<select id="date_change" style="font-size: 15px;width:8em;height:1.5em;border:1px solid" onchange="javascript:get_order_information_to_merge(0,40);">
        <option value="<?php echo date("Y-m-d",strtotime("-12 Days"));?>"><?php echo date("Y-m-d",strtotime("-12 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-11 Days"));?>"><?php echo date("Y-m-d",strtotime("-11 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-10 Days"));?>"><?php echo date("Y-m-d",strtotime("-10 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-9 Days"));?>"><?php echo date("Y-m-d",strtotime("-9 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-8 Days"));?>"><?php echo date("Y-m-d",strtotime("-8 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-7 Days"));?>"><?php echo date("Y-m-d",strtotime("-7 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-6 Days"));?>"><?php echo date("Y-m-d",strtotime("-6 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-5 Days"));?>"><?php echo date("Y-m-d",strtotime("-5 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-4 Days"));?>"><?php echo date("Y-m-d",strtotime("-4 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-3 Days"));?>"><?php echo date("Y-m-d",strtotime("-3 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-2 Days"));?>"><?php echo date("Y-m-d",strtotime("-2 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("-1 Days"));?>"><?php echo date("Y-m-d",strtotime("-1 Days"));?></option>
        <option value="<?php echo date("Y-m-d",time());?>" ><?php echo date("Y-m-d",time());?></option>
        <option value="<?php echo date("Y-m-d",strtotime("+1 Days"));?>" selected><?php echo date("Y-m-d",strtotime("+1 Days"));?></option>
        <option value="<?php echo date("Y-m-d",strtotime("+2 Days"));?>"><?php echo date("Y-m-d",strtotime("+2 Days"));?></option>

    </select>
    <span id="show_order_status" style="display:none;">
    &nbsp;&nbsp;<span style="width: 3em">订单状态:</span>
    &nbsp;<select id="get_order_status" style="font-size: 15px;width:8em;height:1.5em;border:1px solid" onchange="javascript:get_order_information_to_merge(0,40,1);">
        <option value="1">全部</option>
        <option value="2">已确认</option>
        <option value="3">已取消</option>
        <option value="5" selected>分拣中</option>
        <option value="6">已捡完</option>
<!--        <option value="8">待审核</option>-->
    </select>
    </span>
    <br />
    <br />
        <input class="addprod style_green" id="hide_order_history" type="button" onclick="javascript:hide_order_history();" value="隐藏" style="width:10em;" />
    <table border="0" style="width:100%;" cellpadding="2" cellspacing="3" id="productsHold">
        <thead>
        <tr>
            <th style="background-color: #d0e9c6;">待配送订单</th>
            <th style="background-color: #d0e9c6;">DO单信息<br>分拣仓：DO单号》状态:整件[计划量：投篮量]；周转筐[计划量：投篮量]</th>
            <th style="background-color: #d0e9c6;">操作</th>
        </tr>
        </thead>
        <tbody id="productsInfo">

        </tbody>
    </table>
    <table width="100%" align="right">
        <tr><td><div id="barcon" name="barcon"></div></td></tr>
    </table>
</div>
<div id="get_deliver_information" style="display: none">
    <b><span id="deliver_order_title" style="text-align: center;"></span></b><br />
    <button class="invopt" style="width:4em;" onclick="javascript:catOrderInventoryView(3)">整件</button>《》<button class="invopt" style="width:4em;" onclick="javascript:catOrderInventoryView(4)">周转筐</button><span id="container_lists"></span>
    <table border="0" style="width:100%;display: none;" cellpadding="2" cellspacing="3" id="productsHold4">
        <caption><button class="invopt" style="width:8em;" onclick="javascript:catOrderInventoryInfo(3)">周转框商品总计</button></caption>
        <thead>
        <tr>
            <th style="background-color: #d0e9c6;">周转框ID</th>
            <th style="background-color: #d0e9c6;">分拣</th>
            <th style="background-color: #d0e9c6;">投篮</th>
            <th style="background-color: #d0e9c6;">操作</th>
        </tr>
        </thead>
        <tbody id="productsInfo4">

        </tbody>
    </table>
    <hr />
    <table border="0" style="width:100%;display: none;" cellpadding="2" cellspacing="3" id="productsHold3">
        <caption><B><span id="local_container" style="color: red;"></span></B></caption>
        <thead>
        <tr>
            <th style="background-color: #d0e9c6;">商品ID</th>
            <th style="background-color: #d0e9c6;">名称</th>
            <th style="background-color: #d0e9c6;">订单量</th>
            <th style="background-color: #d0e9c6;">分拣量</th>
            <th style="background-color: #d0e9c6;">投篮量</th>
            <th style="background-color: #d0e9c6;">操作</th>
        </tr>
        </thead>
        <tbody id="productsInfo3">

        </tbody>
    </table>
    <?php if(in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
    <div id="cancle_order_return" style="float: right;"></div>
    <?php } ?>
</div>
<?php } ?>
<script>
    function hide_order_history() {
        var hide_order_history = $("#hide_order_history").val();
        if (hide_order_history == "隐藏") {
            $("#get_information").hide();
            $("#hide_order_history").val("显示");
        } else if (hide_order_history == "显示") {
            $("#get_information").show();
            $("#hide_order_history").val("隐藏");
        }
    }
</script>


<div id="overlay">

</div>




<script>
    /* 显示遮罩层 */
    function showOverlay() {
        $("#overlay").height(pageHeight());
        $("#overlay").width(pageWidth());

        // fadeTo第一个参数为速度，第二个为透明度
        // 多重方式控制透明度，保证兼容性，但也带来修改麻烦的问题
        $("#overlay").fadeTo(1, 0.5);
    }

    /* 隐藏覆盖层 */
    function hideOverlay() {
        $("#overlay").fadeOut(1);
    }


    /* 当前页面高度 */
    function pageHeight() {
        return document.body.scrollHeight;
    }

    /* 当前页面宽度 */
    function pageWidth() {
        return document.body.scrollWidth;
    }
    function check_in_right_time(){
        var start_time = '<?php echo (strtotime(date("Y-m-d"),time())+$start_time);?>';
        var end_time = '<?php echo ((strtotime(date("Y-m-d"),time())+$end_time));?>';
        var now_time = '<?php echo time();?>';
        // console.log(start_time);
        // console.log(end_time);
        // console.log(now_time);
        if (start_time <= now_time && now_time <= end_time) {
            alert("系统正在备份，为防止数据丢失，请于2：20之后再操作");
            return true;
        } else {
            return false;
        }
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
                    window.location = 'inventory_login.php?return=i.php';
                }
            });
        }
    }
    function handleProductList2(){
        if (check_in_right_time()) {
            return true;
        }
        $("#show_container").show();
        $("#show_product").hide();
        $("#show_make_change").show();
        var s_count_id = $('#product2').val();
        var count_id =  s_count_id.substr(0, 6);
        if (count_id<100000) {
            alert("条码长度错误");
            return true;
        }
        showOverlay();

        var warehouse_id =  '<?php echo $_COOKIE['warehouse_id'];?>' ;
        // var date_added = $("#date_added").val();
        if(count_id == '' ){
            alert('请添加扫描框');
            hideOverlay();
            return false ;
        }
        $("#button4_id"+count_id).attr("disabled", false);
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=getRelevantInfoByInput',
            data: {
                method: 'getRelevantInfoByInput',
                data: {
                    warehouse_id:warehouse_id,
                    product_id: count_id,
                    type:2,
                }
            },
            success: function (response) {
                if(response) {
                    var html = '';
                    var jsonDatas = $.parseJSON(response);
                    var jsonData = jsonDatas.array1;
                    var jsonData2 = jsonDatas.array2;
                    // console.log(response);
                    if (jsonDatas.return_code == 'ERROR'){
                        alert(jsonDatas.return_msg);
                        $('#productsInfo2').html('');
                        return true;
                    }
                    $.each(jsonData, function (i, v) {
                        var shoot = 0 ;
                        html += "<tr style='background:#d0e9c6; height: 30px;' id='check" + v.order_id + "'>";
                        html += "<th>" + v.deliver_order_id + '<br> '+ '['+v.order_id  + ']<br />'+v.deliver_date+ "</th>";
                        html += "<th>" + v.container_id +"</th>";
                        var inv_comment = v.inv_comment.split(',')[0];
                        var inv_comments = inv_comment.split('@');
                        if (parseInt(inv_comments[0]) == 0) {
                            html += '<th>';
                            html +='<input class="" style="width: 2.5em;display:;" id="inv_comment'+ v.container_id +'" type="text" value="'+inv_comments[1]+'" ;" >';
                            html += '</th>';
                        } else {
                            html += "<th>" + inv_comments[1] + "</th>";
                        }

                        // html += '<th  >' + + '</th>';
                        html += "<th>";
                        html += '<span id="num2_'+v.container_id+'">' + v.sorting_quantity  + '</span><br />[';
                        html += "<span id='num1_"+v.container_id +"'>" + v.plan_quantity + "</span>]<br />";
                        if(v.quantity > 0 ){
                            html +='<input class="qtyopt" type="button" id="button_id'+ v.container_id +'"  value="+" onclick="javascript:qtyaddc(\''+ v.container_id +'\');" ><br />';
                            html +='<input class="qtyopt"  id="button2_id'+ v.container_id +'" type="button" value="-" onclick="javascript:qtyminusc(\''+ v.container_id +'\');" >';
                        }
                        html += '</th>';
                        html += '<th>';

                        if(v.quantity >0 ){

                            if ((v.warehouse_id == v.do_warehouse_id && parseInt(v.is_repack) == 1) || (v.warehouse_id != v.do_warehouse_id)) {
                                html +='<br /><input class="invopt"  id="button3_id'+ v.container_id +'" type="button" value="修正货位号" onclick="javascript:updateInvComment('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                            }
                            html +='<br /><br /><input class="invopt"  id="button4_id'+ v.container_id +'" type="button" value="确认提交" onclick="javascript:confirmDoRelevantC('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                            html +='<br /><input class="invopt"  id="button5_id'+ v.container_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevantC('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';


                        }else{
                            html +='<input class="invopt"  id="button5_id'+ v.container_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevantC('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                            if ((parseInt(v.warehouse_id) == parseInt(v.do_warehouse_id) && parseInt(v.is_repack) == 1) || (parseInt(v.warehouse_id) != parseInt(v.do_warehouse_id))) {
                                html +='<br /><input class="invopt"  id="button3_id'+ v.container_id +'" type="button" value="修正货位号" onclick="javascript:updateInvComment('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                            }

                        }
                        html += '</th>';


                        html +="</tr>";


                    });
                    $.each(jsonData2, function (i, v) {
                        if (parseInt(v.do_warehouse_id)!=parseInt(warehouse_id)) {

                            html += "<tr style='background:#d0e9c6; height: 30px;'>";
                            html += "<th>该订单其他周转筐</th>";
                            var str = v.frame_vg_list;
                            var sear = new RegExp(',');

                            if (sear.test(str)) {
                                var vg_array = v.frame_vg_list.split(",");
                                html += "<th>";
                                $.each(vg_array, function (i, v1) {
                                    if (parseInt(v1) != parseInt(count_id)) {
                                        html += v1;
                                        html += "<br/>";
                                    }

                                });
                                html += "</th>";
                            } else {
                                html += "<th>" + v.frame_vg_list + "</th>";
                            }

                            html += "</tr>";

                        }

                    });
                    $('#productsInfo2').html(html);

                }
            },
            complete: function () {
                hideOverlay();

            }

        });
    }

    function  showInvComment() {
        $("#inv_comment").show();
    }


    function qtyadd(id){
        var num2 =  $("#num2_"+id).text();
        var num1 =  $("#num1_"+id).text();


        if(parseInt(num1)<parseInt(num2)+1){
            alert('不能超过计划数量');
        }else{
            var num= parseInt(num2)+1;
            $("#num2_"+id).text(num);

        }
    }

    function qtyaddc(id){
        var num2 =  $("#num2_"+id).text();
        var num1 =  $("#num1_"+id).text();


        if(parseInt(num1)<parseInt(num2)+1){
            alert('不能超过计划数量');
        }else{
            var num= parseInt(num2)+1;
            $("#num2_"+id).text(num);

        }
    }
    function qtyminusc(id){
        var num2 =  $("#num2_"+id).text();
        var num1 =  $("#num1_"+id).text();

        if( parseInt(num2)  == 0 ) {
            alert('已经为零不能再调') ;
        }else {
            $("#num2_"+id).text(parseInt(num2)-1);
        }


    }

    function qtyminus(id){
        var num2 =  $("#num2_"+id).text();
        var num1 =  $("#num1_"+id).text();

         if( parseInt(num2)  == 0 ) {
             alert('已经为零不能再调') ;
         }else {
             $("#num2_"+id).text(parseInt(num2)-1);
         }


    }


    function handleProductList3() {
        if (check_in_right_time()) {
            return true;
        }
        showOverlay();

        $("#show_container").hide();
        $("#show_product").show();
        $("#show_make_change").show();
        var warehouse_id =  '<?php echo $_COOKIE['warehouse_id'];?>' ;
        var product_id = $("#product3").val();

        if(product_id == '' ){
            alert('请添加商品ID');
            hideOverlay();
            return false ;
        }
        $("#button4_id"+product_id).attr("disabled", false);
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=getRelevantInfoByInput',
            data: {
                method: 'getRelevantInfoByInput',
                data: {
                    warehouse_id:warehouse_id,
                    product_id: product_id,
                    type:1,
                }
            },
            success: function (response) {
                if(response) {
                    var html = '';
                    var jsonDatas = $.parseJSON(response);
                    var jsonData = jsonDatas.array1;

                    // console.log(response);

                    if (jsonDatas) {
                        if (jsonDatas.return_code == 'ERROR'){
                            alert(jsonDatas.return_msg);
                            $('#productsInfo2').html('');
                            return true;
                        }

                        $.each(jsonData, function (i, v) {
                            var shoot = 0 ;
                            html += "<tr style='background:#d0e9c6; height: 30px;' id='check" + jsonData.order_id + "'>";
                            html += "<th>" + v.deliver_order_id + '<br> '+ '['+v.order_id  + ']<br />'+v.deliver_date+ "</th>";
                            // html += "<th>" + v.container_id +"</th>";
                            var inv_comment = v.inv_comment.split(',')[0];
                            var inv_comments = inv_comment.split('@');
                            if (parseInt(inv_comments[0]) == 0) {
                                html +='<th><input class="" style="width: 2.5em;display: ;" id="inv_comment'+ v.order_id +'" type="text" value="'+inv_comments[1]+'" ;" ></th>';
                            } else {
                                html += "<th>" + inv_comments[1] + "</th>";
                            }
                            var id =v.product_id + v.deliver_order_id ;
                            html += "<th style='font-size:10px;'>" + v.product_id +'<br /> '+ '['+v.name + ']'+  "</th>";
                            html += "<th>";
                            html += "<span id='num2_"+id +"'>" + v.sorting_quantity + "</span><br />[";
                            html += "<span id='num1_"+id +"'>" + v.plan_quantity + "</span>]<br />";
                            if(v.quantity > 0 ){
                                html +='<input class="qtyopt" type="button" id="button_id'+ v.product_id +'"  value="+" onclick="javascript:qtyadd(\''+ id +'\');" ><br />';
                                html +='<input class="qtyopt"  id="button2_id'+ v.product_id +'" type="button" value="-" onclick="javascript:qtyminus(\''+ id +'\');" >';
                            }
                            html += "</th>";
                            html  += '<th>';
                            if(v.quantity > 0 ){
                                if ((v.warehouse_id == v.do_warehouse_id && parseInt(v.is_repack) == 1) || (v.warehouse_id != v.do_warehouse_id)) {
                                    html +='<br /><input class="invopt"  id="button3_id'+ v.order_id +'" type="button" value="修正货位号" onclick="javascript:updateInvComment('+ v.order_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                                }
                                html +='<br /><br /><input class=" invopt"  id="button4_id'+ v.product_id +'" type="button" value="确认提交" onclick="javascript:confirmDoRelevant('+ id +', '+v.deliver_order_id+' , '+v.order_id+','+v.product_id+');" >';
                                html +='<input class="invopt"  id="button5_id'+ v.product_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevant('+ id +', '+v.deliver_order_id+' , '+v.order_id+','+v.product_id+');" >';
                            }else{
                                html +='<input class="invopt"  id="button5_id'+ v.product_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevant('+ id +', '+v.deliver_order_id+' , '+v.order_id+','+v.product_id+');" >';
                                if ((v.warehouse_id == v.do_warehouse_id && parseInt(v.is_repack) == 1) || (v.warehouse_id != v.do_warehouse_id)) {
                                    html +='<br /><br /><input class="invopt"  id="button3_id'+ v.order_id +'" type="button" value="修正货位号" onclick="javascript:updateInvComment('+ v.order_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                                }
                            }

                            html += '</th>';
                            html += "</tr>";


                        });
                        $('#productsInfo2').html(html);
                    }
                }
            },
            complete: function () {
                hideOverlay();

            }
        });
    }

    /*
    * 提交或取消缺货
    * @order_id      订单号
    * @container_id  周转筐号
    * @product_id    商品id
    * @type          1提交缺货；2取消缺货
    *
    * */
    function submitReturnProduct(order_id,type,container_id,product_id) {
        if (check_in_right_time()) {
            return true;
        }
        var quantity = 0;
        if (type == 1 && product_id > 0) {
            quantity = prompt("请输入商品数量");
            if (quantity == null) {
                return true;
            }
            var plan_quantity = global.product_quantity[product_id];
            // alert(quantity);
            if (quantity > 0 && quantity <= plan_quantity) {
            } else {
                alert('数量必须大于零并且不大于分拣数量');
                return true;
            }
        }
        var title = type == 1 ? '提交缺货' : '取消缺货' ;
        // alert(quantity);
        if (confirm("确认"+title)) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php?method=catOrderProductInfo',
                data: {
                    method: 'submitReturnProduct',
                    data: {
                        order_id: order_id,
                        type: type,
                        product_id : product_id,
                        container_id : container_id,
                        quantity : quantity,
                        added_by : '<?php echo $_COOKIE['inventory_user'];?>',
                    }
                },
                success: function (response) {
                }
            });
        }

    }

    /*
    * 分拣单明细查询
    * @order_id  查询
    * @type     1 明细;2返回;3整件;4周转框
    *
    * */
    function catOrderInventoryView(type,deliver_order_id) {
        switch(type){
            case 1:
                $("#cancle_order_return").html('<button class="invopt" style="width:8em;display: inline" onclick="javascript:submitReturnProduct(\''+deliver_order_id+'\',2,0,0)">取消缺货操作</button>');
                $("#make_item_show").html('<button class="invopt" id="return_index" style="width:4em;" onclick="javascript:catOrderInventoryView(2,'+deliver_order_id+')">返回</button>');
                $("#deliver_order_title").html('分拣单号：'+deliver_order_id);
                $("#barcodescanner2").hide();
                $("#productsHold2").hide();
                $("#get_merge_info").hide();
                $("#get_merge_info1").hide();
                $("#get_information").hide();
                $("#get_deliver_information").show();
                $("#local_container").html("");
                global.deliver_order_id = parseInt(deliver_order_id);
                break;
            case 2:
                $("#make_item_show").html('<button class="invopt" id="return_index" style="width:4em;" onclick="window.location.href=\''+'i.php?auth=xsj2015inv&ver=db'+'\'">返回</button>');
                $("#barcodescanner2").show();
                $("#productsHold2").show();
                $("#get_merge_info").show();
                $("#get_merge_info1").show();
                $("#get_information").show();
                $("#get_deliver_information").hide();
                $("#productsHold3").hide();
                $("#productsHold4").hide();
                location.href = "#go_to_"+deliver_order_id;
                break;
            case 3:
                $("#productsHold3").show();
                $("#productsHold4").hide();
                $("#productsInfo3").html('');
                $("#productsInfo4").html('');
                $("#local_container").html("");
                catOrderInventoryInfo(1);
                break;
            case 4:
                $("#productsHold3").show();
                $("#productsHold4").show();
                $("#productsInfo3").html('');
                $("#productsInfo4").html('');
                catOrderInventoryInfo(2);
                break;
        }
    }
    /*
        * 商品明细查询
        * @type     1 整件;2周转筐;3所有散件
        *
        * */
    function catOrderInventoryInfo(type) {
        global.total_container = 0;
        var deliver_order_id = global.deliver_order_id;
        var order_id = parseInt(deliver_order_id);
        if (deliver_order_id > 0) {

            $.ajax({
                type: 'POST',
                url: 'invapi.php?method=catOrderProductInfo',
                data: {
                    method: 'catOrderProductInfo',
                    data: {
                        deliver_order_id: deliver_order_id,
                        product_type : type,
                    }
                },
                success: function (response) {
                    if (response) {
                        hideOverlay();

                        var html = '';
                        var jsonData = $.parseJSON(response);
                        if (jsonData.return_code == 'ERROR') {
                            alert(jsonData.return_msg);
                            return true;
                        }
                        switch (type) {
                            case 1:
                                $.each(jsonData.return_data, function (i, v) {
                                    global.product_quantity[v.product_id] = parseInt(v.sorting_quantity);
                                    html += "<tr><th>";
                                    html += v.product_id;
                                    html += "</th><th>";
                                    html += v.name;
                                    html += "</th><th>";
                                    html += v.plan_quantity;
                                    html += "</th><th>";
                                    html += v.sorting_quantity;
                                    html += "</th><th>";
                                    html += v.merge_quantity;
                                    html += "</th><th>";
                                    if (v.merge_quantity == 0) {
                                        html += '<button class="invopt" style="display: inline" onclick="javascript:submitReturnProduct('+order_id+',1,0,'+v.product_id+')">缺货</button>';
                                    }
                                    html += "</th></tr>";

                                });
                                $("#productsInfo3").html(html);
                                $("#local_container").html("");

                                break;
                            case 2:
                                var html2 = '';
                                var show_container = global.local_show_container_id;
                                $.each(jsonData.return_data, function (i, v) {
                                    if (show_container == 0) {
                                        show_container = v.container_id;
                                    }
                                    html += '<tr><th><button class="invopt" style="display: inline" onclick="javascript:show_container_tr('+v.container_id+')">';
                                    html += v.container_id;
                                    html += "</button></th><th>";
                                    html += v.sorting_container;
                                    html += "</th><th>";
                                    html += v.merge_container;
                                    html += "</th><th>";
                                    if (v.merge_container == 0) {
                                        html += '<button class="invopt" style="display: inline" onclick="javascript:submitReturnProduct('+order_id+',1,'+v.container_id+',0)">缺货</button>';
                                    }
                                    html += "</th></tr>";
                                    $.each(v.product_id, function (index, value) {
                                        global.product_quantity[value.product_id] = parseInt(value.sorting_quantity);
                                        if (v.container_id == show_container) {
                                            html2 += "<tr class='container_list show"+v.container_id+"' style='display:;'><th>";
                                        } else {
                                            html2 += "<tr class='container_list show"+v.container_id+"' style='display:none;'><th>";
                                        }
                                        html2 += value.product_id;
                                        html2 += "</th><th>";
                                        html2 += value.name;
                                        html2 += "</th><th>";
                                        html2 += value.plan_quantity;
                                        html2 += "</th><th>";
                                        html2 += value.sorting_quantity;
                                        html2 += "</th><th>";
                                        html2 += value.merge_quantity;
                                        html2 += "</th><th>";
                                        if (value.merge_quantity == 0) {
                                            html2 += '<button class="invopt" style="display: inline" onclick="javascript:submitReturnProduct('+order_id+',1,'+v.container_id+','+value.product_id+')">缺货</button>';
                                        }
                                        html2 += "</th></tr>";
                                    });
                                });

                                $("#productsInfo3").html(html2);
                                $("#productsInfo4").html(html);
                                $("#local_container").html("当前周转框："+show_container);
                                break;
                            case 3:
                                global.total_container = 1;
                                $.each(jsonData.return_data, function (i, v) {
                                    html += "<tr class='container_list'><th>";
                                    html += v.product_id;
                                    html += "</th><th>";
                                    html += v.name;
                                    html += "</th><th>";
                                    html += v.plan_quantity;
                                    html += "</th><th>";
                                    html += v.sorting_quantity;
                                    html += "</th><th>";
                                    html += v.merge_quantity;
                                    if (v.merge_quantity == 0) {
                                        html += '<button class="invopt" style="display: inline" onclick="javascript:submitReturnProduct('+order_id+',1,0,'+v.product_id+')">缺货</button>';
                                    }
                                    html += "</th></tr>";
                                });
                                $("#productsInfo3").html(html);
                                $("#local_container").html("全部周转框商品");
                                break;
                        }
                    }
                }
            });
        } else {
            alert('页面错误请刷新后重试');
            location.reload();
            return false;
        }
    }
    function show_container_tr(container_id) {
        if (global.total_container == 1) {
            catOrderInventoryInfo(2);
        }
        $(".container_list").each(function(){
            $(this).hide();
        });
        $(".show"+container_id).each(function(){
            $(this).show();
        });
        $("#local_container").html("当前周转框："+container_id);
    }
    function get_order_information_to_merge(start_row,psize,condition_type) {
         showOverlay();
        $("#get_information").show();
        var warehouse_id =  '<?php echo $_COOKIE['warehouse_id'];?>' ;
        warehouse_id = parseInt(warehouse_id);
        global.condition_type = condition_type >= 0 ? condition_type : global.condition_type;
        if (parseInt(global.condition_type) == 1) {
            $("#show_order_status").show();
            var order_status_id = $("#get_order_status").val();
        } else {
            $("#show_order_status").hide();
            var order_status_id = 0;
        }
        var deliver_date = $("#date_change").val();
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=get_order_information_to_merge',
            data: {
                method: 'get_order_information_to_merge',
                data: {
                    warehouse_id:warehouse_id,
                    psize : psize,
                    start_row : start_row,
                    deliver_date : deliver_date,
                    condition_type : global.condition_type,
                    order_status_id : order_status_id,
                }
            },
            success: function (response) {
                if(response) {
                    hideOverlay();

                    var html = '';
                    var tempStr = '';
                    var jsonData = $.parseJSON(response);
//                    console.log(response);

                    if (jsonData ) {
                        if (jsonData.return_code == 'ERROR') {
                            alert(jsonData.return_msg);
                        } else if (jsonData.return_code == 'SUCCESS') {
                            var num = 0;
                            var jsonData = jsonData.return_data;
                            $.each(jsonData.data, function (i, v) {
                                //console.log(value.plan_quantity);
                                num++

                                var ty_status_class = "style='border-radius: 0.3rem;background-color: yellow;border: 0.1em solid #8FBB6C;display: inline-block;'";
                                var tg_status_class = "style='border-radius: 0.3rem;background-color: #d0e9c6;border: 0.1em solid #8FBB6C;display: inline-block;'";
                                var t_status_class = "style='border-radius: 0.3rem;border: 0.1em solid #8FBB6C;display: inline-block;'";

                                html += '<tr class="span_order" >';
                                var inv_comment = v.inv_comment.split(',')[0];
                                var inv_comments = inv_comment.split('@');
                                html += '<th >' + v.order_id+'<br />货位:'+inv_comments[1]+'<br />'+v.name+ '</th><th>';

                                var order_id = 0;
                                var order_id2 = 0;
                                var deliver_order_num = 0;
                                var check_mergr_result = 1;
                                $.each(v.deliver_orders, function (index, value) {
                                    deliver_order_num++
                                    var do_warehouse_id = parseInt(value.do_warehouse_id);
                                    if (warehouse_id == do_warehouse_id && parseInt(value.is_repack) == 0) {
                                        html += '<br /><span id="go_to_'+value.deliver_order_id+'" ' + tg_status_class + '>' +'本仓'+':<button class="invopt" style="display: inline" onclick="javascript:catOrderInventoryView(1,' +value.deliver_order_id+');">'+value.deliver_order_id+'</button>》'+value.name+'</span><br />';
                                        order_id2 = value.deliver_order_id;
                                        global.order_information[parseInt(order_id2)] = value;
                                    } else {
                                        order_id = value.deliver_order_id;
                                        if ((parseInt(value.plan_quantity) != parseInt(value.sorting_quantity) || parseInt(value.plan_container_count) != parseInt(value.container_count)) && parseInt(v.order_status_id) != 6 && parseInt(v.order_status_id) != 3) {
                                            html += '<br /><span id="go_to_'+value.deliver_order_id+'" ' + ty_status_class + '>' +value.title+':<button class="invopt" style="display: inline" onclick="javascript:catOrderInventoryView(1,' +value.deliver_order_id+');">'+value.deliver_order_id+'</button>》';
                                        } else {
                                            html += '<br /><span id="go_to_'+value.deliver_order_id+'" ' + tg_status_class + '>' +value.title+':<button class="invopt" style="display: inline" onclick="javascript:catOrderInventoryView(1,' +value.deliver_order_id+');">'+value.deliver_order_id+'</button>》';
                                        }
                                        if (parseInt(value.order_status_id) == 12 || parseInt(value.order_status_id) != 6) {
                                            html += value.name;
                                        } else {
                                            html += "待投篮";
                                        }
                                        html += ':整件['+value.plan_quantity+'：'+value.sorting_quantity+']；周转筐['+value.plan_container_count+'：'+value.container_count+']</span><br />';
                                    }
                                    if (parseInt(value.order_status_id) != 6 && parseInt(value.order_status_id) != 12 ) {
                                        check_mergr_result = 0;
                                    }
                                    global.old_order_id[parseInt(value.deliver_order_id)] = parseInt(v.order_id);
                                    global.order_information[parseInt(order_id2)] = value;
                                });
                                if (deliver_order_num == 1 || order_id2 == 0) {
                                    order_id2 = order_id;
                                }
                                global.old_order_id[parseInt(order_id2)] = parseInt(v.order_id);

                                global.old_order_id[parseInt(order_id)] = parseInt(v.order_id);
                                html += '</th>';

                                if ((parseInt(v.order_status_id)== 5 || parseInt(v.order_status_id)== 8) && check_mergr_result == 1) {
                                    html += '<th><button id="inventoryIn_'+order_id2+'" class="invopt orderStartSortingButton" style="display: inline" onclick="javascript:addConsolidatedRelevant(' + order_id +','+order_id2+ ');">合单</button>';
                                } else if (parseInt(v.order_status_id) == 6) {
                                    html += '<th>已合单';
                                }  else if (parseInt(v.order_status_id) == 3) {
                                    html += '<th>已取消';
                                } else {
                                    html += '<th><span '+ty_status_class+'>不可合单</span>';
                                }
                                html += '</th>';
                                html += '</tr>';
                                html += '';
                            });
                            tempStr = "<button class=\"page_size\">每页"+(psize)+"条</button><button class=\"page_size\">本页"+(num)+"条</button><button class=\"page_size\">第"+(start_row+1)+"页,共"+(jsonData.pages)+"页,共"+(jsonData.count)+"条</button>";
                            tempStr += "<button class=\"page_size\" onClick=\"get_order_information_to_merge(0,"+psize+","+global.condition_type+")\">首页</button>";

                            if(start_row >= 1){
                                tempStr += "<button class=\"page_size\" onClick=\"get_order_information_to_merge("+(start_row-1)+","+psize+","+global.condition_type+")\">上一页</button>"
                            }
                            tempStr += "&nbsp;";
                            if (start_row <= jsonData.pages-2) {
                                tempStr += "<button class=\"page_size\" onClick=\"get_order_information_to_merge("+(start_row+1)+","+psize+","+global.condition_type+")\">下一页</button>";
                            }
                            tempStr += "<button class=\"page_size\" onClick=\"get_order_information_to_merge("+(jsonData.pages-1)+","+psize+","+global.condition_type+")\">尾页</button>";

                        }
                        $("#barcon").html(tempStr);
                        $('#productsInfo').html(html);
                    }

                }
//                hideOverlay();
            }
        });

    }

   function  confirmDoRelevant(id,deliver_order_id ,order_id,product_ids ) {
       if (check_in_right_time()) {
           return true;
       }
        // return false;
       $("#button4_id"+product_ids).attr("disabled", true);

       var product_id = $("#product3").val();
       var s_count_id = $('#product2').val();
       var count_id =  s_count_id.substr(0, 6);

       var user_id= '<?php echo $_COOKIE['inventory_user_id'];?>';
       var warehouse_id= '<?php echo $_COOKIE['warehouse_id'];?>';
       var quantity =  $("#num2_"+id).text();
       var str=location.href; //取得整个地址栏
       var index = str .lastIndexOf("\=");
       var   relevant_id  = str .substring(index + 1, str .length);

       if(quantity == 0 ){
           alert ('数量为零不能提交');
           return false ;
       }
       if(confirm("数量确认？")) {
           $(".qtyopt").hide();
           $.ajax({
               type: 'POST',
               url: 'invapi.php',
               data: {
                   method: 'confirmDoRelevant',
                   data: {
                       id: product_ids,
                       user_id: user_id,
                       deliver_order_id: deliver_order_id,
                       order_id: order_id,
                       quantity: quantity,
                       warehouse_id: warehouse_id,
                       relevant_id: relevant_id,

                   },
               },
               success: function (response) {
                   var jsonData = $.parseJSON(response);
                   if (jsonData == 1) {
                       alert('提交成功');

                       // $('#productsInfo2').html('');
                   }
                   if (jsonData == 2) {
                       alert('提交失败');

                   }
                   if (jsonData == 3) {
                       alert('此商品已提交');
                   }

                   if (jsonData == 4) {
                       alert('该订单已经提交不能做合单操作');
                   }
                   if (product_id > 0) {
                       handleProductList3();
                       $(".qtyopt").show();
                       $("#product3").val('');
                       $("#product2").val('');
                   }
                   if (count_id > 0) {
                       handleProductList2();
                       $(".qtyopt").show();
                       $("#product3").val('');
                       $("#product2").val('');
                   }
               }


           });
       }
   }

    function  updateDoRelevant (id,deliver_order_id ,order_id,product_ids){
        if (check_in_right_time()) {
            return true;
        }
        var product_id = $("#product3").val();
        var s_count_id = $('#product2').val();
        var count_id =  s_count_id.substr(0, 6);

        if(confirm('确认删除合单数据？')) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'updateDoRelevant',
                    data: {
                        id: product_ids,
                        deliver_order_id: deliver_order_id,
                        order_id: order_id,

                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if (jsonData == 1) {
                        alert('删除成功');

                    }
                    if (jsonData == 2) {
                        alert('提交失败');
                    }
                    if (jsonData == 3) {
                        alert('不能删除分拣数据');
                    }
                    if(product_id > 0 ){
                        handleProductList3();
                    }
                    if(count_id > 0 ){
                        handleProductList2();
                    }

                }


            });
        }
    }



    function  updateDoRelevantC (id,deliver_order_id ,order_id){
        if (check_in_right_time()) {
            return true;
        }
        var product_id = $("#product3").val();
        var s_count_id = $('#product2').val();
        var count_id =  s_count_id.substr(0, 6);

        if(confirm('确认删除合单数据？')) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'updateDoRelevantC',
                    data: {
                        id: id,
                        deliver_order_id: deliver_order_id,
                        order_id: order_id,

                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if (jsonData == 1) {
                        alert('删除成功');

                    }
                    if (jsonData == 2) {
                        alert('提交失败');
                    }
                    if (jsonData == 3) {
                        alert('不能删除分拣数据');
                    }
                    if(product_id > 0 ){
                        handleProductList3();
                    }
                    if(count_id > 0 ){
                        handleProductList2();
                    }
                }


            });
        }
    }


    function  confirmDoRelevantC(id,deliver_order_id ,order_id ) {
        if (check_in_right_time()) {
            return true;
        }
        $("#button4_id"+id).attr("disabled", true);

        var product_id = $("#product3").val();
        var s_count_id = $('#product2').val();
        var count_id =  s_count_id.substr(0, 6);

        var user_id= '<?php echo $_COOKIE['inventory_user_id'];?>';
        var warehouse_id= '<?php echo $_COOKIE['warehouse_id'];?>';
        var quantity =  $("#num2_"+id).text();
        var str=location.href; //取得整个地址栏
        var index = str .lastIndexOf("\=");
        var   relevant_id  = str .substring(index + 1, str .length);

        if(quantity == 0 ){
            alert ('数量为零不能提交');
            return false ;
        }
        if(confirm("数量确认？")) {
            $(".qtyopt").hide();
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'confirmDoRelevantC',
                    data: {
                        id: id,
                        user_id: user_id,
                        deliver_order_id: deliver_order_id,
                        order_id: order_id,
                        quantity: quantity,
                        warehouse_id: warehouse_id,
                        relevant_id: relevant_id,

                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if (jsonData == 1) {
                        alert('提交成功');


                    }
                    if (jsonData == 2) {
                        alert('提交失败');
                    }
                    if (jsonData == 3) {
                        alert('此商品已提交');
                    }
                    if (jsonData == 4) {
                        alert('该订单已经提交不能做合单操作');
                    }
                    if (product_id > 0) {
                        handleProductList3();


                        $(".qtyopt").show();
                        $("#product3").val('');
                        $("#product2").val('');
                    }
                    if (count_id > 0) {
                        handleProductList2();
                        $(".qtyopt").show();
                        $("#product3").val('');
                        $("#product2").val('');
                    }
                }


            });
        }

    }

    function updateInvComment(id,deliver_order_id ,order_id) {
        if (check_in_right_time()) {
            return true;
        }
        var inv_comment = $("#inv_comment"+id).val();
        var product_id = $("#product3").val();
        var s_count_id = $('#product2').val();
        var count_id =  s_count_id.substr(0, 6);
        if(inv_comment == ''){
             $("#inv_comment"+id).show();
        }else{


        if(confirm('确认修正货位号？')) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'updateInvComment',
                    // async:false,
                    data: {
                        order_id: order_id,
                        inv_comment:inv_comment,

                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if (jsonData == 1) {
                        alert('修改成功');
                        if (product_id > 0) {
                            handleProductList3();
                        }
                        if (count_id > 0) {
                            handleProductList2();
                        }
                    }
                    if (jsonData == 2) {
                        alert('提交失败');
                    }

                }


            });
        }
        }
    }
    function addConsolidatedRelevant(order_id,order_id2){
        if (check_in_right_time()) {
            return true;
        }
        $("#inventoryIn_"+order_id2).attr("disabled", true);

        showOverlay();
        if(confirm('确认提交合单？')) {
            $('#classSubmitSortingRelevant').remove('class', "submit");
            $('#classSubmitSortingRelevant').attr('value', "正在提交...");
            $.ajax({
                type: 'POST',
                url: 'invapi.php?method=addConsolidatedRelevant',
                data: {
                    method: 'addConsolidatedRelevant',
                    data:{
                        order_id: order_id2,
                        warehouse_id: '<?php echo $_COOKIE['warehouse_id'];?>',
                    },
                },
                success: function (response, status, xhr) {
                    var jsonData = $.parseJSON(response);
                    var old_order_id = parseInt(jsonData.order_id);
                    var deliver_order_ids = jsonData.deliver_order_ids;
                    var order_status_id = jsonData.order_status_id;


                    if (jsonData.status == 999) {
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w2.php';
                    } else
                    if (jsonData.status == 1) {
                        //根据分拣件数确认是否提交订单或待审核

                        var confirmMsg = "商品已全部分拣，是否确认提交合单？";
                        if (parseInt(jsonData.plan_quantity) != parseInt(jsonData.do_quantity)) {
                            confirmMsg = "计划分拣数量" + jsonData.plan_quantity + "，实际分拣" + jsonData.do_quantity + "，是否确认提交合单？";
                        } else {
                            userPendingCheck = 0;
                        }
                        if (confirm(confirmMsg)) {
                            addConsolidatedDoInfo(old_order_id,deliver_order_ids);
                        } else {
                            return false;
                        }
                    } else
                    if (jsonData.status == 0) {
                        alert("今天已经提交过了，不能重复提交");
                    } else
                    if (jsonData.status == 2) {
                        alert("无待确认提交的商品或该订单已经取消");
                    } else
                    if (jsonData.status == 3) {
                        alert("此订单已提交合单不能再次提交");
                    } else
                    if (jsonData.status == 4) {
                        // var orders = global.order_information[parseInt(order_id2)];
                        if (parseInt(order_status_id) == 6) {
                            if (confirm('<?php echo $_COOKIE['warehouse_title'];?>'+"没收到异仓发货，确认修改订单状态并记录")) {
                                updateDoStatus(old_order_id,deliver_order_ids);
                            }
                        } else {
                            alert("当前订单没有捡完不能合单");
                        }
                    } else {
                        alert("错误，请刷新后重试");
                    }

                },
                complete: function () {
                    hideOverlay();

                }

            });
        }
    }
    function updateDoStatus(order_id,deliver_order_ids){
        if (check_in_right_time()) {
            return true;
        }
        showOverlay();
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';

        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=updateDoStatus',
            data: {
                method: 'updateDoStatus',
                data: {
                    order_id: order_id,
                    deliver_order_ids:deliver_order_ids,
                    warehouse_id: warehouse_id,
                },
            },
            success:function(response){
                var jsonData = $.parseJSON(response);
                if(jsonData ==1){
                    alert('修改状态成功，请再次提交合单');
                }else{
                    alert('修改状态失败，需要DO单是已拣完状态');
                }
            },
            complete: function () {
                hideOverlay();
            }
        });
    }
    function  addConsolidatedDoInfo(order_id,deliver_order_ids) {
        if (check_in_right_time()) {
            return true;
        }
        showOverlay();
        var  inventory_user = '<?php echo $_COOKIE['inventory_user'] ;?> ';

        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=addConsolidatedDoInfo',
            //async : false,
            //cache : false,
            data : {
                method : 'addConsolidatedDoInfo',
                data :{
                    order_id : order_id,
                    deliver_order_ids : deliver_order_ids,
                    warehouse_id :'<?php echo $_COOKIE['warehouse_id'];?>',
                    inventory_user:inventory_user,
                    add_user_name_id:'<?php echo $_COOKIE['inventory_user_id'];?>',
                },
            },
            success : function (response, status, xhr){
                if(response){
                    console.log(response);
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);
                    hideOverlay();

                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w2.php';
                    }

                    if(jsonData.status == 1){
                        alert("合单成功");
                        // if (confirm("是否需要进行核查")) {
                        //     window.location = 'make_order_checking_log.php?auth=xsj2015inv&ver=db&get_order_id='+global.old_order_id[parseInt(order_id2)];
                        // }
                    }
                    if(jsonData.status == 0){
                        alert("部分商品未提交入库成功，请重试或联系管理员");
                    }
                    if(jsonData.status == 2){
                        alert("无待确认提交的商品");
                    }
                    if(jsonData.status == 4){
                        alert("每个订单不能重复提交分拣数据");
                    }
                    if(jsonData.status == 5){
                        alert("分拣数量超过订单数量 或 有重复提交相同条码的商品，请删除分拣数据重新分拣 "+jsonData.timestamp);
                    }
                    if(jsonData.status == 6){
                        alert("订单已经提交过，如果继续提交分拣数量就会超过订单数量，请检查");
                    }

                    if(jsonData.status == 8){
                        alert("订单已经提交为‘待审核’，请联系仓库管理员审核");
                    }

                    if(jsonData.status == 9){
                        alert("[Test] "+jsonData.timestamp);
                    }
                } else {
                    alert("当前订单已经合过，无法再次合单");
                }
            },
            complete : function(){
                hideOverlay();
                //提交完成后刷新页面
                //addOrderNum();
                // location.reload();
            }
        });

    }

</script>




</html>
