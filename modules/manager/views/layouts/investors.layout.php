<?php defined('ABSPATH') or die;


?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('notify');?>
    <h2 class="title"><?php
    
    echo AirBoxStringModel::__('Directorio de afiliados' );
    
    ?></h2>
    <?php AirBoxRenderer::getInstance()->getTemplate('investors');?>
</div>