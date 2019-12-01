<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 01.12.2019, 0:56
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces;


interface TaskInterfaces
{
    public function getName(): string;

    public function run(): void;

    public function getFrequency(): string;

}