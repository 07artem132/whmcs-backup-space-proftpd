<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 25.11.2019, 19:22
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Traits;

trait ResponseTraits
{

    /**
     * Send client message and die
     * @param string $status
     * @param string|null $message
     */
    function response(string $status, string $message = null, ?int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
        ]);
        die();
    }

    /**
     * Send client data and die
     * @param string $status
     * @param array $data
     */
    function responseData(string $status, array $data = []): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'data' => $data,
        ]);
        die();
    }

    /**
     * Send client errors array
     * @param string $status
     * @param array $data
     */
    function responseErrors(string $status, array $data = []): void
    {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'status' => $status,
            'errors' => $data,
        ]);
        die();
    }
}