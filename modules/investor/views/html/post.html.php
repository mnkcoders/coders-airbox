<?php defined('ABSPATH') or die;
/**
 * Vista de visualización de blog y noticias
 */

$entryList = $this->get_data('entry');

?>
<div class="content post-entry">
    <?php foreach( $entryList as $pID => $pContent ) : ?>
    <?php $thumb = get_the_post_thumbnail( $pID, 'thumbnail' ); ?>
    <div class="post ">
        
        <?php if($thumb ) { echo $thumb; } ?>
        
        <h4 class="post-title"><a href="<?php
        
        echo get_permalink ($pID);
        
        ?>" target="_blank"><?php echo
        
        $pContent['post_title'];
        
        ?></a></h4>
        <p class="post-meta"><?php
        
        echo AirBoxStringModel::__compose(AirBoxStringModel::LBL_PUBLISHED_ON,$pContent['post_date']);
        
        ?></p>
        <?php if( strlen( $pContent['post_excerpt'] ) ): ?>
        <?php echo $pContent['post_excerpt']; ?>
        <?php elseif(strlen($pContent['post_content']) > 256) : ?>
        <?php echo substr($pContent['post_content'],0,255) . ' ...'; ?>
        <?php else: ?>
        <?php echo $pContent['post_content']; ?>
        <?php endif; ?>
        
        <a class="read-more" href="<?php
        
        echo get_permalink ($pID); ?>" target="_blank"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_READ_MORE); ?></a>
    </div>
    <?php endforeach; ?>
</div>