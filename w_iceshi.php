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
if(!in_array($_COOKIE['user_group_id'],$inventory_user_admin)){
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    exit("入库功能仅限指定人员操作, 请返回");
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪采购入库</title>
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
            width:1.2em;
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
            height: 2em;

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
    <?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <button onclick="javascript:logout_inventory_user();">退出</button>
</div>
<div align="center" id="purchase_info"></div>
<div  style="display: none" id="warehouse_id"> <?php echo $_COOKIE['warehouse_id'];?> </div>
<div align="center" style="display:block; margin:0.5em auto" id="logo"><img src="view/image/logo.png" style="width:6em"/> 采购入库<button class="invopt" style="display: inline" onclick="javascript:location.reload();">刷新</button></div><br />
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



<div id="searchPurchaseOrder">
    订单状态:
    <select id="orderStatus" style="width:12em;height:2em;">

    </select>

    <br>
    <br>
    采购单/退货单:
    <select id = "ordertype" style="width:12em;height:2em;">
        <option value="1">采购单</option>

        <option value="2">退货单</option>
    </select><br />
    <p style="margin: 0.5em"><span style="width: 3em">到货日期：</span>
        <input id="date_start" name="date_start"  autocomplete="off" class="date" type="text" value=""  style="font-size: 15px; width:15.5em ;height: 40px;border:1px solid" data-date-format="YYYY-MM-DD-HH" id="input-date-end" />
    </p>
    商品id：<input id="handle_product" rows="1" maxlength="19"  autocomplete="off" placeholder="点击激活开始扫描" style="font-size: 1.1em;margin-top: 0.5em;margin-bottom: 0.5em;ime-mode:disabled;background-color:#d0e9c6;height: 2em;" "/><br />

    采购单号：<input id="input_purchase_order_id" name="input_purchase_order_id" style="font-size: 1.2em; padding: 0.2em; background-color: #a9dba9; width: 5em;" />


    <input type="button" style="font-size:1.2em;" onclick="javascript:getOrderByStatus()" value="查询"><br />
</div>

<div id="login" align="center" style="margin:0.5em auto; display: none">
    <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
    <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
</div>

<div id="content" style="display: block">
    <div align="center" id="orderListTable" style="margin:0.5em auto;">
        <input type="hidden" id="current_order_id" value="">
        <table border="0" style="width:100%;" cellpadding="2" cellspacing="3" id="ordersHold">
            <thead>
            <tr>

                <th >订单ID</th>
                <th>供应商</th>
                <th>商品数</th>
                <th id="in">未入库</th>
                <th id="out" style="display: none">未出库</th>
                <th>订单状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="ordersList">

            </tbody>
        </table>



    </div>
    <div align="center" style="margin:0.5em auto;">


        <input style="display: none; font-size: 0.9em; line-height: 0.9em" id="getplanned" class="submit_s style_lightgreen" type="button" value="获取计划入库值(<?php echo $date_array[2]['shortdate']; ?>)" onclick="javascript:getSortingList('<?php echo $date_array[2]['date']; ?>');">
        <input type="hidden" value=0 id="purchasePlanId" />
    </div>
    <div id="message" class="message style_ok" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
    <div id="inv_control" align="center">
        <div id="invMethods">

        </div>
        <div id="shelfLifeStrict" style="display: none"></div>
        <div id="productList" name="productList" method="POST" style="display: none">
            <div id="handleArea">指定存货区：<input type="hidden" id="transferAreaItemId" value="0"/><span id = "transferAreaItem" style="display:none;"></span><select name="transfer_area_item" id="transfer_area_item" rows="1" maxlength="19"  style="width:5em;height:2em;">
                </select></div>
            <table id="productsHoldDo" border="0" style="width:100%;display:none;"  cellpadding=2 cellspacing=3>

                <tbody id="productsInfoDo">

                </tbody>
            </table>
            <div id="barcodescanner" style="display: none">
                    <form method="get" onsubmit="handleProductList(null); return false;">
                    <input id="product_make_hand" rows="1" maxlength="19" placeholder="请手动输入" style="font-size: 1.1em;margin-top: 0.5em;margin-bottom: 0.5em;ime-mode:disabled;background-color:#d0e9c6;height: 2em;display:none"/><input class="addprod style_green" type="submit" id="make_hand" value ="确认" style="font-size: 1em; padding: 0.2em;display:none"/><input name="product" id="product" rows="1" maxlength="19"  autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; height: 2em;"/></form>
                <input class="addprod style_green"  id="make_hand_hide" type="submit" value ="手动输入" onclick="javascript:clickProductInput();" style="font-size: 1em; padding: 0.2em;"><input class="addprod style_green"  id="make_hand_hides" type="submit" value ="隐藏手动" onclick="javascript:clickProductInputs();" style="font-size: 1em; padding: 0.2em;display:none;"><br />
                <!--<input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">-->
            </div>

            <script type="text/javascript">
                $("input[name='product']").keyup(function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));

                    if(tmptxt.length >= 4){
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

            <input type="hidden" id="current_do_product" value="0">
            <div style="display:block; margin-top: 1em;">
                <span style=" font-size: 1.2em;">选择待收货商品</span>
                <span style="float:right;font-size: 1em; line-height: 1.8em;">共<span id="count_plan_quantity"></span>件,待完成<span id="count_quantity"></span>件</span>
            </div>
            <table id="productsHold" border="0" style="width:100%;"  cellpadding=2 cellspacing=3>
                <tr>
                    <th align="left">商品</th>
                    <th style="width:2em">待收货</th>
                    <th style="width:2em">货位区</th>
                    <th style="width:3em">状态</th>
                </tr>
                <tbody id="productsInfo">
                <!-- Scanned Product List -->
                </tbody>
            </table>






            <input type="hidden" name="method" id="method" value="" />
            <div style="float:left">当前时间: <span id="currentTime"><?php echo date('Y-m-d H:i:s', time());?></span></div>
            <br />

            <div id="handleArea2" style="display: none;">存货区：<select name="productsAreaInfo2" id="productsAreaInfo2" rows="1" maxlength="19"  style="width:5em;height:2em;">
                    </select><input class="submit" id="submit_del" type="button" value="清除存货区" onclick="javascript:updateStockSectionArea(1,null);"><input class="submit" id="submit_del" type="button" value="放入存货区" onclick="javascript:updateStockSectionArea(0,null);">
                <br />
                <div style="display:block; margin-top: 3em;">
                    <span style=" font-size: 1.2em;">已放入存货区商品</span>
                </div>
                <table id="productsAreaHold" border="1" style="width:100%;"  cellpadding=2 cellspacing=3>
                    <tr>
                        <th align="left">存货区</th>
                        <th align="left" style="width:2em">商品</th>
                        <th align="left" style="width:2em">收货量</th>
                        <th align="left" style="width:3em">修改存货区</th>
                    </tr>
                    <tbody id="productsAreaInfo">
                    <!-- Scanned Product List -->
                    </tbody>
                </table>
            </div>
            <input class="submit" id="submit_del" type="button" value="删除入库数据" onclick="javascript:delPurchaseOrderProductToInv();">
            <?php if(in_array($_COOKIE['user_group_id'],$inventory_user_admin)){?>
                <input class="submit" id="submit" type="button" value="提交" onclick="javascript:addOrderProductToInv_pre();">
            <?php } ?>




            <script type="text/javascript">

                $("input[name='input_vg_frame']").keyup(function(){
                    var tmptxt=$(this).val();
                    //$(this).val(tmptxt.replace(/\D|^0/g,''));
                    if(tmptxt.length == 8){
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
                    if(tmptxt.length == 8){
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
    $(document).ready(function(){
        var today=new Date();
        var year=today.getFullYear();
        var month=today.getMonth()+1;
        var day=today.getDate();

        $('#date_start').val(year+"-"+month+"-"+day );
    });
</script>
<script>

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

        var warehouse_id = $("#warehouse_id").text();
        var select_date = $("#date_start").val();

        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method : 'getPurchaseOrders',
                date : select_date,
                order_status_id : 0,
                warehouse_id :warehouse_id,


            },
            success : function (response , status , xhr){
                //console.log(response);

                if(response){

                    var jsonData = $.parseJSON(response);



                    if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=w_i.php";
                    }



                    var html = '';

                    var each_i_num = 1;

                    $.each(jsonData.data, function(index, value){

                        if(1==1 ){
                            var t_status_class = '';




                            if(value.order_status_id == 1){
                                t_status_class = "style = 'background-color:#ffff99;'";

                            }
                            if(value.order_status_id == 2){
                                //t_status_class = "";
                            }
                            if(value.order_status_id == 3){
                                t_status_class = "style = 'background-color:#666666;'";
                            }
                            if(value.order_status_id == 5){
                                //t_status_class = "";
                            }
                            if(value.order_status_id == 6){
                                t_status_class = "style = 'background-color:#666666;'";
                            }


                            html += '<tr station_id="'+value.station_id+'">';
                            html += '<td '+t_status_class+'>'+value.order_id+'['+'采购单'+']';
                            html += '<input type="hidden" id="order_comment_'+value.order_id+'" value="'+value.order_comment+'">';


                            html +='</td>';

                            html += '<td '+t_status_class+'>'+value.st_name+'</td>';
                            html += '<td '+t_status_class+'>'+value.plan_quantity+'</td>';
                            html += '<td '+t_status_class+'>'+value.quantity+'</td>';








                            html += '<td '+t_status_class+'>'+value.os_name+'</td>';
                            html += '<td>';
                            html += '<button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderInventoryView('+value.order_id+');">查看</button>';
                            if(value.order_status_id == 2){
                                html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+');">开始入库</button>';
                            }


                            html += '</td>';
                            html += '</tr>';
                            html += '';
                        }
                        each_i_num++;
                    });




                    $('#ordersList').html(html);
                    // console.log('Load Stations');
                }
            },
            complete : function(){
                $.ajax({
                    type : 'POST',
                    url : 'invapi_ceshi.php?vali_user=1',
                    data : {
                        method : 'getPurchaseOrderStatus'
                    },
                    success : function (response , status , xhr){
                        //console.log(response);

                        if(response){
                            var jsonData = eval(response);
                            if(jsonData.status == 999){
                                alert("未登录，请登录后操作");
                                window.location = 'inventory_login.php?return=w_i.php';
                            }
                            var html = '<option value=0>-请选择订单状态-</option>';
                            $.each(jsonData, function(index, value){
                                html += '<option value='+ value.order_status_id +' >' + value.name + '</option>';
                            });
                            $('#orderStatus').html(html);

                            console.log('Load Stations');
                        }
                    }
                });





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


    function orderInventory(order_id){
        $('#invMethods').hide();
        $("#orderListTable").hide();
        $('#productList').show();

        $("#searchPurchaseOrder").hide();



        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>'+'');
        var order_comment = $("#order_comment_"+order_id).val();
        $("#show_order_comment").html(order_comment);

        $("#current_order_id").val(order_id);
        getOrderSortingList(order_id,0);
        getOrderAreaList(0);

    }

    function orderInventoryView(order_id){
        $('#invMethods').hide();
        $("#orderListTable").hide();
        $('#productList').show();

        $("#searchPurchaseOrder").hide();


        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>'+'');
        var order_comment = $("#order_comment_"+order_id).val();
        $("#show_order_comment").html(order_comment);
        $("#current_order_id").val(order_id);
        getOrderSortingList(order_id,1);
        getOrderAreaList(1);
    }



    function inventoryMethodHandler(method){
        var methodId = "#"+method;
        $('#method').val(method);
        $('#label').html($(methodId).text());

        $('#invMethods').hide();
        $('#message').hide();
        $('#move_list').hide();

        $('#productList').show();
        $('title').html($(methodId).text() + '-鲜世纪库存管理');
        $('#logo').html('鲜世纪库存管理－'+$(methodId).text());

        if(method == 'inventoryIn'){
            $('#getplanned').show();
            //getSortingList('<?php echo $date_array[2]['date']; ?>');
        }
        else{
            $('#getplanned').hide();
        }


        if($('#station').val() > 0){
            $('#station').attr('disabled',"disabled");
        }
        locateInput();
    }


    function getPurchaseInfo(order_id){
        var warehouse_id = $("#warehouse_id").text();
        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method: 'getPurchaseInfo',
                data:{
                    order_id: order_id,
                    warehouse_id: warehouse_id,
                }
            },
            success:function(response){

                if(response){
                    var jsonData = $.parseJSON(response);
                    console.log(jsonData);
                    var  html = '';
                    html +='采购单:'+ jsonData.purchase_order_id;
                    html += '/'
                    html +="供应商:" + jsonData.name;

                    $("#purchase_info").html(html);
                }
            }
        });
    }


    function getOrderSortingList(order_id,is_view){

        var warehouse_id = $("#warehouse_id").text();

        $('#barcodescanner').show();

        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method : 'getPurchaseOrderSortingList',
                order_id : order_id ,
                warehouse_id : warehouse_id ,
            },
            success : function (response , status , xhr){
                var html = '<td colspan="4">正在载入...</td>';
                $('#productsInfo').html(html);

                if(response){
                    var jsonData = $.parseJSON(response);

                    if(jsonData.status == 1){
                        html = '';
                        var count_plan_quantity = 0;
                        var count_quantity = 0;

                        var order_been_over = "";
                        var order_been_over_size = "";
                        window.product_id_arr = {};

                        $.each(jsonData.data, function(index,value){

                            if( 1==1 ){
                                count_plan_quantity = parseInt(value.plan_quantity) + parseInt(count_plan_quantity);
                                count_quantity = parseInt(value.quantity) + parseInt(count_quantity);


                                if(value.barcode > 0){
                                    product_id_arr[value.barcode] = value.product_id;
                                }
                                if(value.sku > 0){
                                    product_id_arr[value.sku] = value.product_id;
                                }
                                product_id_arr[value.product_id] = value.product_id;
// console.log(product_id_arr);
                                if(value.quantity > 0){
                                    order_been_over = "";
                                    order_been_over_size = "";

                                }
                                else{
                                    order_been_over = "style = 'background-color:#666666;'";
                                    order_been_over_size = "style = 'background-color:#666666;font-size:2em;'";;

                                }
                                var areaname = typeof (value.areaname)=="undefined"?'无':value.areaname;
                                var areaid = typeof (value.areaid)=="undefined"?0:value.areaid;



                                html += '<tr class="barcodeHolder"  id="bd'+ value.product_id +'">' +
                                    '<td '+order_been_over+' class="prodlist" id="td'+value.product_id+'" >';
                                html += value.inv_class_sort + '  --';
                                html +=    '<span name="productBarcode" style="display:none;" >' ;



                                html += value.product_id;

                                html +=    '</span> <span name="productId" id="pid'+value.product_id+'">' + value.product_id + '</span>	<br />';
                                html +=    '<span id="info';

                                html += value.product_id;

                                html += '"></span></td>' +
                                    '<td '+order_been_over_size+' align="center" class="prodlist update_qunity" style="font-size:2em;"><input class="qty update_section" id="'+ value.product_id +'" name="'+ value.product_id +'" value="'+value.quantity+'" /><input type="hidden" id="plan'+ value.product_id +'" value="'+value.quantity+'"><input type="hidden" id="old_plan'+ value.product_id +'" value="'+value.plan_quantity+'"><input type="hidden" id="do'+value.product_id+'" value="0"><input type="hidden" id="pur_plan'+value.product_id+'" value="'+value.purchase_plan_id+'">['+ value.plan_quantity +']</td>' +
                                    '<td '+order_been_over+' align="center" class="prodlist" id="area_item_'+value.product_id+'"><input type="hidden" id="areanames_'+value.product_id+'" name="areanames" value="'+areaid+'"/>'+areaname+'</td>' +
                                    '<td '+order_been_over+' id="opera'+value.product_id+'">';
                                if(value.quantity > 0){
                                    if(is_view != 1){
                                        $('#handleArea2').show();
                                        /* html +=    '<input class="qtyopt pda_add_inv_'+value.product_id+'"  type="button" value="+" onclick="javascript:qtyadd(\''+ value.product_id +'\')" >' +
                                         '<input class="qtyopt style_green pda_add_inv_'+value.product_id+'" type="button" value="-" onclick="javascript:qtyminus2(\''+ value.product_id +'\')" >';*/
                                        html += '<input class="addprod style_green" type="submit" id="make_hand'+value.product_id+'" value ="收货"  onclick="handleProductList(\''+value.product_id+'\');" style="font-size: 1em; padding: 0.2em;"/>';
                                    }
                                    else{
                                        $('#handleArea2').hide();
                                        $('#make_hand').hide();
                                        $('#product_make_hand').hide();
                                        $('#make_hand_hide').hide();
                                        $('#make_hand_hides').hide();
                                        $('#handleArea').hide();
                                        html += '';
                                    }
                                }
                                else{
                                    html += '已完成';
                                }

                                html +=   '</td>' + '</tr>';
                            }



                        });
                        $.ajax({
                            type : 'POST',
                            url : 'invapi_ceshi.php',
                            data : {
                                method: 'getWarehouseTransferArea',
                                warehouse_id :warehouse_id,
                                warehouse_transfer_area_id: 2,

                            },
                            success:function(response){
                                if(response){
                                    var jsonDataTransferItem = $.parseJSON(response);

                                    //分区
                                    var htmlss = '<option value=0>指定存货区</option>';
                                    $.each(jsonDataTransferItem, function (index, value) {
                                        htmlss += '<option value=' + value.stock_section_id + ' >' + value.name + '</option>';
                                    });
                                    $('#transfer_area_item').html(htmlss);
                                }
                            }
                        });

                        // console.log(product_id_arr);
                        // console.log( window.product_inv_barcode_arr);

                        $("#count_plan_quantity").html(count_plan_quantity);
                        $("#count_quantity").html(count_quantity);

                        $('#productsInfo').html(html);

                        if(is_view == 1){
                            $("#submit").hide();
                            $("#product").hide();
                        }

                    }
                    else if(jsonData.status == 1){
                        alert('采购数据已获取并提交入库!');
                        $('#productsInfo').html('');
                    }
                    else if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=w_i.php";
                    }
                    else if(jsonData.status == 6){
                        alert('此订单已提交，不能重复分拣!');
                        $('#productsInfo').html('');
                    }
                    else{
                        alert('无采购数据!');
                        $('#productsInfo').html('');
                    }
                }
            },
            complete : function(){
                getProductName();
                getPurchaseInfo(order_id);
            }
        });
    }
    function getOrderAreaList(is_view){
        if (is_view != 1) {
            var warehouse_id = $("#warehouse_id").text();
            var order_id = $('#current_order_id').val();
            // $('#barcodescanner').show();

            $.ajax({
                type : 'POST',
                url : 'invapi_ceshi.php',
                data : {
                    method : 'getOrderAreaList',
                    order_id : order_id ,
                    warehouse_id : warehouse_id ,
                    warehouse_transfer_area_id: 2,
                },
                success : function (response , status , xhr){
                    var html = '<td colspan="4">正在载入...</td>';
                    $('#productsAreaInfo').html(html);

                    if(response){
                        var jsonData = $.parseJSON(response);
                        var count = jsonData.count;
                        var areas1 = jsonData.areas1;
                        var areas2 = jsonData.areas2;
                        var areas3 = jsonData.areas3;
                        if(jsonData.status == 1){
                            html = '';

                            var order_been_over = "";
                            var order_been_over_size = "";
// console.log(jsonData);
// console.log(jsonData.data);
// console.log(count);
// console.log(areas2);
// console.log(areas1);
                            $.each(jsonData.data, function(index,value){

                                if(value){
                                    html += '<tr class="barcodeHolder"  id="abd'+ value.product_id +'">';
                                    // for (i in count){
                                    //     if(count[i]['stock_section_id'] == value.stock_section_id){
                                    html += '<td '+order_been_over+' class="prodlist" id="area'+value.product_id+'" >'+value.areaname+'</td>';
                                    //     }
                                    // }
                                    html += '<td '+order_been_over+' class="prodlist" id="atd'+value.product_id+'" >';
                                    html += '<span name="productBarcode"  >' ;
                                    html += value.product_id;
                                    html +=    '</span></td>' +
                                        '<td '+order_been_over_size+' align="left" class="prodlist update_qunity" style="font-size:2em;">'+value.quantity+'</td>'+
                                        '<td '+order_been_over+' id="aopera'+value.product_id+'">';
                                    html += '<select id="updateArea_'+value.product_id+'" onchange="updateStockSectionArea(2,\''+value.product_id+'\');">';
                                    for (j in areas1){
                                        if (value.stock_section_id == areas1[j]['stock_section_id']) {
                                            html += '<option value="'+value.stock_section_id+'" selected>'+value.areaname+'</option>';
                                        } else {
                                            html += '<option value="'+areas1[j]['stock_section_id']+'">'+areas1[j]['areaname']+'</option>';
                                        }
                                    }
                                    html += '</select>';
                                    html +=   '</td>' + '</tr>';


                                }
                            });
                            if (!areas2[0]) {
                                html += '<input type="hidden" id="check_area_status" value="-1"/>';
                            } else {
                                html += '<input type="hidden" id="check_area_status" value="'+areas2[0]['stock_section_id']+'"/>';
                            }
                            $('#productsAreaInfo').html(html);
                            html2 = '';
                            for (k in areas3){
                                html2 += '<option value="'+areas3[k]['stock_section_id']+'">'+areas3[k]['areaname']+'</option>';
                            }
                            $('#productsAreaInfo2').html(html2);
                        }
                    }
                }
            });
        }
    }
    // function getWarehouseTransferArea(){
    //     $('#transfer_area_item').show();
    //     var transfer_area_id = $("#transfer_area").val();
    //     $.ajax({
    //         type : 'POST',
    //         url : 'invapi_ceshi.php',
    //         data : {
    //             method: 'getWarehouseTransferArea',
    //             warehouse_transfer_area_id: transfer_area_id,
    //
    //         },
    //         success:function(response){
    //             if(response){
    //                 var jsonDataTransferItem = $.parseJSON(response);
    //
    //                 //分区
    //                 var htmlss = '<option value=0>请选择区域</option>';
    //                 $.each(jsonDataTransferItem, function (index, value) {
    //                     htmlss += '<option value=' + value.stock_section_id + ' >' + value.name + '</option>';
    //                 });
    //                 $('#transfer_area_item').html(htmlss);
    //             }
    //         }
    //     });
    //
    // }
        function updateStockSectionArea(status,data){

        $('#transfer_area_item').show();
        var order_id = $('#current_order_id').val();
        var stock_section_id = $("#updateArea_"+data).val();
        var stock_section_ids = $("#productsAreaInfo2").val();
        var warehouse_id = $("#warehouse_id").text();
        console.log();
        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method: 'updateStockSectionArea',
                stock_section_ids: stock_section_ids,
                stock_section_id: stock_section_id,
                order_id: order_id,
                warehouse_id: warehouse_id,
                status:status,
                product_id:data
            },
            success:function(response){
                if(response){
                    if (status == 1 ){
                        alert('已清空该存货区');
                    } else if ( status == 0) {
                        alert('已放入该存货区');
                    } else if (status == 2) {
                        alert('该存货区已修改');
                    }
                    getOrderAreaList(0);
                    // getOrderSortingList(order_id,0);
                }
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

    function addProduct(id){
        //var id = parseInt(id);

        //Barcode rules for Code128(18) OR Ean13(13||12)
        //18: 6+6+6
        //12: 1+5+5+x
        //13: 2+5+5+x

        if(id !== ''){
            if(id.length == 18){
                var productId = parseInt(id.substr(6,6));
                var price = parseInt(id.substr(12,6))/100;

                if(productId > 3000){
                    alert('非法的商品编号');
                    return false;
                }
            }
            //else if(id.length == 12 || id.length == 13){
            //    var productId = parseInt(id.substr(1-(12-id.length),5));
            //    var price = parseInt(id.substr(1-(12-id.length)+5,5))/100;
            //}
            else{
                alert('错误的条码');
                return false;
            }

            if(productId == NaN || price == NaN){
                console.log('Error barcode format');
                return false;
            }

            var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                '<td><span name="productBarcode" style="display: none" >' + id + '</span><span name="productId" >' + productId + '</span><br /><span id="info'+ id +'"></span></td>' +
                '<td>'+ price +'</td>' +
                '<td><input class="qty" id="'+ id +'" name="'+ id +'" value="1" readonly="readonly" /></td>' +
                '<td>' +
                '<input class="qtyopt" type="button" value="+" onclick="javascript:qtyadd(\''+ id +'\')" >' +
                '<input class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus(\''+ id +'\')" >'+
                '</td>' +
                '</tr>';
            $('#productsInfo').append(html);
            $('#product').val('');

            var todayDate = getSetDate(0,'yyMMdd');
            var tomorrowDate = getSetDate(1,'yyMMdd');
            var productDueDate = parseInt(id.substr(0,6));
            //console.log(todayDate+'---'+productDueDate);

            var shelfLifeStrictText = '['+$('#shelfLifeStrict').text()+']';
            var shelfLifeStrict = eval(shelfLifeStrictText);

            //console.log(shelfLifeStrict[0]);

            var prodInfo = "#info"+id;
            var method = $('#method').val();

            //执行报损提醒，盘点时播放报警，报损时只更改颜色
            if(method =='inventoryInit' || method =='inventoryBreakage'){
                if($.inArray(productId,shelfLifeStrict) >=0 && tomorrowDate >= productDueDate){

                    if(method =='inventoryInit'){
                        playOverdueAlert();
                    }
                    markBarCodeLine('#bd'+id, "#FDDB00");

                    $(prodInfo).html('严格品控，建议报损');
                }
                else if(todayDate > productDueDate){
                    if(method =='inventoryInit'){
                        playOverdueAlert();
                    }
                    markBarCodeLine('#bd'+id, "#EE0000");

                    $(prodInfo).html('已过期，删除并报损');
                }
            }
        }
    }


    function markBarCodeLine(barCodeId,color){
        //console.log('Marked Barcode line'+barCodeId);
        $(barCodeId+' td').css('backgroundColor',color);
    }

    function locateInput(){
        $('#product').focus();
    }

    function getProductBarcodeList(){
        var prodList = '';
        var m = 0;
        $('#productsInfo tr').each(function () {
            var productBarcode = $(this).find('span[name=productBarcode]').text();

            if(m == $("#productsInfo tr").length-1){
                prodList += productBarcode;
            }
            else{
                prodList += productBarcode+',';
            }

            m++;
        });

        return prodList;
    }

    function getProductBarcodeWithQty(){
        var prodList = '';
        var m = 0;
        $('#productsInfo tr').each(function () {
            var productBarcode = $(this).find('span[name=productBarcode]').text();
            var productBarcodeId = '#'+productBarcode;
            var productBarcodeQty = $(productBarcodeId).val();

            if(m == $("#productsInfo tr").length-1){
                prodList += productBarcode+':'+productBarcodeQty+'';
            }
            else{
                prodList += productBarcode+':'+productBarcodeQty+',';
            }

            m++;
        });

        return prodList;
    }

    function getProductName(){

        console.log('Get products name from barcode');

        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }

        var prodList = getProductBarcodeList();

        if(prodList == '' || prodList == null){
            alert('获取条码列表错误或还没有输入商品条码。');
            return false;
        }

        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method : 'getSortingProductInfo',
                products : prodList
            },
            success : function (response , status , xhr){
                if(response){
                    //console.log(response);
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);

                    $.each(jsonData, function(index,value){
                        var infoId = "#info"+index;
                        $(infoId).html(value);
                    });
                }
            },
            complete : function(){

            }
        });

    }

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


    function handleFrameList(frame_type){
        var frame_num = $("#input_"+frame_type+"_frame").val();
        frame_num = frame_num.substr(0,6);//Get 18 code

        var frame_vg_list = $("#frame_"+frame_type+"_list").val();
        if(frame_vg_list == ""){
            $("#frame_"+frame_type+"_list").val(frame_num);
        }
        else{
            if(frame_vg_list.indexOf(frame_num) != -1 )
            {
                alert('不能重复扫描同一个框子');
                return false;
            }
            $("#frame_"+frame_type+"_list").val(frame_vg_list+','+frame_num);
        }
        var frame_num_html = '<div id="frame_'+frame_type+'_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\''+frame_type+'\','+frame_num+');">X</span></div>';
        $("#"+frame_type+"_list").append(frame_num_html);

    }

    function handleMergeFrameList(frame_num){


        frame_num = frame_num.substr(0,6);//Get 18 code

        var frame_vg_list = $("#frame_vg_list").val();
        var frame_mi_list = $("#frame_mi_list").val();
        var frame_meat_list = $("#frame_meat_list").val();
        var frame_ice_list = $("#frame_ice_list").val();

        var has_frame_num = false;

        if(frame_vg_list.indexOf(frame_num) != -1 ){
            if(frame_vg_list.indexOf(','+frame_num) != -1){

                frame_vg_list=frame_vg_list.replace(','+frame_num,'' );
            }
            else if(frame_vg_list.indexOf(frame_num+',') != -1){

                frame_vg_list=frame_vg_list.replace(frame_num+',','' );
            }
            else{

                frame_vg_list=frame_vg_list.replace(frame_num,'' );
            }

            $("#frame_vg_"+frame_num).remove();
            $("#frame_vg_list").val(frame_vg_list);
            $("#frame_vg_count").val(parseInt($("#frame_vg_count").val()) - 1);
            has_frame_num = true;
        }

        if(frame_ice_list.indexOf(frame_num) != -1 ){
            if(frame_ice_list.indexOf(','+frame_num) != -1){

                frame_ice_list=frame_ice_list.replace(','+frame_num,'' );
            }
            else if(frame_ice_list.indexOf(frame_num+',') != -1){

                frame_ice_list=frame_ice_list.replace(frame_num+',','' );
            }
            else{

                frame_ice_list=frame_ice_list.replace(frame_num,'' );
            }
            $("#frame_ice_"+frame_num).remove();
            $("#frame_ice_list").val(frame_ice_list);
            $("#frame_ice_count").val(parseInt($("#frame_ice_count").val()) - 1);
            has_frame_num = true;
        }

        if(frame_meat_list.indexOf(frame_num) != -1 ){
            if(frame_meat_list.indexOf(','+frame_num) != -1){

                frame_meat_list=frame_meat_list.replace(','+frame_num,'' );
            }
            else if(frame_meat_list.indexOf(frame_num+',') != -1){

                frame_meat_list=frame_meat_list.replace(frame_num+',','' );
            }
            else{

                frame_meat_list=frame_meat_list.replace(frame_num,'' );
            }
            $("#frame_meat_"+frame_num).remove();
            $("#frame_meat_list").val(frame_meat_list);
            $("#frame_meat_count").val(parseInt($("#frame_meat_count").val()) - 1);
            has_frame_num = true;
        }

        if(frame_mi_list.indexOf(frame_num) != -1 ){
            if(frame_mi_list.indexOf(','+frame_num) != -1){

                frame_mi_list=frame_mi_list.replace(','+frame_num,'' );
            }
            else if(frame_mi_list.indexOf(frame_num+',') != -1){

                frame_mi_list=frame_mi_list.replace(frame_num+',','' );
            }
            else{

                frame_mi_list=frame_mi_list.replace(frame_num,'' );
            }
            $("#frame_mi_"+frame_num).remove();
            $("#frame_mi_list").val(frame_mi_list);
            $("#frame_mi_count").val(parseInt($("#frame_mi_count").val()) - 1);
            has_frame_num = true;
        }
        if(has_frame_num == false){
            alert("输入错误，要合并弃用的框号不在分拣提交的框号内");
        }
        else{

            var frame_num_html = '<div>'+frame_num+' </div>';
            $("#merge_list").append(frame_num_html);

        }

    }

    function remove_frame(frame_type,frame_num){

        $("#frame_"+frame_type+"_"+frame_num).remove();
        var frame_vg_list = $("#frame_"+frame_type+"_list").val();

        if(frame_vg_list.indexOf(frame_num) != -1 ){
            if(frame_vg_list.indexOf(','+frame_num) != -1){

                frame_vg_list=frame_vg_list.replace(','+frame_num,'' );
            }
            else if(frame_vg_list.indexOf(frame_num+',') != -1){

                frame_vg_list=frame_vg_list.replace(frame_num+',','' );
            }
            else{

                frame_vg_list=frame_vg_list.replace(frame_num,'' );
            }

            $("#frame_"+frame_type+"_list").val(frame_vg_list);

        }
        else{
            alert("数据错误，请刷新页面重新提交框号数据或联系管理员");
        }

    }

    function handleProductList(ids){



        if (ids == null || ids == '') {
            var rawId = $('#product').val().trim() == '' ? $('#product_make_hand').val().trim() : $('#product').val().trim();
            id = rawId.substr(0, 18);//Get 18 code

        } else {
            id = ids;
        }
        if(id.length == 0){
            return false;
        }

        var scan_barcode = id;
        window.scanBarcode = id;

        /*
        if(id.length == 18){

            id = parseInt(id.substr(6,6));
            if(window.product_weight_info[id]){
                alert("此商品需按重出库，请更换商品标签");
                return false;
            }
        }
        */

//       if(id.length == 4 && !check_in_array(id,window.no_scan_product_id_arr)){
//            alert("不能输入商品ID，必须扫描");
//            return false;
//        }
//
        if(id.length == 4){

            id = parseInt(id);

        }


        if(id.length == 18 || id.length == 16){
            id = parseInt(id.substr(0,4));
            if(window.product_weight_info[id]){
                //奇偶校验
                var jiou_count = 0;
                for(i=0;i<scan_barcode.length-1;i++){
                    if(i%2 == 0){
                        jiou_count += (parseInt(scan_barcode.substr(i,1))*3);
                    }
                    else{
                        jiou_count += parseInt(scan_barcode.substr(i,1));
                    }
                }
                var jiou_count_str = jiou_count.toString();


                var jiou_count_last = parseInt(jiou_count_str.substr(jiou_count_str.length-1,1));
                var jiaoyanma = 10 - jiou_count_last;
                var jiaoyanma_str = jiaoyanma.toString();
                var jiaoyanma_last = parseInt(jiaoyanma_str.substr(jiaoyanma_str.length-1,1));

                if(jiaoyanma_last != parseInt(scan_barcode.substr(scan_barcode.length-1,1))){
                    alert("非法条码");
                    return false;
                }

                //重量检测
                var product_weight =  parseInt(scan_barcode.substr(4,5));
                if(window.product_weight_info[id]){
                    var standard_weight = parseInt(window.product_weight_info[id]['weight']);
                }
                else{
                    alert("此商品不需按重出库，请更换条码");
                    return false;
                }

                var weight_propo = (product_weight / standard_weight).toFixed(2);

                if(weight_propo < window.product_weight_info[id]['weight_range_least']){
                    alert("商品包装过少，不合格");
                    return false;
                }
                if(weight_propo > window.product_weight_info[id]['weight_range_most']){
                    alert("商品包装过多，不合格");
                    return false;
                }
            }

        }

        // var areanames = true;
        // $("input[name='areanames']").each(function () {
        //     if($(this).val()==0){
        //         areanames = false;
        //         $("#transfer_area_item").show();
        //         $("#transferAreaItem").hide();
        //     } else {
        //
        //     }
        // });
        // if (areanames == false) {
        //     alert("请扫描全部商品后在提交");
        //     return false;
        // }


        if(window.product_id_arr[id] > 0){
            $("html,body").animate({scrollTop:0}, 500);
            id = window.product_id_arr[id];
            var barCodeId = "#bd"+id;
//扫描栏没有物品
            if ($(".manysubmit").length <= 0) {
                $("#current_do_product").val(0);

                // var products_id = false;
                // $("span[name='productBarcode']").each(function () {
                //     if ($(this).text() == id){
                //         products_id = true;
                //     }
                // });
                // if (products_id == false) {
                //     alert("请输入正确的条码");
                //     return false;
                // }

                var transfer_area_item_text2 = $("#areanames_"+id).val();
                console.log(transfer_area_item_text2);
                if (transfer_area_item_text2 != 0) {
                    $('#transfer_area_item').hide();
                    $('#transferAreaItem').show();
                    $('#transferAreaItem').html($('#area_item_'+id).text());
                    $('#transferAreaItemId').val($('#areanames_'+id).val());
                }else{
                    $('#transfer_area_item').show();
                    $('#transferAreaItem').hide();
                    var transfer_area_text = $("#transfer_area").val();
                    var transfer_area_item_text = $("#transfer_area_item").val();
                    if (transfer_area_text <= 0 || transfer_area_item_text <= 0) {
                        alert("请选择区域");
                        $("#current_do_product").val(1);
                    } else {
                        $('#transferAreaItem').html($("#transfer_area_item").find("option:selected").text());
                        $('#transferAreaItemId').val($("#transfer_area_item").val());
                    }
                }


                //扫描栏有物品

            } else {
                var ls = $(".manysubmit");
                var submit_success = true;
                ls.each(function() {
                    if ($(this).val() == id) {
                        //再次输入正确条码进行操作
                        if ($('#transferAreaItemId').val() != 0) {
                            // $('#transfer_area_item').html('<option value="'+$('#transferAreaItemId').val()+'" selected>'+$('#transferAreaItem').text()+'</option>');
                            $('#transfer_area_item').val($('#transferAreaItemId').val());
                            $('#transfer_area_item').show();
                            $('#transferAreaItem').hide();
                        }
                        // if ($('#area_item_'+id).text()) {
                        //     $('#transferAreaItem').html($('#area_item_'+id).text());
                        //     $('#transferAreaItemId').val($('#areanames_'+id).val());
                        // } else {
                        //     $('#transferAreaItem').html($("#transfer_area_item").find("option:selected").text());
                        //     $('#transferAreaItemId').val($("#transfer_area_item").val());
                        // }
                        $("#current_product_quantity_change"+id).show();
                        $("#manysubmits").show();
                        submit_success = false;
                        $("#current_do_product").val(1);
                        qtyminus(id);


                        /*
                        if(window.product_weight_info[current_do_product]){
                            if(confirm("当前商品还未完成分拣，确认提交？")){
                                tjStationPlanProduct(current_do_product);
                            }
                        }
                        else{
                            var player = $("#player2")[0];
                            player.play();

                            alert("当前商品还未完成分拣，请先提交后再分拣其它商品");
                        }
                        */
                        // var player = $("#player2")[0];
                        // player.play();
                        // if(confirm("当前商品还未完成入库，确认提交？")){
                        //     tjStationPlanProduct(current_do_product);
                        //     $("#current_do_product").val(0);
                        // } else {
                        //     $("#current_do_product").val(0);
                        // }

                        // $("#transfer_area_item").show();
                        // $("#transferAreaItem").hide();
                        // $('#transferAreaItem').show();
                        //

                        // $('#transfer_area_item').show();
                        // $('#transferAreaItem').hide();
                    } else {
                        //再次扫描输入不一样条码
                        $('#transfer_area_item').val(0);
                        var order_id = $('#current_order_id').val();
                        //  getOrderSortingList(order_id,0);
                        $("#current_do_product").val(0);

                        var transfer_area_item_text2 = $("#areanames_"+id).val();
                        console.log(transfer_area_item_text2);
                        if (transfer_area_item_text2 != 0) {
                            $('#transfer_area_item').hide();
                            $('#transferAreaItem').show();
                            add_foot_table($(this).val(),null);
                            $('#transferAreaItem').html($('#area_item_'+id).text());
                            $('#transferAreaItemId').val($('#areanames_'+id).val());
                        }else{
                            $('#transfer_area_item').show();
                            $('#transferAreaItem').hide();

                            // alert(transfer_area_item_text2);
                            var transfer_area_text = $("#transfer_area").val();

                            var transfer_area_item_text = $("#transfer_area_item").val();
                            // alert(transfer_area_item_text);
                            if (transfer_area_item_text <= 0) {
                                alert("请选择区域");
                                $("#current_do_product").val(1);
                                add_foot_table($(this).val(),null);
                            }else {
                                add_foot_table($(this).val(),null);
                                $('#transferAreaItem').html($("#transfer_area_item").find("option:selected").text());
                                $('#transferAreaItemId').val($("#transfer_area_item").val());
                            }

                        }
                        $("#clear"+$(this).val()).remove();
                        $("#clears"+$(this).val()).remove();
                        $("#clearss"+$(this).val()).remove();
                    }


                });
                if (submit_success == false) {
                    // alert("现在可以操作了");
                    $('#product_make_hand').val('');
                    // $('#transferAreaItem').show();
                    // $('#transfer_area_item').show();
                    // return false;



                }
            }
            $('#product').val('');
            // if($("#"+id).val() == 0){
            //
            //         var player = $("#player2")[0];
            //         player.play();
            //         alert("此商品已完成入库，不要重复入库");
            //         return false;
            // }

            var current_do_product = $("#current_do_product").val();



            if(current_do_product == 0){

                showOverlay();

                $("#current_do_product").val(id);
                $("#productsInfoDo").html('<tr id="clear'+id+'"><input type="hidden" class="manysubmit" value="'+id+'"/><td colspan="3" id="product_name'+id+'" align="center" style="font-size:1.4em;"></td></tr><tr id="clearss'+id+'"><th style="width:4em">计划量</th><th style="width:4em">待投篮</th><th align="center" id ="manysubmits" style="display:none;"><button style="float:left" class="invopt manysubmits" onclick="javascript:tjStationPlanProduct(\''+id+'\');">提交</button></th></tr><tr id="clears'+id+'"><td id="current_product_plan'+id+'"  align="center" style="font-size:1.5em;"></td> <td id="current_product_quantity'+id+'" align="center" style="font-size:1.4em;"></td> <td id="current_product_quantity_change'+id+'" style="display:none;"></td></tr>');
                $("#product_name"+id).html($("#info"+id).html());
                $("#current_product_plan"+id).html($("#old_plan"+id).val()+'<span style="display:none;" name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>');
                $("#current_product_quantity"+id).html( '<input class="qty"  id="'+id+'" value="'+$("#"+id).val()+'"><input type="hidden" id="plan'+ id +'" value="'+$("#plan"+id).val()+'"><input type="hidden" id="old_plan'+ id +'" value="'+$("#old_plan"+id).val()+'"><input type="hidden" id="do'+id+'" value="0">');
                $("#current_product_quantity_change"+id).html('<input style="height:3em;width:3em;" class="qtyopt" type="button" value="+" onclick="javascript:qtyadd(\''+id+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus(\''+id+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-10" onclick="javascript:qtyminus10(\''+id+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-50" onclick="javascript:qtyminus50(\''+id+'\')">');





                // $('#make_hand').hide();
                // $('#product_make_hand').hide();
                // $('#make_hand_hides').hide();
                // $('#make_hand_hide').show();

                $("#productsHoldDo").show();

                $(barCodeId).remove();
                hideOverlay();


            }
            // else{
            //     // alert(current_do_product);
            //     // alert(id);
            //     // if(current_do_product == id){
            //     //     qtyminus(id);
            //     // }
            //     // else{
            //     //     /*
            //     //     if(window.product_weight_info[current_do_product]){
            //     //         if(confirm("当前商品还未完成分拣，确认提交？")){
            //     //             tjStationPlanProduct(current_do_product);
            //     //         }
            //     //     }
            //     //     else{
            //     //         var player = $("#player2")[0];
            //     //         player.play();
            //     //
            //     //         alert("当前商品还未完成分拣，请先提交后再分拣其它商品");
            //     //     }
            //     //     */
            //     //     var player = $("#player2")[0];
            //     //         player.play();
            //     //     if(confirm("当前商品还未完成入库，确认提交？")){
            //     //         tjStationPlanProduct(current_do_product);
            //     //         $("#current_do_product").val(0);
            //     //     } else {
            //     //         $("#current_do_product").val(0);
            //     //     }
            //
            //
            //     // }
            //
            // }





            // console.log('Add exist product barcode:'+id);

        }
        else{

            var player = $("#player2")[0];
            player.play();
            alert("此商品不需入库");
            $("#product").val("");
            // addProduct(id);
            console.log('Add product barcode:'+id);
            $("#current_do_product").val(0);

        }
        $('#product_make_hand').val('');

    }

    function qtyadd(id){
        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 1;

        var do_qty = parseInt($("#do"+id).val()) - 1;


        if(qty >= parseInt($("#plan"+id).val())){
            qty = parseInt($("#plan"+id).val());
            do_qty = 0;

        }

        $(prodId).val(qty);
        $("#do"+id).val(do_qty);
        //locateInput();

        console.log(id+':'+qty);
    }
    // //移除
    // function clearproducts(id){
    //     html = add_foot_table(id);
    //     html += '待提交';
    //
    //
    //     html +=   '</td>' +
    //         '</tr>';
    //
    //     $("#productsInfo").append(html);
    //     $("#clear"+id).remove();
    //     $("#clears"+id).remove();
    //     $("#clearss"+id).remove();
    // }
    //新增下方
    function add_foot_table(id,check){
        if (check !== null) {
            var quantity = $("#"+id).val();
            if($("#"+id).val() > 0){
                order_been_overs = "";
            }
            else{
                order_been_overs = "background-color:#666666;";
            }
        } else {
            order_been_overs = "";
            var quantity = $("#plan"+id).val();
        }

        var html = '';
        html += '<tr class="barcodeHolder"  id="bd'+ id +'">' +
            '<td style="'+order_been_overs+'" class="prodlist" id="td'+id+'" ><span name="productBarcode" style="display:none;" >' + id + '</span> <span name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>	<br /><span id="info'+ id +'">'+$("#product_name"+id).html()+'</span></td>' +
            '<td style = "font-size:2em;'+order_been_overs+'" align="center" class="prodlist update_qunity" >'+
            '<input class="qty update_section" id="'+id +'" name="'+ id +'" value="'+quantity+'" /><input type="hidden" id="plan'+ id +'" value="'+$("#plan"+id).val()+'"><input type="hidden" id="old_plan'+ id +'" value="'+$("#old_plan"+id).val()+'"><input type="hidden" id="do'+id+'" value="0">['+ $("#old_plan"+id).val()+']</td>' ;
        var transfer_area_item_id = $("#transfer_area_item").val()==0? $('#transferAreaItemId').val():$("#transfer_area_item").val();
        var transfer_area_item_text = $("#transfer_area_item").val()==0? $('#transferAreaItem').text():$("#transfer_area_item").find("option:selected").text();
        html += '<td style="font-size: 1.4em;'+order_been_overs+'" class="" id="area_item_'+id+'" ><input type="hidden" id="stock_section_id_val"  name="areanames" value="'+transfer_area_item_id+'">'+transfer_area_item_text+'</td>';


        html += '<td style="'+order_been_overs+'"  id="opera'+id+'">';
        if (check !== null) {
            var quantity = $("#"+id).val();
            if($("#"+id).val() > 0){
                html += '<input class="addprod style_green" type="submit" id="make_hand'+id+ '" value ="收货" onclick="handleProductList(\''+id+'\');" style="font-size: 1em; padding: 0.2em;"/>';
            }
            else{
                html += '已完成';

            }
        } else {
            var quantity = $("#plan"+id).val();
            html += '<input class="addprod style_green" type="submit" id="make_hand'+id+ '" value ="收货" onclick="handleProductList(\''+id+'\');" style="font-size: 1em; padding: 0.2em;"/>';
        }


        html +=   '</td>' +
            '</tr>';
        $("#productsInfo").append(html);
        // var html = '';
        // html += '<tr class="barcodeHolder"  id="bd'+ id +'">' +
        //     '<td style="'+order_been_overs+'" class="prodlist" id="td'+id+'" ><span name="productBarcode" style="display:none;" >' + id + '</span> <span name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>	<br /><span id="info'+ id +'">'+$("#product_name"+id).html()+'</span></td>' +
        //     '<td style = "font-size:2em;'+order_been_overs+'" align="center" class="prodlist update_qunity" >'+
        //     '<input class="qty update_section" id="'+id +'" name="'+ id +'" value="'+$("#"+id).val()+'" /><input type="hidden" id="plan'+ id +'" value="'+$("#plan"+id).val()+'"><input type="hidden" id="old_plan'+ id +'" value="'+$("#old_plan"+id).val()+'"><input type="hidden" id="do'+id+'" value="0">['+ $("#old_plan"+id).val()+']</td>' +
        //     '<td style="font-size: 1.4em;'+order_been_overs+'" class="" id="area_item_'+id+'" ><input type="hidden" id="stock_section_id_val"  name="areanames" value="'+$("#transfer_area_item").val()+'">'+$("#transfer_area_item").find("option:selected").text()+'</td>'+
        //     '<td style="'+order_been_overs+'"  id="opera'+id+'">';
        // return html;
    }
    /*
    var arr = ['a','b','c','d'];
    arr.splice($.inArray('c',arr),1);
    alert(arr);
    */
    function qtyminus_weight(id,scan_barcode){

        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 1;

        var do_qty = parseInt($("#do"+id).val()) - 1;


        if(qty >= parseInt($("#plan"+id).val())){
            qty = parseInt($("#plan"+id).val());
            do_qty = 0;

        }

        $(prodId).val(qty);
        $("#do"+id).val(do_qty);
        delete window.product_barcode_arr[id][scan_barcode];
        delete window.product_barcode_arr_s[id][scan_barcode];
        $("#"+id+"_"+scan_barcode).remove();

    }

    function qtyminus(id){
        var prodId = "#"+id;



        scan_barcode = window.scanBarcode;



        if($(prodId).val() >= 1){
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);

            var do_qty = parseInt($("#do"+id).val()) + 1;
            $("#do"+id).val(do_qty);


            if(scan_barcode.length == 18){
                window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            }

            console.log(window.product_barcode_arr);

        }
        // if($(prodId).val() == 0){
        //
        //     if(false){
        //
        //     }
        //     else{
        //         //提交插入中间表
        //         addOrderProductStation(id);
        //         hideOverlay();
        //
        //
        //         html = add_foot_table(id);
        //         html += '已完成';
        //
        //
        //         html +=   '</td>' +
        //             '</tr>';
        //
        //         $("#productsInfo").append(html);
        //         $("#clear"+id).remove();
        //         $("#clears"+id).remove();
        //         $("#clearss"+id).remove();
        //         $("#current_do_product").val(0);
        //
        //     }
        //
        //
        // }

        //locateInput();

        console.log(id+':'+qty);
    }

    function qtyminus10(id){
        var prodId = "#"+id;



        scan_barcode = window.scanBarcode;



        if(parseInt($(prodId).val()) - 10 >= 0){
            var qty = parseInt($(prodId).val()) - 10;
            $(prodId).val(qty);

            var do_qty = parseInt($("#do"+id).val()) + 10;
            $("#do"+id).val(do_qty);


            if(scan_barcode.length == 18){
                window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            }

            console.log(window.product_barcode_arr);

        }
        else{
            alert("入库数量不能超过采购数量，拒收超出商品或联系采购补充采购单");
        }

        // if($(prodId).val() == 0){
        //
        //     if(false){
        //
        //     }
        //     else{
        //         //提交插入中间表
        //         addOrderProductStation(id);
        //         hideOverlay();
        //
        //
        //         html = add_foot_table(id);
        //         html += '已完成';
        //
        //
        //         html +=   '</td>' +
        //             '</tr>';
        //
        //         $("#productsInfo").append(html);
        //         $("#clear"+id).remove();
        //         $("#clears"+id).remove();
        //         $("#clearss"+id).remove();
        //         $("#current_do_product").val(0);
        //
        //     }
        //
        //
        // }

        //locateInput();

        console.log(id+':'+qty);
    }

    function qtyminus50(id){
        var prodId = "#"+id;



        scan_barcode = window.scanBarcode;



        if(parseInt($(prodId).val()) - 50 >= 0){
            var qty = parseInt($(prodId).val()) - 50;
            $(prodId).val(qty);

            var do_qty = parseInt($("#do"+id).val()) + 50;
            $("#do"+id).val(do_qty);


            if(scan_barcode.length == 18){
                window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            }

            console.log(window.product_barcode_arr);

        }
        else{
            alert("入库数量不能超过采购数量，拒收超出商品或联系采购补充采购单");
        }

        // if($(prodId).val() == 0){
        //
        //     if(false){
        //
        //     }
        //     else{
        //         //提交插入中间表
        //         addOrderProductStation(id);
        //         hideOverlay();
        //
        //
        //         html = add_foot_table(id);
        //         html += '已完成';
        //
        //
        //         html +=   '</td>' +
        //             '</tr>';
        //
        //         $("#productsInfo").append(html);
        //         $("#clear"+id).remove();
        //         $("#clears"+id).remove();
        //         $("#clearss"+id).remove();
        //         $("#current_do_product").val(0);
        //
        //     }
        //
        //
        // }

        //locateInput();

        console.log(id+':'+qty);
    }






    function qtyminus2(id){
        var prodId = "#"+id;

        if($(prodId).val() >= 1){
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);

            var do_qty = parseInt($("#do"+id).val()) + 1;
            $("#do"+id).val(do_qty);
        }
        // if($(prodId).val() == 0){
        //
        //     //提交插入中间表
        //     addOrderProductStation(id);
        //     hideOverlay();
        //
        //
        //
        //     $(".pda_add_inv_"+id).hide();
        //     $("#clear"+id).remove();
        //     $("#clears"+id).remove();
        //     $("#clearss"+id).remove();
        //     $("#current_do_product").val(0);
        //
        //
        // }

        //locateInput();

        console.log(id+':'+qty);
    }

    function tjStationPlanProduct(id){

        // if (document.getElementsByClassName("manysubmit").length <= 0) {
        //     alert("请扫描商品在提交");
        //     $("#productsHoldDo").hide();
        //     return false;
        // }
//         if (id == null) {
//            $(".manysubmit").each(function(){
//                var idss = $(this).val();
// // console.log(idss);
//                addOrderProductStation(idss);
//                hideOverlay();
//
//                html = add_foot_table(idss);
//                html += '';
//
//
//                html +=   '</td>' +
//                    '</tr>';
//                $("#productsInfo").append(html);
//                $("#clear"+idss).remove();
//                $("#clears"+idss).remove();
//                $("#clearss"+idss).remove();
//
//            });
//         } else {
        // console.log(id);

        addOrderProductStation(id);
        hideOverlay();
        add_foot_table(id,1);
        //     if($("#"+id).val() > 0){
        //         order_been_overs = "";
        //     }
        //     else{
        //         order_been_overs = "background-color:#666666;";
        //     }
        //     html = add_foot_table(id);
        //     if($("#"+id).val() > 0){
        //         html += '<input class="addprod style_green" type="submit" id="make_hand' + id + '" value ="收货" onclick="handleProductList(\''+id+'\');" style="font-size: 1em; padding: 0.2em;"/>';
        //     }
        //     else{
        //         html += '已完成';
        //     }
        //
        //     html +=   '</td>' +
        //         '</tr>';
        //     $("#productsInfo").append(html);
        //     $("#clear"+id).remove();
        //     $("#clears"+id).remove();
        //     $("#clearss"+id).remove();
        // // }
        //
        // $('#transfer_area_item').show();
        // $('#transferAreaItem').hide();
        // $("#current_do_product").val(0);
        $('#transferAreaItem').hide();


    }

    // function tjStationPlanProduct2(id){
    //     addOrderProductStation(id);
    //
    //     hideOverlay();
    //
    //
    //     $("#current_do_product").val(0);
    //     $("#productsHoldDo").hide();
    //     $("#product_name").html("");
    //
    //
    // }


    function addOrderProductStation(id){


        var order_id = $('#current_order_id').val();
        var transfer_area = '2';
        var transfer_area_item = $("#transfer_area_item").val()==0? $('#transferAreaItemId').val():$("#transfer_area_item").val();
        var warehouse_id = $('#warehouse_id').text();
        var product_id = $("#pid"+id).html();
        var product_quantity = $("#do"+id).val();
// console.log(product_quantity);
// console.log($("#plan"+id).val());
        if(product_quantity > parseInt($("#plan"+id).val())){
            product_quantity = $("#plan"+id).val();
        }
        // console.log(product_quantity);
        // console.log(transfer_area);
        // console.log(transfer_area_item);
        //   console.log(product_quantity);
        // if(product_quantity == 0){
        //     var player = $("#player3")[0];
        //     player.play();
        //     return false;
        // }
        showOverlay();
        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method : 'addPurchaseOrderProductStation',
                order_id : order_id,
                product_id : product_id,
                product_quantity : product_quantity,
                warehouse_id :warehouse_id,
                transfer_area :transfer_area,
                transfer_area_item :transfer_area_item,
                product_barcode_arr : window.product_barcode_arr
            },
            success : function (response, status, xhr){
                if(response){
                    // getOrderSortingList(order_id,0);
                    var jsonData = $.parseJSON(response);
                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w_i.php';
                    }
                    if(jsonData.status == 0){
                        alert(jsonData.msg);
                        return false;
                    }
                    if(jsonData.status == 2){
                        alert("该商品已放入存货区");
                        $("#clear"+product_id).remove();
                        $("#clears"+product_id).remove();
                        $("#clearss"+product_id).remove();
                        return false;
                    }
                    getOrderAreaList(0);
                    $("#clear"+product_id).remove();
                    $("#clears"+product_id).remove();
                    $("#clearss"+product_id).remove();
                    $(".pda_add_inv_"+id).hide();


                    window.product_barcode_arr[id] = {};

                    //var jsonData = eval(response);
                    var player = $("#player3")[0];
                    player.play();


                    $("#plan"+id).val($("#plan"+id).val()-product_quantity);

                    $("#count_quantity").html(parseInt($("#count_quantity").html()) - parseInt(product_quantity));

                    var jsonData = $.parseJSON(response);
                    return jsonData.status;
                }
            }
        });
    }


    function delPurchaseOrderProductToInv(){
        if(confirm("是否确认删除已入库数据？")){
            var order_id = $('#current_order_id').val();

            $.ajax({
                type : 'POST',
                url : 'invapi_ceshi.php',
                data : {
                    method : 'delPurchaseOrderProductToInv',
                    order_id : order_id
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        hideOverlay();


                        if(jsonData.status == 999){
                            alert("未登录，请登录后操作");
                            window.location = 'inventory_login.php?return=w_i.php';
                        }

                        if(jsonData.status == 1){
                            alert("入库数据已删除");
                            location.reload();
                        }

                        if(jsonData.status == 2){
                            alert("入库数据已提交，无法删除");
                        }

                    }
                }
            });
        }
    }


    function addOrderProductToInv_pre(){
        var stock_area = $('#check_area_status').val();
        if (stock_area >= 0) {
            alert("请全部提交到存货区");
            return false;
        }
        // var areanames = true;
        // $("input[name='areanames']").each(function () {
        //     console.log($(this).val());
        //     if($(this).val()==0){
        //         areanames = false;
        //     }
        // });
        // if (areanames == false) {
        //     alert("请扫描全部商品后在提交");
        //     return false;
        // }
        showOverlay();
        var order_id = $('#current_order_id').val();
        var warehouse_id = $("#warehouse_id").text();
        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method : 'addPurchaseOrderProductToInv_pre',
                order_id : order_id,
                warehouse_id :warehouse_id,
            },
            success : function (response, status, xhr){
                var jsonData = $.parseJSON(response);


                if(jsonData.status == 999){
                    alert("未登录，请登录后操作");
                    window.location = 'inventory_login.php?return=w_i.php';
                }

                if(jsonData.status == 1){
                    if(confirm("计划入库数量"+jsonData.plan_quantity+"，实际入库"+jsonData.do_quantity+"，是否确认提交完成？")){
                        addPurchaseOrderProductToInv();
                    }else{
                        hideOverlay();
                        return false;
                    }
                }
                if(jsonData.status == 0){
                    alert("今天已经提交过了，不能重复提交");
                    hideOverlay();
                }
                if(jsonData.status == 2){
                    alert("无待确认提交的商品");
                    hideOverlay();
                }
                if(jsonData.status == 3){
                    alert(jsonData.msg);
                }
            }
        });
    }


    function addPurchaseOrderProductToInv(){

        var order_id = $('#current_order_id').val();
        var warehouse_id = $("#warehouse_id").text();


        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method : 'addPurchaseOrderProductToInv',
                order_id : order_id,
                warehouse_id :warehouse_id,
            },
            success : function (response, status, xhr){
                if(response){
                    //console.log(response);
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);
                    hideOverlay();


                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w_i.php';
                    }

                    if(jsonData.status == 1){
                        alert("提交商品入库成功");
                        updateStockSectionProduct();
                    }
                    if(jsonData.status == 0){
                        alert("部分商品未提交入库成功，请重试或联系管理员");
                    }
                    if(jsonData.status == 2){
                        alert("无待确认提交的商品");
                    }
                    if(jsonData.status == 3){
                        alert(jsonData.msg);
                    }
                    if(jsonData.status == 4){
                        alert("每个订单不能重复提交分拣数据");
                    }
                }
            }
        });
    }
    //更新采购确认表及记录表
    function updateStockSectionProduct() {

        var transfer_area = '2';
        var product_ids = [];
        $("span[name='productBarcode']").each(function () {
            product_ids.push($(this).text());
        });
        var areanames = [];
        $("input[name='areanames']").each(function () {
            areanames.push($(this).val());
        });
        var update_section = [];
        $(".update_section").each(function () {
            update_section.push(parseInt($(this).val()));
        });
        var update_qunity = [];
        $(".update_qunity").each(function () {
            update_qunity.push(parseInt($(this).text().substr(1)));
        });
        var order_id = $('#current_order_id').val();
        var warehouse_id = $("#warehouse_id").text();
// console.log(product_ids);
// console.log(areanames);
// console.log(update_section);
// console.log(update_qunity);
// console.log(transfer_area);
// // return false;
        $.ajax({
            type: 'POST',
            url: 'invapi_ceshi.php',
            data: {
                method: 'updateStockSectionProduct',
                order_id: order_id,
                warehouse_id: warehouse_id,
                areanames: areanames,
                product_ids: product_ids,
                transfer_area: transfer_area,
                update_section: update_section,
                update_qunity: update_qunity,
            },
            success: function (response) {
// console.log(13123);
                var jsonData = $.parseJSON(response);
                if (jsonData.status !== 1) {
                    alert('数据更新错误');
                }
            }
        });
    }
    function addOrderNum(){
        showOverlay();

        var order_id = $('#current_order_id').val();
        var frame_count= '';
        var incubator_count = '';
        var foam_count = '';
        var inv_comment = '';
        var frame_mi_count = '';
        var incubator_mi_count = '';
        var frame_ice_count = '';
        var box_count = '';
        var foam_ice_count = '';
        var frame_meat_count = '';

        var frame_vg_list = '';
        var frame_meat_list = '';
        var frame_mi_list = '';
        var frame_ice_list = '';


        if(window.inventory_user_order[order_id]&&window.inventory_user_order[order_id][1]){
            var add_type = 1;
        }
        if(window.inventory_user_order[order_id]&&window.inventory_user_order[order_id][2]){
            var add_type = 2;
        }
        if(window.inventory_user_order[order_id]&&(window.inventory_user_order[order_id][3]||window.inventory_user_order[order_id][5])){
            var add_type = 4;
        }
        if(window.inventory_user_order[order_id]&&window.inventory_user_order[order_id][4]){
            var add_type = 5;
        }
        <?php if(in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
        var add_type = 3;
        <?php } ?>


        if($("#frame_count")){
            frame_count = $("#frame_count").val();
        }
        if($("#incubator_count")){
            incubator_count = $("#incubator_count").val();
        }
        if($("#foam_count")){
            foam_count = $("#foam_count").val();
        }
        if($("#inv_comment")){
            inv_comment = $("#inv_comment").val();
        }
        if($("#frame_mi_count")){
            frame_mi_count = $("#frame_mi_count").val();
        }
        if($("#incubator_mi_count")){
            incubator_mi_count = $("#incubator_mi_count").val();
        }
        if($("#frame_ice_count")){
            frame_ice_count = $("#frame_ice_count").val();
        }
        if($("#box_count")){
            box_count = $("#box_count").val();
        }
        if($("#frame_meat_count")){
            frame_meat_count = $("#frame_meat_count").val();
        }
        if($("#foam_ice_count")){
            foam_ice_count = $("#foam_ice_count").val();
        }
        if($("#frame_vg_list")){
            frame_vg_list = $("#frame_vg_list").val();
        }
        if($("#frame_meat_list")){
            frame_meat_list = $("#frame_meat_list").val();
        }
        if($("#frame_mi_list")){
            frame_mi_list = $("#frame_mi_list").val();
        }
        if($("#frame_ice_list")){
            frame_ice_list = $("#frame_ice_list").val();
        }

        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method : 'addOrderNum',
                order_id : order_id,
                frame_count : frame_count,
                inv_comment : inv_comment,
                incubator_count : incubator_count,
                foam_count : foam_count,
                frame_mi_count : frame_mi_count,
                incubator_mi_count : incubator_mi_count,
                frame_ice_count : frame_ice_count,
                box_count : box_count,
                frame_meat_count : frame_meat_count,
                foam_ice_count : foam_ice_count,
                add_type : add_type,
                frame_vg_list : frame_vg_list,
                frame_meat_list : frame_meat_list,
                frame_mi_list : frame_mi_list,
                frame_ice_list : frame_ice_list
            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);
                    hideOverlay();


                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w_i.php';
                    }

                    if(jsonData.status == 1){
                        alert("提交框数成功");
                    }
                    if(jsonData.status == 0){
                        alert("提交失败");
                    }
                    if(jsonData.status == 11){
                        alert("提交的框数和框号数量不符，请检查");
                    }
                    if(jsonData.status == 12){
                        alert(jsonData.timestamp);
                    }
                    if(jsonData.status == 13){
                        alert(jsonData.timestamp);
                    }
                    if(jsonData.status == 14){
                        alert(jsonData.timestamp);
                    }
                    if(jsonData.status == 15){
                        alert(jsonData.timestamp);
                    }
                    if(jsonData.status == 16){
                        alert(jsonData.timestamp);
                    }
                    if(jsonData.status == 17){
                        alert(jsonData.timestamp);
                    }
                }
            }
        });
        hideOverlay();


    }









    function inventoryProcess(){
        var method = $('#method').val();
        var prodListWithQty = getProductBarcodeWithQty();
        var station = $('#station').val();

        console.log('Process inventory method:'+method);
        console.log('Process inventory data:'+prodListWithQty);

        if(method == ''){
            alert('请确认操作类型。');
            return false;
        }

        if(!checkStation()){
            return false;
        }

        if(prodListWithQty == '' || prodListWithQty == null ){
            alert('获取条码列表错误或还没有输入商品条码。');
            return false;
        }

        if(confirm('确认提交此次［'+$('#'+method).text()+'］操作？')){
            $('#submit').attr('class',"submit style_gray");
            $('#submit').attr('value',"正在提交...");

            $.ajax({
                type : 'POST',
                url : 'invapi_ceshi.php',
                data : {
                    method : method,
                    station : station,
                    products : prodListWithQty,
                    purchase_plan_id : $('#purchasePlanId').val()
                },
                success : function (response , status , xhr){
                    if(response){
                        //console.log(response);


                        var jsonData = $.parseJSON(response);
                        if(jsonData.status){
                            $('#message').attr('class',"message style_ok");
                            $('#productsInfo').html('');
                            $('#productList').hide();
                            $('#invMethods').show();

                            console.log('Inv. Process OK');
                        }
                        else{
                            $('#message').attr('class',"message style_error");
                            console.log('Inv. Process Error.');
                        }

                        $('#message').show();
                        $('#message').html(jsonData.msg);
                    }
                },
                complete : function(){
                    $('#submit').attr('class',"submit");
                    $('#submit').attr('value',"提交");
                }
            });
        }
    }

    function getInvMoveList(gap){
        //$('#move_list').html(222);
        console.log('Get Station Move List In ['+gap+'] Day(s).');

        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }

        var station = $('#station').val();

        $('#move_list').show();

        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method : 'getStationMove',
                station : station,
                date_gap : gap
            },
            success : function (response , status , xhr){
                var html = '<td colspan="4">正在载入...</td>';
                $('#invMovesInfo').html(html);

                if(response){
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    html = '';
                    $.each(jsonData, function(index,value){
                        if(value.inventory_type_id !== '5'){
                            html += '<tr>' +
                                '<td align="center"><span id="invMoveType_'+ value.inventory_move_id +'">' + value.move_name + '</span></td>' +
                                '<td><span style="display:none" id="invMoveConcact_'+ value.inventory_move_id +'">' + value.contact_name +","+ value.contact_phone + '</span><span id="invMoveTitle_'+ value.inventory_move_id +'">' + value.station_title + '</span><br /><span id="invMoveTime_'+ value.inventory_move_id +'">' + value.date_added + '</div></td>' +
                                '<td align="center">' + value.total_qty + '</td>' +
                                '<td><input class="submit_s style_yellow" type="button" value="查看" onclick="javascript:printInvMove('+value.inventory_move_id+');"></td>' +
                                '</tr>';
                        }
                    });

                    $('#invMovesInfo').html(html);
                }
            },
            complete : function(){

            }
        });

        //locateInput();
    }


    function printInvMove(invMoveId){
        $('#content').hide();
        $('#print').show();

        $('#prtInvMoveType').html( $('#invMoveType_'+invMoveId).html() );
        $('#prtInvMoveTitle').html( $('#invMoveTitle_'+invMoveId).html() +' (' + $('#invMoveConcact_'+invMoveId).html() + ')' );
        $('#prtInvMoveTime').html( $('#invMoveTime_'+invMoveId).html() );

        $('#printTime').html($('#currentTime').html());

        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }

        var station = $('#station').val();

        $.ajax({
            type : 'POST',
            url : 'invapi_ceshi.php',
            data : {
                method : 'getStationMoveItem',
                station : station,
                invMoveId : invMoveId
            },
            success : function (response , status , xhr){
                var html = '<tr><td colspan="5">正在载入...</td></tr>';
                $('#invMovesPrintInfo').html(html);
                var invMovesPrintQtyTotal = 0;
                var invMovesPrintAmountTotal = 0;

                if(response){
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    html = '';
                    $.each(jsonData, function(index,value){
                        html += '<tr>' +
                            '<td align="left">' + value.product_id + '</td>' +
                            '<td align="left">' + value.name + '</td>' +
                            '<td align="center" style="font-size:14px; font-weight: bold">' + value.price + '</td>' +
                            '<td align="center" style="font-size:16px; font-weight: bold">' + value.quantity + '</td>' +
                            '<td></td>' +
                            '</tr>';
                        invMovesPrintQtyTotal += parseInt(value.quantity);
                        invMovesPrintAmountTotal += parseFloat(value.price)*parseInt(value.quantity);
                    });

                    $('#invMovesPrintInfo').html(html);
                    $('#invMovesPrintQtyTotal').html(invMovesPrintQtyTotal);
                    $('#invMovesPrintAmountTotal').html(invMovesPrintAmountTotal.toFixed(2));
                }
            },
            complete : function(){

            }
        });
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
                url : 'invapi_ceshi.php',
                data : {
                    method : 'inventory_logout'
                },
                success : function (response , status , xhr){
                    //console.log(response);
                    window.location = 'inventory_login.php?return=w_i.php';
                }
            });
        }
    }

    function getOrderByStatus(){
        var order_status_id = $("#orderStatus").val();
        var select_date = $("#date_start").val();
        var purchase_order_id = $("#input_purchase_order_id").val();
        var warehouse_id = $("#warehouse_id").text();
        var order_type = $("#ordertype").val();
        var handle_product = $("#handle_product").val();
        if (order_type == 1) {
            $("#in").show();
            $("#out").hide();
            $.ajax({
                type : 'POST',
                url : 'invapi_ceshi.php',
                data : {
                    method : 'getPurchaseOrders',
                    date : select_date,
                    order_status_id : order_status_id,
                    purchase_order_id : purchase_order_id,
                    warehouse_id :warehouse_id,
                    handle_product :handle_product,

                },
                success : function (response , status , xhr){
                    //console.log(response);

                    if(response){

                        var jsonData = $.parseJSON(response);



                        if(jsonData.status == 999){
                            alert(jsonData.msg);
                            location.href = "inventory_login.php?return=w_i.php";
                        }

                        var html = '';

                        var each_i_num = 1;

                        $.each(jsonData.data, function(index, value){

                            if(1==1 ){
                                var t_status_class = '';




                                if(value.order_status_id == 1){
                                    t_status_class = "style = 'background-color:#ffff99;'";

                                }
                                if(value.order_status_id == 2){
                                    //t_status_class = "";
                                }
                                if(value.order_status_id == 3){
                                    t_status_class = "style = 'background-color:#666666;'";
                                }
                                if(value.order_status_id == 5){
                                    //t_status_class = "";
                                }
                                if(value.order_status_id == 6){
                                    //t_status_class = "";
                                }


                                html += '<tr station_id="' + value.station_id + '">';
                                html += '<td ' + t_status_class + '>' + value.order_id +'['+ '采购单' +']';
                                html += '<input type="hidden" id="order_comment_' + value.order_id + '" value="' + value.order_comment + '">';

                                html +='</td>';

                                html += '<td '+t_status_class+'>'+value.st_name+'</td>';
                                html += '<td '+t_status_class+'>'+value.plan_quantity+'</td>';
                                html += '<td '+t_status_class+'>'+value.quantity+'</td>';








                                html += '<td '+t_status_class+'>'+value.os_name+'</td>';
                                html += '<td>';
                                html += '<button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderInventoryView('+value.order_id+');">查看</button>';
                                if(value.order_status_id == 2){
                                    html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+');">开始入库</button>';
                                }


                                html += '</td>';
                                html += '</tr>';
                                html += '';
                            }
                            each_i_num++;
                        });


                        $('#ordersList').html(html);
                        console.log('Load Stations');
                    }
                }


            });
        }


        if(order_type ==  2){
            $("#in").hide();
            $("#out").show();

            $.ajax({
                type: 'POST',
                url: 'invapi_ceshi.php',
                data: {
                    method: 'getPurchaseTypeOrders',
                    data : {
                        date: select_date,
                        order_status_id: order_status_id,
                        purchase_order_id: purchase_order_id,
                        warehouse_id: warehouse_id,
                    }

                },
                success: function (response, status, xhr) {
                    //console.log(response);

                    if (response) {

                        var jsonData = $.parseJSON(response);


                        if (jsonData.status == 999) {
                            alert(jsonData.msg);
                            location.href = "inventory_login.php?return=w_i.php";
                        }

                        var html = '';

                        var each_i_num = 1;

                        $.each(jsonData.data, function (index, value) {

                            if (1 == 1) {
                                var t_status_class = '';


                                if (value.order_status_id == 1) {
                                    t_status_class = "style = 'background-color:#ffff99;'";

                                }
                                if (value.order_status_id == 2) {
                                    //t_status_class = "";
                                }
                                if (value.order_status_id == 3) {
                                    t_status_class = "style = 'background-color:#666666;'";
                                }
                                if (value.order_status_id == 5) {
                                    //t_status_class = "";
                                }
                                if (value.order_status_id == 6) {
                                    //t_status_class = "";
                                }


                                html += '<tr station_id="' + value.station_id + '">';
                                html += '<td ' + t_status_class + '>' + value.order_id +'['+ '退货单' +']';
                                html += '<input type="hidden" id="order_comment_' + value.order_id + '" value="' + value.order_comment + '">';

                                html += '</td>';

                                html += '<td ' + t_status_class + '>' + value.st_name + '</td>';
                                html += '<td ' + t_status_class + '>' + value.plan_quantity + '</td>';
                                html += '<td ' + t_status_class + '>' + value.quantity + '</td>';


                                html += '<td ' + t_status_class + '>' + value.os_name + '</td>';
                                html += '<td>';
                                html += '<button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderInventoryView(' + value.order_id + ');">查看</button>';
                                if (value.order_status_id == 2) {
                                    html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.order_id + ');">开始出库</button>';
                                }


                                html += '</td>';
                                html += '</tr>';
                                html += '';
                            }
                            each_i_num++;
                        });


                        $('#ordersList').html(html);
                        console.log('Load Stations');
                    }
                }


            });






        }


    }

    window.check_category_arr = new Array();
    window.check_category_arr[0] = 72;
    window.check_category_arr[1] = 74;
    window.check_category_arr[2] = 157;




    window.check_product_arr = new Array();
    window.check_product_arr[0] = 6930;
    window.check_product_arr[1] = 6931;
    window.check_product_arr[2] = 6932;
    window.check_product_arr[3] = 6933;
    window.check_product_arr[4] = 6935;
    window.check_product_arr[5] = 6936;
    window.check_product_arr[6] = 6937;
    window.check_product_arr[7] = 6938;
    window.check_product_arr[8] = 6939;
    window.check_product_arr[9] = 6940;
    window.check_product_arr[10] = 6941;
    window.check_product_arr[11] = 6942;
    window.check_product_arr[12] = 6943;


    window.check_product_arr[13] = 6934;
    window.check_product_arr[14] = 6946;
    window.check_product_arr[15] = 6947;
    window.check_product_arr[16] = 6986;



    window.no_scan_product_id_arr = new Array();
    <?php foreach($no_scan_product_id_arr_w_i as $key=>$value){ ?>
    window.no_scan_product_id_arr[<?php echo $key;?>] = <?php echo $value;?>;
    <?php } ?>



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