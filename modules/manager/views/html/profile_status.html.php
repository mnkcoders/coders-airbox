<?php defined('ABSPATH') or die;

$investor = $this->get_data('investor');

$parent = $this->get_data('parent');

$owned_boxes = 0;

$reserved_boxes = 0;

$sold_boxes = 0;

$affiliates = count( $investor->getChildren( true ) );

foreach( $investor->getBoxes(true) as $box ){
    if( $box[AirBoxOrderModel::FIELD_META_TYPE] > AirBoxOrderModel::ORDER_TYPE_RESERVED ){
        $owned_boxes += $box[AirBoxOrderModel::FIELD_META_AMOUNT];
    }
    else{
        $reserved_boxes += $box[AirBoxOrderModel::FIELD_META_AMOUNT];
    }
}

?>
<ul class="coinbox-content half-width status">
    <li class="title"><h2 class="investor-plan plan-<?php echo $investor->getPlan(); ?>"><?php
        echo sprintf( '%s %s <span style="float: right; color: #d5d5d5; font-style: italic;">[%s]</span>',
            AirBoxStringModel::__('Inversor'),
            AirBoxInvestorModel::displayPlan( $investor->getPlan()),
            AirBoxInvestorModel::displayStatus($investor->getStatus())); ?></h2>
    </li>
    <li>
        <span><?php echo AirBoxStringModel::__('AirPoints'); ?></span>
        <strong><?php echo sprintf('%s AP',$investor->getAirPoints()); ?></strong>
    </li>
    <li>
        <span><?php echo AirBoxStringModel::__('Boxes'); ?></span>
        <strong class="boxes"><?php echo $reserved_boxes > 0 ?
            sprintf('%s <i class="reserved">(+%s)</i>',$owned_boxes,$reserved_boxes) :
            $owned_boxes;
        ?></strong>
    </li>
    <?php if( $sold_boxes > 0) : ?>
    <li>
        <span><?php echo AirBoxStringModel::__('Boxes vendidos'); ?></span>
        <strong class="boxes"><?php echo $sold_boxes; ?></strong>
    </li>
    <?php endif; ?>
    <?php if( !is_null($parent) ) : ?>
    <li>
        <span><?php echo AirBoxStringModel::__('Afiliado por'); ?></span>
        <?php echo sprintf( '<a href="%s" target="_self">%s</a>',
            AirBoxRouter::Route(null,array(
                'view'=>  AirBoxManagerBootStrap::ADMIN_OPTION_INVESTOR_PROFILE,
                AirBoxInvestorModel::FIELD_META_ID=>$parent->getId())),
            //2016-04-20 - se sustituye el nombre de usuario por el nombre completo
            $parent->getFullName());
        ?>
    </li>
    <?php else : ?>
    <li>
        <span><?php echo AirBoxStringModel::__('Asignar a un inversor'); ?></span>
        <span class="right">
            <!-- FORM DE ASIGNACION DE INVERSOR PRINCIPAL -->
            <form name="parent_investor_selector_form" method="get" action="<?php
            
            echo AirBoxRouter::RouteAdmin();
            
            ?>">
        <?php
        //no es capaz de interpretar esto por url ¬_¬
        echo AirBoxRenderer::renderHidden( 'page','coinbox');
                
        echo AirBoxRenderer::renderHidden(
                AirBoxEventModel::EVENT_SELECTED_VIEW,
                AirBoxManagerView::VIEW_LAYOUT_INVESTOR_PROFILE);
                
        echo AirBoxRenderer::renderHidden(
                AirBoxInvestorModel::FIELD_META_ID,
                $investor->getId());
                
        echo AirBoxRenderer::renderSubmit(
                AirBoxEventModel::EVENT_TYPE_COMMAND,
                'set_parent', 'Asignar');
        
        echo AirBoxRenderer::renderList(
                AirBoxInvestorModel::FIELD_META_PARENT_ID,
                $this->get_data('non_child_investors'),
                null, 'list', 'Seleccionar inversor');
        
        ?>
            </form>
            <!-- FIN FORM ASIGNACION INVERSOR PRINCIPAL -->
        </span>
    </li>
    <?php endif; ?>
    <li>
        <span><?php echo AirBoxStringModel::__('Cuota de afiliados'); ?></span>
        <strong class="affiliates"><?php echo $affiliates; ?></strong>
    </li>
    <?php if( $investor->getAirPoints() > 0 ) : ?>
    <li>
        <a href="<?php
        
        echo AirBoxRouter::RouteAdmin(array(
            AirBoxEventModel::EVENT_TYPE_COMMAND => AirBoxManagerBootStrap::ADMIN_OPTION_APPLY_REFUND,
            AirBoxEventModel::EVENT_SELECTED_VIEW => AirBoxManagerView::VIEW_LAYOUT_INVESTOR_PROFILE,
            AirBoxInvestorModel::FIELD_META_ID => $investor->getId(),
        ));
        
        ?>" target="_self"><?php echo AirBoxStringModel::__('Aplicar reintegro'); ?></a>
    </li>
    <?php endif; ?>
    <?php if( $investor->getStatus() < AirBoxInvestorModel::INVESTOR_STATUS_ACTIVE ) : ?>
    <li>
        <a href="<?php
        
        echo AirBoxRouter::RouteAdmin(array(
            AirBoxEventModel::EVENT_TYPE_COMMAND => AirBoxManagerBootStrap::ADMIN_OPTION_ACTIVATE_INVESTOR,
            AirBoxEventModel::EVENT_SELECTED_VIEW => AirBoxManagerView::VIEW_LAYOUT_INVESTOR_PROFILE,
            AirBoxInvestorModel::FIELD_META_ID => $investor->getId(),
        ));
                
        ?>" target="_self"><?php echo AirBoxStringModel::__('Activar inversor sin confirmar pedido?' ); ?></a>
    </li>
    <?php endif ; ?>
</ul>
