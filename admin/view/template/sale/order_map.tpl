<?php echo $header; ?><?php echo $column_left; ?>
<script type="text/javascript" src=" http://api.map.baidu.com/api?v=1.5&ak=TkbDdiAKKOmHBuHDMeHQk0eO"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/TextIconOverlay/1.2/src/TextIconOverlay_min.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/MarkerClusterer/1.2/src/MarkerClusterer_min.js"></script>

 <div style="width:100%;min-width:500px;min-height:425px;height:100%;">

 <a  href="index.php?route=logistic/van_allocate&token=<?php echo $token; ?>&deliver_date=<?php echo $deliver_date; ?>&time_slot=<?php echo $time_slot; ?>&wave=<?php echo $wave; ?>&region=<?php echo $region; ?>&van_id=<?php echo $van_id; ?>" class="button" style="margin: 5px 0;">
        <?php echo "列表分车"; ?>
    </a>

	 <a  href="javascript:" class="button" style="margin: 5px 0;" onclick="showscreen(this)">
        <?php echo "全屏"; ?>
    </a>
	<div id="map" style="width:75%;height:100%;min-height:570px;float:left;height:570px;" > </div>
	 <div  id="infolistdiv" style = "width:25%;min-width:200px;min-height:560px;height:570px;float:left;overflow:scroll;" >
	   <div id="infolistdiv2">
	   <select name="selectOders" id="selectOders" onchange="selectOrdersType(this)" ><option value="all">全部订单</option><option value="vans">已分配订单</option><option value="not" selected>未分配订单</option><option value="lost">未显示订单</option></select><br>
	   
	   <a href="javascript:checkAllOrders()">全选</a>&nbsp;<a href="javascript:cancleAllOrders();">全不选</a>&nbsp;<select name="allSelect" id="allSelect">
	 <option value="">分配车辆</option>
	 <?php foreach($vans as $van){ ?>
	 <option value='<?php echo $van["logistic_van_id"];?>'><?php echo $van["code"];?></option>
	 <?php }?>
	 </select>&nbsp;<input type="button" value="确定" onclick="doChangeCar()">&nbsp;</div>

    <div id="order_list">
	 <?php 
	for ($i=0;$i<count($not_allocated_orders);$i++){
         if($not_allocated_orders[$i]["van_id"] !=""){
				continue;
		 }

		 
		  if(trim($not_allocated_orders[$i]["SHIPPING_ADDRESS_CN"]) != "" ){
				$show_address =  $not_allocated_orders[$i]["SHIPPING_ADDRESS_CN"];
			 } else {
				$show_address = $not_allocated_orders[$i]["SHIPPING_ADDRESS"];
			 }
		
		?>
	<table class="list" id="to_<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>">
     <tr>
	   <td width="25%"><b>订单编号</b></td>
	   <td><a class="showpops" href="javascript:" orderId='<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>' onclick="showpops(this)"><?php echo $not_allocated_orders[$i]["ORDER_ID"]?><a><input type="checkbox" name="orders" value="<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>"></td>
     </tr>

	  <tr>
	   <td><b>货架编号</b></td>
	   <td><?php echo $not_allocated_orders[$i]['SHELF_INFO']?></td>
     </tr>

	 <tr>
	   <td><b>送货地址</b></td>
	   <td><?php echo $not_allocated_orders[$i]["CITY"]." ".$not_allocated_orders[$i]["AREA"]." ".$show_address?></td>
     </tr>

	 <tr>
	   <td><b>送货时间</b></td>
	   <td><?php echo $not_allocated_orders[$i]["DELIVER_TIME"];
	             if($not_allocated_orders[$i]["SPEC_DELIVER_TIME"]){
					echo  "(".$not_allocated_orders[$i]["SPEC_DELIVER_TIME"].")";
				 }
	   ?></td>
     </tr>
	
	<tr>
	   <td><b>订单状态</b></td>
	   <td><?php echo $not_allocated_orders[$i]["order_status"];?></td>
     </tr>

	 <tr>
	   <td><b>配送车辆</b></td>
	   <td>
	      <div name="carsdivs" id="scars_<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>">
	        <p id="carsp_<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>"><?php echo strtoupper($not_allocated_orders[$i]["van_id"]);?></p>
	        <input type="button" value="分配车辆"  onclick="showCarsSelect('<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>')">
		  </div>
			<br>
			<div id="van_val_<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>" old_van='<?php echo $not_allocated_orders[$i]["van_val"]?>'       regionId='<?php echo $not_allocated_orders[$i]["region_id"]?>'  ></div>
			<div  name="carSelectDiv" id="carss_<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>" style="display:none;">
			<select id="car_select_<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>">
			  <option value="">---</option>
			  <?php foreach ($vans as $van){
			    echo "<option value=".$van["logistic_van_id"].">".$van["code"]."</option>";
			  }?>
			</select>
			<br><input type="button" value="确定" onclick="add2Cars('<?php echo $not_allocated_orders[$i]["ORDER_ID"]?>')"> <input type="button" value="取消" onclick="cancelselect()">
			</div>
	   
	   </td>
     </tr>
	 </table>
     <?php }?>
	</div>
 </div>
 </div>
