<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/20
 * Time: 17:23
 */

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
$get_order_id = empty($_GET['get_order_id']) ? 0 : $_GET['get_order_id'];
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
            width:4.3em;
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
        #product2{
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
        #productsInfo1{
            border: 0.1em solid #888888;
        }
        #productsInfo2{
            border: 0.1em solid #888888;
        }
        #productsInfo3{
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
        #productsHold3 td{
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
        #productsHold3 th{
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
        #ordersHold2 td{
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
        #ordersHold2 th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #Shipment_Page th{
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
        .linkButton {width: 2.4rem; height: 1.4rem; margin: 0.3rem; padding:0.2rem; font-size: 0.9rem; color:#ffffff; border-radius: 0.2rem; background-color: #DF0000;}

    </style>

    <style media="print">
        .noprint{display:none;}
    </style>

    <script>
        <?php if(!in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
        $(document).keydown(function (event) {
            $('#product').focus();
        });
        <?php } ?>
    </script>
</head>

<body>

<div style="background-color: #FFFFFF;height:2.6rem; width: 100%">
    <div align="left" style="float: left; margin: 0.5rem;">
        <img src="view/image/logo.png" align="absmiddle" style="width:5rem; "/>
    </div>
    <div align="right">
        <button class="linkButton" style="display: inline;float:left;" onclick="window.location.href='i.php?auth=xsj2015inv&ver=db'">菜单</button>
        <button class="linkButton" style="display: inline;float:left" onclick="javascript:location.reload();">刷新</button>
        <button class="linkButton" onclick="javascript:logout_inventory_user();">退出</button><br />

    </div>
    <br />
    <div style="font-size: 12px; ">
            <?php echo date('Y-m-d H:i:s', time());?>
            <?php echo $_COOKIE['inventory_user'];?>@<?php echo $_COOKIE['warehouse_title'];?>
            <?php if(in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
                <script type="text/javascript">
                    var is_admin = 1;
                </script>
            <?php } ?>
    </div>

</div>
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

<br />
<hr />
<div style="text-align:center;">订单核查</div><br />
<div style="text-align:center;" id="createRelevantWarehouse"><input  style="height:2em;" class="invopt" type="button" value ="开始核查" onclick="javascript:createRelevantWarehouse(1);">
   <br /> <input id="get_search_stock_order_id" style="height:2em;" type="number" value="" placeholder="请输入订单号"/>
    <input  style="height:2em;" class="invopt" type="button" onclick="javascript:get_search_stock_order_id();" value ="核查该订单"/>
</div>
<div id="retail_check_order" style="text-align:center;display: none">
    <span id="get_order_type"></span>单号：<span id = "retail_order_id"></span>,分捡人：<span id = "added_by_name"></span><br />
    <span id = "total_product_sum"></span>箱，<span id = "total_container_sum"></span>筐,货位<span id = "old_stock_area"></span>

</div>
<hr />
<div id="searchPurchaseOrder">
    <span style="width: 3em">分拣日期:</span>
    <input id="date_start" name="date_start"  autocomplete="off" class="date" type="text" style="font-size: 15px; width:6em;height:1.5em;border:1px solid" data-date-format="YYYY-MM-DD-HH" value="<?php echo date("Y-m-d",time());?>"/>
    <input type="hidden" id="last1_day_time" value="<?php echo date("Y-m-d",strtotime("-1 Days"));?>"/>
    <input type="hidden" id="last2_day_time" value="<?php echo date("Y-m-d",strtotime("-2 Days"));?>"/>
    <input type="hidden" id="last3_day_time" value="<?php echo date("Y-m-d",strtotime("-3 Days"));?>"/>
    <input type="hidden" id="next1_day_time" value="<?php echo date("Y-m-d",strtotime("-4 Days"));?>"/>
    <input type="hidden" id="next2_day_time" value="<?php echo date("Y-m-d",strtotime("-5 Days"));?>"/>
    <input type="hidden" id="next0_day_time" value="<?php echo date("Y-m-d",time());?>"/>
    &nbsp;<select id="date_change" style="font-size: 15px;width:4em;height:1.5em;border:1px solid" onchange="javascript:change_date_end_time('date_start','date_change');">
        <option value="next2_day_time">前五天</option>
        <option value="next1_day_time">前四天</option>
        <option value="last3_day_time">前三天</option>
        <option value="last2_day_time">前两天</option>
        <option value="last1_day_time">前一天</option>
        <option value="next0_day_time" selected>今天</option>
    </select>
    <input  style="height:2em;display:;" class="invopt" type="button" value ="搜索" onclick="javascript:getOrderCheckInformation();"/>
    <br />
    <script>
        function change_date_end_time(date_id,date_end_change) {
            var date_change_id = "#"+date_end_change;
            $("#"+date_id).val($("#"+$(date_change_id).val()).val());
        }
    </script>
    <br />
</div>
<div id="login" align="center" style="margin:0.5em auto; display: none">
    <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
    <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
</div>
<div id="content_lists" style="display: block">
    <div align="center" id="orderListTable" style="margin:0.5em auto;">
        <input type="hidden" id="current_deliver_order_id" value="">
        <div style="font-size: 12px; ">共<span id="total_orders"></span>单</div>
        <table border="0" style="width:100%;" cellpadding="2" cellspacing="3" id="ordersHold2">
            <thead>
            <tr>

                <th>货位号</th>
                <th>订单号</th>
                <th>状态/备注</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="ordersList2">

            </tbody>
        </table>
    </div>
</div>

<div id="content" style="display: block">
    <div id="message" class="message style_ok" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
    <div id="inv_control" align="center">
        <div id="invMethods">
<!---->
        </div>
        <div id="shelfLifeStrict" style="display: none"></div>

<!--            <table id="productsHoldDo2" border="1px" style="width:100%;display:none;border:1px solid;solid-color: black;" >-->
<!---->
<!--                <tbody id="productsInfoDo2">-->
<!--                <tr >-->
<!--                    <th colspan="2">班组长核查任务</th>-->
<!--                    <th>核查人</th><th id="current_product_plan2" ></th>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <th>订单号</th><th id="current_product_quantity_change2"></th>-->
<!--                    <th>货位号</th><th id="current_product_quantity2"></th>-->
<!--                </tr>-->
<!--                </tbody>-->
<!--            </table>-->
            <div id="barcodescanner" style="display: none">
                <div style="background: #b9def0;display: ;">
                    <div id="stock_area_message">
                        <br />
                        <B>货位号</B><input id="real_stock_area" type="hidden" value ="0" />
                        <br />
                        <br />
                        <div id="stock_barcode_scanner" style="display:;">
                        <form method="post" onsubmit="check_stock_area(); return false;">
<!--                            <input id="product_make_hand2" rows="1" maxlength="19" placeholder="请手动输入货位号" style="font-size: 1.1em;margin-top: 0.5em;margin-bottom: 0.5em;ime-mode:disabled;background-color:#d0e9c6;height: 2em;display:none"/>-->
<!--                            <input class="addprod style_green" type="submit" id="make_hand2" value ="添加" style="font-size: 1em; padding: 0.2em;display:none"/>-->
                            <input name="stock_area" id="product2" rows="1" maxlength="19"  autocomplete="off" placeholder="货位号扫描" style="ime-mode:disabled; height: 2em;"/></form>
<!--                        <input class="addprod style_green"  id="make_hand_hide2" type="submit" value ="手动输入" onclick="javascript:clickProductInput(2);" style="font-size: 1em; padding: 0.2em;"/>-->
                        <input class="addprod invopt"  id="confirm_stock_area" type="submit" value ="更正/确认货位" style="height:2em;font-size: 1em; padding: 0.2em;"/>
                        </div>
                        <br />
                        <br />
                        <hr />
                    </div>

<!--                <div id="product_div" style="background: #b9def0">-->
                    <B id="product_title" style="display: ;">整件商品分拣信息</B>

                    <div id="product_message" style="display: ;">
                        <br />
                        <br />
                        共<span id = "total_product_sum2"></span>箱，<span id = "product_check_status">待核查</span>
                        <div style="text-align:center;"><input id="check_product" style="height:2em;display:;" class="invopt" type="button" onclick="javascript:onclick_function(2);" value="核查整件商品"/></div>
                        <br />

                        <!--                </div>-->
                        <hr />
                    </div>


<!--                <div id="container_div" style="background: #b9def0">-->
                    <B id="container_title" style="display: ;">周转筐商品分拣信息</B>

                    <div id="container_message" style="display: ;">
                        <br />
                        <br />

                        共<span id = "total_container_sum2"></span>筐，<span id = "container_check_status">待核查</span>
                        <div style="text-align:center;"><input id="check_container" style="height:2em;display: ;" class="invopt" type="button" value ="商品核查" onclick="javascript:onclick_function(3);"></div>
                        <br />
                    </div>


                </div>
                <div id="container_barcodescanner" style="display:none;">
                    <br />
                    <form method="post" onsubmit="handleContainerList(); return false;">
                        <input id="product_make_hand" rows="1" maxlength="19" placeholder="请手动输入" style="font-size: 1.1em;margin-top: 0.5em;margin-bottom: 0.5em;ime-mode:disabled;background-color:#d0e9c6;height: 2em;display:none"/>
                        <input class="addprod style_green" type="submit" id="make_hand" value ="添加" style="font-size: 1em; padding: 0.2em;display:none"/>
                        <input name="container" id="product" rows="1" maxlength="19"  autocomplete="off" placeholder="周转筐扫描" style="ime-mode:disabled; height: 2em;"/></form>
                    <input class="addprod style_green"  id="make_hand_hide" type="submit" value ="手动输入" onclick="javascript:clickProductInput(1);" style="font-size: 1em; padding: 0.2em;"/>
                </div>
                <hr />
                <div style="display:;">
                    <textarea  id ="order_comment" cols="30" rows="5" placeholder="添加备注..."></textarea>
                </div>
                <script type="text/javascript">

                    /*
                    * 扫描手写切换
                    * hide_id 扫描
                    * show_id 输入
                    * submit_id 输入确认
                    * */
                    function clickProductInput(n) {
                        switch(n)
                        {
                            case 1:
                                var hide_id = "#product";
                                var show_id = "#product_make_hand";
                                var submit_id = "#make_hand";
                                var text_id = "#make_hand_hide";
                                var text1 = "手动输入";
                                var text2 = "隐藏手动";
                                clickChangeInput(text_id,hide_id,submit_id,text1,text2,show_id);
                                break;
                            case 2:
                                var hide_id = "#product2";
                                var show_id = "#product_make_hand2";
                                var submit_id = "#make_hand2";
                                var text_id = "#make_hand_hide2";
                                var text1 = "手动输入";
                                var text2 = "隐藏手动";
                                clickChangeInput(text_id,hide_id,submit_id,text1,text2,show_id);
                                break;
                            case 3:
                                var hide_id = "#product3";
                                var show_id = "#product_make_hand3";
                                var submit_id = "#make_hand3";
                                var text_id = "#make_hand_hide3";
                                var text1 = "手动输入";
                                var text2 = "隐藏手动";
                                clickChangeInput(text_id,hide_id,submit_id,text1,text2,show_id);
                                break;
                        }
                    }
                    function clickChangeInput(text_id,hide_id,submit_id,text1,text2,show_id){
                        var hide_order_history = $(text_id).val();
                        if (hide_order_history == text1) {
                            $(hide_id).hide();
                            $(show_id).show();
                            $(submit_id).show();
                            $(text_id).val(text2);
                        } else if (hide_order_history == text2) {
                            $(hide_id).show();
                            $(show_id).hide();
                            $(submit_id).hide();
                            $(text_id).val(text1);
                        }
                    }
                </script>
            </div>
            <table id="productsHoldDo1" border="1px" style="width:100%;display:none;border:1px solid;solid-color: black;" cellpadding=2 cellspacing=3>

                <tbody id="productsInfoDo1">

                </tbody>
            </table>
        <div id="productList" name="productList" method="POST" style="display: none">
<!--            <input type="hidden" id="current_do_product" value="0">-->
            <div id="product_back_information" style="display:; margin-top: 0em;">
                <span style=" font-size: 1.2em;"><span style="font-size: 1.3em">订单商品列表</span>分拣<span id="check_plan_quantity"></span>，核查<span id="check_do_quantity"></span></span>
                <div id="product_barcodescanner" style="display:none;">
                    <br />
                    <form method="post" onsubmit="handleProductList(); return false;">
                        <input id="product_make_hand3" rows="1" maxlength="19" placeholder="请手动输入" style="font-size: 1.1em;margin-top: 0.5em;margin-bottom: 0.5em;ime-mode:disabled;background-color:#d0e9c6;height: 2em;display:none"/>
                        <input class="addprod style_green" type="submit" id="make_hand3" value ="添加" style="font-size: 1em; padding: 0.2em;display:none"/>
                        <input name="product" id="product3" rows="1" maxlength="19"  autocomplete="off" placeholder="商品扫描" style="ime-mode:disabled; height: 2em;"/></form>
                    <input class="addprod style_green"  id="make_hand_hide3" type="submit" value ="手动输入" onclick="javascript:clickProductInput(3);" style="font-size: 1em; padding: 0.2em;"/>
                </div>
            </div>
            <script type="text/javascript">
                $("input[name='product']").keyup(function(){
                    var tmptxt=$(this).val();
                    // $(this).val(tmptxt.replace(/\D/g,''));

                    if(tmptxt.length >= 4){
                        handleProductList();
                    }
                    $(this).val("");
                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));
                });
                $("input[name='container']").keyup(function(){
                    var tmptxt=$(this).val();
                    // $(this).val(tmptxt.replace(/\D/g,''));

                    if(tmptxt.length >= 5){
                        handleContainerList();
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
                $("#product_make_hand3").keyup(function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));

                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));
                });
                $("input[name='stock_area']").keyup(function(){
                    var tmptxt=$(this).val();
                    // $(this).val(tmptxt.replace(/\D/g,''));
                    if(tmptxt.length >= 1){
                        check_stock_area();
                    }
                }).bind("paste",function(){
                    var tmptxt=$(this).val();
                    // $(this).val(tmptxt.replace(/\D/g,''));
                });
                //$("input[name='product']").css("ime-mode", "disabled");
            </script>
            <div id="cancel_container_product" style="margin-right:3em;display: none;"><input style="height:2em;display: ;" class="invopt" type="button" value ="清空当前周转筐商品" onclick="javascript:cancel_product();"/></div>
                <table id="productsHold" border="0" style="width:100%;display:;"  >
                    <tr><td id="container_lists" colspan="4"></td></tr>
                    <tr>
                        <th style="width:3em">商品</th>
                        <th style="width:3em">订单数</th>
                        <th style="width:3em">分拣数</th>
                        <th style="width:3em">已核查</th>
                    </tr>
                    <tbody id="productsInfo">
                    <!-- Scanned Product List -->
                    </tbody>
                </table>
            <hr />
            <table id="Shipment_Page" border="0" style="width:100%;display:;"  >
                <caption style="font-size: 1.5em;color: blue;">核查结果</caption>
                <tr>
                    <th style="width:3em">商品</th>
                    <th style="width:3em">分拣数</th>
                    <th style="width:3em">核查数</th>
                    <th style="width:3em">结果</th>
                </tr>
                <tbody id="form_return3">
                <!-- Scanned Product List -->
                </tbody>
            </table>
