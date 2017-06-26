<!DOCTYPE html>
 <head>
    <meta charset="UTF-8" />
</head>
<div class="container" id="print_a4" style="display: block; ">
    <table class=" table-bordered3" border="1" cellspacing="0" cellpadding="0">
        <thead>
        <tr style=" font-size:15px; font-weight:bold; border=0"><td colspan = "1">鲜世纪配送单:</td><td >日期[<?php echo date("Y-m-d H:i:s", time()+8*3600);?>]</td><td colspan = "3">线路:[<?php echo $data[0]['logistic_line_title'] ;?>];司机:[<?php echo $data[0]['logistic_driver_title'] ;?>]</td>
            <td colspan = "2" >
                <img style="width:8em;float: right" src="<?php echo SITE_URI;?>/www/admin/barcode/barcode.php?text=<?php echo $data[0]['logistic_allot_id'];?>" alt="barcode" />
            </td></tr>
        <tr style=" font-size:12px; border=1 ;font-weight:bold;">
        <td style="width: 70px">订单编号</td>
        <td style="width: 90px">分拣位</td>
        <td>地址/收货人</td>
        <td style="width: 90px">件数</td>
        <td style="width: 80px">应收金额</td>
        <td style="width: 85px">市场人员</td>
        <td>条码</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $value) { ?>
        <tr style="font-size:12px; font-weight:normal;">
       <td> <?php echo $value['order_id'] ;?> </td>
            <?php if(!empty($value['invComment'])){ ;?>
            <td>  <?php echo $value['invComment'] ?>  </td>
            <?php }else { ?>
            <td>无货位号</td>
            <?php } ;?>
            <td><?php echo  $value['shipping_firstname'] ;?>-<?php echo  $value['shipping_phone'] ;?>- <?php echo  $value['shipping_city'] ;?>- <?php echo  $value['shipping_address_1'] ;?></td>
            <td><?php echo $value['num'] ;?></td>
            <td><?php echo $value['total'] ;?></td>
            <td><?php echo $value['bd_name'] ;?>-<?php echo $value['phone'] ;?></td>
            <td style="border-top:1px solid black;">
                <img style="width:8em;float:right;" src="<?php echo SITE_URI;?>/www/admin/barcode/barcode.php?text=<?php echo $value['order_id'];?>" alt="barcode" />
            </td>
        </tr>
        <?php };?>
        </tbody>
    </table>
</div>
</html>