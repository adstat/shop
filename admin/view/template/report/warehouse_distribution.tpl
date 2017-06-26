<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>仓库分拣报表</h1>
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
                                <label class="control-label">选择商品属性</label>
                                <select name="filter_product_type" id="input_product_type" class="form-control">
                                    <option value='0'>全部</option>
                                    <?php foreach ($product_types as $val) { ?>
                                    <?php if ($val['product_type_id'] == $filter_product_type) { ?>
                                    <option value="<?php echo $val['product_type_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['product_type_id']; ?>" ><?php echo $val['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>



                        </div>
                        <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">分拣人</label>
                            <input type="text" name="filter_product_name" id="input_product_name"  value="<?php echo $filter_product_name; ?>" placeholder="..." class="form-control" />
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
                        <th>平台仓库</th>
                        <th>商品分类属性</th>
                        <th>下单时间</th>
                        <th>分拣人</th>
                        <th>分拣数量</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                if (sizeof($orderdistrs)) {
                  foreach($orderdistrs as $orderdistr){
              ?>
                    <tr>
                        <td><?php echo $orderdistr['order_id']; ?></td>
                        <td><?php echo $orderdistr['station_name']; ?></td>
                        <td><?php echo $orderdistr['product_name']; ?></td>
                        <td><?php echo $orderdistr['date_added']; ?></td>
                        <td><?php echo $orderdistr['inventory_name']; ?></td>
                        <td><?php echo $orderdistr['quantity']; ?></td>
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

            url = 'index.php?route=report/warehouse_distribution&token=<?php echo $token; ?>';

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

            var filter_product_name = $('input[name=\'filter_product_name\']').val();

           if(filter_product_name) {
                url += '&filter_product_name=' + encodeURIComponent(filter_product_name);
            }

            var filter_product_type = $('select[name=\'filter_product_type\']').val();
            if(filter_product_type) {
                url += '&filter_product_type=' + encodeURIComponent(filter_product_type);
            }

            return url;
        }


    </script>

