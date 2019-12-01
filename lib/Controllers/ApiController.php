<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:22
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Controllers;

use WHMCS\Module\Addon\BackupSpaceProftpd\Configs\ModuleConfig;
use WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces\ApiInterface;
use WHMCS\Module\Addon\BackupSpaceProftpd\Traits\ResponseTraits;

class ApiController
{
    use ResponseTraits;
    /**
     * @var string
     */
    private $action = '';

    private function getAction()
    {
        return $_REQUEST['action'];
    }

    public function run()
    {
        $ClassName = implode(array_map('ucfirst', array_map('strtolower', explode('_', $this->getAction())))) . 'Api';
        $ClassNameFull = 'WHMCS\\Module\\Addon\\' . ModuleConfig::getModuleName() . '\\API\\' . $ClassName;
        try {
            /**
             * @var $class ApiInterface
             */
            $class = new $ClassNameFull();

            $validateResult = $class->validateRequestParameters();

            if (!empty($validateResult)) {
                $this->responseErrors('error', $validateResult);
            }

            if (!$class->isAuth()) {
                $this->response('error', 'forbidden');
            }

            $class->run();
        } catch (\Throwable $e) {
            $this->response('error', 'Internal api error:' . $e->getMessage(),500);
        }
    }
}