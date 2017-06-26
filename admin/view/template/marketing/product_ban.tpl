<form method="post" enctype="multipart/form-data" id="form-product-bind" class="form-horizontal">
  <table id="product_ban_row" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
      <td class="text-left">商品号</td>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="height:100px;width:80%">
        <textarea id="products_ban_id" style="width:100%;height:100%"></textarea>
        <input id="coupon_id" name="coupon_id" type="hidden" value="<?php echo $coupon_id; ?>"/>
          <button type="button" class="btn btn-danger" onclick="banProduct();">添加排除</button></td>
      </td>
    </tr>
    <thead>
    <tr>
      <td class="text-left">优惠券排除的商品列表</td>
    </tr>
    </thead>
    <tr>
      <td><div id="product_ban_name"></div></td>
    </tr>
    </tbody>
  </table>
<form method="post" enctype="multipart/form-data" id="form-coupon-bind" class="form-horizontal">
<script type="text/javascript">
  function getExcludedProducts(){
    $("#product_ban_name").html('');

    $.ajax({
      type: 'GET',
      url: 'index.php?route=marketing/coupon/getExcludedProducts&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>',
      dataType: 'json',
      success: function(data){
          //console.log(data);
          var html = '';
          $.each(data,function(i,v){
              var productStatus = (v.status == '0') ? '［已下架］' : '';
              html += '<div><i class="fa fa-minus-circle" value="'+v.product_id+'" onclick="removeExcludedProduct('+v.coupon_id+','+v.product_id+');"></i> [' + v.product_id + ']'+v.product_name+productStatus+'</div>';
          });

          $("#product_ban_name").html(html);
      },
      error: function(){
        alert(global.returnErrorMsg);
      }
    });
  }
  getExcludedProducts();

  function removeExcludedProduct(coupon_id, product_id){
      if(confirm('确认恢复此商品吗（优惠券将适用于此商品）？')){
          $.ajax({
              type:'GET',
              url:'index.php?route=marketing/coupon/returnBanProduct&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&product_id='+product_id,
              dataType: 'json',
              success:function(data){
                  if(data){
                      $('#product_id_'+product_id).remove();
                  }
              },
              complete:function(){
                  getExcludedProducts();
              }
          });
      }
  }

  function banProduct() {
    var products = $('#products_ban_id').val();
    $('#products_ban_id').val('');
    var product_ids = styleChange(products);
    $.ajax({
      type:'POST',
      async: false,
      cache: false,
      url:'index.php?route=marketing/coupon/banproduct&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&product_ids='+product_ids,
      data:$('#form-product-bind').serialize(),
      success:function(data){
        var data = JSON.parse(data);
        if(data.status== 'true'){
          alert(data.message);
        }else if(data.status== 'null'){
          alert(data.message);
        }else {
          alert(data.message);
        }
      },
      error: function(){
        alert(global.returnErrorMsg);
      },
      complete:function(){
          getExcludedProducts();
      }
    });
  }
</script>