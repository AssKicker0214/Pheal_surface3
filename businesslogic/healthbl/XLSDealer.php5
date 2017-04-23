<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 2015/12/5
 * Time: 20:46
 */
include_once $_SERVER['DOCUMENT_ROOT'].'/PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/PHPExcel_1.8.0/Classes/PHPExcel/Reader/Excel2007.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/PHPExcel_1.8.0/Classes/PHPExcel.php';
include_once $_SERVER["DOCUMENT_ROOT"]."/businesslogic/healthbl/HealthData.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/DataManager/HealthDataManager.php5";

function loadXslData($filePath){
    $userid=$_COOKIE['userid'];

    $mng = new HealthDataManager();
    $PHPReader = new PHPExcel_Reader_Excel2007();
    if(!$PHPReader->canRead($filePath)){
        $PHPReader = new PHPExcel_Reader_Excel5();
        if(!$PHPReader->canRead($filePath)){
            echo 'no Excel';
            return 0;
        }
    }

    $PHPExcel = $PHPReader->load($filePath);


    //HealthData//////////////////////////////////////////////////////////////
    $currentSheet = $PHPExcel->getSheet(0);  /**取得一共有多少列*/
    $allColumn = $currentSheet->getHighestColumn();     /**取得一共有多少行*/
    $allRow = $currentSheet->getHighestRow();

    for( $currentRow = 1 ; $currentRow <= $allRow ; $currentRow++){

        $flag = 0;
        $row = [$userid];
        $dates = array();
        $currentColumn='A';
        for($i=0; $i<4; $i++){
            $address = $currentColumn.$currentRow;


            $string = $currentSheet->getCell($address)->getValue();
            array_push($row,$string);
            if($i == 0){
                $dates[] = $string;
            }

            $currentColumn++;
            $flag++;
        }
        $invalid = false;
        foreach($row as $col){
            if($col==''){
                $invalid = true;
            }
        }
        if($invalid){
            break;
        }
        $mng->insertInfoHealthData($userid, $dates, $row);
//        print_r($col);
    }
////////////////////////////////////////////////////////////////////////////

    //jog///////////////////////////////////////////////////////////////////
    $currentSheet = $PHPExcel->getSheet(1);  /**取得一共有多少列*/
    $allColumn = $currentSheet->getHighestColumn();     /**取得一共有多少行*/
    $allRow = $currentSheet->getHighestRow();

    $rows =array();
    $dates = array();
    for( $currentRow = 1 ; $currentRow <= $allRow ; $currentRow++){

        $flag = 0;
        $row = [$userid];
        $currentColumn='A';
        for($i=0; $i<4; $i++){
            $address = $currentColumn.$currentRow;


            $string = $currentSheet->getCell($address)->getValue();
            array_push($row,$string);
            if($i == 0){
                $dates[] = $string;
            }

            $currentColumn++;
            $flag++;
        }
        $invalid = false;
        foreach($row as $col){
            if($col==''){
                $invalid = true;
            }
        }
        if($invalid){
            break;
        }
        $rows[]=$row;
//        print_r($col);
    }
    $mng->insertIntoJog($userid, $dates, $rows);
    ////////////////////////////////////////////////////////////////////////

    //walk///////////////////////////////////////////////////////////////////
    $currentSheet = $PHPExcel->getSheet(2);  /**取得一共有多少列*/
    $allColumn = $currentSheet->getHighestColumn();     /**取得一共有多少行*/
    $allRow = $currentSheet->getHighestRow();
    $rows = array();
    for( $currentRow = 1 ; $currentRow <= $allRow ; $currentRow++){

        $flag = 0;
        $row = [$userid];
        $dates = array();
        $currentColumn='A';
        for($i=0; $i<4; $i++){
            $address = $currentColumn.$currentRow;


            $string = $currentSheet->getCell($address)->getValue();
            array_push($row,$string);
            if($i == 0){
                $dates[] = $string;
            }

            $currentColumn++;
            $flag++;
        }
        $invalid = false;
        foreach($row as $col){
            if($col==''){
                $invalid = true;
            }
        }
        if($invalid){
            break;
        }
        $rows[] = $row;
    }

    $mng->insertIntoWalk($userid, $dates, $rows);
    ////////////////////////////////////////////////////////////////////////

    //STAGE///////////////////////////////////////////////////////////////////
    $currentSheet = $PHPExcel->getSheet(3);  /**取得一共有多少列*/
    $allColumn = $currentSheet->getHighestColumn();     /**取得一共有多少行*/
    $allRow = $currentSheet->getHighestRow();
    $rows = array();
    for( $currentRow = 1 ; $currentRow <= $allRow ; $currentRow++){

        $flag = 0;
        $row = [$userid];
        $dates = array();
        $currentColumn='A';
        for($i=0; $i<3; $i++){
            $address = $currentColumn.$currentRow;


            $string = $currentSheet->getCell($address)->getValue();
            array_push($row,$string);
            if($i == 0){
                $dates[] = $string;
            }

            $currentColumn++;
            $flag++;
        }

        $invalid = false;
        foreach($row as $col){
            if($col==''){
                $invalid = true;
            }
        }
        if($invalid){
            break;
        }

        $rows[] = $row;
    }
    $mng->insertIntoSleepStage($userid, $dates, $rows);
    ////////////////////////////////////////////////////////////////////////

    //pulse////////////////////////////////////////////////////
    $currentSheet = $PHPExcel->getSheet(4);  /**取得一共有多少列*/
    $allColumn = $currentSheet->getHighestColumn();     /**取得一共有多少行*/
    $allRow = $currentSheet->getHighestRow();
    $rows = array();
    for( $currentRow = 1 ; $currentRow <= $allRow ; $currentRow++){

        $flag = 0;
        $row = [$userid];
        $dates = array();
        $currentColumn='A';
        for($i=0; $i<3; $i++){
            $address = $currentColumn.$currentRow;


            $string = $currentSheet->getCell($address)->getValue();
            array_push($row,$string);
            if($i == 0){
                $dates[] = $string;
            }

            $currentColumn++;
            $flag++;
        }

        $invalid = false;
        foreach($row as $col){
            if($col==''){
                $invalid = true;
            }
        }
        if($invalid){
            break;
        }

        $rows[] = $row;
    }
    $mng->insertIntoPulse($userid, $dates, $rows);
    //////////////////////////////////////////////////////

    //pulse////////////////////////////////////////////////////
    $currentSheet = $PHPExcel->getSheet(4);  /**取得一共有多少列*/
    $allColumn = $currentSheet->getHighestColumn();     /**取得一共有多少行*/
    $allRow = $currentSheet->getHighestRow();
    $rows = array();
    for( $currentRow = 1 ; $currentRow <= $allRow ; $currentRow++){

        $flag = 0;
        $row = [$userid];
        $dates = array();
        $currentColumn='A';
        for($i=0; $i<4; $i++){
            $address = $currentColumn.$currentRow;


            $string = $currentSheet->getCell($address)->getValue();
            array_push($row,$string);
            if($i == 0){
                $dates[] = $string;
            }

            $currentColumn++;
            $flag++;
        }

        $invalid = false;
        foreach($row as $col){
            if($col==''){
                $invalid = true;
            }
        }
        if($invalid){
            break;
        }

        $rows[] = $row;
    }
    $mng->insertIntoPressure($userid, $dates, $rows);
    //////////////////////////////////////////////////////
}
