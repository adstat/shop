$(document).ready(function(){
    $('#list').hide();
    $('#begin_storage').hide();
});

/*
    * 生成商品详情栏，可进行加减操作
    *
    *table_id         表名id
    *tbody_id         tbody栏id
    *product_name     商品名栏id
    *plan_quantity    计划数量栏id
    *quantity         实际数量栏id
    *operation        操作栏id
    *prodId           实际操作数量的id
    *planId           计划数量的id
    *
    * */
function make_product_information(id,text1,text2,quantity,text3,real_quantity,text4,table_id,tbody_id,product_text){
    var product_type = global.order_detail_information.product_type;
    var table_id = "#"+table_id;
    var tbody_id = "#"+tbody_id;
    var product_name_id = "product_name1"+id;
    var plan_quantity_id = "current_product_plan1"+id;
    var quantity_id = "current_product_quantity1"+id;
    var operation_id = "current_product_quantity_change1"+id;
    var prodId = id;
    var planId = "sdpid"+id;
    var display1 = '';
    var display2 = '';
    var display3 = '';
    if (text1 == 0) {
        display1 = "none";
    }
    if (text2 == 0) {
        display2 = "none";
    }
    if (text3 == 0) {
        display3 = "none";
    }
    if (text4 == 0) {
        text4 = "提交";
    }
    $(tbody_id).html('<tr id="clear' + id + '"><input type="hidden" id="get_product_id" value="' +
        id + '"/><td colspan="3" id="'+product_name_id+'" align="center" style="font-size:1.4em;display:'+display1+';"></td></tr><tr id="clearss' +
        id + '"><th style="width:4em;display:'+display2+';">'+text2+'</th><th style="width:4em;display:'+display3+';">'+text3+'</th><th align="center" id ="manysubmits"><button style="float:left" class="invopt manysubmits" onclick="javascript:tjStationPlanProduct(' +
        id +',\''+product_text+'\');">'+text4+'</button></th></tr><tr id="clears'+
        id + '"><td id="'+
        plan_quantity_id+'" align="center" style="font-size:1.5em;display:'+display2+';"></td> <td id="'+
        quantity_id+'" align="center" style="font-size:1.4em;display:'+display3+';"></td> <td id="'+
        operation_id+'"></td></tr>');
    if (product_type == 1) {
        $("#"+product_name_id).html(text1);
    } else if (product_type == 2) {
        $("#"+product_name_id).html('当前周转筐：'+window.local_container+'<br />'+text1);
    }
    $("#"+plan_quantity_id).html(quantity + '<span style="display:none;" name="productId" id="'+planId+'">' + quantity + '</span>');
    $("#"+quantity_id).html('<input class="qty"  id="'+prodId+'" value="' + real_quantity + '">');
    $("#"+operation_id).html('<input style="height:3em;width:3em;" class="qtyopt" type="button" value="-" onclick="javascript:qtyminus(\'' +
        id + '\',1,1,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+" onclick="javascript:qtyminus(\'' +
        id + '\',2,1,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+10" onclick="javascript:qtyminus(\'' +
        id + '\',2,10,\''+prodId+'\',\''+planId+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="+50" onclick="javascript:qtyminus(\'' +
        id + '\',2,50,\''+prodId+'\',\''+planId+'\')">');
    $(table_id).show();

}
/*
* 商品数量操作
*id          商品id
*status      1为减，2为加
*num         需要操作的数量
*prodId      操作数量的id
*planId      计划数量id(货位有多少数量)
* */
function qtyminus(id,status,num,prodId,planId){
    var prodId = "#"+prodId;
    var planId = "#"+planId;
    if (status == 1) {
        if($(prodId).val() >= num){
            var qty = parseInt($(prodId).val()) - num;
            $(prodId).val(qty);
        }
    } else if (status == 2) {
        // if ($(planId).text() > 0) {
        //     if ($(prodId).val() <= $(planId).text() -num) {
        //         var qty = parseInt($(prodId).val()) + num;
        //         $(prodId).val(qty);
        //     }
        // } else {
        var qty = parseInt($(prodId).val()) + num;
        $(prodId).val(qty);
        // }


    }
}




//modal弹出方法中要展示的一条数据
function getTableName(tableName,id,findId){
    $.post('put_away_dispose.php/find',{tableName:tableName,id:id,findId:findId,flag:'4'},function (data) {
        alert(data);
    });
}


