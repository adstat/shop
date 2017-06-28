<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td class="text-center">商品ID</td>
        <td class="text-center">商品名称</td>
        <td class="text-center">商品编码</td>
        <td class="text-center">是否外箱码</td>
        <td class="text-center">仓库唯一</td>
        <td class="text-center">操作</td>
      </tr>
    </thead>
    <tbody>
      <?php if (sizeof($histories)) { ?>
      <?php $line = 0; ?>
      <?php foreach ($histories as $history) { ?>

      <tr id="<?php echo $history['sku_barcode_id'] ; ?>">
        <td class="text-center"><?php echo $history['product_id']; ?></td>
        <td class="text-center"><?php echo $history['name']; ?></td>
        <td class="text-center" id="sku_<?php echo $history['sku_barcode_id']; ?>"><?php echo $history['sku_barcode']; ?></td>
        <td class="text-center" id="box_<?php echo $history['sku_barcode_id']; ?>"><?php echo $history['box']; ?></td>
        <td class="text-center" id="warehouse_<?php echo $history['sku_barcode_id']; ?>"><?php echo $history['warehouse']; ?></td>
        <td class="text-center">
          <button type="button" id="edit_<?php echo $history['sku_barcode_id']; ?>" value="<?php echo $history['sku_barcode_id']; ?>" class="btn btn-default" id="" onclick="editRow($(this).val());"><i class="fa fa-pencil"></i></button>
          <button style="display: none" id="save_<?php echo $history['sku_barcode_id']; ?>" value="<?php echo $history['sku_barcode_id']; ?>" type="button" class="btn btn-danger" onclick="updateRow($(this).val())"><i class="fa fa-save"></i></button>
        </td>
      </tr>
      <?php $line++; ?>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<script type="text/javascript">
  function editRow(id){
    $('#edit_'+id).hide();
    $('#save_'+id).show();

    $('#sku_'+id).html('<input id="save_sku_'+id+'" name="sku" value="'+$('#sku_'+id).text()+'">');
    $('#box_'+id).html('<select id="save_box_'+id+'" name="box">'+ getBox($('#box_'+id).text()) +'</select>');
    $('#warehouse_'+id).html('<select id="save_warehouse_'+id+'" name="warehouse">'+ getWarehouse($('#warehouse_'+id).text()) +'</select>');

  }

  function updateRow(id){
    $('#edit_'+id).show();
    $('#save_'+id).hide();

    var sku = $("#save_sku_"+id).val();
    var box = $("#save_box_"+id).val();
    var warehouse = $("#save_warehouse_"+id).val();


    var flag = confirm('确认更改编码信息?');
    if(flag){
      $.ajax({
        type: 'POST',
        async: false,
        cache: false,
        url: 'index.php?route=catalog/product_skubarcode/updateBarcode&token=<?php echo $_SESSION["token"]; ?>&product_id=<?php echo $product_id; ?>&sku_barcode_id='+id,
        data : {
          sku : sku,
          box: box,
          warehouse : warehouse,
        },
        dataType: 'json',
        success: function(data){
          if(data){
            alert('更新编码成功！');
            $('#sku_'+id).html(sku);
            $('#box_'+id).html($("#save_box_"+id).find('option:selected').text());
            $('#warehouse_'+id).html( $("#save_warehouse_"+id).find('option:selected').text());

          }else{
            alert('更新编码失败！');
          }
        },
        error: function(){
          alert('更新编码失败！');
        }
      });
    }
  }

  function getWarehouse(data){

    var warehouse = <?php echo $warehouses; ?>;
    var html= '';

    html += '<option value="0">'+ '无' +'</option>';

    $.each(warehouse,function(i,v){
        if(v.title == data){
          html += '<option value="'+ v.warehouse_id +'" selected="selected">'+ v.title +'</option>';
        }else{
          html += '<option value="'+ v.warehouse_id +'">'+ v.title +'</option>';
        }
    });

    return html;
  }

  function getBox(data){
    var html= '';
    if(data == '否'){
      html += '<option value="0" selected="selected">'+ '否' +'</option>';
      html += '<option value="1">'+ '是' +'</option>';
    }else{
      html += '<option value="0">'+ '否' +'</option>';
      html += '<option value="1" selected="selected">'+ '是' +'</option>';
    }
    return html;
  }
</script>
