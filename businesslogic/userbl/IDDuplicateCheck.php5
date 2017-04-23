<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 2015/12/6
 * Time: 11:45
 */
include_once $_SERVER['DOCUMENT_ROOT'].'/DataManager/IDSearcher.php';
$id = $_POST['id'];
$result = getPersonalInfo($id);
if(count($result) >0){
    echo true;
}else{
    echo false;
}