<div id="point_p" style="display:none;"></div>
<script type="text/javascript">
<!--
      <?php foreach ($vans as $van){
		  $url = "./view/image/map/".strtolower($van["code"]).".png" ;
		   ?>
       preLoadImg("<?php echo $url?>");
	  <?php }?>
function preLoadImg(url) {
 var img = new Image();
 img.src = url;
}

  var allscreenFlag = 0;
  function showscreen(o){
	  
	  if(allscreenFlag == 0 ){
	     $("#map").css("width","100%");
		  $("#infolistdiv").hide();
		  $(o).html("取消全屏");
		  allscreenFlag =1;
	  } else {
		 $("#map").css("width","75%");
		 $("#infolistdiv").show();
		  $(o).html("全屏");
		  allscreenFlag =0;
	  }
     
	 
  }


   var markers = [];
   var order_arr = new Array();
   var point_arr = new Array();
   var time_slot  = "<?php echo $time_slot; ?>";
   var regions  = "<?php echo $region; ?>";
   var show_orders = ""; 
   var EXAMPLE_URL = "http://api.map.baidu.com/library/MarkerClusterer/1.2/examples/";
   var MapOptions = {
		enableMapClick :false
   }
    var mp = new BMap.Map('map',MapOptions);  
//    var  mapStyle ={ 
//        //features: ["road", "building","water","land"],//隐藏地图上的poi
//        style : "light"  //设置地图风格为高端黑
//    }
//    mp.setMapStyle(mapStyle);

    mp.centerAndZoom(new BMap.Point(121.491, 31.233), 13);
	mp.addControl(new BMap.NavigationControl());
	mp.enableScrollWheelZoom();    //启用滚轮放大缩小，默认禁用
    mp.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用
	mp.clearOverlays();
	var market  = <?php echo json_encode($not_allocated_orders)?>;
	var market2 = <?php echo json_encode($not_allocated_orders2)?>;
	var van_arr = <?php echo json_encode($vans);?>;
	var markerClusterer = new BMapLib.MarkerClusterer(mp);
	var str_title = "";
    var address ="";
	var index_str ="";
    var order_html= "";
	var new_market = "";
	var myGeo = new BMap.Geocoder();
	mp.clearOverlays();
    for(var t=0;t<market.length;t++)
	//for(var t=0;t<2;t++)
    {
	   address = market[t]["ADDRESS"];
	   myGeo.getPoint(address,function (point,i){   
			 // 创建标注对象并添加到地图			
             if(point !== null){
				var point_str = point.lng+"|"+point.lat;
                getback(point_str,i["address"]);
			 }
	  },market[t]["CITY"]);   
    }
	

function getback(point_str,address){
	 //market
	 var marker_address="";

	  var sContent = "";
      
    sContent +="<div style='height:250px;overflow-y:auto;'>";
	 for(var i=0;i<market.length;i++)
		//for(var i=0;i<2;i++)
     {
		   marker_address = market[i]["ADDRESS"];

		   if(market[i]["point"])
		   {
			   continue;
		   }

		if(marker_address == address){
			 
			 var addhtml = "<p point='"+point_str+"' name='point_p' order='"+market[i]["ORDER_ID"]+"' >"+marker_address+"</p>";
             var htmls = $("#point_p").html();
			 $("#point_p").html("");
             $("#point_p").html(htmls+addhtml);
		
			market[i].point = point_str;
		
            var jwd =  point_str.split("|")
			var point = new BMap.Point(jwd[0], jwd[1]);
            
			var pngName = "no";
			if($.trim(market[i]["van_id"])!=""){
				pngName=market[i]["van_id"];
			}
			var myIcon = new BMap.Icon("./view/image/map/"+pngName+".png", new BMap.Size(39, 25), {   
			// 指定定位位置。   
			// 当标注显示在地图上时，其所指向的地理位置距离图标左上   
			 offset: new BMap.Size(10, 25),   
			 // 设置图片偏移。   
			 // 当您需要从一幅较大的图片中截取某部分作为标注图标时，您   
			 // 需要指定大图的偏移位置，此做法与css sprites技术类似。   
			 imageOffset: new BMap.Size(0, 0 - 0 * 25)   // 设置图片偏移
			 });     
			 // 创建标注对象并添加到地图   

            orderId = market[i]["ORDER_ID"]
			
	         
			 var show_address = "";
             if($.trim(market[i]["SHIPPING_ADDRESS_CN"]) != "" ){
				show_address = market[i]["SHIPPING_ADDRESS_CN"];
			 } else {
				show_address = market[i]["SHIPPING_ADDRESS"];
			 }
			 var marker = new BMap.Marker(point,{icon:myIcon});
             

			   //var sContent = "";
               var points = $("p[name='point_p']");
			   var point_arrs = new Array();;

			   var showOrderID ="";
			   var showVanId =""
		
			  marker.addEventListener("click", function(){          		  
			  showPointInfo(this);
              //mp.openInfoWindow(infoWindow, point);
			  //marker.openInfoWindow(infoWindow);
			 });
			  mp.addOverlay(marker);
           
			
		}
	 }
	            
}


