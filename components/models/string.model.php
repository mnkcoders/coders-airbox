<?php defined('ABSPATH') or die;
/**
 * Gestor de cadenas de traducción
 * 
 * Constantes y mensajes traducibles en la intranet
 * 
 */
class AirBoxStringModel implements AirBoxIModel{

    /**
     * Solicita un parámetro para mostrar el nombre del inversor acabado de registrar
     * @var String
     */
    const LBL_INFO_SIGNUP_LOGIN = 'Ya has sido registrado, <strong>%s</strong>! Recuerdas tu usuario y contraseña?';
    const LBL_INFO_ACCOUNT_ACTIVATION = 'Para proceder con la activaci&oacute;n de tu cuenta es necesario completar el primer pedido';
    const LBL_INFO_ORDER_RESERVED = 'Tu pedido ha sido agregado!';
    const LBL_INFO_ORDER_ACTIVATION = 'Tu pedido ha sido activado!';
    const LBL_INFO_ORDER_REMOVED = 'Se ha borrado un pedido';
    /**
     * Solicita un parámetro para mostrar el número de AirPoints descontados de la cuenta  de inversor en una operación de reinversión
     * @var String
     */
    const LBL_INFO_AIRPOINT_DISCOUNT = 'Se han descontado %s AirPoints de tu cuenta. Puedes borrar el pedido reservado para recuperarlos.';
    const LBL_INFO_AIRPOINT_RECOVER = 'Has recuperado %s AirPoints invertidos';
    const LBL_INFO_PROJECT_FINISHED = 'El proyecto ha finalizado';
    const LBL_INFO_PROJECT_FINISHED_DETAIL = 'El proyecto ha completado el cupo de inversores y no es posible registrar nuevas participaciones.';
    const LBL_INFO_REFUND_CONDITIONS = 'Puede solicitar aquí el reintegro de sus AirPoints aceptando las siguientes condiciones:';
    const LBL_INFO_REQUEST_BANK_ACCOUNT = 'Indique su n&uacute;mero de cuenta bancaria para poder procesar el reintegro por transferencia.';
    const LBL_INFO_REFUND_NOTIFIED = 'Se ha notificado del reintegro a %s';
    const LBL_INFO_ADMIN_CONTACT = 'Necesitas contactar con el administrador?';
    const LBL_INFO_ADMIN_CONTACT_DETAIL = 'Si eres inversor, por favor indica tu DNI en el formulario de contacto';
    const LBL_INFO_UNACTIVATED_ACCOUNT_WELCOME = 'Hola %s, <br/>bienvenid@ a la intranet de Global Airbox!';
    const LBL_INFO_AFFILIATION_TIP = 'Puedes ganar afiliados vendiendo BOXes desde tu formulario de afiliaci&oacute;n';
    const LBL_INFO_BOX_RESERVATION_TIP = 'Puedes comenzar a reservar tus boxes ahora mismo!';
    const LBL_INFO_INVEST_AIRPOINTS = 'Reinvertir %s AirPoints';
    const LBL_INFO_INVESTOR_FORM_TIP = 'Gana <strong>AirPoints</strong> captando inversores con tu formulario!!';
    const LBL_INFO_INVESTOR_UPGRADE_TIP = 'Puedes subir hasta el plan PRO re-invirtiendo en <strong>AirBox</strong>!';
    const LBL_INFO_AFFILIATION_FORM_TIP = 'Ya est&aacute;s registrado, no necesitas rellenar de nuevo este formulario. Env&iacute;a este enlace a tus contactos para captar inversores.';
    const LBL_INFO_TERMS_AND_CONDITIONS = 'Debe aceptar los t&eacute;rminos del contrato de inversi&oacute;n (descargar PDF)';
    //AVISOS
    const LBL_ADVICE_USER_DUPLICATED = 'Por favor, utilize otro nombre de usuario';
    const LBL_ADVICE_EMAIL_DUPLICATED = 'Por favor, utilice otra direcci&oacute;n de correo electr&oacute;nico';
    const LBL_ADVICE_DOCUMENT_ID_DUPLICATED = 'Su DNI ya existe en nuestra base de datos. Dispone ya de un usuario en AirBox?';
    const LBL_ADVICE_SINGLE_ORDER_ALLOWED = 'Atenci&oacute;n:<br/>El sistema solo permite procesar un pedido a la vez.<br/>Borre los pedidos innecesarios.';
    //ADVERTENCIAS
    const LBL_WARNING_NOT_ALLOWED_USER = 'El usuario %s no tiene acceso a esta intranet';
    const LBL_WARNING_OUT_OF_STOCK = 'Su pedido no se puede completar por que ha superado el número de boxes disponibles.';
    const LBL_WARNING_LIMIT_EXCEEDED = 'Ha intentado reservar mas boxes de los dispobibles, se han asignado todos los boxes disponibles a su solicitud.';
    const LBL_WARNING_CANNOT_CREATE_USER = 'No se ha podido crear su usuario. Contacte con el administrador.';
    const LBL_WARNING_AFFILIATION_FORM_FIELD_ERROR = 'Hay errores en el formulario';
    const LBL_WARNING_CANNOT_UPDATE_USER = 'El nuevo usuario no pudo ser actualizado';
    const LBL_WARNING_CANNOT_REGISTER_ORDER = 'No se ha podido crear el pedido. Contacte con el administrador';
    const LBL_WARNING_CANNOT_ACTIVATE_ORDER = 'No se ha podido activar el pedido. Contacte con el administrador';
    const LBL_WARNING_CANNOT_REMOVE_ORDER = 'Este pedido no puede ser borrado: %s';
    ///ERRORES
    const LBL_ERROR_INVALID_VALUE = 'Se requiere un valor v&aacute;lido';
    const LBL_ERROR_INVALID_EMAIL = 'El email no es v&aacute;lido';
    const LBL_ERROR_INVALID_LOADER = 'Cargador inv&aacute;lido';
    const LBL_ERROR_INVALID_USER = 'El nombre de usuario es inv&aacute;lido';
    const LBL_ERROR_INVALID_ORDER_ID = 'N&uacute;mero de pedido inv&aacute;lido';
    const LBL_ERROR_INVALID_PAYMENT_METHOD = 'M&eacute;todo de pago inv&aacute;lido';
    const LBL_ERROR_INVALID_INVESTOR = 'Inversor invalido';    
    const LBL_ERROR_VIEW_NOT_FOUND = 'Vista no encontrada';
    
