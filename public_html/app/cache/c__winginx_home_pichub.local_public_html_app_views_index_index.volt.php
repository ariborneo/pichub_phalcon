<form action="upload" method="post" enctype="multipart/form-data" style="border: #000000 1px solid;">
    <input type="file" name="Filedata" id="file" size="60" accept="image/*"><br>
    Альбом:
    <select name="album">
        <option value="0">-</option>
        <?php foreach ($albums as $album) { ?>
            <option value="<?php echo $album['id']; ?>"><?php echo $album['name']; ?></option>
        <?php } ?>
    </select><br>
    Описание:<br>
    <textarea name="opis"></textarea><br>
    <input type="submit" value="Загрузить" class="btn info" style="margin-top: 10px;">
</form>
<br>

<?php if ($user['id']) { ?>
    Имя пользователя: <a href="/user/<?php echo $user['name']; ?>"><?php echo $user['name']; ?></a><br>
    Ид пользователя: <?php echo $user['id']; ?><br>
    <a href="/logout">Выйти</a><br>
<?php } else { ?>
    <a href="/registration">Зарегистрироваться</a>
    <br><br>
    <form action="/login" method="post">
        <input type="text" name="name">
        <input type="password" name="password">
        <input type="submit" value="Войти">
    </form>
<?php } ?>