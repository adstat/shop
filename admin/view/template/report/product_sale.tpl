<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <div class="container-fluid" id="productsale">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-start">开始日期</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label">用户ID</label>
                <input type="text" name="filter_customer_id" id="input_customer_id"  value="<?php echo $filter_customer_id; ?>" placeholder="23,664,665,..." class="form-control" />
              </div>
              <div class="from-group">
                <label class="control-label" for="input-if-category">是否分类排行</label>
                <select name="filter_if_category" id="input-if-category" class="form-control">
                  <option value="*"></option>
                  <option value="1" <?php if($filter_if_category == 1){echo 'selected="selected"';} ?> >是</option>
                  <option value="0" <?php if($filter_if_category === '0'){echo 'selected="selected"';} ?> >否</option>
                </select>
                </select>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-start">结束日期</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label">商品ID</label>
                <input type="text" name="filter_product_id_name" id="input_product_id_name"  value="<?php echo $filter_product_id_name; ?>" placeholder="23,664,665,..." class="form-control" />
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">选择仓库</label>
                <select name="filter_station_id" id="input_station_id" class="form-control" <?php if($station_set){ ?>disabled="disabled"<?php } ?>>
                  <option value='0'>全部</option>
                  <?php foreach($stations as $station){ ?>
                  <?php if($station_set){ ?>
                  <option value="<?php echo $station['station_id']; ?>" <?php if($station['station_id']==$station_set){ ?>selected="selected"<?php } ?>><?php echo $station['name']; ?></option>
                  <?php }else{ ?>
                  <option value="<?php echo $station['station_id']; ?>" <?php if($station['station_id']==$filter_station_id){ ?>selected="selected"<?php } ?>><?php echo $station['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label">商品分类（一级分类/二级分类）</label>
                <input type="text" name="filter_category_id" id="input_category_id"  value="<?php echo $filter_category_id; ?>" placeholder="奶制品" class="form-control" />
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">选择用户组</label>
                <select name="filter_customer_group_id" id="input_customer_group_id" class="form-control">
                  <option value='0'>全部</option>
                  <?php foreach ($customerGroup as $val) { ?>
                  <?php if ($val['customer_group_id'] == $filter_customer_group_id) { ?>
                  <option value="<?php echo $val['customer_group_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $val['customer_group_id']; ?>" ><?php echo $val['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label">商品名称</label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="商品名称" id="input-name" class="form-control" />
              </div>
            </div>

          </div>
          <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
        </div>
        <?php if($date_gap > 31) { ?>
        <div class="alert alert-warning">
          查询时间不可大于31天!
        </div>
        <?php } ?>

        <?php if($warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $warning; ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if(!$nofilter) { ?>
        <div class="table-responsive">
          <button type="button" id="button-export" class="btn btn-primary pull-right" style="margin:0 5px;"><i class="fa fa-bar-chart"></i>导出EXCEL</button>
        </div>
        <?php } ?>

        <?php if(!$nofilter) { ?>
        <div class="table-responsive">
          <table class="table table-bordered" id="totals">
            <thead>
            <tr>
              <th>商品ID</th>
              <th>商品名称</th>
              <th>状态</th>
              <th>规格</th>
              <th>一级分类</th>
              <th>二级分类</th>
              <th>价格</th>
              <th>销量</th>
              <th>周转率</th>
              <th>当前库存</th>
            </tr>
            </thead>
            <tbody>
            <?php
                if (sizeof($sales)) {
                  foreach($sales as $total){
              ?>
            <tr>
              <td><?php echo $total['product_id']; ?></td>
              <td><?php echo $total['name']; ?></td>
              <td><?php echo $total['status']; ?></td>
              <td><?php echo $total['formate']; ?></td>
              <td><?php echo $total['first_category']; ?></td>
              <td><?php echo $total['second_category']; ?></td>
              <td><?php echo $total['price']; ?></td>
              <td><?php echo $total['quangtity']; ?></td>
              <td>
                <?php
                if(!array_key_exists($total['product_id'],$s_inventory) && !array_key_exists($total['product_id'],$inventory_in)){
                  echo "无库存计算";
                }else{

                  if(array_key_exists($total['product_id'],$inventory_in) && array_key_exists($total['product_id'],$s_inventory)){
                    if((0.5*((2*($s_inventory[$total['product_id']]['inv_end']+$inventory_in[$total['product_id']]['inv_end']))-$total['quangtity'])) != 0){
                      echo round($total['quangtity']/(0.5*((2*($s_inventory[$total['product_id']]['inv_end']+$inventory_in[$total['product_id']]['inv_end']))-$total['quangtity'])),4);
                    }else{
                      echo "除数为0";
                    }
                  }elseif(array_key_exists($total['product_id'],$inventory_in) && !array_key_exists($total['product_id'],$s_inventory)){
                    if((0.5*((2*$inventory_in[$total['product_id']]['inv_end'])-$total['quangtity'])) !=0 ){
                      echo round($total['quangtity']/(0.5*((2*$inventory_in[$total['product_id']]['inv_end'])-$total['quangtity'])),4);
                    }else{
                      echo "除数为0";
                    }
                  }elseif(!array_key_exists($total['product_id'],$inventory_in) && array_key_exists($total['product_id'],$s_inventory)){
                    if((0.5*((2*$s_inventory[$total['product_id']]['inv_end'])-$total['quangtity'])) != 0){
                      echo round($total['quangtity']/(0.5*((2*$s_inventory[$total['product_id']]['inv_end'])-$total['quangtity'])),4);
                    }else{
                      echo "除数为0";
                    }
                  }
                }
              ?>

              </td>

              <td>
                <?php
                 if(array_key_exists($total['product_id'],$ori_inv)){
                    echo $ori_inv[$total['product_id']];
                 }else{
                    echo "该商品无库存变化记录";
                 }
              ?>
              </td>
            </tr>
            <?php
                 }
               } else {
              ?>
            <tr>
              <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
        <?php } ?>

      </div>
    </div>
  </div>
  <script type="text/javascript">
    $('#button-filter').on('click', function() {
      location = getUrl();
    });

    $('#button-export').on('click',function() {
      url = getUrl();
      url += '&export=1';
      location = url;
    });

    function getUrl() {
      url = 'index.php?route=report/product_sale&token=<?php echo $token; ?>';

      var filter_date_start = $('input[name=\'filter_date_start\']').val();

      if (filter_date_start) {
        url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
      }

      var filter_date_end = $('input[name=\'filter_date_end\']').val();

      if (filter_date_end) {
        url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
      }

      var filter_station_id = $('select[name=\'filter_station_id\']').val();

      if(filter_station_id) {
        url += '&filter_station_id=' + encodeURIComponent(filter_station_id);
      }

      var filter_customer_group_id = $('select[name=\'filter_customer_group_id\']').val();

      if(filter_customer_group_id) {
        url += '&filter_customer_group_id=' + encodeURIComponent(filter_customer_group_id);
      }

      var filter_customer_id = $('input[name=\'filter_customer_id\']').val();

      if(filter_customer_id) {
        url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
      }

      var filter_product_id_name = $('input[name=\'filter_product_id_name\']').val();

      if(filter_product_id_name) {
        url += '&filter_product_id_name=' + encodeURIComponent(filter_product_id_name);
      }

      var filter_category_id = $('input[name=\'filter_category_id\']').val();

      if(filter_category_id) {
        url += '&filter_category_id=' + encodeURIComponent(filter_category_id);
      }

      var filter_name = $('input[name=\'filter_name\']').val();

      if(filter_name) {
        url += '&filter_name=' + encodeURIComponent(filter_name);
      }

      var filter_if_category = $('select[name=\'filter_if_category\']').val()

      if(filter_if_category != '*') {
        url += '&filter_if_category=' + encodeURIComponent(filter_if_category);
      }

      return url;
    }

  </script>
<script type="text/javascript">
  $('input[name=\'filter_name\']').autocomplete({
    'source': function(request, response) {
      $.ajax({
        url: 'index.php?route=report/product_sale/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
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
      $('input[name=\'filter_name\']').val(item['label']);
    }
  });
</script>

<script type="text/javascript"><!--
  $('.date').datetimepicker({
    pickTime: false
  });
  //--></script></div>

<?php echo $footer; ?>