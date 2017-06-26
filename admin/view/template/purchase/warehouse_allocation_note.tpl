<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="添加仓库出库单" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-purchase-order-id">仓库出库单ID</label>
                                <input type="text" name="filter_warehouse_order_id" value="<?php echo $filter_warehouse_order_id; ?>" placeholder="仓库出库单ID" id="input-warehouse-order-id" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input_warehouse_id">出库类型</label>
                                <select name="out_type" id="input-out_type" class="form-control" >
                                    <option value="">选择出库类型</option>
                                    <option value="1">出库单</option>
                                    <option value="2">仓间调拨单</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input_date_deliver">下单日期</label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_deliver" value="<?php echo $filter_date_deliver ;  ?>" placeholder="<?php echo $filterDefault['deliver_date']; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input_warehouse_id">调往仓库</label>
                                <select name="warehouse_id" id="input-warehouse_id" class="form-control" >
                                    <option value="">选择需要调往的仓库</option>
                                    <?php foreach ($warehouse as $val) { ?>
                                    <?php if ($val['warehouse_id'] == $warehouse_id) { ?>
                                    <option value="<?php echo $val['warehouse_id']; ?>" selected="selected"><?php echo $val['title']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['warehouse_id']; ?>" ><?php echo $val['title']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-order-status">出库单状态</label>
                                <select name="filter_order_status_id" id="input-order-status" class="form-control">
                                    <option value="">- -</option>
                                    <?php foreach ($warehouse_status_id as $warehouse_status) { ?>
                                    <?php if ($warehouse_status['relevant_status_id'] == $filter_order_status_id) { ?>
                                    <option value="<?php echo $warehouse_status['relevant_status_id']; ?>" selected="selected"><?php echo $warehouse_status['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $warehouse_status['relevant_status_id']; ?>"><?php echo $warehouse_status['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>
                <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <td class="text-left">出库单号</td>
                                <td class="text-left">出库类型</td>
                                <td class="text-left">调往仓库</td>
                                <td class="text-left">添加时间</td>
                                <td class="text-left">添加人</td>
                                <td class="text-left">预计出库时间</td>
                                <td class="text-left">出库单状态</td>
                                <td class="text-left">备注</td>
                                <td class="text-left">操作</td>

                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($warehouse_requisition as $val) { ;?>
                                <tr>
                                    <td class="text-left"><?php echo $val['relevant_id'] ;?></td>
                                    <td class="text-left"><?php echo $val['out_type'] ;?></td>
                                    <td class="text-left"><?php echo $val['to_warehouse'] ;?></td>
                                    <td class="text-left"><?php echo $val['date_added'] ;?></td>
                                    <td class="text-left"><?php echo $val['add_user'] ;?></td>
                                    <td class="text-left"><?php echo $val['deliver_date'] ;?></td>
                                    <?php if($val['relevant_status_id'] ==1) { ;?>
                                    <td class="text-left" id="staus_<?php echo $val['relevant_id'] ;?>"><?php echo $val['warehouse_status'] ;?>
                                    <input type="button" id="button_<?php echo $val['relevant_id'] ;?>" value="确认" onclick="confirm(<?php echo $val['relevant_id'] ;?>)">
                                    </td>

                                    <?php }else { ;?>
                                    <td class="text-left"><?php echo $val['warehouse_status'] ;?></td>
                                    <?php } ;?>
                                    <td class="text-left"><?php echo $val['comment'] ;?></td>
                                    <td> <a href="<?php echo $val['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a></td>
                                </tr>
                            <?php }?>

                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#button-filter').on('click', function() {
        location = getUrl();
    });
    $('#button-export').on('click',function() {
        url = getUrl();
        url += '&export=1';
        location = url;
    });

    function getUrl(){
        var warehouse_id = $("#warehouse_id_global").val();
        url = 'index.php?route=purchase/warehouse_allocation_note&token=<?php echo $token; ?>&filter_warehouse_id_global='+ warehouse_id;

        var filter_warehouse_order_id = $('input[name=\'filter_warehouse_order_id\']').val();

        if(filter_warehouse_order_id) {
            url += '&filter_warehouse_order_id=' + encodeURIComponent(filter_warehouse_order_id);
        }

        var filter_date_deliver = $('input[name=\'filter_date_deliver\']').val();
        if(filter_date_deliver) {
            url += '&filter_date_deliver=' + encodeURIComponent(filter_date_deliver);
        }

        var warehouse_id = $('select[name=\'warehouse_id\']').val();
        if(warehouse_id) {
            url += '&warehouse_id=' + encodeURIComponent(warehouse_id);
        }
        var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();
        if(filter_order_status_id) {
            url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
        }

        var filter_out_type = $('select[name=\'out_type\']').find("option:selected").text();
        if(filter_out_type) {
            url += '&filter_out_type=' + encodeURIComponent(filter_out_type);
        }
        var filter_out_type_id = $('select[name=\'out_type\']').val();
        if(filter_out_type_id) {
            url += '&filter_out_type=' + encodeURIComponent(filter_out_type_id);
        }
        return url;
    }


    function confirm(relevant_id){
        $.ajax({
            type:'POST',
            async: false,
            cache: false,
            url:  'index.php?route=purchase/warehouse_allocation_note/confirm&token=<?php echo $token; ?>&relevant_id='+ relevant_id,
            success: function(response) {
                if(response == 1){
                    $("#button_"+relevant_id).hide();
                    $("#staus_"+relevant_id).text('已确认');
                }
            },

        });
    }
</script>

    <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
    <script type="text/javascript"><!--
        $('.date').datetimepicker({
            pickTime: false
        });
        //--></script>


