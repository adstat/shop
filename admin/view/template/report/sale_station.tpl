<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>门店销售日报表</h1>
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
            <div class="col-sm-4" style="display: none">
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
            <div class="col-sm-6" style="display:none">
                  <div class="form-group">
                      <label class="control-label" for="input-station—id">指定商品ID(逗号分割)</label>
                      <div>
                      <textarea name='filter_products' rows="3" style="width: 80%"></textarea>
                      </div>
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
                <td width="40px">ID</td>
                <td width="150px">名称</td>
                <td width="60px">规格</td>
                <td width="42px">效期</td>
                <td width="55px">现价格</td>

                <td style="background-color:#efefef" width="55px">网销量</td>
                <td style="background-color:#efefef" width="55px" class="text-right" title＝"前天库存+当天入库－当天报损－当天库存">店销量</td>
                <td style="background-color:#efefef" width="70px" class="text-right">店网合计</td>

                <td class="text-right">店均价</td>
                <td class="text-right">昨日库存</td>
                <td class="text-right">入库</td>
                <td class="text-right">出库</td>
                <td class="text-right">报损</td>
                <td class="text-right">POS零售</td>
                <td class="text-right" style="background-color:#efefef">库存</td>
            </tr>
            </thead>
            <tbody>

            <?php
                $onlineSaleTotal = 0;
                $onlineQtyTotal = 0;
                $saleTotal = 0;
                $saleQty = 0;
                $yesterdayInvCheckQty = 0;
                $invInQty = 0;
                $invOutQty = 0;
                $invBreakQty = 0;
                $invRetail = 0;
                $invCheckQty = 0;
            ?>

            <?php foreach($moveInfo as $move){ ?>
            <tr>
                <?php
                    $online_sale = 0;
                    if(array_key_exists($move['product_id'],$orderInfo)){
                        $online_sale = $orderInfo[$move['product_id']]['sale_qty'];
                    }
                ?>
                <td><?php echo $move['product_id']; ?></td>
                <td title="<?php echo $move['name']; ?>"><?php echo $move['short_name']; ?></td>
                <td title="规格"><?php echo $move['unit']; ?></td>
                <td title="效期"><?php echo $move['shelf_life']; ?></td>
                <td title="现价格"><?php echo $move['current_price']; ?></td>

                <td title="网销量" style="background-color:#efefef"><?php if($station_id == -1){ echo $online_sale; } else{ echo ''; } ?></td>
                <td title="店销量" style="background-color:#efefef" id="stationSale_<?php echo $product; ?>" class="text-right"><?php echo $move['today_retail']; ?></td>
                <td title="网站及门店销量合计" style="background-color:#efefef" id="stationAvgPrice_<?php echo $product; ?>" class="text-right"><?php if($station_id == -1){ echo $online_sale+$move['today_retail']; } else{ echo ''; } ?></td>

                <td title="店均价" id="stationAvgPrice_<?php echo $product; ?>" class="text-right"><?php echo $move['today_retail_avg_price']; ?></td>
                <td title="昨日库存" class="text-right"><?php echo $move['yesterday_s_check']; ?></td>
                <td title="入库" class="text-right"><?php echo $move['today_s_in']; ?></td>
                <td title="出库" class="text-right"><?php echo $move['today_s_out']; ?></td>
                <td title="报损" class="text-right"><?php echo $move['today_s_breakage']; ?></td>
                <td title="POS零售" class="text-right"><?php echo $move['inv_pos_retail']; ?></td>
                <td title="库存" style="background-color:#efefef"><?php echo $move['today_s_check']; ?></td>

                <?php
                    @$onlineSaleTotal += '';
                    @$onlineQtyTotal += '';

                    if($station_id == -1){
                        @$onlineSaleTotal += $online_sale * $orderInfo[$move['product_id']]['sale_avg_price'];
                        @$onlineQtyTotal += $online_sale;
                    }

                    @$saleTotal += $move['today_retail_avg_price']*$move['today_retail'];
                    @$saleQty += $move['today_retail'];

                    @$yesterdayInvCheckQty += $move['yesterday_s_check'];

                    @$invInQty += $move['today_s_in'];
                    @$invOutQty += $move['today_s_out'];
                    @$invBreakQty += $move['today_s_breakage'];
                    @$invRetail += $move['inv_pos_retail'];
                    @$invCheckQty += $move['today_s_check'];
                ?>
            </tr>
            <?php } ?>
            </tbody>
            <tr>
                <td colspan="4"></td>
                <td class="text-right">网销量</td>
                <td class="text-right">网合计</td>
                <td>店合计</td>
                <td class="text-right"></td>
                <td>店销量</td>
                <td>昨日存数</td>
                <td class="text-right">入库合计</td>
                <td class="text-right">出库合计</td>
                <td class="text-right">报损合计</td>
                <td class="text-right">零售合计</td>
                <td class="text-right" style="background-color:#efefef">库存合计</td>
            </tr>
            <tr>
                <td colspan="4"></td>
                <td class="text-right"><?php echo $onlineSaleTotal; ?></td>
                <td class="text-right"><?php echo $onlineQtyTotal; ?></td>
                <td class="text-right"><?php echo $saleQty; ?></td>
                <td></td>
                <td class="text-right"><?php echo $saleTotal; ?></td>
                <td class="text-right"><?php echo $yesterdayInvCheckQty; ?></td>
                <td class="text-right"><?php echo $invInQty; ?></td>
                <td class="text-right"><?php echo $invOutQty; ?></td>
                <td class="text-right"><?php echo $invBreakQty; ?></td>
                <td class="text-right"><?php echo $invRetail; ?></td>
                <td class="text-right" style="background-color:#efefef"><?php echo $invCheckQty; ?></td>
            </tr>

        </table>
    </div>
    <?php }
     else {
        echo "无门店数据";
     }?>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=report/sale_station&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	//var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	//if (filter_date_end) {
		//url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	//}
		
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