

<?php
if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';
$inventory_user_admin = array('1','22');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器
    header("Location: inventory_login.php?return=i.php&ver=db");
    //确保重定向后，后续代码不会被执行
    exit;
}

//if(!in_array($_COOKIE['inventory_user'],$inventory_user_admin)){
//    exit("此功能仅限指定库管操作, 请返回");
//}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪出库回库管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
    <!-- <script type="text/javascript" src="js/alert.js"></script> -->
    <style>
        html, body, div, object, pre, code, h1, h2, h3, h4, h5, h6, p, span, em,
        cite, del, a, img, ul, li, ol, dl, dt, dd, fieldset, legend, form,
        input, button, textarea, header, section, footer, article, nav, aside,
        menu, figure, figcaption {
            margin: 0;
            padding: 0;
            outline: none
        }

        h1, h2, h3, h4, h5, h6, sup {
            font-size: 100%;
            font-weight: normal
        }

        fieldset, img {
            border: 0;
        }

        input, textarea, select {
            -webkit-appearance: none;
            outline: none;
        }

        mark {
            background: transparent;
        }

        header, section, footer, article, nav, aside, menu {
            display: block
        }

        .clr {
            display: block;
            clear: both;
            height: 0;
            overflow: hidden;
        }
        /*table {
            border-collapse:collapse;
            border-spacing:0;
        }*/
        ol, ul, li {
            list-style: none;
        }

        em {
            font-style: normal;
        }

        label, input, button, textarea {
            border: none;
            vertical-align: middle;
        }

        html, body {
            width: 100%;
            overflow-x: hidden;
        }

        html {
            -webkit-text-size-adjust: none;
        }

        body {
            text-align: left;
            font-family: Helvetica, Tahoma, Arial, Microsoft YaHei, sans-serif;
            color: #666;
            background-color: #fff;
            font-size: 1em;
        }

        .invopt{
            background-color:#DF0000;
            width:8em;
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

        .invback{
            background-color:#DF0000;
            width:5em;
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

        #input_order_id{
            height: 1.5em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_order_id2{
            height: 1.5em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        .qtyopt{
            background-color:#DF0000;
            width:2em;
            height:1.8em;
            line-height: 1.2em;
            padding: 0.1em 0.1em;
            margin:0.1em 0.1em;
            font-size: 1.2em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            float: left;
            font-weight: bold;
        }

        .submit{
            background-color: #DF0000;
            padding: 0.3em 0.8em;
            margin:0.1em 0.1em;
            font-size: 1em;
            text-decoration: none;
            border: 0.1em solid #CC0101;
            border-radius: 0.2em;
            color: #ffffff;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            float: right;
        }

        #inventory{
            width: 100%;
        }

        #product{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }
        #product2{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }
        #invcomment{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }

        #product3{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
        }
        .addprod{
            cursor: pointer;
            color: #fff;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .qty{
            width:2em;
            height:1.2em;
            font-size: 1.5em;
            text-align: center;
            background: none;
        }

        #inv_control{
            padding:0.5em
        }

        .style_green{
            background-color: #117700;
            border: 0.1em solid #006600;
        }

        .style_lightgreen{
            background-color: #8FBB6C;
            border: 0.1em solid #8FBB6C;
        }

        .style_gray{
            background-color:#9d9d9d;
            border: 0.1em solid #888888;
        }

        .style_red{
            background-color:#DF0000;
            border: 0.1em solid #CC0101;
        }

        .style_yellow{
            background-color:#FF6600;
            border: 0.1em solid #df8505;
        }

        .style_light{
            background-color:#fbb450;
            border: 0.1em solid #fbb450;
        }

        .style_ok{
            background-color:#ccffcc;
            border: 0.1em solid #669966;
        }

        .style_error{
            background-color:#ffff00;
            border: 0.1em solid #ffcc33;
        }

        #productsInfo{
            border: 0.1em solid #888888;
        }

        #station{
            font-size: 1em;
        }

        .message{
            width: auto;
            margin: 0.5em;
            padding: 0.5em;
            text-align: center;

            border-radius: 0.3em;
            box-shadow: 0.2em rgba(0, 0, 0, 0.2);
        }

        #productsHold td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold2 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold2 th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHold3 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold3 th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold4 td{
            background-color:#d0e9c6;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        #productsHold4 th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .submit_s{
            padding: 0.2em 0.2em;
            margin:0.1em 0.1em;
            font-size: 0.9em;
            text-decoration: none;
            border-radius: 0.2em;
            color:#333;
            cursor: pointer;
            text-align: center;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }


        #invMovesHold th{
            padding: 0.3em;
            background-color:#f0ad4e;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #invMovesHold td{
            background-color:#ffffaa;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #invMovesPrintCaption{
            width: 100%;
            text-align: left;
        }


        #invMovesPrintHold{
            border-right: solid #000 1px;
            border-bottom: solid #000 1px;
        }

        #invMovesPrintHold th{
            padding: 3px;
            /*background-color:#f0ad4e;*/
            color: #000;
            font-size: 12px;
            font-weight: bold;

            border-left: solid #000 1px;
            border-top: solid #000 1px;
            /*border-radius: 0.1em;*/
            /*box-shadow: 0.1em rgba(0, 0, 0, 0.2);*/
        }

        #invMovesPrintHold td{
            /*background-color:#ffffaa;*/
            color: #000;
            height: 15px;
            font-size: 12px;
            padding: 2px;

            border-left: solid #000 1px;
            border-top: solid #000 1px;
            /*border-radius: 0.1em;*/
            /*box-shadow: 0.1em rgba(0, 0, 0, 0.2);*/
        }

        #prtInvMoveType,#prtInvMoveTitle,#prtInvMoveTime{
            color: #000;
            font-weight: bold;
        }

        .simple-player-container {
            display: inline-block;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        .simple-player-container > div > ul {
            margin: 0;
            padding-left: 0;
        }
        .simpleplayer-play-control {
            background-image: url('images/play.png');
            display: block;
            width: 16px;
            height: 16px;
            bottom: -5px;
            position: relative;
        }
        .simpleplayer-play-control:hover {
            background-image: url('images/playing.png');
        }
        .simpleplayer-stop-control {
            background-image: url('images/stop.png');
            display: block;
            width: 16px;
            height: 16px;
            bottom: -5px;
            position: relative;
        }
        .simpleplayer-stop-control:hover {
            background-image: url('images/stoped.png');
        }

        input::-webkit-input-placeholder {
            color: #CC0000;
        }
        input:-moz-placeholder {
            color: #CC0000;
        }

        input#product{
            height: 1.2em;
            font-size: 1.1em;
        }

        .productBarcode{font-size:14px;}

        .input_default{
            height: 1.8rem;
            font-size:1rem;
            margin: 0.1rem 0;

            background-color: #e3e3e3;
            border-radius: 0.2rem;
            box-shadow: 0.1rem rgba(0, 0, 0, 0.2);
            padding-left: 0.2rem;
        }

        #orderMissing div{
            margin: 3px;
        }

        .w6rem{ width: 6rem; }
        .w4rem{ width: 4rem; }
        .w2rem{ width: 2rem; }

        .f0_7rem{ font-size: 0.7rem; }
        .f0_8rem{ font-size: 0.8rem; }
        .f0_9rem{ font-size: 0.9rem; }
        .f1_0rem{ font-size: 1.0rem; }
        .f1_1rem{ font-size: 1.1rem; }
        .f1_2rem{ font-size: 1.2rem; }
    </style>

    <style media="print">
        .noprint{display:none;}
    </style>


