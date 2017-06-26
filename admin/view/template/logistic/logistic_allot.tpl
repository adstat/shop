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
                    <select name="station_id" id="input-station_id" class="form-control">
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
                            <option v-for="l in line.line" v-bind:value="l.logistic_line_id">{{l.logistic_line_title}}</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label  for="driver">司机</label>
                        <select class="form-control" name="driver_id" id="driver"  style="width:195px;">
                            <option value="">-</option>
                            <option v-for="d in line.driver" v-bind:value="d.logistic_driver_id">{{d.logistic_driver_title}}</option>

                        </select>
                    </div>
                </div>


          <!--  </form>  -->
            <div style="float: right; margin-right: 15px; margin-top: 23px">
                <label> </label>
                <button type="button" v-on:click="search" class="btn btn-primary" >查找</button>
                <button type="button" v-on:click="print" class="btn btn-primary">打印</button>
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
                <tbody>
                <tr v-for="val in data" >
                    <td v-if="val.order_status_id =='3'" class="noprint"> {{val.order_id}}<br/><span  id={{val.order_status_id}}>{{val.status_name}}</span></td>

                    <td v-if="val.order_status_id =='3'" class="noprint">{{val.sortIndex}}</td>
                    <td v-if="val.order_status_id =='3'" class="noprint">
                        [{{ val.shipping_firstname }}]-[{{ val.shipping_phone }}]-[{{ val.shipping_city}}] {{ val.shipping_address_1 }}
                    </td>
                    <td v-if="val.order_status_id =='3'" class="noprint">{{ val.num }}</td>
                    <td v-if="val.order_status_id =='3'" class="noprint">{{ val.total }}</td>
                    <td v-if="val.order_status_id =='3'" class="noprint">{{val.bd_name}} <br /> {{val.phone}}</td>
                    <td v-if="val.order_status_id !='3'">{{val.order_id}}<br/><span  id={{val.order_status_id}}>{{val.status_name}}</span></td>
                    <td v-if="val.order_status_id !='3'" >{{val.sortIndex}}</td>
                    <td v-if="val.order_status_id !='3'" >
                        [{{ val.shipping_firstname }}]-[{{ val.shipping_phone }}]-[{{ val.shipping_city}}] {{ val.shipping_address_1 }}
                    </td>
                    <td v-if="val.order_status_id !='3'" >{{ val.num }}</td>
                    <td v-if="val.order_status_id !='3'" >{{ val.total }}</td>
                    <td v-if="val.order_status_id !='3'" >{{val.bd_name}} <br /> {{val.phone}}</td>

                </tr>

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
                <tbody>
                    <tr v-for="val in data">
                        <td v-if="val.order_status_id =='3'" class="noprint">{{val.order_id}}<br />{{val.station_name}}<br/>{{val.status_name}}</td>
                        <td v-if="val.order_status_id =='3'" class="noprint">{{val.invComment}}</td>
                        <td v-if="val.order_status_id =='3'" class="noprint">
                            [{{ val.shipping_firstname }}]-[{{ val.shipping_phone }}]-[{{ val.shipping_city}}] {{ val.shipping_address_1 }}
                        </td>
                        <td v-if="val.order_status_id =='3'" class="noprint"{{ val.num }}</td>
                        <td v-if="val.order_status_id =='3'" class="noprint">{{ val.total }}</td>
                        <td v-if="val.order_status_id =='3'" class="noprint">{{val.bd_name}} <br /> {{val.phone}}</td>


                        <td v-if="val.order_status_id !='3'">{{val.order_id}}<br />{{val.station_name}}<br/>{{val.status_name}}</td>
                        <td v-if="val.order_status_id !='3'">{{val.invComment}}</td>
                        <td v-if="val.order_status_id !='3'">
                            [{{ val.shipping_firstname }}]-[{{ val.shipping_phone }}]-[{{ val.shipping_city}}] {{ val.shipping_address_1 }}
                        </td>
                        <td v-if="val.order_status_id !='3'">{{ val.num }}</td>
                        <td v-if="val.order_status_id !='3'">{{ val.total }}</td>
                        <td v-if="val.order_status_id !='3'">{{val.bd_name}} <br /> {{val.phone}}</td>


                        <td class="noprint">{{val.logistic_line_title}}</td>
                        <td class="noprint">{{val.logistic_driver_title}} <br /> {{val.logistic_driver_phone}}</td>
                        <td class="noprint">{{val.logistic_van_title}}</td>
                        <td class="noprint" style="display: none">{{val.logistic_deliveryman_title}} -- {{val.logistic_deliveryman_phone}}</td>
                        <td class="noprint" style="display: none"><button class="del" v-bind:data-logistic_allot_order_id="val.logistic_allot_order_id" v-bind:data-logistic_allot_id="val.logistic_allot_id">取消</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    </div>
</div>
<script src="view/javascript/bootstrap/vue.js"></script>
<script type="text/javascript">
    $('.date').datetimepicker({
        pickTime: false
    });
</script>

