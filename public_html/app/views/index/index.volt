<form action="upload" method="post" enctype="multipart/form-data" style="border: #000000 1px solid;">
    <input type="file" name="Filedata" id="file" size="60" accept="image/*"><br>
    Альбом:
    <select name="album">
        <option value="0">-</option>
        {% for album in albums %}
            <option value="{{ album["id"] }}">{{ album["name"] }}</option>
        {% endfor %}
    </select><br>
    Описание:<br>
    <textarea name="opis"></textarea><br>
    <input type="submit" value="Загрузить" class="btn info" style="margin-top: 10px;">
</form>
<br>

{% if user["id"] %}
    Имя пользователя: <a href="/user/{{ user["name"] }}">{{ user["name"] }}</a><br>
    Ид пользователя: {{ user["id"] }}<br>
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