<?php defined('ABSPATH') or die;
/**
 * Vista de estado general del inversor
 */

/**
 * @var AirBoxInvestorModel Inversor validado en el sistema
 */
//$investor = AirBoxRenderer::getInstance()->getModel();
$investor = AirBoxRenderer::getInstance()->getModel();

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <div class="coinbox-header">
        <h1 class="title"><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_TITLE_ACCESS_RESTRICTED ); ?></h1>
    </div>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <div class="tab-content full-width status">
            <a class="button" href="<?php echo get_site_url(); ?>" target="_self"><?php
        
            echo AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_BACK_TO_HOME );?></a>
        </div>
    </div>
</div>