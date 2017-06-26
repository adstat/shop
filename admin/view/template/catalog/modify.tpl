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
        <form method="post" enctype="multipart/form-data" id="form-catalog-modify" class="form-horizontal">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <td colspan="3">批量商品ID</td>
                    <td>操作</td>
                </tr>
                <tr>
                    <td colspan="3" style="height:100px">
                        <textarea id="catalog_goods_id" style="width:100%;height:100%"></textarea>
                        <input name="catalog_id" id="goods_id" type="hidden" value="<?php echo $goods_id; ?>" />
                        <input name="catalog_goodsids" id="catalog_goodsids" type="hidden" value=""/>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger" onclick="checkGoods();getGoodsInfo();">检查商品</button>
                    </td>
                </tr>
                <tr>

                    <td colspan="3">商家</td>
                    <td>批量操作</td>
                </tr>
                <tr>

                    <td  colspan="3">
                        <select name="supplier_id" id="input-supplier" class="form-control">
                            <option value="" >-</option>
                            <?php foreach($suppliers as $supplier) { ?>
                            <?php if($supplier['supplier_id'] == $supplier_id) { ?>
                            <option value="<?php echo $supplier['supplier_id']; ?>" selected="selected"><?php echo $supplier['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $supplier['supplier_id']; ?>"><?php echo $supplier['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td colspan = "1" align="left"><button type="button" class="btn btn-danger" onclick="addRows();">批量更改</button></td>
                </tr>
                </thead>
            </table>
        </form>
        <table  id='goodsinfo'  class="table table-bordered table-hover" >
            <thead>
            <tr>
                <td>商家编号</td>
            <td>原料</td>
            <td>原供应商</td>
            </tr>
            </thead>
            <tbody >

            </tbody>
        </table>
    </div>
</div>


<script>
    function styleChange(str){
        //把一列数据变为逗号分隔
        return str.replace(/[\r\n]/g, ',');
    }
    function isEmptyObject(e) {
        var t;
        for (t in e)
            return false;
        return true;
    }
    function checkGoods(){
        var goods = $('#catalog_goods_id').val();

        var goods_ids = styleChange(goods);
        $('#catalog_goods_id').val(goods_ids);
        $('#catalog_goodsids').val(goods_ids);
        $.ajax({
            type: 'GET',
            url: 'index.php?route=catalog/sku/checkGoods&token=<?php echo $_SESSION["token"]; ?>&goods_ids='+goods_ids ,
            dataType: 'json',
            success: function(data){
                if(!isEmptyObject(data['no'])){
                    alert(data['no']+"这些客户不符合要求");
                }
                $('#catalog_goods_id').val(data['yes']);
                $('#catalog_goodsids').val(data['yes']);
            },
            error: function(){
                alert('确认是否填写了商品ID');
            }
        });

    }

    function addRows(){
        var goods = $('#catalog_goods_id').val();
        var goods_ids = styleChange(goods);
        $('#catalog_goods_id').val(goods_ids);
        $('#catalog_goodsids').val(goods_ids);
        $.ajax({
            type:'POST',
            async:false,
            cache:false,
            url: 'index.php?route=catalog/sku/updateGoods&token=<?php echo $_SESSION["token"]; ?>&goods_ids='+goods_ids ,
            data:$('#form-catalog-modify').serialize(),
            success: function(data){
                if(data == 'true'){
                    alert('保存成功');
                    $('#catalog_goods_id').val('');
                }else{
                    alert('保存失败，确认是否检查过商品ID是否正确');
                }
            },

        });
    }

    function getGoodsInfo(){
        var goods = $('#catalog_goods_id').val();
        var goods_ids = styleChange(goods);
        $('#catalog_goods_id').val(goods_ids);
        $('#catalog_goodsids').val(goods_ids);
        $.ajax({
            type: 'GET',
            url: 'index.php?route=catalog/sku/getGoodsInfo&token=<?php echo $_SESSION["token"]; ?>&goods_ids='+goods_ids ,
            dataType: 'json',
            success: function(data){
                $('#goodsinfo tbody tr').remove();
                html = '';
               $.each(data ,function(key,val) {
                   $.each(val ,function(key,v){
                       html += '<tr>';
                       html += '<td>' + v.product_id + '</td>';
                       html += '<td>' + v.sku_name + '</td>';
                       html += '<td>' + v.supplier_name + '</td>';
                       html += '</tr>';

                   });
               });
                $('#goodsinfo tbody ').append(html);

            },
            error: function(){
                alert('报错');
            }
        });
    }
</script>



<script type="text/javascript">
    $('.date').datetimepicker({
        pickTime: false
    });
</script>