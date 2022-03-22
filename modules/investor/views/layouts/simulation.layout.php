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
        <div class="tab-content full-width simulation">
            <h1 class="title"><?php 
            
            echo AirBoxStringModel::__(AirBoxStringModel::LBL_MENU_OPTION_SIMULATION ); ?></h1>
            <?php AirBoxRenderer::getInstance()->getTemplate('simulation_form');?>
        </div>
    </div>
</div>