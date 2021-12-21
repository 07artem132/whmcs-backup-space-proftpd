<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 01.12.2019, 1:00
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Tasks;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use WHMCS\Mail\Template;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\ProFTPDController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Exceptions\InvalidServerIdException;
use WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces\TaskInterfaces;
use WHMCS\Module\Addon\BackupSpaceProftpd\Models\NotifyModel;
use WHMCS\Module\Addon\BackupSpaceProftpd\Models\SettingsModel;

class SendNotifyTask implements TaskInterfaces
{
    private $frequency = '* * * * *';

    public $name = 'Send notify for space usage ';

    function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFrequency(): string
    {
        return $this->frequency;
    }

    function run(): void
    {
        $tasks = $this->getFormattedTaskList();

        if (empty($tasks)) {
            return;
        }

        foreach ($tasks as $server_id => $notificationsConfig) {
            try {
                $api = new ProFTPDController($server_id);
            } catch (InvalidServerIdException $e) {
                echo $e->getMessage();
                continue;
            }
            foreach ($notificationsConfig as $notify) {
                try {
                    $stats = $api->getAccountStats($notify['username']);
                    $usagePercentage = floor(($stats['disk_use'] / $stats['disk_space']) * 100);
                    if ($usagePercentage > $notify['threshold']) {
                        if (sendMessage($notify['emailTemplate'], 1, [
                            'usagePercentage' => $usagePercentage,
                            'threshold' => $notify['threshold'],
                            'disk_space' => $stats['disk_space_format'],
                            'disk_use' => $stats['disk_use_format'],
                            'to' => [
                                $notify['emailNotify']
                            ]
                        ])) {
                            echo sprintf('Завершено для: %s на почту: %s', $notify['username'], $notify['emailNotify']) . PHP_EOL;
                            $notify['model']->last_notify = Carbon::now()->toDateTimeString();
                            $notify['model']->save();
                        } else {
                            echo 'Письмо не отправлено для: ' . $notify['username'] . PHP_EOL;
                        }
                    }
                } catch (RequestException $e) {
                    echo $e->getMessage();
                    continue;
                }
            }

        }
    }

    /**
     * @return array
     */
    private function getFormattedTaskList(): array
    {
        $NotifyTasks = NotifyModel::with(['service.client'])
            ->whereNotNull('delay')
            ->whereNotNull('threshold')
            ->where('status', '=', 1)->get();
        $emailTemplateId = SettingsModel::where('key', '=', 'email_template')->first();
        $emailTemplate = Template::find($emailTemplateId->val);
        $emailNotifyStatus = SettingsModel::where('key', '=', 'email_notify')->first();
        $result = [];

        if (empty($emailNotifyStatus) && $emailNotifyStatus->val != 'true') {
            echo 'Отправка уведомлений отключена';
            return $result;
        }

        if (empty($emailTemplate)) {
            echo 'Отсутствут email шаблон';
            return $result;
        }

        foreach ($NotifyTasks as $task) {
            try {
                if ($task->last_notify != null) {
                    $lastNotify = Carbon::parse($task->last_notify);
                    $nextNotify = $lastNotify->copy()->addHour($task->delay);
                    echo 'Отправка для '.$task->service->username.' через: '.Carbon::now()->diffInSeconds($nextNotify,false).' сек.'.PHP_EOL;
                    if (Carbon::now()->diffInSeconds($nextNotify,false) > 0) {
                        echo sprintf(
                                'Пропускаем отправку для %s так как ему уже отправили в %s следующая отправка не раньше %s',
                                $task->service->username,
                                $lastNotify,
                                $nextNotify
                            ) . PHP_EOL;
                        continue;
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            $result[$task->service->server][] = [
                'threshold' => $task->threshold,
                'emailNotify' => $task->service->client->email,
                'emailTemplate' => $emailTemplate,
                'username' => $task->service->username,
                'user_id' => $task->service->clientId,
                'model' => $task,
            ];

        }
        return $result;
    }
}
