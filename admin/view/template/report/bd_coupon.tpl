<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>优惠券使用报表</h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid" id="bdcoupon">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>

            </div>
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">选择仓库</label>
                                <select name="filter_station_id" id="input_station_id" class="form-control">
                                    <option value='0'>全部</option>
                                    <?php foreach ($stations as $val) { ?>
                                    <?php if ($val['station_id'] == $filter_station_id) { ?>
                                    <option value="<?php echo $val['station_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['station_id']; ?>" ><?php echo $val['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">BD列表</label>
                                <select multiple="multiple" name="filter_bd_list" id="input_bd_list"  style="width: 230px">
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-date-start">开始日期</label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="开始日期" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">区域列表</label>
                                <select name="filter_bd_area_list" id="input_bd_area_list"  multiple="multiple" style="width: 230px;">
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
                                <label class="control-label" for="input-date-start">结束日期</label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="结束日期" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">优惠券编号</label>
                                <input type="text" name="filter_bd_num" id="input_bd_num" class="form-control" placeholder="123,456" value="<?php echo $filter_bd_num; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>

                <?php if(!$nofilter) { ?>
                <div class="table-responsive">
                    <button type="button" id="button-export" class="btn btn-primary pull-right" style="margin:0 5px;"><i class="fa fa-bar-chart"></i>导出EXCEL</button>
                </div>
                <?php } ?>
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
                        <th>优惠券</th>
                        <th>订单号</th>
                        <th>订单状态</th>
                        <th>下单日期</th>
                        <th>商家ID</th>
                        <th>商家地址</th>
                        <th>BD归属</th>
                        <th>商家会员等级</th>
                        <th>商家区域</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                if (sizeof($bdcoupons)) {
                  foreach($bdcoupons as $bdcoupon){
              ?>
                    <tr>
                        <td><?php echo $bdcoupon['coupon_id']; ?>/ <?php echo $bdcoupon['coupon_name']; ?></td>
                        <td><?php echo $bdcoupon['order_id']; ?></td>
                        <td><?php echo $bdcoupon['status_name']; ?></td>
                        <td><?php echo $bdcoupon['date_added']; ?></td>
                        <td><?php echo $bdcoupon['customer_id']; ?></td>
                        <td><?php echo $bdcoupon['merchant_address']; ?></td>
                        <td><?php echo $bdcoupon['bd_name']; ?></td>
                        <td><?php echo $bdcoupon['level_name']; ?></td>
                        <td><?php echo $bdcoupon['area_name']; ?></td>
                    </tr>

                    <?php
                 }
               } else {
              ?>
                    <tr>
                        <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
                    </tr>
                    <?php }?>
                    </tbody>

                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('.date').datetimepicker({
        pickTime: false
    });
</script>
<script type="text/javascript">
    $('#button-filter').on('click', function() {
        var couponIds = $('input[name=\'filter_bd_num\']').val();
        if(couponIds == ''){
            alert('请输入优惠券编号, 可半角逗号分割输入多个编号。');
            return false;
        }

        location = getUrl();
    });

    $('#button-export').on('click',function() {
        var couponIds = $('input[name=\'filter_bd_num\']').val();
        if(couponIds == ''){
            alert('请输入优惠券编号, 可半角逗号分割输入多个编号。');
            return false;
        }

        url = getUrl();
        url += '&export=1';
        location = url;
    });

    function getUrl() {

        url = 'index.php?route=report/bd_coupon&token=<?php echo $token; ?>';

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

        var filter_bd_list = $('select[name=\'filter_bd_list\']').val();
        if(filter_bd_list) {
            url += '&filter_bd_list=' + encodeURIComponent(filter_bd_list);
        }

        var filter_bd_area_list = $('select[name=\'filter_bd_area_list\']').val();
        if(filter_bd_area_list) {
            url += '&filter_bd_area_list=' + encodeURIComponent(filter_bd_area_list);
        }

        var filter_bd_num = $('input[name=\'filter_bd_num\']').val();

        if(filter_bd_num) {
            url += '&filter_bd_num=' + encodeURIComponent(filter_bd_num);
        }

        return url;
    }

  </script>
<script src="view/javascript/multiple-select/multiple-select.js"></script>
<link href="view/javascript/multiple-select/multiple-select.css" rel="stylesheet"/>

<script>
    $('#input_bd_list').multipleSelect();
    $('#input_bd_area_list').multipleSelect();
</script>