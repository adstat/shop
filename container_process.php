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
    <script type="text/javascript" src="view/javascript/jquery/jquery-1.8.3.min.js"></script>
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
        <strong>开始：</strong><input id="go_date" name="go_date" size="10px" style="font-size:0.9rem; border:1px solid #378888; padding: 2px;" type="text" value="<?= date('Y-m-d',strtotime('-5 day')); ?>">&emsp;&emsp;
        <strong>结束：</strong><input id="to_date" name="to_date" size="10px" style="font-size:0.9rem; border:1px solid #378888;  padding: 2px;" type="text" value="<?= date('Y-m-d',strtotime('1 day')); ?>">&emsp;&emsp;
    </div>
    <div style="margin: 0.3rem">
        <div class="out_host">
            <strong>分拣仓：</strong>
            <select id="out_host" name="out_host" id="">
                <option value="100">--全部--</option>
                <?php foreach ($hos as $ho): ?>
                    <option value="<?=$ho['warehouse_id']?>"?><?=$ho['shortname']?></option>
                <?php endforeach; ?>
            </select>
        </div><div class="entry_host">
            <strong>出库仓：</strong>
            <select id="entry_host" name="entry_host" id="">
                <option value="100">--全部--</option>
                <?php foreach ($hos as $ho): ?>
                    <option value="<?=$ho['warehouse_id']?>"?><?=$ho['shortname']?></option>
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
        <thead class="header" id="content">
            <th class="lessGreen">时间</th>
            <th class="lessGreen">分拣仓</th>
            <th class="lessGreen">出库仓</th>
            <th class="lessGreen need_allot ">分拣框数</th>
            <th class="lessGreen null_audit">》调拨生成框数</th>
            <th class="lessGreen already_notarize">》调拨出库框数</th>
            <th class="lessGreen already_abolish">》调拨入库框数</th>
            <th class="lessGreen already_out">》投篮数</th>
            <th class="lessGreen already_entry">》出库框数</th>
            <th class="lessGreen already_entry">操作</th>
        </thead>
        <!-- <tbody class="content"  style="text-align: center;"> -->
        </tbody>
    </table>
</div>
</body>
<script>

   $(function(){
    $('#search').click(function (){
        //获取搜索条件
       var go_data = $('#go_date').val();
       var to_data = $('#to_date').val();
       var out_host = $('#out_host').val();
       var entry_host = $('#entry_host').val();
       // console.log(go_data,entry_host);
       $.ajax({
            type : 'POST',
            url : 'invapi.php',

            dataType : 'json',
            data : {
                method : 'searchContainer',
                data:{
                    go_data : go_data,
                    to_data : to_data,
                    out_host : out_host,
                    entry_host : entry_host
                }
                    
            },
            success : function (data) {
                $('thead #remove').remove();
                // console.log(data.length);
                for (var i = 0; i < data.length; i++) {
                $('<tr id="remove"><td>'+data[i].deliver_date+'</td><td>'+data[i].do_warehouse_id+'</td><td>'+data[i].warehouse_id+'</td><td>'+data[i].count_deliver_containt+'</td><td>'+data[i].allot_count+'</td><td>'+data[i].allot_out_count+'</td><td>'+data[i].allot_entry_count+'</td><td>'+data[i].idos_count+'</td><td>'+data[i].cfm_count+'</td><td><button class="linkButton" id="abnormal" onclick="show_container_detail($(this))")">查看</button></td></tr>').appendTo('#content');

            } 
            },
            error : function () {
                alert('AJAX处理失败');
            }

       })

    });
    
  

   })

   function show_container_detail(value)
   {
     $('#scroll_bar #remove_1').remove();
    var time = value.parent().parent().find('td').eq(0).html();
    var do_warehouse = value.parent().parent().find('td').eq(1).html();
    var warehouse = value.parent().parent().find('td').eq(2).html();
    $.ajax({
       type : 'POST',
            url : 'invapi.php',

            dataType : 'json',
            data : {
                method : 'show_container_detail',
                data:{
                    time : time,
                    do_warehouse : do_warehouse,
                    warehouse : warehouse
                }
                    
            },
            success : function (data) {
                // console.log(data);
               // var jsonData = (data);
               var container_array = [data.container_diff.length,data.in_diff.length,data.order_diff.length,data.out_diff.length,data.put_diff.length];
               var team = [].concat(data.container_diff,data.in_diff,data.order_diff,data.out_diff,data.put_diff);
               var team_1 = [];
               for(var i = 0;i < team.length; i++){
                 team_1.indexOf(team[i]) == -1 ? team_1.push(team[i]) : 0
               }
              
              
              for(var i = 0; i < team_1.length; i++){
                if($.inArray(team_1[i],data.container_diff) == -1){
                    var a = '';
                } else {
                    var a = team_1[i];
                }
                if($.inArray(team_1[i],data.in_diff) == -1){
                    var b = '';
                }else {
                    var b = team_1[i];
                }
                if($.inArray(team_1[i],data.order_diff) == -1){
                    var c = '';
                }else {
                    var c = team_1[i];
                }
                if($.inArray(team_1[i],data.out_diff) == -1){
                    var d = '';
                }else {
                    var d = team_1[i];
                }
                if($.inArray(team_1[i],data.put_diff) == -1){
                    var e = '';
                }else {
                    var e = team_1[i];
                }


                $('<tr id="remove_1" style="background-color:#ccc"><td></td><td></td><td></td><td>'+team_1[i]+'</td><td>'+a+'</td><td>'+d+'</td><td>'+b+'</td><td>'+e+'</td><td>'+c+'</td><td></td></tr>').insertAfter(value.parent().parent());
              }
            },
            error : function (){
                alert('AJAX异常');
            }
    });
   }
</script>
</html>