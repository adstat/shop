<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/21
 * Time: 11:23
 */

require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
$change_type = intval($_POST['change_type']);
switch ($change_type){
    case 1:

        $sql = "SELECT * FROM `oc_x_container`
WHERE container_id != 999999 
ORDER BY container_id DESC LIMIT 0,1 ";
        $container_id = intval($db->query($sql)->row['container_id'])+1;
        $a = $container_id; //起始筐号
        $b = empty($_POST['every_count'])? 500 : intval($_POST['every_count']);//每个文件多少条
        $c = empty($_POST['total_count'])? 2000 : intval($_POST['total_count']);//总共多少条
        $url = "view/";
        $d=$c/$b;
        $type = empty($_POST['container_type'])? 1 : intval($_POST['container_type']);//类型，1 临时；2 非临时
        switch($type){
            case 1:
                $text = '临时周转筐';
                $container_type = 8;
                break;
            case 2:
                $text = '周转筐';
                $container_type = 7;
                break;
            case 3:
                $text = '冷冻箱';
                $container_type = 11;
                break;
            case 4:
                $text = '保温箱';
                $container_type = 12;
                break;
        }
        $files = [];
        for ($j=1;$j<=$d;$j++) {
            $data = '';
            for ($i = $a+($j-1)*$b;$i < ($a+$j*$b);$i++) {
                $data .=  $i.','.$i.'X'.$container_type.','.$text.",1\r\n";
            }
            file_put_contents($url.($a+($j-1)*$b).'-'.($a+$j*$b-1).'.txt', $data);
            $files[] = ($a+($j-1)*$b).'-'.($a+$j*$b-1).'.txt';
        }

        $sql = "INSERT INTO `oc_x_container` (
	`container_id`,
	`status`,
	`type`,
	`instore`,
	`warehouse_id`,
	`occupy`,
	`date_added`
)
VALUES";
        for ($k = $a;$k < ($a+$c);$k++) {
            $sql.= '('.$k.',1,'.$container_type.',1,0,0,NOW()),';
        }
        $sql = rtrim($sql,',');
