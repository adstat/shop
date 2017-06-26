<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>库内丢失确认</h1>
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
                                <label class="control-label">是否确认</label>
                                <select name="filter_confirm_id" id="input_confirm_id" class="form-control">
                                    <option value="2">全部</option>
                                    <?php foreach ($confirm_id as $val) { ?>
                                    <?php if ($val['confirm_id'] == $filter_confirm_id) { ?>
                                    <option value="<?php echo $val['confirm_id']; ?>"  selected="selected"><?php echo $val['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['confirm_id']; ?>"  ><?php echo $val['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label">是否有效</label>
                                <select name="filter_status_id" id="input_status_id" class="form-control">
                                    <option value="2">全部</option>
                                    <?php foreach ($status_id as $val) { ?>
                                    <?php if ($val['status_id'] == $filter_status_id) { ?>
                                    <option value="<?php echo $val['status_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['status_id']; ?>"   ><?php echo $val['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">订单号</label>
                                <input type="text" name="filter_order_id" id="input_order_id"  value="<?php echo $filter_order_id; ?>" placeholder="..." class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label">是否找回</label>
                                <select name="filter_recover_id" id="input_recover_id" class="form-control">
                                    <option value="2">全部</option>
                                    <?php foreach ($recover_id as $val) { ?>
                                    <?php if ($val['recover_id'] == $filter_recover_id) { ?>
                                    <option value="<?php echo $val['recover_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['recover_id']; ?>" ><?php echo $val['name']; ?></option>
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
                        <th>添加人</th>
                        <th>添加时间</th>
                        <th>确认人</th>
                        <th>确认时间</th>
                        <th>是否确认</th>
                        <th>确认提交时间</th>
                        <th>找回人</th>
                        <th>找回时间</th>
                        <th>是否找回</th>
                        <th>是否有效</th>
                        <th>备注</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                if (sizeof($inventoryordermissings)) {
                  foreach($inventoryordermissings as $inventoryordermissing){
              ?>
                    <tr>
                        <td><?php echo $inventoryordermissing['order_id']; ?></td>
                        <td><?php echo $inventoryordermissing['added_by']; ?></td>
                        <td><?php echo $inventoryordermissing['date_added']; ?></td>
                        <td><?php echo $inventoryordermissing['confirmed_name']; ?></td>
                        <td>主管确认时间：<?php echo $inventoryordermissing['supervisor_checked']; ?>
                            <br>
                            视频确认时间：<?php echo $inventoryordermissing['monitor_checked']; ?>
                        </td>
                        <td><?php if ($inventoryordermissing['confirmed'] == 0) { ?>
                             否
                            <?php } else { ?>
                             是
                            <?php } ?>
                        </td>
                        <td><?php echo $inventoryordermissing['date_confirmed']; ?></td>
                        <td><?php echo $inventoryordermissing['recovered_name']; ?></td>
                        <td><?php echo $inventoryordermissing['date_recovered']; ?></td>
                        <td><?php if ($inventoryordermissing['recovered'] == 0) { ?>
                            否
                            <?php } else { ?>
                            是
                            <?php } ?>
                        </td>
                        <td><?php if ($inventoryordermissing['status'] == 0) { ?>
                            否
                            <?php } else { ?>
                            是
                            <?php } ?>
                        </td>

                        <td><?php echo $inventoryordermissing['memo']; ?></td>
                        <td><button name="button-show[]" id="butto-show[]" type="button" value="<?php echo $inventoryordermissing['order_id']; ?>" class="change_status" style="color:#117700" onclick="confirmed_order(<?php echo $inventoryordermissing['order_id']; ?> )">确认丢失</button>
                            <br>
                            <button name="button-show[]" id="butto_show<?php echo $inventoryordermissing['order_id']; ?>" type="button" value="<?php echo $inventoryordermissing['order_id']; ?>" class="change_status" style="color:#117700" onclick="recovered_reason(<?php echo $inventoryordermissing['order_id']; ?>)">找回原因</button>
                            <button name="button_recover" id="button_recover<?php echo $inventoryordermissing['order_id']; ?>" type="button" value="<?php echo $inventoryordermissing['order_id']; ?>" class="change_status" style="display:none" onclick="recovered_order(<?php echo $inventoryordermissing['order_id']; ?>)">订单找回提交</button>
                      <input  name = "reasons" style="display:none" id = "reasons<?php echo $inventoryordermissing['order_id']; ?>">

                        </td>
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

        url = 'index.php?route=user/inventory_order_missing&token=<?php echo $token; ?>';

        var filter_date_start = $('input[name=\'filter_date_start\']').val();

        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').val();

        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }

        var filter_order_id = $('input[name=\'filter_order_id\']').val();

        if(filter_order_id) {
            url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
        }

        var filter_confirm_id = $('select[name=\'filter_confirm_id\']').val();

        if(filter_confirm_id) {
            url += '&filter_confirm_id=' + encodeURIComponent(filter_confirm_id);
        }

        var filter_status_id = $('select[name=\'filter_status_id\']').val();

        if(filter_status_id) {
            url += '&filter_status_id=' + encodeURIComponent(filter_status_id);
        }

        var filter_recover_id = $('select[name=\'filter_recover_id\']').val();

        if(filter_recover_id) {
            url += '&filter_recover_id=' + encodeURIComponent(filter_recover_id);
        }

        return url;
    }


</script>
<script>

    function  confirmed_order(order_id){
        if(confirm("确认订单已经丢失？确认之后，订单将会重新分拣，之前的分拣记录跟分车记录将会被删除")) {
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=user/inventory_order_missing/confirmed_order&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                data: {
                    order_id: order_id,
                },
                success: function (response) {
                    if (response == 2) {
                        alert('只有分拣完的订单才能确认丢失，该订单不能做此操作');
                    } else if (response == 3 )  {
                        alert('该订单已经被确认过一次，不能再次被确认！');
                    }else{
                        alert('提交成功');
                        window.location.reload();
                    }
                }


            });
        }
    }

    function recovered_reason(order_id){
        $("#reasons"+order_id).show();
        $("#button_recover"+order_id).show();
        $("#butto_show"+order_id).hide();

    }

    function recovered_order(order_id){
        var  reasons =   $("#reasons"+order_id).val();
        var  reasons_length = $("#reasons"+order_id).val().length;
            if(reasons_length  == 0){
                    alert('找回的原因必须填写');
            }else {
                if(confirm("确认订单已经找回？")) {
                    $.ajax({
                        type: 'POST',
                        async: false,
                        cache: false,
                        url: 'index.php?route=user/inventory_order_missing/recovered_order&token=<?php echo $_SESSION["token"]; ?>',
                        dataType: 'json',
                        data: {
                            reasons:reasons,
                            order_id: order_id,
                        },
                        success: function (response) {
                            if (response == 2) {
                               alert('已经提过一次，不能再次提交');

                            }else {
                                alert('提交成功');
                                window.location.reload();
                            }
                        }


                    });
                }
            }


    }
</script>

