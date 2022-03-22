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
        <div class="tab-content half-width progress">
            <h2 class="title"><?php
            
            echo AirBoxStringModel::__( AirBoxStringModel::LBL_MENU_OPTION_SETTLEMENT );
            
            ?></h2>
            <?php echo AirBoxRenderer::renderPost(
                    AirBox::getOption('coinbox_page_settlement',0),
                    'content' ); ?>
            <?php AirBoxRenderer::getInstance()->getTemplate('progress'); ?>
        </div>
        <div class="tab-content half-width calendar">
            <?php AirBoxRenderer::getInstance()->getTemplate('calendar');?>
        </div>
    </div>
</div>