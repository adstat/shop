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

if(!in_array($_COOKIE['user_group_id'],$inventory_user_admin)){
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    exit("可售库存管理仅限指定人员操作, 请返回");
}

$date = date('Y-m-d');
$dateList = array();
$dateList[] = date("Y-m-d", strtotime("0 day"));
$dateList[] = date("Y-m-d", strtotime("-1 day"));
$dateList[] = date("Y-m-d", strtotime("-2 day"));
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>可售库存管理</title>
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

#list td{
    background-color:#d0e9c6;
    color: #000;
    height: 2.5em;

    border-radius: 0.2em;
    box-shadow: 0.1em rgba(0, 0, 0, 0.2);
}

#list th{
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

.linkButton {width: 2.4rem; height: 1.4rem; margin: 0.3rem; padding:0.2rem; font-size: 0.9rem; color:#ffffff; border-radius: 0.2rem; background-color: #DF0000;}
</style>

<style media="print">
    .noprint{display:none;}
</style>

<script>
    var global = {};
    global.warehouseId = '<?php echo $_COOKIE['warehouse_id'];?>';
    global.userId = '<?php echo $_COOKIE['inventory_user_id'];?>';

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
        <button class="linkButton" id="backToIndex" style="display: inline;float:left" onclick="javascript:location='i.php?auth=xsj2015inv';">首页</button>
        <button class="linkButton" id="reloadPage" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>
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
    <div id="message" class="message style_light">可售库存管理-分拣缺货退回计划<br />当前仅可处理2018-03-25之后分拣完成的订单，先处理退回再作废，作废后不可再退<br />每两小时系统处理提交的计划，添加到前台可售库存</div>

    <div id="inv_control" align="center">
        <div id="invMethods">
            分拣日期 <input class="input_default" list="date_list" id="setDate" type="text" value="<?php echo date('Y-m-d', time());?>" autofocus="autofocus">
            <!-- HTML5表单列表 -->
            <datalist id="date_list">
                <?php
                    foreach($dateList as $m){
                        echo '<option label="'.$m.'" value="'.$m.'" />';
                    }
                ?>
            </datalist>
            <hr />
            <button id="containerQuery" class="invopt" style="display: block" onclick="javascript:inventoryMethodHandler('getInventorySortingReturn');">分拣缺货退回</button>
        </div>

        <div id="itemList" name="itemList" method="POST" style="display: none;">

            <div id="scanner" style="display: none">
                <input name="barcode" id="barcode" rows="1" maxlength="19" autocomplete="off" placeholder="单个商品编号" style="ime-mode:disabled; font-size: 1.2rem"/>
                <button class="linkButton" onclick="javascript:getInventorySortingReturnById();">搜索</button>
            </div>

            <table id="itemHold" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                <caption>
                    <strong>分拣日期：</strong><span id="selectedDate"></span>
                </caption>
                <tr>
                    <th align="left">商品</th>
                    <th style="width:6rem">订单-货位-缺</th>
                    <th style="width:3rem">未拣</th>
                    <th style="width:2.5rem">操作</th>
                </tr>
                <tbody id="itemInfo">
                <!-- Scanned Itme List -->
                    <tr><td colspan="4"><button class="linkButton" style="width: 6rem" onclick="javascript:getInventorySortingReturn(0);">列出分拣缺货</button></td></tr>
                </tbody>
            </table>

            <script type="text/javascript">
                $("input[name='barcode']").keyup(function(){
                    var tmptxt=$(this).val();
                    $(this).val(tmptxt.replace(/\D/g,''));

                    //if(tmptxt.length >= 5){
                    //  handleProductList2();
                    //}
                    //$(this).val("");
                }).bind("paste",function(){
                        var tmptxt=$(this).val();
                        $(this).val(tmptxt.replace(/\D/g,''));
                    });
                //$("input[name='product']").css("ime-mode", "disabled");
            </script>

            <div style="float:left">当前时间: <span id="currentTime2"><?php echo date('Y-m-d H:i:s', time());?></span></div>
            <div style="float: none; clear: both"></div>
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
            console.log(response);
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


function inventoryMethodHandler(method){
    var setDate = $("#setDate").val();

    $('#selectedDate').html($('#setDate').val());

    var methodId = "#"+method;
    $('#method').val(method);
    $('#label').html($(methodId).text());
    $('#invMethods').hide();
    $('#itemList').show();
    $('#message').show();
    $('#scanner').show();
    $("#return_index").show();


    $('title').html($(methodId).text());
    $('#logo').html('鲜世纪仓库管理－'+$(methodId).text());

    if(method == 'getInventorySortingReturn'){
        console.log('Method:'+method);
        //$('#message').html('xxx');
    }
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
    var rawId = $('#barcode').val();
    id = rawId.substr(0,18);//Get 18 code

    addItem(id);
}

function handleProductList(){
    var rawId = $('#barcode').val();
    id = rawId.substr(0,18);//Get 18 code

    var barCodeId = "#bd"+id;
    if($(barCodeId).length > 0){
        $('#barcode').val('');
        return qtyadd2(id);
    }
    else{
        addItem(id);
    }
}

function resetScan(){
    $('#barcode').val('');
    $('#barcode').focus();
}

function removeItem(divId,id){
    var item = '#'+divId+id;
    $(item).remove();
}

function qtyadd2(id){
    var prodId = "#"+id;
    var qty = parseInt($(prodId).val()) + 1;
    $(prodId).val(qty);

    //locateInput();

    console.log(id+':'+qty);
}

function qtyminus2(id){
    var prodId = "#"+id;


    if($(prodId).val() > 1 || $("#method").val() == 'inventoryAdjust'){
        var qty = parseInt($(prodId).val()) - 1;
        $(prodId).val(qty);
    }
    else{
        var barcodeId = '#bd'+id;
        $(barcodeId).remove();
    }

    //locateInput();

    console.log(id+':'+qty);
}


function addInventorySortingReturn(key,product_id,opt){

    if(opt=='add'){
        if(!confirm('确认要返还吗？此操作不可撤销。')){
            return;
        }
    }

    if(opt=='del'){
        if(!confirm('确认要作废吗？此操作不可撤销。')){
            return;
        }
    }

    var doWarehouseId = global.warehouseId;
    var setDate = $("#setDate").val();
    var qty =  $('#qty_'+key).val();

    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=addInventorySortingReturn',
        data : {
            method : 'addInventorySortingReturn',
            data : {
                userId : global.userId,
                doWarehouseId : doWarehouseId,
                setDate : setDate,
                item_key : key,
                productId : product_id,
                quantity : qty,
                opt: opt
            }
        },
        success : function (response , status , xhr){
            if(response){
                console.log(response);
            }
        },
        complete : function(){
            $('#op_'+key).html('');
        }
    });
}

