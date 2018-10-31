<?php
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
//require_once "common.php";
//require_once "init.php"; //微信企业号访问设置
//error_reporting(E_ALL);
//ini_set('display_errors',1);
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;
}

$date = date('Y-m-d');
$warehouse_id = isset($_REQUEST['warehouse_id']) ? (int)$_REQUEST['warehouse_id'] : 21;
$gap = isset($_REQUEST['gap']) ? (int)$_REQUEST['gap'] : 2;
$gap = $gap<=15 ? $gap : 15; //查询不超过15天
//$orderType = isset($_REQUEST['orderType']) ? (int)$_REQUEST['orderType'] : 0;
//$orderCond = " order by city,warehouse,order_date";

//仓库列表
$sql = "select warehouse_id, shortname warehouse from oc_x_warehouse where status = 1 and station_id = 2";
$list = $db->query($sql);
$dataWarehouseRaw = $list->rows;
foreach($dataWarehouseRaw as $m){
    $dataWarehouse[$m['warehouse_id']] = $m;
}
//$gap =$gap-1;


$sql ="select
        date_format(o.date_deliver,'%m%d') adate,
        count(distinct if(1, o.purchase_order_id, NULL)) poCounts,
        count(distinct if(o.status in (4,5), o.purchase_order_id, NULL)) donePoCounts,
        count(distinct pr.purchase_order_id) printPoCounts,
        count(distinct if(1, op.product_id, NULL)) skuCounts,
        count(distinct if(o.status in (4,5), smi.product_id, NULL)) doneSkuCounts,
        sum(op.quantity) skuQty,
        round(sum(smi.quantity),0) doneSkuQty,
        group_concat(distinct  if(o.status in (4,5), o.purchase_order_id, NULL)) doneOrderList,
        group_concat(distinct  pr.purchase_order_id ) printOrderList,
        group_concat(distinct  if(o.status =2, o.purchase_order_id, NULL)) issue
        from oc_x_pre_purchase_order o
        left join oc_x_pre_purchase_order_product op on o.purchase_order_id = op.purchase_order_id
        left join (select ppp.purchase_order_id from oc_x_pre_purchase_print ppp group by purchase_order_id) pr on o.purchase_order_id = pr.purchase_order_id
        LEFT JOIN oc_x_stock_move AS xsm ON xsm.purchase_order_id = o.purchase_order_id
        LEFT JOIN oc_x_stock_move_item AS smi ON xsm.inventory_move_id = smi.inventory_move_id AND smi.product_id = op.product_id
        where o.date_deliver between date_sub(current_date(), interval '".$gap."' day) and date_add(current_date(), interval 0 day)
        and o.warehouse_id = '".$warehouse_id."' and o.status in (2,4,5)
        group by o.date_deliver";
$list = $db->query($sql);
$poInvData = $list->rows;


$sql = "
    select date(ios.uptime) adate, date_format(ios.uptime,'%m%d') sortdate, ios.deliver_order_id from oc_x_inventory_order_sorting ios
    left join oc_x_deliver_order o on ios.deliver_order_id = o.deliver_order_id
    where ios.status = 1 and ios.uptime between date_sub(current_date(), interval '".$gap."' day) and date_add(current_date(), interval 1 day)
        and o.do_warehouse_id = '".$warehouse_id."'
    group by adate, ios.deliver_order_id
    ";
$list = $db->query($sql);
$sortingOrderRaw = $list->rows;
$sortingOrderData = array();
foreach($sortingOrderRaw as $m){
    $sortingOrderData[$m['adate']][] = (int)$m['deliver_order_id'];
}
//var_dump($sortingOrderData);

$sortingLeakData = array();
// 异常单处理
$sql = "SELECT group_concat(ocp.product_id,',',ocp.check_date,',',ocp.warehouse_id) as mark,ocs.memo
            from oc_x_foreman_check_product as ocp
            LEFT JOIN oc_x_foreman_check_result as ocs on ocp.check_result_id = ocs.check_result_id
            WHERE ocp.check_date between date_sub(current_date(), interval '".$gap."' day) and date_add(current_date(), interval 0 day) and ocp.warehouse_id = ".$warehouse_id." 
            group by ocp.product_id,ocp.check_date,ocp.warehouse_id ";
$foremanRaw = $db->query($sql);
//var_dump($sql);
$foremanRaws = $foremanRaw->rows;
$arr = [];
foreach($foremanRaws as $v) {
    $arr[$v['mark']] = $v;
    }   
