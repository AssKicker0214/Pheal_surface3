<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 2015/11/17
 * Time: 9:09
 */
include_once "PDO.php";
include_once $_SERVER['DOCUMENT_ROOT'].'/businesslogic/healthbl/HealthData.php';
class HealthDataManager{
    private $pdo = null;
    public function __construct(){
        $this->pdo = PDOManager::getPDO();
    }

    //先删除原来的记录（用户，日期）
    public function insert($datas){

        $this->pdo->beginTransaction();


        $tables = array("HealthData", "Walk", "Jog", "SleepStage", "Pressure", "Pulse");
        foreach($tables as $table){
            $sql = "delete from ".$table." where UID=:uid AND DAY=:date";
            $stmt = $this->pdo->prepare($sql);
            foreach($datas as $data)
            $stmt->execute(array(":uid"=>$data->userid, ":date"=>$data->date));
        }

        $sql1 = "Insert into HealthData (UID, DAY, SLEEPSTART, SLEEPEND, STEPS) VALUES (?, ?, ?, ?, ?)";
        $stmt1 = $this->pdo->prepare($sql1);
        foreach($datas as $data)
        $stmt1->execute(array($data->userid, $data->date, $data->getSleepStart(), $data->getSleepEnd(), $data->steps));

        $sql3 = "Insert into Walk (UID, DAY, STARTTIME, DURATION, DISTANCE) VALUES (?,?,?,?,?)";
        $stmt3 = $this->pdo->prepare($sql3);
        foreach($datas as $data) {
            $walks = $data->walks;
            foreach ($walks as $walk) {
                $stmt3->execute(array($data->userid, $data->date, $walk->startTime, $walk->duration, $walk->distance));
            }
        }

        $sql4 = "Insert into Jog (UID, DAY, STARTTIME, DURATION, DISTANCE) VALUES (?,?,?,?,?)";
        $stmt4 = $this->pdo->prepare($sql4);
        foreach($datas as $data) {
            $jogs = $data->jogs;
            foreach ($jogs as $jog) {
                $stmt4->execute(array($data->userid, $data->date, $jog->startTime, $jog->duration, $jog->distance));
            }
        }

        $sql2 = "Insert into SleepStage (UID, DAY, STAGE, STARTTIME) VALUES(?,?,?,?)";
        $stmt2 = $this->pdo->prepare($sql2);
        foreach($datas as $data){
            $stageTypes = $data->sleep->stageTypes;
            for($i=0;$i<count($stageTypes);$i++){
                $stageType = $data->sleep->stageTypes[$i];
                $stageTime = $data->sleep->stageTimes[$i];
                $stmt2->execute(array($data->userid, $data->date, $stageType, $stageTime));
            }
        }

        $sql5 = 'Insert into Pressure(UID, DAY, TIME, HIGH, LOW) VALUES (?,?,?,?,?)';
        $stmt5 = $this->pdo->prepare($sql5);
        foreach($datas as $data){
            foreach($data->pressures as $pressure){
                $stmt5->execute(array($data->userid, $data->date, $pressure['time'], $pressure['high'], $pressure['low']));
            }
        }


        $sql6 = 'Insert into Pulse(UID, DAY, TIME, RATE) VALUES (?,?,?,?)';
        $stmt6 = $this->pdo->prepare($sql6);
        foreach($datas as $data){

            foreach($data->pulses as $pulse){
                $stmt6->execute(array($data->userid, $data->date, $pulse['time'], $pulse['rate']));
            }
        }

        $this->pdo->commit();
    }

    public function getData($userid, $date){
        //$this->pdo->beginTransaction();

        $condition = " where UID='".$userid."' AND DAY='".$date."'";

        $sql1 = "select SLEEPSTART, SLEEPEND, STEPS from HealthData".$condition;
        //echo "<br/>",$sql1;
        $stmt1 = $this->pdo->prepare($sql1);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $sleep = new Sleep();
        $sleepStartTime = $result1['SLEEPSTART'];
        $sleepEndTime = $result1['SLEEPEND'];
        $sleep->startTime = $sleepStartTime;
        $sleep->endTime = $sleepEndTime;
        $steps = $result1['STEPS'];

        $sql2 = "select STARTTIME, DURATION, DISTANCE from Walk".$condition;
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute();
        $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $walks = array();
        foreach($result2 as $line){
//            $walk = new Walk();
//            $walk->distance = $line['DISTANCE'];
//            $walk->duration = $line['DURATION'];
//            $walk->startTime = $line['STARTTIME'];
            array_push($walks, $line);
        }

        $sql3 = "select STARTTIME, DURATION, DISTANCE from Jog".$condition;
        $stmt3 = $this->pdo->prepare($sql3);
        $stmt3->execute();
        $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        $jogs = array();
        foreach($result3 as $line){
//            $jog = new Jog();
//            $jog->distance = $line['DISTANCE'];
//            $jog->duration = $line['DURATION'];
//            $jog->startTime = $line['STARTTIME'];

            array_push($jogs, $line);
        }

        $sql4 = "select STAGE, STARTTIME from SleepStage".$condition;
        $stmt4 = $this->pdo->prepare($sql4);
        $stmt4->execute();
        $result4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
        $stageTypes = array();
        $stageTimes = array();
        foreach ($result4 as $line) {
            array_push($stageTimes, $line['STARTTIME']);
            array_push($stageTypes, $line['STAGE']);
        }
        $sleep->stageTypes = $stageTypes;
        $sleep->stageTimes = $stageTimes;

        $sql5 = 'select * from Pressure'.$condition;
        $stmt5 = $this->pdo->prepare($sql5);
        $stmt5->execute();
        $result5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);
        $pressures = array();
        foreach($result5 as $item){
            $pressure = array();
            $pressure['time'] = $item['TIME'];
            $pressure['high'] = $item['HIGH'];
            $pressure['low'] = $item['LOW'];
            array_push($pressures, $pressure);
        }

