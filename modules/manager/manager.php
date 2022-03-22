<?php defined('ABSPATH') or die;
/**
 * Cargador del administrador (back-end)
 */
class AirBoxManagerBootStrap extends AirBox{
    /**
     * Vistas disponibles de la aplicación
     * Parte privada
     */
    //const ADMIN_OPTION_DEFAULT = 'default';
    const ADMIN_OPTION_ROOT = 'coinbox';
    const ADMIN_OPTION_MAIN = 'dashboard';
    const ADMIN_OPTION_SETTINGS = 'settings';
    const ADMIN_OPTION_MILESTONES = 'milestones';
    const ADMIN_OPTION_INVESTORS = 'investors';
    const ADMIN_OPTION_INVESTOR_PROFILE = 'profile';
    const ADMIN_OPTION_TRANSACTIONS = 'transactions';
    const ADMIN_OPTION_BOXES = 'boxes';
    const ADMIN_OPTION_NOTIFIER = 'notifier';
    const ADMIN_OPTION_LOGS = 'logs';
    
    const ADMIN_COMMAND_CLEAR_LOG = 'clear_log';
    
    const ADMIN_OPTION_APPLY_REFUND = 'apply_refund';
    const ADMIN_OPTION_ACTIVATE_INVESTOR = 'activate_investor';
    const ADMIN_OPTION_SET_MILESTONE = 'set_milestone';
    const ADMIN_OPTION_REMOVE_MILESTONE = 'remove_milestone';
    const ADMIN_OPTION_CLEAR_MILESTONES = 'clear_milestones';
    const ADMIN_OPTION_REMOVE_UNLINKED = 'remove_unlinked';
    const ADMIN_OPTION_GENERATE_TRANSLATION = 'generate_translation';
    //const ADMIN_OPTION_SYSTEM = 'system';
    
    const ADMIN_FRONT_END = 'frontend';
    
    private static $_aminMenu = array();

