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
        #stock_section_type{
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
        #productmanaual{
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








    </div>


    <table id="productsHold1" border="1" style="width:100%; border:1px solid ; ">


        <tbody id="productsInfo1" >
        <!-- Scanned Product List -->
        </tbody>
    </table>

<hr>


    <table id="productsHold2" border="1" style="width:100%; display: none " cellpadding=2 cellspacing=3>
        <tr style="background:#8fbb6c;">
            <th >存货区区域</th>
            <th >存货区数量拣量</th>
        </tr>
        <tbody id="productsInfo2">
        <!-- Scanned Product List -->
        </tbody>
    </table>
<div>
    <input class="submit" id="submitProductSku" type="button" style="width: 120px;height: 30px; font-size: 10px;" value="返回录入信息界面" onclick="javascript:location='stock_checks.php?auth=xsj2015inv'"">
<input class="submit" id="submitProductSku" type="button" style="width: 120px;height: 30px; font-size: 10px;" value="手动添加移库商品" onclick="javascript:manualAddTransfer();">
</div>
<hr style="height: 5px;">
<div align="center" id = "stock_section_type_div" style="display: none;">
    请选择移库类型：
<select id="stock_section_type" style=" width:200px ;height: 40px">
    <option value="0">请选择移库类型</option>
    <option value="1">从某一个货位移动另一个货位</option>
    <option value="2">商品无货位需要移动到某一货位</option>
</select>
</div>
<div id="barcodescanner2" align="center" style="display: none;" >
    <form method="post" onsubmit="handleProductList2(); return false;">
        <input name="product2" id="productmanaual" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; font-size: 2rem; height:  50px"/>

        <input class="addprod style_green" type="submit" value ="添加" style="font-size: 2em; padding: 0.2em">
    </form>
</div>
<table border="1" style="width:100%; display: none " cellpadding="2" cellspacing="3" id = 'fenjianform'>
    <input style="display: none "  id = 'product_id_transfer' value="" >
    <tbody><tr>
        <th style="width:3.3rem">ID/商品名称</th>
        <td colspan="2" id="singleProductId"></td>
    </tr>
    <tr>
        <th>分拣位数量</th>
        <td colspan="2" id="stock_area_quantity"><input type="text" style="font-size: 2rem ;height: 20px;" placeholder="输入分拣位现有数量" id="input_stock_area_quantity" maxlength="10" value=""></td>
    </tr>
    <tr id="new_sku_barcode">
        <th>安全数量</th>
        <td colspan="2" id="safe_area_quantity"><input type="text" style="font-size: 2rem ;height: 20px;" placeholder="输入安全值" id="input_safe_area_quantity" maxlength="10" value=""></td>
    </tr>
    <tr>
        <th>货位最大容纳量</th>
        <td colspan="2" id="storage_capacity_quantity"><input type="text" style="font-size: 2rem ;height: 20px;" placeholder="输入货位能存放的最大数量" id="input_capacity_quantity" maxlength="20" value=""></td>
    </tr>
    <tr>
        <th>货位号</th>
        <td id="stock_area"></td>
    </tr>
    <tr>
        <th>是否启用</th>
        <td id="status_id"></td>
    </tr>
    <tr id="stock_move_out_tr">
        <th>移出货位</th>
        <td id="stock_move_out"><input type="text" style="font-size: 2rem ;height: 30px;" placeholder="移出的货位号" id="stock_move_out_area" maxlength="20" value=""> </td>
    </tr>
    <tr id="stock_move_out_num_tr">
        <th>移出数量</th>
        <td id="stock_move_out_num"><input type="text" style="font-size: 2rem ;height: 30px;" placeholder="移出数量" id="stock_move_out_area_num" maxlength="20" value=""></td>
    </tr>
    <tr id="stock_move_out_button_tr">
        <th>确认移出</th>
        <td  style="height: 50px;"><input type="button" style="font-size: 2rem ;height: 30px; background: red" value="确认移出" id="stock_move_out_button" maxlength="20" onclick="javascript:confirmOut() ;" ></td>
    </tr>
    <tr id = "stock_move_in_tr" style="display:none;">
        <th>移入货位</th>
        <td ><input type="text" style="font-size: 2rem ;height: 50px;" placeholder="移入的货位号" id="stock_move_in_area" maxlength="20" value=""> </td>
    </tr>
    <tr  id = "stock_move_in_num_tr" style="display:none;">
        <th>移入数量</th>
        <td id="stock_move_in_num"><input type="text" style="font-size: 2rem ;height: 30px;" placeholder="移入数量" id="stock_move_in_area_num" maxlength="20" value=""></td>
    </tr>
    <tr id = "stock_move_in_button_tr" style="display:none;">
        <th>确认移出</th>
        <td  style="height: 50px;"><input type="button" style="font-size: 2rem ;height: 30px; background: red" value="确认移入" id="stock_move_in_button" maxlength="20" onclick="javascript:confirmIn() ;" ></td>
    </tr>

    </tbody>
