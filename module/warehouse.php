<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');

class WAREHOUSE{


    public function getperms(array $data){
        global $db;
        $username = $data['data']['user'] ? $data['data']['user'] : '';

        $sql = "SELECT WUG.user_group_id , WU.user_id , WUG.perms FROM  oc_w_user  WU LEFT JOIN oc_w_user_group WUG ON WU.user_group_id = WUG.user_group_Id  WHERE WU.username = '" . $db->escape($username) . "' ";
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
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE p.sku like '%".$sku."%'AND OP.order_id = '".$order_id."' AND  p.repack = 1 ";

        }elseif(is_numeric($sku) && strlen($sku) <= 6){
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id   WHERE p.product_id = '".$sku."' AND OP.order_id = '".$order_id."' AND p.repack = 1 ";
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
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id  WHERE p.sku like '%".$sku."%'AND OP.order_id = '".$order_id."' AND  p.repack = 0 ";

        }elseif(is_numeric($sku) && strlen($sku) <= 6){
            $sql = "SELECT pd.name, p.status, p.sku, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_product AS p LEFT JOIN oc_product_description AS pd ON p.product_id = pd.product_id  LEFT JOIN  oc_order_product OP  ON OP.product_id = p.product_id LEFT JOIN  oc_order O ON  O.order_id = OP.order_id   WHERE p.product_id = '".$sku."' AND OP.order_id = '".$order_id."' AND p.repack = 0 ";
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
        $sql = "  SELECT A.order_id , B.product_id ,sum(B.quantity) AS order_quantity ,C.sku ,C.name,
  sum(if(OM.quantity*-1 is null,'0',OM.quantity*-1)) AS oty_quantity,if(D.STATUS =1,'1','0') as status,
  C.station_section_title FROM oc_order A
                  LEFT JOIN oc_order_product B ON  A.order_id = B.order_id
                  LEFT JOIN oc_product C   ON  B.product_id = C.product_id
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
        $sql = "  SELECT A.order_id , B.product_id ,sum(B.quantity) AS order_quantity ,C.sku ,C.name,
  sum(if(OM.quantity*-1 is null,'0',OM.quantity*-1)) AS oty_quantity,if(D.status =1,'1','0') as status,
  C.station_section_title FROM oc_order A
                  LEFT JOIN oc_order_product B ON  A.order_id = B.order_id
                  LEFT JOIN oc_product C   ON  B.product_id = C.product_id
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

        $sql = "SELECT
                C.inv_comment,
                A.order_id,
                F.name,
                sum(B.quantity) AS  quantity,
                E.logistic_driver_title,
                A.order_deliver_status_id,
                A.order_status_id,
                os.name AS order_name
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
        $sql .=" ORDER BY A.order_id ";

        $query = $db->query($sql);
        $result = $query->rows;

        return $result;
    }

    public function confirm_orderStatus(array $data){
        global  $db;
        $order_id = $data['data']['order_id'] ;
        $user_id = $data['data']['user_id'];
        $logistic_allot_id = $data['data']['logistic_allot_id'];
        $comment = '订单配送状态改为出库确认';
        $sql = "SELECT A.order_id ,A.order_status_id,A.order_payment_status_id  FROM oc_x_logistic_allot_order B   LEFT JOIN  oc_order A ON A.order_id = B.order_id WHERE B.order_id = '". $order_id ."' AND B.logistic_allot_id = '". $logistic_allot_id ."' and A.order_deliver_status_id = 1";
        $query = $db->query($sql);
        $result = $query->row;
        if($result){
            $sql = "insert into oc_order_history (`order_id`,`comment`,`date_added`,`order_status_id`,`order_payment_status_id`,`order_deliver_status_id`,`modified_by` ) VALUES ('".$order_id ."','".$comment ."',current_timestamp() , '".$result['order_status_id'] ."','".$result['order_payment_status_id']."','8','".$user_id."')";
            $query = $db->query($sql);
            if($query){
                $sql ="update oc_order set order_deliver_status_id = 2 WHERE order_id= '". $order_id ."' and order_deliver_status_id = 1";
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
               $sql = "SELECT A.order_id ,A.order_status_id,A.order_payment_status_id  FROM oc_x_logistic_allot_order B   LEFT JOIN  oc_order A ON A.order_id = B.order_id WHERE B.order_id = '". $value ."'  and A.order_deliver_status_id = 1 AND B.logistic_allot_id = '". $logistic_id ."' and A.order_status_id !=3";

               $query = $db->query($sql);
               $result = $query->row;
               if($result){
                   $sql = "insert into oc_order_history (`order_id`,`comment`,`date_added`,`order_status_id`,`order_payment_status_id`,`order_deliver_status_id`,`modified_by` ) VALUES ('".$value ."','".$comment ."',current_timestamp() , '".$result['order_status_id'] ."','".$result['order_payment_status_id']."','8','".$user_id."')";
                   $query = $db->query($sql);
                   if($query){
                       $sql ="update oc_order set order_deliver_status_id = 2 WHERE order_id= '". $value ."' and order_deliver_status_id = 1";
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
}



$warehouse = new WAREHOUSE();
?>