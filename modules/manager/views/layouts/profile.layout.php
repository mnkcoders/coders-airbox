<?php defined('ABSPATH') or die;

$investor = $this->get_data('investor'); //$this->getModel();

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('notify');?>
    <h2 class="title"><?php
    
    echo AirBoxStringModel::__('Directorio de afiliados' );
    
    ?></h2>
    <?php AirBoxRenderer::getInstance()->getTemplate('profile_info');?>
    <?php AirBoxRenderer::getInstance()->getTemplate('profile_status');?>
    <?php AirBoxRenderer::getInstance()->getTemplate('profile_boxes');?>
    <?php AirBoxRenderer::getInstance()->getTemplate('profile_transactions');?>
</div>

