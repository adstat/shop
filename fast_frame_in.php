<?php
date_default_timezone_set('Asia/Shanghai');

//var_dump($_COOKIE);
/**
 * Created by PhpStorm.
 * User: jshy
 * Date: 2017/3/13
 * Time: 14:45
 */
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=r.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪仓库篮筐回收</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
    <script>
        var global = {};
        global.warehouseId = '<?php echo $_COOKIE['warehouse_id'];?>';
        global.userId = '<?php echo $_COOKIE['inventory_user_id'];?>';

        global.contaienr_barcode_arr = {};
        global.logistic_driver_id = 0;
    </script>
</head>

<header class="bar bar-nav">
    <div class="title" align="center">
        <input type="button" class="invopt" style="background: red;float: left" id="return_index"  value="返回" onclick="javascript:history.back(-1);"><span id="small_title">订单篮筐回收</span><input class="invopt" style="background: red ; float: right" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();">
    </div>


</header>
<hr>
<body>
<span  id = 'user_id' hidden="hidden"><?php echo $_COOKIE['inventory_user_id'];?></span>
<span  id = 'warehouse_id' hidden="hidden"><?php echo $_COOKIE['warehouse_id'];?></span>


<div id="div_order_search"  align="center" style="height: 100px;">
    物流配送单号:<input id="input_logistic_id" name="input_logistic_id" style="width:12.5em;height: 25px;border:1px solid" onchange="loadOrderBtDriver($(this).val())"/><br />
    已扫描配送单:<input id="input_logistic_allot_id" name="input_logistic_id" disabled="disabled" style=" width:12.5em;height: 25px;border:0px none;"/>
<br /><br />
    篮框:<input id="input_order_id" name="input_order_id" valu="" style="width:12.5em;height: 25px;border:1px solid"/>
</div>


<hr>


<div align="center">

    <form id="form-return">
        <table  border='1'cellspacing="0" cellpadding="0" id="order_table" style="width:100%;">
            <caption id="show_plan_add"></caption>
            <thead>
            <tr style="background:#8fbb6c;">
                <td>确认</td>
                <td>配送单号</td>
                <td>订单号</td>
                <td>框号</td>
                <td>类型</td>
                <td>司机</td>
                <td>状态</td>
            </tr>
            </thead>
            <tbody id="order_logistic_driver">
            </tbody>
        </table>
    </form>
</div>
<div align="center"><input  id="submitDeliverStatus"    style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="confirmFrameInStatus();" value="确认提交"></div>
<!--<div align="center"><input  id="submitDeliverStatus"    style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="submitFrameInStatus();" value="选中框号全部入库"></div> -->
</body>