foreach($sortingOrderData as $k=>$v){
    $doList = $v;
    if(!sizeof($v)){
        $doList = array(0);
    }
    $sql = "
        select A.product_id, A.name, P.repack,  PW.stock_area,  A.orderQtyTotal, if(B.sortQtyTotal is null, 0, B.sortQtyTotal) sortQtyTotal,  (A.orderQtyTotal-if(B.sortQtyTotal is null, 0, if(B.sortQtyTotal>A.orderQtyTotal, A.orderQtyTotal,B.sortQtyTotal))) gap
        from (
            select op.product_id, op.name, sum(op.quantity) orderQtyTotal from oc_x_deliver_order o
            left join oc_x_deliver_order_product op on o.deliver_order_id = op.deliver_order_id
            where o.deliver_order_id in (".implode(',',$v).") and o.order_status_id in (6,8)
                and o.do_warehouse_id = '".$warehouse_id."'
            group by op.product_id
        ) A
        left join (
            select ios.product_id, sum(quantity) sortQtyTotal from oc_x_inventory_order_sorting ios
            left join oc_x_deliver_order o on ios.deliver_order_id = o.deliver_order_id
            where ios.status=1 and ios.deliver_order_id in (".implode(',',$v).") and o.order_status_id in (6,8)
            group by ios.product_id
        ) B on A.product_id = B.product_id
        left join oc_product P on A.product_id = P.product_id
        left join oc_product_to_warehouse PW on A.product_id = PW.product_id and PW.warehouse_id = '".$warehouse_id."' and PW.do_warehouse_id = '".$warehouse_id."'
        where 1=1
        order by gap desc";

    //var_dump($sql); echo '<hr />';
    $list = $db->query($sql);
    $sortingLeakRaw = $list->rows;
    $sortingLeakData[$k]['orderQtyTotal'] = 0;
    $sortingLeakData[$k]['gap'] = 0;
    $sortingLeakData[$k]['doCounts'] = sizeof($v);
    $sortingLeakIssueBoxData[$k] = array();
    $sortingLeakIssueRepackData[$k] = array();
    $sortingLeakData[$k]['return_counts'] = 0;
    $sortingLeakData[$k]['return_counts_checked'] = 0;
    foreach($sortingLeakRaw as $m){
        $sortingLeakData[$k]['orderQtyTotal'] += $m['orderQtyTotal'];
        $sortingLeakData[$k]['gap'] += $m['gap'];
        $mark_1 = $m['product_id'].','.$k.','.$warehouse_id;
        if ($m['gap']>0) {
            $sortingLeakData[$k]['return_counts'] += 1;
            if (array_key_exists($mark_1,$arr)) {
                $sortingLeakData[$k]['return_counts_checked'] += 1;
            }

            if(sizeof($sortingLeakIssueBoxData[$k])<20 && $m['repack'] == 0){
                if(array_key_exists($mark_1,$arr)) {
                    $sortingLeakIssueBoxData[$k][] = '<b>'.$m['product_id'].'</b>-'.$m['name'].'<br /><b>['.$m['stock_area'].']-[订'.$m['orderQtyTotal'].']-[缺'.$m['gap'].']</b>--<b style="color:red">'.$arr[$mark_1]["memo"].'</b>';
                } else {
                    $sortingLeakIssueBoxData[$k][] = '<b>'.$m['product_id'].'</b>-'.$m['name'].'<br /><b>['.$m['stock_area'].']-[订'.$m['orderQtyTotal'].']-[缺'.$m['gap'].']</b>--<b style="color:red">未处理</b>';
                }
            }

            if(sizeof($sortingLeakIssueRepackData[$k])<20 && $m['repack'] == 1){
                if(array_key_exists($mark_1,$arr)) {
                    $sortingLeakIssueRepackData[$k][] = '<b>'.$m['product_id'].'</b>-'.$m['name'].'<br /><b>['.$m['stock_area'].']-[订'.$m['orderQtyTotal'].']-[缺'.$m['gap'].']</b>--<b style="color:red">'.$arr[$mark_1]["memo"].'</b>';
                } else {
                    $sortingLeakIssueRepackData[$k][] = '<b>'.$m['product_id'].'</b>-'.$m['name'].'<br /><b>['.$m['stock_area'].']-[订'.$m['orderQtyTotal'].']-[缺'.$m['gap'].']</b>--<b style="color:red">未处理</b>';
                }
            }
        }

    }
   

}



