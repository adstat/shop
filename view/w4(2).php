<?php
//exit('服务器更新中，请稍候...');

if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';

$inventory_user_admin = array('1','22');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
$time_plan =  strtotime('2018-7-16');
$time_now = time();
//设定日期
$date = date('Y-m-d');
$dateList = array();
$tomorrow = date("Y-m-d", strtotime("1 day"));
$dateList[] = date("Y-m-d", strtotime("1 day"));
$dateList[] = date("Y-m-d", strtotime("0 day"));
$dateList[] = date("Y-m-d", strtotime("-1 day"));
$dateList[] = date("Y-m-d", strtotime("-2 day"));
$dateList[] = date("Y-m-d", strtotime("-3 day"));
$dateList[] = date("Y-m-d", strtotime("-4 day"));
$dateList[] = date("Y-m-d", strtotime("-5 day"));
$dateList[] = date("Y-m-d", strtotime("-6 day"));
$dateList[] = date("Y-m-d", strtotime("-7 day"));
$dateList[] = date("Y-m-d", strtotime("-8 day"));
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>鲜世纪订单分拣页面</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap4/js/bootstrap.min.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap4/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="view/javascript/bootstrap4/css/bootstrap.min.css"></link>
<link rel="stylesheet" href="view/javascript/bootstrap4/css/bootstrap-grid.min.css"></link>
<!--<link rel="stylesheet" href="view/javascript/bootstrap4/css/bootstrap-reboot.min.css"></link>-->
<link rel="stylesheet" href="view/javascript/bootstrap4/css/font-awesome.min.css"></link>
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
    /*-webkit-appearance: none;*/
    outline: none;
}
table {
     border-collapse:separate;
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
    global.go_warehouse_id = 0;
    global.check_is_repack = 0;
    global.resJson = '';
    global.resStr = '';
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
<div align="center" style="display:block; margin:0.3rem auto" id="logo"><img src="view/image/logo.png" style="width:6em"/> 订单分拣<button class="invopt style_gray" id="reload" style="display: inline" onclick="reloadPage();" disabled="disabled">载入中...</button>
    <button class="invopt" style="display: inline;float:left" onclick="window.location.href='auto_dis.php?auth=xsj2015inv&ver=db'">领单</button>
</div>
<div align="center" id="is_need_merge_order"></div>

<div id="show_select_info" style="display: ;">

<?php if(in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
    订单状态:
    <select id="orderStatus" style="width:12em;height:2em;">

    </select>
    <button style="font-size:1.1rem; display: none" onclick="javascript:getOrderByStatus()">查询(停用)</button>
    <button style="font-size:1.1rem;" onclick="javascript:getOrders($('#orderStatus').val())">查询</button>




    <div style="display: none">
        订单分类:
        <select id="orderStation" style="margin-top: 0.2em;width:12em;height:2em;">
            <option value="0">全部</option>
            <option value="1">生鲜(包装菜、奶制品)</option>
            <option value="2">快销品</option>
        </select>
    </div>

    <br />
<?php } ?>
<div id="show_date">
    配送日期：
    <select id="deliver_date" style="margin-top: 0.2em;width:12em;height:2em;">
        <option value="0">-请选择-</option>
        <?php foreach($dateList as $m){ ?>
            <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
        <?php } ?>
    </select>
</div>




<div id="login" align="center" style="margin:0.5em auto; display: none">
    <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
    <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
</div>

<div id="content" style="display: block">
<div align="center" id="orderListTable" style="margin:0.5em auto; font-size: 0.8rem">
    <div style="margin:0.3rem; padding:0.3rem; color:#5e5e5e; font-style: italic; background-color: #d6d6d6; border: 1px dashed #5e5e5e;">
        <button class="invopt getOrdersFilter style_gray" id="getOrders_2" style="display: inline; margin: 0.1rem" onclick="$('#deliver_date').val(0); getOrders(2);">待分拣</button>
        <button class="invopt getOrdersFilter style_gray" id="getOrders_5" style="display: inline; margin: 0.1rem" onclick="$('#deliver_date').val(0); getOrders(5);">分拣中</button>
        <button class="invopt getOrdersFilter style_gray" id="getOrders_8" style="display: inline; margin: 0.1rem" onclick="getOrders(8);">待审核</button>
        <!--        --><?php //if($_COOKIE['warehouse_id'] == 14 && ($_COOKIE['user_group_id'] ==  1 || $_COOKIE['user_group_id'] ==22 )) {  ?>
        <!--            <button class="invopt getOrdersFilter style_gray" id="getOrders_500" style="display: inline; margin: 0.1rem" onclick="getOrders(500);">待审核整件缺货</button>-->
        <!--        <button class="invopt getOrdersFilter style_gray" id="getOrders_600" style="display: inline; margin: 0.1rem" onclick="getOrders(600);">待审核散件缺货</button>-->
        <!---->
        <!--       <?php // }?>  -->
        <button class="invopt getOrdersFilter style_gray" id="getOrders_6" style="display: inline; margin: 0.1rem" onclick="getOrders(6);">已拣完</button>
        <button class="invopt getOrdersFilter style_gray" id="getOrders_12" style="display: inline; margin: 0.1rem" onclick="getOrders(12);">待核单</button>
        <button class="invopt getOrdersFilter style_gray" id="getOrders_777" style="display: inline; margin: 0.1rem" onclick="getOrders(777);">加急</button>
        <?php  if ($_COOKIE['warehouse_id']  ==  12 || !empty($_COOKIE['warehouse_is_dc'])) { ?>
            <button class="invopt getOrdersFilter style_gray" id="getOrders_666" style="display: inline; margin: 0.1rem" onclick="getOrders(666);">苏州单</button>
            <button class="invopt getOrdersFilter style_gray" id="getOrders_888" style="display: inline; margin: 0.1rem" onclick="getOrders(888);">浦西单</button>
        <?php } ?>
        <?php  if ( ($_COOKIE['warehouse_id']  ==  18 || $_COOKIE['warehouse_id']  ==  16 || $_COOKIE['warehouse_id']  ==  15 || $_COOKIE['warehouse_id']  ==  17 ) && (in_array($_COOKIE['user_group_id'], $inventory_user_admin)) ) { ?>
            <button class="invopt getOrdersFilter style_gray" id="getOrders_999" style="display: inline; margin: 0.1rem" onclick="getOrders(999);">异地浦西出库</button>
            <button class="invopt getOrdersFilter style_gray" id="getOrders_555" style="display: inline; margin: 0.1rem" onclick="getOrders(555);">本仓拣完浦西未到货</button>
        <?php } ?>
        <button class="invopt getOrdersFilter style_gray style_green" id="getOrders_0" style="display: inline; margin: 0.1rem" onclick="getOrders(0);">全部</button>

    </div>
    <input type="hidden" id="current_order_id" value="">
    <table border="0" style="width:100%;" cellpadding="2" cellspacing="3" id="ordersHold">
        <thead>
        <tr>
            <th>订单ID</th>
            <th style="display: none">信息</th>
            <th>商品数</th>
            <th>未分拣</th>
            <th>分拣人</th>
            <th>订单状态</th>
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
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div id="fastmove_order_frame" class="input-group  input-group-md">
                    <select onchange="switchSelect(this.value)" id="setType" class="custom-select-md">
                        <option value="1" selected>扫描</option>
                        <option value="2">手输</option>
                    </select>
                    <input type="hidden" class="fm_frame_vg_list" />
                    <input  name="product" id="frame_vg_list" type="text" class="form-control col-xs-8" placeholder="请输入商品或条码" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
                    <div class="input-group-append  col-xs-3">
                        <button id="putSwitch" onclick="putSwitch()"   class="btn btn-outline-secondary col-xs-2" type="button">提交</button>
                    </div>
                </div>
                <div class="row col-auto" id="show_product_msg"></div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function(){
        $("input[name='product']").on('keyup',function(){
            var tmptxt=$(this).val();
            if(tmptxt.length >= 4){
                putSwitch();
                $(this).val("");
            } else {
                $(this).val("");
            }

        });

    });


    function switchSelect(val){
        // alert(val);
        if (val == 1) {//扫描
            $("input[name='product']").on('keyup',function(){
                // var setType=$.trim($('#setType option:selected').val());
                // switchSelect(setType);
                var tmptxt=$(this).val();
                if(tmptxt.length < 4){
                    // handleProductList();
                    $(this).val("");
                }
            });
            scanFunc();
        }
        if (val == 2) {//手动
            $("input[name='product']").off("keyup");

            manualFunc();
        }
    }
    function scanFunc(){
        $("input[name='product']").keyup(function(){
            var tmptxt=$(this).val();
            if(tmptxt.length >= 4){
                // handleProductList();
            }
            $(this).val("");
        });
    }
    function manualFunc(){
        $("input[name='product']").mouseleave(function(){
            var tmptxt=$(this).val();
            if(tmptxt.length < 4){
                showAlertError('#getMsg','商品或条码不能少于四位','请重新输入!');
            }

            // $(this).val("");
        });
    }
</script>




<!-- 分拣完成后台提交 -->
<div id="fastmove_order_comment" style="margin: 1rem 0 2.5rem 0; padding: 0.5rem; display: none; background-color: #c9e2b3;">
    <span id="sttttt"></span>
    <input type="hidden" id="box_count" class="fm_box_count" value="0" readonly="readonly" />
    <div style="margin: 0.5rem 0; font-weight: bold;">已分拣整件数: <span id="nonRepackBoxCount" style="font-size: 1.2rem">0</span></div>
    <div>所属仓库：<span style="background: red" id = 'to_warehouse_title'></span></div>
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


<div id="getMsg"></div>


<hr />
<!--选项卡-->
<div class="container-fluid">
    <div class="row">
        <div id="show_nav_tab" class="table-responsive-xs">
            <ul class="nav nav-pills nav-fill" id="myTab" role="tablist">

            </ul>
            <div class="tab-content" id="myTabContent">

            </div>
        </div>
    </div>
</div>
<!--选项卡结束-->
<!--折叠id="productsHold"-->
    <div class="container-fluid">
        <div class="row">
            <a type="button" id="foldList"
               class="btn btn-outline-info btn-sm btn-block">
                <i class="icon-th-list"></i>折叠列表
            </a>
        </div>
    </div>
<!--折叠结束-->
    <input type="hidden" id="current_do_product" value="0">
<div style="display:block; margin-top: 1em;">
    <span style=" font-size: 1.2em;">订单商品信息</span>
    <span style="float:right;font-size: 1em; line-height: 1.8em;">共<span id="count_plan_quantity"></span>件,待完成<span id="count_quantity"></span>件</span>
<!--    <div id="vg_list" class="fm_vg_list"></div>-->
    <div>
        <input class="submit" type="button"  style="display: none" id = "inventory_order_id" value="" onclick="javascript:getOrderSortingList(this.value ,0,2);">
        <input class="submit" type="button"  style="display: none"  id = "inventory_order_id_view" value="" onclick="javascript:getOrderSortingList(this.value ,1,2);">

    </div>
</div>

<!--    <div  style="font-size: 1.2em; width:90%;" id = 'frame_button_vg_list' ></div>-->
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

