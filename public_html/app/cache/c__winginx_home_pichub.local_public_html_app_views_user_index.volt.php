Логин: <?php echo $user['login']; ?><br>
Дата региастрации: <?php echo $user['reg_time']; ?><br>

<a href="/create_album">Создать альбом</a><br>

<br>Альбомы:<br>
<?php foreach ($albums as $album) { ?>
    <a href="/album/<?php echo $album['id']; ?>" target="_blank"><?php echo $album['name']; ?></a><br>
<?php } ?>

<br>Изображения:<br>
<?php foreach ($images as $image) { ?>
    <a href="/show/<?php echo $image['code']; ?>" target="_blank"><img src="/<?php echo $image['path']; ?>"></a>&nbsp;
<?php } ?>