</head>
<?php

//if(isset($_GET['date'])){
//    $date_array = array();
//
//    $date_array[0]['date'] = date("m-d",  strtotime($_GET['date']));
//    $date_array[1]['date'] = date("m-d",strtotime($_GET['date']));
//    $date_array[2]['date'] = date("Y-m-d",strtotime($_GET['date']));
//    $date_array[2]['shortdate'] = date("m-d",strtotime($_GET['date']));
//}
//else{
//    $date_array = array();
//
//    $date_array[0]['date'] = date("m-d",time());
//    $date_array[1]['date'] = date("m-d",time() + 24*3600);
//    $date_array[2]['date'] = date("Y-m-d",time() + 12*3600);
//    $date_array[2]['shortdate'] = date("m-d",time() + 12*3600);
//
//}


?>
<button class="invopt" id="return_index" style="width:4em;" onclick="javascript:location.reload();">返回</button>
<div align="right"><?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <span onclick="javascript:logout_inventory_user();">退出</span></div>
<div id="barcodescanner2" >
    <form method="post" onsubmit="handleProductList2(); return false;">

        <input name="product2" id="product2" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描周转筐" style="ime-mode:disabled; font-size: 1.2rem"/>
        <input class="addprod style_green" type="submit" value ="添加周转框编号" style="font-size: 1em; padding: 0.2em">
    </form>
    <form method="post" onsubmit="handleProductList3(); return false;">

        <input name="product2" id="product3" rows="1" maxlength="19" autocomplete="off" placeholder="点击激活开始扫描商品ID" style="ime-mode:disabled; font-size: 1.2rem"/>
        <input class="addprod style_green" type="submit" value ="添加商品ID" style="font-size: 1em; padding: 0.2em">
    </form>
