<?php defined('ABSPATH') or die;
/**
 * Vista del administrador
 */
class AirBoxManagerView extends AirBoxRenderer{
    
    const VIEW_LAYOUT_DASHBOARD = 'dashboard';
    const VIEW_LAYOUT_MILESTONES = 'milestones';
    const VIEW_LAYOUT_BOXES = 'boxes';
    const VIEW_LAYOUT_INVESTORS = 'investors';
    const VIEW_LAYOUT_INVESTOR_PROFILE = 'profile';
    const VIEW_LAYOUT_TRANSACTIONS = 'transactions';
    const VIEW_LAYOUT_SETTINGS = 'settings';
    const VIEW_LAYOUT_FRONTEND = 'frontend';
    
    //const VIEW_LAYOUT_NOTIFIER = 'notifier';
    
    private $_investor_plan = array();
    private $_investor_status = array();
    
    private $_transaction_type = array();
    private $_transaction_coin = array();
    
    private $_box_type = array();
    private $_payment_method = array();
    
    protected function __construct() {
        
        parent::__construct();
        
        //inicializar y cargar parámetros requeridos
        foreach( AirBoxDictionary::listParameters('inv_plan') as $planId=>$label ){
            $this->_investor_plan[$planId] = $label;
        }
        foreach( AirBoxDictionary::listParameters('inv_status') as $statusId=>$label ){
            $this->_investor_status[$statusId] = $label;
        }
        foreach( AirBoxDictionary::listParameters('trans_type') as $statusId=>$label ){
            $this->_transaction_type[$statusId] = $label;
        }
        foreach( AirBoxDictionary::listParameters('trans_coin') as $statusId=>$label ){
            $this->_transaction_coin[$statusId] = $label;
        }
        foreach( AirBoxDictionary::listParameters('box_type') as $statusId=>$label ){
            $this->_box_type[$statusId] = $label;
        }
        foreach( AirBoxDictionary::listParameters('payment_method') as $statusId=>$label ){
            $this->_payment_method[$statusId] = $label;
        }
    }
    /**
     * @param string $layout
     */
    public final function Render( $layout ) {
        
        parent::Render($layout);
    }
    /**
     * Recuento de unidades pendientes de asignar a la lista de objetivos
     * @return int Unidades sin asignar
     */
    public final function get_unassigned_units_data(){
        
        $total_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);
        
