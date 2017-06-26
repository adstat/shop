<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>销售统计</h1>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-start">开始日期</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-end">结束日期</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="input-station—id">选择门店</label>
                    <select name="station_id" id="station_id" class="form-control">
                        <option value="0">请选择门店</option>
                        <option value="-1">[所有门店合计]</option>
                        <?php foreach($stationInfo as $m) { ?>
                            <option <?php if($station_id == $m['station_id']){ echo "selected='selected'"; } ?> value="<?php echo $m['station_id']; ?>"><?php echo $m['station_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> 查询</button>
          </div>
        </div>
      </div>
    </div>
  </div>

    <?php if( isset($moveInfo) && sizeof($moveInfo) ){ ?>
    <div class="table-responsive" style="padding:10px;">
        <table class="table table-bordered">
            <thead>
            <tr>
                <td colspan="5">商品信息</td>
                <td colspan="9" style="background-color:#efefef">合计</td>
                <?php foreach($days as $day) { ?>
                    <td colspan="7"><?php echo $day; ?></td>
                <?php } ?>

            </tr>

            <tr>
                <td width="40px">ID</td>
                <td width="130px">名称</td>
                <td width="60px">规格</td>
                <td width="55px">现价格</td>
                <td width="22px">效期</td>

                <td class="text-right">昨日存合</td>
                <td class="text-right">入合</td>
                <td class="text-right">出合</td>
                <td class="text-right">损合</td>
                <td class="text-right">存合</td>
                <td class="text-right" style="background-color:#efefef">销合</td>
                <td class="text-right" style="background-color:#fff5cb">网销合</td>
                <td title="商品采购系数" class="text-right" style="background-color:#fff5cb; display:none">系数</td>
                <td title="所选时间段内均销量" class="text-right" style="background-color:#fff5cb">均销</td>
                <td title="建议计划采购量" class="text-right" style="background-color:#fff5cb">计划</td>

                <?php foreach($days as $day) { ?>
                    <td class="text-right">昨日库存</td>
                    <td class="text-right">入库</td>
                    <td class="text-right">出库</td>
                    <td class="text-right">报损</td>
                    <td class="text-right">库存</td>

                    <td class="text-right" style="background-color:#efefef">销售</td>
                    <td class="text-right" style="background-color:#fff5cb">网销量</td>
                <?php } ?>

            </tr>
            </thead>
            <tbody>

            <?php foreach($prodInfo as $prod){ ?>
            <tr>
                <?php
                $orderProdSale = '';
                $orderProdTotalSale = '';
                if($station_id == -1){
                    $orderProdSale = @$orderInfo[$day][$prod['product_id']]['sale_qty'];
                    $orderProdTotalSale = @$orderTotalInfo[$prod['product_id']]['sale_qty'];
                }


                $avgSale = 0;
                $suggestPurchase = 0;
                $factor = @$prod['factor'];
                $retailWithShortFactor = @$rawAvgRetail[$prod['product_id']]['retail_short_factor'];
                $rawRetailDay = 1;
                if( @$rawAvgRetail[$prod['product_id']]['raw_retail_day'] > 0 ){
                    $rawRetailDay = $rawAvgRetail[$prod['product_id']]['raw_retail_day'];
                }

                $stationFactor = 1;
                if($station_id > 0){
                    $stationFactor = $stationInfo[$station_id]['factor'];
                    $avgSale = number_format($retailWithShortFactor/$rawRetailDay, 2);
                    $suggestPurchase = number_format($factor*$avgSale*$stationFactor,2);
                }
                else{
                    $avgSale = '';
                    $suggestPurchase = '';
                }


                ?>
                <td><?php echo $prod['product_id']; ?></td>
                <td title="<?php echo $prod['name']; ?>"><?php echo $prod['short_name']; ?></td>
                <td title="规格"><?php echo $prod['unit']; ?></td>
                <td title="现价格"><?php echo $prod['current_price']; ?></td>
                <td title="效期"><?php echo $prod['shelf_life']; ?></td>

                <td title="昨日库存合计" class="text-right"><?php echo @$moveTotalInfo[$prod['product_id']]['yesterday_inv_check']; ?></td>
                <td title="入库合计" class="text-right"><?php echo @$moveTotalInfo[$prod['product_id']]['inv_in']; ?></td>
                <td title="出库合计" class="text-right"><?php echo @$moveTotalInfo[$prod['product_id']]['inv_out']; ?></td>
                <td title="报损合计" class="text-right"><?php echo @$moveTotalInfo[$prod['product_id']]['inv_breakage']; ?></td>
                <td title="库存合计"><?php echo @$moveTotalInfo[$prod['product_id']]['inv_check']; ?></td>
                <td title="销售合计" class="text-right" style="background-color:#efefef"><?php echo @$moveTotalInfo[$prod['product_id']]['inv_sale']; ?></td>
                <td title="网销量合计" class="text-right" style="background-color:#fff5cb"><?php echo @$orderProdTotalSale; ?></td>

                <td title="商品采购系数" class="text-right" style="background-color:#fff5cb; display:none"><?php echo @$prod['factor']; ?></td>
                <td title="所选时间段内均销量: （<?php echo @$rawAvgRetail[$prod['product_id']]['retail']; ?>＋<?php echo @$rawAvgRetail[$prod['product_id']]['short_factor']; ?>) / <?php echo @$rawAvgRetail[$prod['product_id']]['raw_retail_day']; ?>" class="text-right" style="background-color:#fff5cb"><?php echo @$avgSale; ?></td>
                <td title="建议计划采购量: <?php echo @$avgSale.'*'.@$factor.'*'.$stationFactor; ?>" class="text-right" style="background-color:#fff5cb"><?php echo @$suggestPurchase; ?></td>


                <?php foreach($days as $day) { ?>
                    <td title="昨日库存" class="text-right"><?php echo @$moveInfo[$day][$prod['product_id']]['yesterday_inv_check']; ?></td>
                    <td title="入库" class="text-right"><?php echo @$moveInfo[$day][$prod['product_id']]['inv_in']; ?></td>
                    <td title="出库" class="text-right"><?php echo @$moveInfo[$day][$prod['product_id']]['inv_out']; ?></td>
                    <td title="报损" class="text-right"><?php echo @$moveInfo[$day][$prod['product_id']]['inv_breakage']; ?></td>
                    <td title="库存"><?php echo @$moveInfo[$day][$prod['product_id']]['inv_check']; ?></td>
                    <td title="销售" class="text-right" style="background-color:#efefef"><?php echo @$moveInfo[$day][$prod['product_id']]['inv_sale']; ?></td>
                    <td title="网销量" class="text-right" style="background-color:#fff5cb"><?php echo @$orderProdSale; ?></td>
                <?php } ?>

            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php }
     else {
        echo "无门店数据";
     }?>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
    url = 'index.php?route=report/sale_stat&token=<?php echo $token; ?>';
    
    var filter_date_start = $('input[name=\'filter_date_start\']').val();
    
    if (filter_date_start) {
        url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
    }

    var filter_date_end = $('input[name=\'filter_date_end\']').val();
    
    if (filter_date_end) {
        url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
    }
        
    var station_id = $('select[name=\'station_id\']').val();
    
    if (station_id) {
        url += '&station_id=' + encodeURIComponent(station_id);
    }

    location = url;
});
//--></script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
    pickTime: false
});
//--></script></div>
<?php echo $footer; ?>