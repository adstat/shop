
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/28
 * Time: 19:41
 */

--按分拣单查单数
SELECT
	A.deliver_date '配送日期',
	A.warehouse '目的仓库',
	count(DISTINCT A.deliver_order_id) '分拣单数',
IF (
    A.warehouse_id IN (12, 14),
IF (A.is_repack = 0, '整', '散'),
 '-'
) '分拣单类型',
 sum(

	IF (
        A.order_status_id IN (1, 2),
		1,
		0
	)
) '未分拣单数',
 sum(

	IF (A.order_status_id = 4, 1, 0)
) '已分配单数',
 sum(

	IF (A.order_status_id = 5, 1, 0)
) '分拣中单数',
 sum(

	IF (A.order_status_id = 8, 1, 0)
) '待审核单数',
 sum(

	IF (A.order_status_id = 6, 1, 0)
) '已捡完单数',
 sum(

	IF (
        A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),
		1,
		0
	)
) '其他类型'
FROM
(
    SELECT
			xdo.order_id,
			xdo.deliver_date,
			xdo.warehouse_id,
			xdo.do_warehouse_id,
		IF (
            xdo.warehouse_id <> xdo.do_warehouse_id,
			1,
			xdo.is_repack
		) is_repack,
		xdo.deliver_order_id,
		xdo.order_status_id,
		xdo.order_status_id soStatus,
		w1.title warehouse
	FROM
		 oc_x_deliver_order xdo
	LEFT JOIN oc_x_warehouse w1 ON xdo.warehouse_id = w1.warehouse_id
	WHERE
		date(xdo.date_added) = "2018-5-28"
        AND xdo.order_status_id != 3
        AND xdo.order_type = 1
        AND xdo.station_id = 2
        AND xdo.do_warehouse_id = "12"
	) A
GROUP BY
	A.deliver_date,
	A.warehouse_id,
	A.do_warehouse_id,
	A.is_repack




    --按分拣单查未分捡单数及商品数
SELECT
	A.deliver_date '配送日期',
	A.warehouse '目的仓库',
	count(DISTINCT A.deliver_order_id) '分拣单数',
IF (
    A.warehouse_id IN (12, 14),
IF (A.is_repack = 0, '整', '散'),
 '-'
) '分拣单类型',
 sum(

	IF (
        A.order_status_id IN (1, 2),
		1,
		0
	)
) '未分拣单数',
 sum(

	IF (A.order_status_id = 4, 1, 0)
) '已分配单数',
 sum(

	IF (A.order_status_id = 5, 1, 0)
) '分拣中单数',
 sum(

	IF (A.order_status_id = 8, 1, 0)
) '待审核单数',
 sum(

	IF (A.order_status_id = 6, 1, 0)
) '已捡完单数',
 sum(

	IF (
        A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),
		1,
		0
	)
) '其他类型',
sum(
    A.quantity_zheng) '整件商品数',sum(A.quantity_san) '散件商品数',
sum(IF (
    A.order_status_id IN (1, 2),
		A.quantity_zheng,0)) '未分捡整件商品数',sum(IF (
    A.order_status_id IN (1, 2),A.quantity_san,0)) '未分捡散件商品数',
