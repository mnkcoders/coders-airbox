<?php defined('ABSPATH') or die; ?>
<div class="tab-content recover">
    <p><?php echo sprintf('<a class="button" href="%s" target="_self">%s</a>',
            site_url('#seccion-contacto'),
            AirBoxStringModel::__( AirBoxStringModel::LBL_INFO_ADMIN_CONTACT ));
        ?></p>
    <p><?php echo AirBoxStringModel::__(
            AirBoxStringModel::LBL_INFO_ADMIN_CONTACT_DETAIL ); ?></p>
</div>