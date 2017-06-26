<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-promotion" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <div class="alert alert-info">赠品界面开发中，需要联系技术后台绑定。</div>
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-promotion"
                      class="form-horizontal">
                    <!--<div class="form-group"
                         style="border: 1px #333 dashed; padding: 5px; margin: 8px 3px; background-color: #fff9df;">
                        <label class="col-sm-1 control-label" for="input-station_id">平台/仓库</label>
                        <div class="col-sm-3">
                            <select name="station_id" id="input-station_id" class="form-control">
                                <option value="0">－</option>
                                <?php foreach($stations as $station){　?>
                                <option value="<?php echo $station['station_id'] ?>"
                                <?php echo $station_id == $station['station_id']?'selected':'';?>
                                ><?php echo $station['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>-->
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                        <li><a href="#tab-gift" data-toggle="tab"><?php echo $tab_gift; ?></a></li>
                        <?php if ($promotion_id) { ?>
                        <li><a href="#tab-history" data-toggle="tab"><?php echo $tab_history; ?></a></li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content ">
                        <div class="tab-pane active" id="tab-general">
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="input-type"><span data-toggle="tooltip"
                                                                                             title="<?php echo $help_type; ?>"><?php echo $entry_type; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="type" id="input-type" class="form-control">
                                        <option value="gift"
                                        <?php echo $type=='gift'?'selected':''; ?> ><?php echo $text_gift; ?></option>
                                        <option value="discount"
                                        <?php echo $type=='discount'?'selected':''; ?>
                                        ><?php echo $text_discount; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label"
                                       for="input-name"><?php echo $entry_title; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="title" value="<?php echo $title; ?>"
                                           placeholder="<?php echo $entry_title; ?>" id="input-name"
                                           class="form-control"/>
                                    <?php if ($error_title) { ?>
                                    <div class="text-danger"><?php echo $error_title; ?></div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-stations">平台</label>
                                <div class="col-sm-10">
                                    <select name="station_id" id="input-station_id" class="form-control" onchange="changeStation()">
                                        <option value="0"> 全部 </option>
                                        <?php if ( !empty($station_list) ){ foreach( $station_list as $value ){ ?>
                                        <option value="<?php echo $value['station_id']; ?>" <?php if(!empty($station_id) && $station_id == $value['station_id'] ){ echo "selected"; } ?> ><?php echo $value['name']; ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-stations">仓库</label>
                                <div class="col-sm-10" id="insert-warehouse-list">

                                </div>
                            </div>

                            <div class="form-group required">
                                <label class="col-sm-2 control-label"
                                       for="input-status"><?php echo $entry_firstorder; ?></label>
                                <div class="col-sm-10">
                                    <select name="firstorder" id="input-status" class="form-control">
                                        <option value="1"
                                        <?php echo $firstorder==1?'selected':'';?>
                                        ><?php echo $text_enabled; ?></option>
                                        <option value="0"
                                        <?php echo $firstorder==0?'selected':'';?>
                                        ><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label"
                                       for="input-status"><?php echo $entry_overlap; ?></label>
                                <div class="col-sm-10">
                                    <select name="overlap" id="input-status" class="form-control">
                                        <option value="1"
                                        <?php echo $overlap==1?'selected':'';?> ><?php echo $text_enabled; ?></option>
                                        <option value="0"
                                        <?php echo $overlap==1?'selected':'';?> ><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label"
                                       for="input-name"><?php echo $entry_min_cart_total; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="min_cart_total" value="<?php echo $min_cart_total ?>"
                                           placeholder="<?php echo $entry_min_cart_total; ?>" id="input-name"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label"
                                       for="input-name"><?php echo $entry_max_cart_total; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="max_cart_total" value="<?php echo $max_cart_total ?>"
                                           placeholder="<?php echo $entry_max_cart_total; ?>" id="input-name"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label"
                                       for="input-status"><?php echo $entry_disount_fixed; ?></label>
                                <div class="col-sm-10">
                                    <select name="disount_fixed" id="input-status" class="form-control">
                                        <option value='fixed'
                                        <?php echo $disount_fixed=='fixed'?'selecteed':''; ?>
                                        ><?php echo $text_fixed; ?></option>
                                        <option value='rate'
                                        <?php echo $disount_fixed=='rate'?'selecteed':''; ?>
                                        ><?php echo $text_rate; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-discount"><?php echo $entry_discount; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="discount" value="<?php echo $discount; ?>"
                                           placeholder="<?php echo $entry_discount; ?>" id="input-discount"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-date-start"><?php echo $entry_date_start; ?></label>
                                <div class="col-sm-3">
                                    <div class="input-group date">
                                        <input type="text" name="date_start" value="<?php echo $date_start; ?>"
                                               placeholder="<?php echo $entry_date_start; ?>"
                                               data-date-format="YYYY-MM-DD" id="input-date-start"
                                               class="form-control"/>
                                        <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-date-end"><?php echo $entry_date_end; ?></label>
                                <div class="col-sm-3">
                                    <div class="input-group date">
                                        <input type="text" name="date_end" value="<?php echo $date_end; ?>"
                                               placeholder="<?php echo $entry_date_end; ?>"
                                               data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control"/>
                                        <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-status"><?php echo $entry_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="status" id="input-status" class="form-control">
                                        <option value="1"
                                        <?php echo $status? 'selected':'';?>
                                        selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"
                                        <?php echo $status? '':'selected';?> ><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"
                                       for="input-discount"><?php echo $entry_desc; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="desc" value="<?php echo $desc; ?>"
                                           placeholder="<?php echo $entry_desc; ?>" id="input-discount"
                                           class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-gift">
                            <div id="gift">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <td class="text-center">
                                        <?php echo $table_product_id; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $table_product_name; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $column_price; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $column_action; ?>
                                    </td>
                                    </thead>
                                    <tbody id="t-tbody">

                                    <?php if(!empty($gifts_info)){ foreach($gifts_info as $gift_info){ ?>
                                    <tr data-product_id="<?php echo  $gift_info['product_id']?>">
                                        <td><?php echo  $gift_info['product_id']?></td>
                                        <td><?php echo  $gift_info['name']?></td>
                                        <td><?php echo  $gift_info['price']?></td>
                                        <td class="text-center del" style="font-size: 2em"> ×</td>
                                    </tr>
                                    <?php }}?>
                                    </tbody>
                                </table>
                                <input type="hidden" name="gifts" id="gifts" value="<?php echo $gifts?>">
                                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal"
                                        data-target="#myModal">添加赠品商品
                                </button>
                            </div>
                        </div>
                        <?php if ($promotion_id) { ?>
                        <div class="tab-pane" id="tab-history">
                            <div id="history">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <td class="text-center">
                                        <?php echo $table_order_id; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $table_product_id; ?> : <?php echo $table_product_name; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $table_price; ?> :  <?php echo $table_special_price; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $table_quantity; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $table_customer_id; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $table_added_by; ?>
                                    </td>
                                    </thead>
                                    <tbody id="h-tbody">
                                    </tbody>
                                </table>
                                <div class="page">

                                </div>

                            </div>
                        </div>
                        <?php } ?>

                </form>
            </div>
        </div>
    </div>
    <!-- 模态框（Modal） -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">添加</h4>
                </div>
                <div class="col">
                    <label class="col-sm-2 control-label"
                           for="input-status"><?php echo $column_name; ?></label>
                    <div class="col-sm-8">
                        <input id="gift_name" type="text"
                               placeholder="<?php echo $column_name; ?>" class="form-control"/>
                    </div>
                    <button id="select_gift">搜索</button>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                    <td class="text-center">
                        <?php echo $table_product_id; ?>
                    </td>
                    <td class="text-center">
                        <?php echo $table_product_name; ?>
                    </td>
                    <td class="text-center">
                        <?php echo $column_price; ?>
                    </td>
                    </thead>
                    <tbody id="m-tbody">
                    </tbody>
                </table>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <script>
        var select_gifts = [ <?php echo$gifts ?>];
        Array.prototype.indexOf = function (val) {
            for (var i = 0; i < this.length; i++) {
                if (this[i] == val) return i;
            }
            return -1;
        };
        Array.prototype.remove = function (val) {
            var index = this.indexOf(val);
            if (index > -1) {
                this.splice(index, 1);
            }
        };

        function in_array(search, array) {
            for (var i in array) {
                if (array[i] == search) {
                    return true;
                }
            }
            return false;
        }
        $(function () {
            $('#select_gift').click(function () {
                var name = $('#gift_name').val();
                var station_id = $('#input-station_id').val();
                console.log(station_id);
                $.ajax({
                    type: "GET",
                    url: 'index.php?route=marketing/promotion/selectGift&token=<?php echo $token; ?>&name=' + name + '&station_id=' + station_id,
                    dataType: "json",
                    success: function (data) {
                        data = $(data);
                        var m_tbody = $('#m-tbody');
                        m_tbody.empty();
                        data.each(function () {
                            var tr = $('<tr>').data('product_id', this.product_id);
                            if(in_array(this.product_id,select_gifts)){
                                tr.css('background-color','#ccc');
                            }
                            tr.append($('<td>').html(this.product_id));
                            tr.append($('<td>').html(this.name));
                            tr.append($('<td>').html(this.price));
                            m_tbody.append(tr);
                        });
                    }
                });
            });
            $('#myModal').on('show.bs.modal', function () {
                $('#select_gift').click();
            })

            $('#m-tbody').on('click', 'tr', function () {
                var that = $(this);
                if (!in_array(that.data('product_id'), select_gifts)) {
                    select_gifts.push(that.data('product_id'));
                    $('#gifts').val(select_gifts.join(','));
                    that.css('background-color','#ccc');
                    $('#t-tbody').append(that.clone().data('product_id', that.data('product_id')).append($('<td>').addClass('text-center del').html('×').css('font-size','2em')));
                }else{
                    select_gifts.remove(that.data('product_id'));
                    $('#gifts').val(select_gifts.join(','));
                    that.css('background-color','#fff');
                    $('#t-tbody tr').each(function() {
                        if($(this).data('product_id') == that.data('product_id')){
                            this.remove();
                        }
                    });
                }
            });
            $('#t-tbody').on('click', '.del', function () {
                var that = $(this);
                if (in_array(that.parent().data('product_id'), select_gifts)) {
                    select_gifts.remove(that.parent().data('product_id'));
                    $('#gifts').val(select_gifts.join(','));
                    that.parent().detach();
                }
            });
            var page = 1;
            (function load_history() {
                $.ajax({
                    type: "GET",
                    url: 'index.php?route=marketing/promotion/history&token=<?php echo $token; ?>&promotion_id=<?php echo $promotion_id ?>&page=' + page,
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        $('.page').empty();
                        $('#h-tbody').empty();
                        $('.page').html(data.pagination + '<div class="text-right">'+data.results+'</div>');
                        $(data.histories_infos).each(function(){
                            $('<tr>').html('<td>'+this.order_id+'</td><td>'+this.product_id+' : '+this.product_name+'</td><td>'+this.price+' : '+this.special_price+'</td><td>'+ this.quantity+'</td><td>'+ this.customer_id+'</td><td>'+this.added_by +'</td>').appendTo($('#h-tbody'));
                        });

                        $('.page a').click(function(){
                            page = $(this).attr('href');
                            load_history();
                            return false;
                        })
                    }
                });
            })();


        });

        changeStation();

        function changeStation(){
            var station_id      = $('#input-station_id').val();

            var warehouse_list = JSON.parse('<?php echo json_encode($warehouse_list); ?>');
            var warehouse_ids  = JSON.parse('<?php echo json_encode($warehouse_ids); ?>');
            var html = "";
            $.each(warehouse_list, function(index, item){
                if(station_id == 0 || item.station_id == station_id){
                    html += '<div class="checkbox">';
                    html +=     '<label>';

                    if(warehouse_ids.length > 0){
                        var checked = "";
                        if($.inArray(item.warehouse_id, warehouse_ids) >= 0){
                            checked = "checked";
                        }
                        html += '<input type="checkbox" name="warehouse_ids[]" value="'+ item.warehouse_id +'" '+ checked +' > ';
                    }
                    else
                    {
                        html += '<input type="checkbox" name="warehouse_ids[]" value="'+ item.warehouse_id +'" checked > ';
                    }

                    if (station_id == 0){ html += ' [ '+ item.name +' ]  '; }

                    html +=         ' <b>'+ item.title +'</b>';
                    html +=     '</label>';
                    html += '</div>';
                }
                if(station_id == 0){
                    html = "";
                }
            });

            $('#insert-warehouse-list').html( html );
        }
    </script>
    <script type="text/javascript">
        $('.date').datetimepicker({
            pickTime: false
        });
    </script>
</div>
<?php echo $footer; ?>