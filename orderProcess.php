<?php
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header("Content-Type: text/html;charset=utf-8");
//$array = '';
//
//$sql = 'SELECT GROUP_CONCAT(A) order_ids from sheet2 GROUP BY B ORDER BY B';
////        $sql = "SELECT WUG.user_group_id , WU.user_id , WUG.perms ,WU.warehouse_id  FROM  oc_w_user  WU LEFT JOIN oc_w_user_group WUG ON WU.user_group_id = WUG.user_group_Id  WHERE WU.user_id = '" . $db->escape($username) . "' ";
//$user_query =$db->query($sql);
//$ads = $user_query->rows;
//var_dump($ads);
//$i = 0;
////        return $ads;
//foreach ($ads as $value) {
//    $i++;
//    $array  .= "<br /><hr />".$i."<hr />"."
//        select
//             O.order_id 订单ID,
//             date(O.date_added) 下单日期,
//             O.deliver_date 配送日期,
//             O.customer_id 用户ID,
//             C.firstname 用户名,
//             C.merchant_name 店名,
//             C.telephone 电话,
//             C.merchant_address 地址,
//             OP.product_id 商品ID,
//             OP.name 商品名,
//             OP.quantity 订购数量,
//             OP.price 单价,
//             OP.total 小计,
//             if(RT.outstock_return_qty is null, 0, RT.outstock_return_qty) 缺货数量,
//             if(RT.outstock_return_total is null, 0, RT.outstock_return_total)*-1 缺货金额,
//             if(RT.return_qty is null, 0, RT.return_qty) 用户退货数量,
//             if(RT.return_total is null, 0, RT.return_total)*-1 用户退货金额
//             from oc_order_product OP
//             left join (
//                  select A.order_id, A.return_reason_id, A.return_action_id, B.product_id, max(A.date_added) adate,
//                  sum(B.quantity) qty, B.price, sum(B.total) total, sum(B.return_product_credits) return_credits,
//                  if(A.return_reason_id in (1), B.total, 0) outstock_return_total,
//                  if(A.return_reason_id in (2,3,4), B.return_product_credits, 0) return_total,
//                  if(A.return_reason_id in (1), B.quantity, 0) outstock_return_qty,   if(A.return_reason_id in (2,3,4), B.quantity, 0) return_qty,
//                  group_concat(B.quantity) gp_qty, group_concat(B.price) gp_price, group_concat(B.total) gp_total, group_concat(B.return_product_credits) gp_return_credit
//                  from oc_return A left join oc_return_product B on A.return_id = B.return_id
//                  where  A.return_reason_id in (1, 2,3,4) and date(A.date_added)
//                  group by A.order_id, A.return_reason_id, B.product_id
//                  order by A.order_id, A.return_reason_id, B.product_id
//             ) RT on OP.order_id = RT.order_id and OP.product_id = RT.product_id
//             left join oc_order O on OP.order_id = O.order_id
//             left join oc_customer C on O.customer_id = C.customer_id
//             left join oc_product P on OP.product_id = P.product_id
//             where O.station_id = 2 and O.order_id in (".$value['order_ids']."
//             )
//             group by O.order_id, OP.product_id ORDER BY O.order_id desc ;";
//}
//var_dump($array);
//exit;
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=w_dis.php");
    //确保重定向后，后续代码不会被执行
    exit;
}

$warehouseId = isset($_REQUEST['warehouseId']) ? (int)$_REQUEST['warehouseId'] : 0;
if(!$warehouseId){
    exit('请选择指定仓库账户登录。');
}

$sql = "select
A.deliver_date,
datediff(current_date(),A.deliver_date) gap,
A.doWarehouse,
A.warehouse,
count(distinct A.order_id) orders,
count(distinct if((A.soStatus=6),A.order_id,null)) orderDone,
count(distinct if((A.soStatus!=6),A.order_id,null)) orderPending,
left(group_concat( if((A.soStatus!=6),A.order_id,null)),139) orderPendingList,

