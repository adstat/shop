<?php
if (!isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv') {
    exit('Not authorized!');
}
include_once 'config_scan.php';
$inventory_user_admin = array('1', '22');
if (empty($_COOKIE['inventory_user'])) {
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
    <title>周转筐管理</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
            /*-webkit-appearance: none;*/
            /*outline: none;*/
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

        .invopt {
            background-color: #DF0000;
            width: 8em;
            height: 3em;
            line-height: 1em;
            padding: 0.5em 0.5em;
            margin: 0.1em 0.1em;
            font-size: 1em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .invback {
            background-color: #DF0000;
            width: 5em;
            height: 3em;
            line-height: 1em;
            padding: 0.5em 0.5em;
            margin: 0.1em 0.1em;
            font-size: 1em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .qtyopt {
            background-color: #DF0000;
            width: 2em;
            height: 1.8em;
            line-height: 1.2em;
            padding: 0.1em 0.1em;
            margin: 0.1em 0.1em;
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

        .submit {
            background-color: #DF0000;
            padding: 0.3em 0.8em;
            margin: 0.1em 0.1em;
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

        #inventory {
            width: 100%;
        }

        #product {
            margin: 0.5em auto;
            height: 1.5em;
            font-size: 0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }

        #barcode {
            margin: 0.5em auto;
            height: 1.5em;
            font-size: 0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }
        #container_lists {
            margin: 0.5em auto;
            height: 1.5em;
            font-size: 0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }

        .addprod {
            cursor: pointer;
            color: #fff;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .qty {
            width: 2em;
            height: 1.2em;
            font-size: 1.5em;
            text-align: center;
            background: none;
        }

        #inv_control {
            padding: 0.5em
        }

        .style_green {
            background-color: #117700;
            border: 0.1em solid #006600;
        }

        .style_lightgreen {
            background-color: #8FBB6C;
            border: 0.1em solid #8FBB6C;
        }

        .style_gray {
            background-color: #9d9d9d;
            border: 0.1em solid #888888;
        }

        .style_red {
            background-color: #DF0000;
            border: 0.1em solid #CC0101;
        }

        .style_yellow {
            background-color: #FF6600;
            border: 0.1em solid #df8505;
        }

        .style_light {
            background-color: #fbb450;
            border: 0.1em solid #fbb450;
        }

        .style_ok {
            background-color: #ccffcc;
            border: 0.1em solid #669966;
        }

        .style_error {
            background-color: #ffff00;
            border: 0.1em solid #ffcc33;
        }

        #productsInfo {
            border: 0.1em solid #888888;
        }

        #station {
            font-size: 1em;
        }

        .message {
            width: auto;
            margin: 0.5em;
            padding: 0.5em;
            text-align: center;

            border-radius: 0.3em;
            box-shadow: 0.2em rgba(0, 0, 0, 0.2);
        }

        #productsHold td {
            background-color: #d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold th {
            padding: 0.3em;
            background-color: #8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #itemHold td {
            background-color: #d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #itemHold th {
            padding: 0.3em;
            background-color: #8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
 #list td {
            background-color: #d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #list th {
            padding: 0.3em;
            background-color: #8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #list td {
            background-color: #d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #list th {
            padding: 0.3em;
            background-color: #8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .submit_s {
            padding: 0.2em 0.2em;
            margin: 0.1em 0.1em;
            font-size: 0.9em;
            text-decoration: none;
            border-radius: 0.2em;
            color: #333;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #invMovesHold th {
            padding: 0.3em;
            background-color: #f0ad4e;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #invMovesHold td {
            background-color: #ffffaa;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #invMovesPrintCaption {
            width: 100%;
            text-align: left;
        }

        #invMovesPrintHold {
            border-right: solid #000 1px;
            border-bottom: solid #000 1px;
        }

        #invMovesPrintHold th {
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

        #invMovesPrintHold td {
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

        #prtInvMoveType, #prtInvMoveTitle, #prtInvMoveTime {
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

        input#product {
            height: 1.2em;
            font-size: 1.1em;
        }

        .productBarcode {
            font-size: 14px;
        }

        .input_default {
            height: 1.8rem;
            font-size: 1rem;
            margin: 0.1rem 0;

            background-color: #e3e3e3;
            border-radius: 0.2rem;
            box-shadow: 0.1rem rgba(0, 0, 0, 0.2);
            padding-left: 0.2rem;
        }

        #orderMissing div {
            margin: 3px;
        }

        .w6rem {
            width: 6rem;
        }

        .w4rem {
            width: 4rem;
        }

        .w2rem {
            width: 2rem;
        }

        .f0_7rem {
            font-size: 0.7rem;
        }

        .f0_8rem {
            font-size: 0.8rem;
        }

        .f0_9rem {
            font-size: 0.9rem;
        }

        .f1_0rem {
            font-size: 1.0rem;
        }

        .f1_1rem {
            font-size: 1.1rem;
        }

        .f1_2rem {
            font-size: 1.2rem;
        }

        .linkButton {
            width: 2.4rem;
            height: 1.4rem;
            margin: 0.3rem;
            padding: 0.2rem;
            font-size: 0.9rem;
            color: #ffffff;
            border-radius: 0.2rem;
            background-color: #DF0000;
        }
    </style>

    <style media="print">
        .noprint {
            display: none;
        }
    </style>

    <script>
        var global = {};
        global.warehouseId = '<?php echo $_COOKIE['warehouse_id'];?>';
        global.userId = '<?php echo $_COOKIE['inventory_user_id'];?>';

        window.product_barcode_arr = {};
        window.product_inv_barcode_arr = {};
        <?php if(strstr($_COOKIE['inventory_user'], 'scfj')){ ?>
        $(document).keydown(function (event) {
            $('#product').focus();
        });
        <?php } ?>
    </script>
