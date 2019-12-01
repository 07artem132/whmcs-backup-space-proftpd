<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 01.12.2019, 0:53
 *
 */

use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\CronController;

require __DIR__ . '/../../../init.php';

$cron = new CronController();
$cron->runTasks();