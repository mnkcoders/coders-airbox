<?php defined('ABSPATH') or die;
/**
 * Cargador del inversor (intranet)
 */
class AirBoxInvestorBootStrap extends AirBox{
    //lista de opciones dispobibles para el inversor
    const INVESTOR_OPTION_UNACTIVATED = 'unactivated';
    const INVESTOR_OPTION_DASHBOARD = 'dashboard';
    const INVESTOR_OPTION_PROFILE = 'profile';
    const INVESTOR_OPTION_REFUND = 'request_refund';
    const INVESTOR_OPTION_TRANSACTIONS = 'transactions';
    //const INVESTOR_OPTION_INVEST = 'invest';  //OBSOLETO
    //const INVESTOR_OPTION_SIMULATION = 'simulation';
    //const INVESTOR_OPTION_SIMULATION = 'salepoints';
    const INVESTOR_OPTION_LOGOUT = 'logout';
    //BORRAR un pedido reservado
    const INVESTOR_OPTION_REMOVE_ORDER = 'remove_order';
    //completa el proceso de pago (si es automático, se completa solo, si manual, requiere de la intervención del adm)
    const INVESTOR_OPTION_CHECKOUT = 'checkout';

    //lista de comandos disponibles para el controlador
    //canjea AirPoints por Boxes
    //const INVESTOR_COMMAND_INVEST_EXCHANGE = 'exchange';
    //botón de actualización de form de datos de inversor en el form de perfil
    const INVESTOR_COMMAND_PROFILE_UPDATE = 'update';
    //opción de solicitud de recuperación de los airpoints en su valor real
    const INVESTOR_COMMAND_REFUND = 'refund';
    //lista de comandos disponibles en cada contexto/opción
    const INVESTOR_COMMAND_LOGOUT_EXIT = 'exit';
    //primero reservas las unidades en el form de pedidos
    const INVESTOR_COMMAND_ADD_TO_CART = 'add_to_cart';
    //procede al checkout cuando hay un pedido en curso
    const INVESTOR_COMMAND_PURCHASE = 'purchase';
    //activar pedido del inversor de manera automática (pasarela o reinversión de airpoints)
    const INVESTOR_COMMAND_COMMIT_ORDER = 'commit_order';
    
    /**
     * @var AirBoxInvestorModel Perfil de inversor
     */
    private $_investor = null;
    
