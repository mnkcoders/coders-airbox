<?php defined('ABSPATH') or die;

$transactions = $this->get_data('profile_transactions');

$trans_type = $this->get_data('trans_type');

$trans_coin = $this->get_data('trans_coin');

?>
<table class="coinbox-content full-width transactions">
    <thead>
        <tr>
            <th colspan="6"><h2><?php echo AirBoxStringModel::__('Historial'); ?></h2></th>
            <th></th>
        </tr>
        <tr>
            <th>Num. Transacci&oacute;n</th>
            <th>Inversor</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Valor</th>
            <th>Concepto</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $transactions as $transData ) : ?>
        <tr>
            <td><strong>[ <?php echo $transData[AirBoxTransactionModel::FIELD_META_ID]; ?> ]</strong></td>
            <td><?php

            echo $transData[AirBoxInvestorModel::FIELD_META_AFFILIATE];

            ?></td>
            <td><?php echo AirBoxTransactionModel::displayType(
                    $transData[AirBoxTransactionModel::FIELD_META_TYPE] ); ?></td>
            <td><?php
            
            echo sprintf('%s %s',
                    $transData[AirBoxTransactionModel::FIELD_META_AMOUNT],
                    AirBoxTransactionModel::displayCoin(
                            $transData[AirBoxTransactionModel::FIELD_META_COIN]));
            
            ?></td>
            <td><?php
            
            if( $transData[AirBoxTransactionModel::FIELD_META_VALUE] > 0 ){
                echo AirBoxTransactionModel::displayValue(
                        $transData[AirBoxTransactionModel::FIELD_META_VALUE] );
            }
            
            ?></td>
            <td><?php echo $transData[AirBoxTransactionModel::FIELD_META_DETAILS]; ?></td>
            <td><?php echo $transData[AirBoxTransactionModel::FIELD_META_DATE_CREATED]; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <!--tr>
            <td colspan="7">paginaci&oacute;n</td>
        </tr-->
    </tfoot>
</table>