<?php defined('ABSPATH') or die;
/**
 * Plantilla de resumen de pedido
 */

$checkout = $this->get_data( 'checkout' );

?>
<div class="tab-content half-width order-review">
    <h2 class="title"><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_TITLE_CHECKOUT); ?></h2>
    <?php if( !is_null($checkout) ) : ?>
    <h4 class="sub-title"><?php echo AirBoxStringModel::__compose(
            AirBoxStringModel::LBL_CHECKOUT_ORDER_DETAIL,
            sprintf('<span class="highlight">%s</span>',$checkout->getOrderId() ) );
    ?></h4>
    <ul class="content order-review">
        <li>
            <label><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_AMOUNT); ?></label>
            <strong class="highlight right"><?php echo $checkout->getOrderSummary(); ?></strong>
        </li>
        <li>
            <label><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_PRICE_AMOUNT); ?></label>
            <strong class="highlight right"><?php echo $checkout->getOrderValue(); ?>€</strong>
        </li>
        <?php if( $checkout->getAirPoints() > 0 ) : ?>
        <li class="highlight">
            <label><?php

                echo AirBoxStringModel::__(AirBoxStringModel::LBL_CHECKOUT_DISCOUNT);

            ?>:</label>
            <strong class="airpoints right"><?php

            echo sprintf('%s %s',$checkout->getAirPoints(),AirBoxStringModel::LBL_AIRPOINTS_AMOUNT);

            ?></strong>
        </li>
        <?php endif; ?>
    </ul>
    <?php else : ?>
        <h4 class="sub-title"><?php echo AirBoxStringModel::__(
            AirBoxStringModel::LBL_ORDER_SELECT_PAYMENT);
        ?></h4>
        <?php echo AirBoxRenderer::renderLink(
                AirBoxRouter::RoutePublic(), 
                AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_BACK),
                'button'); ?>
    <?php endif; ?>
</div>
