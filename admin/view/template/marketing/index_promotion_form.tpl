<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
          <button type="button" form="form-activity" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary" onclick="submitForm();"><i class="fa fa-save"></i></button>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> 编辑</h3>
      </div>
      <div class="panel-body">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-data" data-toggle="tab">添加促销商品</a></li>
            <li><a href="#tab-search" data-toggle="tab">查询促销商品</a></li>
            <li><a href="#tab-priority" data-toggle="tab">更改促销商品排序</a></li>
          </ul>
          <div class="tab-content">



            <div class="tab-pane active" id="tab-data">
              <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
                <strong>橙色背景的商品促销价设置过低，将导致无法保存本次促销设置</strong>
              </div>
              <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-activity" class="form-horizontal">
                <div class="well">
                  <div class="row">
                      <div class="col-sm-2" <?php if($filter_warehouse_id_global) { ?> style="display:none" <?php } ?>>
                          <select name="set-station" id="search-station" class="form-control">
                              <?php foreach ($stations as $val) { ?>
                              <option value="<?php echo $val['station_id']; ?>" <?php if($val['station_id']==2){ echo "selected='selected'"; } ?>><?php echo $val['name']; ?></option>
                              <?php } ?>
                          </select>
                      </div>

                      <div class="col-sm-4" <?php if($filter_warehouse_id_global) { ?> style="display:none" <?php } ?>>
                      <button type="button" class="btn btn-primary">本促销将会应用到该平台下所有仓库</button>
                      </div>

                      <div class="col-sm-4" <?php if(!$filter_warehouse_id_global) { ?> style="display:none" <?php } ?>>
                          <div class="form-group">
                              <label class="control-label" for="search-area">选择区域</label>
                              <select name="set-area[]" id="search-area" multiple="multiple" style="width:50%" >
                                  <?php foreach($areas as $area){ ?>
                                  <option value="<?php echo $area['area_id']?>"><?php echo $area['title'] ; ?></option>
                                  <?php } ?>
                              </select>
                          </div>
                      </div>

                  </div>
                </div>

                <div class="table-responsive">
                  <table id="products" class="table table-bordered table-hover">
                      <thead>
                      <tr>
                          <td style="width: 75px">商品ID</td>
                          <td>商品名称</td>
                          <td style="width: 55px">可售</td>
                          <!--<td>会员组</td>-->
                          <td style="width: 65px">排序</td>
                          <td style="width: 65px">价格</td>
                          <td style="width: 90px">促销价</td>
                          <td style="width: 65px">限量</td>
                          <td style="width: 80px; display: none">置顶</td>
                          <td style="width: 140px">促销标题</td>
                          <td style="width: 145px">开始日期</td>
                          <td style="width: 145px">结束日期</td>
                          <td style="width: 65px">操作</td>
                      </tr>
                      </thead>
                      <tbody>
                      </tbody>
                      <tfoot>
                      <tr>
                          <td colspan="10"></td>
                          <td class="text-left"><button type="button" onclick="addLinks();" data-toggle="tooltip" title="添加" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                      </tr>
                      </tfoot>
                  </table>
                </div>
              </form>
            </div>




            <div class="tab-pane" id="tab-search">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group date">
                                <input type="text" name="end_date" id = "search_date" value="<?php echo $valid_date; ?>" placeholder="日期有效" data-date-format="YYYY-MM-DD" class="form-control" />
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="product_id_q" id="search-produt-id" placeholder="查询商品ID" class="form-control" />
                        </div>
                        <div class="col-sm-2">
                            <select name="station" id="search-station" class="form-control">
                                <?php foreach ($stations as $val) { ?>
                                <option value="<?php echo $val['station_id']; ?>" <?php if($val['station_id']==2){ echo "selected='selected'"; } ?>><?php echo $val['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" onclick="showhistory();">查询</button>
                        </div>
                    </div>
                </div>
                <div id="special-history"></div>
            </div>

            <div class="tab-pane" id="tab-priority">
                <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i>
                    查询指定日期有效的促销商品，整体列表按<strong style="color: #de7c23">商品排序数值正序</strong>。必须选择一个仓库，才可以进行排序更新选择。
                </div>
                <div class="well" <?php if(!$filter_warehouse_id_global) { ?> style="display:none" <?php } ?>>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group date">
                                <input type="text" name="end_date" id = "sort_date" value="<?php echo $valid_date; ?>" placeholder="日期有效" data-date-format="YYYY-MM-DD" class="form-control" />
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-primary" onclick="showSort();">查询促销</button>
                        </div>
                        <div class="col-sm-8" id = "reset-sort" style="display:none">
                            <button type="button" class="btn btn-primary pull-right" onclick="updateSort();">更新排序</button>
                        </div>
                    </div>
                </div>
                <div id="sort-history"></div>
            </div>

          </div>
      </div>
    </div>
  </div>
<script type="text/javascript">
    var link_row = 0;
    var global = {
        "price_limit" : {},
        "price_compare": {},
        "errors":{},
    };
    function getProductsList() {
        var date_start = $('#start_date').val();
        var date_end = $('#end_date').val();
        var product_id = $('#produt_id_q').val();
        var station_id = $('#station').val();
        $.ajax({
            type : 'POST',
            url: 'index.php?route=marketing/index_promotion/getProductsByCondition&token=<?php echo $_SESSION['token']; ?>',
            data : {
//                start : date_start,
                end : date_end,
                product_id : product_id,
                station_id : station_id,
            },
            dataType: 'json',
            success : function(data){
                if(data.length<1){
                    alert('查无信息');
                }
                $('#products tbody').html('');
                var html = '';
                $.each(data, function(i, n){
                    html += '<tr id="link-row'+i+'">';
                    html += '<td class="text-center" style="width: 7%">';
                    html += '<input type="text" name="product['+i+'][product_id]" value="'+ n.product_id+'" placeholder="商品ID" class="form-control" />';
                    html += '<input type="hidden" name="product['+i+'][product_special_id]" value="'+ n.product_special_id+'" placeholder="商品id" class="form-control" />';
                    html += '</td>';
                    html += '<td>'+'<div class="name">'+n.name+'</div>'+'</td>';
                    html += '<td>'+'<div class="stock">'+n.stock+'</div>'+'</td>';
//                    html += '<td class="text-center" style="width: 10%">';
//                    html += '<select name="product['+i+'][customer_group_id]" class="form-control">';
//                    html += '<?php foreach($customer_group as $group) { ?>';
//                    html += '<option value="<?php echo $group['customer_group_id']; ?>" <?php if($group['customer_group_id']=='+n.customer_group_id+') { ?> selected="selected" <?php } ?>><?php echo $group['name']; ?></option>';
//                    html += '<?php } ?>';
//                    html += '</select>';
//                    html += '</td>';

                    html += '<td class="text-center"><input type="text" name="product['+i+'][priority]" value="'+ n.priority+'" placeholder="排序" class="form-control" /></td>';
                    html += '<td>'+'<div class="ori_price">'+n.ori_price+'</div>'+'</td>';
                    html += '<td class="text-center"><input type="text" name="product['+i+'][price]" value="'+ n.price+'" placeholder="价格" class="form-control" /></td>';
                    html += '<td class="text-center"><input type="text" name="product['+i+'][maximum]" value="'+ n.maximum+'" placeholder="限量" class="form-control" /></td>';

//                    html += '<td class="text-center" style="display: none">';
//                    html += '<select  name="product['+i+'][showup]" class="form-control">';
//                    if(parseInt(n.showup)==1){
//                        html += '<option value="1" selected="selected">是</option>';
//                        html += '<option value="0">否</option>';
//                    }else{
//                        html += '<option value="1">是</option>';
//                        html += '<option value="0" selected="selected">否</option>';
//                    }
//                    html += '</select>';
//                    html += '</td>';

                    html += '<td class="text-center"><input type="text" name="product['+i+'][promo_title]" value="'+ n.promo_title+'" placeholder="标题" class="form-control" /></td>';

                    html += '<td class="text-center">';
                    html += '<div class="input-group date">';
                    html += '<input type="text" name="product['+i+'][date_start]" value="'+ n.date_start+'" placeholder="开始日期" data-date-format="YYYY-MM-DD" class="form-control" />';
                    html += '<span class="input-group-btn">';
                    html += '<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>';
                    html += '</span>';
                    html += '</div>';
                    html += '</td>';

                    html += '<td class="text-center">';
                    html += '<div class="input-group date">';
                    html += '<input type="text" name="product['+i+'][date_end]" value="'+ n.date_end+'" placeholder="开始日期" data-date-format="YYYY-MM-DD" class="form-control" />';
                    html += '<span class="input-group-btn">';
                    html += '<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>';
                    html += '</span>';
                    html += '</div>';
                    html += '</td>';

                    html += '<td class="text-left"><button type="button" value="'+ n.product_special_id+'" onclick="remove_product(this);$(\'#link-row' + i + '\').remove();" data-toggle="tooltip" title="移除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';

                    link_row ++;
                });
                $('#products tbody').html(html);

                $('.date').datetimepicker({
                    pickTime: false
                });
            }
        });
    }

    function addLinks() {
        html  = '<tr id="link-row' + link_row + '">';
        html += '<td class="text-center"><input type="text"  name="product[' + link_row + '][product_id]" value="" onchange="getProductInfo($(this).val(),$(this));" placeholder="商品ID" class="form-control" /></td>';
        html += '<td class="text-center"><div class="name"></div></td>';
        html += '<td class="text-center"><div class="stock"></div></td>';
//        html += '<td class="text-center"><select name="product[' + link_row + '][customer_group_id]" class="form-control"><?php foreach($customer_group as $group) { ?><option value="<?php echo $group['customer_group_id']; ?>"><?php echo $group['name']; ?></option><?php } ?></select></td>';
        html += '<td class="text-center"><input type="text" name="product[' + link_row + '][priority]" value="" placeholder="排序" class="form-control" /></td>';
        html += '<td class="text-center"><div class="ori_price"></div></td>';
        html += '<td class="text-center"><input type="text" name="product[' + link_row + '][price]" value="" onchange="comparePrice($(this).val(),$(this));" placeholder="价格" class="form-control" /></td>';
        html += '<td class="text-center"><input type="text" name="product[' + link_row + '][maximum]" value="" placeholder="限量" class="form-control" /></td>';
//        html += '<td class="text-center" style="display: none"><select name="product[' + link_row + '][showup]" class="form-control"><option value="0">否</option><option value="1">是</option></select></td>';
        html += '<td class="text-center"><input type="text" name="product[' + link_row + '][promo_title]" value="" placeholder="标题" class="form-control" /></td>';
        html += '  <td class="text-left"><div class="input-group date"><input type="text" name="product[' + link_row + '][date_start]" value="" placeholder="开始日期" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
        html += '  <td class="text-left"><div class="input-group date"><input type="text" name="product[' + link_row + '][date_end]" value="" placeholder="结束日期" data-date-format="YYYY-MM-DD" class="form-control" /><span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div></td>';
        html += '<td class="text-left"><button type="button" onclick="$(\'#link-row' + link_row + '\').remove();" data-toggle="tooltip" title="移除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';

        $('#products tbody').append(html);

        $('.date').datetimepicker({
            pickTime: false
        });

        link_row++;
    }
    addLinks();

    function getProductInfo(product_id,obj){
        var product_id = parseInt(product_id);
        var rowId = obj.parent().parent().attr('id');
        console.log(rowId);
        $.ajax({
            type: 'GET',
            url: 'index.php?route=marketing/index_promotion/getProductInfo&token=<?php echo $_SESSION["token"]; ?>&warehouse_id=<?php echo $filter_warehouse_id_global; ?>&product_id='+product_id,
            dataType: 'json',
            success : function(data){
                if(!data[0]['name']){
                    alert('没有该商品！');
                }else{
                    $('#'+rowId+' .name').html(data[0]['name']);
                    $('#'+rowId+' .stock').html(data[0]['stock']);
                    $('#'+rowId+' .ori_price').html(data[0]['ori_price']);
                    global.price_limit[rowId] = data[0]['limit_price'];
                    global.price_compare[rowId] = data[0]['compare_price'];
                }
            }
        });
    }

    function comparePrice(price,obj){
        var set_price = parseFloat(price);
        var rowId = obj.parent().parent().attr('id');
        var row = parseInt(rowId.replace(/[^0-9]/ig,""));
        var numbers = [];
        numbers[0] = parseFloat(global.price_compare[rowId]);
        numbers[1] = parseFloat(global.price_limit[rowId]);

        var thisRow = 'product['+row+']';
        var thisRowProductId = $('#link-row'+row).find('input[name="'+thisRow+'[product_id]"]').val();
        var thisRowProductName = $('#link-row'+row+' .name').html();

        $.each(global.errors,function(i,v){
            if(i==row){
                delete global.errors[row];
            }
        });
        $('#link-row'+row).removeAttr("style");
        if(parseFloat(Math.min.apply(Math, numbers)) > price){
            global.errors[row] = 1;
            $('#link-row'+row).css({'background-color':'#FDA917'});
//            alert("["+thisRowProductId+"]"+thisRowProductName+"采购价过低");
        }
    }

    function remove_product(obj){
        var id = obj.value;
        if(confirm('确认删除该商品？')){
            $.ajax({
                type: 'GET',
                url: 'index.php?route=marketing/index_promotion/deleteProduct&token=<?php echo $_SESSION["token"]; ?>&product_special_id='+id,
                dataType: 'json',
                success : function(data){
                    if(data){
                        alert('删除成功!');
                    }
                }
            });
        }else{
            return;
        }

    }

    function submitForm(){
        var error_num = 0;
        console.log(global.errors);
        $.each(global.errors,function(i,v){
            if(v>0){
                error_num ++;
            }
        });
        //确定后台传的flag为true,否则不予提交
        if(!error_num){
            if(window.confirm('确认提交当前促销？')){
                $('#form-activity').submit();
            }
        }else{
            alert('当前促销中，'+error_num+'条促销价过低!');
        }

    }
</script>
<script type="text/javascript">
    function showhistory(){
        var search_date = $('#search_date').val();
        var product_id = $('#search-produt-id').val();
        var station_id = $('#search-station').val();
        var filter_warehouse_id_global = <?php echo $filter_warehouse_id_global; ?>;
        var data = {
            search_date : search_date,
            product_id : product_id,
            station_id : station_id,
            filter_warehouse_id_global:filter_warehouse_id_global,
        };
        $('#special-history').load('index.php?route=marketing/index_promotion/history&token=<?php echo $token; ?>',{data:data});
    }

    function showSort(){
        var sort_date = $('#sort_date').val();
        var filter_warehouse_id_global = <?php echo $filter_warehouse_id_global; ?>;
        var data = {
            sort_date : sort_date,
            filter_warehouse_id_global:filter_warehouse_id_global,
        };
        $('#sort-history').load('index.php?route=marketing/index_promotion/getSort&token=<?php echo $token; ?>',{data:data});
    }

</script>
<script type="text/javascript">
    $('.date').datetimepicker({
        pickTime: false
    });
</script>
</div>
<link href="view/javascript/multi-select/multiple-select.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/multi-select/multiple-select.js"></script>
<script type="text/javascript">
    $('#search-area').multipleSelect({
        filter: true
    });
</script>
<?php echo $footer; ?>