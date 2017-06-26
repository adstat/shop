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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>生鲜可售库存管理</h3>
      </div>
      <div class="panel-body">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">重置生鲜可售库存</a></li>
            <li><a href="#tab-special" data-toggle="tab">库存调整</a></li>
            <li><a href="#tab-list" data-toggle="tab" onclick="loadInventoryList();">库存列表</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="tab-content">
                  <form action="<?php echo $action_reset; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
                  <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-upload-xls">上传Excel文件 (<span style="color: #CC0000" title="仅限生鲜,快消品21:30自动设置可售库存。">仅限生鲜</span>)</label>
                      <div class="col-sm-10">
                        <input type="file" name="file" id="input-upload-xls" class="form-control">
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="col-sm-2 control-label">21:05-23:59期间可以上传</label>
                      <div class="col-sm-10">
                          <?php
                            $now = (int)date("Hi", time()+8*3600);
                            if($now >= 2105 && $now < 2359){
                          ?>
                            <button type="submit" class="btn btn-primary">上传</button>
                          <?php } ?>
                      </div>
                  </div>
                  </form>
              </div>
            </div>
            <div class="tab-pane" id="tab-special">
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
            url: 'index.php?route=catalog/inventory/inventoryListFresh&token=<?php echo $_SESSION['token']; ?>',
            dataType: 'json',
            data:{
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
                    html += '  <td class="text-center" ' + bgColor + '>' + n.station + ' - '+ n.warehouse + '</td>';
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
                    $('#'+rowId+' .rowStation').html(response.station + ' - ' + response.warehouse);
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
</script>
</div>
<?php echo $footer; ?> 