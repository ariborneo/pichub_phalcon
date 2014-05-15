<script src="/js/jquery.flippy.min.js"></script>
<script>
    function flip()
    {
        c = $("#upload").html();
        $("#upload").flippy({
            verso: c,
            duration: "300",
            background: "#373a3d",
            onStart: function(){
                //alert($(this).html());
            },
            onFinish: function(){
                //alert("ok, it's flipped :)");
            }
        });
    }
</script>
<input type="button" value="flip" onclick="flip()">

<form id="upload" method="post" action="upload" enctype="multipart/form-data">
    <div id="drop">
        Drop Here
        <a>Browse</a>
        <input type="file" name="upl" accept="image/*" multiple />
    </div>
    <ul></ul>
</form>
<script src="/js/jquery.knob.js"></script>
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>
<script src="/js/fileupload.js"></script>






<!--
<form action="upload" method="post" enctype="multipart/form-data" style="border: #000000 1px solid;">
    <input type="file" name="Filedata" id="file" size="60" accept="image/*"><br>
    {% if user.id > 0 %}
        Альбом:
        <select name="album">
            <option value="0">-</option>
            {% for album in albums %}
                <option value="{{ album["id"] }}">{{ album["name"] }}</option>
            {% endfor %}
        </select><br>
    {% endif %}
    Описание:<br>
    <textarea name="opis"></textarea><br>
    <input type="submit" value="Загрузить" class="btn info" style="margin-top: 10px;">
</form>
<br>
-->

{% if user.id > 0 %}
    Имя пользователя: <a href="/user/{{ user.name }}">{{ user.name }}</a><br>
    Ид пользователя: {{ user.id }}<br>
    <a href="/logout">Выйти</a><br>
{% else %}
    <a href="/registration">Зарегистрироваться</a>
    <br><br>
    {% include "login/index.volt" %}
{% endif %}

<br><a href="/top">Популярные по просмотрам</a> | <a href="/last">Последние загруженные</a> | <a href="/feedback">Обратная связь</a>