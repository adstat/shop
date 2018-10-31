<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/6
 * Time: 16:50
 */
if (!empty($_GET['download'])) {
    $fileNames = explode('@',$_GET['filenames']); //得到文件名
} else {
    $fileNames = $_GET['filenames']; //得到文件名
}
//var_dump($fileNames);

$fileName = array_shift($fileNames);
$url = "download.php?download=1&fileNames=".join('',$fileNames);
var_dump($url);

//header( "Content-Disposition:  attachment;  filename=".$fileName.".txt"); //告诉浏览器通过附件形式来处理文件
//header('Content-Length: ' . filesize($fileName)); //下载文件大小
//readfile($fileName);  //读取文件内容
header("location:$url");
?>