/**
 * Created by Ian on 2015/12/1.
 */
function getMyAdviser(type){
    var url = '/businesslogic/userbl/MyAdviserFetcher.php5';

    var xhr = $.ajax({url:url, async: true,
        data:'type='+type, success: (function(respond){
            if(respond==''){
                respond = '空';
            }
            document.getElementById('adviser-container').innerHTML = respond;
        }), type:'get'});
}