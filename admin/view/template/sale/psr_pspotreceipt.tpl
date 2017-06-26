<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title>鲜世纪自提点签收单－<?php echo $deliver_date?></title>
<base href="<?php echo $base; ?>" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />
<style>
    @media print{
        a,
        a:visited {
            text-decoration: none;
        }
        a,
        a[href]:after,
        abbr[title]:after,
        a[href^="javascript:"]:after,
        a[href^="#"]:after{
            content: "";
        }

        .no_print{display:none};
    }
</style>
</head>
<body>
<div class="container">
<?php foreach($psrdata as $psr) {?>
  <div style="page-break-after: always; width:740px;">

    <table class="table table-bordered">
      <caption style="font-size:16px; margin:10px 0;">
        <img src="view/image/logo.png" border="0"> <b><?php echo $psr['name'] ?> <?php echo $deliver_date?></b> 签收单
      </caption>
      <thead>
        <tr>
          <td align="left" style="width: 10%;">订单编号</td>
          <td align="left" style="width: 8%;">会员</td>
          <td align="left" style="width: 12%;">收货人名称</td>
          <td align="left" style="width: 15%;">联系电话</td>
          <td align="left" style="width: 18%;">下单日期</td>
          <td align="left" style="width: 15%;">客户签收</td>
          <td align="left" style="">备注</td>
        </tr>
      </thead>
      <tbody>
      <?php foreach($psr['orders'] as $order) {?>
        <tr>
            <td><?php echo "<a target='_blank' href='index.php?route=sale/order&token=".$token."&filter_order_id=".$order['order_id']."'>".$order['order_id']."</a>"; ?></td>
            <td><?php echo $order['member_name']; ?></td>
            <td><?php echo $order['shipping_name']; ?></td>
            <td><?php echo $order['shipping_phone']; ?></td>
            <td><?php echo $order['order_date']; ?></td>
            <td></td>
            <td></td>
        </tr>
      <?php } ?>
        <tr>
            <td colspan="7" align="right"><b><?php echo $psr['name'] ?> <?php echo $deliver_date?></b> 签收单, 打印时间<?php echo date("Y-m-d H:i:s",time()+8*60*60);?></td>
        </tr>
      </tbody>
    </table>
  </div>
<?php } ?>

<?php if(sizeof($psrdata_unpaid)){?>

    <div style="page-break-after: always; width:740px">
        <hr class="no_print" />
        <table class="table table-bordered">
            <caption style="font-size:16px; margin:10px 0;">
                <img src="view/image/logo.png" border="0"> <b style="color:#CC0000"><?php echo $deliver_date?> 自提微信未支付订单</b>
            </caption>
            <thead>
            <tr>
                <td align="left" style="width: 10%;">订单编号</td>
                <td align="left" style="width: 8%;">会员</td>
                <td align="left" style="width: 12%;">收货人名称</td>
                <td align="left" style="width: 15%;">联系电话</td>
                <td align="left" style="width: 18%;">下单日期</td>
                <td align="left" style="width: 15%;">客户签收</td>
                <td align="left" style="">备注</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach($psrdata_unpaid as $order) {?>
            <tr>
                <td rowspan="2"><?php echo "<a target='_blank' href='index.php?route=sale/order&token=".$token."&filter_order_id=".$order['order_id']."'>".$order['order_id']."</a>"; ?></td>
                <td><?php echo $order['member_name']; ?></td>
                <td><?php echo $order['shipping_name']; ?></td>
                <td><?php echo $order['shipping_phone']; ?></td>
                <td><?php echo $order['order_date']; ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4"><strong>订单自提点：</strong><?php echo $order['pickspot_name']; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="7" align="right"><b style="color:#CC0000"><?php echo $deliver_date?> 自提微信未支付订单</b>, 打印时间<?php echo date("Y-m-d H:i:s", time()+8*60*60);?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php } ?>

</div>
</body>
</html>