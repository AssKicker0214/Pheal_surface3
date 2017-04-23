function getData(source){
    var date = document.getElementById('datepicker').value;
    showData();

    var xhr = $.ajax({url:"../../../businesslogic/healthbl/HealthDataFetcher.php5/?source="+source+"&date="+date,success:update, async: true,type:'get'});

}

function update(data, textStatus){
    //document.write('****'+data+'****');
    var jso = eval("("+data+")");
    var sleepTime = getTimeLast(jso['sleep']['startTime'], jso['sleep']['endTime']);

    updateAvg(sleepTime/60.0, "8.6", jso['sleep']['avgTime']);
    updateStage(jso['sleepStageTimes'], jso['sleepStageTypes'], jso['sleep']['endTime']);
    updateWalk(jso['walks']);
    updateJog(jso['jogs']);
    updateStep(jso['steps'], jso['stepPosition']);
    updatePulse(jso['pulses']);
    updatePressure(jso['pressures']);

}

function updateGeneral(){

}

function updateAvg(thisTime, normalAvg, yourAvg){
    var barChartData = {
        labels : ["本次","正常人平均","你的平均"],
        datasets : [
            {
                fillColor : "rgba(220,220,220,0.5)",
                strokeColor : "rgba(220,220,220,1)",
                data : [thisTime, normalAvg, yourAvg,0]
            }
        ]

    };

    var myLine = new Chart(document.getElementById("sleep-avg-bar").getContext("2d")).Bar(barChartData);
    document.getElementById('sleep-avg-bar').setAttribute('width', '300');
    document.getElementById('sleep-avg-bar').setAttribute('height', '250');

    var withNornaml = '等于';
    if(thisTime>normalAvg){
        withNornaml = '高出';
    }else if(thisTime<normalAvg){
        withNornaml = '低于';
    }

    var withyour = '等于';
    if(thisTime>yourAvg){
        withyour = '高出';
    }else if(thisTime<yourAvg){
        withyour = '低于';
    }

    document.getElementById('this-sleep-figure').innerHTML ='&nbsp;'+ Math.round(thisTime*100)/100;
    document.getElementById('normal-avg-sleep').innerHTML = '<h4>'+withNornaml+'正常人平均睡眠时间<span class="figure-em">&nbsp;'+Math.round(Math.abs(thisTime-normalAvg)*100/normalAvg)+'%&nbsp;</span></h6>';
    document.getElementById('your-avg-sleep').innerHTML =
        '<h4>'+withyour+'你的平均睡眠时间<span class="figure-em">'+Math.round(Math.abs(thisTime-yourAvg)*100/yourAvg)+'%&nbsp;</span></h6>';
}

