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
  <div class="container-fluid" id="slaereport">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> 司机配送信息列表</h3>
      </div>
      <div class="panel-body">
        <div style="margin:3px 0px; border-radius: 3px; background-color: #ECF3E6; border-color: #E3EBD5; padding: 5px;">
            注意：首单及订单生鲜信息当天21:15更新
        </div>
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="control-label">司机</label>
                    <select name="filter_logistic_list" id="input_logistic_list" class="form-control">
                        <option value='0'>全部</option>
                        <?php foreach ($logistic_list as $val) { ?>
                        <?php if ($val['logistic_driver_id'] == $filter_logistic_list) { ?>
                        <option value="<?php echo $val['logistic_driver_id']; ?>" selected="selected"><?php echo $val['logistic_driver_title']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $val['logistic_driver_id']; ?>" ><?php echo $val['logistic_driver_title']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">线路</label>
                    <select name="filter_logistic_line" id="input_logistic_line" class="form-control">
                        <option value='0'>全部</option>
                        <?php foreach ($logistic_line as $val) { ?>
                        <?php if ($val['logistic_line_id'] == $filter_logistic_line) { ?>
                        <option value="<?php echo $val['logistic_line_id']; ?>" selected="selected"><?php echo $val['logistic_line_title']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $val['logistic_line_id']; ?>" ><?php echo $val['logistic_line_title']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-start">线路分配日期开始</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-station">平台</label>
                <select name="filter_station" id="input_station" class="form-control" <?php if($station_set){ ?>disabled="disabled"<?php } ?>>
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

            <div class="col-sm-3">
                <div class="form-group">
                    <label class="control-label" for="input-date-end">线路分配日期结束</label>
                    <div class="input-group date">
                        <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
                </div>
                
                <div class="form-group">
                <label class="control-label">配送状态</label>
                <select name="filter_order_deliver_status" id="input_order_deliver_status" class="form-control">
                  <option value='0'>全部(非配送失败)</option>
                  <?php foreach ($order_deliver_status as $val) { ?>
                  <?php if ($val['id'] == $filter_order_deliver_status) { ?>
                  <option value="<?php echo $val['id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $val['id']; ?>" ><?php echo $val['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
            </div>
                
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">订单状态</label>
                <select name="filter_order_status" id="input_order_status" class="form-control">
                  <option value='0'>全部(非取消)</option>
                  <?php foreach ($order_status as $val) { ?>
                  <?php if ($val['id'] == $filter_order_status) { ?>
                  <option value="<?php echo $val['id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $val['id']; ?>" ><?php echo $val['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group">
                <label class="control-label">支付状态</label>
                <select name="filter_order_payment_status" id="input_order_payment_status" class="form-control">
                    <option value='0'>全部</option>
                    <?php foreach ($order_payment_status as $val) { ?>
                    <?php if ($val['id'] == $filter_order_payment_status) { ?>
                    <option value="<?php echo $val['id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $val['id']; ?>" ><?php echo $val['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
              </div>
                
            </div>
              

            </div>
            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i>筛选</button>
        </div>


          <?php if($date_gap > 31) { ?>
            <div class="alert alert-warning">
                查询时间不可大于31天!
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
                      <th class="text-left">订单号</th>
                      <th>订单状态</th>
                      <th>支付状态</th>
                      <th>配送日期</th>
                      <th>下单日期</th>
                      <th>下单时间</th>
                      <th>用户ID</th>
                      <th>商家名</th>
                      <th>BD</th>
                      <th>小计</th>
                      <th style="display: none">生鲜</th>
                      <th style="display: none">非鲜</th>
                      <th style="display: none">生鲜类</th>
                      <th style="display: none">生鲜量</th>
                      <th>优惠</th>
                      <th>余额支付</th>
                      <th>应收</th>
                      <th>微信支付</th>
                      <th>积分支付</th>
                      <th>缺货</th>
                      <th>退货</th>
                      <th>财务应收</th>
                      <th>司机</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                if (sizeof($orders)) {
                  foreach($orders as $order){
              ?>
                  <tr>
                      <td><?php echo $order['order_id']; ?></td>
                      <td><?php echo $order['order_status']; ?></td>
                      <td><?php echo $order['payment_status']; ?></td>
                      <td><?php echo $order['deliver_date']; ?></td>
                      <td><?php echo $order['order_date']; ?></td>
                      <td><?php echo $order['order_time']; ?></td>
                      <td><?php echo $order['customer_id']; ?></td>
                      <td title="<?php echo $order['order_address']."(".$order['shipping_phone'].")";?>">
                      <?php echo $order['merchant_name']; ?>
                      </td>
                      <td><?php echo $order['bd_name']; ?></td>
                      <td><?php echo round($order['sub_total'],2); ?></td>

                      <td style="display: none"><?php echo round($order['fresh_total'],2); ?></td>
                      <td style="display: none"><?php echo round($order['nonfresh_total'],2); ?></td>
                      <td style="display: none"><?php echo round($order['fresh_skus'],2); ?></td>
                      <td style="display: none"><?php echo round($order['fresh_items'],2); ?></td>

                      <td><?php echo round($order['discount'],2); ?></td>
                      <td><?php echo round($order['credit_paid'],2); ?></td>
                      <td><?php echo round($order['order_due'],2); ?></td>
                      <td><?php echo round($order['wechat_paid'],2); ?></td>
                      <td><?php echo round($order['user_point_paid'],2); ?></td>

                      <td><?php echo round($order['quehuo_credits'] > 0 ? '-' . $order['quehuo_credits'] : 0,2); ?></td>
                      <td><?php echo round($order['tuihuo_credits'] > 0 ? '-' . $order['tuihuo_credits'] : 0,2); ?></td>
                      <td><?php echo round(($order['order_due']+$order['wechat_paid']+$order['user_point_paid']-$order['quehuo_credits']-$order['tuihuo_credits'] > 0 ? $order['order_due']+$order['wechat_paid']+$order['user_point_paid']-$order['quehuo_credits']-$order['tuihuo_credits'] : 0),2); ?></td>
                      <td style="display: none"><?php echo $order['sum_due'] ?></td>
                      <td> <?php echo $order['logistic_driver_title'];?></td>
                  </tr>
                  <?php
                 }
               } else {
              ?>
                  <tr>
                      <td class="text-center" colspan="14">没有查询结果</td>
                  </tr>
                  <?php } ?>
                  </tbody>
              </table>
          </div>
          <?php } ?>

      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
    $('#button-filter').on('click', function() {
        location = getUrl();
    });


    $('#button-export').on('click',function() {
      url = getUrl();
      url += '&export=1';
      location = url;
    });

function getUrl(){
    url = 'index.php?route=report/driver_total&token=<?php echo $token; ?>';


    var filter_date_start = $('input[name=\'filter_date_start\']').val();

    if (filter_date_start) {
        url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
    }

    var filter_date_end = $('input[name=\'filter_date_end\']').val();

    if (filter_date_end) {
        url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
    }

    var filter_order_status = $('select[name=\'filter_order_status\']').val();

    if (filter_order_status  != 0) {
        url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
    }

    var filter_order_payment_status = $('select[name=\'filter_order_payment_status\']').val();

    if (filter_order_payment_status != 0) {
        url += '&filter_order_payment_status=' + encodeURIComponent(filter_order_payment_status);
    }

    
    var filter_order_deliver_status = $('select[name=\'filter_order_deliver_status\']').val();

    if (filter_order_deliver_status != 0) {
        url += '&filter_order_deliver_status=' + encodeURIComponent(filter_order_deliver_status);
    }


    var filter_logistic_list = $('select[name=\'filter_logistic_list\']').val();
    if(filter_logistic_list !=0){
        url += '&filter_logistic_list=' + encodeURIComponent(filter_logistic_list);
    }

    var filter_logistic_line = $('select[name=\'filter_logistic_line\']').val();
    if(filter_logistic_line !=0){
        url += '&filter_logistic_line=' + encodeURIComponent(filter_logistic_line);
    }

    var filter_station = $('select[name=\'filter_station\']').val();

    if (filter_station != 0) {
        
        url += '&filter_station=' + encodeURIComponent(filter_station);
    }

    return url;
}

//--></script>
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>