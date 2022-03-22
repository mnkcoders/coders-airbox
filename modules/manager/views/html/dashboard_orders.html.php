<?php defined('ABSPATH') or die;

$pending_orders = $this->get_data('pending_orders');

if(count($pending_orders) ) : ?>
    <ul class="dashboard incoming">
        <li class="title"><?php echo AirBoxStringModel::__('Pedidos entrantes'); ?></li>
        <?php foreach( $pending_orders as $order ) : ?>
        <li>
            <span class="order-id"><?php
            
            echo sprintf(
                    AirBoxStringModel::__('[nº %s] - '),
                    $order[AirBoxOrderModel::FIELD_META_ID]);
            
            ?></span><a class="investor" href="<?php

            echo AirBoxRouter::RouteAdmin(array(
                AirBoxEventModel::EVENT_SELECTED_VIEW=>'investors',
                AirBoxInvestorModel::FIELD_META_ID=>$order[AirBoxOrderModel::FIELD_META_OWNER_ID]));
        
        ?>" target="_self"><?php 
        
        echo $order[AirBoxInvestorModel::FIELD_META_AFFILIATE];
        
        ?></a><br/><strong class="amount">( <?php echo sprintf(
                    AirBoxStringModel::__('%s Boxes'),
                    $order[AirBoxOrderModel::FIELD_META_AMOUNT]);
            ?> )</strong>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>