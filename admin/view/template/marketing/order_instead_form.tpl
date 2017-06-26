<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <?php if ($success) { ?>
      <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      <?php } ?>
        <?php if ($false) { ?>
        <div class="alert alert-danger"><i class="fa fa-check-circle"></i> <?php echo $false; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($order_false) { ?>
        <div class="alert alert-danger"><i class="fa fa-check-circle"></i> <?php echo $order_false; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($product_false) { ?>
        <div class="alert alert-dager"><i class="fa fa-check-circle"></i> <?php echo $product; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3><i class="fa fa-pencil"></i><?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <ul class="nav nav-tabs">
          <li style="display: none"><a href="#tab-general" data-toggle="tab">代下单(停用)</a></li>
          <li class="active"><a href="#tab-excel" data-toggle="tab">多单导入(仅限闪电购生鲜订单)</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane" id="tab-general">
              <div class="table-responsive">
                  <!--<style>
                      #order-main div{
                          text-align:center;
                          margin: 3px;
                          padding: 5px;
                          font-size: 16px;
                          border: 1px dashed #6c6c6c;
                      }
                  </style>-->

                  <form action="<?php echo $action_insert; ?>" method="post" enctype="multipart/form-data" id="form-order-insert" class="form-horizontal">
                      <div class="form-group col-md-8 col-sm-offset-2 text-center" id="order-main">
                          <div class="col-sm-3">
                              <label class="control-label" for="input-customer-id">用户</label>
                              <div>
                              <input name="customer_id" id="input-customer-id" class="form-control" value="" placeholder="用户ID" onchange="getCustomerInfo(this);" />
                              <?php if ($error_order) { ?>
                              <span class="text-danger"><?php echo $error_order; ?></span>
                              <?php } ?>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <label class="control-label" for="input-shipping-address">地址</label>
                              <input name="shipping_address_1" id="input-shipping-address" class="form-control" value="" placeholder="收货地址" />
                          </div>
                          <div class="col-sm-3">
                              <label class="control-label" for="input-shipping-phone">联系电话</label>
                              <input name="shipping_phone" id="input-shipping-phone" class="form-control" value="" placeholder="收货电话" />
                          </div>
                          <div class="col-sm-3">
                              <label class="control-label">选择仓库</label>
                              <select name="station_id" id="input_station_id" class="form-control">
                                  <option value='0'>全部</option>
                                  <?php foreach ($stations as $val) { ?>
                                  <option value="<?php echo $val['station_id']; ?>" ><?php echo $val['name']; ?></option>
                                  <?php } ?>
                              </select>
                          </div>
                      </div>
                      <table id="products" class="table table-striped table-bordered table-hover">
                          <thead>
                          <tr>
                              <td class="text-center" width="10%">商品ID</td>
                              <td class="text-center" width="40%">商品名称</td>
                              <td class="text-center" width="10%">价格</td>
                              <td class="text-center" width="10%">数量</td>
                              <td class="text-center" width="10%">小计</td>
                          </tr>
                          </thead>
                          <tbody>
                          <!-- 添加商品可售库存 -->
                          </tbody>
                          <tfoot>
                          <tr>
                              <td colspan="3"></td>
                              <td><input type="text" id="quantityTotal" name="quantityTotal" value="" placeholder="数量总计" class="form-control" /></td>
                              <td><input type="text" id="sub_total" name="priceTotal" value="" placeholder="价格总计" class="form-control" readonly /></td>
                              <td class="text-left"><button type="button" onclick="addProducts();" data-toggle="tooltip" title="添加" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                          </tr>
                          <tr>
                              <td colspan="5" class="text-center"><button type="button" class="btn btn-primary" onclick="submitOrder();">确认下单</button></td>
                          </tr>
                          </tfoot>
                      </table>
                  </form>
              </div>
          </div>
          <div class="tab-pane active" id="tab-excel">
              <form action="<?php echo $action_order; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
                  <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-upload-xls">上传Excel文件 (<span style="color: #CC0000" title="必须按照多单导入模板格式导入。">参考模板</span>)</label>
                      <div class="col-sm-10">
                          <input type="file" name="file" id="input-upload-xls" class="form-control">
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="col-sm-2 control-label">务必严格按照模板格式</label>
                      <div class="col-sm-10">
                          <button type="submit" class="btn btn-primary">上传</button>
                      </div>
                  </div>
              </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<script>
    var product_row = 0
    function addProducts(){
        html  = '<tr id="product-row' + product_row + '">';
        html += '<td class="text-center" width="10%"><input type="text" name="products[' + product_row + '][product_id]" value="" placeholder="商品ID" class="form-control" onChange="getProductInfo($(this).val(), $(this));" /></td>';
        html += '<td class="text-center" width="40%"><input type="text" name="products[' + product_row + '][name]" value="" placeholder="商品名称" class="form-control product_name" /></td>';
        html += '<td class="text-center" width="10%"><input type="text" name="products[' + product_row + '][price]" value="" placeholder="商品价格" id="price_'+product_row+'" class="form-control price" onChange="getProductTotal('+product_row+');" /></td>';
        html += '<td class="text-center" width="10%"><input type="text" sort="line_total" name="products[' + product_row + '][quantity]" value="" placeholder="商品数量" id="quantity_'+product_row+'" class="form-control" onChange="getProductTotal('+product_row+');" /></td>';
        html += '<td class="text-center" width="10%"><input type="text" order="line_total" name="products[' + product_row + '][total]" value="" placeholder="商品小计" id="total_'+product_row+'" class="form-control total" readonly /></td>';
        html += '<td class="text-left"><button type="button" onclick="lineRemove('+product_row+');" data-toggle="tooltip" title="删除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';
    $('#products tbody').append(html);
    product_row++;
    }

    function getCustomerInfo(){
        var customer_id = $('#input-customer-id').val();
        $.ajax({
            type: 'GET',
            url: 'index.php?route=marketing/order_instead/order_customer&token=<?php echo $_SESSION['token']; ?>&customer_id='+customer_id,
            dataType: 'json',
            success : function(data){
                if(data.length){
                    $('#input-shipping-address').val(data[0]['merchant_address']);
                    $('#input-shipping-phone').val(data[0]['telephone']);
                }else{
                    alert('无此用户!');
                }
            }
        });
    }

    function getProductInfo(product_id,obj){
        var rowId = obj.parent().parent().attr('id');
        var customer_id = $('#input-customer-id').val();
        var product_id = parseInt(product_id);
        $.ajax({
            type: 'POST',
            async: false,
            cache: false,
            url: 'index.php?route=marketing/order_instead/order_product&token=<?php echo $_SESSION['token']; ?>',
            data:{
                product_id:product_id,
                customer_id:customer_id,
            },
            dataType: 'json',
            success : function(data){
                if(data.length>0){
                    $('#'+rowId+' .product_name').val(data[0]['name']);
                    $('#'+rowId+' .price').val(data[0]['price']);
                }else{
                    alert('无此商品或该商品已停用!');
                }
            }
        });
    }

    function getProductTotal(line){
        var total = parseFloat($('#price_'+line).val()).toFixed(2)*parseFloat($('#quantity_'+line).val()).toFixed(2);
        $('#total_'+line).val(total.toFixed(2));

        getTotal();
    }

    function getTotal() {
        var sub_total = 0.00;
        $('input[order=\'line_total\']').each(function (i,v) {
            sub_total += parseFloat($(v).val());
        });
        sub_total = sub_total.toFixed(2);
        $('#sub_total').val(sub_total);

        var quantity = 0;
        $('input[sort=\'line_total\']').each(function (i,v) {
            quantity += parseInt($(v).val());
        });
        $('#quantityTotal').val(quantity);
    }

    function lineRemove(line){
        $('#product-row' + line ).remove();
        //计算当前总价
        getTotal();
    }

    function submitOrder(){
        var trs = $('#products tbody tr').length;
        if(trs < 1){
            alert('请先添加.');
            return false;
        }

        if(window.confirm('确认代下单？')){
            $('#form-order-insert').submit();
        }
    }

</script>
</div>
<?php echo $footer; ?>
