<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                <button type="submit" form="form-product" formaction="<?php echo $copy; ?>" data-toggle="tooltip" title="<?php echo $button_copy; ?>" class="btn btn-default"><i class="fa fa-copy"></i></button>
                <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
            </div>
            <h1>站点管理</h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if (isset($error_warning) && $error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if (isset($success) && $success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i>站点列表</h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                                <td class="text-left">显示名称</td>
                                <td class="text-left">配送站名称</td>
                                <td class="text-left">配送站地址</td>
                                <td class="text-center">联系人</td>
                                <td class="text-left">联系电话</td>
                                <td class="text-right">城市</td>
                                <td class="text-right">区域</td>
                                <td class="text-right">状态</td>
                                <td class="text-right">操作</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($stations) { ?>
                            <?php foreach ($stations as $station) { ?>
                            <tr>
                                <td style="width: 1px;" class="text-center"><input type="checkbox" name="selected[]" value="<?php echo $station['station_id']; ?>" /></td>
                                <td class="text-left"><?php echo $station['title']; ?></td>
                                <td class="text-left"><?php echo $station['name']; ?></td>
                                <td class="text-left"><?php echo $station['adderss']; ?></td>
                                <td class="text-center"><?php echo $station['contact_name']; ?></td>
                                <td class="text-left"><?php echo $station['contact_phone']; ?></td>
                                <td class="text-right"><?php echo $station['city']; ?></td>
                                <td class="text-right"><?php echo $station['district']; ?></td>
                                <td class="text-right"><?php if($station['status']){ echo '<span class="label label-success">启用</span>'; }else{ echo '<span class="label label-danger">停用</span>'; } ?></td>
                                <td class="text-right"><a href="<?php echo $edit . '&station_id=' . $station['station_id']; ?>" data-toggle="tooltip" title="编辑" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
                    <div class="col-sm-6 text-right"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>