<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td>商品ID</td>
        <td>商品名称</td>
        <td>修改前价格</td>
        <td>申请价格</td>
        <td>申请人</td>
        <td>申请时间</td>
        <td>作废</td>
      </tr>
    </thead>
    <tbody>
    <?php if($histories){ ?>
    <?php foreach ($histories as $history) { ?>
      <tr id="<?php echo $history['price_edit_id']; ?>">
        <td><?php echo $history['product_id'];?></td>
        <td><?php echo $history['name'];?></td>
        <td><?php echo $history['price'];?></td>
        <td><?php echo $history['edit_price'];?></td>
        <td><?php echo $history['add_user'];?></td>
        <td><?php echo $history['date_added'];?></td>
        <td align="center"><button type="button" class="btn btn-primary" value="<?php echo $history['price_edit_id']; ?>" onclick="rollback($(this).val());">取消审核</button></td>
      </tr>
    <?php } ?>
    <?php }else{ ?>
    <tr>
      <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
    </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