    protected function __construct( array $settings = null ) {

        $appName = parent::getOption('application_name', 'AirBox');

        //agregando navegación. La primera opción es la que marca el menú principal
        self::defineMenuItem( self::ADMIN_OPTION_MAIN, $appName, $appName);

        self::defineMenuItem(self::ADMIN_OPTION_MILESTONES,
                AirBoxStringModel::__( 'Ciclos' ),
                AirBoxStringModel::__( 'Configuraci&oacute;n de ciclos y objetivos' ));
        self::defineMenuItem(self::ADMIN_OPTION_INVESTORS,
                AirBoxStringModel::__('Inversores'),
                AirBoxStringModel::__('Directorio de Inversores'));
        self::defineMenuItem(self::ADMIN_OPTION_BOXES,
                AirBoxStringModel::__( 'Boxes' ),
                AirBoxStringModel::__( 'Resumen de inversi&oacute;n' ));
        self::defineMenuItem(self::ADMIN_OPTION_TRANSACTIONS,
                AirBoxStringModel::__( 'Transacciones' ),
                AirBoxStringModel::__( 'Registro global de transacciones' ));
        /*self::defineMenuItem(self::ADMIN_OPTION_NOTIFIER,
                AirBoxStringModel::__( 'Notificaciones' ),
                AirBoxStringModel::__( 'Notificaciones del sistema' ));*/
        self::defineMenuItem(self::ADMIN_OPTION_SETTINGS,
                AirBoxStringModel::__( 'Par&aacute;metros' ),
                AirBoxStringModel::__( 'Par&aacute;metros del sistema' ));
        self::defineMenuItem(self::ADMIN_OPTION_LOGS,
                AirBoxStringModel::__( 'Logs' ),
                AirBoxStringModel::__( 'Logs del sistema' ));

        /**
         * Registrar componentes del administrador aquí
         */
        parent::__construct($settings);
        
        add_action( AirBoxCMSModel::WP_HOOK_ADMIN_MENU, array($this,'admin_menu_loader'), 50 );
        
    }
    /**
     * Permite definir opciones para un menú de navegación al cargar y generar la vista del componente
     * @param String $option Nombre únicop o Slug WP para la URL
     * @param String $label Etiqueta del menú
     * @param String $title Título de la opción o página a que vincula
     * @param array $meta Información meta adicional para su contexto apropiado, si se requiere
     */
    private static final function defineMenuItem( $option, $label, $title = null , array $meta = null ){
        
        self::$_aminMenu[$option] = array(
            'name'=>$option,
            'label'=>$label,
            'title'=>!is_null($title) ? $title : $label,
        );
            
        if( !is_null($meta) ){
            //adjuntar información meta adicional si es necesario para gestionar en su contexto apropiado
            foreach( $meta as $var=>$val ){
                if( !isset(self::$_aminMenu[$option][$val] ) ){
                    self::$_aminMenu[$option][$val] = $var;
                }
            }
        }
    }
    /**
     * Carga el menú de administración
     */
    public final function admin_menu_loader(){
        if( is_admin() ){

            //inicializa la visualización de mensajes de administrador
            //AirBoxNotifyModel::showAdminMessages();

            $mainOption = null;

            foreach( self::$_aminMenu as $option => $content ){
                if( is_null($mainOption) ){

                    $mainOption = $option;

                    add_menu_page(
                        AirBoxStringModel::__($content['title']),
                        AirBoxStringModel::__($content['label']),
                        'administrator', 
                        //sprintf('coinbox-%s',$option),
                        self::ADMIN_OPTION_ROOT,
                        array($this,'coinbox_content_loader'),
                        CODERS__COINBOX__URL.'assets/icon-admin.png');
                }
                else{
                    add_submenu_page(
                        //sprintf('coinbox-%s',self::ADMIN_OPTION_MAIN),
                        self::ADMIN_OPTION_ROOT,
                        AirBoxStringModel::__($content['title'] ),
                        AirBoxStringModel::__($content['label'] ),
                        'administrator', 
                        //sprintf('coinbox-%s&view=%s',$mainOption,$option),
                        sprintf('%s&view=%s',self::ADMIN_OPTION_ROOT,$option),
                        array($this,'coinbox_content_loader'));
                }
            }
        }
    }
    /**
     * Función HOOK de carga del manager en la administración
     */
    public final function coinbox_content_loader() {
        
        //$e = AirBoxEventModel::ImportInputVars();
        
        parent::coinbox_content_loader();
    }
    /**
     * Acción por defecto del cargador
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_default_action( AirBoxEventModel $e ){

        //cargar gestor de vistas del manager
        $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_MANAGER);
        
        if (is_admin()) {

            $view = $e->get(
                    AirBoxEventModel::EVENT_SELECTED_VIEW,
                    self::ADMIN_OPTION_MAIN);

            switch($view){
                case self::ADMIN_OPTION_INVESTOR_PROFILE:

                    $investor = AirBoxInvestorModel::ImportById(
                            $e->get(AirBoxInvestorModel::FIELD_META_ID,0));

                    if( !is_null($investor) ){
                        $R->setModel($investor);
                        $R->Render(self::ADMIN_OPTION_INVESTOR_PROFILE);
                    }
                    else{
                        echo AirBoxStringModel::__( AirBoxStringModel::LBL_ERROR_INVALID_INVESTOR );
                        $R->Render(self::ADMIN_OPTION_INVESTORS);
                    }
                    break;
                case self::ADMIN_OPTION_INVESTORS:

                    $investor_id = $e->get(AirBoxInvestorModel::FIELD_META_ID,0);

                    if( $investor_id ){
                        //si hay un ID de inversor, cargarlo como modelo
                        $R->setModel(AirBoxInvestorModel::ImportById($investor_id));
                        $R->Render(self::ADMIN_OPTION_INVESTOR_PROFILE);
                    }
                    else{
                        $R->Render($view);
                    }
                    break;
                case self::ADMIN_OPTION_BOXES:
                    
                    $type = $e->get(AirBoxOrderModel::FIELD_META_TYPE);
                    
                    if( !is_null( $type )){
                        //capturar filtros de tipo de boxes
                        $R->set(AirBoxOrderModel::FIELD_META_TYPE, $type);
                    }
                    $R->Render($view);
                    break;
                default;
                    $R->Render($view);
                    break;
            }

        }
        elseif ( get_post_type() === 'page' && get_the_ID() == parent::getOption('coinbox_page', 0)) {
            //mostrar botón de acceso al administrador
            $R->Render(self::ADMIN_FRONT_END);
        }
    }
    /**
     * Vaciar los logs
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_clear_log_action( AirBoxEventModel $e ){

        AirBoxNotifyModel::ResetLogFile();
        
        $this->coinbox_default_action($e);
    }
    /**
     * Registrar los cambios de parámetros de AirBox
     * Agregar aquí los valores requeridos de la aplicación
     * Utiliza la función WordPress <strong>update_option($option,$value)</strong>;
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_save_settings_action(AirBoxEventModel $e ){
        
        foreach( parent::listOptions() as $option ){

            switch( $option ){
                case 'coinbox_units':
                    //establecer a 0 por defecto
                    parent::setOption($option, $e->get($option,0));
                    break;
                default:
                    if( !is_null( $value = $e->get($option) ) ){
                        parent::setOption($option, $value);
                    }
                    break;
            }
        }
        AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_MANAGER)->Render(self::ADMIN_OPTION_SETTINGS);
    }
    /**
     * Resetea las tablas de AirBox
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_reset_database_action(AirBoxEventModel $e ){
        //no permitir ejecutar en producción
        //return;
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        $tables = array(
            AirBoxDataBaseModel::DB_SOURCE_INVESTORS,
            AirBoxDataBaseModel::DB_SOURCE_BOXES,
            AirBoxDataBaseModel::DB_SOURCE_TRANSACTIONS,
            AirBoxDataBaseModel::DB_SOURCE_NOTIFICATIONS,
        );
        
        foreach( $tables as $t ){
            switch($t){
                case AirBoxDataBaseModel::DB_SOURCE_INVESTORS:
                    $dbi->query(sprintf('TRUNCATE %s',$dbi->$t ));
                    break;
                default:
                    $dbi->query(sprintf('TRUNCATE %s;',$dbi->$t));
                    $dbi->query(sprintf('ALTER TABLE %s AUTO_INCREMENT=1',$dbi->$t));
                    break;
            }
        }
        AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_MANAGER)->Render(self::ADMIN_OPTION_SETTINGS);
    }
    /**
     * Activa el pedido del inversor indicado
     * @param AirBoxInvestorModel $e
     */
    protected final function coinbox_activate_action( AirBoxEventModel $e ){
        
        //procesar activación del inversor
        $activation_code = $e->get(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY);
        
        $order_id = $e->get(AirBoxOrderModel::FIELD_META_ID,0);
        
        //cargar gestor de vistas del manager
        $R = AirBoxRenderer::CreateRender(parent::COINBOX_PROFILE_MANAGER);

        if( !is_null( $investor = AirBoxInvestorModel::ImportByInvestorKey($activation_code) ) ){
            
            $R->setModel($investor);
            
            if( $investor->activateOrder($order_id) ){
                
                //return true;
            }
            
            $R->Render(AirBoxManagerView::VIEW_LAYOUT_INVESTOR_PROFILE);
        }
        else{
            //volver a vista por defecto
            $R->Render(self::ADMIN_OPTION_INVESTORS);
        }
    }
    /**
     * Efectúa la operación de reintegro solicitada por el cliente (o acordada por el administrador)
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_apply_refund_action(AirBoxEventModel $e ){
        
        $investor = AirBoxInvestorModel::ImportById(
                            $e->get(AirBoxInvestorModel::FIELD_META_ID,0));

        if( !is_null($investor) ){
            
            //esto en su momento podría mejorarse pasando el número de AP en el evento
            $amount = $investor->getAirPoints();
            
            if( $investor->removeAirpoints( $amount ) ){
                //genera la transacción
                if(AirBoxTransactionModel::CreateRefund($investor,$amount ) ){

                    $investor_email = $investor->getEmail();
                    $investor_subject = AirBoxStringModel::__compose(
                        AirBoxStringModel::EML_SUBJECT_APPLY_REFUND,
                            $amount);
                    $investor_content = AirBoxStringModel::__compose(
                        AirBoxStringModel::EML_CONTENT_APPLY_REFUND,
                            array($investor->getName(),$amount));
                    
                    //preparar envío para el inversor
                    $investor_message = AirBoxMailerService::createService();
                    $investor_message->addRecipient($investor_email);
                    $investor_message->setSubject($investor_subject);
                    $investor_message->setContent($investor_content);
                    parent::RegisterService($investor_message);
                    
                    AirBoxNotifyModel::RegisterLog(
                            AirBoxStringModel::__(
                                    AirBoxStringModel::LBL_INFO_REFUND_NOTIFIED,
                                    $investor->getFullName()),
                            AirBoxNotifyModel::LOG_TYPE_INFORMATION);
                    //if( $investor_message->dispatch() ){ }
                }
            }
            else{
                AirBoxNotifyModel::RegisterLog(
                        'No se ha podido aplicar el reintegro',
                        AirBoxNotifyModel::LOG_TYPE_ERROR);
            }
        }
        
        $this->coinbox_default_action($e);
    }
    /**
     * fecha: 2016-04-19
     * @author Coder#1
     * 
     * Se procesa una asignación de dado inversor a  un inversor padre, cuando el primer inversor
     * no dispone todavía de ningún padre asignado. Esta funcionalidad solo es para uso exclusivo en
     * inversores que han sido dados de alta y requieren ser asignados a algún inversor principal.
     * 
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_set_parent_action( AirBoxEventModel $e ){
        
        $investor = AirBoxInvestorModel::ImportById($e->get(AirBoxInvestorModel::FIELD_META_ID,0));
        
        $parent = AirBoxInvestorModel::ImportById($e->get(AirBoxInvestorModel::FIELD_META_PARENT_ID),0);
        if( !is_null( $investor ) && !is_null($parent) ){
            /**
             * Importante definir explícitamente que el parentId debe ser 0, sino, cualquier retorno a esta
             * url vuelve a generar el mismo movimiento, el inversor huerfano solo puede ser asignado 1 vez
             */
            if(  $investor->getParentId() == 0 ){
                /**
                 * Establece implícitamente el inversor padre SOLO SI este es válido y está en estado ACTIVO
                 */
                if( $investor->setParent($parent) ){
                    /**
                     * Ahora aqui toca reproducir todas las recompensas y movimientos generados para el inversor padre
                     * 
                     * Se listan los boxes en propiedad del inversor y en función de cada uno, se genera una
                     * recompensa para el padre con sus movimientos generados implícitamente
                     * 
                     * Esta rutina no tiene por que ejecutarse, si el inversor no dispone todavía de boxes
                     * en propiedad, pero ya será afiliado por un inversor padre y al adquirir los boxes,
                     * debería procederse del método habitual
                     */ 
                    foreach( $investor->getBoxes() as $box ){
                        //retorna un array asociado, ojo, esto ya es efectivo para el padre!!
                        //(incluye sub-rutina save)
                        $parent->reward($box[AirBoxOrderModel::FIELD_META_AMOUNT]);
                    }

                    if( $investor->save() ){
                        //notificar exito de la operación
                        AirBoxNotifyModel::RegisterLog(
                            'Inversor reasignado!',
                            AirBoxNotifyModel::LOG_TYPE_INFORMATION);
                    }
                }
            }
            else{
                //notificar acción de reasignación bloqueada si ya tiene ParentID
                AirBoxNotifyModel::RegisterLog(
                        'No se admite esta acci&oacute;n',
                        AirBoxNotifyModel::LOG_TYPE_ERROR);
            }
        }
        else{
            //notificar error en la carga de inversor o padre invalido
            AirBoxNotifyModel::RegisterLog(
                    'No se ha podido cargar el inversor',
                    AirBoxNotifyModel::LOG_TYPE_ERROR);
        }
        //redirigir a la vista en uso del inversor (perfil de inversor hijo)
        $this->coinbox_default_action($e);
    }
    /**
     * Activa un inversor sin necesidad de activar su pedido de boxes
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_activate_investor_action(AirBoxEventModel $e ){

        $investor = AirBoxInvestorModel::ImportById( $e->get(AirBoxInvestorModel::FIELD_META_ID,0) );
        
        if( !is_null( $investor ) ){
            if( $investor->activateInvestor()->save() ){
                
                //notificar activación del inversor
                $mailer = AirBoxMailerService::createService();
                $mailer->setSubject(
                    AirBoxStringModel::__(
                            AirBoxStringModel::EML_SUBJECT_NOTIFY_ACCOUNT_ACTIVATION));
                $mailer->setContent(
                    AirBoxStringModel::__compose(
                            AirBoxStringModel::EML_CONTENT_NOTIFY_ACCOUNT_ACTIVATION,
                            $investor->getName()));
                $mailer->addContent(AirBoxRenderer::renderInvestorShortCutLink());
                //$mailer->dispatch();
                parent::RegisterService($mailer);

                //notificar al administrador
                AirBoxNotifyModel::RegisterLog(
                    'Inversor activado',
                    AirBoxNotifyModel::LOG_TYPE_ERROR);
            }
            else{
                AirBoxNotifyModel::RegisterLog(
                    'No fu&eacute; posible activar el inversor',
                    AirBoxNotifyModel::LOG_TYPE_ERROR);
            }
        }
        else{
                AirBoxNotifyModel::RegisterLog(
                    'Inversor inv&aacute;lido', AirBoxNotifyModel::LOG_TYPE_ERROR);
        }

        //redirigir a la vista en uso del inversor (perfil de inversor hijo)
        $this->coinbox_default_action($e);
    }
    /**
     * Genera una nueva etapa / objetivo del proyecto
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_set_milestone_action( AirBoxEventModel $e ){

        $amount =  $e->get(AirBoxMilestoneModel::FIELD_META_GOAL,0);
        
        $date = $e->get(AirBoxMilestoneModel::FIELD_META_DATE_LIMIT);
        
        if( $amount > 0){

            $milestone = AirBoxMilestoneModel::CreateMilestone($amount,$date );
            
            AirBoxNotifyModel::RegisterLog(AirBoxStringModel::__compose(
                    'Nuevo objetivo establecido [%s]',$milestone->getDateCreated()));
        }
        else{
            AirBoxNotifyModel::RegisterLog(
                    AirBoxStringModel::__('Fallo ai registrar el objetivo'),
                    AirBoxNotifyModel::LOG_TYPE_WARNING);
        }
        
        //redirigir a la vista en uso del inversor (perfil de inversor hijo)
        $this->coinbox_default_action($e);
    }
    /**
     * Resetea todos los indicadores de objetivos de proyecto y elimina los milestones
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_clear_milestones_action(AirBoxEventModel $e ){

        AirBox::setOption('coinbox_milestone', 0);
        
        AirBoxMilestoneModel::ResetMilestones();
        
        //redirigir a la vista en uso del inversor (perfil de inversor hijo)
        $this->coinbox_default_action($e);
    }
    /**
     * Borra un milestone
     * @param AirBoxManagerBootStrap $e
     */
    protected final function coinbox_remove_milestone_action( AirBoxEventModel $e ){

        $milestones = AirBoxMilestoneModel::CountMilestones();
        
        $milestone_id = $e->get(AirBoxMilestoneModel::FIELD_META_ID,0);
        
        if( $milestone_id == $milestones && AirBoxMilestoneModel::RemoveMilestone($milestone_id) ){
            AirBoxNotifyModel::RegisterLog('Objetivo borrado');
        }
        else{
            AirBoxNotifyModel::RegisterLog('Objetivo inv&aacute;lido',AirBoxNotifyModel::LOG_TYPE_WARNING);
        }
        
        //redirigir a la vista en uso del inversor (perfil de inversor hijo)
        $this->coinbox_default_action($e);
    }
    /**
     * 
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_remove_unlinked_action( AirBoxEventModel $e ){

        global $table_prefix;
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        $sql_query = sprintf(
                'SELECT `user_id`,`first_name`,`last_name` FROM %s WHERE `user_id` NOT IN (SELECT `ID` FROM %susers)',
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_INVESTORS), $table_prefix );
        
        $r_ids = $dbi->query( $sql_query );
        
        $list = array();
        $names = array();
        
        if( !is_null($r_ids) && count($r_ids) ){
            
            foreach( $r_ids as $r ){
                $list[] = $r['user_id'];
                $names[] = $r['first_name'] . ' ' . $r['last_name'] . ' ('.$r['user_id'].')';
            }

            $sql_investors = sprintf('DELETE FROM %s WHERE `user_id` IN (%s)',
                    $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_INVESTORS),
                    implode(',',$list));
            $sql_orders = sprintf('DELETE FROM %s WHERE `owner_id` IN (%s)',
                    $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_BOXES),
                    implode(',',$list));
            $sql_transactions = sprintf('DELETE FROM %s WHERE `account_id` IN (%s)',
                    $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_TRANSACTIONS),
                    implode(',',$list));
            
            $dbi->query($sql_investors);
            $dbi->query($sql_orders);
            $dbi->query($sql_transactions);
            
            AirBoxNotifyModel::RegisterLog(AirBoxStringModel::__compose(
                    'Se han eliminado %s inversores sin usuario en el sistema:<br/>%s',
                    array(count($names),implode('<br/>', $names))),
                    AirBoxNotifyModel::LOG_TYPE_ADVICE );
        }
        else{
            AirBoxNotifyModel::RegisterLog(AirBoxStringModel::__(
                    'Todos los inversores est&aacute;n correctamente vinculados a sus usuarios WP'));
        }
        
        //redirigir a la vista en uso del inversor (perfil de inversor hijo)
        $this->coinbox_default_action($e);
    }
    /**
     * Genera un fichero de traducciones
     * @param AirBoxEventModel $e
     */
    protected final function coinbox_generate_translation_action(AirBoxEventModel $e){
        
        $lang = $e->get('lang','');
        $locale = $e->get('locale','');
        
        if( strlen($lang) && strlen($locale)){
            if( AirBoxStringModel::GenerateTranslationModel(strtolower($lang), strtoupper( $locale) ) ){
                AirBoxNotifyModel::RegisterLog(
                    AirBoxStringModel::__compose(
                            'Modelo de traducci&oacute;n generado %s',
                            AirBoxStringModel::TranslationPath($lang, $locale)),
                    AirBoxNotifyModel::LOG_TYPE_INFORMATION);
            }
            else{
                AirBoxNotifyModel::RegisterLog(
                    'Ya existe el fichero de traducciones para el idioma solicitado',
                    AirBoxNotifyModel::LOG_TYPE_WARNING);
            }
        }
        else{
            AirBoxNotifyModel::RegisterLog(
                'Para la traducci&oacute;n es necesario un c&oacute;digo de idioma y localizaci&oacute;n',
                AirBoxNotifyModel::LOG_TYPE_WARNING);
        }
        
        $this->coinbox_default_action($e);
    }
    /**
     * Comprobación de email
     */
    protected final function coinbox_test_mail_action( AirBoxEventModel $e ){
        
        if( !is_null( $email = $e->get( 'email' ) ) ){

            $message = AirBoxMailerService::createService();
            $message->addRecipient($email);
            $message->setSubject('Mail Test de AirBox');
            $message->setContent(sprintf( '<p>Prueba de env&iacute;o email %s</p>',$email ) );
            parent::RegisterService($message);
            
            AirBoxNotifyModel::RegisterLog(
                    sprintf( 'Mail enviado a %s',$email ),
                    AirBoxNotifyModel::LOG_TYPE_ADVICE);
        }
        else{
                AirBoxNotifyModel::RegisterLog(
                    'No se ha indicado una direccto&oacute;n de destino',
                    AirBoxNotifyModel::LOG_TYPE_WARNING);
        }
        
        $this->coinbox_default_action($e);
    }
    /**
     * 
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getProfileData($name, $default = null) {
        switch( $name ){
            case 'profile':
                return parent::COINBOX_PROFILE_MANAGER;
        }
        return $default;
    }
}