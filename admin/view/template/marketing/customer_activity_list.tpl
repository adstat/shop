<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button style="display: none" type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-coupon').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                <label class="control-label" for="input-name">活动名称</label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="活动名称" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <br>
              <br>
              <button type="button" id="button-filter" class="btn btn-primary pull-left"><i class="fa fa-search"></i> 筛选 </button>
            </div>
          </div>
        </div>

        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-coupon">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'marketing_event_id') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>">活动编号</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>">活动编号</a>
                    <?php } ?></td>
                  <td class="text-left">活动名称</td>
                  <td class="text-left">活动开始时间</td>
                  <td class="text-left">活动结束时间</td>
                  <td class="text-center">操作</td>
                </tr>
              </thead>
              <tbody>
                <?php if ($activities) { ?>
                <?php foreach ($activities as $event) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($event['marketing_event_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $event['marketing_event_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $event['marketing_event_id']; ?>" />
                    <?php } ?></td>
                  <td><?php echo $event['marketing_event_id']; ?></td>
                  <td class="text-left"><?php echo $event['title']; ?></td>
                  <td class="text-left"><?php echo $event['date_start']; ?></td>
                  <td class="text-left"><?php echo $event['date_end']; ?></td>
                  <td class="text-right"><a href="<?php echo $event['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
<script type="text/javascript">
$('#button-filter').on('click', function() {
  url = 'index.php?route=marketing/customer_activity&token=<?php echo $token; ?>';

  var filter_name = $('input[name=\'filter_name\']').val();

  if (filter_name) {
    url += '&filter_name=' + encodeURIComponent(filter_name);
  }

  location = url;
});
</script>
</div>
<?php echo $footer; ?>