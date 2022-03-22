<?php defined('ABSPATH') or die;
/**
 * 
 * MODO DEBUG, ESTA PASARELA NO DEBERÍA UTILZIARSE EN PRODUCCIÓN
 * 
 * Configuración de pasarela de pago
 */
class AirBoxCheckoutDebugGateWay extends AirBoxCheckoutModel{
    
    protected function __construct(array $settings) {

        parent::registerSetting( self::CHECKOUT_LABEL, 'Activaci&oacute;n autom&aacute;tica' );
        
        parent::__construct($settings);
    }
    /**
     * Contenido del form
     * @return array
     */
    public function getContent() {
        return array(
            'Pruebas de desarrollo',
            'Pasarela de activaci&oacute;n autom&aacute;tica del pedido.',
            sprintf('%s: <strong>%s €</strong>',
                    AirBoxStringModel::__(AirBoxStringModel::LBL_PRICE_AMOUNT),
                    $this->getOrderValue())
        );
    }
    /**
     * Esta pasarela auto-activa el pedido sin mas
     * @param \AirBoxEventModel $e
     */
    public function dispatchCallBack(\AirBoxEventModel $e) {
        
        AirBoxNotifyModel::RegisterLog(
                sprintf('Notificado el pago de activaci&oacute;n autom&aacute;tica en modo desarrollo'),
                AirBoxNotifyModel::LOG_TYPE_DEBUG);

        return true;
    }
}