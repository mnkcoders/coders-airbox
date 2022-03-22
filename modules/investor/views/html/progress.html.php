<?php defined('ABSPATH') or die;

$global_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);
$global_completed = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED,0);
$global_available = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);

$milestone_units = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_UNITS,0);
$milestone_completed = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_OWNED,0);

$milestone_id = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_CURRENT,0);
$stages = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_COUNT,0);

?>
<div class="content progress">
    <h2><?php
    
    echo AirBoxStringModel::__(AirBoxStringModel::LBL_TITLE_PROJECT_PROGRESS);
    
    ?></h2>
    <h4><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_PROJECT_GLOBAL); ?></h4>
    <?php echo AirBoxRenderer::renderProgressBar('global-progress', $global_units, $global_completed ); ?>
    <?php if( $milestone_completed < $milestone_units ) : ?>
    <h4><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_PROJECT_STAGE); ?></h4>
    <strong><?php echo AirBoxRenderer::renderProgressBar( 'milestone-progress',
            $milestone_units, $milestone_completed ); ?></strong>
    <?php endif; ?>
    <p><?php
        if ( $global_available ){
            echo AirBoxStringModel::__(AirBoxStringModel::LBL_PROJECT_AVAILABLE_UNITS);
            echo AirBoxRenderer::renderProgressBar( 'milestone-remaining',
                    $milestone_units, $milestone_units - $milestone_completed, 'units' );
        }
        else{
            echo AirBoxStringModel::__(AirBoxStringModel::LBL_PROJECT_STAGE_COMPLETED) ;
        }
    ?></p>
    <strong><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_MILESTONE); ?></strong>
    <?php echo AirBoxRenderer::renderProgressBar( 'stage-progress', $stages, $milestone_id ); ?>
</div>
