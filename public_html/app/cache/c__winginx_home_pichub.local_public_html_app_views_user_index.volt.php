Логин: <?php echo $user['login']; ?><br>
Дата региастрации: <?php echo $user['reg_time']; ?><br>
<br>Изображения:<br>
<?php foreach ($images as $image) { ?>
    <a href="/show/<?php echo $image['code']; ?>" target="_blank"><img src="/<?php echo $image['path']; ?>"></a>&nbsp;
<?php } ?>