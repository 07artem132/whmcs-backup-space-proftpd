<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:13
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces;

interface  PageInterface
{
    /**
     * @return string
     */
    public function getTemplateName();

    /**
     * @return array
     */
    public function getVars();

    public function getSubMenu();

    public function loadJS();
}