function showPointInfo(marker){

     var points = $("p[name='point_p']");
	 var jwd1 = marker.getPosition().lng;
     var jwd2 = marker.getPosition().lat;
	 var point = new BMap.Point(jwd1, jwd2);
	 var  point_str =  "";
                var sContent = "";
				var point_arrs = new Array();;

			   var showOrderID ="";
			   var showVanId =""
              sContent +="<div style='height:250px;overflow-y:auto;'>";
              var selecthtml = "";
			  var regionId = "";
             for(var i=0;i<points.length;i++){
				   var obj =  points[i];
			        var jw=$(obj).attr("point");
					var arr_str=jw.split("|"); 
					var lng = arr_str[0];
					var lat = arr_str[1];
					var orderId = $(obj).attr('order');
                      
                    if(jwd1 == lng && jwd2 == lat){
                       point_str = jw;
					  if($.trim(market2[$(obj).attr("order")]["SHIPPING_ADDRESS_CN"]) != "" ){
							show_address = market2[orderId]["SHIPPING_ADDRESS_CN"];
						 } else {
							show_address = market2[orderId]["SHIPPING_ADDRESS"];
						 }
						  showVanId =market2[orderId].van_val;
						 regionId = market2[orderId].region_id;
					     sContent +="<h4 style='margin:0 0 5px 0;padding:0.2em 0'>Order_id :"+market2[orderId]["ORDER_ID"]+" </h4>" 
						 +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>货架号:"+market2[orderId]["SHELF_INFO"]+"</p>"
						 +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>配送地址:"+show_address+"</p>"
				         +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>订单状态:"+market2[orderId]["order_status"]+"</p>"
				         +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>配送时间:"+market2[orderId]["DELIVER_TIME"]+"</p>"
				         +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>配送车辆:"+market2[orderId]["van_id"].toUpperCase()+"</p>"

                       if(checkArrIn(orderId,point_arrs)){
							continue;
					   } else {
							point_arrs.push(orderId);
					   }

					} else {
					  //point_arrs.push(orderId);
					}


			 }		
		  
			var selecthtml = "";
			 showOrderID = point_arrs.join(",");

			 //alert(showOrderID);
			for(var n=0;n<van_arr.length;n++){
				selecthtml +="<a order_id='"+showOrderID+"' van_id='"+van_arr[n].logistic_van_id+"' point_str='"+point_str+"' old_van='"+showVanId+"' regionId ='"+regionId+"' onclick='changemapcars(this)' >"+van_arr[n].code+"</a> &nbsp;";
			}
            if(showVanId){
				selecthtml +="<a order_id='"+showOrderID+"' van_id='' point_str='"+point_str+"' old_van='"+showVanId+"' regionId ='"+regionId+"' onclick='changemapcars(this)' >取消</a> &nbsp;";
			}

			sContent +="<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>选择配送车辆:"+selecthtml+"</p>";
			sContent +="</div>";

			//alert(sContent);
			var infoWindow = new BMap.InfoWindow(sContent); 
			//marker.openInfoWindow(infoWindow);
			mp.openInfoWindow(infoWindow, point);
			     
}

