<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>退货单</h1>
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
          <h3 class="panel-title"><i class="fa fa-pencil"></i>退货单</h3>
      </div>
      <div class="panel-body">
          
          <div class="tab-content">
            
              
              
              
              
            <div  id="tab-special">
              <div class="table-responsive">
                  <form action="<?php echo $action_select; ?>" method="post" enctype="multipart/form-data" id="form-product-select" class="form-horizontal">
                       <div class="well">
          <div class="row">
            
            
            <div class="col-sm-4">
              
                
              <div class="form-group">
                <label class="control-label" for="input-supplier-type">供应商</label>
                <select name="supplier_type" id="input-supplier-type" class="form-control">
                  <option value="*"></option>
                  
                  <?php foreach ($supplier_types as $s_type) { ?>
                  <?php if ($s_type['supplier_id'] == $supplier_type) { ?>
                  <option value="<?php echo $s_type['supplier_id']; ?>" selected="selected"><?php echo $s_type['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $s_type['supplier_id']; ?>"><?php echo $s_type['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
                
                
                <div class="form-group">
                <label class="control-label" for="input-purchase-order-id">采购单ID</label>
                
                 <input type="text" name="filter_purchase_order_id" value="<?php echo $purchase_order_id; ?>" placeholder="采购单ID" id="input-purchase-order-id" class="form-control" /> 
              </div>
                
               
              
            </div>
              
           
              
              
              
          </div>
        </div>
                  </form>
                  <div class="alert alert-info">退货数量不能超过订货数量</div>
                  <form action="" method="post" enctype="multipart/form-data" id="form-product-adjust" class="form-horizontal">
                    
                      <table id="adjust" class="table table-striped table-bordered table-hover">
                      <thead>
                     
                          <tr>
                            
                            <td class="text-center" width="7%">商品ID</td>
                            <td class="text-center" width="8%">采购数量</td>
                            <td class="text-center" width="8%">退货规格数量</td>
                            
                            
                            <td class="text-center" width="10%">供应商退货规格数量</td>
                            <td class="text-center" width="10%">采购价格</td>
                            <td class="text-center" width="10%">供应商规格</td>
                            <td class="text-center" width="10%">商品规格</td>
                            
                            <td class="text-center">商品名称</td>
                            
                        </tr>
                        
                          
                      </thead>
                      <tbody>
                        <!-- 添加商品可售库存 -->
                        <?php foreach($pre_purchase_product as $key=>$value){ ?> 
                           <?php if($value['pre_purchase_product']['pre_purchase_quantity'] > 0 || $station_id == 2){ ?>
                            
                          <tr id="adjust-row<?php echo $key;?>">
                            <td class="text-center"><input product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][product_id]" value="<?php echo $key;?>" placeholder="商品ID" class="form-control int" readonly="readonly" type="text"></td>  
                            <td class="text-center"><input id="purchase_quantity_<?php echo $key;?>" product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][purchase_quantity_old]" value="<?php echo $value['pre_purchase_product']['pre_purchase_quantity'];?>" placeholder="采购数量" class="form-control int" readonly="readonly" type="text"></td>  
                            
                            <td class="text-left"><input  id="return_quantity_<?php echo $key;?>" product_id="<?php echo $key;?>" name="products[<?php echo $key;?>][quantity]" value="0" placeholder="退货数量" class="form-control int product_quantity return_quantity" type="text"></td>  
                            
                            <td class="text-left"><input product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][supplier_quantity]" value="0" placeholder="供应商退货数量" class="form-control int supplier_quantity" type="text"></td>  
                            <td class="text-left"><input product_id="[<?php echo $key;?>" name="products[<?php echo $key;?>][price]" value="<?php echo $value['pre_purchase_product']['price'];?>" placeholder="请输入采购价格" class="form-control int price" type="text"></td>  
                            <td class="text-left"><div class="rowCurrInv"><?php echo $value['pre_purchase_product']['supplier_unit_size'];?></div></td>  
                            <td class="text-left"><div class="rowCurrInv"><?php echo $value['pre_purchase_product']['inv_size'];?></div></td>  
                           
                            
                            
                            
                            
                            
                            <td class="text-left"><div class="rowCurrInv"><?php echo $value['pre_purchase_product']['name'];?></div></td>  
                            
                            
                          </tr>
                          <?php  } ?>
                        <?php } ?>  
                      </tbody>
                      <tfoot>
                        
                      <tr>
                          <td colspan="7">
                              <input id="get_order_total" onclick="getOrderTotal();" type="button" value="计算退货金额" >:<span id="order_total"></span>
                              <input id="get_order_total_num" onclick="getOrderTotalNum();" type="button" value="计算退货数量" >:<span id="order_total_num"></span>
                          </td>
                        
                      </tr>
                      <tr>
                          <td colspan="7" class="text-center">
                              <input type="hidden" name="station_id" value="<?php echo $station_id?>">
                              <input type="hidden" name="supplier_type" value="<?php echo $supplier_type;?>">
                              <input type="hidden" name="purchase_order_id" value="<?php echo $purchase_order_id;?>">
                              <input type="hidden" name="order_type" value="2">
                              
                              
                          
                          </td>
                      </tr>
                      </tfoot>
                    </table>
                      <div class="col-sm-4">
                        <div class="form-group">
                            <input type="hidden" name="warehouse_id" value="<?php echo $warehouse_id; ?>" id="warehouse_id" />
                            是否退余额：<input type="checkbox" name="sub_use_credits" value="1" ><br>
                            <div style="display:none;" >确认到货时间：<input type="text" name="date_deliver" value="<?php echo $date_deliver;?>" placeholder="到货日期" data-date-format="YYYY-MM-DD" id="input-date-deliver" class="form-control" /></div>
                            退货说明：<textarea name="order_comment" value="" placeholder=""  id="input-order-comment" class="form-control" ></textarea>
                            
                        </div>
                          
                        </div>
                      <div style="float:left;" class="col-sm-4"><button type="button" class="btn btn-primary" onclick="submitAdjust();">确认退货</button></div>
                  </form>
              </div>
            </div>
              
              
              
              
              
              
              
          </div>
      </div>
    </div>
  </div>
 
