<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td class="text-right"><?php echo $column_order_id; ?></td>
        <td class="text-left"><?php echo $column_customer; ?></td>
        <td class="text-right"><?php echo $column_amount; ?></td>
        <td class="text-left"><?php echo $column_date_added; ?></td>
        <td class="text-left">状态</td>
      </tr>
    </thead>
    <tbody>
      <?php if ($histories) { ?>
      <?php foreach ($histories as $history) { ?>
      <tr>
        <td class="text-right"><a href='<?php echo $history["order_url"]; ?>' target="_link"><?php echo $history['order_id']; ?></a></td>
        <td class="text-left"><?php echo $history['customer']; ?></td>
        <td class="text-right"><?php echo $history['amount']; ?></td>
        <td class="text-left"><?php echo $history['date_added']; ?></td>
        <?php if($history['status'] == 1){ ?>
          <td class="text-left">有效</td>
        <?php } else{ ?>
          <td class="text-left" style="color: #DA0000">作废</td>
        <?php } ?>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
