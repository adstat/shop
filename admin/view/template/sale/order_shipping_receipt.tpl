<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
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

        .fontred{
            color: #e9322d;
        }
    </style>
</head>
<body>
<div class="no_print">切换为小票模板</div>
<!-- 开始小票模板 -->
<div class="container" id="print_receipt" style="display: block">
    <?php foreach ($orders as $order) { ?>
    <div style="page-break-after: always; width:180px">
        <h4><?php echo $order['store_name']; ?> 订单编号 #<?php echo $order['order_id']; ?></h4>
        <div style="display: block">关注鲜世纪微信公众号<br/ >售后电话：021-61532092<br /><img src="../image/qrcode.jpg" width="120" height="120"></div>
        <hr style="border-color:#ccc;margin: 0; margin: 3px auto"/>
        <div style="display: block">
            <b>下单日期：</b> <?php echo $order['date_added']; ?><br />
            <b>配送日期：</b> <?php echo $order['date_added']; ?><br />
            <br />
            <b>收货联系：</b><?php echo $order['shipping_phone']; ?><br />
            收货人：<?php echo $order['shipping_address']; ?>
        </div>
        <table class="table table-bordered" style="display:none">
            <thead>
            <tr>
                <td style="width: 50%;"><b><?php echo $text_to; ?></b></td>
                <td style="width: 50%;"><b><?php echo $text_contact; ?></b></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $order['shipping_address']; ?></td>
                <td><?php echo $order['telephone']; ?></td>
            </tr>
            </tbody>
        </table>

        <table class="table table-bordered" style="margin: 3px auto">
            <thead>
            <tr>
                <td><b>ID</b></td>
                <td><b>商品信息</b></td>
                <td align="right"><b>小计</b></td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($order['product'] as $product) { ?>
            <tr>
                <td><?php echo $product['product_id'] ?></td>
                <td>
                    <?php echo $product['name']; ?><br />
                    <?php echo $product['weight']; ?>*<?php echo $product['quantity']; ?><br />单价:<?php echo number_format($product['price'],2); ?>
                </td>
                <td align="center"><?php echo number_format($product['total'],2); ?></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>

        <table border="0" align="right">
            <tr>
                <td width="65%" align="right"><b>商品小计:</b></td>
                <td width="5%">&nbsp;</td>
                <td align="right"><b><?php echo $order['sub_total']; ?></b></td>
            </tr>
            <tr>
                <td align="right"><b>运费:</b></td>
                <td>&nbsp;</td>
                <td align="right"><b><?php echo $order['shipping_fee']; ?></b></td>
            </tr>
            <tr>
                <td align="right"><b>订单合计:</b></td>
                <td>&nbsp;</td>
                <td align="right"><b><?php echo $order['total']; ?></b></td>
            </tr>
            <tr>
                <td colspan="3" style="height: 5px"><hr style="border-color:#ccc;margin: 0; padding: 3px"/></td>
            </tr>
            <tr style="font-size: 16px">
                <td align="right"><b>订单应收金额:</b></td>
                <td>&nbsp;</td>
                <td align="right"><b><?php echo $order['order_due_total']; ?></b></td>
            </tr>
            <tr>
                <td align="right"><b>[<?php echo $order['payment_status']?>]支付方式:</b></td>
                <td>&nbsp;</td>
                <td align="right"><b><?php echo $order['payment_method']; ?></b></td>
            </tr>
        </table>
        <?php if ($order['comment']) { ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <td><b><?php echo $column_comment; ?></b></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?php echo $order['comment']; ?></td>
            </tr>
            </tbody>
        </table>
        <?php } ?>
        <div style="clear: both; float: none"></div>
    </div>
    <?php } ?>
</div>

<!-- 开始A4模板 -->
<div class="container" id="print_a4" style="display: block">
  <?php foreach ($orders as $order) { ?>
  <div style="page-break-after: always; width:650px">
    <h3><?php echo $order['store_name']; ?> 订单编号 #<?php echo $order['order_id']; ?></h3>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td style="width: 50%;"><?php echo $text_order_detail; ?>(ID: <?php echo $order['order_id']; ?>)</td>
          <td style="width: 50%;">关注鲜世纪微信公众号（售后电话：021-61532092）</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="width: 50%;">
            <b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
            <?php if ($order['invoice_no']) { ?>
            <b><?php echo $text_invoice_no; ?></b> <?php echo $order['invoice_no']; ?><br />
            <?php } ?>
            <?php if ($order['shipping_method']) { ?>
            <b><?php echo $text_shipping_method; ?></b> <?php echo $order['shipping_method']; ?><br />
            <?php } ?>
            <br />
            收货联系：<?php echo $order['shipping_phone']; ?><br />
            收货人：<?php echo $order['shipping_address']; ?>
          </td>
          <td><img src="../image/qrcode.jpg" width="120" height="120"></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered" style="display:none">
      <thead>
        <tr>
          <td style="width: 50%;"><b><?php echo $text_to; ?></b></td>
          <td style="width: 50%;"><b><?php echo $text_contact; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $order['shipping_address']; ?></td>
          <td><?php echo $order['telephone']; ?></td>
        </tr>
      </tbody>
    </table>

    <table class="table table-bordered">
      <thead>
        <tr>
          <td><b>商品ID</b></td>
          <td><b><?php echo $column_product; ?></b></td>
          <td><b><?php echo $column_weight; ?></b></td>
          <td><b>商品单价</b></td>
          <td class="text-right"><b><?php echo $column_quantity; ?></b></td>
          <td align="center"><b>商品小计</b></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($order['product'] as $product) { ?>
        <tr>
          <td><?php echo $product['product_id'] ?>
          </td>
          <td><?php echo $product['name']; ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
            <?php } ?></td>
          <td><?php echo $product['weight']; ?></td>
          <td><?php echo number_format($product['price'],2); ?></td>
          <td class="text-right"><?php echo $product['quantity']; ?></td>
          <td align="center"><?php echo number_format($product['total'],2); ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>

    <table border="0" align="right">
        <tr>
            <td width="70%" align="right"><b>商品小计:</b></td>
            <td width="5%">&nbsp;</td>
            <td align="right"><b><?php echo $order['sub_total']; ?></b></td>
        </tr>
        <tr>
            <td align="right"><b>运费:</b></td>
            <td>&nbsp;</td>
            <td align="right"><b><?php echo $order['shipping_fee']; ?></b></td>
        </tr>
        <tr>
          <td align="right"><b>订单合计:</b></td>
            <td>&nbsp;</td>
          <td align="right"><b><?php echo $order['total']; ?></b></td>
        </tr>
        <tr>
            <td colspan="3" style="height: 5px"><hr style="border-color:#ccc;margin: 0; padding: 3px"/></td>
        </tr>
        <tr style="font-size: 16px">
            <td align="right"><b>订单应收金额:</b></td>
            <td>&nbsp;</td>
            <td align="right"><b><?php echo $order['order_due_total']; ?></b></td>
        </tr>
        <tr>
            <td align="right"><b>[<?php echo $order['payment_status']?>] 支付方式:</b></td>
            <td>&nbsp;</td>
            <td align="right"><b><?php echo $order['payment_method']; ?></b></td>
        </tr>
    </table>
    <?php if ($order['comment']) { ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td><b><?php echo $column_comment; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $order['comment']; ?></td>
        </tr>
      </tbody>
    </table>
    <?php } ?>
    <div style="clear: both; float: none"></div>
  </div>
  <?php } ?>
</div>
</body>
</html>