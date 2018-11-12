<?php

// 指定允许其他域名访问
//header('Access-Control-Allow-Origin:*');
//// 响应类型
//header('Access-Control-Allow-Methods:POST');
//// 响应头设置
//header('Access-Control-Allow-Headers:x-requested-with,content-type');
//
////设置内容类型为json
//
//header('content-type:application:json;charset=utf8');
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
$db;
class Good{
    public $opt;
    public $Db;
    public $do_warehouse_id;
    public $warehouse_id;
    public $sortStartDate;
    public $sortEndDate;
    public $date_search;
    public $search_type;
    private static $instance;
    public function __construct($db)
    {
        $this->Db=$db;
        $this->opt=isset($_REQUEST['flag'])?$_REQUEST['flag']:'';
        /*分拣数据*/
        $this->do_warehouse_id = isset($_REQUEST['do_warehouse_id'])?$_REQUEST['do_warehouse_id']:'';
        $this->warehouse_id = isset($_REQUEST['warehouse_id'])?$_REQUEST['warehouse_id']:'';
        $this->sortStartDate = isset($_REQUEST['sortStartDate'])?$_REQUEST['sortStartDate']:'';
        $this->sortEndDate = isset($_REQUEST['sortEndDate'])?$_REQUEST['sortEndDate']:'';
        $this->date_search = isset($_POST['date_search']) ? $_POST['date_search'] : 2;
        $this->search_type = isset($_POST['search_type']) ? intval($_POST['search_type']) : 1;
        $this->init();

    }

    private function init()
    {
        $ads = $this->opt;
        switch ($ads){
            case 0:
                $this->getWarehouse();
                break;
            case 1:
                $this->getSortData();
                break;
            case 2:
                $this->stopCheck();
                break;
            case 3:
                $this->getOtherFun();
                break;
            case 5:
                $this->getOrderInfo();
                break;
            case 6:
                $this->nullSingle();
                break;
            case 7:
                $this->logistic();
                break;
            case 8:
                $this->evaluate();
                break;
            case 9:
                $this->returnPutaway();//退货上架
                break;
            case 10:
                $this->returnContainer();
                break;
            case 11:
                $this->returnCity();//城市仓系统未操作上架
                break;
            case 12:
                $this->returnCityAllot();
                break;



        }
    }


