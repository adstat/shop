<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            </div>
            <h1>仓库货位管理</h1>
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
                <h3 class="panel-title"><i class="fa fa-list"></i> 货位列表</h3>
            </div>
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">选择平台</label>
                                <select name="filter_station_id" id="input_station_id" class="form-control">
                                    <option value='0'>全部</option>
                                    <?php foreach ($stations as $val) { ?>
                                    <?php if ($val['station_id'] == $filter_station_id) { ?>
                                    <option value="<?php echo $val['station_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['station_id']; ?>" ><?php echo $val['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">选择仓库区域</label>
                                <select name="filter_station_section_type_id" id="input_station_section_type_id" class="form-control">
                                    <option value='0'>全部</option>
                                    <?php foreach ($stations_section as $val) { ?>
                                    <?php if ($val['station_section_type_id'] == $filter_station_section_type_id) { ?>
                                    <option value="<?php echo $val['station_section_type_id']; ?>" selected="selected"><?php echo $val['name']; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $val['station_section_type_id']; ?>" ><?php echo $val['name']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">过滤分区区号</label>
                                <input type="text" id="tree_value" class="form-control"  name="filter_station_section_title" value="<?php echo $filter_station_section_title ;?>" nameid="" onclick="showtree();"> </input>
                                <ul id="treeDemo" class="ztree" style="display:none;"></ul>
                            </div>
                        </div>
                        <div>
                            <label class="control-label">隐藏分区区号</label>
                            <div>
                            <button type="button" class="btn btn-default radius  yin"  ><i class="icon-ok"></i> 隐藏区域显示</button>
                            </div>
                        </div>


                    </div>
                </div>
                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                <div class="table-responsive">
                    <button type="button" id="button-export" class="btn btn-primary pull-right" style="margin:0 5px;"><i class="fa fa-bar-chart"></i>导出EXCEL</button>
                </div>
            </div>
            <?php if(!$nofilter) { ?>
            <form method="post" enctype="multipart/form-data" id="form-plist-edit" class="form-horizontal">
            <div class="table-responsive">
                <table class="table table-bordered" id="totals">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>仓库分区号</th>
                        <th>商品ID</th>
                        <th>商品名称</th>
                        <th>仓库平台</th>
                        <th>仓库区域</th>
                        <th>排序</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($productsections) { ?>
                    <?php foreach ($productsections as $productsection) { ?>
                     <tr id="plist_<?php echo $productsection['station_section_id']; ?>">
                         <td class="text-left"><?php echo $productsection['station_section_id']; ?></td>
                         <td class="text-left"><?php echo $productsection['station_section_title']; ?></td>
                         <td class="text-left"><?php echo $productsection['product_id']; ?>
                                               <!--  <?php echo $productsection['sku'] ; ?> </td> -->
                         <td class="text-left"><?php echo $productsection['product_name']; ?>
                                               <!--     <?php echo $productsection['sku_id'] ;?> </td> -->
                         <td class="text-left"><?php echo $productsection['station_id']; ?></td>
                         <td class="text-left"><?php echo $productsection['sectionname']; ?></td>
                         <td class="text-left editable" datatag="sort"><?php echo $productsection['sort']; ?> </td>

                         <td class="text-right">
                             <a href="<?php echo $productsection['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                             <button type="button" class="btn btn-primary" id="edit_plist_<?php echo $productsection['station_section_id']; ?>" onclick="editRow('plist',<?php echo $productsection['station_section_id']; ?>)">排序</button>
                             <button style="display: none" id="save_plist_<?php echo $productsection['station_section_id']; ?>" type="button" class="btn btn-danger" onclick="updateRow('form-plist-edit','plist',<?php echo $productsection['station_section_id']; ?>)">保存</button>
                         </td>

                     </tr>
                    <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            </form>
            <?php } ?>

            <div class="row">
                <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                <div class="col-sm-6 text-right"><?php echo $results; ?></div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#button-filter').on('click', function() {
        location = getUrl();
    });
    $('#button-export').on('click',function() {
        url = getUrl();
        url += '&export=1';
        location = url;
    });

    function getUrl(){
        var warehouse_id = $("#warehouse_id_global").val();
        url = 'index.php?route=user/warehouse_management&token=<?php echo $token; ?>&filter_warehouse_id_global='+ warehouse_id;

        var filter_station_id = $('select[name=\'filter_station_id\']').val();

        if(filter_station_id) {
            url += '&filter_station_id=' + encodeURIComponent(filter_station_id);
        }

        var filter_station_section_type_id = $('select[name=\'filter_station_section_type_id\']').val();
        if(filter_station_section_type_id) {
            url += '&filter_station_section_type_id=' + encodeURIComponent(filter_station_section_type_id);
        }

        var filter_station_section_title = $('input[name=\'filter_station_section_title\']').val();
        if(filter_station_section_title) {
            url += '&filter_station_section_title=' + encodeURIComponent(filter_station_section_title);
        }
        return url;
    }
</script>
<script>

    var global = {
        "rowData":{
            "plist" : {}
        }
    };


    function editRow(type, id){

        var targetRow = '#'+type+'_'+id;
        var targetRowName = '';
        var targetRowValue = '';
        refresh(type);

        $.each($(targetRow+' .editable'), function(i,v){

            targetRowName = $(this).attr("datatag");
            targetRowValue = $(this).text();

            $(this).html('<input id="input_text" type="text" name="'+ targetRowName +'" value="'+ targetRowValue +'" />');
        });

        $('#edit_'+type+'_'+id).hide();
        $('#save_'+type+'_'+id).show();

    }


    function refresh(type){

        $.each(global.rowData[type], function(i,v){
            $.each($('#'+type+'_'+ i + ' .editable'), function(index,value){
                targetRowName = $(this).attr("datatag")
                targetRowValue = global.rowData[type][i][targetRowName];
                $(this).html(targetRowValue);
            });

            $('#edit_'+type+'_'+i).show();
            $('#save_'+type+'_'+i).hide();

        });

    }

    function updateRow(formId,type,id){
        $.ajax({
            type:'POST',
            async: false,
            cache: false,
            url: 'index.php?route=user/warehouse_management/updateSort&token=<?php echo $_SESSION["token"]; ?>',
            data : {
                type : type,
                id : id,
                postData : $('#'+formId).serializeArray()
            },
            success: function(data){

                if(data == 'true'){
                    refresh(type);
                    $('#edit_'+type+'_'+id).show();
                    $('#save_'+type+'_'+id).hide();

                } else{
                    alert(global.returnErrorMsg);
                }
                },
                error: function(){
                    alert(global.returnErrorMsg);
                }

        });
    }

</script>






<script type="text/javascript" src="view/javascript/zTree/jquery.ztree.core-3.5.min.js"></script>



<script>
    var setting = {
        view: {
            dblClickExpand: false,//双击节点时，是否自动展开父节点的标识

        },

        data: {
            simpleData: { //简单数据模式
            enable:true,
            idKey: "id",
            pIdKey: "pId",
            rootPId: ""
            }
        },
        callback:{
            onClick:onClick,
            beforeClick: function(treeId, treeNode) {
                zTree = $.fn.zTree.getZTreeObj("treeDemo");
                if (treeNode.isParent) {
                    zTree.expandNode(treeNode);//如果是父节点，则展开该节点
                }
            }


        }

    };

   function showtree(){
                  $("#treeDemo").show();
                    $.getJSON('index.php?route=user/warehouse_management/GetCaseType&token=<?php echo $_SESSION["token"]; ?>', function(data){ //去后台获取到所有权限信息 用于构造zTree树
                        if (null != data) {
                            treeNodes = data;//把后台封装好的简单Json格式赋给treeNodes
                            var t = $("#treeDemo");
                            console.log(treeNodes);
                            t = $.fn.zTree.init(t, setting, treeNodes);
                        }
              });
    };

    $(".yin").click(function(){
                    $("#treeDemo").hide();
                 });


    function onClick(e, treeId, treeNode) {
        var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
                nodes = zTree.getSelectedNodes(),
                v = "";
        nodes.sort(function compare(a, b) { return a.id - b.id; });
        for (var i = 0, l = nodes.length; i < l; i++) {
            v += nodes[i].name + ",";
        }
        if (v.length > 0) v = v.substring(0, v.length - 1);
        var nameObj = $("#tree_value");
        nameObj.attr("value", v);
        var n = "";
        for (var i = 0, l = nodes.length; i < l; i++) {
            n += nodes[i].id + ",";
        }
        if (n.length > 0) n = n.substring(0, n.length - 1);
        nameObj.attr("nameid", n);

    }

</script>
