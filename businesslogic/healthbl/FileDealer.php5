<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 2015/12/5
 * Time: 20:41
 */
include_once 'XLSDealer.php5';
include_once 'XMLDealer.php5';
$file = $_FILES['file'];
$type = explode('.',$file['name'])[1];
echo $type;

    /*设置上传路径*/
if($type == 'xml'){

    $storagePath = $_SERVER['DOCUMENT_ROOT'].'/xml/'.$_COOKIE['userid'].'_'.date("Y-m-d h-i-s").'_health.xml';
    if(is_uploaded_file($file['tmp_name'])){
        if( move_uploaded_file($file['tmp_name'], $storagePath)){
            echo "上传成功";
        }
    }
    loadXMLData($storagePath);
}else if($type == 'xlsx'){
    $storagePath = $_SERVER["DOCUMENT_ROOT"].'/xls/'.$_COOKIE["userid"].'_'.date("Y-m-d h-i-s").'_health.xlsx';
    if(is_uploaded_file($file['tmp_name'])){
        if( move_uploaded_file($file['tmp_name'], $storagePath)){
            echo "上传成功";
        }
    }

    loadXslData($storagePath);
}else{
    echo '不可识别的文件，请上传xml或xslx';
}

//print_r($result);