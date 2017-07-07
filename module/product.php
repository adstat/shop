<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/redis.php');

class PRODUCT{
    private $db;
    private $expireTime;
    private $stockTime;

    public function __construct()
    {
        $this->expireTime = defined('REDIS_CACHE_TIME')       ? REDIS_CACHE_TIME       : 1800;
        $this->stockTime  = defined('REDIS_STOCK_CACHE_TIME') ? REDIS_STOCK_CACHE_TIME : 600;
    }

    private function newRedis()
    {
        $redis = new MyRedis();
        $redis->selectdb(1);
        return $redis;
    }

    function getProductKey($warehouseId, $productId){
        $keyPrefix = REDIS_PRODUCT_KEY_PREFIX ? REDIS_PRODUCT_KEY_PREFIX : 'product';
        $key       = $keyPrefix.':'.$warehouseId.':'.$productId; //product : warehouseId : productId
        return $key;
    }

    function getListKey($warehouseId, $parentProductId){
        $keyPrefix = REDIS_LIST_KEY_PREFIX ? REDIS_LIST_KEY_PREFIX : 'list';
        $key       = $keyPrefix.':'.$warehouseId.':'.$parentProductId; //list : warehouseId : parentProductId
        return $key;
    }

    function getStockKey($warehouseId, $productId){
        $keyPrefix = REDIS_STOCK_KEY_PREFIX ? REDIS_STOCK_KEY_PREFIX : 'stock';
        $key       = $keyPrefix.':'.$warehouseId.':'.$productId; //stock : warehouseId : productId
        return $key;
    }

    function getCartKey($customerId, $stationId=1){
        $keyPrefix = REDIS_CART_KEY_PREFIX ? REDIS_CART_KEY_PREFIX : 'customer';
        $keyName   = REDIS_CART_KEY_NAME ? REDIS_CART_KEY_NAME : 'cart';
        $key       = $keyPrefix.':'.$customerId.':'.$stationId.':'.$keyName; //customer:[customerId[:[stationId]:cart
        return $key;
    }

    function getCategoryProduct($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Station
        //TODO 目前只处理一级目录
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT
                    p.product_id, pd.name, pd.abstract, p.sku, p.image, p.oss, p.sort_order product_order, if(r.points is null, 0, r.points) reward_points,
                    p.is_gift, round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                    p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,
                    round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,

                    if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,

                    p.shipping,p.weight_inv_flag, p.date_new_on, p.date_new_off, if( current_date() between p.date_new_on and p.date_new_off,1,0) new_arrive,
                    LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                    p.date_available, p.wxpay_only, ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                    c.category_id, c.parent_id, c.sort_order category_order, cd.name category_name, c.promo_order,
                    if(p.instock=1, if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-p.safestock, 999) stock,
                    if( (if(sum(A.quantity) is null, 0,sum(A.quantity))-p.safestock) <= 0, 0 , 1) stock_order
                    FROM oc_product_to_category pc
                    RIGHT JOIN oc_category c ON (pc.category_id = c.category_id)
                    RIGHT JOIN oc_category_description cd ON (c.category_id = cd.category_id)
                    LEFT JOIN oc_product p ON (pc.product_id = p.product_id AND p.status = 1)
                    LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)

