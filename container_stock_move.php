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
        #product3{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }
        #singleProduct{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }
        #warehouse_section{
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

        #productsHold2 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

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
        #productsHold3 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

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

        #productsHold4 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHold4 th{
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
    </style>

    <style media="print">
        .noprint{display:none;}
    </style>

    <script>
        var global = {};
        global.warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';

        window.product_barcode_arr = {};
        window.product_inv_barcode_arr = {};
        <?php if(strstr($_COOKIE['inventory_user'],'scfj')){ ?>
        $(document).keydown(function (event) {
            $('#product').focus();
        });
        <?php } ?>
    </script>
</head>

<div class="title"> <input type="button" class="invopt" style="background: red;width: 70px; font-size: 15px; " id="return_index"  value="返回" onclick="javascript:history.back(-1);">
    <button class="invopt" id="return_index" style="width:4em; float: right" onclick="javascript:location.reload();">刷新</button>
    <div align="right"><?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <span onclick="javascript:logout_inventory_user();">退出</span></div>
    <div  style="display: none" id="inventory_user_id"> <?php echo $_COOKIE['inventory_user_id'];?> </div>
    <div  style="display: none" id="warehouse_id"> <?php echo $_COOKIE['warehouse_id'];?> </div>
    <div align="center" style="display:block; margin:0.5em auto" id="logo"><img src="view/image/logo.png" style="width:6em"/></div>

    <div id="login" align="center" style="margin:0.5em auto; display: none">
        <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
        <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
    </div>
    <div id="message" class="message style_light" style="">周转框出货</div>
    <input type="hidden" id="frame_vg_list" class="fm_frame_vg_list" />
    目的仓库：
    <select id="to_warehouse" style="font-size: 15px;width:8em;height:1.5em;border:1px solid">

    </select>

    <div id="content" style="display: block">

        <div id="barcodescanner2" >
            <form method="post" onsubmit="handleProductList2(); return false;">
                <input name="product2" id="product2" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; font-size: 1.2rem;"/>

                <input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">
            </form>
        </div>

        <table id="productsHold2" border="0" style="width:100%; display: inline " cellpadding=2 cellspacing=3>
            <tr>
                <th align="left">DO单</th>
                <th style="width:2rem">框号</th>
                <th style="width:2rem">商品ID</th>
            </tr>
            <tbody id="productsInfo2">
            <!-- Scanned Product List -->
            </tbody>
        </table>


    </div>

</div>
<div>
    <input class="submit" id="submitReturnProduct" type="button" value="提交" onclick="javascript:addWarehouseContainer();">

    <input class="submit" id="changeStockChekcProduct" type="button" value="" style="display: none" >

</div>
<script>

    $(document).ready(function () {
        var warehouse_ids = $("#warehouse_id").text();
        var select_date = $("#date_start").val();
        /*zx
        * 获取正在使用中的仓库*/
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getUseWarehouseId',
            },
            success : function (response , status , xhr){
                if(response) {
                    var jsonData = $.parseJSON(response);
                    var html = '';
                    $.each(jsonData, function (index, value) {
                        html += '<option value="' +value.warehouse_id+ '">' + value.title + '</option>';
                    });
                    $('#to_warehouse').html(html);
                    $('#to_warehouse').val(14);
                }

            }
        });


    });


    function handleProductList2() {
        var s_count_id = $('#product2').val();

        var count_id =  s_count_id.substr(0, 6);
        var warehouse_id =  '<?php echo $_COOKIE['warehouse_id'];?>' ;
        var frame_vg_list = $("#frame_vg_list").text();

        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=checkContainerId',
            data: {
                method: 'checkContainerId',
                data: {
                    warehouse_id: warehouse_id,
                    count_id: count_id,
                }
            },
            success: function (response) {
                if(response) {
                    var html = '';
                    var jsonData = $.parseJSON(response);
                    console.log(jsonData);
                    $.each(jsonData, function (i, v) {

                        if(v.order_id){
                            html += "<tr  class='deliver_container' style='background:#d0e9c6; height: 30px;' id='check" + v.deliver_order_id + "'>";
                            html += "<td id='deliever"+ v.deliver_order_id+"'>" + v.deliver_order_id + "</td>";
                            html += "<td id='container"+ v.container_id+"'>" + v.container_id +"</td>";
                            html += "<td id='product_id"+ v.product_id+"'>" + v.product_id +"</td>";
//                            html += '<td><input class="much_del invopt" type="button" onclick="javascript:delPurchaseOrderRelevantToInv('+v.deliver_order_id+','+v.container_id+');" value="暂存"/></td>';
                            html +="<tr/>";

                        }
                    });
                    if(frame_vg_list.indexOf(count_id) != -1 )
                    {
                        alert('不能重复扫描同一个框子');
                        return false;
                    }else{
                        $("#frame_vg_list").append( ','+count_id);
                        $('#productsInfo2').append(html);
                    }

                }

            }
        });

    }


    
    function  addWarehouseContainer() {

        var trList = $("#productsInfo2").children("tr");
        var postData = [] ;
        var productArray = [];
        var from_warehouse_id =  '<?php echo $_COOKIE['warehouse_id'];?>' ;
        var user_id =  '<?php echo $_COOKIE['inventory_user_id'];?>' ;
        var to_warehouse_id =parseInt($('#to_warehouse').val());

        for (var i=0;i<trList.length;i++) {
            var tdArr = trList.eq(i).find("td");
            console.log(tdArr);
            productArray.push(tdArr.eq(0).text());//收入类别
            productArray.push(tdArr.eq(1).text());//收入金额
            productArray.push(tdArr.eq(2).text());//收入金额
            postData.push(productArray);
            productArray = [];
        }

        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=addWarehouseContainer',
            data: {
                method: 'addWarehouseContainer',
                data: {
                    products: postData,
                    to_warehouse_id:to_warehouse_id,
                    from_warehouse_id:from_warehouse_id,
                    user_id:user_id,
                }
            },
            success: function (response) {

            }
        });

    }

</script>


</body>
</html>