<?php defined('ABSPATH') or die;

    $box_list = $this->get_data('boxes');

    $box_price = AirBox::getOption('coinbox_cost',1);
    
    $total_boxes = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,10);

    $reserved_boxes = 0;
    
    $owned_boxes = 0;
    
    $reserved_value = 0;
    $owned_value = 0;
    
?>
<table class="coinbox-table full-width boxes">
    <thead>
        <tr>
            <th><?php echo AirBoxStringModel::__('Pedido'); ?></th>
            <th colspan="2"><?php echo AirBoxStringModel::__('Inversor'); ?></th>
            <th><?php echo AirBoxStringModel::__('Cantidad'); ?></th>
            <th><?php echo AirBoxStringModel::__('Invertido'); ?></th>
            <th><?php echo AirBoxStringModel::__('Tipo'); ?></th>
            <th><?php echo AirBoxStringModel::__('Modo de pago'); ?></th>
            <th><?php echo AirBoxStringModel::__('Fecha'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $box_list as $stage ) : ?>
        <tr>
            <td><strong>[ <?php echo $stage[AirBoxOrderModel::FIELD_META_ID]; ?> ]</strong></td>
            <td><a class="investor-name" href="<?php

            echo AirBoxRouter::RouteAdmin(array(
                AirBoxEventModel::EVENT_SELECTED_VIEW=>'profile',
                AirBoxInvestorModel::FIELD_META_ID=>$stage[AirBoxOrderModel::FIELD_META_OWNER_ID]
            ));

            ?>" target="_self"><?php

            echo $stage[AirBoxInvestorModel::FIELD_META_AFFILIATE];

            ?></a></td>
            <td><span class="document-id" ><?php
            
            echo $stage[AirBoxInvestorModel::FIELD_META_DOCUMENT_ID];
            
            ?></span></td>
            <td><span class="amount"><?php

            if( $stage[AirBoxOrderModel::FIELD_META_TYPE] > AirBoxOrderModel::ORDER_TYPE_RESERVED ){
                $owned_boxes += $stage[AirBoxOrderModel::FIELD_META_AMOUNT];
            }
            else{
                $reserved_boxes += $stage[AirBoxOrderModel::FIELD_META_AMOUNT];
            }

            echo $stage[AirBoxOrderModel::FIELD_META_AMOUNT];
            
            ?></span></td>
            <td><span class="price"><?php
            
                if( $stage[AirBoxOrderModel::FIELD_META_TYPE] > AirBoxOrderModel::ORDER_TYPE_RESERVED ){
                    $owned_value += $stage[AirBoxOrderModel::FIELD_META_VALUE];
                }
                else{
                    $reserved_value += $stage[AirBoxOrderModel::FIELD_META_VALUE];
                }
            
                echo number_format( $stage[AirBoxOrderModel::FIELD_META_VALUE],2,',','.');
                
                ?> €</span>
                <?php if( $stage[AirBoxOrderModel::FIELD_META_AIRPOINTS] > 0 ) : ?>
                    
                <span class="airpoints"><?php
                
                echo sprintf(' ( + %s AP )',$stage[AirBoxOrderModel::FIELD_META_AIRPOINTS] );
                
                ?></span>
                
                <?php endif; ?>
            </td>
            <td>
                <span class="type type-<?php echo $stage[AirBoxOrderModel::FIELD_META_TYPE]; ?>"><?php

                echo AirBoxOrderModel::displayType( $stage[AirBoxOrderModel::FIELD_META_TYPE] ); ?></span>
                <?php if( $stage[AirBoxOrderModel::FIELD_META_TYPE] == AirBoxOrderModel::ORDER_TYPE_RESERVED ) : ?>
                <a href="<?php

                echo AirBoxRouter::RouteAdmin(array(
                    AirBoxEventModel::EVENT_TYPE_COMMAND=>'activate',
                    AirBoxInvestorModel::FIELD_META_INVESTOR_KEY=>$stage[AirBoxInvestorModel::FIELD_META_INVESTOR_KEY],
                    AirBoxOrderModel::FIELD_META_ID=>$stage[AirBoxOrderModel::FIELD_META_ID],
                    AirBoxEventModel::EVENT_SELECTED_VIEW=>'boxes',
                ));

                ?>"><?php echo AirBoxStringModel::__('Activar ahora?'); ?></a>
                <?php endif; ?>
            </td>
            <td><span class="payment-method"><?php
            
            echo AirBoxOrderModel::displayPaymentMethod( $stage[AirBoxOrderModel::FIELD_META_PAYMENT_METHOD] );
            
            ?></span></td>
            <td><span class="date"><?php
            
            echo $stage[AirBoxOrderModel::FIELD_META_DATE_CREATED];
            
            ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <?php if( $owned_boxes > 0 ) : ?> 
        <tr>
            <th colspan="2"></th>
            <th><?php echo AirBoxStringModel::__('Adquiridos'); ?></th>
            <th><?php echo $owned_boxes; ?></th>
            <th><?php echo sprintf('%s €',$owned_value);  ?></th>
            <th colspan="3">
                <h4><?php echo AirBoxStringModel::__('Objetivo de proyecto'); ?>
                <strong class="highlight"><?php echo sprintf('%s € ( %s%% financiado )',
                        $total_boxes * $box_price,
                        number_format($owned_value / ($total_boxes * $box_price) * 100,1));
                ?></strong>
                </h4>
            </th>
        </tr>
        <?php endif; ?>
        <?php if( $reserved_boxes > 0 ) : ?> 
        <tr>
            <th colspan="2"></th>
            <th><?php echo AirBoxStringModel::__('Reservados'); ?></th>
            <th><?php echo $reserved_boxes; ?></th>
            <th><?php echo sprintf('%s €',$reserved_value);  ?></th>
            <th colspan="2"></th>
        </tr>
        <?php endif; ?>
    </tfoot>
</table>
