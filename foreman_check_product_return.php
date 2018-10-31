<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/9/12
 * Time: 17:18
 */

require_once '../api/config.php';
require_once(DIR_SYSTEM . 'db.php');

/*
 * 查仓库
 * */

$warehouse_id = isset($_COOKIE['warehouse_id']) ? $_COOKIE['warehouse_id'] : false;
if (!$warehouse_id) {
    exit("未设置仓库登录属性，请在分拣页面重新登录");
}

//当前日期
$h_now = date("H", time());
$today = date("Y-m-d 00:00:00", time());

if ($h_now >= 12) {
    $checkStart = date("Y-m-d 02:00:00", time());
} else {
    $checkStart = date("Y-m-d 17:00:00", time() - 24 * 3600);
}
$checkEnd = date("Y-m-d H:00:00", time());

$checkStart = isset($_POST['checkStart']) ? $_POST['checkStart']." 00:00:01" : $checkStart;
//var_dump($checkStart);exit();
$checkEnd = isset($_POST['checkStart']) ? $_POST['checkStart']." 23:59:59" : $checkEnd;
$displayType = isset($_POST['displayType']) ? $_POST['displayType'] : 1;
$orderStatus = isset($_POST['orderStatus']) ? $_POST['orderStatus'] : 6;

$queryOrderStatus = $orderStatus;
if ($orderStatus == 99) {
    $queryOrderStatus = '6,8'; //同时查找两种状态
}
$station_id = isset($_POST['station_id']) ? $_POST['station_id'] : 2;

//转换为标准日期格式
$checkStart = date('Y-m-d H:i:s', strtotime($checkStart));
$checkEnd = date('Y-m-d H:i:s', strtotime($checkEnd));

//如果查询时间非当天，则查找备份库
if (strtotime($checkEnd) < strtotime($today)) {
    $db = new DB(DB_LASTDAY_DRIVER, DB_LASTDAY_HOSTNAME, DB_LASTDAY_USERNAME, DB_LASTDAY_PASSWORD, DB_LASTDAY_DATABASE);
}

//计算时间间隔, 查询日期范围不可超过7天
if (intval(abs(strtotime($checkStart) - strtotime($checkEnd)) / 86400) >= 3) {
    echo '<input class="button" type="button" value="返回" onclick="javascript:history.go(-1);">';
    exit(' 查询日期范围不可超过3天');
}

