<form method="post" enctype="multipart/form-data" id="form-area" class="form-horizontal">
<div class="table-responsive">
    <table id="set-sort"  class="table table-bordered table-hover">
        <thead>
        <tr>
            <td class="text-center">商品ID</td>
            <td class="text-center">商品名</td>
            <td class="text-center">仓库名</td>
            <td class="text-center">区域</td>
            <!--<td class="text-center">促销价</td>-->
            <td class="text-center sort">排序</td>
        </tr>
        </thead>
        <tbody>
        <?php if ($histories) { ?>
        <?php foreach ($histories as $history) { ?>
        <tr>
            <td class="text-center"><?php echo $history['product_id']; ?></td>
            <td class="text-center"><?php echo $history['product_name']; ?></td>
            <td class="text-center"><?php echo $history['warehouse_name']; ?></td>
            <td class="text-center"><?php echo $history['area_name']; ?></td>
            <!--<td class="text-center"><?php echo $history['price']; ?></td>-->
            <td id = "<?php echo $history['product_id'].'_'.$history['warehouse_id']; ?>" class="text-center sort"><?php echo $history['priority']; ?></td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
            <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
</form>
<script type="text/javascript" src="view/javascript/jquery/jquery-ui.min.js"></script>
<script type="text/javascript">
    var sortData = {};
    var fixHelperModified = function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width())
                });
                return $helper;
            },
            updateIndex = function(e, ui) {
                $('#reset-sort').show();
                sortData = {} ;
                $('td.sort', ui.item.parent()).each(function (i) {
                    $(this).html(i + 1);
                    //找到仓库中商品的新排序
                    sortData[$(this).attr('id')] = i+1;
                    console.log(sortData);
                });
            };
    $("#set-sort tbody").sortable({
        helper: fixHelperModified,
        stop: updateIndex,
    }).disableSelection();

    function updateSort(){
        $.ajax({
            type: 'POST',
            async: false,
            cache: false,
            url: 'index.php?route=marketing/index_promotion/resetSort&token=<?php echo $_SESSION["token"]; ?>',
            data : sortData,
            dataType: 'json',
            success:function(data){
                if(data){
                    alert("排序成功！");
                }else{
                    alert("排序失败！");
                }
            },
            error:function(){
                alert("排序失败！");
            }
        });
    }

</script>

