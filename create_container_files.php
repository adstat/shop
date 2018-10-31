
<!--<html>-->
<!--<head>-->
<!--    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">-->
<!--    <title>生成周转筐</title>-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">-->
<!--    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>-->
<!--    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>-->
<!--</head>-->
<!--<body>-->
<!--<hr />-->
<!--<input type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="刷新页面" onclick="window.location.href='create_container_files.php'">-->
<!--<input type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="返回" onclick="window.location.href='i.php&ver=db'"><br /><br />-->
<!--<input  type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="生成周转筐" onclick="Javascript:show_special(1);">-->
<!--<input  type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="缺货补库存" onclick="Javascript:show_special(2);">-->
<!--<input type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="取消退货" onclick="Javascript:show_special(3);">-->
<!--<hr />-->
<!--<script>-->
<!--    function show_special(is) {-->
<!--        $(".show_div").each(function () {-->
<!--            $(this).hide();-->
<!--        });-->
<!--        $("#show_div"+is).show();-->
<!--    }-->
<!--</script>-->
<!--    <div class="show_div" id="show_div1" style="display: none;">-->
<!--        <span style="width: 3em">生成周转筐总数量：</span>-->
<!--        <input id="total_count" autocomplete="off" class="date" type="text" style="font-size: 15px; width:8em;height:1.5em;border:1px solid"  value="1000"/>-->
<!--        &nbsp;<select id="date_change" style="font-size: 15px;width:5em;height:1.5em;border:1px solid" onchange="javascript:change_date_time();">-->
<!--            <option value="500">500</option>-->
<!--            <option value="1000" selected>1000</option>-->
<!--            <option value="1500">1500</option>-->
<!--            <option value="2000" >2000</option>-->
<!--            <option value="2500">2500</option>-->
<!--            <option value="3000">3000</option>-->
<!--            <option value="5000">5000</option>-->
<!--        </select>-->
<!--        <br />-->
<!--        <br />-->
<!--        <script>-->
<!--            function change_date_time() {-->
<!--                $("#total_count").val($("#date_change").val());-->
<!--            }-->
<!--        </script>-->
<!--        <span style="width: 3em">每份文件数量</span>-->
<!--        &nbsp;<select id="every_count" style="font-size: 15px;width:5em;height:1.5em;border:1px solid">-->
<!--            <option value="500" selected>500</option>-->
<!--            <option value="1000">1000</option>-->
<!--            <option value="1500">1500</option>-->
<!--            <option value="2000">2000</option>-->
<!--        </select>-->
<!--        <br />-->
<!--        <br />-->
<!--        周转筐类型：-->
<!--        <select id="container_type" style="font-size: 15px;width:8em;height:1.5em;border:1px solid">-->
<!--            <option value="2">周转筐</option>-->
<!--            <option value="1" selected>临时周转筐</option>-->
<!--        </select>-->
<!--        <br />-->
<!--        <br />-->
<!--<!--        选择保存位置:<input  type="file" name="photo"/>-->
<!--<!--        起始周转筐号：-->
<!--<!--        <input id="inventory_order_sorting" name="input_purchase_relevant_id" style="font-size: 15px;width:8em;height:1.5em;border:1px solid" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
<!--        <input type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="生成周转筐" onclick="click_different_button(1)">-->
<!--        <hr />-->
<!--        <table>-->
<!--            <caption>生成周转筐下载列表</caption>-->
<!--            <tbody id="container_lists"></tbody>-->
<!--        </table>-->
<!--        <br />-->
<!--        <br />-->
<!--    </div>-->
<!--    <div class="show_div" id="show_div2"  style="display: none;">-->
<!--        订单号<input id="order_id" autocomplete="off" class="date" type="text" style="font-size: 15px; width:8em;height:1.5em;border:1px solid"  value=""/><br /><br />-->
<!--        需补库存商品<input id="product_id" autocomplete="off" class="date" type="text" style="font-size: 15px; width:8em;height:1.5em;border:1px solid"  value=""/><br /><br />-->
<!--        <input type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="查询退货信息" onclick="click_different_button(2)">-->
<!--        <hr />-->
<!--        <table>-->
<!--            <caption>退货信息列表</caption>-->
<!--            <tr>-->
<!--                <th>退货</th>-->
<!--                <th>备注</th>-->
<!--                <th>订单</th>-->
<!--                <th>状态</th>-->
<!--                <th>余额</th>-->
<!--                <th>商品</th>-->
<!--                <th>数量</th>-->
<!--            </tr>-->
<!--            <tbody id="return_lists"></tbody>-->
<!--        </table>-->
<!--        <br />-->
<!--        <hr />-->
<!--        <table>-->
<!--            <caption>需补库存商品列表</caption>-->
<!--            <tr>-->
<!--                <th>库存</th>-->
<!--                <th>订单</th>-->
<!--                <th>商品</th>-->
<!--                <th>id</th>-->
<!--                <th>数量</th>-->
<!--                <th>价格</th>-->
<!--            </tr>-->
<!--            <tbody id="stock_lists"></tbody>-->
<!--        </table>-->
<!--        <br /><br />-->
<!--        <input type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="提交补库存" onclick="click_different_button(3)">-->
<!---->
<!--    </div>-->
<!--    <div class="show_div" id="show_div3"  style="display: none;">-->
<!--        订单号<input id="order_id2" autocomplete="off" class="date" type="text" style="font-size: 15px; width:8em;height:1.5em;border:1px solid"  value=""/><br /><br />-->
<!--        取消退单商品id<input id="product_id2" autocomplete="off" class="date" type="text" style="font-size: 15px; width:8em;height:1.5em;border:1px solid"  value=""/><br /><br />-->
<!--        选择前台库存id-->
<!--        <select id="inventory_is_list" style="font-size: 15px;width:8em;height:1.5em;border:1px solid">-->
<!--        </select><br /><br />-->
<!--        <input type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="查询退货信息" onclick="click_different_button(4)">-->
<!--        <hr />-->
<!--        <table>-->
<!--            <caption>退货信息列表</caption>-->
<!--            <tr>-->
<!--                <th>退货</th>-->
<!--                <th>备注</th>-->
<!--                <th>订单</th>-->
<!--                <th>状态</th>-->
<!--                <th>余额</th>-->
<!--                <th>商品</th>-->
<!--                <th>数量</th>-->
<!--            </tr>-->
<!--            <tbody id="return_lists"></tbody>-->
<!--        </table>-->
<!--        <br />-->
<!--        <hr />-->
<!--        <table>-->
<!--            <caption>需补库存商品列表</caption>-->
<!--            <tr>-->
<!--                <th>库存</th>-->
<!--                <th>订单</th>-->
<!--                <th>商品</th>-->
<!--                <th>id</th>-->
<!--                <th>数量</th>-->
<!--                <th>价格</th>-->
<!--            </tr>-->
<!--            <tbody id="stock_lists"></tbody>-->
<!--        </table>-->
<!--        <br /><br />-->
<!--        <input type="button" style="font-size:1em;width:8em;height:1.5em; background: red" value="取消退货数据" onclick="click_different_button(5)">-->
<!--    </div>-->
<!--</body>-->
<!--</html>-->
<!--<script type="text/javascript">-->
<!--    function click_different_button(type){-->
<!--        var total_count = 0;-->
<!--        var every_count = 0;-->
<!--        var container_type = 0;-->
<!--        switch(type){-->
<!--            case 1:-->
<!--                total_count = $("#total_count").val();-->
<!--                every_count = $("#every_count").val();-->
<!--                container_type = $("#container_type").val();-->
<!---->
<!--                break;-->
<!--            case 2:-->
<!--                total_count = $("#order_id").val();-->
<!--                every_count = $("#product_id").val();-->
<!--                if (parseInt(total_count)>0) {-->
<!---->
<!--                } else {-->
<!--                    alert('条件不能为空');-->
<!--                    return false;-->
<!--                }-->
<!--                break;-->
<!--            case 3:-->
<!--                total_count = $("#order_id").val();-->
<!--                every_count = $("#product_id").val();-->
<!--                if (parseInt(total_count)>0) {-->
<!---->
<!--                } else {-->
<!--                    alert('条件不能为空');-->
<!--                    return false;-->
<!--                }-->
<!--                break;-->
<!--            case 4:-->
<!--                total_count = $("#order_id2").val();-->
<!--                every_count = $("#product_id2").val();-->
<!--                container_type = $("#inventory_is_list").val();-->
<!--                if (parseInt(total_count)>0) {-->
<!---->
<!--                } else {-->
<!--                    alert('条件不能为空');-->
<!--                    return false;-->
<!--                }-->
<!--                break;-->
<!--            case 5:-->
<!--                total_count = $("#order_id2").val();-->
<!--                every_count = $("#product_id2").val();-->
<!--                container_type = $("#inventory_is_list").val();-->
<!--                if (parseInt(total_count)>0 && parseInt(container_type)>0) {-->
<!---->
<!--                } else {-->
<!--                    alert('条件不能为空');-->
<!--                    return false;-->
<!--                }-->
<!--                break;-->
<!--        }-->
<!--        $.ajax({-->
<!--            type: 'POST',-->
<!--            url: 'change_error_array_only.php',-->
<!--            //async : false,-->
<!--            //cache : false,-->
<!--            data: {-->
<!--                total_count: total_count,-->
<!--                every_count: every_count,-->
<!--                container_type: container_type,-->
<!--                change_type:type,-->
<!--            },-->
<!--            success: function (response) {-->
<!--                if (response) {-->
<!--                    console.log(response);-->
<!--                    var jsonData = $.parseJSON(response);-->
<!--                    var html = '';-->
<!--                    switch(type){-->
<!--                        case 1:-->
<!---->
<!--                            $.each(jsonData, function (i, v) {-->
<!--                                html += '<tr><th><a href="download.php?filename='+v+'">下载'+v+'</a></th></tr>';-->
<!--                            });-->
<!--                            $("#container_lists").html(html);-->
<!---->
<!--                            break;-->
<!--                        case 2:-->
<!---->
<!--                            var html2 = '';-->
<!--                            $.each(jsonData.return, function (i1, v1) {-->
<!--                                html += '<tr>';-->
<!--                                html += '<th>'+v1.return_id+'</th>';-->
<!--                                html += '<th>'+v1.comment+'</th>';-->
<!--                                html += '<th>'+v1.order_id+'</th>';-->
<!--                                html += '<th>'+v1.return_status_id+'</th>';-->
<!--                                html += '<th>'+v1.return_credits+'</th>';-->
<!--                                html += '<th>'+v1.product_id+'</th>';-->
<!--                                html += '<th>'+v1.quantity+'</th>';-->
<!--                                html += '</tr>';-->
<!--                            });-->
<!--                            $.each(jsonData.stock, function (i2, v2) {-->
<!--                                html2 += '<tr>';-->
<!--                                html2 += '<th>'+v2.inventory_move_id+'</th>';-->
<!--                                html2 += '<th>'+v2.order_id+'</th>';-->
<!--                                html2 += '<th>'+v2.name+'</th>';-->
<!--                                html2 += '<th>'+v2.product_id+'</th>';-->
<!--                                html2 += '<th>'+v2.quantity+'</th>';-->
<!--                                html2 += '<th>'+v2.price+'</th>';-->
<!--                                html2 += '</tr>';-->
<!--                            });-->
<!--                            $("#return_lists").html(html);-->
<!--                            $("#stock_lists").html(html2);-->
<!---->
<!--                            break;-->
<!--                        case 3:-->
<!---->
<!--                            if (parseInt(jsonData) == 1) {-->
<!--                                alert("成功");-->
<!--                            }-->
<!---->
<!--                            break;-->
<!--                        case 4:-->
<!--                            $.each(jsonData.return, function (i1, v1) {-->
<!--                                html += '<tr>';-->
<!--                                html += '<th>'+v1.return_id+'</th>';-->
<!--                                html += '<th>'+v1.comment+'</th>';-->
<!--                                html += '<th>'+v1.order_id+'</th>';-->
<!--                                html += '<th>'+v1.return_status_id+'</th>';-->
<!--                                html += '<th>'+v1.return_credits+'</th>';-->
<!--                                html += '<th>'+v1.product_id+'</th>';-->
<!--                                html += '<th>'+v1.quantity+'</th>';-->
<!--                                html += '</tr>';-->
<!--                            });-->
<!---->
<!--                            $("#return_lists").html(html);-->
<!---->
<!--                            break;-->
<!--                        case 5:-->
<!---->
<!--                            if (parseInt(jsonData)>0) {-->
<!--                                alert("成功");-->
<!--                                alert(jsonData);-->
<!--                            }-->
<!---->
<!--                            break;-->
<!--                    }-->
<!---->
<!--                }-->
<!--            }-->
<!--        });-->
<!--    }-->
<!--</script>-->



