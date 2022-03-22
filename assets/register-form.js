jQuery(document).ready( function(){
    /**
     * Actualiza el precio de coste del box durante el proceso de compra en la etiqueta
     * del form de afiliación
     */
    jQuery( '.widget_airbox_form #id_boxes' ).on( 'change' , function( e ){
        
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