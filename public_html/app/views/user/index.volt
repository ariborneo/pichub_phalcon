Логин: {{ user["login"] }}<br>
Дата региастрации: {{ user["time"] }}<br>
id vk:
{% if user["vk_id"] is defined %}
    <a href="http://vk.com/id{{ user["vk_id"] }}" target="_blank">{{ user["vk_id"] }}</a>
{% else %}
    <a href='#' onclick="vk_login()">VK</a>
{% endif %}
<br><br>

<a href="/create_album">Создать альбом</a><br>

<br>Альбомы:<br>
{% for album in albums %}
    <a href="/album/{{ album["id"] }}" target="_blank">{{ album["name"] }}</a><br>
{% endfor %}

<br>Изображения:<br>
{% for image in images %}
    <a href="/show/{{ image["code"] }}" target="_blank"><img src="/pic_c/{{ image["path"] }}"></a>&nbsp;
{% endfor %}