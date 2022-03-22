<?php defined('ABSPATH') or die;
/**
 * Cargador del invitado (acceso general front-end)
 */
class AirBoxGuestBootStrap extends AirBox{
    
    //const GUEST_OPTION_LOGIN_FORM = 'login_form';
    
    //const GUEST_OPTION_INVESTOR_FORM = 'investor_form';
    
    const GUEST_ACTION_REGISTER = 'register';
    
    const GUEST_ACTION_LOGIN = 'login';
    
    /**
     * @var AirBoxInvestorModel Perfil de inversor registrado para facilitar selección de vistas
     */
    private $_investor = null;
    
    protected function __construct( array $settings = null ) {
        
        parent::registerComponent('Form', parent::COINBOX_COMPONENT_TYPE_MODEL );

        parent::__construct($settings);

    }
    /**
     * Carga el contenido de la parte pública
     * 
     * Form general de inversión
     * 
     * Panel de inicio de sesión
     */
    public final function coinbox_content_loader() {
        
        if( get_post_type() === 'page' && get_the_ID() == parent::getOption('coinbox_page',0) ){
            
            //capturar el evento para comprobar si hay datos adjuntos que procesar
            //$e = AirBoxEventModel::ImportInputVars();

            //ejecutar la llamada de la acción, si no hay acción, mostrar vistas por defecto
            /*if( !$this->callAction( $e, 'default' ) ){

            } */
            parent::coinbox_content_loader();
        }
    }
    /**
     * Ejecuta la acción y vista por defecto
     * @param \AirBoxEventModel $e
     */
    protected final function coinbox_default_action(\AirBoxEventModel $e) {

        $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_GUEST);

        $view = $e->get(
                AirBoxEventModel::EVENT_SELECTED_VIEW,
                AirBoxGuestView::VIEW_LOGIN_FORM);

