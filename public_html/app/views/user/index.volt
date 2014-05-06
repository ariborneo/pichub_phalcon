Логин: {{ user["login"] }}<br>
Дата региастрации: {{ user["time"] }}<br>

<a href="/create_album">Создать альбом</a><br>

<br>Альбомы:<br>
{% for album in albums %}
    <a href="/album/{{ album["id"] }}" target="_blank">{{ album["name"] }}</a><br>
{% endfor %}

<br>Изображения:<br>
{% for image in images %}
    <a href="/show/{{ image["code"] }}" target="_blank"><img src="{{ image["path"] }}"></a>&nbsp;
{% endfor %}

{{ t._("hello") }}