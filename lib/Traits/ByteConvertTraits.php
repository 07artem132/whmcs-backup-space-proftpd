<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 27.11.2019, 16:09
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Traits;

trait ByteConvertTraits
{
    function byte_format_size($bytes)
    {
        $minus = false;

        if ($bytes < 0) {
            $minus = true;
            $bytes = abs($bytes);
        }

        if ($bytes == 0) {
            return '0 B';
        }

        $bytes = floatval($bytes);

        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
                break;
            }
        }

        return $minus ? '-' . $result : $result;
    }

}