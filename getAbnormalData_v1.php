<?php
require_once '../api/config.php';
require_once(DIR_SYSTEM . 'db.php');
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/10/25
 * Time: 14:36
 */
global $db;
$do_warehouse_id = isset($_POST['do_warehouse_id']) ? $_POST['do_warehouse_id'] : 21;
$warehouse_id = isset($_POST['warehouse_id']) ? $_POST['warehouse_id'] : 22;
$date_search = isset($_POST['date_search']) ? $_POST['date_search'] : 2;
$search_type = isset($_POST['search_type']) ? intval($_POST['search_type']) : 1;
$sql = "select warehouse_id, shortname warehouse from oc_x_warehouse where status = 1 and station_id = 2";
$list = $db->query($sql);
$dataWarehouseRaw = $list->rows;
foreach($dataWarehouseRaw as $m){
    $dataWarehouse[$m['warehouse_id']] = $m;
}
switch ($search_type) {
    case 1:
        $sql = "SELECT
			ios.warehouse_id,
			ios.deliver_order_id,
			ios.do_warehouse_id,
			ios.container_id ios_container,ios.deliver_date,ios.order_id,ios.container_id container_id,
            oxw.container_id diff_container							
		FROM
			(
				SELECT
					ioss.container_id,
					odo.deliver_order_id,
					odo.warehouse_id,
					odo.do_warehouse_id,
					odo.deliver_date,odo.order_id
				FROM
					oc_x_deliver_order odo
				LEFT JOIN oc_x_inventory_order_sorting ioss ON ioss.deliver_order_id = odo.deliver_order_id
				WHERE
					odo.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)				
	";
        if ($warehouse_id >0) {
            $sql .= " and odo.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql .= " and odo.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql .= "AND ioss. STATUS = 1
 				AND ioss.container_id > 0
				GROUP BY
					odo.deliver_order_id,
					ioss.container_id
			) ios 
		LEFT JOIN (
			SELECT
				doo1.deliver_order_id,
				oxwt1.container_id
			FROM
				oc_x_deliver_order doo1
			LEFT JOIN oc_x_warehouse_requisition_temporary oxwt1 ON oxwt1.relevant_id = doo1.relevant_id
			WHERE
				doo1.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)";
        if ($warehouse_id >0) {
            $sql .= " and doo1.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql .= " and doo1.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql .= " 			
	AND oxwt1.container_id > 0
			AND oxwt1.relevant_status_id = 2
			GROUP BY
				doo1.deliver_order_id,
				oxwt1.container_id
		) oxw ON oxw.deliver_order_id = ios.deliver_order_id
		AND oxw.container_id = ios.container_id";



        $sql1 = "SELECT
	batch.deliver_date,
	batch.do_warehouse_id ,
	batch.warehouse_id,
	batch.product_id,oop.name,
	batch.quantity batch_quantity,
	wrt_out.quantity type_quantity
	
FROM
	(
		SELECT
			sum(

				IF (
					bsi.plan_quantity > bss.quantity,
					bss.quantity,
					bsi.plan_quantity
				)
			) AS quantity,
			date(obs.deliver_date) deliver_date,
			bss.product_id,
			obs.do_warehouse_id,
			obs.warehouse_id
		FROM
			oc_x_batch_sorting obs
		LEFT JOIN oc_x_batch_sorting_item bsi ON bsi.batch_id = obs.batch_id
		LEFT JOIN oc_x_batch_sorting_swap bss ON bss.batch_id = bsi.batch_id
		AND bsi.product_id = bss.product_id
		WHERE
			obs.deliver_date BETWEEN DATE_SUB(
				CURRENT_DATE (),
				INTERVAL '" . $date_search . "' DAY
			)
		AND DATE_SUB(
			CURRENT_DATE (),
			INTERVAL 0 DAY
		) ";
        if ($warehouse_id >0) {
            $sql1 .= " and obs.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql1 .= " and obs.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql1 .= "
		AND obs.`status` = 6
		AND bss.`status` = 1
		GROUP BY
			obs.deliver_date,
			product_id,
			obs.do_warehouse_id,
			obs.warehouse_id
	) batch
LEFT JOIN (
	SELECT
		date(wr.deliver_date) deliver_date,
		sum(wrt.quantity) quantity,
		wrt.product_id,
		wr.from_warehouse do_warehouse_id,
		wr.to_warehouse warehouse_id
	FROM
		oc_x_warehouse_requisition wr
	LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON wrt.relevant_id = wr.relevant_id
	LEFT JOIN oc_product op1 ON op1.product_id = wrt.product_id
	WHERE
		wr.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)
	";
        if ($warehouse_id >0) {
            $sql1 .= " and wr.to_warehouse = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql1 .= " and wr.from_warehouse = '".$do_warehouse_id."' ";
        }
        $sql1 .="
	AND wr.relevant_status_id IN (4, 6)
	AND wrt.relevant_status_id = 2
	AND wr.out_type = 1
	AND op1.repack = 0
	GROUP BY
		date(wr.deliver_date),
		wrt.product_id,
		wr.from_warehouse,
		wr.to_warehouse
) wrt_out ON wrt_out.deliver_date = batch.deliver_date
AND wrt_out.do_warehouse_id = batch.do_warehouse_id
AND wrt_out.warehouse_id = batch.warehouse_id
AND wrt_out.product_id = batch.product_id";
        break;
    case 2:
        $sql = "SELECT
			ios.warehouse_id,
			ios.deliver_order_id,
			ios.do_warehouse_id,
			ios.container_id ,ios.deliver_date,ios.order_id,ios.container_id container_id,
            oxw.container_id ios_container,oxw2.container_id diff_container								
		FROM
			(
				SELECT
					ioss.container_id,
					odo.deliver_order_id,
					odo.warehouse_id,
					odo.do_warehouse_id,
					odo.deliver_date,odo.order_id
				FROM
					oc_x_deliver_order odo
				LEFT JOIN oc_x_inventory_order_sorting ioss ON ioss.deliver_order_id = odo.deliver_order_id
				WHERE
					odo.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)				
	";
        if ($warehouse_id >0) {
            $sql .= " and odo.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql .= " and odo.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql .= "
	AND ioss. STATUS = 1
 				AND ioss.container_id > 0
				GROUP BY
					odo.deliver_order_id,
					ioss.container_id
			) ios 
		LEFT JOIN (
			SELECT
				doo1.deliver_order_id,
				oxwt1.container_id
			FROM
				oc_x_deliver_order doo1
			LEFT JOIN oc_x_warehouse_requisition_temporary oxwt1 ON oxwt1.relevant_id = doo1.relevant_id
			WHERE
				doo1.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	) 			";
        if ($warehouse_id >0) {
            $sql .= " and doo1.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql .= " and doo1.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql .=" AND oxwt1.container_id > 0
			AND oxwt1.relevant_status_id = 2
			GROUP BY
				doo1.deliver_order_id,
				oxwt1.container_id
		) oxw ON oxw.deliver_order_id = ios.deliver_order_id
		AND oxw.container_id = ios.container_id
		LEFT JOIN (
			SELECT
				doo2.deliver_order_id,
				oxwt2.container_id
			FROM
				oc_x_deliver_order doo2
			LEFT JOIN oc_x_warehouse_requisition_temporary oxwt2 ON oxwt2.relevant_id = doo2.relevant_id
			WHERE
				doo2.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)			";
        if ($warehouse_id >0) {
            $sql .= " and doo2.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql .= " and doo2.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql .= "AND oxwt2.container_id > 0
			AND oxwt2.relevant_status_id = 4
			GROUP BY
				doo2.deliver_order_id,
				oxwt2.container_id
		) oxw2 ON oxw2.deliver_order_id = ios.deliver_order_id
		AND oxw2.container_id = ios.container_id ";



        $sql1 = "SELECT
	batch.deliver_date,
	batch.do_warehouse_id ,
	batch.warehouse_id,
	batch.product_id,oop.name,
	wrt_out.quantity batch_quantity,wrt_out2.quantity type_quantity
	
FROM
	(
		SELECT
			sum(

				IF (
					bsi.plan_quantity > bss.quantity,
					bss.quantity,
					bsi.plan_quantity
				)
			) AS quantity,
			date(obs.deliver_date) deliver_date,
			bss.product_id,
			obs.do_warehouse_id,
			obs.warehouse_id
		FROM
			oc_x_batch_sorting obs
		LEFT JOIN oc_x_batch_sorting_item bsi ON bsi.batch_id = obs.batch_id
		LEFT JOIN oc_x_batch_sorting_swap bss ON bss.batch_id = bsi.batch_id
		AND bsi.product_id = bss.product_id
		WHERE
			obs.deliver_date BETWEEN DATE_SUB(
				CURRENT_DATE (),
				INTERVAL '" . $date_search . "' DAY
			)
		AND DATE_SUB(
			CURRENT_DATE (),
			INTERVAL 0 DAY
		)
		";
        if ($warehouse_id >0) {
            $sql1 .= " and obs.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql1 .= " and obs.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql1 .= "
		AND obs.`status` = 6
		AND bss.`status` = 1
		GROUP BY
			obs.deliver_date,
			product_id,
			obs.do_warehouse_id,
			obs.warehouse_id
	) batch
LEFT JOIN (
	SELECT
		date(wr.deliver_date) deliver_date,
		sum(wrt.quantity) quantity,
		wrt.product_id,
		wr.from_warehouse do_warehouse_id,
		wr.to_warehouse warehouse_id
	FROM
		oc_x_warehouse_requisition wr
	LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON wrt.relevant_id = wr.relevant_id
	LEFT JOIN oc_product op1 ON op1.product_id = wrt.product_id
	WHERE
		wr.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	) ";
        if ($warehouse_id >0) {
            $sql1 .= " and wr.to_warehouse = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql1 .= " and wr.from_warehouse = '".$do_warehouse_id."' ";
        }
        $sql1 .= "
	AND wr.relevant_status_id IN (4, 6)
	AND wrt.relevant_status_id = 2
	AND wr.out_type = 1
	AND op1.repack = 0
	GROUP BY
		date(wr.deliver_date),
		wrt.product_id,
		wr.from_warehouse,
		wr.to_warehouse
) wrt_out ON wrt_out.deliver_date = batch.deliver_date
AND wrt_out.do_warehouse_id = batch.do_warehouse_id
AND wrt_out.warehouse_id = batch.warehouse_id
AND wrt_out.product_id = batch.product_id
LEFT JOIN (
	SELECT
		date(wr2.deliver_date) deliver_date,
		sum(wrt2.quantity) quantity,
		wrt2.product_id,
		wr2.from_warehouse do_warehouse_id,
		wr2.to_warehouse warehouse_id
	FROM
		oc_x_warehouse_requisition wr2
	LEFT JOIN oc_x_warehouse_requisition_temporary wrt2 ON wrt2.relevant_id = wr2.relevant_id
	LEFT JOIN oc_product op2 ON op2.product_id = wrt2.product_id
	WHERE
		wr2.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)";
        if ($warehouse_id >0) {
            $sql1 .= " and wr2.to_warehouse = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql1 .= " and wr2.from_warehouse = '".$do_warehouse_id."' ";
        }
        $sql1 .="
	AND wr2.relevant_status_id IN (6)
	AND wrt2.relevant_status_id = 4
	AND wr2.out_type = 1
	AND op2.repack = 0
	GROUP BY
		date(wr2.deliver_date),
		wrt2.product_id,
		wr2.from_warehouse,
		wr2.to_warehouse
) wrt_out2 ON wrt_out2.deliver_date = batch.deliver_date
AND wrt_out2.do_warehouse_id = batch.do_warehouse_id
AND wrt_out2.warehouse_id = batch.warehouse_id
AND wrt_out2.product_id = batch.product_id";
        break;
    case 3:
        $sql = "SELECT
			ios.warehouse_id,
			ios.deliver_order_id,
			ios.do_warehouse_id,
			ios.container_id ,ios.deliver_date,ios.order_id,ios.container_id container_id,
            oxw.container_id ios_container,oxw2.container_id diff_container								
		FROM
			(
				SELECT
					ioss.container_id,
					odo.deliver_order_id,
					odo.warehouse_id,
					odo.do_warehouse_id,
					odo.deliver_date,odo.order_id
				FROM
					oc_x_deliver_order odo
				LEFT JOIN oc_x_inventory_order_sorting ioss ON ioss.deliver_order_id = odo.deliver_order_id
				WHERE
					odo.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)				";
        if ($warehouse_id >0) {
            $sql .= " and odo.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql .= " and odo.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql .= "AND ioss. STATUS = 1
 				AND ioss.container_id > 0
				GROUP BY
					odo.deliver_order_id,
					ioss.container_id
			) ios 
		LEFT JOIN (
			SELECT
				doo2.deliver_order_id,
				oxwt2.container_id
			FROM
				oc_x_deliver_order doo2
			LEFT JOIN oc_x_warehouse_requisition_temporary oxwt2 ON oxwt2.relevant_id = doo2.relevant_id
			WHERE
				doo2.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)			";
        if ($warehouse_id >0) {
            $sql .= " and doo2.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql .= " and doo2.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql .= "AND oxwt2.container_id > 0
			AND oxwt2.relevant_status_id = 4
			GROUP BY
				doo2.deliver_order_id,
				oxwt2.container_id
		) oxw ON oxw.deliver_order_id = ios.deliver_order_id
		AND oxw.container_id = ios.container_id 
