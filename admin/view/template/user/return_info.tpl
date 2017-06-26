<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_little; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
    <div class="container-fluid">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_little . ' #'.$product_id; ?></h3>
        </div>
        <div class="panel-body">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-product" data-toggle="tab">本次退货商品详情</a></li>
            <li><a href="#tab-order" data-toggle="tab"><?php echo "订单".$order_id."的退货列表";?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-product">
              <table class="table table-bordered">
                <tr>
                  <td>订单ID</td>
                  <td><?php echo ' #'.$order_id; ?></td>
                </tr>
                <tr>
                  <td>商品ID</td>
                  <td><?php echo ' #'.$product_id; ?></td>
                </tr>
                <tr>
                  <td>退货会员</td>
                  <td><?php echo $productInfo['customername']; ?></td>
                </tr>
                <tr>
                  <td>退货原因</td>
                  <td><?php echo $productInfo['reason']; ?></td>
                </tr>
                <tr>
                  <td>退货数量</td>
                  <td><?php echo $productInfo['quantity']; ?></td>
                </tr>
                <tr>
                  <td>退货价格</td>
                  <td><?php echo $productInfo['price']; ?></td>
                </tr>
                <tr>
                  <td>是否退货入库</td>
                  <td><?php if($productInfo['return_inventory_flag']){ ?>
                    <?php echo "退货入库"; ?>
                    <?php }else{ ?>
                    <?php echo "不入库"; ?>
                    <?php } ?></td>
                </tr>
                <tr>
                  <td>退货经办物流人员</td>
                  <td><?php echo $productInfo['logisticname']; ?></td>
                </tr>
                <tr>
                  <td>退货登记日期</td>
                  <td><?php echo $productInfo['date_added']; ?></td>
                </tr>
              </table>
            </div>
            <div class="tab-pane" id="tab-order">
              <div id="order"></div>
            </div>

          </div>
        </div>

      </div>
    </div><!-- fluid -->



  </div>
<script type="text/javascript">
  $('#order').delegate('.pagination a', 'click', function(e) {
    e.preventDefault();

    $('#order').load(this.href);
  });
  $('#order').load('index.php?route=user/return_product/orderReturnList&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>');

</script>
</div>

<?php echo $footer; ?>