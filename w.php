<?php
if( !isset($_GET['auth']) || $_GET['auth'] !== 'xsj2015inv'){
    exit('Not authorized!');
}
include_once 'config_scan.php';
//exit('系统繁忙，请稍候...');


$inventory_user_admin = array('randy','alex','leibanban','yangyang','ckczy','liuhe','wuguobiao','wangshaokui');
if(empty($_COOKIE['inventory_user'])){
    //重定向浏览器 
    
    header("Location: inventory_login.php?return=w.php"); 
    
    
    //确保重定向后，后续代码不会被执行 
    exit;
}

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪订单分拣</title>
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

        label, input, button {
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
        .qtyopt_weight{
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


        #frame_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:1rem;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #incubator_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #foam_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;
        
            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #frame_mi_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;
        
            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #incubator_mi_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #frame_ice_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #box_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #frame_merge_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #frame_meat_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #foam_ice_count{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: #d0e9c6;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        
        
        #input_vg_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_meat_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_mi_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_ice_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        #input_merge_frame{
            margin: 0.5em auto;
            height: 1.5em;
            font-size:0.9em;
            background-color: red;
        
            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
            padding-left: 0.2em;
            width: 4em;
        }
        
        
        
        
        .addprod{
            cursor: pointer;
            color: #fff;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        .qty{
            width:1.2em;
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

        .current_do_class td{
            color:red;
            height: 5em;
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
        
        #productsHoldDo td{
            background-color:#d0e9c6;
            color: #000;
            height: 2em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        
        #productsHoldDo th{
            padding: 0.3em;
            background-color:#8fbb6c;
            color: #000;
            height: 3em;
            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }
        
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

        .prodlist{font-size: 1.4em;}

        #overlay {
            background: #000;
            filter: alpha(opacity=50); /* IE的透明度 */
            opacity: 0.5;  /* 透明度 */
            display: none;
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            z-index: 100; /* 此处的图层要大于页面 */
            display:none;
        }


        #vg_list div{
            font-size: 1rem;
            border-radius: 5px;
            background-color: #d0e9c6;
            margin: 0.2rem;
            margin-right: 0.6rem;
            float: left;
            line-height: 1.4rem;
            padding:0.3rem 0 0.2rem 0.2rem;
        }

        .frame_num{
            background-color: yellow;
            font-size: 1.2rem;
            border-radius: 5px;
            padding:0.2rem;
        }

    </style>

    <style media="print">
        .noprint{display:none;}
    </style>

    <script>
        window.product_barcode_arr = {};
        window.product_barcode_arr_s = {};
        <?php if(!in_array($_COOKIE['inventory_user'], $inventory_user_admin)){?> 
        $(document).keydown(function (event) {
            $('#product').focus();
        });
        <?php } ?>

        var isSepicalUser = 0;
        <?php if($_COOKIE['inventory_user'] == 'xsfj030'){?>
            isSepicalUser = 0;
        <?php } ?>

    </script>
