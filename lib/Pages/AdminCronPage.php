<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 27.11.2019, 16:35
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Pages;

use WHMCS\Mail\Template;
use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;
use WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces\PageInterface;
use WHMCS\Module\Addon\BackupSpaceProftpd\Models\SettingsModel;

class AdminCronPage implements PageInterface
{
    private $templateName = 'admin_cron.tpl';
    private $vars = [];

    function __construct()
    {
        $this->vars['sign'] = sha1($_SESSION['adminid'] . ModuleConfig::getSecret());
        $this->vars['userid'] = $_SESSION['adminid'];

        $this->vars['template'] = Template::where("type", "=", 'notification')
            ->where("language", "=", "")
            ->orderBy("name")
            ->get(['name', 'id'])
            ->keyBy('id')->transform(function ($item) {
                return $item->name;
            })->toArray();
    }

    function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * @return array
     */
    function getVars()
    {
        return $this->vars;
    }

    function getSubMenu()
    {
        return null;
    }

    function loadJS()
    {
        return [
            'admin/main.js',
            'admin/notify.min.js',
        ];
    }

}