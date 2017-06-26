<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');
require_once('product.php');

class PROMOTION{
    private $db;

    private function checkAgentCustomer($customer_id){
        global $db;

        //代理用户和账期用户不可使用促销和优惠券
        $sql = "select customer_id from oc_customer where customer_id = '".$customer_id."' and is_agent = 0 and payment_cycle = 0";
        $query = $db->query($sql);

        if(!$query->num_rows){
            return true;
        }
        return false;
    }

    public function verifyCartPromotion($customer_id=0, $station_id=1, $language_id=2, $origin_id=1){
        //TODO Multi-language
        //TODO Station

        global $db;

        $sql = "select count(*) count from oc_promotion_activity where promotion_id =1 and status = 1 and customer_id  = '".$customer_id."'";
        $query = $db->query($sql);
        $results = $query->row;
        $count = 0;
        if($results && sizeof($results)){
            $count = $results['count'];
        }

        return $count;
    }

    public function getPromotions(array $data){
        global $db;
        global $product;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;
        $cartItems = isset($data['data']['cartItems']) ? $data['data']['cartItems'] : array();
        $warehouse_id = !empty($data['data']['warehouse_id']) ? (int)$data['data']['warehouse_id'] : 0;
        $area_id      = !empty($data['data']['area_id'])      ? (int)$data['data']['area_id']      : 0;

        //$type = isset($data['data']['type']) ? $data['data']['type']: false;

        $returnRules = array();
        $validRules = array(0);

        $returnPromotions = array(); //返回有效促销
        $discountTotal = 0; //有效促销规则的折扣合计

        //if($customer_id !== 664){
        //    $cartItems = array();
        //}

        if(sizeof($cartItems)){
            // 检测用户是否首单－ Alex停用20170411
            // $firstOrder = $this->checkFirstOrder($station_id, $customer_id);
            // $firstOrder = true;

            //整理购物车商品,获取购物车商品列表，比对用
            $cartProductList = array();
            foreach($cartItems as $id=>$qty){
                $cartProductList[] = $id;
            }

            $promotionRelevantProductList = array(0); //与促销有关的商品，用户折扣计算

            //获取有效促销规则，限定时间有效性，和一周第几天的设定
            $sql = "
                select pr.promotion_id, pr.type, pr.title, pr.firstorder, pr.min_cart_total, pr.max_cart_total, pr.discount,pr.display
                from oc_x_promotion pr
                LEFT JOIN oc_x_promotion_to_warehouse pw ON pr.promotion_id = pw.promotion_id
                where
                    pr.status = 1 and pr.station_id = '".$station_id."'
                    and now() BETWEEN pr.date_start AND pr.date_end
                    and (pr.weekday = 0 or weekday(current_date())+1 = pr.weekday)";
            if($warehouse_id){
                $sql .= " and pw.warehouse_id =  '".$warehouse_id."'";
            }
            $sql .= " order by pr.type desc, pr.sort_order desc;";

            $query = $db->query($sql);
            if(sizeof($query->rows)){
                foreach($query->rows as $m){
                    $returnRules[$m['promotion_id']] = $m;
                    $returnRules[$m['promotion_id']]['baseDiscount'] = $m['type']=='discount' ? $m['discount'] : 0; //仅discount类型计算折扣
                    //$returnRules[$m['promotion_id']]['validDiscount'] = 0;
                    $returnRules[$m['promotion_id']]['targetProductsCount'] = 0;
                    $returnRules[$m['promotion_id']]['validQty'] = 0;

                    $returnRules[$m['promotion_id']]['triggerProductList'] = array(); //记录触发条件的购物车商品，用于订单商品折扣计算
                    $returnRules[$m['promotion_id']]['triggerProductTotal'] = 0; //记录触发条件的购物车商品，用于订单商品折扣计算

                    $validRules[] = $m['promotion_id'];
                }
            }

            //获取折扣目标商品
            $sql = 'select promotion_id,product_id,addup,quantity,max_quantity,discount,promotion_price,sort_order from oc_x_promotion_product where status = 1 and promotion_id in ('.implode(",",$validRules).')';
            $query = $db->query($sql);
            foreach($query->rows as $m){
                $returnRules[$m['promotion_id']]['targetProducts'][$m['product_id']] = $m;
                $returnRules[$m['promotion_id']]['targetProducts'][$m['product_id']]['discountQtyBound'] = 0;
                $returnRules[$m['promotion_id']]['targetProducts'][$m['product_id']]['discountAmount'] = 0;
                $returnRules[$m['promotion_id']]['targetProducts'][$m['product_id']]['discountQty'] = 0;
                $returnRules[$m['promotion_id']]['targetProducts'][$m['product_id']]['discountTotal'] = 0;
                $promotionRelevantProductList[] = $m['product_id'];
            }

            //获取促销规则绑定的商品，与购物车中商品验证是否可用, 规则返回值添加触发的商品列表及数量
            // TODO 增加指定商品分类多个商品总数合计的计算
            // TODO 对排除商品的比对方式
            $promotionTriggerProductList = array();
            $promotionTriggerProductQty = array();
            $sql = 'select promotion_id, product_id, quantity from oc_x_promotion_bind_product where status = 1 and product_id > 0 and promotion_id in ('.implode(",",$validRules).')';
            $query = $db->query($sql);
            foreach($query->rows as $m){
                $promotionTriggerProductList[$m['promotion_id']][] = $m['product_id'];
                $promotionTriggerProductQty[$m['promotion_id']][$m['product_id']] = $m['quantity'];

                $promotionRelevantProductList[] = $m['product_id'];

                //if(array_key_exists($m['product_id'], $cartItems) && $cartItems[$m['product_id']] >= $m['quantity']){
                //    $returnRules[$m['promotion_id']]['validQty'] += $cartItems[$m['product_id']];
                //}
            }

            //通过商品模块接口获取绑定商品及指定满减商品的商品名称，价格等数据
            $productsPostData = array(
                'station_id' => $station_id,
                'data' => array(
                    'keyword' => SEARCH_PRODUCT_BRIEF_INFO,
                    'products' => $promotionRelevantProductList,
                    'warehouseId' => $warehouse_id,
                    'areaId' => $area_id
                )
            );
            //$promotionRelevantProductsRaw = $product->searchProduct($productsPostData);
            $promotionRelevantProductsRaw = $product->newSearchProduct($productsPostData);
            if(!$promotionRelevantProductsRaw){
                $promotionRelevantProductsRaw = array(); //TODO: searchProduct返回false
            }
            $promotionRelevantProducts = array();
            foreach($promotionRelevantProductsRaw as $m){
                $promotionRelevantProducts[$m['product_id']] = $m;
            }

            //比对绑定商品的促销和购物车商品，购物车中无对应商品则不符合规则，排除相应促销，未绑定商品的促销规则不比对，视为有效
            foreach($promotionTriggerProductList as $promoId=>$productList){
                $matchedProductList = array_intersect($productList, $cartProductList);

                if(!sizeof($matchedProductList)){
                    unset($returnRules[$promoId]);
                }
                else{
                    //判断绑定商品的数量需求，遍历交集商品，任一条购物车商品数量满足绑定商品数量需求，视为有效，若均不满足数量则排除
                    $matchedProductListQtyChecked = false;
                    foreach($matchedProductList as $m){
                        if($cartItems[$m] >= $promotionTriggerProductQty[$promoId][$m]){
                            $matchedProductListQtyChecked = true;
                            //break;

                            //获取有效触发，数量及金额
                            $returnRules[$promoId]['validQty'] += $cartItems[$m];
                            $returnRules[$promoId]['triggerProductList'][] = $m;
                            $returnRules[$promoId]['triggerProductTotal'] += $promotionRelevantProducts[$m]['special_price'] * $cartItems[$m];
                        }
                    }

                    if(!$matchedProductListQtyChecked){
                        unset($returnRules[$promoId]);
                    }
                }
            }

            //根据促销规则绑定目标商品折扣情况和当前这些目标商品售价，计算最终折扣金额
            foreach($returnRules as $promoId=>$promo){
                foreach($promo['targetProducts'] as $m){
                    $finalDiscountAmount = ($m['discount'] > 0) ? $m['discount'] : ($promotionRelevantProducts[$m['product_id']]['special_price']-$m['promotion_price']);
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['discountAmount'] = $finalDiscountAmount;

                    $discountQtyBound = min($returnRules[$promoId]['validQty'],$m['max_quantity']);
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['discountQtyBound'] = $discountQtyBound;

                    //计算促销折扣，判断是否满足指定商品数量，如果addup为1则累加
                    if(array_key_exists($m['product_id'], $cartItems)){
                        $finalDiscountQty = $m['addup']==1 ? min($cartItems[$m['product_id']], $discountQtyBound) : $m['quantity']; //最大折扣数量的限制
                    }else{
                        $finalDiscountQty = $m['quantity']; //最大折扣数量的限制
                    }

                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['discountQty'] = $finalDiscountQty;
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['discountTotal'] = $finalDiscountAmount * $finalDiscountQty;
                    //$returnRules[$promoId]['validDiscount'] += $returnRules[$promoId]['targetProducts'][$m['product_id']]['discountTotal'];

                    //折扣小于零，折为零
                    if($returnRules[$promoId]['targetProducts'][$m['product_id']]['discountTotal'] < 0){
                        $returnRules[$promoId]['targetProducts'][$m['product_id']]['discountTotal'] = 0;
                    }

                    //合并目标商品信息
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['product_name'] = $promotionRelevantProducts[$m['product_id']]['name'];
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['price'] = $promotionRelevantProducts[$m['product_id']]['price'];
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['special_price'] = $promotionRelevantProducts[$m['product_id']]['special_price'];
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['stock'] = $promotionRelevantProducts[$m['product_id']]['stock'];
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['image'] = $promotionRelevantProducts[$m['product_id']]['image'];
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['sale_jump_quantity']  = $promotionRelevantProducts[$m['product_id']]['sale_jump_quantity'];
                    $returnRules[$promoId]['targetProducts'][$m['product_id']]['sale_start_quantity'] = $promotionRelevantProducts[$m['product_id']]['sale_start_quantity'];
                }

                $returnRules[$m['promotion_id']]['targetProductsCount'] = sizeof($returnRules[$m['promotion_id']]['targetProducts']);
                //if($returnRules[$promoId]['validDiscount'] == 0){
                //    $returnRules[$promoId]['validDiscount'] = $returnRules[$promoId]['baseDiscount'];
                //}

                //折扣小于零，折为零
                //if($returnRules[$promoId]['validDiscount'] < 0){
                //    $returnRules[$promoId]['validDiscount'] = 0;
                //}
            }
        }

        //最后整理数据格式－非关联数组
        foreach($returnRules as $m){
            $returnPromotions[] = $m;
        }

        //检查是否为代理用户，代理用户不可以使用促销规则
        if($this->checkAgentCustomer($customer_id)){
            $returnPromotions = array();
            $discountTotal = 0;
        }

        return array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
            'return_data' => array(
                'promotions' => $returnPromotions,
                'discountTotal' => $discountTotal
            )
        );
    }

    private function checkFirstOrder($station_id, $customer_id){
        global $db;

        $sql = "select count(*) orders from oc_order
        where order_status_id not in (3) and customer_id = '".$customer_id."'
        and station_id = '".$station_id."' and type = 1
        ";
        $query = $db->query($sql);
        if($query->row){
            $orderInfo = $query->row;

            return $orderInfo['orders'];
        }

        return false;
    }

    function getCoupons(array $data){
        global $db;
        global $product;

        $station_id   = isset($data['station_id']) ? (int)$data['station_id']                       : 0;
        $customer_id  = isset($data['customer_id']) ? (int)$data['customer_id']                     : 0;
        $cartItems    = isset($data['data']['cartItems']) ? $data['data']['cartItems']              : array();
        $warehouse_id = !empty($data['data']['warehouse_id']) ? (int)$data['data']['warehouse_id']  : 0;
        $area_id      = !empty($data['data']['area_id'])      ? (int)$data['data']['area_id']       : 0;


        // 获取用户使用优惠券记录
        // TODO 可缓存预先载入
        $sql = "select H.coupon_id, O.customer_id, if(count(O.order_id) is null, 0, count(O.order_id)) use_count from oc_coupon_history H
                    left join oc_order O on H.order_id = O.order_id
                    where H.customer_id = '".$customer_id."'
                    and O.order_status_id not in ('".CANCELLED_ORDER_STATUS."')
                    and O.station_id = '".$station_id."'
                    group by H.coupon_id";
        $query = $db->query($sql);
        $customerCouponHistory = array();
        if($query->num_rows){
            $result = $query->rows;
            foreach($result as $m){
                $customerCouponHistory[$m['coupon_id']] = $m;
            }
        }

        // 获取优惠券与用户的绑定关系
        // TODO 可缓存预先载入
        // TODO 后台更新缓存
        $customer_sql = "select
                    C.coupon_id, C.name coupon_name, C.type, C.discount, C.total, C.online_payment,
                    CC.times times,  CC.date_end date_end, datediff(CC.date_end, current_date()) overdue_days
                    from oc_coupon C
                    left join oc_coupon_customer CC on C.coupon_id = CC.coupon_id
                    LEFT JOIN oc_coupon_to_warehouse CW ON C.coupon_id = CW.coupon_id
                    where C.status = 1
                    and C.station_id = '".$station_id."'
                    and CC.status = 1 and current_date() between CC.date_start and CC.date_end
                    and CC.customer_id = '".$customer_id."'";

        $coupon_sql = "select
                    C.coupon_id, C.name coupon_name, C.type, C.discount, C.total, C.online_payment,
                    C.times times, C.date_end date_end, datediff(C.date_end, current_date()) overdue_days
                    from oc_coupon C
                    LEFT JOIN oc_coupon_to_warehouse CW ON C.coupon_id = CW.coupon_id
                    where C.status = 1
                    and C.station_id = '".$station_id."'
                    and C.customer_limited = 0
                    and current_date() between C.date_start and C.date_end";

        if(!empty($warehouse_id)){
            $where         = " AND CW.warehouse_id = ".$warehouse_id;
            $customer_sql .= $where;
            $coupon_sql   .= $where;
        }
        $sql = $customer_sql . ' union ' . $coupon_sql;

        $query = $db->query($sql);
        $result = $query->rows;

        $coupons = array();
        $validCouponId = array(0);
        foreach($result as $m){
            $couponUseCount = (array_key_exists($m['coupon_id'],$customerCouponHistory) ? $customerCouponHistory[$m['coupon_id']]['use_count'] : 0);
            $couponValidTimes = $m['times'] - $couponUseCount;

            // 设置有效优惠券的可用次数
            if($couponValidTimes){
                $coupons[$m['coupon_id']] = $m;
                $coupons[$m['coupon_id']]['use_count'] = $couponUseCount;
                $coupons[$m['coupon_id']]['valid_times'] = $couponValidTimes;
                $coupons[$m['coupon_id']]['couponBindProducts'] = array();
                $coupons[$m['coupon_id']]['couponBindProductsValid'] = 0;
                $coupons[$m['coupon_id']]['couponBindProductsTotal'] = 0;
                $coupons[$m['coupon_id']]['ignoredProducts'] = array(); //Ignored while category is assigned
                $coupons[$m['coupon_id']]['globalIgnoredProducts'] = array(); //Ignored without category assignment
                $coupons[$m['coupon_id']]['nonCalcProducts'] = array(); //Ignored without category assignment

                $coupons[$m['coupon_id']]['triggerProductList'] = array(); //记录触发条件的购物车商品，用于订单商品折扣计算
                $coupons[$m['coupon_id']]['triggerProductTotal'] = 0; //记录触发条件的购物车金额
                $validCouponId[] = $m['coupon_id'];
            }
        }

        // 获取购物车商品价格
        // TODO 可缓存
        // TODO 多件折扣商品需要排除
        $cartProductIds = array(0);
        foreach($cartItems as $i=>$v){
            $cartProductIds[] = $i;
        }

        $sql = "SELECT
                p.product_id,round(p.price,2) price, round(if(isnull(ps.price),p.price,ps.price),2) special_price
                FROM oc_product p
                LEFT JOIN oc_product_special ps ON (p.product_id = ps.product_id AND now() BETWEEN ps.date_start AND ps.date_end)
                WHERE p.station_id = '".$station_id."'
                AND p.product_id in (".implode(',',$cartProductIds).")
                GROUP BY p.product_id";
        $query = $db->query($sql);

        $special_data = $product->getAreaProductSpecial($area_id, $warehouse_id, $cartProductIds);
        $cartProductPrice = array();
        if($query->num_rows){
            foreach($query->rows as $m){
                $cartProductPrice[$m['product_id']] = $m['special_price'];

                if(sizeof($special_data) && array_key_exists($m['product_id'], $special_data) && !empty($special_data[$m['product_id']]['special_price'])){
                    $cartProductPrice[$m['product_id']] = $special_data[$m['product_id']]['special_price'];
                }
            }
        }

        //WHY THIS METHOD MISSING SOME DATA???
        //PRODUCT_IDS(5365,7116,7117,7206,7213,7354,8793,8812,8974,8999,9000,9012,9166,9482,9504,9513,9720,9721,9724,9893,9902,9986,9988,9989)
//        $cartProductsPostData = array(
//            'station_id' => $station_id,
//            'data' => array(
//                'keyword' => SEARCH_PRODUCT_PRICE,
//                'products' => $cartProductIds
//            )
//        );
//        $cartProductsRaw = $product->searchProduct($cartProductsPostData);
//        $cartProductPrice = array();
//        foreach($cartProductsRaw as $m){
//            $cartProductPrice[$m['product_id']] = $m['special_price'];
//        }



        // 获取当前有效优惠券排除的商品
        //$sql = "select coupon_id, category_id, product_id from oc_coupon_category_deprecated where coupon_id in (".implode(',',$validCouponId).")";
        $sql = "
            select X.coupon_id, X.category_id, X.product_id from (
            select coupon_id, category_id, product_id from oc_coupon_category_deprecated where coupon_id in (".implode(',',$validCouponId).")
            union
            select A.coupon_id,  A.category_id,  B.product_id from oc_coupon_banned_category A left join oc_product_to_category B on A.category_id = B.category_id
            where A.coupon_id in (".implode(',',$validCouponId).") and B.product_id > 0
            ) X
            group by X.product_id
        ";

        $query = $db->query($sql);
        if($query->num_rows){
            foreach($query->rows as $m){
                $coupons[$m['coupon_id']]['ignoredProducts'][$m['product_id']] = $m['product_id'];
                if($m['category_id'] == 0){
                    $coupons[$m['coupon_id']]['globalIgnoredProducts'][$m['product_id']] = $m['product_id'];
                }
            }
        }

        // 获取当前有效优惠券指定的商品，以及指定品类的商品
        $sql = "select coupon_id, product_id from oc_coupon_product where coupon_id in (".implode(',',$validCouponId).")
                union
                select coupon_id, product_id from oc_coupon_category A left join oc_product_to_category B on A.category_id = B.category_id
where A.coupon_id in (".implode(',',$validCouponId).") and B.product_id > 0";
        $query = $db->query($sql);
        $boundProductList = array();

        foreach($query->rows as $m){
            $boundProductList[$m['coupon_id']][$m['product_id']] = $m['product_id'];
            $coupons[$m['coupon_id']]['couponBindProducts'][] = $m['product_id'];
        }

        foreach($coupons as $m){
            if(sizeof($m['globalIgnoredProducts']) && !sizeof($m['couponBindProducts'])){
                $coupons[$m['coupon_id']]['couponBindProducts'] = array(0); //Occupied
            }

            foreach($cartItems as $product => $qty){
                if( sizeof($m['couponBindProducts']) ){
                    if( !array_key_exists($product,$m['ignoredProducts']) && array_key_exists($product,$boundProductList[$m['coupon_id']]) ){
                        $coupons[$m['coupon_id']]['couponBindProductsValid'] = 1;
                        $coupons[$m['coupon_id']]['couponBindProductsTotal'] += $qty * $cartProductPrice[$product];

                        $coupons[$m['coupon_id']]['triggerProductList'][] = $product;
                        $coupons[$m['coupon_id']]['triggerProductTotal'] += $qty * $cartProductPrice[$product];
                    }
                    else{
                        $coupons[$m['coupon_id']]['nonCalcProducts'][] = $product;
                    }
                }
                else{
                    if( !array_key_exists($product,$m['ignoredProducts']) ){
                        $coupons[$m['coupon_id']]['couponBindProductsValid'] = 1;
                        $coupons[$m['coupon_id']]['couponBindProductsTotal'] += $qty * $cartProductPrice[$product];

                        $coupons[$m['coupon_id']]['triggerProductList'][] = $product;
                        $coupons[$m['coupon_id']]['triggerProductTotal'] += $qty * $cartProductPrice[$product];
                    }
                    else{
                        $coupons[$m['coupon_id']]['nonCalcProducts'][] = $product;
                    }
                }

            }

            if( sizeof($coupons[$m['coupon_id']]['couponBindProducts']) && $coupons[$m['coupon_id']]['couponBindProductsTotal'] < $m['total'] ){
                $coupons[$m['coupon_id']]['couponBindProductsValid'] = 0;
            }
        }


//        if($query->num_rows){
//            foreach($query->rows as $m){
//                if(!array_key_exists($m['product_id'],$coupons[$m['coupon_id']]['ignoredProducts'])){
//                    $coupons[$m['coupon_id']]['couponBindProducts'][] = $m['product_id'];
//                    if(array_key_exists($m['product_id'], $cartItems)){
//                        $coupons[$m['coupon_id']]['couponBindProductsValid'] = 1;
//                        $coupons[$m['coupon_id']]['couponBindProductsTotal'] += (int)$cartItems[$m['product_id']] * (float)$cartProductPrice[$m['product_id']];
//                    }
//                }
//            }
//        }
//
//        //Check if the coupon bound with products, then those products total should meet with the coupon settings
//        foreach($coupons as $m){
//            if(sizeof($m['couponBindProducts']) && $m['couponBindProductsTotal'] < $m['total']){
//                $coupons[$m['coupon_id']]['couponBindProductsValid'] = 0;
//            }
//        }


        //CheckAgentUser
        if($this->checkAgentCustomer($customer_id)){
            $coupons = array();
        }

        return array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
            'return_data' => array(
                'coupons' => $coupons
            )
        );
    }

    function applyCouponToCustomer(array $data){
        global $db, $dbm;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;
        $coupon_id = isset($data['data']['coupon_id']) ? (int)$data['data']['coupon_id'] : 0;

        $returnCode = 'SUCCESS';
        $returnMsg = 'OK';

        //Check if the coupon is valid
        $sql = "select coupon_id from oc_coupon
                where current_date() between date_start and date_end and status = 1 and customer_request = 1
                and coupon_id = '" . $coupon_id . "'";
        $query = $db->query($sql);
        if(!$query->num_rows){
            $returnCode = 'ERROR';
            $returnMsg = '优惠券已过期或不存在';

            return array(
                'return_code' => $returnCode,
                'return_msg' => $returnMsg,
                'return_data' => array()
            );
        }

        //Check if customer already get the coupon
        $sql = "select CC.coupon_id, CC.customer_id from oc_coupon_customer CC
                left join oc_coupon C on CC.coupon_id = C.coupon_id
                where C.customer_request = 1
                and CC.coupon_id = '" . $coupon_id . "'
                and CC.customer_id = '" . $customer_id . "'";
        $query = $db->query($sql);
        if($query->num_rows){
            $returnMsg = '优惠券已获取';

            return array(
                'return_code' => $returnCode,
                'return_msg' => $returnMsg,
                'return_data' => array()
            );
        }

        //Apply the coupon to customer
        $sql = "INSERT INTO `oc_coupon_customer` (`coupon_id`, `customer_id`, `times`, `date_start`, `date_end`, `status`)
                SELECT coupon_id, '".$customer_id."', times, date_start, date_end, status
                FROM oc_coupon
                WHERE current_date() BETWEEN date_start AND date_end AND status = 1 AND customer_request = 1
                AND coupon_id = '" . $coupon_id . "'";
        $dbm->query($sql);

        $returnMsg = '优惠券获取成功';

        return array(
            'return_code' => $returnCode,
            'return_msg' => $returnMsg,
            'return_data' => array()
        );
    }

    function applyTransactionRuleToCustomer(array $data){
        global $db, $dbm;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;
        $customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : 0;
        $transaction_rule_id = isset($data['data']['transaction_rule_id']) ? (int)$data['data']['transaction_rule_id'] : 0;

        $returnCode = 'SUCCESS';
        $returnMsg = 'OK';

        //Check if the coupon is valid
        $sql = "select transaction_rule_id,name,memo from oc_transaction_rule
                where now() between date_start and date_end and status = 1
                and transaction_rule_id = '" . $transaction_rule_id . "'";
        $query = $db->query($sql);
        $transactionRuleInfo = $query->row;
        if(!$query->num_rows){
            $returnCode = 'ERROR';
            $returnMsg = '活动已过期或不存在';

            return array(
                'return_code' => $returnCode,
                'return_msg' => $returnMsg,
                'return_data' => array()
            );
        }

        //Check if customer already get the coupon
        $sql = "select TR.transaction_rule_id, TR.customer_id from oc_transaction_rule_activity TR
                where TR.transaction_rule_id = '" . $transaction_rule_id . "'
                and TR.customer_id = '" . $customer_id . "'";
        $query = $db->query($sql);
        if($query->num_rows){
            $returnMsg = '['.$transactionRuleInfo['name'].']已获取';

            return array(
                'return_code' => $returnCode,
                'return_msg' => $returnMsg,
                'return_data' => array()
            );
        }

        //Apply the coupon to customer
        $dbm->begin();
        $bool = true;
        $sql = "INSERT INTO `oc_customer_transaction` (`customer_id`, `customer_transaction_type_id`, `description`, `amount`, `date_added`)
                SELECT '".$customer_id."', customer_transaction_type_id, memo, amount, now()
                FROM oc_transaction_rule
                WHERE transaction_rule_id = '" . $transaction_rule_id . "'";
        $bool = $bool && $dbm->query($sql);
        $customer_transaction_id = $dbm->getLastId();

        $sql = "INSERT INTO `oc_transaction_rule_activity` (`transaction_rule_id`, `customer_transaction_id`, `customer_id`, `amount`, `date_added`)
                SELECT transaction_rule_id, '".$customer_transaction_id."', '".$customer_id."', amount, now()
                FROM oc_transaction_rule
                WHERE transaction_rule_id = '" . $transaction_rule_id . "'";
        $bool = $bool && $dbm->query($sql);

        if(!$bool) {
            $dbm->rollback();
            $returnMsg = '['.$transactionRuleInfo['name'].']获取失败，请重试';
        }else {
            $dbm->commit();
            $returnMsg = '['.$transactionRuleInfo['name'].']获取成功';
        }

        return array(
            'return_code' => $returnCode,
            'return_msg' => $returnMsg,
            'return_data' => array()
        );
    }

    function getActivityCategory(array $data){
        global $db;

        $station_id = isset($data['station_id']) ? (int)$data['station_id'] : 0;

        $sql = "select act_id, act_name, act_image, sort_order, date_end
        from oc_x_activity
        where now() between date_start and date_end and act_status = 1 and station_id = '".$station_id."'
        order by sort_order desc";

        $query = $db->query($sql);
        $result = $query->rows;

        $activityList = array();
        foreach($result as $m){
            $activityList[$m['act_id']] = $m;
        }

        return array(
            'return_code' => 'SUCCESS',
            'return_msg' => 'OK',
            'return_data' => array(
                'activityList' => $activityList
            )
        );
    }

}

$promotion = new PROMOTION();
?>