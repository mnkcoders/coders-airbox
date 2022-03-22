<?php defined('ABSPATH') or die;
/**
 * Vista de estado general del inversor
 */
?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('header');?>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <div class="tab-content full-width news">
            <h1 class="title"><?php
            
            echo AirBoxStringModel::__( AirBoxStringModel::LBL_TITLE_NEWS ); ?></h1>
            <?php AirBoxRenderer::getInstance()->getTemplate('post');?>
        </div>
    </div>
</div>