//获取时间段内分拣的订单信息
$result = array();
if (sizeof($_POST)) {
    $sql = "SELECT DISTINCT
	ios.deliver_order_id
FROM
	oc_x_inventory_order_sorting ios 
WHERE
	ios.STATUS = 1
AND ios.uptime between '" . $checkStart . "' and '" . $checkEnd . "'";
    $query = $db->query($sql);
    $sortOrderList = array(0);
    foreach ($query->rows as $m) {
        $sortOrderList[] = $m['deliver_order_id'];
    }

    $sortOrderListString = implode(',', $sortOrderList);

//echo $sql;
//echo '<br />';

    $sql = "
            select O.deliver_order_id, OD.inventory_name sorting_by, V.frame_count, V.box_count, V.inv_comment,doi.inv_comment inv_comment2
            from oc_x_deliver_order O
            right join oc_order_distr OD on O.deliver_order_id = OD.deliver_order_id
            left join oc_order_inv V on O.order_id = V.order_id
            left join oc_x_deliver_order_inv doi on doi.deliver_order_id = O.deliver_order_id
            where O.deliver_order_id in (" . $sortOrderListString . ")
                and O.order_status_id in (" . $queryOrderStatus . ")
                and O.station_id = '" . $station_id . "'
                and O.do_warehouse_id = '" . $warehouse_id . "'
            group by O.order_id
        ";
    $query = $db->query($sql);
    $orderInfoList = array();
    $orderList = array(0); //有效订单号
    foreach ($query->rows as $m) {
        $orderInfoList[$m['deliver_order_id']] = $m;
        $orderList[] = $m['deliver_order_id'];
    }
    $orderListString = implode(',', $orderList);

//echo $sql.'<hr />';
//echo $orderListString.'<hr />';

//默认获取缺货的商品信息
    $sql = "
            select
            P.product_id,
            P.name,
            sum(AA.order_qty) order_qty,
            sum(if(BB.sort_qty is null, 0, BB.sort_qty)) sort_qty,
            sum(AA.order_qty) - sum(if(BB.sort_qty is null, 0, BB.sort_qty)) gap,
            group_concat(concat(AA.deliver_order_id,'||', AA.order_qty - if(BB.sort_qty is null, 0, BB.sort_qty), '||',concat(AA.order_id,AA.warehouse))) gap_list,
            P.sku,
            P.model,
            (sum(AA.order_qty) - sum(if(BB.sort_qty is null, 0, BB.sort_qty)))*AA.price gap_total,
            ptw.stock_area inv_class_sort
            from (
                select w.shortname warehouse, o.order_id, o.deliver_order_id, op.product_id, sum(op.quantity) order_qty, op.price
                from oc_x_deliver_order o left join oc_x_deliver_order_product op on o.deliver_order_id  = op.deliver_order_id
                    left join oc_x_warehouse w on o.warehouse_id = w.warehouse_id
                where o.station_id = '" . $station_id . "' and o.deliver_order_id in (" . $orderListString . ")
                group by o.deliver_order_id, op.product_id
            ) AA
            left join (
                select A.deliver_order_id, A.product_id, sum(A.quantity) sort_qty
                from oc_x_inventory_order_sorting A
                where A.deliver_order_id in (" . $orderListString . ")
                and A.status  = 1 
                group by A.deliver_order_id, A.product_id
            ) BB on AA.deliver_order_id = BB.deliver_order_id and AA.product_id = BB.product_id
            left join oc_product P on AA.product_id = P.product_id
            left join oc_product_to_warehouse ptw on ptw.product_id = P.product_id and ptw.warehouse_id = '" . $warehouse_id . "' and ptw.do_warehouse_id = '" . $warehouse_id . "'
            group by AA.product_id having gap > 0
            order by gap_total desc";
    $query = $db->query($sql);
    $result = $query->rows;

    //var_dump($sql);
}
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
            width: 100%;
            display: block;
        }
    </style>
    <title>班组长缺货未分拣处理</title>
</head>
<body>
<?php if (sizeof($_POST)) { ?>
    <?php
    $totalGap = 0;
    foreach ($result as $m) {
        $totalGap += $m['gap'];
    }
    if ($totalGap > 0) {
        $messageInfo = "时间段内共" . $totalGap . "件商品分拣缺货。";
        $messageStyle = 'style_light';
    } else {
        $messageInfo = "查询分拣时间段内无分拣缺货。";
        $messageStyle = 'style_ok';
    }
    ?>
    <div class='message <?php echo $messageStyle; ?>'>
        <?php echo '<script> alert("［测试:20180602更新显示分拣仓缺货］'. $messageInfo.'") </script>'; ?>
    </div>
<?php } ?>
<div class="custom-control-inline">
    <input class="button" type="button" value="返回" onclick="window.location.href='i.php?auth=xsj2015inv&ver=db'">&nbsp;
    <button><img src="view/image/logo.png" style="width:6em"/></button><button><?= $_COOKIE['inventory_user'] ?>：<?= $_COOKIE['warehouse_title'] ?></button>&nbsp;
    <input class="button" type="button" value="退出" onclick="javascript:logout_inventory_user();">

</div>
<div class="table-responsive-sm" id="infoList">

    <form action="#" method="post">
        <div style="margin: 3px;">
            <span>分拣开始时间<input type="date" name="checkStart" value="<?php echo date("Y-m-d",time());?>" id="date_time"/>
<!--                <input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="datetime" id="date_time" name="checkStart" value="--><?php //echo $checkStart; ?><!--">-->
            </span>
        </div>
