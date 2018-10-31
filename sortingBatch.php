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
<title>鲜世纪整件波次分拣</title>
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

#listhold td, #itemHold td{
    background-color:#d0e9c6;
    color: #000;
    height: 2.5em;

    border-radius: 0.2em;
    box-shadow: 0.1em rgba(0, 0, 0, 0.2);
}

#listhold td, #itemHold th{
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

    global.batchInfo = {};

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
        <button class="linkButton" style="display: inline;float:left;" onclick="window.location.href='i.php?auth=xsj2015inv&ver=db'">首页</button>
        <button class="linkButton" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>
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
<div id="invMethods" style="display: none">
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

<div id="itemList" name="itemList" method="POST" style="display: ;">

    <table id="listHold" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
        <caption id="transferSettings">
            <strong>目的仓库：</strong><span id="selectedWarehouse"></span><br />
            <strong>配送日期：</strong><span id="selectedDeliverDate"></span>
        </caption>
        <tr>
            <td><div id="batchInfo"></div></td>
        </tr>
        <tr>
            <td style="background-color: #d0e9c6">
                <input class="submit" id="submitReturnProduct" type="button" value="生成波次" onclick="javascript:addBoxBatchOrder();">
                <input class="submit" id="" type="button" value="刷新" onclick="javascript:getBoxBatchInfo();">
            </td>
        </tr>
    </table>
    <div id="sku_barcode" style="display:none; ">
        <div class="f1_0rem" style="font-weight: bold; float: left;" id="itemBrief"></div>
        <table id="productsHoldDo1" border="1px" style="margin:1rem 0; width:100%;display:;border:1px solid;solid-color: black;" cellpadding=2 cellspacing=3>
            <caption>
                <strong>目的仓库：</strong><span id="showWarehouse"></span><br />
                <strong>配送日期：</strong><span id="showDeliverDate"></span>
            </caption>
            <tbody id="productsInfoDo1">
            </tbody>
        </table>
        <form method="post" onsubmit="handleList(); return false;">
            <!--                <input id="product_make_hand" rows="1" maxlength="19" placeholder="请手动输入" style="font-size: 1.1em;margin-top: 0.5em;margin-bottom: 0.5em;ime-mode:disabled;background-color:#d0e9c6;height: 2em;display:none"/>-->
            <!--                <input class="addprod style_green" type="submit" id="make_hand" value ="添加" style="font-size: 1em; padding: 0.2em;display:none"/>-->
            <input name="product" id="product" rows="1" maxlength="19"  autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled;display:; height: 2em;"/></form>
        <!--            <input class="addprod style_green"  id="make_hand_hide" type="submit" value ="手动输入" onclick="javascript:clickProductInput();" style="font-size: 1em; padding: 0.2em;">-->
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
        </form>
        <hr />
        <div class="f0_8rem">绿色输入框<input class="style_green" value=" " style="width: 1rem">表示已保存，橙色<input class="style_light" value=" " style="width: 1rem">表示未操作。</div>
        <hr />

    </div>

    <table id="itemHold" border="0" style="width:100%; display:; " cellpadding=2 cellspacing=3>
        <caption>


<!--        <br />-->
<!--            波次操作绿色输入框<input class="style_green" value=" " style="width: 1rem">表示已保存，橙色<input class="style_light" value=" " style="width: 1rem">表示未操作;-->
        </caption>
        <tr>
            <th align="left">商品信息-货位</th>
            <th style="width:3rem">数量</th>
            <th style="width:2.5rem">操作</th>
        </tr>
        <tbody id="itemInfo">
        <!-- Scanned Itme List -->
        <tr></td></tr>
        </tbody>
    </table>




<!--    </div>-->
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
    <table id="productsHoldDo2" border="1px" style="width:100%;display:none;border:0px solid;solid-color: black;" cellpadding=2 cellspacing=3>
        <tbody id="productsInfoDo2">
        </tbody>
    </table>
    <div style="float: none; clear: both"></div>

    <hr  style="margin: 1rem 0;"/>
    <h1>已生成波次分拣单</h1>
    <div style="float: none; clear: both">
        <input class="submit" type="button" value="查找" onclick="javascript:getBoxBatchOrders();">
        <input style="float: right; display: none;" class="input_default" id="searchDate" type="text" value="<?php echo date('Y-m-d', time());?>">
        <input style="float: right; display: none;" class="input_default" id="searchBarcode" type="text" value="" placeholder="条码">
    </div>
    <table class="list" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
        <tr id="product_relevant" style="display:;">
            <th style="width:3.5rem">波次号</th>
            <th style="width:3.5rem">仓库</th>
            <th style="width:2.5rem">状态</th>
            <th style="width:2.5rem">数量</th>
            <th>操作</th>
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
    getBoxBatchOrderItem(108);return true;
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

