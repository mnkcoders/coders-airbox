<?php defined('ABSPATH') or die;
/**
 * Vista de checkout con opcion de pago seleccionada
 */
//modelo (inversor)
$investor = AirBoxRenderer::getInstance()->getModel();

$order_list = AirBoxOrderModel::LoadReservedOrders($investor);

if( count($order_list) > 0 ){
    $order = $order_list[ 0 ];
    $this->set_data( 'checkout',AirBoxCheckoutModel::LoadCheckOut( $order_list[ 0 ] ) );
}

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('header');?>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <?php if( isset($order) ) : ?>
            <?php AirBoxRenderer::getInstance()->getTemplate('checkout_order');?>
            <?php AirBoxRenderer::getInstance()->getTemplate('checkout_payment');?>
        <?php else: ?>
        <div class="tab-content full-width checkout-error">
            <h2><?php
            
            echo AirBoxStringModel::__(AirBoxStringModel::LBL_ERROR_INVALID_ORDER_ID); ?></h2>
            <?php  echo AirBoxRenderer::renderLink( AirBoxRouter::RoutePublic(),
                AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_BACK), 'button'); ?>
        </div>
        <?php endif; ?>
    </div>
</div>