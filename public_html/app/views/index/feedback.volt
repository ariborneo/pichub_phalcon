<h4>Обратная связь</h4><br>
<form action="/feedback" method="post">
    <table style="border: 0;">
        <tr><td width="100px"><b>Имя:</b></td><td><input type="text" name="name" class="span4"></td></tr>
        <tr><td><b>Email:</b></td><td><input type="text" name="email" title="Указывайте верный email, т.к. на него придет ответ"></td></tr>
        <tr><td><b>Тема:</b></td><td><input type="text" name="subject"></td><td id="theme_result"></td></tr>
        <tr><td><b>Сообщение:</b></td><td><textarea name="text" rows="5"></textarea></td></tr>
        <tr><td><b>Капча:</b></td><td><img src="/captcha" id="captcha"> <input type="text" name="captcha"></td></tr>
    </table><br>
    <input type="submit" name="submit" value="Отправить сообщение">
</form>