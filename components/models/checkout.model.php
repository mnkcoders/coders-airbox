<?php defined('ABSPATH') or die;
/**
 * Descriptor del perfil de inversor y su relación con el resto del sistema de inversión
 * 
 * El modelo debe describir la composición del formulario de pago para cada pasarela,
 * así como el proceso de retorno del mismo para los pagos automáticos, donde tras pagar
 * se espera una respuesta del sistema.
 * 
 */
abstract class AirBoxCheckoutModel implements AirBoxIModel{

    const CHECKOUT_NAME = 'name';
    const CHECKOUT_LABEL = 'label';
    const CHECKOUT_CURRENCY = 'currency';
    const OPTION_BACK_BUTTON = 'back_button';
    
    /**
     * Lista de dependencias del checkout (plugins, etc)
     * @var array
     */
    private $_dependencies = array();
    
    /**
     * De momento un array. luego podría ser necesario en una tabla de bd
     * 
     * @var array Pasarelas de pago
     */
    private static $_gateways = array(
        /*AirBoxOrderModel::ORDER_PAYMODE_TESTER => array(
            self::CHECKOUT_NAME => 'debug',
            //self::CHECKOUT_LABEL => 'Modo desarrollo',
        ),*/
        //es solo una prueba
        /*AirBoxOrderModel::ORDER_PAYMODE_PAYPAL => array(
            self::CHECKOUT_NAME => 'paypal',
            //self::CHECKOUT_LABEL => 'PayPal',
        ),*/
        AirBoxOrderModel::ORDER_PAYMODE_CREDIT_CARD => array(
            self::CHECKOUT_NAME => 'stripe',
            //self::CHECKOUT_LABEL => 'Pasarela Stripe',
        ),
        AirBoxOrderModel::ORDER_PAYMODE_BANK_TRANSFER => array(
            self::CHECKOUT_NAME => 'bank_transfer',
            //self::CHECKOUT_LABEL => 'La Caixa',
        ),
    );
    /**
     * @var array Descriptor de parámetros de la pasarela
     */
    private $_settings = array(
        //identificador de la acción del botón de checkout
        self::CHECKOUT_NAME => 'default',
        //etiqueta de información a mostrar (tipo de pasarela)
        self::CHECKOUT_LABEL => 'Default',
        //euros por defecto, si ya, se cambia desde los parámetros generales luego
        self::CHECKOUT_CURRENCY => 'eur',
        //sin métodode pago por defecto (debe establecerse al cargar la pasarela con la info del pedido
        AirBoxOrderModel::FIELD_META_PAYMENT_METHOD => AirBoxOrderModel::ORDER_PAYMODE_NONE,
        //num de pedido
        AirBoxOrderModel::FIELD_META_ID => 0,
        //valor del pedido
        AirBoxOrderModel::FIELD_META_VALUE => 0,
        //cantidad de boxes
        AirBoxOrderModel::FIELD_META_AMOUNT => 0,
        AirBoxOrderModel::FIELD_META_AIRPOINTS => 0,
        //clave de activación para el inversor (url de confirmación de compra)
        //AirBoxInvestorModel::FIELD_META_INVESTOR_KEY => false,
        self::OPTION_BACK_BUTTON => true,
    );
    