<script>
    eval('var allot_info = <?= $allot_info?>');
    eval('var line = <?= $line?>');
    $(function(){
        vue = new Vue({
            el:'#content',
            data:{
                data:allot_info,
                line:line,
                deliver_date:'',
                line_id:0,
                driver_id:0,

            },
            methods:{
                search:function () {
                    var _self = this;
                    var date = $('#search_date').val();
                    var station_id = $('#input-station_id').val();
                    var deliver_slot_id = $('#slot').val();
                    var line_id = $('#line').val();
                    var driver_id = $('#driver').val();
                    var assign_status_id = $('#assign_status_id').val();
                    var assign_id =  $("#assign_status_id").val();
                    if(assign_id == 1 || assign_id ==0){
                        $("#untable").hide();
                        $("#table").show();
                    }
                    if(assign_id == 2){
                        $("#table").hide();
                        $("#untable").show();
                    }



                    $.ajax({
                        url:'index.php?route=logistic/logistic_allot/getAllotInfo&token=<?= $_SESSION['token']?>&assign_status_id='+ assign_status_id,
                        data:{
                            order_id:$('#order').val(),
                            line_id:line_id,
                            driver_id:driver_id,
                            date:date,
                            station_id:station_id,
                            deliver_slot_id:deliver_slot_id,
                            assign_status_id:assign_status_id,
                        },
                        success:function(data){
                              console.log((data));
                            try {
                                eval('_self.data ='+data);
                            } catch (e) {
                                //alert(e.name + ": " + e.message);
                                alert('查询错误，请检测检索条件，这是后台检索，请确保页面登录未实效。')
                            }

                            //eval('_self.data ='+data);

                            vue.deliver_date = date;
                            vue.line_id = line_id;
                            vue.driver_id = driver_id;

                            if(line_id > 0){
                                $('#line').children().css('background-color','#ffffff');
                                $('#line').find('option[value='+vue.line_id+']').css('background-color','#ffcc00');
                            }
                            if(driver_id > 0){
                                $('#driver').children().css('background-color','#ffffff');
                                $('#driver').find('option[value='+vue.driver_id+']').css('background-color','#ffcc00');
                            }
                        }
                    });
                },



                print:function(){
                    var assign_status_id = $('#assign_status_id').val();
                    if (assign_status_id  == 2){

                        w = window.open();
                        w.document.open();
                        w.document.write($('head').html());
                        w.document.write(
                                '<style>' +
                                'table thead td{ font-size:12px; font-weight:bold; }' +
                                'table tbody td{ font-size:12px; font-weight:normal; }' +
                                '.noprint{ display:none; }' +
                                '#printHold { width:980px; }' +
                                '</style>'
                        );
                        var date = vue.deliver_date;
                        w.document.write(
                                '<div style="margin: 8px 15px; font-size: 16px; width:950px; font-weight: bold">鲜世纪订单配送列表' +
                                '[日期]' + date + ', ' +

                                '<div style="float:right;font-size: 14px; font-weight: normal">当前时间: <span id="currentTime"><?php echo date("Y-m-d H:i:s", time()+8*3600);?></span></div>' +
                                '</div>'
                        );
                        var print = $("#print");
                        w.document.write(print.html());
                        w.document.close();


                    }



                    if(assign_status_id  == 1) {
                        if (vue.line_id == '' || vue.driver_id == '') {
                            alert("请先指定线路和司机,点击查找更新信息后再打印配送单。");
                            return false;
                        }
                        else {
                            var date = vue.deliver_date;
                            var line_title = $('#line').find('option[value=' + vue.line_id + ']').html();
                            var driver_title = $('#driver').find('option[value=' + vue.driver_id + ']').html();

                            w = window.open();
                            w.document.open();
                            w.document.write($('head').html());
                            w.document.write(
                                    '<style>' +
                                    'table thead td{ font-size:12px; font-weight:bold; }' +
                                    'table tbody td{ font-size:12px; font-weight:normal; }' +
                                    '.noprint{ display:none; }' +
                                    '#printHold { width:980px; }' +
                                    '</style>'
                            );
                            w.document.write(
                                    '<div style="margin: 8px 15px; font-size: 16px; width:950px; font-weight: bold">鲜世纪订单配送列表' +
                                    '[日期]' + date + ', ' +
                                    '[线路]' + line_title + ', ' +
                                    '[司机]' + driver_title +
                                    '<div style="float:right;font-size: 14px; font-weight: normal">当前时间: <span id="currentTime"><?php echo date("Y-m-d H:i:s", time()+8*3600);?></span></div>' +
                                    '</div>'
                            );
                            var print = $("#print");
                            w.document.write(print.html());
                            w.document.close();
                        }
                    }
                }
            }
        });
        $('#table').on('click','.del',function() {
            var that = $(this);
            var logistic_allot_order_id = that.data('logistic_allot_order_id')
            var logistic_allot_id = that.data('logistic_allot_id')
            $.ajax({
                url:'index.php?route=logistic/logistic_allot/del&token=<?= $_SESSION['token']?>&logistic_allot_order_id='+logistic_allot_order_id+'&logistic_allot_id='+logistic_allot_id,
                success:function(){
                    that.parent().parent().remove();
                }
            });
        });

    });
</script>



    <?php echo $footer; ?>