<?php defined('ABSPATH') or die;
/**
 * Vista de estado general del inversor
 */
?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('header');?>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <div class="tab-content half-width status">
            <?php AirBoxRenderer::getInstance()->getTemplate('welcome');?>
            <?php AirBoxRenderer::getInstance()->getTemplate('status'); ?>
            <?php AirBoxRenderer::getInstance()->getTemplate('upgrade'); ?>
        </div>
        <div class="tab-content half-width boxes">
            <?php AirBoxRenderer::getInstance()->getTemplate('order_summary'); ?>
            <?php if( AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0) > 0 ) : ?>
            <?php AirBoxRenderer::getInstance()->getTemplate('purchase_form'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>