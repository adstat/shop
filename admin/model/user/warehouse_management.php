<?php
class ModelUserWarehouseManagement extends Model {
    public function getStationsSection(){
        $sql = "SELECT  station_section_type_id ,name  FROM  oc_x_product_section_type ";
        $query = $this->db->query($sql);

        return $query->rows;
    }



    public function getStationSectionTitle(){
        $sql ="SELECT  A.section_id  id , A.parent_section_id pId ,A.section_title name ,B.name stationname ,C.name sectionname,A.station_id ,A.section_type_id
            FROM oc_x_section  A
            LEFT JOIN oc_x_station B ON A.station_id = B.station_id
            LEFT JOIN  oc_x_product_section_type C ON  A.section_type_id = C.station_section_type_id
            ORDER BY  section_id ";
        $query = $this->db->query($sql);
        return $query->rows;
    }


    public function getSectionProducts($filter_data){
        $sql = "SELECT P.product_id ,P.name productname, S.name stationname ,SST.name sectionname ,SS.product_section_title ,SS.sort,SS.product_section_id
          FROM  oc_product_section  SS
          LEFT JOIN  oc_product P   ON SS.product_id = P.product_id
          LEFT JOIN  oc_x_product_section_type SST ON SST.station_section_type_id = SS.product_section_type_id
          LEFT JOIN  oc_x_station  S ON  S.station_id = SS.station_id  WHERE  1=1";

        if($filter_data['filter_station_id']){
            $sql .=" and SS.station_id = '".$filter_data['filter_station_id']."' ";
        }
        if($filter_data['filter_station_section_type_id']){
            $sql .=" and SS.product_section_type_id = '".$filter_data['filter_station_section_type_id']."' ";
        }

        if($filter_data['filter_station_section_title']){
            $sql .= " and SS.product_section_title like '".$filter_data['filter_station_section_title']."%'";
        }
        if($filter_data['filter_warehouse_id_global']){
            $sql .=" and SS.warehouse_id = '".$filter_data['filter_warehouse_id_global']."' ";
        }

        $sql .= " order by SS.station_id DESC ,SS.product_section_type_id ,SS.product_section_id  ";
        if (isset($filter_data['start']) || isset($filter_data['limit'])) {
            if ($filter_data['start'] < 0) {
                $filter_data['start'] = 0;
            }

            if ($filter_data['limit'] < 1) {
                $filter_data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$filter_data['start'] . "," . (int)$filter_data['limit'];
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }


    public function getTotalProductSections($filter_data){
        $sql = "SELECT  COUNT(*) AS total FROM    oc_product_section  SS
          LEFT JOIN  oc_x_product_section_type SST ON SST.station_section_type_id = SS.product_section_type_id
          LEFT JOIN  oc_x_station  S ON  S.station_id = SS.station_id  WHERE  1=1";

        if($filter_data['filter_station_id']){
            $sql .=" and SS.station_id = '".$filter_data['filter_station_id']."' ";
        }
        if($filter_data['filter_station_section_type_id']){
            $sql .=" and SS.product_section_type_id = '".$filter_data['filter_station_section_type_id']."' ";
        }

        if($filter_data['filter_station_section_title']){
            $sql .= " and SS.product_section_title like '".$filter_data['filter_station_section_title']."%'";
        }
        if($filter_data['filter_warehouse_id_global']){
            $sql .=" and SS.warehouse_id = '".$filter_data['filter_warehouse_id_global']."' ";
        }
        $query = $this->db->query($sql);
        return $query->row['total'];
    }


    public function addProductSection($data,$userId,$parent_station_section,$filter_warehouse_id_global){
      $sort =  trim($data["sort_order"]);
        //判断是否添加商品
        if($data['products']){
            $sql = "insert into oc_product_section (`product_id`,`warehouse_id`,`station_id`,`product_section_title`,`sort`,`product_section_type_id`,`is_tray`,`is_shelf`,`is_mobile`,`status`,`date_added`,`added_by` ) VALUES ('".$data['products']."','".$filter_warehouse_id_global."','".$data['filter_station_id']."','".$data['station_section_title']."','".$data['sort_order']."','".$data['filter_station_section_id']."','".$data['tray']."','".$data['shelf']."','".$data['mobile']."','".$data['status']."',NOW(),'".$userId."') ";
            $query = $this->db->query($sql);

        }else{
            $sql = "select section_id  from oc_x_section WHERE section_title = '".$parent_station_section ."' and warehouse_id = '".$filter_warehouse_id_global."'";
            $query = $this->db->query($sql);
            $result = $query->row;
            //判断是否有父级
            if($result){
                $sql = "insert into  oc_x_section  (`station_id`,`parent_section_id`,`section_title`, `section_type_id`,`is_tray`,`is_shelf`,`is_mobile`,`sort`,`status`,`date_added`,`added_by` ,`warehouse_id`   )
 VALUES
 ('".$data['filter_station_id']."',  '".$result['section_id']."','".$data['station_section_title']."' , '".$data['filter_station_section_id']."' , '".$data['tray']."', '".$data['shelf']."', '".$data['mobile']."','".$sort."' ,'".$data['status']."',NOW(),'".$userId."','".$filter_warehouse_id_global ."')";

                $query = $this->db->query($sql);

            }else{
                $sql = "insert into  oc_x_section  (`station_id`,`parent_section_id`,`section_title`, `section_type_id`,`is_tray`,`is_shelf`,`is_mobile`,`sort`,`status`,`date_added`,`added_by` ,`warehouse_id`   )
 VALUES
 ('".$data['filter_station_id']."',  '0','".$data['station_section_title']."' , '".$data['filter_station_section_id']."' , '".$data['tray']."', '".$data['shelf']."', '".$data['mobile']."','".$sort."' ,'".$data['status']."',NOW(),'".$userId."','".$filter_warehouse_id_global ."')";

                $query = $this->db->query($sql);
            }
        }
        return $query;

    }

    public function getProductsSectionInfo($station_section_id){
        $sql = "SELECT P.product_id ,P.name productname, S.name stationname ,SST.name sectionname ,SS.product_section_title ,SS.station_id ,SS.product_section_type_id ,SS.status ,SS.sort,SS.is_tray,SS.is_shelf,SS.is_mobile
          FROM   oc_product P
          LEFT JOIN  oc_product_section  SS ON SS.product_id = P.product_id
          LEFT JOIN  oc_x_product_section_type SST ON SST.station_section_type_id = SS.product_section_type_id
          LEFT JOIN  oc_x_station  S ON  S.station_id = SS.station_id  WHERE  SS.product_section_id = '$station_section_id'";
        $query = $this->db->query($sql);

        return $query->row;

    }


    public function updateProductsSectionInfo($station_section_id,$data,$userId,$parent_station_section,$filter_warehouse_id_global){
       $sql1 = "select product_section_title from oc_product_section WHERE product_section_id = '".$station_section_id."'";
        $query = $this->db->query($sql1);
        $result =  $query->row;

        $sql = " update oc_product_section  set  product_id ='".$data['products']."' , station_id ='".$data['filter_station_id']."',product_section_type_id = '".$data['filter_station_section_id']."',product_section_title = '".$data['station_section_title']."',  status = '".$data['status']."' ,is_tray='".$data['tray']."' ,is_shelf='".$data['shelf']."',is_mobile='".$data['mobile']."',sort= '".$data['sort_order']."' ,date_added = NOW(),added_by = '".$userId."' where product_section_id = '".$station_section_id."' and warehouse_id = '".$filter_warehouse_id_global."'";
        $query = $this->db->query($sql);
        if($query){
            $sql = "insert into oc_product_section_history ( `product_id`,`inv_class_sort`,`new_section`,`added_by`,`date_added`  ) VALUES ('".$data['products'] ."' ,'".$result['product_section_title']."','".$data['station_section_title']."','".$userId."', NOW())";
            $query = $this->db->query($sql);
        }
        return $query;
    }

    public function updateSort($station_section_id,$sort,$modify_by){
        $sql = "UPDATE oc_product_section  SET sort = '$sort' ,added_by = '$modify_by' WHERE product_section_id = '$station_section_id'";

        return $this->db->query($sql);
    }


    public function getProducts($data = array(),$filter_warehouse_id_global ){
        $sql = "SELECT   ptw.product_id ,p.name ,ptw.status ,w.title FROM oc_product_to_warehouse ptw LEFT JOIN oc_product p ON ptw.product_id = p.product_id  LEFT JOIN  oc_x_warehouse w ON  ptw.warehouse_id = w.warehouse_id WHERE 1=1 ";
        if($filter_warehouse_id_global){
            $sql .= "  and ptw.warehouse_id = '".$filter_warehouse_id_global ."' ";
        }

        $implode = array();
        if(!empty($data['filter_name'])){
             //商品名字
            if(preg_match('/\D/', $data['filter_name']) == 1){
                $implode[] = '( p.name LIKE \'%' . $this->db->escape($data['filter_name']) . '%\')';
            }else{
                $implode[] = '( p.name LIKE \'%' . $this->db->escape($data['filter_name']) . '%\' OR             ptw.product_id=' . $this->db->escape($data['filter_name']) . ')';

            }

            if ($implode) {
                $sql .= " AND " . implode(" AND ", $implode);

            }

        }

        $query = $this->db->query($sql);
        $rows = $query->rows;
        return $rows;
    }

    public function getProductSection($data = array(),$filter_warehouse_id_global){
        $sql = "select ps.product_id ,p.name , ps.status ,w.title ,ps.product_section_title from oc_product_section ps LEFT JOIN  oc_x_warehouse w ON  ps.warehouse_id = w.warehouse_id LEFT JOIN  oc_product p ON  ps.product_id = p .product_id WHERE  1=1 ";

        if($filter_warehouse_id_global){
            $sql .= "  and ps.warehouse_id = '".$filter_warehouse_id_global ."' ";
        }
        if($data['section']){
            $sql .= ' and (  ps.product_section_title LIKE \'%' . $this->db->escape($data['section']) . '%\')';

        }
        $sql .= " order by ps.warehouse_id";
        $query = $this->db->query($sql);
        $rows = $query->rows;
        return $rows;

    }


}