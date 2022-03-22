<?php defined('ABSPATH') or die;

$pending_refunds = $this->get_data('pending_refunds');

if(count($pending_refunds) ) : ?>
    <ul class="dashboard incoming">
        <li class="title"><?php echo AirBoxStringModel::__('Reintegros solicitados'); ?></li>
        <?php foreach( $pending_refunds as $refund ) : ?>
        <li>
            <a class="investor" href="<?php
        
        //user_id
        echo AirBoxRouter::Route(null,array(
            AirBoxEventModel::EVENT_SELECTED_VIEW=>'investors',
            AirBoxInvestorModel::FIELD_META_ID=>$refund[AirBoxTransactionModel::FIELD_META_ACCOUNT_ID]
                ));
        
        ?>" target="_self"><?php 
        
        echo $refund[AirBoxInvestorModel::FIELD_META_AFFILIATE];
        
        ?></a>
            <span class="amount"><?php
            
            echo sprintf(' ( %s APS )',
                $refund[AirBoxTransactionModel::FIELD_META_AMOUNT]);
            
            ?></span>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>