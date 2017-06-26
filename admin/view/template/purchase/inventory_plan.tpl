<?php echo $header; ?><?php echo $column_left; ?>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>数据导入</h3>
      </div>
      <div class="panel-body">
          <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> 上传明天及以后的可售库存计划，仅最后一次上传数据，有效请导入EXCEL格式文件，数据要求3列，表头格式为：[商品编号], [商品名称],<strong>[可售库存计划数量]</strong></div>
          <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> 隔日预设库存最迟每天22:25分前上传，之后上传的数据无效。系统将在22:30分开始计算并重设生鲜库存。若超出时间请在“生鲜可售库存”中手动重置库存（包含所有生鲜商品）。</div>
          <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> 目前仅需预设生鲜蔬果，盒装保鲜冷鲜肉，盒装保鲜豆制品，鲜奶等商品。<br />其他冷冻冷藏标品，目前将根据剩余库存及采购单实际到货量计算，实际收货与到货不符合，目前需要手工调整，后续将改为商品入库自动增加库存（开发中）。</div>
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">上传数据</a></li>
            <li><a href="#tab-list" data-toggle="tab" onclick="loadInventoryPlanList();">数据上传列表</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="tab-content">
                  <form action="<?php echo $action_upload; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
                      <div class="form-group">
                          <div class="col-sm-2">
                              <label class="control-label" style="font-size: 110%; ">可售库存计划日期</label>
                              <div class="input-group date">
                                  <input type="text" name="inventory_plan_date" value="<?php echo $inventory_plan_date; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input_inventory_plan_date" class="form-control" />
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
                          <div class="col-sm-2">
                              <button type="submit" class="btn btn-primary">上传</button>
                          </div>
                      </div>
                  </form>
              </div>
            </div>
            <div class="tab-pane" id="tab-list">
                <div class="table-responsive">
                    <table id="inventory_plan" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <td class="text-center">编号</td>
                            <td class="text-left">预设日期</td>
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

        if(window.confirm('确认导入采购数据么？')){
            $('#form-product-adjust').submit();
        }
    }

    function loadInventoryPlanList(){
        $.ajax({
            type: 'GET',
            url: 'index.php?route=purchase/inventory_plan/getInventoryPlanList&token=<?php echo $_SESSION['token']; ?>',
            dataType: 'json',
            success: function(data){
                console.log(data);

                $('#inventory_plan tbody').html('');
                var html = '';
                $.each(data, function(i, n){
                    html += '<tr>';
                    html += '  <td class="text-center">' + n.inventory_plan_id + '</td>';
                    html += '  <td class="text-center">' + n.inventory_plan_date + '</td>';
                    html += '  <td class="text-center">' + n.adate + '</td>';
                    html += '  <td class="text-center">' + n.upload_times + '</td>';
                    html += '  <td class="text-center">' + n.add_user_name + '</td>';
                    html += '  <td class="text-center"><button onclick="loadInventoryPlanDetail(' + n.inventory_plan_id + ');">查看</button></td>';
                    html += '</tr>';

                    html += '<tr style="display: none; background-color: #fffadf" class="inventory_plan_detail" id="inventory_plan_id_'+ n.inventory_plan_id +'"><td  colspan="6" ></td></tr>';
                });
                $('#inventory_plan tbody').html(html);
            },
            error: function(){
                //console.log(this);
            }
        });
    }

    function loadInventoryPlanDetail(inventory_plan_id){
        $.ajax({
            type: 'GET',
            url: 'index.php?route=purchase/inventory_plan/getInventoryPlanDetail&token=<?php echo $_SESSION["token"]; ?>&inventory_plan_id='+inventory_plan_id,
            dataType: 'json',
            success: function(data){
                console.log(data);

                var refIdCotainer = '#inventory_plan_id_'+ inventory_plan_id;
                $('.inventory_plan_detail').hide();
                var html = '<div class="table-responsive">';
                html += '<div style="text-align: right">';
                html += '   <button style="color:#df8505" onclick="$(\'.inventory_plan_detail\').hide();">关闭</button>';
                html += '</div>';
                html += '<table id="purchase-detail-list" class="table table-striped table-bordered table-hover">';
                html += '<caption style="font-size: 16px;  text-align:center; font-weight: bold;">预设可售库存单据#'+inventory_plan_id+'</caption>';
                html += '   <thead>';
                html += '       <tr>';
                html += '           <td>商品编号</td>';
                html += '           <td>商品名称</td>';
                html += '           <td>预设库存</td>';
                html += '           <td>状态</td>';
                html += '       </tr>';
                html += '   </thead>';

                $.each(data, function(i, n){
                    html += '<tr>';
                    html += '  <td class="text-center">' + n.product_id + '</td>';
                    html += '  <td class="text-center">' + n.product_name + '</td>';
                    html += '  <td class="text-center">' + n.quantity + '</td>';
                    if(parseInt(n.status)==1){
                        html += '  <td class="text-center">在售</td>';
                    }
                    else{
                        html += '  <td class="text-center" style="background-color: #ffff66;color: #CC0000">停用</td>';
                    }
                    html += '</tr>';
                });
                html += '</table>';
                html += "  <div style='text-align: right; font-size: 12px; font-weight: bold; margin: 0 0 30px 0;'>预设可售库存单据#" + inventory_plan_id;
                html += '   <button style="color:#df8505" onclick="$(\'.inventory_plan_detail\').hide();">关闭</button>';
                html += "  </div>";
                html += "</div>";

                $(refIdCotainer+' td').html(html);
                $(refIdCotainer).show();
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