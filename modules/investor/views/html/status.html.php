<?php defined('ABSPATH') or die;
/**
 * Vista de boxes adquiridos y reservados por el inversor
 * Es preciso haber cargado los boxes antes!!!
 */

/**
 * @var AirBoxInvestorModel Inversor validado en el sistema
 */
$investor = AirBoxRenderer::getInstance()->getModel();

$owned_boxes = 0;
$reserved_boxes = 0;
$affiliates = $investor->getChildren(true);
$inv_child_boxes = 0;
$investor_key = AirBox::Instance()->getProfileData(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY);

foreach( $investor->getChildBoxes() as $child ){
    $inv_child_boxes += $child[AirBoxOrderModel::FIELD_META_AMOUNT];
}

foreach( $investor->getBoxes() as $box ){
    if( $box[AirBoxOrderModel::FIELD_META_TYPE] > AirBoxOrderModel::ORDER_TYPE_RESERVED ){
        $owned_boxes += $box[AirBoxOrderModel::FIELD_META_AMOUNT];
    }
    else{
        $reserved_boxes += $box[AirBoxOrderModel::FIELD_META_AMOUNT];
    }
}

$affiliates_url = AirBoxRouter::RoutePublic(array(
    AirBoxEventModel::EVENT_SELECTED_VIEW => AirBoxInvestorView::INVESTOR_OPTION_AFFILIATES
));

?>
<ul class="content status">
    <?php if( $investor->getStatus() < AirBoxInvestorModel::INVESTOR_STATUS_ACTIVE) : ?>
    <li class="investor-status"><?php
    
    echo AirBoxInvestorModel::displayStatus( $investor->getStatus() );
    
    ?></li>
    <?php elseif( $investor->getPlan() > AirBoxInvestorModel::INVESTOR_PLAN_NONE ) : ?>
    <li><strong><?php
    
        echo  sprintf('%s %s', 
                AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR),
                AirBoxInvestorModel::displayPlan( $investor->getPlan() ));

    ?></strong></li>
    <?php endif; ?>
    <?php if(count($affiliates) ): ?>
    <!-- recuento de inversores atraidos -->
    <li class="affiliates icon-investors">
        <p><a href="<?php echo $affiliates_url; ?>" target="_self"><?php
        
        echo sprintf('<strong class="highlight">%s</strong> %s',
                count($affiliates),
                AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_AFFILIATES));

        ?></a></p>
        <p><a href="<?php echo $affiliates_url; ?>" target="_self"><?php
        
        echo sprintf('<strong class="highlight">%s</strong> %s',
                $inv_child_boxes,
                AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_SOLD_BOXES) );
        
        ?></a></p>
    </li>
    <?php endif; ?>
    <li class="airpoint-summary">
    <?php echo AirBoxStringModel::__compose( AirBoxStringModel::LBL_INVESTOR_AIRPOINTS,
            sprintf('<strong class="airpoints">%s</strong>',$investor->getAirPoints())); ?>
    </li>
    <?php if($investor->getAirPoints()) : ?>
    <li class="airpoint-value">
    <?php echo AirBoxStringModel::__compose(
            AirBoxStringModel::LBL_INVESTOR_AIRPOINTS_VALUE,
            sprintf('<strong class="highlight">%s €</strong>',
                    $investor->getAirPointValue($investor->getAirPoints()))); ?>
    <a href="<?php
    
    echo AirBoxRouter::RoutePublic(array(
        AirBoxEventModel::EVENT_SELECTED_VIEW=>'refund') );
    
    ?>" target="_self" class="button right"><?php
    
    echo AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_REQUEST_REFUND);
    
    ?></a>
    </li>
    <?php endif; ?>
    <li class="affiliate-form"><a href="<?php
    
    echo AirBoxRouter::RoutePublic(array(
        AirBoxEventModel::EVENT_SELECTED_VIEW => $investor_key ) );
    
    ?>" target="_blank"><?php
    
    echo AirBoxStringModel::__(AirBoxStringModel::LBL_INFO_INVESTOR_FORM_TIP);
    
    ?></a><p><input type="text" disabled="disabled" value="<?php
    
    echo AirBoxRouter::RoutePublic( array(
                        AirBoxEventModel::EVENT_SELECTED_VIEW=>$this->get_data('key') ));
    
    ?>"/></p></li>
</ul>


