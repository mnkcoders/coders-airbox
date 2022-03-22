<?php
/**
 * 
 * Modelo de Objetivos o Milestones
 * 
 * Permite la creación de objetivos que facilitarán el desglose y recuento de unidades de una manera
 * mas preccisa, según la etapa o según el proyecto global.
 * 
 * Desde la versión 1.3.3 pasa a ser el gestor de unidades objetivo del proyecto
 * eliminando la necesidad de utilizar un simple parámetro de sistema que generaría
 * incoherencias para gestionar las etapas solicitadas, en lugar del progreso global.
 * 
 * Nota:
 * 
 * Si en adelante interesa también gestionar el precio de las unidades, es posible crear precios
 * diferenciados por etapa, para futuras revisiones.
 * 
 */
//class AirBoxMilestoneModel extends AirBoxDictionary implements AirBoxIModel{
class AirBoxMilestoneModel implements AirBoxIModel{
    
    const FIELD_META_ID = 'id';
    const FIELD_META_GOAL = 'goal';
    const FIELD_META_UNITS = 'units';
    //const FIELD_META_STATUS = 'status';
    const FIELD_META_DATE_COMPLETED = 'date_completed';
    const FIELD_META_DATE_LIMIT = 'date_limit';
    const FIELD_META_DATE_CREATED = 'date_created';
    
    const STAGE_STATUS_INACTIVE = 0;
    const STAGE_STATUS_STARTED = 1;
    const STAGE_STATUS_COMPLETED = 2;
    const STAGE_STATUS_EXPIRED = 3;
    

    private $_mData = array();

    protected function __construct(array $dataSet ) {
        
        //definición de campos en el constructor para extraer del modelo AirBoxDictionary
        $this->_mData[ self::FIELD_META_ID ] = 0;
        $this->_mData[ self::FIELD_META_GOAL ] = 0;
        $this->_mData[ self::FIELD_META_UNITS ] = 0;
        $this->_mData[ self::FIELD_META_DATE_CREATED ] = date('Y-m-d H:i:s');
        $this->_mData[ self::FIELD_META_DATE_LIMIT ] = AirBox::getOption('termination_date');
        $this->_mData[ self::FIELD_META_DATE_COMPLETED ] = '';
        
        if( !is_null($dataSet)){
            foreach( $dataSet as $field=>$value ){
                if( isset($this->_mData[$field]) ){
                    $this->_mData[$field] = $value;
                }
            }
        }
    }
    /**
     * Crea un registro de objetivos en la BD.
     * 
     * ESTA TP retorna siempre 0, asi que no fies de la ID que retorna el conecto WPDB
     * 
     * @param int $amount
     * @param String $date_limit
     * @param String $date_completed Establece la fecha de completado si procede, nulo por defecto
     * @return int
     */
    private static final function db_create( $amount, $date_limit, $date_completed = null ){
        
        $dbi = AirBoxDataBaseModel::getDatabase();

        $table = $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_MILESTONES);
        
        $columns = array(
            self::FIELD_META_ID,
            self::FIELD_META_GOAL,
            self::FIELD_META_DATE_LIMIT);
        
        $values = sprintf(
                "SELECT COUNT(*)+1 AS `id`,%s AS `goal`,'%s' AS `date_limit`",
                $amount, $date_limit );

        if( !is_null($date_completed) ){
            //concatenar
            $columns[] = self::FIELD_META_DATE_COMPLETED;
            $values .= sprintf(",'%s' AS `date_completed`", $date_completed );
        }

        $sql_create = sprintf('INSERT INTO `%s` (%s) %s FROM `%s`',
                $table, implode(',', $columns), $values, $table );

        $dbi->query($sql_create);
        
