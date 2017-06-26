<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="#<?php //echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary" onclick="alert('系统商品ID超4位，仓库系统需要调整，暂停新建，请联系技术后台导入。'); return false"><i class="fa fa-plus"></i></a>

        <div style="display: none;">
            <button type="submit" form="form-product" formaction="<?php echo $copy; ?>" data-toggle="tooltip" title="<?php echo $button_copy; ?>" class="btn btn-default"><i class="fa fa-copy"></i></button>
            <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
            <span style="font-size:1.8em;color: red; display: none;">散菜：</span><a href="<?php echo $add_ws; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            <br>
            <span style="color:red; display: none;">复制商品后需重新编辑原料!</span>
        </div>
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
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>

              <div class="form-group">
                <label class="control-label" for="input-station-id">平台</label>
                <select name="filter_station_id" id="input-station-id" class="form-control" <?php if($station_set){ ?>disabled="disabled"<?php } ?>>
                    <option value="*">全部</option>
                    <?php foreach($stations as $station){ ?>
                    <?php if($station_set){ ?>
                    <option value="<?php echo $station['station_id']; ?>" <?php if($station['station_id']==$station_set){ ?>selected="selected"<?php } ?>><?php echo $station['name']; ?></option>
                    <?php }else{ ?>
                    <option value="<?php echo $station['station_id']; ?>" <?php if($station['station_id']==$filter_station_id){ ?>selected="selected"<?php } ?>><?php echo $station['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label class="control-label" for="input-model">商品ID</label>
                    <input type="text" name="filter_model" value="<?php echo $filter_model; ?>" placeholder="商品ID" id="input-model" class="form-control" />
                </div>
                
                <!--
              <div class="form-group">
                <label class="control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
                <input type="text" name="filter_quantity" value="<?php echo $filter_quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control" />
              </div>
                -->

                <div class="form-group">
                <label class="control-label" for="input-product-category">商品分类</label>
                <select name="filter_product_category" id="input-product-category" class="form-control">
                  <option value="*"></option>
                  <?php foreach($categories as $ck=>$cv){ ?>
                    <?php if($cv['parent_id'] == 0){ ?>
                        <option value="*" disabled=""><?php echo $cv['name'];?></option>
                    <?php }else{ ?>
                        <option value="<?php echo $cv['category_id'];?>" <?php if($filter_product_category == $cv['category_id']){ echo 'selected="selected"'; } ?> ><?php echo $cv['name'];?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
            </div>
                
                
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-sku">商品编码</label>
                <input type="text" name="filter_sku" value="<?php echo $filter_sku; ?>" placeholder="商品编码" id="input-sku" class="form-control" />
              </div>
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


                
                <div style="display: none" class="form-group">
                <label class="control-label" for="input-product-type">商品类型</label>
                <select name="filter_product_type" id="input-product-type" class="form-control">
                  <option value="*"></option>
                  <option value="1" <?php if($filter_product_type == 1){ echo 'selected="selected"'; } ?> >生鲜(包装菜)</option>
                  <option value="2" <?php if($filter_product_type == 2){ echo 'selected="selected"'; } ?> >非生鲜</option>
                  <option value="3" <?php if($filter_product_type == 3){ echo 'selected="selected"'; } ?> >散菜</option>  
                </select>
              </div>
                
                
                
                
                
            </div>

            <div class="col-sm-3">
                <div class="form-group">
                    <label class="control-label" for="input-product-type-id">商品属性分类</label>
                    <select name="filter_product_type_id" id="input-product-type-id" class="form-control">
                        <option value="*">全部</option>
                        <?php foreach($all_product_type_id as $type) { ?>
                        <?php if($type['product_type_id'] == $filter_product_type_id){ ?>
                        <option value="<?php echo $type['product_type_id']; ?>" selected = "selected"><?php echo $type['name']; ?></option>
                        <?php }else{ ?>
                        <option value="<?php echo $type['product_type_id']; ?>"><?php echo $type['name']; ?></option>
                        <?php } ?>
                        <?php } ?>

                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label" for="input-page-id">分页</label>
                    <select name="filter_page_id" id="input-page-id" class="form-control">
                        <option value="*">全部</option>
                        <option value="20" <?php if($filter_page_id == 20 ){ ?> selected="selected" <?php } ?> >20条/页</option>
                        <option value="50" <?php if($filter_page_id == 100 ){ ?> selected="selected" <?php } ?> >50条/页</option>
                        <option value="100" <?php if($filter_page_id == 100 ){ ?> selected="selected" <?php } ?> >100条/页</option>
                    </select>
                </div>

                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-center"><?php echo $column_image; ?></td>
                  <td class="text-left"><?php if ($sort == 'p.model') { ?>
                    <a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>">商品ID</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_model; ?>">商品ID</a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'pd.name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'p.price') { ?>
                    <a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
                    <?php } ?></td>
                  <!--<td class="text-right">负库存</td>-->
                  <td>限购</td>
                  <td>分类</td>
                  <td>规格</td>
                  <td>按重</td>
                  <td>散件</td>
                  <td>赠品</td>
                  <td>外仓</td>
                    <td class="text-left"><?php if ($sort == 'p.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($product['product_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-center"><?php if ($product['image']) { ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail" />
                    <?php } else { ?>
                    <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
                    <?php } ?></td>

                  <td class="text-left">
                      <?php echo $product['product_id']; ?>
                      <!--<br / >商品条码：<?php echo $product['sku']; ?>-->
                  </td>
                  <td class="text-left">
                    <strong style="font-size: 110%"><?php echo $product['name']; ?></strong>
                    <!--<br /><?php echo "建议售价:".$product['retail_price']; ?>
                    <br /><?php echo "最佳食用期: ".$product['shelf_life']."天"; ?>
                    <?php
                    if($product['date_new_on'] !== '0000-00-00' && $product['date_new_off'] !== '0000-00-00'){
                        echo "<br />";
                    echo "新品上线: ".$product['date_new_on']."~".$product['date_new_off'];
                    }
                    ?>
                    <?php
                    if($product['abstract'] !== ''){
                        echo "<br />";
                    echo "箱规: ".$product['abstract'];
                    }
                    ?>-->
                      <br /><?php echo "[分拣条码]:"."<br />".$product['sku_barcode']; ?>
                      <br /><?php echo $product['warehouse_sale']; ?>
                  </td>
                  <td class="text-left">
                  <?php if ($product['special']) { ?>
                    <div>
                        <span style="text-decoration: line-through;"><?php echo $product['price']; ?></span>
                        <span class="text-danger"><?php echo $product['special']; ?></span>
                    </div>
                    <?php echo $product['showup']?"[置顶]":"" ?>
                    <?php echo $product['promo_maximum']?"[限购:".$product['promo_maximum']."件]" : "" ?>
                    <?php echo $product['promo_title']?"<br />[标签:".$product['promo_title']."]":"" ?>
                    <?php } else { ?>
                    <?php echo $product['price']; ?>
                  <?php } ?></td>
              <!--<td class="text-right"><?php echo $product['safestock']; ?></td>-->
                  <td><?php echo $product['limit_num'] ; ?></td>
                  <td><?php echo $product['category']; ?></td>
                  <td><?php echo $product['w_size']; ?></td>
                  <td><?php echo $product['weight_inv_flag'] ? '是': ''; ?></td>
                  <td><?php echo $product['repack'] ? '是': ''; ?></td>
                  <td><?php echo $product['is_gift'] ? '是': ''; ?></td>
                  <td><?php echo $product['instock'] ? '是': ''; ?></td>
                  <td class="text-left"<?php if($product['status_id']==0){ ?> style=" border: 1px dashed; background-color: #FF2222;" <?php } ?> ><?php echo $product['status']; ?></td>
                  <td class="text-right"><a href="<?php echo $product['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
	var url = 'index.php?route=catalog/product&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

    var filter_sku = $('input[name=\'filter_sku\']').val();

    if (filter_sku) {
        url += '&filter_sku=' + encodeURIComponent(filter_sku);
    }

	var filter_model = $('input[name=\'filter_model\']').val();

	if (filter_model) {
		url += '&filter_model=' + encodeURIComponent(filter_model);
	}

	var filter_price = $('input[name=\'filter_price\']').val();

	if (filter_price) {
		url += '&filter_price=' + encodeURIComponent(filter_price);
	}

	var filter_quantity = $('input[name=\'filter_quantity\']').val();

	if (filter_quantity) {
		url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
	}

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

    var filter_product_type = $('select[name=\'filter_product_type\']').val();

	if (filter_product_type != '*') {
		url += '&filter_product_type=' + encodeURIComponent(filter_product_type);
	}
        
        
    var filter_product_category = $('select[name=\'filter_product_category\']').val();

	if (filter_product_category != '*') {
		url += '&filter_product_category=' + encodeURIComponent(filter_product_category);
	}

    var filter_station_id = $('select[name=\'filter_station_id\']').val();

    if (filter_station_id != '*') {
        url += '&filter_station_id=' + encodeURIComponent(filter_station_id);
    }

    var filter_product_type_id = $('select[name=\'filter_product_type_id\']').val();

    if (filter_product_type_id != '*') {
        url += '&filter_product_type_id=' + encodeURIComponent(filter_product_type_id);
    }

    var filter_page_id = $('select[name=\'filter_page_id\']').val();

    if (filter_page_id != '*') {
        url += '&filter_page_id=' + encodeURIComponent(filter_page_id);
    }

	location = url;
});
//--></script> 
  <script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=%' +  encodeURIComponent(request) + '%',
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
		$('input[name=\'filter_name\']').val(item['label']);
	}
});

$('input[name=\'filter_model33\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['model'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_model\']').val(item['label']);
	}
});
//--></script></div>
<?php echo $footer; ?>