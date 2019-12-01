<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 19:10
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Abstracts;

use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;

abstract class ApiAccessController
{
    public static function verifySignature(array $param, string $sign): bool
    {
        $sha1 = sha1(implode("", $param) . ModuleConfig::getSecret());

        if (strcmp($sha1, $sign) === 0) {
            return true;
        }
        return false;
    }
}