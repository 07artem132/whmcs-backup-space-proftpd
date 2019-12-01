<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 19:33
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\API;

use GuzzleHttp\Exception\RequestException;
use WHMCS\Module\Addon\BackupSpaceProftpd\Abstracts\ApiValidatorAbstract;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\ProFTPDController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Controllers\UserApiAccessController;
use WHMCS\Module\Addon\BackupSpaceProftpd\Exceptions\InvalidServerIdException;
use WHMCS\Module\Addon\BackupSpaceProftpd\Interfaces\ApiInterface;
use WHMCS\Module\Addon\BackupSpaceProftpd\Traits\ResponseTraits;

class EditOversellApi extends ApiValidatorAbstract implements ApiInterface
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
            'oversell' => [
                'required',
            ],
            'server_id' => [
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
        try {
            $api = new ProFTPDController($_REQUEST['server_id']);
            $api->setOversell($_REQUEST['oversell']);
        } catch (InvalidServerIdException $e) {
            $this->response('error','invalid server id->' . $e->getMessage(),500);
        } catch (RequestException $e) {
            $this->response('error', $e->getMessage(),500);
        }

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