function updateStage(stageTimes, stageTypes, endTime){
    var times = getStageLastTimes(stageTimes, endTime);
    var types = new Array();
    var stageCount = stageTimes.length;

    for(var i=0;i<stageCount;i++){
        if(stageTypes[i]=='first'){
            types[i]='入睡期';
        }else if(stageTypes[i]=='second'){
            types[i]='浅睡期';
        }else if(stageTypes[i]=='third'){
            types[i]='熟睡期';
        }else if(stageTypes[i]=='forth'){
            types[i]='深睡期';
        }else if(stageTypes[i]=='rem'){
            types[i]='快速动眼期';
        }
    }
    var html = "";
    for(i=0;i<stageCount;i++){
        html += "<tr><td>"+(i+1)+"</td><td>"+types[i]+"</td><td>"+stageTimes[i]+"</td><td>"+times[i]+"</td></tr>";
    }
    document.getElementById('sleep-stage-items').innerHTML = html;

    var datasets = new Array();
    var cricles = divideCircle(stageTypes, stageTimes, endTime);
    for(var i=0;i<cricles.length;i++){
        var circle = {
            fillColor : "rgba(220,220,220,0.0)",
            strokeColor : "rgba(220,220,220,0.5)",
            pointColor : "rgba(220,220,220,1.0)",
            pointStrokeColor : "rgba(220,220,220,0.0)",
            data : cricles[i]
        };
        datasets.push(circle);
    }

    var lineChartData = {
        labels : ["浅睡期","深睡期","熟睡期","深睡期","浅睡期","快速动眼期"],
        datasets : datasets

    };

    var myRadar = new Chart(document.getElementById("sleep-stage-radar").getContext("2d")).Radar(lineChartData,{scaleShowLabels : true, pointLabelFontSize : 10});

    document.getElementById('sleep-stage-radar').setAttribute('width', '450');
    document.getElementById('sleep-stage-radar').setAttribute('height', '450');

    //conclusion
    var sleepStartTime = stageTimes[0];
    var startComment = '';
    var startDif = parseInt(sleepStartTime.split(':')[0]) - 23;
    if(startDif >= -23 && startDif<-17){
        startComment = ',睡觉时间过晚';
    }

    var sleepEndTime = endTime;
    var interuptCount = countSleepInterupt(stageTypes);
    var stageConclusionHTML =
        '<ul>' +
        '<li><h4><span class="figure-em"> '+sleepStartTime+'</span> 开始睡觉'+startComment+'</h4></li>' +
        '<li><h4><span class="figure-em"> '+sleepEndTime +'</span> 醒来</h4></li>'+
        '<li><h4>睡眠被打断 <span class="figure-em"> '+interuptCount+'</span> 次</h4>'+
        '</ul>';

    document.getElementById('sleep-stage-conclusion').innerHTML=stageConclusionHTML;

    var sumFirst = 0;
    var sumSecond = 0;
    var sumThird = 0;
    var sumForth = 0;
    var sumREM = 0;
    for(var i=0;i<stageTypes.length;i++){
        if(stageTypes[i] == 'first'){
            sumFirst += times[i];
        }else if(stageTypes[i] == 'second'){
            sumSecond += times[i];
        }else if(stageTypes[i] == 'third'){
            sumThird += times[i];
        }else if(stageTypes[i] == 'forth'){
            sumForth += times[i];
        }else if(stageTypes[i] == 'rem'){
            sumREM += times[i];
        }
    }
    var sum = sumFirst+sumSecond+sumThird+sumForth+sumREM;
    var doughnutData = [
        {
            value: sumFirst,
            color:"#0099CC"
        },
        {
            value : sumSecond,
            color : "#3E8FF6"
        },
        {
            value : sumThird,
            color : "#005580"
        },
        {
            value : sumForth,
            color : "#DE120B"
        },
        {
            value : sumREM,
            color : "#761c19"
        }

    ];

    var myDoughnut = new Chart(document.getElementById("sleep-stage-dougnut").getContext("2d")).Doughnut(doughnutData);
    document.getElementById('sleep-stage-dougnut').setAttribute('width', '200');
    document.getElementById('sleep-stage-dougnut').setAttribute('height', '200');

    document.getElementById('first-stage').innerHTML = "&nbsp;"+(Math.round((sumFirst*10000)/sum)/100.0) +"%&nbsp;";
    document.getElementById('second-stage').innerHTML = "&nbsp;"+(Math.round((sumSecond*10000)/sum)/100.0) +"%&nbsp;";
    document.getElementById('third-stage').innerHTML = "&nbsp;"+(Math.round((sumThird*10000)/sum)/100.0) +"%&nbsp;";
    document.getElementById('forth-stage').innerHTML = "&nbsp;"+(Math.round((sumForth*10000)/sum)/100.0) +"%&nbsp;";
    document.getElementById('rem-stage').innerHTML = "&nbsp;"+(Math.round((sumREM*10000)/sum)/100.0) +"%&nbsp;";
}

function updateStageBalance(){

}

function updateHeartBlood(){
    var timeLabels = new Array();
    var heartRate = new Array();
    var bloodPressure = new Array();
    for(i=0;i<25;i++){
        timeLabels[i]=i+":00";
        heartRate[i] = 100;
        bloodPressure[i] = 24/43;
    }
    var lineChartData = {
        labels : timeLabels,
        datasets : [
            {
                fillColor : "rgba(220,220,220,0.5)",
                strokeColor : "rgba(220,220,220,1)",
                pointColor : "rgba(220,220,220,1)",
                pointStrokeColor : "#fff",
                data : heartRate
            },
            {
                fillColor : "rgba(151,187,205,0.5)",
                strokeColor : "rgba(151,187,205,1)",
                pointColor : "rgba(151,187,205,1)",
                pointStrokeColor : "#fff",
                data : bloodPressure
            }
        ]

    };

    var myLine = new Chart(document.getElementById("heart-blood-line").getContext("2d")).Line(lineChartData);

}

function getStageLastTimes(stageTimes, endTime){
    var stageCount = stageTimes.length;
    var times = new Array();
    for(i=0;i<stageCount-1;i++){
        var time = getTimeLast(stageTimes[i], stageTimes[i+1]);

        times[i]=time;
    }
    times[stageCount-1]=getTimeLast(stageTimes[stageCount-1], endTime);
    return times;
}

function countSleepInterupt(stageTypes){
    var interupt = -1;
    for(var i=0;i<stageTypes.length;i++){
        if(stageTypes[i]=='first'){
            interupt++;
        }
    }

    if(interupt == -1){
        interupt = 0;
    }
    return interupt;
}

