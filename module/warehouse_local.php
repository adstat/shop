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
						WHERE inv_pro.status = 1  and  p.product_id =  '" . $db->escape($sku) . "'  AND oo.order_id =   '" . $db->escape($order_id) . "'
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
        if($warehouse_id ==12 || $warehouse_id ==14 || $warehouse_id ==15 || $warehouse_id ==16 ){

            $sql =  "select  logistic_driver_id  from  oc_x_logistic_allot  WHERE logistic_allot_id='". $logistic_allot_id."'";

            $query = $db->query($sql);
            $result = $query->row;

            $sql1 = " select sum(total) total  from  oc_x_logistic_allot la  LEFT JOIN  oc_x_logistic_allot_order lao  ON  la.logistic_allot_id = lao.logistic_allot_id LEFT JOIN   oc_order o ON  lao.order_id = o.order_id WHERE  o.order_payment_status_id != 2 and o.payment_code != 'CYCLE' and la.logistic_driver_id = '".$result['logistic_driver_id'] ."'   and o.order_status_id!=3  and DATE(la.date_added) between  '2017-12-01' and '".$date1."' and o.warehouse_id = '". $warehouse_id ."' and  o.order_deliver_status_id in (1,2,3)  group by la.logistic_driver_id  ";

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

                $sql ="update oc_x_deliver_order set order_deliver_status_id = 2, date_out=now() WHERE order_id= '". $order_id ."' and order_deliver_status_id in(1,11)";
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

                        $sql ="update oc_x_deliver_order  set order_deliver_status_id = 2, date_out=now() WHERE order_id= '". $value ."' and order_deliver_status_id in(1,11)";
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


        $sql = "SELECT  warehouse_id ,title,station_id  FROM  oc_x_warehouse  where  status = 1 ";

        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }
    //获取正在使用的仓库
    public function getUseWarehouseId( ){
            global $db;


            $sql = "SELECT  warehouse_id ,title FROM  oc_x_warehouse WHERE status = 1";

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
              WHERE  o.order_id = '".$order_id."' and o.order_deliver_status_id = '2' and ios.status = 1
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

    //获取订单状态更改历史
    public function get_order_deliver_status_history(array  $data){
        global $db;
//        return $data['data']['old_deliver_order_id'];
        $old_deliver_order_id = !empty($data['data']['old_deliver_order_id']) ? $data['data']['old_deliver_order_id'] : false;
        $order_type = isset($data['data']['order_type']) ? $data['data']['order_type'] : 0;
        if (!$old_deliver_order_id) {
           return 1;
        }
        //订单号查询相关信息
        if ($order_type == 2) {
            $sql = "SELECT GROUP_CONCAT(dods.name) AS deliver_name,GROUP_CONCAT(odos.name) AS order_name,GROUP_CONCAT(dos.date_added) AS date,dos.deliver_order_id,oxw.title AS doWarehouse,odo.date_added,odo.deliver_date
              FROM oc_x_deliver_order_history dos
              LEFT JOIN oc_x_deliver_order odo ON odo.deliver_order_id = dos.deliver_order_id
              LEFT JOIN oc_x_deliver_order_deliver_status dods ON dos.order_deliver_status_id = dods.order_deliver_status_id
              LEFT JOIN oc_x_deliver_order_status odos ON odos.order_status_id = dos.order_status_id
              LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = odo.do_warehouse_id
              WHERE odo.order_id = '".$old_deliver_order_id ."'
              GROUP BY dos.deliver_order_id";
//        return $sql;
            $query = $db->query($sql);
            $results['doStatus'] = $query->rows;
            $sql = "SELECT dods.name AS deliver_name,odos.name AS order_name,dos.order_id,oxw.title AS warehouse,dos.date_added,dos.deliver_date,oxa.name AS area_name,oxa.city,oxa.district
              FROM oc_order dos
              LEFT JOIN oc_order_deliver_status dods ON dos.order_deliver_status_id = dods.order_deliver_status_id
              LEFT JOIN oc_order_status odos ON odos.order_status_id = dos.order_status_id
              LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = dos.warehouse_id
            LEFT JOIN oc_x_area oxa ON oxa.area_id = dos.area_id 
              WHERE dos.order_id = '".$old_deliver_order_id ."'";
//        return $sql;
            $query = $db->query($sql);
            $results['soStatus'] = $query->row;
            $sql = "SELECT dods.name AS deliver_name,odos.name AS order_name,dos.deliver_order_id,oxw.title AS doWarehouse,dos.date_added,dos.deliver_date,odod.inventory_name
              FROM oc_x_deliver_order dos
              LEFT JOIN oc_x_deliver_order_deliver_status dods ON dos.order_deliver_status_id = dods.order_deliver_status_id
              LEFT JOIN oc_x_deliver_order_status odos ON odos.order_status_id = dos.order_status_id
            LEFT JOIN oc_order_distr odod ON odod.deliver_order_id = dos.deliver_order_id
              LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = dos.do_warehouse_id
              WHERE dos.order_id = '".$old_deliver_order_id ."' ORDER BY dos.do_warehouse_id";
//        return $sql;
            $query = $db->query($sql);
            $results['doLists'] = $query->rows;
            $sql = " SELECT oco.uptime,oco.added_by,doo.deliver_order_id,if (doo.is_repack=0,odi.inv_comment,odi.frame_vg_list) AS stock_area,doo.order_id,doo.is_repack
FROM oc_x_deliver_order doo
LEFT JOIN oc_order o ON doo.order_id = o.order_id
LEFT JOIN oc_x_deliver_order_inv odi ON doo.deliver_order_id = odi.deliver_order_id
LEFT JOIN oc_x_consolidated_order oco ON doo.order_id = oco.order_id
WHERE o.order_id = '".$old_deliver_order_id ."' 
GROUP BY doo.deliver_order_id ORDER BY doo.order_id";
//return $sql;
            $query = $db->query($sql);
            $results['doInformation'] = $query->rows;
        //DO单号查询相关信息
        } else if ($order_type == 1) {
            $sql = "SELECT GROUP_CONCAT(dods.name) AS deliver_name,GROUP_CONCAT(odos.name) AS order_name,GROUP_CONCAT(dos.date_added) AS date,dos.deliver_order_id,oxw.title AS doWarehouse,odo.date_added,odo.deliver_date
              FROM oc_x_deliver_order_history dos
              LEFT JOIN oc_x_deliver_order odo ON odo.deliver_order_id = dos.deliver_order_id
              LEFT JOIN oc_x_deliver_order_deliver_status dods ON dos.order_deliver_status_id = dods.order_deliver_status_id
              LEFT JOIN oc_x_deliver_order_status odos ON odos.order_status_id = dos.order_status_id
              LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = odo.do_warehouse_id
              WHERE odo.deliver_order_id = '".$old_deliver_order_id ."'
              GROUP BY dos.deliver_order_id";
//        return $sql;
            $query = $db->query($sql);
            $results['doStatus'] = $query->rows;
            $sql = "SELECT dods.name AS deliver_name,odos.name AS order_name,dos.order_id,oxw.title AS warehouse,dos.date_added,dos.deliver_date,oxa.name AS area_name,oxa.city,oxa.district
              FROM oc_x_deliver_order odo 
            LEFT JOIN oc_order dos ON odo.order_id = dos.order_id
              LEFT JOIN oc_order_deliver_status dods ON dos.order_deliver_status_id = dods.order_deliver_status_id
              LEFT JOIN oc_order_status odos ON odos.order_status_id = dos.order_status_id
              LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = dos.warehouse_id
            LEFT JOIN oc_x_area oxa ON oxa.area_id = dos.area_id 
              WHERE odo.deliver_order_id = '".$old_deliver_order_id ."'";
//        return $sql;
            $query = $db->query($sql);
            $results['soStatus'] = $query->row;
            $sql = "SELECT dods.name AS deliver_name,odos.name AS order_name,dos.deliver_order_id,oxw.title AS doWarehouse,dos.date_added,dos.deliver_date,odod.inventory_name
              FROM oc_x_deliver_order dos
              LEFT JOIN oc_x_deliver_order_deliver_status dods ON dos.order_deliver_status_id = dods.order_deliver_status_id
              LEFT JOIN oc_x_deliver_order_status odos ON odos.order_status_id = dos.order_status_id
              LEFT JOIN oc_order_distr odod ON odod.deliver_order_id = dos.deliver_order_id
              LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = dos.do_warehouse_id
              WHERE dos.deliver_order_id = '".$old_deliver_order_id ."'";
//        return $sql;
            $query = $db->query($sql);
            $results['doLists'] = $query->rows;
            $sql = " SELECT oco.uptime,oco.added_by,doo.deliver_order_id,if (doo.is_repack=0,odi.inv_comment,odi.frame_vg_list) AS stock_area,doo.order_id,doo.is_repack
FROM oc_x_deliver_order doo
LEFT JOIN oc_order o ON doo.order_id = o.order_id
LEFT JOIN oc_x_deliver_order_inv odi ON doo.deliver_order_id = odi.deliver_order_id
LEFT JOIN oc_x_consolidated_order oco ON doo.order_id = oco.order_id
WHERE doo.deliver_order_id = '".$old_deliver_order_id ."' 
GROUP BY doo.deliver_order_id ORDER BY doo.order_id";
//return $sql;
            $query = $db->query($sql);
            $results['doInformation'] = $query->rows;
        }

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
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 and wr.relevant_status_id != 3  and DATE (wr.date_added) between  date_sub(current_date(), interval 3 day)  and  current_date()  and wr.out_type  =  3  ";



        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }
    //苏州仓调拨单
    public function getNewWarehouseRequisition( array $data){
        global $db;
//return $data;
        $warehouse_id = isset($data['data']['warehouse_id']) ? trim($data['data']['warehouse_id']) : false;
        $relevant_id = !empty($data['data']['relevant_id']) ? trim($data['data']['relevant_id']) : false;
        $to_warehouse_id = isset($data['data']['to_warehouse_id']) ? trim($data['data']['to_warehouse_id']) : false;
        $date_added = isset($data['data']['date_added']) ? $data['data']['date_added'] : false;
        $date_end = isset($data['data']['date_end']) ? $data['data']['date_end'] : false;
        $warehouse_out_type = isset($data['data']['warehouse_out_type']) ? trim($data['data']['warehouse_out_type']) : false;
//return $relevant_id;
        /*zx
        判断是不是退货调拨入库查看*/
        if ($warehouse_out_type == 4) {
            /*zx
            入库查看*/
            if ($warehouse_id == $to_warehouse_id) {
                $sql = " SELECT SUM(wri.num) AS numbers,w.title AS to_title, wr.relevant_id ,wr.from_warehouse ,wr.to_warehouse,wr.date_added,u.username,wr.deliver_date,wrs.name ,w.title,wr.relevant_status_id,wr.out_type,wr.comment
              FROM  oc_x_warehouse_requisition wr 
              LEFT JOIN oc_x_warehouse_requisition_item wri ON wr.relevant_id = wri.relevant_id
              LEFT JOIN oc_x_warehouse w  ON  wr.from_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 ";
                if ($relevant_id) {
                    $sql .= " AND wr.relevant_id = " . $relevant_id;
                } else {
                    $sql .= " AND wr.relevant_status_id != 3  
            AND DATE (wr.date_added) BETWEEN '".$date_added."' AND '".$date_end."'";
                }
                $sql .= " AND wr.to_warehouse =".$to_warehouse_id;
                $sql .= " AND wr.out_type =".$warehouse_out_type;
                $sql .= " AND wr.from_warehouse !=".$warehouse_id;
                $sql .= " GROUP BY wr.relevant_id";

                $sql1 = " SELECT SUM(wrt.quantity) AS numbers2,wrt.relevant_id
              FROM  oc_x_warehouse_requisition wr 
              LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON wr.relevant_id = wrt.relevant_id AND wrt.warehouse_id = ".$warehouse_id."
              LEFT JOIN oc_x_warehouse w  ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 ";
                if ($relevant_id) {
                    $sql1 .= " AND wr.relevant_id = " . $relevant_id;
                } else {
                    $sql1 .= " AND wr.relevant_status_id != 3  
            AND DATE (wr.date_added) BETWEEN '".$date_added."' AND '".$date_end."'";
                }
                $sql1 .= " AND wr.to_warehouse =".$to_warehouse_id;
                $sql1 .= " AND wr.out_type =".$warehouse_out_type;
                $sql1 .= " AND wr.from_warehouse !=".$warehouse_id;
                $sql1 .= " GROUP BY wr.relevant_id";
                $query = $db->query($sql);
                $results['data']['container1'] = $query->rows;
                $query1 = $db->query($sql1);
                $results['data']['container2'] = $query1->rows;

            /*zx
            查看已生成的退货调拨单*/
            } else {
                $sql = " SELECT SUM(wri.num) AS numbers,w.title AS to_title, wr.relevant_id ,wr.from_warehouse ,wr.to_warehouse,wr.date_added,u.username,wr.deliver_date,wrs.name ,w.title,wr.relevant_status_id,wr.out_type,wr.comment
              FROM  oc_x_warehouse_requisition wr 
              LEFT JOIN oc_x_warehouse_requisition_item wri ON wr.relevant_id = wri.relevant_id
              LEFT JOIN oc_x_warehouse w  ON  wr.from_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 ";
                if ($relevant_id) {
                    $sql .= " AND wr.relevant_id = " . $relevant_id;
                } else {
                    $sql .= " AND wr.relevant_status_id != 3  
            AND DATE (wr.date_added) BETWEEN '".$date_added."' AND '".$date_end."'";
                }
                $sql .= " AND wr.to_warehouse =".$to_warehouse_id;
                $sql .= " AND wr.out_type =".$warehouse_out_type;
                $sql .= " AND wr.from_warehouse =".$warehouse_id;
                $sql .= " GROUP BY wr.relevant_id";
                $query = $db->query($sql);
                $results['data']['container1'] = $query->rows;
            }

        /*zx
        非退货调拨单*/
        } else {

            //待操作明细
            if (trim($warehouse_id) == trim($to_warehouse_id)) {                //入库看已出库
                $sql = " SELECT SUM(wrt.quantity) AS numbers,w.title AS to_title, wr.relevant_id ,wr.from_warehouse ,wr.to_warehouse,wr.date_added,u.username,wr.deliver_date,wrs.name ,w.title,wr.relevant_status_id,wr.out_type,wr.comment
              FROM  oc_x_warehouse_requisition wr 
              LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON wr.relevant_id = wrt.relevant_id AND wrt.warehouse_id != " . $warehouse_id . "
              LEFT JOIN oc_x_warehouse w  ON  wr.from_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 ";
                if ($relevant_id) {
                    $sql .= " AND wr.relevant_id = " . $relevant_id;
                } else {
                    $sql .= " AND wr.relevant_status_id != 3  
            AND DATE (wr.date_added) BETWEEN '" . $date_added . "' AND '" . $date_end . "'";
                }
                $sql .= " AND wr.to_warehouse =" . $to_warehouse_id;
                $sql .= " AND wr.out_type =" . $warehouse_out_type;
                $sql .= " AND wr.from_warehouse !=" . $warehouse_id;
            } else {         //出库看明细
                $sql = " SELECT SUM(wri.num) AS numbers,w.title AS to_title, wr.relevant_id ,wr.from_warehouse ,wr.to_warehouse,wr.date_added,u.username,wr.deliver_date,wrs.name ,w.title,wr.relevant_status_id,wr.out_type,wr.comment
              FROM  oc_x_warehouse_requisition wr 
              LEFT JOIN oc_x_warehouse_requisition_item wri ON wr.relevant_id = wri.relevant_id
              LEFT JOIN oc_x_warehouse w  ON  wr.from_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 ";
                if ($relevant_id) {
                    $sql .= " AND wr.relevant_id = " . $relevant_id;
                } else {
                    $sql .= " AND wr.relevant_status_id != 3  
            AND DATE (wr.date_added) BETWEEN '" . $date_added . "' AND '" . $date_end . "'";
                }
                $sql .= " AND wr.to_warehouse =" . $to_warehouse_id;
                $sql .= " AND wr.out_type =" . $warehouse_out_type;
                $sql .= " AND wr.from_warehouse =" . $warehouse_id;
            }
            $sql .= " GROUP BY wr.relevant_id";
//中间表数量
            if (trim($warehouse_id) == trim($to_warehouse_id)) {                //入库看入库数量
                $sql1 = " SELECT SUM(wrt.quantity) AS numbers2,wrt.relevant_id
              FROM  oc_x_warehouse_requisition wr 
              LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON wr.relevant_id = wrt.relevant_id AND wrt.warehouse_id = " . $warehouse_id . "
              LEFT JOIN oc_x_warehouse w  ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 ";
                if ($relevant_id) {
                    $sql1 .= " AND wr.relevant_id = " . $relevant_id;
                } else {
                    $sql1 .= " AND wr.relevant_status_id != 3  
            AND DATE (wr.date_added) BETWEEN '" . $date_added . "' AND '" . $date_end . "'";
                }
                $sql1 .= " AND wr.to_warehouse =" . $to_warehouse_id;
                $sql1 .= " AND wr.out_type =" . $warehouse_out_type;
                $sql1 .= " AND wr.from_warehouse !=" . $warehouse_id;
            } else {         //出库已出库数量
                $sql1 = " SELECT SUM(wrt.quantity) AS numbers2,wrt.relevant_id
              FROM  oc_x_warehouse_requisition wr 
              LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON wr.relevant_id = wrt.relevant_id AND wrt.warehouse_id = " . $warehouse_id . "
              LEFT JOIN oc_x_warehouse w  ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 ";
                if ($relevant_id) {
                    $sql1 .= " AND wr.relevant_id = " . $relevant_id;
                } else {
                    $sql1 .= " AND wr.relevant_status_id != 3  
            AND DATE (wr.date_added) BETWEEN '" . $date_added . "' AND '" . $date_end . "'";
                }
                $sql1 .= " AND wr.to_warehouse =" . $to_warehouse_id;
                $sql1 .= " AND wr.out_type =" . $warehouse_out_type;
                $sql1 .= " AND wr.from_warehouse =" . $warehouse_id;
            }
            $sql1 .= " GROUP BY wr.relevant_id";
            $query = $db->query($sql);
            $results['data']['container1'] = $query->rows;
            $query1 = $db->query($sql1);
            $results['data']['container2'] = $query1->rows;
        }

//return $sql;

        return $results;
    }
/*zx
根据商品条码获取商品信息*/
    public function getProductInformation( array $data){
        global  $db;
        $product_id = isset($data['data']['product_id']) ? $data['data']['product_id'] : false;

        $sql = "SELECT ptw.product_id,p.name
              FROM  oc_product_to_warehouse ptw
              LEFT JOIN  oc_product p ON ptw.product_id  = p.product_id
             WHERE ";
        if (strlen($product_id) > 6) {
            $sql .= " ptw.sku_barcode = '".$product_id."'";
        } else {
            $sql .= " ptw.product_id = '".$product_id."'";
        }

        $query = $db->query($sql);
        $results = $query->row;
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
            $sql .= " and wr.out_type = '3";
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
        $user_warehouse_id  = $data['data']['user_warehouse_id'];
        $sql = "select username from oc_w_user  WHERE  warehouse_id = '".$warehouse_id."'and status = 1 and user_group_id =15 and repack =0 ";

        if($warehouse_id == 12){
            $sql .=  "  and to_warehouse_id = '".$user_warehouse_id."' ";
        }
        $query = $db->query($sql);
        $results = $query->rows;
        return $results;
    }
    public function getinventorynamerepack(array $data){
        global  $db ;
        $warehouse_id  = $data['data']['warehouse_id'];
        $repack  = $data['data']['repack'];
        $user_warehouse_id  = $data['data']['user_warehouse_id'];
        $sql = "select username from oc_w_user  WHERE  warehouse_id = '".$warehouse_id."'and status = 1 and user_group_id =15 and repack ='".$repack."'";
         if($warehouse_id == 12){
             $sql .=  "  and to_warehouse_id = '".$user_warehouse_id."' ";
         }

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

    /*zx
    调拨单详情*/
    public function relevantViewItem(array  $data){
        global  $db;

        $invalidProducts = '';
        $invalidContainers = '';
//return $data;
        $warehouse_id = isset($data['data']['warehouse_id']) ? trim($data['data']['warehouse_id']) : false;
        $relevant_id = !empty($data['data']['relevant_id']) ? trim($data['data']['relevant_id']) : false;
        $to_warehouse_id = isset($data['data']['to_warehouse_id']) ? trim($data['data']['to_warehouse_id']) : false;
        $from_warehouse_id = isset($data['data']['from_warehouse_id']) ? trim($data['data']['from_warehouse_id']) : false;
        $date_added = isset($data['data']['date_added']) ? trim($data['data']['date_added']) : false;
        $relevant_status_id = isset($data['data']['relevant_status_id']) ? trim($data['data']['relevant_status_id']) : false;
        $warehouse_out_type = isset($data['data']['warehouse_out_type']) ? trim($data['data']['warehouse_out_type']) : false;
//return $relevant_id;
        /*zx
        退货调拨单明细查看*/
        if ($warehouse_out_type == 4) {

            /*zx
            入库查看*/
            if ($warehouse_id == $to_warehouse_id) {
                $sql = "SELECT  wr.relevant_id ,wr.relevant_status_id ,
GROUP_CONCAT(wri.product_id) AS product_id
FROM oc_x_warehouse_requisition wr  
LEFT JOIN oc_x_warehouse_requisition_item wri ON  wri .relevant_id = wr.relevant_id  
LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
WHERE  1=1 ";
                if ($relevant_status_id == 4) {                             //出库
                    $sql .= " AND wr.relevant_status_id = 4";
                } else if ($relevant_status_id == 6) {                       //查看出库
                    $sql .= " AND wr.relevant_status_id != 2";
                }
                $sql .= " AND wr.relevant_id = " . $relevant_id;
                $sql .= " AND wr.from_warehouse =" . $from_warehouse_id . "
            AND wr.to_warehouse =" . $to_warehouse_id;
                $sql .= " GROUP BY wr.relevant_id";
//            return $sql;
                $query = $db->query($sql);
                $results = $query->rows;
                $return['product2'] = [];
                $return['product1'] = [];
//        return $return;
                foreach ($results as $result) {
                    //判断临时表是否有
                    $relevant_ids = $result['relevant_id']; //字符串
                    $product_ids = explode(',', $result['product_id']); //数组
                    foreach ($product_ids as $key => $product_id) {

                        //入库查询插入临时表的信息
                        $sql1 = " select wrt.container_id ,wrt.relevant_id,wrt.product_id,wrt.quantity AS num1,wri.num AS num2,ptw.sku_barcode , p.name
FROM oc_x_warehouse_requisition_temporary wrt
LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wrt.relevant_id
LEFT JOIN oc_x_warehouse_requisition_item wri ON  wri .product_id = wrt.product_id  AND wri.container_id = wrt.container_id AND wri.relevant_id = wrt.relevant_id
LEFT  JOIN oc_product_to_warehouse ptw ON wrt.product_id = ptw.product_id AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wrt.product_id = p.product_id                     
WHERE wrt.relevant_id = '" . $relevant_ids . "'
AND wrt.product_id = '" . $product_id . "'";
                        if ($relevant_status_id == 4) {
                            $sql1 .= " AND wrt.warehouse_id =" . $to_warehouse_id;
                        } else if ($relevant_status_id == 6) {
                            $sql1 .= " AND wrt.warehouse_id =" . $to_warehouse_id;
                        }
                        $sql1 .= " GROUP BY wrt.product_id";
//                    return $sql1;
                        $invalid = $db->query($sql1)->rows;
                        if (!$invalid) {
                            $invalidProducts .= ',' . $product_id;  //不存在临时表中
                        } else {
                            $return['product1'] = array_merge($invalid, $return['product1']);
                        }
                    }
                    $invalidProducts = ltrim($invalidProducts, ",");
                    $invalidProducts = explode(',', $invalidProducts); //数组
//                return $invalidProducts;
                    if ($invalidProducts) {
                        foreach ($invalidProducts as $key => $invalidProduct) {
                            //出库看没插入临时表时的取值
                            $sql2 = "select wri.container_id ,wri.relevant_id,wri.product_id,wri.num AS num2 , ptw.sku_barcode , p.name 
FROM oc_x_warehouse_requisition_item wri
LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wri.relevant_id
LEFT JOIN oc_product_to_warehouse ptw ON wri.product_id = ptw.product_id AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wri.product_id = p.product_id 
WHERE  wri.relevant_id = '" . $result['relevant_id'] . "'
AND wri.product_id = '" . $invalidProduct . "'         
AND wr.from_warehouse =" . $from_warehouse_id . "
AND wr.to_warehouse = " . $to_warehouse_id;
                            $sql2 .= " GROUP BY wri.product_id";

//                        return $sql2;
                            $query2 = $db->query($sql2);
                            $return['product2'] = array_merge($query2->rows, $return['product2']);
                        }
                    }
                }

           /*zx
           出库查看*/
            } else {
                $sql2 = "select wri.container_id ,wri.relevant_id,wri.product_id,wri.num AS num2 , ptw.sku_barcode , p.name 
FROM oc_x_warehouse_requisition_item wri
LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wri.relevant_id
LEFT JOIN oc_product_to_warehouse ptw ON wri.product_id = ptw.product_id AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wri.product_id = p.product_id 
WHERE  wri.relevant_id = '" .$relevant_id. "'
AND wr.from_warehouse =" . $from_warehouse_id . "
AND wr.to_warehouse = " . $to_warehouse_id."
GROUP BY wri.product_id";
//                        return $sql2;
                $query2 = $db->query($sql2);
                $return['product1'] =$query2->rows;
            }

        /*zx
        非退货调拨单*/
        } else {

            //获取该出库单的状态以及所有商品ID
            if ($warehouse_id == $from_warehouse_id) {
                $sql = "SELECT  wr.relevant_id ,wr.relevant_status_id ,
wri.product_id AS product_id,
wri.container_id AS container_id
FROM oc_x_warehouse_requisition wr  
LEFT JOIN oc_x_warehouse_requisition_item wri ON  wri .relevant_id = wr.relevant_id  
LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
WHERE  1=1 ";
                if ($relevant_status_id == 2) {                             //出库
                    $sql .= " AND wr.relevant_status_id = 2";
                } else if ($relevant_status_id == 1) {                       //查看出库
                    $sql .= " AND wr.relevant_status_id != 2";
                }
                $sql .= " AND wr.relevant_id = " . $relevant_id;
                $sql .= " AND wr.from_warehouse =" . $from_warehouse_id . "
            AND wr.to_warehouse =" . $to_warehouse_id;
                $sql .= " GROUP BY product_id,container_id ORDER BY container_id";
//            查询出库时有多少周转筐
                $sq2 = "SELECT  COUNT(DISTINCT wri.container_id) AS container_nums
FROM oc_x_warehouse_requisition wr  
LEFT JOIN oc_x_warehouse_requisition_item wri ON  wri .relevant_id = wr.relevant_id  
LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
WHERE  1=1 ";
                if ($relevant_status_id == 2) {                             //出库
                    $sq2 .= " AND wr.relevant_status_id = 2";
                } else if ($relevant_status_id == 1) {                       //查看出库
                    $sq2 .= " AND wr.relevant_status_id != 2";
                }
                $sq2 .= " AND wr.relevant_id = " . $relevant_id;
                $sq2 .= " AND wr.from_warehouse =" . $from_warehouse_id . "
            AND wr.to_warehouse =" . $to_warehouse_id;
                $sq2 .= "  AND wri.container_id > 0";

                //            查询出库时扫描了多少周转筐
                $sq3 = "SELECT  COUNT(DISTINCT wrt.container_id) AS container_nums
FROM oc_x_warehouse_requisition wr  
LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON  wrt .relevant_id = wr.relevant_id  
LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
WHERE  1=1 ";
                if ($relevant_status_id == 2) {                             //出库
                    $sq3 .= " AND wr.relevant_status_id = 2";
                    $sq3 .= " AND wrt.relevant_status_id = 2";
                } else if ($relevant_status_id == 1) {                       //查看出库
                    $sq3 .= " AND wr.relevant_status_id != 2";
                    $sq3 .= " AND wrt.relevant_status_id = 2";
                }
                $sq3 .= " AND wr.relevant_id = " . $relevant_id;
                $sq3 .= " AND wr.from_warehouse =" . $from_warehouse_id . "
            AND wr.to_warehouse =" . $to_warehouse_id;
                $sq3 .= "  AND wrt.container_id > 0";
                //获取该入库单的状态以及所有商品ID
            } else {
                $sql = "SELECT  wr.relevant_id ,wr.relevant_status_id ,
wrt.product_id AS product_id,
wrt.container_id AS container_id
FROM oc_x_warehouse_requisition wr  
LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON  wrt .relevant_id = wr.relevant_id  
LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
WHERE  1=1 ";
                if ($relevant_status_id == 4) {                      //入库
                    $sql .= " AND wr.relevant_status_id = 4";
                    $sql .= " AND wrt.relevant_status_id = 2";
                } else if ($relevant_status_id == 6) {                       //查看入库
                    $sql .= " AND wr.relevant_status_id != 4";
                    $sql .= " AND wrt.relevant_status_id = 2";
                }
                $sql .= " AND wr.relevant_id = " . $relevant_id;
                $sql .= " AND wr.from_warehouse =" . $from_warehouse_id . "
            AND wr.to_warehouse =" . $to_warehouse_id;
                $sql .= " GROUP BY product_id,container_id ORDER BY container_id";
//            查询入库时有多少周转筐
                $sq2 = "SELECT  COUNT(DISTINCT wrt.container_id) AS container_nums
FROM oc_x_warehouse_requisition wr  
LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON  wrt .relevant_id = wr.relevant_id  
LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
WHERE  1=1 ";
                if ($relevant_status_id == 4) {                      //入库
                    $sq2 .= " AND wr.relevant_status_id = 4";
                    $sq2 .= " AND wrt.relevant_status_id = 2";
                } else if ($relevant_status_id == 6) {                       //查看入库
                    $sq2 .= " AND wr.relevant_status_id != 4";
                    $sq2 .= " AND wrt.relevant_status_id = 2";
                }
                $sq2 .= " AND wr.relevant_id = " . $relevant_id;
                $sq2 .= " AND wr.from_warehouse =" . $from_warehouse_id . "
            AND wr.to_warehouse =" . $to_warehouse_id;
                $sq2 .= "  AND wrt.container_id > 0";

                //            查询入库时扫描多少周转筐
                $sq3 = "SELECT  COUNT(DISTINCT wrt.container_id) AS container_nums
FROM oc_x_warehouse_requisition wr  
LEFT JOIN oc_x_warehouse_requisition_temporary wrt ON  wrt .relevant_id = wr.relevant_id  
LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
WHERE  1=1 ";
                if ($relevant_status_id == 4) {                      //入库
                    $sq3 .= " AND wr.relevant_status_id = 4";
                    $sq3 .= " AND wrt.relevant_status_id = 4";
                } else if ($relevant_status_id == 6) {                       //查看入库
                    $sq3 .= " AND wr.relevant_status_id != 4";
                    $sq3 .= " AND wrt.relevant_status_id = 4";
                }
                $sq3 .= " AND wr.relevant_id = " . $relevant_id;
                $sq3 .= " AND wr.from_warehouse =" . $from_warehouse_id . "
            AND wr.to_warehouse =" . $to_warehouse_id;
                $sq3 .= " AND wrt.container_id > 0";
            }
            $query = $db->query($sql);
            $results = $query->rows;
            $queryContainer = $db->query($sq2);
            $resultsContainer = $queryContainer->rows;
            $queryContainer2 = $db->query($sq3);
            $resultsContainer2 = $queryContainer2->rows;

            $return['product2'] = [];
            $return['product1'] = [];
            $return['container_numbers'] = $resultsContainer[0]['container_nums'];
            $return['container_numbers2'] = $resultsContainer2[0]['container_nums'];
//        return $return;
                //判断临时表是否有
                $relevant_ids = $relevant_id; //字符串
//            return $result['container_id'];
            foreach ($results as $key => $result) {
                $product_id = $result['product_id']; //数组
                $container_id = $result['container_id']; //数组

//            return $product_ids;
                    if ($warehouse_id == $from_warehouse_id) {

                        //出库查询插入临时表的信息
                        $sql1 = " SELECT wrt.container_id ,wrt.relevant_id,wrt.product_id,SUM(wrt.quantity) AS num1,SUM(wri.num) AS num2,ptw.sku_barcode , p.name
FROM oc_x_warehouse_requisition_temporary wrt
LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wrt.relevant_id
LEFT JOIN oc_x_warehouse_requisition_item wri ON  wri .product_id = wrt.product_id  AND wri.container_id = wrt.container_id AND wri.relevant_id = wrt.relevant_id
LEFT JOIN oc_product_to_warehouse ptw ON wrt.product_id = ptw.product_id AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wrt.product_id = p.product_id                     
WHERE wrt.relevant_id = '" . $relevant_ids . "'
AND wrt.product_id = '" . $product_id . "'
AND wrt.container_id = '" . $container_id . "'";
                        if ($relevant_status_id == 2) {
                            $sql1 .= " AND wrt.warehouse_id =" . $from_warehouse_id;
                        } else if ($relevant_status_id == 1) {
                            $sql1 .= " AND wrt.warehouse_id =" . $from_warehouse_id;
                        }
                        $sql1 .= " GROUP BY wrt.product_id ORDER BY wrt.container_id";
                    } else {
                        //入库查询插入临时表的信息
                        $sql1 = " SELECT wrt.container_id ,wrt.relevant_id,wrt.product_id,SUM(wrt.quantity) AS num1,wrts.quantity AS num2,ptw.sku_barcode,p.name
FROM oc_x_warehouse_requisition_temporary wrt
LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wrt.relevant_id
LEFT JOIN oc_x_warehouse_requisition_temporary wrts ON  wrts.product_id = wrt.product_id  AND wrts.container_id = wrt.container_id AND wrts.relevant_id = wrt.relevant_id
AND wrts.warehouse_id = " . $from_warehouse_id . "
LEFT JOIN oc_product_to_warehouse ptw ON wrt.product_id = ptw.product_id AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wrt.product_id = p.product_id 
WHERE wrt.relevant_id = '" . $relevant_ids . "' 
AND wrt.product_id = '" . $product_id . "'
AND wrt.container_id = '" . $container_id . "'";
                        if ($relevant_status_id == 4) {
                            $sql1 .= " AND wrt.warehouse_id =" . $to_warehouse_id;
                        } else if ($relevant_status_id == 6) {
                            $sql1 .= " AND wrt.warehouse_id =" . $to_warehouse_id;
                        }
                        $sql1 .= " GROUP BY wrt.product_id ORDER BY wrt.container_id";
                    }

//                return $sql1;
                    $invalid = $db->query($sql1)->rows;
//                                return $invalid;
                    if (!$invalid) {
                        $invalidProducts .= ',' . $product_id;  //不存在临时表中
                        $invalidContainers .= ',' . $container_id;  //不存在临时表中
                    } else {
                        $return['product1'] = array_merge($invalid, $return['product1']);
                    }
                }
                $invalidProducts = ltrim($invalidProducts, ",");
                $invalidContainers = ltrim($invalidContainers, ",");
//            return $invalidContainers.'#'.$invalidProducts;

                $invalidContainers = explode(',', $invalidContainers); //数组
                $invalidProducts = explode(',', $invalidProducts); //数组
                if ($invalidProducts) {
                    foreach ($invalidProducts as $key => $invalidProduct) {
                        if ($warehouse_id == $from_warehouse_id) {
                            //出库看没插入临时表时的取值
                            $sql2 = "SELECT wri.container_id ,wri.relevant_id,wri.product_id,SUM(wri.num) AS num2 , ptw.sku_barcode , p.name 
FROM oc_x_warehouse_requisition_item wri
LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wri.relevant_id
LEFT  JOIN oc_product_to_warehouse ptw ON wri.product_id = ptw.product_id AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wri.product_id = p.product_id 
WHERE  wri.relevant_id = '" . $result['relevant_id'] . "'
AND wri.product_id = '" . $invalidProduct . "'         
AND wri.container_id = '" . $invalidContainers[$key] . "'   
AND wr.from_warehouse =" . $from_warehouse_id . "
AND wr.to_warehouse = " . $to_warehouse_id;
                            $sql2 .= " GROUP BY wri.product_id ORDER BY wri.container_id";
                        } else {
                            //入库看没插入临时表时的取值
                            $sql2 = "SELECT wrt.container_id ,wrt.relevant_id,wrt.product_id,SUM(wrt.quantity) AS num2,ptw.sku_barcode , p.name 
FROM oc_x_warehouse_requisition_temporary wrt
LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wrt.relevant_id
LEFT JOIN oc_x_warehouse_requisition_item wri ON  wri.product_id = wrt.product_id  AND wri.container_id = wrt.container_id AND wri.relevant_id = wrt.relevant_id
LEFT JOIN oc_product_to_warehouse ptw ON wrt.product_id = ptw.product_id AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wrt.product_id = p.product_id 
WHERE  wrt.relevant_id = '" . $result['relevant_id'] . "'
AND wri.product_id = '" . $invalidProduct . "'         
AND wri.container_id = '" . $invalidContainers[$key] . "'               
AND wrt.warehouse_id =" . $from_warehouse_id . "
AND wr.from_warehouse =" . $from_warehouse_id . "
AND wr.to_warehouse = " . $to_warehouse_id;
                            $sql2 .= " GROUP BY wrt.product_id ORDER BY wrt.container_id";
                        }
//return $sql2;
                        $query2 = $db->query($sql2);
                        $return['product2'] = array_merge($query2->rows, $return['product2']);
                    }
                }
        }
        return $return;

    }
    /*zx
    调拨单提交*/
    public function addPurchaseOrderRelevantToInv(array  $data){
        global  $db;

        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;
        $box_quantitys = isset($data['data']['box_quantitys']) ? $data['data']['box_quantitys'] : false;
        $int_parts = isset($data['data']['int_parts']) ? $data['data']['int_parts'] : false;
        $relevant_ids1 = !empty($data['data']['relevant_id']) ? $data['data']['relevant_id'] : false;
        $to_warehouse_id = isset($data['data']['to_warehouse_id']) ? $data['data']['to_warehouse_id'] : false;
        $date_added = isset($data['data']['date_added']) ? $data['data']['date_added'] : false;
        $added_by = isset($data['data']['added_by']) ? $data['data']['added_by'] : false;
        $added_by_id = isset($data['data']['added_by_id']) ? $data['data']['added_by_id'] : false;
        $warehouse_out_type = isset($data['data']['warehouse_out_type']) ? $data['data']['warehouse_out_type'] : false;
        $relevant_ids2 = $relevant_ids1[0];
//        return $data;
        /*zx
        t退货调拨单*/
        if (trim($warehouse_out_type) == 4) {
            /*zx
            入库*/
            if (trim($to_warehouse_id) == trim($warehouse_id)) {
                /*zx
                 * 获取周转筐中的所有商品，并进行商品分类统计数量*/
                $sql4 = "SELECT wrt.product_id,SUM(wrt.quantity) AS quantity,oxw.station_id,ptw.name AS product,ptw.price,wrt.container_id 
FROM oc_x_warehouse_requisition_temporary wrt
LEFT JOIN oc_product ptw ON ptw.product_id = wrt.product_id
LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = wrt.warehouse_id AND oxw.warehouse_id =".$warehouse_id." 
WHERE wrt.warehouse_id = ".$warehouse_id."
AND wrt.relevant_id IN (".$relevant_ids2.")
AND wrt.relevant_status_id = 4
GROUP BY wrt.product_id ORDER BY wrt.container_id";
                $query4 = $db->query($sql4);
                $results2 = $query4->rows;
                if (!$results2) {
                    return 0;
                }
                /*zx
                 * 修改调拨单状态*/

                $sql2 = "UPDATE oc_x_warehouse_requisition SET relevant_status_id = 6 WHERE relevant_id IN (" . $relevant_ids2 . ")";
                $query2 = $db->query($sql2);
                $this->insert_warehouse_requisition_history(6,$relevant_ids2,$added_by_id);

                /*zx
                出库记录*/
                $sql5 = "INSERT INTO oc_x_stock_move (station_id,timestamp,date_added,added_by,relevant_id,inventory_type_id,warehouse_id) VALUES (2,'" .
                    time() . "',NOW(),'" . $added_by_id . "','" . $relevant_ids2 . "','23','" . $warehouse_id . "')";
                $query5 = $db->query($sql5);
                $stock_move_id = $db->getLastId();
                $sql6 = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`,  `quantity`, `box_quantity`,  `status`) VALUES ";
                foreach ($results2 as $values) {
                    $sql6 .= "(" . $stock_move_id . ",2,NOW()," . $values['product_id'] . ",'" . $values['price'] . "','" . $values['quantity'] . "','1','1'),";
                }
                $sql6 = rtrim($sql6, ',');
                $query6 = $db->query($sql6);
                /*zx
                修改前台库存*/
                $sql5 = "INSERT INTO oc_x_inventory_move (`relevant_id`,`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `add_user_name`, `memo`,`warehouse_id`)
                VALUES('" . $relevant_ids2 . "','2',current_date(),unix_timestamp(),'0','23',now(),'" . $added_by_id . "','仓间调拨单入库','" . $warehouse_id . "')";
                $query5 = $db->query($sql5);
                $inventory_move_id = $db->getLastId();
                $sql6 = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `quantity`,`warehouse_id`) VALUES ";
                foreach ($results2 as $values) {
                    $sql6 .= "(" . $inventory_move_id . ",2,NOW()," . $values['product_id'] . ",'" .$values['quantity']. "','" . $warehouse_id . "'),";
                }
                $sql6 = rtrim($sql6, ',');
                $query6 = $db->query($sql6);
            /*zx
            出库*/
            } else {
                $container_ids = isset($data['data']['container_id']) ? $data['data']['container_id'] : false;
                $product_ids = isset($data['data']['product_ids']) ? $data['data']['product_ids'] : false;
                $quantitys = isset($data['data']['quantitys']) ? $data['data']['quantitys'] : false;
                /*zx
                生成调拨单*/
                $sql5 = "INSERT INTO oc_x_warehouse_requisition (from_warehouse,to_warehouse,date_added,added_by,relevant_status_id,confirm_by,deliver_date,out_type) VALUES 
(".$warehouse_id.",'".$to_warehouse_id."',NOW(),'".$added_by_id."',4,'".$added_by_id."',NOW(),4)";
                $query5 = $db->query($sql5);
                $new_relevant_id = $db->getLastId();
                $this->insert_warehouse_requisition_history(4,$new_relevant_id,$added_by_id);
                $sql6 = "INSERT INTO `oc_x_warehouse_requisition_item` (`relevant_id`, `product_id`, `num`, `date_added`, `added_by`,  `status`, `box_quantity`,  `int_part`) VALUES ";
                foreach ($product_ids as $key => $values) {
                    $sql6 .= "(".$new_relevant_id.",".$values.",'".$quantitys[$key]."',NOW(),'".$added_by_id."',1,1,0),";
                }
                $sql6 = rtrim($sql6, ',');
                $query6 = $db->query($sql6);
                /*zx
                入库记录*/
                $sql5 = "INSERT INTO oc_x_stock_move (station_id,timestamp,date_added,added_by,relevant_id,inventory_type_id,warehouse_id) VALUES (2,'".
                    time()."',NOW(),'".$added_by_id."','".$new_relevant_id."','22','".$warehouse_id."')";
                $query5 = $db->query($sql5);
                $stock_move_id = $db->getLastId();
                $sql6 = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`,quantity, `box_quantity`,  `status`) VALUES ";
                foreach ($product_ids as $key => $values) {
                    $sql6 .= "(".$stock_move_id.",2,NOW(),".$values.",'".(0-$quantitys[$key])."',1,'1'),";
                }
                $sql6 = rtrim($sql6, ',');
                $query6 = $db->query($sql6);
                /*zx
                修改前台库存*/
                $sql5 = "INSERT INTO oc_x_inventory_move (`relevant_id`,`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `add_user_name`, `memo`,`warehouse_id`)
                VALUES('".$new_relevant_id."','2',current_date(),unix_timestamp(),'0','22',now(),'".$added_by_id."','仓间调拨单出库','".$warehouse_id."')";
                $query5 = $db->query($sql5);
                $inventory_move_id = $db->getLastId();
                $sql6 = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `quantity`,`warehouse_id`) VALUES ";
                foreach ($product_ids as $key => $values) {
                    $sql6 .= "(".$inventory_move_id.",2,NOW(),".$values.",'".(0-$quantitys[$key])."','".$warehouse_id ."'),";
                }
                $sql6 = rtrim($sql6, ',');
                $query6 = $db->query($sql6);
            }

            /*zx
            仓间调拨单或DO调拨单*/
        } else {
//return $data;
            $sql = "SELECT wr.out_type 
FROM oc_x_warehouse_requisition wr
WHERE wr.relevant_id =" . $relevant_ids2;
            $query = $db->query($sql);
            $results = $query->row;
            /*
             *zx
             * 入库操作*/
            if (trim($to_warehouse_id) == trim($warehouse_id)) {

                /*zx
                 * 入库库存更新*/
                /*
                 *zx
                 * 获取周转筐中的所有商品，并进行商品分类统计数量*/
                $sql4 = "SELECT wrt.product_id,SUM(wrt.quantity) AS quantity,oxw.station_id,ptw.name AS product,ptw.price,wrt.container_id 
FROM oc_x_warehouse_requisition_temporary wrt
LEFT JOIN oc_product ptw ON ptw.product_id = wrt.product_id
LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = wrt.warehouse_id AND oxw.warehouse_id =" . $warehouse_id . " 
WHERE wrt.warehouse_id = " . $warehouse_id . "
AND wrt.relevant_id IN (" . $relevant_ids2 . ")
AND wrt.relevant_status_id = 4
GROUP BY wrt.product_id ORDER BY wrt.container_id";
                $query4 = $db->query($sql4);
                $results2 = $query4->rows;
                if (!$results2) {
                    return 0;
                }
                /*zx
                 * 修改调拨单状态*/
                $sql2 = "UPDATE oc_x_warehouse_requisition SET relevant_status_id = 6 WHERE relevant_id IN (" . $relevant_ids2 . ")";
                $query2 = $db->query($sql2);
                $this->insert_warehouse_requisition_history(6,$relevant_ids2,$added_by_id);
                /*zx
                入库记录*/
                $sql5 = "INSERT INTO oc_x_stock_move (station_id,timestamp,date_added,added_by,relevant_id,inventory_type_id,warehouse_id) VALUES (2,'" .
                    time() . "',NOW(),'" . $added_by_id . "','" . $relevant_ids2 . "','23','" . $warehouse_id . "')";

                $query5 = $db->query($sql5);
                $stock_move_id = $db->getLastId();
                $sql6 = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`,  `quantity`, `box_quantity`,  `status`) VALUES ";
                foreach ($results2 as $values) {
                    $sql6 .= "(" . $stock_move_id . ",2,NOW()," . $values['product_id'] . ",'" . $values['price'] . "','" . $values['quantity'] . "','1','1'),";
                }
                $sql6 = rtrim($sql6, ',');
                $query6 = $db->query($sql6);
                /*zx
                判断是否是仓间调拨单，如果是修改前台库存*/
                if (trim($warehouse_out_type) == 2) {
                    $sql5 = "INSERT INTO oc_x_inventory_move (`relevant_id`,`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `add_user_name`, `memo`,`warehouse_id`)
                    VALUES('" . $relevant_ids2 . "','2',current_date(),unix_timestamp(),'0','23',now(),'" . $added_by_id . "','仓间调拨单入库','" . $warehouse_id . "')";
                    $query5 = $db->query($sql5);
                    $inventory_move_id = $db->getLastId();
                    $sql6 = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `quantity`,`warehouse_id`) VALUES ";
                    foreach ($results2 as $values) {
                        $sql6 .= "(" . $inventory_move_id . ",2,NOW()," . $values['product_id'] . ",'" . $values['quantity'] . "','" . $warehouse_id . "'),";
                    }
                    $sql6 = rtrim($sql6, ',');
                    $query6 = $db->query($sql6);
                    $update_product = '';
                    foreach ($results2 as $values) {
                        $update_product .= ','.$values['product_id'];
                    }
                    $update_product = ltrim($update_product,',');
                    /*zx
                    查询要修改状态的商品*/
                    $sql3 = "SELECT product_id,status,do_warehouse_id
