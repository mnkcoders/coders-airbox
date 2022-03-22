<?php defined('ABSPATH') or die;
/**
 * Descriptor del perfil de inversor y su relación con el resto del sistema de inversión
 */
class AirBoxInvestorModel extends AirBoxDictionary implements AirBoxIModel{
    
    //número de afiliados (o multiplo del mismo) para obtener un box de regalo
    const INVESTOR_AFFILIATE_REWARD = 10;
    
    //siempre estado nuevo cuando se crea, pero no se llega a registrar en ba bbdd
    const INVESTOR_STATUS_NEW = 0;
    const INVESTOR_STATUS_PENDING = 1;
    const INVESTOR_STATUS_ACTIVE = 2;
    const INVESTOR_STATUS_QUITTING = 3;
    const INVESTOR_STATUS_CLOSED = 4;
    
    const INVESTOR_PLAN_NONE = 0;
    const INVESTOR_PLAN_BASE = 1;
    const INVESTOR_PLAN_PLUS = 2;
    const INVESTOR_PLAN_PRO = 3;
    
    const FIELD_META_ID = 'user_id';
    //const FIELD_META_FORM_CODE = 'form_code';
    const FIELD_META_PARENT_ID = 'parent_id';
    const FIELD_META_USER_NAME = 'user_name';
    const FIELD_META_FIRST_NAME = 'first_name';
    const FIELD_META_LAST_NAME = 'last_name';
    const FIELD_META_DOCUMENT_ID = 'document_id';
    const FIELD_META_TELEPHONE = 'telephone';
    const FIELD_META_EMAIL = 'email';
    const FIELD_META_STATUS = 'status';
    const FIELD_META_PLAN = 'plan';
    const FIELD_META_DATE_CREATED = 'date_created';
    const FIELD_META_DATE_UPDATED = 'date_updated';
    const FIELD_META_AIRPOINTS = 'airpoints';

    //descripción de propiedades no definidas implicitamente (formularios etc)
    const FIELD_META_BOXES = 'boxes';
    const FIELD_META_AFFILIATE = 'affiliate';
    //const FIELD_META_BANK_ACCOUNT = 'bank_account';
    //const FIELD_META_PAYMENT_METHOD = 'payment_method';
    const FIELD_META_FULL_NAME = 'full_name';
    const FIELD_META_INVESTOR_KEY = 'investor_key';
    const FIELD_META_USER_PASS = 'user_pass';
    
    //variables de los valores del aircoin en función del tipo de plan
    //mover esto a parametrización en proximas versiones
    const AIRPOINT_BASE_VALUE = 22;
    const AIRPOINT_PLUS_VALUE = 45;
    const AIRPOINT_PRO_VALUE = 89;
    //descripción de coste de boxes en función del tipo de plan
    //idem, mover a parametrización
    const BOX_BASE_VALUE = 67;
    const BOX_PLUS_VALUE = 89;
    const BOX_PRO_VALUE = 111;
    
    /**
     *
     * @var array Declaración de planes de inversor, donde cada uno contiene su definición de AP ganados por
     * veta de BOX y tasa de descuento de reinversión con AP en mas BOXES
     */
    private static $_INVESTOR_PLAN = array(
        self::INVESTOR_PLAN_BASE=>array(
            'min_boxes'=>1,
            'airpoints'=>35,
            'airpoint_value'=>1,
            'boxpoint_value'=>2),
        self::INVESTOR_PLAN_PLUS=>array(
            'min_boxes'=>3,
            'airpoints'=>55,
            'airpoint_value'=>1,
            'boxpoint_value'=>2),
        self::INVESTOR_PLAN_PRO=>array(
            'min_boxes'=>10,
            'airpoints'=>100,
            'airpoint_value'=>1,
            'boxpoint_value'=>1.1),
    );
    
    /**
     * @var boolean Define si la información del inversor ha sido actualizada
     */
    private $_updated = false;
    /**
     * @var AirBoxOrderModel[] Lista de Boxes reservados y adquiridos por el inversor
     */
    //private $_ownedBoxes = null;
    /**
     * @var AirBoxInvestorModel[] Lista de inversores atraidos directamente
     */
    //private $_children = array();
    
