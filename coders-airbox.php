<?php

defined('ABSPATH') or die;
/**
 * Plugin Name: CODERS - AirBox
 * Version: 0.0.1
 * Author: Coder#1
 * Text Domain: mnk_coinbox
 * Domain Path: /languages/
 */

/**
 * App Starter
 * @author Coder#1
 */
abstract class AirBox {

    //CONSTANTES GLOBALES DEL PLUGIN
    const ENDPOINT = 'coinbox';
    /**
     * @var string[]
     */
    private static $_status = array();
    /**
     * @var array Lista de componentes del plugin
     */
    private static $_core = array(
        //core components
        //'log',
        //'db',
        //'service',
        //'model',
        //'request',
        //'response',
        //'view',
        //'text'
    );
    /**
     * @var array
     */
    private static $_settings = array(
        //
        'app_name',
    );
    /**
     *
     * @var type register here all reqiuired components, models, etc
     */
    private $_components = array(
        //
    );

    /**
     * @var AirBox Instancia activa del sistema de inversión
     */
    private static $_INSTANCE = null;


    protected function __construct() {

       $this->preload(); 
        
    }
    
    /**
     * @return string
     */
    public final function __toString() {
        $NS = explode('\\', get_class($this ) );
        $class = $NS[count($NS) - 1 ];
        $suffix = strrpos($class, 'Module');
        return strtolower( substr($class, 0 ,$suffix) );
    }
    
    /**
     * @param string $text
     * @return string
     */
    public static final function __( $text ){
        
        return __( $text , 'coders_artpad');

        //return \CODERS\AirBox\Text::__($text);
    }
    
    /**
     * @param string $component
     * @param string $type
     * @return boolean
     */
    private final function register( $component , $type ){

        $path = self::path(sprintf('components/%s/%s.php',
                strtolower( $type ),
                strtolower( $component ) ) );

        if(file_exists($path)){
            require $path;
            return TRUE;
        }
        
        return FALSE;
    }

    /**
     * @return \AirBox
     */
    private final function preload(){
        foreach( $this->_components as $type ){
            $component = explode('.', $type );
                if( !$this->register( $component[0], $component[1]) ){
                    self::notice(sprintf('Invalid component %s.%s',$component[0],$component[1]));
                }
        }
        return $this;
    }
    /**
     * 
     * @param string $component
     * @return \AirBox
     */
    protected final function import( $component ){
        if( !in_array($component, $this->_components)){
            $this->_components[] = $component;
        }
        return this;
    }

    /**
     * @param string $path
     * @return string
     */
    public static final function path( $path = '' ){
        $root = preg_replace( '/\\\\/', '/', CODERS__COINBOX__DIR );
        return strlen($path) ? sprintf('%s/%s',$root,$path) : $root;
    }


    /**
     * Retorna el valor de un parámetro o un valor por defecto si no existe
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    public final function getOption( $param, $default = null){
        
        return get_option( $param, $default); 
    }
    /**
     * @return array Lista de parámetros disponibles del plugin
     */
    protected final function listOptions(){
        
        $list = array();
        
        foreach( self::$_settings as $var ){
        
            $list[ $var ] = $this->getOption($var,false);
        }

        return $list;
    }
    /**
     * @param string $param
     * @param mixed $value
     * @return boolean
     */
    protected final function setOption( $param, $value ){
        
        if( isset( self::$_settings[$param]) ){
            return update_option($param, $value, true );
        }
        
        return false;
    }
    
    
    public static final function init(){
        
        //one time loader
        if(!self::check('init')){ return; }
        
        //Activate plugin
        register_activation_hook(__FILE__, function( ){
            //database setup check
            //rewrite check
            \AirBox::setupRewrite(true);
        });
        //Disable plugin
        register_deactivation_hook(__FILE__, function(){
            //flush_rewrite_rules( );
        });
        
        //Initialize framework
        define('CODERS__COINBOX__DIR',__DIR__);
        define('CODERS__COINBOX__URL', plugin_dir_url(__FILE__));
        
        //register dependencies        
        foreach( self::$_core as $class ){
            require_once( sprintf( '%s/classes/%s.php' , CODERS__COINBOX__DIR , $class ) );
        }
        
        //register ajax handlers
        add_action( sprintf('wp_ajax_%s_admin',AirBox::ENDPOINT) , function(){
            //admin ajax
        }, 100000 );
        add_action( sprintf('wp_ajax_%s_public',AirBox::ENDPOINT) , function(){
            //public ajax
        });
        //add_action( sprintf('wp_ajax_nopriv_%s_admin',AirBox::ENDPOINT) , 'exit', 100000 );
        //add_action( sprintf('wp_ajax_nopriv_%s_public',AirBox::ENDPOINT) , 'exit', 100000 );
        
        //Initialize integration
        add_action( 'init' , function(){
            if(is_admin()){
                //load amin modules
            }
            else{
                if( !self::check('rewrite') ){ return; }
                //now let wordpress do it's stuff with the query router
                \AirBox::setupRewrite();
            }
        },10);
        //Setup Route management
        //add_action( 'parse_query', function( ){
        add_action( 'template_redirect', function( ){
            $endpoint = get_query_var( AirBox::ENDPOINT  );
            if ( strlen($endpoint)) {
                if( !AirBox::check('redirect')) { return; }
                global $wp_query;
                $wp_query->set('is_404', FALSE);
                if( !AirBox::redirect($endpoint) ){
                    wp_die('Wrong Endpoint');
                }
                else{
                    exit;
                }
            }
        },10);
    }
    /**
     * 
     * @param string $endpoint
     * @return boolean
     */
    public static final function redirect( $endpoint ){
        
        $path = explode('-', strtolower($endpoint));
        switch ($path[0]) {
            case 'admin':
                //locked in public
                break;
            default:
                $app = self::create($path[0]);
                return $app !== false ? $app->run( $endpoint ) : false;
        }

        return false;
    }
    
