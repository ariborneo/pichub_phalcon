$(document).ready(function(){
});

function vk_login()
{
    var vk_app_id = 4357987;
    var redirect_uri = "http://" + window.location.hostname + "/login_vk";
    window.location.href = "https://oauth.vk.com/authorize?client_id="+vk_app_id+"&scope=offline&redirect_uri="+redirect_uri+"&display=page&response_type=code";
}

function reload_captcha()
{
    $("#captcha").attr("src", $("#captcha").attr("src") + "?timestamp=" + new Date().getTime());
}