    public static function getParam()
    {
        $class_name=__CLASS__;
        $class= new $class_name;
        $url=$_SERVER['PHP_SELF'];
        $action=trim(strchr('/',$url),'/');
        $class->$action();

    }
    //城市仓系统未操作上架
    public function returnCity()
    {
        //城市仓系统未操作上架
        $sql_return = "
		SELECT
			date_format(rdp.date_added, '%m%d') date_added,
			rp.product_id,
			rdp.box_quantity,
			rdp.warehouse_id,pw.stock_area,
			sum(rp.quantity) return_quantity,
			sum(

				IF (
					rp.return_confirmed = 0,
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
		w.shortname doWarehouse,
		o.order_id
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
	AND rdp.date_added between '".$this->sortStartDate."' and date_add(current_date(), interval 1 day)
	";
        if ($this->warehouse_id) {
            $sql_return .= " AND rdp.warehouse_id = '" . $this->warehouse_id. "'";
        }
        $sql_return .= "
	AND rdp.is_repack_missing = '0'
	AND rdp.is_back = '1'
	AND r.return_reason_id IN (2, 3, 4, 5, 6)
	GROUP BY
		rdp.product_id,date_added,r.order_id,rdp.warehouse_id ORDER BY rdp.warehouse_id,date_added
	";
        $return_data_raw = $this->Db->query($sql_return)->rows;
        $return_data = [];
        if (!empty($return_data_raw)) {
            foreach ($return_data_raw as $v1){
                $return_data[$v1['date_added'].$v1['warehouse_id']]['return_num'] = empty($return_data[$v1['date_added'].$v1['warehouse_id']]['return_num'])?intval($v1['return_quantity']):intval($v1['return_quantity'])+intval($return_data[$v1['date_added'].$v1['warehouse_id']]['return_num']);
                $return_data[$v1['date_added'].$v1['warehouse_id']]['shelf_num'] = empty($return_data[$v1['date_added'].$v1['warehouse_id']]['shelf_num'])?intval($v1['self_quantity']):intval($v1['self_quantity'])+intval($return_data[$v1['date_added'].$v1['warehouse_id']]['shelf_num']);
                $return_data[$v1['date_added'].$v1['warehouse_id']]['date_added'] = $v1['date_added'];
                $return_data[$v1['date_added'].$v1['warehouse_id']]['warehouse_id'] = $v1['warehouse_id'];
                $return_data[$v1['date_added'].$v1['warehouse_id']]['order_id'] = $v1['order_id'];
                if (intval($v1['return_quantity'])!= intval($v1['self_quantity'])) {
                    $return_data[$v1['date_added'].$v1['warehouse_id']]['product'][] = $v1;
                }
            }
        }
        echo json_encode($return_data);
    }
    //城市仓调拨退货回总仓
    public function returnCityAllot()
    {


        $sql="SELECT A.return_id,A.relevant_id,A.order_id,A.deliver_order_id,A.product_id,A.returnNum,A.deliverNum,A.rdbData,SUM(wrt.quantity)wrtNum,GROUP_CONCAT(DISTINCT wrt.container_id) useContainer,GROUP_CONCAT(DISTINCT rdb.relevant_id) otherRelevantId,ww.shortname returnHouse,ww1.shortname deliverHouse,ww2.shortname deliverToHouse,ww3.shortname from_warehouse,ww4.shortname to_warehouse
FROM
(
SELECT
	rdp.return_id,
	rdp.order_id,
	rdp.product_id,
	rdp.warehouse_id returnHouse,
	rdp.quantity returnNum,
	dop.quantity deliverNum,
	DATE( rdp.date_added ) rdbData,
	dop.deliver_order_id,
	doo.do_warehouse_id deliverToHouse,
	doo.warehouse_id deliverHouse,
	doo.relevant_id
FROM
	oc_return_deliver_product rdp
INNER JOIN oc_x_deliver_order_product dop ON rdp.order_id=dop.order_id	AND rdp.product_id=dop.product_id
INNER JOIN oc_x_deliver_order doo ON dop.deliver_order_id=doo.deliver_order_id

WHERE
	DATE(rdp.date_added)BETWEEN '".$this->sortStartDate."' 
	AND date_add( CURRENT_DATE (), INTERVAL 0 DAY) 
	AND rdp.warehouse_id = '".$this->warehouse_id."'
	AND rdp.confirmed>0
	AND rdp.STATUS=1
	AND doo.relevant_id >0
) A
LEFT JOIN oc_x_relevant_deliver_binding rdb ON A.relevant_id=rdb.relevant_id AND A.deliver_order_id=rdb.deliver_order_id
LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON rdb.relevant_id=wrt.relevant_id AND A.product_id=wrt.product_id AND wrt.relevant_status_id=4
LEFT JOIN oc_x_warehouse_requisition wr ON wrt.relevant_id=wr.relevant_id 
LEFT JOIN oc_x_warehouse ww ON A.returnHouse=ww.warehouse_id
LEFT JOIN oc_x_warehouse ww1 ON A.deliverHouse=ww1.warehouse_id
LEFT JOIN oc_x_warehouse ww2 ON A.deliverToHouse=ww2.warehouse_id
LEFT JOIN oc_x_warehouse ww3 ON wr.from_warehouse=ww3.warehouse_id
LEFT JOIN oc_x_warehouse ww4 ON wr.to_warehouse=ww4.warehouse_id
GROUP BY wrt.product_id
HAVING returnHouse != from_warehouse
order by rdbData DESC";
        $list=$this->Db->query($sql);
        $msg= $list->rows;
        $_arr=[];

        echo json_encode($msg);
//
//
    }



    public function getWarehouse()
    {
        $sql="select warehouse_id, shortname warehouse from oc_x_warehouse where status = 1 and station_id = 2";
        $info=$this->Db->query($sql);
        $list=$info->rows;
        echo json_encode($list);
    }

    private  function setDeliverList()
    {
//        var_dump($this->sortStartDate,$this->do_warehouse_id);
        $sql="SELECT GROUP_CONCAT(distinct deliver_order_id) doList FROM oc_x_deliver_order
WHERE date(deliver_date) BETWEEN  date_sub(current_date(), interval '".$this->sortStartDate."' day)  AND date_sub(current_date(), interval '0' day) 
    and do_warehouse_id = $this->do_warehouse_id 
    and order_status_id IN(2,4,5) ";
//        return $sql;
        $info=$this->Db->query($sql);
        $list=$info->row;
        return $list;
    }
    private  function setRelevantList()
    {
        $sql="SELECT GROUP_CONCAT(distinct relevant_id) doList FROM oc_x_deliver_order
WHERE deliver_date BETWEEN '".$this->sortStartDate."' AND '$this->sortEndDate ' and do_warehouse_id = $this->do_warehouse_id AND relevant_id>0 
                AND order_status_id IN(6,8,12)";
        $info=$this->Db->query($sql);
        return $info->row;
    }
    private function setOrderList()
    {
        $sql="SELECT GROUP_CONCAT(distinct order_id) ooList FROM oc_x_deliver_order
WHERE date(date_added) BETWEEN  date_sub(current_date(), interval '".$this->sortStartDate."' day)  AND date_sub(current_date(), interval '0' day)
AND do_warehouse_id = $this->do_warehouse_id";
        $info=$this->Db->query($sql);
        return $info->row;
    }
    /* 未分拣订单 */
    public  function getScanData()
    {
        $sql="SELECT GROUP_CONCAT(distinct deliver_order_id)  doList FROM oc_x_deliver_order doo 
                WHERE doo.deliver_date BETWEEN '".$this->sortStartDate."' AND '$this->sortEndDate ' and do_warehouse_id = $this->do_warehouse_id
                AND relevant_id=0 
                AND order_status_id IN(6,8,12)";
//        echo $sql;
        $info=$this->Db->query($sql);
        $list['ScanData']=$info->row;
        $list['ScanFailureData']=$this->getScanFailureData();
        echo json_encode($list);
    }
    /*
     * 抽查问题例表
     * */
    public function stopCheck()
    {

//        $sql="SELECT
//	IFNULL(sum(c.total),0)AS total,title,date_added
//FROM
//	(
//		SELECT
//			count(DISTINCT ocl.order_id) total,wu.title,ocl.date_added
//		FROM
//			oc_x_deliver_order_check_location ocl
//		LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id
//		left join  oc_w_user wu on ocl.add_user=wu.user_id
//		left join oc_order oo on ocl.order_id=oo.order_id
//		WHERE
//			ocl.date_added BETWEEN $this->sortStartDate AND SUBDATE(CURRENT_DATE(),INTERVAL 0 DAY)
//			and oo.warehouse_id=$this->do_warehouse_id
//		AND ocl. STATUS = 1
//		GROUP BY
//			ocl.order_id
//	) c";
        $sql="SELECT A.*,IF(pp.repack=0,'整','散')repack,pp.`name`,wu.title FROM
(
SELECT
	docl.check_location_id,docl.order_id,docd.container_id,docd.product_id,docd.quantity,docd.sorting_quantity,docd.final_quantity,DATE(docl.date_added) date_added,docl.add_user 
FROM
	oc_x_deliver_order_check_location docl
	LEFT JOIN oc_x_deliver_order_check_details docd ON docl.check_location_id = docd.check_location_id
	LEFT JOIN oc_order oo ON docl.order_id=oo.order_id
WHERE
	DATE(docl.date_added) BETWEEN $this->sortStartDate
	AND SUBDATE(CURRENT_DATE(),INTERVAL 0 DAY)
	AND warehouse_id=$this->do_warehouse_id
	AND docl.`status`=1
	AND docd.`status`=1
	) A
	LEFT JOIN oc_product pp ON A.product_id=pp.product_id
	LEFT JOIN oc_w_user wu ON A.add_user=wu.user_id
	ORDER BY check_location_id,order_id DESC";
        $info=$this->Db->query($sql);
        $msg=$info->rows;


//        $arr_['num']=$arrNum;
//        $_arr=array_merge($msg,$arr_);
        $_arr=[];
        foreach ($msg as $k=>$v){
            $_arr[$v['check_location_id'].$v['order_id']]['product'][]=$v;
            $_arr[$v['check_location_id'].$v['order_id']]['order_id']=$v['order_id'];
            $_arr[$v['check_location_id'].$v['order_id']]['check_location_id']=$v['check_location_id'];
            $_arr[$v['check_location_id'].$v['order_id']]['date_added']=$v['date_added'];
            $_arr[$v['check_location_id'].$v['order_id']]['final_quantity']=empty($_arr[$v['check_location_id'].$v['order_id']]['final_quantity'])?intval($v['final_quantity']):intval($v['final_quantity'])+intval($_arr[$v['check_location_id'].$v['order_id']]['final_quantity']);
            $_arr[$v['check_location_id'].$v['order_id']]['sorting_quantity']=empty($_arr[$v['check_location_id'].$v['order_id']]['sorting_quantity'])?intval($v['sorting_quantity']):intval($v['sorting_quantity'])+intval($_arr[$v['check_location_id'].$v['order_id']]['sorting_quantity']);
            $_arr[$v['check_location_id'].$v['order_id']]['quantity']=empty($_arr[$v['quantity'].$v['order_id']]['quantity'])?intval($v['quantity']):intval($v['quantity'])+intval($_arr[$v['check_location_id'].$v['order_id']]['quantity']);
            $_arr[$v['check_location_id'].$v['order_id']]['title']=$v['title'];
        }
        echo json_encode($_arr);
    }

    public function getOrderInfo()
    {
        $orderList=$this->setOrderList();
        $sql="select ocl.order_id,p.product_id,p.name,p.inv_class_sort,if(sum(ocd.sorting_quantity) != sum(ocd.final_quantity),'异常','正常') isFinal, ocd.quantity,count(distinct ocl.order_id) count ,sum(ocd.sorting_quantity) as sorting,sum(ocd.final_quantity) final,ocl.old_inv_comment,ocl.new_inv_comment
                 from oc_x_deliver_order_check_location ocl
                 left join oc_x_deliver_order_check_details as ocd on ocd.check_location_id = ocl.check_location_id 
                 left join oc_product p on ocd.product_id = p.product_id
                 where ocl.order_id in (".implode(',',$orderList).")  and ocl.status = 1 and ocd.status = 1
                 group by ocl.order_id";
//        echo $sql;
        $info=$this->Db->query($sql);
        $msg=$info->rows;
        echo json_encode($msg);
    }
    /*
     * 出库
     * */
    //周转筐
    public function getOtherFun()
    {
//        var_dump($_REQUEST);
        $sql = "select warehouse_id, shortname warehouse from oc_x_warehouse where status = 1 and station_id = 2";
        $list = $this->Db->query($sql);
        $dataWarehouseRaw = $list->rows;
        foreach($dataWarehouseRaw as $m){
            $dataWarehouse[$m['warehouse_id']] = $m;
        }
        switch ($this->search_type) {
            case 1:
                $sql = "SELECT
			ios.warehouse_id,
			ios.deliver_order_id,
			ios.relevant_id,
			ios.do_warehouse_id,
			ios.container_id ios_container,ios.deliver_date,ios.order_id,ios.container_id container_id,
            oxw.container_id diff_container,ios.is_urgent,ios.is_stage_target,oxw.otherRelevant							
		FROM
			(
				SELECT
					ioss.container_id,
					odo.deliver_order_id,
					odo.relevant_id,
					odo.warehouse_id,
					odo.do_warehouse_id,
					odo.deliver_date,
					odo.order_id,o.is_urgent,oc.is_stage_target
				FROM
					oc_x_deliver_order odo
					LEFT JOIN oc_order o ON odo.order_id = o.order_id
LEFT JOIN oc_customer oc on oc.customer_id = o.customer_id
				LEFT JOIN oc_x_inventory_order_sorting ioss ON ioss.deliver_order_id = odo.deliver_order_id
				WHERE
					odo.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)				
	";
                if ($this->warehouse_id >0) {
                    $sql .= " and odo.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql .= " and odo.do_warehouse_id = '".$this->do_warehouse_id."' ";
                }
                $sql .= "AND ioss. STATUS = 1
 				AND ioss.container_id > 0
				GROUP BY
					odo.deliver_order_id,
					odo.relevant_id,
					ioss.container_id
			) ios 
		LEFT JOIN (
			SELECT
				doo1.deliver_order_id,
				doo1.relevant_id,
				oxwt1.container_id,
				GROUP_CONCAT(DISTINCT doo1.relevant_id) otherRelevant
			FROM
				oc_x_deliver_order doo1
			INNER JOIN oc_x_relevant_deliver_binding rdb ON doo1.relevant_id=rdb.relevant_id AND rdb.deliver_order_id=doo1.deliver_order_id
			INNER JOIN oc_x_warehouse_requisition_temporary oxwt1 ON oxwt1.relevant_id = doo1.relevant_id
			WHERE
				doo1.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)";
                if ($this->warehouse_id >0) {
                    $sql .= " and doo1.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql .= " and doo1.do_warehouse_id = '".$this->do_warehouse_id."' ";
                }
                $sql .= " 			
	AND oxwt1.container_id > 0
			AND oxwt1.relevant_status_id = 2
			GROUP BY
				doo1.deliver_order_id,
				doo1.relevant_id,
				oxwt1.container_id
		) oxw ON oxw.deliver_order_id = ios.deliver_order_id
		AND oxw.container_id = ios.container_id AND oxw.relevant_id = ios.relevant_id ";
//                var_dump($sql);





                $sql1 = "SELECT
	batch.deliver_date,
	batch.do_warehouse_id ,
	batch.warehouse_id,
	batch.product_id,oop.name,
	batch.quantity batch_quantity,
	wrt_out.quantity type_quantity,
	wrt_out.relevantNum
	
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
				INTERVAL '" . $this->date_search . "' DAY
			)
		AND DATE_SUB(
			CURRENT_DATE (),
			INTERVAL 0 DAY
		) ";
                if ($this->warehouse_id >0) {
                    $sql1 .= " and obs.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql1 .= " and obs.do_warehouse_id = '".$this->do_warehouse_id."' ";
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
		wr.to_warehouse warehouse_id,
		GROUP_CONCAT(DISTINCT wrt.relevant_id) relevantNum
	FROM
		oc_x_warehouse_requisition wr
	LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON wrt.relevant_id = wr.relevant_id
	LEFT JOIN oc_product op1 ON op1.product_id = wrt.product_id
	WHERE
		wr.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)
	";
                if ($this->warehouse_id >0) {
                    $sql1 .= " and wr.to_warehouse = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql1 .= " and wr.from_warehouse = '".$this->do_warehouse_id."' ";
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
//                echo $sql1;die;
                break;
            case 2:
                $sql = "SELECT
			ios.warehouse_id,
			ios.deliver_order_id,
			ios.relevant_id,
			ios.do_warehouse_id,
			ios.container_id ,ios.deliver_date,ios.order_id,ios.container_id container_id,
            oxw.container_id ios_container,oxw2.container_id diff_container	,ios.is_urgent,ios.is_stage_target							
		FROM
			(
				SELECT
					ioss.container_id,
					odo.deliver_order_id,
					odo.relevant_id,
					odo.warehouse_id,
					odo.do_warehouse_id,
					odo.deliver_date,odo.order_id,o.is_urgent,oc.is_stage_target
				FROM
					oc_x_deliver_order odo
					LEFT JOIN oc_order o ON odo.order_id = o.order_id
LEFT JOIN oc_customer oc on oc.customer_id = o.customer_id
				LEFT JOIN oc_x_inventory_order_sorting ioss ON ioss.deliver_order_id = odo.deliver_order_id
				WHERE
					odo.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)				
	";
                if ($this->warehouse_id >0) {
                    $sql .= " and odo.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql .= " and odo.do_warehouse_id = '".$this->do_warehouse_id."' ";
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
			INNER JOIN oc_x_relevant_deliver_binding rdb ON doo1.relevant_id=rdb.relevant_id AND rdb.deliver_order_id=doo1.deliver_order_id 
			LEFT JOIN oc_x_warehouse_requisition_temporary oxwt1 ON oxwt1.relevant_id = doo1.relevant_id
			WHERE
				doo1.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	) 			";
                if ($this->warehouse_id >0) {
                    $sql .= " and doo1.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql .= " and doo1.do_warehouse_id = '".$this->do_warehouse_id."' ";
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
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)			";
                if ($this->warehouse_id >0) {
                    $sql .= " and doo2.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql .= " and doo2.do_warehouse_id = '".$this->do_warehouse_id."' ";
                }
                $sql .= "AND oxwt2.container_id > 0
			AND oxwt2.relevant_status_id = 4
			GROUP BY
				doo2.deliver_order_id,
				oxwt2.container_id
		) oxw2 ON oxw2.deliver_order_id = ios.deliver_order_id
		AND oxw2.container_id = ios.container_id ";
//                var_dump($sql);



                $sql1 = "SELECT
	batch.deliver_date,
	batch.do_warehouse_id ,
	batch.warehouse_id,
	batch.product_id,oop.name,
	wrt_out.quantity batch_quantity,wrt_out2.quantity type_quantity,wrt_out.relevantNum
	
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
				INTERVAL '" . $this->date_search . "' DAY
			)
		AND DATE_SUB(
			CURRENT_DATE (),
			INTERVAL 0 DAY
		)
		";
                if ($this->warehouse_id >0) {
                    $sql1 .= " and obs.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql1 .= " and obs.do_warehouse_id = '".$this->do_warehouse_id."' ";
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
		wr.to_warehouse warehouse_id,
		GROUP_CONCAT(DISTINCT wrt.relevant_id) relevantNum
	FROM
		oc_x_warehouse_requisition wr
	LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON wrt.relevant_id = wr.relevant_id
	LEFT JOIN oc_product op1 ON op1.product_id = wrt.product_id
	WHERE
		wr.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	) ";
                if ($this->warehouse_id >0) {
                    $sql1 .= " and wr.to_warehouse = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql1 .= " and wr.from_warehouse = '".$this->do_warehouse_id."' ";
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
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)";
                if ($this->warehouse_id >0) {
                    $sql1 .= " and wr2.to_warehouse = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql1 .= " and wr2.from_warehouse = '".$this->do_warehouse_id."' ";
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
			ios.relevant_id,
			ios.do_warehouse_id,
			ios.container_id ,ios.deliver_date,ios.order_id,ios.container_id container_id,
            oxw.container_id ios_container,oxw2.container_id diff_container	,ios.is_urgent,ios.is_stage_target							
		FROM
			(
				SELECT
					ioss.container_id,
					odo.deliver_order_id,
					odo.relevant_id,
					odo.warehouse_id,
					odo.do_warehouse_id,
					odo.deliver_date,odo.order_id,o.is_urgent,oc.is_stage_target
				FROM
					oc_x_deliver_order odo
					LEFT JOIN oc_order o ON odo.order_id = o.order_id
LEFT JOIN oc_customer oc on oc.customer_id = o.customer_id
				LEFT JOIN oc_x_inventory_order_sorting ioss ON ioss.deliver_order_id = odo.deliver_order_id
				WHERE
					odo.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)				";
                if ($this->warehouse_id >0) {
                    $sql .= " and odo.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql .= " and odo.do_warehouse_id = '".$this->do_warehouse_id."' ";
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
			INNER JOIN oc_x_relevant_deliver_binding rdb ON doo2.relevant_id=rdb.relevant_id AND rdb.deliver_order_id=doo2.deliver_order_id
			INNER JOIN oc_x_warehouse_requisition_temporary oxwt2 ON oxwt2.relevant_id = doo2.relevant_id
			WHERE
				doo2.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)			";
                if ($this->warehouse_id >0) {
                    $sql .= " and doo2.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql .= " and doo2.do_warehouse_id = '".$this->do_warehouse_id."' ";
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
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	) 			";
                if ($this->warehouse_id >0) {
                    $sql .= " and odo3.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql .= " and odo3.do_warehouse_id = '".$this->do_warehouse_id."' ";
                }
                $sql .= "AND doo3.container_id > 0
			GROUP BY
				doo3.deliver_order_id,
				doo3.container_id
		) oxw2 ON oxw2.deliver_order_id = ios.deliver_order_id
		AND oxw2.container_id = ios.container_id ";
//                var_dump($sql);