function divideCircle(stageTypes, stageTimes, endTime){
    var circles = new Array();
    var lastTimes = getStageLastTimes(stageTimes, endTime);
    var sleepAbortCount = 0;
    for(i=0;i<stageTypes.length;i++){
        if(stageTypes[i] == 'first'){//第一期，一定开始一个循环
            var circle = new Array();
            i++;
            if(stageTypes[i]!='second'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }

            i++;
            if(stageTypes[i]!='third'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }

            i++;
            if(stageTypes[i]!='forth'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }

            i++;
            if(stageTypes[i]!='third'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }

            i++;
            if(stageTypes[i]!='second'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }

            i++;
            if(stageTypes[i]!='rem'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }
            circles.push(circle);

        }else if(stageTypes[i] == 'second'){
            circle = new Array();
            circles.push(circle);
            circle.push(lastTimes[i]);
            i++;
            if(stageTypes[i]!='third'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }

            i++;
            if(stageTypes[i]!='forth'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }

            i++;
            if(stageTypes[i]!='third'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }

            i++;
            if(stageTypes[i]!='second'){
                sleepAbortCount++;
                fillArray(circle, 6);
                continue;
            }else{
                circle.push(lastTimes[i]);
            }

            i++;
            if(stageTypes[i]!='rem'){
                sleepAbortCount++;
                fillArray(circle, 6);
            }else{
                circle.push(lastTimes[i]);
            }
        }
    }
    return circles;
}

function fillArray(array, total){
    while(array.length < total){
        array.push(0);
    }
}

function getTimeLast(start, end){
    var startH = parseInt(start.split(':')[0]);
    var startM = parseInt(start.split(':')[1]);
    var endH = parseInt(end.split(':')[0]);
    var endM = parseInt(end.split(':')[1]);
    //alert(startH+":"+startM+" "+endH+":"+endM );

    var last = 0;
    if(startH > endH){//隔天了
        last = 60*(24-startH)-startM + 60*(endH)+endM;
    }else if(startH < endH){
        last = (endH - startH) *60-startM+endM;
    }else if(startH == endH){
        last = endM - startM;
    }

    return last;
}


var sleepDetailInnerHTML = '';
function sleepDetailRecordClicked(){
    if(document.getElementById('sleep-stage-conclusion').style.visibility == 'visible'){
        sleepDetailInnerHTML = document.getElementById('sleep-stage-conclusion').innerHTML;
        document.getElementById('sleep-stage-conclusion').innerHTML = '';
        document.getElementById('sleep-stage-conclusion').style.visibility = 'hidden';
    }else{
        document.getElementById('sleep-stage-conclusion').innerHTML = sleepDetailInnerHTML;
        document.getElementById('sleep-stage-conclusion').style.visibility = 'visible';
    }
}

function updateWalk(walkArray){
    var totalDistance = 0;
    var totalDuration = 0;
    var startTimes = new Array();
    var durations = new Array();
    var distances = new Array();
    var walkRowHTML = '';
    for(var i=0;i<walkArray.length;i++){
        var walk = walkArray[i];
        var duration = parseInt(walk['DURATION']);
        var distance = parseFloat(walk['DISTANCE']);
        durations.push(duration);
        distances.push(distance/100);
        totalDistance+=distance;
        totalDuration+=duration;
        var startTime = walk['STARTTIME'];
        startTimes.push(startTime);
        walkRowHTML += '<tr><td>'+(i+1)+'</td><td>'+startTime+'</td><td>'+duration+'</td><td>'+distance+'</td></tr>';
    }
    document.getElementById('walk-table-items').innerHTML = walkRowHTML;
    document.getElementById('walk-distance').innerHTML = totalDistance;
    document.getElementById('walk-time').innerHTML = totalDuration;

    var barChartData = {
        labels : startTimes,
        datasets : [
            {
                fillColor : "rgba(62,143,246,1)",
                strokeColor : "rgba(220,220,220,1)",
                data : durations
            },
            {
                fillColor : "rgba(245,184,0,1)",
                strokeColor : "rgba(220,220,220,1)",
                data : distances
            }
        ]

    };

    document.getElementById('walk-bar').setAttribute('width', '300');
    document.getElementById('walk-bar').setAttribute('height', '300');
    var myLine = new Chart(document.getElementById("walk-bar").getContext("2d")).Bar(barChartData);
}

