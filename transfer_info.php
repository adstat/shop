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
    <style>
        html, body, div, object, pre, code, h1, h2, h3, h4, h5, h6, p, span, em,
        cite, del, a, img, ul, li, ol, dl, dt, dd, fieldset, legend, form,
        input, button, textarea, header, section, footer, article, nav, aside,
        menu, figure, figcaption {
            margin: 0;
            padding: 0;
            outline: none
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

        .style_green{
            background-color: #117700;
            border: 0.1em solid #006600;
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

        #manual_add{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }

        #TransferProduct{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }
    </style>
</head>

<header class="bar bar-nav">
    <div class="title"> <input type="button" class="invopt" style="background: red;width: 70px; font-size: 15px; " id="return_index"  value="返回" onclick="javascript:history.back(-1);"><span id="small_title" style="width: 70px; font-size: 20px;">移库操作</span><input class="invopt"  style="background: red;width: 70px; font-size: 15px;" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();"></div>

</header>
<hr>
<body>
<span  id = 'user_id' hidden="hidden"><?php echo $_COOKIE['inventory_user'];?></span>
<div id="inv_comment">
    <div align="right"><?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <span onclick="javascript:logout_inventory_user();">退出</span></div>
    <div  style="display: none" id="inventory_user_id"> <?php echo $_COOKIE['inventory_user_id'];?> </div>
    <div  style="display: none" id="warehouse_id"> <?php echo $_COOKIE['warehouse_id'];?> </div>

    <!--    <div align="center"><input class="invopt"  style=" width: 100px; background: red"  type="button" value="添加移库商品"  ></div>-->
    <?php  if(  $_COOKIE['user_group_id'] == 1)  { ; ?>
        <div align="center">
<!--            <select id="transfer_area"  style="width:200px ;height: 40px">-->
<!---->
<!--            </select>-->

            <form method="post"  onsubmit="getTransferProductInfo(); return false;">
                <input name="singleProduct" id="singleProduct" rows="1" maxlength="19" autocomplete="off" placeholder="点击扫描或输入" style="ime-mode:disabled; height: 40px; font-size: 2rem">
                <input class="invopt " type="submit" value="确认" style="font-size: 1em; padding: 0.2em ; background: red ;width: 50px; font-size: 10px;">
            </form>

        </div>
        <hr>

        <table border="1" style="width:100%;" cellpadding="2" cellspacing="3">
            <input style="display: none "  id = 'product_id_transfer' value="" >
            <tbody><tr>
                <th style="width:3.3rem">ID/商品名称</th>
                <td colspan="2" id="singleProductId"></td>
            </tr>
            <tr>
                <th>分拣位数量</th>
                <td colspan="2" id="stock_area_quantity"><input type="text" style="font-size: 2rem ;height: 40px;" placeholder="输入分拣位现有数量" id="input_stock_area_quantity" maxlength="20" value=""></td>
            </tr>
            <tr id="new_sku_barcode">
                <th>安全数量</th>
                <td colspan="2" id="safe_area_quantity"><input type="text" style="font-size: 2rem ;height: 40px;" placeholder="输入安全值" id="input_safe_area_quantity" maxlength="20" value=""></td>
            </tr>
            <tr>
                <th>货位最大容纳量</th>
                <td colspan="2" id="storage_capacity_quantity"><input type="text" style="font-size: 2rem ;height: 40px;" placeholder="输入货位能存放的最大数量" id="input_capacity_quantity" maxlength="20" value=""></td>
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

        <input class="submit" id="submitProductSku" type="button" style="width: 90px;height: 30px; font-size: 10px;" value="更新/添加" onclick="javascript:addChangeProductTransfer();">
        <input class="submit" id="submitProductSku" type="button" style="width: 90px;height: 30px; font-size: 10px;" value="启用/停用" onclick="javascript:ChangeProductTransferStatus();">
        <input class="submit" id="submitProductSku" type="button" style="width: 120px;height: 30px; font-size: 10px;" value="手动添加移库商品" onclick="javascript:manualAddTransfer();">
    <?php  } ?>





</div>



<hr>








<div style="height: 30px;">
    <br/>
</div>







<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="playerSubmit" src="view/sound/redalert.wav">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>

<script>




    var  user_group_id  =  parseInt('<?php echo $_COOKIE['user_group_id'];?>');
    var warehouse_id = parseInt($("#warehouse_id").text());
//    $(document).ready(function(){
//        var startrow = '';
//        var psize = '';
//       // getTransferMission(startrow ,psize);
//      // goPage(1,20);
//    });

    function soundEffectInit(){
        //音效设置
        var sound = {};
        sound.playerSubmit = $("#playerSubmit")[0];
        return sound;
    }


//    $(document).ready(function () {
//        $.ajax({
//            type: 'POST',
//            url: 'invapi.php?method=getWarehouseTransferInfo',
//            data: {
//                method: 'getWarehouseTransferInfo',
//            },
//            success: function (response, status, xhr) {
//                console.log(response);
//
//                if(response){
//                    var jsonData = eval(response);
//                    var html = '<option value=0>-请选择仓库区域-</option>';
//                    $.each(jsonData, function(index, value){
//                        html += '<option  value='+ value.warehouse_transfer_area_id +'  style = " '  '" >' + value.title + '</option>';
//                    });
//
//                    $('#transfer_area').html(html);
//                }
//            }
//        });
//    });



    function manualAddTransfer() {
        $("#productsInfo3").hide();
        $("#barcon").hide();
        $("#productsInfo4").show();
        $("#manualAdd1").show();

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



    function getTransferProductInfo(){
        var id = $('#singleProduct').val();
        if(id !== ''){
            if(id.length >= 4){
                var method = $('#method').val();
                $.ajax({
                    type : 'POST',
                    url : 'invapi.php',
                    data: {
                        method: 'getTransferProductInfo',
                        data: {
                            warehouse_id: warehouse_id,
                            sku_id: id,
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
                                $('#input_stock_area_quantity').val(jsonData.stock_area_quantity);

                                $('#input_safe_area_quantity').val(jsonData.safe_stock);
                                $('#input_capacity_quantity').val(jsonData.storage_capacity_quantity);

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
                    complete : function(){
                        $('#singleProduct').val('');
                        $("#singleProduct").blur();
                    }
                });
            }
            else{
                alert('错误的条码['+id+']');
                $('#singleProduct').val('');
                return false;
            }
        }

    }

    function addChangeProductTransfer(){
        var product_id_transfer = $("#product_id_transfer").val();
        var input_stock_area_quantity = $("#input_stock_area_quantity").val();
        var input_safe_area_quantity = $("#input_safe_area_quantity").val();
        var input_capacity_quantity = $("#input_capacity_quantity").val();
        var stock_area = $("#stock_area").val();
        var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';
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
                    warehouse_id :warehouse_id,

                },
            },
            success: function (response, status, xhr) {
                var jsonData = $.parseJSON(response);

                if(jsonData == 1){
                    alert('更新成功');
                }
            }

        });
    }

    function  ChangeProductTransferStatus(){
        var product_id_transfer = $("#product_id_transfer").val();
        var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'ChangeProductTransferStatus',
                data: {
                    product_id_transfer: product_id_transfer,
                    inventory_user_id:inventory_user_id,
                    warehouse_id :warehouse_id,

                },
            },
            success: function (response, status, xhr) {
                var jsonData = $.parseJSON(response);

                if(jsonData == 1){
                    alert('更新成功');
                }
            }

        });

    }


</script>



