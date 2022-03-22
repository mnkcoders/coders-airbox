<?php defined('ABSPATH') or die;

//$incoming_investors = $this->get_data('incoming');

?>
<div class="<?php echo AirBoxRenderer::getInstance()->getClass('view'); ?>">
    <?php AirBoxRenderer::getInstance()->getTemplate('notify');?>
    <h2 class="title"><?php
    
    echo AirBoxStringModel::__(AirBoxStringModel::LBL_MENU_OPTION_DASHBOARD);
    
    ?></h2>
    <?php AirBoxRenderer::getInstance()->getTemplate('dashboard_incoming');?>
    <?php AirBoxRenderer::getInstance()->getTemplate('dashboard_orders'); ?>
    <?php AirBoxRenderer::getInstance()->getTemplate('dashboard_project');?>
</div>