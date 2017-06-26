<?php
class ControllerReportSaleStat extends Controller {
    public function index() {

        $this->document->setTitle('销售统计');

        $today = date('Y-m-d', time()+8*60*60);

        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : $today;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : $today;
        $station_id = isset($this->request->get['station_id']) ? $this->request->get['station_id'] : 0;

        $url = '';

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }

        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }

        if (isset($this->request->get['station_id'])) {
            $url .= '&station_id=' . $this->request->get['station_id'];
        }

        $filter_data = array(
            'filter_date_start'         => $filter_date_start,
            'filter_date_end'         => $filter_date_end,
            'station_id'             => $station_id
        );

        $data['results'] = '';

        $data['filter_date_start'] = $filter_date_start;
        $data['filter_date_end'] = $filter_date_end;
        $data['station_id'] = $station_id;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['days'] = array();

        if($filter_date_start && $filter_date_start && $station_id){
            $result = $this->getProductSaleInfo($filter_date_start,$filter_date_end,$station_id);

            $data['moveInfo'] = $result['moveInfo'];
            $data['orderInfo'] = $result['orderInfo'];
            $data['prodInfo'] = $result['prodInfo'];
            $data['moveTotalInfo'] = $result['moveTotalInfo'];
            $data['days'] = $result['days'];
            $data['orderTotalInfo'] = $result['orderTotalInfo'];
            $data['rawAvgRetail'] = $result['rawAvgRetail'];
        }

        //Get station info
        $data['stationInfo'] = array();
        $sql = "select station_id, title, if(status=0, concat(name,'(关闭)'), name) station_name, status, city, district, factor from oc_x_station where parent_station_id > 0";
        $query = $this->db->query($sql);
        $result = $query->rows;

        foreach($result as $m){
            $data['stationInfo'][$m['station_id']] = $m;
        }

        //exit(var_dump($data['days']));

        $data['token'] = $this->session->data['token'];

        $this->response->setOutput($this->load->view('report/sale_stat.tpl', $data));
    }

    function addDate($orgDate,$day){
        $cd = strtotime($orgDate);
        $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$day,date('Y',$cd)));
        return $retDAY;
    }

    function dateList($start='',$end=''){
        $a = date_create($start);
        $b = date_create($end);

        $dayList = array();
        $m = date_diff($a,$b);
        $gap = $m->format('%a');

        $cd = strtotime($start);
        for($m=0; $m<$gap+1; $m++){
            $dayList[] = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$m,date('Y',$cd)));
        }

        return $dayList;
    }

    function cleanstr($str,$sym='') {
        //Change lind break to comma

        //Remove any blank & space
        $str = trim($str);
        $str = ereg_replace("\t",$sym,$str);
        $str = ereg_replace("\r\n",$sym,$str);
        $str = ereg_replace("\r",$sym,$str);
        $str = ereg_replace("\n",$sym,$str);
        $str = ereg_replace(" ","",$str);
        return trim($str);
    }

    function getProductSaleInfo($date_start,$date_end,$station_id){

        $data = array();

        $moveInfo = array();
        $moveProducts = array();

        $data['days'] = $this->dateList($date_start, $date_end);

        //Get and sort inventory move info
        if($station_id >0){
            $sql = "select
                    A.date,
                    A.product_id,
                    sum(A.yesterday_inv_check) yesterday_inv_check,
                    sum(A.inv_in) inv_in,
                    sum(A.inv_out) inv_out,
                    sum(A.inv_breakage) inv_breakage,
                    sum(A.inv_check) inv_check,
                    sum(A.inv_retail) inv_sale
                    from xsj_retail.retail A
                    where A.date between '".$date_start."' and '".$date_end."'
                    and A.station_id = '".$station_id."'
                    group by A.date, A.product_id";
        }
        elseif($station_id == -1){
            $sql = "select
                    A.date,
                    A.product_id,
                    sum(A.yesterday_inv_check) yesterday_inv_check,
                    sum(A.inv_in) inv_in,
                    sum(A.inv_out) inv_out,
                    sum(A.inv_breakage) inv_breakage,
                    sum(A.inv_check) inv_check,
                    sum(A.inv_retail) inv_sale
                    from xsj_retail.retail A
                    where A.date between '".$date_start."' and '".$date_end."'
                    group by A.date, A.product_id";
        }
        $query = $this->db->query($sql);
        $result = $query->rows;

        $data['moveInfo'] = array();
        foreach($result as $m){
            $data['moveInfo'][$m['date']][$m['product_id']] = $m;
        }

        if(!sizeof($data['moveInfo'])){
            $data['orderInfo'] = array();
            $data['moveInfo'] = array();
            $data['prodInfo'] = array();
            $data['moveTotalInfo'] = array();
            $data['orderTotalInfo'] = array();
            $data['rawAvgRetail'] = array();

            return $data;
        }

        //Get Inv Move Total Info
        if($station_id >0){
            $sql = "select
                    A.product_id,
                    sum(A.yesterday_inv_check) yesterday_inv_check,
                    sum(A.inv_in) inv_in,
                    sum(A.inv_out) inv_out,
                    sum(A.inv_breakage) inv_breakage,
                    sum(A.inv_check) inv_check,
                    sum(A.inv_retail) inv_sale
                    from xsj_retail.retail A
                    where A.date between '".$date_start."' and '".$date_end."'
                    and A.station_id = '".$station_id."'
                    group by A.product_id";
        }
        elseif($station_id == -1){
            $sql = "select
                    A.product_id,
                    sum(A.yesterday_inv_check) yesterday_inv_check,
                    sum(A.inv_in) inv_in,
                    sum(A.inv_out) inv_out,
                    sum(A.inv_breakage) inv_breakage,
                    sum(A.inv_check) inv_check,
                    sum(A.inv_retail) inv_sale
                    from xsj_retail.retail A
                    where A.date between '".$date_start."' and '".$date_end."'
                    group by A.product_id";
        }
        $query = $this->db->query($sql);
        $result = $query->rows;

        $data['moveTotalInfo'] = array();
        foreach($result as $m){
            $data['moveTotalInfo'][$m['product_id']] = $m;
        }


        //Get product info
        $sql = "select
                A.product_id,
                if(char_length(B.name) > 20, concat(left(B.name,20),'...'), B.name) short_name,
                B.name,
                round(p.weight,0) unit_amount,
                wd.title unit_title,
                concat(round(p.weight,0),wd.title) unit,
                round(p.price,2) ori_price,
                round(if(isnull(ps.price),p.price,ps.price),2 ) current_price,
                p.shelf_life,
                p.class,
                p.factor
                from (select distinct product_id from xsj_retail.retail) A
                left join xsj.oc_product p on A.product_id = p.product_id
                left join xsj.oc_product_description B on A.product_id = B.product_id and B.language_id  = 2
                left join xsj.oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id and wd.language_id = 2
                left join xsj.oc_product_special ps on (p.product_id = ps.product_id and now() between ps.date_start and ps.date_end)
                order by A.product_id";
        $query = $this->db->query($sql);
        $result = $query->rows;

        $data['prodInfo'] = array();
        foreach($result as $m){
            $data['prodInfo'][$m['product_id']] = $m;
        }
        //var_dump($data['prodInfo']); exit();

        //Get and sort orders info
        $data['orderInfo'] = array();
        $data['orderTotalInfo'] = array();
        if($station_id == -1){
            $sql = "select
                OP.product_id,
                round(avg(OP.price),2) sale_avg_price,
                sum(OP.quantity) sale_qty,
                round(sum(OP.total),2) sale_total,
                O.deliver_date
                from xsj.oc_order O
                left join xsj.oc_order_product OP on O.order_id = OP.order_id
                where O.deliver_date between '".$date_start."' and '".$date_end."'
                and O.shipping_code in ('D2D','PSPOT')
                and OP.status = 1
                group by O.deliver_date, OP.product_id";
            $query = $this->db->query($sql);
            $result = $query->rows;

            $data['orderInfo'] = array();
            foreach($result as $m){
                $data['orderInfo'][$m['deliver_date']][$m['product_id']] = $m;
            }



            $sql = "select
                OP.product_id,
                round(avg(OP.price),2) sale_avg_price,
                sum(OP.quantity) sale_qty,
                round(sum(OP.total),2) sale_total,
                O.deliver_date
                from xsj.oc_order O
                left join xsj.oc_order_product OP on O.order_id = OP.order_id
                where O.deliver_date between '".$date_start."' and '".$date_end."'
                and O.shipping_code in ('D2D','PSPOT')
                and OP.status = 1
                group by OP.product_id";
            $query = $this->db->query($sql);
            $result = $query->rows;

            $data['orderTotalInfo'] = array();
            foreach($result as $m){
                $data['orderTotalInfo'][$m['product_id']] = $m;
            }
        }


        //Get Raw Avg Retail
        $data['rawAvgRetail'] = array();
        if($station_id > 0){
//            $sql = "select AA.product_id, AA.retail, AA.raw_retail_day, round( AA.retail/if(AA.raw_retail_day=0,1,AA.raw_retail_day) ,2) raw_avg_retail
//            from (
//                 select
//                 A.product_id,
//                 sum(A.inv_retail) retail,
//                 sum( if( (abs(A.inv_in) + abs(A.inv_breakage) + abs(A.inv_check) + abs(A.inv_retail))>0 ,1,0)) raw_retail_day
//                 from xsj_retail.retail A
//                 where A.date between '".$date_start."' and '".$date_end."' and A.station_id = '".$station_id."' group by A.product_id
//            ) AA";

            $sql = "select
                AA.product_id,
                AA.retail,
                AA.raw_retail_day,
                round( AA.retail/if(AA.raw_retail_day=0,1,AA.raw_retail_day) ,2) raw_avg_retail,
                BB.short_factor,
                BB.short_factor+AA.retail retail_short_factor
                from (
                select
                A.product_id,
                sum(A.inv_retail) retail,
                sum( if( (abs(A.inv_in) + abs(A.inv_breakage) + abs(A.inv_check) + abs(A.inv_retail))>0 ,1,0)) raw_retail_day
                from xsj_retail.retail A
                where A.date between '".$date_start."' and '".$date_end."' and A.station_id = '".$station_id."'
                group by A.product_id
                ) AA
                left join(
                select
                B.product_id,
                B.inv_check,
                B.inv_retail,
                if(B.inv_retail > 0 and B.inv_check = 0, ".SHORT_RETAIL_FACTOR.", 1) short_factor_a,
                if(B.inv_retail > 0 and B.inv_check = 0, ".SHORT_RETAIL_FACTOR."*B.inv_retail, B.inv_retail) short_factor_b,
                if(B.inv_retail > 0 and B.inv_check = 0, CEILING(".SHORT_RETAIL_FACTOR."*B.inv_retail), B.inv_retail) short_factor_c,
                if(B.inv_retail > 0 and B.inv_check = 0, CEILING(".SHORT_RETAIL_FACTOR."*B.inv_retail), B.inv_retail)-B.inv_retail short_factor
                from xsj_retail.retail B
                where B.date = '".$date_end."' and B.station_id = '".$station_id."'
                ) BB on AA.product_id = BB.product_id";

            $query = $this->db->query($sql);
            $result = $query->rows;

            foreach($result as $m){
                $data['rawAvgRetail'][$m['product_id']] = $m;
            }
        }

        //echo $sql;
        //var_dump($data['orderInfo']); exit();

        return $data;
    }
}