<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$os = explode(" ", php_uname()); echo $os[0];
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=create_container_files.php&ver=db");
    //确保重定向后，后续代码不会被执行
    exit;
}
if (!in_array($_COOKIE['inventory_user_id'],[872,875,1366,1367,1368,1384,1457])) {
    echo '<script> alert("无该账户权限");window.location="Location: inventory_login.php?return=i.php&ver=db"; </script>';
    exit;
} else if (empty($_GET['set_safe'])) {
    echo '<script> alert("无该页面权限");window.location="Location: i.php?ver=db";</script>';
    exit;
} else if ($_GET['set_safe'] != 'ad9f186d3a82513b72a3246295f9fce9') {
    echo '<script> alert("无该密码权限");window.location="Location: i.php?ver=db";</script>';
    exit;
}

?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>生成周转筐</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
    <style>
        th{
            /*background-color:#ffffaa;*/
            color: #000;
            height: 15px;
            font-size: 12px;
            padding: 2px;

            border-left: solid #000 1px;
            border-top: solid #000 1px;
            /*border-radius: 0.1em;*/
            /*box-shadow: 0.1em rgba(0, 0, 0, 0.2);*/
        }

    </style>
</head>
<body>
<hr />
<input type="button" style="font-size:1em;width:6em;height:1.5em; background: red" value="刷新页面" onclick="window.location.href='create_container_files.php?auth=xsj2015inv&set_safe=ad9f186d3a82513b72a3246295f9fce9'">
<input type="button" style="font-size:1em;width:6em;height:1.5em; background: red" value="返回" onclick="window.location.href='i.php?ver=db'">
<input  type="button" style="font-size:1em;width:6em;height:1.5em; background: red" value="生成周转筐" onclick="Javascript:show_special(1);">
<input  type="button" style="font-size:1em;width:6em;height:1.5em; background: red" value="缺货补库存" onclick="Javascript:show_special(2);">
<input type="button" style="font-size:1em;width:6em;height:1.5em; background: red" value="取消退货" onclick="Javascript:show_special(3);">
<input type="button" style="font-size:1em;width:6em;height:1.5em; background: red" value="运行sql" onclick="Javascript:show_special(6);">
<script>
    function show_special(is) {
        $(".show_div").each(function () {
            $(this).hide();
        });
        $("#show_div"+is).show();
    }