</head>
<body>
    <script type="text/javascript">
               var is_admin = 0;
            </script>
    <div align="right"><?php echo $_COOKIE['inventory_user'];?> 所在仓库: <?php echo $_COOKIE['warehouse_title'];?> <span onclick="javascript:logout_inventory_user();">退出</span>
        <?php if(in_array($_COOKIE['inventory_user'], $inventory_user_admin)){?> 
            <script type="text/javascript">
                is_admin = 1;
            </script>
            <a href="inv_data.php">查看分拣数据</a>
        <?php } ?>
         <a href="inv_dif_data.php">查看未分拣数据</a>
    </div>
    <div  style="display: none" id="warehouse_id"> <?php echo $_COOKIE['warehouse_id'];?> </div>
    <div align="center" style="display:block; margin:0.5em auto" id="logo"><img src="view/image/logo.png" style="width:6em"/> 订单分拣<button class="invopt style_gray" id="reload"  style="display: inline" onclick="javascript:location.reload();" disabled="disabled">载入中...</button></div>

    
    
    
    
    <?php if(in_array($_COOKIE['inventory_user'], $inventory_user_admin)){?> 
    订单状态:
    <select id="orderStatus" style="width:12em;height:2em;">
        
    </select>
    <input type="button" style="font-size:1.2em;" onclick="javascript:getOrderByStatus()" value="查询">
    <br>
    订单分类:
    <select id="orderStation" style="margin-top: 0.2em;width:12em;height:2em;">
        <option value="0">全部</option>
        <option value="1">生鲜(包装菜、奶制品)</option>
        <option value="2">快销品</option>
    </select>
    
    <?php } ?>
    
    
    
    <div id="login" align="center" style="margin:0.5em auto; display: none">
        <input name="pwd" id="pwd" rows="1" maxlength="18" style="font-size: 1.5em; background-color: #b9def0" />
        <input class="submit_s style_green" type="button" value ="登陆" onclick="javascript:login();">
    </div>

    <div id="content" style="display: block">
        <div align="center" id="orderListTable" style="margin:0.5em auto;">
            <div style="margin:0.3rem; padding:0.3rem; color:#5e5e5e; font-style: italic; background-color: #d6d6d6; border: 1px dashed #5e5e5e;">点击"查看"提交周转筐信息<br />快消品使用新界面分拣</div>
            <input type="hidden" id="current_order_id" value="">
            <table border="0" style="width:100%;" cellpadding="2" cellspacing="3" id="ordersHold">
                <thead>
                    <tr>
                        <th >编号</th>
                        <th >订单ID</th>
                        <th>商品数</th>
                        <th>未分拣</th>
                        <th>分拣人</th>
                        <th>订单状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="ordersList">
                    
                </tbody>
            </table>
            
            
            
        </div>
        <div align="center" style="margin:0.5em auto;">
            
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

            $date_array[0]['date'] = date("m-d",time() + 8*3600);
            $date_array[1]['date'] = date("m-d",time() + 8*3600 + 24*3600);
                $date_array[2]['date'] = date("Y-m-d",time() + 8*3600 + 9*3600);
                $date_array[2]['shortdate'] = date("m-d",time() + 8*3600 + 9*3600);
            
            }
            
            
            ?>
            <input style="display: none; font-size: 0.9em; line-height: 0.9em" id="getplanned" class="submit_s style_lightgreen" type="button" value="获取计划入库值(<?php echo $date_array[2]['shortdate']; ?>)" onclick="javascript:getSortingList('<?php echo $date_array[2]['date']; ?>');">
            <input type="hidden" value=0 id="purchasePlanId" />
        </div>
        <div id="message" class="message style_ok" style="display: none;"><!-- Insert Inventory Control Message Here--></div>
        <div id="inv_control" align="center">
            <div id="invMethods">
                
            </div>
            <div id="shelfLifeStrict" style="display: none"></div>
            <div id="productList" name="productList" method="POST" style="display: none">
                
                <div id="product_name" style="font-size:2em;" align="center"></div>
                <table id="productsHoldDo" border="0" style="width:100%;display:none;"  cellpadding=2 cellspacing=3>
                    <tr>
                        <th style="width:4em">计划量</th>
                        <th style="width:4em">待投篮</th>
                        <th align="center" id="current_do_tj"></th>
                    </tr>
                    <tbody id="productsInfoDo">
                        <tr>
                            <td id="current_product_plan"  align="center" style="font-size:3.75em;"></td>
                            <td id="current_product_quantity" align="center" style="font-size:2.5em;"></td>
                            <td id="current_product_quantity_change" align="center">
                                
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div id="barcodescanner" style="display: none">
                    <form method="get" onsubmit="handleProductList(); return false;">
                        <input name="product" id="product" rows="1" maxlength="19" onclick="javascript:clickProductInput();" autocomplete="off" placeholder="点击激活开始扫描" style="ime-mode:disabled; height: 2em;"/>
                        <!--<input class="addprod style_green" type="submit" value ="添加" style="font-size: 1em; padding: 0.2em">-->
                    </form>
                </div>
                <script type="text/javascript">
                    $("input[name='product']").keyup(function(){
                        var tmptxt=$(this).val();
                        $(this).val(tmptxt.replace(/\D/g,''));
                        if(tmptxt.length >= 4){
                            handleProductList();
                        }
                        $(this).val("");
                    }).bind("paste",function(){
                            var tmptxt=$(this).val();
                            $(this).val(tmptxt.replace(/\D/g,''));
                        });
                    //$("input[name='product']").css("ime-mode", "disabled");
                </script>

                <input type="hidden" id="current_do_product" value="0">
                <div style="display:block; margin-top: 1em;">
                    <span style=" font-size: 1.2em;">选择待投篮商品</span>
                    <span style="float:right;font-size: 1em; line-height: 1.8em;">共<span id="count_plan_quantity"></span>件,待完成<span id="count_quantity"></span>件</span>
                </div>
                <table id="productsHold" border="0" style="width:100%;"  cellpadding=2 cellspacing=3>
                    <tr>
                        <th align="left">商品</th>
                        <th style="width:2em">计划量</th>
                        <th style="width:2em">待投篮</th>
                        <th style="width:3em">状态</th>
                    </tr>
                    <tbody id="productsInfo">
                        <!-- Scanned Product List -->
                    </tbody>
                </table>
                
                
                
                
                
                
                <input type="hidden" name="method" id="method" value="" />
                <div style="float:left">当前时间: <span id="currentTime"><?php echo date('Y-m-d H:i:s', time());?></span></div>
                <br />
                
                
                <?php if(in_array($_COOKIE['inventory_user'],$inventory_user_admin)){?>
                    <input class="submit" id="submit_del" type="button" value="删除分拣数据" onclick="javascript:delOrderProductToInv();">
                    <input class="submit" id="submit" type="button" value="提交" onclick="javascript:addOrderProductToInv_pre();">
                <?php } ?>
                
                
                
                <div style="float:left;width: 100%;">
                    
                    <div id="inv_do_vg" style="border-bottom:1px dashed black;" >
                        框数：<input type="text" id="frame_count" ><br>
                        备注:<textarea id="inv_comment"></textarea><br>
                        <input type="hidden" id="frame_vg_list">
                        蔬菜框号：<input style="font-size: 1.4em;" id="input_vg_frame" name="input_vg_frame">
                        <div id="vg_list" ></div>
                    </div>
                    <div id="inv_do_meat" style="border-bottom:1px dashed black;">
                        肉框数：<input type="text" id="frame_meat_count" ><br>
                        <input type="hidden" id="frame_meat_list">
                        肉框号：<input style="font-size: 1.4em;" id="input_meat_frame" name="input_meat_frame">
                        <div id="meat_list"></div>
                </div>  
                    <div id="inv_do_mi" style="border-bottom:1px dashed black;">
                    泡沫箱数：<input type="text" id="foam_count" ><br>
                        奶框数：<input type="text" id="frame_mi_count" ><br>
                        保温箱数：<input type="text" id="incubator_mi_count" ><br>
                        <input type="hidden" id="frame_mi_list">
                        奶框号：<input style="font-size: 1.4em;" id="input_mi_frame" name="input_mi_frame">
                        <div id="mi_list"></div>
                    </div>

                    <div id="inv_do_ice" style="border-bottom:1px dashed black;">
                        保温箱数：<input type="text" id="incubator_count" ><br>
                        冷冻框：<input type="text" id="frame_ice_count" ><br>
                        
                        冷冻泡沫箱：<input type="text" id="foam_ice_count" ><br>
                        纸箱(无框号)：<input type="text" id="box_count" ><br>
                        <input type="hidden" id="frame_ice_list">
                        冷冻框号：<input style="font-size: 1.4em;" id="input_ice_frame" name="input_ice_frame">
                        <div id="ice_list"></div>
                    </div>


                    <div id="fastmove_order_comment">
                        <table border="1" style="width:100%;"  cellpadding=3 cellspacing=1>
                            <tr>
                                <th>已分拣整件数</th>
                                <td>
                                    <input type="hidden" id="box_count" class="fm_box_count" value="0" readonly="readonly" />
                                    <div id="nonRepackBoxCount" style="font-size: 1.2rem">0</div>
                                </td>
                            </tr>
                            <tr>
                                <th>分拣备注<br />(货位号)</th>
                                <td>
                                    <input type="text" style="font-size: 1.5rem; width:5rem; height:2.8rem; padding: 0.2rem; border: 1px #333333 solid;" id="inv_comment" class="fm_inv_comment" />
                                </td>
                            </tr>
                            <tr>
                                <th>周转筐数量</th>
                                <td><input type="text" id="frame_count" class="fm_frame_count" value="0" readonly="readonly" /></td>
                            </tr>
                            <tr>
                                <th>周转筐扫描</th>
                                <td>
                                    <input type="hidden" id="frame_vg_list" class="fm_frame_vg_list" />
                                    <input style="font-size: 1.2em; width:90%;" id="input_vg_frame" name="input_vg_frame" />
                                    <div id="vg_list" class="fm_vg_list"></div>
                                </td>
                            </tr>
                        </table>

                        <div style="display: none">备注:<textarea id="inv_comment" class="fm_inv_comment" ></textarea></div>
                    </div>
                    
                    
                    
                    
                    
                    
                    
                    <input class="submit" id="submit_num" type="button" value="修改框数货位" onclick="javascript:addOrderNum();">
                    <br>
                    <?php if(in_array($_COOKIE['inventory_user'],$inventory_user_admin)){?>
                    <div>合并废弃框号：<input style="font-size: 1.4em;" id="input_merge_frame" name="input_merge_frame"></div>
                    <div id="merge_list"></div>
                    <?php } ?>
                </div>    

                
                
                
                <script type="text/javascript">
                    
                    $("input[name='input_vg_frame']").keyup(function(){
                        var tmptxt=$(this).val();
                        //$(this).val(tmptxt.replace(/\D|^0/g,''));
                        if(tmptxt.length == 8){
                            handleFrameList('vg');
                            $(this).val("");
                        }
                    }).bind("paste",function(){
                            var tmptxt=$(this).val();
                            $(this).val(tmptxt.replace(/\D|^0/g,''));
                    });
                    
                    $("input[name='input_meat_frame']").keyup(function(){
                        var tmptxt=$(this).val();
                        //$(this).val(tmptxt.replace(/\D|^0/g,''));
                        if(tmptxt.length == 8){
                            
                            handleFrameList('meat');
                             $(this).val("");
                        }
                    }).bind("paste",function(){
                            var tmptxt=$(this).val();
                            $(this).val(tmptxt.replace(/\D|^0/g,''));
                    });
                    
                    $("input[name='input_mi_frame']").keyup(function(){
                        var tmptxt=$(this).val();
                        //$(this).val(tmptxt.replace(/\D|^0/g,''));
                        if(tmptxt.length == 8){
                            handleFrameList('mi');
                             $(this).val("");
                        }
                    }).bind("paste",function(){
                            var tmptxt=$(this).val();
                            $(this).val(tmptxt.replace(/\D|^0/g,''));
                    });
                    
                    $("input[name='input_ice_frame']").keyup(function(){
                        var tmptxt=$(this).val();
                        //$(this).val(tmptxt.replace(/\D|^0/g,''));
                        if(tmptxt.length == 8){
                            handleFrameList('ice');
                             $(this).val("");
                        }
                    }).bind("paste",function(){
                            var tmptxt=$(this).val();
                            $(this).val(tmptxt.replace(/\D|^0/g,''));
                    });
                    
                    
                    $("input[name='input_merge_frame']").keyup(function(){
                        var tmptxt=$(this).val();
                        //$(this).val(tmptxt.replace(/\D|^0/g,''));
                        if(tmptxt.length == 8){
                            handleMergeFrameList($(this).val());
                             $(this).val("");
                        }
                    }).bind("paste",function(){
                            var tmptxt=$(this).val();
                            $(this).val(tmptxt.replace(/\D|^0/g,''));
                    });
                    
                </script>
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                <!--<input class="submit style_yellow" type="button" value="获取商品信息" onclick="javascript:getProductName();">-->
                <!-- <input class="submit style_gray" type="button" value="取消" onclick="javascript:cancel();"> -->
            </div>
        </div>
        <div style="display: none">
            <div comment="Used for alert but hide">
                <audio id="player" src="view/sound/redalert.wav">
                    Your browser does not support the <code>audio</code> element.
                </audio>
            </div>
        </div>
        <div style="float: none; clear: both"><hr style="border: 0.1em #999 dashed"></div>
        <div id='move_list' align="center" style="display:none; margin:0.5em auto;">
            <!-- Insert Move List -->
            <table id="invMovesHold" border="0" style="width:100%;" cellpadding=2 cellspacing=3>
                <tr>
                    <th style="width:4.2em">类型</th>
                    <th style="">站点/添加时间</th>
                    <th style="width:2.4em">总数</th>
                    <th style="width:2.4em">操作</th>
                </tr>
                <tbody id="invMovesInfo">
                <!-- Scanned Product List -->
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                </tbody>
            </table>
        </div>
    </div>

    <div id="print" style="display: none">
        <div id='invMovesPrint' align="center" style="margin:0.5em auto;">
            <!-- Insert Move List -->


            <div class="noprint"><input class="submit_s style_gray" type="button" value="返回主页" onclick="javascript:backhome();"></div>

            <div id="invMovesPrintCaption" style="padding: 0 5px;">类型:<span id="prtInvMoveType"></span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;门店:<span id="prtInvMoveTitle"></span>&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;添加时间:<span id="prtInvMoveTime"></span></div>
            <div style=" padding: 10px 5px;">
                <table id="invMovesPrintHold" border="0" style="width:100%;"  cellpadding=0 cellspacing=0>
                    <tr>
                        <th align="left" style="width:4em">商品ID</th>
                        <th align="left" style="">商品名称</th>
                        <th style="width:4em">价格</th>
                        <th style="width:3em">数量</th>
                        <th style="width:3em">备注</th>
                    </tr>
                    <tbody id="invMovesPrintInfo">
                        <!-- Scanned Product List -->
                    </tbody>
                </table>
            </div>
            <div style="padding: 0 5px; display: block; float: none; clear: both">
                <div style="float: right">
                    合计数量: <span id="invMovesPrintQtyTotal">0</span>(件)&nbsp;&nbsp;&nbsp;
                    合计金额: <span id="invMovesPrintAmountTotal">0</span>(元)
                </div>
            </div>
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
    
    
    $(document).ready(function(){
        $('#orderStation').change(function(){
    
            var p1=$(this).children('option:selected').val();//这就是selected的值
            $('#ordersList tr').each(function () {
                if(p1 == 0){
                    $(this).show();
                }
                else{
                    if(p1 == $(this).attr("station_id")){
                        $(this).show();
                    }
                    else{
                        $(this).hide();
                    }
                }
            });
        })
    }) 
    
    
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

    var inventory_user = 0;
    if(is_admin == 0){
        inventory_user = '<?php echo $_COOKIE['inventory_user'];?>';
    }

    $(document).ready(function () {
        startTime();
        var warehouse_id = $("#warehouse_id").text();

         var html = '<td colspan="6">正在载入...</td>';
                $('#ordersList').html(html);


        $.ajax({
            type : 'POST',
            url : 'invapi.php?vali_user=1',
            data : {
                method : 'getInventoryUserOrder',
                warehouse_id : warehouse_id,
                date : '<?php echo $date_array[2]['date']; ?>'
            },
            success : function (response , status , xhr){
                console.log(response);

                if(response){
                    var jsonData = $.parseJSON(response);
                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w.php';
                    }
                    
                    window.inventory_user_order = jsonData;
                    
                    
                }
            }
        });






        //Get RegMethod
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getOrders',
                station_id: 1,
                //inventory_user: inventory_user,
                date : '<?php echo $date_array[2]['date']; ?>'
                
            },
            success : function (response , status , xhr){
                //console.log(response);

                if(response){

                    //释放刷新按钮
                    $('#reload').attr('class',"invopt");
                    $('#reload').removeAttr('disabled');
                    $('#reload').html("刷新");
                    
                    var jsonData = $.parseJSON(response);



                    if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=w.php";
                    }

                    var html = '';
                    
                    var each_i_num = 1;
                    var each_i_num_new = 0;
                    var each_i_num_kuai = 501;
                    var each_i_num_veg = 0;
                    var num_flag = true;
                    $.each(jsonData.data, function(index, value){

                        var planQty = parseInt(value.plan_quantity);
                        var dueQty = parseInt(value.quantity);
                        console.log("订单:"+value.order_id+", 商品数:"+value.plan_quantity+", 未分拣:"+value.quantity)

                        //快消订单跳过，新界面分拣
                        if(is_admin == 0 && value.station_id == 2 && isSepicalUser == 0){
                            return true;
                        }

                        if(value.station_id == 2&&num_flag){
                            num_flag = false;
                            each_i_num_veg = each_i_num;
                            each_i_num_new = each_i_num;
                        }
                        
                        if(window.inventory_user_order[value.order_id] || <?php echo !in_array($_COOKIE['inventory_user'], $inventory_user_admin) ? 0 : 1;?> ){
                        var t_status_class = '';
                        var product_str = '';
                        
                        
                        if(value.order_product_type == 1){
                            product_str = '菜';
                        }
                        if(value.order_product_type == 2){
                            t_status_class = "style = 'background-color:#ffff00;'";
                            product_str = '菜+奶';
                        }
                        if(value.order_product_type == 3 && value.station_id == 1){
                            t_status_class = "style = 'background-color:#9933ff;'";
                            product_str = '奶';
                        }
                        
                        
                        
                        
                        
                        if(value.order_status_id == 1){
                            t_status_class = "style = 'background-color:#ffff99;'";
                            
                        }
                        if(value.order_status_id == 2){
                            //t_status_class = "";
                        }
                        if(value.order_status_id == 3){
                            t_status_class = "style = 'background-color:#666666;'";
                        }
                        if(value.order_status_id == 5){
                            //t_status_class = "";
                        }
                        if(value.order_status_id == 6){
                            //t_status_class = "";
                        }
                        
                        
                        html += '<tr station_id="'+value.station_id+'">';
                        html += '<td '+t_status_class+'>';
                        
                        if(value.is_bao == 1){
                            html += '<span style="color:red;">爆</span><br>';
                        }
                        if(value.station_id == 2){
                            
                            each_i_num = each_i_num_kuai+(each_i_num_new - each_i_num_veg);

                            html += '<span style="color:red;">快</span><br />';
                        }

                        if(value.group_id > 1 && parseInt(value.station_id) == 2){
                            html += '<span style="color:red; font-size:0.9rem">'+value.group_shortname+'</span><br />';
                        }
                        
                        html += '<input type="hidden" id="order_frame_count_'+value.order_id+'" value="'+value.frame_count+'">';
                        html += '<input type="hidden" id="order_incubator_count_'+value.order_id+'" value="'+value.incubator_count+'">';
                            html += '<input type="hidden" id="order_foam_count_'+value.order_id+'" value="'+value.foam_count+'">';
                            html += '<input type="hidden" id="order_frame_mi_count_'+value.order_id+'" value="'+value.frame_mi_count+'">';
                            html += '<input type="hidden" id="order_incubator_mi_count_'+value.order_id+'" value="'+value.incubator_mi_count+'">';
                            html += '<input type="hidden" id="order_frame_ice_count_'+value.order_id+'" value="'+value.frame_ice_count+'">';
                            html += '<input type="hidden" id="order_box_count_'+value.order_id+'" value="'+value.box_count+'">';
                            
                            html += '<input type="hidden" id="order_frame_meat_count_'+value.order_id+'" value="'+value.frame_meat_count+'">';
                            html += '<input type="hidden" id="order_frame_vg_list_'+value.order_id+'" value="'+value.frame_vg_list+'">';
                            
                            html += '<input type="hidden" id="order_frame_meat_list_'+value.order_id+'" value="'+value.frame_meat_list+'">';
                            html += '<input type="hidden" id="order_frame_mi_list_'+value.order_id+'" value="'+value.frame_mi_list+'">';
                            html += '<input type="hidden" id="order_frame_ice_list_'+value.order_id+'" value="'+value.frame_ice_list+'">';
                            
                            html += '<input type="hidden" id="order_foam_ice_count_'+value.order_id+'" value="'+value.foam_ice_count+'">';
                            
                        html += '<input type="hidden" id="order_inv_comment_'+value.order_id+'" value="'+value.inv_comment+'">';
                        
                        html += '<input type="hidden" id="order_each_num_'+value.order_id+'" value="'+each_i_num+'">';
                        
                        html += '<span id="order_num_'+ value.order_id +'">'+each_i_num+'</span><br>'+product_str+'</td>';
                        html += '<td '+t_status_class+'>';
                            html += value.order_id;

                            //标记闪电购订单
                            if(parseInt(value.customer_id) == 8765){
                                html += '<br /><span style="color:red; font-size: 0.8rem">'+ value.shipping_name +'</span>';
                            }

                            if(value.district == '浦东' && parseInt(value.station_id) == 2){
                                html += '<br /><span style="color:red;">浦东</span>';
                            }

                            if(value.is_nopricetag == 1){
                                html += '<br /><span style="color:red;">无价签</span>';
                            }

                            if(parseInt(value.inv_comment) > 0){
                                html += '<br><span style="color:darkgreen; font-weight: bold;">[货位' + value.inv_comment + ']</span>';
                            }
                        
                        html +='<input type="hidden" id="shipping_name_'+value.order_id+'" value="'+value.shipping_name+'"><input type="hidden" id="shipping_phone_'+value.order_id+'" value="'+value.shipping_phone+'"><input type="hidden" id="shipping_address_'+value.order_id+'" value="'+value.shipping_address_1+'"></td>';
                            
                            if(<?php echo !in_array($_COOKIE['inventory_user'], $inventory_user_admin) ? 1 : 0;?> > 0){
                                if(window.inventory_user_order[value.order_id][1] > 0){
                                   html += '<td '+t_status_class+'>'+window.inventory_user_order[value.order_id][1]+'</td>'; 
                                   html += '<td '+t_status_class+'>'+(window.inventory_user_order[value.order_id][1] - value.inv_type_1)+'</td>';
                                }
                                else if(window.inventory_user_order[value.order_id][2] > 0 ){
                                    html += '<td '+t_status_class+'>'+window.inventory_user_order[value.order_id][2]+'</td>';
                                    html += '<td '+t_status_class+'>'+(window.inventory_user_order[value.order_id][2] - value.inv_type_2)+'</td>';
                                }
                                else if(window.inventory_user_order[value.order_id][3] > 0 ){
                                    html += '<td '+t_status_class+'>'+window.inventory_user_order[value.order_id][3]+'</td>';
                                    html += '<td '+t_status_class+'>'+(window.inventory_user_order[value.order_id][3] - value.inv_type_3)+'</td>';
                            }
                                else if(window.inventory_user_order[value.order_id][4] > 0){
                                    html += '<td '+t_status_class+'>'+window.inventory_user_order[value.order_id][4]+'</td>';
                                    html += '<td '+t_status_class+'>'+(window.inventory_user_order[value.order_id][4] - value.inv_type_4)+'</td>';
                            }
                                else if(window.inventory_user_order[value.order_id][5] > 0 ){
                                    html += '<td '+t_status_class+'>'+window.inventory_user_order[value.order_id][5]+'</td>';
                                    html += '<td '+t_status_class+'>'+(window.inventory_user_order[value.order_id][5] - value.inv_type_3)+'</td>';
                            }
                                 else if(window.inventory_user_order[value.order_id][6] > 0 ){
                                    html += '<td '+t_status_class+'>'+window.inventory_user_order[value.order_id][6]+'</td>';
                                    html += '<td '+t_status_class+'>'+(window.inventory_user_order[value.order_id][6] - value.inv_type_6)+'</td>';
                            }
                                 else if(window.inventory_user_order[value.order_id][7] > 0 ){
                                    html += '<td '+t_status_class+'>'+window.inventory_user_order[value.order_id][7]+'</td>';
                                    html += '<td '+t_status_class+'>'+(window.inventory_user_order[value.order_id][7] - value.inv_type_7)+'</td>';
                                }
                            }
                            else{
                        html += '<td '+t_status_class+'>'+value.plan_quantity+'</td>';
                        html += '<td '+t_status_class+'>'+value.quantity+'</td>';
                            }
                            
                            
                            
                            
                            
                            
                            
                        html += '<td '+t_status_class+'>'+value.added_by+'</td>';
                        html += '<td '+t_status_class+'>'+value.name+'</td>';
                        html += '<td '+t_status_class+'><button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderInventoryView('+value.order_id+','+value.station_id+');">查看</button>';
                        if((value.order_status_id == 2 || value.order_status_id == 5 || value.order_status_id == 8) && value.no_inv != 1){
                            html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+','+value.station_id+');">开始分拣</button>';
                        }
                        
                        /*
                        if(is_admin == 1){
                            html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+');">提交分拣</button>';
                        }
                        */
                        html += '</td>';
                        html += '</tr>';
                        html += '';
                        }
                        each_i_num++;
                        if(each_i_num_new != 0){
                            each_i_num_new++;
                        }
                    });
                    
                        
                        
                        
                        
                        
                    
                    
                    $('#ordersList').html(html);

                    console.log('Load Stations');
                }
            },
            complete : function(){
                $.ajax({
                    type : 'POST',
                    url : 'invapi.php?vali_user=1',
                    data : {
                        method : 'getOrderStatus'
                    },
                    success : function (response , status , xhr){
                        //console.log(response);

                        if(response){
                            var jsonData = eval(response);
                            if(jsonData.status == 999){
                                alert("未登录，请登录后操作");
                                window.location = 'inventory_login.php?return=w.php';
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

                $.ajax({
                    type : 'POST',
                    url : 'invapi.php?vali_user=1',
                    data : {
                        method : 'getProductWeightInfo',
                        date : '<?php echo $date_array[2]['date']; ?>'
                        
                    },
                    success : function (response , status , xhr){
                        //console.log(response);
                
                        if(response){
                            window.product_weight_info =  $.parseJSON(response);
                
                        }
                    }
                });
                
                
                
            }
            
        });
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

    function playOverdueAlert(){
        //$('#player').attr('src',sound);
        $('.simpleplayer-play-control').click();
    }

    function stopOverdueAlert(){
        $('.simpleplayer-stop-control').click();
    }

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


    function orderInventory(order_id,station_id){
        $('#invMethods').hide();
        $("#orderListTable").hide();
        $('#productList').show();
        var order_num = $("#order_num_"+order_id).html();
        var order_frame_count = $("#order_frame_count_"+order_id).val();
        var order_incubator_count = $("#order_incubator_count_"+order_id).val();
        var order_frame_mi_count = $("#order_frame_mi_count_"+order_id).val();
        var order_incubator_mi_count = $("#order_incubator_mi_count_"+order_id).val();
        var order_frame_ice_count = $("#order_frame_ice_count_"+order_id).val();
        var order_box_count = $("#order_box_count_"+order_id).val();
        var order_frame_meat_count = $("#order_frame_meat_count_"+order_id).val();
        var order_frame_vg_list = $("#order_frame_vg_list_"+order_id).val();
        var order_frame_meat_list = $("#order_frame_meat_list_"+order_id).val();
        var order_frame_mi_list = $("#order_frame_mi_list_"+order_id).val();
        var order_frame_ice_list = $("#order_frame_ice_list_"+order_id).val();
        var order_foam_ice_count = $("#order_foam_ice_count_"+order_id).val();
        var order_foam_count = $("#order_foam_count_"+order_id).val();
        var order_inv_comment = $("#order_inv_comment_"+order_id).val();
        var order_each_num = $("#order_each_num_"+order_id).val();
        
        $("#frame_count").val(order_frame_count);
        $("#incubator_count").val(order_incubator_count);
        $("#foam_count").val(order_foam_count);
        $("#frame_mi_count").val(order_frame_mi_count);
        $("#incubator_mi_count").val(order_incubator_mi_count);
        $("#frame_ice_count").val(order_frame_ice_count);
        $("#box_count").val(order_box_count);
        $("#frame_meat_count").val(order_frame_meat_count);
        $("#frame_vg_list").val(order_frame_vg_list);
        $("#foam_ice_count").val(order_foam_ice_count);
        $("#inv_comment").val(order_inv_comment);

        $(".fm_frame_count").val(order_frame_count);
        $(".fm_inv_comment").val(order_inv_comment);
        $(".fm_box_count").val(order_box_count);
        $(".fm_frame_vg_list").val(order_frame_vg_list);

         if(order_frame_vg_list != ''){
            var order_frame_vg_arr = order_frame_vg_list.split(",");
            for(var i=0;i<order_frame_vg_arr.length;i++){
                var frame_num = order_frame_vg_arr[i];
                var frame_num_html = '<div id="frame_vg_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'vg\','+frame_num+');">X</span></div>';
                $("#vg_list").append(frame_num_html);
                $(".fm_vg_list").append(frame_num_html);
            }
        }
        if(order_frame_meat_list != ''){
            var order_frame_meat_arr = order_frame_meat_list.split(",");
            for(var i=0;i<order_frame_meat_arr.length;i++){
                var frame_num = order_frame_meat_arr[i];
                var frame_num_html = '<div id="frame_meat_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'meat\','+frame_num+');">X</span></div>';
                $("#meat_list").append(frame_num_html);
            }
        }
        if(order_frame_mi_list != ''){
            var order_frame_mi_arr = order_frame_mi_list.split(",");
            for(var i=0;i<order_frame_mi_arr.length;i++){
                var frame_num = order_frame_mi_arr[i];
                var frame_num_html = '<div id="frame_mi_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'mi\','+frame_num+');">X</span></div>';
                $("#mi_list").append(frame_num_html);
            }
        }
        if(order_frame_ice_list != ''){
            var order_frame_ice_arr = order_frame_ice_list.split(",");
            for(var i=0;i<order_frame_ice_arr.length;i++){
                var frame_num = order_frame_ice_arr[i];
                var frame_num_html = '<div id="frame_ice_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'ice\','+frame_num+');">X</span></div>';
                $("#ice_list").append(frame_num_html);
            }
        }
        
        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>'+'鲜世纪订单分拣－'+order_id+'('+order_num+')'+'<br>'+$("#shipping_name_"+order_id).val()+' - '+order_each_num+$("#shipping_address_"+order_id).val()+'<br>'+$("#shipping_phone_"+order_id).val());
        $("#current_order_id").val(order_id);
        getOrderSortingList(order_id,0,station_id);
    }
    
    function orderInventoryView(order_id,station_id){

        $('#invMethods').hide();
        
        $("#orderListTable").hide();
        $('#productList').show();
        
        
        var order_num = $("#order_num_"+order_id).html();
        var order_frame_count = $("#order_frame_count_"+order_id).val();
        var order_foam_count = $("#order_foam_count_"+order_id).val();
        var order_incubator_count = $("#order_incubator_count_"+order_id).val();
        var order_frame_mi_count = $("#order_frame_mi_count_"+order_id).val();
        var order_incubator_mi_count = $("#order_incubator_mi_count_"+order_id).val();
        var order_frame_ice_count = $("#order_frame_ice_count_"+order_id).val();
        var order_box_count = $("#order_box_count_"+order_id).val();
        var order_frame_meat_count = $("#order_frame_meat_count_"+order_id).val();
        var order_frame_vg_list = $("#order_frame_vg_list_"+order_id).val();
        var order_frame_meat_list = $("#order_frame_meat_list_"+order_id).val();
        var order_frame_mi_list = $("#order_frame_mi_list_"+order_id).val();
        var order_frame_ice_list = $("#order_frame_ice_list_"+order_id).val();
        var order_foam_ice_count = $("#order_foam_ice_count_"+order_id).val();
        var order_inv_comment = $("#order_inv_comment_"+order_id).val();
        var order_each_num = $("#order_each_num_"+order_id).val();
        $("#frame_count").val(order_frame_count);
        $("#incubator_count").val(order_incubator_count);
        $("#inv_comment").val(order_inv_comment);
        $("#frame_mi_count").val(order_frame_mi_count);
        $("#incubator_mi_count").val(order_incubator_mi_count);
        $("#frame_ice_count").val(order_frame_ice_count);
        $("#box_count").val(order_box_count);
        $("#frame_meat_count").val(order_frame_meat_count);
        $("#frame_vg_list").val(order_frame_vg_list);
        $("#frame_meat_list").val(order_frame_meat_list);
        $("#frame_mi_list").val(order_frame_mi_list);
        $("#frame_ice_list").val(order_frame_ice_list);
        $("#foam_ice_count").val(order_foam_ice_count);
        $("#foam_count").val(order_foam_count);

        $(".fm_frame_count").val(order_frame_count);
        $(".fm_inv_comment").val(order_inv_comment);
        $(".fm_box_count").val(order_box_count);
        $(".fm_frame_vg_list").val(order_frame_vg_list);
        
        if(order_frame_vg_list != ''){
            var order_frame_vg_arr = order_frame_vg_list.split(",");
            for(var i=0;i<order_frame_vg_arr.length;i++){
                var frame_num = order_frame_vg_arr[i];
                var frame_num_html = '<div id="frame_vg_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'vg\','+frame_num+');">X</span></div>';
                $("#vg_list").append(frame_num_html);
                $(".fm_vg_list").append(frame_num_html);
            }
        }
        if(order_frame_meat_list != ''){
            var order_frame_meat_arr = order_frame_meat_list.split(",");
            for(var i=0;i<order_frame_meat_arr.length;i++){
                var frame_num = order_frame_meat_arr[i];
                var frame_num_html = '<div id="frame_meat_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'meat\','+frame_num+');">X</span></div>';
                $("#meat_list").append(frame_num_html);
            }
        }
        if(order_frame_mi_list != ''){
            var order_frame_mi_arr = order_frame_mi_list.split(",");
            for(var i=0;i<order_frame_mi_arr.length;i++){
                var frame_num = order_frame_mi_arr[i];
                var frame_num_html = '<div id="frame_mi_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'mi\','+frame_num+');">X</span></div>';
                $("#mi_list").append(frame_num_html);
            }
        }
        if(order_frame_ice_list != ''){
            var order_frame_ice_arr = order_frame_ice_list.split(",");
            for(var i=0;i<order_frame_ice_arr.length;i++){
                var frame_num = order_frame_ice_arr[i];
                var frame_num_html = '<div id="frame_ice_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\'ice\','+frame_num+');">X</span></div>';
                $("#ice_list").append(frame_num_html);
            }
        }
        
        $('#logo').html('<button class="invopt" style="display: inline;float:left" onclick="javascript:location.reload();">返回</button>'+'鲜世纪订单分拣－'+order_id+'<br>'+$("#shipping_name_"+order_id).val()+' - '+order_each_num+$("#shipping_address_"+order_id).val()+'<br>'+$("#shipping_phone_"+order_id).val());
        $("#current_order_id").val(order_id);
        $("#product").hide();
        
        
        
        
        getOrderSortingList(order_id,1,station_id);
    }



    function inventoryMethodHandler(method){
        var methodId = "#"+method;
        $('#method').val(method);
        $('#label').html($(methodId).text());

        $('#invMethods').hide();
        $('#message').hide();
        $('#move_list').hide();

        $('#productList').show();
        $('title').html($(methodId).text() + '-鲜世纪库存管理');
        $('#logo').html('鲜世纪库存管理－'+$(methodId).text());

        if(method == 'inventoryIn'){
            $('#getplanned').show();
            //getSortingList('<?php echo $date_array[2]['date']; ?>');
        }
        else{
            $('#getplanned').hide();
        }


        if($('#station').val() > 0){
            $('#station').attr('disabled',"disabled");
        }
        locateInput();
    }

    function getOrderSortingList(order_id,is_view,station_id){
        $('#barcodescanner').show();
        window.product_inv_barcode_arr = {};
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getOrderSortingList',
                order_id : order_id,
                is_view : is_view
            },
            success : function (response , status , xhr){
                var html = '<td colspan="4">正在载入...</td>';
                $('#productsInfo').html(html);

                if(response){
                    console.log("Sorting Data:" + response);

                    var jsonData = $.parseJSON(response);

                    if(jsonData.status == 1){
                        html = '';
                        var count_plan_quantity = 0;
                        var count_quantity = 0;
                        
                        var order_been_over = "";
                        var order_been_over_size = "";
                        var boxCount = 0;
                        window.product_id_arr = {};
                        
                        
                        if(window.inventory_user_order[order_id]&& window.inventory_user_order[order_id][1]){
                            $("#inv_do_mi").remove();
                            $("#inv_do_ice").remove();
                            $("#inv_do_meat").remove();
                        }
                        
                        if(window.inventory_user_order[order_id]&& window.inventory_user_order[order_id][2]){
                            $("#inv_do_vg").remove();
                            $("#inv_do_ice").remove();
                            $("#inv_do_meat").remove();
                        }
                        if(window.inventory_user_order[order_id]&& (window.inventory_user_order[order_id][3]|| window.inventory_user_order[order_id][5])){
                            $("#inv_do_vg").remove();
                            $("#inv_do_mi").remove();
                            $("#inv_do_meat").remove();
                        }
                        if(window.inventory_user_order[order_id]&&  window.inventory_user_order[order_id][4]){
                            $("#inv_do_vg").remove();
                            $("#inv_do_mi").remove();
                            $("#inv_do_ice").remove();
                        }

                        if(station_id == 1){
                            $("#fastmove_order_comment").remove();
                        }
                        else{
                            $("#inv_do_vg").remove();
                            $("#inv_do_mi").remove();
                            $("#inv_do_meat").remove();
                            $("#inv_do_ice").remove();
                        }

                        if(is_view){
                            $("#fastmove_order_comment").show();
                            $("#submit_num").show();
                        }
                        else{
                            $("#fastmove_order_comment").hide();
                            $("#submit_num").hide();
                        }
                        
                        
                        
                        $.each(jsonData.data, function(index,value){
                            
                            window.product_barcode_arr[value.product_id] = {};
                            window.product_barcode_arr_s[value.product_id] = {};
                            window.product_inv_barcode_arr[value.product_id] = value.product_barcode_arr;
                            
                            if((window.inventory_user_order[order_id]&& window.inventory_user_order[order_id][1] && value.product_type_id == 1) ||
                                (window.inventory_user_order[order_id]&&window.inventory_user_order[order_id][2] && value.product_type_id == 2) ||
                                (window.inventory_user_order[order_id]&&window.inventory_user_order[order_id][3] && value.product_type_id == 3) ||
                                (<?php echo in_array($_COOKIE['inventory_user'], $inventory_user_admin) ? 1 : 0;?> > 0)
                            )
                            {
                                count_plan_quantity = parseInt(value.plan_quantity) + parseInt(count_plan_quantity);
                                count_quantity = parseInt(value.quantity) + parseInt(count_quantity);
                                if(value.repack == 0 && !isNaN(value.boxCount) && value.boxCount>0){
                                    boxCount += value.boxCount;
                                }
                                console.log("BOX["+value.product_id+"]:" + value.boxCount);

                                if(value.barcode > 0){
                                    product_id_arr[value.barcode] = value.product_id;
                                }
                                if(value.sku > 0){
                                    product_id_arr[value.sku] = value.product_id;
                                }
                                product_id_arr[value.product_id] = value.product_id;

                                if(value.quantity > 0){
                                    order_been_over = "";
                                    order_been_over_size = "";
                                }
                                else{
                                    order_been_over = "style = 'background-color:#666666;'";
                                    order_been_over_size = "style = 'background-color:#666666;font-size:2em;'";;

                                }



                                html += '<tr class="barcodeHolder"  id="bd'+ value.product_id +'">' +
                                    '<td '+order_been_over+' class="prodlist" id="td'+value.product_id+'" >';
                                html += value.inv_class_sort + ' ';
                                html += '<span name="productBarcode" style="display:none;" >' + value.product_id + '</span>' ;



                                html += '[<span name="productId" id="pid'+value.product_id+'">' + value.product_id + '</span>]';
                                if(parseInt(value.repack) ==1 && parseInt(value.station_id) == 2){
                                    html += '<span style="color:red">[散]</span>';
                                }
                                html += '<br />';
                                html += '<span id="info';

                                html += value.product_id;

                                html += '"></span>      </td>' +
                                    '<td '+order_been_over_size+' align="center" class="prodlist" style="font-size:2em;">'+ value.plan_quantity +'</td>' +
                                    '<td '+order_been_over+' align="center" class="prodlist"><input class="qty" id="'+ value.product_id +'" name="'+ value.product_id +'" value="'+value.quantity+'" /><input type="hidden" id="plan'+ value.product_id +'" value="'+value.quantity+'"><input type="hidden" id="old_plan'+ value.product_id +'" value="'+value.plan_quantity+'"><input type="hidden" id="do'+value.product_id+'" value="0"><input type="hidden" id="pur_plan'+value.product_id+'" value="'+value.purchase_plan_id+'"></td>' +
                                    '<td '+order_been_over+' id="opera'+value.product_id+'">';
                                if(value.quantity > 0 ){

                                    if(is_admin == 1 && is_view == 0){
                                        html +=    '<input class="qtyopt pda_add_inv_'+value.product_id+'"  type="button" value="+" onclick="javascript:qtyadd(\''+ value.product_id +'\')" >' +
                                        '<input class="qtyopt style_green pda_add_inv_'+value.product_id+'" type="button" value="-" onclick="javascript:qtyminus2(\''+ value.product_id +'\')" >'+
                                        '<input class="qtyopt style_green pda_add_inv_'+value.product_id+'"  type="button" value="提交" onclick="javascript:tjStationPlanProduct2(\''+ value.product_id +'\')" >';
                                    }

                                    html += '';
                                }
                                else{
                                    html += '已完成';
                                }
                            
                                html +=   '</td>' + '</tr>';
                            }
                            
                            
                            
                        });

                        console.log(product_id_arr);
                        console.log( window.product_inv_barcode_arr);
                        
                        $("#count_plan_quantity").html(count_plan_quantity);
                        $("#count_quantity").html(count_quantity);
                        
                        $('#productsInfo').html(html);

                        console.log('BoxCount:'+boxCount);
                        if(boxCount > 0){
                            $('.fm_box_count').val(parseInt(boxCount));
                            $('#nonRepackBoxCount').text(parseInt(boxCount));
                        }
                        else{
                            $('.fm_box_count').val(0);
                            $('#nonRepackBoxCount').text(0);
                        }
                    }
                    else if(jsonData.status == -1){
                        alert('采购数据已获取并提交入库!');
                        $('#productsInfo').html('');
                    }
                    else if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=w.php";
                    }
                     else if(jsonData.status == 6){
                        alert('此订单已提交，不能重复分拣!');
                        $('#productsInfo').html('');
                    }
                    else{
                        alert('无采购数据!');
                        $('#productsInfo').html('');
                    }
                }
            },
            complete : function(){
                getProductName();
            }
        });
    }

    function chooseStation(){
        if($('#station').val() > 0){
            $('#barcodescanner').show();
            $('#product').focus();

            if($('#method').val()){
                $('#station').attr('disabled',"disabled");
            }
        }
        else{
            $('#product').blur();
            $('#barcodescanner').hide();
        }
    }

    function checkStation(){
        if($('#station').val() <= 0){
            alert('请选择站点');

            $('#product').blur();
            $('#station').focus();

            return false;
        }

        return true;
    }

    function getSetDate(dateGap,dateFormart) {
        var dd = new Date();
        dd.setDate(dd.getDate()+dateGap);//获取AddDayCount天后的日期

        return dd.Format(dateFormart);

        //console.log(getSetDate(1,'yyMMdd')); //Tomorrow
        //console.log(getSetDate(-1,'yyMMdd')); //Yesterday
    }

    function addProduct(id){
        //var id = parseInt(id);

        //Barcode rules for Code128(18) OR Ean13(13||12)
        //18: 6+6+6
        //12: 1+5+5+x
        //13: 2+5+5+x

        if(id !== ''){
            if(id.length == 18){
                var productId = parseInt(id.substr(6,6));
                var price = parseInt(id.substr(12,6))/100;

                if(productId > 3000){
                    alert('非法的商品编号');
                    return false;
                }
            }
            //else if(id.length == 12 || id.length == 13){
            //    var productId = parseInt(id.substr(1-(12-id.length),5));
            //    var price = parseInt(id.substr(1-(12-id.length)+5,5))/100;
            //}
            else{
                alert('错误的条码');
                return false;
            }

            if(productId == NaN || price == NaN){
                console.log('Error barcode format');
                return;
            }

            var html = '<tr class="barcodeHolder" id="bd'+ id +'">' +
                '<td><span name="productBarcode" style="display: none" >' + id + '</span><span name="productId" >' + productId + '</span><br /><span id="info'+ id +'"></span></td>' +
                '<td>'+ price +'</td>' +
                '<td><input class="qty" id="'+ id +'" name="'+ id +'" value="1" readonly="readonly" /></td>' +
                '<td>' +
                '<input class="qtyopt" type="button" value="+" onclick="javascript:qtyadd(\''+ id +'\')" >' +
                '<input class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus(\''+ id +'\')" >' +
                '</td>' +
                '</tr>';
            $('#productsInfo').append(html);
            $('#product').val('');

            var todayDate = getSetDate(0,'yyMMdd');
            var tomorrowDate = getSetDate(1,'yyMMdd');
            var productDueDate = parseInt(id.substr(0,6));
            //console.log(todayDate+'---'+productDueDate);

            var shelfLifeStrictText = '['+$('#shelfLifeStrict').text()+']';
            var shelfLifeStrict = eval(shelfLifeStrictText);

            //console.log(shelfLifeStrict[0]);

            var prodInfo = "#info"+id;
            var method = $('#method').val();

            //执行报损提醒，盘点时播放报警，报损时只更改颜色
            if(method =='inventoryInit' || method =='inventoryBreakage'){
                if($.inArray(productId,shelfLifeStrict) >=0 && tomorrowDate >= productDueDate){

                    if(method =='inventoryInit'){
                        playOverdueAlert();
                    }
                    markBarCodeLine('#bd'+id, "#FDDB00");

                    $(prodInfo).html('严格品控，建议报损');
                }
                else if(todayDate > productDueDate){
                    if(method =='inventoryInit'){
                        playOverdueAlert();
                    }
                    markBarCodeLine('#bd'+id, "#EE0000");

                    $(prodInfo).html('已过期，删除并报损');
                }
            }
        }
    }


    function markBarCodeLine(barCodeId,color){
        //console.log('Marked Barcode line'+barCodeId);
        $(barCodeId+' td').css('backgroundColor',color);
    }

    function locateInput(){
        $('#product').focus();
    }

    function getProductBarcodeList(){
        var prodList = '';
        var m = 0;
        $('#productsInfo tr').each(function () {
            var productBarcode = $(this).find('span[name=productBarcode]').text();

            if(m == $("#productsInfo tr").length-1){
                prodList += productBarcode;
            }
            else{
                prodList += productBarcode+',';
            }

            m++;
        });

        return prodList;
    }

    function getProductBarcodeWithQty(){
        var prodList = '';
        var m = 0;
        $('#productsInfo tr').each(function () {
            var productBarcode = $(this).find('span[name=productBarcode]').text();
            var productBarcodeId = '#'+productBarcode;
            var productBarcodeQty = $(productBarcodeId).val();

            if(m == $("#productsInfo tr").length-1){
                prodList += productBarcode+':'+productBarcodeQty+'';
            }
            else{
                prodList += productBarcode+':'+productBarcodeQty+',';
            }

            m++;
        });

        return prodList;
    }

    function getProductName(){
        console.log('Get products name from barcode');

        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }

        var prodList = getProductBarcodeList();

        if(prodList == '' || prodList == null){
            alert('获取条码列表错误或还没有输入商品条码。');
            return false;
        }

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getSortingProductInfo',
                products : prodList
            },
            success : function (response , status , xhr){
                if(response){
                    //console.log(response);
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);

                    $.each(jsonData, function(index,value){
                        var infoId = "#info"+index;
                        $(infoId).html(value);
                    });
                }
            },
            complete : function(){

            }
        });

    }

    function clickProductInput(){
        $('#move_list').hide();
    }

    function applyFrameCount(){
        $(".fm_frame_count").val($("#vg_list div").length);
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
        var frame_num_html = '<div id="frame_'+frame_type+'_'+frame_num+'">'+frame_num+' <span class="frame_num" onclick="remove_frame(\''+frame_type+'\','+frame_num+');">X</span></div>';
        $("#"+frame_type+"_list").append(frame_num_html);

        applyFrameCount();
    }

    function handleMergeFrameList(frame_num){

        
        frame_num = frame_num.substr(0,6);//Get 18 code
        
        var frame_vg_list = $("#frame_vg_list").val();
        var frame_mi_list = $("#frame_mi_list").val();
        var frame_meat_list = $("#frame_meat_list").val();
        var frame_ice_list = $("#frame_ice_list").val();
        
        var has_frame_num = false;
         
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

            $("#frame_vg_"+frame_num).remove();
            $("#frame_vg_list").val(frame_vg_list);
            $("#frame_vg_count").val(parseInt($("#frame_vg_count").val()) - 1);
            has_frame_num = true;
        }
        
        if(frame_ice_list.indexOf(frame_num) != -1 ){
            if(frame_ice_list.indexOf(','+frame_num) != -1){

                frame_ice_list=frame_ice_list.replace(','+frame_num,'' );
            }
            else if(frame_ice_list.indexOf(frame_num+',') != -1){

                frame_ice_list=frame_ice_list.replace(frame_num+',','' );
            }
            else{

                frame_ice_list=frame_ice_list.replace(frame_num,'' );
            }
            $("#frame_ice_"+frame_num).remove();
            $("#frame_ice_list").val(frame_ice_list);
            $("#frame_ice_count").val(parseInt($("#frame_ice_count").val()) - 1);
            has_frame_num = true;
        }
        
        if(frame_meat_list.indexOf(frame_num) != -1 ){
            if(frame_meat_list.indexOf(','+frame_num) != -1){

                frame_meat_list=frame_meat_list.replace(','+frame_num,'' );
            }
            else if(frame_meat_list.indexOf(frame_num+',') != -1){

                frame_meat_list=frame_meat_list.replace(frame_num+',','' );
            }
            else{

                frame_meat_list=frame_meat_list.replace(frame_num,'' );
            }
            $("#frame_meat_"+frame_num).remove();
            $("#frame_meat_list").val(frame_meat_list);
            $("#frame_meat_count").val(parseInt($("#frame_meat_count").val()) - 1);
            has_frame_num = true;
        }
        
        if(frame_mi_list.indexOf(frame_num) != -1 ){
            if(frame_mi_list.indexOf(','+frame_num) != -1){

                frame_mi_list=frame_mi_list.replace(','+frame_num,'' );
            }
            else if(frame_mi_list.indexOf(frame_num+',') != -1){

                frame_mi_list=frame_mi_list.replace(frame_num+',','' );
            }
            else{

                frame_mi_list=frame_mi_list.replace(frame_num,'' );
            }
            $("#frame_mi_"+frame_num).remove();
            $("#frame_mi_list").val(frame_mi_list);
            $("#frame_mi_count").val(parseInt($("#frame_mi_count").val()) - 1);
            has_frame_num = true;
        }
        if(has_frame_num == false){
            alert("输入错误，要合并弃用的框号不在分拣提交的框号内");
        }
        else{
        
            var frame_num_html = '<div>'+frame_num+' </div>';
            $("#merge_list").append(frame_num_html);
            
    }

    }

    function remove_frame(frame_type,frame_num){
        
        $("#frame_"+frame_type+"_"+frame_num).remove();
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

    function handleProductList(){
        
        var rawId = $('#product').val();
        
        id = rawId.substr(0,18);//Get 18 code

        //13位编码，已29开头，为内码，精选商品专用
        //if(id.length == 13 && parseInt(id.substr(0,2)) == 29){
        //    id = parseInt(id.substr(2,5));
        //}
        
        if(id.length == 0){
            return false;
        }
        var scan_barcode = id;
        window.scanBarcode = id;
        
        /*
        if(id.length == 18){
           
            id = parseInt(id.substr(6,6));
            if(window.product_weight_info[id]){
                alert("此商品需按重出库，请更换商品标签");
                return false;
        }
        }
        */
        
        
        if(id.length == 4 && !check_in_array(id,window.no_scan_product_id_arr)){
            alert("不能输入商品ID，必须扫描");
            return false;
        }
        
        
        if(id.length == 4){
           
            id = parseInt(id);
            if(window.product_weight_info[id]){
                alert("此商品需按重出库，请扫描标签出库");
                return false;
            }
        }
        
        if(id.length == 18 || id.length == 16){
            id = parseInt(id.substr(0,4));
            if(window.product_weight_info[id]){
            //奇偶校验
            var jiou_count = 0;
            for(i=0;i<scan_barcode.length-1;i++){
                if(i%2 == 0){
                    jiou_count += (parseInt(scan_barcode.substr(i,1))*3);
                }
                else{
                    jiou_count += parseInt(scan_barcode.substr(i,1));
                }
            }
            var jiou_count_str = jiou_count.toString();
            
            
            var jiou_count_last = parseInt(jiou_count_str.substr(jiou_count_str.length-1,1));
            var jiaoyanma = 10 - jiou_count_last;
            var jiaoyanma_str = jiaoyanma.toString();
            var jiaoyanma_last = parseInt(jiaoyanma_str.substr(jiaoyanma_str.length-1,1));

            if(jiaoyanma_last != parseInt(scan_barcode.substr(scan_barcode.length-1,1))){
                alert("非法条码");
                return false;
            }
            
            //重量检测
            var product_weight =  parseInt(scan_barcode.substr(4,5));
            if(window.product_weight_info[id]){
                var standard_weight = parseInt(window.product_weight_info[id]['weight']);
            }
            else{
                alert("此商品不需按重出库，请更换条码");
                return false;
            }
            
                var weight_propo = parseFloat((product_weight / standard_weight).toFixed(2));
            
                if(weight_propo < parseFloat(window.product_weight_info[id]['weight_range_least'])){
                alert("商品包装过少，不合格");
                return false;
            }
                if(weight_propo > parseFloat(window.product_weight_info[id]['weight_range_most'])){
                alert("商品包装过多，不合格");
                return false;
            }
            }
            
        }
        //id = parseInt(id);
        
        if(window.product_id_arr[id] > 0){
            
            id = window.product_id_arr[id];
            var barCodeId = "#bd"+id;
            
            $('#product').val('');
            if($("#"+id).val() == 0){
                
                    var player = $("#player2")[0];  
                    player.play();
                    alert("此商品已完成分拣，不要重复分拣");
                    return false;
            }

            var current_do_product = $("#current_do_product").val();
            
            
            
            if(current_do_product == 0){
                
                showOverlay();
                
                $("#current_do_product").val(id);
                
                $("#product_name").html($("#info"+id).html());
                $("#current_do_tj").html('<span id="tj'+id+'" style="" onclick="javascript:tjStationPlanProduct(\''+id+'\')" class="invopt">提交</span>');
                $("#current_product_plan").html($("#old_plan"+id).val()+'<span style="display:none;" name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>');
                $("#current_product_quantity").html( '<input class="qty"  id="'+id+'" value="'+$("#"+id).val()+'"><input type="hidden" id="plan'+ id +'" value="'+$("#plan"+id).val()+'"><input type="hidden" id="old_plan'+ id +'" value="'+$("#old_plan"+id).val()+'"><input type="hidden" id="do'+id+'" value="0">');
                
                if(window.product_weight_info[id] && window.product_weight_info[id]['weight'] ){
                    $("#current_product_quantity_change").html('');
                }else{
                $("#current_product_quantity_change").html('<input style="height:3em;width:3em;" class="qtyopt" type="button" value="+" onclick="javascript:qtyadd(\''+id+'\')"><input style="height:3em;width:3em;" class="qtyopt style_green" type="button" value="-" onclick="javascript:qtyminus(\''+id+'\')">');
                }
               
                
                
               
                $("#productsHoldDo").show();
                
                $(barCodeId).remove();
                hideOverlay();
                
                
            }
            else{
                if(current_do_product == id){
                    qtyminus(id);
                }
                else{
                    /*
                    if(window.product_weight_info[current_do_product]){
                        if(confirm("当前商品还未完成分拣，确认提交？")){
                            tjStationPlanProduct(current_do_product);
                        }
                    }
                    else{
                    var player = $("#player2")[0];  
                    player.play();

                    alert("当前商品还未完成分拣，请先提交后再分拣其它商品");
                }
                    */
                    var player = $("#player2")[0];  
                        player.play();
                    if(confirm("当前商品还未完成分拣，确认提交？")){
                        tjStationPlanProduct(current_do_product);
                    }
                
                    
            }

            }





            console.log('Add exist product barcode:'+id);
            
        }
        else{
        
            var player = $("#player2")[0];  
            player.play();
            alert("此商品不需入库");
            $("#product").val("");
            //addProduct(id);
            console.log('Add product barcode:'+id);
        }
    }

    function qtyadd(id){
        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 1;
        
        var do_qty = parseInt($("#do"+id).val()) - 1;
          
        
        if(qty >= parseInt($("#plan"+id).val())){
            qty = parseInt($("#plan"+id).val());
            do_qty = 0;
            
        }
     
        $(prodId).val(qty);
        $("#do"+id).val(do_qty);
        //locateInput();

        console.log(id+':'+qty);
    }

/*
var arr = ['a','b','c','d'];
arr.splice($.inArray('c',arr),1);
alert(arr);
*/
    function qtyminus_weight(id,scan_barcode){
        
        var prodId = "#"+id;
        var qty = parseInt($(prodId).val()) + 1;
        
        var do_qty = parseInt($("#do"+id).val()) - 1;
          
        
        if(qty >= parseInt($("#plan"+id).val())){
            qty = parseInt($("#plan"+id).val());
            do_qty = 0;
            
        }
     
        $(prodId).val(qty);
        $("#do"+id).val(do_qty);
          delete window.product_barcode_arr[id][scan_barcode];
          delete window.product_barcode_arr_s[id][scan_barcode];
         $("#"+id+"_"+scan_barcode).remove();

    }

    function qtyminus(id){
        var prodId = "#"+id;
        
        
        
        scan_barcode = window.scanBarcode;
        
        if(window.product_weight_info[id]){
            if(window.product_barcode_arr_s[id][scan_barcode] == scan_barcode){
                alert("此商品已经扫描分拣了，不能重复扫描分拣同一件商品");
                return false;
            }
            if(window.product_inv_barcode_arr[id]){
                var inv_product_barcode_flag = false;
                 $.each(window.product_inv_barcode_arr[id], function(index,value){
                     if(value == scan_barcode){
                        alert("此商品已经扫描分拣了，不能重复扫描分拣同一件商品");
                        inv_product_barcode_flag = true;
                        return false;
                     }
                 })
                 if(inv_product_barcode_flag){
                     return false;
                 }
            }
        
        }
        
        if($(prodId).val() >= 1){
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);
            
            var do_qty = parseInt($("#do"+id).val()) + 1;
            $("#do"+id).val(do_qty);
            
            if(window.product_weight_info[id]){
                window.product_barcode_arr[id][scan_barcode] = scan_barcode;
                window.product_barcode_arr_s[id][scan_barcode] = scan_barcode;
                $("#current_product_quantity_change").append('<span id="'+id+'_'+scan_barcode+'">'+scan_barcode+'<input style="" class="qtyopt_weight style_green" type="button" value="-" onclick="javascript:qtyminus_weight(\''+id+'\',\''+scan_barcode+'\')"><br></span>');
                
            }else{
            if(scan_barcode.length == 18){
                window.product_barcode_arr[id][scan_barcode] = scan_barcode;
            }
            }
            console.log(window.product_barcode_arr);
            
        }
        if($(prodId).val() == 0){
           
            if(window.product_weight_info[id]&&window.product_weight_info[id]['weight']&&false){
                
            }
            else{
            //提交插入中间表
            addOrderProductStation(id);
            hideOverlay();
            
            
            var html = '';
            
            
            html += '<tr class="barcodeHolder"  id="bd'+ id +'">' +
                                '<td style = "background-color:#666666;" class="prodlist" id="td'+id+'" ><span name="productBarcode" style="display:none;" >' + id + '</span> <span name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>	<br /><span id="info'+ id +'">'+$("#product_name").html()+'</span>      </td>' +
                                '<td style = "background-color:#666666; font-size:2em;" align="center" class="prodlist" >'+ $("#old_plan"+id).val() +'</td>' +
                                '<td style = "background-color:#666666;" align="center" class="prodlist"><input class="qty" id="'+ id +'" name="'+ id +'" value="'+$("#"+id).val()+'" /><input type="hidden" id="plan'+ id +'" value="'+id+'"><input type="hidden" id="do'+id+'" value="0"></td>' +
                                '<td style = "background-color:#666666;" id="opera'+id+'">';
            
            html += '已完成';
            

            html +=   '</td>' +
                '</tr>';
            
            $("#productsInfo").append(html);
            
            $("#current_do_product").val(0);
            $("#productsHoldDo").hide();
            $("#product_name").html("");
            }
            
            
        }

        //locateInput();

        console.log(id+':'+qty);
    }

    
    
    
    
    
    function qtyminus2(id){
        var prodId = "#"+id;
        
        if($(prodId).val() >= 1){
            var qty = parseInt($(prodId).val()) - 1;
            $(prodId).val(qty);
            
            var do_qty = parseInt($("#do"+id).val()) + 1;
            $("#do"+id).val(do_qty);
        }
        if($(prodId).val() == 0){
           
            //提交插入中间表
            addOrderProductStation(id);
            hideOverlay();
            
            
            
            $(".pda_add_inv_"+id).hide();
            
            $("#current_do_product").val(0);
            $("#productsHoldDo").hide();
            $("#product_name").html("");
            
        }

        //locateInput();

        console.log(id+':'+qty);
    }

    function tjStationPlanProduct(id){
        addOrderProductStation(id);
        
        hideOverlay();
         var html = '';
        html += '<tr class="barcodeHolder"  id="bd'+ id +'">' +
                            '<td class="prodlist" id="td'+id+'" ><span name="productBarcode" style="display:none;" >' + id + '</span> <span name="productId" id="pid'+id+'">' + $("#pid"+id).html() + '</span>	<br /><span id="info'+ id +'">'+$("#product_name").html()+'</span>      </td>' +
                            '<td align="center" class="prodlist" style="font-size:2em;">'+ $("#old_plan"+id).val() +'</td>' +
                            '<td align="center" class="prodlist"><input class="qty" id="'+ id +'" name="'+ id +'" value="'+$("#"+id).val()+'" /><input type="hidden" id="plan'+ id +'" value="'+$("#plan"+id).val()+'"><input type="hidden" id="old_plan'+ id +'" value="'+$("#old_plan"+id).val()+'"><input type="hidden" id="do'+id+'" value="0"></td>' +
                            '<td id="opera'+id+'">';

        html += '';


        html +=   '</td>' +
            '</tr>';

        $("#productsInfo").append(html);

        $("#current_do_product").val(0);
        $("#productsHoldDo").hide();
        $("#product_name").html("");
        
        
    }

