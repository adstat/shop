<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>门店订单</title>
<style>
.home{
	margin-left: auto;
	margin-right: auto;
	width:320px;
}
.title{
	margin-left: auto;
	margin-right: auto;
	text-align:center;
	width:320px;
}
.order{
	margin-left: auto;
	margin-right: auto;
	width:320px;
}
.table{
	margin-left: auto;
	margin-right: auto;
	width:320px;
}
.line{
	margin-left: auto;
	margin-right: auto;
	width:320px;
	border-top:1px dashed #cccccc;
	height: 1px;
	overflow:hidden
}
.prodtablelileft{
	float:left;
	width:149px;
	list-style:none outside;
	text-align:left;
}
.prodtableliright{
	float:left;
	width:129px;
	list-style:none outside;
	text-align:left;
}
.table div{
	float:left;
}
.tb1{
	width:94px;
}
.tb2{
	width:35px;
}
.tb3{
	width:60px;
}
.tb4{
	width:60px;
}
.clear{
	border-top:1px solid transparent !important;
	margin-top:-1px !important;
	border-top:0;
	margin-top:0;
	clear:both;
	visibility:hidden;
}
.logo{
	margin-left: auto;
	margin-right: auto;
	width:120px;
	height:46px;
}
.logo img{width:100%;height:100%;}
.qr{
	margin-left: auto;
	margin-right: auto;
	width:215px;
	height:215px;
}
.qr img{width:100%;height:100%;}
.top{
	margin-left: auto;
	margin-right: auto;
	width:320px;
	height:50px;
}
.bottom{
	margin-left: auto;
	margin-right: auto;
	width:320px;
	height:100px;
}
</style>
</head>
<?php
if($_GET['auth'] !== 'klsdlcklsdnon23lnwl'){
    exit('NO Auth!');
}
$oid = $_GET['oid'];

require_once('config.php');
require_once(DIR_SYSTEM . 'startup.php');

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$orderProducts = $db->query("SELECT name,quantity,price,total FROM " . DB_PREFIX . "order_product WHERE order_id = ".$oid);
$orderMaster = $db->query("SELECT total,discount_total,sub_total,shipping_name,telephone,shipping_address_1,shipping_fee FROM " . DB_PREFIX . "order WHERE order_id = ".$oid);
$orderDiscountInfo = $db->query("SELECT title FROM " . DB_PREFIX . "order_total WHERE code = 'discount_total' and order_id = ".$oid);

$orderDiscountTitle = '优惠';
if(sizeof($orderDiscountInfo->rows)){
    $orderDiscountTitle = $orderDiscountInfo->row['title'];
}

?>
<body>
<div id="injection" class="home">
	<div class="top"></div>
	<div class="logo">
		<img src="../image/logo_t.png"/>
	</div>
	<div class="title" style="margin-bottom: 10px;">感谢您惠顾鲜世纪生鲜超市</div>
	<div class="order">订单号：<?php echo $oid; ?></div>

    <div class="table" style="margin-bottom: 10px;">
        <li class="prodtablelileft">
            <div class="tb1" style="font-weight: bold; font-size: 150%">合计应付</div>
            <div class="tb2"></div>
        </li>
        <li class="prodtableliright">
            <div class="tb3">&nbsp;</div>
            <div class="tb4" style="font-weight: bold; font-size: 150%">￥<?php echo $orderMaster->row['total']; ?></div>
        </li>
    </div>

    <div class="line"></div>

	<div class="table">
		<li class="prodtablelileft">
			<div class="tb1">商品</div>
			<div class="tb2">数量</div>
		</li>
		<li class="prodtableliright">
			<div class="tb3">单价</div>
			<div class="tb4">金额</div>
		</li>
	</div>
	<div class="line"></div>
	<div class="table">
		<?php if (sizeof($orderProducts->rows)) { ?>
			<?php foreach ($orderProducts->rows as $orderProduct) { ?>
				<li class="prodtablelileft">
					<div class="tb1"><?php echo $orderProduct['name']; ?></div>
					<div class="tb2"><?php echo $orderProduct['quantity']; ?></div>
				</li>
				<li class="prodtableliright">
					<div class="tb3">￥<?php echo substr($orderProduct['price'],0,strlen($orderProduct['price'])-2); ?></div>
					<div class="tb4">￥<?php echo substr($orderProduct['total'],0,strlen($orderProduct['total'])-2); ?></div>
				</li>
			<?php } ?>
		<?php } ?>
	</div>
	<div class="line"></div>
	<div class="table">
		<li class="prodtablelileft">
			<div class="tb1">合计</div>
			<div class="tb2"></div>
		</li>
		<li class="prodtableliright">
			<div class="tb3">&nbsp;</div>
			<div class="tb4">￥<?php echo $orderMaster->row['sub_total']; ?></div>
		</li>
		<li class="prodtablelileft">
			<div class="tb1">运费</div>
			<div class="tb2"></div>
		</li>
		<li class="prodtableliright">
			<div class="tb3">&nbsp;</div>
			<div class="tb4">￥<?php echo $orderMaster->row['shipping_fee']; ?></div>
		</li>
		<li class="prodtablelileft">
			<div class="tb1"><?php echo $orderDiscountTitle; ?></div>
			<div class="tb2"></div>
		</li>
		<li class="prodtableliright">
			<div class="tb3">&nbsp;</div>
			<div class="tb4">￥<?php echo $orderMaster->row['discount_total']; ?></div>
		</li>
	</div>
	<div class="line"></div>
	<div class="title" style="float: none; clear: both; margin-top: 60px;">微信扫一扫，关注鲜世纪公众号</div>
	<div class="qr">
		<img src="../image/qrcode.jpg"/>
	</div>
	<div class="title">微信下单满30元可0元购鸡蛋一份</div>
	<div class="title">手机生鲜超市，新鲜到家</div>
	<div class="bottom"></div>
	<div class="clear"></div>
</div>
</body>
</html> 