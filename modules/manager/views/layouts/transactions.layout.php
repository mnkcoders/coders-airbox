<?php defined('ABSPATH') or die; ?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <h2 class="title"><?php
    
    echo AirBoxStringModel::__('Transacciones' );
    
    ?></h2>
    <?php AirBoxRenderer::getInstance()->getTemplate('notify');?>
    <?php AirBoxRenderer::getInstance()->getTemplate('transactions');?>
</div>