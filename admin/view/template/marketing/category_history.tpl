<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
    <tr>
      <td class="text-right">品类ID</td>
      <td class="text-left">品类名称</td>
      <td class="text-left">查看排除的商品</td>
    </tr>
    </thead>
    <tbody>
    <?php if ($histories) { ?>
    <?php foreach ($histories as $history) { ?>
    <tr>
      <td class="text-right" id="category_id_+<?php echo $history['category_id']; ?>"><?php echo $history['category_id']; ?></td>
      <td class="text-left"><?php echo $history['name']; ?></td>
      <td class="text-left">
        <?php if($history['count']){  ?>
          <button value="<?php echo $history['category_id']; ?>" onclick="showProductsBan(this); return false;">查看</button>
          <?php echo $history['count']."件商品被禁用"; ?>
        <?php }else{ ?>
          <?php echo "零件商品被禁用"; ?>
        <?php } ?>

      </td>
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

<div id="productsBan" class="table-responsive" style="overflow: auto;display:none;">
  <table class="table table-bordered table-hover">
    <caption>已排除的商品</caption>
    <thead>
    <tr>
      <td class="text-right">品类ID</td>
      <td class="text_left">品类名称</td>
      <td class="text-right">商品ID</td>
      <td class="text-left">商品名称</td>
    </tr>
    </thead>
    <tbody>
    <?php if ($banhistories) { ?>
    <?php foreach ($banhistories as $history) { ?>
    <tr id="product_id_<?php echo $history['product_id']; ?>" class="banproductlist category_id_<?php echo $history['category_id']; ?>" style="display:none;">
      <td class="text-right"><?php echo $history['category_id']; ?></td>
      <td class="text_left"><?php echo $history['category_name']; ?></td>
      <td class="text-right"><?php echo $history['product_id']; ?></td>
      <td class="text-left"><?php echo $history['product_name']; ?><i class="fa fa-minus-circle" value="<?php echo $history['product_id']; ?>" onclick="returnProduct(this);"></i></td>
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

<script type="text/javascript">
  function showProductsBan(obj){
    var category_id = obj.getAttribute('value');
    $('#productsBan').show();

    $('.banproductlist').hide();
    $('.category_id_'+category_id).show();
  }
  function returnProduct(obj){

    if(confirm('确认恢复此商品吗（优惠券将适用于此商品）？')){
        var product_id = obj.getAttribute('value');
        $.ajax({
            type:'GET',
            url:'index.php?route=marketing/coupon/returnBanProduct&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&product_id='+product_id,
            dataType: 'json',
            success:function(data){
                if(data){
                    $('#product_id_'+product_id).remove();
                }
            }
        });
    }

  }
</script>

