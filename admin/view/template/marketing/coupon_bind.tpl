
<button type="button" value="<?php echo $customerforbidden; ?>"  class="btn btn-primary" style="margin-bottom: 5px; float: right" onclick="customersbind(this);">批量添加</button>
<button type="button" value="<?php echo $customerforbidden; ?>" class="btn btn-primary" style="margin-bottom: 5px; float: right" onclick="customerbind(this);">添加</button>

<form method="post" enctype="multipart/form-data" id="form-coupon-bind" class="form-horizontal">
  <table id="bind_add_row" style="display: none" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
      <td class="text-left">用户</td>
      <td class="text-left">用户电话</td>
      <td class="">用户名称</td>
      <td class="text-left">用户店铺</td>
      <td class="text-left">开始日期 </td>
      <td class="text-left">结束日期</td>
      <td class="text-left">次数</td>
      <td class="text-left">操作</td>
    </tr>
    </thead>
    <tbody>
    <tr>
      <td>
        <input name="customer_id" id="customer_id" placeholder="用户ID" datatype="*" nullmsg="请输入客户ID" onchange="fullWrite()"/>
        <input name="coupon_id" id="coupon_id" type="hidden" value="<?php echo $coupon_id; ?>" />
      </td>
      <td><div id="customer_telephone"></div></td>
      <td><div id="customer_name"></div></td>
      <td><div id="customer_email"></div></td>
      <td>
        <div class="input-group date">
          <input type="text" name="date_start" value="<?php echo $date_start; ?>"  data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
            <span class="input-group-btn">
            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
      </td>
      <td>
        <div class="input-group date">
          <input type="text" name="date_end" value="<?php echo $date_end; ?>"  data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
            <span class="input-group-btn">
            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
      </td>
      <td><input name="coupon_times" id="coupon_times" value="<?php echo $times; ?>" size="2"/></td>
      <td><button type="button" class="btn btn-danger" onclick="addRow();">保存</button></td>
    </tr>
    </tbody>
  </table>
</form>

<form method="post" enctype="multipart/form-data" id="form-coupons-bind" class="form-horizontal">
  <table id="bind_add_rows" style="display: none" class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
      <td colspan="3">批量客户ID</td>
      <td>操作</td>
    </tr>
    <tr>
      <td colspan="3" style="height:100px">
        <textarea id="coupon_cutomers_id" style="width:100%;height:100%"></textarea>
        <input name="coupon_id" id="coupon_id" type="hidden" value="<?php echo $coupon_id; ?>" />
        <input name="coupon_customerids" id="coupon_customerids" type="hidden" value=""/>
      </td>
      <td>
        <button type="button" class="btn btn-danger" onclick="checkCustomers();">检查客户</button>
      </td>
    </tr>
    <tr>
      <td>开始时间</td>
      <td>结束时间</td>
      <td>次数</td>
      <td>批量操作</td>
    </tr>
    <tr>
      <td rowspan="2">
        <div class="input-group date">
          <input type="text" name="date_start" value="<?php echo $date_start; ?>"  data-date-format="YYYY-MM-DD" id="input-date-starts" class="form-control" />
            <span class="input-group-btn">
            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
      </td>
      <td rowspan="2">
        <div class="input-group date">
          <input type="text" name="date_end" value="<?php echo $date_end; ?>"  data-date-format="YYYY-MM-DD" id="input-date-ends" class="form-control" />
            <span class="input-group-btn">
            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
      </td>
      <td rowspan = "2"><input name="coupon_times" id="coupon_times_customers" value="<?php echo $times; ?>"/></td>
      <td colspan = "2" align="left"><button type="button" class="btn btn-danger" onclick="addRows();">批量保存</button></td>
    </tr>
    </thead>
  </table>
