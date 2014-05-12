<!-- Google web fonts -->
<link href="http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700" rel='stylesheet' />
<!-- The main CSS file -->
<link href="/css/style.css" rel="stylesheet" />
<form id="upload" method="post" action="upload" enctype="multipart/form-data">
    <div id="drop">
        Drop Here
        <a>Browse</a>
        <input type="file" name="upl" accept="image/*" multiple />
    </div>
    <ul>
        <!-- The file uploads will be shown here -->
    </ul>
</form>
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.knob.js"></script>
<!-- jQuery File Upload Dependencies -->
<script src="/js/jquery.ui.widget.js"></script>
<script src="/js/jquery.iframe-transport.js"></script>
<script src="/js/jquery.fileupload.js"></script>
<!-- Our main JS file -->
<script src="/js/script.js"></script>






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
    <form action="/login" method="post">
        <input type="text" name="name">
        <input type="password" name="password">
        <input type="submit" value="Войти">
    </form>
{% endif %}

<br><a href="/top">Популярные по просмотрам</a> | <a href="/last">Последние загруженные</a> | <a href="/feedback">Обратная связь</a>