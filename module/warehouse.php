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
        $sql .=" ORDER BY E.logistic_driver_id ,A.order_deliver_status_id, A.order_id desc";

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



    //出库单
    public function getWarehouseRequisition( array $data){
        global $db;

        $warehouse_id = isset($data['data']['warehouse_id']) ? $data['data']['warehouse_id'] : false;

        $sql = " select wr.relevant_id ,wr.from_warehouse ,wr.to_warehouse,wr.date_added,u.username,wr.deliver_date,wrs.name ,w.title,wr.relevant_status_id,wr.out_type,wr.comment
              from  oc_x_warehouse_requisition wr
              LEFT JOIN oc_x_warehouse w  ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN oc_x_warehouse_requisition_status wrs ON wr.relevant_status_id = wrs.relevant_status_id
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1  and DATE (wr.date_added) between  date_sub(current_date(), interval 3 day)  and  current_date()  ";

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
              LEFT JOIN oc_user u ON  u.user_id = wr.added_by WHERE 1=1 and
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

        $sql = " select wri.relevant_id ,wri.product_id ,p.name ,pi.inventory ,wri.num ,ps.product_section_title ,w.title,wr.out_type
              from oc_x_warehouse_requisition_item wri
              LEFT JOIN  oc_product_section  ps ON wri.product_id = ps.product_id
              LEFT JOIN  oc_x_warehouse_requisition wr ON wr.relevant_id = wri.relevant_id
              LEFT JOIN  oc_x_warehouse w ON  wr.to_warehouse = w.warehouse_id
              LEFT JOIN  oc_product p ON wri.product_id = p.product_id
              LEFT JOIN  oc_product_inventory pi ON wri.product_id = pi.product_id and wr.from_warehouse = pi.warehouse_id
              WHERE  wri.relevant_id = '".$relevant_id."' ";

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
                $sql2 = "select wri.product_id ,wri.relevant_id ,p.name ,pi.inventory ,wri.num ,ps.product_section_title ,w.title,wr.out_type
            from oc_x_warehouse_requisition_item wri
            LEFT JOIN  oc_product_section  ps ON wri.product_id = ps.product_id
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
                $sql1 = " select   wrt.product_id ,p.name, ps.product_section_title ,wri.num  ,wrt.quantity
                from oc_x_warehouse_requisition_temporary wrt
                LEFT JOIN oc_x_warehouse_requisition_item wri ON  wrt.relevant_id = wri.relevant_id and wrt.product_id = wri.product_id
                LEFT JOIN oc_product p ON  p.product_id = wrt.product_id
                LEFT JOIN  oc_product_section ps ON  ps.product_id = wrt.product_id
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

        if (!$sku) {
            return false;
        }
        if(is_numeric($sku) && strlen($sku) > 6){
            $sql = " select wri.product_id,psb.sku_barcode,p.name,ps.product_section_title,wri.num
                  from oc_x_warehouse_requisition_item  wri
                  LEFT JOIN oc_product_sku_barcode psb  ON  wri.product_id = psb.product_id
                  LEFT JOIN oc_product p ON  wri.product_id = p.product_id
                  LEFT JOIN oc_product_section ps ON  wri.product_id = ps.product_id
                  WHERE  wri.relevant_id = '".$relevant_id ."' and psb.sku_barcode = '".$sku."'";

        }elseif(is_numeric($sku) && strlen($sku) <= 6){
            $sql = " select wri.product_id,p.name,ps.product_section_title,wri.num
                  from oc_x_warehouse_requisition_item  wri
                  LEFT JOIN oc_product p ON  wri.product_id = p.product_id
                  LEFT JOIN oc_product_section ps ON  wri.product_id = ps.product_id
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

        $sql = "select   wr.relevant_status_id
              from  oc_x_warehouse_requisition wr  WHERE wr.relevant_id = '".$relevant_id."'";

        $query = $db->query($sql);
        $result = $query->row;

        if($result['relevant_status_id'] == 2){
                $inventory_type_id = 21;
            $sql = "insert  into oc_x_stock_move
              (`relevant_id`,`timestamp`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id`  )
              VALUES ('".$relevant_id ."' ,UNIX_TIMESTAMP(NOW()), '".$inventory_type_id ."' ,NOW(),'".$warehouse_user ."' ,'".$warehouse_id ."')";

            $query = $db->query($sql);

            $inventory_move_id = $db->getLastId();

            if($inventory_move_id){
                foreach ($postData as $value){
                    $product_id = substr($value[0],9);
                    $quantity = $value[4]-$value[3];
                    $sql = "insert into oc_x_stock_move_item (`inventory_move_id`,`product_id`,`quantity`,`warehouse_id`)
                        VALUES ('".$inventory_move_id."','".$product_id."' , '". $quantity ."','".$warehouse_id."')";
                    $query = $db->query($sql);
                }
                $sql = " update oc_x_warehouse_requisition  set  relevant_status_id = 4 WHERE relevant_id = '".$relevant_id ."'";
                $query = $db->query($sql);
            }
        }else if($result['relevant_status_id'] == 4){
                $inventory_type_id = 22;
            $sql = "insert  into oc_x_stock_move
              (`relevant_id`,`timestamp`,`inventory_type_id`,`date_added`,`add_user_name` ,`warehouse_id`  )
              VALUES ('".$relevant_id ."' ,UNIX_TIMESTAMP(NOW()), '".$inventory_type_id ."' ,NOW(),'".$warehouse_user ."' ,'".$warehouse_id ."')";

            $query = $db->query($sql);

            $inventory_move_id = $db->getLastId();

            if($inventory_move_id){
                foreach ($postData as $value){
                    $product_id = substr($value[0],9);
                    $quantity = $value[3]-$value[4];
                    $sql = "insert into oc_x_stock_move_item (`inventory_move_id`,`product_id`,`quantity`,`warehouse_id`)
                        VALUES ('".$inventory_move_id."','".$product_id."' , '". $quantity ."','".$warehouse_id."')";
                    $query = $db->query($sql);
                }

                $sql = " update oc_x_warehouse_requisition  set  relevant_status_id = 6 WHERE relevant_id = '".$relevant_id ."'";
                $query = $db->query($sql);

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

        $sql = "SELECT pd.name, p.status, p.sku, p.box_size, p.inv_class_sort, p.model, p.price, p.product_id FROM oc_order_deliver_issue odi LEFT JOIN oc_order_product op ON odi.order_id = op.order_id LEFT JOIN oc_product AS p ON op.product_id = p.product_id LEFT JOIN oc_product_to_warehouse ptw ON p.product_id = ptw.product_id LEFT JOIN  oc_product_description AS pd ON p.product_id = pd.product_id WHERE odi.order_id = '".$order_id."' ";

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


}



$warehouse = new WAREHOUSE();
?>