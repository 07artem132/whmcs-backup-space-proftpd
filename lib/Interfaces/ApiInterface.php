<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:23
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces;

interface  ApiInterface
{
    /**
     * @return string
     */
    function validateRequestParameters(): ?array;

    function run(): void;

    function isAuth(): bool;
}