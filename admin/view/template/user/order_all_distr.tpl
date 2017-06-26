<div  class="table-responsive">
  <table class="table table-bordered">
    <thead>
    <tr>
      <th>订单号</th>
      <th>分拣量</th>
      <th>订单总量</th>
      <th>分拣类型</th>
      <th>下单时间</th>
      <th>会员等级</th>
      <th>地址</th>
      <th>分拣人</th>
      <th>重新分配</th>
    </tr>
    </thead>
    <tbody>
    <?php $i = 0; ?>
    <?php if ($histories) { ?>
    <?php foreach ($histories as $history) { ?>
    <tr id="distr_order<?php echo $history['flag_key']; ?>">
      <td> <?php echo $history['order_id']; ?></td>
      <td><?php echo $history['fj_quantity']; ?></td>
      <td><?php echo $history['quantity']; ?></td>
      <td><?php echo $history['product_name']; ?></td>
      <td><?php echo $history['date_added']; ?></td>
      <td><?php echo $history['customer_level']; ?></td>
      <td><?php echo $history['shipping_address']; ?></td>
      <td><?php echo $history['inventory_name'];?></td>
      <td>

        <button type="button" value="<?php echo $history['order_id'].';'.$history['product_type_id'].';'.$history['flag_key']; ?>" onclick="redistrOrderToWorker(this);">重分配</button>
      </td>

    </tr>
    <?php $i++; ?>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
    </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
<div class="row">
  <!-- <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div> -->
</div>
<script type="text/javascript">
  function redistrOrderToWorker(obj) {
    var data = obj.value.split(';');
    var order_id = data[0];
    var product_type_id = data[1];
    var flag_key = data[2];

    $.ajax({
      url: 'index.php?route=user/warehouse_distribution/redistrOrderToWoker&token=<?php echo $token; ?>',
      type: 'post',
      dataType: 'json',
      data: {
        order_id: order_id,
        product_type_id: product_type_id,
      },

      success: function (response) {
        if(response){
          //清除分配成功的tr
          $('#distr_order'+flag_key).remove();
        }
      },
    });
  }
</script>
<script type="text/javascript">
  $('#distr-table').delegate('.pagination a', 'click', function(e) {
    e.preventDefault();
    $('#distr-table').load(this.href);
  });
</script>