<?php
header("Content-Type:text/html;charset=utf-8");
$sourceID = $_GET['source'];
$currentID = $_COOKIE['userid'];
$fileUploadInvisible = ' style="visibility: hidden;" ';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="/bootstrap.min.css" rel="stylesheet">
    <link href="/presentation/PersonalIndex/health/HealthCSS.css" rel="stylesheet">
    <title></title>
</head>
<body>
<form <?php if($sourceID!=$currentID) echo $fileUploadInvisible;?> method="post" enctype="multipart/form-data"
                                               action="../../../businesslogic/healthbl/FileDealer.php5">
    <div class="input-group" id="file-selector">
        <span class="input-group-addon">选择数据文件</span>
        <input type="file" name="file" class="form-control">
        <span class="input-group-btn">
            <button class="btn btn-primary" type="submit">upload</button>
        </span>
    </div>
    &nbsp;&nbsp;
</form>

    <ul class="nav nav-pills">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">睡眠<b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li class=""><a href="#sleep-duration-block">时间</a></li>
                <li class=""><a href="#sleep-stage-block">分期</a></li>
                <li class=""><a href="#sleep-weight-block">比例</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">运动<b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li class=""><a href="#exercise-block">总览</a></li>
                <li class=""><a href="#walk-block">走路</a></li>
                <li class=""><a href="#jog-block">慢跑</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">心率&血压<b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li class=""><a href="#pulse-block">心率</a></li>
                <li class=""><a href="#pressure-block">血压</a></li>
            </ul>
        </li>
        <li>
            <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span>
                <input id="datepicker" class="form-control" type="text" value="2015-12-01" onClick="WdatePicker()">
                <span class="input-group-btn">
                    <button class="btn btn-primary" value="<?php echo $sourceID;?>" onclick="getData(this.value)">确定</button>
                </span>
            </div>
            &nbsp;&nbsp;
        </li>
    </ul>




<div>
<!--    <nav id="content-nav" class="navbar navbar-default">-->

