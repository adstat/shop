<?php echo $header; ?><?php echo $column_left; ?>
<!-- 页面 -->
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
    <?php if (isset($error_warning) && $error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i>库存管理</h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
            <form action="<?php echo $action_confirm; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
                <input type="hidden" name="uploaded_file" value="<?php echo $uploaded_file; ?>">
                <input type="hidden" name="purchase_date" value="<?php echo $purchase_date; ?>">
            </form>
            <table class="table table-bordered table-hover">
                <caption style="font-style: italic; font-size: 1.2em">正准备导入<span style="font-size: 1.3em; font-weight: bold; color: #FF3300"><?php echo $purchase_date; ?></span>库存数据，已有日期数据将被覆盖</caption>
                <thead>
                    <tr>
                        <td class="text-center">序号</td>
                        <td class="text-center">原料编号</td>
                        <td class="text-center">原料名称</td>
                        <td class="text-center" style="background-color: #f0ad4e;">系统原料名称</td>
                        <td class="text-center" style="color: #CC0000">原库存保存程度</td>
                        <td class="text-center">采购前的库存量</td>
                        <td class="text-center">采购后的库存量</td>
                        <td class="text-center">库存合计</td>
                        <td class="text-center">采购员</td>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1; foreach($products as $product){ ?>
                    <tr>
                        <?php
                            $nameCompColor = "#ffffaa" ;
                            if($product['name'] !== $product['sys_name']){
                                $nameCompColor = "#f89406" ;
                        }
                        ?>
                        <td class="text-center"><?php echo $i; ?></td>
                        <td class="text-center"><?php echo $product['sku_id']; ?></td>
                        <td class="text-center"><?php echo $product['name']; ?></td>
                        <td class="text-center" style="background-color:<?php echo $nameCompColor; ?>;"><?php echo $product['sys_name']; ?></td>
                        <td class="text-center"><?php echo $product['purchase_price_500g_gross']; ?></td>
                        <td class="text-center"><?php echo $product['purchase_price_500g']; ?></td>
                        <td class="text-center"><?php echo $product['purchase_qty_500g']; ?></td>
                        <td class="text-center"><?php echo $product['purchase_total']; ?></td>
                        <td class="text-center"><?php echo $product['buyer']; ?></td>
                    </tr>
                    <?php $i++; } ?>
                    <tr>
                        <td class="text-right" colspan="8">
                            <div style="font-style: italic; font-size: 1.2em">正准备导入<span style="font-size: 1.3em; font-weight: bold; color: #FF3300"><?php echo $purchase_date; ?></span>库存数据，已有日期数据将被覆盖</div>
                            <button type="button" class="btn btn-primary" onclick="submitForm();">确认提交</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
    <script type="text/javascript">
        function submitForm(){
            if(window.confirm('确认提交此次采购数据吗？')){
                $('#form-product').submit();
            }
        }
    </script>
<?php echo $footer; ?>