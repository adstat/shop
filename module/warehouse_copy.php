<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');

class WAREHOUSE{


    public function getperms(array $data){
        global $db;

        $username = $data['data']['inventory_user_id'] ? $data['data']['inventory_user_id'] : '';

        $sql = "SELECT WUG.user_group_id , WU.user_id , WUG.perms ,WU.warehouse_id  FROM  oc_w_user  WU LEFT JOIN oc_w_user_group WUG ON WU.user_group_id = WUG.user_group_Id  WHERE WU.user_id = '" . $db->escape($username) . "' ";
        $user_query =$db->query($sql);

        if ($user_query->num_rows) {
            $return['status'] = 2;
            $return['user'] = $user_query->row;
            return $return;

        }
        else{
            $return['status'] = 1;
            return $return;
        }

    }


    public function find_order(array $data){
        global $db;

        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $sql ="SELECT O.order_id , OI.inv_comment ,SUM(OP.quantity) quantity  FROM oc_order O
          LEFT JOIN  oc_order_product OP ON  OP.order_id = O.order_id
          LEFT JOIN  oc_order_inv OI ON  OI.order_id = O.order_id
          WHERE    O.order_status_id IN (2,5,6,8)  AND O.order_id = '" . $db->escape($order_id) . "'  ";

        $query = $db->query($sql);

        if ($query->num_rows) {
            $return['status'] = 2;
            $return['user'] = $query->row;
            return $return;

        }
        else{
            $return['status'] = 1;
            return $return;
        }
    }
    public function getcheck(array $data){
        global $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $sql = "SELECT check_status FROM oc_order_inv WHERE order_id = '".$order_id ."' ";
        $query = $db->query($sql);
        $result = $query->row;
        return $result;

    }

    public  function short_regist(array $data){
        global $db;

        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';

        $sql = " SELECT  C.product_id ,D.name,sum(C.quantity*-1) out_qty  ,D.sku ,OD.inventory_name FROM oc_order A
 LEFT JOIN oc_x_stock_move B ON A.order_id = B.order_id
 LEFT JOIN  oc_order_distr OD ON  A.order_id = OD.order_id
 LEFT JOIN oc_x_stock_move_item C ON  B.inventory_move_id = C.inventory_move_id
 LEFT JOIN oc_product D ON C.product_id = D.product_id
 WHERE  A.order_id = '" . $db->escape($order_id) . "'   AND  D.repack = 0 AND A.station_id =2  AND  A.order_status_id IN (2,5,6,8) and B.inventory_type_id = 12 and C.status = 1
 GROUP BY C.product_id ";
        $query = $db->query($sql);

        if ($query->num_rows) {
            $sql1 = "SELECT  E.container_id FROM oc_order A LEFT JOIN  oc_x_container_move E ON  E.order_id = A.order_id
 WHERE  A.order_id = '" . $db->escape($order_id) . "' ";
            $query1 = $db->query($sql1);

            $return['status'] = 2;
            $return['product'] = array($query1->rows,
                $query->rows);

            return $return;

        } else{
            $return['status'] = 1;
            return $return;
        }
    }



    public function submitReturn(array $data){

        global $db;
        $array=[];
        $array1=[];
        $product = $data['data']['product'];

        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $user_id =$data['data']['add_user'] ? $data['data']['add_user'] : '';


        foreach($product as $value) {
            $product_id = trim(substr($value[0],2));

            $sql3 = "SELECT product, sum(quantity) AS oty_quantity FROM  oc_return_deliver_product WHERE product_id ='".$product_id."'  AND order_id ='".$order_id."' ";

            $query3 = $db->query($sql3);
            $result3 = $query3->rows;

            $sum_que = $result3[0]['oty_quantity']+$value[3];

            if($sum_que>$value[2]){
                $array1[]=array($result3[0]['product'],$sum_que,$value[2]);
                continue;

            }elseif($value[3] > 0 AND $sum_que <= $value[2] ){
                $sql = "select price from oc_product where product_id = '" . $product_id . "'";
                $price = $db->query($sql)->row['price'];
                $total = $price * $value[3];
                $sql = "INSERT INTO oc_return_deliver_product (`order_id`,`return_reason_id`,`product_id`,`product`,`quantity`,`price`,`total`,`add_user_id`,`date_added`,`return_id`)VALUES ('" . $order_id . "','5','" . $product_id . "','" . $value[1] . "','" . $value[3] . "','" . $price . "','" . $total . "','$user_id',current_timestamp(),'1')";
                $query = $db->query($sql);

                $sql1 = "SELECT  product,quantity  FROM  oc_return_deliver_product  ORDER  BY  return_deliver_product_id DESC limit 0,1";
                $query1 = $db->query($sql1);
                $result= $query1->rows;
                $array[]=array($result);

            }
        }


        $array2=array($array,$array1);

        return $array2;


    }

    public function getInvComment(array $data){
        global $db;

        $order_id = $data['data']['order_id'];

        $sql = "select inv_comment from oc_order_inv where order_id = '".$order_id."'";

        $inv_comment = $db->query($sql)->row['inv_comment'];

        if(strlen($inv_comment)){
            $return['status'] = 2;
            $return['inv_comment'] = $inv_comment;
            return $return;
        }else{
            $return['status'] = 1;
            return $return;
        }
    }
    public function getSpareSkuProductInfo($data, $station_id, $language_id = 2, $origin_id){
        global $db;
        global $log;

        $sku = isset($data['data']['sku']) ? $data['data']['sku'] : false;
        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id']:false;

        if (!$sku) {
            return false;
        }

        if (strlen($sku) == 18) {
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, pd.special_price as price, p.product_id FROM oc_product AS p LEFT JOIN labelprinter.productlist AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE pd.barcode = '" . $sku . "' AND OP.order_id = '".$order_id."' AND p.repack = 0 ";
        }
        elseif(is_numeric($sku) && strlen($sku) <= 6) {
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id WHERE p.product_id = '".$sku."' AND OP.order_id = '".$order_id."' AND p.repack = 1 ";
        }
        elseif(is_numeric($sku) && strlen($sku) > 6) {
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE p.sku like '%".$sku."%' AND OP.order_id = '".$order_id."' AND p.repack = 1";
        }
        else{
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE p.inv_class_sort like '%".$sku."%' AND OP.order_id = '".$order_id."' AND p.repack = 1 ";
        }


        $query = $db->query($sql);

        if ($query->num_rows) {
            $return['status'] = 2;
            $return['product'] = $query->rows;
            return $return;

        }
        else{
            $return['status'] = 1;
            return $return;
        }
    }
    public function getSkuProductInfoS($data, $station_id, $language_id = 2, $origin_id) {
        global $db;
        global $log;

        $sku = isset($data['data']['sku']) ? $data['data']['sku'] : false;
        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id']:false;

        if (!$sku) {

            return false;
        }

//        if (strlen($sku) == 18) {
//            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, pd.special_price as price, p.product_id FROM oc_product AS p LEFT JOIN labelprinter.productlist AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE pd.barcode = '" . $sku . "' AND OP.order_id = '".$order_id."' AND P.repack = 0 ";
//        }
        if(is_numeric($sku) && strlen($sku) <= 6) {
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id WHERE p.product_id = '".$sku."' AND OP.order_id = '".$order_id."' AND p.repack = 0 ";
        }
        elseif(is_numeric($sku) && strlen($sku) > 6) {
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE p.sku like '%".$sku."%' AND OP.order_id = '".$order_id."' AND p.repack = 0";
        }
//        else{
//            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE p.inv_class_sort like '%".$sku."%' AND OP.order_id = '".$order_id."' AND P.repack = 0 ";
//        }


        $query = $db->query($sql);

        if ($query->num_rows) {
            $return['status'] = 2;
            $return['product'] = $query->rows;
            return $return;

        }
        else{
            $return['status'] = 1;
            return $return;
        }
    }
    public function getSpareProductID($data, $station_id, $language_id = 2, $origin_id){
        global $db;
        global $log;

        $sku = isset($data['data']['sku']) ? $data['data']['sku'] : false;
        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id']:false;

        if (!$sku) {
            return false;
        }
        if(is_numeric($sku) && strlen($sku) > 6){
            $sql = "SELECT pd.name, p.status, ptw.sku_barcode sku, ptw.stock_area inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id LEFT JOIN  oc_product_to_warehouse ptw ON p.product_id = ptw.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE ptw.sku_barcode like '%".$sku."%'AND OP.order_id = '".$order_id."' AND  p.repack = 1 ";

        }elseif(is_numeric($sku) && strlen($sku) <= 6){
            $sql = "SELECT pd.name, p.status, ptw.sku_barcode sku, ptw.stock_area inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id LEFT JOIN  oc_product_to_warehouse ptw ON p.product_id = ptw.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id   WHERE ptw.product_id = '".$sku."' AND OP.order_id = '".$order_id."' AND p.repack = 1 ";
        }


        //    $query = $db->query($sql);

        $query = $db->query($sql);

        if ($query->num_rows) {
            $return['status'] = 2;
            $return['product'] = $query->rows;
            return $return;

        }
        else{
            $return['status'] = 1;
            return $return;
        }
    }
    public function getProductID($data, $station_id, $language_id = 2, $origin_id){
        global $db;
        global $log;

        $sku = isset($data['data']['sku']) ? $data['data']['sku'] : false;
        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id']:false;

        if (!$sku) {
            return false;
        }
        if(is_numeric($sku) && strlen($sku) > 6){
            $sql = "SELECT pd.name, p.status, ptw.sku_barcode sku, ptw.stock_area inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id LEFT JOIN oc_product_to_warehouse ptw ON  p.product_id = ptw.product_id LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE ptw.sku_barcode like '%".$sku."%'AND OP.order_id = '".$order_id."' AND  p.repack = 0 ";

        }elseif(is_numeric($sku) && strlen($sku) <= 6){
            $sql = "SELECT pd.name, p.status, ptw.sku_barcode sku, ptw.stock_area inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id  LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id   WHERE p.product_id = '".$sku."' AND OP.order_id = '".$order_id."' AND p.repack = 0 ";
        }

        $query = $db->query($sql);

        if ($query->num_rows) {
            $return['status'] = 2;
            $return['product'] = $query->rows;
            return $return;

        }
        else{
            $return['status'] = 1;
            return $return;
        }

    }

    public function getSortNum(array  $data){
        global $db;
        global $log;

        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id'] : false;
        $sku = isset($data['data']['sku']) ? $data['data']['sku'] : false;

        $sql = "SELECT op.order_id,op.product_id,op. NAME,op.quantity,p.sku , sum(inv_pro.quantity) as inv_sum
						FROM oc_order AS oo
						LEFT JOIN oc_order_product AS op ON oo.order_id = op.order_id
						LEFT join oc_product p on op.product_id = p.product_id
						LEFT JOIN oc_x_inventory_order_sorting AS inv_pro ON inv_pro.order_id = op.order_id
						WHERE  p.product_id =  '" . $db->escape($sku) . "'  AND oo.order_id =   '" . $db->escape($order_id) . "'
						 ";

        $query = $db->query($sql);
        $result = $query->row;
        return $result;
    }





    public function getSpareGoods(array $data){
        global $db;

        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';

        $sql = " SELECT  C.product_id ,D.name,sum(C.quantity*-1) out_qty  ,D.sku  FROM oc_order A
 LEFT JOIN oc_x_stock_move B ON A.order_id = B.order_id
 LEFT JOIN oc_x_stock_move_item C ON  B.inventory_move_id = C.inventory_move_id
 LEFT  JOIN oc_product D ON C.product_id = D.product_id
 WHERE  A.order_id = '" . $db->escape($order_id) . "'    AND A.station_id =2  AND  A.order_status_id IN (2,5,6,8) and B.inventory_type_id = 12 and C.status = 1 AND D.repack = 1
 GROUP BY C.product_id  	";

        $query = $db->query($sql);
        if ($query->num_rows) {
            $return['status'] = 2;
            $return['product'] = $query->rows;
            return $return;

        } else{
            $return['status'] = 1;
            return $return;
        }

    }

    public function submitReturnSpare(array $data)
    {
        global $db;
        $array = [];
        $array1 = [];
        $product = $data['data']['product'];

        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $user_id = $data['data']['add_user'] ? $data['data']['add_user'] : '';


        foreach ($product as $value) {
            $product_id = trim(substr($value[0], 7));

            $sql3 = "SELECT product, sum(quantity) AS oty_quantity FROM  oc_return_deliver_product WHERE product_id ='" . $product_id . "'  AND order_id ='" . $order_id . "' ";

            $query3 = $db->query($sql3);
            $result3 = $query3->rows;

            $sum_que = $result3[0]['oty_quantity'] + $value[3];

            if ($sum_que > $value[2]) {
                $array1[] = array($result3[0]['product'], $sum_que, $value[2]);
                continue;

            } elseif ($value[3] > 0 AND $sum_que <= $value[2]) {
                $sql = "select price from oc_product where product_id = '" . $product_id . "'";
                $price = $db->query($sql)->row['price'];
                $total = $price * $value[3];
                $sql = "INSERT INTO oc_return_deliver_product (`order_id`,`return_reason_id`,`product_id`,`product`,`quantity`,`price`,`total`,`add_user_id`,`date_added`,`return_id`)VALUES ('" . $order_id . "','5','" . $product_id . "','" . $value[1] . "','" . $value[3] . "','" . $price . "','" . $total . "','$user_id',current_timestamp(),'1')";
                $query = $db->query($sql);

                $sql1 = "SELECT  product,quantity  FROM  oc_return_deliver_product  ORDER  BY  return_deliver_product_id DESC limit 0,1";
                $query1 = $db->query($sql1);
                $result = $query1->rows;
                $array[] = array($result);

            }
        }


        $array2 = array($array, $array1);

        return $array2;
    }

    public function confirm_product(array $data){
        global $db;
        $order_id = $data['data']['order_id'];
        $product_id = $data['data']['product_id'];
        $product_name = $data['data']['product_name'];
        $quantity = $data['data']['quantity'];
        $add_user_id = $data['data']['add_user_id'];
        $inventory_name = $data['data']['inventory_name'];
        $ordersorted = $data['data']['ordersorted'];

        $sql = "SELECT sum(quantity)AS sort_quantity FROM oc_x_order_check_details WHERE order_id = '".$order_id ."' and product_id = '". $product_id."'";

        $query = $db->query($sql);
        $sort_quantity = $query->row;
        $results = $sort_quantity['sort_quantity'] + $quantity;

        if($results <= $ordersorted  and $quantity > 0){
            $sql = "INSERT INTO oc_x_order_check_details  (`order_id`,`product_id`,`product_name`,`quantity`,`add_user_id`,`inventory_name`,`date_added`) VALUES ('".$order_id."','".$product_id."','".$product_name."','".$quantity."','".$add_user_id."','".$inventory_name."',current_timestamp())";

            $query = $db->query($sql);
            $result = $query->rows;
            $result['status'] = 1;
            return $result;
        }else{
            $result['status'] =2;
            return $result;
        }

    }

    function location_details(array $data)
    {
        global $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $sql = "  SELECT A.order_id , B.product_id ,sum(B.quantity) AS order_quantity ,ptw.sku_barcode sku ,C.name,
  sum(if(OM.quantity*-1 is null,'0',OM.quantity*-1)) AS oty_quantity,if(D.STATUS =1,'1','0') as status,
  C.station_section_title FROM oc_order A
                  LEFT JOIN oc_order_product B ON  A.order_id = B.order_id
                  LEFT JOIN oc_product C   ON  B.product_id = C.product_id
                  LEFT JOIN  oc_product_to_warehouse  ptw ON  B.product_id = ptw.product_id and ptw.warehouse_id = '".$warehouse_id."'
                  LEFT JOIN oc_x_order_check_details D ON A.order_id = D.order_id and D.product_id = B.product_id
                  LEFT JOIN (SELECT O.order_id ,SMI.quantity , SMI.product_id FROM  oc_order O LEFT JOIN  oc_x_stock_move SM ON  O.order_id = SM.order_id
                              LEFT JOIN  oc_x_stock_move_item SMI ON  SM.inventory_move_id = SMI.inventory_move_id  WHERE O.order_id = '" . $order_id . "'  GROUP BY  SMI.product_id ) OM ON  OM.order_id = A.order_id and OM.product_id = B.product_id
           WHERE A.order_id = '" . $order_id . "' and C.repack = 0    GROUP BY A.order_id, C.product_id ";

        $query = $db->query($sql);
        if ($query->num_rows) {
            $sql1 = "SELECT  E.container_id FROM oc_order A LEFT JOIN  oc_x_container_move E ON  E.order_id = A.order_id
 WHERE  A.order_id = '" . $order_id . "' ";
            $query1 = $db->query($sql1);

            $return['status'] = 2;
            $return['product'] = array($query1->rows,
                $query->rows);
            return $return;
        }
    }

    function cancel_product(array $data){

        global $db;
        $order_id = $data['data']['order_id'];
        $product_id = $data['data']['product_id'];

        $sql = "select status from oc_x_order_check_details WHERE order_id = '".$order_id."' and product_id = '".$product_id ."' ";
        $query = $db->query($sql);
        $results = $query->row;
        if($results['status'] == 1){
            $sql = "delete from oc_x_order_check_details WHERE order_id = '".$order_id."' and product_id = '".$product_id."' ";
            $query = $db->query($sql);
            $result = $query->row;
            $result['status'] = 1;
            return $result;
        }else{
            $result['status'] = 2;
            return $result;
        }

    }


    public function submitCheckDetails(array $data){
        global $db;
        $array=[];
        $array1=[];
        $product = $data['data']['product'];
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $user_id =$data['data']['add_user'] ? $data['data']['add_user'] : '';


        foreach($product as $value) {

            $product_id = trim(substr($value[0],2));

            $sql = "select sum(A.quantity) AS sort_quantity,B.inventory_name from oc_x_order_check_details A  RIGHT JOIN  oc_order_distr B ON  A.order_id = B.order_id WHERE B.order_id = '". $order_id ."'";

            $query = $db->query($sql);
            $sort_quantity = $query->row;
            $results = $sort_quantity['sort_quantity']+$value[3] ;

            if($results <= $value[2] and $value[3]>0 ){
                $sql = "INSERT INTO oc_x_order_check_details  (`order_id`,`product_id`,`product_name`,`quantity`,`add_user_id`,`inventory_name`,`date_added`) VALUES ('".$order_id."','".$product_id."','".$value[1]."','".$value[3]."','".$user_id."','".$sort_quantity['inventory_name']."',current_timestamp())";
                $query = $db->query($sql);
                $result = $query->rows;
                $array[] = array($value[0],$value[1],'status'=>1);

            }else{
                $array1[]=array($value[0],$value[1],'status'=>2);

            }

        }

        $array2 = array($array,$array1);
        return $array2;

    }

    public  function getSpareDetails(array $data){

        global $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $sql = "  SELECT A.order_id , B.product_id ,sum(B.quantity) AS order_quantity ,ptw.sku_barcode sku ,C.name,
  sum(if(OM.quantity*-1 is null,'0',OM.quantity*-1)) AS oty_quantity,if(D.status =1,'1','0') as status,
  C.station_section_title FROM oc_order A
                  LEFT JOIN oc_order_product B ON  A.order_id = B.order_id
                  LEFT JOIN oc_product C   ON  B.product_id = C.product_id
                  LEFT JOIN  oc_product_to_warehouse ptw ON  B.product_id = ptw.product_id  and  ptw.warehouse_id = '".$warehouse_id."'
                  LEFT JOIN  oc_x_order_check_details D ON D.order_id = A.order_id and D.product_id = B.product_id
                  LEFT JOIN (SELECT O.order_id ,SMI.quantity , SMI.product_id FROM  oc_order O LEFT JOIN  oc_x_stock_move SM ON  O.order_id = SM.order_id
                              LEFT JOIN  oc_x_stock_move_item SMI ON  SM.inventory_move_id = SMI.inventory_move_id  WHERE O.order_id = '" . $order_id . "'  GROUP BY  SMI.product_id ) OM ON  OM.order_id = A.order_id and OM.product_id = B.product_id
           WHERE A.order_id = '" . $order_id . "' and C.repack = 1    GROUP BY A.order_id, C.product_id ";

        $query = $db->query($sql);

        if ($query->num_rows) {
            $sql1 = "SELECT  E.container_id FROM oc_order A LEFT JOIN  oc_x_container_move E ON  E.order_id = A.order_id
 WHERE  A.order_id = '" . $order_id . "' ";
            $query1 = $db->query($sql1);

            $return['status'] = 2;
            $return['product'] = array($query1->rows,
                $query->rows);
            return $return;
        }

    }

    public function submitCheckSpareDetails(array $data){
        global $db;
        $array=[];
        $array1=[];
        $product = $data['data']['product'];

        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $user_id =$data['data']['add_user'] ? $data['data']['add_user'] : '';


        foreach($product as $value) {
            $product_id = trim(substr($value[0],7));

            $sql = "select sum(A.quantity)AS sort_quantity,B.inventory_name from oc_x_order_check_details A  LEFT JOIN  oc_order_distr B ON  A.order_id = B.order_id WHERE A.order_id = '". $order_id ."' and A.product_id = '". $product_id."'";

            $query = $db->query($sql);
            $sort_quantity = $query->row;
            $results = $sort_quantity['sort_quantity']+$value[3] ;

            if($results <= $value[2] and $value[3]>0 ){
                $sql = "INSERT INTO oc_x_order_check_details  (`order_id`,`product_id`,`product_name`,`quantity`,`add_user_id`,`inventory_name`,`date_added`) VALUES ('".$order_id."','".$product_id."','".$value[1]."','".$value[3]."','".$user_id."','".$sort_quantity['inventory_name']."',current_timestamp())";
                $query = $db->query($sql);
                $result = $query->rows;
                $array[] = array($value[0],$value[1]);

            }else{
                $array1[]=array($value[0],$value[1]);

            }

        }

        $array2 = array($array,$array1);
        return $array2;
    }

