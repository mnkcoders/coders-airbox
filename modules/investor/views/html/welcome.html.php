<?php defined('ABSPATH') or die;

$investor = AirBoxRenderer::getInstance()->getModel();

?>
<h1 class="title investor-plan-<?php echo $investor->getPlan(); ?>"><?php

    if( $investor->getPlan() > AirBoxInvestorModel::INVESTOR_PLAN_NONE ) {
        /*echo sprintf(__('Bienvenido <a href="%s" target="_self">%s</a>'),
            AirBoxRouter::RoutePublic(array(
            AirBoxEventModel::EVENT_SELECTED_VIEW=>AirBoxInvestorBootStrap::INVESTOR_OPTION_PROFILE)),
            $investor->getValue(AirBoxInvestorModel::FIELD_META_FIRST_NAME));
         */
        echo sprintf(__('Bienvenido %s'),
            $investor->getValue(AirBoxInvestorModel::FIELD_META_FIRST_NAME));
    }
    else{
        echo sprintf(__('Bienvenido %s'),$investor->getValue(AirBoxInvestorModel::FIELD_META_FIRST_NAME));
    }

?></h1>


