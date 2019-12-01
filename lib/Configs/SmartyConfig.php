<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:15
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Configs;


class SmartyConfig
{
    public static function GetPluginsDir()
    {
        return ModuleConfig::getWhmcsRootDir() . '/modules/addons/' . ModuleConfig::getModuleName() . '/lib/SmartyPlugin';
    }

    public static function GetTemplateDir()
    {
        return ModuleConfig::getWhmcsRootDir() . '/modules/addons/' . ModuleConfig::getModuleName() . '/templates/';
    }
    public static function GetJsDir()
    {
        return ModuleConfig::getBaseRelativePath() . '/templates/js/';
    }
    public static function GetCssDir()
    {
        return ModuleConfig::getBaseRelativePath() . '/templates/css';
    }

    public static function GetCompileDir()
    {
        global $templates_compiledir;

        return $templates_compiledir;
    }
}