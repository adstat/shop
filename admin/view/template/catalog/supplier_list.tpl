<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-manufacturer').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name ; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="制造商/产地名称/编号" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_starttime; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_starttime; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-added"><?php echo $entry_added ; ?></label>
                <input type="text" name="filter_added" value="<?php echo $filter_added; ?>" placeholder="添加用户" id="input-added" class="form-control">
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_endtime; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_endtime; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
              <label class="control-label" for="input-manage"><?php echo $entry_manage ; ?></label>
              <input type="text" name="filter_manage" value="<?php echo $filter_manage;?>" placeholder="管理用户" id="input-manage" class="form-control">
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!$filter_status && !is_null($filter_status)) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>

          </div>
        </div>






        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-manufacturer">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>

                  <td class="text-left"><?php if ($sort =='supplier_id') { ?>
                    <a href="<?php echo $sort_supplier_id; ?>" class="<?php echo strtolower($order); ?>">编号</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_supplier_id; ?>">编号</a>
                    <?php } ?></td>

                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_added ;?></td>
                  <td class="text-left"><?php echo $column_usergroup ;?></td>
                  <td class="text-left"><?php echo $column_manage ;?></td>
                  <td class="text-left"><?php echo $column_date_added ;?></td>
                  <td class="text-right">
                    状态
                    </td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($manufacturers) { ?>
                <?php foreach ($manufacturers as $manufacturer) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($manufacturer['supplier_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $manufacturer['supplier_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $manufacturer['supplier_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $manufacturer['supplier_id']; ?></td>
                  <td class="text-left"><?php echo $manufacturer['name']; ?></td>
                  <td class="text-left"><?php echo $manufacturer['added_by']; ?></td>
                  <td class="text-left"><?php echo $manufacturer['usergroup']; ?></td>
                  <td class="text-left"><?php echo $manufacturer['username']; ?></td>
                  <td class="text-left"><?php echo $manufacturer['date_added']; ?></td>

                  <td class="text-right" <?php if($manufacturer['status_id']==0){ ?> style=" border: 1px dashed; background-color: #FF2222;" <?php } ?> ><?php echo $manufacturer['status']; ?></td>
                  <td class="text-right">
                      <?php if($manufacturer['edit']){ ?>
                        <a href="<?php echo $manufacturer['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                      <?php } ?>
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
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
</div>

<script type="text/javascript">
  $('#button-filter').on('click', function() {
    var url = 'index.php?route=catalog/supplier&token=<?php echo $token; ?>';
    var filter_name = $('input[name=\'filter_name\']').val();

    if (filter_name) {
      url += '&filter_name=' + encodeURIComponent(filter_name);
    }

    var filter_added = $('input[name=\'filter_added\']').val();

    if (filter_added) {
      url += '&filter_added=' + encodeURIComponent(filter_added);
    }

    var filter_manage = $('input[name=\'filter_manage\']').val();

    if (filter_manage) {
      url += '&filter_manage=' + encodeURIComponent(filter_manage);
    }

    var filter_status = $('select[name=\'filter_status\']').val();

    if (filter_status != '*') {
      url += '&filter_status=' + encodeURIComponent(filter_status);
    }

    var filter_date_start = $('input[name=\'filter_date_start\']').val();

    if (filter_date_start) {
      url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
    }

    var filter_date_end = $('input[name=\'filter_date_end\']').val();

    if (filter_date_end) {
      url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
    }

    location = url;


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