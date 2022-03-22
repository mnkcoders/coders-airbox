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
        <div class="tab-content half-width affiliates">
            <h1 class="title"><?php
            echo AirBoxStringModel::__(AirBoxStringModel::LBL_MENU_OPTION_AFFILIATES); ?></h1>
            <?php AirBoxRenderer::getInstance()->getTemplate('affiliates');?>
        </div>
        <div class="tab-content half-width contact">
            <h1 class="title"><?php
            echo AirBoxStringModel::__(AirBoxStringModel::LBL_TITLE_FAQ_FORM); ?></h1>
            <?php AirBoxRenderer::getInstance()->getTemplate('contact');?>
            <?php AirBoxRenderer::getInstance()->getTemplate('upgrade');?>
        </div>
    </div>
</div>