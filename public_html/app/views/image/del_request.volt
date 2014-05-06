<h1>Запрос на удаление</h1>
<form action="/del_request/{{ this.dispatcher.getParam("code") }}" method="post">
    <textarea name="text"></textarea><br>
    <input type="submit" value="Отправить">
</form>