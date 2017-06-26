<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-manufacturer" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>原料供应商管理</h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-manufacturer" class="form-horizontal">
          
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab-general" data-toggle="tab">常规</a></li>
              <li><a href="#tab-transaction" data-toggle="tab">供应商余额</a></li>
            </ul>
          
            <div class="tab-content">
                <div class="tab-pane active" id="tab-general">  
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-contact-name"><span data-toggle="tooltip" title="联系人">联系人</span></label>
            <div class="col-sm-10">
              <input type="text" name="contact_name" value="<?php echo $contact_name; ?>" placeholder="联系人" id="input-contact-name" class="form-control" />
              
            </div>
          </div>
            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-contact-phone"><span data-toggle="tooltip" title="联系电话">联系电话</span></label>
            <div class="col-sm-10">
              <input type="text" name="contact_phone" value="<?php echo $contact_phone; ?>" placeholder="联系电话" id="input-contact-phone" class="form-control" />
              
            </div>
          </div>


            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-manage"><?php echo $entry_manage; ?></label>
                <div class="col-sm-8">
                    <select name="filter_manage_userid" id="input-manage-usergroup" class="form-control">
                        <option value="0">请选择</option>
                        <?php foreach ($manage_usergroups as $manage_usergroup) { ?>
                        <?php if ($manage_usergroup['user_id'] == $filter_manage_userid) { ?>
                        <option value="<?php echo $manage_usergroup['user_id']; ?>" selected="selected"><?php echo $manage_usergroup['name']; ?>/<?php echo $manage_usergroup['usergroup']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $manage_usergroup['user_id']; ?>"><?php echo $manage_usergroup['name']; ?>/<?php echo $manage_usergroup['usergroup']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>



                    <?php if ($error_name) { ?>
                    <div class="text-danger"><?php echo $error_name; ?></div>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group required" style="display: none">
                <label class="col-sm-2 control-label" for="input-date-added"><?php echo $entry_addedtime; ?></label>
                <div class="col-sm-8">
                <div class="input-group date">
                    <input type="text" name="date_added" value="<?php echo $date_added; ?>" placeholder="<?php echo $entry_name; ?>"data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                    <?php if ($error_name) { ?>
                    <div class="text-danger"><?php echo $error_name; ?></div>
                    <?php } ?>
                      <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
                </div>
            </div>




            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-memo"><span data-toggle="tooltip" title="备注">备注</span></label>
            <div class="col-sm-8">
              <input type="text" name="memo" value="<?php echo $memo; ?>" placeholder="备注" id="input-memo" class="form-control" />
              
            </div>
          </div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status">状态</label>
                <div class="col-sm-8">
                  <select name="status" id="input-status" class="form-control">
                    <?php if ($status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            
          <div class="form-group">
                <label class="col-sm-2 control-label" for="input-market-id"><span data-toggle="tooltip" title="(输入时自动筛选结果)">采购市场</span></label>
                <div class="col-sm-10">
                  <input type="text" name="market" value="<?php echo $market; ?>" placeholder="采购市场" id="input-market-id" class="form-control" />
                  <input type="hidden" name="market_id" value="<?php echo $market_id; ?>" />
                </div>
              </div>
            
          <div class="form-group">
                <label class="col-sm-2 control-label" for="input-checkout-type-id">结款类型</label>
                <div class="col-sm-10">
                  <select name="checkout_type_id" id="input-checkout-type-id" class="form-control">
                    <?php foreach ($supplier_checkout_type as $checkout_type) { ?>
                    <?php if ($checkout_type['checkout_type_id'] == $checkout_type_id) { ?>
                    <option value="<?php echo $checkout_type['checkout_type_id']; ?>" selected="selected"><?php echo $checkout_type['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $checkout_type['checkout_type_id']; ?>"><?php echo $checkout_type['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
          
            <div class="form-group">
                <label class="col-sm-2 control-label" for="input-checkout-cycle-id">结款周期类型</label>
                <div class="col-sm-10">
                  <select name="checkout_cycle_id" id="input-checkout-cycle-id" class="form-control">
                    <?php foreach ($supplier_checkout_cycle as $checkout_cycle) { ?>
                    <?php if ($checkout_cycle['checkout_cycle_id'] == $checkout_cycle_id) { ?>
                    <option value="<?php echo $checkout_cycle['checkout_cycle_id']; ?>" selected="selected"><?php echo $checkout_cycle['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $checkout_cycle['checkout_cycle_id']; ?>"><?php echo $checkout_cycle['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
            
            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-checkout-cycle-num"><span data-toggle="tooltip" title="结款周期">结款周期</span></label>
            <div class="col-sm-10">
              <input type="text" name="checkout_cycle_num" value="<?php echo $checkout_cycle_num; ?>" placeholder="结款周期" id="input-checkout-cycle-num" class="form-control" />
            
            </div>
            
            
            
            </div>

            <div class="form-group" style="display: none">
            <label class="col-sm-2 control-label" for="input-checkout-cycle-date"><span data-toggle="tooltip" title="结款预设日期">结款预设日期类型</label>
            <div class="col-sm-10">
              <input type="text" name="checkout_cycle_date" value="<?php echo $checkout_cycle_date; ?>" placeholder="结款预设日期" id="input-checkout-cycle-date" class="form-control" />
              <?php if ($error_date) { ?>
              <div class="text-danger"><?php echo $error_date; ?></div>
              <?php } ?>
            </div>
            </div>
            
            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-checkout-username"><span data-toggle="tooltip" title="收款人">收款人</span></label>
            <div class="col-sm-10">
              <input type="text" name="checkout_username" value="<?php echo $checkout_username; ?>" placeholder="收款人" id="input-checkout-username" class="form-control" />
              
            </div>
            
            
            
          </div>
            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-checkout-userbank"><span data-toggle="tooltip" title="收款银行">收款银行</span></label>
            <div class="col-sm-10">
              <input type="text" name="checkout_userbank" value="<?php echo $checkout_userbank; ?>" placeholder="收款银行" id="input-checkout-userbank" class="form-control" />
              
            </div>
            
            
            
          </div>
            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-checkout-usercard"><span data-toggle="tooltip" title="收款卡号">收款卡号</span></label>
            <div class="col-sm-10">
              <input type="text" name="checkout_usercard" value="<?php echo $checkout_usercard; ?>" placeholder="收款卡号" id="input-checkout-usercard" class="form-control" />
              
            </div>
            
            
            
          </div>
            
            
            
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-invoice-flag">是否有发票</label>
                        <div class="col-sm-10">
                          <select name="invoice_flag" id="input-invoice-flag" class="form-control">
                            <?php if ($invoice_flag) { ?>
                            <option value="1" selected="selected">是</option>
                            <option value="0">否</option>
                            <?php } else { ?>
                            <option value="1">是</option>
                            <option value="0" selected="selected">否</option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    
                </div>

            <div class="tab-pane" id="tab-transaction">
              <div id="transaction"></div>
            </div>














            </div>
        </form>
          
          
          
          
      </div>
    </div>
  </div>
    
   
    
    
</div>
<script type='text/javascript'>
      $('input[name=\'market\']').autocomplete({
    
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/supplier/market_autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				json.unshift({
					market_id: 0,
					name: ''
				});
				
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['market_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'market\']').val(item['label']);
		$('input[name=\'market_id\']').val(item['value']);
	}	
});
    



    
</script>



  <script type="text/javascript">
$('#transaction').delegate('.pagination a', 'click', function(e) {
   e.preventDefault();

   $('#transaction').load(this.href);
});
$('#transaction').load('index.php?route=catalog/supplier/transaction&token=<?php echo $token; ?>&supplier_id=<?php echo $supplier_id; ?>');

$('#button-transaction').on('click', function(e) {
    
  e.preventDefault();

  $.ajax({
		url: 'index.php?route=catalog/supplier/transaction&token=<?php echo $token; ?>&supplier_id=<?php echo $supplier_id; ?>',
		type: 'post',
		dataType: 'html',
		data: 'description=' + encodeURIComponent($('#tab-transaction input[name=\'description\']').val()) + '&amount=' + encodeURIComponent($('#tab-transaction input[name=\'amount\']').val()) + '&transaction_type=' + encodeURIComponent($('#tab-transaction select[name=\'transaction_type\']').val())+ '&transaction_order_id=' + encodeURIComponent($('#tab-transaction select[name=\'transaction_order_id\']').val()),
		beforeSend: function() {
			$('#button-transaction').button('loading');
		},
		complete: function() {
			$('#button-transaction').button('reset');
		},
		success: function(html) {
			$('.alert').remove();

			$('#transaction').html(html);

			$('#tab-transaction input[name=\'amount\']').val('');
			$('#tab-transaction input[name=\'description\']').val('');
		}
	});
});
</script>

<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript"><!--
    $('.date').datetimepicker({
        pickTime: false
    });
    //--></script>
<?php echo $footer; ?>