</script>
<div class="show_div" id="show_div1" style="display: none;">
    <span style="width: 3em">生成周转筐总数量：</span>
    <input id="total_count" autocomplete="off" class="date" type="text" style="font-size: 15px; width:8em;height:1.5em;border:1px solid"  value="1000"/>
    &nbsp;<select id="date_change" style="font-size: 15px;width:5em;height:1.5em;border:1px solid" onchange="javascript:change_date_time();">
        <option value="500">500</option>
        <option value="1000" selected>1000</option>
        <option value="1500">1500</option>
        <option value="2000" >2000</option>
        <option value="2500">2500</option>
        <option value="3000">3000</option>
        <option value="5000">5000</option>
    </select>
    <br />
    <br />
    <script>
        function change_date_time() {
            $("#total_count").val($("#date_change").val());
        }
    </script>
    <span style="width: 3em">每份文件数量</span>
    &nbsp;<select id="every_count" style="font-size: 15px;width:5em;height:1.5em;border:1px solid" onchange="javascript:change_date_end_time();">
        <option value="500" selected>500</option>
        <option value="1000">1000</option>
        <option value="1500">1500</option>
        <option value="2000">2000</option>
    </select>
    <br />
    <br />
    周转筐类型：
    <select id="container_type" style="font-size: 15px;width:8em;height:1.5em;border:1px solid">
        <option value="2">周转筐</option>
        <option value="1" selected>临时周转筐</option>
        <option value="3">冷冻箱</option>
        <option value="4">保温箱</option>
    </select>
    <br />
    <br />
    <!--        选择保存位置:<input  type="file" name="photo"/>-->
    <!--        起始周转筐号：-->
    <!--        <input id="inventory_order_sorting" name="input_purchase_relevant_id" style="font-size: 15px;width:8em;height:1.5em;border:1px solid" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
    <input type="button" style="font-size:1em;width:6em;height:1.5em; background: red" value="生成周转筐" onclick="click_different_button(1)">
    <table>
        <caption>生成周转筐下载列表</caption>

    </table>
    <form method="get" action="download.php">
        <div id="container_lists"></div>
    </form>
    <br />
    <br />
