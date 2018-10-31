<?php
    require_once '../api/config.php';
    //exit('调试中,未分拣数据暂时停用');
    require_once(DIR_SYSTEM.'db.php');
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */



    $inventory_user_admin = array('alex','leibanban','wangshaokui','wuguobiao','wuguobiaosx');
    if(empty($_COOKIE['inventory_user'])){
        //重定向浏览器

        header("Location: inventory_login.php?return=w2.php");

        //确保重定向后，后续代码不会被执行
        exit;
    }

    $warehouse_id = isset($_COOKIE['warehouse_id']) ? $_COOKIE['warehouse_id'] : false;
    if(!$warehouse_id){
        exit("未设置仓库登录属性，请在分拣页面重新登录");
    }

    //当前日期
    $h_now = date("H",time());
    $today = date("Y-m-d 00:00:00", time());

    if($h_now >= 12){
        $checkStart = date("Y-m-d 02:00:00",time());
    }
    else{
        $checkStart = date("Y-m-d 17:00:00",time() - 24*3600);
    }
    $checkEnd = date("Y-m-d H:00:00",time());

    $checkStart = isset($_POST['checkStart']) ? $_POST['checkStart'] : $checkStart;
    $checkEnd = isset($_POST['checkEnd']) ? $_POST['checkEnd'] : $checkEnd;
    $displayType = isset($_POST['displayType']) ? $_POST['displayType'] : 1;
    $orderStatus = isset($_POST['orderStatus']) ? $_POST['orderStatus'] : 6;

    $queryOrderStatus = $orderStatus;
    if($orderStatus == 99){
        $queryOrderStatus = '6,8'; //同时查找两种状态
    }
    $station_id = isset($_POST['station_id']) ? $_POST['station_id'] : 2;

    //转换为标准日期格式
    $checkStart = date('Y-m-d H:i:s', strtotime($checkStart));
    $checkEnd = date('Y-m-d H:i:s', strtotime($checkEnd));

    //如果查询时间非当天，则查找备份库
    //if(strtotime($checkEnd) < strtotime($today)){
    //    $db = new DB(DB_LASTDAY_DRIVER, DB_LASTDAY_HOSTNAME, DB_LASTDAY_USERNAME, DB_LASTDAY_PASSWORD, DB_LASTDAY_DATABASE);
    //}

    //计算时间间隔, 查询日期范围不可超过7天
    if(intval(abs(strtotime($checkStart)-strtotime($checkEnd))/86400) >= 3){
        echo '<input class="button" type="button" value="返回" onclick="javascript:history.go(-1);">';
        exit(' 查询日期范围不可超过3天');
    }


    //
    $deliverDate = date("Y-m-d", time());




