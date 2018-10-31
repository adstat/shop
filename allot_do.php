<?php
if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';

$inventory_user_admin = array('1','22','24');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
if(!in_array($_COOKIE['user_group_id'],$inventory_user_admin)){
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    exit("调拨申请仅限指定人员操作, 请返回");
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪整件波次调拨申请</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
    <!-- <script type="text/javascript" src="js/alert.js"></script> -->
    <style>

        #ordersHold td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        #ordersHold th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        .invopt{
            background-color:#DF0000;
            height:3em;
            line-height: 1em;
            padding: 0.5em 0.5em;
            margin:0.1em 0.1em;
            font-size: 1em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }


    </style>

    <style media="print">
        .noprint{display:none;}
    </style>

    <script>
        window.product_barcode_arr = {};
        window.product_barcode_arr_s = {};
        <?php if(!in_array($_COOKIE['user_group_id'], $inventory_user_admin)){?>
        $(document).keydown(function (event) {
            $('#product').focus();
        });
        <?php } ?>
    </script>
</head>

<body>
<script type="text/javascript">
    var is_admin = 0;
</script>
<div align="right" style="margin: 0.2rem">
    <?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <button onclick="javascript:logout_inventory_user();">退出</button>
</div>
<div align="center" id="purchase_info"></div>
<div  style="display: none" id="warehouse_id"> <?php echo $_COOKIE['warehouse_id'];?> </div>
<button class="invopt" style="display: inline;float:left" onclick="window.location.href='i.php?auth=xsj2015inv&ver=db'">返回</button>
<div align="center" style="display:block; margin:0.5em auto" id="logo"><img src="view/image/logo.png" style="width:6em"/> 整件波次调拨申请<button class="invopt" style="display: inline" onclick="javascript:location.reload();">刷新</button></div><br />
<div align="center" style="display:block; margin:0.5em auto" id="show_order_comment"></div>
<?php

if(isset($_GET['date'])){
    $date_array = array();

    $date_array[0]['date'] = date("m-d",  strtotime($_GET['date']));
    $date_array[1]['date'] = date("m-d",strtotime($_GET['date']));
    $date_array[2]['date'] = date("Y-m-d",strtotime($_GET['date']));
    $date_array[2]['shortdate'] = date("m-d",strtotime($_GET['date']));
}
else{
    $date_array = array();

    $date_array[0]['date'] = date("m-d",time());
    $date_array[1]['date'] = date("m-d",time() + 24*3600);
    $date_array[2]['date'] = date("Y-m-d",time());
    $date_array[2]['shortdate'] = date("m-d",time()  + 9*3600);

}


$selete_date = array();
$today_date = $date_array[2]['date'];

$selete_date[] = '';

for($i = 14; $i >= 0 ;$i--){
    $cur_date = date("Y-m-d", strtotime($today_date . " 00:00:00") - $i * 24 * 3600);
    $selete_date[] = $cur_date;

}

for($i = 1; $i <=14 ;$i++){
    $cur_date = date("Y-m-d", strtotime($today_date . " 00:00:00") + $i * 24 * 3600);
    $selete_date[] = $cur_date;

}

?>



<div id="searchPurchaseOrder">

    是否加入调拨单:
    <select id="do_relevant_id" style="width:12em;height:2em;">
        <option value="0" selected > 否</option>
        <option value="1"> 是</option>
    </select>
    <br>
    <br>
    目的仓库：
    <select id="to_warehouse" style="font-size: 15px;width:8em;height:1.5em;border:1px solid">
    </select>
    <br/>
    <br/>
    <p style="margin: 0.5em"><span style="width: 3em">配送日期：</span>
        <input id="date_start" name="date_start"  autocomplete="off" class="date" type="text" value=""  style="font-size: 15px; width:15.5em ;height: 40px;border:1px solid" data-date-format="YYYY-MM-DD-HH" id="input-date-end" />

    <input type="button" style="font-size:1.2em;" onclick="javascript:getOrderByStatus()" value="查询"><br />
</div>

<div id="login" align="center" style="margin:0.5em auto; display: none">
    <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
    <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
</div>

<div id="content" style="display: block">
    <div align="center" id="orderListTable" style="margin:0.5em auto;">
        <input type="hidden" id="current_order_id" value="">
        <table border="0" style="width:100%;background:" cellpadding="2" cellspacing="3" id="ordersHold">
            <thead>
            <tr>
                <th >选中</th>
                <th >波次单号</th>
                <th >DO单号</th>
                <th>配送日期</th>
                <th>分拣状态</th>
                <th id="in">是否加入了调拨单</th>
