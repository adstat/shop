<?php ini_set('display_errors',0); ?>
<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title>鲜世纪采购订单－<?php echo $deliver_date?></title>
<base href="<?php echo $base; ?>" />
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

    <div style="page-break-after: always; width:840px">
    <table class="table table-bordered">
      <caption style="font-size:16px; margin:10px 0;">
          <img src="view/image/logo.png" border="0"> <b><?php echo $deliver_date?></b>需配送商品采购订单(含微信支付<b style="color:#CC0000">且尚未支付</b>)
          <?php
              if($filter_customer_group){
                  echo '<br /><b style="color:#CC0000">'. $customer_groups[$filter_customer_group]. '</b>';
              }
           ?>
      </caption>
      <thead>
        <tr>
          <td align="left" style="width: 8%;">商品编号</td>
            <td align="left" style="">商品名称</td>
            <td align="left" style="width: 8%;">二级分类</td>
            <td align="left" style="width: 8%;">一级分类</td>
            <td align="left" style="width: 7%;">均售价</td>
            <td align="left" style="width: 6%;">规格</td>
            <td align="left" style="width: 5%;">按克</td>
            <td align="left" style="width: 5%;">合计</td>
            <td align="left" style="width: 7.5%;"><b style="color:#CC0000">未支付</b></td>
            <td align="left" style="width: 5%;">原料</td>
            <td align="left" style="">原料名称</td>
        </tr>
      </thead>
      <tbody>
      <?php for($i=0; $i<sizeof($podata); $i++){ ?>
        <tr>
            <td><?php echo $podata[$i]['product_id']; ?></td>
            <td><?php echo $podata[$i]['name']; ?> [<?php echo $podata[$i]['product_unit']; ?>]</td>
            <td><?php echo $podata[$i]['category']; ?></td>
            <td><?php echo $podata[$i]['parent_category']; ?></td>
            <td><?php echo $podata[$i]['price']; ?></td>
            <td title="<?php echo $podata[$i]['product_unit']; ?>"><?php echo $podata[$i]['product_weight']; ?></td>
            <td><?php echo $podata[$i]['by_gram']; ?></td>
            <td><?php echo $podata[$i]['qty_total']; ?></td>
            <td><?php echo $podata[$i]['unpaid_qty_total']; ?></td>
            <td><?php echo $podata[$i]['sku_id']; ?></td>
            <td><?php echo $podata[$i]['sku_name']; ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
    <div style="text-align: right"><b><?php echo $deliver_date?></b>需配送商品采购订单(含微信支付<b style="color:#CC0000">且尚未支付</b>), 打印时间<?php echo date("Y-m-d H:i:s",time()+8*60*60);?></div>
    </div>

    <?php if( sizeof($podata_unpaid) ) {?>
    <div style="page-break-after: always; width:740px">
        <hr class="no_print" />
        <table class="table table-bordered">
            <caption style="font-size:16px; margin:10px 0;"><img src="view/image/logo.png" border="0"> <b><?php echo $deliver_date?></b>送货，选择微信支付<b style="color:#CC0000">且尚未支付</b>的商品</caption>
            <thead>
            <tr>
                <td align="left" style="width: 10%;">商品编号</td>
                <td align="left" style="">商品名称</td>
                <td align="left" style="width: 10%;">二级分类</td>
                <td align="left" style="width: 10%;">一级分类</td>
                <td align="left" style="width: 8%;">均售价</td>
                <td align="left" style="width: 7%;">规格</td>
                <td align="left" style="width: 6%;">合计</td>
                <td align="left" style="width: 10%;">采购数量</td>
            </tr>
            </thead>
            <tbody>
            <?php for($i=0; $i<sizeof($podata_unpaid); $i++){ ?>
            <tr>
                <td rowspan="2"><?php echo $podata_unpaid[$i]['product_id']; ?></td>
                <td rowspan="2"><?php echo $podata_unpaid[$i]['name']; ?></td>
                <td><?php echo $podata_unpaid[$i]['category']; ?></td>
                <td><?php echo $podata_unpaid[$i]['parent_category']; ?></td>
                <td><?php echo $podata_unpaid[$i]['price']; ?></td>
                <td><?php echo $podata_unpaid[$i]['product_unit']; ?></td>
                <td><?php echo $podata_unpaid[$i]['qty_total']; ?></td>
                <td><?php echo $podata_unpaid[$i]['purchase']; ?></td>
            </tr>
            <tr>
                <td colspan="6"><strong>相关未支付订单：
                        <?php
                         $unpaid_orders = explode(',',$podata_unpaid[$i]['unpaid_orders']);
                         foreach($unpaid_orders as $unpaid_order){
                            echo "[<a target='_blank' href='index.php?route=sale/order&token=".$token."&filter_order_id=".$unpaid_order."'>".$unpaid_order."</a>] ";
                         }
                         ?></strong>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <div style="text-align: right"><b><?php echo $deliver_date?></b>送货，选择微信支付<b style="color:#CC0000">且尚未支付</b>的商品, 打印时间<?php echo date("Y-m-d H:i:s",time()+8*60*60);?></div>
    </div>
    <?php }?>

</div>
</body>
</html>