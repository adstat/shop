<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>测试-鲜世纪库存管理-仓库</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.simpleplayer.js"></script>
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


        .addprod{
            cursor: pointer;
            color: #fff;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .qty{
            width:1.2em;
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

        .ime-disabled{ime-mode:disabled;}


    </style>

    <style media="print">
        .noprint{display:none;}
    </style>
</head>
<body>
    <div align="center" style="display:block; margin:0.5em auto" id="logo"><img src="logo.png" style="width:6em"/></div>

    <div id="login" align="center" style="margin:0.5em auto; display: none">
        <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
        <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
    </div>

    <div id="content" style="display: block">
        <div align="center" style="margin:0.5em auto;">
            <select name="station" id="station" onchange="javascript:chooseStation();">
                <!-- Insert Stations Here -->
            </select>
        </div>
        <div align="center" style="margin:0.5em auto;">
            <input class="submit_s style_gray" type="button" value="退出" onclick="javascript:cancel();">
            <input class="submit_s style_yellow" type="button" value="前五天变动" onclick="javascript:getInvMoveList(5);">
            <input class="submit_s style_yellow" type="button" value="当天库存变动" onclick="javascript:getInvMoveList(1);">
        </div>
        <div id="message" class="message style_ok" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
        <div id="inv_control" align="center">
            <div id="invMethods">
                <!-- Insert Inventory Control Methods Here-->
                正在加载...
            </div>
            <div id="shelfLifeStrict" style="display: none"></div>
            <div id="productList" name="productList" method="POST" style="display: none">
                <table id="productsHold" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                    <tr>
                        <th align="left">ID/名称</th>
                        <th style="width:2.4em">价格</th>
                        <th style="width:2.4em">数量</th>
                        <th style="width:5em">操作</th>
                    </tr>
                    <tbody id="productsInfo">
                        <!-- Scanned Product List -->
                    </tbody>
                </table>

                <div id="barcodescanner" style="display: none">
                    <form method="get" onsubmit="handleProductList(); return false;">
                        <input name="product" id="product" rows="1" maxlength="19" onclick="javascript:clickProductInput();" autocomplete="off" placeholder="点击激活这里开始扫描" class="ime-disabled"/>
                        <input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">
                    </form>
                </div>

                <input type="hidden" name="method" id="method" value="" />
                <div style="float:left">当前时间: <span id="currentTime"><?php echo date('Y-m-d H:i:s', time());?></span></div>
                <br />
                <input class="submit" id="submit" type="button" value="提交" onclick="javascript:inventoryProcess();">
                <input class="submit style_yellow" type="button" value="获取商品信息" onclick="javascript:getProductName();">
                <!-- <input class="submit style_gray" type="button" value="取消" onclick="javascript:cancel();"> -->
            </div>
        </div>
        <div style="display: none">
            <div comment="Used for alert but hide">
                <audio id="player" src="sound/redalert.wav">
                    Your browser does not support the <code>audio</code> element.
                </audio>
            </div>
        </div>
        <div style="float: none; clear: both"><hr style="border: 0.1em #999 dashed"></div>
        <div id='move_list' align="center" style="display:none; margin:0.5em auto;">
            <!-- Insert Move List -->
            <table id="invMovesHold" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                <tr>
                    <th style="width:4.2em">类型</th>
                    <th style="">站点/添加时间</th>
                    <th style="width:2.4em">总数</th>
                    <th style="width:2.4em">操作</th>
                </tr>
                <tbody id="invMovesInfo">
                <!-- Scanned Product List -->
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                </tbody>
            </table>
        </div>
    </div>

    <div id="print" style="display: none">
        <div id='invMovesPrint' align="center" style="margin:0.5em auto;">
            <!-- Insert Move List -->


            <div class="noprint"><input class="submit_s style_gray" type="button" value="返回主页" onclick="javascript:backhome();"></div>

            <div id="invMovesPrintCaption" style="padding: 0 5px;">类型:<span id="prtInvMoveType"></span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;门店:<span id="prtInvMoveTitle"></span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;添加时间:<span id="prtInvMoveTime"></span></div>
            <div style=" padding: 10px 5px;">
                <table id="invMovesPrintHold" border="0" style="width:100%;"  cellpadding=0 cellspacing=0>
                    <tr>
                        <th style="width:4.2em">商品ID</th>
                        <th align="left" style="">商品名称</th>
                        <th style="width:4em">价格</th>
                        <th style="width:3em">数量</th>
                        <th style="width:10em">备注</th>
                        <th style="width:10em">其他</th>
                    </tr>
                    <tbody id="invMovesPrintInfo">
                        <!-- Scanned Product List -->
                    </tbody>
                </table>
            </div>
            <div style="padding: 0 5px; display: block; float: none; clear: both">
                <div style="float: right">
                    合计数量: <span id="invMovesPrintQtyTotal">0</span>(件)&nbsp;&nbsp;&nbsp;
                    合计金额: <span id="invMovesPrintAmountTotal">0</span>(元)
                </div>
            </div>
            <div style="padding: 0 5px; display: block; float: none; clear: both">
                <div style="float: left">打印时间: <span id="printTime"></span></div>
                <div style="float: right">签收: ________</div>
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
                method : 'init'
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

        $('#productList').show();
        $('title').html($(methodId).text() + '-鲜世纪库存管理');
        $('#logo').html('鲜世纪库存管理－'+$(methodId).text());


        if($('#station').val() > 0){
            $('#station').attr('disabled',"disabled");
        }
        locateInput();
    }

    function chooseStation(){
        if($('#station').val() > 0){
            $('#barcodescanner').show();
            $('#product').focus();

            if($('#method').val()){
                $('#station').attr('disabled',"disabled");
            }
        }
        else{
            $('#product').blur();
            $('#barcodescanner').hide();
        }
    }

    function checkStation(){
        if($('#station').val() <= 0){
            alert('请选择站点');

            $('#product').blur();
            $('#station').focus();

            return false;
        }

        return true;
    }

    function getSetDate(dateGap,dateFormart) {
        var dd = new Date();
        dd.setDate(dd.getDate()+dateGap);//获取AddDayCount天后的日期

        return dd.Format(dateFormart);

        //console.log(getSetDate(1,'yyMMdd')); //Tomorrow
        //console.log(getSetDate(-1,'yyMMdd')); //Yesterday
    }

    function addProduct(id){
        //var id = parseInt(id);

        //Barcode rules for Code128(18) OR Ean13(13||12)
        //18: 6+6+6
        //12: 1+5+5+x
        //13: 2+5+5+x

        if(id !== ''){

            if(id.length == 18){
                var productId = parseInt(id.substr(6,6));
                var price = parseInt(id.substr(12,6))/100;
            }
            else if(id.length == 12 || id.length == 13){
                var productId = parseInt(id.substr(1-(12-id.length),5));
                var price = parseInt(id.substr(1-(12-id.length)+5,5))/100;
            }
            else{
                return false;
            }

            if(productId == NaN || price == NaN){
                console.log('Error barcode format');
                return;
            }

            var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                '<td><span name="productBarcode" >' + id + '</span><br /><span id="info'+ id +'"></span></td>' +
                '<td>'+ price +'</td>' +
                '<td><input class="qty" id="'+ id +'" name="'+ id +'" value="1" /></td>' +
                '<td>' +
                '<input class="qtyopt" type="button" value="+" onclick="javascript:qtyadd(\''+ id +'\')" >' +
                '<input class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus(\''+ id +'\')" >' +
                '</td>' +
                '</tr>';
            $('#productsInfo').append(html);
            $('#product').val('');

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
        locateInput();
    }


    function markBarCodeLine(barCodeId,color){
        //console.log('Marked Barcode line'+barCodeId);
        $(barCodeId+' td').css('backgroundColor',color);
    }

    function locateInput(){
        $('#product').focus();
    }

    function getProductBarcodeList(){
        var prodList = '';
        var m = 0;
        $('#productsInfo tr').each(function () {
            var productBarcode = $(this).find('span[name=productBarcode]').text();

            if(m == $("#productsInfo tr").length-1){
                prodList += productBarcode;
            }
            else{
                prodList += productBarcode+',';
            }

            m++;
        });

        return prodList;
    }

    function getProductBarcodeWithQty(){
        var prodList = '';
        var m = 0;
        $('#productsInfo tr').each(function () {
            var productBarcode = $(this).find('span[name=productBarcode]').text();
            var productBarcodeId = '#'+productBarcode;
            var productBarcodeQty = $(productBarcodeId).val();

            if(m == $("#productsInfo tr").length-1){
                prodList += productBarcode+':'+productBarcodeQty+'';
            }
            else{
                prodList += productBarcode+':'+productBarcodeQty+',';
            }

            m++;
        });

        return prodList;
    }

    function getProductName(){
        console.log('Get products name from barcode');

        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }

        var prodList = getProductBarcodeList();

        if(prodList == '' || prodList == null){
            alert('获取条码列表错误或还没有输入商品条码。');
            return false;
        }

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getProductInfo',
                products : prodList
            },
            success : function (response , status , xhr){
                if(response){
                    //console.log(response);
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);

                    $.each(jsonData, function(index,value){
                        var infoId = "#info"+index;
                        $(infoId).html(value);
                    });
                }
            },
            complete : function(){

            }
        });

        locateInput();
    }

    function handleProductList(){
        var id = $('#product').val();

        //Avoid exist barcode
        var barCodeId = "#bd"+id;
        if($(barCodeId).length > 0){
            $('#product').val('');

            console.log('Add exist product barcode:'+id);
            return qtyadd(id);
        }
        else{
            addProduct(id);
            console.log('Add product barcode:'+id);
        }

    }

    function qtyadd(id){
        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 1;
        $(prodId).val(qty);

        locateInput();

        console.log(id+':'+qty);
    }

    function qtyminus(id){
        var prodId = "#"+id;
        if($(prodId).val() > 1){
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);
        }
        else{
            var barcodeId = '#bd'+id;
            $(barcodeId).remove();
        }

        locateInput();

        console.log(id+':'+qty);
    }

    function inventoryProcess(){
        var method = $('#method').val();
        var prodListWithQty = getProductBarcodeWithQty();
        var station = $('#station').val();

        console.log('Process inventory method:'+method);
        console.log('Process inventory data:'+prodListWithQty);

        if(method == ''){
            alert('请确认操作类型。');
            return false;
        }

        if(!checkStation()){
            return false;
        }

        if(prodListWithQty == '' || prodListWithQty == null ){
            alert('获取条码列表错误或还没有输入商品条码。');
            return false;
        }

        if(confirm('确认提交此次［'+$('#'+method).text()+'］操作？')){
            $('#submit').attr('class',"submit style_gray");
            $('#submit').attr('value',"正在提交...");

            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : method,
                    station : station,
                    products : prodListWithQty
                },
                success : function (response , status , xhr){
                    if(response){
                        //console.log(response);


                        var jsonData = $.parseJSON(response);
                        if(jsonData.status){
                            $('#message').attr('class',"message style_ok");
                            $('#productsInfo').html('');
                            $('#productList').hide();
                            $('#invMethods').show();

                            console.log('Inv. Process OK');
                        }
                        else{
                            $('#message').attr('class',"message style_error");
                            console.log('Inv. Process Error.');
                        }

                        $('#message').show();
                        $('#message').html(jsonData.msg);
                    }
                },
                complete : function(){
                    $('#submit').attr('class',"submit");
                    $('#submit').attr('value',"提交");
                }
            });
        }
    }

    function getInvMoveList(gap){
        //$('#move_list').html(222);
        console.log('Get Station Move List In ['+gap+'] Day(s).');

        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }

        var station = $('#station').val();

        $('#move_list').show();

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getStationMove',
                station : station,
                date_gap : gap
            },
            success : function (response , status , xhr){
                var html = '<td colspan="4">正在载入...</td>';
                $('#invMovesInfo').html(html);

                if(response){
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    html = '';
                    $.each(jsonData, function(index,value){
                        if(value.inventory_type_id !== '5'){
                            html += '<tr>' +
                                '<td align="center"><span id="invMoveType_'+ value.inventory_move_id +'">' + value.move_name + '</span></td>' +
                                '<td><span id="invMoveTitle_'+ value.inventory_move_id +'">' + value.station_title + '</span><br /><span id="invMoveTime_'+ value.inventory_move_id +'">' + value.date_added + '</div></td>' +
                                '<td align="center">' + value.total_qty + '</td>' +
                                '<td><input class="submit_s style_yellow" type="button" value="打印" onclick="javascript:printInvMove('+value.inventory_move_id+');"></td>' +
                                '</tr>';
                        }
                    });

                    $('#invMovesInfo').html(html);
                }
            },
            complete : function(){

            }
        });

        locateInput();
    }


    function printInvMove(invMoveId){
        $('#content').hide();
        $('#print').show();

        $('#prtInvMoveType').html( $('#invMoveType_'+invMoveId).html() );
        $('#prtInvMoveTitle').html( $('#invMoveTitle_'+invMoveId).html() );
        $('#prtInvMoveTime').html( $('#invMoveTime_'+invMoveId).html() );

        $('#printTime').html($('#currentTime').html());

        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }

        var station = $('#station').val();

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getStationMoveItem',
                station : station,
                invMoveId : invMoveId
            },
            success : function (response , status , xhr){
                var html = '<tr><td colspan="5">正在载入...</td></tr>';
                $('#invMovesPrintInfo').html(html);
                var invMovesPrintQtyTotal = 0;
                var invMovesPrintAmountTotal = 0;

                if(response){
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    html = '';
                    $.each(jsonData, function(index,value){
                        html += '<tr>' +
                            '<td align="left">' + value.product_id + '</td>' +
                            '<td align="left">' + value.name + '</td>' +
                            '<td align="center" style="font-size:14px; font-weight: bold">' + value.price + '</td>' +
                            '<td align="center" style="font-size:16px; font-weight: bold">' + value.quantity + '</td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '</tr>';
                        invMovesPrintQtyTotal += parseInt(value.quantity);
                        invMovesPrintAmountTotal += parseFloat(value.price)*parseInt(value.quantity);
                    });

                    $('#invMovesPrintInfo').html(html);
                    $('#invMovesPrintQtyTotal').html(invMovesPrintQtyTotal);
                    $('#invMovesPrintAmountTotal').html(invMovesPrintAmountTotal.toFixed(2));
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
    </script>
</body>
</html>