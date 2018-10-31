<?php

require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');

if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
$date = date('Y-m-d');
$warehouse_id = isset($_REQUEST['warehouse_id']) ? (int)$_REQUEST['warehouse_id'] : 21;
$gap = isset($_REQUEST['gap']) ? (int)$_REQUEST['gap'] : 2;
$gap = $gap<=15 ? $gap : 15; //查询不超过15天
$sql = "select warehouse_id, shortname warehouse from oc_x_warehouse where status = 1 and station_id = 2";
$list = $db->query($sql);
$dataWarehouseRaw = $list->rows;
foreach($dataWarehouseRaw as $m){
    $dataWarehouse[$m['warehouse_id']] = $m;
}

?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>仓库班组日报</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
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
            font-size: 1rem;
        }
        td{
            /*background-color:#d0e9c6;*/
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            font-size: 0.9rem;
        }

        th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.3);
        }

        #listData table caption{text-align: left; padding:0.3rem;}
        #listData table {width: 100%}
        .list{display: flex;flex-direction: column;}
        .list li{flex: 1;border: 1px solid #00b3ee; text-align:-webkit-center;cursor:pointer;background: #dddddd;margin: 10px 0px 10px 0px;
        }
        input{
            border: 1px solid #ccc;
            padding: 2px;
            font-size: 1.2em;
            color: #444;
            width: 200px;
            border-radius: 5px;
            border-color: #34ce57;
            margin: 10px auto;
        }
        button{
            cursor:pointer;
            border: 1px solid #ccc;
            padding: 2px;
            font-size: 1.2em;
            color: #444;
            width: 200px;
            border-radius: 5px;
            border-color: #34ce57;
            margin: 10px auto;
        }

        .select {
            border: 1px solid #ccc;
            padding: 2px;
            font-size: 1.2em;
            color: #444;
            width: 200px;
            border-radius: 5px;
            border-color: #34ce57;
            margin: 10px auto;
            text-align: center;
        }


    </style>

</head>

<body>
<script>
    window.search_type=0;
    $(document).ready(function () {
        var flag=0;
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php/getWarehouse',
            dataType: 'json',
            data: {'flag': flag},
            success: function (data) {
                var li='';
                $.each(data,function (k, v) {
                    li+='<option value="'+v.warehouse_id+'">'+v.warehouse+'</option>';
                })
                $("select[name='warehouse']").append(li);
            }
        });//查询结束
    });
</script>
<div>
    <ul class="list">
        <li>分拣</li>
        <li onclick="search_information(1)">出库</li>
        <li onclick="search_information(2)">入库</li>
        <li onclick="search_information(3)">合单</li>
        <li>配送</li>
        <li>回库退货</li>
        <li>退货上架</li>
        <li>回款</li>
    </ul>
</div>
<div id="setOptions" hidden >
    <p style="text-align: center"><strong>分拣仓库:</strong><select class="select" name="warehouse"></select></p>
    <p style="text-align: center"><strong>分拣日期:</strong><input type="text" name="date" value="<?=date("Y-m-d",strtotime("-1 day")) ?>"></p>

    <p style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;<button id="sortSearch" type="button" value="1">未分拣订单</button>&nbsp;&nbsp;&nbsp;&nbsp; <button id="sortSearch" type="button" value="2">抽查问题列表</button>&nbsp;&nbsp;&nbsp;&nbsp;</p>
    <hr>
</div>


<!--<div id="sortList" hidden>-->
<!--    <table  style="margin-top: 5rem; height: 100px; overflow: hidden;">-->
        <table id="showSortData_1" hidden>
            <thead>
            <tr>
                <th>分拣单号</th>
                <th>订单号</th>
                <th>目地仓库</th>
                <th>所属仓库/th>
                <th>加急</th>
                <th>深度</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <table id="showSortData_2" hidden>
            <thead>
            <tr>
                <th>订单ID</th>
                <th>订单整件</th>
                <th>订单散件</th>
                <th>分拣整件</th>
                <th>分拣散件</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <table id="showSortData_3" hidden>
            <thead>
            <tr>
                <th>分拣日期</th>
                <th>仓库</th>
                <th>订单数</th>
                <th>滞留三天订单数</th>
                <th>满足出库条件</th>
                <th>不满足出库条件</th>
                <th>滞留三天订单占比</th>
            </tr>
            </thead>
            <tbody >

            </tbody>
        </table>
<!--    </table>-->
<!--</div>-->