//退货上架
$sql_return = "
		SELECT
			date_format(rdp.date_added, '%m%d') date_added,
			rp.product_id,
			rdp.box_quantity,
			rdp.warehouse_id,pw.stock_area,
			sum(rp.quantity) return_quantity,
			sum(

				IF (
					rp.return_confirmed > 0,
					rp.quantity,
					0
				)
			) self_quantity,
			p.repack,
			p.box_size,
			rdp.in_part,
			p.name,

		IF (
			pw.do_warehouse_id = pw.warehouse_id,
			1,
			0
		) isDoWarehouse,
		w.shortname doWarehouse
	FROM
		oc_return_deliver_product rdp
	LEFT JOIN oc_return r ON r.return_id = rdp.return_id
	AND r.return_status_id != 3
	LEFT JOIN oc_return_product rp ON r.return_id = rp.return_id
	AND rp.product_id = rdp.product_id
	LEFT JOIN oc_order o ON o.order_id = r.order_id
	LEFT JOIN oc_product p ON rp.product_id = p.product_id
	LEFT JOIN oc_product_to_warehouse pw ON p.product_id = pw.product_id
	AND pw.warehouse_id = rdp.warehouse_id
	LEFT JOIN oc_x_warehouse w ON pw.do_warehouse_id = w.warehouse_id
	WHERE
		rdp.status = 1
	AND rdp.date_added between date_sub(current_date(), interval '".$gap."' day) and date_add(current_date(), interval 1 day)
	";
if ($warehouse_id) {
    $sql_return .= " AND rdp.warehouse_id = '" . $warehouse_id . "'";
}
$sql_return .= "
	AND rdp.is_repack_missing = '0'
	AND rdp.is_back = '1'
	AND r.return_reason_id IN (2, 3, 4, 5, 6)
	GROUP BY
		rdp.product_id,date_added,rdp.warehouse_id ORDER BY rdp.warehouse_id,date_added
	";
$return_data_raw = $db->query($sql_return)->rows;
$return_data = [];
if (!empty($return_data_raw)) {
    foreach ($return_data_raw as $v1){
        $return_data[$v1['date_added'].$v1['warehouse_id']]['return_num'] = empty($return_data[$v1['date_added'].$v1['warehouse_id']]['return_num'])?intval($v1['return_quantity']):intval($v1['return_quantity'])+intval($return_data[$v1['date_added'].$v1['warehouse_id']]['return_num']);
        $return_data[$v1['date_added'].$v1['warehouse_id']]['shelf_num'] = empty($return_data[$v1['date_added'].$v1['warehouse_id']]['shelf_num'])?intval($v1['self_quantity']):intval($v1['self_quantity'])+intval($return_data[$v1['date_added'].$v1['warehouse_id']]['shelf_num']);
        $return_data[$v1['date_added'].$v1['warehouse_id']]['date_added'] = $v1['date_added'];
        $return_data[$v1['date_added'].$v1['warehouse_id']]['warehouse_id'] = $v1['warehouse_id'];
        if (intval($v1['return_quantity'])!= intval($v1['self_quantity'])) {
            $return_data[$v1['date_added'].$v1['warehouse_id']]['product'][] = $v1;
        }
    }
}

//货位核查
$sql_check = "select oo.order_id ,oi.inv_comment,
                      ocl.new_inv_comment ,
                      od.inventory_name ,
                      ocl.add_user,
                      oclr.reason_name,oo.warehouse_id,date_format(oo.deliver_date, '%m%d') date_added,ocl.reasons 
              from oc_order oo 
              left join oc_order_inv  oi on oo.order_id = oi.order_id
              left join oc_x_order_check_location  ocl on oo.order_id = ocl.order_id
              LEFT JOIN oc_x_order_check_location_reason oclr ON   ocl.reasons = oclr.check_location_reason_id
              LEFT JOIN (SELECT
		odr.order_id,
		group_concat(odr.inventory_name) inventory_name
	FROM
		oc_order_distr odr
	LEFT JOIN oc_order o ON o.order_id = odr.order_id
	WHERE
		DATE(o.deliver_date) between date_sub(current_date(), interval '".$gap."' day) and date_add(current_date(), interval 1 day) AND o.order_type=1 AND o.order_status_id >= 6
		";
if ($warehouse_id) {
    $sql_check .= " and o.warehouse_id =  '" . $warehouse_id . "'";
}
$sql_check .= "
	GROUP BY
		order_id) od ON  od.order_id = oo.order_id
              WHERE  DATE(oo.deliver_date) between date_sub(current_date(), interval '".$gap."' day) and date_add(current_date(), interval 1 day) AND oo.order_type=1 AND oo.order_status_id >= 6  ";

