<?php

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
    <title>鲜世纪仓库分拣缺货确认表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>

    <link rel="stylesheet" type="text/css" href="view/css/i.css"/>

</head>

<header class="bar bar-nav">
    <div class="title"> <input type="button" class="invopt" style="background: red" id="return_index"  value="返回" onclick="javascript:history.back(-1);"><span id="small_title">明细核查</span><input class="invopt" style="background: red" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();"></div>

</header>
<hr>
<body >

<div align="center" >
    <input id="order_id_id" hidden="hidden" value ="<?php echo $_GET['order_id'];?>">订单号：<?php echo $_GET['order_id'];?></input>
    <h2 id="inv_comment">货位号</h2>
</div>
<hr>
<span  id = 'user_id' hidden="hidden"><?php echo $_COOKIE['inventory_user_id'];?></span>
<span  id = 'order_id' hidden="hidden" ><?php echo $_GET['order_id'];?></span>
<div id="check_details">
<hr>

<div >
    <form id="form_info">
        <table id="productsHold2" border="1" style="width:100%;" cellpadding=2 cellspacing=3>
            <tr style="background:#8fbb6c;">
                <th >ID/名称</th>
                <th >分拣量</th>
                <th >核实量</th>
                <th >操作</th>
            </tr>
            <tbody id="productsInfo2">
            <!-- Scanned Product List -->
            </tbody>
        </table>
    </form>

    <form id="form_info2" hidden="hidden">
        <table id="productsHold" border="1" style="width:100%;" cellpadding=2 cellspacing=3>
            <tr style="background:#8fbb6c;">
                <th >ID/名称</th>
                <th >分拣量</th>
                <th >核实量</th>
                <th >操作</th>
            </tr>
            <tbody id="productsInfo1">
            <!-- Scanned Product List -->
            </tbody>
        </table>
    </form>

    <form id="form_info3" hidden="hidden">
        <table id="productsHold3" border="1" style="width:100%;" cellpadding=2 cellspacing=3>
            <tr style="background:#8fbb6c;">
                <th >ID/名称</th>
                <th >分拣量</th>
                <th >核实量</th>
                <th >操作</th>
            </tr>
            <tbody id="productsInfo3">
            <!-- Scanned Product List -->
            </tbody>
        </table>
    </form>



</div>
<hr>
<div id="div_bar_code" align="center" style="height: 40px"><input id="bar_code" name="bar_code"  autocomplete="off" type="text" value="" style="height: 25px;border:1px solid" placeholder="商品条码"></div>
<div id="div_bar_code2" hidden="hidden" align="center" style="height: 40px"><input id="bar_code2"  name="bar_code"  autocomplete="off" type="text" value="" style="height: 25px;border:1px solid" placeholder="商品条码"></div>
<div id="div_bar_code3" hidden="hidden" align="center" style="height: 40px"><input id="bar_code3"  name="bar_code"  autocomplete="off" type="text" value="" style="height: 25px;border:1px solid" placeholder="商品条码">   </div>

<!--  <button id="single_button" name="single_button" style="width: 100px;height: 20px;background: red" type="button"     onclick="getSingle();">单个商品缺货</button>  -->
<button id="spare_button" name="single_button" style="width: 100px;height: 20px;background: red" type="button"     onclick="getSpareDetails();">散件商品缺货</button>
    <button id="whole_button" name="single_button" style="width: 100px;height: 20px;background: red" type="button"   hidden="hidden" onclick="javascript:location.reload();">整件商品缺货</button>