function tjStationPlanProduct2(id){
        addOrderProductStation(id);

        hideOverlay();
         

        $("#current_do_product").val(0);
        $("#productsHoldDo").hide();
        $("#product_name").html("");
        
        
    }


    function addOrderProductStation(id){
    
        
        var order_id = $('#current_order_id').val();
    
        var product_id = $("#pid"+id).html();
        var product_quantity = $("#do"+id).val();
      
        if(product_quantity > parseInt($("#plan"+id).val())){
            product_quantity = $("#plan"+id).val();
        }
      
        if(product_quantity == 0){
            var player = $("#player3")[0];  
            player.play();
            return false;
        }
      
        showOverlay();
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'addOrderProductStation',
                order_id : order_id,
                product_id : product_id,
                product_quantity : product_quantity,
                product_barcode_arr : window.product_barcode_arr
            },
            success : function (response, status, xhr){
                console.log(response);
                if(response){
                    
                    var jsonData = $.parseJSON(response);
                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w.php';
                    }

                    $(".pda_add_inv_"+id).hide();
                    
                    
                    window.product_barcode_arr[id] = {};
                    
                    //var jsonData = eval(response);
                    var player = $("#player3")[0];  
                    player.play();
                        
                        
                    $("#plan"+id).val($("#plan"+id).val()-product_quantity);
                        
                    $("#count_quantity").html(parseInt($("#count_quantity").html()) - parseInt(product_quantity));    
                        
                    var jsonData = $.parseJSON(response);
                    return jsonData.status;
                }
            }
        });
    } 


    function delOrderProductToInv(){
        if(confirm("是否确认删除已分拣数据？")){
           var order_id = $('#current_order_id').val();

            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : 'delOrderProductToInv',
                    order_id : order_id
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);
                        hideOverlay();


                        if(jsonData.status == 999){
                            alert("未登录，请登录后操作");
                            window.location = 'inventory_login.php?return=w.php';
                        }

                        if(jsonData.status == 1){
                            alert("分拣数据已删除");
                            location.reload();
                        }
                       
                        if(jsonData.status == 2){
                            alert("订单分拣数据已提交，无法删除");
                        }
                        
                    }
                }
            });              
        }
    }


    function batchProcessAddOrderProductToInv(orderList){
        console.log(orderList);

        //return false;
        showOverlay();
        $.each(orderList, function(i,order_id){
            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                cache: false,
                async: false,
                data : {
                    method : 'addOrderProductToInv',
                    order_id : order_id
                },
                success : function (response, status, xhr){
                    if(response){
                        //var jsonData = eval(response);
                        var jsonData = $.parseJSON(response);

                        if(jsonData.status == 0){
                            alert("["+order_id+"]部分商品未提交入库成功，请重试");
                            hideOverlay();
                            return false;
                        }

                        if(jsonData.status == 2){
                            alert("无待确认提交的商品");
                            hideOverlay();
                            return false;
                        }

                        if(jsonData.status == 4){
                            alert("每个订单不能重复提交分拣数据");
                            hideOverlay();
                            return false;
                        }

                        if(jsonData.status == 5){
                            alert("["+order_id+"]分拣数量超过订单数量 或 有重复提交相同条码的商品，请删除分拣数据重新分拣 "+jsonData.timestamp);
                            hideOverlay();
                            return false;
                        }

                        if(jsonData.status == 6){
                            alert("订单已经提交过，如果继续提交分拣数量就会超过订单数量，请检查");
                            hideOverlay();
                            return false;
                        }
                    }
                }
            });
        });
        hideOverlay();
        getOrderByStatus();
    }


    function addOrderProductToInv_pre(){
        showOverlay();
        var order_id = $('#current_order_id').val();
         $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'addOrderProductToInv_pre',
                order_id : order_id
            },
            success : function (response, status, xhr){
                var jsonData = $.parseJSON(response);
               
               
               if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w.php';
                    }
               
                if(jsonData.status == 1){
                    if(confirm("计划分拣数量"+jsonData.plan_quantity+"，实际分拣"+jsonData.do_quantity+"，是否确认提交完成？")){
                        addOrderProductToInv();
                    }else{
                        return false;
                    }
                }
                if(jsonData.status == 0){
                    alert("今天已经提交过了，不能重复提交");
                     hideOverlay();
                }
                if(jsonData.status == 2){
                    alert("无待确认提交的商品");
                     hideOverlay();
                }
            }
        });
    }


    function addOrderProductToInv(){



        var order_id = $('#current_order_id').val();




        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'addOrderProductToInv',
                order_id : order_id
            },
            success : function (response, status, xhr){
                if(response){
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);
                    hideOverlay();
                    
                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w.php';
                    }
                    
                    if(jsonData.status == 1){
                        alert("提交商品入库成功");
                    }
                    if(jsonData.status == 0){
                        alert("部分商品未提交入库成功，请重试或联系管理员");
                    }
                    if(jsonData.status == 2){
                        alert("无待确认提交的商品");
                    }
                    if(jsonData.status == 4){
                        alert("每个订单不能重复提交分拣数据");
                    }

                    if(jsonData.status == 5){
                        alert("分拣数量超过订单数量 或 有重复提交相同条码的商品，请删除分拣数据重新分拣 "+jsonData.timestamp);
                    }
                    if(jsonData.status == 6){
                        alert("订单已经提交过，如果继续提交分拣数量就会超过订单数量，请检查");
                    }
                }
            }
        });

        addOrderNum();
    }




    function addOrderNum(){
        showOverlay();
        
        var order_id = $('#current_order_id').val();
        var frame_count= '';
        var incubator_count = '';
        var foam_count = '';
        var inv_comment = '';
        var frame_mi_count = '';
        var incubator_mi_count = '';
        var frame_ice_count = '';
        var box_count = '';
        var foam_ice_count = '';
        var frame_meat_count = '';
        
        var frame_vg_list = '';
        var frame_meat_list = '';
        var frame_mi_list = '';
        var frame_ice_list = '';
        

        if(window.inventory_user_order[order_id]&&window.inventory_user_order[order_id][1]){
            var add_type = 1;
        }
        if(window.inventory_user_order[order_id]&&window.inventory_user_order[order_id][2]){
            var add_type = 2;
        }
        if(window.inventory_user_order[order_id]&&(window.inventory_user_order[order_id][3]||window.inventory_user_order[order_id][5])){
            var add_type = 4;
        }
        if(window.inventory_user_order[order_id]&&window.inventory_user_order[order_id][4]){
            var add_type = 5;
        }
        <?php if(in_array($_COOKIE['inventory_user'], $inventory_user_admin)){?> 
            var add_type = 3;
        <?php } ?>
        
        
        if($("#frame_count")){
             frame_count = $("#frame_count").val();
        }
        if($("#incubator_count")){
             incubator_count = $("#incubator_count").val();
        }
        if($("#foam_count")){
             foam_count = $("#foam_count").val();
        }
        if($("#inv_comment")){
             inv_comment = $("#inv_comment").val();
        }
        if($("#frame_mi_count")){
             frame_mi_count = $("#frame_mi_count").val();
        }
        if($("#incubator_mi_count")){
             incubator_mi_count = $("#incubator_mi_count").val();
        }
        if($("#frame_ice_count")){
             frame_ice_count = $("#frame_ice_count").val();
        }
        if($("#box_count")){
             box_count = $("#box_count").val();
        }
        if($("#frame_meat_count")){
             frame_meat_count = $("#frame_meat_count").val();
        }
        if($("#foam_ice_count")){
             foam_ice_count = $("#foam_ice_count").val();
        }
        if($("#frame_vg_list")){
             frame_vg_list = $("#frame_vg_list").val();
        }
        if($("#frame_meat_list")){
             frame_meat_list = $("#frame_meat_list").val();
        }
        if($("#frame_mi_list")){
             frame_mi_list = $("#frame_mi_list").val();
        }
        if($("#frame_ice_list")){
             frame_ice_list = $("#frame_ice_list").val();
        }
        
        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'addOrderNum',
                order_id : order_id,
                frame_count : frame_count,
                inv_comment : inv_comment,
                incubator_count : incubator_count,
                foam_count : foam_count,
                frame_mi_count : frame_mi_count,
                incubator_mi_count : incubator_mi_count,
                frame_ice_count : frame_ice_count,
                box_count : box_count,
                frame_meat_count : frame_meat_count,
                foam_ice_count : foam_ice_count,
                add_type : add_type,
                frame_vg_list : frame_vg_list,
                frame_meat_list : frame_meat_list,
                frame_mi_list : frame_mi_list,
                frame_ice_list : frame_ice_list
            },
            success : function (response, status, xhr){
                console.log(response);
                if(response){
                    //var jsonData = eval(response);
                    var jsonData = $.parseJSON(response);
                    hideOverlay();
                    
                    
                    if(jsonData.status == 999){
                        alert("未登录，请登录后操作");
                        window.location = 'inventory_login.php?return=w.php';
                    }

                    if(jsonData.status == 99){
                        alert(jsonData.msg);
                    }
                    
                    if(jsonData.status == 1){
                        alert("提交框数成功");
                    }
                    if(jsonData.status == 0){
                        alert("提交失败");
                    }
                    if(jsonData.status == 11){
                        alert("提交的框数和框号数量不符，请检查");
                    }
                    if(jsonData.status == 12){
                        alert(jsonData.timestamp);
                    }
                    if(jsonData.status == 13){
                        alert(jsonData.timestamp);
                    }
                     if(jsonData.status == 14){
                        alert(jsonData.timestamp);
                    }
                    if(jsonData.status == 15){
                        alert(jsonData.timestamp);
                    }
                    if(jsonData.status == 16){
                        alert(jsonData.timestamp);
                    }
                    if(jsonData.status == 17){
                        alert(jsonData.timestamp);
                    }
                }
            }
        });
        hideOverlay();


    }









    function inventoryProcess(){
        var method = $('#method').val();
        var prodListWithQty = getProductBarcodeWithQty();
        var station = $('#station').val();

        console.log('Process inventory method:'+method);
        console.log('Process inventory data:'+prodListWithQty);

        if(method == ''){
            alert('请确认操作类型。');
            return false;
        }

        if(!checkStation()){
            return false;
        }

        if(prodListWithQty == '' || prodListWithQty == null ){
            alert('获取条码列表错误或还没有输入商品条码。');
            return false;
        }

        if(confirm('确认提交此次［'+$('#'+method).text()+'］操作？')){
            $('#submit').attr('class',"submit style_gray");
            $('#submit').attr('value',"正在提交...");

            $.ajax({
                type : 'POST',
                url : 'invapi.php',
                data : {
                    method : method,
                    station : station,
                    products : prodListWithQty,
                    purchase_plan_id : $('#purchasePlanId').val()
                },
                success : function (response , status , xhr){
                    if(response){
                        //console.log(response);


                        var jsonData = $.parseJSON(response);
                        if(jsonData.status){
                            $('#message').attr('class',"message style_ok");
                            $('#productsInfo').html('');
                            $('#productList').hide();
                            $('#invMethods').show();

                            console.log('Inv. Process OK');
                        }
                        else{
                            $('#message').attr('class',"message style_error");
                            console.log('Inv. Process Error.');
                        }

                        $('#message').show();
                        $('#message').html(jsonData.msg);
                    }
                },
                complete : function(){
                    $('#submit').attr('class',"submit");
                    $('#submit').attr('value',"提交");
                }
            });
        }
    }

    function getInvMoveList(gap){
        //$('#move_list').html(222);
        console.log('Get Station Move List In ['+gap+'] Day(s).');

        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }

        var station = $('#station').val();

        $('#move_list').show();

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getStationMove',
                station : station,
                date_gap : gap
            },
            success : function (response , status , xhr){
                var html = '<td colspan="4">正在载入...</td>';
                $('#invMovesInfo').html(html);

                if(response){
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    html = '';
                    $.each(jsonData, function(index,value){
                        if(value.inventory_type_id !== '5'){
                            html += '<tr>' +
                                '<td align="center"><span id="invMoveType_'+ value.inventory_move_id +'">' + value.move_name + '</span></td>' +
                                '<td><span style="display:none" id="invMoveConcact_'+ value.inventory_move_id +'">' + value.contact_name +","+ value.contact_phone + '</span><span id="invMoveTitle_'+ value.inventory_move_id +'">' + value.station_title + '</span><br /><span id="invMoveTime_'+ value.inventory_move_id +'">' + value.date_added + '</div></td>' +
                                '<td align="center">' + value.total_qty + '</td>' +
                                '<td><input class="submit_s style_yellow" type="button" value="查看" onclick="javascript:printInvMove('+value.inventory_move_id+');"></td>' +
                                '</tr>';
                        }
                    });

                    $('#invMovesInfo').html(html);
                }
            },
            complete : function(){

            }
        });

        //locateInput();
    }


    function printInvMove(invMoveId){
        $('#content').hide();
        $('#print').show();

        $('#prtInvMoveType').html( $('#invMoveType_'+invMoveId).html() );
        $('#prtInvMoveTitle').html( $('#invMoveTitle_'+invMoveId).html() +' (' + $('#invMoveConcact_'+invMoveId).html() + ')' );
        $('#prtInvMoveTime').html( $('#invMoveTime_'+invMoveId).html() );

        $('#printTime').html($('#currentTime').html());

        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }

        var station = $('#station').val();

        $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getStationMoveItem',
                station : station,
                invMoveId : invMoveId
            },
            success : function (response , status , xhr){
                var html = '<tr><td colspan="5">正在载入...</td></tr>';
                $('#invMovesPrintInfo').html(html);
                var invMovesPrintQtyTotal = 0;
                var invMovesPrintAmountTotal = 0;

                if(response){
                    console.log(response);
                    var jsonData = $.parseJSON(response);

                    html = '';
                    $.each(jsonData, function(index,value){
                        html += '<tr>' +
                            '<td align="left">' + value.product_id + '</td>' +
                            '<td align="left">' + value.name + '</td>' +
                            '<td align="center" style="font-size:14px; font-weight: bold">' + value.price + '</td>' +
                            '<td align="center" style="font-size:16px; font-weight: bold">' + value.quantity + '</td>' +
                            '<td></td>' +
                            '</tr>';
                        invMovesPrintQtyTotal += parseInt(value.quantity);
                        invMovesPrintAmountTotal += parseFloat(value.price)*parseInt(value.quantity);
                    });

                    $('#invMovesPrintInfo').html(html);
                    $('#invMovesPrintQtyTotal').html(invMovesPrintQtyTotal);
                    $('#invMovesPrintAmountTotal').html(invMovesPrintAmountTotal.toFixed(2));
                }
            },
            complete : function(){

            }
        });
    }

    function backhome(){
        $('#content').show();
        $('#print').hide();
    }

    function cancel(){
        if(confirm('确认取消此次操作，所有页面数据将不被保存！')){
            location=window.location.href;
        }

        return;
    }

    function checkStation(){
        if($('#station').val() == 0){
            alert('请选择站点，或者点击“退出”，重新载入。');
            return false;
        }
        return true;
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
                window.location = 'inventory_login.php?return=w.php';
            }
        });
        }
    }

    function getOrderByStatus(){
        var order_status_id = $("#orderStatus").val();
        var warehouse_id  = $("#warehouse_id").val();
         $.ajax({
            type : 'POST',
            url : 'invapi.php',
            data : {
                method : 'getOrders',
                station_id: 1,
                //inventory_user: inventory_user,
                date : '<?php echo $date_array[2]['date']; ?>',
                order_status_id : order_status_id,
                warehouse_id : warehouse_id,
                
            },
            success : function (response , status , xhr){
                //console.log(response);

                if(response){
                    
                    var jsonData = $.parseJSON(response);



                    if(jsonData.status == 999){
                        alert(jsonData.msg);
                        location.href = "inventory_login.php?return=w.php";
                    }

                    var html = '';
                    
                    var each_i_num = 1;
                    var each_i_num_new = 0;
                    var each_i_num_kuai = 501;
                    var each_i_num_veg = 0;
                    var num_flag = true;
                    var batchProcessOrderList = [];
                    $.each(jsonData.data, function(index, value){

                        //快消订单跳过，新界面分拣
                        if(is_admin == 0 && value.station_id == 2 && isSepicalUser == 0){
                            return true;
                        }

                        var t_status_class = '';
                        var product_str = '';


                        if(value.station_id == 2 && num_flag){
                            num_flag = false;
                            each_i_num_veg = each_i_num;
                            each_i_num_new = each_i_num;
                        }
                        
                        if(value.order_product_type == 1){
                            product_str = '菜';
                        }
                        if(value.order_product_type == 2){
                            t_status_class = "style = 'background-color:#ffff00;'";
                            product_str = '菜+奶';
                        }
                        if(value.order_product_type == 3){
                            t_status_class = "style = 'background-color:#9933ff;'";
                            product_str = '奶';
                        }
                        
                        
                        
                        
                        
                        if(value.order_status_id == 1){
                            t_status_class = "style = 'background-color:#ffff99;'";
                            
                        }
                        if(value.order_status_id == 2){
                            //t_status_class = "";
                        }
                        if(value.order_status_id == 3){
                            t_status_class = "style = 'background-color:#666666;'";
                        }
                        if(value.order_status_id == 5){
                            //t_status_class = "";
                        }
                        if(value.order_status_id == 6){
                            //t_status_class = "";
                        }
                        
                        
                        html += '<tr station_id="'+value.station_id+'">';
                        html += '<td '+t_status_class+'>';
                        if(value.is_bao == 1){
                            html += '<span style="color:red;">爆</span><br>';
                        }
                         if(value.station_id == 2){
                              each_i_num = each_i_num_kuai+(each_i_num_new - each_i_num_veg);
                            html += '<span style="color:red;">快</span><br>';
                        }
                        html += '<input type="hidden" id="order_frame_count_'+value.order_id+'" value="'+value.frame_count+'">';
                        html += '<input type="hidden" id="order_incubator_count_'+value.order_id+'" value="'+value.incubator_count+'">';
                        html += '<input type="hidden" id="order_foam_count_'+value.order_id+'" value="'+value.foam_count+'">';
                        html += '<input type="hidden" id="order_frame_mi_count_'+value.order_id+'" value="'+value.frame_mi_count+'">';
                        html += '<input type="hidden" id="order_incubator_mi_count_'+value.order_id+'" value="'+value.incubator_mi_count+'">';
                        html += '<input type="hidden" id="order_frame_ice_count_'+value.order_id+'" value="'+value.frame_ice_count+'">';
                        html += '<input type="hidden" id="order_box_count_'+value.order_id+'" value="'+value.box_count+'">';
                        html += '<input type="hidden" id="order_frame_meat_count_'+value.order_id+'" value="'+value.frame_meat_count+'">';
                        html += '<input type="hidden" id="order_frame_vg_list_'+value.order_id+'" value="'+value.frame_vg_list+'">';
                        
                        html += '<input type="hidden" id="order_frame_meat_list_'+value.order_id+'" value="'+value.frame_meat_list+'">';
                        html += '<input type="hidden" id="order_frame_mi_list_'+value.order_id+'" value="'+value.frame_mi_list+'">';
                        html += '<input type="hidden" id="order_frame_ice_list_'+value.order_id+'" value="'+value.frame_ice_list+'">';
                        
                        html += '<input type="hidden" id="order_foam_ice_count_'+value.order_id+'" value="'+value.foam_ice_count+'">';
                        html += '<input type="hidden" id="order_inv_comment_'+value.order_id+'" value="'+value.inv_comment+'">';
                        
                        html += '<input type="hidden" id="order_each_num_'+value.order_id+'" value="'+each_i_num+'">';
                        
                        html += '<span id="order_num_'+value.order_id+'">'+each_i_num+'</span><br>'+product_str+'</td>';
                        html += '<td '+t_status_class+'>'+value.order_id;
                        
                        if(value.customer_group_id == 2){
                            html += '<br><span style="color:red;">无价签</span>';
                        }

                        if(parseInt(value.inv_comment) > 0){
                            html += '<br><span style="color:darkgreen; font-weight: bold;">[货位' + value.inv_comment + ']</span>';
                        }
                        
                        html +='<input type="hidden" id="shipping_name_'+value.order_id+'" value="'+value.shipping_name+'"><input type="hidden" id="shipping_phone_'+value.order_id+'" value="'+value.shipping_phone+'"><input type="hidden" id="shipping_address_'+value.order_id+'" value="'+value.shipping_address_1+'"></td>';
                        html += '<td '+t_status_class+'>'+value.plan_quantity+'</td>';
                        html += '<td '+t_status_class+'>'+value.quantity+'</td>';
                        html += '<td '+t_status_class+'>'+value.added_by+'</td>';
                        html += '<td '+t_status_class+'>'+value.name+'</td>';
                        html += '<td '+t_status_class+'><button id="inventoryInView" class="invopt" style="display: inline" onclick="javascript:orderInventoryView('+value.order_id+','+value.station_id+');">查看</button>';
                        if((value.order_status_id == 2 || value.order_status_id == 5 || value.order_status_id == 8 ) && value.no_inv != 1){
                            html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+','+value.station_id+');">开始分拣</button>';
                        }
                        
                        /*
                        if(is_admin == 1){
                            html += '<button id="inventoryIn" class="invopt" style="display: inline" onclick="javascript:orderInventory('+value.order_id+');">提交分拣</button>';
                        }
                        */
                        html += '</td>';
                        html += '</tr>';
                        html += '';

                        if(parseInt(value.quantity) == 0 && parseInt(value.station_id) == 2 && parseInt(value.order_status_id) == 5 && batchProcessOrderList.length<10){
                            batchProcessOrderList.push(value.order_id);
                        }

                        
                        if(each_i_num_new != 0){
                            each_i_num_new++;
                        }
                    });

                    var batchProcessHtml = ''; //停止批量提交
//                    if(batchProcessOrderList.length){
//                        var batchProcessOrderListString = '';
//                        $.each(batchProcessOrderList,function(i,v){
//                            batchProcessOrderListString += v;
//                            if(i < batchProcessOrderList.length - 1){
//                                batchProcessOrderListString += ', ';
//                            }
//                        });
//                        batchProcessHtml += '<tr>' +
//                            '<td colspan="6" span=""><span style="color:#CC0000">[测试!]</span>批量提交未分拣量为0订单(仅快消，每10单一批)：'+batchProcessOrderListString+'</td>' +
//                            '<td><button class="invopt" onclick="javascript:batchProcessAddOrderProductToInv(['+batchProcessOrderList+'])">批量提交</button></td>' +
//                            '</tr>';
//                    }
                    html = batchProcessHtml + html;
                        
                        
                        
                        
                        
                    
                    
                    $('#ordersList').html(html);

                    console.log('Load Stations');
                }
            }
            
        });
        
    }

