<?php defined('ABSPATH') or die;
/**
 * Vista de logs y depuración
 */

$log_list = $this->get_data('log_file',array() );

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('notify');?>
    <h2 class="title"><?php
    
    echo AirBoxStringModel::__('Logs del sistema y depuraci&oacute;n' );
    
    ?></h2>
    <ul class="tool-box inline">
        <li><?php echo AirBoxRenderer::renderLink(
                AirBoxRouter::RouteAdmin(array(
                AirBoxEventModel::EVENT_SELECTED_VIEW => AirBoxManagerBootStrap::ADMIN_OPTION_LOGS,
                AirBoxEventModel::EVENT_TYPE_COMMAND => AirBoxManagerBootStrap::ADMIN_COMMAND_CLEAR_LOG)),
                AirBoxStringModel::__('Vaciar registro' ),'button');
        ?></li>
        <li><?php echo AirBoxRenderer::renderLink(
                AirBoxRouter::RouteAdmin(array(
                AirBoxEventModel::EVENT_SELECTED_VIEW => AirBoxManagerBootStrap::ADMIN_OPTION_LOGS)),
                AirBoxStringModel::__('Recargar' ),'button');
        ?></li>
        <li><!-- PLADEHOLDER --></li>
    </ul>
    <?php foreach( $log_list as $log ){ echo $log; } ?>
</div>