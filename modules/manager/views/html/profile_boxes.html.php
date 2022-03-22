<?php defined('ABSPATH') or die;

$investor = $this->get_data('investor');

$activation_code = $investor->getActivationCode() ;

$owned_boxes = 0;
$reserved_boxes = 0;

if( !is_null($investor)) : ?>
<table class="coinbox-content full-width coinbox-product-bundle">
    <thead>
        <tr>
            <th colspan="4"><h2><?php echo AirBoxStringModel::__('Pedidos'); ?></h2></th>
            <th></th>
        </tr>
        <tr>
            <th><?php echo AirBoxStringModel::__('Boxes'); ?></th>
            <th><?php echo AirBoxStringModel::__('Fecha'); ?></th>
            <th><?php echo AirBoxStringModel::__('Tipo'); ?></th>
            <th><?php echo AirBoxStringModel::__('Cantidad'); ?></th>
            <th><?php echo AirBoxStringModel::__('Modo de pago'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($this->get_data('boxes') as $item ) : ?>
        <tr>
            <td><?php echo sprintf('<strong>[ %s ]</strong>',
                    $item[AirBoxOrderModel::FIELD_META_ID]); ?></td>
            <td><?php echo $item[AirBoxOrderModel::FIELD_META_DATE_CREATED]; ?></td>
            <td><?php
            
            echo AirBoxOrderModel::displayType($item[AirBoxOrderModel::FIELD_META_TYPE]);
            
            //recuento de boxes dependiendo del tipo
            if( $item[AirBoxOrderModel::FIELD_META_TYPE] > AirBoxOrderModel::ORDER_TYPE_RESERVED ){
                $owned_boxes += $item[AirBoxOrderModel::FIELD_META_AMOUNT];
            }
            else{
                $reserved_boxes += $item[AirBoxOrderModel::FIELD_META_AMOUNT];
                //incluir el link de activación
                
                if( !is_null($activation_code) ){
                    echo sprintf(' <a href="%s" target="_self">%s</a>',
                        AirBoxRouter::RouteAdmin( array(
                            AirBoxEventModel::EVENT_TYPE_COMMAND=>'activate',
                            AirBoxInvestorModel::FIELD_META_INVESTOR_KEY=>$item[AirBoxInvestorModel::FIELD_META_INVESTOR_KEY],
                            AirBoxOrderModel::FIELD_META_ID=>$item[AirBoxOrderModel::FIELD_META_ID],
                            AirBoxEventModel::EVENT_SELECTED_VIEW=>AirBoxManagerBootStrap::ADMIN_OPTION_INVESTOR_PROFILE,
                        )), AirBoxStringModel::__('Activar ahora?'));
                }
            }

            ?></td>
            <td><?php echo $item[AirBoxOrderModel::FIELD_META_AMOUNT]; ?></td>
            <td><?php echo AirBoxOrderModel::displayPaymentMethod(
                    $item[AirBoxOrderModel::FIELD_META_PAYMENT_METHOD]); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr><th colspan="5">Navegaci&oacute;n</th></tr>
    </tfoot>
</table>
<?php endif; ?>