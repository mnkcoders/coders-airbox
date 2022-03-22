<?php defined('ABSPATH') or die;
/**
 * Servicio gestionado por el CRON del sistema
 */
class AirBoxCronService implements AirBoxIService{
    /**
     * 
     * @return boolean
     */
    public function dispatch() {
        return false;
    }
    /**
     * 
     * @return \AirBoxCronService
     */
    public static function createService() {
        return new AirBoxCronService();
    }

}