<hr>
<div align="center">
    <h2 id="order_list">订单列表</h2>

    <h2 id="order_list3"  hidden="hidden">散件缺货</h2>
    <form id="form-return">
        <table  border='1'cellspacing="0" cellpadding="0" id="order_table">
            <thead>
            <tr style="background:#8fbb6c;">
                <td>商品名称</td>
                <td>订单数</td>
                <td>待核实数</td>

            </tr>
            </thead>
            <tbody id="order_sorting_short">

            </tbody>

        </table>
    </form>

    <form id="form-return2" hidden="hidden">
        <table  border='1'cellspacing="0" cellpadding="0" id="order_table2">
            <thead>
            <tr style="background:#8fbb6c;">
                <td>商品名称</td>
                <td>分拣数</td>
                <td>待核实数</td>

            </tr>
            </thead>
            <tbody id="order_sorting_short2">

            </tbody>

        </table>
    </form>
    <form id="form-return3" hidden="hidden">
        <table  border='1'cellspacing="0" cellpadding="0" id="order_table3">
            <thead>
            <tr style="background:#8fbb6c;">
                <td>商品名称</td>
                <td>订单数</td>
                <td>待核实数</td>


            </tr>
            </thead>
            <tbody id="order_sorting_short3">

            </tbody>

        </table>
    </form>

    <div>周转箱:<span id="container"></span></div>
    <input  id="shortage"   style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="submitCheckDetails();" value="全部确定" >
    <!-- <input  id="shortage2"   style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="submitReturnSingle();" value="缺货提交"> -->
    <input  id="shortage3"  hidden="hidden"  style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="submitCheckSpareDetails();" value="全部确定">

    <div style="display: none">
        <div comment="Used for alert but hide">
            <audio id="playerSubmit" src="view/sound/ding.mp3">
                Your browser does not support the <code>audio</code> element.
            </audio>
        </div>
    </div>
</div>
</div>





</body>
<!--<script type="text/javascript">-->
<!--    short_regist();-->
<!--    getInvComment();-->
<!--</script>-->
<script>

    $(document).ready(function() {
        $('#search_details').hide();
        window.onload=document.getElementById('bar_code').focus();
        // 获取订单明细以及分拣数量
        $('#single_shortage').hide();
        $('#shortage2').hide();
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for (var i = 0; i < strs.length; i++) {
                theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
            }
        }
        var order_id = (theRequest['order_id']);
        var warehouse_id = '<?php $_COOKIE['warehouse_id'] ;?>';
        $('#find_order').hide();
        $('#short_regist').show();


        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            dataType: 'json',
            data: {
                method: 'location_details',
                data: {
                    order_id: order_id,
                    warehouse_id :warehouse_id,

                }
            },
            success: function (response) {

                if (response.status == 2) {
                    var productArray = [];
                    var html = '';
                    $.each(response.product[0], function (i, v) {
                        //  productArray.push(v.container_id);
                        html += '<span>' + v.container_id + ';&nbsp;</span>';
                    });
                    $("#container").html(html);

                }


                if (response.status == 2) {

                    var html = '';
                    $.each(response.product[1], function (i, v) {

                        html += "<tr style='background:#d0e9c6;' id='tr" + v.product_id + "'>";
                        html += "<td  id='name" + v.product_id + "'>" + "<span style='font-weight:bold'>" + v.product_id + "</span>" + '/' + v.name;
                        html += "<div>" + "<span style='font-weight:bold' >" + v.sku + "</span>" + "</div>"
                        html += "</td>";
                        html += "<td id='ordersorted" + v.product_id + "' >" + v.order_quantity + "</td>";
                        html += "<td id='unsorted" + v.product_id + "' >" + v.order_quantity + "</td>";
                        html +="</tr>";

                    });

                    $('#order_sorting_short').html(html);

                }
            }
        });


        // 获取货位号信息
        var order_id=$("#order_id").text();
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            dataType: 'json',
            data: {
                method: 'getInvComment',
                data: {
                    order_id:order_id,
                }
            },
            success: function(response){
                if(response.status ==2){
                    $('#inv_comment').text(response.inv_comment);
                }else{
                    $('#inv_comment').text('没查到货位号');
                }
            }
        });


    });
</script>
<script>

    $("input[id='bar_code']").keyup(function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
        if( tmptxt.length >= 4){
            handleProductList2();
            //handleProductList();
            handleProductList3();
        }

    }).bind("paste",function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
    });
    $("input[id='bar_code3']").keyup(function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
        if( tmptxt.length >= 5){
            handleProductList2();
            //handleProductList();
            handleProductList3();
        }

    }).bind("paste",function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
    });

