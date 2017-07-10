<?php
//设定时区
date_default_timezone_set('Asia/Shanghai');

if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}

$inventory_user_admin = array('alex','leibanban','wuguobiao','yangpeng','penglie');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器

    header("Location: inventory_login.php?return=w_dis.php");


    //确保重定向后，后续代码不会被执行
    exit;
}


?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪订单分拣</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
    <link href="view/stylesheet/inv.css" type="text/css" rel="stylesheet"/>

    <style media="print">
        .noprint{display:none;}

    </style>

    <script>
        $(document).keydown(function (event) {
            $('#product').focus();
        });
    </script>
</head>
<body>
<script type="text/javascript">
    var is_admin = 0;
</script>
<div align="right"><?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <span onclick="javascript:logout_inventory_user();">退出</span>
    <?php if(in_array($_COOKIE['inventory_user'], $inventory_user_admin)){?>
        <script type="text/javascript">
            is_admin = 1;
        </script>
        <a target="_blank" href="inv_data.php" style="margin: 0 5px;">查看分拣数据</a>
        <a target="_blank" href="invAllot.php" style="margin: 0 5px;">[测]补货建议</a>
    <?php } ?>
</div>
<div  style="display: none" id="warehouse_id"> <?php echo $_COOKIE['warehouse_id'];?> </div>
<div align="center" style="display:block; margin:0.5em auto" id="logo"><img src="view/image/logo.png" style="width:6em"/> 订单分拣<button class="invopt" style="display: inline" onclick="javascript:location.reload();">刷新</button></div>

<?php

if(isset($_GET['date'])){
    $date_array = array();

    $date_array[0]['date'] = date("m-d",  strtotime($_GET['date']));
    $date_array[1]['date'] = date("m-d",strtotime($_GET['date']));
    $date_array[2]['date'] = date("Y-m-d",strtotime($_GET['date']));
    $date_array[2]['shortdate'] = date("m-d",strtotime($_GET['date']));
}
else{
    $date_array = array();

    $date_array[0]['date'] = date("m-d",time());
    $date_array[1]['date'] = date("m-d",time() + 24*3600);
    $date_array[2]['date'] = date("Y-m-d",time() + 12*3600);
    $date_array[2]['shortdate'] = date("m-d",time() + 12*3600);

}


?>


<form name= "select" method="post">
    <div style="display:block; margin:0.5em auto" id="sec1">
        <span> 商品分类:</span>
        <select id="product_id" style="width:10em;" onchange="ordered();">
            <option value="1">生鲜蔬果</option>
            <option value="5001">清美中粮冷藏品</option>
            <option value="5002">冷冻商品</option>
            <!--<option value="5003">清美中粮</option>-->
            <option value="5004">快销品</option>
            <!--<option value="5005">日化品</option>-->
            <!--<option value="5006">辣味(贤哥)</option>-->
        </select>
        <span> 送货日期:</span>
        <input type='datetime' id="deliver_date" name = "deliver_date" value = "<?php echo $date_array[2]['date']; ?>" style="width:10em;">
    </div>


    <div id="sec2" style="display:inline; margin:0.5em auto">
        <span> 订单状态:</span>
        <select id="orderStatus" name="orderStatus" style="width:10em;"style="float:left;">

        </select>
    </div>

    <div id="sec2" style="display:inline; margin:0.5em auto">
        <span> 订单区域:</span>
        <select id="areaIds" name="areaIds" style="width:10em;"style="float:left;">

        </select>
    </div>
    <input type="button" onclick="javascript:getOrderBycdt();" value="查询" style="width:10em;">

</form>
<div  style="display:inline-block; margin:0.5em auto" id="sec3" class="sec3" >
    <h1>用户名</h1>
    <select id="inventory_name" class="inventory_name"size=12 style="heigth:100%;width:200px">
        <option selected value="xsfj001">xsfj001</option>
        <option value="xsfj002">xsfj002</option>
        <option value="xsfj003">xsfj003</option>
        <option value="xsfj004">xsfj004</option>
        <option value="xsfj005">xsfj005</option>
        <option value="xsfj006">xsfj006</option>
        <option value="xsfj007">xsfj007</option>
        <option value="xsfj008">xsfj008</option>
        <option value="xsfj009">xsfj009</option>
        <option value="xsfj010">xsfj010</option>
        <option value="xsfj011">xsfj011</option>
        <option value="xsfj012">xsfj012</option>
        <option value="xsfj013">xsfj013</option>
        <option value="xsfj014">xsfj014</option>
        <option value="xsfj015">xsfj015</option>
        <option value="xsfj016">xsfj016</option>
        <option value="xsfj017">xsfj017</option>
        <option value="xsfj018">xsfj018</option>
        <option value="xsfj019">xsfj019</option>
        <option value="xsfj020">xsfj020</option>

        <option value="xsfj021">xsfj021</option>
        <option value="xsfj022">xsfj022</option>
        <option value="xsfj023">xsfj023</option>
        <option value="xsfj024">xsfj024</option>
        <option value="xsfj025">xsfj025</option>
        <option value="xsfj026">xsfj026</option>
        <option value="xsfj027">xsfj027</option>
        <option value="xsfj028">xsfj028</option>
        <option value="xsfj029">xsfj029</option>
        <option value="xsfj030">xsfj030</option>


        <option value="rhfj001">rhfj001</option>
        <option value="rhfj002">rhfj002</option>
        <option value="rhfj003">rhfj003</option>
        <option value="rhfj004">rhfj004</option>
        <option value="rhfj005">rhfj005</option>
        <option value="rhfj006">rhfj006</option>
        <option value="rhfj007">rhfj007</option>
        <option value="rhfj008">rhfj008</option>
        <option value="rhfj009">rhfj009</option>
        <option value="rhfj010">rhfj010</option>

        <option value="lwfj001">lwfj001</option>
        <option value="lwfj002">lwfj002</option>
        <option value="lwfj003">lwfj003</option>
        <option value="lwfj004">lwfj004</option>
        <option value="lwfj005">lwfj005</option>
        <option value="lwfj006">lwfj006</option>
        <option value="lwfj007">lwfj007</option>
        <option value="lwfj008">lwfj008</option>
        <option value="lwfj009">lwfj009</option>
        <option value="lwfj010">lwfj010</option>






    </select>