window.check_category_arr = new Array();
window.check_category_arr[0] = 72;
window.check_category_arr[1] = 74;
window.check_category_arr[2] = 157;




window.check_product_arr = new Array();
window.check_product_arr[0] = 6930;
window.check_product_arr[1] = 6931;
window.check_product_arr[2] = 6932;
window.check_product_arr[3] = 6933;
window.check_product_arr[4] = 6935;
window.check_product_arr[5] = 6936;
window.check_product_arr[6] = 6937;
window.check_product_arr[7] = 6938;
window.check_product_arr[8] = 6939;
window.check_product_arr[9] = 6940;
window.check_product_arr[10] = 6941;
window.check_product_arr[11] = 6942;
window.check_product_arr[12] = 6943;


window.check_product_arr[13] = 6934;
window.check_product_arr[14] = 6946;
window.check_product_arr[15] = 6947;
window.check_product_arr[16] = 6986;



 window.no_scan_product_id_arr = new Array();
    <?php foreach($no_scan_product_id_arr_w as $key=>$value){ ?>
        window.no_scan_product_id_arr[<?php echo $key;?>] = <?php echo $value;?>;
    <?php } ?>


function check_in_array(stringToSearch, arrayToSearch) {
 for (s = 0; s < arrayToSearch.length; s++) {
  thisEntry = arrayToSearch[s].toString();
  if (thisEntry == stringToSearch) {
   return true;
  }
 }
 return false;
}
    </script>
</body>
</html>