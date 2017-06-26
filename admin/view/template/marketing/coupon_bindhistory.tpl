<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td class="text-right">会员ID</td>
        <td class="text-left">会员</td>
        <td class="text-right">优惠券名称</td>
        <td class="text-right">开始日期</td>
        <td class="text-left">结束日期</td>
        <td class="text-left">次数</td>
      </tr>
    </thead>
    <tbody>
      <?php if ($bindhistory) { ?>
      <?php foreach ($bindhistory as $history) { ?>
      <tr>
        <td class="text-right"><a href='<?php echo $history["customer_url"]; ?>' target="_link"><?php echo $history['customer_id']; ?></a></td>
        <td class="text-left"><?php echo $history['customer']; ?></td>
        <td class="text-right"><?php echo $history['name']; ?></a></td>
        <td class="text-right"><?php echo $history['date_start']; ?></td>
        <td class="text-left"><?php echo $history['date_end']; ?></td>
        <td class="text-left"><?php echo $history['times']; ?></td>
      </tr>
      <?php } ?>
      <?php } ?>
    </tbody>
  </table>
</div>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
