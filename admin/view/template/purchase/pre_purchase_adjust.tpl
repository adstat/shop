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
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-purchase-order-id">采购单ID</label>

                                <input type="text" name="filter_purchase_order_id" value="<?php echo $filter_purchase_order_id; ?>" placeholder="采购单ID" id="input-purchase-order-id" class="form-control" />
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="input-supplier-type">供应商</label>
                                <select name="filter_supplier_type" id="input-supplier-type" class="form-control">
                                    <option value="*"></option>

                                    <?php foreach ($supplier_types as $supplier_type) { ?>
                                        <?php if ($supplier_type['supplier_id'] == $filter_supplier_type) { ?>
                                            <option value="<?php echo $supplier_type['supplier_id']; ?>" selected="selected"><?php echo $supplier_type['supplier_id'] .'#'. $supplier_type['name']; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $supplier_type['supplier_id']; ?>"><?php echo $supplier_type['supplier_id'] .'#'. $supplier_type['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="input_purchase_person_id">采购人员</label>
                                <select name="filter_purchase_person_id" id="input-purchase-person-id" class="form-control">
                                    <option value="*"></option>
                                    <?php foreach ($purchase_person as $purchase_person) { ?>
                                        <?php if ($purchase_person['user_id'] == $filter_purchase_person_id) { ?>
                                            <option value="<?php echo $purchase_person['user_id']; ?>" selected="selected"><?php echo $purchase_person['user_id'] .'#'. $purchase_person['name']; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $purchase_person['user_id']; ?>"><?php echo $purchase_person['user_id'] .'#'. $purchase_person['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input_date_deliver">到货日期开始</label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_deliver" value="<?php echo $filter_date_deliver; ?>" placeholder="到货日期开始" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="input_date_deliver_end">到货日期结束</label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_deliver_end" value="<?php echo $filter_date_deliver_end; ?>" placeholder="到货日期结束" data-date-format="YYYY-MM-DD" id="input-date-modified-end" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
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

                            <input type="hidden" name="filter_warehouse_id_global" value="<?php echo $filter_warehouse_id_global; ?>">
                            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>

                <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td class="text-right">
                                        <?php if ($sort == 'o.purchase_order_id') { ?>
                                            <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">供应商分类</td>
                                    <td class="text-left">商品属性</td>
                                    <td class="text-left">订单总额</td>
                                    <td class="text-left">收货总额</td>
                                    <td class="text-left">添加人</td>
                                    <td class="text-left">添加时间</td>
                                    <td class="text-left">订单状态</td>
                                    <td class="text-left">付款状态</td>
                                    <td class="text-left">是否有发票</td>
                                    <td class="text-right">
                                        <?php if ($sort == 'o.date_deliver') { ?>
                                            <a href="<?php echo $sort_date_deliver; ?>" class="<?php echo strtolower($order); ?>">实际到货日期</a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_date_deliver; ?>">实际到货日期</a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">审核状态</td>
                                    <td class="text-right"><?php echo $column_action; ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($orders) { ?>
                                <?php foreach ($orders as $order) { ?>

                                <?php
                                    $cancelled = '';
                                    if($order['status'] == 3){
                                        $cancelled = "cancelled_order";
                                        $lock = true;
                                    }
                                    $adjust_style = '';
                                    if($order['adjust_status'] == 1){
                                        $adjust_style = "background-color: #cc0000; color: #FFFF00; padding:3px;";
                                    }elseif($order['adjust_status'] == 2){
                                        $adjust_style = "background-color: #33CC33; color: #ffffff; padding:3px;";
                                    }
                                ?>

                                <tr class="<?php echo $cancelled; ?>">
                                    <td class="text-right" style="font-size: 130%;text-align: center;"><span style="color: #33CC33;">调</span><br><b><?php echo $order['purchase_order_id']; ?></b></td>
                                    <td class="text-left"><?php echo $order['supplier_type_name']; ?><br />[# <?php echo $order['supplier_type']; ?> ]</td>
                                    <td class="text-left"><?php echo $order_stations[$order['station_id']]; ?></td>
                                    <td class="text-left"><?php echo $order['order_total']; ?></td>
                                    <td class="text-left"><?php echo $order['get_total']; ?></td>
                                    <td class="text-left"><?php echo $order['add_user_name']; ?></td>
                                    <td class="text-left"><?php echo $order['date_added']; ?></td>
                                    <td class="text-left"><?php echo $order['status_name']; ?></td>
                                    <td class="text-left">
                                        小计：<?php echo $order['order_total'];?><br>
                                        余额支付：<?php echo $order['use_credits_total'];?><br>
                                        缺货：<?php echo $order['quehuo_credits'];?><br>
                                        退货：<?php echo $order['order_return'];?><br>
                                        订单总额：<?php
                                                    if($order['checkout_status'] == 2){
                                                        $order['order_due'] = $order['order_total'] - $order['use_credits_total'];
                                                    }
                                                    else{
                                                        $order['order_due'] = $order['order_total'] - $order['use_credits_total'] - $order['quehuo_credits'] - $order['order_return'];
                                                    }
                                                    echo $order['order_due'] < 0 ? 0 : $order['order_due'];
                                                ?>
                                        <br>
                                        <?php echo $order['checkout_status'] == 2 ? "已付款" : "未付"; ?>
                                        <?php if($order['checkout_type_id'] == 3){ ?>
                                            <span style="color:red;">预</span>
                                        <?php  } ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if( $order['invoice_flag'] == 1 ) { ?>
                                            <span style="background-color: #33CC33; color: #ffffff; padding:3px;">是</span>
                                        <?php } else { ?>
                                            <span style="background-color: #cc0000; color: #FFFF00; padding:3px;">否</span>
                                        <?php } ?>
                                    </td>

                                    <td class="text-left">
                                        计划到货日期:<br><?php echo $order['date_deliver_plan']; ?><br>
                                        实际到货日期:<br><?php echo $order['date_deliver']; ?>
                                    </td>
                                    <td>
                                        <span style="<?php echo $adjust_style; ?>">
                                        <?php echo $order['adjust_type'] ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <?php if($modifyPermission){ ?>
                                            <a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td class="text-center" colspan="13"><?php echo $text_no_results; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        </table>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript"><!--
        $('#button-filter').on('click', function() {
            var url = 'index.php?route=purchase/pre_purchase_adjust&token=<?php echo $token; ?>';
            //Fix url with all filters.
            url += fixUrl(2);
            location = url;
        });

        function fixUrl(type){
            var url ='';

            var filter_warehouse_id_global = $('input[name=\'filter_warehouse_id_global\']').val();
            if(filter_warehouse_id_global && filter_warehouse_id_global != '') {
                url += '&filter_warehouse_id_global='+ encodeURIComponent(filter_warehouse_id_global);
            }

            var filter_purchase_order_id = $('input[name=\'filter_purchase_order_id\']').val();
            if (filter_purchase_order_id && filter_purchase_order_id != '') {
                url += '&filter_purchase_order_id=' + encodeURIComponent(filter_purchase_order_id);
            }

            var filter_supplier_type = $('select[name=\'filter_supplier_type\']').val();
            if (filter_supplier_type && filter_supplier_type != '*') {
                url += '&filter_supplier_type=' + encodeURIComponent(filter_supplier_type);
            }

            var filter_order_type = $('select[name=\'filter_order_type\']').val();
            if (filter_order_type && filter_order_type != '*') {
                url += '&filter_order_type=' + encodeURIComponent(filter_order_type);
            }

            var filter_date_deliver = $('input[name=\'filter_date_deliver\']').val();
            if (filter_date_deliver) {
                url += '&filter_date_deliver=' + encodeURIComponent(filter_date_deliver);
            }

            var filter_date_deliver_end = $('input[name=\'filter_date_deliver_end\']').val();
            if (filter_date_deliver_end) {
                url += '&filter_date_deliver_end=' + encodeURIComponent(filter_date_deliver_end);
            }

            var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();
            if (filter_order_status_id && filter_order_status_id != '*') {
                url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
            }

            var filter_order_checkout_status_id = $('select[name=\'filter_order_checkout_status_id\']').val();
            if (filter_order_checkout_status_id && filter_order_checkout_status_id != '*') {
                url += '&filter_order_checkout_status_id=' + encodeURIComponent(filter_order_checkout_status_id);
            }

            var filter_purchase_person_id = $('select[name=\'filter_purchase_person_id\']').val();
            if (filter_purchase_person_id && filter_purchase_person_id != '*') {
                url += '&filter_purchase_person_id=' + encodeURIComponent(filter_purchase_person_id);
            }

            if(type == 1){
                var filter_page = <?php echo isset($_GET['page']) ? $_GET['page'] : 0; ?>;
                if (filter_page > 0) {
                    url += '&page=' + encodeURIComponent(filter_page);
                }
            }

            return url;
        }
    //--></script>
    <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
    <script type="text/javascript"><!--
        $('.date').datetimepicker({
            pickTime: false
        });
    //--></script>
</div>
<?php echo $footer; ?>