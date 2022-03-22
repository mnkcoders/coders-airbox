<?php defined('ABSPATH') or die;
/**
 * Vista de iframe calendario
 * No funcionarà en local, probar en test server
 */
?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('header');?>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <h2 class="title"><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_MENU_OPTION_CALENDAR); ?></h2>
        <?php AirBoxRenderer::getInstance()->getTemplate('calendar');?>
    </div>
</div>