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
    <h3 class="panel-title"><i class="fa fa-pencil"></i>采购单</h3>
</div>
<div class="panel-body">
<form action="<?php echo $action_select; ?>" method="post" enctype="multipart/form-data" id="form-product-select">
    <div class="well">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="input_date_before">均销开始日期</label>
                    <div class="input-group date">
                        <input type="text" name="date_before" value="<?php echo $pre_three_day_before; ?>" placeholder="均销开始日期" data-date-format="YYYY-MM-DD" id="input-date-before" class="form-control" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>
                </div>
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
                    <label class="control-label" for="input-status-id">商品状态</label>
                    <select name="status_id" id="input-status-id" class="form-control">
                        <option value="*" <?php echo $status_id == '*' ? 'selected="selected"' : '';?> ></option>
                        <?php foreach ($product_statuses as $key=>$product_status) { ?>
                        <?php if ($key == $status_id && $status_id != '*') { ?>
                        <option value="<?php echo $key; ?>" selected="selected"><?php echo $product_status; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $key; ?>"><?php echo $product_status; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="input_date_end">均销结束日期</label>
                    <div class="input-group date">
                        <input type="text" name="date_end" value="<?php echo $pre_three_day_end; ?>" placeholder="均销结束日期" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                            </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="input-supplier-type">供应商</label>
                    <select name="supplier_type" id="input-supplier-type" class="form-control">
                        <option value="*"></option>
                        <?php foreach ($supplier_types as $s_type) { ?>
                        <?php if ($s_type['supplier_id'] == $supplier_type) { ?>
                        <option value="<?php echo $s_type['supplier_id']; ?>" selected="selected"><?php echo $s_type['name']."#".$s_type['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $s_type['supplier_id']; ?>"><?php echo $s_type['supplier_id']."#".$s_type['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label" for="input-s-quantity-status-id">当前库存为今日截止库存</label>
                    <select name="s_quantity_status_id" id="input-s-quantity-status-id" class="form-control">
                        <option value="1" <?php echo $s_quantity_status_id == 1 ? 'selected="selected"' : '';?> >是</option>
                        <option value="2" <?php echo $s_quantity_status_id == 2 ? 'selected="selected"' : '';?> >否，需根据均销扣除当前库存</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label">排除日期</label>
                    <input type="text" name="date_no_in" id="input_date_no_in"  value="<?php echo $date_no_in; ?>" placeholder="2016-05-12,2016-05-13,..." class="form-control" />
                </div>
                <div class="form-group">
                    <label class="control-label" for="input_date_deliver">到货日期</label>
                    <div class="input-group date">
                        <input type="text" name="date_deliver" value="<?php echo $date_deliver; ?>" placeholder="到货日期" data-date-format="YYYY-MM-DD" id="input-date-deliver" class="form-control" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                            </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="input-warehouse-id">选择仓库</label>
                    <select name="select_warehouse_id" id="input-warehouse-id" onchange="setWarehouseId();" class="form-control">
                        <option value="0">请选择仓库</option>
                        <?php foreach($warehouses as $warehouse){ ?>
                        <option value="<?php echo $warehouse['warehouse_id']; ?>"><?php echo  $warehouse['title']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="pull-right" style="margin-right: 15px;">
                    <button type="button" id="button-filter" class="btn btn-primary pull-right" onclick="submitSelect();"><i class="fa fa-search"></i> 选择供应商</button>
                    <button style="margin-right: 15px;" type="button" class="btn btn-default" onclick="javascript:getPrePurchaseProductByAlert();">计算三日预警</button>
                    <button style="margin-right: 15px;" type="button" class="btn btn-default" onclick="javascript:getPrePurchaseProduct();">计算均销</button>
                    <br />
                    <a href="javascript:removePrePurchaseProduct();">清除列表</a>
                </div>
            </div>

            </div>
            <input type=hidden name="preload" id="preload" value="0" />
            <input type=hidden name="pre_purchase_alert" id="pre_purchase_alert" value="0" />
        </div>
    </div>
</form>
<div class="alert alert-info">
    先选择供应商信息，一次采购可录入多条商品记录，但不能有重复商品。输入<strong style="color: goldenrod">"供应商规格数量"</strong>，将根据如下公式自动计算<strong>"入库规格数量"</strong>，“入库规格数量”可手工调整。
    <br />
    <i>Exp. 按箱采购的商品：<strong>入库规格数量</strong>=供应商规格数量*供应商规格/商品规格；按件采购的商品：<strong>入库规格数量</strong>=供应商规格数量/商品规格</i>
    <br />
    <i>Exp. <strong style="color: goldenrod">计算三日预警时</strong>，<strong>计算采购数量</strong>=当前库存-按5日均销值*3天-已采购3天到货的数量。</i>
</div>

<div class = "alert">
    <button type="button" class="btn btn-primary" onclick="showExcel();">导入外仓采购单</button>
</div>

<div class="table-responsive alert">
    <div id="excel-show" style="display: none">
    <div class="form-group">
        <label class="col-sm-2 control-label" for="input-upload-xls">上传Excel文件 (<span style="color: #CC0000" title="必须按照多单导入模板格式导入。">参考模板</span>)</label>
        <div class="col-sm-10">
            <form enctype="multipart/form-data" id="form-product-excel" class="form-horizontal">
            <input type="file" name="file" id="input-upload-xls" class="form-control">
            </form>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">务必严格按照模板格式</label>
        <div class="col-sm-10">
            <button type="button" class="btn btn-primary" onclick="writeToTable();">上传</button>
        </div>
    </div>
    </div>
</div>

<div class="table-responsive">
    <form action="" method="post" enctype="multipart/form-data" id="form-product-adjust" class="form-horizontal">

        <table id="adjust" class="table table-striped table-bordered table-hover">
            <thead>

            <tr>

                <td class="text-center" width="75px">商品ID</td>
                <td class="text-center" width="70px">计算采购数量</td>
                <td class="text-center" title="供应商采购数量" width="70px">供应商规格数量</td>
                <td class="text-center" width="70px">入库规格数量</td>
                <td class="text-center" width="90px">采购价格</td>
                <td class="text-center" width="90px">真实成本</td>
                <td class="text-center" width="55px">供应商规格</td>
                <td class="text-center" width="50px">商品规格</td>
                <td class="text-center" width="60px">按箱/件</td>
                <!--<?php foreach($date_array as $key=>$value){ ?>
                <td class="text-center" width="5%"><?php echo $value;?>销量</td>
                <?php } ?>-->
                <td class="text-center" width="60px">日销量</td>
                <td class="text-center" width="50px">均销</td>
                <td class="text-center" width="65px">已采购</td>
                <td class="text-center">商品名称</td>
                <td class="text-center" width="55px">可售库存</td>
                <td class="text-center" width="55px">当前库存</td>
                <td width="55px">操作</td>
            </tr>


            </thead>
            <tbody>
                <style>
                    .product_quantity{
                        font-weight: bold;
                        background-color: gold;
                    }
                </style>
            <!-- 添加商品可售库存 -->
            <?php foreach($pre_purchase_product as $key=>$value){ ?>
            <?php if($value['pre_purchase_product']['pre_purchase_quantity'] > 0 || $station_id == 2){ ?>

            <tr id="adjust-row<?php echo $key;?>">
                <td class="text-center"><input product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][product_id]" value="<?php echo $key;?>" placeholder="商品ID" class="form-control int product_id" readonly="readonly" type="text"></td>
                <td class="text-center"><div product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][purchase_quantity_old]"><?php echo $value['pre_purchase_product']['pre_purchase_quantity_old'];?></div></td>
                <td class="text-left"><input product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][supplier_quantity]" value="<?php echo $value['pre_purchase_product']['supplier_quantity'] > 0 ? $value['pre_purchase_product']['supplier_quantity'] : 0;?>" placeholder="请输入供应商采购数量" class="form-control int supplier_quantity" onChange="updateQuantity($(this).parent().parent().attr('id'));" type="text"></td>
                <td class="text-left"><input product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][quantity]" value="<?php echo $value['pre_purchase_product']['pre_purchase_quantity'] > 0 ? $value['pre_purchase_product']['pre_purchase_quantity'] : 0;?>" placeholder="请输入采购数量" class="form-control int product_quantity" type="text"></td>
                <td class="text-left"><input product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][price]" value="<?php echo $value['pre_purchase_product']['price'];?>" placeholder="请输入采购价格" class="form-control int price" type="text"></td>
                <td class="text-left"><input product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][real_cost]" value="<?php echo $value['pre_purchase_product']['real_cost'];?>" placeholder="请输入真实成本" class="form-control int real_cost" type="text"></td>
                <td class="text-left"><div class="supplier_unit_size"><?php echo $value['pre_purchase_product']['supplier_unit_size'];?></div></td>
                <td class="text-left"><div class="inv_size"><?php echo $value['pre_purchase_product']['inv_size'];?></div></td>
                <td class="text-left">
                    <div class="supplier_order_quantity_type_name">
                        <?php
                            echo  $value['pre_purchase_product']['supplier_order_quantity_type'] == 1 ? "按箱" : "按件";
                        ?>
                    </div>
                    <input type="hidden" class="supplier_order_quantity_type" value="<?php echo $value['pre_purchase_product']['supplier_order_quantity_type'];?>" />
                </td>
                <td class="text-left"><div class="rowStatus">
                        <?php foreach($date_array as $dk=>$dv){ ?>
                        <?php echo !isset($value[$dv]) ? "<span style='color:red;'>" : ""; ?>
                        <?php echo $dv;?> [
                        <?php echo isset($value[$dv]) ? $value[$dv]['op_quantity'] : 0 ;?>
                        ]
                        <?php echo !isset($value[$dv]) ? "</span>" : ""; ?>
                        <br>
                        <?php } ?>
                    </div></td>
                <td class="text-left"><div class="rowOriInv"><?php echo $value['pre_purchase_product']['avg_quantity'];?></div></td>
                <td class="text-left"><div class="rowStatus">
                        <?php if(isset($pre_purchase_order_product[$key])){ ?>
                        <?php foreach($pre_purchase_order_product[$key] as $ok=>$ov){ ?>
                        <?php echo $ok;?> [
                        <?php echo $ov['quantity'] ;?>
                        ]<br>
                        <?php } ?>
                        <?php } ?>

                        <?php
                            if(isset($value['pre_purchase_product']['inv_in'])){
                                echo $value['pre_purchase_product']['inv_in'];
                            }
                        ?>
                    </div></td>
                <td class="text-left"><div class="rowCurrInv"><?php echo $value['pre_purchase_product']['name'] . " [¥".$value['pre_purchase_product']['sale_price']."]";?></div></td>
                <td class="text-left"><div class="rowCurrInv"><?php echo $value['pre_purchase_product']['ori_inv'];?></div></td>
                <td class="text-left"><div class="rowCurrInv"><?php echo $value['pre_purchase_product']['s_quantity'];?></div></td>
                <td class="text-left"><button type="button" onclick="removeRow(<?php echo $key;?>);" data-toggle="tooltip" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
            </tr>
            <?php  } ?>
            <?php } ?>
            </tbody>
            <tfoot>

            <tr>
                <td colspan="14">
                    <input id="get_order_total" onclick="getOrderTotal();" type="button" value="计算采购金额" >:<span id="order_total"></span>
                    <input id="get_order_total_num" onclick="getOrderTotalNum();" type="button" value="计算采购数量" >:<span id="order_total_num"></span>
                    <input id="get_supplier_order_total_num" onclick="getOrderSupplierTotalNum();" type="button" value="计算供应商数量" >:<span id="supplier_order_total_num"></span>
                </td>
                <td class="text-left"><button type="button" onclick="addAdjust();" data-toggle="tooltip" title="添加" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
            </tr>
            <tr>
                <td colspan="14" class="text-center">
                    <input type="hidden" name="station_id" value="<?php echo $station_id?>">
                    <input type="hidden" name="supplier_type" value="<?php echo $supplier_type?>">
                    <input type="hidden" name="warehouse_id" value="<?php echo $filter_warehouse_id_global; ?>" >
                </td>
            </tr>
            </tfoot>
        </table>
        <div class="col-sm-4">
            <div class="form-group">
                <div style="font-size: 1.4em;">
                    可用余额：<?php echo $supplier_credits?>&nbsp;&nbsp;
                    <?php
                                $is_check_credits = '';
                                //if(isset($checkout_info['checkout_type_id']) && $checkout_info['checkout_type_id'] == 1 && $supplier_credits > 0){
                    if($supplier_credits > 0){
                    $is_check_credits = 'checked="checked"';
                    }
                    ?>
                    使用余额支付：<input type="checkbox" <?php echo $is_check_credits;?> name="sub_use_credits" value="1" >
                </div>
                确认到货时间：<input type="text" name="date_deliver" value="<?php echo $date_deliver;?>" placeholder="到货日期" data-date-format="YYYY-MM-DD" id="input-date-deliver" class="form-control" />
                收货注意事项：<textarea name="order_comment" value="" placeholder=""  id="input-order-comment" class="form-control" ></textarea>
                <br>供应商支付信息
                <br>收款人：<?php echo isset($checkout_info['checkout_username']) ? $checkout_info['checkout_username'] : '';?>
                <br>银行：<?php echo isset($checkout_info['checkout_userbank']) ? $checkout_info['checkout_userbank'] : '';?>
                <br>卡号：<?php echo isset($checkout_info['checkout_usercard']) ? $checkout_info['checkout_usercard'] : '';?>
                <div style="font-size: 1.4em;">
                    <br>是否有发票：<input type="radio" value="1" name="invoice_flag" <?php echo isset($checkout_info['invoice_flag'])&&$checkout_info['invoice_flag'] == 1 ? 'checked="checked"' : "";?> >是
                    <input type="radio" value="0" name="invoice_flag" <?php echo isset($checkout_info['invoice_flag'])&&$checkout_info['invoice_flag'] == '0' ? 'checked="checked"' : "";?> >否
                </div>
                <br>
                <div style="float:left;" class="col-sm-4"><button type="button" class="btn btn-primary" onclick="submitAdjust();">确认添加</button></div>
            </div>

        </div>
    </form>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
