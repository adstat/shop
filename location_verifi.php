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

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪仓库分拣缺货确认表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>


    <link rel="stylesheet"  type="text/css"  href="view/javascript/jquery/datetimepicker/bootstrap.min.css">
    <script type="text/javascript" src="view/javascript/jquery/datetimepicker/bootstrap.min.js"></script>

    <script type="text/javascript" src="view/javascript/jquery/datetimepicker/moment.js"></script>
    <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>

    <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
    <link rel="stylesheet" type="text/css" href="view/css/i.css"/>

</head>

<header class="bar bar-nav">
    <div class="title"> <input type="button" class="invopt" style="background: red" id="return_index"  value="返回" onclick="javascript:history.back(-1);"><span id="small_title">货位核查</span><input class="invopt" style="background: red" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();"></div>

</header>
<hr>
<body>

<div id="inv_comment">
<div id="div_date_start"  align="center" style="height: 70px;font-size: 15px">开始时间:<input id="date_start" name="date_start"  autocomplete="off" class="date" type="text" value=""  style="font-size: 20px; width:15.5em ;height: 40px;border:1px solid" data-date-format="YYYY-MM-DD-HH" id="input-date-end" >
</div>
<div id="div_date_end"  align="center" style="height: 70px;font-size: 15px">结束时间:<input id="date_end" name="date_end"  autocomplete="off"  class="date" type="text" value="" style="font-size: 20px;width:15.5em ;height: 40px;border:1px solid" data-date-format="YYYY-MM-DD" id="input-date-end">

</div>

<div id="div_order_status"  align="center"  style="height: 40px;font-size: 15px" >
    订单状态:<select id="orderStatus" style="width:12.5em;height:2em;">
    </select>
</div>
<div id="div_order_check_status"  align="center"  style="height: 40px;font-size: 15px" >
    核查状态:<select id="checkStatus" style="width:12.5em;height:2em;">
        <option value="0"  selected="selected">待核查</option>
        <option value="1">已核查</option>
    </select>
</div>

<div id="div_order_search"  align="center" style="height: 40px;font-size: 15px">
    订单查询:<input id="input_order_id" name="input_order_id" style="width:12.5em;height: 25px;border:1px solid">
</div>
    <div>
        <input type="button"  style=" width:100px;font-size:1.2em; background: red; float: left" onclick="javascript:getOrderByStatus()" value="货位核查查询">
        <input type="button" id="spare_button" name="single_button" style="font-size:1.2em;width: 120px;background: red;float: right" type="button"  onclick="searchChecks();" value="分拣错误信息查询界面">
    </div>
<hr>
<div id="sumcheck">

</div>

<div align="center">
    <form id="form-return">
        <table  border='1'cellspacing="0" cellpadding="0" id="order_table" style="width:100%;">
            <thead>
            <tr style="background:#8fbb6c;">
                <td>货位号</td>
                <td>订单号</td>
                <td>已分拣</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody id="order_sorting_short">

            </tbody>

        </table>
    </form>
</div>
</div>

<div id="search_details" hidden="hidden">
    <div id="div_date_start"  align="center" style="height: 40px">开始时间:<input id="date_starts" name="date_start"  autocomplete="off" class="date" type="text" value=""  style="width:12.5em ;height: 25px;border:1px solid" data-date-format="YYYY-MM-DD-HH" id="input-date-end" >
    </div>
    <div id="div_date_end"  align="center" style="height: 40px">结束时间:<input id="date_ends" name="date_end"  autocomplete="off"  class="date" type="text" value="" style="width:12.5em ;height: 25px;border:1px solid" data-date-format="YYYY-MM-DD" id="input-date-end">
    </div>
    <input type="button"  style="float:left;width: 100px;height: 20px;background: red"onclick="location.reload();" value="货位核查更正界面">
    <button id="spare_button" name="single_button" style="float:right;width: 100px;height: 20px;background: red" type="button"     onclick="getSearchCheck();">查询</button>

    <hr>

    <form id="form-return4" >
        <table  border='1'cellspacing="0" cellpadding="0" id="order_table4">
            <thead>
            <tr style="background:#8fbb6c;">
                <td>订单ID</td>
                <td>商品ID</td>
                <td>商品名称</td>
                <td>分拣缺少数量</td>
                <td>分拣人</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody id="order_sorting_short4">

            </tbody>

        </table>
    </form>
</div>



</body>


<script>
    $(document).ready(function(){
        var today=new Date();
        var year=today.getFullYear();
        var month=today.getMonth()+1;
        var day=today.getDate();

        $('#date_start').val(year+"-"+month+"-"+day+" "+'07:00' );
    });
    $(document).ready(function(){
        var today=new Date();
        var year=today.getFullYear();
        var month=today.getMonth()+1;
        var day=today.getDate();
        var hour=today.getHours();
        $('#date_end').val(year+"-"+month+"-"+day+" "+hour+":00" );
    });


