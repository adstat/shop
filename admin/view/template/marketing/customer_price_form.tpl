<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-activity" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($success) { ?>
      <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> 编辑</h3>
      </div>
      <div class="panel-body">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-data" data-toggle="tab">指定用户商品</a></li>
        </ul>
        <div class="content">
          <div class="tab-pane active" id="tab-data">
            <div class="table-responsive">
              <form action="<?php echo $action_edit; ?>" method="post" enctype="multipart/form-data" id="form-product-adjust" class="form-horizontal">
                <table  id="product_link" class="table table-striped table-bordered table-hover">
                  <thead>
                  <tr>
                    <td class="center" style="width: 10%">商品ID</td>
                    <td class="center" style="width: 30%">商品名称</td>
                    <td class="center" style="width: 10%">商品价格</td>
                    <td class="center" style="width: 10%">指定用户价格</td>
                  </tr>
                  </thead>
                  <tbody>
                  <!-- 关联商品 -->
                  <?php $link_row = 0; ?>
                  <?php if (sizeof($product_links) > 0) { ?>
                  <?php foreach ($product_links as $product_link) { ?>
                  <tr id="link-row<?php echo $link_row; ?>">
                    <td class="text-center" style="width: 10%"><input type="text" name="product_link[<?php echo $link_row; ?>][product_id]" value="<?php echo $product_link['product_id']; ?>" placeholder="商品id" class="form-control" onChange="getProductInfo($(this).val(), $(this));" /></td>
                    <td class="text-center" style="width: 30%"><div class="rowName"><?php echo $product_link['name']; ?></div></td>
                    <td class="text-center">
                      <input type="text" name="product_link[<?php echo $link_row; ?>][price]" value="<?php echo $product_link['price']; ?>" placeholder="价格" class="form-control rowPrice" readonly/>
                    </td>
                    <td class="text-center" style="width: 10%">
                      <input type="text" name="product_link[<?php echo $link_row; ?>][customer_price]" value="<?php echo $product_link['customer_price']; ?>" placeholder="指定价格" class="form-control" />
                    </td>
                    <td class="text-left"><button type="button" onclick="$('#link-row<?php echo $link_row; ?>').remove();" data-toggle="tooltip" title="移除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                  </tr>
                  <?php $link_row++; ?>
                  <?php } ?>
                  <?php } ?>
                  </tbody>
                  <tfoot>
                  <tr>
                    <td colspan="7"></td>
                    <td class="text-left"><button type="button" onclick="addLinks();" data-toggle="tooltip" title="添加" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                  </tr>
                  </tfoot>
                </table>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    var link_row = <?php echo $link_row; ?>;

    function addLinks() {
      html  = '<tr id="link-row' + link_row + '">';
      html += '<td class="text-center" style="width: 10%"><input type="text" name="product_link[' + link_row + '][product_id]" value="" placeholder="商品id" class="form-control" onChange="getProductInfo($(this).val(), $(this));" /></td>';
      html += '<td class="text-center" style="width: 30%"><div class="rowName"></div></td>';
      html += '<td class="text-center" style="width: 10%"><input type="text" name="product_link[' + link_row + '][price]" value="" placeholder="价格" class="form-control rowPrice" readonly/></td>';
      html += '<td class="text-center" style="width: 10%"><input type="text" name="product_link[' + link_row + '][customer_price]" value="" placeholder="指定价格" class="form-control" /></td>';
      html += '<td class="text-left"><button type="button" onclick="$(\'#link-row' + link_row + '\').remove();" data-toggle="tooltip" title="移除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
      html += '</tr>';

      $('#product_link tbody').append(html);

      link_row++;
    }

    function getProductInfo(product_id,obj) {
      var rowId = obj.parent().parent().attr('id');
      var product_id = parseInt(product_id);

      $.ajax({
        type:'GET',
        async: false,
        cache: false,
        url: 'index.php?route=catalog/activity/getProductInfo&token=<?php echo $_SESSION['token']; ?>',
        data: {
          product_id: product_id
        },
        dataType: 'json',
        success:function(response){
          console.log(response);
          if(response.name){
            $('#'+rowId+' .rowName').html(response.name);
            $('#'+rowId+' .rowPrice').val(response.price);
          }else{
            $('#'+rowId+' .rowName').html('');
            $('#'+rowId+' .rowPrice').html('');

            alert('该商品id无对应商品');
          }
        }
      });
    }
  </script>
</div>
<?php echo $footer; ?>