<!--            <div id="Shipment_Page"  style="display:;">-->
<!--                <div><hr></div>-->
<!--                <div>-->
<!--                    <ul id="form_return3" style="color: red;">-->
<!--                    </ul>-->
<!--                </div>-->
<!--            </div>-->

            <input type="hidden" name="method" id="method" value="" />

            <br />

            <br />
            <br />
            <br />
        </div>
    </div>
</div>
<div id="onclick_button_display" style="display: none">
<span style="align:left;">
    <input id="left_button1" style="height:2em;display: ;" class="invopt" type="button" value ="返回"/>
    <input id="left_button2" style="height:2em;display: ;" class="invopt" type="button" value ="当前订单检查完毕"/>
</span>
<span style="align:right;">
    <input id="left_button3" style="height:2em;display: ;" class="invopt" type="button" value ="继续检查"/>
</span>
    <input id="compulsory_submission" style="height:2em;display:none;" class="invopt" type="button" onclick="javascript:submitCheckOrderResult(1);" value ="强制提交已核查"/>
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
    var global = {};
    global.warehouseId = '<?php echo $_COOKIE['warehouse_id'];?>';
    global.userId = '<?php echo $_COOKIE['inventory_user_id'];?>';
    <?php if(strstr($_COOKIE['inventory_user'],'scfj')){ ?>
    $(document).keydown(function (event) {
        $('#product').focus();
    });
    <?php } ?>
