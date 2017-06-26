<html>
<head>
    <meta charset="UTF-8" />
    <title>鲜世纪商品信息－<?php echo $now?></title>
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
<?php if($mkt){ ?>
<div class="content">
    <table id="productsHold" border="0" cellpadding=2 cellspacing=3>
        <caption>鲜世纪生鲜商品信息[<?php echo $now?>]</caption>
        <thead>
            <th style="width:4em">一级分类</th>
            <th style="width:4em">二级分类</th>
            <th style="width:3em">商品ID</th>
            <th style="width:15em">名称</th>
            <th style="width:4em">现价</th>
            <th style="width:3em">规格</th>
            <th style="width:3em">保质期</th>
        </thead>

        <tbody id="productsInfo">
            <?php foreach($info as $val){ ?>
            <tr id="prod1002" class="productline">
                <td><?php echo $val['parent_cate']; ?></td>
                <td><?php echo $val['cate']; ?></td>
                <td><?php echo $val['product_id']; ?></td>
                <td><?php echo $val['name']; ?></td>
                <td><?php echo $val['price']; ?></td>
                <td><?php echo $val['unit']; ?></td>
                <td><?php echo $val['shelf_life']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } else{ ?>
<div class="content">
    <table id="productsHold" border="0" cellpadding=2 cellspacing=3>
        <caption>商品信息[<?php echo $now?>]</caption>
        <thead>
            <th style="width:4em">一级分类</th>
            <th style="width:4em">二级分类</th>
            <th style="width:3em">商品ID</th>
            <th style="width:15em">名称</th>
            <th style="width:4em">原价</th>
            <th style="width:4em">现价</th>
            <th style="width:4em">价格效期</th>
            <th style="width:3em">规格</th>
            <th style="width:3em">保质期</th>
            <th style="width:2em">重量</th>
            <th style="width:2em">单位</th>
            <th style="width:2em">以克重计</th>
            <th style="width:2em">采购参数</th>
        </thead>

        <tbody id="productsInfo">
        <?php foreach($info as $val){ ?>
        <tr id="prod1002" class="productline">
            <td><?php echo $val['parent_cate']; ?></td>
            <td><?php echo $val['cate']; ?></td>
            <td><?php echo $val['product_id']; ?></td>
            <td><?php echo $val['name']; ?></td>
            <td><?php echo $val['ori_price']; ?></td>
            <td><?php echo $val['price']; ?></td>
            <td><?php echo $val['date_start'].'~'.$val['date_end']; ?></td>
            <td><?php echo $val['unit']; ?></td>
            <td><?php echo $val['shelf_life']; ?></td>
            <td><?php echo $val['weight_amount']; ?></td>
            <td><?php echo $val['count_with_grams']; ?></td>
            <td><?php echo $val['unit_title']; ?></td>
            <td><?php echo $val['factor']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>
<?php } ?>
</body>
</html>