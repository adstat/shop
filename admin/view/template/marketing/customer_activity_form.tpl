<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-coupon" data-toggle="tooltip" title="保存" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="返回" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-coupon" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">活动</a></li>
            <?php if($marketing_event_id) { ?>
            <li><a href="#tab-customer" data-toggle="tab" onclick="signUpList();">已报名的商家</a></li>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-title">标题</label>
                <div class="col-sm-10">
                  <input type="text" name="title" value="<?php echo $title; ?>" placeholder="标题" id="input-title" class="form-control" />
                  <?php if ($error_title) { ?>
                  <div class="text-danger"><?php echo $error_title; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-content">内容</label>
                <div class="col-sm-10">
                  <textarea name="content" placeholder="" id="input-content" class="form-control"><?php echo $content; ?></textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-start">开始日期</label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_start" value="<?php echo $date_start; ?>" placeholder="开始日期" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-end">结束日期</label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_end" value="<?php echo $date_end; ?>" placeholder="结束日期" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-customer">
              <div id="customers"></div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript">
  function signUpList(){
    $('#customers').load('index.php?route=marketing/customer_activity/signUpList&token=<?php echo $token; ?>&marketing_event_id=<?php echo $marketing_event_id; ?>');

  }
</script>
<?php if($marketing_event_id) { ?>
<script type="text/javascript">
  $('#customers').delegate('.pagination a', 'click', function(e) {
    e.preventDefault();
    $('#customers').load(this.href);
  });
</script>
<?php } ?>
<script type="text/javascript">
  $('#input-content').summernote({
    height: 80
  });
  $('.date').datetimepicker({
    pickTime: false
  });
</script>
</div>
<?php echo $footer; ?>
