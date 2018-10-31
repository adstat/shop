<?php
header("Content-type:text/html;charset=utf8");
//return $_POST;

require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
if (!empty($_POST['go_date'])) {
    $go_date = $_POST['go_date'];
    $to_date = $_POST['to_date'];
    $to_host = $_POST['to_host'];
    $opt = $_POST['opt'];
//    return $opt;
    switch ($opt){
        case $opt == 'fen':
            $sql="SELECT
                    gg.order_id,
                    gg.deliver_date,
                    COUNT(DISTINCT gg.order_id) count_orderId,
                    gg.warehouse_id,
                    gg.shortname,
                    SUM(IF(gg.order_deliver_status_id=1 AND ISNULL(gg.is_null),1,0)) count_null_allot,
                    SUM(IF(gg.order_deliver_status_id=1 AND gg.is_null >0,1,0)) count_over_allot,
                    SUM(IF(gg.order_deliver_status_id=2,1,0)) count_on_allot,
                    SUM(IF(gg.order_deliver_status_id=7,1,0)) count_allot_fairly,
                    SUM(IF(gg.order_deliver_status_id in(3,6),1,0)) count_achieve_allot,
                    SUM(IF(gg.order_deliver_status_id=3 AND gg.is_null_return,1,0)) count_sales_return
                FROM
                    (
                        SELECT
                            a.order_id,
                            a.order_deliver_status_id,
                            a.date_added,
                            a.warehouse_id,
                            a.deliver_date,
                            d.order_id is_null,
                            e.order_id is_null_return,
                            f.shortname
                            
                FROM
                    `oc_order` a
                LEFT JOIN oc_x_logistic_allot_order d ON a.order_id = d.order_id
                LEFT JOIN oc_return e ON a.order_id=e.order_id AND e.return_reason_id = 2
                LEFT JOIN oc_x_warehouse f ON a.warehouse_id=f.warehouse_id
                WHERE
                   
                    a.order_status_id = 6
                AND date(a.deliver_date) between '".$go_date."' and  '".$to_date."' 
                AND a.order_type = 1
                AND a.station_id = 2";
            if ($to_host != '99'){
                $sql .= "
                            AND a.warehouse_id = $to_host ";
            }
            $sql .= " GROUP BY a.order_id
                    ) AS gg
                GROUP BY 
                    gg.warehouse_id,
                    gg.deliver_date";
//            echo $sql;die;
            $res=$db->query($sql);
            $info=$res->rows;
            /*sql3,未拣完*/
            $sql3="SELECT
	A.shortname,
	A.deliver_date,
	A.warehouse_id,
	COUNT(DISTINCT A.order_id) count_unfinished_oid
 FROM(
		SELECT
			a.order_id,
			a.warehouse_id,
			a.order_status_id,
			a.order_deliver_status_id,
			a.deliver_date,
			count(a.order_id) count_oid,
			d.shortname
		FROM
			oc_order a
		LEFT JOIN oc_x_warehouse d ON a.warehouse_id = d.warehouse_id
		WHERE
			a.order_status_id  NOT IN(3,6)";
            $sql3.=" AND date(a.deliver_date) BETWEEN '".$go_date."' AND  '".$to_date."'
			AND a.order_type = 1
			AND a.station_id = 2";

            if ($to_host != '99'){
                $sql3 .= " AND a.warehouse_id = $to_host";
            }
            $sql3.=" GROUP BY
			a.order_id
	) A
GROUP BY
A.warehouse_id,A.deliver_date";
//            echo $sql3;die;
            $res3=$db->query($sql3);
            $info3=$res3->rows;
            $sql4="SELECT
	COUNT(DISTINCT A.order_id) null_single,
	A.deliver_date,
	A.warehouse_id
FROM
	(
		SELECT
			a.order_id,
			a.deliver_date,
			a.warehouse_id
		FROM
			oc_order a
		LEFT JOIN oc_x_deliver_order b ON a.order_id = b.order_id
		WHERE
			b.order_status_id = 6
		AND a.order_status_id=5";
            $sql4.=" AND date(a.deliver_date) BETWEEN '".$go_date."' AND  '".$to_date."'
        AND a.order_type = 1
		AND a.station_id = 2";

            if ($to_host != '99'){
                $sql4 .= " AND a.warehouse_id = $to_host";
            }
            $sql4 .= " ORDER BY
			a.order_id
	) A
GROUP BY
	A.deliver_date,
	A.warehouse_id";
            $res4=$db->query($sql4);
            $info4=$res4->rows;
            $_arr=[];
            $arr_=[];
            $arr=[];
            foreach ($info3 as $v){
                $_arr[$v['deliver_date'].'@'.$v['warehouse_id']]=$v;
            }
            foreach ($info4 as $k4=>$v4) {
                $arr[$v4['deliver_date'].'@'.$v4['warehouse_id']]=$v4;
            }
            foreach ($info as $v2) {
                $arr_[$v2['deliver_date'].'@'.$v2['warehouse_id']]=$v2;
                $arr_[$v2['deliver_date'].'@'.$v2['warehouse_id']]['count_unfinished_oid']=empty($_arr[$v2['deliver_date'].'@'.$v2['warehouse_id']])?0:$_arr[$v2['deliver_date'].'@'.$v2['warehouse_id']]['count_unfinished_oid'];
                $arr_[$v2['deliver_date'].'@'.$v2['warehouse_id']]['null_single']=empty($arr[$v2['deliver_date'].'@'.$v2['warehouse_id']])?0:$arr[$v2['deliver_date'].'@'.$v2['warehouse_id']]['null_single'];
            }
//            var_dump($arr_);die;
            echo json_encode($arr_);
            break;
        case $opt =='good':
            $sql="SELECT
	gg.order_id,
	COUNT(DISTINCT gg.order_id) count_orderId,
	gg.deliver_date,
	gg.warehouse_id,
	gg.shortname,
	SUM(IF(gg.order_deliver_status_id=1 AND ISNULL(gg.is_null),1,0)) count_null_allot,
    SUM(IF(gg.order_deliver_status_id=1 AND gg.is_null >0,1,0)) count_over_allot,
    SUM(IF(gg.order_deliver_status_id=2,1,0)) count_on_allot,
    SUM(IF(gg.order_deliver_status_id=7,1,0)) count_allot_fairly,
    SUM(IF(gg.order_deliver_status_id in(3,6),1,0)) count_achieve_allot,
    SUM(IF(gg.order_deliver_status_id=3 AND gg.is_null_return>0,1,0)) count_sales_return,
    
	SUM(IF(gg.order_deliver_status_id=1 AND ISNULL(gg.is_null),zheng,0)) null_allot_z,
	SUM(IF(gg.order_deliver_status_id=1 AND ISNULL(gg.is_null),san,0)) null_allot_s,
	SUM(IF(gg.order_deliver_status_id=1 AND gg.is_null >0,zheng,0)) over_allot_z,
	SUM(IF(gg.order_deliver_status_id=1 AND gg.is_null >0,san,0)) over_allot_s,
	SUM(IF(gg.order_deliver_status_id=2,zheng,0)) on_allot_z,
	SUM(IF(gg.order_deliver_status_id=2,san,0)) on_allot_s,
	SUM(IF(gg.order_deliver_status_id=3,zheng,0)) achieve_allot_z,
	SUM(IF(gg.order_deliver_status_id=3,san,0)) achieve_allot_s,
	SUM(IF(gg.order_deliver_status_id=7,zheng,0)) allot_fairly_z,
	SUM(IF(gg.order_deliver_status_id=7,san,0)) allot_fairly_s
FROM
	(
		SELECT
			a.order_id,
			a.order_deliver_status_id,
			a.date_added,
			a.warehouse_id,
			a.deliver_date,
			b.frame_count san,
			b.box_count zheng,
			d.order_id is_null,
			e.order_id is_null_return,
			f.shortname
			
FROM
	`oc_order` a
LEFT JOIN oc_order_inv b ON a.order_id = b.order_id
LEFT JOIN oc_x_logistic_allot_order d ON a.order_id = d.order_id
LEFT JOIN oc_return e ON a.order_id=e.order_id and e.return_reason_id = 2
LEFT JOIN oc_x_warehouse f ON a.warehouse_id=f.warehouse_id
WHERE
	a.order_status_id = 6
AND date(a.deliver_date) between '".$go_date."' AND  '".$to_date."'
AND a.order_type = 1
AND a.station_id = 2";
if ($to_host != '99'){
                $sql .= " AND a.warehouse_id = $to_host";
            }

$sql.=" GROUP BY a.order_id
	) AS gg
GROUP BY 
	gg.warehouse_id,
	gg.deliver_date";
//            echo $sql;die;
            $res=$db->query($sql);
            $info=$res->rows;
//            echo json_encode($res);
            /*sql2,退货数*/
            $sql2="SELECT
	g2.order_id,
	COUNT(DISTINCT g2.order_id) count_orderId,
	g2.deliver_date,
	g2.warehouse_id,
	SUM(IF(g2.order_deliver_status_id=3 AND  g2.is_null_return >0,zheng,0)) sales_return_z,
	SUM(IF(g2.order_deliver_status_id=3 AND g2.is_null_return >0,san,0)) sales_return_s
FROM
	(
		SELECT
			a.order_id,
			a.order_deliver_status_id,
			a.date_added,
			a.warehouse_id,
			a.deliver_date,
			SUM(IF(e.repack = 1, d.quantity, 0)) san,
			SUM(IF (e.repack = 0, d.quantity, 0)) zheng,
			c.order_id is_null_return,
			d.quantity return_num
		FROM
			`oc_order` a
		LEFT JOIN oc_return c ON a.order_id = c.order_id 
		LEFT JOIN oc_return_product d ON c.return_id = d.return_id
		LEFT JOIN oc_product e ON d.product_id = e.product_id
		WHERE
			a.order_status_id =6
			AND c.return_reason_id = 2
		AND date(a.deliver_date) between '".$go_date."' AND  '".$to_date."'
		AND a.order_type = 1
		AND a.station_id = 2";
            if ($to_host != '99'){
                $sql2 .= " AND a.warehouse_id = $to_host";
            }
		$sql2.= " GROUP BY
			a.order_id
	) AS g2
GROUP BY
g2.warehouse_id,
g2.deliver_date";
//            echo $sql2;die;
            $res2=$db->query($sql2);
            $info2=$res2->rows;
            /*sql3,未拣完*/
            $sql3="SELECT
	A.shortname,
	A.deliver_date,
	A.warehouse_id,
	COUNT(DISTINCT A.order_id) count_unfinished_oid,
	SUM(IF(A.order_deliver_status_id >0,zheng,0)) unfinished_z,
	SUM(IF(A.order_deliver_status_id >0,san,0)) unfinished_s
 FROM(
		SELECT
			a.order_id,
			a.warehouse_id,
			a.order_status_id,
			a.order_deliver_status_id,
			a.deliver_date,
			SUM(
				IF (c.repack = 1, b.quantity, 0)
			) san,
			SUM(
				IF (c.repack = 0, b.quantity, 0)
			) zheng,
			d.shortname
		FROM
			oc_order a
		LEFT JOIN oc_order_product b ON a.order_id = b.order_id
		LEFT JOIN oc_product c ON b.product_id = c.product_id
		LEFT JOIN oc_x_warehouse d ON a.warehouse_id = d.warehouse_id
		WHERE
			a.order_status_id  NOT IN(3,6)";
			$sql3.=" AND date(a.deliver_date) BETWEEN '".$go_date."' AND  '".$to_date."'
			AND a.order_type = 1
			AND a.station_id = 2";

			if ($to_host != '99'){
                $sql3 .= " AND a.warehouse_id = $to_host";
            }
		$sql3.=" GROUP BY
			a.order_id
	) A
GROUP BY
A.warehouse_id,A.deliver_date";
//            echo $sql3;die;
            $res3=$db->query($sql3);
            $info3=$res3->rows;
        $sql4="SELECT
	COUNT(DISTINCT A.order_id) null_single,
	A.deliver_date,
	A.warehouse_id
FROM
	(
		SELECT
			a.order_id,
			a.deliver_date,
			a.warehouse_id
		FROM
			oc_order a
		LEFT JOIN oc_x_deliver_order b ON a.order_id = b.order_id
		WHERE
			b.order_status_id = 6
		AND a.order_status_id=5";
        $sql4.=" AND date(a.deliver_date) BETWEEN '".$go_date."' AND  '".$to_date."'
        AND a.order_type = 1
		AND a.station_id = 2";

		if ($to_host != '99'){
                $sql4 .= " AND a.warehouse_id = $to_host";
            }
            $sql4 .= " ORDER BY
			a.order_id
	) A
GROUP BY
	A.deliver_date,
	A.warehouse_id";
//		    echo  $sql4;die;
            $res4=$db->query($sql4);
            $info4=$res4->rows;
//            var_dump($info3);die;
            $_arr=[];
            $arr_=[];
            $arr2=[];
            $arr3=[];
            foreach ($info2 as $k=>$v){
                $arr2[$v['deliver_date'].'@'.$v['warehouse_id']]=$v;
            }
//            var_dump($arr2);die;
            foreach ($info3 as $k1=>$v2) {
                $_arr[$v2['deliver_date'].'@'.$v2['warehouse_id']]=$v2;
            }
            foreach ($info4 as $k4=>$v4) {
                $arr3[$v4['deliver_date'].'@'.$v4['warehouse_id']]=$v4;
            }

            foreach ($info as $v3){
                $arr_[$v3['deliver_date'].'@'.$v3['warehouse_id']]=$v3;
                $arr_[$v3['deliver_date'].'@'.$v3['warehouse_id']]['unfinished_z']=empty($_arr[$v3['deliver_date'].'@'.$v3['warehouse_id']])?0:$_arr[$v3['deliver_date'].'@'.$v3['warehouse_id']]['unfinished_z'];
                $arr_[$v3['deliver_date'].'@'.$v3['warehouse_id']]['unfinished_s']=empty($_arr[$v3['deliver_date'].'@'.$v3['warehouse_id']])?0:$_arr[$v3['deliver_date'].'@'.$v3['warehouse_id']]['unfinished_s'];
                $arr_[$v3['deliver_date'].'@'.$v3['warehouse_id']]['sales_return_z']=empty($arr2[$v3['deliver_date'].'@'.$v3['warehouse_id']])?0:$arr2[$v3['deliver_date'].'@'.$v3['warehouse_id']]['sales_return_z'];
                $arr_[$v3['deliver_date'].'@'.$v3['warehouse_id']]['sales_return_s']=empty($arr2[$v3['deliver_date'].'@'.$v3['warehouse_id']])?0:$arr2[$v3['deliver_date'].'@'.$v3['warehouse_id']]['sales_return_s'];
                $arr_[$v3['deliver_date'].'@'.$v3['warehouse_id']]['count_unfinished_oid']=empty($_arr[$v3['deliver_date'].'@'.$v3['warehouse_id']])?0:$_arr[$v3['deliver_date'].'@'.$v3['warehouse_id']]['count_unfinished_oid'];
                $arr_[$v3['deliver_date'].'@'.$v3['warehouse_id']]['count_orderId']=empty($arr2[$v3['deliver_date'].'@'.$v3['warehouse_id']])?0:$arr2[$v3['deliver_date'].'@'.$v3['warehouse_id']]['count_orderId'];
                $arr_[$v3['deliver_date'].'@'.$v3['warehouse_id']]['null_single']=empty($arr3[$v3['deliver_date'].'@'.$v3['warehouse_id']])?0:$arr3[$v3['deliver_date'].'@'.$v3['warehouse_id']]['null_single'];
            }
            echo json_encode($arr_);
            break;
    }
}