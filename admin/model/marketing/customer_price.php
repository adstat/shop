<?php
class ModelMarketingCustomerPrice extends Model {
    public function getTotalCustomers($data = array())
    {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer";

        $implode = array();

        $implode[] = "is_agent <> 0 ";

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
        }

        if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
            $implode[] = "newsletter = '" . (int)$data['filter_newsletter'] . "'";
        }

        if (!empty($data['filter_customer_group_id'])) {
            $implode[] = "customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
        }

        if (!empty($data['filter_ip'])) {
            $implode[] = "customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" .
                $this->db->escape($data['filter_ip']) . "')";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "status = '" . (int)$data['filter_status'] . "'";
        }

        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getCustomers($data = array())
    {
        $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX .
            "customer c LEFT JOIN " . DB_PREFIX .
            "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" .
            (int)$this->config->get('config_language_id') . "'";

        $implode = array();

        $implode[] = "c.is_agent <> 0 ";

        if (!empty($data['filter_name'])) {

            if (preg_match('/\D/', $data['filter_name']) == 1) {
                if((preg_match('/\s/', $data['filter_name']) == 1)){
                    $implode[] = " CONCAT(c.firstname, ' ', c.lastname) = '".$data['filter_name'] ."' ";
                }else{
                    //                var_dump('店名 or 地址');
                    $implode[] = '(c.merchant_name LIKE \'%' . $this->db->escape($data['filter_name']) . '%\' OR
                c.merchant_address LIKE \'%' . $this->db->escape($data['filter_name']) . '%\')';
                }
            } else {
                if (preg_match('/\d{11}/', $data['filter_name']) == 1) {
//                    var_dump('手机号');
                    $implode[] = 'c.telephone=' . $data['filter_name'];
                } else {
//                    var_dump('id or 店名 or 地址');
                    $implode[] = '(c.merchant_name LIKE \'%' . $this->db->escape($data['filter_name']) . '%\' OR
                c.merchant_address LIKE \'%' . $this->db->escape($data['filter_name']) . '%\' OR
                c.customer_id=' . $this->db->escape($data['filter_name']) . ')';
                }
            }


        }

//		if (!empty($data['filter_email'])) {
//			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
//		}
        if (isset($data['filter_bd']) && !is_null($data['filter_bd'])) {
            $implode[] = "c.bd_id = '" . (int)$data['filter_bd'] . "'";
        }

//        if (isset($data['filter_agent']) && !is_null($data['filter_agent'])) {
//            $implode[] = "c.is_agent = '" . (int)$data['filter_agent'] . "'";
//        }

        if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
            $implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
        }

        if (!empty($data['filter_customer_group_id'])) {
            $implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
        }

//		if (!empty($data['filter_ip'])) {
//			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
//		}

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
        }

        if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
            $implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if ($implode) {
            $sql .= " AND " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'name',
            'c.email',
            'customer_group',
            'c.status',
            'c.approved',
            'c.ip',
            'c.date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        //var_dump($sql);
        $query = $this->db->query($sql);
        $rows = $query->rows;
        $customer_ids = [];
        foreach ($rows as &$row) {
            $sql = 'select orderid, date_added from ' . DB_PREFIX . 'order where customer_id =' . $row['customer_id'] .
                ' order by date_added desc limit 1';
            $last_order = $this->db->query($sql)->row;
            if(!empty($last_order)){
                $row['orderid'] = $last_order['orderid'];
                $row['order_date_added'] = $last_order['date_added'];
        }
    }

    return $rows;
    }

    public function getCustomerProductPrice($customer_id){
        $sql = "select cp.*,p.price,p.name from
            oc_product_customer_price cp
            left join oc_product p on p.product_id = cp.product_id
            where cp.customer_id = '".$customer_id."'";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function editCustomerProduct($customer_id,$user_id,$data = array()){
        $sql = "delete from oc_product_customer_price where customer_id = '". $customer_id ."'";
        $this->db->query($sql);
        $date = date('Y-m-d H:i:s');
        if(sizeof($data['product_link']) > 1){
            foreach($data['product_link'] as $product){
                if($product['product_id']){
                    $sql = "insert into oc_product_customer_price (`product_id`,`customer_id`,`customer_price`,`added_by`,`date_added`)
                            values('".$product['product_id']."','".$customer_id."','".$product['customer_price']."','".$user_id."','".$date."')";
                    $this->db->query($sql);
                }
            }
        }
    }
}