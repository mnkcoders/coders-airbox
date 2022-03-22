<?php defined('ABSPATH') or die;
if( count( $logs = AirBoxNotifyModel::getLogs() ) ) : ?>
<ul class="coinbox-logs">
    <?php foreach( $logs as $logData ) : ?>
    <li><?php echo $logData; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

