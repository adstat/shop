<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header" style="display: none">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid" id="slaereport">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> 基础库存数据</h3>
      </div>
      <div class="panel-body">
            <button type="button" class="btn btn-primary pull-left" id="fresh"><i class="fa fa-search"></i> 生鲜库存</button>
            <button type="button" class="btn btn-primary pull-left" id="fm" style="margin: 0 3px;"><i class="fa fa-search"></i> 快消库存</button>
       </div>
        盘点时间：<?php echo $inv_check_date;?>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-left">商品ID</th>
                <th>货位号</th>
                <th>商品名称</th>
                <th>盘点库存</th>
                <th>盘点库存调整</th>
                <th>采购入库</th>
                <th>订单出库</th>
                <th>退货入库</th>
                <th>商品报损</th>
                <th>转促销品</th>
                <th>当前库存</th>
              </tr>
            </thead>
            <tbody>
              <?php
                if (sizeof($inv_mi_cold_arr)) {
                  foreach($inv_mi_cold_arr as $inv_key=>$inv_mi_cold){
              ?>
                <tr>
                    <td><?php echo $inv_key; ?></td>
                    <td><?php echo $inv_mi_cold['inv_class_sort'];?></td>
                <td><?php echo $inv_mi_cold['name']; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['14']) ? $inv_mi_cold['quantity']['14'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['16']) ? $inv_mi_cold['quantity']['16'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['11']) ? $inv_mi_cold['quantity']['11'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['12']) ? $inv_mi_cold['quantity']['12'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['8']) ? $inv_mi_cold['quantity']['8'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['13']) ? $inv_mi_cold['quantity']['13'] : 0; ?></td>
                <td><?php echo isset($inv_mi_cold['quantity']['15']) ? $inv_mi_cold['quantity']['15'] : 0; ?></td>
                <td class="text-center"><?php echo $inv_mi_cold['sum_quantity']; ?></td>
              </tr>
              <?php
                 }
               } else {
              ?>
              <tr>
                <td class="text-center" colspan="14"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        

      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
    $('#fresh').on('click', function() {
        location = 'index.php?route=report/inv_mi_cold&token=<?php echo $token; ?>&filter_station=1';
    });

    $('#fm').on('click', function() {
        location = 'index.php?route=report/inv_mi_cold&token=<?php echo $token; ?>&filter_station=2';
    });

//--></script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>