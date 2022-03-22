<?php defined('ABSPATH') or die;
/**
* Configuración de pasarela de pago
*/
class AirBoxCheckoutBankTransferGateWay extends AirBoxCheckoutModel{
    
    const BT_ACCOUNT = 'account';
    
    protected function __construct(array $settings) {

        //configurado desde el backend
        parent::registerSetting( self::BT_ACCOUNT,AirBox::getOption('bank_transfer_account','') );
        parent::registerSetting( self::CHECKOUT_LABEL, AirBox::getOption('bank_transfer_label',''));
        
        parent::registerSetting( AirBoxInvestorModel::FIELD_META_DOCUMENT_ID,
                AirBox::Instance()->getProfileData(AirBoxInvestorModel::FIELD_META_DOCUMENT_ID));
        
        parent::__construct($settings);
    }
    /**
     * @return array Lista de contenidos
     */
    public function getContent() {
        
        return array(
            AirBoxStringModel::__compose(
                    'Pagar por transferencia bancaria: %s',
                    sprintf('<strong class="right">%s</strong>', $this->getLabel())),
            AirBoxStringModel::__compose(
                    'Num CC: %s',
                    sprintf('<strong class="right">%s</strong>',$this->getSetting(self::BT_ACCOUNT))),
            sprintf('%s: %s', AirBoxStringModel::__('Indicar concepto'), $this->displayDocumentId()),
            AirBoxStringModel::__('Una vez ingresada la cantidad total el administrador podr&aacute; proceder a activar <strong>su cuenta de inversor</strong>'),
        );
    }
    /**
     * @return HTML mostrar el DNI del inversor (concepto de la transferencia)
     */
    private function displayDocumentId(){
        
        $style = 'width: auto; display: inline-block; text-align: center; margin-left: 20px; float: right;';
        
        $value = sprintf( 'COINBOX: %s', $this->getSetting(AirBoxInvestorModel::FIELD_META_DOCUMENT_ID,'TU DNI') );
        
        return sprintf('<input type="text" style="%s" value="%s" disabled="disabled" />',$style,$value);
    }
    /**
     * No hay parámetros de formulario para el pago con tarnsferencia
     * @return type
     */
    public function getFormData() { return array(); }
    /**
     * No hace nada
     */
    public function doCommitButton() { return ''; }
    /**
     * Nunca se activa un pago por transferencia bancaria en el proceso.
     * Es el admin que debe supervisar el pago.
     * @param \AirBoxEventModel $e
     * @return boolean
     */
    public function dispatchCallBack(\AirBoxEventModel $e) {

        return false;
    }
}