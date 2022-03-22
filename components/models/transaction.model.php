<?php defined('ABSPATH') or die;
/**
 * Gestor de transacciones de la aplicación
 * 
 * Algunos tipos de transacción generan otras transacciones implicitas a modo de conversión u actualización
 * de estado del contador de créditos.
 * 
 * 1.- Un nuevo inversor compra N boxes
 * 
 *      Genera un movimiento de adquisición de N Boxes (tipo moneda €)
 * 
 *      Al ser nuevo se le recompensan con X airpoints (tipo moneda AirPoint)
 * 
 * 2.- Un inversor consigue que otro invierta a través de su formulario
 * 
 *      Obtiene una recompensa de X aircoins (tipo moneda AirCoin)
 * 
 *      Si es el 10n inversor que entra por su form, adquiere un AirBox extra (siempre que queden)
 * 
 *      Genera un movimiento de adquisición de N Boxes (sin tipo moneda, concepto recompensa)
 * 
 * 3.- Un inversor reintegra sus AirPoints en euros para extraer el valor
 * 
 *      Genera movimiento de X Euros recuperados y concepto de reintegro aircoins (tipo moneda Real)
 * 
 *      ------ Debe permitir reintegro parcial????!!??!?!?
 * 
 * 4.- Compra un nuevo set de AirBoxes (desde 1 hasta N) con dinero real
 * 
 *      Genera un movimiento de adquisición de N Boxes (tipo moneda €)
 * 
 *      !?!?!?Genera un movimiento de ganancia de X aircoins extra ?????? (tipo moneda AirCoin)
 * 
 * 5.- Compra un nuevo set de Airboxes (desde 1 hasta N) con Airpoints disponibles, si tiene suficientes
 * 
 *      Genera un movimiento de adquisición de N Boxes (tipo moneda AirCoin)
 * 
 */
class AirBoxTransactionModel implements AirBoxIModel{
    
    const MESSAGE_TRANSACTION_REINVEST = 'Reinversi&oacute;n en pack AirBox';
    const MESSAGE_TRANSACTION_REFUND = 'Reintegro de AirPoints';
    const MESSAGE_TRANSACTION_PURCHASE = 'Compra pack AirBox';
    const MESSAGE_TRANSACTION_REWARD = 'Recompensa pack AirBox';
    const MESSAGE_TRANSACTION_RESERVED = 'Reserva pack AirBox';
    const MESSAGE_TRANSACTION_AIRPOINTS_REWARD = 'Ganancia por atraer a un inversor';
    const MESSAGE_TRANSACTION_AIRPOINTS_NEWCOMER = 'Tus nuevos %s airpoints';
    /**
     * @var String Requiere del uso de un número de APs para indicar el descuento aplicado
     */
    const MESSAGE_TRANSACTION_DISCOUNT = ' ( aplicado descuento por reinversi&oacute;n de %s AirPoints )';

    const FIELD_META_ID = 'id';
    const FIELD_META_ACCOUNT_ID = 'account_id';
    const FIELD_META_TYPE = 'type';
    const FIELD_META_COIN = 'coin';
    const FIELD_META_AMOUNT = 'amount';
    const FIELD_META_VALUE = 'value';
    const FIELD_META_DETAILS = 'details';
    const FIELD_META_DATE_CREATED = 'date_created';
    
    const TRANSACTION_COIN_NONE = 0;        //transacciones que no mueven cantidad
    const TRANSACTION_COIN_AIRPOINTS = 1;   //transacción con airpoints
    const TRANSACTION_COIN_REAL = 2;        //transacción con moenda real
    const TRANSACTION_COIN_BOX = 3;         //transacción con BOX (para definir recompensa de 10n inversores)
    
    const TRANSACTION_TYPE_UNDEFINED = 0;
    //obtención de airpoints por parte del sistema si:
    //  se registra como nuevo inversor (ha pagado un set de coinbox)
    //  consigue atraer a otros inversores (se registran desde su form de inversor)
    const TRANSACTION_TYPE_REWARD = 1;
    //conversión de airpoints en euros y extracción, airpoints a 0 y cantidad mostrada en euros
    const TRANSACTION_TYPE_REFUND = 2;
    //Reserva de coinboxes, proceso intermedio entre el registro y la compra efectiva de acciones
    const TRANSACTION_TYPE_RESERVE = 3;
    //compra de coinboxes:
    //  si el tipo de moneda es real (euro) generar movimiento de obtención de N aircoins
    //  si el tipo de moneda es aircoin, adquirir box (siempre que tenga suficientes)
    const TRANSACTION_TYPE_PURCHASE = 4;
    
