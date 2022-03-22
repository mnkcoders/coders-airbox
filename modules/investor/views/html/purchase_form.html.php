<?php defined('ABSPATH') or die;
/**
 * Vista de opciones de pago y procesar checkout
 * 
 * Si el método de pago del pedido en activo corresponde a la reinversión completa de AirPoints
 * no muestra métodos de pago, tan solo muestra un mensaje de la reinversión.
 * 
 * En este punto, el pedido puede ser eliminado y los airpoints se retornan permitiendo
 * esta vez cancelar la compra.
 */
$order_data = $this->get_data( 'cart' );

if( !is_null($order_data) ) : ?>
<div class="content cart">
    <form name="coinbox-purchase-form" method="post" action="<?php echo AirBoxRouter::RoutePublic(); ?>">
        <?php echo AirBoxRenderer::renderHidden(AirBoxOrderModel::FIELD_META_ID, $order_data->getId() ); ?>
        <?php if( $order_data->getPaymentMethod() != AirBoxOrderModel::ORDER_PAYMODE_AIRPOINTS ) : ?>
        <ul class="coinbox-payment-form inline">
            <li class="full-width"><strong><?php
            
            echo AirBoxStringModel::__(AirBoxStringModel::LBL_ORDER_SELECT_PAYMENT);
            
            ?></strong></li>
            <!-- mostrar información de CC y motivo del pago - si son airpoints, mostrar solo si hay airpoints y el inversor està activo -->
            <?php foreach( AirBoxCheckoutModel::ListGateways() as $method => $gateway ) : ?>
            <?php if( $method !== AirBoxOrderModel::ORDER_PAYMODE_AIRPOINTS ) : ?>
            <li class="width-1-3">
                <input id="id_<?php echo $gateway; ?>" type="radio" name="<?php
                echo AirBoxOrderModel::FIELD_META_PAYMENT_METHOD; ?>" value="<?php echo $method; ?>" />
                <label class="payment-gateway <?php echo $gateway; ?>" for="id_<?php echo $gateway; ?>">
                <?php echo AirBoxOrderModel::displayPaymentMethod( $method ); ?></label>
            </li>
            <?php endif ; ?>
            <?php endforeach; ?>
        </ul>
        <?php else : ?>
        <h4 class="highlight"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_ORDER_PAYMETHOD_AIRPOINTS);
        
        ?></h4>
        <?php endif; ?>
        <ul class="coinbox-purchase-form inline">
            <li class="width-2-3"><a href="<?php echo AirBox::getOption('coinbox_contract_link','#' );
            
            ?>" target="_blank">* <?php 

            echo AirBoxStringModel::__( AirBoxStringModel::LBL_INFO_TERMS_AND_CONDITIONS );

            ?></a></li>
            <li class="width-1-3"><?php echo AirBoxRenderer::renderSubmit(
                    AirBoxEventModel::EVENT_TYPE_COMMAND,
                    AirBoxInvestorBootStrap::INVESTOR_COMMAND_PURCHASE,
                    AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_CHECKOUT),
                    'intense'); ?>
            </li>
        </ul>
    </form>
</div>
<?php endif; ?>