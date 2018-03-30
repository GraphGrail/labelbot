<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */
/**
 * @var \omnilight\scheduling\Schedule $schedule
 */

$schedule
    ->command('blockchain/update-completed-work')
    ->hourly();