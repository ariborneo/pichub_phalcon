Логин: {{ user["login"] }}<br>
Дата региастрации: {{ user["reg_time"] }}<br>
<br>Изображения:<br>
{% for image in images %}
    <a href="/show/{{ image["code"] }}" target="_blank"><img src="/{{ image["path"] }}"></a>&nbsp;
{% endfor %}