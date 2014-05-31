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


<script>

    $(document).ready(function(){
        $("#upload_url").submit(function(){
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
        $("#login, #registration, #feedback").click(function(){
            var link = $(this);
            var id = link.attr("id");
            var form = $("#modal_forms").children("#modal_"+id);
            form.children("form").children("input[type='submit'], br").remove();
            var send = function(){
                var fallr = $(this);
                $.post(fallr.children('form').attr('action'), fallr.children('form').serialize(), function(data){
                    console.log(data);
                    var obj = JSON.parse(data);
                    if(obj["status"] == "success")
                    {
                        if(id == "feedback")
                        {
                            fallr.next().html('');
                            fallr.html('<h4>Сообщение отправлено</h4>');
                            setTimeout(function(){ $.fallr('hide') }, 2000);
                        }
                        else
                        {
                            window.location.reload();
                        }
                    }
                    else if(obj["status"] == "error")
                    {
                        $.fallr('shake');
                    }
                });
            };
            var get_icon = function(){
                switch (id){
                    case "feedback":
                        icon = "mail"; break;
                    default:
                        icon = "check";
                }
                return icon;
            };
            $.fallr('show', {
                icon        : get_icon(),
                width       : '500px',
                position    : "center",
                content     : form.html(),
                buttons : {
                    button1 : {text: 'Отправить', onclick: send},
                    button2 : {text: 'Отмена'}
                }
            });
            return false;
        });
        $("#logout").click(function(){
            var link = $(this);
            $.fallr('show', {
                buttons : {
                    button1 : {text: 'Да', danger: true, onclick: function(){
                        window.location.href = link.attr("href");
                    }},
                    button2 : {text: 'Отмена'}
                },
                content : '<p>Точно выйти?</p>',
                icon    : 'error'
            });
            return false;
        });
    });
</script>






<link rel="stylesheet" type="text/css" href="/css/flipcard/flipcard.css"/>
<script src="/js/flipcard/flipcard.js"></script>
<script>
    function flip()
    {
        $(".card-container").flip();
    }
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
            <form action="/upload" method="post" id="upload_url">
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
        </div>
    </div>
</div>


<div id="modal_forms" style="display: none;">
    <div id="modal_login">{% include "login/login.volt" %}</div>
    <div id="modal_registration">{% include "login/registration.volt" %}</div>
    <div id="modal_feedback">{% include "index/feedback.volt" %}</div>
</div>


<script src="/js/fileupload/jquery.knob.js"></script>
<script src="/js/fileupload/jquery.ui.widget.js"></script>
<script src="/js/fileupload/jquery.iframe-transport.js"></script>
<script src="/js/fileupload/jquery.fileupload.js"></script>
<script src="/js/fileupload/fileupload.js"></script>


{% if user.id > 0 %}
    Имя пользователя: <a href="/user/{{ user.name }}">{{ user.name }}</a><br>
    Ид пользователя: {{ user.id }}<br>
    <a href="/logout" id="logout">Выйти</a><br>
{% else %}
    <a href="/registration" id="registration">Зарегистрироваться</a> | <a href="/login" id="login">Войти</a><br>
{% endif %}

<br><a href="/top">Популярные по просмотрам</a> | <a href="/last">Последние загруженные</a> | <a href="/feedback" id="feedback">Обратная связь</a>