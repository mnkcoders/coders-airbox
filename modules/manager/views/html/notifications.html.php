<?php defined('ABSPATH') or die; ?>
<ul class="dashboard global-notifier">
    <li class="title"><?php echo AirBoxStringModel::__('Notificaciones'); ?></li>
    <li><input type="text" name="subject" placeholder="Asunto"/></li>
    <li><textarea name="message" placeholder="Contenido"></textarea></li>
    <li><button type="submit" name="send">Remitir notificaci&oacute;n</button></li>
</ul>
