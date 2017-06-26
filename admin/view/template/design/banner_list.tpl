<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-banner').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-stations"><?php echo $entry_stations; ?></label>
                <select name="filter_station_id" id="input-stations" class="form-control">
                  <option value="*">全部</option>
                  <?php foreach($stationList as $station){ ?>
                  <?php if($station['station_id'] == $filter_station_id){ ?>
                  <option value="<?php echo $station['station_id']; ?>" selected = "selected"><?php echo $station['name']; ?></option>
                  <?php }else{ ?>
                  <option value="<?php echo $station['station_id']; ?>"><?php echo $station['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                  <select name="status" id="input-status" class="form-control">
                    <option value="*"></option>
                    <?php if ($status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <?php } ?>
                    <?php if (!$status && !is_null($status)) { ?>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_start; ?></label>
                <div class="input-group date">
                  <input type="text" name="date_start" value="<?php echo $date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_end; ?></label>
                <div class="input-group date">
                  <input type="text" name="date_end" value="<?php echo $date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>




        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-banner">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'banner_id') { ?>
                    <a href="<?php echo $sort_banner_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_banner_id; ?>"><?php echo $column_id; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'station_id') { ?>
                     <a href="<?php echo $sort_station_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_stations; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_station_id; ?>"><?php echo $column_stations; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php if ($sort == 'c.date_start') { ?>
                    <a href="<?php echo $sort_date_start; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_start; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_start; ?>"><?php echo $column_date_start; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.date_end') { ?>
                    <a href="<?php echo $sort_date_end; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_end; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_end; ?>"><?php echo $column_date_end; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($banners) { ?>
                <?php foreach ($banners as $banner) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($banner['banner_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $banner['banner_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $banner['banner_id']; ?>" />
                    <?php } ?></td>
                  <td><?php echo $banner['banner_id']; ?></td>
                  <td class="text-left"><?php echo $banner['name']; ?></td>
                  <td class="text-left">
                    <?php if(sizeof($banner['station'])){ ?>
                    <?php foreach($banner['station'] as $staion){ ?>
                    <?php echo $staion.'<br>'; ?>
                    <?php } ?>
                    <?php } ?>
                  </td>
                  <td class="text-left"><?php echo $banner['date_start'] ;?></td>
                  <td class="text-left"><?php echo $banner['date_end'] ;?></td>
                  <td class="text-left"><?php echo $banner['status']; ?></td>
                  <td class="text-right"><a href="<?php echo $banner['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
</div>

<script type="text/javascript"><!--
  $('#button-filter').on('click', function() {
    var url = 'index.php?route=design/banner&token=<?php echo $token; ?>';

    var name = $('input[name=\'name\']').val();

    if (name) {
      url += '&name=' + encodeURIComponent(name);
    }

    var status = $('select[name=\'status\']').val();

    if (status != '*') {
      url += '&status=' + encodeURIComponent(status);
    }

    var date_start = $('input[name=\'date_start\']').val();

    if (date_start) {
      url += '&date_start=' + encodeURIComponent(date_start);
    }

    var date_end = $('input[name=\'date_end\']').val();

    if (date_end) {
      url += '&date_end=' + encodeURIComponent(date_end);
    }

    var filter_station_id = $('select[name=\'filter_station_id\']').val();
    if (filter_station_id != '*') {
      url += '&filter_station_id=' + encodeURIComponent(filter_station_id);
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
<?php echo $footer; ?>