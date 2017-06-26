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
  <div class="container-fluid" id="purchasereport">
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
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-end">结束日期</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-order-type">类型</label>
                <select name="filter_order_type" id="input-order-type" class="form-control">
                  <option value="*"></option>

                  <?php foreach ($order_types as $order_type) { ?>
                  <?php if ($order_type['order_type_id'] == $filter_order_type) { ?>
                  <option value="<?php echo $order_type['order_type_id']; ?>" selected="selected"><?php echo $order_type['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_type['order_type_id']; ?>"><?php echo $order_type['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-supplier-type">供应商</label>
                <select name="filter_supplier_type" id="input-supplier-type" class="form-control">
                  <option value="*"></option>

                  <?php foreach ($supplier_types as $supplier_type) { ?>
                  <?php if ($supplier_type['supplier_id'] == $filter_supplier_type) { ?>
                  <option value="<?php echo $supplier_type['supplier_id']; ?>" selected="selected"><?php echo $supplier_type['supplier_id'].'#'.$supplier_type['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $supplier_type['supplier_id']; ?>"><?php $supplier_type['supplier_id'].'#'.echo $supplier_type['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-purchase-order-id">采购单ID</label>
                <input type="text" name="filter_purchase_order_id" value="<?php echo $filter_purchase_order_id; ?>" placeholder="采购单ID" id="input-purchase-order-id" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-order-status">订单状态</label>
                <select name="filter_order_status_id" id="input-order-status" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $filter_order_status_id) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-order-checkout-status">订单支付状态</label>
                <select name="filter_order_checkout_status_id" id="input-order-checkout-status" class="form-control">
                  <option value="*"></option>
                  <?php foreach ($order_checkout_statuses as $order_checkout_status) { ?>
                  <?php if ($order_checkout_status['order_checkout_status_id'] == $filter_order_checkout_status_id) { ?>
                  <option value="<?php echo $order_checkout_status['order_checkout_status_id']; ?>" selected="selected"><?php echo $order_checkout_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_checkout_status['order_checkout_status_id']; ?>"><?php echo $order_checkout_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
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
            <button type="button" id="button-export" class="btn btn-primary pull-right" style="margin:0 5px;"><i class="fa fa-bar-chart"></i>导出EXCEL</button>
          </div>
          <?php } ?>

      </div>

      <?php if(!$nofilter) { ?>
      <div class="table-responsive">
        <table class="table table-bordered" id="purchases">
          <thead>
            <tr>
              <th class="text-left">采购单号</th>
              <th style="display: none">下单日期</th>
              <th style="display: none">计划到货日期</th>
              <th style="display: none">实际收货日期</th>
              <th style="display: none">供应商编号</th>
              <th style="display: none">供应商名称</th>
              <th>支付状态</th>
              <th>采购单状态</th>
              <th>采购单类型</th>
              <th>采购单金额</th>
              <th>实收金额</th>
              <th>是否有发票</th>
              <th>账户余额支付</th>
              <th>下单人员</th>
              <th>收款人</th>
              <th style="display: none">收款银行</th>
              <th style="display: none">收款账号</th>
              <th>收款时间</th>
              <th>打款确认</th>
              <th>支付类型</th>
            </tr>
          </thead>
          <tbody>
          <?php
              if (sizeof($purchases)) {
                foreach($purchases as $purchase){
            ?>
              <tr>
                <td><?php echo $purchase['purchase_order_id']; ?></td>
                <td style="display: none"><?php echo $purchase['date_purchase']; ?></td>
                <td style="display: none"><?php echo $purchase['date_deliver_plan']; ?></td>
                <td style="display: none"><?php echo $purchase['date_deliver_receive']; ?></td>
                <td style="display: none"><?php echo $purchase['supplier_id']; ?></td>
                <td style="display: none"><?php echo $purchase['supplier_name']; ?></td>
                <td><?php echo $purchase['checkout_status']; ?></td>
                <td><?php echo $purchase['purchase_status']; ?></td>
                <td><?php echo $purchase['purchase_type']; ?></td>
                <td><?php echo $purchase['order_total']; ?></td>
                <td><?php echo $purchase['order_real_total']; ?></td>
                <td><?php echo $purchase['invoice']; ?></td>
                <td><?php echo $purchase['credits_total']; ?></td>
                <td><?php echo $purchase['add_user_name']; ?></td>
                <td><?php echo $purchase['checkout_username']; ?></td>
                <td style="display: none"><?php echo $purchase['checkout_userbank']; ?></td>
                <td style="display: none"><?php echo $purchase['checkout_usercard']; ?></td>
                <td><?php echo $purchase['checkout_time']; ?></td>
                <td><?php echo $purchase['user_confirm']; ?></td>
                <td><?php echo $purchase['pay_type']; ?></td>
              </tr>
          <?php
                   }
                 } else {
                ?>
              <tr>
                <td class="text-center" colspan="20"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
          </tbody>
        </table>
      </div>
      <?php } ?>
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

  function getUrl(){
    url = 'index.php?route=report/purchase_order&token=<?php echo $token; ?>';

    var filter_date_start = $('input[name=\'filter_date_start\']').val();

    if (filter_date_start) {
      url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
    }

    var filter_date_end = $('input[name=\'filter_date_end\']').val();

    if (filter_date_end) {
      url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
    }

    var filter_supplier_type = $('select[name=\'filter_supplier_type\']').val();

    if (filter_supplier_type && filter_supplier_type != '*') {
      url += '&filter_supplier_type=' + encodeURIComponent(filter_supplier_type);
    }

    var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();

    if (filter_order_status_id && filter_order_status_id != '*') {
      url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
    }

    var filter_order_checkout_status_id = $('select[name=\'filter_order_checkout_status_id\']').val();

    if (filter_order_checkout_status_id && filter_order_checkout_status_id != '*') {
      url += '&filter_order_checkout_status_id=' + encodeURIComponent(filter_order_checkout_status_id);
    }

    var filter_purchase_order_id = $('input[name=\'filter_purchase_order_id\']').val();

    if (filter_purchase_order_id && filter_purchase_order_id != '') {
      url += '&filter_purchase_order_id=' + encodeURIComponent(filter_purchase_order_id);
    }

    var filter_order_type = $('select[name=\'filter_order_type\']').val();

    if (filter_order_type && filter_order_type != '*') {
      url += '&filter_order_type=' + encodeURIComponent(filter_order_type);
    }

    return url;
  }
  $('.date').datetimepicker({
    pickTime: false
  });
</script>
</div>
<?php echo $footer; ?>