<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-activity" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-activity" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
            <!--<li><a href="#tab-products" data-toggle="tab"><?php echo $entry_relatedproduct; ?></a></li>-->
            <li><a href="#tab-links" data-toggle="tab">关联</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-data">
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-model"><?php echo $entry_name; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="act_name" value="<?php echo $act_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-model" class="form-control" />
                  <?php if ($error_model) { ?>
                  <div class="text-danger"><?php echo $error_model; ?></div>
                  <?php } ?>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-stations">平台</label>
                <div class="col-sm-10">
                    <select name="station_id" id="input-station_id" class="form-control" onchange="changeStation()">
                        <option value="0"> 全部 </option>
                        <?php if ( !empty($station_list) ){ foreach( $station_list as $value ){ ?>
                        <option value="<?php echo $value['station_id']; ?>" <?php if(!empty($station_id) && $station_id == $value['station_id'] ){ echo "selected"; } ?> ><?php echo $value['name']; ?></option>
                        <?php }} ?>
                    </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-stations">仓库</label>
                <div class="col-sm-10" id="insert-warehouse-list">

                </div>
              </div>


              <div class="form-group" style="display: none">
                <label class="col-sm-2 control-label" for="input-image"><?php echo $entry_image; ?>(未开放)</label>
                <div class="col-sm-10">
                  <a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="act_image" value="<?php echo $act_image; ?>" id="input-image" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-available"><?php echo $entry_starttime; ?></label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_start" value="<?php echo $date_start; ?>" placeholder="<?php echo $entry_starttime; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-date-available" class="form-control" />
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-date-available"><?php echo $entry_endtime; ?></label>
                <div class="col-sm-3">
                  <div class="input-group date">
                    <input type="text" name="date_end" value="<?php echo $date_end; ?>" placeholder="<?php echo $entry_endtime; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-date-available" class="form-control" />
                    <span class="input-group-btn">
                    <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                    </span></div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-2">
                  <select name="act_status" id="input-status" class="form-control">
                    <?php if ($act_status) { ?>
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
                    <label class="col-sm-2 control-label" for="input-model">排序</label>
                    <div class="col-sm-2">
                        <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" id="input-model" class="form-control" />
                    </div>
                </div>
            </div>
			
            <div class="tab-pane" id="tab-products">
              <div class="form-group">
                <div class="col-sm-12">
                  <input type="text" name="related" value="" placeholder="商品ID或模糊查询商品名" id="input-related" class="form-control" />

                    <style>
                        #product-related div{
                            margin: 3px;
                            padding: 5px;
                            font-size: 16px;
                            border: 1px dashed #6c6c6c;
                        }

                        #product-related div:hover{
                            background-color: #b2dba1;
                        }

                        .sort{float:right;}
                        .sortablelist{position:relative;}
                        .sortableitem{position:relative;}
                    </style>
                  <div id="product-related" class="well well-sm sortablelist">
                    <?php foreach ($relatedProducts as $product) { ?>
                    <div class="sortableitem" id="product-related<?php echo $product['product_id']; ?>"><i class="fa fa-minus-circle"></i>
                        <?php
                            $productInfo = "[商品编号:".$product['product_id']."] [仓库:".$product['station_name']."] [价格:".$product['price']."] ".$product['name'];
                            if($product['status'] == 0){
                                $productInfo = "<del>[下架]".$productInfo."</del>";
                            }

                            echo $productInfo;
                        ?>
                      <input type="hidden" name="product_related[]" value="<?php echo $product['product_id']; ?>" />
                      <span class="sort"><span class="fa fa-hand-o-up moveup"></span> <span class="fa fa-hand-o-down movedown"></span></span>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <!-- 活动关联商品新界面 -->
            <div class="tab-pane" id="tab-links">
              <div class="form-group">
                <div class="col-sm-12">
                  <input type="text" name="related" value="" placeholder="商品ID或模糊查询商品名" id="input-related" class="form-control" />
                </div>
              </div>
              <div class="table-responsive">
                <form action="<?php echo $action_adjust; ?>" method="post" enctype="multipart/form-data" id="form-product-adjust" class="form-horizontal">
                    <table  id="product_link" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <td class="center">商品ID</td>
                            <td class="center">商品名称</td>
                            <td class="center">商品平台</td>
                            <td class="center">商品价格</td>
                            <td class="center">商品销量属性</td>
                            <td class="center">商品状态</td>
                            <td class="center">排序</td>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- 关联商品 -->
                        <?php $link_row = 0; ?>
                        <?php foreach ($product_links as $product_link) { ?>
                        <tr id="link-row<?php echo $link_row; ?>">
                            <td class="text-center"><input type="text" name="product_link[<?php echo $link_row; ?>][product_id]" value="<?php echo $product_link['product_id']; ?>" placeholder="商品id" class="form-control" onChange="getProductInfo($(this).val(), $(this));" /></td>
                            <td class="text-left"><div class="rowName"><?php echo $product_link['name']; ?></div></td>
                            <td class="text-center"><div class="rowStation"><?php echo $product_link['station_name']; ?></div></td>
                            <td class="text-center"><div class="rowPrice"><?php echo $product_link['price']; ?></div></td>
                            <td class="text-center"><div class="rowClass"><?php echo $product_link['class']; ?></div></td>
                            <td class="text-center">
                                <?php if($product_link['status']){ ?>
                                <div class="rowStatus"><span style="background-color: #66CC66; color: #ffffff; padding: 3px;">启用</span></div>
                                <?php } else { ?>
                                <div class="rowStatus"><span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">停用</span></div>
                                <?php } ?>
                            </td>
                            <td class="text-center"><input type="text" name="product_link[<?php echo $link_row; ?>][sort_order]" value="<?php echo $product_link['sort_order']; ?>" placeholder="排序" class="form-control" /></td>
                            <td class="text-left"><button type="button" onclick="$('#link-row<?php echo $link_row; ?>').remove();" data-toggle="tooltip" title="移除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                        </tr>
                        <?php $link_row++; ?>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="7"></td>
                            <td class="text-left"><button type="button" onclick="addLinks();" data-toggle="tooltip" title="添加" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                        </tr>
                        </tfoot>
                    </table>
                </form>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
// Related
$('input[name=\'related\']').autocomplete({
	'source': function(request, response) {
        var request = request.replace(' ','');
        var limit = 10;
        var url = 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&limit='+ limit +'&filter_name=' +  encodeURIComponent(request);

        if(/^\d{4,6}$/.test(request)){
            var url = 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&limit='+ limit +'&filter_product_id=' +  encodeURIComponent(request);
        }

		$.ajax({
			url: url,
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
                    var label =  '[商品编号:' + item['product_id'] + '] [仓库:'+ item['station'] + '] [价格:'+ item['price']  + '] '+ item['name'];
                    if(parseInt(item['status']) == 0){
                        label = '[下架]'+ label;
                    }

					return {
						label: label,
						//label: '['+item['product_id']+']'+item['name'],
						value: item['product_id'],
                        status : parseInt(item['status'])
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'related\']').val('');
		
		$('#product-related' + item['value']).remove();
		
		$('#product-related').append('<div class="sortableitem" id="product-related' + item['value'] + '"><i class="fa fa-minus-circle"></i> <span class="labelInfo"></span><input type="hidden" name="product_related[]" value="' + item['value'] + '" /> <span class="sort"><span class="fa fa-hand-o-up moveup"></span> <span class="fa fa-hand-o-down movedown"></span></span></div>');
        $('#product-related'+item['value']+' .labelInfo').html( item['label'] );
        if(item['status'] == 0){
            $('#product-related'+item['value']+' .labelInfo').html( '<del>'+item['label']+'</del>' );
        }
    }
});

$('#product-related').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

    //更改排序，测试
    (function($){
        $.fn.clickSort = function(opts){
            var defaults = {
                speed:200
            }
            var option = $.extend(defaults,opts);
            this.each(function(){
                var _this = $(this);
                _this.on('click','.moveup',function(){
                    var parent = $(this).parents('.sortableitem');
                    var prevItem = parent.prev('.sortableitem');
                    if(prevItem.length==0)return;
                    var parentTop = parent.position().top;
                    var prevItemTop = prevItem.position().top;
                    parent.css('visibility','hidden');
                    prevItem.css('visibility','hidden');
                    parent.clone().insertAfter(parent).css({position:'absolute',visibility:'visible',top:parentTop}).animate({top:prevItemTop},option.speed,function(){
                        $(this).remove();
                        parent.insertBefore(prevItem).css('visibility','visible');
                        option.callback();
                    });
                    prevItem.clone().insertAfter(prevItem).css({position:'absolute',visibility:'visible',top:prevItemTop}).animate({top:parentTop},option.speed,function(){
                        $(this).remove();
                        prevItem.css('visibility','visible');
                    });
                });
                _this.on('click','.movedown',function(){
                    var parent = $(this).parents('.sortableitem');
                    var nextItem = parent.next('.sortableitem');
                    if(nextItem.length==0)return;
                    var parentTop = parent.position().top;
                    var nextItemTop = nextItem.position().top;
                    parent.css('visibility','hidden');
                    nextItem.css('visibility','hidden');
                    parent.clone().insertAfter(parent).css({position:'absolute',visibility:'visible',top:parentTop}).animate({top:nextItemTop},option.speed,function(){
                        $(this).remove();
                        parent.insertAfter(nextItem).css('visibility','visible');
                        option.callback();
                    });
                    nextItem.clone().insertAfter(nextItem).css({position:'absolute',visibility:'visible',top:nextItemTop}).animate({top:parentTop},option.speed,function(){
                        $(this).remove();
                        nextItem.css('visibility','visible');
                    });
                });

            });
        }
    })(jQuery)
    $('.sortablelist').clickSort();

    //关联商品动态表格
    var link_row = <?php echo $link_row; ?>;

    function addLinks() {
        html  = '<tr id="link-row' + link_row + '">';
        html += '<td class="text-center"><input type="text" name="product_link[' + link_row + '][product_id]" value="" placeholder="商品id" class="form-control" onChange="getProductInfo($(this).val(), $(this));" /></td>';
        html += '<td class="text-left"><div class="rowName"></div></td>';
        html += '<td class="text-center"><div class="rowStation"></div></td>';
        html += '<td class="text-center"><div class="rowPrice"></div></td>';
        html += '<td class=text-center><div class="rowClass"></div></td>';
        html += '<td class=text-center><div class="rowStatus"></div></td>';
        html += '<td class=text-center><input type="text" name="product_link[' + link_row + '][sort_order]" value="'+ parseInt(link_row+1) +'" placeholder="排序" class="form-control" /></td>';
        html += '<td class="text-left"><button type="button" onclick="$(\'#link-row' + link_row + '\').remove();" data-toggle="tooltip" title="移除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';

        $('#product_link tbody').append(html);

        link_row++;
    }

    function getProductInfo(product_id,obj) {
        var rowId = obj.parent().parent().attr('id');
        var product_id = parseInt(product_id);

        $.ajax({
            type:'GET',
            async: false,
            cache: false,
            url: 'index.php?route=catalog/activity/getProductInfo&token=<?php echo $_SESSION['token']; ?>',
            data: {
                product_id: product_id
            },
            dataType: 'json',
            success:function(response){
                console.log(response);
                if(parseInt(response.station_id) > 0){
                    $('#'+rowId+' .rowName').html(response.name);
                    $('#'+rowId+' .rowStation').html(response.station_name);
                    $('#'+rowId+' .rowPrice').html(response.price);
                    $('#'+rowId+' .rowClass').html(response.class);
                    if(parseInt(response.status) == 1){
                        $('#'+rowId+' .rowStatus').html('<span style="background-color: #66CC66; color: #ffffff; padding: 3px;">启用</span>');
                    }else{
                        $('#'+rowId+' .rowStatus').html('<span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">停用</span>');

                    }
                }else{
                    $('#'+rowId+' .rowName').html('');
                    $('#'+rowId+' .rowStation').html('');
                    $('#'+rowId+' .rowPrice').html('');
                    $('#'+rowId+' .rowClass').html('');
                }
            }
        });
    }
    //table表中tr行的移动
    function moveTr(t,oper){
        var data_tr=$(t).parent().parent(); //获取到触发的tr
        if(oper=="MoveUp"){    //向上移动
            if($(data_tr).prev().html()==null){ //获取tr的前一个相同等级的元素是否为空
                alert("已经是最顶部了!");
                return;
            }{
                $(data_tr).insertBefore($(data_tr).prev()); //将本身插入到目标tr的前面
            }
        }else{
            if($(data_tr).next().html()==null){
                alert("已经是最低部了!");
                return;
            }{
                $(data_tr).insertAfter($(data_tr).next()); //将本身插入到目标tr的后面
            }
        }
    }

    changeStation();

    function changeStation(){
        var station_id      = $('#input-station_id').val();

        var warehouse_list = JSON.parse('<?php echo json_encode($warehouse_list); ?>');
        var warehouse_ids  = JSON.parse('<?php echo json_encode($warehouse_ids); ?>');
        var html = "";
        $.each(warehouse_list, function(index, item){
            if(station_id == 0 || item.station_id == station_id){
                html += '<div class="checkbox">';
                html +=     '<label>';

                if(warehouse_ids.length > 0){
                    var checked = "";
                    if($.inArray(item.warehouse_id, warehouse_ids) >= 0){
                        checked = "checked";
                    }
                    html += '<input type="checkbox" name="warehouse_ids[]" value="'+ item.warehouse_id +'" '+ checked +' > ';
                }
                else
                {
                    html += '<input type="checkbox" name="warehouse_ids[]" value="'+ item.warehouse_id +'" checked > ';
                }

                if (station_id == 0){ html += ' [ '+ item.name +' ]  '; }

                html +=         ' <b>'+ item.title +'</b>';
                html +=     '</label>';
                html += '</div>';
            }
            if(station_id == 0){
                html = "";
            }
        });

        $('#insert-warehouse-list').html( html );
    }

//--></script>
 <script type="text/javascript"><!--
$('.date').datetimepicker({
});
//--></script>
<?php echo $footer; ?> 