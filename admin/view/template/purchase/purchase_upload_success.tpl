<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
      <div class="alert alert-success"><i class="fa fa-check-circle"></i> 上传成功!
          <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i>库存管理</h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
            <form action="<?php echo $action_confirm; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
                <input type="hidden" name="uploaded_file" value="<?php echo $uploaded_file; ?>">
            </form>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <td class="text-center">序号</td>
                        <td class="text-center">商品ID</td>
                        <td class="text-center">商品名称</td>
                        <td class="text-center">重置后的库存</td>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1; foreach($list as $product){ ?>
                    <tr>
                        <td class="text-center"><?php echo $i; ?></td>
                        <td class="text-center"><?php echo $product['product_id']; ?></td>
                        <td class="text-center"><?php echo $product['name']; ?></td>
                        <td class="text-center"><?php echo $product['quantity']; ?></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
<?php echo $footer; ?> 