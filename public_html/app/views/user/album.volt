Название: {{ album["name"] }}<br>
Всего изображений: {{ album["count"] }}<br>

<br>Изображения:<br>
{% for image in images %}
    <a href="/show/{{ image["code"] }}" target="_blank"><img src="/{{ image["path"] }}"></a>&nbsp;
{% endfor %}