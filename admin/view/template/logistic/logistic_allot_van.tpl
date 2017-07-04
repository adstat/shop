<?php echo $header; ?><?php echo $column_left;?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h3><?php echo $heading_title; ?></h3>
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
            <div class="panel-body">
                <ul class="nav nav-tabs" id="replenish_tabs">
                    <li class="active"><a href="#tab-special" data-toggle="tab"><i class="fa fa-info"></i> 物流分区信息</a></li>

                    <li><a href="#tab-area-customer" data-toggle="tab" ><i class="fa fa-gear"></i> 物流分区管理</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="tab-special">
                        <div class="table-responsive">
                            <div class="alert alert-warning">项目功能测试中。</div>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab-area-customer">
                        <script type="text/javascript" src="<?php echo BAIDU_MAP_URL;?>"></script>
                        <script type="text/javascript" src="http://api.map.baidu.com/library/GeoUtils/1.2/src/GeoUtils_min.js"></script>
                        <script type="text/javascript" src="http://api.map.baidu.com/library/DrawingManager/1.4/src/DrawingManager_min.js"></script>
                        <link rel="stylesheet" href="http://api.map.baidu.com/library/DrawingManager/1.4/src/DrawingManager_min.css" />
                        <style>
                            .smallbtn{
                                padding: 3px 5px;
                            }
                        </style>
                        <div class="table-responsive">
                            <div class="alert alert-info">功能测试中,选择平台、区域、配送日期等可过滤订单在地图上的显示，并等待地址定位完成。选择用户请使用封闭图形（圆形，矩形，多边形）工具绘制，最好选择多边形进行绘制。</div>
                            <from >
                            <table id="area_add_row" class="table table-striped table-bordered table-hover">
                                <tbody>
                                <tr>
                                    <td width="584">
                                        状态： <select  id="classify">
                                            <option value="0">全部状态</option>
                                            <option value="1" selected="selected">未分配</option>
                                            <option value="2">已分配</option>
                                        </select>&nbsp;&nbsp;&nbsp;
                                        区域: <select   name="area_name" id="map_area_id" ;"></select>&nbsp;&nbsp;
                                        平台： <select name="station_id" id="station_id" >
                                            <option value="">选择仓库</option>
                                            <?php foreach($stations as $station){　?>
                                            <option value="<?php echo $station['station_id'] ?>" <?php if($station['station_id'] == $filterDefault['station_id']){ echo 'selected="selected"';} ?> ><?php echo $station['name']?></option>
                                            <?php }?>
                                        </select>&nbsp;&nbsp;&nbsp;
                                        配送日期：<input class="date " name="date" type="text" data-date-format="YYYY-MM-DD" placeholder="配送日期" value="<?php echo $filterDefault['deliver_date']?>" id="search_date" / >&nbsp;&nbsp;&nbsp;
                                        配送时间：  <select name="deliver_slot_id" id="slot"  >
                                            <option value="">配送时段</option>
                                            <?php foreach($slots as $slot){　?>
                                            <option value="<?php echo $slot['deliver_slot_id'] ?>" <?php if($slot['deliver_slot_id'] == $filterDefault['deliver_slot_id']){ echo 'selected="selected"';} ?> ><?php echo $slot['start_time'].'~'.$slot['end_time']?></option>
                                            <?php }?>
                                        </select>&nbsp;
                                         <input type="hidden" name="order_id" style="width:60px" id="map_order_id" value="" placeholder="订单编号" onfocus="searchByOrderId();" />&nbsp;
                                        <button type="button" class="btn btn-primary smallbtn" onclick="loadMap();">查询</button>
                                    </td>
                                    <tr>
                                    <td width="400">
                                        <div>
                                        订单数: <span id="map_all_orders" style="background-color: #66CC66; color:#5e5e5e; padding:3px; width:50px; font-size: 14px; font-weight: bold;">0</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        选中的订单数：<span id="map_selected_customers" style="background-color: #66CC66; color:#5e5e5e; padding:3px; width:50px;  font-size: 14px; font-weight: bold;">0</span>
                                        选中的总件数：<span id="map_selected_quantiy" style="background-color: #66CC66; color:#5e5e5e; padding:3px; width:50px;  font-size: 14px; font-weight: bold;">0</span>
                                        </div>
                                        应用至路线: <select name="area_linename" id="map_apply_area_line">
                                        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        司机： <select name="area_driver" id="map_apply_area_driver"></select>&nbsp;&nbsp;
                                        车辆： <select name="area_car" id="map_apply_area_car"></select>
                                        配送员：<select name="area_deliverman" id="map_apply_area_deliverman"></select>
                                        <button type="button" class="btn btn-danger smallbtn" onclick="applyOrderToDriver();">应用</button>
                                    </td>
                                    </tr>
                                </tr>
                                </tbody>
                            </table>
                            </from>
                        </div>
                        <div id="map" style="width: 100%; height:580px; margin-bottom: 10px; display: block;">
                            <!-- Load Map -->
                        </div>
                        <div class="well">
                            <div class="table-responsive">
                                <table id="unlocate" class="table table-striped table-bordered table-hover">
                                    <caption>无法定位的地址信息</caption>
                                    <thead>
                                    <tr>
                                        <td>用户编号</td>
                                        <td>送货日期</td>
                                        <td>定位地址</td>
                                        <td>BD信息</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Load unlocate point -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('.date').datetimepicker({
            pickTime: false
        });
    </script>
    <script type="text/javascript">
        <!--
        $('#language a:first').tab('show');
        $('#option a:first').tab('show');
        //-->

        var global = {
            "rowData":{
                "area" : {}
            },
            "addressList" : {},
            "returnErrorMsg": "操作失败，请检查数据是否重复，确保帐号登录状态未失效，以及网络是否正常。"
        };
    </script>

    <script type="text/javascript">
        $('#map_area_id').html(getAreaOptionList(0));
        $('#map_apply_area_line').html(getLineOptionList(0));
        $('#map_apply_area_driver').html(getDriverOptionList(0));
        $('#map_apply_area_car').html(getCarOptionList(0));
        $('#map_apply_area_deliverman').html(getDeliverymanOptionList(0));
        function getAreaOptionList(selectedValue){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/area/getArea&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(v.status == '1'){
                            if(selectedValue == v.area_name){
                                html += '<option value="'+ v.area_id +'" selected="selected">'+ v.area_district + '/' + v.area_name +'</option>';
                            }
                            else{
                                html += '<option value="'+ v.area_id +'">'+ v.area_district + '/' + v.area_name +'</option>';
                            }
                        }
                    });

                }
            });
            return html;
        }

        function getLineOptionList(Value){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_allot_van/getLine&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',

                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(v){
                                html += '<option value="'+ v.logistic_line_id +'" selected="selected">'+  v.logistic_line_title +'</option>';
                        }
                    });
                }
            });
            return html;
        }

        function getDriverOptionList(Valu){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_allot_van/getDriver&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(v){
                            html += '<option value="'+ v.logistic_driver_id +'" selected="selected">'+  v.logistic_driver_title +'</option>';
                        }
                    });
                }
            });
            return html;
        }

        function getCarOptionList(Val){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_allot_van/getCar&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(v){
                            html += '<option value="'+ v.logistic_van_id +'" selected="selected">'+  v.logistic_van_title +'</option>';
                        }
                    });
                }
            });
            return html;
        }
        function  getDeliverymanOptionList(Va){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_allot_van/getDeliveryman&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(v){
                            html += '<option value="'+ v.logistic_deliveryman_id +'" selected="selected">'+  v.logistic_deliveryman_title +'</option>';
                        }
                    });
                }
            });
            return html;
        }
    </script>


    <script type="text/javascript">
        var map = new BMap.Map("map");
        map.centerAndZoom(new BMap.Point(120.668, 31.398),11);//创建一个坐标点，其中121表示经度，31表示纬度

        //创建地图实例后，必须对其进行初始化，初始化后才能进行其它的操作，该方法设置中心点坐标和地图级别
        //map.enableScrollWheelZoom(); //鼠标缩放
        map.addControl(new BMap.NavigationControl());//平移缩放控件，默认在地图左上
        map.setCurrentCity("上海");

        var opts = {
            width : 150,     // 信息窗口宽度
            height: 150,     // 信息窗口高度
            title : "" , // 信息窗口标题
            enableMessage:true//设置允许信息窗发送短息
        };

        var index = 0;
        var myGeo = new BMap.Geocoder();
        var adds = global.address;
        var markers = [];
        var selectedMarkers = [];
        var unLocatePoint = [];