if ($warehouse_id) {
    $sql_check .= " and oo.warehouse_id =  '" . $warehouse_id . "'";
}
         $sql_check  .= " GROUP BY oo.order_id ORDER BY  oo.warehouse_id,date_added ";
//var_dump($sql_check);
$check_data_raw =
//    [];
    $db->query($sql_check)->rows;
$check_data = [];
if (!empty($check_data_raw)) {
    foreach ($check_data_raw as $v1){
        $check = (!empty($v1['reasons']) && intval($v1['reasons'])== 4)?1:0;
        $check_data[$v1['date_added'].$v1['warehouse_id']]['check_num'] = empty($check_data[$v1['date_added'].$v1['warehouse_id']]['check_num'])?$check:$check+intval($check_data[$v1['date_added'].$v1['warehouse_id']]['check_num']);
        $check_data[$v1['date_added'].$v1['warehouse_id']]['order_num'] = empty($check_data[$v1['date_added'].$v1['warehouse_id']]['order_num'])?1:1+intval($check_data[$v1['date_added'].$v1['warehouse_id']]['order_num']);
        $check_data[$v1['date_added'].$v1['warehouse_id']]['check_order_num'] = empty($check_data[$v1['date_added'].$v1['warehouse_id']]['check_order_num'])?$check:$check+intval($check_data[$v1['date_added'].$v1['warehouse_id']]['check_order_num']);
        $check_data[$v1['date_added'].$v1['warehouse_id']]['date_added'] = $v1['date_added'];
        $check_data[$v1['date_added'].$v1['warehouse_id']]['warehouse_id'] = $v1['warehouse_id'];
        if (!empty($v1['reasons']) && intval($v1['reasons'])== 4) {
        } else {
            $check_data[$v1['date_added'].$v1['warehouse_id']]['order'][] = $v1;
        }

    }
}

//调拨出库
$sql = "
    select
      concat(date_format(wr.date_added,'%m%d'),'_',wr.from_warehouse,'_',wr.to_warehouse) wrKey,
      date_format(wr.date_added,'%m%d') adate,
      wr.from_warehouse doWarehouse,
      wr.to_warehouse warehouse,
      sum(if(wrp.container_id>0, wrp.quantity, 0)) repackQty,
      sum(if(wrp.container_id=0, wrp.quantity, 0)) boxQty,
      count(distinct if(wrp.container_id>0,wrp.container_id,null)) containers,
      group_concat(distinct if(wrp.container_id>0,wrp.container_id,null)) containerList,
      group_concat(distinct wrp.relevant_id order by wrp.relevant_id) relevantIds
    from oc_x_warehouse_requisition wr
    left join oc_x_warehouse_requisition_temporary wrp on wrp.relevant_id=  wr.relevant_id and wrp.relevant_status_id = 2
    where wr.date_added between date_sub(current_date(), interval {$gap} day) and  date_add(current_date(), interval 1 day)
        and wr.out_type=1 and wr.relevant_status_id != 3
  ";
if($warehouse_id){
    $sql .= " and wr.to_warehouse =".$warehouse_id;
}
$sql .= " group by wrKey";
// var_dump($sql);
$list = $db->query($sql);
$dataRaw = $list->rows;
$wrRows = array(); //统计行数
foreach($dataRaw as $i){
    $wrRows[$i['wrKey']] = $i;
}
unset($list);
unset($dataRaw);


//调拨入库
$sql = "
    select
      concat(date_format(wr.date_added,'%m%d'),'_',wr.from_warehouse,'_',wr.to_warehouse) wrKey,
      date_format(wr.date_added,'%m%d') adate,
      wr.from_warehouse doWarehouse,
      wr.to_warehouse warehouse,
      sum(if(wrp.container_id>0, wrp.quantity, 0)) repackQty,
      sum(if(wrp.container_id=0, wrp.quantity, 0)) boxQty,
      count(distinct if(wrp.container_id>0,wrp.container_id,null)) containers,
      group_concat(distinct if(wrp.container_id>0,wrp.container_id,null)) containerList,
      group_concat(distinct wrp.relevant_id order by wrp.relevant_id) relevantIds
    from oc_x_warehouse_requisition wr
    left join oc_x_warehouse_requisition_temporary wrp on wrp.relevant_id=  wr.relevant_id and wrp.relevant_status_id = 4
    where wr.date_added between date_sub(current_date(), interval {$gap} day) and  date_add(current_date(), interval 1 day)
        and wr.out_type=1 and wr.relevant_status_id != 3
  ";
