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
                                <label class="control-label" for="input-station">订单平台<b style="color: #CC0000">生鲜/快消</b></label>
                                <select name="filter_station" id="input-station" class="form-control">
                                    <option value="">全部</option>

                                    <?php foreach($order_stations as $s_key=>$station){ ?>
                                    <?php if($s_key == $filter_station ){ ?>
                                    <option value="<?php echo $s_key; ?>" selected="selected" ><?php echo $station; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $s_key; ?>"><?php echo $station; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label" >物流司机</label>
                                <select name="filter_logistic_driver_list" id="input_logistic_driver_list" class="form-control">
                                    <option value='0'>全部</option>
                                    <?php foreach ($logistic_driver_list as $val) { ?>
                                    <?php if ($val['logistic_driver_id'] == $filter_logistic_driver_list) { ?>
                                    <option value="<?php echo $val['logistic_driver_id']; ?>" selected="selected"><?php echo $val['logistic_driver_title']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['logistic_driver_id']; ?>" ><?php echo $val['logistic_driver_title']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-date-start">配送开始日期</label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="input-date-start">配送结束日期</label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="order_payment_status">支付状态</label>

                                <select name="filter_order_payment_status" id="order_payment_status" class="form-control">
                                    <option value="0">全部</option>

                                    <?php foreach($order_payment_statuses as $m){ ?>
                                    <?php if($m['id'] == $filter_order_payment_status ){ ?>
                                    <option value="<?php echo $m['id'] ?>" selected="selected" ><?php echo $m['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $m['id'] ?>"><?php echo $m['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="order_deliver_status">配送状态</label>

                                <select name="filter_order_deliver_status" id="order_deliver_status" class="form-control">
                                    <option value="0">全部</option>

                                    <?php foreach($order_deliver_statuses as $m){ ?>
                                    <?php if($m['id'] == $filter_order_deliver_status ){ ?>
                                    <option value="<?php echo $m['id'] ?>" selected="selected" ><?php echo $m['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $m['id'] ?>"><?php echo $m['name']; ?></option>
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
                                <label class="control-label">用户区域</label>
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
                    </div>
                </div>
                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
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
            <div  class="table-responsive">
                <table class="table table-bordered" id="logistics">
                    <thead>
                    <tr>
                        <th>订单号</th>
                        <th>配送日期</th>
                        <th>仓库平台</th>
                        <th>司机编号</th>
                        <th>司机名称</th>
                        <th>配送地址</th>
                        <th>订单状态</th>
                        <th>支付状体</th>
                        <th>配送状态</th>
                        <th>行政区域</th>
                        <th>用户区域</th>
                        <th>篮框数</th>
                        <th>纸箱数</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($logistics as $logistic ) { ;?>
                            <tr>
                                <td><?php echo $logistic['order_id']; ?></td>
                                <td><?php echo $logistic['deliver_date'] ;?></td>
                                <td><?php echo $logistic['station_id']; ?></td>
                                <td><?php echo $logistic['logistic_driver_id']; ?></td>
                                <td><?php echo $logistic['logistic_driver_title']; ?></td>
                                <td><?php echo $logistic['shipping_address_1']; ?></td>
                                <td><?php echo $logistic['order_name']; ?></td>
                                <td><?php echo $logistic['payment_name']; ?></td>
                                <td><?php echo $logistic['deliver_name']; ?></td>
                                <td><?php echo $logistic['district']; ?></td>
                                <td><?php echo $logistic['name']; ?></td>
                                <td><?php echo $logistic['frame_count']; ?></td>
                                <td><?php echo $logistic['box_count']; ?></td>
                            </tr>
                    <?php } ; ?>
                    </tbody>
                </table>

            </div>
            <?php ; }?>
        </div>
    </div>
</div>
<script>
    $('.date').datetimepicker({
        pickTime: false
    });
</script>
<script>
    $('#button-filter').on('click', function() {
        location = getUrl();
    });
    $('#button-export').on('click',function() {
        url = getUrl();
        url += '&export=1';
        location = url;
    });

    function getUrl() {
        url = 'index.php?route=report/logistic_driver&token=<?php echo $token; ?>';

        var filter_station = $('select[name=\'filter_station\']').val();

        if (filter_station) {
            url += '&filter_station=' + encodeURIComponent(filter_station);
        }

        var filter_logistic_driver_list = $('select[name=\'filter_logistic_driver_list\']').val();

        if (filter_logistic_driver_list !=0) {
            url += '&filter_logistic_driver_list=' + encodeURIComponent(filter_logistic_driver_list);
        }

        var filter_date_start = $('input[name=\'filter_date_start\']').val();
        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').val();
        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }

        var filter_order_payment_status = $('select[name=\'filter_order_payment_status\']').val();
        if (filter_order_payment_status != '*') {
            url += '&filter_order_payment_status=' + encodeURIComponent(filter_order_payment_status);
        }

        var filter_order_deliver_status = $('select[name=\'filter_order_deliver_status\']').val();
        if (filter_order_deliver_status != '*') {
            url += '&filter_order_deliver_status=' + encodeURIComponent(filter_order_deliver_status);
        }

        var filter_order_status = $('select[name=\'filter_order_status\']').val();
        if (filter_order_status != '*') {
            url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
        }

        var filter_bd_area_list = $('select[name=\'filter_bd_area_list\']').val();

        if (filter_bd_area_list != 0) {
            url += '&filter_bd_area_list=' + encodeURIComponent(filter_bd_area_list);
        }

        var filter_area_list = $('select[name=\'filter_area_list\']').val();

        if (filter_area_list != 0) {
            url += '&filter_area_list=' + encodeURIComponent(filter_area_list);
        }


        return url;
    }
</script>

<?php echo $footer; ?>