FROM oc_product_to_warehouse 
WHERE warehouse_id =".$warehouse_id." 
AND product_id IN (".$update_product.") 
AND do_warehouse_id !=".$warehouse_id." 
OR warehouse_id =".$warehouse_id." 
AND product_id IN (".$update_product.") 
AND status = 0 GROUP BY product_id";
                    $query3 = $db->query($sql3);
                    $results3 = $query3->rows;
                    /*zx
                    记录修改历史*/
                    $sql4 = "INSERT INTO `oc_x_product_status_history` (`relevant_id`, `status`, `date_added`, `product_id`, `add_user_name`,`do_warehouse_id`) VALUES ";
                    foreach ($results3 as $values) {
                        $sql4 .= "(".$relevant_ids2.",".$values['status'].",NOW(),".$values['product_id'].",'".$added_by_id."','".$values['do_warehouse_id']."'),";
                    }
                    $sql4 = rtrim($sql4, ',');
                    $query4 = $db->query($sql4);
                    /*zx
                    * 修改商品表以及商品仓库对应状态*/
                    $sql2 = "UPDATE oc_product_to_warehouse 
SET status = 1,do_warehouse_id=".$warehouse_id." 
WHERE warehouse_id=".$warehouse_id." 
AND product_id IN (".$update_product.")";
                    $query2 = $db->query($sql2);
                    $sql2 = "UPDATE oc_product SET status = 1 WHERE product_id IN (".$update_product.")";
                    $query2 = $db->query($sql2);
                }
                /*zx
                周转筐记录*/
                $sql5 = "SELECT wrt.container_id 