sum(IF (A.order_status_id = 4,A.quantity_zheng,0)) '已分配整件商品数',sum(IF (A.order_status_id = 4,A.quantity_san,0)) '已分配散件商品数',
sum(IF (A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),A.quantity_zheng,0)) '其他状态整件商品数',sum(IF (A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),A.quantity_san,0)) '其他状态散件商品数'
FROM
(
    SELECT
			xdo.order_id,
			xdo.deliver_date,
			xdo.warehouse_id,
			xdo.do_warehouse_id,
		IF (
            xdo.warehouse_id <> xdo.do_warehouse_id,
			1,
			xdo.is_repack
		) is_repack,
		xdo.deliver_order_id,
		xdo.order_status_id,
		xdo.order_status_id soStatus,
		w1.title warehouse,
		sum(if(op.repack = 0,dop.quantity,0)) quantity_zheng,
		sum(if(op.repack = 1,dop.quantity,0)) quantity_san
	FROM
		 oc_x_deliver_order xdo
	LEFT JOIN oc_x_warehouse w1 ON xdo.warehouse_id = w1.warehouse_id
	LEFT JOIN oc_x_deliver_order_product dop ON dop.deliver_order_id = xdo.deliver_order_id
	LEFT JOIN oc_product op ON op.product_id = dop.product_id
	WHERE
		date(xdo.date_added) = "2018-5-28"
        AND xdo.order_status_id != 3
        AND xdo.order_type = 1
        AND xdo.station_id = 2
        AND xdo.do_warehouse_id = "12"
GROUP BY xdo.deliver_order_id
	) A
GROUP BY
	A.deliver_date,
	A.warehouse_id,
	A.do_warehouse_id,
	A.is_repack

    --



按分拣单查分捡单数及商品数
SELECT
	A.deliver_date '配送日期',
	A.warehouse '目的仓库',
	count(DISTINCT A.deliver_order_id) '分拣单数',
IF (
    A.warehouse_id IN (12, 14),
IF (A.is_repack = 0, '整', '散'),
 '-'
) '分拣单类型',
 sum(

	IF (
        A.order_status_id IN (1, 2),
		1,
		0
	)
) '未分拣单数',
 sum(

	IF (A.order_status_id = 4, 1, 0)
) '已分配单数',
 sum(

	IF (A.order_status_id = 5, 1, 0)
) '分拣中单数',
 sum(

	IF (A.order_status_id = 8, 1, 0)
) '待审核单数',
 sum(

	IF (A.order_status_id = 6, 1, 0)
) '已捡完单数',
 sum(

	IF (
        A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),
		1,
		0
	)
) '其他类型',
sum(IF (A.order_status_id = 5,A.quantity_zheng,0)) '分拣中整件商品数',sum(IF (A.order_status_id = 5,A.quantity_san,0)) '分拣中散件商品数',
sum(IF (A.order_status_id = 8,A.quantity_zheng,0)) '待审核整件商品数',sum(IF (A.order_status_id = 8,A.quantity_san,0)) '待审核散件商品数',
sum(IF (A.order_status_id = 6,A.quantity_zheng,0)) '已捡完整件商品数',sum(IF (A.order_status_id = 6,A.quantity_san,0)) '已捡完散件商品数'
FROM
(
    SELECT
			xdo.order_id,
			xdo.deliver_date,
			xdo.warehouse_id,
			xdo.do_warehouse_id,
		IF (
            xdo.warehouse_id <> xdo.do_warehouse_id,
			1,
			xdo.is_repack
		) is_repack,
		xdo.deliver_order_id,
		xdo.order_status_id,
		xdo.order_status_id soStatus,
		w1.title warehouse,
		sum(if(op.repack = 0,dop.quantity,0)) quantity_zheng,
		sum(if(op.repack = 1,dop.quantity,0)) quantity_san
	FROM
		 oc_x_deliver_order xdo
	LEFT JOIN oc_x_warehouse w1 ON xdo.warehouse_id = w1.warehouse_id
	LEFT JOIN oc_x_inventory_order_sorting dop ON dop.deliver_order_id = xdo.deliver_order_id
	LEFT JOIN oc_product op ON op.product_id = dop.product_id
	WHERE
		date(xdo.date_added) = "2018-5-28"
        AND xdo.order_status_id != 3
        AND xdo.order_type = 1
        AND xdo.station_id = 2
        AND xdo.do_warehouse_id = "12"
GROUP BY xdo.deliver_order_id
	) A
GROUP BY
	A.deliver_date,
	A.warehouse_id,
	A.do_warehouse_id,
	A.is_repack