<?php if(in_array($_COOKIE['user_group_id'],$inventory_user_admin)){ ?>
    <input class="submit classSubmitSortingPendingCheck"  type="button" value="提交分拣完成" onclick="javascript:showSubmitSorting();">
<!--    <input class="submit classShowSortingInvComment" type="button" value="显示周转筐" onclick="javascript:showSubmitFrame()">-->

    <input class="submit classRemoveSortingData" type="button" value="删除分拣数据" onclick="javascript:delOrderProductToInv();">
    <input class="submit classSubmitSorting"  id = 'classSubmitSorting' type="button" value="直接提交已拣完" onclick="javascript:addOrderProductToInv_pre();">
<!--                --><?php // if ($_COOKIE['warehouse_id']  ==  15 || $_COOKIE['warehouse_id']  ==  16  || $_COOKIE['warehouse_id']  ==  17 ) { ?>
<!--        <input class="submit classSubmitSortingRelevant"  id = 'classSubmitSortingRelevant' type="button" value="提交合单" onclick="javascript:addConsolidatedRelevant();">-->
<!--    --><?php //} ?>
<!--    --><?php // if ($_COOKIE['warehouse_id']  ==  15 ||$_COOKIE['warehouse_id']  ==  16 ||$_COOKIE['warehouse_id']  ==  17) { ?>
<!--        <input class="submit classUpdateDoStatus"  id = 'classUpdateDoStatus' type="button" value="浦西没到货修改订单状态并记录" onclick="javascript:updateDoStatus();">-->
<!--    --><?php //} ?>
<i class="icon-arrow-up">回到顶部</i>
<?php } else{ ?>
    <a type="button" name="" id="" href="#"
            class="btn btn-outline-success btn-lg btn-block">
        <i class="icon-arrow-up">回到顶部</i>
    </a>
    <input class="submit classSubmitSortingPendingCheck" type="button" value="提交分拣完成" onclick="javascript:showSubmitSorting();">
<!--    <input class="submit classShowSortingInvComment" type="button" value="显示周转筐" onclick="javascript:showSubmitFrame()">-->
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

    // $("input[name='input_vg_frame']").keyup(function(){
    //     var tmptxt=$(this).val();
    //     //$(this).val(tmptxt.replace(/\D|^0/g,''));
    //     if(tmptxt.length >= 6){
    //         var frameContainerNumber =  tmptxt.substr(0,6);
    //         if(!/^[1-9]\d{0,5}$/.test(frameContainerNumber.trim())){
    //             alert("请输入数字格式周转筐号");
    //             $(this).val("");
    //             return false;
    //         }
    //
    //         handleFrameList('vg');
    //         $(this).val("");
    //     }
    // }).bind("paste",function(){
    //         var tmptxt=$(this).val();
    //         $(this).val(tmptxt.replace(/\D|^0/g,''));
    //     });

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
<br/>
<br/>
<br/>
<br/>
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
function check_in_right_time(){

    //var start_time = '<?php //echo (strtotime(date("Y-m-d"),time())+$start_time);?>//';
    //var end_time = '<?php //echo ((strtotime(date("Y-m-d"),time())+$end_time));?>//';
    //var now_time = '<?php //echo time();?>//';
    // console.log(start_time);
    // console.log(end_time);
    // console.log(now_time);
    // if (start_time <= now_time && now_time <= end_time) {
    //     alert("系统正在备份，为防止数据丢失，请于2：20之后再操作");
    //     return true;
    // } else {
    //     return false;
    // }
}
function hideNum(str,count){
    var strLen = str.length;

    return str.substring(0,count) + '****' + str.substring(strLen-count,strLen);
}

var sound = soundEffectInit();
$(document).ready(function(){
    $("#frame_vg_list").val("");
    $('#orderStation').change(function(){

        var p1=$(this).children('option:selected').val();//这就是selected的值
        $('#ordersList tr').each(function () {
            if(p1 == 0){
                $(this).show();
            }
            else{
                if(p1 == $(this).attr("station_id")){
                    $(this).show();
                }
                else{
                    $(this).hide();
                }
            }
        });
    });
})

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


    $.ajax({
        type : 'POST',
        url : 'invapi.php?vali_user=1',
        data : {
            method : 'getInventoryUserOrder',
            warehouse_id : warehouse_id,
            date : '<?php echo $date_array[2]['date']; ?>',

        },
        success : function (response , status , xhr){
            //console.log(response);

            if(response){
                var jsonData = $.parseJSON(response);
                if(jsonData.status == 999){
                    alert("未登录，请登录后操作");
                    window.location = 'inventory_login.php?return=w4.php';
                }

                window.inventory_user_order = jsonData;
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
    <?php if(in_array($_COOKIE['user_group_id'],$inventory_user_admin)){ ?>
    getOrders(8);
    <?php } else{ ?>
    getOrders(2);
    <?php } ?>
});

function handelFilter(order_status_id){
    var choosedFilter = '#getOrders_'+order_status_id;

    $(".getOrdersFilter").removeClass('style_green');
    $(".getOrdersFilter").removeClass('style_gray');
    $(".getOrdersFilter").addClass('style_gray');

    $(choosedFilter).removeClass('style_green');
    $(choosedFilter).removeClass('style_gray');
    $(choosedFilter).addClass('style_green');
}

function getOrders(order_status_id){

    if(order_status_id == undefined){
        order_status_id = 0;
    }

    handelFilter(order_status_id);

    var warehouse_id = $("#warehouse_id").text();
    var warehouse_repack = 0;
    var user_repack = 0 ;
    var inventoryUser = 0;
    if(is_admin == 0){
        inventoryUser = '<?php echo $_COOKIE['inventory_user'];?>';
        warehouse_repack ='<?php echo $_COOKIE['warehouse_repack'];?>';
        user_repack = '<?php echo $_COOKIE['user_repack'];?>';
    }

    //设定查询配送日期
    var deliver_date = '<?php echo $date_array[2]['date']; ?>';
    var specDeliverDate = $("#deliver_date").val();
    if(parseInt(specDeliverDate) > 0){
        deliver_date = specDeliverDate;
    }


    //Get RegMethod
    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=getOrders',
        data : {
            method : 'getOrders',
            station_id: 2,
            inventory_user: inventoryUser,
            warehouse_id :warehouse_id,
            order_status_id : order_status_id,
            date : deliver_date,
            warehouse_repack : warehouse_repack,
            user_repack : user_repack
        },
        success : function (response , status , xhr){
            //console.log(response);

            if(response){
                //释放刷新按钮
                $('#reload').attr('class',"invopt");
                $('#reload').removeAttr('disabled');
                $('#reload').html("刷新");

                var jsonData = $.parseJSON(response);



                if(jsonData.status == 999){
                    alert(jsonData.msg);
                    location.href = "inventory_login.php?return=w4.php";
                }

                var html = '';

                var each_i_num = 1;
                var each_i_num_new = 0;
                var each_i_num_kuai = 501;
                var each_i_num_veg = 0;
                var num_flag = true;
                if (order_status_id == 999) {
                    console.log(jsonData);

                    $.each(jsonData.data2, function (index1, value1) {
                        var jsonDataData2 = jsonData.data;
                        var new_deliver_order_id = '2'+value1['order_id'];
                        var value = jsonDataData2[new_deliver_order_id];
                        console.log(value);
                        console.log(jsonDataData2);
                        console.log(new_deliver_order_id);
                        var planQty = parseInt(value.plan_quantity);
                        var dueQty = parseInt(value.quantity);


                        //console.log("#1订单:" + value.order_id + ", 商品数:" + value.plan_quantity + ", 未分拣:" + value.quantity)

                        if (value.station_id == 2 && num_flag) {
                            num_flag = false;
                            each_i_num_veg = each_i_num;
                            each_i_num_new = each_i_num;
                        }

                        if (value.station_id == 1) {
                            return true;
                        }

                        if (window.inventory_user_order[value.order_id] || <?php echo !in_array($_COOKIE['user_group_id'], $inventory_user_admin) ? 0 : 1;?> ) {
                            var t_status_class = '';
                            var product_str = '';


                            if (value.order_product_type == 1) {
                                product_str = '菜';
                            }
                            if (value.order_product_type == 2) {
                                t_status_class = "style = 'background-color:#ffff00;'";
                                product_str = '菜+奶';
                            }
                            if (value.order_product_type == 3 && value.station_id == 1) {
                                t_status_class = "style = 'background-color:#9933ff;'";
                                product_str = '奶';
                            }


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

                            html += '<td ' + t_status_class + '>';
                            if (value.is_urgent == 1) {
                                html += '<span style="color:red;">[加急]</span><br>';
                            }
                            html += '<span style="color:red;" id="warehouse_id' + value.order_id + '" value="' + value.shortname + '">' + value.shortname + '</span><br>';
                            html += '<span style="font-weight: bold; font-size: 1.1rem;">' + value.order_id + '</span>';
                            if (parseInt(value.inv_comment) > 0) {
                                html += '<br /><span style="color:darkgreen;font-weight: bold; font-size: 0.8rem;">[货位' + value.inv_comment + ']</span>';
                            }

                            if (value.doInfo != '') {
                                html += '<br /><br /><span style="color:darkgreen; font-size: 0.8rem;">合单 ' + value.doInfo + '</span>';
                            }

                            html += '<br /><br /><span style="color:red; font-size: 0.8rem;">订单' + value.so_order_id + '</span>';
                            if (parseInt(value.station_id) == 2 && value.district != '') {
                                html += '<br /><span style="color:red; font-size: 0.8rem;">' + value.area_name + '</span>';
                            }
                            if (value.is_nopricetag == 1) {
                                html += '<br /><span style="color:red;">无价签</span>';
                            }


                            html += '<input type="hidden" id="shipping_name_' + value.order_id + '" value="' + value.shipping_name + '"><input type="hidden" id="shipping_phone_' + value.order_id + '" value="' + value.shipping_phone + '"><input type="hidden" id="shipping_address_' + value.order_id + '" value="' + value.shipping_address_1 + '"></td>';

                            html += '<td style="display:none">';

                            if (value.is_bao == 1) {
                                html += '<span style="color:red;">爆</span><br>';
                            }
                            if (value.station_id == 2) {

                                each_i_num = each_i_num_kuai + (each_i_num_new - each_i_num_veg);

                                //html += '<span style="color:red;">快</span><br />';
                            }

                            if (value.group_id > 1 && parseInt(value.station_id) == 2) {
                                //html += '<span style="color:red; font-size:0.9rem">'+value.group_shortname+'</span><br />';
                            }

                            html += '<input type="hidden" id="order_frame_count_' + value.order_id + '" value="' + value.frame_count + '">';
                            html += '<input type="hidden" id="order_incubator_count_' + value.order_id + '" value="' + value.incubator_count + '">';
                            html += '<input type="hidden" id="order_foam_count_' + value.order_id + '" value="' + value.foam_count + '">';
                            html += '<input type="hidden" id="order_frame_mi_count_' + value.order_id + '" value="' + value.frame_mi_count + '">';
                            html += '<input type="hidden" id="order_incubator_mi_count_' + value.order_id + '" value="' + value.incubator_mi_count + '">';
                            html += '<input type="hidden" id="order_frame_ice_count_' + value.order_id + '" value="' + value.frame_ice_count + '">';
                            html += '<input type="hidden" id="order_box_count_' + value.order_id + '" value="' + value.box_count + '">';

                            html += '<input type="hidden" id="order_frame_meat_count_' + value.order_id + '" value="' + value.frame_meat_count + '">';
                            html += '<input type="hidden" id="order_frame_vg_list_' + value.order_id + '" value="' + value.frame_vg_list + '">';
                            html += '<input type="hidden" id="order_frame_vg_product_' + value.order_id + '" value="' + value.order_container + '">';
                            html += '<input type="hidden" id="order_frame_meat_list_' + value.order_id + '" value="' + value.frame_meat_list + '">';
                            html += '<input type="hidden" id="order_frame_mi_list_' + value.order_id + '" value="' + value.frame_mi_list + '">';
                            html += '<input type="hidden" id="order_frame_ice_list_' + value.order_id + '" value="' + value.frame_ice_list + '">';

                            html += '<input type="hidden" id="order_foam_ice_count_' + value.order_id + '" value="' + value.foam_ice_count + '">';

                            html += '<input type="hidden" id="order_inv_comment_' + value.order_id + '" value="' + value.inv_comment + '">';


                            html += '<input type="hidden" id="order_inv_spare_comment_' + value.order_id + '" value="' + value.inv_spare_comment + '">';


                            html += '<input type="hidden" id="order_each_num_' + value.order_id + '" value="' + each_i_num + '">';

                            html += '<span style="display: none" id="order_num_' + value.order_id + '">' + each_i_num + '</span><br>' + product_str + '</td>';

                            html += '<td ' + t_status_class + '>' + planQty + '</td>';
                            html += '<td ' + t_status_class + '>' + dueQty + '</td>';

                            html += '<td ' + t_status_class + '>' + value.added_by + '</td>';


                            html += '<td ' + t_status_class + '>';
                            if (parseInt(value.order_count)>1) {
                                html += '<span style="color:red;">需要合单</span><br />';
                            }
                            html += value.name;
                            html += '<input type="hidden" class="soringOrderStatus" value="' + value.order_status_id + '"></td>';


                            html += '<td ' + t_status_class + '><button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderInventoryView(' + value.order_id + ',' + value.station_id + ');">查看</button>';

                            //当订单状态为2（已确认）或5（分拣中），可用开始分拣，已有在分拣订单其他不显示
                            //散整分拣


//                                    html += '<button id="inventoryIn" class="invopt orderStartSortingButton" style="display: inline" onclick="javascript:orderInventory(' + value.order_id + ',' + value.station_id + ');">开始</button>';


                            /*
                             if(is_admin == 1){
                             html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+');">提交分拣</button>';
                             }
                             */
                            html += '</td>';
                            html += '</tr>';
                            html += '';
                        }
                        each_i_num++;
                        if (each_i_num_new != 0) {
                            each_i_num_new++;
                        }
                    });
                } else {
                    $.each(jsonData.data, function (index, value) {

                        //console.log(value.plan_quantity);


                        var planQty = parseInt(value.plan_quantity);
                        var dueQty = parseInt(value.quantity);


                        //console.log("#2订单:" + value.order_id + ", 商品数:" + value.plan_quantity + ", 未分拣:" + value.quantity)

                        if (value.station_id == 2 && num_flag) {
                            num_flag = false;
                            each_i_num_veg = each_i_num;
                            each_i_num_new = each_i_num;
                        }

                        if (value.station_id == 1) {
                            return true;
                        }

                        if (inventoryUser || <?php echo !in_array($_COOKIE['user_group_id'], $inventory_user_admin) ? 0 : 1;?> ) {
                            var t_status_class = '';
                            var product_str = '';


                            if (value.order_product_type == 1) {
                                product_str = '菜';
                            }
                            if (value.order_product_type == 2) {
                                t_status_class = "style = 'background-color:#ffff00;'";
                                product_str = '菜+奶';
                            }
                            if (value.order_product_type == 3 && value.station_id == 1) {
                                t_status_class = "style = 'background-color:#9933ff;'";
                                product_str = '奶';
                            }


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

                            html += '<td ' + t_status_class + '>';
                            if (value.is_urgent == 1) {
                                html += '<span style="color:red;">[加急]</span><br>';
                            }
                            html += '<span style="color:red;" id="warehouse_id' + value.order_id + '" value="' + value.shortname + '">' + value.shortname + '</span><br>';
                            html += '<span style="font-weight: bold; font-size: 1.1rem;">' + value.order_id + '</span>';
                            if (parseInt(value.inv_comment) > 0) {
                                html += '<br /><span style="color:darkgreen;font-weight: bold; font-size: 0.8rem;">[货位' + value.inv_comment + ']</span>';
                            }

                            if (value.doInfo != '') {
                                html += '<br /><br /><span style="color:darkgreen; font-size: 0.8rem;">合单 ' + value.doInfo + '</span>';
                            }

                            html += '<br /><br /><span style="color:red; font-size: 0.8rem;">订单' + value.so_order_id + '</span>';
                            if (parseInt(value.station_id) == 2 && value.district != '') {
                                html += '<br /><span style="color:red; font-size: 0.8rem;">' + value.area_name + '</span>';
                            }
                            if (value.is_nopricetag == 1) {
                                html += '<br /><span style="color:red;">无价签</span>';
                            }


                            html += '<input type="hidden" id="shipping_name_' + value.order_id + '" value="' + value.shipping_name + '"><input type="hidden" id="shipping_phone_' + value.order_id + '" value="' + value.shipping_phone + '"><input type="hidden" id="shipping_address_' + value.order_id + '" value="' + value.shipping_address_1 + '"></td>';

                            html += '<td style="display:none">';

                            if (value.is_bao == 1) {
                                html += '<span style="color:red;">爆</span><br>';
                            }
                            if (value.station_id == 2) {

                                each_i_num = each_i_num_kuai + (each_i_num_new - each_i_num_veg);

                                //html += '<span style="color:red;">快</span><br />';
                            }

                            if (value.group_id > 1 && parseInt(value.station_id) == 2) {
                                //html += '<span style="color:red; font-size:0.9rem">'+value.group_shortname+'</span><br />';
                            }

                            html += '<input type="hidden" id="order_frame_count_' + value.order_id + '" value="' + value.frame_count + '">';
                            html += '<input type="hidden" id="order_incubator_count_' + value.order_id + '" value="' + value.incubator_count + '">';
                            html += '<input type="hidden" id="order_foam_count_' + value.order_id + '" value="' + value.foam_count + '">';
                            html += '<input type="hidden" id="order_frame_mi_count_' + value.order_id + '" value="' + value.frame_mi_count + '">';
                            html += '<input type="hidden" id="order_incubator_mi_count_' + value.order_id + '" value="' + value.incubator_mi_count + '">';
                            html += '<input type="hidden" id="order_frame_ice_count_' + value.order_id + '" value="' + value.frame_ice_count + '">';
                            html += '<input type="hidden" id="order_box_count_' + value.order_id + '" value="' + value.box_count + '">';

                            html += '<input type="hidden" id="order_frame_meat_count_' + value.order_id + '" value="' + value.frame_meat_count + '">';
                            html += '<input type="hidden" id="order_frame_vg_list_' + value.order_id + '" value="' + value.frame_vg_list + '">';
                            html += '<input type="hidden" id="order_frame_vg_product_' + value.order_id + '" value="' + value.order_container + '">';
                            html += '<input type="hidden" id="order_frame_meat_list_' + value.order_id + '" value="' + value.frame_meat_list + '">';
                            html += '<input type="hidden" id="order_frame_mi_list_' + value.order_id + '" value="' + value.frame_mi_list + '">';
                            html += '<input type="hidden" id="order_frame_ice_list_' + value.order_id + '" value="' + value.frame_ice_list + '">';

                            html += '<input type="hidden" id="order_foam_ice_count_' + value.order_id + '" value="' + value.foam_ice_count + '">';

                            html += '<input type="hidden" id="order_inv_comment_' + value.order_id + '" value="' + value.inv_comment + '">';


                            html += '<input type="hidden" id="order_inv_spare_comment_' + value.order_id + '" value="' + value.inv_spare_comment + '">';


                            html += '<input type="hidden" id="order_each_num_' + value.order_id + '" value="' + each_i_num + '">';

                            html += '<span style="display: none" id="order_num_' + value.order_id + '">' + each_i_num + '</span><br>' + product_str + '</td>';

                            html += '<td ' + t_status_class + '>' + planQty + '</td>';
                            html += '<td ' + t_status_class + '>' + dueQty + '</td>';

                            html += '<td ' + t_status_class + '>' + value.added_by + '</td>';


                            html += '<td ' + t_status_class + '>';
                            if (parseInt(value.order_count)>1) {
                                html += '<span style="color:red;">需要合单</span><br />';
                            }
                            html += value.name;
                            html += '<input type="hidden" id="soringOrderStatus'+value.order_id+'" class="soringOrderStatus" value="' + value.order_status_id + '"></td>';


                            html += '<td ' + t_status_class + '><button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderInventoryView(' + value.order_id + ',' + value.station_id + ');">查看</button>';

                            //当订单状态为2（已确认）或5（分拣中），可用开始分拣，已有在分拣订单其他不显示
                            //散整分拣


                            if ((value.order_status_id == 2 || value.order_status_id == 5 || value.order_status_id == 4) && value.no_inv != 1) {
                                html += '<button id="inventoryIn" class="invopt orderStartSortingButton" style="display: inline" onclick="javascript:orderInventory(' + value.order_id + ',' + value.station_id + ');">开始</button>';
                            }


                            /*
                             if(is_admin == 1){
                             html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+');">提交分拣</button>';
                             }
                             */
                            html += '</td>';
                            html += '</tr>';
                            html += '';
                        }
                        each_i_num++;
                        if (each_i_num_new != 0) {
                            each_i_num_new++;
                        }
                    });
                }
                $('#ordersList').html(html);



                //隐藏分拣按钮，遍历待分拣订单，如有状态5（分拣中），显示分拣按钮，如果没有状态5（分拣中），全部显示。

                var orderOnSorting = false;
                $('.orderStartSortingButton').hide();
                $.each($('#ordersList tr'), function (n, v) {
                    var thisOrderStatus = $(this).find(".soringOrderStatus").val();
                    if (thisOrderStatus == 5 && !orderOnSorting) {
                        orderOnSorting = true;
                        $(this).find(".orderStartSortingButton").show();
                    }

                });
                if (!orderOnSorting) {
                    $('.orderStartSortingButton').show();
                }
            }
        },
        complete : function(){
            $.ajax({
                type : 'POST',
                url : 'invapi.php?vali_user=1',
                data : {
                    method : 'getOrderStatus'
                },
                success : function (response , status , xhr){
                    //console.log(response);
                    if(response){
                        var jsonData = eval(response);
                        if(jsonData.status == 999){
                            alert("未登录，请登录后操作");
                            window.location = 'inventory_login.php?return=w4.php';
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

            $.ajax({
                type : 'POST',
                url : 'invapi.php?vali_user=1',
                data : {
                    method : 'getProductWeightInfo',
                    date : '<?php echo $date_array[2]['date']; ?>'

                },
                success : function (response , status , xhr){
                    //console.log(response);

                    if(response){
                        window.product_weight_info =  $.parseJSON(response);

                    }
                }
            });



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


function orderInventory(order_id,station_id,repack){
    $('#invMethods').hide();
    $("#orderListTable").hide();
    $('#productList').show();
    var order_num = $("#order_num_"+order_id).html();
    var order_frame_count = $("#order_frame_count_"+order_id).val();
    var order_incubator_count = $("#order_incubator_count_"+order_id).val();
    var order_frame_mi_count = $("#order_frame_mi_count_"+order_id).val();
    var order_incubator_mi_count = $("#order_incubator_mi_count_"+order_id).val();
    var order_frame_ice_count = $("#order_frame_ice_count_"+order_id).val();
    var order_box_count = $("#order_box_count_"+order_id).val();
    var order_frame_meat_count = $("#order_frame_meat_count_"+order_id).val();
    var order_frame_vg_list = $("#order_frame_vg_list_"+order_id).val();
    var order_frame_vg_product = $("#order_frame_vg_product_"+order_id).val();

    var order_frame_meat_list = $("#order_frame_meat_list_"+order_id).val();
    var order_frame_mi_list = $("#order_frame_mi_list_"+order_id).val();
    var order_frame_ice_list = $("#order_frame_ice_list_"+order_id).val();
    var order_foam_ice_count = $("#order_foam_ice_count_"+order_id).val();
    var order_foam_count = $("#order_foam_count_"+order_id).val();
    var order_inv_comment = $("#order_inv_comment_"+order_id).val();
    var order_inv_spare_comment = $("#order_inv_spare_comment_"+order_id).val();
    var order_each_num = $("#order_each_num_"+order_id).val();
    var to_warehouse_title = $("#warehouse_id"+order_id).text();

    $("#to_warehouse_title").text(to_warehouse_title);
    $("#frame_count").val(order_frame_count);
    $("#incubator_count").val(order_incubator_count);
    $("#foam_count").val(order_foam_count);
    $("#frame_mi_count").val(order_frame_mi_count);
    $("#incubator_mi_count").val(order_incubator_mi_count);
    $("#frame_ice_count").val(order_frame_ice_count);
    $("#box_count").val(order_box_count);
    $("#frame_meat_count").val(order_frame_meat_count);
    $("#frame_vg_list").val(order_frame_vg_list);
    $("#frame_vg_product").val(order_frame_vg_product);




    $("#foam_ice_count").val(order_foam_ice_count);
    $("#inv_comment").val(order_inv_comment);
    $("#inv_spare_comment").val(order_inv_spare_comment);
    $(".fm_frame_count").val(order_frame_count);
    $(".fm_inv_comment").val(order_inv_comment);
    $(".fm_inv_spare_comment").val(order_inv_spare_comment);
    $(".fm_box_count").val(order_box_count);
    $(".fm_frame_vg_list").val(order_frame_vg_list);
    $("#frame_vg_product").val(order_frame_vg_product);
    if(order_frame_vg_list != ''){
        var order_frame_vg_arr = order_frame_vg_list.split(",");
        for(var i=0;i<order_frame_vg_arr.length;i++){
            var frame_num = order_frame_vg_arr[i];
            var frame_num_html = '<div id="frame_vg_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'vg\','+frame_num+');">X</span></div>';
            $("#vg_list").append(frame_num_html);
            //$(".fm_vg_list").append(frame_num_html);
        }


    }
    if(order_frame_meat_list != ''){
        var order_frame_meat_arr = order_frame_meat_list.split(",");
        for(var i=0;i<order_frame_meat_arr.length;i++){
            var frame_num = order_frame_meat_arr[i];
            var frame_num_html = '<div id="frame_meat_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'meat\','+frame_num+');">X</span></div>';
            $("#meat_list").append(frame_num_html);
        }
    }
    if(order_frame_mi_list != ''){
        var order_frame_mi_arr = order_frame_mi_list.split(",");
        for(var i=0;i<order_frame_mi_arr.length;i++){
            var frame_num = order_frame_mi_arr[i];
            var frame_num_html = '<div id="frame_mi_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'mi\','+frame_num+');">X</span></div>';
            $("#mi_list").append(frame_num_html);
        }
    }
    if(order_frame_ice_list != ''){
        var order_frame_ice_arr = order_frame_ice_list.split(",");
        for(var i=0;i<order_frame_ice_arr.length;i++){
            var frame_num = order_frame_ice_arr[i];
            var frame_num_html = '<div id="frame_ice_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'ice\','+frame_num+');">X</span></div>';
            $("#ice_list").append(frame_num_html);
        }
    }

    var titleContent = '鲜世纪订单分拣－'+order_id;
    if(order_inv_comment>0){
        titleContent += '[货位' + order_inv_comment + ']';
    }

    titleContent += '<br />' + $("#shipping_name_"+order_id).val() + ',';
    titleContent += $("#shipping_address_"+order_id).val();
    $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>' + titleContent);

    //设置当前分拣订单号，情况当前分拣商品编号
    $("#current_order_id").val(order_id);
    $("#current_do_product").val(0);

    //分拣界面恢复分拣完成按钮
    $('.classSubmitSortingPendingCheck').show();

    //分拣界面恢复条码扫描框
    $('#barcodescanner').show();

        /*zx
                * 查询do单的仓库与分拣仓库*/
        var warehouse_id = global.warehouse_id;
        // if (warehouse_id == 17) {
        $.ajax({
            type: 'POST',
            url : 'invapi.php',
            data : {
                method : 'get_frame_vg_list_status',
                data :{
                    deliver_order_id : order_id ,
                }
            },
            success: function (response) {
                var jsonData = $.parseJSON(response);
                if (parseInt(jsonData.order_count)>1) {
                    $("#is_need_merge_order").html("<span style='color:red;'>需要合单</span>");
                } else {
                    $("#is_need_merge_order").html("");
                }
                global.go_warehouse_id = parseInt(jsonData.warehouse_id);
                if (jsonData.warehouse_id != jsonData.do_warehouse_id ) {
                    $("#inv_comment").keyup(function(){
                        var tmptxt=$(this).val();
                        if(tmptxt.length >= 4 && tmptxt.length <= 8){
                            handleStockArea(tmptxt,(jsonData.warehouse_id),(jsonData.is_repack));
                        } else {
                            $(this).val("");
                        }

                    });
                }
                getOrderSortingList(order_id,0,station_id,repack);

            }
        });
    }

    function orderInventoryView(order_id,station_id){
        $("#get_deliver_order_id").val(order_id);
        $('#invMethods').hide();

    $("#orderListTable").hide();
    $('#productList').show();


    var order_num = $("#order_num_"+order_id).html();
    var order_frame_count = $("#order_frame_count_"+order_id).val();
    var order_foam_count = $("#order_foam_count_"+order_id).val();
    var order_incubator_count = $("#order_incubator_count_"+order_id).val();
    var order_frame_mi_count = $("#order_frame_mi_count_"+order_id).val();
    var order_incubator_mi_count = $("#order_incubator_mi_count_"+order_id).val();
    var order_frame_ice_count = $("#order_frame_ice_count_"+order_id).val();
    var order_box_count = $("#order_box_count_"+order_id).val();
    var order_frame_meat_count = $("#order_frame_meat_count_"+order_id).val();
    var order_frame_vg_list = $("#order_frame_vg_list_"+order_id).val();

    var order_frame_vg_product = $("#order_frame_vg_product_"+order_id).val();
    var order_frame_meat_list = $("#order_frame_meat_list_"+order_id).val();
    var order_frame_mi_list = $("#order_frame_mi_list_"+order_id).val();
    var order_frame_ice_list = $("#order_frame_ice_list_"+order_id).val();
    var order_foam_ice_count = $("#order_foam_ice_count_"+order_id).val();
    var order_inv_comment = $("#order_inv_comment_"+order_id).val();
    var order_inv_spare_comment = $("#order_inv_spare_comment_"+order_id).val();
    var order_each_num = $("#order_each_num_"+order_id).val();
    var to_warehouse_title = $("#warehouse_id"+order_id).text();
    $("#to_warehouse_title").text(to_warehouse_title);
    $("#frame_count").val(order_frame_count);
    $("#incubator_count").val(order_incubator_count);
    $("#inv_comment").val(order_inv_comment);
    $("#inv_spare_comment").val(order_inv_spare_comment);
    $("#frame_mi_count").val(order_frame_mi_count);
    $("#incubator_mi_count").val(order_incubator_mi_count);
    $("#frame_ice_count").val(order_frame_ice_count);
    $("#box_count").val(order_box_count);
    $("#frame_meat_count").val(order_frame_meat_count);
    $("#frame_vg_list").val(order_frame_vg_list);

    $("#frame_vg_product").val(order_frame_vg_product);
    $("#frame_meat_list").val(order_frame_meat_list);
    $("#frame_mi_list").val(order_frame_mi_list);
    $("#frame_ice_list").val(order_frame_ice_list);
    $("#foam_ice_count").val(order_foam_ice_count);
    $("#foam_count").val(order_foam_count);

    $(".fm_frame_count").val(order_frame_count);
    $(".fm_inv_comment").val(order_inv_comment);
    $(".fm_inv_spare_comment").val(order_inv_spare_comment);
    $(".fm_box_count").val(order_box_count);
    $(".fm_frame_vg_list").val(order_frame_vg_list);

    // if(order_frame_vg_list != ''){
    //     var order_frame_vg_arr = order_frame_vg_list.split(",");
    //     for(var i=0;i<order_frame_vg_arr.length;i++){
    //         var frame_num = order_frame_vg_arr[i];
    //         var frame_num_html = '<div id="frame_vg_'+frame_num+'" class="invopt getOrdersFilter style_gray style_green"><span class="invopt getOrdersFilter style_gray style_green"  id="frame_frame_'+frame_num+'"  onclick="getOrderSortingList('+order_id+','+1+','+station_id+','+0+','+frame_num+' );"   id="frame_vg_'+frame_num+'">'+frame_num+'</span>  <span class="frame_num" onclick="remove_frame(\'vg\','+frame_num+');">X</span></div>';
    //         $("#vg_list").append(frame_num_html);
    //         //$(".fm_vg_list").append(frame_num_html);
    //     }
    // }
    if(order_frame_meat_list != ''){
        var order_frame_meat_arr = order_frame_meat_list.split(",");
        for(var i=0;i<order_frame_meat_arr.length;i++){
            var frame_num = order_frame_meat_arr[i];
            var frame_num_html = '<div id="frame_meat_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'meat\','+frame_num+');">X</span></div>';
            $("#meat_list").append(frame_num_html);
        }
    }
    if(order_frame_mi_list != ''){
        var order_frame_mi_arr = order_frame_mi_list.split(",");
        for(var i=0;i<order_frame_mi_arr.length;i++){
            var frame_num = order_frame_mi_arr[i];
            var frame_num_html = '<div id="frame_mi_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'mi\','+frame_num+');">X</span></div>';
            $("#mi_list").append(frame_num_html);
        }
    }
    if(order_frame_ice_list != ''){
        var order_frame_ice_arr = order_frame_ice_list.split(",");
        for(var i=0;i<order_frame_ice_arr.length;i++){
            var frame_num = order_frame_ice_arr[i];
            var frame_num_html = '<div id="frame_ice_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'ice\','+frame_num+');">X</span></div>';
            $("#ice_list").append(frame_num_html);
        }
    }

    $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>'+'鲜世纪订单分拣－'+order_id+'<br>'+$("#shipping_name_"+order_id).val()+' - '+order_each_num+$("#shipping_address_"+order_id).val()+'<br>'+$("#shipping_phone_"+order_id).val());
    $("#current_order_id").val(order_id);
    $('#barcodescanner').hide();

    //查看界面隐藏分拣完成按钮
    $('.classSubmitSortingPendingCheck').hide();

    //查看界面隐藏条码扫描框
    $('#barcodescanner').hide();

    //管理员查看界面显示周转筐及货位号，隐藏分拣完成按钮（此处分拣完成为待审核）
    //if(is_admin == 1){
    showSubmitFrame();
    $('#fastmove_order_comment').show();
    $('#submitSorting').hide();
    //}
        /*zx
                * 查询do单的仓库与分拣仓库*/
        var warehouse_id = global.warehouse_id;
        // if (warehouse_id == 17) {
        $.ajax({
            type: 'POST',
            url : 'invapi.php',
            data : {
                method : 'get_frame_vg_list_status',
                data :{
                    deliver_order_id : order_id ,
                }
            },
            success: function (response) {
                var jsonData = $.parseJSON(response);
                if (parseInt(jsonData.order_count)>1) {
                    $("#is_need_merge_order").html("<span style='color:red;'>需要合单</span>");
                } else {
                    $("#is_need_merge_order").html("");
                }
                global.go_warehouse_id = parseInt(jsonData.warehouse_id);

                if (jsonData.warehouse_id != jsonData.do_warehouse_id ) {
                    $("#inv_comment").keyup(function(){
                        var tmptxt=$(this).val();
                        if(tmptxt.length >= 4 && tmptxt.length <= 8){
                            handleStockArea(tmptxt,(jsonData.warehouse_id),(jsonData.is_repack));
                        } else {
                            $(this).val("");
                        }

                    });
                }
                getOrderSortingList(order_id,1,station_id);

            }
        });
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
/*
* 数据列表
* */
function getOrderSortingList(order_id,is_view,station_id,repack,frame_num){
    $("#show_date").hide();

    //查看时隐藏条码扫描，分时显示
    if(is_view == 1){
        $('#barcodescanner').hide();
    }
    else{
        $('#barcodescanner').show();
    }

    window.product_inv_barcode_arr = {};
    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=getOrderSortingList',
        data : {
            method : 'getOrderSortingList',
            order_id : order_id,
            is_view : is_view,
            warehouse_id : global.warehouse_id,
            repack :repack ,
            frame_num:frame_num,


        },
        success : function (response , status , xhr){
            var html = '<td colspan="4">正在载入...</td>';
            $('#productsInfo').html(html);
            $("#frame_vg_list").val("");
            if(response){
                // console.log("Sorting Data:" + response);

                jsonData = $.parseJSON(response);
                if(jsonData.status == 1){
                    html = '';
                    var count_plan_quantity = 0;
                    var count_quantity = 0;

                    var order_been_over = "";
                    var order_been_over_size = "";
                    var boxCount = 0;
                    window.product_id_arr = {};


                    if(window.inventory_user_order[order_id]&& window.inventory_user_order[order_id][1]){
                        $("#inv_do_mi").remove();
                        $("#inv_do_ice").remove();
                        $("#inv_do_meat").remove();
                    }

                    if(window.inventory_user_order[order_id]&& window.inventory_user_order[order_id][2]){
                        $("#inv_do_vg").remove();
                        $("#inv_do_ice").remove();
                        $("#inv_do_meat").remove();
                    }
                    if(window.inventory_user_order[order_id]&& (window.inventory_user_order[order_id][3]|| window.inventory_user_order[order_id][5])){
                        $("#inv_do_vg").remove();
                        $("#inv_do_mi").remove();
                        $("#inv_do_meat").remove();
                    }
                    if(window.inventory_user_order[order_id]&&  window.inventory_user_order[order_id][4]){
                        $("#inv_do_vg").remove();
                        $("#inv_do_mi").remove();
                        $("#inv_do_ice").remove();
                    }

                    if(station_id == 1){
                        $("#fastmove_order_comment").remove();
                    }
                    else{
                        $("#inv_do_vg").remove();
                        $("#inv_do_mi").remove();
                        $("#inv_do_meat").remove();
                        $("#inv_do_ice").remove();
                    }
                    global.check_is_repack = 0;
                    var time_now = parseInt('<?php  echo $time_now;?>');
                    var time_plan = parseInt('<?php  echo $time_plan;?>');
                    $.each(jsonData.data, function(index,value){
                        if(parseInt(global.go_warehouse_id)!= 0 && parseInt(value.repack) ==0 && parseInt(value.station_id) == 2 && parseInt(global.warehouse_id) != parseInt(global.go_warehouse_id) && (parseInt(global.warehouse_id) == 12 || parseInt(global.warehouse_id) == 21) && time_now > time_plan) {
                            return true;
                        }
                            global.check_is_repack = 1;

                            window.product_barcode_arr[value.product_id] = {};
                            window.product_barcode_arr_s[value.product_id] = {};
                            window.product_inv_barcode_arr[value.product_id] = value.product_barcode_arr;

                            if (true) {
                                count_plan_quantity = parseInt(value.plan_quantity) + parseInt(count_plan_quantity);
                                count_quantity = parseInt(value.quantity) + parseInt(count_quantity);
                                if (value.repack == 0 && !isNaN(value.boxCount) && value.boxCount > 0) {
                                    boxCount += value.boxCount;
                                }
                                // console.log("BOX[" + value.product_id + "]:" + value.boxCount);

                                if (value.barcode > 0) {
                                    product_id_arr[value.barcode] = value.product_id;
                                }
                                if (value.sku > 0) {
                                    product_id_arr[value.sku] = value.product_id;
                                }
                                if (value.sku1.length != 0) {

                                    var array_sku = value.sku1.split(",");

                                    $.each(array_sku, function (index, val1) {
                                        product_id_arr[val1] = value.product_id;
                                    });
                                }
                                product_id_arr[value.product_id] = value.product_id;

                                if (value.quantity > 0) {
                                    order_been_over = "";
                                    order_been_over_size = "";
                                }
                                else {
                                    order_been_over = "style = 'background-color:#666666;'";
                                    order_been_over_size = "style = 'background-color:#666666;font-size:2em;'";
                                    ;

                                }


                                html += '<tr class="barcodeHolder "  id="bd' + value.product_id + '">';
                                html += '<td ' + order_been_over + ' class="prodlist" id="td' + value.product_id + '" >';
                                html += '<span style="font-size: 1.2rem"><span>' + value.inv_class_sort + '</span> ';
                                html += '<span name="productBarcode" class="sortingProductBarcode" style="display:none;" >' + value.product_id + '</span>';
                                html += '<span class="sortingProductSku" style="display:none;" >' + value.sku + '</span>';
                                html += '<span class="sortingProductQty" style="display:none;" >' + value.quantity + '</span>';
                                html += '<span class="sortingProductRepack" style="display:none;" id="sortingProductRepack' + value.product_id + '"  >' + value.repack + '</span>';
                                html += '[<span name="productId"" id="pid' + value.product_id + '">' + value.product_id + '</span>]';
                                if (parseInt(value.repack) == 1 && parseInt(value.station_id) == 2) {
                                    html += '<span style="color:red">[散]</span>';
                                }
                                if (parseInt(value.station_id) == 2 && parseInt(value.is_repack) == 1) {
                                    html += '<span style="color:red">[单个散件]</span>';
                                }
                                html += '<br />';
                                // html += '<span>' + value.name + '</span>';
                                html += '<span id="info' + value.product_id + '">' + value.name + '</span>';
                                html += '<span style="font-size: 0.8rem"><br />[条码:' + hideNum(value.sku, 4) + ']</span></span> ';
                                html += '</td>';

                                html += '<td ' + order_been_over_size + ' align="center" class="prodlist" style="font-size:2em;">' + value.plan_quantity + '</td>' +
                                    '<td ' + order_been_over + ' align="center" class="prodlist"><input class="qty" id="' + value.product_id + '" name="' + value.product_id + '" value="' + value.quantity + '" /><input type="hidden" id="plan' + value.product_id + '" value="' + value.quantity + '"><input type="hidden" id="old_plan' + value.product_id + '" value="' + value.plan_quantity + '"><input type="hidden" id="do' + value.product_id + '" value="0"><input type="hidden" id="pur_plan' + value.product_id + '" value="' + value.purchase_plan_id + '"></td>' +
                                    '<td ' + order_been_over + ' id="opera' + value.product_id + '">';
                                if (value.quantity > 0) {

                                    if (is_view == 0) {
                                        if (is_admin == 1) {
                                            html += '<input class="qtyopt pda_add_inv_' + value.product_id + '"  type="button" value="+" onclick="javascript:qtyadd(\'' + value.product_id + '\')"> <br/><br/><br/>' +
                                                '<input class="qtyopt style_green pda_add_inv_' + value.product_id + '" type="button" value="-" onclick="javascript:qtyminus2(\'' + value.product_id + '\')" ><br/><br/><br/>' +
                                                '<input class="qtyopt style_green pda_add_inv_' + value.product_id + '"  type="button" value="提交" onclick="javascript:tjStationPlanProduct2(\'' + value.product_id + '\')" >' +
                                                '<input style="font-size: 0.9rem;" class="innerSubmit" type="button" value="缺货提醒" onclick="javascript:shortReminder(\'' + value.product_id + '\')">';
                                        }
                                        else {
//                                            html += '<input class="innerSubmit" style="font-size: 0.9rem;" id="submit" type="button" value="拣" onclick="javascript:autoChooseSortingProduct(\''+value.sku+'\');">';
                                            html += '<input style="font-size: 0.9rem;" class="innerSubmit" type="button" value="缺货提醒" onclick="javascript:shortReminder(\'' + value.product_id + '\')">';

                                        }
                                    }
                                    html += '';
                                }
                                else {
                                    html += '已完成';
                                }

                                html += '</td>' + '</tr>';
                            }


                    });

                    // console.log(product_id_arr);
                    // console.log( window.product_inv_barcode_arr);

                    $("#count_plan_quantity").html(count_plan_quantity);
                    $("#count_quantity").html(count_quantity);
                    $('#productsInfo').html(html);

                    // console.log('BoxCount:'+boxCount);
                    if(boxCount > 0){
                        $('.fm_box_count').val(parseInt(boxCount));
                        $('#nonRepackBoxCount').text(parseInt(boxCount));
                    }
                    else{
                        $('.fm_box_count').val(0);
                        $('#nonRepackBoxCount').text(0);
                    }
                }
                else if(jsonData.status == -1){
                    alert('数据已并提交入库!');
                    $('#productsInfo').html('');
                }
                else if(jsonData.status == 999){
                    alert(jsonData.msg);
                    location.href = "inventory_login.php?return=w4.php";
                }
                else if(jsonData.status == 6){
                    alert('此订单已提交，不能重复分拣!');
                    $('#productsInfo').html('');
                }
                else{
                    alert('无数据或订单已取消!');
                    $('#productsInfo').html('');
                }
            }
        },
        complete : function(){
            getProductName();
            getAccomplishFrame(order_id);



            // console.log("Start Soring...");
            //autoChooseSortingProduct(9870,4897036691175);

//                //自动开始分拣，查看状态时忽略
               if(is_view == 0){
                   autoChooseSortingProduct();
               }
        }
    });
}


/*
*
* 扫描框的值
* */
function putNavTab(product,chkTab = false) {
    if (chkTab == false) {
        $("#myTab").append('<li id="remove_li'+product+'" class="nav-item">\n' +
            '    <a class="nav-link" id="input_'+product+'_frame-tab" data-toggle="tab" href="#'+product+'" role="tab" aria-controls="'+product+'" aria-selected="true">'+product+'<i id="remove_one"  class="icon-remove icon-1x" onclick="removeIcon('+product+')"></i></a>\n' +
            '  </li>');
        var html = '<div class="tab-pane fade " id="'+product+'" role="tabpanel" aria-labelledby="'+product+'-tab">\n' +
            '                    <div class="row">\n' +
            '                        <div class="table-responsive-sm">\n' +
            '                            <table class="table table-hover">\n' +
            '                                <thead class="table-success">\n' +
            '                                <tr>\n' +
            '                                    <th scope="col">商品信息</th>\n' +
            '                                    <th scope="col">商品数</th>\n' +
            '                                    <th scope="col">已分拣</th>\n' +
            '                                    <th scope="col">其它框数</th>\n' +
            '                                    <th scope="col">操作</th>\n' +
            '                                </tr>\n' +
            '                                </thead>\n' +
            '                                <tbody id="show_container_'+product+'">\n' +
            '                                </tbody>\n' +
            '                            </table>\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                </div>\n';
        $("#myTabContent").append(html);
        $('#myTab li:last-child a').tab('show');
    }
    if (chkTab == true) {
        $("#myTab").append('<li id="remove_li'+product+'" class="nav-item">\n' +
            '    <a class="nav-link" id="input_'+product+'_frame-tab" data-toggle="tab" href="#'+product+'" role="tab" aria-controls="'+product+'" aria-selected="true">'+product+'<i  class="icon-remove icon-2x" onclick="removeIcon('+product+')"></i></a>\n' +
            '  </li>');
        var html = '<div class="tab-pane fade " id="'+product+'" role="tabpanel" aria-labelledby="'+product+'-tab">\n' +
            '                    <div class="row">\n' +
            '                        <div class="table-responsive-sm">\n' +
            '                            <table class="table table-hover">\n' +
            '                                <thead class="table-success">\n' +
            '                                <tr>\n' +
            '                                    <th scope="col">商品信息</th>\n' +
            '                                    <th scope="col">商品数</th>\n' +
            '                                    <th scope="col">已分拣</th>\n' +
            '                                    <th scope="col">其它框数</th>\n' +
            '                                    <th scope="col">操作</th>\n' +
            '                                </tr>\n' +
            '                                </thead>\n' +
            '                                <tbody id="show_container_'+product+'">\n' +
            '                                </tbody>\n' +
            '                            </table>\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                </div>\n';
        $("#myTabContent").append(html);
    }

        // $('#myTab li a').tab('show');
        // $('#myTab li:last-child a').tab('show'); 不能开启

        // $('.icon-remove').on('click', function(ev) {
        //     var ev=window.event||ev;
        //     ev.stopPropagation();
        //     var gParent=$(this).parent().parent(),
        //         parent=$(this).parent();
        //     if(gParent.hasClass('active')){
        //         if(gParent.index()==gParent.length){
        //             gParent.prev().addClass('active');
        //             $(parent.attr('href')).prev().addClass('active');
        //         }else{
        //             gParent.next().addClass('active');
        //             $(parent.attr('href')).next().addClass('active');
        //         }
        //     }
        //
        //
        //     var container_id=$.trim(gParent.children().text());
        //     // console.log(container_id);
        //     if(confirm('确定要删除周转筐'+container_id+'吗?')){
        //         remove_frame('vg',container_id,1,gParent,parent);
        //         console.log(a);
        //
        //     }
        //
        // });
}

/*
* 删除对应周转筐和商品
* */
function removeIcon(container_id) {
    if (chkJurisdiction()) {
        return true;
    }
    if(confirm('确定要删除周转筐'+container_id+'吗?')){
        remove_frame('vg',container_id,1);
        window.location.reload();
    }
}
/*
* 检测权限
* */
function chkJurisdiction() {
    // console.log(order_id);
    var order_id = parseInt( $("#current_order_id").val());
    var soringOrderStatus=parseInt($("#soringOrderStatus"+parseInt(order_id)).val());
    var user_group_id = parseInt('<?php echo $_COOKIE['user_group_id'];?>');
    if (user_group_id == 15) {
        if (soringOrderStatus > 5) {
            // alert('----检测权限------');
            $("#putSwitch").attr('disabled',true);
            $("#frame_vg_list").attr('readonly','readonly');
            $("#delOne").removeAttr('onclick');
            $('a').removeAttr('onclick');
            alert('该分拣单已提交无法操作，如果是待审核，请联系班组长或仓管');
            return true;
        }


    } else if (user_group_id == 1 || user_group_id == 22) {
        if (soringOrderStatus == 6 || soringOrderStatus == 12) {
            alert('该分拣单已分拣完成无法再操作');
            return true;
        }
    } else {
        alert('该分拣单仅限分拣人员，班组长或仓管可操作');
        return true;
    }

}
/*
* 判断周转筐是否存在
*
* */
function self(product) {
    var arr_ =0;
    $("#myTab").find('li').each(function () {
        var list=$.trim($(this).text());
        // console.log(product);
        // console.log(list);
        // arr_.push(parseInt(list));
        if (parseInt(product) == parseInt(list)) {
            alert('周转筐已经存在','请再次检查周转筐编号！');
            $("#input_"+product+"_frame-tab").tab('show');
            // console.log("#input_"+product+"_frame");
            // showAlertError('#getMsg','周转筐已经存在','请再次检查周转筐编号！');
            arr_ = 1;
        }


    });
    return arr_;
    // console.log(arr_);
    // console.log(product);
    // if(($.inArray(parseInt(product),arr_))== -1 ){
    //     alert('周转筐已经存在','请再次检查周转筐编号！');
        // showAlertError('#getMsg','周转筐已经存在','请再次检查周转筐编号！');
        //  return 1;
        // return false;

    // }
}

/*
* 加载框子与商品
* */
function getAccomplishFrame(order_id) {
    // console.log(order_id);
    $.ajax({
        type: 'POST',
        url: 'invapi.php?method=getAccomplishFrame',
        dateType:'json',
        async:false,
        data: {
            method:'getAccomplishFrame',
            data:{
                 order_id:order_id,
                 product_id_arr:product_id_arr},
        },
        success: function(response , status , xhr){

            if (response !== '') {
                var list=$.parseJSON(response);
                $.each(list,function(k,v){
                    putNavTab(k,true);
                    $.each(v,function(kk,vv){
                        putNavList(vv,true);
                    });
                });
                $('#myTab li:last-child a').tab('show');
                // getProductFrequency();

            }

        },
        error:function (err) {
            
        },
        complete:function () {
            
        }
    });
}

/*
* 当前框子编号
* */
function getActive() {
    var str='';
    $('#myTab li a').each(function () {
        if ($(this).hasClass('active')){
           str=$.trim($(this).text());
        }
    });
    // var str=$.trim(activeNav);
    return str;
}
/*
* 获取所有的框子
* */
function getTab() {
    var arr='';
    $('#myTab li a').each(function () {
        arr = arr+$.trim($(this).text())+',';

    });
    var str='';
    str=arr.slice(0,-1);
    return str;
    // console.log(str);
}
/*
* 统计框子个数
* */
function getCountTab() {
    var i=0;
    $('#myTab li a').each(function () {
        i++
    });
    return i;
    // console.log(str);
}
/*
*@productNum 输入商品编号
* */
function putNavList(product,checkCabinet=false) {
        if (parseInt(product['quantity']) == 0) {
            return true;
        }
        check_produvt_arr = [];
        if (checkCabinet == false) {
            handleProductList(product);
            var id= product_status;
            global._arr={};
            $.each(jsonData['data'],function (k, v) {
                if (v['product_id'] == product) {
                    _arr=v;
                }
            });

            // console.log(_arr['name']);
            if (id){
                var arr=[];
                if ($.inArray(_arr,check_produvt_arr) == -1) {
                    $.each(jsonData['data'], function (k, v) {
                        if (v.product_id === _arr) {
                            arr = v;
                            check_produvt_arr.push(v.product_id);
                        }
                    });


                    $("#show_container_"+parseInt(product['container_id'])).append('<tr>\n' +
                        '                                    <th scope="row">'+parseInt(product['container_id'])+'_'+product['product_id']+'-'+product['product_name']+product['other_number']+'</th>\n' +
                        '                                    <td id="calcquantity_'+product['product_id']+'">'+product['product_num']+'</td>\n' +
                        '                                    <td id="calcNum_'+product['container_id']+'_'+product['product_id']+'">'+product['quantity']+'</td>\n' +
                        '                                    <td id="otherNum_'+product['container_id']+'_'+product['product_id']+'"><i class="icon-2x"><span class="badge badge-pill badge-warning">'+product['other_number']+'</span></i></td>\n' +
                         '                                    <td class="calc_operate">\n' +
                         '                                        <button value="-"  onclick="javascript:delOneProductInv()" type="button" class="btn btn-outline-danger"><i class="icon-minus icon-large"></i></input>\n' +
                         '                                    </td>\n' +
                        '                                </tr>');

                    console.log('--------checkCabinet=false--------');
                }
            }

        }else
        if (checkCabinet == true) {
            console.log('--------checkCabinet=true--------');
            // console.log(product['quantity']);

            if ($("#calcquantity_"+parseInt(product['container_id'])+'_'+product['product_id']).val()>=0) {
                $("#calcquantity_"+parseInt(product['container_id'])+'_'+product['product_id']).val(product['quantity']);
            } else {
                if (parseInt(product['other_number']) == 0||parseInt(product['other_number'])== undefined || parseInt(product['other_number'])==''){
                    $("#show_container_"+parseInt(product['container_id'])).append('                                <tr class="text-center" id="line_'+parseInt(product['container_id'])+'_'+parseInt(product['product_id'])+'">\n' +
                        '                                    <th id="proName_'+parseInt(product['container_id'])+'_'+product['product_id']+'" scope="row">'+product['product_id']+'-'+product['product_name']+'</th>\n' +
                        '                                    <td id="calcquantity_'+parseInt(product['container_id'])+'_'+product['product_id']+'">'+product['product_num']+'</td>\n' +
                        '                                    <td id="calcNum_'+product['container_id']+'_'+product['product_id']+'">'+product['quantity']+'</td>\n' +
                        '                                    <td id="otherNum_'+product['container_id']+'_'+product['product_id']+'"><i class="icon-2x"><span class="badge badge-pill badge-warning">'+product['other_number']+'</span></i></td>\n' +
                        '                                    <td class="calc_operate ">\n' +
                        '                                        <button value="'+product['container_id']+','+product['product_id']+'"  onclick="javascript:delOneProductInv(this.value)" type="button" class="btn btn-outline-danger"><i class="icon-minus icon-large"></i></input>\n' +
                        '                                    </td>\n' +
                        '                                </tr>');
                } else {
                    $("#show_container_"+parseInt(product['container_id'])).append('                                <tr class="text-center" id="line_'+parseInt(product['container_id'])+'_'+parseInt(product['product_id'])+'">\n' +
                        '                                    <th id="proName_'+parseInt(product['container_id'])+'_'+product['product_id']+'" scope="row">'+product['product_id']+'-'+product['product_name']+'</th>\n' +
                        '                                    <td id="calcquantity_'+parseInt(product['container_id'])+'_'+product['product_id']+'">'+product['product_num']+'</td>\n' +
                        '                                    <td id="calcNum_'+product['container_id']+'_'+product['product_id']+'">'+product['quantity']+'</td>\n' +
                        '                                    <td id="otherNum_'+product['container_id']+'_'+product['product_id']+'"><i class="icon-2x"><span class="badge badge-pill badge-warning">'+product['other_number']+'</span></i></td>\n' +
                        '                                    <td class="calc_operate">\n' +
                        '                                        <button id="delOne" value="'+product['container_id']+','+product['product_id']+'"  onclick="javascript:delOneProductInv(this.value)" type="button" class="btn btn-outline-danger"><i class="icon-minus icon-large"></i></input>\n' +
                        '                                    </td>\n' +
                        '                                </tr>');
                }

            }

        }



}



/*
* 单入口
* */
function putSwitch() {
    if (chkJurisdiction()) {
        return true;
    }
    var product=$.trim($("input[name='product']").val());
    $("input[name='product']").val("");
    chkGoods(product);
}
/*
* 入口检测
* */
function chkGoods(product) {
    res=true;
    var reg= /^[0-9]{6,12}[X]{1}[1-9]{1,2}$/;
    if((product.length == 6) || reg.test(product)){
        product=$.trim(product).split('X')[0];
        var aa = self(product);
        if(aa == 1){
            return true;
        }
       handleFrameList('vg',product);
       if (global.ajaxReturn == -1) {
           return false;
       }
        product=$.trim(product).split('X')[0];
        putNavTab(product,false);//生产框
    }else {
        var num= /^[0-9]{4,5}$|^[0-9]{8,15}$/;
        if(num.test(product)){
            if (0) {
                // putNavList(product,false);
                // showAlertError('#getMsg','当前没有周转筐！','请扫描周转筐');
            }else{
                putNavList(product,false);//生产 产品
            }
        }else{
            showAlertError('#getMsg','不是周转筐也非商品！','请重新扫描');
        }
    }


}
/*
* 检查框子,废弃使用
* */
function chkFrame(product) {
    $("input[name='product']").click(function(){
        var tmptxt=$(this).val();
        var container_id_length = tmptxt.length;

        if(container_id_length < 6){
            $(this).val('');
        }
    });

    $("input[name='product']").click(function(){
        var tmptxt=$(this).val();
        //$(this).val(tmptxt.replace(/\D|^0/g,''));
        if(tmptxt.length >= 6){
            var frameContainerNumber =  tmptxt.substr(0,6);
            if(!/^[1-9]\d{0,5}$/.test(frameContainerNumber.trim())){
                alert("请输入数字格式周转筐号");
                $(this).val("");
                return false;
            }
            $(this).val("");
        }
    }).bind("paste",function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D|^0/g,''));
    });
}
/*
* 获取长度为len的随机字符串
* */
function getRandomString(len) {
    len = len || 32;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
    var maxPos = $chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}
/**
 * @param header 标题
 * @param content 内容
 */
function showAlertSuccess(tally,header,content,auto=false) {
    // alert('123');
    $(tally).html("<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n" +
        "                                            <strong>"+header+"!</strong> "+content+".\n" +
        "                                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
        "                                                <span aria-hidden=\"true\">&times;</span>\n" +
        "                                            </button>\n" +
        "                                        </div>");
    $(tally).alert();
    if (auto==true){
        setTimeout(function(){$(tally).hide('slow')}, 4000);
    }
    // setTimeout(function(){$(tally).hide('slow')}, 2000);
}

/*
* 错误
* */
function showAlertError(tally,header,content,auto = false) {
    $(tally).html("<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n" +
        "                                            <strong>"+header+"!</strong> "+content+".\n" +
        "                                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
        "                                                <span aria-hidden=\"true\">&times;</span>\n" +
        "                                            </button>\n" +
        "                                        </div>");
    $(tally).alert();
    if (auto==true){
        setTimeout(function(){$(tally).hide('slow')}, 4000);
    }
}

/*
* 检测周转筐在商品的个数
* N:1
* */
function chkFrameProductNum() {
    var frameNum=getTab();
    return getFrameAllProduct(frameNum);
    // console.log(frameNum);
}

/*
* 获取N个框子下的所有产品
* */
function getFrameAllProduct(frameNum =''){
    if (frameNum) {
        var strList=frameNum.split(',');
        var allProductArray=[];
        for (i = 0; i < strList.length; i++) {
            $("#show_container_"+strList[i]).children().each(function () {
                //取ID过来
                var str=$(this).children(":first").attr('id');
                allProductArray.unshift(str);
            });
        }
        return allProductArray;


    }
}

/*
* 当前商品出现的次数
* count 当前商品已分拣的商品
* */
function getProductFrequency(product_id,count) {
        var frameNum=getTab();
        var frame=frameNum.split(',');
        $.each(frame,function(k,v){
            var str=$("#calcNum_"+v+"_"+product_id).text();
            var nowSum=parseInt(count)-parseInt(str);
            $("#otherNum_"+v+"_"+product_id).html('<i class="icon-2x"><span class="badge badge-pill badge-warning">'+nowSum+'</span></i>');
        })
}
/*
*一个商品的已分拣总数
* */
function invCount(product_id) {
    var frameNum=getTab();
    var frame=frameNum.split(',');
    $.each(frame,function(k,v){
        //每个柜子下的一个商品的总数
        var _arr=[];
        var str=$("#calcNum_"+v+"_"+product_id).text();
        _arr.unshift(str);
    })
    return _arr;
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
        $('#barcodescanner').show();
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
    $(barCodeId+' td').css('backgroundColor',color);
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
        url : 'invapi.php?method=getSortingProductInfo',
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
    $('#move_list').hide();
}

function applyFrameCount(){
    $(".fm_frame_count").val($("#vg_list div").length);
}


function handleFrameList(frame_type,frame_num=''){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    global.ajaxReturn = '';
    // frame_num = frame_num.slice(0,-2);
    frame_num = frame_num
    // console.log(frame_num);
    var order_id = $("#current_order_id").val();
    var frame_vg_list = $("#frame_"+frame_type+"_list").val();
    // console.log(frame_vg_list);
    if(frame_vg_list == ""){
        $("#frame_"+frame_type+"_list").val(frame_num);
    }

    // console.log(frame_num);
    $.ajax({
        type: 'POST',
        url: 'invapi.php',
        data: {
            method: 'checkContainer',
            data: {
                warehouse_id: global.warehouse_id,
                frame_vg_list: frame_num
            }
        },
        success: function (response) {
            global.resTmp = response;
            var obj = jQuery.parseJSON(response);
            global.resJson = obj;
            if(obj.container_id > 0 && obj.occupy  == 1 && obj.instore  ==  1){
                global.ajaxReturn = '-1';
                alert ('该周转筐被占用但未出库');
                // echo '1';
            }
            if(obj.container_id > 0 &&  obj.occupy  == 1  && obj.instore  ==  0  ){
                global.ajaxReturn = '-1';
                // console.log(global.ajaxReturn);
                alert('该周转筐被占用且已出库');
                // return global.ajaxReturn;
            }
            if(obj.container_id > 0 &&  obj.occupy  == 0  && obj.instore  ==  0  ){
                global.ajaxReturn = '-1';
                alert('该周转筐没被占用但已出库');
                //return 0;

            }
            if(obj.container_id > 0 &&  obj.occupy  == 0  && obj.instore  ==  1  ){
                global.ajaxReturn = '1';
            //

            }
        },
    });

    // console.log(global.ajaxReturn);

    // return global.ajaxReturn;
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

function remove_frame(frame_type,frame_num,type,gParent,parent){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    var order_id = $('#current_order_id').val();
    var container_id = frame_num;
    var user_id = "<?php  echo $_COOKIE['inventory_user_id'] ;?>";
    $.ajax({
        type: 'POST',
        url: 'invapi.php?method=getInventoryOrderSoring',
        data : {
            method: 'getInventoryOrderSoring',
            data: {
                order_id: order_id,
                container_id: container_id,
                user_id:user_id,

            },
        },
        success : function (response){
            var jsonData = $.parseJSON(response);
            if(jsonData == 1 ){
                alert('该周转筐已存商品不能删除');

            }else {
                if (type > 0){

                    $("#remove_li"+container_id).remove();
                    $("#"+container_id).remove();
                    $("#"+container_id+'-tab').remove();
                    $('#myTab li:last-child a').tab('show');
                    return 2;
                } else{
                    $("#frame_"+frame_type+"_"+frame_num).remove();
                    $("#frame_vg_product").val('');
                    applyFrameCount();
//                getOrderSortingList();
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


            }
        }
    });


}




//遍历待商品表,选择第一个待分拣商品开始分拣
function autoChooseSortingProduct(id){
    $("#product_name").show();
    //  $("#productsHoldDo").show();
    // console.log('Choose Sorting Product:');
    // console.log('id:'+id);

    $('html,body').animate({scrollTop: '0px'}, 0);
    var sortingProductId = id;

    var idPicked = id;

    //若未指定，取下一个商品，若指定，当前商品移除放在末尾，跳转到指定商品
    var firstSoringProductNode = $('#productsInfo tr')[0];
    if(id==undefined){
        var id = parseInt($(firstSoringProductNode).find('.sortingProductBarcode').text());
        var sku = $(firstSoringProductNode).find('.sortingProductSku').text();

        sortingProductId = sku;
        if(check_in_array(id,window.no_scan_product_id_arr)){
            sortingProductId = id;
        }
    }

    //待分拣为0或者下一条已分拣过，跳转到提交货位号界面
    var count_quantity = parseInt($('#count_quantity').text());
    var sortingQty = parseInt($(firstSoringProductNode).find('.sortingProductQty').text());

    if((count_quantity == 0 || sortingQty==0) && idPicked == undefined){
        $('#barcodescanner').show();
        $('#fastmove_order_comment').show();
    }
    else{
        //若下一条为散货，显示周转筐
        var sortingRepack = parseInt($(firstSoringProductNode).find('.sortingProductRepack').text());
        if(sortingRepack){
            // $('#fastmove_order_frame').hide();
        }

        //隐藏商品直接加减，待扫描条码后显示
        $('#current_product_quantity_change').hide();
        $("#quehuotixing").show();
        $('#current_product_quantity_change_memo').hide();
        $('#current_product_quantity_change_start').val(0); //分拣新品，置为0

        //管理员直接显示加减
        if(is_admin){

            $('#current_product_quantity_change').show();
            $('#current_product_quantity_change_memo').hide();
        }

        //隐藏分拣提交
        $('#fastmove_order_comment').hide();

        //显示分拣扫描
        $('#barcodescanner').show();

        //$('#product').val(sortingProductId);
        //handleProductList(sortingProductId);
    }
}

function showSubmitFrame(){
    $('html,body').animate({scrollTop: '0px'}, 0);
    $('#fastmove_order_frame').show();
    $("#input_vg_frame").focus();
}

function hideSubmitFrame(){
    $('#fastmove_order_frame').hide();
    $("#input_vg_frame").blur();
}

function showSubmitSorting(){
    window.location ="#sttttt";
    $('html,body').animate({scrollTop: '0px'}, 0);
    var warehouse_id = $("#warehouse_id").text();
    //强制显示提交订单分拣完成按钮，若当前有操作中的商品，直接提交
    var currentDoProduct = parseInt($("#current_do_product").val());
    if(currentDoProduct > 0){
        if(confirm('当前有分拣中的商品，确认提交此商品？')){
            tjStationPlanProduct(currentDoProduct);

            //$("#current_do_product").val(0);
            //$("#productsHoldDo").hide();
            //$("#product_name").html("");
        }
    }

    $('#barcodescanner').show();
    $('#fastmove_order_comment').show();
    $('#submitSorting').show(); //恢复提交按钮，在管理员修改货位时可能被隐藏。
}

function locateInput(){
    // console.log("开始聚焦扫描框");
    $('#product').focus();
}

function showScannerInfo(msg){
    console.log("---" + msg + "---");
    console.log("当前扫描条码：" + $('#product').val());
    console.log("当前缓存保存条码：" + window.scanBarcode);
    console.log("当前处理商品：" + $('#product_name').text());

    //$("#pid"+id).parent().find('.sortingProductSku').text();
}

function scannerFocused(){
    //输入框已聚焦，移除通知
    removeAlertInfo();
}

function showAlertInfo(msg){
    $("#alertinfo").html(msg);
    $("#alertinfo").show();
}

function removeAlertInfo(msg){
    $("#alertinfo").html('');
    $("#alertinfo").hide();
}

function soundEffectInit(){
    //音效设置
    var sound = {};
    sound.playerSubmit = $("#playerSubmit")[0];
    sound.playerAlert = $("#playerAlert")[0];
    sound.playerMessage = $("#playerMessage")[0];

        return sound;
    }
    /*zx
    * 货位号扫描*/
    function handleStockArea(tmptxt,warehouse_id,is_repack){
        if (chkJurisdiction()) {
            return true;
        }
        if (warehouse_id == 14) {
            if (3000 < tmptxt && tmptxt <= 4000) {
                $("#inv_comment").val(tmptxt);
            } else {
                alert("该订单不能放在此货位号,属于浦东出库位");
                $("#inv_comment").val('');
            }
        } else if (warehouse_id == 15) {
            if (4000 < tmptxt && tmptxt <= 4500) {
                $("#inv_comment").val(tmptxt);
            } else {
                alert("该订单不能放在此货位号，属于苏州出库位");
                $("#inv_comment").val('');
            }
        } else if (warehouse_id == 19) {
            if (4500 < tmptxt && tmptxt <= 5000) {
                $("#inv_comment").val(tmptxt);
            } else {
                alert("该订单不能放在此货位号，属于苏州吴江出库位");
                $("#inv_comment").val('');
            }
        } else if (warehouse_id == 16) {
            if (5000 < tmptxt && tmptxt <= 5500) {
                $("#inv_comment").val(tmptxt);
            } else {
                alert("该订单不能放在此货位号,属于宁波（老仓）出库位");
                $("#inv_comment").val('');
            }
        } else if (warehouse_id == 18) {
            if (5500 < tmptxt && tmptxt <= 6000) {
                $("#inv_comment").val(tmptxt);
            } else {
                alert("注意！该订单不能放在此货位号，此货位属于宁波新仓出库位");
                $("#inv_comment").val('');
            }
        } else if (warehouse_id == 17) {
            if (6000 < tmptxt && tmptxt <= 7000) {
                $("#inv_comment").val(tmptxt);
            } else {
                alert("该订单不能放在此货位号,属于金山出库位");
                $("#inv_comment").val('');
            }
        } else if (warehouse_id == 20) {
            if (7000 < tmptxt && tmptxt <= 8000) {
                $("#inv_comment").val(tmptxt);
            } else {
                alert("该订单不能放在此货位号,属于金山出库位");
                $("#inv_comment").val('');
            }
        } else if (warehouse_id == 22) {
            if (8000 < tmptxt && tmptxt <= 9000) {
                $("#inv_comment").val(tmptxt);
            } else {
                alert("该订单不能放在此货位号,属于嘉定仓出库位");
                $("#inv_comment").val('');
            }
        }
        if (warehouse_id == 12 && is_repack == 1) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'get_frame_vg_list_unique',
                    data: {
                        container_id: tmptxt,
                    }
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);
                    if (jsonData == 2) {
                        $("#inv_comment").val(tmptxt);
                    } else if (jsonData == 1) {
                        alert("所选货位号已被使用");
                        $("#inv_comment").val('');
                    }
                }
            });
        }
    }

function handleProductList(id){
    if (chkJurisdiction()) {
        return true;
    }
    locateInput();
   product_status = 0;
//    console.log('laile');
    var rawId = $('#product').val();
    /*if(id == 'undefined' || id == undefined || !id > 0){
        id = rawId.substr(0,18);//Get 18 code
    }*/

    if(id.length == 0){
        return false;
    }
    var scan_barcode = id;
    window.scanBarcode = id;

    // if(id.length <= 6 && !check_in_array(id,window.no_scan_product_id_arr)){
    //     //alert("不能输入商品ID，必须扫描");
    //     showAlertInfo("不能输入商品ID，必须扫描");
    //     return false;
    // }
    //添加商品条码
    if(window.product_id_arr[id] > 0){
        id = window.product_id_arr[id];

        var barCodeId = "#bd"+id;

        var  frame_repack = $("#sortingProductRepack"+id).text();
        if(frame_repack == 1){
            var vg_list = $("#frame_vg_list").val();
            var frame_vg_product = $("#frame_vg_product").val();
            if(frame_vg_product == ''){
                alert('请先扫描周转框');
                showSubmitFrame();
                return false;
            }


        }

        $('#product').val('');
        if($("#"+id).val() == 0){

            var player = $("#player2")[0];
            player.play();
            alert("["+id+"]此商品已完成分拣，不要重复分拣");

            // sound.playerAlert.play();
            // showAlertInfo("["+id+"]此商品已完成分拣，不要重复分拣");

            return false;
        }

        var current_do_product = $("#current_do_product").val();
        if(current_do_product == 0){

            showOverlay();

            $("#current_do_product").val(id);

            $("#product_name").html($("#td"+id).html());
            $("#current_do_tj").html('<span id="tj'+id+'" style="" onclick="javascript:autoSubmitSoringProduct(\''+id+'\')" class="invopt">提交</span>');
            $("#current_product_plan").html('<span id="order_plan_quantity_'+id+'">'+$("#old_plan"+id).val()+'</span>'+'<span style="display:none;" name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>');
            $("#current_product_quantity").html( '<input class="qty"  id="'+id+'" value="'+$("#"+id).val()+'"><input type="hidden" id="plan'+ id +'" value="'+$("#plan"+id).val()+'"><input type="hidden" id="old_plan'+ id +'" value="'+$("#old_plan"+id).val()+'"><input type="hidden" id="do'+id+'" value="0">');

            if(window.product_weight_info[id] && window.product_weight_info[id]['weight'] ){

                $("#current_product_quantity_change").html('');

            }else{
                var repack = $("#sortingProductRepack"+id).text();
                var current_product_quantity =  $("#old_plan"+id).val();

                if(current_product_quantity >= 5){
                    var quyu = current_product_quantity % 5 ;

                }
                var productQtyOpt = '';
                productQtyOpt += '<input style="height:3em;width:3em;" class="qtyopt" type="button" value="+" onclick="javascript:qtyadd(\''+id+'\')">';
                productQtyOpt += '<input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus(\''+id+'\')">';


                if(repack == 1 && current_product_quantity >=5){
                    productQtyOpt += '<input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-5" onclick="javascript:qtyminus5(\''+id+'\')">';
                    if(quyu >=2){
                        productQtyOpt += '<input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-'+quyu+'"  onclick="javascript:qtyminus10(\''+id+'\',\''+quyu+'\')">';
                    }

                }

                $("#current_product_quantity_change").html(productQtyOpt);

                var quehuo = '';
                quehuo += '<input style="height:3em;width:5em;" class="qtyopt" type="button" value="缺货提醒" onclick="javascript:shortReminder(\''+id+'\')">';
                $("#quehuotixing").html(quehuo);
            }

            $("#productsHoldDo").show();
            $("#product_name").show();

            $('#current_product_quantity_change').show();


//               var plan =  $("#plan"+id).val();
//                var old_plan = $("#old_plan"+id).val();
//                if(plan < old_plan ){
//
//                    $("#current_product_quantity_change").show();
//                }

            $(barCodeId).remove();
            hideOverlay();
        }
        else{
            if(current_do_product == id){
                //执行分拣减少商品，显示可加减
                $('#current_product_quantity_change_memo').hide();
                $('#current_product_quantity_change_start').val(1); //开始分拣，置为1
                $('#current_product_quantity_change').show();
                $("#productsHoldDo").show();
                $("#product_name").show();

                qtyminus(id);
            }
            else{
                //alert(current_do_product);

                if(parseInt($('#current_product_quantity_change_start').val()) == 1){
                    //var player = $("#player2")[0];
                    //player.play();
                    sound.playerAlert.play();

                    if(confirm("当前商品还未完成分拣，确认提交并分拣其他商品？")){
                        tjStationPlanProduct(current_do_product);
                        autoChooseSortingProduct(window.scanBarcode);

                        handleProductList(window.scanBarcode);
                        $('#current_product_quantity_change').show();

                        $("#productsHoldDo").show();
                        $("#product_name").show();
                    }
                }
                else{
                    //var player = $("#player3")[0];
                    //player.play();
                    sound.playerMessage.play();

                    tjStationPlanProduct(current_do_product);
                    autoChooseSortingProduct(window.scanBarcode);
                    $("#productsHoldDo").show();
                    $("#product_name").show();


                    $('#current_product_quantity_change').show();

//                        $('#current_product_quantity_change').show();
                }

            }

        }

        //$('#product').val('');
    }
    else{
        //var player = $("#player2")[0];
        //player.play();
        sound.playerAlert.play();

        $('#product').val("");
        // alert("编号["+id+"]此商品不需入库");
        showAlertInfo("编号["+id+"]此商品不需入库");
        return false;
    }

    product_status = scan_barcode;

    console.log(product_status);
}



function qtyadd(id){
    $("#product").focus();
    var prodId = "#"+id;
    var calcNum_prodId = "#calcNum_"+id;
    var qty = parseInt($(prodId).val()) + 1;
    var calcNum_qty = parseInt($(calcNum_prodId).text()) + 1;
    var do_qty = parseInt($("#do"+id).val()) - 1;

    if(qty >= parseInt($("#calcquantity_"+id).text())){
        calcNum_qty = parseInt($("#calcquantity_"+id).text());
        // do_qty = 0;

    }

    if(qty >= parseInt($("#plan"+id).val())){
        qty = parseInt($("#plan"+id).val());
        do_qty = 0;

    }

    $(calcNum_prodId).text(calcNum_qty);
    $(prodId).val(qty);
    $("#do"+id).val(do_qty);
//locateInput();

    // console.log(id+':'+qty);
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


    var calcNum_prodId = "#calcNum_"+id;
    $("#product").focus();


    scan_barcode = window.scanBarcode;

    if(window.product_weight_info[id]){
        if(window.product_barcode_arr_s[id][scan_barcode] == scan_barcode){
            alert("此商品已经扫描分拣了，不能重复扫描分拣同一件商品");
            return false;
        }
        if(window.product_inv_barcode_arr[id]){
            var inv_product_barcode_flag = false;
            $.each(window.product_inv_barcode_arr[id], function(index,value){
                if(value == scan_barcode){
                    alert("此商品已经扫描分拣了，不能重复扫描分拣同一件商品");
                    inv_product_barcode_flag = true;
                    return false;
                }
            })
            if(inv_product_barcode_flag){
                return false;
            }
        }

    }

    if($(prodId).val() >= 1){
        /*
        * calcNum
//        * */
        var containerId=getActive();
        $num=$("#calcNum_"+containerId+'_'+id).text();
        var calcNum_qty = parseInt($(calcNum_prodId).text()) - 1;
//        $newNum=parseInt($num)-parseInt(1);
        $(calcNum_prodId).text(calcNum_qty);
//        if($("#calcNum").text() == parseInt(0) ){
//            $(".btn-outline-danger").hide();
//            showAlertError('#getMsg','已经完成了！','请点击取消');
//
//        }
        var qty = parseInt($(prodId).val()) - 1;
        $(prodId).val(qty);

        var do_qty = parseInt($("#do"+id).val()) + 1;
        $("#do"+id).val(do_qty);
        $('#current_product_quantity_change_start').val(1);
        if(window.product_weight_info[id]){
            window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            window.product_barcode_arr_s[id][scan_barcode] = scan_barcode;
            $("#current_product_quantity_change").append('<span id="'+id+'_'+scan_barcode+'">'+scan_barcode+'<input style="" class="qtyopt_weight style_green" type="button" value="-" onclick="javascript:qtyminus_weight(\''+id+'\',\''+scan_barcode+'\')"><br></span>');

        }else{
            if(scan_barcode.length == 18){
                window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            }
        }
        // console.log(window.product_barcode_arr);

    }

    if ($(calcNum_prodId).text() == 0){
        $(calcNum_prodId).siblings('.calc_operate').find('.btn-outline-danger').hide();
        $(calcNum_prodId).siblings('.calc_operate').find('.btn-outline-success').hide();

    }

    if($(prodId).val() == 0){//添加到周转框

        if(window.product_weight_info[id]&&window.product_weight_info[id]['weight']&&false){

        }
        else{
            //提交插入中间表
            //addOrderProductStation(id);
            //hideOverlay();

            //pushCurrentSoringProductIntoEnd();
            //removeCurrentSoringProduct();

            //开始分拣下一个--------1
            //autoChooseSortingProduct();
            autoSubmitSoringProduct(id);
        }


    }

    //locateInput();

    // console.log(id+':'+qty);
}


function qtyminus5(id){
    var prodId = "#"+id;

    $("#product").focus();

    scan_barcode = window.scanBarcode;

    if(window.product_weight_info[id]){
        if(window.product_barcode_arr_s[id][scan_barcode] == scan_barcode){
            alert("此商品已经扫描分拣了，不能重复扫描分拣同一件商品");
            return false;
        }
        if(window.product_inv_barcode_arr[id]){
            var inv_product_barcode_flag = false;
            $.each(window.product_inv_barcode_arr[id], function(index,value){
                if(value == scan_barcode){
                    alert("此商品已经扫描分拣了，不能重复扫描分拣同一件商品");
                    inv_product_barcode_flag = true;
                    return false;
                }
            })
            if(inv_product_barcode_flag){
                return false;
            }
        }

    }

    if($(prodId).val()-5 >= 0){
        var qty = parseInt($(prodId).val()) - 5;
        $(prodId).val(qty);

        var do_qty = parseInt($("#do"+id).val()) + 5;
        $("#do"+id).val(do_qty);
        $('#current_product_quantity_change_start').val(1);
        if(window.product_weight_info[id]){
            window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            window.product_barcode_arr_s[id][scan_barcode] = scan_barcode;
            $("#current_product_quantity_change").append('<span id="'+id+'_'+scan_barcode+'">'+scan_barcode+'<input style="" class="qtyopt_weight style_green" type="button" value="-" onclick="javascript:qtyminus_weight(\''+id+'\',\''+scan_barcode+'\')"><br></span>');

        }else{
            if(scan_barcode.length == 18){
                window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            }
        }
        console.log(window.product_barcode_arr);

    }
    if($(prodId).val() == 0){

        if(window.product_weight_info[id]&&window.product_weight_info[id]['weight']&&false){

        }
        else{
            //提交插入中间表
            //addOrderProductStation(id);
            //hideOverlay();


            //pushCurrentSoringProductIntoEnd();
            //removeCurrentSoringProduct();

            //开始分拣下一个
            //autoChooseSortingProduct();
            autoSubmitSoringProduct(id);
        }


    }

    //locateInput();

    // console.log(id+':'+qty);
}


function qtyminus10(id,quyu){
    var prodId = "#"+id;

    $("#product").focus();

    scan_barcode = window.scanBarcode;

    if(window.product_weight_info[id]){
        if(window.product_barcode_arr_s[id][scan_barcode] == scan_barcode){
            alert("此商品已经扫描分拣了，不能重复扫描分拣同一件商品");
            return false;
        }
        if(window.product_inv_barcode_arr[id]){
            var inv_product_barcode_flag = false;
            $.each(window.product_inv_barcode_arr[id], function(index,value){
                if(value == scan_barcode){
                    alert("此商品已经扫描分拣了，不能重复扫描分拣同一件商品");
                    inv_product_barcode_flag = true;
                    return false;
                }
            })
            if(inv_product_barcode_flag){
                return false;
            }
        }

    }

    if($(prodId).val()-quyu >= 0){
        var qty = parseInt($(prodId).val()) - quyu;
        $(prodId).val(qty);

        var do_qty = parseInt($("#do"+id).val()) + quyu;
        $("#do"+id).val(do_qty);
        $('#current_product_quantity_change_start').val(1);
        if(window.product_weight_info[id]){
            window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            window.product_barcode_arr_s[id][scan_barcode] = scan_barcode;
            $("#current_product_quantity_change").append('<span id="'+id+'_'+scan_barcode+'">'+scan_barcode+'<input style="" class="qtyopt_weight style_green" type="button" value="-" onclick="javascript:qtyminus_weight(\''+id+'\',\''+scan_barcode+'\')"><br></span>');

        }else{
            if(scan_barcode.length == 18){
                window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            }
        }
        console.log(window.product_barcode_arr);

    }
    if($(prodId).val() == 0){

        if(window.product_weight_info[id]&&window.product_weight_info[id]['weight']&&false){

        }
        else{
            //提交插入中间表
            //addOrderProductStation(id);
            //hideOverlay();

            //pushCurrentSoringProductIntoEnd();
            //removeCurrentSoringProduct();

            //开始分拣下一个
            //autoChooseSortingProduct();
            autoSubmitSoringProduct(id);
        }


    }

    //locateInput();

    // console.log(id+':'+qty);
}







function qtyminus2(id){
    var prodId = "#"+id;

    if($(prodId).val() >= 1){
        var qty = parseInt($(prodId).val()) - 1;
        $(prodId).val(qty);

        var do_qty = parseInt($("#do"+id).val()) + 1;
        $("#do"+id).val(do_qty);
    }
    if($(prodId).val() == 0){

        //提交插入中间表
        addOrderProductStation(id);
        hideOverlay();



        $(".pda_add_inv_"+id).hide();

        $("#current_do_product").val(0);
        $("#productsHoldDo").hide();
        $("#product_name").html("");

    }

    //locateInput();

    // console.log(id+':'+qty);
}










function autoSubmitSoringProduct(id){
    tjStationPlanProduct(id);

    //自动挑选下一个分拣
    autoChooseSortingProduct();
}

function tjStationPlanProduct(id){
    addOrderProductStation(id);
    hideOverlay();

    pushCurrentSoringProductIntoEnd(id);
    removeCurrentSoringProduct();
}

function pushCurrentSoringProductIntoEnd(id){
    var doneStyle = '';
//        if($("#"+id).val() == 0){
//            doneStyle = 'style="background-color:#666666;"';
//        }

    var thisQty = $('#'+id).val();
    if(thisQty == 0){
        doneStyle = 'style="background-color:#666666;"';
    }

    var thisSku = $("#pid"+id).parent().find('.sortingProductSku').text();

    var html = '';
    html += '<tr class="barcodeHolder" id="bd'+ id +'">';
    html += '<td class="prodlist" '+ doneStyle +' id="td'+id+'" ><span id="info'+ id +'">'+$("#product_name").html()+'</span></td>';
    html += '<td align="center" '+ doneStyle +' class="prodlist" style="font-size:2em;">'+ $("#old_plan"+id).val() +'</td>';
    html += '<td align="center" '+ doneStyle +' class="prodlist"><input class="qty" id="'+ id +'" name="'+ id +'" value="'+$("#"+id).val()+'" /><input type="hidden" id="plan'+ id +'" value="'+$("#plan"+id).val()+'"><input type="hidden" id="old_plan'+ id +'" value="'+$("#old_plan"+id).val()+'"><input type="hidden" id="do'+id+'" value="0"></td>';
    html += '<td id="opera'+id+'" '+ doneStyle +'>';
    if($("#"+id).val() == 0){
        html += '已完成';
    }else{
        if(thisSku>0){
//                html += '<input class="innerSubmit" style="font-size: 0.9rem;" id="submit" type="button" value="拣" onclick="javascript:autoChooseSortingProduct(\''+thisSku+'\');">';

        }
        else{
            html += '';
        }

    }
    html += '</td>';
    html += '</tr>';

    if($("#"+id).val() == 0){
        $("#productsCompleted").append(html);
    }else{
        $("#productsInfo").append(html);
    }
}

function removeCurrentSoringProduct(){
    //清除当前分拣
    $("#current_do_product").val(0);
    $("#productsHoldDo").hide();
    $("#product_name").html("");

    //window.scanBarcode = 0;
    $("product").val('');
}

function tjStationPlanProduct2(id){
    addOrderProductStation(id);

    hideOverlay();


    $("#current_do_product").val(0);
    $("#productsHoldDo").hide();
    $("#product_name").html("");

    //开始分拣下一个
    autoChooseSortingProduct();
}


function addOrderProductStation(id){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    var container_id= getActive();
    var order_id = $('#current_order_id').val();
    var warehouse_id = $("#warehouse_id").text();
    var product_id = $("#pid"+id).html();
    var product_quantity = $("#do"+id).val();
    var warehouse_repack = '<?php echo $_COOKIE['warehouse_repack'];?>';
    var user_repack = '<?php echo $_COOKIE['user_repack'];?>';
    var frame_vg_product = $("#frame_vg_product").val();
    var frame_vg_list = $("#frame_vg_list").val();
    var frame_count = $("#frame_count").val();

    var  nowNumber=$("#calcNum_"+container_id+'_'+product_id).text();
    var  countNumber=$("#calcquantity_"+container_id+"_"+product_id).text();
    // nowNumber=nowNumber?nowNumber:0;


    // console.log(product_quantity);
    // nowNumber = parseInt(product_quantity)+parseInt(nowNumber);
    if(product_quantity >0){
        $.ajax({
            type : 'POST',
            async:false,
            url : 'invapi.php?method=addOrderProductStation',
            data : {
                method : 'addOrderProductStation',
                order_id : order_id,
                product_id : product_id,
                product_quantity : product_quantity,
                warehouse_id : warehouse_id ,
                container_id : container_id ,

            },
            success : function (response, status, xhr){
                var allData = $.parseJSON(response);
                // console.log(allData);
                //分拣数量
                var  nowNumber=$("#calcNum_"+container_id+'_'+product_id).text();
                //总数
                var  countNumber=$("#calcquantity_"+container_id+"_"+product_id).text();
                if (nowNumber !== "" ||nowNumber !== 0) {
                    $("#line_"+container_id+"_"+product_id).remove();
                    if (nowNumber > countNumber){
                        alert('分拣数量不应大于总数,请再次确认!');
                        return true;
                    }
                }
                nowNumber=nowNumber?nowNumber:0;
                // console.log(product_quantity);
                //提交的数量+之前的数量
                nowNumber = parseInt(product_quantity)+parseInt(nowNumber);
                if(allData){
                    //当前已分拣总数
                    allData['allNumber']=allData['allNumber']?allData['allNumber']:nowNumber;
                    var productName=_arr['name'];
                    var vv={};
                    vv.product_id = product_id;
                    vv.other_number = parseInt(allData['allNumber']) - parseInt(nowNumber);
                    vv.product_name= productName;
                    vv.quantity = nowNumber;
                    vv.container_id = container_id;
                    vv.product_num = $("#order_plan_quantity_"+product_id).text();
                    putNavList(vv,true);
                    getProductFrequency(product_id,allData['allNumber']);
                }
            }
        });
    }

}
/*
* 删除一条数据
* */
function delOneProductInv(data) {
    if (chkJurisdiction()) {
        return true;
    }

    var container_id=$.trim(data).split(',')[0];
    var product_id=$.trim(data).split(',')[1];
    var order_id = $("#current_order_id").val();
    // alert(order_id);
    if(confirm("是否确认删除已分拣数据？")){
        $.ajax({
            type: 'POST',
            async:false,
            url: 'invapi.php?method=delOneProductInv',
            dateType:'json',
            data: {
                method:'delOneProductInv',
                data:{
                    container_id:container_id,
                    product_id:product_id,
                    order_id:order_id
                },
            },
            success:function(delData, status, xhr){
                if (delData){
                    var data = $.parseJSON(delData);
                    $("#line_"+container_id+"_"+product_id).remove();
                    showAlertSuccess("#getMsg",'删除成功!','请重新分拣',true);
                    $("#input_"+container_id+'_frame-tab').tab('show');
                    getProductFrequency(product_id,data['allNumber']);
                }
            },
            error:function (err) {
                console.log(err);
            }
        })
    }


}


/*获取当前商品在几个框里*/
function getNowFrameProductNumber() {
    var order_id = $('#current_order_id').val();
    var container_id = getActive();
    // alert(order_id);
    $.ajax({
        type: 'POST',
        url: 'invapi.php?method=getFrameProductNumber',
        dateType:'json',
        cache: false,
        async: false,
        data: {
            method:'getFrameProductNumber',
            data:{
                order_id:order_id,container_id:container_id},
        },
        success:function(frameData, status, xhr){
            // console.log(frameData);
            if (frameData){
                var data = $.parseJSON(frameData);
                disposeNowFrameProductNumber(data);
            }
        },
        error:function (err) {
            console.log(err);
        }
    })
}
/*获取当前商品在几个框里,拆解数据*/
function disposeNowFrameProductNumber(Framedata) {
    var data=chkFrameProductNum();
    for (var i = 0; i < data.length; i++) {
        // $.each(Framedata,function (k, v) {
        //     $("#"+v['container_id']+'_'+v['product_id']).append('<span class="badge badge-pill badge-warning">'+66+'</span>');
        // });
        $("#"+data[i]).append('<span class="badge badge-pill badge-warning">'+66+'</span>');
    }
    // for (i = 0; i < Framedata.length; i++) {
    //     $("#"+data[i]['container_id']+"_"+data[i]['product_id']).append('<span class="badge badge-pill badge-warning">'+66+'</span>');
    // }
}
function delOrderProductToInv(){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    if(confirm("是否确认删除已分拣数据？")){
        var order_id = $('#current_order_id').val();

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'delOrderProductToInv',
                order_id : order_id
            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);
                    hideOverlay();


                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w4.php';
                    }

                    if(jsonData.status == 1){
                        alert("分拣数据已删除");
                        location.reload();
                    }

                    if(jsonData.status == 2){
                        alert("订单分拣数据已提交，无法删除");
                    }

                }
            }
        });
    }
}


