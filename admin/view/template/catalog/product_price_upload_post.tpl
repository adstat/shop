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
    <?php if (isset($error_warning) && $error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i>商品价格维护</h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
            <form action="<?php echo $action_confirm; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
                <input type="hidden" name="uploaded_file" value="<?php echo $uploaded_file; ?>">
            </form>

            <?php if(sizeof($repeatIds)) {
                foreach($repeatIds as $key=>$val){
                    echo "重复商品:[".$key."][".$sysInfo[$key]['name']."], 重复[".$val."]次<br />";
                }
            }
            else{
            ?>
                <table class="table table-bordered table-hover">
                    <caption style="font-style: italic; font-size: 1.1em">正准备导入商品价格数据，当日已上传同商品编号数据将被覆盖
                        <br />
                        [新分销价=新零售价*0.8, 1位小数], <span style="background-color: #fadb4e">颜色高亮</span>表示商名称或原零售价与当前系统不符。</caption>
                    <thead>
                        <tr>
                            <td class="text-center">编号</td>
                            <td class="text-center">商品编号</td>
                            <td class="text-center">商品名称</td>
                            <td class="text-center">系统商品名称</td>
                            <td class="text-center">系统当前零售价</td>
                            <td class="text-center">原零售价</td>
                            <td class="text-center">新零售价</td>
                            <td class="text-center">价差</td>
                            <td class="text-center">价差率</td>
                            <td class="text-center">系统当前分销价</td>
                            <td class="text-center">新分销价</td>
                            <td class="text-center">备注</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; foreach($products as $product){ ?>
                        <?php
                            $nameCompColor = '';
                            if($product['name'] !== $product['sys_name']){
                                $nameCompColor = 'style="background-color:#ffffaa;"' ;
                            }

                            $retailPriceCompColor = '';
                            if(round($product['ori_retail_price'],2) !== round($product['last_retail_price'],2)){
                                $retailPriceCompColor = 'style="background-color:#f89406;"' ;
                            }
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i; ?></td>
                            <td class="text-center"><?php echo $product['product_id']; ?></td>
                            <td class="text-center" <?php echo $nameCompColor; ?> ><?php echo $product['name']; ?></td>
                            <td class="text-center" <?php echo $nameCompColor; ?> ><?php echo $product['sys_name']; ?></td>
                            <td class="text-center" <?php echo $retailPriceCompColor; ?> ><?php echo $product['last_retail_price']; ?></td>
                            <td class="text-center" <?php echo $retailPriceCompColor; ?> ><?php echo round($product['ori_retail_price'],2); ?></td>
                            <td class="text-center"><?php echo round($product['retail_price'],2); ?></td>
                            <td class="text-center"><?php echo round(($product['last_retail_price']-$product['retail_price']),2); ?></td>
                            <td class="text-center"><?php echo round(($product['last_retail_price']-$product['retail_price'])/$product['last_retail_price']*100,1).'%'; ?></td>
                            <td class="text-center"><?php echo round($product['last_price'],2); ?></td>
                            <td class="text-center"><?php echo round($product['retail_price']*0.8,1); ?></td>
                            <td class="text-center"><?php echo $product['memo']; ?></td>
                        </tr>
                        <?php $i++; } ?>
                        <tr>
                            <td class="text-right" colspan="12">
                                <div style="font-style: italic; font-size: 1.2em">正准备导入商品价格数据，当日已上传同商品编号数据将被覆盖</div>
                                <button type="button" class="btn btn-primary" onclick="submitForm();">确认提交</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php } ?>
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