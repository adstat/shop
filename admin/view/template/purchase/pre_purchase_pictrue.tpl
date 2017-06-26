<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>上传采购收货单</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if (isset($error_warning) && $error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if (isset($success_msg) && $success_msg) { ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success_msg; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i>采购单<?php echo $filter_purchase_order_id ? '-'.$filter_purchase_order_id:'' ?>
        </h3>
      </div>
      <div class="panel-body">
          <div class="tab-content">
            <div class="well">
              <div class="row">
                <div class="col-sm-3">
                  <div class="form-group">
                    <label class="control-label" for="input-purchase-order-id">采购单ID</label>
                    <input type="text" name="filter_purchase_order_id" value="<?php echo $filter_purchase_order_id ? $filter_purchase_order_id:'' ;?>" placeholder="采购单ID" id="input-purchase-order-id" class="form-control" />
                  </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="input-supplier-type">供应商</label>
                        <select name="supplier_type" id="input-supplier-type" class="form-control" >
                            <option value="*"></option>
                            <?php foreach ($supplier_types as $s_type) { ?>
                            <?php if ($s_type['supplier_id'] == $supplier_type) { ?>
                            <option value="<?php echo $s_type['supplier_id']; ?>" selected="selected"><?php echo $s_type['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $s_type['supplier_id']; ?>"><?php echo $s_type['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="input-order-status">订单状态</label>
                        <select name="filter_order_status_id" id="input-order-status" class="form-control" >
                            <option value="*"></option>
                            <?php foreach ($order_statuses as $order_status) { ?>
                            <?php if ($order_status['order_status_id'] == $status) { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="input-station-id">商品类型</label>
                        <select name="station_id" id="input-station-id" class="form-control" >
                            <option value="*"></option>
                            <?php foreach ($order_stations as $key=>$order_station) { ?>
                            <?php if ($key == $station_id) { ?>
                            <option value="<?php echo $key; ?>" selected="selected"><?php echo $order_station; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $key; ?>"><?php echo $order_station; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="input_date_deliver">到货日期</label>
                        <div class="input-group date">
                        <input type="text" name="date_deliver" value="<?php echo $order_id ? $date_deliver: ''; ?>" placeholder="到货日期" data-date-format="YYYY-MM-DD" id="input-date-deliver" class="form-control" />
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group" style="margin-top: 20px">
                    <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="panel-body">
              <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-general" data-toggle="tab" onclick="orderList();">采购单列表</a></li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="tab-general">
                    <div id = "general"></div>
                </div>
            </div>

            <?php if(!$order_id){ ?>
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover">
              <thead></thead>
              </table>
            </div>
            <?php } ?>

            <?php if($order_id){ ?>
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <td>商品ID</td>
                  <td>商品名称</td>
                  <td>采购数量</td>
                  <td>供应商数量</td>
                  <td>实收数量</td>
                </tr>
              </thead>
              <tbody >
              <?php if($filter_purchase_order_id){ ?>
              <?php foreach($products as $key=>$value){ ?>
                <tr>
                    <td><?php echo $value['product_id'];?></td>
                    <td><?php echo $value['name'];?></td>
                    <td><?php echo $value['quantity'];?></td>
                    <td><?php echo $value['supplier_quantity'];?></td>
                    <td><?php echo isset($order_get_product_info[$value['product_id']]) ? $order_get_product_info[$value['product_id']] : '';?></td>
                </tr>
              <?php } ?>
              <?php } ?>
              </tbody>
              </table>
            </div>
            <?php } ?>
              <form action="<?php echo $action_image;?>" method="post" enctype="multipart/form-data" id="form-order-image" class="form-horizontal">

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-image"></label>
                <div class="col-sm-10">
                  <!--<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="" /></a>-->
                  <input type="hidden" name="image" value="<?php echo $order_id ? $image : ''; ?>" id="input-image" />
                  <input type="hidden" name="purchase_order_id" value="<?php echo $order_id; ?>" id="input-purchase-order-id" />

                </div>
              </div>

              <div class="table-responsive">
              <table id="images" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                  <td class="text-left">收货单</td>
                  <td>收货单名称</td>
                  <td>收货单编号</td>
                  <td></td>
                </tr>
                </thead>
                <tbody>
                <?php $image_row = 0; ?>
                <?php foreach ($product_images as $product_image) { ?>
                <tr id="image-row<?php echo $image_row; ?>">
                  <td class="text-left"><a href="<?php echo $product_image['image'];?>" id="thumb-image<?php echo $image_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $product_image['thumb']; ?>" alt="" title="" data-placeholder="采购单图片" /></a><input type="hidden" name="product_image[<?php echo $image_row; ?>][image]" value="<?php echo $product_image['image_dir']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                  <td><?php echo $product_image['image_title'];?><input type="hidden" name="product_image[<?php echo $image_row; ?>][image_title]" value="<?php echo $product_image['image_title']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                  <td><?php echo $product_image['image_num'];?><input type="hidden" name="product_image[<?php echo $image_row; ?>][image_num]" value="<?php echo $product_image['image_num']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                  <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="删除图片" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                </tr>
                <?php $image_row++; ?>

                <?php } ?>
                </tbody>
                <tfoot>
                <tr>
                  <td colspan="1"><button type="button" id="button-filter" class="btn btn-primary pull-right" onclick="submitImage();"><i class="fa"></i> 提交收货单图片</button></td>
                  <td colspan="3" class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="添加图片" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                </tr>
                </tfoot>
              </table>
            </div>
            </form>
          </div>

      </div>
    </div>
  </div>
<script tyep="text/javascript">
    $('#button-filter').on('click', function() {
        var result = getUrl();
        location = result['url'];
        //alert(getUrl());
    });

    function orderList(){
        var result = getUrl();
        $('#general').load(result['url_a']);
    }

    function setOrderID(purchase_order_id){
//        alert(purchase_order_id);
        $('input[name=\'filter_purchase_order_id\']').val(purchase_order_id);
    }

    function getUrl(){
        var url = 'index.php?route=purchase/pre_purchase_upload&token=<?php echo $token; ?>';
        var url_a = 'index.php?route=purchase/pre_purchase_upload/purchaseList&token=<?php echo $token; ?>';

        var filter_purchase_order_id = $('input[name=\'filter_purchase_order_id\']').val();

        if (filter_purchase_order_id && filter_purchase_order_id != '') {
            url += '&filter_purchase_order_id=' + encodeURIComponent(filter_purchase_order_id);
            url_a += '&filter_purchase_order_id=' + encodeURIComponent(filter_purchase_order_id);
        }

        var supplier_type = $('select[name=\'supplier_type\']').val();

        if (supplier_type != '*') {
            url += '&supplier_type=' + encodeURIComponent(supplier_type);
            url_a += '&supplier_type=' + encodeURIComponent(supplier_type);
        }

        var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();

        if (filter_order_status_id != '*') {
            url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
            url_a += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
        }

        var station_id = $('select[name=\'station_id\']').val();

        if (station_id != '*') {
            url += '&station_id=' + encodeURIComponent(station_id);
            url_a += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
        }

        var date_deliver = $('input[name=\'date_deliver\']').val();

        if (date_deliver && date_deliver != '') {
            url += '&date_deliver=' + encodeURIComponent(date_deliver);
            url_a += '&date_deliver=' + encodeURIComponent(date_deliver);
        }

        var result = new Array();
        result['url'] = url;
        result['url_a'] = url_a;
        return result;
    }

    var image_row = <?php echo $image_row; ?>;

    function addImage() {

        html  = '<tr id="image-row' + image_row + '">';
        html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail"><img src="" alt="" title="" data-placeholder="采购单" /><input class="int" type="hidden" name="product_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
        html += '  <td class="text-left"><input class="int" type="text" name="product_image[' + image_row + '][image_title]" value="" id="input-image' + image_row + '" /></td>';
        html += '  <td class="text-left"><input class="int" type="text" name="product_image[' + image_row + '][image_num]" value="" id="input-image' + image_row + '" /></td>';
        html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="移除" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';

        $('#images tbody').append(html);

        image_row++;
    }

    function submitImage(){
        var inp_error = 0;
        $('#images tbody input.int').each(function(index, element){
            var val = $(element).val();
            if(val == ''){

                inp_error = 1;
            }
        });

        if(inp_error == 1){
            alert("送货单图片、送货单标题、送货单编号 不能为空！");
            return false;
        }

        $('#form-order-image').submit();
    }

    $('#general').delegate('.pagination a', 'click', function(e) {
        e.preventDefault();
        $('#general').load(this.href);
    });

</script>
 
<script type="text/javascript">
    <!--
    $('.date').datetimepicker({
        pickTime: false
    });
    //-->
</script>
</div>
<?php echo $footer; ?> 