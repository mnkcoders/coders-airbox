<?php defined('ABSPATH') or die; ?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <p><?php 
    
    echo AirBoxStringModel::__('Est&aacute; registrado como administrador');
    
    ?></p>
    <p><?php
    
    echo AirBoxStringModel::__(
            'Este contenido debe ser gestionado desde el panel de administraci&oacute;n');
    
    ?></p>
    <p><a href="<?php
    
    echo site_url('wp-admin/admin.php?page='.AirBoxManagerBootStrap::ADMIN_OPTION_ROOT);
    
    ?>" target="_blank" class="button"><?php
    
    echo AirBoxStringModel::__('Administraci&oacute;n AirBox');
    
    ?></a></p>
</div>