function showInfoWindow(marker){
   var jwd1 = marker.getPosition().lng;
   var jwd2 = marker.getPosition().lat;
    var points = $("p[name='point_p']");
	 var point_arrs = new Array();;
	  var orderId = "";
    for(var i=0;i<points.length;i++){
		 var obj =  points[i];
		 var jw=$(obj).attr("point");
		 var arr_str=jw.split("|"); 
		 var lng = arr_str[0];
		 var lat = arr_str[1];
		 orderId = $(obj).attr('order');
          if(jwd1 == lng && jwd2 == lat){
				if(point_arrs.length>0){
					   if(checkArrIn(orderId,point_arrs)){
							continue;
					   } else {
							point_arrs.push(orderId);
					   }
					} else {
					   point_arrs.push(orderId);
					}
              point_str = jw;
      
		  }



	}

	showPointInfo(marker);
}


  mp.addEventListener("zoomend",function(e){

	     var map= e.currentTarget;
		 //map.getBounds().getSouthWest().lng);  //西南点经度
	     //alert(map.getBounds().getSouthWest().lat);  //西南点纬度
         var sw_point_lng= map.getBounds().getSouthWest().lng;
		 var sw_point_lat= map.getBounds().getSouthWest().lat;
         var ne_point_lng = map.getBounds().getNorthEast().lng;
		 var ne_point_lat = map.getBounds().getNorthEast().lat;        
		// alert(map.getBounds().getNorthEast().lng);  //东北点经度
        //alert(map.getBounds().getNorthEast().lat);  //东北点纬度

		showAreaOrder(sw_point_lng,sw_point_lat,ne_point_lng,ne_point_lat);
    
  });



    mp.addEventListener("moveend",function(e){
         
	     var map= e.currentTarget;
		 //map.getBounds().getSouthWest().lng);  //西南点经度
	     //alert(map.getBounds().getSouthWest().lat);  //西南点纬度
         var sw_point_lng= map.getBounds().getSouthWest().lng;
		 var sw_point_lat= map.getBounds().getSouthWest().lat;
         var ne_point_lng = map.getBounds().getNorthEast().lng;
		 var ne_point_lat = map.getBounds().getNorthEast().lat;        
		// alert(map.getBounds().getNorthEast().lng);  //东北点经度
        //alert(map.getBounds().getNorthEast().lat);  //东北点纬度
		showAreaOrder(sw_point_lng,sw_point_lat,ne_point_lng,ne_point_lat);
        
		//alert();
    
  });


  function showAreaOrder(sw_point_lng,sw_point_lat,ne_point_lng,ne_point_lat){
       

	   
	   var types = $("#selectOders").val();
	   if(types == "lost"){
	       return false;
	   }

	   $("#order_list").html("");
       var selecthtml = "";
	    var point_arrs = new Array();;
       for(var n=0;n<van_arr.length;n++){
			selecthtml +="<option value='"+van_arr[n].logistic_van_id+"'>"+van_arr[n].code+"</option>";
	   }
       var showhtml = "";
       var points = $("p[name='point_p']");	  
       var list_str="";
	   show_orders ="";
       for(var i=0;i<points.length;i++){
              
			  var obj =  points[i];
			  var jw=$(obj).attr("point");
			  var arr_str=jw.split("|"); 
			  var lng = arr_str[0];
			  var lat = arr_str[1];
			  var orderId = $(obj).attr('order');
              

			  if(point_arrs.length>0){
					   if(checkArrIn(orderId,point_arrs)){
							continue;
					   } else {
							point_arrs.push(orderId);
					   }
					} else {
                       
					   point_arrs.push(orderId);
					}




			if(lng >= sw_point_lng &&  lat >= sw_point_lat ){
				 if(lng <= ne_point_lng && lat <= ne_point_lat){
					
              var show_address = "";
             if($.trim(market2[$(obj).attr("order")]["SHIPPING_ADDRESS_CN"]) != "" ){
				show_address = market2[$(obj).attr("order")]["SHIPPING_ADDRESS_CN"];
			 } else {
				show_address = market2[$(obj).attr("order")]["SHIPPING_ADDRESS"];
			 }
             
             show_orders += $(obj).attr("order")+",";
			 if(types == "vans" && market2[$(obj).attr("order")].van_id == ""){
					continue;
			 }
        
			if(types == "not" && $.trim(market2[$(obj).attr("order")].van_id) != "" ){
					continue;
			}

                  showhtml+="<table class='list' id='to_"+market2[$(obj).attr("order")].ORDER_ID+"'><tr>"
	   +"<td width='25%'><b>订单编号</b></td><td><a  orderId='"+market2[$(obj).attr("order")].ORDER_ID+"' href='javascript:' onclick='showpops(this)'>"+market2[$(obj).attr("order")].ORDER_ID+"</a><input type='checkbox' name='orders' value='"+market2[$(obj).attr("order")].ORDER_ID+"' ></td></tr>"
       +"<tr><td><b>货架编号</b></td><td>"+market2[$(obj).attr("order")].SHELF_INFO+"</td></tr>"
       +"<tr><td><b>送货地址</b></td><td>"+market2[$(obj).attr("order")].CITY+" "+market2[$(obj).attr("order")].AREA+" "+show_address+"</td></tr>"
       +"<tr><td><b>送货时间</b></td><td>"+market2[$(obj).attr("order")].DELIVER_TIME+"</td></tr>"
	   +"<tr><td><b>订单状态</b></td><td>"+market2[$(obj).attr("order")].order_status+"</td></tr>"
	   +"<tr><td><b>配送车辆</b></td><td><div name='carsdivs' id='scars_"+market2[$(obj).attr("order")].ORDER_ID+"'><p id='carsp_"+orderId+"'>"+market2[$(obj).attr("order")].van_id.toUpperCase()+"</p>"
	   +"<input type='button' value='分配车辆'  onclick='showCarsSelect(\""+orderId+"\")'></div><br>"
	   +"<div style='display:none' id='van_val_"+orderId+"' old_van='"+market2[$(obj).attr("order")].van_val+"'  regionId ='"+market2[$(obj).attr("order")].region_id+"' ></div>"
	   +"<div  name='carSelectDiv' id='carss_"+orderId+"' style='display:none;'>"
	   +"<select id='car_select_"+orderId+"'><option value=''>---</option>"
	   +selecthtml
	   +"</select>"
	   +"<input type='button' value='确定' onclick='add2Cars("+$.trim(orderId)+")' > <input type='button' value='取消' onclick='cancelselect()'>"
	   +"</div></td>"
	   +"</table>";		 
				 }
			}
		}


		$("#order_list").html("");
    	$("#order_list").html(showhtml);
		showhtml="";
  
  
  
  }

