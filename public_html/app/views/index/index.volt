<form action="upload" method="post" enctype="multipart/form-data">
    <input type="file" name="Filedata" id="file" size="60" accept="image/*">
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