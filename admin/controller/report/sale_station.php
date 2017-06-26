<?php
class ControllerReportSaleStation extends Controller {
	public function index() {

        $this->document->setTitle('门店销售列表');

        $today = date('Y-m-d', time()+8*60*60);

        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : $today;
        //$filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : $today;
        $station_id = isset($this->request->get['station_id']) ? $this->request->get['station_id'] : 0;

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		//if (isset($this->request->get['filter_date_end'])) {
			//$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		//}

        if (isset($this->request->get['station_id'])) {
            $url .= '&station_id=' . $this->request->get['station_id'];
        }

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			//'filter_date_end'	     => $filter_date_end,
			'station_id'             => $station_id
		);

        $data['results'] = '';

		$data['filter_date_start'] = $filter_date_start;
		//$data['filter_date_end'] = $filter_date_end;
		$data['station_id'] = $station_id;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
        $data['days'] = array();

        if($filter_date_start && $station_id){
            $result = $this->getProductSaleInfo($filter_date_start, $station_id);

            $data['moveInfo'] = $result['moveInfo'];
            $data['orderInfo'] = $result['orderInfo'];
        }

        //if(!sizeof($data['days'])){
        //    $data['days'] = array( );
        //}

        //Get station info
        $data['stationInfo'] = array();
        $sql = "select station_id, title, if(status=0, concat(name,'(关闭)'), name) station_name, status, city, district from oc_x_station where parent_station_id > 0";
        $query = $this->db->query($sql);
        $result = $query->rows;

        foreach($result as $m){
            $data['stationInfo'][$m['station_id']] = $m;
        }

        //exit(var_dump($data['days']));

        $data['token'] = $this->session->data['token'];

		$this->response->setOutput($this->load->view('report/sale_station.tpl', $data));
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

