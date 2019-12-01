<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 17:45
 *
 */

use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\ApiController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\InstallController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\PageController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\UninstallController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Menu\AdminAreaMenu;

function BackupSpaceProftpd_config()
{
    $configarray = array(
        "name" => "BackupSpaceProftpd",
        "description" => "Продажа места для резервного копирования (провижинг через proftpd)",
        "version" => "1",
        "author" => "service-voice",
        "language" => "russian",
        "fields" => [
            "DeleteTableWhenDisabled" => [
                "FriendlyName" => "Удалять данные модуля при отключении ?",
                "Type" => "yesno",
                "Description" => " Отметьте здесь дабы удалить данные модуля при отключении оного.",
            ]
        ]
    );
    return $configarray;
}

function BackupSpaceProftpd_activate()
{
    if (!empty($error = InstallController::installServerModule())) {
        return $error;
    }

    if (!empty($error = InstallController::createTableNotifyQueue())) {
        return $error;
    }

    if (!empty($error = InstallController::createTableSettings())) {
        return $error;
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно активирован',
    );
}

function BackupSpaceProftpd_deactivate()
{
    if (!empty($dropTable = ModuleConfig::getModuleSetting('DeleteTableWhenDisabled'))) {
        if ($dropTable === 'on') {
            if (!empty($error = UninstallController::dropTable('mod_addon_backup_space_proftpd_settings'))) {
                return $error;
            }
            if (!empty($error = UninstallController::dropTable('mod_addon_backup_space_proftpd_notify_queue'))) {
                return $error;
            }
        }
    }

    if (!empty($error = UninstallController::deleteServerModule())) {
        return $error;
    }

    return array(
        'status' => 'success',
        'description' => 'Модуль успешно деактивирован',
    );
}

function BackupSpaceProftpd_output($vars)
{
    if ($_REQUEST['ajax'] === 'true') {
        $api = new ApiController();
        $api->run();
        die();
    }

    $PageController = new PageController($vars);
    $PageController->setDefaultAction('index');
    $PageController->setSuffixTemplate('admin');

    $PageController->setMenuTemplate('include\navbar.tpl');
    $PageController->setMenu((new AdminAreaMenu())->navbar());
    $PageController->run();
}

function BackupSpaceProftpd_clientarea($vars)
{
    $api = new ApiController();
    $api->run();
    die();
}
