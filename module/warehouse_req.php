
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/14
 * Time: 10:15
 */
SELECT
	osm.relevant_id 调拨单号,
	SUM(
        (osmi.price * osmi.quantity) / osmi.box_quantity
    ) `调拨金额`,
	oxw.title '操作仓库',

IF (
osm.inventory_type_id = 22,
	'出库',
	'入库'
) '入库类型',
 wrt. NAME '调拨类型',
 CONCAT(oxwi.title,'-',oxwm.title) '发出仓-目的仓',
 oxwi.title '发出仓',
 oxwm.title '目的仓',
 osm.date_added '日期'
FROM
	`oc_x_stock_move` osm
LEFT JOIN oc_x_stock_move_item osmi ON osmi.inventory_move_id = osm.inventory_move_id
LEFT JOIN oc_x_warehouse_requisition wr ON wr.relevant_id = osm.relevant_id
LEFT JOIN oc_x_warehouse_requisition_type wrt ON wrt.out_type_id = wr.out_type
LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = osm.warehouse_id
LEFT JOIN oc_x_warehouse oxwi ON oxwi.warehouse_id = wr.from_warehouse
LEFT JOIN oc_x_warehouse oxwm ON oxwm.warehouse_id = wr.to_warehouse
WHERE
	date(osm.date_added) BETWEEN '2018-4-30 23:59:59'
AND '2018-5-31 23:59:59'
AND osm.inventory_type_id IN (22, 23)
AND osm.relevant_id > 0
AND wr.out_type in (1)
GROUP BY
	osm.relevant_id,
	osm.inventory_type_id
ORDER BY
	osm.relevant_id