if($warehouse_id){
    $sql .= " and wr.to_warehouse =".$warehouse_id;
}
$sql .= " group by wrKey";
$list = $db->query($sql);
$dataRaw = $list->rows;
$wrInRows = array(); //统计行数
foreach($dataRaw as $i){
    $wrInRows[$i['wrKey']] = $i;
}
unset($list);
unset($dataRaw);



$briefData=array();
foreach($wrRows as $k=>$v){
    $briefData[$k] = $v;
    $briefData[$k]['inRepackQty'] = isset($wrInRows[$k]) ? $wrInRows[$k]['repackQty'] : 0;
    $briefData[$k]['inBoxQty'] = isset($wrInRows[$k]) ? $wrInRows[$k]['boxQty'] : 0;
    $briefData[$k]['inContainers'] = isset($wrInRows[$k]) ? $wrInRows[$k]['containers'] : 0;
    $briefData[$k]['inContainerList'] = isset($wrInRows[$k]) ? $wrInRows[$k]['containerList'] : 0;
    $briefData[$k]['inRelevantIds'] = isset($wrInRows[$k]) ? $wrInRows[$k]['relevantIds'] : 0;
}



//收货合单投篮
$seedData = array();
foreach($wrInRows as $k=>$v){
    $seedData[$k] = $v;
    $relevantList = array();
    $relevantList = $v['relevantIds'] ? explode(',',$v['relevantIds']) : array(1);
    $inContainerList = explode(',',$v['containerList']);

    $seedRows = array();
    if(is_array($relevantList) && sizeof($relevantList)){
        $sql = "select
               group_concat(distinct if(b.container_id>0,b.container_id,null)) containerList
            from oc_x_deliver_order a
            left join oc_x_inventory_deliver_order_sorting b on a.deliver_order_id = b.deliver_order_id
            where a.relevant_id > 0 and a.relevant_id in (".implode(',',$relevantList).")";

        $list = $db->query($sql);
        $seedRows = $list->row;
    }

    $seedData[$k]['seedContainerList'] = isset($seedRows['containerList']) ? explode(',',$seedRows['containerList']) : array();
    $seedData[$k]['issueContainerList'] = array_diff($inContainerList, $seedData[$k]['seedContainerList']);
    $seedData[$k]['wrInSeedContainerCount'] = sizeof($inContainerList) - sizeof($seedData[$k]['issueContainerList']);
}
unset($list);
unset($seedRows);

