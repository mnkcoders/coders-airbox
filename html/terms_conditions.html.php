<?php defined('ABSPATH') or die;

/**
 * @todo Subir actualización a globalcoinbox.mnkvisual.eu
 * @todo Subir actualización a globalcoinbox.com
 */

?>
<div class="content terms-conditions">
    <a href="<?php
    
    echo AirBox::getOption('coinbox_contract_link','#' );
    //echo site_url('/wp-content/uploads/2015/12/CONTRATO-PARA-INVERSORES.pdf');
    
    ?>" target="_self">* <?php
    
    echo AirBoxStringModel::__( AirBoxStringModel::LBL_INFO_TERMS_AND_CONDITIONS );
    
    ?></a>
</div>