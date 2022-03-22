<?php defined('ABSPATH') or die;
/**
 * Vista formulario de Reintegrp para la intranet del inversor
 * 
 * El inversor puede solicitar el reintegro de X Airpoints desde aqui
 */

$investor = $this->getModel();

?>
<ul class="content contact">

    <li><?php echo AirBoxRenderer::renderTextArea(
            'message', '', 'message',
            AirBoxStringModel::__(AirBoxStringModel::LBL_MESSAGE) ); ?></li>

    <?php if( !is_null($investor) ) : ?>
    <li><?php
        //incluir la referencia al usuario/inversor
        echo AirBoxRenderer::renderHidden(
                AirBoxInvestorModel::FIELD_META_ID,
                $investor->getId() );
        
        echo AirBoxRenderer::renderHidden('honeypot', '');
    
        echo AirBoxRenderer::renderSubmit(
            AirBoxEventModel::EVENT_TYPE_COMMAND,
            AirBoxInvestorBootStrap::INVESTOR_OPTION_REFUND,
            AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_REQUEST_FORM),
                'contact-request'); ?></li>

    <?php endif; ?>
</ul>