<?php defined('ABSPATH') or die;
/**
 * Vista de perfil del inversor
 */

$form = $this->get_data('profile_form');

?>
<form name="password_reset" action="<?php echo AirBoxRouter::RoutePublic(); ?>" method="post" >
    <?php echo AirBoxRenderer::renderHidden(
            AirBoxEventModel::EVENT_SELECTED_VIEW,
            AirBoxInvestorBootStrap::INVESTOR_OPTION_PROFILE); ?>
    <ul class="password">
        <li>
            <label for="id_<?php echo AirBoxInvestorModel::FIELD_META_USER_PASS; ?>"><?php

            echo AirBoxStringModel::__('Resetear password');

            ?></label>
            <?php echo AirBoxRenderer::renderPassword(
                    AirBoxInvestorModel::FIELD_META_USER_PASS, 'primary',
                    AirBoxStringModel::__(AirBoxStringModel::LBL_USER_PASS) ); ?>
        </li>
        <li><?php echo AirBoxRenderer::renderSubmit(
            AirBoxEventModel::EVENT_TYPE_COMMAND,
            'reset_password',
            AirBoxStringModel::__('Cambiar password'));

        ?></li>
    </ul>
</form>