?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>仓库班组日报</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
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

        #listData table caption{text-align: left; padding:0.3rem;}
        #listData table {width: 100%}


        .f08rem{font-size: 0.8rem;}
        .f09rem{font-size: 0.9rem;}

        .hide{display: none;}


        .lessGreen {background-color: #afdb76;}

        .lightGreen {background-color:#d0e9c6;}
        .nobg {background-color:#eeeeee;}
        .linkButton {width: 2.4rem; height: 1.4rem; margin: 0.3rem; padding:0.2rem; font-size: 0.9rem; color:#ffffff; border-radius: 0.2rem; background-color: #DF0000;}
    </style>
    <script>
        function loadData(){
            var warehouse_id = $("#warehouse_id").val();
            var gap = $("#gap").val();
            var url = 'operate_manager_report.php?warehouse_id='+warehouse_id+'&gap='+gap;

            location=url;
        }

        function viewInfo(id){
            $("#"+id).toggle();
            $("."+id).each(function () {
                $(this).toggle();
            });
        }
    </script>
</head>
<body>
<div style="position: fixed; top: 0.3rem; right:0.3rem; font-size: 0.8rem; z-index: 1000;"><?php echo date("Y-m-d H:i", time());?></div>
<div style="position: fixed; background-color: #FFFFFF; width: 100%; text-align: center; border-bottom: 1px #666666 solid">
    <select id="warehouse_id" name="warehouse_id">
    <option value="0">-选择仓库-</option>
    <?php
        foreach($dataWarehouse as $m){
            $selected = isset($warehouse_id) && $warehouse_id == $m['warehouse_id'] ? 'selected="selected"' : '';
            echo '<option value="'.$m['warehouse_id'].'" '.$selected.'>'.$m['warehouse'].'</option>';
        }
    ?>
    </select>

    <select id="gap" name='gap'>
        <option value="1" <?php if($gap==1){ echo 'selected="selected"'; } ?> >1天</option>
        <option value="2" <?php if($gap==2){ echo 'selected="selected"'; } ?> >2天</option>
        <option value="3" <?php if($gap==3){ echo 'selected="selected"'; } ?> >3天</option>
        <option value="4" <?php if($gap==4){ echo 'selected="selected"'; } ?> >4天</option>
        <option value="5" <?php if($gap==5){ echo 'selected="selected"'; } ?> >5天</option>
        <option value="6" <?php if($gap==6){ echo 'selected="selected"'; } ?> >6天</option>
        <option value="7" <?php if($gap==7){ echo 'selected="selected"'; } ?> >7天</option>
    </select>
    <button class="linkButton" onclick="javascript:loadData();">查看</button>
    <div><span style="font-size: 1rem">仓库班组日报</span></div>
    <div><span style="font-size: 0.8rem">[入库-上架补货-分拣-调拨-合单-出库-退货上架-结款]</span></div>

</div>
<div id="listData">
<table style="margin-top: 5rem; height: 100px; overflow: hidden;">
    <caption>》入库</caption>
    <tr>
        <th style="width: 3rem">日期</th>
        <th>计划单</th>
        <th>已收货</th>
        <th>已打印</th>
        <th>商品数</th>
        <th>已收数</th>
        <th>商品量</th>
        <th>实收量</th>
        <th>异常</th>
    </tr>
    <?php
        $i=0;
        $pid = 0;
        foreach($poInvData as $m){
            $i++;
            $thisClass = ($i%2) ? 'lightGreen' : 'nobg';
            //$thisClass='nobg';
            //$showRow = $m['soKey'] != $pid ? true : false;
            //$pid = $m['soKey'];
            //$rowSpan = $soRows[$m['soKey']];

            echo '<tr class="'.$thisClass.'">';
            echo '<td>'.$m['adate'].'</td>';
            echo '<td>'.$m['poCounts'].'</td>';
            echo '<td>'.$m['donePoCounts'].'</td>';
            echo '<td>'.$m['printPoCounts'].'</td>';
            echo '<td>'.$m['skuCounts'].'</td>';
            echo '<td>'.$m['doneSkuCounts'].'</td>';
            echo '<td>'.$m['skuQty'].'</td>';
            echo '<td>'.$m['doneSkuQty'].'</td>';
            echo '<td><button onclick="viewInfo(\'poinv_'.$m['adate'].'\')">查看</button></td>';
            echo '</tr>';

            echo '<tr id="poinv_'.$m['adate'].'" class="hide">';
            echo '<td colspan="9" class="f08rem"  style="padding-bottom: 1rem">';
            echo '未入库采购单: <br />'. implode(', ', explode(',',$m['issue']));
            echo '<hr />';
            echo '未打印采购单: <br />'. implode(', ',array_diff(explode(',',$m['doneOrderList']), explode(',',$m['printOrderList'])));;
            echo '</td>';
            echo '</tr>';
        }
    ?>
</table>
<hr />

    <table style="margin-top: 1.5rem; height: 100px; overflow: hidden;">
    <caption>》上架补货（当前显示缺货前20）</caption>
    <tr>
        <th style="width: 3rem">日期</th>
        <th style="background-color: #ccffcc">分拣仓</th>
        <th>分拣单</th>
        <th>商品数</th>
        <th>缺货数</th>
        <th>异常</th>
    </tr>
    <?php
    $i=0;
        //var_dump($sortingLeakData);
    foreach($sortingLeakData as $k=>$v){
        $i++;
        $thisClass = ($i%2) ? 'lightGreen' : 'nobg';
        echo '<tr class="'.$thisClass.'">';
        echo '<td>'.date("md",strtotime($k)).'</td>';
        echo '<td>'.$dataWarehouse[$warehouse_id]['warehouse'].'</td>';
        echo '<td>'.$v['doCounts'].'</td>';
        echo '<td>'.$v['orderQtyTotal'].'</td>';
        echo '<td>'.$v['gap'].'</td>';
        echo '<td><button onclick="viewInfo(\'sort_'.$k.'\')">查看</button></td>';
        echo '</tr>';

        echo '<tr id="sort_'.$k.'" class="hide">';
        echo '<td colspan="6" class="f09rem"  style="padding-bottom: 1rem">';
        echo '全部分拣缺货商品'.$v['return_counts'].'种,已处理'.$v['return_counts_checked'].'<br />';
        echo '[缺整件]:<br />'. implode('<br /><br />',$sortingLeakIssueBoxData[$k]);
        echo '<hr />';
        echo '[缺散件]:<br />'. implode('<br /><br />',$sortingLeakIssueRepackData[$k]);
        echo '</td>';
        echo '</tr>';
    }
    ?>
</table>
<hr />

<table style="margin-top: 1.5rem; height: 100px; overflow: hidden;">
    <caption>》分拣组（货位及订单核查）</caption>
    <tr>
        <th style="width: 3rem">日期</th>
        <th style="background-color: #ccffcc">分拣仓</th>
        <th>已分拣</th>
        <th>货位核查</th>
        <th>订单核查</th>
        <th>异常</th>
    </tr>
    <?php
    $i = 0;
    foreach ($check_data as $m) {
        $i++;
        $thisClass = ($i % 2) ? 'lightGreen' : 'nobg';

        echo '<tr class="' . $thisClass . '">';
        echo '<td>' . $m['date_added'] . '</td>';
        echo '<td>' . $dataWarehouse[$m['warehouse_id']]['warehouse'] . '</td>';
        echo '<td>' . $m['order_num'] . '</td>';
        echo '<td>' . $m['check_num'] . '</td>';
        echo '<td>' . $m['check_order_num'] . '</td>';
        echo '<td><button onclick="viewInfo(\'check_' . $m['date_added'].$m['warehouse_id'].'\')">查看</button></td>';
        echo '</tr>';
        if (!empty($m['order'])) {
            foreach ($m['order'] as $n) {
                if (intval($m['date_added'])==intval($n['date_added']) && intval($m['warehouse_id'])==intval($n['warehouse_id'])) {
                    echo '<tr class="check_'.$m['date_added'].$m['warehouse_id'].' hide">';
                    echo '<td colspan="8" class="f08rem"  style="padding-bottom: 1rem">';
                    echo '核查异常:'. '<b>订单：'.$n['order_id'].'</b>-原货位：'.$n['inv_comment'].'-新货位：'.$n['new_inv_comment'].'<b>['.$n['reason_name'].']-[分拣人'.$n['check_quantity'].']-[核查人'.$n['add_user'].']</b>';
                    echo '</td>';
                    echo '</tr>';
                }
            }
        } else {
                echo '<tr class="check_'.$m['date_added'].$m['warehouse_id'].' hide"><td>无异常</td></tr>';
            }
    }
    ?>
</table>
<hr />

    <table style="margin-top: 3rem; height: 100px; overflow: hidden;">
        <caption>》调拨</caption>
        <tr>
            <th style="width: 3rem">日期</th>
            <th>仓库</th>
            <th style="background-color: #ccffcc">分拣仓</th>
            <th>散调拨出</th>
            <th>散调拨收</th>
            <th>蓝框出</th>
            <th>蓝框收</th>
            <th>异常</th>
        </tr>
        <?php
        $i=0;
        $pid = 0;
        foreach($briefData as $m){
            $i++;
            $thisClass = ($i%2) ? 'lightGreen' : 'nobg';
            //$thisClass='nobg';
            //$showRow = $m['soKey'] != $pid ? true : false;
            //$pid = $m['soKey'];
            //$rowSpan = $soRows[$m['soKey']];

            echo '<tr class="'.$thisClass.'">';
            echo '<td>'.$m['adate'].'</td>';
            echo '<td>'.$dataWarehouse[$m['warehouse']]['warehouse'].'</td>';
            echo '<td>'.$dataWarehouse[$m['doWarehouse']]['warehouse'].'</td>';
            echo '<td>'.$m['repackQty'].'</td>';
            echo '<td>'.$m['inRepackQty'].'</td>';
            echo '<td>'.$m['containers'].'</td>';
            echo '<td>'.$m['inContainers'].'</td>';
            echo '<td><button onclick="viewInfo(\''.$m['wrKey'].'\')">查看</button></td>';
            echo '</tr>';

            echo '<tr id="'.$m['wrKey'].'" class="hide">';
            echo '<td colspan="8" class="f08rem"  style="padding-bottom: 1rem">';
            echo '异常调拨单:'. implode(', ',array_diff(explode(',',$m['relevantIds']), explode(',',$m['inRelevantIds'])));
            echo '<br />';
            echo '未入周转筐:'. implode(', ',array_diff(explode(',',$m['containerList']), explode(',',$m['inContainerList'])));
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </table>
    <hr />

    <table style="margin-top: 1.5rem; height: 100px; overflow: hidden;">
        <caption>》周转筐合单异常数据</caption>
        <tr>
            <th style="width: 3rem">日期</th>
            <th>仓库</th>
            <th style="background-color: #ccffcc">分拣仓</th>
            <th>收蓝框数</th>
            <th>蓝框投篮</th>
            <th>异常</th>
        </tr>
        <?php
        $i=0;
        foreach($seedData as $m){
            $i++;
            $thisClass = ($i%2) ? 'lightGreen' : 'nobg';

            echo '<tr class="'.$thisClass.'">';
            echo '<td>'.$m['adate'].'</td>';
            echo '<td>'.$dataWarehouse[$m['warehouse']]['warehouse'].'</td>';
            echo '<td>'.$dataWarehouse[$m['doWarehouse']]['warehouse'].'</td>';
            echo '<td>'.$m['containers'].'</td>';
            echo '<td>'.$m['wrInSeedContainerCount'].'</td>';
            echo '<td><button onclick="viewInfo(\'seed_'.$m['wrKey'].'\')">查看</button></td>';
            echo '</tr>';

            echo '<tr id="seed_'.$m['wrKey'].'" class="hide">';
            echo '<td colspan="8" class="f08rem"  style="padding-bottom: 1rem">';
            echo '未投篮周转筐:'. implode(', ',$m['issueContainerList']);
            echo '</td>';
            echo '</tr>';
        }
        ?>
    </table>

<hr />

    <table style="margin-top: 1.5rem; height: 100px; overflow: hidden;">
        <caption>》退货上架</caption>
        <tr>
            <th style="width: 3rem">日期</th>
            <th style="background-color: #ccffcc">仓库</th>
            <th>退货数</th>
            <th>上架数</th>
            <th>查看</th>
        </tr>
        <?php
        $i = 0;
        foreach ($return_data as $m) {
            $i++;
            $thisClass = ($i % 2) ? 'lightGreen' : 'nobg';

            echo '<tr class="' . $thisClass . '">';
            echo '<td>' . $m['date_added'] . '</td>';
            echo '<td>' . $dataWarehouse[$m['warehouse_id']]['warehouse'] . '</td>';
            echo '<td>' . $m['return_num'] . '</td>';
            echo '<td>' . $m['shelf_num'] . '</td>';
            echo '<td><button onclick="viewInfo(\'return_' . $m['date_added'].$m['warehouse_id'].'\')">查看</button></td>';
            echo '</tr>';
            if (!empty($m['product'])) {
                foreach ($m['product'] as $n) {
                    if (intval($m['date_added'])==intval($n['date_added']) && intval($m['warehouse_id'])==intval($n['warehouse_id'])) {
                        echo '<tr class="return_'.$m['date_added'].$m['warehouse_id'].' hide">';
                        echo '<td colspan="8" class="f08rem"  style="padding-bottom: 1rem">';
                        echo '未上架商品:'. '<b>'.$n['product_id'].'</b>-'.$n['name'].'<b>['.$n['stock_area'].']-[退'.$n['return_quantity'].']-[上架'.$n['self_quantity'].']</b>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }
            } else {
                echo '<tr class="return_'.$m['date_added'].$m['warehouse_id'].' hide"><td>无异常</td></tr>';
            }

        }
        ?>
    </table>

<hr />



</div>

<hr />
<div style="font-size: 0.85rem; margin-bottom: 0.5rem; padding: 0.3rem">
    # 2018-09-07数据测试中，<span style="color:red;">模块添加中</span>；<br />
    # 此报表反映仓库各班组工作情况；<br />
    # “入库”环节中，计划单为采购设置，计划到货的日期，数据按计划单做查找比对（<span style="color:red;">待调整</span>）；<br />
    # “上架补货”环节中，查询的是已经分拣完成或待审核的分拣单数据，查询时间为分拣日 00:00～25:59；<br />
    # 此报表反映各仓库近商品调拨收货合单情况，<span style="color:red;font-size:0.75rem">合单目前仅统计散件</span><br />
    # 点击“异常”栏目下的“查看”按钮，可以查看异常调拨单和未收货的周转筐明细，再次点击可关闭<br />
    # 篮框投篮目前仅显示周转筐数量，非商品数量。
    # <b>数据更新记录</b><br />
      <ul style="padding-left: 1rem; font-size: 0.8rem; font-style: italic;">
          <li>[2018-09-09] 修复“上架补货“中缺货计算问题，原结果查询日期节点之前分拣的部分数据未被计算（前一天分拣，隔天提交分拣完成/待审核）</li>
      </ul>
</div>

</body>
</html>