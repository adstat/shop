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
            text-align: center;
            border:1px solid #008855;
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
        #listData table caption{text-align: left; padding:0.3rem;}
        #listData table {width: 100%}
        .list{
            display :flex;
            display:-webkit-flex;
            align-items:center;
            -webkit-align-items:center;
            justify-content:center ;
            flex-direction: column;
        }
        .list li{
            display: -webkit-flex;
            display: flex;
            background-color: lightgrey;
            width: 75px;
            height: 75px;
            margin: auto;
            float:left;list-style:none;
            display:inline;
            width:178px;
            height:30px;
            line-height:30px;
            border:1px solid #ccc;
            margin:5px;
            text-align:center;
            cursor: pointer;
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
        a{
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
        .hide{display: none;}


        .f08rem{font-size: 0.8rem;}
        .f09rem{font-size: 0.9rem;}

        .hide{display: none;}



        .lessGreen {background-color: #afdb76;}

        .lightGreen {background-color:#d0e9c6;}
        .nobg {background-color:#eeeeee;}
        .linkButton {width: 2.4rem; height: 1.4rem; margin: 0.3rem; padding:0.2rem; font-size: 0.9rem; color:#ffffff; border-radius: 0.2rem; background-color: #DF0000;}
    </style>

</head>


<body>
<script>
    // window.search_type=0;
    // $(document).ready(function () {
    //     var flag=0;
    //     $.ajax({
    //         type: 'POST',
    //         url: 'getAbnormalData.php/getWarehouse',
    //         dataType: 'json',
    //         data: {'flag': flag},
    //         success: function (data) {
    //             var li='';
    //             $.each(data,function (k, v) {
    //                 li+='<option value="'+v.warehouse_id+'">'+v.warehouse+'</option>';
    //             })
    //             $("select[name='warehouse']").append(li);
    //         }
    //     });//查询结束
    // });
</script>
<!--<div style="background-color: #FFFFFF;height:2.6rem; width: 100%">-->
<!--    <div align="left" style="float: left; margin: 0.5rem;">-->
<!--        <img src="view/image/logo.png" align="absmiddle" style="width:5rem; "/>-->
<!--    </div>-->
<!--    <div align="right">-->
<!--        <button class="linkButton" style="display: inline;float:left;" onclick="window.location.href='i.php?auth=xsj2015inv&ver=db'">菜单</button>-->
<!--        <button class="linkButton" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>-->
<!--        <button class="linkButton" onclick="javascript:logout_inventory_user();">退出</button><br />-->
<!---->
<!--    </div>-->
<!--</div>-->
<!--<button class="linkButton" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button><br /><hr />-->
<div id="showLocal">
    <a href="javascript:history.go(-1)">后退到首页</a>
</div>
<div>

    <ul class="list">

        <li id="sorting_product">分拣</li>
        <li onclick="search_information(1)">出库</li>
        <li onclick="search_information(2)">入库</li>
        <li onclick="search_information(3)">合单</li>
        <li>配送</li>
        <li>回库退货</li>
        <li>退货上架</li>
<!--        <li>回款</li>-->
    </ul>
</div>
<div id="setOptions" hidden >
    <button type="button" class="links">后退</button><button type="button" class="position"></button>
    <p id="do_warehouse"  style="text-align: center"><strong>分拣仓库:</strong><select class="select" name="do_warehouse"><?php foreach($dataWarehouse as $value){echo '<option value="'.$value['warehouse_id'].'">'.$value['warehouse'].'</option>';} ?></select></p>
    <p id="warehouse"  style="text-align: center"><strong>目地仓库:</strong><select class="select" name="warehouse"><?php foreach($dataWarehouse as $value){echo '<option value="'.$value['warehouse_id'].'">'.$value['warehouse'].'</option>';} ?></select></p>
    <p id="sortDate"  style="text-align: center"><strong>分拣日期:</strong><input type="text" name="date" value="<?=date("Y-m-d",strtotime("-1 day")) ?>"></p>
    <p id="outDate"  style="text-align: center"><strong>出库日期:</strong><input type="text" name="outDate" value="<?=date("Y-m-d",strtotime("-1 day")) ?>"></p>
</div>
<hr>
<!--<div class="container">-->
<!--    <table id="showSortData_1" hidden class="table">-->
<!--    </table>-->
<!--</div>-->
<!--<div class="container-fluid">-->
<!--    <table class="table" id="showSortData_2" hidden>-->
<!--        <thead>-->
<!--        <tr>-->
<!--            <th>订单数量</th>-->
<!--            <th>抽查数量</th>-->
<!--            <th>抽查人</th>-->
<!--            <th>抽查日期</th>-->
<!--            <th>操作</th>-->
<!--        </tr>-->
<!--        </thead>-->
<!--        <tbody id="showTbody">-->
<!--        </tbody>-->
<!--    </table>-->
<!--</div>-->



<!--    <table  style="margin-top: 5rem; height: 100px; overflow: hidden;">-->
<!--未分拣订单-->
<div>
    <table style="margin-top: 5rem; height: 100px; overflow: hidden;" id="showSortData_1" hidden>
        <div id="data-area">
            <ul>　　　　　　　　　　　　　　　　<!--这里添加分页数据-->　　　　　　　　　　　　　　</ul>
        </div>
        <thead>
        <tr>
            <th>分拣日期</th>
            <th>分拣单号</th>
            <th>订单号</th>
            <th>目地仓库</th>
            <th>所属仓库</th>
            <th>数量</th>
            <th>加急</th>
            <th>深度</th>
            <th>配送日期</th>
            <th>配送状态</th>
            <th>订单状态</th>
            <th>分拣框号</th>
        </tr>
        </thead>
        <tbody>
        </tbody>

    </table>
    <div><p id="pageBar"></p></div>
</div>


<!--抽查问题列表-->
<table style="margin-top: 1.5rem; height: 100px; overflow: hidden;" id="showSortData_2" hidden>
    <thead>
        <tr>
            <th>订单号</th>
            <th>抽查日期</th>
            <th>抽查人员</th>
            <th>分拣数量</th>
            <th>抽查数量</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
<table id="showInfo" hidden class="table">
    <thead>
    <tr>
        <th>订单号</th>
        <th>分拣数量</th>
        <th>最终数量</th>
        <th>货位号</th>
        <th>分拣异常</th>
    </tr>
    </thead>
    <tbody>

    </tbody>
</table>
<!--结束抽查问题列表-->
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
            <tbody>

            </tbody>
        </table>

<table id="logisticAllot" hidden>
    <thead>
    <tr>
        <th>订单号</th>
        <th>货位号</th>
        <th>DB人员</th>
        <th>DB区域</th>
        <th>客户ID</th>
        <th>配送日期</th>
        <th>配送人员</th>
        <th>加急</th>
        <th>深度</th>
        <th>滞留天数</th>
    </tr>
    </thead>
    <tbody>

    </tbody>
</table>
<table id="evaluate" hidden>
    <p hidden id="evaluateCount"></p>
    <thead>
    <tr>
        <th>订单号</th>
        <th>配送司机ID</th>
        <th>配送司机姓名</th>
        <th>评价总分</th>
        <th>配送日期</th>
        <th>加急</th>
        <th>深度</th>
    </tr>
    </thead>
    <tbody>

    </tbody>
</table>
<table id="returnPutaway" hidden>
<!--        <span>退货总数:</span><button class="button" id="returnPutawayNum" ></button>-->
<!--        <span>框子总数:</span><button class="button" id="returnPutawayBoxNum" ></button>-->
    <thead>
    <tr>
        <th>日期</th>
        <th>退货数</th>
        <th>上架数</th>
        <th>操作</th>
<!--        <th>查看</th>-->
    </tr>
    </thead>
    <tbody id="show_returnPutaway">

    </tbody>
</table>
<table id="returnContainer" hidden>
    <p hidden id="containerCount"></p>
    <thead>
    <tr>
        <th>配送单ID</th>
        <th>司机姓名</th>
        <th>出筐</th>
        <th>回收筐</th>
        <th>强制回收筐</th>
        <th>其它回收筐</th>
    </tr>
    </thead>
    <tbody>

    </tbody>
</table>
<table id="returnCity" hidden>
    <thead>
    <tr>
        <th>订单号</th>
        <th>退货数</th>
        <th>上架数</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>

    </tbody>
</table>
<table id="returnCityAllot" hidden>
    <p id="show_returnCityAllot"></p>
    <thead>
    <tr>
        <th>退货日期</th>
        <th>退货单号</th>
        <th>调拨单号</th>
        <th>订单号</th>
        <th>商品ID</th>
        <th>分拣数</th>
        <th>调拨出库数</th>
        <th>退货数</th>
        <th>分拣框</th>
        <th>其它调拨单</th>
        <th>调拨仓库</th>
    </tr>
    </thead>
    <tbody id="returnCityBody">

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
            <button type="button" class="links">后退</button><button type="button" class="position"></button>
            <p><strong>分拣仓库</strong><select class="select" id="do_warehouse_id"> <?php foreach($dataWarehouse as $value){  if($_COOKIE['warehouse_id']==$value['warehouse_id']){ $aaaa = 'selected'; } else { $aaaa = ''; }  echo '<option value="'.$value['warehouse_id'].'" '.$aaaa.'>'.$value['warehouse'].'</option>';} ?></select></p>
            <p><strong>出库仓库</strong><select class="select" id="warehouse_id"><?php foreach($dataWarehouse as $value){ if($_COOKIE['warehouse_id']==$value['warehouse_id']){ $aaaa = 'selected'; } else { $aaaa = ''; }   echo '<option value="'.$value['warehouse_id'].'" '.$aaaa.'>'.$value['warehouse'].'</option>';} ?></select></p>
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
            <button type="button" onclick="Javascript:search_information()">搜索</button>|<button hidden id="show_nullSingle" type="button" onclick="nullSingle(this.value);" value="6">已入库未合单货未合齐</button>
        </div>
        <strong id="search_type_name">出库</strong>
        <table id="showNullSingle" hidden>
            <caption>已入库未合单和货未合齐</caption>
            <thead>
            <tr>
                <th>分拣整件</th>
                <th>分拣散件</th>
                <th>投篮整件</th>
                <th>投篮散件</th>
                <th>仓库</th>
                <th>加急</th>
                <th>深度</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <table>
            <caption>周转筐</caption>
            <thead>
            <tr>
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
        <hr />

    </table>
</div>
<!--<div class="footer" style="text-align: center"><h1>备注:</h1><strong><br>加急指的是货物加急送到客户 <br>深度指的是公司的重要客户</strong></div>-->
</body>

<script>
    $('.list li').on('click',function () {
        var idea='<strong>当前位置:</strong>';
        var item=$(this).text();
        $("#showLocal").hide();

        $('.position').html(idea+item);
        if (item == '配送') {
            $("table").hide();
            $('.list li').hide();
            $("#setOptions").show();
            $("#sortDate ").hide();
            $("#do_warehouse ").hide();
            var html='';
            html+='<p style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="logisticAllot(this.value);" type="button" value="7">滞留订单</button>&nbsp;&nbsp;&nbsp;&nbsp;<button  onclick="getEvaluate(this.value);" type="button" value="8">差评</button>&nbsp;&nbsp;&nbsp;&nbsp;</p>';
            $("#setOptions").append(html);
        }
        if (item == '回库退货') {
            $("table").hide();
            $('.list li').hide();
            $("#setOptions").show();
            $("#do_warehouse").hide();
            $("#sortDate").hide();
            var html='';
            html+='<p style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="returnPutAways(this.value);" type="button" value="9">退货上架</button>&nbsp;&nbsp;&nbsp;&nbsp;<button  onclick="getContainer(this.value);" type="button" value="10">回框准确率</button>&nbsp;&nbsp;&nbsp;&nbsp;</p>';
            $("#setOptions").append(html);
        }
        if (item == '退货上架') {
            $("table").hide();
            $('.list li').hide();
            $("#setOptions").show();
            $("#do_warehouse").hide();
            $("#sortDate").hide();
            var html='';
            html+='<p style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="returnCity(this.value);" type="button" value="11">城市仓系统未操作上架</button>&nbsp;&nbsp;&nbsp;&nbsp;<button  onclick="returnCityAllot(this.value);" type="button" value="12">城市仓调拨退货回总仓</button>&nbsp;&nbsp;&nbsp;&nbsp;</p>';
            $("#setOptions").append(html);
        }
    });
    $(".links").on('click',function () {
        location.reload();
    });

    $('#sorting_product').click(function () {
        $("#setOptions").show();
        $("#warehouse").hide();
        $("#outDate").hide();
        var html='';
        html+='<p style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;<button  onclick="pages(1)" type="button" value="1">未分拣订单</button>&nbsp;&nbsp;&nbsp;&nbsp; <button onclick="sortSearch(this.value)" type="button" value="2">抽查问题列表</button>&nbsp;&nbsp;&nbsp;&nbsp;</p>';
        $("#setOptions").append(html);

        // $(this).css({'background-color':'#E67A30','font-size':'150%','font-color':'#6BD089'});
        $('.list li').hide();
    });
    function returnCity(flag) {
        var flag=11;
        $("table").hide();
        $("#returnCity").show();
        var warehouse=$('select[name="warehouse"]').val();
        var outDate=$('input[name="outDate"]').val();


        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            dataType: 'json',
            async:false,
            cache:false,
            data: {
                'flag': flag,
                'warehouse_id': warehouse,
                'sortStartDate': outDate,
            },
            success: function (data) {
                console.log(data);
                var li='';
                var tr='';
                $.each(data,function (k,v) {

                    li+='<tr>';
                    li+='<td>'+v.date_added+'</td>';
                    li+='<td>'+v.return_num+'</td>';
                    li+='<td>'+v.shelf_num+'</td>';
                    // li+='<td>'+v.warehouse_id+'</td>';
                    li+='<td id="returnCity_'+v.date_added+v.warehouse_id+'"><button onclick="showreturnCity(\'returnCity_'+v.date_added+v.warehouse_id+'\');">查看</button></td>';
                    li+='</tr>';
                    if (v.product) {
                        $.each(v.product,function (k1, v1) {
                            if (parseInt(v.date_added) == parseInt(v1['date_added']) && parseInt(v.warehouse_id) == parseInt(v1['warehouse_id'])) {
                                li+='<tr class=\"returnCity_' + v.date_added+v.warehouse_id+'\" hidden >';
                                li+='<td colspan="8" class="f08rem"  style="padding-bottom: 1rem">';
                                li+='订单:<b>'+v1.order_id+'</b>ID:'+v1.product_id+'商品:'+v1.name+'货位号:['+v1.stock_area+']框数:'+v1.box_size+'退货数:'+v1.return_quantity+'上架数:'+v1.self_quantity+'退货仓库:'+v1.doWarehouse;
                                li+='</td>';
                                li+='</tr>';
                            }
                        });
                    }
                });
                $("#returnCity tbody").html(li);


            }
        });//查询结束


    }
    function viewInfo(str) {
        // $("#"+str).toggle();
        $("."+str).each(function () {
            $(this).toggle();
        });
    }
    function showreturnCity(str) {
        $("."+str).each(function () {
            $(this).toggle();
        });

        // $("#"+str).toggle();

    }
    function returnCityAllot(flag) {
        $("table").hide();
        $("#returnCityAllot").show();
        var warehouse=$('select[name="warehouse"]').val();
        var outDate=$('input[name="outDate"]').val();
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            dataType: 'json',
            data: {
                'flag': flag,
                'warehouse_id': warehouse,
                'sortStartDate': outDate,
            },
            success: function (data) {
                console.log(data);
                var li='';
                $.each(data,function (k,v) {
                    li+='<tr>';
                    li+='<td>'+v.rdbData+'</td>';
                    li+='<td>'+v.return_id+'</td>';
                    li+='<td>'+v.relevant_id+'</td>';
                    li+='<td>'+v.order_id+'</td>';
                    li+='<td>'+v.product_id+'</td>';
                    li+='<td>'+v.deliverNum+'</td>';
                    li+='<td>'+v.wrtNum+'</td>';
                    li+='<td>'+v.returnNum+'</td>';
                    li+='<td>'+v.useContainer+'</td>';
                    li+='<td>'+v.otherRelevantId+'</td>';
                    li+='<td>'+v.from_warehouse+'</td>';
                    li+='</tr>';
                });
                // var tr='';
                // tr+='分拣总数:<strong>'+data['item']['sortCount']+'</strong><br>';
                // tr+='分拣总数:<strong>'+data['item']['returnCount']+'</strong><br>';
                // $("#show_returnCityAllot").html(tr);
                $("#returnCityAllot tbody").html(li);


            }
        });//查询结束
    }
    function getContainer(flag) {
        $("table").hide();
        $("#returnContainer").show();
        var warehouse=$('select[name="warehouse"]').val();
        var outDate=$('input[name="outDate"]').val();
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            dataType: 'json',
            data: {
                'flag': flag,
                'warehouse_id': warehouse,
                'sortStartDate': outDate,
            },
            success: function (data) {
                console.log(data['agv']);
                var li='';
                $.each(data['base'],function (k,v) {
                    li+='<tr>';
                    li+='<td>'+v.logistic_allot_id+'</td>';
                    li+='<td>'+v.logistic_driver_title+'</td>';
                    li+='<td>'+v.plan_count+'</td>';
                    li+='<td>'+v.have_count+'</td>';
                    li+='<td>'+v.admin_count+'</td>';
                    li+='<td>'+v.out_plan+'</td>';
                    li+='</tr>';
                });

                $("#returnContainer tbody").html(li);
                var p='';
                p+='<strong>占比</strong>'+data['avg']+'<span>%</span>';
                p+='<strong>出筐总数:</strong><span>'+data['plan_count']+'</span>';
                p+='<strong>回筐总数:</strong><span>'+data['count']+'</span>';
                $("#containerCount").html(p);
                $("#containerCount").show();
            }
        });//查询结束
    }
    function logisticAllot(flag) {
        $("table").hide();
        $("#logisticAllot").show();
        var warehouse=$('select[name="warehouse"]').val();
        var outDate=$('input[name="outDate"]').val();
        var flag=7;
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            dataType: 'json',
            data: {
                'flag': flag,
                'warehouse_id': warehouse,
                'sortStartDate': outDate,
            },
            success: function (data) {
                console.log(data);
                var li='';
                $.each(data,function (k,v) {
                    li+='<tr>';
                    li+='<td>'+v.order_id+'</td>';
                    li+='<td>'+v.stockId+'</td>';
                    li+='<td>'+v.bd_name+'</td>';
                    li+='<td>'+v.bd_area+'</td>';
                    li+='<td>'+v.customer_id+'</td>';
                    li+='<td>'+v.logisticDate+'</td>';
                    li+='<td>'+v.logisticPer+'</td>';
                    li+='<td>'+v.is_urgent+'</td>';
                    li+='<td>'+v.is_stage_target+'</td>';
                    li+='<td>'+v.diffDates+'</td>';
                    li+='</tr>';
                });

                $("#logisticAllot tbody").html(li);


            }
        });//查询结束
    }
    function returnPutAways(flag) {
        $("table").hide();
        $("#returnPutaway").show();
        var flag=9;
        var warehouse=$('select[name="warehouse"]').val();
        var outDate=$('input[name="outDate"]').val();
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            dataType: 'json',
            async:false,
            cache:false,
            data: {
                'flag': flag,
                'warehouse_id': warehouse,
                'sortStartDate': outDate,
            },
            success: function (data) {
                console.log(data);
                var li='';
                $.each(data,function (k,v) {

                    li+='<tr>';
                    li+='<td>'+v.date_added+'</td>';
                    li+='<td>'+v.return_num+'</td>';
                    li+='<td>'+v.shelf_num+'</td>';
                    // li+='<td>'+v.warehouse_id+'</td>';
                    li+='<td id="returnPutaway_'+v.date_added+v.warehouse_id+'"><button onclick="showreturnPutaway(\'returnPutaway_'+v.date_added+v.warehouse_id+'\');">查看</button></td>';
                    li+='</tr>';
                    if (v.product) {
                        $.each(v.product,function (k1, v1) {
                            if (parseInt(v.date_added) == parseInt(v1['date_added']) && parseInt(v.warehouse_id) == parseInt(v1['warehouse_id'])) {
                                li+='<tr class=\"returnPutaway_' + v.date_added+v.warehouse_id+'\" hidden >';
                                li+='<td colspan="8" class="f08rem"  style="padding-bottom: 1rem">';
                                li+='订单:<b>'+v1.order_id+'</b>ID:'+v1.product_id+'商品:'+v1.name+'货位号:['+v1.stock_area+']框数:'+v1.box_size+'退货数:'+v1.return_quantity+'上架数:'+v1.self_quantity+'退货仓库:'+v1.doWarehouse;
                                li+='</td>';
                                li+='</tr>';
                            }
                        });
                    }
                });
                $("#returnPutaway tbody").html(li);


            }
        });//查询结束

    }
    function showreturnPutaway(str) {
        $("."+str).each(function () {
            $(this).toggle();
        });
    }
    function viewInfo(val) {
        // alert(val);
        $("#show_"+val).show();
    }
    function getEvaluate(flag) {
        $("table").hide();
        $("#evaluate").show();
        var outDate=$('input[name="outDate"]').val();
        var warehouse_id=$('select[name="warehouse"]').val();
        // alert(flag);
        flag=8;
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            dataType: 'json',
            data: {
                'flag': flag,
                'sortStartDate': outDate,
                'warehouse_id': warehouse_id,
            },
            success: function (data) {
                // console.log(data['count']['driver_score']);
                var li='';
                $.each(data['base'],function (k,v) {
                    li+='<tr>';
                    li+='<td>'+v.order_id+'</td>';
                    li+='<td>'+v.logistic_driver_id+'</td>';
                    li+='<td>'+v.logistic_driver_title+'</td>';
                    li+='<td>'+v.driver_score+'</td>';
                    li+='<td>'+v.date_added+'</td>';
                    li+='<td>'+v.is_urgent+'</td>';
                    li+='<td>'+v.is_stage_target+'</td>';
                    li+='</tr>';
                });

                $("#evaluate tbody").html(li);
                var p='';
                p+='<strong>总分数</strong>'+data['idea']['count'];
                p+='<strong>平均分</strong>'+data['idea']['avg'];
                p+='<strong>总单数</strong>'+data['idea']['countNum'];
                $("#evaluateCount").html(p);
                $("#evaluateCount").show();


            }
        });//查询结束
    }
    //抽查问题列表
    function sortSearch(flag){
        var flag=2;
        $("#pageBar").hide();
        $("#showSortData_1").hide();
        var time=$('input[name="date"]').val();
        var warehouse=$('select[name="do_warehouse"]').val();
            $("#showSortData_2").show();
            $.ajax({
                type: 'POST',
                url: 'getAbnormalData.php',
                dataType: 'json',
                data: {
                    'flag': flag,
                    'sortStartDate':time,
                    'do_warehouse_id':warehouse
                },
                success: function (data) {
                    console.log(data);
                    var li='';
                    $.each(data,function (k,v) {
                        li+='<tr>';
                        li+='<td>'+v.order_id+'</td>';
                        li+='<td>'+v.date_added+'</td>';
                        li+='<td>'+v.title+'</td>';
                        li+='<td>'+v.sorting_quantity+'</td>';
                        li+='<td>'+v.final_quantity+'</td>';
                        li+='<td id="sortSearch_'+v.check_location_id+v.order_id+'" style="cursor: pointer" onclick="getSortSearch(\'sortSearch_' + v.check_location_id+v.order_id+ '\')">查看</td>';
                        li+='</tr>';
                        if (v.product) {
                            $.each(v.product,function (k1, v1) {
                                if (parseInt(v.check_location_id) == parseInt(v1['check_location_id']) && parseInt(v.order_id) == parseInt(v1['order_id'])&& v.title == v1['title']) {
                                    li+='<tr class="sortSearch_' + v.check_location_id+v.order_id+ '" hidden>';
                                    li+='<td colspan="8" class="f08rem"  style="padding-bottom: 1rem">';
                                    li+='抽查商品:<b>'+v1['product_id']+'</b>-'+v1['name']+'<b>['+v1['title']+']<b>['+v1['repack']+']-[抽'+v1['final_quantity']+']-[分'+v1['sorting_quantity']+']</b>';
                                    li+='</td>';
                                    li+='</tr>';
                                }
                            });
                        }

                    });
                    $("#showSortData_2 tbody").html(li);

                }
            });//查询结束
    }
    function getSortSearch(str) {
        $("."+str).each(function () {
            $(this).toggle();
        });
    }
    function findOne() {
        $("#showInfo").show();
        var time=$('input[name="date"]').val();
        var warehouse=$('select[name="do_warehouse"]').val();
        var flag=5;

        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            dataType: 'json',
            data: {
                'flag': flag,
                'sortStartDate':time,
                'do_warehouse_id':warehouse
            },
            success: function (data) {
                var li='';
                $.each(data,function (k,v) {
                    li+='<tr>';
                    li+='<td>'+v.order_id+'</td>';
                    li+='<td>'+v.sorting+'</td>';
                    li+='<td>'+v.final+'</td>';
                    li+='<td>'+v.inv_class_sort+'</td>';
                    li+='<td>'+v.isFinal+'</td>';
                    li+='</tr>';
                });

                $("#showInfo tbody").html(li);

            }
        });//查询结束
    }
    function pages(page)
    {
        $("table").hide();
        $('.list li').hide();
        $("#setOptions").show();
        $("#warehouse").hide();
        $("#outDate").hide();
        // $("#sortDate").hide();

        var time=$('input[name="date"]').val();
        var warehouse=$('select[name="do_warehouse"]').val();
        var flag=1;
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            data: {
                'flag': flag,
                'sortStartDate':time,
                'do_warehouse_id':warehouse,
                'pageNum':page,
            },
            dataType: 'json',
            beforeSend: function() {
                $("#data-area ul").append("加载中...");
            },
            success: function(json) {
                console.log(json);
                $("#data-area ul").empty();
                totalItem = json.totalItem;
                pageSize = json.pageSize;
                curPage = page;
                totalPage = json.totalPage;
                var data_content = json.data_content;
                var data_html = "";
                var li='';
                li+='';

                $.each(data_content,function (k,v) {
                    li+='<tr>';
                    li+='<td>'+v.date_added+'</td>';
                    li+='<td>'+v.deliver_order_id+'</td>';
                    li+='<td>'+v.order_id+'</td>';
                    li+='<td>'+v.shortname+'</td>';
                    li+='<td>'+v.warehouseZS+'</td>';
                    li+='<td>'+v.sortNum+'</td>';
                    li+='<td>'+v.urgent+'</td>';
                    li+='<td>'+v.stageTarget+'</td>';
                    li+='<td>'+v.deliver_date+'</td>';
                    li+='<td>'+v.odsStatu+'</td>';
                    li+='<td>'+v.osStatu+'</td>';
                    li+='<td>'+v.container_id+'</td>';
                    li+='</tr>';
                });

                $("#showSortData_"+flag+" tbody").html(li);


                $("#data-area ul").html(data_html);
                $("#showSortData_1").show();
            },
            complete: function() {
                getPageBar();
            },
            error: function() {
                alert("数据加载失败");
            }
        });
    }

    function getPageBar()
    {
        if(curPage > totalPage) {
            curPage = totalPage;
        }
        if(curPage < 1) {
            curPage = 1;
        }

        pageBar = "";


        if(curPage != 1){
            pageBar += "<span class='pageBtn'><a href='javascript:pages(1)'>首页</a></span>";
            pageBar += "<span class='pageBtn'><a href='javascript:pages("+(curPage-1)+")'><<</a></span>";
        }


        var start,end;
        if(totalPage <= 5) {
            start = 1;
            end = totalPage;
        } else {
            if(curPage-2 <= 0) {
                start = 1;
                end = 5;
            } else {
                if(totalPage-curPage < 2) {
                    start = totalPage - 4;
                    end = totalPage;
                } else {
                    start = curPage - 2;
                    end = curPage + 2;
                }
            }
        }

        for(var i=start;i<=end;i++) {
            if(i == curPage) {
                pageBar += "<span class='pageBtn-selected'><a href='javascript:pages("+i+")'>"+i+"</a></span>";
            } else {
                pageBar += "<span class='pageBtn'><a href='javascript:pages("+i+")'>"+i+"</a></span>";
            }
        }


        if(curPage != totalPage){
            pageBar += "<span class='pageBtn'><a href='javascript:pages("+(parseInt(curPage)+1)+")'>>></a></span>";
            pageBar += "<span class='pageBtn'><a href='javascript:pages("+totalPage+")'>尾页</a></span>";
        }

        $("#pageBar").html(pageBar);
    }

    function search_information(type) {
        //position
        // var str=$(this).text;
        // console.log(str);
        $("#search_informations").show();
        $("#setOptions").hide();
        $('.list li').hide();
        
        var type = type>0?type:window.search_type;
        if (parseInt(type) == 3) {
            $("#show_nullSingle").show();
            $("#showNullSingle").show();
        }else {
            $("#show_nullSingle").hide();
        }
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
                repack1_text = '分拣框数';
                repack2_text = '调拨出库框数';
                box2_text = '调拨出库';
                relevantOrderId = '调拨单号';
                relevantNum= '调拨单号';
                deliverOrderId = '分拣单号';
                break;
            case  2:
                repack1_text = '调拨出库数';
                repack2_text = '调拨入库数';
                box2_text = '入库';
                relevantOrderId = '调拨单号';
                deliverOrderId = '分拣单号';
                relevantNum= '调拨单号';
                break;
            case  3:
                repack1_text = '调拨入库数';
                repack2_text = '投篮数';
                box2_text = '合单';
                relevantOrderId = '调拨单号';
                deliverOrderId = '分拣单号';
                relevantNum= '调拨单号';
                break;
        }
        $("#search_type_name").html(box2_text);
        $("#repack_text1").html(repack1_text);
        $("#repack_text2").html(repack2_text);
        $("#box_text1").html(repack1_text);
        $("#box_text2").html(repack2_text);
            $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            dataType: 'json',
            cache:false,
            async:false,
            data: {
                warehouse_id: warehouse_id,
                do_warehouse_id: do_warehouse_id,
                date_search: date_search,
                search_type: type,
                flag:3,
                },
            success: function (response) {
                console.log(response);

                var jsonData = response;
                if (jsonData.return_code != "SUCCESS") {
                    alert(jsonData.return_msg);
                    return true;
                }
                var repack_product = jsonData.return_data.repack_products;
                var box_product = jsonData.return_data.box_products;
                var dataWarehouse = jsonData.return_data.dataWarehouse;
                var html1 = '';
                var html2 = '';
                console.log(repack_product);
                $.each(repack_product, function (index1, value1) {
                    html1 += '<tr>';
                    html1 += '<th>' + value1.order_id + '</th>';
                    html1 += '<th>' + value1.deliver_date + '</th>';
                    html1 += '<th>' + value1.warehouse_name + '</th>';
                    html1 += '<th>' + value1.do_warehouse_name + '</th>';
                    html1 += '<th>' + value1.ios_num + '</th>';
                    html1 += '<th>' + value1.diff_num + '</th>';
                    html1 += '<th onclick="show_search_info(\'' + index1 + '\')">查看</th>';
                    html1 += '</tr>';
                    html1 += '<tr class = "show_repack_' + index1 + '" style="display: none;background-color: #bee5eb;">';
                    html1 += '<td>分拣框号</td><td>' + repack1_text + '</td><td>' + repack2_text + '</td><td>' +deliverOrderId+ '</td><td>' +relevantOrderId+ '</td>';
                    html1 += '</tr>';
                    $.each(value1.containers, function (i1, v1) {
                        html1 += '<tr class = "show_repack_' + index1 + '" style="display: none;background-color: #bee5eb;">';
                        html1 += '<td>' + v1.container_id + '</td>';
                        html1 += '<td>' + (v1.ios_container > 0 ? 1 : 0) + '</td>';
                        html1 += '<td>' + (v1.diff_container > 0 ? 1 : 0) + '</td>';
                        html1 += '<td>' + v1.deliver_order_id + '</td>';
                        html1 += '<td>' + v1.relevant_id + '</td>';
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
                    html2 += '<tr class = "show_repack_' + index2 + '" style="display: none;background-color: #bee5eb;">';
                    html2 += '<td>商品ID</td><td>商品名称</td><td>' + repack1_text + '</td><td>' + repack2_text + '</td><td>' + relevantNum + '</td>';
                    html2 += '</tr>';
                    $.each(value2.products, function (i2, v2) {
                        html2 += '<tr class = "show_repack_' + index2 + '" style="display: none;background-color: #bee5eb;">';
                        html2 += '<td>' + v2.product_id + '</td>';
                        html2 += '<td>' + v2.name + '</td>';
                        html2 += '<td>' + (v2.batch_quantity > 0 ? v2.batch_quantity : 0) + '</td>';
                        html2 += '<td>' + (v2.type_quantity > 0 ? v2.type_quantity : 0) + '</td>';
                        html2 += '<td>' + v2.relevantNum + '</td>';

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
    function nullSingle(flag) {
        var do_warehouse_id = $("#do_warehouse_id").val();
        var warehouse_id = $("#warehouse_id").val();
        var gap=$('select[name="gap"]').val();
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php',
            dataType: 'json',
            cache: false,
            async: false,
            data: {
                warehouse_id: warehouse_id,
                do_warehouse_id: do_warehouse_id,
                date_search: gap,
                flag: flag,
            },
            success:function (data) {
                console.log(data);
                var li='';
                if (nullSingle.length > 0) {
                    $.each(nullSingle,function (k,v) {
                        li+='<tr>';
                        li+='<td>'+v.sortZ+'</td>';
                        li+='<td>'+v.sortS+'</td>';
                        li+='<td>'+v.shootZ+'</td>';
                        li+='<td>'+v.shootS+'</td>';
                        li+='<td>'+v.is_urgent+'</td>';
                        li+='<td>'+v.is_stage_target+'</td>';
                        li+='</tr>';
                    });
                    $("#showNullSingle tbody").html(li);
                }else {
                    alert('暂无数据!');
                }

            }
        });
    }

</script>
<style>
    #pageBar {
        text-align: right;
        padding: 0 20px 20px 0;
    }
    .pageBtn a {
        display: inline-block;
        border: 1px solid #aaa;
        padding: 2px 5px;
        margin : 0 3px;
        font-size: 13px;
        background: #ECECEC;
        color: black;
        text-decoration: none;
        -moz-border-radius: 2px;
        -webkit-border-radius: 3px;
    }
    .pageBtn-selected a {
        display: inline-block;
        border: 1px solid #aaa;
        padding: 2px 5px;
        margin : 0 3px;
        font-size: 13px;
        background: #187BBD;
        color: white;
        text-decoration: none;
        -moz-border-radius: 2px;
        -webkit-border-radius: 3px;
    }
    .pageBtn a:hover {
        background: #187BBD;
        color: white;
    }
    .footer{

        height: 100px;

        width: 100%;

        background-color: #dddddd;

        position: fixed;

        bottom: 0;

    }
</style>
</html>