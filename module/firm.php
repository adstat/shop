<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');

//Last Day
$db_lastday = new DB(DB_LASTDAY_DRIVER, DB_LASTDAY_HOSTNAME, DB_LASTDAY_USERNAME, DB_LASTDAY_PASSWORD, DB_LASTDAY_DATABASE);

class FIRM{

    function getUserInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $db->escape($data['uid']) : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;
        $key = isset($data['key']) && $data['key'] ? $data['key'] : false;

        if(!$uid){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Not receive uid';
            $returnData = array();
        }
        else{
            $sql = "SELECT * from oc_user where md5(concat(username,'".$key."','".$code."')) ='" . $uid . "'";
            $query = $db->query($sql);
            if($query->row){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'ok';
                $returnData = $query->row;
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;
    }

    function getBdInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $db->escape($data['uid']) : false;
        $code = isset($data['code']) && $data['code'] ? $data['code'] : false;
        $key = isset($data['key']) && $data['key'] ? $data['key'] : false;

        if(!$uid){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Not receive uid';
            $returnData = array();
        }
        else{
            $sql = "SELECT bd_id, bd_name, bd_code, phone from oc_x_bd where md5(concat(crm_username,'".$key."','".$code."')) ='" . $uid . "'";
            $query = $db->query($sql);
            if($query->row){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'ok';
                $returnData = $query->row;
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;
    }

    private function stripSearchKeyword($keyword){
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
    //获取商家名下用户数量
    private function _getCustomerTotal($data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            return null;
        }else{
            $countSql = "select count(customer_id) customer_count from oc_customer where bd_id = '". $bd_id ."' and approved = 1";
            $customerCount = $db->query($countSql)->rows;
            return $customerCount;
        }
    }
    //获取BD管理商家订单总信息
    private function _getOrderTotalInfo($data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            return null;
        }else{
            $orderInfoSeparator = '<br />';
            $ordersTotalSql = "select sum(AAA.week_orders) week_orders,sum(AAA.today_orders) today_orders,sum(AAA.today_sub_total) today_sub_total
                    from
                    (
                      select
                        AA.customer_id,

                        sum(  if(AA.order_status_id not in (3) and AA.amonth = month(date_sub(current_date(), interval 1 month)),1,0)  ) lastmonth_orders,
                        sum(  if(AA.order_status_id not in (3) and AA.amonth = month(date_sub(current_date(), interval 1 month)),AA.sub_total,0)  ) lastmonth_sub_total,

                        sum(  if(AA.order_status_id not in (3) and AA.amonth = month(current_date()),1,0)  ) month_orders,
                        sum(  if(AA.order_status_id not in (3) and AA.amonth = month(current_date()),AA.sub_total,0)  ) month_sub_total,

                        sum(  if(AA.order_status_id not in (3) and AA.aweek = week(date_sub(current_date(), interval 1 week)),1,0)  ) lastweek_orders,
                        sum(  if(AA.order_status_id not in (3) and AA.aweek = week(date_sub(current_date(), interval 1 week)),AA.sub_total,0)  ) lastweek_sub_total,

                        sum(  if(AA.order_status_id not in (3) and AA.aweek = week(current_date()),1,0)  ) week_orders,
                        sum(  if(AA.order_status_id not in (3) and AA.aweek = week(current_date()),AA.sub_total,0)  ) week_sub_total,

                        sum(  if(AA.order_status_id not in (3) and AA.adate = current_date(),1,0)  ) today_orders,
                        sum(  if(AA.order_status_id not in (3) and AA.adate = current_date(), AA.sub_total,0)  ) today_sub_total,

                        group_concat( if(AA.order_status_id not in (3) and AA.adate = current_date(), concat(AA.order_id,'(',round(AA.sub_total,0), '元 ', AA.order_status, ' ', AA.order_payment_status,')'), NULL) Separator '".$orderInfoSeparator."') today_order_info,
                        group_concat( if(AA.order_status_id not in (3) and AA.aweek = week(current_date()), concat(AA.order_id,'(',round(AA.sub_total,0), '元 ', AA.order_payment_status, ' ',right(order_date,5),')'), NULL) Separator '".$orderInfoSeparator."') week_order_info,
                        group_concat( if(AA.order_status_id not in (3) and AA.amonth = month(current_date()), concat(AA.order_id,'(',round(AA.sub_total,0), '元 ', AA.order_payment_status, ' ',right(order_date,5),')'), NULL) Separator '".$orderInfoSeparator."') month_order_info,
                        group_concat( if(AA.order_status_id not in (3) and AA.amonth = month(date_sub(current_date(), interval 1 month)), concat(AA.order_id,'(',round(AA.sub_total,0), '元 ', AA.order_payment_status,' ',right(order_date,5),')'), NULL) Separator '".$orderInfoSeparator."') lastmonth_order_info
                        from(
                             select
                             O.order_id, O.bd_id, date(O.date_added) order_date, O.customer_id,O.sub_total,O.total,O.order_status_id,
                             O.payment_method,OS.name order_status,OPS.name order_payment_status,ODS.name order_deliver_status,date(O.date_added) adate,month(O.date_added) amonth,week(O.date_added) aweek, O.bd_id order_bd_id,
                             C.firstname cust_name,C.merchant_name,C.merchant_address,C.telephone, A.name area_name,
                             C.date_added reg_date
                             from oc_order O
                             left join oc_customer C on O.customer_id = C.customer_id
                             left join oc_x_area A on C.area_id = A.area_id
                             left join oc_order_status OS on O.order_status_id = OS.order_status_id
                             left join oc_order_payment_status OPS on O.order_payment_status_id = OPS.order_payment_status_id
                             left join oc_order_deliver_status ODS on O.order_deliver_status_id = ODS.order_deliver_status_id
                             where date(O.date_added) between date_sub(current_date(), interval 61 day) and current_date()
                             and O.station_id = '".$station_id."' and O.type = 1 and O.bd_id = '".$bd_id."'
                        ) AA
                        group by AA.customer_id
                    ) AAA";
            return $db->query($ordersTotalSql)->rows;
        }
    }
    //内部调用订单信息
    private function _getOrderInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            return null;
        }else{
            $orderInfoSeparator = '<br />';
            $ordersSql = "select
                    AA.customer_id,

                    sum(  if(AA.order_status_id not in (3) and AA.amonth = month(date_sub(current_date(), interval 1 month)),1,0)  ) lastmonth_orders,
                    sum(  if(AA.order_status_id not in (3) and AA.amonth = month(date_sub(current_date(), interval 1 month)),AA.sub_total,0)  ) lastmonth_sub_total,

                    sum(  if(AA.order_status_id not in (3) and AA.amonth = month(current_date()),1,0)  ) month_orders,
                    sum(  if(AA.order_status_id not in (3) and AA.amonth = month(current_date()),AA.sub_total,0)  ) month_sub_total,

                    sum(  if(AA.order_status_id not in (3) and AA.aweek = week(date_sub(current_date(), interval 1 week)),1,0)  ) lastweek_orders,
                    sum(  if(AA.order_status_id not in (3) and AA.aweek = week(date_sub(current_date(), interval 1 week)),AA.sub_total,0)  ) lastweek_sub_total,

                    sum(  if(AA.order_status_id not in (3) and AA.aweek = week(current_date()),1,0)  ) week_orders,
                    sum(  if(AA.order_status_id not in (3) and AA.aweek = week(current_date()),AA.sub_total,0)  ) week_sub_total,

                    sum(  if(AA.order_status_id not in (3) and AA.adate = current_date(),1,0)  ) today_orders,
                    sum(  if(AA.order_status_id not in (3) and AA.adate = current_date(), AA.sub_total,0)  ) today_sub_total,

                    group_concat( if(AA.order_status_id not in (3) and AA.adate = current_date(), concat(AA.order_id,'(',round(AA.sub_total,0), '元 ', AA.order_status, ' ', AA.order_payment_status,')'), NULL) Separator '".$orderInfoSeparator."') today_order_info,
                    group_concat( if(AA.order_status_id not in (3) and AA.aweek = week(current_date()), concat(AA.order_id,'(',round(AA.sub_total,0), '元 ', AA.order_payment_status, ' ',right(order_date,5),')'), NULL) Separator '".$orderInfoSeparator."') week_order_info,
                    group_concat( if(AA.order_status_id not in (3) and AA.amonth = month(current_date()), concat(AA.order_id,'(',round(AA.sub_total,0), '元 ', AA.order_payment_status, ' ',right(order_date,5),')'), NULL) Separator '".$orderInfoSeparator."') month_order_info,
                    group_concat( if(AA.order_status_id not in (3) and AA.amonth = month(date_sub(current_date(), interval 1 month)), concat(AA.order_id,'(',round(AA.sub_total,0), '元 ', AA.order_payment_status,' ',right(order_date,5),')'), NULL) Separator '".$orderInfoSeparator."') lastmonth_order_info
                    from(
                         select
                         O.order_id, O.bd_id, date(O.date_added) order_date, O.customer_id,O.sub_total,O.total,O.order_status_id,
                         O.payment_method,OS.name order_status,OPS.name order_payment_status,ODS.name order_deliver_status,date(O.date_added) adate,month(O.date_added) amonth,week(O.date_added) aweek, O.bd_id order_bd_id,
                         C.firstname cust_name,C.merchant_name,C.merchant_address,C.telephone, A.name area_name,
                         C.date_added reg_date
                         from oc_order O
                         left join oc_customer C on O.customer_id = C.customer_id
                         left join oc_x_area A on C.area_id = A.area_id
                         left join oc_order_status OS on O.order_status_id = OS.order_status_id
                         left join oc_order_payment_status OPS on O.order_payment_status_id = OPS.order_payment_status_id
                         left join oc_order_deliver_status ODS on O.order_deliver_status_id = ODS.order_deliver_status_id
                         where date(O.date_added) between date_sub(current_date(), interval 61 day) and current_date()
                         and O.station_id = '".$station_id."' and O.type = 1 and O.bd_id = '".$bd_id."'
                    ) AA
                    group by AA.customer_id";
            return $db->query($ordersSql);
        }
    }

    private function _getCustomerInfo(){

    }

    private function _getBDRegisterInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            return null;
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    $queryCondition = " and BD.bd_id = '".$keyword."'";
                }else{
                    $queryCondition = " and BD.bd_name like '%".$keyword."%'";
                }
            }

            $sql = "select BD.bd_id, BD.bd_name, if(AA.reg_customers is null, 0,  AA.reg_customers) reg_customers,  if(AA.reg_customer_orders is null, 0, AA.reg_customer_orders) reg_customer_orders
            from xsjb2b.oc_x_bd BD
            left join(
                select
                A.bd_id, count(A.customer_id) reg_customers, sum(if(O.lastorder is null, 0, 1)) reg_customer_orders
                from xsjb2b.oc_customer A
                left join (
                     select A.customer_id, sum(A.sub_total) subtotal, count(A.order_id) orders, min(A.order_id) firstorder,  max(A.order_id) lastorder, min(A.date_added) first_orderdate, max(A.date_added) last_orderdate
                     from xsjb2b.oc_order A
                     where A.order_status_id not in (3)
                     and A.station_id = '".$station_id."'
                     group by A.customer_id order by subtotal desc
                ) O on A.customer_id = O.customer_id
                left join xsjb2b.oc_x_bd C on C.bd_id = A.bd_id
                where date(A.date_added) = date_sub(current_date, interval 1 day)
                group by A.bd_id
                order by A.bd_id
            ) AA on BD.bd_id = AA.bd_id
            where BD.status = 1 and BD.bd_id > 1";
            return $sql;