function batchProcessAddOrderProductToInv(orderList){
    return false;
    console.log(orderList);

    //return false;
    showOverlay();
    $.each(orderList, function(i,order_id){
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            cache: false,
            async: false,

            data : {
                method : 'addOrderProductToInv',
                order_id : order_id
            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);

                    if(jsonData.status == 0){
                        alert("["+order_id+"]部分商品未提交入库成功，请重试");
                        hideOverlay();
                        return false;
                    }

                    if(jsonData.status == 2){
                        alert("无待确认提交的商品");
                        hideOverlay();
                        return false;
                    }

                    if(jsonData.status == 4){
                        alert("每个订单不能重复提交分拣数据");
                        hideOverlay();
                        return false;
                    }

                    if(jsonData.status == 5){
                        alert("["+order_id+"]分拣数量超过订单数量 或 有重复提交相同条码的商品，请删除分拣数据重新分拣 "+jsonData.timestamp);
                        hideOverlay();
                        return false;
                    }

                    if(jsonData.status == 6){
                        alert("订单已经提交过，如果继续提交分拣数量就会超过订单数量，请检查");
                        hideOverlay();
                        return false;
                    }
                }
            }
        });
    });
    hideOverlay();
    getOrderByStatus();
}

function addConsolidatedRelevant(){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    var order_id = $('#current_order_id').val();
    var warehouse_id = $("#warehouse_id").text();

    if(confirm('确认直接提交已拣完？提交之后将不能进行投篮，请确认好商品全部投篮完成再提交！')) {
        $('#classSubmitSortingRelevant').remove('class', "submit");
        $('#classSubmitSortingRelevant').hide();


        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=addConsolidatedRelevant',
            data: {
                method: 'addConsolidatedRelevant',
                data:{
                    order_id: order_id,
                    warehouse_id: warehouse_id,
                },
            },
            success: function (response, status, xhr) {
                var jsonData = $.parseJSON(response);

                if (jsonData.status == 999) {
                    alert("未登录，请登录后操作");
                    window.location = 'inventory_login.php?return=w4.php';
                }

                if (jsonData.status == 1) {
                    //根据分拣件数确认是否提交订单或待审核
                    var confirmMsg = "商品已全部分拣，是否确认分拣完成？";
                    if (parseInt(jsonData.plan_quantity) != parseInt(jsonData.do_quantity)) {
                        confirmMsg = "计划分拣数量" + jsonData.plan_quantity + "，实际分拣" + jsonData.do_quantity + "，是否确认提交完成？";
                    } else {
                        userPendingCheck = 0;
                    }
                    if (confirm(confirmMsg)) {
                        addConsolidatedDoInfo();
                    } else {
                        hideOverlay();
                        return false;
                    }
                }
                if (jsonData.status == 0) {
                    alert("今天已经提交过了，不能重复提交");
                    hideOverlay();
                }
                if (jsonData.status == 2) {
                    alert("无待确认提交的商品或该订单已经取消");
                    hideOverlay();
                }
                if (jsonData.status == 3) {
                    alert("此订单已提交分拣不能再次提交");
                    hideOverlay();
                }
                if (jsonData.status == 4) {
                    alert("当前订单没有捡完不能合单");
                    hideOverlay();
                }

            },
            complete: function () {
                $('#classSubmitSortingRelevant').attr('class', "submit");
                $('#classSubmitSortingRelevant').attr('value', "提交合单");
            }

        });
    }

}

