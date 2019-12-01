<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:24
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Menu;

use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;
use WHMCS\View\Menu\MenuFactory;

class AdminAreaMenu extends MenuFactory
{
    protected $rootItemName = "BackupSpaceProftpd Manager nav bar";

    public function navbar()
    {
        return $this->loader->load($this->buildMenuStructure($this->getNavBarStructure()));
    }

    protected function getNavBarStructure()
    {
        $menuItems = [
            [
                "name" => "index",
                "label" => 'Список инстансов',
                "uri" => ModuleConfig::getModuleLink() . "&action=index",
                "order" => 0,
                "attributes" => [
                    "class" => !array_key_exists('action', $_GET) || $_GET['action'] === 'index' ? 'active' : ''
                ]
            ],
            [
                "name" => "notify",
                "label" => 'Уведомления пользователей',
                "uri" => ModuleConfig::getModuleLink() . "&action=notify",
                "order" => 0,
                "attributes" => [
                    "class" => $_GET['action'] === 'notify' ? 'active' : ''
                ]
            ],
            [
                "name" => "cron",
                "label" => 'Крон',
                "uri" => ModuleConfig::getModuleLink() . "&action=cron",
                "order" => 1,
                "attributes" => [
                    "class" => $_GET['action'] === 'cron' ? 'active' : ''
                ]
            ]
        ];

        return $menuItems;
    }

}