</head>

<div style="background-color: #FFFFFF;height:2.6rem; width: 100%">
    <!--    <div align="left" style="float: left; margin: 0.5rem;">-->
    <!--        <img src="view/image/logo.png" align="absmiddle" style="width:5rem; "/>-->
    <!--    </div>-->
    <div align="right">
        <button class="linkButton" id="backToIndex" style="display: inline;float:left"
                onclick="javascript:location='i.php?auth=xsj2015inv';">首页
        </button>
        <button class="linkButton" id="reloadPage" style="display: inline;float:left"
                onclick="javascript:location.reload();">返回
        </button>
        <?php echo $_COOKIE['inventory_user']; ?> 所在仓库: <?php echo $_COOKIE['warehouse_title']; ?>
        <button class="linkButton" onclick="javascript:logout_inventory_user();">退出</button>
        <?php if (in_array($_COOKIE['user_group_id'], $inventory_user_admin)) { ?>
            <script type="text/javascript">
                is_admin = 1;
            </script>
        <?php } ?>
    </div>
</div>

<div id="content" style="display: block">
    <div align="center" style="margin:0.5em auto;"></div>
    <div align="center" style="margin:0.5em auto;"></div>
    <!--<div id="message" class="message style_light">查询指定订单计划配送日期前三日周转筐使用情况</div>-->

    <div id="inv_control" align="center">
        <div id="invMethods">
            配送日期 <input class="input_default" id="deliverDate" type="text" value="<?php echo date('Y-m-d', time()); ?>">
            --- <input class="input_default" id="deliverDate" type="text"
                       value="<?php echo date('Y-m-d', time() + 1); ?>">
            <hr/>
            <button id="containerQuery" class="invopt" style="display: block"
                    onclick="javascript:inventoryMethodHandler('containerQuery');">周转筐查询
            </button>
        </div>

        <div id="itemList" name="itemList" method="POST" style="display: none;">

            <table id="itemHold" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                <!--        <caption>-->
                <!--            <strong>配送日期：</strong><span id="selectedDeliverDate"></span>-->
                <!--        </caption>-->
                <tr>
                    <th align="left">周转筐</th>
                    <th style="width:6rem">订单号<br/>DO单<br/>配送日</th>
                    <th style="width:6rem">提交时间</th>
                    <th style="width:2rem">货位</th>
                    <th style="width:2.5rem">提交人</th>
                </tr>
                <tbody id="itemInfo">
                <!-- Scanned Itme List -->
                </tbody>
            </table>


            <div id="scanner" style="display: none">
                <form method="post" onsubmit="handleList(); return false;">
                    按分拣信息查询：<input id="search_type" type="checkbox" style="ime-mode:disabled; font-size: 1.2rem"/><br/>
                    <input name="barcode" id="barcode" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描"
                           style="ime-mode:disabled; font-size: 1.2rem"/>
                    <input class="addprod style_green" type="submit" value="添加" style="font-size: 1em; padding: 0.2em"/>
                    <br />
                </form>

                <?php if(in_array($_COOKIE['user_group_id'],[1,22,24])){?>
                        <input  id="container_lists" rows="1" type="text"  autocomplete="off" placeholder="周转筐批量查询"
                               style="width:30%;height:60px;ime-mode:disabled; font-size: 1.2rem"/>
                        <input class="addprod style_green" type="button" value="批量查询" style="font-size: 1em; padding: 0.2em" onclick="handleList(2);"/>
                    <?php }?>
            </div>
            <script type="text/javascript">
                $("input[name='barcode']").keyup(function () {
                    var tmptxt = $(this).val();
                    $(this).val(tmptxt.replace(/\D/g, ''));

                    //if(tmptxt.length >= 5){
                    //  handleProductList2();
                    //}
                    //$(this).val("");
                }).bind("paste", function () {
                    var tmptxt = $(this).val();
                    $(this).val(tmptxt.replace(/\D/g, ''));
                });
                //$("input[name='product']").css("ime-mode", "disabled");
            </script>

            <div style="float:left">当前时间: <span id="currentTime2"><?php echo date('Y-m-d H:i:s', time()); ?></span>
            </div>

            <!--    <input class="submit" id="submitReturnProduct" type="button" value="提交" onclick="javascript:addTransferOrder();">-->

            <div style="float: none; clear: both"></div>
            <hr style="margin: 1rem 0;"/>
            <table  border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                <tr id="show_th_text"></tr>
                <tbody id="sql_result"></tbody>
            </table>
            <div style="display: none">
                <h1>已提交异常</h1>
                <div style="float: none; clear: both">
                    <input class="submit" type="button" value="查找" onclick="#">
                    <input style="float: right; display: none;" class="input_default" id="searchDate" type="text"
                           value="<?php echo date('Y-m-d', time()); ?>">
                </div>
                <table id="list" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                        <tr id="show_th_text"></tr>
                        <tbody id="sql_result"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<script>
    //JS Date Format Extend
    Date.prototype.Format = function (fmt) { //author: meizz
        var o = {
            "M+": this.getMonth() + 1,                 //月份
            "d+": this.getDate(),                    //日
            "h+": this.getHours(),                   //小时
            "m+": this.getMinutes(),                 //分
            "s+": this.getSeconds(),                 //秒
            "q+": Math.floor((this.getMonth() + 3) / 3), //季度
            "S": this.getMilliseconds()             //毫秒
        };
        if (/(y+)/.test(fmt))
            fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    }

    $(document).ready(function () {
        startTime();

        //Get RegMethod
//    $.ajax({
//        type : 'POST',
//        url : 'invapi.php?method=getWarehouseId',
//        data : {
//            method : 'getWarehouseId'
//        },
//        success : function (response){
//            console.log(response);
//            if(response){
//                var jsonData = eval(response);
//                var html = '<option value=0>-请选择目的仓库-</option>';
//                $.each(jsonData, function(index, value){
//                    html += '<option value='+ value.warehouse_id +' >' + value.title + '</option>';
//                });
//
//                $('#toWarehouseId').html(html);
//            }
//
//        }
//    });

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


    $(document).ready(function () {
        inventoryMethodHandler('containerQuery');
    });

    function inventoryMethodHandler(method) {
        var deliverDate = $("#deliverDate").val();

        $('#selectedDeliverDate').html($('#deliverDate').val());

        var methodId = "#" + method;
        $('#method').val(method);
        $('#label').html($(methodId).text());
        $('#invMethods').hide();
        $('#itemList').show();
        $('#message').show();
        $('#scanner').show();
        $("#return_index").show();


        $('title').html($(methodId).text());
        $('#logo').html('鲜世纪仓库管理－' + $(methodId).text());

        if (method == 'containerQuery') {
            console.log('Method:' + method);
            //$('#message').html('');
            //$('#message').hide();
            //$('#message').html('调拨周转筐前确认订单配送日期，注意滞留订单独立调拨。');
            //getTransferBoxes(global.warehouseId,selectedToWarehouseId,deliverDate);
        }
    }

    function getTransferBoxes(doWarehouseId, toWarehouseId, deliverDate) {
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=getTransferBoxes',
            data: {
                method: 'getTransferBoxes',
                data: {
                    doWarehouseId: doWarehouseId,
                    toWarehouseId: toWarehouseId,
                    deliverDate: deliverDate
                }
            },
            success: function (response, status, xhr) {
                if (response) {
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    if (jsonData.return_code == 'ERROR') {
                        alert("错误，缺少关键数据");
                    }

                    global.readyBoxes = jsonData.return_data.readyBoxes;
                    global.transferringBoxes = jsonData.return_data.transferringBoxes;
                }
            },
            complete: function () {
            }
        });
    }


    function playOverdueAlert() {
        //$('#player').attr('src',sound);
        $('.simpleplayer-play-control').click();
    }

    function stopOverdueAlert() {
        $('.simpleplayer-stop-control').click();
    }

    function startTime() {
        var today = new Date();
        var year = today.getFullYear();
        var month = today.getMonth() + 1;
        var day = today.getDate();

        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        // add a zero in front of numbers<10
        m = checkTime(m);
        s = checkTime(s);
        $('#currentTime').html(year + "/" + month + "/" + day + " " + h + ":" + m + ":" + s);
        $('#currentTime2').html(year + "/" + month + "/" + day + " " + h + ":" + m + ":" + s);
        t = setTimeout('startTime()', 500)
    }

    function checkTime(i) {
        if (i < 10) {
            i = "0" + i
        }
        return i
    }


    function getSetDate(dateGap, dateFormart) {
        var dd = new Date();
        dd.setDate(dd.getDate() + dateGap);//获取AddDayCount天后的日期

        return dd.Format(dateFormart);

        //console.log(getSetDate(1,'yyMMdd')); //Tomorrow
        //console.log(getSetDate(-1,'yyMMdd')); //Yesterday
    }

    function handleList(type) {
        if (type==2) {
            var id = $('#container_lists').val();
            if (id.split(',')[0]>0) {
                getOrderContainerHistory(id,2);
            } else {
                alert("框子格式错误,应为'框号,框号,注意要用英文,'");
            }
            return 1;

        } else {
            var rawId = $('#barcode').val();
            id = rawId.substr(0, 6);//Get 18 code
            getOrderContainerHistory(id);
        }

//    addItem(id);

    }

    function handleProductList() {
        var rawId = $('#barcode').val();
        id = rawId.substr(0, 18);//Get 18 code

        var barCodeId = "#bd" + id;
        if ($(barCodeId).length > 0) {
            $('#barcode').val('');
            return qtyadd2(id);
        }
        else {
            addItem(id);
        }
    }

    function getOrderContainerHistory(id,type) {
        if (id == '') {
            alert('请添加扫描框');
            return false;
        }
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        var search_type = $("#search_type").is(':checked') ? 1 : 0;
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=getOrderContainerHistory',
            data: {
                method: 'getOrderContainerHistory',
                data: {

                    count_id: id,
                    warehouse_id: warehouse_id,
                    search_type: search_type,
                    container_type: type,
                }
            },
            success: function (response) {
                if (response) {
                    var html = '';
                    var jsonData = $.parseJSON(response);
                    if (type == 2) {
                        var html = '';
                        var th_text = '';
                        if (jsonData.return_code == 'ERROR') {
                            alert(jsonData.return_msg);
                            return true;
                        } else if (jsonData.return_code == 'OK') {
                            alert(jsonData.return_msg);
                            return true;
                        } else if (jsonData.return_code == 'SUCCESS') {
                            // alert(jsonData.return_msg);
                        } else {
                            alert('数据异常请刷新后重试');
                        }
                        $.each(jsonData.return_data, function (i, v) {
                            html += '<tr>';
                            th_text = '';
                            $.each(v, function (index,value) {
                                html += '<th>'+value+'</th>';
                                th_text += '<th>'+index+'</th>';
                            });
                            html += '</tr>';
                        });
                        $("#sql_result").html(html);
                        $("#show_th_text").html(th_text);
                        return true;
                    }
                    console.log(response);

                    $.each(jsonData, function (i, v) {
                        html += "<tr style='background:#d0e9c6; height: 30px;' id='check" + jsonData.order_id + "'>";
                        var str = v.frame_vg_list;
                        var sear = new RegExp(',');
                        var arrTable = {};
                        if (sear.test(str)) {
                            var vg_array = v.frame_vg_list.split(",");
                            html += "<td>";
                            $.each(vg_array, function (i, v1) {
                                if (!arrTable[vg_array[i]]) {
                                    arrTable[vg_array[i]] = true;
                                    html += v1;
                                    html += "<br/>";
                                }

                            });
                            html += "</td>";
                        } else {
                            html += "<td>" + v.frame_vg_list + "</td>";
                        }

                        html += "<td>" + v.order_id + '[' + v.warehouse + ']<br /> ' + '[' + v.deliver_order_id + ']<br />[' + v.deliver_date + "]</td>";
                        html += "<td>" + v.uptime + "</td>";
                        html += "<td>" + v.inv_comment + "</td>";
                        html += "<td>" + v.inventory_name + "</td>";
                        html += "</tr>";


                    });
                    if (html == '') {
                        alert('最近无使用记录');
                    }

                    $('#itemInfo').html(html);
                }
            }
        });

    }



    function resetScan() {
        $('#barcode').val('');
        $('#barcode').focus();
    }

    function addItem(id) {
        var selectedWarehouse = $('#selectedWarehouse').html();
        var selectedDeliverDate = $('#selectedDeliverDate').html();

        if (id !== '') {
            //TODO 条码处理
            resetScan();

            var findAddedItem = $("#boxHold" + id).length;
            if (findAddedItem) {
                alert(id + '已经添加');
                return;
            }

            //排除已调拨
            if (global.transferringBoxes[id] !== undefined) {
                alert(id + '已经添加, 调拨单号' + global.transferringBoxes[id]['relevant_id'] + ', 添加时间' + global.transferringBoxes[id]['date_added']);
                //return;
            }

            //排除非选择配送日订单
            if (global.readyBoxes[id] == undefined) {
                alert(id + '，不可调拨，不在仓库[' + selectedWarehouse + ']计划配送日期[' + selectedDeliverDate + ']内。');
                return;
            }

            if (id > 0) {
                var html = '' +
                    '<tr class="barcodeHolder" boxid="' + id + '" id="boxHold' + id + '">' +
                    '<td>' +
                    '<span style="display:none;" name="itemCode" >' + id + '</span>' +
                    '<span id="box' + id + '">' + id + '</span>' +
                    '</td>' +

                    '<td>' +
                    '<span id="deliver_order' + id + '">' + global.readyBoxes[id]['deliver_order_id'] + '[共' + global.readyBoxes[id]['frame_count'] + '筐]</span><br />' +
                    '[<spans style="font-size: 0.8rem" id="order' + id + '">' + global.readyBoxes[id]['order_id'] + '</span>]' +
                    '</td>' +

                    '<td>' +
                    '<span id="pos' + id + '">' + global.readyBoxes[id]['pos'] + '</span>' +
                    '</td>' +

                    '<td>' +
                    '<input class="qtyopt style_red" type="button" value="-" onclick="javascript:removeItem(\'' + 'boxHold\',\'' + id + '\')" >' +
                    '</td>' +
                    '</tr>';
                $('#itemInfo').append(html);
            }

            else {
                alert(id + '错误的条码');
                return;
            }
        }
    }


    function removeItem(divId, id) {
        var item = '#' + divId + id;
        $(item).remove();
    }

    function qtyadd2(id) {
        var prodId = "#" + id;
        var qty = parseInt($(prodId).val()) + 1;
        $(prodId).val(qty);

        //locateInput();

        console.log(id + ':' + qty);
    }

    function qtyminus2(id) {
        var prodId = "#" + id;


        if ($(prodId).val() > 1 || $("#method").val() == 'inventoryAdjust') {
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);
        }
        else {
            var barcodeId = '#bd' + id;
            $(barcodeId).remove();
        }

        //locateInput();

        console.log(id + ':' + qty);
    }


    function addTransferOrder() {
        if (!confirm('确认要添加新的调拨单吗?')) {
            return;
        }

        var toWarehouseId = $('#toWarehouseId').val();
        var doWarehouseId = global.warehouseId;
        var deliverDate = $("#deliverDate").val();

        $('#itemInfo tr').each(function () {
            var id = $(this).attr('boxid');
            global.addTranseBoxList[id] = {};
            global.addTranseBoxList[id]['box_id'] = id;
            global.addTranseBoxList[id]['order_id'] = global.readyBoxes[id]['order_id'];
            global.addTranseBoxList[id]['deliver_order_id'] = global.readyBoxes[id]['deliver_order_id'];
            global.addTranseBoxList[id]['pos'] = global.readyBoxes[id]['pos'];
        });

        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=addTransferOrder',
            data: {
                method: 'addTransferOrder',
                data: {
                    userId: global.userId,
                    doWarehouseId: doWarehouseId,
                    toWarehouseId: toWarehouseId,
                    deliverDate: deliverDate,
                    addList: global.addTranseBoxList
                }
            },
            success: function (response, status, xhr) {
                if (response) {
                    console.log(response);

                    //var jsonData = $.parseJSON(response);
                    //alert(jsonData.message);
                }
            },
            complete: function () {

                $('#itemInfo').html('');
                getTransferOrder();
            }
        });
    }

    function getTransferOrder() {
        var toWarehouseId = $('#toWarehouseId').val();
        var doWarehouseId = global.warehouseId;
        var deliverDate = $("#deliverDate").val();

        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=getTransferOrder',
            data: {
                method: 'getTransferOrder',
                data: {
                    doWarehouseId: doWarehouseId,
                    toWarehouseId: toWarehouseId,
                    deliverDate: deliverDate,
                    addList: global.addTranseBoxList
                }
            },
            success: function (response, status, xhr) {
                if (response) {
                    console.log(response);

                    var jsonData = $.parseJSON(response);
                    //alert(jsonData.message);

                    var html = '';
                    $.each(jsonData.return_data, function (idx, val) {
                        html += '<tr>';
                        html += '<td>' + val.relevant_id + '<br /><span class="f0_7rem">[' + val.date_added + ']</span></td>';
                        html += '<td class="f0_8rem">' + val.status_name + '</td>';
                        html += '<td>' + val.containers + '</td>';
                        if (val.relevant_status_id == 1 || val.relevant_status_id == 2) {
                            html += '<td><button class="linkButton" onclick="javascript:cancelTransferOrder(' + val.relevant_id + ');">取消</button></td>';
                        } else {
                            html += '<td></td>';
                        }
                        html += '</tr>';
                    });
                    $("#listItem").html(html);
                }
            },
            complete: function () {
            }
        });
    }

    function cancelTransferOrder(id) {
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=cancelTransferOrder',
            data: {
                method: 'cancelTransferOrder',
                data: {
                    reqId: id
                }
            },
            success: function (response, status, xhr) {
                if (response) {
                    console.log(response);
                }
            },
            complete: function () {
                getTransferOrder();
            }
        });
    }

    function backhome() {
        $('#content').show();
        $('#print').hide();
    }

    function cancel() {
        if (confirm('确认取消此次操作，所有页面数据将不被保存！')) {
            location = window.location.href;
        }

        return;
    }

    function checkStation() {
        if ($('#station').val() == 0) {
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }
        return true;
    }

    function logout_inventory_user() {
        if (confirm("确认退出？")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'inventory_logout'
                },
                success: function (response, status, xhr) {
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