function showCarsSelect(oderid){
   cancelselect();
   $("#carss_"+oderid).show();
   $("#scars_"+oderid).hide();
}


function cancelselect(){

    var divs = $("div[name='carsdivs']");
	var divselects = $("div[name='carSelectDiv']");
    for(var i=0;i<divselects.length;i++){
	      $(divselects[i]).hide();
		  $(divs[i]).show();
	}
}

function add2Cars(orderID){
    var van_id = $("#car_select_"+orderID).val();
    var van_old = $("#van_val_"+orderID).attr("old_van");
	var region = $("#van_val_"+orderID).attr("regionId");
	var checkText=$("#car_select_"+orderID).find("option:selected").text();
    var sltypes = $("#selectOders").val();

	
        if(time_slot == 0){
            alert('尚未指定送货时间段！');
            return false;
        }
    $.ajax({
	   type: "get",
	   url: "index.php?route=logistic/appoint/add2cars",
	   //datatype: "html",
	   data: "order_id="+orderID+"&van_id="+van_id+"&old_van="+van_old+"&region="+region+"&time_slot=<?php echo $time_slot?>&deliver_date=<?php echo $deliver_date?>&token=<?php echo $token;?>&rand="+Math.random(),
	   success: function(msg){
		    if(msg){
				$("#van_val_"+orderID).attr("old_van",van_id);
				$("#carsp_"+orderID).html(checkText);
				//alert("分派成功！");
				if(sltypes == "not" ){
					    $("#to_"+orderID).hide();
				}
                var points = $("p[name='point_p']");
				 for(var i=0;i<points.length;i++){
					 var obj =  points[i];
					 var s_order_id =$(obj).attr("order");
					 var jw=$(obj).attr("point");

					 if(sltypes == "all"){
					    $("to_"+orderID).hide();
					 }

                      if(van_id !="") {
						market2[orderID]["van_id"]= checkText;
					  }else {
						market2[orderID]["van_id"]="";
					  }
					  market2[orderID]["van_val"]= van_id;
					  ChgMarketVanId(orderID,checkText,van_id);
					 if(s_order_id  == orderID){
						// market2[orderID]["van_id"]= checkText;
					     showpoints(orderID,jw,0);
					     break;
					 }
				 }
				
				cancelselect();
			} else {      
				alert("分派出错！");
			}
	   }
	 });
}


