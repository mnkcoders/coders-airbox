<?php defined('ABSPATH') or die;
/**
 * Vista del invitado
 */
class AirBoxGuestView extends AirBoxRenderer{

    const VIEW_AFFILIATION_FORM = 'affiliation';
    const VIEW_LOGIN_FORM = 'login';

    /**
     * @return String Nombre de usuario si se ha registrado recientemente
     */
    protected final function get_user_data(){
        if( !is_null($this->getModel()) ){
            return $this->getModel()->getValue(AirBoxInvestorModel::FIELD_META_USER_NAME,'' );
        }
        return '';
    }
    /**
     * @return int Contador de boxes disponibles (no reservados)
     */
    protected final function get_available_data() {
        return AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE);
    }
    /**
     * @return int Número de boxes seleccionados  o por defecto 1 
     */
    protected final function get_box_amount_data(){
        return  !is_null( $this->getModel()) ?
            $this->getModel()->getValue(AirBoxInvestorModel::FIELD_META_BOXES,1) : 1;
    }
    /**
     * @return String Retorna la clave de inversor para el form de captura
     */
    protected final function get_investor_key_data(){
        if( !is_null($this->getModel()) ){
            return $this->getModel()->getValue(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY);
        }
        return null;
    }
}