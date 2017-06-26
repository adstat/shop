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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> 退货列表</h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-order-id">订单编号</label>
                <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="订单编号" id="input-order-id" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-product-id">商品编号</label>
                <input type="text" name="filter_product_id" value="<?php echo $filter_product_id; ?>" placeholder="商品编号" id="inout-product-id" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-lable" for="input-return-confirmed">确认状态</label>
                <select name="filter_return_confirmed" id="input-return-confirmed" class="form-control">
                  <option value="*">全部</option>
                  <?php foreach ($return_confirmed as $confirmed) { ?>
                  <?php if($confirmed['confirm'] == $filter_return_confirmed) { ?>
                  <option value="<?php echo $confirmed['confirm']; ?>" selected == "selected"><?php echo $confirmed['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $confirmed['confirm']; ?>"><?php echo $confirmed['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-lable" for="input-logistic-user">物流操作人员</label>
                <select name="filter_logistic_user" id="input-logistic-user" class="form-control">
                  <option value="">全部</option>
                  <?php foreach ($logistic_user as $users) { ?>
                  <?php if($users['user_id'] == $filter_logistic_user) { ?>
                  <option value="<?php echo $users['user_id']; ?>" selected == "selected"><?php echo $users['username']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $users['user_id']; ?>"><?php echo $users['username']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-lable" for="input-date-added">物流登记日期</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo '物流登记日期'; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-lable" for="input-return-reason">退货原因</label>
                <select name="filter_return_reason" id="input-return-reason" class="form-control">
                  <?php if($filter_return_reason == 0) { ;?>
                  <option value="0" selected="selected">全部</option>
                  <option value="1">出库前退货(不包括散件)</option>
                  <option value="2">出库后退货(整件)</option>
                  <option value="3">出库后退货(散件)</option>
                  <option value="4">出库后退货(散整)</option>
                  <?php }  ;?>

                  <?php if($filter_return_reason == 1) { ;?>
                  <option value="0" >全部</option>
                  <option value="1" selected="selected">出库前退货(不包括散件)</option>
                  <option value="2">出库后退货(整件)</option>
                  <option value="3">出库后退货(散件)</option>
                  <option value="4">出库后退货(散整)</option>
                  <?php }  ;?>
                  <?php if($filter_return_reason == 2) { ;?>
                  <option value="0" >全部</option>
                  <option value="1">出库前退货(不包括散件)</option>
                  <option value="2" selected="selected">出库后退货(整件)</option>
                  <option value="3">出库后退货(散件)</option>
                  <option value="4">出库后退货(散整)</option>
                  <?php }  ;?>
                  <?php if($filter_return_reason == 3) { ;?>
                  <option value="0" >全部</option>
                  <option value="1">出库前退货(不包括散件)</option>
                  <option value="2">出库后退货(整件)</option>
                  <option value="3" selected="selected">出库后退货(散件)</option>
                  <option value="4">出库后退货(散整)</option>
                  <?php }  ;?>
                  <?php if($filter_return_reason == 4) { ;?>
                  <option value="0" >全部</option>
                  <option value="1">出库前退货(不包括散件)</option>
                  <option value="2">出库后退货(整件)</option>
                  <option value="3">出库后退货(散件)</option>
                  <option value="4" selected="selected">出库后退货(散整)</option>
                  <?php }  ;?>
                </select>
              </div>
              <div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> 筛选 </button>
              </div>
            </div>
          </div>

        </div>
        <form method="post" enctype="multipart/form-data" target="_blank" id="form-return">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-right"><?php if ($sort == 'op.order_id') { ?>
                    <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_product_id; ?></td>
                  <td class="text-left"><?php echo $column_product; ?></td>
                  <td class="text-left"><?php echo $column_quantity; ?></td>
                  <td class="text-left"><?php echo $column_price; ?></td>
                  <td class="text-left">是否散件</td>
                  <td class="text-left">整箱件数</td>
                  <td class="text-left"><?php echo $column_add_user; ?></td>
                  <td class="text-left"><?php echo $column_add_date; ?></td>
                  <td class="text-left"><?php echo $column_return_reason; ?></td>
                  <td class="text-left"><?php echo $column_return_status; ?></td>
                  <td class="text-left">状态</a></td>
                  <!--<td class="text-right"><?php echo $column_action; ?></td>-->
                </tr>
              </thead>
              <tbody id="return_orderlist">
              <?php if ($returns) { ?>
              <?php foreach ($returns as $return) { ?>
                <tr id = "<?php echo $return['orderid'].'-'.$return['productid']; ?>" class="<?php echo $return['orderid']; ?>">

                  <td class="text-center"><?php if (in_array($return['orderid'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $return['orderid']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $return['orderid']; ?>" />
                    <?php } ?></td>
                  <td class="text-right" style="font-size: 130%">
                    <b>
                      <?php echo $return['orderid']; ?>
                    </b>

                    <?php //echo "<br />";  //暂停后台确认退货?>
                    <?php if(!$return['confirmed'] && false){ ?>
                    <button class = "btn btn-primary" type="button" value="<?php echo $return['orderid']; ?>" class="change_status" style="color:#ffffff" onclick="confirmReturnInfo($(this),$(this).val());" >提交退货</button>
                    <?php } ?>

                  </td>
                  <td>
                      <?php echo $return['productid']; ?>
                  </td>
                  <td>
                      <?php echo $return['product']; ?>
                  </td>
                  <td>
                      <?php echo $return['quantity']; ?>
                  </td>
                  <td>
                      <?php echo $return['price']; ?>
                  </td>
                  <?php  if($return['in_part'] == 0) { ; ?>
                  <td>
                     否
                  </td>
                  <?php }else{ ;?>
                   <td>
                     是
                   </td>
                  <?php } ;?>
                  <td>
                      <?php echo $return['box_quantity']; ?>
                  </td>
                  <td>
                      <?php echo $return['username']; ?>
                  </td>
                  <td>
                      <?php echo $return['dateadded']; ?>
                  </td>
                  <td>
                      <?php echo $return['returnreason']; ?>
                  </td>
                  <td>
                      <?php echo $return['statusTitle']; ?>
                  </td>
                  <td class="text-center">
                      <?php echo $return['confirmedTitle']; ?>
                  </td>
                  <!--<td>
                    <a href="<?php echo $return['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                  </td>-->
                </tr>
                <?php } ?><!--foreach的花括号-->
                <?php } ?><!--最外层的花括号-->
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>



      </div>

    </div>


  </div>
  <div></div>


<script type="text/javascript">
  //根据条件筛选退货登记表
  $('#button-filter').on('click', function() {
    var url = 'index.php?route=user/return_product&token=<?php echo $token; ?>';

    //Fix url with all filters.
    url += fixUrl(2);
    location = url;
  });
//  function ifshow(obj){
//    $(obj).parent('span').next().show();
//  }
  function fixUrl(type){
    var url = '';

    var filter_order_id = $('input[name=\'filter_order_id\']').val();
    if (filter_order_id) {
      url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
    }

    var filter_product_id = $('input[name=\'filter_product_id\']').val();
    if (filter_product_id) {
      url += '&filter_product_id=' + encodeURIComponent(filter_product_id);
    }

    var filter_return_confirmed = $('select[name=\'filter_return_confirmed\']').val();
    if (filter_return_confirmed != '*') {
      url += '&filter_return_confirmed=' + encodeURIComponent(filter_return_confirmed);
    }

    var filter_date_added = $('input[name=\'filter_date_added\']').val();
    if (filter_date_added) {
      url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
    }

    var filter_logistic_user = $('select[name=\'filter_logistic_user\']').val();
    if (filter_logistic_user) {
      url += '&filter_logistic_user=' + encodeURIComponent(filter_logistic_user);
    }

    var filter_return_reason = $('select[name=\'filter_return_reason\']').val();
    if (filter_return_reason) {
      url += '&filter_return_reason=' + encodeURIComponent(filter_return_reason);
    }

    if(type == 1){
      var filter_page = <?php echo isset($_GET['page']) ? $_GET['page'] : 0;?>

        if (filter_page > 0) {
            url += '&page=' + encodeURIComponent(filter_page);
        }
    }

    return url;
  }
  //合并相同的订单号
  mergeRows(1);
  function mergeRows(col){
    var trs = $("table tr");
    var rows = 1;
    for(var i=trs.length;i>0;i--){
      var cur = $($(trs[i]).find("td")[col]).text();
      var next = $($(trs[i-1]).find("td")[col]).text();
      if(cur==next){
        rows++;
        $($(trs[i]).find("td")[col]).remove();
      } else {
        $($(trs[i]).find("td")[col]).attr("rowspan",rows);
        rows=1;
      }
    }
  }
  $("select[name='rerurn_action_select[]']").change(function(){
    //获取本次确认退货的action_id
    var action_id = $(this).find("option:checked").val();
    var id=$(this).attr("id");

    var bool=false;
    var returnid = id.split("-");
    bool=confirm('确认订单为'+returnid[0]+'商品id为'+returnid[1]+'的商品以"'+$(this).find("option:checked").text()+'"的方式退货');

    if(!bool){
      exit();//取消操作退出
    }

    var opid = id;
    var url = 'index.php?route=user/return_product&token=<?php echo $_SESSION["token"]; ?>';
    $.ajax({
      type:'POST',
      async: false,
      cache: false,
      url:'index.php?route=user/return_product/confirmReturn&token=<?php echo $_SESSION["token"]; ?>&orderid-productid='+opid+'&action_id='+action_id,

    });

    url += fixUrl(1);
    location = url;

  });

  function disableDeliverReturnProduct(obj){
    var url = 'index.php?route=user/return_product&token=<?php echo $_SESSION["token"]; ?>';
    var opid = obj.value;
    //alert(opid);
    var bool = false;
    bool=confirm('确认作废该条信息？');
    if(!bool){
      exit();//取消操作退出
    }

    $.ajax({
      type:'POST',
      async: false,
      cache: false,
      url:'index.php?route=user/return_product/disableDeliverReturnProduct&token=<?php echo $_SESSION["token"]; ?>',
      dataType: 'json',
      data:{
        opid : opid,
      },
      success: function(response){
        if(response){
          alert('删除成功！');
//          $('#'+opid).remove(); 删除元素方法需要考虑合并的单元格的处理情况
          location = url;
        }else{
          alert('删除失败！');
          location = url;
        }
      }
    });
  }

  function confirmReturnInfo(obj,id){
    var url = 'index.php?route=user/return_product&token=<?php echo $_SESSION["token"]; ?>';
    var order_id = id;
    var data = $("#return_orderlist").find("tr");
    console.log(data);
    var productArray = [];
    var order_product = [];
    var count = 0;
    /*
    如果count为0，则v.childNodes[5].innerText为product_id,
     v.childNodes[7].innerText为product_name,
     v.childNodes[9].innerText为quantity,
     v.childNodes[11].innerText为price,
     v.childNodes[13].innerText为in_part,
     v.childNodes[15].innerText为box_quantity,
     如果count大于0，则相应的键值都要减一
     */
    $.each(data,function(i,v){
      if(v.className == order_id){
        if(count == 0){
          var price = v.childNodes[11].innerText;
          productArray.push(v.childNodes[5].innerText);
          productArray.push(v.childNodes[7].innerText);
          productArray.push(v.childNodes[9].innerText);
          productArray.push(v.childNodes[11].innerText);
          productArray.push(v.childNodes[13].innerText);
          productArray.push(v.childNodes[15].innerText);

        }else{
          var price = v.childNodes[10].innerText;
          productArray.push(v.childNodes[4].innerText);
          productArray.push(v.childNodes[6].innerText);
          productArray.push(v.childNodes[8].innerText);
          productArray.push(v.childNodes[10].innerText);
          productArray.push(v.childNodes[12].innerText);
          productArray.push(v.childNodes[14].innerText);
        }
        order_product.push(productArray);
        productArray = []
        count ++;
      }
    });
    console.log(order_product);
    $.ajax({
      type:'POST',
      async: false,
      cache: false,
      url:'index.php?route=user/return_product/confirmReturnInfo&token=<?php echo $_SESSION["token"]; ?>',
      dataType: 'json',
      data:{
        order_id : order_id,
        order_product: order_product,
      },
      success:function(response){
        if(response){
          alert('退货操作成功！');
          location = url;
        }else{
          alert('退货操作失败！');
          location = url;
        }
      }
    });
  }
//  function confirmReturn(obj){
//    var bool=false;
//    var returnid = obj.value.split("-");
//    bool=confirm('确认订单为'+returnid[0]+'商品id为'+returnid[1]+'的商品退货');
//
//    if(!bool){
//      exit();//取消操作退出
//    }
//
//    var opid = obj.value;
//    var url = 'index.php?route=user/return_product&token=<?php echo $_SESSION["token"]; ?>';
//
//    var returnaction = $('#return_action_select').val();
//    alert()
//    $.ajax({
//      type:'POST',
//      async: false,
//      cache: false,
//      url:'index.php?route=user/return_product/confirmReturn&token=<?php echo $_SESSION["token"]; ?>&orderid-productid='+opid,
//
//    });
//    location = url;
//  }

  $('.date').datetimepicker({
    pickTime: false
  });
</script>
</div>
<?php echo $footer; ?>