<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>编辑商品</h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li style="display: none;" ><a href="#tab-general" data-toggle="tab">常规</a></li>
            <li class="active"><a href="#tab-data" data-toggle="tab">数据</a></li>
            <li style="display: none;"><a href="#tab-links" data-toggle="tab">关联</a></li>
            <li style="display: none;"><a href="#tab-special" data-toggle="tab">特价</a></li>
            <li style="display: none;"><a href="#tab-image" data-toggle="tab">图像</a></li>
          </ul>
          <div class="tab-content">
            <div style="display: none;" class="tab-pane active" id="tab-general">
              <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>">商品名称</label>
                    <div class="col-sm-10">
                      <input type="text" name="product_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['name'] : ''; ?>" placeholder="商品名称" datatype="*" nullmsg="请输入商品名称" id="input-name<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_name[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>">商品摘要(箱规)</label>
                        <div class="col-sm-10">
                            <input type="text" name="product_description[<?php echo $language['language_id']; ?>][abstract]" placeholder="摘要信息，箱装规格等" id="input-abstract<?php echo $language['language_id']; ?>" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['abstract'] : ''; ?>" class="form-control" />
                        </div>
                    </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>">描述</label>
                    <div class="col-sm-10">
                      <textarea name="product_description[<?php echo $language['language_id']; ?>][description]" placeholder="描述" id="input-description<?php echo $language['language_id']; ?>"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['description'] : ''; ?></textarea>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>">Meta Tag 标题</label>
                    <div class="col-sm-10">
                      <input type="text" name="product_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="Meta Tag 标题" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>">Meta Tag 描述</label>
                    <div class="col-sm-10">
                      <textarea name="product_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="Meta Tag 描述" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>">Meta Tag 关键词</label>
                    <div class="col-sm-10">
                      <textarea name="product_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="Meta Tag 关键词" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-tag<?php echo $language['language_id']; ?>"><span data-toggle="tooltip" title="英文逗号分割">商品标签</span></label>
                    <div class="col-sm-10">
                      <input type="text" name="product_description[<?php echo $language['language_id']; ?>][tag]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['tag'] : ''; ?>" placeholder="商品标签" id="input-tag<?php echo $language['language_id']; ?>" class="form-control" />
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
              
              
              
              
              
              
              <div class="tab-pane" id="tab-data" style="display: block;">
                  <div class="form-group">
                      <label class="col-sm-2 control-label">商品编号</label>
                      <div class="col-sm-10" style="padding: 8px 13px; font-size: 16px;">
                          <?php echo $product_id; ?>
                      </div>

                      <label class="col-sm-2 control-label">商品名称</label>
                      <div class="col-sm-10" style="padding: 8px 13px;  font-size: 16px;">
                          <?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['name'] : ''; ?>
                      </div>
                  </div>

                   <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sku">SKU/商品编码</label>
                        <div class="col-sm-10">
                            <input type="text" name="sku" value="<?php echo $sku; ?>" placeholder="SKU/商品编码" id="input-sku" class="form-control" />
                        </div>
                    </div>
                   <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-inv-class-sort">货位号</label>
                        <div class="col-sm-10">
                            <input type="text" name="inv_class_sort" value="<?php echo $inv_class_sort; ?>" placeholder="货位号" id="input-inv-class-sort" class="form-control" />
                        </div>
                    </div>
                   <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-weight-range-least">最小重量比例<br>(除以标准克重的比例，2位小数)</label>
                        <div class="col-sm-10">
                            <input type="text" name="weight_range_least" value="<?php echo $weight_range_least; ?>" placeholder="最小重量比例" id="input-weight-range-least" class="form-control" />
                        </div>
                    </div>
                   <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-weight-range-most">最大重量比例<br>(除以标准克重的比例，2位小数)</label>
                        <div class="col-sm-10">
                            <input type="text" name="weight_range_most" value="<?php echo $weight_range_most; ?>" placeholder="最大重量比例" id="input-weight-range-most" class="form-control" />
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
                    <label class="col-sm-2 control-label" for="input-inv_class">生产分类</label>
                    <div class="col-sm-10">
                      <select name="inv_class" id="input-inv_class" class="form-control">
                        <?php foreach($inv_classes as $ikey=>$ivalue){ ?>
                        <option value="<?php echo $ivalue['product_inv_class_id'];?>" <?php if($inv_class == $ivalue['product_inv_class_id']){ echo 'selected="selected"'; } ?> ><?php echo $ivalue['name'];?></option>
                        <?php } ?>
                      </select>
              </div>
                  </div>
              
              
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-produce_group_id">生产分组</label>
                    <div class="col-sm-10">
                      <select name="produce_group_id" id="input-produce_group_id" class="form-control">
                        <?php foreach($produce_groups as $pkey=>$pvalue){ ?>
                        <option value="<?php echo $pvalue['produce_group_id'];?>" <?php if($produce_group_id == $pvalue['produce_group_id']){ echo 'selected="selected"'; } ?> ><?php echo $pvalue['title'];?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-scan_product">是否可扫商品编号</label>
                    <div class="col-sm-10">
                      <select name="scan_product" id="input-scan_product" class="form-control">
                        <option value="0" <?php echo $scan_product==0?"selected='selected'":""; ?> >否</option>
                        <option value="1" <?php echo $scan_product==1?"selected='selected'":""; ?> >是</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-repack">散发商品(快消)</label>
                    <div class="col-sm-10">
                      <select name="repack" id="input-repack" class="form-control">
                        <option value="0" <?php echo $repack==0?"selected='selected'":""; ?> >非散发</option>
                        <option value="1" <?php echo $repack==1?"selected='selected'":""; ?> >散发</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-product_type_id">商品分拣类型</label>
                    <div class="col-sm-10">
                      <select name="product_type_id" id="input-product_type_id" class="form-control">
                        <option value="0" <?php echo $product_type_id==0 ? "selected='selected'" : ""; ?> >无</option>
                        <?php foreach($productTypes as $m){ ?>
                          <option value="<?php echo $m['product_type_id'];?>" <?php if($product_type_id == $m['product_type_id']){ echo 'selected="selected"'; } ?> ><?php echo $m['type_name'];?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
              
              
              </div>
              
              
              
              
              
              
              
              
              
              
              
              <div class="tab-pane" id="tab-data2" style="display: none;">
              
              <div  class="form-group">
                <label class="col-sm-2 control-label" for="input-image">图像</label>
                <div class="col-sm-10">
                  <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                </div>
              </div>
              
                <div class="form-group required" style="display:none;">
              <label class="col-sm-2 control-label" for="input-model">型号</label>
              <div class="col-sm-10">
                <input type="text" name="model" value="<?php echo $model; ?>" placeholder="型号" id="input-model" class="form-control" />
                <?php if (isset($error_model) && $error_model) { ?>
                <div class="text-danger"><?php echo $error_model; ?></div>
                <?php } ?>
              </div>
              </div>
              
              
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
              <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-price">价格</label>
                  <div class="col-sm-10">
                      <input type="text" name="price" value="<?php echo $price; ?>" placeholder="价格" id="input-price" class="form-control" />
                  </div>
              </div>

              <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-location">产地</label>
                  <div class="col-sm-10">
                      <input type="text" name="location" value="<?php echo $location; ?>" placeholder="产地" id="input-location" class="form-control" />
                  </div>
              </div>

            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-retail_price"><span data-toggle="tooltip" title="出仓打印的条码价格">建议零售价</span></label>
              <div class="col-sm-10">
                <input type="text" name="retail_price" value="<?php echo $retail_price; ?>"  id="input-retail_price" class="form-control" />
              </div>
            </div>

            <!--
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-upc"><span data-toggle="tooltip" title="<?php echo $help_upc; ?>"><?php echo $entry_upc; ?></span></label>
              <div class="col-sm-10">
                <input type="text" name="upc" value="<?php echo $upc; ?>" placeholder="<?php echo $entry_upc; ?>" id="input-upc" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-ean"><span data-toggle="tooltip" title="<?php echo $help_ean; ?>"><?php echo $entry_ean; ?></span></label>
              <div class="col-sm-10">
                <input type="text" name="ean" value="<?php echo $ean; ?>" placeholder="<?php echo $entry_ean; ?>" id="input-ean" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-jan"><span data-toggle="tooltip" title="<?php echo $help_jan; ?>"><?php echo $entry_jan; ?></span></label>
              <div class="col-sm-10">
                <input type="text" name="jan" value="<?php echo $jan; ?>" placeholder="<?php echo $entry_jan; ?>" id="input-jan" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-isbn"><span data-toggle="tooltip" title="<?php echo $help_isbn; ?>"><?php echo $entry_isbn; ?></span></label>
              <div class="col-sm-10">
                <input type="text" name="isbn" value="<?php echo $isbn; ?>" placeholder="<?php echo $entry_isbn; ?>" id="input-isbn" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-mpn"><span data-toggle="tooltip" title="<?php echo $help_mpn; ?>"><?php echo $entry_mpn; ?></span></label>
              <div class="col-sm-10">
                <input type="text" name="mpn" value="<?php echo $mpn; ?>" placeholder="<?php echo $entry_mpn; ?>" id="input-mpn" class="form-control" />
              </div>
            </div>
            -->
              <!--
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-tax-class"><?php echo $entry_tax_class; ?></label>
                <div class="col-sm-10">
                  <select name="tax_class_id" id="input-tax-class" class="form-control">
                    <option value="0"><?php echo $text_none; ?></option>
                    <?php foreach ($tax_classes as $tax_class) { ?>
                    <?php if ($tax_class['tax_class_id'] == $tax_class_id) { ?>
                    <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              -->

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-quantity">数量</label>
                <div class="col-sm-10">
                  <input type="text" name="quantity" value="<?php echo $quantity; ?>" placeholder="数量" id="input-quantity" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-minimum"><span data-toggle="tooltip" title="加入订单时所需最小数量">最小购买数量</span></label>
                <div class="col-sm-10">
                  <input type="text" name="minimum" value="<?php echo $minimum; ?>" placeholder="最小购买数量" id="input-minimum" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-maximum"><span data-toggle="tooltip" title="加入订单时最大数量">最大购买数量</span></label>
                <div class="col-sm-10">
                  <input type="text" name="maximum" value="<?php echo $maximum; ?>" placeholder="最小购买数量" id="input-maximum" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-customer_total_limit"><span data-toggle="tooltip" title="用户最大购买量">用户最大购买量</span></label>
                <div class="col-sm-10">
                  <input type="text" name="customer_total_limit" value="<?php echo $customer_total_limit; ?>" placeholder="最小购买数量" id="input-customer_total_limit" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-wxpay_only">限微信支付</label>
                <div class="col-sm-10">
                  <select name="wxpay_only" id="input-wxpay_only" class="form-control">
                    <option value="1" <?php if($wxpay_only == '1'){ echo 'selected="selected"'; } ?> >是</option>
                    <option value="0" <?php if($wxpay_only == '0'){ echo 'selected="selected"'; } ?> >否</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-shelf_life"><span data-toggle="tooltip" title="从包装日起的天数">最佳食用期</span></label>
                <div class="col-sm-10">
                  <input type="text" name="shelf_life" value="<?php echo $shelf_life; ?>" placeholder="最佳食用期，从包装日起的天数" id="input-shelf_life" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-issupportstore"><span data-toggle="tooltip" title="是否支持线下零售">是否支持线下零售</span></label>
                <div class="col-sm-10">
                  <select name="issupportstore" id="input-issupportstore" class="form-control">
                    <option value="1" <?php if($issupportstore == '1'){ echo 'selected="selected"'; } ?> >是</option>
                    <option value="0" <?php if($issupportstore == '0'){ echo 'selected="selected"'; } ?> >否</option>
                  </select>
                </div>
              </div>
              <!--
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-subtract">减少库存</label>
                <div class="col-sm-10">
                  <select name="subtract" id="input-subtract" class="form-control">
                    <?php if ($subtract) { ?>
                    <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                    <option value="0"><?php echo $text_no; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_yes; ?></option>
                    <option value="0" selected="selected"><?php echo $text_no; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>-->
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-stock-status"><span data-toggle="tooltip" title="缺少该商品时所显示的数量">缺货时状态</span></label>
                <div class="col-sm-10">
                  <select name="stock_status_id" id="input-stock-status" class="form-control">
                    <?php foreach ($stock_statuses as $stock_status) { ?>
                    <?php if ($stock_status['stock_status_id'] == $stock_status_id) { ?>
                    <option value="<?php echo $stock_status['stock_status_id']; ?>" selected="selected"><?php echo $stock_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $stock_status['stock_status_id']; ?>"><?php echo $stock_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <!--
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_shipping; ?></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <?php if ($shipping) { ?>
                    <input type="radio" name="shipping" value="1" checked="checked" />
                    <?php echo $text_yes; ?>
                    <?php } else { ?>
                    <input type="radio" name="shipping" value="1" />
                    <?php echo $text_yes; ?>
                    <?php } ?>
                  </label>
                  <label class="radio-inline">
                    <?php if (!$shipping) { ?>
                    <input type="radio" name="shipping" value="0" checked="checked" />
                    <?php echo $text_no; ?>
                    <?php } else { ?>
                    <input type="radio" name="shipping" value="0" />
                    <?php echo $text_no; ?>
                    <?php } ?>
                  </label>
                </div>
              </div>
              -->
              <!--
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-keyword"><span data-toggle="tooltip" title="不要用空格，使用-链接关键字，确保该关键词为全站唯一。">SEO Keyword</span></label>
                <div class="col-sm-10">
                  <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="SEO Keyword" id="input-keyword" class="form-control" />
                  <?php if (isset($error_keyword) && $error_keyword) { ?>
                  <div class="text-danger"><?php echo $error_keyword; ?></div>
                  <?php } ?>               
                </div>
              </div>
              -->
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
                <label class="col-sm-2 control-label" for="input-length">尺寸 (长 x 宽 x 高)</label>
                <div class="col-sm-10">
                  <div class="row">
                    <div class="col-sm-4">
                      <input type="text" name="length" value="<?php echo $length; ?>" placeholder="长" id="input-length" class="form-control" />
                    </div>
                    <div class="col-sm-4">
                      <input type="text" name="width" value="<?php echo $width; ?>" placeholder="宽" id="input-width" class="form-control" />
                    </div>
                    <div class="col-sm-4">
                      <input type="text" name="height" value="<?php echo $height; ?>" placeholder="高" id="input-height" class="form-control" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-length-class">尺寸单位</label>
                <div class="col-sm-10">
                  <select name="length_class_id" id="input-length-class" class="form-control">
                    <?php foreach ($length_classes as $length_class) { ?>
                    <?php if ($length_class['length_class_id'] == $length_class_id) { ?>
                    <option value="<?php echo $length_class['length_class_id']; ?>" selected="selected"><?php echo $length_class['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $length_class['length_class_id']; ?>"><?php echo $length_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
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
                <label class="col-sm-2 control-label" for="input-sort-order">排序</label>
                <div class="col-sm-10">
                  <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="排序" id="input-sort-order" class="form-control" />
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-links">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-manufacturer"><span data-toggle="tooltip" title="(输入时自动筛选结果)">制造商/品牌</span></label>
                <div class="col-sm-10">
                  <input type="text" name="manufacturer" value="<?php echo $manufacturer ?>" placeholder="制造商/品牌" id="input-manufacturer" class="form-control" />
                  <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer_id; ?>" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="(输入时自动筛选结果)">分类</span></label>
                <div class="col-sm-10">
                  <input type="text" name="category" value="" placeholder="分类" id="input-category" class="form-control" />
                  <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_categories as $product_category) { ?>
                    <div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                      <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
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
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-related"><span data-toggle="tooltip" title="(输入时自动筛选结果)">相关商品</span></label>
                <div class="col-sm-10">
                  <input type="text" name="related" value="" placeholder="相关商品" id="input-related" class="form-control" />
                  <div id="product-related" class="well well-sm" style="height: 150px; overflow: auto;">
                    <?php foreach ($product_relateds as $product_related) { ?>
                    <div id="product-related<?php echo $product_related['product_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_related['name']; ?>
                      <input type="hidden" name="product_related[]" value="<?php echo $product_related['product_id']; ?>" />
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
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

// Category
$('input[name=\'category\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'category\']').val('');
		
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