    public function run( $route = '' ){
        if(strlen($route)){
            var_dump($this);
            return true;
            //return \CODERS\ArtPad\Response::Route( preg_replace('/-/', '.', $route) );
        }
        return FALSE;
    }
    
    /**
     * @param string $endpoint
     * @return AirBox|Boolean
     */
    public static final function create( $endpoint ){
        
        if( self::$_INSTANCE !== NULL ){
            return self::$_INSTANCE;
        }
        
        if( strlen($endpoint) === 0){
            return FALSE;
        }
        
        $modName = strtolower( $endpoint );
        $path = self::path(sprintf('modules/%s/%s.php',$modName,$modName ));
        $class = sprintf('\CODERS\AirBox\%s\%sModule',$endpoint,$endpoint);

        if( $modName === 'admin' && !is_admin() ){
            return FALSE;
        }

        if( file_exists( $path ) ){
            require_once $path;
            if(class_exists( $class ) && is_subclass_of( $class, self::class ) ){
                self::check(sprintf('%s_module', $modName));
                self::$_INSTANCE = new $class();
                return self::$_INSTANCE;
            }
        }
        return FALSE;
    }


    /**
     * @param boolean $flush FALSE default
     * @global WP $wp
     * @global WP_Rewrite $wp_rewrite
     */
    public static final function setupRewrite( $flush = FALSE ){
        if( !self::check('setup_rewrite')) { return; }
        global $wp, $wp_rewrite;
        //import the regiestered locale's endpoint from the settinsg
        $endpoint = AirBox::ENDPOINT;
        add_rewrite_endpoint( $endpoint, EP_ROOT );
        $wp->add_query_var( $endpoint );
        $wp_rewrite->add_rule("^/$endpoint/?$",'index.php?' . $endpoint . '=$matches[1]', 'top');
        //and rewrite
        if ($flush ){
            $wp_rewrite->flush_rules();
            self::notify($endpoint . ' activated!!');
        }
    }
    
    /**
     * @global wpdb $wpdb
     * @global string $table_prefix
     * @return boolean
     */
    private static final function setupDataBase(){
        
        if( !self::check('database') ){ return; }
        
        $endpoint = self::ENDPOINT;

        global $wpdb,$table_prefix;

        $script_path = sprintf('%s/sql/setup.sql', preg_replace( '/\\\\/' , '/' , __DIR__ ) );

        if(file_exists($script_path)){
            $script_file = file_get_contents($script_path);
            if( FALSE !== $script_file && strlen($script_file)){
                $coders_table = $table_prefix . $endpoint;
                $script_sql = preg_replace('/{{TABLE_PREFIX}}/',$coders_table,$script_file);
                $tables = explode(';', $script_sql);
                $counter = 0;
                foreach( $tables as $T ){
                    if ($wpdb->query($T)) {
                        $counter++;
                    }
                    else {
                        //
                    }
                }
                return $counter === count( $tables );
            }
        }
        return FALSE;
    }
    
    /**
     * @param string $message
     * @global WP_Query $wp_query
     */
    public static final function terminate( $message = '' ){
        global $wp_query;
        $wp_query->set('is_404', FALSE);
        if(strlen($message) ){
            wp_die($message);
        }
        else{
            exit;
        }
    }

    /**
     * Send a message through the admin notifier
     * @param string $message
     * @param string $type (success, info, warning, error)
     * @param boolean $dismissible
     */
    public static final function notify( $message , $type = 'warning' , $dismissible = FALSE ){
        if( is_admin( ) ){
            add_action( 'admin_notices' , function() use( $type , $dismissible, $message ){
                printf('<div class="notice notice-%s %s"><p>%s</p></div>',
                        $type,
                        $dismissible ? 'is-dismissible' : '',
                        $message);
            });
        }
        else{
            //do something in public?
            $ts = date('Y-m-d H:i:s');
            printf('<p>[ %s : <strong>%s</strong> ] %s</p>',$ts,$type,$message);
        }
    }
        
    /**
     * @param string $status
     * @return boolean
     */
    public static final function check( $status ){
        if( strlen($status) && !array_key_exists($status, self::$_status) ){
            self::$_status[ $status ] = time();
            return TRUE;
        }
        return FALSE;
    }
    /**
     * @return array
     */
    public static final function stamp( $asCounter = FALSE ){
        if( $asCounter ){
            $first = 0;
            $output = array();
            foreach( self::$_status as $id => $ts ){
                if( $first > 0 ){
                    $output[ $id ] = ($ts - $first);
                }
                else{
                    $output[ $id ] = $first;
                    $first = $ts;
                }
                //$output[ $id ] = date('Y-m-d H:i:s',$ts);
            }
            return $output;
        }
        return self::$_status;
    }
    
    /**
     * @return AirBox
     */
    public static final function Instance(){ return self::$_INSTANCE; }
}


AirBox::init();


