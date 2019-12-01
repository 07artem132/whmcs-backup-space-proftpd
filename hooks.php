<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 29.11.2019, 13:13
 *
 */

use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\ProFTPDController;
use WHMCS\Service\Service;

add_hook('ClientAreaPageUpgrade', 1, function ($vars) {
    $configoptions = $vars['configoptions'];
    $service = Service::find($vars['id']);
    $product = $service->product()->first();
    $configOptionSpace = explode(' - ', $product['configoption2'])[1];
    $configOptionLocation = explode(' - ', $product['configoption1'])[1];

    $api = new ProFTPDController($service->serverId);
    $stats = $api->getAccountStats($service->username);

    for ($i = 0; $i < count($configoptions); $i++) {
        if ($configoptions[$i]['optionname'] == $configOptionLocation) {
            unset($configoptions[$i]);
            continue;
        }

        if ($configoptions[$i]['optionname'] != $configOptionSpace) {
            continue;
        }

        for ($j = 0; $j < count($configoptions[$i]['options']); $j++) {
            $spaceUpgrade = (int)filter_var($configoptions[$i]['options'][$j]['rawName'], FILTER_SANITIZE_NUMBER_INT);
            if ($stats['disk_use'] >= $spaceUpgrade * 1073741824) {
                unset($configoptions[$i]['options'][$j]);
            }
        }
    }

    return [
        'configoptions' => $configoptions
    ];
});


