<?php
if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';
$inventory_user_admin = array('randy','alex','leibanban','yangyang','wuguobiao');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器 
    header("Location: inventory_login.php?return=l.php&ver=db"); 
    //确保重定向后，后续代码不会被执行 
    exit;
}


?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪篮框管理-物流</title>
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
        <div id="message" class="message style_ok" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
        <div id="inv_control" align="center">
            <div id="invMethods">
                
                <button id="inventoryFrameCheck" class="invopt" style="display: inline" onclick="javascript:inventoryMethodHandler('inventoryFrameCheck');">仓库确认框号(出库前操作)</button><br>
                
                <button id="inventoryFrameOut" class="invopt" style="display: inline" onclick="javascript:inventoryMethodHandler('inventoryFrameOut');">确认篮框到店</button>
               <button id="inventoryFrameIn" class="invopt" style="display: inline" onclick="javascript:inventoryMethodHandler('inventoryFrameIn');">回收篮框</button><br>
               <button id="inventoryFrameCage" class="invopt" style="display: inline" onclick="javascript:inventoryMethodHandler('inventoryFrameCage');">确认网笼到店</button><br>
               <!--<button id="inventoryFrameCage" class="invopt" style="display: inline" onclick="javascript:inventoryMethodHandler('inventoryFrameCage');">昨日退货</button>-->
               <button id="inventoryReturnProduct" class="invopt" style="display: inline" onclick="javascript:inventoryMethodHandler('inventoryReturnProduct');">快销品退货(物流回库)</button>
               
                
               
               
            </div>
            <div id="shelfLifeStrict" style="display: none"></div>
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
                <div id="order_id2" style="display:none;">扫描订单号<input style="font-size:1.4em;" type="text" id="input_order_id2" name="input_order_id2"></div>
                <div style="color: #df8505; font-size: 0.8rem;">＊订单<strong>[配送完成/配送失败]</strong>后由物流发起</div>
                <table id="productsHold2" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                    <tr>
                        <th style="width:2em">ID</th>
                        <th align="left" style="width:2em;">ID/名称</th>
                        <th style="width:2em; display: none">价格</th>
                        <th style="width:3em">数量</th>
                        <th style="width:5em">操作</th>
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
                    <form method="get" onsubmit="handleProductList2(); return false;">
                        <input name="product2" id="product2" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled;"/>
                        <input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">
                    </form>
        </div>
                <script type="text/javascript">
                    $("input[name='product2']").keyup(function(){
                        var tmptxt=$(this).val();
                        $(this).val(tmptxt.replace(/\D/g,''));
       
                        if(tmptxt.length >= 4){
                            handleProductList2();
                        }
                        //$(this).val("");
                    }).bind("paste",function(){
                            var tmptxt=$(this).val();
                            $(this).val(tmptxt.replace(/\D/g,''));
                        });
                    //$("input[name='product']").css("ime-mode", "disabled");
                </script>
        
                <div style="float:left">当前时间: <span id="currentTime2"><?php echo date('Y-m-d H:i:s', time());?></span></div>
                <br />
                <input class="submit" id="submitReturnProduct" type="button" value="提交" onclick="javascript:submitReturnProduct();">
                
                <br><br><br><br>
                <h1>今日已提交退货</h1>
                <table id="productsHold3" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                    <tr>
                        <th style="width:1em;">订单号</th>
                        <th style="width:1em">ID</th>
                        <th align="left" style="width:8em;">ID/名称</th>
                        <th style="width:1em">价格</th>
                        <th style="width:1em">数量</th>
                    </tr>
                    <tbody id="productsInfo3">
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
        $('#message').hide();
        $('#move_list').hide();

        if(method != 'inventoryReturnProduct'){
        $('#productList').show();
        }
        else{
            
           $('#productList2').show();
        }
        $('title').html($(methodId).text() + '-鲜世纪篮框管理');
        $('#logo').html('鲜世纪篮框管理－'+$(methodId).text());

        if(method == 'inventoryFrameOut'){
            
            $('#barcodescanner').show();
            $("#submit_frame_in").hide();
            $("#submit_frame_cage").hide();
            $("#submit_frame_check").hide();
            $("#submit_frame_out").show();
            $("#return_index").show();
            $("#order_id").show();
           
           $("#input_order_id").focus();
           
           $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'getAddedFrameOut'
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        
                        var added_order_html = '';
                        added_order_html += '今日已提交订单：(总数量：'+jsonData.length+')：';
                        $.each(jsonData, function(index,value){
                            added_order_html += '<br>';
                            added_order_html += value.order_id;
                            
                        });
                        $("#showFrameData").html(added_order_html);
                    }
                }
            });
            
        }
        else if(method == 'inventoryFrameIn'){
            
            
            
            
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
       
       else if(method == 'inventoryFrameCheck'){
            
            
            $('#barcodescanner').show();
            $("#submit_frame_in").hide();
            $("#submit_frame_out").hide();
            $("#submit_frame_cage").hide();
            $("#submit_frame_check").show();
            $("#return_index").show();
            $("#order_id").show();
           $("#input_order_id").focus();
           $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'getAddedFrameCheck'
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        
                        var added_order_html = '';
                        added_order_html += '已检查订单：(总数量：'+jsonData.length+')：';
                        $.each(jsonData, function(index,value){
                            added_order_html += '<br>';
                            added_order_html += value.order_id;
                            
                        });
                        $("#showFrameData").html(added_order_html);
                    }
                }
            });
            

            
        }
        else if(method == 'inventoryFrameCage'){
            
            
            $('#barcodescanner').show();
            $("#submit_frame_in").hide();
            $("#submit_frame_out").hide();
            $("#submit_frame_check").hide();
            $("#submit_frame_cage").show();
            $("#return_index").show();
            $("#order_id").show();
           $("#input_order_id").focus();
           $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'getAddedFrameCage'
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        
                        var added_order_html = '';
                        added_order_html += '已提交网笼订单：(总数量：'+jsonData.length+')：';
                        $.each(jsonData, function(index,value){
                            added_order_html += '<br>';
                            added_order_html += value.order_id;
                            
                        });
                        $("#showFrameData").html(added_order_html);
                    }
                }
            });
            

            
        }
        else if(method == 'inventoryReturnProduct'){
            
            
            $('#barcodescanner2').show();
            $("#submit_frame_in").hide();
            $("#submit_frame_out").hide();
            $("#submit_frame_check").hide();
            $("#submit_frame_cage").hide();
            $("#return_index").show();
            $("#order_id").hide();
            $("#order_id2").show();
            $("#input_order_id2").val("");
            $("#input_order_id2").focus();
            
           $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'getAddedReturnDeliverProduct'
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        
                        if(jsonData.status == 999){
                            alert(jsonData.msg);
                            location.href = "inventory_login.php?return=l.php";
                        }
                        
                        var html = '';
                        $.each(jsonData, function(index,value){
                            html += '<tr class="barcodeHolder" >' +
                                    '<td>'+value.order_id+'</td>' +
                                '<td>'+value.product_id+'</td>' +
                                '<td>'+value.product+'</td>' +
                                '<td>'+ value.price +'</td>' +
                                '<td>'+value.quantity+'</td>' +
                                
                                '</tr>';

                        });


                        $('#productsInfo3').html(html);
                    }
                }
            });
           
           
          
            
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

    function addProduct_p(id){
        //var id = parseInt(id);

        //Barcode rules for Code128(18) OR Ean13(13||12)
        //18: 6+6+6
        //12: 1+5+5+x
        //13: 2+5+5+x

        if(id !== ''){
            if(id.length == 18){
                var productId = parseInt(id.substr(0,4));
                var price = parseInt(id.substr(4,5))/100;
                
               
            }
            if(id.length == 13 || id.length == 14 || id.length == 4 || id.length == 18){
                ajax_id = id;
               
                
                if(id.length == 18){
                    ajax_id = parseInt(id.substr(0,4));
                    
                }
                $.ajax({
                    type : 'POST',
                    url : 'invapi.php',
                    data : {
                        method : 'getSkuProductInfo',
                        sku : ajax_id
                    },
                    success : function (response , status , xhr){
                        if(response){
                            //console.log(response);
                            //var jsonData = eval(response);
                            var jsonData = $.parseJSON(response);


                            if(typeof(jsonData.price) == "undefined"){
                                alert("未找到对应商品，请输入商品ID");
                                $("#bd"+id).remove();
                            }

                            $("#info"+id).html(jsonData.product_id+'/'+jsonData.name);
                            $("#price"+id).html(jsonData.price);
                            $("#productBarcodeProduct"+id).html(jsonData.product_id);
                            $("#product_id_"+id).html(jsonData.product_id);
                            
                            if(jsonData.status == 999){
                                alert("未登录，请登录后操作");
                                window.location = 'inventory_login.php?return=l.php';
                            }

                        }
                    },
                    complete : function(){

                    }
                });
            }

            else{
                alert('错误的条码');
                return false;
            }

            if(productId == NaN || price == NaN){
                console.log('Error barcode format');
                return;
            }
            
            var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                    '<td id="product_id_'+id+'"></td>' +
                '<td><span name="productBarcode" >' + id + '</span><span style="display:none;" inputBarcode="'+id+'" class="productBarcodeProduct" name="productBarcodeProduct" id="productBarcodeProduct'+id+'" ></span><br /><span id="info'+ id +'"></span></td>' +
                //'<td id="price'+id+'">'+  +'</td>' +
                
                '<td style="width:4em;"><input class="qty" id="'+ id +'" name="'+ id +'" value="1"  /></td>' +
                '<td>' +
                '<input class="qtyopt" type="button" value="+" onclick="javascript:qtyadd2(\''+ id +'\')" >' +
                
                '<input class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus2(\''+ id +'\')" >' +
                '</td>' +
                '</tr>';
            $('#productsInfo2').append(html);
            $('#product2').val('');

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
    function getProductBarcodeWithQty2(){
        var prodList = '';
        var m = 0;
        var num_err_flag = 0;

        $('#productsInfo2 tr').each(function () {

            var productBarcode = $(this).find('span[name=productBarcodeProduct]').html();
            var inputProductBarcode = $(this).find('span[name=productBarcodeProduct]').attr("inputBarcode");
            
            var productBarcodeId = '#'+inputProductBarcode;
            
            var productBarcodeQty = $(productBarcodeId).val();
            
            if((productBarcodeQty < 0 || isNaN(productBarcodeQty)) && $("#method").val() != 'inventoryAdjust'){
                //alert(productBarcode);
                //alert(productBarcodeQty);
                //alert(typeof productBarcodeQty);
                num_err_flag = 1;
            }
            
            
            if(m == $("#productsInfo tr").length-1){
                if(parseInt(productBarcodeQty) > 0 || $("#method").val() == 'inventoryAdjust'){
                    prodList += productBarcode+':'+productBarcodeQty+'';
                }
                
            }
            else{
                if(parseInt(productBarcodeQty) > 0 || $("#method").val() == 'inventoryAdjust'){
                    prodList += productBarcode+':'+productBarcodeQty+',';
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
                                        alert("未登录，请登录后操作");
                                        window.location = 'inventory_login.php?return=l.php&ver=db';
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
    
        
        
         //if(id.length == 4 && !check_in_array(id,window.no_scan_product_id_arr)){
         //   alert("不能输入商品ID，必须扫描");
         //   return false;
         //}
        
        
        //Avoid exist barcode
        if(id.length == 18){
    
            
            product_id = id.substr(0,4);
            var barCodeId = "#bd"+product_id;
            
            if($(barCodeId).length > 0){
                $('#product2').val('');

                return qtyadd2(id);
            }
            else{
                addProduct_p(id);
                console.log('Add product barcode:'+id);
            }
        }
        else{
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



    
    function addCheckFrameOut(){
        
        
        var frame_list = getProductBarcodeWithQty();
        var order_id = parseInt($("#input_order_id").val());
        /*
        if(frame_list == ''){
            alert("请扫描框号");
            return false;
        }
        */
        if(order_id == ''){
            alert("请输入订单号");
            return false;
        }
        
        if(confirm("确认提交到店？")){
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'addCheckFrameOut',
                    order_id : order_id,
                    frame_list : frame_list
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        


                        if(jsonData.status == 999){
                            alert("未登录，请登录后操作");
                            window.location = 'inventory_login.php?return=l.php&ver=db';
                        }

                        if(jsonData.status == 1){
                            alert("提交到货成功");
                            $("#input_order_id").val("");
                            $('#productsInfo').html("");
                            inventoryMethodHandler('inventoryFrameOut');
                            
                        }
                        if(jsonData.status == 0){
                            alert("订单号错误，请输入正确的订单号");
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
                            alert("订单未提交分拣 或 未点配送中，请联系仓库");
                        }
                        if(jsonData.status == 6){
                            alert("订单号有误，不可输入快消品订单!");
                        }
                }
                }
            });
        }else{
            return false;
        }
    }

     function addCheckFrameCage(){
    
        
        var frame_list = getProductBarcodeWithQty();
        var order_id = parseInt($("#input_order_id").val());
        if(frame_list == ''){
            alert("请扫描框号");
            return false;
        }
        
        if(order_id == ''){
            alert("请输入订单号");
            return false;
        }
        
        if(confirm("确认提交网笼到店？")){
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'addCheckFrameCage',
                    order_id : order_id,
                    frame_list : frame_list
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        
                            

                        if(jsonData.status == 999){
                            alert("未登录，请登录后操作");
                            window.location = 'inventory_login.php?return=l.php&ver=db';
                        }

                        if(jsonData.status == 1){
                            alert("提交网笼到店成功");
                            $("#input_order_id").val("");
                            $('#productsInfo').html("");
                            inventoryMethodHandler('inventoryFrameCage');
                            
                        }
                        if(jsonData.status == 0){
                            alert("订单号错误，请输入正确的订单号");
                        }
                        if(jsonData.status == 2){
                            alert("系统中无订单分拣框号信息，请联系仓库");
                        }
                         if(jsonData.status == 3){
                            alert("扫描框号与仓库分拣框号不符，请检查");
                        }
                        if(jsonData.status == 4){
                            alert("此订单已提交网笼到货，请不要重复操作");
                        }
                        if(jsonData.status == 5){
                            alert(jsonData.timestamp);
                        }
                    }
                }
            });
        }else{
            return false;
        }
    }


    function addCheckFrameCheck(){
        
        
        var frame_list = getProductBarcodeWithQty();
        var order_id = parseInt($("#input_order_id").val());
        if(frame_list == ''){
            alert("请扫描框号");
            return false;
        }
        
        if(order_id == ''){
            alert("请输入订单号");
            return false;
        }
        
        if(confirm("确认提交检查框号？")){
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'addCheckFrameCheck',
                    order_id : order_id,
                    frame_list : frame_list
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        


                        if(jsonData.status == 999){
                            alert("未登录，请登录后操作");
                            window.location = 'inventory_login.php?return=l.php&ver=db';
                        }

                        if(jsonData.status == 1){
                            alert("框号数据正确");
                             $("#input_order_id").val("");
                            $('#productsInfo').html("");
                            inventoryMethodHandler('inventoryFrameCheck');
                        }
                        if(jsonData.status == 0){
                            alert("订单号错误，请输入正确的订单号");
                        }
                        if(jsonData.status == 2){
                            alert("系统中无订单分拣框号信息，请联系仓库");
                        }
                         if(jsonData.status == 3){
                            alert("扫描框号与仓库分拣框号不符，请检查");
                        }
                        if(jsonData.status == 4){
                            alert("此订单已检查正确，不要重复操作");
                        }
                    }
                }
            });
        }else{
            return false;
        }
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
                url : 'invapi.php',
                data : {
                    method : 'addCheckFrameIn',
                    frame_list : frame_list
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        


                        if(jsonData.status == 999){
                            alert("未登录，请登录后操作");
                            window.location = 'inventory_login.php?return=l.php&ver=db';
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


    //快销品当面退
    function submitReturnProduct(){

        var prodListWithQty = getProductBarcodeWithQty2();
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
            $('#submitReturnProduct').attr('value',"正在提交...");

            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'addReturnDeliverProduct',
                    order_id : order_id,
                    products : prodListWithQty
                },
                success : function (response , status , xhr){
                    if(response){
                        console.log(response);

                        var jsonData = $.parseJSON(response);
                        if(jsonData.status){
                            
                            
                            
                            if(jsonData.status == 3){
                                alert(jsonData.err_msg);
                                return false;
                            }
                            if(jsonData.status == 1){
                                alert("提交成功");
                                $('#productsInfo2').html("");
                                inventoryMethodHandler('inventoryReturnProduct');
                            }
                        }
                        else{
                            $('#message').attr('class',"message style_error");
                            console.log('Inv. Process Error.');
                        }

                       
                        if(jsonData.status == 999){
                            alert("未登录，请登录后操作");
                            window.location = 'inventory_login.php?return=l.php';
                        }
                    }
                },
                complete : function(){
                    $('#submitReturnProduct').attr('class',"submit");
                    $('#submitReturnProduct').attr('value',"提交");
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
                window.location = 'inventory_login.php?return=l.php&ver=db';
            }
        });
        }
    }
    
    
     window.no_scan_product_id_arr = new Array();
    <?php foreach($no_scan_product_id_arr_l as $key=>$value){ ?>
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
</html>