<?php defined('ABSPATH') or die; ?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    
    <?php AirBoxRenderer::getInstance()->getTemplate('notify');?>
    
    <h2 class="title"><?php
    
    echo AirBoxStringModel::__('Ciclos y objetivos de proyecto' );
    
    ?></h2>
    <?php AirBoxRenderer::getInstance()->getTemplate('milestone_table');?>
    <?php AirBoxRenderer::getInstance()->getTemplate('milestone_form');?>
</div>