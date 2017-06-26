<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-customer').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-name"><?php echo $column_select_name; ?></label>
                                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $column_select_name; ?>" id="input-name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="input-bd"><?php echo $column_bd; ?></label>
                                <select name="filter_bd" id="input-bd" class="form-control">
                                    <option></option>
                                    <?php foreach ($bd_lists as $bd_list) { ?>
                                    <?php if ($bd_list['bd_id'] == $filter_bd) { ?>
                                    <option value="<?php echo $bd_list['bd_id']; ?>" selected="selected"><?php echo $bd_list['bd_name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $bd_list['bd_id']; ?>"><?php echo $bd_list['bd_name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <!--<div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="input-agent"><?php echo $column_agent; ?></label>
                                <select name="filter_agent" id="input-agent" class="form-control">
                                    <option></option>
                                    <option value="1" <?php echo $filter_agent==1?'selected':'';?> ><?php echo $text_yes; ?></option>
                                    <option value="0" <?php echo $filter_agent==0?'selected':'';?> ><?php echo $text_no; ?></option>
                                </select>
                            </div>
                        </div>-->
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="input-customer-group"><?php echo $entry_customer_group; ?></label>
                                <select name="filter_customer_group_id" id="input-customer-group" class="form-control">
                                    <option value="*"></option>
                                    <?php foreach ($customer_groups as $customer_group) { ?>
                                    <?php if ($customer_group['customer_group_id'] == $filter_customer_group_id) { ?>
                                    <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                                <select name="filter_status" id="input-status" class="form-control">
                                    <option value="*"></option>
                                    <?php if ($filter_status) { ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <?php } else { ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <?php } ?>
                                    <?php if (!$filter_status && !is_null($filter_status)) { ?>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                    <?php } else { ?>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="input-approved"><?php echo $entry_approved; ?></label>
                                <select name="filter_approved" id="input-approved" class="form-control">
                                    <option value="*"></option>
                                    <?php if ($filter_approved) { ?>
                                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                    <?php } else { ?>
                                    <option value="1"><?php echo $text_yes; ?></option>
                                    <?php } ?>
                                    <?php if (!$filter_approved && !is_null($filter_approved)) { ?>
                                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                    <?php } else { ?>
                                    <option value="0"><?php echo $text_no; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <br>
                            <button type="button" id="button-filter" class="btn btn-primary pull-left"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>
                </div>
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-customer">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                                <td>会员id</td>
                                <td class="text-left"><?php if ($sort == 'name') { ?>
                                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                                    <?php } ?></td>
                                <td class="text-center">
                                    <?php echo $column_merchant_name; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $column_merchant_address; ?>
                                </td>
                                <td class="text-left"><?php if ($sort == 'customer_group') { ?>
                                    <a href="<?php echo $sort_customer_group; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer_group; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_customer_group; ?>"><?php echo $column_customer_group; ?></a>
                                    <?php } ?></td>
                                <td class="text-left"><?php if ($sort == 'c.status') { ?>
                                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                                    <?php } ?></td>

                                <td class="text-center">
                                    <?php echo $column_last_order?>
                                </td>

                                <td class="text-left"><?php if ($sort == 'c.date_added') { ?>
                                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                                    <?php } ?></td>
                                <td class="text-right"><?php echo $column_action; ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($customers) { ?>
                            <?php foreach ($customers as $customer) { ?>
                            <tr>
                                <td class="text-center"><?php if (in_array($customer['customer_id'], $selected)) { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $customer['customer_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $customer['customer_id']; ?>" />
                                    <?php } ?></td>
                                <td class="text-left"><?php echo $customer['customer_id']; ?></td>
                                <td class="text-left"><?php echo $customer['name']; ?></td>

                                <td class="text-left"><?php echo $customer['merchant_name']; ?></td>
                                <td class="text-left"><?php echo $customer['merchant_address']; ?></td>

                                <td class="text-left"><?php echo $customer['customer_group']; ?></td>
                                <td class="text-left"><?php echo $customer['status']; ?></td>
                                <td class="text-center"><?php echo $customer['orderid'].'('.$customer['order_date_added'].')'; ?></td>
                                <td class="text-left"><?php echo $customer['date_added']; ?></td>
                                <td class="text-right">


                                    <a href="<?php echo $customer['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                            </tr>
                            <?php } ?>
                            <?php } else { ?>
                            <tr>
                                <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
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
            url = 'index.php?route=marketing/customer_price&token=<?php echo $token; ?>';

            var filter_name = $('input[name=\'filter_name\']').val();

            if (filter_name) {
                url += '&filter_name=' + encodeURIComponent(filter_name);
            }

//	var filter_email = $('input[name=\'filter_email\']').val();
//
//	if (filter_email) {
//		url += '&filter_email=' + encodeURIComponent(filter_email);
//	}
            var filter_bd = $('select[name=\'filter_bd\']').val();

            if (filter_bd) {
                url += '&filter_bd=' + encodeURIComponent(filter_bd);
            }

//            var filter_agent = $('select[name=\'filter_agent\']').val();
//
//            if (filter_agent) {
//                url += '&filter_agent=' + encodeURIComponent(filter_agent);
//            }


            var filter_customer_group_id = $('select[name=\'filter_customer_group_id\']').val();

            if (filter_customer_group_id != '*') {
                url += '&filter_customer_group_id=' + encodeURIComponent(filter_customer_group_id);
            }

            var filter_status = $('select[name=\'filter_status\']').val();

            if (filter_status != '*') {
                url += '&filter_status=' + encodeURIComponent(filter_status);
            }

            var filter_approved = $('select[name=\'filter_approved\']').val();

            if (filter_approved != '*') {
                url += '&filter_approved=' + encodeURIComponent(filter_approved);
            }

//	var filter_ip = $('input[name=\'filter_ip\']').val();
//
//	if (filter_ip) {
//		url += '&filter_ip=' + encodeURIComponent(filter_ip);
//	}

            var filter_date_added = $('input[name=\'filter_date_added\']').val();

            if (filter_date_added) {
                url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
            }

            location = url;
        });
        //--></script>
    <script type="text/javascript"><!--
        $('input[name=\'filter_name\']').autocomplete({
            'source': function(request, response) {
                $.ajax({
                    url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
                    dataType: 'json',
                    success: function(json) {
                        response($.map(json, function(item) {
                            return {
                                label: item['name'],
                                value: item['customer_id']
                            }
                        }));
                    }
                });
            },
            'select': function(item) {
                $('input[name=\'filter_name\']').val(item['label']);
            }
        });

        $('input[name=\'filter_email\']').autocomplete({
            'source': function(request, response) {
                $.ajax({
                    url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_email=' +  encodeURIComponent(request),
                    dataType: 'json',
                    success: function(json) {
                        response($.map(json, function(item) {
                            return {
                                label: item['email'],
                                value: item['customer_id']
                            }
                        }));
                    }
                });
            },
            'select': function(item) {
                $('input[name=\'filter_email\']').val(item['label']);
            }
        });
        //--></script>
    <script type="text/javascript"><!--
        $('.date').datetimepicker({
            pickTime: false
        });
        //--></script></div>
<?php echo $footer; ?>
