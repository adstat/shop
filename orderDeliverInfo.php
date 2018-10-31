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
    exit("订单货位仅限指定人员操作, 请返回");
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>订单货位管理</title>
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

    global.updateList = '';
</script>
</head>

<div style="background-color: #FFFFFF;height:2.6rem; width: 100%">
    <div align="left" style="float: left; margin: 0.5rem;">
        <img src="view/image/logo.png" align="absmiddle" style="width:5rem; "/>
    </div>
    <div align="right">
        <button class="linkButton" id="backToIndex" style="display: inline;float:left" onclick="javascript:location='i.php?auth=xsj2015inv';">首页</button>
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
    <div id="message" class="message style_light">订单货位更新<br />用于分拣出库班组长查询修正订单货位，未分拣、非本仓订单、已配送出库的订单, 不可更改货位。</div>

    <div id="inv_control" align="center">
        <div id="itemList" name="itemList" method="POST">

            <div id="scanner">
                <input name="barcode" id="barcode" rows="1" maxlength="19" autocomplete="off" placeholder="订单号" style="ime-mode:disabled; font-size: 1.2rem"/>
                <button class="linkButton" onclick="javascript:getOutBasicInfo();">搜索</button>
            </div>

            <div id="order_info"></div>

            <hr />
            <div id="updateHistory" style="display: none">
                [临时页面记录]
                <div id="updateList"></div>
                <hr />
            </div>

            <div style="float:left">当前时间: <span id="currentTime2"><?php echo date('Y-m-d H:i:s', time());?></span></div>
            <div style="float: none; clear: both"></div>
        </div>

    </div>
</div>


<script>
    $('#barcode').focus();

    function getOutBasicInfo(){
        var order_id = $("#barcode").val();
        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=getCheckOrdersInfo',
            dataType: 'json',
            data : {
                method : 'getCheckOrdersInfo',
                data: {
                    warehouse_id: global.warehouseId,
                    order_id: order_id
                }
            },
            success : function (response , status , xhr){

                console.log(response.length);

                if(response.length){
                    var html = "";
                    $.each(response, function(i, v){
                        html += "<div style='font-size: 1rem; padding:0.3rem;'>订单号:"+ v.order_id+", 当前货位:" + v.inv_comment + "</div>";
                        if(v.order_status_id == 6 || v.order_status_id == 8){
                            html += "<div >货位号:"+"<input id='inv_comment' style='width:6rem;height:2rem;border:1px solid; font-size: 1.2rem; font-weight: bold' value='"+v.inv_comment +"'>";
                            html += " <br /><button class='linkButton' onclick='javascript:addInvComment();'>更新</button>";
                            html += "</div>";

                        }else{
                            html +="<span style='font-size: 13px;'>未分拣完成, 不可更改货位</span>";
                        }

                        global.updateList = "#订单号:"+ v.order_id+", 原货位:" + v.inv_comment;
                    });

                    $('#order_info').html(html);
                }
                else{
                    alert('订单[#'+order_id+']未分拣，或非本仓订单或已配送完成, 不可更改货位, 不可更改货位');
                }

                $("#inv_comment").focus();
            }
        });
    }


    function addInvComment(){
        var order_id = $("#barcode").val();
        var inv_comment = $("#inv_comment").val();


        if(confirm("确认将订单【"+order_id+"】货位为【"+inv_comment+"】?")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php?method=addInvComment',
                data: {
                    method: 'addInvComment',
                    data: {
                        warehouse_id: global.warehouseId,
                        user_id: global.userId,
                        invComment: inv_comment,
                        order_id: order_id
                    }
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response)

                    if(jsonData == 1){
                        alert('订单[#'+order_id+']货位已更新。');

                        var updateList = $("#updateList").html();
                        updateList += global.updateList + ', 新货位:'+inv_comment + '<br />';

                        $("#updateHistory").show();
                        $("#updateList").html(updateList);

                        $('#order_info').html('');
                        $('#barcode').val('');
                    }else{
                        alert('订单[#'+order_id+']货位更新失败。');
                    }

                }
            });
        }
    }
</script>
</body>
</html>
