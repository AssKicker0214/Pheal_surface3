/**
 * Created by Ian on 2015/12/2.
 */
function applyActivity(btn){
    actvtid = btn.value.split(';')[0];
    userid = btn.value.split(';')[1];
    var xhr = $.ajax({url:"../../businesslogic/activitybl/ApplyActivity.php5", async: true,
        data:'actvtid='+actvtid+'&userid='+userid, success: (function(respond){
            //btn.outerHTML = '<h2><span class="label label-default">'+respond+'</span></h2>';
            location.reload(true);
            document.getElementById('participant-count').innerHTML = ''+(getParticipantCount()+1);
        }), type:'post'});
}

function quitActivity(btn){
    actvtid = btn.value.split(';')[0];
    userid = btn.value.split(';')[1];
    var xhr = $.ajax({url:"../../businesslogic/activitybl/QuitActivity.php5", async: true,
        data:'actvtid='+actvtid+'&userid='+userid, success: (function(respond){
            btn.outerHTML = '<button id="apply-now-btn" onclick="applyActivity(this)" value="'+btn.value+'" class="btn btn-success btn-lg">立刻参加</button>'

            location.reload(true);
            document.getElementById('participant-count').innerHTML = ''+(getParticipantCount()-1);
        }), type:'post'});
}

function applyActivity_Tile(btn){
    actvtid = btn.value.split(';')[0];
    userid = btn.value.split(';')[1];
    var xhr = $.ajax({url:"../../businesslogic/activitybl/ApplyActivity.php5", async: true,
        data:'actvtid='+actvtid+'&userid='+userid, success: (function(respond){
            btn.outerHTML = '<button onclick="quitActivity_Tile(this)" value="'+btn.value+'" class="btn btn-danger btn-block btn-sm">退出</button>'
            alert(respond);
            //location.reload(true);
            //document.getElementById('participant-count').innerHTML = ''+(getParticipantCount()+1);
        }), type:'post'});
}

function quitActivity_Tile(btn){
    actvtid = btn.value.split(';')[0];
    userid = btn.value.split(';')[1];
    var xhr = $.ajax({url:"../../businesslogic/activitybl/QuitActivity.php5", async: true,
        data:'actvtid='+actvtid+'&userid='+userid, success: (function(respond){
            btn.outerHTML = '<button onclick="applyActivity_Tile(this)" value="'+btn.value+'" class="btn btn-success btn-block btn-sm">参加</button>'
            alert(respond);
        }), type:'post'});
}

function getParticipantCount(){
    var count = parseInt(document.getElementById('participant-count').innerHTML);
    //alert(count);
    return count;
}

function getActivityList(text){
    var type = '全部';
    if(text == '即将开始'){
        type = '未开始';
    }else if(text == '正在进行'){
        type = '进行中';
    }else if(text == '已经结束'){
        type = '已结束';
    }
    var xhr = $.ajax({url:"../../businesslogic/activitybl/ActivityClassifier.php5",
                        async: true, data:'type='+type, success: (function(respond){
            document.getElementById('activity-list').innerHTML = respond;
            document.getElementById('activity-type').innerHTML = text;
        })})
}