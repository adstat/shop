<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title>鲜世纪-配送订单列表－<?php echo $deliver_date?></title>
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

        .no_print{display:none};

    }

    .fontred{
        color: #e9322d;
    }
</style>
</head>
<body>
<div class="container">
  <div style="page-break-after: always; width:740px;">

    <table class="table table-bordered">
      <caption style="font-size:16px; margin:10px 0;">
        <img src="view/image/logo.png" border="0"> <b><?php echo $deliver_date?></b> 配送订单列表
        
      </caption>
      <thead>
        <tr>
          <td align="left" >线路：</td>
          <td align="left" ></td>
          <td align="left" >出车人：</td>
          <td align="left" ></td>
          <td align="left">出车里程数：</td>
          <td></td>
          <td colspan="3">出车主管（发出总数）：</td>
          
          <td colspan="3"></td>
          <td></td>
          
        </tr>
        
        <tr>
          <td align="left" >页数：</td>
          <td align="left" ></td>
          <td align="left" >回车人：</td>
          <td align="left" ></td>
          <td align="left">回车里程数：</td>
          <td></td>
          <td colspan="3">回车主管（回收总数）：</td>
          
          <td colspan="3"></td>
          <td></td>
          
        </tr>
        
        
        
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
        <tr>
          <td align="left" >编号</td>
          <td align="left" >订单编号</td>
          <!--<td align="left" >金额</td>-->
          <td align="left" >收货人/电话</td>
          <td align="left" >BD/电话</td>
          <td align="left">配送地址</td>
          <td>商品数量</td>
          <td>框数</td>
          <td>金额</td>
          <td>分拣备注</td>
          
          
          <td>到店时间</td>
          <!--<td>笼车</td>-->
          <td>出车任务</td>
          <td>回车任务</td>
          <td>备注</td>
          
        </tr>
      </thead>
      <tbody>
      <?php $t_num = 1;?>
      <?php foreach($do_data as $order) {?>
      <?php
        if($order['payment_code'] == 'WXPAY' && $order['order_payment_status_id'] !== '2'){
            $unpaid_font = 'class="fontred"';
        }
        else{
            $unpaid_font = '';
        }
        
        $order_hide = "";
        
        if($order['order_status_id'] == 3 || ($filter_station && $filter_station != $order['station_id'])){
            $order_hide = "style='display:none;'";
        }
        
        
      ?>
      <tr <?php echo $order_hide; ?> >
            <td><?php echo $t_num;?>
                <?php if($order['station_id'] == 2){ ?>
                <br><span style="color:red;">快</span>
                <?php } ?>
            </td>
            <td>
                <?php echo "<a target='_blank' href='index.php?route=sale/order&token=".$token."&filter_order_id=".$order['order_id']."'>".$order['order_id']."</a>"; ?>
                ,<?php echo $order['order_status']; ?>
                
            </td>
            <!--
            <td <?php echo $unpaid_font; ?> >
                <?php echo $order['total']; ?>,<?php echo $order['order_payment_status']; ?>
                
            </td>
            -->
            <td><?php echo $order['shipping_name']; ?>(<?php echo $order['customer_name']; ?>)--<?php echo $order['shipping_phone']; ?></td>
            <td><?php echo $order['bd_name']; ?> -- <?php echo $order['phone']; ?></td>
            <td>
                <?php echo $order['shipping_address']; ?>
                <?php if($order['comment']) { ?>
                    <div style="margin: 3px 0; padding: 3px;  border: 1px dashed;"/>
                        <strong>订单备注：</strong><?php echo $order['comment']; ?>
                    </div>
                <?php } ?>
            </td>
            <td>叶：<span style="color:red"><?php echo $order['category_67'];?></span>; 根+果：<span style="color:red"><?php echo $order['category_65_66'];?></span>; 其它：<span style="color:red"><?php echo $order['category_other'];?></span></td>
            <td>
                
                <?php if(!empty($order['frame_count']) || !empty($order['frame_meat_count'])){ ?>框:[<?php echo (int)$order['frame_count'] + (int)$order['frame_meat_count'];?>]<?php } ?>
                <?php if(!empty($order['incubator_count']) || !empty($order['incubator_mi_count'])){ ?>保:[<?php echo $order['incubator_count'] + $order['incubator_mi_count'];?>]<?php } ?>
                <?php if(!empty($order['foam_count']) || !empty($order['foam_ice_count'])){ ?>泡:[<?php echo (int)$order['foam_count'] + (int)$order['foam_ice_count'];?>]<?php } ?>
                <?php if(!empty($order['frame_mi_count']) || !empty($order['frame_ice_count'])){ ?>奶框:[<?php echo (int)$order['frame_mi_count'] + (int)$order['frame_ice_count'];?>]<?php } ?>
                <?php if(!empty($order['box_count'])){ ?>箱:[<?php echo $order['box_count'];?>]<?php } ?>
                <!--<?php if(!empty($order['frame_ice_count'])){ ?>冷冻框:[<?php echo $order['frame_ice_count'];?>]<?php } ?>-->
                <!--<?php if(!empty($order['foam_ice_count'])){ ?>冷冻泡:[<?php echo $order['foam_ice_count'];?>]<?php } ?>-->
            </td>
            <td><?php echo $order['need_pay'];?>
                <br>
                <?php echo $order['order_payment_status'];?>
            </td>
            <td><?php echo $order['inv_comment'];?></td>
            
            <td><?php echo $deliver_slots[$order['deliver_slot_id']]; ?></td>
            <!--<td><?php if($order['has_locker']){ echo "有"; } ?></td>-->
            <td></td>
            <td></td>
            <td><?php if($order['firstorder']==1){ echo "首单"; } ?></td>
        </tr>
        <?php $t_num++; ?>
      <?php } ?>
        <tr>
            <td colspan="15" align="right">打印时间<?php echo date("Y-m-d H:i:s",time()+8*60*60);?></td>
        </tr>
        <tr>
            <td colspan="15" align="right">其它：包含除叶菜类、根茎类、茄果类 外其它所有商品(奶制品、肉、水果、菇类 等)</td>
        </tr>
        
        
        
      </tbody>
    </table>
  </div>
</div>
</body>
</html>