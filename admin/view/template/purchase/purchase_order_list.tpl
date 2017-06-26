<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td class="text-right">采购单号</td>
        <td class="text-left">供应商</td>
        <td class="text-right">下单人</td>
      </tr>
    </thead>
    <tbody>
      <?php if ($orderLists) { ?>
      <?php foreach ($orderLists as $history) { ?>
      <tr>
        <td class="text-right" onclick="setOrderID(<?php echo $history['purchase_order_id']; ?>);"><?php echo $history['purchase_order_id']; ?></a></td>
        <td class="text-left"><?php echo $history['name']; ?></td>
        <td class="text-right"><?php echo $history['username']; ?></td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td class="text-center" colspan="3">没有结果</td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
