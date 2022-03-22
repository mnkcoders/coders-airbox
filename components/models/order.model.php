<?php defined('ABSPATH') or die;
/**
 * Descriptor de una unidad o conjutno de boxes adquiridos en una misma transacción por parte
 * de un inversor, a fin de controlar la pertenencia de cada uno de los boxes
 */
class AirBoxOrderModel extends AirBoxDictionary implements AirBoxIModel{
    
    //id o número de pedido de box
    const FIELD_META_ID = 'id';
    //propietario del pack de boxes
    const FIELD_META_OWNER_ID = 'owner_id';
    //cantidad, por defecto debe ser 1 pero puede ser mas para describir un conjunto adquirido en una transacción
    const FIELD_META_AMOUNT = 'amount';
    //valor en € del pedido
    const FIELD_META_VALUE = 'value';
    //AirPoints Invertidos en este paquete
    const FIELD_META_AIRPOINTS = 'airpoints';
    //tipo de box, reservado, comprado, intercambiado-reinvertido, regalado ...
    const FIELD_META_TYPE = 'type';
    //modo de pago
    const FIELD_META_PAYMENT_METHOD = 'payment_method';
    //fecha de creación del registro de box
    const FIELD_META_DATE_CREATED = 'date_created';
    //fecha de actualización de estado
    const FIELD_META_DATE_UPDATED = 'date_updated';
    //tipo de pago
    const ORDER_PAYMODE_NONE = 0;
    const ORDER_PAYMODE_AIRPOINTS = 1;
    const ORDER_PAYMODE_CREDIT_CARD = 2;
    const ORDER_PAYMODE_BANK_TRANSFER = 3;
    const ORDER_PAYMODE_PAYPAL = 4;
    const ORDER_PAYMODE_TESTER = 9;
    
    //reservado, pero estado volatil
    const ORDER_TYPE_ALL = -1;
    const ORDER_TYPE_RESERVED = 0;
    const ORDER_TYPE_PURCHASED = 1;
    //canjeado por boxes cuando se canjea todo su valor por APs
    const ORDER_TYPE_EXCHANGED = 2;
    const ORDER_TYPE_REWARDED = 3;
    