</script>
<script>

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
    function get_search_stock_order_id() {
        var order_id = parseInt($("#get_search_stock_order_id").val())
        if (order_id > 700000) {
            createRelevantWarehouse(1);
        } else {
            alert("无效的订单号");
        }
    }

    $(document).ready(function () {
        var get_order_id = parseInt($("#get_search_stock_order_id").val());

        if (get_order_id > 0) {
            createRelevantWarehouse(1);
        } else {
            getOrderCheckInformation();
        }

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
    /*zx
    核查任务领取操作*/
    function createRelevantWarehouse(order_type,check_location_id){
        //订单信息
        global.order_detail_information = {};
        //扫描量
        window.product_do_num = 0;
        //计划量
        window.product_plan_number = 0;
        //商品集
        window.product_arr = {};
        //预计周转筐集
        window.get_product_result = {};
        //正在操作的周转筐
        window.local_container = 0;
        //周转筐检查
        window.container_check_result = 0;
        //整件检查
        window.product_check_result = 0;
        //订单检查
        window.order_check_result = 0;

        // var order_type = parseInt($('#warehouse_out_type').val());
        // var order_type = 3;
        var added_by = '<?php echo $_COOKIE['inventory_user_id'];?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id'];?>';
        var cookie_warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var order_id = parseInt($("#get_search_stock_order_id").val())

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getDeliverOrderToCheck',
                data: {
                    warehouse_id: cookie_warehouse_id,
                    added_by: added_by,
                    user_group_id: user_group_id,
                    order_type: order_type,
                    check_location_id: check_location_id,
                    get_order_id : order_id,
                }
            },
            success: function (response, status, xhr) {

                if (response) {
                    var jsonData = $.parseJSON(response);
                    console.log(jsonData.return_code);
                    if (jsonData.return_code == "ERROR") {
                        alert(jsonData.return_data+jsonData.return_msg);
                    } else if (jsonData.return_code == "SUCCESS") {
                        var data = jsonData.return_data;
                        console.log(data.status);
                        if (parseInt(data.status) == 1) {
                            window.container_check_result = 1;
                            window.product_check_result = 1;
                            window.order_check_result = 1;
                            $("product_check_status").html("已核查");
                            $("container_check_status").html("已核查");
                        } else if (parseInt(data.status) == 0) {
                            window.container_check_result = 0;
                            window.product_check_result = 0;
                            window.order_check_result = 0;
                        }
                        global.order_detail_information = data;
                        if (parseInt(data.product_num) > 0) {
                            var product_num = parseInt(data.product_num);
                        } else {
                            var product_num =0;
                        }
                        if (parseInt(data.container_num) > 0) {
                            var container_num = parseInt(data.container_num);
                        } else {
                            var container_num = 0;
                        }
                        global.order_detail_information['prroduct_num'] = product_num;
                        global.order_detail_information['container_num'] = container_num;
                        // if () {
                        //
                        // }

                        $("#total_product_sum").html(product_num);
                        $("#total_product_sum2").html(product_num);
                        $("#total_container_sum").html(container_num);
                        $("#total_container_sum2").html(container_num);
                        $("#added_by_name").html(data.added_by);
                        $("#old_stock_area").html(data.inv_comment);
                        if (parseInt(data.type) != 1) {
                            var order_type = 'SO';
                            $("#retail_order_id").html(data.order_id);
                        } else {
                            var order_type = 'DO';
                            $("#retail_order_id").html(data.deliver_order_id);
                        }
                        $("#get_order_type").html(order_type);
                        // orderInventory(data.order_id,2);
                        $("#retail_check_order").show();

                        onclick_function(1);
                    } else {
                        alert("页面错误，请刷新后重试");
                    }
                }
            }
        });
    }
    function playOverdueAlert(){
        //$('#player').attr('src',sound);
        $('.simpleplayer-play-control').click();
    }
    //主页按钮控制
    function onclick_function(type){
        var order_type = parseInt(global.order_detail_information['type']);
        if (order_type == 0) {
            var order_id = global.order_detail_information['order_id']
        } else if (order_type == 1) {
            var order_id = global.order_detail_information['deliver_order_id']
        }
        $("#onclick_button_display").show();
        $("#barcodescanner").show();
        $("#createRelevantWarehouse").hide();
        $("#orderListTable").hide();
        $("#searchPurchaseOrder").hide();
        $("#Shipment_Page").hide();
        $("#cancel_container_product").hide();
        // $("#product_back_information").hide();
        $("#left_button1").unbind();
        $("#left_button2").unbind();
        $("#left_button3").unbind();
        switch(type)
        {
            /*
            * 主页
            * */
            case 1:

                $('#productList').hide();
                $('#product_barcodescanner').hide();
                $('#container_barcodescanner').hide();
                $('#product_back_information').hide();
                $('#stock_area_message').show();

                $('#order_comment').show();
                $("#productsHold").hide();
                $("#productsInfo").html('');
                $("#productsHoldDo1").hide();
                $("#productsInfoDo1").html('');
                $("#container_lists").html('');
                $("#form_return3").html('');
                if (parseInt(global.order_detail_information['product_num']) > 0) {

                    $('#product_message').show();
                    $('#product_title').show();
                    $('#product_div').show();

                } else {
                    $('#product_message').hide();
                    $('#product_title').hide();
                    $('#product_div').hide();
                }
                if (parseInt(global.order_detail_information['container_num']) > 0) {
                    $('#container_message').show();
                    $('#container_title').show();
                    $('#container_div').show();

                } else {
                    $('#container_message').hide();
                    $('#container_title').hide();
                    $('#container_div').hide();
                }
                if (window.order_check_result == 0) {
                    $("#left_button2").click(function(){onsubmit_function(1,2);});
                    $('#stock_area_message').show();
                } else {
                    $('#stock_area_message').hide();
                }
                $("#left_button1").val("返回");
                $("#left_button2").val("当前订单核查完毕");
                $("#left_button3").val("继续核查");
                $("#left_button1").click(function(){onsubmit_function(1,1);});
                $("#left_button3").click(function(){onsubmit_function(1,3);});

                break;
            /*
            * 商品页
            * */
            case 2:
                if (parseInt(global.order_detail_information['product_num']) > 0) {

                } else {
                    return false;
                }
                $('#productList').show();
                $('#product_back_information').show();
                $('#container_barcodescanner').hide();
                $('#stock_area_message').hide();
                $('#container_message').hide();
                $('#product_message').hide();
                $('#container_title').hide();
                $('#order_comment').hide();
                $('#container_div').hide();
                $('#product_div').show();
                $('#product_title').show();
                $("#productsHold").show();
                $("#container_lists").hide();


                $("#left_button1").val("返回");
                $("#left_button2").val("核查完毕");
                $("#left_button3").val("执行数据更新");
                getOrderSortingList(order_id,order_type,1);
                $("#left_button1").click(function(){onsubmit_function(2,1);});
                if (window.order_check_result == 0) {
                    $('#product_barcodescanner').show();
                    $("#left_button2").click(function () {
                        onsubmit_function(2, 2);
                    });
                    $("#left_button3").click(function () {
                        onsubmit_function(2, 3);
                    });
                }
                break;
            /*
            * 周转筐页
            * */
            case 3:
                if (parseInt(global.order_detail_information['container_num']) > 0) {

                } else {
                    return false;
                }
                $('#productList').show();
                $('#product_back_information').show();
                $('#stock_area_message').hide();
                $('#container_message').hide();
                $('#product_message').hide();
                $('#container_title').show();
                $('#product_title').hide();
                $('#order_comment').hide();
                $('#container_div').show();
                $('#product_div').show();
                $("#productsHold").show();
                $("#container_lists").show();


                $("#left_button1").val("返回");
                $("#left_button2").val("核查完毕");
                $("#left_button3").val("执行数据更新");
                getOrderSortingList(order_id,order_type,2);
                $("#left_button1").click(function(){onsubmit_function(3,1);});
                if (window.order_check_result == 0) {
                    $("#cancel_container_product").show();
                    $('#product_barcodescanner').show();
                    $('#container_barcodescanner').show();
                    $("#left_button2").click(function () {
                        onsubmit_function(3, 2);
                    });
                    $("#left_button3").click(function () {
                        onsubmit_function(3, 3);
                    });
                }
                break;
        }

    }
    /*zx
    * 按钮控制方法*/
    function onsubmit_function(type,style){
        switch(type)
        {
            /*
            * 主页
            * */
            case 1:

                switch(style)
                {
                    /*
                    * 返回
                    * */
                    case 1:
                        window.location = 'make_order_checking_log.php?auth=xsj2015inv&ver=db';
                        break;
                    /*
                    * 核查完毕
                    * */
                    case 2:
                        var total = 0;
                        if (parseInt(global.order_detail_information['container_num']) > 0) {
                            total += 1;
                        }
                        if (parseInt(global.order_detail_information['product_num']) > 0) {
                            total += 1;
                        }
                        if ((window.container_check_result+window.product_check_result) >= total) {
                            if (window.order_check_result == 1) {
                                alert('请不要重复提交');
                                return false;
                            }
                            submitCheckOrderResult();
                        } else {
                            if (window.order_check_result == 1) {
                                alert('请不要重复提交');
                                return false;
                            } else {
                                alert("请全部核查完毕后在提交");
                                $("#compulsory_submission").show();
                            }

                        }
                        break;
                    /*
                    * 继续核查
                    * */
                    case 3:
                        if (window.order_check_result == 1) {
                            createRelevantWarehouse(1);
                        } else {
                            alert("请提交完毕后在核查下一个");
                        }

                        break;
                }

                break;
            /*
            * 商品页
            * */
            case 2:

                switch(style)
                {
                    /*
                    * 返回
                    * */
                    case 1:
                        onclick_function(1);
                        break;
                    /*
                    * 完毕
                    * */
                    case 2:
                        $("#Shipment_Page").show();
                        getCheckOrderInformation(1);
                        break;
                    /*
                    * 数据更新
                    * */
                    case 3:
                        if (window.product_check_result > 0) {
                            alert("已经更新过，不可重复更新");
                            return false;
                        }
                        submitCheckProductResult(1);
                        break;
                }
                break;
            /*
            * 周转筐页
            * */
            case 3:

                switch(style)
                {
                    /*
                    * 返回
                    * */
                    case 1:
                        onclick_function(1);
                        break;
                    /*
                    * 完毕
                    * */
                    case 2:
                        $("#Shipment_Page").show();
                        getCheckOrderInformation(2);
                        break;
                    /*
                    * 数据更新
                    * */
                    case 3:
                        if (window.container_check_result > 0) {
                            alert("已经更新过，不可重复更新");
                            return false;
                        }
                        submitCheckProductResult(2);
                        break;
                }
                break;
        }
    }
    /*
    * 商品或周转筐数据更新
    * */
    function submitCheckProductResult(type) {
        if (window.order_check_result == 1) {
            alert('请不要重复提交');
            return false;
        }
        var get_result = window.get_product_result;
        var result = false;
        if (JSON.stringify(get_result) != "{}") {
            $.each(get_result,function(index,value) {
                if (parseInt(value['quantity']) > parseInt(value['plan_quantity'])) {
                    result = true;
                }
            });
            if (result) {
                alert('请将大于订单数量的商品更正后再执行数据更新');
                return false;
            }
        } else {
            alert('请提交核查完毕后再执行数据更新');
            return false;
        }
        // if (type == 1) {
        //     $.each(window.product_arr,function(index,value2) {
        //         var container_product_id = index;
        //         if (container_product_id == product_id) {
        //             do_product_num += parseInt(value2.num);
        //         }
        //     })
        // } else {
        //     $.each(window.product_arr,function(index,value2) {
        //         var container_product_id = index.split('@');
        //         if (container_product_id[0] == product_id) {
        //             do_product_num += parseInt(value2.num);
        //         }
        //     })
        // }

        var warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var added_by = '<?php echo $_COOKIE['inventory_user_id'];?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id'];?>';
        var order_information = global.order_detail_information;
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'submitCheckProductResult',
                data:{
                    check_location_id: order_information['check_location_id'],
                    order_id: order_information['order_id'],
                    order_type: order_information['type'],
                    product_type: type,
                    warehouse_id: warehouse_id,
                    added_by: added_by,
                    user_group_id: user_group_id,
                }

            },
            success:function(response){
                if(response){
                    var jsonData = $.parseJSON(response);
                    if (jsonData.return_code == "ERROR") {
                        alert(jsonData.return_msg);
                        if (parseInt(jsonData.return_data) == 1) {
                            if (type == 1) {
                                window.product_check_result = 1;
                                $("product_check_status").html("已核查");
                            } else if (type == 2) {
                                window.container_check_result = 1;
                                $("container_check_status").html("已核查");
                            }
                        }
                    } else if (jsonData.return_code == "SUCCESS") {
                        if (type == 1) {
                            window.product_check_result = 1;
                            $("product_check_status").html("已核查");
                        } else if (type == 2) {
                            window.container_check_result = 1;
                            $("container_check_status").html("已核查");
                        }
                        window.get_product_result = {};
                        alert("数据更新成功");
                    }
                }
            }
        });

    }
    /*
    * 订单核查完毕
    * */
    function submitCheckOrderResult(type) {
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var added_by = '<?php echo $_COOKIE['inventory_user_id'];?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id'];?>';
        var order_information = global.order_detail_information;
        var comment = $("#order_comment").val();
        var new_inv_comment = $("#product2").val();
        if (new_inv_comment.length < 1) {
            alert('请输入货位号再提交');
            return false;
        }
        if (type > 0) {
            if (comment.length < 1) {
                alert('请在下方输入提交原因');
                return false;
            }
        }
        $("#product2").val('');
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'submitCheckOrderResult',
                data:{
                    check_location_id: order_information['check_location_id'],
                    order_id: order_information['order_id'],
                    order_type: order_information['type'],
                    warehouse_id: warehouse_id,
                    added_by: added_by,
                    user_group_id: user_group_id,
                    comment: comment,
                    new_inv_comment: new_inv_comment,
                    submit_type:type,
                }

            },
            success:function(response){
                if(response){
                    var jsonData = $.parseJSON(response);
                    if (jsonData.return_code == "ERROR") {
                        alert(jsonData.return_msg);
                    } else if (jsonData.return_code == "SUCCESS") {
                        window.order_check_result = 1;
                        alert("提交成功，请继续核查下一单");
                        $("#compulsory_submission").hide();
                    }
                }
            }
        });

    }
    /*zx
    * 取消或清空周转箱*/
    function cancel_container(container_id){
        if (window.local_container == 0) {
            alert("请先选择周转筐号，再清空商品");
            return false;
        }
        window.product_do_num -= 1;
        $("#check_do_quantity").html(window.product_do_num+'箱');
        $("#cancel_"+container_id).remove();
        cancel_product(container_id);
    }
    /*
    * 取消或清空周转筐
    * */
    function cancel_product(container_id) {
        if (window.order_check_result == 1) {
            alert("该订单已核查过，无法删除周转筐");
            return false;
        }
        if (container_id) {
            var container_id = container_id;
        } else {
            var container_id = window.local_container;
            if (container_id) {

            } else {
                alert("请选择周转筐再清空");
                return false;
            }
        }
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var added_by = '<?php echo $_COOKIE['inventory_user_id'];?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id'];?>';
        var order_information = global.order_detail_information;
        var product_type = global.order_detail_information.product_type;
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'cancelCheckOrderProduct',
                data:{
                    check_location_id: order_information['check_location_id'],
                    order_id: order_information['order_id'],
                    order_type: order_information['type'],
                    warehouse_id: warehouse_id,
                    added_by: added_by,
                    user_group_id: user_group_id,
                    container_id: container_id,
                }

            },
            success:function(response){
                if(response){
                    var jsonData = $.parseJSON(response);
                    if (jsonData.return_code == "ERROR") {
                        alert(jsonData.return_msg);
                    } else if (jsonData.return_code == "SUCCESS") {
                        var get_result = window.get_product_result;
                        var result = false;
                        if (JSON.stringify(get_result) != "{}") {
                            if (order_information['type'] == 0) {
                                getOrderSortingList(order_information['order_id'],0,product_type);
                            } else if (order_information['type'] == 1) {
                                getOrderSortingList(order_information['deliver_order_id'],1,product_type);
                            }
                            get_container_change(container_id);
                            getCheckOrderInformation(product_type);
                        }
                    }
                }
            }
        });

    }
    /*
    * 商品或周转筐核查完毕
    * */
    function getCheckOrderInformation(type) {
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var added_by = '<?php echo $_COOKIE['inventory_user_id'];?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id'];?>';
        var order_information = global.order_detail_information;
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getCheckOrderInformation',
                data:{
                    check_location_id: order_information['check_location_id'],
                    order_id: order_information['order_id'],
                    order_type: order_information['type'],
                    warehouse_id: warehouse_id,
                    added_by: added_by,
                    user_group_id: user_group_id,
                    product_type: type,
                }

            },
            success:function(response){
                if(response){
                    var jsonData = $.parseJSON(response);
                    if (jsonData.return_code == "ERROR") {
                        alert(jsonData.return_msg);
                    } else if (jsonData.return_code == "SUCCESS") {
                        var jsonDatas = jsonData.return_data;
                        var html = '';
                        if (jsonDatas) {
                            window.get_product_result = jsonDatas;
                            $.each(jsonDatas,function(i,value) {
                                var order_been_over = "style = ''";
                                var order_been_over_size = "style = ''";
                                // console.log(value);
                                /*整件*/
                                // if (product_type == 1) {
                                    var sorting_quantity = value.sorting_quantity == '' ? 0 : parseInt(value.sorting_quantity);
                                    var containers = value.container_id.split(',');
                                    var quantitys = value.quantitys.split(',');
                                    var sorting_quantitys = value.sorting_quantitys.split(',');
                                    html += '<tr >';
                                    html += '<th '+order_been_over+'>'+value.stock_area+' [';
                                    html += value.product_id+']<br />'+value.name+'<br />订单量:<span >'+value.plan_quantity+'</span></th>';
                                    html += '<th '+order_been_over+' >';
                                    $.each(containers,function(index2,value2) {
                                        if (value2 == 0) {
                                            html += sorting_quantitys[index2]+'箱<br />';
                                        } else {
                                            html += value2+':<br />'+sorting_quantitys[index2]+'箱<br />';
                                        }
                                    });
                                    html += '</th>';
                                    html += '<th '+order_been_over+' ><span>';
                                    $.each(containers,function(index2,value2) {
                                    if (value2 == 0) {
                                        html += quantitys[index2]+'箱<br />';
                                    } else {
                                        html += value2+':<br />'+quantitys[index2]+'箱<br />';
                                    }
                                    });
                                    html += '</th>';
                                    html += '<th '+order_been_over+'>';
                                    if (parseInt(value.quantity) > parseInt(value.plan_quantity)) {
                                        html += '<br /><span style="color:red;">核查数量大于订单数量，请扫描后进行更正,并放回原货位</span>';
                                    } else {
                                        if (parseInt(value.quantity) > parseInt(value.sorting_quantity)) {
                                            html += '<br /><span style="color:red;">该商品核查数量与分拣数量不匹配</span>';
                                        } else if (parseInt(value.quantity) < parseInt(value.sorting_quantity)) {
                                            html += '<br /><span style="color:red;">该商品核查数量与分拣数量不匹配</span>';
                                        } else {
                                            html += '该商品分拣数量正确';
                                        }
                                    }
                                    html += '</th></tr>';
                                // }
                            });
                            // html += '<tr><th colspan="2" style="color:">核查结果</th><th>货位号</th><th>';
                            // if (parseInt(order_information['invcomment']) != parseInt($("#product2").val())) {
                            //     html += '<span style="color:red;">货位号错误</span>';
                            // }
                            // html += '</th></tr>';
                            // console.log(html);
                            $("#form_return3").html(html);
                        } else {
                            alert("请扫描后再进行提交");
                        }
                    }
                }
            }
        });

    }
    /*zx
    * 获得调拨单详细数据*/
    function getOrderSortingList(deliver_order_id,is_view,product_type){
        //商品信息
        global.transferringBoxes = {};
        //周转筐信息
        global.addTranseBoxList = {};
        var cookie_warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var date_added = $('#date_start').val();
        var order_type = parseInt($('#warehouse_out_type').val());
        var added_by = '<?php echo $_COOKIE['inventory_user_id'];?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id'];?>';
        var order_information = global.order_detail_information;
        global.order_detail_information.product_type = product_type;
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getCheckOrderInStockArea',
                data :{
                    order_id : deliver_order_id ,
                    warehouse_id : cookie_warehouse_id ,
                    added_by :added_by,
                    user_group_id :user_group_id,
                    order_type :is_view,
                    product_type :product_type,
                    check_location_id: order_information['check_location_id'],
                    order_type: order_information['type'],
                }
            },
            success : function (response , status , xhr){
                // var html = '<td colspan="4">正在载入...</td>';
                // $('#productsInfo').html(html);
                var jsonData = $.parseJSON(response);
                if (jsonData.return_code == "ERROR") {
                    alert(jsonData.return_msg);
                } else if (jsonData.return_code == "SUCCESS") {
                    if (product_type == 1) {
                        var jsonDatas = jsonData.return_data;
                        var html = '';
                        if (jsonDatas) {
                            $.each(jsonDatas,function(i,value) {
                                var order_been_over = "style = ''";
                                var order_been_over_size = "style = ''";
                                /*整件*/
                                if (product_type == 1) {
                                    var sorting_quantity = value.sorting_quantity == '' ? 0 : parseInt(value.sorting_quantity);
                                    global.transferringBoxes[value.product_id] = value;
                                    global.transferringBoxes[value.sku_barcode] = value;
                                    if (parseInt(value.quantity) > parseInt(value.plan_quantity) || parseInt(value.plan_quantity) == 0) {
                                        order_been_over_size = "style = 'color:red;'";
                                    }
                                    html += '<tr class="container'+value.container_id+'" id="bd'+ value.product_id +'">';
                                    html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+'">'+value.stock_area+' [';
                                    html += value.product_id+']<br />'+value.name;
                                    if (parseInt(value.plan_quantity) == 0) {
                                        html += '<br /><span '+order_been_over_size+' id="alert_'+value.product_id+'">该商品不属于该订单</span>';
                                    }
                                    html += '</th>';
                                    html += '<th '+order_been_over+' class="product_lists'+value.container_id+'" id="container'+value.product_id+'" >';
                                    html += value.plan_quantity+'</th>';
                                    html += '<th '+order_been_over+' class="containerlist" id="containerl'+value.product_id+'" ><span class="num1_lists" id="containero'+value.product_id+'">';
                                    html += sorting_quantity+'</th>';
                                    html += '<th '+order_been_over_size+'>';
                                    html += '<span id="num_'+value.product_id+'">'+value.quantity+'</span>';
                                    if (parseInt(value.quantity) > parseInt(value.plan_quantity)) {
                                        html += '<br />超出订单数量';
                                    }
                                    html += '</th>';
                                    html += '</tr>';
                                    window.product_do_num += parseInt(value.quantity);
                                }
                            });
                            // console.log(product_type);
                            $("#productsInfo").html(html);
                            $("#check_plan_quantity").html(order_information['product_num']+'箱');
                            $("#check_do_quantity").html(window.product_do_num+'箱');
                        }
                    } else {
                        var jsonDatas1 = jsonData.return_data.container;
                        var jsonDatas2 = jsonData.return_data.product;
                        var html = '';
                        if (jsonDatas1) {
                            $.each(jsonDatas1,function(i,value) {
                                if (window.order_check_result == 1) {
                                    window.product_do_num += 1;
                                    html += '<span id="cancel_' + i + '"><button class="all_container addprod" style="margin-left: 6px;background-color: green;" id="change_color_' + i + '" onclick="javascript:get_container_change(' + i + ')">' + i + '</button><input type="button" class="addprod" style="background-color: red;" onclick="javascript:cancel_container(' + i + ')" value="X"/></span>';
                                }
                                global.addTranseBoxList[i] = value;
                            });
                        }
                        if (jsonDatas2) {
                            // global.addTranseBoxList = jsonDatas2;
                            $.each(jsonDatas2,function(i,value) {
                                window.product_arr[i] = value;
                                global.transferringBoxes[value.product_id+'@'+value.container_id] = value;
                                global.transferringBoxes[value.sku_barcode+'@'+value.container_id] = value;
                            });
                        }
                        $("#container_lists").append(html);
                        $("#check_plan_quantity").html(order_information['container_num']+'筐');
                        $("#check_do_quantity").html(window.product_do_num+'筐');
                    }
                }
            }
        });
    }

    function get_container_change(container_id){
        var value = global.addTranseBoxList[container_id];
        window.local_container = container_id;
        var html = '';
        var order_been_over = "style = ''";
        var order_been_over_size = "style = ''";
        $(".all_container").each(function(){
            $(this).css('background-color','green');
        });
        $("#change_color_"+container_id).css('background-color','red');
        if (typeof(value) != "undefined") {
            $.each(value,function(index,value2) {
                if (parseInt(value2.container_id) == parseInt(container_id)) {
// global.transferringBoxes[value2.product_id] = value2;
                    // global.transferringBoxes[value2.sku_barcode] = value2;
                    var do_product_num = 0;
                    $.each(window.product_arr,function(index3,value3) {
                        var container_product_id = index3.split('@');
                        if (container_product_id[0] == value2['product_id'] && container_product_id[1] != container_id) {
                            do_product_num += parseInt(value3.quantity);
                        }
                    });
                    if ((do_product_num+parseInt(value2.quantity)) > parseInt(value2.plan_quantity) || parseInt(value2.plan_quantity) == 0) {
                        order_been_over_size = "style = 'color:red;'";
                    }
                    var sorting_quantity = value2.sorting_quantity == '' ? 0 : parseInt(value2.sorting_quantity);
                    html += '<tr class="container' + value2.container_id + '" id="bd' + value2.product_id + '">';
                    html += '<th ' + order_been_over + ' class="containerlist" id="containern' + value2.product_id + '">' + value2.stock_area + ' [';
                    html += value2.product_id + ']<br />' + value2.name + '<br />';
                    if (parseInt(value2.plan_quantity) == 0) {
                        html += '<br /><span '+order_been_over_size+' id="alert_'+value2.product_id+'">该商品不属于该订单</span>';
                    }
                    html += '</th>';
                    html += '<th ' + order_been_over_size + ' class="product_lists' + value2.container_id + '" id="container' + value2.product_id + '" >';
                    html += value2.plan_quantity + '<br />共核'+(do_product_num+parseInt(value2.quantity))+'件</th>';
                    html += '<th ' + order_been_over + ' class="containerlist" id="containerl' + value2.product_id + '" ><span class="num1_lists" id="containero' + value2.product_id + '">';
                    html += sorting_quantity + '</th>';
                    html += '<th ' + order_been_over_size + '  id="num_' + value2.product_id + '">';
                    html += value2.quantity;
                    if ((parseInt(value2.quantity)+do_product_num) > parseInt(value2.plan_quantity)) {
                        html += '<br />超出订单数量';
                    }
                    html += '</th>';
                    html += '</tr>';
                }
            });
        } else {
            html = '';
        }
        $("#productsInfo").html(html);
    }


    /*zx
    * 货位号检查*/
    function confirm_stock_area(){
        $("#real_stock_area").val(1);
    }
    function check_stock_area(){
        var player = $("#player3")[0];
        player.play();
        // var confirm_stock_area = $("#real_stock_area").val();
        var right_stock_area = $("#old_stock_area").text().trim();
        var rawId = $('#product2').val().trim() == '' ? $('#product_make_hand2').val().trim() : $('#product2').val().trim();
        var input_stock_area = rawId.substr(0,8);//Get 18 code
        // if (confirm_stock_area == 0) {
        if (right_stock_area != input_stock_area) {

            // $('#product2').val('');
            $('#confirm_stock_area').val('更正货位');
            // $('#product_make_hand2').val('');
            // var player = $("#player2")[0];
            // player.play();
            // alert('货位号更正成功');
        } else {
            $('#confirm_stock_area').val('确认货位');
        }
        // }
    }
    /*zx
    * 商品扫描触发*/
    function handleContainerList() {
        var rawId2 = $('#product').val().trim() == '' ? $('#product_make_hand').val().trim() : $('#product').val().trim();
        var container_id = rawId2.substr(0, 6);
        var html = '';
        if ($("#change_color_"+container_id).text() > 0) {
            // console.log($("#change_color_"+container_id).text());
            var player = $("#player2")[0];
            player.play();
            alert("该周转筐已扫描，请直接选择");
            return false;
        }
        if (typeof(global.addTranseBoxList[container_id]) != 'undefined') {
            $(".all_container").each(function(){
                $(this).css('background-color','green');
            });
            html += '<span id="cancel_'+container_id+'"><button class="all_container addprod" style="margin-left: 6px;background-color: red;" id="change_color_' + container_id + '" onclick="javascript:get_container_change(' + container_id + ')">' + container_id + '</button><input type="button" class="addprod" style="background-color: red;" onclick="javascript:cancel_container('+container_id+')" value="X"/></span>';
            window.product_do_num += 1;
            var player = $("#player3")[0];
            player.play();
            $("#container_lists").append(html);
        } else {
            var player = $("#player2")[0];
            player.play();
            if (confirm('该周转筐不属于该订单,确认添加该周转筐')) {
                $(".all_container").each(function(){
                    $(this).css('background-color','green');
                });
                html += '<span id="cancel_'+container_id+'"><button class="all_container addprod" style="margin-left: 6px;background-color: red;" id="change_color_' + container_id + '" onclick="javascript:get_container_change(' + container_id + ')">' + container_id + '</button><input style="background-color: red;" class="addprod" type="button" onclick="javascript:cancel_container('+container_id+')" value="X"/></span>';
                window.product_do_num += 1;
                $("#container_lists").append(html);
            } else {
                return false;
            }
        }
        get_container_change(container_id);
        window.local_container = container_id;
        $("#check_do_quantity").html(window.product_do_num+'箱');
    }
    /*zx
    * 商品扫描触发*/
    function handleProductList(){
        var order_information = global.order_detail_information;
        var order_type = parseInt(order_information['type']);
        if (order_type == 0) {
            var order_id = order_information['order_id'];
        } else {
            var order_id = order_information['deliver_order_id'];
        }
        // var warehouse_id2 = parseInt($("#warehouse_id").text());
        // var warehouse_id1 =parseInt($('#to_warehouse').val());
        // if (warehouse_id1 == warehouse_id2) {
        //     var relevant_status_id = 4;
        // } else {
        //     var relevant_status_id = 2;
        // }
        // $('.simpleplayer-play-control').click();
        var product_type = global.order_detail_information.product_type;
        /*zx
        * 获得扫描或手输的条码*/
        var rawId = $('#product3').val().trim() == '' ? $('#product_make_hand3').val().trim() : $('#product3').val().trim();
        var container_id = rawId.substr(0, 18);//Get 18 code


        $('#product').val('');
        /*判断是否是退货调拨，如果是，进入调拨单生成页面*/
        // var warehouse_out_type = parseInt($('#warehouse_out_type').val());
        /*
        * 扫描商品*/
        if (product_type == 1) {
            var global_products = global.transferringBoxes[container_id];
            // console.log(typeof(global_products));
            // 扫描已有的商品
            if (typeof(global_products) != 'undefined') {
                var product_id = global_products['product_id'];
                var sorting_quantity = global_products['sorting_quantity'] == "" ? 0 : global_products['sorting_quantity'];
                var product_name = global_products['name'];
                /*zx
                 * 扫描正在扫描商品加数量*/
                if (product_id == parseInt($("#get_product_id").val())) {
                    // console.log(parseInt($("#get_product_id").val()));
                    qtyminus(product_id,2,1,product_id,"pid"+container_id);
                    var player = $("#player3")[0];
                    player.play();
                    /*
                * 扫描列表中的商品*/
                } else {
                    var num2 = parseInt($("#num_" + product_id).text());
                    // console.log(num2);
                    /*zx
                    * 扫描已经扫描过的商品，获得之前扫描的数量*/
                    if (!isNaN(num2)) {
                        var player = $("#player3")[0];
                        player.play();
                        // console.log(container_id);
                        // var products_id = parseInt($("#sku_barcode" + container_id).text());
                        make_product_information(product_id,product_name,"分拣数",sorting_quantity,"核查数",num2,0,"productsHoldDo1","productsInfoDo1",'');                            /*zx
                        * 扫描未扫描过的商品,扫描数量为0，生成商品详情操作栏*/
                    } else {
                        var player = $("#player3")[0];
                        player.play();
                        make_product_information(product_id,product_name,"分拣数",sorting_quantity,"核查数",0,0,"productsHoldDo1","productsInfoDo1",'');
                    }
                }
            // 扫描新商品，获得的数据存入全局中
            } else {
                $.ajax({
                    type: 'POST',
                    url: 'invapi.php',
                    data: {
                        method: 'getProductsInformation',
                        data: {
                            product_id: container_id,
                            order_type: order_type,
                            order_id: order_id,
                            warehouse_id: '<?php echo $_COOKIE['warehouse_id'];?>',
                        }
                    },
                    success: function (response) {
                        if (response) {
                            var jsonData = $.parseJSON(response);
                            hideOverlay();
                            if (jsonData != '') {
                                var player = $("#player2")[0];
                                player.play();
                                if (confirm('该商品不属于该订单,确认添加该商品')) {
                                    var product_name = jsonData.name;
                                    var product_id = jsonData.product_id;
                                    var get_products = [];
                                    get_products['product_id'] = product_id;
                                    get_products['plan_quantity'] = 0;
                                    get_products['sorting_quantity'] = 0;
                                    get_products['sku_barcode'] = jsonData.sku_barcode;
                                    get_products['stock_area'] = jsonData.stock_area;
                                    get_products['name'] = product_name;
                                    get_products['container_id'] = 0;
                                    global.transferringBoxes[product_id] = get_products;
                                    global.transferringBoxes[container_id] = get_products;
                                    make_product_information(container_id,product_name,"分拣数",0,"核查数",0,0,"productsHoldDo1","productsInfoDo1","非此订单商品");
                                }
                            } else {
                                var player = $("#player2")[0];
                                player.play();
                                alert('请输入正确的条码');
                            }
                        }
                    }
                });
            }
        // 扫描周转筐中的商品
        } else if (product_type == 2) {
            //不选择周转筐不能扫描商品
            if (window.local_container == 0) {
                alert("请先选择周转筐号，再扫描商品");
                return false;
            }
            // var container_products = global.addTranseBoxList[window.local_container];
            // if (typeof(container_products) != 'undefined') {
            //     var global_products = container_products[container_id];
            // } else if (typeof(global.transferringBoxes[container_id+"@"+window.local_container]) != 'undefined') {
                var global_products = global.transferringBoxes[container_id+"@"+window.local_container];
            // }
            //扫描已有的商品
            if (typeof(global_products) != 'undefined') {
                var product_id = global_products['product_id'];
                var sorting_quantity = global_products['sorting_quantity'] == "" ? 0 : global_products['sorting_quantity'];
                var product_name = global_products['name'];
                /*zx
                 * 扫描相同商品减数量*/
                if (product_id == parseInt($("#get_product_id").val())) {
                    // console.log(parseInt($("#get_product_id").val()));
                    qtyminus(product_id,2,1,product_id,"pid"+container_id);
                    var player = $("#player3")[0];
                    player.play();
                    /*zx
                    * 扫描新商品生成商品扫描信息栏*/
                } else {
                    var num2 = parseInt($("#num_" + product_id).text());
                    // console.log(num2);
                    /*zx
                    * 扫描已经扫描过的商品，获得之前扫描的数量*/
                    if (!isNaN(num2)) {
                        var player = $("#player3")[0];
                        player.play();
                        // console.log(container_id);
                        // var products_id = parseInt($("#sku_barcode" + container_id).text());
                        make_product_information(product_id,product_name,"分拣数",sorting_quantity,"核查数",num2,0,"productsHoldDo1","productsInfoDo1",'');                            /*zx
                        * 扫描未扫描过的商品,判断数据库是否存在，生成商品详情操作栏*/
                    } else {
                        var player = $("#player3")[0];
                        player.play();
                        make_product_information(product_id,product_name,"分拣数",sorting_quantity,"核查数",0,0,"productsHoldDo1","productsInfoDo1",'');
                    }
                }
            //扫描新商品
            } else {
                $.ajax({
                    type: 'POST',
                    url: 'invapi.php',
                    data: {
                        method: 'getProductsInformation',
                        data: {
                            order_type: order_type,
                            order_id: order_id,
                            product_id: container_id,
                            warehouse_id: '<?php echo $_COOKIE['warehouse_id'];?>',
                        }
                    },
                    success: function (response) {
                        if (response) {
                            var jsonData = $.parseJSON(response);
                            hideOverlay();
                            if (jsonData != '') {
                                var player = $("#player2")[0];
                                player.play();
                                if (confirm('该商品不属于该周转筐或该订单,确认在该周转筐添加该商品')) {
                                    var product_name = jsonData.name;
                                    var product_id = jsonData.product_id;
                                    var get_products = [];
                                    var plan_quantity = parseInt(jsonData.plan_quantity);
                                    var text = '';
                                    if (plan_quantity > 0) {

                                    } else {
                                        plan_quantity = 0;
                                        text = "非此订单商品";
                                    }
                                    get_products['product_id'] = product_id;
                                    get_products['plan_quantity'] = plan_quantity;
                                    get_products['sorting_quantity'] = 0;
                                    get_products['sku_barcode'] = jsonData.sku_barcode;
                                    get_products['stock_area'] = jsonData.stock_area;
                                    get_products['name'] = product_name;
                                    get_products['container_id'] = 0;
                                    global.transferringBoxes[container_id + "@" + window.local_container] = get_products;
                                    global.transferringBoxes[product_id + "@" + window.local_container] = get_products;
                                    make_product_information(product_id, product_name, "分拣数", 0, "核查数", 0, 0, "productsHoldDo1", "productsInfoDo1", text);
                                }
                            } else {
                                var player = $("#player2")[0];
                                player.play();
                                alert('请输入正确的条码');
                            }
                        }
                    }
                });
            }
        }

    }


    /*zx
    * 调拨单首页查询操作*/
    function getOrderCheckInformation(){
        // getNewWarehouseStatus();
        var cookie_warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var deliver_order_id = $('#inventory_deliver_order_sorting').val();
        var order_id = $('#inventory_order_sorting').val();
        var date_added = $('#date_start').val();
        // var date_end = $('#date_end').val();
        // var order_type = parseInt($('#warehouse_out_type').val());
        var order_type = 3;
        var added_by = '<?php echo $_COOKIE['inventory_user_id'];?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id'];?>';
        // if ((Date.parse(date_end)-Date.parse(date_added))>(3*24000*3600)) {
        //     alert("日期间隔不能大于三天");
        //     return false;
        // }
        if (order_type <= 2) {
            $("#get_warehouse_out_type").html('未合单DO单');
        }
        if (order_type >= 2) {
            $("#get_warehouse_out_type2").html('已合单SO单');
        }
