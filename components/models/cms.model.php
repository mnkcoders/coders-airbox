<?php defined('ABSPATH') or die;
/**
 * Gestor de métodos y eventos de WordPress
 * 
 * Control de opciones get_option/set_option
 * 
 * @todo Por implementar en el plugin y testar
 */
class AirBoxCMSModel implements AirBoxIModel{
    
    const WP_HOOK_INIT = 'init';
    const WP_HOOK_AFTER_SETUP_THEME = 'after_setup_theme';
    const WP_HOOK_PLUGINS_LOADED = 'plugins_loaded';
    const WP_HOOK_WIDGETS_INIT = 'widgets_init';
    const WP_HOOK_ADMIN_MENU = 'admin_menu';
    const WP_HOOK_ADMIN_ENQUEUE_SCRIPTS = 'admin_enqueue_scripts';
    const WP_HOOK_ENQUEUE_SCRIPTS = 'wp_enqueue_scripts';
    const WP_HOOK_ADMIN_INIT = 'admin_init';
    const WP_HOOK_HEADER = 'wp_head';
    const WP_HOOK_CONTENT = 'the_content';
    const WP_HOOK_POST = 'the_post';
    const WP_HOOK_FOOTER = 'get_footer';
    
    /**
     * No permitir instanciar de momento
     */
    private final function __construct() {
        
    }

    /**
     * Registra una acción a ejecutarse por el CMS
     * @param string $action
     * @param mixed $callback Método a ejeccutar
     */
    public static final function register_action( $action, $callback, $priority = 50 ){
        
        add_action( $action , $callback, $priority );
            
    }
    /**
     * 
     * @return AirBoxCMSModel | NULL
     */
    public static final function instance(){

        return new AirBoxCMSModel();
    }
}