    /**
     * Constructor con parámetros de la pasarela
     * 
     * Para registrar parámetros utilizar el método registerSetting()
     * en el constructor de la subclase antes de invocar al constructor principal
     * 
     * @param array $settings
     */
    protected function __construct( array $settings ) {

        //importar la configuración
        foreach( $settings as $var => $val ){
            if( isset($this->_settings[ $var ] ) ){
                $this->_settings[ $var ] = $val;
            }
        }
    }
    /**
     * Representa la pasarela en una cadena de texto
     * @return string
     */
    public function __toString() {
        return sprintf('<i>%s</i>(OrderID:<strong>%s</strong>)',
                get_class($this),$this->getOrderId());
    }
    /**
     * Comprueba las dependencias del componente para permitir su carga.
     * 
     * También generará una entrada en el diario de logs si no encuentra alguna dependencia
     * 
     * Este método permite sobreescritura a fin de inicializar las dependencias necesarias
     * desde las subclases si es preciso. El constructor no debe inicializarlas por que 
     * es inicializado directamente desde el cargador de la pasarela
     * 
     * @return boolean
     */
    protected function checkDependencies(){
        
        $success = true;
        
        foreach( $this->_dependencies as $type => $dependencies ){
            foreach( $dependencies as $name ){
                if( !AirBox::checkComponent($name, $type)){
                    AirBoxNotifyModel::RegisterLog(
                            sprintf('Componente inaccesible [%s]',
                                    AirBox::displayComponentType($name,$type)),
                            AirBoxNotifyModel::LOG_TYPE_DEBUG,
                            get_class($this));
                    $success = false;
                }
            }
        }
        return $success;
    }
    /**
     * Crea una dependencia del componente  hacia otro componente
     * @param string $component Nombre del componente a requerir
     * @param int $type Tipo de componente: modelo, plugin, servicio
     * @return bool Resultado del registro de la dependencia
     */
    protected final function registerDependency( $component, $type = AirBox::COINBOX_COMPONENT_TYPE_MODEL ){
        switch( $type ){
            case AirBox::COINBOX_COMPONENT_TYPE_MODEL:
            case AirBox::COINBOX_COMPONENT_TYPE_PLUGIN:
            case AirBox::COINBOX_COMPONENT_TYPE_SERVICE:
                if( !isset($this->_dependencies[$type]) ){
                    $this->_dependencies[$type] = array($component);
                }
                else{
                    $this->_dependencies[$type][] = $component;
                }
                return true;
        }
        return false;
    }
    /**
     * Permite establecer parámetros de configuración para la pasarela de pago.
     * Conviene registrarlos todos en el constructor heredado, antes de invocar al constructor
     * de la superclase.
     * 
     * @param String $setting
     * @param mixed $default
     * @return boolean
     */
    protected final function  registerSetting( $setting, $default = null ){
        if( !isset($this->_settings[$setting]) || !is_null($default) ){
            $this->_settings[$setting] = !is_null($default) ? $default : false;
            return true;
        }
        return false;
    }
    /**
     * Parámetro de la pasarela
     * @param string $setting
     * @param mixed $default
     * @return mixed
     */
    protected final function getSetting( $setting , $default = null ){
        return isset( $this->_settings[$setting] ) ? $this->_settings[$setting] : $default;
    }
    /**
     * @return array Retorna un array de contenidos a mostrar en la vista de checkout
     */
    public abstract function getContent();
    /**
     * Retorna un conjunto de valores requeridos para el formulario de compra adaptable a las
     * necesidades de cada pasarela en concreto, con valores tales como el id de pedido, valor
     * a pagar, url de retorno, etc.
     * 
     * Valores por defecto los requeridos para el evento commit, luego cada pasarela que cambie
     * o implemente los que necesite sobre el array
     * 
     * @return array parámetros de formulario (ocultos)
     */
    public function getFormData(){
        return array(
            //AirBoxEventModel::EVENT_TYPE_COMMAND => AirBoxInvestorBootStrap::INVESTOR_OPTION_ACTIVATE_ORDER,
            AirBoxInvestorModel::FIELD_META_INVESTOR_KEY => $this->getActivationKey(),
            AirBoxOrderModel::FIELD_META_ID=>$this->getOrderId(),
            AirBoxEventModel::EVENT_SELECTED_VIEW => AirBoxInvestorBootStrap::INVESTOR_OPTION_DASHBOARD,
        );
    }
    /**
     * @return URL Define la url que procesará los datos del formulario. Por defecto el panel de inversor
     */
    public function getActionUrl(){
        
        return AirBoxRouter::RoutePublic( );
    }
    /**
     * @return String Nombre o etiqueta del método de pago
     */
    public function getLabel(){
        return isset($this->_settings[self::CHECKOUT_LABEL]) ?
            $this->_settings[self::CHECKOUT_LABEL] : '--';
    }
    /**
     * @return String Nombre o etiqueta del método de pago
     */
    public final function getName(){
        return isset($this->_settings[self::CHECKOUT_NAME]) ? 
            $this->_settings[self::CHECKOUT_NAME] : 'default';
    }
    /**
     * @return int Método de pago
     */
    public final function getMethod(){
        return isset($this->_settings[AirBoxOrderModel::FIELD_META_PAYMENT_METHOD]) ?
            $this->_settings[AirBoxOrderModel::FIELD_META_PAYMENT_METHOD] :
            AirBoxOrderModel::ORDER_PAYMODE_NONE;
    }
    /**
     * Muestra la cantidad a pagar
     * @param bool $showdiscount Mostrar descuento en AP
     * @return int Cantidad a pagar
     */
    public final function getOrderValue( ){
        return isset($this->_settings[AirBoxOrderModel::FIELD_META_VALUE]) ?
            $this->_settings[AirBoxOrderModel::FIELD_META_VALUE] : 0;
    }
    /**
     * @return int AirPoints del descuento
     */
    public final function getAirPoints(){
        return isset($this->_settings[AirBoxOrderModel::FIELD_META_AIRPOINTS]) ?
            $this->_settings[AirBoxOrderModel::FIELD_META_AIRPOINTS] : 0;
    }
    /**
     * @return int Id de pedido
     */
    public final function getOrderId(){
        return isset($this->_settings[AirBoxOrderModel::FIELD_META_ID])?
            $this->_settings[AirBoxOrderModel::FIELD_META_ID] : 0;
    }
    /**
     * @return String Resumen del pedido (concepto a incluir en las pasarelas de pago)
     */
    public final function getOrderSummary(){
        return isset($this->_settings[AirBoxOrderModel::FIELD_META_AMOUNT]) ?
            sprintf('%s %s',
                    $this->_settings[AirBoxOrderModel::FIELD_META_AMOUNT],
                    AirBoxStringModel::LBL_BOX_AMOUNT ) : '';
    }
    /**
     * Clave de activación del perfil cargado en la sesión
     * @return String
     */
    protected final function getActivationKey( ){
        return AirBox::Instance()->getProfileData(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY);
        //return $this->_settings[AirBoxInvestorModel::FIELD_META_INVESTOR_KEY];
    }
    /**
     * Puede ser necesario requerir una variable mas para comprobar el estado del retorno de la url
     * @param array $override Describe campos personalizados en la url de retorno
     * @return URL Activación automática del pedido al proceder con la compra
     */
    protected final function getCallBackUrl( array $override = null ){
        
        /**
         * @var array Parámetros de la redirección en la confirmación de compra
         */
        $params = array(
            //aqui no utilizar constantes por que no sabemos si el cargador en uso es el de inversor
            AirBoxEventModel::EVENT_TYPE_COMMAND => AirBoxInvestorBootStrap::INVESTOR_COMMAND_COMMIT_ORDER,
            //la compra debe procesarse validando la clave de inversor
            AirBoxInvestorModel::FIELD_META_INVESTOR_KEY => $this->getActivationKey(),
            //num de pedido
            AirBoxOrderModel::FIELD_META_ID=>$this->getOrderId(),
            //la compra se ejecuta manualmente desde el apartado de inversor, por tanto retornar al panel de control
            AirBoxEventModel::EVENT_SELECTED_VIEW=> AirBoxInvestorView::INVESTOR_MENU_DASHBOARD
        );
        
        if( !is_null($override) ){
            foreach( $override as $option => $value ){
                switch($option){
                    case AirBoxInvestorModel::FIELD_META_INVESTOR_KEY:
                    case AirBoxOrderModel::FIELD_META_ID:
                        //no tocar
                        break;
                    default:
                        //sobreescribir
                        $params[$option] = $value;
                        break;
                }
            }
        }
        
        return AirBoxRouter::RoutePublic( $params );

    }
    /**
     * @return bool Mostrar Botón volver atrás
     */
    public final function getBackButton(){ return $this->_settings['back_button']; }
    /**
     * Mostrar Botón de procesar pago
     * @return HTML Input, botón o control de ejecución del pago
     */
    public function doCommitButton(){
        return AirBoxRenderer::renderSubmit(
            AirBoxEventModel::EVENT_TYPE_COMMAND,
            AirBoxInvestorBootStrap::INVESTOR_COMMAND_COMMIT_ORDER,
            AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_CHECKOUT), 'intense');
    }
    /**
     * Cargar pasarela de pago
     * @param int $method Método de pago (CC,TRANSFER,PAYPAL ...)
     * @return AirBoxCheckoutModel | NULL
     */
    public static final function LoadCheckOut( AirBoxOrderModel $order ){
        
        //esto debería pasarse a cargar desde la BD, de momento es un array estático
        if( isset(self::$_gateways[$order->getPaymentMethod()]) ){
            
            $name = self::$_gateways[$order->getPaymentMethod()][self::CHECKOUT_NAME];

            $gw_data = self::$_gateways[$order->getPaymentMethod()];

            $gw_data[AirBoxOrderModel::FIELD_META_PAYMENT_METHOD] = $order->getPaymentMethod();
            $gw_data[AirBoxOrderModel::FIELD_META_ID] = $order->getId();
            $gw_data[AirBoxOrderModel::FIELD_META_AMOUNT] = $order->getAmount();
            $gw_data[AirBoxOrderModel::FIELD_META_VALUE] = $order->getOrderValue();
            $gw_data[AirBoxOrderModel::FIELD_META_AIRPOINTS] = $order->getAirPoints();

            return self::LoadGateWay($name, $gw_data);
        }
        
        return null;
    }
    /**
     * Carga una pasarela de pago (instancia de AirBoxCheckoutModel)
     * @param string $gateway
     * @param array $data
     * @return \AirBoxCheckoutModel|null
     */
    private static final function LoadGateWay( $gateway, array $data ){
        
        $path = sprintf(
                '%scomponents/checkout/%s.gateway.php',
                CODERS__COINBOX__DIR, $gateway );

        if( file_exists( $path ) ){

            $GW = sprintf('AirBoxCheckout%sGateWay',
                preg_replace( '/_/' , '', $gateway) );

            require_once $path;

            if(class_exists($GW)){

                $instance =  new $GW( $data );
                        
                //importante comprobar las dependencias antes que retornarlo
                if( method_exists($instance, 'checkDependencies') && $instance->checkDependencies() ){
                    //todo correcto, retorna la pasarela
                    //die($instance->__toString());
                    return $instance;
                }
                else{

                    //notificar al admin
                    AirBoxNotifyModel::RegisterLog(
                            'No se puede cargar el método de pago por que depende de uno o varios componentes inaccesibles',
                            AirBoxNotifyModel::LOG_TYPE_ERROR);
                }
            }
        }
        
        return null;
    }
    /**
     * Obtiene la lista de pasarelas de pago configuradas
     * 
     * @return array Lista asociada de pasarelas de pago por nombre => tipo
     */
    public static final function ListGateways(){
        
        $list = array();
        
        foreach( self::$_gateways as $method => $parameters ){
        
            $list[$method] = $parameters[self::CHECKOUT_NAME];
        }

        return $list;
    }
    /**
     * Procesa la respuesta del pago y remite las notificaciones pertinentes
     * @param AirBoxEventModel $e
     * @return bool Resultado del proceso
     */
    abstract public function dispatchCallBack( AirBoxEventModel $e );
    /**
     * Genera una notificación de la correcta ejecución del cargo/medio de pago
     */
    protected final function notifyCallbackSuccess(){
        AirBoxNotifyModel::RegisterLog(
                sprintf('El pago del pedido Nº<strong>%s</strong> ha sido procesado',
                        $this->getOrderId() ),
                AirBoxNotifyModel::LOG_TYPE_ADVICE,
                get_class($this));
    }
    /**
     * Notifica un error en timepo de ejecución genérico registrando la respuesta del callback
     * y su error en el log del framework
     * 
     * @param Exception $ex
     */
    protected final function notifyCallbackException( Exception $ex ){
        //log directo al diario de seguimiento
        AirBoxNotifyModel::RegisterException($ex,get_class($this));
        //notificación para el cliente que no quede mosca
        AirBoxNotifyModel::RegisterLog(
                sprintf('Error al procesar el pago del pedido Nº<strong>%s</strong>. Contacte con el administrador',
                    $this->getOrderId()),
                AirBoxNotifyModel::LOG_TYPE_ERROR,
                get_class($this));
        
        /**
         * Notificar de los errores en tiempo de ejecución por email?
         */
    }
}

