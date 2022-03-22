<?php defined('ABSPATH') or die;
/**
 * Vista de estado general del inversor
 */
?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('header');?>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <div class="tab-content full-width salepoints">
            <h1 class="title"><?php
            
            echo AirBoxStringModel::__(AirBoxStringModel::LBL_MENU_OPTION_SALEPOINTS ); ?></h1>
            <iframe src="https://www.google.com/maps/d/embed?mid=zWd2AFq-VDlg.kCcFM6SGJYLQ" width="640" height="480"></iframe>
        </div>
    </div>
</div>