<?php
    require_once '../../api/config.php';
    //exit('调试中,未分拣数据暂时停用');
    require_once(DIR_SYSTEM.'db.php');
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */



    $inventory_user_admin = array('alex','leibanban','wangshaokui');
    if(empty($_COOKIE['inventory_user'])){
        //重定向浏览器

        header("Location: inventory_login.php?return=w2.php");

        //确保重定向后，后续代码不会被执行
        exit;
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
    if(strtotime($checkEnd) < strtotime($today)){
        $db = new DB(DB_LASTDAY_DRIVER, DB_LASTDAY_HOSTNAME, DB_LASTDAY_USERNAME, DB_LASTDAY_PASSWORD, DB_LASTDAY_DATABASE);
    }

    //计算时间间隔, 查询日期范围不可超过7天
    if(intval(abs(strtotime($checkStart)-strtotime($checkEnd))/86400) >= 3){
        echo '<input class="button" type="button" value="返回" onclick="javascript:history.go(-1);">';
        exit(' 查询日期范围不可超过3天');
    }

    //获取时间段内分拣的订单信息
    $result = array();
    if(sizeof($_POST)){
        $sql = "select distinct order_id from oc_x_inventory_order_sorting where uptime between '".$checkStart."' and '".$checkEnd."'";
        $query = $db->query($sql);
        $sortOrderList = array(0);
        foreach($query->rows as $m){
            $sortOrderList[] = $m['order_id'];
        }
        $sortOrderListString = implode(',',$sortOrderList);

        //echo $sql;
        //echo '<br />';

        $sql = "
            select O.order_id, OD.inventory_name sorting_by, V.frame_count, V.box_count, V.inv_comment
            from oc_order O
            right join oc_order_distr OD on O.order_id = OD.order_id
            left join oc_order_inv V on O.order_id = V.order_id
            where O.order_id in (".$sortOrderListString.") and O.order_status_id in (".$queryOrderStatus.") and O.station_id = '".$station_id."'
            group by O.order_id
        ";
        $query = $db->query($sql);
        $orderInfoList = array();
        $orderList = array(0); //有效订单号
        foreach($query->rows as $m){
            $orderInfoList[$m['order_id']] = $m;
            $orderList[] = $m['order_id'];
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
            group_concat(concat(AA.order_id,'||', AA.order_qty - if(BB.sort_qty is null, 0, BB.sort_qty))) gap_list,
            P.sku,
            P.model,
            P.inv_class_sort
            from (
                select o.order_id, op.product_id, sum(op.quantity) order_qty
                from oc_order o left join oc_order_product op on o.order_id  = op.order_id
                where o.station_id = '".$station_id."' and o.order_id in (".$orderListString.")
                group by o.order_id, op.product_id
            ) AA
            left join (
                select A.order_id, A.product_id, sum(A.quantity) sort_qty
                from oc_x_inventory_order_sorting A
                where A.order_id in (".$orderListString.")
                group by A.order_id, A.product_id
            ) BB on AA.order_id = BB.order_id and AA.product_id = BB.product_id
            left join oc_product P on AA.product_id = P.product_id
            group by AA.product_id having gap > 0
        ";

        if($displayType == 2){
            $sql = "
                select
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
                P.inv_class_sort
                from (
                    select o.order_id, op.product_id, sum(op.quantity) order_qty
                    from oc_order o left join oc_order_product op on o.order_id  = op.order_id
                    where o.station_id = '".$station_id."' and o.order_id in (".$orderListString.")
                    group by o.order_id, op.product_id
                ) AA
                left join (
                    select A.order_id, A.product_id, sum(A.quantity) sort_qty, A.added_by
                    from oc_x_inventory_order_sorting A
                    where A.order_id in (".$orderListString.")
                    group by A.order_id, A.product_id
                ) BB on AA.order_id = BB.order_id and AA.product_id = BB.product_id
                left join oc_order_distr OD on AA.order_id = OD.order_id
                left join oc_product P on AA.product_id = P.product_id
                group by AA.order_id, AA.product_id having gap > 0
                order by AA.order_id, AA.product_id
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
                <?php echo $messageInfo; ?>
            </div>
          <?php } ?>

            <form action="#" method="post">
                <div style="margin: 3px;">
                    <span>分拣开始时间<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="datetime" name="checkStart" value="<?php echo $checkStart; ?>"></span>
                </div>
                <div style="margin: 3px;">
                    <span>分拣结束时间<input style="padding: 3px; font-size: 1rem; border: 1px solid #cccccc" type="datetime" name="checkEnd" value="<?php echo $checkEnd; ?>"></span>
                </div>
                <div style="padding: 3px;">
                    <span style="padding: 0 5px;">
                       状态
                       <select name="orderStatus" style="font-size: 1rem; ">
                           <option value="99" <?php if($orderStatus==99){ echo "selected='selected'"; } ?>>待审核及已拣完</option>
                           <option value="8" <?php if($orderStatus==8){ echo "selected='selected'"; } ?>>仅待审核</option>
                           <option value="6" <?php if($orderStatus==6){ echo "selected='selected'"; } ?>>仅已拣完</option>
                       </select>
                    </span>
                    <span style="padding: 0 5px;">
                       显示
                       <select name="displayType" style="font-size: 1rem; ">
                           <option value="1" <?php if($displayType==1){ echo "selected='selected'"; } ?>>按商品缺货数</option>
                           <option value="2" <?php if($displayType==2){ echo "selected='selected'"; } ?>>按订单缺货数</option>
                       </select>
                    </span>
                </div>
                <input class="button" type="button" value="返回" onclick="javascript:history.go(-1);">
                <input class="button" type="submit" value="查询">
            </form>
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
            </tr>
            <?php $pivotOrderId = 0?>
            <?php foreach($result as $m){?>
                <tr>
                  <?php if($pivotOrderId == $m['order_id']){ ?>
                    <td class="tdWhite"></td>
                  <?php } else{?>
                    <td><?php echo $m['order_id'] . '<br /><span class="font08rem">[货位'.$orderInfoList[$m['order_id']]['inv_comment'].']</span><br /><span class="font08rem">[分拣'.$m['sorting_user'].']</span>'; ?></td>
                  <?php } ?>
                    <td><?php echo $m['product_id']; ?></td>
                    <td><?php echo $m['name'] . '<br /><span class="font08rem">[分拣人:'.$m['sorting_by'].']</span>';?></td>
                    <td><?php echo $m['order_qty'];?></td>
                    <td><?php echo $m['gap'];?></td>
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
                    <td style="width: 4.5rem">订单分布</td>
                    <td style="width: 3.2rem">总订货</td>
                    <td style="width: 3.2rem">未出库</td>
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
                                    $gapListOrderId = $gapListInfo[0];
                                    $gapListQty = $gapListInfo[1];
                                    if($gapListQty>0){
                                        $gapInfo .= '<div class="order_block">'.$gapListOrderId.'['.$gapListQty.']<br /><span class="font08rem">[货位'.$orderInfoList[$gapListOrderId]['inv_comment'].']</span><br /><span class="font08rem">[分拣'.$orderInfoList[$gapListOrderId]['sorting_by'].']</span></div>';
                                    }
                                }
                            echo $gapInfo;
                            ?>
                        </td>
                        <td><?php echo $m['order_qty'];?></td>
                        <td><?php echo $m['gap'];?></td>
                    </tr>
                <?php } ?>
            </table>
        </table>
      <?php } ?>
    <?php } ?>
    </body>
</html>