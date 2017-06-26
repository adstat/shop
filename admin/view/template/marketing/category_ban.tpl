<form method="post" enctype="multipart/form-data" id="form-category-ban" class="form-horizontal">
    <div class="alert alert-warning">修改优惠券绑定品类，现有全部绑定品类及排除商品的设定将会被替换。</div>
    <table id="category_ban_row"  class="table">
        <thead>
        <tr>
            <td class="text-left" style="width:40%">分类(按Ctrl可多选)</td>
            <td class="text-left" style="width:40%">已选择(可去掉之前勾选的分类)</td>
            <td class="text-center">保存</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="text-center" style="vertical-align:top;" >
                <select name="category_ban_id" id="category_id_ban" class="form-control" onchange="banCategory();" multiple="true" style="height: 300px">
                    <option value="0">－</option>
                    <?php
                    foreach($category_list as $m){
                    echo '<option value="'.$m["category_id"].'">'.$m["name"].'</option>';
                    }
                    ?>
                </select>
                <br />
            </td>
            <td  style="vertical-align:top" >
                <div id="categories_banned" class="well well-sm" style="overflow: auto;">
                </div>

                <input type="hidden" name="categories" id="input-categories-banned"/>
            </td>
            <td class="text-center"  style="vertical-align:top" >
                <button type="button" class="btn btn-danger" onclick="saveCategoryBanned();">保存</button>
            </td>
        </tr>
        </tbody>
    </table>
</form>

<script type="text/javascript">
    var removedCategoriesList = [];
    var bindCategoriesList = [];
    var CategoriesListToSave = [];
    getPreCategoriesBanned();
//    getPreCategoriesBinded();
    function getPreCategoriesBanned(){
        var html = '';
        $.ajax({
            type: 'GET',
            async: false,
            url: 'index.php?route=marketing/coupon/getPreCategoriesBanned&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>',
            dataType: 'json',
            success: function(data){
                $.each(data,function(i,v){
                    removedCategoriesList.push(v.category_id);
                    html += '<div id="category_id_ban_'+ v.category_id+'" value="'+v.category_id+'"><i class="fa fa-minus-circle" value="'+v.category_id+'" onclick="removeBan(this);"></i>'+v.name+'</div>'
                });
            },
        });
        $('div[id="categories_banned"]').html(html);
    }

//    function getPreCategoriesBinded(){
//        $.ajax({
//            type: 'GET',
//            async: false,
//            url: 'index.php?route=marketing/coupon/getPreCategoriesBinded&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>',
//            dataType: 'json',
//            success: function(data){
//                $.each(data,function(i,v){
//                    bindCategoriesList.push(v.category_id);
//                });
//            },
//        });
//    }

    function banCategory(){

        var newRemovedCategoriesList = [];
        var conflictCategoriesList = [];

        var select = document.getElementById("category_id_ban");
        var categories = [];
        var html = '';
        var category_id = 0;
        for(var i=0;i<select.length;i++){
            category_id = select[i].value;
            if(select.options[i].selected) {
                newRemovedCategoriesList.push(category_id);
            }
        }

        //对比newRemovedCategoriesListd是否与removedCategoriesList有重合分类id，有，去掉。
        for(var i=0;i<select.length;i++){
            category_id = select[i].value;
            if(select.options[i].selected){
//                if(removedCategoriesList.indexOf(select[i].value) == -1 && bindCategoriesList.indexOf(select[i].value) == -1){
                if(removedCategoriesList.indexOf(select[i].value) == -1){
                        removedCategoriesList.push(category_id);
                    CategoriesListToSave.push(category_id);
                    html += '<div id="category_id_ban_'+category_id+'" value="'+category_id+'"><i class="fa fa-minus-circle" value="'+category_id+'" onclick="removeBan(this);"></i>'+select[i].text+'</div>';
                }else{
                    conflictCategoriesList.push(select[i].text);
                }
            }
        }
        if(conflictCategoriesList.length>0){
            alert(conflictCategoriesList+"为冲突选项选项！");
        }
        $('div[id="categories_banned"]').append(html);
        $('#input-categories-banned').val(removedCategoriesList)
    }

    function removeBan(obj){
        var category_id = obj.getAttribute("value");
        $('div').remove('#category_id_ban_'+category_id);
        var str = removedCategoriesList;
        str.splice($.inArray(category_id,str),1);
        removedCategoriesList = str
        $('#input-categories-banned').val(removedCategoriesList);
    }

    function saveCategoryBanned(){
        var categories_ids = $('#input-categories-banned').val();
        $.ajax({
            type: 'POST',
            async: false,
            url: 'index.php?route=marketing/coupon/banCategory&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>',
            dataType: 'json',
            data:{
                categories_ids : categories_ids,
            },
            success: function(response){
                if(response){
                    alert('排除品类成功！');
                }
            }
        });
        $('#categoryban').load('index.php?route=marketing/coupon/categoryBan&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');

    }
</script>