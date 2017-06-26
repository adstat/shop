<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />
    <style>
        @media print{
            a,
            a:visited {
                text-decoration: none;
            }
            a,
            a[href]:after,
            abbr[title]:after,
            a[href^="javascript:"]:after,
            a[href^="#"]:after{
                content: "";
            }



            }
.no_print{display:none}
        .fontred{
            color: #e9322d;
        }
        
        .table{
            margin-bottom:10px;
            
        }
        
        
        
        .table-bordered2{
            border-top:1px solid black;
            border-left:1px solid black;
        }
        
        .table-bordered2 td{
            border-right:1px solid black;
            border-bottom:1px solid black;
            
            
        }
        
        
        .table-bordered3{
            width: 725px;
            border-top:1px solid black;
            border-left:1px solid black;
        }
        
        .table-bordered3 td{
            border-right:1px solid black;
            border-bottom:1px solid black;
            
            padding:2px;
            text-align:center;
            
        }
        
       .container h3{
           font-size: 18px;
           line-height: 20px;
       }
        
        
    </style>
</head>
<body>
<!-- 开始A4模板 -->
<div class="container" id="print_a4" style="display: block; ">
  <?php foreach ($orders as $order) { ?>
  <div style="page-break-after: always; width:725px">
    <h3><?php echo $order['store_name']; ?> 订单编号 #
        <?php
        if($user_group_id == ADMIN_VIEW){
            echo $order['orderid'].'[#'.substr($order['order_id'],-3).']';
        }
        else{
            echo $order['order_id'];
        }
        ?>

        <?php if(isset($order['order_inv_data']['inv_comment']) && $order['order_inv_data']['inv_comment']){ ?>
            <span style="float: right; font-size: 14px; line-height: 20px; ">分拣位[参考]: <?php echo $order['order_inv_data']['inv_comment']; ?></span>
        <?php } ?>
    </h3>
    
      <table class="table table-bordered2"  >
      
      <tbody >
        <tr >
          <td style="width: 40%;border-top:1px solid black;" >
            <b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
            <?php if ($order['invoice_no']) { ?>
            <b><?php echo $text_invoice_no; ?></b> <?php echo $order['invoice_no']; ?><br />
            <?php } ?>
            <b>配送日期: </b><?php echo $order['deliver_date']; ?>
            <br />
            <span style="font-size: 14px;">售后电话：400-991-7571</span>
          </td>
          <td style="border-top:1px solid black;">
              <b>收货联系：</b><?php echo $order['shipping_phone']; ?><br />
              <b>收货人：</b><?php echo $order['shipping_name']; ?><br />
              <b>收货地址：</b><?php echo $order['shipping_address']; ?>
          </td>
          <td style="border-top:1px solid black;">
              <img style="width:10em;float:right;" src="<?php echo SITE_URI;?>/www/admin/barcode/barcode.php?text=<?php echo $order['order_id'];?>" alt="barcode" />
          </td>
        </tr>
      </tbody>
    </table>


    <table class="table table-bordered2">
      <thead>
        <tr>
            <td><b>到店周转框</b></td><td><?php echo !empty($order['order_inv_data']) ? $order['order_inv_data']['frame_count'] + $order['order_inv_data']['frame_meat_count'] + $order['order_inv_data']['frame_mi_count'] + $order['order_inv_data']['frame_ice_count'] : 0;?></td>
            <td><b>到店保温箱数</b></td><td><?php echo !empty($order['order_inv_data']) ? $order['order_inv_data']['incubator_count'] + $order['order_inv_data']['incubator_mi_count'] : 0;?></td>
            <td><b>到店泡沫箱数</b></td><td><?php echo !empty($order['order_inv_data']) ? $order['order_inv_data']['foam_count'] + $order['order_inv_data']['foam_ice_count'] : 0;?></td>
            <td><b>到店纸箱数</b></td><td><?php echo !empty($order['order_inv_data']) ? $order['order_inv_data']['box_count'] : 0;?></td>
            <td><b>回收筐数</b></td><td></td>
            <td><b>回收保温箱数</b></td><td></td>
            <td><b>回收网笼</b></td><td></td>

        </tr>
      </thead>

    </table>
    


    <?php if ($order['comment']) { ?>
      <table class="table table-bordered2">
          <tr>
              <th width="9%"><b><?php echo $column_comment; ?></b></th>
              <td><?php echo $order['comment']; ?></td>
          </tr>
      </table>
    <?php } ?>

    <?php if($order['station_id'] == 1){ ?> <!-- 生鲜订单打印模板 -->
    <table class=" table-bordered3">
          <thead>
            <tr>
              <td width="55"><b>商品ID</b></td>
                <td width="45" class="text-right"><b>数量</b></td>
                <td><b>商品名称</b></td>
                <td width="65"><b>商品目录</b></td>
                <td width="30"><b>商品规格</b></td>
              <td width="30"><b>商品单价</b></td>
              <td width="75" align="center"><b>商品小计</b><br>(出库小计)</td>

              <?php if($has_inv_product == 1){ ?>
                <td width="75"><b>出库数量<br>(出库重量)</b></td>
                <td width="45"><b>缺货</b></td>
              <?php } ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($order['product'] as $product) { ?>
            <tr>
              <td><?php echo $product['product_id'] ?></td>
              <td class="text-right" style="font-size: 20px; line-height: 16px"><b><?php echo $product['quantity']; ?></b></td>
              <td><?php echo $product['name']; ?>
                <?php foreach ($product['option'] as $option) { ?>
                <br />
                &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                <?php } ?></td>
              <td><?php echo @$product['inv_class_name'].'-'.$product['cate_name']; ?></td>
              <td><?php echo $product['weight']; ?></td>

              <td><?php echo number_format($product['price'],2);?>
                  <?php if($product['weight_inv_flag'] == 1){ ?>
                    <?php echo "/" . $product['weight'];?>
                  <?php } ?>
              </td>
              <td align="center">
                  <?php echo number_format($product['total'],2); ?>
                  <?php if($product['weight_inv_flag']&&isset($all_order_product_weight_inv_arr[$order['order_id']][$product['product_id']])){ ?>
                    <?php echo "<br>(" . sprintf("%.2f",$all_order_product_weight_inv_arr[$order['order_id']][$product['product_id']]['total']) . ")";?>
                  <?php } ?>
              </td>

              <?php if($has_inv_product == 1){ ?>
              <td><?php echo $product['inv_product_quantity'];?>
                  <?php if($product['weight_inv_flag']&&isset($all_order_product_weight_inv_arr[$order['order_id']][$product['product_id']])){ ?>
                    <?php echo "<br>(" . sprintf("%.2f",$all_order_product_weight_inv_arr[$order['order_id']][$product['product_id']]['weight_total']) . ")";?>
                  <?php } ?>
              </td>
              <td><?php echo isset($product['inv_product_quantity']) ? $product['quantity'] - $product['inv_product_quantity'] : $product['quantity'];?></td>
              <?php } ?>
            </tr>
            <?php } ?>
          </tbody>
        </table>
    <?php } else { ?>  <!-- 快消订单打印模板 -->
        <table class=" table-bordered3">
            <caption style="text-align: left">订单商品明细（“现场记录退货缺货”一栏不可涂改，“缺货数量”一栏已注明的部分，无需再记录）</caption>
            <thead>
            <tr>
                <td rowspan="2" width="55"><b>商品ID</b></td>
                <td rowspan="2" width="45" class="text-right"><b>数量</b></td>
                <td rowspan="2"><b>商品名称</b></td>
                <td rowspan="2" width="65"><b>商品目录</b></td>
                <td rowspan="2" width="50"><b>单价</b></td>
                <td rowspan="2" width="50" align="center"><b>小计</b></td>

                <?php if($has_inv_product == 1){ ?>
                <td rowspan="2" width="45"><b>出库数量</b></td>
                <td rowspan="2" width="45"><b>缺货数量</b></td>
                <?php } ?>

                <td width="158" style="text-align:center; padding: 1px;" colspan="3"><b>现场记录退货缺货(涂改无效)</b></td>
            </tr>

            <tr>
                <td width="48" height="12" style="font-size: 10px; padding: 1px; text-align:center;">退货</td>
                <td width="45" height="12" style="font-size: 10px; padding: 1px; text-align:center;">缺货</td>
                <td width="65" height="12" style="font-size: 10px; padding: 1px; text-align:center;">物流签收</td>
            </tr>

            </thead>
            <tbody>
            <?php foreach ($order['product'] as $product) { ?>
            <tr>
                <td style=" "><?php echo $product['product_id'] ?></td>
                <td class="text-right" style="font-size: 14px; line-height: 10px"><?php echo $product['quantity']; ?></td>
                <td><?php echo $product['name']; ?>
                    <?php
                        if($product['abstract']){
                            echo '<div style="font-size: 10px; line-height: 12px;">［'.$product['abstract'].'］</div>';
                        }
                    ?>
                    <?php foreach ($product['option'] as $option) { ?>
                    <br />
                    &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                    <?php } ?></td>
                <td><?php echo @$product['inv_class_name'].'-'.$product['cate_name']; ?></td>

                <td><?php echo number_format($product['price'],2);?>
                    <?php if($product['weight_inv_flag'] == 1){ ?>
                    <?php echo "/" . $product['weight'];?>
                    <?php } ?>
                </td>
                <td align="center">
                    <?php echo number_format($product['total'],2); ?>
                    <?php if($product['weight_inv_flag']&&isset($all_order_product_weight_inv_arr[$order['order_id']][$product['product_id']])){ ?>
                    <?php echo "<br>(" . sprintf("%.2f",$all_order_product_weight_inv_arr[$order['order_id']][$product['product_id']]['total']) . ")";?>
                    <?php } ?>
                </td>

                <?php if($has_inv_product == 1){ ?>
                <td>
                    <span style="font-size: 18px; line-height: 10px; font-weight: bold">
                        <?php
                            $deliverReturnProduct = isset($return[$order['order_id']][$product['product_id']]['deliver_return_quantity']) ? $return[$order['order_id']][$product['product_id']]['deliver_return_quantity'] : 0;

                            echo $product['inv_product_quantity']-$deliverReturnProduct;
                        ?>
                    </span>

                    <?php echo $product['inv_size'] > 1 ? ("<br />(共" . $product['inv_size'] * $product['inv_product_quantity'] . ")") : "";?>
                </td>
                <?php
                  } else{
                    echo '<td></td>';
                  }
                ?>
                <td>
                    <?php
                        echo isset($return[$order['order_id']][$product['product_id']]['return_quantity']) ? $return[$order['order_id']][$product['product_id']]['return_quantity'] : 0;
                    ?>
                </td>


                <td>
                    <?php
                        $customer_return_box = isset($return[$order['order_id']][$product['product_id']]) ? $return[$order['order_id']][$product['product_id']]['customer_return_box_quantity'] : '';
                        if($customer_return_box > 0){
                            echo $customer_return_box;
                        }
                        if(isset($return[$order['order_id']][$product['product_id']])){
                            if($return[$order['order_id']][$product['product_id']]['customer_return_part_quantity_flag']>0){
                                echo ($customer_return_box > 0) ? ', ' : '';
                                echo $return[$order['order_id']][$product['product_id']]['customer_return_part_quantity'];
                            }
                        }
                    ?>
                </td>
                <td>
                    <?php
                        $back_missing_quantity = isset($return[$order['order_id']][$product['product_id']]) ? $return[$order['order_id']][$product['product_id']]['back_missing_quantity'] : '';
                        if($back_missing_quantity > 0){
                            echo $back_missing_quantity;
                        }
                    ?>
                </td>
                <td></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <?php echo $order['store_name']; ?> 订单编号 #
    <?php
        if($user_group_id == ADMIN_VIEW){
            echo $order['orderid'].'[#'.substr($order['order_id'],-3).']';
        }
        else{
            echo $order['order_id'];
        }
        ?>

    <table align="right" width="100%">
        <tr style="font-size: 16px">
            <td align="right" width="33%" style="border-top:1px solid black;border-left: 1px solid black;"><b>订单金额&nbsp;</b></td>
            <td align="right" width="33%" style="border-top:1px solid black;border-left: 1px solid black;"><b>订单支付&nbsp;</b></td>
            <td align="right" width="33%" style="border-top:1px solid black;border-left: 1px solid black;border-right: 1px solid black;"><b>订单变动&nbsp;</b></td>
        </tr>
        <tr>
        
            <td width="33%" align="right" style="border-top:1px solid black;border-left: 1px solid black;border-bottom: 1px solid black;">
                <table border="0" align="right" >
                    <tr>
                        <td width="70%" align="right" ><b>商品小计:</b></td>
                        <td width="5%">&nbsp;</td>
                        <td align="right"><b><?php echo $order['sub_total']; ?></b>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><b>订单优惠:</b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?php echo $order['discount_total']; ?></b>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><b>运费:</b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?php echo $order['shipping_fee']; ?></b>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><b>结转周转筐押金:</b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?php echo @$order['balance_container_deposit']; ?></b>&nbsp;</td>
                    </tr>

                    <tr>
                        <td width="70%" align="right"><b>&nbsp;</b></td>
                        <td width="5%">&nbsp;</td>
                        <td align="right"><b>&nbsp;</b>&nbsp;</td>

                    </tr>
                    <tr>
                        <td align="right" style="font-size:16px;"><b>订单合计:</b></td>
                        <td>&nbsp;</td>
                        <td align="right" ><b><?php echo $order['total'] + abs($order['credit_paid']); ?></b>&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td width="33%" align="right" style="border-top:1px solid black;border-left: 1px solid black;border-bottom: 1px solid black;">
                <table border="0" align="right">

                    <?php if(abs($order['credit_paid'])>0) { ?>
                    <tr>
                        <td width="70%" align="right"><b>余额支付:</b></td>
                        <td width="5%">&nbsp;</td>
                        <td align="right"><b><?php echo $order['credit_paid']; ?></b></td>
                    </tr>
                    <?php } ?>

                    <?php if(abs($order['wxpay_paid'])>0) { ?>
                    <tr>
                        <td width="70%" align="right"><b>微信支付</b></td>
                        <td width="5%">&nbsp;</td>
                        <td align="right"><b><?php echo $order['wxpay_paid']; ?></b></td>
                    </tr>
                    <?php } ?>

                    <?php if(abs($order['user_point_paid'])>0) { ?>
                    <tr>
                        <td width="70%" align="right"><b>积分支付</b></td>
                        <td width="5%">&nbsp;</td>
                        <td align="right"><b><?php echo $order['user_point_paid']; ?></b></td>
                    </tr>
                    <?php } ?>


                    <tr>
                        <td width="70%" align="right">&nbsp;</td>
                        <td width="5%"></td>
                        <td align="right"></td>
                    </tr>

                    <tr style="font-size: 16px">
                        <td align="right"><b>订单应收金额:</b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?php echo $order['order_due_total']; ?></b>&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><b>[<?php echo $order['payment_status']?>] 支付方式:</b></td>
                        <td>&nbsp;</td>
                        <td align="right"><b><?php echo $order['payment_method']; ?></b>&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td width="33%" align="right" style="border-top:1px solid black;border-left: 1px solid black;border-right: 1px solid black;border-bottom: 1px solid black;">
                <table border="0" align="right">
                    <tr>
                        <td style="width:80%;" align="right"><b>称重调整(<?php echo $order['weight_change'] != 0 ? ($order['weight_change'] > 0 ? "已扣余额" : "已退余额") : ""; ?>):</b></td>
                          <td>&nbsp;</td>
                        <td align="right"><b><?php echo $order['weight_change']; ?></b>&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="right"><b>调价调整(<?php echo $order['price_change'] != 0 ? ($order['price_change'] > 0 ? "已扣余额" : "已退余额") : ""; ?>):</b></td>
                          <td>&nbsp;</td>
                        <td align="right"><b><?php echo $order['price_change']; ?></b>&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="right"><b>缺货(<?php echo $order['order_payment_status_id'] == 1 ? '' : ($order['quantity_change'] != 0 ? ($order['quantity_change'] > 0 ? "已扣余额" : "已退余额") : ""); ?>):</b></td>
                          <td>&nbsp;</td>
                        <td align="right"><b><?php echo $order['order_payment_status_id'] == 1 ? ($order['order_return_outofstock'] > 0 ? $order['order_return_outofstock'] : '') : $order['quantity_change']; ?></b>&nbsp;</td>
                      </tr>
                      <tr style="display: none">
                        <td align="right"><b>退货:</b></td>
                          <td>&nbsp;</td>
                          <td align="right"><b><?php echo $order['order_return_fromcustomer'];?></b>&nbsp;</td>
                      </tr>
                      <tr>
                        <td width="70%" align="right"><b>&nbsp;</b></td>
                        <td width="5%">&nbsp;</td>
                        <td align="right"><b>&nbsp;</b>&nbsp;</td>
                        
                    </tr>
                    <tr>
                        <td width="70%" align="right"><b>&nbsp;</b></td>
                        <td width="5%">&nbsp;</td>
                        <td align="right"><b>&nbsp;</b>&nbsp;</td>
                        
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <?php if($order['station_id'] == 2){ ?>
    <div style="clear: both; float: none"></div>
    <br />
    <div style="text-align:center; font-size: 14px; font-weight: bold; line-height: 20px;">*** 商家签收 ***</div>
    <div style="font-size: 12px;">
        <div align="left" style="text-indent: 2rem">
            请确认本次订单（编号#<?php echo $order['order_id']; ?>）的商品(除订单显示缺货、现场退货或缺货部分)已全部当面点清收齐。
            订单明细行末“现场记录退货缺货”一栏，涂改部分视为无效，如涂改处确有退货或缺货，参考以下物流备注内容。
        </div>

        <div align="left" style="float: left; width: 250px; height:50px; margin-top:10px; padding:5px; border: 1px #666666 dashed;">确认周转筐红色扎带<br/ >封闭完好(商家签字)</div>
        <div style="float: right; width: 460px; height:50px;  margin-top:10px; padding:5px; border: 1px #666666 dashed;">物流备注<br/ ><span style="padding-top: 20px; padding-left: 300px">(商家签收)</span></div>
        <div style="float: none; clear: both"></div>

        <div align="right" style="line-height: 50px;">
            <span style="margin-right: 20px;">[回单]财务确认：___________________</span>
            <span>[回单]司机确认：___________________</span>
        </div>
    </div>
    <?php } ?>
    <hr />
    <div style="clear: both; float: none"></div>
  </div>
  <?php } ?>
</div>

</body>
</html>