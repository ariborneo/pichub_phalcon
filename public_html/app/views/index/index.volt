<style>
    .stuff {
        display: none;
    }

    video, canvas {
        background: rgba(0,0,0,0.1);
    }

    video {
        transform: scaleX(-1);
        -o-transform: scaleX(-1);
        -ms-transform: scaleX(-1);
        -moz-transform: scaleX(-1);
        -webkit-transform: scaleX(-1);
    }

    .button {
        display: inline-block;
        cursor: pointer;
        padding: 0.75em 1em;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        font: normal normal normal 1.25em/normal "Abel", Helvetica, sans-serif;
        color: rgba(255,255,255,1);
        background: rgb(98,172,21);
        text-shadow: 0 -1px 0 rgb(91,129,17);
        border: none;
        outline: none;
        display: none;
    }

    .button:hover {
        background: rgb(111,186,34);
        -webkit-transition: all 200ms cubic-bezier(0.42,0,0.58,1) 10ms;
        -moz-transition: all 200ms cubic-bezier(0.42,0,0.58,1) 10ms;
        -o-transition: all 200ms cubic-bezier(0.42,0,0.58,1) 10ms;
        transition: all 200ms cubic-bezier(0.42,0,0.58,1) 10ms;
    }

    .button:active {
        -webkit-box-shadow: 0 1px 4px 0 rgb(65,105,23) inset;
        box-shadow: 0 1px 4px 0 rgb(65,105,23) inset;
    }

    @media only screen and (max-width: 680px) {
        .item2 {
            display: none;
        }
    }
</style>

<script>

    window.onload = function(){
        var canvas = document.getElementById('canvas');
        var video = document.getElementById('video');
        var button = document.getElementById('button');
        var buttonStart = document.getElementById('buttonStart');
        var stuff = document.getElementById('stuff');
        var context = canvas.getContext('2d');
        var videoStreamUrl = false;
        $(buttonStart).show();
        var base64dataUrl;

        $(buttonStart).click(function(){
            $(buttonStart).hide();
            $(stuff).show();
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
            window.URL.createObjectURL = window.URL.createObjectURL || window.URL.webkitCreateObjectURL || window.URL.mozCreateObjectURL || window.URL.msCreateObjectURL;
            //console.log('request', navigator.getUserMedia);
            navigator.getUserMedia({video: true}, function(stream) {
                videoStreamUrl = window.URL.createObjectURL(stream);
                video.src = videoStreamUrl;
                button.style.display = 'block';
            }, function(){
                console.log('что-то не так с видеостримом');
            });
        });

        $(button).click(function(){
            if(!videoStreamUrl) alert('То-ли вы не нажали "разрешить" в верху окна, то-ли что-то не так с вашим видео стримом');
            canvas.width = $(video).width();
            canvas.height = $(video).height();
            video.width = $(video).width();
            video.height = $(video).height();
            $("#video, #button").hide();
            $("#canvas, #wc_cancel, #wc_upload").show();
            context.translate(canvas.width, 0);
            context.scale(-1, 1);
            context.drawImage(video, 0, 0, video.width, video.height);
            base64dataUrl = canvas.toDataURL('image/png');
        });

        $('#wc_upload').click(function(){
            $.post("/upload", {"base64": base64dataUrl}, function(data){
                console.log(data);
                $('#wc_cancel').click();
            })
        });

        $('#wc_cancel').click(function(){
            $("#canvas, #wc_cancel, #wc_upload").hide();
            $("#video, #button").show();
        });

    };

</script>



<input id="buttonStart" type="button" class="button" value="Камера" />
<div class="stuff" id="stuff">
    <input id="button" type="button" class="button" value="Кадр" />
    <input id="wc_cancel" type="button" class="button" value="Отмена" style="display: none;" />
    <input id="wc_upload" type="button" class="button" value="Загрузить" style="display: none;" />
    <video id="video" autoplay="autoplay" ></video>
    <canvas id="canvas" style="display: none;"></canvas>
</div>






<br><br><br>






<link rel="stylesheet" type="text/css" href="/css/flipcard/flipcard.css"/>
<script src="/js/flipcard/flipcard.js"></script>
<script>
    function flip()
    {
        $(".card-container").flip();
    }
</script>







<form action="/upload" method="post" id="upload_url" style="margin-bottom: 50px;">
    <input name="urls[]" type="text"><br/>
    <input name="urls[]" type="text"><br/>
    <input name="urls[]" type="text"><br/>
    <input name="urls[]" type="text"><br/>
    <input name="urls[]" type="text"><br/>
    <input name="urls[]" type="text"><br/>
    <input name="urls[]" type="text"><br/>
    <input name="urls[]" type="text"><br/>
    <input name="urls[]" type="text"><br/>
    <input name="urls[]" type="text"><br/>
    <input type="submit" value="Upload">
</form>

<script>
    $(function()
    {
        $("#upload_url").submit(function()
        {
            var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
            var urls = $(this).find("input[type='text']");
            $.each(urls, function(key, value){
                var url = $(value).val();
                if(regexp.test(url))
                {
                    $.post("/upload", {url: url}, function(data){
                        console.log(data);
                        $(this).remove();
                    });
                }
            });
            return false;
        });
    });