</script>
<script>

    function soundEffectInit(){
        //音效设置
        var sound = {};
        sound.playerSubmit = $("#playerSubmit")[0];
        return sound;
    }


    function handleProductList2(){
        var rawId = $('#bar_code').val();
        var order_id = $('input[id=\'order_id_id\']').val();
        id = rawId.substr(0,18);//Get 18 code

        var ajax_id = id

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getProductID',
                data: {
                    sku: ajax_id,
                    order_id :order_id,
                }
            },

            success : function (response , status , xhr){
                var jsonData = $.parseJSON(response);
                var status = jsonData.status;
                if(status == 1){
                    alert('未找到对应商品，请输入正确的商品ID或条形码');
                    $('#bar_code').val('');
                }
                if(status == 2) {
                    var id = jsonData.product[0].product_id;
                    var barCodeId = "#bd" + id;

                    var prodId = "#" + id;
                    var sorting_num_num = $("#needsorted" + id).text();
                    if (parseInt($(prodId).val()) == parseInt(sorting_num_num)) {
                        $('#bar_code').val('');
                        getInfoMessage(id);
                    } else {
                        if ($(barCodeId).length > 0) {
                            $('#bar_code').val('');
                            qtyadd2(id);
                           return short_decrease(id);

                        } else {
                            $('#bar_code').val('');
                            addProduct_p(id);
                            short_decrease(id);
                        }

                    }
                }
            }

        });

    }
    function  getInfoMessage(id){
        var sorting_num= $("#unsorted"+id).text();
        var sorting_num_num= $("#needsorted"+id).text();
        var sorting_num2= $("#spareunsorted"+id).text();
        if(parseInt(sorting_num) == 0){
            alert('已经核实完成');
        }
        if(parseInt(sorting_num2) == 0){
            alert('已经核实完成');
        }
    }

    function addProduct_p(id){
        var order_id = $('input[id=\'order_id_id\']').val();

        //var id = parseInt(id);

        //Barcode rules for Code128(18) OR Ean13(13||12)
        //18: 6+6+6
        //12: 1+5+5+x
        //13: 2+5+5+x

        if(id !== ''){


            ajax_id = id;

            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'getSortNum',
                    data: {
                        order_id: order_id,
                        sku : ajax_id
                    }
                },

                success : function (response , status , xhr){
                    if(response){
                        var jsonData = $.parseJSON(response);

                        $("#order_id"+id).html(jsonData.quantity);

                    }
                }

            });

            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'getSkuProductInfoS',
                    data:{
                        sku : ajax_id,
                        order_id :order_id,
                    }
                },
                success : function (response , status , xhr){

                    var jsonData = $.parseJSON(response);
                    //    console.log(jsonData);
                    var status =jsonData.status
                    if(status == 2 ){

                        $("#info"+id).html(jsonData.product[0].product_id+'/'+jsonData.product[0].name+'/'+jsonData.product[0].sku);
                        $("#product_id_"+id).html(jsonData.product[0].product_id);

                    }
                },
                complete : function(){

                }
            });

            var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                '<td><span style="display:none;" inputBarcode="'+id+'" class="productBarcodeProduct" name="productBarcodeProduct" id="productBarcodeProduct'+id+'" ></span><span id="info'+ id +'"></span></td>' +
                '<td><span id="order_id'+ id +'"></td>'+

                '<td ><input style="width: 50px;" class="qty" id="'+ id +'" name="'+ id +'" value="1"  /></td>' +
                '<td>' +
                '<input class="qtyopt" type="button" id="button_id'+ id +'"  value="+" onclick="javascript:qtyadd2(\''+ id +'\');short_decrease(\''+ id +'\');" >' +

                '<input class="qtyopt style_green"  id="button_id'+ id +'" type="button" value="-" onclick="javascript:qtyminus2(\''+ id +'\');reduce(\''+ id +'\');" >' +
                '</td>' +
                '</tr>';

            $('#productsInfo2').html(html);
        }
    }

    function qtyadd2(id){
        var prodId = "#"+id;
        var sorting_num_num= $("#unsorted"+id).text();

        if( parseInt(sorting_num_num)==0){
            alert("已经核实完成");
           // $("#button_id"+id).attr('disabled',true);

        }else {
            var qty = parseInt($(prodId).val()) + 1;
            $(prodId).val(qty);
        }


        //  console.log(id+':'+qty);
    }

    function qtyminus2(id){
        var prodId = "#"+id;

        $("#button_id"+id).attr('disabled',false);


        if($(prodId).val() > 1 || $("#method").val() == 'inventoryAdjust'){
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);
        }
        else{

            var barcodeId = '#bd'+id;
            $(barcodeId).remove();
        }

        //locateInput();

        //   console.log(id+':'+qty);
    }

    function short_decrease(id){
        var sorting_num= $("#unsorted"+id).text();
        var sorting_num_num= $("#needsorted"+id).text();

        if(sorting_num >0) {
            var check_num = $("#" + id).val();
            if (parseInt(sorting_num_num) < parseInt(check_num)) {

                var sound = soundEffectInit();
                sound.playerSubmit.play();
                var choose_tr = $("#tr" + id).css("background-color", "#F9F900");
                //   $("#button_id"+id).attr('disabled',true);

            } else {

                //多个商品确认
                var check_sorting_num = $("#unsorted" + id).text(sorting_num - 1);
                var new_td_text = check_sorting_num[0].innerText;
                if (new_td_text == 0) {
                    $("#unsorted" + id).text(0);
                    $("#order_sorting_single").hide()
                    var choose_tr = $("#tr" + id).css("background-color", "#ADADAD");
                    var sound = soundEffectInit();
                    sound.playerSubmit.play();
                    var choose_tr_tr = $("#tr" + id);
                    $("#order_table tbody").append(choose_tr_tr);

                }
            }

        }else{

        }

    }

    function reduce(id){
        var sorting_num_num= $("#needsorted"+id).text();
        var unsorted =  $("#unsorted"+id).text();

        if( parseInt(unsorted) >= 0){

            var check_sorting_num= parseInt(unsorted)+1;

            if(check_sorting_num >0){
                var choose_tr = $("#tr" + id).css("background-color", "#d0e9c6");
            }
            var  check =  $("#unsorted"+id).text(check_sorting_num);
        }

    }

    function getSpareDetails(){
        $("#spare_button").hide();
        $("#whole_button").show();
        $("#form_info").hide();
        $("#form_info2").hide();
        $("#form_info3").show();
        $("#div_bar_code").hide();
        $("#div_bar_code2").hide();
        $("#div_bar_code3").show();
        $("#order_list").hide();
        $("#order_list2").hide();
        $("#order_list3").show();
        $("#form-return").hide();
        $("#form-return2").hide();
        $("#form-return3").show();
        $("#shortage").hide();
        $("#shortage2").hide();
        $("#shortage3").show();
        var order_id=$("#order_id").text();

        var warehouse_id = '<?php $_COOKIE['warehouse_id'] ;?>' ;
        window.onload=document.getElementById('bar_code3').focus();
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getSpareDetails',
                data: {
                    order_id: order_id,
                    warehouse_id  : warehouse_id
                }
            },
            success: function (response, status, xhr) {

                var jsonData = $.parseJSON(response);

                if(jsonData.status == 2){
                    console.log(jsonData.product);
                    var html = '';
                    $.each(jsonData.product[1],function(i,v){

                        html += "<tr style='background:#d0e9c6;' id='sparetr"+v.product_id+"'>";
                        html += "<td  id='sparename"+ v.product_id+"'>"+"<span style='font-weight:bold'>"+v.product_id +"</span>"+'/'+  v.name ;
                        html += "<div>"+"<span style='font-weight:bold' >"+ v.sku +"</span>" +  "</div>"

                        html += "</td>";
                        html += "<td id='spareneedsorted"+ v.product_id+"' >"+ v.order_quantity + "</td>";
                        html += "<td style='font-weight:bold' id='spareunsorted"+ v.product_id+"'>"+ v.order_quantity +  "</td>";
                       html  +="</tr>";

                    });

                    $('#order_sorting_short3').html(html);

                }
            }

        });

    }

    function handleProductList3(){
        var rawId = $('#bar_code3').val();
        var order_id = $('input[id=\'order_id_id\']').val();
        id = rawId.substr(0,18);//Get 18 code

        var ajax_id = id

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getSpareProductID',
                data: {
                    sku: ajax_id,
                    order_id :order_id,
                }
            },

            success : function (response , status , xhr){
                var jsonData = $.parseJSON(response);
                var status = jsonData.status;
                if(status == 1){
                    alert('未找到对应商品，请输入商品ID');
                    $('#bar_code3').val('');
                }
                if(status == 2) {
                    var id = jsonData.product[0].product_id;
                    var barCodeId = "#bd" + id;

                    var prodId = "#" + id;
                    var sorting_num_num = $("#spareneedsorted" + id).text();
                    if (parseInt($(prodId).val()) == parseInt(sorting_num_num)) {
                        $('#bar_code3').val('');
                        getInfoMessage(id);

                    } else {
                        if ($(barCodeId).length > 0) {
                            $('#bar_code3').val('');
                            short_decrease3(id);
                            return qtyadd3(id);
                        } else {
                            $('#bar_code3').val('');
                            addSpareProduct_p(id);
                            short_decrease3(id);
                        }

                    }
                }
            }

        });

    }

    function addSpareProduct_p(id){
        var order_id = $('input[id=\'order_id_id\']').val();
        if(id !== ''){
            ajax_id = id;
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'getSortNum',
                    data: {
                        order_id: order_id,
                        sku: ajax_id
                    }
                },
                success : function (response , status , xhr){

                    if(response){
                        var jsonData = $.parseJSON(response);

                        $("#order_id_id_id"+id).html(jsonData.quantity);

                    }
                }
            });

            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'getSpareSkuProductInfo',
                    data:{
                        sku : ajax_id,
                        order_id :order_id,
                    }
                },
                success : function (response , status , xhr){

                    var jsonData = $.parseJSON(response);
                    //    console.log(jsonData);
                    var status =jsonData.status
                    if(status == 2 ){

                        $("#info3"+id).html(jsonData.product[0].product_id+'/'+jsonData.product[0].name+'/'+jsonData.product[0].sku);
                        // $("#product_id3"+id).html(jsonData.product[0].product_id);

                    }
                },
                complete : function(){

                }
            });




            var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                '<td><span style="display:none;" inputBarcode="'+id+'" class="productBarcodeProduct" name="productBarcodeProduct" id="productBarcodeProduct'+id+'" ></span><span id="info3'+ id +'"></span></td>' +
                '<td><span id="order_id_id_id'+ id +'"></td>'+

                '<td><input style="width: 10px;" class="qty" id="'+ id +'" name="'+ id +'" value="1"  /></td>' +
                '<td>' +
                '<input class="qtyopt" type="button" id="spare_button_id'+ id +'"  value="+" onclick="javascript:qtyadd3(\''+ id +'\');short_decrease3(\''+ id +'\');" >' +

                '<input class="qtyopt style_green"  id="spare_button_id'+ id +'" type="button" value="-" onclick="javascript:qtyminus3(\''+ id +'\');reduce3(\''+ id +'\');" >' +
                '</td>' +
                '</tr>';

            $('#productsInfo3').html(html);


        }
    }
    function qtyadd3(id){
        var prodId = "#"+id;
        var sorting_num_num= $("#spareneedsorted"+id).text();
        if(parseInt($(prodId).val()) == parseInt(sorting_num_num)){
            $("#spare_button_id"+id).attr('disabled',true);
            //  alert("核实量跟分拣量相同");
        }else{
            var qty = parseInt($(prodId).val()) + 1;
            $(prodId).val(qty);
        }

        //  console.log(id+':'+qty);
    }

    function qtyminus3(id){
        var prodId = "#"+id;

        $("#spare_button_id"+id).attr('disabled',false);


        if($(prodId).val() > 1 || $("#method").val() == 'inventoryAdjust'){
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);
        }
        else{

            var barcodeId = '#bd'+id;
            $(barcodeId).remove();
        }

        //locateInput();

        //   console.log(id+':'+qty);
    }
    function short_decrease3(id){
        var sorting_num= $("#spareunsorted"+id).text();
        var sorting_num_num= $("#spareneedsorted"+id).text();

        if(sorting_num >0) {

            var check_num = $("#" + id).val();
            if (parseInt(sorting_num_num) < parseInt(check_num)) {

                var sound = soundEffectInit();
                sound.playerSubmit.play();
                var choose_tr = $("#tr" + id).css("background-color", "#F9F900");
                //   $("#button_id"+id).attr('disabled',true);

            } else {

                //多个商品确认
                var check_sorting_num = $("#spareunsorted" + id).text(sorting_num - 1);
                var new_td_text = check_sorting_num[0].innerText;
                if (new_td_text == 0) {
                    $("#spareunsorted" + id).text(0);

                    var choose_tr = $("#sparetr" + id).css("background-color", "#ADADAD");
                    var sound = soundEffectInit();
                    sound.playerSubmit.play();
                    var choose_tr_tr = $("#sparetr" + id);
                    $("#order_table3 tbody").append(choose_tr_tr);

                }
            }

        }else{
            alert("已经核实完成");
        }

    }

    function reduce3(id){
        var sorting_num_num= $("#spareneedsorted"+id).text();
        var unsorted =  $("#spareunsorted"+id).text();

        if(parseInt(sorting_num_num) >= parseInt(unsorted)){

            var check_sorting_num= parseInt(unsorted)+1;

            if(check_sorting_num >0){
                var choose_tr = $("#sparetr" + id).css("background-color", "#d0e9c6");
            }
            var  check =  $("#spareunsorted"+id).text(check_sorting_num);
        }

    }

    function submitCheckDetails(){

        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
            }
        }

        var  order_id = (theRequest['order_id']);

        var data = $("#order_sorting_short").find("tr");
        var postData = [] ;
        var productArray = [];
        var a = 0;
        var user_id=$("#user_id").text();

        $.each(data,function(i,v){

            productArray.push(v.id) ;
            productArray.push(v.childNodes[0].innerText) ;
            productArray.push(v.childNodes[1].innerText) ;
            productArray.push(v.childNodes[2].innerText) ;
            postData.push(productArray);
            productArray = [];
            //品名为v.childNodes[0].innerText，缺货数量为v.childNodes[2].innerText，商品ID为v.id
        });
        if(confirm("确认提交么？")) {
            $('#shortage').attr('class',"submit style_gray");
            $('#shortage').attr('value',"正在提交...");
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                dataType: 'json',
                data: {
                    method: 'submitCheckDetails',

                    data: {
                        add_user: user_id,
                        order_id: order_id,
                        product: postData,

                    }
                },
                success: function (response) {
                  if(response){
                      alert('提交成功');
                     // location.reload();
                  }
;
                },


                complete : function(){
                    $('#shortage').attr('class',"submit");
                    $('#shortage').attr('value',"提交");
                }
            });
        }
    }

    function submitCheckSpareDetails(){
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
            }
        }

        var  order_id = (theRequest['order_id']);

        var data = $("#order_sorting_short3").find("tr");
        var postData = [] ;
        var productArray = [];
        var a = 0;
        var user_id=$("#user_id").text();


        $.each(data,function(i,v){

            productArray.push(v.id) ;
            productArray.push(v.childNodes[0].innerText) ;
            productArray.push(v.childNodes[1].innerText);
            productArray.push(v.childNodes[2].innerText) ;
            postData.push(productArray);
            productArray = [];
            //品名为v.childNodes[0].innerText，缺货数量为v.childNodes[2].innerText，商品ID为v.id
        });
        if(confirm("确认提交么？")) {
            $('#shortage3').attr('class',"submit style_gray");
            $('#shortage3').attr('value',"正在提交...");
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                dataType: 'json',
                data: {
                    method: 'submitCheckSpareDetails',

                    data: {
                        add_user: user_id,
                        order_id: order_id,
                        product: postData,

                    }
                },
                success: function (response) {
                    if(response){
                        alert('提交的数量不能大于订单数量，否则将不予提交');
                        getSpareDetails();
                    }
                },
                complete : function(){
                    $('#shortage3').attr('class',"submit");
                    $('#shortage3').attr('value',"提交");
                }
            });
        }
    }


    function confirm_spareProduct(product_id,name,inventory_name){
        var unsorted =  $("#spareunsorted"+product_id).text();
        var user_id=$("#user_id").text();
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for (var i = 0; i < strs.length; i++) {
                theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
            }
        }
        var order_id = (theRequest['order_id']);
        var ordersorted = $('#spareneedsorted'+product_id).text();
        if(confirm("确认提交么？")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                dataType: 'json',
                data: {
                    method: 'confirm_product',
                    data: {
                        product_id: product_id,
                        product_name: name,
                        quantity: unsorted,
                        ordersorted: ordersorted,
                        add_user_id: user_id,
                        inventory_name: inventory_name,
                        order_id: order_id,

                    }
                },
                success: function (response) {
                    if (response.status == 1) {
                        alert('提交成功');
                        getSpareDetails();
                    } else {
                        alert('提交的总数量已经大于订单数量或者待确认量不能为零，提交失败');
                    }
                }
            });
        }
    }

    function  confirm_product(product_id ,name,inventory_name){
        var unsorted =  $("#unsorted"+product_id).text();
        var user_id=$("#user_id").text();
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for (var i = 0; i < strs.length; i++) {
                theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
            }
        }
        var order_id = (theRequest['order_id']);
        var ordersorted = $('#ordersorted'+product_id).text();
        if(confirm("确认提交么？")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                dataType: 'json',
                data: {
                    method: 'confirm_product',
                    data: {
                        product_id: product_id,
                        product_name: name,
                        quantity: unsorted,
                        ordersorted: ordersorted,
                        add_user_id: user_id,
                        inventory_name: inventory_name,
                        order_id: order_id,

                    }
                },
                success: function (response) {
                    if (response.status == 1) {
                        alert('提交成功');
                        location.reload();
                    } else {
                        alert('提交的总数量已经大于订单数量或者待确认量不能为零，提交失败');
                    }
                }
            });
        }
    }


    function cancel_spareProduct(product_id){
        var user_id=$("#user_id").text();
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for (var i = 0; i < strs.length; i++) {
                theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
            }
        }
        var order_id = (theRequest['order_id']);
        if(confirm("确认取消么？")) {

            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                dataType: 'json',
                data: {
                    method: 'cancel_product',
                    data: {
                        product_id: product_id,
                        order_id: order_id,

                    }
                },
                success: function (response) {
                    if (response.status == 1) {
                        alert('取消成功');
                        getSpareDetails();
                    } else {
                        alert('没有该数据不能取消');
                    }

                }
            });
        }
    }

    function cancel_product(product_id){
        var user_id=$("#user_id").text();
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for (var i = 0; i < strs.length; i++) {
                theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
            }
        }
        var order_id = (theRequest['order_id']);
        if(confirm("确认取消么？")) {

            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                dataType: 'json',
                data: {
                    method: 'cancel_product',
                    data: {
                        product_id: product_id,
                        order_id: order_id,

                    }
                },
                success: function (response) {
                    if (response.status == 1) {
                        alert('取消成功');
                        location.reload();
                    } else {
                        alert('没有该数据不能取消');
                    }

                }
            });
        }
    }





</script>




</html>
