<?php defined('ABSPATH') or die;

/**
 * Vista de boxes adquiridos y reservados por el inversor
 * Es preciso haber cargado los boxes antes!!!
 */

/**
 * @var AirBoxInvestorModel Inversor validado en el sistema
 */
$investor = AirBoxRenderer::getInstance()->getModel();

$investor_airpoints = $investor->getAirPoints();

$investor_status = $this->get_data('status');

$order_data_price = AirBox::getOption('coinbox_cost',0);

//recuento de boxxes en propiedad y reservados, se incrementan en el loop del pedido
$owned_boxes = 0;
$reserved_boxes = 0;
$pending_orders = 0;
//resumen de valor de los boxes en posesión y reservados
$reserved_price = 0;
$owned_price = 0;
//extrae una lista de pedidos para mostrar
$order_list = $this->get_data('boxes');

?>
<div class="content cart">
    <form name="coinbox-purchase-form" method="post" action="<?php

    echo AirBoxRouter::RoutePublic();

    ?>">
        <table class="content orders">
            <thead>
                <tr>
                    <th><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_YOUR_BOX_AMOUNT);?></th>
                    <th></th>
                    <th ><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_AMOUNT);?></th>
                    <th ><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_YOUR_PRICE_AMOUNT);?></th>
                    <th ><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_DATE_ORDER);?></th>
                </tr>
            </thead>
            <tbody>
            <?php if( count($order_list) ) : ?>
            <?php foreach( $order_list as $order_data ) :
                
                    //boxes en propiedad son los que han sido ya adquiridos, los reservados no lo son
                    if( $order_data[AirBoxOrderModel::FIELD_META_TYPE] > AirBoxOrderModel::ORDER_TYPE_RESERVED ){
                        $owned_boxes += $order_data[AirBoxOrderModel::FIELD_META_AMOUNT];
                        $owned_price += $order_data[AirBoxOrderModel::FIELD_META_VALUE];
                    }
                    else{
                        $reserved_boxes += $order_data[AirBoxOrderModel::FIELD_META_AMOUNT];
                        $reserved_price += $order_data[AirBoxOrderModel::FIELD_META_VALUE];
                        $pending_orders++;

                        //guarda el pedido en reserva en caché para facilitar cargarlo en la siguiente vista
                        $this->set_data(
                                AirBoxOrderModel::FIELD_META_ID,
                                $order_data[AirBoxOrderModel::FIELD_META_ID]);
                    }

                    //imagen a mostrar para el logo del pedido
                    $status_img = $order_data[AirBoxOrderModel::FIELD_META_TYPE];

                    $status_desc = AirBoxOrderModel::displayType($order_data[AirBoxOrderModel::FIELD_META_TYPE]);

                    if( $order_data[AirBoxOrderModel::FIELD_META_TYPE] == AirBoxOrderModel::ORDER_TYPE_RESERVED &&
                            $order_data[AirBoxOrderModel::FIELD_META_PAYMENT_METHOD] > AirBoxOrderModel::ORDER_PAYMODE_NONE ){
                        //anexar sufijo a la imagen para mostrar progreso en el proceso de compra
                        $status_img .= '-pending';
                        //anexar al tag de la imagen la descripción adicional del estado
                        $status_desc .= sprintf(' (%s)',AirBoxStringModel::__(AirBoxStringModel::LBL_ORDER_STATUS_PROCESSING));
                    }
                ?>
                <tr>
                    <td><img class="coinbox-icon" alt="<?php echo $status_desc;
                    
                    ?>" title="<?php echo $status_desc; ?>" src="<?php

                    echo AirBoxRouter::Asset( sprintf('coinbox-icon-%s.png',$status_img));

                    ?>" /></td>
                    <td><!-- describir aqui las acciones disponibles en función del esmnkd del pedido-->
                        <?php if( $order_data[AirBoxOrderModel::FIELD_META_TYPE] == AirBoxOrderModel::ORDER_TYPE_RESERVED ) : ?>
                        <a class="command remove" href="<?php

                        echo AirBoxRouter::RoutePublic(array(
                            AirBoxEventModel::EVENT_SELECTED_VIEW=>AirBoxInvestorView::INVESTOR_MENU_DASHBOARD,
                            AirBoxEventModel::EVENT_TYPE_COMMAND=>AirBoxInvestorBootStrap::INVESTOR_OPTION_REMOVE_ORDER,
                            AirBoxOrderModel::FIELD_META_ID=>$order_data[AirBoxOrderModel::FIELD_META_ID]
                            ));

                        ?>" target="_self"><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_ORDER_CANCEL); ?></a>
                        <?php if( $order_data[AirBoxOrderModel::FIELD_META_PAYMENT_METHOD] > AirBoxOrderModel::ORDER_PAYMODE_NONE ) : ?>
                        <a class="command checkout" href="<?php
                        /**
                         * Muestra un enlace para proceder a ver el método de pago cuando este ya ha sido configurado
                         * en el pedido.
                         * 
                         * Alternativamente, el inversor puede volver a seleccionar otro método de pago en el formulario de compra.
                         * 
                         * De este modo se provee acceso de nuevo al método de pago seleccionado para continuar (transferenca, etc)
                         */
                        echo AirBoxRouter::RoutePublic(array(
                            AirBoxEventModel::EVENT_SELECTED_VIEW=>AirBoxInvestorView::INVESTOR_MENU_DASHBOARD,
                            AirBoxEventModel::EVENT_TYPE_COMMAND=>AirBoxInvestorBootStrap::INVESTOR_COMMAND_PURCHASE,
                            AirBoxOrderModel::FIELD_META_ID=>$order_data[AirBoxOrderModel::FIELD_META_ID],
                            AirBoxOrderModel::FIELD_META_PAYMENT_METHOD=>$order_data[AirBoxOrderModel::FIELD_META_PAYMENT_METHOD]
                            ));

                        ?>" target="_self"><?php

                        echo AirBoxStringModel::__(AirBoxStringModel::LBL_ORDER_REVIEW);

                        ?></a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td><strong class="bundle-amount"><?php

                    echo $order_data[AirBoxOrderModel::FIELD_META_AMOUNT];

                    ?></strong></td>
                    <td>
                        <span class="value"><?php

                        echo sprintf('%s €', $order_data[AirBoxOrderModel::FIELD_META_VALUE] ) ;

                        ?></span>

                        <?php if( $order_data[AirBoxOrderModel::FIELD_META_AIRPOINTS] > 0 ) : ?>
                        <span class="airpoints"><?php

                        echo sprintf(__(' +%s AP'),$order_data[AirBoxOrderModel::FIELD_META_AIRPOINTS]);

                        ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php

                    echo $order_data[AirBoxOrderModel::FIELD_META_DATE_CREATED];

                    ?></td>
                </tr>
            <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5"><strong><?php
                    
                    echo AirBoxStringModel::__(AirBoxStringModel::LBL_INFO_BOX_RESERVATION_TIP);
                    
                    ?></strong></td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
                <?php if( count($order_list) ): ?>
                <!-- SOLO MOSTRAR CUANDO HAY PEDIDOS QUE MOSTRAR -->
                <tr class="order-summary">
                    <th colspan="3" style="text-align: right;"><?php echo ( $reserved_boxes > 0 ) ?
                         sprintf('<strong class="amount total">%s ( +%s )</strong> %s',
                                 $owned_boxes,$reserved_boxes,
                                 AirBoxStringModel::__(AirBoxStringModel::LBL_BOX_AMOUNT)) :
                         sprintf('<strong class="amount total">%s</strong> %s',
                                 $owned_boxes,
                                 AirBoxStringModel::__(AirBoxStringModel::LBL_BOX_AMOUNT ) );
                    ?></th>
                    <th colspan="2" style="text-align: left;"><?php echo $reserved_price > 0 ?
                            sprintf('<strong class="amount price">%s + ( %s )</strong> €',
                                    $owned_price,$reserved_price) :
                            sprintf('<strong class="amount price">%s</strong> €',
                                    $owned_price);
                    ?></th>
                </tr>
                <?php endif; ?>
                <?php if( $pending_orders === 0 ) : ?>
                <!-- SOLO MOSTRAR EL FORM DE RESERVA CUANDO NO HAY PEDIDO RESERVADO -->
                <tr class="add-to-cart-form">
                    <td colspan="2">
                        <?php echo AirBoxRenderer::renderSubmit(
                            AirBoxEventModel::EVENT_TYPE_COMMAND,
                            AirBoxInvestorBootStrap::INVESTOR_COMMAND_ADD_TO_CART,
                            AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_ORDER),'left'); ?>
                    </td>
                    <td>
                        <!-- UNIDADES -->
                        <?php echo AirBoxRenderer::renderNumber(
                            AirBoxOrderModel::FIELD_META_AMOUNT, 1,1,
                            AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE)); ?>
                    </td>
                    <td>
                        <input type="hidden" id="id_box_price" value="<?php echo $order_data_price; ?>" />
                        <h4 class="highlight">+<span  id="id_order_price"><?php echo $order_data_price; ?></span> €</h4>
                    </td>
                    <td>
                    <?php if( $investor_airpoints) : ?>
                    <!-- consumir airpoints si dispone de los suficientes -->
                        <?php echo AirBoxRenderer::renderCheckBox( 'airpoint_exchange', false); ?>
                        <label for="id_<?php echo 'airpoint_exchange'; ?>"><?php
                            
                            echo sprintf(
                                    AirBoxStringModel::__(AirBoxStringModel::LBL_INFO_INVEST_AIRPOINTS),
                                    sprintf('<strong>%s</strong>',$investor_airpoints));
                        ?></label>
                    <?php endif; ?>
                    </td>
                </tr>
                <?php elseif( $pending_orders > 1 ) : ?>
                <!-- NO MOSTRAR FORM DE RESERVA SI HAY PEDIDOS RESERVADOS, MOSTRAR MENSAJE PARA VACIAR PEDIDOS -->
                <tr class="warning"><th colspan="5"><?php
                
                echo AirBoxStringModel::__(AirBoxStringModel::LBL_ADVICE_SINGLE_ORDER_ALLOWED);
                
                ?></th></tr>
                <?php endif; ?>
            </tfoot>
        </table>
    </form>
    <script type="text/javascript">
        /**
         * Es una copia del script cargado para el widget. Con algo mas de tiempo debería ser colocado
         * en la cabecera.
         * Actualiza el precio de coste del box durante el proceso de compra en la etiqueta
         * del form de afiliación
         */
        jQuery( '.add-to-cart-form #id_amount' ).on( 'change' , function( e ){

            //capturar precio base obtenido de un campo oculto
            var base_price = jQuery('#id_box_price').val();
            //capturar valor seleccionado
            var amount = jQuery( this ).val();
            //actualizar precio coste
            jQuery('#id_order_price').html( amount * base_price );
        });
    </script>
</div>