</script>










<link href='http://fonts.googleapis.com/css?family=Headland+One|Open+Sans:400,300&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="/css/avgrund/reset.css">
<link rel="stylesheet" href="/css/avgrund/style.css">
<link rel="stylesheet" href="/css/avgrund/avgrund.css">
<script src="/js/avgrund/jquery.avgrund.js"></script>
<script>

    $(document).ready(function(){
        $('#feedback').click(function(){
            var feedback_send = function(){
                var fallr = $(this);
                $.post(fallr.children('form').attr('action'), fallr.children('form').serialize(), function(data){
                    console.log(data);
                    var obj = JSON.parse(data);
                    if(obj["status"] == "success")
                    {
                        fallr.next().html('');
                        fallr.html('<h4>Сообщение отправлено</h4>');
                        setTimeout(function(){ $.fallr('hide') }, 2000);
                    }
                    else if(obj["status"] == "error")
                    {
                        $.fallr('shake');
                    }
                });
            }
            $.fallr('show', {
                icon        : 'mail',
                width       : '600px',
                position    : "center",
                content     : '<h4>Обратная связь</h4>'
                        + '<form action="/feedback" method="post">'
                        + '<table style="border: 0;">'
                        + '<tr><td width="100px"><b>Имя:</b></td><td><input type="text" name="name"></td></tr>'
                        + '<tr><td><b>Email:</b></td><td><input type="text" name="email" title="Указывайте верный email, т.к. на него придет ответ"></td></tr>'
                        + '<tr><td><b>Тема:</b></td><td><input type="text" name="subject"></td><td id="theme_result"></td></tr>'
                        + '<tr><td><b>Сообщение:</b></td><td><textarea name="text" rows="5"></textarea></td></tr>'
                        + '<tr><td><b>Капча:</b></td><td> <input type="button" value="reload" onclick="reload_captcha()"> <img src="/captcha" id="captcha"> <input type="text" name="captcha"></td></tr>'
                        + '</table>'
                        + '</form>',
                buttons : {
                    button1 : {text: 'Отправить', onclick: feedback_send},
                    button4 : {text: 'Отмена'}
                }
            });
            return false;
        });
    });

    $(function() {
        /*
        $('#feedback').avgrund({
            height: 500,
            holderClass: 'custom',
            showClose: true,
            showCloseText: 'Закрыть',
            enableStackAnimation: true,
            onBlurContainer: '#container',
            template: '<p></p>',
            onLoad: function (elem){
                $.get("/feedback", function(data){
                    $(".avgrund-popin").html("<p>" + data + "</p> <a href='#' class='avgrund-close'>Закрыть</a>");
                });
            }
        });
        */
        $('#login').avgrund({
            height: 200,
            holderClass: 'custom',
            showClose: true,
            showCloseText: 'Закрыть',
            enableStackAnimation: true,
            onBlurContainer: '#container',
            template: '<p></p>',
            onLoad: function (elem){
                $.get("/login", function(data){
                    $(".avgrund-popin").html("<p>" + data + "</p> <a href='#' class='avgrund-close'>Закрыть</a>");
                });
            }
        });
        $('#registration').avgrund({
            height: 200,
            holderClass: 'custom',
            showClose: true,
            showCloseText: 'Закрыть',
            enableStackAnimation: true,
            onBlurContainer: '#container',
            template: '<p></p>',
            onLoad: function (elem){
                $.get("/registration", function(data){
                    $(".avgrund-popin").html("<p>" + data + "</p> <a href='#' class='avgrund-close'>Закрыть</a>");
                });
            }
        });
    });
</script>







<input type="button" value="flip" onclick="flip()">

<div class="card-container" style="width: 320px; height:300px; margin:200px auto 100px; padding: 10px; left:5px; top:5px;">
    <div class="card">
        <div class="front">
            <form id="upload" method="post" action="upload" enctype="multipart/form-data">
                <div id="drop">
                    Drop Here
                    <a>Browse</a>
                    <input type="file" name="upl" accept="image/*;capture=camera" multiple />
                </div>
                <ul></ul>
            </form>

        </div>
        <div class="back">
            <img src="http://pichub.local/pic_c/14/05/11/fd8754e688dfd9f72b43bca2b31f18f8.jpg" width="310px" height="284px">
        </div>
    </div>
</div>


<script src="/js/jquery.knob.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>
<script src="/js/fileupload.js"></script>





{% if user.id > 0 %}
    Имя пользователя: <a href="/user/{{ user.name }}">{{ user.name }}</a><br>
    Ид пользователя: {{ user.id }}<br>
    <a href="/logout">Выйти</a><br>
{% else %}
    <a href="/registration" id="registration">Зарегистрироваться</a> | <a href="#" id="login">Войти</a><br>
{% endif %}

<br><a href="/top">Популярные по просмотрам</a> | <a href="/last">Последние загруженные</a> | <a href="/feedback" id="feedback">Обратная связь</a>