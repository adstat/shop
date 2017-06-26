<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>分拣班组长报表</h1>
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
                                <label class="control-label">核对原因</label>
                                <select name="filter_check_id" id="input_check_id" class="form-control">
                                    <option value='0'>全部</option>
                                    <?php foreach ($check_list as $val) { ?>
                                    <?php if ($val['check_location_reason_id'] == $filter_check_id) { ?>
                                    <option value="<?php echo $val['check_location_reason_id']; ?>" selected="selected"><?php echo $val['reason_name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['check_location_reason_id']; ?>" ><?php echo $val['reason_name']; ?></option>
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
                        <th>旧货位号</th>
                        <th>新货位号</th>
                        <th>核对的筐号</th>
                        <th>出错原因</th>
                        <th>添加人</th>
                        <th>添加日期</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                if (sizeof($sortingleadershipments)) {
                  foreach($sortingleadershipments as $sortingleadershipment){
              ?>
                    <tr>
                        <td><?php echo $sortingleadershipment['order_id']; ?></td>
                        <td><?php echo $sortingleadershipment['old_inv_comment']; ?></td>
                        <td><?php echo $sortingleadershipment['new_inv_comment']; ?></td>
                        <td><?php echo $sortingleadershipment['container_id']; ?></td>
                        <td><?php echo $sortingleadershipment['reason_name']; ?></td>
                        <td><?php echo $sortingleadershipment['add_user']; ?></td>
                        <td><?php echo $sortingleadershipment['date_added']; ?></td>
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

        url = 'index.php?route=report/sorting_leader_shipment&token=<?php echo $token; ?>';

        var filter_date_start = $('input[name=\'filter_date_start\']').val();

        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').val();

        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }

        var filter_check_id = $('select[name=\'filter_check_id\']').val();

        if(filter_check_id) {
            url += '&filter_check_id=' + encodeURIComponent(filter_check_id);
        }

        return url;
    }


</script>