//获取时间段内分拣的订单信息
    $result = array();
    if(sizeof($_POST)){
        $sql = "select distinct deliver_order_id from oc_x_inventory_order_sorting where status = 1 and uptime between '".$checkStart."' and '".$checkEnd."'";
        $query = $db->query($sql);
        $sortOrderList = array(0);
        foreach($query->rows as $m){
            $sortOrderList[] = $m['deliver_order_id'];
        }
        $sortOrderListString = implode(',',$sortOrderList);

        //echo $sql;
        //echo '<br />';

        $sql = "
            select O.deliver_order_id, OD.inventory_name sorting_by, V.frame_count, V.box_count, V.inv_comment
            from oc_x_deliver_order O
            right join oc_order_distr OD on O.deliver_order_id = OD.deliver_order_id
            left join oc_order_inv V on O.order_id = V.order_id
            where O.deliver_order_id in (".$sortOrderListString.")
                and O.order_status_id in (".$queryOrderStatus.")
                and O.station_id = '".$station_id."'
                and O.do_warehouse_id = '".$warehouse_id."'
            group by O.order_id
        ";
        $query = $db->query($sql);
        $orderInfoList = array();
        $orderList = array(0); //有效订单号
        foreach($query->rows as $m){
            $orderInfoList[$m['deliver_order_id']] = $m;
            $orderList[] = $m['deliver_order_id'];
        }
        $orderListString = implode(',',$orderList);

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
                where o.station_id = '".$station_id."' and o.deliver_order_id in (".$orderListString.")
                group by o.deliver_order_id, op.product_id
            ) AA
            left join (
                select A.deliver_order_id, A.product_id, sum(A.quantity) sort_qty
                from oc_x_inventory_order_sorting A
                where A.deliver_order_id in (".$orderListString.")
                and A.status  = 1 
                group by A.deliver_order_id, A.product_id
            ) BB on AA.deliver_order_id = BB.deliver_order_id and AA.product_id = BB.product_id
            left join oc_product P on AA.product_id = P.product_id
            left join oc_product_to_warehouse ptw on ptw.product_id = P.product_id and ptw.warehouse_id = '".$warehouse_id."' and ptw.do_warehouse_id = '".$warehouse_id."'
            group by AA.product_id having gap > 0
            order by gap_total desc";

        if($displayType == 2){
            $sql = "
                select
                AA.warehouse,
                AA.deliver_order_id,
                AA.order_id,
                OD.inventory_name sorting_user,
                AA.product_id,
                P.name,
                BB.added_by sorting_by,
                AA.order_qty order_qty,
                if(BB.sort_qty is null, 0, BB.sort_qty) sort_qty,
                AA.order_qty - if(BB.sort_qty is null, 0, BB.sort_qty) gap,
                P.sku,
                P.model,
                (AA.order_qty - if(BB.sort_qty is null, 0, BB.sort_qty))*AA.price gap_total,
                ptw.stock_area  inv_class_sort
                from (
                    select w.shortname warehouse, o.order_id, o.deliver_order_id, op.product_id, sum(op.quantity) order_qty, op.price
                    from oc_x_deliver_order o left join oc_x_deliver_order_product op on o.deliver_order_id  = op.deliver_order_id
                        left join oc_x_warehouse w on o.warehouse_id = w.warehouse_id
                    where o.station_id = '".$station_id."' and o.deliver_order_id in (".$orderListString.")
                    group by o.deliver_order_id, op.product_id
                ) AA
                left join (
                    select A.deliver_order_id, A.product_id, sum(A.quantity) sort_qty, A.added_by
                    from oc_x_inventory_order_sorting A
                    where A.deliver_order_id in (".$orderListString.")
                    and A.status = 1 
                    group by A.deliver_order_id, A.product_id
                ) BB on AA.deliver_order_id = BB.deliver_order_id and AA.product_id = BB.product_id
                left join oc_order_distr OD on AA.deliver_order_id = OD.deliver_order_id
                left join oc_product P on AA.product_id = P.product_id
                left join oc_product_to_warehouse ptw on ptw.product_id = P.product_id and ptw.warehouse_id = '".$warehouse_id."' and ptw.do_warehouse_id = '".$warehouse_id."'
                group by AA.deliver_order_id, AA.product_id having gap > 0
                order by gap_total desc
            ";
        }


        $query = $db->query($sql);
        $result = $query->rows;

        //var_dump($sql);
    }
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
                font-size: 1em;
            }
            td{
                background-color:#d0e9c6;
                color: #000;
                height: 2.5em;

                border-radius: 0.2em;
                box-shadow: 0.1em rgba(0, 0, 0, 0.2);
                font-size: 2em;
            }

            th{
                padding: 0.3em;
                background-color:#8fbb6c;
                color: #000;

                border-radius: 0.2em;
                box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            }

            .button{
                font-size: 1.1rem;
                margin: 0.2rem;
                padding: 0.2rem;
                background-color:#fa6800;
                border-radius: 0.2em;
                box-shadow: 0.1em rgba(0, 0, 0, 0.5);
            }

            .message{
                width: auto;
                margin: 0.5em;
                padding: 0.5em;
                text-align: center;

                border-radius: 0.3em;
                box-shadow: 0.2em rgba(0, 0, 0, 0.2);
            }

            .style_green{
                background-color: #117700;
                border: 0.1em solid #006600;
            }

            .style_lightgreen{
                background-color: #8FBB6C;
                border: 0.1em solid #8FBB6C;
            }

            .style_gray{
                background-color:#9d9d9d;
                border: 0.1em solid #888888;
            }

            .style_red{
                background-color:#DF0000;
                border: 0.1em solid #CC0101;
            }

            .style_yellow{
                background-color:#FF6600;
                border: 0.1em solid #df8505;
            }

            .style_light{
                background-color:#fbb450;
                border: 0.1em solid #fbb450;
            }

            .style_ok{
                background-color:#ccffcc;
                border: 0.1em solid #669966;
            }

            .style_error{
                background-color:#ffff00;
                border: 0.1em solid #ffcc33;
            }

            #infoList td{
                font-size: 1rem;
            }

            .tdWhite{
                background-color: #ffffff;
            }

            .font08rem{
                font-size: 0.8rem;
            }

            .font09rem{
                font-size: 0.9rem;
            }

            .order_block{
                border:1px #666 dashed;
                margin:2px 1px;
            }

        </style>
    </head>
    <body>
        <div style="text-align: center; margin: 0 auto;">
          <?php if(sizeof($_POST)){ ?>
            <?php
            $totalGap = 0;
            foreach($result as $m){
                $totalGap += $m['gap'];
            }
            if($totalGap > 0){
                $messageInfo =  "时间段内共".$totalGap."件商品分拣缺货。";
                $messageStyle = 'style_light';
            }
            else{
                $messageInfo = "查询分拣时间段内无分拣缺货。";
                $messageStyle = 'style_ok';
            }
            ?>
            <div class='message <?php echo $messageStyle; ?>'>
                <?php echo '［测试:20180602更新显示分拣仓缺货］'.$messageInfo; ?>
            </div>
          <?php } ?>

            <div><?php echo $_COOKIE['warehouse_title'];?>-整件波次</div>
            <hr />
            <form action="#" method="post">

                <span style="padding: 0 5px;">
                       配送仓库
                       <select name="toWarehouse" style="font-size: 1rem; ">
                           <option value=0>-选择-</option>
                           <option value="14">浦东新仓</option>
                           <option value="15">苏州仓</option>
                           <option value="17">金山仓</option>
                           <option value="18">宁波仓</option>
                           <option value="19">吴江仓</option>
                           <option value="20">杭州仓</option>
                           <option value="22">嘉定仓</option>
                       </select>
                    </span>
                <div style="margin: 3px;">
                    <span>配送日期<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="datetime" name="checkStart" value="<?php echo $deliverDate; ?>"></span>
                </div>
                <input class="button" type="submit" value="查询">
                <input class="button" type="submit" value="生成波次">
            </form>
            <hr />
        </div>

    <?php if(sizeof($result)){ ?>
      <?php if($displayType == 2){ ?>
        <table id="infoList" border="0" style="width:100%;"  cellpadding=2 cellspacing=3>
            <tr>
                <td style="width: 4.2rem">订单号</td>
                <td style="width: 3rem">商品号</td>
                <td>商品名称</td>
                <td style="width: 3.2rem">商品数</td>
                <td style="width: 3.2rem">未出库</td>
                <td style="width: 3.2rem">金额</td>
            </tr>
            <?php $pivotOrderId = 0?>
            <?php foreach($result as $m){?>
                <tr>
                  <?php if($pivotOrderId == $m['deliver_order_id']){ ?>
                    <td class="tdWhite"></td>
                  <?php } else{?>
                    <td><?php
                            echo $m['deliver_order_id'] .
                                '
                                <br /><span class="font08rem">[货位'.$orderInfoList[$m['deliver_order_id']]['inv_comment'].']</span>
                                <br /><span class="font08rem">[分拣'.$m['sorting_user'].']</span>
                                <br /><span class="font08rem">['.$m['order_id'].$m['warehouse'].']</span>
                                ';
                        ?>
                    </td>
                  <?php } ?>
                    <td><?php echo $m['product_id']; ?></td>
                    <td><?php echo $m['name'] . '<br /><span class="font08rem">[分拣人:'.$m['sorting_by'].']</span>';?></td>
                    <td><?php echo $m['order_qty'];?></td>
                    <td><?php echo $m['gap'];?></td>
                    <td><?php echo round($m['gap_total'],1);?></td>
                </tr>
                <?php $pivotOrderId = $m['order_id']; ?>
            <?php } ?>
        </table>
      <?php } else{ ?>
        <table id="infoList" border="0" style="width:100%;"  cellpadding=2 cellspacing=3>
            <table id="infoList" border="0" style="width:100%;"  cellpadding=2 cellspacing=3>
                <tr>
                    <td style="width: 3rem">商品号</td>
                    <td>商品名称</td>
                    <td style="width: 5rem">订单分布</td>
                    <td style="width: 3.2rem">总订货</td>
                    <td style="width: 3.2rem">未出库</td>
                    <td style="width: 3.2rem">金额</td>
                </tr>
                <?php foreach($result as $m){?>
                    <tr>
                        <td><?php echo $m['product_id']; ?></td>
                        <td><?php echo $m['name'].'<br /><span class="font08rem">[条码'.$m['sku'].'][货位'.$m['inv_class_sort'].']</span>';?></td>
                        <td>
                            <?php //echo $m['gap_list'] ?>
                            <?php
                                $gapInfo = '';
                                $gapList = explode(',',$m['gap_list']);
                                foreach($gapList as $n){
                                    $gapListInfo = explode('||',$n);
                                    $gapListDoOrderId = $gapListInfo[0];
                                    $gapListQty = $gapListInfo[1];
                                    $gapListOrderId = $gapListInfo[2];
                                    if($gapListQty>0){
                                        $gapInfo .= '<div class="order_block">'.$gapListDoOrderId.'['.$gapListQty.']
                                            <br /><span class="font08rem">[货位'.$orderInfoList[$gapListDoOrderId]['inv_comment'].']</span>
                                            <br /><span class="font08rem">[分拣'.$orderInfoList[$gapListDoOrderId]['sorting_by'].']</span>
                                            <br /><span class="font08rem">['.$gapListOrderId.']</span></div>';
                                    }
                                }
                            echo $gapInfo;
                            ?>
                        </td>
                        <td><?php echo $m['order_qty'];?></td>
                        <td><?php echo $m['gap'];?></td>
                        <td><?php echo round($m['gap_total'],1);?></td>
                    </tr>
                <?php } ?>
            </table>
        </table>
      <?php } ?>
    <?php } ?>
    </body>
</html>