//            return $db->query($sql);
        }
    }

    private function _getBDOrderInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            return null;
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    $queryCondition = " and BD.bd_id = '".$keyword."'";
                }else{
                    $queryCondition = " and BD.bd_name like '%".$keyword."%'";
                }
            }

            $sql = "select BD.bd_id, BD.bd_name,
            if(AA.orders is null, 0,  AA.orders) orders,
            if(AA.customers is null, 0, AA.customers) customers,
            if(AA.sub_total is null, 0, AA.sub_total) sub_total,
            if(AA.discount_total is null, 0, AA.discount_total) discount_total,
            if(AA.first_orders is null, 0, AA.first_orders) first_orders,
            if(AA.today_regs is null, 0, AA.today_regs) today_regs
            from xsjb2b.oc_x_bd BD
            left join(
                select A.bd_id, count(A.order_id) orders, count(distinct A.customer_id) customers, sum(A.sub_total) sub_total, sum(A.discount_total) discount_total,   sum(if(X.firstorder_fm=1, 1, 0)) first_orders,  sum( if( date(C.date_added) = date(A.date_added), 1, 0 )) today_regs
                from xsjb2b.oc_order A
                left join xsjb2b.oc_order_extend X on A.order_id = X.order_id
                left join xsjb2b.oc_x_bd B on A.bd_id = B.bd_id
                left join xsjb2b.oc_customer C on A.customer_id = C.customer_id
                where A.station_id = '".$station_id."'
                and date(A.date_added) = date_sub(current_date, interval 1 day)
                and A.order_status_id not in (3)
                group by A.bd_id
                order by A.bd_id
            ) AA on BD.bd_id = AA.bd_id
            where BD.status = 1 and BD.bd_id > 1";

            return $sql;
//            return $db->query($sql);
        }
    }

    function getCustomerInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;
        //添加加载数据的控件
        $loadSize = isset($data['data']['loadSize']) && $data['data']['loadSize'] ? $data['data']['loadSize'] : null;
        $lastIndex = isset($data['data']['lastIndex']) && $data['data']['lastIndex'] ? $data['data']['lastIndex'] : 0;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }
        else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and BB.telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and BB.customer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (BB.firstname like '%".$keyword."%' or BB.merchant_name like '%".$keyword."%' or BB.merchant_address like '%".$keyword."%')";
                }
            }

            $orderInfoReturnFields = array('lastmonth_orders','lastmonth_sub_total','month_orders','month_sub_total','lastweek_orders','lastweek_sub_total','week_orders','week_sub_total','today_orders','today_sub_total','today_order_info','week_order_info','month_order_info','lastmonth_order_info');

            $queryOrders = $this->_getOrderInfo($data);
            //订单汇总信息
            $orderTotalInfo = $this->_getOrderTotalInfo($data);
            //获取客户数量
            $customerCount = $this->_getCustomerTotal($data);

            $totalInfo = array();
            $totalInfo['week_orders'] = $orderTotalInfo[0]['week_orders'];
            $totalInfo['today_orders'] = $orderTotalInfo[0]['today_orders'];
            $totalInfo['today_sub_total'] = $orderTotalInfo[0]['today_sub_total'];
            $totalInfo['customer_count'] = $customerCount[0]['customer_count'];

            $customerInfoReturnFields = array('customer_id','cust_name','merchant_name','merchant_address','telephone','reg_date','reg_date_format','reg_week','reg_thisweek');
            $customerSql = "select BB.customer_id,BB.firstname cust_name, BB.merchant_name, BB.merchant_address, BB.telephone,
                    BB.date_added reg_date, date_format(BB.date_added, '%Y%m%d') reg_date_format,
                    week(BB.date_added) reg_week, if( week(BB.date_added) = week(now()),  1, 0) reg_thisweek, count(O.order_id) orders
                    from oc_customer BB
                    left join oc_order O on BB.customer_id = O.customer_id and date(O.date_added) = current_date()
                    where BB.approved = 1 and BB.bd_id = '".$bd_id."'" . $queryCondition . "
                    group by BB.customer_id
                    order by orders desc
                    ";
            if(!$keyword){
                $customerSql .= " limit $lastIndex,$loadSize";
            }

            $queryCustomers = $db->query($customerSql);

            if(sizeof($queryCustomers->rows)){
                $ordersInfo = array();
                foreach($queryOrders->rows as $m){
                    $ordersInfo[$m['customer_id']] = $m;

                    //$ordersInfo[$m['customer_id']]['today_order_info'] = explode($orderInfoSeparator,$m['today_order_info']);
                    //$ordersInfo[$m['customer_id']]['week_order_info']  = explode($orderInfoSeparator,$m['week_order_info']);
                    //$ordersInfo[$m['customer_id']]['month_order_info']  = explode($orderInfoSeparator,$m['month_order_info']);
                    //$ordersInfo[$m['customer_id']]['lastmonth_order_info']  = explode($orderInfoSeparator,$m['lastmonth_order_info']);
                }

                //TODO, Bad performance
                $bdCustomerInfo = array();
                $bdCustomerInfoToReturn = array();
                foreach($queryCustomers->rows as $m){
                    if(array_key_exists($m['customer_id'], $ordersInfo)){
                        $bdCustomerInfoIndex = (10000+(int)$ordersInfo[$m['customer_id']]['today_orders']).'_'.$m['customer_id'];
                        foreach($ordersInfo[$m['customer_id']] as $k=>$v){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $v;
                        }
                    }
                    else{
                        $bdCustomerInfoIndex = '10000_'.$m['customer_id'];
                        foreach($orderInfoReturnFields as $k){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = 0;
                        }
                    }

                    foreach($customerInfoReturnFields as $k){
                        $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $m[$k];
                    }
                }
                krsort($bdCustomerInfo);

                foreach($bdCustomerInfo as $m){
                    $bdCustomerInfoToReturn[] = $m;
                }

                //TODO, Simplier but not sort
//                $bdCustomerInfo = array();
//                foreach($queryCustomers->rows as $m){
//                    $bdCustomerInfo[$m['customer_id']] = $m;
//                    if(array_key_exists($m['customer_id'], $ordersInfo)){
//                        foreach($ordersInfo[$m['customer_id']] as $k=>$v){
//                            $bdCustomerInfo[$m['customer_id']][$k] = $v;
//                        }
//                    }
//                }

            }

            if(sizeof($queryOrders->rows)){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                //$returnData = $queryCustomers->rows;
                $returnData = $bdCustomerInfoToReturn;
            }
            else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NO RECORD';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData,
            'return_total' => $totalInfo
        );

        return $return;
    }

    function getSleepCustomerInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }else{
            //从注册开始下单的客户
            $orderNumberSql = "select count(*) as order_count, O.customer_id
                from oc_order O
                left join oc_customer C on O.customer_id = C.customer_id
                where C.bd_id = '".$bd_id."' and C.approved = 1
                and O.order_status_id != ".CANCELLED_ORDER_STATUS."
                and O.station_id = '".$station_id."'
                group by O.customer_id
                having order_count > 0";

            $customer = $db->query($orderNumberSql)->rows;
            $customer_id = array();
            foreach($customer as $value){
                $customer_id[] = $value['customer_id'];
            }
            //获得所有客户的id
            $customerSql = "select customer_id from oc_customer where approved = 1 and bd_id = '".$bd_id."'";
            $customerTotal = $db->query($customerSql)->rows;
            $customer_total_id = array();
            foreach($customerTotal as $value){
                $customer_total_id[] = $value['customer_id'];
            }
            //获得从未下单的客户
            $customer_sleep_id = array_merge(array_diff($customer_total_id,$customer_id));

            $customer_ids = implode(',',$customer_sleep_id);

            if($customer_ids){
                $orderInfoReturnFields = array('lastmonth_orders','lastmonth_sub_total','month_orders','month_sub_total','lastweek_orders','lastweek_sub_total','week_orders','week_sub_total','today_orders','today_sub_total','today_order_info','week_order_info','month_order_info','lastmonth_order_info');

                $queryOrders = $this->_getOrderInfo($data);

                $customerInfoReturnFields = array('customer_id','cust_name','merchant_name','merchant_address','telephone','reg_date','reg_date_format','reg_week','reg_thisweek');
                $customerSql = "select BB.customer_id,BB.firstname cust_name, BB.merchant_name, BB.merchant_address, BB.telephone,
                    BB.date_added reg_date, date_format(BB.date_added, '%Y%m%d') reg_date_format,
                    week(BB.date_added) reg_week, if( week(BB.date_added) = week(now()),  1, 0) reg_thisweek
                    from oc_customer BB
                    where BB.bd_id = '".$bd_id."' and BB.customer_id in ($customer_ids)
                    ";
                $queryCustomers = $db->query($customerSql);

                if(sizeof($queryCustomers->rows)){
                    $ordersInfo = array();
                    foreach($queryOrders->rows as $m){
                        $ordersInfo[$m['customer_id']] = $m;
                    }

                    //TODO, Bad performance
                    $bdCustomerInfo = array();
                    $bdCustomerInfoToReturn = array();
                    foreach($queryCustomers->rows as $m){
                        if(array_key_exists($m['customer_id'], $ordersInfo)){
                            $bdCustomerInfoIndex = (10000+(int)$ordersInfo[$m['customer_id']]['today_orders']).'_'.$m['customer_id'];
                            foreach($ordersInfo[$m['customer_id']] as $k=>$v){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $v;
                            }
                        }
                        else{
                            $bdCustomerInfoIndex = '10000_'.$m['customer_id'];
                            foreach($orderInfoReturnFields as $k){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = 0;
                            }
                        }

                        foreach($customerInfoReturnFields as $k){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $m[$k];
                        }
                    }
                    krsort($bdCustomerInfo);

                    foreach($bdCustomerInfo as $m){
                        $bdCustomerInfoToReturn[] = $m;
                    }
                }

                if(sizeof($queryOrders->rows)){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    //$returnData = $queryCustomers->rows;
                    $returnData = $bdCustomerInfoToReturn;
                }
                else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NO RECORD';
                    $returnData = array();
                }
            }else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NO RECORD';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;

    }

    function getWeekInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and BB.telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and BB.customer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (BB.firstname like '%".$keyword."%' or BB.merchant_name like '%".$keyword."%' or BB.merchant_address like '%".$keyword."%')";
                }
            }
            //取得本周下订单的客户
            $orderSetCustomerSql = "select customer_id from oc_order where (date(date_added) between date_sub(subdate(current_date(),date_format(curdate(),'%w')-1), interval 7 day) and current_date()) and bd_id = '".$bd_id."' and station_id = '".$station_id."'";
            $customerSet = $db->query($orderSetCustomerSql)->rows;
            $order_have_customer = array();
            foreach($customerSet as $value){
                $order_have_customer[] = $value['customer_id'];
            }
            //取得该bd的所有客户
            $customerSql = "select customer_id from oc_customer where bd_id = '".$bd_id."'";
            $customer = $db->query($customerSql)->rows;
            $all_customer = array();
            foreach($customer as $value){
                $all_customer[] = $value['customer_id'];
            }
            //取得本周未下订单客户
            $customer_without_order = array_merge(array_diff($all_customer,$order_have_customer));
            $customer_order_no_id = implode(',',$customer_without_order);

            if($customer_order_no_id){
                //然后按照列表的原有形式，把本后未下订单的客户列出来
                $orderInfoReturnFields = array('lastmonth_orders','lastmonth_sub_total','month_orders','month_sub_total','lastweek_orders','lastweek_sub_total','week_orders','week_sub_total','today_orders','today_sub_total','today_order_info','week_order_info','month_order_info','lastmonth_order_info');
                $queryOrders = $this->_getOrderInfo($data);

                $customerInfoReturnFields = array('customer_id','cust_name','merchant_name','merchant_address','telephone','reg_date','reg_date_format','reg_week','reg_thisweek');
                $customerSql = "select BB.customer_id,BB.firstname cust_name, BB.merchant_name, BB.merchant_address, BB.telephone,
                    BB.date_added reg_date, date_format(BB.date_added, '%Y%m%d') reg_date_format,
                    week(BB.date_added) reg_week, if( week(BB.date_added) = week(now()),  1, 0) reg_thisweek
                    from oc_customer BB
                    where BB.approved = 1 and BB.customer_id in($customer_order_no_id) and BB.bd_id = '".$bd_id."'" . $queryCondition . "
                    ";
                $queryCustomers = $db->query($customerSql);

                if(sizeof($queryCustomers->rows)){
                    $ordersInfo = array();
                    foreach($queryOrders->rows as $m){
                        $ordersInfo[$m['customer_id']] = $m;
                    }

                    //TODO, Bad performance
                    $bdCustomerInfo = array();
                    $bdCustomerInfoToReturn = array();
                    foreach($queryCustomers->rows as $m){
                        if(array_key_exists($m['customer_id'], $ordersInfo)){
                            $bdCustomerInfoIndex = (10000+(int)$ordersInfo[$m['customer_id']]['today_orders']).'_'.$m['customer_id'];
                            foreach($ordersInfo[$m['customer_id']] as $k=>$v){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $v;
                            }
                        }
                        else{
                            $bdCustomerInfoIndex = '10000_'.$m['customer_id'];
                            foreach($orderInfoReturnFields as $k){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = 0;
                            }
                        }

                        foreach($customerInfoReturnFields as $k){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $m[$k];
                        }
                    }
                    krsort($bdCustomerInfo);

                    foreach($bdCustomerInfo as $m){
                        $bdCustomerInfoToReturn[] = $m;
                    }
                }

                if(sizeof($queryOrders->rows)){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $bdCustomerInfoToReturn;
                }
                else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NO RECORD';
                    $returnData = array();
                }

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg'  => $returnMessage,
                    'return_data' => $returnData
                );

            }else{
                $return = array(
                    'return_code' => API_RETURN_SUCCESS,
                    'return_msg'  => 'NO RECORD',
                    'return_data' => array()
                );
            }
        }

        return $return;

    }

    function getLastWeekInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and BB.telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and BB.customer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (BB.firstname like '%".$keyword."%' or BB.merchant_name like '%".$keyword."%' or BB.merchant_address like '%".$keyword."%')";
                }
            }
            //取得上周下订单的客户
