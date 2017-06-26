<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-coupon" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-delete').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
    <div class="alert alert-info">折扣券绑定用户，绑定商品<span style="color:red">请先保存</span>，仅“常规”设置需要点击保存，其他绑定设置在对应标签内已仅保存。不绑定用户所有用户可用，不绑定商品全场通用。</div>
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-coupon" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-history" data-toggle="tab"><?php echo $tab_history; ?></a></li>
            <?php } ?>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-customebind" data-toggle="tab"><?php echo $tab_customebind; ?></a></li>
            <?php } ?>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-bindhistory" data-toggle="tab" onclick="bindHistory();"><?php echo $tab_bindhistory; ?></a></li>
            <?php } ?>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-productbind" data-toggle="tab"><?php echo $tab_productbind; ?></a></li>
            <?php } ?>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-producthistory" data-toggle="tab" onclick="productHistory();"><?php echo $tab_producthistory; ?></a></li>
            <?php } ?>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-productban" data-toggle="tab">排除商品</a></li>
            <?php } ?>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-categorybind" data-toggle="tab">绑定品类</a></li>
            <?php } ?>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-categorybindhistory" data-toggle="tab" onclick="categorybindhistory();">绑定品类记录</a></li>
            <?php } ?>
            <?php if ($coupon_id) { ?>
            <li><a href="#tab-categoryban" data-toggle="tab" onclick="showCategoryBanTpl();">排除品类</a></li>
            <?php } ?>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                  <?php if ($error_name) { ?>
                  <div class="text-danger"><?php echo $error_name; ?></div>
                  <?php } ?>
                </div>
              </div>
              <!--<div class="form-group required">
                <label class="col-sm-2 control-label" for="input-code"><span data-toggle="tooltip" title="<?php echo $help_code; ?>"><?php echo $entry_code; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="code" value="<?php echo $code; ?>" placeholder="<?php echo $entry_code; ?>" id="input-code" class="form-control" />
                  <?php if ($error_code) { ?>
                  <div class="text-danger"><?php echo $error_code; ?></div>
                  <?php } ?>
                </div>
              </div>-->
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-type"><span data-toggle="tooltip" title="<?php echo $help_type; ?>"><?php echo $entry_type; ?></span></label>
                <div class="col-sm-10">
                  <select name="type" id="input-type" class="form-control">
                    <?php if ($type == 'P') { ?>
                    <option value="P" selected="selected"><?php echo $text_percent; ?></option>
                    <?php } else { ?>
                    <option value="P"><?php echo $text_percent; ?></option>
                    <?php } ?>
                    <?php if ($type == 'F') { ?>
                    <option value="F" selected="selected"><?php echo $text_amount; ?></option>
                    <?php } else { ?>
                    <option value="F"><?php echo $text_amount; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-discount"><?php echo $entry_discount; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="discount" value="<?php echo $discount; ?>" placeholder="<?php echo $entry_discount; ?>" id="input-discount" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="total" value="<?php echo $total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
                </div>
              </div>
              <div class="form-group" style="display: none">
                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_logged; ?>"><?php echo $entry_logged; ?></span></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <?php if ($logged) { ?>
                    <input type="radio" name="logged" value="1" checked="checked" />
                    <?php echo $text_yes; ?>
                    <?php } else { ?>
                    <input type="radio" name="logged" value="1" />
                    <?php echo $text_yes; ?>
                    <?php } ?>
                  </label>
                  <label class="radio-inline">
                    <?php if (!$logged) { ?>
                    <input type="radio" name="logged" value="0" checked="checked" />
                    <?php echo $text_no; ?>
                    <?php } else { ?>
                    <input type="radio" name="logged" value="0" />
                    <?php echo $text_no; ?>
                    <?php } ?>
                  </label>
                </div>
              </div>
              <div class="form-group" style="display: none">
                <label class="col-sm-2 control-label"><?php echo $entry_shipping; ?></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <?php if ($shipping) { ?>
                    <input type="radio" name="shipping" value="1" checked="checked" />
                    <?php echo $text_yes; ?>
                    <?php } else { ?>
                    <input type="radio" name="shipping" value="1" />
                    <?php echo $text_yes; ?>
                    <?php } ?>
                  </label>
                  <label class="radio-inline">
                    <?php if (!$shipping) { ?>
                    <input type="radio" name="shipping" value="0" checked="checked" />
                    <?php echo $text_no; ?>
                    <?php } else { ?>
                    <input type="radio" name="shipping" value="0" />
                    <?php echo $text_no; ?>
                    <?php } ?>
                  </label>
                </div>
              </div>
              <div class="form-group" style="display: none">
                <label class="col-sm-2 control-label" for="input-product"><span data-toggle="tooltip" title="<?php echo $help_product; ?>"><?php echo $entry_product; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="product" value="" placeholder="<?php echo $entry_product; ?>" id="input-product" class="form-control" />
                  <div id="coupon-product" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($coupon_product as $coupon_product) { ?>
                    <div id="coupon-product<?php echo $coupon_product['product_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $coupon_product['name']; ?>
                      <input type="hidden" name="coupon_product[]" value="<?php echo $coupon_product['product_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group" style="display: none">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo $entry_category; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
                  <div id="coupon-category" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($coupon_category as $coupon_category) { ?>
                    <div id="coupon-category<?php echo $coupon_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $coupon_category['name']; ?>
                      <input type="hidden" name="coupon_category[]" value="<?php echo $coupon_category['category_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_start" value="<?php echo $date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-end"><?php echo $entry_date_end; ?></label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_end" value="<?php echo $date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>
              <div class="form-group" style="display: none">
                <label class="col-sm-2 control-label" for="input-uses-total"><span data-toggle="tooltip" title="<?php echo $help_uses_total; ?>"><?php echo $entry_uses_total; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="uses_total" value="<?php echo $uses_total; ?>" placeholder="<?php echo $entry_uses_total; ?>" id="input-uses-total" class="form-control" />
                </div>
              </div>
              <div class="form-group" style="display: none">
                <label class="col-sm-2 control-label" for="input-uses-customer"><span data-toggle="tooltip" title="<?php echo $help_uses_customer; ?>"><?php echo $entry_uses_customer; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="uses_customer" value="<?php echo $uses_customer; ?>" placeholder="<?php echo $entry_uses_customer; ?>" id="input-uses-customer" class="form-control" />
                </div>
              </div>

              <!--<div class="form-group">
                <label class="col-sm-2 control-label" for="input-station"><?php echo $entry_station; ?></label>
                <div class="col-sm-10">
                  <select name="station_id" id="input-station" class="form-control">
                    <option value="1" <?php if($station_id == 1) { ?> selected="selected" <?php } ?>><?php echo "包装菜仓"; ?></option>
                    <option value="2" <?php if($station_id == 2) { ?> selected="selected" <?php } ?>><?php echo "快消品仓"; ?></option>
                  </select>
                </div>
              </div>-->


              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-stations"><?php echo $entry_station; ?></label>
                <div class="col-sm-10">
                  <select name="station_id" id="input-station_id" class="form-control" onchange="changeStation()">
                    <option value="0"> 全部 </option>
                    <?php if ( !empty($station_list) ){ foreach( $station_list as $value ){ ?>
                    <option value="<?php echo $value['station_id']; ?>" <?php if(!empty($station_id) && $station_id == $value['station_id'] ){ echo "selected"; } ?> ><?php echo $value['name']; ?></option>
                    <?php }} ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-stations">仓库</label>
                <div class="col-sm-10" id="insert-warehouse-list">

                </div>
              </div>



              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="status" id="input-status" class="form-control">
                    <?php if ($status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-customerlimited"><?php echo $entry_customerlimited; ?></label>
                <div class="col-sm-10">
                  <select name="customerlimited" id="input-customerlimited" class="form-control">
                    <?php if($customerlimited == 0) { ?>
                    <option value="0" selected="selected">不指定用户</option>
                    <option value="1">指定用户</option>
                    <?php } else if($customerlimited == 1) { ?>
                    <option value="0">不指定用户</option>
                    <option value="1" selected="selected">指定用户</option>
                    <?php } else { ?>
                    <option value="0">不指定用户</option>
                    <option value="1" selected="selected">指定用户</option>
                    <?php } ?>
                  </select>
                  <!--<?php if ($error_alert) { ?>
                  <div class="text-danger"><?php echo $error_alert; ?></div>
                  <?php } ?>-->
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-times"><?php echo $entry_times; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="times" value="<?php echo $times; ?>" placeholder="<?php echo $entry_times; ?>" id="input-times" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-request"><?php echo $entry_request; ?></label>
                <div class="col-sm-10">
                  <select name="request" id="input-request" class="form-control">
                    <?php if ($request) { ?>
                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                    <option value="0"><?php echo $text_no; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes; ?></option>
                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-request">限制在线支付</label>
                <div class="col-sm-10">
                  <select name="online_payment" id="input-request" class="form-control">
                    <?php if ($online_payment) { ?>
                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                    <option value="0"><?php echo $text_no; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes; ?></option>
                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-bd-only">仅限BD发放</label>
                <div class="col-sm-10">
                  <select name="bd_only" id="input-bd-only" class="form-control">
                    <?php if ($bd_only) { ?>
                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                    <option value="0"><?php echo $text_no; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes; ?></option>
                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <hr />
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-newcustomer"><?php echo $entry_newcustomer; ?></label>
                <div class="col-sm-3">
                  <select name="newcustomer" id="input-newcustomer" class="form-control">
                    <?php if ($newcustomer) { ?>
                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                    <option value="0"><?php echo $text_no; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes; ?></option>
                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label" for="input-valid-days">折扣券有效天数</label>
                <div class="col-sm-2">
                  <input type="text" name="valid_days" value="<?php echo $valid_days; ?>" placeholder="折扣券有效天数" id="input-valid-days" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label" for="input-reserve_days">新用户延迟生效天数</label>
                <div class="col-sm-2">
                  <input type="text" name="reserve_days" value="<?php echo $reserve_days; ?>" placeholder="新用户折扣券延迟生效天数" id="input-reserve_days" class="form-control" />
                </div>
              </div>
            </div>
            <?php if ($coupon_id) { ?>
            <div class="tab-pane" id="tab-history">
              <div id="history"></div>
            </div>
            <?php } ?>
            <div class="tab-pane" id="tab-customebind">
              <div id="customebind"></div>
            </div>
            <div class="tab-pane" id="tab-bindhistory">
              <div id="bindhistory"></div>
            </div>
            <div class="tab-pane" id="tab-productbind">
              <div id="productbind"></div>
            </div>
            <div class="tab-pane" id="tab-producthistory">
              <div id="producthistory"></div>
            </div>
            <div class="tab-pane" id="tab-productban">
              <div id="productban"></div>
            </div>
            <div class="tab-pane" id="tab-categorybind">
              <div id="categorybind"></div>
            </div>
            <div class="tab-pane" id="tab-categorybindhistory">
              <div id="categorybindhistory"></div>
            </div>
            <div class="tab-pane" id="tab-categoryban">
              <div id="categoryban"></div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
    changeStation();

    function changeStation(){
      var station_id      = $('#input-station_id').val();

      var warehouse_list = JSON.parse('<?php echo json_encode($warehouse_list); ?>');
      var warehouse_ids  = JSON.parse('<?php echo json_encode($warehouse_ids); ?>');
      var html = "";
      $.each(warehouse_list, function(index, item){
        if(station_id == 0 || item.station_id == station_id){
          html += '<div class="checkbox">';
          html +=     '<label>';

          if(warehouse_ids.length > 0){
            var checked = "";
            if($.inArray(item.warehouse_id, warehouse_ids) >= 0){
              checked = "checked";
            }
            html += '<input type="checkbox" name="warehouse_ids[]" value="'+ item.warehouse_id +'" '+ checked +' > ';
          }
          else
          {
            html += '<input type="checkbox" name="warehouse_ids[]" value="'+ item.warehouse_id +'" checked > ';
          }

          if (station_id == 0){ html += ' [ '+ item.name +' ]  '; }

          html +=         ' <b>'+ item.title +'</b>';
          html +=     '</label>';
          html += '</div>';
        }
        if(station_id == 0){
          html = "";
        }
      });

      $('#insert-warehouse-list').html( html );
    }

    function bindHistory(){
        $('#bindhistory').load('index.php?route=marketing/coupon/bindhistory&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');

    }

    function productHistory(){
        $('#producthistory').load('index.php?route=marketing/coupon/producthistory&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');

    }

    function categorybindhistory(){
        $('#categorybindhistory').load('index.php?route=marketing/coupon/categoryBindHistory&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');

    }

    function showCategoryBanTpl(){
      $('#categoryban').load('index.php?route=marketing/coupon/categoryBan&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
    }

    $('input[name=\'product\']').autocomplete({
        'source': function(request, response) {
            $.ajax({
                url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
                dataType: 'json',
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            label: item['name'],
                            value: item['product_id']
                        }
                    }));
                }
            });
        },
        'select': function(item) {
            $('input[name=\'product\']').val('');

            $('#coupon-product' + item['value']).remove();

            $('#coupon-product').append('<div id="coupon-product' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="coupon_product[]" value="' + item['value'] + '" /></div>');
        }
    });

    $('#coupon-product').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();
    });

    // Category
    $('input[name=\'category\']').autocomplete({
        'source': function(request, response) {
            $.ajax({
                url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
                dataType: 'json',
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            label: item['name'],
                            value: item['category_id']
                        }
                    }));
                }
            });
        },
        'select': function(item) {
            $('input[name=\'category\']').val('');

            $('#coupon-category' + item['value']).remove();

            $('#coupon-category').append('<div id="coupon-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="coupon_category[]" value="' + item['value'] + '" /></div>');
        }
    });

    $('#coupon-category').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();
    });
    //--></script>
      <?php if ($coupon_id) { ?>
      <script type="text/javascript"><!--
    $('#history').delegate('.pagination a', 'click', function(e) {
        e.preventDefault();
        $('#history').load(this.href);
    });
    $('#bindhistory').delegate('.pagination a', 'click', function(e) {
        e.preventDefault();
        $('#bindhistory').load(this.href);
    });
    $('#producthistory').delegate('.pagination a', 'click', function(e) {
        e.preventDefault();
        $('#producthistory').load(this.href);
    });
    $('#categorybindhistory').delegate('.pagination a', 'click', function(e) {
        e.preventDefault();
        $('#categorybindhistory').load(this.href);
    });

    $('#history').load('index.php?route=marketing/coupon/history&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
    $('#customebind').load('index.php?route=marketing/coupon/customebind&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
    //$('#bindhistory').load('index.php?route=marketing/coupon/bindhistory&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
    $('#productbind').load('index.php?route=marketing/coupon/productbind&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
    //$('#producthistory').load('index.php?route=marketing/coupon/producthistory&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
    $('#categorybind').load('index.php?route=marketing/coupon/categoryBind&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
    //$('#categorybindhistory').load('index.php?route=marketing/coupon/categoryBindHistory&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
        //-->
    $('#productban').load('index.php?route=marketing/coupon/productban&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
//    $('#categoryban').load('index.php?route=marketing/coupon/categoryBan&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');


      </script>
      <?php } ?>
      <script type="text/javascript"><!--
    $('.date').datetimepicker({
      pickTime: false
    });
    //-->
  </script>
</div>
<?php echo $footer; ?>