    const TRANSACTION_STATUS_NEW = 0;
    const TRANSACTION_STATUS_SAVED = 1;
    const TRANSACTION_STATUS_LOADED = 2;
    
    private $_transaction_id = 0;
    /**
     * @var int Id  de la cuenta de propietario o Inversor (Usuario WP)
     */
    private $_account_id = 0;
    
    //no queda claro que llegue a ser necesario, de momento no complicar el tema
    //private $_target_id = 0;
    /**
     * @var int Cantidad
     */
    private $_amount = 0;
    /**
     * @var float Valor de la transacción en €
     */
    private $_value = 0;
    /**
     * @var int Tipo de transacción
     */
    private $_type;
    /**
     * @var int Moneda (Real o AirPoints)
     */
    private $_coin = self::TRANSACTION_COIN_AIRPOINTS;
    /**
     * @var String Concepto
     */
    private $_details = null;
    /**
     * @var int Describe el estado de la transacción. 0 es nueva, 1 creada, 2 cargada desde la bbdd
     */
    private $_status = self::TRANSACTION_STATUS_NEW;
    /**
     * @var string Fecha-hora de creación
     */
    private $_date;
    
    private function __construct( $account_id, $amount, $details, $type = self::TRANSACTION_TYPE_UNDEFINED, $coin = self::TRANSACTION_COIN_NONE ) {
        $this->_account_id = $account_id;
        $this->_amount = $amount;
        $this->_details = $details;
        $this->_coin = $coin;
        $this->_type = $type;
        $this->_date = date('Y-m-d H:i:s');
    }
    /**
     * Crea una nueva transacción
     * @param int $account_id ID del inversor
     * @param int $amount Cantidad a reflejar en la transacción
     * @param string $details Detalle o concepto de la transacción
     * @param int $type Tipo de transacción
     * @param int $coin Moneda de transacción (ficticia o AirPoints | Real o Euros)
     * @return \AirBoxTransactionModel| null
     */
    public static final function CreateTransaction( $account_id, $amount, $details, $type = self::TRANSACTION_TYPE_REWARD, $coin = self::TRANSACTION_COIN_AIRPOINTS ){
        
        return new AirBoxTransactionModel( $account_id,$amount,$details,$type,$coin );
    }
    /**
     * Genera un movimiento de SOLICITUD de reintegro de AirPoints
     * Esta solicitud deberá ser confirmada posteriormente por el admin
     * a fin de actualizar el movimiento de reintegro y efectuar el cambio sobre
     * el inversor
     * @param AirBoxInvestorModel $investor
     * @param int $amount
     * @return bool
     */
    public static final function CreateRefund( AirBoxInvestorModel $investor, $amount ){
        
        $transaction = new AirBoxTransactionModel(
                $investor->getId(), $amount,
                sprintf(__('Reintegro de %s AirPoints'), $amount));
        
        $transaction->_type = self::TRANSACTION_TYPE_REFUND;
        
        $transaction->_coin = self::TRANSACTION_COIN_AIRPOINTS;
        
        $transaction->_value = $investor->getAirPointValue($amount);
        
        return $transaction->save();
    }
    /**
     * Genera una transacción de recompensa de n AirPoints para el inversor
     * @param AirBoxInvestorModel $investor
     * @param int $amount
     * @return bool
     */
    public static final function CreateAirPointReward(AirBoxInvestorModel $investor, $airpoints , $amount ){

        $transaction = new AirBoxTransactionModel(
                $investor->getId(), $airpoints,
                sprintf(AirBoxStringModel::__(
                        'Has ganado %s AirPoints por la venta de %s BOXES!'),
                        $airpoints,$amount));
        
        $transaction->_coin = self::TRANSACTION_COIN_AIRPOINTS;
        $transaction->_type = self::TRANSACTION_TYPE_REWARD;
        //$transaction->_value = 0
        
        return $transaction->save();
    }
    /**
     * Crea una transacción de recompensa de n BOXES para el inversor
     * @param AirBoxInvestorModel $investor
     * @param AirBoxOrderModel $order
     * @return bool
     */
    public static final function CreateBoxReward(AirBoxInvestorModel $investor, AirBoxOrderModel $order ){
        
        $transaction = new AirBoxTransactionModel(
                $investor->getId(), $order->getAmount(),
                sprintf(AirBoxStringModel::__(
                        '%s BOXes adquiridos por atraer a varios inversores'),
                        $order->getAmount()));
        
        $transaction->_coin = self::TRANSACTION_COIN_BOX;
        $transaction->_type = self::TRANSACTION_TYPE_REWARD;
        //$transaction->_value = 0
        
        return $transaction->save();
    }
    /**
     * Crea una reserva de pak AirBox para pagar
     * 
     * Las reservas solo admiten pago con moneda real
     * 
     * @param AirBoxInvestorModel $investor
     * @param AirBoxOrderModel $order
     * @return bool
     */
    public static final function CreateReservation( AirBoxInvestorModel $investor, AirBoxOrderModel $order , $aircoins = 0 ){
        
        $transaction = new AirBoxTransactionModel(
                $investor->getId(), $order->getAmount(),
                AirBoxStringModel::__compose(
                        'Reserva de %s BOXes',
                        $order->getAmount()));
        
        $transaction->_type = self::TRANSACTION_TYPE_RESERVE;
        
        $transaction->_coin = self::TRANSACTION_COIN_BOX;
        
        $value = $order->getAmount() * AirBox::getOption('coinbox_cost');
        
        //valor en euros. Si se reinvierte con AP, descontar APs del inversor (conversión implicita BoxPoints)
        if( $aircoins > 0 ){
            $transaction->_value = $value - $investor->getBoxPointValue( $aircoins );
            $transaction->_details .= AirBoxStringModel::__compose(
                    self::MESSAGE_TRANSACTION_DISCOUNT,
                    $aircoins);
        }
        else{
            $transaction->_value = $value;
        }
        
        return $transaction->save();
    }
    /**
     * Genera un movimiento de compra efectiva de un pack AirBox
     * 
     * Permite indicar el tipo de moneda, siendo esta € o AP
     * 
     * @param AirBoxInvestorModel $investor
     * @param AirBoxOrderModel $order
     * @return bool
     */
    public static final function CreatePurchase( AirBoxInvestorModel $investor, AirBoxOrderModel $order ){

        $transaction = new AirBoxTransactionModel(
                $investor->getId(), $order->getAmount(),
                AirBoxStringModel::__compose(
                    'Compra y activaci&oacute;n de %s BOXes',
                    $order->getAmount()));
        
        $transaction->_type = self::TRANSACTION_TYPE_PURCHASE;
        
        $transaction->_coin = self::TRANSACTION_COIN_BOX;
        
        $value = $order->getAmount() * AirBox::getOption('coinbox_cost');
        
        //valor en euros. Si se reinvierte con AP, descontar APs del inversor (conversión implicita BoxPoints)
        if( $order->getAirPoints() > 0 ){
            $transaction->_value = $value - $investor->getBoxPointValue( $order->getAirPoints() );
            $transaction->_details .= AirBoxStringModel::__compose(
                    self::MESSAGE_TRANSACTION_DISCOUNT,
                    $order->getAirPoints());
        }
        else{
            $transaction->_value = $value;
        }
        
        return $transaction->save();
    }
    /**
     * Obtiene el detalle de una transacción por medio de su id
     * @param int $transaction_id
     * @return AirBoxTransactionModel Transacción cargada o NULL si hubo error
     */
    public static final function LoadTransaction( $transaction_id ){
        
        if( !is_null( $dbi = AirBoxDataBaseModel::getDatabase() ) ){
            
            $db_transaction = $dbi->get(
                    AirBoxDataBaseModel::DB_SOURCE_TRANSACTIONS,
                    null,
                    array(self::FIELD_META_ID=>$transaction_id), 1);

            if( !is_null( $db_transaction ) && count($db_transaction) ){
                
                $transaction_data = $db_transaction[0];

                $transaction = new AirBoxTransactionModel(
                        $transaction_data[self::FIELD_META_ACCOUNT_ID],
                        $transaction_data[self::FIELD_META_AMOUNT],
                        $transaction_data[self::FIELD_META_DETAILS]);
                
                $transaction->_type = $transaction_data[self::FIELD_META_TYPE];
                $transaction->_coin = $transaction_data[self::FIELD_META_COIN];
                $transaction->_value = $transaction_data[self::FIELD_META_VALUE];

                $transaction->_transaction_id = $transaction_data[self::FIELD_META_ID];
                $transaction->_date = $transaction_data[self::FIELD_META_DATE_CREATED];
                $transaction->_status = self::TRANSACTION_STATUS_LOADED;

                return $transaction;
            }
        }

        return null;
    }
    /**
     * Lista un conjunto de registros de transacciones
     * @param array $filters
     * @return array Lista de transacciones
     */
    public static final function listTransactions( array $filters = null ){
        
        $dbi = AirBoxDataBaseModel::getDatabase();
        
        $sql_select = sprintf(
                "SELECT trs.id AS %s,trs.account_id AS %s,"
                . "trs.type AS %s,trs.coin AS %s,trs.amount AS %s,"
                . "trs.value AS %s,trs.details AS %s,trs.date_created AS %s,"
                . "CONCAT(inv.first_name,' ',inv.last_name) AS %s",
                AirBoxTransactionModel::FIELD_META_ID,
                AirBoxTransactionModel::FIELD_META_ACCOUNT_ID,
                AirBoxTransactionModel::FIELD_META_TYPE,
                AirBoxTransactionModel::FIELD_META_COIN,
                AirBoxTransactionModel::FIELD_META_AMOUNT,
                AirBoxTransactionModel::FIELD_META_VALUE,
                AirBoxTransactionModel::FIELD_META_DETAILS,
                AirBoxTransactionModel::FIELD_META_DATE_CREATED,
                AirBoxInvestorModel::FIELD_META_AFFILIATE );
        
        $sql_from = sprintf(
                "FROM %s AS trs INNER JOIN %s AS inv ON (trs.account_id=inv.user_id)",
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_TRANSACTIONS),
                $dbi->getTable(AirBoxDataBaseModel::DB_SOURCE_INVESTORS));
        
