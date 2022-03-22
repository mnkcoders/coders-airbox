<?php defined('ABSPATH') or die;
/**
 * Configuración de pasarela de pago
 */
class AirBoxCheckoutStripeGateWay extends AirBoxCheckoutModel{
    
    const STRIPE_TOKEN = 'stripeToken';
    const STRIPE_EMAIL = 'stripeEmail';
    
    const STRIPE_LIVE_SECRET_KEY = 'secret_key';
    const STRIPE_LIVE_PUBLIC_KEY = 'publishable_key';
    const STRIPE_TEST_SECRET_KEY = 'test_secret_key';
    const STRIPE_TEST_PUBLIC_KEY = 'test_publishable_key';
    const STRIPE_MODE = 'test_mode';
    const STRIPE_MODE_LIVE = 'live';
    const STRIPE_MODE_TEST = 'test';
    const STRIPE_MODE_UNSET = 'UNSET';

    /**
     * @var URL Script AJAX de la pasarela de pago
     */
    const STRIPE_AJAX_URL = 'https://checkout.stripe.com/checkout.js';
    
    /**
     * Instancia Stripe para trabajar dentro del plugin
     * @var AirBoxStripePlugin
     */
    private $_stripe = null;
    
    protected function __construct(array $settings) {
        
        //registrar dependencia del plugin de stripe
        parent::registerDependency('Stripe', AirBox::COINBOX_COMPONENT_TYPE_PLUGIN);
        
        $is_test_mode = AirBox::getOption('stripe_test_mode',AirBox::PLUGIN_OPTION_DISABLED);
        //selector de modo desarrollo de la pasarela
        $mode = $is_test_mode === AirBox::PLUGIN_OPTION_ENABLED ?
                self::STRIPE_MODE_TEST :
                self::STRIPE_MODE_LIVE;
        
        parent::registerSetting( self::CHECKOUT_LABEL, 'Stripe' );
        //valores estáticos de la pasarela
        parent::registerSetting(self::STRIPE_MODE, $mode );
        parent::registerSetting(self::STRIPE_LIVE_SECRET_KEY,
                'sk_live_'.AirBox::getOption('stripe_live_sk'));
        parent::registerSetting(self::STRIPE_LIVE_PUBLIC_KEY,
                'pk_live_'.AirBox::getOption('stripe_live_pk'));
        parent::registerSetting(self::STRIPE_TEST_SECRET_KEY,
                'sk_test_'.AirBox::getOption('stripe_test_sk'));
        parent::registerSetting(self::STRIPE_TEST_PUBLIC_KEY,
                'pk_test_'.AirBox::getOption('stripe_test_pk'));
        
        //estos valores serán cargados al cargar la pasarela desde el callback
        parent::registerSetting(self::STRIPE_EMAIL);
        parent::registerSetting(self::STRIPE_TOKEN);

        parent::__construct($settings);
    }
    /**
     * Comprueba e inicializa las dependencias necesarias. Este método se inicializa explícitamente
     * en el cargador de pasarelas de pago CheckoutModel antes de retornar la instancia, por tanto
     * debería ser seguro, pero comprobar la instancia _stripe por si fallara algo
     * 
     * @return boolean
     */
    protected final function checkDependencies() {
        
        if(parent::checkDependencies()){
            /**
             * Inicializar aqui el manejador de la API Stripe del plugin cargado
             */
            $this->_stripe = AirBoxStripePlugin::getInstance($this->getSecretKey());

            //asegurarse SIEMPRE que la instancia retornada es válida
            return !is_null( $this->_stripe );
        }
        
        return false;
    }
    /**
     * @return String Clave secreta
     */
    private final function getSecretKey(){
        switch( $this->getSetting(self::STRIPE_MODE,self::STRIPE_MODE_LIVE)){
            case self::STRIPE_MODE_LIVE:
                return $this->getSetting(self::STRIPE_LIVE_SECRET_KEY,'');
            case self::STRIPE_MODE_TEST:
                return $this->getSetting(self::STRIPE_TEST_SECRET_KEY,'');
            default:
                return self::STRIPE_MODE_UNSET;
        }
    }
    /**
     * @return String Clave pública
     */
    private final function getPublicKey(){
        switch( $this->getSetting(self::STRIPE_MODE,self::STRIPE_MODE_LIVE)){
            case self::STRIPE_MODE_LIVE:
                return $this->getSetting(self::STRIPE_LIVE_PUBLIC_KEY,'');
            case self::STRIPE_MODE_TEST:
                return $this->getSetting(self::STRIPE_TEST_PUBLIC_KEY,'');
            default:
                return self::STRIPE_MODE_UNSET;
        }
    }
    /**
     * Moneda de la transacción, EURO por defecto
     * @return string
     */
    private final function getCurrency(){
        return strtoupper($this->getSetting(parent::CHECKOUT_CURRENCY,'eur'));
    }
    /**
     * Stripe requiere los dígitos de céntimos por lo que se multiplica el valor por 100
     * @return int Cantidad del pedido en formato céntimos
     */
    private final function formatOrderValue(){
        return $this->getOrderValue() * 100;
    }
    /**
     * @return array Lista de contenidos
     */
    public function getContent() {
        
        $config = array(
            AirBoxStringModel::__('Stripe is a suite of APIs that powers commerce for businesses of all sizes'),
            AirBoxStringModel::__('Condiciones de pago aqu&iacute;'),
        );
        
        //$config[] = $this->getPublicKey();
        
        return $config;
    }
    /**
     * Este método de Stripe funciona por AKAX y ejecuta automáticamente el envío del formulario
     * una vez el checkout de stripe ha resuelto la compra correctamente.
     * 
     * Al no ir el evento commit incluido  en el botón de compra, conviene adjuntarla en
     * la url de redirección del form o bien en el apartado getFormData
     * 
     * @return URL Destino donde procesar los datos del formulario
     */
    public function getActionUrl() {
        return AirBoxRouter::RoutePublic(array(
            AirBoxEventModel::EVENT_TYPE_COMMAND => AirBoxInvestorBootStrap::INVESTOR_COMMAND_COMMIT_ORDER));
    }
    /**
     * Definir los inputs ocultos del formulario
     * @return array Retorna todos los inputs ocultos para preparar la pasarela de Stripe
     */
    public function getFormData() {
        
        $formData = parent::getFormData();
        
        /**
         * @todo INVESTIGAR EL NONCE ENVIADO SI PERSISTE COMO UNA OPCIÓN DE VALIDACIÓN
         * DEL PEDIDO EN EL SERVIDOR Y EVITAR QUE EL VISITANTE COMPRADOR PUEDA VER DATOS
         * IMPORTANTES DE LA ACTIVACIÓN DE BOXES EN EL FORMULARIO DE COMPRA
         */
        $formData[AirBoxStripePlugin::PLUGIN_META_NONCE] = $this->_stripe->createStripeNonce(
                $this->getActivationKey());
        
        return $formData;
    }
    public final function getLabel() {
        return $this->getSetting(self::STRIPE_MODE,self::STRIPE_MODE_LIVE) === self::STRIPE_MODE_TEST ?
            parent::getLabel() . ' [ Test ]' :
            parent::getLabel();
    }
    /***
     * Cargar script de generado del botón ajax de compra
     */
    public final function doCommitButton() {
        //sobreescrito
        //parent::doCommitButton();
        /**
         * @var array Configuración del script
         */
        $arguments = array(
            'data-key' => $this->getPublicKey(),
            //nombre del proyecto/comercio
            'data-name' => AirBox::getOption('application_name'),
            //detalle y concepto del pedido
            'data-description' => $this->getOrderSummary(),
            //imagen a mostrar
            'data-image' => AirBoxRouter::Asset('coinbox-icon-1.png'),
            //cantidad (por defecto asume que los 2 últimos dígitos son los céntimos, por tanto x100)
            'data-amount' => $this->formatOrderValue(),
            //moneda (siempre EURO)? hay una parametrización básica creada en los parámetros de la intranet
            'data-currency' => $this->getCurrency(),
            //texto del botón
            'data-label' => AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_ORDER),
            //email del inversor
            'data-email' => AirBox::Instance()->getProfileData(AirBoxInvestorModel::FIELD_META_EMAIL),
            //otros parámetros del script
            'data-allow-remember-me' => 'false',
            'data-zip-code' => 'false',
        );
        
