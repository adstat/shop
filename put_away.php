<?php
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
$warehouseId = isset($_REQUEST['warehouseId']) ? (int)$_REQUEST['warehouseId'] : 0;
/*
 * 查仓库
 * */

$sql="SELECT * FROM oc_x_stock_section_type";
$type=$db->query($sql)->rows;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script type="text/javascript" src="view/javascript/bootstrap4/js/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/bootstrap4/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="view/javascript/bootstrap4/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="view/javascript/bootstrap4/css/bootstrap.min.css"></link>
    <link rel="stylesheet" href="view/javascript/bootstrap4/css/bootstrap-grid.min.css"></link>
    <link rel="stylesheet" href="view/javascript/bootstrap4/css/bootstrap-reboot.min.css"></link>
    <link rel="stylesheet" href="view/javascript/bootstrap4/css/font-awesome.min.css"></link>
    <style>
        .form-control {
            width:100%;
            display: block;
        }
    </style>
    <title>上架</title>
</head>
<body>

<div class="custom-control-inline">
<!--    <form id="addData" method="post">-->
        <input type="hidden" name="warehouse_id" value="<?=$_COOKIE['warehouse_id']?>">
        <input type="hidden" name="inventory_user" value="<?=$_COOKIE['inventory_user']?>">
        <!--综合数据-->
        <div class="showData"></div>
<!--    </form>-->
</div>
<!--列表-->
<div class="table-responsive-sm" id="begin_storage">
    <table  class="table table-sm table-striped table-hover">
        <thead>
        <tr>
            <th scope="col">产品号</th>
            <th scope="col">托盘号</th>
            <th scope="col">原库位</th>
            <th scope="col">移库位</th>
            <th scope="col">数量</th>
            <th scope="col">操作</th>
        </tr>
        </thead>
        <tbody class="col-sm-12" id="show_list">
        </tbody>
    </table>
