<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/1
 * Time: 15:59
 */
?>
<div style="display:none; margin:0.5em auto" id="sec5">
    <hr />
    <h1>查询订单号信息<input id="hide_order_history" type="button" onclick="javascript:hide_order_history();" value="隐藏" style="width:10em;" ></h1>
    <hr />
    <script>
        function hide_order_history() {
            var hide_order_history = $("#hide_order_history").val();
            if (hide_order_history == "隐藏") {
                $("#orderhistorylist").hide();
                $("#hide_order_history").val("显示");
            } else if (hide_order_history == "显示") {
                $("#orderhistorylist").show();
                $("#hide_order_history").val("隐藏");
            }
        }
    </script>
    <table border="0" style="width:100%;background-color: white;" bgcolor="#00ff7f" cellpadding="2" cellspacing="3" id="orderhistorylist">
        <thead>
        <tr><td colspan="10">订单信息</td></tr>
        <tr>
            <th>订单号</th>
            <th>下单仓库</th>
            <th>下单日期</th>
            <th>订单状态</th>
            <th>配送日期</th>
            <th>配送地区</th>
        </tr>
        </thead>
        <tbody id="orderedhistorylist1">
        </tbody>
        <thead>
        <tr><td colspan="10">DO单信息</td></tr>
        <tr>
            <th>DO单号</th>
            <th>分拣仓库</th>
            <th>分拣人</th>
            <th>配送日期</th>
            <th>DO单状态</th>
            <th>分拣状态</th>
        </tr>
        </thead>
        <tbody id="orderedhistorylist3">
        </tbody>
        <thead>
        <tr><td colspan="10">DO单修改历史信息</td></tr>
        <tr>
            <th>DO单号</th>
            <th>分拣仓库</th>
            <th>配送日期</th>
            <th>DO单状态</th>
            <th>修改日期</th>
            <th>分拣状态</th>
        </tr>
        </thead>
        <tbody id="orderedhistorylist2">
        </tbody>
    </table>

    <hr />
</div>