<?php defined('ABSPATH') or die;

$ab_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,100);

?>
<ul class="content simulation">
    <li>
        <label for="id_<?php echo AirBoxOrderModel::FIELD_META_AMOUNT; ?>"><?php
        
        echo AirBoxStringModel::__( AirBoxStringModel::LBL_BOX_AMOUNT );
        
        ?></label>
        <?php echo AirBoxRenderer::renderNumber( AirBoxOrderModel::FIELD_META_AMOUNT, $ab_units, 1, $ab_units ); ?>
    </li>
    <li>
        <label for="id_hours"><?php echo AirBoxStringModel::__( AirBoxStringModel::LBL_TIME_AMOUNT); ?></label>
        <?php echo AirBoxRenderer::renderNumber( 'hours', 3, 1, 8 ); ?>
    </li>
    <li>
        <label for="id_price"><?php echo AirBoxStringModel::__( AirBoxStringModel::LBL_PRICE_AMOUNT ); ?></label>
        <?php echo AirBoxRenderer::renderNumber( 'price', 3.5, 1, 100 ); ?>
        <?php echo AirBoxRenderer::renderSubmit(
                AirBoxEventModel::EVENT_TYPE_COMMAND,
                'simulation',
                AirBoxStringModel::__( AirBoxStringModel::LBL_BUTTON_SIMULATION ) ) ?>
    </li>
</ul>