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
                <li class="active"><a href="#tab-special" data-toggle="tab"><i class="fa fa-info"></i> 用户分区信息</a></li>
                <li><a href="#tab-area" data-toggle="tab" onclick="getArea();"><i class="fa fa-gear"></i> 分区管理</a></li>
                <li><a href="#tab-area-customer" data-toggle="tab" onclick="initMap()"><i class="fa fa-gear"></i> 用户分区管理</a></li>
                <li><a href="#tab-area-map-set" data-toggle="tab" onclick="initSetMap()"><i class="fa fa-gear"></i>区域地图管理</a></li>
              </ul>

              <div class="tab-content">
                  <div class="tab-pane active" id="tab-special">
                      <div class="table-responsive">
                          <div class="alert alert-warning">项目功能测试中。</div>
                      </div>
                  </div>

                  <div class="tab-pane" id="tab-area">
                      <div class="table-responsive">
                          <div class="alert alert-info">
                            订单要调离到新的BD人员下，区域用户调离必须得选择确认选项否则无效。
                          </div>
                          <form method="post" enctype="multipart/form-data" id="form-area-edit" class="form-horizontal">
                              <table id="area_list" class="table table-striped table-bordered table-hover">
                                  <thead>
                                      <tr>
                                          <td class="text-left"></td>
                                          <td class="text-left">城市</td>
                                          <td class="text-left">区域</td>
                                          <td class="text-left">分区名称</td>
                                          <td class="text-left">市场人员</td>
                                          <td class="text-left">区域仓库</td>
                                          <!--<td class="text-left">区域用户调离</td>-->
                                          <td class="text-left">订单调离起始日期</td>
                                          <td class="text-left">状态</td>
                                          <td class="text-left">操作</td>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <!-- data -->
                                  </tbody>
                                  <tfoot>
                                        <!-- data -->
                                  </tfoot>
                              </table>
                          </form>

                          <button type="button" class="btn btn-primary" style="margin: 3px; float: right" onclick="addArea();">添加</button>
                          <button type="button" class="btn btn-primary" style="margin: 3px; float: right" onclick="toggleBlockedArea();">查看/隐藏无效区域</button>
                          <form method="post" enctype="multipart/form-data" id="form-area-add" class="form-horizontal">
                              <table id="area_add_row" style="display: none" class="table table-striped table-bordered table-hover">
                                  <thead>
                                      <tr>
                                          <td class="text-left">城市</td>
                                          <td class="text-left">区域</td>
                                          <td class="text-left">分区名称</td>
                                          <td class="text-left">市场人员</td>
                                          <td class="text-left">区域仓库</td>
                                          <td class="text-left">操作</td>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <tr>
                                          <td><select name="area_city" id="area_city"><!-- City List --></select></td>
                                          <td><select name="area_district" id="area_district"><!-- District List --></select></td>
                                          <td><input name="area_name" id="area_name" value="" /></td>
                                          <td><select name="bd_id" id="bd_id"><!-- Bd List --></select></td>
                                          <td id="warehouse_ids"><!-- Warehouse List --></td>
                                          <td><button type="button" class="btn btn-danger" onclick="addRow('form-area-add','area');">保存</button></td>
                                      </tr>
                                  </tbody>
                              </table>
                          </form>
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
                              <div class="alert alert-info">功能测试中, 区域留空，选择BD可过滤当前未被分区的用户，并等待地址定位完成。选择用户请使用封闭图形（圆形，矩形，多边形）工具绘制。</div>
                              <table id="area_add_row" class="table table-striped table-bordered table-hover">
                                  <tbody>
                                  <tr>
                                      <td>
                                          区域: <select name="area_name" id="map_area_id" onchange="searchByArea();"></select>&nbsp;&nbsp;&nbsp;
                                          BD: <select name="bd_id" id="map_bd_id" onchange="searchByArea();"></select>&nbsp;&nbsp;&nbsp;
                                          [或] 用户ID: <input name="customer_id" style="width:60px" id="map_customer_id" value="" placeholder="用户编号" onfocus="searchByCustomerId();" />&nbsp;&nbsp;&nbsp;
                                          <button type="button" class="btn btn-primary smallbtn" onclick="loadMap();">查询</button>
                                      </td>
                                      <td>
                                          用户数: <span id="map_all_customers" style="background-color: #66CC66; color:#5e5e5e; padding:3px; width:50px; font-size: 14px; font-weight: bold;">0</span>&nbsp;&nbsp;&nbsp;
                                          定位用户数: <span id="map_marked_customers" style="background-color: #66CC66; color:#5e5e5e; padding:3px; width:50px;  font-size: 14px; font-weight: bold;">0</span>&nbsp;&nbsp;&nbsp;
                                          已选择: <span id="map_selected_customers" style="background-color: #66CC66; color:#5e5e5e; padding:3px; width:50px;  font-size: 14px; font-weight: bold;">0</span>&nbsp;&nbsp;&nbsp;
                                          应用至区域: <select name="area_name" id="map_apply_area_id"></select>
                                          <button type="button" class="btn btn-danger smallbtn" onclick="applyCustomerToArea();">应用</button>
                                      </td>
                                  </tr>
                                  </tbody>
                              </table>
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
                                          <td>店名</td>
                                          <td>注册地址</td>
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

                  <div class="tab-pane" id="tab-area-map-set">
                      <script type="text/javascript" src="<?php echo BAIDU_MAP_URL;?>"></script>
                      <script type="text/javascript" src="http://api.map.baidu.com/library/GeoUtils/1.2/src/GeoUtils_min.js"></script>
                      <script type="text/javascript" src="http://api.map.baidu.com/library/DrawingManager/1.4/src/DrawingManager_min.js"></script>
                      <link rel="stylesheet" href="http://api.map.baidu.com/library/DrawingManager/1.4/src/DrawingManager_min.css" />
                      <div class="table-responsive">
                          <table id = "set-area" class="table table-striped table-bordered table-hover">
                            <tbody>
                            <tr>
                                <td>
                                    区域: <select name="area_name" id="draw_map_area_id"></select>&nbsp;&nbsp;&nbsp;
                                    BD: <select name="bd_id" id="draw_map_bd_id"></select>&nbsp;&nbsp;&nbsp;
                                    <button type="button" class="btn btn-primary smallbtn" onclick="loadDrawMap();">查询</button>
                                </td>
                                <td>
                                    应用至区域: <select name="area_name" id="draw_map_apply_area_id"></select>
                                    <button type="button" class="btn btn-danger smallbtn" onclick="applyDrawToArea();">应用</button>
                                    <button type="button" class="btn btn-primary smallbtn" onclick="clearAll();">清除</button>

                                </td>
                            </tr>
                            </tbody>
                          </table>
                      </div>

                      <div id="map1" style="width: 100%; height:580px; margin-bottom: 10px; display: block;">
                          <!-- Load Map1 -->
                      </div>
                  </div>

              </div>
          </div>
      </div>
    </div>
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
            "returnErrorMsg": "操作失败，请检查数据是否重复，确保帐号登录状态未失效，以及网络是否正常。",
            'warehouseData':{}
        };
    </script>

    <script type="text/javascript">
        function statusTag(status){
            var statusTag = '';
            if(status == 1){
                statusTag = '<span style="background-color: #66CC66; color: #ffffff; padding: 3px;">启用</span>';
            }

            if(status == 0){
                statusTag = '<span style="background-color: #cc0000; color: #FFFF00; padding: 3px;">停用</span>';
            }

            return statusTag;
        }

        function operationTag(formId,type,id){
            var tag = '<button type="button" class="btn btn-default" id="edit_'+ type + '_' + id +'" onclick="editRow(\''+ type +'\','+ id +')"><i class="fa fa-pencil"></i></button>';
                tag += '<button style="display: none" id="save_'+ type + '_' + id +'" type="button" class="btn btn-danger" onclick="updateRow(\''+ formId +'\',\''+ type +'\','+ id +')"><i class="fa fa-save"></i></button>';
            return tag;
        }

        function recallFunc(type){
            if(type == 'area'){ getArea(); }
        }

        function addRow(formId,type){
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/area/add&token=<?php echo $_SESSION["token"]; ?>',
                //dataType: 'json',
                data : {
                    type :type,
                    postData : $('#'+formId).serializeArray()
                },
                success: function(data){
                    console.log(data);
                    if(data == 'true'){
                        recallFunc(type);
                        $('#'+ type +'_add_row').hide();
                    }
                    else{
                        alert(global.returnErrorMsg);
                    }
                },
                error: function(){
                    alert(global.returnErrorMsg);
                }
            });
        }

        function editRow(type, id){
            var targetRow = '#'+type+'_'+id;
            var targetRowName = '';
            var targetRowValue = '';
            var targetDataset = '';

            //Recorver All Others
            $.each(global.rowData[type], function(i,v){
                if( i == id ){
                    $.each($('#'+type+'_'+ i + ' .editable'), function(index,value){
                        targetRowName = $(this).attr("datatag");
                        targetRowValue = global.rowData[type][i][targetRowName];

                        if(targetRowValue instanceof Array && targetRowValue.length > 0){
                            targetRowValue = JSON.stringify(targetRowValue);
                        }

                        $(this).html(targetRowValue);
                    });
                }

                $('#edit_'+type+'_'+i).show();
                $('#save_'+type+'_'+i).hide();
            });

            $.each($(targetRow+' .editable'), function(i,v){
                targetRowName = $(this).attr("datatag");
                targetRowValue = $(this).text();
                targetDataset = $(this).attr("dataset");

                if(targetDataset == 'city'){
                    $(this).html('<select name="area_city">'+ getCityOptionList($(this).attr('value')) +'</select>');
                }
                else if(targetDataset == 'district'){
                    $(this).html('<select name="area_district">'+ getDistrictOptionList($(this).attr('value')) +'</select>');
                }
                else if(targetDataset == 'bd'){
                    $(this).html('<span>'+ '更改BD为：' +'</span>'+'<select name="bd_id">'+ getBdOptionList($(this).attr('value')) +'</select>');
                }

//                else if(targetDataset == 'users'){
//                    $(this).html('<select name="area_users"><option value="0" selected="selected">-</option><option value="1" >确认</option><option value="2">否认</option></select>');
//                }
                else if(targetDataset == 'orders'){
                    $(this).html(' <div class = "input-group order_date"><input type="text"  name="order_date"   data-date-format="YYYY-MM-DD" id="input_purchase_date" class="form-control" /> <span class="input-group-btn">  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button></span></div>');

                        $('.order_date').datetimepicker({
                        pickTime: false
                    });



                }
                else if(targetDataset == 'status'){
                    $(this).html('<select name="status">'+ getStatusOptionList($(this).attr('value')) +'</select>');
                }
                else if(targetDataset == 'warehouse_ids'){
                    var warehouseIds  = [];
                    if(targetRowValue != ''){
                        targetRowValue = JSON.parse(targetRowValue);
                    }
                    $.each(targetRowValue, function(index, value){
                        warehouseIds.push(value.warehouse_id)
                    });

                    $(this).html(getWarehouseList(warehouseIds));
                }
                else{
                    $(this).html('<input type="text" name="'+ targetRowName +'" value="'+ targetRowValue +'" />');
                }
            });

            $('#edit_'+type+'_'+id).hide();
            $('#save_'+type+'_'+id).show();
        }

        function updateRow(formId,type,id){
            var flag = confirm('确认更改BD信息?');
            if(flag) {
                $.ajax({
                    type: 'POST',
                    async: false,
                    cache: false,
                    url: 'index.php?route=marketing/area/update&token=<?php echo $_SESSION["token"]; ?>',
                    data : {
                        type : type,
                        id : id,
                        postData : $('#'+formId).serializeArray()
                    },
                    dataType: 'json',
                    success: function(data){

                        console.log(data);
                        if(data.flag == 'ok'){
                            alert('更改BD信息成功！');
                            recallFunc(type);
                        }else{
                            alert(global.returnErrorMsg);
                            recallFunc(type);
                        }
                    },
                    error: function(){
                        alert(global.returnErrorMsg);
                    }
                });
            }else{
                return false;
            }

        }

        function getCityOptionList(selectedValue){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/area/getAreaCity&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(selectedValue == v){
                            html += '<option value="'+ v +'" selected="selected">'+ v +'</option>';
                        }
                        else{
                            html += '<option value="'+ v +'">'+ v +'</option>';
                        }
                    });

                }
            });

            //console.log(html);
            return html;
        }

        function getDistrictOptionList(selectedValue){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/area/getAreaDistrict&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(selectedValue == v){
                            html += '<option value="'+ v +'" selected="selected">'+ v +'</option>';
                        }
                        else{
                            html += '<option value="'+ v +'">'+ v +'</option>';
                        }
                    });

                }
            });

            //console.log(html);
            return html;
        }

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

            //console.log(html);
            return html;
        }

        function getAreaUnsetList(selectedValue){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/area/getAreaUnset&token=<?php echo $_SESSION["token"]; ?>',
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

            //console.log(html);
            return html;
        }

        function getBdOptionList(selectedValue){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/area/getBdList&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(selectedValue == v.bd_id){
                            html += '<option value="'+ v.bd_id +'" selected="selected">'+ v.bd_name +'</option>';
                        }
                        else{
                            html += '<option value="'+ v.bd_id +'">'+ v.bd_name +'</option>';
                        }
                    });

                }
            });

            //console.log(html);
            return html;
        }

        function getStatusOptionList(selectedValue){
            var html= '';
            if(selectedValue == 1){
                html += '<option value="1" selected="selected">启用</option>';
            }
            else{
                html += '<option value="1">启用</option>';
            }

            if(selectedValue == 0){
                html += '<option value="0" selected="selected">停用</option>';
            }
            else{
                html += '<option value="0">停用</option>';
            }

            return html;
        }

        function getWarehouseList(warehouseIds){
            var warehouseHtml  = '';
            $.each(global.warehouseData, function(i, v){
                var selected   = "";
                warehouseHtml += "<span>"+ v.station_name +"：</span>";
                warehouseHtml += "<select name='warehouse_ids'>";
                warehouseHtml += "<option value='0'>请选择</option>";

                $.each(v.warehouse_data, function(wi, wv){
                    if($.inArray(wv.warehouse_id, warehouseIds) >= 0){
                        selected = "selected";
                    }
                    warehouseHtml += "<option value='"+ wv.warehouse_id +"' "+ selected +" >"+ wv.title +"</option>";
                });
                warehouseHtml += "</select><br >";
            });

            return warehouseHtml;
        }

        function addArea(){
            $('#area_add_row').show();
            var selectedValue = 0;

            $('#area_city').html(getCityOptionList(selectedValue));
            $('#area_district').html(getDistrictOptionList(selectedValue));
            $('#bd_id').html(getBdOptionList(selectedValue));
            $('#warehouse_ids').html(getWarehouseList(selectedValue));
        }

        function toggleBlockedArea(){
            $('#area_list tfoot').toggle();
        }

        function getArea(){
            $.ajax({
                type: 'GET',
                url: 'index.php?route=marketing/area/getArea&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    console.log(data);
                    var html= '';
                    var htmlBlocked= '';
                    var num = 1;
                    $('#area_list tbody').html('');
                    $('#area_list tfoot').html('');

                    $.each(data, function(i,v){
                        if(v.status == '1'){
                            html += '<tr id="area_'+ v.area_id +'">';
                            html += '<td>'+ num++ +'</td>';
                            html += '<td>'+ v.area_city +'</td>';
                            html += '<td class="editable" dataset="district" value="'+ v.area_district +'" datatag="area_district">'+ v.area_district +'</td>';
                            html += '<td class="editable" datatag="area_name">'+ v.area_name +'</td>';
                            html += '<td class="editable" dataset="bd" value="'+ v.bd_id+'" datatag="bd_name">'+ v.bd_name +'</td>';
                            html += '<td class="editable" dataset="warehouse_ids" datatag="warehouse_data">';
                            $.each(v.warehouse_data, function(wi,wx){
                                html += '['+ wx.station_name +'] <b>'+ wx.title + '</b><br>';
                            });
                            html += '</td>';
//                            html += '<td class="editable" dataset="users"  datatag="area_users"></td>';
                            html += '<td class="editable" dataset="orders"  datatag="area_orders"></td>';

                            html += '<td class="editable" dataset="status" value="'+ v.status +'" datatag="status">'+ statusTag(v.status) +'</td>';
                            html += '<td>'+ operationTag("form-area-edit","area", v.area_id) +'</td>';
                            html += '</tr>';


                        }
                        else{
                            htmlBlocked += '<tr id="area_'+ v.area_id +'">';
                            htmlBlocked += '<td>'+ num++ +'</td>';
                            htmlBlocked += '<td>'+ v.area_city +'</td>';
                            htmlBlocked += '<td class="editable" dataset="district" value="'+ v.area_district +'" datatag="area_district">'+ v.area_district +'</td>';
                            htmlBlocked += '<td class="editable" datatag="area_name">'+ v.area_name +'</td>';
                            htmlBlocked += '<td class="editable" dataset="bd" value="'+ v.bd_id+'" datatag="bd_name">'+ v.bd_name +'</td>';
                            htmlBlocked += '<td class="editable" dataset="warehouse_ids" datatag="warehouse_data">';
                            $.each(v.warehouse_data, function(wi,wx){
                                htmlBlocked += '['+ wx.station_name +'] <b>'+ wx.title + '</b><br>';
                            });
                            htmlBlocked += '</td>';
//                            htmlBlocked += '<td class="editable" dataset="users"  datatag="area_users"></td>';
                            htmlBlocked += '<td class="editable" dataset="orders"  datatag="area_orders"></td>';
                            htmlBlocked += '<td class="editable" dataset="status" value="'+ v.status +'" datatag="status">'+ statusTag(v.status) +'</td>';
                            htmlBlocked += '<td>'+ operationTag("form-area-edit","area", v.area_id) +'</td>';
                            htmlBlocked += '</tr>';
                        }

                        global.rowData['area'][v.area_id] = v;
                    });

                    $('#area_list tbody').html(html);
                    $('#area_list tfoot').html(htmlBlocked);
                    $('#area_list tfoot').hide();

                }
            });

            $.ajax({
                type: 'GET',
                url: 'index.php?route=marketing/area/getWarehouseAndStation&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    global.warehouseData = data;
                    console.log(data);
                }
            });

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
            height: 100,     // 信息窗口高度
            title : "" , // 信息窗口标题
            enableMessage:true//设置允许信息窗发送短息
        };

        var index = 0;
        var myGeo = new BMap.Geocoder();
        var adds = global.address;
        var markers = [];
        var selectedMarkers = [];
        var unLocatePoint = [];

        function initMap(){
            $('#map_area_id').html(getAreaOptionList(0));
            $('#map_bd_id').html(getBdOptionList(0));

            $('#map_apply_area_id').html(getAreaOptionList(0));
        }

        //添加鼠标绘制
        var overlays = [];
        var overlaycomplete = function(e){
            clearDrawing(); //清空之前的图形
            selectedMarkers = []; //清空之前的所选用户

            overlays.push(e.overlay);
            getSelectedMarkers(e.overlay); //获取选择的用户
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
            overlays.length = 0
        }

        function getSelectedMarkers(ply){
            $.each(markers, function(i,v){
                var pt = v.point;
                var result = BMapLib.GeoUtils.isPointInPolygon(pt, ply);
                if(result == true){
                    selectedMarkers.push(v.customer_id);
                }
            });

            $('#map_selected_customers').html(selectedMarkers.length);
        }

        function applyCustomerToArea(){
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/area/applyCustomerToArea&token=<?php echo $_SESSION["token"]; ?>',
                data : {
                    customers : selectedMarkers,
                    area_id : $('#map_apply_area_id').val()
                },
                success: function(data){
                    console.log(data);

                    if(data == 'true'){
                        alert('设置成功');
                        loadMap();
                    }
                },
                error: function(){
                    alert(global.returnErrorMsg);
                }
            });
        }

        function loadMap() {
            removeOverlay();
            clearDrawing();
            markers = [];
            selectedMarkers = [];
            $('#map_all_customers').html(0);
            $('#map_marked_customers').html(0);
            $('#unlocate tbody').html('');

            var area_id = $('#map_area_id').val();
            var bd_id = $('#map_bd_id').val() == 0 ? false : $('#map_bd_id').val();
            var customer_id = $('#map_customer_id').val() == '' ? false : parseInt($('#map_customer_id').val());
            //var bd_id = $('#map_bd_id').val();
            getCustomerByAreaByBd(area_id,bd_id,customer_id);

            $('#map_all_customers').html(global.addressList.length);
            bdGEO();
        }

        function searchByCustomerId(){
            $('#map_area_id').val(0);
            $('#map_bd_id').val(0);
        }

        function searchByArea(){
            $('#map_customer_id').val('');
        }

        function getCustomerByAreaByBd(area_id,bd_id,customer_id){
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/area/getCustomerByAreaByBd&token=<?php echo $_SESSION["token"]; ?>',
                data : {
                    area_id : area_id,
                    bd_id : bd_id,
                    customer_id : customer_id
                },
                success: function(data){
                   // console.log(data);
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
            myGeo.getPoint(add.address, function(point){
                if (point) {
                    //document.getElementById("result").innerHTML +=  index + "、" + add + ":" + point.lng + "," + point.lat + "</br>";
                    var address = new BMap.Point(point.lng, point.lat);
                    var marker = new BMap.Marker(address);
                    var customerStatus = '';
                    if(add.status == '0'){
                        customerStatus = '(已停用)';
                    }
                    var content = "<strong>商家名称: </strong> " + add.merchant_name + customerStatus + "<br /><strong>BD人员: </strong>"+ add.bd_name + "<br /><strong>下单地址: </strong>" + add.address + "<br /><strong>注册地址: </strong>"+add.merchant_address;

                    marker.customer_id = add.customer_id;
                    markers.push(marker); //记录点的位置数据。
                    map.addOverlay(marker);
                    addClickHandler(content,marker);

                    $('#map_marked_customers').html(markers.length);
                }
                else{
                    unLocatePoint.push(add);
                    var html = '';
                    html += '<tr>'
                        html += '<td>' + add.customer_id + '</td>';
                        html += '<td>' + add.merchant_name + '</td>';
                        html += '<td>' + add.merchant_address + '</td>';
                        html += '<td>' + add.address + '</td>';
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

    <script type="text/javascript">
        function initSetMap(){
//            $('#map_area_id').html(getAreaOptionList(0));
            $('#draw_map_bd_id').html(getBdOptionList(0));
            $('#draw_map_area_id').html(getAreaOptionList(0));
            $('#draw_map_apply_area_id').html(getAreaUnsetList(0));

            loadDrawMap();
        }
    </script>

    <script type="text/javascript">
        // 百度地图API功能
        var map1 = new BMap.Map('map1');
        var poi1 = new BMap.Point(120.668, 31.398);
        map1.centerAndZoom(poi1, 11);
        map1.enableScrollWheelZoom();

        map1.addControl(new BMap.NavigationControl());//平移缩放控件，默认在地图左上
        map1.setCurrentCity("上海");

        var overlays1 = [];
        var overlaycomplete1 = function(e){
            overlays1.push(e.overlay);
        };
        var styleOptions1 = {
            strokeColor:"red",    //边线颜色。
            fillColor:"#CCCFFF",      //填充颜色。当参数为空时，圆形将没有填充效果。
            strokeWeight: 3,       //边线的宽度，以像素为单位。
            strokeOpacity: 0.8,	   //边线透明度，取值范围0 - 1。
            fillOpacity: 0.6,      //填充的透明度，取值范围0 - 1。
            strokeStyle: 'solid' //边线的样式，solid或dashed。
        }
        //实例化鼠标绘制工具
        var drawingManager1 = new BMapLib.DrawingManager(map1, {
            isOpen: false, //是否开启绘制模式
            enableDrawingTool: true, //是否显示工具栏
            drawingToolOptions: {
                anchor: BMAP_ANCHOR_TOP_RIGHT, //位置
                offset: new BMap.Size(5, 5), //偏离值
            },
            circleOptions: styleOptions1, //圆的样式
            polylineOptions: styleOptions1, //线的样式
            polygonOptions: styleOptions1, //多边形的样式
            rectangleOptions: styleOptions1 //矩形的样式
        });
        //添加鼠标绘制工具监听事件，用于获取绘制结果
        drawingManager1.addEventListener('overlaycomplete', overlaycomplete1);

        //删除覆盖物
        function clearAll() {
            //删除没有保存的绘图
            for(var i = 0; i < overlays1.length; i++){
                map1.removeOverlay(overlays1[i]);
            }
            overlays1.length = 0

            //删除地图上所有覆盖物
            map1.clearOverlays();
        }

        //绑定所画地图到区域
        function applyDrawToArea(){
            var area_map = [];
            console.log(overlays1);
            if(overlays1.length){
                console.log(overlays1[overlays1.length - 1]['po']);
                var positions = overlays1[overlays1.length - 1]['po'];
                $.each(positions,function(i,v){
                    var position = [];
                    position.push(v.lat);
                    position.push(v.lng);
                    area_map.push(position);
                });

                $.ajax({
                    type: 'POST',
                    async: false,
                    cache: false,
                    url: 'index.php?route=marketing/area/applyDrawToArea&token=<?php echo $_SESSION["token"]; ?>',
                    data : {
                        positions : area_map,
                        area_id : $('#draw_map_apply_area_id').val(),
                    },
                    success: function(data){
                        console.log(data);

                        if(data == 'true'){
                            alert('设置成功');
                            loadDrawMap();
                            $('#draw_map_apply_area_id').html(getAreaUnsetList(0));
                        }
                    },
                    error: function(){
                        alert(global.returnErrorMsg);
                    }
                });

            }else{
                alert("没有画区域");
            }
        }

        //加载区域绘图信息
        function loadDrawMap(){
            //每次初始化地图的时候删除地图上已有的信息
            clearAll();
            var area_id = $('#draw_map_area_id').val();
            var bd_id = $('#draw_map_bd_id').val() == 0 ? false : $('#draw_map_bd_id').val();
            showDrawed(area_id,bd_id);
        }

        function showDrawed(area_id,bd_id){
            var draw_info = getDrawPoint(area_id,bd_id);
            console.log(draw_info);
            $.each(draw_info,function(i,v){
                setTimeout(drawRectangle(v), 10);
            });
        }

        //获取多边形的定点，并在地图上画出来
        function drawRectangle(data){
            var position = eval("("+data[1]+")");//获取多边形的顶点
            console.log(data[3]);
            //创建标签
            var Label = new BMap.Label(data[0],{position: new BMap.Point(data[2][0],data[2][1])});
            Label.setStyle({"line-height": "20px", "text-align": "center", "width": "80px", "height": "29px", "border": "none", "padding": "2px","background": "url(http://jixingjx.com/mapapi/ac.gif) no-repeat",});

//            var PolygonPoints = [new BMap.Point(121.112985,31.234125),new BMap.Point(121.091138,31.122406),new BMap.Point(121.199222,31.084807),new BMap.Point(121.259013,31.124385),new BMap.Point(121.272811,31.165923),new BMap.Point(121.262463,31.194593),new BMap.Point(121.236017,31.218314),new BMap.Point(121.21302,31.23116),new BMap.Point(121.190023,31.241041),new BMap.Point(121.164727,31.243017),new BMap.Point(121.14518,31.243017)];
            var PolygonPoints = position;
            //创建多边形
            var drawPolygon = new BMap.Polygon(PolygonPoints, {strokeColor:"blue", strokeWeight:5, strokeOpacity:0.5});

            map1.addOverlay(drawPolygon);

            //给多边形添加鼠标事件
            drawPolygon.addEventListener("mouseover",function(){
                drawPolygon.setStrokeColor("red");
                map1.addOverlay(Label);
            });

            drawPolygon.addEventListener("mouseout",function(){
                drawPolygon.setStrokeColor("blue");
                map1.removeOverlay(Label);
            });

            drawPolygon.addEventListener("click",function(){
                var flag = confirm("是否删除该区域绘制的地图信息？");
                if(flag){
                    map1.removeOverlay(drawPolygon);
                    $.ajax({
                        type: 'POST',
                        async: false,
                        cache: false,
                        url: 'index.php?route=marketing/area/deleteDrawToArea&token=<?php echo $_SESSION["token"]; ?>',
                        data : {
                            area_id:data[3],
                        },
                        success: function(response){
                            if(response){
                                alert('删除区域：['+data[0]+']成功!');
                                //删除成功之后，区域下拉选择框返还没有之前被绘制图像的区域
                                $('#draw_map_apply_area_id').html(getAreaUnsetList(0));
                            }else{
                                alert('删除失败!');
                            }
                        },
                    });
                }else{
                    return false;
                }
                map1.zoomIn();
                drawPolygon.setStrokeColor("red");
            });

        }

        //获取区域存储的绘图信息
        function getAreaDrawInfo(area_id,bd_id){
            var result;
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/area/getAreaDrawInfo&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                data:{
                    area_id:area_id,
                    bd_id:bd_id,
                },
                success: function(data){
                    if(data.length){
                        result = data;
                    }else{
                        result = 0;
                    }
                    console.log(result);
                }
            });

            return result;
        }

        //生成多边形信息模板
        function getDrawPoint(area_id,bd_id){
            var point = getAreaDrawInfo(area_id,bd_id);
            var pointString = [];
            //循环遍历绘制的地图信息
            $.each(point,function(i,v){
                var push_array = [];
                var new_map = '';
                $.each(v.draw_info,function(ii,vv){
                    new BMap.Point(116.362875,39.949459);
                    if(ii < v.draw_info.length-1){
                        if(ii == 0){
                            new_map += '[new BMap.Point('+vv[1]+','+vv[0]+'),';
                        }
                        new_map += 'new BMap.Point('+vv[1]+','+vv[0]+'),';
                    }else{
                        new_map += 'new BMap.Point('+vv[1]+','+vv[0]+')]';
                    }
                });
                push_array.push(v.name);
                push_array.push(new_map);
                push_array.push(v.center);
                push_array.push(v.area_id);

                pointString.push(push_array);
            });
            return pointString;
        }
    </script>
</div>
<?php echo $footer; ?> 