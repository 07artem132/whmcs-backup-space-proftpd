<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 17:52
 *
 */

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\ProFTPDController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Models\NotifyModel;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function BackupSpaceProftpd_MetaData()
{
    return array(
        'DisplayName' => 'BackupSpaсeProftpd',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '5000', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '5000', // Default Non-SSL Connection Port
    );
}

function BackupSpaceProftpd_ConfigOptions()
{
    $custom_fields = [
        '0|Не выбрано'
    ];

    foreach (Capsule::table("tblproductconfiglinks")
                 ->join('tblproductconfiggroups', 'tblproductconfiglinks.gid', '=', 'tblproductconfiggroups.id')
                 ->join('tblproductconfigoptions', 'tblproductconfigoptions.gid', '=', 'tblproductconfiggroups.id')
                 ->where('pid', '=', $_POST['id'])->get() as $item) {
        $custom_fields[] = (string)$item->id . "|" . $item->name . ' - ' . $item->optionname;
    }

    return [
        "configOptionLocation" => [
            "FriendlyName" => "Выберите настраиваемое поле локации",
            "Type" => "dropdown",
            "Options" => implode(",", $custom_fields)
        ],
        "configOptionSpace" => [
            "FriendlyName" => "Выберите настраиваемое поле места",
            "Type" => "dropdown",
            "Options" => implode(",", $custom_fields)
        ],
        "prefix" => [
            "FriendlyName" => "Укажите префикс",
            "Type" => "text",
        ],
    ];
}

function BackupSpaceProftpd_CreateAccount($params)
{
    try {
        $configOptionLocation = explode(' - ', $params['configoption1'])[1]; //локация
        $configOptionSpace = explode(' - ', $params['configoption2'])[1]; //место
        $location = $params['configoptions'][$configOptionLocation];//локация
        $prefix = $params['configoption3'];//префикс
        $serviceId = $params['serviceid'];//префикс
        $space = (int)filter_var($params['configoptions'][$configOptionSpace], FILTER_SANITIZE_NUMBER_INT); //место
        $serverIdFromLocation = collect(Capsule::table("tblservergroupsrel")
            ->where('tblservergroups.name', '=', $location)
            ->join('tblservers', 'tblservergroupsrel.serverid', '=', 'tblservers.id')
            ->join('tblservergroups', 'tblservergroups.id', '=', 'tblservergroupsrel.groupid')
            ->get(['tblservers.id']))
            ->keyBy('id')
            ->transform(function ($item) {
                $api = new ProFTPDController($item->id);
                $stats = $api->status();
                return ((float)$stats['disk_free_without_quota'] * (float)$stats['oversell']) - (float)$stats['disk_use'];
            })->filter(function ($value) {
                return $value > 0;
            })->filter(function ($value) use ($space) {
                return $value > ($space * 1073741824);
            })->sortByDesc(function ($value) {
                return $value;
            })->keys()->first();

        if ($serverIdFromLocation === null) {
            return 'К сожалению нет сервера с достаточным кол-вом свободного места';
        }

        $login = $prefix . $serviceId;
        $password = $params['password'];

        $api = new ProFTPDController($serverIdFromLocation);
        $api->createAccount($login, $password, $space * 1073741824);
        $command = 'UpdateClientProduct';
        $postData = array(
            'serviceid' => $serviceId,
            'serviceusername' => $login,
            'serverid' => $serverIdFromLocation,
        );

        $results = localAPI($command, $postData);
    } catch (Exception $e) {
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }

    if ($results['result'] == 'success') {
        return 'success';
    }

    return $results['result'];
}