    protected function __construct(array $dataSet = null) {
        
        //por defecto deben ser todas las propiedades estáticas
        //en el caso de compra, se deben generar automáticamente por bbbdd
        //si se da el caso que se necesite cambiar algo, debe modificarse de manera
        //explicita el atributo mode del campo requerido
        parent::add(self::FIELD_META_ID, parent::FIELD_TYPE_ID );
        
        parent::add(self::FIELD_META_OWNER_ID, parent::FIELD_TYPE_ID,
                array('mode'=>parent::FIELD_MODE_DISPLAY ) );
        parent::add(self::FIELD_META_AMOUNT, parent::FIELD_TYPE_NUMBER,
                array('mode'=>parent::FIELD_MODE_EDIT,
                    'value'=>1,
                    'minimum'=>1,
                    'label'=>AirBoxStringModel::__('Cantidad')) );
        parent::add(self::FIELD_META_VALUE, parent::FIELD_TYPE_CURRENCY,
                array('mode'=>parent::FIELD_MODE_EDIT,
                    'label'=>AirBoxStringModel::__('Valor')) );
        parent::add(self::FIELD_META_AIRPOINTS, parent::FIELD_TYPE_NUMBER,
                array('mode'=>parent::FIELD_MODE_EDIT,
                    'label'=>AirBoxStringModel::__('AirPoints')) );
        parent::add(self::FIELD_META_TYPE, parent::FIELD_TYPE_NUMBER,
                array(
                    'mode'=>parent::FIELD_MODE_DISPLAY,
                    'value'=>self::ORDER_TYPE_RESERVED,
                    'source'=>'box_type',
                    'label'=>AirBoxStringModel::__('Tipo')));
        parent::add(self::FIELD_META_PAYMENT_METHOD, parent::FIELD_TYPE_NUMBER,
                array(
                    'mode'=>parent::FIELD_MODE_DISPLAY,
                    'value'=>self::ORDER_PAYMODE_NONE,
                    'source'=>'payment_method',
                    'label'=>AirBoxStringModel::__('Modo de pago')));
        
        parent::add(self::FIELD_META_DATE_CREATED,parent::FIELD_TYPE_DATETIME,
                array('mode'=>parent::FIELD_MODE_DISPLAY,
                    'label'=>AirBoxStringModel::__('Fecha')) );
        parent::add(self::FIELD_META_DATE_UPDATED,parent::FIELD_TYPE_DATETIME,
                array('mode'=>parent::FIELD_MODE_DISPLAY,
                    'label'=>AirBoxStringModel::__('&Uacute;ltima modificaci&oacute;n')) );
        
        parent::__construct($dataSet);

    }
    /**
     * @return String Muestra el tipo de método de pago
     */
    public static final function displayPaymentMethod( $method ){
        switch( $method ){
            case self::ORDER_PAYMODE_AIRPOINTS:
                return AirBoxStringModel::__('Reinversi&oacute;n de AirPoints');
            case self::ORDER_PAYMODE_PAYPAL:
                return AirBoxStringModel::__('PayPal');
            case self::ORDER_PAYMODE_BANK_TRANSFER:
                return AirBoxStringModel::__('Transferencia Bancaria');
            case self::ORDER_PAYMODE_CREDIT_CARD:
                return AirBoxStringModel::__('Tarjeta de cr&eacute;dito');
            case self::ORDER_PAYMODE_NONE:
                return AirBoxStringModel::__('Ninguno');
            case self::ORDER_PAYMODE_TESTER:
                return AirBoxStringModel::__('Testing');
            default:
                return AirBoxStringModel::__('Sin definir');
        }
    }
    /**
     * @return String Muestra el tipo de PACK
     */
    public static final function displayType( $type ){
        switch( $type )
        {
            case self::ORDER_TYPE_RESERVED:
                return AirBoxStringModel::__('Reservado');
            case self::ORDER_TYPE_PURCHASED:
                return AirBoxStringModel::__('Comprado');
            case self::ORDER_TYPE_EXCHANGED:
                return AirBoxStringModel::__('Canjeado');
            case self::ORDER_TYPE_REWARDED:
                return AirBoxStringModel::__('Ganado');
            default:
                return AirBoxStringModel::__('Sin definir');
        }
    }
    /**
     * @return int Tipo de BoxPack
     */
    public final function getType(){
        return intval( $this->getValue(self::FIELD_META_TYPE,self::ORDER_TYPE_RESERVED) );
    }
    /**
     * @return int Valor del pedido
     */
    public final function getOrderValue(){
        return intval($this->getValue(self::FIELD_META_VALUE,0));
    }
    /**
     * @return int ID de pedido
     */
    public final function getId(){
        return intval( $this->getValue(self::FIELD_META_ID,0) );
    }
    /**
     * @return int Modo de pago
     */
    public final function getPaymentMethod(){
        return $this->getValue(self::FIELD_META_PAYMENT_METHOD,self::ORDER_PAYMODE_NONE);
    }
    /**
     * Establece el método de pago del pedido
     * @param int $method
     */
    public final function setPaymentMethod( $method = self::ORDER_PAYMODE_NONE ){
        if( $this->getType() == self::ORDER_TYPE_RESERVED ){
            /**
             * @todo Controlar aqui que no pasemos un método de pago incorrecto
             */
            $this->setValue(self::FIELD_META_PAYMENT_METHOD, $method );
        }
        return $this;
    }
    /**
     * @return int Cantidad de Boxes en el pack
     */
    public final function getAmount(){
        return $this->getValue(self::FIELD_META_AMOUNT,0);
    }
    /**
     * @return int retorna la cantidad de AP invertidos en la compra
     */
    public final function getAirPoints(){
        return $this->getValue(self::FIELD_META_AIRPOINTS,0);
    }
    /**
     * @return String Fecha de última actualización
     */
    public final function getDate(){
        return $this->getValue(self::FIELD_META_DATE_UPDATED,'');
    }
    /**
     * Guarda el estado del pedido en la BBDD
     * Si actualiza, procesa solo los valores marcados con el meta updated=>true
     * Si crea uno nuevo, asigna automáticamente el ID de pack al procesar la inserción
     * @return boolean TRUE si se ha registrado con éxito
     */
    public final function save(){
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        //fecha timestamp
        $date = date('Y-m-d H:i:s');
        
        if( ($bundle_id = $this->getValue(self::FIELD_META_ID,0) ) > 0 ){
            //actualizar pedido. no se actializa el id de propietario ni fecha creación
            
            $columns = $this->listUpdated();

            if( count( $columns) ){
                
                $updated = $dbi->update(
                    AirBoxDataBaseModel::DB_SOURCE_BOXES, $columns,
                    array(AirBoxOrderModel::FIELD_META_ID=>$bundle_id));
                
                return $updated;
            }
        }
        elseif( ($owner_id = $this->getValue(self::FIELD_META_OWNER_ID, 0) ) > 0 ){
            //guardar nuevo pedido (INSERT)
            $bundle_id = $dbi->create(
                    AirBoxDataBaseModel::DB_SOURCE_BOXES,
                    array(
                        //imprescindible tener un ID de propietario o no se registra el pedido
                        self::FIELD_META_OWNER_ID=>$owner_id,
                        self::FIELD_META_AMOUNT=>$this->getValue(self::FIELD_META_AMOUNT,0),
                        self::FIELD_META_AIRPOINTS=>$this->getValue(self::FIELD_META_AIRPOINTS,0),
                        self::FIELD_META_VALUE=>$this->getValue(self::FIELD_META_VALUE,0),
                        self::FIELD_META_TYPE=>$this->getValue(self::FIELD_META_TYPE,self::ORDER_TYPE_RESERVED),
                        self::FIELD_META_PAYMENT_METHOD=>$this->getValue(self::FIELD_META_PAYMENT_METHOD,self::ORDER_PAYMODE_NONE),
                        self::FIELD_META_DATE_UPDATED=>$date,
                        self::FIELD_META_DATE_CREATED=>$date,
                    ));

            //utilizar  set meta en lugar de setValue, a fin de evitar marcar como actualizado el ID
            $this->setMeta(self::FIELD_META_ID,'value', $bundle_id );
            
            return $bundle_id > 0;
        }
        
        return false;
    }
    /**
     * Activar el pedido aplicando los descuentos aplicados si procede
     * 
     * Este método no genera recompensa para el inversor padre. Utilizar en su lugar:
     * 
     * AirBoxInvestorModel::activateOrder( $order_id );
     * 
     * @return boolean
     */
    public final function activate( AirBoxInvestorModel $investor ){
        
        if( $investor->getId() != $this->getValue(AirBoxOrderModel::FIELD_META_OWNER_ID,0) ){
            return false;
        }
        
        //UNIDADES DISPONIBLES EN EL PROYECTO
        $available = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);

