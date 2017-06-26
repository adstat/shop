<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>分拣员工报表</h1>
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
                        <th>分拣人</th>
                        <th>开始分拣时间</th>
                        <th>结束分拣时间</th>
                        <th>分拣总数</th>
                        <th>分拣整箱数量</th>
                        <th>分拣散件数量</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                if (sizeof($sortingstaffs)) {
                  foreach($sortingstaffs as $sortingstaff){
              ?>
                    <tr>
                        <td><?php echo $sortingstaff['added_by']; ?></td>
                        <td><?php echo $sortingstaff['start_time']; ?></td>
                        <td><?php echo $sortingstaff['end_time']; ?></td>
                        <td><?php echo $sortingstaff['quantity']; ?></td>
                        <td><?php echo $sortingstaff['whole_quantity']; ?></td>
                        <td><?php echo $sortingstaff['spare_quantity']; ?></td>

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

        url = 'index.php?route=report/sorting_staff&token=<?php echo $token; ?>';

        var filter_date_start = $('input[name=\'filter_date_start\']').val();

        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').val();

        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }

        var filter_product_name = $('input[name=\'filter_product_name\']').val();

        if(filter_product_name) {
            url += '&filter_product_name=' + encodeURIComponent(filter_product_name);
        }

        return url;
    }


</script>

