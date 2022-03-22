<?php defined('ABSPATH') or die;
/**
 * Vista de perfil del inversor
 */

$form = $this->get_data('profile_form');

?>
<ul class="content profile">
    <?php foreach( $form->getFormFields() as $field => $definition ) : ?>
    <li>
        <label for="id_<?php echo $field; ?>"><?php
        
        echo AirBoxStringModel::__($definition['label']);
        
        ?></label>
        <?php echo AirBoxRenderer::renderField($definition); ?>
    </li>
    <?php endforeach; ?>
    <li><?php echo AirBoxRenderer::renderSubmit( AirBoxEventModel::EVENT_TYPE_COMMAND,
            'update_profile', AirBoxStringModel::__('Actualizar perfil'));

    ?></li>
</ul>