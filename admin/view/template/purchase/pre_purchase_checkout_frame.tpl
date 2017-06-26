<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xmlns="http://www.w3.org/1999/html">
<head>
<meta charset="UTF-8" />
<title>付款申请单</title>
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
    </style>
</head>
<body>
<!-- 开始A4模板 -->
<div class="container" id="print_a4" style="display: block;">
  <?php foreach ($supplier_orders as $supplier_s => $supplier_order) { ?>
  <?php $order = $supplier_order['order'][0]; ?>
  <div style="page-break-after: always; width:650px">
      <div><h3 style="text-align:center;"> 付款申请单</h3></div>
      <span>申请日期: <?php echo date("Y-m-d", time() + 8 * 3600);?></span>
    <table class="table table-bordered">
      
      <tbody>
        <tr>
          <td style="width: 16%;">
            收款人：
          </td>
          <td style="width: 20%;" colspan="2">
            <?php echo $order['checkout_username'];?>
          </td>
          <td style="width: 20%;">
            开户行及帐号：
          </td>
          <td style="width: 40%;" colspan="4">
            <?php echo $order['checkout_userbank'];?>&nbsp;&nbsp;<?php echo $order['checkout_usercard'];?>
          </td>
          
          
        </tr>
        <tr>
            <td style="width: 20%;" colspan="2">
            付款金额（大写）：
          </td>
          <td style="width: 50%;" colspan="3">
            <?php echo $supplier_order['d_total'];?>
          </td>
          <td style="width: 30%;" colspan="3">
            小写：<?php echo $supplier_order['total'];?>
          </td>
          
          
          
        </tr>
        <tr style="height:8em;">
          <td style="width: 10%;" >
            付款内容：
          </td>
          <td style="width: 80%;" colspan="7">
              <?php echo $order['checkout_type_id'] == 3 ? "预付款\r" : "";?>
                  供应商：#<?php echo $order['supplier_type']; ?># <?php echo $order['supplier_name'] . "\r";?>
                  <?php foreach($supplier_order['order'] as $s_order){ ?>
                  <br />
                  <?php if($s_order['checkout_status'] == '2'){ ?><span style="color:#CC0000"><strong>[已支付]</strong></span><?php } ?>
                  采购单：<?php echo $s_order['purchase_order_id'];?><?php echo $s_order['invoice_flag'] == 1 ? "<strong>[有发票]</strong>" : "<strong><i>(无发票)</i></strong>";?>，订单金额：<?php echo $s_order['order_total'];?>，余额支付：<?php echo $s_order['use_credits_total'];?>，缺货：<?php echo $s_order['quehuo_credits'];?>，退货：<?php echo $s_order['order_return'];?>，入库金额：<?php echo sprintf("%.4f", $s_order['get_total']);?>；到货日期：<?php echo $s_order['get_product_date'];?><?php echo "\r";?>
              <?php } ?>
              <br /><br />
              <textarea style="border:none; width: 40em;height: 3em;" placeholder="备注(手写无效)："></textarea>
          </td>
          
          
        </tr>
        <tr>
            <td style="width: 10%;" >
            要求付款日期：
          </td>
          <td style="width: 70%;" colspan="7">
            
          </td>
          
          
          
        </tr>
        <tr>
          <td style="width: 8%;">
            CEO：
          </td>
          <td style="width: 14%;">
            
          </td>
          <td style="width: 10%;">
            财务主管：
          </td>
          <td style="width: 10%;">
            
          </td>
          <td style="width: 10%;">
            部门主管：
          </td>
          <td style="width: 10%;">
            
          </td>
          <td style="width: 10%;">
            申请人：
          </td>
          <td style="width:14%;">
            
          </td>
          
          
        </tr>
      </tbody>
    </table>

      <?php foreach($supplier_order['order'] as $order_s2){ ?>
        <?php 
            $quantity_total = 0;
            $supplier_quantity_total = 0;
            $get_quantity_total = 0;
            
        ?>
        采购单:<?php echo $order_s2['purchase_order_id'];?><br>
      备注：<?php echo $order_s2['order_comment'];?><br>
          <table id="adjust" class="table table-striped table-bordered table-hover">
                      <thead>
          
                        <tr>
                            
                            <td class="text-center" width="10%">商品ID</td>
                            <td class="text-center" width="10%">采购数量</td>
                            <td class="text-center" width="10%">供应商采购量</td>
                            <td class="text-center" width="10%">采购价格</td>
                            <td class="text-center" width="10%">单品总价</td>
                            <td class="text-center" width="10%" style="display: none">平均成本</td>
                            <td class="text-center" width="10%">真实成本</td>
                            <td class="text-center" width="10%">实收数量</td>
                            <td class="text-center">商品名称</td>
                            
                        </tr>
                      </thead>
                      <tbody>
                        <?php $abnomal = ''; ?>
                        <?php $price_change = 0;$cost_change = 0; ?>
                        <!-- 添加商品可售库存 -->
                        <?php foreach($order_s2['products'] as $key=>$value){ ?>

                        <?php

                            $price_change = round($value['price'] - $value['ds_price'],4); $cost_change = round($value['real_cost'] - $value['ds_real_cost'],4);
                        ?>

                        <!-- 两个价格都有变动 -->
                        <?php if($price_change != 0 && $cost_change !=0){ ?>

                            <?php if($value['price']*$value['real_cost'] != 0){ ?>
                            <?php $abnomal .= '['.$value['product_id'].$value['name'].']'.'采购价浮动'.$price_change.'真实成本浮动'.$cost_change.'</br>'; ?>
                            <tr style="border-style:dashed; border-width:4px; border-color:#000000;">
                             <?php }elseif($value['price'] == 0 && $value['real_cost'] != 0){ ?>
                            <?php $abnomal .= '['.$value['product_id'].$value['name'].']'.'真实成本浮动'.$cost_change.'</br>'; ?>
                            <tr style="border-style:dashed; border-width:2px; border-color:#000000;">
                            <?php }elseif($value['price'] != 0 && $value['real_cost'] == 0){ ?>
                            <?php $abnomal .= '['.$value['product_id'].$value['name'].']'.'采购价浮动'.$price_change.'</br>'; ?>
                            <tr style="border-style:dashed; border-width:2px; border-color:#000000;">
                            <?php }else{ ?>
                            <tr >
                            <?php } ?>
                        <!-- 两个价格有一个变动 -->
                        <?php }elseif($price_change*$cost_change == 0 && abs($price_change)+abs($cost_change) !=0 ){ ?>
                            <?php if($price_change != 0 && $value['price'] != 0) { ?>

                            <?php $abnomal .= '['.$value['product_id'].$value['name'].']'.'采购价浮动'.$price_change.'</br>'; ?>
                            <tr style="border-style:dashed; border-width:2px; border-color:#000000;">

                            <?php } ?>

                            <?php if($price_change != 0 && $value['price'] == 0) { ?>

                            <tr>

                            <?php } ?>

                            <?php if($cost_change != 0 && $value['real_cost'] != 0) { ?>
                            <tr style="border-style:dashed; border-width:2px; border-color:#000000;">

                            <?php $abnomal .= '['.$value['product_id'].$value['name'].']'.'真实成本浮动'.$cost_change.'</br>';; ?>

                            <?php } ?>

                            <?php if($cost_change != 0 && $value['real_cost'] == 0) { ?>

                            <tr>

                            <?php } ?>

                        <!-- 两个价格都没有变动 -->
                        <?php }else{ ?>
                            <tr>
                        <?php } ?>

                            <td class="text-center"><?php echo $value['product_id'];?></td>
                            <td class="text-center"><?php echo $value['quantity'];?></td>
                            <td class="text-center"><?php echo $value['supplier_quantity'];?></td>
                            <td class="text-center"><?php echo $value['price'];?></td>
                            <td class="text-center"><?php echo round($value['supplier_quantity']*$value['price'],4) ; ?></td>
                            <td class="text-center" style="display: none"><?php echo $value['purchase_cost'];?></td>
                            <td class="text-center"><?php echo $value['real_cost'];?></td>
                            <td class="text-center"><?php echo isset($order_s2['get_product'][$value['product_id']]) ? $order_s2['get_product'][$value['product_id']] : '';?></td>
                            <td class="text-center"><?php echo $value['name'];?></td>  
                            
                          </tr>
                          <?php
                            $quantity_total += $value['quantity'];
                            $supplier_quantity_total += $value['supplier_quantity'];
                            $get_quantity_total += isset($order_s2['get_product'][$value['product_id']]) ? $order_s2['get_product'][$value['product_id']] : 0;
                          
                          ?>
                        <?php } ?>  
                        <tr>
                            <td>总计</td>
                            <td><?php echo $quantity_total;?></td>
                            <td><?php echo $supplier_quantity_total;?></td>
                            <td></td>
                            <td style="display: none"></td>
                            <td></td>
                            <td><?php echo $get_quantity_total;?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="8"><?php echo $abnomal; ?></td>
                        </tr>
                      </tbody>
      </table>
   
   <?php } ?>
    
    
    

    
    <div style="clear: both; float: none"></div>
  </div>
  
  <?php } ?>
</div>
</body>
</html>