    //títulos y cabeceras (para las vistas accesibles desde las opciones de menú utilizar LBL_MENU_OPTION_*
    const LBL_TITLE_AFFILIATION_FORM = 'Formulario de afiliaci&oacute;n';
    const LBL_TITLE_INVESTOR_ACCESS = 'Acceso Inversores';
    const LBL_TITLE_ACCESS_RESTRICTED = 'Acceso no autorizado';
    const LBL_TITLE_PROJECT_FINISHED = 'Vaya! el proyecto ya ha finalizado';
    const LBL_TITLE_FAQ_FORM = 'Quieres hacernos alguna consulta?';
    const LBL_TITLE_NEWS = 'Novedades AirBox';
    const LBL_TITLE_REFUND = 'Reintegro';
    const LBL_TITLE_CHECKOUT = 'Completar compra';
    const LBL_TITLE_PROJECT_PROGRESS = 'Progreso de la financiaci&oacute;n';
    const LBL_TITLE_ORDER_REVIEW = 'Historial de pedidos y progreso de financiaci&oacute;n';
    
    const LBL_MENU_OPTION_DASHBOARD = 'Panel de Control';
    const LBL_MENU_OPTION_HISTORY = 'Mi historial';
    const LBL_MENU_OPTION_PROFILE = 'Mi perfil';
    const LBL_MENU_OPTION_CALENDAR = 'Calendario';
    const LBL_MENU_OPTION_DISCONNECT = 'Desconectar';
    const LBL_MENU_OPTION_CONTRACT = 'Contrato de Inversor (PDF)';
    const LBL_MENU_OPTION_ADMIN = 'Administraci&oacute;n';
    
    const LBL_MENU_OPTION_SALEPOINTS = 'Puntos de Venta';
    const LBL_MENU_OPTION_NEWS = 'Noticias';
    const LBL_MENU_OPTION_SIMULATION = 'Simulaci&oacute;n';
    const LBL_MENU_OPTION_SETTLEMENT = 'Liquidaci&oacute;n';
    const LBL_MENU_OPTION_AFFILIATES = 'Programa de Afiliados';
    