        $sql_transactions = $sql_select. ' '. $sql_from;
        
        if( !is_null($filters) ){
            
            $where = array();
            
            foreach( $filters as $column=>$value ){
                
                if( is_array($value) ){
                    $val = implode(',',$value);
                }
                elseif(is_string($value)){
                    $val = "'{$value}'";
                }
                else{
                    $val = $value;
                }

                switch($column){
                    /**
                     * @todo Incluir aquí casos para campos importados desde la tabla de inversores
                     * y boxes
                     */
                    default:
                        $where[] = sprintf("trs.%s=%s", $column, $val );
                        break;
                }
            }
            $sql_transactions .= sprintf(' WHERE %s',implode(' AND ',$where));
        }
        
        $sql_transactions .= sprintf(" ORDER BY %s",self::FIELD_META_DATE_CREATED);

        return $dbi->query($sql_transactions);
    }
    /**
     * Guarda el estado de una nueva transacción
     * Solo permite nuevas transacciones
     * @return boolean
     */
    public final function save(){

        if( $this->_status === self::TRANSACTION_STATUS_NEW ){
            /**
             * Registrar transacción en la bbdd
             */
            $dbi = AirBoxDataBaseModel::getDatabase();
            
            $this->_transaction_id = $dbi->create(
                    AirBoxDataBaseModel::DB_SOURCE_TRANSACTIONS,
                    array(
                        self::FIELD_META_ACCOUNT_ID=>$this->_account_id,
                        self::FIELD_META_TYPE=>$this->_type,
                        self::FIELD_META_COIN=>$this->_coin,
                        self::FIELD_META_AMOUNT=>$this->_amount,
                        self::FIELD_META_VALUE=>$this->_value,
                        self::FIELD_META_DETAILS=>$this->_details,
                        self::FIELD_META_DATE_CREATED=>$this->_date,
                    ));
            
            if( $this->_transaction_id ){
                
                $this->_status = self::TRANSACTION_STATUS_SAVED;
                
                return true;
            }
        }
        
        return false;
    }
    /**
     * Retorna la cantidad de la transacción, si se define mostrar moneda, se retorna con formato
     * @return int
     */
    public final function getAmount( ){ return $this->_amount; }
    /**
     * @return float Valor monetario de la transacción en €
     */
    public final function getValue(){ return $this->_value; }
    /**
     * @return int Estado de la transacción
     */
    public final function getStatus(){ return $this->_status; }
    /**
     * @return int ID de cuenta de Inversor
     */
    public final function getAccountId(){ return $this->_account_id; }
    /**
     * @return int Tipo de moneda, AirCoin o Real (€)
     */
    public final function getCoin(){ return $this->_coin; }
    /**
     * @return int Tipo de transacción
     */
    public final function getType(){ return $this->_type ; }
    /**
     * @return String Concepto de la transacción
     */
    public final function getDetail(){ return $this->_details; }
    /**
     * @return int ID de la transacción
     */
    public final function getTransactionId(){ return $this->_transaction_id; }
    /**
     * @return String Timestamp de la fecha de la transacción
     */
    public final function getDate(){
        return $this->_date;
    }
    /**
     * @return string Formato de la cantidad con tipo de moneda
     */
    public static final function displayAmount( $amount, $coin = self::TRANSACTION_COIN_AIRPOINTS ){
        
        switch( $coin ){
            case self::TRANSACTION_COIN_AIRPOINTS:
                return sprintf(
                        '%s <span class="coin coint-%s">%s</span>',
                        $amount, $coin,AirBoxStringModel::__('AP'));
            case self::TRANSACTION_COIN_REAL:
                return sprintf(
                    '%s <span class="coin coin-%s">%s</span>',
                    $amount, $coin,AirBoxStringModel::__('€'));
            case self::TRANSACTION_COIN_BOX:
                return sprintf(
                    '%s <span class="coin coin-%s">%s</span>',
                    $amount, $coin, AirBoxStringModel::__('BOX'));
        }
        
        return '';
    }
    /**
     * @param float $value
     * @param int $coin
     * @return string Formato del valor con el tipo de moneda
     */
    public static final function displayValue( $value , $coin = self::TRANSACTION_COIN_REAL ){
        switch( $coin ){
            case self::TRANSACTION_COIN_BOX:
                return sprintf('%s <span class="coin coin-%s">%s</span>',
                        $value,$coin,  AirBoxStringModel::__('BOX') );
            case self::TRANSACTION_COIN_AIRPOINTS:
                return sprintf('%s <span class="coin coin-%s">%s</span>',
                        $value,$coin,  AirBoxStringModel::__('AP') );
            case self::TRANSACTION_COIN_REAL:
                return sprintf('%s <span class="coin coin-%s">%s</span>',
                        $value,$coin,  AirBoxStringModel::__('€') );
        }
        return '';
    }
    /**
     * @return string Tipo de transacción
     */
    public static final function displayType( $type ){
        switch( $type ){
            case self::TRANSACTION_TYPE_PURCHASE:
                return AirBoxStringModel::__('Compra' );
            case self::TRANSACTION_TYPE_REFUND:
                return AirBoxStringModel::__('Reintegro' );
            case self::TRANSACTION_TYPE_REWARD:
                return AirBoxStringModel::__('Recompensa' );
            case self::TRANSACTION_TYPE_RESERVE:
                return AirBoxStringModel::__('Reserva' );
            default:
                return AirBoxStringModel::__('N/D' );
        }
    }
    /**
     * @return string Tipo de moneda con formato
     */
    public static final function displayCoin( $coin = self::TRANSACTION_COIN_AIRPOINTS ){
        switch( $coin ){
            case self::TRANSACTION_COIN_AIRPOINTS:
                return AirBoxStringModel::__('APs' ) ;
            case self::TRANSACTION_COIN_REAL:
                return AirBoxStringModel::__('€' );
            case self::TRANSACTION_COIN_BOX:
                return AirBoxStringModel::__('BOXes' );
        }
        return '';
    }
}
