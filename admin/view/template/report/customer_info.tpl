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
                <label class="control-label" for="input-report-type">会员日期类型</label>
                <select name="filter_report_type" id="input-report-type" class="form-control">
                  <option value="*"></option>

                  <?php foreach ($report_types as $report_type) { ?>
                  <?php if ($report_type['report_id'] == $filter_report_type) { ?>
                  <option value="<?php echo $report_type['report_id']; ?>" selected="selected"><?php echo $report_type['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $report_type['report_id']; ?>"><?php echo $report_type['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
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
            </div>

            <div class="col-sm-3">
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

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label">用户ID</label>
                <input type="text" name="filter_customer_id" id="input_customer_id"  value="<?php echo $filter_customer_id; ?>" placeholder="23,664,665..." class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label">用户名称</label>
                <input type="text" name="filter_customer_name" id="input_customer_name"  value="<?php echo $filter_customer_name; ?>" placeholder="张三" class="form-control" />
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
        <table class="table table-bordered" id="customers">
          <thead>
            <tr>
              <th class="text-left">用户编号</th>
              <th>会员等级</th>
              <th>用户名</th>
              <th>电话</th>
              <th>店名</th>
              <th>地址</th>
              <th>当前用户BD</th>
              <th>注册时间</th>
              <th>区域名称</th>
              <th>当前区域负责BD</th>
              <th>最早下单时间</th>
              <th>最后下单时间</th>
              <th>未下单天数</th>
              <th>最近一单</th>
            </tr>
          </thead>
          <tbody>
          <?php
              if (sizeof($customers)) {
                foreach($customers as $customer){
            ?>
             <tr>
               <td><?php echo $customer['customer_id']; ?></td>
               <td><?php echo $customer['customer_grade']; ?></td>
               <td><?php echo $customer['customer_name']; ?></td>
               <td><?php echo $customer['telephone']; ?></td>
               <td><?php echo $customer['merchant_name']; ?></td>
               <td><?php echo $customer['merchant_address']; ?></td>
               <td><?php echo $customer['bd_name']; ?></td>
               <td><?php echo $customer['registe_date']; ?></td>
               <td><?php echo $customer['area_name']; ?></td>
               <td><?php echo $customer['area_bd_name']; ?></td>
               <td><?php echo $customer['order_first_date']; ?></td>
               <td><?php echo $customer['order_recent_date']; ?></td>
               <td><?php echo $customer['order_no_dates']; ?></td>
               <td><?php echo $customer['recen_order']; ?></td>
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
      url = 'index.php?route=report/customer_info&token=<?php echo $token; ?>';

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

      var filter_customer_id = $('input[name=\'filter_customer_id\']').val();

      if (filter_customer_id) {
        url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
      }

      var filter_customer_group_id = $('select[name=\'filter_customer_group_id\']').val();

      if(filter_customer_group_id) {
        url += '&filter_customer_group_id=' + encodeURIComponent(filter_customer_group_id);
      }

      var filter_bd_area_list = $('select[name=\'filter_bd_area_list\']').val();

      if (filter_bd_area_list != 0) {
        url += '&filter_bd_area_list=' + encodeURIComponent(filter_bd_area_list);
      }

      var filter_customer_name = $('input[name=\'filter_customer_name\']').val();

      if (filter_customer_name) {
        url += '&filter_customer_name=' + encodeURIComponent(filter_customer_name);
      }

      var filter_report_type = $('select[name=\'filter_report_type\']').val();

      if (filter_report_type && filter_report_type != '*') {
        url += '&filter_report_type=' + encodeURIComponent(filter_report_type);
      }

      return url;
    }

  </script>

<script type="text/javascript"><!--
  $('.date').datetimepicker({
    pickTime: false
  });
  //--></script></div>

<?php echo $footer; ?>