function updateDoStatus(){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    var order_id = $('#current_order_id').val();
    var warehouse_id = $("#warehouse_id").text();

    if(confirm('确认修改订单状态并确认？')) {
        $('#classUpdateDoStatus').remove('class', "submit");
        $('#classUpdateDoStatus').attr('value', "正在修改...");
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=updateDoStatus',
            data: {
                method: 'updateDoStatus',
                data: {
                    order_id: order_id,
                    warehouse_id: warehouse_id,
                },
            },
            success:function(response){
                var jsonData = $.parseJSON(response);
                if(jsonData ==1){
                    alert('修改状态成功可以提交合单');
                }else{
                    alert('修改状态失败需要DO单是已拣完状态');
                }
            },
            complete: function () {
                $('#classUpdateDoStatus').attr('class', "submit");
                $('#classUpdateDoStatus').attr('value', "浦西没到货修改订单状态并记录");
            }
        });
    }
}

function  addConsolidatedDoInfo() {
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    var order_id = parseInt($('#current_order_id').val());
    var invComment = parseInt($('#inv_comment').val()); //保存货位号
    var boxCount = parseInt($("#box_count").val()); //保存整箱数
    var frame_vg_list = $("#frame_vg_list").val();
    var frame_count = $("#frame_count").val();
    var warehouse_id = $("#warehouse_id").text();
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
                invComment : invComment,
                boxCount : boxCount,
                frame_count : frame_count,
                frame_vg_list : frame_vg_list,
                warehouse_id :warehouse_id,
                inventory_user:inventory_user,
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
                    window.location = 'inventory_login.php?return=w4.php';
                }

                if(jsonData.status == 1){
                    alert("提交商品入库成功");
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
            }
        },
        complete : function(){
            //提交完成后刷新页面
            //addOrderNum();
                location.reload();
        }
    });

}