function BackupSpaceProftpd_SuspendAccount(array $params)
{
    try {
        $api = new ProFTPDController($params['serverid']);
        $api->changePassword($params['username'], str_random());
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'teamspeak',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function BackupSpaceProftpd_AdminServicesTabFields(array $params)
{
    if ($params['status'] == 'Terminated')
        return [];

    try {
        $api = new ProFTPDController($params['serverid']);
        $result = $api->getAccountStats($params['username']);

        $notify = NotifyModel::where('service_id', '=', $params['serviceid'])->first();

        if (!empty($notify)) {
            $userNotify = [
                'Уведомления' => $notify->status ? 'Включены' : 'Отключены',
                'Каждые' => empty($notify->delay) ? 'Не задано' : $notify->delay . ' час',
                'Сейчас занято' => floor(($result['disk_use'] / $result['disk_space']) * 100) . '%',
                'Порог' => empty($notify->threshold) ? 'Не задан' : $notify->threshold . '%',
            ];
        } else {
            $userNotify = [
                'Уведомления' => 'не инициализированы'
            ];
        }

        return $userNotify + [
                'Всего места (api)' => $result['disk_space_format'],
                'Использовано места (api)' => $result['disk_use_format'],
            ];
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        // In an error condition, simply return no additional fields to display.
    }
    return array();
}

function BackupSpaceProftpd_ChangePassword(array $params)
{
    try {
        $api = new ProFTPDController($params['serverid']);
        $api->changePassword($params['username'], $params['password']);
    } catch (Exception $e) {
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return 'Возникла ошибка при изменении пароля';
    }
    return 'success';
}

function BackupSpaceProftpd_UnsuspendAccount(array $params)
{
    try {
        $api = new ProFTPDController($params['serverid']);
        $api->changePassword($params['username'], $params['password']);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'teamspeak',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function BackupSpaceProftpd_ChangePackage(array $params)
{
    try {
        $configOptionSpace = explode(' - ', $params['configoption2'])[1]; //место
        $space = (int)filter_var($params['configoptions'][$configOptionSpace], FILTER_SANITIZE_NUMBER_INT); //место
        $api = new ProFTPDController($params['serverid']);
        $stats = $api->status();
        $allowSpace = ((float)$stats['disk_free_without_quota'] * (float)$stats['oversell']) - (float)$stats['disk_use'];

        if ($allowSpace < ($space * 1073741824)) {
            return 'На ноде недостаточно места для изменения тарифа';
        }
        $api->updateQuota($params['username'], $space * 1073741824);
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return 'Возникла ошибка при изменении квоты';
    }
    return 'success';
}

function BackupSpaceProftpd_TerminateAccount(array $params)
{
    try {
        $api = new ProFTPDController($params['serverid']);
        $api->deleteAccount($params['username']);
        NotifyModel::where('service_id', '=', $params['serviceid'])->delete();
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'provisioningmodule',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return 'success';
}

function BackupSpaceProftpd_ClientArea(array $params)
{
    global  $_LANG;
    $defaultLanguage = 'russian';
    $clientLanguage = $params['clientsdetails']['language'];

    if (file_exists(sprintf(ModuleConfig::getBaseFullPath() . '/serverModule/lang/%s.php', $clientLanguage))) {
        include_once sprintf(ModuleConfig::getBaseFullPath() . '/serverModule/lang/%s.php', $clientLanguage);
    } else {
        include_once sprintf(ModuleConfig::getBaseFullPath() . '/serverModule/lang/%s.php', $defaultLanguage);
    }

    try {
        $api = new ProFTPDController($params['serverid']);
        $account = $api->getAccountStats($params['username']);
        $node = $api->status();
        return array(
            'tabOverviewReplacementTemplate' => 'templates/clientarea.tpl',
            'templateVariables' => array(
                'user_id' => $params['userid'],
                'service_id' => $params['serviceid'],
                'sign' => sha1($params['userid'] . ModuleConfig::getSecret()),
                'dedicatedip' => $params['serverip'],
                'allow_protocol' => array_flip($node['allow_protocol']),
                'diskspaceUsed' => $account['disk_use_format'],
                'diskspaceTotal' => $account['disk_space_format'],
                'diskspaceUsedInPercent' => floor(($account['disk_use'] / $account['disk_space']) * 100),
            ),
        );
    } catch (Exception $e) {
        logModuleCall(
            'teamspeak',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'tabOverviewReplacementTemplate' => 'templates/clientarea.tpl',
            'templateVariables' => array(
                'dedicatedip' => '127.0.0.1',
                'user_id' => $params['userid'],
                'service_id' => $params['serviceid'],
                'allow_protocol' => [],
                'sign' => sha1($params['userid'] . ModuleConfig::getSecret()),
                'error' => $_LANG['BackupSpaceProftpd_the_remote_server_is_not_currently_responding'],
                'diskspaceUsed' => 0,
                'diskspaceTotal' => 0,
                'diskspaceUsedInPercent' => 0,
            ),
        );
    }
}
