<?php

//$prefix = 'B10-'; //99
//$prefix = 'B20-'; //99
//$prefix = 'B30-'; //99
//$prefix = 'B40-'; //99

//获取变量
$prefix = isset($_REQUEST['prefix']) ? $_REQUEST['prefix'] : 'A';
$m1 = isset($_REQUEST['m1']) ? $_REQUEST['m1'] : '2';
$m2 = isset($_REQUEST['m2']) ? $_REQUEST['m2'] : '3';
$a1 = isset($_REQUEST['a1']) ? $_REQUEST['a1'] : '1';
$a2 = isset($_REQUEST['a2']) ? $_REQUEST['a2'] : '2';
$b1 = isset($_REQUEST['b1']) ? $_REQUEST['b1'] : '1';
$b2 = isset($_REQUEST['b2']) ? $_REQUEST['b2'] : '2';
$c1 = isset($_REQUEST['c1']) ? $_REQUEST['c1'] : '0';
$c2 = isset($_REQUEST['c2']) ? $_REQUEST['c2'] : '0';
$lineNum = isset($_REQUEST['lineNum']) ? $_REQUEST['lineNum'] : 1;
$download = isset($_REQUEST['download']) ? true : false;

$str = '';
$chl = $download ? "\r\n" : "<br />";


//检查数据格式
if(!is_numeric($m1) || !is_numeric($m1) || !is_numeric($a1) || !is_numeric($a2) || !is_numeric($b1) || !is_numeric($b2) || !is_numeric($c1) || !is_numeric($c2)){
	$str = 'ERROR';
	$message = '除前缀，其他段位区间必须为数字。';
}

if($m1>$m2 || $a1>$a2 || $b1>$b2 || $c1>$c2){
	$str = 'ERROR';
	$message = '段位区间数字大小设置错误。';
}

if($m1<0){
	$str = 'ERROR';
	$message = '第一段位区间数字必须大于0';
}

$strList = array();
for($i=$m1;$i<=$m2;$i++){
	if($a1<1){
		$strList[] = $prefix.str_pad($i,2,0,STR_PAD_LEFT);
	}
	else{
		for($x=$a1;$x<=$a2;$x++){
			if($b1<1){
				$strList[] = $prefix.str_pad($i,2,0,STR_PAD_LEFT).'-'.str_pad($x,2,0,STR_PAD_LEFT);
			}
			else{
				for($y=$b1;$y<=$b2;$y++){
					if($c1<1){
						$strList[] = $prefix.str_pad($i,2,0,STR_PAD_LEFT).'-'.str_pad($x,2,0,STR_PAD_LEFT).'-'.str_pad($y,2,0,STR_PAD_LEFT);
					}
					else{
						for($z=$c1;$z<=$c2;$z++){
						$strList[] = $prefix.str_pad($i,2,0,STR_PAD_LEFT).'-'.str_pad($x,2,0,STR_PAD_LEFT).'-'.str_pad($y,2,0,STR_PAD_LEFT).'-'.str_pad($z,2,0,STR_PAD_LEFT);
			}
					}
				}
			}
		}
	}
}

if($lineNum){ 
	$strList = array_chunk($strList,$lineNum,true); 
}

foreach($strList as $m){
	$str .= implode(',',$m).$chl;
}

// for($i=$m1;$i<=$m2;$i++){
// 	if($a1<1){
// 		$str .= $prefix.str_pad($i,2,0,STR_PAD_LEFT).$chl;
// 	}
// 	else{
// 		for($x=$a1;$x<=$a2;$x++){
// 			if($b1<1){
// 				$str .= $prefix.str_pad($i,2,0,STR_PAD_LEFT).'-'.str_pad($x,2,0,STR_PAD_LEFT).$chl;
// 			}
// 			else{
// 				for($y=$b1;$y<=$b2;$y++){
// 					if($c1<1){
// 						$str .= $prefix.str_pad($i,2,0,STR_PAD_LEFT).'-'.str_pad($x,2,0,STR_PAD_LEFT).'-'.str_pad($y,2,0,STR_PAD_LEFT).$chl;
// 					}
// 					else{
// 						for($z=$c1;$z<=$c2;$z++){
// 						$str .= $prefix.str_pad($i,2,0,STR_PAD_LEFT).'-'.str_pad($x,2,0,STR_PAD_LEFT).'-'.str_pad($y,2,0,STR_PAD_LEFT).'-'.str_pad($z,2,0,STR_PAD_LEFT).$chl;
// 			}
// 					}
// 				}
// 			}
// 		}
// 	}
// }
//echo $str;
//导出txt
	if($str && $str !== 'ERROR' && $download) { 
		$filename = $prefix.str_pad($m1,2,0,STR_PAD_LEFT).'~'.str_pad($m2,2,0,STR_PAD_LEFT).'.txt';
		Header( "Content-type: application/octet-stream");
		Header( "Accept-Ranges: bytes");
		header( "Content-Disposition: attachment; filename=".$filename);
		header( "Expires: 0");
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header( "Pragma: public");
		echo $str;
		
		exit();
	}
