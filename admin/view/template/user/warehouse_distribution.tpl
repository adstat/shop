<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>订单分拣分配页面</h1>
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
                <h3 class="panel-title"><i class="fa fa-bar-chart"></i>订单分配</h3>
            </div>

        <div class="panel-body">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="input-station">订单平台<b style="color: #CC0000">生鲜/快消</b></label>
                            <select name="filter_station" id="input-station" class="form-control">
                                <option value="0">全部</option>
                                <?php foreach($order_stations as $station){ ?>
                                <option value="<?php echo $station['station_id']; ?>"><?php echo $station['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">用户区域</label>
                            <select name="filter_bd_area_list" id="input_bd_area_list" class="form-control">
                                <option value='0'>全部</option>
                                <?php foreach ($area_list as $val) { ?>
                                <option value="<?php echo $val['id']; ?>" ><?php echo $val['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="input-date-start">分拣类型</label>
                            <select name="filter_product_type_list" id="input_product_type_list" class="form-control">
                                <option value='0'>全部</option>
                                <?php foreach ($product_type_list as $val) { ?>
                                <option value="<?php echo $val['product_type_id']; ?>" ><?php echo $val['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="input-date-start">区域</label>
                            <select name="filter_area_list" id="input_area_list" class="form-control">
                                <option value='0'>全部</option>
                                <?php foreach ($area as $val) { ?>
                                <option value="<?php echo $val['district']; ?>" ><?php echo $val['district']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                        <label class="control-label" for="input-date-start">配送日期</label>
                            <div class="input-group date">
                            <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="input-customer-group">会员等级</label>
                            <select name="filter_customer_group_id" id="input-customer-group" class="form-control">
                                <option value="0">全部</option>
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

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">订单状态</label>
                            <select name="filter_order_status" id="input_order_status" class="form-control">
                                <option value='0'>全部(非取消)</option>
                                <?php foreach ($order_status as $val) { ?>
                                <option value="<?php echo $val['order_status_id']; ?>" ><?php echo $val['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">已经分配的订单<b style="color: #CC0000">是否重分配</b></label>
                            <select name="filter_fj_status" id="input_fj_status" class="form-control">
                                <option value='0'>忽略</option>
                                <option value='1'>已经分配的订单</option>
                            </select>
                        </div>
                    </div>
                </div>
            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>

        </div>

            <div  class="table-responsive">
                <form class="form-horizontal">
                    <div class="col-sm-2 pull-right" id = 'fixed'>
                        <div class="form-group">
                            <label class="control-label">分拣人员</label>
                            <select name="filter_w_user_list" id="input_w_user_list" class="form-control">
                                <option value='0'>全部</option>
                                <?php foreach ($w_user_list as $val) { ?>
                                <option value="<?php echo $val['user_id']; ?>" ><?php echo $val['username']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div>
                            <button type="button"  id="button-filter"  class="btn btn-primary pull-right" onclick="distrOrderToWorker(this);"><i class="fa fa-search"></i>分配</button>
                        </div>
                    </div>

                    <div class="col-sm-10">
                        <label class="control-label" id="distr-flag">订单</label>

                        <div  id="distr-table">

                        </div>
                    </div>
                </form>

            </div>
    </div>
</div>

<script>
    $('.date').datetimepicker({
        pickTime: false
    });
</script>
<script type="text/javascript">


    function getOrdersToDistr(){
        var conditions = new Array();
        conditions[0] = $('select[name=\'filter_station\']').val();
        conditions[1] = $('input[name=\'filter_date_start\']').val();
        conditions[2] = $('select[name=\'filter_customer_group_id\']').val();
        conditions[3] = $('select[name=\'filter_order_status\']').val();
        conditions[4] = $('select[name=\'filter_bd_area_list\']').val();
        conditions[5] = $('select[name=\'filter_product_type_list\']').val();
        conditions[6] = $('select[name=\'filter_area_list\']').val();
        var condition = conditions.join();

        $('#distr-table').load('index.php?route=user/warehouse_distribution/getOrdersToDistr&token=<?php echo $token; ?>&condition='+condition);
    }

    function getUnConfirmDistr(){
        var conditions = new Array();

        conditions[0] = $('select[name=\'filter_station\']').val();
        conditions[1] = $('input[name=\'filter_date_start\']').val();
        conditions[2] = $('select[name=\'filter_customer_group_id\']').val();
        conditions[3] = $('select[name=\'filter_order_status\']').val();
        conditions[4] = $('select[name=\'filter_bd_area_list\']').val();
        conditions[5] = $('select[name=\'filter_product_type_list\']').val();
        conditions[6] = $('select[name=\'filter_area_list\']').val();
        conditions[7] = $('select[name=\'filter_w_user_list\']').val();

        var condition = conditions.join();

        $('#distr-table').load('index.php?route=user/warehouse_distribution/getUnConfirmDistr&token=<?php echo $token; ?>&condition='+condition);

    }
</script>
<script>
    $('#button-filter').on('click', function() {
//        getDistrOrders();
        if($('#input_fj_status').val() <= 0){
            getOrdersToDistr();
        }else{
            getUnConfirmDistr();
        }
    });
</script>


