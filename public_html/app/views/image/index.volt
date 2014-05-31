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

{% if is_edit == false %}
    <br><a href="/del_request/{{ image["code"] }}" id="del_request">Запрос на удаление</a><br>
{% endif %}


{% if is_edit == true %}
    <br><br>
    <script>
        $(document).ready(function(){
            $("#pr").click(function(){
                $.get("/change_private/" + get_image_code(), function(data){
                    console.log(data);
                });
            });
        });
    </script>
    {% if user.id > 0 %}
        <input type="checkbox" id="pr" value="1" {% if image["private"] == 1 %}checked{% endif %}>Приватное
        <br><br>
    {% endif %}
    <form action="/edit/{{ image['code'] }}?editcode={{ image["editcode"] }}" method="post">
        Повернуть на
        <select name="rotate">
            <option value="0" selected>0&deg;</option>
            <option value="270">90&deg; вправо</option>
            <option value="180">180&deg;</option>
            <option value="90">90&deg; влево</option>
        </select><br>
        Надпись
        <input type="text" name="title" maxlength="100" style="width:90px; margin:3px;" onclick="jQuery(this).animate({width:220},200)" onblur="this.style.width='90px'">
        <select name="title_color">
            <option value="FFFFFF" selected>Белый</option>
            <option value="000000">Черный</option>
            <option value="00FF00">Зеленый</option>
            <option value="FF0000">Красный</option>
            <option value="0000FF">Синий</option>
            <option value="FFFF00">Желтый</option>
        </select>
        <select name="title_size">
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12" selected>12</option>
            <option value="14">14</option>
            <option value="16">16</option>
            <option value="18">18</option>
            <option value="20">20</option>
            <option value="22">22</option>
            <option value="24">24</option>
            <option value="26">26</option>
            <option value="28">28</option>
            <option value="36">36</option>
            <option value="48">48</option>
            <option value="72">72</option>
        </select><br>
        Закруглить углы на
        <select name="corner_radius">
            <option value="0">0&deg;</option>
            <option value="3">3&deg;</option>
            <option value="5">5&deg;</option>
            <option value="10">10&deg;</option>
            <option value="15">15&deg;</option>
            <option value="20">20&deg;</option>
            <option value="30">30&deg;</option>
            <option value="40">40&deg;</option>
            <option value="50">50&deg;</option>
            <option value="60">60&deg;</option>
            <option value="70">70&deg;</option>
            <option value="80">80&deg;</option>
            <option value="90">90&deg;</option>
            <option value="100">100&deg;</option>
        </select><br>
        <div class="param-header" onclick="showhide('param_filter',2,'h',200)">Тональность, цвета</div>
        <div class="param-body" id="param_filter">
            <input type="radio" name="filter" id="filter" value="" checked><label for='filter'>Не изменять</label><br>
            <input type="radio" name="filter" id="filter_grey" value="grey"><label for='filter_grey'>Черно-белый</label><br>
            <input type="radio" name="filter" id="filter_red" value="red"><label for='filter_red'>Красный</label><br>
            <input type="radio" name="filter" id="filter_green" value="green"><label for='filter_green'>Зеленый</label><br>
            <input type="radio" name="filter" id="filter_blue" value="blue"><label for='filter_blue'>Синий</label><br>
            <input type="radio" name="filter" id="filter_negate" value="negate"><label for='filter_negate'>Негатив</label><br>
            <input type="radio" name="filter" id="filter_sepia" value="sepia"><label for='filter_sepia'>Сепия</label><br>
            Осветление изображения на
            <select name="light">
                <option value="0" selected>0</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="60">60</option>
                <option value="70">70</option>
                <option value="80">80</option>
                <option value="90">90</option>
                <option value="100">100</option>
            </select> %<br>
            Размыть
            <select name="blur">
                <option value="0" selected>0</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="60">60</option>
                <option value="70">70</option>
                <option value="80">80</option>
                <option value="90">90</option>
                <option value="100">100</option>
            </select><br>
            Контраст
            <select name="contrast">
                <option value="0" selected>0</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="60">60</option>
                <option value="70">70</option>
                <option value="80">80</option>
                <option value="90">90</option>
                <option value="100">100</option>
            </select> %<br>
            Сглаживание
            <select name="smooth">
                <option value="0" selected>0</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="60">60</option>
                <option value="70">70</option>
                <option value="80">80</option>
                <option value="90">90</option>
                <option value="100">100</option>
            </select> %<br>
        </div>
        <div class="param-header" onclick="showhide('param_resize',2,'h',200)">Изменение размера</div>
        <div class="param-body" id="param_resize">
            <input type="radio" name="resize" value="0" checked>
            <label for='resize'>Не уменьшать</label><br>
            <input type="radio" id="resize1" name="resize" value="1"><label for='resize1'>Уменьшить по ширине до</label>
            <input type="text" name="resize_w" size="3" maxlength="5" value="300"> пикселей<br>
            <input type="radio" id="resize2" name="resize" value="2"><label for='resize2'>Уменьшить по высоте до</label>
            <input type="text" name="resize_h" size="3" maxlength="5" value="300"> пикселей<br>
            <input type="radio" id="resize3" name="resize" value="3"><label for='resize3'>Уменьшить до</label>
            <input type="text" name="resize_p" size="2" maxlength="2" value="30"> %<br>
        </div>
        <div class="param-header" onclick="showhide('param_reflect',2,'h',200)">Отразить</div>
        <div class="param-body" id="param_reflect">
            <input type="radio" id="reflect" name="reflect" value="0" checked><label for='reflect'>Не отражать</label><br>
            <input type="radio" id="reflect1" name="reflect" value="1"><label for='reflect1'>Отразить по горизонтали</label><br>
            <input type="radio" id="reflect2" name="reflect" value="2"><label for='reflect2'>Отразить по вертикали</label><br>
            <input type="checkbox" id="reflection_effect" name="reflection_effect" value="1"><label for='reflection_effect'>Эффект отражения</label>
        </div>
        <input type="submit" value="Отправить">
    </form>
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
    {% if is_edit == true %}
        Ссылка на редактирование и удаление<br>
        <textarea onMouseOver="this.select()" rows="2" cols="200" readonly style="border-color:red;">http://{{ domain }}/show/{{ image["code"] }}/{{ image["editcode"] }}</textarea>
    {% endif %}
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