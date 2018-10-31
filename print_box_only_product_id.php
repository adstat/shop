<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/16
 * Time: 17:57
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
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>整件波次列表打印</title>
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
            /*隐藏checkbox*/
            /*-webkit-appearance: none;*/
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

        #productsInfoRelevant{
            border: 0.1em solid #888888;
        }


        #productsHold td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHoldRelevant td{
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

        #productsHold th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHoldRelevant th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHoldRelevant2 th{
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

        #invMovesPrintHold2 th{
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
         #invMovesPrintHold2 th{
            padding: 3px;
            background-color:#f0ad4e;
            color: #000;
            font-size: 12px;
            font-weight: bold;

            border-left: solid #000 1px;
            border-right: solid #000 1px;
            border-bottom: solid #000 1px;
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


        button{
            font-size: 0.8rem;
            padding: 0.2rem;
        }

    </style>
<body>
<div id='invMovesPrint' align="center" style="margin:0.5em auto;">

    <div id="invMovesPrintCaption" style="padding: 10px 5px;">类型:<span id="prtInvMoveType">整件波次打印</span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;打印人:<span id="prtInvMoveTitle"><?php echo $_COOKIE['inventory_user'];?></span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;时间:<span id="prtInvMoveTime"><?php echo date('Y-m-d H:i:s', time());?></span></div>
    <div style=" padding: 10px 5px;">
        <table id="invMovesPrintHold2" border="0" style="width:75%;"  cellpadding=0 cellspacing=0>

            <tbody id="productsInfoRelevant2">
            </tbody>
        </table>
    </div>
    <div style=" padding: 10px 5px;">
        <table id="invMovesPrintHold" border="0" style="width:75%;"  cellpadding=0 cellspacing=0>
            <tr>
                <th align="center" style="width:4em">商品ID</th>
                <th align="center" style="width:4em">货位号</th>
                <th align="center" style="width:4em">商品名称</th>
                <th align="center" style="width:4em">计划数量</th>
                <th align="center" style="width:4em">分拣数量</th>
            </tr>
            <tbody id="productsInfoRelevant">
            <!-- Scanned Product List -->
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<script>
    var get_reqId = parseInt('<?php echo empty($_GET['reqId'])?0:$_GET['reqId'];?>');
    if (get_reqId>0) {
        print_relevants_merge(get_reqId);
    } else {
        alert('格式非法');
        window.location = 'inventory_login.php?return=i.php';
    }
    function print_relevants_merge(reqId) {
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getBoxBatchOrderItem',
                data: {
                    reqId:reqId,
                }
            },
            success: function (response, status, xhr) {

                if (response) {
                    var jsonData = $.parseJSON(response);

                    var html = '';
                    var html2 = '';
                    if (jsonData.return_code == 'ERROR') {
                        alert(jsonData.return_msg);
                    } else {
                        var total_array = jsonData.return_data;
                        $.each(total_array.batchDetail,function(i,value) {
                            order_been_over = "style = ''";
                            order_been_over_size = "style = ''";
                            if (parseInt(value.product_id)>0) {
                                html += '<tr class="container'+value.product_id+'" id="bd'+ value.product_id +'">';
                                html += '<th '+order_been_over+' class="containerlist" align="left" id="containern'+value.product_id+'">';
                                html += value.product_id+'</th>';
                                html += '<th '+order_been_over+' class="containerlist" align="left" id="containern'+value.product_id+'">';
                                html += value.stock_area+'</th>';
                                html += '<th '+order_been_over+' class="containerlist" align="left" id="containern'+value.product_id+'">';
                                html += value.name+'<br />'+value.sku_barcode+'</th>';
                                html += '<th '+order_been_over+' class="containerlist" align="left" id="containern'+value.product_id+'">';
                                html += value.quantity+'</th>';
                                html += '<th></th>';
                                html += '</tr>';

                            }

                        });
                        var batchInfos = total_array.batchInfo;
                        html2 = '<tr><th align="center" style="width:4em">分拣仓库</th><th>'+batchInfos.doWarehouse+
                            '</th><th align="center" style="width:4em">目的仓库</th><th>'+batchInfos.warehouse+
                            '</th><th align="center" style="width:4em">添加日期</th><th>'+batchInfos.date_added+
                            '</th></tr><tr><th align="center" style="width:4em">波次单号</th><th>'+batchInfos.batch_id+
                            '</th><th align="center" style="width:4em">商品数量</th><th>'+batchInfos.quantity+
                            '</th><th align="center" style="width:4em">商品种类</th><th>'+batchInfos.products+'</th></tr>';
                    }

                    $('#productsInfoRelevant').html(html);
                    $('#productsInfoRelevant2').html(html2);
                    window.print();

                }
            }
        });
    }
</script>