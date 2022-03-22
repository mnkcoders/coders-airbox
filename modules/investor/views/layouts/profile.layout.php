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
        <div class="tab-content full-width">
            <h1 class="title"><?php
                echo AirBoxStringModel::__( AirBoxStringModel::LBL_MENU_OPTION_PROFILE ); ?></h1>
        </div>
        <div class="tab-content half-width profile">
            <?php AirBoxRenderer::getInstance()->getTemplate('profile_edit');?>
            <?php AirBoxRenderer::getInstance()->getTemplate('profile_password');?>
        </div>
        <div class="tab-content half-width options">
            <?php AirBoxRenderer::getInstance()->getTemplate('profile_options');?>
        </div>
    </div>
</div>