<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip"
                                       title="<?php echo $button_add; ?>" class="btn btn-primary"><i
                            class="fa fa-plus"></i></a>
                <button  type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>"
                        class="btn btn-danger"
                        onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-promotion').submit() : false;"><i
                            class="fa fa-trash-o"></i></button>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-promotion">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <td style="width: 1px;" class="text-center"><input type="checkbox"
                                                                                   onclick="$('input[name*=\'selected\']').prop('checked', this.checked);"/>
                                </td>
                                <td>编号</td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_type; ?>"
                                       class=" <?php echo $sort == 'type'? strtolower($order):''; ?> "><?php echo $column_type; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_title;?>" class=" <?php echo $sort == 'title'? strtolower($order):''; ?> "><?php echo $column_title; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_station; ?>"
                                       class=" <?php echo $sort == 'station'? strtolower($order):''; ?> "><?php echo $column_station; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_firstorder; ?>"
                                       class=" <?php echo $sort == 'firstorder'? strtolower($order):''; ?> "><?php echo $column_firstorder; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_overlap; ?>"
                                       class=" <?php echo $sort == 'overlap'? strtolower($order):''; ?> "><?php echo $column_overlap; ?></a>
                                </td>

                                <td class="text-center">
                                    <a href="<?php echo $sort_min_cart_total; ?>"
                                       class=" <?php echo $sort == 'min_cart_total'? strtolower($order):''; ?> "><?php echo $column_min_cart_total; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_max_cart_total; ?>"
                                       class=" <?php echo $sort == 'max_cart_total'? strtolower($order):''; ?> "><?php echo $column_max_cart_total; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_disount_fixed; ?>"
                                       class=" <?php echo $sort == 'disount_fixed'? strtolower($order):''; ?> "><?php echo $column_disount_fixed; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_discount; ?>"
                                       class=" <?php echo $sort == 'discount'? strtolower($order):''; ?> "><?php echo $column_discount; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_date_start; ?>"
                                       class=" <?php echo $sort == 'date_start'? strtolower($order):''; ?> "><?php echo $column_date_start; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_date_end; ?>"
                                       class=" <?php echo $sort == 'date_end'? strtolower($order):''; ?> "><?php echo $column_date_end; ?></a>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $sort_status; ?>"
                                       class=" <?php echo $sort == 'status'? strtolower($order):''; ?> "><?php echo $column_status; ?></a>
                                </td>


                                <td class="text-center">
                                    操作
                                </td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php  if(empty($promotions)){
                                echo '<td class="text-center" colspan="15">'.$text_no_results.'</td>';
                            }else{ ?>
                            <?php foreach($promotions as $promotion){ ?>
                            <tr>
                                <td class="text-center"><?php if (in_array($promotion['promotion_id'], $selected)) { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $promotion['promotion_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $promotion['promotion_id']; ?>" />
                                    <?php } ?></td>
                                <td class="text-center"><?php echo $promotion['promotion_id']; ?></td>
                                <td class="text-center"><?php echo $promotion['type']; ?></td>
                                <td class="text-center">
                                    <p>
                                        <?php echo $promotion['title']; ?>
                                    </p>
                                    <?php echo empty($promotion['desc']) ? '': '<p class="text-left">'.$column_desc.':'.$promotion['desc']. '</p>'?>



                                </td>
                                <td class="text-center"><?php echo $promotion['station']; ?></td>
                                <td class="text-center"><?php echo $promotion['firstorder']; ?></td>
                                <td class="text-center"><?php echo $promotion['overlap']; ?></td>

                                <td class="text-center"><?php echo $promotion['min_cart_total']; ?></td>
                                <td class="text-center"><?php echo $promotion['max_cart_total']; ?></td>
                                <td class="text-center"><?php echo $promotion['disount_fixed']; ?></td>
                                <td class="text-center"><?php echo $promotion['discount']; ?></td>
                                <td class="text-center"><?php echo $promotion['date_start']; ?></td>
                                <td class="text-center"><?php echo $promotion['date_end']; ?></td>
                                <td class="text-center"><?php echo $promotion['status']; ?></td>

                                <td class="text-center"><a href="<?php echo $promotion['edit']; ?>" data-toggle="tooltip"
                                                          title="<?php echo $button_edit; ?>" class="btn btn-primary"><i
                                                class="fa fa-pencil"></i></a></td>
                            </tr>
                            <?php }?>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>