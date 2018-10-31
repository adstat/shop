<?php

require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="view/javascript/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="view/javascript/bootstrap/css/bootstrap-theme.min.css">
    <style>
        .list{display: flex;flex-direction: column;}
        .list li{flex: 1;border: 1px solid #00b3ee; text-align:-webkit-center;cursor:pointer;background: #dddddd;margin: 10px 0px 10px 0px;

    </style>
    <title>Document</title>

</head>
<body>
<script>
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
        <li>出库</li>
        <li>入库</li>
        <li>合单</li>
        <li>配送</li>
        <li>回库退货</li>
        <li>退货上架</li>
        <li>回款</li>
    </ul>
</div>
<div id="setOptions" hidden >
    <p style="text-align: center"><strong>分拣仓库:</strong><select class="select" name="warehouse"></select></p>
    <p style="text-align: center"><strong>分拣日期:</strong><input type="text" name="date" value="<?=date("Y-m-d",strtotime("-1 day")) ?>"></p>
    <p style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="pages(1)"  type="button" value="1">未分拣订单</button>&nbsp;&nbsp;&nbsp;&nbsp; <button onclick="sortSearch(this.value)"  type="button" value="2">抽查问题列表</button>&nbsp;&nbsp;&nbsp;&nbsp;</p>
    <hr>
</div>
<div class="container">
    <table id="showSortData_1" hidden class="table">
    </table>
</div>
<div class="container-fluid">
    <table class="table" id="showSortData_2" hidden>
        <thead>
        <tr>
            <th>订单数量</th>
            <th>抽查数量</th>
            <th>抽查人</th>
            <th>抽查日期</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody id="showTbody">
        </tbody>
        <div id="data-area">
            <ul>　　　　　　　　　　　　　　　　<!--这里添加分页数据-->　　　　　　　　　　　　　　</ul>
        </div>
        <div id="pageBar"><!--这里添加分页按钮栏--></div>

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
</div>

<script>
    $('.list').on('click','li',function () {
        $("#setOptions").show();
        // $(this).css({'background-color':'#E67A30','font-size':'150%','font-color':'#6BD089'});
        $('.list li').hide();
    });
    function sortSearch(flag){
        $("table").hide();
        $("#pageBar").hide();
        var time=$('input[name="date"]').val();
        var warehouse=$('select[name="warehouse"]').val();
        if (flag == 2) {
            $("#showSortData_2").show();
            $.ajax({
                type: 'POST',
                url: 'getAbnormalData.php/stopCheck',
                dataType: 'json',
                data: {
                    'flag': flag,
                    'sortStartDate':time,
                    'do_warehouse_id':warehouse
                },
                success: function (data) {
                    var tr='';
                    tr+='<tr >';
                    tr+='<td>'+data['num']+'</td>';
                    tr+='<td>'+data['total']+'</td>';
                    tr+='<td>'+data['title']+'</td>';
                    tr+='<td>'+data['date_added']+'</td>';
                    tr+='<td><button onclick="findOne();" class="btn-default">查看</button></td>';
                    tr+='</tr>';
                    $("#showSortData_"+flag+" tbody").html(tr);

                }
            });//查询结束
        }
    }
    function findOne() {
        $("#showInfo").show();

        var time=$('input[name="date"]').val();
        var warehouse=$('select[name="warehouse"]').val();
        var flag=5;

        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php/getOrderInfo',
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

    var curPage;
    var totalItem;
    var pageSize;
    var totalPage;

    function pages(page)
    {
        $("table").hide();
        var time=$('input[name="date"]').val();
        var warehouse=$('select[name="warehouse"]').val();
        var flag=1;
        $.ajax({
            type: 'POST',
            url: 'getAbnormalData.php/getSortData',
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
                $("#data-area ul").empty();
                totalItem = json.totalItem;
                pageSize = json.pageSize;
                curPage = page;
                totalPage = json.totalPage;
                var data_content = json.data_content;
                var data_html = "";
                var li='';
                li+='<thead>';
                li+='<tr>';
                li+='<th>分拣单号</th>';
                li+='<th>订单号</th>';
                li+='<th>目地仓库</th>';
                li+='<th>所属仓库</th>';
                li+='<th>数量</th>';
                li+='<th>加急</th>';
                li+='<th>深度</th>';
                li+='</tr>';
                li+='</thead>';
                li+='<tbody>';
                $.each(data_content,function (k,v) {
                    li+='<tr>';
                    li+='<td>'+v.deliver_order_id+'</td>';
                    li+='<td>'+v.order_id+'</td>';
                    li+='<td>'+v.title+'</td>';
                    li+='<td>'+v.warehouseZS+'</td>';
                    li+='<td>'+v.sortNum+'</td>';
                    li+='<td>'+v.urgent+'</td>';
                    li+='<td>'+v.stageTarget+'</td>';
                    li+='</tr>';
                });
                li+='</tbody>';
                $("#showSortData_"+flag).html(li);


                $("#data-area ul").append(data_html);
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
</style>
</body>
</html>
