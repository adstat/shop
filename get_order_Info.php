<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/30
 * Time: 11:53
 */
header("Content-type:text/html;charset=utf8");
require_once '../api/config.php';
require_once(DIR_SYSTEM.'db.php');
/*取出所有的仓库*/
    if (!empty($_GET['go_date'])){
        $go_date=$_GET['go_date'];
        $to_date=$_GET['to_date'];
        $do_host=$_GET['go_host'];
        $to_host=$_GET['to_host'];
        $opt=$_GET['opt'];
        switch ($opt){
            case $opt == 'fen':
                $sql="
                SELECT
                    A.deliver_date delivery_date,
                    A.warehouse target_house,
                    A.do_warehouse do_house,
                    count(DISTINCT A.deliver_order_id) sort_num,
                IF (
                    A.warehouse_id IN (12, 14),
                IF (A.is_repack = 0, '整', '散'),
                 '-'
                ) sort_type,
                 sum(

                    IF (
                        A.order_status_id IN (1, 2),
                        1,
                        0
                    )
                ) null_sort_num,
                 sum(

                    IF (A.order_status_id = 4, 1, 0)
                ) already_allot,
                 sum(

                    IF (A.order_status_id = 5, 1, 0)
                ) sorting_num,
                 sum(

                    IF (A.order_status_id = 8, 1, 0)
                ) to_audit_num,
                 sum(

                    IF (A.order_status_id = 6, 1, 0)
                ) over_sort_num,
                 sum(

                    IF (
                        A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),
                        1,
                        0
                    )
                ) other_type
                FROM
                (
                    SELECT
                            xdo.order_id,
                            xdo.deliver_date,
                            xdo.warehouse_id,
                            xdo.do_warehouse_id,
                        IF (
                            xdo.warehouse_id <> xdo.do_warehouse_id,
                            1,
                            xdo.is_repack
                        ) is_repack,
                                                  
                        w2.shortname do_warehouse, 
                        xdo.deliver_order_id,
                        xdo.order_status_id,
                        xdo.order_status_id soStatus,
                        w1.shortname warehouse
                    FROM
                         oc_x_deliver_order xdo
                    LEFT JOIN oc_x_warehouse w1 ON xdo.warehouse_id = w1.warehouse_id
                    LEFT JOIN oc_x_warehouse w2 ON xdo.do_warehouse_id = w2.warehouse_id
                    WHERE
                        date(xdo.deliver_date) between '".$go_date."' and  '".$to_date."' 
                        AND xdo.order_status_id != 3
                        AND xdo.order_type = 1
                        AND xdo.station_id = 2";
                if ($do_host != '99'){
                    $sql .= "
                        AND xdo.do_warehouse_id = $do_host";
                }
                if ($to_host != '99'){
                    $sql .= "
                        AND xdo.warehouse_id = $to_host";
                }
                    $sql .= "
                    ) A
                GROUP BY
                    A.deliver_date,
                    A.warehouse_id,
                    A.do_warehouse_id,
                    A.is_repack
                order by do_warehouse_id,warehouse_id,deliver_date";
                $res=$db->query($sql);
                $info=$res->rows;
                echo json_encode($info);
                break;
            case $opt =='good':
                $sql1="SELECT
                        A.deliver_date delivery_date,
                        A.warehouse target_house,
                        A.do_warehouse do_house,
                        count(DISTINCT A.deliver_order_id) sort_num,
                    IF (
                        A.warehouse_id IN (12, 14),
                    IF (A.is_repack = 0, '整', '散'),
                     '-'
                    ) sort_type,
                     sum(

                        IF (
                            A.order_status_id IN (1, 2),
                            1,
                            0
                        )
                    ) null_sort_num,
                     sum(

                        IF (A.order_status_id = 4, 1, 0)
                    ) already_allot,
                     sum(

                        IF (A.order_status_id = 5, 1, 0)
                    ) sorting_num,
                     sum(

                        IF (A.order_status_id = 8, 1, 0)
                    ) to_audit_num,
                     sum(

                        IF (A.order_status_id = 6, 1, 0)
                    ) over_sort_num,
                     sum(

                        IF (
                            A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),
                            1,
                            0
                        )
                    ) other_type,
                    sum(
                        A.quantity_zheng) full_num,sum(A.quantity_san) scatter_num,
                    sum(IF (
                        A.order_status_id IN (1, 2),
                            A.quantity_zheng,0)) null_sort_full_num,sum(IF (
                        A.order_status_id IN (1, 2),A.quantity_san,0)) null_sort_scatter_num,
                    sum(IF (A.order_status_id = 4,A.quantity_zheng,0)) over_allot_full_num,
                    sum(IF (A.order_status_id = 4,A.quantity_san,0)) over_allot_scatter_num,
                    sum(IF (A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),A.quantity_zheng,0)) other_statu_full_num,
                    sum(IF (A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),A.quantity_san,0)) other_statu_scatter_num
                    FROM
                    (
                        SELECT
                                xdo.order_id,
                                xdo.deliver_date,
                                xdo.warehouse_id,
                                xdo.do_warehouse_id,
                            IF (
                                xdo.warehouse_id <> xdo.do_warehouse_id,
                                1,
                                xdo.is_repack
                            ) is_repack,
                            w2.shortname do_warehouse,
                            xdo.deliver_order_id,
                            xdo.order_status_id,
                            xdo.order_status_id soStatus,
                            w1.shortname warehouse,
                            sum(if(op.repack = 0,dop.quantity,0)) quantity_zheng,
                            sum(if(op.repack = 1,dop.quantity,0)) quantity_san
                        FROM
                             oc_x_deliver_order xdo
                        LEFT JOIN oc_x_warehouse w1 ON xdo.warehouse_id = w1.warehouse_id
                        LEFT JOIN oc_x_warehouse w2 ON w2.warehouse_id = xdo.do_warehouse_id
                        LEFT JOIN oc_x_deliver_order_product dop ON dop.deliver_order_id = xdo.deliver_order_id
                        LEFT JOIN oc_product op ON op.product_id = dop.product_id
                        WHERE
                            date(xdo.deliver_date) between '".$go_date."' and  '".$to_date."' 
                            AND xdo.order_status_id != 3
                            AND xdo.order_type = 1
                            AND xdo.station_id = 2";
                        if ($do_host != '99'){
                            $sql1 .= "
                                AND xdo.do_warehouse_id = $do_host";
                        }
                        if ($to_host != '99'){
                            $sql1 .= "
                                AND xdo.warehouse_id = $to_host";
                        }
                        $sql1 .= "
                           
                            
                    GROUP BY xdo.deliver_order_id
                        ) A
                    GROUP BY
                        A.deliver_date,
                        A.warehouse_id,
                        A.do_warehouse_id,
                        A.is_repack 
                    order by do_warehouse_id,warehouse_id,deliver_date
                        ";
