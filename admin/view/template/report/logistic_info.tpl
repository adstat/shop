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
    <div class="container-fluid" id="productsale">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
            </div>
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-date-start">配送开始日期</label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="input-date-start">配送结束日期</label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" >物流司机</label>
                                <select name="filter_logistic_driver_list" id="input_logistic_driver_list" class="form-control">
                                    <option value='0'>全部</option>
                                    <?php foreach ($logistic_driver_list as $val) { ?>
                                    <?php if ($val['logistic_driver_id'] == $filter_logistic_driver_list) { ?>
                                    <option value="<?php echo $val['logistic_driver_id']; ?>" selected="selected"><?php echo $val['logistic_driver_title']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['logistic_driver_id']; ?>" ><?php echo $val['logistic_driver_title']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                <?php if($date_gap > 31) { ?>
                <div class="alert alert-warning">
                    查询时间不可大于31天!
                </div>
                <?php } ?>

                <?php if(!$nofilter) { ?>
                <div class="table-responsive">
                    <button type="button" id="button-export" class="btn btn-primary pull-right" style="margin:0 5px;"><i class="fa fa-bar-chart"></i>导出EXCEL</button>
                </div>
                <?php } ?>

            </div>

            <?php if(!$nofilter) { ?>
            <div  class="table-responsive">
                <table class="table table-bordered" id="logistics">
                    <thead>
                        <tr>
                            <th>订单号</th>
                            <th>仓库平台</th>
                            <th>商家</th>
                            <th>收货地址</th>
                            <th>订单金额</th>
                            <th>BD人员</th>
                            <th>司机</th>
                            <th>物流评分</th>
                            <th>到货核对</th>
                            <th>单据签字</th>
                            <th>周转箱使用</th>
                            <th>用户投诉</th>
                            <th>事项记录</th>
                            <th>用户建议</th>
                            <th>投诉时间</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(sizeof($logistics)){

                    foreach($logistics as $logistic){
                    ?>
                     <tr>
                         <td><?php echo $logistic['order_id'];?></td>
                         <td><?php echo $logistic['name'];?></td>
                         <td><?php echo $logistic['shipping_firstname'];?></td>
                         <td><?php echo $logistic['shipping_address_1'];?></td>
                         <td><?php echo $logistic['total'];?></td>
                         <td><?php echo $logistic['bd_name'];?></td>
                         <td><?php echo $logistic['logistic_driver_title'];?></td>
                         <td><?php echo $logistic['logistic_score'];?></td>
                         <?php if(!$logistic['cargo_check']) { ; ?>
                         <td>无记录</td>
                         <?php } ;?>
                         <?php if($logistic['cargo_check'] == 1) { ;?>
                         <td>整件清点,散件未清点</td>
                         <?php }; ?>
                         <?php if($logistic['cargo_check'] == 2){ ;?>
                         <td>整散件均当场清点</td>
                         <?php }; ?>
                         <?php if($logistic['cargo_check'] == 3){ ; ?>
                         <td>没有清点货物</td>
                         <?php } ;?>
                         <?php if(!$logistic['bill_of']){ ; ?>
                         <td>无记录</td>
                         <?php } ;?>
                         <?php if($logistic['bill_of'] == 1) { ;?>
                         <td>有</td>
                         <?php } ;?>
                         <?php if($logistic['bill_of'] == 2) { ;?>
                         <td>无</td>
                         <?php } ;?>
                         <?php if(!$logistic['box']) { ;?>
                         <td>无记录</td>
                         <?php } ;?>
                         <?php if($logistic['box'] ==1){ ;?>
                         <td>是</td>
                         <?php } ;?>
                         <?php if($logistic['box'] ==2){ ;?>
                         <td>否</td>
                         <?php } ;?>
                         <?php if($logistic['box'] ==3){ ;?>
                         <td>没有散件商品</td>
                         <?php } ;?>
                         <td><?php echo $logistic['checkname'];?></td>
                         <td><?php echo $logistic['comments']; ?></td>
                         <td><?php echo $logistic['user_comments'];?></td>
                         <td><?php echo $logistic['date_added'] ;?></td>
                     </tr>
                    <?php
                    }
                    }else{
                    ?>
                    <tr>
                        <td class="text-center" colspan="14"><?php echo $text_no_results; ?></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <table  class="table table-bordered" id="logistics">
                    <tr>
                        <td>投诉：
                             <?php if(!empty($tmp)){ ?>
                             <?php foreach($tmp as $k =>$v){ ?>
                             <?php echo $k.':'.$v .'次'?>
                             <?php }?>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    $('.date').datetimepicker({
        pickTime: false
    });
</script>
<script>
    $('#button-filter').on('click', function() {
        location = getUrl();
    });
    $('#button-export').on('click',function() {
        url = getUrl();
        url += '&export=1';
        location = url;
    });
    function getUrl() {
        url = 'index.php?route=report/logistic_info&token=<?php echo $token; ?>';
        var filter_date_start = $('input[name=\'filter_date_start\']').val();
        if (filter_date_start) {
            url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
        }

        var filter_date_end = $('input[name=\'filter_date_end\']').val();
        if (filter_date_end) {
            url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
        }

        var filter_logistic_driver_list = $('select[name=\'filter_logistic_driver_list\']').val();

        if (filter_logistic_driver_list !=0) {
            url += '&filter_logistic_driver_list=' + encodeURIComponent(filter_logistic_driver_list);
        }
        return url;
    }

</script>
<?php echo $footer; ?>