        if( $available > 0 && $this->getAmount() <= $available ){
            /**
             * Si el valor en € del pedido es superior a 0, el inversor ha pagado 
             * una cantidad parcial o total del mismo, y el pedido es marcado como
             * PURCHASED (comprado)
             * Si el pedido tiene valor 0, ha sido obtenido por alguna bonificación
             * como la reinversión de airpoints del inversor, por tanto es EXCHANGED
             */
            $this->setValue(
                    AirBoxOrderModel::FIELD_META_TYPE,
                    $this->getOrderValue() > 0 ?
                        AirBoxOrderModel::ORDER_TYPE_PURCHASED :
                        AirBoxOrderModel::ORDER_TYPE_EXCHANGED);

            /**
             * @todo Controlar aquí si es preciso la actualización del método de pago
             */
            if( $this->getPaymentMethod() == self::ORDER_PAYMODE_NONE ){
                $this->setPaymentMethod(self::ORDER_PAYMODE_AIRPOINTS);
            }

            //fecha de actualización
            $this->setValue(
                    AirBoxOrderModel::FIELD_META_DATE_UPDATED,
                    date('Y-m-d H:i:s'));

            if( $this->save() ){

                //generar transacción de compra
                if( AirBoxTransactionModel::CreatePurchase($investor, $this )){ }

                return true;
            }

        }
        else{
            //no es posible activar el pedido reservado si se agotan los boxes
            //si no hay boxes suficientes para la reserva, el inversor debe borrar el pedido y crear uno nuevo
            AirBoxNotifyModel::RegisterLog(
                    AirBoxStringModel::LBL_WARNING_OUT_OF_STOCK,
                    AirBoxNotifyModel::LOG_TYPE_WARNING);
        }                
        
