<?php defined('ABSPATH') or die;

$total_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);

$unit_pack = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED,0);

$available_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);

$reserved_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_RESERVED,0);

$milestone_units = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_UNITS,0);

$milestone_completed = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_OWNED,0);

$milestone_id = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_CURRENT,0);

$global_current_profit = AirBox::cache( AirBoxCacheModel::CACHE_PROFIT_UNITS,0);

$global_units_profit = AirBox::cache(AirBoxCacheModel::CACHE_PROFIT_GLOBAL,0);

$milestone_units_profit = AirBox::cache(AirBoxCacheModel::CACHE_PROFIT_MILESTONE_UNITS,0);

$milestone_current_profit = AirBox::cache(AirBoxCacheModel::CACHE_PROFIT_MILESTONE_COMPLETED,0);

?>
<ul class="dashboard progress">
    <li class="title"><?php echo AirBoxStringModel::__('Progreso del proyecto'); ?></li>
    <li><?php echo AirBoxRenderer::renderProgressBar( 'project_progress',
            $total_units, $unit_pack); ?></li>
    <li><?php

    echo sprintf('%s: <strong class="profit-current">%s</strong> / <strong class="profit-goal">%s</strong> €',
            AirBoxStringModel::__('Financiaci&oacute;n global'), $global_current_profit, $global_units_profit ); ?></li>
    <li><?php echo AirBoxRenderer::renderProgressBar( 'milestone_progress',
            $milestone_units,$milestone_completed,'units'); ?></li>
    <li><?php

    echo sprintf('%s: <strong class="profit-current">%s</strong> / <strong class="profit-goal">%s</strong> €',
            AirBoxStringModel::__('Objetivo en curso'), $milestone_current_profit, $milestone_units_profit); ?></li>

    <li><hr/></li>
    
    <li><?php echo sprintf('<a href="%s" target="_self"><strong>%s</strong> %s</a>',
            AirBoxRouter::RouteAdmin(array(
                AirBoxEventModel::EVENT_SELECTED_VIEW => AirBoxManagerView::VIEW_LAYOUT_BOXES,
                AirBoxOrderModel::FIELD_META_TYPE => AirBoxOrderModel::ORDER_TYPE_PURCHASED,
            )), $unit_pack, AirBoxStringModel::__('Boxes vendidos') );
    ?></li>
    <?php if( $reserved_units > 0 ) : ?>
    <li><?php echo sprintf('<a href="%s" target="_self"><strong>%s</strong> %s</a>',
            AirBoxRouter::RouteAdmin(array(
                AirBoxEventModel::EVENT_SELECTED_VIEW => AirBoxManagerView::VIEW_LAYOUT_BOXES,
                AirBoxOrderModel::FIELD_META_TYPE => AirBoxOrderModel::ORDER_TYPE_RESERVED,
            )), $reserved_units, AirBoxStringModel::__('Boxes reservados') );
    ?></li>
    <?php endif; ?>
    <li><?php echo sprintf('<strong>%s</strong> %s', $available_units,
                AirBoxStringModel::__(' Boxes disponibles')); ?></li>
</ul>