function updateJog(jogArray){
    var totalDistance = 0;
    var totalDuration = 0;
    var startTimes = new Array();
    var durations = new Array();
    var distances = new Array();
    var walkRowHTML = '';
    for(var i=0;i<jogArray.length;i++){
        var jog = jogArray[i];
        //alert(walk);
        var duration = parseInt(jog['DURATION']);
        var distance = parseFloat(jog['DISTANCE'])/100;
        durations.push(duration);
        distances.push(distance);
        totalDistance+=distance;
        totalDuration+=duration;
        var startTime = jog['STARTTIME'];
        startTimes.push(startTime);
        walkRowHTML += '<tr><td>'+(i+1)+'</td><td>'+startTime+'</td><td>'+duration+'</td><td>'+distance+'</td></tr>';
    }
    document.getElementById('jog-table-items').innerHTML = walkRowHTML;
    document.getElementById('jog-distance').innerHTML = totalDistance;
    document.getElementById('jog-duration').innerHTML = totalDuration;

    var barChartData = {
        labels : startTimes,
        datasets : [
            {
                fillColor : "rgba(62,143,246,1)",
                strokeColor : "rgba(220,220,220,1)",
                data : durations
            },
            {
                fillColor : "rgba(245,184,0,1)",
                strokeColor : "rgba(220,220,220,1)",
                data : distances
            }
        ]

    };

    document.getElementById('jog-bar').setAttribute('width', '300');
    document.getElementById('jog-bar').setAttribute('height', '300');
    var myLine = new Chart(document.getElementById("jog-bar").getContext("2d")).Bar(barChartData);
}

function updateStep(step, position){
    document.getElementById('total-step').innerHTML = step;
    document.getElementById('step-beat').innerHTML = (1-parseFloat(position))*100;
    document.getElementById('step-lose').innerHTML = parseFloat(position)*100;


    var pieData = [
        {
            value: parseFloat(position),
            color:"#DE120B"
        },
        {
            value : 1-parseFloat(position),
            color : "#3E8FF6"
        }

    ];
    document.getElementById('step-pie').setAttribute('width', '300');
    document.getElementById('step-pie').setAttribute('height', '300');
    var myPie = new Chart(document.getElementById("step-pie").getContext("2d")).Pie(pieData);
}

function updatePulse(pulseArray){
    var pulseRowHTML = '';
    var times = ['正常下限'];
    var rates = [55];
    for(var i=0;i<pulseArray.length;i++){
        var pulse = pulseArray[i];
        times.push(pulse['time']);
        rates.push(parseInt(pulse['rate']));
        var comment = '正常';
        if(pulse['rate']>100){
            comment = '偏高';
        }else if(pulse['rate']<55){
            comment = '偏低';
        }
        pulseRowHTML += '<tr><td>'+(i+1)+'</td><td>'+pulse['time']+'</td><td>'+pulse['rate']+'</td><td>'+comment+'</td></tr>';
    }
    times.push('正常上限');
    rates.push(100);
    rates.push(0);
    document.getElementById('pulse-table-items').innerHTML = pulseRowHTML;


    var barChartData = {
        labels : times,
        datasets : [
            {
                fillColor : "rgba(62,143,246,1)",
                strokeColor : "rgba(220,220,220,1)",
                data : rates
            }
        ]

    };

    document.getElementById('pulse-bar').setAttribute('width', '300');
    document.getElementById('pulse-bar').setAttribute('height', '300');
    var myChart = new Chart(document.getElementById("pulse-bar").getContext("2d")).Bar(barChartData);
}

function updatePressure(pressureArray){
    var pressureRowHTML = '';
    var highs = [90];
    var lows = [60];
    var times = ['正常下限'];
    for(var i=0;i<pressureArray.length;i++){
        var pressure = pressureArray[i];
        var time = pressure['time'];
        var high = parseInt(pressure['high']);
        var low = parseInt(pressure['low']);
        //times.push(time);
        times.push(time);
        highs.push(high);
        lows.push(low);
        var comment = '正常';

        if(high>140 || low>90){
            comment = '血压偏高';
        }else if(high<90 || low<60){
            comment = '血压偏低';
        }


        pressureRowHTML += '<tr><td>'+(i+1)+'</td><td>'+time+'</td><td>'+high+'</td><td>'+low+'</td><td>'+comment+'</td></tr>';
    }
    times.push('正常上限');
    highs.push(140);
    lows.push(90);

    //
    highs.push(0);

    document.getElementById('pressure-table-items').innerHTML = pressureRowHTML;


    var barChartData = {
        labels : times,
        datasets : [
            {
                fillColor : "#DE120B",
                strokeColor : "#DE120B",
                data : highs
            },
            {
                fillColor : "#3E8FF6",
                strokeColor : "#3E8FF6",
                data : lows
            }
        ]

    };

    document.getElementById('pressure-bar').setAttribute('pressure', '300');
    document.getElementById('pressure-bar').setAttribute('pressure', '300');
    var myChart = new Chart(document.getElementById("pressure-bar").getContext("2d")).Bar(barChartData);
}

function showData(){
    document.getElementById('data-presentor').style.visibility = 'visible';
}