function changemapcars(o){
	var orderID = $(o).attr("order_id");
    var van_id = $(o).attr("van_id");
	var point_str = $(o).attr("point_str");
	var old_van = $(o).attr("old_van");
	var region = $(o).attr("regionId");
    var showcode  = $(o).html();
	  if (showcode == "取消")
	  {
		  showcode="";
	  }
	var sltypes = $("#selectOders").val();
	var checkText=$("#car_select_"+orderID).find("option:selected").text();
	var order_str="";
	
	
    if(time_slot == 0){
            alert('尚未指定送货时间段！');
            return false;
        }
	$.ajax({
	   type: "get",
	   url: "index.php?route=logistic/appoint/add2cars",
	   //datatype: "html",
	   data: "order_id="+orderID+"&van_id="+van_id+"&old_van="+old_van+"&region="+region+"&time_slot=<?php echo $time_slot?>&deliver_date=<?php echo $deliver_date?>&token=<?php echo $token;?>&rand="+Math.random(),
	   success: function(msg){
		    if(msg){
				var orderArrTmp = orderID.split(",");
				if(orderArrTmp.length >1){
					for( var i=0;i<orderArrTmp.length;i++){
					$("#van_val_"+orderArrTmp[i]).attr("old_van",van_id);
					$("#carsp_"+orderArrTmp[i]).html(showcode);
					var showorders = orderArrTmp[i];
					//alert("分派成功！");
					market2[orderArrTmp[i]]["van_id"] = showcode;
					market2[orderArrTmp[i]]["van_val"] =  van_id;
					ChgMarketVanId(orderID,showcode,van_id);
						if(sltypes == "not"){
					    $("#to_"+orderArrTmp[i]).hide();
						}
					}
					showpoints(showorders,point_str,1);
				}else { 

					$("#van_val_"+orderArrTmp).attr("old_van",van_id);
					$("#carsp_"+orderArrTmp).html(showcode);
					//alert("分派成功！");
					market2[orderArrTmp]["van_id"] = showcode;
					market2[orderArrTmp]["van_val"] =  van_id;
					ChgMarketVanId(orderArrTmp,showcode,van_id);
					showpoints(orderArrTmp,point_str,1);
                }
				cancelselect();
			} else {      
				alert("分派出错！");
			}
	   }
	 });
    
}


function showpoints(orderID,point_str,showflag){
    var jwd =  point_str.split("|")
	var point = new BMap.Point(jwd[0], jwd[1]);

	//alert(market2[orderID]["van_id"].toLowerCase());
    var imgName = "";
	//alert(market2[orderID]["van_id"]);
	if(market2[orderID]["van_id"]==""){
	     imgName="no";
	}else {
		 imgName=market2[orderID]["van_id"].toLowerCase();
	}
    var myIcon = new BMap.Icon("./view/image/map/"+imgName+".png", new BMap.Size(39, 25), {   
			// 指定定位位置。   
			// 当标注显示在地图上时，其所指向的地理位置距离图标左上   
			 offset: new BMap.Size(10, 25),   
			 // 设置图片偏移。   
			 // 当您需要从一幅较大的图片中截取某部分作为标注图标时，您   
			 // 需要指定大图的偏移位置，此做法与css sprites技术类似。   
			  imageOffset: new BMap.Size(0,0 - 0 * 50)   // 设置图片偏移
			 }); 
		    var marker = new BMap.Marker(point,{icon:myIcon});
	        
			 var show_address = "";
             if($.trim(market2[orderID]["SHIPPING_ADDRESS_CN"]) != "" ){
				show_address = market2[orderID]["SHIPPING_ADDRESS_CN"];
			 } else {
				show_address = market2[orderID]["SHIPPING_ADDRESS"];
			 }
			 var marker = new BMap.Marker(point,{icon:myIcon});
               var sContent = "";
               var points = $("p[name='point_p']");
			   var point_arrs = new Array();;

			   var showOrderID ="";
			   var showVanId =""
			    var regionId = "";
			   sContent +="<div style='height:200px;overflow-y:auto;'>";
			   for(var i=0;i<points.length;i++){
				   var obj =  points[i];
			        var jw=$(obj).attr("point");
					var arr_str=jw.split("|"); 
					var lng = arr_str[0];
					var lat = arr_str[1];
					var orderId = $(obj).attr('order');
                    if(jwd[0] == lng && jwd[1] == lat){
					if(point_arrs.length>0){
					   if(checkArrIn(orderId,point_arrs)){
							continue;
					   } else {
							point_arrs.push(orderId);
					   }
					} else {
					   point_arrs.push(orderId);
					}
						if($.trim(market2[$(obj).attr("order")]["SHIPPING_ADDRESS_CN"]) != "" ){
							show_address = market2[orderId]["SHIPPING_ADDRESS_CN"];
						 } else {
							show_address = market2[orderId]["SHIPPING_ADDRESS"];
						 }
						  showVanId =market2[orderId].van_val;
						 regionId = market2[orderId].region_id;
					     sContent +="<h4 style='margin:0 0 5px 0;padding:0.2em 0'>Order_id :"+market2[orderId]["ORDER_ID"]+" </h4>" 
						 +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>货架号:"+market2[orderId]["SHELF_INFO"]+"</p>"
						 +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>配送地址:"+show_address+"</p>"
				         +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>订单状态:"+market2[orderId]["order_status"]+"</p>"
				         +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>配送时间:"+market2[orderId]["DELIVER_TIME"]+"</p>"
				         +"<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>配送车辆:"+market2[orderId]["van_id"].toUpperCase()+"</p>"
					   }
			   }
            
            showOrderID = point_arrs.join(",");
            var selecthtml = "";
			 showOrderID = point_arrs.join(",");
			for(var n=0;n<van_arr.length;n++){
				selecthtml +="<a order_id='"+showOrderID+"' van_id='"+van_arr[n].logistic_van_id+"' point_str='"+point_str+"' old_van='"+showVanId+"' regionId ='"+regionId+"' onclick='changemapcars(this)' >"+van_arr[n].code+"</a> &nbsp;";
			}
			if(showVanId){
				selecthtml +="<a order_id='"+showOrderID+"' van_id='' regionId ='"+regionId+"' point_str='"+point_str+"' old_van='"+showVanId+"' onclick='changemapcars(this)' >取消</a> &nbsp;";
			}
			sContent +="<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>选择配送车辆:"+selecthtml+"</p></div>"
			var infoWindow = new BMap.InfoWindow(sContent); 
             marker.addEventListener("click", function(){          
			   this.openInfoWindow(infoWindow);
			   //图片加载完毕重绘infowindow
			 });
            if(showflag == 0 ){
				mp.closeInfoWindow();
				mp.openInfoWindow(infoWindow, point);
			} else {
			    mp.closeInfoWindow();
			}
	         mp.addOverlay(marker);
}