    public function getSearchCheck( array $data){
        global $db;
        $date_start = $data['data']['date_start'];
        $date_end = $data['data']['date_end'];
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';

        if($order_id){
            $sql = "select cl.order_id ,cd.product_id ,cd.product_name,SUM(cd.quantity) as quantity,i.inv_comment,d.inventory_name,cl.reasons,clr.reason_name,cl.add_user
                 from oc_x_order_check_location cl
                 LEFT JOIN  oc_x_order_check_details cd ON  cd.order_id = cl.order_id
                 LEFT JOIN  oc_order_distr d ON  cl.order_id = d.order_id
                 LEFT JOIN  oc_order_inv i ON  cd.order_id = i.order_id
                 LEFT JOIN oc_x_order_check_location_reason clr ON  cl.reasons = clr.check_location_reason_id
                 WHERE  cl.order_id = '". $order_id."'
                 group by cl.order_id ,cd.product_id";

        }else{

            $sql = "select cl.order_id ,cd.product_id ,cd.product_name,SUM(cd.quantity) as quantity,i.inv_comment,d.inventory_name,cl.reasons,clr.reason_name,cl.add_user
                 from oc_x_order_check_location cl
                 LEFT JOIN  oc_x_order_check_details cd ON  cd.order_id = cl.order_id
                 LEFT JOIN  oc_order_distr d ON  cl.order_id = d.order_id
                 LEFT JOIN  oc_order_inv i ON  cd.order_id = i.order_id
                 LEFT JOIN oc_x_order_check_location_reason clr ON  cl.reasons = clr.check_location_reason_id
                 WHERE  cl.date_added BETWEEN '".$date_start."' and '".$date_end."'
                 group by cl.order_id ,cd.product_id";
        }

        $query = $db->query($sql);
        $results = $query->rows;

        return $results;
    }

    public function cancel_searchProduct(array $data){
        global $db;

        $order_id = $data['data']['order_id'];
        $product_id = $data['data']['product_id'];
        $sql = "delete from oc_x_order_check_details WHERE order_id = '".$order_id."' and product_id = '".$product_id."' ";
        $query = $db->query($sql);
        $result = $query->row;
        $result['status'] = 1;
        return $result;

    }

    public function getDrivers(){
        global  $db;
        $sql = "select A.logistic_driver_title ,A.status ,A.logistic_driver_id from oc_x_logistic_driver A ORDER BY  A.status DESC ";
        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }

    public function getDeliverStatus(){
        global  $db;
        $sql = "select A.order_deliver_status_id ,A.name from oc_order_deliver_status A  ";
        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }

    public function getOrderByDriver(array $data){
        global  $db;
        $logistic_allot_id = $data['data']['logistic_allot_id'] ? $data['data']['logistic_allot_id'] : '';

        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';

        $date1=date("Y-m-d",strtotime("-1 day"));
        if($warehouse_id ==12 || $warehouse_id ==14  ){

            $sql =  "select  logistic_driver_id  from  oc_x_logistic_allot  WHERE logistic_allot_id='". $logistic_allot_id."'";

            $query = $db->query($sql);
            $result = $query->row;

            $sql1 = " select sum(total) total  from  oc_x_logistic_allot la  LEFT JOIN  oc_x_logistic_allot_order lao  ON  la.logistic_allot_id = lao.logistic_allot_id LEFT JOIN   oc_order o ON  lao.order_id = o.order_id WHERE  o.order_payment_status_id != 2 and o.payment_code != 'CYCLE' and la.logistic_driver_id = '".$result['logistic_driver_id'] ."'   and o.order_status_id != 3  and DATE(la.date_added) between  '2017-12-01' and '".$date1."' and o.warehouse_id = '". $warehouse_id ."' and  o.order_deliver_status_id in (1,2,3)  group by la.logistic_driver_id  ";

            $query1 = $db->query($sql1);
            $result1 = $query1->row;


            if(  $result1['total'] &&  $result1['total'] > 3000){
                return 1  ;
            }else{
                $sql = "SELECT
                C.inv_comment,
                A.order_id,
                F.name,
                sum(B.quantity) AS  quantity,
                E.logistic_driver_title,
                A.order_deliver_status_id,
                A.order_status_id,
                os.name AS order_name,
                C.frame_vg_list,
                C.frame_count
            FROM
                oc_x_logistic_allot_order D
            LEFT JOIN oc_order A ON A.order_id = D.order_id
            LEFT JOIN oc_order_deliver_status F ON  A.order_deliver_status_id = F.order_deliver_status_id
            LEFT JOIN  oc_order_status os ON  A.order_status_id = os.order_status_id
            LEFT JOIN oc_order_product B ON A.order_id = B.order_id
            LEFT JOIN oc_order_inv C ON A.order_id = C.order_id
            LEFT JOIN oc_x_logistic_allot E ON D.logistic_allot_id = E.logistic_allot_id WHERE D.logistic_allot_id='". $logistic_allot_id."'
 ";

                $sql .=" GROUP BY A.order_id";
                $sql .=" ORDER BY E.logistic_driver_id ,A.order_deliver_status_id, A.order_id desc";

                $query = $db->query($sql);
                $result = $query->rows;
                return $result;
            }

        }else{
            $sql = "SELECT
                C.inv_comment,
                A.order_id,
                F.name,
                sum(B.quantity) AS  quantity,
                E.logistic_driver_title,
                A.order_deliver_status_id,
                A.order_status_id,
                os.name AS order_name,
                C.frame_vg_list,
                C.frame_count
            FROM
                oc_x_logistic_allot_order D
            LEFT JOIN oc_order A ON A.order_id = D.order_id
            LEFT JOIN oc_order_deliver_status F ON  A.order_deliver_status_id = F.order_deliver_status_id
            LEFT JOIN  oc_order_status os ON  A.order_status_id = os.order_status_id
            LEFT JOIN oc_order_product B ON A.order_id = B.order_id
            LEFT JOIN oc_order_inv C ON A.order_id = C.order_id
            LEFT JOIN oc_x_logistic_allot E ON D.logistic_allot_id = E.logistic_allot_id WHERE D.logistic_allot_id='". $logistic_allot_id."'
 ";

            $sql .=" GROUP BY A.order_id";
            $sql .=" ORDER BY E.logistic_driver_id ,A.order_deliver_status_id, A.order_id desc";

            $query = $db->query($sql);
            $result = $query->rows;
            return $result;
        }




    }

    public function confirm_orderStatus(array $data){
        global  $db;
        $order_id = $data['data']['order_id'] ;
        $user_id = $data['data']['user_id'];
        $logistic_allot_id = $data['data']['logistic_allot_id'];
        $comment = '订单配送状态改为出库确认';
        $sql = "SELECT A.order_id ,A.order_status_id,A.order_payment_status_id ,oi.frame_vg_list , A.customer_id  FROM oc_x_logistic_allot_order B   LEFT JOIN  oc_order A ON A.order_id = B.order_id LEFT JOIN  oc_order_inv oi ON  A.order_id = oi.order_id WHERE B.order_id = '". $order_id ."' AND B.logistic_allot_id = '". $logistic_allot_id ."' and A.order_deliver_status_id in(1,11)";
        $query = $db->query($sql);
        $result = $query->row;
        if($result['frame_vg_list']){

            $sql = "update   oc_x_container  set  instore =  0  WHERE  container_id in ( ".$result['frame_vg_list'].")";
            $query = $db->query($sql);
            $array=explode(',',$result['frame_vg_list']);

            if($query){
                $sql = " insert  into  oc_x_container_fast_move  (`container_id` , `customer_id` ,`order_id` , `move_type` ,`date_added` , `add_w_user_id`)  VALUES ";

                $m = 0;

                foreach ($array as $product) {
                    $sql .= " (".$product." , ".$result['customer_id']."  , ".$result['order_id']." , " . 1 ."   ,NOW() , " . $user_id ."  )   ";
                    if (++$m < sizeof($array)) {
                        $sql .= ', ';
                    } else {
                        $sql .= ';';
                    }
                }

            }
            $query = $db->query($sql);
        }




        if($result){
            $sql = "insert into oc_order_history (`order_id`,`comment`,`date_added`,`order_status_id`,`order_payment_status_id`,`order_deliver_status_id`,`modified_by` ) VALUES ('".$order_id ."','".$comment ."',current_timestamp() , '".$result['order_status_id'] ."','".$result['order_payment_status_id']."','8','".$user_id."')";
            $query = $db->query($sql);
            if($query){
                $sql ="update oc_order set order_deliver_status_id = 2, date_out=now() WHERE order_id= '". $order_id ."' and order_deliver_status_id in(1,11)";
                $query = $db->query($sql);
                return $result['status']=1;
            }
        }else{
            return $result['status'] = 2;
        }
    }

    public function submitDeliverStatus(array $data){
        global  $db;
        $check_value = $data['data']['check_value'] ;
        $user_id = $data['data']['user_id'];
        $logistic_id = $data['data']['logistic_id'];
        $comment = '订单配送状态改为出库确认';
        if($check_value){
            foreach($check_value as $value){
                $sql = "SELECT A.order_id ,A.order_status_id,A.order_payment_status_id ,oi.frame_vg_list , A.customer_id  FROM oc_x_logistic_allot_order B   LEFT JOIN  oc_order A ON A.order_id = B.order_id LEFT JOIN  oc_order_inv oi ON  A.order_id = oi.order_id WHERE B.order_id = '". $value ."'  and A.order_deliver_status_id in(1,11) AND B.logistic_allot_id = '". $logistic_id ."' and A.order_status_id = 6";

                $query = $db->query($sql);
                $result = $query->row;

                if($result['frame_vg_list']){

                    $sql = "update   oc_x_container  set  instore =  0  WHERE  container_id in ( ".$result['frame_vg_list'].")";
                    $query = $db->query($sql);
                    $array=explode(',',$result['frame_vg_list']);

                    if($query){
                        $sql = " insert  into  oc_x_container_fast_move  (`container_id` , `customer_id` ,`order_id` , `move_type` ,`date_added` , `add_w_user_id`)  VALUES ";

                        $m = 0;

                        foreach ($array as $product) {
                            $sql .= " (".$product." , ".$result['customer_id']."  , ".$result['order_id']." , " . 1 ."   ,NOW() , " . $user_id ."  )   ";
                            if (++$m < sizeof($array)) {
                                $sql .= ', ';
                            } else {
                                $sql .= ';';
                            }
                        }

                    }
                    $query = $db->query($sql);
                }


                if($result){
                    $sql = "insert into oc_order_history (`order_id`,`comment`,`date_added`,`order_status_id`,`order_payment_status_id`,`order_deliver_status_id`,`modified_by` ) VALUES ('".$value ."','".$comment ."',current_timestamp() , '".$result['order_status_id'] ."','".$result['order_payment_status_id']."','2','".$user_id."')";
                    $query = $db->query($sql);
                    if($query){
                        $sql ="update oc_order set order_deliver_status_id = 2, date_out=now() WHERE order_id= '". $value ."' and order_deliver_status_id in(1,11)";
                        $query = $db->query($sql);
                    }
                }
            }
            return $result['status'] =2;
        }else{
            return $result['status'] =3;
        }
    }

    public function getWarehouseId( ){
        global $db;


        $sql = "SELECT  warehouse_id ,title,station_id  FROM  oc_x_warehouse ";

        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }

    public function getWarehouseProductId(array  $data){
        global  $db;
        $sql = "select  product_id
              from  oc_product_inventory pi
              LEFT JOIN oc_product p ON  pi.product_id = p.product_id
              LEFT JOIN oc_x_warehouse w ON  w.warehouse_id = pi.warehouse_id";
    }

    //获取整单退货订单信息
    public  function getIssueOrderInfo( array $data){
        global $db;


        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id'] : false;

        $sql = "select o.order_id, sum(ios.quantity) as quantity,ios.product_id,p.sku,p.name , p.repack
              from  oc_order o
              LEFT JOIN oc_x_inventory_order_sorting ios  ON o.order_id = ios.order_id
              LEFT JOIN oc_product p  ON  ios.product_id = p.product_id
              WHERE  o.order_id = '".$order_id."' and o.order_deliver_status_id = '2'
              GROUP BY ios.order_id ,ios.product_id
              order by p.repack
               ";
        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }

    public function getIssueReason(){
        global $db;
        $sql = "select * from oc_issue_reason ";

        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }

    public function getLogisticId(array $data){
        global $db;
        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id'] : false;

        $sql = " select lao.logistic_allot_id  from  oc_x_logistic_allot_order lao LEFT JOIN oc_x_logistic_allot la ON  lao.logistic_allot_id = la.logistic_allot_id WHERE lao.order_id  = '".$order_id."'";
        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }

    public  function redistr( array $data){
        global $db;
        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id'] : false;
        $issue_reason = isset($data['data']['issue_reason']) ? $data['data']['issue_reason'] : false;
        $position_num = isset($data['data']['position_num']) ? $data['data']['position_num'] : false;
        $inventory_user = isset($data['data']['inventory_user']) ? $data['data']['inventory_user'] : false;
        $logistic_allot_id = isset($data['data']['logistic_allot_id']) ? $data['data']['logistic_allot_id'] : false;
        //获取司机信息
        $sql = " select la.logistic_driver_id ,la.logistic_line_id ,la.deliver_date
                from  oc_x_logistic_allot_order lao
                LEFT JOIN oc_x_logistic_allot la ON  lao.logistic_allot_id = la.logistic_allot_id
                LEFT JOIN  oc_order  o ON lao.order_id = o.order_id
                where lao.order_id = '". $order_id ."' and lao.logistic_allot_id = '".$logistic_allot_id."'
                and o.order_deliver_status_id = 2
                ";

        $query = $db->query($sql);
        $results = $query->row;

        $sql1 = " select order_id from oc_order_deliver_issue WHERE order_id  = '".$order_id."' and logistic_allot_id = '".$logistic_allot_id."'";
        $query1 = $db->query($sql1);

        if($query1->num_rows){
            $status = 1;
        }else{

            if($results){
                //插入整单退的表中
                $sql = "insert into  oc_order_deliver_issue
                  (`order_id`,`logistic_allot_id`,`driver_id`,`line_id`,`deliver_date`,`date_added`,`issue_reason`,`added_by`,`position_num` )
                  VALUES ('".$order_id ."' ,  '".$logistic_allot_id ."' ,'".$results['logistic_driver_id'] ."','".$results['logistic_line_id']."','".$results['deliver_date']."',NOW(),'".$issue_reason."','".$inventory_user."','".$position_num."')";
                $query = $db->query($sql);
                if($query){
                    $sql1 = "select * from oc_order  where order_id = '".$order_id ."'";

                    $query = $db->query($sql1);
                    $results = $query->row;
                    //修改订单配送状态
                    $sql = " update oc_order  set order_deliver_status_id = '10' where order_id = '".$order_id ."' ";
                    $query = $db->query($sql);
                    //给订单重新键一个货位号
                    $sql = " update oc_order_inv set inv_comment = '".$position_num."' where order_id = '".$order_id ."'";
                    $query = $db->query($sql);
                    if($query){
                        //插入历史订单表中
                        $comment = "整单退货";
                        $sql = "insert into  oc_order_history
                        (`order_id`,`comment`,`date_added`,`order_status_id`,`order_payment_status_id`,`order_deliver_status_id`)
                        VALUES ('".$order_id."','".$comment."',NOW(), '".$results['order_status_id']."','".$results['order_payment_status_id']."','10')";
                        $query = $db->query($sql);
                        $status = 2;
                    }

                    if($query){

                        $sql1 = "select * from oc_order  where order_id = '".$order_id ."'";

                        $query = $db->query($sql1);
                        $results = $query->row;


                        //退货
                        $sql = " update oc_order  set order_deliver_status_id = '7' where order_id = '".$order_id ."' ";
                        $query = $db->query($sql);

                        //仓库取消
                        $sql = " update oc_order_deliver_issue  set status = '2' where order_id = '".$order_id ."' ";
                        $query = $db->query($sql);

                        if($query){
                            //插入历史订单表中
                            $comment = "仓库取消订单";
                            $sql = "insert into  oc_order_history
                        (`order_id`,`comment`,`date_added`,`order_status_id`,`order_payment_status_id`,`order_deliver_status_id`)
                        VALUES ('".$order_id."','".$comment."',NOW(), '".$results['order_status_id']."','".$results['order_payment_status_id']."','7')";
                            $query = $db->query($sql);
                            $status = 2;
                        }



                    }

                }
            }else{
                $status = 3;
            }

        }


        return $status;
    }

    public function reDistrList(){
        global $db;
        $sql = " select odi.order_id , ld.logistic_driver_title,ll.logistic_line_title,ir.name,odi.deliver_date,odi.date_added,odi.logistic_allot_id
                from  oc_order_deliver_issue odi
                LEFT JOIN  oc_x_logistic_driver ld ON  odi.driver_id = ld.logistic_driver_id
                LEFT JOIN  oc_x_logistic_line ll ON  odi.line_id = ll.logistic_line_id
                LEFT JOIN  oc_issue_reason ir on odi.issue_reason = ir.issue_reason_id";
        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }

    public function getOrderInfo(array  $data){
        global $db;
        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id'] : false;
        $sql = "select oi.box_count ,oi.frame_count
              from oc_order o
              LEFT JOIN oc_order_inv oi ON o.order_id = oi.order_id
              WHERE o.order_deliver_status_id = 2  and o.order_id = '".$order_id ."'";
        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }



    //出库单
    public function getWarehouseRequisition( array $data){
        global $db;

        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;

        $sql = " select wr.relevant_id ,wr.from_warehouse ,wr.to_warehouse,wr.date_added,u.username,wr.deliver_date,wrs.name ,w.title,wr.relevant_status_id,wr.out_type,wr.comment
              from  oc_x_warehouse_requisition wr
              LEFT JOIN oc_x_warehouse w  ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 and wr.relevant_status_id != 3  and DATE (wr.date_added) between  date_sub(current_date(), interval 3 day)  and  current_date()  ";



        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }

    public function searchRequisition( array $data){
        global  $db;
        $date_start = isset($data['data']['date_start']) ? $data['data']['date_start'] : false;
        $date_end = isset($data['data']['date_end']) ? $data['data']['date_end'] : false;
        $filter_out_type = isset($data['data']['filter_out_type']) ? $data['data']['filter_out_type'] : false;
        $filter_out_type_id = isset($data['data']['filter_out_type_id']) ? $data['data']['filter_out_type_id'] : false;

        $sql = " select wr.relevant_id ,wr.from_warehouse ,wr.to_warehouse,wr.date_added,u.username,wr.deliver_date,wrs.name ,w.title,wr.relevant_status_id,wr.out_type,wr.comment
              from  oc_x_warehouse_requisition wr
              LEFT JOIN oc_x_warehouse w  ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 and wr.relevant_status_id !=3 and 
              DATE(wr.date_added) BETWEEN '".$date_start."' and '".$date_end ."'
               ";


        if($filter_out_type_id){
            $sql .= " and wr.out_type = '".$filter_out_type ."'";
        }

        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }


    public function viewItem(array $data){
        global  $db;

        $relevant_id = isset($data['data']['relevant_id']) ? $data['data']['relevant_id'] : false;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $sql = " select wri.relevant_id ,wri.product_id ,p.name ,pi.inventory ,wri.num ,wri.scale_num,ps.stock_area ,w.title,wr.out_type
              from oc_x_warehouse_requisition_item wri
              LEFT JOIN  oc_product_to_warehouse  ps ON wri.product_id = ps.product_id  and ps.warehouse_id ='".$warehouse_id."'
              LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wri.relevant_id
              LEFT JOIN  oc_x_warehouse w ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN  oc_product p ON wri.product_id = p.product_id
              LEFT JOIN  oc_product_inventory pi ON wri.product_id = pi.product_id and wr.from_warehouse = pi.warehouse_id
              WHERE  wri.relevant_id = '".$relevant_id."' ";

        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }

    public function getinventoryname(array $data){
        global  $db ;
        $warehouse_id  = $data['data']['warehouse_id'];
        $sql = "select username from oc_w_user  WHERE  warehouse_id = '".$warehouse_id."'and status = 1 and user_group_id =15 and repack =0 ";

        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }
    public function getinventorynamerepack(array $data){
        global  $db ;
        $warehouse_id  = $data['data']['warehouse_id'];
        $repack  = $data['data']['repack'];
        $sql = "select username from oc_w_user  WHERE  warehouse_id = '".$warehouse_id."'and status = 1 and user_group_id =15 and repack ='".$repack."'";

        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }


    public function startShipment(array  $data){
        global  $db;

        $invalidProducts = '';
        $invalidProducts2 = '';

        $relevant_id = isset($data['data']['relevant_id']) ? $data['data']['relevant_id'] : false;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;

//        $sql = "select  sm.relevant_id ,wr.relevant_status_id from oc_x_stock_move sm   left join  oc_x_warehouse_requisition wr on sm.relevant_id = wr.relevant_id WHERE  sm.relevant_id = '".$relevant_id ."' and sm.warehouse_id = '".$warehouse_id."'";
//
//        $query = $db->query($sql);
//        $results = $query->row;
//
//        if ($query->num_rows) {
//                if($results['relevant_status_id'] == 4) {
//                    $inventory_type_id = 21;
//                }else if($results['relevant_status_id'] == 6){
//                    $inventory_type_id = 22;
//                }
//            //查询是否写入oc_stock_move表
//            $sql = " select
//                  from oc_x_stock_move sm
//                  LEFT  JOIN   oc_x_stock_move_item smi ON  sm.inventory_move_id = smi.inventory_move_id
//                  WHERE  sm.relevent_id = '".$relevant_id ."' and '". $warehouse_id ."' ";
//
//
//
//
//
//        }else{

        //获取该出库单的状态以及所有商品ID
        $sql = "select  wr.relevant_id ,wr.relevant_status_id ,wri.product_id from oc_x_warehouse_requisition wr  LEFT JOIN oc_x_warehouse_requisition_item wri ON  wri .relevant_id = wr.relevant_id  WHERE  wr.relevant_id = '".$relevant_id."'";
        $query = $db->query($sql);
        $results = $query->rows;


        $product_id='';
        $productids = '';
        foreach($results  as $result){
            $product_id .= ','.($result['product_id']); //字符串
            $productids[] = $result['product_id']; //数组
        }
        $product_ids = ltrim($product_id, ",");


        foreach ($productids as $productid){
            $sql = "select   wrt.product_id
                from oc_x_warehouse_requisition_temporary wrt WHERE wrt.relevant_id = '".$relevant_id ."' and
                wrt.relevant_status_id = '".$results[0]['relevant_status_id']."' and
                wrt.product_id = '".$productid ."'
                group by  wrt.product_id ";


            $invalid =$db->query($sql)->row;
            if(!$invalid){
                $invalidProducts .=','.$productid;  //不存在临时表中

            }else{
                $invalidProducts2  .=','.$invalid['product_id']; //存在临时表中
            }
        }

        $invalidProducts = ltrim($invalidProducts, ",");
        $invalidProducts2= ltrim($invalidProducts2, ",");



        if($invalidProducts){
            //没插入临时表时的取值
            $sql2 = "select wri.product_id ,wri.relevant_id ,p.name ,pi.inventory ,wri.num ,wri.scale_num,ps.stock_area ,w.title,wr.out_type,wri.comment
            from oc_x_warehouse_requisition_item wri
            LEFT JOIN  oc_product_to_warehouse  ps ON wri.product_id = ps.product_id and ps.warehouse_id = '".$warehouse_id."'
            LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wri.relevant_id
            LEFT JOIN  oc_x_warehouse w ON  wr.to_warehouse = w.warehouse_id
            LEFT JOIN  oc_product p ON wri.product_id = p.product_id
            LEFT JOIN  oc_product_inventory pi ON wri.product_id = pi.product_id and wr.from_warehouse = pi.warehouse_id
            WHERE  wri.relevant_id = '".$relevant_id."'and  wri.product_id in (".$invalidProducts .")
             ";


            $query2 = $db->query($sql2);
            $return['product2'] = $query2->rows;
        }

        if($invalidProducts2){
            //查询插入临时表的信息
            $sql1 = " select   wrt.product_id ,p.name, ps.stock_area ,wri.num  ,wrt.quantity,wr.out_type,wri.comment,wri.scale_num
                from oc_x_warehouse_requisition_temporary wrt
                LEFT JOIN oc_x_warehouse_requisition_item wri ON  wrt.relevant_id = wri.relevant_id and wrt.product_id = wri.product_id
                LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wri.relevant_id
                LEFT JOIN oc_product p ON  p.product_id = wrt.product_id
                LEFT JOIN  oc_product_to_warehouse ps ON  ps.product_id = wrt.product_id and ps.warehouse_id = '".$warehouse_id."'
                WHERE wrt.relevant_id = '".$relevant_id ."' and
                wrt.relevant_status_id = '".$results[0]['relevant_status_id']."' and
                wrt.product_id in (".$invalidProducts2 .")
                group by  wrt.product_id";

            $query1 = $db->query($sql1);
            $return['product1'] = $query1->rows;
        }

        return $return;

    }



