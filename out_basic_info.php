<?php
/**
 * Created by PhpStorm.
 * User: jshy
 * Date: 2017/3/13
 * Time: 14:45
 */
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪仓库分拣缺货确认表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>


    <link rel="stylesheet"  type="text/css"  href="view/javascript/jquery/datetimepicker/bootstrap.min.css">
    <script type="text/javascript" src="view/javascript/jquery/datetimepicker/bootstrap.min.js"></script>

    <script type="text/javascript" src="view/javascript/jquery/datetimepicker/moment.js"></script>
    <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>

    <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<!--    <link rel="stylesheet" type="text/css" href="view/css/i.css"/>-->

</head>

<header class="bar bar-nav">
    <div class="title"> <input type="button" class="invopt" style="background: red" id="return_index"  value="返回" onclick="javascript:history.back(-1);"><span id="small_title">出库基础信息录入</span><input class="invopt" style="background: red" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();"></div>

</header>
<hr>
<body>
<span  id = 'user_id' hidden="hidden"><?php echo $_COOKIE['inventory_user'];?></span>
<div id="inv_comments">
    <div align="right"><?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <span onclick="javascript:logout_inventory_user();">退出</span></div>
    <div  style="display: none" id="inventory_user_id"> <?php echo $_COOKIE['inventory_user_id'];?> </div>
    <div  style="display: none" id="warehouse_id"> <?php echo $_COOKIE['warehouse_id'];?> </div>

    <div id="div_order_search"  align="center" style="height: 60px;font-size: 15px">
        订单查询:<input id="input_order_id" name="input_order_id" style="width:12.5em;height: 25px;border:1px solid">
    </div>
    <div align="center" style="height: 30px;">
        <input type="button"   style=" width:150px;font-size:1.5em; background: red; " onclick="javascript:getOutBasicInfo()" value="出库订单基础信息">

    </div>
    <hr>


</div>
<div>
    <div id="order_info">订单信息：</div>

    <div>[货位号为3以下的数字]</div>
    <hr>
    <div>筐数：<span id="frame_num"></span></div>
    <div style="width:100% ;height: 50px ;">
        筐号:<span id="frame_id" style="width: 100px"></span>
    </div>
    <div style="font-size: 0.5rem;background-color: red">没提交的筐号可以在此扫描显示在文本框中，经过核实之后提交周转筐</div>
    <div>
        <span>周转筐数量</span>
        <input type="text" id="frame_count" class="fm_frame_count" value="0" readonly="readonly" />
    </div>
    <span>周转筐扫描</span>
    <input type="hidden" id="frame_vg_list" class="fm_frame_vg_list" />
    <input style="font-size: 1.2em; width:90%;" id="input_vg_frame" name="input_vg_frame" />
    <div id="vg_list" class="fm_vg_list"></div>
    <hr>
    <div>
        <span>纸箱数量</span>
        <input type="text" id="carton_count" class="fm_frame_carton_count" value="0" readonly="readonly" />
    </div>
    <span>纸箱临时条码</span>
    <input type="hidden" id="frame_carton_list" class="fm_frame_carton_list" />
    <input style="font-size: 1.2em; width:90%;" id="input_carton_frame" name="input_carton_frame" />
    <div id="carton_list" class="fm_carton_list"></div>






    <div id ="check_reason">

    </div>






    <div>
        <span>周转筐差额</span>
        <input type="text" id="frame_margin" class="fm_frame_counts" name = "frame_margin">
    </div>

    <input  id="correction_location_order"   style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="submitCorrectionOutOrder();" value="核实" >
</div>
</body>

