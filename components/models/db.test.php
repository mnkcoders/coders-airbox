<?php defined('ABSPATH') or die;
/**
 * Gestor de conexión a la base de datos WP
 */
abstract class AirBoxDBModel extends wpdb implements AirBoxIModel{
    
    const DB_SOURCE_USERS = 'users';
    
    const DB_SOURCE_PARAMETERS = 'parameters';
    const DB_SOURCE_BOXES = 'boxes';
    const DB_SOURCE_INVESTORS = 'investors';
    const DB_SOURCE_NOTIFICATIONS = 'notifications';
    const DB_SOURCE_TRANSACTIONS = 'transactions';
    
    
    const PLUGIN_DB_NAMESPACE = 'globalcoinbox';
    
    /**
     * @var wpdb Conector WPDB
     */
    private static $_instance = null;
    /**
     * Retorna el nombre completo y prefijo de la tabla solicitada
     * @param string $resource
     * @return string Nombre completo y prefijo WP de la tabla
     */
    public static final function getTable( $resource ){
        if( !is_null( self::$_instance) ){
 
            switch( $resource ){
                case self::DB_SOURCE_USERS:
                    return self::$_instance->prefix.$resource;
                case self::DB_SOURCE_BOXES:
                case self::DB_SOURCE_INVESTORS:
                case self::DB_SOURCE_NOTIFICATIONS:
                case self::DB_SOURCE_PARAMETERS:
                case self::DB_SOURCE_TRANSACTIONS:
                    return sprintf( '%s%s_%s',
                            self::$_instance->prefix,
                            self::PLUGIN_DB_NAMESPACE,
                            $resource);
            }

        }
        return NULL;
    }
    
    /**
     * Retorna la instancia del objeto WPDB global
     * @global wpdb $wpdb
     * @return wpdb
     */
    public static final function getDb(){
        if(is_null( self::$_instance) ){
            
            global $wpdb;
            
            self::$_instance = $wpdb;
        }
        
        return self::$_instance;
    }
}