                $sql1 = "SELECT
	batch.deliver_date,
	batch.do_warehouse_id ,
	batch.warehouse_id,
	batch.product_id,oop.name,
	wrt_out.quantity batch_quantity,wrt_out2.quantity type_quantity,wrt_out.relevantNum
	
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
				INTERVAL '" .$this->date_search . "' DAY
			)
		AND DATE_SUB(
			CURRENT_DATE (),
			INTERVAL 0 DAY
		) ";
                if ($this->warehouse_id >0) {
                    $sql1 .= " and obs.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql1 .= " and obs.do_warehouse_id = '".$this->do_warehouse_id."' ";
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
		wr2.to_warehouse warehouse_id,
		GROUP_CONCAT(DISTINCT wrt2.relevant_id) relevantNum
	FROM
		oc_x_warehouse_requisition wr2
	LEFT JOIN oc_x_warehouse_requisition_temporary wrt2 ON wrt2.relevant_id = wr2.relevant_id
	LEFT JOIN oc_product op2 ON op2.product_id = wrt2.product_id
	WHERE
		wr2.deliver_date BETWEEN DATE_SUB(
			CURRENT_DATE (),
			INTERVAL '" . $this->date_search . "' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	)";
                if ($this->warehouse_id >0) {
                    $sql1 .= " and wr2.to_warehouse = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql1 .= " and wr2.from_warehouse = '".$this->do_warehouse_id."' ";
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
			INTERVAL '".$this->date_search."' DAY
		)
	AND DATE_SUB(
		CURRENT_DATE (),
		INTERVAL 0 DAY
	) ";
                if ($this->warehouse_id >0) {
                    $sql1 .= " and odo.warehouse_id = '".$this->warehouse_id."' ";
                }
                if ($this->do_warehouse_id >0) {
                    $sql1 .= " and odo.do_warehouse_id = '".$this->do_warehouse_id."' ";
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
//var_dump($sql1);
//        echo $sql1;
//        var_dump($sql);

        $repack_product = $this->Db->query($sql)->rows;
        $box_product = $this->Db->query($sql1)->rows;
//echo "<pre>";print_r($repack_product);echo "<pre>";exit;
        if ($this->search_type == '3'){
            /*
             * 入库（已入库未合单和货未合齐）
             * */
            $sql = "SELECT
                GROUP_CONCAT(DISTINCT odo.deliver_order_id)deliver_order_id
            FROM
                oc_x_deliver_order odo
            LEFT JOIN oc_order o ON odo.order_id = o.order_id
            WHERE
                odo.order_status_id IN (12) and odo.deliver_date > DATE_SUB(CURRENT_DATE (),INTERVAL '" . $this->date_search . "' DAY) ";
//        echo $sql;
            if ($this->warehouse_id >0) {
                $sql .= " and odo.warehouse_id = '".$this->warehouse_id."' ";
            }
            if ($this->do_warehouse_id >0) {
                $sql .= " and odo.do_warehouse_id = '".$this->do_warehouse_id."' ";
            }
            $sql .= "
AND o.order_status_id = 5 and (odo.do_warehouse_id !=  odo.warehouse_id or (odo.warehouse_id = odo.do_warehouse_id and odo.is_repack = 1))";
//        echo $sql;
//        $result=[];
//            var_dump($sql);
            $result = $this->Db->query($sql)->rows;
            if (!empty($result['deliver_order_id'])) {
                $sql = "SELECT
        o.order_id,
        sum(ios.repack_quantity) sortS,
        sum(ios.box_quantity) sortZ,
        sum(dos.repack_quantity) shootS,
        sum(dos.box_quantity) shootZ,
        o.warehouse_id,if(o.is_urgent=0,null ,'是') is_urgent,if(oc.is_stage_target=0,null ,'是') is_stage_target
    FROM
        oc_x_deliver_order odo
    LEFT JOIN oc_order o ON odo.order_id = o.order_id
    LEFT JOIN oc_customer oc on oc.customer_id = o.customer_id
    LEFT JOIN (
        SELECT
            sum(
    
                IF (container_id = 0, quantity, 0)
            ) AS box_quantity,
            sum(
    
                IF (container_id > 0, quantity, 0)
            ) repack_quantity,
            deliver_order_id,
            order_id
        FROM
            oc_x_inventory_deliver_order_sorting
        WHERE
            deliver_order_id IN (".$result['deliver_order_id'].")
        GROUP BY
            deliver_order_id
    ) dos ON dos.deliver_order_id = odo.deliver_order_id
    LEFT JOIN (
        SELECT
            sum(
    
                IF (container_id > 0, 0, quantity)
            ) AS box_quantity,
            COUNT(
                DISTINCT
                IF (
                    container_id > 0,
                    container_id,
                    NULL
                )
            ) repack_quantity,
            deliver_order_id,
            order_id
        FROM
            oc_x_inventory_order_sorting
        WHERE
            deliver_order_id IN (".$result['deliver_order_id'].")
        AND STATUS = 1
        GROUP BY
            deliver_order_id
    ) ios ON ios.deliver_order_id = odo.deliver_order_id
    WHERE
        odo.deliver_order_id IN (".$result['deliver_order_id'].")
    GROUP BY
     order_id";
//                var_dump($sql);
            $nullSingle = $this->Db->query($sql)->rows;

            }

        }
//        echo $sql;
        $repack_products = [];
        $box_products = [];
        foreach ($repack_product as $value1) {
            $repack_products[$value1['deliver_order_id']]['deliver_order_id'] = $value1['deliver_order_id'];
            $repack_products[$value1['deliver_order_id']]['relevant_id'] = $value1['relevant_id'];
            $repack_products[$value1['deliver_order_id']]['do_warehouse_id'] = $value1['do_warehouse_id'];
            $repack_products[$value1['deliver_order_id']]['do_warehouse_name'] = $dataWarehouse[$value1['do_warehouse_id']]['warehouse'];
            $repack_products[$value1['deliver_order_id']]['warehouse_id'] = $value1['warehouse_id'];
            $repack_products[$value1['deliver_order_id']]['warehouse_name'] = $dataWarehouse[$value1['warehouse_id']]['warehouse'];
            $repack_products[$value1['deliver_order_id']]['deliver_date'] = $value1['deliver_date'];
            $repack_products[$value1['deliver_order_id']]['order_id'] = $value1['order_id'];
            $repack_products[$value1['deliver_order_id']]['is_urgent'] = $value1['is_urgent'];
            $repack_products[$value1['deliver_order_id']]['is_stage_target'] = $value1['is_stage_target'];
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
//            $box_products[$key1]['diff_num'] = empty($box_products[$key1]['diff_num']) ? (empty($value2['type_quantity'])?0:(int)$value2['type_quantity']) : $box_products[$key1]['ios_num'] + (empty($value2['type_quantity'])?0:(int)$value2['type_quantity']);
            @$box_products[$key1]['diff_num'] += $value2['type_quantity'];
            $box_products[$key1]['products'][$value2['product_id']] = $value2;

        }
//        var_dump($box_products);
//echo "<pre>";print_r($box_products);echo "<pre>";exit;

        $return = [
            'return_code' => 'SUCCESS',
            'return_msg'  => '',
            'return_data' => ['repack_products'=>$repack_products,'box_products'=>$box_products,'dataWarehouse'=>$dataWarehouse,'nullSingle'=>isset($nullSingle)?$nullSingle:'null',]
        ];

        if(empty($repack_products) && empty($box_products)) {
            $return = [
                'return_code' => 'ERROR',
                'return_msg'  => '无数据',
                'return_data' => ''
            ];

        }
        echo json_encode($return);
    }
    /*
     * 入库（已入库未合单和货未合齐）
     * */
    public function nullSingle()
    {


        $sql = "SELECT
	GROUP_CONCAT(DISTINCT odo.deliver_order_id)deliver_order_id
FROM
	oc_x_deliver_order odo
LEFT JOIN oc_order o ON odo.order_id = o.order_id
WHERE
	odo.order_status_id IN (12) and date(odo.deliver_date) >  DATE_SUB(CURRENT_DATE(),INTERVAL '".$this->date_search."' DAY)  ";
//        echo $sql;
        if ($this->warehouse_id >0) {
            $sql .= " and odo.warehouse_id = '".$this->warehouse_id."' ";
        }
        if ($this->do_warehouse_id >0) {
            $sql .= " and odo.do_warehouse_id = '".$this->do_warehouse_id."' ";
        }
        $sql .= "
AND o.order_status_id = 5 and (odo.do_warehouse_id !=  odo.warehouse_id or (odo.warehouse_id = odo.do_warehouse_id and odo.is_repack = 1))";
//        echo $sql;
//        $result=[];
//        var_dump($sql);
        $result = $this->Db->query($sql)->rows;


        if (!empty($result['deliver_order_id'])) {
            $sql = "SELECT
	o.order_id,
	sum(ios.repack_quantity) sortS,
	sum(ios.box_quantity) sortZ,
	sum(dos.repack_quantity) shootS,
	sum(dos.box_quantity) shootZ,
	o.warehouse_id,if(o.is_urgent=0,null ,'是') is_urgent,if(oc.is_stage_target=0,null ,'是') is_stage_target
FROM
	oc_x_deliver_order odo
LEFT JOIN oc_order o ON odo.order_id = o.order_id
LEFT JOIN oc_customer oc on oc.customer_id = o.customer_id
LEFT JOIN (
	SELECT
		sum(

			IF (container_id = 0, quantity, 0)
		) AS box_quantity,
		sum(

			IF (container_id > 0, quantity, 0)
		) repack_quantity,
		deliver_order_id,
		order_id
	FROM
		oc_x_inventory_deliver_order_sorting
	WHERE
		deliver_order_id IN (".$result['deliver_order_id'].")
	GROUP BY
		deliver_order_id
) dos ON dos.deliver_order_id = odo.deliver_order_id
LEFT JOIN (
	SELECT
		sum(

			IF (container_id > 0, 0, quantity)
		) AS box_quantity,
		COUNT(
			DISTINCT
			IF (
				container_id > 0,
				container_id,
				NULL
			)
		) repack_quantity,
		deliver_order_id,
		order_id
	FROM
		oc_x_inventory_order_sorting
	WHERE
		deliver_order_id IN (".$result['deliver_order_id'].")
	AND STATUS = 1
	GROUP BY
		deliver_order_id
) ios ON ios.deliver_order_id = odo.deliver_order_id
WHERE
	odo.deliver_order_id IN (".$result['deliver_order_id'].")
GROUP BY
 order_id";
            echo $sql;
            $results = $this->Db->query($sql)->rows;
            echo json_encode($results);
        }else{
            $result=[];
            echo json_encode($result);
        }


    }
    /*
     * 配送
     * */
    //滞留订单
    public function logistic()
    {
        $sql="select oo.order_id, group_concat(distinct concat('[', if(oo.do_warehouse_id=oo.warehouse_id, w.shortname, dw.shortname),']', oo.deliver_order_id,'-',oos.name)) sorting, 
if(oi.inv_comment is null, '', oi.inv_comment) stockId, 
bd.bd_name,  ar.name bd_area,
o.customer_id,
o.deliver_date logisticDate,
ifnull(la.logistic_driver_title,'暂无') logisticPer,
if(o.is_urgent=0,'否' ,'是') is_urgent,if(c.is_stage_target=0,'否','是') is_stage_target,
DATEDIFF(current_date(),o.deliver_date) diffDates	
from oc_x_deliver_order oo
inner join oc_x_deliver_order_product op on oo.deliver_order_id = op.deliver_order_id
inner join oc_order o on oo.order_id = o.order_id
left join oc_customer c on o.customer_id = c.customer_id
left join oc_customer_group cg on c.customer_group_id = cg.customer_group_id
left join oc_x_customer_class cc on c.current_customer_class_id = cc.customer_class_id
left join oc_x_deliver_order_status oos on oo.order_status_id = oos.order_status_id
left join oc_x_deliver_order_inv doi on oo.deliver_order_id = doi.deliver_order_id and oo.warehouse_id = $this->warehouse_id
left join oc_order_inv oi on o.order_id = oi.order_id
left join oc_x_area ar on o.area_id = ar.area_id
left join oc_x_bd bd on o.bd_id  = bd.bd_id
left join oc_x_warehouse w on oo.warehouse_id = w.warehouse_id
left join oc_x_warehouse dw on oo.do_warehouse_id = dw.warehouse_id
left join oc_order_status os on o.order_status_id = os.order_status_id
left join oc_order_deliver_status ds on o.order_deliver_status_id = ds.order_deliver_status_id
left join oc_order_payment_status ps on o.order_payment_status_id = ps.order_payment_status_id
left join oc_x_stock_move sm on o.order_id = sm.order_id and sm.inventory_type_id = 12
left join oc_x_logistic_allot_order lao on o.order_id = lao.order_id
left join oc_x_logistic_allot la on lao.logistic_allot_id = la.logistic_allot_id
where oo.warehouse_id = '".$this->warehouse_id."' and o.order_status_id !=3 and o.deliver_date between '".$this->sortStartDate."' and SUBDATE(CURRENT_DATE(),INTERVAL 0 DAY)
and o.order_deliver_status_id = 1
group by oo.order_id";

        echo $sql;
        $list=$this->Db->query($sql);
        $msg= $list->rows;
        echo json_encode($msg);
    }
    //差评
    public function evaluate()
    {

        $sql="SELECT
                ff.order_id,ff.logistic_driver_id,la.logistic_driver_title,DATE(ff.date_added) date_added,ff.driver_score,if(oo.is_urgent=0,'否' ,'是') is_urgent,if(cc.is_stage_target=0,'否','是') is_stage_target
                FROM
                    `oc_x_feedback` ff
                LEFT JOIN oc_customer cc ON ff.customer_id=cc.customer_id
                LEFT JOIN oc_order oo ON ff.order_id=oo.order_id
                left join  oc_x_logistic_allot_order lao on lao.order_id = ff.order_id
                left join  oc_x_logistic_allot la on la.logistic_allot_id = lao.logistic_allot_id
                left join oc_x_logistic_driver ld on la.logistic_driver_id = ld.logistic_driver_id
                left join oc_x_logistic_agent lag on ld.logistic_agent_id = lag.logistic_agent_id
                WHERE
                    DATE(la.deliver_date) BETWEEN '".$this->sortStartDate."'
                AND SUBDATE(
                    CURRENT_DATE (),
                    INTERVAL 0 DAY
                )
                and la.warehouse_id='".$this->warehouse_id."' 
                and ff.driver_score>0
                GROUP BY order_id
                ORDER BY driver_score,date_added ASC";
//        var_dump($sql);
//        echo $sql;
        $list=$this->Db->query($sql);
        $msg= $list->rows;
        $_arr=[];
        if (!empty($msg)){
            foreach ($msg as $k=>$v){
                $_arr['base']=$msg;
                @$_arr['idea']['count']+=$v['driver_score'];
                @$_arr['idea']['countNum']+=1;
            }
            $_arr['idea']['avg']=round($_arr['idea']['count']/$_arr['idea']['countNum'],2);
        }

        echo json_encode($_arr);
    }
    /*
     * 回库退货
     *
     * */
    //退货上架
    public function returnPutaway()
    {

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
		w.shortname doWarehouse,
		o.order_id
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
	AND rdp.date_added between '".$this->sortStartDate."' and date_add(current_date(), interval 1 day)
	";
        if ($this->warehouse_id) {
            $sql_return .= " AND rdp.warehouse_id = '" . $this->warehouse_id. "'";
        }
        $sql_return .= "
	AND rdp.is_repack_missing = '0'
	AND rdp.is_back = '1'
	AND r.return_reason_id IN (2, 3, 4, 5, 6)
	GROUP BY
		rdp.product_id,date_added,r.order_id,rdp.warehouse_id ORDER BY rdp.warehouse_id,date_added
	";
        $return_data_raw = $this->Db->query($sql_return)->rows;
        $return_data = [];
        if (!empty($return_data_raw)) {
            foreach ($return_data_raw as $v1){
                $return_data[$v1['date_added'].$v1['warehouse_id']]['return_num'] = empty($return_data[$v1['date_added'].$v1['warehouse_id']]['return_num'])?intval($v1['return_quantity']):intval($v1['return_quantity'])+intval($return_data[$v1['date_added'].$v1['warehouse_id']]['return_num']);
                $return_data[$v1['date_added'].$v1['warehouse_id']]['shelf_num'] = empty($return_data[$v1['date_added'].$v1['warehouse_id']]['shelf_num'])?intval($v1['self_quantity']):intval($v1['self_quantity'])+intval($return_data[$v1['date_added'].$v1['warehouse_id']]['shelf_num']);
                $return_data[$v1['date_added'].$v1['warehouse_id']]['date_added'] = $v1['date_added'];
                $return_data[$v1['date_added'].$v1['warehouse_id']]['warehouse_id'] = $v1['warehouse_id'];
                $return_data[$v1['date_added'].$v1['warehouse_id']]['order_id'] = $v1['order_id'];
                if (intval($v1['return_quantity'])!= intval($v1['self_quantity'])) {
                    $return_data[$v1['date_added'].$v1['warehouse_id']]['product'][] = $v1;
                }
            }
        }
        echo json_encode($return_data);
    }
    //回框准确率
    public function returnContainer()
    {
        $sql="
            SELECT
                oo.order_id,
                la.logistic_driver_title,
                la.deliver_date AS out_ware_date,
                la.logistic_allot_id,
                la.logistic_driver_id,
                la.warehouse_id,
                ww.title
            FROM
                oc_order oo
            LEFT JOIN oc_x_logistic_allot_order lao ON lao.order_id = oo.order_id
            LEFT JOIN oc_x_logistic_allot la ON lao.logistic_allot_id = la.logistic_allot_id
            left join oc_x_warehouse ww on la.warehouse_id=ww.warehouse_id
            WHERE 1=1 ";

//                if($data['order_id']){
//                    $sql .= " AND oo.order_id = '".$data['order_id']."' ";
//                }

        if(!empty($this->sortStartDate)){
            $sql .= " AND la.deliver_date between '".$this->sortStartDate."' AND SUBDATE(
                    CURRENT_DATE (),
                    INTERVAL 0 DAY
                ) ";
        }
        if(!empty($this->warehouse_id)){
            $sql .= " AND oo.warehouse_id = '".$this->warehouse_id."' ";
        }


        $sql.=" AND la.logistic_allot_id >0 AND oo.order_status_id >= 6
            AND oo.order_deliver_status_id >= 2
            GROUP BY
                la.logistic_allot_id,oo.order_id
            ORDER BY
                la.deliver_date
        ";
//        echo $sql;

        $query=$this->Db->query($sql);
        $baseData= $query->rows;


//        return $baseData;
        $arr=[];
//        $_str='';
        $str='';

        foreach ($baseData as $v){
//            $arr[$v['logistic_allot_id']];
            $str.=','.$v['logistic_allot_id'];
//            $_arr=[$v['order_id']];
//            $_str.=','.$v['order_id'];
        }
//        $orderList=substr($_str,1);
        $logisticList=substr($str,1);
//        echo json_encode($logisticList);
        if (empty($logisticList)) {
//            echo json_encode($arr);
            return false;
        }
//        echo $logisticList;
        $sql="SELECT A.*,B.plan_count FROM
                (
                SELECT
                    count(DISTINCT odr.container_id) AS have_count,
                    count(
                        DISTINCT
                        IF (
                            odr.added_by_manager > 0,
                            odr.container_id,
                            NULL
                        )
                    ) AS admin_count,
                    count(
                        DISTINCT
                        IF (
                            odr.order_id = 0,
                            odr.container_id,
                            NULL
                        )
                    ) AS out_plan,
                    odr.logistic_allot_id,
                    la.logistic_driver_title
                FROM
                    oc_x_container_deliver_return odr
                LEFT JOIN oc_x_container ocr ON ocr.container_id = odr.container_id
                LEFT JOIN oc_x_logistic_allot la ON odr.logistic_allot_id=la.logistic_allot_id
                WHERE
                    odr.logistic_allot_id IN($logisticList) 
                AND odr.container_id > 0
                AND ocr.type != 8
                AND odr. STATUS = 1
                AND odr.checked = 1
                GROUP BY
                    odr.logistic_allot_id
                ) A
                INNER JOIN
                (
                SELECT
                    count(DISTINCT ios.container_id) AS plan_count,
                    lao.logistic_allot_id,
                    la.warehouse_id
                FROM
                    oc_x_container_fast_move ios
                LEFT JOIN oc_x_logistic_allot_order lao ON lao.order_id = ios.order_id
                LEFT JOIN oc_x_logistic_allot la ON lao.logistic_allot_id=la.logistic_allot_id
                LEFT JOIN oc_x_container oc ON oc.container_id = ios.container_id
                WHERE
                    ios.move_type = 1
                AND ios.container_id > 0
                AND oc.type != 8
                AND lao.logistic_allot_id IN($logisticList) 
                GROUP BY
                    lao.logistic_allot_id
                HAVING
                    plan_count > 0
                ) B ON A.logistic_allot_id=B.logistic_allot_id ";
//        echo $sql;
        $list=$this->Db->query($sql);
        $msg= $list->rows;
//        var_dump($msg);
        $arr_=[];
        $arr_['count']=0;
        $arr_['plan_count']=0;
        foreach ($msg as $k => $v){
            $arr_['count'] += $v['have_count'];//b
            $arr_['plan_count'] += $v['plan_count'];//a
        }
        $str=($arr_['count']-$arr_['plan_count'])/$arr_['count']*100;
        $avg=round($str,2);
        $_arr=[];
        if (isset($msg)){
            $_arr['base']=$msg;
            $_arr['avg']=$avg;
            $_arr['plan_count']=$arr_['plan_count'];
            $_arr['count']=$arr_['count'];
        }

        echo json_encode($_arr);

    }




    protected  function getScanFailureData()
    {
        $relevantId=$this->setRelevantList();
        $sql="
                SELECT A.relevant_id,A.container_id,A.product_id,A.quantity outNum,B.quantity relevantNum FROM
                (
                SELECT relevant_id,container_id,product_id,SUM(quantity )AS quantity FROM oc_x_warehouse_requisition_temporary WHERE relevant_id IN('".$relevantId['doList']."')
                AND relevant_status_id=2
                AND product_id>0
                GROUP BY relevant_id,container_id,product_id
                ) A
                INNER JOIN
                (
                SELECT relevant_id,container_id,product_id,SUM(num) quantity FROM oc_x_warehouse_requisition_item 
                WHERE relevant_id IN('".$relevantId['doList']."')
                AND product_id > 0
                AND STATUS=1
                GROUP BY relevant_id,container_id,product_id
                ) B ON A.relevant_id=B.relevant_id AND A.container_id=B.container_id AND A.product_id=B.product_id AND A.quantity != B.quantity
                GROUP BY relevant_id,container_id,product_id
                ORDER BY relevant_id DESC
                ";
        $list=$this->Db->query($sql);
        return $list->rows;
    }

    
    //散件
    public function getSortData()
    {

        $sql="SELECT
	A.date_added,
	A.deliver_order_id,
	A.order_id,
	SUM( A.quantity ) AS sortNum,
	ww.shortname,
	A.odsStatu,
	A.osStatu,
	A.deliver_date,
	GROUP_CONCAT(DISTINCT ios.container_id) container_id,
IF
	( ww.repack = 0, '散件仓库', '整件仓库' ) warehouseZS,
IF
	( oo.is_urgent = 0, '否', '是' ) AS urgent,
IF
	( cc.is_stage_target = 0, '否', '是' ) AS stageTarget 
FROM
	(
	SELECT
		doo.deliver_order_id,
		doo.order_id,
		dop.product_id,
		doo.warehouse_id,
		doo.do_warehouse_id,
		SUM(dop.quantity)quantity,
		doo.date_added,
		ods.name odsStatu,
		dos.`name` osStatu,
		doo.deliver_date
	FROM
		oc_x_deliver_order doo
		LEFT JOIN oc_x_deliver_order_product dop ON doo.deliver_order_id = dop.deliver_order_id
		LEFT JOIN oc_order_deliver_status ods ON doo.order_deliver_status_id=ods.order_deliver_status_id
	LEFT JOIN oc_order_status dos ON	doo.order_status_id=dos.order_status_id
	WHERE
		DATE( doo.deliver_date ) BETWEEN '".$this->sortStartDate."'
		AND date_sub( CURRENT_DATE ( ), INTERVAL 0 DAY ) 
		AND doo.do_warehouse_id = '".$this->do_warehouse_id."'
		AND doo.order_status_id IN ( 2, 4, 5 )
		AND dop.STATUS=1
	GROUP BY deliver_order_id,product_id	
	) A
	LEFT JOIN oc_x_inventory_order_sorting ios ON A.order_id=ios.order_id AND A.deliver_order_id=ios.deliver_order_id AND A.product_id=ios.product_id
	LEFT JOIN oc_product pp ON A.product_id = pp.product_id
	LEFT JOIN oc_x_warehouse ww ON A.do_warehouse_id = ww.warehouse_id
	LEFT JOIN oc_order oo ON A.order_id = oo.order_id
	LEFT JOIN oc_customer cc ON oo.customer_id = cc.customer_id 
GROUP BY
	deliver_order_id 
ORDER BY
	deliver_date DESC";
//        echo $sql;
//        var_dump($sql);
        $info=$this->Db->query($sql);
        $msg['data']=$info->rows;
        $totalItem=count($msg['data']);
        $pageSize = 50;
        $pageNum=$_REQUEST['pageNum'];
        $totalPage = ceil($totalItem/$pageSize);
        $startItem = ($pageNum-1) * $pageSize;
        $sql="SELECT
	A.date_added,
	A.deliver_order_id,
	A.order_id,
	SUM( A.quantity ) AS sortNum,
	ww.shortname,
	A.odsStatu,
	A.osStatu,
	A.deliver_date,
	ifnull(GROUP_CONCAT(DISTINCT ios.container_id),'无') container_id,
IF
	( ww.repack = 0, '散件仓库', '整件仓库' ) warehouseZS,
IF
	( oo.is_urgent = 0, '否', '是' ) AS urgent,
IF
	( cc.is_stage_target = 0, '否', '是' ) AS stageTarget 
FROM
	(
	SELECT
		doo.deliver_order_id,
		doo.order_id,
		dop.product_id,
		doo.warehouse_id,
		doo.do_warehouse_id,
		SUM(dop.quantity)quantity,
		doo.date_added,
		ods.name odsStatu,
		dos.`name` osStatu,
		doo.deliver_date
	FROM
		oc_x_deliver_order doo
		LEFT JOIN oc_x_deliver_order_product dop ON doo.deliver_order_id = dop.deliver_order_id
		LEFT JOIN oc_order_deliver_status ods ON doo.order_deliver_status_id=ods.order_deliver_status_id
	LEFT JOIN oc_order_status dos ON	doo.order_status_id=dos.order_status_id
	WHERE
		DATE( doo.deliver_date ) BETWEEN '".$this->sortStartDate."'
		AND date_sub( CURRENT_DATE ( ), INTERVAL 0 DAY ) 
		AND doo.do_warehouse_id = '".$this->do_warehouse_id."'
		AND doo.order_status_id IN ( 2, 4, 5 )
		AND dop.STATUS=1
	GROUP BY deliver_order_id,product_id	
	) A
	LEFT JOIN oc_x_inventory_order_sorting ios ON A.order_id=ios.order_id AND A.deliver_order_id=ios.deliver_order_id AND A.product_id=ios.product_id
	LEFT JOIN oc_product pp ON A.product_id = pp.product_id
	LEFT JOIN oc_x_warehouse ww ON A.do_warehouse_id = ww.warehouse_id
	LEFT JOIN oc_order oo ON A.order_id = oo.order_id
	LEFT JOIN oc_customer cc ON oo.customer_id = cc.customer_id 
GROUP BY
	deliver_order_id 
ORDER BY
	deliver_date DESC 
LIMIT $startItem,$pageSize";

//        var_dump($sql);
        $info=$this->Db->query($sql);
        $labels=$info->rows;
        $arr['totalItem'] = $totalItem;
        $arr['pageSize'] = $pageSize;
        $arr['totalPage'] = $totalPage;


        foreach($labels as $lab) {
            $arr['data_content'][] = $lab;
        }

        echo json_encode($arr);

    }
    protected function getOrderSortData()
    {
        $OrderSort=$this->setOrderList();
        $sql="
            SELECT
                A.order_id,
            IF (pp.repack = 0, A.orderNum, 0) Orderzheng,
            IF (pp.repack = 1, A.orderNum, 0) Ordersan,
            IF (pp.repack = 0, B.sortNum, 0) Sortzheng,
            IF (pp.repack = 1, B.sortNum, 0) Sortsan
            FROM
                (
                    SELECT
                        op.order_id,
                        op.product_id,
                        SUM(op.quantity) orderNum
                    FROM
                        oc_order oo 
                    LEFT JOIN oc_order_product op ON oo.order_id=op.order_id
                    WHERE
                        op.order_id IN ('".$OrderSort['ooList']."')
                    GROUP BY
                        order_id,
                        product_id
                ) A
            INNER JOIN (
                SELECT
                    order_id,
                    product_id,
                    sum(quantity) sortNum
                FROM
                    oc_x_inventory_order_sorting
                WHERE
                    order_id IN ('".$OrderSort['ooList']."')
                AND STATUS = 1
                GROUP BY
                    order_id,
                    product_id
            ) B ON A.order_id = B.order_id
            AND A.product_id = B.product_id
            AND A.orderNum != B.sortNum
            LEFT JOIN oc_product pp ON A.product_id = pp.product_id
            ORDER BY order_id DESC";
        $db=$this->Db->query($sql);
        $orderSortData=$db->row;
        echo json_encode($orderSortData);
    }
    public function getRetentionOrder()
    {
        $sql="SELECT
                o.deliver_date deliver_date,
                oxw.title title,
                count(o.order_id) orderNum,
                sum(IF(o.days >= 3, 1, 0)) orderThreeNum,
            sum(IF(o.days >= 3 and o.order_id2>0,1,0)) passOutNum,
            sum(IF(o.days >= 3,if(o.order_id2>0,0,1),0)) nullOutNum,
            CONCAT((sum(IF(o.days >= 3, 1, 0)) / count(o.order_id)) * 100,'','%') proportion
            FROM
                (
                    -- explain
                    SELECT
                        oo.order_id,
                        oo.deliver_date,
                        oo.date_out,
                        oo.warehouse_id,
                        datediff(
                            oo.date_out,
                            oo.deliver_date
                        ) days,
                        oo.order_type,ooh.order_id order_id2
                    FROM
                        oc_order oo
                    LEFT JOIN oc_order_history ooh ON ooh.order_id = oo.order_id
                    AND date(date_add(oo.deliver_date, interval 3 day)) = date(ooh.date_added)
                    AND ooh.order_status_id = 6
                    WHERE
                        oo.deliver_date BETWEEN '$this->sortStartDate'
                    AND '$this->sortEndDate'
                    AND oo.order_status_id != 3
                    GROUP BY
                        oo.order_id
                    HAVING
                        oo.order_type = 1
                ) o
            LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = o.warehouse_id
            GROUP BY
                o.warehouse_id,
                o.deliver_date
            ORDER BY
                o.warehouse_id,
                o.deliver_date";
        $info=$this->Db->query($sql);
        $RetentionOrder= $info->rows;
        echo  json_encode($RetentionOrder);
    }
    public function getRelevantData()
    {
        $sql="SELECT GROUP_CONCAT(deliver_order_id) doId FROM oc_x_deliver_order
WHERE date (deliver_date) BETWEEN '$this->sortStartDate ' AND '$this->sortEndDate '
AND warehouse_id='$this->do_warehouse_id'
AND relevant_id > 0";
        $db=$this->Db->query($sql);
        $deliverOrderS=$db->row;
        $sql="SELECT BC.deliver_order_id,BC.relevantS,BC.relevantS2,IFNULL(CC.relevantZ,0) AS relevantZ, IFNULL(CC.relevantZ2,0) AS relevantZ2 FROM
                (
                SELECT AB.deliver_order_id,AB.relevantZ relevantS,AC.relevantZ AS relevantS2 FROM
                (SELECT AA.deliver_order_id,IF(pp.repack=1,SUM(BB.quantity),0) relevantZ FROM
                (
                
                SELECT doo.relevant_id,ios.container_id,doo.deliver_order_id,ios.product_id,SUM(ios.quantity) quantity FROM oc_x_deliver_order doo 
                INNER JOIN oc_x_deliver_order_inv doi ON doo.deliver_order_id=doi.deliver_order_id
                INNER JOIN oc_x_inventory_order_sorting ios ON doi.deliver_order_id=ios.deliver_order_id
                WHERE
                doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                AND ios.container_id >0
                GROUP BY relevant_id,container_id,product_id
                ) AA LEFT JOIN
                (
                SELECT wrt.relevant_id,wrt.container_id,wrt.product_id,SUM(wrt.quantity) quantity FROM
                    (
                    SELECT rdb.relevant_id,doo.deliver_order_id FROM oc_x_deliver_order doo 
                    LEFT JOIN oc_x_warehouse_requisition wr ON doo.relevant_id=wr.relevant_id
                    LEFT JOIN oc_x_relevant_deliver_binding rdb ON wr.relevant_id=rdb.relevant_id
                    WHERE
                    doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                    GROUP BY relevant_id
                    ) A
                LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON A.relevant_id=wrt.relevant_id AND wrt.container_id>0 AND wrt.product_id>0
                WHERE
                wrt.relevant_status_id=2
                GROUP BY relevant_id,container_id,product_id
                ORDER BY relevant_id,container_id,product_id DESC
                ) BB ON AA.relevant_id=BB.relevant_id AND AA.container_id=BB.container_id AND AA.product_id=BB.product_id
                LEFT JOIN oc_product pp ON BB.product_id=pp.product_id
                GROUP BY deliver_order_id
                ) AB INNER JOIN
                (
                SELECT AA.deliver_order_id,IF(pp.repack=1,SUM(BB.quantity),0) relevantZ FROM
                (
                SELECT doo.relevant_id,ios.container_id,doo.deliver_order_id,ios.product_id,SUM(ios.quantity) quantity FROM oc_x_deliver_order doo 
                INNER JOIN oc_x_deliver_order_inv doi ON doo.deliver_order_id=doi.deliver_order_id
                INNER JOIN oc_x_inventory_order_sorting ios ON doi.deliver_order_id=ios.deliver_order_id
                WHERE
                doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                AND ios.container_id >0
                GROUP BY relevant_id,container_id,product_id
                ) AA LEFT JOIN
                (
                SELECT wrt.relevant_id,wrt.container_id,wrt.product_id,SUM(wrt.quantity) quantity FROM
                    (
                    SELECT rdb.relevant_id,doo.deliver_order_id FROM oc_x_deliver_order doo 
                    LEFT JOIN oc_x_warehouse_requisition wr ON doo.relevant_id=wr.relevant_id
                    LEFT JOIN oc_x_relevant_deliver_binding rdb ON wr.relevant_id=rdb.relevant_id
                    WHERE
                    doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                    GROUP BY relevant_id
                    ) A
                LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON A.relevant_id=wrt.relevant_id AND wrt.container_id>0 AND wrt.product_id>0
                WHERE
                wrt.relevant_status_id=4
                GROUP BY relevant_id,container_id,product_id
                ORDER BY relevant_id,container_id,product_id DESC
                ) BB ON AA.relevant_id=BB.relevant_id AND AA.container_id=BB.container_id AND AA.product_id=BB.product_id
                LEFT JOIN oc_product pp ON BB.product_id=pp.product_id
                GROUP BY deliver_order_id
                ) AC ON AB.deliver_order_id=AC.deliver_order_id AND AB.relevantZ != AC.relevantZ
                ) BC LEFT JOIN
                (
                SELECT AB.deliver_order_id,AB.relevantZ relevantZ,AC.relevantZ relevantZ2 FROM
                (
                SELECT AA.deliver_order_id,IF(pp.repack=0,SUM(BB.quantity),0) relevantZ FROM
                (
                SELECT doo.relevant_id,doo.deliver_order_id,ios.product_id,SUM(ios.quantity) quantity FROM oc_x_deliver_order doo 
                INNER JOIN oc_x_deliver_order_inv doi ON doo.deliver_order_id=doi.deliver_order_id
                INNER JOIN oc_x_inventory_order_sorting ios ON doi.deliver_order_id=ios.deliver_order_id
                WHERE
                doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                AND ios.container_id =0
                GROUP BY relevant_id,container_id,product_id
                ) AA LEFT JOIN
                (
                SELECT wrt.relevant_id,wrt.product_id,SUM(wrt.quantity) quantity FROM
                    (
                    SELECT rdb.relevant_id,doo.deliver_order_id FROM oc_x_deliver_order doo 
                    LEFT JOIN oc_x_warehouse_requisition wr ON doo.relevant_id=wr.relevant_id
                    LEFT JOIN oc_x_relevant_deliver_binding rdb ON wr.relevant_id=rdb.relevant_id
                    WHERE
                    doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                    GROUP BY relevant_id
                    ) A
                LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON A.relevant_id=wrt.relevant_id AND wrt.container_id=0 AND wrt.product_id>0
                WHERE
                wrt.relevant_status_id=2
                GROUP BY relevant_id,product_id
                ORDER BY relevant_id,product_id DESC
                ) BB ON AA.relevant_id=BB.relevant_id  AND AA.product_id=BB.product_id
                LEFT JOIN oc_product pp ON BB.product_id=pp.product_id
                GROUP BY deliver_order_id
                ) AB INNER JOIN
                (
                SELECT AA.deliver_order_id,IF(pp.repack=0,SUM(BB.quantity),0) relevantZ FROM
                (
                SELECT doo.relevant_id,doo.deliver_order_id,ios.product_id,SUM(ios.quantity) quantity FROM oc_x_deliver_order doo 
                INNER JOIN oc_x_deliver_order_inv doi ON doo.deliver_order_id=doi.deliver_order_id
                INNER JOIN oc_x_inventory_order_sorting ios ON doi.deliver_order_id=ios.deliver_order_id
                WHERE
                doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                AND ios.container_id =0
                GROUP BY relevant_id,product_id
                ) AA LEFT JOIN
                (
                SELECT wrt.relevant_id,wrt.product_id,SUM(wrt.quantity) quantity FROM
                    (
                    SELECT rdb.relevant_id,doo.deliver_order_id FROM oc_x_deliver_order doo 
                    LEFT JOIN oc_x_warehouse_requisition wr ON doo.relevant_id=wr.relevant_id
                    LEFT JOIN oc_x_relevant_deliver_binding rdb ON wr.relevant_id=rdb.relevant_id
                    WHERE
                    doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                    GROUP BY relevant_id
                    ) A
                LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON A.relevant_id=wrt.relevant_id AND wrt.container_id=0 AND wrt.product_id>0
                WHERE
                wrt.relevant_status_id=4
                GROUP BY relevant_id,product_id
                ORDER BY relevant_id,product_id DESC
                ) BB ON AA.relevant_id=BB.relevant_id AND AA.product_id=BB.product_id
                LEFT JOIN oc_product pp ON BB.product_id=pp.product_id
                GROUP BY deliver_order_id
                ) AC ON AB.deliver_order_id=AC.deliver_order_id AND AB.relevantZ != AC.relevantZ
                ) CC ON BC.deliver_order_id=CC.deliver_order_id";
//        echo $sql;
        $db=$this->Db->query($sql);
        $relevantData=$db->rows;
        echo json_encode($relevantData);
    }

    public function getSingleData()
    {
        $sql="SELECT GROUP_CONCAT(deliver_order_id) doId FROM oc_x_deliver_order
WHERE date (deliver_date) BETWEEN '$this->sortStartDate ' AND '$this->sortEndDate '
AND do_warehouse_id='$this->do_warehouse_id'
AND relevant_id > 0";
        $db=$this->Db->query($sql);
        $deliverOrderS=$db->row;
//        echo json_encode($deliverOrderS);
        if (isset($deliverOrderS)){
            $sql="SELECT CC.order_id,CC.deliver_order_id,IF(pp.repack=1,SUM(CC.quantity),0) AS relevantNumS,IF(pp.repack=1,SUM(DD.quantity),0) AS countNum FROM
                (
                SELECT AA.order_id,AA.deliver_order_id,BB.product_id,IF(BB.quantity>AA.quantity,SUM(AA.quantity),SUM(BB.quantity)) quantity FROM
                (
                SELECT doo.relevant_id,ios.container_id,doo.order_id,doo.deliver_order_id,ios.product_id,SUM(ios.quantity) quantity 
                FROM oc_x_deliver_order doo 
                INNER JOIN oc_x_deliver_order_inv doi ON doo.deliver_order_id=doi.deliver_order_id
                INNER JOIN oc_x_inventory_order_sorting ios ON doi.deliver_order_id=ios.deliver_order_id
                WHERE
                doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                AND ios.container_id >0
                GROUP BY relevant_id,container_id,product_id
                ) AA INNER JOIN
                
                (
                
                SELECT wrt.relevant_id,wrt.container_id,wrt.product_id,SUM(wrt.quantity) quantity FROM
                    (
                    SELECT rdb.relevant_id,doo.deliver_order_id FROM oc_x_deliver_order doo 
                    INNER JOIN oc_x_warehouse_requisition wr ON doo.relevant_id=wr.relevant_id
                    INNER JOIN oc_x_relevant_deliver_binding rdb ON wr.relevant_id=rdb.relevant_id
                    WHERE
                    doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                    GROUP BY relevant_id
                    ) A
                INNER JOIN oc_x_warehouse_requisition_temporary wrt ON A.relevant_id=wrt.relevant_id AND wrt.container_id>0 AND wrt.product_id>0
                WHERE
                wrt.relevant_status_id=4
                AND wrt.container_id>0
                AND wrt.product_id>0
                GROUP BY relevant_id,container_id,product_id
                ORDER BY relevant_id,container_id,product_id DESC
                ) BB ON AA.relevant_id=BB.relevant_id AND AA.container_id=BB.container_id AND AA.product_id=BB.product_id
                GROUP BY order_id,deliver_order_id,product_id
                ORDER BY order_id DESC
                ) CC INNER JOIN 
                (
                SELECT iorq.order_id,iorq.deliver_order_id,iorq.product_id,SUM(iorq.sorting_quantity) AS quantity 
                FROM oc_x_deliver_order doo
                INNER JOIN oc_x_inventory_order_return_quantity iorq ON doo.deliver_order_id=iorq.deliver_order_id AND doo.order_id=iorq.order_id
                WHERE 
                doo.deliver_order_id IN(".$deliverOrderS['doId'].")
                GROUP BY iorq.order_id,iorq.deliver_order_id,iorq.product_id
                ORDER BY iorq.order_id DESC
                ) DD ON CC.order_id=DD.order_id AND CC.deliver_order_id=DD.deliver_order_id AND CC.product_id = DD.product_id AND CC.quantity != DD.quantity
                INNER JOIN oc_product pp ON DD.product_id=pp.product_id
                GROUP BY order_id,deliver_order_id
                ORDER BY deliver_order_id DESC";
            $db=$this->Db->query($sql);
            $SingleData=$db->rows;
        }else{
            $SingleData=[];
        }

        echo json_encode($SingleData);
    }



    //查当前库位下的商品
    public function findProductId()
    {
        $sql="select product_id,SUM(quantity) stock_num  from oc_x_stock_section_product where product_id = '$this->product_id' GROUP BY product_id";
//        echo $sql;
        $db=$this->Db->query($sql);
        $msg=$db->rows;
        if ($msg){
            $sql1="SELECT
                    a.product_id,a.`name`,a.is_repack,b.sku_barcode
                FROM
                     oc_product a
                LEFT JOIN oc_product_to_warehouse b ON a.product_id=b.product_id
                WHERE
                    a.product_id = '$this->product_id'
                GROUP BY product_id";
            $db1=$this->Db->query($sql1);
            $baCodeData=$db1->rows;
            if (!empty($this->id)){
                $sql2="select product_id,SUM(quantity) buffer_num  from oc_x_stock_section_buffer where id = '$this->id' ";
                $db2=$this->Db->query($sql2);
                $bufferData=$db2->rows;
                $arr2=[];
                foreach ($bufferData as $v2){
                    $arr2[$v2['product_id']]=$v2;
                }

            }else{
                $sql2="select product_id,SUM(quantity) buffer_num  from oc_x_stock_section_buffer where product_id = '$this->product_id' ";
                $db2=$this->Db->query($sql2);
                $bufferData=$db2->rows;
                $arr2=[];
                foreach ($bufferData as $v2){
                    $arr2[$v2['product_id']]=$v2;
                }
            }

            $arr=[];
            $arr1=[];

            foreach ($baCodeData as $v1){
                $arr1[$v1['product_id']]=$v1;
            }
            foreach ($msg as $k=> $v){
                $arr[$v['product_id']]=$v;
                $arr[$v['product_id']]['name']=$arr1[$v['product_id']]['name'];
                $arr[$v['product_id']]['sku_barcode']=$arr1[$v['product_id']]['sku_barcode'];
                $arr[$v['product_id']]['buffer_num']=isset($arr2[$v['product_id']]['buffer_num'])?$arr2[$v['product_id']]['buffer_num']:'0';
            }
            echo json_encode($arr);

        }else{
            echo json_encode($msg);
        }

    }

}

$obj= new Good($db);
//if (isset($_REQUEST['method'])) {
//    $obj->$_REQUEST['method']();
//}