    //botones genericos
    const LBL_BUTTON_BACK_TO_HOME = 'Volver a inicio';
    const LBL_BUTTON_BACK = 'Volver';
    const LBL_BUTTON_REQUEST_FORM = 'Remitir solicitud';
    const LBL_BUTTON_ORDER = 'Comprar';
    const LBL_BUTTON_CHECKOUT = 'Pagar';
    const LBL_BUTTON_READ_MORE = 'Leer m&aacute;s ...';
    const LBL_BUTTON_SIMULATION = 'Calcular';
    const LBL_BUTTON_REQUEST_REFUND = 'Solicitar Reintegro?';
    const LBL_BUTTON_INVESTOR_SHORTCUT = 'Acceder a mi panel de control';
    const LBL_BUTTON_AMIN_SHORTCUT = 'Acceder a la administraci&oacute;n';
    
    ////FORMULARIOS DE INVERSOR E INICIO DE SESIÓN
    const LBL_INVESTOR = 'Inversor';
    const LBL_USER_NAME = 'Nombre de Usuario';
    const LBL_USER_PASS = 'Password';
    const LBL_FIRST_NAME = 'Nombre';
    const LBL_LAST_NAME = 'Apellido';
    const LBL_DOCUMENT_ID = 'DNI';
    const LBL_EMAIL = 'Email';
    const LBL_PHONE = 'Tel&eacute;fono';
    const LBL_DATE_CREATED = 'Fecha de alta';
    const LBL_DATE_ORDER = 'Fecha de pedido';
    const LBL_DATE_MODIFIED = '&Uacute;ltima modificaci&oacute;n';
    const LBL_INVESTOR_STATUS = 'Estado';
    const LBL_INVESTOR_PLAN = 'Plan';
    const LBL_INVESTOR_KEY = 'C&oacute;digo de activaci&oacute;n';
    const LBL_INVESTOR_PARENT = 'Inversor superior';
    
    const LBL_INVESTOR_STATUS_ACTIVE = 'Cuenta validada';
    const LBL_INVESTOR_STATUS_CLOSED = 'Cuenta cerrada';
    const LBL_INVESTOR_STATUS_PENDING = 'Pendiente de activaci&oacute;n';
    const LBL_INVESTOR_STATUS_QUITTING = 'Solicitando cierre';
    
    const LBL_INVESTOR_PLAN_NEW = 'Nuevo';
    const LBL_INVESTOR_AFFILIATES = 'Inversores atraidos';
    const LBL_INVESTOR_SOLD_BOXES = 'BOXES vendidos';
    const LBL_INVESTOR_AIRPOINTS = 'Tienes %s AirPoints';
    const LBL_INVESTOR_AIRPOINTS_VALUE = 'Tus AirPoints tienen un valor de %s';
    
    const LBL_INVESTOR_PLAN_BASIC = 'Plan BASIC';
    const LBL_INVESTOR_PLAN_BASIC_DESC = 'De 1 a 2 BOXES';
    const LBL_INVESTOR_PLAN_PLUS = 'Plan PLUS';
    const LBL_INVESTOR_PLAN_PLUS_DESC = 'De 3 a 9 BOXES';
    const LBL_INVESTOR_PLAN_PRO = 'Plan PRO';
    const LBL_INVESTOR_PLAN_PRO_DESC = 'Desde 10+ BOXES';
    
    //resumen de afiliados
    const LBL_AFFILIATE_NAME = 'Tus Afiliados';
    const LBL_AFFILIATE_PROFIT = 'Beneficio';
    const LBL_AFFILIATE_LIST_EMPTY = 'Vaya, aqu&iacute; no hay nadie!';
    //etiquetas genericas de campo
    const LBL_UNDEFINED = 'Indefinido';
    const LBL_EMPTY = 'Vac&iacute;o';
    const LBL_REPEAT  = 'Repetir';
    const LBL_CLEAR = 'Vaciar';
    const LBL_SELECT = 'Seleccionar';
    const LBL_YES = 'Si';
    const LBL_NO = 'No';
    const LBL_TOTAL = 'Total';
    const LBL_DATE = 'Fecha';
    const LBL_DATE_ENTERED = 'Registrado';
    const LBL_DATE_ORDERED = 'Comprado';
    const LBL_AMOUNT = 'Cantidad';
    const LBL_VALUE = 'Valor';
    const LBL_UNITS = 'Unidades';
    const LBL_BOX_AMOUNT = 'Boxes';
    const LBL_TIME_AMOUNT = 'Horas';
    const LBL_PRICE_AMOUNT = 'Precio';
    const LBL_MILESTONE = 'Ciclo';
    const LBL_TYPE = 'Tipo';
    const LBL_DETAIL = 'Detalle';
    const LBL_MESSAGE = 'Mensaje';
    const LBL_PUBLISHED_ON = 'Publicado el %s';

