<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-bd" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <button type="button" style="display: none" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-delete').submit() : false;"><i class="fa fa-trash-o"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
    </div>
    <div class="panel-body">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-bd" class="form-horizontal">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-general" data-toggle="tab">常规</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab-general">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-bd-code"><span data-toggle="tooltip" title="授权码(不可重复)">授权码</span></label>
              <div class="col-sm-5">
                <input type="text" name="bd_code" value="<?php echo $bd_code; ?>" placeholder="授权码(不可重复)" id="input-bd-code" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-bd-name">市场人员姓名</label>
              <div class="col-sm-5">
                <input type="text" name="bd_name" value="<?php echo $bd_name; ?>" placeholder="姓名" id="input-bd-name" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-phone">电话</label>
              <div class="col-sm-5">
                <input type="text" name="phone" value="<?php echo $phone; ?>" placeholder="电话" id="input-phone" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-status">状态</label>
              <div class="col-sm-5">
                <select name="status" id="input-status" class="form-control">
                  <?php if ($status) { ?>
                  <option value="1" selected="selected">启用</option>
                  <?php } else { ?>
                  <option value="1">启用</option>
                  <?php } ?>
                  <?php if (!$status) { ?>
                  <option value="0" selected="selected">停用</option>
                  <?php } else { ?>
                  <option value="0">停用</option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-crm-username"><span data-toggle="tooltip" title="微信企业号账号(不可重复)">微信企业号账号</span></label>
              <div class="col-sm-5">
                <input type="text" name="crm_username" value="<?php echo $crm_username; ?>" placeholder="微信企业号账号" id="input-crm-username" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-sale-access-control"><span data-toggle="tooltip">企业号全局权限</span></label>
              <div class="col-sm-5">
                <select name="sale_access_control" id="input-sale-access-control" class="form-control">
                  <?php if ($sale_access_control) { ?>
                  <option value="1" selected="selected">是</option>
                  <?php } else { ?>
                  <option value="1">是</option>
                  <?php } ?>
                  <?php if (!$sale_access_control) { ?>
                  <option value="0" selected="selected">否</option>
                  <?php } else { ?>
                  <option value="0">否</option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-wx-id">微信号</label>
              <div class="col-sm-5">
                <input type="text" name="wx_id" value="<?php echo $wx_id; ?>" placeholder="微信号(可选)" id="input-wx-id" class="form-control" />
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>