<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="page-header">
    <div class="container-fluid">
        <h1>采购单</h1>
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
            <h3 class="panel-title"><i class="fa fa-pencil"></i>采购单
            <?php echo '-' . $order_id;?>
            </h3>
        </div>

        <div class="panel-body">
            <div class="tab-content">
                <div id="tab-special">
                    <div class="table-responsive">
                        <form action="<?php echo $action_select; ?>" method="post" enctype="multipart/form-data" id="form-product-select" class="form-horizontal">
                            <div class="well">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="input-station-id">商品类型</label>
                                            <select name="station_id" id="input-station-id" class="form-control">
                                                <option value="*"></option>
                                                <?php foreach ($order_stations as $key=>$order_station) { ?>
                                                    <?php if ($key == $station_id) { ?>
                                                        <option value="<?php echo $key; ?>" selected="selected"><?php echo $order_station; ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $order_station; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                
                                        <div class="form-group">
                                            <label class="control-label" for="input-order-status">订单状态</label>
                                            <select name="filter_order_status_id" id="input-order-status" class="form-control">
                                                <option value="*"></option>
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                    <?php if ($order_status['order_status_id'] == $status) { ?>
                                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                        <label class="control-label" for="input-supplier-type">供应商</label>
                                        <select name="supplier_type" id="input-supplier-type" class="form-control">
                                            <option value="*"></option>
                                            <?php foreach ($supplier_types as $s_type) { ?>
                                                <?php if ($s_type['supplier_id'] == $supplier_type) { ?>
                                                    <option value="<?php echo $s_type['supplier_type_id']; ?>" selected="selected"><?php echo $s_type['name']; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $s_type['supplier_type_id']; ?>"><?php echo $s_type['name']; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        </div>
                                    </div>
              
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label" for="input_date_deliver">到货日期</label>
                                            <div class="input-group date">
                                                <input type="text" name="date_deliver" value="<?php echo $date_deliver; ?>" placeholder="到货日期" data-date-format="YYYY-MM-DD" id="input-date-deliver" class="form-control" />
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                        <?php if($order_info['checkout_status'] != 1){ ?>
                                            <div class="form-group">
                                                <button type="button" id="button-filter" class="btn btn-primary pull-right" onclick="markAdjustOrder()"> 生成调整单 </button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- <form action="" method="post" enctype="multipart/form-data" id="form-product-adjust" class="form-horizontal"> -->
                        <form method="post" enctype="multipart/form-data" id="form-plist-edit" class="form-horizontal">
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
                                        <?php if($order_info['checkout_status'] == 1 && $modifyPermission) { ?>
                                            <td class="text-center">操作</td>
                                        <?php } else { ?>
                                            <td class="text-center operation" style="display: none;">操作</td>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- 添加商品可售库存 -->
                                    <?php foreach($products as $key=>$value){ ?>
                                        <?php
                                            $style = '';
                                            $can_adjust = true;
                                            if(in_array($value['product_id'], $finish_adjust_products)){
                                                $style = "text-decoration:line-through; color: #BBBBBB;";
                                                $can_adjust = false;
                                            }
                                        ?>
                                        <tr id="plist_<?php echo $value['purchase_order_product_id']; ?>" style="<?php echo $style; ?>" >
                                            <td class="text-center"><?php echo $value['product_id'];?></td>
                                            <td class="text-center editable" datatag="quantity"><?php echo $value['quantity'];?></td>
                                            <td class="text-center editable" datatag="supplier_quantity"><?php echo $value['supplier_quantity'];?></td>
                                            <td class="text-center editable changeOrder" datatag="price"><?php echo $value['price'];?></td>
                                            <td class="text-center editable" datatag="p_total"><?php echo $value['price']*$value['supplier_quantity'];?></td>
                                            <td class="text-center editable" datatag="real_cost"><?php echo $value['real_cost']; ?></td>
                                            <td class="text-center"><?php echo isset($order_get_product_info[$value['product_id']]) ? $order_get_product_info[$value['product_id']] : '';?></td>
                                            <td class="text-center"><?php echo $value['name'];?></td>
                                            <?php if($order_info['checkout_status'] == 1 && $modifyPermission) { ?>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary" id="edit_plist_<?php echo $value['purchase_order_product_id']; ?>" onclick="editRow('plist',<?php echo $value['purchase_order_product_id']; ?>)"><i class="fa fa-pencil"></i></button>
                                                    <button style="display: none" id="save_plist_<?php echo $value['purchase_order_product_id']; ?>" type="button" class="btn btn-danger" onclick="updateRow('form-plist-edit','plist',<?php echo $value['purchase_order_product_id']; ?>, <?php echo $order_id; ?>)"><i class="fa fa-save"></i></button>
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-center operation" style="display: none;">
                                                    <?php if($can_adjust){ ?>
                                                        <button type="button" class="btn btn-primary btn-sm" id="edit_plist_<?php echo $value['purchase_order_product_id']; ?>" onclick="editAdjustOrder('plist', <?php echo $value['purchase_order_product_id']; ?>)"><i class="fa fa-pencil"></i></button>
                                                        <button type="button" class="btn btn-danger btn-sm"  id="save_plist_<?php echo $value['purchase_order_product_id']; ?>" onclick="createAdjustOrder('plist', <?php echo $value['purchase_order_product_id']; ?>, <?php echo $order_id; ?>)" style="display: none;"><i class="fa fa-save"></i></button>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td>收货注意事项：</td>
                                        <td colspan="5"><?php echo $order_comment;?></td>
                                    </tr>
                                </tbody>
                                <tfoot>

                                </tfoot>
                            </table>
                        </form>

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
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($adjust_products as $k=>$v){ ?>
                                    <tr <?php if($v['status'] == 0){ echo 'style="text-decoration:line-through; color: #BBBBBB;"'; } ?> >
                                      <td class="text-center"><?php echo $v['product_id']; ?></td>
                                      <td class="text-center editable" datatag="quantity"><?php echo $v['quantity']; ?></td>
                                      <td class="text-center editable" datatag="supplier_quantity"><?php echo $v['supplier_quantity']; ?></td>
                                      <td class="text-center editable changeOrder" datatag="price"><?php echo $v['price']; ?></td>
                                      <td class="text-center editable" datatag="p_total"><?php echo $v['price']*$v['supplier_quantity']; ?></td>
                                      <td class="text-center editable" datatag="real_cost"><?php echo $v['real_cost']; ?></td>
                                      <td class="text-center"><?php echo isset($order_get_product_info[$v['product_id']]) ? $order_get_product_info[$v['product_id']] : ''; ?></td>
                                      <td class="text-center"><?php echo $v['name']; ?></td>
                                      <td class="text-center"><?php echo $v['status_name']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } ?>

                        <form action="<?php echo $action_image;?>" method="post" enctype="multipart/form-data" id="form-order-image" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-image"></label>
                                <div class="col-sm-10">
                                    <!--<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="" /></a>-->
                                    <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                                    <input type="hidden" name="purchase_order_id" value="<?php echo $order_id; ?>" id="input-purchase-order-id" />
                                </div>
                            </div>
                      
                            <table id="images" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td class="text-left">收货单</td>
                                        <td>收货单名称</td>
                                        <td>收货单编号</td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $image_row = 0; ?>
                                    <?php foreach ($product_images as $product_image) { ?>
                                        <tr id="image-row<?php echo $image_row; ?>">
                                            <td class="text-left"><a href="<?php echo $product_image['image'];?>" id="thumb-image<?php echo $image_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $product_image['thumb']; ?>" alt="" title="" data-placeholder="采购单图片" /></a><input type="hidden" name="product_image[<?php echo $image_row; ?>][image]" value="<?php echo $product_image['image_dir']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                                            <td><?php echo $product_image['image_title'];?><input type="hidden" name="product_image[<?php echo $image_row; ?>][image_title]" value="<?php echo $product_image['image_title']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                                            <td><?php echo $product_image['image_num'];?><input type="hidden" name="product_image[<?php echo $image_row; ?>][image_num]" value="<?php echo $product_image['image_num']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                                            <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="删除图片" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                                        </tr>
                                        <?php $image_row++; ?>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="1"><button type="button" id="button-filter" class="btn btn-primary pull-right" onclick="submitImage();"><i class="fa"></i> 提交收货单图片</button></td>
                                        <td class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="添加图片" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
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
 
<script type="text/javascript">
    var global = {
        "rowData":{
            "plist" : {}
        }
    };
    getPrePurchaseOrderProducts(<?php echo $order_id; ?>);

    function getPrePurchaseOrderProducts(id){
        $.ajax({
            type: 'GET',
            async: false,
            cache: false,
            url: 'index.php?route=purchase/pre_purchase/getPrePurchaseOrderProducts&token=<?php echo $_SESSION["token"]; ?>&id=' + id,
            dataType: 'json',
            success: function(data){
                //console.log(data);
                $.each(data, function(i,v){
                    global.rowData['plist'][v.purchase_order_product_id] = v;
                });
            }
        });
    }

    function refresh(type){
        $.each(global.rowData[type], function(i,v){
            $.each($('#'+type+'_'+ i + ' .editable'), function(index,value){
                targetRowName = $(this).attr("datatag");
                targetRowValue = global.rowData[type][i][targetRowName];

                $(this).html(targetRowValue);
            });

            $('#edit_'+type+'_'+i).show();
            $('#save_'+type+'_'+i).hide();

        });
    }

    function editRow(type, id){
        var targetRow = '#'+type+'_'+id;
        var targetRowName = '';
        var targetRowValue = '';


        //Recorver All Others
        refresh(type);

        $.each($(targetRow+' .editable'), function(i,v){
            targetRowName = $(this).attr("datatag");
            targetRowValue = $(this).text();

            $(this).html('<input type="text" name="'+ targetRowName +'" value="'+ targetRowValue +'" />');
        });

        $('#edit_'+type+'_'+id).hide();
        $('#save_'+type+'_'+id).show();
    }

    function updateRow(formId,type,id,order_id){
        if(confirm('确认修改采购的吗？')){
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=purchase/pre_purchase/update&token=<?php echo $_SESSION["token"]; ?>',
                data : {
                    type : type,
                    id : id,
                    order_id : order_id,
                    postData : $('#'+formId).serializeArray()
                },
                success: function(data){
                    console.log(data);
                    var returnData = $.parseJSON(data);
                    if(returnData.return_code == 'SUCCESS'){
                        getPrePurchaseOrderProducts(<?php echo $order_id; ?>);
                        refresh(type);
                    }
                    alert(returnData.return_message);
                },
                error: function(){
                    alert('未知错误，请检查登录状态和网路连接情况');
                }
            });
        }
    }

    //var r = /^[0-9]*[1-9][0-9]*$/　　//正整数
    //r.test(str); //str为你要判断的字符 执行返回结果 true 或 false

    var image_row = <?php echo $image_row; ?>;
    
    
    function addImage() {
        
        html  = '<tr id="image-row' + image_row + '">';
        html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail"><img src="<?php echo $no_image_thumb; ?>" alt="" title="" data-placeholder="采购单" /><input class="int" type="hidden" name="product_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
        html += '  <td class="text-left"><input class="int" type="text" name="product_image[' + image_row + '][image_title]" value="" id="input-image' + image_row + '" /></td>';
            html += '  <td class="text-left"><input class="int" type="text" name="product_image[' + image_row + '][image_num]" value="" id="input-image' + image_row + '" /></td>';
        html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="移除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';

        $('#images tbody').append(html);

        image_row++;
    }
    
    
    
    var adjust_row = 0;
    
    function addAdjust() {
        html  = '<tr id="adjust-row' + adjust_row + '">';
        html += '  <td class="text-center"><input type="text" name="products[' + adjust_row + '][product_id]" value="" placeholder="商品ID" class="form-control int" onChange="getProductInv($(this).val(), $(this));" /></td>';
        html += '  <td class="text-left"><input type="text" name="products[' + adjust_row + '][quantity]" value="" placeholder="订购数量" class="form-control int" /></td>';
        html += '  <td class="text-left"></td>';
        html += '  <td class="text-left"></td>';
        html += '  <td class="text-left"><div class="rowName"></div></td>';
        html += '  <td class="text-left"><div class="rowOriInv"></div></td>';
        html += '  <td class="text-left"><div class="rowCurrInv"></div></td>';
        html += '  <td class="text-left"><button type="button" onclick="$(\'#adjust-row' + adjust_row + '\').remove();" data-toggle="tooltip" title="删除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';

        $('#adjust tbody').append(html);
        adjust_row++;
    }

    function submitAdjust(){
        var trs = $('#adjust tbody tr').length;
        if(trs < 1){
            alert('请先添加.');
            return false;
        }

        var valid = true;
        $('#adjust tbody input.int').each(function(index, element){
            var val = parseInt($(element).val());
            if(!val){
                valid = false;
                return false;
            }
        });
        
        var date_deliver = $("#input-date-deliver").val();
        if(date_deliver == ''){
            alert("请选择到货日期");return false;
        }

        if(window.confirm('确认提交采购订单？')){
            $('#form-product-adjust').submit();
        }
    }


    function submitSelect(){
        
        
        var pre_three_day_before = $("#input-date-before").val();
        if(pre_three_day_before == ''){
            alert("请选择均销开始日期！");
            return false;
        }
        
        var pre_three_day_end = $("#input-date-end").val();
        if(pre_three_day_end == ''){
            alert("请选择均销结束日期！");
            return false;
        }
        
        var station_id = $("#input-station-id").val();
        if(station_id == '*'){
            alert("请选择商品类型！");
            return false;
        }
        
        
        
        var supplier_type = $("#input-supplier-type").val();
        if(supplier_type == '*'){
            alert("请选择供应商分类！");
            return false;
        }
        
        

        $('#form-product-select').submit();
        
    }


    function submitImage(){
        var inp_error = 0;
        $('#images tbody input.int').each(function(index, element){
            var val = $(element).val();
            if(val == ''){
                
                inp_error = 1;
            }
        });

        if(inp_error == 1){
                alert("送货单图片、送货单标题、送货单编号 不能为空！");
                return false;
            }
        
        $('#form-order-image').submit();
        
    }


    function getProductInv(product_id,obj){
        var rowId = obj.parent().parent().attr('id');
        //rowStation
        //rowName
        //rowOriInv
        //rowCurrInv

        var product_id = parseInt(product_id);

        $.ajax({
            type: 'GET',
            async: false,
            cache: false,
            url: 'index.php?route=catalog/inventory/getProductInv&token=<?php echo $_SESSION['token']; ?>',
            data: {
                product_id: product_id
            },
            dataType: 'json',
            success: function(response){
                console.log(response);

                if(parseInt(response.station_id) > 0){
                   
                    $('#'+rowId+' .rowName').html(response.name);
                    $('#'+rowId+' .rowOriInv').html(response.ori_inv);
                    $('#'+rowId+' .rowCurrInv').html(response.curr_inv);
                    
                }
                else{
                    
                }
            }
        });
    }

    function markAdjustOrder(){
        $('#operation').show();
        $('.operation').show();
    }

    function editAdjustOrder(type, id){
        var targetRow       = '#'+ type +'_'+ id;
        var targetRowName   = '';
        var targetRowValue  = '';

        $.each(global.rowData[type], function(i, v){
            $.each($('#'+ type +'_'+ i + ' .changeOrder'), function(index,value){
                targetRowName  = $(this).attr("datatag");
                targetRowValue = global.rowData[type][i][targetRowName];
                $(this).html(targetRowValue);
            });
            $('#edit_'+ type +'_'+ i).show();
            $('#save_'+ type +'_'+ i).hide();
        });

        $.each($(targetRow+' .changeOrder'), function(i, v){
            targetRowName  = $(this).attr("datatag");
            targetRowValue = $(this).text();
            $(this).html('<input type="text" name="'+ targetRowName +'" value="'+ targetRowValue +'" data="'+ targetRowValue +'" />');
        });

        $('#edit_'+ type +'_'+id).hide();
        $('#save_'+ type +'_'+id).show();
    }


    function createAdjustOrder(type, id, order_id){
        if(confirm('确认生成采购调整单吗？')){
            var targetRow   = '#'+ type +'_'+ id;
            var price       = parseFloat($(targetRow +' input[name=\'price\']').val());
            if(price <= 0 || isNaN(price)){
                alert('采购价格不正确.');
                return false;
            }
            var oldPrice  = parseFloat($(targetRow +' input[name=\'price\']').attr("data"));
            if(oldPrice == price){
                alert('采购价格相同');
                return false;
            }

            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=purchase/pre_purchase_adjust/createAdjustOrder&token=<?php echo $_SESSION["token"]; ?>',
                data : {
                    id : id,
                    order_id : order_id,
                    price : price
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