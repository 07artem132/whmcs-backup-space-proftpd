<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 23:02
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\API;

use WHMCS\Module\Addon\BackupSpaceProftpd\Abstracts\ApiValidatorAbstract;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\UserApiAccessController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces\ApiInterface;
use WHMCS\Module\Addon\BackupSpaceProftpd\Models\NotifyModel;
use WHMCS\Module\Addon\BackupSpaceProftpd\Traits\ResponseTraits;

class EditEmailUserNotifyApi extends ApiValidatorAbstract implements ApiInterface
{
    use ResponseTraits;

    function rules(): array
    {
        return [
            'client_type' => [
                'required',
                'in:user',
            ],
            'user_id' => [
                'required',
            ],
            'service_id' => [
                'required',
            ],
            'status' => [
                'required',
            ],
            'sign' => [
                'required',
            ]
        ];
    }

    /**
     * @return array|null
     */
    function validateRequestParameters(): ?array
    {
        $errors = $this->validate();

        if (count($errors) !== 0) {
            return $errors;
        }

        if (!UserApiAccessController::verifySignature(
            [
                $_REQUEST['user_id'],
            ],
            $_REQUEST['sign']
        )) {
            $errors['sign'][] = 'error verify signature';
        }

        return empty($errors) ? null : $errors;
    }


    function run(): void
    {
        $notify = NotifyModel::firstOrNew([
            'service_id' => $_REQUEST['service_id']
        ]);
        $notify->status = $_REQUEST['status'] == 'true' ? true : false;
        $notify->saveOrFail();
        $this->responseData('success', []);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    function isAuth(): bool
    {
        switch ($_REQUEST['client_type']) {
            case 'user':
                return UserApiAccessController::AllowUserActionService($_REQUEST['user_id'], $_REQUEST['service_id']);
                break;
            case 'admin':
                return UserApiAccessController::AllowAdminActionService($_REQUEST['user_id']);
                break;
            default:
                return false;
        }
    }

}