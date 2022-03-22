<?php defined('ABSPATH') or die;
/**
 * Vista del inversor
 */
class AirBoxInvestorView extends AirBoxRenderer{
    
    //no incluido en el menú de navegación
    const INVESTOR_MENU_DASHBOARD = 'dashboard';
    const INVESTOR_MENU_PROFILE = 'profile';
    const INVESTOR_MENU_TRANSACTIONS = 'transactions';
    //const INVESTOR_MENU_AFFILIATE_FORM = 'affiliate-form';
    const INVESTOR_MENU_TEST = 'test';
    const INVESTOR_MENU_CONTRACT = 'contract-link';
    const INVESTOR_MENU_LOGOUT = 'logout';
    const INVESTOR_MENU_CALENDAR = 'calendar';
    const INVESTOR_MENU_ADMIN = 'admin';
    
    
    //opciones del menú de navegación
    //puntos de venta
    const INVESTOR_OPTION_SALE_POINTS = 'salepoints';
    //simulación
    const INVESTOR_OPTION_SIMULATION = 'simulation';
    //noticias y blog
    const INVESTOR_OPTION_NEWS = 'news';
    //liquidación
    const INVESTOR_OPTION_SETTLEMENT = 'settlement';
    //programa de afiliados
    const INVESTOR_OPTION_AFFILIATES = 'affiliates';
    //solicitar reintegro
    const INVESTOR_OPTION_REFUND = 'request_refund';
    //mostrar cuenta cerrada
    const INVESTOR_OPTION_CLOSED_ACCOUNT = 'closed';    
    //Vista de método de pago del pedido NO INCLUIR EN MENUS!!!!
    const INVESTOR_OPTION_CHECKOUT = 'checkout';

    protected function __construct() {
        //permite mostrar opciones explícitas como el acceso al manager (tipo de perfil administrador)
        $profile = AirBox::Instance()->getProfileData('profile',AirBox::COINBOX_PROFILE_INVESTOR);
        //permite mostrar opciones inaccesibles en el menú de afiliado
        $investor_status = AirBox::Instance()->getProfileData(
                AirBoxInvestorModel::FIELD_META_STATUS,
                AirBoxInvestorModel::INVESTOR_STATUS_PENDING);
        //deshabilitar opciones del menú superior
        $disable_topmenu = ($investor_status === AirBoxInvestorModel::INVESTOR_STATUS_ACTIVE) ? false : true;
        
        /*
         * PREPARANDO LOS 4 ITEMS DE MENÚ PRINCIPAL
         */
        parent::registerMenuItem(
                self::INVESTOR_OPTION_SALE_POINTS,
                AirBoxStringModel::LBL_MENU_OPTION_SALEPOINTS, 'affiliate');
        parent::registerMenuItem(
                self::INVESTOR_OPTION_NEWS,
                AirBoxStringModel::LBL_MENU_OPTION_NEWS, 'affiliate' );
        parent::registerMenuItem(
                self::INVESTOR_OPTION_SETTLEMENT,
                AirBoxStringModel::LBL_MENU_OPTION_SETTLEMENT, 'affiliate',
                array( 'disabled' => $disable_topmenu ) );
        parent::registerMenuItem(
                self::INVESTOR_OPTION_AFFILIATES,
                AirBoxStringModel::LBL_MENU_OPTION_AFFILIATES, 'affiliate',
                array( 'disabled' => $disable_topmenu ) );
        /*
         * PREPARANDO LOS ITEMS DE MENÚ DE INVERSOR
         */
        parent::registerMenuItem(
                self::INVESTOR_MENU_DASHBOARD,
                AirBoxStringModel::LBL_MENU_OPTION_DASHBOARD,
                'profile');
        
        /*parent::registerMenuItem(
                self::INVESTOR_MENU_PROFILE,
                AirBoxStringModel::LBL_MENU_OPTION_PROFILE,
                'profile',
                array( 'event'=>AirBoxEventModel::EVENT_SELECTED_VIEW ));*/
        
        parent::registerMenuItem(
                self::INVESTOR_MENU_TRANSACTIONS,
                AirBoxStringModel::LBL_MENU_OPTION_HISTORY,
                'profile',
                array(
                    'event'=>AirBoxEventModel::EVENT_SELECTED_VIEW,
                    'access'=>AirBoxInvestorModel::INVESTOR_STATUS_ACTIVE ));
        
        parent::registerMenuItem(
                self::INVESTOR_MENU_CALENDAR,
                AirBoxStringModel::LBL_MENU_OPTION_CALENDAR,
                'profile',
                array(
                    'event'=>AirBoxEventModel::EVENT_SELECTED_VIEW,
                    'access'=>AirBoxInvestorModel::INVESTOR_STATUS_ACTIVE ));
        
        /**
         * Mofificación 2016-04-20
         * agregar + opciones de menú manualmente
         * Agregado el tipo link para que se muestre enlaces a recursos externos a la intranet
         */
        parent::registerMenuItem(
                self::INVESTOR_MENU_CONTRACT,
                AirBoxStringModel::LBL_MENU_OPTION_CONTRACT,
                'profile',
                array(
                    'target' => '_blank',
                    //esta acción será interpretada directamente por la vista para mostrar un enlace
                    'link'=> AirBoxRenderer::renderMediaUrl( AirBox::getOption('coinbox_contract_link',0))));
        
        //si el perfil es administrador, mostrar link al back-end
        if( $profile === AirBox::COINBOX_PROFILE_MANAGER){
            parent::registerMenuItem(
                self::INVESTOR_MENU_ADMIN,
                AirBoxStringModel::LBL_MENU_OPTION_ADMIN,
                'profile',
                array(
                    'target' => '_blank',
                    'class' => 'admin',
                    //esta acción será interpretada directamente por la vista para mostrar un enlace
                'link' => AirBoxRouter::RouteAdmin(),
                'access' => AirBoxInvestorModel::INVESTOR_STATUS_ACTIVE));
        }

        parent::registerMenuItem(
                self::INVESTOR_MENU_LOGOUT,
                AirBoxStringModel::LBL_MENU_OPTION_DISCONNECT,
                'profile',
                array(
                    'class'=>'right',
                    //esta acción será interpretada directamente por la vista para mostrar un enlace
                    'link'=> parent::renderWordPressLogOut()));
        
        //indica que el formulario de inversor en este contexto es un formulario de demostración
        $this->set_data('demo_form', true );

        parent::__construct();
    }
    
