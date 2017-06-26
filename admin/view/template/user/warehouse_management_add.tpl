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
                <h3 class="panel-title"><i class="fa fa-list"></i>添加新增区域</h3>
            </div>
        </div>
        <div>
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-banner" class="form-horizontal">
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-station">平台仓库</label>
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
                    <label class="col-sm-2 control-label" for="input-station-section-title">仓库分区号</label>
                    <div class="col-sm-10">
                        <input type="text" name="station_section_title" placeholder="例 A01" id="input-station-section-title" class="form-control" />
                    </div>
                </div>
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name">仓库分区名称</label>
                    <div class="col-sm-10">
                        <input type="text" name="title" placeholder="例 散货区" id="input-title" class="form-control" />
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
                    <label class="col-sm-2 control-label" for="input-mode">摆放方式</label>

                    <div class="col-sm-10">
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
                    </div>
                </div>

                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-sort-order">排序</label>
                    <div class="col-sm-10">
                        <input type="text" name="sort_order"  placeholder="排序号" id="input-sort-order" class="form-control" />
                    </div>
                </div>

                <div class="form-group required" >
                    <label class="col-sm-2 control-label" for="input-products">绑定商品ID</label>
                    <div class="col-sm-10">
                        <input type="text" name="products" placeholder="商品ID" id="input-products" class="form-control" />
                    </div>
                </div>

            </form>
        </div>



    </div>


</div>