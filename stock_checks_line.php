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
    <div id="message" class="message style_light" style="">分拣区/存货区基础信息录入</div>
    <div id="content" style="display: block">
        <div align="center" style="margin:0.5em auto;">

        </div>
        <div align="center" style="margin:0.5em auto;">

        </div>
        <div id="message" class="message style_light" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
        <div id="inv_control" align="center">
            仓库区域：<select  id="warehouse_section"  style="width:200px ;height: 40px" onchange="showform(this.value);">

            </select>
            <div id="barcodescanner2" style="display: none" >
                <form method="get" onsubmit="handleProductList3(); return false;" >
                    存货区号： <input name="product3"  id="product3" rows="1" maxlength="10" autocomplete="off" placeholder="添加存货区号" style="width: 150px;me-mode:disabled; font-size: 1.2rem" /></div>




            </form>
        </div>
        <script type="text/javascript">
            //            $("input[name='product3']").keyup(function(){
            //                var tmptxt=$(this).val();
            //                $(this).val(tmptxt.replace(/\D/g,''));
            //
            //                if(tmptxt.length >= 4){
            //                    updateStockChecks(tmptxt);
            //                }
            //                //$(this).val("");
            //            }).bind("paste",function(){
            //                var tmptxt=$(this).val();
            //                $(this).val(tmptxt.replace(/\D/g,''));
            //            });
            //$("input[name='product']").css("ime-mode", "disabled");
        </script>
        <div>


        </div>

        <table border="1" style="width:100%; display: none " cellpadding="2" cellspacing="3" id = 'fenjianform'>
            <input style="display: none "  id = 'product_id_transfer' value="" >
            <tbody><tr>
                <th style="width:3.3rem">ID/商品名称</th>
                <td colspan="2" id="singleProductId"></td>
            </tr>
            <tr>
                <th>分拣位数量</th>
                <td colspan="2" id="stock_area_quantity"><input type="text" style="font-size: 1rem ;height: 20px;" placeholder="输入分拣位现有数量" id="input_stock_area_quantity" maxlength="10" value=""></td>
            </tr>
            <tr id="new_sku_barcode">
                <th>安全数量</th>
                <td colspan="2" id="safe_area_quantity"><input type="text" style="font-size: 1rem ;height: 20px;" placeholder="输入安全值" id="input_safe_area_quantity" maxlength="10" value=""></td>
            </tr>
            <tr>
                <th>货位最大容纳量</th>
                <td colspan="2" id="storage_capacity_quantity"><input type="text" style="font-size: 1rem ;height: 20px;" placeholder="输入货位能存放的最大数量" id="input_capacity_quantity" maxlength="20" value=""></td>
            </tr>
            <tr>
                <th>货位号</th>
                <td id="stock_area"></td>
            </tr>
            <tr>
                <th>是否启用</th>
                <td id="status_id"></td>
            </tr>

            </tbody></table>
        <div id="submitProductSku" style="display: none ">

            <input class="submit" id="submitProductSku1" type="button" style="width: 90px;height: 30px; font-size: 10px; " value="更新/添加" onclick="javascript:addChangeProductTransfer();">
            <input class="submit" id="submitProductSku2" type="button" style="width: 90px;height: 30px; font-size: 10px;" value="启用/停用" onclick="javascript:ChangeProductTransferStatus();">
            <!--                <input class="submit" id="submitProductSku3" type="button" style="width: 120px;height: 30px; font-size: 10px; " value="手动添加移库商品" onclick="javascript:manualAddTransfer();">-->
        </div>



        <table id="productsHold2" border="0" style="width:100%; display: none " cellpadding=2 cellspacing=3>
            <tr>
                <th align="left">ID/名称</th>
                <th style="width:2rem">存货区数量</th>
                <th style="width:2.5rem">操作</th>
            </tr>
            <tbody id="productsInfo2">
            <!-- Scanned Product List -->
            </tbody>
        </table>


        <div id="barcodescanner2" >
            <form method="post" onsubmit="handleProductList2(); return false;">
                <input name="product2" id="product2" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; font-size: 1.2rem;"/>

                <input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">
            </form>
        </div>
        <script type="text/javascript">
            $("input[name='product2']").keyup(function(){
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
        <br />
        <input class="submit" id="submitProductSku" type="button"  value="返回移库操作界面" onclick="javascript:location='transfer.php?auth=xsj2015inv'"">
        <input class="submit" id="submitReturnProduct" type="button" value="提交" onclick="javascript:submitStockChecksProduct();">

        <input class="submit" id="changeStockChekcProduct" type="button" value="" style="display: none" >
        <div style="float: none; clear: both"></div>
        <hr  style="margin: 1rem 0;"/>
        <!--            <input class="submit" type="button" value="删除" onclick="javascript:deleteStockChecks();">-->
        <div style="float: none; clear: both">
            <input class="submit" type="button" value="查找" onclick="javascript:getStockChecks();">

            <input style="float: right ; display: none " class="input_default" id="searchDate" type="text" value="<?php echo date('Y-m-d', time());?>">
            <!--                <input style="float: right" class="input_default" id="searchProductId" type="text" value="" placeholder="商品ID">-->

            <input style="float: right" class="input_default" id="searchPalletNumber" type="text" value="" placeholder="存货区">

        </div>
        <div style="float: none; clear: both">

        </div>
        <table id="productsHold3" border="0" style="width:100%;display: none;" cellpadding=2 cellspacing=3>
            <tr>
                <th style="width:3rem;">存货区名称</th>
                <th style="width:2rem">商品ID</th>
                <th align="left">ID/名称</th>
                <th style="width:2rem">数量</th>

            </tr>
            <tbody id="productsInfo3">
            <!-- Scanned Product List -->
            </tbody>
        </table>
        <table id="productsHold4" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
            <tr>
                <th style="width:3rem;">存货区名称</th>

                <th align="left">记录人</th>
                <th style="width:2rem">时间</th>

            </tr>
            <tbody id="productsInfo4">
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
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=getProductSectionType',
            data: {
                method: 'getProductSectionType',
            },
            success: function (response, status, xhr) {
                console.log(response);

                if(response){
                    var jsonData = eval(response);
                    var html = '<option value=0>-请选择所属区域-</option>';
                    $.each(jsonData, function(index, value){
                        html += '<option value='+ value.station_section_type_id +' >' + value.name +  '</option>';
                    });

                    $('#warehouse_section').html(html);
                }
            }
        });
    });

    function  showform(id) {
        if(id == 2){
            $("#barcodescanner2").show();
            $("#productsHold2").show();
            $("#submitReturnProduct").show();
            $("#submitProductSku").hide();
            $("#fenjianform").hide();
        }else if (id == 1 ){
            $("#fenjianform").show();
            $("#submitProductSku").show();
            $("#barcodescanner2").hide();
            $("#productsHold2").hide();
            $("#submitReturnProduct").hide();
        }

    }


    function updateStockChecks(id) {
        var warehouse_id = $("#warehouse_id").text();
        var pallet_number = id;



        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=updateStockChecks&isback='+$('#isBack').val(),
            data : {
                method : 'updateStockChecks',
                data:{
                    warehouse_id : warehouse_id,
                    pallet_number : pallet_number,
                },


            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=r.php&ver=db";
                    }
                    if(jsonData == 1){
//                      getAddedStcokChecksProduct(pallet_number);

                    }else {
                        var html = '';
                        $.each(jsonData, function(index,value){
                            var returnQty = value.quantity;
                            var unitPrice = '<i class="f0_8rem">［单价' + parseFloat(value.price).toFixed(2) + '元]</i>';
                            var html = '<tr class="barcodeHolder" id="bd'+ value.product_id +'">' +
                                '<td style="display:none;" id="product_id_'+value.product_id+'"></td>' +
                                '<td>' +
                                '<span style="display:none;" name="productBarcode" >' + value.product_id + '</span>' +
                                '<span style="display:none;" inputBarcode="'+value.product_id+'" class="productBarcodeProduct" name="productBarcodeProduct" id="productBarcodeProduct'+value.product_id+'" >'+value.product_id+ '</span>' +
                                '<span id="info'+ value.product_id +'">'+'['+value.product_id+']'+value.name+ '</span> <br/>' +

                                '<br /><span style="font-size: 0.8rem">[<span id="sku'+ value.product_id +'">'+ value.sku_barcode+'</span>]</span>' +
                                '</td>' +

                                //'<input class="qtyopt" type="button" value="更新"  style="display: none ;font-size: 0.8rem ;width: 2.5rem;" id="Inventory'+value.product_id +'" onclick="javascript:addStockInventory(\''+ value.product_id +'\' , \''+value.sku_barcode+'\')" >'+
                                '<td style="width:4em;"><input class="qty" id="'+ value.product_id +'" name="'+ value.product_id +'" value="'+returnQty+'" />'+ '<input class="qtyopt" type="button" value="更新"  style="display: none ;font-size: 0.8rem ;width: 2.5rem;" id="Inventory'+value.product_id +'" onclick="javascript:addStockInventory(\''+ value.product_id +'\' , \''+value.sku_barcode+'\')" >'+ '</td>' +
                                '<td>' +
                                '<input class="qtyopt" type="button" value="+" onclick="javascript:qtyadd2(\''+value.product_id +'\')" >' +
                                '<input class="qtyopt" type="button" value="+10" onclick="javascript:qtyadd10(\''+ value.product_id +'\')" >' +
                                '<input class="qtyopt" type="button" value="+50" style="display: none;" onclick="javascript:qtyadd50(\''+ value.product_id +'\')" >' +
                                '<input class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus2(\''+ value.product_id +'\')" >' +
                                '</td>' +
                                '</tr>';


                            $("#info"+value.product_id).html();


                            $("#sku"+value.product_id).html(value.sku_barcode);
                            $("#warehouse_section").val(2);
                            $("#warehouse_section").attr("disabled","true");
                            $('#productsInfo2').append(html);
                        });

                        $("#product3").attr("readOnly","true");


                    }

                }
            }
        });
    }


    //盘点
    function submitStockChecksProduct(add){

        var prodListWithQty = getReturnProductBarcodeWithQty();
        var warehouse_id = $("#warehouse_id").text();
        var changeStockCheckProduct = $("#changeStockChekcProduct").val();
        var warehouse_section_id =  $('#warehouse_section option:selected') .val();//选中的值
        var pallet_number = $("#product3").val();
        var inventory_user_id = '<?php echo $_COOKIE["inventory_user_id"] ?>';
        if(add != 1 ){
            var  add = 0 ;
        }
        if(prodListWithQty == 0){
            alert("输入的数量不合法");return false;
        }

        if(prodListWithQty == '' || prodListWithQty == null ){
            alert('获取条码列表错误或还没有输入商品条码。');
            return false;
        }
        if(pallet_number == ''){
            alert('请输入分拣区号');
            return false ;
        }


        if(confirm('确认提交此次操作？')){

//            $('#submitReturnProduct').attr('class',"submit style_gray");
//            $('#submitReturnProduct').attr('disabled',"disabled");
//            $('#submitReturnProduct').attr('value',"正在提交...");

            $.ajax({
                type : 'POST',
                url : 'invapi.php?method=submitStockChecksProduct',
                data : {
                    method : 'submitStockChecksProduct',
                    products : prodListWithQty,
                    warehouse_id : warehouse_id,
                    changeStockCheckProduct:changeStockCheckProduct,
                    pallet_number: pallet_number,
                    warehouse_section_id: warehouse_section_id,
                    inventory_user_id:inventory_user_id,
                    add : add ,
                },
                success : function (response , status , xhr){
                    if(response){
                        console.log(response);

                        var jsonData = $.parseJSON(response);
                        if(jsonData.status){
                            if(jsonData.status !== 1){
                                alert(jsonData.message);

                                if(jsonData.message == '此分拣区已提交') {

                                    if (confirm('此区域已经添加了商品是否需要再次添加？')) {
                                        var add = 1;
                                        submitStockChecksProduct(add);
                                    } else {

                                    }
                                }
                                return false;
                            }
                            if(jsonData.status == 1){
                                alert("提交成功");
                                $('#productsInfo2').html("");
                                $('#changeStockChekcProduct').val('');
                                //inventoryMethodHandler($('#returnMethod').val());
                                //getAddedStcokChecksProduct();
                                $("#product3").val('');
                                $("#warehouse_section").val(0);
                                location.reload();
                                getStockChecks();
                            }
                        }
                        else{
                            //$('#message').attr('class',"message style_error");
                            //$('#message').html(jsonData.message);
                            //console.log('Inv. Process Error.');

                        }


                        if(jsonData.status == 999){
                            //alert(jsonData.msg);
                            window.location = 'inventory_login.php?return=r.php&ver=db';
                        }
                    }
                },
                complete : function(){
//                    $('#submitReturnProduct').attr('class',"submit");
//                    $('#submitReturnProduct').removeAttr("disabled");
//                    $('#submitReturnProduct').attr('value',"提交");
                }
            });
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


    function addProduct2(id){
        //var id = parseInt(id);

        //Barcode rules for Code128(18) OR Ean13(13||12)
        //18: 6+6+6
        //12: 1+5+5+x
        //13: 2+5+5+x

        if(id !== ''){

            var barCodeId = "#bd"+id;

            if($(barCodeId).length > 0){
                $("input[name='product']").val("");
            }

            else{
                var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                    '<td><span name="productBarcode" >' + id + '</span></td>' +

                    '<td style="width:4em;"><input class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus(\''+ id +'\')" ></td>' +

                    '</tr>';
                $('#productsInfo').append(html);
                $('#product').val('');
            }



        }
    }

    function returnBox(id){
        var returnBoxButton = "#returnbox" + id;
        var returnPartButton = "#returnpart" + id;
        var boxQtyInput = "#boxqty" + id;
        var returnBoxQtyInput = "#returnboxqty" + id;
        var returnType = "#returntype" + id;

        $(returnType).html('退整箱');
        $(returnBoxQtyInput).val(1);
        $(returnBoxQtyInput).hide();
        $(returnPartButton).show();
        $(returnBoxButton).hide();

        //重置数量
        $('#'+id).val(1);
    }


    function returnBox1(id,in_part){
        var returnBoxButton = "#returnbox" + id+in_part;
        var returnPartButton = "#returnpart" + id+in_part;
        var boxQtyInput = "#boxqty" + id+in_part;
        var returnBoxQtyInput = "#returnboxqty" + id + in_part;
        var returnType = "#returntype" + id;

        $(returnType).html('退整箱');
        $(returnBoxQtyInput).val(1);
        $(returnBoxQtyInput).hide();
        $(returnPartButton).show();
        $(returnBoxButton).hide();

        //重置数量
        $('#'+id).val(1);
    }

    function returnPart1(id,in_part){
        var returnBoxButton = "#returnbox" + id+in_part;
        var returnPartButton = "#returnpart" + id+in_part;
        var boxQtyInput = "#boxqty" + id+in_part;
        var returnBoxQtyInput = "#returnboxqty" + id+in_part;
        var returnType = "#returntype" + id;

        $(returnType).html('整箱件数');
        $(returnBoxQtyInput).val($(boxQtyInput).val());
        $(returnBoxQtyInput).show();
        $(returnPartButton).hide();
        $(returnBoxButton).show();

        //重置数量
        $('#'+id).val(1);
    }





    function returnPart(id){
        var returnBoxButton = "#returnbox" + id;
        var returnPartButton = "#returnpart" + id;
        var boxQtyInput = "#boxqty" + id;
        var returnBoxQtyInput = "#returnboxqty" + id;
        var returnType = "#returntype" + id;

        $(returnType).html('整箱件数');
        $(returnBoxQtyInput).val($(boxQtyInput).val());
        $(returnBoxQtyInput).show();
        $(returnPartButton).hide();
        $(returnBoxButton).show();

        //重置数量
        $('#'+id).val(1);
    }

    function addProduct_p(id){
        var warehouse_section_id =  $('#warehouse_section option:selected') .val();//选中的值
        $("#product3").attr("readOnly","true");
        $("#warehouse_section").attr("disabled","true");
        if(id !== ''){
            //TODO 条码处理

            if(id>0){


                //如果不是回库退货，隐藏散件退货方式
                var isBack = $("#isBack").val();
                if(isBack == 0){
                    $(".returnType").hide();
                }

                ajax_id = id;
                $.ajax({
                    type : 'POST',
                    url : 'invapi.php?method=getStockChecksProductInfo',
                    data : {
                        method : 'getStockChecksProductInfo',
                        sku : ajax_id,
                        warehouse_id : parseInt(global.warehouse_id)
                    },
                    success : function (response , status , xhr){
                        if(response){
                            console.log(response);
                            //var jsonData = eval(response);
                            var jsonData = $.parseJSON(response);

                            var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                                '<td style="display:none;" id="product_id_'+id+'"></td>' +
                                '<td>' +
                                '<span style="display:none;" name="productBarcode" >' + id + '</span>' +
                                '<span style="display:none;" inputBarcode="'+id+'" class="productBarcodeProduct" name="productBarcodeProduct" id="productBarcodeProduct'+id+'" ></span>' +
                                '<span id="info'+ id +'"></span> <br/>' +

                                '<span id="stock_area'+ id +'"></span>' +
                                '<br /><span style="font-size: 0.8rem">[<span id="sku'+ id +'"></span>]</span>' +
                                '</td>' +
                                //'<input class="qtyopt" type="button" value="更新"  style="display: none ;font-size: 0.8rem ;width: 2.5rem; " id="Inventory'+id +'" onclick="javascript:addStockInventory(\''+ jsonData.product_id +'\',\''+ id+'\')" >'+

                                '<td style="width:4em;"><input class="qty" id="'+ id +'" name="'+ id +'" value="1"  />'+ '<input class="qtyopt" type="button" value="更新"  style="display: none ;font-size: 0.8rem ;width: 2.5rem;" id="Inventory'+id +'" onclick="javascript:addStockInventory(\''+ jsonData.product_id +'\' , \''+id+'\')" >'+ '</td>' +
                                '<td>' +
                                '<input class="qtyopt" type="button" value="+" onclick="javascript:qtyadd2(\''+ id +'\')" >' +
                                '<input class="qtyopt" type="button" value="+10" onclick="javascript:qtyadd10(\''+ id +'\')" >' +
                                '<input class="qtyopt" type="button" value="+50"  style="display: none;" onclick="javascript:qtyadd50(\''+ id +'\')" >' +
                                '<input class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus2(\''+ id +'\')" >' +
                                '</td>' +
                                '</tr>';
                            $('#productsInfo2').append(html);




//                            if(jsonData.warehouse_section_id  != warehouse_section_id){
//                                alert(jsonData.product_id+'商品不属于这个分类'+'/'+'属于'+jsonData.section_name+'分类');
//                                qtyminus2(id);
//                                return false ;
//                            }

                            if(typeof(jsonData.price) == "undefined"){
                                alert("未找到对应商品，请输入商品ID");
                                $("#bd"+id).remove();
                            }

                            var boxSize = parseInt(jsonData.box_size);
                            if(boxSize == 0){
                                boxSize = 1;
                            }

                            $("#info"+id).html('['+jsonData.product_id+']'+jsonData.name);
                            $("#sku"+id).html(jsonData.sku_barcode);
                            $("#stock_area"+id).html(jsonData.inv_class_sort);
                            $("#boxqty"+id).val(boxSize);
                            //$("#price"+id).html(jsonData.price);
                            $("#productBarcodeProduct"+id).html(jsonData.product_id);
                            $("#product_id_"+id).html(jsonData.product_id);

//                            if(jsonData.status == 999){
//                                alert("[出库回库退货]未登录，请登录后操作");
//                                window.location = 'inventory_login.php?return=r.php&ver=db';
//                            }

                        }
                    },
                    complete : function(){

                        $('#product2').val("");
                        $('#product2').focus();
                    }
                });
            }

            else{
                alert('错误的条码');
                return false;
            }

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

    function addStockInventory(id,sku_id) {

        var warehouse_id = $("#warehouse_id").text();
        var add_user = '<?php echo $_COOKIE["inventory_user_id"] ?>';
        var warehouse_section_id =  $('#warehouse_section option:selected') .val();//选中的值
        var pallet_number = $("#product3").val();
        var product_num1 = $("#"+sku_id).val();
        var product_num2 = $("#"+id).val();



        if(product_num1 > 0 ){
            var   product_num = product_num1;
        }else{
            var  product_num = product_num2;
        }

        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=addStockInventory',
            data: {
                method: 'addStockInventory',
                data: {
                    product_id: id,
                    pallet_number:pallet_number,
                    warehouse_section_id:warehouse_section_id,
                    product_num:product_num,
                    warehouse_id:warehouse_id,
                    add_user:add_user,
                },
            },
            success: function (response, status, xhr) {
                console.log(response);
                var jsonData = $.parseJSON(response);
                if (jsonData == 1) {
                    $("#Inventory"+sku_id).hide();
                    $("#Inventory"+id).hide();
                }
            }
        });

    }


    function locateInput(){
        //$('#product').focus();
    }

    function getProductBarcodeWithQty(){
        var prodList = '';
        var m = 0;

        $('#productsInfo tr').each(function () {

            var productBarcode = $(this).find('span[name=productBarcode]').html();

            productBarcode = parseInt(productBarcode);

            if(m == $("#productsInfo tr").length-1){

                prodList += productBarcode+'';

            }
            else{

                prodList += productBarcode+',';

            }

            m++;
        });

        return prodList;
    }

    function getReturnProductBarcodeWithQty(){
        var prodList = '';
        var m = 0;
        var num_err_flag = 0;

        $('#productsInfo2 tr').each(function () {

            var productBarcode = $(this).find('span[name=productBarcodeProduct]').html();

            var inputProductBarcode = $(this).find('span[name=productBarcodeProduct]').attr("inputBarcode");
            var productBarcodeId = '#'+inputProductBarcode;
            var productBarcodeQty = $(productBarcodeId).val();

            var productReturnBoxQty = $('#returnboxqty'+inputProductBarcode).val();
            if((productBarcodeQty < 0 || isNaN(productBarcodeQty)) && $("#method").val() != 'inventoryAdjust'){
                //alert(productBarcode);
                //alert(productBarcodeQty);
                //alert(typeof productBarcodeQty);

                num_err_flag = 1;

            }


            if(m == $("#productsInfo2 tr").length-1){
                if(parseInt(productBarcodeQty) > 0 || $("#method").val() == 'inventoryAdjust'){
                    prodList += productBarcode+':'+productBarcodeQty+':'+productReturnBoxQty;
                }

            }
            else{
                if(parseInt(productBarcodeQty) > 0 || $("#method").val() == 'inventoryAdjust'){
                    prodList += productBarcode+':'+productBarcodeQty+':'+productReturnBoxQty+',';
                }

            }

            m++;
        });
        if(num_err_flag == 1){
            return 0;
        }
        else{
            return prodList;
        }
    }
    function  handleProductList3() {
        var pallet_num = $("#product3").val();
        updateStockChecks(pallet_num);
    }

    function handleProductList2(){
        var rawId = $('#product2').val();
        id = rawId.substr(0,18);//Get 18 code
        var warehouse_section_id =  $('#warehouse_section option:selected') .val();//选中的值
        var pallet_number = $("#product3").val();

        if(warehouse_section_id == 0 ){
            alert('请选择需要记录数据的仓库区域');
            return false ;
        }

        if(warehouse_section_id == 1 ){

            getTransferProductInfo();

        }else if(warehouse_section_id == 2 ){
            if(pallet_number == ''){
                alert('请输入分拣区ID');
                return false ;
            }
            if(warehouse_section_id == 0 ){
                alert('请选择商品分类');
                $("#product2").val('');
                return false
            }
            //TODO 判断商品编号
            var barCodeId = "#bd"+id;
            if($(barCodeId).length > 0){
                $('#product2').val('');
                return qtyadd2(id);
            }
            else{
                addProduct_p(id);
            }
        }

    }


    function getTransferProductInfo(){
        var warehouse_id = $("#warehouse_id").text();
        var id = $('#product2').val();
        var warehouse_section_id =  $('#warehouse_section option:selected') .val();//选中的值
        if(id !== ''){
            if(id.length >= 4){

                $.ajax({
                    type : 'POST',
                    url : 'invapi.php',
                    data: {
                        method: 'getTransferProductInfo',
                        data: {
                            warehouse_id: warehouse_id,
                            sku_id: id,
                            warehouse_section_id:warehouse_section_id,
                        },
                    },
                    success : function (response , status , xhr){


                        if(response){
                            console.log(response);
                            var jsonData = $.parseJSON(response);

                            if(typeof(jsonData.product_id) == "undefined"){
                                alert("未找到对应商品["+id+"]");
                                return false;
                            }
                            else{
                                $('#singleProductId').html(jsonData.product_id);
                                $('#input_stock_area_quantity').val(jsonData.quantity);

                                $('#input_safe_area_quantity').val(jsonData.safe_quantity);
                                $('#input_capacity_quantity').val(jsonData.capacity);

                                $('#stock_area').html(jsonData.stock_area);

                                $("#product_id_transfer").val(jsonData.transfer_id);
                                $("#status_id").html(jsonData.status);
                                //$("#newProductBarCode").focus();

                            }


                            if(jsonData.status == 999){
                                alert("未登录，请登录后操作");
                                window.location = 'inventory_login.php?return=i.php';
                            }

                        }
                    },

                });
            }
            else{
                alert('错误的条码['+id+']');
                $('#product2').val('');
                return false;
            }
        }

    }



    function qtyadd2(id){

        $("#Inventory"+id).show();
        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 1;
        $(prodId).val(qty);

        //locateInput();

        console.log(id+':'+qty);
    }
    function qtyadd10(id){
        $("#Inventory"+id).show();
        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 10;
        $(prodId).val(qty);

        //locateInput();

        console.log(id+':'+qty);
    }
    function qtyadd20(id){
        $("#Inventory"+id).show();
        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 20;
        $(prodId).val(qty);

        //locateInput();

        console.log(id+':'+qty);
    }
    function qtyadd50(id){
        $("#Inventory"+id).show();
        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 50;
        $(prodId).val(qty);

        //locateInput();

        console.log(id+':'+qty);
    }
    function qtyminus2(id){

        var prodId = "#"+id;


        if($(prodId).val() > 1 || $("#method").val() == 'inventoryAdjust'){
            $("#Inventory"+id).show();
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

    function qtyminus(id){
        var prodId = "#"+id;

        var barcodeId = '#bd'+id;
        $(barcodeId).remove();

    }

    function getStockChecks() {
        var warehouse_id = $("#warehouse_id").text();
        var pallet_number = $("#searchPalletNumber").val();
        $("#productsHold4").show();
        $("#productsHold3").hide();
        $("#barcodescanner2").show();
        $("#productsHold2").show();
        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=getStockChecks&isback='+$('#isBack').val(),
            data : {
                method : 'getStockChecks',
                data:{
                    searchDate: $("#searchDate").val(),
                    product_id: $("#searchProductId").val(),
                    warehouse_id: warehouse_id,
                    pallet_number: pallet_number,
                },

            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=r.php&ver=db";
                    }

                    var html = '';
                    $.each(jsonData, function(index,value){
                        var returnQty = value.quantity;
                        var unitPrice = '<i class="f0_8rem">［单价' + parseFloat(value.price).toFixed(2) + '元]</i>';
                        var disableReutrnButton = '<br /><input style="width: 2rem" class="addprod style_red" id="submitReturnProduct" type="button" value="明细" onclick="if(confirm(\'查看['+value.pallet_number+']明细？\')){ javascript:getAddedStcokChecksProduct(\''+value.pallet_number+'\');}"><br />';
                        var disableDeleteButton = '<br /><input style="width: 2rem" class="addprod style_red" id="submitReturnProduct" type="button" value="删除" onclick="if(confirm(\'删除['+value.pallet_number+']信息？\')){ javascript:deleteStockChecks('+value.stock_section_id+');}"><br />';

                        html += '<tr class="barcodeHolder" >' +
                            '<td>'+value.pallet_number+disableReutrnButton+'</td>' +
                            //                                '<td>'+value.section_name+disableDeleteButton+'</td>' +
                            '<td>'+value.username+disableDeleteButton+'</td>' +
                            '<td>'+value.date_added+'</td>' +
                            '</tr>';
                    });
//                        if(html == ''){
//                            html = '<tr><td colspan="5">无记录</td></tr>';
//                        }

                    $('#productsInfo4').html(html);
                }
            }
        });
    }



    function  deleteStockChecks(stock_check_id) {
        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=deleteStockChecks&isback='+$('#isBack').val(),
            data : {
                method : 'deleteStockChecks',
                data:{
                    stock_check_id: stock_check_id,
                },

            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    console.log(response);
                    var jsonData = $.parseJSON(response);
                    if(jsonData == 1){
                        alert('删除成功');
                        getStockChecks();
                    }else{
                        alert('已装车不能删除')
                    }
                }

            }
        });
    }

    function getAddedStcokChecksProduct(stock_check_id){

        var warehouse_id = $("#warehouse_id").text();
        var pallet_number = stock_check_id;


//        $("#productsHold3").show();
//        $("#productsHold4").hide();

        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=getAddedStcokChecksProduct&isback='+$('#isBack').val(),
            data : {
                method : 'getAddedStcokChecksProduct',
                searchDate : $("#searchDate").val(),
                product_id : $("#searchProductId").val(),
                warehouse_id : warehouse_id,
                pallet_number : pallet_number,

            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=r.php&ver=db";
                    }

                    var html = '';
                    $.each(jsonData, function(index,value){
                        var returnQty = value.quantity;
                        var unitPrice = '<i class="f0_8rem">［单价' + parseFloat(value.price).toFixed(2) + '元]</i>';


                        html += '<tr class="barcodeHolder" >' +
                            '<td>'+value.product_id+value.name+ '</td>' +
                            '<td>'+returnQty+'</td>' +
                            '<td>'+ '<br /><input style="width: 2rem" class="addprod style_red" id="submitReturnProduct" type="button" value="删除" onclick="if(confirm(\'删除['+value.product_id+']信息？\')){ javascript:deleteStockSectionPorduct('+value.stock_section_id+','+value.product_id +');}"><br />' + '</td>'+
                            '</tr>';
                        $("#warehouse_section").val(value.stock_section_type_id);
                        $("#warehouse_section").attr("disabled","true");
                    });


                    $('#productsInfo2').html(html);
                }
            },
            complete:function () {
                $("#product3").val(stock_check_id);
                $("#product3").attr("readOnly","true");
//                    $("#product2").attr("readOnly","true");

            }
        });


    }

    function  deleteStockSectionPorduct(stock_section_id ,product_id ) {
        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=deleteStockSectionPorduct&isback='+$('#isBack').val(),
            data : {
                method : 'deleteStockSectionPorduct',
                data:{
                    stock_section_id: stock_section_id,
                    product_id:product_id,
                },

            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    console.log(response);
                    var jsonData = $.parseJSON(response);
                    if(jsonData == 1){
                        alert('删除成功');
                        var name = $("#product3").val();
                        getAddedStcokChecksProduct(name);
                    }else{
                        alert('已装车不能删除')
                    }
                }

            }
        });
    }



    function changeCheckSingleProduct(stock_check_id,product_id){
        $("#product2").val(product_id);


        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=changeCheckSingleProduct',
            data : {
                method : 'changeCheckSingleProduct',
                data: {
                    stock_check_id: stock_check_id,
                },

            },
            success : function (response, status, xhr){
                var jsonData = $.parseJSON(response);
                if(jsonData){
                    handleProductList2();

                    $("#"+product_id).val(jsonData);
                    $("#changeStockChekcProduct").val(stock_check_id);

                }
            }
        });
    }





    function confirmCheckSingleProduct(id){
        var warehouse_id = $("#warehouse_id").text();

        if(confirm('确认这些盘点记录吗，操作不可撤销？')) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php?method=confirmCheckSingleProduct',
                data: {
                    method: 'confirmCheckSingleProduct',
                    data: {
                        stock_check_id: id,
                    },
                },
                success: function (response, status, xhr) {
                    console.log(response);
                    var jsonData = $.parseJSON(response);
                    if (jsonData == 1) {


                        alert('确认盘点成功');

                        getAddedStcokChecksProduct();


                        if (jsonData.status == 999) {
                            alert(jsonData.msg);
                            window.location = 'inventory_login.php?return=r.php&ver=db';
                        }
                    }
                }
            });

        }


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


    function  ChangeProductTransferStatus(){
        var product_id_transfer = $("#product_id_transfer").val();
        var warehouse_section_id =  $('#warehouse_section option:selected') .val();//选中的值
        var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'ChangeProductTransferStatus',
                data: {
                    product_id_transfer: product_id_transfer,
                    inventory_user_id:inventory_user_id,
                    warehouse_id :parseInt(global.warehouse_id),
                    warehouse_section_id:warehouse_section_id,

                },
            },
            success: function (response, status, xhr) {
                var jsonData = $.parseJSON(response);

                if(jsonData == 1){
                    alert('更新成功');
                    getTransferProductInfo();
                }
            }

        });

    }

    function addChangeProductTransfer(){
        var product_id_transfer = $("#product_id_transfer").val();
        var input_stock_area_quantity = $("#input_stock_area_quantity").val();
        var input_safe_area_quantity = $("#input_safe_area_quantity").val();
        var input_capacity_quantity = $("#input_capacity_quantity").val();
        var stock_area = $("#stock_area").text();

        var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';
        var warehouse_section_id =  $('#warehouse_section option:selected') .val();//选中的值
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'addChangeProductTransfer',
                data: {
                    product_id_transfer: product_id_transfer,
                    input_stock_area_quantity: input_stock_area_quantity,
                    input_safe_area_quantity:input_safe_area_quantity,
                    input_capacity_quantity:input_capacity_quantity,
                    stock_area:stock_area,
                    inventory_user_id:inventory_user_id,
                    warehouse_id :parseInt(global.warehouse_id),
                    warehouse_section_id:warehouse_section_id,

                },
            },
            success: function (response, status, xhr) {
                var jsonData = $.parseJSON(response);

                if(jsonData == 1){
                    alert('更新成功');
                    getTransferProductInfo();
                    $("#product2").val('');
                }
            }

        });
    }

</script>


</body>
</html>