        return false;
    }
    /**
     * Importa un pedido de boxes
     * @param int $order_id ID de pedido válido (mayor que 0)
     * @param int $owner_id ID de inversor válido (mayor que 0)
     * @return \AirBoxOrderModel
     */
    public static final function LoadOrderById( $order_id,$owner_id ){
        
        if( $order_id > 0 ){
            $db_order = self::LoadBoxes(array(
                self::FIELD_META_ID=>$order_id,
                self::FIELD_META_OWNER_ID=>$owner_id,
                ));

            return (!is_null( $db_order) && count($db_order)) ?
                new AirBoxOrderModel($db_order[0]) : null;
        }
        
        return null;
    }
    /**
     * Carga la lista de Boxes asignados al inversor (por definir si reservados, adquiridos y otra categoría)
     * @param AirBoxInvestorModel $investor
     * @param boolean $all_boxes Incluir todos los boxes incluidos los reservados (no en propiedad)
     * @return array Lista de Boxes adquiridos por el inversor
     */
    public static final function LoadInvestorBoxes( $from_level = self::ORDER_TYPE_RESERVED, AirBoxInvestorModel $investor = null ){
        
        return self::LoadBoxes(!is_null($investor) ?
                "box.type>={$from_level} AND box.owner_id={$investor->getId()}":
                "box.type>={$from_level}");
    }
    /**
     * Selecciona la lista exacta de boxes asignados por tipo al inversor.
     * @param int $type Nivel exclusivo
     * @param AirBoxInvestorModel $investor
     * @return array Lista de Boxes adquiridos por inversor
     */
    public static final function FilterInvestorBoxes( $type , AirBoxInvestorModel $investor ){
        return self::LoadBoxes(!is_null($investor) ?
                "box.type={$type} AND box.owner_id={$investor->getId()}":
                "box.type={$type}");
    }
    /**
     * @param type $filters
     * @return string
     */
    public static final function LoadBoxes( $filters = null, $limit = 0 ){
        
        $dbi = AirBoxDataBaseModel::getDatabase();

        $sql_boxes = sprintf( "SELECT box.owner_id AS %s,box.id AS %s,box.amount AS %s,"
                . "box.type AS %s,box.value AS %s,box.airpoints AS %s,box.payment_method AS %s,"
                . "box.date_created AS %s,box.date_updated AS %s,"
                . "CONCAT(inv.first_name,' ',inv.last_name) AS %s,"
                . "inv.document_id AS %s,inv.investor_key AS %s"
                . " FROM %s AS box INNER JOIN %s AS inv ON (box.owner_id=inv.user_id)",
                self::FIELD_META_OWNER_ID,self::FIELD_META_ID,self::FIELD_META_AMOUNT,
                self::FIELD_META_TYPE,self::FIELD_META_VALUE, self::FIELD_META_AIRPOINTS,
                self::FIELD_META_PAYMENT_METHOD, self::FIELD_META_DATE_CREATED,
                self::FIELD_META_DATE_UPDATED, AirBoxInvestorModel::FIELD_META_AFFILIATE,
                AirBoxInvestorModel::FIELD_META_DOCUMENT_ID,
                AirBoxInvestorModel::FIELD_META_INVESTOR_KEY,
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_BOXES),
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_INVESTORS));
        
        if( !is_null( $filters) ){ 
            
            if( is_array($filters) ){
                
                $filter_list = array();

                foreach ($filters as $column=>$value ){
                    switch($column){
                        case AirBoxInvestorModel::FIELD_META_PARENT_ID:
                            $filter_list[] = "inv.{$column}={$value}";
                            break;
                        default:
                            //preparar contenido del filtro en función del tipo de dato
                            if( is_array( $value ) ){
                                //para array preparar formato IN (valores)
                                $filter_list[] = sprintf("box.%s IN ('%s')",
                                        $column, implode("','", $value));
                            }
                            elseif(is_string($value)){
                                //para texto, formato = 'valor'
                                $filter_list[] = "box.{$column}='{$value}'";
                            }
                            else{
                                //por defecto formato = valor
                                $filter_list[] = "box.{$column}={$value}";
                            }
                            break;
                    }
                }

                $sql_boxes .= ' WHERE ' . implode(' AND ', $filter_list);
            }
            else{
                $sql_boxes .= ' WHERE '.$filters;
            }
            
        }
        
        $sql_boxes .= ' ORDER BY '.self::FIELD_META_ID;
        
        if( $limit ){
            $sql_boxes .= ' LIMIT '.$limit;
        }
        
        //die($sql_boxes);
        
        return $dbi->query( $sql_boxes);
    }
    /**
     * Realiza un recuento de los boxes en función del tipo
     * @param int $type
     * @param id $owned_id Id de inversor, 0 para ignorar y realizar selección global
     * @param bool $single_type Define si la selección de tipo des ñunica y exclusiva o si contará los niveles superiores
     * @return int
     */
    public static final function CountBoxes( $type = self::ORDER_TYPE_PURCHASED, $owner_id = 0, $single_type = true ){
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        $sql_boxes = sprintf('SELECT SUM(`amount`) AS boxes FROM %s',
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_BOXES));
        
        //definir si el filtro es exclusivo para el tipo de pedido o inclusivo con los niveles superiores
        $sql_boxes .= $single_type ?
                sprintf( ' WHERE `type`=%s', $type ) :
                sprintf( ' WHERE `type`>=%s', $type );
        
        if( $owner_id ){
            $sql_boxes .= ' AND `owner_id`='.$owner_id;
        }
        
        $r_boxes = $dbi->query( $sql_boxes );
        
        return !is_null($r_boxes) && count($r_boxes) ?
        intval( $r_boxes[0]['boxes'] ) : 0;
    }
    /**
     * Realiza un recuento de los boxes en función del tipo
     * @param int $type
     * @param id $owned_id Id de inversor, 0 para ignorar y realizar selección global
     * @param bool $single_type Define si la selección de tipo des ñunica y exclusiva o si contará los niveles superiores
     * @return int
     */
    public static final function CountOrders( $type = self::ORDER_TYPE_PURCHASED, $owner_id = 0, $single_type = true ){
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        $sql_orders = sprintf('SELECT COUNT(*) AS orders FROM %s',
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_BOXES));
        
        //definir si el filtro es exclusivo para el tipo de pedido o inclusivo con los niveles superiores
        $sql_orders .= $single_type ?
                sprintf( ' WHERE `type`=%s', $type ) :
                sprintf( ' WHERE `type`>=%s', $type );
        
        if( $owner_id ){
            $sql_orders .= ' AND `owner_id`='.$owner_id;
        }
        
        $r_orders = $dbi->query( $sql_orders );
        
        return !is_null($r_orders) && count($r_orders) ? $r_orders[0]['orders'] : 0;
    }
    /**
     * @param AirBoxInvestorModel $investor Inversor asociado, nulo para seleccionar todas las reservas
     * @return AirBoxOrderModel[] Lista los boxes por filtro
     */
    public static final function LoadReservedOrders( AirBoxInvestorModel $investor = null ){
        
        $filters = array(AirBoxOrderModel::FIELD_META_TYPE => AirBoxOrderModel::ORDER_TYPE_RESERVED);
        
        if( !is_null($investor)){
            $filters[AirBoxOrderModel::FIELD_META_OWNER_ID] = $investor->getId();
        }
        
        
        $order_data = self::LoadBoxes( $filters );

        $order_list = array();

        foreach( $order_data as $order ){
            $order_list[] = new AirBoxOrderModel($order);
        }
        
        return $order_list;
    }
    /**
     * Crea un nuevo pedido
     * 
     * @param AirBoxInvestorModel $investor
     * @param int $amount Cantidad
     * @param int $airpoints AirPoints consumidos para el pedido
     * @param int $type Tipo de pedido / estado
     * @param int $paymethod Método de pago
     * @return null|\AirBoxOrderModel Pedido
     */
    public static final function CreateOrder( AirBoxInvestorModel $investor, $amount, $airpoints = 0 ){
        
        if( $investor->getId() > 0 ){
            
            //valor de la compra /antes del descuento)
            $order_value = AirBox::getOption('coinbox_cost',1) * $amount;
            
            //controlar que no se pase el contador de airpoints
            $airpoints = ( $airpoints <= $investor->getAirPoints() ) ? 
                    $airpoints :
                    $investor->getAirPoints();
            
            /**
             * Aplicar el descuento computando los airpoints reinvertidos del inversor
             */
            if( $airpoints > 0 ){
                /**
                 * el descuento indica el valor total en € de los airpoints, según el plan de inversor.
                 * es la cantidad que deducirá el valor total del pedido hasta 0 si procede (reinversión completa)
                 */
                $discount = $investor->getBoxPointValue($airpoints);
                
                //Airpoints restantes del inversor (deducir airpoints consumidos)
                /*$remain = ($investor->getAirPoints() - $airpoints > 0) ? 
                        $investor->getAirPoints() - $airpoints : 0;*/

                //si el inversor tiene mas APs de los suficientes para llevarse la compra gratis
                // devolver los APs restantes de la diferencia
                if( $order_value - $discount < 0 ){

                    //obtener el valor del AP por tipo de plan (1 AP == n € )
                    $boxpoint_value = $investor->getBoxPointValue();

                    //aplicar reintegro de APs
                    $ap_refund = (int)(($discount - $order_value) / $boxpoint_value);

                    //en este caso el descuento es la cantidad entera de la compra, por tanto el inversor
                    //obtiene el pedido gratis (Reinvirtiendo sus APs)
                    $discount = $order_value;
                    
                    //y controlar que sea positivo
                    if( $ap_refund > 0 ){
                        //sumar los AP recuperados de la conversión
                        //esta variable no se está utilizando en este contexto, puede ser inecesaria
                        //$remain += $ap_refund;
                        //importante actualizar el numero de APs para realizar la compra!!!!
                        //el contador de APs continuaba teniendo mas APs de los admitidos
                        $airpoints = $airpoints - $ap_refund;
                    }
                }
                
                //el valor del pedido que ha sido pagado con AP debe ser deducido del valor original
                //a fin de justificar el descuento.
                //cuando el valor del descuento es el total del pedido, este queda en 0
                $order_value = $order_value - $discount;
            }

            //crea un nuevo pack de boxes. por defecto se crean como reservados sin modo de pago
            $order = new AirBoxOrderModel(array(
                self::FIELD_META_OWNER_ID => $investor->getId(),
                self::FIELD_META_AMOUNT => $amount,
                self::FIELD_META_VALUE => $order_value,
                self::FIELD_META_AIRPOINTS => $airpoints,
                //asigna el método de pago siempre a nulo, salvo que sea una reinversión completa de airpoints
                self::FIELD_META_PAYMENT_METHOD => ( $order_value == 0 ) ?
                    self::ORDER_PAYMODE_AIRPOINTS :
                    self::ORDER_PAYMODE_NONE,
                //asignar por defecto reservados, pero permitir regalar también
                self::FIELD_META_TYPE => self::ORDER_TYPE_RESERVED,
            ));
            
            /**
             * Aplicar los cambios una vez el pedido se ha guardado
             * hacer las gestiones de descuento de airpoints si procede
             */
            if( $order->save() ){
                
                if( $airpoints > 0 ){
                    //actualizar el valor de APs con los restantes
                    //$this->setValue(self::FIELD_META_AIRPOINTS, $ap_left);
                    $investor->removeAirpoints($airpoints);

                    //notificar operación
                    AirBoxNotifyModel::RegisterLog(sprintf(
                            AirBoxStringModel::LBL_INFO_AIRPOINT_DISCOUNT,
                            $airpoints),
                            AirBoxNotifyModel::LOG_TYPE_ADVICE);
                }
                //crea automáticamente una transacción en función del tipo de box
                switch( $order->getType() ){
                    case self::ORDER_TYPE_EXCHANGED:
                    case self::ORDER_TYPE_PURCHASED:
                        //compra efectiva de pack AirBox
                        /* De momento no se comprará directamente, pero si se acaba desarrollando una pasarela
                         * de pago, puede realizarse la compra diirectamente al haber abonado la cantidad
                         * indicada
                         */
                        AirBoxTransactionModel::CreatePurchase($investor,$order);
                        break;
                    case self::ORDER_TYPE_REWARDED:
                        //obsequio por atraer 10n inversores
                        AirBoxTransactionModel::CreateBoxReward($investor,$order);
                        break;
                    case self::ORDER_TYPE_RESERVED:
                        //Reserva de pack AirBox (precompra)
                        AirBoxTransactionModel::CreateReservation($investor,$order);
                    default:
                        break;
                }
    
                return $order;
            }
        }
        
        return null;
    }
    /**
     * Vacía la lista de boxes reservados si su plazo de adquisición ha expirado.
     * 
     * Asegurarse de que la orden eliminada es del tipo RESERVADA (0)
     * 
     * @return int Recuento de Boxes liberados en la operación
     */
    public static final function RemoveOrder(AirBoxInvestorModel $investor, $order_id ){
        
        $db_order = self::LoadBoxes(array(
            self::FIELD_META_ID=>$order_id,
            self::FIELD_META_OWNER_ID=>$investor->getId()));
        
        if( !is_null($db_order) && count($db_order) ){
            
            $order_data = $db_order[0];
            
            if( $order_data[self::FIELD_META_TYPE] < self::ORDER_TYPE_PURCHASED ){

                $airpoints = intval($order_data[self::FIELD_META_AIRPOINTS]);

                //recuperar los AirPoints si procede
                if( $airpoints ) {
                    
                    $investor->giveAirPoints($airpoints);
                    
                    //notificar recuperación de airpoints
                    AirBoxNotifyModel::RegisterLog(sprintf(
                            AirBoxStringModel::LBL_INFO_AIRPOINT_RECOVER, $airpoints),
                            AirBoxNotifyModel::LOG_TYPE_ADVICE);
                }
                
                $dbi = AirBoxDataBaseModel::getDatabase();

                $result = $dbi->delete(AirBoxDataBaseModel::DB_SOURCE_BOXES, array(
                    AirBoxOrderModel::FIELD_META_ID => $order_id,
                    AirBoxOrderModel::FIELD_META_OWNER_ID => $investor->getId(),
                    AirBoxOrderModel::FIELD_META_TYPE => self::ORDER_TYPE_RESERVED,
                ));
                
                return $result;
            }
            
        }
        
        return false;
    }
}