function forceAddDoOrder(){
    var invComment = $('#inv_comment').val();
}


function addOrderProductToInv_pre(userPendingCheck){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    showOverlay();
    var warehouse_id = $("#warehouse_id").text();
    if(userPendingCheck == undefined || userPendingCheck == 'undefined'){
        userPendingCheck = 0;
    }
    var invComment = $('#inv_comment').val();
    var inv_spare_comment = $('#inv_spare_comment').val();

    var warehouse_repack = '<?php echo $_COOKIE['warehouse_repack'];?>';
    var user_repack = '<?php echo $_COOKIE['user_repack'];?>' ;



    if(!/^[1-9]\d{0,3}$/.test(invComment.trim())){
        alert("请输入数字格式货位号");
        hideOverlay();
        return false;
    }




    console.log("Start Submit Order(Pre-check).");

    var order_id = $('#current_order_id').val();

    if(confirm('确认直接提交已拣完？')) {
        $('#classSubmitSorting').remove('class', "submit");
        $('#classSubmitSorting').attr('value', "正在提交...");
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=addOrderProductToInv_pre',
            data: {
                method: 'addOrderProductToInv_pre',
                order_id: order_id,
                warehouse_id: warehouse_id,
                go_warehouse_id: global.go_warehouse_id,
            },
            success: function (response, status, xhr) {
                var jsonData = $.parseJSON(response);

                if (jsonData.status == 999) {
                    alert("未登录，请登录后操作");
                    window.location = 'inventory_login.php?return=w4.php';
                }

                if (jsonData.status == 1) {
                    //根据分拣件数确认是否提交订单或待审核
                    var confirmMsg = "商品已全部分拣，是否确认分拣完成？";
                    if (parseInt(jsonData.plan_quantity) != parseInt(jsonData.do_quantity)) {
                        confirmMsg = "计划分拣数量" + jsonData.plan_quantity + "，实际分拣" + jsonData.do_quantity + "，是否确认提交完成？";
                    } else {
                        userPendingCheck = 0;
                    }
                    if (confirm(confirmMsg)) {
                        addOrderProductToInv(userPendingCheck);
                    } else {
                        hideOverlay();
                        return false;
                    }
                }
                if (jsonData.status == 0) {
                    alert("今天已经提交过了，不能重复提交");
                    hideOverlay();
                }
                if (jsonData.status == 2) {
                    alert("无待确认提交的商品或该订单已经取消");
                    hideOverlay();
                }
                if (jsonData.status == 3) {
                    alert("此订单已提交分拣不能再次提交");
                    hideOverlay();
                }
            },
            complete: function () {
                hideOverlay();

                $('#classSubmitSorting').attr('class', "submit");
                $('#classSubmitSorting').attr('value', "直接提交已拣完");
            }

        });
    }
}


