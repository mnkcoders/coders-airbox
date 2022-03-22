<?php defined('ABSPATH') or die;
/**
 * Vista formulario de login
 * 
 * El visitante puede validarse en el sistema como inversor con sus datos de acceso
 * 
 * Retornar al form de login si no ha validado
 * 
 */

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <div class="coinbox-header">
        <h2 class="title"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_TITLE_INVESTOR_ACCESS); ?></h2>
    </div>
    <div class="coinbox-content boxed">
        <?php AirBoxRenderer::renderMessages('coinbox-login-form'); ?>
        <?php AirBoxRenderer::getInstance()->getTemplate('login_form'); ?>
        <?php AirBoxRenderer::getInstance()->getTemplate('contact');?>
    </div>
</div>