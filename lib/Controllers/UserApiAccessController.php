<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 19:11
 *
 */

/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 20.11.2019, 16:52
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Controllers;

use Exception;
use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\BackupSpaceProftpd\Abstracts\ApiAccessController;
use WHMCS\Service\Service;

class UserApiAccessController extends ApiAccessController
{
    /**
     * @param int $user_id
     * @param int $service_id
     * @return bool
     * @throws Exception
     */
    public static function AllowUserActionService(int $user_id, int $service_id): bool
    {
        $service = Service::find($service_id);

        if (empty($service)) {
            return false;
        }

        if ($service->userid !== $user_id) {
            return false;
        }

        return true;
    }

    public static function AllowAdminActionService(int $admin_id): bool
    {
        $admin = Capsule::table("tbladmins")->find($admin_id);

        if (empty($admin)) {
            return false;
        }

        return true;
    }
}