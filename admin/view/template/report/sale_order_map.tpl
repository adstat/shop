<?php echo $header; ?>
<style type="text/css">
    body, html{width: 100%;height: 100%;margin:0;font-family:"微软雅黑";}
    #l-map{height:650px;width:100%;}
    #r-result{width:100%; font-size:14px;line-height:20px;}
</style>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=TkbDdiAKKOmHBuHDMeHQk0eO"></script>

<div id="content" style="padding-bottom:0;">
<div class="page-header" style="display: none">
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
    <div class="panel panel-default" style="margin-bottom: 3px;">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
        </div>
        <div class="panel-body">
            <div style="margin:3px 0px; border-radius: 3px; background-color: #ECF3E6; border-color: #E3EBD5; padding: 5px;">
                <span style="color: #CC0000; font-size: 120%; font-weight: bold">选择日期类型: </span>
                <input type="radio" name="filter_datatype" <?php if($filter_datatype !== 1){ echo 'checked="checked"'; } ?> value=2 /> 下单日期
                <input type="radio" name="filter_datatype" <?php if($filter_datatype == 1){ echo 'checked="checked"'; } ?> value=1 /> 配送日期 &nbsp;&nbsp;&nbsp;
            </div>
            <div class="well">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label" for="input-date-start">开始日期</label>
                            <div class="input-group date">
                                <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label" for="input-date-end">结束日期</label>
                            <div class="input-group date">
                                <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">订单状态</label>
                            <select name="filter_order_status" id="input_order_status" class="form-control">
                                <option value='0'>全部(不含取消)</option>
                                <?php foreach ($order_status as $val) { ?>
                                <?php if ($val['id'] == $filter_order_status) { ?>
                                <option value="<?php echo $val['id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $val['id']; ?>" ><?php echo $val['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">支付状态</label>
                            <select name="filter_order_payment_status" id="input_order_payment_status" class="form-control">
                                <option value='0'>全部</option>
                                <?php foreach ($order_payment_status as $val) { ?>
                                <?php if ($val['id'] == $filter_order_payment_status) { ?>
                                <option value="<?php echo $val['id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $val['id']; ?>" ><?php echo $val['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">市场开发(BD)人员</label>
                            <select name="filter_bd_list" id="input_bd_list" class="form-control">
                                <option value='0'>全部</option>
                                <?php foreach ($bd_list as $val) { ?>
                                <?php if ($val['id'] == $filter_bd_list) { ?>
                                <option value="<?php echo $val['id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $val['id']; ?>" ><?php echo $val['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="control-label">用户ID</label>
                            <input type="text" name="filter_customer_id" id="input_customer_id"  value="<?php echo $filter_customer_id; ?>" placeholder="23,664,665,..." class="form-control" />
                            </span></div>
                    </div>
                </div>

                <button type="button" id="button-map" class="btn btn-primary pull-right" style="margin:0 5px;"><i class="fa fa-map-marker"></i> 商家地图</button>
                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>

            <?php if($date_gap > 31) { ?>
            <div class="alert alert-warning">
                查询时间不可大于31天!
            </div>
            <?php } ?>
        </div>
    </div>
</div>
</div>
<div id="l-map"></div>
<script type="text/javascript"><!--
    $('#button-filter').on('click', function() {
        location = getUrl();
    });

    var showmap = 1;
    $('#button-map').on('click', function() {
        url = getUrl();
        url += '&showmap=1';

        if(showmap){
            location = url;
        }
        else{
            window.open(url,"_blank");
        }
    });

    function getUrl(){
        url = 'index.php?route=report/sale_order&token=<?php echo $token; ?>';

        var filter_datatype = $('input[name=\'filter_datatype\']').val();

        if (filter_datatype) {
            url += '&filter_datatype=' + encodeURIComponent(filter_datatype);
        }

        var filter_date_start = $('input[name=\'filter_date_start\']').val();

        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').val();

        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }

        var filter_order_status = $('select[name=\'filter_order_status\']').val();

        if (filter_order_status  != 0) {
            url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
        }

        var filter_order_payment_status = $('select[name=\'filter_order_payment_status\']').val();

        if (filter_order_payment_status != 0) {
            url += '&filter_order_payment_status=' + encodeURIComponent(filter_order_payment_status);
        }

        var filter_bd_list = $('select[name=\'filter_bd_list\']').val();

        if (filter_bd_list != 0) {
            url += '&filter_bd_list=' + encodeURIComponent(filter_bd_list);
        }

        var filter_customer_id = $('input[name=\'filter_customer_id\']').val();

        if (filter_customer_id) {
            url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
        }

        return url;
    }


//--></script>
<script type="text/javascript"><!--
    $('.date').datetimepicker({
        pickTime: false
    });
    //--></script>
<br />
<?php echo $footer; ?>
<script type="text/javascript">
    // 百度地图API功能
    var map = new BMap.Map("l-map");
    map.centerAndZoom(new BMap.Point(121.491, 31.233), 13);
    //map.addControl(new BMap.MapTypeControl());
    map.addControl(new BMap.NavigationControl());
    map.enableScrollWheelZoom(false);
    map.setCurrentCity("上海");

    var opts = {
        width : 260,     // 信息窗口宽度
        height: 150,     // 信息窗口高度
        title : "<b>鲜世纪商家版订单信息</b>" , // 信息窗口标题
        enableMessage:true//设置允许信息窗发送短息
    };

    var index = 0;
    var myGeo = new BMap.Geocoder();
    var adds = [
          <?php
            foreach($orders as $order){
                echo '["'. $order['order_address'] .'","'.$order['order_id'].'","'.$order['merchant_name'].'('.$order['firstname'].')","'.$order['deliver_date'].'","'.round($order['sub_total'],2).'","'.$order['bd_name'].'","'.$order['bd_phone'].'"],';
            }
          ?>
    ];

    function bdGEO(){
        var add = adds[index];
        geocodeSearch(add);
        index++;
    }

    function geocodeSearch(add){
        if(index < adds.length){
            setTimeout(window.bdGEO,10);
        }
        myGeo.getPoint(add[0], function(point){
            if (point) {
                //document.getElementById("result").innerHTML +=  index + "、" + add + ":" + point.lng + "," + point.lat + "</br>";
                var address = new BMap.Point(point.lng, point.lat);
                //addMarker(address,new BMap.Label(add[1]+":"+add[2],{offset:new BMap.Size(20,-10)}));

                var marker = new BMap.Marker(address);
                var content = "订单号:"+add[1]+"<br />商家名称:"+add[2]+"<br />地址:"+add[0]+"<br />配送日期:"+add[3]+"<br />订单金额:"+add[4]+"<br /><br />BD人员:"+add[5]+"<br />地址:"+add[0];
                map.addOverlay(marker);
                addClickHandler(content,marker);
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

    bdGEO();
</script>