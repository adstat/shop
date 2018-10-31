<?php
if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';
$inventory_user_admin = array('1','22');
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
<title>鲜世纪整件调拨</title>
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
    width:8em;
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

#barcode{
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

#itemHold td{
    background-color:#d0e9c6;
    color: #000;
    height: 2.5em;

    border-radius: 0.2em;
    box-shadow: 0.1em rgba(0, 0, 0, 0.2);
}

#itemHold th{
    padding: 0.3em;
    background-color:#8fbb6c;
    color: #000;

    border-radius: 0.2em;
    box-shadow: 0.1em rgba(0, 0, 0, 0.2);
}

.list td{
    background-color:#d0e9c6;
    color: #000;
    height: 2.5em;

    border-radius: 0.2em;
    box-shadow: 0.1em rgba(0, 0, 0, 0.2);
}

.list th{
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

.input_default{
    height: 1.8rem;
    width: 8rem;
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

.w6rem{ width: 6rem; }
.w4rem{ width: 4rem; }
.w2rem{ width: 2rem; }

.f0_7rem{ font-size: 0.7rem; }
.f0_8rem{ font-size: 0.8rem; }
.f0_9rem{ font-size: 0.9rem; }
.f1_0rem{ font-size: 1.0rem; }
.f1_1rem{ font-size: 1.1rem; }
.f1_2rem{ font-size: 1.2rem; }

hr { margin: 0.1rem 0; float: none; clear: both; }
.linkButton {height: 1.4rem; margin: 0.3rem; padding:0.2rem; font-size: 0.9rem; color:#ffffff; border-radius: 0.2rem; background-color: #DF0000;}
</style>

<style media="print">
    .noprint{display:none;}
</style>

<script>
    var global = {};
    global.warehouseId = '<?php echo $_COOKIE['warehouse_id'];?>';
    global.userId = '<?php echo $_COOKIE['inventory_user_id'];?>';
    global.readyBoxes = {};
    global.transferringBoxes = {};
    global.addTranseBoxList = {};

    window.product_barcode_arr = {};
    window.product_inv_barcode_arr = {};
    <?php if(strstr($_COOKIE['inventory_user'],'scfj')){ ?>
    $(document).keydown(function (event) {
        $('#product').focus();
    });
    <?php } ?>
</script>
</head>

<div style="background-color: #FFFFFF;height:2.6rem; width: 100%">
    <div align="left" style="float: left; margin: 0.5rem;">
        <img src="view/image/logo.png" align="absmiddle" style="width:5rem; "/>
    </div>
    <div align="right">
        <button class="linkButton" style="display: inline;float:left;" onclick="window.location.href='i.php?auth=xsj2015inv&ver=db'">返回</button>
        <button class="linkButton" style="display: inline;float:left" onclick="javascript:location.reload();">刷新</button>
        <?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?>
        <button class="linkButton" onclick="javascript:logout_inventory_user();">退出</button>
        <?php if(in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
            <script type="text/javascript">
                is_admin = 1;
            </script>
        <?php } ?>
    </div>
</div>

<div id="content" style="display: block">
<div align="center" style="margin:0.5em auto;"></div>
<div align="center" style="margin:0.5em auto;"></div>
<div id="message" class="message style_light" style="display: none">指定配送仓库及改仓库订单配送日期</div>

    <div id="inv_control" align="center">
<div id="invMethods">
    <label>
        目的仓库:
        <select id="toWarehouseId" style="width:8rem;height:2rem;margin-bottom: 0.6rem">
        </select>
    </label>
    <br />
    配送日期:<input class="input_default" id="deliverDate" type="text" value="<?php echo date('Y-m-d', strtotime("+1 Days"));?>">
<!--    日期选择-->
    <input type="hidden" id="last1_day_time" value="<?php echo date("Y-m-d",strtotime("-1 Days"));?>"/>
    <input type="hidden" id="last2_day_time" value="<?php echo date("Y-m-d",strtotime("-2 Days"));?>"/>
    <input type="hidden" id="last3_day_time" value="<?php echo date("Y-m-d",strtotime("-3 Days"));?>"/>
    <input type="hidden" id="next1_day_time" value="<?php echo date("Y-m-d",strtotime("+1 Days"));?>"/>
    <input type="hidden" id="next2_day_time" value="<?php echo date("Y-m-d",strtotime("+2 Days"));?>"/>
    <input type="hidden" id="next3_day_time" value="<?php echo date("Y-m-d",strtotime("+3 Days"));?>"/>
    <input type="hidden" id="next0_day_time" value="<?php echo date("Y-m-d",time());?>"/>
    <select id="date_end_change" style="width:3rem;height:2rem;margin-bottom: 0.6rem" onchange="javascript:change_date_end_time('deliverDate','date_end_change');">
        <option value="last3_day_time">大前天</option>
        <option value="last2_day_time">前天</option>
        <option value="last1_day_time">昨天</option>
        <option value="next0_day_time">今天</option>
        <option value="next1_day_time" selected>明天</option>
        <option value="next2_day_time">后天</option>
        <option value="next3_day_time">大后天</option>
    </select>
    <script>
        function change_date_end_time(date_id,date_end_change) {
            var date_change_id = "#"+date_end_change;
            $("#"+date_id).val($("#"+$(date_change_id).val()).val());
        }
    </script>
    <!--日期选择-->
<hr />
    <button id="transferBoxes" class="invopt" style="display: block" onclick="javascript:inventoryMethodHandler('getBoxBatchInfo');">整件批次分拣</button>
</div>
<div id="itemList" name="itemList" method="POST" style="display: none;">

    <table id="itemHold" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
        <caption id="transferSettings">
            <!-- transferSettings -->
            <strong>目的仓库：</strong><span id="selectedWarehouse"></span><br />
            <strong>配送日期：</strong><span id="selectedDeliverDate"></span>
        </caption>
        <tr id="container_lists" style="display: none;">
            <th align="left">周转筐</th>
            <th style="width:6rem">订单号</th>
            <th style="width:2rem">货位</th>
            <th style="width:2.5rem">操作</th>
        </tr>
        <tr id="product_lists" style="display: none;">
            <th style="width:2rem">编号</th>
            <th style="width:6rem">商品</th>
            <th style="width:2rem">总数</th>
            <th style="width:2rem">已拣</th>
            <th style="width:2rem">操作</th>
        </tr>
        <tbody id="itemInfo">
        <!-- Scanned Itme List -->
        </tbody>
    </table>
    <table id="productsHoldDo1" border="1px" style="margin:1rem 0; width:100%;display:none;border:1px solid;solid-color: black;" cellpadding=2 cellspacing=3>
        <tbody id="productsInfoDo1">
        </tbody>
    </table>
    <div id="scanner" style="display: ;">
        <form method="post" onsubmit="handleList(); return false;">
            <input id="product_make_hand" rows="1" maxlength="19" placeholder="请手动输入" style="font-size: 1.1em;margin-top: 0.5em;margin-bottom: 0.5em;ime-mode:disabled;background-color:#d0e9c6;height: 2em;display:none"/>
            <input class="addprod style_green" type="submit" id="make_hand" value ="添加" style="font-size: 1em; padding: 0.2em;display:none"/>
            <input name="product" id="product" rows="1" maxlength="19"  autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; height: 2em;"/></form>
        <input class="addprod style_green"  id="make_hand_hide" type="submit" value ="手动输入" onclick="javascript:clickProductInput();" style="font-size: 1em; padding: 0.2em;">
        <script>
            /*
            * 扫描手写切换
            * hide_id 扫描
            * show_id 输入
            * submit_id 输入确认
            * */
            function clickProductInput() {
                var hide_id = "#product";
                var show_id = "#product_make_hand";
                var submit_id = "#make_hand";
                var text_id = "#make_hand_hide";
                var text1 = "手动输入";
                var text2 = "隐藏手动";
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
            $("input[name='product']").keyup(function(){
                var tmptxt=$(this).val();
                // $(this).val(tmptxt.replace(/\D/g,''));
                if(tmptxt.length >= 4){
                    handleList();
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
        </script>
<!--            <input name="barcode" id="barcode" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; font-size: 1.2rem"/>-->
<!--            <input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">-->
            <input type='hidden' value="0" name="transferProduct" id="transferProduct" />
        </form>
    </div>
<!--    <script type="text/javascript">-->
<!--        $("input[name='barcode']").keyup(function(){-->
<!--            var tmptxt=$(this).val();-->
<!--            $(this).val(tmptxt.replace(/\D/g,''));-->
<!---->
<!--            //if(tmptxt.length >= 5){-->
<!--            //  handleProductList2();-->
<!--            //}-->
<!--            //$(this).val("");-->
<!--        }).bind("paste",function(){-->
<!--                var tmptxt=$(this).val();-->
<!--                $(this).val(tmptxt.replace(/\D/g,''));-->
<!--            });-->
<!--        //$("input[name='product']").css("ime-mode", "disabled");-->
<!--    </script>-->

    <div style="float:left">当前时间: <span id="currentTime2"><?php echo date('Y-m-d H:i:s', time());?></span></div>
    <input class="submit" id="submitReturnProduct" type="button" value="提交" onclick="javascript:addTransferOrder();">
    <table id="productsHoldDo2" border="1px" style="width:100%;display:none;border:0px solid;solid-color: black;" cellpadding=2 cellspacing=3>
        <tbody id="productsInfoDo2">
        </tbody>
    </table>
    <div style="float: none; clear: both"></div>

    <hr style="margin: 1rem 0;"/>
    <h1>已分拣DO单待调拨的整件</h1>
    <button class="linkButton" style="display: inline;" onclick="">显示全部</button>
    <button class="linkButton" style="display: inline;" onclick="">显示可调拨</button>
    <table class="list"  border="0" style="width:100%;" cellpadding=2 cellspacing=3>
        <tr style="display:;">
            <th style="">商品信息</th>
            <th style="width:2.5rem">已出<hr />合计</th>
            <th style="width:2.5rem">待出库</th>
            <th style="width:2.5rem">可调拨</th>
            <th style="width:2.5rem">操作</th>
        </tr>
        <tbody id="transferProductsPlanList">
            <tr>
                <td>4668<hr />A01-15<hr />（邻记优选）老北京麻辣方便面65g*40</td>
                <td>12<hr />48</td>
                <td>20</td>
                <td>16</td>
                <td><button class="linkButton" style="display: inline;" onclick="javascript:processSelectedProduct(4668);">拣</button></td>
            </tr>
            <tr>
                <td>16160<hr />B02-02<hr />清风绿茶茉香2层200抽3包（BR38SGZ） *16提/箱</td>
                <td>0<hr />8</td>
                <td>2</td>
                <td>4</td>
                <td><button class="linkButton" style="display: inline;" onclick="javascript:processSelectedProduct(16160);">拣</button></td>
            </tr>
        </tbody>
    </table>

    <hr  style="margin: 1rem 0;"/>
    <h1>已提交调拨单</h1>
    <div style="float: none; clear: both">
        <input class="submit" type="button" value="查找" onclick="javascript:getTransferOrder();">
        <input style="float: right; display: none;" class="input_default" id="searchDate" type="text" value="<?php echo date('Y-m-d', time());?>">
        <input style="float: right; display: none;" class="input_default" id="searchBarcode" type="text" value="" placeholder="条码">
    </div>
    <table class="list" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
        <tr id="product_relevant" style="display:;">
            <th style="width:2.5rem">调拨单号</th>
            <th style="width:2.5rem">状态</th>
            <th style="width:2.5rem">类型</th>
            <th style="width:2.5rem">数量</th>
            <th style="width:2.5rem">操作</th>
        </tr>
        <tbody id="listItem">
        <!-- Scanned Product List -->
        </tbody>
    </table>
</div>

</div>
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

$(document).ready(function () {
    startTime();

    //Get RegMethod
    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=getWarehouseId',
        data : {
            method : 'getWarehouseId'
        },
        success : function (response){
            // console.log(response);
            if(response){
                var jsonData = eval(response);
                var html = '<option value=0>-请选择目的仓库-</option>';
                $.each(jsonData, function(index, value){
                    html += '<option value='+ value.warehouse_id +' >' + value.title + '</option>';
                });

                $('#toWarehouseId').html(html);
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


function processSelectedProduct(id){
    $('#product').val(id);
    handleList();
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
    $("#"+operation_id).html('<input style="height:3em;width:3em;" class="qtyopt" type="button" value="-" onclick="javascript:qtyminus(\'' +
        id + '\',1,1,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+" onclick="javascript:qtyminus(\'' +
        id + '\',2,1,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+10" onclick="javascript:qtyminus(\'' +
        id + '\',2,10,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt" type="button" value="-10" onclick="javascript:qtyminus(\'' +
        id + '\',1,10,\''+prodId+'\',\''+planId+'\')">');
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
        if($(prodId).val() <= $(planId).text() -num) {
            var qty = parseInt($(prodId).val()) + num;
            $(prodId).val(qty);
        }
    }
}
function inventoryMethodHandler(method){

    var selectedToWarehouseId = $('#toWarehouseId').val();
    var deliverDate = $("#deliverDate").val();

    $('#selectedWarehouse').html($('#toWarehouseId option:selected').text());
    $('#selectedDeliverDate').html($('#deliverDate').val());

    if(selectedToWarehouseId == 0){
        $('#message').html('指定配送仓库及改仓库订单配送日期');
        $('#message').show();
        alert('请选择仓库');
        return;
    }
    var methodId = "#"+method;
    $('#method').val(method);
    $('#label').html($(methodId).text());
    $('#invMethods').hide();
    $('#message').html('');
    $('#itemList').show();
    $('#message').show();
    $('#scanner').show();
    //$('#creat_shipping_cost').show();
    $("#return_index").show();


    $('title').html($(methodId).text());
    $('#logo').html('鲜世纪仓库管理－'+$(methodId).text());

    if(method == 'getBoxBatchInfo'){
        $('#container_lists').show();
        $('#product_lists').hide();

        $('#transferProduct').val(1); //周转筐调拨
        $('#message').html('确认订单配送日期，注意滞留订单独立调拨。');

        getBoxBatchInfo(global.warehouseId,selectedToWarehouseId,deliverDate);
    } else {
        $('#getplanned').hide();
    }
}

function getBoxBatchInfo(doWarehouseId,toWarehouseId,deliverDate){
    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=getBoxBatchInfo',
        data : {
            method : 'getBoxBatchInfo',
            data : {
                doWarehouseId : doWarehouseId,
                toWarehouseId : toWarehouseId,
                deliverDate : deliverDate
            }
        },
        success : function (response , status , xhr){
            if(response){
                console.log(response);
                var jsonData = $.parseJSON(response);
            }
        },
        complete : function(){
        }
    });
}

function getTransferBoxes(doWarehouseId,toWarehouseId, deliverDate,type){
    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=getTransferBoxes',
        data : {
            method : 'getTransferBoxes',
            data : {
                doWarehouseId : doWarehouseId,
                toWarehouseId : toWarehouseId,
                deliverDate : deliverDate,
                type : type,
            }
        },
        success : function (response , status , xhr){
            if(response){
                // console.log(response);
                var jsonData = $.parseJSON(response);

                if(jsonData.return_code == 'ERROR'){
                    alert("错误，缺少关键数据");
                    location.reload();
                }

                global.readyBoxes = jsonData.return_data.readyBoxes;
                global.transferringBoxes = jsonData.return_data.transferringBoxes;
            }
        },
        complete : function(){
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
    $('#currentTime2').html(year+"/"+month+"/"+day+" "+h+":"+m+":"+s);
    t=setTimeout('startTime()',500)
}

function checkTime(i)
{
    if (i<10)
    {i="0" + i}
    return i
}


function getSetDate(dateGap,dateFormart) {
    var dd = new Date();
    dd.setDate(dd.getDate()+dateGap);//获取AddDayCount天后的日期

    return dd.Format(dateFormart);

    //console.log(getSetDate(1,'yyMMdd')); //Tomorrow
    //console.log(getSetDate(-1,'yyMMdd')); //Yesterday
}

function handleList(){
    var scanning_id = "#product";
    var input_id = "#product_make_hand";
    var rawId = $(scanning_id).val().trim() == '' ? $(input_id).val().trim() : $(scanning_id).val().trim();
    var type = $('#transferProduct').val();
    if (type == 1) {
        var id = rawId.substr(0,6);//Get 18 code
    } else if (type == 2) {
        var id = rawId.substr(0,18);//Get 18 code
    }
// console.log(id);
    addItem(id);
}

// function handleProductList(){
//     var rawId = $('#barcode').val();
//     id = rawId.substr(0,18);//Get 18 code
//
//     var barCodeId = "#bd"+id;
//     if($(barCodeId).length > 0){
//         $('#barcode').val('');
//         return qtyadd2(id);
//     }
//     else{
//         addItem(id);
//     }
// }

function resetScan(){
    $('#product').val('');
    $('#product_make_hand').val('');
    $('#product').focus();
    $('#product_make_hand').focus();
}

function addItem(id){
    var selectedWarehouse = $('#selectedWarehouse').html();
    var selectedDeliverDate = $('#selectedDeliverDate').html();
    var type = $('#transferProduct').val();
    if(id !== ''){
        //TODO 条码处理
        resetScan();

        if (type == 1) {
            /*zx
            * 扫描周转筐*/
            var findAddedItem = $("#boxHold"+id).length;
            if(findAddedItem){
                alert(id+'已经添加');
                return false;
            }
            //排除已调拨
            if(global.transferringBoxes[id] !== undefined){
                alert(id+'已经添加, 调拨单号'+global.transferringBoxes[id]['relevant_id']+', 添加时间'+global.transferringBoxes[id]['date_added']);
                return false;
            }

            //排除非选择配送日订单
            if(global.readyBoxes[id] == undefined){
                alert(id+'，不可调拨，不在仓库['+selectedWarehouse+']计划配送日期['+selectedDeliverDate+']内。');
                return false;
            }

            if(id>0){
                var html = '' +
                    '<tr class="barcodeHolder" boxid="'+id+'" id="boxHold'+ id +'">' +
                    '<td>' +
                    '<span style="display:none;" name="itemCode" >' + id + '</span>' +
                    '<span id="box'+ id +'">'+id+'</span>' +
                    '</td>' +

                    '<td>' +
                    '<span id="deliver_order'+ id +'">'+global.readyBoxes[id]['deliver_order_id']+'[共'+global.readyBoxes[id]['frame_count']+'筐]</span><br />' +
                    '[<spans style="font-size: 0.8rem" id="order'+ id +'">'+global.readyBoxes[id]['order_id']+'</span>]' +
                    '</td>' +

                    '<td>' +
                    '<span id="pos'+ id +'">'+global.readyBoxes[id]['pos']+'</span>' +
                    '</td>' +

                    '<td>' +
                    '<input class="qtyopt style_red" type="button" value="-" onclick="javascript:removeItem(\''+'boxHold\',\''+ id +'\')" >' +
                    '</td>' +
                    '</tr>';

                $('#itemInfo').append(html);
            } else {
                alert(id+'错误的条码');
                return false;
            }
        } else if (type == 2) {
            /*zx
            * 扫描商品*/
            //排除已调拨
//            if(global.transferringBoxes[id] !== undefined){
//                if (global.transferringBoxes[id]['quantity'] >= global.readyBoxes[id]['quantity']) {
//                    alert(id+'已经全部添加');
//                    return false;
//                }
//            }

            //排除非选择配送日订单
            if(global.readyBoxes[id] == undefined){
                alert(id+'，不可调拨，不在仓库['+selectedWarehouse+']计划配送日期['+selectedDeliverDate+']内。');
                return false;
            }
            // console.log(id);return false;

            if(id>0){
                var id = global.readyBoxes[id]['product_id'];
                var name = global.readyBoxes[id]['name'];
                var quantity = global.readyBoxes[id]['quantity'];
                var deliver_order_id = global.readyBoxes[id]['deliver_order_id'];
                var inv_comment = global.readyBoxes[id]['inv_comment'];
                var products_num = global.readyBoxes[id]['products_num'];
                var deliver_order_ids = deliver_order_id.split(',');
                var inv_comments = inv_comment.split(',');
                var products_nums = products_num.split(',');
//                console.log(deliver_order_id);
//                console.log(deliver_order_ids);
//                console.log(inv_comment);
//                console.log(inv_comments);
//                var html = "<tr><th>do单号</th><th>货位号</th><th>件数</th></tr>";
//                for (var index in deliver_order_ids) {
//                    html += "<tr><th>"+deliver_order_ids[index]+"</th><th>"+inv_comments[index]+"</th><th>"+products_nums[index]+"</th></tr>";
//                }
//                $("#productsInfoDo2").html(html);
//                $("#productsHoldDo2").show();

                if ($("#box"+id).text() > 0) {
                    make_product_information(id,name,quantity,$("#pos"+id).text(),"productsHoldDo1","productsInfoDo1");
                } else {
                    make_product_information(id,name,quantity,0,"productsHoldDo1","productsInfoDo1");
                }
            } else {
                alert(id+'错误的条码');
                return false;
            }
        } else {
            alert("页面错误，请刷新后重试");
        }
    }
}
//商品栏提交生成
function addProducts(id) {
    removeItem('boxHold',id);
    /*zx
    * 判断是否是商品，如果是先进行数量的判断生成对应的样式*/
    var html = '' +
        '<tr class="barcodeHolder" boxid="'+id+'" id="boxHold'+ id +'">' +
        '<td>' +
        '<span style="display:none;" name="itemCode" >' + id + '</span>' +
        '<span id="box'+ id +'">'+id+'</span>' +
        '</td>' +
        '<td>' +
        '<span style="display:none;" name="itemCode" >' + global.readyBoxes[id]['name'] + '</span>' +
        '<span id="name'+ id +'">'+global.readyBoxes[id]['name']+'</span>' +
        '</td>' +

        '<td>' +
        '<span id="deliver_order'+ id +'">'+global.readyBoxes[id]['quantity']+'</span><br />' +
        // '[<spans style="font-size: 0.8rem" id="order'+ id +'">'+global.readyBoxes[id]['order_id']+'</span>]' +
        // '</td>' +

        '<td>' +
        '<span id="pos'+ id +'">'+$("#"+id).val()+'</span>' +
        '</td>' +

        '<td>' +
        '<input class="qtyopt style_red" type="button" value="-" onclick="javascript:removeItem(\''+'boxHold\',\''+ id +'\')" >' +
        '</td>' +
        '</tr>';

    $('#itemInfo').append(html);
    $("#productsInfoDo1").html('');
    $("#productsInfoDo2").html('');
}


function removeItem(divId,id){
    var item = '#'+divId+id;
    $(item).remove();
}

function addTransferOrder(){
//    var shipping_cost = parseInt($("#shipping_cost").val());
//    var warehouse_cost = parseInt($("#warehouse_cost").val());
//    if(shipping_cost > 0){
//    } else {
//        alert('请填写调拨运费');
//        return false;
//    }
//    if(warehouse_cost > 0){
//    } else {
//        alert('请填写仓库成本');
//        return false;
//    }

    var shipping_cost=0;
    var warehouse_cost=0;
    var type = $('#transferProduct').val();
    if(!confirm('确认要添加新的调拨单吗?')){
        return;
    }

    var toWarehouseId = $('#toWarehouseId').val();
    var doWarehouseId = global.warehouseId;
    var deliverDate = $("#deliverDate").val();

    $('#itemInfo tr').each(function(){
        var id = $(this).attr('boxid');
        if (type == 1) {
            global.addTranseBoxList[id] = {};
            global.addTranseBoxList[id]['box_id'] = id;
            global.addTranseBoxList[id]['order_id'] = global.readyBoxes[id]['order_id'];
            global.addTranseBoxList[id]['deliver_order_id'] = global.readyBoxes[id]['deliver_order_id'];
            global.addTranseBoxList[id]['pos'] = global.readyBoxes[id]['pos'];
        } else if (type == 2) {
            global.addTranseBoxList[id] = {};
            global.addTranseBoxList[id]['product_id'] = id;
            global.addTranseBoxList[id]['order_id'] = global.readyBoxes[id]['order_id'];
            global.addTranseBoxList[id]['deliver_order_id'] = global.readyBoxes[id]['deliver_order_id'];
            global.addTranseBoxList[id]['quantity'] = $("#pos"+id).text();
        }
    });

    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=addTransferOrder',
        data : {
            method : 'addTransferOrder',
            data : {
                userId : global.userId,
                doWarehouseId : doWarehouseId,
                toWarehouseId : toWarehouseId,
                deliverDate : deliverDate,
                addType : type,
                shipping_cost:shipping_cost,
                warehouse_cost:warehouse_cost,
                addList : global.addTranseBoxList,
            }
        },
        success : function (response , status , xhr){
            if(response){
                // console.log(response);

                var jsonData = $.parseJSON(response);
                if (jsonData.return_code == "SUCCESS") {
                    alert("生成成功");
                } else {
                    alert("失败");
                }
            }
        },
        complete : function(){

            $('#itemInfo').html('');
            $("#productsInfoDo1").html('');
            $("#productsInfoDo2").html('');
            getTransferOrder();
        }
    });
    $("#shipping_cost").val('');
    $("#warehouse_cost").val('');
}

function getTransferOrder(){
    // var type = $('#transferProduct').val();
    var toWarehouseId = $('#toWarehouseId').val();
    var doWarehouseId = global.warehouseId;
    var deliverDate = $("#deliverDate").val();

    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=getTransferOrder',
        data : {
            method : 'getTransferOrder',
            data : {
                doWarehouseId : doWarehouseId,
                toWarehouseId : toWarehouseId,
                deliverDate : deliverDate,
                addList : global.addTranseBoxList
            }
        },
        success : function (response , status , xhr){
            if(response){
                // console.log(response);

                var jsonData = $.parseJSON(response);
                //alert(jsonData.message);

                var html = '';
                $.each(jsonData.return_data, function(idx,val){
                    html += '<tr>';
                    html += '<td>' + val.relevant_id + '<br /><span class="f0_7rem">[' + val.date_added + ']</span></td>';
                    html += '<td class="f0_8rem">'+ val.status_name+'</td>';
                    if (val.relevant_type == 1) {
                        html += '<td class="f0_8rem">周转筐</td>';
                    } else if (val.relevant_type == 2) {
                        html += '<td class="f0_8rem">商品</td>';
                    }
                    html += '<td>'+ val.num+'</td>';
                    if(val.relevant_status_id == 1 || val.relevant_status_id == 2){
                        html += '<td><button class="linkButton" onclick="javascript:cancelTransferOrder('+val.relevant_id+');">取消</button></td>';
                        // html += '<td><button class="linkButton" onclick="javascript:addOrderProductToInv_pre('+val.relevant_id+');">出库</button></td>';
                    }else{
                        html += '<td></td>';
                    }
                    html += '</tr>';
                });
                $("#listItem").html(html);
            }
        },
        complete : function(){
        }
    });
}

function cancelTransferOrder(id){
    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=cancelTransferOrder',
        data : {
            method : 'cancelTransferOrder',
            data : {
                reqId : id,
                userId : global.userId,
            }
        },
        success : function (response , status , xhr){
            if(response){
                // console.log(response);
                if (jsonData.return_code == "SUCCESS") {
                    alert("成功");
                } else {
                    alert("失败");
                }
            }
        },
        complete : function(){
            getTransferOrder();
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
                window.location = 'inventory_login.php?return=r.php&ver=db&ver=db';
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