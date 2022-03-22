<?php defined('ABSPATH') or die;
/**
 * Vista formulario de login
 * 
 * El visitante puede validarse en el sistema como inversor con sus datos de acceso
 * 
 * Retornar al form de login si no ha validado
 * 
 */

$formData = AirBoxRenderer::getInstance();

?>
<div class="tab-content login-form">
<?php wp_login_form(array(
    'value_username'=>!is_null($formData) ? $formData->get_data('user'): '',
    'redirect'=>AirBoxRouter::Route())); ?>
</div>