if(A.warehouse_id in (12,14),  if(A.is_repack=0,'整','散'),  '-') sortingType,
count(A.deliver_order_id) doOrders,
sum(if(A.order_status_id in (1,2),1,0)) comingOrders,
sum(if(A.order_status_id = 4,1,0)) planningOrders,
sum(if(A.order_status_id = 5,1,0)) processingOrders,
sum(if(A.order_status_id=8,1,0)) waitingOrders,
sum(if(A.order_status_id=6,1,0)) done,
sum(if(A.order_status_id not in (1,2,4,5,6,8),1,0)) others,
left(group_concat( if((A.order_status_id not in (1,2,4,5,6,8) ),A.order_id,null)),139) othersList
from
(select o.order_id, o.deliver_date, xdo.warehouse_id, xdo.do_warehouse_id, if(xdo.warehouse_id <> xdo.do_warehouse_id, 1, xdo.is_repack) is_repack, xdo.deliver_order_id, xdo.order_status_id, o.order_status_id soStatus, w2.title doWarehouse, w1.title warehouse from oc_order o
left join oc_x_deliver_order xdo on o.order_id = xdo.order_id
left join oc_x_warehouse w1 on xdo.warehouse_id = w1.warehouse_id
left join oc_x_warehouse w2 on xdo.do_warehouse_id = w2.warehouse_id
where date(o.date_added) between date_sub(current_date(), interval 9 day) and current_date()
and o.order_status_id != 3 and o.order_type = 1 and o.station_id = 2
and xdo.do_warehouse_id = ".$warehouseId."
) A
group by A.deliver_date,A.warehouse_id,A.do_warehouse_id, A.is_repack
";

$query = $db->query($sql);
$result = $query->rows;
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



        .lessGreen {background-color: #afdb76;}

        .lightGreen {background-color:#d0e9c6;}
        .nobg {background-color:#eeeeee;}
        .linkButton {width: 2.4rem; height: 1.4rem; margin: 0.3rem; padding:0.2rem; font-size: 0.9rem; color:#ffffff; border-radius: 0.2rem; background-color: #DF0000;}
        </style>
    </head>
    <body style="padding: 5px;">
        <table>
            <caption>
                当前仓库十日内订单处理情况 <?php echo date("Y-m-d H:i:s", time());?>
                <button class="linkButton" onclick="javascript:location.reload();">刷新</button>
                <button class="linkButton" style="background-color: #ffcc00; width: 4.4rem" onclick="javascript:location='orderProcess2.php';">测试新版</button>
		<button class="linkButton" style="background-color: #ffcc00; width: 4.4rem" onclick="javascript:location='keeperAllotState.php';">物流报表</button>
		<button class="linkButton" style="background-color: #ffcc00; width: 4.4rem" onclick="javascript:location='AllocationProcessState.php';">调拨报表</button>
            </caption>
            <thead>
            <tr>
                <th>配送日期</th>
                <th>分拣仓库</th>
                <th>目的仓库</th>
                <th>订单数*</th>
                <th>已拣完订单</th>
                <th style="background-color: #ffcc00">未完成订单</th>

                <th class="lessGreen">类型</th>
                <th class="lessGreen">分拣单数</th>
                <th class="lessGreen">》未分拣</th>
                <th class="lessGreen">》已分配</th>
                <th class="lessGreen">》分拣中</th>
                <th class="lessGreen">》待审核</th>
                <th class="lessGreen">》已拣完</th>
                <th class="lessGreen">》其他状态</th>
            </tr>
            </thead>
            <?php
                for($i=0;$i<sizeof($result);$i++){
                    $thisClass = ($result[$i]['gap']%2) ? 'lightGreen' : 'nobg';
            ?>
                <tr class="<?php echo $thisClass; ?>">
                    <td><?php echo $result[$i]['deliver_date'];?></td>
                    <td><?php echo $result[$i]['doWarehouse'];?></td>
                    <td><?php echo $result[$i]['warehouse'];?></td>
                    <td><?php echo $result[$i]['orders'];?></td>
                    <td><?php echo $result[$i]['orderDone'];?></td>
                    <td title="<?php echo '[列出前20单]'.$result[$i]['orderPendingList'] ;?>"><?php echo $result[$i]['orderPending'];?></td>

                    <td><?php echo $result[$i]['sortingType'];?></td>
                    <td><?php echo $result[$i]['doOrders'];?></td>
                    <td><?php echo $result[$i]['comingOrders'];?></td>
                    <td><?php echo $result[$i]['planningOrders'];?></td>
                    <td><?php echo $result[$i]['processingOrders'];?></td>
                    <td><?php echo $result[$i]['waitingOrders'];?></td>
                    <td><?php echo $result[$i]['done'];?></td>
                    <td title="<?php echo '[列出前20单]'.$result[$i]['othersList'] ;?>"><?php echo $result[$i]['others'];?></td>
                </tr>
            <?php } ?>
        </table>
        <i style="font-size: 0.9rem">*[1]一个订单有多个分拣单</i>
    </body>
</html>