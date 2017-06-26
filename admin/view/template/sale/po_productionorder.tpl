<?php ini_set('display_errors',0); ?>
<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title>鲜世纪生产订单－<?php echo $deliver_date?> <?php echo $this_product_group; ?></title>
<base href="<?php echo $base; ?>" />
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
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
</style>
</head>
<body>
<div class="container">

    
    
    <h3>送货日期：<?php echo $deliver_date;?></h3>
    
    
     <div class="well no_print">
          <div class="row">
            
              
              
            <!--
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-order-status">订单状态</label>
                <select name="filter-order-status" id="input-order-status" class="form-control">
                  <?php if ($filter_order_status == '0') { ?>
                  <option value="0" selected="selected"></option>
                  <?php } else { ?>
                  <option value="0"></option>
                  <?php } ?>
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $filter_order_status) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-payment-status">支付状态</label>
                <select name="filter-payment-status" id="input-payment-status" class="form-control">
                  
                  <?php if ($filter_payment_status == '0') { ?>
                  <option value="0" selected="selected"></option>
                  <?php } else { ?>
                  <option value="0"></option>
                  <?php } ?>
                  <?php foreach ($order_payment_statuses as $order_payment_status) { ?>
                  <?php if ($order_payment_status['order_payment_status_id'] == $filter_payment_status) { ?>
                  <option value="<?php echo $order_payment_status['order_payment_status_id']; ?>" selected="selected"><?php echo $order_payment_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_payment_status['order_payment_status_id']; ?>"><?php echo $order_payment_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
                
              </div>
            </div>
              
             --> 
              
              
              
              
              
              
              
            <div class="col-sm-2">
              <div class="form-group">
                <label class="control-label" for="input-date-added">下单时间</label>
                <select name="filter-date-added" id="input-date-added" class="form-control">
                  
                  <?php if ($filter_date_add == '0') { ?>
                  <option value="0" selected="selected">全部</option>
                  <?php } else { ?>
                  <option value="0">全部</option>
                  <?php } ?>
                  <?php foreach ($order_date_add as $odk=>$odv) { ?>
                  <?php if ($odk == $filter_date_add) { ?>
                  <option value="<?php echo $odk; ?>" selected="selected"><?php echo $odv; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $odk; ?>"><?php echo $odv; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
                <label class="control-label" for="input-produc-inv-class">商品分类</label>
                <select name="filter-product-inv-class" id="input-product-inv-class" class="form-control">
                  
                  <?php if ($filter_product_inv_class == '0') { ?>
                  <option value="0" selected="selected">全部</option>
                  <?php } else { ?>
                  <option value="0">全部</option>
                  <?php } ?>
                  <?php foreach ($product_inv_classes as $product_inv_class) { ?>
                  <?php if ($product_inv_class['product_inv_class_id'] == $filter_product_inv_class) { ?>
                  <option value="<?php echo $product_inv_class['product_inv_class_id']; ?>" selected="selected"><?php echo $product_inv_class['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $product_inv_class['product_inv_class_id']; ?>"><?php echo $product_inv_class['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
                
              
            </div>
              
              
              <div class="col-sm-2">
              <div class="form-group">
                <label class="control-label" for="input-price-tag">是否有价签</label>
                <select name="filter-price-tag" id="input-price-tag" class="form-control">
                  
                  <?php if ($filter_price_tag == '0') { ?>
                  <option value="0" selected="selected">全部</option>
                  <?php } else { ?>
                  <option value="0">全部</option>
                  <?php } ?>
                  <?php foreach ($order_price_tages as $opk=>$opv) { ?>
                  <?php if ($opk == $filter_price_tag) { ?>
                  <option value="<?php echo $opk; ?>" selected="selected"><?php echo $opv; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $opk; ?>"><?php echo $opv; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              </div>

              <div class="col-sm-2">
              <div class="form-group">
                  <label class="control-label" for="input-produce-group">生产分组</label>
                  <select name="filter-produce-group" id="input-produce-group" class="form-control">

                      <?php if ($filter_produce_group == '0') { ?>
                      <option value="0" selected="selected">全部</option>
                      <?php } else { ?>
                      <option value="0">全部</option>
                      <?php } ?>
                      <?php foreach ($product_group as $val) { ?>
                      <?php if ($val['produce_group_id'] == $filter_produce_group) { ?>
                      <option value="<?php echo $val['produce_group_id']; ?>" selected="selected"><?php echo $val['title']; ?></option>
                      <?php } else { ?>
                      <option value="<?php echo $val['produce_group_id']; ?>"><?php echo $val['title']; ?></option>
                      <?php } ?>
                      <?php } ?>
                  </select>
              </div>

              <div class="col-sm-2">
                  <div class="form-group">
                      <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> 筛选</button>
                  </div>
                </div>

            </div>
              
              
              
          </div>
        </div>


    <div style="page-break-after: always; width:740px">
    <table class="table table-bordered">
      <caption style="font-size:16px; margin:10px 0;">鲜世纪生产单 <b><?php echo $deliver_date?></b> <?php echo $this_product_group; ?><br /><span style="font-size: 90%">打印时间:<?php echo date('Y-m-d H:i:s', time()+8*60*60)?></span></caption>
      <thead>
        <tr>
          <td align="left" style="width: 10%;">商品编号</td>
          <td align="left" style="">商品名称</td>
          <td align="left" style="width: 10%;">二级分类</td>
          <td align="left" style="width: 10%;">一级分类</td>
          <td align="left" style="width: 8%;">均售价</td>
          <td align="left" style="width: 7%;">规格</td>
          <td align="left" style="width: 6%;">合计</td>
          <td align="left" style="width: 7.5%;"><b style="color:#CC0000">未支付</b></td>
          <td align="left" style="width: 10%;">加工总数</td>
        </tr>
      </thead>
      <tbody>
      <?php for($i=0; $i<sizeof($podata); $i++){ ?>
        <tr>
            <td><?php echo $podata[$i]['product_id']; ?></td>
            <td><?php echo $podata[$i]['name']; ?></td>
            <td><?php echo $podata[$i]['category']; ?></td>
            <td><?php echo $podata[$i]['parent_category']; ?></td>
            <td><?php echo $podata[$i]['price']; ?></td>
            <td><?php echo $podata[$i]['product_unit']; ?></td>
            <td><?php echo $podata[$i]['qty_total']; ?></td>
            <td><?php echo $podata[$i]['unpaid_qty_total']; ?></td>
            <td><?php echo $podata[$i]['purchase']; ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
    <div align="right"><span style="font-size: 90%">打印时间:<?php echo date('Y-m-d H:i:s', time()+8*60*60)?></span></div>
        
        
        
    <!--<div style="text-align: right"><b><?php echo $deliver_date?></b>需配送商品采购订单(含微信支付<b style="color:#CC0000">且尚未支付</b>), 打印时间<?php echo date("Y-m-d H:i:s",time()+8*60*60);?></div>-->
    </div>

    <?php if( sizeof($podata_unpaid) ) {?>
    <div style="page-break-after: always; width:740px">
        <hr class="no_print" />
        <table class="table table-bordered">
            <caption style="font-size:16px; margin:10px 0;"><img src="view/image/logo.png" border="0"> <b><?php echo $deliver_date?></b>送货，选择微信支付<b style="color:#CC0000">且尚未支付</b>的商品</caption>
            <thead>
            <tr>
                <td align="left" style="width: 10%;">商品编号</td>
                <td align="left" style="">商品名称</td>
                <td align="left" style="width: 10%;">二级分类</td>
                <td align="left" style="width: 10%;">一级分类</td>
                <td align="left" style="width: 8%;">均售价</td>
                <td align="left" style="width: 7%;">规格</td>
                <td align="left" style="width: 6%;">合计</td>
                <td align="left" style="width: 10%;">采购数量</td>
            </tr>
            </thead>
            <tbody>
            <?php for($i=0; $i<sizeof($podata_unpaid); $i++){ ?>
            <tr>
                <td rowspan="2"><?php echo $podata_unpaid[$i]['product_id']; ?></td>
                <td rowspan="2"><?php echo $podata_unpaid[$i]['name']; ?></td>
                <td><?php echo $podata_unpaid[$i]['category']; ?></td>
                <td><?php echo $podata_unpaid[$i]['parent_category']; ?></td>
                <td><?php echo $podata_unpaid[$i]['price']; ?></td>
                <td><?php echo $podata_unpaid[$i]['product_unit']; ?></td>
                <td><?php echo $podata_unpaid[$i]['qty_total']; ?></td>
                <td><?php echo $podata_unpaid[$i]['purchase']; ?></td>
            </tr>
            <tr>
                <td colspan="6"><strong>相关未支付订单：
                        <?php
                         $unpaid_orders = explode(',',$podata_unpaid[$i]['unpaid_orders']);
                         foreach($unpaid_orders as $unpaid_order){
                            echo "[<a target='_blank' href='index.php?route=sale/order&token=".$token."&filter_order_id=".$unpaid_order."'>".$unpaid_order."</a>] ";
                         }
                         ?></strong>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <div style="text-align: right"><b><?php echo $deliver_date?></b>送货，选择微信支付<b style="color:#CC0000">且尚未支付</b>的商品, 打印时间<?php echo date("Y-m-d H:i:s",time()+8*60*60);?></div>
    </div>
    <?php }?>

</div>



  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
    
    var url = 'index.php?route=sale/order/download&token=<?php echo $token; ?>&deliver_date=<?php echo $deliver_date;?>&type=<?php echo $filter_type;?>';

    //Fix url with all filters.
    url += fixUrl();
    
    location = url;
});


function fixUrl(){
    var url ='';

   

    var filter_order_status = $('select[name=\'filter-order-status\']').val();

    if (filter_order_status) {
        url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
    }

    var filter_payment_status = $('select[name=\'filter-payment-status\']').val();

    if (filter_payment_status) {
        url += '&filter_payment_status=' + encodeURIComponent(filter_payment_status);
    }

    var filter_date_add = $('select[name=\'filter-date-added\']').val();

    if (filter_date_add) {
        url += '&filter_date_add=' + encodeURIComponent(filter_date_add);
    }

    var filter_product_inv_class = $('select[name=\'filter-product-inv-class\']').val();

    if (filter_product_inv_class) {
        url += '&filter_product_inv_class=' + encodeURIComponent(filter_product_inv_class);
    }

    var filter_price_tag = $('select[name=\'filter-price-tag\']').val();

    if (filter_price_tag) {
        url += '&filter_price_tag=' + encodeURIComponent(filter_price_tag);
    }

    var filter_produce_group = $('select[name=\'filter-produce-group\']').val();

    if (filter_produce_group) {
        url += '&filter_produce_group=' + encodeURIComponent(filter_produce_group);
    }

    return url;
}
//--></script> 




</body>
</html>