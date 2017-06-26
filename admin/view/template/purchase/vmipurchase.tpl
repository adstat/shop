<?php echo $header; ?><?php echo $column_left; ?>
<!-- 页面 -->
<div id="content">
  <div class="page-header">
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>库存数据导入</h3>

      <div class="panel-body">
          <div class="alert alert-warning">上传指定日期的库存数据将覆盖现有日期全部数据，仅最后一次上传数据有效</div>
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">上传数据</a></li>
            <li><a href="#tab-list" data-toggle="tab" onclick="loadPurchaseList();">库存数据上传列表</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="tab-content">
                  <form action="<?php echo $action_upload; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
                      <div class="form-group">

                          <div class="alert alert-info">
                              原数据列"到货件数"已改为<strong style="color: #CC0000">“蔬果毛重价”</strong>，蔬果类需要填写毛重价。
                              <br />EXCEL数据要求7列，表头格式为：[原料ID], [原料名称],<strong>[原库存的保存程度]</strong>,[采购前的库存量],[采购后的库存量],[总库存量],[库存记录员]
                          </div>

                          <div class="col-sm-2">
                              <label class="control-label" style="font-size: 110%; ">库存记录日期</label>
                              <div class="input-group date">
                                  <input type="text" name="purchase_date" value="<?php echo $purchase_date; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input_purchase_date" class="form-control" />
                                  <span class="input-group-btn">
                                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                  </span>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="form-group">
                                <label class="control-label">上传Excel文件</label>
                                <input type="file" name="file" id="input-upload-xls" class="form-control" />
                              </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="control-label">23:00-凌晨03:59期间不能上传</label>
                          <div class="col-sm-2">
                              <?php $now = date("H:i", time()+8*3600); if( ($now >= '04:00' || $now <'23:00')){ ?>
                              <button type="submit" class="btn btn-primary">上传</button>
                              <?php } ?>
                          </div>
                      </div>
                  </form>
              </div>
            </div>
            <div class="tab-pane" id="tab-list">
                <div class="table-responsive">
                    <table id="purchase-list" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <td class="text-center">库存单编号</td>
                            <td class="text-left">库存记录日期</td>
                            <td class="text-left">最后上传日期</td>
                            <td class="text-left">上传次数</td>
                            <td class="text-center">上传用户</td>
                            <td class="text-center">查看明细</td>
                        </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="6">正在查询...</td></tr>
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
        html += '  <td class="text-center"><input type="text" name="products[' + adjust_row + '][product_id]" value="" placeholder="商品ID" class="form-control int" /></td>';
        html += '  <td class="text-left"><input type="text" name="products[' + adjust_row + '][quantity]" value="" placeholder="数量调整,可以为负,在原库存上增加或减少" class="form-control int" /></td>';
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

        if(window.confirm('确认导入库存数据么？')){
            $('#form-product-adjust').submit();
        }
    }

    function loadPurchaseList(){
        $.ajax({
            type: 'GET',
            url: 'index.php?route=purchase/vmipurchase/vmipurchaseList&token=<?php echo $_SESSION['token']; ?>',
            dataType: 'json',
            success: function(data){
                console.log('load='+data);

                $('#purchase-list tbody').html('');
                var html = '';
                $.each(data, function(i, n){
                    html += '<tr>';
                    html += '  <td class="text-center">' + n.purchase_order_id + '</td>';
                    html += '  <td class="text-center">' + n.purchase_date + '</td>';
                    html += '  <td class="text-center">' + n.adate + '</td>';
                    html += '  <td class="text-center">' + n.upload_times + '</td>';
                    html += '  <td class="text-center">' + n.add_user_name + '</td>';
                    html += '  <td class="text-center"><button onclick="loadPurchaseDetail(' + n.purchase_order_id + ');">查看</button></td>';
                    html += '</tr>';

                    html += '<tr style="display: none; background-color: #fffadf" class="purchase_order_detail" id="purchase_order_id_'+ n.purchase_order_id +'"><td  colspan="6" ></td></tr>';
                });
                $('#purchase-list tbody').html(html);
            },
            error: function(){
                //console.log(this);
            }
        });
    }

    function loadPurchaseDetail(purchase_order_id){
        $.ajax({
            type: 'GET',
            url: 'index.php?route=purchase/vmipurchase/getVmiPurchaseDetail&token=<?php echo $_SESSION["token"]; ?>&purchase_order_id='+purchase_order_id,
            dataType: 'json',
            success: function(data){
                console.log(data);

                var purchaseOrderIdCotainer = '#purchase_order_id_'+purchase_order_id;
                $('.purchase_order_detail').hide();
                var html = '<div class="table-responsive">';
                html += '<div style="text-align: right">';
                html += '   <button style="color:#df8505" onclick="$(\'.purchase_order_detail\').hide();">关闭</button>';
                html += '</div>';
                html += '<table id="purchase-detail-list" class="table table-striped table-bordered table-hover">';
                html += '<caption style="font-size: 16px;  text-align:center; font-weight: bold;">库存明细单#'+purchase_order_id+'</caption>';
                html += '   <thead>';
                html += '       <tr>';
                html += '           <td>原料编号</td>';
                html += '           <td>原料名称</td>';
                html += '           <td>原库存的保存程度</td>';
                html += '           <td>采购前的库存量(斤|份)</td>';
                html += '           <td>采购后的库存量(斤|份)</td>';
                html += '           <td>总计的库存量</td>';
                html += '           <td>库存记录员</td>';
                html += '       </tr>';
                html += '   </thead>';

                $.each(data, function(i, n){
                    html += '<tr>';
                    html += '  <td class="text-center">' + n.sku_id + '</td>';
                    html += '  <td class="text-center">' + n.sku_name + '</td>';
                    html += '  <td class="text-center">' + n.purchase_price_500g_gross + '</td>';
                    html += '  <td class="text-center">' + n.purchase_price_500g + '</td>';
                    html += '  <td class="text-center">' + n.purchase_qty_500g + '</td>';
                    html += '  <td class="text-center">' + n.purchase_total + '</td>';
                    html += '  <td class="text-center">' + n.buyer + '</td>';
                    html += '</tr>';
                });
                html += '</table>';
                html += "  <div style='text-align: right; font-size: 12px; font-weight: bold; margin: 0 0 30px 0;'>以上库存明细单#" + purchase_order_id;
                html += '   <button style="color:#df8505" onclick="$(\'.purchase_order_detail\').hide();">关闭</button>';
                html += "  </div>";
                html += "</div>";

                $(purchaseOrderIdCotainer+' td').html(html);
                $(purchaseOrderIdCotainer).show();
            },
            error: function(){
                //console.log(this);
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