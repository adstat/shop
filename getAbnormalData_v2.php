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
    public $sortStartDate;
    public $sortEndDate;
    private static $instance;
    public function __construct($db)
    {
        $this->Db=$db;
        $this->opt=isset($_REQUEST['flag'])?$_REQUEST['flag']:'';
        /*分拣数据*/
        $this->do_warehouse_id = isset($_REQUEST['do_warehouse_id'])?$_REQUEST['do_warehouse_id']:'';
        $this->sortStartDate = isset($_REQUEST['sortStartDate'])?$_REQUEST['sortStartDate']:'';
        $this->sortEndDate = isset($_REQUEST['sortEndDate'])?$_REQUEST['sortEndDate']:'';
        $this->init();

    }

    private function init()
    {
        switch ($this->opt){
            case $this->opt=0:
                $this->getWarehouse();
                break;
            case $this->opt=1:
                $this->getSortData();
                break;
            case $this->opt=2:
                $this->stopCheck();
                break;
            case $this->opt=3:
                $this->getSingleData();
                break;
            case $this->opt=4:
                $this->getScanData();
                break;
            case $this->opt=5:
                $this->getOrderInfo();
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
WHERE deliver_date BETWEEN  date_sub(current_date(), interval '".$this->sortStartDate."' day)  AND date_sub(current_date(), interval '0' day)
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
        //订单数量
        $orderList=$this->setOrderList();
        $str=explode(',',$orderList['ooList']);
        $arrNum=count($str);
        $sql="SELECT
	IFNULL(sum(c.total),0)AS total,title,date_added
FROM
	(
		SELECT
			count(DISTINCT ocl.order_id) total,wu.title,ocl.date_added
		FROM
			oc_x_deliver_order_check_location ocl
		LEFT JOIN oc_x_deliver_order_check_details ocd ON ocd.check_location_id = ocl.check_location_id
		left join  oc_w_user wu on ocl.add_user=wu.user_id
		WHERE
			ocl.order_id IN (".$orderList['ooList'].")
		AND ocl. STATUS = 1
		GROUP BY
			ocl.order_id
	) c";
        $info=$this->Db->query($sql);
        $msg=$info->row;
        $arr_['num']=$arrNum;
        $_arr=array_merge($msg,$arr_);
        echo json_encode($_arr);
    }

    public function getOrderInfo()
    {
        $orderList=$this->setOrderList();
        $sql="select ocl.order_id,p.product_id,p.name,p.inv_class_sort,if(sum(ocd.sorting_quantity) != sum(ocd.final_quantity),'异常',null) isFinal, ocd.quantity,count(distinct ocl.order_id) count ,sum(ocd.sorting_quantity) as sorting,sum(ocd.final_quantity) final,ocl.old_inv_comment,ocl.new_inv_comment
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
        $sql="SELECT  A.deliver_order_id,A.order_id,SUM(A.quantity)AS sortNum,ww.title,IF(ww.repack=0,'散件仓库','整件仓库') warehouseZS,IF(oo.is_urgent=0,'否','是') AS urgent,IF(cc.is_stage_target=0,'否','是') AS stageTarget FROM
                (
                SELECT doo.deliver_order_id,doo.order_id,dop.product_id,doo.warehouse_id,doo.do_warehouse_id,dop.quantity 
                FROM oc_x_deliver_order doo
                LEFT JOIN oc_x_deliver_order_product dop ON doo.deliver_order_id=dop.deliver_order_id
                WHERE DATE(doo.deliver_date) BETWEEN '".$this->sortStartDate."' AND  date_sub(current_date(), interval 0 day)
                AND doo.do_warehouse_id='".$this->do_warehouse_id."'
                AND doo.order_status_id IN(2,4,5)
                )A
                INNER JOIN oc_product pp ON A.product_id=pp.product_id
                INNER JOIN oc_x_warehouse ww ON A.warehouse_id=ww.warehouse_id
                INNER JOIN oc_order oo ON A.order_id=oo.order_id
                INNER JOIN oc_customer cc ON oo.customer_id = cc.customer_id
                GROUP BY deliver_order_id";
            $info=$this->Db->query($sql);
            $msg['data']=$info->rows;
            $totalItem=count($msg['data']);
            $pageSize = 50;
            $pageNum=$_REQUEST['pageNum'];
            $totalPage = ceil($totalItem/$pageSize);
            $startItem = ($pageNum-1) * $pageSize;
        $sql="SELECT  A.deliver_order_id,A.order_id,SUM(A.quantity)AS sortNum,ww.title,IF(ww.repack=0,'散件仓库','整件仓库') warehouseZS,IF(oo.is_urgent=0,'否','是') AS urgent,IF(cc.is_stage_target=0,'否','是') AS stageTarget FROM
                (
                SELECT doo.deliver_order_id,doo.order_id,dop.product_id,doo.warehouse_id,doo.do_warehouse_id,dop.quantity 
                FROM oc_x_deliver_order doo
                LEFT JOIN oc_x_deliver_order_product dop ON doo.deliver_order_id=dop.deliver_order_id
                WHERE DATE(doo.deliver_date) BETWEEN '".$this->sortStartDate."' AND  date_sub(current_date(), interval 0 day)
                AND doo.do_warehouse_id='".$this->do_warehouse_id."'
                AND doo.order_status_id IN(2,4,5)
                )A
                INNER JOIN oc_product pp ON A.product_id=pp.product_id
                INNER JOIN oc_x_warehouse ww ON A.warehouse_id=ww.warehouse_id
                INNER JOIN oc_order oo ON A.order_id=oo.order_id
                INNER JOIN oc_customer cc ON oo.customer_id = cc.customer_id
                GROUP BY deliver_order_id
                order by deliver_order_id desc 
                limit $startItem,$pageSize";
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
