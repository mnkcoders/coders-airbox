<?php defined('ABSPATH') or die; ?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('notify');?>
    <table class="coinbox-table full-width notifier">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Destinatario</th>
                <th>Mensaje</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>