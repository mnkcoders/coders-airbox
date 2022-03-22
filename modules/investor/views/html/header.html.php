<?php defined('ABSPATH') or die;
/**
 * Vista de estado general del inversor
 */

$media_id = AirBox::getOption('coinbox_background_image',0);

$header_background = $media_id ? AirBoxRenderer::renderMediaUrl( $media_id ) : '';

?>
<div class="coinbox-header" <?php

    if( strlen($header_background) ){
        echo sprintf( 'style="background-image: url(\'%s\');"', $header_background );
    }

?>>
    <?php echo AirBoxRenderer::renderMenu('affiliate', $this ); ?>
    <?php echo AirBoxRenderer::renderMenu('profile', $this ); ?>
</div>