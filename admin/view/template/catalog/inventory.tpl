<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="page-header" style="display: none">
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
    <?php if (isset($error_warning) && $error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if (isset($success_msg) && $success_msg) { ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success_msg; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-pencil"></i>快消品可售库存管理</h3>
        </div>
        <div class="panel-body">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-special" data-toggle="tab">库存调整</a></li>
                <li><a href="#tab-list" data-toggle="tab" onclick="loadInventoryList();">库存列表</a></li>
                <li><a href="#tab-history" data-toggle="tab">历史查询</a></li>
                <li><a href="#tab-inventory-on-the-way" data-toggle="tab">在途库存查询</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-special">
                    <div class="table-responsive">
                        <div class="alert alert-info">一次调整可录入多条商品记录，但不能有重复商品。</div>
                        <div class="alert alert-warning"><span style="color:red;">*调整类型：预设库存为商品到货前预设可售库存，商品实际到货时仓库扫描入库后会按实际入库数量重置预设库存！！！</span></div>
                        <form action="<?php echo $action_adjust; ?>" method="post" enctype="multipart/form-data" id="form-product-adjust" class="form-horizontal">
                            <table id="adjust" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <td colspan="7">调整备注: <input name="comment" type="text" maxlength="50" size="50" /></td>
                                </tr>

                                <tr>
                                    <td colspan="7">调整类型:
                                        <select name='inventory_type' style="width:20em;">
                                            <option value='16'>库存调整</option>
                                            <option value='18'>预设库存</option>
                                        </select>

                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center" width="10%">商品ID</td>
                                    <td class="text-center" width="15%">库存调整</td>
                                    <td class="text-center" width="13%">平台 - 仓库</td>
                                    <td class="text-center" width="6%">状态</td>
                                    <td class="text-center">商品名称</td>
                                    <td class="text-center" width="8%">可售库存</td>
                                    <td class="text-center" width="8%">安全库存</td>
                                    <td class="text-center" width="8%">当前库存</td>
                                    <td width="10%"></td>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- 添加商品可售库存 -->
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8"></td>
                                    <td class="text-left"><button type="button" onclick="addAdjust();" data-toggle="tooltip" title="添加" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-center"><button type="button" class="btn btn-primary" onclick="submitAdjust();">确认调整</button></td>
                                </tr>
                                </tfoot>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" id="tab-list">
                    <div class="table-responsive">
                        <table id="inventory-list" class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <td class="text-center">商品ID</td>
                                <td class="text-left">平台 - 仓库</td>
                                <td class="text-left">销售状态</td>
                                <td class="text-left">商品名称</td>
                                <td class="text-left">可售库存</td>
                                <td class="text-left">安全库存</td>
                                <td class="text-center">当前库存</td>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- 现实当前可售库存 -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tab-history">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="product_ids">商品编号</label>
                            <input type="text" name="filter_model" value="" placeholder="1001,1002..." id="product_ids" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="start_date">开始时间</label>
                            <div class="input-group date">
                                <input type="text" name="start_date" value="<?php echo $start_date; ?>" data-date-format="YYYY-MM-DD" id="start_date" class="form-control" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="end_date">结束时间</label>
                            <div class="input-group date">
                                <input type="text" name="end_date" value="<?php echo $end_date; ?>" data-date-format="YYYY-MM-DD" id="end_date" class="form-control" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="inventory_type_id">变更类型</label>
                            <select name="inventory_type_id" id="inventory_type_id" class="form-control">
                                <option value="0"> - 请选择 - </option>
                                <?php if(!empty($inventory_type)){ ?>
                                <?php foreach($inventory_type as $value){ ?>
                                    <option value="<?php echo $value['inventory_type_id'] ?>"><?php echo $value['name']; ?></option>
                                <?php }}?>
                            </select>
                        </div>

                        <button type="button" class="btn btn-primary pull-right" onclick="getProductInventoryHistory(this);">查询</button>
                    </div>

                    <div class="col-sm-12">
                        <table id="inventory-history" class="table table-striped table-bordered table-hover" style="margin-top: 15px;">
                            <thead>
                                <tr>
                                    <td class="text-center">商品编号</td>
                                    <td class="text-center">商品名称</td>
                                    <td class="text-center">平台 - 仓库</td>
                                    <td class="text-center">变更类型</td>
                                    <td class="text-center">变更数量</td>
                                    <td class="text-center">变更时间</td>
                                    <td class="text-center">操作人</td>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 商品价格数据 -->

                                <tr>
                                    <td colspan="7">选择日期查询</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row" id="pageRow">
                            <div class="col-sm-6 text-left" id="page-left"></div>
                            <div class="col-sm-2 text-left" id="page-center" style="display: none;">
                                <div class="col-sm-6" style="padding-right: 0;">
                                    <input type="number" name="goPage" value="" id="goPage" class="form-control" placeholder=""/>
                                </div>
                                <div class="col-sm-4" style="padding-left: 1px;">
                                    <button class="btn btn-small btn-default" onclick="getProductInventoryHistory(this, true)">Go</button>
                                </div>
                            </div>
                            <div class="col-sm-4 text-right"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab-inventory-on-the-way">
                    <div class="col-sm-6">
                        <div class="form-group col-sm-12">
                            <label class="control-label" for="product_ids">商品编号</label>
                            <input type="text" name="filter_model" value="" placeholder="1001,1002..." id="purchase_product_ids" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right" onclick="getProductInventoryOnTheWay(this);">查询</button>
                    </div>
                    <div class="col-sm-12">
                        <table id="inventory-on-the-way" class="table table-striped table-bordered table-hover" style="margin-top: 15px;">
                            <thead>
                                <tr>
                                    <td class="text-center">商品编号</td>
                                    <td class="text-center">商品名称</td>
                                    <td class="text-center">平台 - 仓库</td>
                                    <td class="text-center">当前库存</td>
                                    <td class="text-center">在途数量</td>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 商品价格数据 -->

                                <tr>
                                    <td colspan="5">选择商品ID查询</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="row" id="on-the-way">
                            <div class="col-sm-6 text-left" id="on-left"></div>
                            <div class="col-sm-2 text-left" id="on-center" style="display: none;">
                                <div class="col-sm-6" style="padding-right: 0;">
                                    <input type="number" name="goPage" value="" id="on-go-page" class="form-control" placeholder=""/>
                                </div>
                                <div class="col-sm-4" style="padding-left: 1px;">
                                    <button class="btn btn-small btn-default" onclick="getProductInventoryOnTheWay(this, true)">Go</button>
                                </div>
                            </div>
                            <div class="col-sm-4 text-right"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
    $('#language a:first').tab('show');
    $('#option a:first').tab('show');
    //--></script>
<script type="text/javascript">
    var adjust_row = 0;
    function addAdjust() {
        html  = '<tr id="adjust-row' + adjust_row + '">';
        html += '  <td class="text-center"><input type="text" name="products[' + adjust_row + '][product_id]" value="" placeholder="商品ID" class="form-control int invAdjustProductList" onChange="getProductInv($(this).val(), $(this));" /></td>';
        html += '  <td class="text-left"><input type="text" name="products[' + adjust_row + '][quantity]" value="" placeholder="正负值新增调整" class="form-control int" /></td>';
        html += '  <td class="text-left"><input type="hidden" name="products[' + adjust_row + '][station_id]" class="rowStationId" value="" /><div class="rowStation"></div></td>';
        html += '  <td class="text-left"><div class="rowStatus"></div></td>';
        html += '  <td class="text-left"><div class="rowName"></div></td>';
        html += '  <td class="text-left"><div class="rowOriInv"></div></td>';
        html += '  <td class="text-left"><div class="rowSafeStock"></div></td>';
        html += '  <td class="text-left"><div class="rowCurrInv"></div></td>';
        html += '  <td class="text-left"><button type="button" onclick="$(\'#adjust-row' + adjust_row + '\').remove();" data-toggle="tooltip" title="删除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';

        $('#adjust tbody').append(html);
        adjust_row++;
    }

    function submitAdjust(){
        var trs = $('#adjust tbody tr').length;
        if(trs < 1){
            alert('请先添加.');
            return false;
        }

        var valid = true;
        $('#adjust tbody input.int').each(function(index, element){
            var val = parseInt($(element).val());
            if(!val){
                valid = false;
                return false;
            }
        });
        if(!valid){
            alert('不能为空!');
            return false;
        }

        if(window.confirm('确认调整这些商品的库存么？')){
            $('#form-product-adjust').submit();
        }
    }

    function loadInventoryList(){
        $.ajax({
            type: 'GET',
            url: 'index.php?route=catalog/inventory/inventoryList&token=<?php echo $_SESSION['token']; ?>',
            dataType: 'json',
            data: {
                warehouse_id : <?php echo $filter_warehouse_id_global; ?>
            },
            success: function(data){
                console.log(data);

                $('#inventory-list tbody').html('');
                var html = '';
                var bgColor= '';

                $.each(data, function(i, n){
                    if(parseInt(n.station_id) == 2){
                        bgColor= ' style="background-color: #f8efe0;"';
                    }
                    else{
                        bgColor= '';
                    }

                    html += '<tr>';
                    html += '  <td class="text-center" ' + bgColor + '>' + n.product_id + '</td>';
                    html += '  <td class="text-center" ' + bgColor + '>' + n.station + ' - ' + n.warehouse + '</td>';
                    if(parseInt(n.status) == 1){
                        html += '  <td class="text-center" style="background-color: #66CC66; color: #ffffff;">是</td>';
                    }
                    else{
                        html += '  <td class="text-center" style="background-color: #cc0000; color: #FFFF00;">否</td>';
                    }
                    html += '  <td class="text-center">' + n.name + '</td>';
                    html += '  <td class="text-center">' + n.ori_inv + '</td>';
                    html += '  <td class="text-center">' + n.safestock + '</td>';

                    if(parseInt(n.status) == 1 && parseInt(n.ori_inv) > 0 && parseInt(n.quantity) < 1){
                        html += '  <td class="text-center" style="background-color: #cc0000; color: #FFFF00;">' + n.quantity + '</td>';
                    }
                    else{
                        html += '  <td class="text-center">' + n.quantity + '</td>';
                    }
                    html += '</tr>';
                });
                $('#inventory-list tbody').html(html);
            }
        });
    }

    function getProductInv(product_id,obj){
        var rowId = obj.parent().parent().attr('id');
        //rowStation
        //rowName
        //rowOriInv
        //rowCurrInv

        //$('.invAdjustProductList').each(function(i){
        //   console.log('M2:'+$(this).val());
        //});

        var product_id = parseInt(product_id);

        $.ajax({
            type: 'GET',
            async: false,
            cache: false,
            url: 'index.php?route=catalog/inventory/getProductInv&token=<?php echo $_SESSION['token']; ?>',
            data: {
                product_id: product_id,
                warehouse_id : <?php echo $filter_warehouse_id_global; ?>
            },
            dataType: 'json',
            success: function(response){
                //console.log(response);

                if(parseInt(response.station_id) > 0){
                    $('#'+rowId+' .rowStation').html(response.station +' - '+ response.warehouse);
                    $('#'+rowId+' .rowStationId').val(response.station_id);
                    $('#'+rowId+' .rowName').html(response.name);
                    $('#'+rowId+' .rowOriInv').html(response.ori_inv);
                    $('#'+rowId+' .rowSafeStock').html(response.safestock);
                    $('#'+rowId+' .rowCurrInv').html(response.curr_inv);
                    if(parseInt(response.status) == 1){
                        $('#'+rowId+' .rowStatus').html('<span style="background-color: #66CC66; color: #ffffff; padding: 3px;">是</span>');
                    }
                    else{
                        $('#'+rowId+' .rowStatus').html('<span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">否</span>');
                    }
                }
                else{
                    $('#'+rowId+' .rowStation').html('');
                    $('#'+rowId+' .rowStationId').html('');
                    $('#'+rowId+' .rowName').html('');
                    $('#'+rowId+' .rowOriInv').html('');
                    $('#'+rowId+' .rowSafeStock').html('');
                    $('#'+rowId+' .rowCurrInv').html('');
                    $('#'+rowId+' .rowStatus').html('<span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">不存在</span>');
                }
            }
        });
    }

    function getProductInventoryHistory(obj, goPage = false){
        var page = parseInt(obj.innerText);
        if(goPage){ page = $('#goPage').val(); }
        if(isNaN(page) || page < 1){ page = 1; }

        var product_ids       = $.trim($('#product_ids').val());
        var start_date        = $('#start_date').val();
        var end_date          = $('#end_date').val();
        var inventory_type_id = $('#inventory_type_id').val();

        if(product_ids.length <= 0){ alert('请填写商品编号'); return false; }
        if(start_date.length <= 0) { alert('请填写开始时间'); return false; }
        if(end_date.length <= 0)   { alert('请填写结束时间'); return false; }

        var start_ms      = Date.parse(new Date(start_date.replace(/-/g, "/")));
        var end_ms        = Date.parse(new Date(end_date.replace(/-/g, "/")));
        var days          = Math.floor((end_ms - start_ms)/(24*3600*1000));
        var time_interval = parseInt('<?php echo $time_interval; ?>');
        if(days > time_interval){
            alert('查询时间间隔不能超过'+ time_interval + '天');
            return false;
        }

        $.ajax({
            type: 'POST',
            async: false,
            cache: false,
            url: 'index.php?route=catalog/inventory/getProductInventoryHistory&token=<?php echo $_SESSION['token']; ?>',
            data: {
                product_ids         : product_ids,
                start_date          : start_date,
                end_date            : end_date,
                inventory_type_id   : inventory_type_id,
                page                : page,
                warehouse_id        : <?php echo $filter_warehouse_id_global; ?>
            },
            dataType: 'json',
            success: function(response){
                //console.log(response);
                var html = '';
                $.each(response.inventory_data, function(i, n){
                    var color = "style='background-color: #66CC66; color: #ffffff;'";
                    if(n.quantity < 0){
                        color = 'style="background-color: #cc0000; color: #FFFF00;"';
                    }

                    html += '<tr>';
                    html += '  <td class="text-center">' + n.product_id + '</td>';
                    html += '  <td class="text-center">' + n.name + '</td>';
                    html += '  <td class="text-center">' + n.station + ' - ' + n.warehouse + '</td>';
                    html += '  <td class="text-center">' + n.type + '</td>';
                    html += '  <td class="text-center" ' + color +'>' + n.quantity + '</td>';
                    html += '  <td class="text-center">' + n.date_added + '</td>';
                    html += '  <td class="text-center">' + n.add_user_name + '</td>';
                    html += '</tr>';
                });

                if(response.show_input > 0){
                    $('#page-center').show();
                }else{
                    $('#page-center').hide();
                }
                $('#page-left').html(response.pagination);
                $('#pageRow .text-right').html(response.results);
                $('#inventory-history tbody').html(html);
            }
        });
    }

    function getProductInventoryOnTheWay(obj, goPage = false){
        var page = parseInt(obj.innerText);
        if(goPage){ page = $('#on-go-page').val(); }
        if(isNaN(page) || page < 1){ page = 1; }
        var product_ids = $.trim($('#purchase_product_ids').val());

        $.ajax({
            type: 'POST',
            async: false,
            cache: false,
            url: 'index.php?route=catalog/inventory/getProductInventoryOnTheWay&token=<?php echo $_SESSION['token']; ?>',
            data: {
                product_ids  : product_ids,
                status : 2,
                order_type : 1,
                page : page,
                warehouse_id : <?php echo $filter_warehouse_id_global; ?>
            },
            dataType: 'json',
            success: function(response){
                console.log(response);

                var html = '';
                $.each(response.inventory_data, function(i, n){
                    html += '<tr>';
                    html += '  <td class="text-center">' + n.product_id + '</td>';
                    html += '  <td class="text-center">' + n.name + '</td>';
                    html += '  <td class="text-center">' + n.station + ' - ' + n.warehouse + '</td>';
                    html += '  <td class="text-center">' + n.inventory + '</td>';
                    html += '  <td class="text-center">' + n.on_the_way + '</td>';
                    html += '</tr>';
                });

                if(response.show_input > 0){
                    $('#on-center').show();
                }else{
                    $('#on-center').hide();
                }
                $('#on-left').html(response.pagination);
                $('#on-the-way .text-right').html(response.results);
                $('#inventory-on-the-way tbody').html(html);
            }
        });
    }
</script>
<script type="text/javascript">
<!--
$('.date').datetimepicker({
    pickTime: false
});
//-->
</script>
</div>
<?php echo $footer; ?> 