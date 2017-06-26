<?php echo $header; ?>
<?php echo $column_left; ?>
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
  <div class="container-fluid" id="slaereport">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-2">
              <div class="form-group">
                <label class="control-label" for="input-date-start">开始日期</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label class="control-label" for="input-date-end">结束日期</label>
                    <div class="input-group date">
                        <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
                </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="control-label">市场开发</label>
                  <select name="filter_bd_list" id="input_bd_list" class="form-control">
                      <option value='0'>全部</option>
                      <?php foreach ($bd_list as $val) { ?>
                      <?php if ($val['id'] == $filter_bd_list) { ?>
                      <option value="<?php echo $val['id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $val['id']; ?>" ><?php echo $val['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                  </select>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="control-label">平台</label>
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
            </div>
            <div class="col-sm-2">
              <div>
                  <label class="control-label">市场区域</label>
                  <select name="filter_bd_area_list" id="input_bd_area_list" class="form-control">
                      <option value='0'>全部</option>
                      <?php foreach ($bd_area_list as $val) { ?>
                      <?php if ($val['id'] == $filter_bd_area_list) { ?>
                      <option value="<?php echo $val['id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $val['id']; ?>" ><?php echo $val['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                  </select>
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                  <label class="control-label">活跃度大于(下单/上线)</label>
                  <input type="text" name="filter_activity" id="input_activity" class="form-control" value="<?php echo $filter_activity; ?>">
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

          <?php if(!$nofilter) { ?>
          <div class="table-responsive">
              <table class="table table-bordered" id="totals">
                  <thead>
                  <tr>
                      <th class="text-left">BD名称</th>
                      <th>订单数</th>
                      <th>小计合</th>
                      <th>生鲜合</th>
                      <th>客单价</th>
                      <th>生鲜客单价</th>
                      <th>用户数</th>
                      <th>首单数</th>
                      <th style="background-color: #d6d6d6" title="当前BD负责维护的用户数，可能因区域更改有变动">当前BD维护用户</th>
                      <th style="background-color: #d6d6d6" title="当前BD负责维护的用户中已下单的用户，可能因区域更改有变动">已下单</th>
                      <th style="background-color: #d6d6d6" title="当前BD负责维护的用户中未下单的用户，可能因区域更改有变动">未下单</th>
                      <th>活跃用户数</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                    if (sizeof($totals)) {
                      foreach($totals as $total){
                      if($total['bd_id'] > 0){
                  ?>
                  <tr>
                      <td title="<?php echo 'BD编号:'.$total['bd_id']; ?>"><?php echo $total['bd_name']; ?></td>
                      <td><?php echo $total['sum_orders']; ?></td>
                      <td><?php echo $total['sum_sub_total']; ?></td>
                      <td><?php echo $total['sum_fresh_total']; ?></td>
                      <td><?php echo round($total['sum_sub_total']/$total['sum_orders'],2); ?></td>
                      <td><?php echo round($total['sum_fresh_total']/$total['sum_orders'],2); ?></td>
                      <td><?php echo $total['sum_customers']; ?></td>
                      <td>
                          <?php
                            if($filter_station_id==1){ echo $total['fistorders']; }
                            if($filter_station_id==2){ echo $total['fm_fistorders']; }
                          ?>
                      </td>
                      <td style="background-color: #efefef" title="已禁用用户数：<?php if(isset($bd_customers[$total['bd_id']]['disable_customer'])){ echo $bd_customers[$total['bd_id']]['disable_customer'];}else{ echo '0'; } ?>个">
                          <?php if(isset($bd_customers[$total['bd_id']]['customers'])){ echo $bd_customers[$total['bd_id']]['customers']; }else{ echo '0'; } ?>
                      </td>
                      <td style="background-color: #efefef"><?php echo isset($bd_ordered_customers[$total['bd_id']]) ? $bd_ordered_customers[$total['bd_id']]['ordered_customers'] : 0; ?></td>
                      <td style="background-color: #efefef">
                          <?php if(isset($bd_ordered_customers[$total['bd_id']])){ ?>
                          <?php echo isset($bd_ordered_customers[$total['bd_id']]) ? ($bd_customers[$total['bd_id']]['customers'] - $bd_ordered_customers[$total['bd_id']]['ordered_customers']) : $bd_customers[$total['bd_id']]['customers']; ?>
                          <?php }else{ ?>
                          <?php echo '0'; ?>
                          <?php } ?>
                      </td>
                      <td><?php echo $total['active_customers']; ?></td>
                  </tr>
                  <?php
                  }
                 }
               } else {
              ?>
                  <tr>
                      <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
                  </tr>
                  <?php } ?>
                  <?php
                    if (sizeof($totals)) {
                ?>
                  <tr>
                      <td>合计</td>
                      <td><?php echo $sum_orders; ?></td>
                      <td><?php echo $money_total; ?></td>
                  </tr>
                  <?php } ?>
                  </tbody>
              </table>
          </div>
          <?php } ?>

          <?php if(!$nofilter) { ?>
          <div class="table-responsive">
              <button type="button" id="button-area" class="btn btn-primary pull-right" style="margin:0 5px;"><i class="fa fa-map-marker"></i>显示区域报表</button>
              <button type="button" id="button-area-toggle" class="btn btn-primary pull-right" style="margin:0 5px;display:none"><i class="fa fa-map-marker"></i>隐藏区域报表</button>
          </div>
          <?php } ?>

          <?php if(!$nofilter) { ?>
          <div class="table-responsive">
              <table class="table table-bordered" id="area-totals" style="display:none;">
                  <thead>
                  <tr>
                      <th class="text-left">区域负责人</th>
                      <th>市场区域</th>
                      <th>订单数</th>
                      <th>小计合</th>
                      <th>客单价</th>
                      <th>生鲜客单价</th>
                      <th>用户数</th>
                      <th>首单数</th>
                      <th style="background-color: #d6d6d6" title="当前BD负责维护的用户数，可能因区域更改有变动">当前区域维护用户</th>
                      <th style="background-color: #d6d6d6" title="当前BD负责维护的用户中已下单的用户，可能因区域更改有变动">已下单</th>
                      <th style="background-color: #d6d6d6" title="当前BD负责维护的用户中未下单的用户，可能因区域更改有变动">未下单</th>
                      <th>活跃用户数</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                    if (sizeof($areas)) {
                      foreach($areas as $total){
                      if($total['bd_id'] >= 0){
                  ?>
                  <tr>
                      <td><?php echo $total['bd_name']; ?></td>
                      <td><?php echo $total['area_name']; ?></td>
                      <td><?php echo $total['sum_orders']; ?></td>
                      <td><?php echo $total['sum_sub_total']; ?></td>
                      <td><?php echo round($total['sum_sub_total']/$total['sum_orders'],2); ?></td>
                      <td><?php echo round($total['sum_fresh_total']/$total['sum_orders'],2); ?></td>
                      <td><?php echo $total['sum_customers']; ?></td>
                      <td>
                          <?php
                            if($filter_station_id==1){ echo $total['fistorders']; }
                            if($filter_station_id==2){ echo $total['fm_fistorders']; }
                          ?>
                      </td>
                      <td style="background-color: #efefef" title="已禁用用户数：<?php echo $area_customers[$total['area_id']]['disable_customer']; ?>个"><?php echo $area_customers[$total['area_id']]['customers']; ?></td>
                      <td style="background-color: #efefef"><?php echo isset($bd_area_customers[$total['area_id']]) ? $bd_area_customers[$total['area_id']]['ordered_customers'] : 0; ?></td>
                      <td style="background-color: #efefef"><?php echo isset($bd_area_customers[$total['area_id']]) ? ($area_customers[$total['area_id']]['customers'] - $bd_area_customers[$total['area_id']]['ordered_customers']) : $area_customers[$total['area_id']]['customers']; ?></td>
                      <td><?php echo $total['active_customers']; ?></td>
                  </tr>
                  <?php
                  }
                 }
               } else {
              ?>
                  <tr>
                      <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
                  </tr>
                  <?php } ?>
                  <?php
                    if (sizeof($areas)) {
                ?>
                  <tr>
                      <td>合计</td>
                      <td><?php echo $sum_orders_a; ?></td>
                      <td><?php echo $money_total_a; ?></td>
                  </tr>
                  <?php } ?>
                  </tbody>
              </table>
          </div>
          <?php } ?>
        </div>

        <?php if(!$nofilter && $filter_bd_list>0) { ?>
        <div class="table-responsive">
          <table class="table table-bordered" id="orders">
            <thead>
              <tr>
                  <th>BD人员</th>
                  <th>用户ID</th>
                  <th>用户名</th>
                  <th>商家名</th>
                  <th>月活计算日期</th>
                  <th>最早订单日期</th>
                  <th>最早订单号</th>
                  <th>区间含首单</th>
                  <th>订单数</th>
                  <th>下单天数</th>
                  <th>上线天数</th>
                  <th>活跃度</th>
                  <th>小计合</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if (sizeof($orders)) {
                  foreach($orders as $order){
              ?>
              <tr>
                  <td><?php echo $order['bd_name']; ?></td>
                  <td><?php echo $order['customer_id']; ?></td>
                  <td><?php echo $order['customer_name']; ?></td>
                  <td title="<?php echo $order['merchant_address']."(".$order['shipping_phone'].")";?>">
                    <?php echo $order['merchant_name']; ?></span>
                  </td>
                  <td><?php echo $order['checkdate']; ?></td>
                  <td><?php echo $order['earlist_order_adate']; ?></td>
                  <td><?php echo $order['firstorder_id']; ?></td>
                  <td><?php if($order['fistorder'] || $order['fistorder_fm']){ echo "是"; } else{ echo "否"; } ?></td>
                  <td><?php echo $order['orders']; ?></td>
                  <td><?php echo $order['order_dates']; ?></td>
                  <td><?php echo $order['checkdate_gap']; ?></td>
                  <td><?php echo round($order['order_dates']/$order['checkdate_gap'], 2); ?></td>
                  <td><?php echo $order['sub_total']; ?></td>
              </tr>
              <?php
                 }
               } else {
              ?>
              <tr>
                <td class="text-center" colspan="14"><?php echo $text_no_results; ?></td>
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
    <!--
    $('#button-filter').on('click', function() {
        location = getUrl();
    });

    $('#button-area').on('click',function() {
        $('#area-totals').show();
        $('#button-area').hide();
        $('#button-area-toggle').toggle();
    });

    $('#button-area-toggle').on('click',function() {
        $('#area-totals').hide();
        $('#button-area').show();
        $('#button-area-toggle').toggle();
    })

    function getUrl(){
        url = 'index.php?route=report/sale_customer_active&token=<?php echo $token; ?>';

        var filter_date_start = $('input[name=\'filter_date_start\']').val();

        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').val();

        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }

        var filter_bd_list = $('select[name=\'filter_bd_list\']').val();

        if (filter_bd_list != 0) {
            url += '&filter_bd_list=' + encodeURIComponent(filter_bd_list);
        }

        var filter_station_id = $('select[name=\'filter_station_id\']').val();
        if (filter_station_id != 0) {
            url += '&filter_station_id=' + encodeURIComponent(filter_station_id);
        }

        var filter_activity = $('input[name=\'filter_activity\']').val();
        if (filter_activity != 0) {
            url += '&filter_activity=' + encodeURIComponent(filter_activity);
        }

        var filter_bd_area_list = $('select[name=\'filter_bd_area_list\']').val();

        if (filter_bd_area_list != 0) {
            url += '&filter_bd_area_list=' + encodeURIComponent(filter_bd_area_list);
        }

        return url;
    }

    //-->
  </script>

  <script type="text/javascript">
    <!--
    $('.date').datetimepicker({ pickTime: false});
    //-->
  </script>
</div>
<?php echo $footer; ?>