//            $orderSetCustomerSql = "select customer_id from oc_order where (date(date_added) between date_sub(subdate(current_date(),date_format(curdate(),'%w')-1), interval 14 day) and date_sub(subdate(current_date(),date_format(curdate(),'%w')-1), interval 7 day)) and bd_id = '".$bd_id."'";
            $orderSetCustomerSql = "select customer_id from oc_order where week(date(date_added)) = week(now())-1 and year(now()) = year(date_added) and bd_id = '".$bd_id."' and station_id = '".$station_id."'";

            $customerSet = $db->query($orderSetCustomerSql)->rows;
            $order_have_customer = array();
            foreach($customerSet as $value){
                $order_have_customer[] = $value['customer_id'];
            }
            //取得该bd的所有客户
            $customerSql = "select customer_id from oc_customer where bd_id = '".$bd_id."'";
            $customer = $db->query($customerSql)->rows;
            $all_customer = array();
            foreach($customer as $value){
                $all_customer[] = $value['customer_id'];
            }
            //取得上周未下订单客户
            $customer_without_order = array_merge(array_diff($all_customer,$order_have_customer));
            $customer_order_no_id = implode(',',$customer_without_order);

            if($customer_order_no_id){
                //然后按照列表的原有形式，把本后未下订单的客户列出来
                $orderInfoReturnFields = array('lastmonth_orders','lastmonth_sub_total','month_orders','month_sub_total','lastweek_orders','lastweek_sub_total','week_orders','week_sub_total','today_orders','today_sub_total','today_order_info','week_order_info','month_order_info','lastmonth_order_info');
                $queryOrders = $this->_getOrderInfo($data);

                $customerInfoReturnFields = array('customer_id','cust_name','merchant_name','merchant_address','telephone','reg_date','reg_date_format','reg_week','reg_thisweek');
                $customerSql = "select BB.customer_id,BB.firstname cust_name, BB.merchant_name, BB.merchant_address, BB.telephone,
                    BB.date_added reg_date, date_format(BB.date_added, '%Y%m%d') reg_date_format,
                    week(BB.date_added) reg_week, if( week(BB.date_added) = week(now()),  1, 0) reg_thisweek
                    from oc_customer BB
                    where BB.approved = 1 and BB.customer_id in($customer_order_no_id) and BB.bd_id = '".$bd_id."'" . $queryCondition . "
                    ";
                $queryCustomers = $db->query($customerSql);

                if(sizeof($queryCustomers->rows)){
                    $ordersInfo = array();
                    foreach($queryOrders->rows as $m){
                        $ordersInfo[$m['customer_id']] = $m;
                    }

                    //TODO, Bad performance
                    $bdCustomerInfo = array();
                    $bdCustomerInfoToReturn = array();
                    foreach($queryCustomers->rows as $m){
                        if(array_key_exists($m['customer_id'], $ordersInfo)){
                            $bdCustomerInfoIndex = (10000+(int)$ordersInfo[$m['customer_id']]['today_orders']).'_'.$m['customer_id'];
                            foreach($ordersInfo[$m['customer_id']] as $k=>$v){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $v;
                            }
                        }
                        else{
                            $bdCustomerInfoIndex = '10000_'.$m['customer_id'];
                            foreach($orderInfoReturnFields as $k){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = 0;
                            }
                        }

                        foreach($customerInfoReturnFields as $k){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $m[$k];
                        }
                    }
                    krsort($bdCustomerInfo);

                    foreach($bdCustomerInfo as $m){
                        $bdCustomerInfoToReturn[] = $m;
                    }
                }

                if(sizeof($queryOrders->rows)){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $bdCustomerInfoToReturn;
                }
                else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NO RECORD';
                    $returnData = array();
                }

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg'  => $returnMessage,
                    'return_data' => $returnData
                );

            }else{
                $return = array(
                    'return_code' => API_RETURN_SUCCESS,
                    'return_msg'  => 'NO RECORD',
                    'return_data' => array()
                );
            }
        }

        return $return;

    }
    function getLastMonthInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and BB.telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and BB.customer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (BB.firstname like '%".$keyword."%' or BB.merchant_name like '%".$keyword."%' or BB.merchant_address like '%".$keyword."%')";
                }
            }
            //取得上月下订单的客户
            $orderSetCustomerSql = "select customer_id from oc_order where (date(date_added) between date_sub(date_sub(date_format(now(),'%y-%m-%d'),interval extract(day from now())-1 day),interval 1 month) and date_sub(date_sub(date_format(now(),'%y-%m-%d'),interval extract(day from now()) day),interval 0 month)) and bd_id = '".$bd_id."' and station_id = '".$station_id."'";
            $customerSet = $db->query($orderSetCustomerSql)->rows;
            $order_have_customer = array();
            foreach($customerSet as $value){
                $order_have_customer[] = $value['customer_id'];
            }
            //取得该bd的所有客户
            $customerSql = "select customer_id from oc_customer where bd_id = '".$bd_id."'";
            $customer = $db->query($customerSql)->rows;
            $all_customer = array();
            foreach($customer as $value){
                $all_customer[] = $value['customer_id'];
            }
            //取得上月未下订单客户
            $customer_without_order = array_merge(array_diff($all_customer,$order_have_customer));
            $customer_order_no_id = implode(',',$customer_without_order);

            if($customer_order_no_id){
                //然后按照列表的原有形式，把本后未下订单的客户列出来
                $orderInfoReturnFields = array('lastmonth_orders','lastmonth_sub_total','month_orders','month_sub_total','lastweek_orders','lastweek_sub_total','week_orders','week_sub_total','today_orders','today_sub_total','today_order_info','week_order_info','month_order_info','lastmonth_order_info');
                $queryOrders = $this->_getOrderInfo($data);

                $customerInfoReturnFields = array('customer_id','cust_name','merchant_name','merchant_address','telephone','reg_date','reg_date_format','reg_week','reg_thisweek');
                $customerSql = "select BB.customer_id,BB.firstname cust_name, BB.merchant_name, BB.merchant_address, BB.telephone,
                    BB.date_added reg_date, date_format(BB.date_added, '%Y%m%d') reg_date_format,
                    week(BB.date_added) reg_week, if( week(BB.date_added) = week(now()),  1, 0) reg_thisweek
                    from oc_customer BB
                    where BB.approved = 1 and BB.customer_id in($customer_order_no_id) and BB.bd_id = '".$bd_id."'" . $queryCondition . "
                    ";
                $queryCustomers = $db->query($customerSql);

                if(sizeof($queryCustomers->rows)){
                    $ordersInfo = array();
                    foreach($queryOrders->rows as $m){
                        $ordersInfo[$m['customer_id']] = $m;
                    }

                    //TODO, Bad performance
                    $bdCustomerInfo = array();
                    $bdCustomerInfoToReturn = array();
                    foreach($queryCustomers->rows as $m){
                        if(array_key_exists($m['customer_id'], $ordersInfo)){
                            $bdCustomerInfoIndex = (10000+(int)$ordersInfo[$m['customer_id']]['today_orders']).'_'.$m['customer_id'];
                            foreach($ordersInfo[$m['customer_id']] as $k=>$v){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $v;
                            }
                        }
                        else{
                            $bdCustomerInfoIndex = '10000_'.$m['customer_id'];
                            foreach($orderInfoReturnFields as $k){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = 0;
                            }
                        }

                        foreach($customerInfoReturnFields as $k){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $m[$k];
                        }
                    }
                    krsort($bdCustomerInfo);

                    foreach($bdCustomerInfo as $m){
                        $bdCustomerInfoToReturn[] = $m;
                    }
                }

                if(sizeof($queryOrders->rows)){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $bdCustomerInfoToReturn;
                }
                else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NO RECORD';
                    $returnData = array();
                }

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg'  => $returnMessage,
                    'return_data' => $returnData
                );

            }else{
                $return = array(
                    'return_code' => API_RETURN_SUCCESS,
                    'return_msg'  => 'NO RECORD',
                    'return_data' => array()
                );
            }
        }

        return $return;

    }

    function getOneOrder(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and BB.telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and BB.customer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (BB.firstname like '%".$keyword."%' or BB.merchant_name like '%".$keyword."%' or BB.merchant_address like '%".$keyword."%')";
                }
            }
            //取得本周下一单的客户
            $orderNumberSql = "select count(*) as order_count,customer_id
                from oc_order
                where week(date(date_added)) = week(current_date()) and year(date(date_added)) = year(current_date())
                and bd_id = '".$bd_id."'
                and order_status_id != ".CANCELLED_ORDER_STATUS."
                and station_id = '".$station_id."'
                group by customer_id
                having order_count = 1";

            $customer = $db->query($orderNumberSql)->rows;
            $customer_id = array();
            foreach($customer as $value){
                $customer_id[] = $value['customer_id'];
            }

            $customer_ids = implode(',',$customer_id);

            if($customer_ids){
                //然后按照列表的原有形式，把本后未下订单的客户列出来
                $orderInfoReturnFields = array('lastmonth_orders','lastmonth_sub_total','month_orders','month_sub_total','lastweek_orders','lastweek_sub_total','week_orders','week_sub_total','today_orders','today_sub_total','today_order_info','week_order_info','month_order_info','lastmonth_order_info');
                $queryOrders = $this->_getOrderInfo($data);

                $customerInfoReturnFields = array('customer_id','cust_name','merchant_name','merchant_address','telephone','reg_date','reg_date_format','reg_week','reg_thisweek');
                $customerSql = "select BB.customer_id,BB.firstname cust_name, BB.merchant_name, BB.merchant_address, BB.telephone,
                    BB.date_added reg_date, date_format(BB.date_added, '%Y%m%d') reg_date_format,
                    week(BB.date_added) reg_week, if( week(BB.date_added) = week(now()),  1, 0) reg_thisweek
                    from oc_customer BB
                    where BB.approved = 1 and BB.customer_id in($customer_ids) and BB.bd_id = '".$bd_id."'" . $queryCondition . "
                    ";
                $queryCustomers = $db->query($customerSql);

                if(sizeof($queryCustomers->rows)){
                    $ordersInfo = array();
                    foreach($queryOrders->rows as $m){
                        $ordersInfo[$m['customer_id']] = $m;
                    }

                    //TODO, Bad performance
                    $bdCustomerInfo = array();
                    $bdCustomerInfoToReturn = array();
                    foreach($queryCustomers->rows as $m){
                        if(array_key_exists($m['customer_id'], $ordersInfo)){
                            $bdCustomerInfoIndex = (10000+(int)$ordersInfo[$m['customer_id']]['today_orders']).'_'.$m['customer_id'];
                            foreach($ordersInfo[$m['customer_id']] as $k=>$v){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $v;
                            }
                        }
                        else{
                            $bdCustomerInfoIndex = '10000_'.$m['customer_id'];
                            foreach($orderInfoReturnFields as $k){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = 0;
                            }
                        }

                        foreach($customerInfoReturnFields as $k){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $m[$k];
                        }
                    }
                    krsort($bdCustomerInfo);

                    foreach($bdCustomerInfo as $m){
                        $bdCustomerInfoToReturn[] = $m;
                    }
                }

                if(sizeof($queryOrders->rows)){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $bdCustomerInfoToReturn;
                }
                else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NO RECORD';
                    $returnData = array();
                }

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg'  => $returnMessage,
                    'return_data' => $returnData
                );
            }else{
                $return = array(
                    'return_code' => API_RETURN_SUCCESS,
                    'return_msg'  => 'NO RECORD',
                    'return_data' => array()
                );
            }
        }

        return $return;

    }

    function getTwoOrder(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and BB.telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and BB.customer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (BB.firstname like '%".$keyword."%' or BB.merchant_name like '%".$keyword."%' or BB.merchant_address like '%".$keyword."%')";
                }
            }
            //取得本周下两单的客户
            $orderNumberSql = "select count(*) as order_count,customer_id
                from oc_order
                where week(date(date_added)) = week(current_date()) and year(date(date_added)) = year(current_date())
                and bd_id = '".$bd_id."'
                and order_status_id != ".CANCELLED_ORDER_STATUS."
                and station_id = '".$station_id."'
                group by customer_id
                having order_count = 2";

            $customer = $db->query($orderNumberSql)->rows;
            $customer_id = array();
            foreach($customer as $value){
                $customer_id[] = $value['customer_id'];
            }

            $customer_ids = implode(',',$customer_id);

            if($customer_ids){
                //然后按照列表的原有形式，把本后未下订单的客户列出来
                $orderInfoReturnFields = array('lastmonth_orders','lastmonth_sub_total','month_orders','month_sub_total','lastweek_orders','lastweek_sub_total','week_orders','week_sub_total','today_orders','today_sub_total','today_order_info','week_order_info','month_order_info','lastmonth_order_info');
                $queryOrders = $this->_getOrderInfo($data);

                $customerInfoReturnFields = array('customer_id','cust_name','merchant_name','merchant_address','telephone','reg_date','reg_date_format','reg_week','reg_thisweek');
                $customerSql = "select BB.customer_id,BB.firstname cust_name, BB.merchant_name, BB.merchant_address, BB.telephone,
                    BB.date_added reg_date, date_format(BB.date_added, '%Y%m%d') reg_date_format,
                    week(BB.date_added) reg_week, if( week(BB.date_added) = week(now()),  1, 0) reg_thisweek
                    from oc_customer BB
                    where BB.approved = 1 and BB.customer_id in($customer_ids) and BB.bd_id = '".$bd_id."'" . $queryCondition . "
                    ";
                $queryCustomers = $db->query($customerSql);

                if(sizeof($queryCustomers->rows)){
                    $ordersInfo = array();
                    foreach($queryOrders->rows as $m){
                        $ordersInfo[$m['customer_id']] = $m;
                    }

                    //TODO, Bad performance
                    $bdCustomerInfo = array();
                    $bdCustomerInfoToReturn = array();
                    foreach($queryCustomers->rows as $m){
                        if(array_key_exists($m['customer_id'], $ordersInfo)){
                            $bdCustomerInfoIndex = (10000+(int)$ordersInfo[$m['customer_id']]['today_orders']).'_'.$m['customer_id'];
                            foreach($ordersInfo[$m['customer_id']] as $k=>$v){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $v;
                            }
                        }
                        else{
                            $bdCustomerInfoIndex = '10000_'.$m['customer_id'];
                            foreach($orderInfoReturnFields as $k){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = 0;
                            }
                        }

                        foreach($customerInfoReturnFields as $k){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $m[$k];
                        }
                    }
                    krsort($bdCustomerInfo);

                    foreach($bdCustomerInfo as $m){
                        $bdCustomerInfoToReturn[] = $m;
                    }
                }

                if(sizeof($queryOrders->rows)){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $bdCustomerInfoToReturn;
                }
                else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NO RECORD';
                    $returnData = array();
                }

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg'  => $returnMessage,
                    'return_data' => $returnData
                );
            }else{
                $return = array(
                    'return_code' => API_RETURN_SUCCESS,
                    'return_msg'  => 'NO RECORD',
                    'return_data' => array()
                );
            }
        }

        return $return;

    }

    function getThreeOrder(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and BB.telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and BB.customer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (BB.firstname like '%".$keyword."%' or BB.merchant_name like '%".$keyword."%' or BB.merchant_address like '%".$keyword."%')";
                }
            }
            //取得本周下一单的客户
            $orderNumberSql = "select count(*) as order_count,customer_id
                from oc_order
                where week(date(date_added)) = week(current_date()) and year(date(date_added)) = year(current_date())
                and bd_id = '".$bd_id."'
                and order_status_id != ".CANCELLED_ORDER_STATUS."
                and station_id = '".$station_id."'
                group by customer_id
                having order_count > 2";

            $customer = $db->query($orderNumberSql)->rows;
            $customer_id = array();
            foreach($customer as $value){
                $customer_id[] = $value['customer_id'];
            }

            $customer_ids = implode(',',$customer_id);

            if($customer_ids){
                //然后按照列表的原有形式，把本后未下订单的客户列出来
                $orderInfoReturnFields = array('lastmonth_orders','lastmonth_sub_total','month_orders','month_sub_total','lastweek_orders','lastweek_sub_total','week_orders','week_sub_total','today_orders','today_sub_total','today_order_info','week_order_info','month_order_info','lastmonth_order_info');
                $queryOrders = $this->_getOrderInfo($data);

                $customerInfoReturnFields = array('customer_id','cust_name','merchant_name','merchant_address','telephone','reg_date','reg_date_format','reg_week','reg_thisweek');
                $customerSql = "select BB.customer_id,BB.firstname cust_name, BB.merchant_name, BB.merchant_address, BB.telephone,
                    BB.date_added reg_date, date_format(BB.date_added, '%Y%m%d') reg_date_format,
                    week(BB.date_added) reg_week, if( week(BB.date_added) = week(now()),  1, 0) reg_thisweek
                    from oc_customer BB
                    where BB.approved = 1 and BB.customer_id in($customer_ids) and BB.bd_id = '".$bd_id."'" . $queryCondition . "
                    ";
                $queryCustomers = $db->query($customerSql);

                if(sizeof($queryCustomers->rows)){
                    $ordersInfo = array();
                    foreach($queryOrders->rows as $m){
                        $ordersInfo[$m['customer_id']] = $m;
                    }

                    //TODO, Bad performance
                    $bdCustomerInfo = array();
                    $bdCustomerInfoToReturn = array();
                    foreach($queryCustomers->rows as $m){
                        if(array_key_exists($m['customer_id'], $ordersInfo)){
                            $bdCustomerInfoIndex = (10000+(int)$ordersInfo[$m['customer_id']]['today_orders']).'_'.$m['customer_id'];
                            foreach($ordersInfo[$m['customer_id']] as $k=>$v){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $v;
                            }
                        }
                        else{
                            $bdCustomerInfoIndex = '10000_'.$m['customer_id'];
                            foreach($orderInfoReturnFields as $k){
                                $bdCustomerInfo[$bdCustomerInfoIndex][$k] = 0;
                            }
                        }

                        foreach($customerInfoReturnFields as $k){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $m[$k];
                        }
                    }
                    krsort($bdCustomerInfo);

                    foreach($bdCustomerInfo as $m){
                        $bdCustomerInfoToReturn[] = $m;
                    }
                }

                if(sizeof($queryOrders->rows)){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $bdCustomerInfoToReturn;
                }
                else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NO RECORD';
                    $returnData = array();
                }

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg'  => $returnMessage,
                    'return_data' => $returnData
                );
            }else{
                $return = array(
                    'return_code' => API_RETURN_SUCCESS,
                    'return_msg'  => 'NO RECORD',
                    'return_data' => array()
                );
            }
        }

        return $return;

    }

    function addRealVisit(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        //表单填写数据
        $customer_id = isset($data['data'][0]['value']) && $data['data'][0]['value'] ? $data['data'][0]['value'] : '';
        $merchant_name = isset($data['data'][1]['value']) && $data['data'][1]['value'] ? $data['data'][1]['value'] : '';
        $merchant_address = isset($data['data'][2]['value']) && $data['data'][1]['value'] ? $data['data'][2]['value'] : "";
        $telephone = isset($data['data'][3]['value']) && $data['data'][3]['value'] ? $data['data'][3]['value'] : "";
        $visit_result_id = isset($data['data'][4]['value']) && $data['data'][3]['value'] ? $data['data'][4]['value'] : "";
        $visit_reason = isset($data['visit_reason']) && $data['visit_reason'] ? $data['visit_reason'] : '';
        $visit_date = date('Y-m-d H:i:s');

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            //保存表单
            if($customer_id){
                $saveSql = "INSERT INTO `oc_x_bd_visit_history`
                    (`customer_id`,`merchant_name`,`merchant_address`,`telephone`,`visit_result_id`,`visit_id`,`bd_id`,`visit_date`,`comment`)
                    VALUES('$customer_id','$merchant_name','$merchant_address','$telephone','$visit_result_id','0','$bd_id','$visit_date','$visit_reason')";

                $query = $db->query($saveSql);

                if($query){
                    #成功
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                    );
                }else{
                    #报错
                    $returnCode = API_RETURN_ERROR;
                    $returnMessage = 'FAIL';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                    );
                }
            }else{
                #报要填写完整的信息
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'UNFULL';
                $return = array(
                    'return_code' => $returnCode,
                    'return_msg' => $returnMessage,
                );
            }
        }
        return $return;
    }

    function addVisitPlan(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        //表单填写数据
        $customer_id = isset($data['data'][0]['value']) && $data['data'][0]['value'] ? $data['data'][0]['value'] : '';
        $merchant_name = isset($data['data'][1]['value']) && $data['data'][1]['value'] ? $data['data'][1]['value'] : '';
        $merchant_address = isset($data['data'][2]['value']) && $data['data'][1]['value'] ? $data['data'][2]['value'] : "";
        $telephone = isset($data['data'][3]['value']) && $data['data'][3]['value'] ? $data['data'][3]['value'] : "";
        $visit_plan_date = isset($data['data'][4]['value']) && $data['data'][3]['value'] ? $data['data'][4]['value'] : "";
        $visit_reason = isset($data['visit_reason']) && $data['visit_reason'] ? $data['visit_reason'] : '';

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            //保存表单
            if($customer_id && $visit_plan_date){
                $saveSql = "INSERT INTO `oc_x_bd_visit`
                    (`customer_id`,`merchant_name`,`merchant_address`,`telephone`,`visit_plan_date`,`visit_reason`,`bd_id`)
                    VALUES('$customer_id','$merchant_name','$merchant_address','$telephone','$visit_plan_date','$visit_reason','$bd_id')";

                $query = $db->query($saveSql);

                if($query){
                    #成功
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                    );
                }else{
                    #报错
                    $returnCode = API_RETURN_ERROR;
                    $returnMessage = 'FAIL';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                    );
                }
            }else{
                #报要填写完整的信息
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'UNFULL';
                $return = array(
                    'return_code' => $returnCode,
                    'return_msg' => $returnMessage,
                );
            }
        }

        return $return;

    }

    function getVisitPlan(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg' => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and customer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (merchant_name like '%".$keyword."%'or merchant_address like '%".$keyword."%')";
                }
            }

            //获取之前登记的拜访计划表的内容
            $visitPlanSql = "select visit_id,customer_id,merchant_name,merchant_address,telephone,visit_reason
                from oc_x_bd_visit where bd_id = '".$bd_id."' and is_visited = 0 " . $queryCondition . "
                ";

            $visitInfo = $db->query($visitPlanSql);
            if($visitInfo){
                if($visitInfo->rows){
                    #接口返回数据给BD页面
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $visitInfo->rows;

                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData
                    );
                }else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NULL';
                    $returnData = array();

                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData
                    );
                }
            }else{
                #执行失败
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'FAIL';
                $returnData = array();

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg' => $returnMessage,
                    'return_data' => $returnData
                );
            }
        }

        return $return;

    }

    function confirmVisit(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $visit_id = isset($data['visit_id']) && $data['visit_id'] ? $data['visit_id'] : '';
        $visit_result_id = isset($data['data']['visit_result_id']) && $data['data']['visit_result_id'] ? $data['data']['visit_result_id'] : 0 ;
        $visit_reason = isset($data['visit_reason']) && $data['visit_reason'] ? $data['visit_reason'] : '';

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            if($visit_id){
                #执行确认动作
                $confirmSql = "update oc_x_bd_visit set is_visited = 1 where visit_id = '".$visit_id."'";
                $confirm = $db->query($confirmSql);
                if($confirm){
                    #执行成功
                    $visitHistoryInfo = "select customer_id,merchant_name,merchant_address,telephone,bd_id from oc_x_bd_visit where visit_id = '".$visit_id."'";
                    $visitHistory = $db->query($visitHistoryInfo)->rows;

                    $customer_id =  $visitHistory[0]['customer_id'];
                    $merchant_name = $visitHistory[0]['merchant_name'];
                    $merchant_address = $visitHistory[0]['merchant_address'];
                    $telephone = $visitHistory[0]['telephone'];
                    $bd_id = $visitHistory[0]['bd_id'];
                    $visit_date = date('y-m-d H:i:s',time());

                    $saveSql = "INSERT INTO `oc_x_bd_visit_history`
                    (`customer_id`,`merchant_name`,`merchant_address`,`telephone`,`bd_id`,`visit_date`,`visit_id`,`visit_result_id`,`comment`)
                    VALUES('$customer_id','$merchant_name','$merchant_address','$telephone','$bd_id','$visit_date','$visit_id','$visit_result_id','$visit_reason')";

                    $visitHistoryW = $db->query($saveSql);

                    if($visitHistoryW){
                        $returnCode = API_RETURN_SUCCESS;
                        $returnMessage = 'HISTORY_OK';

                        $return = array(
                            'return_code' => $returnCode,
                            'return_msg' => $returnMessage,
                        );
                         return $return;
                    }else{
                        $returnCode = API_RETURN_SUCCESS;
                        $returnMessage = 'HISTORY_FAIL';

                        $return = array(
                            'return_code' => $returnCode,
                            'return_msg' => $returnMessage,
                        );
                        return $return;
                    }

                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                    );
                }else{
                    #报执行错误
                    $returnCode = API_RETURN_ERROR;
                    $returnMessage = 'FAIL';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                    );
                }
            }else{
                #报无效操作
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'NULL';
                $return = array(
                    'return_code' => $returnCode,
                    'return_msg' => $returnMessage,
                );
            }

        }

        return $return;

    }

    function getVisitDone(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg' => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and oh.telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and ohcustomer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (oh.merchant_name like '%".$keyword."%'or oh.merchant_address like '%".$keyword."%')";
                }
            }
            #获取拜访记录
            $visitHistorySql = "select oh.customer_id,oh.merchant_name,oh.merchant_address,oh.telephone,oh.visit_date,oh.comment,ot.name from
                  oc_x_bd_visit_history oh
                  left join oc_x_bd_visit_result ot on ot.visit_result_id = oh.visit_result_id
                  where oh.bd_id = '".$bd_id."'" . $queryCondition . "
                  ";

            $visitHistory = $db->query($visitHistorySql);
            if($visitHistory){
                if($visitHistory->rows){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $visitHistory->rows;

                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData
                    );
                }else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NULL';
                    $returnData = array();

                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData
                    );
                }
            }else{
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'FAIL';
                $returnData = array();

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg' => $returnMessage,
                    'return_data' => $returnData
                );
            }

        }
        return $return;

    }

    function addNewMerchant(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        $merchant_name = isset($data['data']['data'][0]['value']) && $data['data']['data'][0]['value'] ? $data['data']['data'][0]['value'] : '';
        $merchant_address = isset($data['data']['data'][1]['value']) && $data['data']['data'][1]['value'] ? $data['data']['data'][1]['value'] : '';
        $telephone = isset($data['data']['data'][2]['value']) && $data['data']['data'][2]['value'] ? $data['data']['data'][2]['value'] : '';
        $visit_status_id = isset($data['data']['data'][3]['value']) && $data['data']['data'][3]['value'] ? $data['data']['data'][3]['value'] : '';
        $is_registed = isset($data['data']['data'][4]['value']) && $data['data']['data'][4]['value'] ? $data['data']['data'][4]['value'] : '';
        $visit_date = isset($data['data']['data'][5]['value']) && $data['data']['data'][5]['value'] ? $data['data']['data'][5]['value'] : '';
        $comment = isset($data['data']['data'][6]['value']) && $data['data']['data'][6]['value'] ? $data['data']['data'][6]['value'] : '';

        $location_x = isset($data['data']['location'][0]['location_x']) && $data['data']['location'][0]['location_x'] ?$data['data']['location'][0]['location_x'] : '';
        $location_y = isset($data['data']['location'][0]['location_y']) && $data['data']['location'][0]['location_y'] ?$data['data']['location'][0]['location_y'] : '';

        $changeLocation = "http://api.map.baidu.com/geoconv/v1/?coords=$location_x,$location_y&from=3&to=5&ak=IDvNBsejl9oqMbPF316iKsXR";
        $result = json_decode(file_get_contents($changeLocation));

        $location_x = $result->result[0]->x;
        $location_y = $result->result[0]->y;

        if($location_x && $location_y){
            $location = $location_x .','.$location_y;
            //在百度坐标下经纬度都存在的情况下，通过百度地图url get方式获得地理位置json信息
            $locationName = "http://api.map.baidu.com/geocoder/v2/?ak=IDvNBsejl9oqMbPF316iKsXR&coordtype=wgs84ll&location=$location&output=json&pois=1";
            $address = json_decode(file_get_contents($locationName));
            $signAddress = $address->result->formatted_address;
        }else{
            $location ='';
            $signAddress = '';
        }

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            if($merchant_address && $visit_date){
                #保存表单_暂时没有把跟进新商家的编号保存进去，后期处理
                $saveSql = "INSERT INTO `oc_x_bd_visit_new`
                    (`merchant_name`,`merchant_address`,`telephone`,`visit_status_id`,`is_registed`,`visit_date`,`bd_id`,`comment`,`location`,`sign_address`)
                    VALUES('$merchant_name','$merchant_address','$telephone','$visit_status_id','$is_registed','$visit_date','$bd_id','$comment','$location','$signAddress')";

                $query = $db->query($saveSql);

                if($query){
                    #成功
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                    );
                }else{
                    $returnCode = API_RETURN_ERROR;
                    $returnMessage = 'FAIL';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                    );
                }
            }else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NOTFULL';
                $returnData = array();

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg'  => $returnMessage,
                    'return_data' => $returnData
                );
            }

        }

        return $return;
    }

    function updatePreCustomerUnregister(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        $new_visit_id = isset($data['data'][0]['value']) && $data['data'][0]['value'] ? $data['data'][0]['value'] : '';
        $merchant_name = isset($data['data'][1]['value']) && $data['data'][1]['value'] ? $data['data'][1]['value'] : '';
        $merchant_address = isset($data['data'][2]['value']) && $data['data'][2]['value'] ? $data['data'][2]['value'] : '';
        $telephone = isset($data['data'][3]['value']) && $data['data'][3]['value'] ? $data['data'][3]['value'] : '';
        $visit_result_id = isset($data['data'][4]['value']) && $data['data'][4]['value'] ? $data['data'][4]['value'] : '';
        $comment = isset($data['visit_reason']) && $data['visit_reason'] ?  $data['visit_reason'] : '';
        $visit_date = date('Y-m-d');

        if($visit_result_id>3){
            $is_registed = 1;
        }else{
            $is_registed = 0;
        }

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            if($new_visit_id){
                $sql = "select visit_times as count from oc_x_bd_visit_new where new_visit_id = '".$new_visit_id."'";
                $query = $db->query($sql)->row;
                $times = $query['count']+1;

                $updateSql = "update oc_x_bd_visit_new set merchant_name = '".$merchant_name."',merchant_address = '".$merchant_address."',telephone = '".$telephone."',visit_status_id = '".$visit_result_id."',visit_times = '$times', is_registed = '$is_registed',visit_date = '$visit_date',comment = '$comment' where new_visit_id = '".$new_visit_id."'";

                $update = $db->query($updateSql);

                if($update){
                    $sql = "select is_registed from oc_x_bd_visit_new where new_visit_id = '".$new_visit_id."'";
                    $query = $db->query($sql)->row;
                    $registed = $query['is_registed'];

                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $registed
                    );
                }else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NULL';
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                    );
                }
            }else{
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'FAIL';
                $returnData = array();

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg'  => $returnMessage,
                    'return_data' => $returnData
                );
            }

        }

        return $return;
    }

    function getNewVisitInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg' => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            #查询条件根据sql语句来修改
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and ovn.telephone = '".$keyword."'";
                    }else if($keyword == 1){
                        $queryCondition = "and ovn.is_registed = 1";
                    }else if($keyword == 2){
                        $queryCondition = "and ovn.is_registed = 0";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (ovn.merchant_name like '%".$keyword."%' or ovn.merchant_address like '%".$keyword."%')";
                }
            }

            $newVisitSql = "select ovn.new_visit_id, ovn.merchant_name,ovn.merchant_address,ovn.telephone,ovn.visit_date,ovn.is_registed,ovs.status_name
                from oc_x_bd_visit_new ovn
                left join oc_x_bd_visit_status ovs on ovs.visit_status_id = ovn.visit_status_id
                where ovn.bd_id = '".$bd_id."'" . $queryCondition . "
                ";