<script>
    function getOutBasicInfo(){
        var order_id = $("#input_order_id").val();

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            dataType: 'json',
            data : {
                method : 'getCheckReason',

            },
            success:function(response){
                if(response){
                    var html ="";
                    $.each(response, function(i, v){
                        html += '<div>';
                        html +=" <input type='checkbox' name='feadcheckbox[]' value='"+ v.check_location_reason_id+"'  id='"+ v.check_location_reason_id+"' >"+ v.reason_name;
                        html += '</div>';
                    });
                    $("#check_reason").html(html);
                }
            }
        });



        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            dataType: 'json',
            data : {
                method : 'getCheckOrdersInfo',
                data: {
                    order_id: order_id
                }

            },
            success : function (response , status , xhr){

                if(response){
                    var html = "";
                    $.each(response, function(i, v){
                        html += "<span style='font-size: 13px;'>订单号:"+ v.order_id+"</span>";
                        if(v.order_status_id == 6){
                            html +="<span style='font-size: 13px;' >，已拣完</span>";
                        }else{
                            html +="<span style='font-size: 13px;'>，待审核</span>";
                        }
                        if(v.check_status == 0 ){
                            html +="<span style='font-size: 13px;'>，待核查</span>";
                        }else {
                            html +="<span style='font-size: 13px;'>，已核查</span>";
                        }
                        html +="<div >货位号:"+"<input id='inv_comment' style='width:8em;height: 25px;border:1px solid' value='"+v.inv_comment +"'>" +"</div>";
                    });

                    $('#order_info').html(html);
                }
            }
        });

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            dataType: 'json',
            data: {
                method: 'getContainer',
                data: {
                    order_id: order_id
                }

            },

            success: function(response) {
                console.log(response.container);
                if(response){
                    var html = '';
                    html +="<span>"+ response.container['1'].frame_count+"</span>";
                    $('#frame_num').html(html);
                }
                if(response){
                    var html = '';
                    html +="<span>"+ response.container[0].frame_vg_list+"</span>";


                    $('#frame_id').html(html);
                }
            }
        });

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            dataType: 'json',
            data: {
                method: 'getLocationOrderInfo',
                data: {
                    order_id: order_id
                }

            },
            success: function(response) {

                if(response){
                    var html='';
                    $.each(response[1], function(i, v){
                        html += "<span style='font-size: 12px;'>订单总数："+ v.order_quantity +"</span>";
                        html +="<span>&nbsp;&nbsp;&nbsp;</span>"
                        html += "<span  style='font-size: 12px;'>分拣总数："+v.oty_quantity + "</span>";
                    })

                    $("#sum_num").html(html);
                }

                if(response){
                    var html ='';
                    $.each(response[0], function(i, v){
                        html +="<tr>";
                        html +="<td>"+ v.station_section_title+'/'+'['+v.product_id+']';
                        html +="<div>"+ v.name+"</div>";
                        html +="</td>";
                        html +="<td>"+ v. order_quantity+"</td>";
                        html +="<td>"+ v.oty_quantity+"</td>";
                        html +="</tr>";

                    });
                    $("#location_verifi_order").html(html);
                }
            }
        });
    }

</script>

