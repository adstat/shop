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
                <label class="control-label" for="input-name">优惠券名称</label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="优惠券名称" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-valid-days">有效天数</label>
                <input type="text" name="filter_valid_days" value="<?php echo $filter_valid_days; ?>" placeholder="有效天数" id="input-valid-days" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-status">状态</label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_status == 1) { ?>
                  <option value="1" selected="selected">启用</option>
                  <?php } else { ?>
                  <option value="1">启用</option>
                  <?php } ?>
                  <?php if($filter_status == 0 && !is_null($filter_status)) { ?>
                  <option value="0" selected="selected">停用</option>
                  <?php } else { ?>
                  <option value="0">停用</option>
                  <?php } ?>
                </select>
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
                  <td>编号</td>
                  <td class="text-left"><?php if ($sort == 'cd.name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-right">类型</td>
                  <td class="text-left">最低订单金额</td>
                  <!--<td class="text-left"><?php if ($sort == 'c.code') { ?>
                    <a href="<?php echo $sort_code; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_code; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_code; ?>"><?php echo $column_code; ?></a>
                    <?php } ?></td>-->
                  <td class="text-right"><?php if ($sort == 'c.discount') { ?>
                    <a href="<?php echo $sort_discount; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_discount; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_discount; ?>"><?php echo $column_discount; ?></a>
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
                  <td class="text-left"><?php if ($sort == 'c.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_customer_forbidden; ?></td>
                  <td class="text-left"><?php echo $column_time; ?></td>
                  <td class="text-left">在线支付</td>
                  <td class="text-left">BD发放</td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($coupons) { ?>
                <?php foreach ($coupons as $coupon) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($coupon['coupon_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $coupon['coupon_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $coupon['coupon_id']; ?>" />
                    <?php } ?></td>
                  <td><?php echo $coupon['coupon_id']; ?></td>
                  <td class="text-left"><?php echo $coupon['name']; ?></td>
                  <td class="text-right"><?php echo $coupon['type']; ?></td>
                  <td class="text-left"><?php echo $coupon['total']; ?></td>
                  <!--<td class="text-left"><?php echo $coupon['code']; ?></td>-->
                  <td class="text-right"><?php echo $coupon['discount']; ?></td>
                  <td class="text-left"><?php echo $coupon['date_start']; ?></td>
                  <td class="text-left"><?php echo $coupon['date_end']; ?></td>
                  <td class="text-left"><?php echo $coupon['status']; ?></td>
                  <td class="text-left"><?php echo $coupon['forbidden']; ?></td>
                  <td class="text-left"><?php echo $coupon['times']; ?></td>
                  <td class="text-left"><?php echo $coupon['online_payment']; ?></td>
                  <td class="text-left"><?php echo $coupon['bd_only']; ?></td>
                  <td class="text-right"><a href="<?php echo $coupon['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
  url = 'index.php?route=marketing/coupon&token=<?php echo $token; ?>';

  var filter_name = $('input[name=\'filter_name\']').val();

  if (filter_name) {
    url += '&filter_name=' + encodeURIComponent(filter_name);
  }

  var filter_valid_days = $('input[name=\'filter_valid_days\']').val();

  if (filter_valid_days) {
    url += '&filter_valid_days=' + encodeURIComponent(filter_valid_days);
  }

  var filter_status = $('select[name=\'filter_status\']').val();

  if (filter_status != '*') {
    url += '&filter_status=' + encodeURIComponent(filter_status);
  }

  location = url;
});
</script>
</div>
<?php echo $footer; ?>