    function getProductSaleInfo($date,$station_id){

        $data = array();

        $moveInfo = array();
        $moveProducts = array();

        //$data['days'] = $this->dateList($date_start, $date_end);
        //$orderInfo = array();
        //$orderProducts = array();

        //Get and sort inventory move info
        if($station_id >0){
            $sql = "select
                    PP.product_id,
                    PP.name,
                    if(char_length(PP.name) > 20, concat(left(PP.name,20),'...'), PP.name) short_name,
                    PP.price current_price,
                    PP.shelf_life,
                    PP.ori_price,
                    PP.unit,
                    PP.weight_amount,
                    PP.unit_title,
                    PP.count_with_grams,
                    if(BB.price is null, 0, BB.price) yesterday_s_check_avg_price,
                    if(BB.yesterday_s_check is null, 0, BB.yesterday_s_check) yesterday_s_check,
                    if(AA.today_s_in is null, 0, AA.today_s_in) today_s_in,
                    if(AA.today_s_out is null, 0, AA.today_s_out) today_s_out,
                    if(AA.today_s_breakage is null, 0, AA.today_s_breakage) today_s_breakage,
                    if(AA.today_s_check is null, 0, AA.today_s_check) today_s_check,
                    if(AA.today_s_pos_retail is null, 0, AA.today_s_pos_retail) inv_pos_retail,
                    if(AA.price is null, 0, AA.price) today_retail_avg_price,
                    if(BB.yesterday_s_check is null, 0, BB.yesterday_s_check)+if(AA.today_s_in is null, 0, AA.today_s_in)+if(AA.today_s_out is null, 0, AA.today_s_out)+if(AA.today_s_breakage is null, 0, AA.today_s_breakage)-if(AA.today_s_check is null, 0, AA.today_s_check) today_retail
                     from(
                    select
                    a.product_id,
                    pd.name,
                    round(if(isnull(ps.price),p.price,ps.price),2 ) price,
                    round(p.price,2) ori_price,
                    concat(round(p.weight,0), wd.title) unit,
                    p.shelf_life,
                    round(p.weight,0) weight_amount,
                    if(p.weight_class_id=1, 'Y', 'N') count_with_grams,
                    wd.title unit_title
                    from oc_x_inventory_move_item a
                    left join oc_product p on a.product_id = p.product_id
                    left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id and wd.language_id = 2
                    left join oc_product_special ps on (p.product_id = ps.product_id and now() between ps.date_start and ps.date_end)
                    left join oc_product_description pd on p.product_id = pd.product_id and pd.language_id = 2
                    group by a.product_id
                    ) PP
                    left join
                        (select
                        B.product_id,
                        D.name,
                        round(avg(B.price),2) price,
                        sum(if(A.inventory_type_id = 2,B.quantity,0)) today_s_in,
                        sum(if(A.inventory_type_id = 4,B.quantity,0)) today_s_out,
                        sum(if(A.inventory_type_id = 3,B.quantity,0)) today_s_breakage,
                        sum(if(A.inventory_type_id = 1,B.quantity,0)) today_s_check,
                        sum(if(A.inventory_type_id = 5,B.quantity,0)) today_s_pos_retail
                        from oc_x_inventory_move A
                        left join oc_x_inventory_move_item as B on A.inventory_move_id = B.inventory_move_id
                        left join oc_x_inventory_type as C on A.inventory_type_id = C.inventory_type_id
                        left join oc_product_description as D on B.product_id = D.product_id and D.language_id = 2
                        where date(A.date_added) = '".$date."' and B.status = 1
                        and A.station_id = '".$station_id."'
                        group by B.product_id
                        order by A.inventory_move_id, B.product_id
                        ) AA on PP.product_id = AA.product_id
                    left join
                        (select
                        B.product_id,
                        D.name,
                        round(avg(B.price),2) price,
                        sum(B.quantity) yesterday_s_check
                        from oc_x_inventory_move A
                        left join oc_x_inventory_move_item as B on A.inventory_move_id = B.inventory_move_id
                        left join oc_x_inventory_type as C on A.inventory_type_id = C.inventory_type_id
                        left join oc_product_description as D on B.product_id = D.product_id and D.language_id = 2
                        where date(A.date_added) = DATE_SUB(date('".$date."'), INTERVAL 1 DAY) and B.status = 1
                        and A.station_id = '".$station_id."'
                        and A.inventory_type_id = 1
                        group by B.product_id
                        order by A.inventory_move_id, B.product_id
                        ) BB on PP.product_id = BB.product_id;";
        }
        elseif($station_id == -1){
            $sql = "select
                    PP.product_id,
                    PP.name,
                    if(char_length(PP.name) > 20, concat(left(PP.name,20),'...'), PP.name) short_name,
                    PP.price current_price,
                    PP.shelf_life,
                    PP.ori_price,
                    PP.unit,
                    PP.weight_amount,
                    PP.unit_title,
                    PP.count_with_grams,
                    if(BB.price is null, 0, BB.price) yesterday_s_check_avg_price,
                    if(BB.yesterday_s_check is null, 0, BB.yesterday_s_check) yesterday_s_check,
                    if(AA.today_s_in is null, 0, AA.today_s_in) today_s_in,
                    if(AA.today_s_out is null, 0, AA.today_s_out) today_s_out,
                    if(AA.today_s_breakage is null, 0, AA.today_s_breakage) today_s_breakage,
                    if(AA.today_s_check is null, 0, AA.today_s_check) today_s_check,
                    if(AA.today_s_pos_retail is null, 0, AA.today_s_pos_retail) inv_pos_retail,
                    if(AA.price is null, 0, AA.price) today_retail_avg_price,
                    if(BB.yesterday_s_check is null, 0, BB.yesterday_s_check)+if(AA.today_s_in is null, 0, AA.today_s_in)+if(AA.today_s_out is null, 0, AA.today_s_out)+if(AA.today_s_breakage is null, 0, AA.today_s_breakage)-if(AA.today_s_check is null, 0, AA.today_s_check) today_retail
                     from(
                    select
                    a.product_id,
                    pd.name,
                    round(if(isnull(ps.price),p.price,ps.price),2 ) price,
                    round(p.price,2) ori_price,
                    concat(round(p.weight,0), wd.title) unit,
                    p.shelf_life,
                    round(p.weight,0) weight_amount,
                    if(p.weight_class_id=1, 'Y', 'N') count_with_grams,
                    wd.title unit_title
                    from oc_x_inventory_move_item a
                    left join oc_product p on a.product_id = p.product_id
                    left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id and wd.language_id = 2
                    left join oc_product_special ps on (p.product_id = ps.product_id and now() between ps.date_start and ps.date_end)
                    left join oc_product_description pd on p.product_id = pd.product_id and pd.language_id = 2
                    group by a.product_id
                    ) PP
                    left join
                        (select
                        B.product_id,
                        D.name,
                        round(avg(B.price),2) price,
                        sum(if(A.inventory_type_id = 2,B.quantity,0)) today_s_in,
                        sum(if(A.inventory_type_id = 4,B.quantity,0)) today_s_out,
                        sum(if(A.inventory_type_id = 3,B.quantity,0)) today_s_breakage,
                        sum(if(A.inventory_type_id = 1,B.quantity,0)) today_s_check,
                        sum(if(A.inventory_type_id = 5,B.quantity,0)) today_s_pos_retail
                        from oc_x_inventory_move A
                        left join oc_x_inventory_move_item as B on A.inventory_move_id = B.inventory_move_id
                        left join oc_x_inventory_type as C on A.inventory_type_id = C.inventory_type_id
                        left join oc_product_description as D on B.product_id = D.product_id and D.language_id = 2
                        where date(A.date_added) = '".$date."' and B.status = 1
                        group by B.product_id
                        order by A.inventory_move_id, B.product_id
                        ) AA on PP.product_id = AA.product_id
                    left join
                        (select
                        B.product_id,
                        D.name,
                        round(avg(B.price),2) price,
                        sum(B.quantity) yesterday_s_check
                        from oc_x_inventory_move A
                        left join oc_x_inventory_move_item as B on A.inventory_move_id = B.inventory_move_id
                        left join oc_x_inventory_type as C on A.inventory_type_id = C.inventory_type_id
                        left join oc_product_description as D on B.product_id = D.product_id and D.language_id = 2
                        where date(A.date_added) = DATE_SUB(date('".$date."'), INTERVAL 1 DAY) and B.status = 1
                        and A.inventory_type_id = 1
                        group by B.product_id
                        order by A.inventory_move_id, B.product_id
                        ) BB on PP.product_id = BB.product_id;";
        }
        $query = $this->db->query($sql);
        $result = $query->rows;

        $data['moveInfo'] = $result;

        if(!sizeof($data['moveInfo'])){
            $data['orderInfo'] = array();
            $data['moveInfo'] = array();

            return $data;
        }

        //Get and sort orders info
        $data['orderInfo'] = array();
        if($station_id == -1){
            $sql = "select
                OP.product_id,
                round(avg(OP.price),2) sale_avg_price,
                sum(OP.quantity) sale_qty,
                round(sum(OP.total),2) sale_total,
                date(O.date_added) order_date
                from xsj.oc_order O
                left join xsj.oc_order_product OP on O.order_id = OP.order_id
                where O.deliver_date ='".$date."' and OP.status = 1
                and O.shipping_code in ('D2D','PSPOT')
                group by OP.product_id";
            $query = $this->db->query($sql);
            $result = $query->rows;

            $orderInfo = array();
            foreach($result as $m){
                $orderInfo[$m['product_id']] = $m;
            }
            $data['orderInfo'] = $orderInfo;
        }

        //unset($orderProducts);
        //$data['orderInfo'] = $orderInfo;
        //unset($orderInfo);

        return $data;
    }
}