<table id="entryWarehouseList">
    <thead>
    <tr>
        <th>分拣单号</th>
        <th>出库数量[整]</th>
        <th>出库数量[散]</th>
        <th>入库数量[整]</th>
        <th>入库数量[散]</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div id="outWarehouseList" hidden>
    <table style="margin-top: 5rem; height: 100px; overflow: hidden;">
        <div id="scanRander">
            <p><strong>分拣仓库</strong><select class="select" name="scanWarehouse"></select></p>
            <p><strong>分拣开始时间</strong><input type="text" name="scanStartDate"></p>
            <p><strong>分拣结束时间</strong><input type="text" name="scanEndDate"></p>
            <hr>
            <button id="scanSearch" type="button">搜索</button>
        </div>
        <strong>》已提交分拣未扫描出库</strong>
        <table>
            <h2>分拣单号</h2>
            <tbody>
            <textarea name="" id="showScanData" cols="30" rows="30">

            </textarea>
            </tbody>
        </table>
        <strong>》扫码失败</strong>
        <table>
            <thead>
            <tr>
                <th>调拨单号</th>
                <th>筐号</th>
                <th>商品ID</th>
                <th>调拨出库数量</th>
                <th>调拨生成数量</th>
            </tr>
            </thead>
            <tbody id="showScanFailureData">
            </tbody>
        </table>
    </table>
</div>
<div id="singleList" hidden>
        <table style="margin-top: 5rem; height: 100px; overflow: hidden;">
            <div id="singleRander">
                <p><strong>分拣仓库</strong><select class="select" id=""></select></p>
                <p><strong>分拣开始时间</strong><input type="text" name="singleLStartDate"></p>
                <p><strong>分拣结束时间</strong><input type="text" name="singleLEndDate"></p>
                <hr>
                <button id="singleSearch" type="button" onclick="Javascript:search_information()">搜索</button>
            </div>
            <strong>》异常合单</strong>
            <table>
                <thead>
                <tr>
                    <th>订单号</th>
                    <th>分拣单号</th>
                    <th>调拨入库数量</th>
                    <th>合单数量</th>
                </tr>
                </thead>
                <tbody id="showSingleData">

                </tbody>
            </table>
        </table>
</div>
<div id="search_informations" hidden>
    <table style="margin-top: 5rem; height: 100px; overflow: hidden;">
        <div id="search_info">
            <p><strong>分拣仓库</strong><select class="select" id="do_warehouse_id"><?php foreach($dataWarehouse as $value){echo '<option value="'.$value['warehouse_id'].'">'.$value['warehouse'].'</option>';} ?></select></p>
            <p><strong>出库仓库</strong><select class="select" id="warehouse_id"><?php foreach($dataWarehouse as $value){echo '<option value="'.$value['warehouse_id'].'">'.$value['warehouse'].'</option>';} ?></select></p>
            <p><strong>时间</strong><select id="before_time" name='gap'>
                    <option value="1" > >1天</option>
                    <option value="2" > >2天</option>
                    <option value="3"  > >3天</option>
                    <option value="4" > >4天</option>
                    <option value="5" > >5天</option>
                    <option value="6"> >6天</option>
                    <option value="7" >7天</option>
                </select></p>
            <hr>
            <button type="button" onclick="Javascript:search_information()">搜索</button>
        </div>
        <strong id="search_type_name">出库</strong>
        <table>
            <caption>周转筐</caption>
            <thead>
            <tr>
                <th>分拣单号</th>
                <th>订单号</th>
                <th>配送日期</th>

                <th>出库仓库</th>
                <th>分拣仓库</th>

                <th id="repack_text1"></th>
                <th id="repack_text2"></th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody id="search_information_repack">

            </tbody>
        </table>
        <hr />
        <table>
            <caption>整件</caption>
            <thead>
            <tr>
                <th>配送日期</th>
                <th>出库仓库</th>
                <th>分拣仓库</th>

                <th id="box_text1"></th>
                <th id="box_text2"></th>
                <th>操作</th>

            </tr>
            </thead>
            <tbody id="search_information_box">

            </tbody>
        </table>
    </table>
</div>
</body>