</script>

<script>
    $(document).ready(function(){
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getLocationOrderStatus'
            },
            success : function (response){

                if(response){
                    var jsonData = eval(response);
                    var html = '<option value=0>-请选择订单状态-</option>';
                    $.each(jsonData, function(index, value){
                        html += '<option value='+ value.order_status_id +' >' + value.name + '</option>';
                    });

                    $('#orderStatus').html(html);
                }

            }
        });
    });
</script>
<script>
    function getOrderByStatus (){
        var date_start = $("#date_start").val();
        var date_end = $("#date_end").val();
        var order_status_id = $("#orderStatus").val();
        var order_check_status = $("#checkStatus").val();
        var order_id = $("#input_order_id").val();

        var time_diff = new Date(date_end) - new Date(date_start);
        var day = parseInt(time_diff / (1000*24*60*60));
        if(day >=1){
            alert('只许查一天内的分拣数据');
        }else {
            getSumCheckOrder();
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                dataType: 'json',
                data: {
                    method: 'getOrderByStatus',
                    data: {
                        date_start: date_start,
                        date_end: date_end,
                        order_status_id: order_status_id,
                        order_check_status: order_check_status,
                        order_id: order_id
                    }
                },
                success: function (response, status, xhr) {

                    if (response) {
                        var html = '';
                        $.each(response, function (i, v) {
                            html += "<tr style='background:#d0e9c6;' id='check" + v.order_id + "'>";
                            html += "<td  id = 'order_id" + v.order_id + "' >" + v.inv_comment + "</td>";
                            html += "<td>" + v.order_id + '/' + v.name + " </td>";
                            html += "<td>" + v.quantity + '/' + v.inventory_name + "</td>";

                            if (v.check_status == 0) {
                                html += "<td>";

                                html += '待核查<input style="background: red ;border-radius: 10px ;width: 60px" type="button" id="button_id' + v.order_id + '"  value="查看"  onclick="javascript:search(\'' + v.order_id + '\')">';
                                html += "</td>";
                            }

                            if (v.check_status == 1) {
                                html += "<td>";
                                html += "<span>" + v.reasons + "</span>";
                                html += '已核查<input style="background: red ;border-radius: 10px ;width: 60px" type="button" id="button_id' + v.order_id + '"  value="查看"  onclick="javascript:search(\'' + v.order_id + '\')">';
                                html += "</td>";
                            }


                            html += "</tr>";
                        });
                        $('#order_sorting_short').html(html);
                    }
                }
            });
        }

    }

    function getSumCheckOrder(){
        var date_start = $("#date_start").val();
        var date_end = $("#date_end").val();
        var order_status_id = $("#orderStatus").val();
        var order_check_status = $("#checkStatus").val();
        var order_id = $("#input_order_id").val();
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            dataType: 'json',
            data: {
                method: 'getSumCheckOrder',
                data: {
                    date_start: date_start,
                    date_end: date_end,
                    order_status_id: order_status_id,
                    order_check_status: order_check_status,
                    order_id: order_id
                }
            },
            success : function (response , status , xhr){
                if(response){
                    var html = '';
                    html += "<span style='font-size: 15px'> 未核查"+ response[0].sum +"</span>";
                    html += "<span style='font-size: 15px'> 已核查"+ response[1].sum1 +"</span>";
                    $('#sumcheck').html(html);

                }
            }

        });
    }
    function search(order_id){
        location.href="check_verifi.php?order_id="+order_id;
    }


    function searchChecks(){
        $("#inv_comment").hide();
        $("#search_details").show();
        var today=new Date();
        var year=today.getFullYear();
        var month=today.getMonth()+1;
        var day=today.getDate();
        var days=today.getDate()+1;
        $('#date_starts').val(year+"-"+month+"-"+day );
        $('#date_ends').val(year+"-"+month+"-"+days );

    }
    function getSearchCheck(){

        var date_start = $("#date_starts").val();
        var date_end = $("#date_ends").val();

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            dataType: 'json',
            data: {
                method: 'getSearchCheck',
                data: {
                    date_start: date_start,
                    date_end: date_end,
                }
            },
            success: function (response) {
                console.log(response);
                var html='';
                $.each(response,function(i,v){
                    html +="<tr>";
                    html +="<td>"+ v.order_id + "</td>";
                    html +="<td>"+ v.product_id + "</td>";
                    html +="<td>"+ v.product_name + "</td>";
                    html +="<td>"+ v.quantity + "</td>";
                    html +="<td>"+ v.username + "</td>";
                    html +="<td>";
                    html += '<input style="background: red ;border-radius: 10px ;width: 40px" type="button" id="button_id'+ v.product_id +'"  value="取消"  onclick="javascript:cancel_searchProduct('+ v.product_id +','+ v.order_id+')">'
                    html +='</td>';
                    html +="</tr>";
                });

                $('#order_sorting_short4').html(html);
            }
        });
    }


</script>

