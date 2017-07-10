<?php
/**
 * Created by PhpStorm.
 * User: jshy
 * Date: 2017/3/13
 * Time: 14:45
 */
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
        <title>鲜世纪仓库调度</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
        <link rel="stylesheet" type="text/css" href="view/css/i.css"/>
    </head>
    <header class="bar bar-nav">
        <div class="title"> <input type="button" class="invopt" style="background: red" id="return_index"  value="返回" onclick="javascript:history.back(-1);"><span id="small_title">仓库调度</span><input class="invopt" style="background: red" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();">
        </div>
    </header>
    <hr>
   <style>
        .qtycolor {
           background-color:  #0bb20c;
           color: #ffffff;
            border: 0.1em solid  #0bb20c;
       }
   </style>
    <body>
        <div>
            <span>所在仓库：<span id="warehouse"><?php echo $_COOKIE['warehouse_title'];?></span></span>
            <span hidden="hidden">所在仓库：<span id="warehouse_id" hidden="hidden"><?php echo $_COOKIE['warehouse_id'];?></span></span>
            <span>登陆用户：<span id="warehouse_user"><?php echo $_COOKIE['inventory_user'];?></span></span>
        </div>
        <div id="Initial_Page">
            <div>
                <div id="div_date_start"  align="center" style="height: 70px;font-size: 15px">开始时间:<input id="date_start" name="date_start"  autocomplete="off" class="date" type="text" value=""  style="font-size: 20px; width:15.5em ;height: 40px;border:1px solid" data-date-format="YYYY-MM-DD-HH" id="input-date-end" >
                </div>
                <div id="div_date_end"  align="center" style="height: 70px;font-size: 15px">结束时间:<input id="date_end" name="date_end"  autocomplete="off"  class="date" type="text" value="" style="font-size: 20px;width:15.5em ;height: 40px;border:1px solid" data-date-format="YYYY-MM-DD" id="input-date-end">
                </div>
                <div align="center" style=" font-size: 15px">出库类型:
                    <select name="out_type" id="input-out_type" class="form-control"  style="font-size: 20px;width:15.5em ;height: 40px;border:1px solid">
                        <option value="">选择出库类型</option>
                        <option value="1">出库单</option>
                        <option value="2">仓间调拨单</option>
                    </select>
                    <div  style="margin-left:200px ;margin-top: 20px;">
                        <input type="button" value="开始查询" style="width: 90px; height: 20px; background: red" onclick="searchRequisition();">
                    </div>
                </div>
            </div>
            <hr>
            <div>
                <form id="form_return" >
                    <table  border='1'cellspacing="0" cellpadding="0" id="warehouse_change">
                        <thead>
                        <tr style="background:#8fbb6c;">
                            <td>出库单号</td>
                            <td>出库类型</td>
                            <td>调往仓库</td>
                            <td>添加时间</td>
                            <td>添加人</td>
                            <td>出库单状态</td>
                            <td>备注</td>
                            <td>操作</td>
                        </tr>
                        </thead>
                        <tbody id="warehouse_product_relevant">

                        </tbody>

                    </table>

                </form>
            </div>
        </div>
        <div id="View_Page" style="display: none">
            <form id="form_return2" >
                <table  border='1'cellspacing="0" cellpadding="0" id="warehouse_change2">
                    <thead>
                    <tr style="background:#8fbb6c;">
                        <td>商品ID</td>
                        <td>商品名称</td>
                        <td>货位号</td>
                        <td>仓库数量</td>
                        <td>待投篮数量</td>
                    </tr>
                    </thead>
                    <tbody id="warehouse_product_relevant2">

                    </tbody>

                </table>

            </form>
        </div>
        <div id="Shipment_Page"  style="display:none;">
            <div>
                <div>出库单号:<span id="return_relevant_id"></span></div>
                <form  id="form_return_index">
                    <table  border='1'cellspacing="0" cellpadding="0" id="table_return_index">
                        <thead>
                        <tr style="background:#8fbb6c;">
                            <td>商品名称</td>
                            <td>货位号</td>
                            <td>调拨数量</td>
                            <td>待投篮数量</td>
                            <td>操作</td>
                        </tr>
                        </thead>
                        <tbody id="tbody_return_index">

                        </tbody>

                    </table>
                </form>
            </div>
            <div id="div_bar_code" align="center" style="height: 60px;"><input id="bar_code" name="bar_code"  autocomplete="off" type="text" value="" style="height: 25px;margin-top:10px;border:1px solid" placeholder="商品条码">
            </div>
            <div><hr></div>
            <div>
                <form id="form_return3" >
                    <table  border='1'cellspacing="0" cellpadding="0" id="warehouse_change3">
                        <thead>
                        <tr style="background:#8fbb6c;">
                            <td>商品名称</td>
                            <td>货位号</td>
                            <td>调拨数量</td>
                            <td>待投篮数量</td>
                            <td>操作</td>
                        </tr>
                        </thead>
                        <tbody id="warehouse_product_relevant3">

                        </tbody>

                    </table>

                </form>
                <input class="qtyopt"  type="button"  style="float: right" value="提交" onclick="javascript:submitProducts();" >
            </div>
        </div>
        <div style="display: none">
            <div comment="Used for alert but hide">
                <audio id="playerSubmit" src="view/sound/ding.mp3">
                    Your browser does not support the <code>audio</code> element.
                </audio>
            </div>
        </div>
    </body>
