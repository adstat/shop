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
    public $type_id;
    public $warehouse_id;
    public $tableName;
    public $id;
    public $findId;
    public $product_id;
    public $tray_id;
    public $storage_id;
    public $quantity;
    private static $instance;
    public function __construct($db)
    {
        $this->Db=$db;
        $this->opt=isset($_REQUEST['flag'])?$_REQUEST['flag']:'';
        $this->type_id=isset($_REQUEST['type_id'])?$_REQUEST['type_id']:'';
        $this->tableName=isset($_REQUEST['tableName'])?$_REQUEST['tableName']:'';
        $this->id=isset($_REQUEST['id'])?$_REQUEST['id']:'';
        $this->findId=isset($_REQUEST['findId'])?$_REQUEST['findId']:'';
        /*主数据*/
        $this->id=isset($_REQUEST['id'])?$_REQUEST['id']:'';//产品编号
        $this->product_id=isset($_REQUEST['product_id'])?$_REQUEST['product_id']:'';//产品编号
        $this->tray_id=isset($_REQUEST['tray_id'])?$_REQUEST['tray_id']:'';//托盘号
        $this->quantity=isset($_REQUEST['quantity'])?$_REQUEST['quantity']:'';//数量
        $this->warehouse_id=isset($_REQUEST['warehouse_id'])?$_REQUEST['warehouse_id']:'';//仓库
        $this->to_area_id=isset($_REQUEST['to_area_id'])?$_REQUEST['to_area_id']:'';
        $this->from_area_id=isset($_REQUEST['from_area_id'])?$_REQUEST['from_area_id']:'';
        $this->product_id=isset($_REQUEST['product_id'])?$_REQUEST['product_id']:'';
        $this->user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:'';
//        $this->init();
//        $this->getInstance();
        $this->init();

    }

    private function init()
    {
        switch ($this->opt){
            case $this->opt=1:
                $this->insert();
                break;
            case $this->opt=2:
                $this->select();
                break;
            case $this->opt=3:
                $this->delete();
                break;
            case $this->opt=4:
                $this->find();
                break;
            case $this->opt=5:
                $this->getWarehouse();
                break;
            case $this->opt=6:
                $this->findStorageId();
                break;
            case $this->opt=7:
                $this->findProductId();
                break;
            case $this->opt=8:
                $this->findTrayId();
                break;
            case $this->opt=9:
                $this->updateStatu();
                break;


        }
    }

//    public static function getInstance()
//    {
//
//        if (!self::$instance instanceof self){
//            self::$instance= new self();
//        }
//         return self::$instance;
//
//    }

    public static function getParam()
    {
        $class_name=__CLASS__;
        $class= new $class_name;
        $url=$_SERVER['PHP_SELF'];
        $action=trim(strchr('/',$url),'/');
        $class->$action();

    }
    public function insert()
    {

            if (!empty($this->id)){//移库操作
                $sql="select quantity,status from oc_x_stock_section_buffer where id='$this->id'";
                $info=$this->Db->query($sql);
                $msg=$info->row;
                if ($msg['status'] =='0'){
                    $number=(int)$msg['quantity'];

                    $getNumber=(int)$this->quantity;
                    $number -= $getNumber;
                    $sql1="update oc_x_stock_section_buffer set `quantity` = '$number' , status = '1' where id = '$this->id'";
                    $this->Db->query($sql1);
                    $sql="insert into oc_x_stock_section_buffer (user_id,tray_id,from_area_id,to_area_id,product_id,type_id,quantity,warehouse_id,create_time
                    )values ('$this->user_id','$this->tray_id','$this->from_area_id','$this->to_area_id','$this->product_id','$this->type_id','$this->quantity',".$this->warehouse_id.",now())";
                    $msg=$this->Db->query($sql);

                }else{
                     echo $msg='error';
                    return false;
                }
            }else{//添加操作
                $sql="insert into oc_x_stock_section_buffer (user_id,tray_id,,to_area_id,product_id,type_id,quantity,warehouse_id,create_time
            )values ('$this->user_id','$this->tray_id','$this->to_area_id','$this->product_id','$this->type_id','$this->quantity',".$this->warehouse_id.",now())";
                $msg=$this->Db->query($sql);
            }


            /*other*/
            $sql_ssp="select quantity from oc_x_stock_section_product where product_id='$this->product_id'";
            $info_ssp=$this->Db->query($sql_ssp);
            $msg_ssp=$info_ssp->row;
            $ssp_number=(int)$msg_ssp['quantity'];
            $sspNowNum=(int)$ssp_number-(int)$this->quantity;
            $sql_ssps="update oc_x_stock_section_product set `quantity` = '$sspNowNum' where product_id='$this->product_id'";
            $this->Db->query($sql_ssps);

            $sqlInv="insert into oc_x_stock_section_product_move (warehouse_id,stock_section_id,section_move_type_id,product_id,quantity,date_added,added_by,added_name,order_id,status,purchase_order_id,relevant_id,repack
            )values ('$this->warehouse_id','$this->type_id','1','$this->product_id','$this->quantity',now(),'$this->user_id','','','1','','','')";
            $this->Db->query($sqlInv);

            echo json_encode($msg);




    }

    public function select()
    {
        $sql="select a.*,b.name as product_name from oc_x_stock_section_buffer a left join oc_product b on a.product_id=b.product_id where type_id = $this->type_id";
        $db=$this->Db->query($sql);
        $msg=$db->rows;
        echo json_encode($msg);
        /*其它*/

    }

    public function delete()
    {
        $sql="delete from oc_x_stock_section_buffer where id = $this->id and type_id = $this->type_id";
        $db=$this->Db->query($sql);
        echo json_encode($db);
    }
    //获取一条数据
    public function find()
    {
        $sql="select * from oc_product where product_id = $this->findId";
        $db=$this->Db->query($sql);
        $msg=$db->row;
        echo json_encode($msg);
    }
    public function getWarehouse()
    {
        $sql="select warehouse_id,title from oc_x_warehouse where warehouse_id=$this->id";
        $db=$this->Db->query($sql);
        $msg=$db->row;
        echo json_encode($msg);
    }
    //查当前库位下的商品
    public function findStorageId()
    {
        $msg='';
        $sql="select name from oc_x_stock_section where  name= '$this->to_area_id' and warehouse_id= '$this->warehouse_id' and stock_section_type_id= '$this->type_id' and status= '1'";
//        echo $sql;
        $db=$this->Db->query($sql);
        $obj=$db->row;

        if (!empty($obj)){
            $sql="select a.*,b.name as product_name from  oc_x_stock_section_buffer a  left  join  oc_product b on  a.product_id=b.product_id     where a.to_area_id = '$this->to_area_id' and a.type_id = '$this->type_id' and a.warehouse_id = '$this->warehouse_id'";
            $db=$this->Db->query($sql);
            $msg=$db->rows;
            if (empty($msg)){
                $msg='1';
            }else{
                $msg;
            }
        }else{
            $msg='0';
        }
        echo json_encode($msg);
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
    //查当前托盘下的商品
    public function findTrayId()
    {
        $sql="select * from oc_x_stock_section_buffer where tray_id = '$this->tray_id'";
//        echo $sql;
        $db=$this->Db->query($sql);
        $msg=$db->rows;
        echo json_encode($msg);
    }

    public function updateStatu()
    {
        $sql="update oc_x_stock_section_buffer set `is_submit` = '0' where id = '$this->id'";
        $db=$this->Db->query($sql);

//        $msg=$db->rows;
        echo json_encode($db);
    }
}

$obj= new Good($db);