    const LBL_AIRPOINTS_AMOUNT = 'AirPoints';
    const LBL_YOUR_BOX_AMOUNT = 'Tus Boxes';
    const LBL_YOUR_PRICE_AMOUNT = 'Invertido';
    const LBL_YOUR_AIRPOINTS_AMOUNT = 'Tus AirPoints';
    
    //const LBL_ORDER_AMOUNT = 'Cantidad';
    //const LBL_ORDER_PRICE = 'Precio';
    //const LBL_ORDER_INVEST_AIRPOINTS = 'Reinvertir mis <strong>%s</strong> <i>AirPoints</i>?';
    const LBL_ORDER_PAYMENT_METHOD = 'M&eacute;todo de pago';
    const LBL_ORDER_CANCEL = 'Borrar';
    const LBL_ORDER_REVIEW = 'Revisar';
    const LBL_ORDER_ID = 'Num. pedido';
    const LBL_ORDER_STATUS_PROCESSING = 'Procesando pago';
    const LBL_ORDER_PAYMETHOD_AIRPOINTS = 'Aplicada reinversi&oacute;n completa de AirPoints';
    const LBL_ORDER_SELECT_PAYMENT = 'Seleccione un m&eacute;todo de pago';
    
    const LBL_PROJECT_GLOBAL = 'Global';
    const LBL_PROJECT_STAGE = 'Ciclo actual';
    const LBL_PROJECT_STAGE_COMPLETED = 'Ciclo actual completado';
    const LBL_PROJECT_AVAILABLE_UNITS = 'Todav&iacute;a quedan unidades disponibles para el ciclo actual!';
    
    const LBL_CHECKOUT_PRODUCT_DISPLAY = '%s BOXES por %s €';
    const LBL_CHECKOUT_DISCOUNT = 'Aplicado descuento por reinversi&oacute;n';
    const LBL_CHECKOUT_ORDER_DETAIL = 'Detalles del pedido Nº %s';
    
    const LBL_WIDGET_COINBOX_FORM = 'Formulario AirBox';
    const LBL_WIDGET_COINBOX_FORM_DESC = 'Widget de vista de formulario para acceder a la intranet de AirBox';
    