</div>

<div style="display:inline-block; margin:0.5em auto" id="sec4" class="sec4" >
    <h1>订单 <span style="font-size: 12px;">(增加“浦东”标记，仅作为参考，新注册用户地址尚未定位)</span></h1>
    <select id="order_id"  size="8" multiple style="heigth:100%;width:500px">

    </select>
    <input id="distr" type="button"onclick="javascript:orderdistr()" value="分配" style="width:10em;" >
</div>
<div style="display:block; margin:0.5em auto" id="sec4">
    <h1>已分配</h1>

    <table border="0" style="width:100%;" cellpadding="2" cellspacing="3" id="orderlist">
        <thead>
        <tr>
            <th>分类</th>
            <th>分拣人</th>
            <th>总数量</th>
            <th >订单ID</th>

        </tr>
        </thead>
        <tbody id="orderedlist">

        </tbody>
    </table>


</div>

<div id="login" align="center" style="margin:0.5em auto; display: none">
    <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
    <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
</div>

<div align="center" style="margin:0.5em auto;">







    <input style="display: none; font-size: 0.9em; line-height: 0.9em" id="getplanned" class="submit_s style_lightgreen" type="button" value="获取计划入库值(<?php echo $date_array[2]['shortdate']; ?>)" onclick="javascript:getSortingList('<?php echo $date_array[2]['date']; ?>');">
    <input type="hidden" value=0 id="purchasePlanId" />
</div>
<div id="message" class="message style_ok" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
<div id="inv_control" align="center">
    <div id="invMethods">

    </div>
    <div id="shelfLifeStrict" style="display: none"></div>
    <div id="productList" name="productList" method="POST" style="display: none">

        <div id="product_name" style="font-size:2em;" align="center"></div>
        <table id="productsHoldDo" border="0" style="width:100%;display:none;"  cellpadding=2 cellspacing=3>
            <tr>
                <th style="width:4em">计划量</th>
                <th style="width:4em">待投篮</th>
                <th align="center" id="current_do_tj"></th>
            </tr>
            <tbody id="productsInfoDo">
            <tr>
                <td id="current_product_plan"  align="center" style="font-size:3.75em;"></td>
                <td id="current_product_quantity" align="center" style="font-size:2.5em;"></td>
                <td id="current_product_quantity_change" align="center">

                </td>
            </tr>
            </tbody>
        </table>

        <div id="barcodescanner" style="display: none">
            <form method="get" onsubmit="handleProductList(); return false;">
                <input name="product" id="product" rows="1" maxlength="19" onclick="javascript:clickProductInput();" autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; height: 2em;"/>
                <!--<input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">-->
            </form>
        </div>
        <script type="text/javascript">
            $("input[name='product']").keyup(function(){
                var tmptxt=$(this).val();
                $(this).val(tmptxt.replace(/\D|^0/g,''));

                if(tmptxt.length >= 4){
                    handleProductList();
                }
            }).bind("paste",function(){
                var tmptxt=$(this).val();
                $(this).val(tmptxt.replace(/\D|^0/g,''));
            });
            //$("input[name='product']").css("ime-mode", "disabled");
        </script>

        <input type="hidden" id="current_do_product" value="0">
        <div style="display:block; margin-top: 1em;">
            <span style=" font-size: 1.2em;">选择待投篮商品</span>
            <span style="float:right;font-size: 1em; line-height: 1.8em;">共<span id="count_plan_quantity"></span>件,待完成<span id="count_quantity"></span>件</span>
        </div>
        <table id="productsHold" border="0" style="width:100%;"  cellpadding=2 cellspacing=3>
            <tr>
                <th align="left">商品</th>
                <th style="width:2em">计划量</th>
                <th style="width:2em">待投篮</th>
                <th style="width:3em">状态</th>
            </tr>
            <tbody id="productsInfo">
            <!-- Scanned Product List -->
            </tbody>
        </table>







        <input type="hidden" name="method" id="method" value="" />
        <div style="float:left">当前时间: <span id="currentTime"><?php echo date('Y-m-d H:i:s', time());?></span></div>
        <br />


        <?php if(in_array($_COOKIE['inventory_user'],$inventory_user_admin)){?>
            <input class="submit" id="submit" type="button" value="提交" onclick="javascript:addOrderProductToInv_pre();">
        <?php } ?>


        <!--<input class="submit style_yellow" type="button" value="获取商品信息" onclick="javascript:getProductName();">-->
        <!-- <input class="submit style_gray" type="button" value="取消" onclick="javascript:cancel();"> -->
    </div>
