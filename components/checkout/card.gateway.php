<?php defined('ABSPATH') or die;
/**
 * Configuración de pasarela de pago
 */
class AirBoxCheckoutCardGateWay extends AirBoxCheckoutModel{
    
    protected function __construct(array $settings) {
        
        parent::registerSetting( self::CHECKOUT_LABEL, 'Tarjeta' );
        
        parent::__construct($settings);
    }
    /**
     * @return array Lista de contenidos
     */
    public function getContent() {
        
        return array();
    }

    public function getActionUrl() {
        return parent::getActionUrl();
    }

    public function getFormData() {
        return parent::getFormData();
    }
    /**
     * @todo por implementar
     */
    public function doCommitButton() { 
        return parent::doCommitButton();
    }
    /**
     * 
     * @param \AirBoxEventModel $e
     */
    public function dispatchCallBack(\AirBoxEventModel $e) {
        
        AirBoxNotifyModel::RegisterLog(
                sprintf('Notificado el pago por Tarjeta (placeholder)'),
                AirBoxNotifyModel::LOG_TYPE_DEBUG);
        
        return true;
    }
}