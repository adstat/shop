<?php
if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';
$inventory_user_admin = array('niudoudou','doormen','alex');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器 
    header("Location: inventory_login.php?return=r.php&ver=db");
    //确保重定向后，后续代码不会被执行 
    exit;
}

if(!in_array($_COOKIE['inventory_user'],$inventory_user_admin)){
    exit("此功能仅限指定库管操作, 请返回");
}
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
        window.product_barcode_arr = {};
        window.product_inv_barcode_arr = {};
        <?php if(strstr($_COOKIE['inventory_user'],'scfj')){ ?>
        $(document).keydown(function (event) {
            $('#product').focus();
        });
        <?php } ?>
    </script>
</head>

    
    <button class="invopt" id="return_index" style="display:none;width:4em;" onclick="javascript:location.reload();">返回</button>
    <div align="right"><?php echo $_COOKIE['inventory_user'];?>  <span onclick="javascript:logout_inventory_user();">退出</span></div>
    <div align="center" style="display:block; margin:0.5em auto" id="logo"><img src="view/image/logo.png" style="width:6em"/></div>

    <div id="login" align="center" style="margin:0.5em auto; display: none">
        <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
        <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
    </div>

    <div id="content" style="display: block">
        <div align="center" style="margin:0.5em auto;">
          
        </div>
        <div align="center" style="margin:0.5em auto;">
            
        </div>
        <div id="message" class="message style_light" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
        <div id="inv_control" align="center">
            <div id="invMethods">
                <button id="orderDeliverOut" class="invopt" style="display: block" onclick="javascript:location='change_orderstatus.php?auth=xsj2015inv&ver=db'">确认订单出库</button>
                <button id="inventoryReturnProduct" class="invopt" style="display: block" onclick="javascript:inventoryMethodHandler('inventoryReturnProduct');">出库缺货</button>
                <button id="inventoryOrderMissing" class="invopt" style="display: block" onclick="javascript:inventoryMethodHandler('inventoryOrderMissing');">库内丢失</button>
                <button id="deliverReturnProduct" class="invopt" style="display: block" onclick="javascript:inventoryMethodHandler('deliverReturnProduct');">物流退货</button>
                <button id="deliverReturnMissingProduct" class="invopt" style="display: block" onclick="javascript:inventoryMethodHandler('deliverReturnMissingProduct');">回库散件缺货</button>
                <button id="inventoryFrameIn" class="invopt" style="display: block" onclick="javascript:inventoryMethodHandler('inventoryFrameIn');">回收篮框</button><br>
            </div>

            <div id="orderMissing" style="display: none">
                <div id="missing_order_id">遗失订单 <input class="input_default" type="text" placeholder="订单号" name="missing_order_id"></div>
                <div id="monitor_checked">
                    监控确认
                    <input class="input_default w6rem" type="text" value="<?php echo date('Y-m-d', time()); ?>"  placeholder="日期" name="monitor_checked_date">日
                    <input class="input_default w2rem" type="text"  placeholder="时" name="monitor_checked_hour">时
                    <input class="input_default w2rem" type="text"  placeholder="分" name="monitor_checked_minute">分
                </div>
                <div id="supervisor_checked">
                    主管确认
                    <input class="input_default w6rem" type="text" value="<?php echo date('Y-m-d', time()); ?>"  placeholder="日期" name="supervisor_checked_date">日
                    <input class="input_default w2rem" type="text"  placeholder="时" name="supervisor_checked_hour">时
                    <input class="input_default w2rem" type="text"  placeholder="分" name="supervisor_checked_minute">分
                </div>
                <br />
                <input class="submit" id="submitReturnProduct" type="button" value="确认已分拣订单遗失" onclick="javascript:orderMissing();">
            </div>

            <div id="productList" name="productList" method="POST" style="display: none;">
                <div id="order_id" style="display:none;">订单号:<input style="font-size:1.4em;" type="text" id="input_order_id" name="input_order_id"></div>
                <table id="productsHold" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                    <tr>
                        <th style="width:2em">框号</th>
                        
                        <th style="width:5em">操作</th>
                    </tr>
                    <tbody id="productsInfo">
                        <!-- Scanned Product List -->
                    </tbody>
                </table>
                <script type="text/javascript">
                    $("input[name='input_order_id']").keyup(function(){
                        var tmptxt=$(this).val();
                        //$(this).val(tmptxt.replace(/\D|^0/g,''));

                        if(tmptxt.length == 6){
                            $("#product").focus();
                        }
                    }).bind("paste",function(){
                           
                        });
                    //$("input[name='product']").css("ime-mode", "disabled");
                </script>

                <div id="barcodescanner" style="display: none">
                    
                    <form method="get" onsubmit="handleProductList(); return false;">
                        <input name="product" id="product" rows="1" maxlength="19" onclick="" autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled;"/>
                        <input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">
                    </form>
                </div>
                <script type="text/javascript">
                    $("input[name='product']").keyup(function(){
                        var tmptxt=$(this).val();

                        
                        if(tmptxt.length == 8){
                            handleProductList();
                            $(this).val("");
                        }
                    }).bind("paste",function(){
                            var tmptxt=$(this).val();
                            //$(this).val(tmptxt.replace(/\D|^0/g,''));
                        });
                    //$("input[name='product']").css("ime-mode", "disabled");
                </script>

                <input type="hidden" name="method" id="method" value="" />
                <div style="float:left">当前时间: <span id="currentTime"><?php echo date('Y-m-d H:i:s', time());?></span></div>
                <br />
               
                <input class="submit" id="submit_frame_out" type="button" value="确认到店" onclick="javascript:addCheckFrameOut();">
                
                <input class="submit" id="submit_frame_check" type="button" value="确认检查" onclick="javascript:addCheckFrameCheck();">
                
                
                <input class="submit" id="submit_frame_in" type="button" value="确认回收" onclick="javascript:addCheckFrameIn();">
                <input class="submit" id="submit_frame_cage" type="button" value="确认到店" onclick="javascript:addCheckFrameCage();">
               <br>
               <br>
                <div id="showFrameData" style="color:green;">
                    
                    
                </div>
                
                <!-- <input class="submit style_gray" type="button" value="取消" onclick="javascript:cancel();"> -->
            </div>

            <div id="productList2" name="productList2" method="POST" style="display: none;">
                <input type="button" class="submit"  id="whole_order"  value="操作整单退货" onclick="hide();">
                <div id="order_id2" style="display:none;">订单号 <input placeholder="扫描订单号" style="font-size:1.2rem; width: 7rem" type="text" id="input_order_id2" name="input_order_id2">   </div>
                <input type="hidden" id="isBack" value="0" />
                <input type="hidden" id="isRepackMissing" value="0" />
                <input type="hidden" id="returnMethod" value="" />
                <table id="productsHold2" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                    <tr>
                        <th style="display:none; width:2em">ID</th>
                        <th align="left">ID/名称</th>
                        <th style="width:2rem">退货类型</th>
                        <th style="width:2rem">数量</th>
                        <th style="width:2.5rem">操作</th>
                    </tr>
                    <tbody id="productsInfo2">
                        <!-- Scanned Product List -->
                    </tbody>
                </table>

                <script type="text/javascript">
                    $("input[name='input_order_id2']").keyup(function(){
                        var tmptxt=$(this).val();
                        //$(this).val(tmptxt.replace(/\D|^0/g,''));

                        if(tmptxt.length == 6){
                            $("#product2").focus();
                            
                        }
                    }).bind("paste",function(){
                           
                        });
                    //$("input[name='product']").css("ime-mode", "disabled");
                </script>
                
                
                
                <div id="barcodescanner2" style="display: none">
                    <form method="post" onsubmit="handleProductList2(); return false;">
                        <input name="product2" id="product2" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; font-size: 1.2rem"/>
                        <input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">
                    </form>
                </div>
                <script type="text/javascript">
                    $("input[name='product2']").keyup(function(){
                        var tmptxt=$(this).val();
                        $(this).val(tmptxt.replace(/\D/g,''));
       
                        //if(tmptxt.length >= 5){
                        //    handleProductList2();
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
                <div ><select  style="margin:0 5rem" id = "return_reason">
                        <option value="">可选择退货原因</option>
                        <option value="6">分拣出库,物流未配送</option>
                    </select>
                </div>
                <input class="submit" id="submitReturnProduct" type="button" value="提交" onclick="javascript:submitReturnProduct();">

                <div style="float: none; clear: both"></div>
                <hr  style="margin: 1rem 0;"/>
                <h1>已提交退货</h1>
                <div style="float: none; clear: both">
                    <input class="submit" type="button" value="查找" onclick="javascript:getAddedReturnDeliverProduct();">
                    <input style="float: right" class="input_default" id="searchDate" type="text" value="<?php echo date('Y-m-d', time());?>">
                </div>
                <table id="productsHold3" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                    <tr>
                        <th style="width:3rem;">订单号</th>
                        <th style="width:2rem">ID</th>
                        <th align="left">ID/名称</th>
                        <th style="width:2rem">数量</th>
                        <th style="width:2rem">小计</th>
                    </tr>
                    <tbody id="productsInfo3">
                        <!-- Scanned Product List -->
                    </tbody>
                </table>

                <div style="text-align: left">
                    <input style="float: right" class="submit" id="confirmReturnProduct" type="button" value="全部确认" onclick="javascript:confirmReturnProduct();">
                </div>
            </div>

            <div id="issue" name="issue" method="POST" style="display: none;">
                <div id="issue_order" >订单号 <input placeholder="扫描订单号" style="font-size:1.2rem; width: 7rem ;border:1px solid;" type="text" id="input_issue_order" name="input_issue_order">

                  <span id="span_logistic_allot_id" style="display: none"> 配送单号: <select id="logistic_allot_id" style="font-size:1.2rem; width: 7rem ;border:1px solid;">
                                    </select>
                  </span>
                </div>

                <div>
                   <div>订单基本信息:<span id="order_info"></span></div>
                    <table id="issue_order_table"  style="width:100%;border:1px solid ;" >
                        <tr>
                            <th style=" width:2em">商品ID</th>
                            <th style=" width:2em">订单数量</th>
                        </tr>
                        <tbody id="issue_order_tableInfo">

                        </tbody>
                    </table>
                </div>
                <div id="issue_info" style="display: none">
                    <div>
                   退货原因: <select id = 'issue_reason'>

                    </select>
                    货位号:<input placeholder="重新输入货位号" style="font-size:1.2rem;  width: 7rem ;" type="text" id="position_num" name="position_num">
                    </div>
                    <div>
                    <input type="button"   class="submit"    value="整单退货"  onclick="redistr();" >
                    </div>
                </div>
                <input type="button"   class="submit"    value="整单退货列表"  onclick="reDistrList();" >
                <div id = "issue_info_list" style="display: none">
                    <table id="issue_order_list"  style="width:100%;border:1px solid; " >
                        <tr style="width:100%;border:1px solid; ">
                            <th style=" width:2em">订单ID</th>
                            <th style=" width: 2em">配送单号</th>
                            <th style=" width:2em">司机</th>
                            <th style=" width:2em">配送日期</th>
                            <th style=" width:2em">添加日期</th>
                            <th style=" width:2em">退货原因</th>
                        </tr>
                        <tbody id="issue_order_listInfo" style="width:100%;border:1px solid; ">

                        </tbody>
                    </table>
                </div>
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
            url : 'invapi.php',
            data : {
                method : 'warehouseInit'
            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);

                    var html = '';
                    $.each(jsonData, function(index, value){
                        html += '<button id="' + index + '" class="invopt" style="display: inline" onclick="javascript:inventoryMethodHandler(\'' + index + '\');">' + value + '</button>';
                    });

                    $('#invMethods').html(html);

                    console.log('Init. Load Methods');
                }
            },
            complete : function(){
                $.ajax({
                    type : 'POST',
                    url : 'invapi.php',
                    data : {
                        method : 'getStations'
                    },
                    success : function (response , status , xhr){
                        //console.log(response);

                        if(response){
                            var jsonData = eval(response);

                            var html = '<option value=0>-请选择站点-</option>';
                            $.each(jsonData, function(index, value){
                                html += '<option value='+ value.station_id +' >' + value.name + '</option>';
                            });
                            $('#station').html(html);

                            console.log('Load Stations');
                        }
                    }
                });

                $.ajax({
                    type : 'POST',
                    url : 'invapi.php',
                    data : {
                        method : 'getShelfLifeStrict'
                    },
                    success : function (response , status , xhr){
                        //console.log(response);

                        if(response){
                            $('#shelfLifeStrict').text(response);

                            console.log('Load Strict Shelf Life');
                        }
                    }
                });
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

    function inventoryMethodHandler(method){

        var methodId = "#"+method;
        $('#method').val(method);
        $('#label').html($(methodId).text());

        $('#invMethods').hide();
        $('#message').html('');
        $('#move_list').hide();

        if(method == 'inventoryFrameIn'){
            $('#productList').show();
            $('title').html($(methodId).text() + '-鲜世纪篮框管理');
            $('#logo').html('鲜世纪篮框管理－'+$(methodId).text());
        }
        else{
           $('#productList2').show();

            $('title').html($(methodId).text());
            $('#logo').html('鲜世纪仓库管理－'+$(methodId).text());
        }

        if(method == 'inventoryFrameIn'){
            $('#barcodescanner').show();
            $("#submit_frame_in").show();
            $("#submit_frame_out").hide();
            $("#submit_frame_check").hide();
            $("#submit_frame_cage").hide();
            $("#return_index").show();
            $("#order_id").hide();
            
            $("#product").focus();
            
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'getAddedFrameIn'
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        
                        var added_order_html = '';
                        added_order_html += '今日已回收框号(总数量：'+jsonData.length+')：';
                        $.each(jsonData, function(index,value){
                            added_order_html += '<br>';
                            added_order_html += value.container_id;
                            
                        });
                        $("#showFrameData").html(added_order_html);
                    }
                }
            });

        }
        else if(method == 'inventoryReturnProduct' || method == 'deliverReturnProduct' || method == 'deliverReturnMissingProduct'){
            console.log('Method:'+method);
            $('#barcodescanner2').show();
            $("#submit_frame_in").hide();
            $("#submit_frame_out").hide();
            $("#submit_frame_check").hide();
            $("#submit_frame_cage").hide();
            $("#return_index").show();
            $("#order_id").hide();
            if(method == 'inventoryReturnProduct'  || method == 'deliverReturnMissingProduct'){
                $("#whole_order").hide();
                $("#return_reason").hide();
            }

            $("#order_id2").show();
            $("#input_order_id2").val("");
            $("#input_order_id2").focus();
            $('#message').show();
            $('#returnMethod').val(method);

            $('#isRepackMissing').val(0);

            //根据选择的任务，设定是否为回库的操作isBack
            if(method == 'inventoryReturnProduct'){
                console.log('Start inventoryReturnProduct');
                $('#message').html('订单出库时处理，可退整箱数量');
                $('#isBack').val(0);
            }

            if(method == 'deliverReturnProduct'){
                console.log('Start deliverReturnProduct');
                $('#message').html('订单回库时处理，退散件请输入整箱数量');
                $('#isBack').val(1);
            }

            if(method == 'deliverReturnMissingProduct'){
                console.log('Start deliverReturnMissingProduct');
                $('#message').html('订单回库时记录周转筐缺货');
                $('#isBack').val(1);
                $('#isRepackMissing').val(1);
            }

           getAddedReturnDeliverProduct();
        }
        else if(method == 'inventoryOrderMissing'){
            console.log('Method:'+method);
            $('#message').show();

            $('#message').html('订单出库未找到，需要和及时和分拣班组核实并查阅监控，同时告知上级主管确认，记录监控查询时间和确认时间。<br /><br /><span style="font-style: italic">如确认订单丢失，将归档改订单的出库数据并删除改订单分拣数据，订单需要重新分拣，可选订单延迟配送。</span>');
            $("#return_index").show();
            $('#productList').hide();
            $('#productList2').hide();
            $('#orderMissing').show();

        }
        else{
            $('#getplanned').hide();
        }
        locateInput();
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
        if(id !== ''){
            //TODO 条码处理

            if(id>0){
                var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                    '<td style="display:none;" id="product_id_'+id+'"></td>' +
                    '<td>' +
                    '<span style="display:none;" name="productBarcode" >' + id + '</span>' +
                    '<span style="display:none;" inputBarcode="'+id+'" class="productBarcodeProduct" name="productBarcodeProduct" id="productBarcodeProduct'+id+'" ></span>' +
                    '<span id="info'+ id +'"></span>' +
                    '<br /><span style="font-size: 0.8rem">[<span id="sku'+ id +'"></span>]</span>' +
                    '</td>' +

                    '<td style="width:4em; text-align: center">' +
                        '<span id="returntype'+id+'">退整件</span>' +
                        '<input class="qty" type="hidden" id="boxqty'+ id +'" name="boxqty'+ id +'" value="1" />' +
                        '<input class="qty" style="display: none; background-color: #fff; border: 1px #ccc solid; margin: 5px 0" id="returnboxqty'+ id +'" name="returnboxqty'+ id +'" value="1" />' +
                        '<input class="addprod style_green returnType" style="display: none" id="returnbox'+id+'" type="button" value="改退整件" onclick="returnBox('+id+');">' +
                        '<input class="addprod style_green returnType" style="display: inline" id="returnpart'+id+'" type="button" value="改退散件" onclick="returnPart('+id+');">' +
                    '</td>' +
                    '<td style="width:4em;"><input class="qty" id="'+ id +'" name="'+ id +'" value="1"  /></td>' +
                    '<td>' +
                    '<input class="qtyopt" type="button" value="+" onclick="javascript:qtyadd2(\''+ id +'\')" >' +
                    '<input class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus2(\''+ id +'\')" >' +
                    '</td>' +
                    '</tr>';
                $('#productsInfo2').append(html);

                //如果不是回库退货，隐藏散件退货方式
                var isBack = $("#isBack").val();
                if(isBack == 0){
                    $(".returnType").hide();
                }

                ajax_id = id;
                $.ajax({
                    type : 'POST',
                    url : 'invapi.php',
                    data : {
                        method : 'getSkuProductInfo',
                        sku : ajax_id
                    },
                    success : function (response , status , xhr){
                        if(response){
                            console.log(response);
                            //var jsonData = eval(response);
                            var jsonData = $.parseJSON(response);

                            if(typeof(jsonData.price) == "undefined"){
                                alert("未找到对应商品，请输入商品ID");
                                $("#bd"+id).remove();
                            }

                            var boxSize = parseInt(jsonData.box_size);
                            if(boxSize == 0){
                                boxSize = 1;
                            }

                            $("#info"+id).html('['+jsonData.product_id+']'+jsonData.name);
                            $("#sku"+id).html(jsonData.sku);
                            $("#boxqty"+id).val(boxSize);
                            //$("#price"+id).html(jsonData.price);
                            $("#productBarcodeProduct"+id).html(jsonData.product_id);
                            $("#product_id_"+id).html(jsonData.product_id);
                            
                            if(jsonData.status == 999){
                                alert("[出库回库退货]未登录，请登录后操作");
                                window.location = 'inventory_login.php?return=r.php&ver=db';
                            }

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


    function handleProductList(){
        var rawId = $('#product').val();
        id = rawId.substr(0,6);//Get 18 code

        var method = $("#method").val();
        

        //Avoid exist barcode
        if(id.length == 6){
            
            
            var barCodeId = "#bd"+id;
            
            if($(barCodeId).length > 0){
                $("input[name='product']").val("");
            }
            else{
                
                //如果是回收筐，判断是否能回收
                if(method == 'inventoryFrameIn'){
                    
                        $.ajax({
                            type : 'POST',
                            url : 'invapi.php',
                            data : {
                                method : 'checkFrameCanIn',
                                frame_list : id
                            },
                            success : function (response, status, xhr){
                                if(response){
                                    
                                    var jsonData = $.parseJSON(response);
                        
                                    
                                    if(jsonData.status == 999){
                                        alert("[handleList]未登录，请登录后操作");
                                        window.location = 'inventory_login.php?return=r.php&ver=db&ver=db';
                                    }
                                    
                                    if(jsonData.status == 1){
                                        
                addProduct2(id);
                                        
            }
                                    if(jsonData.status == 0){
                                        alert(jsonData.timestamp);
                                        $("#product").val("");
        }
                                    if(jsonData.status == 5){
                                        alert(jsonData.timestamp);
                                        $("#product").val("");
                                    }
                                    
                                    
                                    
                                }
                            }
                        });
                }
        else{
                    addProduct2(id);
                }
                
                
                
                
            }
        }
        else{
            $("input[name='product']").val("");
        }
    }

    function handleProductList2(){
        var rawId = $('#product2').val();
        id = rawId.substr(0,18);//Get 18 code

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
    
    function qtyminus(id){
        var prodId = "#"+id;
       
        var barcodeId = '#bd'+id;
        $(barcodeId).remove();
        
    }

    
    function addCheckFrameIn(){
        var frame_list = getProductBarcodeWithQty();
        if(frame_list == ''){
            alert("请扫描框号");
            return false;
        }
      
        if(confirm("确认回收篮框？")){
            $.ajax({
                type : 'POST',
                url : 'invapi.php?method=addCheckFrameIn',
                data : {
                    method : 'addCheckFrameIn',
                    frame_list : frame_list
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        


                        if(jsonData.status == 999){
                            alert("[回收篮筐]未登录，请登录后操作");
                            window.location = 'inventory_login.php?return=r.php&ver=db&ver=db';
                        }

                        if(jsonData.status == 1){
                            alert("提交收框成功");
                            
                            $('#productsInfo').html("");
                            inventoryMethodHandler('inventoryFrameIn');
                        }
                        if(jsonData.status == 0){
                            alert(jsonData.timestamp);
                            
                            $("#product").val("");
                        }
                        if(jsonData.status == 2){
                            alert("系统中无订单分拣框号信息，请联系仓库");
                        }
                         if(jsonData.status == 3){
                            alert("扫描框号与仓库分拣框号不符，请检查");
                        }
                        if(jsonData.status == 4){
                            alert("此订单已提交确认到货，请不要重复操作");
                        }
                        if(jsonData.status == 5){
                            alert(jsonData.timestamp);
                            
                            $("#product").val("");
                        }
                    }
                }
            });
        }else{
            return false;
        }
    }


    function getAddedReturnDeliverProduct(){
        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=getAddedReturnDeliverProduct&isback='+$('#isBack').val(),
            data : {
                method : 'getAddedReturnDeliverProduct',
                searchDate : $("#searchDate").val(),
                isBack : $('#isBack').val(),
                isRepackMissing: $('#isRepackMissing').val()
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

                        if(parseInt(value.in_part) == 1){
                            returnQty = value.quantity + '/' + value.box_quantity + '[拆]';
                            unitPrice = '<i class="f0_8rem">［拆包单价' + parseFloat(value.price).toFixed(2) + '元]</i>';
                        }
                        var disableReutrnButton = '<br /><input class="addprod style_red" id="submitReturnProduct" type="button" value="取消" onclick="if(confirm(\'取消['+value.product+']退货吗？\')){ javascript:disableReturnDeliverProduct('+value.return_deliver_product_id+');}">';
                        if(parseInt(value.confirmed)==1){
                            disableReutrnButton = '<br /><span style="font-size: 0.8rem">[已确认]</span>';
                        }

                        html += '<tr class="barcodeHolder" >' +
                            '<td>'+value.order_id+disableReutrnButton+'</td>' +
                            '<td>'+value.product_id+'</td>' +
                            '<td>'+value.product+unitPrice+'</td>' +
                            '<td>'+returnQty+'</td>' +
                            '<td>'+ parseFloat(value.total).toFixed(2) +'</td>' +
                            '</tr>';
                    });
                    if(html == ''){
                        html = '<tr><td colspan="5">无记录</td></tr>';
                    }

                    $('#productsInfo3').html(html);
                }
            }
        });
    }

    function disableReturnDeliverProduct(id){
        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=disableReturnDeliverProduct',
            data : {
                method : 'disableReturnDeliverProduct',
                return_deliver_product_id : id
            },
            success : function (response, status, xhr){
                if(response){
                    console.log(response);

                    var jsonData = $.parseJSON(response);
                    alert(jsonData.message);

                    inventoryMethodHandler($('#returnMethod').val());
                }
            }
        });
    }

    //快销品当面退
    function submitReturnProduct(){
        var return_reason = $("#return_reason").val();
        var prodListWithQty = getReturnProductBarcodeWithQty();
        if(prodListWithQty == 0){
            alert("输入的数量不合法");return false;
        }
        
        if(prodListWithQty == '' || prodListWithQty == null ){
            alert('获取条码列表错误或还没有输入商品条码。');
            return false;
        }
        var order_id = $("#input_order_id2").val();
        if(order_id == ''){
            alert('请扫描订单号。');
            return false;
        }
        
        if(confirm('确认提交此次操作？')){
            
            $('#submitReturnProduct').attr('class',"submit style_gray");
            $('#submitReturnProduct').attr('disabled',"disabled");
            $('#submitReturnProduct').attr('value',"正在提交...");

            $.ajax({
                type : 'POST',
                url : 'invapi.php?method=addReturnDeliverProduct',
                data : {
                    method : 'addReturnDeliverProduct',
                    order_id : order_id,
                    products : prodListWithQty,
                    return_reason:return_reason,
                    isBack : parseInt($('#isBack').val()),
                    isRepackMissing: parseInt($('#isRepackMissing').val())
                },
                success : function (response , status , xhr){
                    if(response){
                        console.log(response);

                        var jsonData = $.parseJSON(response);
                        if(jsonData.status){
                            if(jsonData.status !== 1){
                                alert(jsonData.message);
                                return false;
                            }
                            if(jsonData.status == 1){
                                alert("提交成功");
                                $('#productsInfo2').html("");
                                //inventoryMethodHandler($('#returnMethod').val());
                                getAddedReturnDeliverProduct();
                            }
                        }
                        else{
                            //$('#message').attr('class',"message style_error");
                            $('#message').html(jsonData.message);
                            //console.log('Inv. Process Error.');
                        }


                        if(jsonData.status == 999){
                            alert(jsonData.msg);
                            window.location = 'inventory_login.php?return=r.php&ver=db';
                        }
                    }
                },
                complete : function(){
                    $('#submitReturnProduct').attr('class',"submit");
                    $('#submitReturnProduct').removeAttr("disabled");
                    $('#submitReturnProduct').attr('value',"提交");
                }
            });
        }
    }

    function confirmReturnProduct(){

        if(confirm('确认这些退货记录吗，操作不可撤销？')){

            $('#confirmReturnProduct').attr('class',"submit style_gray");
            $('#confirmReturnProduct').attr('disabled',"disabled");
            $('#confirmReturnProduct').attr('value',"正在提交...");

            $.ajax({
                type : 'POST',
                url : 'invapi.php?method=confirmReturnDeliverProduct',
                data : {
                    method : 'confirmReturnDeliverProduct',
                    searchDate : $("#searchDate").val(),
                    isBack : parseInt($('#isBack').val()),
                    isRepackMissing: parseInt($('#isRepackMissing').val())
                },
                success : function (response , status , xhr){
                    if(response){
                        console.log(response);

                        var jsonData = $.parseJSON(response);
                        alert(jsonData.message);
                        getAddedReturnDeliverProduct();

//                        if(jsonData.status !== 1){
//                            return false;
//                        }
//
//                        if(jsonData.status == 2){
//                            alert("提交确认成功");
//                            if(jsonData.message !== ''){
//                                alert("部分订单退货金额有误未确认，请核实"+jsonData.message);
//                            }
//                            getAddedReturnDeliverProduct();
//                        }


                        if(jsonData.status == 999){
                            alert(jsonData.msg);
                            window.location = 'inventory_login.php?return=r.php&ver=db';
                        }
                    }
                },
                complete : function(){
                    $('#confirmReturnProduct').attr('class',"submit");
                    $('#confirmReturnProduct').removeAttr("disabled");
                    $('#confirmReturnProduct').attr('value',"全部确认");
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
    
    </script>


<script>
    //获取整单退货订单信息
    function hide (){
        $('#productList2').hide();
        $('#issue').show();
        $('#message').html('整单退货,如果有商品缺失,请在纸质单上做好详细记录');
    }

    $("input[name='input_issue_order']").keyup(function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));

        if(tmptxt.length >= 6){
            getIssueOrderInfo(tmptxt);
            getOrderInfo(tmptxt);
            getLogisticId(tmptxt);
        }
    }).bind("paste",function(){

    });
    function  getOrderInfo(order_id){
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getOrderInfo',
                data: {
                    order_id: order_id,
                }
            },
            success:function(response){
                var jsonData = $.parseJSON(response);
                console.log(jsonData);
                var html = '';
                $.each(jsonData,function(i,v){
                    html +="<span>"+ v.frame_count + " 框 "+ v.box_count+ "箱"+"</span>";

                });
                $("#order_info").html(html);
            }
        });
    }


    function getIssueOrderInfo(order_id){
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getIssueOrderInfo',
                data: {
                    order_id: order_id,
                }
            },
            success:function(response){
                var jsonData = $.parseJSON(response);
                console.log(jsonData);
                var html = '';
                $.each(jsonData,function(i,v){
                    html +="<tr id='"+v.product_id+"' >";
                    if(v.repack == 1){
                        html += "<td style='border:1px solid'>";
                        html +="<span style='background: red'>"+ '散件' + "/ "+ "</span>"+"<span>"+ v.product_id + "/ "+ "</span>"+"<span>"+ v.name + "/ "+ "</span>"+"<span>"+ v.sku + "/ "+ "</span>"
                        html +="</td>";
                    }else{
                        html += "<td style='border:1px solid'>";
                        html +="<span>"+ '整件' + "/ "+ "</span>"+"<span>"+ v.product_id + "/ "+ "</span>"+"<span>"+ v.name + "/ "+ "</span>"+"<span>"+ v.sku + "/ "+ "</span>"
                        html +="</td>";
                    }

                    html +="<td style='border:1px solid'>";
                    html +="<span>"+ v.quantity+"</span>";
                    html +="</td>";
                    html +="</tr>";
                });
                $("#issue_order_tableInfo").html(html);
            },
            complete:function(){
                $("#issue_info").show();
                $("#span_logistic_allot_id").show();
                getIssueReason();

            }
        });
    }

    function getLogisticId(order_id){

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getLogisticId',
                data: {
                    order_id: order_id,
                }
            },
            success:function(response){
                var  html ='';
                var jsonData = $.parseJSON(response);
                if(response){
                    $.each(jsonData, function(index, value){
                        html += '<option value='+ value.logistic_allot_id +' >' + value.logistic_allot_id + '</option>';
                    });
                    $("#logistic_allot_id").html(html);
                }
            }
        });
    }

    function getIssueReason(){
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getIssueReason',

            },
            success:function(response){
            var jsonData = $.parseJSON(response);
            console.log(jsonData);

            var html = '<option value=0>-请选择所在仓库-</option>';
            $.each(jsonData, function(index, value){
                html += '<option value='+ value.issue_reason_id +' >' + value.name + '</option>';
            });
            $("#issue_reason").html(html);
        }
        });
    }
    function  redistr(){
        var order_id = $("#input_issue_order").val();
        var issue_reason = $("#issue_reason").val();
        var position_num = $("#position_num").val();
        var inventory_user = '<?php echo $_COOKIE['inventory_user'];?>';
        var logistic_allot_id = $("#logistic_allot_id").val();
        if(issue_reason == 0  || position_num == ''){
            alert('需要选择退货原因和重新定义货位号');
        }else{
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'redistr',
                    data:{
                        order_id :order_id,
                        issue_reason :issue_reason,
                        position_num:position_num,
                        inventory_user:inventory_user,
                        logistic_allot_id:logistic_allot_id,
                    }
                },
                success:function(response){

                        if(response == 1){

                            alert('该订单所属配送单号已操作过整单退货');
                        }else if(response == 2){
                            alert('操作成功');
                            getIssueOrderInfo(order_id);
                        }else if(response ==3){
                            alert('不能操作整单退货');
                        }
                }
            });
        }

    }

    function reDistrList(){
        $("#issue_info_list").show();
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'reDistrList',

            },
            success:function(response){
                var jsonData = $.parseJSON(response);
                console.log(jsonData);
                var html = '';
                $.each(jsonData,function(i,v){
                    html +="<tr id='"+v.order_id+"' >";
                    html +="<td style='border:1px solid'>";
                    html +="<span>"+ v.order_id + "</span>"
                    html +="</td>";
                    html +="<td style='border:1px solid'>";
                    html +="<span>"+ v.logistic_allot_id + "</span>"
                    html +="</td>";
                    html +="<td style='border:1px solid'>";
                    html +="<span>"+ v.logistic_driver_title+"</span>";
                    html +="</td>";
                    html +="<td style='border:1px solid'>";
                    html +="<span>"+ v.deliver_date+"</span>";
                    html +="</td>";
                    html +="<td style='border:1px solid'>";
                    html +="<span>"+ v.date_added+"</span>";
                    html +="</td>";
                    html +="<td style='border:1px solid'>";
                    html +="<span>"+ v.name+"</span>";
                    html +="</td>";
                    html +="</tr>";
                });
                $("#issue_order_listInfo").html(html);
            },

        });
    }


</script>
</body>
</html>