<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 23:40
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Pages;

use GuzzleHttp\Exception\RequestException;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\ProFTPDController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Exceptions\InvalidServerIdException;
use WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces\PageInterface;
use WHMCS\Module\Addon\BackupSpaceProftpd\Models\NotifyModel;
use WHMCS\Module\Addon\BackupSpaceProftpd\Traits\ByteConvertTraits;

class AdminNotifyPage implements PageInterface
{
    use ByteConvertTraits;
    private $templateName = 'admin_notify.tpl';
    private $vars = [];

    function __construct()
    {
        $listNotify = NotifyModel::all();
        /**
         * id услуги
         * статус уведомлений
         * Отсылать каждые х часов
         * Порог для уведомлений
         * Последнее уведомление было
         */
        foreach ($listNotify as $notify) {
            try {
                $service = $notify->service()->first();
                $api = new ProFTPDController($service->serverId);
                $stats = $api->getAccountStats($service->username);
            } catch (InvalidServerIdException $e) {
                echo $e->getMessage();
                $stats['disk_use'] = 0;
                $stats['disk_space'] = 0;
            } catch (RequestException $e) {
                echo $e->getMessage();
                $stats['disk_use'] = 0;
                $stats['disk_space'] = 0;
            }

            $this->vars['notifications'][$notify->id] = [
                'service_id' => $notify->service_id,
                'status' => $notify->status,
                'delay' => $notify->delay,
                'usePercentage' => floor(($stats['disk_use'] / $stats['disk_space']) * 100),
                'threshold' => $notify->threshold,
                'last_notify' => $notify->last_notify,
            ];
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