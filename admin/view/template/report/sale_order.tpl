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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div style="margin:3px 0px; border-radius: 3px; background-color: #ECF3E6; border-color: #E3EBD5; padding: 5px;">
            注意：首单及订单生鲜信息当天21:15更新
        </div>
        <div class="well">
          <div class="row">
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="control-label">日期类型</label>
                  <select name="filter_datatype" id="input_datatype" class="form-control">
                      <option value='2' <?php if($filter_datatype == 2){ echo 'selected="selected"'; } ?> >下单</option>
                      <option value='1' <?php if($filter_datatype == 1){ echo 'selected="selected"'; } ?> >配送</option>
                  </select>
              </div>

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
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                <label class="control-label" for="input-date-start">开始日期</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>

              <div class="form-group">
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
                    <label class="control-label" for="input-date-end">结束日期</label>
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
            <div class="col-sm-2">
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
                
                
            </div>
              
              
            <div class="col-sm-2">
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
                      <label class="control-label">是否首单</label>
                      <select name="filter_firstorder" id="input_firstorder" class="form-control">
                          <option value='0'>全部</option>
                          <option value='1' <?php if($filter_firstorder == 1){ echo 'selected="selected"'; } ?> >首单</option>
                      </select>
                  </div>
              </div>

            <div class="col-sm-2">
              <div class="form-group">
                  <label class="control-label">用户ID</label>
                      <input type="text" name="filter_customer_id" id="input_customer_id"  value="<?php echo $filter_customer_id; ?>" placeholder="23,664,665,..." class="form-control" />
              </span></div>
              </div>
              
              
              
              <div class="col-sm-2">
                <div class="form-group">
                    <label class="control-label" for="input-station">订单属性-<b style="color: #CC0000">生鲜(包装菜、奶制品)，非生鲜(快销品)</b></label>
                  <select name="filter_station" id="input-station" class="form-control" <?php if($station_set){ ?>disabled="disabled"<?php } ?>>
                      <option value='0'>全部</option>
                      <?php foreach($stations as $station){ ?>
                      <?php if($station_set){ ?>
                      <option value="<?php echo $station['station_id']; ?>" <?php if($station['station_id']==$station_set){ ?>selected="selected"<?php } ?>><?php echo $station['name']; ?></option>
                      <?php }else{ ?>
                      <option value="<?php echo $station['station_id']; ?>" <?php if($station['station_id']==$filter_station){ ?>selected="selected"<?php } ?>><?php echo $station['name']; ?></option>
                      <?php } ?>
                      <?php } ?>
                  </select>
            </div>
              </div>
              
              
              
              
              
            </div>
            <button type="button" id="button-map" class="btn btn-primary pull-right" style="margin:0 5px;"><i class="fa fa-map-marker"></i>显示商家地图</button>
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
                      <th class="text-left">合计日期</th>
                      <th>订单数</th>
                      <th>小计合</th>
                      <th>客单价</th>
                      <th style="display: none">生鲜合</th>
                      <th style="display: none">
                          <label class="control-label">
                            <span data-original-title="生鲜品类在15种以上的订单" data-toggle="tooltip" title="">生鲜订单数</span>
                          </label>
                      </th>
                      <th style="display: none">生鲜客单价</th>
                      <th style="display: none">非生鲜合</th>
                      <th>优惠合</th>
                      <th>余额支付合</th>
                      <th>应收合</th>
                      <th>积分支付合</th>
                      <th>微信支付合</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                if (sizeof($totals)) {
                  foreach($totals as $total){
              ?>
                  <tr>
                      <td><?php echo $total['sum_date']; ?></td>
                      <td><?php echo $total['order_count']; ?></td>
                      <td><?php echo $total['sum_sub_total']; ?></td>
                      <td><?php echo round($total['sum_sub_total']/$total['order_count'],2); ?></td>
                      <td style="display: none"><?php echo $total['sum_fresh_total']; ?></td>
                      <td style="display: none"><?php echo $total['fresh_orders']; ?></td>
                      <td style="display: none"><?php echo $total['fresh_order_total']; ?></td>
                      <td style="display: none"><?php echo $total['sum_nonfresh_total']; ?></td>
                      <td><?php echo $total['sum_discount']; ?></td>
                      <td><?php echo $total['sum_credit_paid']; ?></td>
                      <td><?php echo $total['sum_order_due']; ?></td>
                      <td><?php echo $total['sum_user_point_paid']; ?></td>
                      <td><?php echo $total['sum_wechat_paid']; ?></td>
                  </tr>
                  <?php
                 }
               } else {
              ?>
                  <tr>
                      <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
                  </tr>
                  <?php } ?>
                  </tbody>
              </table>
          </div>
          <?php } ?>

          <?php if(!$nofilter) { ?>
          <div class="table-responsive">
          <button type="button" id="button-area" class="btn btn-primary pull-right" style="margin:0 5px;" onclick="getAreaInfo();"><i class="fa fa-map-marker"></i>显示区域报表</button>
              <button type="button" id="button-export" class="btn btn-primary pull-right" style="margin:0 5px;"><i class="fa fa-bar-chart"></i>导出EXCEL</button>
          <button type="button" id="button-area-toggle" class="btn btn-primary pull-right" style="margin:0 5px;display:none"><i class="fa fa-map-marker"></i>隐藏区域报表</button>
          </div>
          <?php } ?>

          <!--合计区域-->
          <?php if(!$nofilter) { ?>
          <div class="table-responsive">
              <table class="table table-bordered" id="area-totals" style="display:none;">
                  <thead>
                  <tr>
                      <th>区域</th>
                      <th>下单客户数</th>
                      <th class="text-left">首单数</th>
                      <th>订单数</th>
                      <th>小计合</th>
                      <th>客单价</th>
                      <th>优惠合</th>
                      <th>余额支付合</th>
                      <th>应收合</th>
                      <th>积分支付合</th>
                      <th>微信支付合</th>
                  </tr>
                  </thead>
                  <tbody id="area-ajax-info">
                 <!-- <?php
                if (sizeof($areas)) {
                  foreach($areas as $total){
              ?>
                  <tr>
                      <td><?php echo $total['area_name']; ?></td>
                      <td><?php echo $total['sum_customer_count']; ?></td>
                      <td><?php echo $total['sum_first_total']; ?></td>
                      <td><?php echo $total['order_count']; ?></td>
                      <td><?php echo $total['sum_sub_total']; ?></td>
                      <td><?php echo round($total['sum_sub_total']/$total['order_count'],2); ?></td>
                      <td style="display: none"><?php echo $total['sum_fresh_total']; ?></td>
                      <td style="display: none"><?php echo $total['fresh_orders']; ?></td>
                      <td style="display: none"><?php echo $total['fresh_order_total']; ?></td>
                      <td style="display: none"><?php echo $total['sum_nonfresh_total']; ?></td>
                      <td><?php echo $total['sum_discount']; ?></td>
                      <td><?php echo $total['sum_credit_paid']; ?></td>
                      <td><?php echo $total['sum_order_due']; ?></td>
                      <td><?php echo $total['sum_user_point_paid']; ?></td>
                      <td><?php echo $total['sum_wechat_paid']; ?></td>
                  </tr>
                  <?php
                 }
               } else {
              ?>
                  <tr>
                      <td class="text-center" colspan="11"><?php echo $text_no_results; ?></td>
                  </tr>
                  <?php } ?>-->
                  </tbody>
              </table>
          </div>
          <?php } ?>

        </div>

        <?php if(!$nofilter) { ?>
        <div class="table-responsive">
          <table class="table table-bordered" id="orders">
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
                  <?php echo $order['merchant_name']; ?><span style="background-color: #faa732"><?php if($order['firstorder']==1){ echo "[首单]"; } ?></span>
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

                <td><?php echo ($order['quehuo_credits'] > 0 ? round($order['quehuo_credits'],2)*-1 : 0); ?></td>
                <td><?php echo ($order['tuihuo_credits'] > 0 ? round($order['tuihuo_credits'],2)*-1 : 0); ?></td>
                <td><?php echo round(($order['order_due']+$order['wechat_paid']+$order['user_point_paid']-$order['quehuo_credits']-$order['tuihuo_credits'] > 0 ? $order['order_due']+$order['wechat_paid']+$order['user_point_paid']-$order['quehuo_credits']-$order['tuihuo_credits'] : 0),2); ?></td>
                  <td style="display: none"><?php echo $order['sum_due'] ?></td>
                  <td> <?php echo $order['logistic_driver_title'];?></td>
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
  <script type="text/javascript"><!--
    $('#button-filter').on('click', function() {
        location = getUrl();
    });

    $('#button-map').on('click', function() {
        url = getUrl();
        url += '&showmap=1';

        window.open(url,"_blank");
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
      $('#button-export').on('click',function() {
          url = getUrl();
          url += '&export=1';
          location = url;
      });

function getUrl(){
    url = 'index.php?route=report/sale_order&token=<?php echo $token; ?>';

    var filter_datatype = $('select[name=\'filter_datatype\']').val();

    if (filter_datatype) {
        url += '&filter_datatype=' + encodeURIComponent(filter_datatype);
    }

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

    var filter_bd_list = $('select[name=\'filter_bd_list\']').val();

    if (filter_bd_list != 0) {
        url += '&filter_bd_list=' + encodeURIComponent(filter_bd_list);
    }

    var filter_logistic_list = $('select[name=\'filter_logistic_list\']').val();
    if(filter_logistic_list !=0){
        url += '&filter_logistic_list=' + encodeURIComponent(filter_logistic_list);
    }

    var filter_bd_area_list = $('select[name=\'filter_bd_area_list\']').val();

    if (filter_bd_area_list != 0) {
        url += '&filter_bd_area_list=' + encodeURIComponent(filter_bd_area_list);
    }

    var filter_firstorder = $('select[name=\'filter_firstorder\']').val();

    if (filter_firstorder != 0) {
        url += '&filter_firstorder=' + encodeURIComponent(filter_firstorder);
    }

    
    var filter_station = $('select[name=\'filter_station\']').val();

    if (filter_station != 0) {
        
        url += '&filter_station=' + encodeURIComponent(filter_station);
    }

    var filter_customer_id = $('input[name=\'filter_customer_id\']').val();

    if (filter_customer_id) {
        url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
    }

    return url;
}

function getAreaInfo(){
    var html = '';
    $.ajax({
        type:'POST',
        async: false,
        cache: false,
        url:'index.php?route=report/sale_order/getAreaInfo&token=<?php echo $_SESSION["token"]; ?>',
        dataType:'json',
        data: {
            filter_data : {
                filter_datatype : $('select[name=\'filter_datatype\']').val(),
                filter_date_start : $('input[name=\'filter_date_start\']').val(),
                filter_date_end : $('input[name=\'filter_date_end\']').val(),
                filter_order_status : $('select[name=\'filter_order_status\']').val(),
                filter_order_payment_status : $('select[name=\'filter_order_payment_status\']').val(),
                filter_order_deliver_status : $('select[name=\'filter_order_deliver_status\']').val(),
                filter_bd_list : $('select[name=\'filter_bd_list\']').val(),
                filter_logistic_list : $('select[name=\'filter_logistic_list\']').val(),
                filter_bd_area_list : $('select[name=\'filter_bd_area_list\']').val(),
                filter_firstorder : $('select[name=\'filter_firstorder\']').val(),
                filter_station : $('select[name=\'filter_station\']').val(),
                filter_customer_id : $('input[name=\'filter_customer_id\']').val()
            }
        },
        success : function(response){
            $.each(response,function(i,v){
                html += '<tr>';
                html += "<td>"+ v.area_name+"</td>";
                html += "<td>"+ v.sum_customer_count+"</td>";
                html += "<td>"+ v.sum_first_total+"</td>";
                html += "<td>"+ v.order_count+"</td>";
                html += "<td>"+ v.sum_sub_total+"</td>";
                html += "<td>"+ (v.sum_sub_total/ v.order_count).toFixed(4)+"</td>";
                html += "<td>"+ v.sum_discount+"</td>";
                html += "<td>"+ v.sum_credit_paid+"</td>";
                html += "<td>"+ v.sum_order_due+"</td>";
                html += "<td>"+ v.sum_user_point_paid+"</td>";
                html += "<td>"+ v.sum_wechat_paid+"</td>";

                html += '</tr>';
            });
            $('#area-ajax-info').html(html);
        }
    });
}


//--></script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>