                    LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)

                    LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                    LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)
                    left join oc_product_reward r on p.product_id = r.product_id
                    left join oc_x_inventory_move_item A on p.product_id = A.product_id and A.station_id = '".$station_id."' and A.status=1
                    WHERE c.status =1 and p.station_id = '".$station_id."'";

        if($id>0){
            $sql = $sql." AND ( c.category_id = {$id} OR c.parent_id = {$id} ) ";

            $sql = $sql. " AND p.product_id is not null GROUP BY p.product_id ";
            if($station_id == 2){
                $sql .= ' ORDER BY stock_order desc, pd.name, category_order ASC, product_order ASC';
            }
            else{
                $sql .= ' ORDER BY category_order ASC, product_order ASC';
            }
        }
        else{
            $sql = $sql." AND current_date() between p.date_new_on and p.date_new_off ";

            $sql = $sql. " AND p.product_id is not null
                    GROUP BY p.product_id
                    ORDER BY promo_order desc,  category_order ASC, product_order ASC ";
        }
        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){

            //To avoid JS ERROR, TO BE REMOVE
            for($m=0; $m<sizeof($results); $m++){
                $results[$m]['discountInfo'] = array();
            }

            return $results;
        }

        return false;
    }

    // 前台商品展示 [缓存]
    function getProductWithCache(array $data){
        global $db;
        $id         = (int)$data['data']['id'];
        $station_id = (int)$data['station_id'];
        $page       = isset($data['data']['page']) ? (int)$data['data']['page'] : 0;
        $pageSize   = isset($data['data']["pageSize"]) ? (int)$data['data']["pageSize"] : 0;
        $start      = 0;
        $end        = -1;
        $warehouseId = !empty($data['data']['warehouseId']) ? (int)$data['data']['warehouseId'] : 0;
        $areaId      = !empty($data['data']['areaId'])      ? (int)$data['data']['areaId']      : 0;
        if(!$warehouseId){ return false; }

        $results    = array();
        if(!empty($page) && !empty($pageSize)){
            $start  = ( $page - 1 ) * $pageSize;
            $end    = $page * $pageSize - 1;
        }

        // 先判断List key存不存在
        $redis      = $this->newRedis();
        $listKey    = $this->getListKey($warehouseId, $id);
        $productLen = $redis->llen($listKey);
        if($productLen){
            $productIds = $redis->lrange($listKey, $start, $end);
            foreach($productIds as $productId){
                $productKey  = $this->getProductKey($warehouseId, $productId);
                $productInfo = $redis->get($productKey);
                $productInfo = unserialize($productInfo);

                $stockKey  = $this->getStockKey($warehouseId, $productId);
                if($redis->exists($stockKey)){
                    $stock = $redis->get($stockKey); // 缓存查询库存
                }else{
                    $stock = $this->getProductStock($warehouseId, $productId); // 查询库存 && 写入缓存
                }
                $productInfo['stock'] = $stock;
                $results[]   = $productInfo;
            }

            return $results;
        }


        $sql = "SELECT
                    p.product_id, if(isnull(pw.name) or pw.name='', pd.name, pw.name) name, if(isnull(pw.abstract) OR pw.abstract='', pd.abstract, pw.abstract) abstract, p.sku, p.image, p.oss, p.sort_order product_order,
                    p.is_gift, round(if(isnull(pw.price) OR pw.price<0, p.price, pw.price),2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,if(r.points is null, 0, r.points) reward_points,
                    p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,
                    round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,

                    if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,

                    p.shipping,p.weight_inv_flag, p.date_new_on, p.date_new_off, if( current_date() between p.date_new_on and p.date_new_off,1,0) new_arrive,
                    LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                    p.date_available, p.wxpay_only, ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                    c.category_id, c.parent_id, c.sort_order category_order, cd.name category_name, c.promo_order,
                    if(p.instock=1, if(pi.inventory is null or pi.inventory < 0, 0, pi.inventory-p.safestock), 999) stock,
                    if( if(pi.inventory is null , 0, pi.inventory-p.safestock)<=0, 0, 1) stock_order,
                    p.sale_start_quantity,p.sale_jump_quantity
                    FROM oc_product_to_category pc
                    RIGHT JOIN oc_category c ON (pc.category_id = c.category_id)
                    RIGHT JOIN oc_category_description cd ON (c.category_id = cd.category_id)
                    LEFT JOIN oc_product p ON (pc.product_id = p.product_id AND p.status = 1)
                    LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)

                    LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)

                    LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end AND ps.warehouse_id = {$warehouseId} AND ps.area_id = 0)
                    LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)
                    LEFT JOIN oc_product_inventory pi ON p.product_id = pi.product_id
                    LEFT JOIN oc_product_to_warehouse pw ON (pw.product_id = pc.product_id AND pw.status = 1)
                    left join oc_product_reward r on p.product_id = r.product_id
                    WHERE c.status =1 and p.station_id = '{$station_id}'
                    AND pw.warehouse_id = {$warehouseId}
                    AND pi.warehouse_id = {$warehouseId}
                    ";


        if($id>0){
            $sql = $sql." AND ( c.category_id = {$id} OR c.parent_id = {$id} ) ";

            $sql = $sql. " AND p.product_id is not null GROUP BY p.product_id ";
            if($station_id == 2){
                $sql .= ' ORDER BY stock_order desc, pd.name, category_order ASC, product_order ASC';
            }
            else{
                $sql .= ' ORDER BY category_order ASC, product_order ASC';
            }
        }
        else{
            $sql = $sql." AND current_date() between p.date_new_on and p.date_new_off ";

            $sql = $sql. " AND p.product_id is not null
                    GROUP BY p.product_id
                    ORDER BY promo_order desc,  category_order ASC, product_order ASC ";
        }


        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){


            //To avoid JS ERROR, TO BE REMOVE
            $product_ids = array();
            for($m=0; $m<sizeof($results); $m++){
                $results[$m]['discountInfo'] = array();
                $product_ids[] = $results[$m]['product_id'];
            }

            // 查询区域下首页促销
            $special_data = $this->getAreaProductSpecial($areaId, $warehouseId, $product_ids);
            if(sizeof($special_data)){
                foreach($results as &$val){
                    if(array_key_exists($val['product_id'], $special_data)){
                        !empty($special_data[$val['product_id']]['special_price'])  && $val['special_price'] = $special_data[$val['product_id']]['special_price'];
                        !empty($special_data[$val['product_id']]['is_promo'])       && $val['is_promo']      = $special_data[$val['product_id']]['is_promo'];
                        !empty($special_data[$val['product_id']]['promo_title'])    && $val['promo_title']   = $special_data[$val['product_id']]['promo_title'];
                        !empty($special_data[$val['product_id']]['promo_limit'])    && $val['promo_limit']   = $special_data[$val['product_id']]['promo_limit'];
                        !empty($special_data[$val['product_id']]['showup'])         && $val['showup']        = $special_data[$val['product_id']]['showup'];
                        !empty($special_data[$val['product_id']]['maximum'])        && $val['maximum']       = $special_data[$val['product_id']]['maximum'];
                    }
                }
            }

            $return = array();
            foreach($results as $key => $value){
                $productKey = $this->getProductKey($warehouseId, $value['product_id']);
                $stockKey   = $this->getStockKey($warehouseId, $value['product_id']);

                $redis->setex($productKey, $this->expireTime, serialize($value));
                // search 有设置缓存
                if(!$redis->exists($stockKey)){
                    $redis->setex($stockKey, $this->stockTime, $value['stock']);
                }
                $redis->rpush($listKey, $value['product_id']);

                if(($start<=$key) && ($key<=$end)){ $return[] = $value; }
            }
            $redis->expire($listKey, $this->expireTime);

            return $return;
        }

        return false;
    }

    // 获取区域促销
    function getAreaProductSpecial($area_id = 0, $warehouse_id = 0, $product_ids = array())
    {
        global $db;
        $area_id      = (int)$area_id;
        $warehouse_id = (int)$warehouse_id;
        if($area_id <= 0 || $warehouse_id <= 0 || !is_array($product_ids) || empty($product_ids)){
            return array();
        }

        $sql = "SELECT ps.product_id, ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                round(IF(isnull(ps.price),p.price,ps.price),2) special_price,
                LEAST(IF(isnull(p.maximum),999,p.maximum), IF(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                ps.priority index_promo_priority
                FROM oc_product_special ps
                LEFT JOIN oc_product p ON (p.product_id = ps.product_id AND p.status = 1)
                WHERE
                NOW() BETWEEN ps.date_start AND ps.date_end
                AND ps.area_id = {$area_id}
                AND ps.warehouse_id = {$warehouse_id}
                AND ps.product_id IN (". implode(',', $product_ids) .")";
        $query    = $db->query($sql);
        $results  = $query->rows;

        $data     = array();
        if($results && sizeof($results)) {
            foreach ($results as $v) {
                $data[$v['product_id']] = $v;
            }
        }

        return $data;
    }

    // 订单生成, 减少Redis的商品缓存
    function minusStock(array $data){
        $warehouseId = (int)$data['data']['warehouse_id'];
        $stationId   = (int)$data['station_id'];
        $customerId  = (int)$data['customer_id'];
        if($stationId <= 0 || $customerId <= 0 || $warehouseId <= 0){ return false; }

        $redis      = $this->newRedis();
        $cartKey    = $this->getCartKey($customerId, $stationId);
        $cartInfo   = $redis->hgetall($cartKey);

        if(is_array($cartInfo) && sizeof($cartInfo)){
            foreach($cartInfo as $productId => $num){
                $stockKey  = $this->getStockKey($warehouseId, $productId);
                if( $redis->exists($stockKey) ){
                    $stock = $redis->get($stockKey);
                    $stock = $stock - $num;
                    if($stock < 0){ $stock = 0; }
                    $redis->setex($stockKey, $this->stockTime, $stock);
                }else{
                    $this->getProductStock($warehouseId, $productId);// 查询Stock & 写入缓存
                }
            }
        }
        return true;
    }

    // 订单取消, 增加Redis的商品缓存
    function addCacheStock(array $data){
        global $db;
        $orderId     = (int)$data['data']['order_id'];
        $warehouseId = (int)$data['data']['warehouse_id'];
        $stationId   = (int)$data['station_id'];
        if($stationId <= 0 || $orderId <= 0 || $warehouseId <= 0){ return false; }

        $sql     = "select product_id, quantity from oc_order_product where order_id = {$orderId}";
        $query   = $db->query($sql);
        $productInfo = array();
        if($query->rows){
            foreach($query->rows as $m){
                $productInfo[$m['product_id']] = $m['quantity'];
            }
        }

        $redis     = $this->newRedis();
        if(sizeof($productInfo)){
            foreach($productInfo as $productId => $num){
                $stockKey  = $this->getStockKey($warehouseId, $productId);
                if( $redis->exists($stockKey) ){
                    $stock = $redis->get($stockKey);
                    $stock = $stock + $num;
                    if($stock < 0){ $stock = 0; }
                    $redis->setex($stockKey, $this->stockTime, $stock);
                }else{
                    $this->getProductStock($warehouseId, $productId);// 查询Stock & 写入缓存
                }
            }
        }
        return true;
    }

    // 获取商品库存 && 写入缓存
    public function getProductStock($warehouseId, $productId)
    {
        global $db;
        $warehouseId = (int)$warehouseId;
        $productId   = (int)$productId;
        if($productId <= 0 || $warehouseId <= 0){ return 0; }
        $sql = "SELECT
                IF(p.instock=1, if(pi.inventory IS NULL OR pi.inventory < 0, 0, pi.inventory-p.safestock), 999) stock
                FROM oc_product p
                LEFT JOIN oc_product_inventory pi ON p.product_id = pi.product_id
                WHERE p.product_id = {$productId}
                AND pi.warehouse_id = {$warehouseId}";

        $query  = $db->query($sql);
        $result = $query->row;
        $stock  = 0;
        if($result){ $stock = $result['stock']; }

        $redis     = $this->newRedis();
        $stockKey  = $this->getStockKey($warehouseId, $productId);
        $redis->setex($stockKey, $this->stockTime, $stock);

        return $stock;
    }

    function getProductDiscount(array $data){
        global $db;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        //Get Product Discount Info
        $sql = "select A.product_id, A.price, B.quantity discount_qty, if(B.price>0, B.price,A.price) discount_price, B.date_start, B.date_end
                    from oc_product A
                    inner join oc_product_discount B on A.product_id =B.product_id
                    left join oc_product_to_category C on A.product_id = C.product_id
                    where A.status = 1 and A.station_id = '".$station_id."'
                    and current_date() between B.date_start and B.date_end
                    order by B.quantity";
        $discountInfo = array();
        $dicountQuery = $db->query($sql);
        if($dicountQuery->rows){
            foreach($dicountQuery->rows as $m){
                $discountInfo[$m['product_id']][] = $m;
            }
        }

        return array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
            'return_data' => array(
                'discountInfo' => $discountInfo
            )
        );
    }

    private function stripSearchKeyword($keyword){
        if($keyword === SEARCH_PROMO_PROD || $keyword === SEARCH_PRODUCT_BRIEF_INFO || $keyword === SEARCH_ACTIVITY_PRODUCT  || $keyword === SEARCH_PRODUCT_PRICE  || is_numeric($keyword)){
            return $keyword;
        }

        if($keyword){
            $str = trim(chop(strip_tags($keyword)));
            $find = array(";","*","\n","\r","%","$","&","-","_","+","<",">","=","/","\\","(",")","{","}",".",",","!","\"","'");
            $str = str_replace($find,'',$str);
            $strArr = explode(' ',$str);
            $keyword = '';
            foreach($strArr as $k){
                if(strlen($k)){
                    $keyword .= '%'.$k;
                }
            }
        }

        return $keyword;
    }

    function searchProduct(array $data){
        //TODO Station
        //TODO 目前只处理一级目录
        global $db;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $keyword = isset($data['data']['keyword']) ? $data['data']['keyword'] : false;
        $products = isset($data['data']['products']) && is_array($data['data']['products']) ? array_slice($data['data']['products'],0,20) : array(); //Get product brief info, 不可超过20个商品
        $activity_id = isset($data['data']['activity_id']) ? (int)$data['data']['activity_id'] : 0;
        $agent_id = isset($data['data']['agent_id']) ? (int)$data['data']['agent_id'] : 0;
        $page = isset($data['data']['page']) ? (int)$data['data']['page'] : 0;
        $pageSize = isset($data['data']["pageSize"]) ? (int)$data['data']["pageSize"] : 0;

        $keyword = $this->stripSearchKeyword($keyword);

        if(strlen($keyword) >= 3){
            $sql = "SELECT
                        p.product_id, pd.name, pd.abstract, p.sku, p.image, p.oss, p.sort_order product_order, if(r.points is null, 0, r.points) reward_points,
                        p.is_gift, round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                        p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,

                        round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,
                        if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,

                        p.shipping,p.weight_inv_flag, p.date_new_on, p.date_new_off, if( current_date() between p.date_new_on and p.date_new_off,1,0) new_arrive,
                        LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                        p.date_available, p.wxpay_only, ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                        c.category_id, c.parent_id, c.sort_order category_order, cd.name category_name, c.promo_order,
                        ps.priority index_promo_priority,
                        if(p.instock=1, if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-p.safestock, 999) stock,
                        if( (if(sum(A.quantity) is null, 0,sum(A.quantity))-p.safestock) <= 0, 0 , 1) stock_order,
                        p.sale_start_quantity,p.sale_jump_quantity
                        FROM oc_product_to_category pc
                        RIGHT JOIN oc_category c ON (pc.category_id = c.category_id)
                        RIGHT JOIN oc_category_description cd ON (c.category_id = cd.category_id)
                        LEFT JOIN oc_product p ON (pc.product_id = p.product_id AND p.status = 1)
                        LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)

                        LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)

                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                        LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)
                        left join oc_product_reward r on p.product_id = r.product_id
                        left join oc_x_inventory_move_item A on p.product_id = A.product_id and A.station_id = '".$station_id."' and A.status=1
                        WHERE c.status =1 and p.station_id = '".$station_id."'
                        ";

            if($keyword == SEARCH_PROMO_PROD){ //Search for  promotion products
                $sql = $sql. " AND ps.product_id IS NOT NULL
                          GROUP BY p.product_id
                          ORDER BY stock_order DESC,  index_promo_priority ASC, product_id DESC";
            }
            elseif(is_numeric($keyword)){
                $sql = $sql. " AND p.product_id = '".(int)$keyword."' GROUP BY p.product_id";
            }
            else{
                $sql = $sql. " AND pd.name like '%".$keyword."%'
                          GROUP BY p.product_id
                          ORDER BY stock_order DESC, category_order ASC, product_order ASC";
            }

            //Get PRODUCT_BRIEF_INFO
            if($keyword == SEARCH_PRODUCT_BRIEF_INFO && sizeof($products)){ //Just Get Product Inv
                $sql = "SELECT
                        p.product_id, pd.name, pd.abstract, p.sku, p.image, p.oss, p.sort_order product_order, if(r.points is null, 0, r.points) reward_points,
                        p.is_gift, round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                        p.retail_price, p.cashback, p.inv_size, p.is_selected, p.is_soon_to_expire,
                        p.weight_inv_flag,
                        LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                        ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                        if(p.instock=1, if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-p.safestock, 999) stock,
                        p.sale_start_quantity,p.sale_jump_quantity
                        FROM oc_product p
                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                        LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)
                        left join oc_product_reward r on p.product_id = r.product_id
                        LEFT JOIN oc_x_inventory_move_item A on p.product_id = A.product_id and A.station_id = '".$station_id."' and A.status=1
                        WHERE p.station_id = '".$station_id."'
                        AND p.product_id in (".implode(',',$products).")
                        GROUP BY p.product_id";
            }

            if($keyword == SEARCH_PRODUCT_PRICE && sizeof($products)){ //Just Get Product Inv
                $sql = "SELECT
                        p.product_id,round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price
                        FROM oc_product p
                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                        WHERE p.station_id = '".$station_id."'
                        AND p.product_id in (".implode(',',$products).")
                        GROUP BY p.product_id";
            }

            //Get Activity Product
            if($keyword == SEARCH_ACTIVITY_PRODUCT){ //Just Get Product Inv
                $sql = "SELECT
                        p.product_id, pd.name, pd.abstract, p.sku, p.image, p.oss, p.sort_order product_order, if(r.points is null, 0, r.points) reward_points,
                        p.is_gift, round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                        p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,

                        round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,
                        if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,

                        p.shipping,p.weight_inv_flag, p.date_new_on, p.date_new_off, if( current_date() between p.date_new_on and p.date_new_off,1,0) new_arrive,
                        LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                        p.date_available, p.wxpay_only, ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                        if(p.instock=1, if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-p.safestock, 999) stock,
                        if( (if(sum(A.quantity) is null, 0,sum(A.quantity))-p.safestock) <= 0, 0 , 1) stock_order,
                        p.sale_start_quantity,p.sale_jump_quantity

                        FROM oc_x_activity_product ap
                        INNER JOIN oc_product p on ap.product_id = p.product_id
                        LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)
                        LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)
                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                        LEFT JOIN oc_product_description pd ON p.product_id = pd.product_id and pd.language_id = 2
                        left join oc_product_reward r on p.product_id = r.product_id
                        LEFT JOIN oc_x_inventory_move_item A ON p.product_id = A.product_id and A.station_id = '".$station_id."' and A.status=1
                        WHERE p.station_id = '".$station_id."' and ap.act_id = '".$activity_id."' and p.status = 1
                        GROUP BY p.product_id
                        ORDER BY stock_order DESC, ap.sort_order DESC
                        ";
            }

            //Get Agent Product
            if($keyword == SEARCH_PROMO_PROD && $agent_id){
                $sql = "SELECT
                        p.product_id, concat(p.product_id,'#',pd.name) name, pd.abstract, p.sku, p.image, p.oss, p.sort_order product_order,
                        p.is_gift, round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                        p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,

                        round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,
                        if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,

                        p.shipping,p.weight_inv_flag, p.date_new_on, p.date_new_off, if( current_date() between p.date_new_on and p.date_new_off,1,0) new_arrive,
                        LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                        p.date_available, p.wxpay_only, ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                        if(p.instock=1, if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-p.safestock, 999) stock,
                        if( (if(sum(A.quantity) is null, 0,sum(A.quantity))-p.safestock) <= 0, 0 , 1) stock_order,
                        p.sale_start_quantity,p.sale_jump_quantity

                        FROM oc_product p
                        LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)
                        LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)
                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                        LEFT JOIN oc_product_description pd ON p.product_id = pd.product_id and pd.language_id = 2
                        LEFT JOIN oc_x_inventory_move_item A ON p.product_id = A.product_id and A.station_id = '".$station_id."' and A.status=1
                        WHERE p.station_id = '".$station_id."' and p.agent_id = '".$agent_id."' and p.status = 1
                        GROUP BY p.product_id
                        ORDER BY p.sort_order desc, p.product_id
                        ";
            }

            if(!empty($page) && !empty($pageSize)){
                $sql = $sql. " LIMIT ". ( $page - 1 ) * $pageSize .','. $pageSize;
            }

            $query = $db->query($sql);
            $results = $query->rows;

            if($results && sizeof($results)){

                //To avoid JS ERROR, TO BE REMOVE
                for($m=0; $m<sizeof($results); $m++){
                    $results[$m]['discountInfo'] = array();
                }
                return $results;
            }
        }

        return false;
    }

    // 新商品搜索
    function newSearchProduct(array $data){
        //TODO Station
        //TODO 目前只处理一级目录
        global $db;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $keyword = isset($data['data']['keyword']) ? $data['data']['keyword'] : false;
        $products = isset($data['data']['products']) && is_array($data['data']['products']) ? array_slice($data['data']['products'],0,20) : array(); //Get product brief info, 不可超过20个商品
        $activity_id = isset($data['data']['activity_id']) ? (int)$data['data']['activity_id'] : 0;
        $agent_id = isset($data['data']['agent_id']) ? (int)$data['data']['agent_id'] : 0;
        $page = isset($data['data']['page']) ? (int)$data['data']['page'] : 0;
        $pageSize = isset($data['data']["pageSize"]) ? (int)$data['data']["pageSize"] : 0;
        $warehouseId = !empty($data['data']['warehouseId']) ? (int)$data['data']['warehouseId'] : 0;
        $areaId = !empty($data['data']['areaId']) ? (int)$data['data']['areaId'] : 0;

        $keyword = $this->stripSearchKeyword($keyword);

        if(strlen($keyword) >= 3 && $warehouseId){
            $sql = "SELECT
                        p.product_id, if(isnull(pw.name) or pw.name='', pd.name, pw.name) name, if(isnull(pw.abstract) or pw.abstract='', pd.abstract, pw.abstract) abstract, p.sku, p.image, p.oss, p.sort_order product_order, if(r.points is null, 0, r.points) reward_points,
                        p.is_gift, round(if(isnull(pw.price) or pw.price<0, p.price, pw.price),2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                        p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,

                        round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,
                        if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,

                        p.shipping,p.weight_inv_flag, p.date_new_on, p.date_new_off, if( current_date() between p.date_new_on and p.date_new_off,1,0) new_arrive,
                        LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                        p.date_available, p.wxpay_only, ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                        c.category_id, c.parent_id, c.sort_order category_order, cd.name category_name, c.promo_order,
                        ps.priority index_promo_priority,
                        if(p.instock=1, if(pi.inventory is null or pi.inventory < 0, 0, pi.inventory-p.safestock), 999) stock,
                        if( if(pi.inventory is null , 0, pi.inventory-p.safestock)<=0, 0, 1) stock_order,
                        p.sale_start_quantity,p.sale_jump_quantity
                        FROM oc_product_to_category pc
                        RIGHT JOIN oc_category c ON (pc.category_id = c.category_id)
                        RIGHT JOIN oc_category_description cd ON (c.category_id = cd.category_id)
                        LEFT JOIN oc_product p ON (pc.product_id = p.product_id AND p.status = 1)
                        LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)

                        LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)

                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end AND ps.warehouse_id = {$warehouseId} AND ps.area_id = 0)
                        LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)
                        LEFT JOIN oc_product_inventory pi ON (p.product_id = pi.product_id AND pi.warehouse_id = {$warehouseId})
                        LEFT JOIN oc_product_to_warehouse pw ON (p.product_id = pw.product_id AND pw.status = 1)
			            left join oc_product_reward r on p.product_id = r.product_id
                        WHERE c.status = 1
                        AND p.station_id = '".$station_id."'
                        AND pw.warehouse_id = {$warehouseId}
                        ";

            if($keyword == SEARCH_PROMO_PROD){ //Search for  promotion products
                $sql = $sql. " AND ps.product_id IS NOT NULL
                          GROUP BY p.product_id
                          ORDER BY stock_order DESC,  index_promo_priority ASC, product_id DESC";
            }
            elseif(is_numeric($keyword)){
                $sql = $sql. " AND p.product_id = '".(int)$keyword."' GROUP BY p.product_id";
            }
            else{
                $sql = $sql. " AND pd.name like '%".$keyword."%'
                          GROUP BY p.product_id
                          ORDER BY stock_order DESC, category_order ASC, product_order ASC";
            }

            //Get PRODUCT_BRIEF_INFO
            if($keyword == SEARCH_PRODUCT_BRIEF_INFO && sizeof($products)){ //Just Get Product Inv
                $sql = "SELECT
                        p.product_id, if(isnull(pw.name) or pw.name='', pd.name, pw.name) name, if(isnull(pw.abstract) or pw.abstract='', pd.abstract, pw.abstract) abstract, p.sku, p.image, p.oss, p.sort_order product_order, if(r.points is null, 0, r.points) reward_points,
                        p.is_gift, round(if(isnull(pw.price) or pw.price<0, p.price, pw.price),2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                        p.retail_price, p.cashback, p.inv_size, p.is_selected, p.is_soon_to_expire,
                        p.weight_inv_flag,
                        LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                        ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                        if(p.instock=1, if(pi.inventory is null or pi.inventory < 0, 0, pi.inventory-p.safestock), 999) stock,
                        p.sale_start_quantity,p.sale_jump_quantity
                        FROM oc_product p
                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end AND ps.warehouse_id = {$warehouseId} AND ps.area_id = 0)
                        LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id)
                        LEFT JOIN oc_product_inventory pi ON (p.product_id = pi.product_id AND pi.warehouse_id = {$warehouseId})
                        LEFT JOIN oc_product_to_warehouse pw ON (p.product_id = pw.product_id AND pw.status = 1)
			            left join oc_product_reward r on p.product_id = r.product_id
                        WHERE p.station_id = '".$station_id."'
                        AND p.product_id in (".implode(',',$products).")
                        AND pw.warehouse_id = {$warehouseId}
                        GROUP BY p.product_id";
            }

            if($keyword == SEARCH_PRODUCT_PRICE && sizeof($products)){ //Just Get Product Inv
                $sql = "SELECT
                        p.product_id,round(if(isnull(pw.price) or pw.price<0, p.price, pw.price),2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price
                        FROM oc_product p
                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end AND ps.warehouse_id = {$warehouseId} AND ps.area_id = 0)
                        LEFT JOIN oc_product_to_warehouse pw ON (p.product_id = pw.product_id AND pw.status = 1)
                        WHERE p.station_id = '".$station_id."'
                        AND p.product_id in (".implode(',',$products).")
                        AND pw.warehouse_id = {$warehouseId}
                        GROUP BY p.product_id";
            }

            //Get Activity Product
            if($keyword == SEARCH_ACTIVITY_PRODUCT){ //Just Get Product Inv
                $sql = "SELECT
                        p.product_id, if(isnull(pw.name) or pw.name='', pd.name, pw.name) name, if(isnull(pw.abstract) or pw.abstract='', pd.abstract, pw.abstract) abstract, p.sku, p.image, p.oss, p.sort_order product_order,
                        p.is_gift, round(if(isnull(pw.price) or pw.price<0, p.price, pw.price),2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                        p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,

                        round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,
                        if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,

                        p.shipping,p.weight_inv_flag, p.date_new_on, p.date_new_off, if( current_date() between p.date_new_on and p.date_new_off,1,0) new_arrive,
                        LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                        p.date_available, p.wxpay_only, ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                        if(p.instock=1, if(pi.inventory is null or pi.inventory < 0, 0, pi.inventory-p.safestock), 999) stock,
                        if( if(pi.inventory is null , 0, pi.inventory-p.safestock)<=0, 0, 1) stock_order,
                        p.sale_start_quantity,p.sale_jump_quantity

                        FROM oc_x_activity_product ap
                        INNER JOIN oc_product p on ap.product_id = p.product_id
                        LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)
                        LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)
                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end AND ps.warehouse_id = {$warehouseId} AND ps.area_id = 0)
                        LEFT JOIN oc_product_description pd ON p.product_id = pd.product_id and pd.language_id = 2
                        LEFT JOIN oc_product_inventory pi ON (p.product_id = pi.product_id AND pi.warehouse_id = {$warehouseId})
                        LEFT JOIN oc_product_to_warehouse pw ON (p.product_id = pw.product_id AND pw.status = 1)
			            left join oc_product_reward r on p.product_id = r.product_id
                        WHERE
                        p.station_id = '".$station_id."'
                        and ap.act_id = '".$activity_id."'
                        and p.status = 1
                        AND pw.warehouse_id = {$warehouseId}
                        GROUP BY p.product_id
                        ORDER BY stock_order DESC, ap.sort_order DESC
                        ";
            }

            //Get Agent Product
            if($keyword == SEARCH_PROMO_PROD && $agent_id){
                $sql = "SELECT
                        p.product_id, concat(p.product_id,'#',if(isnull(pw.name) or pw.name='', pd.name, pw.name)) name, if(isnull(pw.abstract) or pw.abstract='', pd.abstract, pw.abstract) abstract, p.sku, p.image, p.oss, p.sort_order product_order,
                        p.is_gift, round(if(isnull(pw.price) or pw.price<0, p.price, pw.price),2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                        p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,

                        round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,
                        if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,

                        p.shipping,p.weight_inv_flag, p.date_new_on, p.date_new_off, if( current_date() between p.date_new_on and p.date_new_off,1,0) new_arrive,
                        LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                        p.date_available, p.wxpay_only, ps.is_promo, ps.promo_title, ps.promo_limit, ps.showup,
                        if(p.instock=1, if(pi.inventory is null or pi.inventory < 0, 0, pi.inventory-p.safestock), 999) stock,
                        if( if(pi.inventory is null , 0, pi.inventory-p.safestock)<=0, 0, 1) stock_order,
                        p.sale_start_quantity,p.sale_jump_quantity

                        FROM oc_product p
                        LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)
                        LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)
                        LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end AND ps.warehouse_id = {$warehouseId} AND ps.area_id = 0)
                        LEFT JOIN oc_product_description pd ON p.product_id = pd.product_id and pd.language_id = 2
                        LEFT JOIN oc_product_inventory pi ON (p.product_id = pi.product_id AND pi.warehouse_id = {$warehouseId})
                        LEFT JOIN oc_product_to_warehouse pw ON (p.product_id = pw.product_id AND pw.status = 1)
                        WHERE
                        p.station_id = '".$station_id."'
                        and p.agent_id = '".$agent_id."'
                        and p.status = 1
                        AND pw.warehouse_id = {$warehouseId}
                        GROUP BY p.product_id
                        ORDER BY p.sort_order desc, p.product_id
                        ";
            }

            if(!empty($page) && !empty($pageSize)){
                $sql = $sql. " LIMIT ". ( $page - 1 ) * $pageSize .','. $pageSize;
            }

            $query = $db->query($sql);
            $results = $query->rows;

            if($results && sizeof($results)){

                //To avoid JS ERROR, TO BE REMOVE
                $product_ids = array();;
                for($m=0; $m<sizeof($results); $m++){
                    $results[$m]['discountInfo'] = array();
                    $product_ids[] = $results[$m]['product_id'];;
                }

                $redis        = $this->newRedis();
                $special_data = $this->getAreaProductSpecial($areaId, $warehouseId, $product_ids);
                foreach($results as &$val){
                    if(array_key_exists($val['product_id'], $special_data)){
                        isset($val['special_price'])        && !empty($special_data[$val['product_id']]['special_price'])           && $val['special_price']        = $special_data[$val['product_id']]['special_price'];
                        isset($val['is_promo'])             && !empty($special_data[$val['product_id']]['is_promo'])                && $val['is_promo']             = $special_data[$val['product_id']]['is_promo'];
                        isset($val['promo_title'])          && !empty($special_data[$val['product_id']]['promo_title'])             && $val['promo_title']          = $special_data[$val['product_id']]['promo_title'];
                        isset($val['promo_limit'])          && !empty($special_data[$val['product_id']]['promo_limit'])             && $val['promo_limit']          = $special_data[$val['product_id']]['promo_limit'];
                        isset($val['showup'])               && !empty($special_data[$val['product_id']]['showup'])                  && $val['showup']               = $special_data[$val['product_id']]['showup'];
                        isset($val['maximum'])              && !empty($special_data[$val['product_id']]['maximum'])                 && $val['maximum']              = $special_data[$val['product_id']]['maximum'];
                        isset($val['index_promo_priority']) && !empty($special_data[$val['product_id']]['index_promo_priority'])    && $val['index_promo_priority'] = $special_data[$val['product_id']]['index_promo_priority'];
                    }

                    // 库存缓存 [ 存在->获取缓存 不存在->设置缓存 ]
                    if( isset($val['stock']) ){
                        $stockKey = $this->getStockKey($warehouseId, $val['product_id']);
                        if( $redis->exists($stockKey) ){
                            $val['stock'] = $redis->get($stockKey);
                        } else {
                            $redis->setex($stockKey, $this->stockTime, $val['stock']);
                        }
                    }
                }

                return $results;
            }
        }

        return false;
    }

    function getParentCategoryProduct($id, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Station
        //TODO 目前只处理一级目录
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        if(!$id){
            return false;
        }

        $sql = "SELECT
                p.product_id, pd.name, pd.abstract, p.sku, p.image, p.oss, p.sort_order product_order,
                p.is_gift, round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                p.retail_price, left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,
                round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,

                if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,

                p.shipping,
                LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                p.date_available, p.wxpay_only,
                c.category_id, c.parent_id, c.sort_order category_order, cd.name category_name
                FROM oc_product_to_category pc
                RIGHT JOIN oc_category c ON (pc.category_id = c.category_id)
                RIGHT JOIN oc_category_description cd ON (c.category_id = cd.category_id AND cd.language_id = {$language_id})
                LEFT JOIN oc_product p ON (pc.product_id = p.product_id AND p.status = 1)
                LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id AND wcd.language_id = {$language_id})

                LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)

                LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id AND pd.language_id = {$language_id})
                WHERE c.status =1 and p.station_id = {$station_id}
                AND c.parent_id = {$id}
                AND p.product_id is not null
                ORDER BY category_order ASC, product_order ASC";
        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }

        return false;
    }

    function getOnSaleProduct($id=0, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        $sql = "SELECT
                p.product_id, pd.name, pd.abstract, p.sku, p.image, p.oss,
                p.is_gift, round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,
                round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,

                if(p.unit_size is null or p.unit_size =0 , '', round(p.unit_size,0)) box_unit_size,  if(p.unit_size is null or p.unit_size =0 , '', uwcd.title) box_unit_title, p.unit_price box_unit_price,


                p.shipping,p.weight_inv_flag,
                LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                p.date_available, p.wxpay_only, ps.is_promo, ps.promo_title, ps.promo_limit,
                if(p.instock=1, if(sum(A.quantity) is null or sum(A.quantity)<0, 0,sum(A.quantity))-p.safestock, 999) stock
                FROM oc_product_special ps
                LEFT JOIN oc_product p ON (ps.product_id = p.product_id AND p.status = 1 AND p.station_id = {$station_id})
                LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id AND wcd.language_id = {$language_id})

                LEFT JOIN oc_weight_class_description uwcd ON (p.unit_weight_class_id = uwcd.weight_class_id)

                LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id AND pd.language_id = {$language_id})
                left join  oc_x_inventory_move_item A on p.product_id = A.product_id and A.station_id = {$station_id} and A.status=1
                WHERE now() BETWEEN ps.date_start AND ps.date_end
                AND p.status = 1 AND p.station_id = {$station_id}
                AND p.product_id is not null
                GROUP BY p.product_id
                ORDER BY stock DESC";
        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return false;
    }

    function getProduct($id=0, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Station
        //TODO 目前只处理一级目录
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT
                p.product_id, pd.name,p.sku, p.image, p.oss,
                p.is_gift, p.price, if(isnull(ps.price),p.price,ps.price) special_price,
                p.retail_price, left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,
                p.shipping,p.weight_inv_flag,
                LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                p.date_available,
                pd.description,ps.is_promo, ps.promo_title, ps.promo_limit
                FROM oc_product p
                LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id AND pd.language_id = {$language_id})
                WHERE  p.status = 1 AND p.product_id = {$id}";

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return false;
    }

    function getProducts($data='', $station_id=1, $language_id=2, $origin_id=1){
        //TODO Station
        global $db;

        $data = unserialize($data); //Product IDs
        if(!is_array($data) ){
            return false;
        }
        $productIds = implode(',', $data);

        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT
                p.product_id, pd.name,p.sku,p.weight_inv_flag, if(r.points is null, 0, r.points) reward_points,
                p.is_gift, p.price, if(isnull(ps.price),p.price,ps.price) special_price,
                round(p.weight,0) unit_amount,wcd.title unit_title,
                p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,
                LEAST(if(isnull(p.maximum),".REDIS_CART_ITEM_QTY_LIMIT.",p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                p.shipping, p.status,ps.is_promo, ps.promo_title, ps.promo_limit,p.sale_start_quantity,p.sale_jump_quantity
                FROM oc_product p
                left join oc_product_reward r on p.product_id = r.product_id
                LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)
                LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id AND pd.language_id = {$language_id})
                WHERE p.station_id = {$station_id} and p.product_id in ({$productIds})";

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return false;
    }

    // 获取购物车商品详情 [新]
    function newGetProducts($data = array()){
        global $db;

        $customer_id    = !empty($data['customer_id'])          ? (int)$data['customer_id']             : 0;
        $station_id     = !empty($data['station_id'])           ? (int)$data['station_id']              : 0;
        $language_id    = !empty($data['language_id'])          ? (int)$data['language_id']             : 2;
        $warehouse_id   = !empty($data['data']['warehouse_id']) ? (int)$data['data']['warehouse_id']    : 0;
        $area_id        = !empty($data['data']['area_id'])      ? (int)$data['data']['area_id']         : 0;
        $product_ids    = !empty($data['data']['product_ids'])  ? $data['data']['product_ids']          : array();

        if($warehouse_id <= 0) { return array(); }
        if(empty($product_ids)){ return array(); }

        $productIds = implode(',', $product_ids);

        $sql = "SELECT
                p.product_id, if(isnull(pw.name) or pw.name='', pd.name, pw.name) name, p.sku,p.weight_inv_flag, if(r.points is null, 0, r.points) reward_points,
                p.is_gift, if(isnull(pw.price) or pw.price<0, p.price, pw.price) price, if(isnull(ps.price),p.price,ps.price) special_price,
                round(p.weight,0) unit_amount,wcd.title unit_title,
                p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,
                LEAST(if(isnull(p.maximum),".REDIS_CART_ITEM_QTY_LIMIT.",p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                p.shipping, p.status,ps.is_promo, ps.promo_title, ps.promo_limit,p.sale_start_quantity,p.sale_jump_quantity
                FROM oc_product p
                LEFT JOIN oc_product_to_warehouse pw ON (p.product_id = pw.product_id AND pw.status = 1)
		        left join oc_product_reward r on p.product_id = r.product_id
                LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)
                LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end AND ps.warehouse_id = {$warehouse_id} AND area_id = 0)
                LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id AND pd.language_id = {$language_id})
                WHERE pw.warehouse_id = {$warehouse_id}
                AND p.station_id = {$station_id}
                AND p.product_id in ({$productIds})";

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            $special_data = $this->getAreaProductSpecial($area_id, $warehouse_id, $product_ids);
            if(sizeof($special_data)){
                foreach($results as &$val){
                    if(array_key_exists($val['product_id'], $special_data)){
                        !empty($special_data[$val['product_id']]['special_price']) && $val['special_price'] = $special_data[$val['product_id']]['special_price'];
                        !empty($special_data[$val['product_id']]['is_promo'])      && $val['is_promo']      = $special_data[$val['product_id']]['is_promo'];
                        !empty($special_data[$val['product_id']]['promo_title'])   && $val['promo_title']   = $special_data[$val['product_id']]['promo_title'];
                        !empty($special_data[$val['product_id']]['promo_limit'])   && $val['promo_limit']   = $special_data[$val['product_id']]['promo_limit'];
                        !empty($special_data[$val['product_id']]['maximum'])       && $val['maximum']       = $special_data[$val['product_id']]['maximum'];
                    }
                }
            }

            return $results;
        }

        return array();
    }

    // 获取购物车商品详情 [新]
    function newGetCartProducts($data = array()){
        global $db;

        $customer_id    = !empty($data['customer_id'])          ? (int)$data['customer_id']             : 0;
        $station_id     = !empty($data['station_id'])           ? (int)$data['station_id']              : 0;
        $language_id    = !empty($data['language_id'])          ? (int)$data['language_id']             : 2;
        $warehouse_id   = !empty($data['data']['warehouse_id']) ? (int)$data['data']['warehouse_id']    : 0;
        $area_id        = !empty($data['data']['area_id'])      ? (int)$data['data']['area_id']         : 0;
        $product_ids    = !empty($data['data']['product_ids'])  ? $data['data']['product_ids']          : array();

        if($warehouse_id <= 0) { return array(); }
        if(empty($product_ids)){ return array(); }

        $productIds = implode(',', $product_ids);

        $sql = "SELECT
                p.product_id, if(isnull(pw.name) or pw.name='', pd.name, pw.name) name, p.sku,p.weight_inv_flag, if(r.points is null, 0, r.points) reward_points,
                p.is_gift, if(isnull(pw.price) or pw.price<0, p.price, pw.price) price, if(isnull(ps.price),p.price,ps.price) special_price,
                round(p.weight,0) unit_amount,wcd.title unit_title,
                p.retail_price,left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,
                LEAST(if(isnull(p.maximum),".REDIS_CART_ITEM_QTY_LIMIT.",p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                p.shipping, p.status,ps.is_promo, ps.promo_title, ps.promo_limit,p.sale_start_quantity,p.sale_jump_quantity
                FROM oc_product p
                LEFT JOIN oc_product_to_warehouse pw ON (p.product_id = pw.product_id AND pw.status = 1)
		        left join oc_product_reward r on p.product_id = r.product_id
                LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id)
                LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end AND ps.warehouse_id = {$warehouse_id} AND area_id = 0)
                LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id AND pd.language_id = {$language_id})
                WHERE pw.warehouse_id = {$warehouse_id}
                AND p.station_id = {$station_id}
                AND p.product_id in ({$productIds})";

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            $special_data = $this->getAreaProductSpecial($area_id, $warehouse_id, $product_ids);
            if(sizeof($special_data)){
                foreach($results as &$val){
                    if(array_key_exists($val['product_id'], $special_data)){
                        !empty($special_data[$val['product_id']]['special_price']) && $val['special_price'] = $special_data[$val['product_id']]['special_price'];
                        !empty($special_data[$val['product_id']]['is_promo'])      && $val['is_promo']      = $special_data[$val['product_id']]['is_promo'];
                        !empty($special_data[$val['product_id']]['promo_title'])   && $val['promo_title']   = $special_data[$val['product_id']]['promo_title'];
                        !empty($special_data[$val['product_id']]['promo_limit'])   && $val['promo_limit']   = $special_data[$val['product_id']]['promo_limit'];
                        !empty($special_data[$val['product_id']]['maximum'])       && $val['maximum']       = $special_data[$val['product_id']]['maximum'];
                    }
                }
            }
        }


        $result = array();
        $sql = "SELECT A.product_id,
                ABS(SUM(IF(A.status = 1, A.quantity, 0))) customer_ordered_today,
                ABS(SUM(IF(A.status = 0, A.quantity, 0))) customer_ordered_tmr
                FROM oc_x_inventory_move_item A
                WHERE A.status = 1
                AND A.customer_id = {$customer_id}
                AND A.warehouse_id = {$warehouse_id}
                AND A.station_id = {$station_id}
                AND A.product_id IN (". implode(',', $product_ids) .")
                GROUP BY A.product_id";
        $query          = $db->query($sql);
        $move_result    = $query->rows;
        $customer_order = array();
        if(!empty($move_result)){
            foreach($move_result as $val){
                $customer_order[$val['product_id']]['today'] = $val['customer_ordered_today'];
                $customer_order[$val['product_id']]['tmr']   = $val['customer_ordered_tmr'];
            }
        }

        $redis  = $this->newRedis();
        foreach($product_ids as $key => $product_id){
            $result[$key]['product_id']             = $product_id;
            $result[$key]['customer_ordered_today'] = 0;
            $result[$key]['customer_ordered_tmr']   = 0;
            if(!empty($customer_order[$product_id])) {
                $result[$key]['customer_ordered_today'] = $customer_order[$product_id]['today'];
                $result[$key]['customer_ordered_tmr']   = $customer_order[$product_id]['tmr'];
            }

            $stockKey = $this->getStockKey($warehouse_id, $product_id);
            if( $redis->exists($stockKey) ){
                $result[$key]['inventory'] = $redis->get($stockKey);
            } else {
                $result[$key]['inventory'] = $this->getProductStock($warehouse_id, $product_id);
            }
        }

        return array($results, $result);
    }

    function getStationProduct($id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        //$sql = "SELECT product_id,inventory FROM oc_x_inventory WHERE station_id = ".$station_id;
        $sql = "select product_id, sum(quantity) inventory from oc_x_inventory_move_item where checked = 0 and status=1 and station_id = ".$station_id." group by product_id";

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && sizeof($results)){
            return $results;
        }
        return false;
    }

    function getPromProduct($id, $station_id=1, $language_id=2, $origin_id=1){
        global $db;

        $id = (int)$id;
        $station_id = (int)$station_id;
        $language_id = (int)$language_id;

        $sql = "SELECT
                ac.act_image,p.product_id, pd.name, pd.abstract, p.sku, p.image, p.sort_order product_order,
                p.is_gift, round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price,
                p.retail_price, left(pd.description, 20) short_desc, p.cashback, p.inv_size, p.instock, p.is_selected, p.is_soon_to_expire,
                round(p.weight,0) unit_amount,wcd.title unit_title,wcd.unit,
                p.shipping,p.weight_inv_flag,
                LEAST(if(isnull(p.maximum),999,p.maximum),if(isnull(ps.maximum),p.maximum,ps.maximum)) maximum,
                p.date_available, p.wxpay_only,
                c.category_id, c.parent_id, c.sort_order category_order, cd.name category_name
                FROM oc_category c
                RIGHT JOIN oc_category_description cd ON (c.category_id = cd.category_id AND cd.language_id = {$language_id})
                LEFT JOIN oc_product_to_category pc ON (c.category_id = pc.category_id)
                LEFT JOIN oc_product p ON (pc.product_id = p.product_id AND p.status = 1)
                LEFT JOIN oc_weight_class_description wcd ON (p.weight_class_id = wcd.weight_class_id AND wcd.language_id = {$language_id})
                LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                LEFT JOIN oc_product_description pd ON (p.product_id = pd.product_id AND pd.language_id = {$language_id})
                RIGHT JOIN activity_product ap ON (
                    p.product_id = ap.product_id)
                LEFT JOIN activity ac ON (
                    ap.act_id = ac.act_id)
                WHERE c.status =1 and p.station_id = {$station_id}
                AND ac.act_status = 1
                AND p.product_id is not null
                AND ac.act_id = {$id}
                ORDER BY ap.sort_order ASC";

        $query = $db->query($sql);
        $results = $query->rows;

        if($results && count($results)){
            return $results;
        }
        return false;
    }

    function productDetail(array $data){
        global $db, $cart;

        $customer_id = (int)$data['customer_id'] ? (int)$data['customer_id'] : 0;
        $uid         = $data['uid'];
        if(!$customer_id && !$uid){ return array( 'return_code' => 'FAIL', 'return_msg'  => '无法获取用户信息'); }

        $post         = $data['data'];
        $product_id   = !empty($post['product_id'])  ? (int)$post['product_id']  : 0;
        $warehouse_id = !empty($post['warehouseId']) ? (int)$post['warehouseId'] : 0;
        $area_id      = !empty($post['areaId'])      ? (int)$post['areaId']      : 0;

        if(!$product_id)    { return array('return_code' => 'FAIL', 'return_msg'  => '需要商品ID'); }
        if(!$warehouse_id)  { return array('return_code' => 'FAIL', 'return_msg'  => '需要仓库ID'); }
        if(!$area_id)       { return array('return_code' => 'FAIL', 'return_msg'  => '需要区域ID'); }

        $sql = "SELECT p.product_id, p.image, p.oss, p.retail_price, p.minimum, p.maximum, p.weight, p.weight_class_id, pd.description,
                  IF(isnull(pw.name) or pw.name='', pd.name, pw.name) name, round(IF(isnull(pw.price) or pw.price<0, p.price, pw.price),2) price
                  FROM oc_product p
                  LEFT JOIN oc_product_to_warehouse pw ON (p.product_id = pw.product_id AND pw.status = 1)
                  LEFT JOIN oc_product_description pd ON p.product_id = pd.product_id
                  WHERE
                  p.product_id = {$product_id}
                  AND pw.warehouse_id = {$warehouse_id}";
        $query = $db->query($sql);
        if(!$query->num_rows){ return array('return_code' => 'FAIL', 'return_msg'  => '商品不存在'); }


        $special_price = 0;
        $sql = "SELECT price FROM oc_product_special
                  WHERE product_id = {$product_id}
                  AND warehouse_id = {$warehouse_id}
                  AND area_id = 0
                  AND NOW() BETWEEN date_start AND date_end";
        $query_special_warehouse = $db->query($sql);
        if($query_special_warehouse->row['price']){ $special_price = $query_special_warehouse->row['price']; }

        $sql = "SELECT price FROM oc_product_special
                  WHERE product_id = {$product_id}
                  AND warehouse_id = {$warehouse_id}
                  AND area_id = {$area_id}
                  AND NOW() BETWEEN date_start AND date_end";
        $query_special_area = $db->query($sql);
        if($query_special_area->row['price']){ $special_price = $query_special_area->row['price']; }


        $sql = "SELECT title FROM oc_weight_class_description
                  WHERE weight_class_id='" . $query->row['weight_class_id'] . "'
                  AND language_id=2";
        $query_weight_class = $db->query($sql);


        $cart_key = $cart->getKey($customer_id ? $customer_id : $uid);
        return array(
            'return_code'  => 'SUCCESS',
            'product_id'   => $query->row['product_id'],
            'image'        => $query->row['oss'] ? OSSIMGPATH . $query->row['image'] : IMGPATH . $query->row['image'],
            'name'         => $query->row['name'],
            'price'        => $special_price ? sprintf("%.2f", $special_price) : sprintf("%.2f", $query->row['price']),
            'weight'       => round($query->row['weight'], 2) . $query_weight_class->row['title'],
            'description'  => $query->row['description'],
            'icebox'       => $query->row['product_id'] == ICEBOX_PRODUCT ? 'Y' : 'N',
            'min'          => $query->row['minimum'],
            'max'          => $query->row['maximum'],
            'customer_id'  => $customer_id,
            'uid'          => $uid,
            'current_cart_num' => (int)$cart->getCartItem($cart_key, $query->row['product_id'])
        );

    }

    function freshProducts(array $data){
        global $db;
        $query = $db->query("SELECT f.id, f.name, f.image, f.oss, f.market_price, f.price, f.weight, w.title FROM oc_x_product_fresh AS f LEFT JOIN oc_weight_class_description AS w ON f.weight_class_id=w.weight_class_id WHERE f.status=1 ORDER BY f.sort_order ASC");
        $products = array();
        foreach($query->rows as $row){
            $products[] = array(
                'id'           => $row['id'],
                'name'         => $row['name'],
                'img'          => $row['oss'] ? OSSIMGPATH . $row['image'] : IMGPATH . $row['image'],
                'market_price' => sprintf("%.2f", $row['market_price']),
                'price'        => sprintf("%.2f", $row['price']),
                'weight'       => round($row['weight'], 2) . $row['title']
            );
        }
        return array(
            'return_code' => 'SUCCESS',
            'products'    => $products
        );
    }
}

$product = new PRODUCT();
?>