function addOrderProductToInv(userPendingCheck){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    var order_id = parseInt($('#current_order_id').val());
    var invComment = parseInt($('#inv_comment').val()); //保存货位号
    var inv_spare_comment = parseInt($('#inv_spare_comment').val()); //保存货位号
    var boxCount = parseInt($("#box_count").val()); //保存整箱数
    var frame_vg_list = getTab();
    var frame_count = getCountTab();
    var warehouse_id = $("#warehouse_id").text();
    var warehouse_repack = '<?php echo $_COOKIE['warehouse_repack'];?>';
    var user_repack = '<?php echo $_COOKIE['user_repack'];?>' ;
    console.log("Pending check:" + userPendingCheck);
    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=addFastMoveSortingToInv',
        //async : false,
        //cache : false,
        data : {
            method : 'addFastMoveSortingToInv',
            order_id : order_id,
            userPendingCheck : userPendingCheck,
            invComment : invComment,
            boxCount : boxCount,
            frame_count : frame_count,
            frame_vg_list : frame_vg_list,
            warehouse_id :warehouse_id,
            warehouse_repack:warehouse_repack,
            user_repack:user_repack,
            inv_spare_comment : inv_spare_comment,
        },
        success : function (response, status, xhr){
            if(response){
                console.log(response);
                //var jsonData = eval(response);
                var jsonData = $.parseJSON(response);
                hideOverlay();

                if(jsonData.status == 999){
                    alert("未登录，请登录后操作");
                    window.location = 'inventory_login.php?return=w4.php';
                }

                if(jsonData.status == 1){
                    alert("提交商品入库成功");
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
            }
        },
        complete : function(){
            //提交完成后刷新页面
            //addOrderNum();
            location.reload();
        }
    });

    //addOrderNum();
}