        //esto no funciona
        return $dbi->get_insert_id();
    }
    /**
     * Actualiza el objetivo en la BD
     * @return boolean
     */
    private final function db_update(){
        
        $dbi = AirBoxDataBaseModel::getDatabase();

        $this->_mData[self::FIELD_META_DATE_COMPLETED] = date('Y-m-d H:i:s');
        
        return $dbi->update(
                AirBoxDataBaseModel::DB_SOURCE_MILESTONES,
                array(self::FIELD_META_DATE_COMPLETED=>$this->_mData[self::FIELD_META_DATE_COMPLETED]),
                array(self::FIELD_META_ID=>$this->getId()));
    }
    /**
     * Lista los registros de objetivos de la bd
     * @param int $id PAra la selección de un único milestone por ID
     * @return array
     */
    private static final function db_load( $id = 0 ){

        $dbi = AirBoxDataBaseModel::getDatabase();

        $sql_stages = sprintf( 'SELECT * FROM `%s`',
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_MILESTONES));
        
        if( $id ){
            $sql_stages .= sprintf( ' WHERE `id`=%s', $id );
        }
        else{
            $sql_stages .= ' ORDER BY `id`,`date_created` ASC';
        }

        return $dbi->query($sql_stages);
    }
    /**
     * Crea y registra una nueva etapa de proyecto.
     * 
     * Si las unidades objetivas ya han sido superadas en el recuento de unidades adquiridas
     * marca ya el objetivo como completado para tener el seguimiento correcto.
     * 
     * @param int $amount
     * @param String $date_limit
     * @return AirBoxMilestoneModel|NULL
     */
    public static final function CreateMilestone( $amount, $date_limit  ){
        //capturar las unidades globales + el nuevo objetivo
        $total_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS) + $amount;
        //capturar las unidades totales adquiridas hasta la fecha
        $owned_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED);
        //marca la fecha de completado automáticamente si se requiere
        $date_completed = ( $owned_units >= $total_units ) ? date('Y-m-d H:i:s') : null;
        
        //marcar como completado y las unidades adquiridas son >= que las objetivas
        $milestone_id = self::db_create($amount, $date_limit, $date_completed );
        
        $milestone_data = array(
            self::FIELD_META_ID => $milestone_id,
            self::FIELD_META_GOAL => $amount,
            self::FIELD_META_DATE_LIMIT => $date_limit,
        );

        if( !is_null($date_completed ) ){
            $milestone_data[self::FIELD_META_DATE_COMPLETED] = $date_completed;
        }

        return new AirBoxMilestoneModel( $milestone_data );
    }
    /**
     * Carga una etapa
     * @param int $stage_id Número de etapa
     * @return AirBoxMilestoneModel | NULL
     */
    public static final function ImportMilestone( $stage_id ){
        
        $record = self::db_load($stage_id);
        
        if( !is_null($record) && count($record) ){
            
            $stage_data = $record[0];
            //unidades globales en propiedad
            $owned = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED);
            //unidades de las etapas anteriores
            $previous = self::CountGlobalUnits($stage_data[self::FIELD_META_ID]-1);
            //objetivo de la etapa en uso
            $goal = $stage_data[self::FIELD_META_GOAL];
            
            if( $owned > $previous ){
                //la etapa ha sido COMENZADA O COMPLETADA
                $stage_data[self::FIELD_META_UNITS] = $owned - $previous > $goal ?
                        $goal : $owned - $previous;
                
                //se añade el contador de unidades completadas al registro para importar sobre el objetivo
            }

            return new AirBoxMilestoneModel( $stage_data );
        }
        
        return null;
    }
    /**
     * Lista de milestones del sistema
     * @return AirBoxMilestoneModel[]
     */
    public static final function LoadMilestones(  ){
        
        $records = self::db_load();
        
        $milestone_list = array();
        //unidades globales en propiedad
        //$global_owned = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED);
        $global_owned = AirBoxOrderModel::CountBoxes(
                    AirBoxOrderModel::ORDER_TYPE_PURCHASED,0,false);

        foreach( $records as $milestone ){

            if( $global_owned > 0 ){
                //objetivo de la etapa en uso
                $goal = $milestone[self::FIELD_META_GOAL];

                //unidades completadas del milestone en uso
                if( $global_owned < $goal ){
                    $units = $global_owned;
                    //unidades globales ya consumidas
                    $global_owned = 0;
                }
                else{
                    $units =  $goal;
                    $global_owned -= $goal;
                }
                //esto es posible hacerlo con el item de la lista $milestone ya uqe solo afectará al mismo item
                $milestone[ self::FIELD_META_UNITS ] = $units;
            } 

            //ahora si, crear el nuevo milestone
            $milestone_list[] = new AirBoxMilestoneModel( $milestone );
        }

        return $milestone_list;
    }
    /**
     * Retorna las unidades asignadas en las etapas y objetivos de proyecto
     * 
     * @param int $milestone_id Suma solo las unidades de los milestones hasta el seleccionado. Si es 0 o menor, retorna todos los milestones
     * @return int Recuento de unidades asignadas
     */
    public static final function CountGlobalUnits( $milestone_id = 0 ){

        $dbi = AirBoxDataBaseModel::getDatabase();

        $sql_units = sprintf( 'SELECT SUM(`goal`) AS amount FROM `%s`',
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_MILESTONES) );
        
        if( $milestone_id > 0 ){
            //suma solo hasta el milestone en selección
            $sql_units .= sprintf(' WHERE `id`<=%s', $milestone_id );
        }
        
        $r_units = $dbi->query($sql_units);
        
        return (!is_null($r_units) && count($r_units ) && !is_null($r_units[0]['amount'])) ?
            intval( $r_units[0]['amount']) : 0;
    }
    /**
     * @param int $top_id Define el recuento de objetivos hasta el milestone proveido como límite
     * @return int Contador general de milestones
     */
    public static final function CountMilestones( $top_id = 0 ){
        
        $dbi = AirBoxDataBaseModel::getDatabase();

        $sql_milestones = sprintf( 'SELECT COUNT(*) AS milestones FROM `%s`',
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_MILESTONES) );
        
        if( $top_id ){
            //suma solo hasta el milestone en selección
            $sql_milestones .= sprintf(' WHERE `id`<=%s', $top_id );
        }
        
        $r_units = $dbi->query($sql_milestones);
        
        return (!is_null($r_units) && count($r_units ) && !is_null($r_units[0]['milestones'])) ?
            intval( $r_units[0]['milestones']) : 0;
    }
    /**
     * Retorna el id de objetivo actual comprobando a su vez cualquier actualización de los objetivos
     * de la lista.
     */
    public static final function CheckMilestone(){

        //unidades globales en propiedad
        //$owned_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED,0);
        $owned_units = AirBoxOrderModel::CountBoxes(
                    AirBoxOrderModel::ORDER_TYPE_PURCHASED,0,false);

        if( $owned_units ){

            $milestones = self::LoadMilestones();

            foreach( $milestones as $m ){
                if( $owned_units >= $m->getGoal() ){
                    if( !$m->getCompleted() ){
                        //marcar el milestone con la fecha actual
                        $this->_mData[self::FIELD_META_DATE_COMPLETED] = date('Y-m-d H:i:s');
                        $m->db_update();
                    } 
                    //si las unidades han alcanado o superado el objetivo
                    $owned_units -= $m->getGoal();
                }
                else{
                    //establece el nuevo milestone en la caché
                    AirBox::update_cache(AirBoxCacheModel::CACHE_MILESTONE_CURRENT, $m->getId());
                    //retorna el id del milestone
                    return $m->getId();
                }
            }
            //retornar el contador de ciclos
            return count($milestones);
        }
        
        return 0;
    }
    /**
     * Borra un objetivo
     * @param int $milestone_id
     * @return int Numero de registros borrados
     */
    public static final function RemoveMilestone( $milestone_id ){
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        return $dbi->delete(
                AirBoxDataBaseModel::DB_SOURCE_MILESTONES,
                array( AirBoxMilestoneModel::FIELD_META_ID => $milestone_id ) );
    }
    /**
     * Resetea la tabla de milestones en la BD
     */
    public static final function ResetMilestones(){

        $dbi = AirBoxDataBaseModel::getDatabase();

        $sql_reset = sprintf( 'TRUNCATE `%s`',
            $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_MILESTONES));
        
        $dbi->query($sql_reset);
    }
    /**
     * Interpreta el valor legible del estado de la etapa o milestone
     * @param int $status
     * @return String
     */
    public static final function displayStatus( $status ){
        switch( $status ){
            case self::STAGE_STATUS_INACTIVE:
                return AirBoxStringModel::__('No iniciado');
            case self::STAGE_STATUS_STARTED:
                return AirBoxStringModel::__('En curso');
            case self::STAGE_STATUS_COMPLETED:
                return AirBoxStringModel::__('Completado');
            case self::STAGE_STATUS_EXPIRED:
                return AirBoxStringModel::__('Expirado');
        }
    }
    /**
     * @return int Id de etapa
     */
    public final function getId(){
        return $this->_mData[self::FIELD_META_ID];
    }
    /**
     * @return int Estado de la etapa
     */
    public final function getStatus(){
        //return parent::getValue(self::FIELD_META_STATUS,self::STAGE_STATUS_INACTIVE);
        if( $this->getCompleted() ){
            return self::STAGE_STATUS_COMPLETED;
        }
        elseif( $this->getExpired() ){
            return self::STAGE_STATUS_EXPIRED;
        }
        elseif( $this->_mData[self::FIELD_META_UNITS] > 0 ){
            return self::STAGE_STATUS_STARTED;
        }
        
        return self::STAGE_STATUS_INACTIVE;
    }
    /**
     * @return int Objetivo de boxes a vender
     */
    public final function getGoal(){
        return $this->_mData[self::FIELD_META_GOAL];
    }
    /**
     * @todo POR DESARROLLAR MAS
     * @return int Retorna las unidades completadas del objetivo
     */
    public final function getUnits(){
        return $this->_mData[self::FIELD_META_UNITS];
    }
    /**
     * @return int Unidades disponibles
     */
    public final function getRemaining(){
        return $this->_mData[self::FIELD_META_GOAL] - $this->_mData[self::FIELD_META_UNITS] > 0 ?
                $this->_mData[self::FIELD_META_GOAL] - $this->_mData[self::FIELD_META_UNITS] : 0;
    }
    /**
     * @return String Fecha de completado
     */
    public final function getDateCreated(){
        return $this->_mData[self::FIELD_META_DATE_CREATED];
    }
    /**
     * @return String Fecha límite
     */
    public final function getDateLimit(){
        return $this->_mData[self::FIELD_META_DATE_LIMIT];
    }
    /**
     * @return String Fecha completado
     */
    public final function getDateCompleted(){
        return $this->_mData[self::FIELD_META_DATE_COMPLETED];
    }
    /**
     * @return bool Indica si ha expirado
     */
    public final function getExpired(){
        
        return ( is_null($this->getDateCompleted())
                && date('Y-m-d') > $this->getDateLimit() );
    }
    /**
     * @return boolean Indica si el objetivo ha sido completado
     */
    public final function getCompleted(){
        return $this->_mData[self::FIELD_META_GOAL] - $this->_mData[self::FIELD_META_UNITS] <= 0;
        /*return !is_null($this->_mData[self::FIELD_META_DATE_COMPLETED])
            && date('Y-m-d H:i:s') >= $this->_mData[self::FIELD_META_DATE_COMPLETED];*/
    }
}