</div>
<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="player" src="view/sound/redalert.wav">
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
                    <th align="left" style="width:4em">商品ID</th>
                    <th align="left" style="">商品名称</th>
                    <th style="width:4em">价格</th>
                    <th style="width:3em">数量</th>
                    <th style="width:3em">备注</th>
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
    </div>
</div>

<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="player3" src="view/sound/ding.mp3">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>
<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="player2" src="view/sound/redalert.wav">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>

<div id="overlay">

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



    /* 显示遮罩层 */
    function showOverlay() {
        $("#overlay").height(pageHeight());
        $("#overlay").width(pageWidth());

        // fadeTo第一个参数为速度，第二个为透明度
        // 多重方式控制透明度，保证兼容性，但也带来修改麻烦的问题
        $("#overlay").fadeTo(200, 0.5);
    }

    /* 隐藏覆盖层 */
    function hideOverlay() {
        $("#overlay").fadeOut(200);
    }


    /* 当前页面高度 */
    function pageHeight() {
        return document.body.scrollHeight;
    }

    /* 当前页面宽度 */
    function pageWidth() {
        return document.body.scrollWidth;
    }

    $(document).ready(function () {
        var html ;
        $('#orderedlist').html(html);
        var product_id = $('#product_id').val();
        var order_status_id = 2;
        var warehouse_id = $("#warehouse_id").text();


        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=ordered',
            data : {
                method : 'ordered',
                date : '<?php echo $date_array[2]['date']; ?>',
                product_id:product_id,
                order_status_id:order_status_id,
                warehouse_id :warehouse_id,
            },
            success : function (response , status , xhr){

                if(response){
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    var html = '';

                    var each_i_num = 1;

                    $.each(jsonData, function(index, value){
                        var t_status_class = '';
                        var product_str = '';

                        if(value.ordclass == 1){
                            product_str = '蔬鲜';
                        }
                        if(value.ordclass == 2){
                            t_ord_class = "style = 'background-color:#ffff00;'";
                            product_str = '冷藏品等';
                        }
                        if(value.ordclass == 3){
                            t_ord_class = "style = 'background-color:#ffff00;'";
                            product_str = '冷冻常温';
                        }

                        if(value.ordclass%2 == 1){
                            t_ord_class = "style = 'background-color:#ffff99;'";

                        }else{

                            t_ord_class = "style = 'background-color:#666666;'";
                        }



                        html += '<tr >';
                        html += '<td '+t_ord_class+'>'+'<br>'+product_str+'</td>'
                        html += '<td '+t_ord_class+'>'+value.inventory_name;
                        html += '<td '+t_ord_class+'>'+value.total;
                        html += '<td '+t_ord_class+'>';
                        var strs = value.groups;
                        var arr = strs.split(",");
                        for(var item in arr){
                            t_ord_class = "style = 'background-color:#ffff99;'";
                            var str = arr[item];
                            var order_status_id = str.substr(0,1);
                            var noPriceTag = str.substr(1,1);
                            var order_id = str.substr(2,6);
                            var len = str.length-3;
                            var quantity = str.substring(8,len);
                            var name = str.substr(str.length-3,3);

                            html += '<span id= "sp_'+order_id+'" '+t_ord_class+'> '+order_id+'('+quantity+')'+','+name;
                            if(noPriceTag == 1 ){
                                var gname ="无价签";
                                html += '<button class="invopt" style="display: inline" >无价签</button>'
                            }
                            if(order_status_id == 2){
                                html += '<button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderRedistr('+order_id+','+value.ordclass+','+quantity+','+order_status_id+','+value.deliver_date+');">重新分配</button>';
                            }

                            html += '</span>' ;
                        }
                        html += '</td>';
                        html += '</td>';
                        html += '</tr>';
                        html += '';
                        //console.log(value.deliver_date);
                        each_i_num++;
                    });


                    $('#orderedlist').html(html);


                }
            },
            complete : function(){
                $.ajax({
                    type : 'POST',
                    url : 'invapi.php?vali_user=1&m=getOrderStatus',
                    data : {
                        method : 'getOrderStatus'
                    },
                    success : function (response , status , xhr){

                        if(response){
                            var jsonData = eval(response);
                            if(jsonData.status == 999){
                                alert("未登录，请登录后操作");
                                window.location = 'inventory_login.php?return=w.php';
                            }
                            var html = '<option value=0>-请选择订单状态-</option>';
                            $.each(jsonData, function(index, value){
                                html += '<option value='+ value.order_status_id +' >' + value.name + '</option>';
                            });

                            $('#orderStatus').html(html);
                            $("#orderStatus").val(2);
                        }
                    }
                });

                $.ajax({
                    type : 'POST',
                    url : 'invapi.php?vali_user=1&m=getAreaList',
                    data : {
                        method : 'getAreaList'
                    },
                    success : function (response , status , xhr){

                        if(response){

                            var jsonData = $.parseJSON(response);

                            var html = '<option value=0>-请选择区域-</option>';
                            $.each(jsonData.return_data.district_list, function(index, value){
                                html += '<option value='+ value.area_id_list +' >' + value.district + '</option>';
                            });

                            $('#areaIds').html(html);
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


    function orderInventory(order_id){
        $('#invMethods').hide();
        $("#orderListTable").hide();
        $('#productList').show();
        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>'+'鲜世纪订单分拣－'+order_id+'<br>'+$("#shipping_name_"+order_id).val()+' - '+$("#shipping_address_"+order_id).val()+'<br>'+$("#shipping_phone_"+order_id).val());
        $("#current_order_id").val(order_id);
        getOrderSortingList(order_id,0);
    }

    function orderInventoryView(order_id){
        $('#invMethods').hide();

        $("#orderListTable").hide();
        $('#productList').show();

        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>'+'鲜世纪订单分拣－'+order_id+'<br>'+$("#shipping_name_"+order_id).val()+' - '+$("#shipping_address_"+order_id).val()+'<br>'+$("#shipping_phone_"+order_id).val());
        $("#current_order_id").val(order_id);
        $("#product").hide();
        getOrderSortingList(order_id,1);
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

        if(method == 'inventoryIn'){
            $('#getplanned').show();
            //getSortingList('<?php echo $date_array[2]['date']; ?>');
        }
        else{
            $('#getplanned').hide();
        }


        if($('#station').val() > 0){
            $('#station').attr('disabled',"disabled");
        }
        locateInput();
    }

    function getOrderSortingList(order_id,is_view){
        $('#barcodescanner').show();
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getOrderSortingList',
                order_id : order_id,
                is_view : is_view
            },
            success : function (response , status , xhr){
                var html = '<td colspan="4">正在载入...</td>';
                $('#productsInfo').html(html);

                if(response){
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    if(jsonData.status == 1){
                        html = '';
                        var count_plan_quantity = 0;
                        var count_quantity = 0;

                        var order_been_over = "";
                        var order_been_over_size = "";
                        window.product_id_arr = {};
                        $.each(jsonData.data, function(index,value){

                            count_plan_quantity = parseInt(value.plan_quantity) + parseInt(count_plan_quantity);
                            count_quantity = parseInt(value.quantity) + parseInt(count_quantity);


                            if(value.barcode > 0){
                                product_id_arr[value.barcode] = value.product_id;
                            }
                            if(value.sku > 0){
                                product_id_arr[value.sku] = value.product_id;
                            }
                            product_id_arr[value.product_id] = value.product_id;

                            if(value.quantity > 0){
                                order_been_over = "";
                            }
                            else{
                                order_been_over = "style = 'background-color:#666666;'";
                                order_been_over_size = "style = 'background-color:#666666;font-size:2em;'";;

                            }



                            html += '<tr class="barcodeHolder"  id="bd'+ value.product_id +'">' +
                                '<td '+order_been_over+' class="prodlist" id="td'+value.product_id+'" >';
                            html += value.inv_class_sort + '  --';
                            html +=    '<span name="productBarcode" style="display:none;" >' ;



                            html += value.product_id;

                            html +=    '</span> <span name="productId" id="pid'+value.product_id+'">' + value.product_id + '</span>	<br />';
                            html +=    '<span id="info';

                            html += value.product_id;

                            html += '"></span>      </td>' +
                                '<td '+order_been_over_size+' align="center" class="prodlist" style="font-size:2em;">'+ value.plan_quantity +'</td>' +
                                '<td '+order_been_over+' align="center" class="prodlist"><input class="qty" id="'+ value.product_id +'" name="'+ value.product_id +'" value="'+value.quantity+'" /><input type="hidden" id="plan'+ value.product_id +'" value="'+value.quantity+'"><input type="hidden" id="old_plan'+ value.product_id +'" value="'+value.plan_quantity+'"><input type="hidden" id="do'+value.product_id+'" value="0"><input type="hidden" id="pur_plan'+value.product_id+'" value="'+value.purchase_plan_id+'"></td>' +
                                '<td '+order_been_over+' id="opera'+value.product_id+'">';
                            if(value.quantity > 0 ){

                                if(is_admin == 1){
                                    html +=    '<input class="qtyopt pda_add_inv_'+value.product_id+'"  type="button" value="+" onclick="javascript:qtyadd(\''+ value.product_id +'\')" >' +
                                        '<input class="qtyopt style_green pda_add_inv_'+value.product_id+'" type="button" value="-" onclick="javascript:qtyminus2(\''+ value.product_id +'\')" >'+
                                        '<input class="qtyopt style_green pda_add_inv_'+value.product_id+'"  type="button" value="提交" onclick="javascript:tjStationPlanProduct2(\''+ value.product_id +'\')" >';
                                }

                                html += '';
                            }
                            else{
                                html += '已完成';
                            }

                            html +=   '</td>' +
                                '</tr>';
                        });


                        console.log(product_id_arr);

                        $("#count_plan_quantity").html(count_plan_quantity);
                        $("#count_quantity").html(count_quantity);

                        $('#productsInfo').html(html);
                    }
                    else if(jsonData.status == -1){
                        alert('采购数据已获取并提交入库!');
                        $('#productsInfo').html('');
                    }
                    else if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=w.php";
                    }
                    else if(jsonData.status == 6){
                        alert('此订单已提交，不能重复分拣!');
                        $('#productsInfo').html('');
                    }
                    else{
                        alert('无采购数据!');
                        $('#productsInfo').html('');
                    }
                }
            },
            complete : function(){
                getProductName();
            }
        });
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

                if(productId > 3000){
                    alert('非法的商品编号');
                    return false;
                }
            }
            //else if(id.length == 12 || id.length == 13){
            //    var productId = parseInt(id.substr(1-(12-id.length),5));
            //    var price = parseInt(id.substr(1-(12-id.length)+5,5))/100;
            //}
            else{
                alert('错误的条码');
                return false;
            }

            if(productId == NaN || price == NaN){
                console.log('Error barcode format');
                return;
            }

            var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                '<td><span name="productBarcode" style="display: none" >' + id + '</span><span name="productId" >' + productId + '</span><br /><span id="info'+ id +'"></span></td>' +
                '<td>'+ price +'</td>' +
                '<td><input class="qty" id="'+ id +'" name="'+ id +'" value="1" readonly="readonly" /></td>' +
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
                method : 'getSortingProductInfo',
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

    }

    function clickProductInput(){
        $('#move_list').hide();
    }

    function handleProductList(){
        var rawId = $('#product').val();
        id = rawId.substr(0,18);//Get 18 code

        if(id.length == 18){
            id = parseInt(id.substr(6,6));
        }

        if(window.product_id_arr[id] > 0){

            id = window.product_id_arr[id];
            var barCodeId = "#bd"+id;

            $('#product').val('');
            if($("#"+id).val() == 0){

                var player = $("#player2")[0];
                player.play();
                alert("此商品已完成分拣，不要重复分拣");
                return false;
            }

            var current_do_product = $("#current_do_product").val();



            if(current_do_product == 0){

                showOverlay();

                $("#current_do_product").val(id);

                $("#product_name").html($("#info"+id).html());
                $("#current_do_tj").html('<span id="tj'+id+'" style="" onclick="javascript:tjStationPlanProduct(\''+id+'\')" class="invopt">提交</span>');
                $("#current_product_plan").html($("#old_plan"+id).val()+'<span style="display:none;" name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>');
                $("#current_product_quantity").html( '<input class="qty"  id="'+id+'" value="'+$("#"+id).val()+'"><input type="hidden" id="plan'+ id +'" value="'+$("#plan"+id).val()+'"><input type="hidden" id="old_plan'+ id +'" value="'+$("#old_plan"+id).val()+'"><input type="hidden" id="do'+id+'" value="0">');
                $("#current_product_quantity_change").html('<input style="height:3em;width:3em;" class="qtyopt" type="button" value="+" onclick="javascript:qtyadd(\''+id+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus(\''+id+'\')">');

                $("#productsHoldDo").show();

                $(barCodeId).remove();
                hideOverlay();


            }
            else{
                if(current_do_product == id){
                    qtyminus(id);
                }
                else{
                    var player = $("#player2")[0];
                    player.play();
                    alert("当前商品还未完成分拣，请先提交后再分拣其它商品");
                }

            }





            console.log('Add exist product barcode:'+id);

        }
        else{

            var player = $("#player2")[0];
            player.play();
            alert("此商品不需入库");
            $("#product").val("");
            //addProduct(id);
            console.log('Add product barcode:'+id);
        }
    }

    function qtyadd(id){
        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 1;

        var do_qty = parseInt($("#do"+id).val()) - 1;


        if(qty >= parseInt($("#plan"+id).val())){
            qty = parseInt($("#plan"+id).val());
            do_qty = 0;

        }

        $(prodId).val(qty);
        $("#do"+id).val(do_qty);
        //locateInput();

        console.log(id+':'+qty);
    }

    function qtyminus(id){
        var prodId = "#"+id;

        if($(prodId).val() >= 1){
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);

            var do_qty = parseInt($("#do"+id).val()) + 1;
            $("#do"+id).val(do_qty);
        }
        if($(prodId).val() == 0){

            //提交插入中间表
            addOrderProductStation(id);
            hideOverlay();


            var html = '';


            html += '<tr class="barcodeHolder"  id="bd'+ id +'">' +
                '<td style = "background-color:#666666;" class="prodlist" id="td'+id+'" ><span name="productBarcode" style="display:none;" >' + id + '</span> <span name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>	<br /><span id="info'+ id +'">'+$("#product_name").html()+'</span>      </td>' +
                '<td style = "background-color:#666666; font-size:2em;" align="center" class="prodlist" >'+ $("#old_plan"+id).val() +'</td>' +
                '<td style = "background-color:#666666;" align="center" class="prodlist"><input class="qty" id="'+ id +'" name="'+ id +'" value="'+$("#"+id).val()+'" /><input type="hidden" id="plan'+ id +'" value="'+id+'"><input type="hidden" id="do'+id+'" value="0"></td>' +
                '<td style = "background-color:#666666;" id="opera'+id+'">';

            html += '已完成';


            html +=   '</td>' +
                '</tr>';

            $("#productsInfo").append(html);

            $("#current_do_product").val(0);
            $("#productsHoldDo").hide();
            $("#product_name").html("");

        }

        //locateInput();

        console.log(id+':'+qty);
    }


    function qtyminus2(id){
        var prodId = "#"+id;

        if($(prodId).val() >= 1){
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);

            var do_qty = parseInt($("#do"+id).val()) + 1;
            $("#do"+id).val(do_qty);
        }
        if($(prodId).val() == 0){

            //提交插入中间表
            addOrderProductStation(id);
            hideOverlay();



            $(".pda_add_inv_"+id).hide();

            $("#current_do_product").val(0);
            $("#productsHoldDo").hide();
            $("#product_name").html("");

        }

        //locateInput();

        console.log(id+':'+qty);
    }

    function tjStationPlanProduct(id){
        addOrderProductStation(id);

        hideOverlay();
        var html = '';
        html += '<tr class="barcodeHolder"  id="bd'+ id +'">' +
            '<td class="prodlist" id="td'+id+'" ><span name="productBarcode" style="display:none;" >' + id + '</span> <span name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>	<br /><span id="info'+ id +'">'+$("#product_name").html()+'</span>      </td>' +
            '<td align="center" class="prodlist" style="font-size:2em;">'+ $("#old_plan"+id).val() +'</td>' +
            '<td align="center" class="prodlist"><input class="qty" id="'+ id +'" name="'+ id +'" value="'+$("#"+id).val()+'" /><input type="hidden" id="plan'+ id +'" value="'+$("#plan"+id).val()+'"><input type="hidden" id="old_plan'+ id +'" value="'+$("#old_plan"+id).val()+'"><input type="hidden" id="do'+id+'" value="0"></td>' +
            '<td id="opera'+id+'">';

        html += '';


        html +=   '</td>' +
            '</tr>';

        $("#productsInfo").append(html);

        $("#current_do_product").val(0);
        $("#productsHoldDo").hide();
        $("#product_name").html("");


    }

    function tjStationPlanProduct2(id){
        addOrderProductStation(id);

        hideOverlay();


        $("#current_do_product").val(0);
        $("#productsHoldDo").hide();
        $("#product_name").html("");


    }


    function addOrderProductStation(id){


        var order_id = $('#current_order_id').val();

        var product_id = $("#pid"+id).html();
        var product_quantity = $("#do"+id).val();

        if(product_quantity > $("#plan"+id).val()){
            product_quantity = $("#plan"+id).val();
        }

        if(product_quantity == 0){
            var player = $("#player3")[0];
            player.play();
            return false;
        }

        showOverlay();
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'addOrderProductStation',
                order_id : order_id,
                product_id : product_id,
                product_quantity : product_quantity
            },
            success : function (response, status, xhr){
                if(response){

                    var jsonData = $.parseJSON(response);
                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w.php';
                    }

                    $(".pda_add_inv_"+id).hide();

                    //var jsonData = eval(response);
                    var player = $("#player3")[0];
                    player.play();


                    $("#plan"+id).val($("#plan"+id).val()-product_quantity);

                    $("#count_quantity").html(parseInt($("#count_quantity").html()) - parseInt(product_quantity));

                    var jsonData = $.parseJSON(response);
                    return jsonData.status;
                }
            }
        });
    }



    function addOrderProductToInv_pre(){
        showOverlay();
        var order_id = $('#current_order_id').val();
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'addOrderProductToInv_pre',
                order_id : order_id
            },
            success : function (response, status, xhr){
                var jsonData = $.parseJSON(response);


                if(jsonData.status == 999){
                    alert("未登录，请登录后操作");
                    window.location = 'inventory_login.php?return=w.php';
                }

                if(jsonData.status == 1){
                    if(confirm("计划入库数量"+jsonData.plan_quantity+"，实际入库"+jsonData.do_quantity+"，是否确认提交完成？")){
                        addOrderProductToInv();
                    }else{
                        return false;
                    }
                }
                if(jsonData.status == 0){
                    alert("今天已经提交过了，不能重复提交");
                    hideOverlay();
                }
                if(jsonData.status == 2){
                    alert("无待确认提交的商品");
                    hideOverlay();
                }
            }
        });
    }


    function addOrderProductToInv(){



        var order_id = $('#current_order_id').val();


        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'addOrderProductToInv',
                order_id : order_id
            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);
                    hideOverlay();


                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w.php';
                    }

                    if(jsonData.status == 1){
                        alert("提交商品入库成功");
                    }
                    if(jsonData.status == 0){
                        alert("部分商品未提交入库成功，请重试或联系管理员");
                    }
                    if(jsonData.status == 2){
                        alert("无待确认提交的商品");
                    }
                }
            }
        });
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
                    products : prodListWithQty,
                    purchase_plan_id : $('#purchasePlanId').val()
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
                                '<td><span style="display:none" id="invMoveConcact_'+ value.inventory_move_id +'">' + value.contact_name +","+ value.contact_phone + '</span><span id="invMoveTitle_'+ value.inventory_move_id +'">' + value.station_title + '</span><br /><span id="invMoveTime_'+ value.inventory_move_id +'">' + value.date_added + '</div></td>' +
                                '<td align="center">' + value.total_qty + '</td>' +
                                '<td><input class="submit_s style_yellow" type="button" value="查看" onclick="javascript:printInvMove('+value.inventory_move_id+');"></td>' +
                                '</tr>';
                        }
                    });

                    $('#invMovesInfo').html(html);
                }
            },
            complete : function(){

            }
        });

        //locateInput();
    }


    function printInvMove(invMoveId){
        $('#content').hide();
        $('#print').show();

        $('#prtInvMoveType').html( $('#invMoveType_'+invMoveId).html() );
        $('#prtInvMoveTitle').html( $('#invMoveTitle_'+invMoveId).html() +' (' + $('#invMoveConcact_'+invMoveId).html() + ')' );
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
                    window.location = 'inventory_login.php?return=w.php';
                }
            });
        }
    }

    function getOrderBycdt(){
        var order_status_id =$("#orderStatus").val();
        var product_id  = $("#product_id").val();
        var deliverdate = $("#deliver_date").val();
        var area_id_list = $("#areaIds").val();
        var warehouse_id = $("#warehouse_id").text();


        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getOrderss',
                product_id : product_id,
                deliver_date :deliverdate,
                order_status_id : order_status_id,
                area_id_list : area_id_list,
                warehouse_id : warehouse_id,
            },
            success : function (response , status , xhr){

                if(response){
                    console.log(response);
                    var jsonData = eval(response);
                    if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=w_dis.php";
                    }

                    var html = '';
                    $.each(jsonData, function(index, value){
                        var name = "";
                        if(parseInt(value.is_nopricetag) == 1 && parseInt(value.station_id) == 1){
                            name = " [无价签] ";
                        }

                        var district = "";
                        if(value.district !== '' && parseInt(value.station_id) == 2){
                            district = " [" + value.district + ">" + value.area_name + "] ";
                        }

                        var groupname = '';
                        if(value.group_id > 1 && parseInt(value.station_id) == 2){
                            groupname = ' ['+value.group_shortname+'] ';
                        }

                        //增加闪电购标记
                        var spcTagSDG = '';
                        if(parseInt(value.customer_id == 8765)){
                            spcTagSDG = "[闪电购]";
                        }


                        if(value.s_weight_inv_flag > 0){
                            html += '<option value='+ value.order_id +'@'+value.quantity+' >重' + value.order_id + '('+ value.quantity +')'+name +' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '+value.date_added+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' + district + groupname + spcTagSDG + value.shipping_address_1+'</option>';
                        }
                        else{
                            html += '<option value='+ value.order_id +'@'+value.quantity+' >' + value.order_id + '('+ value.quantity +')'+name +' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  '+value.date_added+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' + district + groupname + spcTagSDG + value.shipping_address_1+'</option>';
                        }
                    });
                    $('#order_id').html(html);

                    //console.log('Load Stations');

                }
            }
        });
    }

    function orderRedistr(order_id,ordclass,quantity,order_status_id,deliver_date){
        var prod_id  = $("#product_id").val();
        var ord_status  = $("#orderStatus").val();
        var del_date  = $("#deliver_date").val();
        console.log(ordclass);

        ordered();
        /*
         if(ordclass==1){
         var product_id = 1;
         }else{
         var product_id = 5001;
         }
         */


        if(order_status_id == 2){
            $.ajax({
                type : 'POST',
                url:'invapi.php',
                data : {
                    method : 'orderRedistr',
                    date : '<?php echo $date_array[2]['date']; ?>',
                    ordclass : ordclass,
                    order_id : order_id
                },

                success : function (response , status , xhr){



                    if(response){
                        //console.log(response);
                        var obj=document.getElementById('sp_'+order_id);
                        obj.remove();
                    }
                },complete:function(){

                    getOrderBycdt();
                }
            });
        }else if(order_status_id == 3) {
            alert("订单已取消！");
        }else if(order_status_id == 5){
            alert("订单正在分拣中！");
        }else if(order_status_id == 6){
            alert("订单已分拣！");
        }else if(order_status_id == 10){
            alert("订单已完成！");
        }else{
            alert("订单无法操作！");
        }


    }

    function ordered(){

        var product_id = $('#product_id').val();
        var order_status_id = $('#orderStatus').val();
        //var deliver_date = $('#deliver_date').val();
        var deliverdate = $("#deliver_date").val();
        var warehouse_id = $("#warehouse_id").text();
        //var deliver_date = 20150610;
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'ordered',
                date : '<?php echo $date_array[2]['date']; ?>',
                product_id:product_id,
                order_status_id:order_status_id,
                deliver_date :deliverdate,
                warehouse_id :warehouse_id,
            },
            success : function (response , status , xhr){

                if(response){
                    //console.log(response);
                    var jsonData = $.parseJSON(response);
                    var html = '';

                    var each_i_num = 1;

                    $.each(jsonData, function(index, value){
                        var t_status_class = '';
                        var product_str = '';

                        if(value.ordclass == 1){
                            product_str = '蔬鲜';
                        }
                        if(value.ordclass == 2){
                            t_ord_class = "style = 'background-color:#ffff00;'";
                            product_str = '冷藏品等';
                        }
                        if(value.ordclass == 3){
                            t_ord_class = "style = 'background-color:#ffff00;'";
                            product_str = '冷冻常温';
                        }
                        if(value.ordclass == 4){
                            t_ord_class = "style = 'background-color:#ffff00;'";
                            product_str = '清美中粮';
                        }
                        if(value.ordclass == 5){
                            t_ord_class = "style = 'background-color:#ffff00;'";
                            product_str = '快销品';
                        }
                        if(value.ordclass == 6){
                            t_ord_class = "style = 'background-color:#ffff00;'";
                            product_str = '日化品';
                        }
                        if(value.ordclass == 7){
                            t_ord_class = "style = 'background-color:#ffff00;'";
                            product_str = '辣味(贤哥)';
                        }
                        t_ord_class = "style = 'background-color:#ffff99;'";

                        html += '<tr >';
                        html += '<td '+t_ord_class+'>'+'<br>'+product_str+'</td>'
                        html += '<td '+t_ord_class+'>'+value.inventory_name;
                        html += '<td '+t_ord_class+'>'+value.total;
                        html += '<td '+t_ord_class+'>';
                        var strs = value.groups;
                        var arr = strs.split(",");
                        for(var item in arr){
                            t_ord_class = "style = 'background-color:#ffff99;'";
                            var str = arr[item];
                            var order_status_id = str.substr(0,1);
                            var group = str.substr(1,1);
                            var order_id = str.substr(2,6);
                            var len = str.length-3;
                            var quantity = str.substring(8,len);
                            var name = str.substr(str.length-3,3);

                            html += '<span id= "sp_'+order_id+'" '+t_ord_class+'>'+order_id+'('+quantity+')'+','+name;
                            if(group == 2 ){
                                var gname ="无价签";
                                html += '<button class="invopt" style="display: inline" >无价签</button>'
                            }
                            if(order_status_id==2){
                                html += '<button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderRedistr('+order_id+','+value.ordclass+','+quantity+','+order_status_id+','+value.deliver_date+');">重新分配</button>';
                            }

                            html += '</span>' ;
                        }
                        html += '</td>';
                        html += '</td>';
                        html += '</tr>';
                        html += '';
                        //console.log(value.deliver_date);
                        each_i_num++;
                    });


                    $('#orderedlist').html(html);
                }
            },
            complete : function(){

                getOrderBycdt();
            }

        });

    }

    function orderdistr(){
        var inventory_name = $("#inventory_name").val();
        var product_id  = $("#product_id").val();
        var order_id = $("#order_id").val();
        //console.log(order_id);
        var warehouse_id = $("#warehouse_id").text();
        $.ajax({
            type : 'POST',
            url:'invapi.php',
            data : {
                method : 'orderdistr',
                date : '<?php echo $date_array[2]['date']; ?>',
                order_id : order_id,
                product_id:product_id,
                inventory_name : inventory_name,
                warehouse_id :warehouse_id,
            },

            success : function (response , status , xhr){

                //comsole.log(response);
                if(response){

                    var obj=document.getElementById('order_id');
                    var index=obj.selectedIndex;
                    obj.options.remove(index);
                    /*
                     var objadd=document.getElementById('tr_'+order_id);
                     objadd.remove();
                     objlist.options.add(new Option(order_id+'('+quantity+')',order_id+'@'+quantity));
                     */
                }
            },complete : function(){
                ordered();
                //alert("分配成功");
            }

        });
    }

    function getOrderByStatus(){
        var order_status_id = $("#orderStatus").val();
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getOrders',
                date : '<?php echo $date_array[2]['date']; ?>',
                order_status_id : order_status_id

            },
            success : function (response , status , xhr){
                //console.log(response);

                if(response){

                    var jsonData = $.parseJSON(response);



                    if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=w_dis.php";
                    }

                    var html = '';

                    var each_i_num = 1;
                    $.each(jsonData.data, function(index, value){
                        var t_status_class = '';
                        var product_str = '';


                        if(value.order_product_type == 1){
                            product_str = '菜';
                        }
                        if(value.order_product_type == 2){
                            t_status_class = "style = 'background-color:#ffff00;'";
                            product_str = '菜+奶';
                        }
                        if(value.order_product_type == 3){
                            t_status_class = "style = 'background-color:#9933ff;'";
                            product_str = '奶';
                        }





                        if(value.order_status_id == 1){
                            t_status_class = "style = 'background-color:#ffff99;'";

                        }
                        if(value.order_status_id == 2){
                            //t_status_class = "";
                        }
                        if(value.order_status_id == 3){
                            t_status_class = "style = 'background-color:#666666;'";
                        }
                        if(value.order_status_id == 5){
                            //t_status_class = "";
                        }
                        if(value.order_status_id == 6){
                            //t_status_class = "";
                        }


                        html += '<tr>';
                        html += '<td '+t_status_class+'>'+each_i_num+'<br>'+product_str+'</td>'
                        html += '<td '+t_status_class+'>'+value.order_id;

                        if(value.customer_group_id == 2){
                            html += '<br><span style="color:red;">无价签</span>';
                        }

                        html +='<input type="hidden" id="shipping_name_'+value.order_id+'" value="'+value.shipping_name+'"><input type="hidden" id="shipping_phone_'+value.order_id+'" value="'+value.shipping_phone+'"><input type="hidden" id="shipping_address_'+value.order_id+'" value="'+value.shipping_address_1+'"></td>';
                        html += '<td '+t_status_class+'>'+value.plan_quantity+'</td>';
                        html += '<td '+t_status_class+'>'+value.quantity+'</td>';
                        html += '<td '+t_status_class+'>'+value.added_by+'</td>';
                        html += '<td '+t_status_class+'>'+value.name+'</td>';
                        html += '<td '+t_status_class+'><button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderInventoryView('+value.order_id+');">查看</button>';
                        if((value.order_status_id == 2 || value.order_status_id == 5 ) && value.no_inv != 1){
                            html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+');">开始分拣</button>';
                        }

                        /*
                         if(is_admin == 1){
                         html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+');">提交分拣</button>';
                         }
                         */
                        html += '</td>';
                        html += '</tr>';
                        html += '';

                        each_i_num++;
                    });








                    $('#ordersList').html(html);

                    // console.log('Load Stations');
                }
            }

        });
    }
</script>
</body>
</html>