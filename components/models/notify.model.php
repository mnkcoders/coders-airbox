<?php defined('ABSPATH') or die;
/**
 * Descriptor de LOGS, mensajes visuales y notificaciones del componente.
 * 
 * Descriptor de cadenas de texto (CONSTANTES) para la traducción a múltiples idiomas
 * 
 */
class AirBoxNotifyModel implements AirBoxIModel{
    
    const LOG_TYPE_ALL = 0;
    const LOG_TYPE_INFORMATION = 1;
    const LOG_TYPE_ADVICE = 2;
    const LOG_TYPE_WARNING = 3;
    const LOG_TYPE_ERROR = 4;
    const LOG_TYPE_RUNTIME_ERROR = 5;
    const LOG_TYPE_DEBUG = 6;
    
    private static $_LOGFILE = 'coinbox';
    
    /**
     * @var AirBoxNotifyModel[]
     */
    private static $_logs = array();
    
    private $_message;
    /**
     * Formato aaaammddhhmmss
     * @var string
     */
    private $_timestamp;
    private $_type = self::LOG_TYPE_INFORMATION;
    private $_context = null;
    
    private function __construct( $message, $type , array $arguments = null ) {
        
        $this->_message = $message;
        $this->_type = $type;
        $this->_timestamp = date('YmdHis');
        
        if( !is_null($arguments) ){
            foreach ($arguments as $var=>$val){
                switch($var){
                    case 'context':
                        $this->_context = $val;
                        break;
                    default:
                        break;
                }
            }
        }
        
        self::$_logs[] = $this;
    }
    /**
     * @return String
     */
    public final function getMessage( $full = false){
        return ($full) ? sprintf('%s: %s',$this->_message,$this->_context) : $this->_message;
    }
    /**
     * Serializa un evento LOG en formato HTML
     * @return HTML
     */
    public final function getHTML(){
        
        $UID = !is_null(AirBox::Instance()) ? AirBox::Instance()->getProfileData('ID'): 0;
        
        $context = !is_null( $this->_context ) ?
                    $this->_context :
                    sprintf('%s(%s)', AirBox::getProfile(), strval( $UID ) );
        
        return sprintf(
                "<p class=\"log-type %s\">[ %s : %s ] - %s</p>\n",
                $this->getType(true),
                $this->getTimeStamp(true),
                $context, $this->_message);
    }
    /**
     * @return int|String
     */
    public final function getType( $displayName = false ){
        return $displayName ?
                self::displayType($this->_type) :
                $this->_type;
    }
    /**
     * @param bool $dateTimeFormat Determina si se mostrará con formato de fecha-hora
     * @return string TimeStamp
     */
    public final function getTimeStamp( $dateTimeFormat = false ){
        
        if( $dateTimeFormat ){
            //mostrar en formato aaaa-mm-dd hh-mm-ss
            //20160727112015
            return sprintf('%s-%s-%s %s:%s:%s',
                substr($this->_timestamp,0,4),
                substr($this->_timestamp,4,2),
                substr($this->_timestamp,6,2),
                substr($this->_timestamp,8,2),
                substr($this->_timestamp,10,2),
                substr($this->_timestamp,12,2));
        }
        
        return $this->_timestamp;
    }
    /**
     * @return String
     */
    public final function getContext(){ return $this->_context; }
    /**
     * @return String Mensaje del log
     */
    public final function __toString(){
        
        if( !is_null($this->_context)){
            
            $context = '<ul class="context">';
            foreach( $this->_context as $var=>$val ){
                $context .= sprintf(
                        '<li><label>%s</label><strong>%s</strong></li>',
                        $var,$val);
            }
            $context .= '</ul>';
            
            return sprintf(
                    '<div class="notice coinbox-log-container %s"><p>%s</p>%s</div>',
                    $this->getType(true),$this->_message,$context);
        }

        return sprintf('<div class="notice coinbox-log-container %s">%s</div>',
                $this->getType(true),$this->_message);
    }
    /**
     * Convierte el tipo de notificación en representación textual
     * @param int $type
     * @return string
     */
    public static final function displayType( $type ){
        switch( $type ){
            case AirBoxNotifyModel::LOG_TYPE_DEBUG:
                return 'debug';
            case AirBoxNotifyModel::LOG_TYPE_INFORMATION:
                return 'info';
            case AirBoxNotifyModel::LOG_TYPE_ADVICE:
                return 'advice';
            case AirBoxNotifyModel::LOG_TYPE_WARNING:
                return 'warning';
            case AirBoxNotifyModel::LOG_TYPE_ERROR:
                return 'error';
            case AirBoxNotifyModel::LOG_TYPE_RUNTIME_ERROR:
                return 'exception';
            default:
                return 'log';
        }
    }
    /**
     * Retorna la ruta del fichero log y lo inicializa si es necesario
     * @return string
     */
    public static final function getLogFile(){
        
        $log_folder = CODERS__COINBOX__DIR.'logs';
        
        if( !file_exists( $log_folder ) ){
            mkdir($log_folder);
        }
        
        return sprintf('%s/%s.html', $log_folder,self::$_LOGFILE);
    }
    /**
     * Vuelca todos los logs desde el nivel indicado sobre el fichero de logs
     * 
     * @param int $level Nivel de los mensajes a exportar, por defecto errores
     * @return bool resultado
     */
    public static final function dumpLogFile( $level = self::LOG_TYPE_ERROR ){

        $log_file = self::getLogFile();
        
        if( ($handle = fopen($log_file, 'a'))){
            foreach( self::$_logs as $logData ){
                if( $logData->getType() >= $level ){
                    fwrite($handle, $logData->getHTML() );
                }
            }
            
            return fclose($handle);
        }
        return false;
    }
    /**
     * Reinicia el fichero log
     * @return bool Resultado
     */
    public static final function ResetLogFile(){
        
        $log_file = self::getLogFile();
        
        if(file_exists($log_file) && unlink($log_file)){
            //este será el primer log a registrarse al generarse el nuevo fichero
            self::RegisterLog('Log reiniciado', self::LOG_TYPE_DEBUG);
            return true;
        }
        return false;
    }
    /**
     * Carga el fichero de logs para mostrar
     * @return Array Lineas del fichero de logs
     */
    public static final function ImportLogFile(){
        
        $buffer = null;

        $log_file = self::getLogFile();

        if( file_exists($log_file) && filesize($log_file) ){

            if( ( $handle = fopen($log_file, 'r') ) ){
                
                $stream = fread($handle, filesize($log_file));

                $buffer = explode("\n",$stream);

                fclose($handle);
            }            
        }

        return !is_null( $buffer ) ? $buffer : array();
    }
    /**
     * Registra un LOG
     * @param String $message Mensaje a generar
     * @param int $type Tipo de notificación, por defecto informativa
     * @param String $context
     * @return \AirBoxNotifyModel
     */
    public static final function RegisterLog( $message, $type = self::LOG_TYPE_INFORMATION, $context = null ){
        
        $log = new AirBoxNotifyModel($message,$type,array('context'=>$context));
        
        return $log;
    }
    /**
     * Genera una excepción para la aplicación
     * @param String $message
     * @param array $arguments
     * @return AirBoxNotifyModel Log
     */
    public static final function RegisterException( Exception $ex , $context = null ){
        
        $attached = array(
                'exception_code'=>$ex->getCode(),
                'exception_source'=>$ex->getFile(),
                'exception_line'=>$ex->getLine(),
                'exception_trace'=>$ex->getTrace());
        
        if( is_array($context) ){
            foreach($context as $value){
                $attached[] = $value;
            }
        }
        elseif(is_string($context)){
            $attached[] = $context;
        }

        return new AirBoxNotifyModel(
                $ex->getMessage(),
                self::LOG_TYPE_RUNTIME_ERROR,
                $attached );
    }
    /**
     * @return AirBoxNotifyModel[] Lista de logs generados
     */
    public static final function getLogs( $level = self::LOG_TYPE_INFORMATION ){
        
        $list = array();
        
        foreach( self::$_logs as $logData ){
            if( $logData->_type >= $level ){
                $list[] = $logData;
            }
        }

        return $list;
    }
    /**
     * Ejecuta el cargador de logs de la vista de administración en el back-end
     * @param int $type Tipo de mensajes
     */
    public static final function showAdminMessages( $type = self::LOG_TYPE_INFORMATION ){
        
        add_action( 'admin_notices', function() use( $type ){ 
            foreach( AirBoxNotifyModel::getLogs(AirBoxNotifyModel::LOG_TYPE_INFORMATION) as $log ){
                if( $log->getType() >= $type ){
                    echo sprintf('<div class="%s"><p>%s</p></div>',
                            $log->getType( true ),
                            AirBoxStringModel::__($log->_message));
                }
            }
        } );
    }
}