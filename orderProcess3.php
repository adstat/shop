<?php
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=w_dis.php");
    //确保重定向后，后续代码不会被执行
    exit;
}

$warehouseId = isset($_REQUEST['warehouseId']) ? (int)$_REQUEST['warehouseId'] : 0;
//if(!$warehouseId){
//    exit('请选择指定仓库账户登录。');
//}

/*
 * 查仓库
 * */
$str= [];
$sql="SELECT warehouse_id,shortname FROM oc_x_warehouse";
$hos=$db->query($sql)->rows;
foreach ($hos as $k=>$v){
    $str[]=$v['warehouse_id'];
}
$str=implode(' and ',$str);

?>
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>当前仓库十日订单处理情况</title>
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
            width: calc( 100% - 1em )
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
            background-color: greenyellow;
            font-size: 22px;
            cursor: pointer;
            border-bottom-color: #8fbb6c;
            border-radius: 6px;
        }
        .import{/*选中样式*/
            background-color: #66afe9;
        }
        .lessGreen {background-color: #afdb76;}

        .lightGreen {background-color:#d0e9c6;}
        .nobg {background-color:#eeeeee;}
        .linkButton {width: 2.4rem; height: 1.4rem; margin: 0.3rem; padding:0.2rem; font-size: 0.9rem; color:#ffffff; border-radius: 0.2rem; background-color: #DF0000;}
        </style>
        <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
<!--        <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>-->
    </head>
    <body style="padding: 5px;">
    <form action="" method="get">
        <table id="scroll_bar">
            <thead>
                <tr>
                    <strong style="font-size: 40px">开始日期：</strong><input  name="go_date" size="10px" style="border:0.5px solid #378888;border-radius:10px;height: 40px;font-size: 30px;" type="text" value="<?= date('Y-m-d',strtotime('-5 day')); ?>">&emsp;&emsp;
                    <strong style="font-size: 40px">分拣仓库：</strong>
                    <select name="go_host" id="" style="width: 100px;height: 30px;">
                        <option value="99">全部</option>
                        <?php foreach ($hos as $k=>$v):

                            ?>
                            <?php if ($v['warehouse_id'] == $_REQUEST['warehouseId']):?>
                        <option selected  value="<?=$v['warehouse_id']?>"><?=$v['shortname']?></option>
                        <?php else: ?>
                        <option  value="<?=$v['warehouse_id']?>"><?=$v['shortname']?></option>
                        <?php endif; ?>
                        <?php endforeach;?>
                    </select><br>
                    <strong style="font-size: 40px">结束日期：</strong><input  name="to_date" size="10px" style="border:0.5px solid #378888;border-radius:10px;height: 40px;font-size: 30px;" type="text" value="<?= date('Y-m-d',time()); ?>">&emsp;&emsp;
                    <strong style="font-size: 40px">目地仓库：</strong>
                    <select name="to_host" id="" style="width: 100px;height: 30px;">
                        <option value="99">全部</option>
                        <?php foreach ($hos as $k=>$v): ?>
                            <option value="<?=$v['warehouse_id']?>"><?=$v['shortname']?></option>
                        <?php endforeach;?>
                    </select>
                    <br>
                    <!--管理员条件-->
<!--                    <strong style="font-size: 40px">按周期：</strong><button class="todate import" style="font-size: 30px" look="3" type="button">三天</button>&emsp;&emsp;<button class="todate" style="font-size: 30px" look="5" type="button" >五天</button>&emsp;&emsp;<button class="todate" style="font-size: 30px" look="10" type="button">十天</button>-->
<!--                    <br>-->
                    <strong style="font-size: 40px">条件：</strong><button class="interval import" style="font-size: 30px" shows="fen" type="button" name="fen">按分捡</button>&emsp;&emsp;<button class="interval" style="font-size: 30px" shows="good" type="button" name="good">按商品</button>&emsp;&emsp;&emsp;&emsp;&emsp;<button id="search"  style="font-size: 30px;background-color: green;width: 200px;margin-right: 200px" type="button" >搜索</button>
                    <hr color="green">
                </tr>

                <tr id="bar_head">
                    <th>配送日期</th>
                    <th>分拣仓库</th>
                    <th>目的仓库</th>
                    <th>分拣单数</th>
                    <th class="lessGreen">类型</th>
                    <th class="lessGreen">》未分拣</th>
                    <th class="lessGreen">》已分配</th>
                    <th class="lessGreen">》分拣中</th>
                    <th class="lessGreen">》待审核</th>
                    <th class="lessGreen">》已拣完</th>
                    <th class="lessGreen">》其他状态</th>
                </tr>
            </thead>
            <tbody class="content" style="text-align: center">
            </tbody>
        </table>
    </form>
    </body>
    <script>
        $(".interval").click(function () {
            $('.interval').removeClass('import');
            $(this).addClass('import');

        });
        /*开始整件*/
        $('#search').on('click',(function () {
            var opt = $('.interval.import').attr('shows');
            var go_date = $("input[name='go_date']").val();
            var to_date = $("input[name='to_date']").val();
            var go_host = $("select[name='go_host']").val();
            var to_host = $("select[name='to_host']").val();
            var ware= <?php echo $_REQUEST['warehouseId']; ?>;
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
            if (days > 30){
                // $("#search").attr('disabled',true);
                // $(this).css("background",'#696969');
                alert('时间的间隔不能超过三十天！');
                // return true;

            }else {
                // $("#search").attr('disabled',false);
                /*开始请求*/
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: 'http://localhost/shop/warehouse/getInfo.php',
                    data: {go_date: go_date,to_date: to_date,go_host: go_host,to_host: to_host, opt: opt, ware: ware},
                    success: function (data) {
                        if (data != '') {
                            var tr = '';
                            $.each(data, function (index, obj) {
                                if (obj.null_sort_scatter_num == undefined) {
                                    tr += '<tr>';
                                    tr += '<td>' + obj.delivery_date+ '</td>';
                                    tr += '<td>' + obj.do_house+ '</td>';
                                    tr += '<td>' + obj.target_house + '</td>';
                                    tr += '<td>' + obj.sort_num + '</td>';
                                    tr += '<td>' + obj.sort_type + '</td>';
                                    tr += '<td>' + obj.null_sort_num + '</td>';
                                    tr += '<td>' + obj.already_allot + '</td>';
                                    tr += '<td>' + obj.sorting_num + '</td>';
                                    tr += '<td>' + obj.to_audit_num + '</td>';
                                    tr += '<td>' + obj.over_sort_num + '</td>';
                                    tr += '<td>' + obj.other_type + '</td>';
                                    tr += '</tr>';
                                } else {
                                    tr += '<tr>';
                                    tr += '<td>' + obj.delivery_date+ '</td>';
                                    tr += '<td>' + obj.do_house+ '</td>';
                                    tr += '<td>' + obj.target_house + '</td>';
                                    tr += '<td>' + obj.sort_num + '/单<br/>整：' + obj.full_num + '<br/>散：' + obj.scatter_num + '</td>';
                                    tr += '<td>' + obj.sort_type + '</td>';
                                    tr += '<td>整：' + obj.null_sort_full_num + '<br/>散：' + obj.null_sort_scatter_num + '</td>';
                                    tr += '<td>整：' + obj.over_allot_full_num + '<br/>散：' + obj.over_allot_scatter_num + '</td>';
                                    tr += '<td>整：' + obj.sorting_full_num + '<br/>散：' + obj.sorting_scatter_num + '</td>';
                                    tr += '<td>整：' + obj.audit_full_num + '<br/>散：' + obj.audit_scatter_num + '</td>';
                                    tr += '<td>整：' + obj.over_full_num + '<br/>散：' + obj.over_scatter_num + '</td>';
                                    tr += '<td>整：' + obj.other_statu_full_num + '<br/>散：' + obj.other_statu_scatter_num + '</td>';
                                    tr += '</tr>';
                                }
                            });
                            $(".content").html(tr);
                        } else {
                            $(".content").html('');
                        }
                    },
                    error: function (err) {
                        $(".content").html('');
                    }

                });
            }
        }));
    </script>
</html>