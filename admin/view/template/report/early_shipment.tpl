<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>早班出库报表</h1>
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
                                <label class="control-label">是否是外仓</label>
                                <select name="filter_instock_id" id="input_instock_id" class="form-control">
                                    <option value="2">全部</option>
                                    <option value="0" <?php if($filter_instock_id == 0) { ?> selected == "selected" <?php } ?>>是</option>
                                    <option value="1" <?php if($filter_instock_id == 1) { ?> selected == "selected" <?php } ?>>否</option>



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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">是否散发</label>
                                <select name="filter_repack_id" id="input_repack_id" class="form-control">
                                    <option value="2">全部</option>
                                    <option value="0" <?php if($filter_repack_id == 0) { ?> selected == "selected" <?php } ?>>否</option>
                                    <option value="1" <?php if($filter_repack_id == 1) { ?> selected == "selected" <?php } ?>>是</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label">回库原因</label>
                                <select name="filter_return_id" id="input_return_id" class="form-control">
                                    <option value='0'>全部</option>
                                    <?php foreach ($return_list as $val) { ?>
                                    <?php if ($val['return_reason_id'] == $filter_return_id) { ?>
                                    <option value="<?php echo $val['return_reason_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['return_reason_id']; ?>" ><?php echo $val['name']; ?></option>
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
            <div class="table-responsive">
                <table class="table table-bordered" id="totals">
                    <thead>
                    <tr>
                        <th>订单号</th>
                        <th>下单日期</th>
                        <th>区域</th>
                        <th>商品编号</th>
                        <th>商品名称</th>
                        <th>是否外仓</th>
                        <th>是否散发</th>
                        <th>订单商品数量</th>
                        <th>订单商品金额</th>
                        <th>分拣数量</th>
                        <th>散件遗失数量</th>
                        <th>散件遗失金额</th>
                        <th>分拣人</th>
                        <th>配送司机</th>
                        <th>司机电话</th>
                        <th>记录人</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                if (sizeof($earlyshipments)) {
                  foreach($earlyshipments as $earlyshipment){
              ?>
                    <tr>
                        <td><?php echo $earlyshipment['order_id']; ?></td>
                        <td><?php echo $earlyshipment['date_added']; ?></td>
                        <td><?php echo $earlyshipment['name']; ?></td>
                        <td><?php echo $earlyshipment['product_id']; ?></td>
                        <td><?php echo $earlyshipment['product_name']; ?></td>
                        <td><?php echo $earlyshipment['instock']; ?></td>
                        <td><?php echo $earlyshipment['repack']; ?></td>
                        <td><?php echo $earlyshipment['order_qty']; ?></td>
                        <td><?php echo $earlyshipment['order_total']; ?></td>
                        <td><?php echo $earlyshipment['quantity']; ?></td>
                        <td><?php echo $earlyshipment['deliver_missing_qty']; ?></td>
                        <td><?php echo $earlyshipment['deliver_missing_total']; ?></td>
                        <td><?php echo $earlyshipment['added_by']; ?></td>
                        <td><?php echo $earlyshipment['logistic_driver_title']; ?></td>
                        <td><?php echo $earlyshipment['logistic_driver_phone']; ?></td>
                        <td><?php echo $earlyshipment['adduser']; ?></td>
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
        location = getUrl();
    });

    $('#button-export').on('click',function() {
        url = getUrl();
        url += '&export=1';
        location = url;
    });

    function getUrl() {

        url = 'index.php?route=report/early_shipment&token=<?php echo $token; ?>';

        var filter_date_start = $('input[name=\'filter_date_start\']').val();

        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').val();

        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }

        var filter_instock_id = $('select[name=\'filter_instock_id\']').val();

        if(filter_instock_id) {
            url += '&filter_instock_id=' + encodeURIComponent(filter_instock_id);
        }

        var filter_repack_id = $('select[name=\'filter_repack_id\']').val();

        if(filter_repack_id) {
            url += '&filter_repack_id=' + encodeURIComponent(filter_repack_id);
        }

        var filter_return_id = $('select[name=\'filter_return_id\']').val();

        if(filter_return_id) {
            url += '&filter_return_id=' + encodeURIComponent(filter_return_id);
        }

        var filter_logistic_list = $('select[name=\'filter_logistic_list\']').val();
        if(filter_logistic_list) {
            url += '&filter_logistic_list=' + encodeURIComponent(filter_logistic_list);
        }

        return url;
    }


</script>

