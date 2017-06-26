<html>
<head>
    <meta charset="UTF-8" />
    <title>电子秤用门店商品信息－<?php echo $now?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="api/js/jquery.min.js"></script>
    <style>
            /*COMMON CSS START*/
        html, body, div, object, pre, code, h1, h2, h3, h4, h5, h6, p, span, em,
        cite, del, a, img, ul, li, ol, dl, dt, dd, fieldset, legend, form,
        input, button, textarea, header, section, footer, article, nav, aside,
        menu, figure, figcaption {
            margin: 0;
            padding: 0;
            outline: none;
        }

        h1, h2, h3, h4, h5, h6, sup {
            font-size: 100%;
            font-weight: normal;
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
            display: block;
        }

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

        .float-right{
            float: right;
        }
        .float-left{
            float: left;
        }
        .close{
            float: none;
            clear: both;
        }
        .w80{
            width:80%;
        }
        .w20{
            width: 19.99%
        }
        .w70{
            width:70%;
        }
        .w30{
            width: 29.99%
        }
            /*COMMON CSS END*/


        #productsHold td{
            background-color:#dff0d8;
            color: #000;
            height: 2.5em;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold th{
            padding: 0.3em;
            background-color:#d0e9c6;
            color: #000;

            border-radius: 0.2em;
            box-shadow: 0.1em rgba(0, 0, 0, 0.2);
        }

        #productsHold tr.productlinehover td{
            background-color:#bce8f1;
        }


    </style>
</head>
<body>
<div class="content">
    <table id="productsHold" border="0" cellpadding=2 cellspacing=3>
        <thead>
            <th style="width:3em">编号</th>
            <th style="width:15em">名称</th>
            <th style="width:4em">单位</th>
            <th style="width:4em">单价</th>
            <th style="width:4em">单据1打印格式</th>
            <th style="width:3em">单据1条码格式</th>
            <th style="width:3em">使用日期</th>
            <th style="width:2em">文本1</th>
            <th style="width:2em">文本2</th>
            <th style="width:2em">皮重</th>
        </thead>

        <tbody id="productsInfo">
            <?php foreach($info as $val){ ?>
            <tr id="prod1002" class="productline">
                <td><?php echo $val['product_id']; ?></td>
                <td><?php echo $val['name']; ?></td>
                <td>
                    <?php
                    if($val['weight_inv_flag']){
                        echo '7';
                    }
                    else{
                        echo $val['unit_type'];
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if($val['is_selected']){
                        echo 99.9;
                    }
                    else{
                        if($val['weight_inv_flag']){
                            echo $val['price_500g'];
                        }
                        else{
                            echo $val['price'];
                        }
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if($val['is_selected']){
                        echo '13';
                    }
                    elseif( in_array($today, array('2016-03-05','2016-03-06','2016-03-07')) && in_array($val['product_id'], array(1106, 1086)) ){
                        echo '18';
                    }
                    else{
                        if($val['weight_inv_flag']){
                            if($weight && $noprice){
                                echo '12';
                            }
                            else{
                                echo '11';
                            }
                        }
                        else{
                            echo $val['print_type'];
                        }
                    }

                    ?>
                </td>
                <td><?php echo $val['barcode_type']; ?></td>
                <td><?php echo $val['shelf_life']; ?></td>
                <td>
                    <?php
                    if($val['weight_inv_flag'] && $weight){
                        echo $val['price_500g_title'];
                    }
                    else{
                        echo $val['unit'];
                    }
                    ?>
                </td>
                <td>
                    <?php
                        //if($val['is_selected']){
                            echo date("w",time() + 8*3600)+1;
                        //}
                    ?>
                </td>
                <td><?php echo $val['package_weight']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>