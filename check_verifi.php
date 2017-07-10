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
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪仓库分拣缺货确认表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>

    <link rel="stylesheet" type="text/css" href="view/css/i.css"/>

</head>

<header class="bar bar-nav">
    <div class="title"> <input type="button" class="invopt" style="background: red" id="return_index"  value="返回" onclick="javascript:window.close();"><span id="small_title">货位核查</span><input class="invopt" style="background: red" value="刷新" type="button" id="return_index1" onclick="javascript:location.reload();"></div>

</header>
<hr>
<body>
    <div id="order_info">订单信息：</div>

<div>[货位号为3以下的数字]</div>
    <hr>
    <div>筐数：<span id="frame_num"></span></div>
    <div style="width:100% ;height: 50px ;">
        <div> 筐号：<input id='bar_code' name="bar_code" style='width:10em;height: 25px;border:1px solid' placeholder="扫描周转筐 核实\更正"> </div>
        <div    style="width:100% ; margin-left: 80px;">       <span id="frame_id" style="width: 100px"></span></div>
    </div>
    <input  id="un_location_order"   style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="submitUnLocationOrder();" value="未找到订单" >
    <input  id="correction_location_order"   style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="submitCorrectionLocationOrder();" value="核实/更正" >
<hr>
    <span  id = 'user_id' hidden="hidden"><?php echo $_COOKIE['inventory_user_id'];?></span>

<div ><span style="width:300px;font-size: 12px " >订单整件列表</span> <span id ="sum_num"></span></div>
    <div align="center">
    <form id="location_verifi" >
        <table  border='1'cellspacing="0" cellpadding="0" id="order_table3" style="width: 100%">
            <thead>
            <tr style="background:#8fbb6c;">
                <td>商品名称</td>
                <td>订单数量</td>
                <td>分拣数量</td>
            </tr>
            </thead>
            <tbody id="location_verifi_order">

            </tbody>

        </table>
    </form>
        <input  id="check_location_order"   style=" margin-top: 10px; width: 100px;height: 30px;background: red" type="button" onclick="submitCheckLocationOrder();" value="核查分拣商品" >
    </div>


</body>




</html>
<script type="text/javascript">
//    getCheckInfo();
//    getContainer();
//    getLocationOrderInfo();
      $(document).ready(function(){
              var url = location.search;
              var theRequest = new Object();
              if (url.indexOf("?") != -1) {
                  var str = url.substr(1);
                  strs = str.split("&");
                  for(var i = 0; i < strs.length; i ++) {
                      theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
                  }
              }

              var  order_id = (theRequest['order_id']);

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
                  if(response){
                      var html = '';
                      html +="<span>"+ response.container[1].num+"</span>";
                      $('#frame_num').html(html);
                  }
                  if(response){
                      var html = '';

                      $.each(response.container[0], function(i, v){

                          html +="<span style=' background: red' id ='"+v.container_id +"'>"+v.container_id  +"</span>";
                          html +="&nbsp;&nbsp;&nbsp;";
                      });

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



      });

</script>
<script>
    $("input[name='bar_code']").keyup(function(){
        var tmptxt=$(this).val();

        $(this).val(tmptxt.replace(/\D/g,''));
        getContainerInfo();
//        if(tmptxt.length >= 4){
//            getContainerInfo();
//
//        }

    }).bind("paste",function(){
        var tmptxt=$(this).val();
        $(this).val(tmptxt.replace(/\D/g,''));
    });
</script>
<script>
    function  getContainerInfo(){
        var rawId = $('#bar_code').val();
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
            }
        }
        var container_id = rawId;
        var  order_id = (theRequest['order_id']);

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getContainerInfo',
                data: {
                    container_id: container_id,
                    order_id: order_id,
                }
            },
            success: function(response) {
                var jsonData = $.parseJSON(response);

                if(jsonData.status ==1){
                    alert('该订单没有此周转箱筐号');
                }else {
                    $("#" + container_id).css("background-color", "#d0e9c6");
                    $("#"+container_id).addClass('container_id');


                }
            }

        });
    }

    function  submitCorrectionLocationOrder(){
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
            }
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
        var order_id = (theRequest['order_id']);
        if(confirm("请核实好信息")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'submitCorrectionLocationOrder',
                    data: {
                        user_id:user_id,
                        inv_comment: inv_comment,
                        order_id: order_id,
                        container_ids: container_ids,

                    }
                },
                success: function (response) {


                    var jsonData = $.parseJSON(response);

                    if (jsonData.status == 1) {
                        alert('提交成功！提交原因：周转筐数量不对');
                    }
                    if (jsonData.status == 2) {
                        alert('提交成功！提交原因：该订单的货位号不对');
                    }
                    if (jsonData.status == 3) {
                        alert('提交成功！提交原因：周转筐，货位号都不对');
                    }
                    if (jsonData.status == 4) {
                        alert('提交成功！提交原因：周转筐，货位号核实正确');
                    }
                    if(jsonData[0] == 5){
                        alert('提交失败！此前已经提交一次,提交原因:'+jsonData[1]);

                    }
                }
            });
        }
    }
    function submitUnLocationOrder(){
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
            }
        }
        var user_id=$("#user_id").text();
        var inv_comment = $("#inv_comment").val();
        var order_id = (theRequest['order_id']);
        if(confirm("确认该货位上没有订单？确认前请将货位号改成‘999’")) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'submitUnLocationOrder',
                    data: {
                        user_id:user_id,
                        inv_comment: inv_comment,
                        order_id: order_id,
                    }
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if(jsonData == 1){
                        alert('提交失败，未找到订单请将货位号改成999');
                    }else if(jsonData.status == 2){
                        alert("提交成功！提交原因：该货位号上没有找到此订单");
                    }else if(jsonData[0] == 3){

                        alert('提交失败！此前已经提交一次,提交原因:'+jsonData[1]);

                    }
                }
            });
        }
    }
    function submitCheckLocationOrder(){
        var url = location.search;
        var theRequest = new Object();
        if (url.indexOf("?") != -1) {
            var str = url.substr(1);
            strs = str.split("&");
            for(var i = 0; i < strs.length; i ++) {
                theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
            }
        }

        var order_id = (theRequest['order_id']);

        location.href = "location_details.php?order_id="+order_id;
    }

</script>