//            return $newVisitSql;
            $newVisitHistory = $db->query($newVisitSql);

            if($newVisitHistory){
                #执行成功
                if($newVisitHistory->rows){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $newVisitHistory->rows;

                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData
                    );
                }else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'NULL';
                    $returnData = array();

                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData
                    );
                }
            }else{
                #执行失败
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'FAIL';
                $returnData = array();

                $return = array(
                    'return_code' => $returnCode,
                    'return_msg' => $returnMessage,
                    'return_data' => $returnData
                );
            }

        }

        return $return;
    }

    function cancelCustomer(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $customer_id = isset($data['data']['customer_id']) && $data['data']['customer_id'] ? $data['data']['customer_id'] : '';

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            if($customer_id){
                //将customer表的approved置0，使该客户无效
                $updateSql = "UPDATE oc_customer set approved = 0 WHERE customer_id = '".$customer_id."'";
                $query = $db->query($updateSql);
                if($query){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = array();
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData,
                    );
                }else{
                    $returnCode = API_RETURN_ERROR;
                    $returnMessage = 'FAIL';
                    $returnData = array();
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData,
                    );
                }

            }else{
                #报无效操作
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'NULL';
                $returnData = array();
                $return = array(
                    'return_code' => $returnCode,
                    'return_msg' => $returnMessage,
                    'return_data' => $returnData,
                );
            }
        }


        return $return;
    }

    function cancelCustomerInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }
        else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    if( strlen($keyword)>=11 ){
                        $queryCondition = " and BB.telephone = '".$keyword."'";
                    }
                    else{
                        $queryCondition = " and BB.customer_id = '".$keyword."'";
                    }
                }
                else{
                    $keyword = $this->stripSearchKeyword($keyword);
                    $queryCondition = " and (BB.firstname like '%".$keyword."%' or BB.merchant_name like '%".$keyword."%' or BB.merchant_address like '%".$keyword."%')";
                }
            }

            $orderInfoReturnFields = array('lastmonth_orders','lastmonth_sub_total','month_orders','month_sub_total','lastweek_orders','lastweek_sub_total','week_orders','week_sub_total','today_orders','today_sub_total','today_order_info','week_order_info','month_order_info','lastmonth_order_info');
            $queryOrders = $this->_getOrderInfo($data);

            $customerInfoReturnFields = array('customer_id','cust_name','merchant_name','merchant_address','telephone','reg_date','reg_date_format','reg_week','reg_thisweek');
            $customerSql = "select BB.customer_id,BB.firstname cust_name, BB.merchant_name, BB.merchant_address, BB.telephone,
                    BB.date_added reg_date, date_format(BB.date_added, '%Y%m%d') reg_date_format,
                    week(BB.date_added) reg_week, if( week(BB.date_added) = week(now()),  1, 0) reg_thisweek
                    from oc_customer BB
                    where BB.approved = 0 and BB.bd_id = '".$bd_id."'" . $queryCondition . "
                    ";
            $queryCustomers = $db->query($customerSql);

            if(sizeof($queryCustomers->rows)){
                $ordersInfo = array();
                foreach($queryOrders->rows as $m){
                    $ordersInfo[$m['customer_id']] = $m;

                    //$ordersInfo[$m['customer_id']]['today_order_info'] = explode($orderInfoSeparator,$m['today_order_info']);
                    //$ordersInfo[$m['customer_id']]['week_order_info']  = explode($orderInfoSeparator,$m['week_order_info']);
                    //$ordersInfo[$m['customer_id']]['month_order_info']  = explode($orderInfoSeparator,$m['month_order_info']);
                    //$ordersInfo[$m['customer_id']]['lastmonth_order_info']  = explode($orderInfoSeparator,$m['lastmonth_order_info']);
                }

                //TODO, Bad performance
                $bdCustomerInfo = array();
                $bdCustomerInfoToReturn = array();
                foreach($queryCustomers->rows as $m){
                    if(array_key_exists($m['customer_id'], $ordersInfo)){
                        $bdCustomerInfoIndex = (10000+(int)$ordersInfo[$m['customer_id']]['today_orders']).'_'.$m['customer_id'];
                        foreach($ordersInfo[$m['customer_id']] as $k=>$v){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $v;
                        }
                    }
                    else{
                        $bdCustomerInfoIndex = '10000_'.$m['customer_id'];
                        foreach($orderInfoReturnFields as $k){
                            $bdCustomerInfo[$bdCustomerInfoIndex][$k] = 0;
                        }
                    }

                    foreach($customerInfoReturnFields as $k){
                        $bdCustomerInfo[$bdCustomerInfoIndex][$k] = $m[$k];
                    }
                }
                krsort($bdCustomerInfo);

                foreach($bdCustomerInfo as $m){
                    $bdCustomerInfoToReturn[] = $m;
                }

                //TODO, Simplier but not sort
//                $bdCustomerInfo = array();
//                foreach($queryCustomers->rows as $m){
//                    $bdCustomerInfo[$m['customer_id']] = $m;
//                    if(array_key_exists($m['customer_id'], $ordersInfo)){
//                        foreach($ordersInfo[$m['customer_id']] as $k=>$v){
//                            $bdCustomerInfo[$m['customer_id']][$k] = $v;
//                        }
//                    }
//                }

            }

            if(sizeof($queryOrders->rows)){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                //$returnData = $queryCustomers->rows;
                $returnData = $bdCustomerInfoToReturn;
            }
            else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NO RECORD';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;
    }

    function resetCustomer(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $customer_id = isset($data['data']['customer_id']) && $data['data']['customer_id'] ? $data['data']['customer_id'] : '';

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;

            $return = array(
                'return_code' => $returnCode,
                'return_msg'  => $returnMessage,
                'return_data' => $returnData
            );
        }else{
            if($customer_id){
                //将customer表的approved置0，使该客户无效
                $updateSql = "UPDATE oc_customer set approved = 1 WHERE customer_id = '".$customer_id."'";
                $query = $db->query($updateSql);
                if($query){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = array();
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData,
                    );
                }else{
                    $returnCode = API_RETURN_ERROR;
                    $returnMessage = 'FAIL';
                    $returnData = array();
                    $return = array(
                        'return_code' => $returnCode,
                        'return_msg' => $returnMessage,
                        'return_data' => $returnData,
                    );
                }

            }else{
                #报无效操作
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'NULL';
                $returnData = array();
                $return = array(
                    'return_code' => $returnCode,
                    'return_msg' => $returnMessage,
                    'return_data' => $returnData,
                );
            }
        }


        return $return;
    }

    function getOrderDetail(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = array();
        }else{
            if($keyword) {
                if (is_numeric($keyword)) {
                    #查询特定单号
                    $sql = "select O.order_id order_id,O.total order_total,O.customer_id,O.shipping_name shipping_name,O.shipping_phone shipping_phone, O.shipping_address_1 shipping_address,O.deliver_date deliver_date,OP.product_id product_id, OP.name product_name,  round(OP.price,2) price,  OP.quantity quantity,  round(OP.total,2) total,OS.name order_status,OPS.name order_pay_status,ODS.name order_delivery_status,
                    if(ORT.inv_out is null, 0, ORT.inv_out) out_cnt, if(ORT.inv_out_total is null, 0, round(ORT.inv_out_total,2)) out_total,
                    if(ORT.return_qty is null, 0 ,ORT.return_qty) return_quantity, if(ORT.return_total is null, 0, round(ORT.return_total,2)) return_total,
                    if(ORT.reutrn_credit is null, 0, round(ORT.reutrn_credit,2)) return_credit,
                    if(OP.is_gift,'赠品', '') gift,
                    round(ORC.gift_total,2) gift_total,
                    round(abs(O.discount_total)-ORC.gift_total,2) real_discount,
                    LD.logistic_driver_title,LD.logistic_driver_phone
                    from oc_order O
                    left join oc_order_product OP on O.order_id = OP.order_id
                    left join oc_order_status OS on OS.order_status_id = O.order_status_id
                    left join oc_order_payment_status OPS on OPS.order_payment_status_id = O.order_payment_status_id
                    left join oc_order_deliver_status ODS on ODS.order_deliver_status_id = O.order_deliver_status_id
                    left join oc_x_logistic_allot_order LAO on LAO.order_id = O.order_id
                    left join oc_x_logistic_allot LA on LA.logistic_allot_id = LAO.logistic_allot_id
                    left join oc_x_logistic_driver LD on LD.logistic_driver_id = LA.logistic_driver_id
                    left join
                    (
                            select
                            O.order_id,
                            sum(if(P.is_gift,P.price*P.quantity,0)) gift_total
                            from oc_order O
                            left join oc_order_product P on P.order_id = O.order_id
                            where O.order_id = '".$keyword."'
                    )ORC on ORC.order_id = O.order_id
                    left join
                    (
                        select
                        A.order_id,
                        C.product_id,
                        D.name product_name,
                        sum(C.quantity*-1) inv_out,
                        sum( if(C.weight>0, C.price*(C.weight/D.weight), C.quantity*C.price*-1) ) inv_out_total,
                        E.qty return_qty,
                        E.total return_total,
                        if(E.return_credits is null, E.return_credits, 0)*(-1)  reutrn_credit
                        from xsjb2b.oc_order A
                        left join xsjb2b.oc_x_stock_move B on A.order_id = B.order_id
                        left join xsjb2b.oc_x_stock_move_item C on B.inventory_move_id = C.inventory_move_id
                        left join xsjb2b.oc_product D on C.product_id = D.product_id
                        left join (
                             select AA.order_id, AA.date_added, sum(BB.quantity) qty, BB.product_id, BB.price, sum(BB.total) total, sum(BB.return_product_credits) return_credits
                             from oc_order OO
                             left join oc_return AA on OO.order_id = AA.order_id
                             left join oc_return_product BB on AA.return_id = BB.return_id
                             where  AA.return_action_id in (1,2,3,4) and AA.return_status_id = 2 and OO.order_status_id not in (3)
                                 and OO.order_id = '".$keyword."'
                             group by AA.order_id, BB.product_id
                        ) E on A.order_id = E.order_id and C.product_id = E.product_id
                        where A.order_id = '".$keyword."' and B.inventory_type_id = 12 and C.status = 1 and A.order_status_id not in (3)
                        group by A.order_id, C.product_id
                    ) ORT on O.order_id = ORT.order_id and OP.product_id = ORT.product_id
                    where O.bd_id = '".$bd_id."' and O.order_id = '".$keyword."'";

                    $orderDetail = $db->query($sql);

                    if($orderDetail){
                        if($orderDetail->rows){
                            $returnCode = API_RETURN_SUCCESS;
                            $returnMessage ='OK';
                            $returnData = $orderDetail->rows;
                        }else{
                            $returnCode = API_RETURN_ERROR;
                            $returnMessage ='NULL';
                            $returnData = array();
                        }
                    }else{
                        $returnCode = API_RETURN_ERROR;
                        $returnMessage ='RUN_ERROR';
                        $returnData = array();
                    }
                }else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage ='FORM_ERROR';
                    $returnData = array();
                }
            }
        }

        $return  = array(
            'return_code' => $returnCode,
            'return_msg' => $returnMessage,
            'return_data' => $returnData
        );
        return $return;
    }

    function getBDVisitInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    $queryCondition = " and BD.bd_id = '".$keyword."'";
                }else{
                    $queryCondition = " and BD.bd_name like '%".$keyword."%'";
                }
            }

            $sql = $this->_getBDRegisterInfo($data);
            $querySql = $sql.$queryCondition;

            $registerInfo = $db->query($querySql);

            if($registerInfo->rows){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage ='OK';
                $returnData = $registerInfo->rows;
            }else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage ='NULL';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;

    }

    function getBDOrderInfo(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $keyword = isset($data['data']['keyword']) && $data['data']['keyword'] ? $data['data']['keyword'] : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }else{
            $queryCondition = '';
            if($keyword){
                if(is_numeric($keyword)){
                    $queryCondition = " and BD.bd_id = '".$keyword."'";
                }else{
                    $queryCondition = " and BD.bd_name like '%".$keyword."%'";
                }
            }

            $sql = $this->_getBDOrderInfo($data);
            $querySql = $sql.$queryCondition;

            $orderInfo = $db->query($querySql);

            if($orderInfo->rows){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage ='OK';
                $returnData = $orderInfo->rows;
            }else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage ='NULL';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;

    }

    function getCustomerNearby(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;
        $area_id = isset($data['data']['area_id']) && $data['data']['area_id'] ? $data['data']['area_id'] : '';

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }else{
//            $sql = "select merchant_name title,merchant_address address,location point,telephone tel from oc_x_bd_visit_new where bd_id = '" . $bd_id . "'";
            $sql = "select concat(merchant_address,'(',merchant_name,')') merchant_address  from oc_customer where bd_id = '" . $bd_id . "' and area_id = '".$area_id."'";
            $addressInfo = $db->query($sql)->rows;

            $address = array();
            foreach($addressInfo as $value){
                $address[] = $value['merchant_address'];
            }

            if($addressInfo){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                $returnData = $address;
            }else{
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'NULL';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;
    }

    function getxyLocation(array $data){

        return 13241234;
    }

    function getBDname(array $data){
        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];
        $bd_name = $bdInfo['return_data']['bd_name'];

        $dir = $bd_name .'_'.$bd_id;

        return $dir;
    }

    function getAreaId(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }else{
            $sql = "select area_id,concat(district,'->',name)area from oc_x_area where bd_id = '".$bd_id."'";
            $query =  $db->query($sql)->rows;

            if($query){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                $returnData = $query;
            }else{
                $returnCode = API_RETURN_ERROR;
                $returnMessage = 'NULL';
                $returnData = array();
            }
        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg'  => $returnMessage,
            'return_data' => $returnData
        );

        return $return;
    }

    function getTotalCustomerNumber(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }else{
            $sql = "select count(*) as totalCustomer from oc_customer where bd_id = '".$bd_id."' and approved = 1";
            $query =  $db->query($sql)->rows;
            $totalCustomer = $query[0]['totalCustomer'];
            if($query){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                $returnData = $totalCustomer;
            }else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NULL';
                $returnData = 0;
            }
        }
        $return = array(
            'return_code' => $returnCode,
            'return_msg' => $returnMessage,
            'return_data' => $returnData,
        );
        return $return;
    }

    //获取三个月以前当月最后一天下三单以内的客户
    private function _getCustomersInThree($bd_id){
        global $db_lastday;

        $sql = "select
        o.customer_id,
         count(DISTINCT o.order_id) o_num
        from oc_order o
        left join oc_customer c on c.customer_id = o.customer_id
        left join oc_x_bd b on b.bd_id = c.bd_id
        left join oc_x_area a on a.area_id = c.area_id
        where date(o.date_added) <= LAST_DAY(DATE_SUB(CURRENT_DATE(),INTERVAL 3 month)) and o.order_status_id <> 3
        and b.bd_id = '".(int)$bd_id."'
        group by o.customer_id
        having count(DISTINCT o.order_id) <= 3
        order by date(max(o.date_added)) asc";

        $query =  $db_lastday->query($sql);

        $customers = array();

        if(sizeof($query->rows)){
            foreach($query->rows as $value){
                $customers[] = $value['customer_id'];
            }
        }

        $return = array(
            'customer' => $customers,
            'ord_num' => $query->rows,
        );

        return $return;
    }

    //获取三个月前三单客户在本月下单的客户
    function getOldPerforemance(array $data){
        global $db_lastday;
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db->escape($data['station_id']) : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        $old_customer = $this->_getCustomersInThree($bd_id);

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }else{
            if(sizeof($old_customer['customer'])){

                $ord_num = array();

                $c_id = implode(',',$old_customer['customer']);

                //以客户id为键值，归纳该客户的下单情况
                foreach($old_customer['ord_num'] as $k => $v){
                    $ord_num[$v['customer_id']] = $v['o_num'];
                }


                $sql = "select A.customer_id ,CU.firstname , A.order_id , date(A.date_added) o_date ,
                CU.date_added , C.name order_status, D.name check_status, E.name deliver_status,
                CU.merchant_name ,CU.merchant_address ,
                sum(if(B.code='sub_total', B.value, 0))  s_total,
                sum(if(B.code='discount_total', B.value, 0)) d_total
                from oc_order A
                left join oc_customer CU on A.customer_id = CU.customer_id
                left join oc_order_total B on A.order_id=B.order_id
                left join oc_order_status C on A.order_status_id = C.order_status_id
                left join oc_order_payment_status D on A.order_payment_status_id = D.order_payment_status_id
                left join oc_order_deliver_status E on A.order_deliver_status_id = E.order_deliver_status_id
                left join oc_x_bd BD on CU .bd_id = BD.bd_id
                where month(A.date_added) = month(CURRENT_DATE()) and year(A.date_added) = year(CURRENT_DATE())
                -- where date(A.date_added) between '2017-03-01' and '2017-03-31'
                and A.order_status_id <> 3 and A.station_id = 2 and find_in_set(A.customer_id,'".$c_id."')
                group by B.order_id,A.customer_id order by CU.customer_id , A.order_id ASC";

                $query = $db_lastday->query($sql);

                $order_num = array();

                if(sizeof($query->rows)){
                    $order_info = $query->rows;
                    foreach($order_info as $key => $value){
                        $order_num[$value['customer_id']][] = $value;
                    }
                }

                $perforemance = array();
                foreach($order_num as $k => &$v){
                    $i = 0;
                    foreach($v as $kk => &$vv){
                        $i ++;
                        $vv['order_old'] = $ord_num[$k];
                        $vv['order_sort'] = '第'.($ord_num[$k] + $i).'单';
                        if($ord_num[$k] + $i > 3){
                            unset($v[$kk]);
                        }
                        if($ord_num[$k] + $i<=3){
                            $perforemance[] = $vv;
                        }
                    }
                }

                if($query){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = $perforemance;
                }else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'error';
                    $returnData = 0;
                }
            }else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NULL';
                $returnData = array();
            }


        }
        $return = array(
            'return_code' => $returnCode,
            'return_msg' => $returnMessage,
            'return_data' => $returnData,
        );

        return $return;
    }

    //获得前两个月下了两单以内的首单客户，
    private function _getCustomerInRecentTwoMonth($bd_id){
        global $db_lastday;

        $sql = "select o.customer_id,count(DISTINCT o.order_id) o_num
        from oc_order o
        inner join (
            select  o.customer_id
            from oc_order o
            left join oc_order_extend e on e.order_id = o.order_id
            where date(o.date_added) between concat(date_format(LAST_DAY(DATE_SUB(CURRENT_DATE(), interval 2 month)),'%Y-%m-'),'01') and concat(date_format(LAST_DAY(DATE_SUB(CURRENT_DATE(), interval 1 month)),'%Y-%m-'),'01')
            and e.firstorder = 1 and o.order_status_id <> 3 and o.bd_id = '". (int)$bd_id ."'
          group by o.customer_id
        ) OO on OO.customer_id = o.customer_id
        where date(o.date_added) between concat(date_format(LAST_DAY(DATE_SUB(CURRENT_DATE(), interval 2 month)),'%Y-%m-'),'01') and concat(date_format(LAST_DAY(DATE_SUB(CURRENT_DATE(), interval 1 month)),'%Y-%m-'),'01')
        and o.order_status_id <> 3 and o.bd_id = '". (int)$bd_id ."'
        group by o.customer_id
        having o_num <= 2";

        $query = $db_lastday->query($sql);

        $customers = array();

        if(sizeof($query->rows)){
            foreach($query->rows as $value){
                $customers[] = $value['customer_id'];
            }
        }

        $return = array(
            'customer' => $customers,
            'ord_num' => $query->rows,
        );

        return $return;

    }

    //统计前两个月下了两单以内的首单客户在本月的下单情况
    function getNewPerforemance(array $data){

        global $db_lastday;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db_lastday->escape($data['station_id']) : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }else{
            $new_customer = $this->_getCustomerInRecentTwoMonth($bd_id);
            //获得上两个月下两单以内的首单客户
            $c_id = '';
            $ord_num = array();
            if(sizeof($new_customer['customer'])){
                $c_id = implode(',',$new_customer['customer']);
                //以客户id为键值，归纳该客户的下单情况
                foreach($new_customer['ord_num'] as $k => $v){
                    $ord_num[$v['customer_id']] = $v['o_num'];
                }
            }

            if(!sizeof($new_customer['customer'])){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'error';
                $returnData = 0;
            }else{
                $sql = "select A.customer_id ,CU.firstname , A.order_id , date(A.date_added) o_date ,
                CU.date_added , C.name order_status, D.name check_status, E.name deliver_status,
                CU.merchant_name ,CU.merchant_address ,
                sum(if(B.code='sub_total', B.value, 0))  s_total,
                sum(if(B.code='discount_total', B.value, 0)) d_total,
                sum(if(B.code='total', B.value, 0))  total
                from oc_order A
                left join oc_customer CU on A.customer_id = CU.customer_id
                left join oc_order_total B on A.order_id=B.order_id
                left join oc_order_status C on A.order_status_id = C.order_status_id
                left join oc_order_payment_status D on A.order_payment_status_id = D.order_payment_status_id
                left join oc_order_deliver_status E on A.order_deliver_status_id = E.order_deliver_status_id

                left join oc_order_extend EXT on A.order_id = EXT.order_id
                left join oc_x_bd BD on CU .bd_id = BD.bd_id
                left join oc_x_area AREA on AREA.area_id = CU .area_id
                -- where date(A.date_added) between '2017-03-01' and '2017-03-31'
                where month(A.date_added) = month(CURRENT_DATE()) and year(A.date_added) = year(CURRENT_DATE())
                and A.order_status_id <> 3 and A.station_id = 2 and A.customer_id in ($c_id)
                group by B.order_id,A.customer_id order by CU.customer_id , A.order_id ASC";

                $query = $db_lastday->query($sql);

                $order_num = array();

                if(sizeof($query->rows)){
                    $order_info = $query->rows;
                    foreach($order_info as $key => $value){
                        $order_num[$value['customer_id']][] = $value;
                    }
                }

                $perforemance = array();
                foreach($order_num as $k => &$v){
                    $i = 0;
                    foreach($v as $kk => &$vv){
                        $i ++;
                        $vv['order_old'] = $ord_num[$k];
                        $vv['order_sort'] = '第'.($ord_num[$k] + $i).'单';
                        if($ord_num[$k] + $i > 3){
                            unset($v[$kk]);
                        }
                        if($ord_num[$k] + $i<=3){
                            $perforemance[] = $vv;
//                        return $vv;
                        }
                    }
                }

                if($query){
                    if(sizeof($query->rows)){
                        $returnCode = API_RETURN_SUCCESS;
                        $returnMessage = 'OK';
                        $returnData = $perforemance;
                    }else{
                        $returnCode = API_RETURN_SUCCESS;
                        $returnMessage = 'null';
                        $returnData = array();
                    }
                }else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'error';
                    $returnData = 0;
                }

            }



        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg' => $returnMessage,
            'return_data' => $returnData,
        );

        return $return;

    }

    function getPerforemanceInfo(array $data){
        global $db_lastday;

        $uid = isset($data['uid']) && $data['uid'] ? $data['uid'] : false;
        $station_id = isset($data['station_id']) && $data['station_id'] ? $db_lastday->escape($data['station_id']) : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        $date_start = $data['data']['date_start'];
        $date_end = $data['data']['date_end'];
        $area_id = $data['data']['area_id'];

        if(!$uid || !$bd_id || !$station_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Invalid request';
            $returnData = $bdInfo;
        }else{
            $sql_a = "SELECT o.bd_id, count(DISTINCT o.order_id)order_num,sum(o.sub_total) sub_total,
                sum(if(e.firstorder = 1,1,0)) one, sum(if(e.secondorder_fm = 1,1,0)) two, sum(if(e.thirdorder_fm = 1,1,0)) three
                from oc_order o
                left join oc_order_extend e on e.order_id = o.order_id
                left join oc_customer c on c.customer_id = o.customer_id
                where date(o.date_added) BETWEEN '".$date_start."' and '".$date_end."'
                and o.station_id = '".(int) $station_id ."' and o.order_status_id <> 3 and o.bd_id = '". (int)$bd_id ."'
                ";

            $sql_b = "select c.bd_id, count(DISTINCT c.customer_id) new_customer
                from oc_customer c
                where date(c.date_added) BETWEEN '".$date_start."' and '".$date_end."'
                and c.bd_id = '".(int)$bd_id."'
                ";

            if($area_id){
                $sql_a .= " and c.area_id = '".(int) $area_id ."'";
                $sql_b .= " and c.area_id = '".(int) $area_id ."'";
            }

            $sql_a .= " group by o.bd_id";
            $sql_b .= " group by c.bd_id";

            $sql = "SELECT A.*,
                if(B.new_customer is not null,B.new_customer,0) regist,
                if(C.visit is not null,C.visit,0) visit
                from
                ( ". $sql_a . " ) A
                left join ( ". $sql_b ." ) B on A.bd_id = B.bd_id
                left join (
                select b.bd_id,count(DISTINCT b.telephone) visit
                from oc_x_bd_visit_new b
                where b.visit_date BETWEEN '".$date_start."' and '".$date_end."'
                and b.bd_id = '".(int)$bd_id ."'
                group by b.bd_id
                ) C on C.bd_id = A.bd_id";
//return $sql;
//            $sql = "SELECT A.*,
//                if(B.new_customer is not null,B.new_customer,0) regist,
//                if(C.visit is not null,C.visit,0) visit
//                from
//                (
//                SELECT o.bd_id, count(DISTINCT o.order_id)order_num,sum(o.sub_total) sub_total,
//                sum(if(e.firstorder = 1,1,0)) one, sum(if(e.secondorder_fm = 1,1,0)) two, sum(if(e.thirdorder_fm = 1,1,0)) three
//                from oc_order o
//                left join oc_order_extend e on e.order_id = o.order_id
//                where date(o.date_added) BETWEEN '".$date_start."' and '".$date_end."'
//                and o.station_id = '".(int) $station_id ."' and o.order_status_id <> 3 and o.bd_id = '". (int)$bd_id ."'
//                group by o.bd_id
//                ) A
//                left join (
//                select c.bd_id, count(DISTINCT c.customer_id) new_customer
//                from oc_customer c
//                where date(c.date_added) BETWEEN '".$date_start."' and '".$date_end."'
//                and c.bd_id = '".(int)$bd_id."'
//                group by c.bd_id
//                ) B on A.bd_id = B.bd_id
//                left join (
//                select b.bd_id,count(DISTINCT b.telephone) visit
//                from oc_x_bd_visit_new b
//                where b.visit_date BETWEEN '".$date_start."' and '".$date_end."'
//                and b.bd_id = '".(int)$bd_id ."'
//                group by b.bd_id
//                ) C on C.bd_id = A.bd_id";

            $query = $db_lastday->query($sql);

            if(sizeof($query->rows)){
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                $returnData = $query->rows;
            }else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NULL';
                $returnData = array();
            }

        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg' => $returnMessage,
            'return_data' => $returnData,
        );

        return $return;

    }

    function getCoupon(array $data){
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $db->escape($data['uid']) : false;
        $customer_id = isset($data['data']['customer_id']) && $data['data']['customer_id'] ? $db->escape($data['data']['customer_id']) : false;

        if(!$uid){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Not receive uid';
            $returnData = array();
        }else{
            $sql = "SELECT coupon_id,name from oc_coupon where bd_only = 1 and status = 1 and now() between date_start and date_end";
            $query = $db->query($sql);

            if(sizeof($query->rows)){

                /*如果之前给该客户发放过这个优惠券，并且这个优惠券在有效期内被使用，
                则不会出现在优惠券发放列表中,如果该优惠券在有效期内没被使用，则可以再次发放*/
                $coupon_list = array();
                $coupons = array();
                foreach($query->rows as $value){
                    $coupons[] = $value['coupon_id'];
                    $coupon_list[$value['coupon_id']] = $value;
                }

                $coupon_ids = implode(',',$coupons);
                //查询该客户是否使用过优惠券
                $sql = "select coupon_id from oc_coupon_history where customer_id = '".(int)$customer_id."' and coupon_id in(".$coupon_ids.") and status = 1";

                $coupon_used = $db->query($sql);

                if(sizeof($coupon_used->rows)){
                    foreach($coupon_used->rows as $value){
                        unset($coupon_list[$value['coupon_id']]);
                    }
                }

                //查询被绑定的优惠券，在有效日期内没被使用的，不需要在发放列表中显示
                $sql = "select A.coupon_id,if(B.coupon_id is not null , 1,0) used
                    from oc_coupon_customer A
                    left join (
                        select coupon_id ,customer_id
                        from oc_coupon_history
                        where customer_id = '".(int)$customer_id."' and coupon_id in (".$coupon_ids.") and status = 1
                    ) B on B.coupon_id = A.coupon_id and A.customer_id = B.customer_id
                    where A.customer_id = '".(int)$customer_id."' and A.coupon_id in (".$coupon_ids.") and A.date_end >= CURRENT_DATE()
                    having used = 0";

                //获得发放给用户的优惠券，却在有效期内没有被使用的
                $coupon_valid = $db->query($sql);

                if(sizeof($coupon_valid->rows)){
                    foreach($coupon_valid->rows as $value){
                        unset($coupon_list[$value['coupon_id']]);
                    }
                }

                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'OK';
                $returnData = $coupon_list;
            }else{
                $returnCode = API_RETURN_SUCCESS;
                $returnMessage = 'NULL';
                $returnData = array();
            }

        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg' => $returnMessage,
            'return_data' => $returnData,
        );

        return $return;
    }

    function getCustomerCoupon(array $data){
        global $db;

        $customer_id = isset($data['data']['customer_id']) ? (int)$data['data']['customer_id'] : 0;
        // 获取用户使用优惠券记录
        // TODO 可缓存预先载入
        $sql = "select H.coupon_id, count(H.coupon_id) use_count from oc_coupon_history H
                    where H.customer_id = '".$customer_id."' and H.status = 1
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
        $sql = "select
                    C.coupon_id, C.name coupon_name, C.type, C.discount, C.total, C.online_payment, C.bd_only,
                    CC.times times,  CC.date_end date_end, datediff(CC.date_end, current_date()) overdue_days
                from oc_coupon C
                left join oc_coupon_customer CC on C.coupon_id = CC.coupon_id
                where C.status = 1
                and CC.status = 1
                and current_date() between CC.date_start and CC.date_end
                and CC.customer_id = '".$customer_id."'

                union

                select
                    C.coupon_id, C.name coupon_name, C.type, C.discount, C.total, C.online_payment, C.bd_only,
                    C.times times, C.date_end date_end, datediff(C.date_end, current_date()) overdue_days
                from oc_coupon C
                where C.status = 1
                and C.customer_limited = 0
                and current_date() between C.date_start and C.date_end
                ";

        $query = $db->query($sql);
        $result = $query->rows;

        $coupons = array();
        foreach($result as $m){
            $couponUseCount = (array_key_exists($m['coupon_id'],$customerCouponHistory) ? $customerCouponHistory[$m['coupon_id']]['use_count'] : 0);
            $couponValidTimes = $m['times'] - $couponUseCount;

            // 设置有效优惠券的可用次数
            if($couponValidTimes || $m['bd_only']){
                $coupons[$m['coupon_id']] = $m;
                $coupons[$m['coupon_id']]['use_count'] = $couponUseCount;
                $coupons[$m['coupon_id']]['valid_times'] = $couponValidTimes;
            }
        }

        return array(
            'return_code' => 'SUCCESS',
            'return_msg' => $customerCouponHistory,
            'return_data' => array(
                'coupons' => $coupons
            )
        );
    }

    function bindCoupon(array $data){
        global $dbm;
        global $db;

        $uid = isset($data['uid']) && $data['uid'] ? $dbm->escape($data['uid']) : false;

        $coupon_id =  isset($data['data']['coupon_id']) && $data['data']['coupon_id'] ? $dbm->escape($data['data']['coupon_id']) : false;
        $customer_id =  isset($data['data']['customer_id']) && $data['data']['customer_id'] ? $dbm->escape($data['data']['customer_id']) : false;

        $bdInfo = $this->getBdInfo($data);
        $bd_id = $bdInfo['return_data']['bd_id'];

        if($coupon_id){
            $sql = "select valid_days,times from oc_coupon where coupon_id = '".(int)$coupon_id."'";
            $valid_days = $db->query($sql)->row['valid_days'];
            $validTimes = $db->query($sql)->row['times'];
            $date_start = date('Y-m-d');
            $date_end = date('Y-m-d', strtotime($date_start.' +'.$valid_days.' day'));
        }else{
            //$validTimes = 1; //默认可用次数为1
            //$date_start = date('Y-m-d');
            //$date_end = date('Y-m-d', strtotime($date_start.' +1 month'));

            $return = array(
                'return_code' => API_RETURN_ERROR,
                'return_msg' => 'NO VALID COUPON ID',
                'return_data' => false,
            );

            return $return;
        }

        if(!$uid || !$bd_id){
            $returnCode = API_RETURN_ERROR;
            $returnMessage = __FUNCTION__.' Not receive uid';
            $returnData = array();
        }else{
            //检查该客户之前是否被发放过该优惠券，如果有则进行更新
            $sql = "select * from oc_coupon_customer where customer_id = '".(int)$customer_id."' and coupon_id = '".(int)$coupon_id."'";

            $query = $db->query($sql);

            if(sizeof($query->rows)){
                //更新已经绑定的却没有使用的优惠券
                //更新使用次数和有效时间
                $sql = "update oc_coupon_customer set times = '".$validTimes."', date_start = '".$date_start."',date_end = '".$date_end."' where customer_id = '".(int)$customer_id."' and coupon_id = '".(int)$coupon_id."'";

                $query = $dbm->query($sql);

                if($query){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'Binded';
                    $returnData = true;
                }
            }else{
                $bool = 1;
                //绑定到优惠券用户表
                $sql = "INSERT INTO oc_coupon_customer (`coupon_id`,`customer_id`,`times`,`date_start`,`date_end`,`status`,`bd_id`) VALUES
              ('".(int) $coupon_id."','".(int)$customer_id."','".$validTimes."','". $date_start ."','". $date_end ."','1','". (int)$bd_id ."')";

                $bool = $bool && $dbm->query($sql);



                //进行记录
                $coupon_customer_id = $dbm->getLastId();
                $sql = "INSERT INTO oc_coupon_customer_history (`coupon_customer_history_id`,`coupon_id`,`customer_id`,`bd_id`,`date_added`,`added_by`) VALUES
              ('".(int)$coupon_customer_id."','".(int)$coupon_id."','".(int)$customer_id."','".(int)$bd_id."',now(),'0')";

                $bool = $bool && $dbm->query($sql);

                if($bool){
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'OK';
                    $returnData = true;
                }else{
                    $returnCode = API_RETURN_SUCCESS;
                    $returnMessage = 'FALSE';
                    $returnData = false;
                }
            }

        }

        $return = array(
            'return_code' => $returnCode,
            'return_msg' => $returnMessage,
            'return_data' => $returnData,
        );

        return $return;
    }

}

$firm = new FIRM();
?>