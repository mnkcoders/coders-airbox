<?php defined('ABSPATH') or die;

$total_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);

$coin_list = $this->get_data('coin_list',array());

$log_type_list = $this->get_data('notification_types',array());

$wp_roles = $this->get_data('wp_roles',array());

$wp_pages = $this->get_data('wp_pages',array());

$wp_categories = $this->get_data('wp_categories',array());

/**
 * @var array Activa / Desactiva una opción
 */
$option_toggle = $this->get_data('option_togle');

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    
    <?php AirBoxRenderer::getInstance()->getTemplate('notify');?>
    
    <h2 class="title"><?php echo AirBoxStringModel::__('Configuraci&oacute;n'); ?></h2>

    <form name="coinbox-settings-form" class="coinbox-settings-form" action="<?php
    
    echo AirBoxRouter::Route(null,array('view'=>AirBoxManagerBootStrap::ADMIN_OPTION_SETTINGS));
    
    ?>" method="post">
        <h4 class="title"><?php echo AirBoxStringModel::__('Integraci&oacute;n CMS'); ?></h4>
        <ul class="coinbox-content general-settings" >
            <li><label for="id_user_role"><?php

                echo AirBoxStringModel::__('Rol de usuario para el nuevo afiliado');

                ?></label>
                <?php echo AirBoxRenderer::renderList(
                    'user_role', $wp_roles,
                    AirBox::getOption('user_role')) ; ?></li>
            <li><label for="id_coinbox_blog_category"><?php

                echo AirBoxStringModel::__('Categor&iacute;a entradas');

                ?></label>
                <?php echo AirBoxRenderer::renderList(
                    'coinbox_blog_category',$wp_categories,
                    AirBox::getOption('coinbox_blog_category')) ; ?></li>
            <li><label for="id_coinbox_lock_admin_menu"><?php
                /**
                 * Permite ocultar la barra de administrador de wordpress para los perfiles
                 * no administradores, a elección del gestor del sistema.
                 */
                echo AirBoxStringModel::__('Ocultar barra de administración al suscriptor');
                ?></label>
                <?php echo AirBoxRenderer::renderList(
                    'coinbox_lock_admin_menu', $option_toggle ,
                    AirBox::getOption('coinbox_lock_admin_menu',''),
                    AirBox::PLUGIN_OPTION_ENABLED) ; ?></li>
            <li><label for="id_coinbox_log_level"><?php

                echo AirBoxStringModel::__('Nivel de notificaci&oacute;n para el registro de LOGs');

                ?></label>
                <?php echo AirBoxRenderer::renderList(
                    'coinbox_log_level', $log_type_list,
                    AirBox::getOption('coinbox_log_level')) ; ?></li>
        </ul>
        <h4 class="title"><?php echo AirBoxStringModel::__('Par&aacute;metros de proyecto'); ?></h4>
        <ul class="coinbox-content project-settings" >
            <!--li><label for="id_coinbox_units"><?php
                //deshabilitado contador global de unidades desde la implantación de objetivos
                echo AirBoxStringModel::__('Total unidades del proyecto');

                ?></label>
                <?php echo AirBoxRenderer::renderNumber(
                        'coinbox_units',
                        AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0)); ?></li-->

            <li><label for="id_coinbox_cost"><?php

                echo AirBoxStringModel::__('Coste por unidad - Box');

                ?></label>
                <?php echo AirBoxRenderer::renderNumber(
                        'coinbox_cost',
                        AirBox::getOption('coinbox_cost',100)); ?></li>

            <li><label for="id_coinbox_coin"><?php

                echo AirBoxStringModel::__('Moneda');

                ?></label>
                <?php echo AirBoxRenderer::renderList(
                    'coinbox_coin',$coin_list ,
                    AirBox::getOption('coinbox_coin','€')) ; ?></li>

            <li><label for="id_termination_date"><?php

            echo AirBoxStringModel::__('Fecha de plazo del proyecto');

            ?></label><?php echo AirBoxRenderer::renderDate(
                    'termination_date',
                    AirBox::getOption('termination_date')); ?></li>
        </ul>
        <h4 class="title"><?php echo AirBoxStringModel::__('P&aacute;ginas y contenido'); ?></h4>
        <ul class="coinbox-content content-settings">
            <li><label for="id_coinbox_page"><?php

                echo AirBoxStringModel::__('P&aacute;gina de intranet');

                ?></label>
                <?php echo AirBoxRenderer::renderList(
                    'coinbox_page', $wp_pages,
                    AirBox::getOption('coinbox_page')) ; ?></li>
            <li><label for="id_coinbox_page_unactivated"><?php

                echo AirBoxStringModel::__('Pendiente de activaci&oacute;n');

                ?></label>
                <?php echo AirBoxRenderer::renderList(
                    'coinbox_page_unactivated', $wp_pages,
                    AirBox::getOption('coinbox_page_unactivated',0)) ; ?></li>
            <li><label for="id_coinbox_page_settlement"><?php

                echo AirBoxStringModel::__('Liquidaci&oacute;n');

                ?></label>
                <?php echo AirBoxRenderer::renderList(
                    'coinbox_page_settlement', $wp_pages,
                    AirBox::getOption('coinbox_page_settlement',0)) ; ?></li>
            <li><label for="id_coinbox_contract_link"><?php
                /**
                 * Modificación 2016-04-12 para agregar link de contrato PDF en menú de inversor
                 */
                echo AirBoxStringModel::__('Link de contrato de inversor (intranet)');
                ?></label>
                <?php echo AirBoxRenderer::renderMediaButton(
                        'coinbox_contract_link',
                        AirBox::getOption('coinbox_contract_link',0)); ?></li>
            <li><label for="id_coinbox_background_image"><?php
                /**
                 * Modificación 2016-07-21 facilitar cambio rápido de fondo en cabecera intranet
                 */
                echo AirBoxStringModel::__('Fondo para la cabecera de la intranet');
                ?></label>
                <?php echo AirBoxRenderer::renderMediaButton(
                        'coinbox_background_image',
                        AirBox::getOption('coinbox_background_image',0)); ?></li>
        </ul>
        <h4 class="title"><?php echo AirBoxStringModel::__('Configuraci&oacute;n de pago Transferencia'); ?></h4>
        <ul class="coinbox-content bank-transfer-gateway" >
            <li><label for="id_bank_transfer_label"><?php
                echo AirBoxStringModel::__('Nombre de la entidad');
                ?></label>
                <?php echo AirBoxRenderer::renderText(
                        'bank_transfer_label',
                        AirBox::getOption('bank_transfer_label',100)); ?></li>
            <li><label for="id_bank_transfer_account"><?php
                echo AirBoxStringModel::__('N&uacute;mero de Cuenta Bancaria');
                ?></label>
                <?php echo AirBoxRenderer::renderText(
                        'bank_transfer_account',
                        AirBox::getOption('bank_transfer_account',100)); ?></li>
        </ul>
        <h4 class="title"><?php echo AirBoxStringModel::__('Configuraci&oacute;n de pago Stripe'); ?></h4>
        <ul class="coinbox-content stripe-gateway">
            <li><label for="id_stripe_test_mode"><?php
                echo AirBoxStringModel::__('Modo desarrollo'); ?></label>
                <?php echo AirBoxRenderer::renderList( 'stripe_test_mode',
                $option_toggle,AirBox::getOption('stripe_test_mode',AirBox::PLUGIN_OPTION_DISABLED)); ?></li>
            <li><label for="id_stripe_test_sk"><?php
                echo AirBoxStringModel::__('Clave secreta (test)'); ?></label>
                <?php echo AirBoxRenderer::renderText( 'stripe_test_sk',
                AirBox::getOption('stripe_test_sk','')); ?></li>
            <li><label for="id_stripe_test_pk"><?php
                echo AirBoxStringModel::__('Clave p&uacute;blica (test)'); ?></label>
                <?php echo AirBoxRenderer::renderText( 'stripe_test_pk',
                AirBox::getOption('stripe_test_pk','')); ?></li>
            <li><label for="id_stripe_live_sk"><?php
                echo AirBoxStringModel::__('Clave secreta (producci&oacute;n)'); ?></label>
                <?php echo AirBoxRenderer::renderText( 'stripe_live_sk',
                AirBox::getOption('stripe_live_sk','')); ?></li>
            <li><label for="id_stripe_live_pk"><?php
                echo AirBoxStringModel::__('Clave p&uacute;blica (producci&oacute;n)'); ?></label>
                <?php echo AirBoxRenderer::renderText( 'stripe_live_pk',
                AirBox::getOption('stripe_live_pk','')); ?></li>
        </ul>
        <ul class="coinbox-content commands" >
            <!-- TEST AREA -->
            <li>
                <?php echo AirBoxRenderer::renderSubmit(
                    AirBoxEventModel::EVENT_TYPE_COMMAND,
                    'save_settings',
                    AirBoxStringModel::__('Guardar'),
                    'button-primary');
            ?></li>
        </ul>
    </form>
    <h4 class="title"><?php echo AirBoxStringModel::__('Herramientas'); ?></h4>
    <ul class="coinbox-content tools" >
        <!-- TEST AREA -->
        <li><label><?php echo AirBoxStringModel::__(
                'Borra los inversores que ya no tienen usuario en el sistema'); ?></label><?php
                echo AirBoxRenderer::renderLink(
                    AirBoxRouter::RouteAdmin(array(
                        AirBoxEventModel::EVENT_SELECTED_VIEW=>AirBoxManagerView::VIEW_LAYOUT_SETTINGS,
                        AirBoxEventModel::EVENT_TYPE_COMMAND=>AirBoxManagerBootStrap::ADMIN_OPTION_REMOVE_UNLINKED)),
                    AirBoxStringModel::__('Borrar Inversores desvinculados'), 'button' );
        ?></li>
        <!--li><label><?php echo AirBoxStringModel::__(
                'Vaciado completo de la base de datos de la intranet');
        ?></label><?php echo AirBoxRenderer::renderLink(
                    AirBoxRouter::RouteAdmin(array(
                        AirBoxEventModel::EVENT_SELECTED_VIEW=>AirBoxManagerView::VIEW_LAYOUT_SETTINGS,
                        AirBoxEventModel::EVENT_TYPE_COMMAND=>'reset_database')),
                    AirBoxStringModel::__('Vaciar datos intranet'),'button');
        ?></li-->
    </ul>
    <form name="coinbox-strings-form" class="coinbox-strings-form" action="<?php
    
    echo AirBoxRouter::Route(null,
            array(AirBoxEventModel::EVENT_SELECTED_VIEW=>AirBoxManagerBootStrap::ADMIN_OPTION_SETTINGS));    
    ?>" method="post">
        <ul class="coinbox-content translation-generator" >
            <li><label for="id_lang"><?php
                echo AirBoxStringModel::__('Idioma'); ?></label>
                <?php echo AirBoxRenderer::renderText('lang'); ?></li>
            <li><label for="id_locale"><?php
                echo AirBoxStringModel::__('Localizaci&oacute;n'); ?></label>
                <?php echo AirBoxRenderer::renderText('locale'); ?></li>
            <li><?php echo AirBoxRenderer::renderSubmit(
                    AirBoxEventModel::EVENT_TYPE_COMMAND,
                    'generate_translation',
                    'Generar fichero de traducciones');
            ?></li>
        </ul>
    </form>
</div>