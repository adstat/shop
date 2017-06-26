<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
        </div>
        <div class="panel-body">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-banner" class="form-horizontal">
                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="title" value="<?php echo $title; ?>" placeholder="<?php echo $entry_name; ?>" id="input-title" class="form-control" />
                        <?php if ($error_name) { ?>
                        <div class="text-danger"><?php echo $error_name; ?></div>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                    <div class="col-sm-10">
                        <select name="status" id="input-status" class="form-control">
                            <?php if ($status) { ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-stations"><?php echo $entry_stations; ?></label>
                    <div class="col-sm-10">
                        <select name="station_id" id="input-station_id" class="form-control" onchange="changeStation()">
                            <option value="0"> 所有平台 </option>
                            <?php if ( !empty($station_list) ){ foreach( $station_list as $value ){ ?>
                                <option value="<?php echo $value['station_id']; ?>" <?php if(!empty($station_id) && $station_id == $value['station_id'] ){ echo "selected"; } ?> ><?php echo $value['name']; ?></option>
                            <?php }} ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-stations"><?php echo $entry_warehouses; ?></label>
                    <div class="col-sm-10" id="insert-warehouse-list">

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                    <div class="col-sm-3">
                        <div class="input-group date">
                            <input type="text" name="date_start" value="<?php echo $date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-date-start" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-date-end"><?php echo $entry_date_end; ?></label>
                    <div class="col-sm-3">
                        <div class="input-group date">
                            <input type="text" name="date_end" value="<?php echo $date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-date-end" class="form-control" />
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $('.date').datetimepicker({
                pickTime: true,
                format: 'YYYY-MM-DD HH:mm',
                pickDate: true,
                hourStep: 1,
                minuteStep: 15,
                inputMask: true,
            });

            changeStation();
        });

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
                    }else{
                        html += '<input type="checkbox" name="warehouse_ids[]" value="'+ item.warehouse_id +'" checked > ';
                    }

                    if (station_id == 0){ html += ' [ '+ item.name +' ]  '; }

                    html +=         ' <b>'+ item.title +'</b>';
                    html +=     '</label>';
                    html += '</div>';
                }
            });

            $('#insert-warehouse-list').html( html );
        }

    </script>
</div>
<?php echo $footer; ?>