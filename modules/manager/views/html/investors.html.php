<?php defined('ABSPATH') or die;

$investors = $this->get_data('investors');

$inv_status = $this->get_data( 'inv_status' );

$inv_plan = $this->get_data( 'inv_plan' );

?>
<table class="coinbox-table full-width investors">
        <thead>
            <!--tr>
                <th colspan="10">Buscar</th>
            </tr-->
            <tr>
                <th>Inversor</th>
                <th>DNI</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Tel&eacute;fono</th>
                <th>Airpoints</th>
                <th>Referido por</th>
                <th>Plan</th>
                <th>Estado</th>
                <th>Fecha de entrada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $investors as $invData ) : ?>
            <tr>
                <td><a href="<?php
                
                echo AirBoxRouter::Route(null,
                        array(
                            AirBoxEventModel::EVENT_SELECTED_VIEW=>AirBoxManagerBootStrap::ADMIN_OPTION_INVESTOR_PROFILE,
                            AirBoxInvestorModel::FIELD_META_ID=>$invData[AirBoxInvestorModel::FIELD_META_ID]));
                
                ?>" target="_self"><?php
                
                echo sprintf('%s %s',
                        $invData[AirBoxInvestorModel::FIELD_META_FIRST_NAME],
                        $invData[AirBoxInvestorModel::FIELD_META_LAST_NAME]);
                
                ?></a></td>
                <td><span><?php echo $invData[AirBoxInvestorModel::FIELD_META_DOCUMENT_ID]; ?></span></td>
                <td><a href="<?php
                
                echo admin_url('user-edit.php?user_id='.$invData[AirBoxInvestorModel::FIELD_META_ID]);
                
                ?>" target="_self"><?php
                
                echo $invData[AirBoxInvestorModel::FIELD_META_USER_NAME];
                
                ?></a></td>
                <td><span><?php echo $invData[AirBoxInvestorModel::FIELD_META_EMAIL]; ?></span></td>
                <td><span><?php echo $invData[AirBoxInvestorModel::FIELD_META_TELEPHONE]; ?></span></td>
                <td><span><?php echo $invData[AirBoxInvestorModel::FIELD_META_AIRPOINTS]; ?></span></td>
                <td>
                    <?php if( !is_null($invData[AirBoxInvestorModel::FIELD_META_PARENT_ID]) ) : ?>
                    <a href="<?php
                    
                    echo AirBoxRouter::Route(
                            null,
                            array(
                                'view'=>'investors',
                                AirBoxInvestorModel::FIELD_META_ID=>$invData[AirBoxInvestorModel::FIELD_META_PARENT_ID]));
                    
                    ?>" target="_self"><?php
                    
                    echo sprintf('%s %s',$invData['parent_first_name'],$invData['parent_last_name']);
                    
                    ?></a>
                    <?php else: ?>
                    <span>--</span>
                    <!--a href="<?php
                    
                    echo AirBoxRouter::Route(null, array(
                            'view'=>AirBoxManagerBootStrap::ADMIN_OPTION_INVESTOR_PROFILE,
                            AirBoxInvestorModel::FIELD_META_ID=>$invData[AirBoxInvestorModel::FIELD_META_ID]));

                    ?>" target="_self"><?php echo AirBoxStringModel::__('Reasignar?'); ?></a-->
                    <?php endif; ?>
                </td>
                <td><span><?php
                
                echo $invData[AirBoxInvestorModel::FIELD_META_PLAN] > 0 ?
                    AirBoxInvestorModel::displayPlan($invData[AirBoxInvestorModel::FIELD_META_PLAN]) :
                    '--';
                
                ?></span></td>
                <td><span><?php
                
                echo AirBoxInvestorModel::displayStatus($invData[AirBoxInvestorModel::FIELD_META_STATUS]);
                
                ?></span></td>
                <td><span><?php echo $invData[AirBoxInvestorModel::FIELD_META_DATE_CREATED]; ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <!--tr>
                <td colspan="10">paginaci&oacute;n</td>
            </tr-->
        </tfoot>
    </table>