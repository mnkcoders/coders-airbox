<?php defined('ABSPATH') or die;
/**
 * Gestor de caché de la aplicación
 * 
 * @todo Por implementar en el plugin y testar
 */
class AirBoxCacheModel implements AirBoxIModel{
    /**
     * Porcentaje del progreso global
     * @var String
     */
    const CACHE_GLOBAL_PROGRESS = 'global_progress';    
    /**
     * Contador de inversores activos
     * @var string
     */
    const CACHE_GLOBAL_INVESTORS = 'global_investors';
    /**
     * Contador global de unidades totales
     * @var string
     */
    const CACHE_GLOBAL_UNITS = 'global_units';
    /**
     * Contador global de unidades adquiridas
     * @var string
     */
    const CACHE_GLOBAL_OWNED = 'global_owned';
    /**
     * Contador global de unidades disponibles
     * @var string
     */
    const CACHE_GLOBAL_AVAILABLE = 'global_available';
    /**
     * Contador global de unidades reservadas
     * @var string
     */
    const CACHE_GLOBAL_RESERVED = 'global_reserved';
    /**
     * Contador de unidades en ciclos completados
     * @var string
     */
    const CACHE_GLOBAL_COMPLETED = 'milestone_completed';
    /**
     * Indicador de ciclo actual
     * @var string
     */
    const CACHE_MILESTONE_CURRENT = 'milestone_current';
    /**
     * Contador ciclos
     * @var string
     */
    const CACHE_MILESTONE_COUNT = 'milestone_count';
    /**
     * Progreso del ciclo actual
     * @var string
     */
    const CACHE_MILESTONE_PROGRESS = 'milestone_progress';
    /**
     * Contador de unidades totales de ciclo
     * @var string
     */
    const CACHE_MILESTONE_UNITS = 'milestone_units';
    /**
     * Contador de unidades adquiridas de ciclo
     * @var string
     */
    const CACHE_MILESTONE_OWNED = 'milestone_owned';
    /**
     * Contador de unidades disponibles de ciclo
     * @var string
     */
    const CACHE_MILESTONE_AVAILABLE = 'milestone_available';
    /**
     * Beneficio generado por unidades globales del proyecto
     * @var String
     */
    const CACHE_PROFIT_UNITS = 'profit_units';
    /**
     * Beneficio generado por unidades de ciclos completados
     * @var String
     */
    const CACHE_PROFIT_COMPLETED = 'profit_completed';
    /**
     * Beneficio generado por unidades del ciclo actual
     * @var String
     */
    const CACHE_PROFIT_MILESTONE_UNITS = 'profit_milestone_units';
    /**
     * Beneficio generado por unidades del ciclo actual
     * @var String
     */
    const CACHE_PROFIT_MILESTONE_COMPLETED = 'profit_milestone_completed';
    /**
     * Beneficio generado por unidades del proyecto global
     * @var String
     */
    const CACHE_PROFIT_GLOBAL = 'profit_global';
    
    /**
     * Miembros de la caché
     * @var array
     */
    private $_cache = array( );
    
