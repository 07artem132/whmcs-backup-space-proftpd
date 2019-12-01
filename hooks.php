<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 29.11.2019, 13:13
 *
 */

use GuzzleHttp\Exception\RequestException;
use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\ProFTPDController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Exceptions\InvalidServerIdException;
use WHMCS\Service\Service;

add_hook('ClientAreaPageUpgrade', 1, function ($vars) {
    global $_LANG;
    $defaultLanguage = 'russian';
    $clientLanguage = $vars['clientsdetails']['language'];

    if (file_exists(sprintf(ModuleConfig::getBaseFullPath() . '/serverModule/lang/%s.php', $clientLanguage))) {
        include_once sprintf(ModuleConfig::getBaseFullPath() . '/serverModule/lang/%s.php', $clientLanguage);
    } else {
        include_once sprintf(ModuleConfig::getBaseFullPath() . '/serverModule/lang/%s.php', $defaultLanguage);
    }


    $configoptions = $vars['configoptions'];
    $service = Service::find($vars['id']);
    $product = $service->product()->first();
    $configOptionSpace = explode(' - ', $product['configoption2'])[1];
    $configOptionLocation = explode(' - ', $product['configoption1'])[1];
    $errormessage = '';

    try {
        $api = new ProFTPDController($service->serverId);
        $stats = $api->getAccountStats($service->username);
    } catch (InvalidServerIdException $e) {
        throw  $e;
        // echo '<div class=\'col-md-12\'><div class=\'alert alert-danger\'>The remote server is not responding</div></div>';
    } catch (RequestException $e) {
        throw  $e;
        //  echo '<div class=\'col-md-12\'><div class=\'alert alert-danger\'>The remote server is not responding</div></div>';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (array_key_exists('error', $_GET)) {
            $errormessage = $_LANG['BackupSpaceProftpd_you_have_assigned_less_space_than_used'];
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $optionID = explode('|', $product['configoption2'])[0];
        $diskUpgrade = (int)$_POST['configoption'][$optionID];
        $min = (int)ceil($stats['disk_use'] / 1073741824);
        if ($diskUpgrade > $min) {
            return [];
        } else {
            redir(['type' => $_POST['type'], 'id' => $_POST['id'], 'error' => 'limit']);
        }
    }

    for ($i = 0; $i < count($configoptions); $i++) {
        if ($configoptions[$i]['optionname'] == $configOptionLocation) {
            for ($x = 0; $x < count($configoptions[$i]['options']); $x++) {
                if ($configoptions[$i]['selectedoption'] != $configoptions[$i]['options'][$x]['nameonly']) {
                    unset($configoptions[$i]['options'][$x]);
                }
            }
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
        'errormessage' => $errormessage,
        'configoptions' => $configoptions
    ];
});