var productList = [];



function getOrderTotal(){
    var order_total = 0;
    $('#adjust tbody tr').each(function(index, element){
        var supplier_quantity = $(element).find(".supplier_quantity").val();
        var price =  $(element).find(".price").val();

        order_total  = order_total + parseFloat((supplier_quantity * price).toFixed(2));
    });
    $("#order_total").html(order_total);
}

function getOrderTotalNum(){
    var order_total = 0;
    $('#adjust tbody tr').each(function(index, element){
        var quantity = $(element).find(".product_quantity").val();

        order_total  = order_total + parseInt(quantity);
    });
    $("#order_total_num").html(order_total);
}

function getOrderSupplierTotalNum(){
    var supplier_order_total = 0;
    $('#adjust tbody tr').each(function(index,element){
        var quantity = $(element).find(".supplier_quantity").val();

        supplier_order_total += parseInt(quantity);
    });
    $("#supplier_order_total_num").html(supplier_order_total);
}

var adjust_row = 0;


function addAdjust() {
    html  = '<tr id="adjust-row' + adjust_row + '">';
    html += '  <td class="text-center"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][product_id]" value="" placeholder="商品ID" class="form-control int product_id" onChange="getProductInv($(this).val(), $(this));" /></td>';
    html += '  <td class="text-left"></td>';
    html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][supplier_quantity]" value="" title="供应商采购数量" placeholder="供应商采购数量" class="form-control int supplier_quantity" onChange="updateQuantity($(this).parent().parent().attr(\'id\'));" /></td>';
    html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][quantity]" value="" title="按供应商规格计算后采购数量" placeholder="按供应商规格计算后采购数量" class="form-control int product_quantity" /></td>';
    html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][price]" value="" placeholder="采购价格" class="form-control int purchase_price price" /></td>';
    html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][real_cost]" value="" placeholder="真实成本" class="form-control int real_cost cost" /></td>';
    html += '  <td class="text-left"><div class="supplier_unit_size"></div></td>';
    html += '  <td class="text-left"><div class="inv_size"></div></td>';
    html += '  <td class="text-left"><div class="supplier_order_quantity_type_name"></div><input type="hidden" class="supplier_order_quantity_type" value="0" /></td>';
    html += '  <td class="text-left"></td>';
    html += '  <td class="text-left"></td>';
    html += '  <td class="text-left"></td>';
    html += '  <td class="text-left"><div class="rowName"></div></td>';
    html += '  <td class="text-left"><div class="rowOriInv"></div></td>';
    html += '  <td class="text-left"><div class="rowCurrInv"></div></td>';
    html += '  <td class="text-left"><button type="button" onclick="removeRow(' + adjust_row + ');" data-toggle="tooltip" title="删除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';

    $('#adjust tbody').append(html);
    adjust_row++;
}

