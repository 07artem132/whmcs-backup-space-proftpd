<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:12
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Pages;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\ProFTPDController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Exceptions\InvalidServerIdException;
use WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces\PageInterface;
use WHMCS\Module\Addon\BackupSpaceProftpd\Traits\ByteConvertTraits;
use WHMCS\Service\Service;

class AdminIndexPage implements PageInterface
{
    use ByteConvertTraits;
    private $templateName = 'admin_index.tpl';
    private $vars = [];

    function __construct()
    {
        $this->vars['sign'] = sha1($_SESSION['adminid'] . ModuleConfig::getSecret());
        $this->vars['userid'] = $_SESSION['adminid'];

        $listServers = Capsule::table('tblservers')->where('type', 'BackupSpaceProftpd')->get();
        /**
         * хостнейм
         * ip
         * страна
         * количество аккаунтов
         * фактическое свободное место
         * место свободное учитывая квоты
         * фактическое занятое место
         * место занятое учитывая квоты
         * величина оверсела
         */
        foreach ($listServers as $server) {
            try {
                $node = new ProFTPDController($server->id);
                $statusNode = $node->status();
                $this->vars['servers'][$server->id] = [
                    'hostname' => $server->hostname,
                    'ip' => $server->ipaddress,
                    'country' => Capsule::table("tblservergroupsrel")
                        ->where('tblservers.id', '=', $server->id)
                        ->join('tblservers', 'tblservergroupsrel.serverid', '=', 'tblservers.id')
                        ->join('tblservergroups', 'tblservergroups.id', '=', 'tblservergroupsrel.groupid')
                        ->first()->name,
                    'account' => Service::where("server", "=", $server->id)
                        ->whereIn("domainstatus", array("Active", "Suspended"))
                        ->count(),
                    'disk_free_without_quota' => $this->byte_format_size($statusNode['disk_free_without_quota']),
                    'disk_free' => $this->byte_format_size($statusNode['disk_free']),
                    'disk_use_without_quota' => $this->byte_format_size($statusNode['disk_use_without_quota']),
                    'disk_use' => $this->byte_format_size($statusNode['disk_use']),
                    'oversell' => $statusNode['oversell'],
                ];
            } catch (InvalidServerIdException $e) {
                echo $e->getMessage();
            } catch (RequestException $e) {
                echo $e->getMessage();
            }
        }
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