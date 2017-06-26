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
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">

            <div class="well">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="input-order-id">订单号</label>
                            <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="订单号" id="input-order-id" class="form-control" />

                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                        <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                        </div>
                    </div>

            </div>
            </div>
           <table>
               <tbody>
                <?php if($sorterror) { ?>
                <?php foreach($sorterror as $serror) { ?>
                <td style="width: 100px">订单号:<?php echo $serror['order_id']; ?></td>
                <td>分拣人:<?php echo $serror['inventory_name'] ;?></td>
                <?php  }?>
               <?php  } else { ?>
                <td class="text-center" colspan="8"><?php echo $text_no_results; ?>
               <?php } ?>
               </tbody>
           </table>
            <hr>
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-customer" class="form-horizontal">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <td>订单号</td>
                            <td>分拣错误信息</td>
                            <td>备注</td>
                            <td>添加时间</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if($sorterrors){ ?>
                        <?php foreach ($sorterrors as $sorterror) { ?>
                        <tr>
                            <td class="text-left"><?php echo $sorterror['order_id']; ?></td>
                            <td class="text-left"><?php echo $sorterror['name']; ?></td>
                            <td class="text-left"><?php echo $sorterror['comment']; ?></td>
                            <td class="text-left"><?php echo $sorterror['date_added']; ?></td>

                        </tr>
                        <?php } ?>
                        <?php } else { ?>
                        <tr>
                            <td class="text-center" colspan="8"><?php echo $text_no_result; ?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="tab-pane" id="tab-sorterror">
                <div id="sorterror"></div>
                <br />
                <fieldset>
                    <legend><?php echo $text_sorterrortype; ?></legend>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_sorterrortype; ?></label>
                            <div class="col-sm-10">
                                <?php if($sorterror_type){ ?>
                                <?php foreach($sorterror_type as $sorttype){ ?>
                                <div>
                                <input type="radio" name="sorterrorbox[]" value='<?php echo $sorttype["sorterror_id"];?>' id="input-notify"  /> <?php echo $sorttype["sorterror_id"]; ?> .<?php echo  $sorttype['name']; ?>
                                </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-comment"><?php echo $entry_comment; ?></label>
                            <div class="col-sm-10">
                                <textarea name="comment" rows="8" id="comments" class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                    <div class="text-right">
                        <button id="button_sorterror" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" ><i class="fa fa-plus-circle"></i> <?php echo $button_sorterror_add; ?></button>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#button-filter').on('click', function() {
        url = 'index.php?route=user/sort_error&token=<?php echo $token; ?>';
        var filter_order_id = $('input[name=\'filter_order_id\']').val();

        if (filter_order_id) {
            url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
        }
        var filter_inventory_name = $('input[name=\'filter_inventory_name\']').val();

        if (filter_inventory_name) {
            url += '&filter_inventory_name=' + encodeURIComponent(filter_inventory_name);
        }

        location = url;
    });


</script>

<script>
    $('#button_sorterror').on('click', function(filter_order_id) {
        var sorterrorbox = $("input[name='sorterrorbox[]']:checked").val([]);
        var filter_order_id = $('input[name=\'filter_order_id\']').val();
        var  check_value = [];

        for(var i=0;i<sorterrorbox.length;i++){
            check_value.push(sorterrorbox[i].value);
        }

        $.ajax({
            url: 'index.php?route=user/sort_error/addsorterrors&token=<?php echo $token;?>',
            type: 'post',
            dataType: 'json',
            data:{
                check_value:check_value,
                comments:$('#comments').val(),
                filter_order_id:filter_order_id,
            },
            success:function(data){
                if(data == true){
                    alert('添加成功');
                    location.reload();
                }
            },
            error:function(){
                alert('确认反馈选项已选择或已经筛选了订单');
            }
        });
        });

</script>