FROM oc_x_warehouse_requisition_temporary wrt
LEFT JOIN oc_product ptw ON ptw.product_id = wrt.product_id
LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = wrt.warehouse_id AND oxw.warehouse_id =" . $warehouse_id . " 
WHERE wrt.warehouse_id = " . $warehouse_id . "
AND wrt.relevant_id IN (" . $relevant_ids2 . ")
AND wrt.relevant_status_id = 4
AND wrt.container_id > 0
GROUP BY wrt.container_id ";
                $query5 = $db->query($sql5);
                $results1 = $query5->rows;
                if ($results1) {
                    $sql3 = "INSERT INTO oc_x_container_fast_relevant_move (container_id,move_type,relevant_id,date_added,checked,status,add_w_user_id) VALUES ";
                    foreach ($results1 as $key => $container_id) {
                        $sql3 .= "(" . $container_id['container_id'] . ",'-1',".$relevant_ids2.",NOW(),1,1," . $added_by_id . "),";
                    }
                   
                    $sql3 = rtrim($sql3, ',');
                    $query3 = $db->query($sql3);
                }


                /*
                 *
                 * 出库操作*/
            } else {


                /*zx
                 * 获取周转筐中的所有商品，并进行商品分类统计数量*/
                $sql4 = "SELECT wrt.product_id,SUM(wrt.quantity) AS quantity,oxw.station_id,ptw.name AS product,ptw.price,wrt.container_id 
FROM oc_x_warehouse_requisition_temporary wrt
LEFT JOIN oc_product ptw ON ptw.product_id = wrt.product_id
LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = wrt.warehouse_id AND oxw.warehouse_id =" . $warehouse_id . " 
WHERE wrt.warehouse_id = " . $warehouse_id . "
AND wrt.relevant_id IN (" . $relevant_ids2 . ")
AND wrt.relevant_status_id = 2
GROUP BY wrt.product_id ORDER BY wrt.container_id";
       
                $query4 = $db->query($sql4);
                $results2 = $query4->rows;
                if (!$results2) {
                    return 0;
                }
                /*zx
                 * 修改调拨单状态*/

                $sql2 = "UPDATE oc_x_warehouse_requisition SET relevant_status_id = 4 WHERE relevant_id IN (" . $relevant_ids2 . ")";
                $query2 = $db->query($sql2);
                $this->insert_warehouse_requisition_history(4,$relevant_ids2,$added_by_id);
                /*zx
                出库记录*/
                $sql5 = "INSERT INTO oc_x_stock_move (station_id,timestamp,date_added,added_by,relevant_id,inventory_type_id,warehouse_id) VALUES (2,'" .
                    time() . "',NOW(),'" . $added_by_id . "','" . $relevant_ids2 . "','22','" . $warehouse_id . "')";
                $query5 = $db->query($sql5);
                $stock_move_id = $db->getLastId();
                $sql6 = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`,  `quantity`, `box_quantity`,  `status`) VALUES ";
                foreach ($results2 as $values) {
                    $sql6 .= "(" . $stock_move_id . ",2,NOW()," . $values['product_id'] . ",'" . $values['price'] . "','" . (0 - $values['quantity']) . "','1','1'),";
                }
                $sql6 = rtrim($sql6, ',');
                $query6 = $db->query($sql6);
                /*zx
                判断是否是仓间调拨单或退货调拨单，如果是修改前台库存*/
                if (trim($warehouse_out_type) == 2) {
                    $sql5 = "INSERT INTO oc_x_inventory_move (`relevant_id`,`station_id`, `date`, `timestamp`, `from_station_id`, `inventory_type_id`, `date_added`, `add_user_name`, `memo`,`warehouse_id`)
                    VALUES('" . $relevant_ids2 . "','2',current_date(),unix_timestamp(),'0','22',now(),'" . $added_by_id . "','仓间调拨单出库','" . $warehouse_id . "')";
                    $query5 = $db->query($sql5);
                    $inventory_move_id = $db->getLastId();
                    $sql6 = "INSERT INTO `oc_x_inventory_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `quantity`,`warehouse_id`) VALUES ";
                    foreach ($results2 as $values) {
                        $sql6 .= "(" . $inventory_move_id . ",2,NOW()," . $values['product_id'] . ",'" . (0 - $values['quantity']) . "','" . $warehouse_id . "'),";
                    }
                    $sql6 = rtrim($sql6, ',');
                    $query6 = $db->query($sql6);
                }
                /*zx
                周转筐记录*/
                $sql5 = "SELECT wrt.container_id 