</div>
<div>周转筐所在的订单号</div>
<table id="productsHold2" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
    <tr>
        <th >DO单号</th>
        <th >周转筐号</th>
        <th align="left">货位号</th>
        <th align="left">商品ID</th>
        <th align="left">计划数量</th>
        <th align="left">实际操作数量</th>
        <th align="left">操作</th>


    </tr>
    <tbody id="productsInfo2">
    <!-- Scanned Product List -->
    </tbody>

</table>





<script>


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
                    window.location = 'inventory_login.php?return=w2.php';
                }
            });
        }
    }
    function handleProductList2(){
        var s_count_id = $('#product2').val();

        var count_id =  s_count_id.substr(0, 6);
        var str=location.href; //取得整个地址栏
        var index = str .lastIndexOf("\=");
      var   relevant_id  = str .substring(index + 1, str .length);

        var warehouse_id =  '<?php echo $_COOKIE['warehouse_id'];?>' ;
        // var date_added = $("#date_added").val();
        if(count_id == '' ){
            alert('请添加扫描框');
            return false ;
        }
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=getRelevantInfoByCount',
            data: {
                method: 'getRelevantInfoByCount',
                data: {
                    warehouse_id:warehouse_id,
                    count_id: count_id,
                    relevant_id:relevant_id,
                }
            },
            success: function (response) {
                if(response) {
                    var html = '';
                    var jsonData = $.parseJSON(response);
                    console.log(response);

//                    $.each(jsonData[0], function (i, v) {
//
//                        var shoot = 0 ;
//                        html += "<tr style='background:#d0e9c6; height: 30px;' id='check" + jsonData.order_id + "'>";
//                        html += "<td>" + v.deliver_order_id + '<br> '+ '['+v.order_id  + ']'+ "</td>";
//                        html += "<td>" + v.container_id +"</td>";
//                        html += "<td>" + jsonData[2].inv_comment + "</td>";
//
//
//                        html += "<td>" + v.product_id +'<br> '+ '['+v.name + ']'+  "</td>";
//                        html += "<td id='num1_"+v.product_id +"'>" + v.quantity + "</td>";
//                        html += "<td id='num2_"+v.product_id +"'>" + shoot  + "</td>";
//                        html += "</tr>";
//                        html += "<tr>";
//                        if(v.quantity > 0 ){
//                            html  += '<td>';
//                            html +='<input class="qtyopt" type="button" id="button_id'+ v.product_id +'"  value="+" onclick="javascript:qtyadd(\''+ v.product_id +'\');" >';
//                            html +='</td>';
//                            html +='<td>';
//                            html +='<input class="qtyopt"  id="button2_id'+ v.product_id +'" type="button" value="-" onclick="javascript:qtyminus(\''+ v.product_id +'\');" >'
//                            html +='</td>';
//                            html +='<td>';
//                            html +='<input class="qtyopt"  id="button2_id'+ v.product_id +'" type="button" value="确认" onclick="javascript:confirmDoRelevant('+ v.product_id +', '+v.deliver_order_id+' , '+v.order_id+');" >';
//                            html +='</td>';
//                            html +='<td>';
//                            html +='<input class="qtyopt"  id="button2_id'+ v.product_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevant('+ v.product_id +', '+v.deliver_order_id+' , '+v.order_id+');" >';
//                            html += '</td>';
//                        }else{
//                            html +='<td>';
//                            html +='<input class="qtyopt"  id="button2_id'+ v.product_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevant('+ v.product_id +', '+v.deliver_order_id+' , '+v.order_id+');" >';
//                            html += '</td>';
//                        }
//
//                        html +="</tr>";
//
//
//                    });

                    $.each(jsonData[1], function (i, v) {
                        var shoot = 0 ;
                        html += "<tr style='background:#d0e9c6; height: 30px;' id='check" + jsonData.order_id + "'>";
                        html += "<td>" + v.deliver_order_id + '<br> '+ '['+v.order_id  + ']'+ "</td>";
                        html += "<td>" + v.container_id +"</td>";
                            html += "<td>" + jsonData[2].inv_comment + "</td>";


                        html += '<td  >' + + '</td>';
                        html += "<td id='num1_"+v.container_id +"'>" + v.quantity + "</td>";
                        html += '<td id="num2_'+v.container_id+'">' + shoot  + '</td>';
                        html += "</tr>";
                        html +="<tr>";
                        if(v.quantity >0 ){
                            html  += '<td>';
                            html +='<input class="qtyopt" type="button" id="button_id'+ v.container_id +'"  value="+" onclick="javascript:qtyaddc(\''+ v.container_id +'\');" >'
                            html += '</td>';
                            html += '<td>';
                            html +='<input class="qtyopt"  id="button2_id'+ v.container_id +'" type="button" value="-" onclick="javascript:qtyminusc(\''+ v.container_id +'\');" >'
                            html += '</td>';
                            html += '<td>';
                            html +='<input class="qtyopt"  id="button3_id'+ v.container_id +'" type="button" value="确认" onclick="javascript:confirmDoRelevantC('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                            html += '</td>';
                            html += '<td>';
                            html +='<input class="qtyopt"  id="button4_id'+ v.container_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevantC('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                            html += '</td>';
                            html += '<td>';
                            html +='<input class="qtyopt"  id="button5_id'+ v.container_id +'" type="button" value="修正货位号" onclick="javascript:updateInvComment('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                            html += '</td>';
                            html += '<td>';
                            html +='<input class="qtyopt" style="display: none" id="inv_comment'+ v.container_id +'" type="text" value="" ;" >';
                            html += '</td>';
                        }else{
                            html += '<td>';
                            html +='<input class="qtyopt"  id="button4_id'+ v.container_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevantC('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                            html += '</td>';
                            html += '<td>';
                            html +='<input class="qtyopt"  id="button5_id'+ v.container_id +'" type="button" value="修正货位号" onclick="javascript:updateInvComment('+ v.container_id+','+ v.deliver_order_id +', '+v.order_id+'  );" >';
                            html += '</td>';
                            html += '<td>';
                            html +='<input class="qtyopt" style="display: none" id="inv_comment'+ v.container_id +'" type="text" value="" ;" >';
                            html += '</td>';



                        }


                        html +="</tr>";


                    });
                    $('#productsInfo2').html(html);
                    $("#product2").val();
                }
            }
        });
    }

    function  showInvComment() {
        $("#inv_comment").show();
    }


    function qtyadd(id){
        var num2 =  $("#num2_"+id).text();
        var num1 =  $("#num1_"+id).text();


        if(parseInt(num1)<parseInt(num2)+1){
            alert('不能超过调拨数量');
        }else{
            var num= parseInt(num2)+1;
            $("#num2_"+id).text(num);

        }
    }

    function qtyaddc(id){
        var num2 =  $("#num2_"+id).text();
        var num1 =  $("#num1_"+id).text();


        if(parseInt(num1)<parseInt(num2)+1){
            alert('不能超过调拨数量');
        }else{
            var num= parseInt(num2)+1;
            $("#num2_"+id).text(num);

        }
    }
    function qtyminusc(id){
        var num2 =  $("#num2_"+id).text();
        var num1 =  $("#num1_"+id).text();

        if( parseInt(num2)  == 0 ) {
            alert('已经为零不能再调') ;
        }else {
            $("#num2_"+id).text(parseInt(num2)-1);
        }


    }

    function qtyminus(id){
        var num2 =  $("#num2_"+id).text();
        var num1 =  $("#num1_"+id).text();

         if( parseInt(num2)  == 0 ) {
             alert('已经为零不能再调') ;
         }else {
             $("#num2_"+id).text(parseInt(num2)-1);
         }


    }


    function handleProductList3() {
        var warehouse_id =  '<?php echo $_COOKIE['warehouse_id'];?>' ;
        var product_id = $("#product3").val();

        var str=location.href; //取得整个地址栏
        var index = str .lastIndexOf("\=");
        var   relevant_id  = str .substring(index + 1, str .length);
        if(product_id == '' ){
            alert('请添加商品ID');
            return false ;
        }
        $.ajax({
            type: 'POST',
            url: 'invapi.php?method=getRelevantInfoByProduct',
            data: {
                method: 'getRelevantInfoByProduct',
                data: {
                    warehouse_id:warehouse_id,
                    product_id: product_id,
                    relevant_id:relevant_id,
                }
            },
            success: function (response) {
                if(response) {
                    var html = '';
                    var jsonData = $.parseJSON(response);
                    console.log(response);

                    if (jsonData ) {

                        $.each(jsonData[0], function (i, v) {
                            var shoot = 0 ;
                            html += "<tr style='background:#d0e9c6; height: 30px;' id='check" + jsonData.order_id + "'>";
                            html += "<td>" + v.deliver_order_id + '<br> '+ '['+v.order_id  + ']'+ "</td>";
                            html += "<td>" + v.container_id +"</td>";
                            html += "<td>" + v.inv_comment + "</td>";

                            var id =v.product_id + v.deliver_order_id ;
                            html += "<td>" + v.product_id +'<br> '+ '['+v.name + ']'+  "</td>";
                            html += "<td id='num1_"+id +"'>" + v.quantity + "</td>";
                            html += "<td id='num2_"+id +"'>" + shoot  + "</td>";
                            html  += '<td>';
                             if(v.quantity > 0 ){
                                 html +='<input class="qtyopt" type="button" id="button_id'+ v.product_id +'"  value="+" onclick="javascript:qtyadd(\''+ id +'\');" >'
                                 html +='<input class="qtyopt"  id="button2_id'+ v.product_id +'" type="button" value="-" onclick="javascript:qtyminus(\''+ id +'\');" >'
                                 html +='<input class="qtyopt"  id="button3_id'+ v.product_id +'" type="button" value="确认" onclick="javascript:confirmDoRelevant('+ id +', '+v.deliver_order_id+' , '+v.order_id+','+v.product_id+');" >';
                                 html +='<input class="qtyopt"  id="button4_id'+ v.product_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevant('+ id +', '+v.deliver_order_id+' , '+v.order_id+','+v.product_id+');" >';
                             }else{
                                 html +='<input class="qtyopt"  id="button5_id'+ v.product_id +'" type="button" value="删除确认数据" onclick="javascript:updateDoRelevant('+ id +', '+v.deliver_order_id+' , '+v.order_id+','+v.product_id+');" >';
                             }

                            html += '</td>';
                            html += "</tr>";


                        });
                        $('#productsInfo2').html(html);
                    }
                }
            }
        });

    }


   function  confirmDoRelevant(id,deliver_order_id ,order_id,product_ids ) {
       $("#button3_id"+product_ids).attr("disabled", true);

       var product_id = $("#product3").val();
       var s_count_id = $('#product2').val();
       var count_id =  s_count_id.substr(0, 6);

       var user_id= '<?php echo $_COOKIE['inventory_user_id'];?>';
       var warehouse_id= '<?php echo $_COOKIE['warehouse_id'];?>';
       var quantity =  $("#num2_"+id).text();
       var str=location.href; //取得整个地址栏
       var index = str .lastIndexOf("\=");
       var   relevant_id  = str .substring(index + 1, str .length);

       if(quantity == 0 ){
           alert ('数量为零不能提交');
           return false ;
       }
       if(confirm("数量确认？")) {
           $(".qtyopt").hide();
           $.ajax({
               type: 'POST',
               url: 'invapi.php',
               data: {
                   method: 'confirmDoRelevant',
                   data: {
                       id: product_ids,
                       user_id: user_id,
                       deliver_order_id: deliver_order_id,
                       order_id: order_id,
                       quantity: quantity,
                       warehouse_id: warehouse_id,
                       relevant_id: relevant_id,

                   },
               },
               success: function (response) {
                   var jsonData = $.parseJSON(response);
                   if (jsonData == 1) {
                       alert('提交成功');

                       if (product_id > 0) {
                           handleProductList3();
                           $(".qtyopt").show();
                           $("#product3").val('');
                           $("#product2").val('');
                       }
                       if (count_id > 0) {
                           handleProductList2();
                           $(".qtyopt").show();
                           $("#product3").val('');
                           $("#product2").val('');
                       }

                   }
                   if (jsonData == 2) {
                       alert('提交失败');

                   }
                   if (jsonData == 3) {
                       alert('此商品已提交');
                   }

                   if (jsonData == 4) {
                       alert('该订单已经提交不能做合单操作');
                   }
               }


           });
       }
   }

    function  updateDoRelevant (id,deliver_order_id ,order_id,product_ids){
        var product_id = $("#product3").val();
        var s_count_id = $('#product2').val();
        var count_id =  s_count_id.substr(0, 6);

        if(confirm('确认删除合单数据？')) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'updateDoRelevant',
                    data: {
                        id: product_ids,
                        deliver_order_id: deliver_order_id,
                        order_id: order_id,

                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if (jsonData == 1) {
                        alert('删除成功');
                        if(product_id > 0 ){
                            handleProductList3();
                        }
                        if(count_id > 0 ){
                            handleProductList2();
                        }


                    }
                    if (jsonData == 2) {
                        alert('提交失败');
                    }
                    if (jsonData == 3) {
                        alert('不能删除分拣数据');
                    }
                }


            });
        }
    }



    function  updateDoRelevantC (id,deliver_order_id ,order_id){
        var product_id = $("#product3").val();
        var s_count_id = $('#product2').val();
        var count_id =  s_count_id.substr(0, 6);

        if(confirm('确认删除合单数据？')) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'updateDoRelevantC',
                    data: {
                        id: id,
                        deliver_order_id: deliver_order_id,
                        order_id: order_id,

                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if (jsonData == 1) {
                        alert('删除成功');
                        if(product_id > 0 ){
                            handleProductList3();
                        }
                        if(count_id > 0 ){
                            handleProductList2();
                        }
                    }
                    if (jsonData == 2) {
                        alert('提交失败');
                    }
                    if (jsonData == 3) {
                        alert('不能删除分拣数据');
                    }
                }


            });
        }
    }


    function  confirmDoRelevantC(id,deliver_order_id ,order_id ) {
        $("#button3_id"+id).attr("disabled", true);

        var product_id = $("#product3").val();
        var s_count_id = $('#product2').val();
        var count_id =  s_count_id.substr(0, 6);

        var user_id= '<?php echo $_COOKIE['inventory_user_id'];?>';
        var warehouse_id= '<?php echo $_COOKIE['warehouse_id'];?>';
        var quantity =  $("#num2_"+id).text();
        var str=location.href; //取得整个地址栏
        var index = str .lastIndexOf("\=");
        var   relevant_id  = str .substring(index + 1, str .length);

        if(quantity == 0 ){
            alert ('数量为零不能提交');
            return false ;
        }
        if(confirm("数量确认？")) {
            $(".qtyopt").hide();
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'confirmDoRelevantC',
                    data: {
                        id: id,
                        user_id: user_id,
                        deliver_order_id: deliver_order_id,
                        order_id: order_id,
                        quantity: quantity,
                        warehouse_id: warehouse_id,
                        relevant_id: relevant_id,

                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if (jsonData == 1) {
                        alert('提交成功');

                        if (product_id > 0) {
                            handleProductList3();


                            $(".qtyopt").show();
                            $("#product3").val('');
                            $("#product2").val('');
                        }
                        if (count_id > 0) {
                            handleProductList2();
                            $(".qtyopt").show();
                            $("#product3").val('');
                            $("#product2").val('');
                        }
                    }
                    if (jsonData == 2) {
                        alert('提交失败');
                    }
                    if (jsonData == 3) {
                        alert('此商品已提交');
                    }
                    if (jsonData == 4) {
                        alert('该订单已经提交不能做合单操作');
                    }
                }


            });
        }

    }

    function updateInvComment(id,deliver_order_id ,order_id) {
        var inv_comment = $("#inv_comment"+id).val();
        var product_id = $("#product3").val();
        var s_count_id = $('#product2').val();
        var count_id =  s_count_id.substr(0, 6);
        if(inv_comment == ''){
             $("#inv_comment"+id).show();
        }else{



        if(confirm('确认修正货位号？')) {
            $.ajax({
                type: 'POST',
                url: 'invapi.php',
                data: {
                    method: 'updateInvComment',
                    data: {
                        order_id: order_id,
                        inv_comment:inv_comment,

                    },
                },
                success: function (response) {
                    var jsonData = $.parseJSON(response);

                    if (jsonData == 1) {
                        alert('修改成功');
                        if (product_id > 0) {
                            handleProductList3();
                        }
                        if (count_id > 0) {
                            handleProductList2();
                        }
                    }
                    if (jsonData == 2) {
                        alert('提交失败');
                    }

                }


            });
        }
        }
    }

</script>




</html>