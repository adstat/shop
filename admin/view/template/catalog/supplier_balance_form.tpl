<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1>原料供应商余额明细管理</h1>
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
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-manufacturer" class="form-horizontal">
          
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab-transaction" data-toggle="tab">供应商余额</a></li>
            </ul>
          
            <div class="tab-content">
            <div class="tab-pane active" id="tab-transaction">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="">供应商</label>
                <div class="col-sm-10">
                  <?php echo $name; ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-transaction-description">描述</label>
                <div class="col-sm-10">
                  <input type="text" name="description" value="" placeholder="描述" id="input-transaction-description" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-amount">金额</label>
                <div class="col-sm-10">
                  <input type="text" name="amount" value="" placeholder="金额" id="input-amount" class="form-control" />
                </div>
              </div>
              
              
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-transaction-type">余额调整类型</label>
                <div class="col-sm-10">
                  <select name="transaction_type" id="input-transaction-type" class="form-control">
                    <?php foreach ($transaction_types as $transaction_type) { ?>
                        
                        <option value="<?php echo $transaction_type['customer_transaction_type_id']; ?>"><?php echo $transaction_type['name']; ?></option>
                        
                        
                        
                    <?php } ?>
                    
                  </select>
                </div>
              </div>
              
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-transaction-order-id">余额调整相关采购单</label>
                <div class="col-sm-10">
                  <select name="transaction_order_id" id="input-transaction-order-id" class="form-control">
                      <option value="0">无</option>
                    <?php foreach ($order_ids as $order_val) { ?>
                        
                        <option value="<?php echo $order_val; ?>"><?php echo $order_val; ?></option>
                        
                    <?php } ?>
                  </select>
                </div>
              </div>
              
              <?php if($add_transaction_flag){ ?>
              <div class="text-right">
                <button type="button" id="button-transaction" data-loading-text="加载中。。" class="btn btn-primary"><i class="fa fa-plus-circle"></i>添加交易记录</button>
              </div>
              <?php } ?>
              <br />
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