FROM oc_x_warehouse_requisition_temporary wrt
LEFT JOIN oc_product ptw ON ptw.product_id = wrt.product_id
LEFT JOIN oc_x_warehouse oxw ON oxw.warehouse_id = wrt.warehouse_id AND oxw.warehouse_id =" . $warehouse_id . " 
WHERE wrt.warehouse_id = " . $warehouse_id . "
AND wrt.relevant_id IN (" . $relevant_ids2 . ")
AND wrt.relevant_status_id = 2
AND wrt.container_id > 0
GROUP BY wrt.container_id ";
                $query5 = $db->query($sql5);
                $results1 = $query5->rows;
                if ($results1) {
                    $sql3 = "INSERT INTO oc_x_container_fast_relevant_move (container_id,move_type,relevant_id,date_added,checked,status,add_w_user_id) VALUES ";
                    foreach ($results1 as $key => $container_id) {
                        $sql3 .= "(" . $container_id['container_id'] . ",'1',".$relevant_ids2.",NOW(),1,1," . $added_by_id . "),";
                    }
                    $sql3 = rtrim($sql3, ',');
                    $query3 = $db->query($sql3);
                }
            }
        }

        return 1;

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
    //出入库中间表提交
    public function submitRelevantProduct( array $data){
        global  $db;
        $relevant_id = isset($data['data']['relevant_id']) ? trim($data['data']['relevant_id']) : false;
        $warehouse_id = isset($data['data']['warehouse_id']) ? trim($data['data']['warehouse_id']) : false;
        $product_ids = isset($data['data']['product_ids']) ? $data['data']['product_ids'] : false;
        $quantitys = isset($data['data']['quantitys']) ? $data['data']['quantitys'] : false;
        $containers = isset($data['data']['containers']) ? $data['data']['containers'] : false;
        $relevant_status_id = isset($data['data']['relevant_status_id']) ? trim($data['data']['relevant_status_id']) : false;
//        return $data['data']['product_ids'];
//周转筐
        if (is_array($product_ids)) {
            //查找是否插入了临时表
            $sql1 = "SELECT wrt.relevant_id
              FROM oc_x_warehouse_requisition_temporary wrt  WHERE  wrt.relevant_id = '" . $relevant_id . "' AND wrt.product_id = '" . $product_ids[0] . "' AND wrt.relevant_status_id = '" . $relevant_status_id . "'  AND wrt.warehouse_id = '" . $warehouse_id . "' AND wrt.container_id =".$containers[0];
//            return $sql1;
            $query1 = $db->query($sql1);

            $result = $query1->num_rows;
            //是否插入中间表执行不同的sql
            if ($result > 0) {
                return 1;
            } else {
                //插入中间表
                $sql = "INSERT INTO oc_x_warehouse_requisition_temporary
( `relevant_id`,`relevant_status_id`,`product_id`,`quantity`,`warehouse_id`,`date_added`,`container_id`)
VALUES ";
                foreach ($product_ids as $key => $value) {
                    $sql .= "(".$relevant_id .",".$relevant_status_id.",".$value.",".$quantitys[$key].",".$warehouse_id.",NOW(),".$containers[$key]."),";
                }
                $sql = rtrim($sql,',');
                $query = $db->query($sql);
                $result_change = $query->row;
                return 2;
            }
//整件
        } else {
            //查找是否插入了临时表
            $sql1 = "SELECT wrt.relevant_id,wrt.quantity
              FROM oc_x_warehouse_requisition_temporary wrt  WHERE  wrt.relevant_id = '" . $relevant_id . "' AND wrt.product_id = '" . $product_ids . "' AND wrt.relevant_status_id = '" . $relevant_status_id . "'  AND wrt.warehouse_id = '" . $warehouse_id . "'";

            $query1 = $db->query($sql1);

            $result = $query1->num_rows;
            //是否插入中间表执行不同的sql
            if ($result > 0) {
                $sql = "UPDATE oc_x_warehouse_requisition_temporary wrt SET
quantity = ".$quantitys." WHERE  wrt.relevant_id = '". $relevant_id . "' AND wrt.product_id = '" . $product_ids . "' AND wrt.relevant_status_id = '" . $relevant_status_id . "'  AND wrt.warehouse_id = '" . $warehouse_id . "'";
                $query = $db->query($sql);
                return 2;
            } else {
                //插入中间表
                $sql = "INSERT INTO oc_x_warehouse_requisition_temporary
( `relevant_id`,`relevant_status_id`,`product_id`,`quantity`,`warehouse_id`,`date_added`,`container_id`)
VALUES ( ".$relevant_id.",".$relevant_status_id.",".$product_ids.",'".$quantitys."',".$warehouse_id.",NOW(),0)";
//                return $sql;
                $query = $db->query($sql);
                return 2;
            }
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


        foreach ($postData as $value){
            $product_id = substr($value[0],9);
            $quantity = $value[4]-$value[3];

            if($quantity == 0 ){
                return   2 ;
            }
        }



        $sql = "select   wr.relevant_status_id,wr.out_type
              from  oc_x_warehouse_requisition wr  WHERE wr.relevant_id = '".$relevant_id."'";

        $query = $db->query($sql);
        $result = $query->row;


     if($result['out_type'] == '3'){


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










     }else if($result['out_type'] == '2'){
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
                group by smi.product_id  ";

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

        //查找是否有当面退
        $sql_return_deliver = " select order_id , product_id , quantity , in_part , box_quantity  from  oc_return_deliver_product where order_id = '".$targetOrdersString ."' and product_id = '".$product_id ."'";


        $query = $db->query($sql_return_deliver);
        $return_deliver = $query->row;

        $sql_apply = " select ra.return_apply_id  from oc_return_apply ra left join oc_return_apply_product rap on ra.return_apply_id = rap.return_apply_id where ra.order_id = '".$targetOrdersString ."' and rap.product_id = '".$product_id."' and rap.quantity = '".$return_deliver['quantity']."' and  rap.in_part = '".$return_deliver['in_part']."' and rap.box_quantity = '".$return_deliver['box_quantity']."'  ";

        $query = $db->query($sql_apply);
        $return_apply = $query->row;

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

            if($return_apply['return_apply_id'] > 0 ){
                $returnCredits = 0 ;
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

                    if($return_apply['return_apply_id'] > 0 ){
                        $sql = "update oc_return_apply set return_id  = '".$return_id."' , return_by = '".$userId."' , date_returned = NOW() where return_apply_id = '".$return_apply['return_apply_id'] ."'";
                        $bool = $bool && $dbm->query($sql);
                    }

                    $bool = $bool && $dbm->query($sql);
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
            if(!empty($data['products'])){
                foreach ($data['products'] as $product) {

                    $sql_warehouse = " select  warehouse_id , do_warehouse_id , status ,do_warehouse_id  from  oc_product_to_warehouse where product_id  = '".$product['product_id']."' and warehouse_id = '".$warehouse_id ."'  ";
                    $query = $db->query($sql_warehouse);
                    $result = $query->row;

                    if($result['warehouse_id'] == $result['do_warehouse_id'] && $result['status'] == 0 ){

                        $sql_insert_status_history = " insert into oc_x_product_status_history (`product_id` , `status` , `date_added` , `add_user_name` , `order_id` , `do_warehouse_id`) VALUES  ('".$product['product_id']."' , '".$result['status']."', NOW(),'".$data_insert['add_user_name']."' , '".$data_insert['order_id']."','".$result['do_warehouse_id']."'   )";

                        $bool = $bool && $dbm->query($sql_insert_status_history);

//                        $sql_product = " update oc_product set status = 1 where product_id = '".$product['product_id']."' ";
//                        $bool = $bool && $dbm->query($sql_product);

                        $sql_product_warehouse = " update oc_product_to_warehouse set status = 1 ,warehouse_id = '".$warehouse_id."'  where warehouse_id = '".$warehouse_id."' and product_id = '".$product['product_id'] ."'  " ;

                        $bool = $bool && $dbm->query($sql_product_warehouse);

                    }

                }

            }

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
            $sql = " update oc_order_inv   set  frame_count = '".$frame_count."' , frame_vg_list= '".$frames_ids ."' ,frame_carton_list = '".$frame_carton_list."', frame_carton_count = '".$frame_carton_count."'  where  order_id = '".$order_id."' ";
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
                        ios.status = 1  and 
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
                $sql = " select  CONCAT(ptw.product_id,'/', p.name) product_id, ptw.product_id transfer_id  ,ptw.stock_area ,  ptw.sku_barcode , ssp.safe_quantity , ssp.quantity , ssp.capacity , if(ssp.status = 1 , '启用' , '停用') status , if(pmh.quantity = 0 , 0 ,  pmh.quantity ) inventory_quantity  , pmh.name  
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
                  set safe_quantity = '" . $input_safe_area_quantity . "'  ,  capacity = '" . $input_capacity_quantity . "' , quantity = '".$input_stock_area_quantity ."' where   warehouse_id =  '" . $warehouse_id . "'   and product_id = '" . $product_id_transfer . "' and stock_section_type_id = '".$warehouse_section_id."' ";

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
            $sql = "update   oc_x_container  set  instore =  1   , occupy = 0  , warehouse_id = '".$warehouse_id."' WHERE  container_id in ( ".$string.")    ";

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
        $sql = "update   oc_x_container  set  instore =  1  , occupy  =  0 , warehouse_id = '".$warehouse_id."' WHERE  container_id in ( ".$container_id.")   ";

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

    public  function checkContainer(array $data){

        global $db;

        $container_id = $data['data']['frame_vg_list'] ? $data['data']['frame_vg_list'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';

        $sql =  " select  container_id , occupy , instore from oc_x_container where container_id = '".$container_id."' and warehouse_id in ( ".$warehouse_id ." , 0  )";

        $query = $db->query($sql);
        $result = $query->row;
        return $result ;

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



//and do.warehouse_id = '".$warehouse_id."'
         $sql = "  select do.deliver_order_id  , do.order_id    from  oc_x_deliver_order do LEFT  JOIN  oc_x_deliver_order_inv doi on  do.deliver_order_id = doi.deliver_order_id left JOIN  oc_order_distr  od  on od.deliver_order_id = doi.deliver_order_id left join oc_order o on o.order_id = do.order_id  where (doi.frame_vg_list like '%,$count_id%' or  doi.frame_vg_list in ($count_id) ) and do.warehouse_id = '".$warehouse_id."'  and do.order_status_id in( 6,8) and do.order_deliver_status_id  in (1, 2)  order by doi.order_inv_id  desc limit 1 ";

        $query = $db->query($sql);
        $result = $query->row;

        $sql_dan = "select count(order_id) num  from oc_x_deliver_order where order_id =  '".$result['order_id']."' ";


        $query = $db->query($sql_dan);
        $result_dan = $query->row;

        if($result_dan['num'] == 1 ) {

            $sql = " select  ddo.deliver_order_id  order_id , od.inventory_name , doi.inv_comment , doi.frame_vg_list  ,  dos.name , sm.inventory_move_id   ,if(ddo.is_repack = 0  , '整单' , '散单') is_repack     from oc_x_deliver_order  ddo left join oc_order_distr od on ddo.order_id =od.order_id  left join  oc_x_deliver_order_inv doi  on  doi.order_id = ddo.order_id LEFT JOIN oc_x_deliver_order_status dos ON dos.order_status_id = ddo .order_status_id   left join oc_x_stock_move  sm on sm.order_id = ddo.order_id   where ddo.order_id  = '".$result['order_id']."'";

            $query = $db->query($sql);
            $result_dan = $query->rows;
            return $result_dan ;
        }else{
            $sql_deliver_order = "SELECT DO
	.deliver_order_id order_id ,
	od.inventory_name,
	doi.inv_comment,
	doi.frame_vg_list,
	dos.name ,
	if(DO.is_repack = 0  , '整单' , '散单') is_repack    , 
	sm.inventory_move_id   , 
		if(DO.is_repack = 0  , DO.order_status_id , DO.order_status_id) order_status_id    
FROM
	oc_x_deliver_order
DO
	LEFT JOIN oc_order_distr od ON DO
	.deliver_order_id = od
	.deliver_order_id 
AND DO
	.order_id = od.order_id
	LEFT JOIN oc_x_deliver_order_inv doi ON DO
	.deliver_order_id = doi.deliver_order_id
	LEFT JOIN oc_x_deliver_order_status dos ON dos.order_status_id = DO
	.order_status_id 
	left join oc_x_stock_move  sm on sm.order_id = DO.order_id 
WHERE
DO
	.order_id = '".$result['order_id']."'
	group by doi.deliver_order_id  ";


            $query = $db->query($sql_deliver_order);
            $result_deliver_order = $query->rows;
            return $result_deliver_order ;
        }


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

    private function  getDeliverOrderInfo($order_id){
        global $dbm;
        $sql = " select do.warehouse_id , do.do_warehouse_id , o.order_status_id , o.order_id ,o.station_id  from  oc_x_deliver_order do  LEFT  JOIN  oc_order  o  on  do.order_id = o.order_id where do.deliver_order_id = '".$order_id ."'";
        $query = $dbm->query($sql);
        $result = $query->row;
        return $result;
    }



    public  function  confirmOrderInfoByCount(array $data){
        global  $db ;
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $inventory_user = $data['data']['inventory_user']? $data['data']['inventory_user'] : '';
        $inv_comment = $data['data']['inv_comment']? $data['data']['inv_comment'] : '0';
        $result_order =  $this->getDeliverOrderInfo($order_id);

           //DO整单
        $sql_zheng =  "select order_status_id from oc_x_deliver_order where deliver_order_id = '".$order_id."'";
       
        $query = $db->query($sql_zheng);
        $result_zheng = $query->row;
        //
         //DO散单
         $sql_san = "  select  order_status_id  from oc_x_deliver_order where order_id = '".$result_order['order_id']."' and is_repack = 1 " ;
        $query = $db->query($sql_san);
        $result_san = $query->row;
       if($result_zheng['order_status_id']  == 6 && $result_san['order_status_id'] == 6){

//           $sql_deliver_order = " select order_status_id  from  oc_x_deliver_order where  deliver_order_id = '".$order_id ."' ";
//           $query = $db->query($sql_deliver_order);
//           $result_move = $query->row;
//
//           if($result_move['order_status_id'] !=6){
//               return false ;
//           }

           // 判断是否写入了stock_move
           $sql_move =  " select order_id  from  oc_x_stock_move  where  order_id = '".$result_order['order_id']."' ";

           $query = $db->query($sql_move);
           $result_move = $query->row;

           if($result_move['order_id'] > 0 ){

               $sql = " update  oc_order set order_status_id = 6 where order_id =  '". $result_order['order_id'] ."'  ";
               $query = $db->query($sql);
               return false ;
           }

//           // 判断是否有货位号
//
//           $sql_comment = "select doi.deliver_order_id , doi.inv_comment   from oc_x_deliver_order_inv doi left join oc_x_deliver_order do on doi.deliver_order_id = do.deliver_order_id  where doi.order_id = '".$result_order['order_id']."' and do.is_repack = 0   ";
//
//           $query = $db->query($sql_comment);
//           $result_comment = $query->row;
//           if($result_comment['inv_comment'] ==0){
//               return false ;
//           }
           //准备合单写入oc_x_stock_move表
           $sql =   "SELECT
                xis.product_id,
                sum(xis.quantity) quantity,
                sum(op.quantity)  order_quantity ,
              round(sum(op.price * op.quantity) / sum(op.quantity) , 2)  as price,
                op.weight_inv_flag,
                p.sku_id
            FROM
                oc_x_inventory_order_sorting AS xis
            LEFT JOIN oc_order_product AS op ON op.order_id = xis.order_id
            AND op.product_id = xis.product_id
            LEFT JOIN oc_product AS p ON p.product_id = xis.product_id
            WHERE
            xis.status =1  and 
                 xis.order_id =  '".$result_order['order_id']."'
             group by  xis.product_id ";

           $query = $db->query($sql);
           $result = $query->rows;
           $stationProductMove = array();






           if(sizeof($result)) {
               foreach ($result as $k => $v) {
                   $stationProductMove[] = array(
                       'product_batch' => '',
                       'due_date' => '0000-00-00', //There is a bug till year 2099.
                       'product_id' => $v['product_id'],
                       'special_price' => $v['price'],
                       'qty' => abs(min($v['quantity'],$v['order_quantity'])),
                       'product_weight' => 0,
                       'sku_id' => $v['sku_id']
                   );
               }

               $data_inv['products'] = $stationProductMove;

               $inventory_op =  -1 ;
//            $result = $this->addInventoryMoveOrder($data_inv, 1,$warehouse_id);

               $sql_stock_move = " insert into oc_x_stock_move  ( `station_id` , `timestamp` ,`order_id` , `inventory_type_id` , `date_added` , `add_user_name` , `warehouse_id` )  VALUES  ('2' ,unix_timestamp(), '".$result_order['order_id']."' , '12' , NOW() , '".$inventory_user."' , '".$warehouse_id ."' )";

               $query = $db->query($sql_stock_move);
               $stock_move_id = $db->getLastId();

               if(!empty($data_inv['products'])){
                   $sql = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`) VALUES ";
                   $m = 0;
                   foreach ($data_inv['products'] as $product) {
                       $sql .= "(" . $stock_move_id . ",  2 , '" . (isset($product['due_date']) ? $product['due_date'] : '0000-00-00') . "', '" . $product['product_id'] . "', '" . $product['special_price'] . "', '" . (isset($product['product_batch']) ? $product['product_batch'] : '') . "', " . $product['qty'] * $inventory_op . ", '".(isset($product['box_quantity']) ? $product['box_quantity'] : 1)."', " . (isset($product['product_weight']) ? $product['product_weight'] : 0) . "," . (isset($product['is_gift']) ? $product['is_gift'] : 0) . ", " . (isset($product['checked']) ? $product['checked'] : 0) . "," . (isset($product['status']) ? $product['status'] : 1) . "," . (isset($product['sku_id']) ? $product['sku_id'] : 0) . ")";

                       if (++$m < sizeof($data_inv['products'])) {
                           $sql .= ', ';
                       } else {
                           $sql .= ';';
                       }
                   }

                   //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存明细表' . "\n\r");
                   $bool =  $db->query($sql);
               }


               //添加分拣缺货至退货表
               $this->addReturn(array($result_order['order_id']),$warehouse_id);

               if($bool){
                   $db->begin();
                   $bool = true;


                    $sql = "update oc_x_inventory_order_sorting set move_flag = 1 where order_id = '". $result_order['order_id']."'  and status = 1 ";
                    // $log->write('[分拣]更新分拣数据提交状态[' . __FUNCTION__ . ']'.$sql."\n\r");
                    $bool = $bool && $db->query($sql);




                   $sql = "update oc_order set order_status_id = 6 where order_id = '". $result_order['order_id'] ."'";
                   // $log->write('[分拣]更新订单状态为已拣完[' . __FUNCTION__ . ']'.$sql."\n\r");
                   $bool = $bool && $db->query($sql);

                   $sql = "select order_id  from oc_order_inv where order_id = '".$result_order['order_id'] ."' ";
                   $query = $db->query($sql);
                   $result = $query->row;

                   if($result['order_id'] > 0 ){
                       $bool =  true ; 
                   }else {
                       $sql2 = "select  doi.frame_vg_list ,doi.frame_count from oc_x_deliver_order do left join oc_x_deliver_order_inv doi on do.order_id = doi.order_id where do.order_id = '" . $result_order['order_id'] . "' and do.deliver_order_id = doi.deliver_order_id and do.is_repack = 1  ";
                       $query = $db->query($sql2);
                       $result2 = $query->row;

                       $sql_box_count = "select sum(box_count) box_count from oc_x_deliver_order_inv  where order_id = '".$result_order['order_id']."' group  by order_id  ";
                       $query = $db->query($sql_box_count);
                       $result_box_count = $query->row;


                       if($inv_comment >0){
                           $sql_order_inv = " insert into oc_order_inv (`order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,`box_count` ,`inv_comment` , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` ) (select `order_id` , '" . $result2['frame_count'] . "' ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,'" . $result2['frame_vg_list'] . "' ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,'".$result_box_count['box_count']."' ,  '".$inv_comment ."' , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` from oc_x_deliver_order_inv   where deliver_order_id = '" . $order_id . "'  ) ";
                       }else{
                           $sql_order_inv = " insert into oc_order_inv (`order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,`box_count` ,`inv_comment` , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` ) (select `order_id` , '" . $result2['frame_count'] . "' ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,'" . $result2['frame_vg_list'] . "' ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,'".$result_box_count['box_count']."' ,  `inv_comment` , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` from oc_x_deliver_order_inv   where deliver_order_id = '" . $order_id . "'  ) ";
                       }



                       $bool = $bool && $db->query($sql_order_inv);

                   }
                   if(!$bool) {
                       //$log->write('[分拣]分拣提交失败[' . __FUNCTION__ . ']' . "\n\r");
                       $db->rollback();

                       return array('status' => 0);
                   }
                   else {
                       //$log->write('[分拣]分拣提交成功[' . __FUNCTION__ . ']' . "\n\r");
                       $db->commit();

                       $sql_consoli= " insert into oc_x_consolidated_order (`order_id` , `uptime` , `added_by` , `warehouse_id` ) VALUES ('".$result_order['order_id']."' , NOW(),'".$inventory_user."' , '".$warehouse_id."' )" ;
                       $query = $db->query($sql_consoli);

                       return array('status' => 1);
                   }
               }
           }
       }else {

//           $sql = "update oc_x_deliver_order  set  order_status_id = 12  where order_id = '".$result_order['order_id']."'";
//           $query = $db->query($sql);



//           $sql_consoli= " insert into oc_x_consolidated_order (`order_id` , `uptime` , `added_by` , `warehouse_id` ) VALUES ('".$result_order['order_id']."' , NOW(),'".$inventory_user."' , '".$warehouse_id."' )" ;
//           $query = $db->query($sql_consoli);
//
//           return array('status' => 2);
       }


    }

  
    
    //分拣缺货
    private function addReturn($data , $warehouse_id) {
        global $db, $dbm;
        $date = date("Y-m-d");
        if(!sizeof($data) || !is_array($data)){
            return false; //为空直接返回
        }

        //$data = json_decode($data, 2);
        // 期望的数据，需要添加退货的订单号: $data = array(10001,10002,...);
        // 由分拣人员提交的数据将分拣人员信息写入备注

        // 计算订单应收金额，缺货金额
        // 计算缺货金额
        // 应收金额 >= 缺货金额,  仅退货
        // 应收金额 < 缺货金额,  退余额＝缺货金额－应收金额，实际应收为0，退余额
        $targetOrdersString = implode(',',$data);

        //查找订单应付
        $sql = "select order_id, sum(if(accounting = 1, value, 0)) due_total,  sum(if((accounting = 1 and value<0) or code = 'credit_paid' , value, 0)) paid_total from oc_order_total where order_id in (".$targetOrdersString.") group by order_id";
        $query = $db->query($sql);
        $dueInfo = $query->rows;
        $dueInfoList = array();
        foreach($dueInfo as $m){
            $dueInfoList[$m['order_id']] = $m;
        }

        //查找实际出库数据, 分拣数据为扣减库存，是负数，可能有多行，需合并计算
        $sql = "select o.order_id, oi.product_id, sum(oi.quantity) quantity
                from oc_x_stock_move o
                left join oc_x_stock_move_item oi on o.inventory_move_id = oi.inventory_move_id
                where o.order_id in (".$targetOrdersString.") and o.inventory_type_id = 12 group by o.order_id, oi.product_id";
        $query = $db->query($sql);
        $stockMoveInfo = $query->rows;
        $stockMoveInfoList = array();
        foreach($stockMoveInfo as $m){
            $stockMoveInfoList[$m['order_id']][$m['product_id']] = $m['quantity'];
        }

        //查找订单数据, 仅处理分拣中或待审核，且未配送出库的订单，此步骤执行成功后订单将变为已分拣，防止重复执行
        $sql = "select o.order_id, date(o.date_added) date_ordered, o.customer_id, op.product_id,
                op.name, sum(op.quantity) quantity  ,round(sum(op.quantity * op.price) /sum(op.quantity),2 ) price ,  sum(op.quantity * op.price) total
                from oc_order o
                left join oc_order_product op on o.order_id = op.order_id
                where o.order_id in (".$targetOrdersString.")   group by op.product_id ";
        $query = $db->query($sql);
        $orderInfo = array();
        foreach($query->rows as $m){
            $orderInfo[$m['order_id']][$m['product_id']] = $m;
            $orderInfoList[$m['order_id']]['customer_id'] = $m['customer_id'];
            $orderInfoList[$m['order_id']]['date_ordered'] = $m['date_ordered'];
        }
        $returnInfo = array();

        //整理退货信息，$data为传入的订单号
        foreach($data as $m){
            //无订单或分拣信息跳过
            if(!isset($stockMoveInfoList[$m]) || !isset($orderInfo[$m])){
                continue;
            }

            //整理退货表信息
            $returnInfo[$m]['order_id'] = $m;
            $returnInfo[$m]['customer_id'] = $orderInfoList[$m]['customer_id'];
            $returnInfo[$m]['date_ordered'] = $orderInfoList[$m]['date_ordered'];
            $returnInfo[$m]['added_by'] = 0;
            $returnInfo[$m]['comment'] = '[系统]分拣缺货';
            $returnInfo[$m]['return_reason_id'] = 1; //TODO, 目前为缺货未出库，待处理其他类型
            $returnInfo[$m]['return_action_id'] = 1; //TODO, 目前为无操作，若有退余额，更改类型
            $returnInfo[$m]['return_status_id'] = 2; //默认未确认，这里设置已确认，分拣缺货将直接写入系统，无需再确认
            $returnInfo[$m]['return_inventory_flag'] = 0; //分拣缺货不需要退库存
            $returnInfo[$m]['credits_returned'] = 1; //分拣缺货不论是否退余额，该状态设置为已退

            $returnInfo[$m]['due_total'] = $dueInfoList[$m]['due_total'];
            $returnInfo[$m]['paid_total'] = $dueInfoList[$m]['paid_total'];
            $returnInfo[$m]['return_credits'] = 0; //以下将重新计算退货金额，和订单应付比对作为余额退款依据
            $returnInfo[$m]['return_total'] = 0; //退货合计金额
            $returnInfo[$m]['return_qty_total'] = 0; //退货合计数量


            //匹配分拣数量，分拣数据为扣减库存，是负数，这里转换为正数
            foreach($orderInfo[$m] as $n){
                $productStockMoveQty = 0;
                if(isset($stockMoveInfoList[$m][$n['product_id']])){
                    $productStockMoveQty = abs($stockMoveInfoList[$m][$n['product_id']]);
                }

                if($n['quantity'] > $productStockMoveQty){
                    //$returnProductInfo[$n['product_id']] = $n;
                    $returnInfo[$m]['products'][$n['product_id']]['product_id'] = $n['product_id'];
                    $returnInfo[$m]['products'][$n['product_id']]['name'] = $n['name'];
                    $returnInfo[$m]['products'][$n['product_id']]['price'] = $n['price'];
                    $returnInfo[$m]['products'][$n['product_id']]['return_qty'] = $n['quantity'] - $productStockMoveQty;
                    $returnInfo[$m]['products'][$n['product_id']]['return_total'] = ($n['quantity'] - $productStockMoveQty) * $n['price'];

                    $returnInfo[$m]['return_total'] += $returnInfo[$m]['products'][$n['product_id']]['return_total'];
                    $returnInfo[$m]['return_qty_total'] += $returnInfo[$m]['products'][$n['product_id']]['return_qty'];
                }
            }
        }


        //依次处理多个退货信息
        $bool = true;


        foreach($returnInfo as $m){

            //若退货数量为0，跳过
            if($m['return_qty_total'] == 0){
                continue;
            }

            // 应退余额 ＝ 缺货值 > 应付值 ? (缺货值-应付值) : 0
            // 应退余额 = 支付金额 > 应退余额 ? 应退余额 : 支付金额;
            // TODO 问题，出库金额小于优惠金额时，白送？
            $dueTotal = $returnInfo[$m['order_id']]['due_total'];
            $paidTotal = abs($returnInfo[$m['order_id']]['paid_total']);
            $returnTotal = $returnInfo[$m['order_id']]['return_total'];

            $returnCredits = ($returnTotal > $dueTotal) ? ($returnTotal - $dueTotal) : 0;
            $returnCredits = ($paidTotal > $returnCredits) ? $returnCredits : $paidTotal; //退货金额不大于支付金额（微信＋余额支付合计）
            $returnActionId = ($returnCredits > 0) ? $m['return_action_id'] : 3;

            //写入退货表
            $sql = "INSERT INTO `oc_return` (`order_id`, `customer_id`, `return_reason_id`, `return_action_id`, `return_status_id`, `comment`, `date_ordered`, `date_added`, `date_modified`, `add_user`, `return_credits`, `return_inventory_flag`, `credits_returned`)
                    VALUES(
                        '".$m['order_id']."',
                        '".$m['customer_id']."',
                        '".$m['return_reason_id']."',
                        '".$returnActionId."',
                        '".$m['return_status_id']."',
                        '".$m['comment']."',
                        '".$m['date_ordered']."',
                        NOW(),
                        NOW(),
                        '".$m['added_by']."',
                        '".$returnCredits."',
                        '".$m['return_inventory_flag']."',
                        '".$m['credits_returned']."')";

            $bool = $bool && $dbm->query($sql);
            $return_id = $dbm->getLastId();

            $sql = "INSERT INTO `oc_return_product` (`return_id`, `product_id`, `product`,  `quantity`, `in_part`, `box_quantity`, `price`, `total`, `return_product_credits`) VALUES";
            $n = 0;
            //TODO, 目前仅处理出库缺货，in_part＝0， box_quantity＝1
            foreach ($returnInfo[$m['order_id']]['products'] as $product) {
                $sql .= "(
                    '".$return_id."',
                    '".$product['product_id']."',
                    '".$product['name']."',
                    '".$product['return_qty']."',
                    '0',
                    '1',
                    '".$product['price']."',
                    '".$product['return_total']."',
                    '".$product['return_total']."'
                    )";
                if (++$n < sizeof($returnInfo[$m['order_id']]['products'])) {
                    $sql .= ', ';
                } else {
                    $sql .= ';';
                }
            }
            $bool = $bool && $dbm->query($sql);



            //退余额
            if($returnCredits > 0 ){
                $sql = "INSERT INTO oc_customer_transaction SET added_by = '".$m['added_by']."', customer_id = '" . $m['customer_id'] . "', order_id = '" . $m['order_id'] . "', description = '[系统]分拣缺货退款', amount = '" . $returnCredits . "', customer_transaction_type_id = '9', date_added = NOW(), return_id = '" . $return_id . "'";
                $bool = $bool && $dbm->query($sql);
            }
        }

        return $bool;
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

        $sql = "select ss.pallet_number , w.username  , ss.date_added  ,ss.stock_check_id stock_section_id   from  oc_x_stock_checks  ss LEFT  JOIN  oc_w_user w on  ss.added_by = w.user_id   where  ss.warehouse_id = '".$warehouse_id ."' ";

        if($pallet_number !=''){
            $sql .= "  and ss.pallet_number   = '". $pallet_number ."' ";
        }



        $query = $db->query($sql);
        $result = $query->rows;

        return $result;
    }


    public function  deleteStockChecks(array $data ){
        global $db;
        $pallet_number = $data['data']['stock_check_id']? $data['data']['stock_check_id'] : '';

            $sql = " delete  from oc_x_stock_checks where stock_check_id  = '".$pallet_number."'";
            $query = $db->query($sql);
            $sql = " delete  from oc_x_stock_checks_item where stock_check_id  = '".$pallet_number."'";
            $query = $db->query($sql);

            if($query){
                return 1 ;
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
        $sql = " select  stock_check_id  from  oc_x_stock_checks where pallet_number = '".$pallet_number ."' ";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['stock_check_id'] >0){
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
        $sql = " select sc.pallet_number , ws.name , w.username ,sc.status ,ws.code , sc.stock_check_id from oc_x_stock_checks sc LEFT join oc_x_warehouse_section ws on sc.stock_check_category = ws.warehouse_section_id LEFT  JOIN oc_w_user w on sc.added_by = w.user_id  where sc.stock_check_id = '".$pallet_number."'   ";

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
              //  if ($result_id['relevant_id'] > 0) {
                //    return 3;
             //   } else {
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
         //   }
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
        $sql = "select stock_check_id ,  status ,pallet_number from  oc_x_stock_checks  where stock_check_id   =  '". $pallet_number ."'";

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
         //   if($result_id['relevant_id'] > 0 ){
       //         return 3 ;
      //      }else {
                $sql = "insert  into oc_x_stock_move
              (`relevant_id`,`station_id`,`timestamp`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id` ,`memo` )
              VALUES ('" . $result['stock_check_id'] . "' ,'2',UNIX_TIMESTAMP(NOW()), '22' ,NOW(),'" . $add_user . "' ,'" . $warehouse_id . "','搬仓出库')";


                $query = $db->query($sql);

                $inventory_move_id = $db->getLastId();

                if (!empty($results)) {
                    $sql = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `product_id`, `quantity`, `box_quantity`, `status`) VALUES ";
                    $m = 0;
                    foreach ($results as $product) {
                        $inventory_op = -1;
                        $sql .= "(" . $inventory_move_id . ", " . 2 . ",  '" . $product['product_id']  . "', '" . $product['quantity']*(-1) . "', 1 , 1 )";

                        if (++$m < sizeof($results)) {
                            $sql .= ', ';
                        } else {
                            $sql .= ';';
                        }
                    }

                    $query = $db->query($sql);

                }
                if ($query) {

                    $sql = " update oc_x_stock_checks set  status = 2   where  stock_check_id  = '" . $pallet_number . "'";

                    $query = $db->query($sql);
                    return 2;
                }
           // }
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
        $status = array();
        //判断退货是否确认
        $sql = " select order_id , return_id ,confirmed  from oc_return_deliver_product  WHERE  warehouse_id = '".$warehouse_id."' and product_id = '".$product_id ."'  and confirmed = 0  ";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['confirmed'] == 0  && $result['order_id'] > 0 ) {

            $status1 = 1  ;

        }else{

            //判断是退货是否上架
//            $sql =  "select  return_confirmed  , product_id  from oc_return_product  where warehouse_id = '".$warehouse_id."' and product_id = '".$product_id ."'  and return_confirmed = 0     ";

            $sql = " select   rp.return_confirmed  , rp.product_id  from  oc_return  r  LEFT  JOIN  oc_order o  on r.order_id = o.order_id  LEFT  JOIN  oc_return_product rp on r.return_id = rp.return_id  LEFT JOIN  oc_return_deliver_product rdp  ON r.return_id = rdp.return_id   where o.warehouse_id = '".$warehouse_id ."'    and rp.product_id = '".$product_id ."'  and rp.return_confirmed = 0   and  rdp.is_repack_missing = '0'
                and  rdp.is_back = '1 ' " ;


            $query = $db->query($sql);
            $result = $query->row;

            if($result['return_congifmed'] ==  0  && $result['product_id'] > 0 ){
                $status2 = 2 ;
            }


        }

        //判断采购单是否录入仓库分区
        $sql = " select ppo.purchase_order_id , ppop.product_id  from oc_x_pre_purchase_order  ppo  LEFT  JOIN  oc_x_pre_purchase_order_product ppop on  ppo.purchase_order_id = ppop.purchase_order_id  where ppop.warehouse_id = '".$warehouse_id."' and ppop.product_id = '".$product_id ."' and ppo.status  = '4' ";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['purchase_order_id'] > 0 ){
            $sql = " select stock_section_id from  oc_x_stock_section_product_move where purchase_order_id = '".$result['purchase_order_id']."'  ";

            $query = $db->query($sql);
            $result = $query->row;

            if($result['stock_section_id'] > 0 ) {
                $status3  = 3;
            }else {
                $status4  = 4;
            }
        }

        return $status = array ($status1,$status2,$status3,$status4) ;

    }

    function   deleteStockSectionPorduct(array $data){
        global $db;
        $stock_section_id = $data['data']['stock_section_id']? $data['data']['stock_section_id'] : '';
        $product_id = $data['data']['product_id']? $data['data']['product_id'] : '';

        $sql = " delete  from oc_x_stock_checks_item where stock_check_id  = '".$stock_section_id."' and product_id = '".$product_id."'";
        $query = $db->query($sql);

        if($query){
            return 1 ;
        }
    }

    function  getInventoryOrderSoring(array $data){
        global $db;
        $order_id= $data['data']['order_id']? $data['data']['order_id'] : '';
        $container_id = $data['data']['container_id']? $data['data']['container_id'] : '';
        $user_id = $data['data']['user_id']? $data['data']['user_id'] : '';

        $sql  =  "select doo.warehouse_id , doo.do_warehouse_id ,doo.order_id , doi.frame_vg_list   from oc_x_deliver_order  doo left join  oc_x_deliver_order_inv doi on doo.deliver_order_id = doo.deliver_order_id  where doi.deliver_order_id = '".$order_id."'" ;

        $query = $db->query($sql);
        $result = $query->row;

        $sql1 = "select inventory_sorting_id  from  oc_x_inventory_order_sorting   where deliver_order_id = '".$order_id."' and container_id = '".$container_id."' and status =1 ";

        $query = $db->query($sql1);
        $result1 = $query->row;

        $sql_inventory =   "select inventory_move_id  from oc_x_stock_move where order_id = '".$result['order_id']."' and inventory_type_id = 12  ";

        $query = $db->query($sql_inventory);
        $result_inventory = $query->row;

        if( $result_inventory['inventory_move_id'] > 0  ){

            return 1 ;

        }else{
            if($result1['inventory_sorting_id'] > 0 ){


                $arr_container = explode(',', $result['frame_vg_list']);

                foreach( $arr_container as $k=>$v) {
                    if($container_id == $v) unset($arr_container[$k]);
                }
                $arr = array_values($arr_container);
                $arr_count  = count($arr);
                $string_container = implode(',',$arr);

                $sql_inv  = " update  oc_x_deliver_order_inv set frame_vg_list = '".$string_container ."' , frame_count =  '".$arr_count."'where  deliver_order_id = '".$order_id."' ";
                $query = $db->query($sql_inv);

                $sql  =  " update oc_x_container set  occupy = 0 where  container_id = '".$container_id ."'";
                $query = $db->query($sql);

                $sql_history = "  insert into oc_x_container_history  (`container_id` , `status`, `type` ,`instore` , `warehouse_id` , `occupy`,`date_added` , `added_by`) (select container_id , status , type , instore ,warehouse_id , occupy , NOW() ,  '".$user_id."' from  oc_x_container  where container_id = '".$container_id ."') ";
                $query = $db->query($sql_history);

                $sql_sorting = "update  oc_x_inventory_order_sorting  set status = 0 where deliver_order_id = '".$order_id ."' and container_id  = '".$container_id ."'";

                $query = $db->query($sql_sorting);


                $sql_status =  " update  oc_x_deliver_order set order_status_id = 5 where deliver_order_id = '".$order_id."'";
                $query = $db ->query($sql_status);

                return 2 ;
            }
        }

    }

    function  addOrderInvComment(array $data){
        global $db;
        $deliver_order_id= $data['data']['deliver_order_id']? $data['data']['deliver_order_id'] : '';
        $invComment = $data['data']['invComment']? $data['data']['invComment'] : '';

        $sql = "select order_id  from  oc_x_deliver_order where deliver_order_id = '".$deliver_order_id ."' ";

        $query = $db->query($sql);
        $result = $query->row;

        $sql_update = "update  oc_order_inv  set  inv_comment = '".$invComment."'  where order_id = '".$result['order_id']."' ";
        $query = $db->query($sql_update);

        if($query){
            return 1 ; 
        }else {
            return 2 ;
        }

    }


    function getAllotDoOrder(array  $data){
        global $db;
        $deliver_date = $data['data']['date']? $data['data']['date'] : '';
        $order_status_id = $data['data']['order_status_id']? $data['data']['order_status_id'] : '';
        $do_relevant_id = $data['data']['do_relevant_id']? $data['data']['do_relevant_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $do_warehouse_id = $data['data']['do_warehouse_id']? $data['data']['do_warehouse_id'] : '';

        $sql = "select odo.deliver_order_id , odo.deliver_date , dos.name , odo.relevant_id  , odo.order_status_id  , w.title ,doi.inv_comment  from  oc_x_deliver_order  odo  left join  oc_x_deliver_order_status dos on odo.order_status_id =  dos.order_status_id   left join oc_x_warehouse w on w.warehouse_id = odo.warehouse_id  left join oc_x_deliver_order_inv doi on doi.deliver_order_id = odo.deliver_order_id   where   odo.deliver_date = '". $deliver_date ."'  and odo.warehouse_id = '".$warehouse_id."' and odo.do_warehouse_id = '".$do_warehouse_id ."' and  odo.order_status_id != 3  ";

        if($order_status_id != 0 ){
            $sql .= "  and odo.order_status_id = '". $order_status_id ."' ";
        }

        if($do_relevant_id == 0){
            $sql .= "  and  odo.relevant_id  = 0 ";
        }else{
            $sql .= " and odo.relevant_id > 0" ;
        }
        $sql .=  "  order by  doi.inv_comment  desc ";
        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }

    function getDoOrderStatus(){
        global $db;
        $sql = "select * from  oc_x_deliver_order_status   ";
        $query = $db->query($sql);
        $result = $query->rows;
        return $result;
    }
    
        function  addDoOrderRelevant(array $data){
        global $db;
        $check_value = $data['data']['check_value']? $data['data']['check_value'] : '';
        $user_id = $data['data']['user_id']? $data['data']['user_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $do_warehouse_id = $data['data']['do_warehouse_id']? $data['data']['do_warehouse_id'] : '';
        $sql = " insert into  oc_x_warehouse_requisition (`relevant_status_id` , `from_warehouse` , `to_warehouse`, `date_added` , `added_by` ,`status` ,  `out_type` ,`deliver_date` ) values( 2, '".$do_warehouse_id."' , '".$warehouse_id."' , NOW(),'".$user_id."' ,  2 , '1'  , NOW() )";

        $query = $db->query($sql);
        $requ_id = $db->getLastId();
        $check_value_list = implode(',',$check_value);


//        $sql = "select dop.product_id , ios.container_id , dop.quantity  , 'X' from  oc_x_deliver_order_product dop  LEFT  join  oc_x_inventory_order_sorting   ios on dop.order_id  = ios.order_id  and  dop.product_id = ios.product_id   where dop.deliver_order_id in ($check_value_list)  and ios.container_id > 0 ";
//
//        $query = $db->query($sql);
//        $result = $query->rows;

        $sql_item = "  insert into oc_x_warehouse_requisition_item ( `relevant_id` , `product_id` , `container_id`  , `num` , `container_x`  , `date_added`)   (select '".$requ_id."' ,  dop.product_id , ios.container_id , dop.quantity  , 'X'  , NOW() from  oc_x_deliver_order_product dop  LEFT  join  oc_x_inventory_order_sorting   ios on dop.order_id  = ios.order_id  and  dop.product_id = ios.product_id   where ios.status = 1 and  dop.deliver_order_id in ($check_value_list)  and ios.container_id > 0  )";

        $query = $db->query($sql_item);

        if($query){
            $sql_item = "  insert into oc_x_warehouse_requisition_item ( `relevant_id` , `product_id` , `container_id`  , `num` , `date_added`  )   (select '".$requ_id."' ,  dop.product_id , ios.container_id , sum(dop.quantity) quantity    , NOW()  from  oc_x_deliver_order_product dop  LEFT  join  oc_x_inventory_order_sorting   ios on dop.order_id  = ios.order_id  and  dop.product_id = ios.product_id   where ios.staatus = 1 and  dop.deliver_order_id in ($check_value_list)  and ios.container_id <= 0  group by dop.product_id )";
            $query = $db->query($sql_item);

            if($query){
                 $sql =  " update  oc_x_deliver_order   set  relevant_id = '".$requ_id ."'  where deliver_order_id in ($check_value_list) ";
                 $query = $db->query($sql);

                $sql_status =  " update  oc_x_deliver_order set order_deliver_status_id  = '2'  where deliver_order_id in ($check_value_list) ";
                $query = $db->query($sql_status);

                 if($query){
                     return 1 ;
                 }else{
                     return 2 ;
                 }
            }
        }
    }

    function getRelevantInfoByCount(array  $data ){
        global $db;

        $count_id = $data['data']['count_id']? $data['data']['count_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $relevant_id = $data['data']['relevant_id']? $data['data']['relevant_id'] : '';

        $sql_relevant_id =  "select wr.relevant_id  from oc_x_warehouse_requisition  wr left join  oc_x_warehouse_requisition_item  wri  on   wri.relevant_id = wr.relevant_id  where wri.container_id = '".$count_id ."'  and   wr.status = 2 and wr.relevant_status_id = 6  and wr.to_warehouse = '".$warehouse_id ."'  and wr.relevant_id = '".$relevant_id ."' group  by  wr.relevant_id order by wr.relevant_id  desc  limit 1  ";

        $query = $db->query($sql_relevant_id);
        $result_relevant_id = $query->row;

        if($result_relevant_id['relevant_id'] > 0 ){
            $sql_order_id =  "  select doo.order_id  , doo.deliver_order_id  from  oc_x_deliver_order  doo  left join  oc_x_inventory_order_sorting  ios  on  doo.order_id = ios.order_id  where ios.status = 1  and  doo.relevant_id = '".$result_relevant_id['relevant_id'] . "'  and  doo.warehouse_id =  '".$warehouse_id."'  and  ios.container_id = '".$count_id ."'  group by  doo.order_id";

            $query = $db->query($sql_order_id);
            $result_order_id  = $query->row;

            if($result_order_id['order_id'] > 0 ){
                $sql_info = " select   ios.container_id  , ios.order_id , ios.deliver_order_id  , ios.product_id ,  doi.inv_comment   , (sum(ios.quantity ) - sum( if(dos.quantity is  null   , 0 , dos.quantity) ) )  quantity  ,  p.name   from  oc_x_inventory_order_sorting ios  LEFT JOIN oc_x_deliver_order_inv doi ON doi.order_id = ios.order_id 
	AND doi.box_count <= 0   left join  oc_product  p  on  p.product_id = ios.product_id  left join  oc_x_inventory_deliver_order_sorting dos on dos.order_id = ios.order_id and ios.product_id = dos.product_id   where ios.status = 1 and  ios.order_id = '".$result_order_id['order_id']."'  and ios.deliver_order_id = '".$result_order_id['deliver_order_id']."'  and ios.container_id  <=0  group by   ios.product_id  order by  quantity desc  ";

                $query = $db->query($sql_info);
                $result_info1   = $query->rows;

                $sql_info2 = " select   ios.container_id  , ios.order_id , ios.deliver_order_id  , ios.product_id ,  doi.inv_comment   , (1 - if(dos.container_id is not null  , 1 , 0 )) quantity    ,  p.name   from  oc_x_inventory_order_sorting ios  LEFT JOIN oc_x_deliver_order_inv doi ON doi.order_id = ios.order_id 
	AND doi.box_count <= 0   left join  oc_product  p  on  p.product_id = ios.product_id  left join  oc_x_inventory_deliver_order_sorting dos on dos.order_id = ios.order_id and ios.container_id = dos.container_id   where ios.status =1 and  ios.order_id = '".$result_order_id['order_id']."'  and ios.deliver_order_id = '".$result_order_id['deliver_order_id']."'  and ios.container_id  >0    group by  ios.container_id   order by  quantity desc ";

                $query = $db->query($sql_info2);
                $result_info2   = $query->rows;

                $sql_dan =  "select count(order_id) num  from oc_x_deliver_order where order_id  = '".$result_order_id['order_id']."' group by order_id   ";
                $query = $db->query($sql_dan);
                $result_dan    = $query->row;
                if($result_dan['num'] == 1){
                    $sql_comment =  " select doi.inv_comment  from oc_x_deliver_order doo left join oc_x_deliver_order_inv  doi on doo.deliver_order_id = doi.deliver_order_id where    doo.order_id = '".$result_order_id['order_id']."'";

                    $query = $db->query($sql_comment);
                    $result_comment   = $query->row;
                }else{
                    $sql_comment =  " select doi.inv_comment  from oc_x_deliver_order doo left join oc_x_deliver_order_inv  doi on doo.deliver_order_id = doi.deliver_order_id where doo.warehouse_id = doo.do_warehouse_id and  doo.order_id = '".$result_order_id['order_id']."'";

                    $query = $db->query($sql_comment);
                    $result_comment   = $query->row;
                }


                return $result_info = array ($result_info1,$result_info2,$result_comment);
            }

        }


    }


    function  confirmDoRelevant(array $data ){
        global $db;
        $id = $data['data']['id']? $data['data']['id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $user_id= $data['data']['user_id']? $data['data']['user_id'] : '';
        $deliver_order_id = $data['data']['deliver_order_id']? $data['data']['deliver_order_id'] : '';
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $quantity = $data['data']['quantity']? $data['data']['quantity'] : '';
        $relevant_id = $data['data']['relevant_id']? $data['data']['relevant_id'] : '';
        $product_id =$id ;

        $sql_consoli_order =  "select order_id  from  oc_x_stock_move where  order_id = '".$order_id ."'" ;
        $query = $db->query($sql_consoli_order);
        $result_consoli_order = $query->row;
        if($result_consoli_order['order_id'] > 0 ){
            return 4 ;
        }

        $sql = "select order_id  from  oc_x_inventory_deliver_order_sorting  where warehouse_id = '".$warehouse_id."' and product_id = '".$product_id."' and deliver_order_id = '".$deliver_order_id."'";

        $query = $db->query($sql);
        $result = $query->row;
        if($result['order_id'] > 0 ){
            return 3 ;
        }else {
//            if(strlen($id)>=10){
//                $product_id= substr($id, 0, -6);
//            }else{
//                $product_id= substr($id, 0, -5);
//            }

            $product_id =$id ;
            $sql = " insert into oc_x_inventory_deliver_order_sorting (`deliver_order_id` , `order_id` , `quantity` , `warehouse_id` , `product_id` , `uptime` ,`added_by`  ,`relevant_id`)  VALUES ('" . $deliver_order_id . "','" . $order_id . "','" . $quantity . "','" . $warehouse_id . "','" . $product_id . "' , NOW() , '".$user_id."' , '".$relevant_id."')";

            $query = $db->query($sql);

            if ($query) {

                $sql =  " update  oc_x_deliver_order  set order_status_id  =  12 where   order_id = '".$order_id ."' ";
                $query = $db->query($sql);

                return 1;
            } else {
                return 2;
            }
        }

    }

    function  confirmDoRelevantC(array $data ){
        global $db;
        $id = $data['data']['id']? $data['data']['id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $user_id= $data['data']['user_id']? $data['data']['user_id'] : '';
        $deliver_order_id = $data['data']['deliver_order_id']? $data['data']['deliver_order_id'] : '';
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $quantity = $data['data']['quantity']? $data['data']['quantity'] : '';
        $relevant_id = $data['data']['relevant_id']? $data['data']['relevant_id'] : '';

        $sql_consoli_order =  "select order_id  from  oc_x_consolidated_order where  order_id = '".$order_id ."'" ;
        $query = $db->query($sql_consoli_order);
        $result_consoli_order = $query->row;
        if($result_consoli_order['order_id'] > 0 ){
            return 4 ;
        }

        $sql = "select order_id  from  oc_x_inventory_deliver_order_sorting  where warehouse_id = '".$warehouse_id."' and container_id = '".$id."' and deliver_order_id = '".$deliver_order_id."'";

        $query = $db->query($sql);
        $result = $query->row;
        if($result['order_id'] > 0 ){
            return 3 ;
        }else {


            $sql = " insert into oc_x_inventory_deliver_order_sorting (`deliver_order_id` , `order_id` , `quantity` , `warehouse_id` , `container_id` , `uptime` , `added_by` ,`relevant_id` )  VALUES ('" . $deliver_order_id . "','" . $order_id . "','" . $quantity . "','" . $warehouse_id . "','" . $id . "' , NOW() ,  '".$user_id ."' , '".$relevant_id."')";

            $query = $db->query($sql);

            if ($query) {
                $sql =  " update  oc_x_deliver_order  set order_status_id  =  12 where   order_id = '".$order_id ."' ";
                $query = $db->query($sql);
                return 1;
            } else {
                return 2;
            }
        }
    }


    function getRelevantInfoByProduct(array $data ){
        global $db;
        $product_id = $data['data']['product_id']? $data['data']['product_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $relevant_id= $data['data']['relevant_id']? $data['data']['relevant_id'] : '';
        if(strlen($product_id) <= 6){
            $sql_relevant_id =  "select wr.relevant_id  from oc_x_warehouse_requisition  wr left join  oc_x_warehouse_requisition_item  wri  on   wri.relevant_id = wr.relevant_id  where wri.product_id = '".$product_id ."'  and   wr.status = 2 and wr.relevant_status_id = 6  and wr.to_warehouse = '".$warehouse_id ."'  group  by  wr.relevant_id order by wr.relevant_id  desc  limit 1  ";

            $query = $db->query($sql_relevant_id);
            $result_relevant_id = $query->row;
        }else{
            $sql = " select product_id   from oc_product_to_warehouse  where  sku_barcode = '". $product_id ."'  and  warehouse_id = '".$warehouse_id ."'";

            $query = $db->query($sql);
            $result = $query->row;

            $sql_relevant_id =  "select wr.relevant_id   from oc_x_warehouse_requisition  wr left join  oc_x_warehouse_requisition_item  wri  on   wri.relevant_id = wr.relevant_id  where wri.product_id = '".$result['product_id'] ."'  and   wr.status = 2 and wr.relevant_status_id = 6  and wr.to_warehouse = '".$warehouse_id ."' and wr.relevant_id = '". $relevant_id."' group  by  wr.relevant_id order by wr.relevant_id  desc  limit 1  ";

            $query = $db->query($sql_relevant_id);
            $result_relevant_id = $query->row;

            $product_id = $result['product_id'];

        }
        if($result_relevant_id['relevant_id'] > 0 ){

            $sql_info = "select wr.relevant_id , doo.deliver_order_id , doo.order_id , p.name , p.product_id  ,	( dop.quantity -if(dos.quantity > 0 , dos.quantity , 0)   ) quantity    , '无周转筐'container_id  ,  min(doi.inv_comment) inv_comment  from oc_x_warehouse_requisition  wr left join  oc_x_warehouse_requisition_item  wri  on   wri.relevant_id = wr.relevant_id left join  oc_x_deliver_order doo on doo.relevant_id = wr.relevant_id  left join oc_x_deliver_order_product dop on doo.deliver_order_id = dop.deliver_order_id and dop.product_id  = wri.product_id  LEFT JOIN oc_product p on dop.product_id = p.product_id   left join  oc_x_inventory_deliver_order_sorting  dos on  dos.product_id = dop.product_id  and dos.relevant_id = '".$relevant_id."' and  dos.deliver_order_id = dop.deliver_order_id  left join  oc_x_deliver_order_inv doi on doi.order_id = doo.order_id   where dop.product_id = '".$product_id ."'  and   wr.status = 2 and wr.relevant_status_id = 6  and wr.to_warehouse = '".$warehouse_id ."'  and wr.relevant_id = '".$relevant_id ."'   GROUP BY doo.order_id   order by quantity  desc   ";


            $query = $db->query($sql_info);
            $result_info   = $query->rows;
            return $result_info = array ($result_info,0);
        }else{
            return 2 ;
        }

    }



    function addConsolidatedRelevant(array $data )
    {

        global $db;
        $data['order_id'] = $data['data']['order_id']? $data['data']['order_id'] : '';

        $sql =  "select order_status_id , order_id   from oc_x_deliver_order WHERE  deliver_order_id = '".$data['order_id'] ."'";
        $query = $db->query($sql);
        $result = $query->row;

        if($result['order_status_id'] == 3){
            $return['status'] = 2;
            return $return;
        }

        if($result['order_status_id'] != 12){
            $return['status'] = 4;
            return $return;
        }



        $sql_move = " select order_id  from  oc_x_stock_move where  order_id = '".$result['order_id']."'  ";

        $query = $db->query($sql_move);
        $result_move = $query->row;
        if($result_move['order_id'] > 0 ){
            $return['status'] = 3;
            return $return ;
        }



        //判断中间库中的商品数量是否满足95% 如果不满足则不能提交
        $sql = "SELECT sum(quantity) as quantity FROM oc_x_inventory_order_sorting AS xis where xis.status = 1 and  xis.move_flag = 0 and xis.deliver_order_id = '" . $data['order_id'] . "' ";
        $query = $db->query($sql);
        $result = $query->row;


        $sql = "SELECT
                    o.deliver_order_id order_id,os.`name`,SUM(op.quantity) as quantity
                FROM
                    oc_x_deliver_order_product AS op
                LEFT JOIN oc_x_deliver_order AS o ON o.deliver_order_id = op.deliver_order_id
                LEFT JOIN oc_x_deliver_order_status AS os ON os.order_status_id = o.order_status_id
               
                WHERE op.deliver_order_id = '" . $data['order_id']."'
                group by op.deliver_order_id  ";



        $query = $db->query($sql);
        $result_plan = $query->row;


        $return = array();


        $return['status'] = 1;
        $return['plan_quantity'] = $result_plan['quantity'];
        $return['do_quantity'] = $result['quantity'] ? $result['quantity'] : 0;
        //}

        return $return;


    }
    function  addConsolidatedDoInfo(array $data ){
        global $db  ;
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $invComment = $data['data']['invComment']? $data['data']['invComment'] : '';
        $boxCount = $data['data']['boxCount']? $data['data']['boxCount'] : '';
        $frame_count = $data['data']['frame_count']? $data['data']['frame_count'] : '';
        $frame_vg_list = $data['data']['frame_vg_list']? $data['data']['frame_vg_list'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $inventory_user = $data['data']['inventory_user']? $data['data']['inventory_user'] : '';

        $order_id = isset($order_id) ? $order_id : false;
        if (!$order_id) {
            return false;
        }

        $result_warehouse = $this->getDeliverOrderInfo($order_id);
        // SO_单
        $data_inv['order_id'] = $result_warehouse['order_id'] ;
         $sql = "select  count(order_id) num  from oc_x_deliver_order where order_id = '".$data_inv['order_id']."' group by  order_id  ";
        $query = $db->query($sql);
        $result = $query->row;



        if($result['num']>1 ) {
            $sql_su = "SELECT
                xis.product_id,
                sum(xis.quantity) quantity,
                sum(op.quantity)  order_quantity ,
              round(sum(op.price * op.quantity) / sum(op.quantity) , 2)  as price,
                op.weight_inv_flag,
                p.sku_id
            FROM
                oc_x_inventory_order_sorting AS xis
            LEFT JOIN oc_x_deliver_order_product AS op ON op.deliver_order_id = xis.deliver_order_id
            AND op.product_id = xis.product_id
            LEFT JOIN oc_product AS p ON p.product_id = xis.product_id
            WHERE
            xis.status =1 and 
                xis.move_flag = 0
            AND xis.deliver_order_id =  '".$order_id."'
            GROUP BY xis.product_id ";

        $query = $db->query($sql_su);
        $result_su = $query->rows;

        $stationProductMove_su = array();
        if(sizeof($result_su)) {
            foreach ($result_su as $k => $v) {
                $stationProductMove_su[] = array(
                    'product_batch' => '',
                    'due_date' => '0000-00-00', //There is a bug till year 2099.
                    'product_id' => $v['product_id'],
                    'special_price' => $v['price'],
                    'qty' => abs(min($v['quantity'], $v['order_quantity'])),
                    'product_weight' => 0,
                    'sku_id' => $v['sku_id']
                );
            }

        }

            $data_inv_su['products'] = $stationProductMove_su;
        }
        $sql_west = "SELECT
                xis.product_id,
                sum(xis.quantity) quantity,
                sum(op.quantity)  order_quantity ,
               round(sum(op.price * op.quantity) / sum(op.quantity) , 2)  as price,
                op.weight_inv_flag,
                p.sku_id
              
            FROM
                oc_x_inventory_deliver_order_sorting AS xis
            LEFT JOIN oc_x_deliver_order_product AS op ON op.deliver_order_id = xis.deliver_order_id
            AND op.product_id = xis.product_id
            LEFT JOIN oc_product AS p ON p.product_id = xis.product_id
            WHERE
                xis.move_flag = 0
            AND xis.order_id =  '".$data_inv['order_id']."'  and xis.container_id  <= 0 
            GROUP BY xis.product_id ";

        $query = $db->query($sql_west);
        $result_west = $query->rows;
        $stationProductMove_west = array();
        if(sizeof($result_west)) {
            foreach ($result_west as $k => $v) {
                $stationProductMove_west[] = array(
                    'product_batch' => '',
                    'due_date' => '0000-00-00', //There is a bug till year 2099.
                    'product_id' => $v['product_id'],
                    'special_price' => $v['price'],
                    'qty' => abs(min($v['quantity'], $v['order_quantity'])),
                    'product_weight' => 0,
                    'sku_id' => $v['sku_id']
                );
            }

        }

        $data_inv_west['products'] = $stationProductMove_west;

        $sql_container = "select  GROUP_CONCAT(dos.container_id ) container_id from  oc_x_inventory_deliver_order_sorting  dos   left join oc_x_inventory_order_sorting  ios on dos.container_id = ios.container_id   where ios.status =1 and dos.order_id = '".$data_inv['order_id'] ."'  and dos.container_id > 0   group by dos.order_id  ";

        $query = $db->query($sql_container);

        $resutl_container = $query->row;

        $container_ids =   $resutl_container['container_id'] ;
       

        if($container_ids != ''){
            $sql_container_product = "SELECT
                xis.product_id,
                sum(xis.quantity) quantity,
                sum(op.quantity)  order_quantity ,
              round(sum(op.price * op.quantity) / sum(op.quantity) , 2)  as price,
                op.weight_inv_flag,
                p.sku_id
            FROM
                oc_x_inventory_order_sorting AS xis
            LEFT JOIN oc_x_deliver_order_product AS op ON op.deliver_order_id = xis.deliver_order_id
            AND op.product_id = xis.product_id
            LEFT JOIN oc_product AS p ON p.product_id = xis.product_id
            WHERE
            xis.status = 1 and 
                xis.move_flag = 0
            AND xis.order_id =  '".$data_inv['order_id']."'  and xis.container_id in ($container_ids)
            GROUP BY xis.product_id ";

            $query = $db->query($sql_container_product);
            $result_container_product = $query->rows;
            $stationProductMove_con_product = array();
            if(sizeof($result_container_product)) {
                foreach ($result_container_product as $k => $v) {
                    $stationProductMove_con_product[] = array(
                        'product_batch' => '',
                        'due_date' => '0000-00-00', //There is a bug till year 2099.
                        'product_id' => $v['product_id'],
                        'special_price' => $v['price'],
                        'qty' => abs(min($v['quantity'], $v['order_quantity'])),
                        'product_weight' => 0,
                        'sku_id' => $v['sku_id']
                    );
                }

            }
            $data_inv_con_product['products'] = $stationProductMove_con_product;
        }

        $inventory_type = 12 ;
        $data_insert['station_id'] = 2;
        $data_insert['timestamp'] = $data['timestamp'];
        $data_insert['from_station_id'] = isset($data['from_station_id']) ? (int) $data['from_station_id'] : 0;
        $data_insert['to_station_id'] = isset($data['to_station_id']) ? (int) $data['to_station_id'] : 0;
        $data_insert['order_id'] = isset($data_inv['order_id']) ? (int) $data_inv['order_id']: 0;
        $data_insert['purchase_order_id'] = isset($data['purchase_order_id']) ? (int) $data['purchase_order_id'] : 0;

        $data_insert['inventory_type_id'] = isset($inventory_type) ? (int) $inventory_type : 0;
        $data_insert['date_added'] = date('Y-m-d H:i:s', time());
        $data_insert['added_by'] = isset($data['added_by']) ? (int) $data['added_by'] : 0;
        $data_insert['memo'] = isset($data['memo']) ? $db->escape($data['memo']) : '';
        $data_insert['add_user_name'] = isset($inventory_user) ? $inventory_user : '';
        $data_insert['warehouse_id'] = $warehouse_id;

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

        $bool = $db->query($sql);
        $inventory_move_id = $db->getLastId();
        $inventory_op = -1  ;
        if($result['num']>1 ) {
            if (!empty($data_inv_su['products'])) {
                $sql1 = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`) VALUES ";
                $m = 0;
                foreach ($data_inv_su['products'] as $product) {
                    $sql1 .= "(" . $inventory_move_id . ", " . 2 . ", '" . (isset($product['due_date']) ? $product['due_date'] : '0000-00-00') . "', '" . $product['product_id'] . "', '" . $product['special_price'] . "', '" . (isset($product['product_batch']) ? $product['product_batch'] : '') . "', " . $product['qty'] * $inventory_op . ", '" . (isset($product['box_quantity']) ? $product['box_quantity'] : 1) . "', " . (isset($product['product_weight']) ? $product['product_weight'] : 0) . "," . (isset($product['is_gift']) ? $product['is_gift'] : 0) . ", " . (isset($product['checked']) ? $product['checked'] : 0) . "," . (isset($product['status']) ? $product['status'] : 1) . "," . (isset($product['sku_id']) ? $product['sku_id'] : 0) . ")";

                if (++$m < sizeof($data_inv_su['products'])) {
                    $sql1 .= ', ';
                } else {
                    $sql1 .= ';';
                }
            }

                //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存明细表' . "\n\r");
                $bool = $db->query($sql1);
            }
        }
        if(!empty($data_inv_west['products'])){
            $sql2 = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`) VALUES ";
            $m = 0;
            foreach ($data_inv_west['products'] as $product) {
                $sql2 .= "(" . $inventory_move_id . ", " . 2 . ", '" . (isset($product['due_date']) ? $product['due_date'] : '0000-00-00') . "', '" . $product['product_id'] . "', '" . $product['special_price'] . "', '" . (isset($product['product_batch']) ? $product['product_batch'] : '') . "', " . $product['qty'] * $inventory_op . ", '".(isset($product['box_quantity']) ? $product['box_quantity'] : 1)."', " . (isset($product['product_weight']) ? $product['product_weight'] : 0) . "," . (isset($product['is_gift']) ? $product['is_gift'] : 0) . ", " . (isset($product['checked']) ? $product['checked'] : 0) . "," . (isset($product['status']) ? $product['status'] : 1) . "," . (isset($product['sku_id']) ? $product['sku_id'] : 0) . ")";

                if (++$m < sizeof($data_inv_west['products'])) {
                    $sql2 .= ', ';
                } else {
                    $sql2 .= ';';
                }
            }

            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存明细表' . "\n\r");
            $bool = $bool && $db->query($sql2);
        }




        if(!empty($data_inv_con_product['products'] )){
            $sql3 = "INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`) VALUES ";
            $m = 0;
            foreach ($data_inv_con_product['products']  as $product) {
                $sql3 .= "(" . $inventory_move_id . ", " . 2 . ", '" . (isset($product['due_date']) ? $product['due_date'] : '0000-00-00') . "', '" . $product['product_id'] . "', '" . $product['special_price'] . "', '" . (isset($product['product_batch']) ? $product['product_batch'] : '') . "', " . $product['qty'] * $inventory_op . ", '".(isset($product['box_quantity']) ? $product['box_quantity'] : 1)."', " . (isset($product['product_weight']) ? $product['product_weight'] : 0) . "," . (isset($product['is_gift']) ? $product['is_gift'] : 0) . ", " . (isset($product['checked']) ? $product['checked'] : 0) . "," . (isset($product['status']) ? $product['status'] : 1) . "," . (isset($product['sku_id']) ? $product['sku_id'] : 0) . ")";

                if (++$m < sizeof($data_inv_con_product['products'] )) {
                    $sql3 .= ', ';
                } else {
                    $sql3 .= ';';
                }
            }

            //$log->write('INFO:[' . __FUNCTION__ . ']' . ': 插入库存明细表' . "\n\r");
            $bool = $bool && $db->query($sql3);
        }

        $this->addReturn(array($data_inv['order_id']),$warehouse_id);
        $sql_consoli= " insert into oc_x_consolidated_order (`order_id` , `uptime` , `added_by` , `warehouse_id` ) VALUES ('".$data_inv['order_id']."' , NOW(),'".$inventory_user."' , '".$warehouse_id."' )" ;
        $query = $db->query($sql_consoli);
        if($bool){
            $db->begin();
            $bool = true;



            $sql = "update oc_x_inventory_order_sorting set move_flag = 1 where status = 1 and  order_id = '". $data_inv['order_id'] ."'";

            // $log->write('[分拣]更新分拣数据提交状态[' . __FUNCTION__ . ']'.$sql."\n\r");
            $bool = $bool && $db->query($sql);


            $sql = "update oc_order set order_status_id = 6 where order_id = '". $data_inv['order_id'] ."'";

            // $log->write('[分拣]更新订单状态为已拣完[' . __FUNCTION__ . ']'.$sql."\n\r");
            $bool = $bool && $db->query($sql);

            $sql = "update oc_x_deliver_order set order_status_id = 6 where order_id = '". $data_inv['order_id'] ."'";


            $bool = $bool && $db->query($sql);

            $this->addOrderSortingHistory($data_inv['order_id']);
            $sql = "select order_id  from  oc_order_inv  where order_id = '".$data_inv['order_id'] ."' ";
            $query = $db->query($sql);
            $order_inv_order_id = $query->row;

            if ($result['num'] > 1) {


                if ($order_inv_order_id['order_id'] > 0) {
                    $bool =  true ;
                } else {

                    $sql_order_inv = " insert into oc_order_inv (`order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,`box_count` ,`inv_comment` , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` ) (select `order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,`box_count` ,  `inv_comment` , `inv_status` ,NOW(),`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` from oc_x_deliver_order_inv   where deliver_order_id = '" . $order_id . "' ) ";

                    $bool = $bool && $db->query($sql_order_inv);
                }
                
                $sql_vg_list1 = " select doo.deliver_order_id , doi.box_count   from oc_x_deliver_order doo left join oc_x_deliver_order_inv doi  on doo.deliver_order_id = doi.deliver_order_id where  doi.deliver_order_id  = '" . $order_id . "' ";

                $query = $db->query($sql_vg_list1);
                $resutl_vg_list1 = $query->row;


                $sql_vg_list2 = "select doi.frame_vg_list  , doi.box_count , doi.frame_count  from oc_x_deliver_order  doo left join oc_x_deliver_order_inv doi  on doo.order_id = doi.order_id  where doi.deliver_order_id != '" . $order_id . "' and doo.order_id = '" . $data_inv['order_id'] . "' ";

                $query = $db->query($sql_vg_list2);
                $resutl_vg_list2 = $query->row;

                $sql_product = "SELECT
	o.order_id,
	sum(smi.quantity *(-1) ) quantity  
FROM
	oc_order o
LEFT JOIN oc_x_stock_move sm ON o.order_id = sm.order_id
LEFT JOIN oc_order_product op ON o.order_id = op.order_id
LEFT JOIN oc_x_stock_move_item smi ON sm.inventory_move_id = smi.inventory_move_id
AND smi.product_id = op.product_id
LEFT JOIN oc_product p ON p.product_id = op.product_id
WHERE
	o.warehouse_id = '".$warehouse_id ."'
AND sm.inventory_type_id = 12
AND p.repack = 0
and o.order_id =  '".$data_inv['order_id'] ."'
GROUP BY
	sm.order_id
";
                $query = $db->query($sql_product);
                $resutlt_product = $query->row;
                $box_count = $resutlt_product['quantity'];

                
                $sql = " update  oc_order_inv  set  box_count = '" . $box_count . "' , frame_count = '" . $resutl_vg_list2['frame_count'] . "'  , frame_vg_list = '" . $resutl_vg_list2['frame_vg_list'] . "' where  order_id = '" . $data_inv['order_id'] . "' ";

                $bool = $bool && $db->query($sql);


            } else {

                $sql_product = "SELECT
	o.order_id,
	sum(smi.quantity *(-1) ) quantity  
FROM
	oc_order o
LEFT JOIN oc_x_stock_move sm ON o.order_id = sm.order_id
LEFT JOIN oc_order_product op ON o.order_id = op.order_id
LEFT JOIN oc_x_stock_move_item smi ON sm.inventory_move_id = smi.inventory_move_id
AND smi.product_id = op.product_id
LEFT JOIN oc_product p ON p.product_id = op.product_id
WHERE
	o.warehouse_id = '".$warehouse_id ."'
AND sm.inventory_type_id = 12
AND p.repack = 0
and o.order_id =  '".$data_inv['order_id'] ."'
GROUP BY
	sm.order_id
";
                $query = $db->query($sql_product);
                $resutlt_product = $query->row;
                $box_count = $resutlt_product['quantity'];
                if($box_count == ''){
                    $box_count = 0 ;
                }

                $sql_order_inv = " insert into oc_order_inv (`order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,`box_count` ,`inv_comment` , `inv_status` ,`uptime` ,`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` ) (select `order_id` , `frame_count` ,`incubator_count` ,`foam_count` ,`frame_mi_count` ,`incubator_mi_count` ,`frame_ice_count` ,`frame_meat_count` ,`foam_ice_count` ,`frame_vg_list` ,`frame_mi_list` ,`frame_meat_list` ,`frame_ice_list` ,$box_count ,  $invComment , `inv_status` ,NOW(),`date_modified` ,`check_status` ,`frame_carton_list` ,`frame_carton_count` ,`status` from oc_x_deliver_order_inv   where order_id = '" . $data_inv['order_id'] . "' ) ";

                $bool = $bool && $db->query($sql_order_inv);


            }
            if (!$bool) {
                //$log->write('[分拣]分拣提交失败[' . __FUNCTION__ . ']' . "\n\r");



                $db->rollback();
                return array('status' => 0, 'timestamp' => $data_inv['timestamp']);
            } else {
                //$log->write('[分拣]分拣提交成功[' . __FUNCTION__ . ']' . "\n\r");
                $db->commit();
                return array('status' => 1, 'timestamp' => $data_inv['timestamp']);
            }

        }

    }
    /*xya
    自动领单*/
    public  function  getAutoOrders(array $data ){
        global $db;
        $data = json_decode($data, 2);
//return $data;
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $user_repack = $data['data']['user_repack']? $data['data']['user_repack'] : 0;
        $to_warehouse_id = $data['data']['to_warehouse_id']? $data['data']['to_warehouse_id'] : '0';

        if($to_warehouse_id == 0){
            $to_warehouse_id = $warehouse_id ;
        }

        if($user_repack == 0){
            $user_user_repack = 1 ;
        }
        if($user_repack == 1){
            $user_user_repack = 0 ;
        }
//return  $warehouse_id;

        $sql =  " SELECT
   doo.deliver_order_id AS order_id,
   doo.date_added,

IF (doo.is_urgent = 1, '1', '0') urgent,
 doo.customer_group_id,
 (
   IF (
      DATEDIFF(NOW(), doo.date_added) < 3,
      DATEDIFF(NOW(), doo.date_added) * 10,
      DATEDIFF(NOW(), doo.date_added) * 10
   ) +
   IF (
      doo.is_urgent = 1,
      10,
      0
    ) + 
    if(doo.customer_group_id = 1 , doo.customer_group_id*10 , doo.customer_group_id) +
	IF(soo.order_id, 10, 0) 
    ) sum_total 
   FROM
      oc_x_deliver_order doo
   LEFT JOIN oc_order o ON doo.order_id = o.order_id
   LEFT JOIN (
	SELECT
		order_id
	FROM
		oc_x_deliver_order
	WHERE
		do_warehouse_id =  '".$warehouse_id ."' and  warehouse_id = '".$to_warehouse_id."'
	AND order_status_id = 6
	AND is_repack =  '".$user_user_repack."' 
	
	 AND DATE(date_added) BETWEEN date_sub(
		CURRENT_DATE(),
		INTERVAL 7 DAY
	)
	AND CURRENT_DATE()  
) soo ON soo.order_id = doo.order_id
   WHERE  doo.do_warehouse_id = '".$warehouse_id ."' and doo.warehouse_id = '".$to_warehouse_id ."' and doo.order_status_id =  2  
   AND doo.is_repack = '".$user_repack."' 

   ORDER BY
      sum_total DESC
      limit  0 , 1 ";
//return $sql;
        $query = $db->query($sql);
        $results = $query->rows;
        return  $results;
    }
    /*zx
    待合单查询*/
    public  function  get_merge_order_status(array $data ){
        global $db;
        $data = json_decode($data, 2);
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $user_repack = $data['data']['user_repack']? $data['data']['user_repack'] : 0;
        $results2 = [];
        $results3 = [];
        $sql = " SELECT
	odos.`name`,odos.order_status_id,doo.deliver_order_id,if (doo.is_repack=0,odi.inv_comment,odi.frame_vg_list) AS stock_area,doo.order_id,doo.is_repack , odi.inv_comment 
FROM
	oc_x_deliver_order doo
LEFT JOIN oc_order o ON doo.order_id = o.order_id
LEFT JOIN oc_x_deliver_order_inv odi ON doo.deliver_order_id = odi.deliver_order_id
LEFT JOIN oc_x_deliver_order_status odos ON doo.order_status_id = odos.order_status_id
WHERE
	o.order_status_id IN (5, 8)
AND doo.warehouse_id = '".$warehouse_id ."' 

GROUP BY doo.deliver_order_id ORDER BY doo.order_id";
//return $sql;
        $query = $db->query($sql);
        $results = $query->rows;
        $sql1 = " SELECT
	GROUP_CONCAT(odos.order_status_id) AS status,doo.order_id
FROM
	oc_x_deliver_order doo
LEFT JOIN oc_order o ON doo.order_id = o.order_id
LEFT JOIN oc_x_deliver_order_status odos ON doo.order_status_id = odos.order_status_id
WHERE
	o.order_status_id IN (5, 8)
AND doo.warehouse_id = '".$warehouse_id ."'

GROUP BY doo.order_id";
        $query1 = $db->query($sql1);
        $results1 = $query1->rows;
        $results3['do'] = $results;
        $results3['so'] = $results1;
        return  $results3;
    }
    private function addOrderSortingHistory($order_id, $added_by=0){
        global $db;
        $sql = "INSERT INTO oc_order_history (`order_id`, `notify`, `comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`)
                select order_id, 0, '[SYS]订单分拣', now(),  order_status_id, order_payment_status_id, order_deliver_status_id, '".$added_by."' from oc_order where order_id = '".$order_id."'
                ";

        return $db->query($sql);
    }

    private function addDeliverOrderSortingHistory($order_id, $added_by=0){

        global $dbm;
        $sql = "INSERT INTO oc_x_deliver_order_history (`deliver_order_id`, `notify`, `comment`, `date_added`, `order_status_id`, `order_deliver_status_id`, `modified_by`)
                select deliver_order_id, 0, '[SYS]订单分拣', now(),  order_status_id,  order_deliver_status_id, '".$added_by."' from oc_x_deliver_order where deliver_order_id = '".$order_id."'
                ";

        return   $dbm->query($sql);
    }

    public function updateDoRelevantC(array $data){
        global $db;
        $id = $data['data']['id']? $data['data']['id'] : '';
        $deliver_order_id = $data['data']['deliver_order_id']? $data['data']['deliver_order_id'] : '';
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';

        $sql =  " select order_status_id from oc_order  where order_id = '".$order_id."' " ;
        $query = $db->query($sql);
        $resutl = $query->row;

        if($resutl['order_status_id'] == 6){
             return 3 ;
        }else{
            $sql = " delete from oc_x_inventory_deliver_order_sorting where order_id = '".$order_id."' and deliver_order_id = '".$deliver_order_id ."' and container_id = '".$id."' ";
            $query = $db->query($sql);

            if($query){
                return 1 ;
            }else{
                return 2 ;
            }
        }

    }

    public function updateDoRelevant(array $data){
        global $db;
        $id = $data['data']['id']? $data['data']['id'] : '';
        $deliver_order_id = $data['data']['deliver_order_id']? $data['data']['deliver_order_id'] : '';
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';

//        if(strlen($id)>=10){
//            $id= substr($id, 0, -6);
//        }else{
//            $id= substr($id, 0, -5);
//        }

        $sql =  " select order_status_id from oc_order  where order_id = '".$order_id."' " ;
        $query = $db->query($sql);
        $resutl = $query->row;

        if($resutl['order_status_id'] == 6){
            return 3 ;
        }else{
            $sql = " delete from oc_x_inventory_deliver_order_sorting where order_id = '".$order_id."' and deliver_order_id = '".$deliver_order_id ."' and product_id = '".$id."' ";
            $query = $db->query($sql);

            if($query){
                return 1 ;
            }else{
                return 2 ;
            }
        }

    }

    public  function updateDoStatus(array $data ){
        global $db;
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';

        $sql_order = "select o.order_status_id ,doo.order_status_id do_order_status_id  from  oc_x_deliver_order doo left join oc_order o on doo.order_id = o.order_id where doo.deliver_order_id = '".$order_id."' ";
        $query = $db->query($sql_order);
        $resutl = $query->row;

        if($resutl['order_status_id'] == 6  || $resutl['do_order_status_id'] !=6 ) {
            return 2 ;
        }else{

            $sql = " update   oc_x_deliver_order set order_status_id = 12  where  deliver_order_id  = '".$order_id ."' and warehouse_id = '".$warehouse_id."'  ";
            $query = $db->query($sql);
            if($query){
                $this->addDeliverOrderSortingHistory($order_id);

                return 1 ;
            }else{
                return 2 ;
            }
        }

    }

    public function mergeDeliverOrder(array $data ){
        global $db;

        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $inventory_user = $data['data']['inventory_user']? $data['data']['inventory_user'] : '';
        $inv_comment = $data['data']['inv_comment']? $data['data']['inv_comment'] : '0';
//
//        if($warehouse_id == 14 ){
//            $sql  =  " SELECT  COUNT(order_id ) num  ,do_warehouse_id ,warehouse_id  from  oc_x_deliver_order   where order_id = '".$order_id."'  GROUP BY order_id   ";
//
//            $query = $db->query($sql);
//            $result = $query->row;
//
//            if( $result['num'] ==1 && $result['warehouse_id'] != $result['do_warehouse_id']){
//                return 3 ;
//            }
//        }

        $sql = " SELECT
 doo.deliver_order_id , 
 doo.order_id , 
 doo.order_status_id  , 
doo.date_added  
FROM
	oc_x_deliver_order doo
LEFT JOIN oc_x_inventory_order_sorting ios ON doo.deliver_order_id = ios.deliver_order_id
left join oc_order o on doo.order_id = o.order_id 
WHERE   doo.order_status_id  = 6 
and ios.status = 1 
 and o.order_status_id !=6  and doo.order_id = '".$order_id."'";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['order_id'] > 0 || $inventory_user == 'wangshaokui' || $inventory_user == 'wangshaokui123' || $inventory_user == 'xiangyiao' || $inventory_user == 'xiangyiao2'){
            $sql = "select deliver_order_id from oc_x_deliver_order  where order_id = '".$order_id."'  ";
            $query = $db->query($sql);
            $result = $query->row;

            $query = $db->query($sql);
            $count_id = '99999';
            $data =  array ('warehouse_id'=>$warehouse_id ,'inventory_user'=>$inventory_user , 'order_id' => $result['deliver_order_id'] , 'inv_comment'=> $inv_comment);
            $data1 = array('data' =>$data);
             $result = $this->confirmOrderInfoByCount($data1);
           
            if($result){
                return 1 ;
            }
        }else{
            return 2;
        }
    }

    public  function  deleteOrderSorting(array $data){
        global $db;

        $order_id = $data['data']['count_id']? $data['data']['count_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';
        $inventory_user = $data['data']['inventory_user']? $data['data']['inventory_user'] : '';
        $inventory_user_id = $data['data']['inventory_user_id']? $data['data']['inventory_user_id'] : '';
        
        
        $sql =  " select  order_payment_status_id  , credit_pay , point_pay  , order_status_id  , warehouse_id from oc_order where order_id =  '".$order_id ."'  ";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['order_payment_status_id'] == 2  || $result['credit_pay'] == 1  || $result['point_pay'] == 1 || $result['order_status_id'] == 3  ){
            return 1 ;
        }

        if($warehouse_id != $result['warehouse_id'] ){
            return   $result['warehouse_id'] ;
            return 3 ;
        }



        $sql_order = "update oc_order set order_status_id = 2 ,order_deliver_status_id = 1  where order_id  = '".$order_id."' ";
        $query = $db->query($sql_order);

        $sql_history = "INSERT INTO oc_order_history (`order_id`, `notify`, `comment`, `date_added`, `order_status_id`, `order_payment_status_id`, `order_deliver_status_id`, `modified_by`)
                select order_id, 0, '删除分拣数据', now(),  order_status_id, order_payment_status_id, order_deliver_status_id, '".$inventory_user_id."' from oc_order where order_id = '".$order_id."'
                ";

        $query = $db->query($sql_history);




        $sql_deliver_order = "update oc_x_deliver_order  set order_status_id = 2 ,order_deliver_status_id = 1  where order_id  = '".$order_id."' ";
        $query = $db->query($sql_deliver_order);

        $sql_return = "update oc_return  set return_status_id = 3   where order_id  = '".$order_id."' ";
        $query = $db->query($sql_return);


        $sql_sorting =  " update  oc_x_inventory_order_sorting  set status = 0  where order_id =  '".$order_id."'  ";
        $query = $db->query($sql_sorting);

        $sql_consolidate = " delete  from oc_x_consolidated_order where order_id = '".$order_id."'";
        $query = $db->query($sql_consolidate);

        $sql_order_inv = " delete from oc_order_inv where order_id =  '".$order_id."'  ";
        $query = $db->query($sql_order_inv);

        $sql_deliver_order_inv = " delete from oc_x_deliver_order_inv where order_id =  '".$order_id."'  ";
        $query = $db->query($sql_deliver_order_inv);

        $sql_stock_move = "  select order_id ,inventory_move_id  from oc_x_stock_move  where order_id =  '".$order_id ."'  and inventory_type_id = 12   ";
        $query = $db->query($sql_stock_move);
        $result_move = $query->row;

        if($result_move['order_id'] > 0 ){

            $sql_update_stock = "update oc_x_stock_move set inventory_type_id  = 26 where order_id = '".$order_id ."'  ";
            $query = $db->query($sql_update_stock);
            $result_move = $query->row;

            $sql_stock_move = "insert  into oc_x_stock_move
              (`order_id`,`station_id`,`timestamp`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id` ,`memo` )
              VALUES ('" . $order_id . "' ,'2',UNIX_TIMESTAMP(NOW()), '24' ,NOW(), '" .$inventory_user ."' ,'" . $warehouse_id . "','删除分拣数据')";
            $query5 = $db->query($sql_stock_move);
            $stock_move_id = $db->getLastId();

            $sql_stock_move_item = " INSERT INTO `oc_x_stock_move_item` (`inventory_move_id`, `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`, `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`) (select  $stock_move_id , `station_id`, `due_date`, `product_id`, `price`, `product_batch`, `quantity`*(-1), `box_quantity`, `weight`, `is_gift`, `checked`, `status`, `sku_id`   from  oc_x_stock_move_item where inventory_move_id  =  '".$result_move['inventory_move_id']."' )";

            $query = $db->query($sql_stock_move_item);


        }

            if($query){
               return  2 ;
            }
    }

    /*zx
    调拨单面单打印*/
    public function print_relevants_merge(array $data){
        global $db;
        $warehouse_id = isset($data['data']['warehouse_id']) ? trim($data['data']['warehouse_id']) : false;
        $relevant_ids = !empty($data['data']['relevant_ids']) ? $data['data']['relevant_ids'] : false;
        $to_warehouse_id = isset($data['data']['to_warehouse_id']) ? trim($data['data']['to_warehouse_id']) : false;
        $from_warehouse_id = isset($data['data']['from_warehouse_id']) ? trim($data['data']['from_warehouse_id']) : false;
        $warehouse_out_type = isset($data['data']['warehouse_out_type']) ? trim($data['data']['warehouse_out_type']) : false;
        $relevant_ids = join($relevant_ids,',');
        $sql1 = "SELECT relevant_status_id AS status FROM oc_x_warehouse_requisition WHERE relevant_id IN(".$relevant_ids.")";
        $query1 = $db->query($sql1);
        $relevant_status_ids = '';
        $relevant_status_id = $query1->rows;
        foreach ($relevant_status_id as $value) {
            $relevant_status_ids .= $value['status'].',';
        }
        $relevant_status_ids = rtrim($relevant_status_ids,',');
        $relevant_status_ids = explode(',',$relevant_status_ids);
        $return = [];
        $count = count(array_unique($relevant_status_ids));
        if ($count != 1) {
            return 1;
        }
        /*入库*/
        if ($warehouse_id == $to_warehouse_id) {
            $sql =  "SELECT
	wrt.container_id,
	wrt.relevant_id,
	wrt.product_id,
	SUM(wrt.quantity) AS num2,
	ptw.sku_barcode,
	p.name
FROM
	oc_x_warehouse_requisition_temporary wrt
LEFT JOIN oc_x_warehouse_requisition wr ON wr.relevant_id = wrt.relevant_id
LEFT JOIN oc_product_to_warehouse ptw ON wrt.product_id = ptw.product_id
AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wrt.product_id = p.product_id
WHERE
	wrt.relevant_id in(".$relevant_ids.") ";
            if (array_unique($relevant_status_ids)[0] == 4) {
                $sql .= " AND wrt.relevant_status_id = 2 AND wr.relevant_status_id = 4 ";
            } else if (array_unique($relevant_status_ids)[0] == 6) {
                $sql .= " AND wrt.relevant_status_id = 4 AND wr.relevant_status_id = 6 ";
            } else {
                return 3;
            }
            $sql .= " GROUP BY wrt.product_id,wrt.container_id,wrt.relevant_id ORDER BY wrt.container_id" ;
        /*出库*/
        } else {
            if (array_unique($relevant_status_ids)[0] == 2) {
                $sql =  "SELECT
	wri.container_id,
	wri.relevant_id,
	wri.product_id,
	SUM(wri.num) AS num2,
	ptw.sku_barcode,
	p.name
FROM
	oc_x_warehouse_requisition_item wri
LEFT JOIN oc_x_warehouse_requisition wr ON wr.relevant_id = wri.relevant_id
LEFT JOIN oc_product_to_warehouse ptw ON wri.product_id = ptw.product_id
AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wri.product_id = p.product_id
WHERE
	wri.relevant_id in(".$relevant_ids.")
    AND wr.relevant_status_id = 2
GROUP BY
	wri.product_id,wri.container_id,wri.relevant_id ORDER BY wri.container_id" ;
            } else if (array_unique($relevant_status_ids)[0] == 4) {
                $sql =  "SELECT
	wrt.container_id,
	wrt.relevant_id,
	wrt.product_id,
	SUM(wrt.quantity) AS num2,
	ptw.sku_barcode,
	p.name
FROM
	oc_x_warehouse_requisition_temporary wrt
LEFT JOIN oc_x_warehouse_requisition wr ON wr.relevant_id = wrt.relevant_id
LEFT JOIN oc_product_to_warehouse ptw ON wrt.product_id = ptw.product_id
AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wrt.product_id = p.product_id
WHERE
	wrt.relevant_id in(".$relevant_ids.") 
	AND wrt.relevant_status_id = 2
	AND wr.relevant_status_id = 4
GROUP BY
	wrt.product_id,wrt.container_id,wrt.relevant_id ORDER BY wrt.container_id" ;
            } else {
                return 3;
            }

        }

        $query = $db->query($sql);
        $resutl = $query->rows;
        $return['item'] = $resutl;
        $return['main'] = $this->get_warehouse_requisition_history($relevant_ids);
        return $return;

    }
    /*zx
    调拨单合并*/
    public function merge_relevant_orders(array $data){
        global $db;
        $warehouse_id = isset($data['data']['warehouse_id']) ? trim($data['data']['warehouse_id']) : false;
        $relevant_ids = !empty($data['data']['relevant_ids']) ? $data['data']['relevant_ids'] : false;
        $to_warehouse_id = isset($data['data']['to_warehouse_id']) ? trim($data['data']['to_warehouse_id']) : false;
        $from_warehouse_id = isset($data['data']['from_warehouse_id']) ? trim($data['data']['from_warehouse_id']) : false;
        $added_by_id = isset($data['data']['added_by_id']) ? $data['data']['added_by_id'] : false;
        $warehouse_out_type = isset($data['data']['warehouse_out_type']) ? trim($data['data']['warehouse_out_type']) : false;
        $relevant_ids = join($relevant_ids,',');
        /*zx
        判断是否类型不对*/
        if ($warehouse_out_type != 1) {
            return 2;
        }
        /*入库*/
        if ($warehouse_id == $to_warehouse_id) {
            /*zx
            判断是否已经合过*/
            $sql = "SELECT relevant_status_id AS status FROM oc_x_warehouse_requisition WHERE relevant_id IN(".$relevant_ids.") AND parent_relevant_id > 0 OR parent_relevant_id IN(".$relevant_ids.")";
            $query = $db->query($sql);
            $result = $query->rows;
            if ($result) {
                return 4;
            }
            $sql = "SELECT relevant_status_id AS status FROM oc_x_warehouse_requisition WHERE relevant_id IN(".$relevant_ids.")";
            $query = $db->query($sql);
            $relevant_status_id = $query->rows;
            foreach ($relevant_status_id as $value) {
                if ($value['status'] != 4) {
                    return 5;
                }
            }

            /*zx
            查询对应调拨单的信息*/
            $sql = "SELECT 
	wrt.container_id,
	wrt.relevant_id,
	wrt.product_id,
	SUM(wrt.quantity) AS num2,
	ptw.sku_barcode,
	p.name
FROM
	oc_x_warehouse_requisition_temporary wrt
LEFT JOIN oc_x_warehouse_requisition wr ON wr.relevant_id = wrt.relevant_id
LEFT JOIN oc_product_to_warehouse ptw ON wrt.product_id = ptw.product_id
AND ptw.warehouse_id = ".$warehouse_id."
LEFT JOIN oc_product p ON wrt.product_id = p.product_id
WHERE
	wrt.relevant_id IN(".$relevant_ids.") 
	AND wrt.relevant_status_id = 2
	AND wr.relevant_status_id = 4
GROUP BY
	wrt.product_id,wrt.container_id ORDER BY wrt.container_id" ;
            $query = $db->query($sql);
            $product_ids = $query->rows;

            /*zx
            合单插入新调拨单*/
            $sql = "INSERT INTO oc_x_warehouse_requisition (from_warehouse,to_warehouse,date_added,added_by,relevant_status_id,confirm_by,deliver_date,out_type,status) VALUES 
(".$from_warehouse_id.",'".$to_warehouse_id."',NOW(),'".$added_by_id."',4,'".$added_by_id."',NOW(),1,2)";
            $query = $db->query($sql);
            $new_relevant_id = $db->getLastId();
            $this->insert_warehouse_requisition_history(4,$new_relevant_id,$added_by_id);
            $sql = "INSERT INTO `oc_x_warehouse_requisition_item` (`relevant_id`,`container_id`, `product_id`, `num`, `date_added`, `added_by`,  `status`, `box_quantity`,  `int_part`) VALUES ";
            $sql1 = "INSERT INTO `oc_x_warehouse_requisition_temporary` (`relevant_id`,`container_id`, `product_id`, `quantity`, `date_added`, `relevant_status_id`,  `warehouse_id`) VALUES ";
            $sql2 = "INSERT INTO `oc_x_warehouse_requisition_temporary` (`relevant_id`,`container_id`, `product_id`, `quantity`, `date_added`, `relevant_status_id`,  `warehouse_id`) VALUES ";
            foreach ($product_ids as $key => $values) {
                $sql .= "(".$new_relevant_id.",".$values['container_id'].",".$values['product_id'].",'".$values['num2']."',NOW(),'".$added_by_id."',1,1,0),";
                $sql1 .= "(".$new_relevant_id.",".$values['container_id'].",".$values['product_id'].",'".$values['num2']."',NOW(),4,".$warehouse_id."),";
                $sql2 .= "(".$new_relevant_id.",".$values['container_id'].",".$values['product_id'].",'".$values['num2']."',NOW(),2,".$from_warehouse_id."),";
            }
            $sql = rtrim($sql, ',');
            $sql1 = rtrim($sql1, ',');
            $sql2 = rtrim($sql2, ',');
            $query = $db->query($sql);
//            $query = $db->query($sql1);
            $query = $db->query($sql2);

            /*zx
            更新被合单的调拨单状态*/
            $sql = "UPDATE oc_x_warehouse_requisition SET relevant_status_id = 3,parent_relevant_id = ".$new_relevant_id." WHERE relevant_id IN (".$relevant_ids.")";
            $query = $db->query($sql);
            $this->insert_warehouse_requisition_history(3,$relevant_ids,$added_by_id);

            /*zx
            合单插入新调拨明细单*/
            $sql = "SELECT relevant_id,deliver_order_id FROM oc_x_deliver_order WHERE relevant_id IN(".$relevant_ids.") GROUP BY deliver_order_id";
            $query = $db->query($sql);
            $result = $query->rows;
            if (!empty($result)) {
                $deliver_order_ids = '';
                /*zx
                插入历史表*/
                $sql = "INSERT INTO oc_x_warehouse_requisition_merge_history (`new_relevant_id`,`old_relevant_id`,`deliver_order_id`,`added_by`,`date_added`) VALUES";
                foreach ($result as $value) {
                    $sql .= "(".$new_relevant_id.",".$value['relevant_id'].",".$value['deliver_order_id'].",".$added_by_id.",NOW()),";
                    $deliver_order_ids .= $value['deliver_order_id'].',';
                }
                $sql = rtrim($sql, ',');
                $deliver_order_ids = rtrim($deliver_order_ids, ',');
                $query = $db->query($sql);

                /*zx
                更新DO单绑定的调拨单号*/
                $sql = "UPDATE oc_x_deliver_order SET relevant_id = ".$new_relevant_id." WHERE deliver_order_id IN (".$deliver_order_ids.")";
                $query = $db->query($sql);
            }

            return 1;
        /*出库*/
        } else {
            /*zx
            不是入库无法合单*/
            return 3;
        }
    }
    /*zx
    删除合并的调拨单*/
    public function delete_merge_relevant_orders(array $data){
        global $db;
        $warehouse_id = isset($data['data']['warehouse_id']) ? trim($data['data']['warehouse_id']) : false;
        $relevant_ids = !empty($data['data']['relevant_ids']) ? $data['data']['relevant_ids'] : false;
        $to_warehouse_id = isset($data['data']['to_warehouse_id']) ? trim($data['data']['to_warehouse_id']) : false;
        $from_warehouse_id = isset($data['data']['from_warehouse_id']) ? trim($data['data']['from_warehouse_id']) : false;
        $added_by_id = isset($data['data']['added_by_id']) ? $data['data']['added_by_id'] : false;
        $warehouse_out_type = isset($data['data']['warehouse_out_type']) ? trim($data['data']['warehouse_out_type']) : false;
        /*zx
        判断是否类型不对*/
        if ($warehouse_out_type != 1) {
            return 2;
        }
        /*入库*/
        if ($warehouse_id == $to_warehouse_id) {
            /*zx
            判断是否是合并过的调拨单，否则不能删除*/
            foreach ($relevant_ids as $value) {
                $sql = "SELECT GROUP_CONCAT(old_relevant_id,'@',deliver_order_id) AS deliver_order_ids,new_relevant_id FROM oc_x_warehouse_requisition_merge_history WHERE status = 1 AND new_relevant_id = ".$value." GROUP BY new_relevant_id";
                $query = $db->query($sql);
                $result = $query->row;
                if (empty($result)) {
                    return 4;
                }
            }

            /*zx
            更新DO单绑定的调拨单号*/
            $result = explode(',',$result['deliver_order_ids']);
            $old_relevant_ids = '';
            foreach ($result as $value) {
                $deliver_order_ids = explode('@',$value);
                $old_relevant_ids .= $deliver_order_ids[0].',';
                $sql = "UPDATE oc_x_deliver_order SET relevant_id = ".$deliver_order_ids[0]." WHERE deliver_order_id = ".$deliver_order_ids[1];
//                $query = $db->query($sql);
            }
            $old_relevant_ids = rtrim($old_relevant_ids,',');

            $sql = "SELECT relevant_status_id AS status FROM oc_x_warehouse_requisition WHERE relevant_id = ".$relevant_ids[0];
            $query = $db->query($sql);
            $relevant_status_id = $query->rows;
            foreach ($relevant_status_id as $value) {
                if ($value['status'] != 6) {
                    return 5;
                }
            }

            /*zx
            更新被合单的调拨单状态*/
            $sql = "UPDATE oc_x_warehouse_requisition SET relevant_status_id = 4 WHERE relevant_id IN (".$old_relevant_ids.")";
            $query = $db->query($sql);
            /*zx
            删除调拨单明细*/
            $sql = "DELETE oc_x_warehouse_requisition_item WHERE relevant_id IN(".$old_relevant_ids.")";
            $query = $db->query($sql);
            /*zx
            删除调拨单中间表*/
            $sql = "DELETE oc_x_warehouse_requisition_temporary WHERE relevant_status_id = 4 AND relevant_id IN(".$old_relevant_ids.")";
            $query = $db->query($sql);


            /*zx
            删除合并历史表数据*/
            $sql = "UPDATE oc_x_warehouse_requisition_merge_history SET status = 0 WHERE status = 1 AND new_relevant_id = ".$relevant_ids[0];
            $query = $db->query($sql);
            /*zx
            删除调拨单数据*/
            $sql = "DELETE oc_x_warehouse_requisition WHERE relevant_id = ".$relevant_ids[0];
            $query = $db->query($sql);
//            /*zx
//            删除调拨单明细*/
//            $sql = "DELETE oc_x_warehouse_requisition_item WHERE relevant_id = ".$relevant_ids[0];
//            $query = $db->query($sql);
//            /*zx
//            删除调拨单中间表*/
//            $sql = "DELETE oc_x_warehouse_requisition_temporary WHERE relevant_id = ".$relevant_ids[0];
//            $query = $db->query($sql);

            return 1;
        /*出库*/
        } else {
            /*zx
            不是入库无法合单*/
            return 3;
        }
    }
    
    
  // 不通过调拨单合单
    public  function  getContainerDeliver(array $data){
        global $db ;
        $count_id= $data['data']['count_id']? $data['data']['count_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';



        $sql = "  select do.deliver_order_id  , do.order_id    from  oc_x_deliver_order do LEFT  JOIN  oc_x_deliver_order_inv doi on  do.deliver_order_id = doi.deliver_order_id left JOIN  oc_order_distr  od  on od.deliver_order_id = doi.deliver_order_id where (doi.frame_vg_list like '%,$count_id%' or  doi.frame_vg_list in ($count_id) ) and do.warehouse_id = '".$warehouse_id."'  and do.order_status_id = 6 and do.order_deliver_status_id = 1    order by doi.order_inv_id  desc limit 1 ";

        $query = $db->query($sql);
        $result = $query->row;


        $sql_order = "select $count_id  container_id,   doo.deliver_order_id , doo.order_id , if(doo.warehouse_id = doo.do_warehouse_id , doi.inv_comment , '' ) inv_comment ,(1- if(dos.container_id , 1 , 0 )) quantity    from oc_x_deliver_order doo left join oc_x_deliver_order_inv doi on doo.order_id = doi.order_id  and doi.deliver_order_id = doo.deliver_order_id left join  oc_x_inventory_deliver_order_sorting dos on dos.order_id = doi.order_id  and dos.deliver_order_id = doi.deliver_order_id  and dos.container_id = '".$count_id."' where doo.order_id = '".$result['order_id']."'  GROUP BY doo.order_id  ";

        $query = $db->query($sql_order);
        $result_order = $query->rows;

        return  $result_info = array (0, $result_order);

    }

    function getProductDeliver(array  $data )
    {
        global $db;

        global $db;
        $product_id = $data['data']['product_id']? $data['data']['product_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';

        if(strlen($product_id) <= 6){
            $sql =  "SELECT
doo.deliver_order_id , 
doo.order_id , 
doi.inv_comment , 
dop.product_id ,
  if(ios.deliver_order_id  , dop.quantity - ios.quantity  , dop.quantity) quantity  , 

'整件商品'  container_id,
p.name 
FROM
	oc_x_deliver_order doo
LEFT JOIN oc_x_deliver_order_product dop ON doo.deliver_order_id = dop.deliver_order_id 
LEFT JOIN oc_x_deliver_order_inv doi on doo.deliver_order_id = doi.deliver_order_id 
left join oc_x_inventory_deliver_order_sorting ios on dop.deliver_order_id = ios.deliver_order_id and ios.product_id = dop.product_id
left join oc_order o on doo.order_id = o.order_id 
left join oc_product p on p.product_id = dop.product_id 
where dop.product_id =  '".$product_id ."' and doo.warehouse_id = '".$warehouse_id ."' and  o.order_status_id not in (1,3,6)  ";
            $query = $db->query($sql);
            $result = $query->rows;
        }else{
            $sql = " select product_id   from oc_product_to_warehouse  where  sku_barcode = '". $product_id ."'  and  warehouse_id = '".$warehouse_id ."'";

            $query = $db->query($sql);
            $result = $query->row;

            $sql =  "SELECT
doo.deliver_order_id , 
doo.order_id , 
doi.inv_comment , 
dop.product_id ,
  if(ios.deliver_order_id  , dop.quantity - ios.quantity  , dop.quantity) quantity  , 

'整件商品'  container_id,
p.name 
FROM
	oc_x_deliver_order doo
LEFT JOIN oc_x_deliver_order_product dop ON doo.deliver_order_id = dop.deliver_order_id 
LEFT JOIN oc_x_deliver_order_inv doi on doo.deliver_order_id = doi.deliver_order_id 
left join oc_x_inventory_deliver_order_sorting ios on dop.deliver_order_id = ios.deliver_order_id and ios.product_id = dop.product_id
left join oc_order o on doo.order_id = o.order_id 
left join oc_product p on p.product_id = dop.product_id 
where dop.product_id =  '".$result['product_id'] ."' and doo.warehouse_id = '".$warehouse_id ."' and  o.order_status_id not in (1,3,6)  ";
            $query = $db->query($sql);
            $result = $query->rows;

        }
        return  $result_info = array ( $result);
    }
    /*zx
  取消调拨单*/
    public function update_relevant_orders(array $data){
        global $db;
        $warehouse_id = isset($data['data']['warehouse_id']) ? trim($data['data']['warehouse_id']) : false;
        $relevant_ids = !empty($data['data']['relevant_ids']) ? $data['data']['relevant_ids'] : false;
        $to_warehouse_id = isset($data['data']['to_warehouse_id']) ? trim($data['data']['to_warehouse_id']) : false;
        $from_warehouse_id = isset($data['data']['from_warehouse_id']) ? trim($data['data']['from_warehouse_id']) : false;
        $added_by_id = isset($data['data']['added_by_id']) ? $data['data']['added_by_id'] : false;
        $warehouse_out_type = isset($data['data']['warehouse_out_type']) ? trim($data['data']['warehouse_out_type']) : false;
        /*zx
        判断是否类型不对*/
        if ($warehouse_out_type != 1) {
            return 2;
        }
        /*出库*/
        if ($warehouse_id != $to_warehouse_id) {

            $sql = "SELECT relevant_status_id AS status FROM oc_x_warehouse_requisition WHERE relevant_id = ".$relevant_ids[0];
            $query = $db->query($sql);
            $relevant_status_id = $query->rows;
            foreach ($relevant_status_id as $value) {
                if ($value['status'] >= 4) {
                    return 5;
                }
            }
            /*zx
            更新DO单绑定的调拨单号*/
            $sql = "UPDATE oc_x_deliver_order SET relevant_id = 0 WHERE relevant_id = ".$relevant_ids[0];
            $query = $db->query($sql);
            /*zx
            更新被合单的调拨单状态*/
            $sql = "UPDATE oc_x_warehouse_requisition SET relevant_status_id = 3 WHERE relevant_id IN (".$relevant_ids[0].")";
            $query = $db->query($sql);
            $this->insert_warehouse_requisition_history(3,$relevant_ids[0],$added_by_id);
//            /*zx
//            删除调拨单明细*/
//            $sql = "DELETE oc_x_warehouse_requisition_item WHERE relevant_id IN(".$old_relevant_ids.")";
//            $query = $db->query($sql);
//            /*zx
//            删除调拨单中间表*/
//            $sql = "DELETE oc_x_warehouse_requisition_temporary WHERE relevant_status_id = 4 AND relevant_id IN(".$old_relevant_ids.")";
//            $query = $db->query($sql);

            /*zx
//            删除合并历史表数据*/
//            $sql = "UPDATE oc_x_warehouse_requisition_merge_history SET status = 0 WHERE status = 1 AND new_relevant_id = ".$relevant_ids[0];
//            $query = $db->query($sql);
//            /*zx
//            删除调拨单数据*/
//            $sql = "DELETE oc_x_warehouse_requisition WHERE relevant_id = ".$relevant_ids[0];
//            $query = $db->query($sql);
//            /*zx
//            删除调拨单明细*/
//            $sql = "DELETE oc_x_warehouse_requisition_item WHERE relevant_id = ".$relevant_ids[0];
//            $query = $db->query($sql);
//            /*zx
//            删除调拨单中间表*/
//            $sql = "DELETE oc_x_warehouse_requisition_temporary WHERE relevant_id = ".$relevant_ids[0];
//            $query = $db->query($sql);

            return 1;
        /*入库*/
        } else {
            /*zx
            入库无法取消*/
            return 3;
        }
    }
    /*zx
    获得框子的信息*/
    public function getContainerInformation(array $data){
        global $db;
        $container_id = isset($data['data']['container_id']) ? trim($data['data']['container_id']) : false;
        $relevant_id = isset($data['data']['relevant_id']) ? trim($data['data']['relevant_id']) : false;

        $sql = "SELECT odoi.deliver_order_id,odoi.inv_comment  
FROM oc_x_warehouse_requisition_item owri  
LEFT JOIN oc_x_deliver_order odo ON owri.relevant_id = odoi.relevant_id
LEFT JOIN oc_x_deliver_order_inv odoi ON odo.deliver_order_id = odoi.deliver_order_id
WHERE owri.relevant_id = ".$relevant_id. " 
AND owri.container_id = ".$container_id."  
GROUP BY owri.relevant_id";
        $query = $db->query($sql);
        $result = $query->row;
        return $result;
    }
    /*zx
    获取do单的仓库与分拣仓库*/
    public function get_frame_vg_list_status(array $data){
        global $db;
        $deliver_order_id = isset($data['data']['deliver_order_id']) ? $data['data']['deliver_order_id'] : false;
        $sql = "SELECT warehouse_id,do_warehouse_id,is_repack  
FROM oc_x_deliver_order 
WHERE deliver_order_id = ".$deliver_order_id;
        $query = $db->query($sql);
        $result = $query->row;
        return $result;
    }
    /*zx
    检验货位号是否唯一*/
    public function get_frame_vg_list_unique(array $data){
        global $db;
        $container_id = isset($data['data']['container_id']) ? $data['data']['container_id'] : false;
        $sql = "SELECT odoi.inv_comment  
FROM oc_x_deliver_order_inv odoi 
LEFT JOIN oc_x_deliver_order odo ON odo.deliver_order_id = odoi.deliver_order_id
LEFT JOIN oc_x_consolidated_order oco ON oco.order_id = odo.order_id  
WHERE odoi.inv_comment = '".$container_id."'
AND odo.order_status_id = 6 
AND oco.order_id is null";
        $query = $db->query($sql);
        $result = $query->row;
        if ($result) {
            return 1;
        } else {
            return 2;
        }
    }

    //新调拨单申请 Alex 20180310 开始
    function getTransferBoxes(array $data){
        global $db;

        $deliverDate = $data['data']['deliverDate']? $data['data']['deliverDate'] : false;
        $doWarehouseId = $data['data']['doWarehouseId']? (int)$data['data']['doWarehouseId'] : false;
        $toWarehouseId = $data['data']['toWarehouseId']? (int)$data['data']['toWarehouseId'] : false;
        $type = $data['data']['type']? (int)$data['data']['type'] : false;

        if(!$deliverDate || !$doWarehouseId || !$toWarehouseId){
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'ERR101: missing deliver date or warehouse id',
                'return_data' => ''
            );
        }else{
            if ($type == 1) {
                //可调拨订单及篮筐
                $sql = "
              SELECT o.order_id, oi.deliver_order_id, oi.frame_count, oi.frame_vg_list, oi.inv_comment, o.is_repack, o.do_warehouse_id,o.warehouse_id
              FROM oc_x_deliver_order o
              LEFT JOIN oc_x_deliver_order_inv oi ON o.deliver_order_id = oi.deliver_order_id
              WHERE o.order_status_id IN (6,8)  AND o.deliver_date = '".$deliverDate."' AND o.do_warehouse_id = '".$doWarehouseId."' AND o.warehouse_id = '".$toWarehouseId."'
            ";
            //return $sql;

            $query = $db->query($sql);
            $results = $query->rows;

            $boxes = array();

            //Check Duplicated Box?
            foreach($results as $m){
                if($m['frame_vg_list'] !== ''){
                    $tmp = explode(',',$m['frame_vg_list']);
                    foreach($tmp as $n){
                        $boxes[$n]['order_id'] = $m['order_id'];
                        $boxes[$n]['deliver_order_id'] = $m['deliver_order_id'];
                        $boxes[$n]['pos'] = $m['inv_comment'];
                        $boxes[$n]['frame_count'] = $m['frame_count'];
                    }
                }
            }

                //调拨中的篮筐
                $sql = "SELECT a.relevant_id, a.date_added, b.container_id FROM oc_x_warehouse_requisition a
                    LEFT JOIN oc_x_warehouse_requisition_item b ON a.relevant_id = b.relevant_id
                    WHERE DATE(deliver_date) = '".$deliverDate."' AND a.relevant_status_id != 3 AND a.status!=0 AND from_warehouse = '".$doWarehouseId."' AND to_warehouse = '".$toWarehouseId."' AND out_type = 1
                    GROUP BY b.container_id HAVING b.container_id > 0";
                $query = $db->query($sql);
                $results = $query->rows;

            $transferringBoxes = array();
            foreach($results as $m){
                $transferringBoxes[$m['container_id']] = $m;
            }

                $return = array(
                    'return_code' => 'SUCCESS',
                    'return_msg'  => 'OK',
                    'return_data' => array('readyBoxes'=>$boxes, 'transferringBoxes'=>$transferringBoxes)
                );
            } else {
                //可调拨订单及商品
                $sql = "
SELECT o.order_id, SUM(odop.quantity) AS quantity,odop.deliver_order_id, odop.product_id,ptw.sku_barcode, o.is_repack, o.do_warehouse_id,o.warehouse_id,op.name,GROUP_CONCAT(oi.deliver_order_id) AS deliver_order_id,GROUP_CONCAT(oi.inv_comment) AS inv_comment,GROUP_CONCAT(odop.quantity) AS products_num
FROM oc_x_deliver_order o
LEFT JOIN oc_x_deliver_order_inv oi ON o.deliver_order_id = oi.deliver_order_id
LEFT JOIN oc_x_inventory_order_sorting odop ON oi.deliver_order_id = odop.deliver_order_id 
LEFT JOIN oc_product_to_warehouse ptw ON ptw.product_id = odop.product_id AND ptw.warehouse_id = o.warehouse_id
LEFT JOIN oc_product op ON op.product_id = odop.product_id
WHERE o.order_status_id IN (6,8) 
and odop.status =1 
AND o.deliver_date = '".$deliverDate."' 
AND o.do_warehouse_id = '".$doWarehouseId."' 
AND o.warehouse_id = '".$toWarehouseId."'
AND odop.container_id = 0
GROUP BY odop.product_id
            ";
//                return $sql;

                $query = $db->query($sql);
                $results = $query->rows;

                $boxes = array();

                //Check Duplicated Box?
                foreach($results as $m){
                    $boxes[$m['product_id']] = $m;
                    $boxes[$m['sku_barcode']] = $m;
                }

                //调拨中的商品
                $sql = "SELECT a.relevant_id,SUM(b.num) AS quantity, a.date_added, b.product_id ,ptw.sku_barcode
FROM oc_x_warehouse_requisition a
LEFT JOIN oc_x_warehouse_requisition_item b ON a.relevant_id = b.relevant_id
LEFT JOIN oc_product_to_warehouse ptw ON ptw.product_id = b.product_id
WHERE DATE(deliver_date) = '".$deliverDate."' 
AND a.relevant_status_id != 3 
AND a.status != 0 
AND a.from_warehouse = '".$doWarehouseId."' 
AND a.to_warehouse = '".$toWarehouseId."' 
AND b.container_id = 0
AND a.out_type = 1
GROUP BY b.product_id ";
//                return $sql;

                $query = $db->query($sql);
                $results = $query->rows;
                $transferringBoxes = array();
                foreach($results as $m){
                    $transferringBoxes[$m['product_id']] = $m;
                    $transferringBoxes[$m['sku_barcode']] = $m;
                }

                $return = array(
                    'return_code' => 'SUCCESS',
                    'return_msg'  => 'OK',
                    'return_data' => array('readyBoxes'=>$boxes, 'transferringBoxes'=>$transferringBoxes)
                );
            }

        }


        return $return;
    }
    /*Alex
    扫描生成调拨单*/
    function addTransferOrder(array $data){

        global $db,$dbm;


        $userId = $data['data']['user_id']? $data['data']['user_id'] : '';
        $deliverDate = $data['data']['deliverDate']? $data['data']['deliverDate'] : false;
        $doWarehouseId = $data['data']['doWarehouseId']? (int)$data['data']['doWarehouseId'] : false;
        $toWarehouseId = $data['data']['toWarehouseId']? (int)$data['data']['toWarehouseId'] : false;
        $add_type = $data['data']['addType']? (int)$data['data']['addType'] : false;
        $addList = sizeof($data['data']['addList']) ? $data['data']['addList'] : array();

        //return $addList;

        if(!sizeof($addList)){
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'ERR102: item list is blank.',
                'return_data' => ''
            );

            return $return;
        }

        if(!$deliverDate || !$doWarehouseId || !$toWarehouseId){
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'ERR103: missing deliver date or warehouse id',
                'return_data' => ''
            );

            return $return;
        }

        $dbm->begin();
        $bool = true;

        //添加主记录
        $sql = "INSERT INTO oc_x_warehouse_requisition (`relevant_status_id`,`from_warehouse`,`to_warehouse`,`date_added`,`added_by`,`status`,`out_type`,`deliver_date`)
        VALUES ( 2,'".$doWarehouseId."','".$toWarehouseId."',NOW(),'".$userId."',1,1,'".$deliverDate."')";

        $bool = $bool && $dbm->query($sql);
        $reqId = $dbm->getLastId();
        $this->insert_warehouse_requisition_history(2,$reqId,$userId);

        /*Alex
        周转筐调拨单提交*/
        if ($add_type == 1) {
            $boxList = array(0);
            $orderList = array(0);
            $boxListString = '';
            $orderListString = '';
            foreach($addList as $k=>$v){
                $boxList[] = $k;
                $orderList[] = $v['order_id'];
            }
            $boxListString = implode(',',$boxList);
            $orderListString = implode(',',$orderList);

            //查找空筐记录
            $checkItem = "SELECT DISTINCT container_id FROM oc_x_inventory_order_sorting WHERE order_id IN (".$orderListString.") AND container_id IN (".$boxListString.") and status = 1 ";
            $query = $dbm->query($checkItem);
            $results = $query->rows;
            $usingBox = array();

        //测试空筐
        //$boxList[] = 116810;
        //$boxList[] = 116809;
        foreach($results as $m){
            $usingBox[] = $m['container_id'];
        }
        $issueBox = array_diff($boxList,$usingBox);

            //添加按筐和订单查找的商品记录及空筐记录
            $req_item = "INSERT INTO `oc_x_warehouse_requisition_item` (`relevant_id`, `product_id`, `container_id`, `num`,`container_x`,`date_added`,`deliver_order_id`, `order_id`)
                     SELECT '".$reqId."', product_id, container_id,quantity,'X',now(),deliver_order_id,order_id
                     FROM oc_x_inventory_order_sorting WHERE order_id IN (".$orderListString.") AND container_id IN (".$boxListString.") AND container_id>0 and status = 1 ";
            $bool = $bool && $dbm->query($req_item);

            if(sizeof($issueBox)){
                $req_issue_item = "INSERT INTO `oc_x_warehouse_requisition_item` (`relevant_id`, `product_id`, `container_id`, `num`,`container_x`,`date_added`,`deliver_order_id`, `order_id`) VALUES ";
                //$deliver_order_ids = '';
                foreach($issueBox as $m){
                    $req_issue_item .= "(".$reqId.",0,$m,0,'X',now(),'".$addList[$m]['deliver_order_id']."','".$addList[$m]['order_id']."' ),";
                    //$deliver_order_ids .= ','.$addList[$m]['deliver_order_id'];
                }
                $req_issue_item  = rtrim($req_issue_item, ',');
                $bool = $bool && $dbm->query($req_issue_item);
            }

            //同时记录临时表
            $req_item = "INSERT INTO `oc_x_warehouse_requisition_temporary` (`relevant_id`, `relevant_status_id`, `product_id`, `container_id`, `quantity`,`warehouse_id`,`date_added`,`deliver_order_id`, `order_id`)
                     SELECT '".$reqId."',2, product_id, container_id,quantity, '".$doWarehouseId."' ,now(),deliver_order_id,order_id
                     FROM oc_x_inventory_order_sorting WHERE order_id IN (".$orderListString.") AND container_id IN (".$boxListString.") AND container_id>0 and status =1 ";
            $bool = $bool && $dbm->query($req_item);

            if(sizeof($issueBox)){
                $req_issue_item = "INSERT INTO `oc_x_warehouse_requisition_temporary` (`relevant_id`,`relevant_status_id`, `product_id`, `container_id`, `quantity`,`warehouse_id`,`date_added`,`deliver_order_id`, `order_id`) VALUES ";
                foreach($issueBox as $m){
                    $req_issue_item .= "(".$reqId.",2,0,$m,0,'".$doWarehouseId."',now(),'".$addList[$m]['deliver_order_id']."','".$addList[$m]['order_id']."' ),";
                }
                $req_issue_item  = rtrim($req_issue_item, ',');
                $bool = $bool && $dbm->query($req_issue_item);
            }
            //$deliver_order_ids = ltrim($deliver_order_ids,',');
            //$sql = "UPDATE oc_x_deliver_order SET relevant_id = ".$reqId." WHERE deliver_order_id IN (".$deliver_order_ids.")";
            //$bool = $bool && $dbm->query($sql);
        /*zx
        整件商品调拨单提交*/
        } else if ($add_type == 2) {

            $req_issue_item = "INSERT INTO `oc_x_warehouse_requisition_item` (`relevant_id`, `product_id`, `container_id`, `num`,`container_x`,`date_added`,`deliver_order_id`, `order_id`) VALUES ";
            $req_issue_item2 = "INSERT INTO `oc_x_warehouse_requisition_temporary` (`relevant_id`,`relevant_status_id`, `product_id`, `container_id`, `quantity`,`warehouse_id`,`date_added`,`deliver_order_id`, `order_id`) VALUES ";
            foreach($addList as $k=>$v){
                $req_issue_item .= "(".$reqId.",".$v['product_id'].",0,".$v['quantity'].",'0',now(),'0','0'),";
                $req_issue_item2 .= "(".$reqId.",2,".$v['product_id'].",0,".$v['quantity'].",'".$doWarehouseId."',now(),'0','0'),";
            }
            $req_issue_item  = rtrim($req_issue_item, ',');
            $req_issue_item2  = rtrim($req_issue_item2, ',');
            $bool = $bool && $dbm->query($req_issue_item);
            $bool = $bool && $dbm->query($req_issue_item2);
        }


        if (!$bool) {
            $dbm->rollback();
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'ERR104: add transfer order fail.',
                'return_data' => ''
            );

        } else {
            $return = array(
                'return_code' => 'SUCCESS',
                'return_msg'  => 'OK',
                'return_data' => $reqId
            );
            $dbm->commit();
        }

        return $return;
    }
    /*Alex
    获取调拨单信息*/
    function getTransferOrder(array $data){
        global $db;

        $userId = $data['data']['user_id']? $data['data']['user_id'] : '';
        $deliverDate = $data['data']['deliverDate']? $data['data']['deliverDate'] : false;
        $doWarehouseId = $data['data']['doWarehouseId']? (int)$data['data']['doWarehouseId'] : false;
        $toWarehouseId = $data['data']['toWarehouseId']? (int)$data['data']['toWarehouseId'] : false;
        $addList = sizeof($data['data']['addList']) ? $data['data']['addList'] : array();

        //return $addList;

        if(!$deliverDate || !$doWarehouseId || !$toWarehouseId){
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'ERR103: missing deliver date or warehouse id',
                'return_data' => ''
            );

            return $return;
        }

        $sql = "SELECT
                a.relevant_id,
                a.date_added,
                c.name status_name,
                a.relevant_status_id,
                if(b.container_id=0,2,1) relevant_type,
                if(b.container_id=0, SUM(b.num),COUNT(DISTINCT b.container_id)) num
                FROM oc_x_warehouse_requisition a
                LEFT JOIN oc_x_warehouse_requisition_item b  ON a.relevant_id = b.relevant_id
                LEFT JOIN oc_x_warehouse_requisition_status c ON a.relevant_status_id = c.relevant_status_id
                WHERE a.from_warehouse = ".$doWarehouseId." AND a.to_warehouse = '".$toWarehouseId."' AND a.deliver_date = '".$deliverDate."'
                AND a.relevant_status_id != 3 AND a.status !=0
                GROUP BY a.relevant_id";
        $query = $db->query($sql);
        $results = $query->rows;

        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg'  => $sql,
            'return_data' => $results
        );

        return $return;
    }
    /*Alex
    取消订单*/
    function cancelTransferOrder(array $data){
        global $dbm;

        $dbm->begin();
        $bool = true;

        $reqId = $data['data']['reqId']? $data['data']['reqId'] : '';
        $add_user = $data['data']['userId']? $data['data']['userId'] : '';
        if(!$reqId){
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'ERR105: missing relevant_id id',
                'return_data' => ''
            );
        }
        $sql = "SELECT relevant_status_id AS status FROM oc_x_warehouse_requisition WHERE relevant_id = ".$reqId;
        $query = $dbm->query($sql);
        $relevant_status = $query->row;
        if ($relevant_status['relevant_status_id'] != 2) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'ERR107: invalid status.',
                'return_data' => ''
            );
        }

        $sql = "UPDATE oc_x_warehouse_requisition SET relevant_status_id = 3 WHERE relevant_id = '".$reqId."'";
        $bool = $bool && $dbm->query($sql);

        $sql = "UPDATE oc_x_deliver_order SET relevant_id = 0 WHERE relevant_id = '".$reqId."'";
        $bool = $bool && $dbm->query($sql);
        $this->insert_warehouse_requisition_history(3,$reqId,$add_user);

        if (!$bool) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'ERR106: cancel transfer order fail.',
                'return_data' => ''
            );
            $dbm->rollback();
        }else {
            $return = array(
                'return_code' => 'SUCCESS',
                'return_msg'  => 'OK',
                'return_data' => ''
            );
            $dbm->commit();
        }

        return $return;
    }
    //新调拨单申请 Alex 20180310 结束

    function getOrderContainerHistory(array $data){
        global  $db ;
        $count_id = $data['data']['count_id']? $data['data']['count_id'] : '';
        $warehouse_id = $data['data']['warehouse_id']? $data['data']['warehouse_id'] : '';

        $sql  =  " select do.deliver_order_id  , do.order_id , doi.uptime ,doi.inv_comment , doi.deliver_order_id , od.inventory_name ,doi.frame_vg_list from  oc_x_deliver_order do LEFT  JOIN  oc_x_deliver_order_inv doi on  do.deliver_order_id = doi.deliver_order_id left JOIN  oc_order_distr  od  on od.deliver_order_id = doi.deliver_order_id    where (doi.frame_vg_list like '%,$count_id%' or  doi.frame_vg_list in ($count_id) ) and do.do_warehouse_id = '".$warehouse_id."'  and do.order_status_id in( 6,8)  order by doi.order_inv_id  desc limit 10";

        $query = $db->query($sql);
        $results = $query->rows;
        return $results;

    }

    //可售库存返还 Alex 20180324 结束
    function getInventorySortingReturn(array $data){
        global $db;

        $userId = $data['data']['user_id']? $data['data']['user_id'] : '';
        $setDate = $data['data']['setDate']? $data['data']['setDate'] : false;
        $doWarehouseId = $data['data']['doWarehouseId']? (int)$data['data']['doWarehouseId'] : false;
        $productId = $data['data']['productId']? (int)$data['data']['productId'] : 0;

        //return $addList;

        if(!$setDate || !$doWarehouseId){
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'Missing sorting date or warehouse id',
                'return_data' => ''
            );

            return $return;
        }

        //Return
        $sql = "select
                concat_ws('_',date_format('".$setDate."','%Y%m%d'),O.do_warehouse_id,OP.product_id) item_key,
                O.do_warehouse_id,
                OP.product_id,
                OP.name product_name,
                sum(OP.quantity) order_qty,
                sum(if(ORT.return_qty is null, 0 ,ORT.return_qty)) miss_qty,
                sum(if(ORT.return_total is null, 0 ,ORT.return_total)) miss_total,
                group_concat(if(ORT.return_qty>0, concat_ws('-',O.order_id,OV.inv_comment,ORT.return_qty), NULL)) order_inv
                from oc_x_deliver_order O
                inner join (select distinct order_id from oc_x_inventory_order_sorting  where status =1  and  uptime between '".$setDate."' and date_add('".$setDate."', interval 1 day) and move_flag = 1) T on O.order_id = T.order_id
                left join oc_order_inv OV on O.order_id = OV.order_id
                left join oc_x_deliver_order_product OP on O.deliver_order_id = OP.deliver_order_id
                left join
                (
                    select AA.order_id, BB.product_id, sum(BB.quantity) return_qty, sum(BB.total) return_total
                    from oc_return AA
                    inner join (select distinct order_id from oc_x_inventory_order_sorting  where status =1  and  uptime between '".$setDate."' and date_add('".$setDate."', interval 1 day) and move_flag = 1)  TT on AA.order_id = TT.order_id
                    left join oc_return_product BB on AA.return_id = BB.return_id
                    where  AA.return_reason_id=1 and AA.return_status_id = 2
                    group by AA.order_id, BB.product_id
                ) ORT on O.order_id = ORT.order_id and OP.product_id = ORT.product_id
                where O.do_warehouse_id = '".$doWarehouseId."' and O.order_status_id != 3 and O.station_id = 2";
        if($productId > 0){
            $sql .= " and OP.product_id = '".$productId."'";
        }
        $sql .= "
                group by O.do_warehouse_id,OP.product_id having miss_qty > 0
                order by O.do_warehouse_id, miss_total desc";

        $query = $db->query($sql);
        $sortingReturnsRaw = $query->rows;
        $sortingReturns = array();

        //Added
        $sql_ext = "select item_key, product_id, sum(quantity) from oc_x_inventory_return_item where status = 1 and sorting_date = '".$setDate."' and do_warehouse_id = '".$doWarehouseId."' group by item_key";
        $query = $db->query($sql_ext);
        $returnAddedRaw = $query->rows;

        $returnAdded = array();
        foreach($returnAddedRaw as $m){
            $returnAdded[$m['item_key']] = $m;
        }

        foreach($sortingReturnsRaw as $m){
            $sortingReturns[$m['item_key']] = $m;
            $sortingReturns[$m['item_key']]['added_qty'] = array_key_exists($m['item_key'],$returnAdded) ? $returnAdded[$m['item_key']]['quantity'] : 0;
            $sortingReturns[$m['item_key']]['block'] =  ($setDate < '2018-03-25') ? 1 : 0;
        }

        $return = array(
            'return_code' => 'SUCCESS',
            'return_msg'  => 'Hi',
            'return_data' => array('sortingReturns'=>$sortingReturns, 'returnAdded'=>$returnAdded)
        );

        return $return;
    }

    function addInventorySortingReturn(array $data){
        global $dbm;

        $userId = $data['data']['userId']? $data['data']['userId'] : '';
        $setDate = $data['data']['setDate']? $data['data']['setDate'] : false;
        $doWarehouseId = $data['data']['doWarehouseId']? (int)$data['data']['doWarehouseId'] : false;
        $item_key = $data['data']['item_key']? $data['data']['item_key'] : 0;
        $productId = $data['data']['productId']? (int)$data['data']['productId'] : 0;
        $quantity = $data['data']['quantity']? $data['data']['quantity'] : 0;

        //return $data;

        $dbm->begin();
        $bool = true;

        if(!$setDate || !$doWarehouseId){
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'Missing date or warehouse id or product',
                'return_data' => ''
            );

            return $return;
        }




        $sql_return_move = "INSERT INTO `oc_x_inventory_return_item` (`item_key`, `sorting_date`, `do_warehouse_id`, `product_id`, `quantity`, `status`, `date_added`, `added_by`)
                          VALUES ('".$item_key."','".$setDate."','".$doWarehouseId."','".$productId."','".$quantity."','1',now(),'".$userId."')";
        $bool = $bool && $dbm->query($sql_return_move);


        if (!$bool) {
            $return = array(
                'return_code' => 'ERROR',
                'return_msg'  => 'ERR106: cancel transfer order fail.',
                'return_data' => ''
            );
            $dbm->rollback();
        }else {
            $return = array(
                'return_code' => 'SUCCESS',
                'return_msg'  => $sql_return_move,
                'return_data' => ''
            );
            $dbm->commit();
        }

        return $return;
    }
    //可售库存返还 Alex 20180324 结束

    public  function  updateInvComment(array $data ){
        global  $db ;
        $order_id = $data['data']['order_id']? $data['data']['order_id'] : '';
        $inv_comment = $data['data']['inv_comment']? $data['data']['inv_comment'] : '';
        $sql = " update  oc_x_deliver_order_inv  set inv_comment = '".$inv_comment ."' where order_id  = '".$order_id."' ";
        $query = $db->query($sql);
        
        if($sql){
            return 1 ; 
        }else{
            return 2 ;
        }
    }
    
    /*
     * zx
     * 插入调拨历史记录表
     * */
    function insert_warehouse_requisition_history($relevant_status_id,$relevant_ids,$added_by_id){
        global  $db ;
        $relevant_ids = explode(',',$relevant_ids);
        /*
         * 状态修改记入历史表
         * */
        $sql = "INSERT INTO oc_x_warehouse_requisition_history (`relevant_status_id`,`relevant_id`,`date_added`,`confirmed_by`) VALUES";
        foreach ($relevant_ids as $relevant_id) {
            $sql .= "(".$relevant_status_id.",".$relevant_id.",NOW(),'".$added_by_id."'),";
        }
        $sql = rtrim($sql,',');
        $query = $db->query($sql);

        return 1;

    }
    /*
     * zx
     * 获取调拨历史记录表数据
     * */
    function get_warehouse_requisition_history($relevant_ids){
        global  $db ;
        $sql2 =  "SELECT
	GROUP_CONCAT(wrh.date_added,'@',owu.username,'@',wrs.name,'@',wrh.relevant_status_id) AS relevant_status,
  	wrh.relevant_id
FROM
	oc_x_warehouse_requisition_history wrh 
LEFT JOIN oc_x_warehouse_requisition_status wrs ON wrs.relevant_status_id = wrh.relevant_status_id
LEFT JOIN oc_w_user owu ON owu.user_id = wrh.confirmed_by 
WHERE
	wrh.relevant_id IN(".$relevant_ids.")
GROUP BY wrh.relevant_id";
        $query2 = $db->query($sql2);
        $resutl2 = $query2->rows;

        return $resutl2;

    }

}

$warehouse = new WAREHOUSE();
?>