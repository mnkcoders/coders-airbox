<?php defined('ABSPATH') or die;

//propiedades captadas desde el modelo de la vista
$box_amount = $this->get_data('box_amount',1);
$investor_key = $this->get_data(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY);
$demo_form = $this->get_data('demo_form',false);

//propiedades de acceso directo a la caché / parámetros
$box_remaining = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);
$box_price = AirBox::getOption('coinbox_cost',1);

?>
<ul class="coinbox-investor-fields <?php if(!is_null($investor_key)) { echo 'investor'; } ?>">
    <li class="form-input half-width"><?php echo AirBoxRenderer::renderText(
        AirBoxInvestorModel::FIELD_META_FIRST_NAME,
        $this->get_data(AirBoxInvestorModel::FIELD_META_FIRST_NAME),
        'investor-input', AirBoxStringModel::LBL_FIRST_NAME);
    ?></li>
    <li class="form-input half-width"><?php echo AirBoxRenderer::renderText(
        AirBoxInvestorModel::FIELD_META_LAST_NAME,
        $this->get_data(AirBoxInvestorModel::FIELD_META_LAST_NAME),
        'investor-input', AirBoxStringModel::LBL_LAST_NAME);
    ?></li>

    <li class="form-input half-width"><?php echo AirBoxRenderer::renderText(
        AirBoxInvestorModel::FIELD_META_USER_NAME,
        $this->get_data(AirBoxInvestorModel::FIELD_META_USER_NAME),
        'investor-input', AirBoxStringModel::LBL_USER_NAME);
    ?></li>
    <li class="form-input half-width"><?php echo AirBoxRenderer::renderText(
        AirBoxInvestorModel::FIELD_META_USER_PASS,
        null, 'investor-input', AirBoxStringModel::LBL_USER_PASS);
    ?></li>

    <li class="form-input half-width"><?php echo AirBoxRenderer::renderEmail(
        AirBoxInvestorModel::FIELD_META_EMAIL,
        $this->get_data(AirBoxInvestorModel::FIELD_META_EMAIL),
        'investor-input', AirBoxStringModel::LBL_EMAIL);
    ?></li>
    <li class="form-input half-width"><?php echo AirBoxRenderer::renderTelephone(
        AirBoxInvestorModel::FIELD_META_TELEPHONE,
        $this->get_data(AirBoxInvestorModel::FIELD_META_TELEPHONE),
        'investor-input', AirBoxStringModel::LBL_PHONE);
    ?></li>
    <li class="form-input half-width"><?php echo AirBoxRenderer::renderText(
        AirBoxInvestorModel::FIELD_META_DOCUMENT_ID,
        $this->get_data(AirBoxInvestorModel::FIELD_META_DOCUMENT_ID),
        'investor-input', AirBoxStringModel::LBL_DOCUMENT_ID);
    ?></li>
    <li class="form-input half-width">
        <input type="hidden" id="id_box_price" value="<?php echo $box_price; ?>" />
        <label class="boxes" for="id_<?php
        
            echo AirBoxInvestorModel::FIELD_META_BOXES?>"><?php

            echo AirBoxStringModel::__( AirBoxStringModel::LBL_BOX_AMOUNT );
            
            ?><br/>
            <strong class="highlight" id="id_order">
                <span class="amount"><?php echo $box_amount; ?></span> BOX =
                <span class="price"><?php echo $box_price; ?></span> €
            </strong>
        </label>
        <?php echo AirBoxRenderer::renderNumber(
                AirBoxInvestorModel::FIELD_META_BOXES,
                $box_amount,1,$box_remaining); ?>
    </li>
    <?php if( $demo_form ) : ?>
    <button type="submit" disabled="disabled" class="button"><?php
    
    echo AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_CHECKOUT);
    
    ?></button>
    <?php else : ?>
    <li class="form-submit full-width"><?php

    if( !is_null( $investor_key ) ){
        //echo AirBoxRenderer::renderLabel('DEBUG_INVESTOR_KEY', 'DEBUG_INVESTOR_KEY: '.$investor_key);
        echo AirBoxRenderer::renderHidden(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY, $investor_key);
    }
    
    echo AirBoxRenderer::renderSubmit(
            AirBoxEventModel::EVENT_TYPE_COMMAND, 'register',
            AirBoxStringModel::LBL_BUTTON_ORDER);
            
    ?></li>
    <?php endif; ?>
</ul>
<!-- esto de momento aqui que no duele -->
<script type="text/javascript">
    jQuery(document).ready( function(){
        /**
         * Es una copia del script cargado para el widget. Con algo mas de tiempo debería ser colocado
         * en la cabecera.
         * Actualiza el precio de coste del box durante el proceso de compra en la etiqueta
         * del form de afiliación
         */
        jQuery( '.coinbox-investor-fields #id_boxes' ).on( 'change' , function( e ){

            //capturar precio base obtenido de un campo oculto
            var base_price = jQuery('#id_box_price').val();
            //capturar valor seleccionado
            var amount = jQuery( this ).val();

            //actualizar cantidad
            jQuery('#id_order .amount').html( amount.toString( ) );
            //actualizar precio coste
            jQuery('#id_order .price').html( amount * base_price );
        });
    });
</script>

