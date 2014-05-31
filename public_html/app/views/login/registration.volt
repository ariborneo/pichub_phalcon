<h4>Регистрация</h4>
<form action="/registration" method="post">
    <table style="border: 0;">
        <tr><td width="100px"><b>Логин:</b></td><td><input type="text" name="name"></td></tr>
        <tr><td><b>Email:</b></td><td><input type="text" name="email"></td></tr>
        <tr><td><b>Пароль:</b></td><td><input type="password" name="password"></td></tr>
        <tr><td><b>Капча:</b></td><td><img src="/captcha" id="captcha"> <input type="text" name="captcha"></td></tr>
    </table>
    <input type="submit" value="Отправить">
</form>
<hr><a href="#" onclick="vk_login()">VK</a>