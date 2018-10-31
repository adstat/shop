<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/15
 * Time: 16:35
 */

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


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <title>调拨进程</title>
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
        <strong>调拨单类型：</strong>
        <select name="allocation">
            <option value="1">DO调拨</option>
            <option value="3">仓内调拨</option>
            <option value="2">仓间调拨</option>
            <option value="4">退货调拨</option>
            <option value="5">异常收货</option>
        </select>
    </div>
    <div style="margin: 0.3rem">
        <div class="out_host">
            <strong>出库仓：</strong>
            <select name="out_host" id="">
                <?php foreach ($hos as $ho): ?>
                    <option value="<?=$ho['warehouse_id']?>" <?php if($ho['warehouse_id'] == 12){ echo 'selected';} ?>><?=$ho['shortname']?></option>
                <?php endforeach; ?>
            </select>
        </div><div class="entry_host">
            <strong>入库仓：</strong>
            <select name="entry_host" id="">
                <?php foreach ($hos as $ho): ?>
                    <option value="<?=$ho['warehouse_id']?>" <?php if($ho['warehouse_id'] == 15){ echo 'selected';} ?>><?=$ho['shortname']?></option>
                <?php endforeach; ?>
            </select>

        </div>
        <button style="float: right;" class="linkButton" id="search">搜索</button>
    </div>
<!--    <strong>显示方式：</strong><!--<button class="interval import" shows="fen" type="button" name="fen">单</button>-->&emsp;&emsp;
<!--    <button class="interval" shows="good" type="button" name="good">件</button>&emsp;&emsp;&emsp;&emsp;&emsp;-->

    <hr style="background-color: #FE4E5B">
    <!--表单开始-->
    <table id="scroll_bar" style="min-width: 36rem;">
        <thead class="header">
            <th class="lessGreen">调拨日期</th>
            <th class="lessGreen">开始仓库</th>
            <th class="lessGreen">结束仓库</th>
            <th class="lessGreen need_allot ">》需调拨</th>
            <th class="lessGreen null_audit">》待审核</th>
            <th class="lessGreen already_notarize">》已确定</th>
            <th class="lessGreen already_abolish">》已取消</th>
            <th class="lessGreen already_out">》已出库</th>
            <th class="lessGreen already_entry">》已入库</th>
        </thead>
        <tbody class="content" style="text-align: centry">
        </tbody>
    </table>
</div>
</body>
<script>
    //仓内调拨时没有入库仓
    $("select[name='allocation']").change(function () {
        if ($(this).val() ==3) {
            //清空并隐藏入库仓
            $(".entry_host").attr('disabled','true');
            $(".entry_host").val("");
            $(".entry_host").hide();
            $("button[name='fen']").hide();
        }else if($(this).val() ==5) {
            $(".entry_host").val("");
            $(".entry_host").attr('disabled','true');
            $(".entry_host").hide();
            $("button[name='fen']").hide();
        }else{
            $(".entry_host").show();
            $("button[name='fen']").show();
        }
    });
    //添加选中样式
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
        var entry_host = $("select[name='entry_host']").val();
        var out_host = $("select[name='out_host']").val();
        var type= $("select[name='allocation']").val();

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
        if (days > 10000){
            alert('时间的间隔不能超过十天！');
        }else {
            $(".content").html('');
            $("#content2").html('');
            /*请求标头判断*/
            if (type ==1){
                $('.null_audit').each(function (){$(this).hide()});
            }else if (type ==3) {
                $('.lessGreen').each(function (){$(this).show()});
                $('.need_allot').each(function (){$(this).hide()});

            }else if (type == 2) {
                $('.need_allot').each(function (){$(this).hide()});


            }else if (type == 4) {
                $('.need_allot').each(function (){$(this).hide()});
                $('.null_audit').each(function (){$(this).hide()});
                $('.already_notarize').each(function (){$(this).hide()});
                $('.already_abolish').each(function (){$(this).hide()});
            }else if (type == 5) {
                $('.need_allot').each(function (){$(this).hide()});
                $('.null_audit').each(function (){$(this).hide()});
                $('.already_notarize').each(function (){$(this).hide()});
                $('.already_abolish').each(function (){$(this).hide()});
                $('.already_out').each(function (){$(this).hide()});
            }
            /*开始请求*/
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: 'getAllocationProcess.php',
                data: {
                    go_date: go_date,
                    to_date: to_date,
                    entry_host: entry_host,
                    out_host: out_host,
                    type: type,
                    opt: 'good',
                    ware: ware
                },
                success: function (data) {
                    console.log(data);
                    var content='';

                    $.each(data,function (k,v) {
                        content += '<tr>';
                        content += '<td>' + v.deliver_dates+ '</td>';
                        content += '<td>' + v.from_house+ '</td>';

                        content += '<td>' + v.to_house+ '</td>';
                        if (v.number == 1) {
                            content += '<td>[整]' + v.already_count_z +'<br>'+v.already_count_s+'</td>';
                            content += '<td>[整]' + v.already_notarize_z +'<br>'+v.already_notarize_s+'</td>';
                            content += '<td>[整]' + v.already_abolish_z +'<br>'+v.already_abolish_s+'</td>';
                            content += '<td>[整]' + v.out_zheng +'<br>'+v.out_san+'</td>';
                            content += '<td>[整]' + v.entry_zheng +'<br>'+v.entry_san+'</td>';
                        }else if (v.number == 3) {
                            content += '<td>[整]' + v.null_audit_z +'<br>'+v.null_audit_s+'</td>';
                            content += '<td>[整]' + v.already_notarize_z +'<br>'+v.already_notarize_s+'</td>';
                            content += '<td>[整]' + v.already_abolish_z +'<br>'+v.already_abolish_s+'</td>';
                            content += '<td>[整]' + v.out_zheng +'<br>'+v.out_san+'</td>';
                            content += '<td>[整]' + v.entry_zheng +'<br>'+v.entry_san+'</td>';


                        }else if (v.number == 2) {

                            content += '<td>[整]' + v.null_audit_z +'<br>'+v.null_audit_s+'</td>';
                            content += '<td>[整]' + v.already_notarize_z +'<br>'+v.already_notarize_s+'</td>';
                            content += '<td>[整]' + v.already_abolish_z +'<br>'+v.already_abolish_s+'</td>';
                            content += '<td>[整]' + v.out_zheng +'<br>'+v.out_san+'</td>';
                            content += '<td>[整]' + v.entry_zheng +'<br>'+v.entry_san+'</td>';

                        }else if (v.number == 4) {
                            content += '<td>[整]' + v.out_zheng +'<br>'+v.out_san+'</td>';
                            content += '<td>[整]' + v.entry_zheng +'<br>'+v.entry_san+'</td>';
                        }else if (v.number == 5) {
                            content += '<td>[整]' + v.entry_zheng +'<br>'+v.entry_san+'</td>';
                        }
                        content += '</tr>';


                    });
                    $(".content").html(content);
                }
            });//ajax结束
        }
    }));
</script>
</html>