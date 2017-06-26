<div  class="table-responsive">
  <table class="table table-bordered" id="dnf">
    <thead>
    <tr>
      <th>
        <span class="sp" style="margin-right:24px;">订单号</span>
        <span class="sp" style="margin-right:24px;">分拣量</span>
        <span class="sp" style="margin-right:24px;"> 订单总量</span>
        <span class="sp" style="margin-right:24px;">分拣类型</span>
        <span class="sp" style="margin-right:24px;">下单时间</span>
        <span class="sp" style="margin-right:24px;"> 会员等级</span>
        <span class="sp" style="margin-right:24px;">  地址</th>
    </tr>
    </thead>
    <tbody>
    <?php $i = 0; ?>
    <?php if ($histories) { ?>
    <?php foreach ($histories as $history) { ?>
    <tr>
      <td  id="<?php echo $history['order_id'] ;?>,<?php echo $history['fj_quantity']; ?>,<?php echo $history['product_type_id']; ?>" style="margin-right:24px;"> <span class="sp" style="margin-right:24px;"> <?php echo $history['order_id']; ?> </span>
        <span class="sp" style="margin-right:24px;"><?php echo $history['fj_quantity']; ?></span>
      <span class="sp" style="margin-right:24px;"> <?php echo $history['quantity']; ?></span>
      <span class="sp" style="margin-right:24px;"> <?php echo $history['product_name']; ?></span>
      <span class="sp" style="margin-right:24px;"> <?php echo $history['date_added']; ?></span>
      <span class="sp" style="margin-right:24px;"> <?php echo $history['customer_level']; ?></span>
        <span class="sp" style="margin-right:24px;">  <?php echo $history['shipping_address']; ?></span></td>

    </tr>
    <?php $i++; ?>
    <?php } ?>

    <?php } else { ?>
    <tr>
      <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
    </tr>
    <?php } ?>
    </tbody>
  </table>
</div>
<div class="row">

</div>
<script type="text/javascript">

  function distrOrderToWorker(obj) {
    var w_user_id = $('select[name="filter_w_user_list"]').val();
    var len = $(".distr ").size(); //获取标签的个数
    var arr = [];
    for(var index = 0; index <= len-1; index++){ //创建一个数字数组
      arr[index] = index;
    }
    $.each(arr, function(i){  //循环得到不同的id的值
      var idValue = $(".distr ").eq(i).attr("id");
      if(idValue != ''){
        data =  idValue.split(',');
        var order_id = data[0];
        var w_user_id = $('select[name="filter_w_user_list"]').val();
        var options=$("#input_w_user_list option:selected");
        var inventory_name = options.text();
        var quantity = data[1];
        var product_type_id = data[2];
        $.ajax({
          url: 'index.php?route=user/warehouse_distribution/distrOrderToWoker&token=<?php echo $token; ?>',
          type: 'post',
          dataType: 'json',
          data:{
            order_id : order_id,
            w_user_id : w_user_id,
            inventory_name : inventory_name,
            quantity : quantity,
            product_type_id :product_type_id,
            success: function (response) {

            },

           }
        });
      }
    });
    if(w_user_id == 0) {
      alert('请选择分拣人员');
    }else {
      $('.distr').remove();
    }



  }
</script>
<script type="text/javascript">
  $('#distr-table').delegate('.pagination a', 'click', function(e) {
    e.preventDefault();
    $('#distr-table').load(this.href);
  });
</script>

<script>
//  function remove_color(obj){
//    if(obj.style.backgroundColor!=clickColor)
//      obj.style.backgroundColor=defaultColor;
//    obj.className = 'dist';
//  }
//  function click_color(obj){
//    var tb=obj.parentNode;//获得父节点对象
//    if(chooseRow!=9999){
//      var lastObj=tb.rows[chooseRow];
//      lastObj.style.backgroundColor=defaultColor;
//    }
//    chooseRow=obj.rowIndex;//获得当前行在表格中的序数
//    obj.style.backgroundColor=clickColor;
//
//  }
//  function over_color(obj){
//    if(obj.style.backgroundColor!=clickColor) {
//      obj.style.backgroundColor = overColor;
//      obj.className = 'distr';
//    }
//  }
</script>
<script>
  var defaultColor="#ffffff";
  var overColor="yellow";
  var clickColor="pink";
  var chooseRow=9999;

  (function(){
    var  fileNodes = document.getElementsByTagName("tr");
    var flag = false;　　　　　//当鼠标被按下时，为true,放开是为false
      var indexs =[];　　　　　　//用来存放鼠标经过的单元格在整个表格的位置，鼠标按下时被初始化，
        dnf.onmousedown = function() {
          flag = true;
          indexs = [];

        }
        dnf.onmousemove = function(e) {

          if (flag)  //只有鼠标被按下时，才会执行复合代码
          {
            fileNodes = e.target,dnf.getElementsByTagName("TR");
            console.log(fileNodes);
            fileNodes.style.backgroundColor = overColor;
            fileNodes.className = 'distr';
          }
        }
    dnf.onmouseup = function(e){
      flag = false;
      fileNodes = e.target,dnf.getElementsByTagName("TR");

      fileNodes.style.backgroundColor = defaultColor;
      fileNodes.className = 'dist';
    }

  })();

</script>









