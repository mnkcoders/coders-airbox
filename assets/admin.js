jQuery( document ).ready(function(){

    if ( jQuery ('.airbox-intranet-container .media-selector').length > 0) {
        
        if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {

            jQuery( '.airbox-intranet-container .media-selector' ).on( 'click', function(e){

                e.preventDefault();

                var _self = jQuery( this );
                
                var _input = jQuery( _self ).prev();
                
                //Extend the wp.media object
                var airbox_media_selector = wp.media.frames.file_frame = wp.media({
                    multiple: false
                });

                //When a file is selected, grab the URL and set it as the text field's value
                airbox_media_selector.on('select', function() {

                    attachment = airbox_media_selector.state().get('selection').first().toJSON();

                    jQuery(_input).val( attachment.id );

                    if( jQuery(_input).val() ){
                        jQuery(_input).addClass('loaded');

                        jQuery(_self).html( attachment.filename );
                    }
                    else{
                        if( jQuery(_input).hasClass( 'loaded' ) ){
                            jQuery(_input).removeClass()('loaded');
                        }
                    }
                });

                //Open the uploader dialog
                airbox_media_selector.open( _self );
                
                return false;
            });
            //link bara borrar
            jQuery('a.clear-media').on('click',function(e){
                
                e.preventDefault();
                
                var target_id = jQuery(this).attr('href');
                
                var target = jQuery( target_id );

                jQuery( target ).val('');
                
                jQuery( target ).next('.media-selector').html('Seleccionar');
                
                return false;
            });
        }
        else{
            jQuery( '.airbox-intranet-container .media-selector' ).on( 'click', function(e){
                alert( 'wp.media not found' );
            });
        }
    }    
});
