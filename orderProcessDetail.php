<?php

header("Content-type:text/html;charset=utf8");
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');

$_POST['dates'] = 1;
$_POST['warehouse'] = 15;

/*取出所有的仓库*/
    if (!empty($_POST['dates'])){
        $datas=$_POST['dates'];
        $warehouse_id=$_POST['warehouse'];
        $sql1 = "SELECT
        GROUP_CONCAT(
            DISTINCT odo.deliver_order_id
        ) count_group
    FROM
        oc_x_inventory_order_sorting ios
    LEFT JOIN oc_x_deliver_order odo ON odo.deliver_order_id = ios.deliver_order_id
    WHERE
        odo.deliver_date between date_sub(current_date(), interval $datas day) and date_add(current_date(), interval 1 day) ;";
        $res1=$db->query($sql1);
        $countOrder=$res1->rows;
        $counts=$countOrder[0]['count_group'];
        if (!isset($countOrder)){
            $countOrder= array();
            echo json_encode($countOrder);
        }else{

//            $counts=$countOrder;
            $sql2="SELECT
	ox1.title outHouse,
ox2.title sortingHouse,
	oo.deliver_date distribution,
	sum(oo.ios_count) AS sortingNum,
	sum(oo.count) AS allocaNum,
	sum(oo.count1) AS allocatoutNum,
	sum(oo.count2) AS  allocatEntryNum,
	sum(oo.count3) AS shootNum,
	sum(oo.count4) AS outWarehouse
FROM
	(
		SELECT
			ios.warehouse_id,
			ios.deliver_date,
			ios.do_warehouse_id,
			count(
				DISTINCT
				IF (
					ios.container_id > 0,
					ios.container_id,
					NULL
				)
			) AS ios_count,
			count(
				DISTINCT
				IF (
					oxw.container_id > 0,
					oxw.container_id,
					NULL
				)
			) AS count,
			count(
				DISTINCT
				IF (
					oxw1.container_id > 0,
					oxw1.container_id,
					NULL
				)
			) AS count1,
			count(
				DISTINCT
				IF (
					oxw2.container_id > 0,
					oxw2.container_id,
					NULL
				)
			) AS count2,
count(
				DISTINCT
				IF (
					oxw3.container_id > 0,
					oxw3.container_id,
					NULL
				)
			) AS count3
,count(
				DISTINCT
				IF (
					oxw4.container_id > 0,
					oxw4.container_id,
					NULL
				)
			) AS count4
		FROM
			(
				SELECT
					ioss.container_id,
					odo.deliver_order_id,
					odo.warehouse_id,
					odo.do_warehouse_id,
					odo.deliver_date
				FROM
					oc_x_deliver_order odo
				LEFT JOIN oc_x_inventory_order_sorting ioss ON ioss.deliver_order_id = odo.deliver_order_id
				WHERE
				    odo.deliver_order_id IN ($counts)
				AND ioss. STATUS = 1
 				AND ioss.container_id > 0
				GROUP BY
					odo.deliver_order_id,
					ioss.container_id
			) ios
		LEFT JOIN (
			SELECT
				doo.deliver_order_id,
				oxwi.container_id
			FROM
				oc_x_deliver_order doo
			LEFT JOIN oc_x_warehouse_requisition_item oxwi ON oxwi.relevant_id = doo.relevant_id
			WHERE
 			    doo.deliver_order_id IN ($counts)
 			AND oxwi.container_id > 0
			GROUP BY
				doo.deliver_order_id,
				oxwi.container_id
		) oxw ON oxw.deliver_order_id = ios.deliver_order_id
		AND oxw.container_id = ios.container_id
		LEFT JOIN (
			SELECT
				doo1.deliver_order_id,
				oxwt1.container_id
			FROM
				oc_x_deliver_order doo1
			LEFT JOIN oc_x_warehouse_requisition_temporary oxwt1 ON oxwt1.relevant_id = doo1.relevant_id
			WHERE
 			    doo1.deliver_order_id IN ($counts)
 			AND oxwt1.container_id > 0
			AND oxwt1.relevant_status_id = 2
			GROUP BY
				doo1.deliver_order_id,
				oxwt1.container_id
		) oxw1 ON oxw1.deliver_order_id = ios.deliver_order_id
		AND oxw1.container_id = ios.container_id
		LEFT JOIN (
			SELECT
				doo2.deliver_order_id,
				oxwt2.container_id
			FROM
				oc_x_deliver_order doo2
			LEFT JOIN oc_x_warehouse_requisition_temporary oxwt2 ON oxwt2.relevant_id = doo2.relevant_id
			WHERE
 			    doo2.deliver_order_id IN ($counts)
 			AND oxwt2.container_id > 0
			AND oxwt2.relevant_status_id = 4
			GROUP BY
				doo2.deliver_order_id,
				oxwt2.container_id
		) oxw2 ON oxw2.deliver_order_id = ios.deliver_order_id
		AND oxw2.container_id = ios.container_id
LEFT JOIN (
			SELECT
				doo3.deliver_order_id,
				doo3.container_id
			FROM
				 oc_x_inventory_deliver_order_sorting doo3 
			WHERE
 			    doo3.deliver_order_id IN ($counts)
 			AND doo3.container_id > 0
			GROUP BY
				doo3.deliver_order_id,
				doo3.container_id
		) oxw3 ON oxw3.deliver_order_id = ios.deliver_order_id
		AND oxw3.container_id = ios.container_id
LEFT JOIN (
			SELECT
				doo3.deliver_order_id,
				ocf.container_id
			FROM
				oc_x_deliver_order doo3
			LEFT JOIN oc_x_container_fast_move ocf ON ocf.order_id = doo3.order_id
			WHERE
 			    doo3.deliver_order_id IN ($counts)
 			AND ocf.container_id > 0 
and ocf.move_type = 1
			GROUP BY
				doo3.deliver_order_id,
				ocf.container_id
		) oxw4 ON oxw4.deliver_order_id = ios.deliver_order_id
		AND oxw4.container_id = ios.container_id
		GROUP BY
			ios.deliver_order_id
	) oo 
LEFT JOIN oc_x_warehouse ox1 on ox1.warehouse_id = oo.warehouse_id
LEFT JOIN oc_x_warehouse ox2 on ox2.warehouse_id = oo.do_warehouse_id ";
            if ((int)$warehouse_id != '99'){
                $sql2.="where oo.warehouse_id = $warehouse_id";
            }

            $sql2.=" GROUP BY
        oo.warehouse_id,
        oo.do_warehouse_id,
        oo.deliver_date
    ORDER BY
        oo.warehouse_id,
    oo.deliver_date,
        oo.do_warehouse_id";
//        echo $sql2;
            $res2=$db->query($sql2);
            //$list=$res2->rows;
            //echo json_encode($list);
        }


    }

var_dump($sql2);