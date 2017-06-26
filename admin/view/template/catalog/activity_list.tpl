<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
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
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_station; ?></label>
                <select name="filter_station" id="input-station" class="form-control">
                  <option value="*"></option>
                  <?php foreach($stations as $station) { ?>
                  <option value="<?php echo $station['station_id']; ?>" <?php if($filter_station == $station['station_id']){ ?>selected="selected"<?php } ?>><?php echo $station['station_name']; ?></option>
                  <?php } ?>
                </select>
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
            </div>
            <div class="col-sm-3">
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
                <label class="control-label" for="input-date-added"><?php echo $entry_endtime; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_endtime; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
               <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-center">分类编号</td>
                  <td class="text-center" style="display: none"><?php echo $column_image; ?></td>
                  <td class="text-center" >仓库平台</td>
                  <td class="text-left"><?php echo $column_name; ?></td>
                  <td class="text-left"><?php if ($sort == 'a.act_status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'a.date_start') { ?>
                    <a href="<?php echo $sort_starttime; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_starttime; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_starttime; ?>"><?php echo $column_starttime; ?></a>
                    <?php } ?></td> 
                  <td class="text-left"><?php if ($sort == 'a.date_end') { ?>
                    <a href="<?php echo $sort_endtime; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_endtime; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_endtime; ?>"><?php echo $column_endtime; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'a.sort_order') { ?>
                    <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>">排序</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_order; ?>">排序</a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($activities && $activity_total) { ?>
                <?php foreach ($activities as $activity) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($activity['act_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $activity['act_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $activity['act_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $activity['act_id']; ?></td>
                  <td class="text-center" style="display: none"><?php if ($activity['image']) { ?>
                    <img src="<?php echo $activity['image']; ?>" alt="<?php echo $activity['name']; ?>" class="img-thumbnail" />
                    <?php } else { ?>
                    <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $activity['station_id']; ?></td>
                  <td class="text-left"><?php echo $activity['name']; ?></td>
                  <td class="text-left"><?php echo $activity['status']; ?></td>
                  <td class="text-left"><?php echo $activity['date_start']; ?></td>
                  <td class="text-left"><?php echo $activity['date_end']; ?></td>
                  <td class="text-left"><?php echo $activity['sort_order']; ?></td>
                  <td class="text-right"><a href="<?php echo $activity['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
	var url = 'index.php?route=catalog/activity&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
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

  var filter_station = $('select[name=\'filter_station\']').val();

  if (filter_station != '*') {
    url += '&filter_station=' + encodeURIComponent(filter_station);
  }

  location = url;
});
//--></script> 
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
  <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
  <script type="text/javascript"><!--
$('.date').datetimepicker({
    pickTime: false
});
//--></script>
</div>
<?php echo $footer; ?>