    //TEXTOS EMAIL
    /**
     * @var String Mensaje genérico del footer del cuerpo de email de notificación
     */
    const EML_FOOTER_SIGNATURE = 'Mailer';
    /**
     * @var String Mensaje de activación de cuenta de inversor
     */
    const EML_SUBJECT_NOTIFY_ACCOUNT_ACTIVATION = 'Tu cuenta de inversor ha sido activada!';
    /**
     * Requiere de un número de pedido
     * 
     * @var String Mensaje de activación del pedido
     */
    const EML_SUBJECT_NOTIFY_ORDER_ACTIVATION = 'Tu pedido %s ha sido activado';
    /**
     * Solicitud de reintegro para administrador:
     * 
     * * Solicita N AirPoints
     * 
     * @var String
     */
    const EML_SUBJECT_NOTIFY_REQUEST_REFUND = 'Solicitud de reintegro de %s';
    /**
     * @var string Notificación de reintegro para inversor
     */
    const EML_SUBJECT_NOTIFY_REFUND = 'Su solicitud de reintegro ha sido enviada';
    /**
     * Requiere del uso de un parámetro
     * 
     * @var string Norifica la ejecución de reintegro de airpoints
     */
    const EML_SUBJECT_APPLY_REFUND = 'Se ha aplicado un reintegro de %s AirPoints';
    /**
     * Notificación de bienvenida para el inversor acabado de registrar
     * @var string
     */
    const EML_SUBJECT_NOTIFY_WELCOME = 'Bienvenid@ a Global AirBox';
    /**
     * Notificación de alta de inversor
     * 
     * Solicita usuario registrado
     * 
     * @var string
     */
    const EML_SUBJECT_NOTIFY_SIGNUP = 'Nuevo inversor  registrado: %s';
    /**
     * Solicita Número de boxes vendidos para mstrar en el asunto
     * @var string
     */
    const EML_SUBJECT_REWARD_NOTIFY = 'Has vendido %s BOXES!';
    /**
     * @var String Mensaje dde activación
     */
    const EML_CONTENT_NOTIFY_ACCOUNT_ACTIVATION = 'Hola %s, tu cuenta de inversor ya ha sido activada.';
    /**
     * Notificación para administrador con 3 parámetros:
     * 
     * * URL de inversor
     * * Nombre inversor
     * * Mensaje de texto del inversor
     * 
     * @var String
     */
    const EML_CONTENT_NOTIFY_REQUEST = '<p>Nuevo mensaje de <a href="%s" target="_blank">%s</a>:</p><p><i>%s</i></p>';
    /**
     * Contenido de la notificación de reintegro:
     * 
     * * Solicita cuerpo del mensaje de la solicitud
     * 
     * @var string
     */
    const EML_CONTENT_NOTIFY_REFUND = 'Hemos remitido tu solicitud al administrador de la intranet:<br/><br/><i>%s</i><br/><br/>Esperamos dar respuesta a tu petici&oacute;n en breve.';
    /**
     * Requiere de 2 parámetros, nomnbre y num de airpoints
     * 
     * @var String Notificación de ejecución del reintegro
     */
    const EML_CONTENT_APPLY_REFUND = '<p>Hola %s!,</p><p>acabamos de aplicar el reintegro de <strong>%s AirPoints</strong> sobre tu cuenta de inversor!</p>';
    /**
     * Solicita nombre del inversor, numero de boxes vendidos y cantidad de airpoints obsequiados
     * @var string Contenido de notificación de recompensa
     */
    const EML_CONTENT_NOTIFY_REWARD = 'Hola %s,<br/>uno de tus afiliados ha comprado %s BOXES.<br/>Has recibido %s AirPoints como recompensa!';
    /**
     * Contenido de bienvenida de inversor (suscripción)
     * 
     * Solicita:
     * 
     * * nombre
     * 
     * * Global AirBox
     * 
     * * usuario
     * 
     * * password
     * 
     * @var string
     */
    const EML_CONTENT_NOTIFY_WELCOME = '<h2>Hola %s,</h2><p><strong>Se bienvenid@ a nuestra red de inversores en %s!</strong></p><p>Tu usuario: <b>%s</b></p><p>Tu password: <b>%s</b></p>';
    /**
     * Mensaje de entrade de nuevo inversor para el administrador.
     * 
     * @var string
     */
    const EML_CONTENT_NOTIFY_SIGNUP = 'Se acaba de registrar un nuevo inversor en el sistema';
    /**
     * Requiere de 3 parámetros, nombre del inversor, num de pedido y url de acceso a su intranet
     * 
     * @var string Mensaje de activación del pedido
     */
    const EML_CONTENT_NOTIFY_ORDER_ACTIVATION = '<p>Hola %s,</p><p>Tu pedido Nº %s ya ha sido activado.</p><p><a href="%s" target="_blank">Quires verlo ahora?</a></p>';
    
    /**
     * Modelo de traducciones
     * @var AirBoxStringModel
     */
    private static $_instance = null;
    
    /**
     *
     * @var string
     */
    private $_stringData = array();
    