LEFT JOIN (
			SELECT
				doo3.deliver_order_id,
				doo3.container_id
			FROM
				 oc_x_inventory_deliver_order_sorting doo3 
				 LEFT JOIN oc_x_deliver_order odo3 on doo3.deliver_order_id = odo3.deliver_order_id
			WHERE
				odo3.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	) 			";
        if ($warehouse_id >0) {
            $sql .= " and odo3.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql .= " and odo3.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql .= "AND doo3.container_id > 0
			GROUP BY
				doo3.deliver_order_id,
				doo3.container_id
		) oxw2 ON oxw2.deliver_order_id = ios.deliver_order_id
		AND oxw2.container_id = ios.container_id ";




        $sql1 = "SELECT
	batch.deliver_date,
	batch.do_warehouse_id ,
	batch.warehouse_id,
	batch.product_id,oop.name,
	wrt_out.quantity batch_quantity,wrt_out2.quantity type_quantity
	
FROM
	(
		SELECT
			sum(

				IF (
					bsi.plan_quantity > bss.quantity,
					bss.quantity,
					bsi.plan_quantity
				)
			) AS quantity,
			date(obs.deliver_date) deliver_date,
			bss.product_id,
			obs.do_warehouse_id,
			obs.warehouse_id
		FROM
			oc_x_batch_sorting obs
		LEFT JOIN oc_x_batch_sorting_item bsi ON bsi.batch_id = obs.batch_id
		LEFT JOIN oc_x_batch_sorting_swap bss ON bss.batch_id = bsi.batch_id
		AND bsi.product_id = bss.product_id
		WHERE
			obs.deliver_date BETWEEN DATE_SUB(
				CURRENT_DATE (),
				INTERVAL '" . $date_search . "' DAY
			)
		AND DATE_SUB(
			CURRENT_DATE (),
			INTERVAL 0 DAY
		) ";
        if ($warehouse_id >0) {
            $sql1 .= " and obs.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql1 .= " and obs.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql1 .= "
		AND obs.`status` = 6
		AND bss.`status` = 1
		GROUP BY
			obs.deliver_date,
			product_id,
			obs.do_warehouse_id,
			obs.warehouse_id
	) batch
LEFT JOIN (
	SELECT
		date(wr2.deliver_date) deliver_date,
		sum(wrt2.quantity) quantity,
		wrt2.product_id,
		wr2.from_warehouse do_warehouse_id,
		wr2.to_warehouse warehouse_id
	FROM
		oc_x_warehouse_requisition wr2
	LEFT JOIN oc_x_warehouse_requisition_temporary wrt2 ON wrt2.relevant_id = wr2.relevant_id
	LEFT JOIN oc_product op2 ON op2.product_id = wrt2.product_id
	WHERE
		wr2.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)";
        if ($warehouse_id >0) {
            $sql1 .= " and wr2.to_warehouse = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql1 .= " and wr2.from_warehouse = '".$do_warehouse_id."' ";
        }
        $sql1 .= "
	AND wr2.relevant_status_id IN (6)
	AND wrt2.relevant_status_id = 4
	AND wr2.out_type = 1
	AND op2.repack = 0
	GROUP BY
		date(wr2.deliver_date),
		wrt2.product_id,
		wr2.from_warehouse,
		wr2.to_warehouse
) wrt_out ON wrt_out.deliver_date = batch.deliver_date
AND wrt_out.do_warehouse_id = batch.do_warehouse_id
AND wrt_out.warehouse_id = batch.warehouse_id
AND wrt_out.product_id = batch.product_id
LEFT JOIN (
	SELECT
odo.deliver_date,odo.do_warehouse_id,odo.warehouse_id,dos.product_id,sum(dos.quantity)quantity
	FROM
		oc_x_deliver_order odo
	LEFT JOIN oc_x_inventory_deliver_order_sorting dos ON dos.deliver_order_id = odo.deliver_order_id
where odo.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '".$date_search."' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	) ";
        if ($warehouse_id >0) {
            $sql1 .= " and odo.warehouse_id = '".$warehouse_id."' ";
        }
        if ($do_warehouse_id >0) {
            $sql1 .= " and odo.do_warehouse_id = '".$do_warehouse_id."' ";
        }
        $sql1 .= "and dos.container_id = 0 GROUP BY odo.deliver_date,odo.do_warehouse_id,odo.warehouse_id,dos.product_id
) wrt_out2 ON wrt_out2.deliver_date = batch.deliver_date
AND wrt_out2.do_warehouse_id = batch.do_warehouse_id
AND wrt_out2.warehouse_id = batch.warehouse_id
AND wrt_out2.product_id = batch.product_id";
        break;
}
$sql .= " having ios_container >0 and  ISNULL(diff_container) ";
$sql1 .= " left join oc_product oop on oop.product_id = batch.product_id having batch_quantity != type_quantity";
//var_dump($sql);
//var_dump($sql1);
$repack_product = $db->query($sql)->rows;
$box_product = $db->query($sql1)->rows;
//echo "<pre>";print_r($repack_product);echo "<pre>";exit;
$repack_products = [];
$box_products = [];
foreach ($repack_product as $value1) {
    $repack_products[$value1['deliver_order_id']]['deliver_order_id'] = $value1['deliver_order_id'];
    $repack_products[$value1['deliver_order_id']]['do_warehouse_id'] = $value1['do_warehouse_id'];
    $repack_products[$value1['deliver_order_id']]['do_warehouse_name'] = $dataWarehouse[$value1['do_warehouse_id']]['warehouse'];
    $repack_products[$value1['deliver_order_id']]['warehouse_id'] = $value1['warehouse_id'];
    $repack_products[$value1['deliver_order_id']]['warehouse_name'] = $dataWarehouse[$value1['warehouse_id']]['warehouse'];
    $repack_products[$value1['deliver_order_id']]['deliver_date'] = $value1['deliver_date'];
    $repack_products[$value1['deliver_order_id']]['order_id'] = $value1['order_id'];
    $repack_products[$value1['deliver_order_id']]['ios_num'] = empty($repack_products[$value1['deliver_order_id']]['ios_num']) ? (empty($value1['ios_container'])?0:1) : $repack_products[$value1['deliver_order_id']]['ios_num'] + (empty($value1['ios_container'])?0:1);
    $repack_products[$value1['deliver_order_id']]['diff_num'] = empty($repack_products[$value1['deliver_order_id']]['diff_num']) ? (empty($value1['diff_container'])?0:1) : $repack_products[$value1['deliver_order_id']]['ios_num'] + (empty($value1['diff_container'])?0:1);
    $repack_products[$value1['deliver_order_id']]['containers'][$value1['container_id']] = $value1;
}
foreach ($box_product as $value2) {
    $key1 = strtotime($value2['deliver_date']).'_'.$value2['warehouse_id'].'_'.$value2['do_warehouse_id'];
    $box_products[$key1]['do_warehouse_id'] = $value2['do_warehouse_id'];
    $box_products[$key1]['do_warehouse_name'] =  $dataWarehouse[$value2['do_warehouse_id']]['warehouse'];
    $box_products[$key1]['warehouse_id'] = $value2['warehouse_id'];
    $box_products[$key1]['warehouse_name'] = $dataWarehouse[$value2['warehouse_id']]['warehouse'];
    $box_products[$key1]['deliver_date'] = $value2['deliver_date'];
    $box_products[$key1]['ios_num'] = empty($box_products[$key1]['ios_num']) ? (empty($value2['batch_quantity'])?0:(int)$value2['batch_quantity']) : $box_products[$key1]['ios_num'] + (empty($value2['batch_quantity'])?0:(int)$value2['batch_quantity']);
    $box_products[$key1]['diff_num'] = empty($box_products[$key1]['diff_num']) ? (empty($value2['type_quantity'])?0:(int)$value2['type_quantity']) : $box_products[$key1]['ios_num'] + (empty($value2['type_quantity'])?0:(int)$value2['type_quantity']);
    $box_products[$key1]['products'][$value2['product_id']] = $value2;
}
//echo "<pre>";print_r($box_products);echo "<pre>";exit;

$return = [
    'return_code' => 'SUCCESS',
    'return_msg'  => '',
    'return_data' => ['repack_products'=>$repack_products,'box_products'=>$box_products,'dataWarehouse'=>$dataWarehouse]
];

echo json_encode($return);



