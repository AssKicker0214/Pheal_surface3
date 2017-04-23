<?php

include($_SERVER['DOCUMENT_ROOT'].'/DataManager/HealthDataManager.php5');
$date = date('2015-12-01');
if(isset($_GET['date'])){
    $date = $_GET['date'];
}
$source = $_GET['source'];
$dataObj = getDataObj($source, $date);
$avgSleepTime = getAvgSleepTime($source);
$stepPosition = getStepPosition($source, $date);
$dataJso = wrapData($dataObj, $avgSleepTime, $stepPosition);
echo $dataJso;

//echo getAvgSleepTime();

function getDataObj($source, $date){

    $dataMng = new HealthDataManager();
    $dataObj = $dataMng->getData($source, $date);
    return $dataObj;
}
function wrapData($obj, $avgSleepTime, $stepPosition){
    $data = array();
    $data['walks'] = $obj->walks;
    $data['jogs'] = $obj->jogs;
    $data['sleep'] = array('startTime'=>$obj->sleep->startTime, 'endTime'=>$obj->sleep->endTime, 'avgTime'=>$avgSleepTime);
    $data['sleepStageTimes'] = $obj->sleep->stageTimes;
    $data['sleepStageTypes'] = $obj->sleep->stageTypes;
    $data['steps'] = $obj->steps;
    $data['stepPosition'] = $stepPosition;
    $data['pressures'] = $obj->pressures;
    $data['pulses'] = $obj->pulses;
//    print_r($data);
    return json_encode($data);
}

function getAvgSleepTime($source){
    $dataMng = new HealthDataManager();
    $result = $dataMng->getAllSleepTimes($source);
    $total = 0.0;
    $amount = 0;
    foreach($result as $pair){
        $total += countLast($pair['SLEEPSTART'], $pair['SLEEPEND']);
        $amount++;
    }
    if($amount==0){
        return 0;
    }
    return ($total)/($amount);
}

function countLast($start, $end){
    $startH = floatval(explode(':', $start)[0]);
    $startM = floatval(explode(':', $start)[1]);
    $endH = floatval(explode(':', $end)[0]);
    $endM = floatval(explode(':', $end)[1]);
    $last = 0.0;
    if($startH > $endH){//隔天了
        $last = 60*(24-$startH)-$startM + 60*($endH)+$endM;
    }else if($startH < $endH){
        $last = ($endH - $startH) *60-$startM+$endM;
    }else if($startH == $endH){
        $last = $endM - $startM;
    }

    return ($last)/60;
}

function getStepPosition($userid, $date){
    $mng = new HealthDataManager();
    return $mng->getPosition($userid, $date);
}