    public final function Render($layout) {

        parent::Render($layout);
    }
    /**
     * @return array Opciones del menú de perfil de inversor (secundario)
     */
    protected final function get_profile_menu_data(){
        $menu = array();
        
        if( !is_null($this->getModel())){
            foreach( $this->_profileOptions as $option => $vars ){
                if( $vars['access'] <= $this->getModel()->getStatus() ){
                    $menu[$option] = $vars;
                }
            }
        }

        return $menu;
    }
    /**
     * @return array Opciones del menú de afiliado (superior)
     */
    protected final function get_affiliate_menu_data(){
        return $this->_affiliateOptions;
    }
    /**
     * @return int Devuelve el número de airpoints del inversor o 0 si no hay
     */
    protected final function get_airpoints_data(){
        return !is_null($this->getModel()) ?
            $this->getModel()->getAirPoints() : 0;
    }
    /**
     * @return int Plan de inversor
     */
    protected final function get_plan_data(){
        return !is_null($this->getModel()) ? 
            $this->getModel()->getPlan() :
            AirBoxInvestorModel::INVESTOR_PLAN_NONE;
    }
    /**
     * @return int Estado del inversor
     */
    protected final function get_status_data(){
        return !is_null($this->getModel()) ?
            $this->getModel()->getStatus() :
            AirBoxInvestorModel::INVESTOR_STATUS_NEW;
    }
    /**
     * @return array Lista de afiliados por medio de este inversor así como sus ganancias o BOXXES
     */
    protected final function get_affiliates_data(){
        return !is_null($this->getModel()) ?
            $this->getModel()->getChildBoxes() :
            array();
    }
    /**
     * @return array Listado de transacciones del inversor
     */
    protected final function get_transaction_data(){
        return !is_null($this->getModel()) ?
             $this->getModel()->getTransactions() :
             array();
    }
    /**
     * @return string Clave de formulario de inversor (investor_key)
     */
    protected final function get_key_data(){
        return !is_null($this->getModel()) ?
            $this->getModel()->getKey() :
            null;
    }
    /**
     * @return array Lista de Boxes del inversor
     */
    public final function get_boxes_data( ){
        return( !is_null( $this->getModel() ) && get_class( $this->getModel()) === 'AirBoxInvestorModel' ) ?
            $this->getModel()->getBoxes( true ) : array();
    }
    /**
     * Retorna el pedido en curso
     * @return AirBoxOrderModel | NULL
     */
    public final function get_cart_data(){

        //mucho ojo que el campo se denomina 'id' y podríainducir a errores
        $order_id = $this->get_data(AirBoxOrderModel::FIELD_META_ID,0);
        
        return ( $order_id ) ? 
            AirBoxOrderModel::LoadOrderById($order_id, $this->getModel()->getId()) :
            null;
    }
    /**
     * Lista de posts de la categoría de noticias o blog (seleccionable desde parametros)
     * @return array O lo que sea que retorna esto.
     */
    public final function get_entry_data(){
        
        $posts = array();
        
        $catId = AirBox::getOption('coinbox_blog_category',0);
        
        if( $catId > 0 ){
            
            $meta = get_posts(array(
                'post_type' => 'post',
                'category' => $catId,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
                ));
            
            foreach( $meta as $m ){
                $posts[ $m->ID ] = array(
                    'post_title' => $m->post_title,
                    'post_content' => $m->post_content,
                    'post_excerpt' => $m->post_excerpt,
                    'post_date' => $m->post_date,
                );
            }
        }
        
        return $posts;
    }
    /**
     * Modelo de formulario de perfil
     * @return AirBoxFormModel
     */
    protected final function get_profile_form_data(){

        $formData = AirBoxFormModel::CreateForm('profile');
        
        $formData->addFormField(
            AirBoxInvestorModel::FIELD_META_FIRST_NAME,
            AirBoxDictionary::FIELD_TYPE_TEXT,
            array('required'=>true,'label'=>'Nombre'));
        $formData->addFormField(
            AirBoxInvestorModel::FIELD_META_LAST_NAME,
            AirBoxDictionary::FIELD_TYPE_TEXT,
            array('required'=>true,'label'=>'Apellidos'));
        $formData->addFormField(
            AirBoxInvestorModel::FIELD_META_DOCUMENT_ID,
            AirBoxDictionary::FIELD_TYPE_TEXT,
            array('required'=>true,'label'=>'DNI'));
        $formData->addFormField(
            AirBoxInvestorModel::FIELD_META_EMAIL,
            AirBoxDictionary::FIELD_TYPE_EMAIL,
            array('required'=>true,'label'=>'Email'));
        $formData->addFormField(
            AirBoxInvestorModel::FIELD_META_TELEPHONE,
            AirBoxDictionary::FIELD_TYPE_TELEPHONE,
            array('required'=>true,'label'=>'Tel&eacute;fono'));
        
        if(get_class($this->getModel()) === 'AirBoxInvestorModel' ){

            $formData->importFromModel($this->getModel());

        }

        return $formData;
    }
}

