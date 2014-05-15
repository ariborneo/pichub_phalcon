Логин: {{ user["login"] }}<br>
Дата региастрации: {{ user["time"] }}<br>
id vk:
{% if user["vk_id"] is defined %}
    <a href="http://vk.com/id{{ user["vk_id"] }}" target="_blank">{{ user["vk_id"] }}</a>
{% else %}
    <script>
        function vk_login(){
            var vk_app_id = 4357987;
            var redirect_uri = 'http://pichub.local/login_vk';
            window.location.href = "https://oauth.vk.com/authorize?client_id="+vk_app_id+"&scope=offline&redirect_uri="+redirect_uri+"&display=page&response_type=code";
        }
    </script>
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
    <a href="/show/{{ image["code"] }}" target="_blank"><img src="{{ image["path"] }}"></a>&nbsp;
{% endfor %}