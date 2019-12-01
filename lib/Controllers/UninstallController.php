<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:19
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Controllers;

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;

class UninstallController
{

    public static function dropTable($tableName)
    {
        try {
            Capsule::schema()->dropIfExists($tableName);
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => sprintf('При удалении таблицы %s произошла ошибка: %s', $tableName, $e->getMessage())
            );
        }

        return [];
    }

    public static function deleteServerModule()
    {
        $serverModulePath = ModuleConfig::getBaseFullPath() . '/serverModule';
        $targetPath = ModuleConfig::getWhmcsRootDir() . '/modules/servers/' . ModuleConfig::getModuleName();

        if (unlink($targetPath)) {
            return [];
        } else {
            return [
                'status' => 'error',
                'description' => 'При удалении символической ссылки возникла ошибка. Цель: ' .
                    $serverModulePath . ' Ссылка:' . $targetPath
            ];
        }
    }
}