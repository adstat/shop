<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-product" data-toggle="tooltip" title="保存" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="返回" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i>编辑原料</h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">常规</a></li>
            <li><a href="#tab-data" data-toggle="tab">数据</a></li>
            <li><a href="#tab-links" data-toggle="tab">关联</a></li>
           
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
                
                
                
                
                
                
              <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>">原料名称</label>
                    <div class="col-sm-10">
                      <input type="text" name="product_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($name) ? $name : ''; ?>" placeholder="原料名称" datatype="*" nullmsg="请输入原料名称" id="input-name<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_name[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                    

                  

                  
                  
                  
                  
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="tab-pane" id="tab-data">
              
                
                
                <div class="form-group required">
              <label class="col-sm-2 control-label" for="input-barcode">原料条码(不填写保存后默认为XS+原料ID)</label>
              <div class="col-sm-10">
                <input type="text" name="barcode" value="<?php echo $barcode; ?>" placeholder="原料条码" id="input-barcode" class="form-control" />
                <?php if (isset($error_model) && $error_model) { ?>
                <div class="text-danger"><?php echo $error_model; ?></div>
                <?php } ?>
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
                
                
                <div class="form-group">
                <label class="col-sm-2 control-label" for="input-is-sell">是否零售</label>
                <div class="col-sm-10">
                  <select name="is_sell" id="input-is-sell" class="form-control">
                    <?php if ($is_sell) { ?>
                    <option value="1" selected="selected">是</option>
                    <option value="0">否</option>
                    <?php } else { ?>
                    <option value="1">是</option>
                    <option value="0" selected="selected">否</option>
                    <?php } ?>
                  </select>
                </div>
              </div>
                
                
                <div class="form-group">
                <label class="col-sm-2 control-label" for="input-is-sell">是否为赠品</label>
                <div class="col-sm-10">
                  <select name="is_gift" id="input-is-gift" class="form-control">
                    <?php if ($is_gift) { ?>
                    <option value="1" selected="selected">是</option>
                    <option value="0">否</option>
                    <?php } else { ?>
                    <option value="1">是</option>
                    <option value="0" selected="selected">否</option>
                    <?php } ?>
                  </select>
                </div>
              </div>
                
              <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-safestock-rate">备货率(小数)</label>
                  <div class="col-sm-10">
                      <input type="text" name="safestock_rate" value="<?php echo $safestock_rate; ?>" placeholder="备货率" id="input-safestock-rate" class="form-control" />
                  </div>
              </div>  
                
               <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-available">上架日期</label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_available" value="<?php echo $date_available; ?>" placeholder="上架日期" data-date-format="YYYY-MM-DD" id="input-date-available" class="form-control" />
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>  
                
              <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-price">价格</label>
                  <div class="col-sm-10">
                      <input type="text" name="price" value="<?php echo $price; ?>" placeholder="价格" id="input-price" class="form-control" />
                  </div>
              </div>  
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
              
              
              
              

           
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-storage_mode_id">储存</label>
                <div class="col-sm-10">
                  <select name="storage_mode_id" id="input-storage_mode_id" class="form-control">
                    <option value="1" <?php if($storage_mode_id == 1){ echo 'selected="selected"'; } ?> >常温</option>
                    <option value="2" <?php if($storage_mode_id == 2){ echo 'selected="selected"'; } ?> >冷藏</option>
                    <option value="3" <?php if($storage_mode_id == 3){ echo 'selected="selected"'; } ?> >冷冻</option>
                    <option value="4" <?php if($storage_mode_id == 4){ echo 'selected="selected"'; } ?> >温热</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-quantity">数量</label>
                <div class="col-sm-10">
                  <input type="text" name="quantity" value="<?php echo $quantity; ?>" placeholder="数量" id="input-quantity" class="form-control" />
                </div>
              </div>
              
              
              
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-shelf_life"><span data-toggle="tooltip" title="从包装日起的天数">最佳食用期</span></label>
                <div class="col-sm-10">
                  <input type="text" name="shelf_life" value="<?php echo $shelf_life; ?>" placeholder="最佳食用期，从包装日起的天数" id="input-shelf_life" class="form-control" />
                </div>
              </div>
                
                
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-weight">重量</label>
                <div class="col-sm-10">
                  <input type="text" name="weight" value="<?php echo $weight; ?>" placeholder="重量" id="input-weight" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-weight-class">重量单位</label>
                <div class="col-sm-10">
                  <select name="weight_class_id" id="input-weight-class" class="form-control">
                    <?php foreach ($weight_classes as $weight_class) { ?>
                    <?php if ($weight_class['weight_class_id'] == $weight_class_id) { ?>
                    <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order">排序</label>
                <div class="col-sm-10">
                  <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="排序" id="input-sort-order" class="form-control" />
                </div>
              </div>
                
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku-class">销量分级</label>
                <div class="col-sm-10">
                  <select name="sku_class" id="sku_class" class="form-control">
                    <option value="A" <?php if($class == "A"){ echo 'selected="selected"'; } ?> >A</option>
                    <option value="B" <?php if($class == "B"){ echo 'selected="selected"'; } ?> >B</option>
                    <option value="C" <?php if($class == "C"){ echo 'selected="selected"'; } ?> >C</option>
                    
                  </select>
                </div>
              </div>  
                
                
                <div class="form-group">
                <label class="col-sm-2 control-label" for="input-weight-type">是否毛重</label>
                <div class="col-sm-10">
                  <select name="weight_type" id="weight_type" class="form-control">
                    <option value="" <?php if($weight_type == ""){ echo 'selected="selected"'; } ?> ></option>
                    <option value="净重" <?php if($weight_type == "净重"){ echo 'selected="selected"'; } ?> >净重</option>
                    <option value="毛重" <?php if($weight_type == "毛重"){ echo 'selected="selected"'; } ?> >毛重</option>
                    
                  </select>
                </div>
              </div>  
                
                
                
                
                <div class="form-group">
                <label class="col-sm-2 control-label" for="input-box-size">整包单位量</label>
                <div class="col-sm-10">
                  <input type="text" name="box-size" value="<?php echo $box_size; ?>" placeholder="整包单位量" id="input-box-size" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-box-weight-class-id">整包单位</label>
                <div class="col-sm-10">
                  <select name="box_weight_class_id" id="input-box-weight-class-id" class="form-control">
                    <?php foreach ($weight_classes as $weight_class) { ?>
                    <?php if ($weight_class['weight_class_id'] == $box_weight_class_id) { ?>
                    <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
                
                
                <div class="form-group">
                <label class="col-sm-2 control-label" for="input-unit-size">最小单位量</label>
                <div class="col-sm-10">
                  <input type="text" name="unit_size" value="<?php echo $unit_size; ?>" placeholder="最小单位量" id="input-unit-size" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-unit-weight-class-id">最小单位</label>
                <div class="col-sm-10">
                  <select name="unit_weight_class_id" id="input-unit-weight-class-id" class="form-control">
                    <?php foreach ($weight_classes as $weight_class) { ?>
                    <?php if ($weight_class['weight_class_id'] == $unit_weight_class_id) { ?>
                    <option value="<?php echo $weight_class['weight_class_id']; ?>" selected="selected"><?php echo $weight_class['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $weight_class['weight_class_id']; ?>"><?php echo $weight_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
                
                
              <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-purchase-cost">最小单位采购价</label>
                  <div class="col-sm-10">
                      <input type="text" name="purchase_cost" value="<?php echo $purchase_cost; ?>" placeholder="最小单位采购价" id="input-purchase-cost" class="form-control" />
                  </div>
              </div>    
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-weightloss-rate">重量损耗占比</label>
                  <div class="col-sm-10">
                      <input type="text" name="weightloss_rate" value="<?php echo $weightloss_rate; ?>" placeholder="重量损耗占比" id="input-weightloss-rate" class="form-control" />
                  </div>
              </div>    
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-warehouse-cost">仓库成本</label>
                  <div class="col-sm-10">
                      <input type="text" name="warehouse_cost" value="<?php echo $warehouse_cost; ?>" placeholder="仓库成本" id="input-warehouse-cost" class="form-control" />
                  </div>
              </div>    
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-weightloss-cost">重量损耗</label>
                  <div class="col-sm-10">
                      <input type="text" name="weightloss_cost" value="<?php echo $weightloss_cost; ?>" placeholder="重量损耗" id="input-weightloss-cost" class="form-control" />
                  </div>
              </div>    
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-repack-cost">重新包装成本</label>
                  <div class="col-sm-10">
                      <input type="text" name="repack_cost" value="<?php echo $repack_cost; ?>" placeholder="重新包装成本" id="input-repack-cost" class="form-control" />
                  </div>
              </div>    
                
                
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-turnover-factor">周转系数</label>
                  <div class="col-sm-10">
                      <input type="text" name="turnover_factor" value="<?php echo $turnover_factor; ?>" placeholder="周转系数" id="input-turnover-factor" class="form-control" />
                  </div>
              </div>    
                
                
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-supplier-unit-size">供应商最小规格转换原料规格 份数</label>
                  <div class="col-sm-10">
                      <input type="text" name="supplier_unit_size" value="<?php echo $supplier_unit_size; ?>" placeholder="供应商最小规格：原料规格" id="input-supplier-unit-size" class="form-control" />
                  </div>
              </div>    

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-supplier-order-quantity-type">按箱/按件采购</label>
                <div class="col-sm-10">
                  <select name="supplier_order_quantity_type" id="input-supplier-order-quantity-type" class="form-control">
                    <?php if ($supplier_order_quantity_type == 1) { ?>
                    <option value="1" selected="selected">按箱</option>
                    <option value="2">按件</option>
                    <?php } elseif($supplier_order_quantity_type == 2) { ?>
                    <option value="1">按箱</option>
                    <option value="2" selected="selected">按件</option>
                    <?php } else { ?>
                    <option value="0">全部</option>
                    <option value="1">按箱</option>
                    <option value="2">按件</option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-tax">税率</label>
                <div class="col-sm-10">
                  <input type="text" name="tax" value="<?php echo $tax > 0 ? $tax : 0.00; ?>" placeholder="税率" datatype="*" nullmsg="请输入税率" id="input-tax" class="form-control" />
                  <?php if (isset($tax_warning) && $tax_warning) { ?>
                  <div class="text-danger"><?php echo $tax_warning; ?></div>
                  <?php } ?>
                </div>
              </div>
                
            </div>
              
              
              
            <div class="tab-pane" id="tab-links">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-manufacturer"><span data-toggle="tooltip" title="(输入时自动筛选结果)">产地</span></label>
                <div class="col-sm-10">
                  <input type="text" name="manufacturer" value="<?php echo $manufacturer ?>" placeholder="产地" id="input-manufacturer" class="form-control" />
                  <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer_id; ?>" />
                </div>
              </div>
                
                
                <div class="form-group">
                <label class="col-sm-2 control-label" for="input-supplier"><span data-toggle="tooltip" title="(输入时自动筛选结果)">供应商</span></label>
                <div class="col-sm-10">
                  <input type="text" name="supplier" value="<?php echo $supplier ?>" placeholder="供应商" id="input-supplier" class="form-control" />
                  <input type="hidden" name="supplier_id" value="<?php echo $supplier_id; ?>" />
                </div>
              </div>
                
                
                
                
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sku-category-id"><span data-toggle="tooltip" title="(输入时自动筛选结果)">分类</span></label>
                <div class="col-sm-10">
                  <input type="text" name="sku-category-id" value="" placeholder="分类" id="input-sku-category-id" class="form-control" />
                  <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_categories as $product_category) { ?>
                    <div id="product-category<?php echo $product_category['sku_category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                      <input type="hidden" name="product_category[]" value="<?php echo $product_category['sku_category_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <!--
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-filter"><span data-toggle="tooltip" title="<?php echo $help_filter; ?>"><?php echo $entry_filter; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="filter" value="" placeholder="<?php echo $entry_filter; ?>" id="input-filter" class="form-control" />
                  <div id="product-filter" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_filters as $product_filter) { ?>
                    <div id="product-filter<?php echo $product_filter['filter_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_filter['name']; ?>
                      <input type="hidden" name="product_filter[]" value="<?php echo $product_filter['filter_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              -->
              <!--
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <div class="checkbox">
                      <label>
                        <?php if (in_array(0, $product_store)) { ?>
                        <input type="checkbox" name="product_store[]" value="0" checked="checked" />
                        <?php echo $text_default; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="product_store[]" value="0" />
                        <?php echo $text_default; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php foreach ($stores as $store) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($store['store_id'], $product_store)) { ?>
                        <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                        <?php echo $store['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="product_store[]" value="<?php echo $store['store_id']; ?>" />
                        <?php echo $store['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              -->
              <!--
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-download"><span data-toggle="tooltip" title="<?php echo $help_download; ?>"><?php echo $entry_download; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="download" value="" placeholder="<?php echo $entry_download; ?>" id="input-download" class="form-control" />
                  <div id="product-download" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_downloads as $product_download) { ?>
                    <div id="product-download<?php echo $product_download['download_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_download['name']; ?>
                      <input type="hidden" name="product_download[]" value="<?php echo $product_download['download_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              -->
              
            </div>
            <div class="tab-pane" id="tab-special">
              <div class="table-responsive">
                <table id="special" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left">会员等级</td>
                      <td class="text-right">优先级</td>
                      <td class="text-right">价格</td>
                      <td class="text-left">开始日期</td>
                      <td class="text-left">结束日期</td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                  <?php $special_row = 0; ?>
                  <?php foreach ($product_specials as $product_special) { ?>
                  <tr id="special-row<?php echo $special_row; ?>">
                    <td class="text-left"><select name="product_special[<?php echo $special_row; ?>][customer_group_id]" class="form-control">
                        <?php foreach ($customer_groups as $customer_group) { ?>
                        <?php if ($customer_group['customer_group_id'] == $product_special['customer_group_id']) { ?>
                        <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select></td>
                    <td class="text-right"><input type="text" name="product_special[<?php echo $special_row; ?>][priority]" value="<?php echo $product_special['priority']; ?>" placeholder="优先级" class="form-control" /></td>
                    <td class="text-right"><input type="text" name="product_special[<?php echo $special_row; ?>][price]" value="<?php echo $product_special['price']; ?>" placeholder="价格" class="form-control" /></td>
                    <td class="text-left" style="width: 20%;"><div class="input-group date">
                        <input type="text" name="product_special[<?php echo $special_row; ?>][date_start]" value="<?php echo $product_special['date_start']; ?>" placeholder="开始日期" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                          </span></div></td>
                    <td class="text-left" style="width: 20%;"><div class="input-group date">
                        <input type="text" name="product_special[<?php echo $special_row; ?>][date_end]" value="<?php echo $product_special['date_end']; ?>" placeholder="结束日期" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                          </span></div></td>
                    <td class="text-left"><button type="button" onclick="$('#special-row<?php echo $special_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                  </tr>
                  <?php $special_row++; ?>
                  <?php } ?>
                  </tbody>
                  <tfoot>
                  <tr>
                    <td colspan="5"></td>
                    <td class="text-left"><button type="button" onclick="addSpecial();" data-toggle="tooltip" title="<?php echo $button_special_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                  </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-image">
              <div class="table-responsive">
                <table id="images" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left">图像</td>
                      <td class="text-right">排序</td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $image_row = 0; ?>
                    <?php foreach ($product_images as $product_image) { ?>
                    <tr id="image-row<?php echo $image_row; ?>">
                      <td class="text-left"><a href="" id="thumb-image<?php echo $image_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $product_image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="product_image[<?php echo $image_row; ?>][image]" value="<?php echo $product_image['image']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                      <td class="text-right"><input type="text" name="product_image[<?php echo $image_row; ?>][sort_order]" value="<?php echo $product_image['sort_order']; ?>" placeholder="排序" class="form-control" /></td>
                      <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $image_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2"></td>
                      <td class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="添加图片" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
$('#input-description<?php echo $language['language_id']; ?>').summernote({height: 300});
<?php } ?>
//--></script> 
  <script type="text/javascript"><!--

      
      
      
      
      
      
      
    // Manufacturer
$('input[name=\'product_sku\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/product_sku_autocomplete&token=<?php echo $token; ?>&filter_product_sku=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				json.unshift({
					product_sku_id: 0,
					name: '<?php echo $text_none; ?>'
				});
				
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_sku_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'product_sku\']').val(item['label']);
		$('input[name=\'product_sku_id\']').val(item['value']);
	}	
});  
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      // Manufacturer
$('input[name=\'manufacturer\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/manufacturer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				json.unshift({
					manufacturer_id: 0,
					name: '<?php echo $text_none; ?>'
				});
				
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['manufacturer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'manufacturer\']').val(item['label']);
		$('input[name=\'manufacturer_id\']').val(item['value']);
	}	
});

// supplier
$('input[name=\'supplier\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/supplier/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				json.unshift({
					manufacturer_id: 0,
					name: '<?php echo $text_none; ?>'
				});
				
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['supplier_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'supplier\']').val(item['label']);
		$('input[name=\'supplier_id\']').val(item['value']);
	}	
});


// Category
$('input[name=\'sku-category-id\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/sku/category_autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['sku_category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'sku-category-id\']').val('');
		
		$('#product-category' + item['value']).remove();
		
		$('#product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');	
	}
});