</div>
</body>
<script>
    $(document).ready(function(){
        initParam(1);
    });
    function initParam(typeId) {
        modalData();
        $("input[name='to_area_id']").focus();
        $("#show_div_from").hide();
        window.typeId = typeId;
        window.warehouse_id = parseInt(<?=$_COOKIE['warehouse_id']?>);
        var flag = 2;//标记
        $.ajax({
            type: 'POST',
            url: 'put_away_dispose.php/select',
            dataType: 'json',
            data: {'flag': flag, 'type_id': typeId},
            success: function (data) {
                var tr = '';
                $.each(data, function (k, vv) {
                    tr += '<tr id=pro_'+vv.id+'>';
                    tr += '<td>' + vv.product_name +'<span class="badge badge-pill badge-warning">'+vv.product_id+'</span></td>';
                    tr += '<td>' + vv.tray_id + '</td>';
                    tr += '<td>' + vv.to_area_id + '</td>';
                    tr += '<td>' + vv.from_area_id+ '</td>';
                    tr += '<td>' + vv.quantity + '</td>';
                    tr += '<td>' + '<a  onclick="updateStatu(' + vv.id +","+vv.product_id+',\''+vv.to_area_id+'\','+vv.tray_id+');" class="btn btn-success" href="#">' + '移入' + '</a>' + '<a onclick="showID(' + vv.id + ');" id="delId" value="' + vv.id + '" class="btn btn-danger" href="#">\n' + '删除' + '</a>' + '</td>';
                    tr += '</tr>';
                });

                //添加到列表
                $('#show_list').html(tr);
            }
        });//查询结束
    }
    /*
    * 正则表达式
    * */
    //检查产品ID
    function chkProductId(product_id,id,autoPosition=true){
        if (autoPosition == false) {
            $('input[name="product_id"]').val(product_id);
        }
            var reg= /^\d{4,6}$/;
            if(reg.test(product_id)){
                $.ajaxSettings.async = false;
                $.post('put_away_dispose.php/findProductId',
                    {id:id,product_id:product_id,flag:'7'},
                    function (productData) {
                        if (productData == "" || productData == undefined) {
                            $("#show_product_id").text('没有数据');
                            showAlertError("#show_success", '此商品不存在', '请检查产品编码的合法性！');
                            $('#show_product').hide();
                        }
                        else {
                            $("#show_product_id").text('数据正确');
                            $("input[name='quantity']").attr('disabled',false);
                            productDatas=productData;

                            if (autoPosition == true) {
                                showModel();
                                $('input[name="quantity"]').focus();
                            }
                        }
                    },"json");//ajax结束
                $.ajaxSettings.async = false;
                $("#show_product_id").removeClass();
                $("#show_product_id").addClass("bg-success text-white");
            }else {
                $("#show_product_id").removeClass();
                $("#show_product_id").addClass("bg-warning text-white");
                $("#show_product_id").text('验证失败');

            }

    }

    function updateStatu(id,product_id,to_area_id,tray_id) {
        $("#show_div_from").show();
        $("#from_area_id").focus();
        clearValue();
        $("#from_area_id").on('click',function () {
            var reg= /(^[A-Z]+(\d{2,3})-(\d{2,3})$)|(^[A-Z]+(\d{2,3})-(\d{2,3})-(\d{2,3})$)/;
            var from_area_id=$("#from_area_id").val();
            if(reg.test(from_area_id)){
                alert('-------------');
                $("#show_from_area_id").text('输入正确');
                chkStorageId(to_area_id,false);
                chkProductId(product_id,id,false);
                showModel(id,tray_id,true);

            }else {
                $("#show_from_area_id").text('输入错误');
            }
        })
    }


    /*
    * 检查库位号
    * */

    function chkStorageId(to_area_id,autoPosition=true){
        if (autoPosition == false) {
            // $('input[name="to_area_id"]').attr('value',to_area_id);
            $('input[name="to_area_id"]').val(to_area_id);
        }
            var reg= /(^[A-Z]+(\d{2,3})-(\d{2,3})$)|(^[A-Z]+(\d{2,3})-(\d{2,3})-(\d{2,3})$)/;
            // StorageMsg = '';
            if(reg.test(to_area_id)){
                $("#show_to_area_id").removeClass();
                $("#show_to_area_id").addClass("bg-success text-white");
                    $.ajax({
                        type: "post",
                        url: 'put_away_dispose.php/findStorageId',
                        data: {to_area_id:to_area_id,
                            warehouse_id:warehouse_id,
                            type_id:typeId,
                            flag:'6'},
                        dataType: "json",
                        async:false,
                        // crossDomain: true,
                        // jsonpCallback: "jsonpCallbackFun",
                        // jsonp: "callback",
                        success: function (storageData) {
                            if (typeof storageData =="object") {
                                $("#show_to_area_id").text('输入正确');

                                var tr = '';
                                $.each(storageData, function (k,v) {
                                    tr += '<tr>';
                                    tr += '<td>' + v.product_name +'<span class="badge badge-pill badge-warning">'+v.id+'</span> </td>';
                                    tr += '<td>' + v.tray_id + '</td>';
                                    tr += '<td>' + v.to_area_id + '</td>';
                                    tr += '<td>' + v.from_area_id + '</td>';
                                    tr += '<td>' + v.quantity + '</td>';
                                    tr += '<td>' + '<a id="up_'+v.id+'" onclick="updateStatu(' + v.id +","+ v.product_id +","+ v.to_area_id +","+ v.tray_id + ');" class="btn btn-success" href="#">' + '移入' + '</a>' + '<a onclick="showID(' + v.id + ');" id="delId" value="' + v.id + '" class="btn btn-danger" href="#">\n' + '删除' + '</a>' + '</td>';
                                    tr += '</tr>';

                                });
                                //添加到列表
                                $("#show_storage").show();
                                $('#show_storage_list').html(tr);
                                $('input[name="quantity"]').attr('disabled',true);
                                $("input[name='product_id']").attr('disabled',false);
                                if (autoPosition == true) {
                                    $('input[name="product_id"]').focus();
                                }
                            }
                            else if (parseInt(storageData) == '1')
                            {//没有查到数据
                                $("#show_to_area_id").text('可以使用');

                                $('input[name="quantity"]').attr('disabled',true);
                                $("input[name='product_id']").attr('disabled',false);
                                if (autoPosition == true) {
                                    $('input[name="product_id"]').focus();
                                }
                                $('#show_storage').hide();


                                // StorageMsg = '1';
                            }
                            else if (parseInt(storageData) == '0') {//没有查到数据
                                $("#show_to_area_id").text('非法使用');
                                if (autoPosition == true) {
                                    $('input[name="to_area_id"]').val("");
                                    $("input[name='product_id']").attr('disabled',true);
                                    $("input[name='quantity']").attr('disabled',true);
                                    $('#show_storage').hide();
                                }
                                // StorageMsg = '-1';
                            }
                            else {
                                // $('#show_storage').hide();
                                $("#show_to_area_id").text('输入错误');
                                $('input[name="quantity"]').attr('disabled',true);
                                $("input[name='product_id']").attr('disabled',true);
                                if (autoPosition == true) {
                                    $('input[name="to_area_id"]').val("");
                                }
                                showAlertError("#show_success",'没有查到数据','请检查库位号的正确性！');
                                // StorageMsg = '0';
                            }
                        },
                        error: function (err) {
                            console.log(err);
                        },
                        /*complete:function () {

                        },*/
                    });
            }else {
                $("#show_to_area_id").removeClass();
                $("#show_to_area_id").addClass("bg-warning text-white");
                $("#show_to_area_id").text('只能是字母、‘-’并且长度为3~12位！');
                $('input[name="product_id"]').attr('disabled',true);
                $('input[name="quantity"]').attr('disabled',true);
                if (autoPosition == true) {
                    $('input[name="to_area_id"]').val("");
                }
                // StorageMsg = '-2';

            }
    }

    /*
    *删除方法
    * */
    function showID(id){
        $.post('put_away_dispose.php/delete',{id:id,flag:'3',type_id:typeId},function (data) {
            var str=$.parseJSON(data);
             if (str) {
                alert('删除成功!');
                $("#pro_"+id).remove();
            }else {
                 alert('删除失败!');
             }
        });
    }

    //计算数量
    function calcNumber(num){

        var nowNumber=$("#calc_Number").val();
        var fromAreaNumber=$("#from_area_id").val().length;
        var sumNumber=$("#stockNumber").text();//总数
        var isNullId=$("input[name='ids']").val();
        var bufferNumber=$("#bufferNumber").text();//移动数
        var number=parseInt(sumNumber) - parseInt(bufferNumber);
        // alert(sumNumber);
        if(nowNumber == "undefined"||nowNumber == ""){
            nowNumber=0;
        }


        var str='';
        str = parseInt(num)+ parseInt(nowNumber);
        if(str <=0){
            str=0;
        }else  if (str > parseInt(sumNumber)) {
            alert('不能大于总数!');
            str=sumNumber;
        }

        if (typeof isNullId !=="undefined" || !empty(isNullId)) {
            if (str > parseInt(bufferNumber)) {
                alert('不能大于已移动数');
                str=0;
            }
        }
        $("#calc_Number").val(str);
        $("input[name='quantity']").val(str);
    }

    //modal弹出方法中要展示的一条数据
    function getTableName(tableName,id,findId){
        $.post('put_away_dispose.php/find',{tableName:tableName,id:id,findId:findId,flag:'4'},function (data) {
            alert(data);
        });
    }
    //获取当前仓库信息
    function getWhoWarehouse(warehouse_id) {
        $.post('put_away_dispose.php/getWarehouse',{id:warehouse_id,flag:'5'},function (data) {
            // console.log(data);
            if(data){
                // showAlertSuccess('删除成功');
                var warehouseName=JSON.parse(data);
                // console.log(warehouseName['title']);
                $("#warehouse_name").text(warehouseName['title']);

            }
        });
    }
    function clearValue() {
        $("input[name='to_area_id']").val('');
        $("input[name='from_area_id']").val('');
        $("input[name='product_id']").val('');
        $("input[name='quantity']").val('');
    }

    /*
    * 提交数据
    *
    * */
    function getAllData() {
        var id= $("input[name='ids']").val();
        var warehouse_id= <?=$_COOKIE['warehouse_id']?>;
        var inventory_user= $("input[name='inventory_user']").val();
        var product_id= $("input[name='product_id']").val();
        var to_area_id= $("input[name='to_area_id']").val();
        var from_area_id= $("input[name='from_area_id']").val();
        var quantity= $("input[name='quantity']").val();
        var tray_id= $("input[name='tray_id']").val();
        var user_id = <?=$_COOKIE['inventory_user_id']?>;
        var flag='1';
        $.ajax({
            type:'POST',
            url:'put_away_dispose.php/insert',
            dataType:'json',
            data:{
                'id':id,
                'user_id':user_id,
                'tray_id':tray_id,
                'warehouse_id':warehouse_id,
                'inventory_user':inventory_user,
                'product_id':product_id,
                'to_area_id':to_area_id,
                'from_area_id':from_area_id,
                'quantity':quantity,
                'type_id':typeId,
                'flag':flag
            },
            // data:$.param({'type_id':typeId})+'&'+$('form').serialize(),
            success:function (data) {
                if(data){
                    $('#exampleModal').modal('hide');
                    clearValue();
                }
            },
            error:function (err) {
                alert('添加失败!');
                $('#exampleModal').modal('hide');
                clearValue();
            }
        });//添加结束
    }
    //合并数据
    function modalData(){
        // getTableName(oc_product,product_id,id);
        $('.showData').html("<div id=\"list\" class=\"container fluid\">\n" +
            "            <div class=\"row\">\n" +
            "                <div class=\"card\">\n" +
            "<!--                    <h5 class=\"card-header\">Featured</h5>-->\n" +
            "                    <div class=\"card-header\">\n" +
            "                        <div class=\"row justify-content-between\">\n" +
            "                            <div class=\"col-4\">\n" +
            "                                <button class=\"btn btn-primary\">返回</button>\n" +
            "                            </div>\n" +
            "                            <div class=\"col-4\">\n" +
            "                               <strong> <?=$_COOKIE['inventory_user']; ?></strong>\n" +
            "                            </div>\n" +
            "                            <div class=\"col-4\">\n" +
            "                                <button class=\"btn btn-info\">退出</button>\n" +
            "                            </div>\n" +
            "                            <div class=\"col-12 text-center\"><strong>当前仓库:</strong><span id=\"warehouse_name\" class=\"badge badge-warning\"><?=$_COOKIE['warehouse_title']; ?></span></div>\n" +
            "                        </div>\n" +
            "                    </div>\n" +
            "                </div>\n" +
            "            </div>\n" +
            "           <div id=\"show_success\"></div>\n" +
            "            <div  id=\"show_div_from\" class=\"row\">\n" +
            "                <div class=\"input-group sm-3\">\n" +
            "                    <div class=\"input-group-prepend\">\n" +
            "                        <span class=\"input-group-text\" id=\"basic-addon1\">移动库位</span>\n" +
            "                    </div>\n" +
            "                    <input  type=\"text\" id=\"from_area_id\"  name=\"from_area_id\" class=\"form-control\" placeholder=\"移动库位到\" >\n" +
            "                    <small id=\"show_from_area_id\"  class=\"bg-warning text-white\"></small>\n" +
            "                </div>\n" +
            "            </div>\n" +
            "            <div class=\"row\">\n" +
            "                <div class=\"input-group sm-3\">\n" +
            "                    <div class=\"input-group-prepend\">\n" +
            "                        <span class=\"input-group-text\" id=\"basic-addon1\">库位号</span>\n" +
            "                    </div>\n" +
            "                    <input onclick=\"chkStorageId(this.value,true)\"  type=\"text\" id=\"StorageId\"  name=\"to_area_id\" class=\"form-control\" placeholder=\"库位号\" >\n" +
            "                    <small id=\"show_to_area_id\"  class=\"bg-warning text-white\"></small>\n" +
            "                </div>\n" +
            "            </div>\n" +

                        "<div style=\"display:none\" id=\"show_storage\" class=\"row\">\n"+
                            "<div class=\"table-responsive-sm\">"+
                            "    <table  class=\"table table-sm table-striped table-hover\">"+
                            "        <thead>"+
                            "        <tr>"+
                            "            <th scope=\"col\">产品号</th>"+
                            "            <th scope=\"col\">托盘号</th>"+
                            "            <th scope=\"col\">库位号</th>"+
                            "            <th scope=\"col\">移库位</th>"+
                            "            <th scope=\"col\">数量</th>"+
                            "            <th scope=\"col\">操作</th>"+
                            "        </tr>"+
                            "        </thead>"+
                            "        <tbody id=\"show_storage_list\" class=\"col-sm-12\">"+
                            "        </tbody>"+
                            "    </table>"+
                            "</div>"+
                        "</div>\n" +
            "            <div  class=\"row\" >\n" +
            "                <div class=\"input-group sm-12\">\n" +
            "                    <div class=\"input-group-prepend\">\n" +
            "                        <span class=\"input-group-text\" id=\"basic-addon1\">商品号</span>\n" +
            "                    </div>\n" +
            "                    <input type=\"text\" onclick=\"chkProductId(this.value)\" name=\"product_id\" class=\"form-control\" placeholder=\"商品编号\" >\n" +
            "                    <small id=\"show_product_id\"  class=\"bg-warning text-white\"></small>\n" +
            "                </div>\n" +
            "            </div>\n" +
                        "<div style=\"display:none\" id=\"show_product\" class=\"row\">\n"+
                            "<div class=\"table-responsive-sm\">"+
                            "    <table  class=\"table table-sm table-striped table-hover\">"+
                            "        <thead>"+
                            "        <tr>"+
                            "            <th scope=\"col\">产品号</th>"+
                            "            <th scope=\"col\">库位号</th>"+
                            "            <th scope=\"col\">移库位</th>"+
                            "            <th scope=\"col\">数量</th>"+
                            "            <th scope=\"col\">操作</th>"+
                            "        </tr>"+
                            "        </thead>"+
                            "        <tbody id=\"show_product_list\" class=\"col-sm-12\">"+

                            "        </tbody>"+
                            "    </table>"+
                            "</div>"+
                        "</div>\n" +

                        "<div style=\"display:none\" id=\"show_tray\" class=\"row\">\n"+
                            "<div class=\"table-responsive-sm\">"+
                            "    <table  class=\"table table-sm table-striped table-hover\">"+
                            "        <thead>"+
                            "        <tr>"+
                            "            <th scope=\"col\">产品号</th>"+

                            "            <th scope=\"col\">托盘位</th>"+
                            "            <th scope=\"col\">库位号</th>"+
                            "            <th scope=\"col\">数量</th>"+
                            "            <th scope=\"col\">操作</th>"+
                            "        </tr>"+
                            "        </thead>"+
                            "        <tbody id=\"show_tray_list\" class=\"col-sm-12\">"+
                            "        </tbody>"+
                            "    </table>"+
                            "</div>"+
                        "</div>\n" +
                        "<div style=\"display:none\" id=\"show_tray\" class=\"row\">\n"+
                        "</div>\n"+
            "            <div class=\"row\">\n" +
            "                <div class=\"input-group sm-3\">\n" +
            "                    <div class=\"input-group-prepend\">\n" +
            "                        <span class=\"input-group-text\" id=\"basic-addon1\">上架数</span>\n" +
            "                    </div>\n" +
            "                    <input id=\"chkQuantity\" data-toggle=\"modal\" data-target=\"#exampleModal\" type=\"text\" name=\"quantity\" class=\"form-control\" placeholder=\"数量\" >\n" +
            "                    <small id=\"show_quantity\"></small>\n" +
            "                </div>\n" +
            "            </div>\n" +
                        "<div id=\"show_model\" class=\"row\">\n" +
                        "</div>\n" +
            "        </div>");
    };
    function showModel(id,tray_id,openModel=true) {


        $("#show_model").html("" +
            "                <!-- Modal -->\n" +
            "                <div class=\"modal fade\" id=\"exampleModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">\n" +
            "                    <div class=\"modal-dialog\" role=\"document\">\n" +
            "                        <div  class=\"modal-content\">\n" +
            "                            <!--计算器-->\n" +
            "                            <div id=\"show_info\" class=\"modal-header\">\n" +
            "                            </div>\n" +
            "                            <div class=\"modal-body\">\n" +
            "                                <form>\n" +
            "                                    <div class=\"container\">\n" +
            "                                        <div class=\"row\">\n" +
            "                                            <div class=\"input-group sm-3\">\n" +
            "                                                <div class=\"input-group-prepend\">\n" +
            "                                                    <span class=\"input-group-text\" id=\"basic-addon1\">托盘号</span>\n" +
            "                                                </div>\n" +
            "                                                <input type=\"text\" name=\"tray_id\" value="+tray_id+" >\n" +
            "                                                <input type='hidden'  name=\"ids\" value="+id+"  >\n" +
            "                                                <small id=\"show_to_area_id\"></small>\n" +
            "                                            </div>\n" +
            "                                        </div>\n" +
            "                                        <div class=\"row\">\n" +
            "                                            <div class=\"col-xs-12 col-sm-12\">\n" +
            "                                                <div class=\"input-group\">\n" +
            "                                                    <div class=\"input-group-prepend\">\n" +
            "                                                        <span class=\"input-group-text\" id=\"basic-addon1\">上架数</span>\n" +
            "                                                    </div>\n" +
            "                                                    <input disabled   id=\"calc_Number\" value=\"\"  type=\"text\" class=\"form-control\" id=\"recipient-name\">\n" +
            "                                                    <small id=\"show_to_area_id\"></small>\n" +
            "                                                </div>\n" +
            "                                                <div class=\"col-xs-8 col-sm-8\">\n" +
            "                                                    <div class=\"form-group\">\n" +
            "                                                        <p> <button onclick=\"calcNumber(+1);\"  type=\"button\" class=\"btn btn-success\"><i class=\"icon-plus icon-large\"></i></button>&nbsp;<button onclick=\"calcNumber(+10);\" type=\"button\" class=\"btn btn-success\">+10</button>&nbsp;<button onclick=\"calcNumber(+50);\" type=\"button\" class=\"btn btn-success\">+50</button></p>\n" +
            "                                                        <p> <button onclick=\"calcNumber(-1);\" type=\"button\" class=\"btn btn-danger\"><i class=\"icon-minus icon-large\"></i></button>&nbsp;<button onclick=\"calcNumber(-10);\" type=\"button\" class=\"btn btn-danger\">-10</button>&nbsp;<button onclick=\"calcNumber(-50);\" type=\"button\" class=\"btn btn-danger\">-50</button></p>\n" +
            "                                                    </div>\n" +
            "                                                </div>\n" +
            "                                            </div>\n" +
            "                                        </div>\n" +
            "                                        <div id=\"show_div\"></div>\n" +
            "                                    </div>\n" +
            "                                </form>\n" +
            "                            </div>\n" +
            "                            <div class=\"modal-footer\">\n" +
            "                                <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">取消</button>\n" +
            "                                <button onclick=\"getAllData();\"; id=\"form_data\" type=\"button\" class=\"btn btn-primary\">提交</button>\n" +
            "                            </div>\n" +
            "                        </div>\n" +
            "                    </div>\n" +
            "                </div>\n" +
            "");

        $.each(productDatas, function (k, v) {
            window.trs =
                "                                <div class=\"col-12 text-lg-center\">" +
                "                                    <p><strong>商品名称：</strong><span class='productName'>" + v.name + "</span></p>" +
                "                                    <p><strong>商品条码：</strong><span>" + v.sku_barcode + "</span></p>" +
                "                                    <p><strong>入库总数：</strong><span id='stockNumber'>" + v.stock_num + "</span></p>" +
                "                                    <p><strong>已移动数：</strong><span id='bufferNumber'>" + v.buffer_num + "</span></p>" +
                "                                </div>";
        });
        $("#show_info").html(trs);
        if (typeof tray_id == "undefined") {
            $("input[name='tray_id']").attr('value','');
        }
        if (openModel == true) {
            $("#exampleModal").modal('show');
        }
        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var recipient = button.data('whatever') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('.modal-title').text('New message to ' + recipient)
            modal.find('.modal-body input').val(recipient)
        });


    };

    /**
     * @param header 标题
     * @param content 内容
     */
    function showAlertSuccess(tally,header,content,auto=false) {
        $(tally).html("<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n" +
            "                                            <strong>"+header+"!</strong> "+content+".\n" +
            "                                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
            "                                                <span aria-hidden=\"true\">&times;</span>\n" +
            "                                            </button>\n" +
            "                                        </div>");
        $(tally).alert();
        if (auto==true){
            setTimeout(function(){$(tally).hide('slow')}, 3000);
        }
        // setTimeout(function(){$(tally).hide('slow')}, 2000);
    }

    /*
    * 错误
    * */
    function showAlertError(tally,header,content,auto = false) {
        $(tally).html("<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n" +
            "                                            <strong>"+header+"!</strong> "+content+".\n" +
            "                                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
            "                                                <span aria-hidden=\"true\">&times;</span>\n" +
            "                                            </button>\n" +
            "                                        </div>");
        $(tally).alert();
        if (auto==true){
            setTimeout(function(){$(tally).hide('slow')}, 3000);
        }
    }
</script>
</html>