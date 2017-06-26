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
                <li class="active"><a href="#tab-special" data-toggle="tab"><i class="fa fa-info"></i> 验证码查询</a></li>
                <li><a href="#tab-area" data-toggle="tab" onclick="getQueryHistory();"><i class="fa fa-gear"></i> 查询记录</a></li>
              </ul>

              <div class="tab-content">
                  <div class="tab-pane active" id="tab-special">
                      <div class="table-responsive">
                          <div class="alert alert-info">选择BD,输入手机号查询，每次查询均会记录，验证码生成后15分钟过期，遇到验证码过期需要注册用户重新获取，再次查询。</div>
                          <table id="area_add_row" class="table table-striped table-bordered table-hover">
                              <tbody>
                              <tr>
                                  <td>
                                      申请BD: <select name="bd_id" id="bd_id"></select>&nbsp;&nbsp;&nbsp;
                                      查询电话号码: <input name="telephone" id="telephone" value="" placeholder="" />&nbsp;&nbsp;&nbsp;
                                      <button type="button" class="btn btn-primary smallbtn" onclick="getSmsPin();">查询</button>
                                  </td>
                                  <td>
                                      验证码: <span id="smspin_code" style="background-color: #66CC66; color:#5e5e5e; padding:3px; width:50px; font-size: 14px; font-weight: bold;">0</span>&nbsp;&nbsp;&nbsp;
                                      过期时间: <span id="smspin_exp" style="padding:3px; width:50px; font-size: 14px; font-weight: bold;">0</span>
                                  </td>
                              </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>

                  <div class="tab-pane" id="tab-area">
                      <div class="table-responsive">
                          <table id="query_history_list" class="table table-striped table-bordered table-hover">
                              <thead>
                                  <tr>
                                      <td class="text-left">市场人员</td>
                                      <td class="text-left">查询手机</td>
                                      <td class="text-left">验证码</td>
                                      <td class="text-left">过期时间</td>
                                      <td class="text-left">查询用户</td>
                                      <td class="text-left">查询日期</td>
                                  </tr>
                              </thead>
                              <tbody>
                                  <!-- data -->
                              </tbody>
                          </table>
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
        $.onload(
            $('#bd_id').html(getBdOptionList())
        );

        function getSmsPin(){
            var bd_id = $('#bd_id').val();
            var telephone = $('#telephone').val();

            $('#smspin_code').html('-');
            $('#smspin_exp').html('-');

            if(bd_id > 0 && telephone.length == 11){
                $.ajax({
                    type: 'POST',
                    async: false,
                    cache: false,
                    url: 'index.php?route=marketing/smspin_query/getSmsPin&token=<?php echo $_SESSION["token"]; ?>',
                    data : {
                        bd_id : bd_id,
                        telephone : telephone
                    },
                    success: function(data){

                        console.log(data);

                        if(data.length == 2){
                            alert('未找到记录');
                        }
                        else{
                            var json = $.parseJSON(data);

                            $('#smspin_code').html(json.code);
                            $('#smspin_exp').html(json.expiration);
                        }
                    }
                });
            }
            else{
                alert('检查BD和电话格式[11位]');
            }
        }

        function getBdOptionList(){
            var html= '';
            $.ajax({
                type: 'GET',
                async: false,
                cache: false,
                url: 'index.php?route=marketing/smspin_query/getBdList&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    html += '<option value=0>-</option>';
                    $.each(data, function(i,v){
                        if(v.status == '1'){
                            html += '<option value="'+ v.bd_id +'">'+ v.bd_name +'</option>';
                        }
                    });

                }
            });

            //console.log(html);
            return html;
        }

        function getQueryHistory(){
            $.ajax({
                type: 'GET',
                url: 'index.php?route=marketing/smspin_query/getQueryHistory&token=<?php echo $_SESSION["token"]; ?>',
                dataType: 'json',
                success: function(data){
                    console.log(data);

                    $('#query_history_list tbody').html('');
                    var html= '';

                    $.each(data, function(i,v){
                            html += '<tr>';
                            html += '<td>'+ v.bd_name +'</td>';
                            html += '<td>'+ v.telephone +'</td>';
                            html += '<td>'+ v.code +'</td>';
                            html += '<td>'+ v.expiration +'</td>';
                            html += '<td>'+ v.username +'</td>';
                            html += '<td>'+ v.date_added +'</td>';
                            html += '</tr>';
                    });

                    $('#query_history_list tbody').html(html);

                }
            });
        }
    </script>
</div>
<?php echo $footer; ?> 