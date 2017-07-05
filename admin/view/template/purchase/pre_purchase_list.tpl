<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
          <button type="submit" id="buttton-excel" form="form-order" formaction="<?php echo$checkout_excel; ?>" data-toggle="tooltip" title="导出订单" class="btn btn-info"><i class="fa fa-file-excel-o"></i></button>
          <button type="submit" id="button-shipping" form="form-order" formaction="<?php echo $checkout_table; ?>" data-toggle="tooltip" title="打印付款单" class="btn btn-info"><i class="fa fa-truck"></i></button>
        <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            
            <div class="col-sm-4">
                
                
              <div class="form-group">
                <label class="control-label" for="input-order-type">类型</label>
                <select name="filter_order_type" id="input-order-type" class="form-control">
                  <option value="*"></option>
                  
                  <?php foreach ($order_types as $order_type) { ?>
                  <?php if ($order_type['order_type_id'] == $filter_order_type) { ?>
                  <option value="<?php echo $order_type['order_type_id']; ?>" selected="selected"><?php echo $order_type['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_type['order_type_id']; ?>"><?php echo $order_type['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>   
                
                
                
                
              <div class="form-group">
                <label class="control-label" for="input-purchase-order-id">采购单ID</label>
                
                 <input type="text" name="filter_purchase_order_id" value="<?php echo $filter_purchase_order_id; ?>" placeholder="采购单ID" id="input-purchase-order-id" class="form-control" /> 
              </div>
              
             <div class="form-group">
                <label class="control-label" for="input-supplier-type">供应商</label>
                <select name="filter_supplier_type" id="input-supplier-type" class="form-control">
                  <option value="*"></option>
                  
                  <?php foreach ($supplier_types as $supplier_type) { ?>
                  <?php if ($supplier_type['supplier_id'] == $filter_supplier_type) { ?>
                  <option value="<?php echo $supplier_type['supplier_id']; ?>" selected="selected"><?php echo $supplier_type['supplier_id'] .'#'. $supplier_type['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $supplier_type['supplier_id']; ?>"><?php echo $supplier_type['supplier_id'] .'#'. $supplier_type['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              
                
                
                
                
            </div>
            <div class="col-sm-4">
              
              
                <div class="form-group">
                    <label class="control-label" for="input_date_deliver">到货日期开始</label>
                    <div class="input-group date">
                        <input type="text" name="filter_date_deliver" value="<?php echo $filter_date_deliver; ?>" placeholder="到货日期开始" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
                    
                </div>
              
                <div class="form-group">
                    <label class="control-label" for="input_date_deliver_end">到货日期结束</label>
                    <div class="input-group date">
                        <input type="text" name="filter_date_deliver_end" value="<?php echo $filter_date_deliver_end; ?>" placeholder="到货日期结束" data-date-format="YYYY-MM-DD" id="input-date-modified-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
            </div>
              
                </div>

                <div class="form-group">
                    <label class="control-label" for="input_purchase_person_id">采购人员</label>
                    <select name="filter_purchase_person_id" id="input-purchase-person-id" class="form-control">
                        <option value="*"></option>

                        <?php foreach ($purchase_person as $purchase_person) { ?>
                        <?php if ($purchase_person['user_id'] == $filter_purchase_person_id) { ?>
                        <option value="<?php echo $purchase_person['user_id']; ?>" selected="selected"><?php echo $purchase_person['user_id'] .'#'. $purchase_person['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $purchase_person['user_id']; ?>"><?php echo $purchase_person['user_id'] .'#'. $purchase_person['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
              
            </div>
              
              
              
              
              
              <div class="col-sm-4">
                  <div class="form-group">
                <label class="control-label" for="input-order-status">订单状态</label>
                <select name="filter_order_status_id" id="input-order-status" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
                  <div class="form-group">
                <label class="control-label" for="input-order-checkout-status">订单支付状态</label>
                <select name="filter_order_checkout_status_id" id="input-order-checkout-status" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($order_checkout_statuses as $order_checkout_status) { ?>
                  <?php if ($order_checkout_status['order_checkout_status_id'] == $filter_order_checkout_status_id) { ?>
                  <option value="<?php echo $order_checkout_status['order_checkout_status_id']; ?>" selected="selected"><?php echo $order_checkout_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_checkout_status['order_checkout_status_id']; ?>"><?php echo $order_checkout_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
                  <input type="hidden" name="filter_warehouse_id_global" value="<?php echo $filter_warehouse_id_global; ?>">
                  <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              </div>
          </div>
        </div>
        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                    <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                <td class="text-right"><?php if ($sort == 'o.purchase_order_id') { ?>
                  <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                  <?php } ?></td>

                <td class="text-left">供应商分类</td>
                <td class="text-left">商品属性</td>
                <td class="text-left">订单总额</td>
                <td class="text-left">收货总额</td>
                <td class="text-left">添加人</td>
                <td class="text-left">添加时间</td>
                <td class="text-left">订单状态</td>
                <td class="text-left">付款状态</td>
                <td class="text-left">发票信息</td>
                <td class="text-right"><?php if ($sort == 'o.date_deliver') { ?>
                  <a href="<?php echo $sort_date_deliver; ?>" class="<?php echo strtolower($order); ?>">实际到货日期</a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_date_deliver; ?>">到货日期</a>
                  <?php } ?></td>  
                
                  
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($orders) { ?>
                <?php foreach ($orders as $order) { ?>
                
                <?php
                    $cancelled = '';
                    if($order['status'] == 3){
                        $cancelled = "cancelled_order";
                        $lock = true;
                    }
                ?>
                  
                <tr class="<?=$cancelled; ?>">
                  <td class="text-center">
                      
                    <?php if($order['order_type'] == 1){ ?>
                    <?php if (in_array($order['purchase_order_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $order['purchase_order_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $order['purchase_order_id']; ?>" />
                    <?php } ?>
                    <?php  } ?>
                    
                    </td>
                  <td class="text-right" style="font-size: 130%;text-align: center;">
                      <?php if($order['order_type'] == 2){ ?>
                      <span style="color:red;">退</span><br>
                      <?php  } ?>
                      <?php if($order['adjust_type'] == 1){ ?>
                      <span style="color:#33CC33;">调</span><br>
                      <?php  } ?>
                      <b><?php echo $order['purchase_order_id']; ?></b>
                      
                  </td>
                  
                 <td class="text-left">
                      <?php echo $order['supplier_type_name'].'<br />[#'.$order['supplier_type'].']'; ?>
                      
                  </td>
                  <td class="text-left">
                      <?php echo $order_stations[$order['station_id']]; ?>
                      <br>
                      [ <?php echo $order['warehouse']; ?> ]
                  </td>
                  
                  <td class="text-left">
                      <?php echo $order['order_total']; ?>
                      
                  </td>
                  <td class="text-left">
                      <?php echo $order['get_total']; ?>
                      
                  </td>
                  
                  
                  <td class="text-left">
                      <?php echo $order['add_user_name']; ?>
                      
                  </td>
                  <td class="text-left">
                      <?php echo $order['date_added']; ?>
                      
                  </td>
                  
                  <td class="text-left">
                      <?php echo $order['status_name']; ?>
                      
                  </td>
                  <td class="text-left">
                      
                      
                      
                      小计：<?php echo $order['order_total'];?><br>
                      余额支付：<?php echo $order['use_credits_total'];?><br>
                      缺货：<?php echo $order['quehuo_credits'];?><br>
                      退货：<?php echo $order['order_return'];?><br>
                      订单总额：<?php
                      
                      if($order['checkout_status'] == 2){
                        $order['order_due'] = $order['order_total'] - $order['use_credits_total'];
                      }
                      else{
                        $order['order_due'] = $order['order_total'] - $order['use_credits_total'] - $order['quehuo_credits'] - $order['order_return'];
                      }
                      echo $order['order_due'] < 0 ? 0 : $order['order_due'];
                      
                      
                      ?><br>
                      <?php echo $order['checkout_status'] == 2 ? "已付款" : "未付"; ?>
                      
                      <?php if($order['checkout_type_id'] == 3){ ?>
                      <span style="color:red;">预</span>
                      <?php  } ?>
                      
                      <?php if($order['checkout_status'] == 1 && $order['status'] != 3 && in_array($user_group_id,array(1,26))){ ?>
                      <span id="handle_checkout_status_<?php echo $order['purchase_order_id'];?>">
                        <button checkout_ope = "2" order_id="<?php echo $order['purchase_order_id'];?>"  data-loading-text="加载中..." class="btn btn-primary button-sub-checkout" id="button-2-checkout-<?php echo $order['purchase_order_id'];?>"><i class="fa fa-plus-circle"></i> 已支付</button>
                      </span>
                      <?php } ?>
                      
                  </td>
                  <td class="text-center">
                      <?php if( $order['invoice_flag'] == 1 ) { ?>
                      有无发票：<span style="background-color: #33CC33; color: #ffffff; padding:3px;">是</span><br>
                      <?php if($order['invoice_provided'] == 0){ ?>
                      <?php if($order['status'] != 3 && in_array($user_group_id,array(1,26))){ ?>
                      <span id="handle_invoice_<?php echo $order['purchase_order_id'];?>">
                        <button type="button" order_id="<?php echo $order['purchase_order_id'];?>"  data-loading-text="加载中..." class="btn btn-primary button-invoice-set" id="button-invoice-<?php echo $order['purchase_order_id'];?>"><i class="fa fa-plus-circle"></i> 已提供发票</button>
                      </span>
                      <?php } ?>
                      已提供发票：<span style="background-color: #33CC33; color: #ffffff; padding:3px;display: none" id="invoice_provided_<?php echo $order['purchase_order_id'];?>">是</span>
                      <?php }else{ ?>
                      已提供发票：<span style="background-color: #33CC33; color: #ffffff; padding:3px;display: block" id="invoice_provided_<?php echo $order['purchase_order_id'];?>">是</span>
                      <?php } ?>
                      <?php }else{ ?>
                      <span style="background-color: #cc0000; color: #FFFF00; padding:3px;">否</span>;
                      <?php } ?>
                  </td>
                  <td class="text-left">
                      计划到货日期:<br><?php echo $order['date_deliver_plan']; ?><br>
                      实际到货日期:<br><?php echo $order['date_deliver']; ?>
                      
                  </td>
                  
                 
                   
                  
                 
                  
                  <td class="text-right">
                      <?php if($modifyPermission){ ?>
                          <?php if($order['status'] == 1 ){ ?>
                          <span id="handle_order_status_<?php echo $order['purchase_order_id'];?>">
                            <button order_ope = "2" order_id="<?php echo $order['purchase_order_id'];?>"  data-loading-text="加载中..." class="btn btn-primary button-sub-order" id="button-2-order-<?php echo $order['purchase_order_id'];?>"><i class="fa fa-plus-circle"></i> 确认</button>
                                <button order_ope = "3" order_id="<?php echo $order['purchase_order_id'];?>" data-loading-text="加载中..." class="btn btn-primary button-can-order"  id="button-3-order-<?php echo $order['purchase_order_id'];?>"><i class="fa fa-plus-circle"></i> 取消</button>
                          </span>
                          <?php } ?>


                          <?php if($order['status'] == 2 && $order['order_type'] == 1){ ?>
                          <span id="handle_order_status_<?php echo $order['purchase_order_id'];?>">
                            <button order_ope = "6" order_id="<?php echo $order['purchase_order_id'];?>"  data-loading-text="加载中..." class="btn btn-primary button-sub-order" id="button-6-order-<?php echo $order['purchase_order_id'];?>"><i class="fa fa-plus-circle"></i> 供应商未送</button>
                              <button order_ope = "3" order_id="<?php echo $order['purchase_order_id'];?>" data-loading-text="加载中..." class="btn btn-primary button-can-order"  id="button-3-order-<?php echo $order['purchase_order_id'];?>"><i class="fa fa-plus-circle"></i> 取消</button>
                          </span>
                          <?php } ?>

                          <br />
                          <a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                          <?php if($order['order_type'] == 1){ ?>
                          <a href="<?php echo $order['copy_order']; ?>" data-toggle="tooltip" title="复制采购单" class="btn btn-info"><i class="fa fa-copy"></i></a>
                          <?php } ?>
                          <?php if($order['status'] == 5){ ?>
                          <a href="<?php echo $order['add_return']; ?>" data-toggle="tooltip" title="添加退货单" class="btn btn-info"><i class="fa fa-minus-circle"></i></a>
                          <?php } ?>

                      <?php } else{ ?>
                          <a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                      <?php } ?>
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="13"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
    var url = 'index.php?route=purchase/pre_purchase&token=<?php echo $token; ?>';

    //Fix url with all filters.
    url += fixUrl(2);
    location = url;
});

$('.order_bd_change').on('change', function() {
    processOrder('order_bd',$(this).attr("order_id"),$(this).val());
});


$('.button-invoice-set').on('click',function(){
    var order_id = $(this).attr("order_id");
    if(confirm("确认操作？")){
        $.ajax({
            url: 'index.php?route=purchase/pre_purchase/update_invoice&token=<?php echo $token; ?>',
            type: 'post',
            data: {order_id : order_id},
            dataType: 'json',
            beforeSend: function() {
                $("#button-invoice-" + order_id).button('loading');
            },
            complete: function() {
                $("#button-invoice-" + order_id).button('reset');
            },
            success:function(json){
                var jsonData = json;

                if(jsonData.success == 0){
                    alert("不能重复确认支付,请刷新页面");
                    return false;
                }
                $("#handle_invoice_" + order_id).hide();
                $("#invoice_provided_" + order_id).show();
                alert(jsonData.return_msg);
            }
        });
    }

    return false;
});


$('.button-sub-checkout').on('click', function() {

    var order_id = $(this).attr("order_id");
    var checkout_ope = $(this).attr("checkout_ope");


    if(confirm("确认操作？")){
        $.ajax({
                url: 'index.php?route=purchase/pre_purchase/update_checkout_status&token=<?php echo $token; ?>',
                type: 'post',
                data: {order_id : order_id, checkout_ope : checkout_ope},
                dataType: 'json',
                beforeSend: function() {
                        $("#button-" + checkout_ope + "-checkout-" + order_id).button('loading');


                },
                complete: function() {

                       $("#button-" + checkout_ope + "-checkout-" + order_id).button('reset');
                },		
                success: function(json) {
                        var jsonData = json;

                        if(jsonData.success == 0){
                            alert("不能重复确认支付,请刷新页面");
                            return false;
                        }
                        $("#handle_checkout_status_" + order_id).hide();

                        alert(jsonData.return_msg);
                },			
                error: function(xhr, ajaxOptions, thrownError) {
                }
        });
    }

    return false;
})








$('.button-sub-order,.button-can-order').on('click', function() {
    
    var order_id = $(this).attr("order_id");
    var order_ope = $(this).attr("order_ope");
    
    if(confirm("确认操作？")){
        $.ajax({
                url: 'index.php?route=purchase/pre_purchase/update_order&token=<?php echo $token; ?>',
                type: 'post',
                data: {order_id : order_id, order_ope : order_ope},
                dataType: 'json',
                beforeSend: function() {
                        $("#button-" + order_ope + "-order-" + order_id).button('loading');


                },
                complete: function() {
                        
                        $("#button-" + order_ope + "-order-" + order_id).button('reset');
                },		
                success: function(json) {
                        var jsonData = json;
                        
                        if(jsonData.success == 0){
                            alert("不能重复确认或取消采购单,请刷新页面");
                            return false;
                        }
                        $("#handle_order_status_" + order_id).hide();

                        alert(jsonData.return_msg);
                },			
                error: function(xhr, ajaxOptions, thrownError) {
                }
        });
    }
    
    
    
    
    return false;
})






































function download(type){
    var deliver_date = $('input[name=\'filter_deliver_date\']').val();

    if(!deliver_date){
        alert('请选择配送日期。');
        exit();
    }

    var filter_customer_group = $('select[name=\'filter_customer_group\']').val();
    var filter_station = $('select[name=\'filter_station\']').val();

    if(!type){
    //PO for Purchase Order, PSR for Pickup-spot Receipt
        alert('请选择指定下载表单类型。');
        exit();
    }

    var url = 'index.php?route=sale/order/download&token=<?php echo $token; ?>';
    url += '&deliver_date=' + encodeURIComponent(deliver_date);
    url += '&type=' + encodeURIComponent(type);

    if(filter_customer_group > 0){
        url += '&filter_customer_group=' + encodeURIComponent(filter_customer_group);
    }
    
    if(filter_station > 0){
        url += '&filter_station=' + encodeURIComponent(filter_station);
    }

    window.open(url,"_blank");
    //window.open(url,name,features);
}

//Add extra order operations
function processOrder(method,order_id,status_id=false){
    var bool;
    switch(method)
    {
        case 'orderStatus':
            if(status_id == 2){
                bool = confirm('将订单['+order_id+']改为［已确认］?');
            }
            else if(status_id == 3){
                bool = confirm('取消订单['+order_id+']，订单取消后无法再修改?');
            }
            break;
        case 'deliver':
            bool = confirm('确认配送订单['+order_id+']?');
            break;
        case 'payment':
            bool = confirm('确认订单['+order_id+']已支付?');
            break;
        case 'order_print_status':
           bool = confirm('确认订单['+order_id+']已打印?');
           break;
        case 'order_bd':
           bool = confirm('确认修改订单['+order_id+']和用户所属的BD?');
           break;
        case 'customer_group_id':
           bool = confirm('确认修改订单['+order_id+']和用户的商品价签?');
           break;
        default:
            break;
    }

    if(!bool){
        exit();//取消操作退出
    }

    var url = 'index.php?route=sale/order/'+method+'&token=<?php echo $token; ?>';
    if (order_id) {
        url += '&order_id=' + encodeURIComponent(order_id);
        if(status_id){
            url += '&status_id=' + encodeURIComponent(status_id);
        }
    }

    //Fix url with all filters.
    url += fixUrl(1);

    location = url;
}

function fixUrl(type){
    var url ='';

    var filter_warehouse_id_global = $('input[name=\'filter_warehouse_id_global\']').val();
    if(filter_warehouse_id_global && filter_warehouse_id_global != '') {
        url += '&filter_warehouse_id_global='+ encodeURIComponent(filter_warehouse_id_global);
    }

    var filter_purchase_order_id = $('input[name=\'filter_purchase_order_id\']').val();
    if (filter_purchase_order_id && filter_purchase_order_id != '') {
        url += '&filter_purchase_order_id=' + encodeURIComponent(filter_purchase_order_id);
    }

    var filter_supplier_type = $('select[name=\'filter_supplier_type\']').val();
    if (filter_supplier_type && filter_supplier_type != '*') {
        url += '&filter_supplier_type=' + encodeURIComponent(filter_supplier_type);
    }
    
    var filter_order_type = $('select[name=\'filter_order_type\']').val();
    if (filter_order_type && filter_order_type != '*') {
        url += '&filter_order_type=' + encodeURIComponent(filter_order_type);
    }
    
    var filter_date_deliver = $('input[name=\'filter_date_deliver\']').val();
    if (filter_date_deliver) {
        url += '&filter_date_deliver=' + encodeURIComponent(filter_date_deliver);
    }

    var filter_date_deliver_end = $('input[name=\'filter_date_deliver_end\']').val();
    if (filter_date_deliver_end) {
        url += '&filter_date_deliver_end=' + encodeURIComponent(filter_date_deliver_end);
    }

    var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();
    if (filter_order_status_id && filter_order_status_id != '*') {
        url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
    }

    var filter_order_checkout_status_id = $('select[name=\'filter_order_checkout_status_id\']').val();
    if (filter_order_checkout_status_id && filter_order_checkout_status_id != '*') {
        url += '&filter_order_checkout_status_id=' + encodeURIComponent(filter_order_checkout_status_id);
    }

    var filter_purchase_person_id = $('select[name=\'filter_purchase_person_id\']').val();
    if (filter_purchase_person_id && filter_purchase_person_id != '*') {
        url += '&filter_purchase_person_id=' + encodeURIComponent(filter_purchase_person_id);
    }

    if(type == 1){
        var filter_page = <?php echo isset($_GET['page']) ? $_GET['page'] : 0; ?>;
        if (filter_page > 0) {
            url += '&page=' + encodeURIComponent(filter_page);
        }
    }

    return url;
}

//--></script> 
  <script type="text/javascript"><!--
$('input[name=\'filter_customer\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',            
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['customer_id']
                    }
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'filter_customer\']').val(item['label']);
    }    
});
//--></script> 
  <script type="text/javascript"><!--
$('input[name^=\'selected\']').on('change', function() {
    
    $('#button-shipping').prop('disabled', false);
    $('#button-excel').prop('disabled',false);
    
    var selected = $('input[name^=\'selected\']:checked');
    
    
            
    
});


//--></script> 
  <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
  <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
  <script type="text/javascript"><!--
$('.date').datetimepicker({
    pickTime: false
});
//--></script></div>
<?php echo $footer; ?>