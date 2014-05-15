<a href='#' onclick="vk_login()">VK</a><br><br>

<form action="registration" method="post" enctype="multipart/form-data">
    Логин <input type="text" name="name" size="30"><br>
    Email <input type="text" name="email" size="30"><br>
    Пароль <input type="password" name="password" size="30"><br>
    Капча <input type="button" value="reload" onclick="reload_captcha()"> <img src="/captcha" id="captcha"> <input type="text" name="captcha"><br>
    <input type="submit" value="Зарегистрироваться">
</form>