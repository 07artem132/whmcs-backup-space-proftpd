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

        $url = $urlPrefix . $server->hostname . ':' . $urlPort . '/api/';

        $this->uid = $server->username;
        $this->token = decrypt($server->password);
        $this->url = $url;

        $this->http_client = new HTTPClient([
            'base_url' => $this->url,
            'timeout' => 2.0,
            'defaults' => [
                // 'auth' => [$this->uid, $this->token ]
            ]
        ]);
    }

    /**
     * @return mixed
     * @throws RequestException
     */
    public function status()
    {
        $response = json_decode($this->http_client->get($this->url . 'node/status', ['auth' => [$this->uid, $this->token]])->getBody()->getContents(), true);

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
        return json_decode($this->http_client->post($this->url . 'user', [
            'form_params' => [
                'login' => $login,
                'password' => $password,
                'quota' => $quota,
            ],
            'auth' => [$this->uid, $this->token]
        ])->getBody()->getContents(), true);
    }

    /**
     * @param $username
     * @return mixed
     * @throws RequestException
     */
    public function deleteAccount($username)
    {
        return json_decode($this->http_client->delete($this->url . $username, ['auth' => [$this->uid, $this->token]])->getBody()->getContents(), true);
    }

    /**
     * @param $username
     * @param int $quota
     * @return mixed
     * @throws RequestException
     */
    public function updateQuota($username, int $quota)
    {
        return json_decode($this->http_client->put($this->url . $username . '/quota', [
            'form_params' => [
                'quota' => $quota,
            ],
            'auth' => [$this->uid, $this->token]
        ])->getBody()->getContents(), true);
    }

    /**
     * @param $username
     * @param $password
     * @return mixed
     * @throws RequestException
     */
    public function changePassword($username, $password)
    {
        return json_decode($this->http_client->put($this->url . $username . '/password', [
            'form_params' => [
                'password' => $password,
            ],
            'auth' => [$this->uid, $this->token]
        ])->getBody()->getContents(), true);
    }

    /**
     * @param $username
     * @return mixed
     * @throws RequestException
     */
    public function getAccountStats($username)
    {
        $response = json_decode($this->http_client->get($this->url . $username, ['auth' => [$this->uid, $this->token]])->getBody()->getContents(), true);
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
        return json_decode($this->http_client->put($this->url . 'node/oversell', [
            'form_params' => [
                'oversell' => $oversell,
            ], 'auth' => [$this->uid, $this->token]
        ])->getBody()->getContents(), true);
    }
}