//        function initMap(){
//            $('#map_area_id').html(getAreaOptionList(0));
//            $('#map_apply_area_line').html(getLineOptionList(0));
//            $('#map_apply_area_driver').html(getDriverOptionList(0));
//            $('#map_apply_area_car').html(getCarOptionList(0));
//            $('#map_apply_area_deliverman').html(getDeliverymanOptionList(0));
//        }

        //添加鼠标绘制
        var overlays = [];
        var overlaycomplete = function(e){
            clearDrawing(); //清空之前的图形
            selectedMarkers = []; //清空之前的所选用户
            overlays.push(e.overlay);
            getSelectedMarkers(e.overlay); //获取选择的用户
            getSelectedQuantity(e.overlay); //获取件数
        };
        var styleOptions = {
            strokeColor:"green",    //边线颜色。
            fillColor:"lightgreen",      //填充颜色。当参数为空时，圆形将没有填充效果。
            strokeWeight: 3,       //边线的宽度，以像素为单位。
            strokeOpacity: 0.8,	   //边线透明度，取值范围0 - 1。
            fillOpacity: 0.6,      //填充的透明度，取值范围0 - 1。
            strokeStyle: 'solid' //边线的样式，solid或dashed。
        }

        //实例化鼠标绘制工具
        var drawingManager = new BMapLib.DrawingManager(map, {
            isOpen: false, //是否开启绘制模式
            enableDrawingTool: true, //是否显示工具栏
            drawingToolOptions: {
                anchor: BMAP_ANCHOR_TOP_RIGHT, //位置
                offset: new BMap.Size(5, 5), //偏离值
            },
            circleOptions: styleOptions, //圆的样式
            polylineOptions: styleOptions, //线的样式
            polygonOptions: styleOptions, //多边形的样式
            rectangleOptions: styleOptions //矩形的样式
        });

        //添加鼠标绘制工具监听事件，用于获取绘制结果
        drawingManager.addEventListener('overlaycomplete', overlaycomplete);
        function clearDrawing() {
            for(var i = 0; i < overlays.length; i++){
                map.removeOverlay(overlays[i]);

            }
            overlays.length = 0;
           // $('#map_marked_customers').html(overlays.length);
        }
     function getSelectedQuantity(ply){
         var array = [];
         $.each(markers,function(i,v) {

             var pt = v.point;
             var result = BMapLib.GeoUtils.isPointInPolygon(pt, ply);
             if(result == true){
                 array.push(v.order_id);
             }


         });

           var  order_id = array;

         $.ajax({
             type: 'POST',
             async: false,
             cache: false,
             url: 'index.php?route=logistic/logistic_allot_van/getSelectedQuantity&token=<?php echo $_SESSION["token"]; ?>',
             data : {
                order_id :order_id

             },
             success: function(data){
                 $("#map_selected_quantiy").html(data);
             }

         });
     }
        function getSelectedMarkers(ply){
            $.each(markers, function(i,v){

                var pt = v.point;
                var result = BMapLib.GeoUtils.isPointInPolygon(pt, ply);
                if(result == true){
                    selectedMarkers.push(v.order_id);
                }
            });

            $('#map_selected_customers').html(selectedMarkers.length);
        }
        function loadMap() {
            removeOverlay();
            clearDrawing();
            markers = [];
            selectedMarkers = [];
            $('#map_all_orders').html(0);
            $('#map_marked_customers').html(0);
            $('#unlocate tbody').html('');

            var classify   = $('#classify').val();
            var station_id = $('#station_id').val();
            var area_id = $('#map_area_id').val();
            var search_date = $('#search_date').val() ;
            var order_id = $('#map_order_id').val() == '' ? false : parseInt($('#map_order_id').val());
            var warehouse_id_global = $("#warehouse_id_global").val();


            getOrdersByAreaByDate(classify,area_id,search_date,order_id,station_id,warehouse_id_global);

            $('#map_all_orders').html(global.addressList.length);
            bdGEO();
        }

        function applyOrderToDriver(){
           // var formData = new FormData($( "#vue_form" ).get(0));
            var warehouse_id_global = $("#warehouse_id_global").val();
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_allot_van/addAllotOrder&token=<?php echo $_SESSION["token"]; ?>',
                data : {
                    order_ids:selectedMarkers,
                    station_id:$('#station_id').val(),
                    deliver_date:$('#search_date').val(),
                    deliver_slot_id:$('#slot').val(),
                    logistic_line_id:$('#map_apply_area_line').val(),
                    logistic_driver_id:$('#map_apply_area_driver').val(),
                    logistic_van_id:$('#map_apply_area_car').val(),
                    logistic_deliveryman_id:$('#map_apply_area_deliverman').val(),
                    warehouse_id_global : warehouse_id_global,
                },
                success: function(data){
                    if(data == 1){
                        alert("分配成功");
                        loadMap();
                    }else{
                        alert('请确认添加的数据完整');
                    }
//                    if(data == 'true'){
//                        alert('设置成功');
//                    }
                },
                error: function(){
                    alert(global.returnErrorMsg);
                }
            });
        }




        function searchByOrderId(){
            $('#map_area_id').val(0);
            $('#station_id').val(0);
            $('#search_date').val(0);
        }
        function searchByArea(){
            $('#map_order_id').val('');
        }


        function getOrdersByAreaByDate(classify,area_id,search_date,order_id,station_id,warehouse_id_global){
            if(warehouse_id_global == 0){
                alert('请先选择所在仓库');
                return false;
            }

            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_allot_van/getOrdersByAreaByDate&token=<?php echo $_SESSION["token"]; ?>',
                data : {
                    classify:classify,
                    area_id : area_id,
                    search_date : search_date,
                    station_id:station_id,
                    order_id : order_id,
                    warehouse_id_global : warehouse_id_global,
                },
                success: function(data){
                    global.addressList = $.parseJSON(data);
                },
                error: function(){
                    alert(global.returnErrorMsg);

                }
            });
        }

        function bdGEO(){

            $.each(global.addressList, function(i,v){
                var add = v;
                setTimeout(geocodeSearch(add), 10);
            });
        }


        function geocodeSearch(add){
            myGeo.getPoint(add.shipping_address_1, function(point){
                if (point) {
                    var address = new BMap.Point(point.lng, point.lat);
                    var marker = new BMap.Marker(address);
                    var customerStatus = '';
                    if(add.status == '0'){
                        customerStatus = '(已停用)';
                    }
                    var content = "<strong>订单号: </strong> " + add.order_id  + "<br /><strong>平台："+add.name+"<br/><strong>BD人员: </strong>"+ add.bd_name + "<br /><strong>送货地址: </strong>" + add.shipping_address_1 + "<br /><strong>送货日期: </strong>"+add.deliver_date+"<br /><strong>配送时间: </strong>"+add.deliver_slot +"<br/><strong>件数:</strong>"+add.num;

                    marker.order_id = add.order_id;
                    markers.push(marker); //记录点的位置数据。
                    map.addOverlay(marker);
                    addClickHandler(content,marker);
                   // $('#map_marked_customers').html(markers.length);
                }
                else{
                    unLocatePoint.push(add);
                    var html = '';
                    html += '<tr>'
                    html += '<td>' + add.order_id + '</td>';
                    html += '<td>' + add.deliver_date + '</td>';
                    html += '<td>' + add.shipping_address_1 + '</td>';
                    html += '<td>' + add.bd_name + '</td>';
                    html += '</tr>';

                    $('#unlocate tbody').append(html);
                    html = '';
                }
            }, "上海市");
        }

        // 编写自定义函数,创建标注
        function addMarker(point,label){
            var marker = new BMap.Marker(point);
            map.addOverlay(marker);
            marker.setLabel(label);
            //addClickHandler(label,marker);
        }

        //清除覆盖物
        function removeOverlay(){
            map.clearOverlays();
        }
        function remove(){
            map.removeOverlay();
        }

        function addClickHandler(content,marker){
            marker.addEventListener("click",function(e){
                openInfo(content,e)}
            );
        }

        function openInfo(content,e){
            var p = e.target;
            var point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
            var infoWindow = new BMap.InfoWindow(content,opts);  // 创建信息窗口对象
            map.openInfoWindow(infoWindow,point); //开启信息窗口
        }

    </script>
</div>
<?php echo $footer; ?>