function checkAllOrders(){
	var checkboxs = $("input[name='orders']");
	 for(var i=0;i<checkboxs.length;i++){
          var checkbox = checkboxs[i];
		  checkbox.checked = true;

	 }
}

function cancleAllOrders(){
	var checkboxs = $("input[name='orders']");
	 for(var i=0;i<checkboxs.length;i++){
          var checkbox = checkboxs[i];
		  checkbox.checked = "";

	 }
}


function doChangeCar(){
	 url = 'index.php?route=logistic/appoint/addCars&token=<?php echo $token; ?>&time_slot=<?php echo $time_slot; ?>&region=<?php echo $region; ?>&deliver_date=<?php echo $deliver_date?>';
     var obj = ("orders");

        if(time_slot == 0){
            alert('尚未指定送货时间段！');
            return false;
        }

		if(regions == 0){
            alert('尚未指定送货区域！');
            return false;
        }
	 if (obj) {
            if(jqchk(obj)){
                url += '&order_id=' + jqchk(obj);
            }
            else{
                alert('还没有选择订单!');
                return false;
            }
        }
    
	    var van_id = $("#allSelect").val();
		if (van_id) {
            url += '&van_id=' + encodeURIComponent(van_id);
        }
        else{
            alert('尚未指定送货车辆');
            return false;
        }
        location = url;

}


   function jqchk(obj){
        var chk_value =[];
        $('input[name='+obj+']:checked').each(function(){
            chk_value.push($(this).val());
        });

        if(chk_value.length==0){
            return false;
        }
        return chk_value;
    }


function query() {
        url = 'index.php?route=logistic/appoint&token=<?php echo $token; ?>&van_id=<?php echo $van_id; ?>&print=<?php echo $print; ?>';

        var deliver_date = $('input[name=\'deliver_date\']').attr('value');

        if (deliver_date) {
            url += '&deliver_date=' + encodeURIComponent(deliver_date);
        }

        var time_slot = $('select[name=\'time_slot\']').attr('value');

        if (time_slot) {
            url += '&time_slot=' + encodeURIComponent(time_slot);
        }

        var region = $('select[name=\'region\']').attr('value');

        if (region) {
            url += '&region=' + encodeURIComponent(region);
        }

        location = url;
    }