    public function getRelevantProductID(array  $data){
        global  $db;
        $relevant_id = isset($data['data']['relevant_id']) ? $data['data']['relevant_id'] : false;
        $sku = isset($data['data']['sku']) ? $data['data']['sku'] : false;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        if (!$sku) {
            return false;
        }
        if(is_numeric($sku) && strlen($sku) > 6){
            $sql = " select wri.product_id,psb.sku_barcode,p.name,ps.stock_area,wri.num
                  from oc_x_warehouse_requisition_item  wri
                  LEFT JOIN oc_product_sku_barcode psb  ON  wri.product_id = psb.product_id
                  LEFT JOIN oc_product p ON  wri.product_id = p.product_id
                  LEFT JOIN oc_product_to_warehouse ps ON  wri.product_id = ps.product_id and ps.warehouse_id = '".$warehouse_id."'
                  WHERE  wri.relevant_id = '".$relevant_id ."' and psb.sku_barcode = '".$sku."'";

        }elseif(is_numeric($sku) && strlen($sku) <= 6){
            $sql = " select wri.product_id,p.name,ps.stock_area,wri.num
                  from oc_x_warehouse_requisition_item  wri
                  LEFT JOIN oc_product p ON  wri.product_id = p.product_id
                  LEFT JOIN oc_product_to_warehouse ps ON  wri.product_id = ps.product_id and ps.warehouse_id = '".$warehouse_id."'
                  WHERE  wri.relevant_id = '".$relevant_id ."' and wri.product_id = '".$sku."' ";
        }

        $query = $db->query($sql);
        if ($query->num_rows) {
            $return['status'] = 2;
            $return['product'] = $query->rows;
            return $return;

        }
        else{
            $return['status'] = 1;
            return $return;
        }
    }

    public function submitProduct( array $data){
        global  $db;
        $relevant_id = isset($data['data']['relevant_id']) ? $data['data']['relevant_id'] : false;
        $postData = isset($data['data']['postData']) ? $data['data']['postData'] : false;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $product_id = explode('/',$postData[0]);

        //获取价格以及出库单状态
        $sql = " select wr.relevant_status_id ,wri.price
                from  oc_x_warehouse_requisition wr
                LEFT JOIN  oc_x_warehouse_requisition_item wri ON  wr.relevant_id = wri.relevant_id
                WHERE wri.relevant_id = '". $relevant_id ."' and wri.product_id = '". $product_id[0]."' ";

        $query = $db->query($sql);
        $result = $query->row;

        //查找是否插入了临时表
        $sql1 = "select wrt.relevant_id
              from oc_x_warehouse_requisition_temporary wrt  WHERE  wrt.relevant_id = '".$relevant_id."' and wrt.product_id = '". $product_id[0]."' and wrt.relevant_status_id = '".$result['relevant_status_id']."'  and wrt.warehouse_id = '". $warehouse_id ."'";
        $query1 = $db->query($sql1);

        //是否插入中间表执行不同的sql
        if ($query1->num_rows) {
            //更新中间表的数据
            $sql = "update oc_x_warehouse_requisition_temporary set quantity = '".$postData[3] ."' where  relevant_id = '".$relevant_id."' and product_id = '". $product_id[0]."' and relevant_status_id = '".$result['relevant_status_id']."' and warehouse_id = '".$warehouse_id ."'";

            $query = $db->query($sql);
            return $result['status'] = 1;
        }else{
            //插入中间表
            $sql = " insert into oc_x_warehouse_requisition_temporary
                ( `relevant_id`,`relevant_status_id`,`product_id`,`price`,`quantity`,`warehouse_id`,`date_added`)
                VALUES ( '".$relevant_id ."','".$result['relevant_status_id']."','".$product_id[0]."','".$result['price']."', '". $postData[3]."','".$warehouse_id."',NOW())";

            $query = $db->query($sql);

            return $result['status'] = 2;
        }

    }