<?php if(sizeof($pre_purchase_product) == 0){ ?>
    addAdjust();
<?php } ?>

function setWarehouseId(){
    $('input[name="warehouse_id"]').val($('select[name="select_warehouse_id"]').val());
}

function submitAdjust(){
    var trs = $('#adjust tbody tr').length;
    if(trs < 1){
        alert('请先添加.');
        return false;
    }

    var valid = true;
    var err_product_id = 0;
    $('#adjust tbody input.int').each(function(index, element){
        var val = parseInt($(element).val());
        if(!val && val != 0){
            valid = false;
            err_product_id = $(element).attr("product_id");
            return false;
        }
    });

    if(!valid){
        alert(err_product_id + '采购数量/采购价格/供应商采购数量 不能为空，请检查!');
        return false;
    }

    if($('input[name="warehouse_id"]').val() == 0){
        alert("请选择区域仓库");
        return false;
    }

    var date_deliver = $("#input-date-deliver").val();
    if(date_deliver == ''){
        alert("请选择到货日期");return false;
    }

    var has_invoice_flag = false;
    var radios=document.getElementsByName("invoice_flag");
    for(var i=0;i<radios.length;i++)
    {
        if(radios[i].checked==true)
        {
            has_invoice_flag = true;
        }
    }
    if(has_invoice_flag == false){
        alert("请选择是否有发票");return false;
    }
//    if($('input[name="warehouse_id"]) == 0){
//        //console.log($('input[name="warehouse_id"]));
//    }


    if(window.confirm('确认提交采购订单？')){
        $('#form-product-adjust').submit();
    }
}

