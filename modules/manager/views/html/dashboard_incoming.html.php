<?php defined('ABSPATH') or die;

$incoming_investors = $this->get_data('incoming');

if(count($incoming_investors) ) : ?>
    <ul class="dashboard incoming">
        <li class="title"><?php echo AirBoxStringModel::__('Nuevos inversores' ); ?></li>
        <?php foreach( $incoming_investors as $investor ) : ?>
        <li>
            <a class="investor <?php
        
        if( !is_null($investor['parent_id']) ){ echo 'has-parent'; }
        
        ?>" href="<?php
        
        //user_id
        echo AirBoxRouter::Route(null,array('view'=>'investors',AirBoxInvestorModel::FIELD_META_ID=>$investor['user_id']));
        
        ?>" target="_self"><?php 
        
        //first_name - last_name
        echo sprintf('%s %s',
                $investor[AirBoxInvestorModel::FIELD_META_FIRST_NAME],
                $investor[AirBoxInvestorModel::FIELD_META_LAST_NAME]);
        
        ?></a>
            <span class="document_id"><?php
            
            echo sprintf(' ( %s: %s )',
                AirBoxStringModel::__('DNI' ),
                $investor[AirBoxInvestorModel::FIELD_META_DOCUMENT_ID]);
            
            ?></span>
        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>