<!--                <th>操作</th>-->
            </tr>
            </thead>
            <tbody id="ordersList">

            </tbody>
        </table>
        <br/>
        <div id="creat_shipping_cost" style="float:left;display:;" class="col-sm-4"  >
            仓库成本：<input style="solid :1px" type="text" id= 'warehouse_cost' placeholder="单击填写仓库成本" class="btn " value="" >
            <br />
            <br />
            调拨运费：<input style="solid :1px" type="text" id= 'shipping_cost' placeholder="单击填写调拨运费" class="btn " value="" >
            <br />
        </div>
        <button class="invopt" style="display: inline" onclick="addDoOrderRelevant()">提交</button>
    </div>

    </div>



<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="player3" src="view/sound/ding.mp3">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>
<div style="display: none">
    <div comment="Used for alert but hide">
        <audio id="player2" src="view/sound/redalert.wav">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
</div>

<div id="overlay">

</div>


<script>
    $(document).ready(function(){
        var today=new Date();
        var year=today.getFullYear();
        var month=today.getMonth()+1;
        var day=today.getDate();

        $('#date_start').val(year+"-"+month+"-"+day );
    });
    $(document).ready(function () {
        startTime();

        var warehouse_ids = $("#warehouse_id").text();
        var select_date = $("#date_start").val();

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getUseWarehouseId',
            },
            success : function (response , status , xhr){
                if(response) {
                    var jsonData = $.parseJSON(response);
                    var html = '';
                    $.each(jsonData, function (index, value) {
                        if(value.warehouse_id != 14){
                            html += '<option value="' +value.warehouse_id+ '">' + value.title + '</option>';
                        }


                    });
                    $('#to_warehouse').html(html);
                    $('#to_warehouse').val(parseInt(warehouse_ids));
                }
            }

        });


    });