function make_product_information(id,name,quantity,real_quantity,table_id,tbody_id,batch_id){

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
        batch_id + ','+id+');">提交</button></th></tr><tr id="clears'+
        id + '"><td id="'+
        plan_quantity_id+'" align="center" style="font-size:1.5em;"></td> <td id="'+
        quantity_id+'" align="center" style="font-size:1.4em;"></td> <td id="'+
        operation_id+'"></td></tr>');
    $("#"+product_name_id).html(name);
    $("#"+plan_quantity_id).html(quantity + '<span style="display:none;" name="productId" id="'+planId+'">' + quantity + '</span>');
    $("#"+quantity_id).html('<input class="qty" disabled="disabled"  id="'+prodId+'" value="' + real_quantity + '">');
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

    $('.selectedWarehouse').html($('#toWarehouseId option:selected').text());
    $('.selectedDeliverDate').html($('#deliverDate').val());

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
        //$('#container_lists').show();
        $('#product_lists').hide();

        $('#listHold').show();
        $('#itemHold').hide();
        $('#sku_barcode').hide();

        $('#transferProduct').val(1); //周转筐调拨
        $('#message').html('确认订单配送日期，注意滞留订单独立调拨。');

        getBoxBatchInfo();
    } else {
        $('#getplanned').hide();
    }
    getBoxBatchOrders(1);

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
    // console.log(rawId);
        var id = rawId.substr(0,18);//Get 18 code
// console.log(id);
    addItem(id);
}


function resetScan(){
    $('#product').val('');
    $('#product_make_hand').val('');
    $('#product').focus();
    $('#product_make_hand').focus();
}

function addItem(id){
    console.log(id);
    if(id !== ''){
        //TODO 条码处理
        resetScan();

       if(global.transferringBoxes[id] == undefined){
               alert(id+'商品条码错误，请输入正确的条码');
               return false;
       }

        if(id>0){
            var id = global.transferringBoxes[id]['product_id'];
            var name = global.transferringBoxes[id]['name'];
            var quantity = global.transferringBoxes[id]['quantity'];
            var batch_id = global.transferringBoxes[id]['batch_id'];
            var inv_comment = global.transferringBoxes[id]['stock_area'];
            var products_num = global.transferringBoxes[id]['swap'];

            if (parseInt($("#qty_"+id).val()) >= 0) {
                make_product_information(id,name,quantity,$("#qty_"+id).val(),"productsHoldDo1","productsInfoDo1",batch_id);
            } else {
                make_product_information(id,name,quantity,products_num,"productsHoldDo1","productsInfoDo1",batch_id);
            }
        } else {
            alert(id+'错误的条码');
            return false;
        }
    } else {
        alert("页面错误，请刷新后重试");
    }

}
//商品栏提交生成
function addProducts(batch_id,id) {
    removeItem('boxHold',id);
    $("#qty_"+id).val($("#"+id).val());
// console.log(batch_id);
// console.log(id);
// console.log(id);
// console.log(id);
    updateBoxBatchOrder(batch_id,id);
    $("#productsInfoDo1").html('');
    $("#productsInfoDo2").html('');
}


function removeItem(divId,id){
    var item = '#'+divId+id;
    $(item).remove();
}


function getBoxBatchInfo(){
    var toWarehouseId = $('#toWarehouseId').val();
    var doWarehouseId = global.warehouseId;
    var deliverDate = $("#deliverDate").val();

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

                global.batchInfo = jsonData;

                $('#batchInfo').text('当前可分拣'+jsonData.return_data.products +'种商品,共'+ jsonData.return_data.quantity +'件。');
            }
        },
        complete : function(){
        }
    });
}


