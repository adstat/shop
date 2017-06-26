<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>采购调整单</h1>
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

        <?php if (isset($success_msg) && $success_msg) { ?>
        <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success_msg; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i>采购调整单 <?php echo '- ' . $order_id;?></h3>
            </div>

            <div class="panel-body">
                <div class="tab-content">
                    <div id="tab-special">
                        <div class="table-responsive">

                            <table id="adjust" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td class="text-center" width="10%">商品ID</td>
                                        <td class="text-center" width="10%">采购数量</td>
                                        <td class="text-center" width="10%">供应商采购数量</td>
                                        <td class="text-center" width="10%">采购价格</td>
                                        <td class="text-center" width="10%">采购总价</td>
                                        <td class="text-center" width="10%">真实采购价</td>
                                        <td class="text-center" width="10%">实收数量</td>
                                        <td class="text-center">商品名称</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products as $key => $value){ ?>
                                        <tr style="
                                            <?php
                                                if(array_key_exists($value['product_id'], $adjust_product_status)){
                                                    echo $adjust_product_status[$value['product_id']] == 1 ? 'text-decoration:line-through; color: #BBBBBB;' : 'color: #f56b6b;';
                                                }
                                            ?>">
                                            <td class="text-center"><?php echo $value['product_id'];?></td>
                                            <td class="text-center"><?php echo $value['quantity'];?></td>
                                            <td class="text-center"><?php echo $value['supplier_quantity'];?></td>
                                            <td class="text-center"><?php echo $value['price'];?></td>
                                            <td class="text-center"><?php echo $value['price']*$value['supplier_quantity'];?></td>
                                            <td class="text-center"><?php echo $value['real_cost']; ?></td>
                                            <td class="text-center"><?php echo isset($order_get_product_info[$value['product_id']]) ? $order_get_product_info[$value['product_id']] : '';?></td>
                                            <td class="text-center"><?php echo $value['name'];?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td>收货注意事项：</td>
                                        <td colspan="5"><?php echo $order_comment;?></td>
                                    </tr>
                                </tbody>
                            </table>


                            <?php if(!empty($adjust_products)){ ?>
                                <table class="table table-striped table-bordered table-hover" style="margin-top: 50px;">
                                    <thead>
                                        <tr>
                                            <td class="text-center" width="10%">商品ID</td>
                                            <td class="text-center" width="10%">采购数量</td>
                                            <td class="text-center" width="10%">供应商采购数量</td>
                                            <td class="text-center" width="10%">采购价格</td>
                                            <td class="text-center" width="10%">采购总价</td>
                                            <td class="text-center" width="10%">真实采购价</td>
                                            <td class="text-center" width="10%">实收数量</td>
                                            <td class="text-center">商品名称</td>
                                            <td class="text-center">是否生效</td>
                                            <td class="text-center">操作</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($adjust_products as $k=>$v){ ?>
                                            <tr style="<?php echo $v['status'] == 0 ? 'text-decoration:line-through; color: #BBBBBB' : 'color: #8fbb6c'; ?>">
                                                <td class="text-center"><?php echo $v['product_id']; ?></td>
                                                <td class="text-center"><?php echo $v['quantity']; ?></td>
                                                <td class="text-center"><?php echo $v['supplier_quantity']; ?></td>
                                                <td class="text-center"><?php echo $v['price']; ?></td>
                                                <td class="text-center"><?php echo $v['price']*$v['supplier_quantity']; ?></td>
                                                <td class="text-center"><?php echo $v['real_cost']; ?></td>
                                                <td class="text-center"><?php echo isset($order_get_product_info[$v['product_id']]) ? $order_get_product_info[$v['product_id']] : ''; ?></td>
                                                <td class="text-center"><?php echo $v['name']; ?></td>
                                                <td class="text-center"><?php echo $v['status_name']; ?></td>
                                                <td class="text-center">
                                                    <?php if($v['status'] == 0 && $v['order_status'] == 1){ ?>
                                                        <button type="button" class="btn btn-success btn-sm" onclick="passAdjustOrder(<?php echo $order_id; ?>, <?php echo $v['purchase_order_product_id']; ?>)"> 通过 </button>
                                                        <button type="button" class="btn btn-danger  btn-sm" onclick="cancelAdjustOrder(<?php echo $order_id; ?>, <?php echo $v['purchase_order_product_id']; ?>)"> 取消 </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function passAdjustOrder(order_id, order_product_id){
            if(confirm('确认修改采购调整单吗？')){
                $.ajax({
                    type: 'POST',
                    async: false,
                    cache: false,
                    url: 'index.php?route=purchase/pre_purchase_adjust/passAdjustOrder&token=<?php echo $_SESSION["token"]; ?>',
                    data : {
                        order_id : order_id,
                        id : order_product_id
                    },
                    success: function(data){
                        console.log(data);
                        var returnData = $.parseJSON(data);
                        alert(returnData.return_message);
                        if(returnData.return_code == 'SUCCESS'){
                            window.location.reload();
                        }
                    },
                    error: function(){
                        alert('未知错误，请检查登录状态和网路连接情况');
                    }
                });
            }
        }

        function cancelAdjustOrder(order_id, order_product_id){
            if(confirm('确认修改采购的吗？')){
                $.ajax({
                    type: 'POST',
                    async: false,
                    cache: false,
                    url: 'index.php?route=purchase/pre_purchase_adjust/cancelAdjustOrder&token=<?php echo $_SESSION["token"]; ?>',
                    data : {
                        order_id : order_id,
                        id : order_product_id
                    },
                    success: function(data){
                        console.log(data);
                        var returnData = $.parseJSON(data);
                        alert(returnData.return_message);
                        if(returnData.return_code == 'SUCCESS'){
                            window.location.reload();
                        }
                    },
                    error: function(){
                        alert('未知错误，请检查登录状态和网路连接情况');
                    }
                });
            }
        }

    </script>
    <script type="text/javascript">
        <!--
        $('.date').datetimepicker({
            pickTime: false
        });
        //-->
    </script>
</div>
<?php echo $footer; ?>