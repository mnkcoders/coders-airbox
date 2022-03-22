<?php defined('ABSPATH') or die;
/**
 * Vista formulario de Reintegrp para la intranet del inversor
 * 
 * El inversor puede solicitar el reintegro de X Airpoints desde aqui
 */

//definir si esto es correcto...
$min_coins = 1;
//cantidad de aircoints
$aircoins = $this->get_data('airpoints');
//valor del aircoin
$aircoin_value = 1;

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('header');?>
    <div class="coinbox-content">
        <?php AirBoxRenderer::renderMessages('full-width'); ?>
        <div class="tab-content full-width request-refund">
            <h1 class="title"><?php
            
            echo AirBoxStringModel::__( AirBoxStringModel::LBL_TITLE_REFUND ); ?></h1>
            <p><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_INFO_REFUND_CONDITIONS); ?></p>
            <p><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_INFO_REQUEST_BANK_ACCOUNT ); ?></p>
            <h4><?php echo sprintf('%s AirPoints = %s €',
                    $aircoins, $aircoins * $aircoin_value ); ?></h4>
            <form name="investor-refund" action="<?php
                echo AirBoxRouter::RoutePublic(array(
                    AirBoxEventModel::EVENT_TYPE_COMMAND => AirBoxInvestorBootStrap::INVESTOR_OPTION_REFUND,
                    AirBoxEventModel::EVENT_SELECTED_VIEW => AirBoxInvestorView::INVESTOR_MENU_DASHBOARD,
                ));
            ?>" method="post">
            <?php AirBoxRenderer::getInstance()->getTemplate('contact');?>
            </form>
        </div>
    </div>
</div>