<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:15
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Configs;

use WHMCS\Module\Addon\Setting;

class ModuleConfig
{
    private static $defaultLanguage = 'russian';
    private static $whmcsRootDir = ROOTDIR;
    private static $moduleName = 'BackupSpaceProftpd';

    /**
     * @return mixed
     */
    public static function getWhmcsRootDir(): string
    {
        return self::$whmcsRootDir;
    }

    /**
     * @return string
     */
    public static function getDefaultLanguage(): string
    {
        return self::$defaultLanguage;
    }

    /**
     * @return string
     */
    public static function getModuleName(): string
    {
        return self::$moduleName;
    }

    public static function getModuleLink(): string
    {
        global $module, $customadminpath;

        return '/' . $customadminpath . '/addonmodules.php?module=' . $module;
    }

    public static function getBaseFullPath(): string
    {
        return self::getWhmcsRootDir() . '/modules/addons/' . self::getModuleName();
    }
    public static function getSecret()
    {
        global $cc_encryption_hash;

        return sha1($cc_encryption_hash);
    }

    public static function getBaseRelativePath(): string
    {
        return '/modules/addons/' . self::getModuleName();
    }

    public static function getModuleSetting($setting)
    {
        return Setting::Module(self::getModuleName())
            ->where('setting', '=', $setting)
            ->first()->value;
    }

}