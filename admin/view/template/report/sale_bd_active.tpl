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
                  <select name="filter_station_id" id="input_station_id" class="form-control">
                      <option value='0'>全部</option>
                      <option value="1" <?php echo $filter_station_id==1 ? 'selected="selected"' : ''; ?> >生鲜</option>
                      <option value="2" <?php echo $filter_station_id==2 ? 'selected="selected"' : ''; ?> >快消品</option>
                  </select>
              </div>
            </div>
            <!--<div class="col-sm-2">
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
            </div> -->

            <!--<div class="col-sm-2">
              <div class="form-group">
                  <label class="control-label">活跃度大于(下单/上线)</label>
                  <input type="text" name="filter_activity" id="input_activity" class="form-control" value="<?php echo $filter_activity; ?>">
              </div>
            </div>-->
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
                      <th>BD</th>
                      <th>订单数</th>
                      <th title="订单产生的金额">小计合</th>
                      <th>优惠金额</th>
                      <th>缺退货</th>
                      <th>客户退货</th>
                      <th title="订单金额减去优惠金额与缺退货金额">有效金额</th>
                      <th>客单价</th>
                      <th title="在此期间下单的用户">用户数</th>
                      <th>首单数</th>
                      <th>二单</th>
                      <th>三单</th>
                      <th>激活</th>
                      <th style="background-color: #d6d6d6">当前BD维护用户</th>
                      <th style="background-color: #d6d6d6">已下单</th>
                      <th style="background-color: #d6d6d6">未下单</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php if(sizeof($orders)){ ?>
                  <?php foreach($orders as $order){ ?>
                  <tr>
                      <td><?php echo $order['bd_name']; ?></td>
                      <td><?php echo $order['order_count']; ?></td>
                      <td><?php echo $order['sub_total']; ?></td>
                      <td><?php echo $order['discount_total']; ?></td>
                      <?php if(array_key_exists($order['bd_id'],$returns)){ ?>
                      <td><?php echo ($returns[$order['bd_id']]['r_sum'] > 0 ? '-':'' ).$returns[$order['bd_id']]['r_sum']; ?></td>
                      <td><?php echo ($returns[$order['bd_id']]['c_r_sum'] > 0 ? '-':'' ).$returns[$order['bd_id']]['c_r_sum']; ?></td>
                      <td><?php echo $order['sub_total']+$order['discount_total']-$returns[$order['bd_id']]['r_sum'] ; ?></td>
                      <?php }else{ ?>
                      <td>0</td>
                      <td>0</td>
                      <td><?php echo $order['sub_total']; ?></td>
                      <?php } ?>
                      <td><?php echo round($order['sub_total']/$order['sum_customer'],4); ?></td>
                      <td><?php echo $order['sum_customer']; ?></td>
                      <td><?php echo $order['firstorder_sum']; ?></td>
                      <td><?php echo $order['secondorder_sum']; ?></td>
                      <td><?php echo $order['thirdorder_sum']; ?></td>
                      <td><?php echo $order['awokenorder_sum']; ?></td>
                      <td style="background-color: #efefef">
                          <?php if(array_key_exists($order['bd_id'],$bd_customers)){ ?>
                          <?php echo $bd_customers[$order['bd_id']]['customers'] ; ?>
                          <?php }else{ ?>
                          <?php echo 0; ?>
                          <?php } ?>
                      </td>
                      <td style="background-color: #efefef">
                          <?php if(array_key_exists($order['bd_id'],$bd_ordered_customers)){ ?>
                          <?php echo $bd_ordered_customers[$order['bd_id']]['ordered_customers'] ; ?>
                          <?php }else{ ?>
                          <?php echo 0; ?>
                          <?php } ?>
                      </td>
                      <td style="background-color: #efefef">
                          <?php if(array_key_exists($order['bd_id'],$bd_customers)){ ?>
                          <?php echo $bd_customers[$order['bd_id']]['customers'] - (array_key_exists($order['bd_id'],$bd_ordered_customers) ? $bd_ordered_customers[$order['bd_id']]['ordered_customers'] : 0) ?>
                          <?php }else{ ?>
                          <?php echo 0; ?>
                          <?php } ?>
                      </td>
                  </tr>
                  <?php } ?>
                  <?php }else{ ?>
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
        url = 'index.php?route=report/sale_bd_perforemance&token=<?php echo $token; ?>';

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