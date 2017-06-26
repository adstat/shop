<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary" ><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>编辑提交修改商品价格的内容</h3>
      </div>
      <div class="panel-body">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-data" data-toggle="tab">添加商品价格</a></li>
          <!-- <li><a href="#tab-history" data-toggle="tab">申请历史</a></li> -->
        </ul>
        <div class="tab-content">
          <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i>
          采购人员如果有修改商品售价的需要，在此添加商品新销售价格，审核之后生效
          </div>
          <div class="tab-pane active" id="tab-data">
            <div class="col-sm-12">
              <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-activity" class="form-horizontal">
                <div class="table-responsive">
                  <table id="products" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                      <td>商品ID</td>
                      <td style="width:30%">商品名称</td>
                      <td>当前售价</td>
                      <td>对应原料价格</td>
                      <td>采购价格</td>
                      <td>提交新售价</td>
                      <td>操作</td>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <td colspan="6"></td>
                      <td><button type="button" onclick="addLinks();" data-toggle="tooltip" title="添加" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tfoot>
                  </table>
                </div>
              </form>
            </div>
          </div>

          <div class="tab-pane" id="tab-history">
          </div>

        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
  var link_row = 0;

  function addLinks(){
    html  = '<tr id="link-row' + link_row + '">';
    html += '<td class="text-center"><input type="text"  name="product[' + link_row + '][product_id]" value="" onchange="getProductInfo($(this).val(),$(this));" placeholder="商品ID" class="form-control" /></td>';
    html += '<td class="text-center" style="width:30%"><input type="text" id="name'+link_row+'" name="product[' + link_row + '][name]" value="" placeholder="商品名称" class="form-control name" readonly/></td>';
    html += '<td class="text-center"><input type="text" id="price'+link_row+'" name="product[' + link_row + '][price]" value="" placeholder="当前售价" class="form-control price" readonly/></td>';
    html += '<td class="text-center"><input type="text" id="s_price'+link_row+'" name="product[' + link_row + '][s_price]" value="" placeholder="原料价格" class="form-control s_price" readonly/></td>';
    html += '<td class="text-center"><input type="text" id="p_price'+link_row+'" name="product[' + link_row + '][p_price]" value="" placeholder="计算成本" class="form-control p_price" readonly/></td>';
    html += '<td class="text-center"><input type="text"  name="product[' + link_row + '][e_price]" value="" placeholder="申请售价" class="form-control" /></td>';
    html += '<td class="text-left"><button type="button" onclick="$(\'#link-row' + link_row + '\').remove();" data-toggle="tooltip" title="移除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';

    $('#products tbody').append(html);

    link_row++;
  }

  addLinks();

  function getProductInfo(product_id,obj){
    var product_id = parseInt(product_id);
    var rowId = obj.parent().parent().attr('id');
    var line = parseInt(rowId);
    console.log(obj);
    $.ajax({
      type: 'POST',
      url: 'index.php?route=catalog/product_mannage/getProductInfo&token=<?php echo $_SESSION["token"]; ?>',
      dataType: 'json',
      data:{
        product_id:product_id,
      },
      success:function(response){
        console.log(response);
        if(response.status == 0){
          alert('请输入存在的商品ID！');
        }else{

          $('#'+rowId+' .name').val(response.info.name);
          $('#'+rowId+' .price').val(response.info.price);
          $('#'+rowId+' .s_price').val(response.info.s_price);
          $('#'+rowId+' .p_price').val(response.info.p_price);
        }
      },
    });
  }
</script>
</div>
<?php echo $footer; ?>