    protected function __construct( array $settings = null) {
        
        /**
         * Registrar componentes del inversor aquí.
         */
        //Primero el plugin de Stripe, para evitar referencias inválidas durante la carga
        parent::registerComponent('Stripe', parent::COINBOX_COMPONENT_TYPE_PLUGIN );
        //modelo de formulario para el form de afiliación modo plantilla
        parent::registerComponent('Form' );
        //componente de checkout y pasarelas de pago
        parent::registerComponent('Checkout');

        //inicializar constructor principal
        parent::__construct($settings);
        
        
        /*if( parent::checkPlugin('Stripe')){
            echo 'Plugin Stripe cargado!!';
        }
        else{
            echo 'Puta Mierda Wordpress';
        }
        die;*/
    }
    /**
     * Carga el contenido de la intranet del inversor
     */
    public final function coinbox_content_loader() {

        if( get_post_type() === 'page' && get_the_ID() == parent::getOption('coinbox_page',0) ){

            $e = AirBoxEventModel::ImportInputVars();
            
            $activation_key = $e->get(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY );
            
            $investor = !is_null($activation_key) ?
                    //cargar inversor a través de la activación
                    AirBoxInvestorModel::ImportByInvestorKey( $activation_key ) :
                    //cargar al inversor importando un usuario validado WP
                    AirBoxInvestorModel::ImportFromUser(wp_get_current_user());

            if( !is_null( $investor ) ){
                
                //registrar la sesión de inversor para permitir su acceso a las acciones ejecutadas
                $this->_investor = $investor;
                
                /*
                 * ESTO SE HIZO PARA ACTIVAR AUTOMÁTICAMENTE EL PEDIDO DEL INVERSOR
                 * AL CURSAR EL ENLACE DE ACTIVACIÓN, DE MOMENTO UTILIZADO PARA LOS CASOS DE
                 * ACTIVACIÓN POR COMPRA CON AIRPOINTS.
                 * 
                 * TAMBBIÉN SERÍA UTILIZADO EN SU CONTEXTO POR LA PASARELA DE PAGO AL PROCEDER 
                 * CON LA COMPRA, SIN EMBARGO LA PARAMETRIZACIÓN SE  DEFINE DE UNA MANERA MUY DÉBIL Y 
                 * CONVIENE PROCEDER A LA ACTIVACIÓN POR MEDIO DE OTRA ACCIÓN DEL CONTROLADOR.
                 * 
                 * EL SIGUIENTE CODIGO SE MUEVE A LA ACCION: ACTIVATE_ORDER
                 * 
                 * 
                 * $order_id = $e->get(AirBoxOrderModel::FIELD_META_ID,0);
                 * if( $order_id ){
                 *   $this->_investor->activateOrder($order_id);
                 * }
                 */
                
                parent::coinbox_content_loader();
            }
            else{
                //si no hay inversor, no hay acceso
                $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_GUEST);
                
                $user = wp_get_current_user();
                
                $message = $user->ID > 0 ?
                            //usuario registrado, pero sin acceso a la intranet
                            AirBoxStringModel::__compose(
                                    AirBoxStringModel::LBL_WARNING_NOT_ALLOWED_USER,
                                    $user->user_login) :
                            //visitante
                            AirBoxStringModel::__(AirBoxStringModel::LBL_TITLE_ACCESS_RESTRICTED);
    
                //postear mensaje
                AirBoxNotifyModel::RegisterLog($message, AirBoxNotifyModel::LOG_TYPE_WARNING);
                //mostrar página estática con mensaje acceso no autorizado
                $R->Render('restricted');
            }
        }
    }
    /**
     * Acción por defecto
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_default_action( AirBoxEventModel $e ){

        if( !is_null($this->_investor)){
            
            $investor_status = $this->_investor->getStatus();
            $activation_key = $this->_investor->getActivationCode();
            
            $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_INVESTOR);

            $R->setModel( $this->_investor );
            
            switch( $investor_status ){
                case AirBoxInvestorModel::INVESTOR_STATUS_CLOSED:
                    $R->Render( AirBoxInvestorView::INVESTOR_OPTION_CLOSED_ACCOUNT );
                    break;
                case AirBoxInvestorModel::INVESTOR_STATUS_ACTIVE:
                case AirBoxInvestorModel::INVESTOR_STATUS_QUITTING:
                    
                    $view = $e->get(
                            AirBoxEventModel::EVENT_SELECTED_VIEW,
                            self::INVESTOR_OPTION_DASHBOARD);

                    if( $view === $activation_key ){
                        //si la vista es el id de inversor, mostrar una demo del panel de inversor
                        AirBoxNotifyModel::RegisterLog(
                                AirBoxStringModel::LBL_INFO_AFFILIATION_FORM_TIP,
                                AirBoxNotifyModel::LOG_TYPE_ADVICE);
                        $R->Render( 'affiliation' );
                    }
                    else{
                        $R->Render($view);
                    }
                    break;
                default:
                    AirBoxNotifyModel::RegisterLog(
                            AirBoxStringModel::__(AirBoxStringModel::LBL_INFO_ACCOUNT_ACTIVATION),
                            AirBoxNotifyModel::LOG_TYPE_ADVICE );
                    $R->Render(self::INVESTOR_OPTION_UNACTIVATED);
                    break;

            }
        }
    }
    /**
     * Solicita reintegro de AirPoints
     * Elmétodo se limita a enviar una notificación por email, el administrador debe recibirla y 
     * resolver el reintegro desde el back-end en la ficha de inversor
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_request_refund_action(AirBoxEventModel $e){

        //mensaje y contenido de la solicitud para enviar por email
        $request = $e->get('message' );
        //id de usuario para validar y referir al inversor
        $user_id = $e->get(AirBoxInvestorModel::FIELD_META_ID,0);
        //validación anti-spam
        $honeypot = $e->get('honeypot','');
        
        if( !is_null($this->_investor) && $this->_investor->getId() == $user_id && strlen($honeypot) == 0 && strlen($request) > 0 ){
            //enviar email
            
            $admin_subject = AirBoxStringModel::__compose(
                AirBoxStringModel::EML_SUBJECT_NOTIFY_REQUEST_REFUND,
                $this->_investor->getUserName());
            
            $investor_subject = AirBoxStringModel::__(
                AirBoxStringModel::EML_SUBJECT_NOTIFY_REFUND);
            
            $admin_content = AirBoxStringModel::__compose(
                    AirBoxStringModel::EML_CONTENT_NOTIFY_REQUEST,
                        array(AirBoxRouter::RouteAdmin(array(
                            AirBoxEventModel::EVENT_SELECTED_VIEW => 'profile',
                            AirBoxInvestorModel::FIELD_META_ID => $user_id,
                        )),
                        $this->_investor->getUserName(), $request));
            
            $investor_content = AirBoxStringModel::__compose(
                    AirBoxStringModel::EML_CONTENT_NOTIFY_REFUND, $request);
            
            $admin_email = get_option('admin_email');
            
            $investor_email = $this->_investor->getEmail();

            //preparar el envío para administrador
            $admin_message = AirBoxMailerService::createService();
            $admin_message->addRecipient($admin_email);
            $admin_message->setSubject( $admin_subject );
            $admin_message->setContent( $admin_content );
            parent::RegisterService($admin_message);
            //if( !$admin_message->dispatch() ){ }

            //preparar envío para el inversor
            $investor_message = AirBoxMailerService::createService();
            $investor_message->addRecipient($investor_email);
            $investor_message->setSubject($investor_subject);
            $investor_message->setContent($investor_content);
            parent::RegisterService($investor_message);
            
            AirBoxNotifyModel::RegisterLog(
                    AirBoxStringModel::EML_SUBJECT_NOTIFY_REFUND,
                    AirBoxNotifyModel::LOG_TYPE_INFORMATION );
        }
        
        $this->coinbox_default_action($e);
    }
    /**
     * Actualiza el perfil del inversor
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_update_profile_action( AirBoxEventModel $e ){
        /**
         * Actualiza los datos del pefil de inversor
         * Crear método que admita la actualización de datos a través del evento
         * Luego guardar
         */
    }
    /**
     * Permite a un inversor anular un pedido (de momento se borra directamebte de la BBDD)
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_remove_order_action(AirBoxEventModel $e ){
        
        $order_id = $e->get(AirBoxOrderModel::FIELD_META_ID,0);
        
        if( !is_null($this->_investor) && $order_id ){
            
            if( AirBoxOrderModel::RemoveOrder($this->_investor, $order_id) ){
                //agregar mensaje
                AirBoxNotifyModel::RegisterLog(AirBoxStringModel::LBL_INFO_ORDER_REMOVED);
            }
            else{
                //notificar error al borrar
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::__compose(
                                AirBoxStringModel::LBL_WARNING_CANNOT_REMOVE_ORDER,
                                $order_id),
                        AirBoxNotifyModel::LOG_TYPE_WARNING);
            }
        }
        //redirigir a la vista por defecto
        $this->coinbox_default_action($e);
    }
    /**
     * El proceso de compra no se ha completado correcatmente o ha sido suspendido por el cliente
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_cancel_order_action(AirBoxEventModel $e ){
        if( !is_null($this->_investor)){
            
            //$order_id = $e->get(AirBoxOrderModel::FIELD_META_ID,0);

            /**
             * Este evento indica que la compra no se ha completado.
             * Proceder a notificar debidamente 
             */
            
            if( !is_null( $view = $e->get(AirBoxEventModel::EVENT_SELECTED_VIEW)) ){
                //en este caso especial, la vista puede estar incluida en el evento
                //pero no necesariamente, por tanto procesar manualmente la redirección si existe
                $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_INVESTOR);

                $R->setModel( $this->_investor );
                
                $R->Render($view);
            }
        }
    }
    /**
     * Procesa la respuesta de retorno de una pasarela de pago tras completar el checkout
     * 
     * El evento contiene el ID de pedido asociado para cargarlo
     * De este se deduce la pasarela seleccionada
     * Dentro se procesa también la respuesta del pedido
     * 
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_commit_order_action( AirBoxEventModel $e ){

        if( !is_null($this->_investor)){
            //el inversor ha sido cargado leyendo esta clave, por si acaso, verificar de nuevo
            //desactivar despuésde revisar la carga del inversor
            $activation_key = $e->get(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY,'');

            $order_id = $e->get(AirBoxOrderModel::FIELD_META_ID,0);
            
            if( $this->_investor->getActivationCode() == $activation_key && $order_id ){
                
                $order = AirBoxOrderModel::LoadOrderById( $order_id,$this->_investor->getId() );

                $gateway = AirBoxCheckoutModel::LoadCheckOut($order);
                
                //Solo activar el pedido si el callback de la pasarela retorna TRUE
                if( !is_null($gateway) && $gateway->dispatchCallBack($e) ){
                    
                    if($this->_investor->activateOrder($order_id)){
                        $mailer = AirBoxMailerService::createService();
                        $mailer->addRecipient($this->_investor->getEmail());
                        $mailer->setSubject(AirBoxStringModel::__compose(
                            AirBoxStringModel::EML_SUBJECT_NOTIFY_ORDER_ACTIVATION,
                            $order_id));
                        $mailer->setContent( AirBoxStringModel::__compose(
                            AirBoxStringModel::EML_CONTENT_NOTIFY_ORDER_ACTIVATION,
                            array(
                                $this->_investor->getName(),
                                $order_id,
                                AirBoxRouter::RoutePublic())));
                        parent::RegisterService($mailer);

                        AirBoxNotifyModel::RegisterLog(AirBoxStringModel::LBL_INFO_ORDER_ACTIVATION);
                    }
                    else{
                        //no se ha `podido activar el pedido, contacte con el admin
                        //o asignar igualmente las unidades vendidas? conviene revisar este apartado
                        //el inverso YA ha pagado
                        AirBoxNotifyModel::RegisterLog(
                                AirBoxStringModel::__compose(
                                        'No se ha podido activar el pedido %s, consulte el administrador',
                                        $order_id),
                                AirBoxNotifyModel::LOG_TYPE_ERROR);
                    }
                }
                else{
                    //la pasarela no es válida o no se ha procesado correctamente el pago
                    AirBoxNotifyModel::RegisterLog(
                            AirBoxStringModel::__compose(
                                    'La pasarela %s no es v&aacute;lida o no se ha procesado el pago correctamente',
                                    AirBoxOrderModel::displayPaymentMethod($order->getPaymentMethod())),
                            AirBoxNotifyModel::LOG_TYPE_ERROR);
                }
            }
            else{
                //no es el inversor que ha comprado el pedido
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::__(
                                'La clave de activaci&oacute;n no corresponde al comprador'),
                        AirBoxNotifyModel::LOG_TYPE_ERROR);
            }

            if( !is_null( $view = $e->get(AirBoxEventModel::EVENT_SELECTED_VIEW)) ){
                //en este caso especial, la vista puede estar incluida en el evento
                //pero no necesariamente, por tanto procesar manualmente la redirección si existe
                $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_INVESTOR);

                $R->setModel( $this->_investor );
                
                $R->Render($view);
            }
        }
    }
    /**
     * Activa un pedido
     * 
     * Requiere del ID de pedido y de la CLAVE DE INVERSOR
     * 
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_activate_order_action( AirBoxEventModel $e ){
        die('');
        if( !is_null($this->_investor)){
            //el inversor ha sido cargado leyendo esta clave, por si acaso, verificar de nuevo
            //desactivar despuésde revisar la carga del inversor
            $activation_key = $e->get(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY,'');
            
            $order_id = $e->get(AirBoxOrderModel::FIELD_META_ID,0);

            ///verificar siempre que el inversor es el que dice ser antes de activar
            if( $this->_investor->getActivationCode() == $activation_key
                    && $this->_investor->activateOrder($order_id) ){
                //notificar a alguien?
                $mailer = AirBoxMailerService::createService();
                $mailer->addRecipient($this->_investor->getEmail());
                $mailer->setSubject(AirBoxStringModel::__compose(
                    AirBoxStringModel::EML_SUBJECT_NOTIFY_ORDER_ACTIVATION,
                    $order_id));
                $mailer->setContent( AirBoxStringModel::__compose(
                    AirBoxStringModel::EML_CONTENT_NOTIFY_ORDER_ACTIVATION,
                    array(
                        $this->_investor->getName(),
                        $order_id,
                        AirBoxRouter::RoutePublic())));
                //$mailer->dispatch();
                parent::RegisterService($mailer);
                
                AirBoxNotifyModel::RegisterLog(AirBoxStringModel::LBL_INFO_ORDER_ACTIVATION);
            }

            if( !is_null( $view = $e->get(AirBoxEventModel::EVENT_SELECTED_VIEW)) ){
                //en este caso especial, la vista puede estar incluida en el evento
                //pero no necesariamente, por tanto procesar manualmente la redirección si existe
                $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_INVESTOR);

                $R->setModel( $this->_investor );
                
                $R->Render($view);
            }
        }

        //no hacer nada si no hay vista que retornar
    }
    /**
     * El formulario de compra redirige a la pasarela de pago, salvo en el
     * caso de la pasarela de demo. Este evento captura este caso para
     * redirigir al evento activate_order
     * 
     * 
     * Procesar checkout (MODO DEMO)
     * @param AirBoxEventModel $e
     */
    /*protected final function coinbox_checkout_action( AirBoxEventModel $e ){

        //esta acción no será alcanzable fuera del entorno de demo
        $e->set(AirBoxEventModel::EVENT_SELECTED_VIEW, self::INVESTOR_OPTION_DASHBOARD );
        
        $this->coinbox_activate_order_action($e);
    }*/
    /**
     * Compra mas boxes (actualmente solo los reserva, el proceso de compra se resuelve de momento por transferencia)
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_purchase_action( AirBoxEventModel $e ){
        
        if( !is_null($this->_investor)){
            //vista por defecto del checkout
            $view = self::INVESTOR_OPTION_DASHBOARD;
            
            $order = AirBoxOrderModel::LoadOrderById(
                    $e->get(AirBoxOrderModel::FIELD_META_ID,0) ,
                    $this->_investor->getId() ) ;
            
            if( !is_null($order)){

                if( $order->getPaymentMethod() == AirBoxOrderModel::ORDER_PAYMODE_AIRPOINTS ){
                    //si el método de pago es con airpoints, activar directamente el pedido (omitir checkout)
                    
                    if( !$this->_investor->activateOrder($order->getId()) ){
                        //si algo va mal, notificar
                        AirBoxNotifyModel::RegisterLog(
                            AirBoxStringModel::LBL_WARNING_CANNOT_ACTIVATE_ORDER,
                            AirBoxNotifyModel::LOG_TYPE_WARNING);
                    }
                }
                elseif( $order->getAmount() <= AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0)){
                    //proceder con el checkout, con el método de pago seleccionado
                    $method = $e->get(
                            AirBoxOrderModel::FIELD_META_PAYMENT_METHOD,
                            AirBoxOrderModel::ORDER_PAYMODE_NONE);

                    if( $method > AirBoxOrderModel::ORDER_PAYMODE_NONE ){
                        $order->setPaymentMethod($method)->save();
                    }
                    
                    $view = self::INVESTOR_OPTION_CHECKOUT;
                }
                else{
                    /*
                     * las unidades del pedido ya no están disponibles.
                     * Es importante evitar el avance del proceso de checkout
                     */
                    AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::LBL_WARNING_OUT_OF_STOCK,
                        AirBoxNotifyModel::LOG_TYPE_WARNING);
                }
            }

            $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_INVESTOR);

            $R->setModel( $this->_investor );

            $R->Render( $view );
        }
        //ya no redirige a la vista seleccionada, en este caso siempre va a checkout
        //$this->coinbox_default_action($e);
    }
    /**
     * Agregar a la cesta como pedidos reservados
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_add_to_cart_action( AirBoxEventModel $e ){
        
        if( !is_null($this->_investor)){
            
            //cantidad de boxes
            $amount = $e->get( AirBoxOrderModel::FIELD_META_AMOUNT, 1 );
            
            //de momento solo permitir usar todos los airpoints disponibles
            $airpoints = intval( $e->get('airpoint_exchange',0) ) > 0 ?
                    $this->_investor->getAirPoints() : 0;

            $order = $this->_investor->addBoxPack($amount,$airpoints);
            
            if( !is_null($order) ){

                if( $order->getType() > AirBoxOrderModel::ORDER_TYPE_RESERVED ){
                    //la notificación se genera implícitamente, pero revisar si conviene moverla aqui
                    AirBoxNotifyModel::RegisterLog(AirBoxStringModel::LBL_INFO_ORDER_ACTIVATION);
                }
                else{
                    AirBoxNotifyModel::RegisterLog(AirBoxStringModel::LBL_INFO_ORDER_RESERVED);
                }
            }
            else{
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::LBL_WARNING_CANNOT_REGISTER_ORDER,
                        AirBoxNotifyModel::LOG_TYPE_WARNING );
            }
        }
        $this->coinbox_default_action($e);
    }
    /**
     * Carga la vista del form de simulación
     * Si se detectan parámetros del form en el evento, se importan sobre el form para mostrar el resultado
     * Solo disponible para inversores validados
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_simulation_action( AirBoxEventModel $e ){
        
        if( !is_null($this->_investor)
                && $this->_investor->getStatus() > AirBoxInvestorModel::INVESTOR_STATUS_PENDING ){
            
            $sim_form = $this->simulation_form_meta();

            $sim_form->importFromEvent($e);

            $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_INVESTOR);
            
            $R->setModel($sim_form);
            
            $R->Render(AirBoxInvestorView::INVESTOR_OPTION_SIMULATION);
        }
        else{
            $this->coinbox_default_action($e);
        }
    }
    /**
     * @return AirBoxFormModel formulario de simulación
     */
    protected final function simulation_form_meta(){
        
        $units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,100);
        
        $form = AirBoxFormModel::CreateForm(self::INVESTOR_OPTION_SIMULATION);
        
        $form->addFormField(
                AirBoxOrderModel::FIELD_META_AMOUNT,
                AirBoxDictionary::FIELD_TYPE_NUMBER,
                array(
                    'required'=>true,
                    'value'=>$units,
                    'minimum'=>1,
                    'maximum'=>$units,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_BOX_AMOUNT)));
        
        $form->addFormField( 'hours',
                AirBoxDictionary::FIELD_TYPE_NUMBER,
                array(
                    'required'=>true,
                    'value'=>6,
                    'minimum'=>1,
                    'maximum'=>8,
                    'label'=>AirBoxStringModel::__(AirBoxStringModel::LBL_TIME_AMOUNT)));
        
        $form->addFormField( 'price',
                AirBoxDictionary::FIELD_TYPE_CURRENCY,
                array(
                    'required'=>true,
                    'value'=>3.50,
                    'minimum'=>1.00,
                    'maximum'=>100.00,
                    'label'=> sprintf( '%s (€)',
                            AirBoxStringModel::__( AirBoxStringModel::LBL_PRICE_AMOUNT))));
        
        return $form;
    }
    /**
     * Consulta propiedades del inversor cargado
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getProfileData($name, $default = null) {
        if( !is_null($this->_investor)){
            switch( $name ){
                case AirBoxInvestorModel::FIELD_META_FULL_NAME:
                    return $this->_investor->getFullName();
                case AirBoxInvestorModel::FIELD_META_DOCUMENT_ID:
                    return $this->_investor->getDocumentId();
                case AirBoxInvestorModel::FIELD_META_INVESTOR_KEY:
                    return $this->_investor->getActivationCode();
                case AirBoxInvestorModel::FIELD_META_EMAIL:
                    return $this->_investor->getEmail();
                case AirBoxInvestorModel::FIELD_META_STATUS:
                    return $this->_investor->getStatus();
            }
        }
        return parent::getProfileData($name,$default);
    }
}

