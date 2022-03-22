<?php defined('ABSPATH') or die;
/**
 * Vista formulario de registro de invitado visitante
 * 
 * El visitante se registra para invertir en N BOXES
 * 
 * Si el formulario incluye un hash de inversor, este es un form de inversor,
 * si no incluye un hash o el hash es de administrador (por discutir) es  el form de inversor de la home
 * 
 * 
 * El form de inversión debería incluirse también como un widget, interesa que los tags y variables locales
 * sean compatibles
 * 
 * 
 * Añade control de visualización en función del número de Boxes disponibles
 * 
 * Si el número de boxes llega a 0, el formulario no se muestra y en su lugar, aparece un mensaje y un link
 * de redirección a la home
 * 
 */

//$formData = AirBoxRenderer::getInstance()->getModel();

$available = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <div class="coinbox-header">
        <h2 class="title"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_TITLE_AFFILIATION_FORM);
        
        ?></h2>
    </div>
    <?php if( $available > 0 ) : ?>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages(); ?>
        <form name="coinbox-investor-form" class="<?php

            echo AirBoxRenderer::getInstance()->getClass('form');

            ?>" action="<?php

            echo AirBoxRouter::RoutePublic();

            ?>" method="post" >
            <?php AirBoxRenderer::getInstance()->getTemplate('affiliate_form'); ?>
        </form>
    </div>
    <?php else : ?>
    <div class="coinbox-content boxed">
        <?php AirBoxRenderer::getInstance()->getTemplate('closed'); ?>
        <?php AirBoxRenderer::getInstance()->getTemplate('contact'); ?>
    <?php endif; ?>
    </div>
</div>