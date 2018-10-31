<?php
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=w_dis.php");
    //确保重定向后，后续代码不会被执行
    exit;
}

$warehouseId = isset($_REQUEST['warehouseId']) ? (int)$_REQUEST['warehouseId'] : 0;
/*
 * 查仓库
 * */
$str= [];
$sql="SELECT warehouse_id,shortname FROM oc_x_warehouse WHERE status = 1";
$hos=$db->query($sql)->rows;
foreach ($hos as $k=>$v){
    $str[]=$v['warehouse_id'];
}
$str=implode(' and ',$str);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <title>配送状态</title>
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
            /*-webkit-appearance: none;*/
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
        table tbody {
            display:block;
            height:195px;
            overflow-y:scroll;
            padding:1px 1px 500px 1px;
        }
        table thead, tbody tr {
            display:table;
            width:100%;
            table-layout:fixed;
        }

        table thead {
            /*width: calc( 100% - 1em )*/
        }
        td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            font-size: 0.9rem;
        }

        th{
            /*padding: 0.3em;*/
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.3);
        }
        button{
            background-color: #e6e6e6;
            font-size: 0.9rem;
            cursor: pointer;
            border-radius: 0.3rem;
        }
        .import{/*选中样式*/
            background-color: #afdb76;
        }
        .lessGreen {background-color: #afdb76;}

        .lightGreen {background-color:#d0e9c6;}
        .nobg {background-color:#eeeeee;}

        .linkButton {width: 2.4rem; height: 1.4rem; margin: 0.3rem; padding:0.2rem; font-size: 0.9rem; color:#ffffff; border-radius: 0.2rem; background-color: #DF0000;}
    </style>
</head>
<body>
<div>
    <div style="margin: 0.3rem">
        <strong>开始：</strong><input  name="go_date" size="10px" style="font-size:0.9rem; border:1px solid #378888; padding: 2px;" type="text" value="<?= date('Y-m-d',strtotime('-5 day')); ?>">&emsp;&emsp;
        <strong>结束：</strong><input  name="to_date" size="10px" style="font-size:0.9rem; border:1px solid #378888;  padding: 2px;" type="text" value="<?= date('Y-m-d',strtotime('1 day')); ?>">&emsp;&emsp;
    </div>
    <div style="margin: 0.3rem">
        <strong>目地仓：</strong>
        <select name="to_host" id="">
            <option value="99">全部</option>
            <?php foreach ($hos as $k=>$v): ?>
                <option value="<?=$v['warehouse_id']?>"><?=$v['shortname']?></option>
            <?php endforeach;?>
        </select>
    </div>
    <strong>显示方式：</strong><button class="interval import" shows="fen" type="button" name="fen">单</button>&emsp;&emsp;<button class="interval" shows="good" type="button" name="good">件</button>&emsp;&emsp;&emsp;&emsp;&emsp;
    <button class="linkButton" id="search">搜索</button>
    <hr style="background-color: #FE4E5B">
    <!--表单开始-->
    <table id="scroll_bar" style="min-width: 36rem;">
        <thead>
        <tr id="bar_head">
            <th>配送日期</th>
            <th>配送仓库</th>
            <th class="lessGreen">》未拣完</th>
            <th>未合单数</th>
            <th>已捡完订单</th>
            <th class="lessGreen">》未分配</th>
            <th class="lessGreen">》已分配</th>
            <th class="lessGreen">》配送中</th>
            <th class="lessGreen">》配送完成</th>
            <th class="lessGreen">》配送失败</th>
            <th class="lessGreen">》退货数</th>
        </tr>
        <tr id="content2" style="text-align: center"></tr>

        </thead>
        <tbody class="content" style="text-align: center">
        </tbody>
    </table>
</div>
</body>
<script>
    $(".interval").click(function () {
        $('.interval').removeClass('import');
        $(this).addClass('import');
    });
    /*开始[整]件*/
    $('#search').on('click',(function () {
        $("#content2").html('');
        $(".content").html('');
        var opt = $('.interval.import').attr('shows');
        var go_date = $("input[name='go_date']").val();
        var to_date = $("input[name='to_date']").val();
        var to_host = $("select[name='to_host']").val();
        var ware = <?php echo $warehouseId; ?>;

        /*
        * 再检查时间
        *
        * */
        var _go=$("input[name='go_date']").val();
        var _to=$("input[name='to_date']").val();
        var sArr =_to.split("-");
        var eArr = _go.split("-");
        var sRDate = new Date(sArr[0], sArr[1], sArr[2]);
        var eRDate = new Date(eArr[0], eArr[1], eArr[2]);
        var days = (sRDate-eRDate)/(24*60*60*1000);
        if (days > 10){
            alert('时间的间隔不能超过十天！');
        }else {
            $(".content").html('');
            $("#content2").html('');
            /*开始请求*/
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: 'getAllotState.php',
                data: {
                    go_date: go_date,
                    to_date: to_date,
                    to_host: to_host,
                    opt: opt,
                    ware: ware
                },
                success: function (data) {
                    // console.log(data);

                    var  tr='';
                    var tt='';
                    var unfinished_z=0;
                    var unfinished_s=0;
                    var null_single=0;
                    var count_orderId=0;
                    var null_allot_z=0;
                    var null_allot_s=0;
                    var over_allot_z=0;
                    var over_allot_s=0;
                    var on_allot_z=0;
                    var on_allot_s=0;
                    var achieve_allot_z=0;
                    var achieve_allot_s=0;
                    var allot_fairly_z=0;
                    var allot_fairly_s=0;
                    var sales_return_z=0;
                    var sales_return_s=null;

                    var count_unfinished_oid=0;
                    var null_single=0;
                    var count_orderId=0;
                    var count_null_allot=0;
                    var count_over_allot=0;
                    var count_on_allot=0;
                    var count_achieve_allot=0;
                    var count_allot_fairly=0;
                    var count_sales_return=null;
                    $.each(data,function (key,obj) {
                        if (obj.sales_return_s == undefined) {//按单
                            count_unfinished_oid += parseInt(obj.count_unfinished_oid);
                            null_single += parseInt(obj.null_single);
                            count_orderId += parseInt(obj.count_orderId);
                            count_null_allot += parseInt(obj.count_null_allot);
                            count_over_allot += parseInt(obj.count_over_allot);
                            count_on_allot += parseInt(obj.count_on_allot);
                            count_achieve_allot += parseInt(obj.count_achieve_allot);
                            count_allot_fairly += parseInt(obj.count_allot_fairly);
                            count_sales_return += parseInt(obj.count_sales_return);
                            tr += '<tr>';
                            tr += '<td>' + obj.deliver_date+ '</td>';
                            tr += '<td>' + obj.shortname+ '</td>';
                            tr += '<td>' + obj.count_unfinished_oid+ '</td>';
                            tr += '<td>' + obj.null_single+ '</td>';
                            tr += '<td>' + obj.count_orderId+ '</td>';
                            tr += '<td>' + obj.count_null_allot+ '</td>';
                            tr += '<td>' + obj.count_over_allot+ '</td>';
                            tr += '<td>' + obj.count_on_allot+ '</td>';
                            tr += '<td>' + obj.count_achieve_allot+ '</td>';
                            tr += '<td>' + obj.count_allot_fairly+ '</td>';
                            tr += '<td>' + obj.count_sales_return+ '</td>';
                            tr += '</tr>';
                        }else {//按件

                            unfinished_z += parseInt(obj.unfinished_z);
                            unfinished_s += parseInt(obj.unfinished_s);
                            null_single += parseInt(obj.null_single);
                            count_orderId += parseInt(obj.count_orderId);
                            null_allot_z += parseInt(obj.null_allot_z);
                            null_allot_s += parseInt(obj.null_allot_s);
                            over_allot_z += parseInt(obj.over_allot_z);
                            over_allot_s += parseInt(obj.over_allot_s);
                            on_allot_z += parseInt(obj.on_allot_z);
                            on_allot_s += parseInt(obj.on_allot_s);
                            achieve_allot_z += parseInt(obj.achieve_allot_z);
                            achieve_allot_s += parseInt(obj.achieve_allot_s);
                            allot_fairly_z += parseInt(obj.allot_fairly_z);
                            allot_fairly_s += parseInt(obj.allot_fairly_s);
                            sales_return_z += parseInt(obj.sales_return_z);
                            sales_return_s += parseInt(obj.sales_return_s);
                            tr += '<tr>';
                            tr += '<td>' + obj.deliver_date+ '</td>';
                            tr += '<td>' + obj.shortname+ '</td>';
                            tr += '<td>[整]' + obj.unfinished_z +'<br>'+obj.unfinished_s+'</td>';
                            tr += '<td>' + obj.null_single+ '</td>';
                            tr += '<td>' + obj.count_orderId+ '</td>';
                            tr += '<td>[整]' + obj.null_allot_z +'<br>'+obj.null_allot_s+'</td>';
                            tr += '<td>[整]' + obj.over_allot_z +'<br>'+obj.over_allot_s+'</td>';
                            tr += '<td>[整]' + obj.on_allot_z +'<br>'+obj.on_allot_s+'</td>';
                            tr += '<td>[整]' + obj.achieve_allot_z +'<br>'+obj.achieve_allot_s+'</td>';
                            tr += '<td>[整]' + obj.allot_fairly_z +'<br>'+obj.allot_fairly_s+'</td>';
                            tr += '<td>[整]' + obj.sales_return_z +'<br>'+obj.sales_return_s+'</td>';
                            tr += '</tr>';
                        }
                    });
                    tt += '<td>合计'  + '</td>';
                    tt += '<td>'  + '</td>';

                    if (sales_return_s == undefined) {
                        tt += '<td>'  +count_unfinished_oid+ '</td>';
                        tt += '<td>'  +null_single+ '</td>';
                        tt += '<td>'  +count_orderId+ '</td>';
                        tt += '<td>'  +count_null_allot+ '</td>';
                        tt += '<td>'  +count_over_allot+ '</td>';
                        tt += '<td>'  +count_on_allot+ '</td>';
                        tt += '<td>'  +count_achieve_allot+ '</td>';
                        tt += '<td>'  +count_allot_fairly+ '</td>';
                        tt += '<td>'  +count_sales_return+ '</td>';
                    }else {
                        tt += '<td>[整]' + unfinished_z+'<br>'+unfinished_s+ '</td>';
                        tt += '<td>' +null_single+ '</td>';
                        tt += '<td>' +count_orderId+ '</td>';
                        tt += '<td>[整]' + null_allot_z+'<br>'+null_allot_s+ '</td>';
                        tt += '<td>[整]' + over_allot_z+'<br>'+over_allot_s+ '</td>';
                        tt += '<td>[整]' + on_allot_z+'<br>'+on_allot_s+ '</td>';
                        tt += '<td>[整]' + achieve_allot_z+'<br>'+achieve_allot_s+ '</td>';
                        tt += '<td>[整]' + allot_fairly_z+'<br>'+allot_fairly_s+ '</td>';
                        tt += '<td>[整]' + sales_return_z+'<br>'+sales_return_s+ '</td>';


                    }
                    $("#content2").html(tt);
                    $(".content").html(tr);


                },
                error: function (err) {
                    $(".content").html('');
                    $("#content2").html('');
                }

            });
        }
    }));
</script>
</html>