//        var_dump($sql);
//    $db->query($sql);
        exit(json_encode($files));

        break;
    case 2:

        $product_id = empty($_POST['every_count'])? 0 : intval($_POST['every_count']);//要放入库存的商品id
        $order_id = empty($_POST['total_count'])? false : intval($_POST['total_count']);//订单号
        if ($order_id) {
            $sql1 = "select orr.return_id,orr.order_id,orr.return_status_id,orr.comment,orr.return_credits,orrs.product_id,orrs.quantity 
from oc_return orr 
LEFT JOIN oc_return_product orrs ON orrs.return_id = orr.return_id 
where orr.order_id = '" . $order_id . "'";
            if (!empty($product_id)) {
                $sql1 .= " AND orrs.product_id IN (" . $product_id . ")";
            }
            $sql1 .= " GROUP BY orr.return_id,orrs.product_id ";
            $result_array1 = $db->query($sql1)->rows;

            $sql2 = " SELECT
 osm.order_id,
	osm.inventory_move_id AS inventory_move_id,
2 AS station_id,
now() due_date,
orp.product_id,
orp.price,
(0-orp.quantity) as quantity,
orp.box_quantity,
opp.name
FROM
	`oc_return_product` orp
LEFT JOIN	`oc_return` ors on ors.return_id = orp.return_id
LEFT JOIN	`oc_x_stock_move` osm on osm.order_id = ors.order_id
left join oc_product opp on opp.product_id = orp.product_id
where ors.order_id = '" . $order_id . "'";
            if (!empty($product_id)) {
                $sql2 .= " and orp.product_id in (" . $product_id . ")";
            }
            $sql2 .= "GROUP BY orp.product_id";
            $result_array2 = $db->query($sql2)->rows;
            $array = [];
            $array['return'] = $result_array1;
            $array['stock'] = $result_array2;
            exit(json_encode($array));
        }
        break;
    case 3:

        $product_id = empty($_POST['every_count'])? 0 : intval($_POST['every_count']);//要放入库存的商品id
        $order_id = empty($_POST['total_count'])? false : intval($_POST['total_count']);//订单号
        if ($order_id) {
            $sql1 = "update oc_return set return_status_id = 3 where order_id = '".$order_id."'";

            $result_array1 = $db->query($sql1);

            $sql2 = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`,`station_id`,`due_date`,`product_id`,`price`,`quantity`,`box_quantity`)
 SELECT
	osm.inventory_move_id AS inventory_move_id,
2 AS station_id,
now() due_date,
orp.product_id,
orp.price,
(0-orp.quantity) as quantity,
orp.box_quantity
FROM
	`oc_return_product` orp
LEFT JOIN	`oc_return` ors on ors.return_id = orp.return_id
LEFT JOIN	`oc_x_stock_move` osm on osm.order_id = ors.order_id
left join oc_product opp on opp.product_id = orp.product_id
where ors.order_id = '".$order_id."'";
            if (!empty($product_id)) {
                $sql2 .= " and orp.product_id in (".$product_id.")";
            }
            $sql2 .= "GROUP BY orp.product_id";
            $result_array2 = $db->query($sql2);
            $array = [];
            $array['return'] = $result_array1;
            $array['stock'] = $result_array2;

        }
        exit(json_encode(1));
        break;
    case 4:

        $product_id = empty($_POST['every_count'])? false : intval($_POST['every_count']);//要放入库存的商品id
        $order_id = empty($_POST['total_count'])? false : intval($_POST['total_count']);//订单号
        $inventory_move_id = empty($_POST['container_type'])? false : intval($_POST['container_type']);

        if ($order_id) {
            $sql1 = "select orr.return_id,orr.order_id,orr.return_status_id,orr.comment,orr.return_credits,orrs.product_id,orrs.quantity 
from oc_return orr 
LEFT JOIN oc_return_product orrs ON orrs.return_id = orr.return_id 
where orr.order_id = '" . $order_id . "'";
            $result_array1 = $db->query($sql1)->rows;
            $sql2 = "SELECT orp.`inventory_move_id`,orp.`station_id`,NOW(),orp.`product_id`,orp.`price`,orp.`product_batch`,(0-orp.`quantity`),orp.`box_quantity`,orp.`weight`,orp.`weight_class_id`,orp.`is_gift`,orp.`checked`,orp.`status`,orp.`sku_id`
FROM `oc_x_stock_move` osm
LEFT JOIN `oc_x_stock_move_item` orp ON osm.inventory_move_id = orp.inventory_move_id
WHERE osm.order_id = '".$order_id."' and osm.inventory_type_id = 8 ";
            if ($product_id) {
                $sql2 .= " and orp.product_id = '".$product_id."'";
            }
            $sql2 .= " GROUP BY orp.product_id ";
            $result_array2 = $db->query($sql2)->rows;
            $sql3 = "SELECT ims.inventory_move_id,imi.product_id,imi.quantity
FROM `oc_x_stock_move` osm
LEFT JOIN `oc_x_stock_move_item` orp ON osm.inventory_move_id = orp.inventory_move_id
LEFT JOIN `oc_x_inventory_move` ims ON osm.warehouse_id = ims.warehouse_id and ims.added_by = osm.added_by and ims.date_added = osm.date_added
LEFT JOIN `oc_x_inventory_move_item` imi ON imi.inventory_move_id = ims.inventory_move_id and imi.product_id = orp.product_id and imi.quantity=orp.quantity
LEFT JOIN oc_product opp ON opp.product_id = orp.product_id
WHERE
	osm.order_id = '".$order_id."' 
and osm.inventory_type_id = 8 ";
            if ($product_id) {
            $sql3 .=  " and orp.product_id = '".$product_id."'";
            }
            $sql3 .= " and imi.quantity>0 ORDER BY ims.inventory_move_id desc";
            $result_array3 = $db->query($sql3)->rows;
            $array = [];
            $array['return'] = $result_array1;
            $array['stock_move_item'] = $result_array2;
            $array['inventory_move_id'] = $result_array3;
            if ($inventory_move_id) {
                $sql4 = "SELECT
	`order_id`, `inventory_type_id`,`added_by`, `add_user_name`,`warehouse_id`
FROM
	`oc_x_inventory_move` 
WHERE
	inventory_move_id = '".$inventory_move_id."'";
                $result_array4 = $db->query($sql4)->rows;
                $sql5 = "SELECT
	 @inventory_move_id3, `product_id`, (0-`quantity`), `order_id`, `checked`, `due_date`, `price`, `warehouse_id`
FROM
	`oc_x_inventory_move_item` 
WHERE
	inventory_move_id = '".$inventory_move_id."'";
                if ($product_id) {
                    $sql5 .= " and product_id = @product_id";
                }
                $result_array5 = $db->query($sql5)->rows;
                $array['inventory_move'] = $result_array4;
                $array['inventory_move_item'] = $result_array5;
            }
        }
        exit(json_encode($array));
        break;
    case 5:

        $product_id = empty($_POST['every_count'])? 0 : intval($_POST['every_count']);//要放入库存的商品id
        $order_id = empty($_POST['total_count'])? false : intval($_POST['total_count']);//订单号
        $inventory_move_id = empty($_POST['container_type'])? false : intval($_POST['container_type']);
        if ($inventory_move_id && $order_id) {
            $sql1 = "update oc_return set return_status_id = 3 where order_id = '".$order_id."'";
            $sql2 = "INSERT INTO `oc_x_stock_move_item` ( `inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `weight_class_id`, `is_gift`, `checked`, `status`, `sku_id`) 
