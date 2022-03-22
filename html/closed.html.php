<?php defined('ABSPATH') or die; ?>
<div class="tab-content full-width closed">
    <h2 class="title"><?php
    
    echo AirBoxStringModel::__(AirBoxStringModel::LBL_TITLE_PROJECT_FINISHED ); ?></h2>
    <p><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_INFO_PROJECT_FINISHED_DETAIL ); ?></p>
    <p>
        <a class="button" href="<?php echo get_site_url(); ?>" target="_self"><?php

        echo AirBoxStringModel::__(AirBoxStringModel::LBL_BUTTON_BACK_TO_HOME );?></a>
    </p>
</div>
