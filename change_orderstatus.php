<?php
date_default_timezone_set('Asia/Shanghai');

//var_dump($_COOKIE);
/**
 * Created by PhpStorm.
 * User: jshy
 * Date: 2017/3/13
 * Time: 14:45
 */
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=r.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪仓库分拣缺货确认表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>

</head>

<header class="bar bar-nav">
    <div class="title" align="center">
        <input type="button" class="invopt" style="background: red;float: left" id="return_index"  value="返回" onclick="javascript:history.back(-1);"><span id="small_title">订单配送状态更改</span><input class="invopt" style="background: red ; float: right" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();">
    </div>


</header>
<hr>
<body>
<span  id = 'user_id' hidden="hidden"><?php echo $_COOKIE['inventory_user_id'];?></span>



<div id="div_order_search"  align="center" style="height: 40px;">
    物流配送单号:<input id="input_logistic_id" name="input_logistic_id" style="width:12.5em;height: 25px;border:1px solid" onchange="loadOrderBtDriver($(this).val())">
    <input id="input_logistic_allot_id"  hidden="" name="input_logistic_id" style="width:12.5em;height: 25px;border:1px solid">
</div>
<div id="div_order_search"  align="center" style="height: 40px;">
    订单号:<input id="input_order_id" name="input_order_id" style="width:12.5em;height: 25px;border:1px solid">
</div>


<hr>


<div align="center">

    <form id="form-return">
        <table  border='1'cellspacing="0" cellpadding="0" id="order_table" style="width:100%;">
            <thead>
            <tr style="background:#8fbb6c;">
                <td>选中</td>
                <td>货位号</td>
                <td>订单号</td>
                <td>件数</td>
                <td>司机</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody id="order_logistic_driver">
            </tbody>
        </table>
    </form>
</div>
<div align="center"><input  id="submitDeliverStatus"    style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="submitDeliverStatus();" value="选中订单全部出库"></div>
</body>

<script>
//    $("input[name='input_logistic_id']").keyup(function(){
//        var tmptxt=$(this).val();
//
//        $(this).val(tmptxt.replace(/\D/g,''));
//        if(tmptxt.length >= 4){
//            getOrderByDriver(tmptxt);
//        }
//
//    });

    function loadOrderBtDriver(val){
        getOrderByDriver(val);
    }

    $("input[name='input_order_id']").keyup(function(){

        var tmptxt=$(this).val();
        if(tmptxt.length >= 6){
            $("#"+tmptxt).attr("checked",true);
            var tmptxt=$(this).val('');
        }
    });

    function getOrderByDriver(tmptxt){
        var  warehouse_id = '<?php echo $_COOKIE['warehouse_id'];?>';
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            dataType: 'json',
            data : {
                method : 'getOrderByDriver',
                data: {
                    logistic_allot_id :tmptxt,
                    warehouse_id:warehouse_id,
                }
            },
            success : function (response){

                console.log(response);
                if(response){
                    var html= '';

                    if(response == 1){
                        alert('司机超过欠款金额不能出库');
                    }else {


                        $.each(response, function (i, v) {
                            html += "<tr style='background:#d0e9c6; height: 30px;' id='check" + v.order_id + "'>";
                            if (v.order_deliver_status_id == 11 || v.order_deliver_status_id == 1 && v.order_status_id != 8) {
                                html += "<td>";
                                html += '<input  type="checkbox" style="zoom:180%;" type="button" name="pich_id[]" class="pich_id"  id="' + v.order_id + '"  value="' + v.order_id + '" >';
                                html += "</td>";
                            } else {
                                html += "<td></td>";
                            }

                            html += "<td>" + v.inv_comment + "</td>";
                            html += "<td>" + v.order_id + '/' + v.name + '/' + v.order_name + "</td>";
                            html += "<td>" + v.quantity + "</td>";
                            html += "<td>" + v.logistic_driver_title + "</td>";
                            // if (v.order_deliver_status_id == 11 || v.order_deliver_status_id == 1 && v.order_status_id != 8) {
                                html += "<td>";
                                html += '<input style="background: red ;border-radius: 10px ;width: 100px" type="button" id="button_id' + v.order_id + '"  value="确认出库"  onclick="javascript:confirm_orderStatus(\'' + v.order_id + '\')">';
                                html += "</td>";
                            // } else {
                            //     html += "<td></td>";
                            //
                            // }

                            html += "</tr>";
                        });
                        $('#order_logistic_driver').html(html);
                    }
                }
            }

        });
    }

    function  confirm_orderStatus(order_id){
        var user_id=$("#user_id").text();
        var logistic_id =$("#input_logistic_id").val();

        // $("#button_id"+order_id).attr("disabled", true);

       if(logistic_id){
           if(confirm("确认出库确认?出库确认之后将不能更改")) {
               $.ajax({
                   type: 'POST',
                   url: 'invapi.php',
                   dataType: 'json',
                   data: {
                       method: 'confirm_orderStatus',
                       data: {
                           order_id: order_id,
                           user_id: user_id,
                           logistic_allot_id:logistic_id,
                       }
                   },
                   success: function (response) {
                       if (response == 1) {
                           getOrderByDriver(logistic_id);
                       } else {
                           alert('此订单不在该配送列表中或者已经更改配送状态');
                       }
                   }


               });
       }

        }else{
           alert('物流配送单号不能为空');
       }
    }

function submitDeliverStatus(){
    var checkbox =$("input[name='pich_id[]']:checked").val([]);
    var user_id=$("#user_id").text();
    var logistic_id =$("#input_logistic_id").val();

    var  check_value = [];
    for(var i=0;i<checkbox.length;i++){
        check_value.push(checkbox[i].value);
    }

    $("#submitDeliverStatus").attr("disabled", true);

    $.ajax({
        type: 'POST',
        url: 'invapi.php',
        dataType: 'json',
        data: {
            method: 'submitDeliverStatus',
            data: {
                check_value: check_value,
                user_id: user_id,
                logistic_id:logistic_id,
            }
        },
        success: function (response) {
            if(response ==3){
                alert('请选择所需要确认的订单');
            }
            if(response ==2){
                getOrderByDriver(logistic_id);
            }
        }


    });



}


</script>