SELECT
	orp.`inventory_move_id`,
	orp.`station_id`,
	NOW(),
	orp.`product_id`,
	orp.`price`,
	orp.`product_batch`,
	(0-orp.`quantity`),
	orp.`box_quantity`,
	orp.`weight`,
	orp.`weight_class_id`,
	orp.`is_gift`,
	orp.`checked`,
	orp.`status`,
	orp.`sku_id`
FROM
	`oc_x_stock_move` osm
LEFT JOIN `oc_x_stock_move_item` orp ON osm.inventory_move_id = orp.inventory_move_id
WHERE osm.order_id = '".$order_id."' and osm.inventory_type_id = 8 ";
            if ($product_id) {
                $sql2 .= " and orp.product_id = '".$product_id."'";
            }
            $sql2 .= " GROUP BY orp.product_id ";
            $sql3 = "INSERT INTO `oc_x_inventory_move` (`station_id`, `date`, `timestamp`, `from_station_id`, `to_station_id`, `order_id`, `inventory_type_id`, `date_added`, `status`, `added_by`, `add_user_name`, `confirmed`, `confirmed_by`, `confirm_user_name`, `printed`, `print_time`, `last_print_time`, `memo`, `discount`, `warehouse_id`, `relevant_id`)
SELECT `station_id`, NOW(), `timestamp`, `from_station_id`, `to_station_id`, `order_id`, `inventory_type_id`, NOW(), `status`, `added_by`, `add_user_name`, `confirmed`, `confirmed_by`, `confirm_user_name`, `printed`, `print_time`, `last_print_time`, CONCAT(`memo`,'','异常'), `discount`, `warehouse_id`, `relevant_id`
FROM `oc_x_inventory_move` 
WHERE inventory_move_id = '".$inventory_move_id."'";
            $db->query($sql1);
            $db->query($sql2);
            $db->query($sql3);
            $inventory_move_id2 = $db->getLastId();
            $sql4 = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `product_id`, `quantity`, `order_id`, `return_order_id`, `purchase_order_id`, `customer_id`, `status`, `is_gift`, `checked`, `due_date`, `price`, `product_batch`, `weight`, `weight_class_id`, `warehouse_id`) 
SELECT $inventory_move_id2, `station_id`, `product_id`, (0-`quantity`), `order_id`, `return_order_id`, `purchase_order_id`, `customer_id`, `status`, `is_gift`, `checked`, `due_date`, `price`, `product_batch`, `weight`, `weight_class_id`, `warehouse_id`
FROM `oc_x_inventory_move_item` 
WHERE inventory_move_id = '".$inventory_move_id."'";
            if ($product_id) {
                $sql4 .= " and product_id = '".$product_id."'";
            }
            $db->query($sql4);
        }
        exit(json_encode($inventory_move_id2));
        break;
    case 6:
        $sql = empty($_POST['container_type'])? false : trim($_POST['container_type'],"'");
        $sql_left = substr($sql,0,6);
        if ($sql_left == 'select') {
            $result = $db->query($sql)->rows;
            $return = array(
                'return_code' => 'SUCCESS',
                'return_msg'  => 'TRUE',
                'return_data' => $result
            );
        } else if(in_array($sql_left,['update','delete','inset'])) {
            if($db->query($sql)){
                $return = array(
                    'return_code' => 'OK',
                    'return_msg'  => '修改成功',
                    'return_data' => ''
                );
            } else {
                $return = array(
                    'return_code' => 'ERROR',
                    'return_msg'  => '执行失败',
                    'return_data' => ''
                );
            }
        } else {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'sql语句有误',
                'return_data' => ''
            );
        }

        exit(json_encode($return));
        break;
}
if (!empty($_POST['total_count'])) {
//   var_dump($_POST);




    $a = 2;
} else if ($_GET['get_fresh']==2) {
    echo "<script>alert('已提交请点击下载，如需再次生成请点击刷新页面按钮');</script>";
}
?>

