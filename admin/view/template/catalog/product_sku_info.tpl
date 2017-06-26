<html>
<head>
    <meta charset="UTF-8" />
    <title>鲜世纪商品原料信息－<?php echo $now?></title>
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
        <caption>鲜世纪商品原料信息[<?php echo $now?>]</caption>
        <thead>
            <th style="width:4em">商品编号</th>
            <th >商品名称</th>
            <th style="width:4em">商品状态</th>
            <th style="width:3em">负库存</th>
            <th style="width:4em">标准规格</th>
            <th style="width:3em">规格量</th>
            <th style="width:4em">按克计量</th>
            <th style="width:4em">按重出库</th>
            <th style="width:4em">原料编号</th>
            <th >原料名称</th>
            <th style="width:2em">类型</th>
            <th style="width:2em">规格</th>
            <th style="width:3em">重量</th>
        </thead>

        <tbody id="productsInfo">
            <?php foreach($skuinfo as $val){ ?>
            <tr id="prod1002" class="productline">
                <td><?php echo $val['product_id']; ?></td>
                <td><?php echo $val['name']; ?></td>
                <td><?php echo $val['status']; ?></td>
                <td><?php echo $val['safestock']; ?></td>
                <td><?php echo $val['unit']; ?></td>
                <td><?php echo $val['weight']; ?></td>
                <td><?php echo $val['weight_class']; ?></td>
                <td><?php echo $val['weight_inv_flag']; ?></td>
                <td><?php echo $val['sku_id']; ?></td>
                <td><?php echo $val['sku_name']; ?></td>
                <td><?php echo $val['weight_type']; ?></td>
                <td><?php echo $val['box_unit']; ?></td>
                <td><?php echo $val['weight500g']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>