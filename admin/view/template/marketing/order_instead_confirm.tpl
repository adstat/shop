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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3><i class="fa fa-pencil"></i>导入订单信息</h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <form action="<?php echo $action_confirm; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
            <input type="hidden" name="uploaded_file" value="<?php echo $uploaded_file; ?>">
            <input type="hidden" name="upload_flag" value="<?php echo $flag; ?>">
          </form>
          <table class="table table-bordered table-hover">
            <thead>
            <tr>
              <td class="text-center">序号</td>
              <td class="text-center">商家ID</td>
              <td class="text-center">门店名称</td>
              <td class="text-center">商家名称</td>
              <td class="text-center">商家地址</td>
              <td class="text-center">电话</td>
              <td class="text-center">系统商品ID</td>
              <td class="text-center">商品名称</td>
              <td class="text-center">系统名称</td>
              <td class="text-center">数量</td>
              <td class="text-center">价格</td>
              <td class="text-center">合计</td>
            </tr>
            <tbody>
              <?php $i=1; foreach($orders_products as $product){ ?>
              <tr>
                <?php
                $nameCompColor = "#ffffaa" ;
                if($product['product_name'] !== $product['sys_name'] && $product['sys_name'] !='无此商品'){
                    $nameCompColor = "#f89406" ;
                }elseif($product['sys_name'] =='无此商品'){
                    $nameCompColor = "#ff0000" ;
                }else{
                    $nameCompColor = "#ffffaa" ;
                }
              ?>
                <?php
               $nameCompColor = "#ffffaa" ;
                if(!$product['product_id']){
                  $nameCompColor = "#ff0000" ;
                }
              ?>
                <td class="text-center"><?php echo $i;  ?></td>
                <td class="text-center"><?php echo $product['customer_id'];  ?></td>
                <td class="text-center"><?php echo $product['merchant_name'];  ?></td>
                <td class="text-center"><?php echo $product['customer_name'];  ?></td>
                <td class="text-center"><?php echo $product['merchant_address'];  ?></td>
                <td class="text-center"><?php echo $product['telephone'];  ?></td>
                <td class="text-center"  style="background-color:<?php echo $nameCompColor; ?>;"><?php echo $product['product_id'];  ?></td>
                <td class="text-center"><?php echo $product['product_name'];  ?></td>
                <td class="text-center"  style="background-color:<?php echo $nameCompColor; ?>;"><?php echo $product['sys_name'];  ?></td>
                <td class="text-center"><?php echo $product['price'];  ?></td>
                <td class="text-center"><?php echo $product['quantity'];  ?></td>
                <td class="text-center"><?php echo $product['line_sum'];  ?></td>
              </tr>
              <?php $i++; } ?>
              <tr>
                <td class="text-center" colspan="11"><button type="button" class="btn btn-primary" onclick="submitForm();">确认提交</button></td>
              </tr>
            </tbody>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script>
    function submitForm(){
      //确定后台传的flag为true,否则不予提交
      if($('input[name=\"upload_flag\"]').val()){
        if(window.confirm('确认导入该excel中的订单？')){
        $('#form-product').submit();
        }
      }else{
         alert('excel信息未能完全填写正确匹配,请修改正确!');
      }

    }
  </script>
</div>
<?php echo $footer; ?>