        $sql6 = 'select * from Pulse'.$condition;
        $stmt6 = $this->pdo->prepare($sql6);
        $stmt6->execute();
        $result6 = $stmt6->fetchAll(PDO::FETCH_ASSOC);
        $pulses = array();
        foreach($result6 as $item){
            $pulse = array();
            $pulse['time'] = $item['TIME'];
            $pulse['rate'] = $item['RATE'];
            array_push($pulses, $pulse);
        }

        $data = new HealthData();
        $data->sleep = $sleep;
        $data->jogs = $jogs;
        $data->walks = $walks;
        $data->userid = $userid;
        $data->date = $date;
        $data->steps = $steps;
        $data->pressures = $pressures;
        $data->pulses = $pulses;

        //$this->pdo->commit();
//print_r($data->pressures);
        return $data;
    }

    public function getAllSleepTimes($userid){
        $sql = 'select SLEEPSTART, SLEEPEND from HealthData where UID=:userid ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(":userid"=>$userid));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function rollBack(){
        $this->pdo->rollBack();
    }

    public function getPosition($userid, $date){
        $sql = 'select STEPS, UID from HealthData where DAY=?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array($date));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        $larger = 0;
        $step = 0;
        foreach($result as $item){
            if($userid == $item['UID']){
                $step = $item['STEPS'];
                break;
            }
        }

        foreach($result as $item){
            if($step < $item['STEPS']){
                $larger++;
            }
            $total++;
        }

        if($total == 0){
            return 0.0;
        }else{
            return ($larger+0.0)/$total;

        }
    }

    public function insertInfoHealthData($userid, $dates, $array){
        $this->pdo->beginTransaction();
        $sql0 = 'delete from HealthData where UID=? AND DAY=?';
        $stmt0 = $this->pdo->prepare($sql0);
        foreach($dates as $date){
            $stmt0->execute(array($userid, $date));
        }
        $sql = 'Insert Into HealthData(UID, DAY, SLEEPSTART, SLEEPEND, STEPS) VALUES (?,?,?,?,?)';
        $stmt1 = $this->pdo->prepare($sql);
        $stmt1->execute($array);
        $this->pdo->commit();

    }

    public function insertIntoJog($userid, $dates, $array){
        $this->pdo->beginTransaction();
        $sql0 = 'delete from Jog where UID=? AND DAY=?';
        $stmt0 = $this->pdo->prepare($sql0);
        foreach($dates as $date){
            $stmt0->execute(array($userid, $date));
        }
        $sql = 'Insert Into Jog(UID, DAY, STARTTIME, DURATION, DISTANCE) VALUES (?,?,?,?,?)';
        $stmt1 = $this->pdo->prepare($sql);
        foreach($array as $item){
            $stmt1->execute($item);
        }
        $this->pdo->commit();
    }

    public function insertIntoWalk($userid, $dates, $array){
        $this->pdo->beginTransaction();
        $sql0 = 'delete from Walk where UID=? AND DAY=?';
        $stmt0 = $this->pdo->prepare($sql0);
        foreach($dates as $date){
            $stmt0->execute(array($userid, $date));
        }

        $sql = 'Insert Into Walk(UID, DAY, STARTTIME, DURATION, DISTANCE) VALUES (?,?,?,?,?)';
        $stmt1 = $this->pdo->prepare($sql);
        foreach($array as $item){
            $stmt1->execute($item);
        }
        $this->pdo->commit();
    }

    public function insertIntoSleepStage($userid, $dates, $array){
        $this->pdo->beginTransaction();
        $sql0 = 'delete from SleepStage where UID=? AND DAY=?';
        $stmt0 = $this->pdo->prepare($sql0);
        foreach($dates as $date){
            $stmt0->execute(array($userid, $date));
        }

        $sql = 'Insert Into SleepStage(UID, DAY, STAGE, STARTTIME) VALUES (?,?,?,?)';
        $stmt1 = $this->pdo->prepare($sql);
//        print_r($array);
        foreach($array as $item){
            $stmt1->execute($item);
        }
        $this->pdo->commit();
    }

    public function insertIntoPulse($userid, $dates, $array){
        $this->pdo->beginTransaction();
        $sql0 = 'delete from Pulse where UID=? AND DAY=?';
        $stmt0 = $this->pdo->prepare($sql0);
        foreach($dates as $date){
            $stmt0->execute(array($userid, $date));
        }

        $sql = 'Insert Into Pulse(UID, DAY, TIME, RATE) VALUES (?,?,?,?)';
        $stmt1 = $this->pdo->prepare($sql);
//        print_r($array);
        foreach($array as $item){
            $stmt1->execute($item);
        }
        $this->pdo->commit();
    }

    public function insertIntoPressure($userid, $dates, $array){
        $this->pdo->beginTransaction();
        $sql0 = 'delete from Pressure where UID=? AND DAY=?';
        $stmt0 = $this->pdo->prepare($sql0);
        foreach($dates as $date){
            $stmt0->execute(array($userid, $date));
        }

        $sql = 'Insert Into Pressure(UID, DAY, TIME, HIGH, LOW) VALUES (?,?,?,?,?)';
        $stmt1 = $this->pdo->prepare($sql);
//        print_r($array);
        foreach($array as $item){
            $stmt1->execute($item);
        }
        $this->pdo->commit();
    }
}