$('#product-category').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// Related
$('input[name=\'related\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'related\']').val('');
		
		$('#product-related' + item['value']).remove();
		
		$('#product-related').append('<div id="product-related' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_related[]" value="' + item['value'] + '" /></div>');	
	}	
});

$('#product-related').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});
//--></script>
<script type="text/javascript"><!--
  var special_row = <?php echo $special_row; ?>;

  function addSpecial() {
    html  = '<tr id="special-row' + special_row + '">';
    html += '  <td class="text-left"><select name="product_special[' + special_row + '][customer_group_id]" class="form-control">';
  <?php foreach ($customer_groups as $customer_group) { ?>
      html += '      <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo addslashes($customer_group['name']); ?></option>';
    <?php } ?>
    html += '  </select></td>';
    html += '  <td class="text-right"><input type="text" name="product_special[' + special_row + '][priority]" value="" placeholder="优先级" class="form-control" /></td>';
    html += '  <td class="text-right"><input type="text" name="product_special[' + special_row + '][price]" value="" placeholder="价格" class="form-control" /></td>';
    html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="text" name="product_special[' + special_row + '][date_start]" value="" placeholder="开始日期" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
    html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="text" name="product_special[' + special_row + '][date_end]" value="" placeholder="结束日期" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
    html += '  <td class="text-left"><button type="button" onclick="$(\'#special-row' + special_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';

    $('#special tbody').append(html);

    $('.date').datetimepicker({
      pickTime: false
    });

    special_row++;
  }
  //--></script>
  <script type="text/javascript"><!--
var image_row = <?php echo $image_row; ?>;

function addImage() {
	html  = '<tr id="image-row' + image_row + '">';
	html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /><input type="hidden" name="product_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
	html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="" placeholder="排序" class="form-control" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	
	$('#images tbody').append(html);
	
	image_row++;
}
//--></script>
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});

$('.time').datetimepicker({
	pickDate: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});
//--></script> 
  <script type="text/javascript"><!--
$('#language a:first').tab('show');
$('#option a:first').tab('show');
//--></script>
<script type="text/javascript">
  $('#form-product').Validform({tiptype: 3});
</script>
</div>
<?php echo $footer; ?> 