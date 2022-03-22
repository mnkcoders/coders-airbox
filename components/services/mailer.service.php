<?php defined('ABSPATH') or die;
/**
 * Notificador por mail whatsapp, etc
 *
 * @author Coder#1
 */
class AirBoxMailerService extends AirBoxService{
    
    //const DEFAULT_SIGNAGURE = '<p><i>Mailer <strong>Global AirBox</strong></i></p>';
    
    const CONTENT_TYPE_PLAIN = 'text/plain';

    const CONTENT_TYPE_HTML = 'text/html';
    
    private $_recipient = array();
    
    protected function __construct( array $settings = null ) {
        parent::__construct($settings);
    }
    /**
     * @return String
     */
    public final function __toString() {
        
        return sprintf('%s(%s)',parent::__toString(),implode(',' ,$this->_recipient ) );
    }
    /**
     * @param String $email agrega una dirección de correo de destino
     */
    public final function addRecipient( $email ){
        $this->_recipient[] = $email;
    }
    /**
     * @param String $subject Asunto del correo
     */
    public final function setSubject( $subject ){
        $this->set('subject', $subject);
    }
    /**
     * @param String $content contenido del mensaje como texto simple o con formato html
     */
    public final function setContent( $content ){
        $this->set('content', $content);
    }
    /**
     * Anexa contenido al mensaje
     * @param string $content
     */
    public final function addContent( $content ){
        $this->set('content', $this->get('content', '' ) . $content );
    }
    /**
     * @param html $signature Cadena html a mostrar como firma del correo
     */
    public final function setSignature( $signature ){
        $this->set('signature', $signature);
    }
    /**
     * Establece los filtros de correo para enviar en nombre del administrador del sistema
     * y el tipo de contenido como texto html
     */
    protected function onBeforeDispatch() {
        add_filter('wp_mail_from',function(){
            return get_option( 'admin_email' );
        });
        add_filter( 'wp_mail_from_name', function(){
            return get_bloginfo ('name');
        });
        add_filter( 'wp_mail_content_type', function(){
            return AirBoxMailerService::CONTENT_TYPE_HTML;
        });
    }
    /**
     * @return bool Resultado del envío
     */
    protected function onDispatch() {
        
        $app_name = AirBox::getOption('application_name','AirBox');
        
        $default_signature = sprintf('<p>%s <i>%s</i></p>',
                $this->get('signature', AirBoxStringModel::EML_FOOTER_SIGNATURE ),
                $app_name);
        
        $content = sprintf('<p>%s</p><hr/><p>%s</p>',
                $this->get('content',''),
                $default_signature );
        
        $subject = sprintf('%s - %s', $app_name, $this->get('subject'));

        return !is_null($subject) ?
            wp_mail( $this->_recipient, $subject, $content ) :
            false;
    }
    /**
     * Restablece el filtro de tipo de contenido a texto plano, requerido por WP
     */
    protected function onAfterDispatch() {
        //increible, pero cierto ¬_¬! hay que devolverlo a su valor original o wordpresss se rompe
        add_filter( 'wp_mail_content_type', function(){
            return AirBoxMailerService::CONTENT_TYPE_PLAIN;
        });
    }
    /**
     * Crea una instancia del servicio de mensajería por email
     * @return AirBoxMailerService Servicio de Mailer
     */
    public static function createService() {
        return new AirBoxMailerService();
    }
}
