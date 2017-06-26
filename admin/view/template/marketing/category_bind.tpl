<form method="post" enctype="multipart/form-data" id="form-product-bind" class="form-horizontal">
    <div class="alert alert-warning">修改优惠券绑定品类，现有全部绑定品类及排除商品的设定将会被替换。</div>
    <table id="category_add_row"  class="table">
        <thead>
        <tr>
            <td class="text-left" style="width:20%">分类(按Ctrl可多选)</td>
            <td class="text-left" style="width:30%">已选择(可指定排除商品)</td>
            <td class="text-left" style="width:40%">排除的商品</td>
            <td class="text-center">保存</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="text-center" style="vertical-align:top;" >
                <select name="category_id" id="category_id_select" class="form-control" multiple="true" style="height: 200px">
                    <option value="0">－</option>
                    <?php
                    foreach($category_list as $m){
                    echo '<option value="'.$m["category_id"].'">'.$m["name"].'</option>';
                       }
                    ?>
                </select>
                <br />
                <input style="float: right" type="button" value="确定" onclick="fun()" />
            </td>
            <td  style="vertical-align:top" >
                <div id="categories_selected" class="well well-sm" style="overflow: auto;">
                </div>

                <input type="hidden" name="categories" id="input-categories"/>
            </td>
            <td  style="vertical-align:top" >
                <input type="hidden" name="product_deprecated" id="input-products"/>
                <textarea id="product_id_deprecated" style="width:100%; display: none;" memo="指定ID功能暂不开放"></textarea>
                <div id="category_products" class="well well-sm" style="overflow: auto;">
                </div>
            </td>
            <td class="text-center"  style="vertical-align:top" >
                <button type="button" class="btn btn-danger" onclick="saveCategory();">保存</button>
            </td>
        </tr>
        </tbody>
    </table>
</form>

<script type="text/javascript">
    var removedProductList = {};

    function fun(){
        //Reset Remove Product List
        removedProductList = {};

        var select = document.getElementById("category_id_select");
        var str = [];
        var str1 = [];
        var categories = [];
        var html = '';
        var category_id = 0;
        for(var i=0;i<select.length;i++){
            category_id = select[i].value;

            removedProductList[category_id] = [];
            if(select.options[i].selected){
                str.push(category_id);
                html += '<div id="category_id_'+category_id+'" value="'+category_id+'"><i class="fa fa-minus-circle" value="'+category_id+'" onclick="remove(this);"></i>'+select[i].text+' <a href="javascript:showProducts('+category_id+');">[排除商品]</a> <a href="javascript:resetRemoveList('+category_id+');">[重置]</a></div>'
            }
            if(select.options[i].selected){
                categories[select[i].value] = select[i].text;
            }
        }
        $('div[id="categories_selected"]').html(html);
        $('#input-categories').val(str)
    }

    function resetRemoveList(category_id){
        var category_id = parseInt(category_id);

        if(category_id){
            removedProductList[category_id] = [];
        }

        showProducts(category_id);
    }

    function remove(obj){
        var category_id = obj.getAttribute("value");
        $('div').remove('#category_id_'+category_id);
        var str = $('#input-categories').val();
        var strs = str.split(',');
        strs.splice($.inArray(category_id,strs),1);
        $('#input-categories').val(strs);
        $.ajax({
            type: 'GET',
            url: 'index.php?route=marketing/coupon/getCouponCategories&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&category_ids='+strs,
            dataType: 'json',
            success: function(data){
            },
            error: function(){
            }
        });
    }

    function showProducts(category_id){
        //var category_id = obj.getAttribute("value");
        var category_id = parseInt(category_id);

        //Reset Removed Product List
        var html = '';
        var str = [];
        var str1 = [];
        $.ajax({
            type: 'GET',
            url: 'index.php?route=marketing/coupon/getCouponProducts&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&category_id='+category_id,
            dataType: 'json',
            success : function(data){
                for(var i=0;i<data[category_id].length;i++){
                    str.push(data[category_id][i]['product_id']);
                    html += '<div id="product_id_'+data[category_id][i]['product_id']+'" name="'+category_id+'" value="'+data[category_id][i]['product_id']+'"><i class="fa fa-minus-circle remove_icon" value="'+data[category_id][i]['category_id']+','+data[category_id][i]['product_id']+'" onclick="removeProduct(this);"></i> [' + data[category_id][i]['product_id'] + ']' + data[category_id][i]['name'] + '</div>';
                }
//                $('#input-products').val(str);

                $('div[id="category_products"]').html(html);
            },
            error: function(){
                alert(global.returnErrorMsg);
            },
            complete:function(){
                //Apply removed product style
                $.each(removedProductList, function(idx,value){
                    if(removedProductList[idx].length > 0){
                        $.each(value, function(n,v){
                            applyRemovedProductStyle(v);
                        });
                    }
                });
            }
        });
    }

    function applyRemovedProductStyle(product_id){
        $('#product_id_'+product_id).css('text-decoration','line-through');
        $('#product_id_'+product_id).css('font-style','italic');
        $('#product_id_'+product_id).css('color','#333333');
        $('#product_id_'+product_id+' .remove_icon').hide();
    }

    function removeProduct(obj){
        var str_c_p = obj.getAttribute("value");
        var arr = str_c_p.split(',');
        var product_id = arr[1];
        var category_id = arr[0];

        //$('div').remove('#product_id_'+product_id);
        var value = $('#input-products').attr('value');
        if(value == undefined){
            value = '';
            value += str_c_p+';';
        }else{
            value += str_c_p+';';
        }

        if(removedProductList[category_id] == undefined){
            removedProductList[category_id] = [];
        }
        removedProductList[category_id].push(product_id);
        applyRemovedProductStyle(product_id);

        $('#input-products').attr('value',value);
    }

    function getRemovedProductList(list){
        var listToString = '';
        $.each(list, function(idx,value){
            if(list[idx].length > 0){
                $.each(value, function(n,v){
                    listToString += idx + ',' + v + ';';
                });
            }
        });

        return listToString;
    }

    function saveCategory(){
        //如果在此搜索商品id，对应把品类Id以及产品Id结合起来，再次添加到那个隐藏的input框里面
        //var products = $('#product_id_deprecated').val();
        //var products = styleChange(products);
        var products = getRemovedProductList(removedProductList);

        var value = $('#input-products').attr('value');
        if(value == undefined){
            value = '';
        }

        $.ajax({
            type: 'GET',
            async: false,
            url: 'index.php?route=marketing/coupon/getCouponProductToCategory&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&products='+products,
            dataType: 'json',
            success: function(data){
                $.each(data,function(i,v) {
                    value += v['product'];
                });
                $('#input-products').attr('value',value);
            },
        });

        var category_ids = $('#input-categories').val();
        var category_to_products = $('#input-products').val();
        $.ajax({
            type:'POST',
            async:false,
            cache:false,
            url:'index.php?route=marketing/coupon/addCategory&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>',
            dataType: 'json',
            data:{
                category : category_ids,
                products : category_to_products,
            },
            success: function(response){
                if(response.is_success == 'Y'){
                    alert('设置品类成功！');
                }
            }
        });
        $('#categorybind').load('index.php?route=marketing/coupon/categoryBind&token=<?php echo $token; ?>&coupon_id=<?php echo $coupon_id; ?>');
    }
</script>