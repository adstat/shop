<?php
//require_once 'init.php';
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=r.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
?>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>出库前操作</title>

<header class="bar bar-nav">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>

    <link rel="stylesheet" type="text/css" href="view/css/i.css"/>
    <!-- <div class="title"> <button class="invopt" id="return_index"  style="background: red" onclick="javascript:location.reload();">刷新</button> <span id="small_title">出库确认</span><button class="invopt" style="background: red" id="return_index1" onclick="logout_inventory_user();">退出</button></div>  -->
    <div class="title"> <input type="button" class="invopt" style="background: red" id="return_index"  value="返回" onclick="javascript:history.back(-1);"><span id="small_title">出库确认</span><input class="invopt" style="background: red" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();"></div>
</header>
<hr>
<body>
<div id = 'find_order'>
    <div>快消品普通仓库</div>
    <div style="height: 30px">
        <span >登陆用户：<?php echo $_COOKIE['inventory_user'];?></span>
        <span >当前时间:<?php echo date('Y-m-d H:i', time());?></span>
    </div>
    <hr>

    <div style="height: 60px" align="center">
        <input id="input_order_id" name="input_order_id" type="text" value="" style="height: 25px;border:1px solid" placeholder="订单号"><button onclick="find_order();"  style="font-size: 1em; padding: 0.2em ;background: red">查找</button>
    </div>
    <div>
        <table id = 'dist_table' style="border:1px solid">
            <thead style="background:#8fbb6c;">
            <th width="150px">订单号</th>
            <th width="150px">货位号</th>
            <th width="150px">件数</th>
            <th width="150px">操作</th>
            </thead>
            <tbody >

            </tbody>
        </table>
    </div>
</div>

<div id="short_regist">
    <div>

    </div>

</div>
</body>


</html>
<script>
    $(document).ready(function(){
        $('#short_regist').hide();
        window.onload=document.getElementById('input_order_id').focus();
    });

    function find_order(){

        var order_id = $("#input_order_id").val();
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            dataType: 'json',
            data: {
                method: 'find_order',
                data:{
                    order_id : order_id,

                }
            },
            success: function(data){
                var html = '';
                var  data = data.user;
                html += '<tr style="background:#d0e9c6;">';
                html += '<td align="center" >' + data.order_id + '</td>';
                html += '<td align="center" >' + data.inv_comment + '</td>';
                html += '<td align="center" >' + data.quantity +'</td>';
                html +="<td><button  style='font-size: 1em; padding: 0.2em ;background: red' class='button' onclick='short_regis(" + data.order_id + ")'>缺货登记</button></td>";
                html += '</tr>'

                $('#dist_table tbody ').html(html);

            },
        });
    }
    function short_regis(order_id){
        location.href = "short_confirm.php?order_id="+order_id;
    }




</script>