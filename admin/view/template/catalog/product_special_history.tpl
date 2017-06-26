<div class="table-responsive">
  <div class="alert alert-warning">商品特价设置记录按照特价生成时间倒叙排列，最近一次信息置顶</div>
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td class="text-right">置顶标题</td>
        <td class="text-left">限量</td>
        <td class="text-right">价格</td>
        <td class="text-right">开始日期</td>
        <td class="text-left">结束日期</td>
        <td class="text-left">操作人</td>
      </tr>
    </thead>
    <tbody>
      <?php if ($specialhistory) { ?>
      <?php foreach ($specialhistory as $history) { ?>
      <tr>
        <td class="text-right"><?php echo $history['promo_title']; ?></td>
        <td class="text-left"><?php echo $history['maximum']; ?></td>
        <td class="text-right"><?php echo $history['price']; ?></a></td>
        <td class="text-right"><?php echo $history['date_start']; ?></td>
        <td class="text-left"><?php echo $history['date_end']; ?></td>
        <td class="text-left"><?php echo $history['username']; ?></td>
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
