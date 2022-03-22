<?php defined('ABSPATH') or die;
/**
 * Gestor de conexión a la base de datos WP
 * 
 * Investigar si conviene instanciar un objeto wpdb (wordpress database)
 * (extender proveedor desde esta clas)
 * 
 * o se debe acceder 
 * 
 */
class AirBoxDataBaseModel implements AirBoxIModel{
    
    const DB_SOURCE_USERS = 'users';
    
    const DB_SOURCE_PARAMETERS = 'parameters';
    const DB_SOURCE_BOXES = 'boxes';
    const DB_SOURCE_MILESTONES = 'milestones';
    const DB_SOURCE_INVESTORS = 'investors';
    const DB_SOURCE_NOTIFICATIONS = 'notifications';
    const DB_SOURCE_TRANSACTIONS = 'transactions';
    
    
    const PLUGIN_DB_TABLE_PREFIX = 'globalcoinbox';
    
    private $_settings = array();

    /**
     * @var WPDB Gestor chapucero de wp para la base de datos
     */
    private $_dbi = null;
   
    private function __construct() {
        
        //vaya mierda de CMS ....
        global $wpdb,$table_prefix;
        
        //$this->_settings['prefix'] = sprintf('%s%s_',$table_prefix,self::PLUGIN_DB_TABLE_PREFIX);
        $this->_settings['prefix'] = $table_prefix;
        
        /**
         * @var wpdb Wordpress DataBase
         */
        $this->_dbi = $wpdb;
    }
    /**
     * @return \AirBoxDataBaseModel
     */
    public static final function getDatabase(){
        return new AirBoxDataBaseModel();
    }
    /**
     * Conector de la base de datos de wordpress
     * @return wpdb;
     */
    public final function getWPDB(){
        return $this->_dbi;
    }
    /**
     * Retorna el nombre de la tabla con prefijo y todo
     * @param string $name Nombre de la tabla solicitada
     * @return null
     */
    public final function __get($name) {
        return $this->getTable( $name );
    }
    /**
     * Retorna el nombre completo y prefijo de la tabla solicitada
     * @param string $resource
     * @return string Nombre completo y prefijo WP de la tabla
     */
    public final function getTable( $resource ){
        switch( $resource ){
            case self::DB_SOURCE_USERS:
                return $this->_settings['prefix'].$resource;
            case self::DB_SOURCE_BOXES:
            case self::DB_SOURCE_INVESTORS:
            case self::DB_SOURCE_NOTIFICATIONS:
            case self::DB_SOURCE_MILESTONES:
            case self::DB_SOURCE_PARAMETERS:
            case self::DB_SOURCE_TRANSACTIONS:
                return sprintf( '%s%s_%s', $this->_settings['prefix'],
                self::PLUGIN_DB_TABLE_PREFIX, $resource);
        }
        
        return NULL;
    }
    /**
     * Query a la tabla $resource indicada
     * @param string $resource
     * @param mixed $columns
     * @param mixed $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public final function get( $resource, $columns = null , $filters = null, $limit = 0, $offset = 0 ){

        $table = $this->getTable($resource);
        
        if(is_array($columns)){
            $scope = implode(',', $columns);
        }
        elseif(is_string($columns)){
            $scope = $columns;
        }
        elseif( is_null($columns)){
            $scope = '*';
        }

        $sql_select = sprintf("SELECT %s FROM %s",$scope,$table);
        
        if( !is_null($filters) ){
            if(is_array($filters)){
                
                $where = '';
                
                foreach( $filters as $var=>$val ){
                    if(is_array($val)){
                        $where .= strlen($where) ?
                            sprintf( " AND {$var} IN ('%s')",implode("','", $val) ) :
                            sprintf( " WHERE {$var} IN ('%s')",implode("','", $val));
                    }
                    if(is_string($val)){
                        $where .= strlen($where) ?
                            " AND {$var}='{$val}'" :
                            " WHERE {$var}='{$val}'";
                    }
                    else{
                        $where .= strlen($where) ?
                            " AND {$var}={$val}" :
                            " WHERE {$var}={$val}";
                    }
                }
                
                $sql_select .= $where;
            }
            elseif(is_string($filters)){
                $sql_select .= ' WHERE '.$filters;
            }
        } 
        
        if( $limit ){
            $sql_select .= ' LIMIT '.$limit;
            
            if( $offset ){
                $sql_select .= ','.$offset;
            }
        }
        
        return $this->_dbi->get_results( $sql_select, ARRAY_A );
    }
    /**
     * @param string $table
     * @param array $filters
     * @return int Numero de registros afectados
     */
    public final function delete($resource, array $filters ){

        $table = $this->getTable($resource);
        
        return !is_null($table) ? $this->_dbi->delete($table, $filters ) : 0;
    }
    /**
     * Actualiza datos en la tabla seleccionada
     * @param string $resource
     * @param array $values
     * @param array $filters
     */
    public final function update( $resource, array $values, array $filters ){
        
        $table = $this->getTable($resource);
        
        return $this->_dbi->update( $table, $values, $filters );
    }
    /**
     * genera una inserción sobre la tabla indicada
     * @param String $resource
     * @param array $values
     * @param String|Array $format
     * @return int ID del registro insertado
     */
    public final function create( $resource, array $values , $format = null){
        
        $table = $this->getTable($resource);
        
        if( $this->_dbi->insert( $table, $values, $format ) ){
            return $this->_dbi->insert_id;
        }
        
        return 0;
    }
    /**
     * Ejecuta una query directa
     * @param SQL $query
     * @return array|null
     */
    public final function query( $query ){
        return $this->_dbi->get_results( $query , ARRAY_A );
    }
    /**
     * Comprueba si ha habido errores
     */
    public final function checkErrors( $interrupt = false ){
        if( $this->_dbi->last_error !== ''){
            if( $interrupt ){
                die($this->_dbi->print_error());
            }
            return true;
        }
        return false;
    }
    /**
     * Retorna el id insertado en la query INSERT
     * @return int
     */
    public final function get_insert_id(){
        return $this->_dbi->insert_id;
    }
    /**
     * @return SQL
     */
    public final function dump(){
        return $this->_dbi->last_query ;
    }
}