<!--    </nav>-->
    <div id="data-presentor" data-spy="scroll" data-target="#content-nav" data-offset="5" class="scrollspy-example">
        <!--        本次睡眠时间，用户平均睡眠时间，正常人睡眠时间-->
        <div class="row area dark-area" id="sleep-duration-block">
            <div class="col-lg-4">
                <canvas id="sleep-avg-bar" width="300" height="250">

                </canvas>

            </div>
            <div class="col-lg-8">
                <h1>睡眠时间 </h1>
                <ul>
                    <li><h4>本次睡眠时间为<span class="figure-em" id="this-sleep-figure"></span> 小时</h4> </li>
                    <li id="normal-avg-sleep"></li>
                    <li id="your-avg-sleep"></li>
                </ul>
            </div>
        </div>

        <!--各阶段睡眠详细-->
        <div class="row area light-area" id="sleep-stage-block">
            <div class="col-lg-6">
                <h1>睡眠分期
                    <div id="sleep-stage-conclusion" style="visibility: visible;">
                    </div>
                </h1>
                <a class="btn btn-primary btn-sm" role="button" data-toggle="collapse" href="#stageTable" onclick="sleepDetailRecordClicked()">
                    详细记录
                </a>
                <div class="collapse" id="stageTable">
                    <table style="font-size: 12px" class="table table-hover table-condensed" id="sleep-stage-table">
                        <thead>
                        <th>序号</th>
                        <th>睡眠类型</th>
                        <th>开始时间</th>
                        <th>持续时间</th>
                        </thead>
                        <tbody id="sleep-stage-items">

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="col-lg-6">
                <canvas id="sleep-stage-radar" width="450" height="450">

                </canvas>
            </div>
        </div>

        <div class="row area dark-area" id="sleep-weight-block">
            <div class="col-lg-4">
                <canvas id="sleep-stage-dougnut" width="200" height="200">

                </canvas>
                <h2></h2>
            </div>
            <div class="col-lg-8">
                <h1>睡眠比例</h1>
                <i>第四期和快速动眼期为睡眠质量决定因素</i>
                <ul>
                    <li><h4>第一期比重<span class="light-blue figure-em" id="first-stage"></span> </h4></li>
                    <li><h4>第二期比重<span class="blue figure-em" id="second-stage"></span> </h4></li>
                    <li><h4>第三期比重<span class="dark-blue figure-em" id="third-stage"></span> </h4></li>
                    <li><h4>第四期比重<span class="red figure-em" id="forth-stage"></span> </h4></li>
                    <li><h4>快速动眼期比重<span class="dark-red figure-em" id="rem-stage"></span> </h4></li>

                </ul>
            </div>
        </div>

        <div class="row area light-area" id="exercise-block">
            <div class="col-lg-8">
                <h2>运动&nbsp;</h2>
                <ul>
                    <li>总步数（走路+慢跑）&nbsp;<span class="figure-em" id="total-step">0</span>&nbsp;步 </li>
                    <li>击败&nbsp;<span class="figure-em blue" id="step-beat">0</span>%&nbsp;的用户</li>
                    <li>仍有&nbsp;<span class="figure-em red" id="step-lose">0</span>%&nbsp;的用户比你多哦</li>
                </ul>

            </div>
            <div class="col-lg-4">
                <canvas height="300" width="300" id="step-pie"></canvas>
            </div>
        </div>

        <div class="row area dark-area" id="walk-block">
            <div class="col-lg-4">
                <canvas height="300" width="300" id="walk-bar"></canvas>
            </div>
            <div class="col-lg-8">
                <h2>走路&nbsp;</h2>
                <q>(只记录时间大于10分钟的连续情况)</q>
                <ul>
                    <li>总距离&nbsp;<span class="figure-em blue" id="walk-distance">0</span>&nbsp;百米 </li>
                    <li>总时间&nbsp;<span class="figure-em orange" id="walk-time">0</span>&nbsp;分钟</li>
                </ul>
                <a data-toggle="collapse" role="button" class="btn btn-primary" href="#walk-table" ">详细记录</a>
                <table class="collapse table table-hover table-condensed" id="walk-table">
                    <thead><th>序号</th><th>开始时间</th><th>持续时间</th><th>距离</th></thead>
                    <tbody id="walk-table-items">

                    </tbody>
                </table>
            </div>
        </div>

        <div class="row area light-area" id="jog-block">
            <div class="col-lg-8">
                <h2>慢跑&nbsp; </h2>
                <q>(只记录时间大于10分钟的连续情况)</q>
                <ul>
                    <li>总距离&nbsp;<span class="figure-em blue" id="jog-distance">0</span>&nbsp;百米 </li>
                    <li>总时间&nbsp;<span class="figure-em orange" id="jog-duration">0</span>&nbsp;分钟</li>
                </ul>
                <a data-toggle="collapse" role="button" class="btn btn-primary" href="#jog-table" ">详细记录</a>
                <table class="collapse table table-hover table-condensed" id="jog-table">
                    <thead><th>序号</th><th>开始时间</th><th>持续时间</th><th>距离</th></thead>
                    <tbody id="jog-table-items">

                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <canvas height="300" width="800" id="jog-bar"></canvas>
            </div>
        </div>

        <div class="row area dark-area" id="pulse-block">
            <div class="col-lg-4">
                <canvas width="300" height="300" id="pulse-bar"></canvas>
            </div>
            <div class="col-lg-8">
                <h2>心率</h2>
                <q>(平静状态下自测)</q>
                <h4>正常值<span class="blue figure-em">&nbsp;55&nbsp;</span>~<span class="red figure-em">&nbsp;100</span> </h4>
                <a data-toggle="collapse" role="button" class="btn btn-primary" href="#pulse-table">详细记录</a>
                <table class="collapse table table-hover " id="pulse-table">
                    <thead><th>序号</th><th>自测时间</th><th>心率(次/每分钟)</th><th>备注</th></thead>
                    <tbody id="pulse-table-items">

                    </tbody>
                </table>
            </div>
        </div>

        <div class="row area light-area" id="pressure-block">
            <div class="col-lg-8">
                <h2>血压</h2>
                <q>(平静状态下自测)</q>
                <ul>
                    <li>
                        <h4>低压正常值<span class="blue figure-em">&nbsp;60&nbsp;</span>~<span class="red figure-em">&nbsp;90</span> </h4>
                    </li>
                    <li>
                        <h4>高压正常值<span class="blue figure-em">&nbsp;90&nbsp;</span>~<span class="red figure-em">&nbsp;140</span> </h4>
                    </li>
                </ul>
                <a data-toggle="collapse" role="button" class="btn btn-primary" href="#pressure-table">详细记录</a>
                <table class="collapse table table-hover " id="pressure-table">
                    <thead><th>序号</th><th>自测时间</th><th>高压</th><th>低压</th><th>备注</th></thead>
                    <tbody id="pressure-table-items">

                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <canvas width="300" height="300" id="pressure-bar"></canvas>
            </div>
        </div>

    </div>
    <hr>
</div>
<script src="h../..//jquery-2.1.4.min.js"></script>
<script src="http://localhost:63342/presentation/PersonalIndex/health/Chart.js"></script>
<script src="http://localhost:63342/presentation/PersonalIndex/calendar/WDatePicker.js"></script>
<script src="http://localhost:63342/bootstrap.min.js"></script>
<script src="http://localhost:63342/presentation/PersonalIndex/health/HealthJS.js"></script>
</body>
</html>