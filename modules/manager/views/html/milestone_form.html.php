<?php defined('ABSPATH') or die;

//unidades totales definidas en el proyecto
$total_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);

//unidades en propiedad ( no confundir con unidades asignadas/no asignadas a objetivos)
$owned_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED,0);

//fecha límite por defecto para el proyecto (para delimitar las fechas de las etapas generadas)
$termination_date = AirBox::getOption('termination_date');

?>
<form name="coinbox-new-milestone" action="<?php

    echo AirBoxRouter::RouteAdmin(array(
        AirBoxEventModel::EVENT_SELECTED_VIEW=>AirBoxManagerView::VIEW_LAYOUT_MILESTONES));

?>" method="post">
    <ul class="coinbox-content non-assigned-units">
        <li>
            <label for="id_goal"><?php

            echo AirBoxStringModel::__(
                    'Definir nuevo objetivo:');

            ?></label>
            <?php echo AirBoxRenderer::renderNumber(
                AirBoxMilestoneModel::FIELD_META_GOAL, 1, 1 ) ?>
        </li>
        <li>
            <label for="id_date_limit"><?php echo AirBoxStringModel::__(
                    'Fecha l&iacute;mite'); ?></label>
            <?php echo AirBoxRenderer::renderDate(
                    AirBoxMilestoneModel::FIELD_META_DATE_LIMIT,
                    AirBox::getOption('termination_date')) ?>
        </li>
        <li>
            <?php echo AirBoxRenderer::renderSubmit(AirBoxEventModel::EVENT_TYPE_COMMAND,
                AirBoxManagerBootStrap::ADMIN_OPTION_SET_MILESTONE,
                AirBoxStringModel::__('Establecer nuevo objetivo'), 'button-primary' ); ?>
        </li>
    </ul>
</form>