<script type="text/javascript">
    
    
    function getOrderTotal(){
        var order_total = 0;
        $('#adjust tbody tr').each(function(index, element){
           var supplier_quantity = $(element).find(".supplier_quantity").val(); 
           var price =  $(element).find(".price").val();
           
           order_total  = order_total + parseFloat((supplier_quantity * price).toFixed(2));
        });
        $("#order_total").html(order_total);
    }
    
    function getOrderTotalNum(){
        var order_total = 0;
        $('#adjust tbody tr').each(function(index, element){
           var quantity = $(element).find(".product_quantity").val(); 
           
           order_total  = order_total + parseInt(quantity);
        });
        $("#order_total_num").html(order_total);
    }
    
    
    
    var adjust_row = 0;
    
    
    function addAdjust() {
        html  = '<tr id="adjust-row' + adjust_row + '">';
        html += '  <td class="text-center"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][product_id]" value="" placeholder="商品ID" class="form-control int" onChange="getProductInv($(this).val(), $(this));" /></td>';
        html += '  <td class="text-left"></td>';
        html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][quantity]" value="" placeholder="按供应商规格计算后采购数量" class="form-control int product_quantity" /></td>';
        html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][supplier_quantity]" value="" placeholder="供应商采购数量" class="form-control int supplier_quantity" /></td>';
        html += '  <td class="text-left"><input product_id="'+adjust_row+'" type="text" name="products[' + adjust_row + '][price]" value="" placeholder="采购价格" class="form-control int purchase_price price" /></td>';
        html += '  <td class="text-left"><div class="supplier_unit_size"></div></td>';
        html += '  <td class="text-left"><div class="inv_size"></div></td>';
        html += '  <td class="text-left"></td>';
        html += '  <td class="text-left"></td>';
        html += '  <td class="text-left"></td>';
        html += '  <td class="text-left"><div class="rowName"></div></td>';
        html += '  <td class="text-left"><div class="rowOriInv"></div></td>';
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
        var valid_return = true;
        var err_product_id = 0;
        $('#adjust tbody .return_quantity').each(function(index, element){
            var p_id = parseInt($(element).attr("product_id"));
            
            var r_q = parseInt($(element).val());
            var p_q = parseInt($("#purchase_quantity_"+p_id).val());
            
            if(r_q > 0){
                valid_return = false;
            }
            
            if(r_q > p_q){
                valid = false;
                err_product_id = $(element).attr("product_id");
                return false;
            }
        });
        
        if(!valid){
            alert(err_product_id + '退货数量不能超过采购单数量!');
            return false;
        }
        if(valid_return){
            alert('请填写退货数量!');
            return false;
        }
        
        
        
        
        
        var date_deliver = $("#input-date-deliver").val();
        if(date_deliver == ''){
            alert("请选择到货日期");return false;
        }
        
        var warehouse_id = $('#warehouse_id').val();
        if(warehouse_id <= 0){
            alert("仓库ID有误");return false;
        }
        

        if(window.confirm('确认提交退货单？')){
            $('#form-product-adjust').submit();
        }
    }


    function submitSelect(){
        
        
        var pre_three_day_before = $("#input-date-before").val();
        if(pre_three_day_before == ''){
            alert("请选择均销开始日期！");
            return false;
        }
        
        var pre_three_day_end = $("#input-date-end").val();
        if(pre_three_day_end == ''){
            alert("请选择均销结束日期！");
            return false;
        }
        
         var date_deliver = $("#input-date-deliver").val();
        if(date_deliver == ''){
            alert("请选择到货日期！");
            return false;
        }
        
        var station_id = $("#input-station-id").val();
        if(station_id == '*'){
            alert("请选择商品类型！");
            return false;
        }
        
        
        
        var supplier_type = $("#input-supplier-type").val();
        if(supplier_type == '*'){
            alert("请选择供应商分类！");
            return false;
        }
        
        

        $('#form-product-select').submit();
        
    }


    function getProductInv(product_id,obj){
        var rowId = obj.parent().parent().attr('id');
        //rowStation
        //rowName
        //rowOriInv
        //rowCurrInv

        var product_id = parseInt(product_id);

        $.ajax({
            type: 'GET',
            async: false,
            cache: false,
            url: 'index.php?route=catalog/inventory/getProductInv&token=<?php echo $_SESSION['token']; ?>',
            data: {
                product_id: product_id
            },
            dataType: 'json',
            success: function(response){
                console.log(response);

                if(parseInt(response.station_id) > 0){
                   
                    $('#'+rowId+' .purchase_price').val(response.purchase_price);
                    
                    $('#'+rowId+' .supplier_unit_size').html(response.supplier_unit_size);
                    $('#'+rowId+' .inv_size').html(response.inv_size);
                    
                    $('#'+rowId+' .rowName').html(response.name);
                    $('#'+rowId+' .rowOriInv').html(response.ori_inv);
                    $('#'+rowId+' .rowCurrInv').html(response.curr_inv);
                    
                }
                else{
                    
                }
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