<form method="post" enctype="multipart/form-data" id="form-area-edit" class="form-horizontal">
<div class="table-responsive">
    <table id="set-merge"  class="table table-bordered table-hover">
        <thead>
        <tr>
            <td class="text-center">商品ID</td>
            <td class="text-center">商品名</td>
            <td class="text-center">仓库名</td>
            <td class="text-center">区域</td>
            <td class="text-center">促销价</td>
            <td class="text-center">原价</td>
            <td class="text-center">限购</td>
            <td class="text-center">标题</td>
            <td class="text-center">开始日期</td>
            <td class="text-center">结束日期</td>
            <td class="text-center">操作</td>
        </tr>
        </thead>
        <tbody>
        <?php if ($histories) { ?>
        <?php foreach ($histories as $history) { ?>
        <tr id = "promotion_<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>">
            <td class="text-center"><?php echo $history['product_id']; ?></td>
            <td class="text-center"><?php echo $history['product_name']; ?></td>
            <td class="text-center"><?php echo $history['warehouse_name']; ?></td>
            <td class="text-center"><?php echo $history['area_name']; ?></td>
            <td class="text-center editable" id="price_<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>" ><?php echo $history['price']; ?></td>
            <td class="text-center"><?php echo $history['ori_price']; ?></td>
            <td class="text-center editable" id="maximum_<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>"><?php echo $history['maximum']; ?></td>
            <td class="text-center editable" id="title_<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>"><?php echo $history['promo_title']; ?></td>
            <td class="text-center editable" id="start_<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>"><?php echo $history['date_start']; ?></td>
            <td class="text-center editable" id="end_<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>"><?php echo $history['date_end']; ?></td>
            <td class="text-center">
                <button type="button" id="edit_<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>" value="<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>" class="btn btn-default" id="" onclick="editRow($(this),$(this).val());"><i class="fa fa-pencil"></i></button>
                <button style="display: none" id="save_<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>" value="<?php echo $history['product_id'].'_'.$history['warehouse_id'].'_'.$history['area_id']; ?>" type="button" class="btn btn-danger" onclick="updateRow($(this),$(this).val())"><i class="fa fa-save"></i></button>
            </td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
            <td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</form>
<script type="text/javascript">
    mergeRows(0);
    function mergeRows(col){
        var trs = $("#set-merge").find('tr');
        var rows = 1;
        for(var i=trs.length;i>0;i--){
            var cur = $($(trs[i]).find("td")[col]).text();
            var next = $($(trs[i-1]).find("td")[col]).text();
            if(cur==next){
                rows++;
                $($(trs[i]).find("td")[col]).remove();
            } else {
                $($(trs[i]).find("td")[col]).attr("rowspan",rows);
                rows=1;
            }
        }
    }

    function editRow(row,id){

        $('#edit_'+id).hide();
        $('#save_'+id).show();

        $('#price_'+id).html('<input id="save_price_'+id+'" name="promotion_price[]" value="'+$('#price_'+id).text()+'">');
        $('#maximum_'+id).html('<input id="save_maximum_'+id+'" name="promotion_maximum[]" value="'+$('#maximum_'+id).text()+'">');
        $('#title_'+id).html('<input id="save_title_'+id+'" name="promotion_title[]" value="'+$('#title_'+id).text()+'">');
        $('#start_'+id).html(' <div class = "input-group promotion_date"><input type="text" id="save_start_'+id+'"  name="date_start[]" value="'+$('#start_'+id).text()+'"  data-date-format="YYYY-MM-DD" id="input_date_start" class="form-control" /> <span class="input-group-btn">  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>');
        $('#end_'+id).html(' <div class = "input-group promotion_date"><input type="text" id="save_end_'+id+'"  name="date_end[]" value="'+$('#end_'+id).text()+'"  data-date-format="YYYY-MM-DD" id="input_date_end" class="form-control" /> <span class="input-group-btn">  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>');

        $('.promotion_date').datetimepicker({
            pickTime: false
        });

    }

    function updateRow(row,id){
        //分割去除商品ID和仓库ID
        var productInfo = id.split("_");
        var product_id = productInfo[0];
        var warehouse_id = productInfo[1];
        var area_id = productInfo[2];
        var price = $("#save_price_"+id).val();
        var title = $("#save_title_"+id).val();
        var start = $("#save_start_"+id).val();
        var end = $("#save_end_"+id).val();
        var maximum = $("#save_maximum_"+id).val();
        var flag = confirm('确认更改促销信息?');
        if(flag){
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/index_promotion/update&token=<?php echo $_SESSION["token"]; ?>',
                data : {
                    product_id:product_id,
                    warehouse_id:warehouse_id,
                    area_id:area_id,
                    price : price,
                    title : title,
                    start: start,
                    end : end,
                    maximum : maximum,
                },
                dataType: 'json',
                success: function(data){
                    if(data){
                        alert('更新促销成功！');
                        $('#price_'+id).html(price);
                        $('#title_'+id).html(title);
                        $('#start_'+id).html(start);
                        $('#end_'+id).html(end);
                        $('#maximum_'+id).html(maximum);

                        $('#edit_'+id).show();
                        $('#save_'+id).hide();
                    }else{
                        alert('更新促销失败！');
                    }
                },
                error: function(){
                    alert('更新促销失败！');
                }
            });
        }
    }

</script>

