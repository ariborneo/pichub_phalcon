Топ по просмотрам<br><br>

{% for image in images %}
    <span style="float: left;">
        <a href="/show/{{ image["code"] }}" target="_blank"><img src="{{ image["c_path"] }}"></a><br>
        {{ image["views"] }}
    </span>
{% endfor %}