        switch( $view ){

            case AirBoxGuestView::VIEW_LOGIN_FORM:
                //$R->setModel(self::login_form_meta());
                //el caso por defecto (sin vista seleccionada) es login
                //pero conviene no incluirlo como default, ya que el default serán los casos
                //en los que la vista seleccionada sea el id de formulario del inversor
                $R->Render( AirBoxGuestView::VIEW_LOGIN_FORM );
                break;
            case AirBoxGuestView::VIEW_AFFILIATION_FORM:
                //si la vista indica explicitamente el form de afiliación
                $form = self::register_form_meta($e->get(
                        AirBoxInvestorModel::FIELD_META_INVESTOR_KEY));

                $R->setModel( $form );

                $R->Render( AirBoxGuestView::VIEW_AFFILIATION_FORM );

                break;
            default:
                //si la vista no indica form de afiliación ni login,
                //previsiblemente indica el id de inversor para crear un form
                //de afiliación a un inversor padre
                $form = self::register_form_meta($view);

                $R->setModel( $form );

                $R->Render( AirBoxGuestView::VIEW_AFFILIATION_FORM );

                break;
        }
    }
    /**
     * Procesa la entrada de datos del form de inversor y lo da de alta
     * Valida los datos del formulario
     * Crea un usuario wordpress si es correcto
     * Crea un nuevo inversor si es correcto
     * Genera los movimientos y reglas de entrada del nuevo inversor
     * Comprueba el form de origen del nuevo inversor
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_register_action(AirBoxEventModel $e ){
        //genera la plantilla del form de inversor, agregando el id de formulario si existe
        $formData = self::register_form_meta( $e->get( AirBoxInvestorModel::FIELD_META_INVESTOR_KEY) );
        //importa los datos del evento sobre el form (acaba de ser procesado y contiene los datos del form)
        $formData->importFromEvent($e);
        
        try{
            
            $box_amount = $formData->getValue( AirBoxInvestorModel::FIELD_META_BOXES , 1 );
            
            if(AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0) < $box_amount){
                throw new Exception(AirBoxStringModel::LBL_WARNING_OUT_OF_STOCK);
            }
            
            $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_GUEST);

            //comprueba que el nuevo afiliado ha sido validado y ...
            if( $formData->validateFormData() ){
                
                //crea el inversor y su usuario asociado
                $investor = AirBoxInvestorModel::CreateInvestor( $formData );
                
                //validar que el inversor es valido y hay boxes seleccionados
                if( !is_null($investor) && ($investor_id = $investor->getId()) > 0 ){
                    
                    //asigna los boxes, reservados por defecto
                    //incluye implícitamente una transacción
                    $order = $investor->addBoxPack($box_amount );
                    
                    if( is_null( $order) ){
                        /**
                         * No se ha podido completar la reserva por que faltan boxes o no hay disponibles
                         */
                        AirBoxNotifyModel::RegisterLog(
                            AirBoxStringModel::LBL_WARNING_CANNOT_REGISTER_ORDER,
                            AirBoxNotifyModel::LOG_TYPE_WARNING);
                    }
                    
                    //de todos modos, permitir acceder a la intranet como inversor no activado
                    $loginForm = self::login_form_meta($investor->getUserName());
                    
                    //Genera una notificación por email
                    $this->mail_signup_investor($formData);
                    $this->mail_signup_admin($investor);
                    
                    //redireccionar wordpress a la página de login para acceder a la intranet
                    $R->setModel( $loginForm );
                    //registra el mensaje de inicio de sesión
                    AirBoxNotifyModel::RegisterLog( 
                            AirBoxStringModel::__compose(
                            AirBoxStringModel::LBL_INFO_SIGNUP_LOGIN,
                            $investor->getValue(AirBoxInvestorModel::FIELD_META_FIRST_NAME)),
                        AirBoxNotifyModel::LOG_TYPE_INFORMATION);
                    //mostrará nombre de usuario si acaba de ser creado
                    $R->Render( AirBoxGuestView::VIEW_LOGIN_FORM );
                }
                else{
                    //devolver form con mensajes de error de validación
                    $R->setModel( $formData );

                    AirBoxNotifyModel::RegisterLog(
                            AirBoxStringModel::LBL_WARNING_CANNOT_CREATE_USER,
                            AirBoxNotifyModel::LOG_TYPE_WARNING);

                    //la vista de formulario detectará los errores y los mostrará
                    $R->Render( AirBoxGuestView::VIEW_AFFILIATION_FORM );
                }
            }
            else{
                //devolver form con mensajes de error de validación
                $R->setModel( $formData );
                
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::LBL_WARNING_AFFILIATION_FORM_FIELD_ERROR,
                        AirBoxNotifyModel::LOG_TYPE_WARNING);

                //la vista de formulario detectará los errores y los mostrará
                $R->Render( AirBoxGuestView::VIEW_AFFILIATION_FORM );
            }
        }
        catch (Exception $ex) {
            die(AirBoxNotifyModel::RegisterException($ex)->getMessage());
        }
    }
    /**
     * @return AirBoxFormModel Generador del form de datos de login
     */
    private static final function login_form_meta( $user ){
        
        $form = AirBoxFormModel::CreateForm('Login');
        
        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_USER_NAME,
                AirBoxDictionary::FIELD_TYPE_TEXT,
                array(
                    'value' => !is_null( $user ) ? $user : '',
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_USER_NAME)));
        
        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_USER_PASS,
                AirBoxDictionary::FIELD_TYPE_PASSWORD,
                array(
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_USER_PASS)));
        
        return $form;
    }
    /**
     * @param string $form_key Clave de formulario de inversor
     * @return AirBoxFormModel Método para generar el form de datos de registro del front-end
     */
    private static final function register_form_meta( $form_key = null ){

        $form = AirBoxFormModel::CreateForm('Investor');
        
        //obtener el maximo de boxes desde una query directa para conocer cuantos quedan disponibles
        //$maxBoxes = AirBoxOrderModel::CountAvailableBoxes(true);
        $maxBoxes = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);
        
        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_USER_NAME,
                AirBoxDictionary::FIELD_TYPE_TEXT,
                array(
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_USER_NAME)));
        
        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_USER_PASS,
                //de momento no ofuscar la clave al escribirla
                //AirBoxDictionary::FIELD_TYPE_PASSWORD,
                AirBoxDictionary::FIELD_TYPE_TEXT,
                array(
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_USER_PASS)));

        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_FIRST_NAME,
                AirBoxDictionary::FIELD_TYPE_TEXT,
                array(
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_FIRST_NAME)));
        
        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_LAST_NAME,
                AirBoxDictionary::FIELD_TYPE_TEXT,
                array(
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_LAST_NAME)));

        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_DOCUMENT_ID,
                AirBoxDictionary::FIELD_TYPE_TEXT,
                array(
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_DOCUMENT_ID)));

        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_EMAIL,
                AirBoxDictionary::FIELD_TYPE_EMAIL,
                array(
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_EMAIL)));

        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_TELEPHONE,
                AirBoxDictionary::FIELD_TYPE_TELEPHONE,
                array(
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_PHONE)));

        $form->addFormField(
                AirBoxInvestorModel::FIELD_META_BOXES,
                AirBoxDictionary::FIELD_TYPE_NUMBER,
                array(
                    'required'=>true,
                    'label'=> AirBoxStringModel::__(AirBoxStringModel::LBL_BOX_AMOUNT),
                    'minimum'=>1,
                    'maximum'=>$maxBoxes,
                    'value'=>1) );
        
        if( !is_null( $form_key ) ){
            //si el formulario es referido por un inversor, incluir su hash para proporcionar la recompensa adecuada
            $form->addFormField(
                AirBoxInvestorModel::FIELD_META_INVESTOR_KEY,
                AirBoxDictionary::FIELD_TYPE_HIDDEN,
                array('value'=>$form_key));
        }
        
        return $form;
    }
    /**
     * Genera la notificación por email de alta en la intranet
     * @param AirBoxInvestorModel $investor
     * @return bool
     */
    private final function mail_signup_investor( AirBoxFormModel $formData ){
        
        $email = $formData->getValue(AirBoxInvestorModel::FIELD_META_EMAIL);
        
        $content_data = array(
            $formData->getValue(AirBoxInvestorModel::FIELD_META_FIRST_NAME),
            AirBox::getOption('application_name'),
            $formData->getValue(AirBoxInvestorModel::FIELD_META_USER_NAME),
            $formData->getValue(AirBoxInvestorModel::FIELD_META_USER_PASS));
        
        $subject = AirBoxStringModel::__(AirBoxStringModel::EML_SUBJECT_NOTIFY_WELCOME);
        
        $content = AirBoxStringModel::__compose(
            AirBoxStringModel::EML_CONTENT_NOTIFY_WELCOME,
            $content_data );
        
        $mailer = AirBoxMailerService::createService();
        $mailer->addRecipient( $email );
        $mailer->setSubject($subject);
        $mailer->setContent($content);
        $mailer->addContent(AirBoxRenderer::renderInvestorShortCutLink());
        return parent::RegisterService($mailer) > 0;
        //return $mailer->dispatch();
    }
    /**
     * Genera la notificación por email del administrador al registrarse un inversor
     * @param AirBoxInvestorModel $investor
     * @return bool
     */
    private final function mail_signup_admin( AirBoxInvestorModel $investor ){
        
        $profile_url = AirBoxRouter::RouteAdmin( array(
            AirBoxEventModel::EVENT_SELECTED_VIEW=>'profile',
            AirBoxInvestorModel::FIELD_META_ID=>$investor->getId()));
        
        $subject = AirBoxStringModel::__compose(
                AirBoxStringModel::EML_SUBJECT_NOTIFY_SIGNUP,
                $investor->getUserName() );

        $content = AirBoxStringModel::__(AirBoxStringModel::EML_CONTENT_NOTIFY_SIGNUP);

        $mailer = AirBoxMailerService::createService();
        $mailer->addRecipient(get_option('admin_email'));
        $mailer->setSubject($subject);
        $mailer->setContent(sprintf('<p>%s:</p>',$content));
        $mailer->addContent(sprintf('<p><a href="%s" target="_blank">%s</a></p>',
                $profile_url,
                $investor->getFullName()));

        return parent::RegisterService($mailer) > 0;
        //return $mailer->dispatch();
    }
    /**
     * No es útil para este perfil
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getProfileData($name, $default = null) {
        switch( $name ){
            case 'profile':
                return parent::COINBOX_PROFILE_GUEST;
        }
        return $default;
    }
}

