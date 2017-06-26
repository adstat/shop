<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i>编辑商品</h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-barcode" data-toggle="tab">条码管理</a></li>
            <li><a href="#tab-history" data-toggle="tab" onclick="history();">商品编码历史</a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane active" id="tab-barcode">
              <div class="form-group">
              <?php foreach ($languages as $language) { ?>
              <label class="col-sm-2 control-label">商品名称</label>
              <div class="col-sm-10">
                <h2><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['name'] : ''; ?></h2>
              </div>
              <?php } ?>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-barcode">新增商品编码</label>
                <div class="col-sm-4">
                  <input type="text" name="sku_barcode" value="" placeholder="新增编码" id="input-barcode" class="form-control" />
                  <?php if (isset($error_length) && $error_length) { ?>
                  <div class="text-danger"><?php echo $error_length; ?></div>
                  <?php } ?>
                  <?php if (isset($error_string) && $error_string) { ?>
                  <div class="text-danger"><?php echo $error_string; ?></div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-box">是否为外箱编码</label>
                <div class="col-sm-4">
                  <select name="box" id="input-box" class="form-control">
                    <option value="*">全部</option>
                    <option value="0">是</option>
                    <option value="1">否</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-warehouse">是否指定仓库唯一</label>
                <div class="col-sm-4">
                  <select name="warehouse" id="input-warehouse" class="form-control">
                    <option value="*">全部</option>
                    <?php foreach($warehouses as $warehouse){ ?>
                    <option value="<?php echo $warehouse['warehouse_id']; ?>"><?php echo $warehouse['title']; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

            </div>

            <div class="tab-pane" id="tab-history">
              <div id="history"></div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript">
  function history(){
    var warehouses = <?php echo json_encode($warehouses,true); ?>;
    $('#history').load('index.php?route=catalog/product_skubarcode/history&token=<?php echo $token; ?>&product_id=<?php echo $product_id; ?>',{warehouses:warehouses});
  }
</script>
</div>
<?php echo $footer; ?> 
