<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<style>
    .change_status { color: #006dcc; font-weight: bold; }
</style>
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" id="button-shipping" form="form-order" formaction="<?php echo $shipping; ?>" data-toggle="tooltip" title="<?php echo $button_shipping_print; ?>" class="btn btn-info"><i class="fa fa-truck"></i></button>
        <button style="display: none" type="submit" id="button-invoice" form="form-order" formaction="<?php echo $invoice; ?>" data-toggle="tooltip" title="<?php echo $button_invoice_print; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>
        <a onclick="javascript:alert('后台下单功能暂时停用！');return false;" href="<?php //echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-order-id"><?php echo $entry_order_id; ?></label>
                <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="<?php echo $entry_order_id; ?>" id="input-order-id" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-customer">会员名/ID/手机号</label>
                <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="会员名/ID/手机号" id="input-customer" class="form-control" />
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                <label class="control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                <select name="filter_order_status" id="input-order-status" class="form-control">
                  <option value="*">全部</option>
                  <?php if ($filter_order_status == '0') { ?>
                  <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_missing; ?></option>
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
                <label class="control-label" for="input-customer-group">会员等级</label>
                  <select name="filter_customer_group" id="input-customer-group" class="form-control">
                      <option value="">全部</option>

                      <?php foreach($customer_groups as $customer_group){ ?>
                        <?php if($customer_group['customer_group_id'] == $filter_customer_group ){ ?>
                        <option value="<?php echo $customer_group['customer_group_id'] ?>" selected="selected" ><?php echo $customer_group['group_name'] ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $customer_group['customer_group_id'] ?>"><?php echo $customer_group['group_name'] ?></option>
                        <?php } ?>
                      <?php } ?>
                  </select>
              </div>
            </div>


              <div class="col-sm-2">
                  <div class="form-group">
                      <label class="control-label" for="order_payment_status">支付状态</label>

                      <select name="filter_order_payment_status" id="order_payment_status" class="form-control">
                          <option value="0">全部</option>

                          <?php foreach($order_payment_statuses as $m){ ?>
                          <?php if($m['id'] == $filter_order_payment_status ){ ?>
                          <option value="<?php echo $m['id'] ?>" selected="selected" ><?php echo $m['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $m['id'] ?>"><?php echo $m['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                      </select>
                  </div>
                  <div class="form-group">
                      <label class="control-label" for="order_deliver_status">配送状态</label>

                      <select name="filter_order_deliver_status" id="order_deliver_status" class="form-control">
                          <option value="0">全部</option>

                          <?php foreach($order_deliver_statuses as $m){ ?>
                          <?php if($m['id'] == $filter_order_deliver_status ){ ?>
                          <option value="<?php echo $m['id'] ?>" selected="selected" ><?php echo $m['name']; ?></option>
                          <?php } else { ?>
                          <option value="<?php echo $m['id'] ?>"><?php echo $m['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                      </select>
                  </div>
              </div>
              <div class="col-sm-3">
                  <div class="form-group">
                      <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                      <div class="input-group date">
                          <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                  </div>
                  <div class="form-group" style="display:none">
                      <label class="control-label" for="input-date-modified"><?php echo $entry_date_modified; ?></label>
                      <div class="input-group date">
                          <input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" placeholder="<?php echo $entry_date_modified; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                  </div>
                  <div class="form-group">
                      <label class="control-label" for="input_deliver_date">配送日期</label>
                      <div class="input-group date">
                          <input type="text" name="filter_deliver_date" value="<?php echo $filter_deliver_date; ?>" placeholder="配送日期" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                      </div>
                      <a href="javascript:download('PO');">下载采购/生产单》</a>
                      <a style="display:none" href="javascript:download('PSR');">下载生产单》</a>
                      <a href="javascript:download('DO');">配送列表》</a>
                      <a href="javascript:download('PR');">生产列表》</a>
                  </div>
                  <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              </div>

              <div class="col-sm-2">
                  <div class="form-group">
                      <label class="control-label" for="input-station">订单平台</label>
                      <select name="filter_station" id="input-station" class="form-control"  <?php if($station_set){ ?>disabled="disabled"<?php } ?>>
                          <option value="">全部</option>

                          <?php foreach($stations as $station){ ?>
                          <?php if($station_set){ ?>
                          <option value="<?php echo $station['station_id']; ?>" <?php if($station['station_id']==$station_set){ ?>selected="selected"<?php } ?>><?php echo $station['name']; ?></option>
                          <?php }else{ ?>
                          <option value="<?php echo $station['station_id']; ?>" <?php if($station['station_id']==$filter_station_id){ ?>selected="selected"<?php } ?>><?php echo $station['name']; ?></option>
                          <?php } ?>
                          <?php } ?>
                      </select>
                  </div>
                  <div class="form-group">
                      <label class="control-label" for="input-driver">司机<span title="为优化查询速度，方便打印，查询司机需要指定配送日期，将列出改司机当天配送所有订单(显示限定100单)" style="color: #CC0000">(指定配送日期)</span></label>
                      <select name="filter_driver" id="input-driver" class="form-control">
                          <option value="">全部</option>

                          <?php foreach($driverList as $m){ ?>
                          <?php if($m['logistic_driver_id'] == $filter_driver ){ ?>
                          <option value="<?php echo $m['logistic_driver_id']; ?>" selected="selected" >
                              <?php if($m['status'] ==1){ ?> <?php echo $m['logistic_driver_title']; ?> <?php }else{ ?>[停用]<?php echo $m['logistic_driver_title']; ?><?php }?> </option>
                          <?php } else { ?>
                          <option value="<?php echo $m['logistic_driver_id']; ?>"><?php if($m['status'] ==1){ ?> <?php echo $m['logistic_driver_title']; ?> <?php }else{ ?>[停用]<?php echo $m['logistic_driver_title']; ?><?php }?></option>
                          <?php } ?>
                          <?php } ?>
                      </select>
                  </div>
              </div>


          </div>
        </div>
        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" name="checkall" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-right"><?php if ($sort == 'o.order_id') { ?>
                    <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'customer') { ?>
                    <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"></a>配送联系人
                      <?php } else { ?>
                    <a href="<?php echo $sort_customer; ?>"><?php //echo $column_customer; ?></a>配送联系人
                      <?php } ?></td>
                  <td class="text-left">商务信息</a></td>
                  <td class="text-left" style="width:95px"><?php if ($sort == 'status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                    <td class="text-left">配送状态</a></td>
                    <td class="text-left">打印状态</a></td>
                    <td class="text-left" style="width:125px">支付信息</a></td>
                  <td class="text-left"><?php if ($sort == 'o.date_added') { ?>
                        <a href="<?php echo $sort_deliver_date; ?>" class="<?php echo strtolower($order); ?>">配送日期</a>
                        <?php } else { ?>
                        <a href="<?php echo $sort_deliver_date; ?>">配送日期</a>
                        <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'o.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>">下单日期</a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>">下单日期</a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($orders) { ?>
                <?php foreach ($orders as $order) { ?>
                <?php
                    $cancelled = "";
                    $lock = false;
                    if($order['order_status_id'] == CANCELLED_ORDER_STATUS){
                        $cancelled = "cancelled_order";
                        $lock = true;
                    }

                    //if( !in_array($order['order_deliver_status_id'],unserialize(ALLOW_M_DELIVER)) && !in_array($order['order_payment_status_id'],unserialize(ALLOW_M_PAYMENT)) ){
                    //    $lock = true;
                    //}

                    if($order['order_deliver_status_id'] != 1){
                        $lock = true;
                    }

                    if( in_array($order['order_status_id'],unserialize(LOCK_ORDER_STATUS)) ){
                        $lock = true;
                    }

                    //var_dump($order);
                ?>
                <tr class="<?=$cancelled; ?>">
                  <td class="text-center"><?php if (in_array($order['order_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" />
                    <?php } ?>
                    <input type="hidden" name="shipping_code[]" value="<?php echo $order['shipping_code']; ?>" /></td>
                  <td class="text-right" style="font-size: 130%">
                      <b>
                          <?php
                          if($user_group_id == CS_VIEW){
                            echo $order['orderid'].'<br>[#'.$order['order_id'].']';
                          }
                          elseif($user_group_id == ADMIN_VIEW){
                            echo $order['orderid'].'<br>[#'.substr($order['order_id'],-3).']';
                          }
                          else{
                            echo $order['order_id'];
                          }
                          ?>
                      </b>
                      <span title="首单信息每天21:15后更新"><?php if($order['firstorder'] == 1){ echo "<br />首单"; } ?></span>
                      <?php if($order['station_id'] == 2){ ?>
                      <br><span style="color:red;">快</span>
                      <?php } ?>
                  </td>
                  <td class="text-left">
                      <?php echo $order['shipping_firstname']."(".$order['customer'].")"."<br />".$order['shipping_phone']; ?>
                      <br />
                      <?php
                        echo $order['shipping_address'];
                      ?>
                      <?php if($order['comment']) { ?>
                      <div style="margin: 3px 0; padding: 3px;  border: 1px dashed;"/>
                        <strong>订单备注：</strong><?php echo $order['comment']; ?>
                      </div>
                      <?php } ?>

                      <?php
                        if($order['logistic_driver_title']){
                            echo "<br />物流信息：".$order['logistic_driver_title']."[".$order['logistic_driver_phone']."]";
                        }
                      ?>
                  </td>
                  <td class="text-left">
                      <?php if($order['bd_name']){ ?>
                      <div style="margin: 3px 0; padding: 1px; " title="<?php echo $order['bd_name']; ?>电话: <?php echo $order['bd_phone']; ?>"/>
                      <strong>BD: </strong><?php echo $order['bd_name']; ?>



                        <?php if($updateCustInfo){ ?>
                        <br>
                        修改为：<select order_id="<?php echo $order['order_id'];?>" class="order_bd_change">
                            <?php foreach($bds as $bdk=>$bdv){ ?>

                            <?php $order_bd_sel = $bdv['bd_id'] == $order['bd_id'] ? "selected=selected" : "";?>

                            <option <?php echo $order_bd_sel;?> value="<?php echo $bdv['bd_id'];?>"><?php echo $bdv['bd_name'];?></option>
                            <?php } ?>
                        </select>
                        <?php } ?>
                      
                      
                      
                      </div>
                      <?php } ?>

                      <?php if($order['is_nopricetag']){ ?>
                          <div style="text-align:center; margin: 3px 0; padding: 1px;  border: 1px dashed; background-color:#ffff00"/>无价签</div>
                      <?php } ?>
                      
                      
                  </td>
                  <td class="text-left">
                      <?php echo $order['status']; ?>
                      <?php if( in_array($order['order_status_id'],unserialize(ALLOW_CANCEL)) && !$lock && $updateOrderStatus ){ ?>
                          <br />
                          <!-- 1.如果订单状态为分拣中，则需要确定是否通知仓库停止分拣
                               2.如果仓库确认之后，关于退款需判断客户是否含已支付，有微信支付wxpy，如果客户有要求，财务退金额，系统退余额
                               才可以完成取消动作，如果全为余额支付，系统直接退余额之后就可改变订单状态为已取消
                               3.未支付的订单可以直接由系统改为已取消状态-->
                          <?php if($order['order_status_id'] == 1){ ?>
                            <button class="change_status" style="color:#117700" onclick="javascript:processOrder('orderStatus',<?=$order['order_id']; ?>, 2);return false;">确认订单</button>
                          <?php } ?>

                          <!--<?php if($order['wxpay_paid'] >= 0 && ($order['order_payment_status_id'] == 2 || $order['order_payment_status_id'] == 3)){ ?>
                          <select name="wx_return_confirm" id="order_select<?php echo $order['order_id']; ?>">
                              <option value="0">财务未退微信支付</option>
                              <option value="1">财务已退微信支付</option>
                          </select>
                          <?php } ?>-->
                        <?php if($order['order_deliver_status_id'] == 1){ ?>

                          <?php if($order['order_status_id'] == 5){ ?>
                          <button class="change_status" style="color:#ff2222" onclick="javascript:processOrder('orderStatus',<?=$order['order_id']; ?>, 5);return false;">取消分拣中的订单</button>
                          <?php } ?>
                          <?php if($order['order_status_id'] == 6){ ?>
                          <button class="change_status" style="color:#ff2222" onclick="javascript:processOrder('orderStatus',<?=$order['order_id']; ?>, 6);return false;">取消已拣完的订单</button>
                          <?php } ?>
                          <?php if($order['order_status_id'] == 2){ ?>
                          <button class="change_status" style="color:#ff2222" onclick="javascript:processOrder('orderStatus',<?=$order['order_id']; ?>, 3);return false;">取消定单</button>
                          <?php } ?>

                        <?php } ?>

                      <?php } ?>
                  </td>
                  <td class="text-left">
                      <?php echo $order['deliver_status']; ?>
                      <!-- 仅未锁定的，已分拣的订单可以配送出库 -->
                      <?php if( in_array($order['order_deliver_status_id'],unserialize(ALLOW_M_DELIVER)) && !$lock && $updateDeliverOn && $order['order_status_id'] == ORDER_SORTED_STATUS ){ ?>
                          <i>改为:</i><br />
                          <?php if($order['order_deliver_status_id'] == 1) { ?>
                                <button class="change_status" onclick="javascript:processOrder('deliver',<?=$order['order_id']; ?>, 2, '配送出库');return false;">配送出库</button>
                          <?php } ?>
                      <?php } ?>

                      <?php if( ($order['order_deliver_status_id'] == 2) && $updateDeliverStatus) { ?>
                          <button class="change_status" style="color:#117700" onclick="javascript:processOrder('deliver',<?=$order['order_id']; ?>, 3, '配送完成');return false;">配送完成</button>
                          <?php if($order['station_id'] == 1){ ?>
                              <button class="change_status"  style="color:#ff2222" onclick="javascript:processOrder('deliver',<?=$order['order_id']; ?>, 7, '配送失败(生鲜)');return false;">配送失败</button>
                          <?php } ?>
                      <?php } ?>

                      <?php if($order['order_deliver_status_id'] == 10 && $updateReDeliverStatus) { ?>
                          <br />
                          <button class="change_status"  style="color:#117700" onclick="javascript:processOrder('deliver',<?=$order['order_id']; ?>, 7, '配送失败(后续操作退货入库)');return false;">配送失败</button>
                          <button class="change_status"  style="color:#ff2222" onclick="javascript:processOrder('deliver',<?=$order['order_id']; ?>, 11, '重新配送(配送日期延后一天)');return false;">重新配送</button>
                      <?php } ?>
                  </td>
                   <td class="text-left">
                      <?php echo $order['order_print_status'] == 0 ?"未打印":"已打印"; ?>
                      <?php if($order['order_print_status'] == 0 && $updatePrintFlag){ ?>
                          <br /><i>改为:</i>
                          <button class="change_status" onclick="javascript:processOrder('order_print_status',<?=$order['order_id']; ?>);return false;">已打印</button>

                      <?php } ?>
                  </td>
                  <td class="text-left">
                      <b>小计: </b><?php echo $order['sub_total']; ?><br />
                      <?php if(abs($order['shipping_fee'])) { ?>
                        <b>运费: </b><?php echo $order['shipping_fee']; ?><br />
                      <?php } ?>
                      <?php if(abs($order['discount_total'])) { ?>
                        <b>优惠: </b><?php echo $order['discount_total']; ?><br />
                      <?php } ?>
                      <?php if(abs($order['credit_paid'])) { ?>
                        <b>余额支付: </b><?php echo $order['credit_paid']; ?><br />
                      <?php } ?>

                      <?php if(abs($order['wxpay_paid'])) { ?>
                        <b>微信支付: </b><?php echo $order['wxpay_paid']; ?><br />
                      <?php } ?>

                      <?php if(abs($order['user_point_paid'])) { ?>
                        <b>积分支付: </b><?php echo $order['user_point_paid']; ?><br />
                      <?php } ?>

                      <div style="font-size:110%;text-align:left; margin: 3px 0; padding: 1px;  border: 1px dashed;"/>
                        <b>应收: </b><?php echo $order['need_pay']; ?><br />
                      </div>
                      <?php if($order['order_payment_status_id'] == 2){ ?>
                        <div style="font-size:110%;text-align:left; margin: 3px 0; padding: 1px;  border: 1px dashed; background-color: #00ff00;"/>
                      <?php } else{ ?>
                        <div style="font-size:110%;text-align:left; margin: 3px 0; padding: 1px;  border: 1px dashed; background-color: #FF2222;"/>
                      <?php } ?>
                        <?php echo $order['payment_status']; ?>(<?php echo $order['payment_method']; ?>)
                        
                        
                        
                      </div>


                      <?php if( in_array($order['order_payment_status_id'],unserialize(ALLOW_M_PAYMENT)) && !$lock && $updatePayment){ ?>
                          <br /><i>改为:</i><br />
                          <button class="change_status" onclick="javascript:processOrder('payment',<?=$order['order_id']; ?>, 2);return false;">现金已支付</button>

                      <?php } ?>
                  </td>
                  <td class="text-left">
                      <?php echo $order['deliver_date']; ?>
                      <?php
                          if( in_array($order['order_deliver_status_id'],unserialize(ALLOW_M_DELIVER_DATE)) ) {
                          //TODO: 改延后一天
                          }
                      ?>
                      <br />
                      <b>配送:</b><?php echo $order['shipping_method']; ?>
                  </td>
                  <td class="text-left">
                      <?php echo $order['date_added']; ?>
                      <?php if($order['date_modified'] <> $order['date_added']){ ?>
                      <br />[修改] <?php echo $order['date_modified']; ?>
                      <?php } ?>
                  </td>
                  <td class="text-right">
                      <a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                    <?php if(!$lock && false) {?>
                      <a href="<?php echo $order['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                      <a href="<?php echo $order['delete']; ?>" id="button-delete<?php echo $order['order_id']; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                    <?php } ?>
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
            <?php if($filter_driver && $filter_deliver_date){ ?>
            <div class="col-sm-6 text-left">共<?php echo $currentListSize; ?>单</div>
            <?php } else{ ?>
            <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
            <div class="col-sm-6 text-right"><?php echo $results; ?></div>
            <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
    var url = 'index.php?route=sale/order&token=<?php echo $token; ?>';

    //Fix url with all filters.
    url += fixUrl(2);

    var filter_driver = $('select[name=\'filter_driver\']').val();
    var filter_deliver_date = $('input[name=\'filter_deliver_date\']').val();

    if (filter_driver && !filter_deliver_date) {
        alert('查询条件指定司机，请同时选择配送日期。');
        return;
    }

    location = url;
});

$('.order_bd_change').on('change', function() {
    processOrder('order_bd',$(this).attr("order_id"),$(this).val());
});

function download(type){
    var deliver_date = $('input[name=\'filter_deliver_date\']').val();

    if(!deliver_date){
        alert('请选择配送日期。');
        exit();
    }

    var filter_customer_group = $('select[name=\'filter_customer_group\']').val();
    var filter_station = $('select[name=\'filter_station\']').val();

    if(!type){
    //PO for Purchase Order, PSR for Pickup-spot Receipt
        alert('请选择指定下载表单类型。');
        exit();
    }

    var url = 'index.php?route=sale/order/download&token=<?php echo $token; ?>';
    url += '&deliver_date=' + encodeURIComponent(deliver_date);
    url += '&type=' + encodeURIComponent(type);

    if(filter_customer_group > 0){
        url += '&filter_customer_group=' + encodeURIComponent(filter_customer_group);
    }

    if(filter_station > 0){
        url += '&filter_station=' + encodeURIComponent(filter_station);
    }

    window.open(url,"_blank");
    //window.open(url,name,features);
}

//Add extra order operations
function processOrder(method,order_id,status_id,msg){
    var bool=false;
    switch(method)
    {
        case 'orderStatus':
            if(status_id == 2){
                bool = confirm('将订单['+order_id+']改为［已确认］?');
            }
            else if(status_id == 3){
                bool = confirm('取消订单['+order_id+']，订单取消后无法再修改?');
            }else if(status_id == 5){
                bool = confirm('确认取消正在分拣的订单['+order_id+']？,该订单将会被取消！');
            }else if(status_id == 6){
                bool = confirm('确认取消已拣完的订单['+order_id+']？，该订单将会被取消！');
            }
            break;
        case 'deliver':
            if(msg !== ''){
                bool = confirm('确认将订单['+order_id+']改为['+msg+']?');
            }
            else{
                bool = confirm('确认配送订单['+order_id+']?');
            }
            break;
        case 'payment':
            bool = confirm('确认订单['+order_id+']已支付?');
            break;
        case 'order_print_status':
           bool = confirm('确认订单['+order_id+']已打印?');
           break;
        case 'order_bd':
           bool = confirm('确认修改订单['+order_id+']和用户所属的BD?');
           break;
        default:
            break;
    }

    if(!bool){
        exit();//取消操作退出
    }

    var url = 'index.php?route=sale/order/'+method+'&token=<?php echo $token; ?>';
    if (order_id) {
        url += '&order_id=' + encodeURIComponent(order_id);
        if(status_id){
            url += '&status_id=' + encodeURIComponent(status_id);
        }
    }

    //Fix url with all filters.
    url += fixUrl(1);

    location = url;
}

function fixUrl(type){
    var url ='';

    var filter_order_id = $('input[name=\'filter_order_id\']').val();

    if (filter_order_id) {
        url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
    }

    var filter_customer = $('input[name=\'filter_customer\']').val();

    if (filter_customer) {
        url += '&filter_customer=' + encodeURIComponent(filter_customer);
    }

    var filter_order_status = $('select[name=\'filter_order_status\']').val();
    if (filter_order_status != '*') {
        url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
    }

    var filter_order_payment_status = $('select[name=\'filter_order_payment_status\']').val();
    if (filter_order_payment_status != '*') {
        url += '&filter_order_payment_status=' + encodeURIComponent(filter_order_payment_status);
    }

    var filter_order_deliver_status = $('select[name=\'filter_order_deliver_status\']').val();
    if (filter_order_deliver_status != '*') {
        url += '&filter_order_deliver_status=' + encodeURIComponent(filter_order_deliver_status);
    }

    var filter_customer_group = $('select[name=\'filter_customer_group\']').val();

    if (filter_customer_group) {
        url += '&filter_customer_group=' + encodeURIComponent(filter_customer_group);
    }

    var filter_station = $('select[name=\'filter_station\']').val();

    if (filter_station) {
        url += '&filter_station=' + encodeURIComponent(filter_station);
    }

    var filter_driver = $('select[name=\'filter_driver\']').val();
    if (filter_driver) {
        url += '&filter_driver=' + encodeURIComponent(filter_driver);
    }

    var filter_date_added = $('input[name=\'filter_date_added\']').val();

    if (filter_date_added) {
        url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
    }

    var filter_date_modified = $('input[name=\'filter_date_modified\']').val();

    if (filter_date_modified) {
        url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
    }

    var filter_deliver_date = $('input[name=\'filter_deliver_date\']').val();

    if (filter_deliver_date) {
        url += '&filter_deliver_date=' + encodeURIComponent(filter_deliver_date);
    }

    
     if(type == 1){
        var filter_page = <?php echo isset($_GET['page']) ? $_GET['page'] : 0;?>

        if (filter_page > 0) {
            url += '&page=' + encodeURIComponent(filter_page);
        }
    }

    return url;
}

//--></script> 
  <script type="text/javascript"><!--
$('input[name=\'filter_customer\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',            
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['customer_id']
                    }
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'filter_customer\']').val(item['label']);
    }    
});
//--></script> 
  <script type="text/javascript"><!--
$('input[name^=\'selected\']').on('change', function() {
    $('#button-shipping, #button-invoice').prop('disabled', true);
    
    var selected = $('input[name^=\'selected\']:checked');
    
    if (selected.length) {
        $('#button-invoice').prop('disabled', false);
    }
    
    for (i = 0; i < selected.length; i++) {
        if ($(selected[i]).parent().find('input[name^=\'shipping_code\']').val()) {
            $('#button-shipping').prop('disabled', false);
            
            break;
        }
    }
});

$('input[name=\'checkall\']').on('change', function(){
    $('#button-shipping, #button-invoice').prop('disabled', true);

    var selected = $('input[name^=\'selected\']:checked');

    if (selected.length) {
        $('#button-invoice').prop('disabled', false);
    }

    for (i = 0; i < selected.length; i++) {
        if ($(selected[i]).parent().find('input[name^=\'shipping_code\']').val()) {
            $('#button-shipping').prop('disabled', false);

            break;
        }
    }
});

$('input[name^=\'selected\']:first').trigger('change');

$('a[id^=\'button-delete\']').on('click', function(e) {
    e.preventDefault();
    
    if (confirm('<?php echo $text_confirm; ?>')) {
        location = $(this).attr('href');
    }
});
//--></script> 
  <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
  <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
  <script type="text/javascript"><!--
$('.date').datetimepicker({
    pickTime: false
});
//--></script></div>
<?php echo $footer; ?>