        $script_data = '';
        foreach( $arguments as $attr=>$value ){
            $script_data .= sprintf(' %s="%s"',$attr,$value);
        }
        
        return sprintf(
                '<script src="%s" data-locale="auto" class="stripe-button" %s></script>',
                self::STRIPE_AJAX_URL, $script_data);
    }
    /**
     * Genera el array de datos meta a procesar con la API para efectuar el cargo
     * @param string $tokenKey
     * @param email $email
     * @return array
     */
    private final function importTokenMeta( $tokenKey, $email ){
        return array(
            //conversión a centimos
            AirBoxStripePlugin::PLUGIN_META_AMOUNT => $this->formatOrderValue(),
            AirBoxStripePlugin::PLUGIN_META_CURRENCY => $this->getCurrency(),
            AirBoxStripePlugin::PLUGIN_META_DESCRIPTION => $this->getOrderSummary(),
            AirBoxStripePlugin::PLUGIN_META_TOKEN => $tokenKey,
            //numero de pedido y comprador
            AirBoxStripePlugin::PLUGIN_META_DATA => array(
                'AirBox Order ID'=>$this->getOrderId(),
                'Email'=>$email));
    }
    /**
     * Procesa el retorno del pago de la pasarela de Stripe
     * @param \AirBoxEventModel $e
     * @return boolean
     */
    public function dispatchCallBack(\AirBoxEventModel $e) {

        /**
         * @todo implementa aqui las variables de retorno
         * Variables a procesar o revisar en el evento
         */
        $success = false;
        //generado por el nonce enviado en el form con wp_create_nonce
        $nonce = $e->get(AirBoxStripePlugin::PLUGIN_META_NONCE,'');
        //email remitido en el formulario
        $email = $e->get(AirBoxStripePlugin::PLUGIN_STRIPE_EMAIL);
        //token recibido tras procesar el pago AJAX
        $tokenKey = $e->get(AirBoxStripePlugin::PLUGIN_STRIPE_TOKEN);
        //tipo de token (card siempre en este caso)
        $tokenType = $e->get(AirBoxStripePlugin::PLUGIN_STRIPE_TOKEN_TYPE,'invalid');
        
        //debugear...
        //die( json_encode( $e->getData() ) );
        
        if( $tokenType === 'card' && !is_null( $tokenKey) ){
            // Create the charge on Stripe's servers - this will charge the user's card
            try{
                ///preparar información meta para efectuar el cargo
                $data = $this->importTokenMeta($tokenKey, $email);
                
                if( !is_null( $this->_stripe->createCharge($data))) {
                    
                    parent::notifyCallbackSuccess();

                    //pago realizado WEEEEEE!!!!
                    $success = true;
                }
                else{
                    /**
                     * Algo ha petado, notificar convenientemente
                     * Revisar lista de errores posibles del stripe
                     * 
                     * y petar una excepción ya de paso
                     */
                    throw new Exception('No se ha podido instanciar la API Stripe para generar el cargo');
                }
            }
            catch(Exception $ex) {
                parent::notifyCallbackException($ex);
            }
        }
        
        //registrar mensajes generados implícitamente por el plugin de la passarela
        foreach( $this->_stripe->getLogs() as $log ){
            switch($log['type']){
                case 'error':
                    AirBoxNotifyModel::RegisterLog( $log,
                        AirBoxNotifyModel::LOG_TYPE_RUNTIME_ERROR,
                        get_class($this->_stripe));
                    break;
                case 'debug':
                    AirBoxNotifyModel::RegisterLog( $log,
                        AirBoxNotifyModel::LOG_TYPE_DEBUG,
                        get_class($this->_stripe));
                    break;
            }
        }
        
        return $success;
    }
}