function addOrderNum(){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    showOverlay();

    var order_id = $('#current_order_id').val();
    var frame_count= '';
    var incubator_count = '';
    var foam_count = '';
    var inv_comment = '';
    var inv_spare_comment = '';
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

    var add_type = 3;


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
    if($("#inv_spare_comment")){
        inv_spare_comment = $("#inv_spare_comment").val();
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
    if($("#frame_vg_product")){
        frame_vg_product = $("#frame_vg_product").val();
    }
    $.ajax({
        type : 'POST',
        url : 'invapi.php',
        data : {
            method : 'addOrderNum',
            order_id : order_id,
            frame_count : frame_count,
            inv_comment : inv_comment,
            inv_spare_comment:inv_spare_comment,
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
            frame_ice_list : frame_ice_list,
            frame_vg_product:frame_vg_product,
        },
        success : function (response, status, xhr){
            console.log(response);
            if(response){
                //var jsonData = eval(response);
                var jsonData = $.parseJSON(response);
                hideOverlay();


                if(jsonData.status == 999){
                    alert("未登录，请登录后操作");
                    window.location = 'inventory_login.php?return=w4.php';
                }

                if(jsonData.status == 99){
                    alert(jsonData.msg);
                }

                if(jsonData.status == 1){
                    alert("提交框数成功");
                    $("#frame_vg_product").val('');
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
    $('#fastmove_order_frame').hide();
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
            url : 'invapi.php',
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
        url : 'invapi.php',
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
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
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
        url : 'invapi.php',
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
            url : 'invapi.php',
            data : {
                method : 'inventory_logout'
            },
            success : function (response , status , xhr){
                //console.log(response);
                window.location = 'inventory_login.php?return=w4.php';
            }
        });
    }
}

function getOrderByStatus(){
    var order_status_id = $("#orderStatus").val();
    var warehouse_id = $("#warehouse_id").text();
    $.ajax({
        type : 'POST',
        url : 'invapi.php',
        data : {
            method : 'getOrders',
            station_id: 2,
            inventory_user: inventory_user,
            date : '<?php echo $date_array[2]['date']; ?>',
            order_status_id : order_status_id,
            warehouse_id:warehouse_id,

        },
        success : function (response , status , xhr){
            //console.log(response);

            if(response){

                var jsonData = $.parseJSON(response);



                if(jsonData.status == 999){
                    alert(jsonData.msg);
                    location.href = "inventory_login.php?return=w4.php";
                }

                var html = '';

                var each_i_num = 1;
                var each_i_num_new = 0;
                var each_i_num_kuai = 501;
                var each_i_num_veg = 0;
                var num_flag = true;
                var batchProcessOrderList = [];
                $.each(jsonData.data, function(index, value){
                    var t_status_class = '';
                    var product_str = '';

                    if(value.station_id == 1){
                        return true;
                    }


                    if(value.station_id == 2&&num_flag){
                        num_flag = false;
                        each_i_num_veg = each_i_num;
                        each_i_num_new = each_i_num;
                    }

                    if(value.order_product_type == 1){
                        product_str = '菜';
                    }
                    if(value.order_product_type == 2){
                        t_status_class = "style = 'background-color:#ffff00;'";
                        product_str = '菜+奶';
                    }
                    if(value.order_product_type == 3){
                        t_status_class = "style = 'background-color:#9933ff;'";
                        product_str = '奶';
                    }





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


                    html += '<tr station_id="'+value.station_id+'">';
                    html += '<td '+t_status_class+'>';
                    if(value.is_bao == 1){
                        html += '<span style="color:red;">爆</span><br>';
                    }
                    if(value.station_id == 2){
                        each_i_num = each_i_num_kuai+(each_i_num_new - each_i_num_veg);
                        //html += '<span style="color:red;">快</span><br>';
                    }
                    html += '<input type="hidden" id="order_frame_count_'+value.order_id+'" value="'+value.frame_count+'">';
                    html += '<input type="hidden" id="order_incubator_count_'+value.order_id+'" value="'+value.incubator_count+'">';
                    html += '<input type="hidden" id="order_foam_count_'+value.order_id+'" value="'+value.foam_count+'">';
                    html += '<input type="hidden" id="order_frame_mi_count_'+value.order_id+'" value="'+value.frame_mi_count+'">';
                    html += '<input type="hidden" id="order_incubator_mi_count_'+value.order_id+'" value="'+value.incubator_mi_count+'">';
                    html += '<input type="hidden" id="order_frame_ice_count_'+value.order_id+'" value="'+value.frame_ice_count+'">';
                    html += '<input type="hidden" id="order_box_count_'+value.order_id+'" value="'+value.box_count+'">';
                    html += '<input type="hidden" id="order_frame_meat_count_'+value.order_id+'" value="'+value.frame_meat_count+'">';
                    html += '<input type="hidden" id="order_frame_vg_list_'+value.order_id+'" value="'+value.frame_vg_list+'">';

                    html += '<input type="hidden" id="order_frame_meat_list_'+value.order_id+'" value="'+value.frame_meat_list+'">';
                    html += '<input type="hidden" id="order_frame_mi_list_'+value.order_id+'" value="'+value.frame_mi_list+'">';
                    html += '<input type="hidden" id="order_frame_ice_list_'+value.order_id+'" value="'+value.frame_ice_list+'">';

                    html += '<input type="hidden" id="order_foam_ice_count_'+value.order_id+'" value="'+value.foam_ice_count+'">';
                    html += '<input type="hidden" id="order_inv_comment_'+value.order_id+'" value="'+value.inv_comment+'">';
                    html += '<input type="hidden" id="order_inv_spare_comment_' + value.order_id + '" value="' + value.inv_spare_comment + '">';


                    html += '<input type="hidden" id="order_each_num_'+value.order_id+'" value="'+each_i_num+'">';

                    html += '<span id="order_num_'+value.order_id+'">'+each_i_num+'</span><br>'+product_str+'</td>';
                    html += '<td '+t_status_class+'>'+value.order_id;

                    if(value.customer_group_id == 2){
                        html += '<br><span style="color:red;">无价签</span>';
                    }
                    if(parseInt(value.inv_comment) > 0){
                        html += '<br><span style="color:darkgreen; font-weight: bold;">[货位' + value.inv_comment + ']</span>';
                    }

                    if(value.is_urgent == 1 ){
                        html +='<input type="hidden" id="shipping_name_'+value.order_id+'" value="'+value.shipping_name+'"><input type="hidden" id="shipping_phone_'+value.order_id+'" value="'+value.shipping_phone+'"><input type="hidden" id="shipping_address_'+value.order_id+'" value="'+value.shipping_address_1+'"></td>';
                    }else{
                        html +='<input type="hidden" id="shipping_name_'+value.order_id+'" value="'+value.shipping_name+'"><input type="hidden" id="shipping_phone_'+value.order_id+'" value="'+value.shipping_phone+'"><input type="hidden" id="shipping_address_'+value.order_id+'" value="'+value.shipping_address_1+'"></td>';
                    }


                    html += '<td '+t_status_class+'>'+value.plan_quantity+'</td>';
                    html += '<td '+t_status_class+'>'+value.quantity+'</td>';
                    html += '<td '+t_status_class+'>'+value.added_by+'</td>';
                    html += '<td '+t_status_class+'>';
                    if (parseInt(value.order_count)>1) {
                        html += '<span style="color:red;">需要合单</span><br />';
                    }
                    html += value.name;
                    html += '</td>';
                    html += '<td '+t_status_class+'><button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderInventoryView('+value.order_id+','+value.station_id+');">查看</button>';
                    if((value.order_status_id == 2 || value.order_status_id == 5) && value.no_inv != 1){
                        html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+','+value.station_id+');">开始</button>';
                    }

                    /*
                     if(is_admin == 1){
                     html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+');">提交分拣</button>';
                     }
                     */
                    html += '</td>';
                    html += '</tr>';
                    html += '';

                    if(parseInt(value.quantity) == 0 && parseInt(value.station_id) == 2 && parseInt(value.order_status_id) == 5 && batchProcessOrderList.length<10){
                        batchProcessOrderList.push(value.order_id);
                    }


                    if(each_i_num_new != 0){
                        each_i_num_new++;
                    }
                });

                var batchProcessHtml = '';
                if(batchProcessOrderList.length){
                    var batchProcessOrderListString = '';
                    $.each(batchProcessOrderList,function(i,v){
                        batchProcessOrderListString += v;
                        if(i < batchProcessOrderList.length - 1){
                            batchProcessOrderListString += ', ';
                        }
                    });
                    batchProcessHtml += '<tr style="display: none">' +
                        '<td colspan="6" span=""><span style="color:#CC0000">[测试!]</span>批量提交未分拣量为0订单(仅快消，每10单一批)：'+batchProcessOrderListString+'</td>' +
                        '<td><button class="invopt" onclick="javascript:batchProcessAddOrderProductToInv(['+batchProcessOrderList+'])">批量提交</button></td>' +
                        '</tr>';
                }
                html = batchProcessHtml + html;
                $('#ordersList').html(html);

                console.log('Load Stations');
            }
        }

    });

}


function shortReminder(id){
    if (check_in_right_time()) {
        return true;
    }if (chkJurisdiction()) {
        return true;
    }
    var warehouse_id = $("#warehouse_id").text();
    var inventoryuser = '<?php echo $_COOKIE['inventory_user'];?>';
    if(confirm("确认缺货提醒吗？")) {
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'shortReminder',
                data: {
                    warehouse_id: warehouse_id,
                    product_id: id,
                    inventoryuser: inventoryuser,
                },
            },
            success: function (response) {
                var jsonData = $.parseJSON(response);
                if (jsonData) {

                    alert('提醒成功');
                    var product_info = jsonData[0]['product_id'] + jsonData[0]['name'] + ' 货位号： ' + jsonData[0]['stock_area'] + ' ,缺货提醒';

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
<?php foreach($no_scan_product_id_arr_w as $key=>$value){ ?>
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
<script>
/*$("#foldList").toggle(function () {
    $("#productsHold").hide();
},function () {
    $("#productsHold").show();
})*/
$("#foldList").on('click',function () {
    $("#productsHold").toggle('slow');
})
</script>
</html>