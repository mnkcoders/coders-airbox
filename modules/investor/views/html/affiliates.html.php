<?php defined('ABSPATH') or die;
/**
 * Vista resumen de inversores atraidos por el pefil actual
 */

$total_boxes = 0;

$total_pricing = 0;

$affiliates = $this->get_data('affiliates');

$investor_key = AirBox::Instance()->getProfileData(AirBoxInvestorModel::FIELD_META_INVESTOR_KEY);

?>
<?php if( count($affiliates) ) : ?>
<table class="content affiliates">
    <thead>
        <tr>
            <th ><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_AFFILIATE_NAME);?></th>
            <th ><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_DATE_ENTERED); ?></th>
            <th ><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_BOX_AMOUNT);?></th>
            <th ><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_DATE_ORDERED);?></th>
            <th ><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_AFFILIATE_PROFIT);?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach( $affiliates as $child ) : ?>
        <!-- <?php print_r($child); ?>-->
        <tr>
            <td><span class="affiliate-icon"><?php
            
            echo $child[AirBoxInvestorModel::FIELD_META_AFFILIATE];
            
            ?></span></td>
            <td><?php
            
            echo substr($child[AirBoxInvestorModel::FIELD_META_DATE_CREATED],0,10);
            
            ?></td>
            <td><?php
            
            $total_boxes += $child[AirBoxOrderModel::FIELD_META_AMOUNT];
            
            echo $child[AirBoxOrderModel::FIELD_META_AMOUNT];
            
            ?></td>
            <td><?php 
            
                echo substr($child[AirBoxOrderModel::FIELD_META_DATE_UPDATED],0,10);
            
            ?></td>
            <td><?php
            
            $total_pricing += $child[AirBoxOrderModel::FIELD_META_VALUE];
            
            echo sprintf('%s €',
                    number_format($child[AirBoxOrderModel::FIELD_META_VALUE]),
                    0,',','.');
            ?></td>
        </tr>
<?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2"><?php echo AirBoxStringModel::__( AirBoxStringModel::LBL_TOTAL ); ?></th>
            <th><?php echo $total_boxes; ?></th>
            <th></th>
            <th><?php echo sprintf('%s €', number_format($total_pricing, 2,',','.') ); ?></th>
        </tr>
        <tr>
            <td colspan="5">* <a href="<?php echo AirBoxRouter::RoutePublic(array(
                AirBoxEventModel::EVENT_SELECTED_VIEW => $investor_key )); ?>" target="_blank"><?php
                echo AirBoxStringModel::__( AirBoxStringModel::LBL_INFO_AFFILIATION_TIP );
            ?></a></td>
        </tr>
    </tfoot>
</table>
<?php else : ?>
<div class="content affiliates">
    <p classs="highlight"><?php echo AirBoxStringModel::__( AirBoxStringModel::LBL_AFFILIATE_LIST_EMPTY ); ?></p>
    <p>* <a href="<?php echo AirBoxRouter::RoutePublic(array(
        AirBoxEventModel::EVENT_SELECTED_VIEW => $investor_key
        )); ?>" target="_blank"><?php
        echo AirBoxStringModel::__( AirBoxStringModel::LBL_INFO_AFFILIATION_TIP );
    ?></a></p>
</div>
<?php endif; ?>