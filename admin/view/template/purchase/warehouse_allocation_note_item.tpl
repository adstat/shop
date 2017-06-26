<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="仓库出库单" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            </div>
            <h1>出库单明细</h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>
            <div class="panel-body">
<div><?php if ($item) { ?>
    出库单号： <?php echo $item[0]['relevant_id'] ;?>
    出库单类型：<?php echo $item[0]['out_type'] ;?>
    调往仓库： <?php echo $item[0]['title'] ;?>
<?php } ?></div>
                <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <td class="text-left">商品ID</td>
                                <td class="text-left">商品名称</td>
                                <td class="text-left">货位号</td>
                                <td class="text-left">仓库数量</td>
                                <td class="text-left">调拨数量</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($item as $val) { ;?>
                            <tr>
                                <td class="text-left"><?php echo $val['product_id'] ;?></td>
                                <td class="text-left"><?php echo $val['product_name'] ;?></td>
                                <td class="text-left"><?php echo $val['product_section_title'] ;?></td>
                                <td class="text-left"><?php echo $val['inventory'] ;?></td>
                                <td class="text-left"><?php echo $val['num'] ;?></td>
                            </tr>
                            <?php }?>

                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript"><!--
    $('.date').datetimepicker({
        pickTime: false
    });
    //--></script>


