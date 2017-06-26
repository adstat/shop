<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i>缺货订单补货管理</h3>
      </div>
      <div class="panel-body">
          <ul class="nav nav-tabs" id="replenish_tabs">
            <li class="active"><a href="#tab-special" data-toggle="tab">手工添加(赠品)补送</a></li>
            <li><a href="#tab-list" data-toggle="tab" onclick="getOrderReplenishList();">补送计划列表</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-special">
              <div class="table-responsive">
                  <div class="alert alert-warning">目前仅支持添加赠品。</div>
                  <div class="well">
                      <div class="row">
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <input type="text" name="check_order_id" placeholder="订单号(查询缺货信息)" id="check_order_id" class="form-control" />
                                  ＊不含取消及配送失败订单
                              </div>
                          </div>
                          <div class="col-sm-1">
                              <div class="form-group">
                                  <button type="button" id="button-search" class="btn btn-primary pull-left" onclick="getOrderProductOutStockHistory();"><i class="fa fa-search"></i> 查询</button>
                              </div>
                          </div>
                      </div>
                      <div class="row" id="replenish_history" style="display: none">
                          <div class="col-sm-12">
                              <div class="table-responsive">
                                  <table class="table table-striped table-bordered table-hover">
                                      <caption id="replenish_history_caption">订单＃0用户赠品缺货信息</caption>
                                      <thead>
                                      <tr>
                                          <th>缺货订单号</th>
                                          <th>下单日期</th>
                                          <th>配送日期</th>
                                          <th>用户信息</th>
                                          <th>BD信息</th>
                                          <th>缺货商品</th>
                                          <th>数量</th>
                                          <th>补货状态</th>
                                      </tr>
                                      </thead>
                                      <tbody id="replenish_history_list">
                                        <!-- 现实当前可售库存 -->
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                  </div>

                  <div class="well">
                      <form action="<?php echo $action_adjust; ?>" method="post" enctype="multipart/form-data" id="form-product-adjust" class="form-horizontal">
                        <table id="adjust" class="table table-striped table-bordered table-hover">
                          <thead>
                              <tr>
                                  <td colspan="8">备注: <input name="comment" type="text" maxlength="50" size="50" /></td>
                              </tr>
                              <tr>
                                  <td class="text-center">缺货订单号</td>
                                  <td class="text-center">平台</td>
                                  <td class="text-center" width="65">订单状态</td>
                                  <td class="text-center">用户信息</td>
                                  <td class="text-center" width="65">BD信息</td>
                                  <td class="text-center">补入未配送订单</td>
                                  <td class="text-center">赠品信息</td>
                                  <td class="text-center">赠品数量</td>
                                </tr>
                          </thead>
                          <tbody>
                            <!-- 添加商品可售库存 -->
                          </tbody>
                          <tfoot>
                          <tr style="display: none">
                            <td colspan="8"></td>
                            <td class="text-left"><button type="button" onclick="addAdjust();" data-toggle="tooltip" title="添加" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                          </tr>
                          <tr>
                              <td colspan="8" class="text-center"><button type="button" class="btn btn-primary" onclick="submitAdjust();">确认添加</button></td>
                          </tr>
                          </tfoot>
                        </table>
                      </form>
                  </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-list">
                <div class="table-responsive">
                    <table id="replenish_list" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <td class="text-center">缺货订单号</td>
                            <td class="text-left">补货名称</td>
                            <td class="text-left">补货数量</td>
                            <td class="text-center">补货订单</td>
                            <td class="text-left">平台</td>
                            <td class="text-center">订单状态</td>
                            <td class="text-center">BD信息</td>
                            <td class="text-center">执行情况</td>
                            <td class="text-center">操作</td>
                        </tr>
                        </thead>
                        <tbody>
                            <!-- data -->
                        </tbody>
                    </table>
                </div>
            </div>
          </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    <!--
    $('#language a:first').tab('show');
    $('#option a:first').tab('show');
    //-->
  </script>

  <script type="text/javascript">
    var adjust_row = 0;
    function addAdjust() {
        var html = '';
        html  = '<tr id="adjust-row' + adjust_row + '">';
        html += '  <td class="text-center"><input type="text" name="lists[' + adjust_row + '][order_id]" value="" placeholder="缺货订单号" class="form-control listVal" onChange="getOrderInfo($(this).val(), $(this));" /></td>';
        html += '  <td class="text-left"><div class="rowStation"></div></td>';
        html += '  <td class="text-left"><div class="rowStatus"></div></td>';
        html += '  <td class="text-left"><div class="rowCustomerInfo" title=""></div></td>';
        html += '  <td class="text-left"><div class="rowBDInfo"></div></td>';

        html += '  <td class="text-left">';
            html += '<select class="rowTargetOrder listVal" name="lists[' + adjust_row + '][target_order]">';
            html += '</select>';
        html += '  </td>';

        html += '  <td class="text-left rowProductList">';
            html += '<select class="listVal" name="lists[' + adjust_row + '][product_id]">';
            html += "</select>";
        html += '  </td>';

        html += '  <td class="text-left"><input type="number" name="lists[' + adjust_row + '][qty]" value="1" placeholder="商品数量" class="form-control listVal" /></td>';
        //html += '  <td class="text-left"><button type="button" onclick="$(\'#adjust-row' + adjust_row + '\').remove();" data-toggle="tooltip" title="删除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';

        $('#adjust tbody').append(html);
        adjust_row++;
    }
    addAdjust();

    function submitAdjust(){
        var trs = $('#adjust tbody tr').length;
        if(trs < 1){
            alert('请先添加.');
            return false;
        }

        var valid = true;
        $('#adjust tbody .listVal').each(function(index, element){
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

        if(window.confirm('确认添加这些补送赠品计划吗？选择“补入未配送订单”的计划将立即被执行。')){
            $('#form-product-adjust').submit();
        }
    }

    function getOrderProductOutStockHistory(){
        $("#replenish_history").hide();

        var order_id = $('#check_order_id').val();

        if(parseInt(order_id)<10000){
            alert("请输入正确的订单号!");
            return false;
        }

        $.ajax({
            type: 'GET',
            async: false,
            cache: false,
            url: 'index.php?route=sale/order_replenish/getOrderProductOutStockHistory&token=<?php echo $_SESSION['token']; ?>',
            data: {
                order_id: order_id
            },
            dataType: 'json',
            success: function(response){
                console.log(response);
                $("#replenish_history").show();
                $("#replenish_history_caption").html('订单<a target="_blank" href="index.php?route=sale/order/info&tab=product&token=<?php echo $_SESSION['token']; ?>&order_id='+ order_id +'">[#'+order_id+']</a>用户，赠品缺货信息');

                var html = '';
                $.each(response, function(n,i){
                    html += '<tr>';
                        html += '<td><a target="_blank" href="index.php?route=sale/order/info&tab=product&token=<?php echo $_SESSION['token']; ?>&order_id='+ i.order_id +'">' + i.order_id +'</a></td>';
                        html += '<td title="'+ i.date_added +'">'+ i.adate +'</td>';
                        html += '<td>'+ i.deliver_date +'</td>';
                        html += '<td title="'+ i.customer_info +'">'+ i.customer_brief_info +'</td>';
                        html += '<td>'+ i.bd_name +'</td>';
                        html += '<td>'+ i.product_info +'</td>';
                        html += '<td>'+ i.order_replenish_qty +'</td>';
                        if(i.order_replenish_id > 0){
                            html += '<td><span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">补货计划已存在</span></td>';
                        }
                        else{
                            html += '<td>未记录</td>';
                        }
                    html += '</tr>';
                });
                if(html == ''){
                    html = '<tr><td colspan="8">未找到缺货记录。</td></tr>';
                }
                $("#replenish_history_list").html(html);
            }
        });
    }

    function getOrderInfo(order_id,obj){
        //TODO 订单状态限制
        var rowId = obj.parent().parent().attr('id');
        var order_id = parseInt(order_id);

        $.ajax({
            type: 'GET',
            async: false,
            cache: false,
            url: 'index.php?route=sale/order_replenish/getOrderInfo&token=<?php echo $_SESSION['token']; ?>',
            data: {
                order_id: order_id
            },
            dataType: 'json',
            success: function(response){
                console.log(response);

                var targetOrders = '';
                targetOrders += "<option value='0'>--选择订单[立即执行]--</option>";
                $.each(response.target_orders, function(n,i){
                    targetOrders += "<option style='padding: 5px 3px; font-size: 14px' value='"+ i.order_id +"'>" + i.order_id + '[' + i.deliver_date + "]</option>";
                });
                targetOrders += "<option value='1' style='display:none;background-color:#ffffaa; padding: 5px 3px; font-size: 14px; font-weight: bold; font-style: italic'>无订单系统自动监测</option>";

                var giftList = '';
                giftList += "<option value='0'>--选择要补送的赠品--</option>";
                <?php foreach($gift_list as $m){ ?>
                    giftList += "<option style='padding: 5px 3px; font-size: 12px;' value='<?php echo $m['product_id']; ?>'><?php echo $m['name']; ?></option>";
                <?php } ?>


                if(parseInt(response.station_id) >= 2){
                    $('#'+rowId+' .rowStation').html(response.station);
                    $('#'+rowId+' .rowStatus').html(response.order_status + '<br />' + response.payment_status + '<br />' + response.deliver_status);                    $('#'+rowId+' .rowCustomerInfo').attr('title',response.customer_info);
                    $('#'+rowId+' .rowStatus').attr('title',response.payment_method+','+response.total);
                    $('#'+rowId+' .rowCustomerInfo').html(response.customer_brief_info);
                    $('#'+rowId+' .rowCustomerInfo').attr('title',response.customer_info);
                    $('#'+rowId+' .rowBDInfo').html(response.bd_name);
                    $('#'+rowId+' .rowTargetOrder').html(targetOrders);
                    $('#'+rowId+' .rowProductList select').html(giftList);
                }
                else if(parseInt(response.station_id) == 1){
                    $('#'+rowId+' .rowStation').html(response.station);
                    $('#'+rowId+' .rowCustomerInfo').html('');
                    $('#'+rowId+' .rowBDInfo').html(response.bd_name);
                    $('#'+rowId+' .rowStatus').html('<span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">不支持</span>');
                    $('#'+rowId+' .rowTargetOrder select').html('');
                    $('#'+rowId+' .rowProductList select').html('');

                }
                else{
                    $('#'+rowId+' .rowStation').html('');
                    $('#'+rowId+' .rowCustomerInfo').html('');
                    $('#'+rowId+' .rowBDInfo').html('');
                    $('#'+rowId+' .rowStatus').html('<span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">不存在</span>');
                }
            }
        });
    }

    function getOrderReplenishList(){
        $.ajax({
            type: 'GET',
            url: 'index.php?route=sale/order_replenish/getOrderReplenishList&token=<?php echo $_SESSION['token']; ?>',
            dataType: 'json',
            success: function(data){
                console.log(data);

                var html= '';
                $('#replenish_list tbody').html('');
                $.each(data, function(n,i){
                    html += '<tr>';
                        html += '<td style="font-size: 14px"><a target="_blank" href="index.php?route=sale/order/info&tab=product&token=<?php echo $_SESSION['token']; ?>&order_id='+i.replenish_order_id+'">'+ i.replenish_order_id +'</a></td>';
                        html += '<td>'+ i.product_name;
                        if(i.comment !== ''){
                            html += '<div style="padding: 3px; margin-top:3px; border-radius: 3px; border: 1px #6c6c6c dashed">备注:'+ i.comment + '</div>';
                        }
                        html += '</td>';
                        html += '<td>'+ i.quantity +'</td>';

                        if(i.target_order_id == 0){
                            html += '<td>无</td>';
                            html += '<td>-</td>';
                            html += '<td>-</td>';
                            html += '<td>-</td>';
                        }
                        else{
                            html += '<td style="font-size: 14px" title="'+ i.customer_info +'"><a target="_blank" href="index.php?route=sale/order/info&tab=product&token=<?php echo $_SESSION['token']; ?>&order_id='+i.target_order_id+'">'+ i.target_order_id +'</a><br />'+ i.deliver_date +'配送'+'<br/ >'+ i.customer_brief_info+'</td>';
                            html += '<td>'+ i.station +'</td>';
                            html += '<td>'+ i.order_status + '<br />' + i.payment_status + '<br/>' + i.deliver_status +'</td>';
                            html += '<td>'+ i.bd_name +'</td>';
                        }

                        html += '<td>';
                            if(i.status == 1){
                                html += '<span style="background-color: #66CC66; color: #ffffff; padding: 3px;">有效</span><br />';
                            }else{
                                html += '<span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">取消</span><br />';
                            }

                            html += '['+ i.added_username+']'+ i.date_added +'添加';

                            if(i.executed == 1){
                                html += '<br />['+ i.executed_username+']'+ i.date_executed + '<span style="background-color: #66CC66; color: #ffffff; padding: 3px;">已执行</span>';
                            }else{
                                html += '<br /><span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">未执行</span>';
                            }
                        html += '</td>';
                        html += '<td>-</td>';

                    html += '</tr>';
                });

                $('#replenish_list tbody').html(html);
            }
        });
    }

    <?php if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'list'){ ?>
        $('#replenish_tabs a[href="#tab-list"]').tab('show');
        $('#replenish_tabs a[href="#tab-list"]').click();
    <?php } ?>
</script>
</div>
<?php echo $footer; ?> 