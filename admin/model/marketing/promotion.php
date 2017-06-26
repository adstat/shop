<?php

class ModelMarketingPromotion extends Model
{
    public function addPromotion($data)
    {
        $this->event->trigger('pre.admin.coupon.add', $data);
        $gifts = explode(',', $data['gifts']);
        unset($data['gifts']);
        $array = [];
        foreach ($data as $key => $value) {
            if($key != 'warehouse_ids'){
                $array[] = '`' . $key . '`=\'' . $this->db->escape($value) . '\'';
            }
        }
        $array = join(', ', $array);
        $sql = 'INSERT INTO ' . DB_PREFIX . 'x_promotion SET ' . $array;
        $query = $this->db->query($sql);
        $promotion_id = $this->db->getLastId();
        foreach ($gifts as $gift) {
            $sql =
                'INSERT INTO ' . DB_PREFIX . 'x_promotion_gift SET promotion_id= ' . $promotion_id . ', product_id= ' .
                $gift;
            $query = $this->db->query($sql);
        }

        if(isset($data['warehouse_ids'])){
            foreach ($data['warehouse_ids'] as $warehouse){
                $this->db->query("INSERT INTO oc_x_promotion_to_warehouse SET promotion_id = '" . (int)$promotion_id ."', warehouse_id = '" .(int)$warehouse."', station_id = '" .(int)$data['station_id']."'");

            }
        }

        return $promotion_id;
    }

    public function getTotalPromotion($data=array())
    {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "x_promotion A left join oc_x_promotion_to_warehouse B on B.promotion_id = A.promotion_id where 1";

        if (isset($data['filter_warehouse_id_global']) and $data['filter_warehouse_id_global']) {
            $sql .= " and B.warehouse_id = '".(int)$data['filter_warehouse_id_global'] ."'";
        }
        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function selectGift($station_id, $name = '')
    {
        if ($name == '') {
            $sql = "select product_id,name,price from " . DB_PREFIX .
                "product where is_gift=1 and station_id=$station_id";
        } else {
            $sql = "select product_id,name,price from " . DB_PREFIX .
                "product where is_gift=1 and station_id=$station_id and `name` like '%$name%'";
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }


    public function getPromotion($promotion_id)
    {
//        A left join '.DB_PREFIX.'x_promotion_gift B on A.promotion_id = B.promotion_id
        $sql = 'SELECT * FROM ' . DB_PREFIX . 'x_promotion WHERE promotion_id =' . (int)$promotion_id;
        $query = $this->db->query($sql);
        $sql = 'SELECT * FROM ' . DB_PREFIX . 'x_promotion_gift WHERE promotion_id =' . (int)$promotion_id;
        $promotion_gifts = $this->db->query($sql)->rows;
        $gifts = [];
        foreach ($promotion_gifts as $promotion_gift) {
            $gifts[] = $promotion_gift['product_id'];
        }
        $query->row['gifts'] = join(',', $gifts);
        return $query->row;
    }

    public function selectGifts($product_ids)
    {
        $sql = "select product_id,name,price from " . DB_PREFIX .
            "product where product_id in ($product_ids)";
        $query = $this->db->query($sql);
        return $query->rows;
    }


    public function editPromotion($promotion_id, $data)
    {
        $this->event->trigger('pre.admin.promotion.edit', $data);
        $gifts = explode(',', $data['gifts']);
        unset($data['gifts']);
        $array = [];
        foreach ($data as $key => $value) {
            if($key != 'warehouse_ids'){
                $array[] = '`' . $key . '`=\'' . $this->db->escape($value) . '\'';
            }
        }
        $array = join(', ', $array);
        $sql = 'UPDATE '.DB_PREFIX .'x_promotion SET '.$array.' WHERE promotion_id='.$promotion_id;
        $this->db->query($sql);
        $sql = 'DELETE FROM '.DB_PREFIX.'x_promotion_gift WHERE promotion_id='.$promotion_id;
        $this->db->query($sql);
        foreach ($gifts as $gift) {
            $sql = 'INSERT INTO ' . DB_PREFIX . 'x_promotion_gift SET promotion_id= ' . $promotion_id . ', product_id= ' . $gift;
            $query = $this->db->query($sql);
        }

        $this->db->query("DELETE FROM oc_x_promotion_to_warehouse WHERE promotion_id = '" . (int)$promotion_id. "'");
        if(isset($data['warehouse_ids'])){
            foreach ($data['warehouse_ids'] as $warehouse){
                $sql = "INSERT INTO oc_x_promotion_to_warehouse SET promotion_id = '" . (int)$promotion_id ."', warehouse_id = '" .(int)$warehouse."', station_id = '" .(int)$data['station_id']."'";
//                var_dump($sql);die;
                $this->db->query($sql);

            }
        }

        $this->event->trigger('post.admin.promotion.edit', $promotion_id);
    }

    public function deletePromotion($promotion_id)
    {
        $this->event->trigger('pre.admin.promotion.delete', $promotion_id);
        $sql = 'DELETE FROM '. DB_PREFIX .'x_promotion WHERE promotion_id = '.$promotion_id;
        $this->db->query($sql);
        $sql = 'DELETE FROM '. DB_PREFIX .'x_promotion_gift WHERE promotion_id = '.$promotion_id;
        $this->db->query($sql);

        $this->event->trigger('post.admin.promotion.delete', $promotion_id);
    }


    public function getPromotions($data = array())
    {
        $sql =
            "SELECT A.promotion_id, A.`type`, A.status, A.title, A.station_id, A.firstorder, A.overlap, A.min_cart_total,A.max_cart_total, A.disount_fixed, A.discount, A.date_start, A.date_end ,A.`desc`   FROM " .
             DB_PREFIX . "x_promotion A
             left join oc_x_promotion_to_warehouse B on B.promotion_id = A.promotion_id
             where 1";

        $sort_data = array(
            'type',
            'station_id',
            'firstorder',
            'overlap',
            'disount_fixed',
            'status',
            'discount',
            'date_start',
            'date_end'
        );

        if (isset($data['filter_warehouse_id_global']) and $data['filter_warehouse_id_global']) {
            $sql .= " and B.warehouse_id = '".(int)$data['filter_warehouse_id_global'] ."'";
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY A." . $data['sort'];
        } else {
            $sql .= " ORDER BY A.type";
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

        $query = $this->db->query($sql);

        return $query->rows;
    }


    public function getPromotionHistories($promotion_id, $start = 0, $limit = 10)
    {
        $start < 0 && $start = 0;
        $limit < 1 && $limit = 10;

        $sql =
            'SELECT title, product_id , price, special_price, quantity, order_id, customer_id, date_added, status, added_by from ' .
            DB_PREFIX . 'x_promotion_activity where promotion_id=' . $promotion_id . ' limit ' . $start . ',' . $limit;

        $query =
            $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalPromotionHistories($promotion_id)
    {
        $sql="SELECT COUNT(*) AS total FROM " . DB_PREFIX . "x_promotion_activity WHERE promotion_id =" .
            (int)$promotion_id;
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getPromotionWarehouse($promotion_id)
    {
        $promotion_id    = (int)$promotion_id;
        if($promotion_id < 0){ return array(); }

        $query = $this->db->query("SELECT warehouse_id FROM oc_x_promotion_to_warehouse WHERE promotion_id = '" . $promotion_id . "'");
        return $query->rows;
    }
}