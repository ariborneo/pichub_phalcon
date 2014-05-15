$(document).ready(function(){

    //$("#upload").css("margin-top", "200px").css("margin-bottom", "100px").css("position", "relative").css("left", (($(window).width()-$("#upload").width())/2) + "px")

});

function vk_login()
{
    var vk_app_id = 4357987;
    var redirect_uri = 'http://pichub.local/login_vk';
    window.location.href = "https://oauth.vk.com/authorize?client_id="+vk_app_id+"&scope=offline&redirect_uri="+redirect_uri+"&display=page&response_type=code";
}

function reload_captcha()
{
    document.getElementById("captcha").src = document.getElementById("captcha").src + "?timestamp=" + new Date().getTime();
}