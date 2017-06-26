<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a> <a href="<?php echo $repair; ?>" data-toggle="tooltip" title="<?php echo $button_rebuild; ?>" class="btn btn-default"><i class="fa fa-refresh"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-category').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                          过滤平台
                          <select name="filter_station" id="input-station" class="form-control" >
                              <option value="">－</option>
                              <?php
                                foreach($stations as $m){
                                    if($filter_station == $m["station_id"]){
                                        echo '<option selected="selected" value="'.$m["station_id"].'">'.$m["name"].'</option>';
                                    }
                                    else{
                                        echo '<option value="'.$m["station_id"].'">'.$m["name"].'</option>';
                                    }
                                }
                              ?>
                          </select>
                      </div>
                  </div>
                  <div class="col-sm-3">
                      <div class="form-group">
                          过滤主分类<select name="filter_parent_category" id="input-filter_parent_category" class="form-control" >
                              <option value="">－</option>
                              <option value="0" <?php if($filter_parent_category === '0'){echo 'selected="selected"';} ?> >仅主分类</option>
                              <?php
                                foreach($parentCategoryList as $m){
                                    if($filter_parent_category == $m["category_id"]){
                                        echo '<option selected="selected" value="'.$m["category_id"].'">'.$m["station"].' / '.$m["category_name"].'</option>';
                                    }
                                    else{
                                        echo '<option value="'.$m["category_id"].'">'.$m["station"].' / '.$m["category_name"].'</option>';
                                    }
                                }
                              ?>
                          </select>
                      </div>
                  </div>
                  <div class="col-sm-3">
                      <div class="form-group">
                          是否启用<select name="filter_status" id="input-status" class="form-control">
                              <option value="">－</option>
                              <option value="1" <?php if($filter_status == 1){echo 'selected="selected"';} ?> ><?php echo $text_enabled; ?></option>
                              <option value="0" <?php if($filter_status === '0'){echo 'selected="selected"';} ?> ><?php echo $text_disabled; ?></option>
                          </select>
                      </div>
                      <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                  </div>
              </div>
          </div>

        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-category">
          <div class="table-responsive">

            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'category_id') { ?>
                    <a href="<?php echo $sort_category_id; ?>" class="<?php echo strtolower($order); ?>">分类编号</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_category_id; ?>">分类编号</a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'station_id') { ?>
                    <a href="<?php echo $sort_station_id; ?>" class="<?php echo strtolower($order); ?>">仓库平台</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_category_id; ?>">仓库平台</a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>">状态</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>">状态</a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'sort_order') { ?>
                    <a href="<?php echo $sort_sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_sort_order; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_sort_order; ?>"><?php echo $column_sort_order; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($categories) { ?>
                <?php foreach ($categories as $category) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($category['category_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $category['category_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $category['category_id']; ?></td>
                  <td class="text-left"><?php echo $category['station']; ?></td>
                  <td class="text-left"><?php echo $category['name']; ?></td>
                  <td class="text-left"><?php echo $category['status']==1 ? "启用" : "<span style='color:#cc0000'>停用</span>"; ?></td>
                  <td class="text-right"><?php echo $category['sort_order']; ?></td>
                  <td class="text-right"><a href="<?php echo $category['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
</div>
<script type="text/javascript"><!--
    $('#button-filter').on('click', function() {
        var url = 'index.php?route=catalog/category&token=<?php echo $token; ?>';

        var filter_station = $('select[name=\'filter_station\']').val();
        if(filter_station !== ""){
            url += '&filter_station=' + encodeURIComponent(filter_station);
        }

        var filter_parent_category = $('select[name=\'filter_parent_category\']').val();
        if(filter_parent_category !== ""){
            url += '&filter_parent_category=' + encodeURIComponent(filter_parent_category);
        }

        var filter_status = $('select[name=\'filter_status\']').val();
        if (filter_status !== "") {
            url += '&filter_status=' + encodeURIComponent(filter_status);
        }

        location = url;
    });
    //--></script>
<?php echo $footer; ?>