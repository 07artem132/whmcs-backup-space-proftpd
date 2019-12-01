<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 20:09
 *
 */

/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 19:10
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\API;

use WHMCS\Module\Addon\BackupSpaceProftpd\Abstracts\ApiValidatorAbstract;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\UserApiAccessController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces\ApiInterface;
use WHMCS\Module\Addon\BackupSpaceProftpd\Models\SettingsModel;
use WHMCS\Module\Addon\BackupSpaceProftpd\Traits\ResponseTraits;

class GetEmailNotifyApi extends ApiValidatorAbstract implements ApiInterface
{
    use ResponseTraits;

    function rules(): array
    {
        return [
            'client_type' => [
                'required',
                'in:admin',
            ],
            'user_id' => [
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
        $result = SettingsModel::where('key', '=', 'email_notify')->first();

        if (!empty($result))
            $status = $result->val;
        else
            $status = 'false';

        $this->responseData('success', ['email_notify' => $status]);
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