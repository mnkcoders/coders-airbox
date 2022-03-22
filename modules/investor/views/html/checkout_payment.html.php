<?php defined('ABSPATH') or die;
/**
 * Plantilla de forma de pago
 */

$checkout = $this->get_data( 'checkout' );

?>
<div class="tab-content half-width checkout">
    <?php if( !is_null($checkout ) ) : ?>
    <h2 class="title"><?php
        
        echo AirBoxStringModel::__( AirBoxStringModel::LBL_ORDER_PAYMENT_METHOD);
        
    ?></h2>
    <h4 class="sub-title"><?php
        
        echo sprintf('%s - <strong class="highlight">%s</strong>',
                AirBoxOrderModel::displayPaymentMethod( $checkout->getMethod()),
                $checkout->getLabel());
    ?></h4>
    <?php if( !is_null($checkout->getActionUrl()) ) : ?>
    <!-- ABRIR FORM DE COMPRA -->
    <form name="checkout-form <?php echo $checkout->getName(); ?>" action="<?php
    //destino del formulario (pasarela de pago)
    echo $checkout->getActionUrl(); ?>" method="post">
    <?php endif; ?>
    <ul class="content order-payment">
        <!-- INICIO información de pago -->
        <?php foreach( $checkout->getContent() as $content ) : ?>
        <?php if( !is_null($content)) : ?>
        <li><?php echo $content; ?></li>
        <?php endif; ?>
        <?php endforeach; ?>
        <!-- FIN información de pago -->
        <li><?php
    
        foreach( $checkout->getFormData() as $name=>$value ){
            //Preparar todos los campos ocultos de la pasarela de compra
            echo AirBoxRenderer::renderHidden($name, $value, $name );
        }
        
        if( $checkout->getBackButton()){
            //mostrar botón volver atrás si procede
            echo AirBoxRenderer::renderLink( AirBoxRouter::RoutePublic(),
                AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_BACK), 'button');
        }
        
        /**
         * Muestra el botón de ejecución del pago
         */
        echo $checkout->doCommitButton();

        ?></li>
    </ul>
    <?php if( !is_null($checkout->getActionUrl()) ) : ?>
    </form><!-- CERRAR FORM DE COMPRA -->
    <?php endif; ?>
    <?php else :?>
    <h2 class="title"><?php
    
    echo AirBoxStringModel::__(AirBoxStringModel::LBL_ERROR_INVALID_PAYMENT_METHOD);
    
    ?></h2>
    <?php endif; ?>
</div>
