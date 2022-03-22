<?php defined('ABSPATH') or die;

//lista de objetivos
//$milestones = $this->get_data('milestones');
$milestones = AirBoxMilestoneModel::LoadMilestones();
//unidades en propiedad ( no confundir con unidades asignadas/no asignadas a objetivos)
$owned_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED,0);
//inversores activos
$active_investors = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_INVESTORS,0);
//para ir decrementando y reasignando resultados
$consumed_units = $owned_units;
//recuento de unidades asignadas (se calcula directamente al iterar sobre la lista de objetivos)
$goal_units = 0;
?>
<table class="coinbox-table full-width milestones">
    <thead>
        <tr>
            <th><?php echo AirBoxStringModel::__('Etapa'); ?></th>
            <th><?php echo AirBoxStringModel::__('Objetivo'); ?></th>
            <th><?php echo AirBoxStringModel::__('Progreso'); ?></th>
            <th><?php echo AirBoxStringModel::__('Fecha'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if( count($milestones) ) : ?>
        <?php for( $i=0 ; $i < count($milestones) ; $i++ ) : ?>
        <tr>
            <td>
                <strong>[ <?php echo $milestones[$i]->getId(); ?> ]</strong>
                <?php if( $i === count($milestones)-1 ): ?>
                <a href="<?php 
                
                echo AirBoxRouter::RouteAdmin(array(
                    AirBoxEventModel::EVENT_SELECTED_VIEW=>  AirBoxManagerView::VIEW_LAYOUT_MILESTONES,
                    AirBoxEventModel::EVENT_TYPE_COMMAND => AirBoxManagerBootStrap::ADMIN_OPTION_REMOVE_MILESTONE,
                    AirBoxMilestoneModel::FIELD_META_ID => $milestones[$i]->getId(),
                ));
                
                ?>" target="_self"><?php echo AirBoxStringModel::__('Borrar'); ?></a>
                <?php endif; ?>
            </td>
            <td><?php echo sprintf('<strong>%s</strong> %s',
                    $milestones[$i]->getGoal(),
                    AirBoxStringModel::__(AirBoxStringModel::LBL_BOX_AMOUNT) );
            ?></td>
            <td><span class="status status-<?php echo $milestones[$i]->getStatus(); ?>"><?php
            /**
             * A cada objetivo, calcular la diferencia de unidades asignadas y las unidades en propiedad
             * a fin de poder representar un informe del estado real del objetivo.
             */
            $goal_units += $milestones[$i]->getGoal();

            if( $consumed_units - $milestones[$i]->getGoal() >= 0 ){

                //objetivo alcanzado
                echo AirBoxRenderer::renderProgressBar('completed',100,100,'progress');

                //deducir las unidades cubiertas en cada objetivo a fin de calcular las siguientes etapas
                $consumed_units = $consumed_units - $milestones[$i]->getGoal() > 0 ?
                        $consumed_units - $milestones[$i]->getGoal() : 0;
            }
            elseif( $consumed_units < $milestones[$i]->getGoal() ){
                //objetivo/etapa/milestone en curso
                echo AirBoxRenderer::renderProgressBar('current',
                        $milestones[$i]->getGoal(), $consumed_units,'progress');

                $consumed_units = 0;
            }
            //aqui sería posible calcular en función de la fecha límite para saber si el estadp es expirado (otra versión)
            else{
                //objetivo sin alcanzar
                echo AirBoxRenderer::renderProgressBar('disabled',100,0,'progress' );
            }

            ?></span></td>
            <td>
                <?php if( $milestones[$i]->getStatus() == AirBoxMilestoneModel::STAGE_STATUS_COMPLETED ) : ?>
                <strong class="success"><?php echo substr($milestones[$i]->getDateCompleted(),0,10); ?></strong>
                <?php elseif( $milestones[$i]->getStatus() == AirBoxMilestoneModel::STAGE_STATUS_EXPIRED ) : ?>
                <strong class="error"><?php echo $milestones[$i]->getDateLimit(); ?></strong>
                <?php elseif( $milestones[$i]->getStatus() == AirBoxMilestoneModel::STAGE_STATUS_INACTIVE ): ?>
                <i class="advice"><?php echo $milestones[$i]->getDateLimit(); ?></i>
                <?php else: ?>
                <strong><?php echo $milestones[$i]->getDateLimit(); ?></strong>
                <?php endif; ?>
            </td>
        </tr>
        <?php endfor; ?>
        <?php else : ?>
        <tr><td colspan="4"><?php echo AirBoxStringModel::__('Ahora puedes definir objetivos para el proyecto. Crea tu primer objetivo!'); ?></td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td><?php
            //unidades asignadas a un objetivo
            echo AirBoxStringModel::__compose(
                AirBoxStringModel::__('<strong>%s</strong> unidades'),
                $goal_units );

            ?></td>
            <td><?php echo AirBoxStringModel::__( 'Progreso global'); ?></td>
            <td><?php

                echo AirBoxRenderer::renderProgressBar( 'overall',
                    $goal_units, $owned_units, 'percent' );

            ?></td>
            <td></td>
        </tr>
        <tr>
            <td><?php
            
            if( count($milestones) ){
                //mostrar botón de reset de la lista de objetivos
                echo AirBoxRenderer::renderSubmit( AirBoxEventModel::EVENT_TYPE_COMMAND,
                        AirBoxManagerBootStrap::ADMIN_OPTION_CLEAR_MILESTONES,
                        AirBoxStringModel::__('Vaciar tabla de objetivos') );
            }
                
            ?></td>
            <th colspan="3"><?php
            
            echo sprintf(__('%s unidades totales adquiridas para %s inversores activos en el proyecto'),
                    $owned_units,
                    $active_investors);
            
            ?></th>
        </tr>
    </tfoot>
</table>