    public function submitProducts(array $data){
        global  $db;
        $relevant_id = isset($data['data']['relevant_id']) ? $data['data']['relevant_id'] : false;
        $postData = isset($data['data']['postData']) ? $data['data']['postData'] : false;
        $warehouse_user = isset($data['data']['warehouse_user']) ? $data['data']['warehouse_user'] : false;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;

        $sql = "select   wr.relevant_status_id,wr.out_type
              from  oc_x_warehouse_requisition wr  WHERE wr.relevant_id = '".$relevant_id."'";

        $query = $db->query($sql);
        $result = $query->row;



        if($result['out_type'] == '仓内调拨单'){


            if($result['relevant_status_id'] == 2){
                $inventory_type_id = 22;
                $inventory_type_id1 = 23;
                $sql = "insert  into oc_x_stock_move
              (`relevant_id`,`station_id`,`timestamp`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id` ,`memo` )
              VALUES ('".$relevant_id ."' ,'2',UNIX_TIMESTAMP(NOW()), '".$inventory_type_id ."' ,NOW(),'".$warehouse_user ."' ,'".$warehouse_id ."','仓内调拨单')";


                $query = $db->query($sql);

                $inventory_move_id = $db->getLastId();

                $sql1 = "insert  into oc_x_stock_move
              (`relevant_id`,`station_id`,`timestamp`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id` ,`memo` )
              VALUES ('".$relevant_id ."' ,'2',UNIX_TIMESTAMP(NOW()), '".$inventory_type_id1 ."' ,NOW(),'".$warehouse_user ."' ,'".$warehouse_id ."','仓内调拨单')";


                $query1 = $db->query($sql1);

                $inventory_move_id1 = $db->getLastId();

                //A->B
                if($inventory_move_id1){
                    foreach ($postData as $value){

                        $arr=explode(':',$value[5]);
                        $product_id =$arr[0];
                        $quantity = $value[6];
                        $sql = "insert into oc_x_stock_move_item (`inventory_move_id`,`station_id`,`product_id`,`quantity`)
                        VALUES ('".$inventory_move_id1."','2','".$product_id."' , '". $quantity ."')";
                        $query = $db->query($sql);
                    }
                }

                //A
                if($inventory_move_id){
                    foreach ($postData as $value){
                        $product_id = substr($value[0],9);
                        $quantity = $value[4]-$value[3];
                        $sql = "insert into oc_x_stock_move_item (`inventory_move_id`,`station_id`,`product_id`,`quantity`)
                        VALUES ('".$inventory_move_id."','2','".$product_id."' , '". $quantity ."')";
                        $query = $db->query($sql);
                    }

                    $sql = " update oc_x_warehouse_requisition  set  relevant_status_id = 4 WHERE relevant_id = '".$relevant_id ."'";
                    $query = $db->query($sql);
                }

                $sql = "INSERT INTO oc_x_inventory_move (`relevant_id`,`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `add_user_name`, `memo`,`warehouse_id`)
                    VALUES('".$relevant_id."','2', current_date(), unix_timestamp(), '0', '".$inventory_type_id."', now(), '".$warehouse_user."',  '仓内调拨单','".$warehouse_id."')";

                $query = $db->query($sql);

                $insert_id = $db->getLastId();

                $sql1 = "INSERT INTO oc_x_inventory_move (`relevant_id`,`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `add_user_name`, `memo`,`warehouse_id`)
                    VALUES('".$relevant_id."', '2', current_date(), unix_timestamp(), '0', '".$inventory_type_id1."', now(), '".$warehouse_user."',  '仓内调拨单','".$warehouse_id."')";

                $query = $db->query($sql1);

                $insert_id1 = $db->getLastId();




                //A
                $m = 0 ;
                if($insert_id){
                    $sql = "INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`,`warehouse_id`) VALUES" ;

                    foreach($postData as $value){
                        $product_id = substr($value[0],9);
                        $quantity = $value[4]-$value[3];

                        $sql .= "('".$insert_id."','2','".$product_id."','".$quantity ."','". $warehouse_id ."')";

                        if (++$m < sizeof($postData)) {
                            $sql .= ', ';
                        } else {
                            $sql .= ';';
                        }
                    }
                    $query = $db->query($sql);
                }

                //A->B
                $n = 0 ;
                if($insert_id){
                    $sql = "INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`,`warehouse_id`) VALUES" ;

                    foreach($postData as $value){
                        $arr=explode(':',$value[5]);
                        $product_id =$arr[0];
                        $quantity = $value[6];

                        $sql .= "('".$insert_id1."','2','".$product_id."','".$quantity ."','". $warehouse_id ."')";

                        if (++$n < sizeof($postData)) {
                            $sql .= ', ';
                        } else {
                            $sql .= ';';
                        }
                    }
                    $query = $db->query($sql);
                }





            }










        }else if($result['out_type'] == '仓间调拨单'){
            if($result['relevant_status_id'] == 2){
                $inventory_type_id = 22;

                $sql = "insert  into oc_x_stock_move
              (`relevant_id`,`station_id`,`timestamp`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id` ,`memo` )
              VALUES ('".$relevant_id ."' ,'2',UNIX_TIMESTAMP(NOW()), '".$inventory_type_id ."' ,NOW(),'".$warehouse_user ."' ,'".$warehouse_id ."','仓库调拨出库')";


                $query = $db->query($sql);

                $inventory_move_id = $db->getLastId();

                if($inventory_move_id){
                    foreach ($postData as $value){
                        $product_id = substr($value[0],9);
                        $quantity = $value[4]-$value[3];
                        $sql = "insert into oc_x_stock_move_item (`inventory_move_id`,`station_id`,`product_id`,`quantity`)
                        VALUES ('".$inventory_move_id."','2','".$product_id."' , '". $quantity ."')";
                        $query = $db->query($sql);
                    }
                    $sql = " update oc_x_warehouse_requisition  set  relevant_status_id = 4 WHERE relevant_id = '".$relevant_id ."'";
                    $query = $db->query($sql);
                }

                $sql = "INSERT INTO oc_x_inventory_move (`relevant_id`,`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `add_user_name`, `memo`,`warehouse_id`)
                    VALUES('".$relevant_id ."','2', current_date(), unix_timestamp(), '0', '".$inventory_type_id."', now(), '".$warehouse_user."',  '仓库调拨出库','".$warehouse_id."')";

                $query = $db->query($sql);

                $insert_id = $db->getLastId();


                $m = 0 ;
                if($insert_id){
                    $sql = "INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`,`warehouse_id`) VALUES" ;

                    foreach($postData as $value){
                        $product_id = substr($value[0],9);
                        $quantity = $value[4]-$value[3];

                        $sql .= "('".$insert_id."','2','".$product_id."','".$quantity ."','". $warehouse_id ."')";

                        if (++$m < sizeof($postData)) {
                            $sql .= ', ';
                        } else {
                            $sql .= ';';
                        }
                    }
                    $query = $db->query($sql);
                }


            }else if($result['relevant_status_id'] == 4){
                $inventory_type_id = 23;

                $sql = "insert  into oc_x_stock_move
              (`relevant_id`,`timestamp`,`station_id`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id`,`memo`  )
              VALUES ('".$relevant_id ."' ,UNIX_TIMESTAMP(NOW()),'2', '".$inventory_type_id ."' ,NOW(),'".$warehouse_user ."' ,'".$warehouse_id ."','仓库调拨入库')";

                $query = $db->query($sql);

                $inventory_move_id = $db->getLastId();

                if($inventory_move_id){
                    foreach ($postData as $value){
                        $product_id = substr($value[0],9);
                        $quantity = $value[3]-$value[4];
                        $sql = "insert into oc_x_stock_move_item (`inventory_move_id`,`station_id`,`product_id`,`quantity`)
                        VALUES ('".$inventory_move_id."','2','".$product_id."' , '". $quantity ."')";
                        $query = $db->query($sql);
                    }

                    $sql = " update oc_x_warehouse_requisition  set  relevant_status_id = 5 WHERE relevant_id = '".$relevant_id ."'";
                    $query = $db->query($sql);

                    $sql = "INSERT INTO oc_x_inventory_move (`relevant_id`,`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `add_user_name`, `memo`,`warehouse_id`)
                    VALUES('".$relevant_id ."','2', current_date(), unix_timestamp(), '0', '".$inventory_type_id."', now(), '".$warehouse_user."',  '仓库调拨入库','".$warehouse_id."')";

                    $query = $db->query($sql);

                    $insert_id = $db->getLastId();
                    $m = 0 ;
                    if($insert_id){
                        $sql = "INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`,`warehouse_id`) VALUES" ;

                        foreach($postData as $value){
                            $product_id = substr($value[0],9);
                            $quantity = $value[3]-$value[4];

                            $sql .= "('".$insert_id."','2','".$product_id."','".$quantity ."','". $warehouse_id ."')";

                            if (++$m < sizeof($postData)) {
                                $sql .= ', ';
                            } else {
                                $sql .= ';';
                            }
                        }
                        $query = $db->query($sql);
                    }


                    return $results['status'] =1 ;
                }
            }
        }




    }

    public function handleRedistr(array  $data){
        global  $db;
        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id'] : false;

        $sql = "select COUNT(order_id)AS num from oc_order_deliver_issue WHERE order_id = '".$order_id."'";


        $query = $db ->query($sql);
        $results = $query->row;
        if($results['num'] > 0){
            return $results['status'] = 1;
        }else{
            return $results['status'] = 2;
        }
    }
    public function showOrderDetail($data){
        global  $db;

        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id'] : false;

        $sql = "SELECT smi.product_id ,p.name, p.status, p.sku, p.box_size, p.inv_class_sort, p.model, smi.price , p.product_id
                FROM oc_order_deliver_issue odi
                LEFT JOIN oc_x_stock_move sm ON odi.order_id = sm.order_id
								LEFT JOIN oc_x_stock_move_item smi on  sm.inventory_move_id = smi.inventory_move_id
                LEFT JOIN oc_product AS p ON smi.product_id = p.product_id

                WHERE odi.order_id = '".$order_id."'
                group by smi.product_id    ";

        $query = $db ->query($sql);
        $results = $query->rows;

        return $results;
    }

    public function showDeliverConfirm(array  $data){
        global  $db;

        $order_id = isset($data['data']['order_id']) ? $data['data']['order_id'] : false;

        $sql = "SELECT rdp.return_id, pd.name, p.status, p.sku, p.box_size, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_return_deliver_product rdp LEFT JOIN oc_product AS p ON rdp.product_id = p.product_id LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id LEFT JOIN  oc_product_description AS pd ON p.product_id = pd.product_id WHERE rdp.order_id = '".$order_id."' and rdp.status = 2  ";

        $query = $db ->query($sql);
        $results = $query->rows;

        return $results;
    }




    public function warehouseConfirmReturnProduct(array $data)
    {

        global $db, $dbm;

        $return_id  = !empty($data['data']['return_id'])    ? (int)$data['data']['return_id']   : false;
        $station_id = !empty($data['station_id'])   ? (int)$data['station_id']  : 2;

        $order_id   = !empty($data['data']['order_id'])     ? (int)$data['data']['order_id']    : false;

        $user_id    = !empty($data['data']['inventory_user_id'])      ? (int)$data['data']['inventory_user_id']     : false;
        $warehouse_id    = !empty($data['data']['warehouse_id'])      ? (int)$data['data']['warehouse_id']     : false;
        $products   = !empty($data['data']['products'])     ? $data['data']['products']         : array();
        $productsBarcodeWithQtyRaw = explode(',',$products);

        foreach($productsBarcodeWithQtyRaw as $m){
            $n = explode(':',$m);
            $productsBarcodeWithQty[]= array(
                $n[0] => $n[1],
            );
        }

        $products = $productsBarcodeWithQty;

        if(!$return_id || !$station_id || !$order_id || !$user_id || empty($products)){

            return array('status' => 0, 'message' => "缺少关键参数，请刷新页面重新提交");
        }


        $dbm->begin();
        $bool = true;
        // 已退 driver_quantity 司机退货数量

        foreach($products as $key => $v){
            foreach( $v as $product_id => $qty) {
                $sql = "UPDATE oc_return_deliver_product
                        SET date_warehouse_confirm = NOW(), driver_return_quantity = {$qty}
                        WHERE order_id = {$order_id}
                        AND product_id = {$product_id}";
                $bool = $bool && $dbm->query($sql);
            }


        }

        // 确认人 确认时间 inventory_returned = 1
        $sql = "UPDATE oc_return SET
                    add_user = {$user_id},
                    date_added = NOW(),
                    date_modified = NOW(),
                    inventory_returned = 1
                    WHERE return_id = {$return_id}";
        $bool = $bool && $dbm->query($sql);

        if (!$bool) {
            $dbm->rollback();
            return array('status' => 0, 'message' => '仓库确认退货失败');
        } else {
            $dbm->commit();

            //退货记录完成，开始写入入库数据
            //退货入库操作写库存表，仅操作回库且需要退货入库的订单
            //if($return_action_id == 2 || $return_action_id == 4){
            $stockMoveData = array();
            $stockMoveData['api_method']        = 'inventoryReturn';
            $stockMoveData['timestamp']         = time();
            $stockMoveData['from_station_id']   = 0;
            $stockMoveData['to_station_id']     = $station_id;
            $stockMoveData['order_id']          = $order_id;
            $stockMoveData['purchase_order_id'] = 0;
            $stockMoveData['added_by']          = $user_id;
            $stockMoveData['memo']              = '司机退货,仓库确认入库';
            $stockMoveData['add_user_name']     = '';
            $stockMoveData['products']          = array();

            //获取退货的商品列表,需要station_id, product_id, price, quantity, box_quantity
            $sql = "SELECT '{$station_id}', `product_id`, `price` special_price, `quantity` qty, `box_quantity`
                    FROM oc_return_product
                    WHERE return_id = '{$return_id}'";
            $query = $db->query($sql);
            $stockMoveData['products'] = $query->rows;
            // product数量要不要更改??
            foreach($stockMoveData['products'] as $key => $value){
                if(!empty($products[$value['product_id']])){
                    $stockMoveData['products'][$key]['qty'] = $products[$value['product_id']];
                }
            }

            $this->addInventoryMoveOrder($stockMoveData, $station_id,$warehouse_id);

            return array('status' => 1, 'message' => '仓库确认退货成功');
        }
    }


    function addInventoryMoveOrder($data, $station_id,$warehouse_id) {
        global $db, $dbm, $log;

        //$log->write('INFO:['.__FUNCTION__.']'.': '.serialize($data)."\n\r");

        if (!is_array($data) || !sizeof($data) || !$station_id) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        if (!sizeof($data['products']) && $data['api_method'] != 'inventoryOrderIn') {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少商品信息' . "\n\r");
            return false;
        }

        if (!isset($data['timestamp']) || !$data['timestamp']) {
            return false;
        }

        if (!defined('INVENTORY_TYPE_OP')) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少库存计算关键配置数据[INVENTORY_TYPE_OP]' . "\n\r");
            return false;
        }

        //Check timestamp
        $sql = "select inventory_move_id from oc_x_stock_move where station_id = '" . $station_id . "' and timestamp = '" . $data['timestamp'] . "';";

        $query = $db->query($sql);
        if (sizeof($query->rows)) {
            return false;
        }


        if($data['api_method'] == 'inventoryOrderIn'){

            $sql = "select xsm.order_id,o.station_id from oc_x_stock_move as xsm left join oc_order as o on o.order_id = xsm.order_id where xsm.inventory_type_id = 12 and xsm.order_id = " . (isset($data['order_id']) ? (int) $data['order_id'] : 0);
            $query = $dbm->query($sql);
            $result_exists = $query->rows;

            if(!empty($result_exists)){
                if($result_exists[0]['station_id'] != 2){
                    return false;
                }
            }

        }





        //Get Inventory Type Opration From config
        $inventory_type_op = unserialize(INVENTORY_TYPE_OP); //array('api method'=>array(inventory_type_id, operation))
        $inventory_op = $inventory_type_op[$data['api_method']][1];
        $inventory_type = $inventory_type_op[$data['api_method']][0];

        if (!$inventory_type) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 未指定库存变动类型' . "\n\r");
            return false;
        }

        $data_insert = array();

        $data_insert['station_id'] = $station_id;
        $data_insert['timestamp'] = $data['timestamp'];
        $data_insert['from_station_id'] = isset($data['from_station_id']) ? (int) $data['from_station_id'] : 0;
        $data_insert['to_station_id'] = isset($data['to_station_id']) ? (int) $data['to_station_id'] : 0;
        $data_insert['order_id'] = isset($data['order_id']) ? (int) $data['order_id'] : 0;

        $data_insert['purchase_order_id'] = isset($data['purchase_order_id']) ? (int) $data['purchase_order_id'] : 0;

        $data_insert['inventory_type_id'] = isset($inventory_type) ? (int) $inventory_type : 0;
        $data_insert['date_added'] = date('Y-m-d H:i:s', time());
        $data_insert['added_by'] = isset($data['added_by']) ? (int) $data['added_by'] : 0;
        $data_insert['memo'] = isset($data['memo']) ? $db->escape($data['memo']) : '';
        $data_insert['add_user_name'] = isset($data['add_user_name']) ? $data['add_user_name'] : '';
        $data_insert['warehouse_id'] = $warehouse_id;
        $log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动类型：'.$data['api_method']);

        $dbm->begin();
        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Begin' . "\n\r");
        $bool = true;
        $sql = "INSERT INTO `oc_x_stock_move` SET ";
        foreach ($data_insert as $key => $val) {
            $sql .= '`' . $key . '`' . '="' . $val . '"';
            if (current($data_insert) === false) {
                $sql .= ';';
            } else {
                $sql .= ', ';
            }
            next($data_insert);
        }

        $bool = $bool && $dbm->query($sql);
        $inventory_move_id = $dbm->getLastId();

        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表' . "\n\r");

        if(!empty($data['products'])){
            $sql = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`,`warehouse_id`) VALUES ";
            $m = 0;
            foreach ($data['products'] as $product) {
                $sql .= "(" . $inventory_move_id . ", " . $station_id . ", '" . (isset($product['due_date']) ? $product['due_date'] : '0000-00-00') . "', '" . $product['product_id'] . "', '" . $product['special_price'] . "', '" . (isset($product['product_batch']) ? $product['product_batch'] : '') . "', " . $product['qty'] * $inventory_op . ", '".(isset($product['box_quantity']) ? $product['box_quantity'] : 1)."', " . (isset($product['product_weight']) ? $product['product_weight'] : 0) . "," . (isset($product['is_gift']) ? $product['is_gift'] : 0) . ", " . (isset($product['checked']) ? $product['checked'] : 0) . "," . (isset($product['status']) ? $product['status'] : 1) . "," . (isset($product['sku_id']) ? $product['sku_id'] : 0) . ",'". $warehouse_id ."')";
                if (++$m < sizeof($data['products'])) {
                    $sql .= ', ';
                } else {
                    $sql .= ';';
                }
            }

            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存明细表' . "\n\r");
            $bool = $bool && $dbm->query($sql);
        }
        //If the method is init, make every other records checked
        if ($data['api_method'] == 'inventoryInit') {
            $sql = "UPDATE oc_x_stock_move_item SET checked=1 WHERE station_id = '" . $station_id . "' AND checked = 0 AND inventory_move_id < " . $inventory_move_id;
            $bool = $bool && $dbm->query($sql);
        }

        //对于指定的库存变动类型（退货入库、商品报损、库存调整），同步调整前台可售库存。（采购入库已在其他地方处理）
        $inventory_type_auto_sync = unserialize(INVENTORY_TYPE_AUTO_SYNC);

        if(in_array($inventory_type, $inventory_type_auto_sync)){
            $sql = "INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`,`warehouse_id`)
                    VALUES('".$station_id."', current_date(), unix_timestamp(), '0', '".$inventory_type."', now(), '".$data_insert['added_by']."', '".$data_insert['add_user_name']."', '[API]".$data_insert['memo']."','".$warehouse_id."')";
            $bool = $bool && $dbm->query($sql);
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动操作SQL：'.$sql);
            $inventory_move_id = $dbm->getLastId();
            //$inventory_move_id = 999;
            $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`,`warehouse_id`) VALUES';

            $m = 0;
            foreach ($data['products'] as $product) {
                //处理散件退货商品 - 散件暂时不退货可售库存
                //TODO 散件售卖
                $returnInvqty = $product['qty'];
                if(isset($product['box_quantity']) && $product['box_quantity'] > 1){
                    $returnInvqty = 0;
                }
                $sql .= "('".$inventory_move_id."','".$station_id."','".$product['product_id']."','".$returnInvqty*$inventory_op ."','". $warehouse_id ."')";
                if (++$m < sizeof($data['products'])) {
                    $sql .= ', ';
                } else {
                    $sql .= ';';
                }

            }
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动操作SQL：'.$sql);
            $bool = $bool && $dbm->query($sql);
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 可售库存变动已添加');
        }

        if (!$bool) {
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Rollback' . "\n\r");
            $dbm->rollback();
            return false;
        } else {
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Commit' . "\n\r");
            $dbm->commit();
            return true;
        }

        //TODO Update inventory / Redis
    }


    //缺货提醒

    function  shortReminder( array  $data){
        global  $db;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $product_id = isset($data['data']['product_id']) ? $data['data']['product_id'] : false;
        $inventoryuser = isset($data['data']['inventoryuser']) ? $data['data']['inventoryuser'] : false;

        $sql = "select ptw.stock_area  from oc_product_to_warehouse ptw  WHERE  ptw.product_id = '". $product_id ."'and warehouse_id = '". $warehouse_id ."'";

        $query = $db ->query($sql);
        $results = $query->row;

        $sql = " insert into oc_x_product_reminder (`product_id`,`stock_area`,`date_added`,`add_user`,`warehouse_id`)  VALUE  ('".$product_id."','".$results['stock_area']."',NOW() , '".$inventoryuser."','".$warehouse_id."')";

        $query = $db ->query($sql);

        if($query){
            $sql = "select ptw.product_id ,p.name ,ptw.stock_area from oc_product_to_warehouse ptw LEFT JOIN  oc_product  p ON  ptw.product_id = p.product_id WHERE ptw.warehouse_id= '".$warehouse_id."' and ptw.product_id = '".$product_id ."'";

            $query = $db ->query($sql);
            $results = $query->rows;
        }



        return $results ;

    }

    function getReminderList(array $data){
        global  $db;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $date = isset($data['data']['date']) ? $data['data']['date'] : false;
        $sql = "select pr.reminder_id , pr.status,pr.product_id,p.name ,pr.stock_area,pr.add_user ,min(pr.date_added) as date_added,pr.date_confirm,pr.date_replenishment,pr.reason
              from oc_x_product_reminder pr
              LEFT  JOIN  oc_product p  ON   pr.product_id = p.product_id
              WHERE  pr.warehouse_id = '". $warehouse_id ."'  AND DATE (pr.date_added) between  date_sub( '".$date."', interval 1 day)  and  '".$date."'
              group by pr.product_id
           ORDER BY pr.`status` asc ,pr.date_added  desc ";

        $query = $db ->query($sql);
        $results = $query->rows;
        return $results;
    }

    function confirmReminder(array  $data){
        global  $db;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $product_id = isset($data['data']['product_id']) ? $data['data']['product_id'] : false;
        $reason = isset($data['data']['reason']) ? $data['data']['reason'] : false;
        $inventoryuser = isset($data['data']['inventory_user']) ? $data['data']['inventory_user'] : false;

        $sql =  " update oc_x_product_reminder set status = 1, confirm_user = '".$inventoryuser."' , date_confirm = NOW() , reason = '".$reason."' where warehouse_id = '" .$warehouse_id ."' and  product_id = '".$product_id."' ";

        $query = $db ->query($sql);
        $results = $query->rows;

        return $results;

    }


    function  confirmReplenishment(array $data){
        global  $db;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $product_id = isset($data['data']['product_id']) ? $data['data']['product_id'] : false;
        $inventoryuser = isset($data['data']['inventory_user']) ? $data['data']['inventory_user'] : false;
        $sql =  " update oc_x_product_reminder set status = 2, replenishment_user = '".$inventoryuser."' , date_replenishment = NOW()  where warehouse_id = '" .$warehouse_id ."' and  product_id = '".$product_id."' ";

        $query = $db ->query($sql);
        $results = $query->rows;

        return $results;
    }

    function getInfo(array $data){
        global  $db;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;

        $sql = "select pr.reminder_id ,pr.product_id ,p.name , ptw.stock_area
              from oc_x_product_reminder pr
              LEFT JOIN  oc_product_to_warehouse ptw  ON  pr.product_id = ptw.product_id and ptw.warehouse_id = '" .$warehouse_id ."'
              LEFT JOIN  oc_product p  on  pr.product_id = p.product_id
              WHERE  pr.warehouse_id = '". $warehouse_id ."' and  pr.status = 0
              order  by pr.reminder_id desc
             limit 1 ";

        $query = $db ->query($sql);
        $results = $query->row;
        return $results;

    }

    //物流退货按商品ID排序
    public function getAddedOrderReturnDeliverProduct (array  $data){
        global  $db;
        return  $data;
        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $date = isset($data['data']['date']) ? $data['data']['date'] : false;
        $isBack = isset($data['data']['isBack']) ? $data['data']['isBack'] : false;
        $isRepackMissing = isset($data['data']['isRepackMissing']) ? $data['data']['isRepackMissing'] : false;

        $sql = "select
        p.`return_deliver_product_id`,  p.`order_id`,  p.`return_reason_id`,  p.`return_action_id`,  p.`is_back`,  p.`is_repack_missing`,  p.`product_id`,  p.`product`,  p.`model`,  p.`quantity`, p.`in_part`,  p.`box_quantity`,  p.`price`,  p.`total`, p.`add_user_id`,  p.`date_added`,  p.`status`,  p.`confirm_user_id`,  p.`date_comfirmed`,  p.`confirmed`,  p.`return_id`
        from oc_return_deliver_product p
        LEFT JOIN oc_order o  ON o.order_id = o.order_id
        where  p.status = 1 and date_format( p.date_added, '%Y-%m-%d') = '" . $date . "'
        and  p.is_back = '". $isBack ."' and  p.is_repack_missing = '".$isRepackMissing."'
        and o.warehouse_id = '".$warehouse_id."'
        group by p.product_id
        order by  p.date_added desc";


        $query = $db->query($sql);
        $result = $query->rows;

        return $result;
    }

    //缺货单个确认


    function  confirmReturnSingleProduct( array $data){

        global $db, $dbm;
        global $log;

        //TODO 计算订单应收金额
        //TODO 计算缺货金额
        //TODO 应收金额 >= 缺货金额,  仅退货
        //TODO 应收金额 < 缺货金额,  退余额＝缺货金额－应收金额，实际应收为0，退余额待财务确认



        $date= isset($data['data']['searchDate']) ? $data['data']['searchDate'] : false;
        $product_id= isset($data['data']['product_id']) ? $data['data']['product_id'] : false;
        $userId= isset($data['data']['add_user_id']) ? $data['data']['add_user_id'] : false;
        $isBack= isset($data['data']['isBack']) ? $data['data']['isBack'] : false;
        $isRepackMissing= isset($data['data']['isRepackMissing']) ? $data['data']['isRepackMissing'] : false;
        $warehouse_id= isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $return_deliver_product_id= isset($data['data']['return_deliver_product_id']) ? $data['data']['return_deliver_product_id'] : false;


//        $userId = $data['add_user_id'];
//        $isBack = (int)$data['isBack'];
//        $isRepackMissing = (int)$data['isRepackMissing'];
//        $warehouse_id = $data['warehouse_id'];
        //查找指定日期，由本人提交的，有效的且未确认的退货记录
        $sql = "select order_id, sum(total) current_return_total from oc_return_deliver_product
        where status = 1 and confirmed = 0 and date_format(date_added, '%Y-%m-%d') = '" . $date . "'
        and is_back = '". $isBack ."' and is_repack_missing = '". $isRepackMissing ."' and  return_deliver_product_id = '".(int)$return_deliver_product_id."'  group by order_id";



        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 出库缺货退货'. $sql . "\n\r");
        $query = $db->query($sql);
        $currentReturnInfo = $query->rows;

        $currentReturnInfoList = array();
        $targetOrders = array(0);
        foreach($currentReturnInfo as $m){
            $targetOrders[] = $m['order_id'];
            $currentReturnInfoList[$m['order_id']] = $m;
        }


        $targetOrdersString =$targetOrders[1] ;

        //查找订单应付
        $sql = "select
                    o.order_id, o.firstname, o.customer_id, o.station_id, DATE(o.date_added) order_date ,
                    sum(if(ot.code in ('sub_total'), `value`, 0) ) sub_total,
                    sum(if(ot.accounting = 1, `value`, 0)) due_total,
                    sum(if(ot.code in ('discount_total','total_adjust','user_point'), `value`, 0) ) discount_total
                from oc_order o left join oc_order_total ot on o.order_id = ot.order_id
                where o.order_id  =  ".$targetOrdersString." group by o.order_id";

        $query = $db->query($sql);
        $dueInfo = $query->rows;
        $dueInfoList = array();
        foreach($dueInfo as $m){
            $dueInfoList[$m['order_id']] = $m;
        }

//        //Debug
//        $debugSql['order'] = $sql;

        //查找已退余额数据
        $sql = "
            select order_id, sum(if(amount is null, 0, amount)) return_credits_total
            from oc_customer_transaction
            where order_id in (".$targetOrdersString.") and customer_transaction_type_id in (1,5,6,7,8,9,10)
            group by order_id
        ";
        $query = $db->query($sql);
        $returnCredits = $query->rows;
        $returnCreditsList = array();
        foreach($returnCredits as $m){
            $returnCreditsList[$m['order_id']] = $m;
        }

//        //Debug
//        $debugSql['credit'] = $sql;

        #查找退货数据
        $sql = "
            select R.order_id, sum(RP.total) return_total
            from oc_return R
            left join oc_return_product RP on R.return_id = RP.return_id
            where R.order_id in (".$targetOrdersString.") and R.return_status_id = 2
            group by R.order_id
        ";
        $query = $db->query($sql);
        $returnedInfo = $query->rows;
        foreach($returnedInfo as $m){
            $returnedInfoList[$m['order_id']] = $m;
        }

        //查找预退款商品
        $sql = "select r.order_id, sum(rp.total) refund_total ,rp.quantity , rp.product_id from  oc_refund r LEFT JOIN  oc_refund_product rp on r.refund_id = rp.refund_id  WHERE r.order_id in (".$targetOrdersString.") and rp.product_id = '".$product_id."' group by r.order_id ";

        $query = $db->query($sql);
        $refundInfo = $query->row;


//        //Debug
//        $debugSql['return'] = $sql;

        //依次处理多个订单
        $issuedOrderId = array();
        foreach($currentReturnInfoList as $m){

            $stationId = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['station_id'] : 0;
            $firstname =  array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['firstname'] : 0;
            $customerId = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['customer_id'] : 0;
            $subTotal = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['sub_total'] : 0;
            $dueTotal = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['due_total'] : 0;
            $discountTotal = array_key_exists($m['order_id'], $dueInfoList) ? (float)$dueInfoList[$m['order_id']]['discount_total'] : 0;

            $returnCreditsTotal = array_key_exists($m['order_id'], $returnCreditsList) ? $returnCreditsList[$m['order_id']]['return_credits_total'] : 0;

            $returnedTotal = array_key_exists($m['order_id'], $returnedInfoList) ? $returnedInfoList[$m['order_id']]['return_total'] : 0; //已退货金额（含分拣缺货，出库缺货）
            $currentReturnTotal = $currentReturnInfoList[$m['order_id']]['current_return_total'];   //记录的退货金额

            //出库应收  = 订单应收金额-已退货
            $dueOut = $dueTotal  - $returnedTotal;

            if($dueOut <= 0){
                $dueOut = 0;

            }
//            else{
//                $dueCurrent = $subTotal  - $currentReturnTotal; //本次退货应收 = 出库应收+优惠 －记录的本次退货
//            }

            $dueCurrent = $dueOut - $currentReturnTotal ;
            $returnCurrent = ($dueCurrent < 0) ? abs($dueCurrent) : 0; //计算退货后后本次应收小于0，退余额



//           // 判断是否全部退货或退货金额占订单出货80%以上，不退余额
//            if($currentReturnTotal >= $subTotal*0.8){
//                $returnCurrent = 0;
//            }



            //判断退货金额是否超过已支付金额
            $creditBound = $subTotal -abs($discountTotal)- abs($returnedTotal); //订单金额- 优惠金额-已退余额-本次退货金额


            $returnCurrent = ($creditBound > $returnCurrent) ? $returnCurrent : $creditBound;

            $e = 0.01;//允许的误差值，刚刚的0.005，换成更精确0.00001
            if(abs($returnCurrent - abs($discountTotal)) < $e) {
                $returnCurrent = 0;
            }

            if($product_id == $refundInfo['product_id']){
                $returnCurrent -= $refundInfo['refund_total'];
            }

            if($returnCurrent < 0 ){
                $returnCurrent = 0 ;
            }

            //根据是出货退货和是否退余额确定退货操作
            $returnCredits = 0;
            $return_action_id = 1; //操作类型1（无），类型2（退还余额,退货入库），类型3（退还余额），类型4（退货入库）
            if($returnCurrent > 0){
                $return_action_id = $isBack ? 2 : 3;
                $returnCredits = $returnCurrent;
            }
            else{
                $return_action_id = $isBack ? 4 : 1;
                $returnCredits = 0;
            }

            $return_reason_id = $isBack ? 2 : 5; //出库缺货类型5（仓库出库，物流未找到），散件缺货时类型3（仓库出库，客户未收到）, 退货时类型2（客户退货）
            if($isRepackMissing){
                $return_reason_id = 3;
                $return_action_id = ($returnCurrent > 0) ? 3 : 1; //如果是回库散件缺货，不入库，判断是否应退余额
            }

//            $returnAll = $returnTotal + $currentReturnTotal;
//            if($returnAll > $outTotal){
//                //退货合计超过出库合计，跳过
//                $issuedOrderId[] = $m['order_id'];
//                continue;
//            }

            //For Debug
            $currentReturn = array(
                'stationId' => $stationId,
                'orderId' => $m['order_id'],
                'customerId' => $customerId,
                'firname用户' => $firstname,
                'subTotal订单商品小计' => $subTotal,
                'dueTotal订单初始应收' => $dueTotal,
                'discountTotal订单优惠' => $discountTotal,
                'returnCreditsTotal已退余额合计' => $returnCreditsTotal,
                'returnedTotal已退货合计' => $returnedTotal,
                'currentReturnTotal本次退货' => $currentReturnTotal,

                'dueOut出库应收' => $dueOut,
                'dueCurrent本次退货后应收'=>$dueCurrent,
                'creditBound余额退回边界'=>$creditBound,
                'returnCurrent本次应退余额'=>$returnCurrent,

                'returnCredits本次退余额' => $returnCredits,
                'return_reason_id退货原因' => $return_reason_id,
                'return_action_id退货操作' => $return_action_id,
                //'debugSQL' => $debugSql
            );
            //return $currentReturn;

            $dbm->begin();
            $bool = true;

            //写入退货表
            $sql = "INSERT INTO `oc_return` (`order_id`, `customer_id`, `return_reason_id`, `return_action_id`, `return_status_id`, `comment`, `date_ordered`, `date_added`, `date_modified`, `add_user`, `return_credits`, `return_inventory_flag`, `credits_returned`)
                    VALUES('".$m['order_id']."','".$customerId."','".$return_reason_id."','".$return_action_id."','2','仓库','".$dueInfoList[$m['order_id']]['order_date']."',NOW(),NOW(),'".$userId."','".$returnCredits."','0','0');";


            $bool = $bool && $dbm->query($sql);
            $return_id = $dbm->getLastId();

            $sql = "INSERT INTO `oc_return_product` (`return_id`, `product_id`, `product`,  `quantity`, `in_part`, `box_quantity`, `price`, `total`, `return_product_credits`)
                    SELECT '".$return_id."', `product_id`, `product`,  `quantity`,  `in_part`, `box_quantity`, `price`, `total`,  `total`
                    FROM oc_return_deliver_product
                    WHERE status = 1  AND confirmed = 0 AND return_deliver_product_id ='".(int)$return_deliver_product_id."' AND is_back = '".$isBack."' and is_repack_missing = '". $isRepackMissing ."'";

            $bool = $bool && $dbm->query($sql);


            //完成后更新出库回库记录状态
            $sql = "UPDATE oc_return_deliver_product set confirm_user_id = '".$userId."', date_comfirmed = NOW(), confirmed = 1, return_id='".$return_id."'
                    WHERE status = 1 AND confirmed = 0 AND return_deliver_product_id = '".(int)$return_deliver_product_id."' AND is_back = '".$isBack."' and is_repack_missing = '". $isRepackMissing ."'";
            $bool = $bool && $dbm->query($sql);

            if (!$bool) {
                $log->write('ERR:[' . __FUNCTION__ . ']' . ':  出库缺货开始退货［回滚］' . "\n\r");
                $dbm->rollback();

                return array('status' => 0, 'message' => '确认退货失败');
            } else {
                $log->write('INFO:[' . __FUNCTION__ . ']' . ': 出库缺货开始退货［提交］' . "\n\r");
                $dbm->commit();

                //退货记录完成，开始写入入库数据
                //退货入库操作写库存表，仅操作回库且需要退货入库的订单
                if($return_action_id == 2 || $return_action_id == 4){
                    //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 开始退货入库' . "\n\r");
                    $stockMoveData = array();
                    $stockMoveData['api_method'] = 'inventoryReturn';
                    $stockMoveData['timestamp'] = time();
                    $stockMoveData['from_station_id'] = 0;
                    $stockMoveData['to_station_id'] = $stationId;
                    $stockMoveData['order_id'] = $m['order_id'];
                    $stockMoveData['purchase_order_id'] = 0;
                    $stockMoveData['added_by'] = isset($userId) ? (int)$userId : 0;
                    $stockMoveData['memo'] = '现场退货入库';
                    $stockMoveData['add_user_name'] = '';
                    $stockMoveData['products'] = array();

                    //获取退货的商品列表,需要station_id, product_id, price, quantity, box_quantity
                    $sql = "SELECT '".$stationId."', `product_id`, `price` special_price, `quantity` qty, `box_quantity`
                            FROM oc_return_product WHERE return_id = '".$return_id."'";
                    $query = $db->query($sql);
                    $stockMoveData['products'] = $query->rows;

                    $this->addInventoryMoveOrders($stockMoveData, $stationId,$warehouse_id);

                    //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 退货入库完成' . "\n\r");
                }

                if(sizeof($issuedOrderId)){
                    return array('status' => 2, 'message' => '确认退货成功, 部分订单退货金额有误，请核实['.implode(',',$issuedOrderId).']');
                }
                return array('status' => 1, 'message' => '确认退货成功' , 'jie' => $creditBound);
            }
        }


    }

    //报损当面
    function  confirmReturnBadSingleProduct(array $data ){
        global $db, $dbm;
        global $log;

        //TODO 计算订单应收金额
        //TODO 计算缺货金额
        //TODO 应收金额 >= 缺货金额,  仅退货
        //TODO 应收金额 < 缺货金额,  退余额＝缺货金额－应收金额，实际应收为0，退余额待财务确认



        $date= isset($data['data']['searchDate']) ? $data['data']['searchDate'] : false;
        $userId= isset($data['data']['add_user_id']) ? $data['data']['add_user_id'] : false;
        $isBack= isset($data['data']['isBack']) ? $data['data']['isBack'] : false;
        $isRepackMissing= isset($data['data']['isRepackMissing']) ? $data['data']['isRepackMissing'] : false;
        $warehouse_id= isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $return_deliver_product_id= isset($data['data']['return_deliver_product_id']) ? $data['data']['return_deliver_product_id'] : false;


//        $userId = $data['add_user_id'];
//        $isBack = (int)$data['isBack'];
//        $isRepackMissing = (int)$data['isRepackMissing'];
//        $warehouse_id = $data['warehouse_id'];
        //查找指定日期，由本人提交的，有效的且未确认的退货记录
        $sql = "select order_id, sum(total) current_return_total from oc_return_deliver_bad_product
        where status = 1 and confirmed = 0 and date_format(date_added, '%Y-%m-%d') = '" . $date . "'
        and is_back = '". $isBack ."' and is_repack_missing = '". $isRepackMissing ."' and  return_deliver_product_id = '".(int)$return_deliver_product_id."'  group by order_id";



        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 出库缺货退货'. $sql . "\n\r");
        $query = $db->query($sql);
        $currentReturnInfo = $query->rows;

        $currentReturnInfoList = array();
        $targetOrders = array(0);
        foreach($currentReturnInfo as $m){
            $targetOrders[] = $m['order_id'];
            $currentReturnInfoList[$m['order_id']] = $m;
        }


        $targetOrdersString =$targetOrders[1] ;

        //查找订单应付
        $sql = "select
                    o.order_id, o.firstname, o.customer_id, o.station_id, DATE(o.date_added) order_date ,
                    sum(if(ot.code in ('sub_total'), `value`, 0) ) sub_total,
                    sum(if(ot.accounting = 1, `value`, 0)) due_total,
                    sum(if(ot.code in ('discount_total','total_adjust','user_point'), `value`, 0) ) discount_total
                from oc_order o left join oc_order_total ot on o.order_id = ot.order_id
                where o.order_id  =  ".$targetOrdersString." group by o.order_id";

        $query = $db->query($sql);
        $dueInfo = $query->rows;
        $dueInfoList = array();
        foreach($dueInfo as $m){
            $dueInfoList[$m['order_id']] = $m;
        }

//        //Debug
//        $debugSql['order'] = $sql;

        //查找已退余额数据
        $sql = "
            select order_id, sum(if(amount is null, 0, amount)) return_credits_total
            from oc_customer_transaction
            where order_id in (".$targetOrdersString.") and customer_transaction_type_id in (1,5,6,7,8,9,10)
            group by order_id
        ";
        $query = $db->query($sql);
        $returnCredits = $query->rows;
        $returnCreditsList = array();
        foreach($returnCredits as $m){
            $returnCreditsList[$m['order_id']] = $m;
        }

//        //Debug
//        $debugSql['credit'] = $sql;

        #查找退货数据
        $sql = "
            select R.order_id, sum(RP.total) return_total
            from oc_return R
            left join oc_return_product RP on R.return_id = RP.return_id
            where R.order_id in (".$targetOrdersString.") and R.return_status_id = 2
            group by R.order_id
        ";
        $query = $db->query($sql);
        $returnedInfo = $query->rows;
        foreach($returnedInfo as $m){
            $returnedInfoList[$m['order_id']] = $m;
        }

//        //Debug
//        $debugSql['return'] = $sql;

        //依次处理多个订单
        $issuedOrderId = array();
        foreach($currentReturnInfoList as $m){

            $stationId = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['station_id'] : 0;
            $firstname =  array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['firstname'] : 0;
            $customerId = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['customer_id'] : 0;
            $subTotal = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['sub_total'] : 0;
            $dueTotal = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['due_total'] : 0;
            $discountTotal = array_key_exists($m['order_id'], $dueInfoList) ? $dueInfoList[$m['order_id']]['discount_total'] : 0;

            $returnCreditsTotal = array_key_exists($m['order_id'], $returnCreditsList) ? $returnCreditsList[$m['order_id']]['return_credits_total'] : 0;

            $returnedTotal = array_key_exists($m['order_id'], $returnedInfoList) ? $returnedInfoList[$m['order_id']]['return_total'] : 0; //已退货金额（含分拣缺货，出库缺货）
            $currentReturnTotal = $currentReturnInfoList[$m['order_id']]['current_return_total'];   //记录的退货金额

            //出库应收  = 订单应收金额-已退货
            $dueOut = $dueTotal  - $returnedTotal;

            if($dueOut <= 0){
                $dueOut = 0;

            }
//            else{
//                $dueCurrent = $subTotal  - $currentReturnTotal;//本次退货应收 = 出库应收+优惠 －记录的本次退货
//            }

            $dueCurrent = $dueOut  - $currentReturnTotal ;
            $returnCurrent = ($dueCurrent < 0) ? abs($dueCurrent) : 0; //计算退货后后本次应收小于0，退余额



//           // 判断是否全部退货或退货金额占订单出货80%以上，不退余额
//            if($currentReturnTotal >= $subTotal*0.8){
//                $returnCurrent = 0;
//            }


            //判断退货金额是否超过已支付金额
            $creditBound = $subTotal - abs($discountTotal) - abs($returnedTotal); //订单金额- 优惠金额-已退余额-本次退货金额


            $returnCurrent = ($creditBound > $returnCurrent) ? $returnCurrent : $creditBound;

            //根据是出货退货和是否退余额确定退货操作
            $returnCredits = 0;
            $return_action_id = 1; //操作类型1（无），类型2（退还余额,退货入库），类型3（退还余额），类型4（退货入库）
            if($returnCurrent > 0){
                $return_action_id = $isBack ? 2 : 3;
                $returnCredits = $returnCurrent;
            }
            else{
                $return_action_id = $isBack ? 4 : 1;
                $returnCredits = 0;
            }

            $return_reason_id = $isBack ? 2 : 5; //出库缺货类型5（仓库出库，物流未找到），散件缺货时类型3（仓库出库，客户未收到）, 退货时类型2（客户退货）
            if($isRepackMissing){
                $return_reason_id = 3;
                $return_action_id = ($returnCurrent > 0) ? 3 : 1; //如果是回库散件缺货，不入库，判断是否应退余额
            }

//            $returnAll = $returnTotal + $currentReturnTotal;
//            if($returnAll > $outTotal){
//                //退货合计超过出库合计，跳过
//                $issuedOrderId[] = $m['order_id'];
//                continue;
//            }

            //For Debug
            $currentReturn = array(
                'stationId' => $stationId,
                'orderId' => $m['order_id'],
                'customerId' => $customerId,
                'firname用户' => $firstname,
                'subTotal订单商品小计' => $subTotal,
                'dueTotal订单初始应收' => $dueTotal,
                'discountTotal订单优惠' => $discountTotal,
                'returnCreditsTotal已退余额合计' => $returnCreditsTotal,
                'returnedTotal已退货合计' => $returnedTotal,
                'currentReturnTotal本次退货' => $currentReturnTotal,

                'dueOut出库应收' => $dueOut,
                'dueCurrent本次退货后应收'=>$dueCurrent,
                'creditBound余额退回边界'=>$creditBound,
                'returnCurrent本次应退余额'=>$returnCurrent,

                'returnCredits本次退余额' => $returnCredits,
                'return_reason_id退货原因' => $return_reason_id,
                'return_action_id退货操作' => $return_action_id,
                //'debugSQL' => $debugSql
            );
            //return $currentReturn;

            $dbm->begin();
            $bool = true;

            //写入退货表
            $sql = "INSERT INTO `oc_return_bad` (`order_id`, `customer_id`, `return_reason_id`, `return_action_id`, `return_status_id`, `comment`, `date_ordered`, `date_added`, `date_modified`, `add_user`, `return_credits`, `return_inventory_flag`, `credits_returned`)
                    VALUES('".$m['order_id']."','".$customerId."','".$return_reason_id."','".$return_action_id."','2','仓库','".$dueInfoList[$m['order_id']]['order_date']."',NOW(),NOW(),'".$userId."','".$returnCredits."','0','0');";


            $bool = $bool && $dbm->query($sql);
            $return_id = $dbm->getLastId();

            $sql = "INSERT INTO `oc_return_bad_product` (`return_id`, `product_id`, `product`,  `quantity`, `in_part`, `box_quantity`, `price`, `total`, `return_product_credits`)
                    SELECT '".$return_id."', `product_id`, `product`,  `quantity`,  `in_part`, `box_quantity`, `price`, `total`,  `total`
                    FROM oc_return_deliver_bad_product
                    WHERE status = 1  AND confirmed = 0 AND return_deliver_product_id ='".(int)$return_deliver_product_id."' AND is_back = '".$isBack."' and is_repack_missing = '". $isRepackMissing ."'";

            $bool = $bool && $dbm->query($sql);


            //完成后更新出库回库记录状态
            $sql = "UPDATE oc_return_deliver_bad_product set confirm_user_id = '".$userId."', date_comfirmed = NOW(), confirmed = 1, return_id='".$return_id."'
                    WHERE status = 1 AND confirmed = 0 AND return_deliver_product_id = '".(int)$return_deliver_product_id."' AND is_back = '".$isBack."' and is_repack_missing = '". $isRepackMissing ."'";
            $bool = $bool && $dbm->query($sql);

            if (!$bool) {
                $log->write('ERR:[' . __FUNCTION__ . ']' . ':  出库缺货开始退货［回滚］' . "\n\r");
                $dbm->rollback();

                return array('status' => 0, 'message' => '确认退货失败');
            } else {
                $log->write('INFO:[' . __FUNCTION__ . ']' . ': 出库缺货开始退货［提交］' . "\n\r");
                $dbm->commit();

                //退货记录完成，开始写入入库数据
                //退货入库操作写库存表，仅操作回库且需要退货入库的订单
                if($return_action_id == 2 || $return_action_id == 4){
                    //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 开始退货入库' . "\n\r");
                    $stockMoveData = array();
                    $stockMoveData['api_method'] = 'inventoryReturn';
                    $stockMoveData['timestamp'] = time();
                    $stockMoveData['from_station_id'] = 0;
                    $stockMoveData['to_station_id'] = $stationId;
                    $stockMoveData['order_id'] = $m['order_id'];
                    $stockMoveData['purchase_order_id'] = 0;
                    $stockMoveData['added_by'] = isset($userId) ? (int)$userId : 0;
                    $stockMoveData['memo'] = '仓库退货之后报损';
                    $stockMoveData['add_user_name'] = '';
                    $stockMoveData['products'] = array();

                    //获取退货的商品列表,需要station_id, product_id, price, quantity, box_quantity
                    $sql = "SELECT '".$stationId."', `product_id`, `price` special_price, `quantity` qty, `box_quantity`
                            FROM oc_return_bad_product WHERE return_id = '".$return_id."'";
                    $query = $db->query($sql);
                    $stockMoveData['products'] = $query->rows;
                    $badReturn = -1;
                    //   $this->addInventoryBadMoveOrders($stockMoveData, $stationId,$warehouse_id,$badReturn);

                    //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 退货入库完成' . "\n\r");
                }

                if(sizeof($issuedOrderId)){
                    return array('status' => 2, 'message' => '确认报损成功, 部分订单退货金额有误，请核实['.implode(',',$issuedOrderId).']');
                }
                return array('status' => 1, 'message' => '确认报损成功' , 'jie' => $creditBound);
            }
        }

    }


//    //报损当面
//    function  addInventoryBadMoveOrders($data, $station_id,$warehouse_id,$badReturn){
//        global $db, $dbm, $log;
//
//        //$log->write('INFO:['.__FUNCTION__.']'.': '.serialize($data)."\n\r");
//
//        if (!is_array($data) || !sizeof($data) || !$station_id) {
//            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
//            return false;
//        }
//
//        if (!sizeof($data['products']) && $data['api_method'] != 'inventoryOrderIn') {
//            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少商品信息' . "\n\r");
//            return false;
//        }
//
//        if (!isset($data['timestamp']) || !$data['timestamp']) {
//            return false;
//        }
//
//        if (!defined('INVENTORY_TYPE_OP')) {
//            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少库存计算关键配置数据[INVENTORY_TYPE_OP]' . "\n\r");
//            return false;
//        }
//
//        //Check timestamp
//        $sql = "select inventory_move_id from oc_x_stock_move where station_id = '" . $station_id . "' and timestamp = '" . $data['timestamp'] . "';";
//
//        $query = $db->query($sql);
//        if (sizeof($query->rows)) {
//            return false;
//        }
//
//
//        if($data['api_method'] == 'inventoryOrderIn'){
//
//            $sql = "select xsm.order_id,o.station_id from oc_x_stock_move as xsm left join oc_order as o on o.order_id = xsm.order_id where xsm.inventory_type_id = 12 and xsm.order_id = " . (isset($data['order_id']) ? (int) $data['order_id'] : 0);
//            $query = $dbm->query($sql);
//            $result_exists = $query->rows;
//
//            if(!empty($result_exists)){
//                if($result_exists[0]['station_id'] != 2){
//                    return false;
//                }
//            }
//
//        }
//
//
//
//
//
//        //Get Inventory Type Opration From config
//        $inventory_type_op = unserialize(INVENTORY_TYPE_OP); //array('api method'=>array(inventory_type_id, operation))
//        $inventory_op =$badReturn ;
//        $inventory_type = $inventory_type_op[$data['api_method']][0];
//
//        if (!$inventory_type) {
//            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 未指定库存变动类型' . "\n\r");
//            return false;
//        }
//
//        $data_insert = array();
//
//        $data_insert['station_id'] = $station_id;
//        $data_insert['timestamp'] = $data['timestamp'];
//        $data_insert['from_station_id'] = isset($data['from_station_id']) ? (int) $data['from_station_id'] : 0;
//        $data_insert['to_station_id'] = isset($data['to_station_id']) ? (int) $data['to_station_id'] : 0;
//        $data_insert['order_id'] = isset($data['order_id']) ? (int) $data['order_id'] : 0;
//
//        $data_insert['purchase_order_id'] = isset($data['purchase_order_id']) ? (int) $data['purchase_order_id'] : 0;
//
//        $data_insert['inventory_type_id'] = 3;
//        $data_insert['date_added'] = date('Y-m-d H:i:s', time());
//        $data_insert['added_by'] = isset($data['added_by']) ? (int) $data['added_by'] : 0;
//        $data_insert['memo'] = '仓库退货之后报损';
//        $data_insert['add_user_name'] = isset($data['add_user_name']) ? $data['add_user_name'] : '';
//        $data_insert['warehouse_id'] = $warehouse_id;
//        $log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动类型：'.$data['api_method']);
//
//        $dbm->begin();
//        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Begin' . "\n\r");
//        $bool = true;
//        $sql = "INSERT INTO `oc_x_stock_move` SET ";
//        foreach ($data_insert as $key => $val) {
//            $sql .= '`' . $key . '`' . '="' . $val . '"';
//            if (current($data_insert) === false) {
//                $sql .= ';';
//            } else {
//                $sql .= ', ';
//            }
//            next($data_insert);
//        }
//
//        $bool = $bool && $dbm->query($sql);
//        $inventory_move_id = $dbm->getLastId();
//
//        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表' . "\n\r");
//
//        if(!empty($data['products'])){
//            $sql = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`) VALUES ";
//            $m = 0;
//            foreach ($data['products'] as $product) {
//                $sql .= "(" . $inventory_move_id . ", " . $station_id . ", '" . (isset($product['due_date']) ? $product['due_date'] : '0000-00-00') . "', '" . $product['product_id'] . "', '" . $product['special_price'] . "', '" . (isset($product['product_batch']) ? $product['product_batch'] : '') . "', " . $product['qty'] * $inventory_op . ", '".(isset($product['box_quantity']) ? $product['box_quantity'] : 1)."', " . (isset($product['product_weight']) ? $product['product_weight'] : 0) . "," . (isset($product['is_gift']) ? $product['is_gift'] : 0) . ", " . (isset($product['checked']) ? $product['checked'] : 0) . "," . (isset($product['status']) ? $product['status'] : 1) . "," . (isset($product['sku_id']) ? $product['sku_id'] : 0) . ")";
//
//                if (++$m < sizeof($data['products'])) {
//                    $sql .= ', ';
//                } else {
//                    $sql .= ';';
//                }
//            }
//
//            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存明细表' . "\n\r");
//            $bool = $bool && $dbm->query($sql);
//        }
//        if (!$bool) {
//            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Rollback' . "\n\r");
//            $dbm->rollback();
//            return false;
//        } else {
//            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Commit' . "\n\r");
//            $dbm->commit();
//            return true;
//        }
//    }

    function addInventoryMoveOrders($data, $station_id,$warehouse_id) {
        global $db, $dbm, $log;

        //$log->write('INFO:['.__FUNCTION__.']'.': '.serialize($data)."\n\r");

        if (!is_array($data) || !sizeof($data) || !$station_id) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 参数错误' . "\n\r");
            return false;
        }

        if (!sizeof($data['products']) && $data['api_method'] != 'inventoryOrderIn') {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少商品信息' . "\n\r");
            return false;
        }

        if (!isset($data['timestamp']) || !$data['timestamp']) {
            return false;
        }

        if (!defined('INVENTORY_TYPE_OP')) {
            $log->write('ERR:[' . __FUNCTION__ . ']' . ': 缺少库存计算关键配置数据[INVENTORY_TYPE_OP]' . "\n\r");
            return false;
        }

        //Check timestamp
        $sql = "select inventory_move_id from oc_x_stock_move where station_id = '" . $station_id . "' and timestamp = '" . $data['timestamp'] . "';";

        $query = $db->query($sql);
        if (sizeof($query->rows)) {
            return false;
        }


        if($data['api_method'] == 'inventoryOrderIn'){

            $sql = "select xsm.order_id,o.station_id from oc_x_stock_move as xsm left join oc_order as o on o.order_id = xsm.order_id where xsm.inventory_type_id = 12 and xsm.order_id = " . (isset($data['order_id']) ? (int) $data['order_id'] : 0);
            $query = $dbm->query($sql);
            $result_exists = $query->rows;

            if(!empty($result_exists)){
                if($result_exists[0]['station_id'] != 2){
                    return false;
                }
            }

        }





        //Get Inventory Type Opration From config
        $inventory_type_op = unserialize(INVENTORY_TYPE_OP); //array('api method'=>array(inventory_type_id, operation))
        $inventory_op = $inventory_type_op[$data['api_method']][1];
        $inventory_type = $inventory_type_op[$data['api_method']][0];

        if (!$inventory_type) {
            $log->write('INFO:[' . __FUNCTION__ . ']' . ': 未指定库存变动类型' . "\n\r");
            return false;
        }

        $data_insert = array();

        $data_insert['station_id'] = $station_id;
        $data_insert['timestamp'] = $data['timestamp'];
        $data_insert['from_station_id'] = isset($data['from_station_id']) ? (int) $data['from_station_id'] : 0;
        $data_insert['to_station_id'] = isset($data['to_station_id']) ? (int) $data['to_station_id'] : 0;
        $data_insert['order_id'] = isset($data['order_id']) ? (int) $data['order_id'] : 0;

        $data_insert['purchase_order_id'] = isset($data['purchase_order_id']) ? (int) $data['purchase_order_id'] : 0;

        $data_insert['inventory_type_id'] = isset($inventory_type) ? (int) $inventory_type : 0;
        $data_insert['date_added'] = date('Y-m-d H:i:s', time());
        $data_insert['added_by'] = isset($data['added_by']) ? (int) $data['added_by'] : 0;
        $data_insert['memo'] = isset($data['memo']) ? $db->escape($data['memo']) : '';
        $data_insert['add_user_name'] = isset($data['add_user_name']) ? $data['add_user_name'] : '';
        $data_insert['warehouse_id'] = $warehouse_id;
        $log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动类型：'.$data['api_method']);

        $dbm->begin();
        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Begin' . "\n\r");
        $bool = true;
        $sql = "INSERT INTO `oc_x_stock_move` SET ";
        foreach ($data_insert as $key => $val) {
            $sql .= '`' . $key . '`' . '="' . $val . '"';
            if (current($data_insert) === false) {
                $sql .= ';';
            } else {
                $sql .= ', ';
            }
            next($data_insert);
        }

        $bool = $bool && $dbm->query($sql);
        $inventory_move_id = $dbm->getLastId();

        //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表' . "\n\r");

        if(!empty($data['products'])){
            $sql = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`) VALUES ";
            $m = 0;
            foreach ($data['products'] as $product) {
                $sql .= "(" . $inventory_move_id . ", " . $station_id . ", '" . (isset($product['due_date']) ? $product['due_date'] : '0000-00-00') . "', '" . $product['product_id'] . "', '" . $product['special_price'] . "', '" . (isset($product['product_batch']) ? $product['product_batch'] : '') . "', " . $product['qty'] * $inventory_op . ", '".(isset($product['box_quantity']) ? $product['box_quantity'] : 1)."', " . (isset($product['product_weight']) ? $product['product_weight'] : 0) . "," . (isset($product['is_gift']) ? $product['is_gift'] : 0) . ", " . (isset($product['checked']) ? $product['checked'] : 0) . "," . (isset($product['status']) ? $product['status'] : 1) . "," . (isset($product['sku_id']) ? $product['sku_id'] : 0) . ")";

                if (++$m < sizeof($data['products'])) {
                    $sql .= ', ';
                } else {
                    $sql .= ';';
                }
            }

            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存明细表' . "\n\r");
            $bool = $bool && $dbm->query($sql);
        }
        //If the method is init, make every other records checked
        if ($data['api_method'] == 'inventoryInit') {
            $sql = "UPDATE oc_x_stock_move_item SET checked=1 WHERE station_id = '" . $station_id . "' AND checked = 0 AND inventory_move_id < " . $inventory_move_id;
            $bool = $bool && $dbm->query($sql);
        }

        //对于指定的库存变动类型（退货入库、商品报损、库存调整），同步调整前台可售库存。（采购入库已在其他地方处理）
        $inventory_type_auto_sync = unserialize(INVENTORY_TYPE_AUTO_SYNC);

        if(in_array($inventory_type, $inventory_type_auto_sync)){
            $sql = "INSERT INTO oc_x_inventory_move (`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `added_by`, `add_user_name`, `memo`,`warehouse_id`)
                    VALUES('".$station_id."', current_date(), unix_timestamp(), '0', '".$inventory_type."', now(), '".$data_insert['added_by']."', '".$data_insert['add_user_name']."', '[API]".$data_insert['memo']."','".$warehouse_id."')";
            $bool = $bool && $dbm->query($sql);
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动操作SQL：'.$sql);
            $inventory_move_id = $dbm->getLastId();
            //$inventory_move_id = 999;
            $sql = 'INSERT INTO oc_x_inventory_move_item(`inventory_move_id`, `station_id`, `product_id`, `quantity`,`warehouse_id`) VALUES';

            $m = 0;
            foreach ($data['products'] as $product) {
                //处理散件退货商品 - 散件暂时不退货可售库存
                //TODO 散件售卖
                $returnInvqty = $product['qty'];
                if(isset($product['box_quantity']) && $product['box_quantity'] > 1){
                    $returnInvqty = 0;
                }
                $sql .= "('".$inventory_move_id."','".$station_id."','".$product['product_id']."','".$returnInvqty*$inventory_op ."','". $warehouse_id ."')";
                if (++$m < sizeof($data['products'])) {
                    $sql .= ', ';
                } else {
                    $sql .= ';';
                }

            }
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 变动操作SQL：'.$sql);
            $bool = $bool && $dbm->query($sql);
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 可售库存变动已添加');
        }

        if (!$bool) {
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Rollback' . "\n\r");
            $dbm->rollback();
            return false;
        } else {
            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存表 Commit' . "\n\r");
            $dbm->commit();
            return true;
        }

        //TODO Update inventory / Redis
    }


    // 出库基础信息录入
    public function submitCorrectionOutOrder(array $data){
        global  $db;

        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $new_inv_comment = $data['data']['inv_comment'] ? $data['data']['inv_comment'] : '';
        $frames = $data['data']['frames'] ? $data['data']['frames'] : '';
        $user_id = $data['data']['user_id'] ? $data['data']['user_id'] : '';
        $check_value = $data['data']['check_value'] ? $data['data']['check_value'] : '';
        $frame_count = $data['data']['frame_count'] ? $data['data']['frame_count'] : '';
        $frames_ids = $data['data']['frames_ids'] ? $data['data']['frames_ids'] : '';
        $frame_margin = $data['data']['frame_margin'] ? $data['data']['frame_margin'] : '';
        $frame_carton_list = $data['data']['frame_carton_list'] ? $data['data']['frame_carton_list'] : '';
        $frame_carton_count = $data['data']['frame_carton_count'] ? $data['data']['frame_carton_count'] : '';

        $string=implode(',',$check_value);
        $sql1 = " SELECT  A.inv_comment FROM oc_order_inv A  WHERE  A.order_id = '" . $order_id . "' ";
        $query1 = $db->query($sql1);
        $result1 = $query1->row;
        $old_inv_comment = $result1['inv_comment'];


        //判断是否提交过周转筐
        if($frame_count > 0){
            //未提交
            $sql = " update oc_order_inv   set  frame_count = '".$frame_count."' , frame_vg_list= '".$frames_ids ."' ,frame_carton_list = '".$frame_carton_list."',frame_carton_count = '".$frame_carton_count."'  where  order_id = '".$order_id."' ";
            $query = $db->query($sql);

            //货位号错误
            if ($new_inv_comment != $old_inv_comment ) {

                $sql = "INSERT INTO oc_x_order_out_info ( `order_id`,`old_inv_comment`,`new_inv_comment`,`reasons`,`container_id`,`add_user`,`date_added`,`num`,`carton_container`)
                VALUES ('" . $order_id . "','" . $old_inv_comment . "','" . $new_inv_comment . "','" . $string . "','" . $frames . "','" . $user_id . "',NOW(),'".$frame_margin."','".$frame_carton_list."')";

                $query = $db->query($sql);
                if ($query) {
                    $sql = "UPDATE oc_order_inv SET check_status = 1 ,inv_comment = $new_inv_comment ,frame_carton_list = '".$frame_carton_list."',frame_carton_count = '".$frame_carton_count."' WHERE order_id = '" . $order_id . "' ";

                    $query = $db->query($sql);
                    $return['status'] = 2;
                    $return['check_location'] = $query->rows;
                }

            }

            //货位号相同
            if ($new_inv_comment == $old_inv_comment) {
                $sql = "INSERT INTO oc_x_order_out_info ( `order_id`,`old_inv_comment`,`new_inv_comment`,`reasons`,`container_id`,`add_user`,`date_added`,`num`,`carton_container`)
                VALUES ('" . $order_id . "','" . $old_inv_comment . "','" . $new_inv_comment . "','" . $string . "','" . $frames . "','" . $user_id . "',NOW(),'".$frame_margin."','".$frame_carton_list."')";
                $query = $db->query($sql);
                if ($query) {
                    $sql = "UPDATE oc_order_inv SET check_status = 1 ,frame_carton_list = '".$frame_carton_list."' ,frame_carton_count = '".$frame_carton_count."' WHERE order_id = '" . $order_id . "' ";
                    $query = $db->query($sql);
                    $return['status'] = 4;
                    $return['check_location'] = $query->rows;

                }
            }


        }else{
            //已经提交
            //货位号错误
            if ($new_inv_comment != $old_inv_comment ) {

                $sql = "INSERT INTO oc_x_order_out_info ( `order_id`,`old_inv_comment`,`new_inv_comment`,`reasons`,`container_id`,`add_user`,`date_added`,`num`,`carton_container`)
                VALUES ('" . $order_id . "','" . $old_inv_comment . "','" . $new_inv_comment . "','" . $string . "','" . $frames . "','" . $user_id . "',NOW(),'".$frame_margin."','".$frame_carton_list."')";

                $query = $db->query($sql);
                if ($query) {
                    $sql = "UPDATE oc_order_inv SET check_status = 1 ,inv_comment = $new_inv_comment,frame_carton_list = '".$frame_carton_list."',frame_carton_count = '".$frame_carton_count."' WHERE order_id = '" . $order_id . "' ";

                    $query = $db->query($sql);
                    $return['status'] = 2;
                    $return['check_location'] = $query->rows;
                }

            }

            //货位号相同
            if ($new_inv_comment == $old_inv_comment) {
                $sql = "INSERT INTO oc_x_order_out_info ( `order_id`,`old_inv_comment`,`new_inv_comment`,`reasons`,`container_id`,`add_user`,`date_added`,`num`,`carton_container`)
                VALUES ('" . $order_id . "','" . $old_inv_comment . "','" . $new_inv_comment . "','" . $string . "','" . $frames . "','" . $user_id . "',NOW(),'".$frame_margin."','".$frame_carton_list."')";
                $query = $db->query($sql);
                if ($query) {
                    $sql = "UPDATE oc_order_inv SET check_status = 1 ,frame_carton_list = '".$frame_carton_list."',frame_carton_count = '".$frame_carton_count."'WHERE order_id = '" . $order_id . "' ";
                    $query = $db->query($sql);
                    $return['status'] = 4;
                    $return['check_location'] = $query->rows;

                }
            }


        }


        return $return;
    }




    //出库订单商品信息核对
    public function showOrderProducts(array $data){
        global  $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';

        $sql =  "SELECT
                smi.product_id ,
                p.name,
                abs(smi.quantity) quantity
                FROM
                    oc_x_stock_move sm
                LEFT JOIN oc_x_stock_move_item smi ON sm.inventory_move_id = smi.inventory_move_id
                LEFT JOIN oc_product p ON smi.product_id = p.product_id
                WHERE  sm.order_id  = '".$order_id."' and sm.inventory_type_id = 12 ";

        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }

    //采购单信息
    function  getPurchaseInfo(array  $data){

        global  $db;
        $order_id = $data['data']['order_id'] ? $data['data']['order_id'] : '';
        $sql = "select ppo.purchase_order_id , st.name  from  oc_x_pre_purchase_order ppo
               LEFT JOIN oc_x_supplier AS st ON st.supplier_id = ppo.supplier_type  WHERE  ppo.purchase_order_id = '".$order_id."'";

        $query = $db->query($sql);
        $result = $query->row;

        return $result;
    }

    public  function  getPurchaseTypeOrders( array $data){
        global $db;
        global $log;

        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $date = $data['data']['date'] ? $data['data']['date'] : '';
        $order_status_id = $data['data']['order_status_id'] ? $data['data']['order_status_id'] : '';
        $purchase_order_id = $data['data']['purchase_order_id'] ? $data['data']['purchase_order_id'] : '';


        if(!$date){
            //return false;
        }


        $sql = "SELECT
	o.purchase_order_id as order_id,
        o.station_id,
	o.`status` as order_status_id,
	os.`name` AS os_name,
	st.`name` AS st_name,
        o.order_comment,
	SUM(op.quantity) as quantity,
	o.order_type

FROM
	oc_x_pre_purchase_order AS o
LEFT JOIN oc_x_pre_purchase_order_product AS op ON o.purchase_order_id = op.purchase_order_id
-- LEFT JOIN oc_x_supplier_type AS st ON st.supplier_type_id = o.supplier_type
LEFT JOIN oc_x_supplier AS st ON st.supplier_id = o.supplier_type
LEFT JOIN oc_x_pre_purchase_order_cancelstatus AS os ON o.`status` = os.order_status_id
where o.order_type = 2  and o.warehouse_id = '".$warehouse_id."'
   ";



        if($date != ''){
            $sql .=" and o.date_deliver = '" . $date . "'";
        }
        if($order_status_id != 0 ){
            $sql .= " AND o.status = " . $order_status_id;
        }

        if($purchase_order_id != 0 ){
            $sql .= " AND o.purchase_order_id = " . $purchase_order_id;
        }

        $sql .= " GROUP BY o.purchase_order_id order by o.purchase_order_id asc";

        $query = $db->query($sql);
        $results = $query->rows;

        $return = array();
        $return['data'] = array();

        //echo "<pre>";print_r($results);

        if(sizeof($results)){
            foreach($results as $k=>$v){



                $return['data'][$v['order_id']] = $v;
                $return['data'][$v['order_id']]['plan_quantity'] = $v['quantity'];
                $return['data'][$v['order_id']]['added_by'] = '';
                $return['data'][$v['order_id']]['station_id'] = $v['station_id'];
            }
        }
        //print_r($return['data']);

        //$last_one_day = date("Y-m-d", time() + 8*3600 - 24*3600);
        //获取入库中间表中已入库的商品，并从计划入库的商品中减去已入库的商品

        $sql = "SELECT
	xis.*
FROM
	oc_x_inventory_purchase_order_sorting AS xis
LEFT JOIN oc_x_pre_purchase_order AS o ON o.purchase_order_id = xis.order_id
WHERE
	o.date_deliver =  '" . $date . "' ";

        $query = $db->query($sql);
        $result = $query->rows;




        if(sizeof($result)){
            foreach($result as $rk => $rv){



                $return_move_p = array();
                if($return['data'][$rv['order_id']]['quantity'] > 0){
                    $return['data'][$rv['order_id']]['quantity'] -= $rv['quantity'];
                    if($return['data'][$rv['order_id']]['quantity'] <= 0){
                        $return_move_p = $return['data'][$rv['order_id']];
                        unset($return['data'][$rv['order_id']]);
                        $return['data'][$rv['order_id']] = $return_move_p;
                    }
                    $return['data'][$rv['order_id']]['added_by'] = $rv['added_by'];



                }
            }
        }
        //echo "<pre>";print_r($return);exit;



        if(sizeof($return)){

            return $return;
        }
        else{
            return array();
        }

    }

    public function changeProductSku(array $data){
        global $db;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $product_id = $data['data']['productId'] ? $data['data']['productId'] : '';
        $productBarCode = $data['data']['productBarCode'] ? $data['data']['productBarCode'] : '';
        $productBarCode = trim($productBarCode);
        $inventory_user = $data['data']['inventory_user'] ? $data['data']['inventory_user'] : '';
        if(isset($product_id)) {

            $sql = "INSERT INTO oc_product_sku_barcode_history (product_id,sku_barcode,new_sku_barcode,added_by,date_added,warehouse_id)
                SELECT product_id, sku_barcode, '".$productBarCode."', '".$inventory_user."', NOW() ,warehouse_id FROM oc_product_to_warehouse
                WHERE product_id = '".$product_id."' and warehouse_id = '".$warehouse_id."'";
            $query = $db->query($sql);


            $sql = "UPDATE oc_product_to_warehouse SET sku_barcode = '" . $productBarCode . "' WHERE product_id = '" . $product_id . "' and warehouse_id = '" . $warehouse_id . "'";

            $query = $db->query($sql);
            if($query){
                return $query;
            }else{
                return false;
            }

        }

    }


    //货架上货
    public  function confirmReturnShelves(array  $data ){
        global $db;
        $shelves_quantity = $data['data']['shelves_quantity'] ? $data['data']['shelves_quantity'] : '';
        $return_quantity = $data['data']['return_quantity'] ? $data['data']['return_quantity'] : '';

        $arr=explode('/',$return_quantity);
        $return_quantity=$arr[0];

        $product_id = $data['data']['product_id'] ? $data['data']['product_id'] : '';
        $return_set = $data['data']['return_set'] ? $data['data']['return_set'] : '';

        $add_user_name = $data['data']['add_user_name'] ? $data['data']['add_user_name'] : '';
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $in_part = $data['data']['in_part'] ? $data['data']['in_part'] : '';
        $box_size = $data['data']['box_size'] ? $data['data']['box_size'] : '';


        if($return_quantity < $shelves_quantity){
            return 3 ;
        }else{

            $sql = " insert  into  oc_return_confirm  (`return_id` ,`product_id`, `return_quantity`,`real_quantity`,  `in_part` ,`box_quantity` , `warehouse_id` ,`add_user` , `date_added` )
                                                  VALUES  ('".$return_set ."','".$product_id ."','".$return_quantity ."','".$shelves_quantity ."','".$in_part ."','".$box_size ."'
                                                  ,'".$warehouse_id ."','".$add_user_name ."',NOW())";


            $query = $db->query($sql);
            $return_confirm_id = $db->getLastId();

            if($return_confirm_id ){
                $sql = " update  oc_return_product  set  return_confirmed = 1 WHERE  product_id = '".$product_id."' and return_id in ($return_set)" ;
                $query1 = $db->query($sql);

            }
            if($query1 and $return_quantity >  $shelves_quantity){

                $sql =  "insert into  oc_x_stock_move (`return_confirm_id` ,`timestamp`,`station_id`,`inventory_type_id` ,`date_added` ,`added_by` ,`memo`,`warehouse_id`)
                                          VALUES  ('".$return_confirm_id."', unix_timestamp()  , 2 ,  '27', NOW() ,'". $add_user_name ."' , '上货人员修正' , '".$warehouse_id."'  )";
                $query = $db->query($sql);
                $hou_inventory_move_id = $db->getLastId();



                if($return_quantity > $shelves_quantity){

                    $quantity = $return_quantity  - $shelves_quantity ;
                    $quantity = $quantity* (-1);

                    $sql = " insert into oc_x_stock_move_item ( `inventory_move_id` ,`station_id` , `product_id` ,`quantity` , `box_quantity` )
                             VALUES ( '".$hou_inventory_move_id."' , '2' , '".$product_id."' ,   '".$quantity ."'  , '".$box_size."'   )";

                    $query = $db->query($sql);
                    $hou_inventory_move_item_id = $db->getLastId();

                    if($hou_inventory_move_item_id){
                        return 1;
                    }

                }

            }else{
                return 1;
            }
        }

    }


    // 移库操作
