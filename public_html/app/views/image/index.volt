<img src="{{ image["path"] }}"><br>
Описание: {{ image["opis"] }}<br>
Дата добавления: {{ image["time"] }}<br>
Просмотров: {{ image["views"] }}<br>

{% if image["album"]["id"] > 0 %}
    Альбом: <a href="/album/{{ image["album"]["id"] }}">{{ image["album"]["name"] }}</a><br>
{% else %}
    Альбом: -<br>
{% endif %}

Лайков: {{ image["likes"] }}&nbsp;
{% if user.id > 0 %}
    {% if image["me_like"] %}
        <a href="/dislike/{{ image["code"] }}">Dislike</a>
    {% else %}
        <a href="/like/{{ image["code"] }}">Like</a>
    {% endif %}
{% endif %}

{% if user.id != image["user"] %}
    <br><a href="/del_request/{{ image["code"] }}">Запрос на удаление</a><br>
{% endif %}

<br>Комментарии ({{ image["comments"] }}) :<br>
<ul>
{% for comment in comments %}
    <li>
        {{ comment.user }} - {{ comment.text }} - {{ comment.time }}
        {% if user.id == comment.user %}
            - <a href="/comment_del/{{ image["code"] }}/{{ comment.id }}">Удалить</a>
        {% endif %}
    </li>
{% endfor %}
</ul>
{% if user.id > 0 %}
    <form action="/comment_add/{{ image["code"] }}" method="post">
        <textarea name="text"></textarea>
        <input type="submit" value="Отправить">
    </form>
{% endif %}