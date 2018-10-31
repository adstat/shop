<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/15
 * Time: 16:37
 */
//var_dump($_REQUEST);
header("Content-type:text/html;charset=utf8");
//return $_POST;
//var_dump($_POST);die;
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
$db;
//var_dump($db);
class SKY{
    public $go_data;
    public $to_date;
    public $entry_host;
    public $out_host;
    public $type;
    public $opt;
    public $Db;
    //初始化操作
    public function __construct($db)
    {
        $this->go_date=$_POST['go_date'];
        $this->to_date=$_POST['to_date'];
        $this->entry_host=$_POST['entry_host'];
        $this->out_host=$_POST['out_host'];
        $this->type=$_POST['type'];
        $this->opt=$_POST['opt'];
        $this->Db= $db;
        $this->init();
    }
    //调具体方法
    public function init()
    {
        switch ($this->opt){
            case $this->type ==1 && $this->opt=='good' :
                self::setAllotToArray($this->getNeedAllot(),$this->getThree(),$this->getTwo());
                break;
            case $this->type ==1 && $this->opt=='fen' :
                $sql="";
                break;
            case $this->type ==3 && $this->opt=='good' :
                $this->entry_host='';
                self::setNullAllotToArray($this->getThree(),$this->getTwo());
                break;
            case $this->type ==3 && $this->opt=='fen' :
                $sql="";
                break;
            case $this->type ==2 && $this->opt=='good' :
                self::setNullAllotToArray($this->getThree(),$this->getTwo());
                break;
            case $this->type ==2 && $this->opt=='fen' :
                $sql="";
                break;
            case $this->type ==4 && $this->opt=='good' :

                var_dump($this->getAbnormal());
                break;
            case $this->type ==4 && $this->opt=='fen' :
                $sql="";
                break;
            case $this->type ==5 && $this->opt=='good' :
                $this->entry_host='';
                $this->getAbnormal();
                break;
            case $this->type ==5 && $this->opt=='fen' :
                $sql="";
                break;

        }

    }
    //仓内、仓间、DO单的待审核、已确定、已取消
    protected function getThree()
    {
        $sql="SELECT 
	G.relevant_id,
	G.number,
	DATE(G.deliver_date) deliver_dates,
	G.from_warehouse,
	G.to_warehouse,
	G.from_house,
	G.to_house,
	SUM(IF(G.relevant_status_id=1,san,0)) null_audit_s,
	SUM(IF(G.relevant_status_id=1,zheng,0)) null_audit_z,
	SUM(IF(G.relevant_status_id=2,san,0)) already_notarize_s,
	SUM(IF(G.relevant_status_id=2,zheng,0)) already_notarize_z,
	SUM(IF(G.relevant_status_id=3,san,0)) already_abolish_s,
	SUM(IF(G.relevant_status_id=3,zheng,0)) already_abolish_z
FROM
(
SELECT 
	a.relevant_id,
	a.deliver_date,
	a.out_type number,
	a.deliver_order_id,
	a.relevant_status_id,
	SUM(IF(c.repack=1,b.num,0)) san,
	SUM(IF(c.repack=0,b.num,0)) zheng,
	d.shortname from_house,
	e.shortname to_house,
	a.from_warehouse,a.to_warehouse
FROM
	oc_x_warehouse_requisition a
LEFT JOIN oc_x_warehouse_requisition_item b ON a.relevant_id = b.relevant_id
LEFT JOIN oc_product c ON b.product_id=c.product_id
LEFT JOIN oc_x_warehouse d ON a.from_warehouse=d.warehouse_id
LEFT JOIN oc_x_warehouse e ON a.to_warehouse=e.warehouse_id
WHERE
a.out_type=$this->type
AND a.deliver_date BETWEEN '$this->go_date' AND '$this->to_date'";
        if ($this->entry_host){
            $sql.=" AND a.to_warehouse=$this->entry_host";
        }
        $sql.=" AND a.from_warehouse=$this->out_host
GROUP BY a.relevant_id
) G
GROUP BY
deliver_dates,G.from_warehouse,G.to_warehouse";
//        echo $sql;
        $db=$this->Db->query($sql);
        $list=$db->rows;
        return $list;
    }
    //已出库、已入库(仓内,仓间,DO)
    protected function getTwo()
    {
        $sql="SELECT 
	G.relevant_id,
	DATE(G.deliver_date) deliver_dates,
	G.from_warehouse,
	G.to_warehouse,
	G.from_house,
	G.to_house,
	SUM(out_san) out_san,
	SUM(out_zheng) out_zheng,
	SUM(entry_zheng) entry_zheng,
	SUM(entry_san) entry_san
FROM
(
SELECT 
	a.relevant_id,
	a.deliver_date,
	a.deliver_order_id,
	a.relevant_status_id,
	SUM(IF(b.container_id>0 AND b.relevant_status_id=2,b.quantity,0)) out_zheng,
	SUM(IF(b.container_id>0 AND b.relevant_status_id=4,b.quantity,0)) entry_zheng,
	COUNT(DISTINCT IF(b.container_id=0 AND b.relevant_status_id=2 ,b.container_id,null)) out_san,
	COUNT(DISTINCT IF(b.container_id=0 AND b.relevant_status_id=4,b.container_id,null)) entry_san,
	c.shortname to_house,
	d.shortname from_house,
	a.from_warehouse,a.to_warehouse
FROM
	oc_x_warehouse_requisition a
LEFT JOIN oc_x_warehouse_requisition_temporary b ON a.relevant_id = b.relevant_id
LEFT JOIN oc_x_warehouse c ON a.from_warehouse=c.warehouse_id
LEFT JOIN oc_x_warehouse d ON a.to_warehouse=d.warehouse_id
WHERE
a.out_type=$this->type
AND a.relevant_status_id in(4,6)
AND a.deliver_date BETWEEN '$this->go_date' AND '$this->to_date'";
        if ($this->entry_host){
            $sql.=" AND a.to_warehouse=$this->entry_host";
        }
        $sql.=" AND a.from_warehouse=$this->out_host
GROUP BY a.relevant_id
) G
GROUP BY
deliver_dates,G.from_warehouse,G.to_warehouse";
//        echo $sql;
        $db=$this->Db->query($sql);
        $list=$db->rows;
        return $list;
    }