function selectOrdersType(o){
	var types = $(o).val();
    var showhtml ="";
	var order_list = "";
	var show_array = "";
    if(show_orders !=""){
	     show_array = show_orders.split(",");
	}
	var showpops_html = "";

    
     //for(var i=0;i<10;i++)
	 for(var i=0;i<market.length;i++)
     {
        orderId = market[i].ORDER_ID;
		var show_address = "";
		if($.trim(market[i]["SHIPPING_ADDRESS_CN"]) != "" ){
			show_address = market[i]["SHIPPING_ADDRESS_CN"];
		} else {
			show_address = market[i]["SHIPPING_ADDRESS"];
		}
        if(types != "lost"){
           if(show_orders){
				if(!checkArrIn(orderId,show_array)){
					continue;
				}
			}
			showpops_html = "onclick='showpops(this)'";
			if(types == "vans" && market[i].van_id == ""){
				continue;
			}
        
			if(types == "not" && $.trim(market[i].van_id) != "" ){
				continue;
			}
        
			
		} else {
			   var points = $("p[name='point_p']");
			   var flag = 0;
			   for(var n=0;n<points.length;n++){
					 var obj =  points[n];
					 var s_order_id =$(obj).attr("order");
					 var jw=$(obj).attr("point");
					 if(orderId == s_order_id ){
						 flag =  1 ;
						continue;
					 }
			   }

			   if( flag  == 1){
			       continue;
			   }

			   showpops_html = "";
		}
		
	   var selecthtml = "";
       for(var n=0;n<van_arr.length;n++){
			selecthtml +="<option value='"+van_arr[n].logistic_van_id+"'>"+van_arr[n].code+"</option>";
	   }

       showhtml+="<table class='list' id='to_"+market[i].ORDER_ID+"'><tr>"
	   +"<td width='25%'><b>订单编号</b></td><td><a  orderId='"+market[i].ORDER_ID+"' href='javascript:' "+showpops_html+" >"+market[i].ORDER_ID+"</a><input type='checkbox' name='orders' value='"+market[i].ORDER_ID+"' ></td></tr>"
       +"<tr><td><b>货架编号</b></td><td>"+market[i].SHELF_INFO+"</td></tr>"
       +"<tr><td><b>送货地址</b></td><td>"+market[i].CITY+" "+market[i].AREA+" "+show_address+"</td></tr>"
       +"<tr><td><b>送货时间</b></td><td>"+market[i].DELIVER_TIME+"</td></tr>"
	   +"<tr><td><b>订单状态</b></td><td>"+market[i].order_status+"</td></tr>"
	   +"<tr><td><b>配送车辆</b></td><td><div name='carsdivs' id='scars_"+market[i].ORDER_ID+"'><p id='carsp_"+orderId+"'>"+market[i].van_id.toUpperCase()+"</p>"
	   +"<input type='button' value='分配车辆'  onclick='showCarsSelect(\""+orderId+"\")'></div><br>"
	   +"<div style='display:none' id='van_val_"+orderId+"' old_van='"+market[i].van_val+"' regionId ='"+market[i].region_id+"' ></div>"
	   +"<div  name='carSelectDiv' id='carss_"+orderId+"' style='display:none;'>"
	   +"<select id='car_select_"+orderId+"'><option value=''>---</option>"
	   +selecthtml
	   +"</select>"
	   +"<input type='button' value='确定' onclick='add2Cars("+$.trim(orderId)+")' > <input type='button' value='取消' onclick='cancelselect()'>"
	   +"</div></td>"
	   +"</table>";		 
     }
      $("#order_list").html("");
	  $("#order_list").html(showhtml);
}


function checkArrIn(v,arr)
{
	var flag = false;
	for(var i=0 ;i<arr.length;i++){
	     if(arr[i]==v){
		   return true;
		 }
	}
	return false;

}


function ChgMarketVanId(orderId,van_id,van_val){
	for(var i=0;i<market.length;i++){
		if(market[i].ORDER_ID ==orderId ){
		    market[i].van_id = van_id;
			 market[i].van_val = van_val;	
		}
	}
}

function showpops(o){

				var orderID = $(o).attr("orderId");//全选
				var points = $("p[name='point_p']");	  
				var list_str="";
		      
				for(var i=0;i<points.length;i++){
					var obj =  points[i];
					var s_order_id =$(obj).attr("order");
					if(s_order_id != orderID )
					{
						continue;
					} else {
						
					   showpoints(orderID,$(obj).attr("point"),0);
                       return false;
					}	
			}


}

 $(document).ready(function() {
        $('#deliver_date').datepicker({dateFormat: 'yy-mm-dd'});
    });
//-->
</script>
<?php echo $footer; ?>