<script>
    $("input[name='input_vg_frame']").keyup(function(){
        var tmptxt=$(this).val();
        //$(this).val(tmptxt.replace(/\D|^0/g,''));
        if(tmptxt.length >= 6){
            var frameContainerNumber =  tmptxt.substr(0,6);
            if(!/^[1-9]\d{0,5}$/.test(frameContainerNumber.trim())){
                alert("请输入数字格式周转筐号");
                $(this).val("");
                return false;
            }

            handleFrameList('vg');
            $(this).val("");
        }
    });


    $("input[name='input_carton_frame']").keyup(function(){
        var tmptxt=$(this).val();
        //$(this).val(tmptxt.replace(/\D|^0/g,''));
        if(tmptxt.length >= 6){
            var frameContainerNumber =  tmptxt.substr(0,6);
            if(!/^[1-9]\d{0,5}$/.test(frameContainerNumber.trim())){
                alert("请输入数字格式周转筐号");
                $(this).val("");
                return false;
            }

            handleFrameCartonList('carton');
            $(this).val("");
        }
    });


    function  handleFrameCartonList(frame_type){
        var frame_num = $("#input_"+frame_type+"_frame").val();
        frame_num = frame_num.substr(0,6);//Get 18 code

        var frame_vg_list = $("#frame_"+frame_type+"_list").val();
        if(frame_vg_list == ""){
            $("#frame_"+frame_type+"_list").val(frame_num);
        }
        else{
            if(frame_vg_list.indexOf(frame_num) != -1 )
            {
                alert('不能重复扫描同一个框子');
                return false;
            }
            $("#frame_"+frame_type+"_list").val(frame_vg_list+','+frame_num);
        }
        var frame_num_html = '<div id="frame_'+frame_type+'_'+frame_num+'">'+frame_num+' <span style="background-color: red" class="frame_num" onclick="remove_frame(\''+frame_type+'\','+frame_num+' );">X</span></div>';
        $("#"+frame_type+"_list").append(frame_num_html);

        applyFrameCartonCount();
    }

    function applyFrameCartonCount(){
        $(".fm_frame_carton_count").val($("#carton_list div").length);
    }



    function handleFrameList(frame_type){
        var frame_num = $("#input_"+frame_type+"_frame").val();
        frame_num = frame_num.substr(0,6);//Get 18 code

        var frame_vg_list = $("#frame_"+frame_type+"_list").val();
        if(frame_vg_list == ""){
            $("#frame_"+frame_type+"_list").val(frame_num);
        }
        else{
            if(frame_vg_list.indexOf(frame_num) != -1 )
            {
                alert('不能重复扫描同一个框子');
                return false;
            }
            $("#frame_"+frame_type+"_list").val(frame_vg_list+','+frame_num);
        }
        var frame_num_html = '<div id="frame_'+frame_type+'_'+frame_num+'">'+frame_num+' <span style="background-color: red" class="frame_num" onclick="remove_frame(\''+frame_type+'\','+frame_num+' );">X</span></div>';
        $("#"+frame_type+"_list").append(frame_num_html);

        applyFrameCount();
    }


    function applyFrameCount(){
        $(".fm_frame_count").val($("#vg_list div").length);
    }
    function remove_frame(frame_type,frame_num) {
        $("#frame_" + frame_type + "_" + frame_num).remove();
        applyFrameCount();
        var frame_vg_list = $("#frame_"+frame_type+"_list").val();
        if(frame_vg_list.indexOf(frame_num) != -1 ){
            if(frame_vg_list.indexOf(','+frame_num) != -1){

                frame_vg_list=frame_vg_list.replace(','+frame_num,'' );
            }
            else if(frame_vg_list.indexOf(frame_num+',') != -1){

                frame_vg_list=frame_vg_list.replace(frame_num+',','' );
            }
            else{

                frame_vg_list=frame_vg_list.replace(frame_num,'' );
            }

            $("#frame_"+frame_type+"_list").val(frame_vg_list);

        }
        else{
            alert("数据错误，请刷新页面重新提交框号数据或联系管理员");
        }

    }

</script>
<script>
    function  submitCorrectionOutOrder(){

        var order_id = $("#input_order_id").val();

        if(order_id == ''){
            alert('请输入订单号');
            return false;
        }

        var frame_count = $("#frame_count").val();
        var frames_ids = $("#frame_vg_list").val();

        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
            }
        }
        var feadbackcheckbox =$("input[name='feadcheckbox[]']:checked").val([]);
        var  check_value = [];
        for(var i=0;i<feadbackcheckbox.length;i++){
            check_value.push(feadbackcheckbox[i].value);
        }


        var container_id  =$(".container_id").text();
        var productArray = [];
        $("span[class='container_id']").each(function(){
            var value = $(this).text();
            productArray.push(value) ;
        });
        var container_ids = productArray;
        var user_id=$("#user_id").text();

        var inv_comment = $("#inv_comment").val();

        var frames = $("#frame_id").text()

        var frame_margin = $("#frame_margin").val();

        var frame_carton_list = $("#frame_carton_list").val();

        var frame_carton_count = $("#carton_count").val();

        if(confirm("请核实好信息")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'submitCorrectionOutOrder',
                    data: {
                        frame_count:frame_count,
                        frames_ids:frames_ids,
                        user_id:user_id,
                        inv_comment: inv_comment,
                        order_id: order_id,
                        check_value :check_value,
                        frames:frames,
                        frame_margin : frame_margin,
                        frame_carton_list:frame_carton_list,
                        frame_carton_count:frame_carton_count,

                    }
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if (jsonData.status == 2 || jsonData.status == 4) {
                        $("#frame_carton_list").val('');
                        $("#carton_count").val('');
                        alert('提交成功！');
                        location.reload();
                    }

                }
            });
        }
    }



</script>

