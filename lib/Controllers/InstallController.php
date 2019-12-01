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

class InstallController
{
    public static function createTableSettings()
    {
        try {
            if (!Capsule::schema()->hasTable('mod_addon_backup_space_proftpd_settings')) {
                Capsule::schema()->create('mod_addon_backup_space_proftpd_settings', function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->string('key');
                    $table->text('val');
                    $table->timestamps();
                });
            }
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => 'При создании таблицы (mod_addon_backup_space_proftpd_settings) возникла ошибка:' . $e->getMessage()
            );
        }
        return [];
    }

    public static function createTableNotifyQueue()
    {
        try {
            if (!Capsule::schema()->hasTable('mod_addon_backup_space_proftpd_notify')) {
                Capsule::schema()->create('mod_addon_backup_space_proftpd_notify', function ($table) {
                    /** @var \Illuminate\Database\Schema\Blueprint $table */
                    $table->increments('id');
                    $table->unsignedInteger('service_id');
                    $table->boolean('status')->nullable();;
                    $table->smallInteger('delay')->nullable();
                    $table->smallInteger('threshold')->nullable();
                    $table->timestamp('last_notify')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Exception $e) {
            return array(
                'status' => 'error',
                'description' => 'При создании таблицы (mod_addon_backup_space_proftpd_notify) возникла ошибка:' . $e->getMessage()
            );
        }
        return [];
    }

    public static function installServerModule()
    {
        $serverModulePath = ModuleConfig::getBaseFullPath() . '/serverModule';
        $targetPath = ModuleConfig::getWhmcsRootDir() . '/modules/servers/' . ModuleConfig::getModuleName();

        if (symlink($serverModulePath, $targetPath)) {
            return [];
        } else {
            return [
                'status' => 'error',
                'description' => 'При создании символической ссылки возникла ошибка. Цель: ' .
                    $serverModulePath . ' Ссылка:' . $targetPath
            ];
        }
    }
}