    protected function __construct( array $data = null ) {
        //Data puede ser nulo cuando interesa generar una estructura meta si valores
        //a fin de facilitar la vista de un formulario de datos
        
        //define todos los datos necesarios del inversor (cuenta WP y de AirBox)
        parent::add(
                self::FIELD_META_ID,
                parent::FIELD_TYPE_ID ,
                array('value'=>0)); //con valor 0 se entiende que entra un nuevo inversor (CREAR)
        
        parent::add(
                self::FIELD_META_USER_NAME,
                parent::FIELD_TYPE_TEXT,
                array('mode'=>parent::FIELD_MODE_DISPLAY,
                    'required'=>true,
                    'label'=>AirBoxStringModel::__( AirBoxStringModel::LBL_USER_NAME )));
        
        parent::add(
                self::FIELD_META_FIRST_NAME,
                parent::FIELD_TYPE_TEXT,
                array('required'=>true,
                    'label'=>AirBoxStringModel::__( AirBoxStringModel::LBL_FIRST_NAME)));
        
        parent::add(
                self::FIELD_META_LAST_NAME,
                parent::FIELD_TYPE_TEXT ,
                array('required'=>true,
                    'label'=>AirBoxStringModel::__(AirBoxStringModel::LBL_LAST_NAME)));
        
        parent::add(
                self::FIELD_META_DOCUMENT_ID,
                parent::FIELD_TYPE_TEXT ,
                array('required'=>true,
                    'label'=>AirBoxStringModel::__(AirBoxStringModel::LBL_DOCUMENT_ID)));
        
        parent::add(
                self::FIELD_META_TELEPHONE,
                parent::FIELD_TYPE_TELEPHONE ,
                array(
                    'required'=>true,
                    'label'=>AirBoxStringModel::__(AirBoxStringModel::LBL_PHONE)));
        
        parent::add(
                self::FIELD_META_EMAIL,
                parent::FIELD_TYPE_EMAIL ,
                array('required'=>true,
                    'label'=>AirBoxStringModel::__(AirBoxStringModel::LBL_EMAIL)));
        
        parent::add(
                self::FIELD_META_AIRPOINTS,
                parent::FIELD_TYPE_NUMBER,
                array(
                    'mode'=>parent::FIELD_MODE_DISPLAY,
                    'minimum'=>1,
                    'label'=>AirBoxStringModel::__(AirBoxStringModel::LBL_AIRPOINTS_AMOUNT)));
        
        //Definición de campos de estado que podrían procesarse como propiedades locales
        parent::add(
                self::FIELD_META_DATE_CREATED,
                parent::FIELD_TYPE_DATETIME ,
                array(
                    'mode'=>parent::FIELD_MODE_DISPLAY,
                    'label'=>AirBoxStringModel::__( AirBoxStringModel::LBL_DATE_CREATED )));
        
        parent::add(
                self::FIELD_META_DATE_UPDATED,
                parent::FIELD_TYPE_DATETIME ,
                array('mode'=>parent::FIELD_MODE_DISPLAY,
                    'label'=>AirBoxStringModel::__( AirBoxStringModel::LBL_DATE_MODIFIED)));

        parent::add(
                self::FIELD_META_STATUS,
                parent::FIELD_TYPE_LIST,
                array(
                    'value'=>self::INVESTOR_STATUS_NEW,  //ojo, actualizar SIEMPRE antes de guardar!!
                    'source'=>'investor_status',
                    'mode'=>parent::FIELD_MODE_DISPLAY,
                    'label'=>AirBoxStringModel::__( AirBoxStringModel::LBL_INVESTOR_STATUS )) );
        
        parent::add(
                self::FIELD_META_PLAN,
                parent::FIELD_TYPE_NUMBER,
                array(
                    'value'=>self::INVESTOR_PLAN_NONE,
                    'source'=>'investor_plan',
                    'mode'=>parent::FIELD_MODE_DISPLAY,
                    'label'=>AirBoxStringModel::__( AirBoxStringModel::LBL_INVESTOR_PLAN)));

        parent::add(
                self::FIELD_META_INVESTOR_KEY,
                parent::FIELD_TYPE_TEXT ,
                //siempre oculto y longitud fija de 32 bytes
                array('mode'=>parent::FIELD_MODE_HIDDEN,
                    'size'=>32,
                    'label'=>AirBoxStringModel::__( AirBoxStringModel::LBL_INVESTOR_KEY)));

        //id de inversor principal
        parent::add(
            self::FIELD_META_PARENT_ID,
            parent::FIELD_TYPE_ID,
            array('mode'=>parent::FIELD_MODE_HIDDEN,
                'label'=>AirBoxStringModel::__( AirBoxStringModel::LBL_INVESTOR_PARENT)));

        parent::__construct($data);

    }
    /**
     * @return int retorna el número de AirPoints disponibles para este inversor
     */
    public final function getAirPoints(){
        return intval($this->getValue(self::FIELD_META_AIRPOINTS,0));
    }
    /**
     * @return int Recuento de APs de recompensa por vender boxes a mas inversores en función del plan
     */
    public static final function getAirPointReward( $plan_id = self::INVESTOR_PLAN_BASE, $amount = 1 ){
        return isset(self::$_INVESTOR_PLAN[$plan_id]) ?
            self::$_INVESTOR_PLAN[$plan_id]['airpoints'] * $amount :
            self::$_INVESTOR_PLAN[self::INVESTOR_PLAN_BASE]['airpoints'] * $amount;
    }
    /**
     * Indica el valor en € de descuento de los airpoints facilitados en función del tipo de plan
     * 
     * Para mostrar el resultado adecuadamente puede interesar utlizar la función number_format de PHP
     * 
     * <strong>Este calculo sólo es aplicable a la reinversión en boxes!!!</strong>
     * <p>Un reintegro siempre será de valor <strong>1AP=1€</strong></p>
     * 
     * @param int $airpoints
     * @return float
     */
    public final function getAirPointValue( $airpoints = 1 ){
        return isset(self::$_INVESTOR_PLAN[$this->getPlan()]) ?
            self::$_INVESTOR_PLAN[ $this->getPlan() ]['airpoint_value'] * $airpoints:
            self::$_INVESTOR_PLAN[self::INVESTOR_PLAN_BASE]['airpoint_value'] * $airpoints;
    }
    /**
     * Genera el descuento de AP para aplicar sobre la reinversión de Boxes
     * @param int $airpoints
     * @return float
     */
    public final function getBoxPointValue( $airpoints = 1 ){
        
        $discount = isset(self::$_INVESTOR_PLAN[$this->getPlan()]) ?
                self::$_INVESTOR_PLAN[ $this->getPlan() ]['boxpoint_value'] * $airpoints:
                self::$_INVESTOR_PLAN[self::INVESTOR_PLAN_BASE]['boxpoint_value'] * $airpoints;
        
        return $discount;
    }
    /**
     * Crea un pedido de boxes en estado reservado. Si no hay boxes suficientes es asignan los restantes. si no hay boxes se notifica y retorna nulo
     * @param int $amount Cantidad
     * @param int $airpoints Airpoints a consumir, por defecto ninguno
     * @return AirBoxOrderModel|NULL Pedido generado
     */
    public final function addBoxPack( $amount = 1 , $airpoints = 0 ){
        //siempre asegurarse de que es un inversor registrado
        if( $this->getId() > 0 ){

            //boxes disponibles en el sistema
            $available_boxes = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);

            if( $available_boxes == 0 ){
                //retornar falso, no permitir nuevas reservas y notificar
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::LBL_WARNING_OUT_OF_STOCK,
                        AirBoxNotifyModel::LOG_TYPE_WARNING);

                return false;
            }
            elseif( $available_boxes > 0 && $amount > $available_boxes ){
                //la cantidad excede los boxes disponibles, permitir generar el pedido
                //pero utilizando la cantidad disponible en lugar de la indicada
                //si el inversor no está deacuerdo, puede dar de baja la compra
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::LBL_WARNING_LIMIT_EXCEEDED,
                        AirBoxNotifyModel::LOG_TYPE_WARNING);
                //pero permitir proceder desde este punto con la reserva
                $amount = $available_boxes;
            }

            //crea el pedido
            $order = AirBoxOrderModel::CreateOrder($this, $amount, $airpoints);
            
            if( !is_null($order) && $order->getId() ){
                
                ///guardar cambios en el inversor (puede no haber sido necessario registrar cambios!!! por tanto pudede retornar false)
                $this->save();
                
                return $order;
            }
        }

        return null;
    }
    /**
     * Asigna un inversor padre al inversor actual
     * 
     * Si el inversor ya tiene asignado un padre, no debe ser posible re-asignarlo
     * 
     * @param AirBoxInvestorModel $parent_id establece el id de inversor de nivel superior
     * @return boolean TRUE si ha sido asignado correctamente
     */
    public final function setParent( AirBoxInvestorModel $parent ){
        if( !is_null($parent) ){
                
            $id = $parent->getId();
            
            $status = $parent->getStatus();
            
            $parent_id = $this->getParentId();

            if( $parent_id == 0 && $id > 0 && $status == self::INVESTOR_STATUS_ACTIVE ){
                // si el inversor padre es activo y valido asignar
                $this->setValue(self::FIELD_META_PARENT_ID, $id );
                
                return ($this->_updated = true );
            }
        }
        return false;
    }
    /**
     * @return AirBoxInvestorModel Retorna el inversor padre asignado. NULL si no tiene inversor padre
     */
    public final function getParent(){
        if( $parent_id = $this->getValue(self::FIELD_META_PARENT_ID,0)){
            return self::ImportById($parent_id);
        }
        return null;
    }
    /**
     * @return int ID de inversor padre
     */
    public final function getParentId(){
        return $this->getValue(self::FIELD_META_PARENT_ID,0);
    }
    /**
     * @return int Retorna el estado del inversor
     */
    public final function getStatus(){
        //return $this->getValue(self::FIELD_META_STATUS,self::INVESTOR_STATUS_NEW);
        return intval( $this->getValue(self::FIELD_META_STATUS,self::INVESTOR_STATUS_NEW) );
    }
    /**
     * @return int Plan de afiliado
     */
    public final function getPlan(){
        return intval( $this->getValue(self::FIELD_META_PLAN,self::INVESTOR_PLAN_BASE) );
    }
    /**
     * @return int Retorna el ID de inversor
     */
    public final function getId(){
        return intval($this->getValue(self::FIELD_META_ID,0));
    }
    /**
     * @return string Nombre completo del inversor
     */
    public final function getFullName(){
        return sprintf('%s %s',
                $this->getValue(self::FIELD_META_FIRST_NAME,''),
                $this->getValue(self::FIELD_META_LAST_NAME,''));
    }
    /**
     * @return string DNI del inversor
     */
    public final function getDocumentId(){
        return $this->getValue(self::FIELD_META_DOCUMENT_ID,'');
    }
    /**
     * @return String Retorna el nombre del inversor
     */
    public final function getName(){
        return $this->getValue(self::FIELD_META_FIRST_NAME,'');
    }
    /**
     * @return String retorna el apellido
     */
    public final function getLastName(){
        return $this->getValue(self::FIELD_META_LAST_NAME,'');
    }
    /**
     * @return string Nombre de usuario
     */
    public final function getUserName(){
        return $this->getValue(self::FIELD_META_USER_NAME,'');
    }
    /**
     * @return String Email del inversor
     */
    public final function getEmail(){
        return strip_tags( trim($this->getValue(self::FIELD_META_EMAIL,'')));
    }
    /**
     * @return string ID de formulario de inversor para capturar afiliados
     */
    public final function getKey(){
        return $this->getValue(self::FIELD_META_INVESTOR_KEY);
    }
    /**
     * @return String Fecha de alta del inversor
     */
    public final function getDateCreated(){
        return $this->getValue(self::FIELD_META_DATE_CREATED);
    }
    /**
     * @return String Último cambio de estado o actualización
     */
    public final function getDateUpdated(){
        return $this->getValue(self::FIELD_META_DATE_UPDATED);
    }
    /**
     * @return String |NULL Retorna el código de activación, si el inversor está en estado pendiente activación
     */
    public final function getActivationCode(){
        return $this->getValue(self::FIELD_META_INVESTOR_KEY);
    }
    /**
     * @return AirBoxInvestorModel[] Lista de inversores atraidos
     */
    public final function getChildren( $activated_only = false ){

        $children = array();
        
        $investor_id = $this->getValue(self::FIELD_META_ID,0);

        if( $investor_id ){

            $dbi = AirBoxDataBaseModel::getDatabase();

            $filters = $activated_only ?
                    array(
                        self::FIELD_META_PARENT_ID=>$investor_id,
                        //este parámetro no sirve, es necesario hacer una evaluación de valores en la query
                        self::FIELD_META_STATUS=>self::INVESTOR_STATUS_ACTIVE) :
                    array(self::FIELD_META_PARENT_ID=>$investor_id);
            
            $db_children = $dbi->get(
                    AirBoxDataBaseModel::DB_SOURCE_INVESTORS,
                    null, $filters );
            
            if( !is_null( $db_children) && count($db_children) ){

                foreach( $db_children as $child ){
                    $children[$child[self::FIELD_META_ID]] = new AirBoxInvestorModel($child);
                }
            }
        }
        
        return $children;
    }
    
    /**
     * Este método devuelve el número de Boxes efectivos que ha aportado este inversor
     * sumando los boxes que posee en propiedad al recuento de boxes en propiedad de todos los afiliados
     * atraidos por su form de inversión en todos los subniveles.
     * @param boolean $multilevel Describe si el recuento debe efectuarse en todos los subniveles o solo en el primer nivel
     * @return array Conjunto de Boxes de los inversores atraidos
     */
    public final function getChildBoxes( ){
        
        return $this->getId() > 0 ?
            AirBoxOrderModel::LoadBoxes(sprintf('inv.%s=%s AND box.%s > %s',
                AirBoxInvestorModel::FIELD_META_PARENT_ID,$this->getId(),
                AirBoxOrderModel::FIELD_META_TYPE,  AirBoxOrderModel::ORDER_TYPE_RESERVED)) :
            array();
    }
    /**
     * Retorna el valor de una propiedad del elemento actual
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public final function getValue($name, $default = null) {
        
        return parent::getValue($name, $default);
    }
    /**
     * Establece un valor para un campo definido o propiedad local
     * @param string $field
     * @param mixed $value
     * @return boolean
     */
    protected final function setValue($field, $value) {
        
        return ( $this->_updated = parent::setValue($field, $value) );
    }
    /**
     * @param boolean $all_boxes Insluye también los boxes reservados, no en propiedad
     * @return array Lista de Boxes reservados y adquiridos por el inversor
     */
    public final function getBoxes( $count_reserved = false ){

        $type = ( $count_reserved ) ?
                    AirBoxOrderModel::ORDER_TYPE_RESERVED :
                    AirBoxOrderModel::ORDER_TYPE_PURCHASED ;
        
        return AirBoxOrderModel::LoadInvestorBoxes( $type, $this );
    }
    /**
     * Método alternativo para recuperar los boxes
     * @param int $type Tipo de box
     * @return type
     */
    public final function getInvestorBoxes( $type ){
        return AirBoxOrderModel::FilterInvestorBoxes($type, $this );
    }
    /**
     * Recuento de boxes del inversor
     * @param int $from_level
     * @return int Número de boxes, desde el nivel solicitado
     */
    public final function countBoxes( $from_level = AirBoxOrderModel::ORDER_TYPE_RESERVED ){
        $counter = 0;
        $boxes = AirBoxOrderModel::LoadInvestorBoxes($from_level,  $this);
        foreach( $boxes as $box ){
            $counter += $box[AirBoxOrderModel::FIELD_META_AMOUNT];
        }
        return $counter;
    }
    /**
     * @return array Lista de transacciones del inversor
     */
    public final function getTransactions(){
        return AirBoxTransactionModel::listTransactions(array(
            AirBoxTransactionModel::FIELD_META_ACCOUNT_ID=>$this->getId()
        ));
    }
    /**
     * Actualiza los datos del inversor en la bbdd
     * No permite actualizaciones en inversores no registrados (NEW)
     * @return boolean Guarda / actualiza los datos del inversor en la BBDD
     */
    public final function save( $force = false ){

        $investor_id = $this->getId();
        
        $status = $this->getValue(self::FIELD_META_STATUS,self::INVESTOR_STATUS_NEW);
        
        if( $status && $investor_id && ($this->_updated || $force) ){

            //actualizar fecha
            $this->setValue(self::FIELD_META_DATE_UPDATED, date('Y-m-d H:i:s'));
            
            /**
             * 2016-04-20
             * Inicialmente vacío, por que no puede contener todos los datos del inversor
             * ANOTAR:
             *  usuario
             *  password
             *  email
             * no son campos propios de la tabla inversores, sino asociados desde la tabla de usuarios
             */
            $investor_data = array();
            /**
             * 2016-04-20
             * 
             * Corrección para evitar enviar una query de actualización que contiene campos
             * inexistentes en la tabla inversores
             * 
             */
            foreach($this->listUpdated() as $name => $content ){
                switch( $name ){
                    case self::FIELD_META_USER_NAME:
                    case self::FIELD_META_USER_PASS:
                    case self::FIELD_META_EMAIL:
                        //filtrar aquí todos los datos
                        break;
                    default:
                        $investor_data[$name] = $content;
                        break;
                }
            }
            
            if( count($investor_data) ){
                //después del filtrado de datos, comprobar que hay información por actualizar
                $dbi = AirBoxDataBaseModel::getDatabase();

                if( $dbi->update(AirBoxDataBaseModel::DB_SOURCE_INVESTORS,
                        $investor_data,
                        array(self::FIELD_META_ID=>$investor_id) )){

                    $this->_updated = false;

                    return true;
                }
            }
        }

        return false;
    }
    /**
     * @return String Muestra un mensake de estado actual del inversor
     */
    public static final function displayStatus( $status_id ){
        switch( $status_id ){
            case self::INVESTOR_STATUS_ACTIVE:
                return AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_STATUS_ACTIVE );
            case self::INVESTOR_STATUS_CLOSED:
                return AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_STATUS_CLOSED );
            case self::INVESTOR_STATUS_PENDING:
                return AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_STATUS_PENDING );
            case self::INVESTOR_STATUS_QUITTING:
                return AirBoxStringModel::__( AirBoxStringModel::LBL_INVESTOR_STATUS_QUITTING );
        }
    }
    /**
     * Retorna el descriptor del tipo de plan
     * @param int $plan_id
     * @return string
     */
    public static final function displayPlan( $plan_id ){
        switch( $plan_id ){
            case self::INVESTOR_PLAN_BASE:
                return AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_BASIC);
            case self::INVESTOR_PLAN_PLUS:
                return AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_PLUS);
            case self::INVESTOR_PLAN_PRO:
                return AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_PRO );
            default:
                return AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_NEW);
        }
    }
    /**
     * @return String Muestra el nombre completo del inversor
     */
    public final function displayName(){
        return sprintf('%s %s',
                $this->getValue(self::FIELD_META_FIRST_NAME,''),
                $this->getValue(self::FIELD_META_LAST_NAME,''));
    }
    /**
     * @return String Muestra el nombre de usuario
     */
    public final function displayUserName(){
        return $this->getValue(self::FIELD_META_USER_NAME,'');
    }
    /**
     * @return string Retorna el email del inversor
     */
    public final function displayEmail( $link = false, $class = '' ){
        
        $email = $this->getValue(self::FIELD_META_EMAIL );
        
        if( !is_null($email) ){
            return $link ? 
                    sprintf( '<a class="%s email" href="mailto:%s" target="_self">%s</a>',
                            $class,$email,$email):
                    sprintf( '<span class="%s email">%s</span>',$class,$email);
        }
        
        return AirBoxStringModel::__(AirBoxStringModel::LBL_UNDEFINED);
    }
    /**
     * @return string Retorna el teléfono del inversor
     */
    public final function displayTelephone( $link = false, $class = '' ){
        $telephone = $this->getValue(self::FIELD_META_TELEPHONE );
        
        if( !is_null($telephone) ){
            return $link ?
                    sprintf( '<a class="%s telephone" href="tel:%s" target="_self">%s</a>',
                            $class,$telephone,$telephone):
                    sprintf( '<span class="%s telephone">%s</span>',$class,$telephone);
        }
        return AirBoxStringModel::__(AirBoxStringModel::LBL_UNDEFINED);
    }
    /**
     * @return string Retorna el DNI del inversor
     */
    public final function displayDocumentId(){
        return $this->getValue(
                self::FIELD_META_DOCUMENT_ID,
                AirBoxStringModel::__(AirBoxStringModel::LBL_UNDEFINED));
    }
    /**
     * @return string Fecha de alta del inversor
     */
    public final function displayDateCreated( $class = '' ){
        return sprintf('<span class="%s date">%s</span>', $class,
                $this->getValue(self::FIELD_META_DATE_CREATED,'') );
    }
    /**
     * @return string Fecha de último cambio de estado
     */
    public final function displayDateUpdated( $class = '' ){
        return sprintf('<span class="%s date">%s</span>', $class,
                $this->getValue(self::FIELD_META_DATE_UPDATED,'') );
    }
    /**
     * @return String Genera un código de formulario para asociar a un nuevo inversor
     */
    private static final function generateFormCode(){
        return md5(uniqid(date('YmdHis'), true));
    }
    /**
     * Registra un usuario de wordpress y los datos del inversor sobre la tabla inversores
     * 
     * 2016-04-20 - Ahora no tira excepciones, sino que devuelve false, registrando un mensaje debidamente
     * en el area de notificación
     * 
     * @param AirBoxFormModel $form
     * @return WP_User Usuario WP registrado o NULL si hubo error
     * 
     * @throws Exception Error al validar la información del usuario en wordpress
     */
    private static final function registerWordPressUser( AirBoxInvestorModel $investor, $password = null ){

        if( $investor->getValue(self::FIELD_META_ID,0) == 0 ){

            if(is_null($password) ){
                $password = wp_generate_password();
            }
            
            $username = $investor->getValue(self::FIELD_META_USER_NAME);
            
            $email = $investor->getValue(self::FIELD_META_EMAIL);
            
            if( !validate_username( $username ) ){
                //por si cuelgan carácteres raros
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::LBL_ERROR_INVALID_USER,
                        AirBoxNotifyModel::LOG_TYPE_ERROR );

                return false;
            }

            if( username_exists($username ) ){
                //comprobar que no existen en la BBDD
                //consultar si interesa notificar este error, dado que se provee información de posibles
                //usuarios en la bbdd
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::LBL_ADVICE_USER_DUPLICATED,
                        AirBoxNotifyModel::LOG_TYPE_ERROR );

                return false;
            }

            if( email_exists($email) ){
                //no permitir registrar un usuario si ya existe un email
                //consultar si interesa notificar este error, dado que se provee información de posibles
                //usuarios en la bbdd
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::LBL_ADVICE_EMAIL_DUPLICATED,
                        AirBoxNotifyModel::LOG_TYPE_ERROR );

                return false;
            }

            //genera password de longitud 12 y carácteres especiales (por defecto)
            $user_id = wp_create_user( $username, $password, $email );

            //valida que se ha creado bien y que es mayor que 0
            if( is_numeric($user_id) && $user_id ){

                //registra el id de inversor
                $investor->setValue(self::FIELD_META_ID, $user_id);
                
                //registrar temporalmente la password en el login de inversor
                $investor->setMeta(
                        self::FIELD_META_USER_NAME,
                        self::FIELD_META_USER_PASS,
                        $password);
                
                $user = new WP_User($user_id);

                $user->first_name = $investor->getValue(self::FIELD_META_FIRST_NAME,$username);

                $user->last_name = $investor->getValue(self::FIELD_META_LAST_NAME,$username);

                $user->set_role(AirBox::getOption('user_role','subscriber'));

                //actualizar cambios registrados (set role es automático)
                if( wp_update_user($user) !== $user_id ){
                    AirBoxNotifyModel::RegisterLog(
                            AirBoxStringModel::__(AirBoxStringModel::LBL_WARNING_CANNOT_UPDATE_USER),
                            AirBoxNotifyModel::LOG_TYPE_ERROR);
                }

                return true;
            }
        }

        return false;
    }
    /**
     * Retorna la información de inversor del usuario actualmente conectado.
     * Solo se debe utilizar desde el front-end
     * 
     * @param WP_User $user
     * @return \AirBoxInvestorModel
     */
    public static final function ImportFromUser( WP_User $user ){
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        $inv_data = $dbi->get(AirBoxDataBaseModel::DB_SOURCE_INVESTORS,
                array(
                    self::FIELD_META_ID,
                    self::FIELD_META_FIRST_NAME,
                    self::FIELD_META_LAST_NAME,
                    self::FIELD_META_DOCUMENT_ID,
                    self::FIELD_META_TELEPHONE,
                    self::FIELD_META_STATUS,
                    self::FIELD_META_PLAN,
                    self::FIELD_META_INVESTOR_KEY,
                    self::FIELD_META_DATE_CREATED,
                    self::FIELD_META_DATE_UPDATED,
                    self::FIELD_META_AIRPOINTS
                ),
                array(self::FIELD_META_ID=>$user->ID));

        if( !is_null($inv_data) && count($inv_data) ){
            
            $investor = $inv_data[0];
            
            $investor[self::FIELD_META_ID] = $user->ID;
            
            $investor[self::FIELD_META_EMAIL] = $user->user_email;
            
            $investor[self::FIELD_META_USER_NAME] = $user->user_nicename;
            
            $investor[self::FIELD_META_FIRST_NAME] = $user->first_name;
            
            $investor[self::FIELD_META_LAST_NAME] = $user->last_name;

            return new AirBoxInvestorModel($investor);
        }
        
        return null;
    }
    /**
     * Crea un nuevo inversor en la base de datos, generando un usuario en el CMS son los parámetros
     * básicos especificados y asignando los valores requeridos de inicialización
     * 
     * Si incluye el formcode, el inversor será asignado a un inversor padre
     * 
     * El proceso está compuesto de varios subprocesos de validación y creación de usuario e inversor
     * 
     * Es necesario retornar excepciones y errores generados para notificar debidamente.
     * 
     * 2016-04-20 - Se añade el control de DNI para evitar repetir el DNI de inversor
     * 
     * @param AirBoxFormModel $formData
     * @param string $formcode Codigo de formulario de inversor padre, NULL si no tiene inversor padre
     * @return AirBoxInvestorModel|NULL Nuevo inversor creado. NULL si hubo error al procesar el alta
     */
    public static final function CreateInvestor( AirBoxFormModel $formData ){
        
        //importar inversor desde los datos del form (ya incluye los datos de usuario WP
        $investor = self::ImportByFormData($formData);
        
        $password = $formData->getValue(
                AirBoxInvestorModel::FIELD_META_USER_PASS,
                wp_generate_password() );
        
        $dni = $investor->getValue(self::FIELD_META_DOCUMENT_ID);
        
        if( !is_null($dni) ){
            if( count( self::loadInvestor(array(self::FIELD_META_DOCUMENT_ID=>$dni)) )){
                /*
                 * Control único de DNI 
                 */
                AirBoxNotifyModel::RegisterLog(
                        AirBoxStringModel::LBL_ADVICE_DOCUMENT_ID_DUPLICATED,
                        AirBoxNotifyModel::LOG_TYPE_ERROR);

                return null;
            }
        }
        

        if( self::registerWordPressUser( $investor, $password ) ){
            
            $date = date('Y-m-d H:i:s');
            
            //$boxes = $formData->getValue(self::FIELD_META_BOXES,1);

            //marca el estado pendiente de activar
            $investor->setValue(self::FIELD_META_STATUS, self::INVESTOR_STATUS_PENDING);
            //generar código de activación
            $investor->setValue(self::FIELD_META_INVESTOR_KEY, self::generateFormCode());
            //fecha de modificación del registro
            $investor->setValue(self::FIELD_META_DATE_UPDATED,$date );
            $investor->setValue(self::FIELD_META_DATE_CREATED,$date );
            //el plan inicial del inversor mientras no se efectue la compra es inactivo
            $investor->setValue(self::FIELD_META_PLAN, self::INVESTOR_PLAN_NONE);
            
            //Si formcode no es nulo, el nuevo inversor proviene desde un form de otro inversor padre
            if( !is_null( $formcode = $formData->getValue(self::FIELD_META_INVESTOR_KEY) ) ){
                $parent = self::ImportByInvestorKey($formcode);
                //establecer inversor padre
                $investor->setParent($parent);
            }
            
            $dbi = AirBoxDataBaseModel::getDatabase();
            
            $investor_data = $investor->getValues(array(
                self::FIELD_META_ID,
                self::FIELD_META_DOCUMENT_ID,
                self::FIELD_META_FIRST_NAME,
                self::FIELD_META_LAST_NAME,
                self::FIELD_META_TELEPHONE,
                self::FIELD_META_PARENT_ID,
                self::FIELD_META_PLAN,
                self::FIELD_META_STATUS,
                self::FIELD_META_INVESTOR_KEY,
                self::FIELD_META_DATE_CREATED,
                self::FIELD_META_DATE_UPDATED,
            ));

            //ejecutar inserción
            if( $dbi->create(AirBoxDataBaseModel::DB_SOURCE_INVESTORS, $investor_data)){

                $this->_updated = false;
            }
            
            //resuelto
            return $investor;
        }
        
        return null;
    }
    /**
     * Recuento de inversores desde un estado concreto o a un estado exclusivo
     * @param int $status Estado del inversor para el filtro
     * @param bool $single_status Define si se selecciona por un estado exclusivo o si realiza el recuento desde el estado inficado
     * @return int Recuento de inversores
     */
    public static final function CountInvestors( $status = self::INVESTOR_STATUS_PENDING, $single_status = true ){
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        $sql_investors = sprintf(
                'SELECT COUNT(*) AS investors FROM `%s`',
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_INVESTORS));
        
        $sql_investors .= ( $single_status ) ?
                sprintf( ' WHERE `status`=%s',$status ) :
                sprintf( ' WHERE `status`>=%s',$status ) ;
        
        $r_investors = $dbi->query( $sql_investors );
        
        return ( !is_null($r_investors) && count($r_investors) ) ?
            intval( $r_investors[0]['investors'] ) : 0 ;
    }
    /**
     * Lista los inversores activos
     * 
     * Wordpress no tiene esta funcionalidad incluida, que triste y penoso
     * 
     * pero se puede agregar metiendo metas en los ususarios, como siempre.
     * 
     * Puede interesar para una nueva revisión.
     * 
     * Es importante saber si hay inversores activos en la red antes de hacer un mantenimiento.
     * 
     * @return array
     */
    public static final function ListConnectedInvestors(){
        
        $investor_list = array();
        
        /*foreach(self::loadInvestor() as $investor ){
            $last_login = (get_user_meta($investor[self::FIELD_META_ID], 'last_login', true));
            $loginDate = new DateTime($last_login);
            $since_start = $loginDate->diff(new DateTime(current_time('mysql', 1)));
        }*/
        
        return $investor_list;
    }
    /**
     * Query de selección de registros de inversor
     * @param array $filters
     * @param int $limit
     * @return array
     */
    private static final function loadInvestor( array $filters = null, $limit = 0 ){
        
        $inv_columns = array(
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_ID,
                    AirBoxInvestorModel::FIELD_META_ID),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_AIRPOINTS,
                    AirBoxInvestorModel::FIELD_META_AIRPOINTS),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_AIRPOINTS,
                    AirBoxInvestorModel::FIELD_META_AIRPOINTS),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_DATE_CREATED,
                    AirBoxInvestorModel::FIELD_META_DATE_CREATED),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_DATE_UPDATED,
                    AirBoxInvestorModel::FIELD_META_DATE_UPDATED),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_DOCUMENT_ID,
                    AirBoxInvestorModel::FIELD_META_DOCUMENT_ID),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_FIRST_NAME,
                    AirBoxInvestorModel::FIELD_META_FIRST_NAME),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_LAST_NAME,
                    AirBoxInvestorModel::FIELD_META_LAST_NAME),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_PARENT_ID,
                    AirBoxInvestorModel::FIELD_META_PARENT_ID),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_PLAN,
                    AirBoxInvestorModel::FIELD_META_PLAN),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_STATUS,
                    AirBoxInvestorModel::FIELD_META_STATUS),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_TELEPHONE,
                    AirBoxInvestorModel::FIELD_META_TELEPHONE),
            sprintf('inv.%s AS %s',
                    AirBoxInvestorModel::FIELD_META_INVESTOR_KEY,
                    AirBoxInvestorModel::FIELD_META_INVESTOR_KEY),
        );
        
        $user_columns = array(
            sprintf('usr.%s AS %s',
                    'user_login',
                    AirBoxInvestorModel::FIELD_META_USER_NAME),
            sprintf('usr.%s AS %s',
                    'user_email',
                    AirBoxInvestorModel::FIELD_META_EMAIL),
        );
        
        //$investor_table = AirBoxDataBaseModel::DB_SOURCE_INVESTORS;

        //$user_table = AirBoxDataBaseModel::DB_SOURCE_USERS;
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        $query = sprintf(
                "SELECT %s,%s,parent.first_name AS parent_first_name,parent.last_name AS parent_last_name"
                . " FROM %s AS inv INNER JOIN %s AS usr ON ( inv.user_id=usr.ID )"
                . " LEFT JOIN %s AS parent ON ( inv.parent_id=parent.user_id )",
                implode(',', $inv_columns ),
                implode(',', $user_columns ),
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_INVESTORS),
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_USERS),
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_INVESTORS));
        
        if( !is_null( $filters ) ){
            
            $where = array();
            
            foreach( $filters as $field=>$value ){
                
                switch($field){
                    case self::FIELD_META_USER_NAME:
                    case self::FIELD_META_USER_PASS:
                    case self::FIELD_META_EMAIL:
                        $column = 'usr.'.$field;
                        break;
                    default:
                        $column = 'inv.'.$field;
                }

                if(is_array($value)){
                    $where[] = sprintf("%s IN ('%s')",implode("','",$value));
                }
                elseif(is_string($value)){
                    $where[] = sprintf("%s='%s'",$column,$value);
                }
                else{
                    $where[] = sprintf('%s=%s',$column,$value);
                }
            }
            
            $query .= ' WHERE '. implode(' AND ', $where);
        }
        
        $query .= ' ORDER BY inv.'.self::FIELD_META_DATE_CREATED;
        
        if( $limit ){ $query .= ' LIMIT '.$limit; }
        
        //die($query);
        
        return $dbi->query($query);
    }
    /**
     * Carga un inversor desde la bbdd
     * @param int $investor_id
     * @return \AirBoxInvestorModel
     */
    public static final function ImportById( $investor_id ){
        
        $db_investor = self::loadInvestor( array( self::FIELD_META_ID=>$investor_id ), 1);
        
        if( !is_null($db_investor) && count($db_investor) ){
            
            $investor = new AirBoxInvestorModel( $db_investor[0]);

            return $investor;
        }

        return null;
    }
    /**
     * Genera un perfil de inversor a través de un form de datos.
     * Si el form de datos incluye un ID de inversor, lo carga desde la bbdd (mostrar perfil de inversor)
     * Si el form de datos no incluye un ID de inversor, lo crea como NUEVO (alta de nuevo inversor)
     * @param AirBoxFormModel $form
     * @return AirBoxInvestorModel
     */
    public static final function ImportByFormData( AirBoxFormModel $form ){
        
        $id = $form->getIndex( true );
        
        $investor = !is_null($id) ?
                //carga un inversor desde la bbdd
                self::LoadById($id) :
                //genera una plantilla de inversor
                new AirBoxInvestorModel();
        
        //combina los valores del form sobre el inversor cargado/creado
        //el valor retornado son el número de valores asignados, 0 si ninguno
        if( $investor->mergeDictionaryValues( $form ) ){
            
            $investor->_updated = true;

            return $investor;
        }

        return null;
    }
    /**
     * Carga un inversor en función de su código de formulario
     * @param string $code
     * @return \AirBoxInvestorModel|null
     */
    public static final function ImportByInvestorKey( $investor_key ){
        
        if( !is_null($investor_key ) ){

            $db_investor = self::loadInvestor( array(self::FIELD_META_INVESTOR_KEY => $investor_key), 1 );

            if( !is_null( $db_investor) && count( $db_investor) ){

                return new AirBoxInvestorModel($db_investor[0]);
            }

        }
        return null;
    }
    /**
     * Genera una URL de activación de pedido
     * @param int $order_id
     * @return URL
     */
    public final function generateActivationUrl( $order_id ){
        return AirBoxRouter::RoutePublic(array(
            //aqui no utilizar constantes por que no sabemos si el cargador en uso es el de inversor
            AirBoxEventModel::EVENT_TYPE_COMMAND => 'activate_order',
            AirBoxInvestorModel::FIELD_META_INVESTOR_KEY => $this->getActivationCode(),
            AirBoxOrderModel::FIELD_META_ID=>$order_id
        ));
    }
    /**
     * Activa un inversor a través del código de activación.
     * Si la activación es correcta, retorna el inversor ya actualizado y activo en el sistema.
     * Sino retorna NULL
     * 
     * Un inversor SOLO puede ser activado cuando:
     *  - se provee su código de activación (generar algún código hash?)
     *  - su estado actual es pendiente de activación PENDING
     * 
     * Cualquier otro caso, este método devolverá NUL como resultado erroneo
     * 
     * @param int $order_id
     * @return boolean
     */
    public final function activateOrder( $order_id ){
        
        $order = AirBoxOrderModel::LoadOrderById($order_id, $this->getId());
        
        if( !is_null($order) && $order->getType() == AirBoxOrderModel::ORDER_TYPE_RESERVED ){
            
            if( $order->activate( $this ) ){
                
                //es el momento de comprobar cuantos boxes hay en propiedad por si se puede subir de nivel
                //comprobar estado del inversor y activar si procede
                //guardar en este punto!!!
                if( $this->promoteInvestor()->activateInvestor()->save() ){
                    $mailer = AirBoxMailerService::createService();
                    $mailer->setSubject( AirBoxStringModel::__(
                                AirBoxStringModel::EML_SUBJECT_NOTIFY_ACCOUNT_ACTIVATION));
                    $mailer->setContent( AirBoxStringModel::__compose(
                                AirBoxStringModel::EML_CONTENT_NOTIFY_ACCOUNT_ACTIVATION,
                                $this->getName()));
                    $mailer->addContent(AirBoxRenderer::renderInvestorShortCutLink());
                    //$mailer->dispatch();
                    AirBox::RegisterService($mailer);
                }

                //recompensa al inversor padre, si existe
                if( !is_null( $parent = $this->getParent()) ){
                    //recompensar con n AirPoints en función del plan
                    $parent->reward( $order->getAmount() );
                }

                //retornar inversor independientemente de que haya sido activado o ya esté activo
                return true;
            }
        }
        return false;
    }
    /**
     * Activa un inversor directamente sin necesidad de activar su pedido
     * @return AirBoxInvestorModel Referencia a sí mismo para encadenar acciones
     */
    public final function activateInvestor(){
        
        //comprobar estado del inversor
        if( $this->getStatus() < self::INVESTOR_STATUS_ACTIVE ){
            //lo marca como activo
            $this->setValue(self::FIELD_META_STATUS, self::INVESTOR_STATUS_ACTIVE);
        }

        return $this;
    }
    /**
     * Promociona un inversor al siguiente plan en función de los Boxes obtenidos si procede
     * @return \AirBoxInvestorModel Referencia a sí mismo para permitir encadenado de acciones
     */
    public final function promoteInvestor(){
        if( $this->getPlan() < self::INVESTOR_PLAN_PRO ){
            
            $amount = $this->countBoxes(AirBoxOrderModel::ORDER_TYPE_PURCHASED);
            //comprobar la definición de planes para evaluar si puede subir de nivel
            for( $plan=self::INVESTOR_PLAN_PRO; $plan > self::INVESTOR_PLAN_NONE; $plan-- ){
                if( $this->getPlan() < $plan
                        && $amount >= self::$_INVESTOR_PLAN[$plan]['min_boxes' ] ){
                    $this->setValue(self::FIELD_META_PLAN, $plan);
                    break;
                }
            }
        }
        return $this;
    }
    /**
     * Recompensa a un inversor padre al atraer a un nuevo afiliado.
     * ***Este método es complementario del de activación y solo debe ser accesible internamente!!
     * 
     * 2016-04-20
     * Ahora hay que proveer de un sistema para asignar inversores 'huerfanos' a un padre desde el back-end
     * Este método debe ser accesible desde fuera (ManagerBootstrap)
     * 
     * @param int $amount Unidades sobre las que generar la recompensa
     * @return boolean TRUE si se ha guardado correctamente
     */
    public final function reward( $amount ){
        
        if( $amount > 0 ){
            /*
             * Agregar airpoints de recompensa
             * 
             * Registrar transacción de airpoints
             */
            $airpoints = self::getAirPointReward( $this->getPlan(),$amount );
            
            $this->setValue(self::FIELD_META_AIRPOINTS, $this->getAirPoints() + $airpoints );

            //guardar actualización del inversor
            if( $this->save() ){

                if( AirBoxTransactionModel::CreateAirPointReward( $this, $airpoints, $amount) ){
                    //generada la notificación
                }
                
                /**
                 * registrada la transacción de recompensa de airpoints
                 * generar notificación al inversor padre
                 */
                $mailer = AirBoxMailerService::createService();
                //email de inversor recompensado
                $mailer->addRecipient($this->getEmail());
                //asunto
                $mailer->setSubject( AirBoxStringModel::__compose(
                        AirBoxStringModel::EML_SUBJECT_REWARD_NOTIFY,
                        array($amount)));
                //contenido del mensaje
                $mailer->setContent(AirBoxStringModel::__compose(
                        AirBoxStringModel::EML_CONTENT_NOTIFY_REWARD,
                        array($this->getName(),$amount,$airpoints)));
                //anexar enlace al panel de control
                $mailer->addContent(AirBoxRenderer::renderInvestorShortCutLink());
                //ejecutar envío
                //$mailer->dispatch();
                AirBox::RegisterService($mailer);

                return true;
            }
        }
        return false;
    }
    /**
     * Retira una cantidad de airpoints del inversor
     * @param int $amount
     * @return boolean
     */
    public final function removeAirpoints( $amount ){

        $airpoints = $this->getValue(self::FIELD_META_AIRPOINTS,0);

        if( $amount > 0 && $airpoints  > 0 ){

            //retira los airpoints especificados, si el valor es superior al valor de airpoints dejar 0 AP
            $this->setValue(self::FIELD_META_AIRPOINTS,
                    ($airpoints > $amount) ?
                    $airpoints - $amount : 0);

            return $this->save();
        }
        return false;
    }
    /**
     * @param int $amount Cantidad de AP a integrar
     */
    public final function giveAirPoints( $amount ){
        
        $current = $this->getAirPoints();
        
        $this->setValue(self::FIELD_META_AIRPOINTS, $current + $amount );
        
        $this->save();
    }
    /**
     * @return array Lista Global de inversores
     */
    public static final function listInvestors(){
        return self::loadInvestor();
    }
}