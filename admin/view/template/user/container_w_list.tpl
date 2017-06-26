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
        <h3 class="panel-title"><i class="fa fa-list"></i> 未还周转筐记录</h3>
      </div>
       
      <div class="panel-body">
        <div class="well">
          <div class="row">
              
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-container-days">未还天数</label>
                <input type="text" name="filter_container_days" value="<?php echo $filter_container_days; ?>" placeholder="超过几天没还" id="input-container-days" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-container-type">周转筐类型</label>
                <select name="filter_container_type" id="input-container-type" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($container_types as $container_type) { ?>
                  <?php if ($container_type['type_id'] == $filter_container_type) { ?>
                  <option value="<?php echo $container_type['type_id']; ?>" selected="selected"><?php echo $container_type['type_name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $container_type['type_id']; ?>"><?php echo $container_type['type_name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
                
                
            </div>
           
              
             <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-container-outdate">出库日期</label>
                <select name="filter_container_outdate" id="input-container-outdate" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($container_outdate_arr as $container_outdate) { ?>
                  <?php if ($container_outdate == $filter_container_outdate) { ?>
                  <option value="<?php echo $container_outdate;?>" selected="selected"><?php echo $container_outdate; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $container_outdate; ?>"><?php echo $container_outdate; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              
                 
               <div class="form-group">
                <label class="control-label" for="input-container-customer">会员</label>
                <input type="text" name="filter_container_customer" value="<?php echo $filter_container_customer; ?>" placeholder="会员" id="input-container-customer" class="form-control" />
              </div>   
                 
            </div> 
              
              
              
            <div class="col-sm-3">
              
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-customer">
          <div class="table-responsive">
              总框数：<?php echo $container_total_num;?>
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  
                  <td class="text-left">
                      <!--<?php if ($sort == 'cm.container_id') { ?>
                    <a href="<?php echo $sort_container_id; ?>" class="<?php echo strtolower($order); ?>">框号</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_container_id; ?>">框号</a>
                    
                    <?php } ?>-->
                      框号
                  </td>
                  
                    
                  <td>类型</td>
                  <td>
                      <!--<?php if ($sort == 'cu.customer_id' ) { ?>
                    <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>">会员</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_customer; ?>">会员</a>
                    
                    <?php } ?>-->
                      会员
                      </td>
                  
                  <td>商家店名</td>
                  <td>商家地址</td>
                  <td>总未还框数</td>
                  <!--<td>订单号</td>-->
                  <td>
                      <!--<?php if ($sort == 'cm.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>">送货日期</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>">送货日期</a>
                    
                    <?php } ?>-->
                      送货日期
                      </td>
                  
                  
                  
                  
                  
                  
                </tr>
              </thead>
              <tbody>
                <?php if ($containers) { ?>
                <?php foreach ($containers as $container) { ?>
                <tr>
                  
                  <td class="text-left"><?php echo $container['container_id']; ?></td>
                  <td class="text-left"><?php echo $container['type_name']; ?></td>
                  <td class="text-left"><a href="<?php echo $container['customer_url'];?>" target="_blank" ><?php echo $container['customer_name']; ?></a></td>
                  <td class="text-left"><?php echo $container['merchant_name']; ?></td>
                  <td class="text-left"><?php echo $container['merchant_address']; ?></td>
                  <td class="text-left"><?php echo $container['container_total']; ?></td>
                  <!--<td class="text-left"><?php echo $container['order_id']; ?></td>-->
                  <td class="text-left">
                    <?php echo $container['date_added']; ?>
                    
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <!--<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>-->
        </div>
      </div>
    </div>
  </div>
    
    
      <script type="text/javascript"><!--
$('input[name=\'filter_container_customer\']').autocomplete({
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
        $('input[name=\'filter_container_customer\']').val(item['label']);
    }    
});
//--></script> 
    
    
    
    
    
    
    
    
    
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=user/container_w&token=<?php echo $token; ?>';
	
	var filter_container_days = $('input[name=\'filter_container_days\']').val();
	
	if (filter_container_days) {
		url += '&filter_container_days=' + encodeURIComponent(filter_container_days);
	}
	
	
	
	var filter_container_type = $('select[name=\'filter_container_type\']').val();
	
	if (filter_container_type != '*') {
		url += '&filter_container_type=' + encodeURIComponent(filter_container_type);
	}	
        
        var filter_container_outdate = $('select[name=\'filter_container_outdate\']').val();
	
	if (filter_container_outdate != '*') {
		url += '&filter_container_outdate=' + encodeURIComponent(filter_container_outdate);
	}	
        
        
        var filter_container_customer = $('input[name=\'filter_container_customer\']').val();
	
	if (filter_container_customer) {
		url += '&filter_container_customer=' + encodeURIComponent(filter_container_customer);
	}	
	
	
	
	
	location = url;
});
//--></script> 

  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?> 
