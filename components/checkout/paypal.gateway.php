<?php defined('ABSPATH') or die;
/**
 * Configuración de pasarela de pago
 */
class AirBoxCheckoutPayPalGateWay extends AirBoxCheckoutModel{
    
    protected function __construct(array $settings) {
        
        parent::registerSetting( self::CHECKOUT_LABEL, 'PayPal' );
        
        parent::__construct($settings);
    }
    /**
     * @return array Lista de contenidos
     */
    public function getContent() {
        
        return array();
    }
    /**
     * 
     * @return URL
     */
    public function getActionUrl() {
        return parent::getActionUrl();
    }
    /**
     * 
     * @return array
     */
    public function getFormData() {
        return parent::getFormData();
    }
    /**
     * 
     * @return HTML
     */
    public final function doCommitButton() {
        return parent::doCommitButton();
    }
    /**
     * 
     * @param \AirBoxEventModel $e
     */
    public function dispatchCallBack(\AirBoxEventModel $e) {
        
        AirBoxNotifyModel::RegisterLog(
                sprintf('Notificado el pago por PayPal'),
                AirBoxNotifyModel::LOG_TYPE_DEBUG);
        
        return true;
    }
}