    private final function __construct( array $cache ){
        
        foreach( $cache as $var => $val ){
            $this->_cache[$var] = $val;
        } 
    }
    /**
     * Carga una instancia de la caché de la aplicación
     * @return \AirBoxCacheModel
     */
    public static final function InitializeCache( array $cache = null ){

        return new AirBoxCacheModel( $cache );
    }    
    /**
     * Acceso a los valores de la caché (por implementar en todos los ámbitos)
     * @param string $cache
     * @param mixed $default
     * @return mixed
     */
    public final function get( $cache, $default = null ){

        $method = sprintf( 'get_%s_cache', $cache );
        
        if( isset($this->_cache[ $cache ]) ){
            return $this->_cache[ $cache ];
        }
        elseif( method_exists($this, $method)){
            return $this->$method( );
        }
        else{
            return $default;
        }
    }
    /**
     * Actualiza la caché solicitada si ha habido cambios
     * @param string $var
     * @param mixed $value
     * @return boolean
     */
    public final function set( $var, $value ){
        if( isset( $this->_cache[ $var ]) && $this->_cache[$var] != $value ){
            $this->_cache[$var] = $value;
            return true;
        }
        return false;
    }
    /**
     * @return array
     */
    public final function dump(){
        return $this->_cache;
    }
    /**
     * @return float Progreso global del proyecto
     */
    private final function get_global_progress_cache(){
        
        $total_units = $this->get(self::CACHE_GLOBAL_UNITS, 0 );
        
        $owned_units = $this->get(self::CACHE_GLOBAL_OWNED,0);
        
        return $total_units > 0 ? ( $owned_units / (float)$total_units ) * 100 : 0;
    }
    /**
     * @return int Boxes disponiblesde ciclo
     */
    private final function get_global_available_cache(){

        $available = $this->get(self::CACHE_GLOBAL_UNITS,0) - $this->get(self::CACHE_GLOBAL_OWNED,0);
        
        //prevenir valores negativos
        return $available > 0 ? $available : 0;
    }
    /**
     * @return int Unidades a alcanzar por el objetivo actual
     */
    private final function get_milestone_units_cache(){
        
        $milestone_id = $this->get(self::CACHE_MILESTONE_CURRENT,0);
        
        if( $milestone_id ){
            
            $milestone = AirBoxMilestoneModel::ImportMilestone($milestone_id);
            
            return $milestone->getGoal();
        }
        
        return 0;
    }
    /**
     * @return int Progreso del milestone Actual
     */
    private final function get_milestone_owned_cache(){

        //unidades de los ciclos completados
        $completed = $this->get(self::CACHE_GLOBAL_COMPLETED,0);
        //unidades adquiridas
        $owned = $this->get(self::CACHE_GLOBAL_OWNED);
        
        return ( $owned - $completed > 0 ) ? $owned - $completed : 0;
    }
    /**
     * Recuento de unidades de los milestones completados
     * @return int Recuento de unidades de los milestones completados
     */
    private final function get_milestone_completed_cache(){
        //unidades de los ciclos completados
        $milestone = $this->get(self::CACHE_MILESTONE_CURRENT,0);
        
        return $milestone > 0 ?
            AirBoxMilestoneModel::CountGlobalUnits($milestone-1) : 0;
    }
    /**
     * @return int Retorna el número de unidades faltantes para cerrar el objetivo
     */
    private final function get_milestone_available_cache(){
        
        $remaining = $this->get_milestone_units_cache() - $this->get_milestone_owned_cache();
        
        return ( $remaining > 0) ? $remaining : 0;
    }
    /**
     * @return int Ganancia actual hasta la fecha
     */
    private final function get_profit_units_cache(){
        return $this->get(self::CACHE_GLOBAL_OWNED,0)
                * AirBox::getOption('coinbox_cost',0);
    }
    /**
     * @return int Ganancia actual hasta la fecha sobre el objetivo actual
     */
    private final function get_profit_completed_cache(){
        return $this->get(self::CACHE_GLOBAL_COMPLETED)
                * AirBox::getOption('coinbox_cost',0);
    }
    /**
     * @return int Ganancia objetiva de la etapa en curso
     */
    private final function get_profit_milestone_units_cache(){
        return $this->get(self::CACHE_MILESTONE_UNITS,0)
                * AirBox::getOption('coinbox_cost',0);
    }
    /**
     * @return int Ganancia actual de la etapa en curso
     */
    private final function get_profit_milestone_completed_cache(){
        return $this->get(self::CACHE_MILESTONE_OWNED,0)
                * AirBox::getOption('coinbox_cost',0);
    }
    /**
     * @return int Ganancia total objetivo
     */
    private final function get_profit_global_cache(){
        return $this->get(self::CACHE_GLOBAL_UNITS,0) *
                AirBox::getOption('coinbox_cost',0);
    }
}