function addBoxBatchOrder(){
    if(!confirm('确认要添加新的整件波次吗?')){
        return;
    }

    if(global.batchInfo.return_data.quantity == 0){
        alert("当前仓库整件待分拣波为0，无需添加波次，请稍后再试");
        return false;
    }

    var toWarehouseId = $('#toWarehouseId').val();
    var doWarehouseId = global.warehouseId;
    var deliverDate = $("#deliverDate").val();

    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=addBoxBatchOrder',
        data : {
            method : 'addBoxBatchOrder',
            data : {
                userId : global.userId,
                doWarehouseId : doWarehouseId,
                toWarehouseId : toWarehouseId,
                deliverDate : deliverDate
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
            getBoxBatchInfo();
            getBoxBatchOrders();
        }
    });
}

function getBoxBatchOrders(type){
    // var type = $('#transferProduct').val();
    var toWarehouseId = $('#toWarehouseId').val();
    var doWarehouseId = global.warehouseId;
    var deliverDate = $("#deliverDate").val();
    var search_type = 0;
    if (type == 1) {
        search_type = 1
    }
    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=getBoxBatchOrders',
        data : {
            method : 'getBoxBatchOrders',
            data : {
                doWarehouseId : doWarehouseId,
                toWarehouseId : toWarehouseId,
                deliverDate : deliverDate,
                search_type : search_type
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
                    html += '<td>' + val.batch_id + '<br /><input type="hidden" id="getDeliverdate" value="'+val.deliver_date+'"/><span  class="f0_7rem">[' + val.date_added + ']</span></td>';
                    html += '<td id="getWarehouse" class="f0_8rem">'+ val.warehouse+'</td>';
                    html += '<td class="f0_8rem">'+ val.status_name+'</td>';
                    html += '<td>'+ val.quantity+'</td>';
                    if(val.batch_status_id == 1 || val.batch_status_id == 2 || val.batch_status_id == 4 || val.batch_status_id == 5){
                        html += '<td>' +
                            '<button class="linkButton" onclick="javascript:cancelBoxBatchOrder('+val.batch_id+');">取消</button>' +
                            '<button class="linkButton" onclick="javascript:printBoxBatchOrder('+val.batch_id+');">打印</button>' +
                            '<button class="linkButton" onclick="javascript:getBoxBatchOrderItem('+val.batch_id+');">分拣</button>' +
                            '</td>';
                        // html += '<td><button class="linkButton" onclick="javascript:addOrderProductToInv_pre('+val.relevant_id+');">出库</button></td>';
                    }else{
                        html += '<td>' +
                            '<button class="linkButton" onclick="javascript:getBoxBatchOrderItem('+val.batch_id+');">查看</button>' +
                            '</td>';
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

function printBoxBatchOrder(id){
    window.open("print_box_only_product_id.php?auth=xsj2015inv&ver=db&reqId="+id);
}

function getBoxBatchOrderItem(id){

    $('#showWarehouse').html($('#getWarehouse').text());
    $('#showDeliverDate').html($('#getDeliverdate').val());
    $('#listHold').hide();
    $('#itemHold').show();
    $('#sku_barcode').show();
    $('#itemBrief').text('');

    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=getBoxBatchOrderItem',
        data : {
            method : 'getBoxBatchOrderItem',
            data : {
                reqId : id,
                userId : global.userId
            }
        },
        success : function (response , status , xhr){
            if(response){
                var jsonData = $.parseJSON(response);
                // console.log(jsonData.return_data.batchDetail);
                var batchInfo = jsonData.return_data.batchInfo;
                var batchDetail = jsonData.return_data.batchDetail;


                var viewOnly = (parseInt(batchInfo.batch_status_id) == 6) ? true : false;

                var html = '';
                $.each(batchDetail, function(idx,val){
                    if(parseInt(val.product_id)>0){
                        global.transferringBoxes[val.product_id] = val;
                        global.transferringBoxes[val.sku_barcode] = val;
                        html += '<tr>';
                        html += '<td>';
                        html += val.product_id + ' <span class="f0_9rem">[' + val.stock_area + ']</span><br /><span class="f0_8rem">[' + val.name + ']</span><br /><span class="f0_7rem">[' + val.sku_barcode + ']</span>';
                        html += '</td>';
                        html += '<td>'+ val.quantity +'</td>';
                        html += '<td id="op_'+val.item_key+'">';
                        if(parseInt(val.quantity) > 0){
                            var thisQty = 0;
                            var thisStyle = 'style_light';
                            if(parseInt(val.swap) > 0){
                                thisQty = parseInt(val.swap);
                                thisStyle = 'style_green';
                            }
                            html += '<input type="hidden" id="plan_'+val.product_id+'" value="'+ (val.quantity) +'" />';
                            if(!viewOnly){
                                html += '<input class="qtyopt '+ thisStyle +'" id="qty_'+val.product_id+'" disabled="disabled" value="'+ thisQty +'" />';
                                // html += '<button class="linkButton" id="add_'+val.product_id+'" onclick="javascript:updateBoxBatchOrder(\''+val.batch_id+'\','+ val.product_id +');">确认</button>';
                            }else{
                                html += '<input readonly="readOnly" class="qtyopt '+ thisStyle +'" id="qty_'+val.product_id+'" value="'+ thisQty +'" />';
                            }
                        }
                        html += '</td>';
                        html += '</tr>';
                    }
                });
                if(!viewOnly){
                    $("#product").show();
                    html += '<tr/><td colspan="3">' +
                        '<button style="float:left" class="linkButton" onclick="javascript:getBoxBatchOrderItem('+id+');">刷新</button>' +
                        '<button style="float:right"class="linkButton" onclick="javascript:saveBoxBatchOrder('+id+');">结束波次操作</button>' +
                        '</td></tr>';
                } else {
                    $("#product").hide();
                }

                $("#itemInfo").html(html);
                $('#itemBrief').text('波次单#'+batchInfo.batch_id);
            }
        },
        complete : function(){
        }
    });
}

function saveBoxBatchOrder(id){
    if(!confirm('确认要结束这个波次吗?')){
        return;
    }

    if(!confirm('结束波次后不可再投篮，更改，请谨慎操作，确认吗?')){
        return;
    }

    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=submit_box_product_batch',
        data : {
            method : 'submit_box_product_batch',
            data : {
                reqId : id,
                username : '<?php echo $_COOKIE['inventory_user'];?>'
            }
        },
        success : function (response , status , xhr){
            if(response){
                console.log(response);

                var jsonData = $.parseJSON(response);
                if (jsonData.return_code == "SUCCESS") {
                    alert("保存成功");
                } else {
                    alert("保存失败");
                }
            }
        },
        complete : function(){
            getBoxBatchOrders();
            getBoxBatchInfo();

            $('#listHold').show();
            $('#itemHold').hide();
        }
    });

}