    /**
     * Ocultar constructor
     * 
     * La instancia de este gestor de idiomas solo como método alternativo si
     * no llega a funcionar la gestión de idiomas nativa de WP PO/MO.
     * 
     * Hay varios strings que no pegan bien. Cuando resuelvan la traducción y
     * se revise si funciona se resuelve.
     */
    private final function __construct( $locale ) {
        
        $path = self::importTranslation( $locale );
        
        if( $this->importLanguage($path) > 0 ){
            self::$_instance = $this;

        }
    }
    /**
     * Importa un set de cadenas y contenidos desde el fichero de idiomas seleccionado
     * @param type $lang_path
     * @return int Num de cadenas procesadas
     */
    private final function importLanguage( $lang_path ){
        
        $counter = 0;
        
        if(file_exists( $lang_path ) ){
            
            $handle = fopen($lang_path, 'r');
            
            if( $handle ){
                
                $buffer = explode("\n",fread($handle, filesize($lang_path)));
                
                foreach( $buffer as $line ){
                    
                    $index = strpos($line, '=');
                    
                    $this->registerString(
                            trim( substr($line, 0,$index)," \t" ),
                            trim( substr($line, $index, strlen($line)-$index)," \t"));
                    
                    $counter++;
                }
                
                fclose($handle);
            }
        }
        
        return $counter;
    }
    /**
     * 
     * @param string $stringId
     * @param string $content
     */
    private final function registerString( $stringId, $content ){
        $this->_stringData[$stringId] = $content;
    }
    /**
     * Instancia del gestor de traducciones
     * @return AirBoxStringModel
     */
    public static final function instance(){
        return !is_null( self::$_instance) ?
            //retorna la instancia por defecto
            self::$_instance :
            // mucho ojo con esta mierda de fucnión que funciona cuando ha pasado un número de hooks
            new AirBoxStringModel(get_locale());
    }
    /**
     * Permite la traducción de la cadena en el contexto de la aplicación de la intranet
     * 
     * Vale, ahora la gañanada del día, el text-domain no puede usarse como una variable (toma ya...)
     * hay que pasar el valor exacto mnk_coinbox en essta función, y menos mal que soy la hostia
     * y que he pensado en meterlo todo desde el principio en un gestor de cadenas que sino, aqui
     * no lo iba a arreglar ni dios con un search & replace sobre todo el proyecto...
     * 
     * Podéis echar la culpa de los extras y desvíos en el tiempo de desarrollo a los
     * subnormales de los desarrolladores de WP, que hacen las cosas tan bien pensadas ...
     * 
     * @param String $string
     * @return String
     */
    public static final function __( $string ){
        
        //return __( $string , mnk_coinbox ); //Esto no le mola a TruñoPress
        return __( $string , 'mnk_coinbox' );
    }
    /**
     * Permite la traducción de la cadena en el contexto de la aplicación de la intranet anexando
     * valores dinámicos en la cadena de texto.
     * @param String $string Modelo/Formato de cadena a traducir y procesar
     * @param mixed $values Lista de valores dinámicos a insertar en la cadena
     * @return String Cadena de salida traducida
     */
    public static final function __compose( $string, $values ){
        
        if(is_array($values)){
            return vsprintf( self::__($string), $values);
        }

        return sprintf( self::__($string), $values);
    }
    /**
     * Genera un fichero modelo de cadenas
     * @return array Lista de cadenas detectadas en el fichero
     */
    private static final function ImportTranslationStrings(){
        
        $source_path = CODERS__COINBOX__DIR . 'components/models/string.model.php';
        
        if(file_exists($source_path)){
            
            $handle = fopen( $source_path, 'r' );
            
            if( $handle ){

                $string_def = array();

                $input = fread( $handle, filesize($source_path));
                
                $string = ( $input !== false ) ? explode("\n", $input ) : '';
                
                for( $line=0 ; $line < count($string) ; $line++ ){

                    if(strpos($string[$line], 'const')){
                        $from = strpos($string[ $line ], " = '");

                        $to = strrpos($string[$line], "'");

                        //echo ($from . ': '$to.'<br/>');

                        if( $from !== false && $to !== false ){
                            $extract = substr($string[ $line ],
                                    $from + 4,
                                    $to - ($from + 4) );
                            $string_def[] = array( 'line' => $line+1, 'string' => $extract );
                        }
                    }
                }

                fclose($handle);
                
                return $string_def;
            }
        }

        return array();
    }
    /**
     * @param string $lang
     * @return bool FALSE si ya existe el fichero
     */
    public static final function GenerateTranslationModel( $lang, $locale ){

        $output_path = self::TranslationPath($lang, $locale);
        
        if(file_exists($output_path)){ return false; }
        
        $strings = self::ImportTranslationStrings();
        
        $handle = fopen($output_path,'w');

        if( $handle ){

            fwrite($handle, self::ImportLangHeader($lang) );
            
            foreach( $strings as $content ){
                fwrite($handle, sprintf("\n#: ../lib/models/string.model.php:%s\n",$content['line']) );
                fwrite($handle, sprintf("msgid \"%s\"\n",$content['string']) );
                fwrite($handle, sprintf("msgstr \"%s\"\n\n",$content['string']) );
            }
            
            return fclose($handle);
        }
        return false;
    }
    /**
     * Obtiene una ruta para el archivo de idioma solicitado
     * 
     * @param string $lang
     * @param string $locale
     * @return string
     */
    public static final function TranslationPath( $lang, $locale ){
        return sprintf( '%slanguages/%s-%s_%s.po',
                CODERS__COINBOX__DIR,
                mnk_coinbox ,
                $lang, $locale );
    }
    /**
     * Obtiene una ruta para el archivo de idioma solicitado
     * 
     * @param string $lang
     * @param string $locale
     * @return string
     */
    public static final function importTranslation( $locale ){
        return sprintf( '%slanguages/%s-%s.cfg',
                CODERS__COINBOX__DIR,
                mnk_coinbox ,
                $locale );
    }
    /**
     * Cabecera
     * @param string $lang
     * @return string
     */
    private static final function ImportLangHeader( $lang ){
        
        
        return '';
    }
}