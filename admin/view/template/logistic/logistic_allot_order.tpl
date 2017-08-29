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
            <div class="alert alert-info">指定仓库，配送日期，配送时间段，点击查询获取订单列表，可以实时自定义索引（限数字），双击选择订单，双击取消。</div>
            <div class="form-group row">
                <div class="col-sm-3">
                    <div class="form-group">


                        <div class="form-group">
                            <label>商品ID</label>
                            <input type="text" name="products" id="product_id"  value="" placeholder="商品ID" class="form-control"  />

                        </div>



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
                        <label>订单状态</label>
                        <select name="order_status_id" id="input-order-status-id" class="form-control">
                            <option value="0">全部</option>
                            <?php foreach($order_status as $order_statu){　;?>
                            <option value="<?php echo $order_statu['order_status_id'] ?>" <?php if($order_statu['order_status_id'] == $filterDefault['order_status_id']){ echo 'selected="selected"';} ?> ><?php echo $order_statu['name'] ;?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>配送日期</label>
                        <div class="input-group date">
                            <input type="text" data-date-format="YYYY-MM-DD" placeholder="配送日期" value="<?php echo $filterDefault['deliver_date']?>" id="search_date" class="form-control"/>
                    <span class="input-group-btn" id="datepicker">
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
            <hr />
            <style>
                .logisticIndex{
                    font-size: 18px;
                    font-weight: bold;
                    width: 36px;
                }

                #d-tbody .logisticIndex{
                    border: none;
                    background-color: #ccc;
                }

                .logistic_index{
                    font-size: 14px;
                    font-weight: bold;
                }
            </style>
            <div class="row" id="table">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-3 form-group">
                            <select class="form-control" id="search_classify">
                                <option value="0">全部状态</option>
                                <option value="1">未分配</option>
                                <option value="2">已分配</option>
                                <option value="3">重新分配</option>
                            </select>
                        </div>
                        <div style="float: left; margin-right: 10px;">
                            <select class="form-control" id="logistic_index">
                                <option value="0">全部索引</option>
                            </select>
                        </div>
                        <div style="float: left; margin-right: 10px;">
                            <select class="form-control" id="customer_area">
                                <option value="0">全部区域</option>
                            </select>
                        </div>
                        <div style="float: left; margin-right: 10px;">
                             <button type="button" class="btn btn-primary "  v-on:click="search()" onclick="javascript:getsumnums();"><i class="fa fa-search"  ></i> 查询</button>
                        </div>

                        <div style="float: right; margin-right: 15px;">
                            <button type="button" class="btn btn-primary"  v-on:select="search()" onclick="javascript:updateLogisticIndex(1);">更新列表索引</button>
                        </div>
                    </div>
                    <div id="vue-model">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <td style="width:70px">订单</td>
                                <td>地址</td>
                                <td style="width: 100px">件数</td>
                                <td style="width: 42px">索引</td>
                            </tr>
                            </thead>
                            <tbody id="m-tbody">
                            <tr class="order_list" v-for="val in list" v-bind:data-order_id="val.order_id" v-on:dblclick="add($event)">
                                <td v-if="val.classify_type==1" v-bind:data-classify_type="1">
                                    <span style="color: #ff0000">已分配 {{val.urgent}}</span><br>{{ val.order_id }}
                                    <span style="color:#64A600">({{val.name }})</span>
                                </td>
                                <td v-if="val.classify_type==2" v-bind:data-classify_type="2">
                                    <span style="color: #ff0000">重新分配 {{val.urgent}}</span><br>{{ val.order_id }}
                                    <span style="color:#64A600">({{val.name }})</span>
                                </td>
                                <td v-if="val.classify_type==3" v-bind:data-classify_type="3">
                                    <span style="color: #f00000">未分配 {{val.urgent}} </span>  {{ val.order_id }}
                                    <span style="color: #64A600">({{val.name }})</span>
                                    <input type="hidden" class="list_order_id" v-bind:value="val.order_id" />
                                </td>
                                <td>
                                    [{{ val.shipping_firstname }}]-[{{ val.shipping_phone }}]-[{{ val.shipping_city}}] {{ val.shipping_address_1 }}
                                    <br />
                                    {{val.comment}}
                                </td>
                                <td>
                                    {{ val.num }}
                                </td>
                                <td>
                                    <input type="text" v-bind:value="val.logistic_index" v-bind:order_id="val.order_id" class="logisticIndex" onchange="javascript:addLogisticIndexChange($(this));" />
                                    <input type="hidden" v-bind:value="val.logistic_index" class="oriLogisticIndex" />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row" style="margin-bottom: 15px;">
                        <form class="form-inline" role="form" id="vue_form" >
                            <div style="float: left; margin-left: 15px; margin-right: 10px;">
                                <div class="form-group">
                                    <select class="form-control" name="logistic_line_id" v-model="l_selected" id="InputLine">
                                        <option value="">*线路名</option>
                                        <option v-for="l in line_data.line" v-bind:value="l.logistic_line_id">
                                            {{l.logistic_line_title}} [{{l.count}} 单]
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div style="float: left; margin-right: 10px;">
                                <div class="form-group">
                                    <select class="form-control" name="logistic_driver_id" v-model="d_selected" id="InputDriver">
                                        <option value="">*指定司机</option>
                                        <option v-for="d in line_data.driver" v-bind:value="d.logistic_driver_id">
                                            {{d.logistic_driver_title}} [{{d.count}} 单] <!-- - {{d.logistic_driver_phone}}  -->
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div style="float: left; margin-right: 10px;">
                                <div class="form-group">
                                    <select class="form-control" name="logistic_van_id"  v-model="v_selected" id="InputVan">
                                        <option value="">*车辆</option>
                                        <option v-for="v in line_data.van" v-bind:value="v.logistic_van_id">
                                            {{v.model}} [{{v.logistic_van_title}}] <!-- {{v.capacity}}件 {{v.payload}}KG -->
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div style="float: left; margin-right: 10px;">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary " v-on:click="submit()" id="submitAllot">分派</button>
                                </div>
                            </div>
                            <div class="form-group col-sm-3" style="display:none">
                                <label  for="InputDeliveryman">配送员</label>
                                <select class="form-control" name="logistic_deliveryman_id" id="InputDeliveryman">
                                    <option value="">-</option>
                                    <option v-for="dm in line_data.deliveryman" v-bind:value="dm.logistic_deliveryman_id">
                                        {{dm.logistic_deliveryman_title}} - {{dm.logistic_deliveryman_phone}}
                                    </option>
                                </select>
                            </div>
                            <input type="hidden" name="order_ids" id="orders">
                        </form>
                    </div>
 <div>总件数：<span id="sumnum"></span></div>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <td style="width:70px">订单</td>
                            <td>地址</td>
                            <td style="width: 100px">件数</td>
                            <td style="width: 42px">标记</td>
                        </tr>
                        </thead>
                        <tbody id="d-tbody">
                        <tr v-for="oh in allot_order_history">
                            <td>
                                {{ oh.order_id }}
                            </td>
                            <td>
                                [{{ oh.shipping_firstname }}]-[{{ oh.shipping_phone }}]-[{{ oh.shipping_city}}] {{ oh.shipping_address_1 }}
                                <br />
                                {{oh.comment}}
                            </td>
                            <td>
                                {{ oh.num }}
                            </td>
                            <td><button v-on:click="del($event)" v-bind:data-logistic_allot_order_id="oh.logistic_allot_order_id" v-bind:data-logistic_allot_id="oh.logistic_allot_id" v-bind:data-order_id="oh.order_id">取消</button></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">

        $('input[name=\'products\']').autocomplete({
            'source': function(request, response) {
                var warehouse_id = $("#warehouse_id_global").val();
                $.ajax({
                    type: 'POST',
                    url: 'index.php?route=purchase/warehouse_allocation_note/autocomplete&token=<?php echo $token; ?>&products=' +  encodeURIComponent(request),
                    dataType: 'json',
                    data:{
                        warehouse_id :warehouse_id,
                    },
                    success: function(json) {
                        console.log(json);

                  response($.map(json, function(item) {
                        console.log( item['fix']);
                        return {
                           label: 1,
                           //   label: item['name'],
                           value: 2
                       }
                    }));
                    }
                });
            },
            'select': function(item) {
                $('input[name=\'products\']').val(item['value']);
            }
        });