</table>


<table width="100%" align="right">
    <tr><td><div id="barcon" name="barcon"></div></td></tr>
</table>

<div style="height: 30px;">
    <br/>
</div>
    <?php  if($_COOKIE['user_group_id'] == 26 || $_COOKIE['user_group_id'] == 23 || $_COOKIE['user_group_id'] == 1 ||  $_COOKIE['user_group_id'] == 24)  { ; ?>
    <table id="productsInfo3" border="1" style="width:100%; border:1px solid ; "  cellpadding="2" cellspacing="3">


    </table>

    <?php  } ?>







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
        $(document).ready(function(){
            var startrow = '';
            var psize = '';
            getTransferMission(startrow ,psize);
            goPage(1,20);
        });

        function soundEffectInit(){
            //音效设置
            var sound = {};
            sound.playerSubmit = $("#playerSubmit")[0];
            return sound;
        }



        function  addTranserMission(product_id){
            var warehouse_id = parseInt($("#warehouse_id").text());
            var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';

            var transfer_quantity = $("#transfer"+product_id).val();
            if(transfer_quantity == 0){
                alert('请填写数量') ;
                return false;
            }
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'addTranserMission',
                    data: {
                        warehouse_id: warehouse_id,
                        product_id : product_id ,
                        add_user : inventory_user_id ,
                        transfer_quantity :transfer_quantity,
                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);
                    if(jsonData == 1 ){
                        alert('开始移库');
                    }
                }
            });

        }

        
        function manualAddTransfer() {
            $("#productsInfo3").hide();
            $("#barcon").hide();
            $("#productsInfo4").show();
            $("#manualAdd1").show();
            $("#stock_section_type_div").show();
            $("#submitProductSku").hide();
            $("#barcodescanner2").show();

        }
        
        
        
        function  addTranserMission1(product_id){
            var warehouse_id = parseInt($("#warehouse_id").text());
            var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';

            var transfer_quantity = $("#transfer"+product_id).val();

            if(transfer_quantity == 0){
                alert('请填写数量') ;
                return false;
            }

            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'addTranserMission1',
                    data: {
                        warehouse_id: warehouse_id,
                        product_id : product_id ,
                        add_user : inventory_user_id ,
                        transfer_quantity :transfer_quantity,
                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);
                    if(jsonData == 1 ){
                        alert('移库结束');
                        var startrow = '';
                        var psize = ''
                        getTransferMission(startrow ,psize);
                    }
                }
            });

        }



        function goPage(pno,psize) {

            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'getTransferMissionNUM',
                    data: {
                        warehouse_id: warehouse_id,
                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);
                    var num  = jsonData['num'];
                    var totalPage = 0;//总页数
                    var pageSize = psize;//每页显示行数
                    //总共分几页
                    if(num/pageSize > parseInt(num/pageSize)){
                        totalPage=parseInt(num/pageSize)+1;
                    }else{
                        totalPage=parseInt(num/pageSize);
                    }

                    var currentPage = pno;//当前页数
                    var startRow = (currentPage - 1) * pageSize+1;//开始显示的行  31
                    var endRow = currentPage * pageSize;//结束显示的行   40
                    endRow = (endRow > num)? num : endRow;
                    console.log(startRow);
                    console.log(endRow);

                    //遍历显示数据实现分页

                    var pageEnd = document.getElementById("pageEnd");
                    var tempStr = "共"+num+"条记录 分"+totalPage+"页 当前第"+currentPage+"页";
                    if(currentPage>1){
//                        tempStr += "<a href=\"#\" onClick=\"goPage("+(1)+","+psize+")\">首页</a>";
                        tempStr += "<a href=\"#\" onClick=\"goPage("+(currentPage-1)+","+psize+") ; getTransferMission("+(startRow-20)+","+psize+")\"><上一页</a>"
                    }else{
//                        tempStr += "首页";
                        tempStr += "<上一页";
                    }

                    if(currentPage<totalPage){
                        tempStr += "<a href=\"#\" onClick=\"goPage("+(currentPage+1)+","+psize+");getTransferMission("+(startRow+20)+","+psize+")\">下一页></a>";
//                        tempStr += "<a href=\"#\" onClick=\"goPage("+(totalPage)+","+psize+")\">尾页</a>";
                    }else{
                        tempStr += "下一页>";
//                        tempStr += "尾页";
                    }

                    document.getElementById("barcon").innerHTML = tempStr;

                }

            });
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



        if (user_group_id == 26 || user_group_id == 1  || user_group_id == 23  || user_group_id == 24) {
            function getTransferMission(startrow ,psize){
                var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';

                $.ajax({
                    type: 'POST',
                    url: 'invapi.php',
                    data: {
                        method: 'getTransferMission',
                        data: {
                            warehouse_id: warehouse_id,
                            transfer_id:inventory_user_id,
                            start_row:startrow ,
                            psize:psize,
                        },
                    },
                    success:function(response){
                        var jsonData = $.parseJSON(response);
                        var html = "";
                        $.each(jsonData, function(index,v) {
                                    html += "<tbody>"
                                    html += "<tr>";
                                    html += "<th style='width:5rem ; background-color: yellowgreen'>ID/商品名称</th>";
                                    html += "<td colspan='8' id='singleProductId' style='font-size: 20px ; background-color: #d0e9c6' >"
                                    html += v.product_id + '/' + v.name ;
                                    html += "</td>";
                                    html += "</tr>";
                                    html += "<tr>";
                                    html += "<th style='width:5rem ; background-color: yellowgreen'>货位号</th>";
                                    html += "<td colspan='8' id='singleProductId' style='font-size: 20px ; background-color: #d0e9c6'>";
                                    html += v.stock_area  ;
                                    html += "</td>";
                                    html += "</tr>";
                                    html += "<tr>";
                                    html += "<th style='width:5rem ;background-color: yellowgreen'>条形码</th>";
                                    html += "<td colspan='8' id='singleProductId' style=' font-size: 20px ;background-color: #d0e9c6'>"
                                    html += v.sku_barcode ;
                                    html += "</td>";
                                    html += "</tr>";
                                    html += "<tr>";
//                                    html += "<th style='width:5rem; background-color: yellowgreen'>最大容量</th>";
//                                    html += "<td colspan='4' id='singleProductId' style=' font-size: 20px ;background-color: #d0e9c6'>"
//                                    html += v.storage_capacity_quantity ;
//                                    html += "</td>";
                                    html += "<th style='width:5rem;background-color: yellowgreen'>安全库存</th>";
                                    html += "<td colspan='4' id='singleProductId' style=' font-size: 20px ;background-color: #d0e9c6'>"
                                    html += v.safe_stock ;
                                   html += '<input style="width: 100px; float: right" class="qtyopt"  type="button" id="button_id'+ v.product_id+'"  value="移库操作" onclick="javascript:getTransferProductInfo(\''+ v.product_id +'\');" >' ;
                                    html += "</td>";
                                    html += "</tr>";
//                                    html += "<tr>";
//                                    html += "<th style='width:5rem;background-color: yellowgreen'>下单量</th>";
//                                    html += "<td colspan='4' id='singleProductId' style='font-size: 20px ; background-color: #d0e9c6'>"
//                                    html += v.op_quantity ;
//                                    html += "</td>";
//                                    html += "<th style='width:5rem; background-color: yellowgreen'>分拣量</th>";
//                                    html += "<td colspan='4' id='singleProductId' style='font-size: 20px ; background-color: #d0e9c6'>"
//                                    html += v.ios_quantity ;
//                                    html += "</td>";
//                                    html += "</tr>";
//                                    html += "<tr>";
//                                    html += "<th style='width:5rem;background-color: yellowgreen'>分拣位库存</th>";
//                                    html += "<td colspan='4' id='singleProductId' style='font-size: 20px ; background-color: #d0e9c6'>"
//                                    html += v.stock_area_quantity ;
//                                    html += "</td>";
//                                    html += "<th style='width:5rem;background-color: yellowgreen'>移库量</th>";
//                                    html += "<td colspan='4' id='singleProductId' style=' background-color: #d0e9c6'>"
//                                    html += "<span><input id='transfer"+ v.product_id +"'  value=''  style='width:100px;height:30px;'> </span>";
//                                    html += "</td>";
//                                    html += "</tr>";
//                                    html += "<tr>";
//                                    html += "<td colspan='8'>";
//                                    html += '<input style="width: 100px;" class="qtyopt" type="button" id="button_id'+ v.product_id+'"  value="开始" onclick="javascript:addTranserMission(\''+ v.product_id +'\');" >' +  '<input style="width: 100px;" class="qtyopt" type="button" id="button_id'+ v.product_id+'"  value="结束" onclick="javascript:addTranserMission1(\''+ v.product_id +'\');" >'
//                                    html +="</td>";
//                                    html += "</tr>";
                                    html += "<tr>";
                                    html += "<td colspan='8'>";
                                    html += "<br >";
                                    html +="</td>";
                                    html += "</tr>";

                                    html += "</tbody>";



                            $('#productsInfo3').html(html);

                        });
                        var sound = soundEffectInit();
                        sound.playerSubmit.play();
                    }
                });
            }

            setInterval("getTransferMission()","9000000");
        }



        function getTransferProductInfo(id){
            $("#fenjianform").show();
            $("#productsInfo3").hide();
            $("#barcon").hide();
            $('#productsHold2').show();
            $("#submitProductSku").hide();
//            var id = $('#product2').val();
           var warehouse_section_id = 1 ;
            var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';
            if(id !== ''){
                if(id.length >= 4){

                    $.ajax({
                        type : 'POST',
                        url : 'invapi.php',
                        data: {
                            method: 'getTransferProductInfo',
                            data: {
                                warehouse_id: warehouse_id,
                                sku_id: id,
                                warehouse_section_id:warehouse_section_id,
                                inventory_user_id:inventory_user_id,
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
                                    $('#input_stock_area_quantity').val(jsonData.quantity);

                                    $('#input_safe_area_quantity').val(jsonData.safe_quantity);
                                    $('#input_capacity_quantity').val(jsonData.capacity);

                                    $('#stock_area').html(jsonData.stock_area);
                                    $("#stock_move_out_area").val(jsonData.name);
                                    $("#stock_move_out_area_num").val(Math.abs(jsonData.inventory_quantity));
                                    $("#product_id_transfer").val(jsonData.transfer_id);
                                    $("#status_id").html(jsonData.status);

                                    var stock_move_out_area_num = $("#stock_move_out_area_num").val();

                                    if(stock_move_out_area_num != 0 ){
                                           $("#stock_move_in_tr").show();
                                            $("#stock_move_in_num_tr").show();
                                            $("#stock_move_in_button_tr").show();
                                        $("#stock_move_out_area").attr("disabled","true");
                                        $("#stock_move_out_area_num").attr("disabled","true");
                                        $("#stock_move_out_button").attr("disabled","true");
                                    }


                                    //$("#newProductBarCode").focus();

                                }


                                if(jsonData.status == 999){
                                    alert("未登录，请登录后操作");
                                    window.location = 'inventory_login.php?return=i.php';
                                }

                            }
                        },
                        complete : function(){
                            getStockSectionProduct(id);
                        }

                    });
                }
                else{
                    alert('错误的条码['+id+']');
                    $('#product2').val('');
                    return false;
                }
            }

        }

        function  getStockSectionProduct(product_id) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'getStockSectionProduct',
                    data: {
                        warehouse_id: warehouse_id,
                        product_id: product_id,
                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);
                    if(jsonData) {
                        var html = '';
                        $.each(jsonData,function(i,v){
                            html += "<tr style='background:#d0e9c6;' id='sparetr"+v.product_id+"'>";
                            html += "<td  id='sparename"+ v.product_id+"'>"+"<span style='font-weight:bold'>"+   v.name ;
                            html += "</td>";
                            html += "<td id='spareneedsorted"+ v.product_id+"' >"+ v.quantity + "</td>";

                            html += "</tr>";
                        });

                        $('#productsInfo2').html(html);
                    }
                }
            });
        }

        function  changeTransferValuse(transfer_move_id , status ){
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'changeTransferValuse',
                    data: {
                        transfer_move_id: transfer_move_id,
                        status: status,
                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(respons);
                    if(jsonData == 1) {
                        alert(jsonData);
                        getTransferMission();
                    }
                }
            });

        }

        function  confirmOut() {
            var stock_move_out_area = $("#stock_move_out_area").val();
            var stock_move_out_area_num = $("#stock_move_out_area_num").val();
            var product_id = $("#product_id_transfer").val();
            var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';
            if(stock_move_out_area == ''  || stock_move_out_area_num == ''){
                alert('移出货位跟数量都不能为空');
                return false ;
            }
            if(confirm("确认移出吗？")) {
                $.ajax({
                    type: 'POST',
                    url: 'invapi.php',
                    data: {
                        method: 'confirmOut',
                        data: {
                            warehouse_id: warehouse_id,
                            stock_move_out_area: stock_move_out_area,
                            stock_move_out_area_num: stock_move_out_area_num,
                            product_id: product_id,
                            inventory_user_id:inventory_user_id,

                        },
                    },
                    success: function (response) {
                        var jsonData = $.parseJSON(response);
                        if (jsonData == 1) {
                            alert('该货位没有此商品');
                            $("#stock_move_out_area").val('');
                            $("#stock_move_out_area_num").val('');

                        }
                        if(jsonData == 2 ){
                            $("#stock_move_out_area").attr("disabled","true");
                            $("#stock_move_out_area_num").attr("disabled","true");
                            $("#stock_move_out_area_num").attr("disabled","true");
                            $("#stock_move_out_button").attr("disabled","true");
                            $("#stock_move_in_tr").show();
                            $("#stock_move_in_num_tr").show();
                            $("#stock_move_in_button_tr").show();
                          alert('已移出');
                        }
                    }


                });

            }
       }

    function confirmIn(){
        var stock_move_in_area = $("#stock_move_in_area").val();
        var stock_move_in_area_num = $("#stock_move_in_area_num").val();
        var stock_move_out_area_num = $("#stock_move_out_area_num").val();
        var stock_move_out_area = $("#stock_move_out_area").val();
        var product_id = $("#product_id_transfer").val();
        var inventory_user_id = '<?php echo $_COOKIE['inventory_user_id'];?>';
        if(stock_move_in_area == ''  || stock_move_in_area_num == ''){
            alert('移入货位跟数量都不能为空');
            return false ;
        }

        if(stock_move_out_area_num != stock_move_in_area_num ){
            alert ('移出的数量跟移入的数量不相等');
            return false ;
        }

        if(confirm("确认移入吗？")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'confirmIn',
                    data: {
                        warehouse_id: warehouse_id,
                        stock_move_in_area: stock_move_in_area,
                        stock_move_in_area_num: stock_move_in_area_num,
                        product_id: product_id,
                        inventory_user_id:inventory_user_id,
                        stock_move_out_area:stock_move_out_area,

                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);
                    if (jsonData == 1) {
                        alert('该货位没有此商品');
                        $("#stock_move_out_area").val('');
                        $("#stock_move_out_area_num").val('');

                    }
                    if(jsonData == 2 ){
                        $("#stock_move_out_area").attr("disabled","true");
                        $("#stock_move_out_area_num").attr("disabled","true");
                        $("#stock_move_out_area_num").attr("disabled","true");
                        $("#stock_move_out_button").attr("disabled","true");
                        $("#stock_move_in_tr").show();
                        $("#stock_move_in_num_tr").show();
                        $("#stock_move_in_button_tr").show();
                        alert('已移入');
                        location.reload();

                    }
                }


            });

        }
    }
    function  handleProductList2() {
        var stock_section_type_id =  $('#stock_section_type option:selected') .val();//选中的值
        if (stock_section_type_id == 0 ) {
            alert('请选择移库类型');
            return false ;
        }

        var sku_id = $("#productmanaual").val();
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getSkuProductId',
                data: {
                    warehouse_id: warehouse_id,
                    sku_id: sku_id,
                },
            },

            success: function (response) {
                var jsonData = $.parseJSON(response);
                getTransferProductInfo(jsonData);
            },
            complete:function(){
                if(stock_section_type_id == 2 ){
                    $("#stock_move_out_tr").hide();
                    $("#stock_move_out_num_tr").hide();
                    $("#stock_move_out_button_tr").hide();
                    $("#stock_move_in_tr").show();
                    $("#stock_move_in_num_tr").show();
                    $("#stock_move_in_button_tr").show();
                }
            }
        });
    }


    </script>



