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
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="alert alert-info">指定仓库，配送日期，配送时间段，点击查询获取已分配的订单列表，打印配送单请先指定线路和司机，点击“查找”更新后，再点击“打印”，将弹出新窗口。</div>
            <div class="form-group row" style="">
                <div class="col-sm-10">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>选择仓库</label>
                            <select name="station_id" id="input-station_id" class="form-control" >
                                <option value="">选择仓库</option>
                                <?php foreach($stations as $station){　?>
                                <option value="<?php echo $station['station_id'] ?>" <?php if($station['station_id'] == $filterDefault['station_id']){ echo 'selected="selected"';} ?> ><?php echo $station['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>分配状态</label>
                            <select name="assign_status_id" id="assign_status_id" class="form-control" >
                                <option value="0"> 状态选择 </option>
                                <option value="1">已分配</option>
                                <option value="2">未分配</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>配送日期</label>
                            <div class="input-group date">
                                <input type="text"  data-date-format="YYYY-MM-DD" placeholder="配送日期" value="<?php echo $filterDefault['deliver_date']?>" id="search_date" class="form-control"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                        </span>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>配送时段</label>
                            <select name="deliver_slot_id" id="slot" class="form-control">
                                <option value="">配送时段</option>
                                <?php foreach($slots as $slot){　?>
                                <option value="<?php echo $slot['deliver_slot_id'] ?>" <?php if($slot['deliver_slot_id'] == $filterDefault['deliver_slot_id']){ echo 'selected="selected"';} ?> ><?php echo $slot['start_time'].'~'.$slot['end_time']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-10">
                    <!--   <form class="form-inline row" role="form" id="form"> -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label  for="order">订单编号</label>
                            <input type="text" name="order_id" class="form-control" id="order" placeholder="订单编号" style="width:195px;">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label  for="line">线路</label>
                            <select class="form-control" name="line_id" id="line"  style="width:195px;">
                                <option value="">-</option>
                                <?php foreach ($lines as $val) { ?>
                                <?php if ($val['line_id'] == $line_id) { ?>
                                <option value="<?php echo $val['logistic_line_id']; ?>" selected="selected"><?php echo $val['logistic_line_title']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $val['logistic_line_id']; ?>" ><?php echo $val['logistic_line_title']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <label class="control-label">司机</label>
                        <select name="filter_logistic_list" id="input_logistic_list" class="form-control">
                            <option value=''>-</option>
                            <?php foreach ($logistic_list as $val) { ?>
                            <?php if ($val['logistic_driver_id'] == $filter_logistic_list) { ?>
                            <option value="<?php echo $val['logistic_driver_id']; ?>" selected="selected"><?php echo $val['logistic_driver_title']; ?></option>
                            <?php } else { ?>
                            <option value="<?php echo $val['logistic_driver_id']; ?>" ><?php echo $val['logistic_driver_title']; ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-sm-3" id="div_logistic_allot_id" hidden="hidden" >
                        <div class="form-group">
                            <label  for="div_logistic_allot_id">配送单号</label>
                            <select class="form-control" name="select_logistic_allot_id" id="select_logistic_allot_id"  style="width:195px;" >

                            </select>
                        </div>
                    </div>

                    <!--  </form>  -->
                    <div style="float: right; margin-right: 15px; margin-top: 23px">
                        <label> </label>
                        <button type="button"  class="btn btn-primary"  onclick="javascript:getLogisticAllotId();javascript:getAllotInfo();" >查找</button>
                        <button  onclick="javascript:logistic_print();" title="订单配送表" class="btn btn-info"><i class="fa fa-truck"></i></button>
                    </div>
                </div>
            </div>
            <hr />
            <div class="row" id="print">
                <div class="col-sm-12" id="printHold">

                    <table id="untable" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <td style="width: 70px">订单编号</td>
                            <td style="width: 90px">分拣位</td>
                            <td>地址/收货人</td>
                            <td style="width: 90px">件数</td>
                            <td style="width: 80px">应收金额</td>
                            <td style="width: 85px">市场人员</td>
                        </tr>
                        <tbody id="untable_info">


                        </tbody>

                    </table>

                    <table id="table" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <td style="width: 70px">订单编号</td>
                            <td style="width: 90px">分拣位</td>
                            <td>地址/收货人</td>
                            <td style="width: 90px">件数</td>
                            <td style="width: 80px">应收金额</td>
                            <td style="width: 85px">市场人员</td>
                            <td class="noprint" style="width: 85px">线路</td>
                            <td class="noprint" style="width: 85px">司机</td>
                            <td class="noprint" style="width: 85px">车辆</td>
                            <td class="noprint" style="display: none">配送员/手机号</td>
                            <td class="noprint" style="display: none">操作</td>
                        </tr>
                        </thead>
                        <tbody id="table_info">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="//cdn.bootcss.com/vue/1.0.28/vue.js"></script>
<script type="text/javascript">
    $('.date').datetimepicker({
        pickTime: false
    });
    $('input[name^=\'selected\']').on('change', function() {
        $('#button-shipping').prop('disabled', false);
        var selected = $('input[name^=\'selected\']:checked');


    });
</script>

<script>
    function getAllotInfo() {
        var date = $('#search_date').val();
        var station_id = $('#input-station_id').val();
        var deliver_slot_id = $('#slot').val();
        var line_id = $('#line').val();
        var driver_id = $('#input_logistic_list').val();
        var assign_status_id = $('#assign_status_id').val();
        var assign_id =  $("#assign_status_id").val();
        var warehouse_id_global = $("#warehouse_id_global").val();
        if(warehouse_id_global == 0){
            return false;
        }
        if(assign_id == 1 ){
            $("#untable").hide();
            $("#table").show();
        }
        if(assign_id == 2){
            $("#table").hide();
            $("#untable").show();
        }
        if(assign_id ==0){
            alert('请选择分配状态');
            return false;
        }


        $.ajax({
            type: 'GET',
            dataType: 'json',
            url:'index.php?route=logistic/logistic_allot2/getAllotInfo&token=<?= $_SESSION['token']?>&assign_status_id='+ assign_status_id,
            data:{
                order_id:$('#order').val(),
                line_id:line_id,
                driver_id:driver_id,
                date:date,
                station_id:station_id,
                deliver_slot_id:deliver_slot_id,
                assign_status_id:assign_status_id,
                warehouse_id_global :warehouse_id_global,
            },
            success:function(data) {
                if (assign_id == 1) {
                    var html = '';
                    $.each(data, function (i, v) {
                        html += "<tr>";
                        html += "<td>" + v.order_id + "<br>" + v.station_name + "<br>" + v.status_name + "</td>";
                        if (v.invComment) {
                            html += "<td>" + v.invComment + "</td>";
                        } else {
                            html += "<td>无货位号</td>";
                        }
                        html += "<td>" + v.shipping_firstname + "-" + v.shipping_phone + "-" + v.shipping_city + "-" + v.shipping_address_1 + "</td>";
                        html += "<td>" + v.num + "</td>";
                        html += "<td>" + v.total + "</td>";
                        html += "<td>" + v.bd_name + "-" + v.phone + "</td>";
                        html += "<td>" + v.logistic_line_title + "</td>";
                        html += "<td>" + v.logistic_driver_title + "</td>";
                        html += "<td>" + v.logistic_van_title + "</td>";

                        html += "</tr>";
                    });
                    $('#table_info').html(html);
                  //  getReturnInfo();
                }
                if (assign_id == 2) {
                    var html = '';
                    $.each(data, function (i, v) {
                        html += "<tr>";
                        html += "<td>" + v.order_id + "<br>" + v.station_name + "<br>" + v.status_name + "</td>";
                        if (v.invComment) {
                            html += "<td>" + v.invComment + "</td>";

                        } else {
                            html += "<td>无货位号</td>";
                        }

                        html += "<td>" + v.shipping_firstname + "-" + v.shipping_phone + "-" + v.shipping_city + "-" + v.shipping_address_1 + "</td>";
                        html += "<td>" + v.num + "</td>";
                        html += "<td>" + v.total + "</td>";
                        html += "<td>" + v.bd_name + "-" + v.phone + "</td>";
                        html += "</tr>";
                    });
                    $('#untable_info').html(html);
                }
            }

        });

    }



</script>

<script>
    function  getLogisticAllotId(){
        var line_id = $('#line').val();
        var driver_id = $('#input_logistic_list').val();
        var date = $('#search_date').val();
        var station_id = $('#input-station_id').val();
        var warehouse_id_global = $("#warehouse_id_global").val();

        if(warehouse_id_global == 0){
            alert('查询打印前请先选择仓库');
            return false;
        }

        if( line_id !='' &&  driver_id !=''){
            $.ajax({
                type: 'POST',
                dataType:'json',
                url: 'index.php?route=logistic/logistic_allot2/getLogisticAllotId&token=<?php echo $_SESSION["token"];?>',
                data:{
                    date:date,
                    line_id:line_id,
                    driver_id:driver_id,
                    station_id:station_id,
                },
                success:function(response){
                    $("#div_logistic_allot_id").show();
                    var  html = '';
                    console.log(response);
                    html += '<option value="">请选择配送单号</option>';
                    $.each(response, function (i, v) {
                        //  productArray.push(v.container_id);

                        html += '<option value="'+ v.logistic_allot_id + '">' + v.logistic_allot_id + '</option>';
                    });
                    $("#select_logistic_allot_id").html(html);
                },
            });
        }
    }



    function logistic_print(){
        var logistic_allot_id =  $("#select_logistic_allot_id").val();
        if(logistic_allot_id){
            window.open('index.php?route=logistic/logistic_allot2/logistic_print&token=<?php echo $_SESSION["token"] ;?>&logistic_allot_id='+logistic_allot_id);
        }else {
            alert('请选择需要打印的配送单号');
        }

    }

</script>
<?php echo $footer; ?>