</script>


<script src="view/javascript/bootstrap/vue.js"></script>
<script type="text/javascript">
    $('.date').datetimepicker({
        pickTime: false
    });

    var filerLockMsg = "分派任务开始，不可修改查询条件，需要更改请刷新重新进入页面。";
    $('#input-station_id').on('click',function(){
        if($("#input-station_id").attr('readonly') == 'readonly'){
            alert(filerLockMsg);
        }
    });
    $('#search_date').on('click',function(){
        if($("#search_date").attr('readonly') == 'readonly'){
            alert(filerLockMsg);
        }
    });
    $('#slot').on('click',function(){
        if($("#slot").attr('readonly') == 'readonly'){
            alert(filerLockMsg);
        }
    });

    var logisticIndexUpdate = new Object();
    function addLogisticIndexChange(obj){
        var order_id = parseInt(obj.attr('order_id'));
        var logistic_index = parseInt(obj.val());
        obj.css('color','#51C332');

        logisticIndexUpdate[order_id] = logistic_index;
    }
</script>
<script>
//    Array.prototype.indexOf = function (val) {
//        for (var i = 0; i < this.length; i++) {
//            if (this[i] == val) return i;
//        }
//        return -1;
//    };
//
//    Array.prototype.remove = function (val) {
//        var index = this.indexOf(val);
//        if (index > -1) {
//            this.splice(index, 1);
//        }
//    };

    function in_array(search, array) {
        for (var i in array) {
            if (array[i] == search) {
                return true;
            }
        }
        return false;
    }

    eval('var line_data = <?= $line_data?>');
    var vue;
    $(function(){
        vue = new Vue({
            el:'#table',
            data:{
                list:'',
                line_data:line_data,
                allot_order_history:'',
                l_selected:0,
                d_selected:0,
                v_selected:0,
                default_logistic_driver_id:0,
                default_logistic_van_id:0,
                classify:0,
                order_ids:[],

                //每次查询保存好查询条件，已备写入
                station_id:0,
                deliver_date:0,
                deliver_slot_id:0,
                order_status_id:0,
                logistic_line_id:0,
                logistic_van_id:0,
                logistic_driver_id:0,
                logistic_deliveryman_id:0
            },
            methods:{
                search:function(){

                    var _self = this;
                    var date = $('#search_date').val();
                    var classify = $('#search_classify').val();
                    var station_id = $('#input-station_id').val();
                    var deliver_slot_id = $('#slot').val();
                    var logistic_index = $('#logistic_index').val();
                    var order_status_id = $('#input-order-status-id').val();
                    var warehouse_id_global = $("#warehouse_id_global").val();
                    var area_id = $("#customer_area").val();
                    if(warehouse_id_global == 0){
                        alert( '分车之前请先选好仓库');
                        return false;
                    }

                    this.classify = classify;
                    $.ajax({
                        type: 'GET',
                        dataType:'json',
                        url: 'index.php?route=logistic/logistic_allot_order/getAllotOrder&token=<?php echo $_SESSION["token"];?>',
                        data:{
                            date:date,
                            station_id:station_id,
                            classify:classify,
                            deliver_slot_id:deliver_slot_id,
                            logistic_index:logistic_index,
                            order_status_id :order_status_id,
                            warehouse_id_global:warehouse_id_global,
                            area_id,area_id,
                        },
                        success:function(data) {
                            console.log("SEARCH: "+data);
                           // getCustomerArea();
                            try {
                                _self.list = data;
                            } catch (e) {
                                //alert(e.name + ": " + e.message);
                                alert('查询错误，请检测检索条件，这是后台检索，请确保页面登录未实效。')
                                return false;
                            }

                            if(_self.v_selected!=0){
                                _self.get_allot_order_history();
                            }
                            _self.get_line();

                            if(logistic_index == 0){
                                updateLogisticIndex();

                            }
                        }
                    });
                },
                get_allot_order_history:function(){
                    var _self = this;
                    var date = $('#search_date').val();
                    var station_id = $('#input-station_id').val();
                    var deliver_slot_id = $('#slot').val();
                    var warehouse_id_global = $("#warehouse_id_global").val();
                    $.ajax({
                        url:'index.php?route=logistic/logistic_allot/getAllotInfo&token=<?= $_SESSION['token']?>',
                        data:{
                            line_id:this.l_selected,
                            driver_id:this.d_selected,
                            van_id:this.v_selected,
                            date:date,
                            station_id:station_id,
                            deliver_slot_id:deliver_slot_id,
                            warehouse_id_global : warehouse_id_global,
                        },
                        success:function(data){
                            console.log(data);
                            eval('_self.allot_order_history ='+data) ;
                        }
                    });
                },
                add:function(e){
                    var that = $(e.target).parents('tr');

                    if(that.children().first().data('classify_type')==1){
                        alert('此订单已分配');
                        return false;
                    }

                    //开始分车，锁定主要检索条件station_id，search_date，slot
                    $('#input-station_id').attr("readonly","readonly");
                    $('#search_date').attr("readonly","readonly");
                    $('#datepicker').hide();
                    $('#slot').attr("readonly","readonly");

                    //var order_id = that.data('order_id');
                    var order_id = parseInt($(e.target).parent().attr('data-order_id'));
                    if (!in_array(order_id, this.order_ids)) {
                        this.order_ids.push(order_id);
                        $('#orders').val(this.order_ids.join(','));
                        that.css('background-color','#ccc');
                        console.log("Selected Orders:"+vue.order_ids);
//                      $('#d-tbody').append(that.clone().data('order_id', that.data('order_id')).append($('<td>').addClass('text-center del').html('×').css('font-size','2em')));
                        $('#d-tbody').append(
                                that.clone().data('order_id', that.data('order_id'))
                                        .addClass('new_add')
                                        .bind("dblclick",function(){
//                                            vue.order_ids.remove(order_id);
                                            var index = $.inArray(order_id, vue.order_ids);
                                            vue.order_ids.splice(index, 1);
                                            var xx_order_ids = vue.order_ids;
                                            $.each(xx_order_ids, function(index, item){
                                                item
                                            });

                                            console.log("Selected Orders:"+vue.order_ids);
                                            $('#orders').val(vue.order_ids.join(','));
                                            that.css('background-color','#fff');
                                            this.remove();
                                        })
                        );
                    }else{
                        //this.order_ids.remove(order_id);
                        var index = $.inArray(order_id, this.order_ids);
                        this.order_ids.splice(index, 1);
                        $('#orders').val(this.order_ids.join(','));
                        that.css('background-color','#fff');
                        $('#d-tbody tr').each(function() {
                            if($(this).data('order_id') == that.data('order_id')){
                                this.remove();
                            }
                        });
                    }
                },
                del:function (e) {
                    var _self = this;
                    var logistic_allot_order_id = e.target.getAttribute('data-logistic_allot_order_id');
                    var logistic_allot_id = e.target.getAttribute('data-logistic_allot_id');
                    var order_id = e.target.getAttribute('data-order_id');

                    $.ajax({
                        url:'index.php?route=logistic/logistic_allot/del&token=<?= $_SESSION['token']?>&logistic_allot_order_id='+logistic_allot_order_id+'&logistic_allot_id='+logistic_allot_id+'&order_id='+order_id,
                        success:function(){
                            _self.get_allot_order_history();
                            _self.search();
                        }
                    });
                },
                submit:function(){
                    var _self = this;
                    var formData = new FormData($( "#vue_form" ).get(0));

                    var line = $("#InputLine").val();
                    var driver = $("#InputDriver").val();
                    var van = $("#InputVan").val();

                    var station_id = $('#input-station_id').val();
                    var date = $('#search_date').val();
                    var deliver_slot_id = $('#slot').val();
                    var warehouse_id_global = $("#warehouse_id_global").val();

                    if(line && driver && van){
                        $.ajax({
                            type: 'POST',
                            async: false,
                            cache: false,
                            url: 'index.php?route=logistic/logistic_allot_order/addAllotOrder&token=<?php echo $_SESSION["token"];?>',
                            //data:formData,
                            data:{
                                station_id:station_id,
                                deliver_date:date,
                                deliver_slot_id:deliver_slot_id,
                                logistic_line_id:this.l_selected,
                                logistic_driver_id:this.d_selected,
                                logistic_van_id:this.v_selected,
                                order_ids:vue.order_ids,
                                warehouse_id_global : warehouse_id_global,
                            },
                            success:function(data){
                                console.log("Allot result:"+data);
                                if(data == 'true'){
                                    vue.order_ids = []; //清空已选择的订单
                                    //                                window.location.href="index.php?route=logistic/logistic_allot_order&token=<?php echo $_SESSION["token"];?>"
                                    $('#d-tbody .new_add').remove();
                                    $('#m-tbody tr').css('background-color','#fff');
                                    _self.get_allot_order_history();
                                    _self.search();
                                }else{
                                    alert('添加失败，请检测查询条件及是否指派了“线路/司机/车辆”。');
                                }
                            },
                            error:function(data){
                                vue.order_ids = [];
                                $('#d-tbody .new_add').remove();
                                $('#m-tbody tr').css('background-color','#fff');
                                _self.get_allot_order_history();
                                _self.search();
                            }
                        });
                    }
                    else{
                        alert('请先选择线路司机信息');
                    }
                },
                get_line:function(){
                    var _self = this;
                    var date = $('#search_date').val();
                    var warehouse_id_global = $("#warehouse_id_global").val();
                    $.ajax({
                        url: 'index.php?route=logistic/logistic_allot_order/getLine&token=<?php echo $_SESSION["token"];?>',
                        data:{
                            date:date,
                            warehouse_id : warehouse_id_global,
                        },
                        success:function(data){
                            eval('_self.line_data ='+data) ;
                        }
                    });

                }
            },
            watch:{
                "l_selected": function(newValue,oldValue) {
                    for (var i = 0; i < this.line_data.line.length; i++) {
                        if (this.line_data.line[i].logistic_line_id == newValue) {
                            this.default_logistic_driver_id = this.line_data.line[i].default_logistic_driver_id;
                            this.d_selected = this.line_data.line[i].default_logistic_driver_id;
                        }
                    }
                },
                "d_selected": function(newValue,oldValue) {
                    for (var i = 0; i < this.line_data.driver.length; i++) {
                        if (this.line_data.driver[i].logistic_driver_id == newValue) {
                            this.default_logistic_van_id = this.line_data.driver[i].default_logistic_van_id;
                            this.v_selected = this.line_data.driver[i].default_logistic_van_id;

                        }
                    }
                },
                "v_selected":function (newValue,oldValue) {
                    if(newValue != undefined){
                        this.get_allot_order_history();
                    }
                },
            },
        });
    });

    function updateLogisticIndex(opt){
        //若参数未传，默认不更新，传入orderLogisticIndex为空
        var orderLogisticIndex = logisticIndexUpdate;
        if(opt == undefined){
            orderLogisticIndex = new Object();
        }
        console.log(orderLogisticIndex);

        var warehouse_id_global = $("#warehouse_id_global").val();

        $('#logistic_index').css('background-color','#ffcc00');
        $.ajax({
            type: 'post',
            async : false,
            cache: false,
            url: 'index.php?route=logistic/logistic_allot_order/updateLogisticIndex&token=<?php echo $_SESSION["token"];?>',
            data:{
                orderLogisticIndex: orderLogisticIndex,
                date: $('#search_date').val(),
                station_id: $('#input-station_id').val(),
                deliver_slot_id: $('#slot').val(),
                warehouse_id_global : warehouse_id_global ,
            },
            success:function(data){
                console.log(data);
                var data = eval(data);

                if(data.error !== undefined){
                    alert(data.error);
                    return flase;
                }

                var html = '<option value="0">全部索引</option>';
                $.each(data, function(n, v){
                    html += '<option class="logistic_index" value="'+ v.logistic_index+'">'+ v.logistic_index + ' ['+ v.orders +']单' +'</option>';
                });

                $('#logistic_index').html(html);

                //恢复已修改的索引值颜色
                $('.logisticIndex').css('color','#000000');
            },
            complete:function(data){
                $('#logistic_index').css('background-color','#ffffff');
                logisticIndexUpdate = new Object();
            }
        });
    }