</html>

<script>
    $(document).ready(function(){
        var today=new Date();
        var year=today.getFullYear();
        var month=today.getMonth()+1;
        var day=today.getDate();

        $('#date_start').val(year+"-"+month+"-"+day );
    });
    $(document).ready(function(){
        var today=new Date();
        var year=today.getFullYear();
        var month=today.getMonth()+1;
        var day=today.getDate()+1;
        var hour=today.getHours();
        $('#date_end').val(year+"-"+month+"-"+day );
    });

    $(document).ready(function(){
        var cookie_warehouse_id =$('#warehouse_id').text();

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getWarehouseRequisition',
                data:{
                    warehouse_id :cookie_warehouse_id,
                }
            },
            success : function (response){
                var html = "";
                var jsonData = $.parseJSON(response);
                if(response){
                    $.each(jsonData,function(i,v){
                        html += "<tr id='"+ v.relevant_id+"'>";
                        html += "<td>"+ v.relevant_id+ "</td>";
                        html += "<td>"+ v.out_type+ "</td>";
                        html += "<td>"+ v.title+ "</td>";
                        html += "<td>"+ v.date_added+ "</td>";
                        html += "<td>"+ v.username+ "</td>";
                        html += "<td>"+ v.name+ "</td>";
                        html += "<td>"+ v.comment+ "</td>";

                        if(v.from_warehouse == cookie_warehouse_id){

                            if(v.relevant_status_id !=2){
                                html += "<td>";
                                html += '<input type="button" value="查看" style="background:red ;margin: 10px 0 10px 0;"  onclick="viewItem('+v.relevant_id +')">';
                                html += "</td>";
                            }else if(v.relevant_status_id == 2){
                                html += "<td>";
                                html += '<input type="button" value="查看" style="background:red ;margin: 10px 0 10px 0;"  onclick="viewItem('+v.relevant_id +')">';
                                html += '<hr>';
                                html += '<input type="button" value="开始调库" style="background:red ;margin: 10px 0 10px 0;"  onclick="startShipment('+v.relevant_id +')">';
                                html += "</td>";
                            }
                        }else{

                            if(v.relevant_status_id !=5){
                                html += "<td>";
                                html += '<input type="button" value="查看" style="background:red ;margin: 10px 0 10px 0;"  onclick="viewItem('+v.relevant_id +')">';
                                html += "</td>";
                            }else if(v.relevant_status_id == 5){
                                html += "<td>";
                                html += '<input type="button"   value="查看" style="background:red ;margin: 10px 0 10px 0;"  onclick="viewItem('+v.relevant_id +')">';
                                html += '<hr>';
                                html += '<input type="button" value="开始入库" style="background:red ;margin: 10px 0 10px 0;"  onclick="startShipment('+v.relevant_id +')">';
                                html += "</td>";
                            }
                        }

                        html += "</tr>";
                    });

                    $("#warehouse_product_relevant").html(html);
                }

            }
        });
    });

</script>

