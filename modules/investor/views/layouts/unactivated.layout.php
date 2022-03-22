<?php defined('ABSPATH') or die;
/**
 * Vista de estado general del inversor
 */

/**
 * @var AirBoxInvestorModel Inversor validado en el sistema
 */
$investor = AirBoxRenderer::getInstance()->getModel();

$pending_orders = AirBoxOrderModel::CountOrders( AirBoxOrderModel::ORDER_TYPE_RESERVED, $investor->getId() );

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('header');?>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <form name="coinbox-checkout-form" action="<?php echo AirBoxRouter::Route(); ?>" method="post">
            <div class="tab-content half-width">
                <p><strong><?php echo AirBoxStringModel::__compose(
                        AirBoxStringModel::LBL_INFO_UNACTIVATED_ACCOUNT_WELCOME,
                        $investor->getFullName()); ?></strong></p>
                <?php AirBoxRenderer::renderPost(
                        AirBox::getOption('coinbox_page_unactivated',0),
                        'content'); ?>
                <?php echo AirBoxRenderer::renderPost(Airbox::getOption('coinbox_page_unactivated'),'content'); ?>
                <?php AirBoxRenderer::getInstance()->getTemplate('progress'); ?>
            </div>
            <div class="tab-content half-width">
                <?php AirBoxRenderer::getInstance()->getTemplate('order_summary');?>
                <?php if( $pending_orders == 1 ){ AirBoxRenderer::getInstance()->getTemplate('purchase_form'); } ?>
            </div>
        </form>
        <!-- fin pendiente activación -->
    </div>
</div>