?>
<html>
<head>
	<title>鲜世纪－仓库货位号生成导出</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<style>
	html{
		padding:2rem; 
		font-family:sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;
		font-size: 1rem;
	}
	.secForm{
		border: 1px #666 dashed;
		padding: 1rem;
	}
	.secComp{
		margin: 0.3rem;
		margin-right: 0.5rem;
		float: left;
		width: 8rem;
	}
	.preFix{
		width: 5rem;
	}
	.downLoad{
		font-size:0.9rem;
		border:1px #999 dashed;
		padding:0.1rem;
	}
	.secComp input{
		width: 1.8rem;
		font-size:1rem;
	}
	
	.secBtn{
		padding:0.3rem;
		font-size:0.8rem;
		width: 3rem;
	}
	
	.msg{
		font-style: italic;
		border: 3px #cc0000 dashed;
		background-color: #ffff00; 
		padding: 0.3rem;
		margin-top:0.3rem;
	}
	
	.rlt{
		border: 1px #333 dashed;
		padding: 0.3rem;
		background-color: #ddd; 
		margin-top:0.5rem;
	}
	</style>
</head>
<body>
	货位号生成导出
	<form class="secForm" action="#" method="post">
		<div class="secComp preFix">
			前缀<input name="prefix" type="text" maxlength=1 value="<?php echo $prefix; ?>" />
		</div>
		<div class="secComp">
			一段<input name="m1" type="text" maxlength=2 value="<?php echo $m1; ?>" /> ~ <input name="m2" type="text" maxlength=2 value="<?php echo $m2; ?>" />
		</div>
		<div class="secComp">
			二段<input name="a1" type="text" maxlength=2 value="<?php echo $a1; ?>" /> ~ <input name="a2" type="text" maxlength=2 value="<?php echo $a2; ?>" />
		</div>
		<div class="secComp">
			三段<input name="b1" type="text" maxlength=2 value="<?php echo $b1; ?>" /> ~ <input name="b2" type="text" maxlength=2 value="<?php echo $b2; ?>" />
		</div>
		<div class="secComp">
			四段<input name="c1" type="text" maxlength=2 value="<?php echo $c1; ?>" /> ~ <input name="c2" type="text" maxlength=2 value="<?php echo $c2; ?>" />
		</div>
		
		<div class="secComp downLoad">
			每行 <input name="lineNum" type="text" maxlength=2 value="<?php echo $lineNum; ?>" /> 个
		</div>
		<div class="secComp downLoad"><input class="secBtn" name="download" type="checkbox" value="1" />直接下载</div>
		<input class="secBtn" type="submit" value="生成">
	</form>
	
	<hr />
	例：依次输入A,2~3,1~2,1~2,0~0，得到结果为，选择“直接下载”可下载为TXT文件：<br />
	<div style="margin: 0.5rem">
	A02-01-01<br />
	A02-01-02<br />
	A02-02-01<br />
	A02-02-02<br />
	A03-01-01<br />
	A03-01-02<br />
	A03-02-01<br />
	A03-02-02
	</div>
	0值不生成段位数，首位前缀可为字母，仅1位，其他段位必须是数字，限两位数，段位中数字区间后一位要大于前一位。
	
	<?php if($str == 'ERROR'){ ?>
		<div class="msg"><?php echo $message; ?></div>
	<?php } ?>
	
	<?php if($str && !$download){ ?>
		<div class="rlt">生成结果:<hr /><br /><?php echo $str; ?></div>
	<?php } ?>
</body>
</html>