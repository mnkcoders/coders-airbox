<?php defined('ABSPATH') or die;
/**
 * Descriptor de eventos del sistema:
 * 
 * - Inputs de usuario: GET y POST desde forms y acciones URL
 * 
 * - Inputs de servicios activos CRON para notificaciones y actualizaciones de estado automáticas del sistema
 * 
 */
class AirBoxEventModel implements AirBoxIModel{
    /**
     * Tipos de evento
     */
    const EVENT_TYPE_INVALID = 'invalid';
    //sin datos, punto de entrada principal para el cargador del cliente
    const EVENT_TYPE_EMPTY = 'empty';
    //tipo de evento de acceso al sistema, valida acceso y genera id de sesión
    const EVENT_TYPE_SESSION = '_session';
    //tipo de evento genérico adjuntando datos GET y/o POST
    const EVENT_TYPE_COMMAND = '_action';
    //tipo de evento de contexto de la aplicación para cargar el módulo apropiado
    const EVENT_TYPE_CONTEXT = '_context';
    //tipo de evento de repositorio, para acceder a recursos adjuntos (imagenes documentos de cliente)
    const EVENT_TYPE_REPOSITORY = '_repository';
    //vista seleccionada para mostrar
    const EVENT_SELECTED_VIEW = 'view';
    /**
     * datos genéricos adjuntos del evento
     */
    //const EVENT_TYPE_COMMAND = '_command';
    const EVENT_DATA_ID = '_id';
    /**
     * @var AirBoxEventModel Evento Importado
     */
    private static $_event = null;

    private $_eData = array();
    private $_eType = self::EVENT_TYPE_INVALID;
    private $_eContext = null;
    private $_eUserID = 0;
    
    private function __construct( array $arguments = null ) {
        
        //ojo controlar donde se carga el id que puede ser que sea 0 antes de cargar la página
        $this->_eUserID = get_current_user_id();
        
        if( !is_null($this->requestClientIP()) && strlen($this->requestClientIP()) ){
            //asignar el tipo de evento solo cuando se verifique que la ip
            //del cliente es válida
            $this->_eType = self::EVENT_TYPE_EMPTY;
        }
        
        //si está validado y hay un input del evento
        if( $this->_eType !== self::EVENT_TYPE_INVALID && !is_null($arguments) ){
            
            //$this->_eContext = $arguments[self::EVENT_TYPE_CONTEXT];
            $this->_eContext = AirBox::getProfile();
            /*
             * Los eventos de tipo contexto van dirigidos a un controlador,
             * si bien pueden tener una definición request para seleccionar el método a ejecutar,
             * van regidos por el identificador del contexto o controlador a cargar
             */
            if( isset( $arguments[self::EVENT_TYPE_CONTEXT] ) ){
                
                $this->_eType = self::EVENT_TYPE_CONTEXT;
            
                $this->_eData[self::EVENT_TYPE_CONTEXT] = $arguments[self::EVENT_TYPE_CONTEXT];
                
                $this->_eData[self::EVENT_TYPE_COMMAND] = isset( $arguments[self::EVENT_TYPE_COMMAND] ) ?
                        $arguments[self::EVENT_TYPE_COMMAND] :
                        'default';
                
            }
            /**
             * Los eventos de tipo request asumen que el contexto es el módulo o controlador principal de la aplicación
             * 
             */
            elseif( isset($arguments[self::EVENT_TYPE_COMMAND]) ){

                //$this->_eContext = 'main';

                $this->_eType = self::EVENT_TYPE_COMMAND;
            
                $this->_eData[self::EVENT_TYPE_COMMAND] = $arguments[self::EVENT_TYPE_COMMAND];
                
            }
            /**
             * Los eventos de tipo sesión no van dirigiros a un controlador sinó al administrador de
             * sesión
             */
            elseif( isset( $arguments[self::EVENT_TYPE_SESSION]) ){

                $this->_eType = self::EVENT_TYPE_SESSION;

                $this->_eData[self::EVENT_TYPE_SESSION] = $arguments[self::EVENT_TYPE_SESSION];
            }

            foreach( $arguments as $param=>$value){
                switch( $param ){
                    case self::EVENT_TYPE_COMMAND:
                    case self::EVENT_TYPE_SESSION:
                    case self::EVENT_TYPE_CONTEXT:
                        break;
                    default:
                        $this->_eData[$param] = $value;
                        break;
                }
            }
        }
    }
    
    /**
     * @return \AirBoxEventModel
     */
    public static final function ImportInputVars(){
        
        if( is_null( self::$_event ) ){
            $values = array();

            $post = filter_input_array(INPUT_POST);

            $get = filter_input_array(INPUT_GET);

            if (!is_null($get) && count($get)) {
                $values = $get;
            }

            if (!is_null($post) && count($post)) {
                $values = array_merge($values, $post);
            }

            self::$_event = new AirBoxEventModel(count($values) ? $values : null );
        }
        
        return self::$_event;
    }
    /**
     * Importa un parámetro del evento
     * @param string $property
     * @param mixed $default
     * @return mixed
     */
    public final function get( $property, $default =null){
        return isset( $this->_eData[$property]) ?
            $this->_eData[$property] :
            $default;
    }
    /**
     * Establece una propiedad en el evento
     * @param string $property
     * @param mixed $value
     */
    public final function set( $property, $value ){
        $this->_eData[$property] = $value;
    }
    /**
     * @return int WP User ID
     */
    public final function getUID(){ return $this->_eUserID; }
    /**
     * @return array Devuelve todos los datos adjuntos en el evento
     */
    public final function getData(){
        $eData = array();
        foreach( $this->_eData as $var=>$val){
            switch($var){
                case self::EVENT_TYPE_CONTEXT:
                case self::EVENT_TYPE_COMMAND:
                case self::EVENT_DATA_ID:
                    break;
                default:
                    $eData[$var] = $val;
                    break;
            }
        }
        return $eData;
    }
    /**
     * @return string Event Type
     */
    public final function getType(){ return $this->_eType; }
    /**
     * @param string $default Contexto por defecto
     * @return string Contexto
     */
    public final function getContext(){ return $this->_eContext; }
    /**
     * @return string|NULL Dirección remota del cliente
     */
    public final function requestClientIP(){
        
        return filter_input(INPUT_SERVER, 'REMOTE_ADDR');
    }
}


