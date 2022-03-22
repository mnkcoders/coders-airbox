<?php defined('ABSPATH') or die;

$investor = $this->get_data('investor');

if( !is_null($investor)) : ?>
<ul class="coinbox-content half-width profile">
    <li class="title">
        <h2><?php

        echo sprintf(
                '%s [ <a href="%s" target="_self">%s</a> ]',
                $investor->displayName(),
                admin_url('user-edit.php?user_id='.$investor->getId()),
                $investor->displayUserName());

        ?></h2>
    </li>
    <li>
        <span class="label"><?php echo AirBoxStringModel::__('DNI'); ?></span>
        <strong><?php echo $investor->displayDocumentId(); ?></strong>
    </li>
    <li>
        <span class="label"><?php echo AirBoxStringModel::__('Email'); ?></span>
        <?php echo $investor->displayEmail( true); ?>
    </li>
    <li>
        <span class="label"><?php echo AirBoxStringModel::__('Tel&eacute;fono de contacto'); ?></span>
        <?php echo $investor->displayTelephone(true); ?>
    </li>
    <li>
        <span class="label"><?php echo AirBoxStringModel::__('Alta'); ?></span>
        <strong><?php echo $investor->displayDateCreated( ); ?></strong>
    </li>
    <li>
        <span class="label"><?php echo AirBoxStringModel::__('&Uacute;ltima actualizaci&oacute;n'); ?></span>
        <strong><?php echo $investor->displayDateUpdated( ); ?></strong>
    </li>
</ul>
<?php endif; ?>