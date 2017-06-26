
<button type="button" class="btn btn-primary" style="margin-bottom: 5px; float: right" onclick="productbind();">添加</button>

<form method="post" enctype="multipart/form-data" id="form-product-bind" class="form-horizontal">
  <table id="product_add_row" style="display: none" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
      <td class="text-left">商品号</td>
      <td class="text-center">操作</td>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td style="height:100px;width:80%">
        <textarea id="products_id" style="width:100%;height:100%"></textarea>
        <input id="coupon_id" name="coupon_id" type="hidden" value="<?php echo $coupon_id; ?>"/>
        <!--<input id="status" name="status" type="hidden" value="<?php echo $status; ?>"/>-->
      </td>
      <td class="text-center" size="20"><button type="button" class="btn btn-danger" onclick="checkProduct();">检查商品</button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-danger" onclick="addProduct();">保存</button></td>
    </tr>
    <thead>
    <tr colspan="2">
      <td class="text-left">合格的商品名称</td>
    </tr>
    </thead>
    <tr>
      <td><div id="product_name"></div></td>
    </tr>
    </tbody>
  </table>
<form method="post" enctype="multipart/form-data" id="form-coupon-bind" class="form-horizontal">
<script type="text/javascript">
  function productbind(){
    $('#product_add_row').show();
  }
  function checkProduct(){
    var products = $('#products_id').val();
    var products = styleChange(products);
    $.ajax({
      type: 'GET',
      url: 'index.php?route=marketing/coupon/checkProducts&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&product_ids=' + products,
      dataType: 'json',
      success: function(data){
        if(!isEmptyObject(data['no'])){
          alert(data['no']+"这些商品不符合要求");
        }
        $('#products_id').val(data['yes']);
        $('#coupon_customerids').val(data['yes']);
        $('#product_name').html(data['workname'].join('<br>'));
      },
      error: function(){
        alert(global.returnErrorMsg);
      }
    });
  }
  function addProduct() {
    var products = $('#products_id').val();
    $('#products_id').val('');
    var product_ids = styleChange(products);
    $.ajax({
      type:'POST',
      async: false,
      cache: false,
      url:'index.php?route=marketing/coupon/addproduct&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&product_ids='+product_ids,
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
      }
    });
  }
</script>