<script>
    //    $("input[name='input_logistic_id']").keyup(function(){
    //        var tmptxt=$(this).val();
    //
    //        $(this).val(tmptxt.replace(/\D/g,''));
    //        if(tmptxt.length >= 4){
    //            getOrderByDriver(tmptxt);
    //        }
    //
    //    });

    function loadOrderBtDriver(val){
        getOrderByFrame(val);
    }

    $("input[name='input_order_id']").change(function(){
// console.log();
// console.log();

        var tmptxt=$(this).val();
        var boxs_id = [];
        if(tmptxt.length >= 6){
            tmptxt = tmptxt.substr(0,6);
            // console.log($('tr').children().eq(3).html());
            for (var j = 1; j < $('tr').length; j++) {
            boxs_id[j-1] = $('tr').eq(j).children().eq(3).html();
            
            }
            if(isInArray(boxs_id,tmptxt)) {

                if($('#allot_'+tmptxt).html() == '待处理') {
                 $("#allot_"+tmptxt).html('<input type="checkbox" data="'+tmptxt+'" checked="checked">');
                } else {
                    alert('请不要重复处理');
                }
            } else {
              if( confirm('该框不在此单中 确认添加？') ) {
                selectContainer(tmptxt);
            }
            }
                $(this).val('');
                return false;
             
        }else {
            $(this).val('');
        }
    });

    function selectContainer(value) {
        var user_id=$("#user_id").text();
        // alert(user_id);
        var warehouse_id = $("#warehouse_id").text();
        var old_logistic_allot_id = $("#input_logistic_allot_id").val();
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            dataType: 'json',
            data : {
                method : 'selectContainer',
                data : {
                    container_id : value,
                    logistic_allot_id : old_logistic_allot_id,
                    warehouse_id : warehouse_id,
                    user_id : user_id

                }
            },
            success : function (data) {
                // var jsonData = $.parse(data); 
                alert(data.return_msg);
                var type = $('#type').html();
                var drive_title = $('#drive_title').html();
                html = '';
                // console.log(data.return_code == 'SUCCESS');
                if(data.return_code == 'SUCCESS') {
                    html +="<tr style='background:#d0e9c6; height: 30px;' id='check"+value+"'>";
                    html +="<td id='allot_"+value+"' style='width:30px;'><input data="+value+" type='checkbox' checked='checked'></td>";
                    html +="<td id='get_allot_"+value+"'>"+ value+"</td>";
                    html +="<td>0</td>";
                    html +="<td>"+ old_logistic_allot_id+"</td>";
                
                    html +="<td>"+type+"</td>";
                    html +="<td>"+drive_title+"</td>";
                    html += '<td name="pich_id[]" class="pich_id"  id="'+ value +'"  value="'+ value +'" >';
                    if (parseInt('<?php echo $_COOKIE['user_group_id'];?>') == 1) {
                                html +='<input style="background: red ;border-radius: 10px ;width: 100px" type="button" id="button_id'+ value +'"  value="强制回收"  onclick="javascript:confirmFrameInStatus($(this),'+old_logistic_allot_id+')">';

                            
                            html += '未回收</td>';
                        }
                     $('#order_table').append(html);
                }
            }
        });
    }

    function isInArray(arr,value){
    for(var i = 0; i < arr.length; i++){
        if(value === arr[i]){
            return true;
        }
    }
    return false;
}
    
    function getOrderByFrame(tmptxt){
        if (parseInt(tmptxt)>10000) {

        } else {
            alert("配送单号不能为空");
            return true;
        }
        var user_group_id = <?php echo $_COOKIE['user_group_id']?>;
        var user_id=$("#user_id").text();
        var warehouse_id = $("#warehouse_id").text();
        var old_logistic_allot_id = $("#input_logistic_allot_id").val();
        if (old_logistic_allot_id.split(',').length >= 5) {
            alert("最多只能放入四个配送单");return true;
        }
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            dataType: 'json',
            data : {
                method : 'getOrderByFrame',
                data: {
                    logistic_allot_id :tmptxt,
                    user_id :user_id,
                    warehouse_id :warehouse_id,
                    old_logistic_allot_id : old_logistic_allot_id,
                    logistic_driver_id : global.logistic_driver_id
                }
            },
            success : function (response){

                // console.log(response);
                if(response){
                    if (response.return_code == "ERROR") {
                        alert(response.return_msg);
                        $("#input_order_id").val('');
                        return true;
                    }
                    if (response.return_code == "OK") {
                        if (old_logistic_allot_id > 0 ) {
                            $("#input_logistic_allot_id").val(old_logistic_allot_id+','+tmptxt);
                        }
                        $("#input_order_id").val('');
                        getOrderByFrame(tmptxt+','+old_logistic_allot_id);
                        return true;
                    }
                    var html= '';
                    var plan_num = 0;
                    var add_num = 0;
                    var plan_add_num = 0;
                    $.each(response.return_data, function(i, v){
                        global.logistic_driver_id = parseInt(v.logistic_driver_id);
                        if(parseInt(v.order_id) >0 ){
                            plan_num += 1;
                        }
                        global.contaienr_barcode_arr[v.container_id] = v;
                        if(parseInt(v.checked) == 1 ){
                            if(parseInt(v.order_id) >0 ){
                                plan_add_num += 1;
                            }
                            add_num += 1;
                            html +="<tr style='background:#D0E645; height: 30px;' id='check"+v.container_id+"'>";
                             html +="<td id='allot_"+v.container_id+"' style='width:30px;'>已处理</td>";
                        } else {
                            html +="<tr style='background:#d0e9c6; height: 30px;' id='check"+v.container_id+"'>";
                            html +="<td id='allot_"+v.container_id+"' style='width:30px;'>待处理</td>";
                        }
                        // if(v.move_type == 1 ){
                        //     html += "<td>";
                        //     html +='<input  type="checkbox" style="zoom:180%;" type="button" name="pich_id[]" class="pich_id"  id="'+ v.container_id +'"  value="'+ v.container_id +'" >';
                        //     html +="</td>";
                        // }else{
                        //     html += "<td></td>";
                        // }

                        html +="<td id='get_allot_"+v.container_id+"'>"+ v.logistic_allot_id+"</td>";
                        html +="<td>"+ v.order_id+"</td>";
                        html +="<td>"+ v.container_id+"</td>";
                        html +="<td id='type'>"+ v.type_name+"</td>";

                        html +="<td id='drive_title'>"+ v.logistic_driver_title+"</td>";
                        if(parseInt(v.checked) == 1 ){
                            if(user_group_id == 1 && parseInt(v.order_id)== 0) {
                            html +='<td name="pich_id[]" class="pich_id"  id="'+ v.container_id +'"  value="'+ v.container_id +'" >已回收<input style="background: red ;border-radius: 10px ;width: 100px" type="button" id="button_id'+ v.container_id +'"  value="强制删除"  onclick="javascript:annul($(this),'+v.logistic_allot_id+')"></td>';    
                        } else {

                            html +='<td name="pich_id[]" class="pich_id"  id="'+ v.container_id +'"  value="'+ v.container_id +'" >已回收</td>';
                        }
                        }else{
                            html += '<td name="pich_id[]" class="pich_id"  id="'+ v.container_id +'"  value="'+ v.container_id +'" >';
                            if (parseInt('<?php echo $_COOKIE['user_group_id'];?>') == 1) {
                                html +='<input style="background: red ;border-radius: 10px ;width: 100px" type="button" id="button_id'+ v.container_id +'"  value="强制回收"  onclick="javascript:confirmFrameInStatus($(this),'+v.logistic_allot_id+')">';

                            }
                            html += '未回收</td>';
                        }
                        // if(v.move_type ==  1 ){
                        //     html += "<td>";
                        //     html +='<input style="background: red ;border-radius: 10px ;width: 100px" type="button" id="button_id'+ v.container_id +'"  value="确认入库"  onclick="javascript:confirmFrameInStatus(\''+ v.container_id +'\')">';
                        //     html +="</td>";
                        // }else{
                        //     html += "<td></td>";
                        //
                        // }

                        html +="</tr>";
                    });
                    $('#order_logistic_driver').html(html);
                    $('#show_plan_add').html('计划回收篮筐数：'+plan_num+',已回收篮筐数：'+add_num+'(计划内：'+plan_add_num+';计划外：'+(add_num-plan_add_num)+')');
                    $("#input_order_id").val('');
                    $("#input_logistic_allot_id").val(tmptxt);
                }
            }

        });
    }
    
    function annul(value) {
        var user_id=$("#user_id").text();
        var warehouse_id = $("#warehouse_id").text();
        var old_logistic_allot_id = value.parent().parent().find('td').eq(1).html();
        var container_id = value.parent().parent().find('td').eq(3).html();
        // console.log(old_logistic_allot_id,container_id);return false;
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            async : false,//同步加载
            dataType : 'json',
            data : {
               method: 'annul',
                        data: {
                            container_id : container_id,
                            user_id: '<?php echo $_COOKIE["inventory_user_id"];?>',
                            logistic_allot_id:old_logistic_allot_id,
                            warehouse_id :warehouse_id
                        } 
                    },
             success : function (data) {
                if(data.status == 1) {
                    value.parent().parent().remove();
                    alert(data.info);
                } else {
                    alert(data.info);
                }
             },
            error : function () {
                alert('AJAX处理异常');
            }       
        });
    }
    function  confirmFrameInStatus(value,logistic_allot_id){
        var id = [];
        var i = 0;
        
         for (var j = 0; j < $('input').length; j++) {
            if(!$('input').eq(j).prop('checked')) {
                continue;
            }
            id[i] = $('input').eq(j).attr('data');
            i++;
        }
        // alert('111');
        // var container_id = $("#input_logistic_allot_id").val();
        var is_admin = parseInt(logistic_allot_id)>0 ? 1 :0;
        // var logistic_id = parseInt(logistic_allot_id)>0 ? parseInt(logistic_allot_id): parseInt($("#allot_"+value).html());
        // alert(value);
        if(is_admin == 1) {
            id[0]=value.parent().parent().children().eq(3).html();
        }
        if(id == '') {
            alert('没有可以提交的篮筐');
            return false;
        }
       
        var user_id=$("#user_id").text();
        var warehouse_id = $("#warehouse_id").text();
        var old_logistic_allot_id = $("#input_logistic_allot_id").val();
            
                $.ajax({
                    type: 'POST',
                    url: 'invapi.php',
                    dataType: 'json',
                    data: {
                        method: 'confirmFrameInStatus',
                        data: {
                            container_id : id,
                            user_id: '<?php echo $_COOKIE["inventory_user_id"];?>',
                            logistic_allot_id:old_logistic_allot_id,
                            warehouse_id :warehouse_id,
                            is_admin : is_admin
                        }
                    },
                    success: function (response) {
                        // console.log(response);
                        if (response == 1) {
                            for(var m = 0;m < id.length; m++) {
                                str = '';
                                if (parseInt('<?php echo $_COOKIE["user_group_id"];?>')==1) {
                                    var str = '<button style="background: red ;border-radius: 10px ;width: 100px" onclick="annul($(this))">强制删除</button>';

                                }
                             $('#'+id[m]).html('已回收');
                            $("#allot_"+id[m]).html('已处理');
                            $("#allot_"+id[m]).parent().css('background','#D0E645');
                                
                            }
                                
                            $("#input_order_id").val('');
                            // alert(container_id+"回收成功");
                        } else if (response.return_code == "ERROR") {
                            alert(response.return_msg);
                        } else {
                            alert('入库失败');
                        }
                    }


                });
            
    }

    function submitFrameInStatus(){
        var checkbox =$("input[name='pich_id[]']:checked").val([]);
        var user_id=$("#user_id").text();
        var warehouse_id=$("#warehouse_id").text();
        var logistic_id =$("#input_logistic_id").val();

        var  check_value = [];
        for(var i=0;i<checkbox.length;i++){
            check_value.push(checkbox[i].value);
        }

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            dataType: 'json',
            data: {
                method: 'submitFrameInStatus',
                data: {
                    check_value: check_value,
                    user_id: user_id,
                    logistic_id:logistic_id,
                    warehouse_id : warehouse_id ,
                }
            },
            success: function (response) {
                if(response ==3){
                    alert('请选择所需要确认的篮框');
                }
                if(response ==2){
                    getOrderByFrame(logistic_id);
                }
            }


        });



    }


</script>




