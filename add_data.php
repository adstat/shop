<?php
header("Content-Type: text/html;charset=utf-8"); 
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'config.php';

$mysql = mysql_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD);

$db = mysql_select_db(DB_DATABASE,$mysql);

mysql_query("set character set 'utf8'");//读库
mysql_query("set names 'utf8'");//写库 

$sql = "SELECT
	c.customer_id,c.firstname,c.merchant_name,o.order_id,o.shipping_firstname,o.shipping_phone,o.shipping_address_1,o.date_added,o.total,o.bd_name,o.phone
FROM
	oc_customer AS c
LEFT JOIN (
	SELECT
		od.*,b.bd_name,b.phone
	FROM
		oc_order as od 
	left join oc_x_bd as b on od.bd_id = b.bd_id
	where od.order_status_id !=3 
	ORDER BY
		od.order_id DESC
) AS o ON c.customer_id = o.customer_id
GROUP BY
	c.customer_id
ORDER BY
	c.customer_id DESC";
$query = mysql_query($sql);
$order = array();
while($assoc = mysql_fetch_assoc($query)){
    if($assoc['date_added'] && $assoc['date_added'] < '2015-12-01 00:00:00'){
        $sql1 = "select * from oc_order_product as op LEFT JOIN oc_product_to_category AS ptc ON op.product_id = ptc.product_id where op.order_id = " . $assoc['order_id'];
        $query1 =  mysql_query($sql1);
        $order_product = array();
        while($assoc1 = mysql_fetch_assoc($query1)){
            $order_product[] = $assoc1;
        }
        $assoc['order_product'] = $order_product;
        $order[] = $assoc;
    }
}


foreach($order as $k=>$v){
    $order[$k]['category_67'] = 0;
    $order[$k]['category_65_66'] = 0;
    $order[$k]['category_other'] = 0;
    foreach($v['order_product'] as $key=>$value){
        if($value['category_id'] == 67){
            $order[$k]['category_67'] += $value['quantity'];
        }
        else if($value['category_id'] == 65 || $value['category_id'] == 66){
            $order[$k]['category_65_66'] += $value['quantity'];
        }
        else{
            $order[$k]['category_other'] += $value['quantity'];
        }
    }
}


?>

<table class="table table-bordered">
      
      <thead>
        
          
        <tr>
          <td align="left">商户ID</td>
          <td align="left">商户名称</td>
          <td align="left">订单ID</td>
          <td align="left">收货人/电话</td>
          <td align="left">BD/电话</td>
          <td align="left">配送地址</td>
          <td>商品数量</td>
          <td>下单时间</td>
          <td>订单金额</td>
          
          
        </tr>
      </thead>
      <tbody>
          <?php foreach($order as $o_key=>$o_value){?>
            <tr>
                <td><?php echo $o_value['customer_id'];?></td>
                <td><?php echo $o_value['merchant_name'];?></td>
                <td><?php echo $o_value['order_id'];?></td>
                <td><?php echo $o_value['shipping_firstname'];?>/<?php echo $o_value['shipping_phone'];?></td>
                <td><?php echo $o_value['bd_name'];?>/<?php echo $o_value['phone'];?></td>
                <td><?php echo $o_value['shipping_address_1'];?></td>
                <td>
                    叶：<?php echo $o_value['category_67'];?><br>
                    根+果：<?php echo $o_value['category_65_66'];?><br>
                    其它：<?php echo $o_value['category_other'];?>
                </td>
                <td><?php echo $o_value['date_added'];?></td>
                <td><?php echo $o_value['total'];?></td>
            </tr>
          <?php } ?>  
            <tr>
                <td colspan="15" align="right">其它：包含除叶菜类、根茎类、茄果类 外其它所有商品(奶制品、肉、水果、菇类 等)</td>
            </tr>
        
        
        
      </tbody>
    </table>