    //需要调拨
    protected function getNeedAllot()
    {
        $sql="SELECT 
    G.relevant_id,
	DATE(G.deliver_date) deliver_dates,
	G.warehouse_id,
	G.do_warehouse_id,
	SUM(G.zheng) already_count_z,
	SUM(G.san) already_count_s,
	G.from_warehouse,
	G.to_warehouse,
	G.from_house,
	G.to_house
	
FROM
(
SELECT
    a.relevant_id,
	a.deliver_date,
	a.deliver_order_id,
	a.warehouse_id,
	a.do_warehouse_id,
	SUM(IF(c.container_id=0,c.quantity,0)) zheng,
 	COUNT(DISTINCT IF(c.container_id>0,c.container_id,NULL)) san,
	d.shortname to_house,
	e.shortname from_house,
	a.do_warehouse_id from_warehouse,a.warehouse_id to_warehouse
FROM
	oc_x_deliver_order a
LEFT JOIN oc_order b ON a.order_id = b.order_id
LEFT JOIN oc_x_inventory_order_sorting c ON a.deliver_order_id = c.deliver_order_id
LEFT JOIN oc_x_warehouse d ON a.warehouse_id =d.warehouse_id
LEFT JOIN oc_x_warehouse e ON a.do_warehouse_id =e.warehouse_id
WHERE
	b.order_status_id = 5
AND a.order_status_id = 6
AND a.warehouse_id !=a.do_warehouse_id";
        if ($this->entry_host){
        $sql.=" AND a.warehouse_id=$this->entry_host";
        }

$sql.=" AND a.do_warehouse_id=$this->out_host
AND a.deliver_date BETWEEN '$this->go_date' AND '$this->to_date'
GROUP BY a.deliver_order_id
) G
GROUP BY
deliver_dates,G.from_warehouse,G.to_warehouse";
//        echo $sql;
        $db=$this->Db->query($sql);
        $list=$db->rows;
        return $list;

    }

