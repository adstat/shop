<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <div class="alert alert-warning">批量更改订单支付状态时,系统将只确认订单状态为："已拣完"或"已完成"，配送状态为"配送中"或"配送完成"的订单。未被确认的订单将列在"未确认支付订单中"</div>
        <form method="post" enctype="multipart/form-data" id="form-payment-status" class="form-horizontal">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <td colspan="3">批量订单ID</td>
                    <td>操作</td>
                </tr>
                <tr>
                    <td colspan="3" style="height:100px">
                        <textarea id="financial_orders_id" style="width:100%;height:100%"></textarea>

                        <input name="financial_ordersids" id="financial_ordersids" type="hidden" value=""/>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger" onclick="checkOrders();">确认支付</button>
                    </td>
                </tr>
           <!--     <tr>
                    <td colspan="3">支付状态</td>
                    <td>批量操作</td>
                </tr>  -->
                <!--       <tr>

                          <td  colspan="3">
                               <select name="payment_status" id="input-payment" class="form-control">
                                   <option value="" >-</option>
                                   <?php foreach($paymentstatus as $paymentstatu) { ?>
                                   <?php if($paymentstatu['status_id'] == $payment_status) { ?>
                                   <option value="<?php echo $paymentstatu['status_id']; ?>" selected="selected"><?php echo $paymentstatu['payment_name']; ?></option>
                                   <?php } else { ?>
                                   <option value="<?php echo  $paymentstatu['status_id']; ?>"><?php echo $paymentstatu['payment_name']; ?></option>
                                   <?php } ?>
                                   <?php } ?>
                               </select>
                           </td>
                           <td colspan = "1" align="left"><button type="button" class="btn btn-danger" onclick="updatePaymentStatus();">批量更改</button></td>  -->
                </tr>
                </thead>
            </table>
        </form>

       <!-- <table  id='ordersinfo'  class="table table-bordered table-hover" >
            <div>需支付订单</div>
            <thead>
            <tr>
                <td>订单号</td>
                <td>订单状态</td>
                <td>支付状态</td>
                <td>配送状态</td>
                <td>支付信息</td>
            </tr>
            </thead>
            <tbody >

            </tbody>
        </table>  -->
      <!--  <div>总的订单数目:<span id = "sum"></span></div> -->
        <div> 已确认订单数目:<span id = "confirm_sum" ></span></div>
        <div>未确认订单数目:<span id = "unconfirm_sum" ></span></div>
    <hr>
        <table  id='unordersinfo'  class="table table-bordered table-hover" >
            <div>未确认支付订单</div>
            <thead>
            <tr>
                <td>订单号</td>
                <td>订单状态</td>
                <td>支付状态</td>
                <td>配送状态</td>
                <td>支付信息</td>

            </tr>
            </thead>
            <tbody >

            </tbody>
        </table>

    </div>
</div>
<?php echo $footer ?>
<script>
    function styleChange(str){
        //把一列数据变为逗号分隔
        return str.replace(/[\r\n]/g, ',');
    }
    function isEmptyObject(e) {
        var t;
        for (t in e)
            return false;
        return true;
    }
    function checkOrders(){
        var orders = $('#financial_orders_id').val();
        var orderids_ids = styleChange(orders);
        $('#financial_ordersids').val(orderids_ids);

        $.ajax({
            type: 'GET',
            url: 'index.php?route=sale/financial_confirm/checkOrders&token=<?php echo $_SESSION["token"]; ?>&orderids_ids='+orderids_ids ,
            dataType: 'json',
            success: function(data){
                $('#financial_orders_id').val(data[3]);
                getUnOrdersInfo();
                if(data[1]){
                    var html= '';
                    html += '<span>'+ data[1]+ '</span>';
                    $("#confirm_sum").html(html);
                }
                if(data[0]){
                    var html= '';
                    html += '<span>'+ data[0]+ '</span>';
                    $("#unconfirm_sum").html(html);
                }
                if(data[2]){
                    alert(data['2']+"不存在的订单会被删除,未能确认的订单将会出现在下面的列表中");
                }

            },

        });
    }



    function getUnOrdersInfo(){
        var orders = $('#financial_orders_id').val();
        var orderids_ids = styleChange(orders);
        $('#financial_ordersids').val(orderids_ids);
        $.ajax({
            type: 'GET',
            url: 'index.php?route=sale/financial_confirm/getUnOrdersInfo&token=<?php echo $_SESSION["token"]; ?>&orderids_ids='+orderids_ids,
            dataType: 'json',
            success: function(data){
                console.log(data);
               // $('#unordersinfo tbody tr').remove();
                html = '';
                $.each(data ,function(key,val) {
                    $.each(val ,function(key,v){
                        html += '<tr>';
                        html += '<td>' + v.order_id + '</td>';
                        html += '<td>' + v.order_status_name + '</td>';
                        html += '<td>' + v.payment_status_name + '</td>';
                        html += '<td>' + v.deliver_status_name + '</td>';
                        html += '<td>';
                        html += '小计:'+ v.sub_total ;
                        html += '&nbsp;';
                        if(v.shipping_fee){
                            html +=   '运费:'+ v.shipping_fee;
                            html += '&nbsp;';
                        }
                        if(v.discount_total){
                            html +=   '优惠:'+ v.discount_total;
                            html += '&nbsp;';
                        }
                        if(v.credit_paid){
                            html +=   '余额支付:'+ v.credit_paid;
                            html += '&nbsp;';
                        }
                        html += '应收:'+ v.total;
                        html += '</td>';
                        html += '</tr>';

                    });
                });
                $('#unordersinfo tbody ').append(html);

            },


        });

    }






</script>