function updateBoxBatchOrder(barch_id, product_id){
    var planQty = $('#plan_'+product_id).val();
    var itemQty = $('#qty_'+product_id).val();
console.log(itemQty);

    if(itemQty>planQty){
        alert("不可大于计划数量!");

        $('#qty_'+product_id).val(planQty);
        itemQty = planQty;
        return false;
    }

    //已操作的样式
    $('#add_'+product_id).hide();
    $('#qty_'+product_id).attr("class","qtyopt style_gray");


    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=updateBoxBatchOrder',
        data : {
            method : 'updateBoxBatchOrder',
            data : {
                reqId : barch_id,
                productId : product_id,
                qty : itemQty,
                userId : global.userId
            }
        },
        success : function (response , status , xhr){
            if(response){
                console.log(response);

                var jsonData = $.parseJSON(response);
            }
        },
        complete : function(){
            //getBoxBatchOrders();
            //getBoxBatchInfo();
            getBoxBatchOrderItem(barch_id);
        }
    });
}

function cancelBoxBatchOrder(id){
    if(!confirm('确认要取消这个波次吗?')){
        return;
    }

    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=cancelBoxBatchOrder',
        data : {
            method : 'cancelBoxBatchOrder',
            data : {
                reqId : id,
                userId : global.userId
            }
        },
        success : function (response , status , xhr){
            if(response){
                console.log(response);

                var jsonData = $.parseJSON(response);
                if (jsonData.return_code == "SUCCESS") {
                    alert("取消成功");
                } else {
                    alert("取消失败");
                }
            }
        },
        complete : function(){
            getBoxBatchOrders();
            getBoxBatchInfo();

            $('#listHold').show();
            $('#itemHold').hide();
        }
    });
}

function backhome(){
    $('#content').show();
    $('#print').hide();
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