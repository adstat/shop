<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td class="text-right">商家编号</td>
        <td class="text-left">商家名称</td>
        <td class="text-right">商家联系电话</td>
        <td class="text-right">所属BD</td>
        <td class="text-left">报名日期</td>
      </tr>
    </thead>
    <tbody>
      <?php if ($customers) { ?>
      <?php foreach ($customers as $customer) { ?>
      <tr>
        <td class="text-right"><?php echo $customer['customer_id']; ?></td>
        <td class="text-left"><?php echo $customer['name']; ?></a></td>
        <td class="text-right"><?php echo $customer['telephone']; ?></td>
        <td class="text-left"><?php echo $customer['bd_name']; ?></td>
        <td class="text-left"><?php echo $customer['date_added']; ?></td>
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