function getPrePurchaseProduct(){
    if(window.confirm('将重载页面，覆盖现有数据，无法还原，继续吗？')){
        $("#preload").val(1);
        $("#pre_purchase_alert").val(0);
        submitSelect();
    }
}

function getPrePurchaseProductByAlert(){
    if(window.confirm('将重载页面，覆盖现有数据，无法还原，继续吗？')){
        $("#preload").val(0);
        $("#pre_purchase_alert").val(1);

        $("#input-date-before").val('');
        $("#input-date-end").val('');
        $("#input_date_no_in").val('');

        submitAlertSelect();
    }
}

function removePrePurchaseProduct(){
    if(window.confirm('将删除现有数据，无法还原，继续吗？')){
        if(window.confirm('再想一下，将删除现有数据，无法还原，继续吗？')){
            $('#adjust tbody').html('');
            adjust_row = 0;
            addAdjust();
        }
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

    var date_deliver = $("#input-date-deliver").val();
    if(date_deliver == ''){
        alert("请选择到货日期！");
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

function submitAlertSelect(){
    var date_deliver = $("#input-date-deliver").val();
    if(date_deliver == ''){
        alert("请选择到货日期！");
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

function removeRow(rowId){
//        productList = [];
//        $.each($('#adjust tr'), function(i,v){
//            console.log($(this).attr('id'));
//            var rowParentId = $(this).attr('id');
//            var product_id = parseInt($('#'+rowParentId+' .product_id').val());
//
//            productList.push(product_id);
//        });
    $('#adjust-row'+rowId).remove();
}

function getProductInv(product_id,obj){
    var supplier_id = parseInt($("#input-supplier-type").val());
    if(!supplier_id>0){
        alert('请先选择供应商');
        return false;
    }

    var rowId = obj.parent().parent().attr('id');
    //rowStation
    //rowName
    //rowOriInv
    //rowCurrInv

    productList = [];
    $.each($('#adjust tr'), function(i,v){
        var rowParentId = $(this).attr('id');
        if(rowParentId !== undefined && rowParentId !== rowId){
            var itemId = parseInt($('#'+rowParentId+' .product_id').val());
            productList.push(itemId);
        }
    });
    console.log(productList);

    var product_id = parseInt(product_id);
    if(productList.indexOf(product_id) >= 0){
        alert('商品编号[' + product_id + ']已存在。');
        var focusId= 'adjust-row'+productList.indexOf(product_id);
        $('#'+focusId+' .product_id').focus();
    }
    else{
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
                    var product_name = response.name;
                    var supplierId = parseInt(response.supplier_id);
                    if(supplier_id !== supplierId){
                        alert("商品["+product_id+"]["+product_name+"], 不属于此供应商，请检查原料及供应商信息");
                        return false;
                    }


                    $('#'+rowId+' .purchase_price').val(response.purchase_price);
                    $('#'+rowId+' .real_cost').val(response.real_cost);
                    $('#'+rowId+' .supplier_unit_size').html(response.supplier_unit_size);
                    $('#'+rowId+' .inv_size').html(response.inv_size);

                    if(response.supplier_order_quantity_type == '1'){
                        $('#'+rowId+' .supplier_order_quantity_type_name').html("按箱");
                    }
                    else if(response.supplier_order_quantity_type == '2'){
                        $('#'+rowId+' .supplier_order_quantity_type_name').html("按件");
                        //$('#'+rowId+' .product_quantity').focus();
                        //$('#'+rowId+' .supplier_quantity').css('background-color','#f4c63f');
                    }
                    $('#'+rowId+' .supplier_quantity').focus();

                    $('#'+rowId+' .supplier_order_quantity_type').val(response.supplier_order_quantity_type);

                    $('#'+rowId+' .rowName').html(response.name + " [¥"+response.sale_price+"]");
                    $('#'+rowId+' .rowOriInv').html(response.ori_inv);
                    $('#'+rowId+' .rowCurrInv').html(response.curr_inv);

                    $('#'+rowId+' .product_id').attr("readonly","readonly");
                }
                else{

                }
            }
        });
    }
}

//Auto change purhcase quantity
function updateQuantity(rowId){
    //var rowId = obj.parent().parent().attr('id');
    var supplier_order_quantity_type = parseInt($('#'+rowId+' .supplier_order_quantity_type').val());
    var product_quantity = 0;
    var supplier_quantity = 0;

    //按箱采购的商品：入库规格数量=供应商规格数量*供应商规格/商品规格
    if(supplier_order_quantity_type == 1){
        product_quantity = parseInt($('#'+rowId+' .supplier_quantity').val())
                            * parseInt($('#'+rowId+' .supplier_unit_size').html())
                            / parseInt($('#'+rowId+' .inv_size').html());
        $('#'+rowId+' .product_quantity').val(parseInt(product_quantity));
    }

    //按件采购的商品：供应商规格数量=入库规格数量x商品规格
    if(supplier_order_quantity_type == 2){
        product_quantity = parseInt($('#'+rowId+' .supplier_quantity').val()) / parseInt($('#'+rowId+' .inv_size').html());
        $('#'+rowId+' .product_quantity').val(parseInt(product_quantity));
    }
}

function showExcel(){
    $('#excel-show').toggle();
}

function writeToTable(){
    removeRow(0);
    var formData = new FormData($('#form-product-excel')[0]);
//    console.log(formData);
    $.ajax({
        type: 'POST',
        async: false,
        cache: false,
        url: 'index.php?route=purchase/pre_purchase/readExcel&token=<?php echo $_SESSION['token']; ?>',
        data: formData,
        contentType: false,
        processData: false,
        dataType:'json',
        success:function(response){
            console.log(response);
            $.each(response,function(i,v){
                html  = '<tr id="adjust-row' + adjust_row + '">';
                html += '  <td class="text-center"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][product_id]" value="'+ v.product_id+'" placeholder="商品ID" class="form-control int product_id" onChange="getProductInv($(this).val(), $(this));" /></td>';
                html += '  <td class="text-left"></td>';
                html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][supplier_quantity]" value="'+ v.supplier_quantity+'" title="供应商采购数量" placeholder="供应商采购数量" class="form-control int supplier_quantity" onChange="updateQuantity($(this).parent().parent().attr(\'id\'));" /></td>';
                html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][quantity]" value="'+ v.product_quantity+'" title="按供应商规格计算后采购数量" placeholder="按供应商规格计算后采购数量" class="form-control int product_quantity" /></td>';
                html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][price]" value="'+ v.product_price+'" placeholder="采购价格" class="form-control int purchase_price price" readonly/></td>';
                html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][real_cost]" value="'+ v.product_price+'" placeholder="真实成本" class="form-control int real_cost cost" /></td>';
                html += '  <td class="text-left"><div class="supplier_unit_size"></div></td>';
                html += '  <td class="text-left"><div class="inv_size"></div></td>';
                html += '  <td class="text-left"><div class="supplier_order_quantity_type_name"></div><input type="hidden" class="supplier_order_quantity_type" value="0" /></td>';
                html += '  <td class="text-left"></td>';
                html += '  <td class="text-left"></td>';
                html += '  <td class="text-left"></td>';
                html += '  <td class="text-left"><div class="rowName">'+ v.product_name+'</div></td>';
                html += '  <td class="text-left"><div class="rowOriInv"></div></td>';
                html += '  <td class="text-left"><div class="rowCurrInv"></div></td>';
                html += '  <td class="text-left"><button type="button" onclick="removeRow(' + adjust_row + ');" data-toggle="tooltip" title="删除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
                html += '</tr>';

                adjust_row++;

                $('#adjust tbody').append(html);
            });
        }
    });
}

//对生成的结果自动计算入库数量
$(document).ready(function () {
    $.each($('#adjust tbody tr'), function(n,v){
        var row = $(v).attr('id');
        updateQuantity(row);
    });
});

</script>
<script type="text/javascript">
    <!--
    $('.date').datetimepicker({
        pickTime: false
    });
    //-->
</script>
<?php echo $footer; ?> 