    //退货、异常(出入库)
    protected function getAbnormal()
    {
        $sql="SELECT 
	G.relevant_id,
	G.number,
	DATE(G.deliver_date) deliver_dates,
	G.from_house,
	G.to_house,
	G.from_warehouse,
	G.to_warehouse,
	SUM(IF(G.inventory_type_id=23,san,0)) already_entry_warehouse_s,
	SUM(IF(G.inventory_type_id=23,zheng,0)) already_entry_warehouse_z,
	SUM(IF(G.inventory_type_id=22,san,0)) already_out_warehouse_s,
	SUM(IF(G.inventory_type_id=22,zheng,0)) already_out_warehouse_z
FROM
(SELECT 
	a.relevant_id,
	a.out_type number,
	a.deliver_date,
	a.deliver_order_id,
	b.inventory_type_id,
	SUM(IF(d.repack=0,d.quantity,0)) san,
	SUM(IF(d.repack=1,d.quantity,0)) zheng,
	e.shortname from_house,
	f.shortname to_house,
	a.from_warehouse,a.to_warehouse
FROM
	oc_x_warehouse_requisition a
LEFT JOIN 
 oc_x_stock_move b ON a.relevant_id = b.relevant_id
LEFT JOIN oc_x_stock_move_item c ON b.inventory_move_id=c.inventory_move_id
LEFT JOIN oc_product d ON c. product_id=d.product_id
LEFT JOIN oc_x_warehouse e ON a.from_warehouse=e.warehouse_id
LEFT JOIN oc_x_warehouse f ON a.to_warehouse=f.warehouse_id
WHERE
a.out_type='$this->type'
AND a.deliver_date BETWEEN '$this->go_date' AND '$this->go_date'";
        if ($this->entry_host){
           $sql.=" AND a.to_warehouse=$this->entry_host";
        }
$sql.=" AND a.from_warehouse=$this->out_host
GROUP BY b.inventory_move_id
) G
GROUP BY
deliver_dates,G.from_warehouse,G.to_warehouse";
//        echo $sql;
        $db=$this->Db->query($sql);
        $list=$db->rows;
        echo json_encode($list);
    }

    private static function setAllotToArray($getNeedAllot,$getThree,$getTwo)
    {
        $_arr=[];
        $arr_=[];
        $arr=[];
        if ($getNeedAllot){
            foreach ($getNeedAllot as $v){
                $_arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]=$v;
            }
            foreach ($getTwo as $v){
                $arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]=$v;
            }
//            var_dump($arr_);


            foreach ($getThree as $v){
//                var_dump($v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']);
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]=$v;
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['already_count_z']=empty($_arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$_arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['already_count_z'];
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['already_count_s']=empty($_arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$_arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['already_count_s'];
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_san']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_san'];
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_zheng']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_zheng'];
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_zheng']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_zheng'];
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_san']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_san'];
            }
//        var_dump($arr);
            echo json_encode($arr);
        }else {
            foreach ($getTwo as $v){
                $arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]=$v;
            }
            foreach ($getThree as $v){
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]=$v;
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_san']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_san'];
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_zheng']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_zheng'];
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_zheng']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_zheng'];
                $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_san']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_san'];
            }
            echo json_encode($arr);
        }
    }
    private static function setNullAllotToArray($getThree,$getTwo)
    {
        $arr_=[];
        $arr=[];
        foreach ($getTwo as $v){
            $arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]=$v;
        }
        foreach ($getThree as $v){
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]=$v;
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_san']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_san'];
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_zheng']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_zheng'];
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_zheng']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_zheng'];
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_san']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_san'];
        }
        echo json_encode($arr);

    }
    /*private static function setReturnToArray($getAbnormal)
    {
        $arr_=[];
        $arr=[];
        foreach ($getTwo as $v){
            $arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]=$v;
        }
        foreach ($getThree as $v){
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]=$v;
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_san']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_san'];
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_zheng']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['out_zheng'];
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_zheng']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_zheng'];
            $arr[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_san']=empty($arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']])?0:$arr_[$v['from_warehouse'].'@'.$v['to_warehouse'].'@'.$v['deliver_dates']]['entry_san'];
        }
        echo json_encode($arr);

    }*/
}
$obj= new SKY($db);