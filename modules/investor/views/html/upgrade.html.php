<?php defined('ABSPATH') or die;

/**
 * Definir aqui una automatización cuando haya tiempo captando  del array de planes de inversor
 * desde el modelo de inversores
 */
?>
<div class="content upgrade">
    <p><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_INFO_INVESTOR_UPGRADE_TIP); ?></p>
    <ul class="plan-upgrade">
        <li><span class="top"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_BASIC);
        
        ?></span><span class="icon investor-plan-1"></span><span class="bottom"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_BASIC_DESC) ;
        
        ?></span></li>
        <li><span class="top"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_PLUS);
        
        ?></span><span class="icon investor-plan-2"></span><span class="bottom"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_PLUS_DESC) ;
        
        ?></span></li>
        <li><span class="top"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_PRO);
        
        ?></span><span class="icon investor-plan-3"></span><span class="bottom"><?php
        
        echo AirBoxStringModel::__(AirBoxStringModel::LBL_INVESTOR_PLAN_PRO_DESC) ;
        
        ?></span></li>
    </ul>
</div>