//    public function getTransferInfo(array  $data)
//    {
//        global $db;
//        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
//
//        $date = date("Y-m-d");
//        $date1 = date("Y-m-d", strtotime("-1 day"));
//
//        $product_id = '7347,
//                    7185,
//                    7250,
//                    7293,
//                    7206,
//                    7249,
//                    7261,
//                    7201,
//                    7203,
//                    7213,
//                    8621,
//                    7202,
//                    7353,
//                    7232,
//                    8793,
//                    7246,
//                    9192,
//                    7324,
//                    8569
//                    ';
//
//
//        $sql = " select ptw.product_id , p.name , ptw.stock_area_quantity ,  AA.order_quantity , ptw.stock_area ,ptw.safe_stock , ptw.safe_stock * 0.3 as warning_safe
//                from oc_product p
//                LEFT JOIN  oc_product_to_warehouse ptw  ON   p.product_id = ptw.product_id and ptw.warehouse_id = '" . $warehouse_id . "'
//                LEFT JOIN ( SELECT  op.product_id ,  sum(op.quantity) order_quantity  FROM   oc_order o  LEFT  JOIN   oc_order_product op ON  o.order_id   = op.order_id  WHERE  o.order_status_id in (2,5,8)  and date(o.date_added) between  '" . $date1 . "' and '" . $date . "'   and op.product_id in ($product_id)  GROUP  BY op.product_id     ) AA    ON  AA.product_id  = p.product_id  where ptw.warehouse_id = '" . $warehouse_id . "'  and p.product_id in ($product_id)   order by  ptw.stock_area_quantity ,warning_safe ";
//
//
//        $query = $db->query($sql);
//        $result = $query->rows;
//
//
//        $sql1 = "select  user_id , username  from oc_w_user WHERE   warehouse_id = '" . $warehouse_id . "'  and user_group_id = 26";
//        $query1 = $db->query($sql1);
//        $result1 = $query1->rows;
//
//        $result = array($result, $result1);
//
//        return $result;
//    }



    public function addTranserMission(array $data){

        global $db;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $product_id = $data['data']['product_id'] ? $data['data']['product_id'] : '';
        $add_user = $data['data']['add_user'] ? $data['data']['add_user'] : '';

        $transfer_quantity = $data['data']['transfer_quantity'] ? $data['data']['transfer_quantity'] : '';

        $sql = "select stock_area from  oc_product_to_warehouse ptw  WHERE   product_id = '" . $product_id . "'  and warehouse_id = '" . $warehouse_id . "' ";
        $query = $db->query($sql);
        $result = $query->row;


        if ($transfer_quantity == 0 ){
            $sql1 = "  insert  into oc_x_product_warehouse_transfer (`product_id` , `stock_area` ,`date_added`   ,`warehouse_id` ,`status`  )
                VALUES  ( '" . $product_id . "', '" . $result['stock_area'] . "' ,NOW(),'" . $warehouse_id . "','1' )";
            $query = $db->query($sql1);
            $result_id = $db->getLastId();

        }

        if($transfer_quantity > 0){
            $sql2 = "  insert  into oc_x_product_warehouse_transfer (`product_id` , `stock_area` ,`date_added` , `added_by`  , `quantity` ,`warehouse_id` ,`status`  )
                VALUES  ( '" . $product_id . "', '" . $result['stock_area'] . "' ,NOW(),'" . $add_user . "','" . $transfer_quantity . "','" . $warehouse_id . "', '2')";
            $query = $db->query($sql2);
            $result_id = $db->getLastId();
        }


        return 1;

    }



    public function addTranserMission1(array $data){

        global $db;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $product_id = $data['data']['product_id'] ? $data['data']['product_id'] : '';
        $add_user = $data['data']['add_user'] ? $data['data']['add_user'] : '';

        $transfer_quantity = $data['data']['transfer_quantity'] ? $data['data']['transfer_quantity'] : '';

        $sql = "select stock_area from  oc_product_to_warehouse ptw  WHERE   product_id = '" . $product_id . "'  and warehouse_id = '" . $warehouse_id . "' ";
        $query = $db->query($sql);
        $result = $query->row;


        if($transfer_quantity > 0){
            $sql2 = "  insert  into oc_x_product_warehouse_transfer (`product_id` , `stock_area` ,`date_added` , `added_by`  , `quantity` ,`warehouse_id`  ,`status` )
                VALUES  ( '" . $product_id . "', '" . $result['stock_area'] . "' ,NOW(),'" . $add_user . "','" . $transfer_quantity . "','" . $warehouse_id . "', '3')";
            $query = $db->query($sql2);
            $result_id = $db->getLastId();
        }

        if($result_id){

            $sql = "select  stock_area_quantity  from oc_product_transfer  WHERE  product_id = '".$product_id ."'  and warehouse_id = '".$warehouse_id ."' ";
            $query = $db->query($sql);
            $result = $query->row;
            $quantity = $result['stock_area_quantity'] +$transfer_quantity ;

            $sql = " update oc_product_transfer pt   set  stock_area_quantity = '".$quantity."' where product_id =  '".$product_id ."' and warehouse_id = '".$warehouse_id ."' ";
            $query = $db->query($sql);


            if($query){

                $sql = "select  *  from oc_product_transfer  WHERE  product_id = '".$product_id ."'  ";
                $query = $db->query($sql);
                $result = $query->row;

                $sql = " insert into  oc_product_transfer_history (`product_id` , `status` , `stock_area_quantity` , `safe_stock` , `storage_capacity_quantity`,`warehouse_id`, `date_added`, `added_by` )  VALUES  ( '" . $result['product_id'] . "' , '" . $result['status'] . "' , '" . $result['stock_area_quantity'] . "' ,'" . $result['safe_stock'] . "' ,'" . $result['storage_capacity_quantity'] . "' ,'" . $warehouse_id . "'  , NOW(),  '" . $add_user . "')";

                $query = $db->query($sql);
                $result = $query->row;
            }


        }

        return 1;

    }


    public function getTransferMission(array $data)
    {
        global $db;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $psize = $data['data']['psize'] ? $data['data']['psize'] : '';
        $start_row = $data['data']['start_row'] ? $data['data']['start_row'] : '';
        $transfer_id = $data['data']['transfer_id'] ? $data['data']['transfer_id'] : '';
        $date = date("Y-m-d");
        $date1 = date("Y-m-d", strtotime("-2 day"));

//
//        $sql = "select group_concat(pt.product_id)  product_id  from  oc_product_transfer  pt   WHERE  pt.warehouse_id = '". $warehouse_id."' and  pt.status = 1  " ;
//
//        $query = $db->query($sql);
//        $result = $query->row;

        $sql_info = "SELECT
                        pt.product_id,
                        p.name,
                        ss. name stock_area,
                        AA.op_quantity,
                        AA.ios_quantity,
                        pt.safe_quantity safe_stock,
                        pt.quantity stock_area_quantity,
                        pt.capacity storage_capacity_quantity,
                        ptw.sku_barcode,
                        ptw.stock_area
                    FROM
                        oc_x_stock_section_product  pt
                    LEFT JOIN (
                        SELECT
                            op.product_id,
                            sum(op.quantity) op_quantity,
                            sum(ios.quantity) ios_quantity
                        FROM
                            oc_order o
                        LEFT JOIN oc_order_product op ON o.order_id = op.order_id
                        LEFT JOIN oc_x_inventory_order_sorting ios ON op.order_id = ios.order_id and op.product_id =ios.product_id
                        AND ios.move_flag = 0
                        WHERE
                            DATE(o.date_added) =  '".$date."'
                        AND o.warehouse_id = '".$warehouse_id."' and o.order_status_id !=3 
                        GROUP BY
                            op.product_id
                    ) AA ON AA.product_id = pt.product_id
                   LEFT JOIN oc_product p ON p.product_id = pt.product_id 
                   LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id  and ptw.warehouse_id = '".$warehouse_id."'
                left join  oc_x_stock_section ss on pt.stock_section_id = ss.stock_section_id 
                     WHERE
                        pt.warehouse_id ='".$warehouse_id."' and pt.status = 1 and pt.stock_section_type_id = 1 
                    and pt.safe_quantity > pt.quantity
                    order by pt.product_id
";
        if($start_row && $psize){
            $sql_info   .=  " limit  ".$start_row." , 20 " ;
        }else {
            $sql_info .= " limit 0, 20  " ;
        }


        $query = $db->query($sql_info);
        $result = $query->rows;

        return $result;


    }




    public function changeTransferValuse(array $data)
    {
        global $db;
        $transfer_move_id = $data['data']['transfer_move_id'] ? $data['data']['transfer_move_id'] : '';
        $status = $data['data']['status'] ? $data['data']['status'] : '';
        if ($status == 1) {
            $sql = " update oc_x_product_warehouse_transfer set  status  = 2 ,date_start = NOW() WHERE  transfer_move_id = '" . $transfer_move_id . "' ";

            $query = $db->query($sql);

        }
        if ($status == 2) {
            $sql = " update oc_x_product_warehouse_transfer set  status  = 3 , date_end = NOW()  WHERE  transfer_move_id = '" . $transfer_move_id . "' ";
            $query = $db->query($sql);
        }
        if ($query) {
            return 1;
        } else {
            return 2;
        }
    }


    public  function  getTransfer(array $data){
        global $db;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $sku_id = $data['data']['product_id'] ? $data['data']['product_id'] : '';
        $sku = isset($sku_id) ? $sku_id : false;
        if (!$sku) {
            return false;
        }

        if (strlen($sku) > 6) {
            $sql = " select  ptw.product_id, p.name , ptw.product_id transfer_id  , ptw.stock_area , ptw.sku_barcode , pt.safe_stock , pt.stock_area_quantity , pt.storage_capacity_quantity , if(pt.status = 1 , '启用' , '停用') status
                    from oc_product_transfer  pt
                    LEFT JOIN  oc_product p ON  pt.product_id = p.product_id
                    LEFT JOIN  oc_product_to_warehouse ptw ON  pt.product_id = ptw.product_id  and pt.warehouse_id = '".$warehouse_id."'
                     WHERE ptw.sku_barcode = '" . $sku . "' and ptw.warehouse_id = '" . $warehouse_id . "' and pt.status = 1";

        } elseif (is_numeric($sku) && strlen($sku) <= 6) {
            $sql = " select  ptw.product_id, p.name , ptw.product_id transfer_id  , ptw.stock_area , ptw.sku_barcode , pt.safe_stock , pt.stock_area_quantity , pt.storage_capacity_quantity , if(pt.status = 1 , '启用' , '停用') status
                    from oc_product_transfer  pt
                    LEFT JOIN  oc_product p ON  pt.product_id = p.product_id
                    LEFT JOIN  oc_product_to_warehouse ptw ON  pt.product_id = ptw.product_id  and pt.warehouse_id = '".$warehouse_id."'
                     WHERE ptw.product_id = '" . $sku . "' and ptw.warehouse_id = '" . $warehouse_id . "' and pt.status = 1 ";

        }

        $query = $db->query($sql);
        $result = $query->rows;
        return $result;


    }



    public function getTransferProductInfo(array $data)
    {
        global $db;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $sku_id = $data['data']['sku_id'] ? $data['data']['sku_id'] : '';
        $warehouse_section_id = $data['data']['warehouse_section_id'] ? $data['data']['warehouse_section_id'] : '';
        $inventory_user_id = $data['data']['inventory_user_id'] ? $data['data']['inventory_user_id'] : '';
        $sku = isset($sku_id) ? $sku_id : false;
        if (!$sku) {
            return false;
        }
        if (strlen($sku) > 6) {

            $sql = " select  CONCAT(ptw.product_id,'/', p.name) product_id , ptw.product_id transfer_id  , ptw.stock_area , ptw.sku_barcode , ssp.safe_quantity , ssp.quantity , ssp.capacity , if(ssp.status = 1 , '启用' , '停用') status
              from  oc_product_to_warehouse ptw
              LEFT JOIN  oc_product p ON  ptw.product_id  = p.product_id
              LEFT JOIN  oc_x_stock_section_product ssp  ON  ptw.product_id = ssp.product_id and ssp.warehouse_id = '" . $warehouse_id . "'and ssp.stock_section_type_id = '".$warehouse_section_id."'
             WHERE ptw.sku_barcode = '" . $sku . "' and ptw.warehouse_id = '" . $warehouse_id . "'    ";

        } elseif (is_numeric($sku) && strlen($sku) <= 6) {

            $sql = "select stock_section_id  from  oc_x_stock_section_product_move_inventory where warehouse_id = '".$warehouse_id ."' and  product_id = '".$sku_id ."' and added_by = '".$inventory_user_id ."' ";

            $query = $db->query($sql);
            $result = $query->row;
            if($result['stock_section_id'] > 0){
                $sql = " select  CONCAT(ptw.product_id,'/', p.name) product_id, ptw.product_id transfer_id  ,ptw.stock_area ,  ptw.sku_barcode , ssp.safe_quantity , ssp.quantity , ssp.capacity , if(ssp.status = 1 , '启用' , '停用') status , if(pmh.quantity = 0 , 0 ,  pmh.quantity ) inventory_quantity  , if(pmh.name = 0 , 0 ,  pmh.name ) name  
              from  oc_product_to_warehouse ptw
              LEFT JOIN  oc_product p ON  ptw.product_id  = p.product_id
               LEFT JOIN  oc_x_stock_section_product ssp  ON  ptw.product_id = ssp.product_id and ssp.warehouse_id = '" . $warehouse_id . "'and ssp.stock_section_type_id = '".$warehouse_section_id."' 
               left join oc_x_stock_section_product_move_inventory pmh on pmh.product_id = ssp.product_id  and pmh.stock_section_id   = '".$result['stock_section_id']."'   
             WHERE ptw.product_id = '" . $sku . "' and ptw.warehouse_id = '" . $warehouse_id . "'    ";


            }else{
                $sql = " select  CONCAT(ptw.product_id,'/', p.name) product_id, ptw.product_id transfer_id  ,ptw.stock_area ,  ptw.sku_barcode , ssp.safe_quantity , ssp.quantity , ssp.capacity , if(ssp.status = 1 , '启用' , '停用') status ,if(pmh.quantity = 0 , 0 ,  pmh.quantity ) inventory_quantity  , if(pmh.name = 0 , 0 ,  pmh.name ) name  
              from  oc_product_to_warehouse ptw
              LEFT JOIN  oc_product p ON  ptw.product_id  = p.product_id
              LEFT JOIN  oc_x_stock_section_product ssp  ON  ptw.product_id = ssp.product_id and ssp.warehouse_id = '" . $warehouse_id . "'and ssp.stock_section_type_id = '".$warehouse_section_id."'
                left join oc_x_stock_section_product_move_inventory pmh on pmh.product_id = ssp.product_id  and pmh.stock_section_id   = '".$result['stock_section_id']."' 
             WHERE ptw.product_id = '" . $sku . "' and ptw.warehouse_id = '" . $warehouse_id . "'    ";

            }



        }

        $query = $db->query($sql);
        $result = $query->row;
        return $result;
    }

    public function addChangeProductTransfer(array $data)
    {
        global $db;

        $product_id_transfer = $data['data']['product_id_transfer'] ? $data['data']['product_id_transfer'] : '';
        $input_stock_area_quantity = $data['data']['input_stock_area_quantity'] ? $data['data']['input_stock_area_quantity'] : '';
        $input_safe_area_quantity = $data['data']['input_safe_area_quantity'] ? $data['data']['input_safe_area_quantity'] : '';
        $input_capacity_quantity = $data['data']['input_capacity_quantity'] ? $data['data']['input_capacity_quantity'] : '';
        $stock_area = $data['data']['stock_area'] ? $data['data']['stock_area'] : '';
        $inventory_user_id = $data['data']['inventory_user_id'] ? $data['data']['inventory_user_id'] : '';
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $warehouse_section_id = $data['data']['warehouse_section_id'] ? $data['data']['warehouse_section_id'] : '';


        if (!$product_id_transfer) {
            return false;
        }


        $sql = "select stock_section_id ,  product_id , status   from oc_x_stock_section_product  WHERE  product_id = '" . $product_id_transfer . "' and  warehouse_id = '" . $warehouse_id . "' and stock_section_type_id = '". $warehouse_section_id ."'";

        $query = $db->query($sql);
        $result = $query->row;

        if ($result['product_id'] > 0 ) {
            $sql = " update  oc_x_stock_section_product
                  set safe_quantity = '" . $input_safe_area_quantity . "'  ,  capacity = '" . $input_capacity_quantity . "' where   warehouse_id =  '" . $warehouse_id . "' and product_id = '" . $product_id_transfer . "' and stock_section_type_id = '".$warehouse_section_id."' ";

            $query = $db->query($sql);
            if ($query) {
                $sql = " insert into  oc_product_transfer_history (`product_id` , `status` , `stock_area_quantity` , `safe_stock` , `storage_capacity_quantity`,`warehouse_id`, `date_added`, `added_by` )  VALUES  ( '" . $result['product_id'] . "' , '" . $result['status'] . "' , '" . $input_stock_area_quantity . "' ,'" . $input_safe_area_quantity . "' ,'" . $input_capacity_quantity . "' ,'" . $warehouse_id . "'  , NOW(),  '" . $inventory_user_id . "')";

                $query = $db->query($sql);
                $result = $query->row;

            }

        } else {

            $sql = " select  stock_section_id  from oc_x_stock_section WHERE  name  = '".$stock_area."' ";

            $query = $db->query($sql);
            $result = $query->row;
            if($result['stock_section_id'] > 0 ){
                $sql = "insert into oc_x_stock_section_product ( `stock_section_id`,`product_id` ,`warehouse_id` ,  `safe_quantity`, `quantity`,`capacity`,`date_added` , `stock_section_type_id`) VALUES  ('".$result['stock_section_id']."','" . $product_id_transfer . "','" . $warehouse_id . "', '" . $input_safe_area_quantity . "', '" . $input_stock_area_quantity . "', '" . $input_capacity_quantity . "', NOW()  , '".$warehouse_section_id."'  )";

                $query = $db->query($sql);
            }else{

                $sql = " insert into oc_x_stock_section (`warehouse_id` , `name` , `stock_section_type_id` , `date_added` , `added_by`) VALUES  
 ('".$warehouse_id."' , '".$stock_area."' , '".$warehouse_section_id."'   , NOW(), '".$inventory_user_id."')";

                $query = $db->query($sql);
                $return_id = $db->getLastId();
                if($query){
                    $sql = "insert into oc_x_stock_section_product ( `stock_section_id`,`product_id` ,`warehouse_id` ,  `safe_quantity`, `quantity`,`capacity`,`date_added` ,`stock_section_type_id`) VALUES  ('".$return_id."','" . $product_id_transfer . "','" . $warehouse_id . "', '" . $input_safe_area_quantity . "', '" . $input_stock_area_quantity . "', '" . $input_capacity_quantity . "', NOW(),  '".$warehouse_section_id."'  )";

                    $query = $db->query($sql);
                }

            }




        }
        return 1;
    }

    public function ChangeProductTransferStatus(array  $data)
    {
        global $db;
        $product_id_transfer = $data['data']['product_id_transfer'] ? $data['data']['product_id_transfer'] : '';
        $inventory_user_id = $data['data']['inventory_user_id'] ? $data['data']['inventory_user_id'] : '';
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $warehouse_section_id = $data['data']['warehouse_section_id'] ? $data['data']['warehouse_section_id'] : '';
        if (!$product_id_transfer) {
            return false;
        }

        $sql = "select  product_id , status ,quantity ,   safe_quantity  ,capacity  from oc_x_stock_section_product  WHERE  product_id = '" . $product_id_transfer . "' and  warehouse_id = '" . $warehouse_id . "' and  stock_section_type_id = '".$warehouse_section_id."' ";

        $query = $db->query($sql);
        $result = $query->row;

        if ($result['product_id']) {
            if ($result['status'] == 1) {
                $sql = " update  oc_x_stock_section_product
                  set status =0  where   warehouse_id =  '" . $warehouse_id . "' and product_id = '" . $result['product_id'] . "' and  stock_section_type_id = '".$warehouse_section_id."' ";

                $query = $db->query($sql);
                if ($query) {
                    $sql = " insert into  oc_product_transfer_history (`product_id` , `status` , `stock_area_quantity` , `safe_stock` , `storage_capacity_quantity`,`warehouse_id`, `date_added`, `added_by` )  VALUES  ( '" . $result['product_id'] . "' , 0 , '" . $result['stock_area_quantity'] . "' ,'" . $result['safe_stock'] . "' ,'" . $result['storage_capacity_quantity'] . "' ,'" . $warehouse_id . "'  , NOW(),  '" . $inventory_user_id . "')";

                    $query = $db->query($sql);
                    $result = $query->row;

                }
            } elseif ($result['status'] == 0) {

                $sql = " update  oc_x_stock_section_product
                  set status =1  where   warehouse_id =  '" . $warehouse_id . "' and product_id = '" . $result['product_id'] . "' and  stock_section_type_id = '".$warehouse_section_id."' ";

                $query = $db->query($sql);
                if ($query) {
                    $sql = " insert into  oc_product_transfer_history (`product_id` , `status` , `stock_area_quantity` , `safe_stock` , `storage_capacity_quantity`,`warehouse_id`, `date_added`, `added_by` )  VALUES  ( '" . $result['product_id'] . "' , 1 , '" . $result['stock_area_quantity'] . "' ,'" . $result['safe_stock'] . "' ,'" . $result['storage_capacity_quantity'] . "' ,'" . $warehouse_id . "'  , NOW(),  '" . $inventory_user_id . "')";

                    $query = $db->query($sql);
                    $result = $query->row;

                }

            }

        }
        return 1;
    }



    //盘点
    public  function confirmCheckSingleProduct(array $data){

        global $db;

        $stock_check_id = $data['data']['stock_check_id'] ? $data['data']['stock_check_id'] : '';

        $sql = "update  oc_x_stock_checks  set  status = 1 , date_confirmed = NOW()  WHERE  stock_check_id = '".$stock_check_id."' ";
        $query = $db->query($sql);
        if($query){
            return  1 ;

        }else{
            return 2 ;
        }

    }

    public  function  changeCheckSingleProduct(array  $data){
        global $db;

        $stock_check_id = $data['data']['stock_check_id'] ? $data['data']['stock_check_id'] : '';

        $sql = "select  quantity  from  oc_x_stock_checks WHERE stock_check_id = '".$stock_check_id."'  ";
        $query = $db->query($sql);
        $result = $query->row;

        $sql = "update  oc_x_stock_checks  set  status = 2 , date_confirmed = NOW()  WHERE  stock_check_id = '".$stock_check_id."' ";
        $query = $db->query($sql);
        if($query){
            return  $result['quantity'] ;

        }else{
            return false ;
        }
    }


    //回收篮筐

    public  function  getOrderByFrame(array  $data){
        global $db;
        $logistic_allot_id = $data['data']['logistic_allot_id'] ? $data['data']['logistic_allot_id'] : '';

        $sql = "  SELECT   cm.container_id , sum(cm.move_type) move_type  , cm.order_id , la.logistic_driver_title
                  FROM oc_x_container_fast_move cm
                LEFT JOIN oc_x_logistic_allot_order lao ON   cm.order_id = lao.order_id
                LEFT JOIN oc_x_logistic_allot la ON la.logistic_allot_id = lao.logistic_allot_id  WHERE la.logistic_allot_id='". $logistic_allot_id."'
                group by cm.container_id order by cm.order_id  ";

        $query = $db->query($sql);
        $result = $query->rows;

        return $result;
    }

    public  function  submitFrameInStatus(array  $data ){
        global $db;

        $check_value = $data['data']['check_value'] ? $data['data']['check_value'] : '';
        $user_id = $data['data']['user_id']? $data['data']['user_id'] : '';
        $logistic_id = $data['data']['logistic_id']? $data['data']['logistic_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';

        if($check_value){

            $string=implode(',',$check_value);
            $sql = "update   oc_x_container  set  instore =  1  WHERE  container_id in ( ".$string.")   and  warehouse_id = '".$warehouse_id."' ";

            $query = $db->query($sql);

            foreach ($check_value as $value){

                $sql = " SELECT
                              cm.order_id  , cm.customer_id
                           FROM   oc_x_logistic_allot  la
                           LEFT  JOIN     oc_x_logistic_allot_order lao  ON  la.logistic_allot_id = lao.logistic_allot_id
                           LEFT  JOIN  oc_x_container_fast_move cm on  cm.order_id = lao.order_id
                           WHERE la.logistic_allot_id = '" . $logistic_id."' and cm.container_id = '".$value."' ";

                $query = $db->query($sql);
                $result = $query->row;

                $sql = " insert  into  oc_x_container_fast_move  (`container_id` , `customer_id` ,`order_id` , `move_type` ,`date_added` , `add_w_user_id`)
                          VALUES  ('".$value."' , '".$result['customer_id']."','".$result['order_id']."' , '-1' , NOW() , '".$user_id."' )  ";
                $query = $db->query($sql);


            }

            return $result['status'] =2;
        }else{
            return $result['status'] =3;
        }
    }

    public  function  confirmFrameInStatus(array $data ){
        global $db;

        $container_id = $data['data']['container_id'] ? $data['data']['container_id'] : '';
        $user_id = $data['data']['user_id']? $data['data']['user_id'] : '';
        $logistic_allot_id = $data['data']['logistic_allot_id']? $data['data']['logistic_allot_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $sql = "update   oc_x_container  set  instore =  1  WHERE  container_id in ( ".$container_id.")   and  warehouse_id = '".$warehouse_id."' ";

        $query = $db->query($sql);
        if($query){
            $sql = " SELECT
                              cm.order_id  , cm.customer_id
                           FROM   oc_x_logistic_allot  la
                           LEFT  JOIN     oc_x_logistic_allot_order lao  ON  la.logistic_allot_id = lao.logistic_allot_id
                           LEFT  JOIN  oc_x_container_fast_move cm on  cm.order_id = lao.order_id
                           WHERE la.logistic_allot_id = '" . $logistic_allot_id."' and cm.container_id = '".$container_id."' ";

            $query = $db->query($sql);
            $result = $query->row;


            $sql = " insert  into  oc_x_container_fast_move  (`container_id` , `customer_id` ,`order_id` , `move_type` ,`date_added` , `add_w_user_id`)
                          VALUES  ('".$container_id."' , '".$result['customer_id']."','".$result['order_id']."' , '-1' , NOW() , '".$user_id."' )  ";
            $query = $db->query($sql);

        }

        return $result['status'] = 1;

    }

    //分区分拣
    public  function  getOrderSpareSortingUser(array $data){
        global $db;
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $warehouse_repack = $data['data']['warehouse_repack']? $data['data']['warehouse_repack'] : '';
        $user_repack = $data['data']['user_repack']? $data['data']['user_repack'] : '';
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';

        $sql = " select order_id , inventory_name  from oc_order_distr_spare  where order_id = '". $order_id ."'  and  warehouse_id = '".$warehouse_id ."' and  warehouse_repack = '".$warehouse_repack ."'  and user_repack = '". $user_repack ."' ";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['inventory_name']){
            return $result['inventory_name'];
        }else {
            return 2 ;
        }
    }

    public  function insertOrderDistrSpare(array $data){
        global $db;
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $warehouse_repack = $data['data']['warehouse_repack']? $data['data']['warehouse_repack'] : '';
        $user_repack = $data['data']['user_repack']? $data['data']['user_repack'] : '';
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $inventoryuser = $data['data']['inventoryuser']? $data['data']['inventoryuser'] : '';

        $sql="INSERT INTO oc_order_distr_spare (order_id ,warehouse_id,warehouse_repack , user_repack ,inventory_name)VALUES ('". $order_id."','". $warehouse_id."','". $warehouse_repack."','". $user_repack."', '". $inventoryuser."')";

        $query = $db->query($sql);

        if($query){
            return 1 ;
        }else{
            return 2 ;
        }

    }
    //根据周转筐获取订单号
    public  function  getOrderInfoByCount(array  $data ){
        global $db;
        $count_id = $data['data']['count_id']? $data['data']['count_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';

        $sql = "select od.inventory_name , oi.order_id , oi.inv_comment ,oi.frame_vg_list ,oi.status ,os.name  from  oc_order o LEFT  JOIN  oc_order_inv  oi   on  o.order_id = oi.order_id  LEFT  JOIN  oc_order_status  os on  os.order_status_id = o.order_status_id  LEFT JOIN  oc_order_distr od on od.order_id = o.order_id  where (oi.frame_vg_list like '%,$count_id%' or  oi.frame_vg_list in ($count_id)  ) and o.warehouse_id = '" .$warehouse_id  ."' and o.order_status_id !=3    and  o.order_deliver_status_id = 1   order by oi.order_inv_id  desc  limit 1  ";

        $query = $db->query($sql);
        $result = $query->rows;
        return $result ;


    }


    public  function getOrderInfoBySpareComment(array $data){
        global  $db ;
        $inv_spare_comment = $data['data']['inv_spare_comment']? $data['data']['inv_spare_comment'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';

        $sql = " select  oi.order_id , oi.inv_comment , os.name ,oi.frame_vg_list  ,sum(if(p.repack = 0 ,op.quantity , 0)) quantity_zheng, sum(if(p.repack = 1 ,op.quantity , 0)) quantity_san  from  oc_order_inv  oi  LEFT  JOIN    oc_order o  on   oi.order_id =  o.order_id  LEFT  JOIN  oc_order_product op  on   o.order_id = op.order_id  left join  oc_product p on op.product_id = p.product_id  LEFT  join oc_order_status os on   o.order_status_id = os.order_status_id    where  o.warehouse_id = '".$warehouse_id."' and oi.inv_spare_comment = '".$inv_spare_comment."' and oi.status = 0  and o.order_status_id in (6,8,12) and o.order_deliver_status_id = 1  group by oi.order_id ";

        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }

    public  function  confirmOrderInfoByCount(array $data){
        global  $db ;
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $inventory_user = $data['data']['inventory_user']? $data['data']['inventory_user'] : '';



        $sql  = "insert into oc_x_consolidated_order (`order_id` , `warehouse_id` , `uptime` , `added_by` ) VALUES ( '".$order_id."','".$warehouse_id."',NOW(),'".$inventory_user."')" ;
        $query  = $db->query($sql);

        $sql1 = " select inventory_move_id   from  oc_x_stock_move where  order_id = '".$order_id ."'  ";
        $query1 = $db->query($sql1);
        $result = $query1->row;

        if($result['inventory_move_id']){
            if($query){
                $sql = " update  oc_order_inv set status = 1  WHERE  order_id = '".$order_id."'";
                $query  = $db->query($sql);

                $sql = " update oc_order set order_status_id = 6 WHERE  order_id = '".$order_id."'";
                $query  = $db->query($sql);
                return  1 ;
            }
        }
        else{
            $sql = " update  oc_order_inv set status = 1  WHERE  order_id = '".$order_id."'";
            $query  = $db->query($sql);

            return  1 ;
        }



    }

    public  function addInvComment(array $data){
        global  $db ;
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $invComment = $data['data']['invComment']? $data['data']['invComment'] : '';
        $sql = "update  oc_order_inv  set inv_comment = '".$invComment ."' WHERE  order_id = '".$order_id ."' ";
        $query  = $db->query($sql);
        if($query){
            return 1 ;
        }else {
            return 2 ;
        }
    }

    public function  updateStocksChecks(array $data){
        global  $db ;
        $pallet_number = $data['data']['pallet_number']? $data['data']['pallet_number'] : '';
        $searchPalletNumber = $data['data']['searchPalletNumber']? $data['data']['searchPalletNumber'] : '';

        $sql = " select  status from oc_x_stock_checks  where pallet_number = '".$searchPalletNumber."'  " ;

        $query = $db->query($sql);
        $result = $query->row;
        if($result['status'] == 1 ){
            return 3 ;
        }else {
            $sql = " update oc_x_stock_checks  set status = 1 , plate_number = '".$pallet_number."'  WHERE  pallet_number = '".$searchPalletNumber."'";
            $query  = $db->query($sql);
            if($query){
                return 1 ;
            }else {
                return 2 ;
            }
        }


    }

    public  function  getStockChecks(array $data ){
        global $db;
        global $log;


        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $add_user_id = $data['data']['add_user_name']? $data['data']['add_user_name'] : '';
        $pallet_number = $data['data']['pallet_number']? $data['data']['pallet_number'] : '';
        $date = $data['data']['searchDate']? $data['data']['searchDate'] : '';

        $sql = "select ss.name  pallet_number , w.username  , ss.date_added  from  oc_x_stock_section  ss LEFT  JOIN  oc_w_user w on  ss.added_by = w.user_id   where  ss.warehouse_id = '".$warehouse_id ."' ";

        if($pallet_number !=''){
            $sql .= "  and ss.name   = '". $pallet_number ."' ";
        }


        $query = $db->query($sql);
        $result = $query->rows;

        return $result;
    }


    public function  deleteStockChecks(array $data ){
        global $db;
        $pallet_number = $data['data']['stock_check_id']? $data['data']['stock_check_id'] : '';
        $sql = " update  oc_x_stock_checks  set status = 3 WHERE  stock_check_id = '$pallet_number' and status = 0 ";
        $query = $db->query($sql);


        $sql = " select stock_check_id ,  status , pallet_number  from  oc_x_stock_checks  WHERE   stock_check_id = '$pallet_number'";


        $query = $db->query($sql);
        $result = $query->row;
        if($result['status'] == 3 ){
            $sql = " delete  from oc_x_stock_checks_inventory where pallet_number  = '".$result['pallet_number']."'";
            $query = $db->query($sql);
            $sql = " delete  from oc_x_stock_checks where stock_check_id  = '".$result['stock_check_id']."'";
            $query = $db->query($sql);
            $sql = " delete  from oc_x_stock_checks_item  where stock_check_id  = '".$result['stock_check_id']."'";
            $query = $db->query($sql);
            return 1 ;
        }else {
            return 2 ;
        }
    }

    public  function  getWarehouseSection(){
        global $db ;

        $sql = "select warehouse_section_id , code ,name   from oc_x_warehouse_section  WHERE  status = 1 ";
        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }

    public function  updateStockChecks($data ){
        global $db;
        global $log;

        $data_inv = json_decode($data, 2);
        $warehouse_id = $data['data']['warehouse_id'];

        $pallet_number = $data['data']['pallet_number'];

        $date = $data_inv['date'];
        $sql = " select  stock_section_id  from  oc_x_stock_section where name = '".$pallet_number ."' ";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['stock_section_id'] >0){
            return 1 ;
        }else{
            $sql  = "SELECT
	sci.stock_check_id,
	sci.product_id,
	sci.quantity,
	p.name,
	cd.name ca_name ,  
	p.price,
	sci.status,
	ptw.sku_barcode,
	sci.pallet_number,
	sci.status ,
	ws.name section_name ,
	ws.warehouse_section_id
FROM
	oc_x_stock_checks_inventory sci

LEFT JOIN oc_product p ON sci.product_id = p.product_id
LEFT join oc_x_sku s on s.sku_id = p.sku_id 
LEFT join oc_x_sku_category scc on scc.sku_category_id = s.sku_category_id
LEFT join  oc_x_warehouse_section ws on  scc.warehouse_section_id = ws.warehouse_section_id 
LEFT JOIN oc_product_to_category ptc ON ptc.product_id = p.product_id
LEFT JOIN oc_category c ON c.category_id = ptc.category_id
LEFT JOIN oc_category_description cd ON cd.category_id = c.category_id
LEFT JOIN oc_product_to_warehouse ptw ON sci.product_id = ptw.product_id and  ptw.warehouse_id = '".$warehouse_id."'  WHERE sci.warehouse_id = '".$warehouse_id."' " ;

            if($pallet_number !=''){
                $sql .= " and sci.pallet_number = '".$pallet_number ."' ";
            }
            $sql .= " order by sci.stock_check_id ";

            $query = $db->query($sql);
            $result = $query->rows;

            return $result;
        }


    }

    public  function  addStockInventory(array $data){
        global $db;
        $product_id = $data['data']['product_id']? $data['data']['product_id'] : '';
        $add_user = $data['data']['add_user']? $data['data']['add_user'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $pallet_number = $data['data']['pallet_number']? $data['data']['pallet_number'] : '';
        $warehouse_section_id = $data['data']['warehouse_section_id']? $data['data']['warehouse_section_id'] : '';
        $product_num = $data['data']['product_num']? $data['data']['product_num'] : '';

        $sql = " select stock_check_id  from  oc_x_stock_checks_inventory  WHERE   pallet_number = '".$pallet_number."' and product_id = '".$product_id."' ";

        $query = $db->query($sql);
        $result = $query->row;
        if($result['stock_check_id'] >0 ){
            $sql = " update oc_x_stock_checks_inventory  set  quantity = '".$product_num."'  where pallet_number = '".$pallet_number."' and product_id = '".$product_id."' ";
            $query = $db->query($sql);
        }else {
            $sql = " insert into oc_x_stock_checks_inventory (`date_added` ,`added_by` ,`warehouse_id`,`pallet_number` ,`stock_check_category`,`product_id` ,`quantity`) VALUES  (NOW() ,'" . $add_user . "','" . $warehouse_id . "' , '" . $pallet_number . "','".$warehouse_section_id."' ,'".$product_id."' ,'".$product_num."')";

            $query = $db->query($sql);
        }


        if($query){
            return 1 ;

        }else{
            return 2 ;
        }

    }

    public function getStockChecksMove(array $data){
        global  $db ;
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $pallet_number = $data['data']['pallet_number']? $data['data']['pallet_number'] : '';
        $sql = " select sc.pallet_number , ws.name , w.username ,sc.status ,ws.code from oc_x_stock_checks sc LEFT join oc_x_warehouse_section ws on sc.stock_check_category = ws.warehouse_section_id LEFT  JOIN oc_w_user w on sc.added_by = w.user_id  where sc.pallet_number = '".$pallet_number."'   ";

        $query = $db->query($sql);

        $result = $query->rows;

        return $result;
    }

    public  function addStockMove(array  $data){
        global $db ;
        $driver_number = $data['data']['driver_number']? $data['data']['driver_number'] : '';

        $pallet_number = $data['data']['pallet_number']? $data['data']['pallet_number'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $add_user = $data['data']['add_user']? $data['data']['add_user'] : '';

        $sql = "select transfer_driver_id  from  oc_x_transfer_driver WHERE  name = '". $driver_number ."'";
        $query = $db->query($sql);
        $result = $query->row;

        if($result['transfer_driver_id'] > 0 ){
            return 5 ;
        }else {


            $sql = "select stock_check_id ,  status ,pallet_number from  oc_x_stock_checks  where pallet_number   =  '" . $pallet_number . "'";

            $query = $db->query($sql);
            $result = $query->row;
            if ($result['status'] == 1) {
                return 1;
            } else if ($result['status'] == 0) {
                $sql = "select  * from  oc_x_stock_checks_item WHERE  stock_check_id = '" . $result['stock_check_id'] . "'";
                $query = $db->query($sql);
                $results = $query->rows;

                $sql = "select relevant_id  from  oc_x_stock_move where  relevant_id  = '" . $result['pallet_number'] . "' and inventory_type_id = 22 ";
                $query = $db->query($sql);
                $result_id = $query->row;
                if ($result_id['relevant_id'] > 0) {
                    return 3;
                } else {
                    $sql = "insert  into oc_x_stock_move
              (`relevant_id`,`station_id`,`timestamp`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id` ,`memo` )
              VALUES ('" . $result['pallet_number'] . "' ,'2',UNIX_TIMESTAMP(NOW()), '22' ,NOW(),'" . $add_user . "' ,'" . $warehouse_id . "','搬仓出库')";


                    $query = $db->query($sql);

                    $inventory_move_id = $db->getLastId();

                    if (!empty($results)) {
                        $sql = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `product_id`, `quantity`, `box_quantity`, `status`) VALUES ";
                        $m = 0;
                        foreach ($results as $product) {
                            $inventory_op = -1;
                            $sql .= "(" . $inventory_move_id . ", " . 2 . ",  '" . $product['product_id'] . "', '" . $product['quantity'] * $inventory_op . "', 1 , 1 )";

                            if (++$m < sizeof($results)) {
                                $sql .= ', ';
                            } else {
                                $sql .= ';';
                            }
                        }

                        $query = $db->query($sql);

                    }
                    if ($query) {

                        $sql = " update oc_x_stock_checks set  status = 1  , plate_number = '" . $driver_number . "'  , date_confirmed = NOW() where  pallet_number = '" . $pallet_number . "'";

                        $query = $db->query($sql);
                        return 2;
                    }
                }
            }
        }

    }

    public  function getStockChecksIn(array $data ){
        global $db ;
        $driver_number = $data['data']['driver_number']? $data['data']['driver_number'] : '';
        $sql = " select sc.pallet_number , ws.name , w.username ,sc.status from oc_x_stock_checks sc LEFT join oc_x_warehouse_section ws on sc.stock_check_category = ws.warehouse_section_id LEFT  JOIN oc_w_user w on sc.added_by = w.user_id  where sc.plate_number = '".$driver_number."'   ";

        $query = $db->query($sql);

        $result = $query->rows;

        return $result;
    }

    public function delectStockMOve(array $data){
        global $db ;
        $pallet_number = $data['data']['pallet_number']? $data['data']['pallet_number'] : '';
        $sql = "update oc_x_stock_checks  set   status = 0 , plate_number = '' , date_confirmed = ''  where pallet_number = '" .$pallet_number ."'";
        $query = $db->query($sql);
        if($query){
            $sql = "select relevant_id  ,inventory_move_id from  oc_x_stock_move where  relevant_id  = '".$pallet_number."' and inventory_type_id = 22 ";
            $query = $db->query($sql);
            $result_id = $query->row;

            if($result_id['relevant_id'] > 0 ){
                $sql = "delete  from  oc_x_stock_move WHERE  inventory_move_id = '".$result_id['inventory_move_id']."'";
                $query = $db->query($sql);
                $sql = "delete  from  oc_x_stock_move_item WHERE  inventory_move_id = '".$result_id['inventory_move_id']."'";
                $query = $db->query($sql);
            }

            if($query){
                return 1 ;
            }else{
                return 2 ;
            }
        }


    }

    public  function addStockIn(array $data ){
        global $db ;
        $driver_number = $data['data']['driver_number']? $data['data']['driver_number'] : '';

        $pallet_number = $data['data']['pallet_number']? $data['data']['pallet_number'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $add_user = $data['data']['add_user']? $data['data']['add_user'] : '';
        $sql = "select stock_check_id ,  status ,pallet_number from  oc_x_stock_checks  where pallet_number   =  '". $pallet_number ."'";

        $query = $db->query($sql);
        $result = $query->row;
        if($result['status'] == 2 || $result['status'] == 0){
            return 1 ;
        }else if($result['status'] == 1 ){
            $sql = "select  * from  oc_x_stock_checks_item WHERE  stock_check_id = '". $result['stock_check_id'] ."'";
            $query = $db->query($sql);
            $results = $query->rows;

            $sql = "select relevant_id  from  oc_x_stock_move where  relevant_id  = '".$result['pallet_number']."' and inventory_type_id = 23 ";
            $query = $db->query($sql);
            $result_id = $query->row;
            if($result_id['relevant_id'] > 0 ){
                return 3 ;
            }else {
                $sql = "insert  into oc_x_stock_move
              (`relevant_id`,`station_id`,`timestamp`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id` ,`memo` )
              VALUES ('" . $result['pallet_number'] . "' ,'2',UNIX_TIMESTAMP(NOW()), '23' ,NOW(),'" . $add_user . "' ,'" . $warehouse_id . "','搬仓入库')";


                $query = $db->query($sql);

                $inventory_move_id = $db->getLastId();

                if (!empty($results)) {
                    $sql = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `product_id`, `quantity`, `box_quantity`, `status`) VALUES ";
                    $m = 0;
                    foreach ($results as $product) {
                        $inventory_op = -1;
                        $sql .= "(" . $inventory_move_id . ", " . 2 . ",  '" . $product['product_id']  . "', '" . $product['quantity'] . "', 1 , 1 )";

                        if (++$m < sizeof($results)) {
                            $sql .= ', ';
                        } else {
                            $sql .= ';';
                        }
                    }

                    $query = $db->query($sql);

                }
                if ($query) {

                    $sql = " update oc_x_stock_checks set  status = 2   where  pallet_number = '" . $pallet_number . "'";

                    $query = $db->query($sql);
                    return 2;
                }
            }
        }
    }

    public  function addStockMoveTransfer(array $data){
        global $db ;
        $pallet_number = $data['data']['pallet_number']? $data['data']['pallet_number'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $add_user = $data['data']['add_user']? $data['data']['add_user'] : '';
        $code = $data['data']['code']? $data['data']['code'] : '';

        $sql = "select  stock_check_category from  oc_x_stock_checks  WHERE  pallet_number = '".$pallet_number."'";
        $query = $db->query($sql);
        $result = $query->row;

        $sql = " insert into oc_x_stock_checks_transfer (`date_added` ,`added_by` ,`warehouse_id`,`pallet_number` ,`stock_check_category` ,`code`) VALUES  (NOW() ,'" . $add_user . "','" . $warehouse_id . "' , '" . $pallet_number . "','".$result['stock_check_category']."'  , '".$code."')";

        $query = $db->query($sql);
        if($query){
            return 1;

        }else{
            return 2 ;
        }


    }

    public function confirmTransfer(array  $data ){
        global $db ;
        $pallet_number = $data['data']['driver_number']? $data['data']['driver_number'] : '';
        $sql = " insert  into  oc_x_transfer_driver (`name`) VALUES ('".$pallet_number."')  " ;
        $query = $db->query($sql);
        if($query){
            return 1 ;
        }else {
            return  2 ;
        }
    }

    public function getTransferMissionNUM(array  $data ){
        global $db ;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $sql = " select  count(product_id) num  from  oc_x_stock_section_product   pt where   pt.warehouse_id ='".$warehouse_id."' and pt.status = 1
                    and pt.safe_quantity > pt.quantity  and pt.stock_section_type_id = 1 ";

        $query = $db->query($sql);
        $result = $query->row;
        return $result ;
    }



    public function  getWarehouseTransferInfo(){
        global $db ;

        $sql = "select warehouse_transfer_area_id , title    from oc_x_warehouse_transfer_area  WHERE  status = 1 ";
        $query = $db->query($sql);
        $result = $query->rows;
        return $result;

    }


    //仓库分区
    public  function  getProductSectionType(){
        global $db ;

        $sql =  " select * from  oc_x_product_section_type WHERE station_section_type_id in (1,2)" ;
        $query = $db->query($sql);
        $result = $query->rows;
        return $result;

    }


    function  confirmOut(array $data){
        global $db ;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $stock_move_out_area = $data['data']['stock_move_out_area'] ? $data['data']['stock_move_out_area'] : '';
        $stock_move_out_area_num = $data['data']['stock_move_out_area_num'] ? $data['data']['stock_move_out_area_num'] : '';
        $product_id = $data['data']['product_id'] ? $data['data']['product_id'] : '';
        $inventory_user_id = $data['data']['inventory_user_id'] ? $data['data']['inventory_user_id'] : '';

        $sql = "select ss.stock_section_id , ssp.product_id , quantity  from oc_x_stock_section  ss LEFT  JOIN  oc_x_stock_section_product  ssp on ss.stock_section_id = ssp.stock_section_id  where ss.warehouse_id = '".$warehouse_id."' and ss.name = '".$stock_move_out_area ."' and ssp.product_id = '".$product_id."' ";

        $query = $db->query($sql);
        $result = $query->row;
        if($result['product_id'] ==  ''){
            return 1 ;
        }else {
            $stock_move_out_area_num1 = $stock_move_out_area_num * (-1);
            $sql = " insert into oc_x_stock_section_product_move  (`warehouse_id` , `stock_section_id` , `section_move_type_id` , `product_id` , `date_added` ,`added_by` , `quantity`) VALUES ('".$warehouse_id."','".$result['stock_section_id']."','2','".$product_id."',NOW(),'".$inventory_user_id."','".$stock_move_out_area_num1 ."')";
            $query = $db->query($sql);

            $sql = " insert into oc_x_stock_section_product_move_inventory  (`warehouse_id` , `stock_section_id` , `section_move_type_id` , `product_id` , `date_added` ,`added_by` , `quantity`,`name`) VALUES ('".$warehouse_id."','".$result['stock_section_id']."','2','".$product_id."',NOW(),'".$inventory_user_id."','".$stock_move_out_area_num1 ."' , '".$stock_move_out_area."')";
            $query = $db->query($sql);

            if($query){
                $update_quantity = $result['quantity'] - $stock_move_out_area_num ;
                $sql = " update oc_x_stock_section_product  set quantity = '".$update_quantity."'  where warehouse_id = '".$warehouse_id."' and stock_section_id = '".$result['stock_section_id'] ."' and product_id = '".$product_id."'  ";

                $query = $db->query($sql);
                if($query){
                    return 2 ;
                }
            }



        }

    }

    function confirmIn(array $data){
        global $db ;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $stock_move_in_area = $data['data']['stock_move_in_area'] ? $data['data']['stock_move_in_area'] : '';
        $stock_move_out_area = $data['data']['stock_move_out_area'] ? $data['data']['stock_move_out_area'] : '';
        $stock_move_in_area_num = $data['data']['stock_move_in_area_num'] ? $data['data']['stock_move_in_area_num'] : '';
        $product_id = $data['data']['product_id'] ? $data['data']['product_id'] : '';
        $inventory_user_id = $data['data']['inventory_user_id'] ? $data['data']['inventory_user_id'] : '';

        $sql = "select ss.stock_section_id , ssp.product_id , quantity  from oc_x_stock_section  ss LEFT  JOIN  oc_x_stock_section_product  ssp on ss.stock_section_id = ssp.stock_section_id  where ss.warehouse_id = '".$warehouse_id."' and ss.name = '".$stock_move_in_area ."' and ssp.product_id = '".$product_id."' ";

        $query = $db->query($sql);
        $result = $query->row;
        if($result['product_id'] ==  ''){
            return 1 ;
        }else {

            $sql = " insert into oc_x_stock_section_product_move  (`warehouse_id` , `stock_section_id` , `section_move_type_id` , `product_id` , `date_added` ,`added_by` , `quantity`) VALUES ('".$warehouse_id."','".$result['stock_section_id']."','2','".$product_id."',NOW(),'".$inventory_user_id."','".$stock_move_in_area_num ."')";
            $query = $db->query($sql);


            if($query){
                $update_quantity = $result['quantity'] + $stock_move_in_area_num ;
                $sql = " update oc_x_stock_section_product  set quantity = '".$update_quantity."'  where warehouse_id = '".$warehouse_id."' and stock_section_id = '".$result['stock_section_id'] ."' and product_id = '".$product_id."'  ";

                $query = $db->query($sql);
                if($query){

                    $sql = "delete  from  oc_x_stock_section_product_move_inventory where  warehouse_id = '".$warehouse_id."' and added_by = '".$inventory_user_id."' and  name =  '".$stock_move_out_area ."'" ;

                    $query = $db->query($sql);
                    return 2 ;
                }
            }



        }

    }

    function getStockSectionProduct(array  $data ){
        global $db ;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $product_id = $data['data']['product_id'] ? $data['data']['product_id'] : '';
        $sql = " select  ss.stock_section_id , ss.name , ssp.quantity , ssp.product_id  from oc_x_stock_section ss left join oc_x_stock_section_product ssp on ss.stock_section_id = ssp.stock_section_id  where ssp.warehouse_id = '".$warehouse_id."' and  ssp.product_id = '".$product_id ."'  order by ss.stock_section_type_id";


        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }

    function  getSkuProductId(array $data){
        global $db ;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $product_id = $data['data']['sku_id'] ? $data['data']['sku_id'] : '';

        if (strlen($product_id) > 6) {
            $sql = " select  product_id  from  oc_product_to_warehouse where warehouse_id = '".$warehouse_id."' and  sku_barcode = '".$product_id ."'";
        }else{
            $sql = " select  product_id  from  oc_product_to_warehouse where warehouse_id = '".$warehouse_id."' and  product_id = '".$product_id ."'";
        }

        $query = $db->query($sql);
        $result = $query->row;
        return $result['product_id'];
    }

    function getProductAllSingleInfo(array $data){

        global $db ;
        $warehouse_id = $data['data']['warehouse_id'] ? $data['data']['warehouse_id'] : '';
        $product_id = $data['data']['product_id'] ? $data['data']['product_id'] : '';

        //判断退货是否确认
        $sql = " select order_id , return_id ,confirmed  from oc_return_deliver_product  WHERE  warehouse_id = '".$warehouse_id."' and product_id = '".$product_id ."'  ";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['confirmed'] == 0 ){

            $result['status'] == 1  ;

        }else{

//            //判断是退货是否上架
//            $sql =  "select  return_confirmed  from oc_return_product  where return_id  = '".$result['return_id']."'   ";
//            $query = $db->query($sql);
//            $result = $query->row;

            $result['status'] == 2 ;
            return $result ;
        }

        //判断采购单是否录入仓库分区
        $sql = " select ppo.purchase_order_id , ppop.product_id  from oc_x_pre_purchase_order  ppo  LEFT  JOIN  oc_x_pre_purchase_order_product ppop on  ppo.purchase_order_id = ppop.purchase_order_id  where ppop.warehouse_id = '".$warehouse_id."' and ppop.product_id = '".$product_id ."' and ppo.status in (4,5) ";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['purchase_order_id'] > 0 ){
            $sql = " select stock_section_id from  oc_x_stock_section_product_move where purchase_order_id = '".$result['purchase_order_id']."'  ";

            $query = $db->query($sql);
            $result = $query->row;

            if($result['stock_section_id'] > 0 ) {
                $result['statuss'] = 3;
            }else {
                $result['statuss'] = 4;
            }
        }

        return $result ;

    }

}

$warehouse = new WAREHOUSE();
?>