</script>
    <script>
        function getsumnums(){
            var logistic_driver_id = $('#InputDriver').val();
            var deliver_date = $('#search_date').val();
            var line_id = $('#InputLine').val();
            var warehouse_id_global = $("#warehouse_id_global").val();
            if(warehouse_id_global == 0){
                return false;
            }

            $.ajax({
                type: 'post',
                async : false,
                cache: false,
                dataType: 'json',
                url: 'index.php?route=logistic/logistic_allot_order/getsumnums&token=<?php echo $_SESSION["token"];?>',
                data:{
                    logistic_driver_id: logistic_driver_id,
                    deliver_date: deliver_date,
                    line_id :line_id,
                    warehouse_id : warehouse_id_global,

                },
                success:function(data){
                    var html='';
                    html += "<span >框："+ data['sum(A.frame_count)'] +"+"+data['sum(A.frame_meat_count)']+ "</span>&nbsp;&nbsp;";
                    html += "<span >箱："+ data['sum(A.box_count)'] +"</span>";
                    $("#sumnum").html(html);
                }
            });

        }

//        $(document).ready(function() {
//            getCustomerArea();
//        });
//        function getCustomerArea(){
//
//
//            var warehouse_id_global = $("#warehouse_id_global").val();
//            var deliver_date = $('#search_date').val();
//            $.ajax({
//                type: 'post',
//                async : false,
//                cache: false,
//                dataType: 'json',
//                url: 'index.php?route=logistic/logistic_allot_order/getCustomerArea&token=<?php echo $_SESSION["token"];?>',
//                data:{
//                    deliver_date: deliver_date,
//                    warehouse_id : warehouse_id_global,
//
//                },
//                success:function(data){
//
//                    var html = '<option value="0">全部区域</option>';
//                    $.each(data, function(n, v){
//                        html += '<option class="customer" value="'+ v.area_id+'">'+ v.name + ' ['+ v.orders +']单' +'</option>';
//                    });
//
//                    $('#customer_area').html(html);
//
//
//
//                }
//            });
//
//        }

    </script>
<?php echo $footer; ?>

