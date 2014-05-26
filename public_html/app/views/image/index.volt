<img src="/pic_b/{{ image["path"] }}" style="max-height: 600px;"><br>
Описание: {{ image["opis"] }}<br>
Дата добавления: {{ image["time"] }}<br>
Просмотров: {{ image["views"] }}<br>
Пользователь:
    {% if image["user"] > 0 %}
        <a href="/user/{{ image["username"] }}">{{ image["username"] }}</a>
    {% else %}
        -
    {% endif %}
<br>

{% if image["album"]["id"] > 0 %}
    Альбом: <a href="/album/{{ image["album"]["id"] }}">{{ image["album"]["name"] }}</a>
{% else %}
    Альбом: -
{% endif %}
<br>

<script>

    function get_image_code()
    {
        var code_arr = window.location.href.split("show/");
        return code_arr[1];
    }

    function _alert(text)
    {
        $.fallr('show', {
            content : "<p>" + text + "</p>"
        });
    }

    $(document).ready(function(){

        $("#del_request").click(function(){
            var del_request = function(){
                var fallr = $(this);
                var text = fallr.children('form').children('textarea[name="text"]').val();
                if(text.length < 20){
                    $.fallr('shake');
                } else {
                    $.post(fallr.children('form').attr('action'), {"text": text}, function(data){
                        var obj = JSON.parse(data);
                        if(obj["status"] == "success")
                        {
                            fallr.next().html('');
                            fallr.html('<h4>Запрос отправлен</h4>');
                            setTimeout(function(){ $.fallr('hide') }, 2000);
                        }
                        else if(obj["status"] == "error")
                        {
                            alert(obj["messages"]);
                        }
                    });
                }
            }
            $.fallr('show', {
                icon        : 'trash',
                width       : '400px',
                position    : "center",
                content     : '<h4>Запрос на удаление</h4>'
                        + '<form action="/del_request/' + get_image_code() + '">'
                        +     '<textarea name="text"></textarea>'
                        + '</form>',
                buttons : {
                    button1 : {text: 'Отправить', onclick: del_request},
                    button4 : {text: 'Отмена'}
                }
            });
            return false;
        });

        $(this).on("submit", "#comment_add", function()
        {
            var form = $(this);
            var url = form.attr("action");
            var text = form.find("textarea[name='text']").first().val();
            if(text.length > 0)
            {
                $.post(url, {"text": text}, function(data){
                    console.log(data);
                    var obj = JSON.parse(data);
                    if(obj["status"] == "success")
                    {
                        $("#comments_count").html(obj["info"]["comments"]);
                        $("#comments").prepend("<li>"+text+"<br><a href='/user/"+obj["info"]["user"]["name"]+"' target='_blank'>"+obj["info"]["user"]["name"]+"</a> - "+obj["info"]["comment"]["time"]+" - <a href='/comment_del/"+obj["info"]["comment"]["id"]+"' class='comment_del'>Удалить</a></li>");
                        form.find("textarea[name='text']").first().val("");
                    }
                    else if(obj["status"] == "error")
                    {
                        alert(obj["message"]);
                    }
                });
            }
            return false;
        });

        $(this).on("click", "#comments li a.comment_del", function()
        {
            var link = $(this);
            $.fallr('show', {
                buttons : {
                    button1 : {text: 'Да', danger: true, onclick: function(){
                        var url = link.attr("href");
                        $.get(url, function(data){
                            console.log(data);
                            var obj = JSON.parse(data);
                            if(obj["status"] == "success")
                            {
                                $("#comments_count").html(obj["info"]["comments"]);
                                link.parent().remove();
                            }
                            else if(obj["status"] == "error")
                            {
                                alert(obj["message"]);
                            }
                        });
                        $.fallr('hide');
                    }},
                    button2 : {text: 'Нет'}
                },
                content : "<p>Are you sure?</p>",
                icon    : "error"
            });
            return false;
        });

        $(this).on("click", "#like_button", function()
        {
            var url = $(this).attr("href");
            $.get(url, function(data){
                console.log(data);
                var obj = JSON.parse(data);
                if(obj["status"] == "success")
                {
                    if(obj["action"] == "like")
                    {
                        $("#like_button").html("Dislike");
                    }
                    else
                    {
                        $("#like_button").html("Like");
                    }
                    $("#likes_count").html(obj["likes"]);
                }
            });
            return false;
        });

    });

</script>

Лайков: <span id="likes_count">{{ image["likes"] }}</span>&nbsp;
{% if user.id > 0 %}
    {% if image["me_like"] %}
        <a href="/like/{{ image["code"] }}" id="like_button">Dislike</a>
    {% else %}
        <a href="/like/{{ image["code"] }}" id="like_button">Like</a>
    {% endif %}
{% endif %}

{% if user.id != image["user"] %}
    <br><a href="/del_request/{{ image["code"] }}" id="del_request">Запрос на удаление</a><br>
{% endif %}


<br><br>
<div>
    Ссылка для просмотра картинки<br>
    <textarea onMouseOver="this.select()" rows="2" cols="200" readonly>http://{{ domain }}/show/{{ image["code"] }}</textarea><br>
    Прямая ссылка<br>
    <textarea onMouseOver="this.select()" rows="2" cols="200" readonly>http://{{ domain }}/pic_b/{{ image["path"] }}</textarea><br>
    BBcode для форума, блога (уменьшенное изображение)<br>
    <textarea  onmouseover="this.select()" rows="2" cols="200" readonly>[url=http://{{ domain }}/show/{{ image["code"] }}][img]http://{{ domain }}/pic_s/{{ image["path"] }}[/img][/url]</textarea><br>
    BBcode для форума, блога (реальный размер)<br>
    <textarea  onmouseover="this.select()" rows="2" cols="200" readonly>[url=http://{{ domain }}/show/{{ image["code"] }}][img]http://{{ domain }}/pic_b/{{ image["path"] }}[/img][/url]</textarea><br>
    HTML-код<br>
    <textarea onMouseOver="this.select()" rows="2" cols="200" readonly><a href="http://{{ domain }}/show/{{ image["code"] }}" target="_blank"><img src="http://{{ domain }}/pic_s/{{ image["path"] }}" border=0></a></textarea><br>
    Ссылка на редактирование и удаление<br>
    <textarea onMouseOver="this.select()" rows="2" cols="200" readonly style="border-color:red;">http://{{ domain }}/show/{{ image["code"] }}/{{ image["editcode"] }}</textarea>
</div>


<br><br>Комментарии (<span id="comments_count">{{ image["comments"] }}</span>) :<br>
<style>
    #comments li
    {
        margin-top: 10px;
    }
    #comments li:last-child
    {
        margin-bottom: 10px;
    }
</style>
<ul id="comments">
{% for comment in comments %}
    <li>
        {{ comment.text }}<br>
        <a href="/user/{{ comment.user.name }}" target="_blank">{{ comment.user.name }}</a> - {{ comment.time }}
        {% if user.id == comment.user.id %}
            - <a class="comment_del" href="/comment_del/{{ comment.id }}">Удалить</a>
        {% endif %}
    </li>
{% endfor %}
</ul>
{% if user.id > 0 %}
    <form action="/comment_add/{{ image["code"] }}" method="post" id="comment_add">
        <textarea name="text"></textarea>
        <input type="submit" value="Отправить">
    </form>
{% endif %}