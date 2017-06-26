<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <!--<button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary" onclick="submitApplication();"><i class="fa fa-save"></i></button>-->
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>审核商品价格</h3>
      </div>
      <div class="panel-body">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-data" data-toggle="tab">待审核列表</a></li>
          <li><a href="#tab-history" data-toggle="tab" onclick="getConfirmedApplications();">审核历史</a></li>
        </ul>
        <div class="tab-content">
          <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i>
          对于业务员的商品售价修改，只有在经理审核之后才能生效
          </div>
          <div class="tab-pane active" id="tab-data">
            <div class="well">
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label" for="input-user">申请人</label>
                    <select name="filter_user" id="input-user" class="form-control">
                      <option value="*"></option>

                      <?php foreach ($purchase_person as $purchase_person) { ?>
                      <?php if ($purchase_person['user_id'] == $filter_user) { ?>
                      <option value="<?php echo $purchase_person['user_id']; ?>" selected="selected"><?php echo $purchase_person['user_id'] .'#'. $purchase_person['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $purchase_person['user_id']; ?>"><?php echo $purchase_person['user_id'] .'#'. $purchase_person['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label" for="input-date">申请日期</label>
                    <div class="input-group date">
                      <input type="text" name="filter_date" value="<?php echo $filter_date; ?>" placeholder="申请日期" data-date-format="YYYY-MM-DD" id="input-date" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <?php echo '</br>'; ?>
                  <button type="button" id="button-filter" class="btn btn-primary pull-left"><i class="fa fa-search"></i> 筛选</button>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-activity" class="form-horizontal">
                <div id = "applications_list">
                  <div class="table-responsive">
                    <table id="applications" class="table table-bordered table-hover">
                      <thead>
                      <tr>
                        <td><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                        <td>商品ID</td>
                        <td style="width:25%">商品名称</td>
                        <td>当前售价</td>
                        <td>对应原料价格</td>
                        <td>采购价格</td>
                        <td>申请价格</td>
                        <td>申请人</td>
                      </tr>
                      </thead>
                      <tbody>
                      <?php if (sizeof($lists)) { ?>
                      <?php foreach ($lists as $list) { ?>
                        <tr>
                          <td>
                            <input type="checkbox" name="selected[]" value="<?php echo $list['price_edit_id']; ?>" />
                          </td>
                          <td><a href='<?php echo $list["product_url"]; ?>' target="_link"><?php echo $list['product_id']; ?></a></td>
                          <td><?php echo $list['name']; ?></td>
                          <td><?php echo $list['price']; ?></td>
                          <td><?php echo $list['sku_price']; ?></td>
                          <td><?php echo $list['purchase_price']; ?></td>
                          <?php if($list['edit_price'] > $list['price']){ ?>
                          <td style="background-color: #ccffcc"><?php echo $list['edit_price']; ?></td>
                          <?php }elseif($list['edit_price'] < $list['price']){ ?>
                          <td style="background-color: #f5e79e"><?php echo $list['edit_price']; ?></td>
                          <?php }else{ ?>
                          <td><?php echo $list['edit_price']; ?></td>
                          <?php } ?>
                          <td><?php echo $list['app_user']; ?></td>
                        </tr>
                        <?php } ?>
                        <?php } else { ?>
                        <tr>
                          <td colspan="8" align="center"><?php echo $text_no_results; ?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                      <tfoot>
                        <td colspan="8" align="center"><button type="submit"  data-toggle="tooltip" title="审核业务员申请" class="btn btn-primary"><i class="icon-ok"></i>审核通过</button></td>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="tab-pane" id="tab-history">
            <div id="exzamined_history">
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
  $('#button-filter').on('click', function() {
    url = 'index.php?route=catalog/product_application&token=<?php echo $token; ?>';

    var filter_user = $('select[name=\'filter_user\']').val();

    if (filter_user) {
      url += '&filter_user=' + encodeURIComponent(filter_user);
    }

    var filter_date = $('input[name=\'filter_date\']').val();

    if (filter_date) {
      url += '&filter_date=' + encodeURIComponent(filter_date);
    }

    location = url;
  });

  function getConfirmedApplications(){
    $('#exzamined_history').load('index.php?route=catalog/product_application/getConfirmed&token=<?php echo $token; ?>');
  }

  function rollback(obj){
    var price_edit_id = obj;
    $.ajax({
      type: 'POST',
      url: 'index.php?route=catalog/product_application/rollbackApplication&token=<?php echo $_SESSION["token"]; ?>',
      dataType: 'json',
      data:{
        price_edit_id:price_edit_id,
      },
      success:function(response){
        if(response){
          alert('作废本次审核成功！')
          getConfirmedApplications();
        }else{
          alert('作废本次审核失败！');
        }
      }
    });
  }

  $('#exzamined_history').delegate('.pagination a', 'click', function(e) {
    e.preventDefault();
    $('#exzamined_history').load(this.href);
  });


</script>
<script type="text/javascript">
$('.date').datetimepicker({
  pickTime: false
});
</script>
</div>
<?php echo $footer; ?>