<!--        <div style="margin: 3px;">-->
<!--            <span>分拣结束时间<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="datetime"-->
<!--                               name="checkEnd" value="--><?php //echo $checkEnd; ?><!--"></span>-->
<!--        </div>-->
        <div style="padding: 3px;">
                    <span style="padding: 0 5px;">
                       状态
                       <select name="orderStatus" style="font-size: 1rem; ">
                           <option value="99" <?php if ($orderStatus == 99) {
                               echo "selected='selected'";
                           } ?>>待审核及已拣完</option>
                           <option value="8" <?php if ($orderStatus == 8) {
                               echo "selected='selected'";
                           } ?>>仅待审核</option>
                           <option value="6" <?php if ($orderStatus == 6) {
                               echo "selected='selected'";
                           } ?>>仅已拣完</option>
                       </select>
                        <input class="button" type="submit" value="查询">
                    </span>

        </div>

        <div class="row">

        <div class="col-4">
            <a type="button" id="foldList"
               class="btn btn-outline-info btn-sm ">
                <i class="icon-th"></i>折叠列表
            </a>
        </div>

        </div>
    </form>
    <?php if (sizeof($result)) { ?>
        <table class="table table-sm table-striped table-hover" id="productsHold">
            <thead>
            <tr>
                <td scope="col">商品号</td>
                <td scope="col">商品名称</td>
                <td scope="col">总订货</td>
                <td scope="col">未出库</td>
                <td scope="col">操作</td>
            </tr>
            </thead>
            <tbody class="col-sm-10" >

            <?php foreach ($result as $m) { ?>
                <tr>
                    <td><?php echo $m['product_id']; ?></td>
                    <td><?php echo $m['name'] . '<br /><span class="font08rem">[条码' . $m['sku'] . '][货位' . $m['inv_class_sort'] . ']</span>'; ?></td>
                    <td><?php echo $m['order_qty']; ?></td>
                    <td><?php echo $m['gap']; ?></td>
                    <td><a class="btn btn-success" onclick="javascript:check_product(<?php echo $m['product_id']; ?>,<?php echo "'".$m['inv_class_sort']."'"; ?>);">处理</a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
<div class="showData"></div>
<hr />
<!--列表-->
<div class="table-responsive-sm" id="begin_storage">
    处理分拣缺货日期：<input type="date" value="<?php echo date("Y-m-d",time());?>" id="date_add"/>
    <input type="button" value="查询"  onclick="get_check_product()"/>
    <table class="table table-sm table-striped table-hover">
        <thead>
        <tr>
            <th scope="col">处理日期</th>
            <th scope="col">商品</th>
            <th scope="col">货位</th>
            <th scope="col">处理结果</th>
            <th scope="col">处理人</th>
            <th scope="col">处理状态</th>
            <th scope="col">操作</th>
        </tr>
        </thead>
        <tbody class="col-sm-12" id="show_list">
        </tbody>
    </table>
</div>
</body>
<script>
    var global = {};
    global.foreman_check_results = {};
    $(document).ready(function () {
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'get_foreman_check_results',
            },
            success: function (response) {
                var jsonData = $.parseJSON(response);
                if (jsonData.return_code != "SUCCESS") {
                    alert(jsonData.return_msg);
                    return true;
                }
                $.each(jsonData.return_data, function (k, vv) {
                    global.foreman_check_results[vv.check_result_id] = vv;
                });
            }
        });
    });
    $("#foldList").on('click',function () {
        $("#productsHold").toggle('slow');
    });
    function check_product(product_id,stock_area,check_result) {
        var user_id = '<?php echo $_COOKIE['inventory_user_id']; ?>';
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id']; ?>';
        var check_date = $("#date_time").val();
        var check_status = check_result>0?1:0;
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'foreman_check_product',
                data: {
                    user_id: user_id,
                    product_id: product_id,
                    warehouse_id: warehouse_id,
                    stock_area: stock_area,
                    check_date: check_date,
                    check_status: check_status,

                },
            },
            success: function (response) {
                var jsonData = $.parseJSON(response);
                // initParam(1);
                // get_check_product();
                if (jsonData.return_code != "SUCCESS") {
                    alert(jsonData.return_msg);
                    return true;
                }
                if (jsonData.return_msg == "OK") {
                    var data = jsonData.return_data;
                    if (confirm("该商品今天"+data.date_modify+"已被"+data.username+"处理过，是否需要重新处理？")){
                        // check_product(product_id,stock_area,1);
                        // return true;
                    } else {
                        return true;
                    }
                }
                showModel(product_id,stock_area, true);
            }
        });
    }

    function get_check_product() {
        var user_id = '<?php echo $_COOKIE['inventory_user_id']; ?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id']; ?>';
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id']; ?>';
        var check_date = $("#date_add").val();
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'get_foreman_check_product',
                data: {
                    user_id: user_id,
                    user_group_id: user_group_id,
                    warehouse_id: warehouse_id,
                    check_date: check_date,

                },
            },
            success: function (response) {
                var jsonData = $.parseJSON(response);
                if (jsonData.return_code != "SUCCESS") {
                    alert(jsonData.return_msg);
                    return true;
                }
                if (jsonData.return_msg == "OK") {
                    alert(jsonData.return_data);
                }
                var tr = '';
                $.each(jsonData.return_data, function (k, vv) {
                    tr += '<tr id=pro_' + vv.check_product_id + '>';
                    tr += '<td>' + vv.date_modify + '</td>';
                    tr += '<td>' + vv.name + '<span class="badge badge-pill badge-warning">' + vv.product_id + '</span></td>';
                    tr += '<td>' + vv.stock_area + '</td>';
                    tr += '<td>' + vv.result_memo + '</td>';
                    tr += '<td>' + vv.username + '</td>';
                    tr += '<td>' + vv.status_memo + '</td>';
                    if (parseInt(vv.check_satus_id)==2) {
                        tr += '<td><a  onclick="check_confirm_result(1,'+vv.product_id+','+ vv.check_product_id + ');" class="btn btn-success" href="#">重新处理</a></td>';
                    } else {
                        tr += '<td><a  onclick="check_confirm_result('+parseInt(vv.check_result_id)+','+vv.product_id+','+ vv.check_product_id + ');" class="btn btn-success" href="#">处理</a></td>';
                    }
                    tr += '</tr>';
                });

                //添加到列表
                $('#show_list').html(tr);
            }
        });//查询结束
    }


    function updateStatu(stock_area,product_id) {
        $("#check_stock_id").val('');
        if (stock_area == '') {
            return true;
        }

        var reg = /(^[A-Z]+(\d{2,3})$)|(^[A-Z]+(\d{2,3})-(\d{2,3})$)|(^[A-Z]+(\d{2,3})-(\d{2,3})-(\d{2,3})$)|(^[A-Z]+(\d{2,3})-(\d{2,3})-(\d{2,3})-(\d{2,3})$)/;
        var from_area_id = $("#from_area_id").val();
        if (reg.test(stock_area)) {
            if (from_area_id.trim() == stock_area.trim()) {
                check_confirm_result(1,product_id);
                return false;
            } else {
                alert('货位号不一致');
                $("#show_from_area_id").text('货位号不一致');
            }

        } else {
            alert('输入错误');
            $("#show_from_area_id").text('输入错误');
        }
    }

    /*
    *删除方法
    * */
    function showID(id) {
        $.post('put_away_dispose.php/delete', {id: id, flag: '3', type_id: typeId}, function (data) {
            var str = $.parseJSON(data);
            if (str) {
                alert('删除成功!');
                $("#pro_" + id).remove();
            } else {
                alert('删除失败!');
            }
        });
    }

    //modal弹出方法中要展示的一条数据
    function check_confirm_result(type,product_id,check_product_id) {
        var user_id = '<?php echo $_COOKIE['inventory_user_id']; ?>';
        var user_group_id = '<?php echo $_COOKIE['user_group_id']; ?>';
        var warehouse_id = '<?php echo $_COOKIE['warehouse_id']; ?>';
        var check_date = $("#date_time").val();
        var message = '';
        var wrong_url = '';
        var wrong_type = 0;
        var right_type = 0;
        // console.log(global.foreman_check_results);
        // console.log(type);
        if (typeof(global.foreman_check_results[type])!= undefined) {
            var check_results = global.foreman_check_results[type];
        } else {
            alert("数据有误，请刷新页面后重试");
            return true;
        }

        wrong_type = parseInt(check_results.wrong_check_result_id);
        right_type = parseInt(check_results.right_check_result_id);
        message = right_type == 0 && wrong_type == 0 ? check_results.check_question : check_results.check_question+"，是点击'确定',否点击'取消'";
        switch (type) {
            case 9:
                wrong_url = 'i.php?auth=xsj2015inv&param=productSection';
                break;
            case 4:
                wrong_url = 'i.php?auth=xsj2015inv&param=inventoryCheckSingle';
                break;
        }
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'submit_foreman_check_product',
                data: {
                    user_id: user_id,
                    user_group_id: user_group_id,
                    warehouse_id: warehouse_id,
                    check_result: type,
                    check_date: check_date,
                    product_id: product_id,
                    check_product_id: check_product_id,

                },
            },
            success: function (response) {
                var jsonData = $.parseJSON(response);
                if (right_type == 0 && wrong_type == 0) {
                    alert(message);
                    if (wrong_url != '') {
                        window.location = wrong_url;
                    }
                } else {
                    if (confirm(message)) {
                        if (right_type>0) {
                            check_confirm_result(right_type,product_id);
                        }
                    } else {
                        if (wrong_type>0) {
                            check_confirm_result(wrong_type,product_id);
                        }
                    }
                }
            }
        });

    }
    function getTableName(tableName, id, findId) {
        $.post('put_away_dispose.php/find', {tableName: tableName, id: id, findId: findId, flag: '4'}, function (data) {
            alert(data);
        });
    }

    function clearValue() {
        $("input[name='to_area_id']").val('');
        $("input[name='from_area_id']").val('');
        $("input[name='product_id']").val('');
        $("input[name='quantity']").val('');
    }


    function showModel(id,stock_area, openModel=true) {
        $('.showData').html("<div id=\"list\" class=\"container fluid\">\n" +
            "<div id=\"show_model\" class=\"row\">\n" +
            "</div>\n" +
            "        </div>");
        $("#show_model").html("" +
            "                <!-- Modal -->\n" +
            "                <div class=\"modal fade\" id=\"exampleModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">\n" +
            "                    <div class=\"modal-dialog\" role=\"document\">\n" +
            "                        <div  class=\"modal-content\">\n" +
            "                            <!--计算器-->\n" +
            "                            <div id=\"show_info\" class=\"modal-header\">\n" +
            "                            </div>\n" +
            "                            <div class=\"modal-body\">\n" +
            "                                    <div class=\"container\">\n" +
            "                                        <div class=\"row\">\n" +
            "                                            <div class=\"input-group sm-3\">\n" +
            "                                                <div class=\"input-group-prepend\">\n" +
            "                                                    <span class=\"input-group-text\" id=\"basic-addon1\">货位号</span>\n" +
            "                    <input  type=\"text\" id=\"from_area_id\"  name=\"from_area_id\" class=\"form-control\" disabled value="+stock_area+" >\n" +
            "                                                </div>\n" +
            "                    <br /><input onkeyup=\"updateStatu(this.value,"+id+")\"  type=\"text\" id=\"check_stock_id\"  name=\"to_area_id\" class=\"form-control\" placeholder=\"请扫描货位号\" >\n" +
            "                                            </div>\n" +
            "                                        </div>\n" +
            "                                    </div>\n" +
            "                            </div>\n" +
            "                        </div>\n" +
            "                    </div>\n" +
            "                </div>\n" +
            "");

        if (openModel == true) {
            $("#exampleModal").modal('show');
        }
        $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var recipient = button.data('whatever');// Extract info from data-* attributes
            var modal = $(this);
            modal.find('.modal-title').text('New message to ' + recipient);
            modal.find('.modal-body input').val(recipient);
        });


    };

    /**
     * @param header 标题
     * @param content 内容
     */
    function showAlertSuccess(tally, header, content, auto=false) {
        $(tally).html("<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">\n" +
            "                                            <strong>" + header + "!</strong> " + content + ".\n" +
            "                                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
            "                                                <span aria-hidden=\"true\">&times;</span>\n" +
            "                                            </button>\n" +
            "                                        </div>");
        $(tally).alert();
        if (auto == true) {
            setTimeout(function () {
                $(tally).hide('slow')
            }, 3000);
        }
        // setTimeout(function(){$(tally).hide('slow')}, 2000);
    }

    /*
    * 错误
    * */
    function showAlertError(tally, header, content, auto = false) {
        $(tally).html("<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">\n" +
            "                                            <strong>" + header + "!</strong> " + content + ".\n" +
            "                                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
            "                                                <span aria-hidden=\"true\">&times;</span>\n" +
            "                                            </button>\n" +
            "                                        </div>");
        $(tally).alert();
        if (auto == true) {
            setTimeout(function () {
                $(tally).hide('slow')
            }, 3000);
        }
    }
    function logout_inventory_user(){
        if(confirm("确认退出？")){
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'inventory_logout'
                },
                success : function (response , status , xhr){
                    //console.log(response);
                    window.location = 'inventory_login.php?return=i.php';
                }
            });
        }
    }
</script>
</html>