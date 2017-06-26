<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header" style="display: none">
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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> 快消品库存</h3>
      </div>
      <div class="panel-body">
        <div style="margin:3px 0px; border-radius: 3px; background-color: #ECF3E6; border-color: #E3EBD5; padding: 5px;display: none;">
            <span style="color: #CC0000; font-size: 120%; font-weight: bold">选择日期类型: </span>
            <input type="radio" name="filter_datatype" <?php if($filter_datatype !== 1){ echo 'checked="checked"'; } ?> value=2 /> 下单日期
            <input type="radio" name="filter_datatype" <?php if($filter_datatype == 1){ echo 'checked="checked"'; } ?> value=1 /> 配送日期 &nbsp;&nbsp;&nbsp;(注意：订单生鲜信息隔天更新)
        </div>
          <div class="well" style="display:none;">
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
                <label class="control-label">订单状态</label>
                <select name="filter_order_status" id="input_order_status" class="form-control">
                  <option value='0'>全部(不含取消)</option>
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
                  <label class="control-label">市场开发(BD)人员</label>
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
                  <label class="control-label">用户ID</label>
                      <input type="text" name="filter_customer_id" id="input_customer_id"  value="<?php echo $filter_customer_id; ?>" placeholder="23,664,665,..." class="form-control" />
              </span></div>
              </div>
            </div>

            
            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
          </div>

          <?php if($date_gap > 31) { ?>
            <div class="alert alert-warning">
                查询时间不可大于31天!
            </div>
          <?php } ?>

         
       </div>
        盘点时间：<?php echo $inv_check_date;?>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-left">商品ID</th>
                <th>货位号</th>
                <th>商品名称</th>
                <th>盘点库存</th>
                <th>盘点库存调整</th>
                <th>采购入库</th>
                <th>订单出库</th>
                <th>退货入库</th>
                <th>商品报损</th>
                <th>转促销品</th>
                <th>当前库存</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if (sizeof($inv_mi_cold_arr)) {
                  foreach($inv_mi_cold_arr as $inv_key=>$inv_mi_cold){
              ?>
                <tr>
                    <td><?php echo $inv_key; ?></td>
                    <td><?php echo $inv_mi_cold['inv_class_sort'];?></td>
                <td><?php echo $inv_mi_cold['name']; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['14']) ? $inv_mi_cold['quantity']['14'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['16']) ? $inv_mi_cold['quantity']['16'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['11']) ? $inv_mi_cold['quantity']['11'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['12']) ? $inv_mi_cold['quantity']['12'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['8']) ? $inv_mi_cold['quantity']['8'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['13']) ? $inv_mi_cold['quantity']['13'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['15']) ? $inv_mi_cold['quantity']['15'] : 0; ?></td>
                <td class="text-center"><?php echo $inv_mi_cold['sum_quantity']; ?></td>
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

function getUrl(){
    url = 'index.php?route=report/sale_order&token=<?php echo $token; ?>';

    var filter_datatype = $('input[name=\'filter_datatype\']').val();

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

    var filter_bd_list = $('select[name=\'filter_bd_list\']').val();

    if (filter_bd_list != 0) {
        url += '&filter_bd_list=' + encodeURIComponent(filter_bd_list);
    }

    var filter_customer_id = $('input[name=\'filter_customer_id\']').val();

    if (filter_customer_id) {
        url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
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