</div>
<div class="show_div" id="show_div2"  style="display: none;">

</div>
<div class="show_div" id="show_div3"  style="display: none;">

</div>
<div class="show_div" id="show_div6"  style="display: none;">
    <table>
        <tr id="show_th_text"></tr>
        <tbody id="sql_result"></tbody>
    </table>
    <textarea id="sql_text" style="width: 90%;height: 15em;"></textarea><br />
    <input type="button" style="font-size:1em;width:6em;height:1.5em; background: red" value="运行sql" onclick="click_different_button(6)">

</div>


</body>
</html>

<script type="text/javascript">
    function click_different_button(type){
        var total_count = 0;
        var every_count = 0;
        var container_type = 0;
        switch(type){
            case 1:
                total_count = $("#total_count").val();
                every_count = $("#every_count").val();
                container_type = $("#container_type").val();
                break;
            case 2:
                break;
            case 3:
                break;
            case 6:
                container_type = $("#sql_text").val();
                break;
        }
        $.ajax({
            type: 'POST',
            url: 'change_error_array_only.php',
            //async : false,
            //cache : false,
            data: {
                total_count: total_count,
                every_count: every_count,
                container_type: container_type,
                change_type:type,
            },
            success: function (response) {
                if (response) {
                    // console.log(response);
                    var jsonData = $.parseJSON(response);
                    switch (type) {
                        case 1:
                            var html = '<input type="submit" value="下载" />';

                            $.each(jsonData, function (i, v) {
                                html += '<input type="hidden" value="' + v.split('.txt')[0] + '" name="filenames[]"/>';
                            });

                            $("#container_lists").html(html);
                            break;

                        case 6:
                            var html = '';
                            var th_text = '';
                            if (jsonData.return_code == 'ERROR') {
                                alert(jsonData.return_msg);
                                return true;
                            } else if (jsonData.return_code == 'OK') {
                                alert(jsonData.return_msg);
                                return true;
                            } else if (jsonData.return_code == 'SUCCESS') {
                                // alert(jsonData.return_msg);
                            } else {
                                alert('数据异常请刷新后重试');
                            }
                            $.each(jsonData.return_data, function (i, v) {
                                html += '<tr>';
                                th_text = '';
                                $.each(v, function (index,value) {
                                    html += '<th>'+value+'</th>';
                                    th_text += '<th>'+index+'</th>';
                                });
                                html += '</tr>';
                            });
                            $("#sql_result").html(html);
                            $("#show_th_text").html(th_text);
                            break;
                    }
                }
            }
        });
    }

</script>