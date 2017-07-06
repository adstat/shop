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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>编辑商品:[<?php echo $product_id; ?>--<?php echo $name; ?>]</h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal" onsubmit="return check()">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">常规</a></li>
            <li><a href="#tab-data" data-toggle="tab">数据</a></li>
            <li><a href="#tab-links" data-toggle="tab">关联</a></li>
            <?php if($rbac){ ?><li style="display:none"><a href="#tab-special" data-toggle="tab">特价</a></li><?php } ?>
            <li style="display: none"><a href="#tab-special-history" data-toggle="tab" onclick="specialHistory();">特价记录</a></li>
            <li><a href="#tab-reward" data-toggle="tab"><?php echo $tab_reward; ?></a></li>
            <!--<?php if($rbac){ ?><li><a href="#tab-discount" data-toggle="tab">多件折扣(覆盖特价)</a></li><?php } ?>-->
            <li style="display: none"><a href="#tab-modify" data-toggle="tab" onclick="modifyHistory();"> 商品属性修改历史</a></li>
            <li><a href="#tab-warehouse" data-toggle="tab">商品仓库属性</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">


                <div class="form-group">
                    <label class="col-sm-1" for="input-product-sku">商品原料<br /><span style="color:red;">(不能修改)</span></label>
                
                    <div class="col-sm-3">
                      <input type="text" name="product_sku" value="<?php echo isset($product_sku['name']) ? $product_sku['name'] : ''; ?>" placeholder="原料" id="input-product-sku" class="form-control" />
                      <input type="hidden" name="product_sku_id" value="<?php echo $product_sku_id; ?>" />
                    </div>
                    <?php if (isset($error_product_sku_id)) { ?>
                      <div class="text-danger"><?php echo $error_product_sku_id; ?></div>
                    <?php } ?>


                    <label class="col-sm-1" for="input-product-type">商品类型<br /><span style="color:red;">(不能修改)</span></label>
                    <div class="col-sm-3">
                        <select name="product_type" id="input-product-type" class="form-control">
                            <option value="1" <?php if($product_type == 1){ echo 'selected="selected"'; } ?> >生鲜(包装菜)</option>
                            <option value="2" <?php if($product_type == 2){ echo 'selected="selected"'; } ?> >非生鲜</option>

                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-1" for="input-station-id">是否快销品<br /><span style="color:red;">(不能修改)</span></label>
                    <div class="col-sm-2">
                        <select name="station_id" id="input-station-id" class="form-control">
                            <option value="1" <?php if($station_id == 1){ echo 'selected="selected"'; } ?> >否</option>
                            <option value="2" <?php if($station_id == 2){ echo 'selected="selected"'; } ?> >是</option>
                        </select>
                    </div>


                    <label class="col-sm-1" for="input-weight-inv-flag">按重出库<br /><span style="color:red;">(不能修改)</span></label>
                    <div class="col-sm-2">
                        <select name="weight_inv_flag" id="input-weight-inv-flag" class="form-control">
                            <option value="1" <?php if($weight_inv_flag == 1){ echo 'selected="selected"'; } ?> >是</option>
                            <option value="0" <?php if($weight_inv_flag == 0){ echo 'selected="selected"'; } ?> >否</option>
                        </select>
                    </div>

                    <label class="col-sm-1" for="input-agent-id">是否代理商专用商品<br /><span style="color:red;">(不能修改)</span></label>
                    <div class="col-sm-2">
                        <select name="agent_id" id="input-agent-id" class="form-control">
                            <option value="1" <?php if($agent_id == 1){ echo 'selected="selected"'; } ?> >是</option>
                            <option value="0" <?php if($agent_id == 0){ echo 'selected="selected"'; } ?> >否</option>
                        </select>
                    </div>
              </div>

              <div class="form-group">
                  <label class="col-sm-1" for="input-is-gift">是否赠品<br /><span style="color:red;">(不能修改)</span></label>
                  <div class="col-sm-2">
                      <select name="is_gift" id="input-is-gift" class="form-control">
                          <option value="1" <?php if($is_gift == 1){ echo 'selected="selected"'; } ?> >是</option>
                          <option value="0" <?php if($is_gift == 0){ echo 'selected="selected"'; } ?> >否</option>
                      </select>
                  </div>

                  <label class="col-sm-1" for="input-repack">是否散发<br /><span style="color:red;">(不能修改)</span></label>
                  <div class="col-sm-2">
                      <select name="repack" id="input-repack" class="form-control">
                          <option value="1" <?php if($repack == 1){ echo 'selected="selected"'; } ?> >是</option>
                          <option value="0" <?php if($repack == 0){ echo 'selected="selected"'; } ?> >否</option>
                      </select>
                  </div>

                  <label class="col-sm-1" for="input-is-replenish-gift">是否可以补送<br /><span style="color:red;">(不能修改)</span></label>
                  <div class="col-sm-2">
                      <select name="is_replenish_gift" id="input-is-replenish-gift" class="form-control">
                          <option value="1" <?php if($is_replenish_gift == 1){ echo 'selected="selected"'; } ?> >是</option>
                          <option value="0" <?php if($is_replenish_gift == 0){ echo 'selected="selected"'; } ?> >否</option>
                      </select>
                  </div>
              </div>

              <div class="form-group">
                  <label class="col-sm-1" for="input-instock">是否自有商品<br /><span style="color:red;">(不能修改)</span></label>
                  <div class="col-sm-2">
                      <select name="instock" id="input-instock" class="form-control">
                          <option value="1" <?php if($instock == 1){ echo 'selected="selected"'; } ?> >是</option>
                          <option value="0" <?php if($instock == 0){ echo 'selected="selected"'; } ?> >否</option>
                      </select>
                  </div>
                  <label class="col-sm-1" for="input-is-selected">是否精选商品<br /><span style="color:red;">(不能修改)</span></label>
                  <div class="col-sm-2">
                      <select name="is_selected" id="input-is-selected" class="form-control">
                          <option value="1" <?php if($is_selected == 1){ echo 'selected="selected"'; } ?> >是</option>
                          <option value="0" <?php if($is_selected == 0){ echo 'selected="selected"'; } ?> >否</option>
                      </select>
                  </div>

                  <label class="col-sm-1" for="input-is-soon-to-expire">是否临期商品<br /><span style="color:red;">(不能修改)</span></label>
                  <div class="col-sm-2">
                      <select name="is_soon_to_expire" id="input-is-soon-to-expire" class="form-control">
                          <option value="1" <?php if($is_soon_to_expire == 1){ echo 'selected="selected"'; } ?> >是</option>
                          <option value="0" <?php if($is_soon_to_expire == 0){ echo 'selected="selected"'; } ?> >否</option>
                      </select>
                  </div>
              </div>

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
                        <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>">新品展示</label>
                        <div class="col-sm-3">
                            <div class="input-group date">
                                <input type="text" name="date_new_on" value="<?php echo $date_new_on; ?>" data-date-format="YYYY-MM-DD" id="input-date-new-on" class="form-control" />
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group date">
                                <input type="text" name="date_new_off" value="<?php echo $date_new_off; ?>" data-date-format="YYYY-MM-DD" id="input-date-new-off" class="form-control" />
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>

                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>">描述</label>
                    <div class="col-sm-10">
                      <textarea name="product_description[<?php echo $language['language_id']; ?>][description]" placeholder="描述" id="input-description<?php echo $language['language_id']; ?>"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['description'] : ''; ?></textarea>
                    </div>
                  </div>

                  <div class="form-group" style="display: none">
                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>">Meta Tag 标题</label>
                    <div class="col-sm-10">
                      <input type="text" name="product_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="Meta Tag 标题" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group" style="display: none">
                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>">Meta Tag 描述</label>
                    <div class="col-sm-10">
                      <textarea name="product_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="Meta Tag 描述" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($product_description[$language['language_id']]) ? $product_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group" style="display: none">
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
            <div class="tab-pane" id="tab-data">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-image">图像</label>
                <div class="col-sm-10">
                  <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                </div>
              </div>

              <div style="border: #df8505 2px dashed; padding: 10px 0; margin: 10px 0;">
                  <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-status">状态</label>
                      <div class="col-sm-6">
                          <?php if($rbac){ ?>
                          <select name="status" id="input-status" class="form-control">
                          <?php }else{ ?>
                          <select name="status" id="input-status" class="form-control" disabled="disabled">
                          <?php } ?>
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
                      <label class="col-sm-2 control-label" for="input-price">价格</label>
                      <div class="col-sm-6">
                          <input type="text" id="set_price" name="price" value="<?php echo $price; ?>" placeholder="价格" id="input-price" class="form-control" />
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-price-protect">价格保护</label>
                      <div class="col-sm-6">
                          <span style="color:red;">改动售价不能低于价格保护与采购平均价的乘积</span>
                          <?php if($rbac){ ?>
                          <input type="text" name="price_protect" value="<?php echo $price_protect; ?>" placeholder="价格保护" id="input-price-protect" class="form-control"/>
                          <?php }else{ ?>
                          <input type="text" name="price_protect" value="<?php echo $price_protect; ?>" placeholder="价格保护" id="input-price-protect" class="form-control" readonly/>
                          <?php } ?>
                          <input type="hidden" name="purchase_cost" id="purchase_cost" value="<?php echo $purchase_cost; ?>" placeholder="价格保护" id="input-price-protect" class="form-control"/>
                      </div>
                  </div>

                  <!--商品设置基础属性价格是否应用到所有平台下的仓库是否 -->
                  <!--<div class="from-group">
                      <label class="col-sm-2 control-label" for="input-model">仓库列表</label>
                      <div class="col-sm-10">
                          <?php foreach($warehouses as $value){ ?>
                          <div class="row">
                              <div class="col-md-2">
                                  <div class="checkbox">
                                      <label>
                                          <input type="checkbox" name="warehouse_product[select][<?php echo $value['warehouse_id']; ?>]" value="<?php echo $value['warehouse_id']; ?>" <?php if(array_key_exists($value['warehouse_id'],$warehousePrices)){ ?> checked = "checked" <?php } ?>><?php echo $value['title']; ?>
                                      </label>
                                  </div>
                              </div>
                              <div class="col-md-8">
                                  <label class="col-sm-4 control-label" for="input-price-protect">仓库价格</label>
                                  <div class="col-sm-4">
                                  <input type="text" name="warehouse_product[price][<?php echo $value['warehouse_id']; ?>]" value="<?php echo array_key_exists($value['warehouse_id'],$warehousePrices)?$warehousePrices[$value['warehouse_id']]['w_price'] : ''; ?>" placeholder="仓库价格" id="input-price-warehouse" class="form-control"/>
                                  </div>
                              </div>
                          </div>
                          <?php } ?>
                      </div>
                  </div>-->

                  <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-cashback">商品返利<br /><span style="color:red;" data-toggle="tooltip" title="下单且订单成功配送后，返现到用户余额账户">配送后返现</span></label>
                      <div class="col-sm-6">
                          商品返利规则待定，暂时停用
                          <input type="text" name="cashback" value="<?php echo $cashback; ?>" placeholder="商品返利" id="input-cashback" class="form-control" style="display: none" />
                      </div>
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
                  <label class="col-sm-2 control-label" for="input-sku"><span data-toggle="tooltip" title="使用成品标准条码，无编码使用商品ID(先保存获取商品ID)">SKU/商品编码</span></label>
                  <div class="col-sm-10">
                      <input type="text" name="sku" value="<?php echo $sku; ?>" placeholder="使用成品标准条码，无编码使用商品ID(先保存获取商品ID)" id="input-sku" class="form-control" />
                      
                  </div>
                      <?php if (isset($error_sku)) { ?>
                      <div class="text-danger"><?php echo $error_sku; ?></div>
                      <?php } ?>
                  </div>
              <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-inv-class-sort">货位号</label>
                  <div class="col-sm-10">
                      <input type="text" name="inv_class_sort" value="<?php echo $inv_class_sort; ?>" placeholder="货位号" id="input-inv-class-sort" class="form-control" />
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
                    <label class="col-sm-2 control-label" for="input-protuct-type-id">商品属性</label>
                    <div class="col-sm-10">
                        <select name="product_type_id" id="input-product-type-id" class="form-control">
                            <?php foreach ($product_type_ids as $product_type_id) { ?>
                            <?php if ($product_type_id['product_type_id'] == $product_type_id) { ?>
                            <option value="<?php echo $product_type_id['product_type_id']; ?>" selected="selected"><?php echo $product_type_id['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $product_type_id['product_type_id']; ?>"><?php echo $product_type_id['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
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
                <label class="col-sm-2 control-label" for="input-safestock">安全库存(可以为负值)</label>
                <div class="col-sm-10">
                  <input type="text" name="safestock" value="<?php echo $safestock; ?>" placeholder="安全库存" id="input-safestock" class="form-control" />
                </div>
              </div>
              
              
              
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-minimum"><span data-toggle="tooltip" title="加入订单时所需最小数量">最少购买量(停用)</span></label>
                <div class="col-sm-10">
                  <input type="text" name="minimum" value="<?php echo $minimum; ?>" placeholder="最小购买数量" id="input-minimum" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-maximum"><span data-toggle="tooltip" title="每天最多购买数量">每日最多购买量</span></label>
                <div class="col-sm-10">
                  <input type="text" name="maximum" value="<?php echo $maximum; ?>" placeholder="最小购买数量" id="input-maximum" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-customer_total_limit"><span data-toggle="tooltip" title="每用户历史累计最大购买数量">累计最大购买量</span></label>
                <div class="col-sm-10">
                  <input type="text" name="customer_total_limit" value="<?php echo $customer_total_limit; ?>" placeholder="最小购买数量" id="input-customer_total_limit" class="form-control" />
                </div>
              </div>
              <div class="form-group" style="display: none">
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
              <div class="form-group"  style="display: none">
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
              <div class="form-group" style="display: none">
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
              <div class="form-group"  style="display: none">
                <label class="col-sm-2 control-label" for="input-date-available">上架日期</label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_available" value="<?php echo $date_available; ?>" placeholder="上架日期" data-date-format="YYYY-MM-DD" id="input-date-available" class="form-control" />
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>
              <div class="form-group"  style="display: none">
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
              <div class="form-group"  style="display: none">
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
                <label class="col-sm-2 control-label" for="input-weight">销售数量值</label>
                <div class="col-sm-10">
                  <input type="text" name="weight" value="<?php echo $weight; ?>" placeholder="销售数量值" id="input-weight" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-weight-class">销售单位</label>
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
                <label class="col-sm-2 control-label" for="input-unit-size">个体数量</label>
                <div class="col-sm-10">
                  <input type="text" name="unit_size" value="<?php echo $unit_size; ?>" placeholder="个体数量" id="input-unit-size" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-unit-weight-class-id">个体单位</label>
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
                <label class="col-sm-2 control-label" for="input-inv-size">打包个体数量</label>
                <div class="col-sm-10">
                  <input type="text" name="inv_size" value="<?php echo $inv_size; ?>" placeholder="打包个体数量" id="input-inv-size" class="form-control" />
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
                <label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="(输入时自动筛选结果)">唯一分类</span></label>
                <div class="col-sm-10">
                  <input type="text" name="category" value="" placeholder="分类" id="input-category" class="form-control" />
                  <div id="product-category" class="well well-sm" style="height: 50px; overflow: auto;">
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
              <diass="table-responsive">
                <table id="special" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left" style="display: none">会员等级</td>
                      <td class="text-right">置顶</td>
                      <td class="text-right">置顶标题(快消)</td>
                      <td class="text-right">限量</td>
                      <td class="text-right" style="display: none">优先级</td>
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
                    <td class="text-left" style="display: none"><select name="product_special[<?php echo $special_row; ?>][customer_group_id]" class="form-control">
                        <?php foreach ($customer_groups as $customer_group) { ?>
                        <?php if ($customer_group['customer_group_id'] == $product_special['customer_group_id']) { ?>
                        <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select></td>
                    <td class="text-right"><input type="checkbox" <?php echo $product_special['showup'] ? 'checked="checked"' : '' ?> name="product_special[<?php echo $special_row; ?>][showup]" value="<?php echo $product_special['showup']; ?>" class="form-control" onchange="$(this).is(':checked') ? $(this).val(1) : $(this).val(0)" /></td>
                    <td class="text-right"><input type="text" name="product_special[<?php echo $special_row; ?>][promo_title]" value="<?php echo $product_special['promo_title']; ?>" placeholder="促销标题" class="form-control" /></td>
                    <td class="text-right"><input type="number" name="product_special[<?php echo $special_row; ?>][maximum]" value="<?php echo $product_special['maximum']; ?>" placeholder="限量" class="form-control" /></td>
                    <td class="text-right" style="display: none"><input type="text" name="product_special[<?php echo $special_row; ?>][priority]" value="<?php echo $product_special['priority']; ?>" placeholder="优先级" class="form-control" /></td>
                    <td class="text-right" style="display: none"><input type="text" name="product_special[<?php echo $special_row; ?>][priority]" value="<?php echo $product_special['priority']; ?>" placeholder="优先级" class="form-control" /></td>
                    <td class="text-right"><input type="text" name="product_special[<?php echo $special_row; ?>][price]" value="<?php echo $product_special['price']; ?>" placeholder="价格" class="form-control" /></td>
                    <td class="text-left" style="width: 20%;"><div class="input-group date">
                        <input type="text" name="product_special[<?php echo $special_row; ?>][date_start]" value="<?php echo $product_special['date_start']; ?>" placeholder="开始日期" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></b HH:mm:ssutton>
                          </span></div></td>
                    <td class="text-left" style="width: 20%;"><div class="input-group date">
                        <input type="text" name="product_special[<?php echo $special_row; ?>][date_end]" value="<?php echo $product_special['date_end']; ?>" placeholder="结束日期" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                          </span></div></td>
                    <td class="text-left"><button type="button" onclick="$('#special-row<?php echo $special_row; ?>').remove(); $('#addSpecialButton').show();" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                  </tr>
                  <?php $special_row++; ?>
                  <?php } ?>
                  </tbody>
                  <tfoot>
                  <tr id="addSpecialButton">
                    <td colspan="6"></td>
                    <td class="text-left"><button type="button" onclick="addSpecial();"  class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                  </tr>
                  </tfoot>
                </table>
              </div>

              <div class="tab-pane" id="tab-special-history">
                <div id="special-history"></div>
              </div>

              <div class="tab-pane" id="tab-reward">
                <div class="form-group">
                    <label class="col-lg-2 control-label" for="input-points"><span data-toggle="tooltip" title="<?php echo $help_points; ?>">积分值</span></label>
                    <div class="col-lg-3">
                        <input type="text" name="product_reward" value="<?php echo $product_reward; ?>" placeholder="<?php echo $entry_points; ?>" id="input-points" class="form-control" />
                    </div>
                </div>
                <!--
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <td class="text-left"><?php echo $entry_customer_group; ?></td>
                            <td class="text-right"><?php echo $entry_reward; ?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($customer_groups as $customer_group) { ?>
                        <tr>
                            <td class="text-left"><?php echo $customer_group['name']; ?></td>
                            <td class="text-right"><input type="text" name="product_reward[<?php echo $customer_group['customer_group_id']; ?>][points]" value="<?php echo isset($product_reward[$customer_group['customer_group_id']]) ? $product_reward[$customer_group['customer_group_id']]['points'] : ''; ?>" class="form-control" /></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                -->
              </div>

              <div class="tab-pane" id="tab-discount" style="display:none">
                <div class="table-responsive">
                    <table id="discount" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <td class="text-right"><?php echo $entry_quantity; ?></td>
                            <td class="text-right"><?php echo $entry_price; ?></td>
                            <td class="text-left"><?php echo $entry_date_start; ?></td>
                            <td class="text-left"><?php echo $entry_date_end; ?></td>
                            <?php if(sizeof($product_discounts)){ ?>
                            <td class="text-right">设置人</td>
                            <?php } ?>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $discount_row = 0; ?>
                        <?php foreach ($product_discounts as $product_discount) { ?>
                        <tr id="discount-row<?php echo $discount_row; ?>">
                            <td class="text-right"><input type="text" name="product_discount[<?php echo $discount_row; ?>][quantity]" value="<?php echo $product_discount['quantity']; ?>" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>
                            <td class="text-right"><input type="text" name="product_discount[<?php echo $discount_row; ?>][price]" value="<?php echo $product_discount['price']; ?>" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>
                            <td class="text-left" style="width: 20%;"><div class="input-group date">
                                    <input type="text" name="product_discount[<?php echo $discount_row; ?>][date_start]" value="<?php echo $product_discount['date_start']; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                          </span></div></td>
                            <td class="text-left" style="width: 20%;"><div class="input-group date">
                                    <input type="text" name="product_discount[<?php echo $discount_row; ?>][date_end]" value="<?php echo $product_discount['date_end']; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
                          <span class="input-group-btn">
                          <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                          </span></div></td>
                            <?php if(sizeof($product_discounts)){ ?>
                            <td class="text-right"><input type="text" name="product_discount[<?php echo $discount_row; ?>][username]" value="<?php echo $product_discount['username']; ?>" placeholder="操作人" class="form-control" /></td>
                            <?php } ?>
                            <td class="text-left"><button type="button" onclick="$('#discount-row<?php echo $discount_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                        </tr>
                        <?php $discount_row++; ?>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="6"></td>
                            <td class="text-left"><button type="button" onclick="addDiscount();" data-toggle="tooltip" title="<?php echo $button_discount_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
              </div>

              <div class="tab-pane" id="tab-modify">
                  <div id="modifyHistory"></div>
              </div>

              <div class="tab-pane" id="tab-warehouse">
                  <div class="alert alert-info">
                      点击"应用到所有"可以同时该商品在所有仓库的信息,分拣条码,货位信息仓库独立维护,可售库存为变动值,系统提供。
                  </div>
                  <h3><strong>商品信息：<?php echo $name; ?></strong></h3>
                  <div class="table-responsive">
                      <table id="warehouse" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <td>仓库</td>
                            <td>分仓摘要</td>
                            <td>价格</td>
                            <!--<td>积分</td>-->
                            <td style="display:none">安全库存</td>
                            <td style="display:none">每天限量</td>
                            <td>是否覆盖</td>
                            <td style="display:none">分拣条码</td>
                            <td>货位</td>
                            <td>可售库存</td>
                            <!--<td>编辑</td>-->
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($warehouses as $warehouse){ ?>
                        <?php $warehouse_row = $warehouse['warehouse_id']; ?>
                        <tr id="warehosue-row<?php echo $warehouse_row; ?>">
                            <td class="text-center"><?php echo $warehouse['title']; ?></td>
                            <td class="text-center"><input type="text" name="warehouse_info[<?php echo $warehouse_row; ?>][abstract]" value="<?php echo array_key_exists($warehouse['warehouse_id'],$warehousePrices) ? $warehousePrices[$warehouse['warehouse_id']]['abstract'] : '分仓摘要'; ?>"/></td>
                            <td class="text-center"><input type="text" name="warehouse_info[<?php echo $warehouse_row; ?>][price]" value="<?php echo array_key_exists($warehouse['warehouse_id'],$warehousePrices) ? $warehousePrices[$warehouse['warehouse_id']]['w_price'] : '仓库价格'; ?>"/></td>
                            <!--<td class="text-center"><input type="text" name="warehouse_info[<?php echo $warehouse_row; ?>][points]" value="<?php echo array_key_exists($warehouse['warehouse_id'],$warehousePrices) ? $warehousePrices[$warehouse['warehouse_id']]['points'] : '仓库价格'; ?>"/></td>-->
                            <td style="display:none" class="text-center"><input type="text" name="warehouse_info[<?php echo $warehouse_row; ?>][safe_stock]" value="<?php echo array_key_exists($warehouse['warehouse_id'],$warehousePrices) ? $warehousePrices[$warehouse['warehouse_id']]['safe_stock'] : '安全库存'; ?>"/></td>
                            <td style="display:none" class="text-center"><input type="text" name="warehouse_info[<?php echo $warehouse_row; ?>][daily_limit]" value="<?php echo array_key_exists($warehouse['warehouse_id'],$warehousePrices) ? $warehousePrices[$warehouse['warehouse_id']]['daily_limit'] : '安全库存'; ?>"/></td>
                            <td class="text-center">
                                <input type="checkbox" name="warehouse_info[<?php echo $warehouse_row; ?>][status]" <?php if(array_key_exists($warehouse['warehouse_id'],$warehousePrices) ? $warehousePrices[$warehouse['warehouse_id']]['status'] : 0){ ?> checked="checked" <?php } ?> value="1"/>
                            </td>
                            <td class="text-center"style="display:none" ><?php echo array_key_exists($warehouse['warehouse_id'],$warehousePrices) ? $warehousePrices[$warehouse['warehouse_id']]['sku_barcode'] : '  分拣条码' ; ?></td>
                            <td class="text-center"><?php echo array_key_exists($warehouse['warehouse_id'],$warehousePrices) ? $warehousePrices[$warehouse['warehouse_id']]['stock_area'] : '货位' ; ?></td>
                            <td class="text-center"><?php echo array_key_exists($warehouse['warehouse_id'],$warehousePrices) ? $warehousePrices[$warehouse['warehouse_id']]['inventory'] : '可售库存' ; ?></td>
                            <!--<td class="text-center"><button type="button" value="<?php echo $warehouse_row ; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button></td>-->
                        </tr>
                        <?php } ?>
                        </tbody>
                      </table>
                  </div>
                  <hr style=" height:2px;border:none;border-top:2px dotted #185598;" />

                  <div id="all-warehouse-set" <?php if($filter_warehouse_id_global) { ?> style="display:none" <?php } ?>>
                      <div class="table-responsive">
                          <table>
                              <tr>
                                  <td>
                                      <input type="checkbox" name="warehouse_all_set" id="warehouse-all-set"  value="1" class="form-control" />
                                  </td>
                                  <td>
                                      应用到所有仓库
                                  </td>
                                  <td class="text-right">
                                      <!--<button style="margin-left: 110px" class="btn btn-primary"><i class="fa fa-save"></i></button>-->
                                  </td>
                              </tr>
                          </table>
                      </div>

                      <div class="col-sm-6" style="margin-top: 10px">
                          <div class="form-group">
                              <label class="col-sm-2 control-label">分仓摘要</label>
                              <div class="col-sm-4">
                                  <input type="text" name="warehouse_info_all[abstract]" value="" placeholder="分仓摘要" class="form-control" />
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-2 control-label">价格</label>
                              <div class="col-sm-4">
                                  <input type="text" name="warehouse_info_all[price]" value="" placeholder="价格" class="form-control" />
                              </div>
                          </div>
                          <div class="form-group" style="display:none">
                              <label class="col-sm-2 control-label">积分</label>
                              <div class="col-sm-4">
                                  <input type="text" name="warehouse_info_all[points]" value="" placeholder="积分" class="form-control" />
                              </div>
                          </div>
                          <div class="form-group" style="display:none">
                              <label class="col-sm-2 control-label">安全库存</label>
                              <div class="col-sm-4">
                                  <input type="text" name="warehouse_info_all[safe_stock]" value="" placeholder="安全库存" class="form-control" />
                              </div>
                          </div>
                          <div class="form-group" style="display:none">
                              <label class="col-sm-2 control-label" >每天限量</label>
                              <div class="col-sm-4">
                                  <input type="text" name="warehouse_info_all[daily_limit]" value="" placeholder="每天限量" class="form-control" />
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-sm-2 control-label">是否启用</label>
                              <div class="col-sm-4" style="margin-top: 10px">
                                  <input type="checkbox" name="warehouse_info_all[status]" value="1" class="form-control" />
                              </div>
                          </div>
                      </div>
                  </div>

              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
$('#input-description<?php echo $language['language_id']; ?>').summernote({height: 100});
<?php } ?>
//--></script>
  <script type="text/javascript"><!--
function check_submit() {
    var price = $('#set_price').val();
    var purchase_cost = $('#purchase_cost').val();
    var flag = confirm('售价为['+price+']采购价为['+purchase_cost+'],是否提交?');
    if(flag){
        $("#form-product").submit(function(e){
            return true;
        });
    }else{
        $("#form-product").submit(function(e){
           return false;
        });
    }
}
//special-history
function specialHistory(){
  $('#special-history').load('index.php?route=catalog/product/specialhistory&token=<?php echo $token; ?>&product_id=<?php echo $product_id; ?>');
}

function modifyHistory(){
  $('#modifyHistory').load('index.php?route=catalog/product/producthistory&token=<?php echo $token; ?>&product_id=<?php echo $product_id; ?>');
}

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
		
		$('#product-category').html('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');
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

$('#special-history').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();
  $('#special-history').load(this.href);
});

$('#modifyHistory').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();
  $('#modifyHistory').load(this.href);
});

//--></script>

<script type="text/javascript">
<!--

    var discount_row = <?php echo $discount_row; ?>;

    function addDiscount() {
        html  = '<tr id="discount-row' + discount_row + '">';
        html += '  <td class="text-right"><input type="text" name="product_discount[' + discount_row + '][quantity]" value="" placeholder="<?php echo $entry_quantity; ?>" class="form-control" /></td>';
        html += '  <td class="text-right"><input type="text" name="product_discount[' + discount_row + '][price]" value="" placeholder="<?php echo $entry_price; ?>" class="form-control" /></td>';
        html += '  <td class="text-left"><div class="input-group date"><input type="text" name="product_discount[' + discount_row + '][date_start]" value="" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
        html += '  <td class="text-left"><div class="input-group date"><input type="text" name="product_discount[' + discount_row + '][date_end]" value="" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
        html += '  <td class="text-left"><button type="button" onclick="$(\'#discount-row' + discount_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';

        $('#discount tbody').append(html);

        $('.date').datetimepicker({
            pickTime: false
        });

        discount_row++;
    }

  var special_row = <?php echo $special_row; ?>;
  var special_index = special_row - 1;
  var special_price = <?php echo $protect_price; ?>;

  function check() {

      var warehouse_id = $('#warehouse-all-set').prop('checked');
      if(warehouse_id){
        var flag = confirm("当前应用到全部仓库被选择，请确认是否进行本次操作，或者取消应用到所有仓库");
        if(flag){
            var price = $('#set_price').val();
            var purchase_cost = $('#purchase_cost').val();
            var price_protect = $('input[name=\'price_protect\']').val();
            var price_limit = purchase_cost*price_protect;
            //售价的设置不允许低于平均采购价与price_protect的乘积
            if(parseFloat(price_limit) > parseFloat(price)){
                alert('售价为['+price+']售价限制为['+price_limit+'],当前售价限制大于销售价，不予提交！');
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
      }
    //暂停价格保护
    return true;

    var price = $('input[name=\'product_special[0][price]\']').val();
    if(price == undefined){
        return true;
    }else{
        if(special_price > price){
            alert('您添加的特价低于该商品的保护价，不能保存！');
            return false;
        }else{
            return true;
        }
    }
  }

  if(parseInt(special_row) > 0){
    $('#addSpecialButton').hide();
  }
  function addSpecial() {
    $('#addSpecialButton').hide();

    html  = '<tr id="special-row' + special_row + '">';
    html += '  <td class="text-left" style="display: none"><select name="product_special[' + special_row + '][customer_group_id]" class="form-control">';
  <?php foreach ($customer_groups as $customer_group) { ?>
      html += '      <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo addslashes($customer_group['name']); ?></option>';
    <?php } ?>
    html += '  </select></td>';
    html += '  <td class="text-right"><input id="product_special_showup" type="checkbox" name="product_special[' + special_row + '][showup]" value="1" class="form-control" /></td>';
    html += '  <td class="text-right"><input type="text" name="product_special[' + special_row + '][promo_title]" value="每天惠" placeholder="置顶标题" class="form-control" /></td>';
    html += '  <td class="text-right"><input type="number" name="product_special[' + special_row + '][maximum]" value="50" placeholder="限量，最大值50" class="form-control" /></td>';
    html += '  <td class="text-right" style="display: none"><input type="text" name="product_special[' + special_row + '][priority]" value="" placeholder="优先级" class="form-control" /></td>';
    html += '  <td class="text-right"><input type="text" name="product_special[' + special_row + '][price]" value="" placeholder="价格" class="form-control" /></td>';
    html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="text" name="product_special[' + special_row + '][date_start]" value="" placeholder="开始日期" data-date-format="YYYY-MM-DD" class="form-control" /><sp HH:mm:ssan class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
    html += '  <td class="text-left" style="width: 20%;"><div class="input-group date"><input type="text" name="product_special[' + special_row + '][date_end]" value="" placeholder="结束日期" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
    html += '  <td class="text-left"><button type="button" onclick="$(\'#special-row' + special_row + '\').remove(); $(\'#addSpecialButton\').show();" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
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