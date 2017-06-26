<form id="feadbackhistory">
<table class="table table-bordered">
    <thead>
    <tr>
        <td class="text-left"><?php echo $column_feadback_station; ?></td>
        <td class="text-left"><?php echo $column_feadback_date; ?></td>
        <td class="text-left">商家</td>
        <td class="text-left">订单金额</td>
        <td class="text-left">BD人员</td>
        <td class="text-left"><?php echo $column_feadback_driver; ?></td>
        <td class="text-left">反馈选项</td>
        <td class="text-left">物流评分</td>
        <td class="text-left">到货核对</td>
        <td class="text-left">单据签字</td>
        <td class="text-left">周转箱使用</td>
        <td class="text-left">事项记录</td>
        <td class="text-left">用户建议</td>
        <td class="text-left">添加日期</td>
    </tr>
    </thead>
    <tbody>
    <?php if ($feadbacks) { ?>
    <?php foreach ($feadbacks as $feadback) { ?>
    <tr>
        <td class="text-left"><?php echo $feadback['name']; ?></td>
        <td class="text-left" id="feadback_date" value="<?php echo $feadback['date_added']; ?>"><?php echo $feadback['date_added']; ?></td>
        <td class="text-left"><?php echo $feadback['shipping_name']; ?></td>
        <td class="text-left"><?php echo $feadback['total']; ?></td>
        <td class="text-left"><?php echo $feadback['bd_name']; ?></td>
        <td class="text-left"><?php echo $feadback['logistic_driver_title']; ?></td>
        <td class="text-left"><?php echo $feadback['feadback_options']; ?></td>
        <td class="text-left"><?php echo $feadback['logistic_score'];?></td>
        <?php if(!$feadback['cargo_check']) { ; ?>
        <td class="text-left">无记录</td>
        <?php } ;?>
        <?php if($feadback['cargo_check'] == 1) { ;?>
        <td class="text-left">整件清点,散件未清点</td>
        <?php }; ?>
        <?php if($feadback['cargo_check'] == 2){ ;?>
        <td class="text-left">整散件均当场清点</td>
        <?php }; ?>
        <?php if($feadback['cargo_check'] == 3){ ; ?>
        <td class="text-left">没有清点货物</td>
        <?php } ;?>
        <?php if(!$feadback['bill_of']){ ; ?>
        <td class="text-left">无记录</td>
        <?php } ;?>
        <?php if($feadback['bill_of'] == 1) { ;?>
        <td class="text-left">有</td>
        <?php } ;?>
        <?php if($feadback['bill_of'] == 2) { ;?>
        <td class="text-left">无</td>
        <?php } ;?>
        <?php if(!$feadback['box']) { ;?>
        <td class="text-left">无记录</td>
        <?php } ;?>
        <?php if($feadback['box'] ==1){ ;?>
        <td class="text-left">是</td>
        <?php } ;?>
        <?php if($feadback['box'] ==2){ ;?>
        <td class="text-left">否</td>
        <?php } ;?>
        <?php if($feadback['box'] ==3){ ;?>
        <td class="text-left">没有散件商品</td>
        <?php } ;?>
        <td class="text-left"><?php echo $feadback['comments']; ?></td>
        <td class="text-left"><?php echo $feadback['user_comments'];?></td>
        <td class="text-left"><?php echo $feadback['record_date']; ?></td>
    </tr>
    <?php } ?>
    <?php } else { ?>

    <?php } ?>

    </tbody>
</table>
</form>