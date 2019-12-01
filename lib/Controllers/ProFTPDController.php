<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:42
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Controllers;


use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Module\Addon\BackupSpaceProftpd\Exceptions\InvalidServerIdException;
use WHMCS\Module\Addon\BackupSpaceProftpd\Traits\ByteConvertTraits;

class ProFTPDController
{
    use ByteConvertTraits;
    private $url;
    private $uid;
    private $token;
    private $http_client;
    /**
     * @var array Параметры/Заголовки которые передаются в месте с запросом ( HEADER)
     */
    private $request_option = [];

    /**
     * ProFTPDController constructor.
     * @param int $server_id
     * @throws InvalidServerIdException
     */
    function __construct(int $server_id)
    {
        $server = Capsule::table('tblservers')->find($server_id);

        if (empty($server)) {
            throw  new InvalidServerIdException($server_id);
        }

        $urlPrefix = empty($server->secure) ? 'http://' : 'https://';
        $urlPort = $server->port ? $server->port : 5000;

        $url = $urlPrefix . $server->ipaddress . ':' . $urlPort . '/api/';

        $this->uid = $server->username;
        $this->token = decrypt($server->password);
        $this->url = $url;

        $this->http_client = new HTTPClient([
            'base_url' => $this->url,
            'timeout' => 2.0,
            'defaults' => [
                'auth' => [
                    $this->uid, $this->token
                ]
            ]
        ]);
    }

    /**
     * @return mixed
     * @throws RequestException
     */
    public function status()
    {
        $response = $this->http_client->get('node/status')->json();
        return $response;
    }

    /**
     * @param $login
     * @param $password
     * @param $quota int byte
     * @return mixed
     * @throws RequestException
     */
    public function createAccount($login, $password, $quota)
    {
        return $this->http_client->post('user', [
            'body' => [
                'login' => $login,
                'password' => $password,
                'quota' => $quota,
            ]
        ])->json();
    }

    /**
     * @param $username
     * @return mixed
     * @throws RequestException
     */
    public function deleteAccount($username)
    {
        return $this->http_client->delete($username)->json();
    }

    /**
     * @param $username
     * @param int $quota
     * @return mixed
     * @throws RequestException
     */
    public function updateQuota($username, int $quota)
    {
        return $this->http_client->put($username . '/quota', [
            'body' => [
                'quota' => $quota,
            ]
        ])->json();
    }

    /**
     * @param $username
     * @param $password
     * @return mixed
     * @throws RequestException
     */
    public function changePassword($username, $password)
    {
        return $this->http_client->put($username . '/password', [
            'body' => [
                'password' => $password,
            ]
        ])->json();
    }

    /**
     * @param $username
     * @return mixed
     * @throws RequestException
     */
    public function getAccountStats($username)
    {
        $response = $this->http_client->get($username)->json();
        if ($response['disk_use'] == 0) {
            $response['disk_use'] = 1;
        }
        $response['disk_space_format'] = $this->byte_format_size($response['disk_space']);
        $response['disk_use_format'] = $this->byte_format_size($response['disk_use']);
        return $response;

    }

    /**
     * @param $oversell
     * @return mixed
     * @throws RequestException
     */
    public function setOversell($oversell)
    {
        return $this->http_client->put('node/oversell', [
            'body' => [
                'oversell' => $oversell,
            ]
        ])->json();
    }
}