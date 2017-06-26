<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1>仓库货位管理</h1>
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
                <h3 class="panel-title"><i class="fa fa-list"></i>修改仓库货位信息</h3>
            </div>
        </div>
        <div>
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-banner" class="form-horizontal">
                <div class="form-group" >
                    <label class="col-sm-2 control-label" for="input-products">绑定商品ID</label>
                    <div class="col-sm-10">
                        <input type="text" name="products" placeholder="商品ID" id="input-products" class="form-control" value="<?php echo $products ?>" />
                    </div>
                </div>
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-station">平台</label>
                    <div class="col-sm-10">
                        <select name="filter_station_id" id="input_station_id" class="form-control">
                            <option value='0'>- -</option>
                            <?php foreach ($stations as $val) { ?>
                            <?php if ($val['station_id'] == $filter_station_id) { ?>
                            <option value="<?php echo $val['station_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $val['station_id']; ?>" ><?php echo $val['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-station-section-id">仓库区域</label>
                    <div class="col-sm-10">
                        <select name="filter_station_section_id" id="input_station_section_id" class="form-control">
                            <option value='0'>- -</option>
                            <?php foreach ($stations_section as $val) { ?>
                            <?php if ($val['station_section_type_id'] == $filter_station_section_id) { ?>
                            <option value="<?php echo $val['station_section_type_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $val['station_section_type_id']; ?>" ><?php echo $val['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-station-section-title">仓库货位号</label>
                    <div class="col-sm-10">
                        <input type="text" name="station_section_title" placeholder="例 A01" id="input-station-section-title" class="form-control"  value="<?php echo $station_section_title ?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-status">状态</label>
                    <div class="col-sm-10">
                        <select name="status" id="input-status" class="form-control">
                            <?php if ($status) { ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group required" >
                    <label class="col-sm-2 control-label" for="input-mode">托盘</label>
                    <div class="col-sm-10">
                        <select name="tray" id="input-status" class="form-control">
                            <?php if ($tray) { ?>
                            <option value="1" selected="selected">是</option>
                            <option value="0">否</option>
                            <?php } else { ?>
                            <option value="1">是</option>
                            <option value="0" selected="selected">否</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group required" >
                    <label class="col-sm-2 control-label" for="input-mode">货架</label>
                    <div class="col-sm-10">
                        <select name="shelf" id="input-status" class="form-control">
                            <?php if ($shelf) { ?>
                            <option value="1" selected="selected">是</option>
                            <option value="0">否</option>
                            <?php } else { ?>
                            <option value="1">是</option>
                            <option value="0" selected="selected">否</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group required" >
                    <label class="col-sm-2 control-label" for="input-mode">移动</label>
                    <div class="col-sm-10">
                        <select name="mobile" id="input-status" class="form-control">
                            <?php if ($mobile) { ?>
                            <option value="1" selected="selected">是</option>
                            <option value="0">否</option>
                            <?php } else { ?>
                            <option value="1">是</option>
                            <option value="0" selected="selected">否</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                  <!--  <div class="col-sm-10">
                        托盘：<input name="tray" type="radio" value="1" />是
                        <input name="tray" type="radio" value="0" />否
                    </div>
                    <div class="col-sm-10">
                        货架：<input name="shelf" type="radio" value="1" />是
                        <input name="shelf" type="radio" value="0" />否
                        <div >
                            移动：<input name="mobile" type="radio" value="1" />是
                            <input name="mobile" type="radio" value="0" />否
                        </div>
                    </div> -->


                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-sort-order">排序</label>
                    <div class="col-sm-10">
                        <input type="text" name="sort_order"  placeholder="排序号" id="input-sort-order" class="form-control"  value=" <?php echo $sort_order ;?>"/>
                    </div>
                </div>



            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('input[name=\'products\']').autocomplete({
        'source': function(request, response) {
            var warehouse_id = $("#warehouse_id_global").val();
            $.ajax({
                type: 'POST',
                url: 'index.php?route=user/warehouse_management/autocomplete&token=<?php echo $token; ?>&products=' +  encodeURIComponent(request),
                dataType: 'json',
                data:{
                    warehouse_id :warehouse_id,
                },
                success: function(json) {
                    console.log(json);
                    response($.map(json, function(item) {
                        return {
                            label: item['fix'],
                         //   label: item['name'],
                            value: item['product_id']
                        }
                    }));
                }
            });
        },
        'select': function(item) {

            $('input[name=\'products\']').val(item['value']);
        }
    });


    $('input[name=\'station_section_title\']').autocomplete({
        'source': function(request, response) {
            var warehouse_id = $("#warehouse_id_global").val();
            $.ajax({
                type: 'POST',
                url: 'index.php?route=user/warehouse_management/getProductSection&token=<?php echo $token; ?>&section=' +  encodeURIComponent(request),
                dataType: 'json',
                data:{
                    warehouse_id :warehouse_id,
                },
                success: function(json) {
                    console.log(json);
                    response($.map(json, function(item) {
                        return {
                            label: item['fix'],
                            //   label: item['name'],
                            value: item['product_id']
                        }
                    }));
                }
            });
        },
        'select': function(item) {
            $('input[name=\'station_section_title\']').val(item['value']);
        }
    });


</script>