</script>
<script>

    window.product_weight_info = new Array();
    //JS Date Format Extend
    Date.prototype.Format = function(fmt)
    { //author: meizz
        var o = {
            "M+" : this.getMonth()+1,                 //月份
            "d+" : this.getDate(),                    //日
            "h+" : this.getHours(),                   //小时
            "m+" : this.getMinutes(),                 //分
            "s+" : this.getSeconds(),                 //秒
            "q+" : Math.floor((this.getMonth()+3)/3), //季度
            "S"  : this.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt))
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        for(var k in o)
            if(new RegExp("("+ k +")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        return fmt;
    }


    /* 显示遮罩层 */
    function showOverlay() {
        $("#overlay").height(pageHeight());
        $("#overlay").width(pageWidth());

        // fadeTo第一个参数为速度，第二个为透明度
        // 多重方式控制透明度，保证兼容性，但也带来修改麻烦的问题
        $("#overlay").fadeTo(200, 0.5);
    }

    /* 隐藏覆盖层 */
    function hideOverlay() {
        $("#overlay").fadeOut(200);
    }


    /* 当前页面高度 */
    function pageHeight() {
        return document.body.scrollHeight;
    }

    /* 当前页面宽度 */
    function pageWidth() {
        return document.body.scrollWidth;
    }

    $(document).ready(function () {
        // startTime();
        getOrderByStatus();
                $.ajax({
                    type : 'POST',
                    url : 'invapi.php?vali_user=1',
                    data : {
                        method : 'getDoOrderStatus'
                    },
                    success : function (response , status , xhr){
                        //console.log(response);

                        if(response){
                            var jsonData = eval(response);
                            if(jsonData.status == 999){
                                alert("未登录，请登录后操作");
                                window.location = 'inventory_login.php?return=w_i.php';
                            }
                            var html = '<option value=0>-请选择订单状态-</option>';
                            $.each(jsonData, function(index, value){
                                html += '<option value='+ value.order_status_id +' >' + value.name + '</option>';
                            });
                            $('#orderStatus').html(html);

                            console.log('Load Stations');
                        }
                    }
                });

        // });


        //Alert Sound Settings
        var settings = {
            progressbarWidth: '0',
            progressbarHeight: '5px',
            progressbarColor: '#22ccff',
            progressbarBGColor: '#eeeeee',
            defaultVolume: 0.8
        };
        $("#player").player(settings);
    });



    function startTime()
    {
        var today=new Date();
        var year=today.getFullYear();
        var month=today.getMonth()+1;
        var day=today.getDate();

        var h=today.getHours();
        var m=today.getMinutes();
        var s=today.getSeconds();
        // add a zero in front of numbers<10
        m=checkTime(m);
        s=checkTime(s);
        $('#currentTime').html(year+"/"+month+"/"+day+" "+h+":"+m+":"+s);
        t=setTimeout('startTime()',500)
    }

    function checkTime(i)
    {
        if (i<10)
        {i="0" + i}
        return i
    }




    function getSetDate(dateGap,dateFormart) {
        var dd = new Date();
        dd.setDate(dd.getDate()+dateGap);//获取AddDayCount天后的日期

        return dd.Format(dateFormart);

        //console.log(getSetDate(1,'yyMMdd')); //Tomorrow
        //console.log(getSetDate(-1,'yyMMdd')); //Yesterday
    }





    function logout_inventory_user(){
        if(confirm("确认退出？")){
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'inventory_logout'
                },
                success : function (response , status , xhr){
                    //console.log(response);
                    window.location = 'inventory_login.php?return=w_i.php';
                }
            });
        }
    }

    function getOrderByStatus(){

        var select_date = $("#date_start").val();
        var do_relevant_id = $("#do_relevant_id").val();
        var order_status_id = $("#orderStatus").val();
        var warehouse_id = $("#to_warehouse").val();
        var do_warehouse_id = '<?php echo $_COOKIE['warehouse_id']?>';
        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getAllotDoOrder',
                data: {
                    date: select_date,
                    order_status_id: order_status_id,
                    do_relevant_id: do_relevant_id,
                    warehouse_id:warehouse_id,
                    do_warehouse_id:do_warehouse_id,


                },

            },
            success: function (response, status, xhr) {
                //console.log(response);

                if (response) {

                    var jsonData = $.parseJSON(response);
                    if (jsonData.return_code == 'ERROR') {
                        alert(jsonData.return_msg);
                        return true;
                    } else if (jsonData.return_code == 'SUCCESS') {
                        // alert(jsonData.return_msg);
                    } else {
                        alert('数据异常请刷新后重试');
                        return true;
                    }
                    var html = '';
                    $.each(jsonData.return_data, function (index, value) {
                            html += '<tr>';
                            if (parseInt(value.relevant_id) == 0) {
                                html += "<td>";
                                html += '<input  type="checkbox" style="zoom:180%;background: red " type="button" name="pich_id[]" class="pich_id"  id="' + value.deliver_order_id + '"  value="'+value.batch_id+ '" >';
                                html += "</td>";
                            } else {
                                html += "<td></td>";
                            }
                            html += '<td>'+value.batch_id+'<br />';
                            html +='<span style="red">'+ value.title + '</span><br />';
                            html +='<span style="red">'+ value.inv_comment + '</span>';
                            html +='</td>';
                            html += '<td >';
                            $.each(value.deliver_order_id.split(','),function(i,v){
                                html += "<br />"+v;
                            });
                            html += '<br />';
                            html += '</td>';
                            html += '<td >' + value.deliver_date + '</td>';
                            html += '<td >' + value.name + '</td>';
                            if (value.relevant_id > 0) {
                                html += '<td >' + '是' + '</td>';
                            } else {
                                html += '<td >' + '否' + '</td>';
                            }

                            // if (value.relevant_id > 0) {
                            //     html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory(' + value.order_id + ');">开始入库</button>';
                            // }


                            // html += '</td>';
                            html += '</tr>';

                    });


                    $('#ordersList').html(html);
                    // console.log('Load Stations');
                }
            },
        });

    }

  function  addDoOrderRelevant() {
      var shipping_cost = parseInt($("#shipping_cost").val());
      var warehouse_cost = parseInt($("#warehouse_cost").val());
      if(shipping_cost > 0){
      } else {
          alert('请填写调拨运费');
          return false;
      }
      if(warehouse_cost > 0){
      } else {
          alert('请填写仓库成本');
          return false;
      }
      var checkbox =$("input[name='pich_id[]']:checked").val([]);
      var user_id=' <?php echo $_COOKIE['inventory_user_id'];?>' ;
      var warehouse_id = $("#to_warehouse").val();
      var do_warehouse_id = '<?php echo $_COOKIE['warehouse_id']?>';

      var  check_value = [];
      for(var i=0;i<checkbox.length;i++){
          check_value.push(checkbox[i].value);
      }


      if(check_value.length == 0 ){
          alert('提交的数量不能为零');
          return false;
      }

      if(confirm("确认提交吗？")) {
          $.ajax({
              type: 'POST',
              url: 'invapi.php',
              dataType: 'json',
              data: {
                  method: 'addDoOrderRelevant',
                  data: {
                      check_value: check_value,
                      user_id: user_id,
		                shipping_cost:shipping_cost,
                      warehouse_cost:warehouse_cost,
                      warehouse_id:warehouse_id,
                      do_warehouse_id :do_warehouse_id,

                  }
              },
              success: function (response) {
                  var jsonData = $.parseJSON(response);
                  if (jsonData.return_code == 'ERROR') {
                      alert(jsonData.return_msg);
                  } else if (jsonData.return_code == 'SUCCESS') {
                      alert(jsonData.return_msg);
                  } else {
                      alert('数据异常请刷新后重试');
                  }
              }


          });
      }
      $("#shipping_cost").val('');
      $("#warehouse_cost").val('');
  }

</script>
</body>
</html>