<script>
    $('.list').on('click','li',function () {
        $("#setOptions").show();
        // $(this).css({'background-color':'#E67A30','font-size':'150%','font-color':'#6BD089'});
        $('.list li').hide();
    });
    $("#sortSearch").on('click',function () {
        var flag=parseInt($(this).val());
        var time=$('input[name="date"]').val();
        var warehouse=$('select[name="warehouse"]').val();
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php/getSortData',
            dataType: 'json',
            cache:false,
            async:false,
            data: {
                'flag': flag,
                'sortStartDate':time,
                'do_warehouse_id':warehouse
            },
            success: function (data) {
                if (parseInt(flag) == 1) {
                    var li='';
                    $.each(data,function (k, v) {
                        li+='<tr>';
                        li+='<td>'+v.deliver_order_id+'<td>';
                        li+='<td>'+v.order_id+'<td>';
                        li+='<td>'+v.title+'<td>';
                        li+='<td>'+v.sortNum+'<td>';
                        li+='<td>'+v.urgent+'<td>';
                        li+='<td>'+v.stageTarget+'<td>';
                        li+='</tr>';
                    });
                    $("#showSortData_"+flag+" tbody").html(li);
                    $("#showSortData_"+flag).show();
                }
            }
        });//查询结束
    });

    function search_information(type) {
        $("#search_informations").show();
        $("#singleList").hide();
        var type = type>0?type:window.search_type;
        window.search_type = type;
        var do_warehouse_id = $("#do_warehouse_id").val();
        var warehouse_id = $("#warehouse_id").val();
        var date_search = $("#before_time").val();
        var repack1_text = '';
        var repack2_text = '';
        var box2_text = '';
        var box_text = '';
        switch(type){
            case  1:
                repack1_text = '分拣数';
                repack2_text = '调拨出库数';
                box2_text = '出库';
                break;
            case  2:
                repack1_text = '调拨出库数';
                repack2_text = '调拨入库数';
                box2_text = '入库';
                break;
            case  3:
                repack1_text = '调拨入库数';
                repack2_text = '投篮数';
                box2_text = '合单';
                break;
        }
        $("#search_type_name").html(box2_text);
        $("#repack_text1").html(repack1_text);
        $("#repack_text2").html(repack2_text);
        $("#box_text1").html(repack1_text);
        $("#box_text2").html(repack2_text);
            $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php/search_information',
            dataType: 'json',
            cache:false,
            async:false,
            data: {
                warehouse_id: warehouse_id,
                do_warehouse_id: do_warehouse_id,
                date_search: date_search,
                search_type: type,
                },
            success: function (response) {

                var jsonData = response;
                if (jsonData.return_code != "SUCCESS") {
                    alert(jsonData.return_msg);
                }
                var repack_product = jsonData.return_data.repack_products;
                var box_product = jsonData.return_data.box_products;
                var dataWarehouse = jsonData.return_data.dataWarehouse;
                var html1 = '';
                var html2 = '';
                $.each(repack_product, function (index1, value1) {
                    html1 += '<tr>';
                    html1 += '<th>' + value1.deliver_order_id + '</th>';
                    html1 += '<th>' + value1.order_id + '</th>';
                    html1 += '<th>' + value1.deliver_date + '</th>';
                    html1 += '<th>' + value1.warehouse_name + '</th>';
                    html1 += '<th>' + value1.do_warehouse_name + '</th>';
                    html1 += '<th>' + value1.ios_num + '</th>';
                    html1 += '<th>' + value1.diff_num + '</th>';
                    html1 += '<th onclick="show_search_info(\'' + index1 + '\')">查看</th>';
                    html1 += '</tr>';
                    html1 += '<tr class = "show_repack_' + index1 + '" style="display: none;"><th>框号</th><th>' + repack1_text + '</th><th>' + repack2_text + '</th></tr>';
                    $.each(value1.containers, function (i1, v1) {
                        html1 += '<tr class = "show_repack_' + index1 + '" style="display: none;">';
                        html1 += '<td>' + v1.container_id + '</td>';
                        html1 += '<td>' + (v1.ios_container > 0 ? 1 : 0) + '</td>';
                        html1 += '<td>' + (v1.diff_container > 0 ? 1 : 0) + '</td>';
                        html1 += '</tr>';
                    });

                });
                $.each(box_product, function (index2, value2) {
                    html2 += '<tr >';
                    html2 += '<th>' + value2.deliver_date + '</th>';
                    html2 += '<th>' + value2.warehouse_name + '</th>';
                    html2 += '<th>' + value2.do_warehouse_name + '</th>';
                    html2 += '<th>' + value2.ios_num + '</th>';
                    html2 += '<th>' + value2.diff_num + '</th>';
                    html2 += '<th onclick="show_search_info(\'' + index2 + '\')">查看</th>';
                    html2 += '</tr>';
                    html2 += '<tr class = "show_repack_' + index2 + '" style="display: none;"><th>商品ID</th><th>商品名称</th><th>' + repack1_text + '</th><th>' + repack2_text + '</th></tr>';
                    $.each(value2.products, function (i2, v2) {
                        html2 += '<tr class = "show_repack_' + index2 + '" style="display: none;">';
                        html2 += '<td>' + v2.product_id + '</td>';
                        html2 += '<td>' + v2.name + '</td>';
                        html2 += '<td>' + (v2.batch_quantity > 0 ? v2.batch_quantity : 0) + '</td>';
                        html2 += '<td>' + (v2.type_quantity > 0 ? v2.type_quantity : 0) + '</td>';
                        html2 += '</tr>';
                    });

                });
                // console.log(html1);
                // console.log(html2);
                $("#search_information_repack").html(html1);
                $("#search_information_box").html(html2);
            }
        });
    }
    function show_search_info(id) {
        $(".show_repack_"+id).each(function () {
                $(this).toggle();
            }
        )
    }
</script>
</html>