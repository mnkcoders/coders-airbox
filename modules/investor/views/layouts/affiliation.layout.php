<?php defined('ABSPATH') or die;
/**
 * Vista formulario de registro de invitado visitante
 * 
 * Formulario de afiliación de demo, el inversor ve una maqueta del formulario, pero este
 * no es funcional
 * 
 */
?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <div class="coinbox-header">
        <h2 class="title"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_TITLE_AFFILIATION_FORM);
        
        ?></h2>
    </div>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <?php AirBoxRenderer::getInstance()->getTemplate('affiliate_form'); ?>
    </div>
</div>