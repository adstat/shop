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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>原料价格数据上传</h3>
      </div>
      <div class="panel-body">
          <div class="alert alert-warning">
              当日上传的相同商品编号的价格数据，仅最后一次有效<br />
              数据表头：[商品编号], [商品名称], [原零售价], [新零售价], [备注(30字内)]
          </div>
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">上传数据</a></li>
            <li><a href="#tab-list" data-toggle="tab">上传数据查询</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="tab-content">
                  <form action="<?php echo $action_upload; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
                      <div class="form-group">
                          <div class="col-sm-6">
                            <label class="control-label">上传Excel文件</label>
                            <input type="file" name="file" id="input-upload-xls" class="form-control" />
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="control-label">23:00-凌晨01:59期间不能上传</label>
                          <div class="col-sm-2">
                              <?php $now = (int)date("Hi", time()+8*3600); if( ($now > '200' || $now <'2300')){ ?>
                              <button type="submit" class="btn btn-primary">上传</button>
                              <?php } ?>
                          </div>
                      </div>
                  </form>
              </div>
            </div>
            <div class="tab-pane" id="tab-list">
                <div class="col-sm-2">
                    <div class="input-group date">
                        <input type="text" name="upload_date" value="<?php echo $upload_date; ?>" data-date-format="YYYY-MM-DD" id="upload_date" class="form-control" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-primary" onclick="getUploadPriceByDate();">查询</button>
                </div>

                <div class="col-sm-12">
                    <table id="upload_price" class="table table-striped table-bordered table-hover" style="margin-top: 15px;">
                        <thead>
                        <tr>
                            <td class="text-center">商品编号</td>
                            <td class="text-center">商品名称</td>
                            <td class="text-center">上传时间</td>
                            <td class="text-center">当时零售价</td>
                            <td class="text-center">上传原价</td>
                            <td class="text-center">上传零售价</td>
                            <td class="text-center">上传用户</td>
                            <td class="text-center">备注</td>
                            <td class="text-center">上传记录</td>
                        </tr>
                        </thead>
                        <tbody>
                            <!-- 原料价格数据 -->
                            <tr><td colspan="9">选择日期查询</td></tr>
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

    function getUploadPriceByDate(){
        var date = $('#upload_date').val();

        $.ajax({
            type: 'GET',
            url: 'index.php?route=catalog/product_price/getUploadPriceByDate&token=<?php echo $_SESSION['token']; ?>&date='+date,
            dataType: 'json',
            success: function(data){
                console.log(data);

                $('#purchase-list tbody').html('');
                var html = '';
                $.each(data, function(i, n){
                    html += '<tr>';
                    html += '  <td class="text-center">' + n.product_id + '</td>';
                    html += '  <td class="text-left">' + n.name + '</td>';
                    html += '  <td class="text-left">' + n.time + '</td>';
                    html += '  <td class="text-left">' + n.last_retail_price + '</td>';
                    html += '  <td class="text-left">' + n.upload_last_retail_price + '</td>';
                    html += '  <td class="text-left">' + n.upload_retail_price, + '</td>';
                    html += '  <td class="text-left">' + n.username + '</td>';
                    html += '  <td class="text-left">' + n.memo + '</td>';
                    html += '  <td class="text-left">' + '上传' + n.upload_times + '次,[' + n.upload_history + ']' + '</td>';
                    html += '</tr>';
                });
                if(data.length == 0){
                    html = '<tr><td colspan="9">' + date + '无上传数据</td></tr>'
                }
                $('#upload_price tbody').html(html);
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