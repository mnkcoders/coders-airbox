<?php defined('ABSPATH') or die;
/**
 * Notificación de errores
 */

$messages = AirBoxNotifyModel::getLogs(AirBoxNotifyModel::LOG_TYPE_WARNING);

?>
<?php if(count($messages) ) : ?>
<ul class="output-log">
<?php foreach( $messages as $content ) : ?>
    <li class="log-type-<?php echo $content->getType(); ?>"><?php
    
    echo $content->getMessage() ;
    
    ?>
    <?php if( !is_null($content->getContext())) : ?>
        <strong><?php echo $content->getContext(); ?></strong>
    <?php endif; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>