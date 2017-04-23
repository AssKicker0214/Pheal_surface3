/**
 * Created by Ian on 2015/12/6.
 */
var idValid = false;
var passwordValid = false;
var repeatValid = false;
function checkID(){
    //alert('check id');
    var id = document.getElementById('id').value;
    if(id == ""){
        document.getElementById("non_id_error").style.visibility="visible";
        idValid = false;
    }else{
        document.getElementById("non_id_error").style.visibility="Hidden";
        idValid = true;
    }

    var xhr = $.ajax({url:"../../businesslogic/userbl/IDDuplicateCheck.php5", async: true,
        data:'id='+id, success: (function(respond){
            if(respond == true){
                alert('id 重复');
                idValid = false;
            }else{

            }
        }), type:'post'});
}


function checkPassword(){
    //alert('check password');
    var password = document.getElementById('password').value;
    if(password == ""){
        document.getElementById("non_password_error").style.visibility="visible";
        passwordValid = false;
    }else{
        document.getElementById("non_password_error").style.visibility="Hidden";
        passwordValid = true;
    }

    if(password.length<6){
        alert('密码长度必须大于6位');
        passwordValid = false;
    }

    if(!document.getElementById('repeatPassword').value==''){
        checkRepeat();
    }
}

function checkRepeat(){
    //alert('check repeat');
    var rePassword = document.getElementById('repeatPassword').value;
    var password = document.getElementById('password').value;
    if(rePassword != password || rePassword == ''){
        document.getElementById("repeat_password_error").style.visibility="visible";
        repeatValid = false;
    }else{
        document.getElementById("repeat_password_error").style.visibility="Hidden";
        repeatValid = true;

    }
}


function checkSubmit(){
    //alert(idValid+" "+passwordValid+" "+repeatValid);
    if(idValid && passwordValid && repeatValid){
        document.getElementById('submit_button').disabled ="";

    }else{
        document.getElementById('submit_button').disabled="disabled";
    }

}