<?php defined('ABSPATH') or die;
/**
 * Vista de estado general del inversor
 */

/**
 * @var AirBoxInvestorModel Inversor validado en el sistema
 */
$investor = AirBoxRenderer::getInstance()->getModel();

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('header');?>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <h1 class="title"><?php echo AirBoxStringModel::__(
                AirBoxStringModel::LBL_MENU_OPTION_HISTORY );
        ?></h1>
        <div class="tab-content full-width history">
            <?php AirBoxRenderer::getInstance()->getTemplate('history');?>
        </div>
        <!--div class="tab-content half-width orders">
            <?php
            //AirBoxRenderer::getInstance()->getTemplate('order_history');
            ?>
        </div-->
    </div>
</div>