//                echo $sql1;
                $res1=$db->query($sql1);
                $list1=$res1->rows;
//                print_r($list1);
                $sql2="SELECT
                        A.deliver_date delivery_date,
                        A.warehouse target_house,
                        A.do_warehouse do_house,
                        count(DISTINCT A.deliver_order_id) sort_num,
                    IF (
                        A.warehouse_id IN (12, 14),
                    IF (A.is_repack = 0, '整', '散'),
                     '-'
                    ) sort_type,
                     sum(

                        IF (
                            A.order_status_id IN (1, 2),
                            1,
                            0
                        )
                    ) null_sort_num,
                     sum(

                        IF (A.order_status_id = 4, 1, 0)
                    ) already_allot,
                     sum(

                        IF (A.order_status_id = 5, 1, 0)
                    ) sorting_num,
                     sum(

                        IF (A.order_status_id = 8, 1, 0)
                    ) to_audit_num,
                     sum(

                        IF (A.order_status_id = 6, 1, 0)
                    ) over_sort_num,
                     sum(

                        IF (
                            A.order_status_id NOT IN (1, 2, 4, 5, 6, 8),
                            1,
                            0
                        )
                    ) other_type,
                    sum(IF (A.order_status_id = 5,A.quantity_zheng,0)) sorting_full_num,sum(IF (A.order_status_id = 5,A.quantity_san,0)) sorting_scatter_num,
                    sum(IF (A.order_status_id = 8,A.quantity_zheng,0)) audit_full_num,sum(IF (A.order_status_id = 8,A.quantity_san,0)) audit_scatter_num,
                    sum(IF (A.order_status_id = 6,A.quantity_zheng,0)) over_full_num,sum(IF (A.order_status_id = 6,A.quantity_san,0)) over_scatter_num
                    FROM
                    (
                        SELECT
                                xdo.order_id,
                                xdo.deliver_date,
                                xdo.warehouse_id,
                                xdo.do_warehouse_id,
                            IF (
                                xdo.warehouse_id <> xdo.do_warehouse_id,
                                1,
                                xdo.is_repack
                            ) is_repack,
                            w2.shortname do_warehouse,
                            xdo.deliver_order_id,
                            xdo.order_status_id,
                            xdo.order_status_id soStatus,
                            w1.shortname warehouse,
                            sum(if(op.repack = 0,dop.quantity,0)) quantity_zheng,
                            sum(if(op.repack = 1,dop.quantity,0)) quantity_san
                        FROM
                             oc_x_deliver_order xdo
                        LEFT JOIN oc_x_warehouse w1 ON xdo.warehouse_id = w1.warehouse_id
                        LEFT JOIN oc_x_warehouse w2 ON xdo.do_warehouse_id = w2.warehouse_id
                        LEFT JOIN oc_x_inventory_order_sorting dop ON dop.deliver_order_id = xdo.deliver_order_id
                        LEFT JOIN oc_product op ON op.product_id = dop.product_id
                        WHERE
                            date(xdo.deliver_date) between '".$go_date."' and  '".$to_date."' 
                            AND xdo.order_status_id != 3
                            AND xdo.order_type = 1
                            AND xdo.station_id = 2";
                if ($do_host != '99'){
                    $sql2 .= "
                            AND xdo.do_warehouse_id = $do_host";
                }
                if ($to_host != '99'){
                    $sql2 .= "
                            AND xdo.warehouse_id = $to_host";
                }
                $sql2 .= "
                    GROUP BY xdo.deliver_order_id
                        ) A
                    GROUP BY
                        A.deliver_date,
                        A.warehouse_id,
                        A.do_warehouse_id,
                        A.is_repack
                    order by do_warehouse_id,warehouse_id,deliver_date";
//                echo $sql2;
                $res2=$db->query($sql2);
                $list2=$res2->rows;
//                print_r($list2);die;
                $arr=[];
                foreach ($list1 as $k=>$v){
                    $arr[$k]=array_merge($list1[$k],$list2[$k]);
                }
                echo json_encode($arr);
                break;
        }

    }else{
        echo  json_encode('没有执行Ajax');
    }
