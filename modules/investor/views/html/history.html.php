<?php defined('ABSPATH') or die;
/**
 * Vista registro de transacciones de inversor
 * 
 * El inversor puede consultar todos los movimientos que afectan a su cuenta de usuario de AirBox
 * 
 * No puede generar ningún tipo de evento.
 * 
 * Interesa que tenga posibilidad de exportar?
 * 
 */
?>
<table class="content transactions">
    <thead>
        <tr>
            <th><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_DATE); ?></th>
            <th><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_TYPE); ?></th>
            <th><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_AMOUNT); ?></th>
            <th><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_VALUE); ?></th>
            <th><?php echo AirBoxStringModel::__(AirBoxStringModel::LBL_DETAIL); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach( $this->get_data('transaction',array()) as $transaction ) : ?>
        <tr>
            <td><?php echo $transaction[AirBoxTransactionModel::FIELD_META_DATE_CREATED]; ?></td>
            <td><?php echo AirBoxTransactionModel::displayType(
                    $transaction[AirBoxTransactionModel::FIELD_META_TYPE] ); ?></td>
            <td><?php echo AirBoxTransactionModel::displayAmount(
                    $transaction[AirBoxTransactionModel::FIELD_META_AMOUNT],
                    $transaction[AirBoxTransactionModel::FIELD_META_COIN]); ?></td>
            <td><?php
            
            if( $transaction[AirBoxTransactionModel::FIELD_META_VALUE] > 0 ){
                echo AirBoxTransactionModel::displayValue(
                        $transaction[AirBoxTransactionModel::FIELD_META_VALUE]);
            }
            else{
                echo '--';
            }

            ?></td>
            <td><?php echo $transaction[AirBoxTransactionModel::FIELD_META_DETAILS]; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <!--tfoot>
        <tr>
            <th colspan="5"></th>
        </tr>
    </tfoot-->
</table>