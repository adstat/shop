<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button style="display: none" type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-bd').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                <label class="control-label" for="input-name">BD姓名</label>
                <input type="text" name="filter_bd_name" value="<?php echo $filter_bd_name; ?>" placeholder="BD姓名" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-id">BD编号</label>
                <input type="text" name="filter_bd_id" value="<?php echo $filter_bd_id; ?>" placeholder="BD编号" id="input-id" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-bd-status">BD状态</label>
                <select name="filter_status" id="input-bd-status" class="form-control">
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
          </div>
          <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i>筛选</button>
        </div>

        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-coupon">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
              <tr>
                <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                <td class="text-left"><?php if ($sort == 'bd_id') { ?>
                  <a href="<?php echo $sort_bd_id; ?>" class="<?php echo strtolower($order); ?>">编号</a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_bd_id; ?>">编号</a>
                  <?php } ?></td>
                <td class="text-left"><?php if ($sort == 'bd_name') { ?>
                  <a href="<?php echo $sort_bd_name; ?>" class="<?php echo strtolower($order); ?>">名称</a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_bd_name; ?>">名称</a>
                  <?php } ?></td>
                <td>电话号码</td>
                <td>微信组名称</td>
                <td class="text-left"><?php if ($sort == 'status') { ?>
                  <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>">状态</a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_status; ?>">状态</a>
                  <?php } ?></td>
                <td>操作</td>
              </tr>
              </thead>
              <tbody>
                <?php if($bds){ ?>
                <?php foreach($bds as $bd){ ?>
                <tr>
                  <td class="text-center"><?php if (in_array($bd['bd_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $bd['bd_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $bd['bd_id']; ?>" />
                    <?php } ?></td>
                  <td><?php echo $bd['bd_id']; ?></td>
                  <td><?php echo $bd['bd_name']; ?></td>
                  <td><?php echo $bd['phone'] ?></td>
                  <td><?php echo $bd['crm_username']; ?></td>
                  <td><?php echo $bd['status']; ?></td>
                  <td class="text-right"><a href="<?php echo $bd['edit']; ?>" data-toggle="tooltip" title="编辑" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
      url = 'index.php?route=marketing/bd&token=<?php echo $token; ?>';

      var filter_bd_name = $('input[name=\'filter_bd_name\']').val();

      if (filter_bd_name) {
        url += '&filter_bd_name=' + encodeURIComponent(filter_bd_name);
      }

      var filter_bd_id = $('input[name=\'filter_bd_id\']').val();

      if (filter_bd_id) {
        url += '&filter_bd_id=' + encodeURIComponent(filter_bd_id);
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