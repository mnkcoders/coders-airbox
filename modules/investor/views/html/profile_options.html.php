<?php defined('ABSPATH') or die;
/**
 * Vista de perfil del inversor
 */
?>
<ul class="profile-options inline">
    <li class="full-width"><h4><?php echo AirBoxStringModel::__('Otras opciones');?></h4></li>
    <li><?php echo AirBoxRenderer::renderLink( AirBoxRouter::RoutePublic(array(
                AirBoxEventModel::EVENT_SELECTED_VIEW => 'cancel_account',
            )), AirBoxStringModel::__('Solicitar cancelaci&oacute;n de mi cuenta'),'button'); ?></li>
    <li><?php echo AirBoxRenderer::renderLink( AirBoxRouter::RoutePublic(array(
                AirBoxEventModel::EVENT_SELECTED_VIEW => 'otra_opcion',
            )), AirBoxStringModel::__('Otra opci&oacute;n'),'button'); ?></li>
</ul>