// console.log(added_by);
// console.log(user_group_id);
        // if (warehouse_out_type == 3) {
        //    $("#form_return").show();
        //    $("#ordersHold").hide();
        //    searchRequisition();
        //    return false;
        // }
        $("#ordersHold").show();
        $("#form_return").hide();
        // /*zx
        // * 出入库不同且选择退货调拨单，显示生成退货调拨单按钮*/
        // if (warehouse_id1 != warehouse_id2 && warehouse_out_type == 4) {
        $("#createRelevantWarehouse").show();
        // }
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getOrderCheckInformation',
                data: {
                    warehouse_id: cookie_warehouse_id,
                    deliver_order_id: deliver_order_id,
                    order_id: order_id,
                    date_added: date_added,
                    added_by: added_by,
                    user_group_id: user_group_id,
                    order_type: order_type,
                }
            },
            success: function (response, status, xhr) {

                if (response) {

                    var jsonData = $.parseJSON(response);
                    if (jsonData.return_code == "ERROR") {
                        alert(jsonData.return_msg);
                    } else if (jsonData.return_code == "SUCCESS") {
                        var jsonDatas = jsonData.return_data;
                        var do_datas = jsonDatas.do;
                        var so_datas = jsonDatas.so;
                        var num_datas = jsonDatas.num;
                        var do_num = num_datas.do_num > 0 ? parseInt(num_datas.do_num) : 0 ;
                        var so_num = num_datas.so_num > 0 ? parseInt(num_datas.so_num) : 0 ;
                        var do_do_num = num_datas.do_do_num > 0 ? parseInt(num_datas.do_do_num) : 0 ;
                        var so_do_num = num_datas.so_do_num > 0 ? parseInt(num_datas.so_do_num) : 0 ;
                        var html = '';
                        var html2 = '';
                        var num = 0;
                        if (do_do_num > 0) {
                            $.each(do_datas, function (index, value) {
                                var t_status_class = '';

                                html += '<tr station_id="' + value.deliver_order_id + '">';
                                html += '<td ' + t_status_class + '>' + value.stock_area+ '</td>';
                                html += '<td>DO单：' + value.deliver_order_id + '<br />'+value.product_num+'箱+'+value.container_num+'框</td>';
                                if (value.comment != '') {
                                    html += '<td ' + t_status_class + '>' + value.comment + '</td>';
                                } else {
                                    html += '<td ' + t_status_class + '>已核查</td>';
                                }
                                html += '<td>';

                                // $("#current_product_plan").html(value.title + '<input type="hidden" id="from_warehouse" value="'+ value.from_warehouse+'">');
                                // if (value.status_id == 9) {
                                //     html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.deliver_order_id + ',' + 1 + ');">核查</button>';
                                // } else {
                                html += '<button  class="invopt" style="display: inline" onclick="javascript:createRelevantWarehouse(3,'+value.check_location_id+');">查看</button>';
                                // }

                                html += '</td>';
                                html += '</tr>';
                            });
                        }
                        if (so_do_num > 0) {
                            $.each(so_datas, function (index, value) {
                                var t_status_class = '';

                                html += '<tr station_id="' + value.order_id + '">';
                                html += '<td ' + t_status_class + '>' + value.stock_area+ '</td>';
                                html += '<td>SO单：' + value.order_id + '<br />'+value.product_num+'箱+'+value.container_num+'框</td>';
                                if (value.comment != '') {
                                    html += '<td ' + t_status_class + '>' + value.comment + '</td>';
                                } else {
                                    html += '<td ' + t_status_class + '>已核查</td>';
                                }
                                html += '<td>';

                                // $("#current_product_plan").html(value.title + '<input type="hidden" id="from_warehouse" value="'+ value.from_warehouse+'">');
                                // if (value.status_id == 9) {
                                //     html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.deliver_order_id + ',' + 1 + ');">核查</button>';
                                // } else {
                                html += '<button  class="invopt" style="display: inline" onclick="javascript:createRelevantWarehouse(3,'+value.check_location_id+');">查看</button>';
                                // }

                                html += '</td>';
                                html += '</tr>';
                            });
                        }

                        // $("#current_product_quantity").html($('#to_warehouse').find("option:selected").text());
                        // $("#current_product_quantity_change").html($('#date_start').val());
                        $("#total_orders").html((so_num+do_num)+"单,已核查DO单"+do_do_num+"单,SO单"+so_do_num+"单;待核查DO单<span style='color:red;'>"+(do_num-do_do_num)+"</span>单,SO单<span style='color:red;'>"+(so_num-so_do_num)+"</span>");
                        // $("#get_warehouse_out_type").html($("#warehouse_out_type option:selected").text());
                        // $('#ordersList').html(html);
                        $('#ordersList2').html(html);
                        // if (jsonData.data.container1 != '') {
                        //     $('#much_look').show();
                        // }
                        // console.log(num);
                        // if (num == 0) {
                        // }
                    } else {
                        alert("页面错误，请刷新后重试");
                    }
                }
            }
        });
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
    function make_product_information(id,text1,text2,quantity,text3,real_quantity,text4,table_id,tbody_id,product_text){
        var product_type = global.order_detail_information.product_type;
        var table_id = "#"+table_id;
        var tbody_id = "#"+tbody_id;
        var product_name_id = "product_name1"+id;
        var plan_quantity_id = "current_product_plan1"+id;
        var quantity_id = "current_product_quantity1"+id;
        var operation_id = "current_product_quantity_change1"+id;
        var prodId = id;
        var planId = "sdpid"+id;
        var display1 = '';
        var display2 = '';
        var display3 = '';
        if (text1 == 0) {
            display1 = "none";
        }
        if (text2 == 0) {
            display2 = "none";
        }
        if (text3 == 0) {
            display3 = "none";
        }
        if (text4 == 0) {
            text4 = "提交";
        }
        $(tbody_id).html('<tr id="clear' + id + '"><input type="hidden" id="get_product_id" value="' +
            id + '"/><td colspan="3" id="'+product_name_id+'" align="center" style="font-size:1.4em;display:'+display1+';"></td></tr><tr id="clearss' +
            id + '"><th style="width:4em;display:'+display2+';">'+text2+'</th><th style="width:4em;display:'+display3+';">'+text3+'</th><th align="center" id ="manysubmits"><button style="float:left" class="invopt manysubmits" onclick="javascript:tjStationPlanProduct(' +
            id +',\''+product_text+'\');">'+text4+'</button></th></tr><tr id="clears'+
            id + '"><td id="'+
            plan_quantity_id+'" align="center" style="font-size:1.5em;display:'+display2+';"></td> <td id="'+
            quantity_id+'" align="center" style="font-size:1.4em;display:'+display3+';"></td> <td id="'+
            operation_id+'"></td></tr>');
        if (product_type == 1) {
            $("#"+product_name_id).html(text1);
        } else if (product_type == 2) {
            $("#"+product_name_id).html('当前周转筐：'+window.local_container+'<br />'+text1);
        }
        $("#"+plan_quantity_id).html(quantity + '<span style="display:none;" name="productId" id="'+planId+'">' + quantity + '</span>');
        $("#"+quantity_id).html('<input class="qty"  id="'+prodId+'" value="' + real_quantity + '">');
        $("#"+operation_id).html('<input style="height:3em;width:3em;" class="qtyopt" type="button" value="-" onclick="javascript:qtyminus(\'' +
            id + '\',1,1,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+" onclick="javascript:qtyminus(\'' +
            id + '\',2,1,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+10" onclick="javascript:qtyminus(\'' +
            id + '\',2,10,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+50" onclick="javascript:qtyminus(\'' +
            id + '\',2,50,\''+prodId+'\',\''+planId+'\')">');
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
    function qtyminus(id,status,num,prodId,planId){
        var prodId = "#"+prodId;
        var planId = "#"+planId;
        if (status == 1) {
            if($(prodId).val() >= num){
                var qty = parseInt($(prodId).val()) - num;
                $(prodId).val(qty);
            }
        } else if (status == 2) {
            // if ($(planId).text() > 0) {
            //     if ($(prodId).val() <= $(planId).text() -num) {
            //         var qty = parseInt($(prodId).val()) + num;
            //         $(prodId).val(qty);
            //     }
            // } else {
            var qty = parseInt($(prodId).val()) + num;
            $(prodId).val(qty);
            // }


        }
    }
    /*生成详细商品列表*/
    function tjStationPlanProduct(product_id,product_text){
        // $("#productsHold2").hide();
        $("#productsHold").show();
        var num1 = parseInt($("#"+product_id).val());
        var num2 = parseInt($("#num_"+product_id).text());
        var num3 = isNaN(num2) ? 10000 : parseInt($("#container_"+product_id).text());
        var product_type = global.order_detail_information.product_type;
        var product_information = [];
        var player = $("#player3")[0];
        player.play();
        /*zx
        * 判断是否已在下方生成过，如果是，修改数量，否则生成新的*/
        var html = '';
        var order_been_over = "style = ''";
        var order_been_over_size = "style = ''";
        /*整件*/
        if (product_type == 1) {
            var value = global.transferringBoxes[product_id];
            var sorting_quantity = value.sorting_quantity == '' ? 0 : parseInt(value.sorting_quantity);
            var plan_quantity = value.plan_quantity == '' ? 0 : parseInt(value.plan_quantity);
            if (num1 > plan_quantity) {
                order_been_over_size = "style = 'color:red'";
            }
            product_information['quantity'] = num1;

            html += '<tr class="container'+value.container_id+'" id="bd'+ value.product_id +'">';
            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+'">'+value.stock_area+' [';
            html += value.product_id+']<br />'+value.name+'<br />';
            if (product_text) {
                html += '<span '+order_been_over_size+' id="alert_'+value.product_id+'">'+product_text+'</span>';
            } else {
                html += '<span '+order_been_over_size+' id="alert_'+value.product_id+'">'+$("#alert_"+value.product_id).text()+'</span>';
            }
            html += '</th><th '+order_been_over+' class="product_lists'+value.container_id+'" id="container'+value.product_id+'" >';
            html += value.plan_quantity+'</th>';
            html += '<th '+order_been_over+' class="containerlist" id="containerl'+value.product_id+'" ><span class="num1_lists" id="containero'+value.product_id+'">';
            html += sorting_quantity+'</th>';
            html += '<th '+order_been_over_size+' ><span id="num_'+value.product_id+'">'+num1+'</span>';
            if (num1 > plan_quantity) {
                html += '<br />超出订单数量';
            }
            html += '</th>';
            html += '</tr>';
            window.product_arr[product_id] = product_information;
            window.product_do_num += (num1 - num2);
            $("#check_do_quantity").html(window.product_do_num+'箱');
            submitProduct(plan_quantity,sorting_quantity,num1,product_id,0);
            /*周转筐*/
        } else if (product_type == 2) {
            if (window.local_container == 0) {
                alert("请先选择周转筐号，再扫描商品");
                return false;
            }
            if (typeof(global.addTranseBoxList[window.local_container]) == "undefined") {
                var container_products = global.transferringBoxes;
                var value = container_products[product_id+"@"+window.local_container];
            } else {
                var container_products = global.addTranseBoxList[window.local_container];
                if (typeof(container_products[product_id]) == "undefined") {
                    var container_products = global.transferringBoxes;
                    var value = container_products[product_id+"@"+window.local_container];
                } else {
                    var value = container_products[product_id];
                }
            }
            // console.log(value);
            // console.log(container_products);
            var sorting_quantity = value.sorting_quantity == '' ? 0 : parseInt(value.sorting_quantity);
            var plan_quantity = value.plan_quantity == '' ? 0 : parseInt(value.plan_quantity);
            var container_product = typeof(window.product_arr[product_id]) == "undefined" ? 0 : parseInt(window.product_arr[product_id]);
            var do_product_num = 0;
            $.each(window.product_arr,function(index,value2) {
                var container_product_id = index.split('@');
                if (container_product_id[0] == product_id && container_product_id[1] != window.local_container) {
                    do_product_num += parseInt(value2.quantity);
                }
            });
            if ((do_product_num+num1) > plan_quantity) {
                order_been_over_size = "style = 'color:red'";
                // product_information['num'] = plan_quantity;
            } else {
                // product_information['num'] = num1;
            }
            html += '<tr class="container'+value.container_id+'" id="bd'+ value.product_id +'">';
            html += '<th '+order_been_over+' class="containerlist" id="containern'+value.product_id+'">'+value.stock_area+' [';
            html += value.product_id+']<br />'+value.name+'<br />';
            if (product_text) {
                // console.log(typeof(product_text));
                html += '<span '+order_been_over_size+' id="alert_'+value.product_id+'">'+product_text+'</span>';
            } else {
                // console.log(typeof($("#alert_"+value.product_id).text()));
                if (typeof($("#alert_"+value.product_id).text()) != 'undefined') {
                    html += '<span '+order_been_over_size+' id="alert_'+value.product_id+'">'+$("#alert_"+value.product_id).text()+'</span>';
                }
            }
            html += '</th><th '+order_been_over_size+' class="product_lists'+value.container_id+'" id="container'+value.product_id+'" >';
            html += parseInt(value.plan_quantity)+'<br />共核'+(do_product_num+num1)+'件</th>';
            html += '<th '+order_been_over+' class="containerlist" id="containerl'+value.product_id+'" ><span class="num1_lists" id="num_containero'+value.product_id+'">';
            html += sorting_quantity+'</th>';
            html += '<th '+order_been_over_size+'><span id="num_'+value.product_id+'">'+num1+'</span>';
            if ((do_product_num+num1) > plan_quantity) {
                html += '<br />超出订单数量';
            }
            html += '</th>';
            html += '</tr>';
            product_information['quantity'] = num1;
            window.product_arr[product_id+"@"+window.local_container] = product_information;
            submitProduct(plan_quantity,sorting_quantity,num1,product_id,window.local_container);
        }

        if (!isNaN(num2)) {
            $("#bd"+product_id).remove();
        }
        $('#productsInfo').append(html);
        $("#productsInfoDo1").html('');
        var get_result = window.get_product_result;
        var result = false;
        if (JSON.stringify(get_result) != "{}") {
            getCheckOrderInformation(product_type);
        }
    }
    /*zx
    *临时表提交 */
    function submitProduct(plan_quantity,sorting_quantity,num,product_id,container_id) {
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var added_by = '<?php echo $_COOKIE['inventory_user_id'];?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id'];?>';
        var order_information = global.order_detail_information;
        var product_type = order_information.product_type;
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'submitCheckProductInformation',
                data:{
                    check_location_id: order_information['check_location_id'],
                    order_type: order_information['type'],
                    warehouse_id: warehouse_id,
                    product_id: product_id,
                    container_id: container_id,
                    plan_quantity: plan_quantity,
                    sorting_quantity: sorting_quantity,
                    product_num: num,
                    added_by: added_by,
                    user_group_id: user_group_id,
                }

            },
            success:function(response){
                if(response){
                    var jsonData = $.parseJSON(response);
                    if (jsonData.return_code == "ERROR") {
                        alert(jsonData.return_msg);
                    } else if (jsonData.return_code == "SUCCESS") {
                        if (order_information['type'] == 0) {
                            getOrderSortingList(order_information['order_id'],0,product_type);
                        } else if (order_information['type'] == 1) {
                            getOrderSortingList(order_information['deliver_order_id'],1,product_type);
                        }
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
            var deliver_order_id = [];
            $('.relevantlist').each(function(){
                deliver_order_id.push(parseInt($(this).text()));
            });
            var warehouse_out_type = parseInt($('#warehouse_out_type').val());
            /*zx
            * 退货调拨单出库删除*/
            if (container_id == 1) {
                $("#bd"+product_id).remove();
                var player = $("#player3")[0];
                player.play();
            } else {
                $('#productsInfo2').html('');
                var player = $("#player3")[0];
                player.play();
            }

        }
        hideOverlay();
    }

    function goWindowUrl(file,param,id) {
        var localtion_url = file+".php?auth=xsj2015inv&ver=db";
        if (param) {
            param = param.split('@');
            id = id.split('@');
            for (var item in param) {
                localtion_url += "&"+param[item]+'='+id[item];
            }
        }
        window.location = localtion_url;
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
                    window.location = 'inventory_login.php?return=make_order_checking_log.php';
                }
            });
        }
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

                                  

















