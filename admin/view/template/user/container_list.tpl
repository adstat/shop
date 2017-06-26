<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
        <!--
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-customer').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
        -->
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
        <h3 class="panel-title"><i class="fa fa-list"></i> 周转筐列表</h3>
      </div>
        <?php if(!empty($container_no_move_order)){ ?>
        <h5 style="color:red;">今日未确认到店订单号：<?php echo $container_no_move_order;?></h5>
        <?php } ?>
      <div class="panel-body">
        <div class="well">
          <div class="row">
              
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-container-id">框号</label>
                <input type="text" name="filter_container_id" value="<?php echo $filter_container_id; ?>" placeholder="框号" id="input-container-id" class="form-control" />
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
                <label class="control-label" for="input-container-instore">是否在仓库</label>
                <select name="filter_container_instore" id="input-container-instore" class="form-control">
                  <option value="*" <?php if(is_null($filter_container_instore)){ echo 'selected="selected"'; } ?>></option>
                  
                  <option value="1" <?php if($filter_container_instore == 1){ echo 'selected="selected"'; } ?>>在仓库</option>
                  
                  <option value="0" <?php if($filter_container_instore == 0 && !is_null($filter_container_instore)){ echo 'selected="selected"'; } ?>>在商家</option>
                  
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-container-status">是否可用</label>
                <select name="filter_container_status" id="input-container-status" class="form-control">
                    <option value="*" <?php if(is_null($filter_container_status)){ echo 'selected="selected"'; } ?>></option>
                  
                  <option value="1" <?php if($filter_container_status == 1){ echo 'selected="selected"'; } ?>>可用</option>
                  
                  <option value="0" <?php if($filter_container_status == 0 && !is_null($filter_container_status)){ echo 'selected="selected"'; } ?>>废弃</option>
                  
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
                <label class="control-label" for="input-container-indate">入库日期</label>
                <select name="filter_container_indate" id="input-container-indate" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($container_indate_arr as $container_indate) { ?>
                  <?php if ($container_indate == $filter_container_indate) { ?>
                  <option value="<?php echo $container_indate;?>" selected="selected"><?php echo $container_indate; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $container_indate; ?>"><?php echo $container_indate; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
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
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'container_id') { ?>
                    <a href="<?php echo $sort_container_id; ?>" class="<?php echo strtolower($order); ?>">框号</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_container_id; ?>">框号</a>
                    <?php } ?></td>
                  
                    
                  <td>类型</td>
                  <td>是否在仓库</td>
                  <td>状态</td>
                  
                  
                  
                  
                  
                  
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($containers) { ?>
                <?php foreach ($containers as $container) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($container['container_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $container['container_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $container['container_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $container['container_id']; ?></td>
                  <td class="text-left"><?php echo $container['type_name']; ?></td>
                  <td class="text-left"><?php echo $container['instore']; ?></td>
                  <td class="text-left"><?php echo $container['status']; ?></td>
                  <td class="text-right">
                    
                    <a href="<?php echo $container['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
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
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=user/container&token=<?php echo $token; ?>';
	
	var filter_container_id = $('input[name=\'filter_container_id\']').val();
	
	if (filter_container_id) {
		url += '&filter_container_id=' + encodeURIComponent(filter_container_id);
	}
	
	
	
	var filter_container_type = $('select[name=\'filter_container_type\']').val();
	
	if (filter_container_type != '*') {
		url += '&filter_container_type=' + encodeURIComponent(filter_container_type);
	}	
	
        var filter_container_outdate = $('select[name=\'filter_container_outdate\']').val();
	
	if (filter_container_outdate != '*') {
		url += '&filter_container_outdate=' + encodeURIComponent(filter_container_outdate);
	}	
        
        
        var filter_container_indate = $('select[name=\'filter_container_indate\']').val();
	
	if (filter_container_indate != '*') {
		url += '&filter_container_indate=' + encodeURIComponent(filter_container_indate);
	}	
	
	var filter_container_instore = $('select[name=\'filter_container_instore\']').val();
	
	if (filter_container_instore != '*') {
		url += '&filter_container_instore=' + encodeURIComponent(filter_container_instore);
	}	
	
	var filter_container_status = $('select[name=\'filter_container_status\']').val();
	
	if (filter_container_status != '*') {
		url += '&filter_container_status=' + encodeURIComponent(filter_container_status);
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