function getInventorySortingReturnById(){
    var productId = $("#barcode").val();
    getInventorySortingReturn(productId);
}

function getInventorySortingReturn(product_id){
    var doWarehouseId = global.warehouseId;
    var setDate = $("#setDate").val();

    $.ajax({
        type : 'POST',
        url : 'invapi.php?method=getInventorySortingReturn',
        data : {
            method : 'getInventorySortingReturn',
            data : {
                doWarehouseId : doWarehouseId,
                setDate : setDate,
                productId : product_id
            }
        },
        success : function (response , status , xhr){
            if(response){
                var jsonData = $.parseJSON(response);
                //console.log(jsonData.return_data.sortingReturns);
                var sortingReturns = jsonData.return_data.sortingReturns;
                var returnAdded = jsonData.return_data.returnAdded;
                var html = '';
                $.each(sortingReturns, function(idx,val){
                    var voidItem = parseInt(val.void);

                    html += '<tr>';
                    html += '<td>';
                    html += val.product_id + '<br /><span class="f0_7rem">[' + val.product_name + ']</span>';
                    if(voidItem == 0){
                        html += '<button class="linkButton" onclick="javascript:addInventorySortingReturn(\''+val.item_key+'\','+ val.product_id +',\'del\');">作废</button>';
                    }
                    html += '</td>';
                    html += '<td class="f0_8rem">'+ val.order_inv.replace(/,/g, "<br>")+'</td>';
                    html += '<td>';
                        html += val.miss_qty + '<br /><span class="f0_8rem">共' + val.order_qty+'</span>';
                        if(parseInt(val.added_qty)>0){
                            html += '<br /><span class="style_light f0_8rem">已返'+val.added_qty+'件</span>';
                        }
                        if(parseInt(val.void_quantity)>0){
                            html += '<br /><span class="style_light f0_8rem">作废'+val.void_quantity+'件</span>';
                        }
                    html += '</td>';
                    html += '<td id="op_'+val.item_key+'">';
                    if(parseInt(val.added_qty) < parseInt(val.miss_qty) && parseInt(val.block)==0 && voidItem==0){
                        html += '<input class="qtyopt style_light" id="qty_'+val.item_key+'" value="'+ (parseInt(val.miss_qty)-parseInt(val.added_qty)) +'" />';
                        html += '<button class="linkButton" onclick="javascript:addInventorySortingReturn(\''+val.item_key+'\','+ val.product_id +',\'add\');">退回</button>';
                    }
                    html += '</td>';
                    html += '</tr>';
                });
                $("#itemInfo").html(html);
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