<script>
    function soundEffectInit(){
        //音效设置
        var sound = {};
        sound.playerSubmit = $("#playerSubmit")[0];
        return sound;
    }

    function searchRequisition(){
        var filter_out_type = $('select[name=\'out_type\']').find("option:selected").text();
        var filter_out_type_id = $('select[name=\'out_type\']').val();
        var cookie_warehouse_id =$('#warehouse_id').text();

        var date_start = $("#date_start").val();
        var date_end = $("#date_end").val();
        $.ajax({
            type: 'POST',
            url : 'invapi.php',
            data:{
                method : 'searchRequisition',
                data: {
                    date_start: date_start,
                    date_end: date_end,
                    filter_out_type:filter_out_type,
                    filter_out_type_id:filter_out_type_id,
                }
            },
            success: function (response) {
                var html = "";
                var jsonData = $.parseJSON(response);
                if(response){
                    $.each(jsonData,function(i,v){
                        html += "<tr id='"+ v.relevant_id+"'>";
                        html += "<td>"+ v.relevant_id+ "</td>";
                        html += "<td>"+ v.out_type+ "</td>";
                        html += "<td>"+ v.title+ "</td>";
                        html += "<td>"+ v.date_added+ "</td>";
                        html += "<td>"+ v.username+ "</td>";
                        html += "<td>"+ v.name+ "</td>";
                        html += "<td>"+ v.comment+ "</td>";

                        if(v.from_warehouse == cookie_warehouse_id){

                            if(v.relevant_status_id !=2){
                                html += "<td>";
                                html += '<input type="button" value="查看" style="background:red ;margin: 10px 0 10px 0;"  onclick="viewItem('+v.relevant_id +')">';
                                html += "</td>";
                            }else if(v.relevant_status_id == 2){
                                html += "<td>";
                                html += '<input type="button" value="查看" style="background:red ;margin: 10px 0 10px 0;"  onclick="viewItem('+v.relevant_id +')">';
                                html += '<hr>';
                                html += '<input type="button" value="开始调库" style="background:red ;margin: 10px 0 10px 0;"  onclick="startShipment('+v.relevant_id +')">';
                                html += "</td>";
                            }
                        }else{

                            if(v.relevant_status_id !=5){
                                html += "<td>";
                                html += '<input type="button" value="查看" style="background:red ;margin: 10px 0 10px 0;"  onclick="viewItem('+v.relevant_id +')">';
                                html += "</td>";
                            }else if(v.relevant_status_id == 5){
                                html += "<td>";
                                html += '<input type="button" value="查看" style="background:red ;margin: 10px 0 10px 0;"  onclick="viewItem('+v.relevant_id +')">';
                                html += '<hr>';
                                html += '<input type="button" value="开始入库" style="background:red ;margin: 10px 0 10px 0;"  onclick="startShipment('+v.relevant_id +')">';
                                html += "</td>";
                            }
                        }

                        html += "</tr>";
                    });

                    $("#warehouse_product_relevant").html(html);
                }
            }


        });
    }


    function  viewItem(relevant_id){
        $("#Initial_Page").hide();
        $("#View_Page").show();
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'viewItem',
                data: {
                    relevant_id: relevant_id,
                }
            },
            success:function (response){
                var html = "";
                var jsonData = $.parseJSON(response);
                if(response){
                    $.each(jsonData,function(i,v) {
                        html += "<tr id='view_" + v.product_id + "'>";
                        html += "<td>" + v.product_id + "</td>";
                        html += "<td>" + v.name + "</td>";
                        html += "<td>" + v.product_section_title + "</td>";
                        html += "<td>" + v.inventory + "</td>";
                        html += "<td>" + v.num + "</td>";
                        html += "</tr>";
                    });
                    $("#warehouse_product_relevant2").html(html);
                }
            }
        });

    }

    function startShipment(relevant_id){
        $("#Initial_Page").hide();
        $("#Shipment_Page").show();
        $("#return_relevant_id").text(relevant_id);
        var warehouse_id = $("#warehouse_id").text();
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'startShipment',
                data: {
                    relevant_id: relevant_id,
                    warehouse_id:warehouse_id,
                }
            },
            success:function(response){
                var html = "";
                var jsonData = $.parseJSON(response);
                     if(jsonData.product2){
                         $.each(jsonData.product2,function(i,v) {
                             html += "<tr id='shipment_" + v.product_id + "' style='background-color: #d0e9c6 '>";
                             html += "<td>" + v.product_id +"/"+v.name+"</td>";
                             html += "<td>" + v.product_section_title + "</td>";
                             html += "<td id='num3_"+v.product_id +"'>" + v.num + "</td>";
                             html += "<td id='num4_"+v.product_id +"'>" + v.num + "</td>";
                             html += "<td>"
                             html +='<input class="qtyopt qtycolor"  type="button" id="button_id'+ v.product_id +'"  value="提交" onclick="javascript:submitProduct(\''+ v.product_id +'\');" >'
                             html += "</td>";
                             html += "</tr>";
                         });
                     }
                    if(jsonData.product1){
                        $.each(jsonData.product1,function(i,v) {
                            html += "<tr id='shipment_" + v.product_id + "' style='background-color: yellow '>";
                            html += "<td>" + v.product_id + "/" + v.name + "</td>";
                            html += "<td>" + v.product_section_title + "</td>";
                            html += "<td id='num3_" + v.product_id + "'>" + v.num + "</td>";
                            html += "<td id='num4_" + v.product_id + "'>" + v.quantity + "</td>";
                            html += "<td>"
                            html += '<input class="qtyopt qtycolor"  type="button" id="button_id' + v.product_id + '"  value="提交" onclick="javascript:submitProduct(\'' + v.product_id + '\');" >'
                            html += "</td>";
                            html += "</tr>";
                        });
                    }

                $("#warehouse_product_relevant3").html(html);
            }

        });
    }

    $("input[name='bar_code']").keyup(function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
        if(tmptxt.length >= 4){
            handleProductList2();
        }

    }).bind("paste",function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
    });

    function handleProductList2(){
        var product_id = $("#bar_code").val();
        var relevant_id = $("#return_relevant_id").text();

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getRelevantProductID',
                data: {
                    sku: product_id,
                    relevant_id :relevant_id,
                }
            },
            success:function(response){
                var jsonData = $.parseJSON(response);
                var status = jsonData.status;
                if(status == 1){
                    alert('未找到对应商品，请输入正确的商品ID或条形码');
                    $('#bar_code').val('');
                }
                if(status == 2) {
                    var html = "";
                    var jsonData = $.parseJSON(response);
                    var id = jsonData.product[0].product_id;
                    var str = "#num2_"+id ;
                    var length = $(str).length > 0;

                    if($(str).length > 0){
                        $('#bar_code').val('');
                        qtyminus3(id);
                    }else{
                        if(response){
                            $.each(jsonData.product,function(i,v) {
                               var shoot =  parseInt(v.num) - 1;
                                html += "<tr id='relevant_" + v.product_id + "'>";
                                html += "<td>" + v.product_id +"/"+v.name+"</td>";
                                html += "<td>" + v.product_section_title + "</td>";
                                html += "<td id='num1_"+v.product_id +"'>" + v.num + "</td>";
                                html += "<td id='num2_"+v.product_id +"'>" + shoot  + "</td>";
                                html += "<td>";
                                html +='<input class="qtyopt " type="button" id="button_id'+ product_id +'"  value="+" onclick="javascript:qtyadd3(\''+ product_id +'\');" >'
                                html +='<input class="qtyopt "  id="button2_id'+ product_id +'" type="button" value="-" onclick="javascript:qtyminus3(\''+ product_id +'\');" >'
                                html +="</td>";
                                html += "</tr>";
                            });
                            $("#tbody_return_index").html(html);
                            var num4 =  $("#num4_"+id).text();
                            $("#num2_"+id).text(parseInt(num4)-1);
                            $("#num4_"+id).text(parseInt(num4)-1);
                            $('#bar_code').val('');
                        }
                    }


                }
            }
        });

    }

    function qtyminus3(id){
       var num4 =  $("#num4_"+id).text();
        if(parseInt(num4) == 0){
            var sound = soundEffectInit();
            sound.playerSubmit.play();
            $("#shipment_"+id).css("background-color", "#ADADAD");

        }else{
            $("#num2_"+id).text(parseInt(num4)-1);
            $("#num4_"+id).text(parseInt(num4)-1);
        }
    }

    function qtyadd3(id){
        var num2 =  $("#num2_"+id).text();
        var num1 =  $("#num1_"+id).text();
        if(parseInt(num2) >= 0){
            $("#shipment_"+id).css("background-color", "#d0e9c6");
        }
        if(parseInt(num1)<=parseInt(num2)){
            alert('不能超过调拨数量');
        }else{
            var num= parseInt(num2)+1;
            $("#num2_"+id).text(num);
            $("#num4_"+id).text(num);
        }
    }

    function submitProduct(product_id){
        var relevant_id = $("#return_relevant_id").text();
        var warehouse_id = $("#warehouse_id").text();
        var text = "";
        var postData = [] ;
        $("#shipment_"+product_id).find("td").each(function () {
            text =  $(this).text()
            postData.push(text);
        });

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'submitProduct',
                data:{
                    relevant_id: relevant_id,
                    warehouse_id: warehouse_id,
                    postData: postData,
                }

            },
            success:function(response){
                if(response){
                    startShipment(relevant_id);
                    alert('提交成功');
                }
            }
        });

    }

    function submitProducts(){
        var warehouse_user = $("#warehouse_user").text();
        var relevant_id = $("#return_relevant_id").text();
        var warehouse_id = $("#warehouse_id").text();
        var data = $("#warehouse_product_relevant3").find("tr");
        var postData = [] ;
        var productArray = [];
        $.each(data,function(i,v){
            productArray.push(v.id) ;
            productArray.push(v.childNodes[0].innerText) ;
            productArray.push(v.childNodes[1].innerText);
            productArray.push(v.childNodes[2].innerText) ;
            productArray.push(v.childNodes[3].innerText) ;
            postData.push(productArray);
            productArray = [];
            //品名为v.childNodes[0].innerText，缺货数量为v.childNodes[2].innerText，商品ID为v.id
        });
        if(confirm("确认提交么？")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'submitProducts',
                    data: {
                        warehouse_id:warehouse_id,
                        relevant_id:relevant_id,
                        warehouse_user:warehouse_user,
                        postData: postData,

                    }

                },
                success: function (response) {
                    if (response) {

                        alert('提交成功');
                    }
                }
            });
        }
    }

</script>