</form>
<script type="text/javascript">
  function customerbind(obj){
    if(obj.value){
      $('#bind_add_row').show();
    }else{
      alert('该优惠券未指定用户');
    }
  }
  function customersbind(obj){
    if(obj.value){
      $('#bind_add_rows').show();
    }else{
      alert('该优惠券未指定用户');
    }
  }
  function rowsChange(){
    //把textarea的客户id按照','排列，以数组形式放在后台进行保存
    var customers = $('#coupon_cutomers_id').val();
    var customer_ids = styleChange(customers);
    $('#coupon_cutomers_id').val(customer_ids);
    $('#coupon_customerids').val(customer_ids);
  }
  function styleChange(str){
    //把一列数据变为逗号分隔
    return str.replace(/[\r\n]/g, ',');
  }
  function fullWrite(){
    var customer_id = $('#customer_id').val();
    $.ajax({
      type: 'GET',
      url: 'index.php?route=marketing/coupon/getCustomer&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&customer_id='+customer_id,
      dataType: 'json',
      success: function(data){
        if(data['status'] == 'false'){
          alert(data['message']);
        }else {
          if(data['customerInfo'][0]['status']==0){
            alert('该客户状态不满足条件');
          }else{
            $('#customer_telephone').html(data['customerInfo'][0]['telephone']);
            $('#customer_email').html(data['customerInfo'][0]['merchant_name']);
//            $('#customer_name').val(data['customerInfo'][0]['name']);
            $('#customer_name').html(data['customerInfo'][0]['name']);
          }
        }
      },
      error: function(){
        alert(global.returnErrorMsg);
      }
    });
  }
  function addRow() {
    var customer_id = $('#customer_id').val();
    $.ajax({
      type:'POST',
      //async: false,
      //cache: false,
      url:'index.php?route=marketing/coupon/addbind&token=<?php echo $_SESSION["token"]; ?>&customer_id='+customer_id,
      data:$('#form-coupon-bind').serialize(),
      success: function(data){
        console.log(data);
        if(data == 'true'){
            alert('添加成功');
            $('#customer_id').val('');
            $('#customer_telephone').html('');
            $('#customer_name').html('');
            $('#customer_email').html('');
            $('#coupon_times').val('');
        }else if(data == 'false'){
            alert('请检查是否完成填写');
        }else{
            alert('添加失败');
        }
      },
      error: function(){
          alert('添加失败');
      }
    });
  }
  function addRows(){
    var customers = $('#coupon_cutomers_id').val();
    var customer_ids = styleChange(customers);
    $('#coupon_cutomers_id').val(customer_ids);
    $('#coupon_customerids').val(customer_ids);
    $.ajax({
      type:'POST',
      //async:false,
      //cache:false,
      url:'index.php?route=marketing/coupon/addbinds&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>',
      data:$('#form-coupons-bind').serialize(),
      success: function(data){
        if(data == 'true'){
          alert('保存成功');
          $('#coupon_times_customers').val('');
          $('#coupon_cutomers_id').val('');
        }
        else{
          alert(global.returnErrorMsg);
        }
      },
      error: function(){
        alert(global.returnErrorMsg);
      }
    });
  }
  function checkCustomers(){
    var customers = $('#coupon_cutomers_id').val();
    var customer_ids = styleChange(customers);
    $('#coupon_cutomers_id').val(customer_ids);
    $('#coupon_customerids').val(customer_ids);
    $.ajax({
      type: 'GET',
      url: 'index.php?route=marketing/coupon/checkCustomers&token=<?php echo $_SESSION["token"]; ?>&coupon_id=<?php echo $coupon_id; ?>&customer_ids=' + customer_ids,
      dataType: 'json',
      success: function(data){
        if(!isEmptyObject(data['no'])){
          alert(data['no']+"这些客户不符合要求");
        }
        $('#coupon_cutomers_id').val(data['yes']);
        $('#coupon_customerids').val(data['yes']);
      },
      error: function(){
        alert(global.returnErrorMsg);
      }
    });

  }
  function isEmptyObject(e) {
    var t;
    for (t in e)
      return false;
    return true;
  }
</script>
<script type="text/javascript">
  $('.date').datetimepicker({
    pickTime: false
  });
</script>