        $owned = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED,0);
        
        return $total_units - $owned > 0 ?
                $total_units - $owned : 0;
    }
    /**
     * Retorna la lista de transacciones
     * @return array
     */
    public final function get_transactions_data( ){
        
        $transactions = AirBoxTransactionModel::listTransactions();
        
        return !is_null($transactions) ? $transactions : array();
    }
    /**
     * Retorna la lista de transacciones del inversor 
     * @return array
     */
    public final function get_profile_transactions_data(){
        
        return( !is_null( $investor = $this->getModel()) ) ?
            $investor->getTransactions() : array();
    }
    /**
     * @return array Retorna la lista de reintegros pendientes de completar
     */
    public final function get_pending_refunds_data(){
        //recuperar una lista de las transacciones que sean reintegros no completados
        //(marcadas con moneda AirPoints
        return AirBoxTransactionModel::listTransactions(array(
            AirBoxTransactionModel::FIELD_META_TYPE => AirBoxTransactionModel::TRANSACTION_TYPE_REFUND,
            AirBoxTransactionModel::FIELD_META_COIN => AirBoxTransactionModel::TRANSACTION_COIN_AIRPOINTS
        ));
    }
    /**
     * Plan del inversor
     * @param int $plan_id
     * @return string
     */
    protected final function get_plan_data( $plan_id ){
        return isset($this->_investor_plan[$plan_id]) ?
            AirBoxStringModel::__($this->_investor_plan[$plan_id]) :
            AirBoxStringModel::__('Indefinido');
    }
    /**
     * Estado del inversor
     * @param int $status_id
     * @return string
     */
    protected final function get_status_data( $status_id ){
        return isset($this->_investor_status[$status_id]) ?
            AirBoxStringModel::__($this->_investor_status[$status_id]) :
            AirBoxStringModel::__('Indefinido');
    }
    /**
     * Tipo de transacción
     * @param int $transaction_id
     * @return String
     */
    protected final function get_transaction_type_data( $transaction_id ){
        return isset($this->_transaction_type[$transaction_id]) ?
            AirBoxStringModel::__($this->_transaction_type[$transaction_id]) :
            AirBoxStringModel::__('Indefinido');
    }
    /**
     * Tipo de moneda
     * @param int $transaction_id
     * @return String
     */
    protected final function get_coin_data( $coin_id ){
        return isset($this->_transaction_coin[$coin_id]) ?
            AirBoxStringModel::__($this->_transaction_coin[$coin_id]) :
            '--';
    }
    /**
     * Tipo de Box
     * @param int $box_type
     * @return String
     */
    protected final function get_box_type_data( $box_type ){
        return isset($this->_box_type[$box_type]) ?
            AirBoxStringModel::__($this->_box_type[$box_type]) :
            AirBoxStringModel::__('Ninguno');
    }
    /**
     * Método de pago
     * @param int $method_id
     * @return String
     */
    protected final function get_payment_method_data( $method_id ){
        return isset($this->_payment_method[$method_id]) ?
            AirBoxStringModel::__($this->_payment_method[$method_id]) :
            AirBoxStringModel::__('Ninguno');
    }
    /**
     * Retorna la lista de parámetros del tipo especificado
     * 
     * @todo Mover las referencias a la BD a un modelo, no es optimo que esté todo aqui
     * 
     * @param string $param
     * @return array
     */
    public final function get_param_data( ){
        $dbi = AirBoxDataBaseModel::getDatabase();

        return $dbi->get(AirBoxDataBaseModel::DB_SOURCE_PARAMETERS);
    }
    /**
     * @return array Lista de Pedidos en estado reservado
     */
    public final function get_pending_orders_data( ){
        
        return AirBoxOrderModel::LoadBoxes(array(
            AirBoxOrderModel::FIELD_META_TYPE => AirBoxOrderModel::ORDER_TYPE_RESERVED
        ));
    }
    /**
     * @todo Mover las referencias a la BD a un modelo, no es optimo que esté todo aqui
     * 
     * @return array Lista de Inversores entrantes
     */
    public final function get_incoming_data( ){
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        return $dbi->get(
                AirBoxDataBaseModel::DB_SOURCE_INVESTORS,
                array(
                    AirBoxInvestorModel::FIELD_META_ID,
                    AirBoxInvestorModel::FIELD_META_DATE_CREATED,
                    AirBoxInvestorModel::FIELD_META_PARENT_ID,
                    AirBoxInvestorModel::FIELD_META_FIRST_NAME,
                    AirBoxInvestorModel::FIELD_META_LAST_NAME,
                    AirBoxInvestorModel::FIELD_META_DOCUMENT_ID),
                array(
                    AirBoxInvestorModel::FIELD_META_STATUS=>AirBoxInvestorModel::INVESTOR_STATUS_PENDING
                ));
    }
    /**
     * @todo Mover las referencias a la BD a un modelo, no es optimo que esté todo aqui
     * 
     * @param int $investor_id Id de inversor para filtrar la selección o NULL si se deben mostrar todos
     * @return array Lista de boxes a mostrar por pantalla
     */
    protected final function get_boxes_data( ){
        
        if( !is_null($this->getModel())){
            return AirBoxOrderModel::LoadInvestorBoxes( AirBoxOrderModel::ORDER_TYPE_RESERVED, $this->getModel());
        }
        elseif( !is_null( $type = $this->get(AirBoxOrderModel::FIELD_META_TYPE)) ){
            return AirBoxOrderModel::LoadBoxes(array(
                AirBoxOrderModel::FIELD_META_TYPE => $type,
            ));
        }
        else{
            return AirBoxOrderModel::LoadBoxes();
        }
        
        $sql_boxes = "SELECT box.owner_id AS investor_id,box.id AS box_id,box.amount AS amount,"
                . "box.type AS type,box.payment_method AS payment_method,box.date_created AS date,"
                . "CONCAT(inv.first_name,' ',inv.last_name) AS affiliate,inv.document_id AS document_id,"
                . "inv.investor_key AS investor_key"
                . " FROM %s AS box INNER JOIN %s AS inv ON (box.owner_id=inv.user_id)";
        
        if( !is_null($investor_id) ){
            $sql_boxes .= " WHERE inv.user_id=".$investor_id;
        }
        
        $sql_boxes .= " ORDER BY date";
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        $result = $dbi->query( sprintf( $sql_boxes,
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_BOXES),
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_INVESTORS)) );
        
        return !is_null( $result ) ? $result : array();
    }
    /**
     * @return AirBoxInvestorModel Inversor a mostrar
     */
    protected final function get_investor_data(){
        return $this->getModel();
    }
    /**
     * @return AirBoxInvestorModel |NULL Inversor padre o nulo si no está definido
     */
    protected final function get_parent_data(){
        return !is_null( $this->getModel()) ?
            $this->getModel()->getParent() :
            null;
    }
    /**
     * @return array Lista de inversores
     */
    public final function get_investors_data(){
        return AirBoxInvestorModel::listInvestors();
    }
    /**
     * 2016-04-20 - Agregada función de listado de inversores disponibles para posible asignación
     * - no son el inversor de la vista actual
     * - no son nuevos inversores, solo activados
     * - no son inversores hijos del inversor de la vista actual
     * 
     * @return array Lista de inversores ACTIVOS asociados por ID
     */
    public final function get_non_child_investors_data(){
        
        $list = array();
        
        $investor = $this->getModel();
        //evitar que se seleccione a sí mismo ...
        $investor_id = $investor->getId();
        
        foreach( $this->get_investors_data() as $affiliate ){
            //filtrar la lista de inversores excluyendose a sí mismo y 
            //aquellos que sean hijos directos del inversor actual
            //la lista carga por defecto SOLO los inversores activos
            if( $affiliate[AirBoxInvestorModel::FIELD_META_PARENT_ID] != $investor_id  &&
                    $affiliate[AirBoxInvestorModel::FIELD_META_ID] != $investor_id &&
                    $affiliate[AirBoxInvestorModel::FIELD_META_STATUS] == AirBoxInvestorModel::INVESTOR_STATUS_ACTIVE){
                
                $list[$affiliate[AirBoxInvestorModel::FIELD_META_ID]] = sprintf(
                        '%s %s - [ %s ]',
                        $affiliate[AirBoxInvestorModel::FIELD_META_FIRST_NAME],
                        $affiliate[AirBoxInvestorModel::FIELD_META_LAST_NAME],
                        $affiliate[AirBoxInvestorModel::FIELD_META_USER_NAME]);
            }
        }
        
        return $list;
    }
    /**
     * @return array Lista de monedas aceptadas (de momento una adición sin utilizar)
     */
    protected final function get_coin_list_data(){
        return array(
            '€'=>'EURO',
            '$'=>'USD',
            '£'=>'POUND');
    }
    /**
     * @return Array genera la lista de páginas disponibles en WP
     */
    protected final function get_wp_pages_data(){
        
        $page_list = array();
        
        foreach( get_pages(array('sort_column'=>'ID','sort_order'=>'ASC')) as $page ){
            $page_list[$page->ID] = $page->post_title;
        }
        
        return $page_list;
    }
    /**
     * @return Array Genera la lista de categorías disponible en el blog
     */
    protected final function get_wp_categories_data(){
        
        $cat_list = array();
        
        foreach( get_categories() as $category ){
            $cat_list[$category->term_id] = $category->name;
        }
        
        return $cat_list;
    }
    /**
     * 
     * @return lista de roles dispobibles a asociar al inversor
     */
    protected final function get_wp_roles_data(){
        return array(
            'subscriber'=>__('Suscriptor'),
            'contributor'=>__('Colaborador'),
            'author'=>__('Autor'),
            'editor'=>__('Editor'),
        );
    }
    /**
     * @return array Retorna las opciones ACTIVAR/DESACTIVAR para facilitar la integración en la vista
     */
    protected final function get_option_togle_data(){
        return array(
            AirBox::PLUGIN_OPTION_ENABLED=>AirBoxStringModel::__('Si'),
            AirBox::PLUGIN_OPTION_DISABLED=>AirBoxStringModel::__('No'));
    }
    /**
     * Importa las lineas del fichero log
     * @return array
     */
    protected final function get_log_file_data(){
        return AirBoxNotifyModel::ImportLogFile();
    }
    /**
     * Tipos de notificación del gestor de logs y notificaciones
     * @return array
     */
    protected final function get_notification_types_data(){
        return array(
            AirBoxNotifyModel::LOG_TYPE_ALL => AirBoxStringModel::__('Todos'),
            AirBoxNotifyModel::LOG_TYPE_INFORMATION => AirBoxStringModel::__('Informaci&oacute;n general'),
            AirBoxNotifyModel::LOG_TYPE_ADVICE => AirBoxStringModel::__('Avisos'),
            AirBoxNotifyModel::LOG_TYPE_WARNING => AirBoxStringModel::__('Advertencias'),
            AirBoxNotifyModel::LOG_TYPE_ERROR => AirBoxStringModel::__('Errores'),
            AirBoxNotifyModel::LOG_TYPE_RUNTIME_ERROR => AirBoxStringModel::__('Excepciones'),
            AirBoxNotifyModel::LOG_TYPE_DEBUG => AirBoxStringModel::__('Depuraci&oacute;n'),
        );
    }
}