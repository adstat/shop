<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>仓库出库单</h1>
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
            <div class="panel-body">
                <div class="col-sm-3">
                    <div class="form-group">
                        <input type="text" name="products" id="product_id"  value="" placeholder="商品ID" class="form-control"  />
                    </div>
                    <div class="form-group">
                        <select name="out_type" id="input-out_type" class="form-control"  onchange="changeOutType(this.value);">
                            <option value="">选择出库类型</option>
                            <option value="1">出库单</option>
                            <option value="2">仓间调拨单</option>
                        </select>

                    </div>
                </div>

            <div class="table-responsive">

            </div>
                <div class="col-sm-3"  id='div_warehouse_id' style="display: none">
                    <div class="form-group">
                        <select name="warehouse_id" id="input-warehouse_id" class="form-control" >
                            <option value="">选择需要调往的仓库</option>
                            <?php foreach ($warehouse as $val) { ?>
                            <?php if ($val['warehouse_id'] == $warehouse_id) { ?>
                            <option value="<?php echo $val['warehouse_id']; ?>" selected="selected"><?php echo $val['title']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $val['warehouse_id']; ?>" ><?php echo $val['title']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3"  id='div_reason' style="display: none">
                    <div class="form-group">
                        <input type="text" name="reasons" id="reason_id"  value="" placeholder="出库理由" class="form-control"  />
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="input-group date">
                            <input type="text"  data-date-format="YYYY-MM-DD" placeholder="预计出库日期" value="" id="search_date" class="form-control"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" id="button-filter" class="btn btn-primary pull-right" onclick="getProductInfo()"><i class="fa fa-search"  ></i> <?php echo $button_filter; ?></button>
                    </div>
                </div>

                <form action="" method="post" enctype="multipart/form-data" id="form-product-adjust" class="form-horizontal">
                    <table id="adjust" class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <td class="text-center" width="75px">商品ID</td>
                            <td class="text-center" width="70px">商品名称</td>
                            <td class="text-center" width="70px">货位号</td>
                            <td class="text-center" width="70px">仓库数量</td>
                            <td class="text-center" width="90px">基础价格</td>
                            <td class="text-center" width="90px">调拨数量</td>
                            <td class="text-center" width="90px">操作</td>
                        </tr>
                        </thead>
                        <tbody id="product_info">

                        </tbody>
                    </table>
                </form>
                <div style="float:left;" class="col-sm-4"><button type="button" class="btn btn-primary" onclick="submitAdjust();">确认添加</button></div>
        </div>

    </div>
</div>
</div>
<script type="text/javascript">
    $('.date').datetimepicker({
        pickTime: false
    });
    $('input[name^=\'selected\']').on('change', function() {
        $('#button-shipping').prop('disabled', false);
        var selected = $('input[name^=\'selected\']:checked');


    });
</script>
<script type="text/javascript">



    $('input[name=\'products\']').autocomplete({
        'source': function(request, response) {
            var warehouse_id = $("#warehouse_id_global").val();
            $.ajax({
                type: 'POST',
                url: 'index.php?route=purchase/warehouse_allocation_note/autocomplete&token=<?php echo $token; ?>&products=' +  encodeURIComponent(request),
                dataType: 'json',
                data:{
                    warehouse_id :warehouse_id,
                },
                success: function(json) {
                    console.log(json);
                    response($.map(json, function(item) {
                        return {
                            label: item['fix'],
                            //   label: item['name'],
                            value: item['product_id']
                        }
                    }));
                }
            });
        },
        'select': function(item) {
            $('input[name=\'products\']').val(item['value']);
        }
    });


     function getProductInfo(){
         var product_id = $("#product_id").val();
         var warehouse_id = $("#warehouse_id_global").val();

         $.ajax({
             type: 'POST',
             url: 'index.php?route=purchase/warehouse_allocation_note/getProductInfo&token=<?php echo $token; ?>',
             dataType: 'json',
             data:{
                 product_id :product_id,
                 warehouse_id:warehouse_id,
             },
             success:function(response){
                if(response){
                    var html ='';
                    $.each(response, function (i, v) {
                        html += "<tr style='border: 1px;' id='" + v.product_id + "'>";
                        html += "<td  id='id" + v.product_id + "'>" + "<span style='font-weight:bold'>" + v.product_id + "</span>" ;
                        html += "</td>";
                        html += "<td id='name" + v.product_id + "' >" + v.name + "</td>";
                        html += "<td id='position" + v.product_id + "' >" + v.product_section_title + "</td>";
                        html += "<td id='inventory" + v.product_id + "' >" + v.inventory + "</td>";
                        html += "<td id='price" + v.product_id + "' >" + v.price + "</td>";
                        html += "<td   >" ;
                        html +="<input id='to_num" + v.product_id + "' type='text'  value='' name='to_num' >";
                        html +="</td>";
                        html += "<td>";
                        html +="<input  type='button' value='取消'  onclick='remove("+ v.product_id +")'>";
                        html += "</td>";
                        html += "</tr>";
                    });
                    $('#product_info').append(html);
                    var warehouse_product_id = $("#product_id").val("");
                }
             }
         });
    }

    function submitAdjust(){
        var data = $("#product_info").find("tr");
        var from_warehouse_id = $("#warehouse_id_global").val();
        var to_warehouse_id = $("#input-warehouse_id").val();
        var deliver_date = $("#search_date").val();
        var reasons = $("#reason_id").val();
        var postData = [] ;
        var productArray = [];

        $.each(data,function(i,v){
            productArray.push(v.id) ;
            productArray.push(v.childNodes[0].innerText) ;
            productArray.push(v.childNodes[1].innerText) ;
            productArray.push(v.childNodes[2].innerText) ;
            productArray.push(v.childNodes[3].innerText) ;
            productArray.push(v.childNodes[4].innerText) ;
            productArray.push(document.getElementById("to_num"+ v.id).value);
            postData.push(productArray);
            productArray = [];
        });
       var out_type =  $("#input-out_type").val();
        var  outtype = $("#input-out_type").find("option:selected").text();

        if(out_type == 0){
            alert('请选择出库类型');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: 'index.php?route=purchase/warehouse_allocation_note/submitAdjust&token=<?php echo $token; ?>',
            dataType: 'json',
            data: {
                reasons : reasons,
                outtype:outtype,
                products: postData,
                from_warehouse_id :from_warehouse_id,
                to_warehouse_id :to_warehouse_id,
                deliver_date :deliver_date,
            },
            success:function(response){
                console.log(response);
                if(response ==1){
                    alert('添加成功');
                }
            }
        });


    }

    function remove(product_id){
        $("#"+product_id).remove();
    }
    function changeOutType(outtype){
        if(outtype ==1){
            $("#div_warehouse_id").hide();
            $("#div_reason").show();
        }else if(outtype ==2){
            $("#div_warehouse_id").show();
            $("#div_reason").hide();
        }else{
            $("#div_warehouse_id").hide();
            $("#div_reason").hide();
        }
    }


</script>
<?php echo $footer; ?> /