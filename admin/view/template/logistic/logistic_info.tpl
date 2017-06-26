<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h3><?php echo $heading_title; ?></h3>
        </div>
    </div>
    <div class="container-fluid">
    <div class="alert alert-info">司机/线路/车辆／配送员信息，添加后名称不可修改，可停用。</div>

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
                <li class="active"><a href="#tab-special" data-toggle="tab"><i class="fa fa-info"></i> 司机线路信息维护</a></li>
                <li><a href="#tab-line" data-toggle="tab" onclick="getLogisticLine();"><i class="fa fa-gear"></i> 线路信息</a></li>
                <li><a href="#tab-driver" data-toggle="tab" onclick="getLogisticDriver();"><i class="fa fa-gear"></i> 司机信息</a></li>
                <li><a href="#tab-van" data-toggle="tab" onclick="getLogisticVan();"><i class="fa fa-gear"></i> 车辆信息</a></li>
                <li><a href="#tab-deliveryman" data-toggle="tab" onclick="getLogisticDeliveryman();"><i class="fa fa-gear"></i> 配送员信息</a></li>
              </ul>

              <div class="tab-content">
                  <div class="tab-pane active" id="tab-special">
                      <div class="table-responsive">
                          <div class="alert alert-warning">项目功能测试中，点击“线路信息”等标签维护相关信息，添加按钮在对应标签页面底部。</div>
                      </div>
                  </div>

                  <div class="tab-pane" id="tab-line">
                      <div class="table-responsive">
                          <form method="post" enctype="multipart/form-data" id="form-line-edit" class="form-horizontal">
                              <table id="line_list" class="table table-striped table-bordered table-hover">
                                  <thead>
                                      <tr>
                                          <td class="text-left">编号</td>
                                          <td class="text-left">线路名称</td>
                                          <td class="text-left">默认司机</td>
                                          <td class="text-left">创建时间</td>
                                          <td class="text-left">状态</td>
                                          <td class="text-left">操作</td>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <!-- data -->
                                  </tbody>
                              </table>
                          </form>

                          <button type="button" class="btn btn-primary" style="margin-bottom: 5px; float: right" onclick="addLine();">添加</button>
                          <form method="post" enctype="multipart/form-data" id="form-line-add" class="form-horizontal">
                              <table id="line_add_row" style="display: none" class="table table-striped table-bordered table-hover">
                                  <thead>
                                      <tr>
                                          <td class="text-left">线路名称</td>
                                          <td class="text-left">默认司机</td>
                                          <td class="text-left">操作</td>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <tr>
                                          <td><input name="logistic_line_title" value="" /></td>
                                          <td><select name="default_logistic_driver_id" id="logistic_driver_id"><!-- Driver List --></select></td>
                                          <td><button type="button" class="btn btn-danger" onclick="addRow('form-line-add','line');">保存</button></td>
                                      </tr>
                                  </tbody>
                              </table>
                          </form>
                      </div>
                  </div>

                  <div class="tab-pane" id="tab-driver">
                      <div class="table-responsive">
                          <form method="post" enctype="multipart/form-data" id="form-driver-edit" class="form-horizontal">
                              <table id="driver_list" class="table table-striped table-bordered table-hover">
                                  <thead>
                                      <tr>
                                          <td class="text-left">编号</td>
                                          <td class="text-left">司机</td>
                                          <td class="text-left">电话</td>
                                          <td class="text-left">默认车辆</td>
                                          <td class="text-left">默认车辆型号</td>
                                          <td class="text-left">创建时间</td>
                                          <td class="text-left">状态</td>
                                          <td class="text-left">操作</td>
                                      </tr>
                                  </thead>
                                  <tbody>
                                  <!-- data -->
                                  </tbody>
                              </table>
                          </form>

                          <button type="button" class="btn btn-primary" style="margin-bottom: 5px; float: right" onclick="addDriver();">添加</button>
                          <form method="post" enctype="multipart/form-data" id="form-driver-add" class="form-horizontal">
                              <table id="driver_add_row" style="display: none" class="table table-striped table-bordered table-hover">
                                  <thead>
                                  <tr>
                                      <td class="text-left">司机</td>
                                      <td class="text-left">电话</td>
                                      <td class="text-left">默认车辆</td>
                                      <td class="text-left">操作</td>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr>
                                      <td><input name="logistic_driver_title" value="" /></td>
                                      <td><input name="logistic_driver_phone" value="" /></td>
                                      <td><select name="default_logistic_van_id" id="logistic_van_id"><!-- Van List --></select></td>
                                      <td><button type="button" class="btn btn-danger" onclick="addRow('form-driver-add','driver');">保存</button></td>
                                  </tr>
                                  </tbody>
                              </table>
                          </form>
                      </div>
                  </div>

                  <div class="tab-pane" id="tab-van">
                      <div class="table-responsive">
                          <form method="post" enctype="multipart/form-data" id="form-van-edit" class="form-horizontal">
                              <table id="van_list" class="table table-striped table-bordered table-hover">
                                  <thead>
                                      <tr>
                                          <td class="text-left">编号</td>
                                          <td class="text-left">车牌</td>
                                          <td class="text-left">车型</td>
                                          <td class="text-left">所有权</td>
                                          <td class="text-left">所有者</td>
                                          <td class="text-left">车辆联系</td>
                                          <td class="text-left">创建时间</td>
                                          <td class="text-left">状态</td>
                                          <td class="text-left">操作</td>
                                      </tr>
                                  </thead>
                                  <tbody>
                                  <!-- data -->
                                  </tbody>
                              </table>
                          </form>

                          <button type="button" class="btn btn-primary" style="margin-bottom: 5px; float: right" onclick="addVan();">添加</button>
                          <form method="post" enctype="multipart/form-data" id="form-van-add" class="form-horizontal">
                              <table id="van_add_row" style="display: none" class="table table-striped table-bordered table-hover">
                                  <thead>
                                  <tr>
                                      <td class="text-left">车牌</td>
                                      <td class="text-left">车型</td>
                                      <td class="text-left">所有权</td>
                                      <td class="text-left">所有者</td>
                                      <td class="text-left">车辆联系</td>
                                      <td class="text-left">操作</td>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr>
                                      <td><input name="logistic_van_title" value="" /></td>
                                      <td><input name="model" value="" /></td>
                                      <td><select name="ownership" id="ownership"><!-- Van List --></select></td>
                                      <td><input name="owner" value="" /></td>
                                      <td><input name="contact" value="" /></td>
                                      <td><button type="button" class="btn btn-danger" onclick="addRow('form-van-add','van');">保存</button></td>
                                  </tr>
                                  </tbody>
                              </table>
                          </form>
                      </div>
                  </div>

                  <div class="tab-pane" id="tab-deliveryman">
                      <div class="table-responsive">
                          <form method="post" enctype="multipart/form-data" id="form-deliveryman-edit" class="form-horizontal">
                              <table id="deliveryman_list" class="table table-striped table-bordered table-hover">
                                  <thead>
                                      <tr>
                                          <td class="text-left">编号</td>
                                          <td class="text-left">配送员</td>
                                          <td class="text-left">电话</td>
                                          <td class="text-left">创建时间</td>
                                          <td class="text-left">状态</td>
                                          <td class="text-left">操作</td>
                                      </tr>
                                  </thead>
                                  <tbody>
                                  <!-- data -->
                                  </tbody>
                              </table>
                          </form>

                          <button type="button" class="btn btn-primary" style="margin-bottom: 5px; float: right" onclick="addDeliveryman();">添加</button>
                          <form method="post" enctype="multipart/form-data" id="form-deliveryman-add" class="form-horizontal">
                              <table id="deliveryman_add_row" style="display: none" class="table table-striped table-bordered table-hover">
                                  <thead>
                                  <tr>
                                      <td class="text-left">配送员</td>
                                      <td class="text-left">电话</td>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr>
                                      <td><input name="logistic_deliveryman_title" value="" /></td>
                                      <td><input name="logistic_deliveryman_phone" value="" /></td>
                                      <td><button type="button" class="btn btn-danger" onclick="addRow('form-deliveryman-add','deliveryman');">保存</button></td>
                                  </tr>
                                  </tbody>
                              </table>
                          </form>
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
    </script>

    <script type="text/javascript">
        var global = {
            "rowData":{
                "line" : { },
                "driver" : { },
                "van" : { },
                "deliveryman" : { }
            },
            "returnErrorMsg": "添加失败，请检查数据是否重复，确保帐号登录状态未失效，以及网络是否正常。"
        };

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
                tag += '<button style="display: none" id="save_'+ type + '_' + id +'" type="button" class="btn btn-danger" onclick="saveRow(\''+ formId +'\',\''+ type +'\','+ id +')"><i class="fa fa-save"></i></button>';
            return tag;
        }

        function recallFunc(type){
            if(type == 'line'){ getLogisticLine(); }
            if(type == 'driver'){ getLogisticDriver(); }
            if(type == 'van'){ getLogisticVan(); }
            if(type == 'deliveryman'){ getLogisticDeliveryman(); }
        }

        function addRow(formId,type){
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_info/add&token=<?php echo $_SESSION["token"]; ?>',
                //dataType: 'json',
                data : {
                    type :type,
                    postData : $('#'+formId).serializeArray()
                },
                success: function(data){
                    console.log(data);
                    recallFunc(type);
                    $('#'+ type +'_add_row').hide();
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
                $.each($('#'+type+'_'+ i + ' .editable'), function(index,value){
                    targetRowName = $(this).attr("datatag")
                    targetRowValue = global.rowData[type][i][targetRowName];

                    $(this).html(targetRowValue);
                });

                $('#edit_'+type+'_'+i).show();
                $('#save_'+type+'_'+i).hide();

            });

            $.each($(targetRow+' .editable'), function(i,v){
                targetRowName = $(this).attr("datatag");
                targetRowValue = $(this).text();
                targetDataset = $(this).attr("dataset");

                if(targetDataset == 'driver'){
                    $(this).html('<select name="default_logistic_driver_id">'+ getDriverOptionList($(this).attr('value')) +'</select>');
                }
                else if(targetDataset == 'van'){
                    $(this).html('<select name="default_logistic_van_id">'+ getVanOptionList($(this).attr('value')) +'</select>');
                }
                else if(targetDataset == 'ownership'){
                    $(this).html('<select name="ownership">'+ getOwnershipOptionList($(this).attr('value')) +'</select>');
                }
                else if(targetDataset == 'status'){
                    $(this).html('<select name="status">'+ getStatusOptionList($(this).attr('value')) +'</select>');
                }
                else{
                    $(this).html('<input type="text" name="'+ targetRowName +'" value="'+ targetRowValue +'" />');
                }
            });

            $('#edit_'+type+'_'+id).hide();
            $('#save_'+type+'_'+id).show();
        }

        function saveRow(formId,type,id){
            $.ajax({
                type: 'POST',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_info/edit&token=<?php echo $_SESSION["token"]; ?>',
                data : {
                    type : type,
                    id : id,
                    postData : $('#'+formId).serializeArray()
                },
                success: function(data){
                    console.log(data);
                    recallFunc(type);
                },
                error: function(){
                    alert(global.returnErrorMsg);
                }
            });
        }

        function getDriverOptionList(selectedValue){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_info/getLogisticDriver&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(selectedValue == v.logistic_driver_id){
                            html += '<option value="'+ v.logistic_driver_id +'" selected="selected">'+ v.logistic_driver_title +'</option>';
                        }
                        else{
                            html += '<option value="'+ v.logistic_driver_id +'">'+ v.logistic_driver_title +'</option>';
                        }
                    });

                }
            });

            //console.log(html);
            return html;
        }

        function getVanOptionList(selectedValue){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=logistic/logistic_info/getLogisticVan&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(selectedValue == v.logistic_van_id){
                            html += '<option value="'+ v.logistic_van_id +'" selected="selected">'+ v.logistic_van_title +'['+ v.model +']['+ v.ownership + '-' + v.owner +']</option>';
                        }
                        else{
                            html += '<option value="'+ v.logistic_van_id +'">'+ v.logistic_van_title +'['+ v.model +']['+ v.ownership + '-' + v.owner +']</option>';
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

        function getOwnershipOptionList(selectedValue){
            var optionList = { 1:"公司",2:"承包商",3:"个人"};
            var html= '';

            $.each(optionList, function(i,v){
                var selected = '';
                if(selectedValue == v){
                    selected = 'selected="selected"';
                }
                html += '<option value="'+ v +'" '+ selected +'>'+ v +'</option>';
            });

            return html;
        }




        function addLine(){
            $('#line_add_row').show();
            var selectedValue = 0;
            var driverOptionsList = getDriverOptionList(selectedValue);

            $('#logistic_driver_id').html(driverOptionsList);
        }

        function addDriver(){
            $('#driver_add_row').show();
            var selectedValue = 0;
            var vanOptionsList = getVanOptionList(selectedValue);

            $('#logistic_van_id').html(vanOptionsList);
        }

        function addVan(){
            $('#van_add_row').show();
            var selectedValue = 0;
            var ownershipOptionsList = getOwnershipOptionList(selectedValue);

            $('#ownership').html(ownershipOptionsList);
        }

        function addDeliveryman(){
            $('#deliveryman_add_row').show();
        }




        function getLogisticLine(){
            $.ajax({
                type: 'GET',
                url: 'index.php?route=logistic/logistic_info/getLogisticLine&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    //console.log(data);
                    var html= '';
                    var num = 1;
                    $('#line_list tbody').html('');

                    $.each(data, function(i,v){
                        html += '<tr id="line_'+ v.logistic_line_id +'">';
                            html += '<td>'+ num++ +'</td>';
                            html += '<td>'+ v.logistic_line_title +'</td>';
                            html += '<td class="editable" dataset="driver" value="'+ v.logistic_driver_id +'" datatag="logistic_driver_title">'+ v.logistic_driver_title +'</td>';
                            html += '<td>'+ v.date_added +'</td>';
                            html += '<td class="editable" dataset="status" value="'+ v.status +'" datatag="status">'+ statusTag(v.status) +'</td>';
                            html += '<td>'+ operationTag("form-line-edit","line", v.logistic_line_id) +'</td>';
                        html += '</tr>';

                        global.rowData['line'][v.logistic_line_id] = v;
                    });

                    $('#line_list tbody').html(html);
                }
            });
        }

        function getLogisticDriver(){
            $.ajax({
                type: 'GET',
                url: 'index.php?route=logistic/logistic_info/getLogisticDriver&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    //console.log(data);
                    var html= '';
                    var num = 1;
                    $('#driver_list tbody').html('');

                    $.each(data, function(i,v){
                        html += '<tr id="driver_'+ v.logistic_driver_id +'">';
                            html += '<td>'+ num++ +'</td>';
                            html += '<td>'+ v.logistic_driver_title +'</td>';
                            html += '<td class="editable" datatag="logistic_driver_phone">'+ v.logistic_driver_phone +'</td>';
                            html += '<td class="editable" dataset="van" value="'+ v.logistic_van_id +'" datatag="logistic_van_title">'+ v.logistic_van_title +'</td>';
                            html += '<td>'+ v.model +'</td>';
                            html += '<td>'+ v.date_added +'</td>';
                            html += '<td class="editable" dataset="status" value="'+ v.status +'" datatag="status">'+ statusTag(v.status) +'</td>';
                            html += '<td>'+ operationTag("form-driver-edit","driver", v.logistic_driver_id) +'</td>';
                        html += '</tr>';

                        global.rowData['driver'][v.logistic_driver_id] = v;
                    });

                    $('#driver_list tbody').html(html);
                }
            });
        }

        function getLogisticVan(){
            $.ajax({
                type: 'GET',
                url: 'index.php?route=logistic/logistic_info/getLogisticVan&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    //console.log(data);
                    var html= '';
                    var num = 1;
                    $('#van_list tbody').html('');

                    $.each(data, function(i,v){
                        html += '<tr id="van_'+ v.logistic_van_id +'">';
                            html += '<td>'+ num++ +'</td>';
                            html += '<td>'+ v.logistic_van_title +'</td>';
                            html += '<td class="editable" datatag="model">'+ v.model +'</td>';
                            html += '<td class="editable" dataset="ownership" value="'+ v.ownership +'" datatag="ownership">'+ v.ownership +'</td>';
                            html += '<td class="editable" datatag="owner">'+ v.owner +'</td>';
                            html += '<td class="editable" datatag="contact">'+ v.contact +'</td>';
                            //html += '<td>'+ v.capacity +'</td>';
                            //html += '<td>'+ v.payload +'</td>';
                            html += '<td>'+ v.date_added +'</td>';
                            html += '<td class="editable" dataset="status" value="'+ v.status +'" datatag="status">'+ statusTag(v.status) +'</td>';
                            html += '<td>'+ operationTag("form-van-edit","van", v.logistic_van_id) +'</td>';
                        html += '</tr>';

                        global.rowData['van'][v.logistic_van_id] = v;
                    });

                    $('#van_list tbody').html(html);
                }
            });
        }

        function getLogisticDeliveryman(){
            $.ajax({
                type: 'GET',
                url: 'index.php?route=logistic/logistic_info/getLogisticDeliveryman&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    var html= '';
                    var num = 1;
                    $('#deliveryman_list tbody').html('');

                    $.each(data, function(i,v){
                        html += '<tr id="deliveryman_'+ v.logistic_deliveryman_id +'">';
                            html += '<td>'+ num++ +'</td>';
                            html += '<td>'+ v.logistic_deliveryman_title +'</td>';
                            html += '<td class="editable" datatag="logistic_deliveryman_phone">'+ v.logistic_deliveryman_phone +'</td>';
                            html += '<td>'+ v.date_added +'</td>';
                            html += '<td class="editable" dataset="status" value="'+ v.status +'" datatag="status">'+ statusTag(v.status) +'</td>';
                            html += '<td>'+ operationTag("form-deliveryman-edit","deliveryman", v.logistic_deliveryman_id) +'</td>';
                        html += '</tr>';

                        global.rowData['deliveryman'][v.logistic_deliveryman_id] = v;
                    });

                    $('#deliveryman_list tbody').html(html);
                }
            });
        }

    </script>
</div>
<?php echo $footer; ?> 