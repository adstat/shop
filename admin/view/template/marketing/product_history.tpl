<div class="panel-body">
<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-delete">
  <div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
    <tr>
      <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
      <td class="text-left">编号</td>
      <td class="text-right">产品ID</td>
      <td class="text-left">产品名称</td>
      <td calss="text-left">产品状态</td>
      <td class="text-right">操作</td>
    </tr>
    </thead>
    <tbody>
    <?php if ($producthistories) { ?>
    <?php foreach ($producthistories as $history) { ?>
    <tr>
      <td class="text-center"><?php if (in_array($history['coupon_product_id'], $selected)) { ?>
        <input type="checkbox" name="selected[]" value="<?php echo $history['coupon_product_id']; ?>" checked="checked" />
        <?php } else { ?>
        <input type="checkbox" name="selected[]" value="<?php echo $history['coupon_product_id']; ?>" />
        <?php } ?></td>
      <td class="text-left"><?php echo $history['coupon_product_id']; ?></td>
      <td class="text-right"><a href='<?php echo $history["product_url"]; ?>' target="_link"><?php echo $history['product_id']; ?></a></td>
      <td class="text-left"><?php echo $history['name']; ?></td>
      <?php if($history['status'] == 1){ ?>
      <td class="text-left">启用</td>
      <?php } else{ ?>
      <td class="text-left" style="color: #DA0000">停用</td>
      <?php } ?>
      <td class="text-right"><button  value="<?php echo $history['name'].';'.$history['status'].';'.$history['coupon_product_id']; ?>" type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" onclick = "getId(this);"><i class="fa fa-pencil"></i></button></td>
      <?php } ?>
      <?php } ?>
      </tbody>
    </table>
  </div>
</form>
<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
</div>
<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:450px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">修改绑定商品状态</h4>
      </div>
      <div class="modal-body">
        <form method="post" enctype="multipart/form-data" id="form-status-bind" class="form-horizontal">
          <table>
            <tr>
              <td align="right">
                <label>产品名称:</label>
              </td>
              <td>
                &nbsp;
                &nbsp;
                &nbsp;
                <input name = "product" id = "product" value="" size="40"/>
                <input name = "product_id" id = "product_id" value="" size="40" type="hidden"/>
                <input name = "status" id = "product_status" value="" type="hidden"/>
              </td>
            </tr>
            <tr>
              <td> &nbsp;</td>
              <td> &nbsp;</td>
            </tr>
            <tr>
              <td align="right">
                <label>是否启用绑定产品:</label>
              </td>
              <td>
                &nbsp;
                &nbsp;
                &nbsp;
                <select name="status" id="status" style="width:260px">
                  <option value="1" selected="selected">启用</option>
                  <option value="0">停用</option>
                </select>
              </td>
            </tr>
          </table>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" onclick = "update();" data-dismiss="modal">提交更改</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal -->
</div>
<script type="text/javascript">
function update(){
  $.ajax({
    type:'POST',
    async: false,
    cache: false,
    url:'index.php?route=marketing/coupon/updateStatus&token=<?php echo $_SESSION["token"]; ?>',
    data:$('#form-status-bind').serialize(),
    success:function(data){
      if(data=='true'){
        alert('修改绑定产品状态成功');
        productHistory();
      }else{
        alert(global.returnErrorMsg);
      }
    },
    error: function(){
      alert(global.returnErrorMsg);
    }
  });
}
function getId(obj){
  var strs = obj.value.split(";");
  $('#product').val(strs[0]);
  $('#product_status').val(strs[1]);
  $('#product_id').val(strs[2]);
  $('#status').get(0).selectedIndex=-(parseInt(strs[1])-1);
}
</script>
